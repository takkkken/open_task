<?php

/**
 * topic_list.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

require_once './webapp/CB.php';

$db = new CB_DB;

//管理者以外は閲覧不可にする
if(!$GLOBALS['user']['admin'])
	$where[] = "is_admin = 0";

//非公開タスクand自分以外は閲覧不可にする（未公開設定は表示しない）
$where[] = " not ( is_admin = 2 and user_name <> '".$GLOBALS['user']['user_name']."') ";

		//公開しない　タスクの場合、SKIP
		if($val["is_admin"]=="2" && $val["is_admin"]!=$GLOBALS['user']['user_name'])
			continue;

if($_GET["mode"]!=all AND !$_GET["topic_status"]){
	if($GLOBALS['topic_status_hidden']){
		foreach($GLOBALS['topic_status_hidden'] as $_topic_status_hidden)
		$where[] = "topic_status NOT LIKE '{$_topic_status_hidden}'";
	}else{
		$where[] = "topic_status NOT LIKE '完了'";
//		$where[] = "topic_status NOT LIKE '作業終了'";
	}
}



//担当者検索
if($_GET["user"]!="all" && $_GET["user"]){
	$where[] = "topic_to = '{$_GET["user"]}'";
}

//関係者検索
if($_GET["topic_cc"]!="all" && $_GET["topic_cc"]){
	$where[] = "topic_cc LIKE '%{$_GET["topic_cc"]}%'";
}

//カテゴリ検索
if($_GET["topic_type"]){
	$where[] = "topic_type = '{$_GET["topic_type"]}'";
}

//プロジェクト検索
if($_GET["topic_project"]){
	$where[] = "topic_project = '{$_GET["topic_project"]}'";
}

//優先度検索
if($_GET["topic_priority"]){
	$where[] = "topic_priority = '{$_GET["topic_priority"]}'";
}

//見積検索
if($_GET["topic_cost"]){
	$where[] = "topic_cost = '{$_GET["topic_cost"]}'";
}

//状態検索
if($_GET["topic_status"]){
	$where[] = "topic_status = '{$_GET["topic_status"]}'";
}

//キーワード検索
if($_GET["q"]){
	$where[] = "(topic.topic_title LIKE '%{$_GET["q"]}%' OR topic.topic_contents LIKE '%{$_GET["q"]}%')";
}

//ソート（期限）
switch($_GET["topic_due_datetime"]){
	case "最新順" :
		$order = "topic_due_datetime DESC";
		break;
	case "古い順" :
		$order = "topic_due_datetime ASC";
		break;
	default :
		$order = "perf_id DESC, modified_time DESC";
	break;
}

if($where){
        $whereSQL = "WHERE is_deleted=0 AND ".implode(" AND ",$where);
}else{
        $whereSQL = "WHERE is_deleted=0 ";
}

//resの最新コメントを優先して表示
$joinRes = "";
$joinRes = $joinRes . " left join (";
$joinRes = $joinRes . "     SELECT";
$joinRes = $joinRes . "         topic.topic_id as topic_id,";
$joinRes = $joinRes . "         resB.res_id as res_id,";
								//リストの更新者の優先表示仕様（表示優先度が高い順に、コメントmodified_user、コメントuser_name、タスクmodified_user、タスクuser_name）
$joinRes = $joinRes . "         ifnull(ifnull((select ifnull(modified_user,user_name) from res where res_id=resB.res_id),topic.modified_user),topic.user_name) as modified_user,";
$joinRes = $joinRes . "         case when resA.res_contents is null then ";
$joinRes = $joinRes . "             topic.topic_contents";
$joinRes = $joinRes . "         else";
$joinRes = $joinRes . "             resA.res_contents";
$joinRes = $joinRes . "         end as topic_contents";
$joinRes = $joinRes . "             ";
$joinRes = $joinRes . "     FROM";
$joinRes = $joinRes . "         (SELECT";
$joinRes = $joinRes . "             topic_id ,";
$joinRes = $joinRes . "             topic_contents,";
$joinRes = $joinRes . "             modified_user,";
$joinRes = $joinRes . "             user_name";
$joinRes = $joinRes . "          FROM";
$joinRes = $joinRes . "             topic";
$joinRes = $joinRes . "          WHERE";
$joinRes = $joinRes . "             is_deleted = 0";
$joinRes = $joinRes . "          ) as topic";
$joinRes = $joinRes . "     left join";
$joinRes = $joinRes . "         (SELECT";
$joinRes = $joinRes . "             topic_id ,";
$joinRes = $joinRes . "             max(res_id) as res_id";
$joinRes = $joinRes . "         FROM";
$joinRes = $joinRes . "             res";
$joinRes = $joinRes . "         WHERE ";
$joinRes = $joinRes . "             is_deleted=0";
$joinRes = $joinRes . "         GROUP BY";
$joinRes = $joinRes . "             topic_id";
$joinRes = $joinRes . "         ) as resB ";
$joinRes = $joinRes . "     on ";
$joinRes = $joinRes . "         topic.topic_id= resB.topic_id";
$joinRes = $joinRes . "     left join";
$joinRes = $joinRes . "         res as resA";
$joinRes = $joinRes . "     on";
$joinRes = $joinRes . "         resA.topic_id=resB.topic_id";
$joinRes = $joinRes . "         and resA.res_id=resB.res_id";
$joinRes = $joinRes . "     ) as new_comments";
$joinRes = $joinRes . " on new_comments.topic_id = topic.topic_id ";

//現在実行中ワーク（perf_id）取得
$joinRes = $joinRes . " left join (";
$joinRes = $joinRes . "     SELECT";
$joinRes = $joinRes . "         perf_id,";
$joinRes = $joinRes . "         topic_id as perf_topic_id,";
$joinRes = $joinRes . "         user_name as perf_user_name,";
$joinRes = $joinRes . "         work_start";
$joinRes = $joinRes . "     FROM";
$joinRes = $joinRes . "         perf";
$joinRes = $joinRes . "     WHERE";
$joinRes = $joinRes . "         work_end is null";
$joinRes = $joinRes . "          ) as perf";
$joinRes = $joinRes . " on perf_topic_id = topic.topic_id ";

//コスト小計（perf_id）取得
$joinRes = $joinRes . " left join (";
$joinRes = $joinRes . "      SELECT";
$joinRes = $joinRes . "          topic_id as cost_topic_id,";
$joinRes = $joinRes . "          ROUND(SUM(TIME_TO_SEC(TIMEDIFF(case when work_end is null then NOW() else work_end end,work_start)))/60/60) as work_hr";
$joinRes = $joinRes . "      FROM";
$joinRes = $joinRes . "          perf";
$joinRes = $joinRes . "      GROUP BY";
$joinRes = $joinRes . "          topic_id";
$joinRes = $joinRes . "          ) as cost";
$joinRes = $joinRes . " on cost_topic_id = topic.topic_id ";

$sql = "SELECT * FROM topic as topic {$joinRes} {$whereSQL} ORDER BY {$order}";
$data = $db->GetAll($sql);

//print( $sql );

///////////////////////////////////////////////////
// RSSの出力
///////////////////////////////////////////////////

if($_GET["mode"]=="rss"){
	require_once("XML/Serializer.php");

	$xmlData = array();
	foreach($data AS $val){
		$xmlData[] = array(
		      'title' => $val['topic_title'], 
		      'link' => SITE_URL . 'topic_detail.php?topic_id=' . $val['topic_id'], 
		      'pubDate' => $val['modified_time'], 
		      'description' => $val['topic_contents'], 
		);
	}
	$data = array( 
	  'channel' => array( 
	    'title' => PJ_NAME, 
	    'link'  => SITE_URL , 
	    'description' => '[進捗管理システム]' . PJ_NAME, 
	    'language' => 'ja-jp', 
	    'pubDate' => $xmlData[0]['pubDate'],
	    ) + $xmlData,
	); 
	$options = array( 
	  XML_SERIALIZER_OPTION_INDENT => "\t", 
	  XML_SERIALIZER_OPTION_XML_ENCODING => 'UTF-8', 
	  XML_SERIALIZER_OPTION_XML_DECL_ENABLED => TRUE, 
	  XML_SERIALIZER_OPTION_ROOT_NAME => 'rss', 
	  XML_SERIALIZER_OPTION_ROOT_ATTRIBS => array('version' => '2.0'), 
	  XML_SERIALIZER_OPTION_DEFAULT_TAG => 'item' 
	); 

	$serializer = new XML_Serializer($options); 
	$serializer->serialize($data); 
	$result = $serializer->getSerializedData(); 

	header("Content-Type: text/xml; charset=utf-8"); 
	echo $result;
	exit();
}

///////////////////////////////////////////////////
// HTMLの出力
///////////////////////////////////////////////////


?>

<?
include_once 'skin/inc/header.inc';
?>
<p class="fright">
<?php foreach($GLOBALS['topic_status'] AS $status_key=>$status_val){
	$count = getStatusCount($status_key);
	$total = $total + $count; 
?>
<a href="<?=$_SERVER["PHPSELF"]?>?topic_status=<?=urlencode($status_key)?>"><?=substr($status_key,0,3)?>(<?=$count?>)</a>
<? } ?>
<a href="./?mode=all">全(<?=$total?>)</a>
</p>
<p>
	<div id="controls" >
		<span id="perpage">
			<b>　全&nbsp;<?=count($data);?>&nbsp;件</b>　
<?
//20件位上超えた場合はページング。
if(count($data)>20){
?>
			<select onchange="sorter.size(this.value)">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="20" selected="selected">20</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
			<span>件づつ表示</span>
		</span>
		<span id="navigation">
			<img src="js/sorter/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" style="vertical-align:middle;"/>
			<img src="js/sorter/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" style="vertical-align:middle;"/>
			<span id="text"> <span id="currentpage"></span> / <span id="pagelimit"></span> </span>
			<img src="js/sorter/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" style="vertical-align:middle;"/>
			<img src="js/sorter/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" style="vertical-align:middle;"/>
		</span>
<?
}
?>
	</div>
</p>
<table width="100%" id="task_list">
<tr valign="top">
	<td width="50%">
<table border=0 width="100%" cellpadding="5" cellspacing="1"  id="table" class="tdBorder sortable">
<thead>
<tr class="trMoreDarker">
	<th nowrap>ID</th>
	<th>種別</th>
	<th>優先</th>
	<th nowrap>状態</th>
	<th nowrap>進捗</th>
	<th nowrap>作成</th>
	<th nowrap>期限</th>
	<th nowrap>更新</th>
	<th nowrap>担当者</th>
<!--	<th nowrap>関係者</th>-->
	<th nowrap>プロジェクト</th>
	<th>タイトル</th>
<!--	<th>添付</th>-->
	<th nowrap>更新者</th>
	<th>最新コメント</th>
<!--	<th>　</th>-->
</tr>
</thead>
<tbody>

<?php
	foreach($data as $val){



		//期限設定があり、かつ、期限切れの場合
		if($val["topic_due_datetime"] && substr($val["topic_due_datetime"],0,10) <= substr(date("Y-m-d"),0,10)){
			//フラッグアイコンを表示
			$todayDueflagImg = "<img src=\"./skin/images/icon/flag.png\">";
			//赤文字表示
			$limitOverDueFontColor = "red";
		}else{
			$todayDueflagImg = "";
			$limitOverDueFontColor = "black";
		}
?>
<? //現在作業中の行に色つける ?>
<tr style="background-color: <?= ($val["perf_id"]==""?"white":"#b0e0e6") ?>;">
	<td nowrap><?=$val["topic_id"]?></td>
	<td nowrap><span style="font-size: 0px;"><?=$val["topic_type"]?></span>
		<?php if($GLOBALS['topic_type'][$val["topic_type"]]){ ?>
		<img src="./skin/images/icon/<?=$GLOBALS['topic_type'][$val["topic_type"]]?>" alt="<?=$val["topic_type"]?>" width="16\" height="16" /></td>
		<?php }else{ ?>
			<?=$val["topic_type"]?>
		<? } ?>
	<td nowrap align=center><span style="font-size: 0px;"><?=$val["topic_priority"]?></span>
		<img src="./skin/images/icon/pri_<?=$val["topic_priority"]?>.gif" alt="<?=$val["topic_priority"]?>" width="16\" height="16" /></td>
	<td nowrap><b style="color:<?=$GLOBALS['topic_status'][$val["topic_status"]]?>;"><?=$val["topic_status"]?></b></td>
	<td nowrap style=text-align:right>
<?
//進捗率とミニパイチャート
if($val["topic_cost"]>0){
	$ritsu=$val["work_hr"]/$val["topic_cost"];
	echo "<span>".(round($ritsu,2)*100)."%"."</span>";
//	echo "<span class='pie'>".$ritsu."/1</span>";
	echo "<span class='pie'>".(round($ritsu/1*100)).",0,".round((1-($ritsu/1))*100)."</span>";
}
?>
	</td>
	<td nowrap><?=changeDate_md($val["registered_time"])?></td>
	<td nowrap style='color:<?=$limitOverDueFontColor?>'><?=$todayDueflagImg.changeDate_md($val["topic_due_datetime"])?></td>
	<td nowrap><span style="font-size: 0px;"><?=$val["modified_time"]?></span><?=leftTime($val["modified_time"])?></td>

	<? //perf_idが取得できた場合は現在作業中のアイコン表示 ?>
	<td nowrap><?=showTopicTo($val["topic_to"],$val["perf_id"],$val["topic_id"],$val["work_start"])?></td>

<!--	<td nowrap><?=showTopicCc($val["topic_cc"])?></td>-->
	<td nowrap><?=$val["topic_project"]?></td>
	<td nowrap><a href="topic_detail.php?topic_id=<?=$val["topic_id"]?>">
<?
if ($val["is_admin"]==2)
	echo "<img src='./skin/images/icon/private.gif' height='20'>";
?>
	<?=cutView($val["topic_title"],35)?> (<?=$val["topic_res_count"]?>)</a>
<!--	<td nowrap><?=downloadIcon($val)?></td>-->
	<td nowrap><?=$val["modified_user"]?></td>
	<td ><?=cutView($val["topic_contents"],35)?></td>
<!--	<td nowrap><a href="topic_delete.php?act=list&topic_id=<?=$val["topic_id"]?>" onClick="res=confirm('削除します。');if(res==false){return false;}">削除</a></td>-->
</tr>
<?php } ?>

</tbody>

</table>
<script type="text/javascript" src="js/sorter/script.js"></script>
<script type="text/javascript">
	var sorter = new TINY.table.sorter("sorter");
	sorter.head = "head";
	sorter.asc = "asc";
	sorter.desc = "desc";
	sorter.even = "evenrow";
	sorter.odd = "oddrow";
	sorter.evensel = "evenselected";
	sorter.oddsel = "oddselected";
	sorter.paginate = true;
	sorter.currentid = "currentpage";
	sorter.limitid = "pagelimit";
	sorter.init("table");	// 例えば、("table",1) で2カラム目をASCソート
</script>
<script type='text/javascript'>
	$(document).ready(function(){
		$('.popbox').popbox();
		$("span.pie").peity("pie");
	});
</script>


<p>
<a href=topic_list_download.php?<? foreach($_GET as $a=>$b) echo $a."=".$b ?> >Excel出力</a>　　
<a href=topic_list.php?mode=rss>RSS出力</a>
</p>
<?php
include_once 'skin/inc/footer.inc';
