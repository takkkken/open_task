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


//サイト名	（iniから取得）
define('SITE_NAME' , $ini['SITE_NAME']);
//サイトURL
define('SITE_URL'  , 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . '/');

//Wikiの名前	（iniから取得）
define('WIKI_NAME'   , $ini['WIKI_NAME']);
//Svnの名前	（iniから取得）
define('SVNV_NAME'   , $ini['SVNV_NAME']);

//FROMのメールアドレスを設定	（iniから取得）
define('FROM_MAIL' , $ini['FROM_MAIL']);
//OpenTaskの名前	（iniから取得）
define('PJ_NAME'   , $ini['LIST_NAME']);
//種別	（iniから取得）
$GLOBALS['topic_type'] 		= $ini['TASK_TYPE'];
//優先度
$GLOBALS['topic_priority'] 	= array(
				            "1"		=>"",
				            "2"		=>"",
				            "3"  	=>"",
				            "4"		=>"",
				            "5"  	=>"",
				            );
//プロジェクト選択項目　設定	（iniから取得）
$GLOBALS['topic_project'] 	= array_flip ( $ini['PROJECT_NAMES'] );

//ディレクトリ名→プロジェクト選択項目　設定	（パスはiniから取得）
if(isset($ini['PROJECT_NAME_DIR'])){
	foreach(glob( $ini['PROJECT_NAME_DIR'] . "/*") as $dirname)
		$GLOBALS['topic_project'][basename($dirname)]="";
}

//状態	（iniから取得）
$GLOBALS['topic_status'] = $ini['TASK_STATUS'];

//？？
$GLOBALS['topic_status_bg'] = array(
				            "完了"    =>" done",
				            );

//期限でソート
$GLOBALS['topic_due_datetime'] = array(
		"最新順"  =>"new_date",
		"古い順"  =>"old_date",
);


//？？
$GLOBALS['topic_status_css'] = "
            .done td,
            .done td a{ color:#BBB; text-decoration: line-through;}
";



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
define('DB_HOST'   , $ini['DB_HOST']);
define('DB_USER'   , $ini['DB_USER']);
define('DB_PASS'   , $ini['DB_PASS']);
define('DB_NAME'   , $ini['DB_NAME']);




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
