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
// Load all tag types and tags from DB
// --------------------------------------------------
$allTagTypes = [];
$result = $conn->query("SELECT tag_type_id, tag_type_name FROM tag_type ORDER BY tag_type_id ASC");
while ($row = $result->fetch_assoc()) {
    $allTagTypes[$row['tag_type_id']] = $row['tag_type_name'];
}

$allTags = [];
$result = $conn->query("SELECT tag_id, tag_type_id, tag_name FROM destination_tags ORDER BY tag_id ASC");
while ($row = $result->fetch_assoc()) {
    $allTags[$row['tag_type_id']][] = $row;
}

// Find Mood tag_type_id
$moodTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'mood') {
        $moodTypeId = $tid;
        break;
    }
}
$moodTags = $allTags[$moodTypeId] ?? [];

// Find Budget tag_type_id
$budgetTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'budget') {
        $budgetTypeId = $tid;
        break;
    }
}

// Filter tag types for dropdowns: exclude Mood and Budget
$filterTagTypes = array_filter(
    $allTagTypes,
    fn($tid) => $tid !== $moodTypeId && $tid !== $budgetTypeId,
    ARRAY_FILTER_USE_KEY
);

// --------------------------------------------------
// Mood → Destination Type mapping
// --------------------------------------------------
$destTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'destination type') {
        $destTypeId = $tid;
        break;
    }
}
$destTypeTags = $allTags[$destTypeId] ?? [];

$destTypeByName = [];
foreach ($destTypeTags as $tag) {
    $destTypeByName[strtolower($tag['tag_name'])] = $tag['tag_id'];
}

$moodTypeMap = [];
foreach ($moodTags as $moodTag) {
    $moodName = strtolower($moodTag['tag_name']);
    switch ($moodName) {
        case 'stressed':
            $moodTypeMap[$moodTag['tag_id']] = array_values(array_filter([
                $destTypeByName['natural'] ?? 0,
                $destTypeByName['beach'] ?? 0,
                $destTypeByName['entertainment'] ?? 0,
            ]));
            break;
        case 'neutral':
            $moodTypeMap[$moodTag['tag_id']] = [];
            break;
        case 'sad':
            $moodTypeMap[$moodTag['tag_id']] = array_values(array_filter([
                $destTypeByName['beach'] ?? 0,
                $destTypeByName['entertainment'] ?? 0,
                $destTypeByName['natural'] ?? 0,
            ]));
            break;
        case 'adventurous':
            $moodTypeMap[$moodTag['tag_id']] = array_values(array_filter([
                $destTypeByName['natural'] ?? 0,
                $destTypeByName['historical'] ?? 0,
            ]));
            break;
        case 'happy':
            $moodTypeMap[$moodTag['tag_id']] = array_values(array_filter([
                $destTypeByName['city life'] ?? 0,
                $destTypeByName['entertainment'] ?? 0,
            ]));
            break;
    }
}

// --------------------------------------------------
// Budget → price range mapping
// --------------------------------------------------
$budgetRangeMap = [];
foreach ($allTags[$budgetTypeId] ?? [] as $tag) {
    preg_match_all('/\d+/', $tag['tag_name'], $matches);
    $nums = $matches[0];
    if (count($nums) >= 2) {
        $budgetRangeMap[$tag['tag_id']] = [(int) $nums[0], (int) $nums[1]];
    } elseif (count($nums) === 1 && str_contains(strtolower($tag['tag_name']), 'above')) {
        $budgetRangeMap[$tag['tag_id']] = [(int) $nums[0], 999999];
    }
}

// --------------------------------------------------
// Read GET params
// --------------------------------------------------
$search = trim($_GET['search'] ?? '');
$selectedMood = isset($_GET['mood']) ? (int) $_GET['mood'] : 0;
$selectedBudget = isset($_GET['budget']) ? (int) $_GET['budget'] : 0;
$selectedClimate = isset($_GET['climate']) ? (int) $_GET['climate'] : 0;
$selectedCompanion = isset($_GET['companion']) ? (int) $_GET['companion'] : 0;
$selectedType = isset($_GET['dest_type']) ? (int) $_GET['dest_type'] : 0;
$selectedState = isset($_GET['state']) ? (int) $_GET['state'] : 0;

