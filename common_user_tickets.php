<?php
if (!isset($_SESSION['UserName'])) {
    header("Location: index.php#login");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['UserName'];
$dept = $_SESSION['DEPT'];

// Filter
$filter = isset($_GET['status']) ? $_GET['status'] : 'ALL';

$sql = "SELECT * FROM tickets WHERE requestor = ?";
$params = [$username];
if ($filter !== 'ALL') {
    $sql .= " AND status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY update_date DESC";

$query = $conn->prepare($sql);
$types = str_repeat('s', count($params));
$query->bind_param($types, ...$params);
$query->execute();
$result = $query->get_result();
$rowCount = $result->num_rows;

function getStatusUI($status, $pic = '') {
    $html = '<div class="ui-status-container">
                <div class="ui-status">
                    <div class="status-img-wrapper">
                        <img src="{STILL}" class="status-img still-img">
                        <img src="{GIF}" class="status-img gif-img">
                    </div>
                    <h2>{TITLE}</h2>
                    <p>{DESC}</p>
                </div>
            </div>';

    switch (strtoupper($status)) {
        case 'ON-GOING':
            return str_replace(
                ['{GIF}', '{STILL}', '{TITLE}', '{DESC}'],
                ['css/img/code.gif', 'css/img/code.png', "Hang tight, " . htmlspecialchars($pic) . " is working on it!", 'Talk to Us!'],
                $html
            );
        case 'RESOLVED':
            return str_replace(
                ['{GIF}', '{STILL}', '{TITLE}', '{DESC}'],
                ['css/img/done.gif', 'css/img/done.png', 'Trouble fixed!', 'Thank you for your patience!'],
                $html
            );
        case 'CANCELLED':
            return str_replace(
                ['{GIF}', '{STILL}', '{TITLE}', '{DESC}'],
                ['css/img/cancelled.gif', 'css/img/cancelled.png', 'Your ticket was cancelled.', 'Talk to Us!'],
                $html
            );
        default:
            return str_replace(
                ['{GIF}', '{STILL}', '{TITLE}', '{DESC}'],
                ['css/img/busy.gif', 'css/img/busy.png', 'Our support is handling other tasks', 'We’ll be there in a flash!'],
                $html
            );
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Tickets</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
        }

        h2 { color: #333; }

        .ticket-container {
            padding: 20px 24px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
            border-left: 6px solid #95A9E8;
            background: #fff;
            transition: 0.2s;
        }

        .ticket-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 8px;
        }

        .ticket-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .ticket-detail {
            margin: 6px 0;
            font-size: 14px;
            color: #555;
        }

        .ticket-detail strong {
            color: #2c3e50;
            min-width: 100px;
            display: inline-block;
        }

        .ticket-status {
            font-weight: bold;
            color: #2ecc71;
        }

        .btn {
            background-color: #95A9E8;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #CAD8FB;
        }

        .user-scrollable-tickets {
            max-height: 825px;
            overflow-y: auto;
            padding-right: 10px;
            border-radius: 8px;
            background: #fff;
        }

        .user-scrollable-tickets::-webkit-scrollbar {
            width: 8px;
        }

        .user-scrollable-tickets::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 10px;
        }

        .noTickets {
            text-align: center;
            margin-top: 40px;
            color: #555;
        }

        .noTickets img {
            width: 120px;
            opacity: 0.7;
            margin-top: 15px;
        }
        .ui-status-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 260px;
        min-height: 200px;
        text-align: center;
    }
        .status-img-wrapper {
            position: relative;         /* Important: anchor for absolute children */
            width: 100px;
            height: 100px;
            margin-top: 15px;
            align-items: center;
            justify-content: center;
        }
        .status-img-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin-top: 15px;
        }

        .status-img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 6px;
            transition: opacity 0.5s ease;
        }

        .still-img {
            z-index: 1;
            opacity: 1;
                margin-top:15px;
            height: 70%;
        }

        .gif-img {
            z-index: 2;
            opacity: 0;
        }

        /* On hover, fade in the GIF and fade out the still */
        .status-img-wrapper:hover .gif-img {
            opacity: 1;
        }

        .status-img-wrapper:hover .still-img {
            opacity: 0;
        }


    </style>
</head>
<body>

<h2>My Request</h2>

<div id="ticketTable">
    <div style="margin-bottom: 20px;">
        <?php
        $statuses = ['ALL', 'PENDING', 'ON-GOING', 'RESOLVED', 'CANCELLED'];
        foreach ($statuses as $status) {
            $active = ($filter === $status) ? 'style="background-color:#CAD8FB;color:black;"' : '';
            echo "<a href='?status=$status' class='btn' $active>$status</a> ";
        }
        ?>
    </div>

    <?php if ($rowCount > 0): ?>
        <div class="user-scrollable-tickets">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="ticket-container">
                    <div class="ticket-header">
                        <div style="flex: 1; margin-right: 24px;">
                            <div class="ticket-title">Ticket #<?= $row['ticket_no'] ?></div>
                            <div class="ticket-detail"><strong>Trouble:</strong> <?= $row['trouble'] ?></div>
                            <div class="ticket-detail"><strong>Requestor:</strong> <?= $row['requestor'] ?></div>
                            <div class="ticket-detail"><strong>IP Address:</strong> <?= $row['ip_address'] ?></div>
                            <div class="ticket-detail"><strong>Status:</strong> <span class="ticket-status"><?= $row['status'] ?></span></div>
                            <div class="ticket-detail"><strong>Timestamp:</strong> <?= $row['timestamp'] ?></div>
                            <div class="ticket-detail"><strong>PIC:</strong> <?= $row['pic'] ?></div>
                        </div>
                        <?= getStatusUI($row['status'], $row['pic']) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="noTickets">
            <h2>Hi <?= htmlspecialchars($username) ?>!</h2>
            <?php if ($filter === 'ALL'): ?>
                <h2>No tickets found.</h2>
                <p>Click the Request Button to create a new ticket.</p>
            <?php else: ?>
                <h2>You have no <strong><?= htmlspecialchars($filter) ?></strong> tickets.</h2>
                <p>Everything looks good or your request is still being processed!</p>
            <?php endif; ?>
            <img src="css/img/ticket.gif" alt="No tickets">
        </div>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.hover-gif').forEach(img => {
    const gif = img.getAttribute('data-gif');
    const still = img.getAttribute('data-still');

    img.addEventListener('mouseenter', () => img.src = gif);
    img.addEventListener('mouseleave', () => img.src = still);
});

</script>

</body>
</html>
