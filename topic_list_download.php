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


///////////////////////////////////////////////////
// XLS HTMLの出力
///////////////////////////////////////////////////

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=output.xls");

?>
<html>
<p>タスク一覧　　　	全<?=count($data);?>件</p>
<table border=1 cellspacing=0 cellpadding=2>
<tr bgcolor=gray>
	<td nowrap>ID</td>
	<td nowrap>種別</td>
	<td nowrap>状態</td>
	<td nowrap>期限</td>
	<td nowrap>更新</td>
	<td nowrap>担当者</td>
	<td nowrap>更新者</td>
	<td>タイトル</td>
	<td>概要</td>
	<td>コメント数</td>
</tr>

<?php foreach($data as $val){ ?>

<tr>
	<td nowrap><?=$val["topic_id"]?></td>
	<td nowrap><?=$val["topic_type"]?></td>
	<td nowrap><?=$val["topic_status"]?></td>
	<td nowrap><?=changeDate($val["topic_due_datetime"])?></td>
	<td nowrap><?=leftTime($val["modified_time"])?></td>
	<td nowrap><?=$val["topic_to"]?></td>
	<td nowrap><?=$val["modified_user"]?></td>
	<td nowrap><?=$val["topic_title"]?></td>
	<td width="80%"><?=$val["topic_contents"]?></td>
	<td nowrap><?=$val["topic_res_count"]?></td>
</td>
</tr>
<?php } ?>

</table>
</html>