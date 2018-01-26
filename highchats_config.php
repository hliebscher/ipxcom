<?php
	ini_set('memory_limit', '-1');

	Global $CfgDaten; // damit kann der Script auch von anderen Scripten aufgerufen werden und bereits mit CfgDaten vorkonfiguriert werden

	$ParentID = IPS_GetParent(IPS_GetParent(IPS_GetParent($_IPS['SELF'])));
	$ObjektID = @IPS_GetObjectIDByName("Daten", $ParentID);

	$werteliste = (IPS_GetChildrenIDs($ObjektID));
	//print_r ($werteliste);
	//print_r(IPS_GetObject(14825 /*[Brandesstrasse 7\Daten\Zirkulation Warmwasser Temperatur]*/));
	//print_r(IPS_GetVariable(14825 /*[Brandesstrasse 7\Daten\Zirkulation Warmwasser Temperatur]*/));
	// IPS Variablen ID¥s

	// Überschriften

	$CfgDaten['ContentVarableId']= -1;  // ID der String Variable in welche die Daten geschrieben werden (-1 oder ¸berhaupt nicht angeben wenn die Content Variable das ¸bergordnete Element ist)
	$CfgDaten['HighChartScriptId']= 31045 /*[scripte\Highcharts_V3.01.ips]*/ ;  				// ID des Highcharts Scripts


	// Highcharts oder Highstock (default = Highcharts
	$CfgDaten['Ips']['ChartType'] = 'Highstock';

	// Zeitraum welcher dargestellt werden soll (kann durch die Zeitvorgaben in den Serien ver‰ndert werden)

	if (!isset($CfgDaten["StartTime"]))
	 	$CfgDaten["StartTime"] = mktime(0,0,0, date("m", time()), date("d",time())-60, date("Y",time())); // ab heute 00:00 Uhr
	if (!isset($CfgDaten["EndTime"]))
		$CfgDaten["EndTime"] = mktime(23,59,59, date("m", time()), date("d",time()), date("Y",time())); // ab heute 23:59 Uhr, oder //$CfgDaten["EndTime"] = time();   // = bis jetzt

	// damit wird die Art des Aufrufes festgelegt
	$CfgDaten['RunMode'] = "script"; 	// file, script, popup

	// wenn Popup dann muss die WebfrontConfigId und der Titele ¸bergeben werden
	if ($CfgDaten['RunMode'] == "popup")
	{
		$CfgDaten['WebFrontConfigId'] = IPS_GetInstanceIDByName("WebFront", 0);
		$CfgDaten['WFCPopupTitle'] = "HighCharts";
	}

	// ‹bergabe der IP-Adresse und des Ports f¸r die Darstellung im Dashboard
	// Wichtig! Wenn Darstellung in Webfront diese Variablen auskommentieren
	//$CfgDaten['Ips']['Dashboard']['Ip'] = "127.0.0.1";
	//$CfgDaten['Ips']['Dashboard']['Port'] = "82";

	// Serien¸bergreifende Einstellung f¸r das Laden von Werten
	$CfgDaten['AggregatedValues']['HourValues'] = 3;      // ist der Zeitraum grˆﬂer als X Tage werden Stundenwerte geladen
	$CfgDaten['AggregatedValues']['DayValues'] = -1;       // ist der Zeitraum grˆﬂer als X Tage werden Tageswerte geladen
	$CfgDaten['AggregatedValues']['WeekValues'] = -1;      // ist der Zeitraum grˆﬂer als X Tage werden Wochenwerte geladen
	$CfgDaten['AggregatedValues']['MonthValues'] = -1;      // ist der Zeitraum grˆﬂer als X Tage werden Monatswerte geladen
	$CfgDaten['AggregatedValues']['YearValues'] = -1;      	// ist der Zeitraum grˆﬂer als X Tage werden Jahreswerte geladen
	$CfgDaten['AggregatedValues']['NoLoggedValues'] = 1000; 	// ist der Zeitraum grˆﬂer als X Tage werden keine Boolean Werte mehr geladen, diese werden zuvor immer als Einzelwerte geladen	$CfgDaten['AggregatedValues']['MixedMode'] = false;     // alle Zeitraumbedingungen werden kombiniert
	$CfgDaten['AggregatedValues']['MixedMode'] = false;


	$CfgDaten['title']['text'] = "Heizungsdaten für ". IPS_GetName ($ParentID);


	$CfgDaten['subtitle']['text'] = "Zeitraum: %STARTTIME% - %ENDTIME%";
	$CfgDaten['subtitle']['Ips']['DateTimeFormat'] = "(D) d.m.Y H:i";

    $CfgDaten['exporting']['enabled'] = true;

    $CfgDaten['lang']['resetZoom'] = "Zoom zurücksetzten";



	$CfgDaten['yAxis'][0]['title']['text'] = "Temperaturen";
	$CfgDaten['yAxis'][0]['Unit'] = "°C";
	$CfgDaten['yAxis'][0]['opposite'] = false;
	$CfgDaten['yAxis'][0]['tickInterval'] = 5;
	//$CfgDaten['yAxis'][0]['min'] = -5;
	//$CfgDaten['yAxis'][0]['max'] = 80;

	$CfgDaten['yAxis'][1]['title']['text'] = "Luftfeuchte";
	$CfgDaten['yAxis'][1]['Unit'] = "%";
	$CfgDaten['yAxis'][1]['opposite'] = true;


	$ts_yet                = date("(D) d.m.Y H:i", $CfgDaten["StartTime"]);
    $te_yet                = date("(D) d.m.Y H:i", time());
    $CfgDaten['subtitle']['text']       = "Zeitraum: $ts_yet - $te_yet"; // "" = Automatisch über Zeitraum

    // Legende
    $CfgDaten['legend']['enabled']                            = true;
    $CfgDaten['legend']['align']                              = "center";
    $CfgDaten['legend']['shadow']                             = true;
    $CfgDaten['legend']['floating']                           = true;
    $CfgDaten['legend']['y']                                  = 40;   // -37
    $CfgDaten['legend']['x']                                  = 0;

    $CfgDaten['chart']['spacingBottom']                        = 50;
    $CfgDaten['chart']['spacingLeft']                          = 10;
    $CfgDaten['chart']['spacingRight']                         = 20;
     $CfgDaten['rangeSelector']['enabled']                     = true;
     $CfgDaten['rangeSelector']['selected']                    = 1;
     $CfgDaten['rangeSelector']['buttons'][]     = array("type"=>'day', "count"=> 2, "text"=>'1T');
     $CfgDaten['rangeSelector']['buttons'][]     = array("type"=>'day', "count"=> 7, "text"=>'7T');
     $CfgDaten['rangeSelector']['buttons'][]     = array("type"=>'day', "count"=> 14, "text"=>'14T');
     $CfgDaten['rangeSelector']['buttons'][]     = array("type"=>'month', "count"=> 1, "text"=>'1M');
     $CfgDaten['rangeSelector']['buttons'][]     = array("type"=>'all', "text"=>'All');
     $CfgDaten['navigator']['enabled']                           = true;
     $CfgDaten['scrollbar']['enabled']                           = true;



	//Werte einlesen

	foreach ($werteliste as $sensor) {

	if (IPS_GetVariable($sensor)["VariableCustomProfile"] ==  "~Temperature") {

	$serie = array();
	$serie['name'] = IPS_GetName ( $sensor );
	$serie['Id'] = $sensor ;
	$serie['Unit'] = "°C";
	$serie['ReplaceValues'] = false;
	$serie['RoundValue'] = 0;
	$serie['type'] = "spline";
	$serie['yAxis'] = 0;
	$serie['marker']['enabled'] = false;
	$serie['shadow'] = true;
	$serie['lineWidth'] = 1;
	$serie['states']['hover']['lineWidth'] = 2;
	$serie['marker']['states']['hover']['enabled'] = true;
	$serie['marker']['states']['hover']['symbol'] = 'circle';
	$serie['marker']['states']['hover']['radius'] = 4;
	$serie['marker']['states']['hover']['lineWidth'] = 1;
	$CfgDaten['series'][] = $serie;
	}

	if (IPS_GetVariable($sensor)["VariableCustomProfile"] ==  "~Humidity.F") {

	$serie = array();
	$serie['name'] = IPS_GetName ( $sensor );
	$serie['Id'] = $sensor;
	$serie['Unit'] = "%";
	$serie['ReplaceValues'] = false;
	$serie['RoundValue'] = 0;
	$serie['type'] = "spline";
    $serie['step'] = false;
	$serie['yAxis'] = 1;
	$serie['marker']['enabled'] = false;
	$serie['shadow'] = true;
	$serie['lineWidth'] = 1;
	$serie['states']['hover']['lineWidth'] = 2;
	$serie['marker']['enabled'] = false;
	$serie['marker']['states']['hover']['enabled'] = true;
	$serie['marker']['states']['hover']['symbol'] = 'circle';
	$serie['marker']['states']['hover']['radius'] = 4;
	$serie['marker']['states']['hover']['lineWidth'] = 1;
	$CfgDaten['series'][] = $serie;
	}


	}


