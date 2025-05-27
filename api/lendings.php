<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType(['staff', 'admin']);

try {
    $stmt = $pdo->prepare("
        SELECT lr.id, lr.issue_date, lr.due_date, lr.status,
               u.full_name as student_name, u.email as student_email, u.student_id,
               b.title as book_title, b.author as book_author, b.isbn,
               DATEDIFF(NOW(), lr.due_date) as days_overdue
        FROM lending_records lr
        JOIN users u ON lr.user_id = u.id
        JOIN books b ON lr.book_id = b.id
        WHERE lr.status = 'issued'
        ORDER BY lr.due_date ASC
    ");
    $stmt->execute();
    $lendings = $stmt->fetchAll();
    
    echo json_encode($lendings);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log("Lendings API error: " . $e->getMessage());
}
?>