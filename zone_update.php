<?php
require_once "includes/db_connect.php";

$error = "";
$zone = null;
$zoneID = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

try {
    if ($zoneID > 0) {
        // ✅ Fetch zone data with error handling
        $stmt = $conn->prepare("SELECT zoneName, capacity, role FROM Zone WHERE zoneID = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error); // 🔐 Error handling
        }

        $stmt->bind_param("i", $zoneID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Zone not found.";
        } else {
            $zone = $result->fetch_assoc();

            //  Process form if submitted
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $zoneName = trim($_POST["zoneName"]);
                $capacity = intval($_POST["capacity"]);
                $role = $_POST["role"];

                if (!empty($zoneName) && $capacity >= 0 && in_array($role, ['student', 'staff', 'visitor'])) {

                    //  Begin transaction for concurrency control
                    $conn->begin_transaction();

                    $updateStmt = $conn->prepare("UPDATE Zone SET zoneName = ?, capacity = ?, role = ? WHERE zoneID = ?");
                    if (!$updateStmt) {
                        throw new Exception("Prepare failed: " . $conn->error); //  Error handling
                    }

                    $updateStmt->bind_param("sisi", $zoneName, $capacity, $role, $zoneID);

                    if ($updateStmt->execute()) {
                        $conn->commit(); // 🔄 Commit the transaction if successful
                        header("Location: admin-logs.php");
                        exit();
                    } else {
                        $conn->rollback(); // 🔄 Roll back on failure
                        $error = "Failed to update zone: " . $updateStmt->error;
                    }
                } else {
                    $error = "Please fill all fields correctly.";
                }
            }
        }
    }
} catch (Exception $e) {
    $conn->rollback(); // Rollback if any uncaught error happens
    $error = "An error occurred: " . $e->getMessage(); // 🔐 Catch general DB/logic errors
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Zone</title>
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
    <?php if (!$zone): ?>
        <h2>No Zone Selected</h2>
        <p class="message error"><?= htmlspecialchars($error ?: "Please select a zone to update.") ?></p>
        <a class="back-link" href="admin.php">← Back to Admin Panel</a>
    <?php else: ?>
        <h2>Update Zone</h2>
        <form method="post">
            <input type="text" name="zoneName" value="<?= htmlspecialchars($zone["zoneName"]) ?>" placeholder="Zone Name" required>
            <input type="number" name="capacity" value="<?= htmlspecialchars($zone["capacity"]) ?>" placeholder="Capacity" required>

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="student" <?= $zone["role"] === "student" ? "selected" : "" ?>>Student</option>
                <option value="staff" <?= $zone["role"] === "staff" ? "selected" : "" ?>>Staff</option>
                <option value="visitor" <?= $zone["role"] === "visitor" ? "selected" : "" ?>>Visitor</option>
            </select>

            <button type="submit">Update Zone</button>
            <?php if ($error): ?>
                <p class="message error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
        <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
    <?php endif; ?>
</div>

</body>
</html>