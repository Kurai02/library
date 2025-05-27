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

$requiredFields = ['title', 'author', 'category', 'book_type'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
        exit();
    }
}

$title = sanitizeInput($input['title']);
$author = sanitizeInput($input['author']);
$isbn = !empty($input['isbn']) ? sanitizeInput($input['isbn']) : null;
$publisher = !empty($input['publisher']) ? sanitizeInput($input['publisher']) : null;
$publicationYear = !empty($input['publication_year']) ? (int)$input['publication_year'] : null;
$category = sanitizeInput($input['category']);
$bookType = sanitizeInput($input['book_type']);
$description = !empty($input['description']) ? sanitizeInput($input['description']) : null;

// Validate book type
if (!in_array($bookType, ['physical', 'digital'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid book type']);
    exit();
}

$totalCopies = 1;
$availableCopies = 1;

if ($bookType === 'physical') {
    $totalCopies = !empty($input['total_copies']) ? max(1, (int)$input['total_copies']) : 1;
    $availableCopies = $totalCopies;
}

try {
    $pdo->beginTransaction();
    
    // Check if ISBN already exists (if provided)
    if ($isbn) {
        $stmt = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
        $stmt->execute([$isbn]);
        if ($stmt->fetch()) {
            throw new Exception('A book with this ISBN already exists');
        }
    }
    
    // Insert book
    $stmt = $pdo->prepare("
        INSERT INTO books (isbn, title, author, publisher, publication_year, category, book_type, 
                          total_copies, available_copies, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $isbn, $title, $author, $publisher, $publicationYear, $category, 
        $bookType, $totalCopies, $availableCopies, $description
    ]);
    
    $newBookId = $pdo->lastInsertId();
    
    // Log activity
    logActivity($_SESSION['user_id'], 'Book Added', "Added new book: $title (ID: $newBookId)");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Book added successfully',
        'book_id' => $newBookId
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Add book error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
}
?>