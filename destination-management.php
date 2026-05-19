<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';
 
if (empty($_SESSION['user_id']) || strtolower($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login-page.php');
    exit();
}
 
// --------------------------------------------------
// Fetch tag types with their tags for filter bar
// --------------------------------------------------
$tagTypeQuery = $conn->prepare("
    SELECT tt.tag_type_id, tt.tag_type_name, dt.tag_id, dt.tag_name
    FROM tag_type tt
    LEFT JOIN destination_tags dt ON tt.tag_type_id = dt.tag_type_id
    ORDER BY tt.tag_type_id ASC, dt.tag_name ASC
");
$tagTypeQuery->execute();
$tagTypeResult = $tagTypeQuery->get_result();
 
$tagTypes = [];
while ($row = $tagTypeResult->fetch_assoc()) {
    $ttid = $row['tag_type_id'];
    if (!isset($tagTypes[$ttid])) {
        $tagTypes[$ttid] = [
            'tag_type_id'   => $ttid,
            'tag_type_name' => $row['tag_type_name'],
            'tags'          => []
        ];
    }
    if ($row['tag_id']) {
        $tagTypes[$ttid]['tags'][] = [
            'tag_id'   => $row['tag_id'],
            'tag_name' => $row['tag_name']
        ];
    }
}
$tagTypeQuery->close();
 
// --------------------------------------------------
// Collect active tag filters from GET
// --------------------------------------------------
$search      = trim($_GET['search'] ?? '');
$activeTagIds = [];
foreach ($tagTypes as $tt) {
    $key = 'tag_type_' . $tt['tag_type_id'];
    if (!empty($_GET[$key])) {
        $activeTagIds[] = intval($_GET[$key]);
    }
}
 
// --------------------------------------------------
// Build destination query with search + tag filters
// --------------------------------------------------
$sql = "
    SELECT 
        d.destination_id,
        d.destination_name,
        d.description,
        d.reviews_count,
        d.image_url,
        d.phone_number,
        d.average_rating,
        d.price,
        s.state_name
    FROM destinations d
    LEFT JOIN states s ON d.state_id = s.state_id
    WHERE 1=1
";
 
$params = [];
$types  = '';
 
if ($search !== '') {
    $sql .= " AND (
        d.destination_name LIKE ?
        OR s.state_name LIKE ?
        OR d.destination_id IN (
            SELECT dtm.destination_id
            FROM destination_tag_mapping dtm
            JOIN destination_tags t ON dtm.tag_id = t.tag_id
            WHERE t.tag_name LIKE ?
        )
    )";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= 'sss';
}
 
// Each selected tag filter: destination must have that tag
foreach ($activeTagIds as $tagId) {
    $sql .= " AND d.destination_id IN (
        SELECT destination_id FROM destination_tag_mapping WHERE tag_id = ?
    )";
    $params[] = $tagId;
    $types   .= 'i';
}
 
$sql .= " ORDER BY d.destination_id ASC";
 
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result       = $stmt->get_result();
$destinations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
 
