<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

    @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
</style>

<style>
    body{
        height: 5000px;
    }

    #header {
        background-color: #1A2B49;
        height: 55px;
        border-radius: 50px;
        margin-left: 20px;
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
    .recommendation,
    .wishlist,
    .about-us,
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
        margin-left: 400px;
        color: white;
    }

    .recommendation {
        margin-top: -37px;
        margin-left: 550px;
        color: white;
    }

    .wishlist {
        margin-top: -37px;
        margin-left: 780px;
        color: white;
    }

    .about-us {
        margin-top: -37px;
        margin-left: 950px;
        color: white;
    }

    .logout {
        margin-top: -37px;
        margin-left: 1200px;
        color: white;
    }

    .profile-box{
        background-color: white;
        width: 160px;
        height: 35px;
        margin-top: -46px;
        margin-left: 1300px;
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

    .favourite {
        width: 70px;
        margin-top: 60px;
        margin-left: 90px;
    }

    .wishlist-name {
        margin-top: -83px;
        font-size: 50px;
        margin-left: 200px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        font-weight: bold;
    }

    .similar-container {
        height: 460px;
        width: 300px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-left: 70px;
        border-radius: 10px;
        margin-top: 10px;
    }

    .KLCC,
    .batu-caves,
    .butterfly-image,
    .bird-park-image,
    .sunway-lagoon-image {
        width: 300px;
        height: 300px;
    }

    .kuala-lumpur,
    .Petronas-Twin-Towers,
    .batu-cave,
    .butterfly,
    .bird-park,
    .theme-park,
    .summer,
    .ratings1 {
        padding-left: 30px;
    }
    
    .kuala-lumpur {
        font-size: 12px;
        color: #63687A;
        padding-top: 10px;
    }

    .Petronas-Twin-Towers,
    .batu-cave,
    .butterfly,
    .bird-park,
    .theme-park {
        font-size: 17px;
        margin-top: -5px
    }

    .summer {
        color: #1A2B49;
        font-size: 12px;
    }

    .ratings1 {
        margin-top: 30px;
        font-size: 14px;
        color: #1A2B49;
    }

    .star-icon {
        width: 24px;
        margin-left: 54px;
        top: -36px;
        position: relative;
    }

    .number-rating {
        color: #63687A;
        font-size: 10px;
        margin-top: -56px;
        margin-left: 83px;
    }

    .from {
        color: #63687A;
        font-size: 14px;
        margin-top: -25px;
        margin-left: 180px;
    }

    .RM {
        color: #1A2B49;
        font-size: 17px;
        margin-top: -33px;
        margin-left: 215px;
    }

    .similar-card-container {
        display: grid;
        grid-template-columns: repeat(4, 360px);
        row-gap: 40px;
        position: relative;
    }

    .heart-button {
        width: 25px;
        margin-top: -11px;
        margin-left: -13px;
        position: absolute;
    }

    .heart-container {
        border: none;
        background-color: white;
        border-radius: 30px;
        width: 40px;
        height: 40px;
        position: absolute;
        margin-top: -450px;
        margin-left: 250px;
    }

</style>

<body>
    <header id="header">
        <div class="logo-container">
            <img src="icon/LogoName.png" class="logo" />
        </div>

        <p class="logo-name">Vibe Vacay</p>
        <p class="home">Home</p>
        <p class="recommendation">Recommendation</p>
        <p class="wishlist">Wishlist</p>
        <p class="about-us">About Us</p>
        <p class="logout">Log Out</p>


        <div class="profile-box">
            <p class="profile">Profile</p>
            <img src="icon/profile1.jpg" class="profile-icon" />

        </div>
    </header>

    <img class="favourite" src="icon/favourite.png">

    <p class="wishlist-name">Wishlist</p>

    <div class="similar-card-container">
        <div class="similar-container">
            <div>
                <img class="KLCC" src="Image/KLCC.jpg">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="Petronas-Twin-Towers">Petronas Twin Towers</p>
            </div>

            <div>
                <p class="summer">Climate: Summer</p>
            </div>

            <div>
                <p class="ratings1">4.6</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,748)</p>

            </div>

            <div>
                <p class="from">Fom</p>
                <p class="RM">RM42</p>
            </div>

            <div>
                <button class="heart-container">
                    <img class="heart-button" src="icon/heart.png">
                </button>
            </div>
        </div>

        <div class="similar-container">
            <div>
                <img class="batu-caves" src="Image/batucaves.jpg">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="batu-cave">Batu Caves</p>
            </div>

            <div>
                <p class="summer">Climate: Summer</p>
            </div>

            <div>
                <p class="ratings1">4.7</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,748)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="RM">FREE</p>
            </div>

            <div>
                <button class="heart-container">
                    <img class="heart-button" src="icon/heart.png">
                </button>
            </div>
        </div>

        <div class="similar-container">
            <div>
                <img class="butterfly-image" src="Image/butterfly.jpg">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="butterfly">Kuala Lumpur Butterfly Park</p>
            </div>

            <div>
                <p class="summer">Climate: Summer</p>
            </div>

            <div>
                <p class="ratings1">4.2</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,748)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="RM">RM30</p>
            </div>

            <div>
                <button class="heart-container">
                    <img class="heart-button" src="icon/heart.png">
                </button>
            </div>
        </div>

        <div class="similar-container">
            <div>
                <img class="bird-park-image" src="Image/bird-park.jpg">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="bird-park">KL Bird Park</p>
            </div>

            <div>
                <p class="summer">Climate: Summer</p>
            </div>

            <div>
                <p class="ratings1">4.5</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,748)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="RM">RM90</p>
            </div>

            <div>
                <button class="heart-container">
                    <img class="heart-button" src="icon/heart.png">
                </button>
            </div>
        </div>

        <div class="similar-container">
            <div>
                <img class="sunway-lagoon-image" src="Image/sunway-lagoon.jpg">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="theme-park">Sunway Lagoon Theme Park</p>
            </div>

            <div>
                <p class="summer">Climate: Summer</p>
            </div>

            <div>
                <p class="ratings1">4.8</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(7,417)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="RM">RM193</p>
            </div>

            <div>
                <button class="heart-container">
                    <img class="heart-button" src="icon/heart.png">
                </button>
            </div>
        </div>
    </div>
    

</body>

</html>