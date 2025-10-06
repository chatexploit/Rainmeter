<?php
$host = "sql305.ezyro.com";
$user = "ezyro_40069215"; 
$pass = "51b8aa1eb0f6b9"; 
$dbname = "ezyro_40069215_rainmeter";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>