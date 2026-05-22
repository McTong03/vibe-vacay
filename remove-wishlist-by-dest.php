<?php
session_start();
require 'conn.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit;
}

$user_id        = $_SESSION['user_id'];
$destination_id = (int)($_POST['destination_id'] ?? 0);

if (!$destination_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid destination.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND destination_id = ?");
$stmt->bind_param("ii", $user_id, $destination_id);
$stmt->execute();

echo json_encode(['success' => true]);