<?php
session_start();
require 'conn.php';
 
header('Content-Type: application/json');
 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}
 
$user_id        = $_SESSION['user_id'];
$destination_id = intval($_POST['destination_id'] ?? 0);
$action         = $_POST['action'] ?? '';
 
if (!$destination_id || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}
 
if ($action === 'add') {
    // Avoid duplicates
    $check = $conn->prepare("SELECT favourite_id FROM favorites WHERE user_id = ? AND destination_id = ?");
    $check->bind_param("ii", $user_id, $destination_id);
    $check->execute();
    $check->store_result();
 
    if ($check->num_rows > 0) {
        $check->close();
        echo json_encode(['success' => true]);
        exit();
    }
    $check->close();
 
    $stmt = $conn->prepare("INSERT INTO favorites (user_id, destination_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $destination_id);
 
} else {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND destination_id = ?");
    $stmt->bind_param("ii", $user_id, $destination_id);
}
 
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed']);
}
$stmt->close();
?>