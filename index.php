<?php

/**
 * index.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

//ログアウト時、ログイン画面表示
if(isset($_GET["logout"])){
?>

<?
	unset($_SERVER['PHP_AUTH_USER']);
	unset($_SERVER['PHP_AUTH_PW']);

	sleep(1);

	header("WWW-Authenticate: Basic realm=\"CBTS\"");
	header("HTTP/1.0 401 Unauthorized");

	//ログイン画面
	require 'login.php';
	exit();

}


require_once 'topic_list.php';
