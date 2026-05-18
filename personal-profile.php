<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
 
        <div class="profile-card">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-input" value=""/>
            </div>
 
            <div class="form-group">
                <label class="form-label">Profile Picture</label>
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <input type="file" id="fileInput" accept="image/*" style="display:none" onchange="previewImage(event)" />
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <i class="ph-bold ph-image upload-icon"></i>
                        <p class="upload-text">Click or drag image here</p>
                    </div>
                    <img id="previewImg" class="preview-img" style="display:none" alt="Preview" />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="" />
            </div>
 
            <div class="form-group">
                <label class="form-label">
                    Password <em>(Leave empty if you do not wish to change)</em>
                </label>
                <div class="password-wrap">
                    <input type="password" class="form-input" id="passwordInput" value="" />
                    <i class="ph-bold ph-eye toggle-pw" onclick="togglePassword('passwordInput', this)"></i>
                </div>
            </div>
 
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <div class="password-wrap">
                    <input type="password" class="form-input" id="confirmInput" placeholder="" />
                    <i class="ph-bold ph-eye toggle-pw" onclick="togglePassword('confirmInput', this)"></i>
                </div>
            </div>
 
            <div class="form-actions">
                <button class="btn-cancel" onclick="history.back()">Cancel</button>
                <button class="btn-save" onclick="saveProfile()">Save</button>
            </div>
 
        </div>
    </div>
 
    <script>
        // Toggle password visibility
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
 
        // Preview uploaded image
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
 
        // Drag and drop support
        const uploadArea = document.getElementById('uploadArea');
        uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
        uploadArea.addEventListener('drop', e => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                document.getElementById('fileInput').files = e.dataTransfer.files;
                previewImage({ target: { files: [file] } });
            }
        });
 
        // Save button
        function saveProfile() {
            const btn = document.querySelector('.btn-save');
            btn.textContent = 'Saving...';
            btn.disabled = true;
            setTimeout(() => { btn.textContent = 'Save'; btn.disabled = false; }, 1500);
        }
    </script>
</body>
</html>