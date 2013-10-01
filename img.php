<?php

/**
 * img.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */


require_once './webapp/CB.php';
require_once CLASS_DIR . '/CB_Thumbnail.php';

$db = new CB_DB;

if($_REQUEST["topic_id"]){
	$table   = "topic";
	$where[] = "topic_id = {$_REQUEST["topic_id"]}";

	//お客さんからはタスクが見れないように
	if(!$GLOBALS['user']['admin']) $where[] = "topic_type NOT LIKE 'タスク'";
}elseif($_REQUEST["res_id"]){
	$table   = "res";
	$where[] = "res_id = {$_REQUEST["res_id"]}";
}else{
	httpRedirect("./");
}

if($where) $whereSQL = implode(" AND ",$where);

$data  = $db->GetRow("SELECT * FROM {$table} WHERE is_deleted=0 AND  {$whereSQL}");
$file_name = DATA_DIR . '/' .md5($data["file_name"].$data["{$table}_id"]).getExt($data["file_name"]);
if(!file_exists($file_name)) httpRedirect("./");


foreach($GLOBALS['image_type'] AS $key=>$val){
	if(preg_match("/\.{$key}$/is",$data["file_name"])){
		$mineType = $val[0];
	}
}

header("Content-type: {$mineType}");

$_GET["w"] = ($_GET["w"]) ? intval($_GET["w"]) : 0;
$_GET["h"] = ($_GET["h"]) ? intval($_GET["h"]) : 0;
$_GET["p"] = ($_GET["p"]) ? intval($_GET["p"]) : 0;
$_GET["q"] = ($_GET["q"]) ? intval($_GET["q"]) : 100;

$_GET["c"] = ($_GET["c"]) ? intval($_GET["c"]) : 0;

$thumb = new CB_Thumbnail($file_name);
$thumb->resize($_GET['w'],$_GET['h']);
$thumb->resizePercent($_GET["p"]);

if($_GET["c"]){
	$thumb->cropFromCenter($_GET["c"]);
}

//		$thumb->crop($startX,$startY,$width,$height);

$thumb->show();		
exit();
