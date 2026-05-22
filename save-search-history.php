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

// 删掉重复的
$stmt = $conn->prepare("DELETE FROM search_history WHERE user_id = ? AND keyword = ?");
$stmt->bind_param("is", $userId, $keyword);
$stmt->execute();

// state_id 给默认值 0 或者你 table 允许的值
$stateId = 0;
$stmt = $conn->prepare("INSERT INTO search_history (user_id, keyword, state_id) VALUES (?, ?, ?)");
$stmt->bind_param("isi", $userId, $keyword, $stateId);
$stmt->execute();

echo json_encode(['success' => true]);