<?php
session_start();
include("config.php");

$data = json_decode(file_get_contents('php://input'), true);

date_default_timezone_set('Australia/Perth');

if (!isset($data['total'], $data['cash'], $data['change'], $data['order_details'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    mysqli_close($con);
    exit();
}

$total = $data['total'];
$cash = $data['cash'];
$change = $data['change'];
$order_date = date('Y-m-d H:i:s'); 

$user_id = $_SESSION['id'];  

$stmt = $con->prepare("INSERT INTO orders (orders_total, orders_cash, orders_change, order_date, Id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $total, $cash, $change, $order_date, $user_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
    $stmt->close();
    mysqli_close($con);
    exit();
}

$order_id = $stmt->insert_id;
$stmt->close();

$order_details = $data['order_details'];
foreach ($order_details as $detail) {
    $product_name = $detail['product_name'];
    $product_id = $detail['product_id'];
    $quantity = $detail['quantity'];
    $subtotal = $detail['subtotal'];
    $price = $detail['price'];

    $stock_query = "SELECT quantity FROM products WHERE product_id = '$product_id'";
    $stock_result = mysqli_query($con, $stock_query);
    $product = mysqli_fetch_assoc($stock_result);

    if ($product) {
        $current_quantity = $product['quantity'];
        if ($current_quantity >= $quantity) {
            $new_quantity = $current_quantity - $quantity;
            $update_query = "UPDATE products SET quantity = '$new_quantity' WHERE product_id = '$product_id'";
            mysqli_query($con, $update_query);

            $detail_stmt = $con->prepare("INSERT INTO order_detail (order_id, product_name, product_id, quantity, subtotal, price) VALUES (?, ?, ?, ?, ?, ?)");
            $detail_stmt->bind_param("isssss", $order_id, $product_name, $product_id, $quantity, $subtotal, $price);

            if (!$detail_stmt->execute()) {
                error_log("Error inserting order detail: " . $detail_stmt->error);
            }
            $detail_stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Insufficient stock for product: ' . $product_name]);
            mysqli_close($con);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Product not found: ' . $product_name]);
        mysqli_close($con);
        exit();
    }
}

echo json_encode(['success' => true, 'order_id' => $order_id]);

mysqli_close($con);
?>
