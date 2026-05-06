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
        margin-left: 90px;
    }

    .admin {
        color: white;
        margin-left: 90px;
        font-size: 18px;
        margin-top: -25px;
    }

    .filter-box {
        background-color: #C7D0EC;
        width: 1030px;
        height: 80px;
        margin-left: 90px;
        border-radius: 15px;
        margin-top: 50px;
    }

    .all-role-container {
        top: 12px;
        left: 60px;
        width: 250px;
    }

    .all-role-container,
    .search-container {
        height: 55px;
        background-color: #828A98;
        position: relative;
        
        border-radius: 8px;
    }
    
    .user {
        filter: brightness(0) invert(1);
        width: 38px;
        margin-top: 7px;
        margin-left: 28px;
    }

    .role {
        color: white;
        margin-top: -32px;
        margin-left: 90px;
        font-size: 18px;
    }

    .drop {
        filter: brightness(0) invert(1);
        width: 25px;
        position: relative;
        top: -40px;
        left: 190px;
    }

    .search-container {
        top: -61px;
        left: 700px;
        width: 280px;
    }

    .search-users {
        color: white;
        position: relative;
        top: 17px;
        left: 22px;
        font-size: 18px;
    }

    .search-button{
        width: 100px;
        height: 40px;
        font-size: 18px;
        border-radius: 80px;
        border-width: none;
        position: relative;
        top: -32px;
        left: 160px;
        border: none;
    }

    .user-container{
        display: grid;
        grid-template-columns: repeat(4, 1fr); 
        gap: 30px;
        padding: 50px 100px;
    }

    .user-box-container {
        width: 304px;
        height: 470px;
        background-color: #EAE7E7;
        margin-top: 30px;
        border-radius: 20px;
        margin-left: -5px;
    }

    .profile-picture1 {
        width: 304px;
        height: 155px;
        background-color: #CFD4E0;
        border-radius: 20px;
    }

    .profile-picture{
        border-radius: 80px;
        width: 125px;
        margin-top: 15px;
        margin-left: 90px;
    }

    .user-id,
    .user-name,
    .user-email,
    .user-password,
    .user-role {
        font-size: 18px;
        padding-left: 35px;
       
    }

    .user-id {
        margin-top: 25px;
    }

    .user-roles {
        background-color: #B3B6C3;
        width: 78px;
        height: 35px;
        border-radius: 20px;
        margin-left: 28px;
    }

    .user-role {
        padding-top: 7px;
        margin-left: -13px;
    }

    .edit-button{
        margin-left: -15px;
        margin-top: 25px;
        padding-left: 34px;
    }

    .edit-button,
    .delete-button {
        width: 110px;
        height: 50px;
        font-size: 16px;
        background-color: #B3B6C3;
        border: none;
        border-radius: 10px;
        
    }
    
    .edit-but,
    .delete-but {
        width: 30px;
        position: relative;
        left: 34px;
        top: 8px;
    }


    .delete-button {
        margin-left: -12px;
        margin-top: 25px;
        padding-left: 38px;
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
    </div>  

    
    <div class="filter-box">
        <div class="all-role-container">
            <img class="user" src="icon/user.png">
            <p class="role">All roles</p>
            <img class="drop" src="icon/drop.png ">
        </div>

        <div class="search-container">
            <div class="search-box-container">
                <p class="search-users">Search Users...</p>
            </div>
            
            <div class="search-button-container">
                <button class="search-button">Search</button>
            </div>
            
        </div>
    </div>

    <div class="user-container">
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

    </div>

    

</body>

</html>