<?php
session_start();
require 'conn.php';

$q = trim($_GET['q'] ?? '');
$results = [];

if (!empty($q)) {
    // 保存 search history
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare("DELETE FROM search_history WHERE user_id = ? AND keyword = ?");
        $stmt->bind_param("is", $userId, $q);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO search_history (user_id, keyword) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $q);
        $stmt->execute();
    }

    // Search destinations
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("
        SELECT d.destination_id, d.destination_name, d.image_url,
               d.average_rating, d.reviews_count, d.price,
               s.state_name
        FROM destinations d
        LEFT JOIN states s ON s.state_id = d.state_id
        WHERE d.destination_name LIKE ?
           OR d.description LIKE ?
           OR s.state_name LIKE ?
        ORDER BY d.average_rating DESC
        LIMIT 20
    ");
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function formatPrice($price)
{
    $price = trim($price);
    $price = preg_replace('/^RM\s*/i', '', $price);
    return ($price == '0' || strtolower($price) == 'free' || empty($price))
        ? 'Free' : 'RM ' . htmlspecialchars($price);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search: <?= htmlspecialchars($q) ?> - Vibe Vacay</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/filter-search.css">
    <link rel="stylesheet" href="css/search-bar.css">
</head>

<body>

    <?php include('./includes/navbar.php'); ?>

    <!-- Search bar at top -->
    <?php include('./includes/search-bar.php'); ?>

    <div class="search-results-wrapper">
        <div class="search-title">
            Search results for: <span style="color:#4f8ef7;">"<?= htmlspecialchars($q) ?>"</span>
        </div>
        <div class="search-count">
            <?= count($results) ?> destination<?= count($results) !== 1 ? 's' : '' ?> found
        </div>

        <?php if (empty($results)): ?>
            <div class="no-results">
                <h2>No destinations found</h2>
                <p>Try searching with different keywords</p>
            </div>
        <?php else: ?>
            <div class="results-grid">
                <?php foreach ($results as $dest): ?>
                    <?php
                    $img = !empty($dest['image_url'])
                        ? explode(',', $dest['image_url'])[0]
                        : 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400';
                    ?>
                    <div class="result-card"
                        onclick="window.location.href='destination-description.php?id=<?= $dest['destination_id'] ?>'">
                        <div class="heart-icon"
                            data-id="<?= $dest['destination_id'] ?>"
                            onclick="event.stopPropagation(); toggleWishlist(this, <?= $dest['destination_id'] ?>)"
                            title="Wishlist">
                            <svg viewBox="0 0 24 24" width="20" height="20" class="heart-svg">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                                        2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
                                        C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5
                                        c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                        <img src="<?= htmlspecialchars(trim($img)) ?>"
                            alt="<?= htmlspecialchars($dest['destination_name']) ?>"
                            onerror="this.src='https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400'">
                        <div class="card-body">
                            <div class="card-state"><?= htmlspecialchars($dest['state_name']) ?></div>
                            <div class="card-name"><?= htmlspecialchars($dest['destination_name']) ?></div>
                            <div class="card-footer">
                                <div class="card-rating">
                                    ⭐ <?= number_format((float)$dest['average_rating'], 1) ?>
                                    <span style="color:#9ca3af;">(<?= number_format((int)$dest['reviews_count']) ?>)</span>
                                </div>
                                <div class="card-price">From <?= formatPrice($dest['price']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // 页面加载时标红已 liked 的 heart
        document.addEventListener('DOMContentLoaded', () => {
            fetch('ajax-handler.php?action=get_wishlist')
                .then(r => r.json())
                .then(data => {
                    window.wishlistIds = data.ids.map(Number);
                    document.querySelectorAll('.heart-icon[data-id]').forEach(el => {
                        if (window.wishlistIds.includes(parseInt(el.dataset.id))) {
                            el.classList.add('liked');
                        }
                    });
                });
        });

        function toggleWishlist(el, destinationId) {
            const isLiked = el.classList.contains('liked');

            if (isLiked) {
                fetch('remove-wishlist-by-dest.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            destination_id: destinationId
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            el.classList.remove('liked');
                            if (window.wishlistIds) {
                                window.wishlistIds = window.wishlistIds.filter(id => id !== destinationId);
                            }
                        }
                    });
            } else {
                fetch('add-wishlist.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            destination_id: destinationId
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success || data.message === 'Already in wishlist') {
                            el.classList.add('liked');
                            if (window.wishlistIds) window.wishlistIds.push(destinationId);
                        } else {
                            alert(data.message || 'Please login first.');
                        }
                    });
            }
        }
    </script>

</body>

</html>