<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
$user_id = $_SESSION['user_id'];

$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;

if ($from && $to) {
    $stmt = $conn->prepare("SELECT id, reading_date, rainfall_mm, notes FROM rain_data WHERE user_id=? AND reading_date BETWEEN ? AND ? ORDER BY reading_date ASC");
    $stmt->bind_param("iss", $user_id, $from, $to);
} else {
    $stmt = $conn->prepare("SELECT id, reading_date, rainfall_mm, notes FROM rain_data WHERE user_id=? ORDER BY reading_date ASC");
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($row = $res->fetch_assoc()) $out[] = $row;
header('Content-Type: application/json');
echo json_encode($out);
?>