<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
    <nav class="navbar">
      <div class="logo"> <img src="css/img/is2logo2.png"/></div>
      <ul class="nav-links">
          <li><a href="#home" class="smooth-scroll">Home</a></li>
          <li><a href="#login" class="smooth-scroll">Login</a></li>
          <li><a href="#team" class="smooth-scroll">Contact</a></li>
      </ul>
  </nav>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ticketing System</title>
  <link rel="stylesheet" href="css/style.css" />
  
</head>
<body>
  <header class="parallax"  id="home">
    <div class="hero-text">
      <h1>ISD2 CLEANROOM</h1>
      <p>Systems Trouble Support</p>
      <a href="#login" class="btn" style="padding: 10px 30px 10px 30px; margin: 10px;">Login</a>
    </div>
    </header>
<!-- Initial Page landing !-->

    <section class="login-section" id="login">
        <div class="container login-container">
          <div class="login-tab">
              <?php include "login.php"; ?>
          </div>
        </div>
    </section>
    <section class="team-section" id="team">
        <?php include "team.php"; ?>
    </section>

<!-- dashboard section 
     <section class="dashboard-section" id="dash">
        < ?php include "support.php"; ?>
    </section>
!-->

    <footer class="footer">
        <h3>© 2025 TOSHIBA INFORMATION EQUIPMENT PHILIPPINES INC. All rights reserved.</h3>
        <h3>Kierwin Ramos</h3>
    </footer>
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
</body>
</html>
