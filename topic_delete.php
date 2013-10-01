<?php

/**
 * index.php
 *
 * @copyright  2010 Y.Ohkouchi
 * @license    BSD
 */

require_once './webapp/CB.php';

$db = new CB_DB;

if (empty($_GET['topic_id']) || empty($_GET['act'])) {
	header("Location: ./");
}

if ($_GET['act'] === "detail") {
	if (!empty($_GET['res_id'])) {
		$data1 = $db->GetAll("SELECT * FROM res WHERE is_deleted=0 AND res_id=${_GET['res_id']}");

		foreach ($data1 as $val) {
			$file_name = DATA_DIR . '/' . md5($val["file_name"].$val["res_id"]).getExt($val["file_name"]);
			@unlink($file_name);
			break;
		}

		@$db->logicalDelete("res","res_id=${_GET['res_id']}");

		$data2 = $db->GetRow("SELECT * FROM res WHERE is_deleted=0 AND topic_id=${_GET['topic_id']} ORDER BY res_id DESC");


		if (!$data2['topic_status']) {
			$topic_status = "未対応";
		}else{
			$topic_status = $data2['topic_status'];
		}

		$data3 = $db->GetAll("SELECT * FROM topic WHERE is_deleted=0 AND topic_id=${_GET['topic_id']}");

		foreach ($data3 as $val) {
			$topic_res_count = $val['topic_res_count'] - 1;
			@$db->Update("topic",array('topic_res_count'=>"${topic_res_count}",'topic_status'=>"${topic_status}"),"topic_id=${_GET['topic_id']}");
			break;
		}

		$ret = "topic_detail.php?topic_id=${_GET['topic_id']}";
	} else {
		$data1 = $db->GetAll("SELECT * FROM res WHERE is_deleted=0 AND topic_id=${_GET['topic_id']}");

		foreach ($data1 as $val) {
			$file_name = DATA_DIR . '/' . md5($val["file_name"].$val["res_id"]).getExt($val["file_name"]);
			@unlink($file_name);
		}

		@$db->logicalDelete("res","topic_id=${_GET['topic_id']}");

		$ret = "./";

		$data2 = $db->GetAll("SELECT * FROM topic WHERE is_deleted=0 AND topic_id=${_GET['topic_id']}");

		foreach ($data2 as $val) {
			$file_name = DATA_DIR . '/' . md5($val["file_name"].$val["topic_id"]).getExt($val["file_name"]);
			@unlink($file_name);
		}

		@$db->logicalDelete("topic","topic_id=${_GET['topic_id']}");
	}
} else {
	$data1 = $db->GetAll("SELECT * FROM res WHERE is_deleted=0 AND topic_id=${_GET['topic_id']}");

	foreach ($data1 as $val) {
		$file_name = DATA_DIR . '/' . md5($val["file_name"].$val["res_id"]).getExt($val["file_name"]);
		@unlink($file_name);
	}

	@$db->logicalDelete("res","topic_id=${_GET['topic_id']}");

	$ret = "./";

	$data2 = $db->GetAll("SELECT * FROM topic WHERE is_deleted=0 AND topic_id=${_GET['topic_id']}");

	foreach ($data2 as $val) {
		$file_name = DATA_DIR . '/' . md5($val["file_name"].$val["topic_id"]).getExt($val["file_name"]);
		@unlink($file_name);
	}

	@$db->logicalDelete("topic","topic_id=${_GET['topic_id']}");
}

header("Location: ${ret}");

?>