<?php
require_once "includes/db_connect.php";

$error = "";

// Error Handling Setup
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline", 0);
});

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_space_id"])) {
        $conn->begin_transaction();

        $spaceId = intval($_POST["delete_space_id"]);

        $stmt = $conn->prepare("SELECT zoneID, status FROM ParkingSpace WHERE spaceID = ?");
        $stmt->bind_param("i", $spaceId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($space = $result->fetch_assoc()) {
            $zoneID = $space['zoneID'];
            $status = $space['status'];

            $deleteStmt = $conn->prepare("DELETE FROM ParkingSpace WHERE spaceID = ?");
            $deleteStmt->bind_param("i", $spaceId);
            $deleteStmt->execute();

            $conn->query("UPDATE Zone SET capacity = capacity - 1 WHERE zoneID = $zoneID");
            if ($status === 'available') {
                $conn->query("UPDATE Zone SET availableSpace = availableSpace - 1 WHERE zoneID = $zoneID");
            }
        }

        $conn->commit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["spaceID"]) && !isset($_POST["delete_space_id"])) {
        $conn->begin_transaction();

        $spaceID = intval($_POST["spaceID"]);
        $zoneID = intval($_POST["zoneID"]);
        $status = $_POST["status"];
        $type = $_POST["type"];

        if (!empty($spaceID) && !empty($zoneID) && in_array($status, ['available', 'occupied']) && !empty($type)) {
            $stmt = $conn->prepare("INSERT INTO ParkingSpace (spaceID, zoneID, status, type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $spaceID, $zoneID, $status, $type);

            if ($stmt->execute()) {
                $conn->query("UPDATE Zone SET capacity = capacity + 1 WHERE zoneID = $zoneID");
                if ($status === 'available') {
                    $conn->query("UPDATE Zone SET availableSpace = availableSpace + 1 WHERE zoneID = $zoneID");
                }
                $conn->commit();
                header("Location: space_add.php");
                exit();
            } else {
                $conn->rollback();
                $error = "Failed to add parking space: " . $conn->error;
            }
        } else {
            $conn->rollback();
            $error = "Please fill all fields correctly.";
        }
    }

    $zonesResult = $conn->query("SELECT zoneID, zoneName FROM Zone");
    $spacesResult = $conn->query("SELECT ps.spaceID, ps.status, ps.type, z.zoneName 
                                  FROM ParkingSpace ps 
                                  JOIN Zone z ON ps.zoneID = z.zoneID");
} catch (Throwable $e) {
    error_log("Exception: " . $e->getMessage(), 0);
    $conn->rollback();
    $error = "An unexpected error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Parking Space</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
<div class="form-container">
    <h2>Add Parking Space</h2>
    <form method="post">
        <input type="number" name="spaceID" placeholder="Space ID (must be unique)" required>
        <select name="zoneID" required>
            <option value="">Select Zone</option>
            <?php while ($zone = $zonesResult->fetch_assoc()): ?>
                <option value="<?= $zone['zoneID'] ?>"><?= htmlspecialchars($zone['zoneName']) ?> (ID: <?= $zone['zoneID'] ?>)</option>
            <?php endwhile; ?>
        </select>
        <select name="status" required>
            <option value="">Select Status</option>
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
        </select>
        <input type="text" name="type" placeholder="Type (e.g. normal, disabled)" required>
        <button type="submit">Add Space</button>
        <?php if ($error): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>

    <h3 style="margin-top: 2rem; color:#ff7f27;">Current Spaces</h3>
    <table style="width:100%; margin-top:1rem;">
        <thead>
            <tr style="color:#ff7f27;">
                <th>Space ID</th>
                <th>Zone</th>
                <th>Status</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($space = $spacesResult->fetch_assoc()): ?>
                <tr>
                    <td><?= $space['spaceID'] ?></td>
                    <td><?= htmlspecialchars($space['zoneName']) ?></td>
                    <td><?= $space['status'] ?></td>
                    <td><?= $space['type'] ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this space?');">
                            <input type="hidden" name="delete_space_id" value="<?= $space['spaceID'] ?>">
                            <button type="submit" style="background:#c0392b;color:white;border:none;padding:6px 10px;border-radius:4px;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
</div>
</body>
</html>
