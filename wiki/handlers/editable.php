<?php
require_once("../config.php");
include("./editcheck.php");

$page = $_POST['id'];
if(!is_numeric($page)) exit;
$lang=$_POST['lang'];
if(strlen($lang) > 2) return;

echo isEditable($page, $lang, $con, $CFG_REGISTERED_ONLY);

?>
