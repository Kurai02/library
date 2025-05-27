<?php 
    require_once '../config.php';
    requireUserType('student');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Library Management</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .search-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .search-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-search {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .book-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .book-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .book-info {
            padding: 1.5rem;
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .book-author {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .book-category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .book-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-reserve, .btn-download {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reserve {
            background: #4caf50;
            color: white;
        }

        .btn-reserve:hover {
            background: #45a049;
        }

        .btn-download {
            background: #2196f3;
            color: white;
        }

        .btn-download:hover {
            background: #1976d2;
        }

        .btn-unavailable {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        .lending-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .lending-table th,
        .lending-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .lending-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-issued {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-overdue {
            background: #ffebee;
            color: #d32f2f;
        }

        .status-pending {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .close:hover {
            color: #333;
        }

        .reservation-success {
            text-align: center;
            padding: 2rem;
        }

        .reservation-number {
            font-size: 2rem;
            font-weight: bold;
            color: #4caf50;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .container {
                padding: 0 0.5rem;
            }

            .search-form {
                flex-direction: column;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .books-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php // Get student statistics
    $userId = $_SESSION['user_id'];

    // Get current borrowed books
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM lending_records WHERE user_id = ? AND status = 'issued'");
    $stmt->execute([$userId]);
    $currentBooks = $stmt->fetchColumn();

    // Get pending reservations
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reservations WHERE user_id = ? AND status = 'pending'");
    $stmt->execute([$userId]);
    $pendingReservations = $stmt->fetchColumn();

    // Get overdue books
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM lending_records WHERE user_id = ? AND status = 'issued' AND due_date < NOW()");
    $stmt->execute([$userId]);
    $overdueBooks = $stmt->fetchColumn();

    // Get total books borrowed (history)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM lending_records WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalBorrowed = $stmt->fetchColumn();
    ?>

    <nav class="navbar">
        <h1>📚 Library Management System</h1>
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </nav>

    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" style="color: #2196f3;"><?php echo $currentBooks; ?></div>
                <div class="stat-label">Current Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ff9800;"><?php echo $pendingReservations; ?></div>
                <div class="stat-label">Pending Reservations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #f44336;"><?php echo $overdueBooks; ?></div>
                <div class="stat-label">Overdue Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #4caf50;"><?php echo $totalBorrowed; ?></div>
                <div class="stat-label">Total Borrowed</div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h3>🔍 Search Books</h3>
            <form class="search-form" onsubmit="searchBooks(event)">
                <input type="text" class="search-input" id="searchTerm" placeholder="Search by title, author, or ISBN...">
                <select class="search-input" id="categoryFilter" style="flex: 0 0 150px;">
                    <option value="">All Categories</option>
                    <option value="Programming">Programming</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Database">Database</option>
                    <option value="Science">Science</option>
                    <option value="Literature">Literature</option>
                </select>
                <select class="search-input" id="typeFilter" style="flex: 0 0 120px;">
                    <option value="">All Types</option>
                    <option value="physical">Physical</option>
                    <option value="digital">Digital</option>
                </select>
                <button type="submit" class="btn-search">Search</button>
            </form>
        </div>

        <div class="dashboard-grid">
            <!-- Books Section -->
            <div class="card" style="grid-column: 1 / -1;">
                <h3>📖 Available Books</h3>
                <div id="booksContainer" class="books-grid">
                    <!-- Books will be loaded here via JavaScript -->
                </div>
            </div>

            <!-- Current Borrowed Books -->
            <div class="card" style="grid-column: 1 / -1;">
                <h3>📋 My Current Books</h3>
                <div style="overflow-x: auto;">
                    <table class="lending-table">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Fine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT lr.*, b.title, b.author, b.isbn,
                                       DATEDIFF(NOW(), lr.due_date) as days_overdue
                                FROM lending_records lr
                                JOIN books b ON lr.book_id = b.id
                                WHERE lr.user_id = ? AND lr.status = 'issued'
                                ORDER BY lr.due_date ASC
                            ");
                            $stmt->execute([$userId]);
                            $currentLendings = $stmt->fetchAll();

                            if (empty($currentLendings)) {
                                echo "<tr><td colspan='6' style='text-align: center; color: #666; padding: 2rem;'>No books currently borrowed</td></tr>";
                            } else {
                                foreach ($currentLendings as $lending) {
                                    $isOverdue = strtotime($lending['due_date']) < time();
                                    $statusClass = $isOverdue ? 'status-overdue' : 'status-issued';
                                    $statusText = $isOverdue ? 'Overdue' : 'Issued';
                                    $fine = $isOverdue ? max(0, $lending['days_overdue'] * FINE_PER_DAY) : 0;
                                    
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($lending['title']) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($lending['author']) . "</td>";
                                    echo "<td>" . formatDate($lending['issue_date']) . "</td>";
                                    echo "<td>" . formatDate($lending['due_date']) . "</td>";
                                    echo "<td><span class='status-badge $statusClass'>$statusText</span></td>";
                                    echo "<td>$" . number_format($fine, 2) . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reservation History -->
            <div class="card" style="grid-column: 1 / -1;">
                <h3>🎫 My Reservations</h3>
                <div style="overflow-x: auto;">
                    <table class="lending-table">
                        <thead>
                            <tr>
                                <th>Reservation #</th>
                                <th>Book Title</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT r.*, b.title, b.author
                                FROM reservations r
                                JOIN books b ON r.book_id = b.id
                                WHERE r.user_id = ?
                                ORDER BY r.reservation_date DESC
                                LIMIT 10
                            ");
                            $stmt->execute([$userId]);
                            $reservations = $stmt->fetchAll();

                            if (empty($reservations)) {
                                echo "<tr><td colspan='5' style='text-align: center; color: #666; padding: 2rem;'>No reservations made</td></tr>";
                            } else {
                                foreach ($reservations as $reservation) {
                                    $statusClass = 'status-' . $reservation['status'];
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($reservation['reservation_number']) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($reservation['title']) . "</td>";
                                    echo "<td>" . formatDate($reservation['reservation_date']) . "</td>";
                                    echo "<td><span class='status-badge $statusClass'>" . ucfirst($reservation['status']) . "</span></td>";
                                    echo "<td>" . ($reservation['expiry_date'] ? formatDate($reservation['expiry_date']) : '-') . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Modal -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>
    <footer style="text-align: center; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-top: 2rem;">
    <p style="margin: 0; font-size: 14px;">Library Portal of Samba Institute</p>
</footer>                        
    <script>
        // Load books on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadBooks();
        });

        async function loadBooks(searchTerm = '', category = '', type = '') {
            try {
                const response = await fetch(`../api/books.php?search=${encodeURIComponent(searchTerm)}&category=${encodeURIComponent(category)}&type=${encodeURIComponent(type)}`);
                const books = await response.json();
                
                const container = document.getElementById('booksContainer');
                
                if (books.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: #666; grid-column: 1 / -1; padding: 2rem;">No books found</p>';
                    return;
                }
                
                container.innerHTML = books.map(book => `
                    <div class="book-card">
                        <div class="book-image">📚</div>
                        <div class="book-info">
                            <div class="book-title">${book.title}</div>
                            <div class="book-author">by ${book.author}</div>
                            <div class="book-category">${book.category}</div>
                            <div style="margin-bottom: 1rem;">
                                <small style="color: #666;">
                                    ${book.book_type === 'physical' ? 
                                        `Available: ${book.available_copies}/${book.total_copies}` : 
                                        'Digital Copy'
                                    }
                                </small>
                            </div>
                            <div class="book-actions">
                                ${book.book_type === 'physical' ? 
                                    (book.available_copies > 0 ? 
                                        `<button class="btn-reserve" onclick="reserveBook(${book.id})">Reserve</button>` :
                                        `<button class="btn-unavailable" disabled>Not Available</button>`
                                    ) :
                                    `<button class="btn-download" onclick="downloadBook(${book.id})">Download</button>`
                                }
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading books:', error);
                document.getElementById('booksContainer').innerHTML = '<p style="text-align: center; color: #f44336;">Error loading books</p>';
            }
        }

        function searchBooks(event) {
            event.preventDefault();
            const searchTerm = document.getElementById('searchTerm').value;
            const category = document.getElementById('categoryFilter').value;
            const type = document.getElementById('typeFilter').value;
            loadBooks(searchTerm, category, type);
        }

        async function reserveBook(bookId) {
            try {
                const response = await fetch('../api/reserve.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showReservationSuccess(result.reservation_number);
                    loadBooks(); // Refresh books
                    location.reload(); // Refresh stats
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error reserving book:', error);
                alert('Error making reservation');
            }
        }

        async function downloadBook(bookId) {
            try {
                const response = await fetch('../api/download.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId })
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `book_${bookId}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                } else {
                    const result = await response.json();
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error downloading book:', error);
                alert('Error downloading book');
            }
        }

        function showReservationSuccess(reservationNumber) {
            const modal = document.getElementById('reservationModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.innerHTML = `
                <div class="reservation-success">
                    <h2>✅ Reservation Successful!</h2>
                    <p>Your reservation has been confirmed.</p>
                    <div class="reservation-number">${reservationNumber}</div>
                    <p>Please show this number to the librarian to collect your book.</p>
                    <p><small>The reservation will expire in 24 hours if not collected.</small></p>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('reservationModal').style.display = 'none';
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../logout.php';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reservationModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>