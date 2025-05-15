<?php require_once "layout/p_layout.php"; ?>
<?php 
session_start();
require_once "logic/database.php";
$activeUser = login_valid();

// Fetch all books
open_connection();
$sql = "SELECT BookID, Title, Author, ISBN FROM Books ORDER BY Title";
$books = select_rows($sql);
close_connection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Update Books</title>
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
    <div>
      <a href="add_book.php"><h2>Add New Book</h2></a>
    </div>
    <div>
      <h2>Edit Books</h2>
      <?php if (count($books) > 0): ?>
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>ISBN</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($books as $book): ?>
              <tr>
                <td><?= htmlspecialchars($book['Title']) ?></td>
                <td><?= htmlspecialchars($book['Author']) ?></td>
                <td><?= htmlspecialchars($book['ISBN']) ?></td>
                <td><a href="edit_book.php?BookID=<?= intval($book['BookID']) ?>">Edit</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No books found. Please add some.</p>
      <?php endif; ?>
    </div>
  </div>
</main>
</body>
</html>
