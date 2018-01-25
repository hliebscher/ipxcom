<?php

 //Version 2.0 EBV     2018


IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');



$table["2D041673B5E2FF28"] = "Vorlauf Heizung";
$table["250416705291FF28"] = "Rücklauf Heizung";
$table["3704167043ADFF28"] = "Warmwasser";
$table["E8000007D31AD028"] = "Innenraum";
$table["8C00000202255826"] = "Innen";
$table["A4000001C55B2726"] = "Außen";

$table["FD041673BF62FF28"] = "Zirkulation Warmwasser";
$table["D8041673CD36FF28"] = "Warmwasser";
$table["EC0416705521FF28"] = "Vorlauf Solar";
$table["E5041673BE4DFF28"] = "Rücklauf Solar";
$table["CA000007D4195028"] = "Innen";
$table["DA0000020384C126"] = "Innen";
$table["08041673CF7EFF28"] = "Vorlauf Heizung";
$table["D3041673D55EFF28"] = "Rücklauf Heizung";
$table["A6000001C570DC26"] = "Außen";

$table["D2041673E644FF28"] = "Vorlauf Heizung";
$table["CE041673E68CFF28"] = "Warmwasser";
$table["45041673D1A6FF28"] = "Vorlauf Solar";
$table["4D04167057D1FF28"] = "Zirkulation Warmwasser";
$table["900416704693FF28"] = "Rücklauf Solar";
$table["1C041673CF53FF28"] = "Rücklauf Heizung";
$table["CB000007AECD8128"] = "Innenraum";
$table["160000020252E026"] = "Innen";
$table["54000001C8E1E726"] = "Außen";

$table["970316702C44FF28"] = "Zirkulation Warmwasser";
$table["F10316611096FF28"] = "Warmwasser";
$table["3C0516704DCAFF28"] = "Rücklauf Heizung";
$table["9A041673E6C6FF28"] = "Vorlauf Heizung";
$table["D0000001C567CC26"] = "Außen";
$table["DC000007AF050328"] = "Innenraum";
$table["DE00000202316C26"] = "Innen";

$table["F4041673BD06FF28"] = "Heizwerk 1";
$table["B50316611116FF28"] = "Heizwerk 2";
$table["B4041673BD61FF28"] = "Vorlauf Heizung";
$table["9905167053E1FF28"] = "Rücklauf Heizung";
$table["10041673D36DFF28"] = "Innenraum";
$table["21000001C5873F26"] = "Außen";

$table["7904167059DDFF28"] = "Zirkulation  Warmwasser";
$table["7A041673D652FF28"] = "Warmwasser";
//$table["7F041673CD75FF28"] = "Bitte rauslöschen";

$table["17041673B63FFF28"] = "Innen";
$table["CF041673D823FF28"] = "Heizkreis 1 Vorlauf";
$table["CA041673D1BFFF28"] = "Heizkreis 1 Rücklauf";
$table["7E031660AFFEFF28"] = "Heizkreis 2 Rücklauf";
$table["9003166117B2FF28"] = "Heizkreis 2 Vorlauf";
$table["18041673C218FF28"] = "Zirkulation  Warmwasser";
$table["25031660D50DFF28"] = "Warmwasser";


$table["6D041673D0E5FF28"] = "Zirkulation  Warmwasser";
$table["DC041673C24FFF28"] = "Warmwasser";

//-----------------------------------------------------------
//$ip = "10.141.48.6";

$iplist[]="10.141.48.2";
$iplist[]="10.141.48.3";
$iplist[]="10.141.48.4";
$iplist[]="10.141.48.5";
$iplist[]="10.141.48.6";




SetTimerByName_InMinuten($_IPS['SELF'], "Timer", 1);  // Legt einen Timer für dieses Skript an was alle x Minuten ausführt wird

//Profil Sollwerte

//Profil ABW
$ProfilABW = "ABW";

//MAX Zeit ABW

//E-Mail Alarm Variable

//Logging




