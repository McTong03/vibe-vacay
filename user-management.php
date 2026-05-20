<?php
require 'conn.php';
session_start();


$roleFilter = $_GET['user_role'] ?? '';
$keyword    = trim($_GET['q'] ?? '');

$sql = "
SELECT 
  u.user_id,
  u.user_name,
  u.user_email,
  u.user_password,
  u.user_role,
  COALESCE(p.profile_picture, 'Image/default-profile.jpg') AS profile_picture
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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Cherry+Bomb+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Titan+One&display=swap');

        body {
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)),
                url("image/background.png");
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            background-color: black;
        }

        #header {
            background-color: #1A2B49;
            height: 55px;
            border-radius: 50px;
            margin-left: 90px;
            margin-right: 20px;
            margin-top: 25px;
            width: calc(100% - 180px);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin-top: -3px;
            margin-left: 30px;
        }

        .logo-name,
        .home,
        .destination-management,
        .statistic,
        .user-management,
        .logout,
        .profile {
            font-size: 17px;
            font-weight: bold;
        }

        .logo-name {
            margin-top: -47px;
            margin-left: 100px;
            color: white;
        }

        .home {
            margin-top: -37px;
            margin-left: 380px;
            color: white;
        }

        .destination-management {
            margin-top: -37px;
            margin-left: 510px;
            color: white;
        }

        .statistic {
            margin-top: -37px;
            margin-left: 770px;
            color: white;
        }

        .user-management {
            margin-top: -37px;
            margin-left: 930px;
            color: white;
        }

        .logout {
            margin-top: -37px;
            margin-left: 1180px;
            color: white;
        }

        .profile-box {
            background-color: white;
            width: 160px;
            height: 35px;
            margin-top: -46px;
            margin-left: 1280px;
            border-radius: 30px;
        }

        .profile {
            padding-top: 8px;
            margin-left: 35px;
        }

        .profile-icon {
            width: 30px;
            height: 30px;
            border-radius: 60px;
            margin-left: 100px;
            position: relative;
            top: -42px;
            left: 5px;
        }

        .manage {
            color: white;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin-top: 60px;
            font-size: 45px;
            margin-left: 150px;
        }

        .admin {
            color: white;
            margin-left: 150px;
            font-size: 18px;
            margin-top: -25px;
        }

        .add-button {
            margin-top: -80px;
            margin-left: 750px;
            width: 250px;
            height: 50px;
            position: absolute;
            font-size: 20px;
            background-color: #0064CE;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 8px;
        }

        .filter-box {
            background-color: #C7D0EC;
            width: 900px;
            height: 65px;
            margin-left: 150px;
            border-radius: 15px;
            margin-top: 40px;
        }

        .all-role-container select {
            top: 10px;
            left: 70px;
            width: 230px;
            height: 45px;
            font-size: 16px;
            padding-left: 20px;
        }

        .all-role-container select,
        .search-container {
            background-color: #828A98;
            position: relative;
            border-radius: 8px;
            color: white;
            cursor: pointer;
        }

        .search-container {
            display: flex;
            align-items: center;
            border: 1px solid #828A98;
            padding: 6px 6px 6px 18px;
            gap: 8px;
            max-width: 300px;
            height: 30px;
            transition: border-color 0.15s, box-shadow 0.15s;
            margin-left: 500px;
            margin-top: -35px;
        }

        #search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 16px;
            color: white;
        }

        #search-input::placeholder {
            font-size: 16px;
            color: white;
            padding-left: 15px;
        }

        /* Clear (X) button — hidden by default */
        #clear-search {
            color: white;
            font-size: 16px;
            cursor: pointer;
            display: none;
            user-select: none;
            padding: 0 4px;
            line-height: 1;
        }

        .search-button {
            width: 110px;
            height: 35px;
            font-size: 16px;
            margin-left: 180px;
            position: absolute;
            cursor: pointer;
        }

        /* ── User Cards ── */
        .user-container {
            display: grid;
            grid-template-columns: repeat(5, 290px);
            padding: 30px 50px;
            row-gap: 10px;
        }

        .user-box-container {
            width: 270px;
            height: 370px;
            background-color: #EAE7E7;
            margin-top: 30px;
            border-radius: 20px;
            margin-left: -5px;
        }

        .profile-picture1 {
            width: 270px;
            height: 100px;
            background-color: #CFD4E0;
            border-radius: 20px;
        }

        .profile-picture {
            border-radius: 80px;
            width: 70px;
            margin-top: 15px;
            margin-left: 100px;
        }

        .user-id,
        .user-name,
        .user-email,
        .user-password,
        .user-role {
            font-size: 14px;
            padding-left: 35px;
        }

        .user-id {
            margin-top: 20px;
        }

        .user-roles {
            background-color: #B3B6C3;
            width: fit-content;
            height: 33px;
            border-radius: 20px;
            margin-left: 28px;
            padding: 0 12px;
            white-space: nowrap;
        }

        .user-role {
            padding-top: 7px;
            margin-left: 0;
            padding-left: 0;
        }

        .edit-button {
            margin-left: -3px;
            margin-top: 25px;
            padding-left: 28px;
        }

        .edit-button,
        .delete-button {
            width: 95px;
            height: 40px;
            font-size: 14px;
            background-color: #B3B6C3;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .edit-but,
        .delete-but {
            width: 20px;
            position: relative;
            top: 5px;
        }

        .delete-button {
            margin-left: -26px;
            margin-top: 35px;
            padding-left: 38px;
        }

        .delete-but { left: 11px; }
        .edit-but   { left: 35px; }

        .delete-container {
            margin-top: -75px;
            margin-left: 140px;
        }

        /* ── Delete Modal Overlay ── */
        #deleteModal {
            display: none;              /* hidden until confirmDelete() is called */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
        }

        /* ── Modal Box — always perfectly centered ── */
        .modal-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            width: 320px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
        }

        .modal-box h3 {
            margin-bottom: 10px;
            font-size: 20px;
        }

        .modal-box p {
            color: #666;
            margin-bottom: 25px;
        }

        .modal-confirm {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }

        .modal-confirm:hover { background: #c0392b; }

        .modal-cancel {
            background: #828A98;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .modal-cancel:hover { background: #6b7280; }
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
                            src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'Image/default-profile.jpg'; ?>">
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