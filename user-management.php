<?php
require 'conn.php';
session_start();

$roleFilter = isset($_GET['user_role']) ? $_GET['user_role'] : 'ALL';

if($roleFilter == 'ALL') {
    $sql = "SELECT * FROM users";
} else {
    $sql = "SELECT * FROM users WHERE user_role = '$roleFilter'";
}

$result = mysqli_query($conn, $sql);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

    @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
</style>

<style>
    body{
        background-image: 
            linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)),
            url("image/background.png");
        background-size: cover;
        /* background-position: center; */
        background-repeat: no-repeat;
        height: 2000px;
        background-color: black;
    }

    #header {
        background-color: #1A2B49;
        height: 55px;
        border-radius: 50px;
        margin-left: 90px;
        margin-right: 20px;
        margin-top: 25px;
        width: calc(100% - 180px);
    }

    .logo {
        width: 65px;
        height: 65px;
        margin-top: -3px;
        margin-left: 30px;
    }

    .logo-name,
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

    .logo-name {
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

    .manage {
        color: white;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        margin-top: 60px;
        font-size: 45px;
        margin-left: 150px;
    }

    .admin {
        color: white;
        margin-left: 150px;
        font-size: 18px;
        margin-top: -25px;
    }

    .add-button {
        margin-top: -80px;
        margin-left: 750px;
        width: 250px;
        height: 50px;
        position: absolute;
        font-size: 20px;
        background-color: #0064CE;
        border: none;
        color: white;
    }

    .filter-box {
        background-color: #C7D0EC;
        width: 900px;
        height: 65px;
        margin-left: 150px;
        border-radius: 15px;
        margin-top: 40px;
    }

    .all-role-container select {
        top: 10px;
        left: 70px;
        width: 230px;
        height: 45px;
        font-size: 16px;
        padding-left: 20px;
    }

    .all-role-container select,
    .search-container {
        background-color: #828A98;
        position: relative;
        border-radius: 8px;
        color: white;
        cursor: pointer;
    }
    

    .search-container {
        display: flex;
        align-items: center;
        border: 1px solid #828A98;
        padding: 6px 6px 6px 18px;
        gap: 8px;
        max-width: 300px;
        height: 30px;
        transition: border-color 0.15s, box-shadow 0.15s;
        margin-left: 500px;
        margin-top: -35px;
    }


    #search-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 16px;
        color: white;
    }

    #search-input::placeholder {
        font-size: 16px;
        color: white;
        padding-left: 15px;
    }

    .search-button {
        width: 110px;
        height: 35px;
        font-size: 16px;
        margin-left: 180px;
        position: absolute;
        cursor: pointer;
    }

    .user-container{
        display: grid;
        grid-template-columns: repeat(5, 290px); 
        padding: 30px 50px;
        row-gap: 10px;
    }

    .user-box-container {
        width: 270px;
        height: 370px;
        background-color: #EAE7E7;
        margin-top: 30px;
        border-radius: 20px;
        margin-left: -5px;
    }

    .profile-picture1 {
        width: 270px;
        height: 100px;
        background-color: #CFD4E0;
        border-radius: 20px;
    }

    .profile-picture{
        border-radius: 80px;
        width: 70px;
        margin-top: 15px;
        margin-left: 100px;
    }

    .user-id,
    .user-name,
    .user-email,
    .user-password,
    .user-role {
        font-size: 14px;
        padding-left: 35px;
       
    }

    .user-id {
        margin-top: 20px;
    }

    .user-roles {
        background-color: #B3B6C3;
        width: 75px;
        height: 33px;
        border-radius: 20px;
        margin-left: 28px;
    }

    .user-role {
        padding-top: 7px;
        margin-left: -13px;
    }

    .edit-button{
        margin-left: -3px;
        margin-top: 25px;
        padding-left: 28px;
    }

    .edit-button,
    .delete-button {
        width: 95px;
        height: 40px;
        font-size: 14px;
        background-color: #B3B6C3;
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }
    
    .edit-but,
    .delete-but {
        width: 20px;
        position: relative;
        top: 5px;
    }


    .delete-button {
        margin-left: -26px;
        margin-top: 35px;
        padding-left: 38px;
    }

    .delete-but {
        left: 11px;
    }

    .edit-but {
        left: 35px;
    }

    .delete-container {
        margin-top: -75px;
        margin-left: 140px;
    }


