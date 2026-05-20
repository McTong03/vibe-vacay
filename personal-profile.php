<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

include 'conn.php';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg   = '';


// HANDLE FORM SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_username = trim($_POST['username'] ?? '');
    $new_email    = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm_pw   = $_POST['confirm_password'] ?? '';

    // --- Validate ---
    if (empty($new_username) || empty($new_email)) {
        $error_msg = "Username and email cannot be empty.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } elseif (!empty($new_password) && $new_password !== $confirm_pw) {
        $error_msg = "Passwords do not match.";
    } else {

        // --- Update users table ---
        if (!empty($new_password)) {
            // Update with new hashed password
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE users SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?"
            );
            mysqli_stmt_bind_param($stmt, "sssi", $new_username, $new_email, $hashed, $user_id);
        } else {
            // Update without changing password
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE users SET user_name = ?, user_email = ? WHERE user_id = ?"
            );
            mysqli_stmt_bind_param($stmt, "ssi", $new_username, $new_email, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // --- Handle profile picture upload ---
        if (!empty($_FILES['profile_picture']['name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['profile_picture']['type'];

            if (!in_array($file_type, $allowed)) {
                $error_msg = "Only JPG, PNG, GIF, and WEBP images are allowed.";
            } else {
                $ext       = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                $filename  = "profile_" . $user_id . "_" . time() . "." . $ext;
                $upload_path = "Image/" . $filename;

                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                    // Check if user_profile row exists
                    $check = mysqli_prepare(
                        $conn,
                        "SELECT profile_id FROM user_profile WHERE user_id = ?"
                    );
                    mysqli_stmt_bind_param($check, "i", $user_id);
                    mysqli_stmt_execute($check);
                    $result = mysqli_stmt_get_result($check);

                    if (mysqli_num_rows($result) > 0) {
                        // Update existing
                        $upd = mysqli_prepare(
                            $conn,
                            "UPDATE user_profile SET profile_picture = ? WHERE user_id = ?"
                        );
                        mysqli_stmt_bind_param($upd, "si", $upload_path, $user_id);
                        mysqli_stmt_execute($upd);
                        mysqli_stmt_close($upd);
                    } else {
                        // Insert new row
                        $ins = mysqli_prepare(
                            $conn,
                            "INSERT INTO user_profile (user_id, profile_picture) VALUES (?, ?)"
                        );
                        mysqli_stmt_bind_param($ins, "is", $user_id, $upload_path);
                        mysqli_stmt_execute($ins);
                        mysqli_stmt_close($ins);
                    }
                    mysqli_stmt_close($check);
                } else {
                    $error_msg = "Failed to upload image. Please try again.";
                }
            }
        }

        if (empty($error_msg)) {
            // Update session username in case it changed
            $_SESSION['user_name'] = $new_username;
            $success_msg = "Profile updated successfully!";
        }
    }
}


// FETCH CURRENT USER DATA
$stmt = mysqli_prepare(
    $conn,
    "SELECT u.user_name, u.user_email, up.profile_picture
     FROM users u
     LEFT JOIN user_profile up ON u.user_id = up.user_id
     WHERE u.user_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

mysqli_close($conn);

// Fallback defaults
$username        = $user['user_name']      ?? '';
$email           = $user['user_email']     ?? '';
$profile_picture = $user['profile_picture'] ?? 'Image/defaultProfile.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Profile</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/personal-profile.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <?php include('./includes/navbar.php'); ?>

    <div class="main-content">

        <div class="page-title-block">
            <h1 class="page-title">Personal Profile</h1>
            <p class="breadcrumb">User / Personal Profile</p>
        </div>

        <!-- Success / Error messages -->
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <!-- enctype required for file upload -->
        <form method="POST" enctype="multipart/form-data">
            <div class="profile-card">

                <!-- Username -->
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input"
                        value="<?php echo htmlspecialchars($username); ?>" required />
                </div>

                <!-- Profile Picture -->
                <div class="form-group">
                    <label class="form-label">Profile Picture</label>
                    <div class="upload-area" id="uploadArea"
                        onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" name="profile_picture"
                            accept="image/*" style="display:none"
                            onchange="previewImage(event)" />

                        <!-- Show existing picture or placeholder -->
                        <?php if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])): ?>
                            <img id="previewImg" class="preview-img"
                                src="<?php echo htmlspecialchars($profile_picture); ?>"
                                alt="Profile Picture" />
                            <div class="upload-placeholder" id="uploadPlaceholder" style="display:none">
                                <i class="ph-bold ph-image upload-icon"></i>
                                <p class="upload-text">Click or drag image here</p>
                            </div>
                        <?php else: ?>
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="ph-bold ph-image upload-icon"></i>
                                <p class="upload-text">Click or drag image here</p>
                            </div>
                            <img id="previewImg" class="preview-img" style="display:none" alt="Preview" />
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input"
                        value="<?php echo htmlspecialchars($email); ?>" required />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label">
                        Password <em>(Leave empty if you do not wish to change)</em>
                    </label>
                    <div class="password-wrap">
                        <input type="password" name="password" class="form-input"
                            id="passwordInput" placeholder="Enter new password" />
                        <i class="ph-bold ph-eye toggle-pw"
                            onclick="togglePassword('passwordInput', this)"></i>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="password-wrap">
                        <input type="password" name="confirm_password" class="form-input"
                            id="confirmInput" placeholder="Re-enter new password" />
                        <i class="ph-bold ph-eye toggle-pw"
                            onclick="togglePassword('confirmInput', this)"></i>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>

            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewImg');
                const placeholder = document.getElementById('uploadPlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        const uploadArea = document.getElementById('uploadArea');
        uploadArea.addEventListener('dragover', e => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
        uploadArea.addEventListener('drop', e => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                document.getElementById('fileInput').files = e.dataTransfer.files;
                previewImage({
                    target: {
                        files: [file]
                    }
                });
            }
        });
    </script>
</body>

</html>