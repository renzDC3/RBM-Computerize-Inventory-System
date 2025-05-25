<?php
include("config.php");

$order_id = $_GET['order_id'];

$order_query = "SELECT * FROM orders WHERE order_id = '$order_id'";
$order_result = mysqli_query($con, $order_query);
$order = mysqli_fetch_assoc($order_result);

$order_details_query = "SELECT * FROM order_detail WHERE order_id = '$order_id'";
$order_details_result = mysqli_query($con, $order_details_query);

$pdf_content = "%PDF-1.4\n";
$pdf_content .= "1 0 obj\n";
$pdf_content .= "<< /Type /Catalog /Pages 2 0 R >>\n";
$pdf_content .= "endobj\n";

$pdf_content .= "2 0 obj\n";
$pdf_content .= "<< /Type /Pages /MediaBox [0 0 595 842] /Kids [3 0 R] /Count 1 >>\n";
$pdf_content .= "endobj\n";

$pdf_content .= "3 0 obj\n";
$pdf_content .= "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << >> >>\n";
$pdf_content .= "endobj\n";

$pdf_text = ""; 

$pdf_content .= "4 0 obj\n";
$pdf_content .= "<< /Length 5 0 R >>\n"; 
$pdf_content .= "stream\n";

$pdf_text .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tRBM MOTORPARTS SHOP\n\n";
$pdf_text .= "Address: Evangelista Street, Bagong Barrio, Caloocan City\n\n";
$pdf_text .= "Tele: 0000-111-2222\n\n";
$pdf_text .= "*******************************************************************\n\n";
$pdf_text .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tCASH RECEIPT\n\n";
$pdf_text .= "*******************************************************************\n\n";

while ($detail = mysqli_fetch_assoc($order_details_result)) {
    $pdf_text .= "Product Name: " . $detail['product_name'] . "\n\n";
    $pdf_text .= "Quantity: " . $detail['quantity'] . "\n\n";
    $pdf_text .= "Price: " . $detail['price'] . "\n\n";
    $pdf_text .= "Subtotal: " . $detail['subtotal'] . "\n\n\n";
}

$pdf_text .= "*******************************************************************\n\n";
$pdf_text .= "Total: " . $order['orders_total'] . "\n\n";
$pdf_text .= "Cash: " . $order['orders_cash'] . "\n\n";
$pdf_text .= "Change: " . $order['orders_change'] . "\n\n\n";
$pdf_text .= "Date/Time: " . $order['order_date'] . "\n";

$length = strlen($pdf_text);
$pdf_content .= "5 0 R " . $length . "\n";

$pdf_content .= "BT\n"; 
$pdf_content .= "/F1 18 Tf\n"; 
$pdf_content .= "72 800 Td\n"; 

foreach (explode("\n", $pdf_text) as $line) {
    $pdf_content .= "($line) Tj\n"; 
    $pdf_content .= "0 -15 Td\n"; 
}

$pdf_content .= "ET\n"; 
$pdf_content .= "endstream\n";
$pdf_content .= "endobj\n";

$pdf_content .= "xref\n";
$pdf_content .= "0 5\n";
$pdf_content .= "0000000000 65535 f \n";
$pdf_content .= "0000000010 00000 n \n";
$pdf_content .= "0000000067 00000 n \n";
$pdf_content .= "0000000120 00000 n \n";
$pdf_content .= "0000000270 00000 n \n"; 

$pdf_content .= "trailer\n";
$pdf_content .= "<< /Size 5 /Root 1 0 R >>\n";
$pdf_content .= "startxref\n";
$pdf_content .= strlen($pdf_content) . "\n";
$pdf_content .= "%%EOF";

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="receipt.pdf"');
header('Content-Length: ' . strlen($pdf_content));

echo $pdf_content;
?>
