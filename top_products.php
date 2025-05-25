<?php
session_start();

include("config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit; 
}

$query = "SELECT product_name, SUM(quantity) AS total_quantity 
          FROM order_detail 
          GROUP BY product_name 
          ORDER BY total_quantity DESC 
          LIMIT 5";

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
