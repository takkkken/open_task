<?php

/**
 * CB_Function.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

function d($data){
    require_once LIB_DIR . '/dBug/dBug.php';
    new dBug($data);
}

function getStatusCount($status){
	$db = new CB_DB;
	if(!$GLOBALS['user']['admin']) $where[] = $where[] = "is_admin = 0";
	 $where[] = "topic_status = '{$status}'";
	if($where) $whereSQL = "WHERE is_deleted=0 AND ".implode(" AND ",$where);
	$data = $db->GetAll("SELECT topic_id FROM topic {$whereSQL}");
	return count($data);
}

function httpRedirect($url,$data = false){
	if($data){
		$request = "?" . http_build_query($data);
	}
	header("Location: {$url}{$request}");
	exit();
}

function viewContents($text){
	$text = htmlspecialchars($text);
	$uri_p = "/(https?|ftp)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/ei";
	//バグ対策
	//$uri_r = "'<a href=\"javascript:document.location.href=\'\\1\\2\'\" target=\"_blank\">\\1\\2</a>'";
	$uri_r = "'<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>'";
	$text = preg_replace($uri_p, $uri_r, $text);
	$text = preg_replace("/\t/is", "　", $text);
	$text = nl2br($text);

	//セキュリティー上開けない・・・
	$text = preg_replace("/(\\\\.+)</", "<a href='file:///\\1'>\\1</a><", $text);

	return $text;
}

function cutView($string, $length = 30, $etc = '...') {
	$string = htmlspecialchars($string);
	mb_internal_encoding("UTF-8");
	$plane_string = strip_tags($string);
	
	$length = $length + strlen($string) - strlen($plane_string);
	$string = preg_replace("/<br([^>]*)>/is","",$string);
	if (mb_strlen($plane_string) > $length) {
		$length -= strlen($etc);
		$string = mb_substr($string, 0, $length);
		$string = mb_substr($string, 0, 40 + (strlen($string) - strlen(mb_convert_kana($string, "a"))));
		return mb_substr($string, 0, $length).$etc.$tail_tag;
	} else {
		return $string;
	}
}

function leftTime($unixTime){
	$sec = time()-strtotime($unixTime);
	$min = ($sec/60) + 1;
	if($min >= 60){
		$hour = intval($min/60);
	}else{
		return intval($min) . "分前";
	}

	if($hour >= 24){
		$day = intval($hour/24);
	}else{
		return intval($hour) . "時間前";
	}
	return intval($day) . "日前";
}

function changeDate($datetime){
	if(!$datetime) return false;
	return substr($datetime,0,4)."/".substr($datetime,5,2)."/".substr($datetime,8,2);
}

function changeDate_md($datetime){
	if(!$datetime) return false;
	return ((int)substr($datetime,5,2))."/".((int)substr($datetime,8,2));
}

function getExt($str){
	$str = preg_replace("/(.*)\.([a-zA-Z0-9]+)$/is",".$2",$str);
	return strtolower($str);
}

function showImage($data){

	if($data["file_name"]){
		foreach($GLOBALS['image_type'] AS $key=>$val){
			if(preg_match("/\.{$key}$/is",$data["file_name"])){
				if($data["topic_id"]) $id = "topic_id";
				if($data["res_id"])   $id = "res_id";
				$image = "<a href=\"./img.php?{$id}=" . $data[$id] . "\" target=\"_blank\"><img src=\"./img.php?{$id}=" . $data[$id] . "&w=100\" /></a>";
				break;
			}
		}

	}
	return $image;

}

function downloadString($data){

	if($data["file_name"]){
		foreach($GLOBALS['contents_type'] AS $key=>$val){
			if(preg_match("/\.{$key}$/is",$data["file_name"])){
				if($data["topic_id"]) $id = "topic_id";
				if($data["res_id"])   $id = "res_id";
				return "添付ファイル：" . urldecode($data["file_name"]) . "<a href=\"./download.php?{$id}=" . $data[$id] . "\"><img src=\"./skin/images/icon/{$val[1]}\" width=\"16\" height=\"16\" /></a>";
			}
		}

	}

}

function downloadIcon($data){

	if($data["file_name"]){
		foreach($GLOBALS['contents_type'] AS $key=>$val){
			if(preg_match("/\.{$key}$/is",$data["file_name"])){
				if($data["topic_id"]) $id = "topic_id";
				if($data["res_id"])   $id = "res_id";
				return "<a href=\"./download.php?{$id}=" . $data[$id] . "\"><img src=\"./skin/images/icon/{$val[1]}\" width=\"16\" height=\"16\" /></a>";
			}
		}

	}

}

function showTopicTo($to,$perf_id="",$topic_id="",$start_time){

	//I'am Working Now
	if($perf_id!=""){
		$replace_my_after = "<img width=16 src=./skin/images/icon/working.gif><a href=\"perf_update.php?perf_id=$perf_id\">$1</a>";

		//Work完了時に、日時を設定可能に
		if($start_time!=""){

			$start_time = substr($start_time,11,5);
//			$end_time   = substr($end_time,11,5);
			$replace_my_after = "
				<img width='16' src='./skin/images/icon/working.gif'>
				<span class='popbox'><a class='open myname' href='#'>$1</a><span class='collapse'><span class='box'><span class='arrow'>	</span><span class='arrow-border'></span>
				<p>
				以下の時間で登録します
				</p><form action='perf_update.php?perf_id=$perf_id' method='post'>
				<input type='time' name='work_start' value='$start_time' >&nbsp;～&nbsp;
				<input type='time' name='work_end'   value=''>(空：now)
				<p>
				<input type='submit' name='regTime' value='OK'>　<a href='#' class='close'>cancel</a>
				</p></form>
				</span></span></span>
			";
		}

		$add_other_star = "<img width=24 src=./skin/images/icon/working2.gif>";
	//I'am Not Working
	}elseif($topic_id!="" ){
		$replace_my_after = "<a href='perf_update.php?topic_id=$topic_id' class='myname'>$1</a>";
	//OtherMenber
	}else{
		$replace_my_after = "<span style=\"color:blue;\">$1</span>";
	}
	
	if(preg_match("/({$GLOBALS["user"]["user_name"]})/is",$to)){
		$to = preg_replace("/({$GLOBALS["user"]["user_name"]})/is",$replace_my_after,$to);
	}else{
		$to = $add_other_star.$to;
	}

	return preg_replace("/,/is","<br />",$to);
/*	$to = explode(",",$to);
	if(count($to)>1){
		return $to[0]."...";
	}else{
		return $to[0];
	}*/
}

