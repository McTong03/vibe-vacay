<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'conn.php';
header('Content-Type: application/json');

$type   = $_GET['type'] ?? '';
$q      = trim($_GET['q'] ?? '');
$userId = $_SESSION['user_id'] ?? null;

if ($type === 'history') {
    if (!$userId) {
        echo json_encode(['history' => []]);
        exit;
    }
    $stmt = $conn->prepare("
        SELECT DISTINCT keyword 
        FROM search_history 
        WHERE user_id = ? AND keyword != ''
        ORDER BY search_id DESC 
        LIMIT 5
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['history' => array_column($rows, 'keyword')]);
    exit;
}

if ($type === 'suggest' && $q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("
        SELECT d.destination_id, d.destination_name, s.state_name
        FROM destinations d
        LEFT JOIN states s ON s.state_id = d.state_id
        WHERE d.destination_name LIKE ?
        ORDER BY d.average_rating DESC
        LIMIT 6
    ");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['suggestions' => $results]);
    exit;
}

echo json_encode(['history' => [], 'suggestions' => []]);