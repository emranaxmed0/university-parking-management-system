<?php
session_start();
require_once "../includes/db_connect.php";

// Error Handling Setup
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline", 0);
});

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Begin Transaction for Read Consistency (optional here)
        $conn->begin_transaction();

        $query = "SELECT * FROM Staff WHERE username = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user["password"])) {
                $conn->commit(); // Commit if user found and password correct
                $_SESSION["user_id"] = $user["staffID"];
                $_SESSION["role"] = "staff";
                header("Location: ../staff_dashboard.php");
                exit();
            }
        }

        $conn->rollback(); // Rollback if login fails
        $error = "Invalid username or password.";
    }
} catch (Throwable $e) {
    error_log("Exception: " . $e->getMessage(), 0);
    $conn->rollback(); // Rollback on any exception
    $error = "An unexpected error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <form method="post">
            <h2>Staff Login</h2>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
     <p class="signup-link">Don't have an account? <a href="../signup/staff-signup.php">Sign up here</a></p>   
    </div>
</body>
</html>

