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

$requiredFields = ['full_name', 'username', 'email', 'user_type', 'password'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
        exit();
    }
}

$fullName = sanitizeInput($input['full_name']);
$username = sanitizeInput($input['username']);
$email = sanitizeInput($input['email']);
$userType = sanitizeInput($input['user_type']);
$password = $input['password'];
$studentId = !empty($input['student_id']) ? sanitizeInput($input['student_id']) : null;
$contactNumber = !empty($input['contact_number']) ? sanitizeInput($input['contact_number']) : null;
$address = !empty($input['address']) ? sanitizeInput($input['address']) : null;

// Validate user type
if (!in_array($userType, ['admin', 'staff', 'student'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
    exit();
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        throw new Exception('Username already exists');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email already exists');
    }
    
    // Check if student ID already exists (if provided)
    if ($studentId) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE student_id = ?");
        $stmt->execute([$studentId]);
        if ($stmt->fetch()) {
            throw new Exception('Student ID already exists');
        }
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, full_name, user_type, student_id, contact_number, address) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$username, $email, $hashedPassword, $fullName, $userType, $studentId, $contactNumber, $address]);
    
    $newUserId = $pdo->lastInsertId();
    
    // Log activity
    logActivity($_SESSION['user_id'], 'User Created', "Created new $userType user: $fullName (ID: $newUserId)");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'User added successfully',
        'user_id' => $newUserId
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Add user error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>