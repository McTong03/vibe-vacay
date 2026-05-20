<?php
require 'conn.php';
 
if (!isset($_GET['id'])) {
    header("Location: user-management.php");
    exit();
}
 
$id = intval($_GET['id']);
$success = false;
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id       = intval($_POST['user_id']);
    $user_name     = $_POST['user_name'];
    $user_email    = $_POST['user_email'];
    $user_password = $_POST['user_password'];
    $user_role     = $_POST['user_role'];
 
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn,
        "UPDATE users SET 
            user_name = ?, 
            user_email = ?, 
            user_password = ?, 
            user_role = ? 
         WHERE user_id = ?"
    );
 
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }
 
    mysqli_stmt_bind_param($stmt, "ssssi",
        $user_name, $user_email, $user_password, $user_role, $user_id
    );
 
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: user-management.php");
        exit();
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
 
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
 
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
 
if (!$user) {
    header("Location: user-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>

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
        border: none;      
        cursor: pointer;    
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
    select {
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
        height: 800px;
        width: 1300px;
        border-radius: 20px;
    }

    label {
        color: white;
        font-size: 20px;
        margin-left: -10px;
    }

    .cancel-button,
    .submit-button {
        width: 350px;
        height: 70px;
        font-size: 30px;
        margin-top: 50px;
        border-radius: 20px;
        cursor: pointer;
    }

    .cancel-button {
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

    <button class="error-container" onclick="window.location.href='user-management.php'"> 
        <img class="error-button" src="icon/error.png">
    </button>

    <h2>Edit User</h2>

    <form method="POST" action="edit-user.php?id=<?php echo $user['user_id']; ?>">

        <input type="hidden" id="orig_name"     value="<?= htmlspecialchars($user['user_name']) ?>">
        <input type="hidden" id="orig_email"    value="<?= htmlspecialchars($user['user_email']) ?>">
        <input type="hidden" id="orig_role"     value="<?= htmlspecialchars($user['user_role']) ?>">
        <input type="hidden" id="orig_password" value="<?= htmlspecialchars($user['user_password']) ?>">

        <div id="container">
            <div class="id-container">
                <br><br><label>User ID</label><br>
                <input type="text" name="user_id" 
                    value="<?php echo htmlspecialchars($user['user_id']); ?>" readonly>
            </div>

            <div class="name-container">
                <br><br><label>User Name</label><br>
                <input type="text" name="user_name"
                    value="<?php echo htmlspecialchars($user['user_name']); ?>">
            </div>

            <div class="email-container">
                <br><br><label>User Email</label><br>
                <input type="text" name="user_email"
                    value="<?php echo htmlspecialchars($user['user_email']); ?>">
            </div>

            <div class="password-container">
                <br><br><label>User Password</label><br>
                <input type="text" name="user_password"
                    value="<?php echo htmlspecialchars($user['user_password']); ?>">
            </div>

            <div class="role-container">
                <br><br><label>User Role</label><br>
                <select id="user_role" name="user_role" required>
                    <option value="" disabled <?= empty($user['user_role']) ? 'selected' : '' ?>>Select a role</option>
                    <option value="admin" <?= $user['user_role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user/traveller" <?= $user['user_role'] === 'user/traveller' ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <button type="button" class="cancel-button" onclick="window.location.href='user-management.php'">Cancel</button>
            <button type="submit" class="submit-button">Submit</button>
            
            
             
        </div>
    </form> 

</body>
</html>