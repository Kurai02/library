<?php
session_start();
require_once "logic/database.php";
login_valid();

$username = $_SESSION['username'];

open_connection();

// Handle form submissions:
$errors = [];
$success = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT UserName, Role, FullName, Email FROM Users WHERE UserName = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_profile'])) {
        // Update FullName and Email
        $fullName = trim($_POST['fullName']);
        $email = trim($_POST['email']);
        
        // Simple validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        
        if (empty($errors)) {
            $updStmt = $conn->prepare("UPDATE Users SET FullName=?, Email=? WHERE UserName=?");
            $updStmt->bind_param("sss", $fullName, $email, $username);
            if ($updStmt->execute()) {
                $success = "Profile updated successfully.";
                // Update local $user array for displaying updated data
                $user['FullName'] = $fullName;
                $user['Email'] = $email;
            } else {
                $errors[] = "Failed to update profile.";
            }
            $updStmt->close();
        }
    }
    elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        } else {
            // Verify current password
            $passStmt = $conn->prepare("SELECT PASSWORD FROM Users WHERE UserName=?");
            $passStmt->bind_param("s", $username);
            $passStmt->execute();
            $passStmt->bind_result($hashed_password);
            $passStmt->fetch();
            $passStmt->close();

            if (!password_verify($current_password, $hashed_password)) {
                $errors[] = "Current password is incorrect.";
            } else {
                // Update password
                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $updatePassStmt = $conn->prepare("UPDATE Users SET PASSWORD=? WHERE UserName=?");
                $updatePassStmt->bind_param("ss", $new_hashed, $username);
                if ($updatePassStmt->execute()) {
                    $success = "Password changed successfully.";
                } else {
                    $errors[] = "Failed to update password.";
                }
                $updatePassStmt->close();
            }
        }
    }
}

close_connection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Profile</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/response_style.css" />
</head>
<body>
  <header><?php require_once "layout/header.php"; ?></header>
  <aside id="sidebar"><?php require_once "layout/sidebar.php"; ?></aside>
  <main>
    <div class="container">
      <h2>Profile: <?php echo htmlspecialchars($user['UserName']); ?></h2>
      <p><strong>Role:</strong> <?php echo htmlspecialchars($user['Role']); ?></p>

      <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="error-message">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <h3>Edit Profile</h3>
      <form method="POST">
        <label for="fullName">Full Name:</label><br />
        <input type="text" name="fullName" id="fullName" value="<?php echo htmlspecialchars($user['FullName']); ?>" /><br />

        <label for="email">Email:</label><br />
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['Email']); ?>" /><br />

        <button type="submit" name="update_profile">Update Profile</button>
      </form>

      <h3>Change Password</h3>
      <form method="POST">
        <label for="current_password">Current Password:</label><br />
        <input type="password" name="current_password" id="current_password" required /><br />

        <label for="new_password">New Password:</label><br />
        <input type="password" name="new_password" id="new_password" required /><br />

        <label for="confirm_password">Confirm New Password:</label><br />
        <input type="password" name="confirm_password" id="confirm_password" required /><br />

        <button type="submit" name="change_password">Change Password</button>
      </form>
    </div>
  </main>
  <footer><?php require_once "layout/footer.php"; ?></footer>
</body>
</html>
