<?php
require_once 'session_config.php';
require_once 'csrf.php'; 
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!verify_csrf_token($data['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit();
}

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

// Fetch user details for logging
$user_query = $con->prepare("SELECT Username, role FROM users WHERE Id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();
$user_query->close();

$username = $user_data['Username'] ?? 'Unknown';
$user_role = $user_data['role'] ?? 'Unknown';

// --- Helper function for system logging ---
function log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result) {
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $stmt = $con->prepare("
        INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $date, $time);
    $stmt->execute();
    $stmt->close();
}

$action_type = 'Sales';
$module = 'Sales';
$submodule = 'Sales';

$stmt = $con->prepare("INSERT INTO orders (orders_total, orders_cash, orders_change, order_date, Id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $total, $cash, $change, $order_date, $user_id);

if (!$stmt->execute()) {
    $error_msg = "Failed to insert order record: " . $stmt->error;
    log_system_action($con, $user_id, $username, $user_role, $action_type, $error_msg, $module, $submodule, 'Failure');
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
                $error_msg = "Error inserting order detail for product '$product_name': " . $detail_stmt->error;
                log_system_action($con, $user_id, $username, $user_role, $action_type, $error_msg, $module, $submodule, 'Failure');
                error_log($error_msg);
            }
            $detail_stmt->close();
        } else {
            $error_msg = "Insufficient stock for product: $product_name (requested $quantity, available $current_quantity)";
            log_system_action($con, $user_id, $username, $user_role, $action_type, $error_msg, $module, $submodule, 'Failure');
            echo json_encode(['success' => false, 'error' => $error_msg]);
            mysqli_close($con);
            exit();
        }
    } else {
        $error_msg = "Product not found: $product_name";
        log_system_action($con, $user_id, $username, $user_role, $action_type, $error_msg, $module, $submodule, 'Failure');
        echo json_encode(['success' => false, 'error' => $error_msg]);
        mysqli_close($con);
        exit();
    }
}

// ---- SUCCESS LOG ENTRY ----
$description = sprintf(
    "Sale recorded successfully (Order ID: %d) | Total: %.2f | Cash: %.2f | Change: %.2f | %d items sold.",
    $order_id,
    $total,
    $cash,
    $change,
    count($order_details)
);

log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, 'Success');

echo json_encode(['success' => true, 'order_id' => $order_id]);

mysqli_close($con);
?>
