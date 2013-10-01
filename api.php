<?php

/**
 * api.php
 *
 * @copyright  2013/08/27 t.ishibashi
 * @license    BSD
 */


//	http://192.168.10.58/opentask/api.php?q=dailysum&date=2013-08-27&user=%E7%9F%B3%E6%A9%8B

require_once './webapp/CB.php';

$db = new CB_DB;

if($_REQUEST["q"]=="dailysum"){

	$sql = "";
    $sql = $sql . " SELECT";
    $sql = $sql . "     topic.user_name,";
    $sql = $sql . "     topic.topic_id,";
    $sql = $sql . "     topic.topic_project,";
    $sql = $sql . "     topic.topic_title,";
    $sql = $sql . "     SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(perf.work_end,perf.work_start)))) as work_hr";
    $sql = $sql . " FROM";
    $sql = $sql . "     (SELECT";
    $sql = $sql . "         topic_id,";
    $sql = $sql . "         work_end,";
    $sql = $sql . "         work_start";
    $sql = $sql . "     FROM";
    $sql = $sql . "         perf";
    $sql = $sql . "     WHERE";
    $sql = $sql . "         user_name='".$_REQUEST["user"]."' AND";
    $sql = $sql . "         SUBSTRING(work_start,1,10) = '".$_REQUEST["date"]."'";
    $sql = $sql . "     ) as perf LEFT JOIN topic as topic ON topic.topic_id = perf.topic_id";
    $sql = $sql . " GROUP BY";
    $sql = $sql . "     user_name,";
    $sql = $sql . "     topic_id,";
    $sql = $sql . "     topic_project,";
    $sql = $sql . "     topic_title";


//echo $sql;
	$data  = $db->GetAll($sql);

	print(json_encode($data));

}else{

	die("api param error");

}

exit();