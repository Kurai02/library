<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $userType = sanitizeInput($_POST['user_type']);
    
    if (empty($username) || empty($password)) {
        header('Location: login.php?message=' . urlencode('Please fill in all fields') . '&type=error');
        exit();
    }
    
    try {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT id, username, email, password, full_name, user_type, is_active FROM users WHERE (username = ? OR email = ?) AND user_type = ? AND is_active = 1");
        $stmt->execute([$username, $username, $userType]);
        $user = $stmt->fetch();
        
        if ($user && (md5($password) === $user['password'] || password_verify($password, $user['password']))) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['email'] = $user['email'];
            
            // Log the login activity
            logActivity($user['id'], 'User Login', 'User logged in successfully');
            
            // Update last login (add this column to users table if needed)
            $updateStmt = $pdo->prepare("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Redirect based on user type
            switch ($user['user_type']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'staff':
                    header('Location: staff/dashboard.php');
                    break;
                case 'student':
                    header('Location: student/dashboard.php');
                    break;
                default:
                    header('Location: dashboard.php');
            }
            exit();
        } else {
            logActivity(0, 'Failed Login Attempt', "Failed login attempt for username: $username, user type: $userType");
            header('Location: login.php?message=' . urlencode('Invalid credentials or user type') . '&type=error');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        header('Location: login.php?message=' . urlencode('System error. Please try again.') . '&type=error');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>