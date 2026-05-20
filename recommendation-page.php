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
                    <div class="state-card">
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

<!-- <section class="hero">
        <button class="nav-arrow left">&#8592;</button>
        <div class="hero-content">
            <h1>Kuala Lumpur</h1>
            <p>Malaysia's capital city blends modern skyscrapers with cultural heritage. From iconic landmarks to shopping and food experiences, it's perfect for travelers seeking energy and excitement.</p>
            <div class="tags">
                <span class="tag-label">Tag:</span>
                <span class="tag">Urban</span>
                <span class="tag">Vibrant</span>
                <span class="tag">Lifestyle</span>
            </div>
        </div>
        <div class="rating-box">
            <span class="score">5.0</span>
            <span class="text">Rating</span>
        </div>
        <button class="nav-arrow right">&#8594;</button>
    </section>

    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Find places and things to do">
            <button>Search</button>
        </div>
    </div>

    <div class="container">
        <section class="destinations-section">
            <h2 class="section-title">High Rate of Travel Destination In Malaysia</h2>
            
            <div class="cards-wrapper">
                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&q=80&w=400" alt="Blue Tears">
                    <div class="card-info">
                        <span class="location">Kuala Selangor</span>
                        <h3 class="dest-title">Magical Blue Tears Night Boat Tour in Kuala Selangor</h3>
                        <span class="climate">Climate: Summer</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">5.0</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(1,148)</span>
                            </div>
                            <div class="price">From RM 31</div>
                        </div>
                    </div>
                </div>

                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1584286595398-a59f21d313f5?auto=format&fit=crop&q=80&w=400" alt="Petronas Towers">
                    <div class="card-info">
                        <span class="location">Kuala Lumpur</span>
                        <h3 class="dest-title">Skip-the-Line Petronas Twin Towers E-Ticket</h3>
                        <span class="climate">Climate: Summer</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">4.6</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(3,148)</span>
                            </div>
                            <div class="price">From RM182</div>
                        </div>
                    </div>
                </div>

                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&q=80&w=400" alt="Sky Mirror">
                    <div class="card-info">
                        <span class="location">Kuala Selangor</span>
                        <h3 class="dest-title">Magical Sky Mirror Experience in Kuala Selangor</h3>
                        <span class="climate">Climate: Summer</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">4.6</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(3,148)</span>
                            </div>
                            <div class="price">From RM182</div>
                        </div>
                    </div>
                </div>

                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1550184658-ff6132a71714?auto=format&fit=crop&q=80&w=400" alt="Rafflesia Flower">
                    <div class="card-info">
                        <span class="location">Kuala Lumpur · Lojing Highlands</span>
                        <h3 class="dest-title">Rafflesia Flower Trek at Lojing Highlands</h3>
                        <span class="climate">Climate: Mountain</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">4.6</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(3,148)</span>
                            </div>
                            <div class="price">From RM182</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="slider-arrow">&#8594;</button>
        </section>

        <section class="states-section">
            <h2 class="section-title">State In Malaysia</h2>
            
            <div class="states-grid">
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400" alt="Kuala Lumpur">
                    <h3>Kuala Lumpur</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1610884631558-86fc35bc61eb?auto=format&fit=crop&q=80&w=400" alt="Melaka">
                    <h3>Melaka</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1582801642861-12001e74f1ec?auto=format&fit=crop&q=80&w=400" alt="Negeri Sembilan">
                    <h3>Negeri Sembilan</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1621868357777-6a2c206cfd15?auto=format&fit=crop&q=80&w=400" alt="Johor Bahru">
                    <h3>Johor Bahru</h3>
                </div>
                
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1587823528410-b96057bbfa74?auto=format&fit=crop&q=80&w=400" alt="Kedah">
                    <h3>Kedah</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1563200938-230af97f1fbc?auto=format&fit=crop&q=80&w=400" alt="Kelantan">
                    <h3>Kelantan</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?auto=format&fit=crop&q=80&w=400" alt="Pahang">
                    <h3>Pahang</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1605333555546-f9479b4890d2?auto=format&fit=crop&q=80&w=400" alt="Perak">
                    <h3>Perak</h3>
                </div>

                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1517436073-3b1b11ce1163?auto=format&fit=crop&q=80&w=400" alt="Perlis">
                    <h3>Perlis</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1580219616035-188ccbc05b22?auto=format&fit=crop&q=80&w=400" alt="Pulau Pinang">
                    <h3>Pulau Pinang</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1588693959253-8d655f4581f3?auto=format&fit=crop&q=80&w=400" alt="Selangor">
                    <h3>Selangor</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1628174780517-5eeb8e94589c?auto=format&fit=crop&q=80&w=400" alt="Terengganu">
                    <h3>Terengganu</h3>
                </div>
            </div>
        </section>

        ``<h2 class="section-title">What people are saying about Their Tour</h2>

        <div class="reviews-grid">
            
            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">B</div>
                    <div class="user-details">
                        <div class="name">Briege</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Moinya I hope I spelt this right was an amazing guide so knowledgeable fun and passionate about the tour we had the best time and were completely blew away with this tour would definitely...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1548625361-ec853c896944?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">NYC: Metropolitan Museum: "Secrets of the MET" Experience</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">P</div>
                    <div class="user-details">
                        <div class="name">Pascal</div>
                        <div class="date">March 29, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Great! Very informative, lovely friendly driver, mountains of food! All very good.</p>
                
                <div class="review-images" style="margin-top: auto;">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1565121544322-835616b49e25?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-red">E</div>
                    <div class="user-details">
                        <div class="name">Elmar</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">An amazing experience! Our guide was highly experienced, incredibly passionate, and made the entire tour truly engaging. You could immediately tell how much he loves his job. The tour was...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1542317148-8badecc4eb97?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1604928141064-207cea6f571f?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">NYC: Metropolitan Museum: "Secrets of the MET" Experience</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">P</div>
                    <div class="user-details">
                        <div class="name">Pascal</div>
                        <div class="date">March 29, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Great! Very informative, lovely friendly driver, mountains of food! All very good.</p>
                
                <div class="review-images" style="margin-top: auto;">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1565121544322-835616b49e25?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-red">E</div>
                    <div class="user-details">
                        <div class="name">Elmar</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">An amazing experience! Our guide was highly experienced, incredibly passionate, and made the entire tour truly engaging. You could immediately tell how much he loves his job. The tour was...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1542317148-8badecc4eb97?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1604928141064-207cea6f571f?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">B</div>
                    <div class="user-details">
                        <div class="name">Briege</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Moinya I hope I spelt this right was an amazing guide so knowledgeable fun and passionate about the tour we had the best time and were completely blew away with this tour would definitely...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1548625361-ec853c896944?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="view-more-container">
            <button class="btn-view-more">View More</button>
        </div>
        
    </div>

</body>
</html> -->