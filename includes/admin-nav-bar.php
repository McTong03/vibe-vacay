<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// if (empty($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
//     header('Location: ./login-page.php');
//     exit();
// }

$isLoggedIn = !empty($_SESSION['user_id']);
$profilePicture = $_SESSION['profile_picture'] ?? 'Image/defaultProfile.png';
?>

<header class="navbar">
    <div class="logo">
        <img src="Image/Vibe-Vacay-Logo.png" alt="Vibe Vacay Logo">
        Vibe Vacay
    </div>
    <nav class="nav-links">
        <a href="./admin-dashboard.php">Home</a>
        <a href="./destination-management.php">Destination Management</a>
        <a href="./statistics-and-report.php">Statistic</a>
        <a href="./user-management.php">User Management</a>
        <a href="./tagging-type-management.php">Tagging Type Management</a>
    </nav>
    <div class="auth-buttons">
        <a href="./logout.php" class="login">Log Out</a>
        <a href="./personal-profile.php" class="signup-btn profile-link">
            <img src="<?php echo htmlspecialchars($profilePicture, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile">
            <?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?>
        </a>
    </div>
</header>