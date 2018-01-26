<?php

//Version 2.1 EBV und 1902 25.01.18

SetTimerByName_InMinuten($_IPS['SELF'], "Timer", 1);  // Legt einen Timer f�r dieses Skript an was alle x Minuten ausf�hrt wird

//Profil Sollwerte

//Profil ABW
$ProfilABW = "ABW";

//MAX Zeit ABW

//E-Mail Alarm Variable

//Logging

//Action Script finden

$ParID = IPS_GetParent($_IPS['SELF']);
$ActionID = @IPS_GetObjectIDByName('Action Script', $ParID);
echo $ActionID, "\n" ;
$ChartID = @IPS_GetObjectIDByName('HighStock_3.01', $ParID);
echo $ChartID, "\n";
$DashID = (@IPS_GetObjectIDByName('Dashboard',(@IPS_GetObjectIDByName('Fehlermeldungen', (@IPS_GetObjectIDByName('Alarme', 0))))));
echo $DashID, "\n" ;
$AnzeigeID = (@IPS_GetObjectIDByName('Anzeige',(@IPS_GetObjectIDByName('Meldungen', (@IPS_GetObjectIDByName('Anzeige alle Systeme', 0))))));
echo $AnzeigeID, "\n" ;

//================ alle IP Adressen durchgehen ==================================
foreach ($iplist as $ip) {

$start = microtime(true);

$ctx = stream_context_create(array(
    'http' => array(
        'timeout' => 25
        )
    )
);
//================= Test auf erreichbarkeit vom GW ==============================
if (Sys_Ping($ip, 1000) ==0) continue;

//echo   (Sys_Ping($ip, 2000))     ;

$xmlstr = @file_get_contents("http://admin:BTmdH1Bh@".$ip."/details.xml",0,$ctx);
// echo        $xmlstr;
//print $xmlstr;

if (false == $xmlstr ) {
  //SetValueFloat(48351 /**/, 0.0);
  //SetValueInteger(23971, 0);
  return;  //wenn keine Daten zurück
  }

$xml = new SimpleXMLElement($xmlstr);
//var_dump ($xml);
$adresse = (string)$xml->DeviceName;
//var_dump ($adresse);
$HostName = (string)$xml->HostName;

$CatID = CreateCategoryByName(0, $adresse);
$Ebene = $CatID ;
$CatID = CreateCategoryByName($Ebene, "Systemdaten");
$varid = CreateVariableByName($CatID, "System Name", 3 );
SetValueString( $varid , $HostName);
$varid = CreateVariableByName($CatID, "Sensoren angeschlossen", 1 );
$DevicesConnected = (string)$xml->DevicesConnected;
SetValueInteger( $varid , $DevicesConnected);
$varid = CreateVariableByName($CatID, "Laufzeit", 2 );
$LoopTime = (string)$xml->LoopTime;
SetValueFloat( $varid , $LoopTime);
$varid = CreateVariableByName($CatID, "ausgelesen am", 3 );
SetValueString( $varid , date(DATE_RFC822));

//########################### Alarme #############################################
$CatID_Alarme = CreateCategoryByName($Ebene, "Alarme");
$varid = CreateVariableByName($CatID_Alarme, "Heizungsfirma", 3 ,"~String");
IPS_SetVariableCustomAction($varid, $ActionID );
$varid = CreateVariableByName($CatID_Alarme, "E-Mail Heizungsfirma", 3 ,"~String");
IPS_SetVariableCustomAction($varid, $ActionID );
$varid = CreateVariableByName($CatID_Alarme, "E-Mail Verwaltung", 3 ,"~String");
IPS_SetVariableCustomAction($varid, $ActionID );
//########################### Sollwerte #############################################
$CatID_Sollwerte = CreateCategoryByName($Ebene, "Sollwerte");
$DI = CreateDummyInstance ("Hier bitte die Sollwerte und die maximale +/- Abweichung eintragen.", $CatID_Sollwerte , $Position=0);
$DI = CreateDummyInstance ("Wenn die Abweichung '-1' ist, wird die Auswertung und Anzeige f�r diesen Wert deaktiviert", $CatID_Sollwerte , $Position=0);
//############################ Grafik ############################################
$CatID_Grafik = CreateCategoryByName($Ebene, "Grafik");
$varid = CreateVariableByName($CatID_Grafik, "HighCharts", 3 ,"~HTMLBox");
//$ZielSkript_Name = ;

$Script_Inhalt = '<?php

	includeScript('."$ChartID".') ;

function includeScript($scriptID)
{
    $s = IPS_GetScript($scriptID);
    include($s["ScriptFile"]);
}

?>	';

$Script_ID = CreateScriptByName($varid,"Config_Highcharts_V3.01",$Script_Inhalt);

$CatID = CreateCategoryByName($Ebene, "Daten");


//Temperatur Sensoren anlegen
   foreach ($xml->owd_DS18B20 as $sensor)
{
   $theSensorID = $sensor->ROMId;
   //If (!isset($table["$theSensorID"])) echo "Fehler";
   //Nur in der Tabelle vorhandene Sensoren werden angelegt
   If (isset($table["$theSensorID"]))
   {

$theVarID = $table["$theSensorID"];
$varid_Sollwerte = CreateVariableByName($CatID_Sollwerte, "$theVarID"." Temperatur", 2, "Temperature");
IPS_SetVariableCustomAction($varid_Sollwerte, $ActionID );

$varid_ABW = CreateVariableByName($CatID_Sollwerte, "$theVarID"." Temperatur max +- vom Soll", 2, $ProfilABW);
IPS_SetVariableCustomAction($varid_ABW, $ActionID );
$varid_TMax = CreateVariableByName($CatID_Sollwerte, "$theVarID"." Temperatur max Fehlermeldungen", 2, $ProfilABW);
IPS_SetVariableCustomAction($varid_TMax, $ActionID );

$varid = CreateVariableByName($CatID, "$theVarID"." Temperatur", 2, "~Temperature");

   //IPS_SetInfo($varid, "$theSensorID");
   // print_r(IPS_GetVariable($varid)) ;

$theValue = (float) $sensor->Temperature;
    echo $theSensorID, " in " , $varid," ", $theVarID, " Wert: ", $theValue , "\n" ;
SetValueFloat( $varid , $theValue);

		//Sollwerte �ndern
if (GetValueFloat($varid_Sollwerte) == 0)  SetValueFloat( $varid_Sollwerte , round($theValue));
if (GetValueFloat($varid_TMax) == 0)  SetValueFloat( $varid_TMax , 19);
	//==========================================Fehler loggen ================================================================
	$VarSoll =		 GetValueFloat($varid_Sollwerte);
	$VarIst  =		 round($theValue,2);
	$VarABW  =		 GetValueFloat($varid_ABW);
	$VarTMax =     GetValueFloat($varid_TMax);

	$VarATempID = @IPS_GetVariableIDByName("Au�en Temperatur", $CatID);
		if ($VarATempID === false)
    		//echo "Variable nicht gefunden!", "\n";
        $VarATemp = 0 ;
		else
    		//echo "Die Variablen-ID lautet: ". $VarATempID, "\n";
	$VarATemp =   round(GetValueFloat($VarATempID),0);
			//echo  $VarATemp ," ", $VarTMax ,"\n";

	if (($VarABW > -1) and ($VarATemp < $VarTMax)) {

	//=======================Nur bei Abweichung loggen ========================================================================
	//if ($theValue >= (GetValueFloat($varid_Sollwerte)+GetValueFloat($varid_ABW)))
	if (($VarIst)>($VarSoll+$VarABW) or  ($VarIst)<($VarSoll)-($VarABW))   {
	$number = IPS_RunScriptWaitEx($DashID, array('action' => 'add_new', 'text' => 'ID:'.$varid.' '.$adresse.' '.$theVarID.' Temperatur Abweichung zu hoch '.' IST=>'.$VarIst.' SOLL=>'.$VarSoll.' +/-=>'.$VarABW, 'expires' => 0, 'removable' => true,'Uhrzeit' => time(),'VarID' => $varid,'VarSoll' => $VarSoll,'VarIst' => $VarIst,'VarABW' => $VarABW  ));
																			                                   }
	else 	{
	      $success = IPS_RunScriptWaitEx($DashID , array('action' => 'remove','number' => $varid));
  			}
	$number = IPS_RunScriptWaitEx($AnzeigeID, array('action' => 'add_new_Anzeige', 'text' => 'ID:'.$varid.' '.$adresse.' '.$theVarID.':', 'expires' => 0, 'removable' => false,'Uhrzeit' => time(),'VarID' => $varid,'VarSoll' => $VarSoll,'VarIst' => $VarIst,'VarABW' => $VarABW ));
												                         }

        }

        }

//Temp und Luftfeuchte anlegen
foreach ($xml->owd_DS2438 as $sensor)
{
   $theSensorID = $sensor->ROMId;
   //Nur in der Tabelle vorhandene Sensoren werden angelegt
   If (isset($table["$theSensorID"]))
  {

   $theVarID = $table["$theSensorID"];



   $varid = CreateVariableByName($CatID, "$theVarID"." Temperatur", 2, "~Temperature");
   IPS_SetInfo($varid, "$theSensorID");
   $theValue = (float) $sensor->Temperature;
   echo $theSensorID, " in " , $varid," ", $theVarID, " Wert: ", $theValue , "\n" ;
   SetValueFloat( $varid , $theValue);
   //$theVarID = $table["$theSensorID"];
   $varid = CreateVariableByName($CatID, "$theVarID"." Luftfeuchte", 2, "~Humidity.F");
   IPS_SetInfo($varid, "$theSensorID");
   $theValue = (float) $sensor->Humidity;
   if ($theValue > 100) { $theValue = 0;} ;
   echo $theSensorID, " in " , $varid," ", $theVarID, " Wert: ", $theValue , "\n" ;
   SetValueFloat( $varid , $theValue);
   }

 }

//var_dump ($CatID);

echo $xml->DevicesConnected, " Sensoren angeschlossen \n";
echo $xml->LoopTime, " s Looptime\n";

//Zuordnung der Sensor IDs zu meinen Variablen IDs
//$CatID = CreateCategoryByName(0, $adresse);
//$anzahlSensoren = $xml->DevicesConnected;

$finish = microtime(true);
}
//SetValueFloat(48351,1000*($finish-$start));

