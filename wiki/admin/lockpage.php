<?php
	session_start();
	if(!isset($_SESSION['admin']))exit;
	require_once("../config.php");
	$page = $_POST['page'];
	$lock = $_POST['lock'];
	$all = $_POST['all'];
    $lang = $_POST['lang'];
	
	$locked = $lock=='true' ? 1 : 0;
	$where = $all=='true' ? '1' : "node_id=$page AND language='$lang'";
	
	$sql = "UPDATE `page` SET locked=$locked WHERE $where";
 	$result = mysql_query($sql,$con) or die("Database Error - Unable to retrive page.");
 ?>