// Chart-Optionen "Tooltip"
   $CfgDaten['tooltip']['useHTML']                          = true;
   $CfgDaten['tooltip']['shared']                           = false;
   $CfgDaten['tooltip']['crosshairs'][]                     = array('width' =>1,'color' =>'grey','dashStyle'=>'dashdot' );
   $CfgDaten['tooltip']['crosshairs'][]                     = array('width' =>1,'color' =>'grey','dashStyle'=>'dashdot' );
   $CfgDaten['tooltip']['formatter']                        = "@function() {var unit = {
                                                                            '".@$CfgDaten['series'][0]['name']."': '".@$CfgDaten['series'][0]['Unit']."',
                                                                            '".@$CfgDaten['series'][1]['name']."': '".@$CfgDaten['series'][1]['Unit']."',
                                                                            '".@$CfgDaten['series'][2]['name']."': '".@$CfgDaten['series'][2]['Unit']."',
                                                                            '".@$CfgDaten['series'][3]['name']."': '".@$CfgDaten['series'][3]['Unit']."',
                                                                            '".@$CfgDaten['series'][4]['name']."': '".@$CfgDaten['series'][4]['Unit']."',
                                                                            '".@$CfgDaten['series'][5]['name']."': '".@$CfgDaten['series'][5]['Unit']."',
                                                                            '".@$CfgDaten['series'][6]['name']."': '".@$CfgDaten['series'][6]['Unit']."',
                                                                            '".@$CfgDaten['series'][7]['name']."': '".@$CfgDaten['series'][7]['Unit']."',
                                                                            '".@$CfgDaten['series'][8]['name']."': '".@$CfgDaten['series'][8]['Unit']."',
                                                                            '".@$CfgDaten['series'][9]['name']."': '".@$CfgDaten['series'][9]['Unit']."',
                                                                            '".@$CfgDaten['series'][10]['name']."': '".@$CfgDaten['series'][10]['Unit']."',
                                                                            '".@$CfgDaten['series'][11]['name']."': '".@$CfgDaten['series'][11]['Unit']."',
                                                                            '".@$CfgDaten['series'][12]['name']."': '".@$CfgDaten['series'][12]['Unit']."',
                                                                            }[this.series.name];
                                                                            return '<b>' + Highcharts.dateFormat('%A, %d.%m.%Y, %H:%M', this.x) + ' Uhr</b><br>' + this.series.name
                                                                                        + ': ' + '<b><span style=color:' + this.series.color + '>' + this.y
                                                                                        + unit + '</b></span>';
                                                                            }@";


	// Highcharts-Theme
	//	$CfgDaten['HighChart']['Theme']="grid.js";   // von Highcharts mitgeliefert: dark-green.js, dark-blue.js, gray.js, grid.js
	$CfgDaten['HighChart']['Theme']="ips.js";   // IPS-Theme muss per Hand in in Themes kopiert werden....

	// Abmessungen des erzeugten Charts
	$CfgDaten['HighChart']['Width'] = "100%"; 	// in px,  0 wird auch in 100% konvertiert
	$CfgDaten['HighChart']['Height'] = 600; 		// in px


	// -------------------------------------------------------------------------------------------------------------------------------------
	// und jetzt los ......
	$s = IPS_GetScript($CfgDaten['HighChartScriptId']); 	// Id des Highcharts-Scripts
	include($s['ScriptFile']);

	// das ist ab V3.000 der neue Aufruf

	RunHighcharts($CfgDaten);



?>
