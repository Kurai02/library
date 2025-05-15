<?php 
require_once "layout/p_layout.php";
session_start();
require_once "logic/database.php";
$activeUser = login_valid();

// Initialize filter variables from GET parameters
$supplier = isset($_GET['supplier']) ? intval($_GET['supplier']) : null;
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$min_cost = isset($_GET['min_cost']) ? floatval($_GET['min_cost']) : null;
$max_cost = isset($_GET['max_cost']) ? floatval($_GET['max_cost']) : null;

// Build dynamic SQL query with filters
open_connection();

$sql = "SELECT p.ProductID, p.ProductName, p.Type, p.Cost, s.Sup_Name 
        FROM product p 
        LEFT JOIN suplier s ON p.Sup_Id = s.Sup_Id 
        WHERE 1=1";

$params = [];
$types_str = "";

// Add supplier filter
if ($supplier) {
    $sql .= " AND p.Sup_Id = ?";
    $params[] = $supplier;
    $types_str .= "i";
}

// Add type filter (partial match)
if ($type !== '') {
    $sql .= " AND p.Type LIKE ?";
    $params[] = "%$type%";
    $types_str .= "s";
}

// Add min cost filter
if ($min_cost !== null) {
    $sql .= " AND p.Cost >= ?";
    $params[] = $min_cost;
    $types_str .= "d";
}

// Add max cost filter
if ($max_cost !== null) {
    $sql .= " AND p.Cost <= ?";
    $params[] = $max_cost;
    $types_str .= "d";
}

// Prepare and execute statement securely
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types_str, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();
close_connection();

// Fetch suppliers for dropdown
$suppliers = fetch_suppliers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Products</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/response_style.css" />
  <script type="text/javascript" src="logic/app.js" defer></script>
  <style>
    /* Fix SVG color and size */
    .search-icon {
      width: 24px;
      height: 24px;
      fill: #e8eaed;
      vertical-align: middle;
      cursor: pointer;
    }
  </style>
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
    <form method="GET" action="" style="display: flex; align-items: center; gap: 5px;">
      <input type="text" placeholder="Product Search" name="type" value="<?= htmlspecialchars($type) ?>" />
      <button type="submit" title="Search">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 20l-5.6-5.6a7 7 0 1 0-1.4 1.4L20 21zM10 16a6 6 0 1 1 0-12 6 6 0 0 1 0 12z"/></svg>
      </button>
    </form>
  </div>

  <div class="container">
    <h2>Advanced Search</h2>
    <form method="GET" action="">
      <label for="supplier">Supplier:</label>
      <select name="supplier" id="supplier">
          <option value="">--Select Supplier--</option>
          <?php foreach ($suppliers as $supplierItem): ?>
            <option value="<?= intval($supplierItem['Sup_Id']) ?>" <?= (isset($_GET['supplier']) && $_GET['supplier'] == $supplierItem['Sup_Id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($supplierItem['Sup_Name']) ?>
            </option>
          <?php endforeach; ?>
      </select><br>

      <label for="type">Type:</label>
      <input type="text" name="type" id="type" value="<?= htmlspecialchars($type) ?>"><br>

      <label for="min_cost">Min Cost:</label>
      <input type="number" step="0.01" name="min_cost" id="min_cost" value="<?= htmlspecialchars($min_cost) ?>"><br>

      <label for="max_cost">Max Cost:</label>
      <input type="number" step="0.01" name="max_cost" id="max_cost" value="<?= htmlspecialchars($max_cost) ?>"><br>

      <input type="submit" value="Filter">
    </form>
  </div>

  <div class="container">
    <h2>Products</h2>
    <?php if (count($products) > 0): ?>
      <table class="table">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Type</th>
            <th>Cost</th>
            <th>Supplier</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['ProductName']) ?></td>
              <td><?= htmlspecialchars($p['Type']) ?></td>
              <td><?= number_format($p['Cost'], 2) ?></td>
              <td><?= htmlspecialchars($p['Sup_Name']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No products found matching the criteria.</p>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
