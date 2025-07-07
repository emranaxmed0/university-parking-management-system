<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["user_id"];
$role = $_SESSION["role"];

$stmt = $conn->prepare("SELECT s.sessionID, s.spaceID, z.zoneName, s.checkinTime, s.checkoutTime 
                        FROM Session s 
                        JOIN ParkingSpace ps ON s.spaceID = ps.spaceID 
                        JOIN Zone z ON ps.zoneID = z.zoneID
                        WHERE s.userID = ? AND s.role = ?
                        ORDER BY s.checkinTime DESC");
$stmt->bind_param("is", $userID, $role);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking History</title>
    <link rel="stylesheet" href="css/availability.css">
</head>
<body>
<div class="dashboard-container">
    <h2>My Parking History</h2>
    <table style="width: 100%; background-color: #1a1a1a; color: white; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Zone</th>
                <th>Space ID</th>
                <th>Check-In</th>
                <th>Check-Out</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['sessionID'] ?></td>
                    <td><?= htmlspecialchars($row['zoneName']) ?></td>
                    <td><?= $row['spaceID'] ?></td>
                    <td><?= $row['checkinTime'] ?></td>
                    <td><?= $row['checkoutTime'] ?: 'Still Parked' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
