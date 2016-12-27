<?php
session_start();

$user = $_GET["user"];
$pass = $_GET["password"];



$con = mysql_connect('localhost', 'root', '123456');
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}

mysql_select_db("phpPro", $con);

$sql="SELECT password FROM user WHERE username = '".$user."'";

$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$mypassword =  $row['password'];



$pass= (string)$pass;
$mypassword = (string)$mypassword;
$legal =  strcmp($pass,$mypassword);


if($legal==0){
    $_SESSION['user']=$user;
    $_SESSION['session_update'] = 0;
    $_SESSION['database']=0;
    echo "true";
}
else{
    echo "false";
}

mysql_close($con);
?>