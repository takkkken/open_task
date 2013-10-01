<?php
require_once("../config.php");
require_once("../class/revision.class.php");

$page = $_POST['id'];
if(!is_numeric($page)) exit; // something's wrong

$text = $_POST['text']; // prepare for database
if(preg_match('/\[javascript:/im', $text))
        exit;
$user = $_POST['user'];
$uid = $_POST['uid'];
$ip = $_POST['ip'];
$comment =htmlspecialchars($_POST['comment']);
$path = $_POST['path'];
if($path != ''){
	
	// create a new page from the path.
	require_once("../class/node.class.php");
	$NodeHandler = new Node($con,$_POST['lang']);
	$NodeHandler->position = 'in';
	$NodeHandler->comment = '';
	$NodeHandler->setUid($uid);
	$NodeHandler->name = $NodeHandler->PageFromPath($path); 
	$NodeHandler->target = $NodeHandler->ParentFromPath($path); // parent id
	$NodeHandler->ip=$_SERVER['REMOTE_ADDR'];
	$NodeHandler->NewNode(); // add the new node
	$NodeHandler->UpdateHistory('add'); // update
	$page = $NodeHandler->NodeFromPath($path); //
	$NodeHandler->Subscriptions($CFG_RETURN_ADDRESS, $action); 

}

$lang=$_POST['lang'];
if(strlen($lang) > 2) return;

$sql = "SELECT page_text FROM page WHERE page.node_id=$page AND language='$lang'";
$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());
$from = '';
if(mysql_num_rows($result) > 0){
	$from = addslashes(mysql_result($result,0,'page_text')); // they get removed when selected
	$sql = "UPDATE page SET page_text='$text' WHERE node_id=$page AND language='$lang'";
	$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());
}else{
	// get label
	$sql = "SELECT label FROM node WHERE node_id=$page";
	$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());
	$label = mysql_result($result, 0, 'label');
	
	// create the new page
	$sql = "INSERT INTO page (node_id,language,label,page_text,locked) ";
	$sql .= "VALUES ($page,'$lang','$label','$text',0) ";
	$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());

}

$sql = "SELECT page_text FROM page WHERE node_id=$page AND language='$lang'";    

$result = mysql_query($sql,$con) or die("Database Error - ".mysql_error());
$to = addslashes(mysql_result($result,0,'page_text'));
// TODO: can this be done without updated then comparing?
if($to==$from){ // no need for history
	include('getpage.php');
	exit;
}

// revision
$rev = new Revision($con,$page,$uid,$CFG_RETURN_ADDRESS,$lang);
$rev->save($from,'page',$comment,$to);

include('getpage.php');
?>

