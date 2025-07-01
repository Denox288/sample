<?php
include 'db_connect.php';

$workerID = $_GET['id'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_no = $_POST['ID_NO'];
    $username = $_POST['UserName'];
    $password = $_POST['Password'];
    $uflag = $_POST['UFLAG'];
    $dept = $_POST['DEPT'];

    $stmt = $conn->prepare("UPDATE LOGIN SET ID_NO=?, UserName=?, Password=?, UFLAG=?, DEPT=? WHERE WorkerID=?");
    $stmt->bind_param("sssiss", $id_no, $username, $password, $uflag, $dept, $workerID);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM LOGIN WHERE WorkerID = ?");
$stmt->bind_param("s", $workerID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>Edit User</title></head>
<body>
    <h2>Edit User</h2>
    <form method="POST">
        ID_NO: <input type="text" name="ID_NO" value="<?= $user['ID_NO'] ?>" required><br><br>
        UserName: <input type="text" name="UserName" value="<?= $user['UserName'] ?>" required><br><br>
        Password: <input type="text" name="Password" value="<?= $user['Password'] ?>" required><br><br>
        UFLAG: <input type="number" name="UFLAG" value="<?= $user['UFLAG'] ?>" required><br><br>
        DEPT: <input type="text" name="DEPT" value="<?= $user['DEPT'] ?>" required><br><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
