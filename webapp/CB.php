<?php
/**
 * CB.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

require_once 'CB_conf.php';
require_once 'CB_mime_type.php';
require_once  WEBAPP_DIR   . '/conf/'.DB_NAME.'.php';
require_once  LIB_DIR   . '/gladius/gladius.php';
require_once  LIB_DIR   . '/adodb_lite/adodb.inc.php';
require_once  CLASS_DIR . '/CB_Functions.php';
require_once  CLASS_DIR . '/CB_DB.php';

if(file_exists(WEBAPP_DIR . '/../../global_conf.php')){
	require_once WEBAPP_DIR . '/../../global_conf.php';
	$GLOBALS['auth_user'] = $GLOBALS['auth_user'] + $GLOBALS['default_user'];
}
//print_r($GLOBALS['auth_user']);exit();

if(
    ( $GLOBALS['auth_user'][$_SERVER['PHP_AUTH_USER']][2]==$_SERVER['PHP_AUTH_PW'] || 
     $GLOBALS['auth_user'][$_SERVER['PHP_AUTH_USER']][2]==md5($_SERVER['PHP_AUTH_PW'])) AND
     isset($_SERVER['PHP_AUTH_PW']) AND $_SERVER['PHP_AUTH_USER']
    ){
	$GLOBALS['user'] = $GLOBALS['auth_user'][$_SERVER['PHP_AUTH_USER']];
	$GLOBALS['user']['user_name'] = $GLOBALS['user'][0];
	$GLOBALS['user']['user_mail'] = $GLOBALS['user'][1];
	$GLOBALS['user']['admin']     = $GLOBALS['user'][3];
//print_r($GLOBALS['user']);exit();
}

if(is_null($GLOBALS['user'])){
	sleep(1);

	header("WWW-Authenticate: Basic realm=\"CBTS\"");
	header("HTTP/1.0 401 Unauthorized");
	//ƒƒOƒCƒ“‰æ–Ê
	require 'login.php';
	exit();
}

$db = new CB_DB;

//$GLOBALS['topic_to_user'] = $db->getAll("SELECT * FROM modified_user_count WHERE user_name NOT LIKE '{$GLOBALS['user']['user_name']}' ORDER BY modified_count DESC");
$GLOBALS['topic_to_user'] = $db->getAll("SELECT * FROM modified_user_count ORDER BY modified_count DESC");

foreach($GLOBALS['auth_user'] as $_auth_user){
	if(!$_auth_user[3]){
		$_topic_to_user_exists = 0;
		foreach($GLOBALS['topic_to_user'] as $_topic_to_user){
			if($_topic_to_user['user_name']==$_auth_user[0]){
				$_topic_to_user_exists = 1;
			}
		}
		if(!$_topic_to_user_exists){
			$GLOBALS['topic_to_user'][] = array("user_name"=>$_auth_user[0],);
		}
	}
}
//asort($GLOBALS['topic_to_user']);