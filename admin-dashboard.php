<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_success_message = '';
if (isset($_GET['success']) && $_GET['success'] === 'login') {
    $page_success_message = 'Login successful!';
}

include 'conn.php';

// Fetch top 5 destinations joined with states, ordered by average_rating
$sql = "SELECT d.destination_name, d.image_url, d.reviews_count, d.average_rating, s.state_name
        FROM destinations d
        JOIN states s ON d.state_id = s.state_id
        ORDER BY d.average_rating DESC
        LIMIT 5";

$result = mysqli_query($conn, $sql);
$top_destinations = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $top_destinations[] = $row;
    }
}

// sql query to fetch data for the chart (top 6 destinations by reviews count)
$chart_sql = "SELECT d.destination_name, d.reviews_count
              FROM destinations d
              ORDER BY d.reviews_count DESC
              LIMIT 6";

$chart_result = mysqli_query($conn, $chart_sql);
$chart_labels = [];
$chart_data = [];

if ($chart_result) {
    while ($row = mysqli_fetch_assoc($chart_result)) {
        $chart_labels[] = $row['destination_name'];
        $chart_data[]   = $row['reviews_count'];
    }
}

mysqli_close($conn);
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
        </div> <!-- end .features -->

        <h2 class="section-title">Most Popular Destinations</h2>
        <div class="destinations-list">
            <?php if (empty($top_destinations)): ?>
                <p style="padding: 20px; color: #6b7fa8;">No destinations found.</p>
            <?php else: ?>
                <?php foreach ($top_destinations as $dest): ?>
                    <div class="destination-item">
                        <img class="destination-img"
                            src="<?= htmlspecialchars($dest['image_url']) ?>"
                            alt="<?= htmlspecialchars($dest['destination_name']) ?>"
                            onerror="this.src='image/placeholder.jpg'" />
                        <div class="destination-info">
                            <p class="destination-state"><?= htmlspecialchars($dest['state_name']) ?></p>
                            <p class="destination-name"><?= htmlspecialchars($dest['destination_name']) ?></p>
                        </div>
                        <div class="destination-rating">
                            <?= number_format($dest['average_rating'], 1) ?>
                            <span class="star">&#9733;</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div> <!-- end .destinations-list -->

        <h2 class="section-title">Destination Chart</h2>
        <div class="chart-placeholder">
            <canvas id="destinationChart"></canvas>
        </div>

        <!-- Load Chart.js from CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('destinationChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chart_labels) ?>,
                    datasets: [{
                        data: <?= json_encode($chart_data) ?>,
                        backgroundColor: '#1A2B49',
                        borderRadius: 6,
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            title: {
                                display: true,
                                text: 'Review Count',
                                font: {
                                    size: 14,
                                    weight: '540',
                                },
                                color: '#1A2B49'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Destination',
                                font: {
                                    size: 14,
                                    weight: '540',
                                },
                                color: '#1A2B49'
                            }
                        }
                    }
                }
            });
        </script>

    </main>

</body>

</html>