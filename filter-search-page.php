<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── DB Connection ───────────────────────────────────────────────────────────
require_once('./conn.php'); // gives us $conn (mysqli)

// ─── 从 database 读取所有 tag types 和 tags ──────────────────────────────────
$allTagTypes = [];
$result = $conn->query("SELECT tag_type_id, tag_type_name FROM tag_type ORDER BY tag_type_id ASC");
while ($row = $result->fetch_assoc()) {
    $allTagTypes[$row['tag_type_id']] = $row['tag_type_name'];
}

// 读取所有 tags，按 tag_type_id 分组
$allTags = [];
$result = $conn->query("SELECT tag_id, tag_type_id, tag_name FROM destination_tags ORDER BY tag_id ASC");
while ($row = $result->fetch_assoc()) {
    $allTags[$row['tag_type_id']][] = $row;
}

// 找出 Mood 的 tag_type_id（tag_type_name = 'Mood'）
$moodTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'mood') {
        $moodTypeId = $tid;
        break;
    }
}
$moodTags = $allTags[$moodTypeId] ?? [];

// 非 Mood 的 tag types（用来生成 dropdown）
$filterTagTypes = array_filter($allTagTypes, fn($tid) => $tid !== $moodTypeId, ARRAY_FILTER_USE_KEY);

// ─── Read GET params ──────────────────────────────────────────────────────────
$selectedMood      = isset($_GET['mood'])      ? (int)$_GET['mood']      : 0;
$selectedClimate   = isset($_GET['climate'])   ? (int)$_GET['climate']   : 0;
$selectedBudget    = isset($_GET['budget'])    ? (int)$_GET['budget']    : 0;
$selectedCompanion = isset($_GET['companion']) ? (int)$_GET['companion'] : 0;
$selectedType      = isset($_GET['dest_type']) ? (int)$_GET['dest_type'] : 0;
$selectedState = isset($_GET['state_filter']) ? (int)$_GET['state_filter'] : 0;
$hiddenGems        = isset($_GET['hidden'])    && $_GET['hidden'] === '1';
$searchText        = isset($_GET['search'])    ? trim($_GET['search'])   : '';
$isRandom          = isset($_GET['random'])    && $_GET['random'] === '1';
$randomDestId      = isset($_GET['rdest'])     ? (int)$_GET['rdest']     : 0;
$isSearched        = array_key_exists('searched', $_GET);

// ─── Mood → Destination Type tag mapping (NOT from tag_mapping table) ────────
// Stressed/Sad → relaxing/fun places; Adventurous → nature/history; Happy → city/entertainment
// $moodTypeMap = [
//     20 => [16, 18, 19],  // Stressed  → Natural, Beach, Entertainment
//     21 => [],            // Neutral   → no restriction
//     22 => [18, 19, 16],  // Sad       → Beach, Entertainment, Natural
//     23 => [16, 17],      // Adventurous → Natural, Historical
//     24 => [15, 19],      // Happy     → City Life, Entertainment
// ];
// ─── Mood → Destination Type tag mapping ─────────────────────────────────────
// 找 Destination Type 的 tag_type_id
$destTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'destination type') {
        $destTypeId = $tid;
        break;
    }
}
$destTypeTags = $allTags[$destTypeId] ?? [];

// 把 destination type tag name map 到 id
$destTypeByName = [];
foreach ($destTypeTags as $tag) {
    $destTypeByName[strtolower($tag['tag_name'])] = $tag['tag_id'];
}

// Mood tag name map 到对应的 destination type tag ids
$moodTypeMap = [];
foreach ($moodTags as $moodTag) {
    $moodName = strtolower($moodTag['tag_name']);
    switch ($moodName) {
        case 'stressed':
            $moodTypeMap[$moodTag['tag_id']] = array_filter([
                $destTypeByName['natural'] ?? 0,
                $destTypeByName['beach'] ?? 0,
                $destTypeByName['entertainment'] ?? 0,
            ]);
            break;
        case 'neutral':
            $moodTypeMap[$moodTag['tag_id']] = [];
            break;
        case 'sad':
            $moodTypeMap[$moodTag['tag_id']] = array_filter([
                $destTypeByName['beach'] ?? 0,
                $destTypeByName['entertainment'] ?? 0,
                $destTypeByName['natural'] ?? 0,
            ]);
            break;
        case 'adventurous':
            $moodTypeMap[$moodTag['tag_id']] = array_filter([
                $destTypeByName['natural'] ?? 0,
                $destTypeByName['historical'] ?? 0,
            ]);
            break;
        case 'happy':
            $moodTypeMap[$moodTag['tag_id']] = array_filter([
                $destTypeByName['city life'] ?? 0,
                $destTypeByName['entertainment'] ?? 0,
            ]);
            break;
    }
}

