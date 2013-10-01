<?php
//require_once("../config.php");

function isEditable($page, $lang, $con, $CFG_REGISTERED_ONLY){
    $admin = false;
    session_start();
    $loggedon = isset($_SESSION['uid']);
    if($loggedon){
            $sql = "SELECT level FROM user WHERE user_id={$_SESSION['uid']}";
            $result = mysql_query($sql,$con) or die("Database Error - Unable to retrive user info. ".mysql_error());
            if(mysql_num_rows($result) > 0)
                    $admin = mysql_result($result, 0, 'level')=='admin';

    }


    $locked = 0;
    $sql = "SELECT locked FROM page WHERE node_id=$page AND language='$lang'";
    $result = mysql_query($sql,$con) or die("Database Error - Unable to retrive locked status. ".mysql_error());
    if(mysql_num_rows($result) > 0)
            $locked = mysql_result($result, 0, 'locked');

    $ip=$_SERVER['REMOTE_ADDR'];

    $sql = "SELECT ip_address FROM blocked WHERE ip_address='$ip'";
    $result = mysql_query($sql,$con) or die("Database Error - Unable to retrive blocked status.");
    $blocked = mysql_numrows($result) > 0;

    $registered = ($CFG_REGISTERED_ONLY && $loggedon) || !$CFG_REGISTERED_ONLY;

    $edit = ($locked == 0 && !$blocked && $registered) || $admin;

    if($admin) $edit=2;

    return $edit;
}

function isPrimary($lang) {
    	$json_string = file_get_contents ("language/languages.json");
        $languages = json_decode($json_string);
        $primary = $languages->languages[0]->symbol;
        return $primary == $lang;
}
?>
