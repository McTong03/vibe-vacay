<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagging Management Page</title>
</head>
<style>
    body {
        height: 4000px;
    }

    /* Header Styles */
    #header {
        background-color: #1A2B49;
        height: 55px;
        border-radius: 50px;
        margin-left: 8px;
        margin-right: 20px;
        margin-top: 25px;
        width: 1480px;
        position: relative;
        z-index: 2;
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

    .profile-box {
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

    /* Global Search Bar */
    .search-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 25px;
        padding-bottom: 1rem;
        margin-right: 20px;
    }

    .search-bar {
        background-color: #1A2B49;
        padding: 0.5rem;
        padding-left: 1.5rem;
        margin-left: 270px;
        border-radius: 40px;
        display: flex;
        width: 600px;
        justify-content: center;
        align-items: center;
    }

    .search-bar input {
        background: transparent;
        border: none;
        color: white;
        outline: none;
        width: 100%;
    }

    .search-bar input::placeholder {
        color: #cbd5e1;
    }

    .search-bar button {
        background-color: white;
        color: var(--primary-dark);
        border: none;
        padding: 0.6rem 2rem;
        border-radius: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    .content-container {
        margin-top: 20px;
        margin-left: 150px;
        background-color: #21375d;
        width: 1200px;
        padding: 30px;
        border-radius: 15px;
    }

    .tag-type {
        background-color: #F9F2F2;
        height: 60px;
        margin-top: 15px;
        border-radius: 10px;

        display: flex;
        justify-content: space-between;
        align-items: center;

        padding-left: 20px;
        padding-right: 20px;
    }

    /* Back Button Styles */
    .back_Btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background-color: #1A2B49;

        display: flex;
        justify-content: center;
        align-items: center;

        cursor: pointer;
    }

    .back-icon {
        width: 22px;
        height: 22px;
        filter: brightness(0) invert(1);
    }

    /* Title Styles */
    .title {
        display: flex;
        align-items: center;
        gap: 15px;

        margin-left: 15px;
        margin-top: 20px;
    }

    .title-icon {
        width: 40px;
        height: 40px;
    }

    .title h1 {
        margin: 0;
    }

    /* Edit button Styles */
    .edit_delete_box {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: right;

    }

    .edit_Btn,
    .delete_Btn {
        background-color: #B3B6C3;
        color: black;
        border: none;
        padding: 0.6rem 2rem;
        border-radius: 30px;
        font-weight: bold;
        cursor: pointer;
        height: 35px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .feature-icon {
        width: 18px;
        height: 18px;
    }

    /* Add button Styles */
    .add_Btn {
        background-color: #0064CE;
        color: white;
        width: 100%;
        height: 50px;
        margin-top: 20px;
        align-items: center;
        justify-content: right;
        border: none;
        padding: 0.6rem 2rem;
        border-radius: 10px;
        font-weight: bold;
        font-size: larger;
        cursor: pointer;
        display: block;
        margin-left: auto;
        margin-right: auto;
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

    <div class="title">
        <button type="button" class="back_Btn" onclick="window.location.href='tagging-type-management.php'">
            <img src="icon/error.png" class="back-icon" />
        </button>

        <img src="icon/tag.png" class="title-icon" alt="Tagging">

        <h1>Tagging Management</h1>
    </div>

    <div class="content-container">
        <div class="container">
            <div class="search-bar">
                <input type="text" placeholder="Find places and things to do">
                <button>Search</button>
            </div>


            <div class="tag-type">
                <h3>Peaceful</h3>
                <div class="edit_delete_box">
                    <button type="button" class="edit_Btn" onclick="window.location.href='edit-tagging.php'">
                        <img src="icon/edit.png" class="feature-icon" alt="edit">
                        Edit</button>
                    <button type="button" class="delete_Btn" onclick="window.location.href='delete-tagging.php'">
                        <img src="icon/delete.png" class="feature-icon" alt="delete">
                        Delete</button>
                </div>
            </div>

            <div class="tag-type">
                <h3>Relaxing</h3>

                <div class="edit_delete_box">
                    <button type="button" class="edit_Btn" onclick="window.location.href='edit-tagging.php'">
                        <img src="icon/edit.png" class="feature-icon" alt="edit">
                        Edit</button>
                    <button type="button" class="delete_Btn" onclick="window.location.href='delete-tagging.php'">
                        <img src="icon/delete.png" class="feature-icon" alt="delete">
                        Delete</button>
                </div>
            </div>

            <div class="tag-type">
                <h3>Romantic</h3>

                <div class="edit_delete_box">
                    <button type="button" class="edit_Btn" onclick="window.location.href='edit-tagging.php'">
                        <img src="icon/edit.png" class="feature-icon" alt="edit">
                        Edit</button>
                    <button type="button" class="delete_Btn">
                        <img src="icon/delete.png" class="feature-icon" alt="delete">
                        Delete</button>
                </div>
            </div>
        </div>
        <button type="button" class="add_Btn" onclick="window.location.href='add-tagging.php'">Add Tagging</button>
    </div>
</body>

</html>