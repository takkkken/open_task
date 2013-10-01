<?php
	$lang = $_GET['lang'];
	if($lang=='') $lang = $_POST['lang'];
	if($lang=='') $lang = 'ja';
	$json_string = file_get_contents ("language/$lang.json");
	
	$language = json_decode($json_string);
?>