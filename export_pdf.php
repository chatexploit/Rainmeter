<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require('config.php');
require __DIR__ . '/TCPDF-main/tcpdf.php';

$user_id = $_SESSION['user_id'];
$from = $_GET['from'] ?? '2000-01-01';
$to = $_GET['to'] ?? date('Y-m-d');

// Fetch rainfall data
$sql = "SELECT reading_date, rainfall_mm, notes 
        FROM rain_data 
        WHERE user_id=? AND reading_date BETWEEN ? AND ? 
        ORDER BY reading_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $from, $to);
$stmt->execute();
$result = $stmt->get_result();

// --- TCPDF Setup ---
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('RainMeter');
$pdf->SetTitle('Rainfall Report');
$pdf->SetMargins(15, 25, 15);
$pdf->AddPage();

// --- Logo ---
if (file_exists('my_logo.png')) {
    $pdf->Image('my_logo.png', 15, 10, 20);
}
$pdf->Ln(25);

// --- Title ---
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(0, 0, 0); // Black title
$pdf->Cell(0, 15, "Rainfall Report ($from to $to)", 0, 1, 'C');
$pdf->Ln(8);

// --- Table Header ---
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(0, 240, 255); // Cyan header
$pdf->SetTextColor(0, 0, 0); // Black text
$pdf->Cell(40, 10, "Date", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Rainfall (mm)", 1, 0, 'C', true);
$pdf->Cell(100, 10, "Notes", 1, 1, 'C', true);

// --- Table Rows ---
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(0, 0, 0); // Black text

$total = 0;
$count = 0;

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['reading_date'], 1);
    $pdf->Cell(40, 10, $row['rainfall_mm'], 1, 0, 'C');
    $pdf->Cell(100, 10, $row['notes'], 1);
    $pdf->Ln();

    $total += $row['rainfall_mm'];
    $count++;
}

// --- Summary Row ---
if ($count > 0) {
    $avg = $total / $count;
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(40, 10, "Summary", 1, 0, 'C', true);
    $pdf->Cell(40, 10, "Total: $total", 1, 0, 'C', true);
    $pdf->Cell(100, 10, "Average: " . number_format($avg, 2) . " mm", 1, 1, 'C', true);
}

// --- Output PDF ---
$pdf->Output("rainfall_report.pdf", "I");