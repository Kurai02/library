<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$bookId = $input['book_id'] ?? null;
$userId = $_SESSION['user_id'];

if (!$bookId) {
    echo json_encode(['success' => false, 'message' => 'Book ID is required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Check if book exists and is available
    $stmt = $pdo->prepare("SELECT id, title, book_type, available_copies FROM books WHERE id = ? AND is_active = 1");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        throw new Exception('Book not found');
    }
    
    if ($book['book_type'] !== 'physical') {
        throw new Exception('Only physical books can be reserved');
    }
    
    if ($book['available_copies'] <= 0) {
        throw new Exception('Book is not available for reservation');
    }
    
    // Check if user already has an active reservation for this book
    $stmt = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND book_id = ? AND status = 'pending'");
    $stmt->execute([$userId, $bookId]);
    if ($stmt->fetch()) {
        throw new Exception('You already have an active reservation for this book');
    }
    
    // Check if user has reached reservation limit (e.g., 5 active reservations)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status = 'pending'");
    $stmt->execute([$userId]);
    $activeReservations = $stmt->fetchColumn();
    
    if ($activeReservations >= 5) {
        throw new Exception('You have reached the maximum number of active reservations (5)');
    }
    
    // Generate reservation number
    $reservationNumber = generateReservationNumber();
    
    // Create reservation
    $expiryDate = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $stmt = $pdo->prepare("
        INSERT INTO reservations (reservation_number, user_id, book_id, expiry_date, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$reservationNumber, $userId, $bookId, $expiryDate]);
    
    // Log activity
    logActivity($userId, 'Book Reserved', "Reserved book: {$book['title']} (ID: $bookId)");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Book reserved successfully',
        'reservation_number' => $reservationNumber,
        'expiry_date' => $expiryDate
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Reservation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>