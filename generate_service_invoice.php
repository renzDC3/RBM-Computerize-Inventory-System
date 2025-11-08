<?php
require_once 'session_config.php'; 
require 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

// Get service ID from query
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

// Get service details
if ($service_id > 0) {
    $service_query = "SELECT * FROM services WHERE services_id = '$service_id'";
} else {
    $service_query = "SELECT * FROM services ORDER BY services_id DESC LIMIT 1";
}

$service_result = mysqli_query($con, $service_query);
$service = mysqli_fetch_assoc($service_result);

if (!$service) {
    die("Service not found.");
}

// Begin PDF content
$pdf_content = "%PDF-1.4\n";
$pdf_content .= "1 0 obj\n";
$pdf_content .= "<< /Type /Catalog /Pages 2 0 R >>\n";
$pdf_content .= "endobj\n";

// Page tree with same dimensions as order report
$pdf_content .= "2 0 obj\n";
$pdf_content .= "<< /Type /Pages /MediaBox [0 0 227 842] /Kids [3 0 R] /Count 1 >>\n";
$pdf_content .= "endobj\n";

// Single page
$pdf_content .= "3 0 obj\n";
$pdf_content .= "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 227 842] /Contents 4 0 R /Resources << >> >>\n";
$pdf_content .= "endobj\n";

// Text data
$pdf_text = ""; 
$pdf_text .= "         RBM MOTORPARTS SHOP\n\n";
$pdf_text .= "Address: Evangelista Street, Bagong \nBarrio, Caloocan City\n\n";
$pdf_text .= "Tele: 0000-111-2222\n\n";
$pdf_text .= "*********************************************\n\n";
$pdf_text .= "               SERVICE RECEIPT\n\n";
$pdf_text .= "*********************************************\n\n";

$pdf_text .= "Description: " . $service['services_description'] . "\n";
$pdf_text .= "Price: " . number_format($service['services_price'], 2) . "\n\n";
$pdf_text .= "Customer Cash: " . number_format($service['services_customer_cash'], 2) . "\n";
$pdf_text .= "Change: " . number_format($service['services_customer_change'], 2) . "\n\n";
$pdf_text .= "*********************************************\n\n";
$pdf_text .= "Date/Time: " . $service['services_date'] . "\n\n";
$pdf_text .= "*********************************************\n";
$pdf_text .= "      Thank you for your business!\n";

// PDF stream
$pdf_content .= "4 0 obj\n";
$pdf_content .= "<< /Length 5 0 R >>\n"; 
$pdf_content .= "stream\n";

$pdf_content .= "BT\n"; 
$pdf_content .= "/F1 12 Tf\n"; // consistent font size
$pdf_content .= "10 800 Td\n"; 

foreach (explode("\n", $pdf_text) as $line) {
    $escaped_line = str_replace(['(', ')'], ['\\(', '\\)'], $line);
    $pdf_content .= "($escaped_line) Tj\n"; 
    $pdf_content .= "0 -14 Td\n"; 
}

$pdf_content .= "ET\n"; 
$pdf_content .= "endstream\n";
$pdf_content .= "endobj\n";

$length = strlen($pdf_text);
$pdf_content .= "5 0 obj\n";
$pdf_content .= "$length\n";
$pdf_content .= "endobj\n";

$pdf_content .= "xref\n";
$pdf_content .= "0 6\n";
$pdf_content .= "0000000000 65535 f \n";
$pdf_content .= "0000000010 00000 n \n";
$pdf_content .= "0000000067 00000 n \n";
$pdf_content .= "0000000120 00000 n \n";
$pdf_content .= "0000000270 00000 n \n";
$pdf_content .= "0000000000 00000 n \n";

$pdf_content .= "trailer\n";
$pdf_content .= "<< /Size 6 /Root 1 0 R >>\n";
$pdf_content .= "startxref\n";
$pdf_content .= strlen($pdf_content) . "\n";
$pdf_content .= "%%EOF";

// Output PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename=\"service_receipt.pdf\"');
header('Content-Length: ' . strlen($pdf_content));

echo $pdf_content;
?>
