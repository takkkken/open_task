<?php

/**
 * perf_update.php
 *
 * @copyright  2013 t.ishibashi
 * @license    BSD
 */

require_once './webapp/CB.php';
$db = new CB_DB;


if(!$_GET["topic_id"] && !$_GET["perf_id"]){
	httpRedirect("./");
}else{


	//ワーク開始時
	if(isset($_GET["topic_id"])){

		//perfデータ更新。現在実行中のWorkを強制的に終了時刻をセット
		$data_update["work_end"]=date("Y-m-d H:i:s");
		$db->Update("perf",$data_update,"user_name='". $GLOBALS['user']['user_name']."' and work_end is null ");

		//perfデータ作成。開始時刻をセット
		$data["topic_id"]=$_GET["topic_id"];
		$data["user_name"]=$GLOBALS['user']['user_name'];
		$data["work_start"]=date("Y-m-d H:i:s");

		$insertID = $db->Insert("perf",$data);
		$data["perf_id"] = $insertID;


	//ワーク終了時
	}elseif(isset($_GET["perf_id"])){

		$data= $db->GetRow("SELECT * FROM perf WHERE perf_id = {$_GET["perf_id"]}");

		if(isset($data["perf_id"])){

			//POPUPからPOST時
			if(isset($_POST["work_start"])&&isset($_POST["work_end"])){
				$data_update["work_start"]	=date("Y-m-d H:i:s",strtotime($_POST["work_start"]));
				$data_update["work_end"]	=date("Y-m-d H:i:s",strtotime($_POST["work_end"]));
				if($_POST["work_end"]=="")
					$data_update["work_end"]	=date("Y-m-d H:i:s");
			//それ以外（現状未使用）
			}else{
				$data_update["work_end"]=date("Y-m-d H:i:s");
			}
			//perfデータ更新。終了時刻をセット
			$db->Update("perf",$data_update,'perf_id='. $_GET["perf_id"]);

		}else{
			die("perfテーブルにデータが存在しません。エラー。");
		}

	}
}

httpRedirect("./");

?>