function showTopicCc($cc){
	$to = preg_replace("/({$GLOBALS["user"]["user_name"]})/is","<span style=\"color:blue;\">$1</span>",$cc);
	return preg_replace("/,/is","<br />",$cc);
/*	$to = explode(",",$to);
	if(count($to)>1){
		return $to[0]."...";
	}else{
		return $to[0];
	}*/
}

function addModifiedUser($userName){
	$db = new CB_DB;
	$user = $db->getRow("SELECT * FROM modified_user_count WHERE user_name = '{$userName}'");
	if(!$user) return $db->Insert("modified_user_count",array("user_name"=>$userName));
	$db->Update("modified_user_count",array("modified_count"=>$user["modified_count"]+1),"user_name = '{$userName}'");
}

function statusColor($status){
	return $GLOBALS['topic_status_bg'][$status];
}

function sendMail($users,$type,$data){
    $site = SITE_URL;
	$body = file_get_contents(WEBAPP_DIR . "/share/mail/{$type}.tpl");
	foreach($data AS $key=>$val) $$key = $val; 

	//関係者宛
	if(!is_array($data["topic_cc"])){
		$data["topic_cc"] = @explode(",",$data["topic_cc"]);
	}
	foreach($data["topic_cc"] AS $user){
		if($user!=$GLOBALS["user"]["user_name"]){
			$email = userName2Mail($user);
			eval("\$contents = \"" . ereg_replace("\"","\\\"",$body) . "\";");
		        if(!$data["subject"]) $data["subject"] = "無題";
		        mailSender(FROM_MAIL, $email, $subject, $contents);
	        }
	}
	//担当者宛
	if($data["topic_to"]!=$GLOBALS["user"]["user_name"]){
		$email = userName2Mail($data["topic_to"]);
		eval("\$contents = \"" . ereg_replace("\"","\\\"",$body) . "\";");
	        if(!$data["subject"]) $data["subject"] = "無題";
	        mailSender(FROM_MAIL, $email, $subject, $contents);
    }
	return true;
}

