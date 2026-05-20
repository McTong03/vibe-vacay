<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── DB Connection ────────────────────────────────────────────────────────────
$host    = 'localhost';
$db      = 'vibe-vacay';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ─── All States ───────────────────────────────────────────────────────────────
$allStates   = $pdo->query("SELECT * FROM states ORDER BY state_id")->fetchAll();
$totalStates = count($allStates);

// ─── Hero Banner — cycle through states with arrows ──────────────────────────
if (!isset($_SESSION['hero_index'])) {
    $_SESSION['hero_index'] = 0;
}
if (isset($_GET['nav'])) {
    if ($_GET['nav'] === 'next') {
        $_SESSION['hero_index'] = ($_SESSION['hero_index'] + 1) % $totalStates;
    } elseif ($_GET['nav'] === 'prev') {
        $_SESSION['hero_index'] = ($_SESSION['hero_index'] - 1 + $totalStates) % $totalStates;
    }
    $keepParams = [];
    if (isset($_GET['dest_page']))    $keepParams['dest_page']    = (int)$_GET['dest_page'];
    if (isset($_GET['reviews_page'])) $keepParams['reviews_page'] = (int)$_GET['reviews_page'];
    $qs = !empty($keepParams) ? '?' . http_build_query($keepParams) : '';
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . $qs);
    exit;
}
$heroState = $allStates[$_SESSION['hero_index']];

