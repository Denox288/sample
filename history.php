<?php
include 'auth_check.php';
include 'db_connect.php';

$result = $conn->query("SELECT * FROM tickets ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket History</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
        }
        h2 { color: #333; }

        .btn {
            background-color: #007acc;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin: 4px;
        }
        .btn:hover { background-color: #005c99; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007acc;
            color: white;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }

        .modal {
            display: none;
            position: fixed;
            z-index: 99;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
        }
        .modal-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .close {
            float: right;
            cursor: pointer;
            color: red;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<h2>📜 Ticket History</h2>

<button class="btn" onclick="openAddModal()">➕ Add Ticket</button>

<table>
    <tr>
        <th>Ticket #</th>
        <th>Trouble</th>
        <!-- <th>TFlag</th> -->
        <th>Requestor</th>
        <th>IP Address</th>
        <th>Status</th>
        <!-- <th>SFlag</th> -->
        <th>Timestamp</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['ticket_no'] ?></td>
        <td><?= $row['trouble'] ?></td>
        <!-- <td>< ?= $row['tflag'] ?></td> !-->
        <td><?= $row['requestor'] ?></td>
        <td><?= $row['ip_address'] ?></td>
        <td>
            <?php
                $color = $row['status'] == 'PENDING' ? 'orange' : 'green';
                echo "<b style='color: $color;'>{$row['status']}</b>";
            ?>
        </td>
        <!-- <td>< ?= $row['sflag'] ?></td> -->
        <td><?= $row['timestamp'] ?></td>
        <td>
            <button class="btn" onclick='openEditModal(<?= json_encode($row) ?>)'>✏️</button>
            <button class="btn" onclick="openDeleteModal(<?= $row['ticket_no'] ?>)">🗑️</button>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Add/Edit Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('editModal')">×</span>
    <div class="modal-header" id="modalTitle">Add Ticket</div>
    <form id="ticketForm" method="post" action="ticket_handler.php">
        <input type="hidden" name="ticket_no" id="ticket_no">
        <label>Trouble:</label><input type="text" name="trouble" id="trouble" required>
        <label>TFlag:</label><input type="number" name="tflag" id="tflag" required>
        <label>Requestor:</label><input type="text" name="requestor" id="requestor" required>
        <label>IP Address:</label><input type="text" name="ip_address" id="ip_address" required>
        <label>Status:</label>
        <select name="status" id="status">
            <option value="PENDING">PENDING</option>
            <option value="RESOLVED">RESOLVED</option>
        </select>
        <label>SFlag:</label><input type="number" name="sflag" id="sflag" required>
        <button type="submit" class="btn">💾 Save</button>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('deleteModal')">×</span>
    <form method="post" action="ticket_handler.php">
        <input type="hidden" name="delete_ticket_no" id="delete_ticket_no">
        <p>Are you sure you want to delete this ticket?</p>
        <button type="submit" class="btn">✅ Yes, Delete</button>
    </form>
  </div>
</div>

<script>
function openAddModal() {
    document.getElementById("modalTitle").innerText = "Add Ticket";
    document.getElementById("ticketForm").reset();
    document.getElementById("ticket_no").value = "";
    document.getElementById("editModal").style.display = "block";
}
function openEditModal(data) {
    document.getElementById("modalTitle").innerText = "Edit Ticket #" + data.ticket_no;
    document.getElementById("ticket_no").value = data.ticket_no;
    document.getElementById("trouble").value = data.trouble;
    document.getElementById("tflag").value = data.tflag;
    document.getElementById("requestor").value = data.requestor;
    document.getElementById("ip_address").value = data.ip_address;
    document.getElementById("status").value = data.status;
    document.getElementById("sflag").value = data.sflag;
    document.getElementById("editModal").style.display = "block";
}
function openDeleteModal(ticket_no) {
    document.getElementById("delete_ticket_no").value = ticket_no;
    document.getElementById("deleteModal").style.display = "block";
}
function closeModal(id) {
    document.getElementById(id).style.display = "none";
}
</script>

</body>
</html>
