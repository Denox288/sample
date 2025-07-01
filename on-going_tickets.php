<?php
if (!isset($_SESSION['UserName'])) {
    header("Location: index.php#login");
    exit();
}
include 'db_connect.php';

$username = $_SESSION['UserName'];
$filter = isset($_GET['status']) ? $_GET['status'] : 'ALL';

// Build query based on filter
$sql = "SELECT * FROM tickets WHERE PIC = ? AND status IN ('ON-GOING','RESOLVED','Cancelled')";
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
    <title>My Tickets</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
        }

        h2 {
            color: #333;
        }

        .ticket-container {
            padding: 20px 24px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
            border-left: 6px solid #95A9E8;
            background: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
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

        .ticket-buttons {
            text-align: right;
            margin-top: 10px;
        }

        .ticket-buttons img {
            width: 1em;
            height: 1em;
            vertical-align: middle;
            object-fit: contain;
        }

        .btn {
            background-color: #95A9E8;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin: 5px 3px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #CAD8FB;
        }

        /* ✅ Scrollable Container */
        .ON-GOING-scrollable-tickets {
            max-height: 600px;
            overflow-y: auto;
            padding-right: 10px;
            border-radius: 10px;
            background: #fff;
        }

        .ON-GOING-scrollable-tickets::-webkit-scrollbar {
            width: 8px;
        }

        .ON-GOING-scrollable-tickets::-webkit-scrollbar-thumb {
            background-color: #bbb;
            border-radius: 10px;
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
        <style>
        .modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
        }

        .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 400px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }

        .modal-content h2 {
        margin-top: 0;
        }

        .modal-content label {
        display: block;
        margin: 10px 0 5px;
        }

        .modal-content input,
        .modal-content select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
        }
    </style>
</head>
<body>

    <h2>Workspace (<?= htmlspecialchars($username) ?>)</h2>

    <div style="margin-bottom: 20px;">
        <?php
        $statuses = ['ALL', 'ON-GOING', 'RESOLVED', 'CANCELLED'];
        foreach ($statuses as $status) {
            $active = ($filter === $status) ? 'style="background-color:#CAD8FB;color:black;"' : '';
            echo "<a href='?status=$status' class='btn' $active>$status</a> ";
        }
        ?>
    </div>

    <div id="ticketTable">
        <div class="ON-GOING-scrollable-tickets">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="ticket-container">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap;">
                        <div class="ticket-ino" style="flex: 1; margin-right: 24px;">
                            <div class="ticket-header">
                                <div class="ticket-title">Ticket #<?= $row['ticket_no'] ?></div>
                            </div>
                            <div class="ticket-detail"><strong>Trouble:</strong> <?= $row['trouble'] ?></div>
                            <div class="ticket-detail"><strong>Requestor:</strong> <?= $row['requestor'] ?></div>
                            <div class="ticket-detail"><strong>IP Address:</strong> <?= $row['ip_address'] ?></div>
                            <div class="ticket-detail"><strong>Status:</strong> <span class="ticket-status"><?= $row['status'] ?></span></div>
                            <div class="ticket-detail"><strong>Timestamp:</strong> <?= $row['timestamp'] ?></div>
                            <div class="ticket-detail"><strong>PIC:</strong> <?= $row['pic'] ?></div>
                        </div>
                        <?= getStatusUI($row['status'], $row['pic']) ?>
                    </div>

                    <div class="ticket-buttons">
                        <button class="btn" onclick='openEditModal(<?= json_encode($row) ?>)' style="background-color:transparent;">
                            <img src="css/img/pencil.png" alt="Edit">
                        </button>
                        <?php if ($row['status'] !== 'Cancelled'): ?>
                            <button class="btn" onclick="cancelTicket(<?= $row['ticket_no'] ?>)" style="background-color:transparent;">
                                <img src="css/img/cancel.png" alt="Cancel">
                            </button>
                        <?php endif; ?>
                        <button class="btn" onclick="openDeleteModal(<?= $row['ticket_no'] ?>)" style="background-color:transparent;">
                            <img src="css/img/delete.png" alt="Delete">
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>



    <!-- Edit Ticket Modal -->
<div id="editModal" class="modal" style="display:none;">
  <div class="modal-content">
    <form action="ticket_submit.php" method="POST">
      <h2>Edit Ticket</h2>
      <input type="hidden" name="ticket_no" id="edit_ticket_no">

      <label>Trouble:</label>
      <input type="text" name="trouble" id="edit_trouble" required>

      <label>Requestor:</label>
      <input type="text" name="requestor" id="edit_requestor" required>

      <label>IP Address:</label>
      <input type="text" name="ip_address" id="edit_ip_address" required>

      <label>Status:</label>
      <select name="status" id="edit_status" required>
        <option value="PENDING">PENDING</option>
        <option value="ON-GOING">ON-GOING</option>
        <option value="RESOLVED">RESOLVED</option>
      </select>

      <div style="margin-top: 10px; text-align: right;">
        <button type="submit" class="btn">Update</button>
        <button type="button" class="btn" onclick="closeModal('editModal')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.hover-gif').forEach(img => {
    const gif = img.getAttribute('data-gif');
    const still = img.getAttribute('data-still');

    img.addEventListener('mouseenter', () => img.src = gif);
    img.addEventListener('mouseleave', () => img.src = still);
});

function openEditModal(data) {
  document.getElementById('edit_ticket_no').value = data.ticket_no;
  document.getElementById('edit_trouble').value = data.trouble;
  document.getElementById('edit_requestor').value = data.requestor;
  document.getElementById('edit_ip_address').value = data.ip_address;
  document.getElementById('edit_status').value = data.status;
  document.getElementById('editModal').style.display = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

window.addEventListener('click', function(e) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (e.target === modal) modal.style.display = 'none';
    });
});

</script>
</body>
</html>
