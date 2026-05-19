<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_success_message = '';
if (isset($_GET['success']) && $_GET['success'] === 'login') {
    $page_success_message = 'Login successful!';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/admin-dashboard.css">
</head>
<body>
    <?php if (!empty($page_success_message)): ?>
        <script>
            alert(<?php echo json_encode($page_success_message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
            if (window.history.replaceState) {
                window.history.replaceState(null, '', window.location.pathname);
            }
        </script>
    <?php endif; ?>

    <?php include('./includes/admin-nav-bar.php'); ?>
    
    <div class="banner-wrapper">
        <section class="banner">
            <div class="banner-content">
                <h1 class="welcome">Welcome Back!</h1>
                <div class="stat-cards-row">
                    <div class="stat-card">
                        <p class="stat-label">Total Users</p>
                        <p class="stat-value">5,670</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Destinations</p>
                        <p class="stat-value">9,270</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <main class="main-content">
        <div class="features">
            <a href="destination-management.php" class="feature-card">
                    <div class="feature-card-img">
                        <img src="https://th.bing.com/th/id/OIG4.HvtzVjw10Ol8Rac1157C?pid=ImgGn" alt="Destination Management Icon">
                    </div>
                    <div class="feature-card-label">Destination Management</div>
                </a>
    
                <a href="statistics-and-report.php" class="feature-card">
                    <div class="feature-card-img">
                        <img src="https://th.bing.com/th/id/OIG1.JYim4WIE8HYOW.HvcCW9?pid=ImgGn" alt="Statistics & Report Icon">
                    </div>
                    <div class="feature-card-label">Statistics &amp; Report</div>
                </a>
    
                <a href="user-management.php" class="feature-card">
                    <div class="feature-card-img">
                        <img src="https://th.bing.com/th/id/OIG3.1r1VnHmaJhKxmdx0wHMJ?pid=ImgGn" alt="User Management Icon">
                    </div>
                    <div class="feature-card-label">User Management</div>
                </a>
            </div>

        <h2 class="section-title">Most Popular Destinations</h2>
            <div>
            <div class="destinations-list">
    
                <div class="destination-item">
                    <img class="destination-img" src="image/batucaves.jpg" 
                        onerror="this.src='../image/batucaves.jpg'" />
                    <div class="destination-info">
                        <p class="destination-location">Kuala Lumpur</p>
                        <p class="destination-name">Batu Caves</p>
                    </div>
                    <div class="destination-rating">4.7 <span class="star">&#9733;</span></div>
                </div>
            </div>

        <h2 class="section-title">Destination Chart (Last 7 days)</h2>
        <div class="chart-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="12" width="4" height="10" rx="1"/>
                <rect x="9" y="7" width="4" height="15" rx="1"/>
                <rect x="16" y="3" width="4" height="19" rx="1"/>
            </svg>
            <span>Live Chart</span>
        </div>
 
    </main>
            

</body>
</html>