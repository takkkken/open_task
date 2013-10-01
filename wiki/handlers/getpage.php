<?php
require_once("../config.php");
require_once("../class/tags.class.php");
include("./editcheck.php");

if($page==null) $page = $_GET['id'];
if(!is_numeric($page)) exit; // extra precaution

if(!isset($_POST['lang'])) $lang=$_GET['lang'];
if(strlen($lang) > 2) return;

$sql = "SELECT * FROM page INNER JOIN node ON node.node_id=page.node_id WHERE node.node_id=$page AND page.language='$lang'";
$result = mysql_query($sql,$con) or die("Database Error ".mysql_error());

if(mysql_num_rows($result)==0){
	include('language.php');

    $pagetext = $language->menu->edit;
} else {

    $pid = mysql_result($result, 0, 'parent_id');

    if($pid > 0)
        $pagetext = stripslashes(mysql_result($result, 0, 'page_text'));
}
$tagger = new Tag($con,$page,$lang);
$tags = $tagger->getTags("csv");
$editable = isEditable($page, $lang, $con, $CFG_REGISTERED_ONLY);

$json = array("page"=>$pagetext, "tags"=>$tags, "editable"=>$editable);
echo json_encode($json);


?>