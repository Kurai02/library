<?php
// DB connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "LibraryDB";  // Updated to new DB name
$conn = null;

session_start();

// Open DB connection
function open_connection() {
    global $servername, $username, $password, $dbname, $conn;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Close DB connection
function close_connection() {
    global $conn;
    if (isset($conn)) {
        $conn->close();
    }
}

// Destroy session and logout
function logoutUser() {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check login state and handle logout
function login_valid() {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        logoutUser();
    } elseif (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    return $_SESSION['username'];
}

// Secure user authentication (with prepared statements)
function check_cred($uname, $psw) {
    global $conn;
    open_connection();

    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserName = ? AND Password = ?");
    $stmt->bind_param("ss", $uname, $psw);  // WARNING: use hashing in production!
    $stmt->execute();
    $result = $stmt->get_result();
    $valid = $result->num_rows === 1;

    $stmt->close();
    close_connection();

    return $valid;
}

// Generic SELECT query
function select_rows($sql) {
    global $conn;
    $rows = [];
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

// Fetch all users (admin view)
function get_users() {
    open_connection();
    $sql = "SELECT UserId, UserName, Role FROM Users";
    $rows = select_rows($sql);
    close_connection();
    return $rows;
}

// Fetch all suppliers
function fetch_suppliers() {
    open_connection();
    $sql = "SELECT SupplierId, Name, Contact FROM Supplier";
    $rows = select_rows($sql);
    close_connection();
    return $rows;
}

// Fetch all books by a specific supplier
function get_books_by_supplier($supplierId) {
    open_connection();
    $supplierId = intval($supplierId);
    $sql = "SELECT * FROM Book WHERE SupplierId = $supplierId";
    $rows = select_rows($sql);
    close_connection();
    return $rows;
}

// Insert new request by student
function insert_request($userId, $productId) {
    open_connection();
    global $conn;
    $stmt = $conn->prepare("INSERT INTO requests (UserID, ProductID, RequestDate) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userId, $productId);
    $result = $stmt->execute();
    close_connection();
    return $result;
}

// Fetch requests by user ID
function get_requests_by_user($userId) {
    open_connection();
    global $conn;
    $stmt = $conn->prepare("SELECT r.RequestID, p.ProductName, r.RequestDate, r.Status FROM requests r JOIN product p ON r.ProductID = p.ProductID WHERE r.UserID = ? ORDER BY r.RequestDate DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    close_connection();
    return $requests;
}

?>