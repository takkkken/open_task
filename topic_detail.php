<?php

/**
 * topic_detail.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */


require_once './webapp/CB.php';

$db = new CB_DB;


//ワード出力時
if( $_GET["mode"]=="word" )
	$word=true;


$where[] = "topic_id = {$_REQUEST["topic_id"]}";

//お客さんからは見れないようにする
if(!$GLOBALS['user']['admin']) $where[] = $where[] = "is_admin = 0";
if($where) $whereSQL = implode(" AND ",$where);

$topic  = $db->GetRow("SELECT * FROM topic WHERE is_deleted=0 AND {$whereSQL}");

if(!$topic) httpRedirect("./");

$data = $db->GetAll("SELECT * FROM res WHERE is_deleted=0 AND topic_id = {$_REQUEST["topic_id"]}");
$data_topic= $db->GetRow("SELECT topic_to FROM topic WHERE topic_id = {$_GET["topic_id"]}");
//print_r($data);

$res = $data[count($data)-1]["res_contents"];
if(!$res) $res = $topic["topic_contents"];
$res =  "&gt;" . preg_replace("/\n/is","\n&gt;",$res);


if($_POST){

	if(!$_POST["res_contents"]) httpRedirect("topic_detail.php?topic_id={$_POST["topic_id"]}");
	if(!$_POST["topic_status"]) $_POST["topic_status"] = $topic["topic_status"];

	$data = $_POST;

	$data["file_name"] = $_FILES['file_name']['name'];
	$data["registered_time"] = date("Y-m-d H:i:s");
	$data["user_name"]       = $GLOBALS['user']['user_name'];
	unset($data["topic_to"]);

	$insertID = $db->Insert("res",$data);

	//登録回数入力
	addModifiedUser($data["user_name"]);


	if($data["file_name"]){
		$saveFileName = DATA_DIR . '/' . md5($data["file_name"].$insertID).getExt($data["file_name"]);
		if(!move_uploaded_file($_FILES['file_name']['tmp_name'], $saveFileName)) {
		    die( "Can't upload ".VAR_DIR." must 777");
		}
	}

	unset($data);

	$data["topic_id"]      = $_POST['topic_id'];
	$data["topic_status"]  = $_POST['topic_status'];
	$data["topic_res_count"] = $topic['topic_res_count']+1;
	$data["modified_time"] = date("Y-m-d H:i:s");
	$data["modified_user"]   = $GLOBALS['user']['user_name'];
	$data["topic_to"] = $_POST["topic_to"];
	
	$db->Update("topic",$data,"topic_id = {$data["topic_id"]}");
	
	$topic  = $db->GetRow("SELECT * FROM topic WHERE is_deleted=0 AND topic_id=".$data["topic_id"]);
	$data["topic_title"] = $topic['topic_title'];
	
	if($data["topic_to"]){
		$data["subject"] = "[".PJ_NAME."] {$data["topic_title"]}";
		sendMail($users,'res_input',$data+$_POST);
	}
	httpRedirect("topic_detail.php?topic_id=". $_POST["topic_id"]);
	
}

if (!$word){
	include_once 'skin/inc/header.inc';
}else{
	header("Content-Type: application/vnd.ms-word");
	header("Content-Disposition: attachment; filename=output.doc");

	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<STYLE type="text/css">
<!--';
	include_once 'skin/word.css';
	echo '
-->
</STYLE>
</head>
<body>';

}

?>

<table border="0" width="100%" cellpadding="5" cellspacing="1">
<tr class="tdTitle">
	<td colspan="2" class="tdTitle"><h1 class="articleTitle"><?=viewContents($topic["topic_title"])?>　

