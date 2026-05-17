<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['user_id']);
$profilePicture = $isLoggedIn ? ($_SESSION['profile_picture'] ?? 'image/defaultProfile.jpg') : '';
?>

<header class="navbar">
    <div class="logo">
        <img src="Image/Vibe-Vacay-Logo.png" alt="Vibe Vacay Logo">
        Vibe Vacay
    </div>
    <nav class="nav-links">
        <a href="./landing-page.php">Home</a>
        <a href="./recommendation-page.php">Recommendation</a>
        <a href="./filter-search-page.php">Filter & Search</a>
        <a href="./wishlist-page.php">Wishlist</a>
        <a href="./about-us-page.php">About Us</a>
    </nav>
    <div class="auth-buttons">
        <?php if ($isLoggedIn): ?>
            <a href="./logout.php" class="login">Log Out</a>
            <a href="./personal-profile.php" class="signup-btn profile-link">
                <img src="<?php echo htmlspecialchars($profilePicture, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile">
                Profile
            </a>
        <?php else: ?>
            <a href="./login-page.php" class="login">Log In</a>
            <a href="./signup-page.php" class="signup-btn">Sign Up <span>➔</span></a>
        <?php endif; ?>
    </div>
</header>
