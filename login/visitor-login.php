<?php
session_start();
require_once "../includes/db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        $conn->begin_transaction();

        $query = "SELECT * FROM Visitor WHERE username = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["visitorID"];
                $_SESSION["role"] = "visitor";
                $conn->commit();
                header("Location: ../visitor_dashboard.php");
                exit();
            }
        }

        $conn->commit(); // Even if login fails, no DB changes were made — still commit to end transaction cleanly
        $error = "Invalid username or password.";
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        $error = "An error occurred during login. Please try again later.";
    }
}
?>

<link rel="stylesheet" href="../css/login.css">
<form method="post">
    <h2>Visitor Login</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
    <p>Don't have an account? <a href="../signup/visitor-signup.php">Sign up here</a></p>
</form>
