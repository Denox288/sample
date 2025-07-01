<?php
include 'db_connect.php';
include 'ticket_handler.php';

$_SESSION['WorkerID'];
$_SESSION['UserName'];
$_SESSION['DEPT'];

$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

$search_sql = '';
if ($search !== '') {
    $search_sql = "AND (
        ticket_no LIKE '%$search%' OR
        trouble LIKE '%$search%' OR
        requestor LIKE '%$search%' OR
        ip_address LIKE '%$search%' OR
        timestamp LIKE '%$search%'
    )";
}

$result = fetch_all_pending_tickets($conn, $search_sql);
$ticket_no = isset($_GET['ticket_no']) ? $_GET['ticket_no'] : null;

?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
            color: black;
        }
        h2 { color: #333; }

        .btn {
            background-color: #95A9E8ff;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin: 4px;
        }
        .btn:hover { background-color: #CAD8FBff; }

           .ticket-container {
            padding: 20px 24px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
            border-left: 6px solid #95A9E8;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .ticket-header {
            justify-content: space-between;
            align-items: center;
        }

        .ticket-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .ticket-detail {
            color: #555;
        }

        .search-container {
            display: flex;
            flex-direction: row;
            gap: 10px;
            align-items: center;
            margin-bottom: 18px;
        }

        .search-input {
          border-radius: 6px;
          border: 1px solid #ccc;
          width: 75%;
	        padding: 9px;
	        margin: 4px;
        }

        .search-button {
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .search-button:hover {
            background-color: #CAD8FBff;
        }

        .page-link {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 3px;
            border: 1px solid #ccc;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }
        .page-link:hover {
            background-color: #eee;
        }
        .page-link.active {
            background-color: #E49B42ff;
            color: white;
            border-color: #E49B42ff;
        }
        .scrollable-tickets {
            max-height: 563px;
            overflow-y: auto;
            padding-right: 10px;
            border-radius: 10px;
        }
        @media (max-width: 500px) {
                #addModal .modal-content {
                  width: 95vw !important;
                  min-width: unset !important;
                  padding: 10px !important;
                }
                #addModal img {
                  width: 100% !important;
                  max-width: 100% !important;
                }
              }
    </style>
</head>
<body>
<div style="display: flex; flex-direction: column; align-items: center;">
    <h2>Public Tray</h2>
    <div class="search-container" style="justify-content: center; width: 100%;">
        <form method="get" action="#ticketTable" class="form" style="display: flex; justify-content: center; width: 100%;">
            <input class="search-input" type="text" name="search" placeholder="Search tickets..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn" type="submit" style="flex:1; min-width:90px; max-width:180px;">Search</button>
            <button class="btn" type="button" style="flex:1; min-width:90px; max-width:180px;" onclick="openAddModal()">Add</button>
        </form>
    </div>
</div>
<div id="ticketTable">
    <div class="scrollable-tickets">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="ticket-container" style="height: 100px; width: 85%;">
                <div class="ticket-header">
                    <div class="ticket-title">Ticket #<?= $row['ticket_no'] ?></div>
                </div>
                  <div class="pt-con"  style=" display: flex; align-items:center; space-between;">
                      <div class="ticket-detail" style="font-size: 8px;"><h2 style="display:inline;"><?= $row['trouble'] ?></h2></div>
                  </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<div id="addModal" class="modal" style="display:none;">
  <div class="modal-content">
    <h2>REQUEST FORM</h2>

    <!-- Tab Navigation -->
    <div class="tab-nav">
      <button type="button" class="tab-btn active" onclick="showTab(0)">R1</button>
      <button type="button" class="tab-btn" onclick="showTab(1)">R2</button>
      <button type="button" class="tab-btn" onclick="showTab(2)">R3</button>
    </div>

    <!-- Tabs Wrapper -->
    <div class="tabs-wrapper">
      <!-- TAB 1 -->
       
      <div class="tab-content" style="display: block;">
        <h3>SYSTEM ERRORS</h3>
        <form action="ticket_submit.php" method="POST">
          <label>Trouble:</label>
          <input type="text" name="trouble" required>

          <label>Requestor:</label>
          <input type="text" name="requestor" required>

          <label>IP Address:</label>
          <input type="text" name="ip_address" required>

          <label>Status:</label>
          <select name="status" required>
            <option value="PENDING" selected>PENDING</option>
            <option value="ON-GOING">ON-GOING</option>
            <option value="RESOLVED">RESOLVED</option>
          </select>

          <button type="submit" class="btn">Save</button>
          <button type="button" class="btn" onclick="closeModal('addModal')">Cancel</button>
        </form>
      </div>

      <!-- TAB 2 -->
      <div class="tab-content">
        <form action="ticket_submit.php" method="POST">
            <h3>HSA/DE RECOVERIES</h3>
              <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; min-height: 310px; overflow-y: auto;">
                <img src="css/img/under-construction.gif" alt="" 
                   style="max-width: 30%; height: auto; width: 200px; margin-bottom: 10px; box-sizing: border-box;">
                <h2 style="font-size: 1.2em; text-align: center; margin: 0;">on-going construction!</h2>
              </div>
              <button type="submit" class="btn">Save</button>
              <button type="button" class="btn" onclick="closeModal('addModal')">Cancel</button>
        </form>
      </div>

      <!-- TAB 3 -->
      <div class="tab-content">
        <form action="ticket_submit.php" method="POST">
            <h3>APPLICATION INSTALLATION</h3>
              <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; min-height: 310px; overflow-y: auto;">
                <img src="css/img/under-construction.gif" alt="" 
                   style="max-width: 30%; height: auto; width: 200px; margin-bottom: 10px; box-sizing: border-box;">
                <h2 style="font-size: 1.2em; text-align: center; margin: 0;">on-going construction!</h2>
              </div>
              <button type="submit" class="btn">Save</button>
              <button type="button" class="btn" onclick="closeModal('addModal')">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- STYLES -->
<style>
  .tab-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
  }

  .tab-btn {
    padding: 8px 14px;
    background: #ccc;
    border: none;
    cursor: pointer;
    border-radius: 6px;
  }

  .tab-btn.active {
    background: #95A9E8;
    color: white;
  }

  .tab-content {
    display: none;
  }

  .modal-content label {
    display: block;
    margin: 8px 0 4px;
  }

  .modal-content input, .modal-content select {
    width: 100%;
    padding: 8px;
    margin-bottom: 12px;
  }

  .btn {
    padding: 8px 14px;
    background-color: #95A9E8;
    border: none;
    color: white;
    margin-right: 10px;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn:hover {
    background-color: #738cc0;
  }
</style>

<!-- SCRIPT -->
<script>
  function showTab(index) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');

    tabs.forEach((tab, i) => {
      tab.style.display = i === index ? 'block' : 'none';
      buttons[i].classList.toggle('active', i === index);
    });
  }

  function closeModal(id) {
    document.getElementById(id).style.display = 'none';
  }
</script>

<!-- Edit Ticket Modal -->
<div id="editModal" class="modal" style="display:none;">
  <div class="modal-content">
    <form action="ticket_submit.php" method="POST">
      <h2>Ticket</h2>
      <input type="hidden" name="ticket_no" id="edit_ticket_no">
      <label>Trouble:</label>
      <input type="text" name="trouble" id="edit_trouble" required>
      <label>Type Flag:</label>
      <input type="number" name="tflag" id="edit_tflag" required>
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
      <label>Status Flag:</label>
      <input type="number" name="sflag" id="edit_sflag" required>
      <button type="submit" class="btn">Update</button>
      <button type="button" class="btn" onclick="closeModal('editModal')">Cancel</button>
    </form>
  </div>
</div>

<!-- Delete Ticket Modal -->
<div id="deleteModal" class="modal" style="display:none;">
  <div class="modal-content">
    <form action="ticket_submit.php" method="POST">
      <h2>Are you sure you want to delete this ticket?</h2>
      <input type="hidden" name="delete_ticket_no" id="delete_ticket_no">
      <button type="submit" class="btn">Yes, Delete</button>
      <button type="button" class="btn" onclick="closeModal('deleteModal')">Cancel</button>
    </form>
  </div>
</div>

<!-- Modal Styling -->
<style>
.modal {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
}
.modal-content h2 {
  margin-top: 0;
}
.modal-content label {
  display: block;
  margin-top: 10px;
}
.modal-content input, select{
  width: 100%;
  padding: 6px;
  margin-top: 4px;
  box-sizing: border-box;
}
</style>

<!-- Modal JS Logic -->
<script>
function openAddModal() {
  document.getElementById('addModal').style.display = 'flex';
}
function openEditModal(data) {
  document.getElementById('edit_ticket_no').value = data.ticket_no;
  document.getElementById('edit_trouble').value = data.trouble;
  document.getElementById('edit_tflag').value = data.tflag;
  document.getElementById('edit_requestor').value = data.requestor;
  document.getElementById('edit_ip_address').value = data.ip_address;
  document.getElementById('edit_status').value = data.status;
  document.getElementById('edit_sflag').value = data.sflag;
  document.getElementById('editModal').style.display = 'flex';
}
function openDeleteModal(ticketNo) {
  document.getElementById('delete_ticket_no').value = ticketNo;
  document.getElementById('deleteModal').style.display = 'flex';
}
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

window.addEventListener('click', function(e) {
    document.querySelectorAll('.modal').forEach(modal => {
        if (e.target === modal) modal.style.display = 'none';
    });
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
    }
});
function openEditModal(data) {
  document.getElementById('edit_ticket_no').value = data.ticket_no;
  document.getElementById('edit_trouble').value = data.trouble;
  document.getElementById('edit_tflag').value = data.tflag;
  document.getElementById('edit_requestor').value = data.requestor;
  document.getElementById('edit_ip_address').value = data.ip_address;
  document.getElementById('edit_status').value = data.status; // this line works for select too
  document.getElementById('edit_sflag').value = data.sflag;
  document.getElementById('editModal').style.display = 'flex';
}
function setToONGOING(ticketNo) {
    if (confirm("Are you sure you want to set this ticket to ON-GOING?")) {
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'ticket_no=' + encodeURIComponent(ticketNo) + '&status=ON-GOING'
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === 'success') {
                alert("Status updated to ON-GOING.");
                location.reload(); // refresh to show changes
            } else {
                alert("Failed to update status.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred.");
        });
    }
}
function cancelTicket(ticketNo) {
            if (!confirm("Are you sure you want to cancel this ticket?")) return;

            fetch("cancel_ticket.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "ticket_no=" + ticketNo
            })
            .then(res => res.text())
            .then(response => {
                alert(response);
                location.reload(); // Refresh to reflect changes
            })
            .catch(error => {
                console.error("Error cancelling ticket:", error);
                alert("Failed to cancel the ticket.");
            });
        }
        function cancelTicket(ticketNo) {
            if (!confirm("Are you sure you want to cancel this ticket?")) return;

            fetch("cancel_ticket.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "ticket_no=" + ticketNo
            })
            .then(res => res.text())
            .then(response => {
                alert(response);
                location.reload(); // Refresh to reflect changes
            })
            .catch(error => {
                console.error("Error cancelling ticket:", error);
                alert("Failed to cancel the ticket.");
            });
        }
    </script>
</body>
</html>
