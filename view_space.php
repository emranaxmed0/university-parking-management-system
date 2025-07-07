<?php
require_once "includes/db_connect.php";

$error = "";
$spaces = [];

try {
    $query = "SELECT ps.spaceID, ps.status, ps.type, z.zoneName 
              FROM ParkingSpace ps
              JOIN Zone z ON ps.zoneID = z.zoneID";

    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Failed to fetch parking spaces: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $spaces[] = $row;
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
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
            <?php if ($error): ?>
                <tr>
                    <td colspan="4" style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></td>
                </tr>
            <?php elseif (empty($spaces)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No parking spaces found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($spaces as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['spaceID']) ?></td>
                        <td><?= htmlspecialchars($row['zoneName']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <a class="back-link" href="admin-logs.php">← Back to Admin Panel</a>
</div>

</body>
</html>
