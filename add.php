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
  <title>Add New Book</title>
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
      <h2>Add New Book</h2>
      <div id="form_container">
        <form action="book_add.php" method="post">
          <label for="isbn"><b>ISBN</b></label>
          <input type="text" placeholder="Enter ISBN" name="isbn" required> <br>

          <label for="title"><b>Title</b></label>
          <input type="text" placeholder="Enter Title" name="title" required> <br>

          <label for="author"><b>Author</b></label>
          <input type="text" placeholder="Enter Author" name="author"> <br>

          <label for="publisher"><b>Publisher</b></label>
          <input type="text" name="publisher"> <br>

          <label for="year"><b>Year Published</b></label>
          <input type="number" name="year"> <br>

          <label for="genre"><b>Genre</b></label>
          <input type="text" name="genre"> <br>

          <label for="quantity"><b>Quantity</b></label>
          <input type="number" name="quantity" min="0"> <br>

          <label for="supplier"><b>Supplier</b></label>
          <select name="supplier" required>
            <?php 
              $suppliers = fetch_suppliers();
              foreach ($suppliers as $supplier) {
                echo "<option value='{$supplier['SupplierId']}'>{$supplier['Name']}</option>";
              }
            ?>
          </select> <br>

          <label for="description"><b>Description</b></label>
          <textarea name="description"></textarea> <br>

          <button type="submit">Add Book</button>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