//=======================================ENDE==================================================================


function CreateVariableByName($fatid, $name, $type, $profile = "")
{
    $vid = @IPS_GetVariableIDByName($name, $fatid);
    if($vid === false)

	{
        $vid = IPS_CreateVariable($type);
        IPS_SetParent($vid, $fatid);
        IPS_SetName($vid, $name);
		//If (isset($table["$theSensorID"])) IPS_SetInfo($varid, "$theSensorID");

        if($profile !== "")
        {
            IPS_SetVariableCustomProfile($vid, $profile);
			//Nur logging bei Profile
			AC_SetLoggingStatus(@IPS_GetInstanceIDByName("Archive", 0) , $vid, true);
            IPS_ApplyChanges(@IPS_GetInstanceIDByName("Archive", 0));
        }
    }
    return $vid;
}

function CreateCategoryByName($fatid, $name)
{
    $CatID = @IPS_GetCategoryIDByName($name, $fatid);

    if($CatID === false)
    {
        $CatID = IPS_CreateCategory();    // Kategorie anlegen
          IPS_SetName($CatID, $name);     // Kategorie benennen
          IPS_SetParent($CatID, $fatid);
    }
    return $CatID;
}


function CreateScriptByName($ParentID, $ZielSkript_Name,$Script_Inhalt)
{
$Script_ID = @IPS_GetScriptIDByName($ZielSkript_Name, $ParentID);


if ($Script_ID === false)
 {
    fopen("$ZielSkript_Name.ips.php","w");
    $Script_Path = "$ZielSkript_Name.ips.php";
    $Script_ID = IPS_CreateScript(0);
    $Script = IPS_GetScript($Script_ID);
    unlink(IPS_GetKernelDir() . '/scripts/' . $Script['ScriptFile']);
    IPS_SetScriptFile($Script_ID, $Script_Path);
    IPS_SetParent($Script_ID, $ParentID);
    IPS_SetName($Script_ID, $ZielSkript_Name);
    IPS_SetScriptContent($Script_ID, $Script_Inhalt);
	IPS_SetHidden($Script_ID, true);
	IPS_SetInfo($Script_ID, "automatisch angelegt");
	echo "Das Skript mit dem Namen '$ZielSkript_Name' angelegt!" ;

 }
else
	{
    	//nur Ausf�hren wenn Variable leer
		if (GetValueString($ParentID) == "") IPS_RunScript($Script_ID);

	}
 return $Script_ID;
}


