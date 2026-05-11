<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination Description</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

    @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
</style>

<style>

    body{
        height: 5000px;
        width: 100%;
        margin: 0;  
        padding: 0;
        overflow-x: hidden; 
    }

    #header {
        background-color: #1A2B49;
        height: 55px;
        border-radius: 50px;
        margin-left: 90px;
        margin-right: 20px;
        margin-top: 25px;
        width: calc(100% - 180px);
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
        margin-left: 380px;
        color: white;
    }

    .recommendation {
        margin-top: -37px;
        margin-left: 530px;
        color: white;
    }

    .wishlist {
        margin-top: -37px;
        margin-left: 760px;
        color: white;
    }

    .about-us {
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

    .batucaves1 {
        width: 100%; 
        margin-top: -80px;
        height: 600px;
        filter: brightness(70%);
        margin-left: 0;
        display: block;
        object-fit: cover;
        }

    .batucaves-name,
    .malaysia,
    .tag,
    .heritage-container,
    .culture-container,
    .landmark-container,
    .rating-container {
        position: relative;
        color: white;
    }

    .batucaves-name {
        margin-top: -250px;
        font-size: 55px;
        margin-left: 60px;
    }

    .malaysia {
        font-size: 26px;
        margin-top: -50px;
        margin-left: 60px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif
    }

    .tag{
        margin-left: 60px;
        color: #474141;
        font-size: 18px;
        margin-top: -8px;
    }

    .heritage-container,
    .culture-container,
    .landmark-container {
        background-color: rgba(255, 255, 255, 0.4);
        height: 50px;
        width: 150px;
        border-radius: 30px;
    }

    .heritage-container {
        margin-left: 60px;
        margin-top: 5px;
        
    }

    .heritage,
    .culture,
    .landmark {
        font-size: 20px;
        padding-top: 13px;
        padding-left: 40px;
    }

    .culture-container {
        margin-top: -70px;
        margin-left: 225px;
    }

    .landmark-container {
        margin-top: -70px;
        margin-left: 390px;
    }

    .rating-container {
        background-color: rgba(255, 255, 255, 0.4);
        height: 100px;
        width: 250px;
        border-radius: 30px;
        margin-top: -200px;
        margin-left: 1090px;
    }

    .rating {
        font-size: 64px;
        padding-top: 15px;
        padding-left: 40px;
    }

    .rating-name {
        margin-top: -100px;
        margin-left: 140px;
        font-size: 25px;
    }
    
    .overview {
        font-size: 36px;
        margin-top: 170px;
        margin-left: 50px;
        font-family: 'Open Sans';
        font-weight: bold;
    }

    .overview1,
    .overview2,
    .overview3 {
        font-size: 25px;
        color: #6F6767;
        margin-left: 50px;
    }

    .overview1 {
        margin-top:-10px;
    }

    .highlight {
        color: #383333;
    }

    .overview-container{
        width: 1200px;
    }

    .overview2 {
        margin-top: -10px;
        line-height: 35px;
    }

    .overview3 {
        margin-top: 20px;
        line-height: 35px;
    }

    .add-container1{
        margin-top: -360px;
        margin-left: 800px;
        position: absolute;
        
    }

    .heart-shape {
        width: 34px;
        position: absolute;
        margin-top: 15px;
        margin-left: 22px;
    }

    .add{
        font-size: 20px;
        border: none;
        background-color: #0064CE;
        color: white;
        width: 250px;
        height: 65px;
        border-radius: 20px;
        padding-left: 30px;
    }

    .price-container {
            background-color: #D3D3D4;
            width: 530px;
            margin-top: -380px;
            height: 114px;
            margin-left: 1140px;
            size: 30px;
            border-radius: 20px;
    }

    .price-tag {
        width: 90px;
        margin-top: 10px;
        margin-left: 60px;
    }

    .price {
        font-size: 48px;
        margin-top: -70px;
        margin-left: 260px;
    }

    .gallery {
        font-size: 36px;
        margin-top: 280px;
        margin-left: 50px;
        font-family: 'Open Sans';
        font-weight: bold;
    }

    .batucaves2,
    .batucaves3,
    .batucaves4,
    .batucaves5,
    .batucaves6,
    .batucaves7 {
        width: 450px;
        height: 350px;
        border-radius: 10px;
    }

    .image-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        margin-left: 50px;
        gap: 35px;
        column-gap: 2px;
    }

    .next-button img,
    .next-button1 img {
        width: 70px;
    }

    .next-button {
        border:none;
        background: none;
        position: relative;
        top: -405px;
        left: 1570px;
    }

    .saying {
        font-size: 36px;
        font-family: 'Open Sans';
        font-weight: bold;
        margin-top: -15px;
        margin-left: 50px;
    }

    .batu-caves-name {
        font-size: 12px;
        margin-left: 20px;
        position: relative;
        text-decoration: underline;
    }

    .batu-container {
        max-width: 400px;
        height: 400px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-left: 50px;
        border-radius: 18px;
        
    }

    .card-container {
        display: grid;
        grid-template-columns: repeat(3, 450px);
        gap: 10px;
        row-gap: 30px;
    }

    .star1 {
        width: 15px;
        position: relative;
        left: 20px;
    }

    .profile-picture {  
        width: 35px;
        position: relative;
        top: 10px;
        left: 20px;
    }

    .rating-names {
        left: 70px;
        font-size: 12px;
        position: relative;
        top: -37px;
    }

    .rating-date {
        font-size: 10px;
        position: relative;
        left: 70px;
        top: -46px;
    }

    .rating-description {
        font-size: 14px;
        width: 340px;
        position: relative;
        left: 20px;
        top: -30px;
        line-height: 20px;
    }

    .batucaves8 {
        width: 76px;
        border-radius: 8px;
        position: relative;
        left: 20px;
        height: 76px;
    }

    .next-button1 {
        border:none;
        background: none;
        position: relative;
        top: -600px;
        left: 1350px;
    }

    .experience {
        font-size: 36px;
        margin-left: 50px;
        font-family: 'Open Sans';
        font-weight: bold;
        margin-top: -130px;
    }

    .rating-experience-container {
        width: 1200px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        height: 500px;
        margin-left: 50px;
        margin-top: -20px;
        background-color: #FAF9F9;
    }

    .rating-name1,
    .description1,
    .photo {
        font-size: 20px;
        text-decoration: underline;
        font-family: 'Open Sans';
        font-weight: bold;
        padding-left: 40px;
    }

    .rating-name1 {
        padding-top: 20px;
    }

    .star2 {
        width:  40px;
        margin-top: -6px;
        margin-left: 40px;
    }

    .star-container {
        border: none;
        background-color: #FAF9F9;
    }

    .description1 {
        padding-top: 10px;
    }

    #search-input {
        background-color: #E8E5E5;
        width: 800px;
        height: 50px;
        border-radius: 10px;
        margin-left: 40px;
        border: none;
    }

    #search-input::placeholder {
        padding-left: 20px;
        color: black;
    }

    .photo {
        margin-top: 30px;
    }

    .upload-box {
        display: flex;
        background-color: #E8E5E5;
        width: 300px;
        height: 50px;
        border-radius: 10px;
        margin-left: 40px;
    }

    .upload-icon {
        width: 35px;
        height: 35px;
        margin-top: 8px;
        margin-left: 20px;
    }

    .upload-image {
        padding-top: 15px;
        padding-left: 30px;
        font-size: 20px;
    }

    .submit-button {
        margin-top: 50px;
        margin-left: 950px;
        width: 200px;
        height: 50px;
        font-weight: bold;
        font-size: 20px;
        background-color: #CECCCC;
        border: none;
    }

    .similar-place {
        font-size: 36px;
        margin-top: 60px;
        margin-left: 50px;
        font-family: 'Open Sans';
        font-weight: bold;
    }

    .similar-container {
        height: 460px;
        width: 400px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-left: 50px;
        border-radius: 10px;
    }

    .thean-hou,
    .national-mosque,
    .genting-highlands {
        width: 400px;
        height: 300px;
    }

    .kuala-lumpur,
    .thean-hou-temple,
    .hot,
    .ratings1 {
        padding-left: 30px;
    }
    
    .kuala-lumpur {
        font-size: 12px;
        color: #63687A;
        padding-top: 10px;
    }

    .thean-hou-temple {
        font-size: 17px;
        margin-top: -5px
    }

    .hot {
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
        margin-left: 200px;
    }

    .free {
        color: #1A2B49;
        font-size: 17px;
        margin-top: -33px;
        margin-left: 235px;
    }

    .similar-card-container {
        display: grid;
        grid-template-columns: repeat(3, 470px);
    }

    .next-button1 {
        margin-top: 320px;
        margin-left: 40px;
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

    <img class="batucaves1" src="image/batucaves1.avif">

    <p class="batucaves-name">Batu Caves</p>
    <p class="malaysia">Selangor, Malaysia</p>
    <p class="tag">Tag:</p>

    <div class="heritage-container">
        <p class="heritage">Heritage</p>
    </div>

    <div class="culture-container">
        <p class="culture">Culture</p>
    </div>
    
    <div  class="landmark-container">
        <p class="landmark">Landmark</p>
    </div>

    <div class="rating-container">
        <p class="rating">4.7</p>
        <p class="rating-name">Rating</p>
    </div>

    <div class="overview-container">
        <p class="overview">Overview</p>
        <p class="overview1"><strong><span class="highlight">Batu Caves</span></strong> is one of Malaysia's most iconic cultural and religious landmarks.</p>
        <p class="overview2">Located about 12km north of Kuala Lumpur, it features a stunning limestone cave complex and a Hindu temple dedicated to Lord Murugan.</p>
        <p class="overview3">Famous for its towering 42.7 meter golden statue and 272 colorful steps. It's a must -visit destination that blends spiritual heritage with natural beauty.</p>
    </div>

    <div class="add-container1">
        <img class="heart-shape" src="icon/heart.png">
        <button  class="add">Add to Wishlist</button>
    </div>
    
    <div class="price-container">
        <img class="price-tag" src="icon/price-tag.png">
        <p class="price">Free</p>
    </div>

    <p class="gallery">Gallery</p>

    <div class="image-container">
        <img class="batucaves2" src="image/batucaves1.avif">
        <img class="batucaves3" src="image/batucaves2.avif">
        <img class="batucaves4" src="image/batucaves3.avif">
        <img class="batucaves5" src="image/batucaves4.avif">
        <img class="batucaves6" src="image/batucaves5.avif">
        <img class="batucaves7" src="image/batucaves6.avif">
    </div>

    <button class="next-button">
        <img src="icon/next.png">
    </button>

    <p class="saying">What people saying about Batu Caves</p>

    <div class="card-container">
        <div class="batu-container">
            <div>
                <p class="batu-caves-name">Batu Caves</p>
            </div>

            <div>
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
            </div>

            <div>
                <img class="profile-picture" src="image/profile-picture.png">
            </div>

            <div>
                <p class="rating-names">John Tan</p>
            </div>

            <div>
                <p class="rating-date">March 31,2026</p>
            </div>

            <div>
                <p class="rating-description">The Batu Caves were absolutely breathtaking! The golden state and colorful stairs create such an unforgettable sight. The spiritual inside the caves is truly powerful</p>
            </div>

            <div>
                <img class="batucaves8" src="image/batucaves6.avif">
            </div>
  
        </div>

        <div class="batu-container">
            <div>
                <p class="batu-caves-name">Batu Caves</p>
            </div>

            <div>
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
            </div>

            <div>
                <img class="profile-picture" src="image/profile-picture.png">
            </div>

            <div>
                <p class="rating-names">John Tan</p>
            </div>

            <div>
                <p class="rating-date">March 31,2026</p>
            </div>

            <div>
                <p class="rating-description">The Batu Caves were absolutely breathtaking! The golden state and colorful stairs create such an unforgettable sight. The spiritual inside the caves is truly powerful</p>
            </div>

            <div>
                <img class="batucaves8" src="image/batucaves6.avif">
            </div>
    
        </div>

        <div class="batu-container">
            <div>
                <p class="batu-caves-name">Batu Caves</p>
            </div>

            <div>
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
            </div>

            <div>
                <img class="profile-picture" src="image/profile-picture.png">
            </div>

            <div>
                <p class="rating-names">John Tan</p>
            </div>

            <div>
                <p class="rating-date">March 31,2026</p>
            </div>

            <div>
                <p class="rating-description">The Batu Caves were absolutely breathtaking! The golden state and colorful stairs create such an unforgettable sight. The spiritual inside the caves is truly powerful</p>
            </div>

            <div>
                <img class="batucaves8" src="image/batucaves6.avif">
            </div>
    
        </div>

        <div class="batu-container">
            <div>
                <p class="batu-caves-name">Batu Caves</p>
            </div>

            <div>
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
            </div>

            <div>
                <img class="profile-picture" src="image/profile-picture.png">
            </div>

            <div>
                <p class="rating-names">John Tan</p>
            </div>

            <div>
                <p class="rating-date">March 31,2026</p>
            </div>

            <div>
                <p class="rating-description">The Batu Caves were absolutely breathtaking! The golden state and colorful stairs create such an unforgettable sight. The spiritual inside the caves is truly powerful</p>
            </div>

            <div>
                <img class="batucaves8" src="image/batucaves6.avif">
            </div>
    
        </div>

        <div class="batu-container">
            <div>
                <p class="batu-caves-name">Batu Caves</p>
            </div>

            <div>
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
            </div>

            <div>
                <img class="profile-picture" src="image/profile-picture.png">
            </div>

            <div>
                <p class="rating-names">John Tan</p>
            </div>

            <div>
                <p class="rating-date">March 31,2026</p>
            </div>

            <div>
                <p class="rating-description">The Batu Caves were absolutely breathtaking! The golden state and colorful stairs create such an unforgettable sight. The spiritual inside the caves is truly powerful</p>
            </div>

            <div>
                <img class="batucaves8" src="image/batucaves6.avif">
            </div>
    
        </div>

        <div class="batu-container">
            <div>
                <p class="batu-caves-name">Batu Caves</p>
            </div>

            <div>
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
                <img class="star1" src="icon/star.png">
            </div>

            <div>
                <img class="profile-picture" src="image/profile-picture.png">
            </div>

            <div>
                <p class="rating-names">John Tan</p>
            </div>

            <div>
                <p class="rating-date">March 31,2026</p>
            </div>

            <div>
                <p class="rating-description">The Batu Caves were absolutely breathtaking! The golden state and colorful stairs create such an unforgettable sight. The spiritual inside the caves is truly powerful</p>
            </div>

            <div>
                <img class="batucaves8" src="image/batucaves6.avif">
            </div>
    
        </div>
    </div>

    <button class="next-button1">
        <img src="icon/next.png">
    </button>

    <p class="experience">Write Your Experience</p>

    <div class="rating-experience-container">
        <div>
            <p class="rating-name1">Rating:</p>
        </div>
        
        <div>
            <button class="star-container">
                <img class="star2" src="icon/star1.png">
            </button>

            <button class="star-container">
                <img class="star2" src="icon/star1.png">
            </button>

            <button class="star-container">
                <img class="star2" src="icon/star1.png">
            </button>

            <button class="star-container">
                <img class="star2" src="icon/star1.png">
            </button>

            <button class="star-container">
                <img class="star2" src="icon/star1.png">
            </button>
 
        </div>

        <div>
            <p class="description1">Description:</p>
            <input id="search-input" type="text" name="search" placeholder="Leave a comment..."/>
        </div>

        <div>
            <p class="photo">Photo:</p>
            <label class="upload-box">
                <input type="file" accept="image/*" hidden>
                
                <img src="icon/upload.png" class="upload-icon">
                <span class="upload-image">Upload image</span>
            </label>
        </div>

        <div class="submit-container">
            <button class="submit-button">Submit</button>
        </div>

    </div>

    <p class="similar-place">Similar Place</p>

    <div class="similar-card-container">
        <div class="similar-container">
            <div>
                <img class="thean-hou" src="image/thean-hou.avif">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="thean-hou-temple">Thean Hou Temple</p>
            </div>

            <div>
                <p class="hot">Climate: Hot</p>
            </div>

            <div>
                <p class="ratings1">4.5</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,728)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="free">Free</p>
            </div>
        </div>

        <div class="similar-container">
            <div>
                <img class="national-mosque" src="image/national-mosque.avif">
            </div>

            <div>
                <p class="kuala-lumpur">Kuala Lumpur</p>
            </div>

            <div>
                <p class="thean-hou-temple">Nasional Mosque</p>
            </div>

            <div>
                <p class="hot">Climate: Tropical</p>
            </div>

            <div>
                <p class="ratings1">4.9</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,748)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="free">Free</p>
            </div>
        </div>

        <div class="similar-container">
            <div>
                <img class="genting-highlands" src="image/genting-highlands.jpg">
            </div>

            <div>
                <p class="kuala-lumpur">Pahang</p>
            </div>

            <div>
                <p class="thean-hou-temple">Genting Highlands (Theme Park)</p>
            </div>

            <div>
                <p class="hot">Climate: Cool</p>
            </div>

            <div>
                <p class="ratings1">4.7</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(1,728)</p>

            </div>

            <div>
                <p class="from">From</p>
                <p class="free">RM167</p>
            </div>
        </div>
    </div>

    <button class="next-button1">
        <img src="icon/next.png">
    </button>

</body>

</html>