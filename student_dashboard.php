<?php
session_start();
require_once "includes/db_connect.php";

// Error logging
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline", 0);
});

$error = "";

// ✅ Verify login and role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login/student-login.php");
    exit();
}

// ✅ Get session details
$studentID = $_SESSION["user_id"];  // FIX: Should be user_id not student_id
$role = $_SESSION["role"];

try {
    // ✅ Get assigned zone
    $zoneStmt = $conn->prepare("SELECT * FROM Zone WHERE role = ?");
    $zoneStmt->bind_param("s", $role);
    $zoneStmt->execute();
    $zoneResult = $zoneStmt->get_result();
    $zone = $zoneResult->fetch_assoc();

    if (!$zone) {
        die("No zone assigned to this role.");
    }

    // ✅ Get real-time available space count
    $availableStmt = $conn->prepare("SELECT COUNT(*) AS availableCount FROM ParkingSpace WHERE zoneID = ? AND status = 'available'");
    $availableStmt->bind_param("i", $zone["zoneID"]);
    $availableStmt->execute();
    $availableResult = $availableStmt->get_result();
    $availableCount = $availableResult->fetch_assoc()["availableCount"];

    // ✅ Check if student is already checked in
    $activeStmt = $conn->prepare("SELECT * FROM Session WHERE userID = ? AND role = ? AND checkoutTime IS NULL");
    $activeStmt->bind_param("is", $studentID, $role);
    $activeStmt->execute();
    $activeSession = $activeStmt->get_result()->fetch_assoc();

    // ✅ Handle Check-in
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkin_space_id"])) {
        if ($activeSession) {
            $error = "You are already checked in. Please check out first.";
        } else {
            $conn->begin_transaction();

            $spaceID = intval($_POST["checkin_space_id"]);

            $updateSpace = $conn->prepare("UPDATE ParkingSpace SET status = 'occupied' WHERE spaceID = ?");
            $updateSpace->bind_param("i", $spaceID);
            $updateSpace->execute();

            $updateZone = $conn->prepare("UPDATE Zone SET availableSpace = availableSpace - 1 WHERE zoneID = ?");
            $updateZone->bind_param("i", $zone["zoneID"]);
            $updateZone->execute();

            $insertSession = $conn->prepare("INSERT INTO Session (userID, role, spaceID) VALUES (?, ?, ?)");
            $insertSession->bind_param("isi", $studentID, $role, $spaceID);
            $insertSession->execute();

            $conn->commit();
            header("Location: student_dashboard.php");
            exit();
        }
    }

    // ✅ Handle Check-out
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkout_space_id"]) && $activeSession) {
        $spaceID = intval($_POST["checkout_space_id"]);

        if ($spaceID === intval($activeSession["spaceID"])) {
            $conn->begin_transaction();

            $freeSpace = $conn->prepare("UPDATE ParkingSpace SET status = 'available' WHERE spaceID = ?");
            $freeSpace->bind_param("i", $spaceID);
            $freeSpace->execute();

            $updateZone = $conn->prepare("UPDATE Zone SET availableSpace = availableSpace + 1 WHERE zoneID = ?");
            $updateZone->bind_param("i", $zone["zoneID"]);
            $updateZone->execute();

            $endSession = $conn->prepare("UPDATE Session SET checkoutTime = NOW() WHERE sessionID = ?");
            $endSession->bind_param("i", $activeSession["sessionID"]);
            $endSession->execute();

            $conn->commit();
            header("Location: student_dashboard.php");
            exit();
        }
    }

    // ✅ Fetch spaces
    $spaceStmt = $conn->prepare("SELECT * FROM ParkingSpace WHERE zoneID = ?");
    $spaceStmt->bind_param("i", $zone["zoneID"]);
    $spaceStmt->execute();
    $spaces = $spaceStmt->get_result();

} catch (Throwable $e) {
    $conn->rollback();
    error_log("Exception: " . $e->getMessage(), 0);
    $error = "An unexpected error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/availability.css">
</head>
<body>

<a href="logout.php" class="btn danger" style="float:right; margin: 1rem;">Logout</a>

<div class="dashboard-container">
    <h2>Welcome, Student</h2>
        <h3><?= htmlspecialchars($zone["zoneName"]) ?> — Available: <?= $availableCount ?> / <?= $zone["capacity"] ?></h3>

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

    <section class="dashboard-section">
        <h2>Feedback</h2>
        <a href="feedback.php" class="btn feedback-btn">💬 Give Feedback</a>

        <h2>Parking History</h2>
        <a href="user_report.php" class="btn">📄 View My Parking Report</a>
    </section>
</div>

</body>
</html>

