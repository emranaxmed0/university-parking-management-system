<?php
require_once "includes/db_connect.php";

$error = "";

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $zoneID = intval($_POST["zoneID"]);
        $zoneName = trim($_POST["zoneName"]);
        $capacity = intval($_POST["capacity"]);
        $role = $_POST["role"];

        if (!empty($zoneID) && !empty($zoneName) && $capacity >= 0 && in_array($role, ['student', 'staff', 'visitor'])) {

            //  Begin transaction for concurrency control
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO Zone (zoneID, zoneName, capacity, role) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error); // Error handling
            }

            $stmt->bind_param("isis", $zoneID, $zoneName, $capacity, $role);

            if ($stmt->execute()) {
                $conn->commit(); //  Commit if successful
                header("Location: admin-logs.php");
                exit();
            } else {
                $conn->rollback(); // Rollback on failure
                $error = "Failed to add zone: " . $stmt->error;
            }

        } else {
            $error = "Please fill all fields correctly.";
        }
    }
} catch (Exception $e) {
    //  Catch any unexpected errors and rollback
    $conn->rollback();
    $error = "An error occurred: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Zone</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 80px auto;
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 8px;
            color: white;
            box-shadow: 0 0 10px rgba(255, 127, 39, 0.2);
        }

        .form-container h2 {
            text-align: center;
            color: #ff7f27;
            margin-bottom: 1.5rem;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid #333;
            border-radius: 5px;
            background: #2c2c2c;
            color: white;
        }

        .form-container button {
            width: 100%;
            padding: 0.75rem;
            background-color: #ff7f27;
            color: #000;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-container .message {
            text-align: center;
            margin-top: 1rem;
            font-weight: bold;
        }

        .form-container .error {
            color: red;
        }

        a.back-link {
            display: inline-block;
            margin-top: 1rem;
            color: #ff7f27;
            text-align: center;
            display: block;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Zone</h2>
    <form method="post">
        <input type="number" name="zoneID" placeholder="Zone ID (must be unique)" required>
        <input type="text" name="zoneName" placeholder="Zone Name" required>
        <input type="number" name="capacity" placeholder="Capacity" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <option value="visitor">Visitor</option>
        </select>

        <button type="submit">Add Zone</button>
        <?php if ($error): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>
    <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
</div>

</body>
</html>
