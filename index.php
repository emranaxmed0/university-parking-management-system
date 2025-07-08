<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Parking Management System</title>
    <link rel="stylesheet" href="css/style.css">
 
</head>
<body>

    <header class="main-header">
        <div class="container">
            <div class="logo-section">
                <div class="logo">🚗</div>
                <h1 class="site-title">University Parking Management System</h1>
            </div>
        </div>
    </header>
  <?php include 'templates/nav.php'; ?>
    <main class="main-content">
        <section class="hero-section">
            <h2 class="hero-heading">Smart Parking Solutions for Campus</h2>
            <p class="hero-text">Efficiently manage your university parking with our comprehensive system. Find available spots and streamline campus mobility.</p>
           <div class="button-group">
    <a href="check-availability.php" class="primary-button">Check Parking Availability</a>
    <div class="dropdown">
    <a class="secondary-button dropdown-toggle" href="#">Get Started Today ▾</a>
         <div class="dropdown-content">
             <a href="signup/staff-signup.php">Staff Signup</a>
             <a href="signup/student-signup.php">Student Signup</a>
             <a href="signup/visitor-signup.php">Visitor Signup</a>
         </div>
    </div>


        </section>

        <section class="features-grid">
            <div class="feature-card">
                <div class="icon-circle">🚗</div>
                <h3 class="feature-title">Real-time Availability</h3>
                <p>View live parking spot availability across all campus locations.</p>
            </div>
            <div class="feature-card">
                <div class="icon-circle">🕒</div>
                <h3 class="feature-title">24/7 Access</h3>
                <p>Access the parking system anytime, anywhere.</p>
            </div>
            <div class="feature-card">
                <div class="icon-circle">🛡️</div>
                <h3 class="feature-title">Secure System</h3>
                <p>Advanced security protects your data and ensures safety.</p>
            </div>
            <div class="feature-card">
                <div class="icon-circle">👥</div>
                <h3 class="feature-title">User-Friendly</h3>
                <p>Designed for students, staff and visitors.</p>
            </div>
        </section>

    </main>

 <?php include 'templates/footer.php'; ?>
</body>
</html>

