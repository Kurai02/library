<?php require_once"layout/p_layout.php"?>
<?php session_start();
  require_once"logic/database.php";
  $activeUser = login_valid();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>purchase</title>
  <link rel="stylesheet" href="style/style.css">
  <link rel="stylesheet" href="style/response_style.css">
  <script type="text/javascript" src="logic/app.js" defer></script>
</head>
<body>
<header>
    <?php
      require_once"layout/header.php";
    ?>
  </header>
  <aside id="sidebar">
    <?php 
      require_once"layout/sidebar.php";
    ?>
  </aside>
  <main>
    <div class="container">
      <h2>Calendar</h2>
      <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Veritatis porro iure quaerat aliquam! Optio dolorum in eum provident, facilis error repellendus excepturi enim dolor deleniti adipisci consectetur doloremque, unde maiores odit sapiente. Atque ab necessitatibus laboriosam consequatur eius similique, ex dolorum eum eaque sequi id veritatis voluptates perspiciatis, cupiditate pariatur.</p>
    </div>
  </main>
</body>
</html>