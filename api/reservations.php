<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType(['staff', 'admin']);

try {
    $stmt = $pdo->prepare("
        SELECT r.id, r.reservation_number, r.reservation_date, r.expiry_date,
               u.full_name as student_name, u.email as student_email, u.student_id,
               b.title as book_title, b.author as book_author, b.isbn
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN books b ON r.book_id = b.id
        WHERE r.status = 'pending'
        ORDER BY r.reservation_date ASC
    ");
    $stmt->execute();
    $reservations = $stmt->fetchAll();
    
    echo json_encode($reservations);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log("Reservations API error: " . $e->getMessage());
}
?>