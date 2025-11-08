<?php
require_once 'session_config.php'; 
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit; 
}

// Query only for the current month
$query = "
    SELECT od.product_name, SUM(od.quantity) AS total_quantity
    FROM order_detail od
    JOIN orders o ON od.order_id = o.order_id
    WHERE YEAR(o.order_date) = YEAR(CURDATE())
      AND MONTH(o.order_date) = MONTH(CURDATE())
    GROUP BY od.product_name
    ORDER BY total_quantity DESC
    LIMIT 5
";

$result = mysqli_query($con, $query);

if ($result) {
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row; 
    }

    header('Content-Type: application/json'); 
    echo json_encode($products);
} else {
    http_response_code(500); 
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($con)]);
}

mysqli_close($con);
?>
