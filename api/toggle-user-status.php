<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['user_id'] ?? null;
$status = $input['status'] ?? null;

if (!$userId || $status === null) {
    echo json_encode(['success' => false, 'message' => 'User ID and status are required']);
    exit();
}

// Prevent admin from deactivating themselves
if ($userId == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot change your own status']);
    exit();
}

try {
    // Get user details
    $stmt = $pdo->prepare("SELECT full_name, user_type FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Update user status
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->execute([$status ? 1 : 0, $userId]);
    
    $action = $status ? 'activated' : 'deactivated';
    
    // Log activity
    logActivity($_SESSION['user_id'], 'User Status Changed', "User {$user['full_name']} has been $action");
    
    echo json_encode([
        'success' => true, 
        'message' => "User has been $action successfully"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    error_log("Toggle user status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>