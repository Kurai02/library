<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$type = $_GET['type'] ?? '';

try {
    $sql = "SELECT id, isbn, title, author, publisher, publication_year, category, 
                   book_type, total_copies, available_copies, description, cover_image
            FROM books 
            WHERE is_active = 1";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    if (!empty($type)) {
        $sql .= " AND book_type = ?";
        $params[] = $type;
    }
    
    $sql .= " ORDER BY title ASC LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    echo json_encode($books);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log("Books API error: " . $e->getMessage());
}
?>