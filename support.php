<?php
session_start();
include 'user.php';

$_SESSION['WorkerID'];
$_SESSION['UserName'];
$_SESSION['DEPT'];

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php#login");
    exit;
}
if (strtoupper($_SESSION['DEPT']) !== 'ISD') {
    echo "Access denied.";
    exit();
}

$ticket_no = isset($_GET['ticket_no']) ? $_GET['ticket_no'] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>IS2 Dashboard</title>
    <style>
        body {
            
            font-family: 'Segoe UI', sans-serif;
            margin-top: 2rem;
        }
         @font-face {
            font-family: 'MyFont';
            src: url('fonts/Nexa-ExtraLight.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'MyFonth1';
            src: url('fonts/Nexa-Heavy.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        .navbar {
        position: fixed;          /* Stays on top */
        top: 0;
        left: 0;
        width: 100%;
        padding: 10px 40px;
        display: flex;
        justify-content: space-between;
        z-index: 999;
        }

        .navbar h1{
            color: rgb(82, 111, 165);
            text-decoration: none;
            font-size: 1.5rem;
            transition: color 0.3s ease;
            gap: 20px;
        }
        .navbar .logo {
        font-size: 24px;
        color: #fff;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin-right: 20px;
        }

        /* Default: white text for dark backgrounds */
        .nav-links li a {
            color: rgb(82, 111, 165);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        /* Invert: dark text for light backgrounds */
        .info-section.light .nav-links li a,
        footer .nav-links li a,
        body.light-bg .nav-links li a {
            color: #222;
        }

        .nav-links li a:hover {
            color: #ffd700;
        }

        /* Invert hover for light backgrounds */
        .info-section.light .nav-links li a:hover,
        footer .nav-links li a:hover,
        body.light-bg .nav-links li a:hover {
            color:  #E49B42ff;
        }
        /* Logo & Brand */
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

        .grid-container {
            display: grid;
            grid-template-columns: .7fr 1fr .5fr ;
            gap: 20px;
            padding: 20px;
            min-height: 80vh;
            background-image: url('css/img/bg.jpg');
        }
        .column-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            grid-template-columns: 1fr 1fr 1fr .5fr;
            gap: 20px;;
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
            background:rgb(220, 230, 249);
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
        .icon-btn {
            background:rgba(221, 230, 249, 0);
            border: none;
            padding: 5px;
            border-radius: 6px;
        }
        .icon-btn img {
            width: 28px;
            height: 28px;
        }
        .user-con{
           justify-content: right;
        }
        
        .footer {
            text-align: center;
            padding: .5em 1em;
            font-family: 'MyFont', Nexa-Light;
            font-size: 10px;
            color: #fff;
        }
        
        #stats, #calendar {
    scroll-margin-top: 80px; /* for smooth scroll */
}

       .panel{
        margin-top:50px;
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
        <div class="logo"> <img src="css/img/is2logo2.png"/></div>
        <ul class="nav-links">
            <li><a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="smooth-scroll">Workspce</a></li>
            <li><a href="#stats" >Stats</a></li>
            
            <li><a href="#calendar" class="smooth-scroll">Space</a></li>
        </ul>
    </header>
    <div class="panel" id="panel">
    <div id="dashboard" class="-section"> 
        
    
    <div id="workspace">
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
                <div id="pending-tickets-section">
                    <?php include 'pending_tickets.php'; ?>
                </div>
            </div>
            <div class="column-box main-widgets" style="grid-column: span 2; display: flex; flex-wrap: wrap;">
                <div class="widget1" style="flex: 1 1 300px; min-width: 250px;">
                    <?php include 'ticket_summary.php'; ?>
                    <?php include 'on-going_tickets.php'; ?> 
                </div>
            </div>
            </div>
            </div>
        </div>
    </div>
    <div id="stats"> 
        <div class="grid-container">
            <div class="column-box" style="grid-column: span 2; display: flex; gap: 20px;">
                <?php include 'trouble_chart.php'; ?>
            </div>
            <div class="column-box">
                <div class='widget3' style="margin-bottom: 20px;">
                    
                </div>
                <div class='widget4'>
                    
                </div>
            </div> 
        </div>
    </div>
    <div id="calendar"> 
        <div class="grid-container">
            <div class="column-box" style="grid-column: span 3; display: flex; gap: 20px;justify-content: center;" >
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                    <div>
                        <img src="css/img/under-construction.gif" alt="" style="max-width: 200px; height: auto;">
                    </div>
                    <div>
                        <h2>on-going construction!</h2>
                    </div>
                </div>
        </div>
    </div>   
</div>     
</body>
    <form action="logout.php" method="post" style="display:inline;">
    <button type="submit">Developer Mode End Session</button>
</form>
  <script>
    // Smooth scroll for all anchor links with hashes
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').slice(1);
            const target = document.getElementById(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
  </script>
</html>
