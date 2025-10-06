<?php
$host = "HOSTNAME";
$user = "USERNAME"; 
$pass = "PASSWORD"; 
$dbname = "DATABASE NAME";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
