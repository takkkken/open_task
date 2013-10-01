<?php

/**
 * topic_input.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

require_once './webapp/CB.php';
require_once './js/liveDate.php';
$db = new CB_DB;

if($_POST["topic_title"] && $_POST["topic_contents"]){

	$data = $_POST;
//print_r($data);
//die;

	//再利用時のIDを除去
	unset($data["topic_id"]);

	$data["topic_to"] = $_POST["topic_to"];
	$data["topic_cc"] = @implode(",",$_POST["topic_to"]);	//2013/08/30関係者追加

	$data["file_name"] = $_FILES['file_name']['name'];
	$data["registered_time"] = date("Y-m-d H:i:s");
	$data["modified_time"]   = date("Y-m-d H:i:s");
	$data["user_name"]       = $GLOBALS['user']['user_name'];
	$data["modified_user"]   = $GLOBALS['user']['user_name'];
	if($data["date"]){
		$data["date"]["month"] = sprintf("%02d",$data["date"]["month"]);
		$data["date"]["date"]  = sprintf("%02d",$data["date"]["date"]);
		$data["topic_due_datetime"]   = "{$data["date"]["year"]}-{$data["date"]["month"]}-{$data["date"]["date"]} 00:00:00";
		unset($data["date"]);
	}

	$data["topic_status"] = "未対応";
	$insertID = $db->Insert("topic",$data);
	$data["topic_id"] = $insertID;

	if($data["topic_to"]){
        $data["subject"] = "[".PJ_NAME."] ({$data["topic_type"]}) {$data["topic_title"]}";
		sendMail($users,"topic_input",$data+$_POST);
	}

	//登録回数入力
	addModifiedUser($data["user_name"]);

	if($data["file_name"]){
		$saveFileName = DATA_DIR . '/' . md5($data["file_name"].$insertID).getExt($data["file_name"]);
		if(!move_uploaded_file($_FILES['file_name']['tmp_name'], $saveFileName)) {
		    die( "Can't upload ".VAR_DIR." must 777");
		}
	}
	httpRedirect("topic_list.php");

}

include_once 'skin/inc/header.inc';

?>

<div class="tdSubTitle">
	<div>
		<b>課題の登録</b>
	</div>
</div>

<div id="comment_form">

<form action="<?=$_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data" name="form" id="form" onSubmit="return check(<?=$GLOBALS['user']['admin']?>);">
<table border="0" width="100%" cellpadding="5" cellspacing="0">


<tr class="trForm">
	<td class="tdFormCaption">名前:</td>
	<td colspan=3><?=$GLOBALS['user']['user_name']?>&nbsp;&lt;<?=$GLOBALS['user']['user_mail']?>&gt;</td>
</tr>

<tr class="trForm">
	<td class="tdFormCaption"><span class="requiredSign">*</span>  タイトル:</td>
	<td colspan=3>
		<input name="topic_title" type="text" value="<?=$_POST["topic_title"]?>" id="title" style="width:300px;" />
	</td>
</tr>

<tr class="trForm">
        <td class="tdFormCaption"><span class="requiredSign">*</span>  期限:</td>
        <td colspan=3>
        <span id="HTML_AJAX_LOADING"></span>
        <label><input type="radio" name="_setDate" checked="checked" onclick="set_date('date','disabled');" />設定しない</label>
        <label><input type="radio" name="_setDate" onclick="set_date('date','');get_date('date');" />設定する</label>
<select name="date[year]" id="date[year]" onchange="get_date('date');" disabled="disabled">
        <?php
        for($i=date("Y");$i<date("Y")+10;$i++){
                $selected = "";
                if($i == date("Y")) $selected = " selected=\"selected\"";
                echo "<option value=\"{$i}\"{$selected}>{$i}年</option>";
        }
        ?>
</select>
<select name="date[month]" id="date[month]" onchange="get_date('date');" disabled="disabled">
        <?php
        for($i=1;$i<13;$i++){
                $selected = "";
                if($i == date("m")) $selected = " selected=\"selected\"";
                echo "<option value=\"{$i}\"{$selected}>{$i}月</option>";
        }
        ?>
</select>
<span id="date">
        <select name="date[date]" id="date[date]"  disabled="disabled">
        	<?php
       			 $str = '<option value="'.date("d").'"';
       			 if(date('w')==6){
       			 	$str .= ' style="background-color:#CCFFFF"';
       			 }else if(date('w')==0){
       			 	$str .= ' style="background-color:#FFCCCC"';
       			 }
       			 $str .= '>'.date("j").'日 ('.change_day(date("Y-m-d H:i:s")).')</option>';
       			 print $str;
			?>
        </select>
        </td>
</tr>

<tr class="trForm" valign="top">
	<td valign="top" class="tdFormCaption">
		<span class="requiredSign">*</span>  コメント:
		<br /><span class="formComment nowrap"></span>
	</td>
	<td valign="top" colspan=3>
		<!-- <p style="color:red;">URLなど状況がわかるものをできる限り記入をお願いします</p> -->
		<textarea cols="60" rows="4" name="topic_contents" id="comment" style="width:600px;height:150px;"><?=$_POST["topic_contents"]?></textarea>
	</td>
</tr>

<tr class="trForm">
	<td class="tdFormCaption">担当者:</td>
	<td><select class="" size="5" name="topic_to" id="topic_to" title="担当者">
<?php
		foreach($GLOBALS['topic_to_user'] AS $val){ 
			print '<option value="'.$val["user_name"].'">'.$val["user_name"].'</option>';
		}
		print'<option></option>';
?>
	</select>
	</td>
	<td class="tdFormCaption">関係者:
	</td>
	<td>
	<select class="" size="5" name="topic_cc[]" id="topic_cc" title="関係者" multiple>
		<option></option>
	</select>
	<input type="button" value="追加" onClick="add_select('_choice_user_id','topic_cc')" />
	<input type="button" value="削除" onClick="remove_select('topic_cc')" />
	<select name="_choice_user_id[]" id="_choice_user_id" size="5" multiple>
		<option value="<?=$GLOBALS['user']['user_name']?>"><?=$GLOBALS['user']['user_name']?></option>
		<?php if($GLOBALS['user']['user_name'] != $topic["modified_user"]){ ?>
			<?php foreach($GLOBALS['topic_to_user'] AS $val){ ?>
				<option value="<?=$val["user_name"]?>"><?=$val["user_name"]?></option>
			<?php } ?>
			<?php foreach($GLOBALS['topic_to'] AS $key=>$val){ ?>
				<option value="<?=$key?>"><?=$key?></option>
			<?php } ?>
		<?php }else{ ?>
			<option value="<?=$GLOBALS['user']['user_name']?>"><?=$GLOBALS['user']['user_name']?></option>
			<?php foreach($GLOBALS['topic_to_user'] AS $val){ ?>
				<option value="<?=$val["user_name"]?>"><?=$val["user_name"]?></option>
			<?php } ?>
			<?php foreach($GLOBALS['topic_to'] AS $key=>$val){ ?>
				<option value="<?=$key?>"><?=$key?></option>
			<?php } ?>
		<?php } ?>
		<option value=""></option>
	</select>
	</td>
</tr>

<tr class="trForm">
	<td class="tdFormCaption">種別:
	</td>
	<td colspan=3>
		<select name="topic_type">
		<option value="">▼種別</option>
<?
			$topic_type_default="タスク";
			foreach($GLOBALS['topic_type'] AS $key=>$val){
?>
				<option value="<?=$key?>" <?= ($topic_type_default==$key?"selected":"") ?>><?=$key?></option>
<?
			}
?>
	</td>
</tr>

<? // webapp\conf\opentask.phpにある場合のみ表示 ?>
<? if(count($GLOBALS['topic_project'])>0){ ?>
	<tr class="trForm">
		<td class="tdFormCaption">プロジェクト:
		</td>
		<td colspan=3>
			<select name="topic_project">
			<option value="">▼プロジェクト</option>
			<?php foreach($GLOBALS['topic_project'] AS $key=>$val){ ?>
			<option value="<?=$key?>"><?=$key?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
<? } ?>
<? // webapp\conf\opentask.phpにある場合のみ表示 ?>
<? if(count($GLOBALS['topic_priority'])>0){ ?>
	<tr class="trForm">
		<td class="tdFormCaption">優先度:
		</td>
		<td colspan=3>
			<select name="topic_priority">
			<option value="">▼優先度(1:低 5:高)</option>
<?
			$topic_priority_default="3";
			foreach($GLOBALS['topic_priority'] AS $key=>$val){
?>
				<option value="<?=$key?>" <?= ($topic_priority_default==$key?"selected":"") ?>><?=$key?></option>
<?
			}
?>
			</select>
		</td>
	</tr>
<? } ?>
	<tr class="trForm">
		<td class="tdFormCaption">見積(時間):</td>
		<td colspan=3>
			<input name="topic_cost" type="text" value="<?=$_POST["topic_cost"]?>" id="title" style="width:100px;" />
		</td>
	</tr>
<tr class="trForm">
	<td class="tdFormCaption">ファイル:
	</td>
	<td colspan=3>
		<span id="fileopen"><a href="JavaScript:showswich('fileopen','fileinput');" class="open">ファイル追加</a></span>
		<div id="fileinput" style="display:none;"><input type="file" name="file_name" /></div>
	</td>
</tr>

<?php if($GLOBALS['user']['admin'] && !$GLOBALS['not_need_client_system']){ ?>
<tr class="trForm">
	<td class="tdFormCaption">公開範囲:
	</td>
	<td colspan=3>
	<p style="color:red;">公開しない = 自分のみに表示（未実装）</p>
	<select name="is_admin" id="is_admin">
	        <option value="0">全員に公開</option>
	        <option value="1">管理者のみに公開</option>
	        <option value="2">公開しない</option>
	</select>
	</td>
</tr>
<?php } ?>

<tr>
	<td></td>
	<td colspan=3><div><br /></div>
		<input type="submit" name="_submit" id="submit" value="登録" class="button" />
	</td>
</tr>
</table>
</form>
<?php
include_once 'skin/inc/footer.inc';
