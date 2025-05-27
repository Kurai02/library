<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Missing user ID']);
    exit();
}

// Prevent admin from deleting themselves
if ($userId == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get user details before deletion
    $stmt = $pdo->prepare("SELECT full_name, user_type FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Check if user has active lendings
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lending_records WHERE user_id = ? AND status = 'issued'");
    $stmt->execute([$userId]);
    $activeLendings = $stmt->fetchColumn();
    
    if ($activeLendings > 0) {
        throw new Exception('Cannot delete user with active book lendings. Please return all books first.');
    }
    
    // Delete in order due to foreign key constraints
    
    // 1. Delete activity logs
    $stmt = $pdo->prepare("DELETE FROM activity_logs WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    // 2. Delete lending records (returned books)
    $stmt = $pdo->prepare("DELETE FROM lending_records WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    // 3. Delete reservations
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    // 4. Delete digital downloads (if table exists)
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'digital_downloads'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("DELETE FROM digital_downloads WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    // 5. Finally delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Log the deletion
    logActivity($_SESSION['user_id'], 'User Deleted', "Permanently deleted user: {$user['full_name']} ({$user['user_type']})");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => "User '{$user['full_name']}' has been permanently deleted"
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Delete user error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>