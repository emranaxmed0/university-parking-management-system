<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "staff") {
    header("Location: login/staff-login.php");
    exit();
}

$userID = $_SESSION["user_id"];
$role = $_SESSION["role"];
$error = "";
$zone = null;
$activeSession = null;
$spaces = [];

try {
    // Get zone for staff
    $zoneStmt = $conn->prepare("SELECT * FROM Zone WHERE role = ?");
    if (!$zoneStmt) throw new Exception("Prepare failed: " . $conn->error);
    $zoneStmt->bind_param("s", $role);
    $zoneStmt->execute();
    $zoneResult = $zoneStmt->get_result();
    $zone = $zoneResult->fetch_assoc();

    if (!$zone) {
        throw new Exception("No zone assigned to this role.");
    }

    // Check for active session
    $activeStmt = $conn->prepare("SELECT * FROM Session WHERE userID = ? AND role = ? AND checkoutTime IS NULL");
    if (!$activeStmt) throw new Exception("Prepare failed: " . $conn->error);
    $activeStmt->bind_param("is", $userID, $role);
    $activeStmt->execute();
    $activeSession = $activeStmt->get_result()->fetch_assoc();

    // Handle check-in
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkin_space_id"])) {
        if ($activeSession) {
            $error = "You are already checked in. Please check out first.";
        } else {
            $spaceID = intval($_POST["checkin_space_id"]);
            $conn->begin_transaction();

            // Mark the space as occupied
            $updateSpace = $conn->prepare("UPDATE ParkingSpace SET status = 'occupied' WHERE spaceID = ?");
            if (!$updateSpace) throw new Exception("Prepare failed: " . $conn->error);
            $updateSpace->bind_param("i", $spaceID);
            $updateSpace->execute();

            // Decrement available space
            $updateZone = $conn->prepare("UPDATE Zone SET availableSpace = availableSpace - 1 WHERE zoneID = ?");
            if (!$updateZone) throw new Exception("Prepare failed: " . $conn->error);
            $updateZone->bind_param("i", $zone["zoneID"]);
            $updateZone->execute();

            // Insert new session
            $insertSession = $conn->prepare("INSERT INTO Session (userID, role, spaceID) VALUES (?, ?, ?)");
            if (!$insertSession) throw new Exception("Prepare failed: " . $conn->error);
            $insertSession->bind_param("isi", $userID, $role, $spaceID);
            $insertSession->execute();

            $conn->commit();
            header("Location: staff_dashboard.php");
            exit();
        }
    }

    // Handle check-out
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkout_space_id"]) && $activeSession) {
        $spaceID = intval($_POST["checkout_space_id"]);

        if ($spaceID === intval($activeSession["spaceID"])) {
            $conn->begin_transaction();

            // Free the space
            $freeSpace = $conn->prepare("UPDATE ParkingSpace SET status = 'available' WHERE spaceID = ?");
            if (!$freeSpace) throw new Exception("Prepare failed: " . $conn->error);
            $freeSpace->bind_param("i", $spaceID);
            $freeSpace->execute();

            // Increment available space
            $updateZone = $conn->prepare("UPDATE Zone SET availableSpace = availableSpace + 1 WHERE zoneID = ?");
            if (!$updateZone) throw new Exception("Prepare failed: " . $conn->error);
            $updateZone->bind_param("i", $zone["zoneID"]);
            $updateZone->execute();

            // Update session checkout
            $endSession = $conn->prepare("UPDATE Session SET checkoutTime = NOW() WHERE sessionID = ?");
            if (!$endSession) throw new Exception("Prepare failed: " . $conn->error);
            $endSession->bind_param("i", $activeSession["sessionID"]);
            $endSession->execute();

            $conn->commit();
            header("Location: staff_dashboard.php");
            exit();
        }
    }

    // Fetch all parking spaces for the zone
    $spaceStmt = $conn->prepare("SELECT * FROM ParkingSpace WHERE zoneID = ?");
    if (!$spaceStmt) throw new Exception("Prepare failed: " . $conn->error);
    $spaceStmt->bind_param("i", $zone["zoneID"]);
    $spaceStmt->execute();
    $spaces = $spaceStmt->get_result();

} catch (Exception $e) {
    $conn->rollback();
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="css/availability.css">
</head>
<body>

<a href="logout.php" class="btn danger" style="float:right;">Logout</a>

<div class="dashboard-container">
    <h2>Welcome, Staff</h2>
    <h3><?= htmlspecialchars($zone["zoneName"]) ?> — Available: <?= $zone["availableSpace"] ?> / <?= $zone["capacity"] ?></h3>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="color: red; text-align: center; font-weight: bold;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="tile-grid">
        <?php while ($space = $spaces->fetch_assoc()): ?>
            <div class="tile">
                <h4>Space #<?= htmlspecialchars($space["spaceID"]) ?></h4>
                <p>Status: <?= htmlspecialchars($space["status"]) ?></p>
                <p>Type: <?= htmlspecialchars($space["type"]) ?></p>

                <?php if ($space["status"] === "available"): ?>
                    <form method="post">
                        <input type="hidden" name="checkin_space_id" value="<?= $space["spaceID"] ?>">
                        <button type="submit" class="btn">Check In</button>
                    </form>
                <?php elseif ($activeSession && $activeSession["spaceID"] == $space["spaceID"]): ?>
                    <form method="post">
                        <input type="hidden" name="checkout_space_id" value="<?= $space["spaceID"] ?>">
                        <button type="submit" class="btn danger">Check Out</button>
                    </form>
                <?php else: ?>
                    <p style="color: #ccc;">Occupied</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
