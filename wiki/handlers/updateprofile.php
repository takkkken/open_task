<?php
session_start(); 
if(!isset($_SESSION['uid']))exit;


if($_SESSION['token'] != $_POST['token'] || !isset($_SESSION['token'])) {
   exit;
}

require_once("../config.php");

$pass = md5($_POST['pass']);
$email = htmlspecialchars($_POST['email']);
$uid = $_SESSION['uid'];

$subscribe = $_POST['subscribe'] == 'true' ? 1 : 0;

$set = '';
if($_POST['pass'] != '')
	$set = "password='$pass',";

$set .= "email='$email',subscribe=$subscribe";

$sql = "UPDATE user SET $set WHERE user_id=$uid";

$result = mysql_query($sql, $con) or die("{'response':'Database Error, Unable to update profile'}");
echo "{'response':'ok'}";

?>
