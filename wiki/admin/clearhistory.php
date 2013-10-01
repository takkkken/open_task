<?php
	session_start();
	if(!isset($_SESSION['admin']))exit;

	require_once("../config.php");
	$page = $_POST['page'];
	$all = $_POST['all']; // true=delete all pages, false = current page, int = older than n days


	$where = '';
	if(is_numeric($all)){
		$where = "DATEDIFF(CURDATE(), `revision_time`) > $all";
	}else{
		$where = $all=='true' ? '1' : "node_id=$page AND language='{$_COOKIE['lang']}'";
	}

	$sql = "DELETE FROM `revision` WHERE $where";
	$result = mysql_query($sql,$con) or die("Database Error - Unable to retrive page.");
	
?>
