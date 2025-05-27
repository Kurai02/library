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
$reservationId = $input['reservation_id'] ?? null;
$dueDate = $input['due_date'] ?? null;
$notes = $input['notes'] ?? '';

if (!$reservationId || !$dueDate) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get reservation details
    $stmt = $pdo->prepare("
        SELECT r.*, b.title, b.available_copies 
        FROM reservations r
        JOIN books b ON r.book_id = b.id
        WHERE r.id = ? AND r.status = 'pending'
    ");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        throw new Exception('Reservation not found or already processed');
    }
    
    // Check if book is still available
    if ($reservation['available_copies'] <= 0) {
        throw new Exception('Book is no longer available');
    }
    
    // Validate due date
    $dueDateObj = new DateTime($dueDate);
    $today = new DateTime();
    if ($dueDateObj <= $today) {
        throw new Exception('Due date must be in the future');
    }
    
    // Create lending record
    $stmt = $pdo->prepare("
        INSERT INTO lending_records (user_id, book_id, reservation_id, due_date, issued_by, notes, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'issued')
    ");
    $stmt->execute([
        $reservation['user_id'],
        $reservation['book_id'],
        $reservationId,
        $dueDate,
        $_SESSION['user_id'],
        $notes
    ]);
    
    // Update reservation status
    $stmt = $pdo->prepare("
        UPDATE reservations 
        SET status = 'approved', approved_by = ?, approved_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $reservationId]);
    
    // Decrease available copies
    $stmt = $pdo->prepare("
        UPDATE books 
        SET available_copies = available_copies - 1 
        WHERE id = ?
    ");
    $stmt->execute([$reservation['book_id']]);
    
    // Log activity
    logActivity($_SESSION['user_id'], 'Book Issued', "Issued book: {$reservation['title']} to user ID: {$reservation['user_id']}");
    logActivity($reservation['user_id'], 'Book Received', "Received book: {$reservation['title']}");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Book issued successfully'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Issue book error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>