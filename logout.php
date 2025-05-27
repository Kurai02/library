<?php
require_once 'config.php';

if (isLoggedIn()) {
    // Log the logout activity
    logActivity($_SESSION['user_id'], 'User Logout', 'User logged out successfully');
    
    // Destroy session
    session_destroy();
}

// Redirect to login page with success message
header('Location: login.php?logout=success');
exit();
?>