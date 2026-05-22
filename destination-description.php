<?php
session_start();
require 'conn.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }


if (!isset($_GET['id'])) {
    $first = $conn->query("SELECT destination_id FROM destinations ORDER BY destination_id ASC LIMIT 1");
    $row   = $first->fetch_assoc();
    if ($row) {
        header("Location: destination-description.php?id=" . $row['destination_id']);
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
    header("Location: destination-description.php");
    exit();
}

// ── Fetch tags for this destination ─────────────────────────
$stmt = $conn->prepare("
    SELECT t.tag_name, tt.tag_type_name
    FROM destination_tag_mapping dtm
    JOIN destination_tags t ON t.tag_id = dtm.tag_id
    JOIN tag_type tt ON tt.tag_type_id = t.tag_type_id
    WHERE dtm.destination_id = ?
    ORDER BY tt.tag_type_id, t.tag_id
");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$tags_result = $stmt->get_result();
$tags = [];
while ($tag = $tags_result->fetch_assoc()) {
    $tags[$tag['tag_type_name']][] = $tag['tag_name'];
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
        s.state_name,
        (
            SELECT t.tag_name 
            FROM destination_tag_mapping dtm
            JOIN destination_tags t ON t.tag_id = dtm.tag_id
            WHERE dtm.destination_id = d.destination_id
              AND t.tag_type_id = 1
            LIMIT 1
        ) AS climate_tag
    FROM destinations d
    LEFT JOIN states s ON s.state_id = d.state_id
    WHERE d.state_id = (SELECT state_id FROM destinations WHERE destination_id = ?)
      AND d.destination_id != ?
    LIMIT 20
");
$stmt->bind_param("ii", $destination_id, $destination_id);
$stmt->execute();
$similar_result = $stmt->get_result();
$similar = [];
while ($row = $similar_result->fetch_assoc()) {
    $similar[] = $row;
}
$stmt->close();

function imgSrc($url, $fallback = 'image/default.jpg')
{
    return !empty($url) ? htmlspecialchars($url) : $fallback;
}

function formatPrice($price)
{
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
    <title>Destination Description</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/destination-description.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
    </style>

</head>



<body>

    <?php include('./includes/navbar.php'); ?>

    <div class="hero-title-row">
        <button type="button" class="back_Btn" onclick="window.history.back()">
            <img src="icon/error.png" class="back-icon" />
        </button>
    </div>
    



    <img class="batucaves1" src="<?php echo $heroImg; ?>" alt="<?php echo htmlspecialchars($destination['destination_name']); ?>">

    <p class="batucaves-name"><?php echo htmlspecialchars($destination['destination_name']); ?></p>

    <p class="malaysia"><?php echo htmlspecialchars($destination['state_name'] ?? ''); ?>, Malaysia</p>

    <?php if (!empty($tags)): ?>
        <div class="tags-overlay-row">
            <?php foreach ($tags as $type => $tagList): ?>
                <?php foreach ($tagList as $tag): ?>
                    <span class="tag-overlay-pill"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
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

        <div class="add-container1">
            <div>
                <img class="heart-shape" src="icon/heart.png">
            </div>

            <div>
                <button class="add" onclick="addToWishlist()">Add to Wishlist</button>
            </div>
            <p id="wishlist-msg" style="font-size:14px; margin-top:10px;"></p>
        </div>

        
        <div class="price-container">
            <img class="price-tag" src="icon/price-tag.png">
            <p class="price"><?php echo formatPrice($destination['price']); ?>
        </div>
    </div>


    <p class="gallery">Gallery</p>

    <div class="image-container" id="galleryGrid"></div>

    <button style="border:none; background:none; cursor:pointer; visibility:hidden; position:absolute; left:15px; top:1050px; " 
        id="galleryPrevBtn" onclick="changeGalleryPage(-1)">
        <img src="icon/previous-button.png" alt="prev" style="width:60px;">
    </button>

    <button style="border:none; background:transparent; cursor:pointer; position:absolute; right:15px; top:1050px;" 
        id="galleryNextBtn" onclick="changeGalleryPage(1)">
        <img src="icon/next.png" alt="next" style="width:60px;">
    </button>

    <p class="saying">What people saying about <?php echo htmlspecialchars($destination['destination_name']); ?></p>

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

   <div style="position:relative; width:100%; height:70px; margin-top:-170px;">
        <button style="border:none; background:none; cursor:pointer; display:none; position:absolute; left:15px; top:0;" 
            id="reviewsPrevBtn" onclick="changeReviewPage(-1)">
            <img src="icon/previous-button.png" alt="prev" style="width:60px;">
        </button>

        <button style="border:none; background:none; cursor:pointer; <?php echo count($reviews) <= 6 ? 'display:none;' : ''; ?> position:absolute; right:15px; top:0;" 
            id="reviewsNextBtn" onclick="changeReviewPage(1)">
            <img src="icon/next.png" alt="next" style="width:60px;">
        </button>
    </div>



    <p class="experience">Write Your Experience</p>

    <div class="rating-experience-container">
        <div>
            <p class="rating-name1">Rating:</p>
        </div>

        <div id="star-rating-row">
            <?php for ($s = 1; $s <= 5; $s++): ?>
                <button class="star-container" onclick="setRating(<?php echo $s; ?>)" type="button">
                    <img class="star2" id="star-<?php echo $s; ?>" src="icon/star1.png">
                </button>
            <?php endfor; ?>
        </div>

        <input type="hidden" id="selected-rating" value="0">

        <div>
            <p class="description1">Description:</p>
            <input id="review-comment" type="text" name="search" placeholder="Leave a comment..." />
        </div>

        <div>
            <p class="photo">Photo:</p>
            <div class="upload-area">
                <label class="upload-box">
                    <input type="file" id="review-images" accept="image/*" multiple hidden>
                    <img src="icon/upload.png" class="upload-icon">
                    <span class="upload-image">Upload image</span>
                </label>
                <div class="photo-preview-row" id="photoPreviewRow"></div>
            </div>

            <div class="submit-container">
                <button class="submit-button" onclick="submitReview()">Submit</button>
            </div>

            <p id="review-msg" style="padding-left:40px; color:green; font-size:16px;"></p>
        </div>

        <p class="similar-place">Similar Place</p>

        <?php if (count($similar) > 0): ?>
            <div class="similar-card-container" id="similarGrid">
                <?php foreach ($similar as $place): ?>
                    <div class="similar-container"
                        onclick="window.location.href='destination-description.php?id=<?php echo $place['destination_id']; ?>'">

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
                            <p class="hot">Climate: <?php echo htmlspecialchars($place['climate_tag'] ?? 'N/A'); ?></p>
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

        <div style="position:relative; width:100%; height:70px; margin-top:-200px;">
            <button style="border:none; background:none; cursor:pointer; position:absolute; left:-60px; top:0; visibility:hidden;" 
                class="next-button2" id="similarPrevBtn" onclick="changeSimilarPage(-1)">
                <img src="icon/previous-button.png" alt="prev" style="width:60px;">
            </button>

            <button style="border:none; background:none; cursor:pointer; position:absolute; right:-420px; top:0;" 
                class="next-button2" id="similarNextBtn" onclick="changeSimilarPage(1)">
                <img src="icon/next.png" alt="next" style="width:60px;">
            </button>
        </div>

        <script>
            var galleryImages = <?php echo $galleryJson; ?>;
            var galleryPage = 0;
            var galleryPerPage = 4;

            function renderGallery() {
                var grid = document.getElementById('galleryGrid');
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
                    if (i >= 4) card.style.display = 'none';
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

            // ── Reviews toggle ────────────────────────────────────
            var reviewPage = 0;
            var reviewsPerPage = 4;

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
                    if (i >= 4) {
                        card.style.display = reviewsExpanded ? 'block' : 'none';
                    }
                });
            }


            function toggleSimilar() {
                var cards = document.querySelectorAll('#similarGrid .similar-container');
                var btn = document.getElementById('similarNextBtn');

                similarExpanded = !similarExpanded;

                cards.forEach(function(card, i) {
                    if (i >= 4) {
                        card.classList.toggle('hidden-card', !similarExpanded);
                    }
                });

                btn.classList.toggle('expanded', similarExpanded);
            }

            var similarPlaces = <?php echo $similarJson; ?>;
            var similarPage = 0;
            var similarPerPage = 4;

            function renderSimilar() {
                var grid = document.getElementById('similarGrid');
                var start = similarPage * similarPerPage;
                var slice = similarPlaces.slice(start, start + similarPerPage);

                grid.innerHTML = '';
                slice.forEach(function(place) {
                    var firstImg = place.image_url ? place.image_url.split(',')[0].trim() : 'image/default.jpg';
                    var price = place.price ? place.price.trim() : '';
                    price = price.replace(/^RM\s*/i, '');
                    var priceDisplay = (price === '0' || price.toLowerCase() === 'free' || price === '') ?
                        'Free' : 'RM ' + price;

                    var div = document.createElement('div');
                    div.className = 'similar-container';
                    div.onclick = function() {
                        window.location.href = 'destination-description.php?id=' + place.destination_id;
                    };

                    div.innerHTML = `
                <img class="thean-hou" src="${firstImg}" alt="${place.destination_name}">
                <p class="kuala-lumpur">${place.state_name}</p>
                <p class="thean-hou-temple">${place.destination_name}</p>
                <p class="hot">Climate: ${place.climate_tag || 'N/A'}</p>
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

            console.log(similarPlaces);



            function setRating(value) {
                document.getElementById('selected-rating').value = value;
                for (var i = 1; i <= 5; i++) {
                    document.getElementById('star-' + i).src =
                        i <= value ? 'icon/star.png' : 'icon/star1.png';
                }
            }

            // ── Multiple Photo Preview ────────────────────────────
            var selectedFiles = [];

            document.getElementById('review-images').addEventListener('change', function() {
                Array.from(this.files).forEach(function(file) {
                    selectedFiles.push(file);
                });
                renderPhotoPreview();
                this.value = ''; // reset so same file can be picked again
            });

            function renderPhotoPreview() {
                var row = document.getElementById('photoPreviewRow');
                row.innerHTML = '';
                selectedFiles.forEach(function(file, index) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var item = document.createElement('div');
                        item.className = 'photo-preview-item';
                        item.innerHTML = `
                    <img src="${e.target.result}" alt="preview">
                    <button class="remove-photo" onclick="removePhoto(${index})">×</button>
                `;
                        row.appendChild(item);
                    };
                    reader.readAsDataURL(file);
                });
            }

            function removePhoto(index) {
                selectedFiles.splice(index, 1);
                renderPhotoPreview();
            }

            // ── Submit Review ─────────────────────────────────────
            function submitReview() {
                var rating = document.getElementById('selected-rating').value;
                var comment = document.getElementById('review-comment').value.trim();
                var msg = document.getElementById('review-msg');

                if (rating == 0) {
                    msg.style.color = 'red';
                    msg.textContent = 'Please select a rating.';
                    return;
                }
                if (!comment) {
                    msg.style.color = 'red';
                    msg.textContent = 'Please write a comment.';
                    return;
                }

                var formData = new FormData();
                formData.append('destination_id', <?php echo $destination_id; ?>);
                formData.append('rating', rating);
                formData.append('comment', comment);
                selectedFiles.forEach(function(file) {
                    formData.append('images[]', file);
                });

                fetch('submit-review.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            msg.style.color = 'green';
                            msg.textContent = 'Review submitted successfully!';

                            // Reset form
                            setRating(0);
                            document.getElementById('review-comment').value = '';
                            selectedFiles = [];
                            renderPhotoPreview();

                            setTimeout(function() {
                                location.reload();
                            }, 1500);

                            // Add new review card to the top of the card container
                            var container = document.querySelector('.card-container');
                            if (!container) {
                                container = document.createElement('div');
                                container.className = 'card-container';
                                document.querySelector('.saying').after(container);
                            }

                            var noReviewMsg = document.getElementById('no-reviews-msg');
                            if (noReviewMsg) noReviewMsg.style.display = 'none';

                            var r = data.review;
                            var stars = '';
                            for (var i = 0; i < r.rating; i++) {
                                stars += '<img class="star1" src="icon/star.png" alt="star">';
                            }

                            var card = document.createElement('div');
                            card.className = 'batu-container';
                            var imgHtml = '';
                            if (r.image_url) {
                                var firstImg = r.image_url.split(',')[0].trim();
                                imgHtml = `<img class="batucaves8" src="${firstImg}" alt="thumb">`;
                            }
                            card.innerHTML = `
                    <p class="batu-caves-name"><?php echo htmlspecialchars($destination['destination_name']); ?></p>
                    <div>${stars}</div>
                    <img class="profile-picture" src="${r.profile_picture}" alt="profile">
                    <p class="rating-names">${r.user_name}</p>
                    <p class="rating-date">Just now</p>
                    <p class="rating-description">${r.comment}</p>
                    ${imgHtml}
                `;

                            container.insertBefore(card, container.firstChild);
                        } else {
                            msg.style.color = 'red';
                            msg.textContent = data.message || 'Something went wrong.';
                        }
                    })
                    .catch(function(err) {
                        console.error(err);
                        msg.style.color = 'red';
                        msg.textContent = 'Network error. Please try again.';
                    });
            }

            function addToWishlist() {
                var msg = document.getElementById('wishlist-msg');
                var formData = new FormData();
                formData.append('destination_id', <?php echo $destination_id; ?>);

                fetch('add-wishlist.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            msg.style.color = 'green';
                            msg.textContent = '✓ Added to wishlist!';
                        } else {
                            msg.style.color = 'red';
                            msg.textContent = data.message;
                        }
                    })
                    .catch(function(err) {
                        msg.style.color = 'red';
                        msg.textContent = 'Network error. Please try again.';
                    });
            }
        </script>
</body>

</html>