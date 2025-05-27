<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType(['staff', 'admin']);

try {
    // Get all books with their details
    $stmt = $pdo->prepare("
        SELECT 
            id,
            isbn,
            title,
            author,
            publisher,
            publication_year,
            category,
            book_type,
            total_copies,
            available_copies,
            cover_image,
            description,
            is_active,
            created_at
        FROM books 
        ORDER BY title ASC
    ");
    $stmt->execute();
    $books = $stmt->fetchAll();
    
    // Get statistics
    $statsQuery = $pdo->prepare("
        SELECT 
            COUNT(*) as total_books,
            SUM(CASE WHEN is_active = 1 THEN available_copies ELSE 0 END) as available_books,
            SUM(CASE WHEN is_active = 1 THEN (total_copies - available_copies) ELSE 0 END) as issued_books,
            COUNT(CASE WHEN book_type = 'digital' AND is_active = 1 THEN 1 END) as digital_books
        FROM books
    ");
    $statsQuery->execute();
    $stats = $statsQuery->fetch();
    
    echo json_encode([
        'success' => true,
        'books' => $books,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
    error_log("Books list API error: " . $e->getMessage());
}
?>