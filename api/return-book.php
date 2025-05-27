<?php
require_once '../config.php';

header('Content-Type: application/json');

requireUserType(['staff', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$lendingId = $input['lending_id'] ?? null;

if (!$lendingId) {
    echo json_encode(['success' => false, 'message' => 'Missing lending ID']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get lending record details
    $stmt = $pdo->prepare("
        SELECT lr.*, b.title, b.available_copies, u.full_name as student_name
        FROM lending_records lr
        JOIN books b ON lr.book_id = b.id
        JOIN users u ON lr.user_id = u.id
        WHERE lr.id = ? AND lr.status = 'issued'
    ");
    $stmt->execute([$lendingId]);
    $lending = $stmt->fetch();
    
    if (!$lending) {
        throw new Exception('Lending record not found or book already returned');
    }
    
    // Calculate fine if overdue
    $fine = 0;
    $currentDate = new DateTime();
    $dueDate = new DateTime($lending['due_date']);
    
    if ($currentDate > $dueDate) {
        // Calculate days overdue
        $interval = $currentDate->diff($dueDate);
        $daysOverdue = $interval->days;
        
        // Fine calculation (e.g., $0.50 per day - adjust as needed)
        $finePerDay = 0.50;
        $fine = $daysOverdue * $finePerDay;
    }
    
    // Update lending record - mark as returned
    $stmt = $pdo->prepare("
        UPDATE lending_records 
        SET status = 'returned', 
            return_date = NOW(), 
            returned_to = ?,
            fine_amount = ?
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $fine, $lendingId]);
    
    // Increase available copies
    $stmt = $pdo->prepare("
        UPDATE books 
        SET available_copies = available_copies + 1 
        WHERE id = ?
    ");
    $stmt->execute([$lending['book_id']]);
    
    // Log activity
    logActivity($_SESSION['user_id'], 'Book Return Processed', "Processed return of book: {$lending['title']} from {$lending['student_name']}");
    logActivity($lending['user_id'], 'Book Returned', "Returned book: {$lending['title']}");
    
    // If there was a fine, log it
    if ($fine > 0) {
        logActivity($lending['user_id'], 'Fine Applied', "Fine of $" . number_format($fine, 2) . " applied for late return of: {$lending['title']}");
    }
    
    $pdo->commit();
    
    $message = 'Book returned successfully';
    if ($fine > 0) {
        $message .= '. Fine applied: $' . number_format($fine, 2);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'fine_amount' => $fine
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Return book error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>