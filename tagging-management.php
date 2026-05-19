<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

// Get tag_type_id from URL (passed from tagging-type-management.php)
$tag_type_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ── ADD ──
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $tag_name = trim($_POST['tag_name']);
    $tid = intval($_POST['tag_type_id']);
    if ($tag_name !== '' && $tid > 0) {
        $stmt = $conn->prepare("INSERT INTO destination_tags (tag_type_id, tag_name) VALUES (?, ?)");
        $stmt->bind_param("is", $tid, $tag_name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: tagging-management.php?id=" . $tid);
    exit();
}

// ── EDIT ──
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $tag_id = intval($_POST['tag_id']);
    $tag_name = trim($_POST['tag_name']);
    $tid = intval($_POST['tag_type_id']);
    if ($tag_name !== '' && $tag_id > 0) {
        $stmt = $conn->prepare("UPDATE destination_tags SET tag_name = ? WHERE tag_id = ?");
        $stmt->bind_param("si", $tag_name, $tag_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: tagging-management.php?id=" . $tid);
    exit();
}

// ── DELETE ──
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $tag_id = intval($_POST['tag_id']);
    $tid = intval($_POST['tag_type_id']);
    if ($tag_id > 0) {
        $stmt = $conn->prepare("DELETE FROM destination_tags WHERE tag_id = ?");
        $stmt->bind_param("i", $tag_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: tagging-management.php?id=" . $tid);
    exit();
}

// ── FETCH TAG TYPE NAME ──
$tag_type_name = '';
if ($tag_type_id > 0) {
    $stmt = $conn->prepare("SELECT tag_type_name FROM tag_type WHERE tag_type_id = ?");
    $stmt->bind_param("i", $tag_type_id);
    $stmt->execute();
    $stmt->bind_result($tag_type_name);
    $stmt->fetch();
    $stmt->close();
}

// ── SEARCH + FETCH TAGS ──
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($tag_type_id > 0) {
    if ($search !== '') {
        $like = "%" . $search . "%";
        $stmt = $conn->prepare("SELECT tag_id, tag_name FROM destination_tags WHERE tag_type_id = ? AND tag_name LIKE ? ORDER BY tag_id ASC");
        $stmt->bind_param("is", $tag_type_id, $like);
    } else {
        $stmt = $conn->prepare("SELECT tag_id, tag_name FROM destination_tags WHERE tag_type_id = ? ORDER BY tag_id ASC");
        $stmt->bind_param("i", $tag_type_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $tags = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $tags = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagging Management Page</title>
    <link rel="stylesheet" href="css/menubar.css">
</head>
<style>
    body {
        min-height: 800px;
        margin: 0;
        font-family: system-ui, sans-serif;
    }

    /* Search */
    .search-bar {
        background-color: #1A2B49;
        padding: 0.5rem;
        padding-left: 1.5rem;
        border-radius: 40px;
        display: flex;
        width: 600px;
        justify-content: center;
        align-items: center;
        margin-bottom: 10px;
        margin-left: auto;
        margin-right: auto;
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

    /* Content */
    .content-container {
        margin-top: 20px;
        margin-left: 150px;
        background-color: #21375d;
        width: 1200px;
        padding: 30px;
        border-radius: 15px;
    }

    .tag-type {
        background-color: #F9F2F2;
        height: 60px;
        margin-top: 15px;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 20px;
        padding-right: 20px;
    }

    .tag-type h3 {
        margin: 0;
    }

    /* Title */
    .title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-left: 15px;
        margin-top: 20px;
    }

    .title-icon {
        width: 40px;
        height: 40px;
    }

    .title h1 {
        margin: 0;
        font-size: 30px;
    }

    /* Back button */
    .back_Btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background-color: #1A2B49;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .back-icon {
        width: 22px;
        height: 22px;
        filter: brightness(0) invert(1);
    }

    /* Tag type breadcrumb label */
    .tag-type-label {
        background-color: #0064CE;
        color: white;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }

    /* Buttons */
    .edit_delete_box {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .edit_Btn,
    .delete_Btn {
        background-color: #B3B6C3;
        color: black;
        border: none;
        padding: 0.6rem 2rem;
        border-radius: 30px;
        font-weight: bold;
        cursor: pointer;
        height: 35px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .feature-icon {
        width: 18px;
        height: 18px;
    }

    .add_Btn {
        background-color: #0064CE;
        color: white;
        width: 100%;
        height: 50px;
        margin-top: 20px;
        border: none;
        padding: 0.6rem 2rem;
        border-radius: 10px;
        font-weight: bold;
        font-size: larger;
        cursor: pointer;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .add_Btn:hover {
        background-color: #0053b0;
    }

    /* No results */
    .no-results {
        color: #ccd;
        text-align: center;
        padding: 30px 0;
        font-size: 16px;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 100;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.18s ease;
    }

    .modal-overlay.active {
        display: flex;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-box {
        background-color: #21375d;
        width: 520px;
        border-radius: 18px;
        padding: 36px 40px 32px;
        position: relative;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
        animation: slideUp 0.22s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .modal-header h2 {
        margin: 0;
        color: white;
        font-size: 22px;
    }

    .modal-header .modal-title-icon {
        width: 32px;
        height: 32px;
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 18px;
        background: none;
        border: none;
        color: #aac;
        font-size: 22px;
        cursor: pointer;
        line-height: 1;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .modal-label {
        color: white;
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 10px;
        display: block;
    }

    .modal-input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: none;
        padding: 0 18px;
        font-size: 15px;
        background-color: #F9F2F2;
        box-sizing: border-box;
        outline: none;
    }

    .modal-input:focus {
        box-shadow: 0 0 0 3px rgba(0, 100, 206, 0.45);
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 28px;
    }

    .modal-btn {
        padding: 0.6rem 2.2rem;
        border-radius: 10px;
        font-weight: bold;
        font-size: 0.95rem;
        cursor: pointer;
        min-width: 150px;
    }

    .modal-btn-reset {
        background-color: white;
        color: #1A2B49;
        border: none;
    }

    .modal-btn-reset:hover {
        background-color: #e8e8e8;
    }

    .modal-btn-confirm {
        background-color: #0064CE;
        color: white;
        border: none;
    }

    .modal-btn-confirm:hover {
        background-color: #0053b0;
    }

    /* Delete confirm modal */
    .modal-delete-msg {
        color: #f0d0d0;
        font-size: 15px;
        margin-bottom: 8px;
    }

    .modal-delete-name {
        color: white;
        font-weight: bold;
        font-size: 18px;
    }

    .modal-btn-danger {
        background-color: #c0392b;
        color: white;
        border: none;
    }

    .modal-btn-danger:hover {
        background-color: #a93226;
    }

    /* Modal sub-label (read-only info) */
    .modal-info-row {
        background-color: rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 8px 14px;
        margin-bottom: 18px;
        color: #b0c4de;
        font-size: 13px;
    }

    .modal-info-row span {
        color: white;
        font-weight: bold;
    }
</style>

<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <!-- TITLE -->
    <div class="title">
        <button type="button" class="back_Btn" onclick="window.location.href='tagging-type-management.php'">
            <img src="icon/error.png" class="back-icon" />
        </button>
        <img src="icon/tag.png" class="title-icon" alt="Tagging">
        <h1>Tagging Management</h1>
        <?php if ($tag_type_name !== ''): ?>
            <span class="tag-type-label"><?php echo htmlspecialchars($tag_type_name); ?></span>
        <?php endif; ?>
    </div>

    <!-- CONTENT -->
    <div class="content-container">
        <div class="container">

            <!-- SEARCH FORM -->
            <form method="GET" action="tagging-management.php">
                <input type="hidden" name="id" value="<?php echo $tag_type_id; ?>">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Search tagging..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Search</button>
                </div>
            </form>

            <!-- TAG ROWS FROM DATABASE -->
            <?php if (count($tags) === 0): ?>
                <p class="no-results">No tags
                    found<?php echo $search !== '' ? ' for "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
            <?php else: ?>
                <?php foreach ($tags as $tag): ?>
                    <div class="tag-type">
                        <h3><?php echo htmlspecialchars($tag['tag_name']); ?></h3>
                        <div class="edit_delete_box">
                            <button type="button" class="edit_Btn"
                                onclick="openEditModal(<?php echo $tag['tag_id']; ?>, '<?php echo addslashes(htmlspecialchars($tag['tag_name'])); ?>')">
                                <img src="icon/edit.png" class="feature-icon" alt="edit"> Edit
                            </button>
                            <button type="button" class="delete_Btn"
                                onclick="openDeleteModal(<?php echo $tag['tag_id']; ?>, '<?php echo addslashes(htmlspecialchars($tag['tag_name'])); ?>')">
                                <img src="icon/delete.png" class="feature-icon" alt="delete"> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <button type="button" class="add_Btn" onclick="openAddModal()">+ Add Tagging</button>
    </div>


    <!-- ══ ADD MODAL ══ -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('addModal')">✕</button>
            <div class="modal-header">
                <img src="icon/tag.png" class="modal-title-icon" alt="tag">
                <h2>Add Tagging</h2>
            </div>
            <div class="modal-info-row">
                Category: <span><?php echo htmlspecialchars($tag_type_name); ?></span>
            </div>
            <form method="POST" action="tagging-management.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="tag_type_id" value="<?php echo $tag_type_id; ?>">
                <label class="modal-label" for="addInput">Tag Name</label>
                <input type="text" id="addInput" name="tag_name" class="modal-input" placeholder="Enter tag name"
                    required>
                <div class="modal-actions">
                    <button type="reset" class="modal-btn modal-btn-reset">Reset</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Add Tagging</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ══ EDIT MODAL ══ -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('editModal')">✕</button>
            <div class="modal-header">
                <img src="icon/tag.png" class="modal-title-icon" alt="tag">
                <h2>Edit Tagging</h2>
            </div>
            <div class="modal-info-row">
                Category: <span><?php echo htmlspecialchars($tag_type_name); ?></span>
            </div>
            <form method="POST" action="tagging-management.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="tag_id" id="editId">
                <input type="hidden" name="tag_type_id" value="<?php echo $tag_type_id; ?>">
                <label class="modal-label" for="editInput">Tag Name</label>
                <input type="text" id="editInput" name="tag_name" class="modal-input" placeholder="Enter new tag name"
                    required>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-reset" id="editResetBtn">Reset</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ══ DELETE CONFIRM MODAL ══ -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('deleteModal')">✕</button>
            <div class="modal-header">
                <img src="icon/delete.png" class="modal-title-icon" alt="delete">
                <h2>Delete Tagging</h2>
            </div>
            <p class="modal-delete-msg">Are you sure you want to delete:</p>
            <p class="modal-delete-name" id="deleteTagName"></p>
            <form method="POST" action="tagging-management.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="tag_id" id="deleteId">
                <input type="hidden" name="tag_type_id" value="<?php echo $tag_type_id; ?>">
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-reset"
                        onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        let editOriginalName = '';

        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        function openEditModal(id, name) {
            editOriginalName = name;
            document.getElementById('editId').value = id;
            document.getElementById('editInput').value = name;
            document.getElementById('editResetBtn').onclick = () => {
                document.getElementById('editInput').value = editOriginalName;
            };
            document.getElementById('editModal').classList.add('active');
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteTagName').textContent = name;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Close when clicking backdrop
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', e => {
                if (e.target === overlay) overlay.classList.remove('active');
            });
        });
    </script>

</body>

</html>