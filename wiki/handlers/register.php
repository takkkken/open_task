<?php
require_once("../config.php");

$json = "";

$user = $_POST['user'];
$pass = md5($_POST['pass']);
$email = $_POST['email'];
$ip=$_SERVER['REMOTE_ADDR'];

$sql = "SELECT * FROM user WHERE user_name = '$user'";
$result = mysql_query($sql,$con)  or die("{'response':'Database Error'}");
if(mysql_num_rows($result) == 1){
	$json = "{'response':'Duplicate User','level':'user','user':'$user'}";
}else{
	$sql = "INSERT INTO user (user_id, user_name, password, email) VALUES('','$user','$pass','$email')";
	$result = mysql_query($sql,$con)  or die("{'response':'Database Error'}");
	$uid = mysql_insert_id($con);
	$json = "{'response':'ok','level':'user','user':'$user','ip':'$ip','uid':$uid}";
    
    // start logged on
	session_start(); 
	$_SESSION['uid'] = $uid;
	$_SESSION['level'] = 'user';
}
echo $json;
?>