<?php
$host = "10.193.20.195";
$user = "root";
$password = "mysqlroot2013";
$dbname = "crit";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    } 
// else {
//     echo "Connected successfully";
// }
?>
