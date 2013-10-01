<?php
$_CONFIG = true;

# obvious db stuff
$CFG_USER = 'root';
$CFG_PASSWORD = 'pwtech456';
$CFG_SERVER = 'localhost';
$CFG_DATABASE = 'opentask';

# this is the return address for emails
$CFG_RETURN_ADDRESS = '';

# for email subject
$CFG_SITE_NAME = '';

# this if true requires registration before edit
$CFG_REGISTERED_ONLY = FALSE;

$con = mysql_connect($CFG_SERVER,$CFG_USER,$CFG_PASSWORD);
mysql_select_db($CFG_DATABASE, $con);
mysql_set_charset('utf8',$con);

//This stops SQL Injection in POST vars 
foreach ($_POST as $key => $value) { 
	$_POST[$key] = mysql_real_escape_string(str_replace("\\","\\\\",$value)); 
} 

//This stops SQL Injection in GET vars 
foreach ($_GET as $key => $value) { 
	$_GET[$key] = mysql_real_escape_string(htmlspecialchars ($value,ENT_QUOTES,'UTF-8')); 
}
?>
