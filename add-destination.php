<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("conn.php");

// ── AJAX: must be FIRST before any other logic ──
if (isset($_GET['get_tags'])) {
    $tid = intval($_GET['get_tags']);
    $result = $conn->query("SELECT tag_id, tag_name FROM destination_tags WHERE tag_type_id = $tid ORDER BY tag_name");
    $tags = [];
    while ($row = $result->fetch_assoc())
        $tags[] = $row;
    header('Content-Type: application/json');
    echo json_encode($tags);
    exit();
}

// ── Fetch states for dropdown ──
$states = [];
$res = $conn->query("SELECT state_id, state_name FROM states ORDER BY state_name");
while ($row = $res->fetch_assoc())
    $states[] = $row;

// ── Fetch tag types + their tags ──
$tagTypes = [];
$res = $conn->query("SELECT tag_type_id, tag_type_name FROM tag_type ORDER BY tag_type_name");
while ($row = $res->fetch_assoc()) {
    $tid = $row['tag_type_id'];
    $tags = [];
    $res2 = $conn->query("SELECT tag_id, tag_name FROM destination_tags WHERE tag_type_id = $tid ORDER BY tag_name");
    while ($t = $res2->fetch_assoc())
        $tags[] = $t;
    $row['tags'] = $tags;
    $tagTypes[] = $row;
}

