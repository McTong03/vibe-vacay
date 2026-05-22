<?php
require 'conn.php';
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$destination_id = intval($_POST['destination_id'] ?? 0);
$rating         = intval($_POST['rating'] ?? 0);
$comment        = trim($_POST['comment'] ?? '');
$image_url      = '';

if ($destination_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit();
}

// Handle image upload
if (!empty($_FILES['images']['name'][0])) {
    $uploadDir = __DIR__ . '/image/reviews/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $urls = [];
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($_FILES['images']['error'][$i] === 0) {
            $filename = time() . '_' . $i . '_' . basename($_FILES['images']['name'][$i]);
            $target   = $uploadDir . $filename;
            if (move_uploaded_file($tmp, $target)) {
                $urls[] = 'image/reviews/' . $filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'move_uploaded_file failed']);
                exit();
            }
        } else {
            // ← THIS is where the file too large message goes
            $errorMsg = 'File upload failed.';
            if ($_FILES['images']['error'][$i] === 1 || $_FILES['images']['error'][$i] === 2) {
                $errorMsg = 'The file is too large. Please upload a smaller image.';
            }
            echo json_encode(['success' => false, 'message' => $errorMsg]);
            exit();
        }
    }
    $image_url = implode(',', $urls);
}

$stmt = $conn->prepare("
    INSERT INTO reviews (user_id, destination_id, rating, comment, image_url, created_at)
    VALUES (?, ?, ?, ?, ?, CURDATE())
");
$stmt->bind_param("iiiss", $user_id, $destination_id, $rating, $comment, $image_url);

if ($stmt->execute()) {
    $review_id = $stmt->insert_id;
    $stmt->close();

    $stmt2 = $conn->prepare("
        SELECT r.review_id, r.rating, r.comment, r.image_url,
               u.user_name,
               COALESCE(p.profile_picture, 'image/default-profile.jpg') AS profile_picture
        FROM reviews r
        JOIN users u ON u.user_id = r.user_id
        LEFT JOIN user_profile p ON p.user_id = r.user_id
        WHERE r.review_id = ?
    ");
    $stmt2->bind_param("i", $review_id);
    $stmt2->execute();
    $row = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();

    $conn->query("
        UPDATE destinations
        SET reviews_count = reviews_count + 1,
            average_rating = (
                SELECT AVG(rating) FROM reviews WHERE destination_id = $destination_id
            )
        WHERE destination_id = $destination_id
    ");

    echo json_encode(['success' => true, 'review' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save review']);
}
?>