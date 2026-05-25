<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';


// ── DELETE DESTINATION ──
if (isset($_POST['action']) && $_POST['action'] === 'delete_destination') {
    $id = intval($_POST['destination_id']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM destination_tag_mapping WHERE destination_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM destinations WHERE destination_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: destination-management.php");
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
            'tag_type_id' => $ttid,
            'tag_type_name' => $row['tag_type_name'],
            'tags' => []
        ];
    }
    if ($row['tag_id']) {
        $tagTypes[$ttid]['tags'][] = [
            'tag_id' => $row['tag_id'],
            'tag_name' => $row['tag_name']
        ];
    }
}
$tagTypeQuery->close();

// --------------------------------------------------
// Collect active tag filters from GET
// 直接用 named params，跟 filter-search-page.php 一样
// --------------------------------------------------
$search = trim($_GET['search'] ?? '');
$activeTagIds = [];

// Climate (tag_type_id=1, ids 1-4)
if (!empty($_GET['tag_type_1']))
    $activeTagIds[] = intval($_GET['tag_type_1']);
// Budget tag (tag_type_id=2, ids 5-10) — optional, skip if not needed
if (!empty($_GET['tag_type_2']))
    $activeTagIds[] = intval($_GET['tag_type_2']);
// Companion (tag_type_id=3, ids 11-14)
if (!empty($_GET['tag_type_3']))
    $activeTagIds[] = intval($_GET['tag_type_3']);
// Destination Type (tag_type_id=4, ids 15-19)
if (!empty($_GET['tag_type_4']))
    $activeTagIds[] = intval($_GET['tag_type_4']);

// Remove any zero values just in case
$activeTagIds = array_filter($activeTagIds, fn($v) => $v > 0);
$activeTagIds = array_values($activeTagIds);

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
$types = '';

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
    $types .= 'sss';
}

// AND logic: destination must have ALL selected tags
if (!empty($activeTagIds)) {
    $inPlaceholders = implode(',', array_fill(0, count($activeTagIds), '?'));
    $tagCount = count($activeTagIds);
    $sql .= "
        AND d.destination_id IN (
            SELECT destination_id
            FROM destination_tag_mapping
            WHERE tag_id IN ($inPlaceholders)
            GROUP BY destination_id
            HAVING COUNT(DISTINCT tag_id) = ?
        )
    ";
    foreach ($activeTagIds as $id) {
        $params[] = $id;
        $types .= 'i';
    }
    $params[] = $tagCount;
    $types .= 'i';
}

$sql .= " ORDER BY d.destination_id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$destinations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --------------------------------------------------
// Fetch tags for each destination card
// --------------------------------------------------
foreach ($destinations as &$dest) {
    $did = $dest['destination_id'];
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
                $key = 'tag_type_' . $tt['tag_type_id'];
                $selectedTag = intval($_GET[$key] ?? 0);
                ?>
                <select name="<?= htmlspecialchars($key) ?>">
                    <option value="">
                        <?= htmlspecialchars($tt['tag_type_name']) ?>
                    </option>
                    <?php foreach ($tt['tags'] as $tag): ?>
                        <option value="<?= $tag['tag_id'] ?>" <?= $selectedTag === intval($tag['tag_id']) ? 'selected' : '' ?>>
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
                        class="destination_image" alt="<?= htmlspecialchars($dest['destination_name'], ENT_QUOTES, 'UTF-8') ?>"
                        onerror="this.src='Image/defaultDestination.png'">

                    <h2 class="destination_name">
                        <?= htmlspecialchars($dest['destination_name'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <p class="destination_state">
                        <?= htmlspecialchars($dest['state_name'] ?? 'Unknown State', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="destination_fee">
                        <?php
                        $rawPrice = $dest['price'] ?? '';
                        if ($rawPrice === '' || $rawPrice === null) {
                            echo 'Fee: N/A';
                        } elseif (is_numeric(trim($rawPrice))) {
                            // Plain number → add RM prefix
                            echo 'Fee: RM ' . htmlspecialchars(number_format((float) $rawPrice, 2), ENT_QUOTES, 'UTF-8');
                        } else {
                            // Already has text like "Free", "RM25 (Adult) / RM15" → show as-is
                            echo 'Fee: ' . htmlspecialchars($rawPrice, ENT_QUOTES, 'UTF-8');
                        }
                        ?>
                    </div>

                    <div class="destination_rating">
                        Rating: <span class="stars"><?= number_format((float) ($dest['average_rating'] ?? 0), 1) ?>★</span>
                    </div>

                    <div class="destination_review_count">
                        Reviews Count: <?= number_format((int) ($dest['reviews_count'] ?? 0)) ?>
                    </div>

                    <div class="destination_phone_number">
                        Phone Number: <?= htmlspecialchars($dest['phone_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="destination_description">
                        Description: <?php
                        $desc = $dest['description'] ?? '';
                        echo htmlspecialchars(
                            strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc,
                            ENT_QUOTES,
                            'UTF-8'
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
                        <button type="button" class="view_Btn"
                            onclick="window.location.href='admin-feedback.php?id=<?= $dest['destination_id'] ?>'">
                            <img src="icon/view.png"
                                style="width:20px; height:20px; min-width:20px; max-width:20px; object-fit:contain;justify-content:center;"
                                alt="view">
                            View Feedback
                        </button>

                        <button type="button" class="edit_Btn"
                            onclick="window.location.href='edit-destination.php?id=<?= $dest['destination_id'] ?>'">
                            <img src="icon/edit.png"
                                style="width:16px; height:16px; min-width:16px; max-width:16px; object-fit:contain;justify-content:center;"
                                alt="edit">
                            Edit
                        </button>

                        <button type="button" class="delete_Btn"
                            onclick="openDeleteModal(<?= $dest['destination_id'] ?>, '<?= addslashes(htmlspecialchars($dest['destination_name'])) ?>')">
                            <img src="icon/delete.png" class="modal-title-icon"
                                style="width:16px; height:16px; min-width:16px; max-width:16px; object-fit:contain;justify-content:center;"
                                alt="delete"> Delete
                        </button>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- ══ DELETE DESTINATION MODAL ══ -->
    <div class="modal-overlay" id="deleteDestModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('deleteDestModal')">✕</button>
            <div class="modal-header">
                <img src="icon/delete.png" class="modal-title-icon" alt="delete">
                <h2>Delete Destination</h2>
            </div>
            <p class="modal-delete-msg">Are you sure you want to delete:</p>
            <p class="modal-delete-name" id="deleteDestName"></p>
            <form method="POST" action="destination-management.php">
                <input type="hidden" name="action" value="delete_destination">
                <input type="hidden" name="destination_id" id="deleteDestId">
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-reset"
                        onclick="closeModal('deleteDestModal')">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(id, name) {
            document.getElementById('deleteDestId').value = id;
            document.getElementById('deleteDestName').textContent = name;
            document.getElementById('deleteDestModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', e => {
                if (e.target === overlay) overlay.classList.remove('active');
            });
        });
    </script>
</body>

</html>