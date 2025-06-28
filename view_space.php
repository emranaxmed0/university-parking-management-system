<?php
require_once "includes/db_connect.php";

// Fetch parking spaces
$query = "SELECT ps.spaceID, ps.status, ps.type, z.zoneName 
          FROM ParkingSpace ps
          JOIN Zone z ON ps.zoneID = z.zoneID";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking Spaces</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="admin-container">
    <h2 style="color:#ff7f27;">All Parking Spaces</h2>
    <table>
        <thead>
            <tr>
                <th>Space ID</th>
                <th>Zone</th>
                <th>Status</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['spaceID']) ?></td>
                    <td><?= htmlspecialchars($row['zoneName']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
</div>

</body>
</html>
