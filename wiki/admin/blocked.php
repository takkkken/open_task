<?php
	session_start();
	if(!isset($_SESSION['admin']))exit;
    
	require_once("../config.php");

	$action=$_POST['action'];
	
	if($action=="add"){
		$sql = "INSERT INTO `blocked` VALUES('{$_POST['ip']}')";
        $result = mysql_query($sql,$con) or die("Database Error - Unable to insert address.");
	}else{
        $sql = "DELETE FROM `blocked` WHERE `ip_address`='{$_POST['ip']}'";
        $result = mysql_query($sql,$con) or die("Database Error - Unable to delete address.");
	}
	
	
?>
