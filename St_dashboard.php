<?php
require_once "layout/p_layout.php";
session_start();
require_once "logic/database.php";

$activeUser = login_valid();
if (!$activeUser || $activeUser['Role'] != 'student') {
    header("Location: login.php");
    exit;
}

// Fetch all available items for student view (e.g., books/products)
open_connection();
$sql = "SELECT p.ProductID, p.ProductName, p.Type, p.Cost, s.Sup_Name FROM product p LEFT JOIN suplier s ON p.Sup_Id = s.Sup_Id";
$result = $conn->query($sql);

$items = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

close_connection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Student Portal - Dashboard</title>
<link rel="stylesheet" href="style/style.css" />
<link rel="stylesheet" href="style/response_style.css" />
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
    <h2>Welcome, <?= htmlspecialchars($activeUser['UserName']); ?></h2>
    <p>Browse available items below:</p>

    <form method="GET" action="student_dashboard.php">
      <input type="text" name="search" placeholder="Search items..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button type="submit">Search</button>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th>Item Name</th>
          <th>Type</th>
          <th>Cost</th>
          <th>Supplier</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        foreach ($items as $item) {
          if ($search && stripos($item['ProductName'], $search) === false && stripos($item['Type'], $search) === false) {
            continue;
          }
          echo "<tr>
                <td>" . htmlspecialchars($item['ProductName']) . "</td>
                <td>" . htmlspecialchars($item['Type']) . "</td>
                <td>" . number_format($item['Cost'], 2) . "</td>
                <td>" . htmlspecialchars($item['Sup_Name']) . "</td>
                <td><a href='request_item.php?id=" . intval($item['ProductID']) . "'>Request</a></td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
