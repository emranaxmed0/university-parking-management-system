<?php
session_start();
require_once "includes/db_connect.php";

// Enable mysqli exceptions and custom error logging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline", 0);
});

$feedback = [];
$error = "";

try {
    $stmt = $conn->prepare("SELECT * FROM Feedback ORDER BY timestamp DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
} catch (Throwable $e) {
    $error = "Failed to load feedback. Please try again later.";
    error_log("Exception in view_feedback.php: " . $e->getMessage(), 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedback</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body>

<div class="feedback-container">
    <h2>User Feedback</h2>

    <?php if (!empty($feedback)): ?>
        <table>
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>User ID</th>
                    <th>Role</th>
                    <th>Message</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedback as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['feedbackID']) ?></td>
                        <td><?= htmlspecialchars($row['userID']) ?></td>
                        <td><?= htmlspecialchars($row['Role']) ?></td>
                        <td><?= htmlspecialchars($row['feedbackText']) ?></td>
                        <td><?= htmlspecialchars($row['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($error): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php else: ?>
        <p style="text-align:center;">No feedback found.</p>
    <?php endif; ?>

    <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
</div>

</body>
</html>
