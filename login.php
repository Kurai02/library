<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .user-types {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1.5rem;
        }

        .user-type {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            margin: 0 0.25rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .user-type:hover, .user-type.active {
            border-color: #667eea;
            background: #f0f2ff;
            color: #667eea;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .demo-credentials {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.85rem;
        }

        .demo-credentials h4 {
            color: #1976d2;
            margin-bottom: 0.5rem;
        }

        .loading {
            display: none;
            text-align: center;
            color: #666;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .user-types {
                flex-direction: column;
            }
            
            .user-type {
                margin: 0.25rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>SAMBA Institute</h1>
            <h1>Library Portal</h1>
            <p>Sign in to your account</p>
        </div>

        <div id="alertContainer"></div>

        <form id="loginForm" method="POST" action="authenticate.php">
            <div class="user-types">
                <div class="user-type active" data-type="student">
                    👨‍🎓 Student
                </div>
                <div class="user-type" data-type="staff">
                    👨‍💼 Staff
                </div>
                <div class="user-type" data-type="admin">
                    👨‍💻 Admin
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username / Email</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="hidden" id="userType" name="user_type" value="student">

            <button type="submit" class="btn-primary">
                <span class="btn-text">Sign In</span>
                <div class="loading">
                    <div class="spinner"></div>
                    Signing in...
                </div>
            </button>
        </form>

        <div class="demo-credentials">
            <h4>Demo Credentials:</h4>
            <strong>Admin:</strong> admin / admin123<br>
            <strong>Staff:</strong> staff / staff123<br>
            <strong>Student:</strong> student / student123
        </div>
    </div>

    <script>
        // User type selection
        document.querySelectorAll('.user-type').forEach(type => {
            type.addEventListener('click', function() {
                document.querySelectorAll('.user-type').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('userType').value = this.dataset.type;
            });
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btnText = document.querySelector('.btn-text');
            const loading = document.querySelector('.loading');
            
            btnText.style.display = 'none';
            loading.style.display = 'block';
        });

        // Add smooth animations
        window.addEventListener('load', function() {
            document.querySelector('.login-container').style.opacity = '0';
            document.querySelector('.login-container').style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                document.querySelector('.login-container').style.transition = 'all 0.6s ease';
                document.querySelector('.login-container').style.opacity = '1';
                document.querySelector('.login-container').style.transform = 'translateY(0)';
            }, 100);
        });

        // Handle URL parameters for messages
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const type = urlParams.get('type') || 'error';
        
        if (message) {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `<div class="alert alert-${type}">${decodeURIComponent(message)}</div>`;
        }
    </script>

    <?php
    // PHP code for handling logout message
    if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
        echo "<script>
            document.getElementById('alertContainer').innerHTML = '<div class=\"alert alert-success\">You have been logged out successfully.</div>';
        </script>";
    }
    ?>
</body>
</html>