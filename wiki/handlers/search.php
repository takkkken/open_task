<?php
require_once("../config.php");

$lang=$_GET['lang'];
if(strlen($lang) > 2) return;

if(!preg_match('/[a-z]{2}/',$lang)) exit;

$id = 1;

$label = '';
header("Content-Type: text/xml");

$search = $_POST['search'];
$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
$xml.=	"<results>\n";
$words = explode(" ", $search);

$sql = "SELECT * , MATCH(page_text) AGAINST ('$search*') AS rank";
$sql .= " FROM page INNER JOIN node on node.node_id = page.node_id";
$sql .= " WHERE MATCH(page_text) AGAINST ('$search*' IN BOOLEAN MODE)";
$sql .= " AND parent_id != 0"; // don't search deleted pages
$sql .= " AND page.language='$lang'"; // only search nodes of current language
$sql .= " ORDER BY rank DESC";
$result = mysql_query($sql, $con) or die("error selecting search items");
$numrows = mysql_num_rows($result);
for($r=0;$r<$numrows;$r++){
	$node_id = mysql_result($result, $r, 'node.node_id');
	$sql = "SELECT label FROM page WHERE node_id=$node_id AND language='$lang'";
	$resultl = mysql_query($sql, $con) or die("error selecting search results");
	$page_text = mysql_result($result, $r, 'page_text');
	if(mysql_num_rows($resultl) > 0){
		foreach($words as $word){
			$pattern = "/$word(?![^\[]*,[^\[\|]*\])/i"; // find words in links [not here,here ok]
			if(preg_match($pattern, $page_text)>0){
				$label = mysql_result($resultl, 0, 'label');
				$xml.=	"<file name='$node_id' title='" . htmlspecialchars( $label, ENT_QUOTES, 'UTF-8' ) . "' />\n";
				break;
			}
		}
	}
}
	
$xml.= "</results>";
echo $xml;

function xmlencode($tag){
	$tag = str_replace("&", "&amp;", $tag);
	$tag = str_replace("<", "&lt;", $tag);
	$tag = str_replace(">", "&gt;", $tag);
	$tag = str_replace("'", "&apos;", $tag);
	$tag = str_replace("\"", "&quot;", $tag);
	return $tag;
}
?>

