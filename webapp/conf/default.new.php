<?php

///////////////////////////////
// サイト固有設定
///////////////////////////////

define('SITE_NAME' , '開発チーム２');
define('SITE_URL'  , 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . '/');

//FROMのメールアドレスを設定
define('FROM_MAIL' , 'cbts@'.$_SERVER['SERVER_NAME']);
define('PJ_NAME'   , 'タスク管理');

$GLOBALS['topic_type'] = array(
            "タスク"=>"task.gif",
//            "タスク(優先)"=>"task_hurryup.gif",
//            "バグ(優先)"  =>"bug_error.png",
            "バグ"  =>"bug.png",
            "保留"        =>"waiting.gif",
            "確認"        =>"check.png",
            "要望"        =>"request.gif",
            );

//2013/08/22　追加
$GLOBALS['topic_priority'] = array(
            "1"		=>"",
            "2"		=>"",
            "3"  	=>"",
            "4"		=>"",
            "5"  	=>"",
            );


//2013/08/09　追加
$GLOBALS['topic_project'] = array(
            "いいいいい"				=>"",
            "あああああ"				=>"",
            );
//ディレクトリ名をプロジェクトのマスタに。
foreach(glob("/home/samba/app/mktg_tool/*") as $dirname)
	$GLOBALS['topic_project'][basename($dirname)]="";



$GLOBALS['topic_status'] = array(
            //※[未対応]・[完了]の項目は変更禁止
            "未対応"  =>"red",
            "確認中"  =>"pink",
            "作業中"  =>"orange",
            "実装終了"=>"orange",
            "作業終了"=>"blue",
            "予定"    =>"green",
            "保留"    =>"green",
            "完了"    =>"#BBB",
            );

$GLOBALS['topic_status_bg'] = array(
            //※[未対応]・[完了]の項目は変更禁止
//            "未対応"  =>" hoge1",
//            "確認中"  =>" hoge1",
//            "作業中"    =>" hoge1",
//            "作業終了"=>" hoge1",
            "完了"    =>" done",
            );

//期限でソート
$GLOBALS['topic_due_datetime'] = array(
		"最新順"  =>"new_date",
		"古い順"  =>"old_date",
);


$GLOBALS['topic_status_css'] = "
            .done td,
            .done td a{ color:#BBB; text-decoration: line-through;}
";

$GLOBALS['auth_user'] = array(

	//SVNからのコミットメッセージを更新する場合、IDは、SVNのユーザ（PCのログインユーザ等）にすること。
            //"×ID(変更不可)" =>array("名前（変更不可）","メール（変更可能）","パスワード（変更可能）md5対応",管理者=1),
			"test1"	=>array("山田",		"yamada@hoge.co.jp",	"yamada"	,1),
            );


//$GLOBALS['topic_status_hidden'] = array("");

