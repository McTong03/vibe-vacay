<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <title>State Destination Page</title>
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

    /* Filter Bar Styles */
    .filter-bar {
        background-color: #1A2B49;
        height: 55px;
        border-radius: 50px;
        margin-left: 8px;
        margin-right: 20px;
        margin-top: 25px;
        width: 1460px;

        display: flex;
        align-items: center;
        gap: 15px;
        padding-left: 20px;
        position: relative;
        /* add this */
        z-index: 3;
        /* add this — higher than header's z-index: 2 */

    }

    .filter-bar select {
        width: 170px;
        height: 30px;
        border-radius: 30px;
        border: none;
        padding-left: 10px;
    }

    .mood-box,
    .climate-box,
    .budget-box,
    .travel-companion-box,
    .destination-type-box,
    .travel-preferences-box {
        align-items: center;
        margin-left: 20px;
        border-radius: 30px;
    }

    /* Filter button Styles */
    .filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: center;
        margin-left: 100px;
        /* push to the right */

    }

    .btn {
        padding: 0.6rem 2rem;
        border-radius: 30px;
        font-weight: bold;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-clear {
        background-color: white;
        color: var(--primary-dark);
        border: none;
    }

    .btn-search {
        background-color: var(--primary-blue);
        color: white;
        border-color: grey;
    }

    /* Global Search Bar */
    .search-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 25px;
        padding-bottom: 1rem;
        margin-right: 20px;
    }

    .search-bar {
        background-color: #1A2B49;
        padding: 0.5rem;
        padding-left: 1.5rem;
        border-radius: 40px;
        display: flex;
        width: 600px;
        justify-content: space-between;
        align-items: center;
        margin: 0 auto;
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

    /*Add Button */
    .add_Btn {
        background-color: #0064CE;
        color: white;
        border: none;
        border-radius: 30px;
        width: 200px;
        height: 40px;
        font-size: 17px;
        font-weight: bold;
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        cursor: pointer;
    }

    /* Destination Styles */
    .destination {
        width: 350px;
        background-color: white;
        float: left;
        margin: 10px;
        padding: 15px;
        box-sizing: border-box;
        border-radius: 10px;
        border: 2px solid #b6b5b5;
        cursor: pointer;
        transition: border 0.2s;
    }

    .destination_details {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .destination_rating,
    .destination_review_count,
    .destination_fee {
        margin: 0;
    }

    .destination_image {
        width: 320px;
        height: 200px;
        border-radius: 10px;
    }

    .destination_name {
        margin: 10px 0 25px 0;
    }

    .destination_state {
        margin: 2px 0 0 0;
    }
    

    .destination_fee,
    .destination_rating,
    .destination_review_count {
        margin: 10px 0;
        /* tight spacing between all p tags */
        display: block;
        /* needed for span (phone number) to respect margin */
    }
    .destination_fee,
    .destination_rating{
        gap: 0;
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
        <h1 style="margin-left: 15px; margin-top: 15px;">Destination Management</h1>
    </div>

    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Find places and things to do">
            <button>Search</button>
        </div>
    </div>

    <div class="destination-list">
        <h1 style="margin-left: 15px; margin-top: 15px;">Top Sights In Kuala Lumpur</h1>

        <div class="destination" onclick="window.location.href='destination-description.php'">
            <img src="Image/kltower.avif" class="destination_image" />

            <p class="destination_state">Kuala Lumpur</p>
            <h2 class="destination_name">Menara Kuala Lumpur</h2>
            <p class="destination_">Climate: Summer</p>

            <div class="destination_details">
                <p class="destination_rating"> 4.7⭐</p>
                <p class="destination_review_count">(1,748)</p>
                <p class="destination_fee">RM 140</p>
            </div>
        </div>
    </div>
</body>

</html>