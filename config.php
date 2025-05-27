<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'samba');

// Application settings
define('SITE_URL', 'http://localhost/samba/');
define('UPLOAD_PATH', 'uploads/');
define('DEFAULT_LENDING_DAYS', 14);
define('FINE_PER_DAY', 2.00);

// Start session


// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireUserType($types) {
    requireLogin();
    if (!in_array(getUserType(), (array)$types)) {
        header('Location: dashboard.php');
        exit();
    }
}

function logActivity($userId, $action, $details = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
}

function generateReservationNumber() {
    return 'RES' . date('Ymd') . sprintf('%04d', rand(1000, 9999));
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y H:i', strtotime($datetime));
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function showAlert($type, $message) {
    return "<div class='alert alert-$type' style='margin: 10px 0; padding: 10px; border-radius: 5px; background: " . 
           ($type === 'success' ? '#d4edda' : ($type === 'error' ? '#f8d7da' : '#fff3cd')) . 
           "; border: 1px solid " . 
           ($type === 'success' ? '#c3e6cb' : ($type === 'error' ? '#f5c6cb' : '#ffeaa7')) . 
           "; color: " . 
           ($type === 'success' ? '#155724' : ($type === 'error' ? '#721c24' : '#856404')) . 
           ";'>$message</div>";
}

// Set timezone
date_default_timezone_set('Australia/Sydney');
?>