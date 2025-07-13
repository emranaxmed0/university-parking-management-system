<?php
session_start();
require_once "includes/db_connect.php";

// Fetch check-in logs
$sql = "SELECT 
            s.sessionID,
            s.userID,
            s.role,
            s.spaceID,
            z.zoneName,
            s.checkinTime,
            s.checkoutTime
        FROM Session s
        JOIN ParkingSpace ps ON s.spaceID = ps.spaceID
        JOIN Zone z ON ps.zoneID = z.zoneID
        ORDER BY s.checkinTime DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Summary Report</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<?php include 'templates/nav.php'; ?>

<div class="admin-container">
    <h2>🧾 User Check-In/Out Logs</h2>

    <table>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>User ID</th>
                <th>Role</th>
                <th>Zone</th>
                <th>Space ID</th>
                <th>Check-in Time</th>
                <th>Check-out Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["sessionID"]) ?></td>
                        <td><?= htmlspecialchars($row["userID"]) ?></td>
                        <td><?= htmlspecialchars($row["role"]) ?></td>
                        <td><?= htmlspecialchars($row["zoneName"]) ?></td>
                        <td><?= htmlspecialchars($row["spaceID"]) ?></td>
                        <td><?= htmlspecialchars($row["checkinTime"]) ?></td>
                        <td><?= $row["checkoutTime"] ? htmlspecialchars($row["checkoutTime"]) : "<em>Active</em>" ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">No session logs available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="admin-logs.php" class="btn">← Back to Dashboard</a>
</div>

<?php include 'templates/footer.php'; ?>
</body>
</html>
