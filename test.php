<?php
// Debug mode on
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load TCPDF
require __DIR__ . '/TCPDF-main/tcpdf.php';

// Create a new PDF
$pdf = new TCPDF();
$pdf->AddPage();

// Add test text
$pdf->SetFont('helvetica', '', 14);
$pdf->Cell(0, 10, "✅ TCPDF is working correctly!", 0, 1, 'C');

// Output
$pdf->Output("test.pdf", "I");