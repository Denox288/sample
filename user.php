<?php
// user.php - Session management and user info

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php#login"); // or your actual login page
    exit();
}

// Optional: access user session data globally
$loggedInUser = [
    'UserName'  => $_SESSION['UserName'] ?? '',
    'WorkerID'  => $_SESSION['WorkerID'] ?? '',
    'DEPT'      => $_SESSION['DEPT'] ?? ''
];

