<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

// ── GET STATE ─────────────────────────────────────────────────────────────────
$state_id = isset($_GET['state_id']) ? (int) $_GET['state_id'] : 1;

$stmt = $conn->prepare("SELECT * FROM states WHERE state_id = ?");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$state = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$state) die("State not found.");

// ── GET HERO TAGS ─────────────────────────────────────────────────────────────
$hero_tag_sql = "
    SELECT dt.tag_name, dt.tag_type_id, COUNT(dtm.tag_id) AS tag_count
    FROM destination_tags dt
    JOIN destination_tag_mapping dtm ON dtm.tag_id = dt.tag_id
    JOIN destinations d ON d.destination_id = dtm.destination_id
    WHERE d.state_id = ? AND dt.tag_type_id NOT IN (5)
    GROUP BY dt.tag_id, dt.tag_name, dt.tag_type_id
    ORDER BY tag_count DESC LIMIT 6
";
$stmt = $conn->prepare($hero_tag_sql);
$stmt->bind_param("i", $state_id);
$stmt->execute();
$heroTags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($heroTags)) {
    $heroTags = [
        ['tag_name' => 'Explore', 'tag_type_id' => 4],
        ['tag_name' => 'Discover', 'tag_type_id' => 4],
        ['tag_name' => 'Travel', 'tag_type_id' => 4],
    ];
}

// ── TOP SIGHTS ────────────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT d.*,
           (SELECT dt2.tag_name FROM destination_tags dt2
            JOIN destination_tag_mapping dtm2 ON dtm2.tag_id = dt2.tag_id
            WHERE dtm2.destination_id = d.destination_id AND dt2.tag_type_id = 1 LIMIT 1) AS climate_tag
    FROM destinations d WHERE d.state_id = ? ORDER BY d.average_rating DESC
");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$top_sights = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── OUTDOOR ATTRACTION ────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT d.*,
           (SELECT dt2.tag_name FROM destination_tags dt2
            JOIN destination_tag_mapping dtm2 ON dtm2.tag_id = dt2.tag_id
            WHERE dtm2.destination_id = d.destination_id AND dt2.tag_type_id = 1 LIMIT 1) AS climate_tag
    FROM destinations d
    JOIN destination_tag_mapping dtm ON dtm.destination_id = d.destination_id
    JOIN destination_tags dt ON dt.tag_id = dtm.tag_id
    WHERE d.state_id = ? AND dt.tag_type_id = 4 AND dt.tag_name = 'Natural'
    ORDER BY d.average_rating DESC
");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$outdoor_attractions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── HISTORICAL PLACES ─────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT d.*,
           (SELECT dt2.tag_name FROM destination_tags dt2
            JOIN destination_tag_mapping dtm2 ON dtm2.tag_id = dt2.tag_id
            WHERE dtm2.destination_id = d.destination_id AND dt2.tag_type_id = 1 LIMIT 1) AS climate_tag
    FROM destinations d
    JOIN destination_tag_mapping dtm ON dtm.destination_id = d.destination_id
    JOIN destination_tags dt ON dt.tag_id = dtm.tag_id
    WHERE d.state_id = ? AND dt.tag_type_id = 4 AND dt.tag_name = 'Historical'
    ORDER BY d.average_rating DESC
");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$historical_places = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── REVIEWS ───────────────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT r.review_id, r.rating, r.comment, r.image_url, r.created_at,
           u.user_name AS username,
           d.destination_name, d.image_url AS dest_img
    FROM reviews r
    JOIN users u ON u.user_id = r.user_id
    JOIN destinations d ON d.destination_id = r.destination_id
    WHERE d.state_id = ? ORDER BY r.created_at DESC LIMIT 9
");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── USER FAVORITES ────────────────────────────────────────────────────────────
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT destination_id FROM favorites WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $fav_result = $stmt->get_result();
    while ($row = $fav_result->fetch_assoc()) {
        $user_favorites[] = $row['destination_id'];
    }
    $stmt->close();
}

// ── HELPERS ───────────────────────────────────────────────────────────────────
function formatPrice($price) {
    $p = trim((string) $price);
    $p = preg_replace('/^RM\s*/i', '', $p);
    if (!$p || $p == '0' || strtolower($p) == 'free') return 'Free';
    return 'From RM' . $p;
}

function timeAgo($datetime) {
    if (empty($datetime)) return 'Unknown';
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    if ($diff->days == 0) return 'Today';
    if ($diff->days == 1) return 'Yesterday';
    if ($diff->days < 7) return $diff->days . ' days ago';
    if ($diff->days < 30) return floor($diff->days / 7) . ' week(s) ago';
    if ($diff->days < 365) return floor($diff->days / 30) . ' month(s) ago';
    return floor($diff->days / 365) . ' year(s) ago';
}

