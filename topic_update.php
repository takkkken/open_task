<?php

/**
 * topic_update.php
 *
 * @copyright  2012 Y.Suzuki
 * @license    BSD
 */

//再利用登録時
if($_GET["mode"]=="recycle"){
	$mode	="recycle";
	$title	="タスクの再利用";
	$btnVal	="再利用登録";
	$action ="topic_input.php";
}else{
	$mode	="update";
	$title	="タスクの編集";
	$btnVal	="更新";
	$action =$_SERVER["PHP_SELF"];
}

$keyMster["topic_title"]="タイトル";
$keyMster["topic_due_datetime"]="期限";
$keyMster["topic_contents"]="コメント";
$keyMster["topic_to"]="担当者";
$keyMster["topic_cc"]="関係者";
$keyMster["topic_type"]="種別";
$keyMster["topic_project"]="プロジェクト";
$keyMster["topic_priority"]="優先度";
$keyMster["topic_cost"]="見積";
$keyMster["is_admin"]="公開設定";


require_once './webapp/CB.php';
require_once './js/liveDate.php';
require_once './webapp/lib/public_holiday.php';
$db = new CB_DB;

if(!$_POST["topic_title"] && !$_POST["topic_contents"]){
	if(!$_GET["topic_id"]){
		httpRedirect("./");
	}else{
		$data= $db->GetRow("SELECT * FROM topic WHERE is_deleted=0 AND topic_id = {$_GET["topic_id"]}");
		if($data["topic_due_datetime"]!=null){
			$topic_due_datetime_year  = mb_substr($data["topic_due_datetime"],0,4,utf8);
			$topic_due_datetime_month = ereg_replace("^0+", "", mb_substr($data["topic_due_datetime"],5,2,utf8));
			$topic_due_datetime_date   = ereg_replace("^0+", "", mb_substr($data["topic_due_datetime"],8,2,utf8));
			$topic_due_datetime_day   = change_day($data["topic_due_datetime"]);
		}else{
			unset($data["topic_due_datetime"]);
		}
	}
}else{
	$data = $_POST;
	unset($data["_setDate"]);
	unset($data["_submit"]);
	unset($data["_choice_user_id"]);

	$data["topic_to"] = $_POST["topic_to"];	
	$data["topic_cc"] = @implode(",",$_POST["topic_cc"]);	//2013/08/30関係者追加
	
	if($data["date"]){
		$data["date"]["month"] = sprintf("%02d",$data["date"]["month"]);
		$data["date"]["date"]  = sprintf("%02d",$data["date"]["date"]);
		$data["topic_due_datetime"]   = "{$data["date"]["year"]}-{$data["date"]["month"]}-{$data["date"]["date"]} 00:00:00";
		unset($data["date"]);
	}else{
		$data["topic_due_datetime"] = null;
	}
	if(! $data["is_admin"]){
		$data["is_admin"] = 1;
	}

	//Topic編集時
	if($mode=="update"){
		//更新前データ保持
		$data_before= $db->GetRow("SELECT * FROM topic WHERE is_deleted=0 AND topic_id = {$_POST["topic_id"]}");
		//resカウントを１Up
		$data["topic_res_count"]	= $data_before["topic_res_count"]+1 ;
	}

	$db->Update("topic",$data,'topic_id='. $_POST["topic_id"]);

	//Topic編集時
	if($mode=="update"){

		//更新後データ取得し、差分チェック
		$data_after= $db->GetRow("SELECT * FROM topic WHERE is_deleted=0 AND topic_id = {$_POST["topic_id"]}");
		$diffAry = array_diff_assoc($data_before, $data_after);

		//更新Keyを名称に変換しコメント本文作成
		foreach( $diffAry as $_key=>$_tmp)
			$diffKeys .= $keyMster[$_key]." ";
		$diffKeys .= "が編集されました。";

		//res　DB登録
		$data_res["topic_id"] 			= $_POST["topic_id"];
		$data_res["topic_status"] 		= $data_after["topic_status"];
		$data_res["user_name"]       	= $GLOBALS['user']['user_name'];
		$data_res["res_title"] 			= "タスクの編集";
		$data_res["res_contents"] 		= $diffKeys;
		$data_res["authority"] 			= "";
		$data_res["registered_time"] 	= date("Y-m-d H:i:s");

		//res(コメント)へのINSERT
		$insertID = $db->Insert("res",$data_res);
	}


	if($data["topic_to"]){
		$data["subject"] = "[".PJ_NAME."] ({$data["topic_type"]}){$data["topic_title"]}";
		sendMail($users,"topic_update",$data+$_POST);
	}
	
	httpRedirect("topic_detail.php?topic_id=". $_POST["topic_id"]);

}

