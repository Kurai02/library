<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType('admin');

try {
    $stmt = $pdo->prepare("
        SELECT id, username, email, full_name, user_type, student_id, 
               contact_number, is_active, created_at
        FROM users 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo json_encode($users);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log("Users API error: " . $e->getMessage());
}
?>