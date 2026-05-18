<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT f.favourite_id, 
               d.destination_id,
               d.destination_name, 
               d.image_url, 
               d.average_rating,
               d.price,
               s.state_name,
               GROUP_CONCAT(dt.tag_name SEPARATOR ', ') as climate
        FROM favorites f
        JOIN destinations d ON f.destination_id = d.destination_id
        JOIN states s ON d.state_id = s.state_id
        LEFT JOIN destination_tags dt ON d.destination_id = dt.tag_id
        WHERE f.user_id = '$user_id'
        GROUP BY f.favourite_id";
$result = mysqli_query($conn, $sql);

// if(isset($_GET['favourite_id'])) {
//     $favourite_id = $_GET['favourite_id'];
//     $user_id = $_SESSION['user_id'];
    
//     $sql = "DELETE FROM favorites WHERE favourite_id = '$favourite_id' AND user_id = '$user_id'";
//     mysqli_query($conn, $sql);
// }

// header('Location: wishlist.php');
// exit();
?>



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
        height: 2000px;
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

    .favourite {
        width: 70px;
        margin-top: 60px;
        margin-left: 150px;
    }

    .wishlist-name {
        margin-top: -83px;
        font-size: 50px;
        margin-left: 250px;
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

    .view-more-container {
        margin-top: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .view-more-container::before,
    .view-more-container::after {
        content: "";
        flex: 1;
        height: 2px;
        background-color: #1A2B49;
    }

    .view-more-container::before {
        margin-right: 10px;
    }

    .view-more-container::after {
        margin-left: 10px;
    }

    .view-more {
        width: 150px;
        height: 55px;
        font-size: 20px;
        background-color: #1A2B49;
        color: white;
        border-radius: 50px;
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
    <?php 
    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) { 
    ?>
        <div class="similar-container">
            <div>
                <?php $firstImg = !empty($row['image_url']) ? explode(',', $row['image_url'])[0] : 'image/default.jpg'; ?>
                <img class="KLCC" src="<?php echo htmlspecialchars(trim($firstImg)); ?>">
            </div>

            <div>
                <p class="kuala-lumpur"><?php echo htmlspecialchars($row['state_name']); ?></p>
            </div>

            <div>
                <p class="Petronas-Twin-Towers"><?php echo htmlspecialchars($row['destination_name']); ?></p>
            </div>

            <div>
                <p class="summer">Climate: <?php echo !empty($row['climate']) ? htmlspecialchars($row['climate']) : 'N/A'; ?></p>
            </div>

            <div>
                <p class="ratings1"><?php echo $row['average_rating']; ?></p>
                <img class="star-icon" src="icon/star.png">
            </div>

            <div style="display:flex; align-items:center; gap:6px; padding-left:170px; margin-top:-60px;">
                    <span style="color:#63687A; font-size:14px; margin-top:-17px; ">From</span>
                    <span style="color:#1A2B49; font-size:17px; font-weight:bold;"><?php
                        $p = trim($row['price']);
                        $p = preg_replace('/^RM\s*/i', '', $p);
                        echo ($p == '0' || strtolower($p) == 'free' || empty($p)) ? 'Free' : 'RM ' . htmlspecialchars($p);
                ?></p>
            </div>

            <!-- ✅ Heart button removes from wishlist -->
            <button class="heart-container" onclick="removeWishlist(<?php echo $row['favourite_id']; ?>)">
                <img class="heart-button" src="icon/heart.png">
            </button>
        </div>
    <?php 
        }
    } else {
        echo '<p style="color:#666; margin-left:150px; font-size:18px;">Your wishlist is empty.</p>';
    }
    ?>
    </div>

<script>
function removeWishlist(favourite_id) {
    if (!confirm('Remove from wishlist?')) return;

    var formData = new FormData();
    formData.append('favourite_id', favourite_id);

    fetch('remove-wishlist.php', {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
    

</body>

</html>