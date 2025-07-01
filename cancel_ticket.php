<?php
session_start();
include 'db_connect.php';

if (!isset($_POST['ticket_no']) || !isset($_SESSION['UserName'])) {
    echo "Invalid request.";
    exit();
}

$ticket_no = $_POST['ticket_no'];
$username = $_SESSION['UserName'];

// Optional: You can also check if the user is the one who submitted the ticket (security)
$stmt = $conn->prepare("UPDATE tickets SET status = 'Cancelled', sflag = 0, update_date = NOW() WHERE ticket_no = ?");
$stmt->bind_param("i", $ticket_no);

if ($stmt->execute()) {
    echo "Ticket #$ticket_no has been cancelled.";
} else {
    echo "Failed to cancel ticket.";
}
?>


