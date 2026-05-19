<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

// Redirect if not logged in or not an admin
if (empty($_SESSION['user_id']) || strtolower($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login-page.php');
    exit();
}

// --------------------------------------------------
// Fetch all destinations with their state name and tags
// --------------------------------------------------
$search = trim($_GET['search'] ?? '');
$filterMood = $_GET['mood'] ?? '';
// (add more filter params as needed)

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
    $sql .= " AND (d.destination_name LIKE ? OR d.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
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
// For each destination, fetch its tags
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
    $tagResult    = $tagQuery->get_result();
    $dest['tags'] = $tagResult->fetch_all(MYSQLI_ASSOC);
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
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding-bottom: 40px;
        }

        /* Filter Bar */
        .filter-bar {
            background-color: #1A2B49;
            height: 55px;
            border-radius: 50px;
            margin: 25px 20px 0 8px;
            width: calc(100% - 30px);
            max-width: 1460px;
            display: flex;
            align-items: center;
            gap: 15px;
            padding-left: 20px;
            position: relative;
            z-index: 3;
            box-sizing: border-box;
        }

        .filter-bar select {
            width: 170px;
            height: 30px;
            border-radius: 30px;
            border: none;
            padding-left: 10px;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            padding-right: 20px;
        }

        .btn {
            padding: 0.6rem 2rem;
            border-radius: 30px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-clear {
            background-color: white;
            color: #1A2B49;
            border: none;
        }

        .btn-search {
            background-color: #0064CE;
            color: white;
            border: 1px solid grey;
        }

        /* Search Container */
        .search-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 25px;
            padding-bottom: 1rem;
            margin-right: 20px;
            margin-left: 8px;
        }

        .search-bar {
            background-color: #1A2B49;
            padding: 0.5rem;
            padding-left: 1.5rem;
            border-radius: 40px;
            display: flex;
            width: 600px;
            justify-content: space-between;
            align-items: center;
            margin: 0 auto;
        }

        .search-bar input {
            background: transparent;
            border: none;
            color: white;
            outline: none;
            width: 100%;
        }

        .search-bar input::placeholder {
            color: #cbd5e1;
        }

        .search-bar button {
            background-color: white;
            color: #1A2B49;
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Add Button */
        .add_Btn {
            background-color: #0064CE;
            color: white;
            border: none;
            border-radius: 30px;
            width: 200px;
            height: 40px;
            font-size: 17px;
            font-weight: bold;
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
            text-decoration: none;
        }

        /* Destination Grid */
        .destination-list {
            display: flex;
            flex-wrap: wrap;
            padding: 0 10px;
            margin-left: 5px;
        }

        .destination {
            width: 350px;
            background-color: white;
            float: left;
            margin: 10px;
            padding: 15px;
            box-sizing: border-box;
            border-radius: 10px;
            border: 2px solid #b6b5b5;
        }

        .destination_image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #e0e0e0;
        }

        .destination_name {
            margin: 10px 0 0 0;
        }

        .destination_state {
            margin: 2px 0 15px 0;
            color: #555;
            font-size: 0.9rem;
        }

        .destination_fee,
        .destination_rating,
        .destination_review_count,
        .destination_description,
        .destination_phone_number {
            margin: 8px 0;
            display: block;
            font-size: 0.9rem;
        }

        /* Tags */
        .tagging_box {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
            margin: 10px 0;
        }

        .destination_tag {
            background-color: #D9D9D9;
            color: black;
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
        }

        .no-tags {
            color: #999;
            font-size: 0.8rem;
            font-style: italic;
        }

        /* Edit / Delete */
        .edit_delete_box {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .edit_Btn,
        .delete_Btn {
            background-color: #B3B6C3;
            color: black;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            height: 35px;
            font-size: 0.85rem;
        }

        /* Empty state */
        .no-results {
            width: 100%;
            text-align: center;
            padding: 60px 20px;
            color: #888;
            font-size: 1.1rem;
        }

        /* Rating stars */
        .stars {
            color: #f59e0b;
            margin-right: 4px;
        }
    </style>
</head>

<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <div class="title">
        <h1 style="margin-left: 15px; margin-top: 15px; display:flex; align-items:center;">
            <img src="icon/destination.png" style="width:40px;height:40px;margin-left:25px;margin-right:15px;"
                alt="Destination">
            Destination Management
        </h1>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="">
        <div class="filter-bar">
            <select name="mood">
                <option value="">Mood</option>
                <option value="Family" <?= ($_GET['mood'] ?? '') === 'Family' ? 'selected' : '' ?>>Family</option>
                <option value="Friend" <?= ($_GET['mood'] ?? '') === 'Friend' ? 'selected' : '' ?>>Friend</option>
                <option value="Colleague" <?= ($_GET['mood'] ?? '') === 'Colleague' ? 'selected' : '' ?>>Colleague</option>
                <option value="Other" <?= ($_GET['mood'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>

            <select name="climate">
                <option value="">Climate</option>
                <option value="Tropical" <?= ($_GET['climate'] ?? '') === 'Tropical' ? 'selected' : '' ?>>Tropical</option>
                <option value="Cool" <?= ($_GET['climate'] ?? '') === 'Cool' ? 'selected' : '' ?>>Cool</option>
            </select>

            <select name="budget">
                <option value="">Budget</option>
                <option value="Low" <?= ($_GET['budget'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
                <option value="Medium" <?= ($_GET['budget'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="High" <?= ($_GET['budget'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
            </select>

            <select name="travel_companion">
                <option value="">Travel Companion</option>
                <option value="Solo" <?= ($_GET['travel_companion'] ?? '') === 'Solo' ? 'selected' : '' ?>>Solo</option>
                <option value="Couple" <?= ($_GET['travel_companion'] ?? '') === 'Couple' ? 'selected' : '' ?>>Couple
                </option>
                <option value="Group" <?= ($_GET['travel_companion'] ?? '') === 'Group' ? 'selected' : '' ?>>Group</option>
            </select>

            <select name="destination_type">
                <option value="">Destination Type</option>
                <option value="Nature" <?= ($_GET['destination_type'] ?? '') === 'Nature' ? 'selected' : '' ?>>Nature
                </option>
                <option value="Urban" <?= ($_GET['destination_type'] ?? '') === 'Urban' ? 'selected' : '' ?>>Urban</option>
                <option value="Beach" <?= ($_GET['destination_type'] ?? '') === 'Beach' ? 'selected' : '' ?>>Beach</option>
            </select>

            <select name="travel_preferences">
                <option value="">Travel Preferences</option>
                <option value="Adventure" <?= ($_GET['travel_preferences'] ?? '') === 'Adventure' ? 'selected' : '' ?>>
                    Adventure</option>
                <option value="Relaxation" <?= ($_GET['travel_preferences'] ?? '') === 'Relaxation' ? 'selected' : '' ?>>
                    Relaxation</option>
                <option value="Cultural" <?= ($_GET['travel_preferences'] ?? '') === 'Cultural' ? 'selected' : '' ?>>
                    Cultural</option>
            </select>

            <div class="filter-actions">
                <a href="recommendation-page.php" class="btn btn-clear" style="text-decoration:none;">Clear</a>
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

                    <p class="destination_fee">
                        <strong>Fee:</strong> <?= htmlspecialchars($dest['price'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="destination_rating">
                        <strong>Rating:</strong>
                        <span class="stars">★</span><?= number_format((float) ($dest['average_rating'] ?? 0), 1) ?>
                    </p>

                    <p class="destination_review_count">
                        <strong>Reviews Count:</strong>
                        <?= number_format((int) ($dest['reviews_count'] ?? 0)) ?>
                    </p>

                    <span class="destination_phone_number">
                        <strong>Phone:</strong>
                        <?= htmlspecialchars($dest['phone_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <p class="destination_description">
                        <strong>Description:</strong>
                        <?php
                        $desc = $dest['description'] ?? '';
                        echo htmlspecialchars(
                            strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

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