<?php
require_once 'config.php';

echo "Inserting sample data...\n";

try {
    // Insert sample staff user
    $staffPassword = password_hash('staff123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, full_name, user_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['staff', 'staff@library.com', $staffPassword, 'Library Staff', 'staff']);
    
    // Insert sample student user
    $studentPassword = password_hash('student123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, full_name, user_type, student_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['student', 'student@library.com', $studentPassword, 'John Student', 'student', 'STU001']);
    
    // Insert more sample books
    $sampleBooks = [
        ['978-0321563842', 'The C Programming Language', 'Brian Kernighan, Dennis Ritchie', 'Prentice Hall', 1988, 'Programming', 'physical', 3, 3],
        ['978-0134092669', 'The Algorithm Design Manual', 'Steven Skiena', 'Springer', 2020, 'Programming', 'physical', 2, 2],
        ['978-0132350884', 'Clean Architecture', 'Robert Martin', 'Prentice Hall', 2017, 'Programming', 'digital', 1, 1],
        ['978-1449344672', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 'O\'Reilly Media', 2017, 'Database', 'physical', 4, 4],
        ['978-0134757599', 'Refactoring', 'Martin Fowler', 'Addison-Wesley', 2018, 'Programming', 'digital', 1, 1],
        ['978-0596009205', 'Head First Design Patterns', 'Eric Freeman', 'O\'Reilly Media', 2004, 'Programming', 'physical', 3, 3],
        ['978-0321127426', 'Patterns of Enterprise Application Architecture', 'Martin Fowler', 'Addison-Wesley', 2002, 'Programming', 'digital', 1, 1],
        ['978-0321146533', 'Test Driven Development', 'Kent Beck', 'Addison-Wesley', 2002, 'Programming', 'physical', 2, 2],
        ['978-0596517748', 'JavaScript: The Definitive Guide', 'David Flanagan', 'O\'Reilly Media', 2020, 'Web Development', 'digital', 1, 1],
        ['978-1617294433', 'Node.js in Action', 'Alex Young', 'Manning Publications', 2017, 'Web Development', 'physical', 2, 2]
    ];
    
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO books (isbn, title, author, publisher, publication_year, category, book_type, total_copies, available_copies, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($sampleBooks as $book) {
        $description = "A comprehensive guide to " . strtolower($book[5]) . " covering essential concepts and practical applications.";
        $bookData = array_merge($book, [$description]);
        $stmt->execute($bookData);
    }
    
    // Insert sample categories if they don't exist
    $categories = ['Programming', 'Web Development', 'Database', 'Science', 'Literature', 'History', 'Mathematics', 'Business'];
    
    echo "Sample data inserted successfully!\n\n";
    echo "Login Credentials:\n";
    echo "==================\n";
    echo "Admin: admin / admin123\n";
    echo "Staff: staff / staff123\n";
    echo "Student: student / student123\n\n";
    echo "Database contains:\n";
    
    // Show statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    echo "- Users: $userCount\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM books");
    $stmt->execute();
    $bookCount = $stmt->fetchColumn();
    echo "- Books: $bookCount\n";
    
    echo "\nSample data setup complete!\n";
    
} catch (PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage() . "\n";
}
?>