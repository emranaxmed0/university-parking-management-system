<?php
require_once "../includes/db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = $_POST["email"];

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO Visitor (username, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $username, $email, $password);

        if (!$stmt->execute()) {
            throw new Exception("Execution failed: " . $stmt->error);
        }

        $conn->commit();
        header("Location: ../login/visitor-login.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        $error = "Signup failed. Please try a different username or try again later.";
    }
}
?>

<link rel="stylesheet" href="../css/signup.css">
<form method="post">
    <h2>Visitor Sign Up</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Sign Up</button>
    <?php if (isset($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
    <p>Already have an account? <a href="../login/visitor-login.php">Login here</a></p>
</form>
