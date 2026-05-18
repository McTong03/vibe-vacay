<?php
session_start();
require 'conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id      = $_SESSION['user_id'];
$favourite_id = intval($_POST['favourite_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM favorites WHERE favourite_id = ? AND user_id = ?");
$stmt->bind_param("ii", $favourite_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove']);
}
$stmt->close();
?>