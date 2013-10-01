<?php

	//カレントを自スクリプトのパスに変更！
	chdir(dirname(__FILE__));

	// インクルード
	require_once('../webapp/CB_conf.php');
	require_once '../webapp/CB_mime_type.php';
	require_once  WEBAPP_DIR   . '/conf/'.DB_NAME.'.php';
	require_once  LIB_DIR   . '/gladius/gladius.php';
	require_once  LIB_DIR   . '/adodb_lite/adodb.inc.php';
	require_once  CLASS_DIR . '/CB_Functions.php';
	require_once  CLASS_DIR . '/CB_DB.php';


	//日本語を使用する！
	mb_language("ja");
	ini_set('mbstring.internal_encoding', 'UTF-8');
	ini_set('mbstring.http_input', 'auto');
	ini_set('mbstring.detect_order', 'auto');

	//タイムゾーンを東京にする！
	date_default_timezone_set('Asia/Tokyo');

	//引数取得
	$repository = $_SERVER['argv'][1];
	$revision = $_SERVER['argv'][2];

	//apache user なので 環境変数LANGを与えた後に実行しないと、svnlookの結果が文字バケる。
	$commit_message = `LANG=ja_JP.UTF-8 /usr/bin/svnlook log $repository -r $revision`;

	//コミットユーザ取得
	$commit_user    	= `svnlook author $repository -r $revision`;
	$commit_user    	= preg_replace("/\r|\n/","",$commit_user);
	$commit_userName	= $GLOBALS['auth_user'][$commit_user][0];
//print_r($GLOBALS['auth_user']);
//die($commit_userName);

	//コミットメッセージにid:xxxがある場合、OpenTaskにres挿入、topic更新
	if(preg_match("/^(id|ID|Id):([0-9]+)/",$commit_message,$matche)){

		$tid = $matche[2];

		//res　DB登録
		$data["topic_id"] 			= $tid;
		$data["topic_status"] 		= '実装終了';
		$data["user_name"]       	= $commit_userName;	//webapp/conf/{DB_NAME}.phpの設定内容から名前解決
		$data["res_title"] 			= 'SVNコミット';
		$data["res_contents"] 		= "Commit:".preg_replace("/^(id|ID|Id):([0-9]+)/","",$commit_message);
		$data["authority"] 			= "rev:$revision@$repository";
		$data["registered_time"] 	= date("Y-m-d H:i:s");

		//DB接続
		$db = new CB_DB;

		//resへのINSERT
		$insertID = $db->Insert("res",$data);

//		insert into res (topic_id,topic_status,user_name,res_title,res_contents,authority,registered_time)
//		values(9,'作業終了','石橋','SVNコミット','コミットメッセージ','rev:123',now())


		//topicの更新 (ステータスを作業終了に)
		$getRow = $db->GetRow("select count(*) as resCnt from res where topic_id=$tid");
		$resCnt = $getRow["resCnt"];

		$data_update["topic_res_count"]	= $resCnt;
		$data_update["topic_status"]	= '実装終了';
		$data_update["modified_user"]	= $commit_userName;
		$data_update["modified_time"]	= date("Y-m-d H:i:s");
		$db->Update('topic',$data_update,'topic_id='.$tid);

//		UPDATE
//		    topic
//		SET
//		    topic_res_count=topic_res_count+1 , topic_status='作業終了' , modified_user='石橋' , modified_time=now() 
//		WHERE
//		    topic_id=6

	}
?>