<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conn.php';


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
               d.reviews_count,
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
$count = mysqli_num_rows($result);

if(isset($_GET['favourite_id'])) {
    $favourite_id = $_GET['favourite_id'];
    $user_id = $_SESSION['user_id'];
    
    $sql = "DELETE FROM favorites WHERE favourite_id = '$favourite_id' AND user_id = '$user_id'";
    mysqli_query($conn, $sql);

    header('Location: wishlist.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/wishlist.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
    </style>

</head>

<body>

    <?php include('./includes/navbar.php'); ?>

    <img class="favourite" src="icon/favourite.png">

    <p class="wishlist-name">Wishlist</p>



    <div class="similar-card-container">
    <?php 
    if($count > 0) {
        $i = 0; // ← added
        while($row = mysqli_fetch_assoc($result)) {
            $i++; // ← added
    ?>
        <div class="similar-container <?php echo $i > 8 ? 'hidden-card' : ''; ?>" 
            onclick="window.location.href='destination-description.php?id=<?php echo $row['destination_id']; ?>'"
            style="cursor: pointer;">
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
                <p class="number-rating">(<?php echo $row['reviews_count']; ?>)</p>
            </div>

            <div style="display:flex; align-items:center; gap:6px; padding-left:170px; margin-top:-18px;">
                    <span style="color:#63687A; font-size:14px; ">From</span>
                    <span style="color:#1A2B49; font-size:17px; font-weight:bold;"><?php
                        $p = trim($row['price']);
                        $p = preg_replace('/^RM\s*/i', '', $p);
                        echo ($p == '0' || strtolower($p) == 'free' || empty($p)) ? 'Free' : 'RM ' . htmlspecialchars($p);
                ?></p>
            </div>

            <!-- ✅ Heart button removes from wishlist -->
            <button class="heart-container" 
                    onclick="event.stopPropagation(); removeWishlist(<?php echo $row['favourite_id']; ?>)">
                <img class="heart-button" src="icon/heart.png">
            </button>
        </div>
    <?php 
        }
    } else {
        echo '<p style="color:#666; margin-left:80px; font-size:20px;">Your wishlist is empty.</p>';
    }
    ?>
    </div>

    <div class="view-more-container" <?php echo $count <= 8 ? 'style="display:none;"' : ''; ?>>
        <button class="view-more" onclick="showMore()">View More</button>
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

var expanded = false;

function showMore() {
    expanded = !expanded;
    var btn = document.querySelector('.view-more');
    var cards = document.querySelectorAll('.similar-container');

    if (expanded) {
        cards.forEach(function(card) {
            card.style.display = 'block';
        });
        btn.textContent = 'Show Less';
    } else {
        cards.forEach(function(card, i) {
            card.style.display = i >= 8 ? 'none' : 'block';
        });
        btn.textContent = 'View More';
    }
}
</script>
    

</body>

</html>