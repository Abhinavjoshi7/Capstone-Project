<?php 
$host_name = 'localhost';
$db_user = 'strawfields2';
$db_password = ''; 
$db_name = '';
// Make sure to create a developer role before executing this script
$connect = mysqli_connect($host_name, $db_user, $db_password, $db_name);

if(!$connect){
    die("An error has occured while connecting to the database" . $connect);
}
?>