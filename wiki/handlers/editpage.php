<?php
require_once("../config.php");
include('language.php'); 
include('buttons.php'); 
session_start();

$page = $_GET['id'];
if(!is_numeric($page)) exit;

$lang = $_GET['lang'];
if(strlen($lang) > 2) return;

$clip = $_GET['clip'];
$admin = false;
if(isset($_SESSION['uid'])){
	$sql = "SELECT level FROM user WHERE user_id={$_SESSION['uid']}";
	$result = mysql_query($sql,$con) or die("Database Error - Unable to retrive user info. ".mysql_error());
	if(mysql_num_rows($result) > 0)
		$admin = mysql_result($result, 0, 'level')=='admin';

}
$loggedon = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';

header("Content-Type: text/html");
$path = $_GET['path'];
$pathinput = '';
if($path!=''){
	$parts = explode("/", $path);
	$title = preg_replace("/([a-z])([A-Z])/", "$1 $2", $parts[count($parts)-1]); // wiki word
	$text = "=$title=";
	$locked = 0;
	$pathinput = "<input type='hidden' id='path' name='path' value='$path'></input>";
}else{
	$sql = "SELECT page_text,label,locked FROM page WHERE node_id=$page AND language='$lang'";
	$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());
	$locked = 0;
	if(mysql_num_rows($result) > 0){
		$text =  mysql_result($result, 0, 'page_text');
		$title = mysql_result($result, 0, 'label');
		$locked = mysql_result($result, 0, 'locked');
	}else{
		$sql = "SELECT label FROM node WHERE node_id=$page";
		$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());
		$title = mysql_result($result, 0, 'label');
		$text = "=$title=";
	}
}

$ip=$_SERVER['REMOTE_ADDR'];

$sql = "SELECT ip_address FROM blocked WHERE ip_address='$ip'";
$result = mysql_query($sql,$con) or die("Database Error - Unable to retrive page.");
$blocked = mysql_num_rows($result) > 0;

$registered = ($CFG_REGISTERED_ONLY && $loggedon) || !$CFG_REGISTERED_ONLY;

$edit = ($locked == 0 && !$blocked && $registered) || $admin;

$html =  "<div style='float:left;color:#008;font-size:18px;'> $title</div>";
if($edit){
	$html .= buttons();
}

$html .= "<div style='float:right;margin-right:12px;'><input type='image' src='images/system/toggleedit.png' class='image' title='Toggle View' onclick='toggleedit();' /></div>";

$html .=  "<div style='height:100%;clear:both;'>";

$ro = "";
if(!$edit) $ro = " readonly";

$html .=  " <textarea id='edittext' rows='28' style='float:left;width:48%;border:1px solid black;padding-left:5px;' onkeyup='inlinepreview();' onfocus='inlinepreview();' onmouseup='previewscroll();' onscroll='previewscroll();'$ro>$text</textarea>";
$html .=  " <div id='previewbox' style='float:right;height:450px;width:48%;overflow:auto;border:1px solid black;margin-top:2px;padding-left:5px;'></div>";
$html .=  " <div style='clear:both'></div>";
$html .=  " <div id='editdiff' style='margin-top:2px;'></div>";

$html .=  " <div style='clear:both;'></div>";
//$html .=  "</div>";

$html .=  "<div style='clear:both;margin-top:20px;'>";
if($edit){
	$html .=  " ".$language->comment.": <input id='commente' style='width:280px;margin-left:5px;'></input>";
}
	$html .=  " <div style='display:inline;margin-left:30px;'><input type='button' value='".$language->cancel."' onclick='tree.click(\"$page\")';></input></div>";

if($edit){
	$html.= $pathinput;
	$html .=  " <div style='display:inline;padding-left:30px;display:inline'>";
	$html .=  "  <input type='button' value='".$language->save."' onclick='editsave();'></input></div>";
	$html .=  " <div style='display:inline;padding-left:30px;display:inline'>";
	$html .=  "  <input type='button' value='{$language->history->diff}' onclick='editdiff();'></input></div>";
	$html .=  " <div style='display:inline;padding-left:30px;display:inline'>";
	$html .=  "  $language->preview.<input id='showpreview' type='checkbox' checked='checked'></input></div>";
	$html .=  " <div style='display:inline;padding-left:10px;display:inline'>";
	$html .=  "  $language->scroll.<input id='autoscroll' type='checkbox' checked='checked'></input></div>";

	session_start();
	if(isset($_SESSION["uid"])){
		$sql = "SELECT COUNT(*) AS cnt FROM `subscription` WHERE user_id={$_SESSION["uid"]} AND page_id=$page AND language='$lang'";
		$result = mysql_query($sql,$con);
		$cnt = mysql_result($result, 0, 'cnt');
		$check = $cnt > 0 ? " checked='checked'" :"";
		$html .=  " <div style='float:right'>";
		$html .=  "  <input id='esub' type='checkbox' value='subscribe' onclick='subscribe();'$check></input>".$language->subscribe."</div>";
	}
	$html .=  "</div>";
}else{
	$html .=  " <div style='display:inline;padding-left:30px;display:inline'>";
	$html .=  "  $language->preview.<input id='showpreview' type='checkbox' checked='checked'></input></div>";
	$html .=  " <div style='display:inline;padding-left:10px;display:inline'>";
	$html .=  "  Scroll.<input id='autoscroll' type='checkbox' checked='checked'></input></div>";
}

echo stripslashes($html);
?>
