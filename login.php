<?php 
require_once "logic/database.php";

if (isset($_REQUEST['signout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$login_failure = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = trim($_POST['uname']);
    $psw = $_POST['psw'];

    if (check_cred($uname, $psw)) {
        $_SESSION['username'] = $uname;
        header('Location: index.php');
        exit;
    } else {
        $login_failure = "Login failed! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/response_style.css" />
  <script type="text/javascript" src="logic/app.js" defer></script>
</head>
<body>

  <div id="login_page">
    <div id="login_container">
      <h2>Login</h2>
      <?php if ($login_failure): ?>
        <h4 style="color:red;"><?php echo htmlspecialchars($login_failure); ?></h4>
      <?php endif; ?>
    </div>
    <div id="form_container">
      <form action="login.php" method="post">
        <label for="uname"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" id="uname" required /><br />

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" id="psw" required /><br />

        <button type="submit">Login</button>
        <label>
          <input type="checkbox" checked="checked" name="remember" /> Remember me
        </label><br />
        <span class="psw">Forgot <a href="#">password?</a></span>
      </form>
    </div>
  </div>
</body>
</html>
