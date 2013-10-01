<?php
    $project = $_GET['project'];
    $tracker = $_GET['tracker'];

    $feedo = $tracker=='bugs'
        ?
        "http://sourceforge.net/p/$project/$tracker/search_feed/?q=%21status%3Aclosed-wont-fix+%26%26+%21status%3Aclosed-invalid+%26%26+%21status%3Aclosed-fixed+%26%26+%21status%3Aclosed+%26%26+%21status%3Aclosed-works-for-me&limit=25&page=0&sort=ticket_num_i%20desc"
        :
        "http://sourceforge.net/p/$project/$tracker/search_feed/?q=%21status%3Awont-fix+%26%26+%21status%3Aclosed&limit=25&page=0&sort=ticket_num_i%20desc";

    $feedc = $tracker=='bugs'
        ?
        "http://sourceforge.net/p/$project/$tracker/search_feed/?q=status%3Aclosed-wont-fix+or+status%3Aclosed-invalid+or+status%3Aclosed-fixed+or+status%3Aclosed+or+status%3Aclosed-works-for-me&limit=25&page=0&sort=ticket_num_i%20desc"
        :
        "http://sourceforge.net/p/$project/$tracker/search_feed/?q=status%3Awont-fix+or+status%3Aclosed&page=0&sort=ticket_num_i%20desc";

    $content = file_get_contents($feedo);
    echo "<h2>Open Tickets</h2>";
	$x = new SimpleXmlElement($content);
    createTable($x->channel);
  
	$content = file_get_contents($feedc);
    echo "<h2>Closed Tickets</h2>";
	$x = new SimpleXmlElement($content);
    createTable($x->channel);

    function createTable($channel) {
        if(count($channel->item)==0){
            echo "<div>No tickets found.</div>";
            return;
        }
        echo "<table class='sf-tracker-table'>";
        echo "<th class='sf-tracker-th'>Title</th><th class='sf-tracker-th'>Description</th>";
        echo "<th class='sf-tracker-th'>Last Updated</th>";
        foreach($channel->item as $entry) {
            echo "<tr class='sf-tracker-tr'>";
            $cont = $entry->children('http://purl.org/rss/1.0/modules/content/');
            $encoded = $cont->encoded;
            $title = explode("-",$entry->title);
            $ticket = trim($title[0]);
            $description = preg_replace("/\\\\(.)/", "$1",str_replace("\n","<br/>",$entry->description));

            $submitted = substr($entry->pubDate, 0, 16);


            echo "<td class='sf-tracker-td'><a class='extlink' href='$entry->link' title='$entry->title'>" . $ticket . "</a></td>";
            echo "<td class='sf-tracker-td'>$description</td>";
            echo "<td class='sf-tracker-td'>$submitted</td>";

            echo "</tr>";
        }
        echo "</table>";

    }
	
?>
