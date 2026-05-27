<?php
if (isset($_POST['registerBtn'])) {
    include("conn.php");

    $user_name             = trim($_POST['user_name'] ?? '');
    $user_email            = trim($_POST['user_email'] ?? '');
    $user_password         = trim($_POST['user_password'] ?? '');
    $user_confirm_password = trim($_POST['user_confirm_password'] ?? '');

    if (empty($user_name) || empty($user_email) || empty($user_password) || empty($user_confirm_password)) {
        $error_message = "All fields are required!";
    } elseif ($user_password !== $user_confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        $user_name  = mysqli_real_escape_string($conn, $user_name);
        $user_email = mysqli_real_escape_string($conn, $user_email);

        $check_email_sql = "SELECT user_id FROM users WHERE user_email = '$user_email'";
        $check_result = mysqli_query($conn, $check_email_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Email already registered! Please use a different email.";
        } else {
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
            $hashed_password = mysqli_real_escape_string($conn, $hashed_password);
            $role_std = 'user/traveller';

            $sql = "INSERT INTO users (user_name, user_email, user_password, user_role)
                    VALUES ('$user_name', '$user_email', '$hashed_password', '$role_std')";
            if (!mysqli_query($conn, $sql)) {
                $error_message = 'Error: ' . mysqli_error($conn);
            } else {
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

        <form action="./signup-page.php" method="POST" onsubmit="return validateForm()">

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
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <input type="email" name="user_email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <input type="password" id="user_password" name="user_password" placeholder="Password" required oninput="checkMatch()">
            </div>

            <div class="input-group" id="confirm-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        <polyline points="9,16 11,18 15,14"></polyline>
                    </svg>
                </div>
                <input type="password" id="user_confirm_password" name="user_confirm_password" placeholder="Confirm Password" required oninput="checkMatch()">
            </div>
            <p id="mismatch-hint" style="display:none; color:red; font-size:12px; margin:-8px 0 10px 4px;">Passwords do not match.</p>

            <button type="submit" name="registerBtn" class="signup-btn">Sign Up</button>
        </form>

        <a href="./login-page.php" class="login-link">
            <span>&larr;</span> Back to Login Page
        </a>
    </div>

    <script>
        function checkMatch() {
            const pw  = document.getElementById('user_password').value;
            const cpw = document.getElementById('user_confirm_password').value;
            const hint  = document.getElementById('mismatch-hint');
            const group = document.getElementById('confirm-group');
            if (cpw && pw !== cpw) {
                hint.style.display = 'block';
                group.style.borderColor = 'red';
            } else {
                hint.style.display = 'none';
                group.style.borderColor = '';
            }
        }

        function validateForm() {
            const pw  = document.getElementById('user_password').value;
            const cpw = document.getElementById('user_confirm_password').value;
            if (pw !== cpw) {
                alert('Passwords do not match!');
                return false;
            }
            return true;
        }
    </script>

</body>
</html>