// ── RENDER FUNCTION (defined once, here at the top) ───────────────────────────
function renderDestinationSection($title, $destinations, $state_name, $state_id, $user_favorites) {
    $section_id = 'section-' . preg_replace('/\s+/', '-', strtolower($title));
    ?>
    <div class="destination-list">
        <div class="section-title-row">
            <h1><?= htmlspecialchars($title) ?> In <?= htmlspecialchars($state_name) ?></h1>
        </div>

        <div class="cards-wrapper">
            <button type="button" class="back_Btn slider-arrow arrow-left"
                onclick="slideCards('<?= $section_id ?>', -1)">
                <img src="icon/error.png" class="back-icon" />
            </button>

            <div class="destination-cards" id="<?= $section_id ?>">
                <?php if (empty($destinations)): ?>
                    <div class="no-destinations">No destinations found in this category.</div>
                <?php else: ?>
                    <?php foreach ($destinations as $i => $dest):
                        $is_fav = in_array($dest['destination_id'], $user_favorites); ?>
                        <div class="dest-card searchable-card <?= $i >= 4 ? 'card-hidden' : '' ?>"
                            data-title="<?= htmlspecialchars(strtolower($dest['destination_name'])) ?>"
                            onclick="window.location.href='destination-description.php?destination_id=<?= $dest['destination_id'] ?>'">

                            <div class="heart-icon <?= $is_fav ? 'favorited' : '' ?>"
                                onclick="toggleFavorite(event, this, <?= $dest['destination_id'] ?>)">
                                <?= $is_fav ? '&#9829;' : '&#9825;' ?>
                            </div>

                            <img class="thumbnail" src="<?= htmlspecialchars($dest['image_url']) ?>"
                                alt="<?= htmlspecialchars($dest['destination_name']) ?>"
                                onerror="this.src='images/placeholder.jpg'">

                            <div class="card-info">
                                <span class="location"><?= htmlspecialchars($state_name) ?></span>
                                <h3 class="dest-title"><?= htmlspecialchars($dest['destination_name']) ?></h3>
                                <span class="climate">Climate: <?= htmlspecialchars($dest['climate_tag'] ?? 'N/A') ?></span>
                                <div class="card-footer">
                                    <div class="rating">
                                        <span class="score"><?= number_format($dest['average_rating'], 1) ?></span>
                                        <span class="star">&#9733;</span>
                                        <span class="reviews">(<?= number_format($dest['reviews_count']) ?>)</span>
                                    </div>
                                    <div class="price"><?= formatPrice($dest['price']) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="back_Btn slider-arrow arrow-right"
                onclick="slideCards('<?= $section_id ?>', 1)"
                <?= count($destinations) <= 4 ? 'style="display:none;"' : '' ?>>
                <img src="icon/error.png" class="back-icon arrow-right-icon" />
            </button>
        </div>
    </div>
    <?php
}

