<?php
/**
 * CB_config.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */


//設定ファイルのインクルード（呼び出し元の階層の違いの為、設定ファイルを探す）
if(file_exists("../@config.ini")){
	$ini = @parse_ini_file("../@config.ini");
}else{
	$ini = @parse_ini_file( "./@config.ini");
}


//タイトル設定
define('SITE_NAME' , $ini['SITE_NAME']);
define('SITE_URL'  , 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . '/');

//FROMのメールアドレスを設定
define('FROM_MAIL' , $ini['FROM_MAIL']);
define('PJ_NAME'   , $ini['LIST_NAME']);	//OpenTaskの名前
define('WIKI_NAME'   , $ini['WIKI_NAME']);	//Wikiの名前


//print_r($ini);
///////////////////////////////
// データベース設定
///////////////////////////////
/*
define('DB_TYPE'   , 'gladius');
define('DB_NAME'   , 'default');
*/

//TODO:環境依存
define('DB_TYPE'   , 'mysql');
define('DB_HOST'   , $ini['SERVER']);
define('DB_USER'   , $ini['USER']);
define('DB_PASS'   , $ini['PASSWORD']);
define('DB_NAME'   , $ini['DATABASE']);




///////////////////////////////
// フォルダ設定
///////////////////////////////

//バグ:Windows環境ではディレクトリが異常になる
//define('ROOT_DIR'  , preg_replace("/\/webapp$/is","",dirname(__FILE__)));
//TODO:環境依存
define('ROOT_DIR', $ini['ROOT_DIR']);
define('WEBAPP_DIR', ROOT_DIR . '/webapp');
define('CLASS_DIR' , ROOT_DIR . '/webapp/class');
define('LIB_DIR'   , ROOT_DIR . '/webapp/lib');
define('VAR_DIR'   , ROOT_DIR . '/webapp/var');
define('PEAR_DIR'  , LIB_DIR . '/pear');
ini_set('include_path', PEAR_DIR);



define('DATA_DIR'  , VAR_DIR  . '/data/' . DB_NAME);

global $GLADIUS_DB_ROOT;
$GLADIUS_DB_ROOT = VAR_DIR . '/db/';
