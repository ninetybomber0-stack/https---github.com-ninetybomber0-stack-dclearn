<?php
$host="localhost";
$user="root";
$pw="12345678";
$name_db="kunuengc_dcl";

$mysqli=new MySQLi($host,$user,$pw,$name_db);
if($mysqli->connect_errno) {
    echo $mysqli->connect_errno;
    exit;
    }
?>