$tagTypeEmojis = [1 => '🌤️', 2 => '💰', 3 => '👥', 4 => '🏖️'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($state['state_name']) ?> - Destinations</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/recommendation-page.css">
    <link rel="stylesheet" href="css/state-destination.css">
</head>
<body>
    <?php include('./includes/navbar.php'); ?>

    <section class="hero" id="hero-section" style="
        background-image: linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.60)),
                          url('<?= htmlspecialchars(trim($state['state_url'])) ?>');
        background-size: cover; background-position: center;">

        <div class="hero-content">
            <div class="hero-title-row">
                <button type="button" class="back_Btn" onclick="window.location.href='recommendation-page.php'">
                    <img src="icon/error.png" class="back-icon" />
                </button>
                <h1><?= htmlspecialchars($state['state_name']) ?></h1>
            </div>
            <p><?= htmlspecialchars($state['state_description'] ?? 'Discover the beauty and culture of ' . $state['state_name'] . '.
                Explore top-rated destinations, local experiences, and hidden gems waiting to be found.') ?></p>
        </div>

        <div class="tags">
            <span class="tag-label">Tag:</span>
            <?php foreach ($heroTags as $tag):
                $emoji = $tagTypeEmojis[$tag['tag_type_id']] ?? '📍'; ?>
                <span class="tag"><?= $emoji ?> <?= htmlspecialchars($tag['tag_name']) ?></span>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="search-container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Find places and things to do" oninput="filterCards()">
            <button onclick="filterCards()">Search</button>
        </div>
    </div>

    <?php renderDestinationSection('Top Sights', $top_sights, $state['state_name'], $state_id, $user_favorites); ?>
    <?php renderDestinationSection('Outdoor Attraction', $outdoor_attractions, $state['state_name'], $state_id, $user_favorites); ?>
    <?php renderDestinationSection('Historical Places', $historical_places, $state['state_name'], $state_id, $user_favorites); ?>

    <div class="reviews-section">
        <h1>What people are saying about <?= htmlspecialchars($state['state_name']) ?></h1>

        <?php if (empty($reviews)): ?>
            <p style="text-align:center; color:var(--text-muted,#9ca3af); padding:2rem 0;">No reviews yet for this state.</p>
        <?php else: ?>
            <div class="review-cards" id="reviewGrid">
                <?php foreach ($reviews as $i => $review): ?>
                    <div class="review-card <?= $i >= 3 ? 'hidden' : '' ?>">
                        <div class="review-place">
                            <img class="review-place-img" src="<?= htmlspecialchars($review['dest_img']) ?>"
                                alt="<?= htmlspecialchars($review['destination_name']) ?>"
                                onerror="this.src='images/placeholder.jpg'">
                            <div>
                                <div class="review-place-name"><?= htmlspecialchars($review['destination_name']) ?></div>
                                <div class="review-place-rating">
                                    <span class="stars"><?= str_repeat('★', round($review['rating'])) . str_repeat('☆', 5 - round($review['rating'])) ?></span>
                                    <span><?= number_format($review['rating'], 1) ?> &middot;
                                        <?php $labels = [5=>'Excellent',4=>'Very Good',3=>'Good',2=>'Fair',1=>'Poor'];
                                        echo $labels[round($review['rating'])] ?? ''; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="review-user">
                            <img class="review-avatar"
                                src="https://ui-avatars.com/api/?name=<?= urlencode($review['username']) ?>&background=1A2B49&color=fff&size=60"
                                alt="<?= htmlspecialchars($review['username']) ?>">
                            <div>
                                <div class="review-username"><?= htmlspecialchars($review['username']) ?></div>
                                <div class="review-date">Reviewed <?= timeAgo($review['created_at']) ?></div>
                            </div>
                        </div>

                        <p class="review-comment"><?= htmlspecialchars($review['comment']) ?></p>

                        <?php if (!empty($review['image_url'])): ?>
                            <div class="review-photos">
                                <?php foreach (array_slice(explode(',', $review['image_url']), 0, 3) as $photo):
                                    $photo = trim($photo);
                                    if (!$photo) continue; ?>
                                    <img class="review-photo" src="<?= htmlspecialchars($photo) ?>" alt=""
                                        onerror="this.style.display='none'">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($reviews) > 3): ?>
                <div class="view-more-wrap">
                    <button class="view-more-btn" id="viewMoreBtn" onclick="showMoreReviews()">View More</button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function showMoreReviews() {
            document.querySelectorAll('.review-card.hidden').forEach(c => c.classList.remove('hidden'));
            document.getElementById('viewMoreBtn').style.display = 'none';
        }

        function filterCards() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            document.querySelectorAll('.searchable-card').forEach(card => {
                const title = card.getAttribute('data-title') || '';
                card.style.display = (!query || title.includes(query)) ? '' : 'none';
            });
        }

        function toggleFavorite(event, el, destinationId) {
            event.stopPropagation();
            <?php if (!isset($_SESSION['user_id'])): ?>
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            <?php endif; ?>
            const isFav = el.classList.contains('favorited');
            const action = isFav ? 'remove' : 'add';
            fetch('favorite-toggle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'destination_id=' + destinationId + '&action=' + action
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (action === 'add') { el.classList.add('favorited'); el.innerHTML = '&#9829;'; }
                    else { el.classList.remove('favorited'); el.innerHTML = '&#9825;'; }
                }
            })
            .catch(err => console.error('Favourite error:', err));
        }

        const sliderState = {};

        function slideCards(sectionId, direction) {
            const grid = document.getElementById(sectionId);
            const cards = Array.from(grid.querySelectorAll('.dest-card'));
            const total = cards.length;
            const perPage = 4;
            if (!sliderState[sectionId]) sliderState[sectionId] = 0;
            let current = sliderState[sectionId];
            let next = current + (direction * perPage);
            if (next < 0) next = 0;
            if (next >= total) next = current;
            sliderState[sectionId] = next;
            cards.forEach((card, i) => {
                card.classList.toggle('card-hidden', !(i >= next && i < next + perPage));
            });
            const wrapper = grid.closest('.cards-wrapper');
            const leftArrow = wrapper.querySelector('.arrow-left');
            const rightArrow = wrapper.querySelector('.arrow-right');
            if (leftArrow) leftArrow.style.display = next > 0 ? 'flex' : 'none';
            if (rightArrow) rightArrow.style.display = (next + perPage) < total ? 'flex' : 'none';
        }
    </script>
</body>
</html>