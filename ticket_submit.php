<?php
session_start();
include 'db_connect.php';

$pic = 'Waiting for PIC';

$pic = $_SESSION['UserName'] ?? 'SYSTEM'; // Add this before using $pic

// EDIT ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_no']) && !isset($_POST['delete_ticket_no'])) {
    $stmt = $conn->prepare("UPDATE tickets SET trouble=?, tflag=?, requestor=?, ip_address=?, status=?, sflag=?, PIC=?, update_date=NOW() WHERE ticket_no=?");
    $stmt->bind_param("sisssssi", $_POST['trouble'], $_POST['tflag'], $_POST['requestor'], $_POST['ip_address'], $_POST['status'], $_POST['sflag'], $pic, $_POST['ticket_no']);
    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}


// DELETE ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ticket_no'])) {
    $stmt = $conn->prepare("DELETE FROM tickets WHERE ticket_no = ?");
    $stmt->bind_param("i", $_POST['delete_ticket_no']);
    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// ADD ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trouble']) && !isset($_POST['ticket_no'])) {

    // Get the current max ticket_no and increment
    $result = $conn->query("SELECT MAX(ticket_no) AS max_ticket FROM tickets");
    $row = $result->fetch_assoc();
    $new_ticket_no = ($row['max_ticket'] ?? 0) + 1;

    $stmt = $conn->prepare("INSERT INTO tickets (ticket_no, trouble, tflag, requestor, ip_address, status, sflag, timestamp, PIC, update_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())");

    $trouble = $_POST['trouble'];
    $tflag = $_POST['tflag'];
    $requestor = $_POST['requestor'];
    $ip = $_POST['ip_address'];
    $status = $_POST['status'];
    $sflag = $_POST['sflag'];
    $pic = $_SESSION['UserName'] ?? 'SYSTEM';

    $stmt->bind_param("isisssis", $new_ticket_no, $trouble, $tflag, $requestor, $ip, $status, $sflag, $pic);
    $stmt->execute();

    // ✅ Send Email Notification
    require_once("class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host     = "10.193.8.192"; // Adjust if needed
    $mail->FromName = "CRIT Ticketing System";
    $mail->From     = "critsystem@toshiba.co.jp"; // Adjust if needed
    $mail->AddAddress("kierwin1.ramos@toshiba.co.jp"); // Replace with correct recipient

    $mail->Subject = "New Ticket Submitted: # $new_ticket_no"; // ✅ Basic ASCII, no emoji


    $body  = "<html><body>";
    //$body .= "<h3>New Ticket Submitted</h3>";
    $body .= "<table border='1' cellspacing='0' cellpadding='6'>";
    $body .= "<tr><th>Ticket No</th><td>$new_ticket_no</td></tr>";
    $body .= "<tr><th>Trouble</th><td>$trouble</td></tr>";
    //$body .= "<tr><th>Trouble Flag</th><td>$tflag</td></tr>";
    $body .= "<tr><th>Requestor</th><td>$requestor</td></tr>";
    $body .= "<tr><th>IP Address</th><td>$ip</td></tr>";
    $body .= "<tr><th>Status</th><td>$status</td></tr>";
    //$body .= "<tr><th>Status Flag</th><td>$sflag</td></tr>";
    $body .= "<tr><th>PIC</th><td>$pic</td></tr>";
    $body .= "<tr><th>Timestamp</th><td>" . date('Y-m-d H:i:s') . "</td></tr>";
    $body .= "</table>";
    $body .= "<p><i>This is an auto-generated email from the CRIT System.</i></p>";
    $body .= "</body></html>";

    $mail->MsgHTML($body);
    $mail->IsHTML(true);
    $mail->WordWrap = 50;

    if (!$mail->Send()) {
        error_log("Ticket Email Failed: " . $mail->ErrorInfo);
    }

    // Redirect back after all is done
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}


?>


