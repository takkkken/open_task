<?php

require_once("../config.php");
include("./editcheck.php");

if ($_POST['mode'] == 'login') {
    $user = $_POST['user'];
    $pass = md5($_POST['pass']);
    $ip = $_SERVER['REMOTE_ADDR'];

    $sql = "SELECT * FROM user WHERE user_name = '$user' AND password = '$pass'";
    $result = mysql_query($sql, $con) or die("{'response':'Database Error'}");
    if (mysql_num_rows($result) == 0) {
        echo "{'response':'Invalid Login, Please try again.'}";
        exit;
    }

    $uid = mysql_result($result, 0, 'user_id');
    $level = mysql_result($result, 0, 'level');
    if (mysql_num_rows($result) == 1) {
        session_start();
        $_SESSION['uid'] = $uid;
        $_SESSION['level'] = $level;
        $json = "{'response':'ok','level':'$level','user':'$user','ip':'$ip', 'uid':'$uid'}";
        if (mysql_result($result, 0, 'level') == 'admin')
            $_SESSION['admin'] = true;
    }else {
        $json = "{'response':'Invalid Login, Please try again.'}";
    }
} else {
    // reset password
    $email = $_POST['user'];
    $sql = "SELECT user_id, user_name FROM user WHERE email='$email'";
    $result = mysql_query($sql,$con) or die("{'response':'No user found with this email address'}");
    if(mysql_num_rows($result) == 1){
        $uid = mysql_result($result, 0, 'user_id'); 
        $username = mysql_result($result, 0, 'user_name');
        $pass = substr(md5(rand()), 0, 6); // random password.
        $sqlpass = md5($pass);

        $sql = "UPDATE user SET password='$sqlpass' where user_id=$uid";
        $result = mysql_query($sql,$con) or die("{'response':'Database Error - Unable to create password'}");

        $send = "Your request for a new password has been processed.\n\nusername: $username\npassword: $pass";  

        $to      = $email;
        $subject = "$CFG_SITE_NAME password reset";
        $from = "From: $CFG_RETURN_ADDRESS\r\n";
        $header = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n$from";

        if(!@mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $send, $header)){
            echo "{'response':'There was an error updating password'}";
            exit;
        }

        $json = "{'response':'ok', 'forgot':true}";
    } else {
        $json = "{'response':'No user found with that email address, Please try again.'}";
    }
}
echo $json;
?>
