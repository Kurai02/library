<?php 
    require_once '../config.php';
    requireUserType('admin');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library Management</title>
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
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
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .recent-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .recent-table th,
        .recent-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .recent-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-inactive {
            background: #ffebee;
            color: #d32f2f;
        }

        .status-pending {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-overdue {
            background: #ffebee;
            color: #d32f2f;
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
            margin: 3% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            position: relative;
            animation: modalSlideIn 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
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

        .tabs {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }

        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            border-bottom-color: #667eea;
            color: #667eea;
            font-weight: 600;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .main-content {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php


    // Get admin statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
    $stmt->execute();
    $totalUsers = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE is_active = 1");
    $stmt->execute();
    $totalBooks = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lending_records WHERE status = 'issued'");
    $stmt->execute();
    $activeLendings = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'pending'");
    $stmt->execute();
    $pendingReservations = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lending_records WHERE status = 'issued' AND due_date < NOW()");
    $stmt->execute();
    $overdueBooks = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM digital_downloads WHERE DATE(download_date) = CURDATE()");
    $stmt->execute();
    $todayDownloads = $stmt->fetchColumn();
    ?>

    <nav class="navbar">
        <h1> Admin Dashboard 🔧 - Library Management</h1>
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
                <div class="stat-icon">👥</div>
                <div class="stat-number" style="color: #2196f3;"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-number" style="color: #4caf50;"><?php echo $totalBooks; ?></div>
                <div class="stat-label">Total Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-number" style="color: #ff9800;"><?php echo $activeLendings; ?></div>
                <div class="stat-label">Active Lendings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎫</div>
                <div class="stat-number" style="color: #9c27b0;"><?php echo $pendingReservations; ?></div>
                <div class="stat-label">Pending Reservations</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-number" style="color: #f44336;"><?php echo $overdueBooks; ?></div>
                <div class="stat-label">Overdue Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⬇️</div>
                <div class="stat-number" style="color: #00bcd4;"><?php echo $todayDownloads; ?></div>
                <div class="stat-label">Today's Downloads</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="action-buttons">
            <button class="action-btn" onclick="openModal('addUserModal')">
                👤 Add New User
            </button>
            <button class="action-btn" onclick="openModal('addBookModal')">
                📖 Add New Book
            </button>
            <button class="action-btn" onclick="viewUsers()">
                👥 Manage Users
            </button>
            <button class="action-btn" onclick="viewBooks()">
                📚 Manage Books
            </button>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Recent Users -->
            <div class="card">
                <h3>👥 Recent Users</h3>
                <div style="overflow-x: auto;">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT full_name, user_type, is_active, created_at FROM users ORDER BY created_at DESC LIMIT 5");
                            $stmt->execute();
                            $recentUsers = $stmt->fetchAll();

                            foreach ($recentUsers as $user) {
                                $statusClass = $user['is_active'] ? 'status-active' : 'status-inactive';
                                $statusText = $user['is_active'] ? 'Active' : 'Inactive';
                                
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                                echo "<td>" . ucfirst($user['user_type']) . "</td>";
                                echo "<td><span class='status-badge $statusClass'>$statusText</span></td>";
                                echo "<td>" . formatDate($user['created_at']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <h3>📋 Recent Activities</h3>
                <div style="overflow-x: auto;">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT al.action, al.created_at, u.full_name 
                                FROM activity_logs al
                                JOIN users u ON al.user_id = u.id
                                ORDER BY al.created_at DESC 
                                LIMIT 8
                            ");
                            $stmt->execute();
                            $activities = $stmt->fetchAll();

                            foreach ($activities as $activity) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($activity['full_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($activity['action']) . "</td>";
                                echo "<td style='font-size: 0.8rem;'>" . formatDateTime($activity['created_at']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            <h3>👤 Add New User</h3>
            <form id="addUserForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>User Type *</label>
                        <select name="user_type" required>
                            <option value="">Select Type</option>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Student ID (for students)</label>
                        <input type="text" name="student_id">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="tel" name="contact_number">
                    </div>
                    <div class="form-group full-width">
                        <label>Address</label>
                        <textarea name="address" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Add User</button>
            </form>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div id="addBookModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addBookModal')">&times;</span>
            <h3>📖 Add New Book</h3>
            <form id="addBookForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Author *</label>
                        <input type="text" name="author" required>
                    </div>
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn">
                    </div>
                    <div class="form-group">
                        <label>Publisher</label>
                        <input type="text" name="publisher">
                    </div>
                    <div class="form-group">
                        <label>Publication Year</label>
                        <input type="number" name="publication_year" min="1900" max="2025">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <option value="Programming">Programming</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Database">Database</option>
                            <option value="Science">Science</option>
                            <option value="Literature">Literature</option>
                            <option value="History">History</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Business">Business</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Book Type *</label>
                        <select name="book_type" required onchange="toggleCopiesField(this)">
                            <option value="">Select Type</option>
                            <option value="physical">Physical</option>
                            <option value="digital">Digital</option>
                        </select>
                    </div>
                    <div class="form-group" id="copiesField">
                        <label>Total Copies</label>
                        <input type="number" name="total_copies" min="1" value="1">
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Add Book</button>
            </form>
        </div>
    </div>

    <!-- Management Modal -->
    <div id="managementModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('managementModal')">&times;</span>
            <div class="tabs">
                <div class="tab active" onclick="switchTab('users')">Users</div>
                <div class="tab" onclick="switchTab('books')">Books</div>
            </div>
            
            <div id="usersTab" class="tab-content active">
                <h3>👥 Manage Users</h3>
                <div style="overflow-x: auto;">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Users will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="booksTab" class="tab-content">
                <h3>📚 Manage Books</h3>
                <div style="overflow-x: auto;">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Available/Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="booksTableBody">
                            <!-- Books will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <footer style="text-align: center; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-top: 2rem;">
    <p style="margin: 0; font-size: 14px;">Library Portal of Samba Institute</p>
</footer>                        
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize forms
            document.getElementById('addUserForm').addEventListener('submit', handleAddUser);
            document.getElementById('addBookForm').addEventListener('submit', handleAddBook);
        });

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function toggleCopiesField(select) {
            const copiesField = document.getElementById('copiesField');
            if (select.value === 'digital') {
                copiesField.style.display = 'none';
            } else {
                copiesField.style.display = 'block';
            }
        }

        async function handleAddUser(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const userData = Object.fromEntries(formData);
            
            if (userData.password !== userData.confirm_password) {
                showAlert('error', 'Passwords do not match');
                return;
            }
            
            try {
                const response = await fetch('../api/add-user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(userData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    closeModal('addUserModal');
                    event.target.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error adding user:', error);
                showAlert('error', 'Error adding user');
            }
        }

        async function handleAddBook(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const bookData = Object.fromEntries(formData);
            
            // Set available copies equal to total copies for new books
            if (bookData.book_type === 'physical') {
                bookData.available_copies = bookData.total_copies || 1;
            } else {
                bookData.total_copies = 1;
                bookData.available_copies = 1;
            }
            
            try {
                const response = await fetch('../api/add-book.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(bookData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    closeModal('addBookModal');
                    event.target.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error adding book:', error);
                showAlert('error', 'Error adding book');
            }
        }

        async function viewUsers() {
    openModal('managementModal');
    switchTab('users');
    
    try {
        const response = await fetch('../api/users.php');
        const users = await response.json();
        
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = users.map(user => {
            const statusClass = user.is_active ? 'status-active' : 'status-inactive';
            const statusText = user.is_active ? 'Active' : 'Inactive';
            const toggleText = user.is_active ? 'Deactivate' : 'Activate';
            
            return `
                <tr>
                    <td>${user.full_name}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.user_type}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button onclick="toggleUserStatus(${user.id}, ${user.is_active})" 
                                    style="background: ${user.is_active ? '#f44336' : '#4caf50'}; color: white; padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                                ${toggleText}
                            </button>
                            <button onclick="deleteUser(${user.id}, '${user.full_name.replace(/'/g, "\\'")}')" 
                                    style="background: #dc2626; color: white; padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading users:', error);
        showAlert('error', 'Error loading users');
    }
}

async function viewBooks() {
    openModal('managementModal');
    switchTab('books');
    
    try {
        const response = await fetch('../api/books-admin.php');
        const books = await response.json();
        
        const tbody = document.getElementById('booksTableBody');
        tbody.innerHTML = books.map(book => `
            <tr>
                <td>${book.title}</td>
                <td>${book.author}</td>
                <td>${book.category}</td>
                <td>${book.book_type}</td>
                <td>${book.book_type === 'physical' ? `${book.available_copies}/${book.total_copies}` : 'Digital'}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="toggleBookStatus(${book.id}, ${book.is_active})" 
                                style="background: ${book.is_active ? '#f44336' : '#4caf50'}; color: white; padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                            ${book.is_active ? 'Deactivate' : 'Activate'}
                        </button>
                        <button onclick="deleteBook(${book.id}, '${book.title.replace(/'/g, "\\'")}')" 
                                style="background: #dc2626; color: white; padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading books:', error);
        showAlert('error', 'Error loading books');
    }
}

// New delete user function
async function deleteUser(userId, userName) {
    if (!confirm(`⚠️ WARNING: Are you absolutely sure you want to PERMANENTLY DELETE user "${userName}"?\n\nThis action cannot be undone and will remove:\n- User account\n- All associated lending records\n- All activity logs\n- All reservations\n\nType "DELETE" to confirm:`)) {
        return;
    }
    
    const confirmText = prompt('Please type "DELETE" to confirm permanent deletion:');
    if (confirmText !== 'DELETE') {
        showAlert('error', 'Deletion cancelled - confirmation text did not match');
        return;
    }
    
    try {
        const response = await fetch('../api/delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            viewUsers(); // Refresh the users list
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        showAlert('error', 'Error deleting user');
    }
}

// New delete book function
async function deleteBook(bookId, bookTitle) {
    if (!confirm(`⚠️ WARNING: Are you absolutely sure you want to PERMANENTLY DELETE book "${bookTitle}"?\n\nThis action cannot be undone and will remove:\n- Book record\n- All associated lending records\n- All reservations for this book\n\nType "DELETE" to confirm:`)) {
        return;
    }
    
    const confirmText = prompt('Please type "DELETE" to confirm permanent deletion:');
    if (confirmText !== 'DELETE') {
        showAlert('error', 'Deletion cancelled - confirmation text did not match');
        return;
    }
    
    try {
        const response = await fetch('../api/delete-book.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ book_id: bookId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            viewBooks(); // Refresh the books list
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        console.error('Error deleting book:', error);
        showAlert('error', 'Error deleting book');
    }
}

        function switchTab(tabName) {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            event.target.classList.add('active');
            document.getElementById(tabName + 'Tab').classList.add('active');
        }

        async function toggleUserStatus(userId, currentStatus) {
            const action = currentStatus ? 'deactivate' : 'activate';
            
            if (!confirm(`Are you sure you want to ${action} this user?`)) {
                return;
            }
            
            try {
                const response = await fetch('../api/toggle-user-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId, status: !currentStatus })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    viewUsers(); // Refresh the users list
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error toggling user status:', error);
                showAlert('error', 'Error updating user status');
            }
        }

        async function toggleBookStatus(bookId, currentStatus) {
            const action = currentStatus ? 'deactivate' : 'activate';
            
            if (!confirm(`Are you sure you want to ${action} this book?`)) {
                return;
            }
            
            try {
                const response = await fetch('../api/toggle-book-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId, status: !currentStatus })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    viewBooks(); // Refresh the books list
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                console.error('Error toggling book status:', error);
                showAlert('error', 'Error updating book status');
            }
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
    </script>
</body>
</html>