foreach ($iplist as $ip) {

$start = microtime(true);

$ctx = stream_context_create(array(
    'http' => array(
        'timeout' => 25
        )
    )
);

if (Sys_Ping($ip, 1000) ==0) continue;

//echo   (Sys_Ping($ip, 2000))     ;

$xmlstr = @file_get_contents("http://admin:BTmdH1Bh@".$ip."/details.xml",0,$ctx);
// echo        $xmlstr;
//print $xmlstr;

if (false == $xmlstr ) {
  //SetValueFloat(48351 /**/, 0.0);
  //SetValueInteger(23971, 0);
  return;
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

//########################################################################
$CatID_Alarme = CreateCategoryByName($Ebene, "Alarme");
$varid = CreateVariableByName($CatID_Alarme, "Heizungsfirma", 3 ,"~String");
IPS_SetVariableCustomAction($varid, 20355 /*[scripte\Action Script]*/);
$varid = CreateVariableByName($CatID_Alarme, "E-Mail Heizungsfirma", 3 ,"~String");
IPS_SetVariableCustomAction($varid, 20355 /*[scripte\Action Script]*/);
$varid = CreateVariableByName($CatID_Alarme, "E-Mail Verwaltung", 3 ,"~String");
IPS_SetVariableCustomAction($varid, 20355 /*[scripte\Action Script]*/);
//########################################################################
$CatID_Sollwerte = CreateCategoryByName($Ebene, "Sollwerte");
$DI = CreateDummyInstance ("Hier bitte die Sollwerte und die maximale +/- Abweichung eintragen.", $CatID_Sollwerte , $Position=0);
$DI = CreateDummyInstance ("Wenn die Abweichung '-1' ist, wird die Auswertung und Anzeige für diesen Wert deaktiviert", $CatID_Sollwerte , $Position=0);
//########################################################################
$CatID_Grafik = CreateCategoryByName($Ebene, "Grafik");
$varid = CreateVariableByName($CatID_Grafik, "HighCharts", 3 ,"~HTMLBox");
//$ZielSkript_Name = ;
$Script_Inhalt = '<?php

	includeScript(34575) ;

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
   IPS_SetVariableCustomAction($varid_Sollwerte, 20355 /*[scripte\Action Script]*/);

   $varid_ABW = CreateVariableByName($CatID_Sollwerte, "$theVarID"." Temperatur max +- vom Soll", 2, $ProfilABW);
   IPS_SetVariableCustomAction($varid_ABW, 20355 /*[scripte\Action Script]*/);

   $varid_TMax = CreateVariableByName($CatID_Sollwerte, "$theVarID"." Temperatur max Fehlermeldungen", 2, $ProfilABW);
   IPS_SetVariableCustomAction($varid_TMax, 20355 /*[scripte\Action Script]*/);


   $varid = CreateVariableByName($CatID, "$theVarID"." Temperatur", 2, "~Temperature");

   //IPS_SetInfo($varid, "$theSensorID");
   // print_r(IPS_GetVariable($varid)) ;

   $theValue = (float) $sensor->Temperature;
    echo $theSensorID, " in " , $varid," ", $theVarID, " Wert: ", $theValue , "\n" ;
   SetValueFloat( $varid , $theValue);

		//Sollwerte ändern
	if (GetValueFloat($varid_Sollwerte) == 0)  SetValueFloat( $varid_Sollwerte , round($theValue));
	if (GetValueFloat($varid_TMax) == 0)  SetValueFloat( $varid_TMax , 19);
	//==========================================Fehler loggen ================================================================
	$VarSoll =		GetValueFloat($varid_Sollwerte);
	$VarIst  =		round($theValue,2);
	$VarABW  =		GetValueFloat($varid_ABW);
	$VarTMax =      GetValueFloat($varid_TMax);

	$VarATempID = @IPS_GetVariableIDByName("Außen Temperatur", $CatID);
		if ($VarATempID === false)
    		echo "Variable nicht gefunden!", "\n";
		else
    		//echo "Die Variablen-ID lautet: ". $VarATempID, "\n";
	$VarATemp =   round(GetValueFloat($VarATempID),0);
			//echo  $VarATemp ," ", $VarTMax ,"\n";

	if (($VarABW > -1) and ($VarATemp < $VarTMax)) {

	//=======================Nur bei Abweichung loggen ========================================================================
	//if ($theValue >= (GetValueFloat($varid_Sollwerte)+GetValueFloat($varid_ABW)))
	if (($VarIst)>($VarSoll+$VarABW) or  ($VarIst)<($VarSoll)-($VarABW))  	    {
	$number = IPS_RunScriptWaitEx(33602 /*[Alarme\Fehlermeldungen\Dashboard]*/, array('action' => 'add_new', 'text' => 'ID:'.$varid.' '.$adresse.' '.$theVarID.' Temperatur Abweichung zu hoch '.' IST=>'.$VarIst.' SOLL=>'.$VarSoll.' +/-=>'.$VarABW, 'expires' => 0, 'removable' => true,'Uhrzeit' => time(),'VarID' => $varid,'VarSoll' => $VarSoll,'VarIst' => $VarIst,'VarABW' => $VarABW  ));
																			    }
	else 	{
			$success = IPS_RunScriptWaitEx(33602 /*[Alarme\Fehlermeldungen\Dashboard]*/, array('action' => 'remove','number' => $varid));
  			}

	$number = IPS_RunScriptWaitEx(31054 /*[Anzeige alle Systeme\Meldungen\Anzeige]*/, array('action' => 'add_new_Anzeige', 'text' => 'ID:'.$varid.' '.$adresse.' '.$theVarID.':', 'expires' => 0, 'removable' => false,'Uhrzeit' => time(),'VarID' => $varid,'VarSoll' => $VarSoll,'VarIst' => $VarIst,'VarABW' => $VarABW ));
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
    	//nur Ausführen wenn Variable leer
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

	   IPS_SetEventCyclic($eid, 0 /* Keine Datumsüberprüfung */, 0, 0, 2, 2 /* Minütlich */ , $Minuten /* Alle x Minuten */);
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
