<?php
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = $_POST["email"];

    $stmt = $conn->prepare("INSERT INTO Visitor (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);


    if ($stmt->execute()) {
        header("Location: ../login/visitor-login.php");
        exit();
    } else {
        $error = "Signup failed. Try a different username.";
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
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p>Already have an account? <a href="../login/visitor-login.php">Login here</a></p>
</form>
