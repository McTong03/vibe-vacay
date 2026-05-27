<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_success_message = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'login':
            $page_success_message = 'Login successful!';
            break;
        case 'logout':
            $page_success_message = 'Logged out successfully!';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe Vacay</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/landingpage.css">
    <link rel="stylesheet" href="css/filter-search.css">
    <link rel="stylesheet" href="css/search-bar.css">
</head>

<body>

    <?php include('./includes/navbar.php'); ?>

    <?php if (!empty($page_success_message)): ?>
        <script>
            alert(<?php echo json_encode($page_success_message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
            if (window.history.replaceState) {
                window.history.replaceState(null, '', window.location.pathname);
            }
        </script>
    <?php endif; ?>

    <section class="hero" style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)),
        url('Image/travel_luggage.jpg') center/cover;">
        <h1>Discover your perfect destination based on how you feel</h1>
        <a href="./recommendation-page.php" class="explore-btn" style="margin-left: 70rem; margin-top: 3rem;">
            Explore <span>➔</span>
        </a>
        <p>Not sure where to go? Let your mood decide. Discover destinations perfectly matched to how you feel right now.</p>
    </section>

    <?php include('./includes/search-bar.php'); ?>

    <section class="mood-section">
        <h2>Start Your Journey Based on How You Feel</h2>
        <div class="cards-container">
            <div class="card card-1" style="background-image: url('Image/Lifestyle-Transformation.jpg');">
                <div class="card-content">
                    <h3>Relax & Recharge</h3>
                    <p>Find peaceful destinations to unwind from quiet beaches to cozy retreats.</p>
                </div>
            </div>
            <div class="card card-2" style="background-image: url('Image/paraglider.jpg');">
                <div class="card-content">
                    <h3>Adventure Awaits</h3>
                    <p>Feeling bold? Explore thrilling locations packed with excitement and new experiences.</p>
                </div>
            </div>
            <div class="card card-3" style="background-image: url('Image/Hiking.jpg');">
                <div class="card-content">
                    <h3>Escape & Reflect</h3>
                    <p>Reconnect with yourself through calming, meaningful travel experiences.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <h2>Smarter Travel Starts Here</h2>
        <div class="features-container">
            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-title">Mood-Scape Recommendation Engine</div>
                    <p>Our intelligent system analyzes your mood and suggests destinations that match your emotions, helping you find the perfect getaway effortlessly.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Smart Search & Filtering</div>
                    <p>Easily explore destinations using advanced filters and search tools, with results tailored to your preferences and travel style.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Personalized User Dashboard</div>
                    <p>Manage your trips, track your preferences, and receive customized recommendations — all in one place.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Explore Popular Destinations</div>
                    <p>Stay inspired with trending locations, rankings, and top picks from travelers around the world.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-title">Detailed Destination Insights</div>
                    <p>Access rich information, travel tips, and reviews to make confident travel decisions.</p>
                </div>
            </div>

            <div class="features-images">
                <img src="Image/travel-with-friend.jpg" alt="Group of travelers looking at a view">
                <img src="Image/yellow-van.jpg" alt="Yellow van on a map">
            </div>
        </div>
    </section>

</body>

</html>