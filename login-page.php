<?php
require 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['user_email'] ?? '');
    $pass  = trim($_POST['user_password'] ?? '');

    if ($email === '' || $pass === '') {
        die("Please enter email and password");
    }

    $stmt = $con->prepare('SELECT user_id, user_name, user_password, user_role FROM users WHERE user_email = ? LIMIT 1');
    if (!$stmt) {
        die('Database error: ' . $con->error);
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        die("Wrong email or password");
    }

    $stmt->bind_result($uid, $uname, $upass, $urole);
    $stmt->fetch();
    $stmt->close();

    // verify password
    if (!password_verify($pass, $upass)) {
        die("Wrong email or password");
    }

    // Get profile picture for the logged in user
    $profile_picture = 'image/defaultProfile.jpg';
    $stmt2 = $con->prepare('SELECT profile_picture FROM user_profile WHERE user_id = ? LIMIT 1');
    if ($stmt2) {
        $stmt2->bind_param('i', $uid);
        $stmt2->execute();
        $stmt2->bind_result($profile_picture_result);
        if ($stmt2->fetch() && !empty($profile_picture_result)) {
            $profile_picture = $profile_picture_result;
        }
        $stmt2->close();
    }

    $_SESSION['user_id']        = (int)$uid;
    $_SESSION['user_name']      = $uname;
    $_SESSION['user_role']      = $urole;
    $_SESSION['profile_picture'] = $profile_picture;

    switch (strtolower($urole)) {
        case 'user/traveller':
        case 'user':
            header('Location: landing-page.php'); break;
        case 'admin':
            header('Location: admin-dashboard.php'); break;
        default:
            header('Location: landing-page.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Better Moods</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body style="background: url('Image/Vibe-Vacay-Background.png') no-repeat center center/cover;">

    <div class="login-card">
        <h1 class="card-title">Your Journey to Better<br>Moods Starts Here</h1>
        
        <div class="welcome-text">WELCOME BACK</div>
        <hr class="divider">
        
        <p class="instruction">Please enter your account and password</p>

        <form action="./login-page.php" method="POST">
            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <input type="email" name="user_email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                    </svg>
                </div>
                <input type="password" name="user_password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <a href="./signup-page.php" class="signup-link">Sign Up</a>
    </div>

</body>
</html>