<?php
require_once("../config.php");

$rev = $_POST['id'];
if(!is_numeric($rev)) exit; // extra precaution

$sql = "SELECT page_text FROM revision WHERE revision_id=$rev";
$result = mysql_query($sql,$con) or die("Database Error");
$rt = $_POST['rt'];

echo "=Revision $rev : $rt=\n<hr/>\n".stripslashes(mysql_result($result, 0, 'page_text'));

?>