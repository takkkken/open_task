<?php
$cb_php = '../webapp/CB.php';
if(file_exists($cb_php)){
	require_once $cb_php;
}
$public_holiday_php = '../webapp/lib/public_holiday.php';
if(file_exists($public_holiday_php)){
	require_once $public_holiday_php;
}
require_once 'HTML/AJAX/Server.php'; 

class liveDate{
	function get_date($year,$month,$date="1",$str="date") {
		$year	= esc($year);
		$month 	= esc($month);
		$date_array		= get_month($year,$month);
		$str = "<select name=\"".$str."[date]\" id=\"".$str."[date]\">";
		foreach($date_array AS $val){
			$selected = "";
			$color 		= "";
			if($val["date"]==$date) $selected = " selected=\"selected\"";
			if($val["rc"]==1)				$color = " style=\"background-color:#CCFFFF\"";
			if($val["rc"]>1) 				$color = " style=\"background-color:#FFCCCC\"";
			$str .= "\t\t<option value=\"".$val["date"]."\"$color$selected>".$val["date"]."日 (".$val["day"].")</option>\n";
		}
		$str .= "</select>";
		return $str;
	}
}

// 日時・曜日の取得
function get_date($str,$i=0){
	$year_date		= date_normalization($str);
	$year	 	= $year_date["year"];
	$month 	= $year_date["month"];
	$date 	= $year_date["date"];

	$sche["year"] 	= date("Y",mktime(0,0,0,$month,$date+$i,$year));
	$sche["month"] 	= date("m",mktime(0,0,0,$month,$date+$i,$year));
	$sche["date"] 	= date("j",mktime(0,0,0,$month,$date+$i,$year));
	$sche["day"] 		= change_day(date("Y-m-j",mktime(0,0,0,$month,$date+$i,$year)));
	$rc 						= public_holiday($sche["year"],$sche["month"],$sche["date"]);
	$sche["holiday"]= $rc["name"];
	$sche["rc"] 		= $rc["rc"];
	$sche["Ymd"] = $sche["year"].$sche["month"].$sche["date"];
	if($sche["rc"]>0 || $sche["day"]=="土" || $sche["day"]=="日") $sche["rc"]++;
	if($sche["rc"]>1 || $sche["day"]=="日") $sche["rc"]++;
	return $sche;
}

// 月の日時・曜日の取得
function get_month($year,$month){
	$unixtime = strtotime("$year-$month-01 00:00:00");
	for($i=1;$i<=date("t",$unixtime);$i++){
		$date = "$year-$month-$i";
		$array[] = get_date($date);
	}
	return $array;
}

function get_month2($year,$month)
{
	$unixtime = strtotime("$year-$month-01 00:00:00");
	$start_day = date("w",$unixtime);
	$start_time = $unixtime - $start_day * 3600 * 24;
	$end_string = "$year-$month-".date("t",$unixtime);
	$end_day = date("w",strtotime($end_string));
	$end_time = strtotime($end_string) + (6-$end_day) * 3600 * 24;
	for($i=$start_time;$i<=$end_time;$i+=(3600 * 24))
	{
		$array[] = get_date(date("Y-m-j",$i));
	}
	return $array;
}

function get_year($year)
{
	for($i=1;$i<=12;$i++){
		$date = "$year-".sprintf("%02d",$i)."-01";
		//echo $date;
		$array[] = get_date($date);
	}
	return $array;
}

//日付の正規化
function date_normalization($str){
	if(preg_match("/\-/",$str)){
		$array = explode("-", $str);
		if(strlen($array[1])<2) $array[1] = "0".$array[1];
		if(strlen($array[2])<2) $array[2] = "0".$array[2];
		$str 	= $array[0].$array[1].$array[2];
	}

	$date["year"]   = substr($str,0,4);
	$date["month"] 	= substr($str,4,2);
	$date["date"] 	= substr($str,6,2);

	$date["hour"] 	= substr($str,9,2);
	$date["min"] 	= substr($str,12,2);
	$date["sec"] 	= substr($str,15,2);
	$date["day"] = change_day($date["year"]."-".$date["month"]."-".$date["date"]);
	return $date;
}

// エスケープ
function esc($str){
	$str = strip_tags($str);
	$str = stripslashes($str);
	$esc = array("/\"/","/'/","/%/");
	$str = preg_replace($esc,"",$str);
	$str = stripslashes($str);
	return $str;
}

// 曜日への変更
function change_day($date){
	$array = array("Monday"=>"月","Tuesday"=>"火","Wednesday"=>"水","Thursday"=>"木","Friday"=>"金","Saturday"=>"土","Sunday"=>"日");
	if($array[strftime("%A",strtotime($date))]){
		return $array[strftime("%A",strtotime($date))];
	}else{
		return $date;
	}
}

$server = new HTML_AJAX_Server();
$server->clientJsLocation = PEAR_DIR . "/data/HTML_AJAX/js/";
$server->registerClass(new liveDate());
$server->handleRequest();
