<?php
include 'auth_check.php';
include 'db_connect.php';

if ($_SESSION['DEPT'] !== 'ISD') {
    echo "Access Denied: You are not authorized to access this page.";
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $workerID = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM login WHERE WorkerID = ?");
    $stmt->bind_param("s", $workerID);
    $stmt->execute();
    header("Location: maintenance.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM login");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Maintenance Panel</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px 36px 28px 36px;
        }
        h2 {
            margin-top: 0;
            color: #007acc;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .user-info {
            margin-bottom: 18px;
            color: #444;
            font-size: 1rem;
        }
        .logout-link {
            color: #007acc;
            text-decoration: none;
            font-weight: 500;
            margin-left: 8px;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        .add-btn {
            display: inline-block;
            background: #007acc;
            color: #fff;
            padding: 9px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 18px;
            transition: background 0.2s;
            font-size: 1rem;
        }
        .add-btn:hover {
            background: #005fa3;
        }
        .search-box {
            width: 100%;
            max-width: 320px;
            margin-bottom: 18px;
            display: block;
        }
        .search-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ccd6dd;
            border-radius: 6px;
            font-size: 1rem;
            background: #f7fafd;
            margin-bottom: 0;
            transition: border 0.2s;
        }
        .search-input:focus {
            border: 1.5px solid #007acc;
            outline: none;
            background: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        th, td {
            padding: 8px 10px;
            text-align: center;
        }
        th {
            background: #007acc;
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background: #f7fafd;
        }
        tr:hover {
            background: #e6f2fb;
        }
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 1rem;
            text-decoration: none;
            margin: 0 2px;
            transition: background 0.15s;
        }
        .edit-btn {
            background: #f0ad4e;
            color: #fff;
        }
        .edit-btn:hover {
            background: #ec971f;
        }
        .delete-btn {
            background: #d9534f;
            color: #fff;
        }
        .delete-btn:hover {
            background: #b52b27;
        }
        @media (max-width: 700px) {
            .container {
                padding: 12px 4px;
            }
            table, th, td {
                font-size: 0.95rem;
            }
            .search-box {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🛠️ User Maintenance Panel</h2>
        <div class="user-info">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['UserName']) ?></strong>
            (<?= htmlspecialchars($_SESSION['DEPT']) ?>)
            | <a class="logout-link" href="logout.php">Logout</a>
        </div>
        <a class="add-btn" href="support.php">← Back</a>
        <a class="add-btn" href="add_user.php">➕ Add New User</a>
        <div class="search-box">
            <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search users... (ID, Name, Dept, etc.)" autocomplete="off">
        </div>
        <table id="userTable">
            <thead>
            <tr>
                <th>Worker ID</th>
                <th>ID Number</th>
                <th>Username</th>
                <th>Password</th>
                <th>UFLAG</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['WorkerID']) ?></td>
                <td><?= htmlspecialchars($row['ID_NO']) ?></td>
                <td><?= htmlspecialchars($row['UserName']) ?></td>
                <td><?= htmlspecialchars($row['Password']) ?></td>
                <td><?= htmlspecialchars($row['UFLAG']) ?></td>
                <td><?= htmlspecialchars($row['DEPT']) ?></td>
                <td>
                    <a class="action-btn edit-btn" href="edit_user.php?id=<?= urlencode($row['WorkerID']) ?>">✏️ Edit</a>
                    <a class="action-btn delete-btn" href="maintenance.php?delete=<?= urlencode($row['WorkerID']) ?>" onclick="return confirm('Are you sure you want to delete this user?')">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        // Live search filter for user table
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#userTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>