include_once 'skin/inc/header.inc';

?>

<div class="tdSubTitle">
	<div>
		<b><?= $title ?></b>
	</div>
</div>

<div id="comment_form">

	<form action="<?=$action?>" method="post"
		enctype="multipart/form-data" name="form" id="form"
		onSubmit="return check_update(<?=$GLOBALS['user']['admin']?>);">
		<table border="0" width="100%" cellpadding="5" cellspacing="0">


			<tr class="trForm">
				<td class="tdFormCaption">名前:</td>
				<td colspan=3><?=$GLOBALS['user']['user_name']?>&nbsp;&lt;<?=$GLOBALS['user']['user_mail']?>&gt;</td>
			</tr>

			<tr class="trForm">
				<td class="tdFormCaption">プロジェクト:</td>
				<td colspan=3><select name="topic_project">
						<option value="">▼プロジェクト</option>
									<?php
				foreach($GLOBALS['topic_project'] AS $key=>$val){ 
					if($key!=$data['topic_project']){
						print '<option value="'.$key.'">'.$key.'</option>';
					}else{
						print '<option selected value="'.$key.'">'.$key.'</option>';
					}
				}
				?>
				</select>
				</td>
			</tr>


			<tr class="trForm">
				<td class="tdFormCaption"><span class="requiredSign">*</span> タイトル:</td>
				<td colspan=3><input name="topic_title" type="text"
					value="<?=$data["topic_title"]?>" id="title" style="width: 300px;" />
				</td>
			</tr>

			
				<tr class="trForm">
					<td class="tdFormCaption"><span class="requiredSign">*</span> 期限:</td>
					<td colspan=3><span id="HTML_AJAX_LOADING"></span> 
        			<?php
						if(!$data["topic_due_datetime"]){
					        print '<label><input type="radio" name="_setDate" checked="checked" onclick="set_date(\'date\',\'disabled\');" />設定しない</label>';
        					print '<label><input type="radio" name="_setDate" onclick="set_date(\'date\',\'\');get_date(\'date\');" />設定する</label>';
						}else{
					        print '<label><input type="radio" name="_setDate" onclick="set_date(\'date\',\'disabled\');" />設定しない</label>';
        					print '<label><input type="radio" name="_setDate" checked="checked" onclick="set_date(\'date\',\'\');get_date(\'date\');" />設定する</label>';
						}
					?>
        			<?php
						if(!$data["topic_due_datetime"]){
							print '<select name="date[year]" id="date[year]" onchange="get_date(\'date\');" disabled="disabled">';
						}else{
							print '<select name="date[year]" id="date[year]" onchange="get_date(\'date\');">';
						}
					?>
			        <?php
			        for($i=date("Y");$i<date("Y")+10;$i++){
			                $selected = "";
			                if($i == $topic_due_datetime_year){
			                	$selected = " selected=\"selected\"";
			                	$j = $topic_due_datetime_year;
			                	echo "<option value=\"{$j}\"{$selected}>{$j}年</option>";
			        		}else{
			                	echo "<option value=\"{$i}\">{$i}年</option>";
			        		}
			        }
			        ?>
			        </select>
        			<?php
						if(!$data["topic_due_datetime"]){
							print '<select name="date[month]" id="date[month]" onchange="get_date(\'date\');" disabled="disabled">';
						}else{
							print '<select name="date[month]" id="date[month]" onchange="get_date(\'date\');">';
						}
					?>
			        <?php
			        for($i=1;$i<13;$i++){
			                $selected = "";
			                if($data["topic_due_datetime"]){
				                if($i == $topic_due_datetime_month){
				                	$selected = " selected=\"selected\"";
				                }
			                }else{
				                if($i == date("m")){
					                $selected = " selected=\"selected\"";
				                }
			        		}
					        echo "<option value=\"{$i}\"{$selected}>{$i}月</option>";
			         }
			        ?>
					</select>
					<span id="date">
        			<?php
						if(!$data["topic_due_datetime"]){
							/*
							 * print '<option value="'.date("d").'">'.date("j").'日 ('.change_day(date("Y-m-d H:i:s")).')</option>';
							 */
							print '<select name="date[date]" id="date[date]" disabled="disabled">';
							
               				$str = '<option value="'.date("d").'"';
							if(date('w')==6){
								$str .= ' style="background-color:#CCFFFF"';
							}else if(date('w')==0){
								$str .= ' style="background-color:#FFCCCC"';
								
							}
							$str .= '>'.date("j").'日 ('.change_day(date("Y-m-d H:i:s")).')</option>';
							print $str;
						}else{
							print '<select name="date[date]" id="date[date]">';
               				print '<option value="'.$topic_due_datetime_date.'">'.$topic_due_datetime_date.'日 ('.$topic_due_datetime_day.')</option>';
						}
					?>
        </select>
        </td>
				</tr>
			
			<tr class="trForm" valign="top">
				<td valign="top" class="tdFormCaption"><span class="requiredSign">*</span>
					コメント: <br /> <span class="formComment nowrap"></span>
				</td>
				<td valign="top" colspan=3>
					<p style="color:red;">URLなど状況がわかるものをできる限り記入をお願いします</p>
					<textarea cols="60" rows="4" name="topic_contents" id="comment" style="width: 600px; height: 150px;"><?=$data["topic_contents"]?></textarea>
				</td>
			</tr>


  			<tr class="trForm">
				<td class="tdFormCaption">担当者:</td>
				<td><select class="" size="5" name="topic_to" id="topic_to" title="担当者">
