<?php
require_once "includes/db_connect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["zoneName"];
    $capacity = $_POST["capacity"];
    $query = "INSERT INTO Zone (zoneName, capacity, availableSpace) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $name, $capacity, $capacity);
    $stmt->execute();
    header("Location: admin.php");
}
?>

<link rel="stylesheet" href="css/forms.css">
<form method="post">
    <h2>Add New Zone</h2>
    <label>Zone Name:</label>
    <input type="text" name="zoneName" required>
    <label>Capacity:</label>
    <input type="number" name="capacity" required>
    <button type="submit">Add Zone</button>
</form>
