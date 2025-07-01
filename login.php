<?php
session_start();
$error = $_SESSION['login_error'] ?? "";
unset($_SESSION['login_error']); // Clear error after showing
?>
<link rel="stylesheet" href="css/login.css" />
<link rel="stylesheet" href="css/style.css" />

<div class="login-section">
    <div class="login-art">
        <img src="css/img/hdd.png" alt="Login Art"/>
    </div>
    <div class="login-tab">
        <div class="login-con">
            <form method="POST" action="login_handler.php">
                <h2>Welcome to Cleanroom Ticketing System</h2>
                <div class="form-group">
                    <label for="id_no">ID Number</label>
                    <input type="text" id="id_no" name="id_no" required autocomplete="username" placeholder="Enter your ID Number">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your Password">
                </div>
                <?php
                if (!empty($error)) {
                    echo "<div style='color:red; font-size:12px; text-align:center;'>$error</div>";
                    echo "<script>window.onload = function() { document.getElementById('id_no').focus(); };</script>";
                }
                ?>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</div>
