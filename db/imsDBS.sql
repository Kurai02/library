CREATE DATABASE LibraryDB;
USE LibraryDB;

-- 1. Users: Admins, Staff, Students
CREATE TABLE Users (
    UserId INT AUTO_INCREMENT PRIMARY KEY,
    UserName VARCHAR(100) NOT NULL,
    Password VARCHAR(255) NOT NULL, -- Use hashed passwords
    Role ENUM('admin', 'staff', 'student') NOT NULL,
    Email VARCHAR(255) UNIQUE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Suppliers: Where books are sourced from
CREATE TABLE Supplier (
    SupplierId INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Address VARCHAR(255),
    Contact VARCHAR(20),
    Notes TEXT
);

-- 3. Books: Inventory of books
CREATE TABLE Book (
    BookId INT AUTO_INCREMENT PRIMARY KEY,
    ISBN VARCHAR(20) UNIQUE NOT NULL,
    Title VARCHAR(255) NOT NULL,
    Author VARCHAR(255),
    Publisher VARCHAR(255),
    YearPublished INT,
    Genre VARCHAR(100),
    Quantity INT DEFAULT 0 CHECK (Quantity >= 0),
    Description TEXT,
    SupplierId INT,
    FOREIGN KEY (SupplierId) REFERENCES Supplier(SupplierId) ON DELETE SET NULL
);

-- 4. Lending: Tracks book borrowing
CREATE TABLE Lending (
    LendingId INT AUTO_INCREMENT PRIMARY KEY,
    BookId INT NOT NULL,
    BorrowerId INT NOT NULL,
    StaffId INT, -- Staff who issued the book
    BorrowDate DATE NOT NULL DEFAULT CURRENT_DATE,
    DueDate DATE NOT NULL,
    ReturnDate DATE,
    Status ENUM('borrowed', 'returned', 'late') DEFAULT 'borrowed',
    FOREIGN KEY (BookId) REFERENCES Book(BookId),
    FOREIGN KEY (BorrowerId) REFERENCES Users(UserId),
    FOREIGN KEY (StaffId) REFERENCES Users(UserId)
);

-- 5. Optional: Fines or Penalties Table (if needed later)
CREATE TABLE Fine (
    FineId INT AUTO_INCREMENT PRIMARY KEY,
    LendingId INT NOT NULL,
    Amount DECIMAL(10, 2) NOT NULL,
    Paid BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (LendingId) REFERENCES Lending(LendingId)
);

-- 6. View: Most Borrowed Books
CREATE VIEW LendingTrends AS
SELECT 
    b.BookId,
    b.Title,
    COUNT(*) AS TimesBorrowed
FROM Lending l
JOIN Book b ON b.BookId = l.BookId
GROUP BY l.BookId
ORDER BY TimesBorrowed DESC;

-- 7. View: Overdue Books (for staff monitoring)
CREATE VIEW OverdueBooks AS
SELECT
    l.LendingId,
    u.UserName AS Borrower,
    b.Title,
    l.DueDate,
    l.Status
FROM Lending l
JOIN Users u ON l.BorrowerId = u.UserId
JOIN Book b ON l.BookId = b.BookId
WHERE l.Status = 'late';
CREATE TABLE requests (
    RequestID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    ProductID INT NOT NULL,
    RequestDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status VARCHAR(20) DEFAULT 'pending',
    FOREIGN KEY (UserID) REFERENCES users(UserID),
    FOREIGN KEY (ProductID) REFERENCES product(ProductID)
);
