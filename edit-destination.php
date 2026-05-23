<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("conn.php");

// ── AJAX: return tags by tag_type_id ──
if (isset($_GET['get_tags'])) {
    $tid = intval($_GET['get_tags']);
    $result = $conn->query("SELECT tag_id, tag_name FROM destination_tags WHERE tag_type_id = $tid ORDER BY tag_name");
    $tags = [];
    while ($row = $result->fetch_assoc()) $tags[] = $row;
    header('Content-Type: application/json');
    echo json_encode($tags);
    exit();
}

// ── Get destination_id ──
$destination_id = intval($_GET['id'] ?? 0);
if ($destination_id <= 0) {
    header("Location: destination-management.php");
    exit();
}

// ── Fetch existing destination data ──
$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$dest = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dest) {
    header("Location: destination-management.php");
    exit();
}

// ── Fetch existing tags for this destination ──
$existingTags = [];
$stmt = $conn->prepare("
    SELECT dt.tag_id, dt.tag_name 
    FROM destination_tag_mapping dtm
    JOIN destination_tags dt ON dtm.tag_id = dt.tag_id
    WHERE dtm.destination_id = ?
");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $existingTags[$row['tag_id']] = $row['tag_name'];
$stmt->close();

// ── Fetch states ──
$states = [];
$res = $conn->query("SELECT state_id, state_name FROM states ORDER BY state_name");
while ($row = $res->fetch_assoc()) $states[] = $row;

// ── Fetch tag types ──
$tagTypes = [];
$res = $conn->query("SELECT tag_type_id, tag_type_name FROM tag_type ORDER BY tag_type_name");
while ($row = $res->fetch_assoc()) $tagTypes[] = $row;

// ── Handle form submission ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-edit'])) {

    $destination_name = trim($_POST['destination_name']);
    $state_id         = intval($_POST['state_id']);
    $description      = trim($_POST['description']);
    $phone_number     = trim($_POST['phone_number']);
    $average_rating   = floatval($_POST['average_rating']);
    $price            = floatval($_POST['price']);

    // Handle image — keep old if no new file uploaded
    $image_url = $dest['image_url'];
    if (!empty($_POST['image_url'])) {
        $image_url = trim($_POST['image_url']);
    }

    // Update destinations table
    $stmt = $conn->prepare("UPDATE destinations SET 
        state_id=?, destination_name=?, description=?, image_url=?, 
        phone_number=?, average_rating=?, price=?
        WHERE destination_id=?");
    $stmt->bind_param("issssddi", $state_id, $destination_name, $description, $image_url, $phone_number, $average_rating, $price, $destination_id);
    $stmt->execute();
    $stmt->close();

    // Delete old tag mappings then re-insert
    $stmt = $conn->prepare("DELETE FROM destination_tag_mapping WHERE destination_id = ?");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $stmt->close();

    if (!empty($_POST['tags'])) {
        $stmt = $conn->prepare("INSERT INTO destination_tag_mapping (destination_id, tag_id) VALUES (?, ?)");
        foreach ($_POST['tags'] as $tag_id) {
            $tag_id = intval($tag_id);
            if ($tag_id > 0) {
                $stmt->bind_param("ii", $destination_id, $tag_id);
                $stmt->execute();
            }
        }
        $stmt->close();
    }

    mysqli_close($conn);
    echo '<script>alert("Destination updated successfully!"); window.location.href="destination-management.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destination Page</title>
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
        <h1>Edit Destination</h1>
    </div>

    <div class="content-container">
        <form class="container" method="POST" action="edit-destination.php?id=<?= $destination_id ?>">

            <h3>Destination Name</h3>
            <input type="text" name="destination_name" class="tag-type"
                value="<?= htmlspecialchars($dest['destination_name']) ?>" required>

            <h3>Destination Picture URL</h3>
            <input type="text" name="image_url" class="tag-type"
                value="<?= htmlspecialchars($dest['image_url']) ?>"
                placeholder="Enter destination picture URL">

            <h3>Destination State</h3>
            <div class="filter-bar">
                <select name="state_id" required>
                    <option value="">Please Select</option>
                    <?php foreach ($states as $s): ?>
                        <option value="<?= $s['state_id'] ?>"
                            <?= $s['state_id'] == $dest['state_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['state_name']) ?>
                        </option>
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
            <div id="selectedTagsContainer" style="margin-top:0.75rem;">
                <p style="font-size:0.8rem; color:#6b7280; margin-bottom:0.5rem;">Selected tags:</p>
                <div id="selectedTagsDisplay"></div>
            </div>

            <h3>Destination Price (RM)</h3>
            <input type="number" step="0.01" name="price" class="tag-type"
                value="<?= htmlspecialchars($dest['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                
            <h3>Destination Rating (/5)</h3>
            <input type="number" step="0.1" min="0" max="5" name="average_rating" class="tag-type"
                value="<?= htmlspecialchars($dest['average_rating'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <h3>Destination Phone Number</h3>
            <input type="text" name="phone_number" class="tag-type"
                value="<?= htmlspecialchars($dest['phone_number']) ?>"
                placeholder="01x-xxxxxxx">

            <h3>Destination Description</h3>
            <textarea name="description" class="tag-type description-box"><?= htmlspecialchars($dest['description']) ?></textarea>

            <div class="filter-actions">
                <button type="reset" class="btn btn-reset">Reset</button>
                <button type="submit" name="btn-edit" class="btn btn-add">Edit Destination</button>
            </div>

        </form>
    </div>

    <!-- Pass existing tags to JS -->
    <script>
        const selectedTags = <?= json_encode($existingTags) ?>;

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
                    display: inline-flex; align-items: center; gap: 6px;
                    background: #1A2B49; color: white; border-radius: 30px;
                    padding: 0.4rem 1rem; font-size: 0.85rem; font-weight: 500;
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

            fetch('edit-destination.php?get_tags=' + tid + '&id=<?= $destination_id ?>')
                .then(res => res.json())
                .then(tags => {
                    box.innerHTML = '';
                    if (tags.length === 0) {
                        box.innerHTML = '<span style="color:#6b7280;font-size:0.88rem;">No tags found</span>';
                        return;
                    }
                    tags.forEach(tag => {
                        const label = document.createElement('label');
                        label.style.cssText = `
                            display: inline-flex; align-items: center; gap: 6px;
                            background: white; border: 2px solid #1A2B49; border-radius: 30px;
                            padding: 0.4rem 1rem; font-size: 0.88rem; font-weight: 500;
                            cursor: pointer; color: #1A2B49; transition: all 0.15s; user-select: none;
                        `;

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'tags_check[]';
                        checkbox.value = tag.tag_id;
                        checkbox.style.display = 'none';

                        const plus = document.createElement('span');
                        plus.className = 'plus-icon';
                        plus.style.cssText = 'font-size:1rem; font-weight:700; line-height:1;';

                        const tagName = document.createElement('span');
                        tagName.textContent = tag.tag_name;

                        // Pre-check if already selected
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

        // Render existing tags on page load
        renderSelectedTags();
    </script>
</body>
</html>