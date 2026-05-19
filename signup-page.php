<?php
if (isset($_POST['registerBtn'])) {
    include("conn.php");

    $user_name     = trim($_POST['user_name'] ?? '');
    $user_email    = trim($_POST['user_email'] ?? '');
    $user_password = trim($_POST['user_password'] ?? '');

    // Validate inputs
    if (empty($user_name) || empty($user_email) || empty($user_password)) {
        $error_message = "All fields are required!";
    } else {
        // Escape for database
        $user_name     = mysqli_real_escape_string($conn, $user_name);
        $user_email    = mysqli_real_escape_string($conn, $user_email);

        // Check if email already exists
        $check_email_sql = "SELECT user_id FROM users WHERE user_email = '$user_email'";
        $check_result = mysqli_query($conn, $check_email_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Email already registered! Please use a different email.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
            $hashed_password = mysqli_real_escape_string($conn, $hashed_password);
            $role_std = 'user/traveller';

            // insert users
            $sql = "INSERT INTO users (user_name, user_email, user_password, user_role)
                    VALUES ('$user_name', '$user_email', '$hashed_password', '$role_std')";
            if (!mysqli_query($conn, $sql)) {
                $error_message = 'Error: ' . mysqli_error($conn);
            } else {
                // insert default profile picture (standardize profile pic: Image/defaultProfile.png)
                $newUserId  = mysqli_insert_id($conn);
                $defaultPic = 'Image/defaultProfile.png';
                $sql2 = "INSERT INTO user_profile (user_id, profile_picture)
                     VALUES ($newUserId, '$defaultPic')";
                if (!mysqli_query($conn, $sql2)) {
                    $error_message = 'Insert user_profile failed: ' . mysqli_error($conn);
                } else {
                    header('Location: login-page.php?success=signup');
                    exit;
                }
            }
        }
    }

    mysqli_close($conn);
    
    if (isset($error_message)) {
        echo '<script>alert("' . $error_message . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Better Moods</title>
    <link rel="stylesheet" href="css/signup.css">
</head>
<body style="background: url('Image/Vibe-Vacay-Background.png') no-repeat center center/cover;">

    <div class="signup-card">
        <h1 class="card-title">Sign Up</h1>
        <hr class="divider">
        <p class="instruction">Please enter your user details.</p>

        <form action="./signup-page.php" method="POST">
            
            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <input type="text" name="user_name" placeholder="Enter Username" required>
            </div>

            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                    </svg>
                </div>
                <input type="password" name="user_password" placeholder="Password" required>
            </div>

            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <input type="email" name="user_email" placeholder="Email" required>
            </div>

            <button type="submit" name="registerBtn" class="signup-btn">Sign Up</button>
        </form>

        <a href="./login-page.php" class="login-link">
            <span>&larr;</span> Back to Login Page
        </a>
    </div>

</body>
</html>