// --------------------------------------------------
// Fetch tags for each destination card
// --------------------------------------------------
foreach ($destinations as &$dest) {
    $did      = $dest['destination_id'];
    $tagQuery = $conn->prepare("
        SELECT t.tag_name
        FROM destination_tag_mapping dtm
        JOIN destination_tags t ON dtm.tag_id = t.tag_id
        WHERE dtm.destination_id = ?
    ");
    $tagQuery->bind_param('i', $did);
    $tagQuery->execute();
    $dest['tags'] = $tagQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    $tagQuery->close();
}
unset($dest);
?>
<!DOCTYPE html>
<html lang="en">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination Management Page</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/destination-management.css">
</head>
 
<body>
    <?php include('./includes/admin-nav-bar.php'); ?>
 
    <div class="title">
        <h1 style="margin-left: 15px; margin-top: 30px; display:flex; align-items:center;">
            <img src="icon/destination.png" style="width:40px;height:40px;margin-left:25px;margin-right:15px;"
                alt="Destination">
            Destination Management
        </h1>
    </div>
 
    <!-- Filter Bar -->
    <form method="GET" action="">
        <div class="filter-bar">
 
            <?php foreach ($tagTypes as $tt): ?>
                <?php
                $key          = 'tag_type_' . $tt['tag_type_id'];
                $selectedTag  = intval($_GET[$key] ?? 0);
                ?>
                <select name="<?= htmlspecialchars($key) ?>">
                    <option value="">
                        <?= htmlspecialchars($tt['tag_type_name']) ?>
                    </option>
                    <?php foreach ($tt['tags'] as $tag): ?>
                        <option value="<?= $tag['tag_id'] ?>"
                            <?= $selectedTag === intval($tag['tag_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag['tag_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endforeach; ?>
 
            <div class="filter-actions">
                <button type="button" class="btn btn-clear"
                    onclick="window.location.href='destination-management.php'">Clear</button>
                <button type="submit" class="btn btn-search">Search</button>
            </div>
        </div>
 
        <!-- Search Bar -->
        <div class="search-container">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Find places and things to do"
                    value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit">Search</button>
            </div>
            <a href="add-destination.php" class="add_Btn">+ Add Destination</a>
        </div>
    </form>
 
    <!-- Destination Cards -->
    <div class="destination-list">
        <?php if (empty($destinations)): ?>
            <div class="no-results">No destinations found.</div>
        <?php else: ?>
            <?php foreach ($destinations as $dest): ?>
                <div class="destination">
 
                    <img src="<?= htmlspecialchars($dest['image_url'] ?? 'Image/defaultDestination.png', ENT_QUOTES, 'UTF-8') ?>"
                        class="destination_image"
                        alt="<?= htmlspecialchars($dest['destination_name'], ENT_QUOTES, 'UTF-8') ?>"
                        onerror="this.src='Image/defaultDestination.png'">
 
                    <h2 class="destination_name">
                        <?= htmlspecialchars($dest['destination_name'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
 
                    <p class="destination_state">
                        <?= htmlspecialchars($dest['state_name'] ?? 'Unknown State', ENT_QUOTES, 'UTF-8') ?>
                    </p>
 
                    <div class="destination_fee">
                        Fee: <?= htmlspecialchars($dest['price'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                    </div>
 
                    <div class="destination_rating">
                        Rating: <span class="stars"><?= number_format((float)($dest['average_rating'] ?? 0), 1) ?>★</span>
                    </div>
 
                    <div class="destination_review_count">
                        Reviews Count: <?= number_format((int)($dest['reviews_count'] ?? 0)) ?>
                    </div>
 
                    <div class="destination_phone_number">
                        Phone Number: <?= htmlspecialchars($dest['phone_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                    </div>
 
                    <div class="destination_description">
                        Description: <?php
                            $desc = $dest['description'] ?? '';
                            echo htmlspecialchars(
                                strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc,
                                ENT_QUOTES, 'UTF-8'
                            );
                        ?>
                    </div>
 
                    <!-- Tags -->
                    <div class="tagging_box">
                        <?php if (!empty($dest['tags'])): ?>
                            <?php foreach ($dest['tags'] as $tag): ?>
                                <span class="destination_tag">
                                    <?= htmlspecialchars($tag['tag_name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="no-tags">No tags</span>
                        <?php endif; ?>
                    </div>
 
                    <!-- Edit / Delete -->
                    <div class="edit_delete_box">
                        <button type="button" class="edit_Btn"
                            onclick="window.location.href='edit-destination.php?id=<?= $dest['destination_id'] ?>'">
                            Edit
                        </button>
                        <button type="button" class="delete_Btn"
                            onclick="window.location.href='delete-destination.php?id=<?= $dest['destination_id'] ?>'">
                            Delete
                        </button>
                    </div>
 
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
 
</body>
 
</html>