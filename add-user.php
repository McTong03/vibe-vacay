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
 

        $stmt = mysqli_prepare($conn,
            "INSERT INTO USERS (user_name, user_email, user_password, user_role)
             VALUES (?, ?, ?, ?)"
        );
 
        mysqli_stmt_bind_param($stmt, "ssss",
            $user_name,
            $user_email,
            $hashed_password,
            $user_role
        );
        mysqli_stmt_execute($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

    @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
</style>

<style>
        body{
        height: 2000px;
    }

    #header {
        background-color: #1A2B49;
        height: 55px;
        border-radius: 50px;
        margin-left: 8px;
        margin-right: 20px;
        margin-top: 25px;
        width: 1480px;
    }

    .logo {
        width: 65px;
        height: 65px;
        margin-top: -3px;
        margin-left: 30px;
    }

    .admin-name,
    .home,
    .destination-management,
    .statistic,
    .user-management,
    .logout,
    .profile {
        /* font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; */
        font-size: 17px;
        font-weight: bold
    }

    .admin-name {
        margin-top: -47px;
        margin-left: 100px;
        color: white;
    }

    .home {
        margin-top: -37px;
        margin-left: 380px;
        color: white;
    }

    .destination-management {
        margin-top: -37px;
        margin-left: 510px;
        color: white;
    }

    .statistic {
        margin-top: -37px;
        margin-left: 770px;
        color: white;
    }

    .user-management {
        margin-top: -37px;
        margin-left: 930px;
        color: white;
    }

    .logout {
        margin-top: -37px;
        margin-left: 1180px;
        color: white;
    }

    .profile-box{
        background-color: white;
        width: 160px;
        height: 35px;
        margin-top: -46px;
        margin-left: 1280px;
        border-radius: 30px;
    }

    .profile {
        padding-top: 8px;
        margin-left: 35px;
    }

    .profile-icon {
        width: 30px;
        height: 30px;
        border-radius: 60px;
        margin-left: 100px;
        /* top: -20px; */
        position: relative;
        top: -42px;
        left: 5px;
    }

    .error-container {
        background-color: #1A2B49;
        width: 70px;
        height: 70px;
        margin-top: 55px;
        border-radius: 50px;
        margin-left: 80px;
    }

    .error-button {
        filter: brightness(0) invert(1);
        width: 45px;
    }

    h2 {
        font-size: 35px;
        font-family: 'Open Sans';
        font-weight: bold;
        margin-top: -62px;
        margin-left: 180px;
    }

    .id-container,
    .name-container,
    .email-container,
    .password-container,
    .role-container {
        margin-left: 50px;
    }

    input[type=text],
    select{
        width: 1200px;
        height: 50px;
        border-width: 3px;
        border-radius: 10px;
        margin-left: -10px;
        margin-top: 10px;
    }

    #container {
        background-color: #1A2B49;
        margin-top: 60px;
        margin-left: 80px;
        height: 700px;
        width: 1300px;
        border-radius: 20px;
    }

    label {
        color: white;
        font-size: 20px;
        margin-left: -10px;
    }

    .reset-button,
    .submit-button {
        width: 350px;
        height: 70px;
        font-size: 30px;
        margin-top: 50px;
        border-radius: 20px;
    }

    .reset-button {
        margin-left: 250px;
        background-color: #C8C8C8;
    }

    .submit-button {
        margin-left: 100px;
        background-color: #0064CE;
        color: white;
    }
</style>



<body>
    <header id="header">
        <div class="logo-container">
            <img src="icon/LogoName.png" class="logo" />
        </div>

        <p class="admin-name">Vibe Vacay</p>
        <p class="home">Home</p>
        <p class="destination-management">Destination Management</p>
        <p class="statistic">Statistic</p>
        <p class="user-management">User Managememt</p>
        <p class="logout">Log Out</p>


        <div class="profile-box">
            <p class="profile">Profile</p>
            <img src="icon/profile1.jpg" class="profile-icon" />

        </div>
    </header>

    <button class="error-container">
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
            </div>

            <div class="password-container">
                <br><br><label for="user_password">User Password</label><br>
                <input type="text" id="user_password" name="user_password" placeholder="Enter password" required>
            </div>

            <div class="role-container">
                <br><br><label for="user_role">User Role</label><br>
                <!-- <input type="text" id="user_role" name="user_role" 
                    value="<?= htmlspecialchars($_POST['user_role'] ?? '') ?>"
                       placeholder="e.g. admin or user" required> -->

                <select id="user_role" name="user_role" required>
                    <option value="" disabled selected>Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>

            <button type="reset" class="reset-button">Reset</button>
            <button type="submit" class="submit-button">Submit</button>
        
                
        </div>
    </form>
</body>
</html>