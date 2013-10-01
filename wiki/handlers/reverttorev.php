<?php
require_once("../config.php");

$rev = $_GET['rev'];
$uid = $_GET['uid'];
if(!is_numeric($rev) || !is_numeric($uid)) exit; 

$lang=$_GET['lang'];
if(strlen($lang) > 2) return;

$ip=$_SERVER['REMOTE_ADDR'];

$comment =$_POST['revcomment'];

$sql = "SELECT page_text,node_id FROM revision WHERE revision_id=$rev";
$result = mysql_query($sql,$con) or die("{'response':'Database Error - Unable to select from history.'}");

$page_text = mysql_result($result,0,'page_text');
$node_id = mysql_result($result, 0, 'node_id');

$sql = "SELECT page_text FROM page WHERE node_id=$node_id AND language='$lang'";
$result = mysql_query($sql,$con) or die("{'response':'Database Error - Unable pull current text.'}");

$bad_text = mysql_result($result,0,'page_text');

if($bad_text == $page_text){
	echo "{'response':'Nothing to do'}";
	exit;
}

$page_text = addslashes($page_text);
$sql = "UPDATE page SET page_text='$page_text' WHERE node_id=$node_id AND language='$lang'";
$result = mysql_query($sql,$con) or die("{'response':'Database Error - Unable to revert page.'}");

$now = date('YmdHis');
$bad_text = addslashes($bad_text);
$sql = "INSERT INTO `revision` (revision_id,node_id,language,user_id,user_ip, `type`, page_text, comment,revision_time) ";
$sql .= "VALUES ('',$node_id,'$lang',$uid,'$ip', 'page', '$bad_text', 'REVERT to rev $rev:$comment','$now') ";

$result = mysql_query($sql,$con) or die("{'response':'Database Error - Unable to update history.'}");

$text = stripslashes($page_text);
echo("{'response':'ok','node':'$node_id'}");

?>