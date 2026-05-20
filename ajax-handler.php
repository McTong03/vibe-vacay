<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
header('Content-Type: application/json');
 
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
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}
 
$action = $_GET['action'] ?? '';
 
// ─── Hero Navigation ──────────────────────────────────────────────────────────
if ($action === 'hero_nav') {
    $allStates   = $pdo->query("SELECT * FROM states ORDER BY state_id")->fetchAll();
    $totalStates = count($allStates);
 
    if (!isset($_SESSION['hero_index'])) {
        $_SESSION['hero_index'] = 0;
    }
 
    $dir = $_GET['dir'] ?? '';
    if ($dir === 'next') {
        $_SESSION['hero_index'] = ($_SESSION['hero_index'] + 1) % $totalStates;
    } elseif ($dir === 'prev') {
        $_SESSION['hero_index'] = ($_SESSION['hero_index'] - 1 + $totalStates) % $totalStates;
    }
 
    $heroState = $allStates[$_SESSION['hero_index']];
 
    // Tags
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
 
    // Rating
    $ratingStmt = $pdo->prepare("SELECT ROUND(AVG(average_rating),1) FROM destinations WHERE state_id = ?");
    $ratingStmt->execute([$heroState['state_id']]);
 
    echo json_encode([
        'state_name' => $heroState['state_name'],
        'state_url' => trim($heroState['state_url']),
        'tags'       => $heroTags,
    ]);
    exit;
}
 
// ─── Destinations Page ────────────────────────────────────────────────────────
if ($action === 'destinations') {
    $destPerPage = 4;
    $destPage    = max(0, (int)($_GET['page'] ?? 0));
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
 
    $cards = [];
    foreach ($topDestinations as $dest) {
        $cards[] = [
            'destination_name' => $dest['destination_name'],
            'state_name'       => $dest['state_name'] ?? 'Malaysia',
            'image_url'        => $dest['image_url'] ?? '',
            'average_rating'   => number_format((float)($dest['average_rating'] ?? 0), 1),
            'reviews_count'    => number_format((int)($dest['reviews_count'] ?? 0)),
            'price'            => !empty($dest['price']) ? 'From ' . $dest['price'] : 'N/A',
        ];
    }
 
    echo json_encode([
        'cards'       => $cards,
        'currentPage' => $destPage,
        'maxPage'     => $maxDestPage,
    ]);
    exit;
}


// ─── Reviews ─────────────────────────────────────────────────────────────────
if ($action === 'reviews') {
    $perPage = 6;
    $offset  = max(0, (int)($_GET['offset'] ?? 0));

    try {
        $reviews = $pdo->query("
            SELECT r.review_id, r.rating, r.comment, r.image_url, r.created_at,
                   u.user_name AS username, d.destination_name
            FROM reviews r
            LEFT JOIN users u   ON u.user_id        = r.user_id
            JOIN destinations d ON d.destination_id = r.destination_id
            WHERE r.rating >= 3
            ORDER BY r.review_id ASC
            LIMIT $perPage OFFSET $offset
        ")->fetchAll();
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]); // 看具体错误
        exit;
    }

    $totalReviews = (int)$pdo->query("SELECT COUNT(*) FROM reviews WHERE rating >= 3")->fetchColumn();

    echo json_encode([
        'reviews' => $reviews,
        'hasMore' => ($offset + $perPage) < $totalReviews,
        'total'   => $totalReviews,  // 临时加，看总数
        'offset'  => $offset,        // 临时加，确认 offset
    ]);
    exit;
}

echo json_encode(['error' => 'Unknown action']);