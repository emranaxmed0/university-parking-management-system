<?php
require_once "../includes/db_connect.php";

// Error Handling Setup
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline", 0);
});

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        // Begin Transaction for Concurrency Control
        $conn->begin_transaction();

        $sql = "INSERT INTO Staff (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $conn->commit(); // Commit transaction
            header("Location: ../login/staff-login.php");
            exit();
        } else {
            $conn->rollback(); // Rollback on failure
            $error = "Signup failed. Please try again.";
        }
    }
} catch (Throwable $e) {
    error_log("Exception: " . $e->getMessage(), 0);
    $conn->rollback(); // Ensure rollback on error
    $error = "An unexpected error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Signup</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>
<body>
    <div class="form-container">
        <form method="POST">
            <h2>Staff Sign Up</h2>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Sign Up</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <p>Already have an account? <a href="../login/staff-login.php">Log in here</a></p>
        </form>
    </div>
</body>
</html>