</style>

<body>
    <header id="header">
        <div class="logo-container">
            <img src="icon/LogoName.png" class="logo" />
        </div>

        <p class="logo-name">Vibe Vacay</p>
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

    <div class="second-part">
        <div class="manage-container">
            <p class="manage">Manage Users</p>
        </div>

        <div class="admin-container">
            <p class="admin">Admin / Manage Users</p>
        </div>

        <div>
            <button class="add-button">+ Add New User</button>
        </div>
        
    </div>  

    
    <div class="filter-box">
        <form method="GET">
            <div class="all-role-container">
                <select class="role-drop" name="user_role" onchange="this.form.submit()">
                    <option value="ALL" <?php echo ($roleFilter == '' || $roleFilter == 'ALL') ? 'selected' : ''; ?>>All Roles</option>
                    <option value="Admin" <?php echo ($roleFilter == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="User/Travaller" <?php echo ($roleFilter == 'User/Travaller') ? 'selected' : ''; ?>>User/Travaller</option>
                    
                </select>
            <!-- <img class="user" src="icon/user.png">
            <p class="role">All roles</p>
            <button class="drop-button">
                <img class="drop" src="icon/drop.png ">
            </button> -->  
            </div>
        </form>

        <div class="search-container">
            <input id="search-input" type="text" name="search" placeholder="Search by name">
            <button class="search-button">Search</button>
            
        </div>
    </div>

    <div class="user-container">
        <?php
        if(mysqli_num_rows($result) > 0) {
            while($user = mysqli_fetch_assoc($result)) {
        ?>

            <div class="user-box-container">
                <div class="profile-picture1">
                    <img class="profile-picture" src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'Image/default-profile.jpg'; ?>">
                </div>

                <div class="id">
                    <p class="user-id">ID: <?php echo $user['user_id']; ?></p>
                </div>

                <div class="name">
                    <p class="user-name"><?php echo htmlspecialchars($user['user_name']); ?></p>
                </div>

                <div class="password">
                    <p class="user-password">••••••</p>
                </div>

                <div class="email">
                    <p class="user-email"><?php echo htmlspecialchars($user['user_email']); ?></p>
                </div>

                <div class="user-roles">
                    <p class="user-role"><?php echo htmlspecialchars($user['user_role']); ?></p>
                </div>

                <div class="edit-container">
                    <img class="edit-but" src="icon/edit.png">
                    <button class="edit-button">Edit</button>
                </div>

                <div class="delete-container">
                    <img class="delete-but" src="icon/delete.png">
                    <button class="delete-button">Delete</button>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<p style="color:white; margin-left:100px;">No users found.</p>';
        }
        ?>
    </div>







    <!-- <div class="user-container">
        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>
        
        <div class="user-box-container">
            <div class="profile-picture1">
                <img class="profile-picture" src="icon/profile1.jpg">
            </div>

            <div class="id">
                <p class="user-id">ID:</p>
            </div>

            <div class="name">
                <p class="user-name">John Tan</p>
            </div>

            <div class="email">
                <p class="user-email">johntan28@gmail.com</p>
            </div>

            <div class="password">
                <p class="user-password">xxx</p>
            </div>

            <div class="user-roles">
                <p class="user-role">User</p>
            </div>

            <div class="edit-container">
                <img class="edit-but" src="icon/edit.png">
                <button class="edit-button">Edit</button>
            </div>

            <div class="delete-container">
                <img class="delete-but" src="icon/delete.png">
                <button class="delete-button">Delete</button>
            </div>   
        </div>

    </div> -->

    

</body>

</html>