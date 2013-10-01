<?php
    require_once("../config.php");

    $ip=$_SERVER['REMOTE_ADDR'];
	session_start();
    if(isset($_SESSION['uid'])){
        
    	$uid = $_SESSION['uid'];
        $level = $_SESSION['level'];
    
        $sql = "SELECT * FROM user WHERE user_id = $uid";
        $result = mysql_query($sql,$con)  or die("{'response':'Database Error'}");
        $user = mysql_result($result, 0, 'user_name');
        if(mysql_num_rows($result)==0){
        	echo "{'response':'no','ip':'$ip'}";
        	exit;
        } else {
            echo "{'response':'ok','level':'$level','user':'$user','ip':'$ip', 'uid':'$uid'}";
            exit;
        }       
    } 
    echo "{'response':'no','ip':'$ip'}";
?>
