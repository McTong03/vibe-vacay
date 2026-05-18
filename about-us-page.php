<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/about-us.css">
</head>
<body>
    <?php include('./includes/navbar.php'); ?>
    <div class="banner-wrapper">
        
        <!-- <header id="header">

            <div class="logo-container">
                <img src="icon/LogoName.png" class="logo" />
                <p class="logo-name">Vibe Vacay</p>
            </div>

            <nav class= "nav-bar">
                <p class="nav-button">Home</p>
                <p class="nav-button">Recommendation</p>
                <p class="nav-button">Wishlist</p>
                <p class="nav-button active">About Us</p>
                <p class="nav-button">Sign Up</p>
            </nav>

            <div class="profile-box">
                <p class="profile">Profile</p>
                <img src="icon/profile1.jpg" class="profile-icon" alt="Profile"
                />
            </div>
        </header> -->

        <section class="banner">
            <div class="banner-content">
                <h1 class="about-us">About Us</h1>
                <h1 class="about-us-title">Plan Your Next Vacation<br>Trip with Ease!</h1>
                <p class="about-us-description">
                    We are a team of 4 software engineering students aim to improve the travel planning experience. VibeVacay goals include making the entire travel planning process smooth and finding the most suitable destinations for users based on their mood.
                </p>
            </div>
        </section>
    </div>

    <section class="why-section">
        <div class="section-inner">
            <div class="why-title">Why VibeVacay?</div>
            <h2 class="why-subtitle">Mood-Based Travel Recommendation</h2>
    
        <div class="cards-grid">
            <div class="feature-card">
                <div class="card-label">
                    <span>Personalised<br>User Dashboard</span>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-label">
                    <span>Smart Search &amp;<br>Filtering Feature</span>
                </div>
            </div>
    
            <div class="feature-card">
                <div class="card-label">
                    <span>Detailed<br>Destination Insights</span>
                </div>
            </div>
    
        </div>
    </div>
  </section>

  <section class="cta-section">
    <div class="section-inner">
      <div class="cta-badge">Start Your Travel Journey Now!</div>
      <p class="cta-desc">
        <strong>Sign Up now to explore exciting destinations.</strong><br>
        Contact us if you need any assistance.
      </p>
 
      <div class="contact-card">
        <div class="contact-row">
          <i class="ph-bold ph-map-pin contact-icon"></i>
          <span>Kuala Lumpur, Malaysia</span>
        </div>
        <div class="contact-row">
          <i class="ph-bold ph-phone contact-icon"></i>
          <span>+60 123004545</span>
        </div>
        <div class="contact-row">
          <i class="ph-bold ph-envelope contact-icon"></i>
          <span>vibevacay04@gmail.com</span>
        </div>
      </div>
 
      <a href="signup-page.php" class="btn-cta">Sign Up</a>
    </div>
  </section>
    
</body>
</html>