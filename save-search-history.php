<?php
session_start();
require 'conn.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId  = $_SESSION['user_id'];
$keyword = trim($_POST['keyword'] ?? '');

if (empty($keyword)) {
    echo json_encode(['success' => false]);
    exit;
}

// ✅ 只存 user_id 和 keyword，不存 state_id
$stmt = $conn->prepare("DELETE FROM search_history WHERE user_id = ? AND keyword = ?");
$stmt->bind_param("is", $userId, $keyword);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO search_history (user_id, keyword) VALUES (?, ?)");
$stmt->bind_param("is", $userId, $keyword);
$stmt->execute();

echo json_encode(['success' => true]);