// ─── Budget → price range mapping ────────────────────────────────────────────
// $budgetRangeMap = [
//     5  => [0,   100],
//     6  => [100, 200],
//     7  => [200, 300],
//     8  => [300, 400],
//     9  => [400, 500],
//     10 => [500, 999999],
// ];

$budgetTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'budget') {
        $budgetTypeId = $tid;
        break;
    }
}

// 从 tag_name 解析 price range
$budgetRangeMap = [];
foreach ($allTags[$budgetTypeId] ?? [] as $tag) {
    // 例如 "RM0 - RM100" → [0, 100]
    preg_match_all('/\d+/', $tag['tag_name'], $matches);
    $nums = $matches[0];
    if (count($nums) >= 2) {
        $budgetRangeMap[$tag['tag_id']] = [(int)$nums[0], (int)$nums[1]];
    } elseif (count($nums) === 1 && str_contains(strtolower($tag['tag_name']), 'above')) {
        $budgetRangeMap[$tag['tag_id']] = [(int)$nums[0], 999999];
    }
}

// ─── Find State tag type ID ──────────────────────────────────────────────────
$stateTypeId = 0;
foreach ($allTagTypes as $tid => $tname) {
    if (strtolower($tname) === 'state') {
        $stateTypeId = $tid;
        break;
    }
}

// ─── Collect tag IDs for AND-logic (exclude mood & budget — handled separately) ──
$tagIds = [];
if ($selectedClimate)   $tagIds[] = $selectedClimate;
if ($selectedCompanion) $tagIds[] = $selectedCompanion;
if ($selectedType)      $tagIds[] = $selectedType;


// ─── Query DB ────────────────────────────────────────────────────────────────
$destinations = [];