<?  if (!$word){ ?>
		<a href="topic_update.php?topic_id=<?=$topic["topic_id"]?>">編集</a>　
		<a href="topic_delete.php?act=detail&topic_id=<?=$topic["topic_id"]?>" onClick="res=confirm('削除します。');if(res==false){return false;}">削除</a>　　　
		<a href="topic_update.php?topic_id=<?=$topic["topic_id"]?>&mode=recycle"><img src="./skin/images/icon/recycle.gif" alt="再利用" width="16\">再利用</a><?  } ?>

</h1></td>
</tr>
<tr>
	<td valign="top" style="padding: 10px 5px;">
		
		<div style="padding: 5px 0px 5px 25px; float: right;">
			<table border="0" cellpadding="0" cellspacing="0" width="165" class="abBorder">
<tr>
	<td>
		<table border="0" cellpadding="7" cellspacing="1" width="100%">
		<tr>
			<td class="abBgr">
				<div class="space">状態:<?=$topic["topic_status"]?></div>
				<div class="space">期限:<?=changeDate($topic["topic_due_datetime"])?></div>
				<div class="space">担当者:<?=$topic["topic_to"]?></div>
				<div class="space">関係者:<?=$topic["topic_cc"]?></div>
				<div class="space">種別:<?=$topic["topic_type"]?></div>
				<div class="space">優先度:<?=$topic["topic_priority"]?></div>
				<div class="space">見積:<?=$topic["topic_cost"]?></div>
				<div class="space">プロジェクト:<?=$topic["topic_project"]?></div>
				<div class="space">更新:<?=leftTime($topic["modified_time"])?></div>
			</td>
		</tr>		
		<tr>
			<td class="abBgrDarker">
				<div class="less_space">コメント数: <?=$topic["topic_res_count"]?></div>
			</td>
		</tr>
 		<tr>
			<td class="abBgrDarker">
				<div class="space">登録者:<?=$topic["user_name"]?></div>
				<div class="space">登録日:<?=$topic["registered_time"]?></div>
				<div class="space">更新者:<?=$topic["modified_user"]?></div>
				<div class="space">更新日:<?=$topic["modified_time"]?></div>
				<div class="space">ID:<?=$topic["topic_id"]?></div>
				</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</div>
<div class="textBlock" style="padding-bottom: 10px;">
	<?=downloadString($topic)?><br />
	<?=showImage($topic);?>
	<?=viewContents($topic["topic_contents"])?>
</div>
	</td>
</tr>
</table>


<div class="tdSubTitle">
	<div id="comment_nav" class="pageByPage fright">全&nbsp;<?=$topic["topic_res_count"]?>件&nbsp;表示&nbsp;&nbsp;</div>
	<div id="comment_title">
		<b>コメント</b>
	</div>
</div>
<br />

<table border="0" width="100%" cellpadding="3" cellspacing="1">


<?php $count=count($data); $loop=0; foreach($data as $val){ $loop++; ?>
<tr valign="top">
	<td style="padding: 0px 5px;">
		<b><?=viewContents($val["res_title"])?>
<?  if (!$word){ ?>
　<a href="res_update.php?topic_id=<?=$topic["topic_id"]?>&res_id=<?=$val["res_id"]?><? if($count==$loop) print'&latest=true'?>">編集</a>　<a href="topic_delete.php?act=detail&topic_id=<?=$topic["topic_id"]?>&res_id=<?=$val["res_id"]?>" onClick="res=confirm('削除します。');if(res==false){return false;}">削除</a>
<?  } ?>
</b>
		<hr size="1" noshade="noshade" />
		<div class="textBlock">
		[<b style="color:<?=$GLOBALS['topic_status'][$val["topic_status"]]?>"><?=$val["topic_status"]?></b>]<br />
		登録者:<?=$val["user_name"]?>&nbsp;
		登録日:<?=$val["registered_time"]?>&nbsp;
		ID:<?=$val["res_id"]?>
		<?php
			if ($val["modified_user"]!=null){
				print '<br />';
				print '更新者:'.$val["modified_user"].'&nbsp;&nbsp;';
				print '更新日:'.$val["modified_time"].'&nbsp;';
			}
		?>
		<?=downloadString($val);?></div><br />
		    <?=showImage($val);?>
		<br />
		<?=viewContents($val["res_contents"])?>
		<?php if(viewContents($val["authority"])!=null){
			print '
		<br />
		<br />
		<br />';

		//パッチファイルの表示用リンク表示
		if( preg_match("/^rev:([0-9]+)@(.+)$/",viewContents($val["authority"])) )
			$patch_dl = true;

		if( $patch_dl )
			print("<a target=_blank href=disp_patch.php?q=".$val["authority"].">");

		print('<font color=blue>');
		print viewContents($val["authority"]).'</font>';

		if( $patch_dl )
			print("</a>");


}?>
		<br />
	</td>
</tr>
<tr>
	<td style="padding: 0px 5px; float: right;">
</div><br /><br />
	</td>
</tr>
<?php } ?>

</table>

<?
//これ以降はWord出力以外時。（通常時）
if (!$word){
?>

<div class="tdSubTitle">
	<div>
		<b>コメントの登録</b>
	</div>
</div>

<div id="comment_form">

<form action="<?=$_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data" name="form" id="form" onSubmit="return check();">
	<input name="topic_id" type="hidden" value="<?=$_GET["topic_id"]?>" />

<table border="0" width="100%" cellpadding="5" cellspacing="0">


<tr class="trForm">
	<td class="tdFormCaption">名前:</td>
	<td><?=$GLOBALS['user']['user_name']?>&nbsp;&lt;<?=$GLOBALS['user']['user_mail']?>&gt;</td>
</tr>

<td class="tdFormCaption">状態:</td>
	<td>
		<select name="topic_status">
			<?php
				foreach($GLOBALS['topic_status'] AS $key=>$val){ 
					if($key!=$topic['topic_status']){
						print '<option value="'.$key.'">'.$key.'</option>';
					}else{
						print '<option selected value="'.$key.'">'.$key.'</option>';
					}
				}
				?>
		</select>	　　　
		宛先担当:
		<select name="topic_to">
			<?php
				foreach($GLOBALS['auth_user'] AS $key=>$val){ 
					if($val[0]!=$data_topic['topic_to']){
						print '<option value="'.$val[0].'">'.$val[0].'</option>';
					}else{
						print '<option selected value="'.$val[0].'">'.$val[0].'</option>';
					}
				}
				?>
        </select>
	</td>
</tr>

<tr class="trForm">
	<td class="tdFormCaption">
	<span class="requiredSign">*</span>  タイトル:</td>
	<td>
		<input name="res_title" type="text" value="Re:[<?=$topic["topic_res_count"]+1?>]&nbsp;<?=$topic["topic_title"]?>" id="title" style="width:300px;" />
		<span id="fileopen"><a href="JavaScript:showswich('fileopen','fileinput');" class="open">ファイルを追加</a></span>
		<div id="fileinput" style="display:none;"><input type="file" name="file_name" /></div>
	</td>
</tr>

<tr class="trForm" valign="top">
	<td valign="top" class="tdFormCaption">
		<span class="requiredSign">*</span>  コメント:
		<br /><span class="formComment nowrap"></span>
	</td>
	<td valign="top">
		<textarea cols="60" rows="4" name="res_contents" id="comment" style="width:100%;height:250px;"></textarea>
	</td>
</tr>

<tr class="trForm">
	<td class="tdFormCaption">
	補足事項など:</td>
	<td>
		<input name="authority" type="text" id="authority" style="width:300px;" />
	</td>
</tr>


<tr>
	<td></td>
	<td><div><br /></div>
		<input type="submit" name="_submit" id="submit" value="登録" class="button" />
	</td>
</tr>
</table>
</form>

<p>
<a href=topic_detail.php?mode=word&topic_id=<?=$_REQUEST["topic_id"]?>>Word出力</a>　　
</p>

<?php
include_once 'skin/inc/footer.inc';

}

