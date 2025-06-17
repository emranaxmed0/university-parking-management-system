<?php
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $query = "INSERT INTO Visitor (username, password, phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $password, $phone);

    if ($stmt->execute()) {
        header("Location: ../login/visitor_login.php");
        exit();
    } else {
        $error = "Signup failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visitor Signup</title>
</head>
<body>
    <form method="post">
        <h2>Visitor Signup</h2>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="text" name="phone" placeholder="Phone" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Sign Up</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</body>
</html>