// ── Handle form submission ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-add'])) {

    $destination_name = trim($_POST['destination_name']);
    $state_id = intval($_POST['state_id']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $phone_number = trim($_POST['phone_number']);
    $average_rating = floatval($_POST['average_rating']);
    $price = trim($_POST['price']);

    // Insert into destinations
    $stmt = $conn->prepare("INSERT INTO destinations 
    (state_id, destination_name, description, image_url, phone_number, price, average_rating, reviews_count)
    VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("isssssd", $state_id, $destination_name, $description, $image_url, $phone_number, $price, $average_rating);
    $stmt->execute();
    $destination_id = $stmt->insert_id;
    $stmt->close();

    // Insert into destination_tag_mapping (can be multiple tags)
    if (!empty($_POST['tags'])) {
        $stmt2 = $conn->prepare("INSERT INTO destination_tag_mapping (destination_id, tag_id) VALUES (?, ?)");
        foreach ($_POST['tags'] as $tag_id) {
            $tag_id = intval($tag_id);
            if ($tag_id > 0) {
                $stmt2->bind_param("ii", $destination_id, $tag_id);
                $stmt2->execute();
            }
        }
        $stmt2->close();
    }

    mysqli_close($conn);
    echo '<script>alert("New destination created successfully!"); window.location.href="destination-management.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination Page</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/add-destination.css">
</head>

<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <div class="title">
        <button type="button" class="back_Btn" onclick="window.location.href='destination-management.php'">
            <img src="icon/error.png" class="back-icon" />
        </button>

        <img src="icon/destination.png" class="title-icon" alt="Destination">

        <h1>Add Destination</h1>
    </div>

    <div class="content-container">
        <form class="container" method="POST" action="add-destination.php">

            <h3>Destination Name</h3>
            <input type="text" name="destination_name" class="tag-type" placeholder="Enter destination name" required>

            <h3>Destination Picture URL</h3>
            <input type="text" name="image_url" class="tag-type" placeholder="Enter destination picture URL">

            <h3>Destination State</h3>
            <div class="filter-bar">
                <select name="state_id" required>
                    <option value="">Please Select</option>
                    <?php foreach ($states as $s): ?>
                        <option value="<?= $s['state_id'] ?>"><?= htmlspecialchars($s['state_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <h3>Tag Type</h3>
            <div class="filter-bar">
                <select name="tag_type_id" id="tagTypeSelect">
                    <option value="">Please Select</option>
                    <?php foreach ($tagTypes as $tt): ?>
                        <option value="<?= $tt['tag_type_id'] ?>"><?= htmlspecialchars($tt['tag_type_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3>Tags</h3>
            <div id="tagCheckboxes">
                <span style="color:#6b7280; font-size:0.88rem;">Select a tag type first</span>
            </div>

            <!-- Selected tags display -->
            <div id="selectedTagsContainer" style="display:none; margin-top:0.75rem;">
                <p style="font-size:0.8rem; color:#6b7280; margin-bottom:0.5rem;">Selected tags:</p>
                <div id="selectedTagsDisplay"></div>
            </div>

            <h3>Destination Price (RM)</h3>
            <input type="text" name="price" class="tag-type" placeholder="e.g. 25, Free, RM25 (Adult) / RM15" required>

            <h3>Destination Rating (/5)</h3>
            <input type="number" step="0.1" min="0" max="5" name="average_rating" class="tag-type"
                placeholder="Enter destination rating" required>

            <h3>Destination Phone Number</h3>
            <input type="text" name="phone_number" class="tag-type" placeholder="01x-xxxxxxx">

            <h3>Destination Description</h3>
            <textarea name="description" class="tag-type description-box"
                placeholder="Enter destination description"></textarea>

            <div class="filter-actions">
                <button type="reset" class="btn btn-reset">Reset</button>
                <button type="submit" name="btn-add" class="btn btn-add">Add Destination</button>
            </div>

        </form>
    </div>
</body>

</html>
<script>
    // Track selected tags: { tag_id: tag_name }
    const selectedTags = {};

    function renderSelectedTags() {
        const container = document.getElementById('selectedTagsContainer');
        const display = document.getElementById('selectedTagsDisplay');
        display.innerHTML = '';

        const ids = Object.keys(selectedTags);
        if (ids.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';

        ids.forEach(tag_id => {
            const pill = document.createElement('span');
            pill.style.cssText = `
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #1A2B49;
            color: white;
            border-radius: 30px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
        `;
            pill.innerHTML = `
            ${selectedTags[tag_id]}
            <input type="hidden" name="tags[]" value="${tag_id}">
            <span onclick="removeTag(${tag_id})" style="cursor:pointer; font-size:1rem; font-weight:700; line-height:1; margin-left:2px;">×</span>
        `;
            display.appendChild(pill);
        });
    }

    function removeTag(tag_id) {
        delete selectedTags[tag_id];

        // Uncheck the pill in the checkbox area if visible
        const cb = document.querySelector(`input[type=checkbox][value="${tag_id}"]`);
        if (cb) {
            cb.checked = false;
            const label = cb.closest('label');
            if (label) {
                label.style.background = 'white';
                label.style.color = '#1A2B49';
                label.style.borderColor = '#1A2B49';
                label.querySelector('.plus-icon').textContent = '+';
            }
        }

        renderSelectedTags();
    }

    document.getElementById('tagTypeSelect').addEventListener('change', function () {
        const tid = this.value;
        const box = document.getElementById('tagCheckboxes');

        box.innerHTML = '';
        box.style.display = 'flex';

        if (!tid) {
            box.innerHTML = '<span style="color:#6b7280;font-size:0.88rem;">Select a tag type first</span>';
            return;
        }

        box.innerHTML = '<span style="color:#6b7280;font-size:0.88rem;">Loading...</span>';

        fetch('add-destination.php?get_tags=' + tid)
            .then(res => res.json())
            .then(tags => {
                box.innerHTML = '';
                if (tags.length === 0) {
                    box.innerHTML = '<span style="color:#6b7280;font-size:0.88rem;">No tags found for this type</span>';
                    return;
                }
                tags.forEach(tag => {
                    const label = document.createElement('label');
                    label.style.cssText = `
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    background: white;
                    border: 2px solid #1A2B49;
                    border-radius: 30px;
                    padding: 0.4rem 1rem;
                    font-size: 0.88rem;
                    font-weight: 500;
                    cursor: pointer;
                    color: #1A2B49;
                    transition: all 0.15s;
                    user-select: none;
                `;

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'tags_check[]';   // not tags[] — hidden inputs handle submission
                    checkbox.value = tag.tag_id;
                    checkbox.style.display = 'none';

                    const plus = document.createElement('span');
                    plus.className = 'plus-icon';
                    plus.style.cssText = 'font-size:1rem; font-weight:700; line-height:1;';

                    const tagName = document.createElement('span');
                    tagName.textContent = tag.tag_name;

                    // Reflect already-selected state
                    if (selectedTags[tag.tag_id]) {
                        checkbox.checked = true;
                        label.style.background = '#1A2B49';
                        label.style.color = 'white';
                        label.style.borderColor = '#1A2B49';
                        plus.textContent = '✓';
                    } else {
                        plus.textContent = '+';
                    }

                    label.appendChild(checkbox);
                    label.appendChild(tagName);
                    label.appendChild(plus);

                    label.addEventListener('click', function () {
                        checkbox.checked = !checkbox.checked;
                        if (checkbox.checked) {
                            label.style.background = '#1A2B49';
                            label.style.color = 'white';
                            label.style.borderColor = '#1A2B49';
                            plus.textContent = '✓';
                            selectedTags[tag.tag_id] = tag.tag_name;
                        } else {
                            label.style.background = 'white';
                            label.style.color = '#1A2B49';
                            label.style.borderColor = '#1A2B49';
                            plus.textContent = '+';
                            delete selectedTags[tag.tag_id];
                        }
                        renderSelectedTags();
                    });

                    box.appendChild(label);
                });
            });
    });
</script>