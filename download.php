<?php

/**
 * download.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

require_once './webapp/CB.php';
require_once 'HTTP/Download.php';

$db = new CB_DB;

if($_REQUEST["topic_id"]){
	$table   = "topic";
	$where[] = "topic_id = {$_REQUEST["topic_id"]}";

	//管理者以外は閲覧不可にする
	if(!$GLOBALS['user']['admin']) $where[] = "topic_type NOT LIKE 'タスク'";
}elseif($_REQUEST["res_id"]){
	$table   = "res";
	$where[] = "res_id = {$_REQUEST["res_id"]}";
}else{
	httpRedirect("./");
}

if($where) $whereSQL = implode(" AND ",$where);

$data  = $db->GetRow("SELECT * FROM {$table} WHERE is_deleted=0 AND  {$whereSQL}");
$file_name = DATA_DIR . '/' . md5($data["file_name"].$data["{$table}_id"]).getExt($data["file_name"]);

if(!file_exists($file_name)) httpRedirect("./");


foreach($GLOBALS['contents_type'] AS $key=>$val){
	if(preg_match("/\.{$key}$/is",$data["file_name"])){
		$mineType = $val[0];
	}
}
$params = array(
		  'file'                => $file_name,
		  'contenttype'         => $mineType,
);
$dl = new HTTP_Download($params);
$dl->setContentDisposition(HTTP_DOWNLOAD_ATTACHMENT, mb_convert_encoding($data["file_name"],"SJIS","UTF-8"));
$dl->send();
exit();