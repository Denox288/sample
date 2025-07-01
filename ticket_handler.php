<?php
include 'db_connect.php';

function add_ticket($conn, $trouble, $tflag, $requestor, $ip_address, $status, $sflag) {
    $pic = 'Waiting for PIC';
    $stmt = $conn->prepare("INSERT INTO tickets (trouble, tflag, requestor, ip_address, status, sflag, timestamp, PIC, update_date) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, NOW())");
    $stmt->bind_param("sisssiss", $trouble, $tflag, $requestor, $ip_address, $status, $sflag, $pic);
    $stmt->execute();
}

function update_ticket($conn, $ticket_no, $trouble, $tflag, $requestor, $ip_address, $status, $sflag) {
    $pic = $_SESSION['UserName'] ?? 'Unknown';
    $stmt = $conn->prepare("UPDATE tickets SET trouble=?, tflag=?, requestor=?, ip_address=?, status=?, sflag=?, PIC=?, update_date=NOW() WHERE ticket_no=?");
    $stmt->bind_param("sisssssi", $trouble, $tflag, $requestor, $ip_address, $status, $sflag, $pic, $ticket_no);
    $stmt->execute();
}


function delete_ticket($conn, $ticket_no) {
    $stmt = $conn->prepare("DELETE FROM tickets WHERE ticket_no = ?");
    $stmt->bind_param("i", $ticket_no);
    $stmt->execute();
}

function fetch_pending_tickets($conn, $limit, $offset, $search_sql = '') {
    $count_result = $conn->query("SELECT COUNT(*) as total FROM tickets WHERE status = 'PENDING' $search_sql");
    if (!$count_result) {
        die("Count query error: " . $conn->error);
    }
    $total_rows = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);

    $query = "
        SELECT * FROM tickets 
        WHERE status = 'PENDING' $search_sql 
        ORDER BY timestamp DESC 
        LIMIT $limit OFFSET $offset
    ";
    $result = $conn->query($query);
    if (!$result) {
        die("Select query error: " . $conn->error);
    }

    return [$result, $total_pages];
    
}

// If POST is submitted, call appropriate function
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_ticket_no'])) {
        delete_ticket($conn, $_POST['delete_ticket_no']);
    } else {
        $ticket_no = $_POST['ticket_no'];
        $trouble = $_POST['trouble'];
        $tflag = $_POST['tflag'];
        $requestor = $_POST['requestor'];
        $ip_address = $_POST['ip_address'];
        $status = $_POST['status'];
        $sflag = $_POST['sflag'];

        if ($ticket_no == "") {
            add_ticket($conn, $trouble, $tflag, $requestor, $ip_address, $status, $sflag);
        } else {
            update_ticket($conn, $ticket_no, $trouble, $tflag, $requestor, $ip_address, $status, $sflag);
        }
    }

    // Redirect back
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
function fetch_all_pending_tickets($conn, $search_sql = '') {
    $sql = "SELECT * FROM tickets WHERE status='PENDING' $search_sql ORDER BY timestamp DESC";
    $result = $conn->query($sql);
    return $result;
}
