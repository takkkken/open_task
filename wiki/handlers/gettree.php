<?php
error_reporting(0);
require_once("../config.php");

$lang = $_GET['lang'];
if(!preg_match('/[a-z]{2}/',$lang)) exit;

$label = '';

$xml = "<tree>";
header("Content-Type: text/xml");
echo parseTree(1, $label, $xml, $con, $lang, false). "</tree>";


function parseTree($node, $label, $xml, $con, $lang, $child=true){
	$sql = "SELECT * FROM node WHERE parent_id=$node ORDER BY node_position";
	$result = mysql_query($sql,$con) or die("<tree><root id='0'></root></tree>");
	$n = mysql_num_rows($result);
	
	if($n==0){
		if($node==1){
			return "<tree><root id='0'></root>";
		}
		$xml .= "<leaf label='" . htmlspecialchars( $label, ENT_QUOTES, 'UTF-8' ) . "' ref='$node' />";
	}else{
		// root needed for selection but not display
		if($child) $xml .= "<folder label='" . htmlspecialchars( $label, ENT_QUOTES, 'UTF-8') . "' ref='$node'>";
	
		for($f=0;$f<$n;$f++){
			$nodex = mysql_result($result, $f, 'node_id');
			$sql = "SELECT label FROM page WHERE node_id=$nodex AND language='$lang'";
			$reslutl = mysql_query($sql, $con) or die("<tree><root id='0'></root></tree>");
			
			$label = mysql_num_rows($reslutl) > 0 ? mysql_result($reslutl, 0, 'label') : mysql_result($result, $f, 'label');

			$xml = parseTree($nodex, $label, $xml, $con, $lang);
		}
		if($child) $xml .= "</folder>"; // not root
	}
	
	return $xml;
	
}

?>
