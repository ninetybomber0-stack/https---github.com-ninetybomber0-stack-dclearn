<?php
$user_reg=$_POST['user_reg'];
$pass_reg=$_POST['pass_reg'];

//echo $user_reg."<BR>".$pass_reg;
include "../config/connect.php";
//echo $dbname;
$sql = "SELECT * FROM tb_member WHERE email='$user_reg' AND pass='$pass_reg'";
echo $sql;  // ดูว่าตรงกับที่ลองใน phpMyAdmin ไหม
$result = $mysqli->query($sql);
if(!$result){
    die("Query Error: ".$mysqli->error);
}
$num=$result->num_rows;
if($result){
    echo "จำนวนแถว: " . $result->num_rows;
} else {
    echo "Query ผิดพลาด: " . $mysqli->error;
}
?>