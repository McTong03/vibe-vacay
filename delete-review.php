<?php
require 'conn.php';

$id = intval($_GET['id'] ?? 0);
$destination_id = intval($_GET['destination_id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

if ($destination_id > 0) {
    header("Location: admin-feedback.php?id=" . $destination_id);
} else {
    header("Location: admin-feedback.php");
}
exit;
?>