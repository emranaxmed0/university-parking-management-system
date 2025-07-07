<?php
require_once "includes/db_connect.php";

$error = "";
$success = "";

try {
    // Fetch all zones and spaces with concurrency-safe reads
    $zones = $conn->query("SELECT zoneID, zoneName FROM Zone FOR UPDATE");
    $spaces = $conn->query("SELECT * FROM ParkingSpace FOR UPDATE");

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $spaceID = intval($_POST["spaceID"]);
        $zoneID = intval($_POST["zoneID"]);
        $status = $_POST["status"];
        $type = $_POST["type"];

        if ($spaceID && $zoneID && in_array($status, ['available', 'occupied']) && !empty($type)) {
            $conn->begin_transaction();

            $stmt = $conn->prepare("UPDATE ParkingSpace SET zoneID = ?, status = ?, type = ? WHERE spaceID = ?");
            $stmt->bind_param("issi", $zoneID, $status, $type, $spaceID);

            if ($stmt->execute()) {
                $conn->commit();
                $success = "Parking space updated successfully.";
            } else {
                $conn->rollback();
                $error = "Failed to update parking space: " . $conn->error;
            }
        } else {
            $error = "Please fill all fields correctly.";
        }
    }
} catch (Exception $e) {
    $conn->rollback();
    $error = "An unexpected error occurred: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Parking Space</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
<div class="form-container">
    <h2>Update Parking Space</h2>
    <form method="post">
        <select name="spaceID" required>
            <option value="">Select Space</option>
            <?php if ($spaces): while ($space = $spaces->fetch_assoc()): ?>
                <option value="<?= $space['spaceID'] ?>">ID: <?= $space['spaceID'] ?> (<?= $space['status'] ?>)</option>
            <?php endwhile; endif; ?>
        </select>

        <select name="zoneID" required>
            <option value="">Select Zone</option>
            <?php if ($zones): while ($zone = $zones->fetch_assoc()): ?>
                <option value="<?= $zone['zoneID'] ?>"><?= $zone['zoneName'] ?> (<?= $zone['zoneID'] ?>)</option>
            <?php endwhile; endif; ?>
        </select>

        <select name="status" required>
            <option value="">Select Status</option>
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
        </select>

        <input type="text" name="type" placeholder="Type (e.g. normal, disabled)" required>

        <button type="submit">Update Space</button>
        <?php if ($error): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p class="message success" style="color:limegreen;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
    </form>
    <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
</div>
</body>
</html>