function SetTimerByName_InMinuten($parentID, $name, $Minuten) {
    Global ${$_IPS['SELF']};
   $eid = @IPS_GetEventIDByName($name, $parentID);
   if($eid === false) {
       $eid = IPS_CreateEvent(1);
      IPS_SetParent($eid, $parentID);
      IPS_SetName($eid, $name);
      IPS_SetInfo($eid, "created by script #".$_IPS['SELF']);
   }
   if ($Minuten === false) {
      IPS_SetEventActive($eid, false);
       return $eid;
   }
   else {

	   IPS_SetEventCyclic($eid, 0 /* Keine Datums�berpr�fung */, 0, 0, 2, 2 /* Min�tlich */ , $Minuten /* Alle x Minuten */);
	   IPS_SetEventActive($eid, true);
       return $eid;
    }
}

function IdentTrueFalse($Value)
{
    if($Value == "0")
        {
      $result = false;
        }
    else
        {
      $result = true;
        }
    return $result;
}

//getVariableProfileCreationCode("~Temperature.FHT");
//getVariableProfileCreationCode("~Temperature.FHT", "TemperatureTest");

// erster Funktionsparameter: Profilname, zweiter Parameter (optional): neuer Profilname
function getVariableProfileCreationCode ($profileName, $newProfileName = "")
{
    $profile = IPS_GetVariableProfile($profileName);
    if ($profile !== false)
    {
        $profileName = (strlen($newProfileName) > 0) ? $newProfileName : $profileName;
        echo 'IPS_CreateVariableProfile("'.$profileName.'", '.$profile['ProfileType'].');'."\n";
        echo 'IPS_SetVariableProfileText("'.$profileName.'", "'.$profile['Prefix'].'", "'.$profile['Suffix'].'");'."\n";
        echo 'IPS_SetVariableProfileValues("'.$profileName.'", '.$profile['MinValue'].', '.$profile['MaxValue'].', '.$profile['StepSize'].');'."\n";
        echo 'IPS_SetVariableProfileDigits("'.$profileName.'", '.$profile['Digits'].');'."\n";
        echo 'IPS_SetVariableProfileIcon("'.$profileName.'", "'.$profile['Icon'].'");'."\n";
        foreach ($profile['Associations'] as $association)
        {
            echo 'IPS_SetVariableProfileAssociation("'.$profileName.'", '.$association['Value'].', "'.$association['Name'].'", "'.$association['Icon'].'", '.$association['Color'].');'."\n";
        }
        echo "\n";
    }
}
?>
