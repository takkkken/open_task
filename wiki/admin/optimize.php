<?php
	session_start();
	if(!isset($_SESSION['admin']))exit;
	require_once("../config.php");

	$alltables = mysql_query('SHOW TABLES');

	while ($table = mysql_fetch_assoc($alltables)){
		foreach ($table as $db => $tablename){
			echo "OPTIMIZE TABLE `$tablename`"."<br>";
			mysql_query("OPTIMIZE TABLE `$tablename`",$con) or die(mysql_error());
		}
	}

?>
