<?php 
    require_once '../config.php';
    requireUserType('staff');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Library Management</title>
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
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
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
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .reservations-table, .lending-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .reservations-table th, .reservations-table td,
        .lending-table th, .lending-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .reservations-table th, .lending-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 0.8rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-approved {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-issued {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-overdue {
            background: #ffebee;
            color: #d32f2f;
        }

        .btn-action {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }

        .btn-approve {
            background: #4caf50;
            color: white;
        }

        .btn-approve:hover {
            background: #45a049;
        }

        .btn-reject {
            background: #f44336;
            color: white;
        }

        .btn-reject:hover {
            background: #d32f2f;
        }

        .btn-issue {
            background: #2196f3;
            color: white;
        }

        .btn-issue:hover {
            background: #1976d2;
        }

        .btn-return {
            background: #ff9800;
            color: white;
        }

        .btn-return:hover {
            background: #f57c00;
        }

        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
            min-width: 200px;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-search {
            padding: 0.75rem 1.5rem;
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

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
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

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .refresh-btn {
            background: #17a2b8;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-left: auto;
            display: block;
            margin-bottom: 1rem;
        }

        .refresh-btn:hover {
            background: #138496;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .search-form {
                flex-direction: column;
                align-items: stretch;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
         .books-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
        }
        
        .books-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #3b82f6;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .books-table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .search-box {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            width: 300px;
        }
        
        .books-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .books-table th,
        .books-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .books-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .books-table tr:hover {
            background: #f9fafb;
        }
        
        .book-cover {
            width: 40px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .book-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .book-details h4 {
            margin: 0;
            font-weight: 600;
            color: #1f2937;
        }
        
        .book-details p {
            margin: 0.25rem 0 0 0;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .copies-info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .available-copies {
            font-weight: bold;
            color: #059669;
        }
        
        .total-copies {
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .book-type-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .type-physical {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .type-digital {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-active {
            color: #059669;
            font-weight: 500;
        }
        
        .status-inactive {
            color: #dc2626;
            font-weight: 500;
        }
        
        .loading {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .no-books {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <?php
    // Get staff statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'pending'");
    $stmt->execute();
    $pendingReservations = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lending_records WHERE status = 'issued'");
    $stmt->execute();
    $activeLendings = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lending_records WHERE status = 'issued' AND due_date < NOW()");
    $stmt->execute();
    $overdueBooks = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE is_active = 1");
    $stmt->execute();
    $totalBooks = $stmt->fetchColumn();
    ?>

    <nav class="navbar">
        <h1>📚 Staff Portal - Library Management</h1>
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </nav>

    <div class="container">
        <div id="alertContainer"></div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" style="color: #ff9800;"><?php echo $pendingReservations; ?></div>
                <div class="stat-label">Pending Reservations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #2196f3;"><?php echo $activeLendings; ?></div>
                <div class="stat-label">Active Lendings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #f44336;"><?php echo $overdueBooks; ?></div>
                <div class="stat-label">Overdue Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #4caf50;"><?php echo $totalBooks; ?></div>
                <div class="stat-label">Total Transcation</div>
            </div>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Pending Reservations -->
            <div class="card">
                <h3>🎫 Pending Reservations</h3>
                <button class="refresh-btn" onclick="loadReservations()">🔄 Refresh</button>
                <div style="overflow-x: auto;">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Reservation #</th>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reservationsTableBody">
                            <!-- Data will be loaded via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Active Lendings -->
            <div class="card">
                <h3>📖 Active Lendings</h3>
                <button class="refresh-btn" onclick="loadLendings()">🔄 Refresh</button>
                <div style="overflow-x: auto;">
                    <table class="lending-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lendingsTableBody">
                            <!-- Data will be loaded via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue Book Modal -->
    <div id="issueModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('issueModal')">&times;</span>
            <h3>📚 Issue Book</h3>
            <form id="issueForm">
                <div class="form-group">
                    <label>Student:</label>
                    <input type="text" id="issueStudentName" readonly>
                </div>
                <div class="form-group">
                    <label>Book:</label>
                    <input type="text" id="issueBookTitle" readonly>
                </div>
                <div class="form-group">
                    <label>Due Date:</label>
                    <input type="date" id="issueDueDate" required>
                </div>
                <div class="form-group">
                    <label>Notes (Optional):</label>
                    <textarea id="issueNotes" rows="3" placeholder="Any additional notes..."></textarea>
                </div>
                <input type="hidden" id="issueReservationId">
                <button type="submit" class="btn-approve" style="width: 100%; padding: 1rem;">Issue Book</button>
            </form>
        </div>
    </div>
    <div class="books-container">
        <div class="page-header">
            <h1 class="page-title">Book</h1>
        </div>
        
        <!-- Stats Cards -->
        <div class="books-stats">
            <div class="stat-card">
                <div class="stat-number" id="totalBooks">-</div>
                <div class="stat-label">Total Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="availableBooks">-</div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="issuedBooks">-</div>
                <div class="stat-label">Currently Issued</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="digitalBooks">-</div>
                <div class="stat-label">Digital Books</div>
            </div>
        </div>
        
        <!-- Books Table -->
        <div class="books-table-container">
            <div class="table-header">
                <h3>All Books</h3>
                <input type="text" class="search-box" placeholder="Search books..." id="searchInput" onkeyup="filterBooks()">
            </div>
            
            <table class="books-table" id="booksTable">
                <thead>
                    <tr>
                        <th>Book Details</th>
                        <th>ISBN</th>
                        <th>Category</th>
                        <th>Publication Year</th>
                        <th>Type</th>
                        <th>Copies</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="booksTableBody">
                    <tr>
                        <td colspan="7" class="loading">Loading books...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <footer style="text-align: center; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-top: 2rem;">
    <p style="margin: 0; font-size: 14px;">Library Portal of Samba Institute</p>
</footer>                    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadReservations();
            loadLendings();
            
            // Set default due date (14 days from today)
            const defaultDueDate = new Date();
            defaultDueDate.setDate(defaultDueDate.getDate() + 14);
            document.getElementById('issueDueDate').value = defaultDueDate.toISOString().split('T')[0];
        });

        async function loadReservations() {
            try {
                const response = await fetch('../api/reservations.php');
                const reservations = await response.json();
                
                const tbody = document.getElementById('reservationsTableBody');
                
                if (reservations.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #666; padding: 2rem;">No pending reservations</td></tr>';
                    return;
                }
                
                tbody.innerHTML = reservations.map(reservation => `
                    <tr>
                        <td><strong>${reservation.reservation_number}</strong></td>
                        <td>
                            <div style="font-weight: 600;">${reservation.student_name}</div>
                            <div style="font-size: 0.8rem; color: #666;">${reservation.student_email}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">${reservation.book_title}</div>
                            <div style="font-size: 0.8rem; color: #666;">by ${reservation.book_author}</div>
                        </td>
                        <td style="font-size: 0.8rem;">${formatDate(reservation.reservation_date)}</td>
                        <td>
                            <button class="btn-action btn-issue" onclick="openIssueModal(${reservation.id}, '${reservation.student_name}', '${reservation.book_title}')">
                                Issue
                            </button>
                            <button class="btn-action btn-reject" onclick="rejectReservation(${reservation.id})">
                                Reject
                            </button>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error loading reservations:', error);
                showAlert('error', 'Error loading reservations');
            }
        }

        async function loadLendings() {
            try {
                const response = await fetch('../api/lendings.php');
                const lendings = await response.json();
                
                const tbody = document.getElementById('lendingsTableBody');
                
                if (lendings.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #666; padding: 2rem;">No active lendings</td></tr>';
                    return;
                }
                
                tbody.innerHTML = lendings.map(lending => {
                    const isOverdue = new Date(lending.due_date) < new Date();
                    const statusClass = isOverdue ? 'status-overdue' : 'status-issued';
                    const statusText = isOverdue ? 'Overdue' : 'Issued';
                    
                    return `
                        <tr>
                            <td>
                                <div style="font-weight: 600;">${lending.student_name}</div>
                                <div style="font-size: 0.8rem; color: #666;">${lending.student_email}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">${lending.book_title}</div>
                                <div style="font-size: 0.8rem; color: #666;">by ${lending.book_author}</div>
                            </td>
                            <td style="font-size: 0.8rem;">${formatDate(lending.issue_date)}</td>
                            <td style="font-size: 0.8rem;">${formatDate(lending.due_date)}</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            <td>
                                <button class="btn-action btn-return" onclick="returnBook(${lending.id})">
                                    Return
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            } catch (error) {
                console.error('Error loading lendings:', error);
                showAlert('error', 'Error loading lendings');
            }
        }

        function openIssueModal(reservationId, studentName, bookTitle) {
            document.getElementById('issueReservationId').value = reservationId;
            document.getElementById('issueStudentName').value = studentName;
            document.getElementById('issueBookTitle').value = bookTitle;
            document.getElementById('issueModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('issueForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const reservationId = document.getElementById('issueReservationId').value;
            const dueDate = document.getElementById('issueDueDate').value;
            const notes = document.getElementById('issueNotes').value;
            
            try {
                const response = await fetch('../api/issue-book.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reservation_id: reservationId,
                        due_date: dueDate,
                        notes: notes
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    closeModal('issueModal');
                    loadReservations();
                    loadLendings();
                    location.reload(); // Refresh stats
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error issuing book:', error);
                showAlert('error', 'Error issuing book');
            }
        });

        async function rejectReservation(reservationId) {
            if (!confirm('Are you sure you want to reject this reservation?')) {
                return;
            }
            
            try {
                const response = await fetch('../api/reject-reservation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reservation_id: reservationId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    loadReservations();
                    location.reload(); // Refresh stats
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error rejecting reservation:', error);
                showAlert('error', 'Error rejecting reservation');
            }
        }

        async function returnBook(lendingId) {
            if (!confirm('Mark this book as returned?')) {
                return;
            }
            
            try {
                const response = await fetch('../api/return-book.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ lending_id: lendingId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    loadLendings();
                    location.reload(); // Refresh stats
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error returning book:', error);
                showAlert('error', 'Error returning book');
            }
        }

        function searchUser(event) {
            event.preventDefault();
            const searchTerm = document.getElementById('userSearch').value;
            if (searchTerm.trim()) {
                // Implement user search functionality
                showAlert('info', 'User search functionality to be implemented');
            }
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../logout.php';
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    
    let allBooks = []; // Store all books for filtering
        
        // Load books when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadBooks();
        });
        
        async function loadBooks() {
            try {
                const response = await fetch('../api/books-list.php');
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                allBooks = data.books || [];
                displayBooks(allBooks);
                updateStats(data.stats || {});
                
            } catch (error) {
                console.error('Error loading books:', error);
                document.getElementById('booksTableBody').innerHTML = 
                    '<tr><td colspan="7" style="text-align: center; color: #dc2626; padding: 2rem;">Error loading books</td></tr>';
            }
        }
        
        function displayBooks(books) {
            const tbody = document.getElementById('booksTableBody');
            
            if (books.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="no-books">No books found</td></tr>';
                return;
            }
            
            tbody.innerHTML = books.map(book => {
                const typeClass = book.book_type === 'digital' ? 'type-digital' : 'type-physical';
                const statusClass = book.is_active ? 'status-active' : 'status-inactive';
                const statusText = book.is_active ? 'Active' : 'Inactive';
                
                return `
                    <tr>
                        <td>
                            <div class="book-info">
                                
                                <div class="book-details">
                                    <h4>${book.title}</h4>
                                    <p>by ${book.author}</p>
                                    <p>${book.publisher || 'Unknown Publisher'}</p>
                                </div>
                            </div>
                        </td>
                        <td>${book.isbn || '-'}</td>
                        <td>${book.category || '-'}</td>
                        <td>${book.publication_year || '-'}</td>
                        <td>
                            <span class="book-type-badge ${typeClass}">
                                ${book.book_type.charAt(0).toUpperCase() + book.book_type.slice(1)}
                            </span>
                        </td>
                        <td>
                            <div class="copies-info">
                                <span class="available-copies">${book.available_copies}</span>
                                <span class="total-copies">of ${book.total_copies}</span>
                            </div>
                        </td>
                        <td>
                            <span class="${statusClass}">${statusText}</span>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        function updateStats(stats) {
            document.getElementById('totalBooks').textContent = stats.total_books || 0;
            document.getElementById('availableBooks').textContent = stats.available_books || 0;
            document.getElementById('issuedBooks').textContent = stats.issued_books || 0;
            document.getElementById('digitalBooks').textContent = stats.digital_books || 0;
        }
        
        function filterBooks() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            const filteredBooks = allBooks.filter(book => 
                book.title.toLowerCase().includes(searchTerm) ||
                book.author.toLowerCase().includes(searchTerm) ||
                book.isbn?.toLowerCase().includes(searchTerm) ||
                book.category?.toLowerCase().includes(searchTerm) ||
                book.publisher?.toLowerCase().includes(searchTerm)
            );
            
            displayBooks(filteredBooks);
        }    
    </script>
</body>
</html>