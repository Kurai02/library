<?php
session_start();
require_once "logic/database.php";  // adjust path if needed

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['uname']);
    $password = $_POST['psw'];
    $role = $_POST['user_role'] ?? 'user'; // default to user if none selected
    
    $errors = [];

    // Basic validations
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if (!in_array($role, ['user', 'admin'])) {
        $errors[] = "Invalid role selected.";
    }
    
    open_connection();

    // Check if username already exists
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE UserName = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $errors[] = "Username already taken.";
    }
    $stmt->close();

    if (empty($errors)) {
        // Hash password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $insert_stmt = $conn->prepare("INSERT INTO Users (UserName, PASSWORD, Role) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($insert_stmt->execute()) {
            $_SESSION['username'] = $username;
            header("Location: index.php"); // Redirect to homepage or login page
            exit;
        } else {
            $errors[] = "Error inserting user into database.";
        }
        $insert_stmt->close();
    }

    close_connection();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h2>Register the User</h2>
        <?php
        if (!empty($errors)) {
            echo "<ul style='color:red;'>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        }
        ?>
        <a href="register_form.php">Go back to Register Form</a>
    </div>
</body>
</html>
