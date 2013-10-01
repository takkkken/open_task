<?php
if(!isset($configpath)) $configpath="..";

$thisdir = $configpath=='.' ? 'pages/' : '' ;
require_once("$configpath/config.php");

$links = ''; $cnt = 0;

if($lang==''){
	// show default if none selected
	$json_string = file_get_contents ("$configpath/language/languages.json");
	$language = json_decode($json_string);
	$lang = $language->languages[0]->symbol;
}
parsenode(1, $node,$con,$lang);


// TODO: need better way to limit count
echo substr($links,3);


function parsenode($lid,$node,$con,$lang){
	$sql = "SELECT node.node_id,page.label FROM page ".
		"INNER JOIN node on page.node_id=node.node_id WHERE ".
		"node.parent_id=$lid AND language='$lang'";
	$resultp = mysql_query($sql,$con) or die(mysql_error());
	$np = mysql_num_rows($resultp);
	for($p=0;$p<$np;$p++){
		$id = mysql_result($resultp, $p, 'node.node_id');
		$label = mysql_result($resultp, $p, 'label');
		addlink($id."-$lang",$label);
		$cnt++;
		if($cnt > 100) return;
        parsenode($id, $node,$con,$lang);
	}
    
}


function addlink($id,$label){
	global $links,$thisdir;
	$links.= " | <a href='".$thisdir."getpage.php?id=$id&amp;title=".htmlspecialchars($label)."'>".htmlspecialchars($label)."</a> ";
}
?>