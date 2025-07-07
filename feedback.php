<?php
session_start();
require_once "includes/db_connect.php";

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["user_id"];
$role = $_SESSION["role"];
$error = $success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $feedbackText = trim($_POST["feedback"]);

    if (!empty($feedbackText)) {
        $stmt = $conn->prepare("INSERT INTO Feedback (userID, role, feedbackText) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userID, $role, $feedbackText);

        if ($stmt->execute()) {
            $success = "Thank you for your feedback!";
        } else {
            $error = "Error submitting feedback. Please try again.";
        }
    } else {
        $error = "Please enter your feedback before submitting.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body>
<div class="feedback-container">
    <h2>We'd love to hear from you!</h2>
    <p>Submit your feedback about your parking experience below.</p>

    <?php if ($success): ?>
        <p class="success-message"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <textarea name="feedback" placeholder="Write your feedback here..." rows="6" required></textarea>
        <button type="submit">Submit Feedback</button>
    </form>

    <a class="back-link" href="<?= $role ?>_dashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>