// Tags for hero state
$heroTags = [];
try {
    $tagStmt = $pdo->prepare("
        SELECT DISTINCT tt.tag_type_name
        FROM tag_type tt
        JOIN destination_tags dt ON dt.tag_type_id = tt.tag_type_id
        JOIN destinations d      ON d.destination_id = dt.destination_id
        WHERE d.state_id = ?
        LIMIT 3
    ");
    $tagStmt->execute([$heroState['state_id']]);
    $heroTags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $heroTags = [];
}
if (empty($heroTags)) $heroTags = ['Urban', 'Vibrant', 'Lifestyle'];

// Hero rating
$ratingStmt = $pdo->prepare("SELECT ROUND(AVG(average_rating),1) FROM destinations WHERE state_id = ?");
$ratingStmt->execute([$heroState['state_id']]);
$heroRating = $ratingStmt->fetchColumn() ?: '5.0';

// ─── High-Rated Destinations (ALL states, NOT filtered by hero) ───────────────
$destPage    = max(0, (int)($_GET['dest_page'] ?? 0));
$destPerPage = 4;
$destOffset  = $destPage * $destPerPage;

$totalDests  = (int)$pdo->query("SELECT COUNT(*) FROM destinations WHERE average_rating IS NOT NULL")->fetchColumn();
$maxDestPage = max(0, (int)ceil($totalDests / $destPerPage) - 1);

$topDestinations = $pdo->query("
    SELECT d.*, s.state_name
    FROM destinations d
    JOIN states s ON s.state_id = d.state_id
    WHERE d.average_rating IS NOT NULL
    ORDER BY d.average_rating DESC, d.reviews_count DESC
    LIMIT $destPerPage OFFSET $destOffset
")->fetchAll();

// ─── Reviews ─────────────────────────────────────────────────────────────────
$reviewsPage    = max(0, (int)($_GET['reviews_page'] ?? 0));
$reviewsPerPage = 6;
$reviewsOffset  = $reviewsPage * $reviewsPerPage;

$totalReviews   = (int)$pdo->query("SELECT COUNT(*) FROM reviews WHERE rating >= 3")->fetchColumn();
$maxReviewsPage = max(0, (int)ceil($totalReviews / $reviewsPerPage) - 1);

$reviews = [];
try {
    $reviews = $pdo->query("
        SELECT r.review_id, r.rating, r.comment, r.image_url, r.created_at,
           u.user_name AS username, u.profile_image,
           d.destination_name
        FROM reviews r
        LEFT JOIN users u        ON u.user_id        = r.user_id
        JOIN destinations d ON d.destination_id = r.destination_id
        WHERE r.rating >= 3
        ORDER BY r.review_id ASC  
        LIMIT $reviewsPerPage OFFSET $reviewsOffset
    ")->fetchAll();
} catch (Exception $e) {
    $reviews = [];
}
if (empty($reviews)) {
    try {
        $reviews = $pdo->query("
            SELECT r.review_id, r.rating, r.comment, r.image_url, r.created_at,
                   NULL AS username, NULL AS profile_image,
                   d.destination_name
            FROM reviews r
            JOIN destinations d ON d.destination_id = r.destination_id
            WHERE r.rating >= 3
            ORDER BY r.review_id ASC
            LIMIT $reviewsPerPage OFFSET $reviewsOffset
        ")->fetchAll();
    } catch (Exception $e) {
        $reviews = [];
    }
}
if (!is_array($reviews)) $reviews = [];

// ─── Helpers ──────────────────────────────────────────────────────────────────
function avatarColor(string $name): string
{
    $colors = ['#ff7f50', '#ff6b6b', '#4ecdc4', '#45b7d1', '#96c93d', '#f7b731', '#a29bfe'];
    return $colors[ord($name[0] ?? 'A') % count($colors)];
}

function renderReviewCard(array $rev): string
{
    $name    = !empty($rev['username']) ? $rev['username'] : 'Traveller';
    $initial = strtoupper($name[0] ?? 'T');
    $color   = avatarColor($name);
    $date    = !empty($rev['created_at']) ? date('F j, Y', strtotime($rev['created_at'])) : '';
    $rating  = max(1, min(5, (int)($rev['rating'] ?? 5)));

    $rawComment  = $rev['comment'] ?? '';
    $safeComment = htmlspecialchars($rawComment);
    $destName    = htmlspecialchars($rev['destination_name'] ?? 'Tour');
    $imgUrl      = $rev['image_url'] ?? '';

    $isLong = mb_strlen($rawComment) > 200;
    $short  = $isLong ? htmlspecialchars(mb_substr($rawComment, 0, 200)) . '...' : $safeComment;

    $starsHtml = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

    $uid = 'r' . ($rev['review_id'] ?? rand(1000, 9999));

    if ($isLong) {
        $commentBlock = '<p class="review-text" id="' . $uid . '-text">' . $short . '</p>'
            . '<a class="show-more" onclick="toggleComment(\'' . $uid . '\')" style="cursor:pointer;">Show more</a>'
            . '<span id="' . $uid . '-full" style="display:none;">' . $safeComment . '</span>';
    } else {
        $commentBlock = '<p class="review-text">' . $short . '</p>';
    }

    $imgHtml = '';
    if (!empty($imgUrl)) {
        $imgHtml = '<div class="review-images"><div class="img-wrapper">'
            . '<img src="' . htmlspecialchars($imgUrl) . '" alt="Review image" '
            . 'onerror="this.parentElement.style.display=\'none\'">'
            . '</div></div>';
    }

    return <<<HTML
<div class="review-card">
    <a href="#" class="tour-link">{$destName}</a>
    <div class="stars">{$starsHtml}</div>
    <div class="user-info">
        <div class="avatar" style="background:{$color};">{$initial}</div>
        <div class="user-details">
            <div class="name">{$name}</div>
            <div class="date">{$date}</div>
        </div>
    </div>
    {$commentBlock}
    {$imgHtml}
</div>
HTML;
}

$dpParam = isset($_GET['dest_page'])    ? '&dest_page='    . (int)$_GET['dest_page']    : '';
$rpParam = isset($_GET['reviews_page']) ? '&reviews_page=' . (int)$_GET['reviews_page'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe Vacay - <?= htmlspecialchars($heroState['state_name']) ?></title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/recommendation-page.css">
</head>

<body>

    <?php include('./includes/navbar.php'); ?>

    <!-- ══════════════════════════════════════════
     HERO — state carousel
    ══════════════════════════════════════════ -->
    <section class="hero" id="hero-section" style="
    
    background-image: linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.55)),
                      url('<?= htmlspecialchars(trim($heroState['state_url'])) ?>');
    background-size: cover; background-position: center;
">
        <button class="nav-arrow left" onclick="navigateHero('prev')"></button>

        <div class="hero-content">
            <h1><?= htmlspecialchars($heroState['state_name']) ?></h1>
            <p>Discover the beauty and culture of <?= htmlspecialchars($heroState['state_name']) ?>.
                Explore top-rated destinations, local experiences, and hidden gems waiting to be found.</p>
        </div>

        <div class="tags">
            <span class="tag-label">Tag:</span>
            <?php foreach ($heroTags as $tag): ?>
                <?php
                $emojis = ['🏙️', '✨', '🌿', '🏖️', '🏔️'];
                $emoji  = $emojis[$i % count($emojis)];
                ?>
                <span class="tag"><?= $emoji ?> <?= htmlspecialchars($tag) ?></span>
            <?php endforeach; ?>
        </div>

        <button class="nav-arrow right" onclick="navigateHero('next')"></button>
    </section>

    <!-- Search bar — cleanly below the hero -->
    <div class="search-container">
        <form method="GET" action="filter-search.php" class="search-bar">
            <input type="text" name="q" placeholder="Find places and things to do">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- ══════════════════════════════════════════
     CONTENT
    ══════════════════════════════════════════ -->
    <div class="container">

        <!-- ── High-Rated Destinations ── -->
        <section class="destinations-section">
            <h2 class="section-title">High Rate of Travel Destination In Malaysia</h2>

            <button class="slider-arrow prev" id="dest-prev" onclick="navigateDest(-1)"></button>

            <button class="slider-arrow next" id="dest-next" onclick="navigateDest(1)"></button>

            <div class="cards-wrapper" id="cards-wrapper">
                <?php if (empty($topDestinations)): ?>
                    <p style="text-align:center;color:#6b7280;grid-column:1/-1;">No destinations found.</p>
                <?php endif; ?>

                <?php foreach ($topDestinations as $dest): ?>
                    <?php
                    $dStateName = $dest['state_name'] ?? 'Malaysia';
                    $dImg = !empty($dest['image_url']) ? $dest['image_url']
                        : 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400';
                    $dRating  = number_format((float)($dest['average_rating'] ?? 0), 1);
                    $dReviews = number_format((int)($dest['reviews_count'] ?? 0));
                    $dPrice   = !empty($dest['price']) ? 'From ' . $dest['price'] : 'N/A';
                    ?>
                    <div class="dest-card">
                        <div class="heart-icon" onclick="this.classList.toggle('liked')" title="Wishlist">&#9825;</div>
                        <img src="<?= htmlspecialchars($dImg) ?>"
                            alt="<?= htmlspecialchars($dest['destination_name']) ?>"
                            onerror="this.src='https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400'">
                        <div class="card-info">
                            <span class="location"><?= htmlspecialchars($dStateName) ?></span>
                            <h3 class="dest-title"><?= htmlspecialchars($dest['destination_name']) ?></h3>
                            <span class="climate">Climate: Summer</span>
                            <div class="card-footer">
                                <div class="rating">
                                    <span class="score"><?= $dRating ?></span>
                                    <span class="star">&#9733;</span>
                                    <span class="reviews">(<?= $dReviews ?>)</span>
                                </div>
                                <div class="price"><?= htmlspecialchars($dPrice) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ── States In Malaysia ── -->
        <section class="states-section">
            <h2 class="section-title">State In Malaysia</h2>
            <div class="states-grid">
                <?php foreach ($allStates as $state): ?>
                    <?php
                    $sImg = !empty($state['state_url']) ? $state['state_url']
                        : 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400';
                    ?>
                    <div class="state-card"
                        onclick="window.location.href='state-destination.php?state_id=<?= $state['state_id'] ?>'"
                        style="cursor:pointer;">
                        <img src="<?= htmlspecialchars($sImg) ?>"
                            alt="<?= htmlspecialchars($state['state_name']) ?>"
                            onerror="this.src='https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400'">
                        <h3><?= htmlspecialchars($state['state_name']) ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ── Reviews ── -->
        <h2 class="section-title">What people are saying about Their Tour</h2>

        <?php if (empty($reviews)): ?>
            <p style="text-align:center;color:#6b7280;padding:2rem 0;">No reviews available yet.</p>
        <?php else: ?>

            <div class="reviews-grid" id="reviews-grid">
                <?php foreach ($reviews as $rev): ?>
                    <?= renderReviewCard($rev) ?>
                <?php endforeach; ?>
            </div>

            <div class="view-more-container">
                <div style="flex:1;border-top:3px solid #1e293b;"></div>
                <?php if ($totalReviews > $reviewsPerPage): ?>
                    <button class="btn-view-more" id="btn-load-more" onclick="loadMoreReviews()">View More</button>
                <?php endif; ?>
                <button class="btn-view-more" id="btn-show-less" onclick="showLessReviews()" style="display:none;">Show Less</button>
                <div style="flex:1;border-top:3px solid #1e293b;"></div>
            </div>

        <?php endif; ?>
    </div><!-- /container -->

    <script>
        let heroTimer = null;

        function startHeroTimer() {
            stopHeroTimer();
            heroTimer = setInterval(() => navigateHero('next'), 4000); // 每4秒切换
        }

        function stopHeroTimer() {
            if (heroTimer) {
                clearInterval(heroTimer);
                heroTimer = null;
            }
        }

        // auto-start hero carousel on page load
        document.addEventListener('DOMContentLoaded', () => {
            startHeroTimer();

            const hero = document.getElementById('hero-section');
            hero.addEventListener('mouseenter', stopHeroTimer);
            hero.addEventListener('mouseleave', startHeroTimer);
        });

        // ── Hero Carousel ──────────────────────────────────────────────
        function navigateHero(dir) {
            fetch(`ajax-handler.php?action=hero_nav&dir=${dir}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) return;

                    // Background image
                    const hero = document.getElementById('hero-section');
                    hero.style.backgroundImage =
                        `linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.55)), url('${data.state_url}')`;

                    // State name
                    hero.querySelector('.hero-content h1').textContent = data.state_name;
                    hero.querySelector('.hero-content p').textContent =
                        `Discover the beauty and culture of ${data.state_name}. Explore top-rated destinations, local experiences, and hidden gems waiting to be found.`;

                    // Tags
                    const tagsEl = document.querySelector('#hero-section .tags');
                    tagsEl.innerHTML = '<span class="tag-label">Tag:</span>';
                    data.tags.forEach(tag => {
                        const span = document.createElement('span');
                        span.className = 'tag';
                        span.textContent = tag;
                        tagsEl.appendChild(span);
                    });

                    // Page title
                    document.title = `Vibe Vacay - ${data.state_name}`;
                })
                .catch(console.error);
        }

        // ── Destinations Pagination ────────────────────────────────────
        let currentDestPage = <?= $destPage ?>;
        const maxDestPage = <?= $maxDestPage ?>;

        function navigateDest(dir) {
            const newPage = currentDestPage + dir;
            if (newPage < 0 || newPage > maxDestPage) return;

            fetch(`ajax-handler.php?action=destinations&page=${newPage}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) return;

                    currentDestPage = data.currentPage;

                    // Rebuild cards
                    const wrapper = document.getElementById('cards-wrapper');
                    wrapper.innerHTML = '';

                    if (data.cards.length === 0) {
                        wrapper.innerHTML = '<p style="text-align:center;color:#6b7280;grid-column:1/-1;">No destinations found.</p>';
                    } else {
                        data.cards.forEach(dest => {
                            const fallback = 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400';
                            const img = dest.image_url || fallback;
                            wrapper.innerHTML += `
                        <div class="dest-card">
                            <div class="heart-icon" onclick="this.classList.toggle('liked')" title="Wishlist">&#9825;</div>
                            <img src="${escHtml(img)}" alt="${escHtml(dest.destination_name)}"
                                 onerror="this.src='${fallback}'">
                            <div class="card-info">
                                <span class="location">${escHtml(dest.state_name)}</span>
                                <h3 class="dest-title">${escHtml(dest.destination_name)}</h3>
                                <span class="climate">Climate: Summer</span>
                                <div class="card-footer">
                                    <div class="rating">
                                        <span class="score">${escHtml(dest.average_rating)}</span>
                                        <span class="star">&#9733;</span>
                                        <span class="reviews">(${escHtml(dest.reviews_count)})</span>
                                    </div>
                                    <div class="price">${escHtml(dest.price)}</div>
                                </div>
                            </div>
                        </div>`;
                        });
                    }

                    // Update arrow states
                    document.getElementById('dest-prev').classList.toggle('disabled', currentDestPage === 0);
                    document.getElementById('dest-next').classList.toggle('disabled', currentDestPage >= data.maxPage);
                })
                .catch(console.error);
        }

        // Set initial arrow states on load
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('dest-prev').classList.toggle('disabled', currentDestPage === 0);
            document.getElementById('dest-next').classList.toggle('disabled', currentDestPage >= maxDestPage);
        });

        // Helper — basic XSS escape for JS-generated HTML
        function escHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function toggleComment(uid) {
            const textEl = document.getElementById(uid + '-text');
            const fullEl = document.getElementById(uid + '-full');
            const link = textEl ? textEl.nextElementSibling : null;

            if (!textEl || !fullEl) return;

            const isExpanded = link && link.textContent === 'Show less';

            if (isExpanded) {
                // Collapse
                const shortText = fullEl.textContent.substring(0, 200) + '...';
                textEl.textContent = shortText;
                if (link) link.textContent = 'Show more';
            } else {
                // Expand
                textEl.textContent = fullEl.textContent;
                if (link) link.textContent = 'Show less';
            }
        }

        // ── Load More Reviews ──────────────────────────────────────────
        let reviewOffset = <?= $reviewsPerPage ?>;

        function loadMoreReviews() {
            const btn = document.getElementById('btn-load-more');
            btn.textContent = 'Loading...';
            btn.disabled = true;

            fetch(`ajax-handler.php?action=reviews&offset=${reviewOffset}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) return;

                    const grid = document.getElementById('reviews-grid');

                    data.reviews.forEach(rev => {
                        const colors = ['#ff7f50', '#ff6b6b', '#4ecdc4', '#45b7d1', '#96c93d', '#f7b731', '#a29bfe'];
                        const name = rev.username || 'Traveller';
                        const initial = name[0].toUpperCase();
                        const color = colors[name.charCodeAt(0) % colors.length];
                        const rating = Math.max(1, Math.min(5, parseInt(rev.rating) || 5));
                        const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
                        const date = rev.created_at ?
                            new Date(rev.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) :
                            '';
                        const comment = rev.comment || '';
                        const isLong = comment.length > 200;
                        const uid = 'r' + (rev.review_id || Math.floor(Math.random() * 9999));

                        let commentBlock = '';
                        if (isLong) {
                            commentBlock = `
                        <p class="review-text" id="${uid}-text">${escHtml(comment.substring(0,200))}...</p>
                        <a class="show-more" onclick="toggleComment('${uid}')" style="cursor:pointer;">Show more</a>
                        <span id="${uid}-full" style="display:none;">${escHtml(comment)}</span>`;
                        } else {
                            commentBlock = `<p class="review-text">${escHtml(comment)}</p>`;
                        }

                        const imgHtml = rev.image_url ?
                            `<div class="review-images"><div class="img-wrapper">
                           <img src="${escHtml(rev.image_url)}" alt="Review image"
                                onerror="this.parentElement.style.display='none'">
                       </div></div>` :
                            '';

                        grid.innerHTML += `
                    <div class="review-card">
                        <a href="#" class="tour-link">${escHtml(rev.destination_name || 'Tour')}</a>
                        <div class="stars">${stars}</div>
                        <div class="user-info">
                            <div class="avatar" style="background:${color};">${initial}</div>
                            <div class="user-details">
                                <div class="name">${escHtml(name)}</div>
                                <div class="date">${date}</div>
                            </div>
                        </div>
                        ${commentBlock}
                        ${imgHtml}
                    </div>`;
                    });

                    reviewOffset += data.reviews.length;

                    if (data.hasMore) {
                        btn.textContent = 'View More';
                        btn.disabled = false;
                    } else {
                        btn.textContent = 'No More Reviews';
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                    }
                    document.getElementById('btn-show-less').style.display = 'inline-block';
                })
                .catch(err => {
                    console.error(err);
                    btn.textContent = 'View More';
                    btn.disabled = false;
                });
        }

        function showLessReviews() {
            const grid = document.getElementById('reviews-grid');
            const allCards = grid.querySelectorAll('.review-card');

            // 只保留前6个
            allCards.forEach((card, index) => {
                if (index >= <?= $reviewsPerPage ?>) {
                    card.remove();
                }
            });

            // 重置 offset
            reviewOffset = <?= $reviewsPerPage ?>;

            // 恢复按钮状态
            const loadBtn = document.getElementById('btn-load-more');
            loadBtn.textContent = 'View More';
            loadBtn.disabled = false;
            loadBtn.style.opacity = '1';
            loadBtn.style.display = 'inline-block';

            // 隐藏 Show Less
            document.getElementById('btn-show-less').style.display = 'none';

            // 滚回 reviews 区域
            grid.scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>

</body>

</html>