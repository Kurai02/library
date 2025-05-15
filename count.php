<?php require_once "layout/p_layout.php"; ?>
<?php 
session_start();
require_once "logic/database.php";
$activeUser = login_valid(); // returns the username
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link rel="stylesheet" href="style/style.css">
  <link rel="stylesheet" href="style/response_style.css">
  <script type="text/javascript" src="logic/app.js" defer></script>
</head>
<body>
<header>
  <?php require_once "layout/header.php"; ?>
</header>

<aside id="sidebar">
  <?php require_once "layout/sidebar.php"; ?>
</aside>

<main>
  <div class="container">
    <h2>User Profile</h2>
    <?php
      open_connection();
      $escapedUser = mysqli_real_escape_string($conn, $activeUser);
      $sql = "SELECT UserId, UserName, Role FROM Users WHERE UserName = '$escapedUser'";
      $result = $conn->query($sql);
      close_connection();

      if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo "<p><strong>User ID:</strong> {$user['UserId']}</p>";
        echo "<p><strong>Username:</strong> {$user['UserName']}</p>";
        echo "<p><strong>Role:</strong> {$user['Role']}</p>";
      } else {
        echo "<p>User not found.</p>";
      }
    ?>
  </div>
</main>
</body>
</html>
