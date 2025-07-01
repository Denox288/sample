<?php
session_start();
include 'db_connect.php'; // Make sure this connects $conn (MySQLi)

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $id_no = $_POST['id_no'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM login WHERE ID_NO = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $id_no, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['UserName'] = $row['UserName'];
        $_SESSION['WorkerID'] = $row['WorkerID']; // still store for internal use
        $_SESSION['DEPT'] = $row['DEPT'];

        // Redirect based on department
        switch (strtoupper($row['DEPT'])) {
            case 'ISD':
                header("Location: support.php");
                break;
            default:
                header("Location: common.php");
                break;
        }
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid ID Number or Password.";
        header("Location: index.php");
        exit();
    }
}
?>
