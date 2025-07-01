<?php
include 'auth_check.php';
include 'db_connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM tickets WHERE ticket_no = $id");
header("Location: history.php");
exit();
