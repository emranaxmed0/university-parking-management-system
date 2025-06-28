<?php
session_start();
require_once "includes/db_connect.php";

// Handle delete zone
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_zone_id"])) {
    $zoneId = intval($_POST["delete_zone_id"]);
    $stmt = $conn->prepare("DELETE FROM Zone WHERE zoneID = ?");
    $stmt->bind_param("i", $zoneId);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// Handle delete parking space
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_space_id"])) {
    $spaceId = intval($_POST["delete_space_id"]);
    $stmt = $conn->prepare("DELETE FROM ParkingSpace WHERE spaceID = ?");
    $stmt->bind_param("i", $spaceId);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// Fetch zones
$zoneQuery = "SELECT * FROM Zone";
$zones = $conn->query($zoneQuery);

// Fetch parking spaces
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
    <p>Welcome, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin' ?></p>
</header>

<main class="admin-container">

    <!-- Zone Management -->
    <section class="admin-section">
        <h2>Zones</h2>
        <div class="action-buttons">
            <a href="zone_add.php" class="btn">➕ Add Zone</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Zone Name</th>
                    <th>Capacity</th>
                    <th>Available Space</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($zone = $zones->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($zone['zoneName']) ?></td>
                        <td><?= htmlspecialchars($zone['capacity']) ?></td>
                        <td><?= htmlspecialchars($zone['availableSpace']) ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this zone?');" style="display:inline;">
                                <input type="hidden" name="delete_zone_id" value="<?= $zone['zoneID'] ?>">
                                <button type="submit" class="btn danger-btn">🗑️ Delete</button>
                            </form>
                            <a href="zone_update.php?id=<?= $zone['zoneID'] ?>" class="btn update-btn">✏️ Update</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Parking Space Management -->
    <section class="admin-section">
        <h2>Parking Spaces</h2>
        <div class="action-buttons">
            <a href="space_add.php" class="btn">➕ Add Space</a>
            <a href="space_update.php" class="btn">✏️ Update Space</a>
            <a href="view_space.php" class="btn">📋 View Spaces</a>
        </div>

    </section>

    <!-- Feedback -->
    <section class="admin-section">
        <h2>User Feedback</h2>
        <a href="view_feedback.php" class="btn feedback-btn">💬 View Feedback</a>
    </section>

</main>

 <?php include 'templates/footer.php'; ?>
</body>
</html>
