<?php 
session_start();
if(!isset($_SESSION['admin']))exit;

require_once ("../config.php");

// are we deleting selected pages?
if(isset($_POST['did'])){ 
	$did = $_POST['did'];
	$sql = "DELETE FROM `node` WHERE node_id IN ($did)";
	$result = mysql_query($sql, $con) or die("Database Error - Unable to delete nodes.");	
	$sql = "DELETE FROM `page` WHERE node_id IN ($did)";
	$result = mysql_query($sql, $con) or die("Database Error - Unable to delete nodes.");	
	exit;
}

// are we deleting all pages?

// deleted nodes have parent = 0, do delete them if not root node
$sql = "SELECT `node_id` FROM `node` WHERE parent_id=0 AND node_id !=1";
$result = mysql_query($sql, $con) or die("Database Error - Unable toselect nodes.");
$do = '';
for ($r = 0; $r < mysql_num_rows($result); $r++) {
	// TODO: delete recursively for each
	$nodeid = mysql_result($result, $r, 'node_id');
	$do .= ",".$nodeid;
	$do .= dochildren($nodeid, $con);
}

$do = substr($do, 1); // strip first comma
$sql = "DELETE FROM `node` WHERE node_id IN ($do)";
$result = mysql_query($sql, $con) or die("Database Error - Unable to delete nodes.");

$sql = "DELETE FROM `page` WHERE node_id IN ($do)";
$result = mysql_query($sql, $con) or die("Database Error - Unable to delete pages.");

$sql = "DELETE FROM `revision` WHERE node_id IN ($do)";
$result = mysql_query($sql, $con) or die("Database Error - Unable to delete revisions.");

$sql = "DELETE FROM `node_revision` WHERE node_id IN ($do)";
$result = mysql_query($sql, $con) or die("Database Error - Unable to delete node revisions.");

function dochildren($nodeid, $con){
	$do = "";
	$sql = "SELECT `node_id` FROM `node` WHERE parent_id=$nodeid";
	$result = mysql_query($sql, $con) or die("Database Error - Unable toselect nodes.");
	for ($r = 0; $r < mysql_num_rows($result); $r++) {
		$childid = mysql_result($result, $r, 'node_id');
		$do .= ",".$childid;
		$do .= dochildren($childid, $con);
	}
	return $do;
}

?>
