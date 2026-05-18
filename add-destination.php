<?php
if (isset($_POST['btn-add'])) {
    include("conn.php");

    $destination_name = mysqli_real_escape_string($con, $_POST['destination_name']);
    $default_points = intval($_POST['default_points']);

    $sql = "INSERT INTO destinations ( destination_name, default_points)
                VALUES ('$action_name', '$default_points')";

    if (!mysqli_query($con, $sql)) {
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);

    echo '<script>alert("New destination created successfully!");
            window.location.href = "A-PointManagement.php";
            </script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination Page</title>
</head>
<style>
    body {
        height: 1000px;
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


    /* Content Styles */
    .content-container {
        margin-top: 20px;
        margin-left: 150px;
        background-color: #21375d;
        width: 1200px;
        padding: 30px;
        border-radius: 15px;
    }

    .container h3 {
        margin: 0;
        color: white;
    }

    /* Tag Type Styles */
    .tag-type {
        background-color: #F9F2F2;
        width: 95%;
        height: 45px;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 10px;

        display: flex;
        justify-content: center;
        align-items: center;

        padding-left: 20px;
        padding-right: 20px;
    }

    .description-box {
        height: 150px;
        resize: vertical;
        padding-top: 15px;
        padding-bottom: 10px;
        align-items: flex-start;
        font-family: inherit;
        font-size: 1rem;
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

    .filter-bar {
        height: 55px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        position: relative; 
        z-index: 3;     
    }

    .filter-bar select {
        width: 1180px;
        height: 30px;
        background-color: #F9F2F2;
        border-radius: 10px;
        padding-left: 10px;
    }

    .filter-box{
        align-items: center;
        margin-left: 20px;
        border-radius: 30px;
    }
    .title h1 {
        margin: 0;
}


    /* Add button Styles */
    .filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
    }

    .btn {
        padding: 0.6rem 2rem;
        border-radius: 10px;
        font-weight: bold;
        font-size: 0.9rem;
        cursor: pointer;
        width: 200px;
    }

    .btn-reset {
        background-color: white;
        color: var(--primary-dark);
        border: none;
    }

    .btn-add {
        background-color: #0064CE;
        color: white;
        border-color: grey;
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
        <button type="button" class="back_Btn" onclick="window.location.href='destination-management.php'">
            <img src="icon/error.png" class="back-icon" />
        </button>

        <img src="icon/destination.png" class="title-icon" alt="Destination">

        <h1>Add Destination</h1>
    </div>

    <div class="content-container">
        <div class="container">
            <h3>Destination Name</h3>
            <input type="text" class="tag-type" placeholder="Enter destination name">

             <h3>Destination Picture</h3>
            <input type="file" class="tag-type" placeholder="Enter destination picture URL">

            <h3>Destination State</h3>
            <div class="filter-bar">
                <select name="filter-box" required>
                    <option value="">Please Select</option>
                    <option value="mood">Johor</option>
                    <option value="Family">Kedah</option>
                    <option value="Friend">Melacca</option>
                    <option value="Colleague">Negeri Sembilan</option>
                    <option value="Other">Pahang</option>
                    <option value="Other">Penang</option>
                    <option value="Other">Perak</option>
                    <option value="Other">Perlis</option>
                    <option value="Other">Sabah</option>
                    <option value="Other">Sarawak</option>
                    <option value="Other">Terengganu</option>
                    <option value="Other">Kuala Lumpur</option>
                    <option value="Other">Putrajaya</option>
                    <option value="Other">Labuan</option>
                </select>
            </div>

             <h3>Tagging Type</h3>
            <div class="filter-bar">
                <select name="filter-box" required>
                    <option value="">Please Select</option>
                    <option value="mood">Mood</option>
                    <option value="Family">Climate</option>
                    <option value="Friend">Travel Companion</option>
                    <option value="Colleague">Destination Type</option>
                    <option value="Other">Hidden Destination</option>
                    <option value="Other">Budget</option>
                </select>
            </div>

            <h3>Tagging</h3>
            <div class="filter-bar">
                <select name="filter-box" required>
                    <option value="">Please Select</option>
                    <option value="mood">Mood</option>
                    <option value="Family">Climate</option>
                    <option value="Friend">Travel Companion</option>
                    <option value="Colleague">Destination Type</option>
                    <option value="Other">Hidden Destination</option>
                    <option value="Other">Budget</option>
                </select>
            </div>

            <h3>Destination Price (RM)</h3>
            <input type="text" class="tag-type" placeholder="Enter destination price">

            <h3>Destination Rating (/5)</h3>
            <input type="text" class="tag-type" placeholder="Enter destination rating">

            <h3>Destination Phone Number (01x-xxxxxxx)</h3>
            <input type="text" class="tag-type" placeholder="Enter destination phone number">

            <h3>Destination Description</h3>
            <textarea class="tag-type description-box" placeholder="Enter destination description"></textarea>

            <div class="filter-actions">
                <button type="reset" class="btn btn-reset">Reset</button>
                <button class="btn btn-add" onclick="window.location.href='destination-management.php'">Add Destination</button>
            </div>
        </div>
    </div>
</body>

</html>