<?php
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $query = "INSERT INTO Student (username, password, email, phone) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $password, $email, $phone);

    if ($stmt->execute()) {
        header("Location: ../login/student_login.php");
        exit();
    } else {
        $error = "Signup failed.";
    }
}
?>
<!-- HTML form -->
<form method="post">
    <h2>Student Signup</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="phone" placeholder="Phone" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Sign Up</button>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</form>
