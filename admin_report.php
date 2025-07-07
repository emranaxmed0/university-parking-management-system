<?php
session_start();
require_once "includes/db_connect.php";


// Total sessions
$totalSessions = $conn->query("SELECT COUNT(*) as total FROM Session")->fetch_assoc()["total"];
$activeSessions = $conn->query("SELECT COUNT(*) as active FROM Session WHERE checkoutTime IS NULL")->fetch_assoc()["active"];
$totalUsers = $conn->query("SELECT 
                                (SELECT COUNT(*) FROM Student) + 
                                (SELECT COUNT(*) FROM Staff) + 
                                (SELECT COUNT(*) FROM Visitor) AS total")->fetch_assoc()["total"];

$zones = $conn->query("SELECT * FROM Zone");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Report</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-container">
    <h2>Admin Summary Report</h2>

    <ul style="line-height: 2;">
        <li>Total Users: <?= $totalUsers ?></li>
        <li>Total Sessions: <?= $totalSessions ?></li>
        <li>Active Sessions (Currently Parked): <?= $activeSessions ?></li>
    </ul>

    <h3>Zone Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Zone Name</th>
                <th>Capacity</th>
                <th>Available</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($zone = $zones->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($zone["zoneName"]) ?></td>
                    <td><?= $zone["capacity"] ?></td>
                    <td><?= $zone["availableSpace"] ?></td>
                    <td><?= ucfirst($zone["Role"]) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
