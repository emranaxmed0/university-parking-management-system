<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "university-parking";

// Turn on MySQLi error reporting for exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $db_name);
    $conn->set_charset("utf8mb4"); // Set character set to avoid encoding issues
} catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage()); // Log error for debugging
    die("A system error occurred. Please try again later."); // User-friendly error
}
?>
