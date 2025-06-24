<?php
session_start();
require_once "includes/db_connect.php";

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

// Fetch all zones
$zoneQuery = "SELECT * FROM Zone";
$zones = $conn->query($zoneQuery);

// Fetch all parking spaces with zone info
$spaceQuery = "SELECT ps.spaceID, ps.status, ps.type, z.zoneName 
               FROM ParkingSpace ps 
               JOIN Zone z ON ps.zoneID = z.zoneID";
$spaces = $conn->query($spaceQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<header class="admin-header">
    <?php include 'templates/nav.php'; ?>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($username) ?></p>
</header>

<main class="admin-container">

    <!-- Zone Management Section -->
    <section class="admin-section">
        <h2>Zones</h2>
        <div class="action-buttons">
            <a href="zone_add.php" class="btn">➕ Add Zone</a>
            <a href="zone_update.php" class="btn">✏️ Update Zone</a>
            <a href="zone_delete.php" class="btn">🗑️ Remove Zone</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Zone Name</th>
                    <th>Capacity</th>
                    <th>Available Space</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $zones->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['zoneName']) ?></td>
                        <td><?= htmlspecialchars($row['capacity']) ?></td>
                        <td><?= htmlspecialchars($row['availableSpace']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Parking Space Management Section -->
    <section class="admin-section">
        <h2>Parking Spaces</h2>
        <div class="action-buttons">
            <a href="space_add.php" class="btn">➕ Add Space</a>
            <a href="space_update.php" class="btn">✏️ Update Space</a>
            <a href="space_delete.php" class="btn">🗑️ Remove Space</a>
        </div>
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
                <?php while ($row = $spaces->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['spaceID']) ?></td>
                        <td><?= htmlspecialchars($row['zoneName']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Feedback Viewer -->
    <section class="admin-section">
        <h2>User Feedback</h2>
        <a href="view_feedback.php" class="btn feedback-btn">💬 View Feedback</a>
    </section>

</main>

<?php include 'templates/footer.php'; ?>
</body>
</html>
