<?php
require_once "includes/db_connect.php";
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline", 0);
});

$error = "";

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $zoneID = intval($_POST["zoneID"]);
        $zoneName = trim($_POST["zoneName"]);
        $capacity = intval($_POST["capacity"]);
        $role = $_POST["role"];

        if (!empty($zoneID) && !empty($zoneName) && $capacity >= 0 && in_array($role, ['student', 'staff', 'visitor'])) {
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO Zone (zoneID, zoneName, capacity, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $zoneID, $zoneName, $capacity, $role);

            if ($stmt->execute()) {
                $conn->commit();
                header("Location: admin-logs.php");
                exit();
            } else {
                $conn->rollback();
                $error = "Failed to add zone: " . $conn->error;
            }
        } else {
            $error = "Please fill all fields correctly.";
        }
    }
} catch (Throwable $e) {
    $conn->rollback();
    error_log("Exception: " . $e->getMessage(), 0);
    $error = "An unexpected error occurred. Please try again.";
}
?>
