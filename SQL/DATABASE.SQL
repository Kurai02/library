-- Library Management System Database Schema

CREATE DATABASE SAMBA;
USE SAMBA;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    user_type ENUM('admin', 'staff', 'student') NOT NULL,
    student_id VARCHAR(20) UNIQUE,
    contact_number VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Books table
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    isbn VARCHAR(20) UNIQUE,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100),
    publication_year YEAR,
    category VARCHAR(50),
    book_type ENUM('physical', 'digital') NOT NULL,
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    file_path VARCHAR(255), -- For digital books
    description TEXT,
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Reservations table
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
    expiry_date TIMESTAMP,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Lending records table
CREATE TABLE lending_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_id INT,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date TIMESTAMP NOT NULL,
    return_date TIMESTAMP NULL,
    status ENUM('issued', 'returned', 'overdue') DEFAULT 'issued',
    issued_by INT NOT NULL,
    returned_to INT,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (returned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Digital downloads table
CREATE TABLE digital_downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    download_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, email, password, full_name, user_type) 
VALUES ('admin', 'admin@library.com', MD5('admin123'), 'system administrator', 'admin'),
('staff', 'staff@library.com', MD5('staff123'), 'library staff', 'staff'),
('student', 'student@library.com', MD5('student123'), 'student', 'student'),
('staff01', 'staff01@library.com', MD5('staff123'), 'James Wilson', 'staff'),
('staff02', 'staff02@library.com', MD5('staff123'), 'Emma Thompson', 'staff'),
('staff03', 'staff03@library.com', MD5('staff123'), 'Michael Chen', 'staff'),
('staff04', 'staff04@library.com', MD5('staff123'), 'Lisa Rodriguez', 'staff'),
('student01', 'john.smith@student.edu.au', MD5('student123'), 'John Smith', 'student'),
('student02', 'mary.johnson@student.edu.au', MD5('student123'), 'Mary Johnson', 'student'),
('student03', 'robert.brown@student.edu.au', MD5('student123'), 'Robert Brown', 'student'),
('student04', 'jennifer.davis@student.edu.au', MD5('student123'), 'Jennifer Davis', 'student'),
('student05', 'william.miller@student.edu.au', MD5('student123'), 'William Miller', 'student'),
('student06', 'linda.wilson@student.edu.au', MD5('student123'), 'Linda Wilson', 'student'),
('student07', 'james.moore@student.edu.au', MD5('student123'), 'James Moore', 'student'),
('student08', 'patricia.taylor@student.edu.au', MD5('student123'), 'Patricia Taylor', 'student'),
('student09', 'michael.anderson@student.edu.au', MD5('student123'), 'Michael Anderson', 'student'),
('student10', 'barbara.thomas@student.edu.au', MD5('student123'), 'Barbara Thomas', 'student'),
('student11', 'christopher.jackson@student.edu.au', MD5('student123'), 'Christopher Jackson', 'student'),
('student12', 'susan.white@student.edu.au', MD5('student123'), 'Susan White', 'student');

-- Insert sample books
INSERT INTO books (isbn, title, author, publisher, publication_year, category, book_type, total_copies, available_copies, description) VALUES
('978-0134685991', 'Effective Java', 'Joshua Bloch', 'Addison-Wesley', 2017, 'Programming', 'physical', 5, 5, 'Best practices for Java programming'),
('978-0596517748', 'JavaScript: The Good Parts', 'Douglas Crockford', 'Reilly Media', 2008, 'Programming', 'physical', 3, 3, 'JavaScript programming guide'),
('978-1449355739', 'Learning PHP, MySQL & JavaScript', 'Robin Nixon', 'Reilly Media', 2018, 'Web Development', 'digital', 1, 1, 'Complete web development guide'),
('978-0134494166', 'Clean Code', 'Robert Martin', 'Prentice Hall', 2008, 'Programming', 'physical', 4, 4, 'Writing maintainable code'),
('978-0321884497', 'Database System Concepts', 'Abraham Silberschatz', 'McGraw-Hill', 2019, 'Database', 'physical', 2, 1, 'Database theory and practice'),
('978-0596005986', 'Learning MySQL', 'Seyed Tahaghoghi', 'Reilly Media', 2007, 'Database', 'physical', 2, 2, 'MySQL database guide'),
('978-0393347777', 'A Brief History of Time', 'Stephen Hawking', 'Bantam Books', 1998, 'Science', 'physical', 4, 3, 'Popular physics book'),
('978-0385537859', 'The Gene: An Intimate History', 'Siddhartha Mukherjee', 'Scribner', 2016, 'Science', 'physical', 2, 1, 'History of genetics'),
('978-0486652214', 'Introduction to Quantum Mechanics', 'David Griffiths', 'Cambridge Press', 2016, 'Science', 'physical', 1, 0, 'Quantum physics textbook'),
('978-0143127550', 'To Kill a Mockingbird', 'Harper Lee', 'Harper Perennial', 1960, 'Literature', 'physical', 5, 4, 'Classic American literature'),
('978-0486415871', 'Pride and Prejudice', 'Jane Austen', 'Dover Publications', 1813, 'Literature', 'physical', 3, 2, 'Classic English romance'),
('978-0451524935', '1984', 'George Orwell', 'Signet Classics', 1949, 'Literature', 'physical', 4, 1, 'Dystopian fiction masterpiece'),
('978-0385614412', 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 'Harper', 2014, 'History', 'physical', 3, 2, 'Human history overview'),
('978-0140449136', 'The Guns of August', 'Barbara Tuchman', 'Penguin Classics', 1962, 'History', 'physical', 2, 1, 'World War I history'),
('978-0486458427', 'Calculus Made Easy', 'Silvanus Thompson', 'Dover Publications', 1998, 'Mathematics', 'physical', 3, 2, 'Introduction to calculus'),
('978-0486137391', 'Linear Algebra Done Right', 'Sheldon Axler', 'Springer', 2015, 'Mathematics', 'digital', 2, 1, 'Linear algebra textbook');


-- Create indexes for better performance
CREATE INDEX idx_users_type ON users(user_type);
CREATE INDEX idx_books_type ON books(book_type);
CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_lending_status ON lending_records(status);
CREATE INDEX idx_activity_user ON activity_logs(user_id);