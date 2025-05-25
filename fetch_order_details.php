<?php
session_start();
include("config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$query = "
    SELECT od.*, p.barcode, p.category 
    FROM order_detail od 
    JOIN products p ON od.product_id = p.product_id 
    WHERE od.order_id = ?";
    
if ($stmt = $con->prepare($query)) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $order_details = [];
    while ($row = $result->fetch_assoc()) {
        $order_details[] = $row;
    }

    echo json_encode($order_details);
    $stmt->close();
} else {
    echo json_encode(["error" => "Database query failed."]);
}

$con->close();
?>
