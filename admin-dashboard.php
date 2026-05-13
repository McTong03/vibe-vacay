<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin-dashboard.css">
</head>
<body>
    <div class="banner-wrapper">
        <header id="header">

            <div class="logo-container">
                <img src="icon/LogoName.png" class="logo" />
                <p class="logo-name">Vibe Vacay</p>
            </div>

            <nav class= "nav-bar">
                <p class="nav-button active">Home</p>
                <p class="nav-button">Destination Management</p>
                <p class="nav-button">Statistic</p>
                <p class="nav-button">User Management</p>
                <p class="nav-button">Log Out</p>
            </nav>

            <div class="profile-box">
                <p class="profile">Profile</p>
                <img src="icon/profile1.jpg" class="profile-icon" alt="Profile"
                />
            </div>
        </header>

        <section class="banner">
            <div class="banner-content">
                <h1 class="welcome">Welcome Back!</h1>
                <div class="stats">
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
                        <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <div class="feature-card-label">Destination Management</div>
                </a>
    
                <a href="statistics-and-report.php" class="feature-card">
                    <div class="feature-card-img">
                        <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <div class="feature-card-label">Statistics &amp; Report</div>
                </a>
    
                <a href="user-management.php" class="feature-card">
                    <div class="feature-card-img">
                        <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <div class="feature-card-label">User Management</div>
                </a>
            </div>

    
</body>
</html>