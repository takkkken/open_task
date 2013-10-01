<?php

/**
 * res_update.php
 *
 * @copyright  2012 Y.Suzuki
 * @license    BSD
 */

require_once './webapp/CB.php';

$db = new CB_DB;

if(!$_POST["res_title"] OR !$_POST["res_contents"]) {
	if(!$_GET["topic_id"] OR !$_GET["res_id"]){
		httpRedirect("./");
	}else{
		$data= $db->GetRow("SELECT * FROM res WHERE is_deleted=0 AND topic_id = {$_GET["topic_id"]} AND res_id = {$_GET["res_id"]}");
		$data_topic= $db->GetRow("SELECT topic_to FROM topic WHERE topic_id = {$_GET["topic_id"]}");
	}
}else{
	$data_update["res_title"] = $_POST["res_title"];
	$data_update["res_contents"] = $_POST["res_contents"];
	$data_update["topic_status"] = $_POST["topic_status"];
	$data_update["modified_user"]   = $GLOBALS['user']['user_name'];
	$data_update["modified_time"] = date("Y-m-d H:i:s");
	
	if($_POST["authority"]==null or $_POST["authority"]==''){
		$data_update["authority"] = null;
	}else{
		$data_update["authority"] = $_POST["authority"];
		
	}
	$db->Update('res',$data_update,'topic_id='. $_POST["topic_id"]. " AND res_id=".$_POST["res_id"]);
	

	//宛先の担当ユーザ（Topic_to）を更新
	$topic_data_update["topic_to"] = $_POST["topic_to"];
	$db->Update("topic",$topic_data_update,'topic_id='. $_POST["topic_id"]);

	if ($_POST["latest"]){
		unset($data_update['res_title']);
		unset($data_update['res_contents']);
		unset($data_update['authority']);
		$db->Update('topic',$data_update,'topic_id='. $_POST["topic_id"]);
		//sendMail($users,"topic_input",$data+$_POST);
	}
	httpRedirect("topic_detail.php?topic_id=". $_POST["topic_id"]);
}

include_once 'skin/inc/header.inc';

?>

<div class="tdSubTitle">
	<div>
		<b>コメントの編集</b>
	</div>
</div>

<div id="comment_form">

<form action="<?=$_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data" name="form" id="form" onSubmit="return check_update();">
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
					if($key!=$data['topic_status']){
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
	<td class="tdFormCaption"><span class="requiredSign">*</span>  タイトル:</td>
	<td>
		<input name="res_title" type="text" value="<?=$data["res_title"]?>" id="title" style="width:300px;" />
	</td>
</tr>

<tr class="trForm" valign="top">
	<td valign="top" class="tdFormCaption">
		<span class="requiredSign">*</span>  コメント:
		<br /><span class="formComment nowrap"></span>
	</td>
	<td valign="top">
		<p style="color:red;">URLなど状況がわかるものをできる限り記入をお願いします</p>
		<textarea cols="60" rows="4" name="res_contents" id="comment" style="width:100%;height:150px;"><?=$data["res_contents"]?></textarea>
	</td>
</tr>

<tr class="trForm">
	<td class="tdFormCaption">
	補足事項など:</td>
	<td>
		<input name="authority" type="text" id="authority" style="width:300px;" value="<?=$data["authority"]?>"/>
	</td>
</tr>


<tr>
	<td></td>
	<td><div><br /></div>
		<input type="submit" name="_submit" id="submit" value="更新" class="button" />
	</td>
</tr>
</table>
		<input type="hidden" name="topic_id" value="<?=$data["topic_id"]?>" />
		<input type="hidden" name="res_id" value="<?=$data["res_id"]?>" />
		<input type="hidden" name="latest" value="<?=$_GET["latest"]?>" />
		
</form>
<?php
include_once 'skin/inc/footer.inc';