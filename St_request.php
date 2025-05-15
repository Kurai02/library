<?php
require_once "logic/database.php";
session_start();

$activeUser = login_valid();
if (!$activeUser || $activeUser['Role'] != 'student') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$itemId = intval($_GET['id']);

open_connection();

// Check if item exists
$stmt = $conn->prepare("SELECT ProductName FROM product WHERE ProductID = ?");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert request logic here (simplified)
    $userId = $activeUser['UserID'];
    $stmt = $conn->prepare("INSERT INTO requests (UserID, ProductID, RequestDate) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userId, $itemId);
    $stmt->execute();

    $message = "Request sent for item: " . htmlspecialchars($item['ProductName']);
}

close_connection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Request Item</title>
<link rel="stylesheet" href="style/style.css" />
</head>
<body>
<header>
  <?php require_once "layout/header.php"; ?>
</header>
<main>
  <div class="container">
    <h2>Request Item</h2>
    <p>You are requesting: <strong><?= htmlspecialchars($item['ProductName']) ?></strong></p>

    <?php if (isset($message)) {
      echo "<p style='color:green;'>$message</p>";
    } ?>

    <form method="POST" action="">
      <button type="submit">Confirm Request</button>
      <a href="student_dashboard.php">Cancel</a>
    </form>
  </div>
</main>
</body>
</html>
