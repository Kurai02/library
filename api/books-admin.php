<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType('admin');

try {
    $stmt = $pdo->prepare("
        SELECT id, isbn, title, author, publisher, publication_year, category, 
               book_type, total_copies, available_copies, is_active, created_at
        FROM books 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $books = $stmt->fetchAll();
    
    echo json_encode($books);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log("Books Admin API error: " . $e->getMessage());
}
?>