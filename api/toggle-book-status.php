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
$bookId = $input['book_id'] ?? null;
$status = $input['status'] ?? null;

if (!$bookId || $status === null) {
    echo json_encode(['success' => false, 'message' => 'Book ID and status are required']);
    exit();
}

try {
    // Get book details
    $stmt = $pdo->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        throw new Exception('Book not found');
    }
    
    // Update book status
    $stmt = $pdo->prepare("UPDATE books SET is_active = ? WHERE id = ?");
    $stmt->execute([$status ? 1 : 0, $bookId]);
    
    $action = $status ? 'activated' : 'deactivated';
    
    // Log activity
    logActivity($_SESSION['user_id'], 'Book Status Changed', "Book '{$book['title']}' has been $action");
    
    echo json_encode([
        'success' => true, 
        'message' => "Book has been $action successfully"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    error_log("Toggle book status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>