<?php
	$project = $_GET['project'];
	$type = $_GET['type']; //svn, bzr
    $start = $_GET['start'];

	$feed = $type == 'bzr' ?
        "http://$project.bzr.sourceforge.net/bzr/$project/atom"
        :
        "http://sourceforge.net/p/$project/code/feed";

function getBzr($entry) {
    $item = array();
    $title = explode(":",$entry->title);
    $item['ticket'] = trim($title[0]);
    $item['description'] = trim($title[1]);

    $item['committed'] = substr($entry->updated, 0, 10); //yyyy-mm-dd
    $item['href'] = $entry->link->attributes()->href;


    return $item;
}

function getSvn($entry) {
    global $feed, $project;
    $item = array();

    $description = $entry->description;

    preg_match("/href=\"\/p\/$project\/code\/([0-9]+)/",$description, $matches);
    $item['ticket'] = $matches[1];
    $item['href'] = str_replace("/feed", "/{$item['ticket']}", $feed);
    preg_match("/<p>(.+)?<br\s?\/>/",$description, $matches);
    $item['description'] = $matches[1];

    $item['committed'] = substr($entry->pubDate, 0, 10); //yyyy-mm-dd
    return $item;
}

$content = file_get_contents($feed);
	$x = new SimpleXmlElement($content);  
  
	echo "<table class='sf-code-table'>";  
	echo "<th class='sf-code-th'>Revision Number</th><th class='sf-code-th'>Description</th>";
	echo "<th class='sf-code-th'>Date Committed</th>";

    $commit = $type == 'bzr' ? $x->entry : $x->channel->item;
  
	foreach($commit as $entry) {
		echo "<tr>";

        if($type == 'bzr')
            $item = getBzr($entry);
        else
            $item = getSvn($entry);

        if($item['ticket'] < $start) break;

		echo "<td class='sf-code-td'><a class='extlink' href='{$item['href']}' title='$entry->title'>" . $item['ticket'] . "</a></td>";
        echo "<td class='sf-code-td'>{$item['description']}</td>";
        echo "<td class='sf-code-td'>{$item['committed']}</td>";
		echo "</tr>";
    }  
    echo "</table>";  
	
?>