$allStates = [];
$result = $conn->query("SELECT state_id, state_name FROM states ORDER BY state_name ASC");
while ($row = $result->fetch_assoc()) {
    $allStates[] = $row;
}
$tagIds = array_values(array_filter([$selectedClimate, $selectedCompanion, $selectedType]));

// --------------------------------------------------
// Build destination query
// --------------------------------------------------
$sql = "
    SELECT
        d.destination_id, d.destination_name, d.description,
        d.reviews_count, d.image_url, d.phone_number,
        d.average_rating, d.price, s.state_name
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
    )";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($selectedMood && !empty($moodTypeMap[$selectedMood])) {
    $moodTypeTags = $moodTypeMap[$selectedMood];
    $moodPlaceholders = implode(',', array_fill(0, count($moodTypeTags), '?'));
    $sql .= " AND d.destination_id IN (
        SELECT destination_id FROM destination_tag_mapping WHERE tag_id IN ($moodPlaceholders)
    )";
    foreach ($moodTypeTags as $tid) {
        $params[] = $tid;
        $types .= 'i';
    }
}

if ($selectedBudget && isset($budgetRangeMap[$selectedBudget])) {
    [$minPrice, $maxPrice] = $budgetRangeMap[$selectedBudget];
    $priceExpr = "CAST(REGEXP_REPLACE(REGEXP_REPLACE(d.price, 'RM', ''), '[^0-9].*', '') AS UNSIGNED)";
    if ($minPrice === 0) {
        $sql .= " AND (LOWER(d.price) = 'free' OR ($priceExpr BETWEEN ? AND ?))";
    } else {
        $sql .= " AND LOWER(d.price) != 'free' AND $priceExpr BETWEEN ? AND ?";
    }
    $params[] = $minPrice;
    $params[] = $maxPrice;
    $types .= 'ii';
}

if (!empty($tagIds)) {
    $inPlaceholders = implode(',', array_fill(0, count($tagIds), '?'));
    $tagCount = count($tagIds);
    $sql .= " AND d.destination_id IN (
        SELECT destination_id FROM destination_tag_mapping
        WHERE tag_id IN ($inPlaceholders)
        GROUP BY destination_id HAVING COUNT(DISTINCT tag_id) = ?
    )";
    foreach ($tagIds as $id) {
        $params[] = $id;
        $types .= 'i';
    }
    $params[] = $tagCount;
    $types .= 'i';
}

if ($selectedState) {
    $sql .= " AND d.state_id = ?";
    $params[] = $selectedState;
    $types .= 'i';
}
$sql .= " ORDER BY d.destination_id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params))
    $stmt->bind_param($types, ...$params);
$stmt->execute();
$destinations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$destIds = array_column($destinations, 'destination_id');


foreach ($destinations as &$dest) {
    $dest['tags'] = [];
}
unset($dest);

