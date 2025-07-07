<?php
require_once "../includes/db_connect.php";

// Error Handling Setup
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline", 0);
});

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $email = $_POST["email"];

        // Begin Transaction for Concurrency Control
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO Student (username, password, email) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $username, $password, $email);

        if ($stmt->execute()) {
            $conn->commit(); // Commit successful insert
            header("Location: ../login/student-login.php");
            exit();
        } else {
            $conn->rollback(); // Rollback if insert fails
            $error = "Signup failed. Try a different username.";
        }
    }
} catch (Throwable $e) {
    error_log("Exception: " . $e->getMessage(), 0);
    $conn->rollback(); // Rollback on any error
    $error = "An unexpected error occurred. Please try again later.";
}
?>

<link rel="stylesheet" href="../css/signup.css">
<form method="post">
    <h2>Student Sign Up</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Sign Up</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p>Already have an account? <a href="../login/student-login.php">Login here</a></p>
</form>
