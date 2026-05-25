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
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/edit-user.css">
</head>


<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

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