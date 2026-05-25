<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';


// ── ADD ──
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['tag_type_name']);
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO tag_type (tag_type_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: tagging-type-management.php");
    exit();
}

// ── EDIT ──
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['tag_type_id']);
    $name = trim($_POST['tag_type_name']);
    if ($name !== '' && $id > 0) {
        $stmt = $conn->prepare("UPDATE tag_type SET tag_type_name = ? WHERE tag_type_id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: tagging-type-management.php");
    exit();
}

// ── DELETE ──
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['tag_type_id']);
    if ($id > 0) {
        // Delete all tags belonging to this tag type first
        $stmt = $conn->prepare("DELETE FROM destination_tags WHERE tag_type_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Then delete the tag type itself
        $stmt = $conn->prepare("DELETE FROM tag_type WHERE tag_type_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: tagging-type-management.php");
    exit();
}

// ── SEARCH + FETCH ──
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT tag_type_id, tag_type_name FROM tag_type WHERE tag_type_name LIKE ? ORDER BY tag_type_id ASC");
    $stmt->bind_param("s", $like);
} else {
    $stmt = $conn->prepare("SELECT tag_type_id, tag_type_name FROM tag_type ORDER BY tag_type_id ASC");
}
$stmt->execute();
$result = $stmt->get_result();
$tags = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagging Type Management Page</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/tagging-type-management.css">
</head>

<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <!-- TITLE -->
    <div class="title">
        <img src="icon/tag.png" class="title-icon" alt="Tagging Type">
        <h1>Tagging Type Management</h1>
    </div>

    <!-- CONTENT -->
    <div class="content-container">
        <div class="container">

            <!-- SEARCH FORM -->
            <form method="GET" action="tagging-type-management.php">
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Search tagging type..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Search</button>
                </div>
            </form>

            <!-- TAG ROWS FROM DATABASE -->
            <?php if (count($tags) === 0): ?>
                <p class="no-results">No tagging types found.</p>
            <?php else: ?>
                <?php foreach ($tags as $tag): ?>
                    <div class="tag-type">
                        <h3><?php echo htmlspecialchars($tag['tag_type_name']); ?></h3>
                        <div class="edit_delete_box">
                            <button type="button" class="view_Btn"
                                onclick="window.location.href='tagging-management.php?id=<?php echo $tag['tag_type_id']; ?>'">
                                <img src="icon/view.png" class="feature-icon" alt="view"> View Tagging
                            </button>
                            <button type="button" class="edit_Btn"
                                onclick="openEditModal(<?php echo $tag['tag_type_id']; ?>, '<?php echo addslashes(htmlspecialchars($tag['tag_type_name'])); ?>')">
                                <img src="icon/edit.png" class="feature-icon" alt="edit"> Edit
                            </button>
                            <button type="button" class="delete_Btn"
                                onclick="openDeleteModal(<?php echo $tag['tag_type_id']; ?>, '<?php echo addslashes(htmlspecialchars($tag['tag_type_name'])); ?>')">
                                <img src="icon/delete.png" class="feature-icon" alt="delete"> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <button type="button" class="add_Btn" onclick="openAddModal()">+ Add Tagging Type</button>
    </div>


    <!-- ══ ADD MODAL ══ -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal('addModal')">✕</button>
            <div class="modal-header">
                <img src="icon/tag.png" class="modal-title-icon" alt="tag">
                <h2>Add Tagging Type</h2>
            </div>
            <form method="POST" action="tagging-type-management.php">
                <input type="hidden" name="action" value="add">
                <label class="modal-label" for="addInput">Tagging Type / Name</label>
                <input type="text" id="addInput" name="tag_type_name" class="modal-input"
                    placeholder="Enter tagging type name" required>
                <div class="modal-actions">
                    <button type="reset" class="modal-btn modal-btn-reset">Reset</button>
                    <button type="submit" class="modal-btn modal-btn-confirm">Add Tagging Type</button>
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
                <h2>Edit Tagging Type</h2>
            </div>
            <form method="POST" action="tagging-type-management.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="tag_type_id" id="editId">
                <label class="modal-label" for="editInput">Tagging Type / Name</label>
                <input type="text" id="editInput" name="tag_type_name" class="modal-input" placeholder="Enter new name"
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
                <h2>Delete Tagging Type</h2>
            </div>
            <p class="modal-delete-msg">Are you sure you want to delete:</p>
            <p class="modal-delete-name" id="deleteTagName"></p>
            <form method="POST" action="tagging-type-management.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="tag_type_id" id="deleteId">
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