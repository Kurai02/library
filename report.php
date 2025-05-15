<?php
require_once "layout/p_layout.php";
session_start();
require_once "logic/database.php";
$activeUser = login_valid();

// Fetch lending statistics
open_connection();

// Total books lent
$totalLentResult = $conn->query("SELECT COUNT(*) as total_lent FROM Lending WHERE ReturnDate IS NULL");
$totalLent = $totalLentResult->fetch_assoc()['total_lent'] ?? 0;

// Top 5 borrowed books (by number of times lent)
$topBooksResult = $conn->query("
    SELECT b.BookID, b.Title, COUNT(l.LendingID) AS times_lent 
    FROM Books b
    LEFT JOIN Lending l ON b.BookID = l.BookID
    GROUP BY b.BookID
    ORDER BY times_lent DESC
    LIMIT 5
");

// Monthly lending trends (last 6 months)
$trendsResult = $conn->query("
    SELECT DATE_FORMAT(LendDate, '%Y-%m') as month, COUNT(*) as lends_count
    FROM Lending
    WHERE LendDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month ASC
");

close_connection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Report</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/response_style.css" />
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
    <h2>Library Lending Report</h2>
    
    <section>
      <h3>Total Books Lent (Currently Borrowed)</h3>
      <p><?= intval($totalLent) ?></p>
    </section>

    <section>
      <h3>Top 5 Borrowed Books</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Book Title</th>
            <th>Times Borrowed</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if ($topBooksResult->num_rows > 0) {
            while($row = $topBooksResult->fetch_assoc()) {
              echo "<tr><td>" . htmlspecialchars($row['Title']) . "</td><td>" . intval($row['times_lent']) . "</td></tr>";
            }
          } else {
            echo "<tr><td colspan='2'>No lending data found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section>
      <h3>Lending Trends (Last 6 Months)</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Month</th>
            <th>Books Lent</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if ($trendsResult->num_rows > 0) {
            while($row = $trendsResult->fetch_assoc()) {
              echo "<tr><td>" . htmlspecialchars($row['month']) . "</td><td>" . intval($row['lends_count']) . "</td></tr>";
            }
          } else {
            echo "<tr><td colspan='2'>No recent lending activity.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
  </div>
</main>
</body>
</html>