function mailSender($MailFrom, $MailTo, $Subject, $Message)
{
	// Subject部分を変換
	$xSubject = mb_convert_encoding($Subject, "JIS", "UTF-8");
	$xSubject = base64_encode($xSubject);
	$xSubject = "=?iso-2022-jp?B?".$xSubject."?=";
	
	// Message部分を変換
//	$xMessage = htmlspecialchars($Message);
	$xMessage = $Message;
	$xMessage = str_replace("&amp;", "&", $xMessage);
	if (get_magic_quotes_gpc()) $xMessage = stripslashes($xMessage);
	$xMessage = str_replace("\r\n", "\r", $xMessage);
	$xMessage = str_replace("\r", "\n", $xMessage);	
	$xMessage = mb_convert_encoding($xMessage, "JIS", "auto");

	// Header部分を生成	
	$GMT = date("Z");
	$GMT_ABS  = abs($GMT);
	$GMT_HOUR = floor($GMT_ABS / 3600);
	$GMT_MIN = floor(($GMT_ABS - $GMT_HOUR * 3600) / 60);
	if ($GMT >= 0) $GMT_FLG = "+"; else $GMT_FLG = "-";
	$GMT_RFC = date("D, d M Y H:i:s ").sprintf($GMT_FLG."%02d%02d", $GMT_HOUR, $GMT_MIN);

	$Headers  = "Date: ".$GMT_RFC."\n";
	$Headers .= "From: $MailFrom\n";
	$Headers .= "Subject: $xSubject\n";
	$Headers .= "MIME-Version: 1.0\n";
	$Headers .= "X-Mailer: PHP/".phpversion()."\n";
	$Headers .= "Content-type: text/plain; charset=ISO-2022-JP\n";
	$Headers .= "Content-Transfer-Encoding: 7bit";
	
//	return;
	return mail($MailTo, $xSubject, $xMessage, $Headers);
}


function userName2Mail($user){
	foreach($GLOBALS['auth_user'] AS $allUser){
		if($user == $allUser[0] && $GLOBALS["user"]["user_namr"]!=$user){
			return $allUser[1];
		}
	}
	return false;
}

function hiddenForm(){
	foreach($_GET AS $key=>$val){
		$hidden .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$val}\">\n\t\t";
	}
	return $hidden;

}

if(!function_exists('http_build_query')) {
    function http_build_query($data,$prefix=null,$sep='',$key='') {
        $ret    = array();
            foreach((array)$data as $k => $v) {
                $k    = urlencode($k);
                if(is_int($k) && $prefix != null) {
                    $k    = $prefix.$k;
                };
                if(!empty($key)) {
                    $k    = $key."[".$k."]";
                };

                if(is_array($v) || is_object($v)) {
                    array_push($ret,http_build_query($v,"",$sep,$k));
                }
                else {
                    array_push($ret,$k."=".urlencode($v));
                };
            };

        if(empty($sep)) {
            $sep = ini_get("arg_separator.output");
        };

        return    implode($sep, $ret);
    }
}