if (!empty($destIds)) {
    $inPlaceholders = implode(',', array_fill(0, count($destIds), '?'));
    $types = str_repeat('i', count($destIds));

    $tagQuery = $conn->prepare("
        SELECT dtm.destination_id, t.tag_name
        FROM destination_tag_mapping dtm
        JOIN destination_tags t ON dtm.tag_id = t.tag_id
        WHERE dtm.destination_id IN ($inPlaceholders)
    ");
    $tagQuery->bind_param($types, ...$destIds);
    $tagQuery->execute();
    $tagRows = $tagQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    $tagQuery->close();

    $tagMap = [];
    foreach ($tagRows as $row) {
        $tagMap[$row['destination_id']][] = ['tag_name' => $row['tag_name']];
    }
    foreach ($destinations as &$dest) {
        $dest['tags'] = $tagMap[$dest['destination_id']] ?? [];
    }
    unset($dest);
}

function esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
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
        <h1 style="margin-left:15px;margin-top:30px;display:flex;align-items:center;">
            <img src="icon/destination.png" style="width:40px;height:40px;margin-left:25px;margin-right:15px;"
                alt="Destination">
            Destination Management
        </h1>
    </div>

    <!-- ══ FILTER BAR ══ -->
    <form method="GET" action="">
        <div class="filter-bar">

            <!-- Mood dropdown -->

            <?php
            ?>
            <select name="mood">
                <option value="0">Mood</option>
                <?php foreach ($moodTags as $moodTag): ?>
                    <option value="<?= $moodTag['tag_id'] ?>" <?= $selectedMood === $moodTag['tag_id'] ? 'selected' : '' ?>>
                        <?= esc($moodTag['tag_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Climate, Travel Companion, Destination Type dropdowns -->
            <?php foreach ($filterTagTypes as $typeId => $typeName):
                $tags = $allTags[$typeId] ?? [];
                if (empty($tags))
                    continue;
                $paramName = match (strtolower($typeName)) {
                    'climate' => 'climate',
                    'travel companion' => 'companion',
                    'destination type' => 'dest_type',
                    default => 'filter_' . $typeId,
                };
                $selectedVal = isset($_GET[$paramName]) ? (int) $_GET[$paramName] : 0;
                ?>
                <select name="<?= $paramName ?>">
                    <option value="0"><?= esc($typeName) ?></option>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= $tag['tag_id'] ?>" <?= $selectedVal === $tag['tag_id'] ? 'selected' : '' ?>>
                            <?= esc($tag['tag_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endforeach; ?>

            <!-- Budget dropdown (filters by actual price field) -->
            <select name="budget">
                <option value="0">Budget</option>
                <?php foreach ($allTags[$budgetTypeId] ?? [] as $tag): ?>
                    <option value="<?= $tag['tag_id'] ?>" <?= $selectedBudget === $tag['tag_id'] ? 'selected' : '' ?>>
                        <?= esc($tag['tag_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="state">
                <option value="0">State</option>
                <?php foreach ($allStates as $state): ?>
                    <option value="<?= $state['state_id'] ?>" <?= $selectedState === $state['state_id'] ? 'selected' : '' ?>>
                        <?= esc($state['state_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="filter-actions">
                <button type="button" class="btn btn-clear"
                    onclick="window.location.href='destination-management.php'">Clear</button>
                <button type="submit" class="btn btn-search">Search</button>
            </div>
        </div>

        <!-- search bar -->
        <div class="search-container">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Find places and things to do" value="<?= esc($search) ?>">
                <button type="submit">Search</button>
            </div>
            <a href="add-destination.php" class="add_Btn">+ Add Destination</a>
        </div>
    </form>

    <!-- ══ ACTIVE FILTER BADGES ══ -->
    <?php
    $badges = [];
    foreach ($moodTags as $mt) {
        if ($selectedMood === (int) $mt['tag_id']) {
            $badges[] = $mt['tag_name'];
        }
    }
    foreach ($filterTagTypes as $typeId => $typeName) {
        $paramName = match (strtolower($typeName)) {
            'climate' => 'climate',
            'travel companion' => 'companion',
            'destination type' => 'dest_type',
            default => 'filter_' . $typeId,
        };
        $selectedVal = isset($_GET[$paramName]) ? (int) $_GET[$paramName] : 0;
        if ($selectedVal) {
            foreach ($allTags[$typeId] ?? [] as $tag) {
                if ((int) $tag['tag_id'] === $selectedVal) {
                    $badges[] = $tag['tag_name'];
                    break;
                } // 👈 change here
            }
        }
    }
    if ($selectedBudget && isset($budgetRangeMap[$selectedBudget])) {
        [$mn, $mx] = $budgetRangeMap[$selectedBudget];
        $badges[] = 'Budget: RM' . $mn . ($mx === 999999 ? '+' : '–RM' . $mx);
    }

    if ($selectedState) {
        foreach ($allStates as $st) {
            if ((int) $st['state_id'] === $selectedState) {
                $badges[] = $st['state_name'];
                break;
            }
        }
    }
    if ($search)
        $badges[] = '"' . $search . '"';
    ?>
    <?php if (!empty($badges)): ?>
        <div style="padding:8px 40px;display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
            <span style="font-weight:600;color:#555;">Active filters:</span>
            <?php foreach ($badges as $b): ?>
                <span
                    style="background:#e0f0ff;color:#1e3a5f;border-radius:50px;padding:4px 14px;font-size:13px;font-weight:500;">
                    <?= esc($b) ?>
                </span>
            <?php endforeach; ?>
            <span style="color:#999;font-size:13px;">
                — <?= count($destinations) ?> destination<?= count($destinations) !== 1 ? 's' : '' ?> found
            </span>
        </div>
    <?php endif; ?>

    <!-- ══ DESTINATION CARDS ══ -->
    <div class="destination-list">
        <?php if (empty($destinations)): ?>
            <div class="no-results">No destinations found.</div>
        <?php else: ?>
            <?php foreach ($destinations as $dest): ?>
                <div class="destination">
                    <img src="<?= esc($dest['image_url'] ?? 'Image/defaultDestination.png') ?>" class="destination_image"
                        alt="<?= esc($dest['destination_name']) ?>" loading="lazy"
                        onerror="this.src='Image/defaultDestination.png'">

                    <h2 class="destination_name"><?= esc($dest['destination_name']) ?></h2>
                    <p class="destination_state"><?= esc($dest['state_name'] ?? 'Unknown State') ?></p>

                    <div class="destination_fee">
                        <?php
                        $rawPrice = $dest['price'] ?? '';
                        if ($rawPrice === '' || $rawPrice === null) {
                            echo 'Fee: N/A';
                        } elseif (strtolower(trim($rawPrice)) === 'free') {
                            echo 'Fee: <span style="color:#16a34a;font-weight:600;">Free</span>';
                        } elseif (is_numeric(trim($rawPrice))) {
                            echo 'Fee: RM ' . number_format((int) abs($rawPrice), 0);
                        } else {
                            echo 'Fee: ' . esc($rawPrice);
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
                        Phone Number: <?= esc($dest['phone_number'] ?? 'N/A') ?>
                    </div>
                    <div class="destination_description">
                        Description: <?php
                        $desc = $dest['description'] ?? '';
                        echo esc(strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc);
                        ?>
                    </div>

                    <div class="tagging_box">
                        <?php if (!empty($dest['tags'])): ?>
                            <?php foreach ($dest['tags'] as $tag): ?>
                                <span class="destination_tag"><?= esc($tag['tag_name']) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="no-tags">No tags</span>
                        <?php endif; ?>
                    </div>

                    <div class="edit_delete_box">
                        <button type="button" class="view_Btn"
                            onclick="window.location.href='admin-feedback.php?id=<?= $dest['destination_id'] ?>'">
                            <img src="icon/view.png" style="width:20px;height:20px;object-fit:contain;" alt="view">
                            View Feedback
                        </button>
                        <button type="button" class="edit_Btn"
                            onclick="window.location.href='edit-destination.php?id=<?= $dest['destination_id'] ?>'">
                            <img src="icon/edit.png" style="width:16px;height:16px;object-fit:contain;" alt="edit">
                            Edit
                        </button>
                        <button type="button" class="delete_Btn"
                            onclick="openDeleteModal(<?= $dest['destination_id'] ?>, '<?= addslashes(esc($dest['destination_name'])) ?>')">
                            <img src="icon/delete.png" style="width:16px;height:16px;object-fit:contain;" alt="delete">
                            Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ══ DELETE MODAL ══ -->
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