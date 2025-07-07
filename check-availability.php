<?php
session_start();
require_once "includes/db_connect.php";

// Redirect if already logged in
if (isset($_SESSION["user_id"]) && isset($_SESSION["role"])) {
    switch ($_SESSION["role"]) {
        case 'student':
            header("Location: student_dashboard.php");
            exit();
        case 'staff':
            header("Location: staff_dashboard.php");
            exit();
        case 'visitor':
            header("Location: visitor_dashboard.php");
            exit();
        default:
            header("Location: logout.php");
            exit();
    }
}

$zones = [];

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("SELECT zoneName, role, capacity, availableSpace FROM Zone");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $zones[$row['role']] = $row;
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error fetching zones: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Parking Availability</title>
    <link rel="stylesheet" href="css/availability.css">
</head>
<body>

<div class="dashboard-container">
    <h2>Check Parking Availability</h2>

    <div class="zone-section">
        <h3>Please Log In or Sign Up to View Zone Availability</h3>

        <div class="tile-grid">

            <div class="tile">
                <h4>Student</h4>
                <p>Zone A — for registered students</p>
                <a href="login/student-login.php" class="btn">Log In</a>
                <a href="signup/student-signup.php" class="btn" style="margin-top: 0.5rem;">Sign Up</a>
            </div>

            <div class="tile">
                <h4>Staff</h4>
                <p>Zone B — reserved for staff members</p>
                <a href="login/staff-login.php" class="btn">Log In</a>
                <a href="signup/staff-signup.php" class="btn" style="margin-top: 0.5rem;">Sign Up</a>
            </div>

            <div class="tile">
                <h4>Visitor</h4>
                <p>Zone C — public parking for visitors</p>
                <a href="login/visitor-login.php" class="btn">Log In</a>
                <a href="signup/visitor-signup.php" class="btn" style="margin-top: 0.5rem;">Sign Up</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>


