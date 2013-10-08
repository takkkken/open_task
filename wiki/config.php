<?php
$_CONFIG = true;

//設定ファイルのインクルード（呼び出し元の階層の違いの為、設定ファイルを探す）
if(file_exists("../@config.ini")){
	$ini = @parse_ini_file("../@config.ini");
}else{
	$ini = @parse_ini_file( "../../@config.ini");
}

//print_r($ini);

# obvious db stuff
$CFG_USER 		= $ini['USER'];
$CFG_PASSWORD 	= $ini['PASSWORD'];
$CFG_SERVER 	= $ini['SERVER'];
$CFG_DATABASE 	= $ini['DATABASE'];

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
