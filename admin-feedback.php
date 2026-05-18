<?php
require 'conn.php';
 
if (!isset($_GET['id'])) {
    $first = $conn->query("SELECT destination_id FROM destinations ORDER BY destination_id ASC LIMIT 1");
    $row   = $first->fetch_assoc();
    if ($row) {
        header("Location: admin-feedback.php?id=" . $row['destination_id']);
        exit();
    } else {
        die('<p style="padding:40px; font-size:20px;">No destinations found in database.</p>');
    }
}
 
$destination_id = intval($_GET['id']);

   
$stmt = $conn->prepare("
    SELECT 
        d.destination_id,
        d.destination_name,
        d.description,
        d.image_url,
        d.average_rating,
        d.price,
        d.reviews_count,
        d.phone_number,
        s.state_name
    FROM destinations d
    LEFT JOIN states s ON s.state_id = d.state_id
    WHERE d.destination_id = ?
");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$result      = $stmt->get_result();
$destination = $result->fetch_assoc();
$stmt->close();
 
if (!$destination) {
    header("Location: admin-feedback.php");
    exit();
}
 
// ── Fetch tags for this destination ─────────────────────────
$stmt = $conn->prepare("
    SELECT t.tag_name
    FROM destination_tag_mapping dtm
    JOIN destination_tags t ON t.tag_id = dtm.tag_id
    WHERE dtm.destination_id = ?
");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$tags_result = $stmt->get_result();
$tags = [];
while ($tag = $tags_result->fetch_assoc()) {
    $tags[] = $tag['tag_name'];
}
$stmt->close();

$stmt = $conn->prepare("
    SELECT 
        r.review_id,
        r.rating,
        r.comment,
        r.image_url,
        r.created_at,
        u.user_name,
        COALESCE(p.profile_picture, 'image/default-profile.jpg') AS profile_picture
    FROM reviews r
    JOIN users u ON u.user_id = r.user_id
    LEFT JOIN user_profile p ON p.user_id = r.user_id
    WHERE r.destination_id = ?
    ORDER BY r.review_id DESC
");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = [];
while ($review = $reviews_result->fetch_assoc()) {
    $reviews[] = $review;
}
$stmt->close();

$stmt = $conn->prepare("
    SELECT 
        d.destination_id,
        d.destination_name,
        d.image_url,
        d.average_rating,
        d.reviews_count,
        d.price,
        s.state_name
    FROM destinations d
    LEFT JOIN states s ON s.state_id = d.state_id
    WHERE d.state_id = (SELECT state_id FROM destinations WHERE destination_id = ?)
      AND d.destination_id != ?
    LIMIT 15
");
$stmt->bind_param("ii", $destination_id, $destination_id);
$stmt->execute();
$similar_result = $stmt->get_result();
$similar = [];
while ($row = $similar_result->fetch_assoc()) {
    $similar[] = $row;
}
$stmt->close();
 
function imgSrc($url, $fallback = 'image/default.jpg') {
    return !empty($url) ? htmlspecialchars($url) : $fallback;
}
 
function formatPrice($price) {
    $price = trim($price);
    $price = preg_replace('/^RM\s*/i', '', $price);
    return ($price == '0' || strtolower($price) == 'free' || empty($price))
        ? 'Free'
        : 'RM ' . htmlspecialchars($price);
}
 
$firstImage = explode(',', $destination['image_url'])[0];
$heroImg = imgSrc(trim($firstImage));

$galleryImages = [];
if (!empty($destination['image_url'])) {
    $allImages = array_filter(array_map('trim', explode(',', $destination['image_url'])));
    $allImages = array_values($allImages);
    $galleryImages = array_slice($allImages, 1);
}
$galleryJson = json_encode($galleryImages);
$similarJson = json_encode($similar);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Feedback</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

    @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
</style>

<style>
    body{
        height: 4000px;
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
        margin-top: -180px;
        font-size: 45px;
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
        margin-top: -130px;
        margin-left: 1150px;
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
    
    .overview-price-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 40px;
        margin-top: 40px;
        padding: 0 50px;
    }

    .overview-container {
        flex: 1;
    }
    
    .overview {
        font-size: 36px;
        margin-top: 110px;
        margin-left: 0;
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

    .price-container {
        background-color: #D3D3D4;
        width: 300px;
        min-width: 530px;
        height: 114px;
        border-radius: 20px;
        margin-top: 50px;
        flex-shrink: 0;
        align-self: flex-start;
    }

    .price-tag {
        width: 90px;
        margin-top: 10px;
        margin-left: 60px;
    }

    .price {
        font-size: 48px;
        margin-top: -70px;
        margin-left: 240px;
    }

    .gallery {
        font-size: 36px;
        margin-top: 40px;
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
    .next-button1 img,
    .next-button2 img {
        width: 70px;
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
        left: 1350px;
    }

    .similar-place {
        font-size: 36px;
        margin-left: 50px;
        font-family: 'Open Sans';
        font-weight: bold;
        padding-top: 400px;
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

    .from-free-row {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px 15px;
    }
 
    .from {
        color: #63687A;
        font-size: 14px;
        margin-top: -50px;
        margin-left: 200px;
    }
 
    .free {
        color: #1A2B49;
        font-size: 17px;
        font-weight: bold;
        margin-top: -50px;
    }

    .similar-card-container {
        display: grid;
        grid-template-columns: repeat(3, 470px);
    }

    .trash-button {
        width: 30px;
        margin-left: 330px;
        margin-top: 10px;
        position: absolute;
    }

    .trash-container {
        border: none;
        background-color: white;
    }

    .next-button2 {
        border:none;
        background: none;
        position: relative;
        top: -300px;
        left: 1400px;
    }

    .no-reviews {
        margin-left: 60px;
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

    <img class="batucaves1" src="<?php echo $heroImg; ?>" alt="<?php echo htmlspecialchars($destination['destination_name']); ?>">

    <p class="batucaves-name"><?php echo htmlspecialchars($destination['destination_name']); ?></p>
    <p class="malaysia"><?php echo htmlspecialchars($destination['state_name']); ?>, Malaysia</p>
    
    <?php if (!empty($tags)): ?>
        <p class="tag">Tag:</p>
        <div class="tags-row">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag-pill"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            </div>
    <?php endif; ?>

    <div class="rating-container">
        <p class="rating"><?php echo number_format($destination['average_rating'], 1); ?></p>
        <p class="rating-name">Rating</p>
    </div>

    <div class="overview-price-wrapper">
        <div class="overview-container">
            <p class="overview">Overview</p>
            <p class="overview1"><?php echo nl2br(htmlspecialchars($destination['description'])); ?></p>
        </div>

        
        <div class="price-container">
            <img class="price-tag" src="icon/price-tag.png">
            <p class="price"><?php echo formatPrice($destination['price']); ?>
        </div>
    </div>

    <p class="gallery">Gallery</p>

    <div class="image-container" id="galleryGrid">
        <?php for ($i = 0; $i < 6; $i++): ?>
            <img class="batucaves2"
                src="<?php echo $heroImg; ?>"
                alt="<?php echo htmlspecialchars($destination['destination_name']); ?>">
        <?php endfor; ?>

    </div>

    <div style="display:flex; gap:10px; position:relative; left:1500px; top:-400px;">   
        <button style="border:none; background:none; cursor:pointer;" class="next-button" id="galleryPrevBtn" onclick="changeGalleryPage(-1)">
            <img src="icon/previous-button.png" alt="prev">
        </button>

        <button style="border:none; background:none; cursor:pointer;" class="next-button" id="galleryNextBtn" onclick="changeGalleryPage(1)">
            <img src="icon/next.png" alt="next">
        </button>
    </div>
    

    <p class="saying">What people saying about <?php echo htmlspecialchars($destination['destination_name']); ?> </p>

    <?php if (count($reviews) > 0): ?>
        <div class="card-container">
            <?php foreach ($reviews as $review): ?>
                <div class="batu-container">

                    <div>
                        <p class="batu-caves-name"><?php echo htmlspecialchars($destination['destination_name']); ?></p>
                    </div>

                    <div>
                        <?php for ($i = 0; $i < intval($review['rating']); $i++): ?>
                            <img class="star1" src="icon/star.png" alt="star">                        
                        <?php endfor; ?>
                    </div>

                    <div>
                        <img class="profile-picture" src="<?php echo htmlspecialchars($review['profile_picture']); ?>" 
                            alt="profile">
                    </div>

                    <div>
                        <p class="rating-names"><?php echo htmlspecialchars($review['user_name']); ?></p>
                    </div>

                    <div>
                        <p class="rating-date"><?php echo !empty($review['created_at']) ? date('F j, Y', strtotime($review['created_at'])) : ''; ?></p>
                    </div>

                    <div>
                        <p class="rating-description"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>

                    <?php if (!empty($review['image_url'])): ?>
                        <?php foreach (explode(',', $review['image_url']) as $imgPath): ?>
                            <img class="batucaves8" src="<?php echo htmlspecialchars(trim($imgPath)); ?>"
                                alt="destination thumb">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div> 
        
    <?php else: ?>
        <p class="no-reviews" id="no-reviews-msg">No reviews yet for this destination.</p>
    <?php endif; ?>
  
    <div style="display:flex; gap:10px; justify-content:flex-end; padding-right:60px; margin-top:-450px;">
        <button style="border:none; background:none; cursor:pointer; display:none;" id="reviewsPrevBtn" onclick="changeReviewPage(-1)">
            <img src="icon/previous-button.png" alt="prev" style="width:70px;">
        </button>

        <button style="border:none; background:none; cursor:pointer; <?php echo count($reviews) <= 6 ? 'display:none;' : ''; ?>" id="reviewsNextBtn" onclick="changeReviewPage(1)">
            <img src="icon/next.png" alt="next" style="width:70px;">
        </button>
    </div>

    

    <p class="similar-place">Similar Place</p>

    <?php if (count($similar) > 0): ?>
        <div class="similar-card-container" id="similarGrid">
            <?php foreach ($similar as $place): ?>
                <div class="similar-container"
                    onclick="window.location.href='admin-feedback.php?id=<?php echo $place['destination_id']; ?>'">
                    
                    <div>
                        <img class="thean-hou" src="<?php echo imgSrc($place['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($place['destination_name']); ?>">
                    </div>

                    <div>
                        <p class="kuala-lumpur"><?php echo htmlspecialchars($place['state_name']); ?></p>
                    </div>

                    <div>
                        <p class="thean-hou-temple"><?php echo htmlspecialchars($place['destination_name']); ?></p>
                    </div>

                    <div>
                        <p class="hot">Climate: Hot</p>
                    </div>

                    <div>
                        <p class="ratings1"><?php echo number_format($place['average_rating'], 1); ?></p>
                        <img class="star-icon" src="icon/star.png">
                        <p class="number-rating">(<?php echo $place['reviews_count']; ?>)</p>

                    </div>

                    <div class="from-free-row">
                            <span class="from">From</span>
                            <span class="free"><?php echo formatPrice($place['price']); ?></span>
                        </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="margin-left:50px; color:#666; margin-bottom:60px;">No similar places found.</p>
    <?php endif; ?>

    <div>
        <button style="border:none; background:none; cursor:pointer;" class="next-button2" id="similarPrevBtn" onclick="changeSimilarPage(-1)">
            <img src="icon/previous-button.png" alt="prev" >
        </button>
        <button style="border:none; background:none; cursor:pointer;" class="next-button2" id="similarNextBtn" onclick="changeSimilarPage(1)">
            <img src="icon/next.png" alt="next">
        </button>
    </div>
    

<script>
    var galleryImages = <?php echo $galleryJson; ?>;
    var galleryPage   = 0;
    var galleryPerPage = 6;

    function renderGallery() {
        var grid  = document.getElementById('galleryGrid');
        var start = galleryPage * galleryPerPage;
        var slice = galleryImages.slice(start, start + galleryPerPage);

        grid.innerHTML = '';
        slice.forEach(function(src) {
            var img = document.createElement('img');
            img.className = 'batucaves2';
            img.src = src;
            grid.appendChild(img);
        });

        // hide prev on first page
        document.getElementById('galleryPrevBtn').style.visibility =
            galleryPage === 0 ? 'hidden' : 'visible';

        // hide next on last page
        var totalPages = Math.ceil(galleryImages.length / galleryPerPage);
        document.getElementById('galleryNextBtn').style.visibility =
            galleryPage >= totalPages - 1 ? 'hidden' : 'visible';
    }

    (function() {
        var cards = document.querySelectorAll('.card-container .batu-container');
        cards.forEach(function(card, i) {
            if (i >= 6) card.style.display = 'none';
        });
    })();

    function changeGalleryPage(dir) {
        var totalPages = Math.ceil(galleryImages.length / galleryPerPage);
        galleryPage += dir;
        if (galleryPage < 0) galleryPage = 0;
        if (galleryPage >= totalPages) galleryPage = totalPages - 1;
        renderGallery();
    }

    renderGallery();

    var reviewPage = 0;
    var reviewsPerPage = 6;

    function changeReviewPage(dir) {
        var cards = document.querySelectorAll('.card-container .batu-container');
        var totalPages = Math.ceil(cards.length / reviewsPerPage);

        reviewPage += dir;
        if (reviewPage < 0) reviewPage = 0;
        if (reviewPage >= totalPages) reviewPage = totalPages - 1;

        cards.forEach(function(card, i) {
            var start = reviewPage * reviewsPerPage;
            var end = start + reviewsPerPage;
            card.style.display = (i >= start && i < end) ? 'block' : 'none';
        });

        document.getElementById('reviewsPrevBtn').style.display =
            reviewPage === 0 ? 'none' : 'inline-block';

        document.getElementById('reviewsNextBtn').style.display =
            reviewPage >= totalPages - 1 ? 'none' : 'inline-block';
    }

    var reviewsExpanded = false;

    function toggleReviews() {
        var cards = document.querySelectorAll('.card-container .batu-container');
        reviewsExpanded = !reviewsExpanded;
        cards.forEach(function(card, i) {
            if (i >= 6) {
                card.style.display = reviewsExpanded ? 'block' : 'none';
            }
        });
    }
 
    function toggleSimilar() {
        var cards = document.querySelectorAll('#similarGrid .similar-container');
        var btn   = document.getElementById('similarNextBtn');
 
        similarExpanded = !similarExpanded;
 
        cards.forEach(function(card, i) {
            if (i >= 3) {
                card.classList.toggle('hidden-card', !similarExpanded);
            }
        });
 
        btn.classList.toggle('expanded', similarExpanded);
    }

    var similarPlaces  = <?php echo $similarJson; ?>;
    var similarPage    = 0;
    var similarPerPage = 3;

    function renderSimilar() {
        var grid  = document.getElementById('similarGrid');
        var start = similarPage * similarPerPage;
        var slice = similarPlaces.slice(start, start + similarPerPage);

        grid.innerHTML = '';
        slice.forEach(function(place) {
            var firstImg = place.image_url ? place.image_url.split(',')[0].trim() : 'image/default.jpg';
            var price = place.price ? place.price.trim() : '';
            price = price.replace(/^RM\s*/i, '');
            var priceDisplay = (price === '0' || price.toLowerCase() === 'free' || price === '')
                ? 'Free' : 'RM ' + price;

            var div = document.createElement('div');
            div.className = 'similar-container';
            div.style.cursor = 'pointer';
            div.onclick = function() {
                window.location.href = 'admin-feedback.php?id=' + place.destination_id;
            };

            div.innerHTML = `
                <img class="thean-hou" src="${firstImg}" alt="${place.destination_name}">
                <p class="kuala-lumpur">${place.state_name}</p>
                <p class="thean-hou-temple">${place.destination_name}</p>
                <p class="hot">Climate: Hot</p>
                <p class="ratings1">${parseFloat(place.average_rating).toFixed(1)}</p>
                <img class="star-icon" src="icon/star.png">
                <p class="number-rating">(${place.reviews_count})</p>
                <div class="from-free-row">
                    <span class="from">From</span>
                    <span class="free">${priceDisplay}</span>
                </div>
            `;
            grid.appendChild(div);
        });

        document.getElementById('similarPrevBtn').style.visibility =
            similarPage === 0 ? 'hidden' : 'visible';

        var totalPages = Math.ceil(similarPlaces.length / similarPerPage);
        document.getElementById('similarNextBtn').style.visibility =
            similarPage >= totalPages - 1 ? 'hidden' : 'visible';
    }

    function changeSimilarPage(dir) {
        var totalPages = Math.ceil(similarPlaces.length / similarPerPage);
        similarPage += dir;
        if (similarPage < 0) similarPage = 0;
        if (similarPage >= totalPages) similarPage = totalPages - 1;
        renderSimilar();
    }

    renderSimilar();
</script>


</body>

</html>