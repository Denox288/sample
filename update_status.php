<?php
session_start(); // Make sure session is started
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_no = $_POST['ticket_no'] ?? '';
    $status = $_POST['status'] ?? '';
    $username = $_SESSION['UserName'] ?? '';

    if ($ticket_no && $status && $username) {
        $stmt = $conn->prepare("UPDATE tickets SET status = ?, sflag = ?, pic = ?, update_date = NOW() WHERE ticket_no = ?");
        $sflag = ($status === 'ON-GOING') ? 1 : 0;

        // Bind parameters (s = string, i = integer)
        $stmt->bind_param("sisi", $status, $sflag, $username, $ticket_no);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid';
    }
} else {
    echo 'invalid';
}
?>
