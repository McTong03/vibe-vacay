<?php
require 'conn.php';
// session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_name     = trim($_POST["user_name"]     ?? "");
    $user_email    = trim($_POST["user_email"]    ?? "");
    $user_password = trim($_POST["user_password"] ?? "");
    $user_role     = trim($_POST["user_role"]     ?? "");

    if (empty($user_name) || empty($user_email) || empty($user_password) || empty($user_role)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {

        $hashed_password = password_hash($user_password, PASSWORD_BCRYPT);


        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO USERS (user_name, user_email, user_password, user_role)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $user_name,
            $user_email,
            $hashed_password,
            $user_role
        );
        mysqli_stmt_execute($stmt);

        if (!mysqli_stmt_errno($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: user-management.php");
            exit();
        } else {
            $error_message = "Error: " . mysqli_stmt_error($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="css/add-user.css">
    <link rel="stylesheet" href="css/menubar.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
    </style>
</head>




<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <button class="error-container" onclick="window.location.href='user-management.php'">
        <img class="error-button" src="icon/error.png">
    </button>

    <h2>Add User</h2>

    <form action="" method="POST" id="addUserForm">
        <div id="container">
            <div class="name-container">
                <br><br><label for="user_name">User Name</label><br>
                <input type="text" id="user_name" name="user_name"
                    value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>"
                    placeholder="Enter full name" required>
            </div>

            <div class="email-container">
                <br><br><label for="user_email">User Email</label><br>
                <input type="text" id="user_email" name="user_email"
                    value="<?= htmlspecialchars($_POST['user_email'] ?? '') ?>"
                    placeholder="Enter email address" required>
                <p class="error-msg" id="email-error">⚠ Please enter a valid email address (e.g. user@example.com)</p>
            </div>

            <div class="password-container">
                <br><br><label for="user_password">User Password</label><br>
                <input type="text" id="user_password" name="user_password" placeholder="Enter password" required>
            </div>

            <div class="role-container">
                <br><br><label for="user_role">User Role</label><br>

                <select id="user_role" name="user_role" required>
                    <option value="" disabled selected>Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="user/traveller">User/Traveller</option>
                </select>
            </div>

            <button type="reset" class="reset-button">Reset</button>
            <button type="submit" class="submit-button">Submit</button>


        </div>
    </form>

    <script>
        (function() {
            const emailInput = document.getElementById('user_email');
            const emailError = document.getElementById('email-error');
            const form = document.getElementById('addUserForm');

            function isValidEmail(val) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
            }

            // Show/hide error as user types
            emailInput.addEventListener('input', function() {
                if (emailInput.value.trim() !== '' && !isValidEmail(emailInput.value.trim())) {
                    emailError.style.display = 'block';
                } else {
                    emailError.style.display = 'none';
                }
            });

            // Block submission if email is invalid
            form.addEventListener('submit', function(e) {
                if (!isValidEmail(emailInput.value.trim())) {
                    emailError.style.display = 'block';
                    emailInput.focus();
                    e.preventDefault();
                }
            });
        })();
    </script>
</body>

</html>