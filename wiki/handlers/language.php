<?php
	$lang = $_GET['lang'];
	if($lang=='') $lang = $_POST['lang'];
	if(strlen($lang) > 2) return;
	
	if(!preg_match('/[a-z]{2}/',$lang)) exit;
	$json_string = file_get_contents ("../language/$lang.json");
	
	$language = json_decode($json_string);
?>