<?php

						foreach($GLOBALS['topic_to_user'] AS $val){ 
							if($data["topic_to"]==$val["user_name"]){
									print '<option value="'.$val["user_name"].'" selected>'.$val["user_name"].'</option>';
							}else{
									print '<option value="'.$val["user_name"].'">'.$val["user_name"].'</option>';
							}
						}
					print'<option></option>';
?>
				</select>
				</td>
				<td class="tdFormCaption">関係者:</td>
				<td><select class="" size="5" name="topic_cc[]" id="topic_cc" title="関係者" multiple>
				<?php
					if($data["topic_cc"]){
						$topic_cc_users=explode(',',$data["topic_cc"]);
						foreach($topic_cc_users AS $val){ 
							print '<option value="'.$val.'" selected>'.$val.'</option>';
						}
					}
					print '<option></option>';
						?>
				</select>
				<input type="button" value="追加" onClick="add_select('_choice_user_id','topic_cc')" />
				<input type="button" value="削除" onClick="remove_select('topic_cc')" />
				<select name="_choice_user_id[]" id="_choice_user_id" size="5" multiple>
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
				<td class="tdFormCaption">種別:</td>
				<td colspan=3><select name="topic_type">
						<option value="">▼種別</option>
									<?php
				foreach($GLOBALS['topic_type'] AS $key=>$val){ 
					if($key!=$data['topic_type']){
						print '<option value="'.$key.'">'.$key.'</option>';
					}else{
						print '<option selected value="'.$key.'">'.$key.'</option>';
					}
				}
				?>
				</select>
				</td>
			</tr>

			<tr class="trForm">
				<td class="tdFormCaption">優先度:</td>
				<td colspan=3><select name="topic_priority">
						<option value="">▼優先度(1:低 5:高)</option>
									<?php
				foreach($GLOBALS['topic_priority'] AS $key=>$val){ 
					if($key!=$data['topic_priority']){
						print '<option value="'.$key.'">'.$key.'</option>';
					}else{
						print '<option selected value="'.$key.'">'.$key.'</option>';
					}
				}
				?>
				</select>
				</td>
			</tr>

			<tr class="trForm">
				<td class="tdFormCaption">見積(時間):</td>
				<td colspan=3><input name="topic_cost" type="text"
					value="<?=$data["topic_cost"]?>" id="title" style="width: 100px;" />
				</td>
			</tr>

			<?php if($GLOBALS['user']['admin'] && !$GLOBALS['not_need_client_system']){ ?>
			<tr class="trForm">
				<td class="tdFormCaption">公開範囲:</td>
				<td colspan=3>
					<p style="color: red;">公開しない = 自分のみに表示</p> <select
					name="is_admin" id="is_admin">
<?php
						if ($data['is_admin']==0){
							print '<option value="0" selected>全員に公開</option>';
							print '<option value="1">管理者のみに公開</option>';
							print '<option value="2">公開しない</option>';
						}elseif ($data['is_admin']==1){
							print '<option value="0">全員に公開</option>';
							print '<option value="1" selected>管理者のみに公開</option>';
							print '<option value="2">公開しない</option>';
						}else{
							print '<option value="0">全員に公開</option>';
							print '<option value="1">管理者のみに公開</option>';
							print '<option value="2" selected>公開しない</option>';
						}
?>
				</select>
				</td>
			</tr>
			<?php } ?>

			<tr>
				<td></td>
				<td colspan=3><div>
						<br />
					</div> <input type="submit" name="_submit" id="submit" value="<?=$btnVal?>"
					class="button" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="topic_id" value="<?=$data["topic_id"]?>" />
	</form>
	<?php
include_once 'skin/inc/footer.inc';


//期限が設定されている場合日付リストを読み込む
if($data["topic_due_datetime"]){
	echo '<script language=javascript>get_date(\'date\');</script>';
}
