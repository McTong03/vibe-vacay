<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'conn.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }


$roleFilter = $_GET['user_role'] ?? '';
$keyword    = trim($_GET['q'] ?? '');

$sql = "
SELECT 
  u.user_id,
  u.user_name,
  u.user_email,
  u.user_password,
  u.user_role,
  COALESCE(p.profile_picture, 'Image/defaultProfile.png') AS profile_picture
FROM users u
LEFT JOIN user_profile p ON p.user_id = u.user_id
";

$where  = [];
$params = [];
$types  = '';

if ($roleFilter !== '' && $roleFilter !== 'ALL') {
  $roleMap = [
    'admin'          => 'Admin',
    'user/traveller' => 'User/traveller'
  ];
  $role_std = isset($roleMap[strtolower($roleFilter)]) ? $roleMap[strtolower($roleFilter)] : $roleFilter;

  $where[]  = 'u.user_role = ?';
  $params[] = $role_std;
  $types   .= 's';
}
if ($keyword !== '') {
  $where[]  = '(u.user_name LIKE ?)';
  $kw       = '%' . $keyword . '%';
  $params[] = $kw;
  $types   .= 's';
}
if ($where) {
  $sql .= ' WHERE ' . implode(' AND ', $where);
}

$stmt = $conn->prepare($sql);
if ($params) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/user-management.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
 
    </style>
</head>

<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <div class="second-part">
        <div class="manage-container">
            <p class="manage">Manage Users</p>
        </div>
        <div class="admin-container">
            <p class="admin">Admin / Manage Users</p>
        </div>
        <div>
            <button class="add-button" onclick="window.location.href='add-user.php'">+ Add New User</button>
        </div>
    </div>

    <div class="filter-box">
        <form method="GET">
            <div class="all-role-container">
                <select class="role-drop" name="user_role" onchange="this.form.submit()">
                    <option value="ALL" <?php echo ($roleFilter == '' || $roleFilter == 'ALL')? 'selected' : ''; ?>>All Roles</option>
                    <option value="admin" <?php echo (strtolower($roleFilter) == 'admin')? 'selected' : ''; ?>>Admin</option>
                    <option value="user/traveller" <?php echo (strtolower($roleFilter) == 'user/traveller') ? 'selected' : ''; ?>>User/Traveller</option>
                </select>
            </div>

            <div class="search-container">
                <input id="search-input" type="text" name="q" placeholder="Search by name"
                    value="<?php echo htmlspecialchars($keyword); ?>">
                <span id="clear-search" title="Clear">&#x2715;</span>
                <button type="submit" class="search-button">Search</button>
            </div>
        </form>
    </div>

    <div class="user-container">
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
                <div class="user-box-container">
                    <div class="profile-picture1">
                        <img class="profile-picture"
                            src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'Image/defaultProfile.png'; ?>">
                    </div>

                    <div class="id">
                        <p class="user-id">ID: <?php echo htmlspecialchars($user['user_id']); ?></p>
                    </div>

                    <div class="name">
                        <p class="user-name"><?php echo htmlspecialchars($user['user_name']); ?></p>
                    </div>

                    <div class="password">
                        <p class="user-password">••••••</p>
                    </div>

                    <div class="email">
                        <p class="user-email"><?php echo htmlspecialchars($user['user_email']); ?></p>
                    </div>

                    <div class="user-roles">
                        <p class="user-role"><?php echo htmlspecialchars($user['user_role']); ?></p>
                    </div>

                    <div class="edit-container">
                        <img class="edit-but" src="icon/edit.png">
                        <button class="edit-button"
                            onclick="window.location.href='edit-user.php?id=<?php echo $user['user_id']; ?>'">Edit</button>
                    </div>

                    <div class="delete-container">
                        <img class="delete-but" src="icon/delete.png">
                        <button class="delete-button"
                            onclick="if(confirm('Are you sure you want to delete this user?')) 
                            window.location.href='delete-user.php?id=<?php echo $user['user_id']; ?>'">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:white; margin-left:100px;">No users found.</p>
        <?php endif; ?>
    </div>

    <script>
        (function() {
            var input    = document.getElementById('search-input');
            var clearBtn = document.getElementById('clear-search');
            if (!input || !clearBtn) return;

            function toggleClearBtn() {
                clearBtn.style.display = input.value.trim() !== '' ? 'inline' : 'none';
            }

            toggleClearBtn();

            clearBtn.addEventListener('click', function() {
                input.value = '';
                toggleClearBtn();
                input.closest('form').submit();
            });

            input.addEventListener('input', toggleClearBtn);
        })();
    </script>

</body>

</html>