<?php
/**
 * CB_config.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */


///////////////////////////////
// フォルダ設定
///////////////////////////////


//バグ:Windows環境ではディレクトリが異常になる
//define('ROOT_DIR'  , preg_replace("/\/webapp$/is","",dirname(__FILE__)));
//TODO:環境依存
define('ROOT_DIR', '/home/samba/app/open_task');
define('WEBAPP_DIR', ROOT_DIR . '/webapp');
define('CLASS_DIR' , ROOT_DIR . '/webapp/class');
define('LIB_DIR'   , ROOT_DIR . '/webapp/lib');
define('VAR_DIR'   , ROOT_DIR . '/webapp/var');
define('PEAR_DIR'  , LIB_DIR . '/pear');
ini_set('include_path', PEAR_DIR);

///////////////////////////////
// データベース設定
///////////////////////////////
/*
define('DB_TYPE'   , 'gladius');
define('DB_NAME'   , 'default');
*/

//TODO:環境依存
define('DB_TYPE'   , 'mysql');
define('DB_HOST'   , 'localhost');
define('DB_USER'   , 'root');
define('DB_PASS'   , 'pwtech456');
define('DB_NAME'   , 'opentask');


define('DATA_DIR'  , VAR_DIR  . '/data/' . DB_NAME);

global $GLADIUS_DB_ROOT;
$GLADIUS_DB_ROOT = VAR_DIR . '/db/';