<?php
include 'db_connect.php';

// add_user.php
$errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workerID = strtoupper($_POST['WorkerID']);
    $id_no = strtoupper($_POST['ID_NO']);
    $username = strtoupper($_POST['UserName']);
    $password = strtoupper($_POST['Password']);
    $uflag = strtoupper($_POST['UFLAG']);
    $dept = strtoupper($_POST['DEPT']);
    $dateCreated = date('Y-m-d H:i:s'); // Current timestamp

    $stmt = $conn->prepare("INSERT INTO login (WorkerID, ID_NO, UserName, Password, UFLAG, DEPT, DateCreated) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $workerID, $id_no, $username, $password, $uflag, $dept, $dateCreated);

    try {
        $stmt->execute();

        // --- PHPMailer logic starts here ---
        require_once("class.phpmailer.php");
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host     = "10.193.8.192"; // Change if needed
        $mail->FromName = "User Management System";
        $mail->From     = "critsystem@toshiba.co.jp"; // Change to your sender email

        // TODO: Change this to the right recipient(s)
        $mail->AddAddress("kierwin1.ramos@toshiba.co.jp"); // <-- Set the correct recipient

        $mail->Subject  = "New User Added: $username";
        $body = "<html>";
        $body .= "<head>";
        $body .= '<style type="text/css">
            body { font: 12px Verdana, Arial, Helvetica, sans-serif; background-color: #FFFFFF; margin: 0px; padding: 20px 10px 0px; }
            table, th, td { border: 1px solid black; font-family: arial, sans-serif; padding: 2px }
            table { width: 100%; border-collapse: collapse; }
            th { font-weight: bold; text-align: left; background-color: yellow; }
        </style>';
        $body .= "</head>";
        $body .= "<body>";
        $body .= "<strong><p>Sir/Ma'am</p></strong>";
        $body .= "<p>A new user has been added to the system:</p>";
        $body .= "<table>";
        $body .= "<tr><th>Worker ID</th><td>$workerID</td></tr>";
        $body .= "<tr><th>ID Number</th><td>$id_no</td></tr>";
        $body .= "<tr><th>Username</th><td>$username</td></tr>";
        $body .= "<tr><th>Password</th><td>$password</td></tr>";
        $body .= "<tr><th>UFLAG</th><td>$uflag</td></tr>";
        $body .= "<tr><th>Department</th><td>$dept</td></tr>";
        $body .= "<tr><th>Date Created</th><td>$dateCreated</td></tr>";
        $body .= "</table>";
        $body .= "<p><i>This is an auto-generated email. Please do not reply.</i></p>";
        $body .= "<p>Thank you!</p>";
        $body .= "</body></html>";

        $mail->MsgHTML($body);
        $mail->IsHTML(true);
        $mail->WordWrap = 50;

        // Optional: Add CC/BCC if needed
        // $mail->AddCC("cc@yourdomain.com");
        // $mail->AddBCC("bcc@yourdomain.com");

        // Send the email (do not stop on error, just log)
        if(!$mail->Send()) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
        }
        // --- PHPMailer logic ends here ---

        header("Location: maintenance.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code
            $errorMsg = "A user with the same ID NUMBER already exists in the database.";
        } else {
            $errorMsg = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 420px;
            margin: 48px auto;
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
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        label {
            font-weight: 500;
            margin-bottom: 4px;
            color: #333;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ccd6dd;
            border-radius: 6px;
            font-size: 1rem;
            background: #f7fafd;
            transition: border 0.2s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border: 1.5px solid #007acc;
            outline: none;
            background: #fff;
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .submit-btn {
            background: #007acc;
            color: #fff;
            padding: 10px 22px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .submit-btn:hover {
            background: #005fa3;
        }
        .back-btn {
            background: #e0e0e0;
            color: #333;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
            display: inline-block;
        }
        .back-btn:hover {
            background: #bdbdbd;
        }
        @media (max-width: 600px) {
            .container {
                padding: 14px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>➕ Add New User</h2>
        <?php if (!empty($errorMsg)): ?>
            <div style="color: #d32f2f; background: #ffeaea; padding: 10px 16px; border-radius: 6px; margin-bottom: 18px; text-align:center;">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div>
                <label for="workerid">Worker ID</label>
                <input type="text" id="workerid" name="WorkerID" required style="text-transform:uppercase">
            </div>
            <div>
                <label for="id_no">ID Number</label>
                <input type="text" id="id_no" name="ID_NO" required style="text-transform:uppercase">
            </div>
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="UserName" required style="text-transform:uppercase">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="text" id="password" name="Password" required style="text-transform:uppercase">
            </div>
            <div>
                <label for="uflag">UFLAG</label>
                <input type="number" id="uflag" name="UFLAG" required style="text-transform:uppercase">
            </div>
            <div>
                <label for="dept">Department</label>
                <input type="text" id="dept" name="DEPT" required style="text-transform:uppercase">
            </div>
            <div class="form-actions">
                <a class="back-btn" href="maintenance.php">← Back</a>
                <input class="submit-btn" type="submit" value="Add User">
            </div>
        </form>
    </div>
</body>
</html>