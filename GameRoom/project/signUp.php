<?php
session_start();

$user = $_GET["user"];
$pass = $_GET["password"];
$email = $_GET["email"];



$con = mysql_connect('localhost', 'root', '123456');
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}

mysql_select_db("phpPro", $con);

$sql="INSERT INTO `user` ( `username` , `password` , `email` , `note` )VALUES ('".$user."', '".$pass."','".$email."', 0)";

$result = mysql_query($sql);


echo $result;

mysql_close($con);
?>