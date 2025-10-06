<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
$user_id = $_SESSION['user_id'];
$id = intval($_POST['id']);
$stmt = $conn->prepare("DELETE FROM rain_data WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
echo 'ok';
?>