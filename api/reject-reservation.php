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

if (!$reservationId) {
    echo json_encode(['success' => false, 'message' => 'Reservation ID is required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get reservation details
    $stmt = $pdo->prepare("
        SELECT r.*, b.title, u.full_name 
        FROM reservations r
        JOIN books b ON r.book_id = b.id
        JOIN users u ON r.user_id = u.id
        WHERE r.id = ? AND r.status = 'pending'
    ");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        throw new Exception('Reservation not found or already processed');
    }
    
    // Update reservation status
    $stmt = $pdo->prepare("
        UPDATE reservations 
        SET status = 'rejected', approved_by = ?, approved_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $reservationId]);
    
    // Log activity
    logActivity($_SESSION['user_id'], 'Reservation Rejected', "Rejected reservation for book: {$reservation['title']} by {$reservation['full_name']}");
    logActivity($reservation['user_id'], 'Reservation Rejected', "Reservation rejected for book: {$reservation['title']}");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Reservation rejected successfully'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Reject reservation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>