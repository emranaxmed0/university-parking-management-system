<?php
session_start();
require_once "includes/db_connect.php";

// Ensure user is logged in and is a student
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["user_id"];
$role = $_SESSION["role"];
$error = "";

// Get zone for student
$zoneStmt = $conn->prepare("SELECT * FROM Zone WHERE role = ?");
$zoneStmt->bind_param("s", $role);
$zoneStmt->execute();
$zoneResult = $zoneStmt->get_result();
$zone = $zoneResult->fetch_assoc();

if (!$zone) {
    die("No zone assigned to this role.");
}

// Check for active session
$activeStmt = $conn->prepare("SELECT * FROM Session WHERE userID = ? AND role = ? AND checkoutTime IS NULL");
$activeStmt->bind_param("is", $userID, $role);
$activeStmt->execute();
$activeSession = $activeStmt->get_result()->fetch_assoc();

// Handle check-in
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkin_space_id"])) {
    if ($activeSession) {
        $error = "You are already checked in. Please check out first.";
    } else {
        $spaceID = intval($_POST["checkin_space_id"]);

        // Mark the space as occupied
        $updateSpace = $conn->prepare("UPDATE ParkingSpace SET status = 'occupied' WHERE spaceID = ?");
        $updateSpace->bind_param("i", $spaceID);
        $updateSpace->execute();

        // Reduce available space
        $updateZone = $conn->prepare("UPDATE Zone SET availableSpace = availableSpace - 1 WHERE zoneID = ?");
        $updateZone->bind_param("i", $zone["zoneID"]);
        $updateZone->execute();

        // Insert session
        $insertSession = $conn->prepare("INSERT INTO Session (userID, role, spaceID) VALUES (?, ?, ?)");
        $insertSession->bind_param("isi", $userID, $role, $spaceID);
        $insertSession->execute();

        header("Location: student_dashboard.php");
        exit();
    }
}

// Handle check-out
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkout_space_id"]) && $activeSession) {
    $spaceID = intval($_POST["checkout_space_id"]);

    if ($spaceID === intval($activeSession["spaceID"])) {
        // Free the space
        $freeSpace = $conn->prepare("UPDATE ParkingSpace SET status = 'available' WHERE spaceID = ?");
        $freeSpace->bind_param("i", $spaceID);
        $freeSpace->execute();

        // Increase available space
        $updateZone = $conn->prepare("UPDATE Zone SET availableSpace = availableSpace + 1 WHERE zoneID = ?");
        $updateZone->bind_param("i", $zone["zoneID"]);
        $updateZone->execute();

        // Update session checkout time
        $endSession = $conn->prepare("UPDATE Session SET checkoutTime = NOW() WHERE sessionID = ?");
        $endSession->bind_param("i", $activeSession["sessionID"]);
        $endSession->execute();

        header("Location: student_dashboard.php");
        exit();
    }
}

// Fetch all parking spaces for the zone
$spaceStmt = $conn->prepare("SELECT * FROM ParkingSpace WHERE zoneID = ?");
$spaceStmt->bind_param("i", $zone["zoneID"]);
$spaceStmt->execute();
$spaces = $spaceStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <a href="logout.php" class="btn danger" style="float:right;">Logout</a>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/availability.css">
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome, Student</h2>

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
