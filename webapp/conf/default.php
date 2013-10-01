<?php

///////////////////////////////
// サイト固有設定
///////////////////////////////

define('SITE_NAME' , 'オープンタスク');
define('SITE_URL'  , 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . '/');

//FROMのメールアドレスを設定
define('FROM_MAIL' , 'cbts@'.$_SERVER['SERVER_NAME']);
define('PJ_NAME'   , 'オープンタスク機能要望');

$GLOBALS['topic_type'] = array(
            "タスク(通常)"=>"task.gif",
            "タスク(優先)"=>"task.gif",
            "バグ(優先)"  =>"bug_error.png",
            "バグ(通常)"  =>"bug.png",
            "保留"        =>"waiting.gif",
            "確認"        =>"check.png",
            "要望"        =>"request.gif",
            );


$GLOBALS['topic_status'] = array(
            //※[未対応]・[完了]の項目は変更禁止
            "未対応"  =>"red",
            "確認中"  =>"pink",
            "作業中"  =>"orange",
            "作業終了"=>"blue",
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

$GLOBALS['topic_to'] = array(
            "システム担当者"  =>"red",
            "デザイン担当者"  =>"green",
            "ディレクター"    =>"orange",
            "その他"          =>"orange",
            );

$GLOBALS['topic_status_css'] = "
            .done td,
            .done td a{ color:#BBB; text-decoration: line-through;}
";
/*
$GLOBALS['auth_user'] = array(
            //"×ID変更不可" =>array("×名前変更不可","○名前変更可能","○パスワード変更可能",),

//            "guest" =>array("ゲスト","example@example.com","guest",),
              "admin" =>array("管理者","example@example.com","admin",1),
            );
*/
$GLOBALS['auth_user'] = array(
            //"×ID(変更不可)" =>array("名前（変更不可）","メール（変更可能）","パスワード（変更可能）md5対応",),
			"guest"=>array("ゲストユーザ","demo@example.com","guest",0),
			"admin"=>array("でもユーザ","admin@example.com","admin",1),


            );