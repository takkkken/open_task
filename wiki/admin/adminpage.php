<?php 
session_start();
if (!isset($_SESSION['admin']))
	exit;
	
require_once ("../config.php");
$page = $_POST['page'];

header("Content-Type: text/html");
$sql = "SELECT * FROM `page` WHERE node_id=$page";
$result = mysql_query($sql, $con) or die("Database Error - Unable to retrive page. ".mysql_error());
$locked = mysql_result($result, 0, 'locked') == 0 ? '' : ' checked';
//$html = "<div style='margin-top:12px;'><a href='javascript:tree.click($page)'>Return</a></div>";
$html .= "<h2>Admin</h2>";

// this page
$html .= "<div style='clear:both;'></div>";
$html .= "<hr class='admin' />";
$html .= "<h3>This Page</h3>";
$html .= "<div class='adminlabel'>Locked</div>";
$html .= "<div class='adminlabel'>&nbsp;</div>";
$html .= "<div class='admininput'><input type=checkbox id='lockbox' onclick='lockpage(this);' $locked/></div>";

// get page information
$sql = "SELECT page.node_id FROM `node` INNER JOIN page ON page.node_id=node.node_id WHERE parent_id !=0";
$result = mysql_query($sql, $con) or die("Database Error - Unable to retrive page. ".mysql_error());
$cntall = mysql_num_rows($result);

$sql = "SELECT page.node_id FROM `node` INNER JOIN page ON page.node_id=node.node_id WHERE parent_id !=0 AND page.locked=1";
$result = mysql_query($sql, $con) or die("Database Error - Unable to retrive page. ".mysql_error());
$cntlocked = mysql_num_rows($result);

// lock / unlock all pages
$html .= "<div style='clear:both;'></div>";
$html .= "<hr class='admin' />";
$html .= "<h3>All Pages</h3>";
$html .= "<div class='adminlabel'>Total Pages</div>";
$html .= "<div class='adminvalue'>$cntall</div><br/>";
$html .= "<div class='adminlabel'>Locked Pages</div>";
$html .= "<div class='adminvalue'>$cntlocked</div>";
$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Lock All' onclick='lockall(true);'/></div>";
$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Unlock All' onclick='lockall(false);'/></div>";
$html .= "<div style='clear:both;'></div>";
$html .= "<hr class='admin' />";
$html .= "<h3>History</h3>";

// get history information
$sql = "SELECT node_id FROM `revision` WHERE 1";
$result = mysql_query($sql, $con) or die("Database Error - Unable to retrive page.");
$cntall = mysql_num_rows($result);

// clear history for all pages
$html .= "<div style='clear:both;'></div>";
$html .= "<div class='adminlabel'>History Entries</div>";
$html .= "<div class='adminvalue'>$cntall</div>";
// clear history for this pages
$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Clear Current Page History' onclick='clearhistory(false);'/></div>";
$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Clear History for All Pages' onclick='clearhistory(true);'/></div>";

// row 2
$html .= "<div style='clear:both;height:20px;'></div>";
$html .= "<div class='adminlabel'>&nbsp;</div>";
$html .= "<div class='adminlabel'>&nbsp;</div>";
// clear history for this pages
$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Clear History older than(days)' onclick='clearhistory(\"days\");'/></div>";
$html .= "<div class='admininput'><input type='text' size='3' id='clear_days' /></div>";


// deleted nodes have parent = 0, do count them if not root node
$sql = "SELECT `node_id`,`label` FROM `node` WHERE parent_id=0 AND node_id!=1";
$result = mysql_query($sql, $con) or die("Database Error - Unable toselect nodes. ").mysql_error();
$deletedpages = mysql_num_rows($result);
$html .= "<div style='clear:both;'></div>";
$html .= "<hr class='admin' />";
$html .= "<h3>Deleted Pages</h3>";


for ($i = 0; $i < $deletedpages; $i++) {
	$html .= "<div class='admininput'>".mysql_result($result, $i, 'label')."</div>";
	$deletedid = mysql_result($result, $i, 'node_id');
	$html .= "<div class='admininput'><input type=checkbox id='deleted_$deletedid' /></div>";
	$html .= "<div style='clear:both;'></div>";
}

if($deletedpages > 0){
	$html .= "<div style='clear:both;'>&nbsp;</div>";
	$html .= "<div class='admininput'>&nbsp;</div>";
	$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Purge Selected Pages' onclick='purge(true)'/></div>";
	$html .= "<div style='clear:both;'>&nbsp;</div>";
}

$html .= "<div style='clear:both;'></div>";
$html .= "<div class='adminlabel'>Deleted Pages</div>";
$html .= "<div class='adminvalue'>$deletedpages</div>";
// clear history for this pages
if($deletedpages > 0)
	$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Purge All Pages' onclick='purge()'/></div>";

// Table overhead
$overhead = 0;
// Find the names of all tables in the selected db.
$alltables = mysql_query('SHOW TABLES');
// Go through all tables.
while ($table = mysql_fetch_assoc($alltables)) {
	foreach ($table as $db=>$tablename) {
		$sql = "SHOW TABLE STATUS FROM $CFG_DATABASE WHERE name='$tablename'";
		$result = mysql_query($sql, $con) or die("Database Error - Unable to select nodes. ").mysql_error();
		$overhead += mysql_result($result, 0, 'Data_free');
	}
}

$html .= "<div style='clear:both;'></div>";
$html .= "<hr class='admin' />";
$html .= "<h3>Table Overhead</h3>";

$html .= "<div style='clear:both;'></div>";
$html .= "<div class='adminlabel'>Total bytes</div>";
$html .= "<div class='adminvalue'>$overhead</div>";
// clear history for this pages
$html .= "<div class='admininput'><input class='adminbutton' type='button' value='Optimize Tables' onclick='optimize()'/></div>";

// Blocked users

$html .= "<div style='clear:both;'></div>";
$html .= "<hr class='admin' />";
$html .= "<h3>Blocked IPs</h3>";

$html .= "<div class='admininput'><input type='text' id='ipblock' /></div>";
$html .= "<div class='admininput'><input class='adminbutton' type='button' onclick='block(\"add\")'; value='Add' /></div>";

$sql = "SELECT * FROM `blocked`";
$result = mysql_query($sql, $con) or die("Database Error - Unable to retrieve blocked users. ").mysql_error();

$html .= "<div style='clear:both;'></div><br/>";

while ($ip = mysql_fetch_assoc($result)) {

	$html .= "<div class='adminvalue'>{$ip['ip_address']}</div>";
	$html .= "<div class='adminvalue'>&nbsp;</div>";
	$html .= "<div class='admininput'><a href='javascript:dummy()' onclick='block(\"{$ip['ip_address']}\");return false;'/>unblock</a></div>";
	$html .= "<div style='clear:both;'></div>";
}
/**/
echo $html;

?>
