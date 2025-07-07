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
    <style>
        .feedback-container {
            max-width: 1000px;
            margin: 100px auto;
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 10px;
            color: white;
            box-shadow: 0 0 20px rgba(255, 127, 39, 0.3);
        }

        .feedback-container h2 {
            text-align: center;
            color: #ff7f27;
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #222;
        }

        th, td {
            padding: 12px;
            border: 1px solid #444;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #ff7f27;
        }

        tr:nth-child(even) {
            background-color: #2c2c2c;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: #ff7f27;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error-message {
            text-align: center;
            color: red;
            margin-top: 1rem;
            font-weight: bold;
        }
    </style>
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
