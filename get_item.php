<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
$user_id = $_SESSION['user_id'];
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT id, reading_date, rainfall_mm, notes FROM rain_data WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
header('Content-Type: application/json');
echo json_encode($row);
?>