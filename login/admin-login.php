<?php
session_start();
require_once "../includes/db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT adminID, username, password FROM adminlog WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($adminID, $dbUsername, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $adminID;
            $_SESSION["username"] = $dbUsername;
            $_SESSION["role"] = "admin";

            header("Location: ../admin-logs.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="login-container">
    <form method="post">
        <h2>Admin Login</h2>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
        
        <?php if (!empty($error)): ?>
            <p class="error" style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <p><a href="../index.php">← Back to Home</a></p>
    </form>
    <p class="signup-link">Don't have an account? <a href="../signup/admin-signup.php">Sign up here</a></p>
</div>
</body>
</html>
