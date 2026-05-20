<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

// ── GET STATE ──
$state_id = isset($_GET['state_id']) ? (int)$_GET['state_id'] : 1;

$stmt = $conn->prepare("SELECT * FROM states WHERE state_id = ?");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$state = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$state) {
    die("State not found.");
}

// ── GET DESTINATION CATEGORIES for this state ──
// destinations → destination_tag_mapping → destination_tags → tag_type
$cat_sql = "
    SELECT DISTINCT tt.tag_type_id, tt.tag_type_name
    FROM tag_type tt
    JOIN destination_tags dt  ON dt.tag_type_id    = tt.tag_type_id
    JOIN destination_tag_mapping dtm ON dtm.tag_id = dt.tag_id
    JOIN destinations d       ON d.destination_id  = dtm.destination_id
    WHERE d.state_id = ?
    ORDER BY tt.tag_type_name
";
$stmt = $conn->prepare($cat_sql);
$stmt->bind_param("i", $state_id);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── GET DESTINATIONS PER CATEGORY (top 4 by rating) ──
$destinations_by_cat = [];
foreach ($categories as $cat) {
    $d_sql = "
        SELECT d.*, dt.tag_name AS climate_tag
        FROM destinations d
        JOIN destination_tag_mapping dtm ON dtm.destination_id = d.destination_id
        JOIN destination_tags dt         ON dt.tag_id          = dtm.tag_id
        WHERE d.state_id = ? AND dt.tag_type_id = ?
        ORDER BY d.average_rating DESC
    ";
    $stmt = $conn->prepare($d_sql);
    $stmt->bind_param("ii", $state_id, $cat['tag_type_id']);
    $stmt->execute();
    $destinations_by_cat[$cat['tag_type_id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// ── GET REVIEWS FOR THIS STATE ──
$rev_sql = "
    SELECT r.*, u.user_name AS username,
           d.destination_name, d.image_url AS dest_img
    FROM reviews r
    JOIN users u        ON u.user_id        = r.user_id
    JOIN destinations d ON d.destination_id = r.destination_id
    WHERE d.state_id = ?
    ORDER BY r.created_at DESC
    LIMIT 9
";
$stmt = $conn->prepare($rev_sql);
$stmt->bind_param("i", $state_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── GET USER FAVORITES ──
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $fav_sql = "SELECT destination_id FROM favorites WHERE user_id = ?";
    $stmt    = $conn->prepare($fav_sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $fav_result = $stmt->get_result();
    while ($row = $fav_result->fetch_assoc()) {
        $user_favorites[] = $row['destination_id'];
    }
    $stmt->close();
}

// ── HELPERS ──
function formatPrice($price) {
    $p = trim($price);
    $p = preg_replace('/^RM\s*/i', '', $p);
    if (!$p || $p == '0' || strtolower($p) == 'free') return 'Free';
    return 'From RM' . $p;
}

function timeAgo($datetime) {
    $now  = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    if ($diff->days == 0)  return 'Today';
    if ($diff->days == 1)  return 'Yesterday';
    if ($diff->days < 7)   return $diff->days . ' days ago';
    if ($diff->days < 30)  return floor($diff->days / 7)  . ' week'  . (floor($diff->days / 7)  > 1 ? 's' : '') . ' ago';
    if ($diff->days < 365) return floor($diff->days / 30) . ' month' . (floor($diff->days / 30) > 1 ? 's' : '') . ' ago';
    return floor($diff->days / 365) . ' year' . (floor($diff->days / 365) > 1 ? 's' : '') . ' ago';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($state['state_name']) ?> - Destinations</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/state-destination.css">
    <style>
        .heart-icon.favorited { color: #ef4444; }
        .heart-icon { cursor: pointer; user-select: none; transition: color 0.2s, transform 0.15s; }
        .heart-icon:active { transform: scale(1.3); }
        .no-destinations { grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php include('./includes/navbar.php'); ?>

    <!-- ── STATE / HERO ── -->
    <section class="state" style="background-image: linear-gradient(to bottom, rgba(10,20,40,0.35) 0%, rgba(10,20,40,0.68) 100%), url('<?= htmlspecialchars($state['state_url']) ?>') center/cover no-repeat;">
        <div class="state-content">
            <div class="state-title-row">
                <button type="button" class="back_Btn" onclick="history.back()">
                    <img src="icon/error.png" class="back-icon" />
                </button>
                <h1><?= htmlspecialchars($state['state_name']) ?></h1>
            </div>
            <p class="state-desc"><?= htmlspecialchars($state['state_description'] ?? '') ?></p>
            <div class="state-meta">
                <div class="state-tags">
                    <?php foreach ($categories as $cat): ?>
                        <span class="state-tag"><?= htmlspecialchars($cat['tag_type_name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ── SEARCH ── -->
    <div class="search-container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Find places and things to do" oninput="filterCards()">
            <button onclick="filterCards()">Search</button>
        </div>
    </div>

    <!-- ── DESTINATION SECTIONS BY CATEGORY ── -->
    <?php if (empty($categories)): ?>
        <p style="text-align:center; padding:3rem; color:#6b7280;">No destination categories found for this state.</p>
    <?php endif; ?>

    <?php foreach ($categories as $cat): ?>
    <div class="destination-list" id="section-<?= $cat['tag_type_id'] ?>">
        <div class="section-title-row">
            <h1><?= htmlspecialchars($cat['tag_type_name']) ?> In <?= htmlspecialchars($state['state_name']) ?></h1>
            <button type="button" class="view-more-circle"
                onclick="window.location.href='destination-list.php?state_id=<?= $state_id ?>&tag_type_id=<?= $cat['tag_type_id'] ?>'">
                <img src="icon/error.png" class="view-icon" />
            </button>
        </div>
        <div class="destination-cards">
            <?php $dests = $destinations_by_cat[$cat['tag_type_id']] ?? []; ?>
            <?php if (empty($dests)): ?>
                <div class="no-destinations">No destinations found in this category.</div>
            <?php else: ?>
                <?php foreach (array_slice($dests, 0, 4) as $dest):
                    $is_fav = in_array($dest['destination_id'], $user_favorites);
                ?>
                <div class="dest-card searchable-card"
                     data-title="<?= htmlspecialchars(strtolower($dest['destination_name'])) ?>"
                     onclick="window.location.href='destination-description.php?destination_id=<?= $dest['destination_id'] ?>'">
                    <div class="heart-icon <?= $is_fav ? 'favorited' : '' ?>"
                         onclick="toggleFavorite(event, this, <?= $dest['destination_id'] ?>)">
                        <?= $is_fav ? '&#9829;' : '&#9825;' ?>
                    </div>
                    <img class="thumbnail"
                         src="<?= htmlspecialchars($dest['image_url']) ?>"
                         alt="<?= htmlspecialchars($dest['destination_name']) ?>"
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="card-info">
                        <span class="location"><?= htmlspecialchars($state['state_name']) ?></span>
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
    </div>
    <?php endforeach; ?>

    <!-- ── REVIEWS SECTION ── -->
    <div class="reviews-section">
        <h1>What people are saying about <?= htmlspecialchars($state['state_name']) ?></h1>
        <?php if (empty($reviews)): ?>
            <p style="text-align:center; color:var(--text-muted); padding:2rem 0;">No reviews yet for this state.</p>
        <?php else: ?>
        <div class="review-cards" id="reviewGrid">
            <?php foreach ($reviews as $i => $review): ?>
            <div class="review-card <?= $i >= 3 ? 'hidden' : '' ?>">
                <div class="review-place">
                    <img class="review-place-img"
                         src="<?= htmlspecialchars($review['dest_img']) ?>"
                         alt="<?= htmlspecialchars($review['destination_name']) ?>"
                         onerror="this.src='images/placeholder.jpg'">
                    <div>
                        <div class="review-place-name"><?= htmlspecialchars($review['destination_name']) ?></div>
                        <div class="review-place-rating">
                            <span class="stars">
                                <?= str_repeat('★', round($review['rating'])) . str_repeat('☆', 5 - round($review['rating'])) ?>
                            </span>
                            <span>
                                <?= number_format($review['rating'], 1) ?> &middot;
                                <?php $labels = [5=>'Excellent',4=>'Very Good',3=>'Good',2=>'Fair',1=>'Poor'];
                                      echo $labels[round($review['rating'])] ?? ''; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="review-user">
                    <?php $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($review['username']) . '&background=1A2B49&color=fff&size=60'; ?>
                    <img class="review-avatar" src="<?= $avatar ?>" alt="<?= htmlspecialchars($review['username']) ?>">
                    <div>
                        <div class="review-username"><?= htmlspecialchars($review['username']) ?></div>
                        <div class="review-date">Reviewed <?= timeAgo($review['create_at']) ?></div>
                    </div>
                </div>
                <p class="review-comment"><?= htmlspecialchars($review['comment']) ?></p>
                <?php if (!empty($review['image_url'])): ?>
                <div class="review-photos">
                    <?php foreach (array_slice(explode(',', $review['image_url']), 0, 3) as $photo):
                        $photo = trim($photo); if (!$photo) continue; ?>
                        <img class="review-photo" src="<?= htmlspecialchars($photo) ?>" alt="" onerror="this.style.display='none'">
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
            const isFav  = el.classList.contains('favorited');
            const action = isFav ? 'remove' : 'add';
            fetch('favorite-toggle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'destination_id=' + destinationId + '&action=' + action
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (action === 'add') {
                        el.classList.add('favorited');
                        el.innerHTML = '&#9829;';
                    } else {
                        el.classList.remove('favorited');
                        el.innerHTML = '&#9825;';
                    }
                }
            })
            .catch(err => console.error('Favourite error:', err));
        }
    </script>
</body>
</html>