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
$bookId = $input['book_id'] ?? null;

if (!$bookId) {
    echo json_encode(['success' => false, 'message' => 'Missing book ID']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get book details before deletion
    $stmt = $pdo->prepare("SELECT title, author, total_copies, available_copies FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        throw new Exception('Book not found');
    }
    
    // Check if book has active lendings
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lending_records WHERE book_id = ? AND status = 'issued'");
    $stmt->execute([$bookId]);
    $activeLendings = $stmt->fetchColumn();
    
    if ($activeLendings > 0) {
        throw new Exception('Cannot delete book with active lendings. Please wait for all copies to be returned first.');
    }
    
    // Delete in order due to foreign key constraints
    
    // 1. Delete digital downloads (if table exists)
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'digital_downloads'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("DELETE FROM digital_downloads WHERE book_id = ?");
        $stmt->execute([$bookId]);
    }
    
    // 2. Delete lending records (completed/returned)
    $stmt = $pdo->prepare("DELETE FROM lending_records WHERE book_id = ?");
    $stmt->execute([$bookId]);
    
    // 3. Delete reservations
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE book_id = ?");
    $stmt->execute([$bookId]);
    
    // 4. Finally delete the book
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    
    // Log the deletion
    logActivity($_SESSION['user_id'], 'Book Deleted', "Permanently deleted book: '{$book['title']}' by {$book['author']}");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => "Book '{$book['title']}' has been permanently deleted"
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Delete book error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>