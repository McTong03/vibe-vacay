<?php
require 'conn.php';
session_start();

$email = '';
$error_message = '';
$success_message = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'signup':
            $success_message = 'Account created successfully! Please log in.';
            break;
        case 'login':
            $success_message = 'Login successful!';
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['user_email'] ?? '');
    $pass  = trim($_POST['user_password'] ?? '');

    if ($email === '' || $pass === '') {
        $error_message = "Please enter email and password";
    } else {
        $stmt = $con->prepare('SELECT user_id, user_name, user_password, user_role FROM users WHERE user_email = ? LIMIT 1');
        if (!$stmt) {
            die('Database error: ' . $con->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();
            $error_message = "Wrong email or password";
        } else {
            $uid = 0;
            $uname = '';
            $upass = '';
            $urole = '';
            $stmt->bind_result($uid, $uname, $upass, $urole);
            $stmt->fetch();
            $stmt->close();

            if (!password_verify($pass, $upass)) {
                $error_message = "Wrong email or password";
            } else {
                $profile_picture = 'Image/defaultProfile.png';
                $stmt2 = $con->prepare('SELECT profile_picture FROM user_profile WHERE user_id = ? LIMIT 1');
                if ($stmt2) {
                    $profile_picture_result = '';
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
                        header('Location: landing-page.php?success=login'); break;
                    case 'admin':
                        header('Location: admin-dashboard.php?success=login'); break;
                    default:
                        header('Location: landing-page.php?success=login');
                }
                exit;
            }
        }
    }
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

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <script>
                alert(<?php echo json_encode($success_message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
                if (window.history.replaceState) {
                    window.history.replaceState(null, '', window.location.pathname);
                }
            </script>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="./login-page.php" method="POST">
            <div class="input-group">
                <div class="input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <input type="email" name="user_email" placeholder="Email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
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