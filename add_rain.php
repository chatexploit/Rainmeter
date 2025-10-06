<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) { http_response_code(403); echo 'no'; exit; }
$user_id = $_SESSION['user_id'];

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$date = $_POST['reading_date'] ?? null;
$mm = $_POST['rainfall_mm'] ?? null;
$notes = $_POST['notes'] ?? '';

if (!$date || $mm === null) { echo 'error'; exit; }

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE rain_data SET reading_date=?, rainfall_mm=?, notes=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sdsii", $date, $mm, $notes, $id, $user_id);
    $stmt->execute();
    echo 'updated';
} else {
    $stmt = $conn->prepare("INSERT INTO rain_data (user_id, reading_date, rainfall_mm, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $date, $mm, $notes);
    $stmt->execute();
    echo 'inserted';
}
?>