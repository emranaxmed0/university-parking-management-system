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

        $stmt = $conn->prepare("INSERT INTO Visitor (username, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $conn->commit(); // Commit on success
            header("Location: ../login/visitor-login.php");
            exit();
        } else {
            $conn->rollback(); // Rollback on failure
            $error = "Signup failed. Try a different username.";
        }
    }
} catch (Throwable $e) {
    error_log("Exception: " . $e->getMessage(), 0);
    $conn->rollback(); // Rollback on any caught exception
    $error = "An unexpected error occurred. Please try again later.";
}
?>

<link rel="stylesheet" href="../css/signup.css">
<form method="post">
    <h2>Visitor Sign Up</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Sign Up</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p>Already have an account? <a href="../login/visitor-login.php">Login here</a></p>
</form>
