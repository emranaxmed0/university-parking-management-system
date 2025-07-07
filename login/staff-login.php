<?php
session_start();
require_once "../includes/db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
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
                $_SESSION["user_id"] = $user["staffID"];
                $_SESSION["role"] = "staff";
                $conn->commit();
                header("Location: ../staff_dashboard.php");
                exit();
            }
        }

        $conn->commit();
        $error = "Invalid username or password.";
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        $error = "An error occurred during login. Please try again later.";
    }
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
            <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        </form>
        <p class="signup-link">Don't have an account? <a href="../signup/staff-signup.php">Sign up here</a></p>
    </div>
</body>
</html>


