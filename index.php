<?php require_once "layout/p_layout.php"; ?>
<?php 
session_start();
require_once "logic/database.php";
$activeUser = login_valid();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
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
    <div class="top_content">
      <div class="container">
        <h2>Total Stock</h2>
        <?php
          open_connection();
          $result = $conn->query("SELECT SUM(Quantity) as total_books FROM Book");
          $totalBooks = $result ? $result->fetch_assoc()['total_books'] : 0;
          close_connection();
          echo "<p><strong>Total Books in Stock:</strong> " . ($totalBooks ?: 0) . "</p>";
        ?>
      </div>

      <div class="container">
        <h2>Status</h2>
        <?php
          open_connection();
          $userCount = $conn->query("SELECT COUNT(*) as cnt FROM Users")->fetch_assoc()['cnt'];
          $loanCount = $conn->query("SELECT COUNT(*) as cnt FROM Lending")->fetch_assoc()['cnt'];
          $overdueCount = $conn->query("SELECT COUNT(*) as cnt FROM Lending WHERE ReturnDate IS NULL AND DueDate < CURDATE()")->fetch_assoc()['cnt'];
          close_connection();
          echo "<p><strong>Total Users:</strong> $userCount</p>";
          echo "<p><strong>Books Lent Out:</strong> $loanCount</p>";
          echo "<p><strong>Overdue Books:</strong> $overdueCount</p>";
        ?>
      </div>
    </div>

    <div class="container">
      <h2>Recent Activity</h2>
      <table class="table">
        <tr>
          <th>Date/Time</th>
          <th>User</th>
          <th>Action</th>
        </tr>
        <?php
          open_connection();
          $sql = "
            SELECT l.LendDate, u.UserName, b.Title
            FROM Lending l
            JOIN Users u ON l.UserId = u.UserId
            JOIN Book b ON l.BookId = b.BookId
            ORDER BY l.LendDate DESC
            LIMIT 5";
          $result = $conn->query($sql);
          close_connection();

          if ($result && $result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['LendDate']}</td>
                          <td>{$row['UserName']}</td>
                          <td>Borrowed <em>{$row['Title']}</em></td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='3'>No recent activity</td></tr>";
          }
        ?>
      </table>
    </div>
  </main>

  <footer id="footer">
    <?php require_once "layout/footer.php"; ?>
  </footer>
</body>
</html>
