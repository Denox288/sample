<?php
include 'user.php';

$_SESSION['WorkerID'];
$_SESSION['UserName'];
$_SESSION['DEPT'];

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php#login");
    exit;
}

$ticket_no = isset($_GET['ticket_no']) ? $_GET['ticket_no'] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            padding: 20px;
            min-height: 80vh;
            background-image: url('css/img/bg.jpg');
            margin-top: 60px;
        }

        .column-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
        }

        iframe {
            width: 100%;
            height: 90vh;
            border: none;
        }

        header {
            position: fixed;
            top: 10px;
            left: 10px;
            right: 10px;
            background: rgb(220, 230, 249);
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            height: 60px;
            border-radius: 80px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 10px 40px;
            display: flex;
            justify-content: space-between;
            z-index: 999;
        }

        .navbar h1 {
            color: rgb(82, 111, 165);
            font-size: 1.5rem;
            gap: 20px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin-right: 20px;
        }

        .nav-links li a {
            color: rgb(82, 111, 165);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links li a:hover {
            color: #ffd700;
        }

        .logo {
            display: flex;
        }

        .logo img {
            max-width: 15%;
        }

        .brand {
            font-size: 18px;
            font-weight: bold;
            color: #ff381e;
            font-family: 'MyFonth1', Nexa-Heavy;
        }

        .icon-btn {
            background: rgba(221, 230, 249, 0);
            border: none;
            padding: 5px;
            border-radius: 6px;
        }

        .icon-btn img {
            width: 28px;
            height: 28px;
        }

        .user-con {
            justify-content: right;
        }

        .footer {
            text-align: center;
            padding: .5em 1em;
            font-family: 'MyFont', Nexa-Light;
            font-size: 10px;
            color: #fff;
        }

        /* Responsive styles */
        @media (max-width: 1200px) {
            .grid-container {
                gap: 16px;
            }
        }

        @media (max-width: 800px) {
            .grid-container {
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 10px;
                margin-top: 2rem;
            }

            header {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }
        }

        @media (max-width: 600px) {
            .column-box {
                padding: 10px;
            }

            .icon-btn img {
                width: 22px;
                height: 22px;
            }
        }
        
       @media (max-width: 1100px) {
                .grid-container {
                    grid-template-columns: 1fr;
                    gap: 10px;
                    padding: 10px;
                }
                .column-box,
                .tickets-list,
                .main-widgets {
                    grid-column: span 1 !important;
                    width: 100%;
                    min-width: 0;
                }
                .main-widgets {
                    flex-direction: column !important;
                }
                .widget1, .widget2 {
                    min-width: 0 !important;
                    width: 100% !important;
                }
            }
            @media (max-width: 700px) {
                .grid-container {
                    padding: 5px;
                }
                .column-box {
                    padding: 10px;
                }
            }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="css/img/is2logo2.png" />
    </div>
    <ul class="nav-links">
        <li><a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">Workspace</a></li>
        <li><a href="#stats">Stats</a></li>
        <li><a href="#calendar">Space</a></li>
    </ul>
</header>

<div class="grid-container">
    <!-- LEFT: TICKETS LIST -->
    <div class="column-box tickets-list" style="grid-column: span 1; grid-row: span 1;">
                <div class="user-con" style="display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 10px;">
                    <?php include 'user_support_profile.php'; ?>    
                        <form action="maintenance.php" method="get" style="display:inline;">
                            <button type="submit" class="icon-btn" title="Maintenance">
                                <img src="css/img/user.png" alt="Maintenance">
                            </button>
                        </form>
                        <form action="logout.php" method="post" style="display:inline;">
                            <button type="submit" class="icon-btn" title="Logout">
                                <img src="css/img/logout.png" alt="Logout">
                            </button>
                        </form>
                        
                    </div>
                <div class="column-box" style="grid-column: span 1;" id="ticketList">
                    <?php include 'common_tickets.php'; ?>
                </div>
            </div>
    <!-- MIDDLE: USER TICKETS -->
    <div class="column-box" style="grid-column: span 2;">
        <?php include 'common_user_tickets.php'; ?>
    </div>

    <!-- STATS SECTION -->
    <div class="column-box" style="grid-column: span 3;" id="stats">
        <?php include 'user_timeline_chart.php'; ?>
    </div>
</div>
</body>
</html>
