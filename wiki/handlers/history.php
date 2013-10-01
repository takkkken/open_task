<?php
require_once("../config.php");
require_once("../class/class.simplediff.php");

include('language.php'); 
include("./editcheck.php");

$page = $_POST['id'];
$offset = $_POST['offset']; // page offset
$lang=$_POST['lang'];
if(strlen($lang) > 2) return;

if(!is_numeric($page) || !is_numeric($offset)) exit; // extra precaution

$editable = isEditable($page, $lang, $con, $CFG_REGISTERED_ONLY);
if($offset == 0) include("htmldiff.php");

$sql = "SELECT label FROM page WHERE node_id=$page AND language='$lang'";
$result = mysql_query($sql,$con) or die("Database Error - Unable to save page.");

if(mysql_num_rows($result) > 0){
	$label = mysql_result($result,0,'label');
}else{
	$sql = "SELECT label FROM node WHERE node_id=$page";
	$result = mysql_query($sql,$con) or die("Database Error - Unable to save page.");
	$label = mysql_result($result,0,'label');
}

// paging calcs
$sql = "SELECT COUNT(*) AS cnt FROM revision WHERE node_id=$page AND language='$lang';";
$result = mysql_query($sql,$con) or die("$sql ".mysql_error());
$nrows = mysql_result($result,0,'cnt');
$limit = 10;
$npages = ceil($nrows/$limit);
$off = $offset * $limit; // query offset

echo "<div style='float:left;font-size:18px;margin-top:14px;'><span style='font-weight:bold;color:#00a;'>$label</span></div>";
$next = $offset + 1;
$prev = $offset - 1;

// nav buttons
$imgp = $imgn = "";
if($nrows > 0 && ($prev > -1 || $next < $npages)){
	echo "<div style='clear:both;height:12px;'></div>";
	echo "<div style='text-align:center;'>";
	
	$imgp = "<span><img src='images/system/no-previous-view.png' alt='previous'/></span>&nbsp;";
	if($prev > -1)
		$imgp = "<a href='javascript:gethistory($prev)'><img src='images/system/go-previous-view.png'  style='border:none;' alt='previous' /></a>&nbsp;";
	
	$imgn = "&nbsp;<span><img src='images/system/no-next-view.png' alt='next' /></span>";
	if($next < $npages)
		$imgn = "&nbsp;<a href='javascript:gethistory($next)'><img src='images/system/go-next-view.png' style='border:none;'  alt='next' /></a>";
	
	echo $imgp.$imgn;
	
	echo "</div>";
}
	
echo "<div style='clear:both;height:12px;'></div>";

$sql = "SELECT * FROM revision  ";
$sql.= "WHERE node_id=$page AND language='$lang' ORDER BY revision_time DESC LIMIT $off,$limit";
$result = mysql_query($sql,$con) or die(mysql_error());
$cnt = mysql_num_rows($result);

$previous = $displayrev = $language->history->current;
$revision = $language->history->revision;

// first page shows current revision, all others show the next revision to restore so we need to get our starting point
$lastpage = 0; 
if($offset > 0 && $cnt > 0){
    $firstrev = mysql_result($result, 0, 'revision_id');
    $sql = "SELECT * FROM revision WHERE node_id=$page AND language='$lang' AND `type`='page' AND revision_id > $firstrev ORDER BY revision_time LIMIT 0,1";
    $r2 = mysql_query($sql,$con) or die(mysql_error());
	if(mysql_num_rows($r2) > 0){
	    $lastpage = mysql_result($r2, 0, 'revision_id');
	}
}

$lasttag=0;
for($i=0;$i<$cnt;$i++){
    $rev = mysql_result($result, $i, 'revision_id');
    $rt = mysql_result($result, $i, 'revision_time');
    $uid = mysql_result($result, $i, 'user_id');
    $ip = mysql_result($result, $i, 'user_ip');
    $type = mysql_result($result, $i, 'type');
	
	$rta = explode(" ",$rt);
    
    $comment = mysql_result($result, $i, 'comment');
    $user = "anonymous";
    $sql = "SELECT user_name FROM user WHERE user_id=$uid";
    $resultu = mysql_query($sql,$con) or die("Database Error - Unable to save page.");
    if(mysql_num_rows($resultu)>0)
        $user = mysql_result($resultu, 0,'user_name');
        
    if($i > 0 || $offset > 0) $displayrev = $type=="page" ? $lastpage : "";
    if($type=="page"){
        if($lastpage==0){
            $revno = "$revision : ".$language->history->current;
            $revert = $display = "";
        }else{
 
            $revno= $lastpage != 0 ? "$revision : $displayrev" : "$revision : ".$language->history->current;
            
            if($editable)
                $revert = $i==0 && $offset == 0 ? "" : "<span class='histrevert'><a href='javascript:revert($lastpage);'>".$language->history->revert."</a></span>";
            
            $display = $i==0 && $offset == 0 ? "" : "<span class='histdisplay'><a href='javascript:getrev($lastpage,\"$rt\",\"$offset\");'>".$language->history->display."</a></span>";
        }
    }else{
        $revert = $display = "";
	$revno = "$revision : <span style='color:#f00;'>".$language->menu->tags."</span>";
    }
    
	$compare = $type=="page" ? $lastpage : $lasttag;
    $cls = $offset==0 && $i==0 ? 'histexpand' : 'histcollapse';
    echo "<div class='histrow'><div class='histdate'>".
			"<a class='$cls' id='histlink_$rev' href='javascript:togglediff($compare,$rev,$page,\"$type\",\"histlink_$rev\")'>{$rta[0]}</a></div>".
			"<div class='histtime'>{$rta[1]}</div><div class='histrev'> $revno </div>".
			"<div class='histip'>$ip</div>".
			"<div class='histuser'> $user</div> ".
			" $revert  $display";
    if($comment !='') echo "<div class='histcomment'>$comment</div>";
    echo "<div style='clear:both;'></div></div>";
    
	if($offset==0 && $i==0){
		echo "<div id='revdiff_$rev'>";
		echo html_diff($rev, $compare, $page, $type);
	}else{	
		echo "<div id='revdiff_$rev' style='display:none'>";
	}
		
	echo "</div>";
    if($type=="page") {
        $lastpage = $rev;
    } else {
        $lasttag = $rev;
    }
    $previous = "$rev";
}

echo "<div style='min-height:20px;clear:both;'>&nbsp;</div>";
echo "<div style='text-align:center;margin-top:20px;clear:both;'>";

echo $imgp.$imgn;

echo "</div>";


?>
