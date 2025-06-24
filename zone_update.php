<?php
require_once "includes/db_connect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["zoneID"];
    $name = $_POST["zoneName"];
    $capacity = $_POST["capacity"];
    $query = "UPDATE Zone SET zoneName = ?, capacity = ? WHERE zoneID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $name, $capacity, $id);
    $stmt->execute();
    header("Location: admin.php");
}
$zones = $conn->query("SELECT * FROM Zone");
?>

<link rel="stylesheet" href="css/forms.css">
<form method="post">
    <h2>Update Zone</h2>
    <label>Select Zone:</label>
    <select name="zoneID" required>
        <?php while ($z = $zones->fetch_assoc()): ?>
            <option value="<?= $z['zoneID'] ?>"><?= $z['zoneName'] ?></option>
        <?php endwhile; ?>
    </select>
    <label>New Name:</label>
    <input type="text" name="zoneName" required>
    <label>New Capacity:</label>
    <input type="number" name="capacity" required>
    <button type="submit">Update Zone</button>
</form>