if ($isSearched) {
    $bindValues = [];
    $bindTypes  = '';

    $sql = "
        SELECT
            d.destination_id,
            d.destination_name,
            d.description,
            d.reviews_count,
            d.image_url,
            d.average_rating,
            d.price,
            s.state_name
        FROM destinations d
        LEFT JOIN states s ON d.state_id = s.state_id
        WHERE 1=1
    ";

    // Free-text search
    if ($searchText !== '') {
        $sql .= " AND (d.destination_name LIKE ? OR d.description LIKE ?)";
        $likeVal = '%' . $searchText . '%';
        $bindValues[] = $likeVal;
        $bindValues[] = $likeVal;
        $bindTypes   .= 'ss';
    }

    if ($selectedState) {
        $stateTagName = '';
        foreach ($allTags[$stateTypeId] ?? [] as $tag) {
            if ((int)$tag['tag_id'] === $selectedState) {
                $stateTagName = $tag['tag_name'];
                break;
            }
        }
        if ($stateTagName !== '') {
            // 用 state_name 找到 states 表的 state_id，再 filter d.state_id
            $mappedStateId = null;
            $stmtState = $conn->prepare("SELECT state_id FROM states WHERE state_name = ?");
            $stmtState->bind_param('s', $stateTagName);
            $stmtState->execute();
            $stmtState->bind_result($mappedStateId);
            $stmtState->fetch();
            $stmtState->close();

            if (!empty($mappedStateId)) {
                $sql .= " AND d.state_id = ?";
                $bindValues[] = $mappedStateId;
                $bindTypes   .= 'i';
            }
        }
    }

    // Hidden gems
    if ($hiddenGems) {
        $sql .= " AND d.reviews_count < 500 AND d.average_rating >= 4.0";
    }

    // ── MOOD: map to destination_type tags via tag_mapping ────────────────────
    if ($selectedMood && !empty($moodTypeMap[$selectedMood])) {
        $moodTypeTags   = $moodTypeMap[$selectedMood];
        $moodPlaceholders = implode(',', array_fill(0, count($moodTypeTags), '?'));
        $sql .= "
            AND d.destination_id IN (
                SELECT destination_id
                FROM destination_tag_mapping
                WHERE tag_id IN ($moodPlaceholders)
            )
        ";
        foreach ($moodTypeTags as $tid) {
            $bindValues[] = $tid;
            $bindTypes   .= 'i';
        }
    }

    // ── BUDGET: filter by actual price field using REGEXP to extract first number ─
    if ($selectedBudget && isset($budgetRangeMap[$selectedBudget])) {
        [$minPrice, $maxPrice] = $budgetRangeMap[$selectedBudget];
        // Extract the first number from price string (handles "RM10", "RM49 - RM79", "RM25 (Adult)..." etc)
        // First strip "RM" prefix, then extract leading number
        $priceExpr = "CAST(REGEXP_REPLACE(REGEXP_REPLACE(d.price, 'RM', ''), '[^0-9].*', '') AS UNSIGNED)";

        // Free destinations: include only in RM0-RM100 range
        if ($minPrice === 0) {
            $sql .= "
                AND (
                    LOWER(d.price) = 'free'
                    OR ($priceExpr BETWEEN ? AND ?)
                )
            ";
        } else {
            $sql .= "
                AND LOWER(d.price) != 'free'
                AND $priceExpr BETWEEN ? AND ?
            ";
        }
        $bindValues[] = $minPrice;
        $bindValues[] = $maxPrice;
        $bindTypes   .= 'ii';
    }

    // ── Climate & Companion & Destination Type: AND-logic via tag_mapping ─────
    if (!empty($tagIds)) {
        $inPlaceholders = implode(',', array_fill(0, count($tagIds), '?'));
        $tagCount = count($tagIds);
        $sql .= "
            AND d.destination_id IN (
                SELECT destination_id
                FROM destination_tag_mapping
                WHERE tag_id IN ($inPlaceholders)
                GROUP BY destination_id
                HAVING COUNT(DISTINCT tag_id) = ?
            )
        ";
        foreach ($tagIds as $id) {
            $bindValues[] = $id;
            $bindTypes   .= 'i';
        }
        $bindValues[] = $tagCount;
        $bindTypes   .= 'i';
    }

    if ($isRandom && $randomDestId) {
        // 已经有 dest ID 了，直接 filter 那一个（refresh 安全）
        $sql .= " AND d.destination_id = ?";
        $bindValues[] = $randomDestId;
        $bindTypes   .= 'i';
        $sql .= " LIMIT 1";
    } elseif ($isRandom) {
        // 第一次 random，才跑 RAND()
        $sql .= " ORDER BY RAND() LIMIT 1";
    } else {
        $sql .= " ORDER BY d.average_rating DESC";
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (!empty($bindValues)) {
        mysqli_stmt_bind_param($stmt, $bindTypes, ...$bindValues);
    }

    mysqli_stmt_execute($stmt);
    $result       = mysqli_stmt_get_result($stmt);
    $destinations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// ─── Helpers ─────────────────────────────────────────────────────────────────
function esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Build a query string keeping current filters, with overrides
function qstr(array $overrides = []): string
{
    $base = [
        'mood'      => $GLOBALS['selectedMood'],
        'climate'   => $GLOBALS['selectedClimate'],
        'budget'    => $GLOBALS['selectedBudget'],
        'companion' => $GLOBALS['selectedCompanion'],
        'dest_type' => $GLOBALS['selectedType'],
        'state_filter' => $GLOBALS['selectedState'],
        'hidden'    => $GLOBALS['hiddenGems'] ? '1' : '0',
        'search'    => $GLOBALS['searchText'],
        'searched'  => '1',
    ];
    $merged = array_merge($base, $overrides);
    // Strip zero/empty values to keep URL clean
    $merged = array_filter($merged, fn($v) => $v !== '' && $v !== '0' && $v !== 0 && $v !== false && $v !== null);
    return '?' . http_build_query($merged);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe Vacay - Discover & Plan</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/filter-search-page.css">
    <link rel="stylesheet" href="css/filter-search.css">
    <link rel="stylesheet" href="css/search-bar.css">
</head>

<body>

    <?php include('./includes/navbar.php'); ?>

    <section class="hero">
        <h1>Discover & plan things to do</h1>

        <div class="filter-panels-container">

            <!-- ══ FILTER BOX ══════════════════════════════════════════════════ -->
            <!--
            Each mood button submits the form with its own value.
            Dropdowns use onchange="this.form.submit()" for instant apply.
            Hidden gems toggle also auto-submits.
            The "Search" button is the explicit submit.
            "Clear" is a plain link to the bare page URL (no params).
        -->
            <form method="GET" action="" class="filter-box">
                <input type="hidden" name="searched" value="1">

                <div class="filter-header">Filter by:</div>

                <div class="filter-grid">

                    <!-- MOOD (tag_type_id = 5 → tag_ids 20-24) -->
                    <div class="mood-section">
                        <h3>How do you feel today:</h3>
                        <!-- Hidden input carries the selected mood value on submit -->
                        <input type="hidden" name="mood" id="mood-input" value="<?= $selectedMood ?>">
                        <div class="mood-buttons">
                            <?php
                            // Mood buttons are loaded from the database via $moodTags
                            $moodEmojis = [
                                'stressed'    => '😫',
                                'neutral'     => '😐',
                                'sad'         => '😢',
                                'adventurous' => '😎',
                                'happy'       => '😀',
                            ];
                            foreach ($moodTags as $moodTag):
                                $isActive = ($selectedMood === $moodTag['tag_id']);
                                $emoji = $moodEmojis[strtolower($moodTag['tag_name'])] ?? '😊';
                            ?>
                                <button
                                    type="button"
                                    data-mood-id="<?= $moodTag['tag_id'] ?>"
                                    class="mood-btn <?= $isActive ? 'active' : '' ?>"
                                    onclick="selectMood(<?= $moodTag['tag_id'] ?>)">
                                    <?= $emoji . ' ' . esc($moodTag['tag_name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- DROPDOWNS -->
                    <div class="dropdown-section">
                        <?php foreach ($filterTagTypes as $typeId => $typeName): ?>
                            <?php
                            $tags = $allTags[$typeId] ?? [];
                            if (empty($tags)) continue;

                            // 找 GET param 名字
                            $paramName = match (strtolower($typeName)) {
                                'climate'          => 'climate',
                                'budget'           => 'budget',
                                'travel companion' => 'companion',
                                'destination type' => 'dest_type',
                                'state'            => 'state_filter',
                                default            => 'filter_' . $typeId,
                            };

                            // 找当前选中的值
                            $selectedVal = isset($_GET[$paramName]) ? (int)$_GET[$paramName] : 0;
                            ?>
                            <select name="<?= $paramName ?>">
                                <option value="0"><?= esc($typeName) ?></option>
                                <?php foreach ($tags as $tag): ?>
                                    <option value="<?= $tag['tag_id'] ?>"
                                        <?= $selectedVal === $tag['tag_id'] ? 'selected' : '' ?>>
                                        <?= esc($tag['tag_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Hidden Gems toggle -->
                <span class="label">Looking for Hidden Gems?</span>
                <div class="toggle-section">
                    <span>Hidden / Less Touristy</span>
                    <label class="switch">
                        <input
                            type="checkbox"
                            name="hidden"
                            value="1"
                            <?= $hiddenGems ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="filter-actions">
                    <!-- Clear = go back to bare page, no GET params -->
                    <a href="?" class="btn btn-clear">Clear</a>
                    <button type="submit" class="btn btn-search">Search</button>
                </div>
            </form>

            <!-- ══ RANDOM BOX ══════════════════════════════════════════════════ -->
            <div class="random-box" id="random-box">
                <h3>Random Recommendation:</h3>
                <div class="dice-wrapper" id="dice-wrapper">
                    <img src="Image/dice.png" alt="Dice Icon" class="random-icon" id="dice-img">
                </div>
                <!-- Hidden form for actual submission -->
                <form method="GET" action="" id="random-form">
                    <?php if ($selectedMood)      echo '<input type="hidden" name="mood"      value="' . $selectedMood      . '">'; ?>
                    <?php if ($selectedClimate)   echo '<input type="hidden" name="climate"   value="' . $selectedClimate   . '">'; ?>
                    <?php if ($selectedBudget)    echo '<input type="hidden" name="budget"    value="' . $selectedBudget    . '">'; ?>
                    <?php if ($selectedCompanion) echo '<input type="hidden" name="companion" value="' . $selectedCompanion . '">'; ?>
                    <?php if ($selectedType)      echo '<input type="hidden" name="dest_type" value="' . $selectedType      . '">'; ?>
                    <?php if ($selectedState)     echo '<input type="hidden" name="state_filter" value="' . $selectedState . '">'; ?>
                    <?php if ($hiddenGems)        echo '<input type="hidden" name="hidden"    value="1">'; ?>
                    <?php if ($searchText)        echo '<input type="hidden" name="search"    value="' . esc($searchText)   . '">'; ?>
                    <input type="hidden" name="random" value="1">
                    <input type="hidden" name="searched" value="1">
                </form>
                <button class="btn btn-random" id="btn-random-trigger" onclick="rollDice()">🎲 Random</button>
            </div>

        </div>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
     SEARCH BAR
════════════════════════════════════════════════════════════════ -->
    <div class="container">

        <div class="search-container">
            <div style="position:relative; width:600px;">
                <form method="GET" action="" class="search-bar" style="display:flex;width:600px;" onsubmit="filterSaveAndSearch(event)">
                    <?php if ($selectedMood)      echo '<input type="hidden" name="mood"      value="' . $selectedMood      . '">'; ?>
                    <?php if ($selectedClimate)   echo '<input type="hidden" name="climate"   value="' . $selectedClimate   . '">'; ?>
                    <?php if ($selectedBudget)    echo '<input type="hidden" name="budget"    value="' . $selectedBudget    . '">'; ?>
                    <?php if ($selectedCompanion) echo '<input type="hidden" name="companion" value="' . $selectedCompanion . '">'; ?>
                    <?php if ($selectedType)      echo '<input type="hidden" name="dest_type" value="' . $selectedType      . '">'; ?>
                    <?php if ($selectedState) echo '<input type="hidden" name="state_filter" value="' . $selectedState . '">'; ?>
                    <?php if ($hiddenGems)        echo '<input type="hidden" name="hidden"    value="1">'; ?>
                    <input type="hidden" name="searched" value="1">
                    <input type="text" name="search" id="filter-search-input"
                        value="<?= esc($searchText) ?>"
                        placeholder="Find places and things to do"
                        autocomplete="off">
                    <button type="submit">Search</button>
                </form>

                <!-- Dropdown -->
                <div id="filter-search-dropdown" style="
                    display:none;
                    position:absolute;
                    top:calc(100% + 8px);
                    left:0; right:0;
                    background:white;
                    border:1px solid #ddd;
                    border-radius:16px;
                    box-shadow:0 4px 12px rgba(0,0,0,0.15);
                    z-index:9999;
                    overflow:hidden;
                "></div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════
         RESULTS
    ════════════════════════════════════════════════════════════ -->
        <?php if ($isSearched): ?>

            <div id="results-anchor" style="scroll-margin-top: 80px;"></div>

            <?php
            // // Active filter badge labels
            // $moodMap      = [20 => '😫 Stressed', 21 => '😐 Neutral', 22 => '😢 Sad', 23 => '😎 Adventurous', 24 => '😀 Happy'];
            // $climateMap   = [1 => 'Sunny/Hot', 2 => 'Tropical', 3 => 'Cool', 4 => 'Mountain'];
            // $budgetMap    = [5 => 'RM0–100', 6 => 'RM100–200', 7 => 'RM200–300', 8 => 'RM300–400', 9 => 'RM400–500', 10 => 'RM500+'];
            // $companionMap = [11 => 'Friend', 12 => 'Solo', 13 => 'Couple', 14 => 'Family'];
            // $typeMap      = [15 => 'City Life', 16 => 'Natural', 17 => 'Historical', 18 => 'Beach', 19 => 'Entertainment'];

            $badges = [];
            foreach ($moodTags as $mt) {
                if ($selectedMood === $mt['tag_id']) {
                    $emoji = $moodEmojis[strtolower($mt['tag_name'])] ?? '😊';
                    $badges[] = $emoji . ' ' . $mt['tag_name'];
                }
            }

            // 其他 filter badges
            foreach ($filterTagTypes as $typeId => $typeName) {
                $paramName = match (strtolower($typeName)) {
                    'climate'          => 'climate',
                    'budget'           => 'budget',
                    'travel companion' => 'companion',
                    'destination type' => 'dest_type',
                    default            => 'filter_' . $typeId,
                };
                $selectedVal = isset($_GET[$paramName]) ? (int)$_GET[$paramName] : 0;
                if ($selectedVal) {
                    foreach ($allTags[$typeId] ?? [] as $tag) {
                        if ($tag['tag_id'] === $selectedVal) {
                            $badges[] = $tag['tag_name'];
                            break;
                        }
                    }
                }
            }
            if ($hiddenGems)   $badges[] = '🔍 Hidden Gems';
            if ($searchText)   $badges[] = '"' . $searchText . '"';
            if ($isRandom)     $badges[] = '🎲 Random Pick';
            ?>

            <div class="results-header">
                <span class="results-count">
                    <?= count($destinations) ?> destination<?= count($destinations) !== 1 ? 's' : '' ?> found
                </span>
                <?php if ($badges): ?>
                    <div class="active-filters">
                        <span class="active-filters-label">Filters:</span>
                        <?php foreach ($badges as $b): ?>
                            <span class="filter-tag"><?= esc($b) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (empty($destinations)): ?>
                <div class="no-results">
                    😕 No destinations found matching your filters. Try adjusting your selection!
                </div>

            <?php else: ?>
                <div class="cards-wrapper" id="cards-wrapper">
                    <?php foreach ($destinations as $i => $dest):
                        $priceRaw = trim($dest['price'] ?? '');
                        if (empty($priceRaw) || strtolower($priceRaw) === 'free') {
                            $priceDisplay = '<span style="color:#16a34a;font-weight:600;">Free</span>';
                        } else {
                            $priceDisplay = 'From ' . esc($priceRaw);
                        }

                        $seed     = (int)$dest['destination_id'];
                        $fallback = "https://picsum.photos/seed/{$seed}/400/250";
                        $imgSrc   = !empty($dest['image_url']) ? esc($dest['image_url']) : $fallback;

                        // Hide cards beyond first 12
                        $hidden = $i >= 12 ? 'style="display:none;"' : '';
                    ?>
                        <div class="dest-card" data-index="<?= $i ?>" <?= $hidden ?>
                            onclick="window.location.href='destination-description.php?id=<?= $dest['destination_id'] ?>'"
                            style="cursor:pointer;">
                            <div class="heart-icon"
                                data-id="<?= $dest['destination_id'] ?>"
                                onclick="event.stopPropagation(); toggleWishlist(this, <?= $dest['destination_id'] ?>)">
                                <svg viewBox="0 0 24 24" width="20" height="20" class="heart-svg">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                                            2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
                                            C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5
                                            c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </div>
                            <img
                                class="thumbnail"
                                src="<?= $imgSrc ?>"
                                alt="<?= esc($dest['destination_name']) ?>"
                                onerror="this.onerror=null;this.src='<?= $fallback ?>'">
                            <div class="card-info">
                                <span class="location"><?= esc($dest['state_name'] ?? '') ?></span>
                                <h3 class="dest-title"><?= esc($dest['destination_name']) ?></h3>
                                <div class="card-footer">
                                    <div class="rating">
                                        <span class="score"><?= number_format((float)$dest['average_rating'], 1) ?></span>
                                        <span class="star">&#9733;</span>
                                        <span class="reviews">(<?= number_format((int)$dest['reviews_count']) ?>)</span>
                                    </div>
                                    <div class="price"><?= $priceDisplay ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($destinations) > 12): ?>
                    <div class="show-more-wrapper" id="show-more-wrapper">
                        <button class="btn-show-more" id="btn-show-more" onclick="toggleShowMore()">
                            Show More <span id="remaining-count">(<?= count($destinations) - 12 ?> more)</span> ▼
                        </button>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        <?php endif; /* $isSearched */ ?>

    </div>

    <script>
        function selectMood(id) {
            const input = document.getElementById('mood-input');
            const buttons = document.querySelectorAll('.mood-btn');

            if (parseInt(input.value) === id) {
                // clicking same mood → deselect
                input.value = '0';
                buttons.forEach(b => b.classList.remove('active'));
            } else {
                input.value = id;
                buttons.forEach(b => {
                    b.classList.toggle('active', parseInt(b.dataset.moodId) === id);
                });
            }
        }

        let showingAll = false;
        const BATCH = 12;

        function toggleShowMore() {
            const cards = document.querySelectorAll('#cards-wrapper .dest-card');
            const btn = document.getElementById('btn-show-more');
            const total = cards.length;
            if (!showingAll) {
                cards.forEach(c => c.style.display = '');
                btn.innerHTML = 'Show Less ▲';
                showingAll = true;
            } else {
                cards.forEach((c, i) => {
                    c.style.display = i >= BATCH ? 'none' : '';
                });
                btn.innerHTML = `Show More <span>(${total - BATCH} more)</span> ▼`;
                showingAll = false;
                document.getElementById('results-anchor').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        function toggleHeart(el) {
            const liked = el.innerHTML.trim() === '&#9829;' || el.innerHTML === '♥';
            el.innerHTML = liked ? '&#9825;' : '&#9829;';
            el.style.color = liked ? '' : 'red';
        }

        // ── Scroll to results after normal search ─────────────────────────────────────
        <?php if ($isSearched && !$isRandom): ?>
            window.addEventListener('DOMContentLoaded', function() {
                const target = document.getElementById('results-anchor');
                if (target) target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        <?php endif; ?>

        // ── Dice Roll ─────────────────────────────────────────────────────────────────
        function rollDice() {
            const dice = document.getElementById('dice-img');
            const btn = document.getElementById('btn-random-trigger');
            btn.disabled = true;
            btn.textContent = '🎲 Rolling...';
            dice.classList.remove('rolling');
            void dice.offsetWidth;
            dice.classList.add('rolling');
            sessionStorage.setItem('randomClicked', '1'); // ← 只 set，不要 remove
            sessionStorage.removeItem('randomDestId');
            setTimeout(() => {
                document.getElementById('random-form').submit();
            }, 1800);
        }

        // ── Show modal if this is a random result ─────────────────────────────────────
        <?php if ($isRandom && !empty($destinations)): ?>
            <?php $r = $destinations[0]; ?>
            window.addEventListener('DOMContentLoaded', function() {

                const freshId = <?= (int)$r['destination_id'] ?>;

                // 如果 URL 里还没有 rdest，把它加进去（replace state，不加 history）
                const urlParams = new URLSearchParams(window.location.search);
                if (!urlParams.has('rdest')) {
                    urlParams.set('rdest', freshId);
                    history.replaceState(null, '', '?' + urlParams.toString());
                }

                // 只有真正按了骰子才弹 modal
                if (!sessionStorage.getItem('randomClicked')) return;
                sessionStorage.removeItem('randomClicked');

                const dest = {
                    id: freshId,
                    name: <?= json_encode($r['destination_name']) ?>,
                    state: <?= json_encode($r['state_name'] ?? '') ?>,
                    rating: <?= json_encode(number_format((float)$r['average_rating'], 1)) ?>,
                    reviews: <?= (int)$r['reviews_count'] ?>,
                    price: <?= json_encode($r['price'] ?? 'Free') ?>,
                    img: <?= json_encode(!empty($r['image_url']) ? $r['image_url'] : 'https://picsum.photos/seed/' . (int)$r['destination_id'] . '/480/220') ?>
                };

                document.getElementById('modal-img').src = dest.img;
                document.getElementById('modal-img').onerror = function() {
                    this.src = 'https://picsum.photos/seed/<?= (int)$r['destination_id'] ?>/480/220';
                };
                document.getElementById('modal-state').textContent = dest.state;
                document.getElementById('modal-title').textContent = dest.name;
                document.getElementById('modal-rating').innerHTML =
                    `<span class="star">★</span> ${dest.rating} &nbsp;(${dest.reviews.toLocaleString()} reviews) &nbsp;·&nbsp; ${dest.price}`;

                document.getElementById('random-modal-overlay').classList.add('show');
                launchConfetti();
            });
        <?php endif; ?>

        function closeModal() {
            document.getElementById('random-modal-overlay').classList.remove('show');
            stopConfetti();
        }

        function rollAgain() {
            closeModal();
            sessionStorage.setItem('randomClicked', '1');
            // 清掉 rdest，让 PHP 重新跑 RAND()
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('rdest');
            const form = document.getElementById('random-form');
            // 确保 form 里没有旧的 rdest hidden input
            const oldRdest = form.querySelector('input[name="rdest"]');
            if (oldRdest) oldRdest.remove();
            setTimeout(() => {
                form.submit();
            }, 200);
        }

        function goToDestination() {
            const id = <?php echo $isRandom && !empty($destinations) ? (int)$destinations[0]['destination_id'] : 0; ?>;
            if (id) {
                sessionStorage.removeItem('randomDestId');
                sessionStorage.removeItem('randomClicked');
                window.location.href = 'destination-description.php?id=' + id;
            } else {
                closeModal();
            }
        }

        // ── Confetti ──────────────────────────────────────────────────────────────────
        let confettiAnimFrame = null;

        function launchConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const colors = ['#0ea5e9', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#f97316'];
            const particles = [];

            for (let i = 0; i < 180; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height - canvas.height,
                    w: Math.random() * 12 + 6,
                    h: Math.random() * 6 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    rot: Math.random() * 360,
                    rotV: (Math.random() - 0.5) * 6,
                    vy: Math.random() * 3 + 2,
                    vx: (Math.random() - 0.5) * 2,
                    alpha: 1,
                });
            }

            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                let alive = false;
                particles.forEach(p => {
                    if (p.alpha <= 0) return;
                    alive = true;
                    p.y += p.vy;
                    p.x += p.vx;
                    p.rot += p.rotV;
                    if (p.y > canvas.height * 0.75) p.alpha -= 0.018;
                    ctx.save();
                    ctx.globalAlpha = p.alpha;
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.rot * Math.PI / 180);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });
                if (alive) confettiAnimFrame = requestAnimationFrame(draw);
                else ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
            draw();
        }

        function stopConfetti() {
            if (confettiAnimFrame) cancelAnimationFrame(confettiAnimFrame);
            const canvas = document.getElementById('confetti-canvas');
            if (canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
        }
    </script>

    <!-- ── Random Result Modal ───────────────────────────────────────── -->
    <div class="random-modal-overlay" id="random-modal-overlay" onclick="closeModal()">
        <canvas id="confetti-canvas"></canvas>
        <div class="random-modal" id="random-modal" onclick="event.stopPropagation()">
            <button class="random-modal-close" onclick="closeModal()">✕</button>
            <img src="" alt="" class="random-modal-img" id="modal-img">
            <div class="random-modal-body">
                <div class="random-modal-emoji">🎉</div>
                <div class="random-modal-label">Your Random Destination</div>
                <div class="random-modal-state" id="modal-state"></div>
                <div class="random-modal-title" id="modal-title"></div>
                <div class="random-modal-rating" id="modal-rating"></div>
                <div class="random-modal-actions">
                    <button class="btn-again" onclick="rollAgain()">🎲 Roll Again</button>
                    <button class="btn-letsgo" onclick="goToDestination()">Let's Go! ✈️</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const FILTER_BASE_PATH = window.location.origin +
            window.location.pathname.replace(/\/[^/]*$/, '').replace(/ /g, '%20');

        const filterInput = document.getElementById('filter-search-input');
        const filterDropdown = document.getElementById('filter-search-dropdown');
        let filterDebounce = null;

        filterInput.addEventListener('focus', () => {
            if (filterInput.value.trim() === '') filterShowHistory();
        });

        filterInput.addEventListener('input', () => {
            const q = filterInput.value.trim();
            clearTimeout(filterDebounce);
            if (q === '') {
                filterShowHistory();
                return;
            }

            filterDebounce = setTimeout(() => {
                fetch(`${FILTER_BASE_PATH}/get-search-suggestions.php?type=suggest&q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.suggestions || data.suggestions.length === 0) {
                            filterHideDropdown();
                            return;
                        }
                        filterRenderDropdown(data.suggestions.map(s => ({
                            label: s.destination_name,
                            sublabel: s.state_name,
                            icon: '📍',
                            id: s.destination_id
                        })));
                    }).catch(console.error);
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!filterInput.contains(e.target) && !filterDropdown.contains(e.target)) {
                filterHideDropdown();
            }
        });

        function filterShowHistory() {
            fetch(`${FILTER_BASE_PATH}/get-search-suggestions.php?type=history`)
                .then(r => r.json())
                .then(data => {
                    if (!data.history || data.history.length === 0) {
                        filterHideDropdown();
                        return;
                    }
                    filterRenderDropdown(data.history.map(h => ({
                        label: h,
                        sublabel: null,
                        icon: '🕐',
                        id: null
                    })));
                }).catch(console.error);
        }

        function filterRenderDropdown(items) {
            filterDropdown.innerHTML = items.map((item, i) => `
        <div data-label="${filterEscHtml(item.label)}"
             data-id="${item.id ? parseInt(item.id) : ''}"
             class="filter-dropdown-item"
             style="padding:12px 16px; cursor:pointer; display:flex; align-items:center; gap:10px;
                    border-bottom:${i < items.length-1 ? '1px solid #f0f0f0' : 'none'};
                    background:white;"
             onmouseover="this.style.background='#f8f8f8'"
             onmouseout="this.style.background='white'">
            <span style="font-size:18px;">${item.icon}</span>
            <div>
                <div style="font-size:14px;color:#1e293b;font-weight:500;">${filterEscHtml(item.label)}</div>
                ${item.sublabel ? `<div style="font-size:12px;color:#9ca3af;">${filterEscHtml(item.sublabel)}</div>` : ''}
            </div>
        </div>
    `).join('');

            filterDropdown.querySelectorAll('.filter-dropdown-item').forEach(el => {
                el.addEventListener('click', () => {
                    const label = el.getAttribute('data-label');
                    const id = el.getAttribute('data-id');
                    filterHideDropdown();
                    filterSaveHistory(label);
                    if (id && parseInt(id) > 0) {
                        window.location.href = `${FILTER_BASE_PATH}/destination-description.php?id=${id}`;
                    } else {
                        // 保留现有 filters，只换 search keyword
                        filterInput.value = label;
                        filterInput.closest('form').submit();
                    }
                });
            });

            filterDropdown.style.display = 'block';
        }

        function filterSaveAndSearch(e) {
            e.preventDefault();
            const q = filterInput.value.trim();
            if (q) filterSaveHistory(q);
            filterInput.closest('form').submit();
        }

        function filterSaveHistory(keyword) {
            fetch(`${FILTER_BASE_PATH}/save-search-history.php`, {
                method: 'POST',
                body: new URLSearchParams({
                    keyword
                })
            });
        }

        function filterHideDropdown() {
            filterDropdown.style.display = 'none';
        }

        function filterEscHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
    <script>
        // 页面加载时标红已 liked 的 heart
        document.addEventListener('DOMContentLoaded', () => {
            const FBASE = window.location.origin +
                window.location.pathname.replace(/\/[^/]*$/, '').replace(/ /g, '%20');

            fetch(FBASE + '/ajax-handler.php?action=get_wishlist')
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
            const FBASE = window.location.origin +
                window.location.pathname.replace(/\/[^/]*$/, '').replace(/ /g, '%20');

            if (isLiked) {
                fetch(FBASE + '/remove-wishlist-by-dest.php', {
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
                fetch(FBASE + '/add-wishlist.php', {
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