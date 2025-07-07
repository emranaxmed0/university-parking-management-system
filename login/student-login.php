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

        // Optional: Transaction for read consistency
        $conn->begin_transaction();

        $query = "SELECT * FROM Student WHERE username = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user["password"])) {
                $conn->commit(); // Commit on success
                $_SESSION["user_id"] = $user["studentID"];
                $_SESSION["role"] = "student";
                header("Location: ../student_dashboard.php");
                exit();
            }
        }

        $conn->rollback(); // Rollback on invalid login
        $error = "Invalid username or password.";
    }
} catch (Throwable $e) {
    error_log("Exception: " . $e->getMessage(), 0);
    $conn->rollback(); // Rollback on any error
    $error = "An unexpected error occurred. Please try again later.";
}
?>

<link rel="stylesheet" href="../css/login.css">
<form method="post">
    <h2>Student Login</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p>Don't have an account? <a href="../signup/student-signup.php">Sign up here</a></p>
</form>
