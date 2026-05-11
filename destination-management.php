<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <title>Destination Management Page</title>
</head>
<style>
     body{
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
        position: relative;  /* add this */
        z-index: 3;          /* add this — higher than header's z-index: 2 */

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
    .travel-preferences-box{
        align-items: center;
        margin-left: 20px;
        border-radius: 30px;
    }

    /* Filter button Styles */
    .filter-actions{
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: center;
        margin-left: 100px; /* push to the right */

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

    .search-bar input::placeholder { color: #cbd5e1; }

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
    .destination{
            width:350px;
            height:flex;
            background-color: white;
            float:left;
            margin: 10px;
            padding:15px;
            box-sizing:border-box;
            border-radius:10px;
            border: 2px solid #b6b5b5ff;
        }
    .destination_name{
        margin: 10px 0 0 0;
    }
    .destination_state{
        margin: 2px 0 25px 0;
    }

    .destination_fee,
    .destination_rating,
    .destination_review_count,
    .destination_description,
    .destination_phone_number {
        margin: 10px 0;        /* tight spacing between all p tags */
        display: block;       /* needed for span (phone number) to respect margin */
    }

        /* Open Hour Styles */
        .destination_open_hour select {
            width: 230px;
            height: 25px;
            border-radius: 30px;
            border: 1px solid #b6b5b5ff;
            padding-left: 10px;
        }

        /* Destination Tag Styles */
        .destination_tag {
            background-color: #D9D9D9;
            color: black;
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 30px;
        }
        .tagging_box {
            display: flex;
            align-items: center;
            justify-content: left;
            gap: 5px;
        }

        /* Edit button Styles */
        .edit_delete_box{
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: right;

        }

        .edit_Btn {
            background-color: #B3B6C3; 
            color: black;
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            height: 35px;
        }

        .delete_Btn {
            background-color: #B3B6C3;  
            color: black;
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            height: 35px;
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
        <h1 style="margin-left: 15px; margin-top: 15px;">
            <img src="icon/destination.png" style="width: 40px; height: 40px; margin-left: 25px; margin-top: 25px; margin-right: 15px;" alt="Tagging Type">
            Destination Management
        </h1>
    </div>

    <div class="filter-bar">
        <select name="mood-box" required>
            <option value="mood">Mood</option>
            <option value="Family">Family</option>
            <option value="Friend">Friend</option>
            <option value="Colleague">Colleague</option>
            <option value="Other">Other</option>
        </select>

        <select name="climate-box" required>
            <option value="climate">Climate</option>
            <option value="Family">Family</option>
            <option value="Friend">Friend</option>
            <option value="Colleague">Colleague</option>
            <option value="Other">Other</option>
        </select>

        <select name="budget-box" required>
            <option value="budget">Budget</option>
            <option value="Family">Family</option>
        </select>

        <select name="travel-companion-box" required>
            <option value="travel-companion">Travel Companion</option>
            <option value="Family">Family</option>
            <option value="Friend">Friend</option>
            <option value="Colleague">Colleague</option>
            <option value="Other">Other</option>
        </select>

        <select name="destination-type-box" required>
            <option value="destination-type">Destination Type</option>
            <option value="Family">Family</option>
            <option value="Friend">Friend</option>
            <option value="Colleague">Colleague</option>
            <option value="Other">Other</option>
        </select>

        <select name="travel-preferences-box" required>
            <option value="">Travel Preferences</option>
            <option value="Family">Family</option>
            <option value="Friend">Friend</option>
            <option value="Colleague">Colleague</option>
            <option value="Other">Other</option>
        </select>

        <div class="filter-actions">
            <button class="btn btn-clear">Clear</button>
            <button class="btn btn-search">Search</button>
        </div>
    </div>



    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Find places and things to do">
            <button>Search</button>
        </div>
            <button type="button" class="add_Btn" onclick="window.location.href='add-destination.php'">+ Add Destination</button>
    </div>
    


    <div class="destination-list">
        <div class="destination">
    
            <img src="capstone assignment/png.png" class="destination_image" />

            <h2 class="destination_name">Menara Kuala Lumpur</h2>

            <p class="destination_state">Kuala Lumpur</p>

            <p class="destination_fee">Fee: RM 140</p>

            <p class="destination_rating">Rating: 4.7</p>

            <p class="destination_review_count">Reviews Count: 1748</p>

            <span class="destination_phone_number">Phone Number: 03-2630 3033</span>

            <p class="destination_description">Description: One of the world's tallest, 
                this landmark tower offers scenic city views & a revolving restaurant...</p>

            <div class="tagging_box">
                <p class="destination_tag">Urban</p>
                <p class="destination_tag">Urban</p>
            </div>

            <div class="edit_delete_box">
                <button type="button" class="edit_Btn" onclick="window.location.href='edit-destination.php'">Edit</button>
                <button type="button" class="delete_Btn" onclick="window.location.href='delete-destination.php'">Delete</button>
            </div>
        </div>

        <div class="destination">
    
            <img src="capstone assignment/png.png" class="destination_image" />

            <h2 class="destination_name">Menara Kuala Lumpur</h2>

            <p class="destination_state">Kuala Lumpur</p>

            <p class="destination_fee">Fee: RM 140</p>

            <p class="destination_rating">Rating: 4.7</p>

            <p class="destination_review_count">Reviews Count: 1748</p>

            <span class="destination_phone_number">Phone Number: 03-2630 3033</span>

            <p class="destination_description">Description: One of the world's tallest, 
                this landmark tower offers scenic city views & a revolving restaurant...</p>

            <div class="tagging_box">
                <p class="destination_tag">Urban</p>
                <p class="destination_tag">Urban</p>
            </div>

            <div class="edit_delete_box">
                <button type="button" class="edit_Btn" onclick="window.location.href='edit-destination.php'">Edit</button>
                <button type="button" class="delete_Btn" onclick="window.location.href='delete-destination.php'">Delete</button>
            </div>
    </div>

     <div class="destination">
    
            <img src="capstone assignment/png.png" class="destination_image" />

            <h2 class="destination_name">Menara Kuala Lumpur</h2>

            <p class="destination_state">Kuala Lumpur</p>

            <p class="destination_fee">Fee: RM 140</p>

            <p class="destination_rating">Rating: 4.7</p>

            <p class="destination_review_count">Reviews Count: 1748</p>

            <span class="destination_phone_number">Phone Number: 03-2630 3033</span>

            <p class="destination_description">Description: One of the world's tallest, 
                this landmark tower offers scenic city views & a revolving restaurant...</p>

            <div class="tagging_box">
                <p class="destination_tag">Urban</p>
                <p class="destination_tag">Urban</p>
            </div>

            <div class="edit_delete_box">
                <button type="button" class="edit_Btn" onclick="window.location.href='edit-destination.php'">Edit</button>
                <button type="button" class="delete_Btn" onclick="window.location.href='delete-destination.php'">Delete</button>
            </div>
    </div>

     <div class="destination">
    
            <img src="capstone assignment/png.png" class="destination_image" />

            <h2 class="destination_name">Menara Kuala Lumpur</h2>

            <p class="destination_state">Kuala Lumpur</p>

            <p class="destination_fee">Fee: RM 140</p>

            <p class="destination_rating">Rating: 4.7</p>

            <p class="destination_review_count">Reviews Count: 1748</p>

            <span class="destination_phone_number">Phone Number: 03-2630 3033</span>

            <p class="destination_description">Description: One of the world's tallest, 
                this landmark tower offers scenic city views & a revolving restaurant...</p>

            <div class="tagging_box">
                <p class="destination_tag">Urban</p>
                <p class="destination_tag">Urban</p>
            </div>

            <div class="edit_delete_box">
                <button type="button" class="edit_Btn" onclick="window.location.href='edit-destination.php'">Edit</button>
                <button type="button" class="delete_Btn" onclick="window.location.href='delete-destination.php'">Delete</button>
            </div>
    </div>
</div>
</body>
</html>