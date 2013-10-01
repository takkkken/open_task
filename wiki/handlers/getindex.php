<?php
require_once("../config.php");

$lang = $_GET['lang'];
if(!preg_match('/[a-z]{2}/',$lang)) exit;

$label = '';
$in = '';
header("Content-Type: text/xml");
$in = parseTree(1, $label, $in, $con, false);
$in = substr($in, 1);

$sql = "SELECT DISTINCT tag.tag,tag.tag_id FROM tagxref ";
$sql .= "INNER JOIN tag ON tagxref.tag_id = tag.tag_id ";
$sql .= "WHERE tagxref.node_id IN ($in) AND tag.tag !='' AND tagxref.language='$lang' ORDER BY tag.tag";
$result = mysql_query($sql,$con) or die("<index><root id='$in'></root></index>");
$xml = "<index>";

for($r=0;$r<mysql_num_rows($result);$r++){
	$tag = htmlspecialchars(mysql_result($result, $r, 'tag'), ENT_QUOTES, 'UTF-8' );
	$tag_id = mysql_result($result, $r, 'tag_id');

	$sql = "SELECT DISTINCT tagxref.node_id,page.label FROM tagxref ";
	$sql .= "INNER JOIN page ON tagxref.node_id=page.node_id ";
	$sql .= "WHERE tag_id=$tag_id AND  tagxref.node_id IN ($in) AND page.language='$lang' ORDER BY page.label";
	$resultn = mysql_query($sql,$con) or die("<index><root id='-1'></root></index>");

	if(mysql_num_rows($resultn) > 0)
			$xml .= "<tag label='$tag'>"; // make sure it is not a tag from deleted page

	for($n=0;$n<mysql_num_rows($resultn);$n++){
		$node_id = mysql_result($resultn, $n, 'node_id');
		$label = mysql_result($resultn, $n, 'page.label');
		$xml.="<node id='$node_id' label='" . htmlspecialchars( $label, ENT_QUOTES, 'UTF-8' ) . "' />";
	}
	$xml.= "</tag>";
}

$xml .= "</index>";
echo $xml;

function parseTree($node, $label, $in, $con, $child=true){
	// root needed for selection but not display
	if($child){
		$in.=",$node";
	}  
	$sql = "SELECT * FROM node WHERE parent_id=$node ORDER BY node_position";
	$result = mysql_query($sql,$con) or die("<tree><root id='-2'></root></tree>");
	$n = mysql_num_rows($result);
	
	for($f=0;$f<$n;$f++){
		$nodex = mysql_result($result, $f, 'node_id');
		$label = mysql_result($result, $f, 'label');
		$in = parseTree($nodex, $label, $in, $con);
	}
	
	return $in;
}

?>
