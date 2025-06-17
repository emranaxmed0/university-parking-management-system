<?php
session_start();
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $query = "SELECT * FROM Visitor WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["visitorID"];
            $_SESSION["role"] = "visitor";
            header("Location: ../visitor_dashboard.php");
            exit();
        }
    }

    $error = "Invalid username or password.";
}
?>

<link rel="stylesheet" href="../css/login.css">
<form method="post">
    <h2>Visitor Login</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p>Don't have an account? <a href="../signup/visitor-signup.php">Sign up here</a></p>
</form>
