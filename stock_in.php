<?php
require_once 'session_config.php';
require_once 'csrf.php'; 
require 'config.php';

date_default_timezone_set('Australia/Perth'); 
$current_date = date('Y-m-d'); 
$current_time = date('H:i:s'); 
$datetime = date('Y-m-d H:i:s'); 

$user_id = $_SESSION['id'] ?? null;

// Helper function to log to system_log
function log_system($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $current_date, $current_time) {
    $stmt = $con->prepare("
        INSERT INTO system_log 
        (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "isssssssss", 
        $user_id, $username, $user_role, $action_type, $description, 
        $module, $submodule, $result, $current_date, $current_time
    );
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $description = "CSRF token verification failed.";
        log_system($con, $user_id ?? 0, $user_id ? $_SESSION['username'] ?? 'Unknown' : 'Guest', $user_id ? $_SESSION['role'] ?? 'Unknown' : 'Unknown', "Stock In", $description, "Products", "Stock In", "Failed", $current_date, $current_time);
        die("Invalid CSRF token");
    }

    // Sanitize input
    $product_id  = intval($_POST['product_id'] ?? 0);
    $qty         = intval($_POST['qty'] ?? 0);
    $supplier_id = intval($_POST['supplier_id'] ?? 0);

    if ($product_id <= 0 || $qty <= 0 || $supplier_id <= 0 || !$user_id) {
        $description = "Invalid input: product_id={$product_id}, qty={$qty}, supplier_id={$supplier_id}, user_id={$user_id}";
        log_system($con, $user_id ?? 0, $user_id ? $_SESSION['username'] ?? 'Unknown' : 'Guest', $user_id ? $_SESSION['role'] ?? 'Unknown' : 'Unknown', "Stock In", $description, "Products", "Stock In", "Failed", $current_date, $current_time);
        die("Invalid input.");
    }

    // Fetch product stock before update
    $product_stmt = $con->prepare("SELECT quantity, name FROM products WHERE product_id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product_data = $product_result->fetch_assoc();
    $product_stmt->close();

    if (!$product_data) {
        $description = "Product not found: product_id={$product_id}";
        log_system($con, $user_id, $_SESSION['username'] ?? 'Unknown', $_SESSION['role'] ?? 'Unknown', "Stock In", $description, "Products", "Stock In", "Failed", $current_date, $current_time);
        die("Product not found.");
    }

    $stock_before = intval($product_data['quantity']);
    $stock_after  = $stock_before + $qty;
    $product_name = $product_data['name'] ?? "Unknown";

    $delivery_id = "";

    // Insert into stock_in table
    $stmt = $con->prepare("
        INSERT INTO stock_in (product_id, qty, date_time, supplier_id, delivery_id, Id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisssi", $product_id, $qty, $datetime, $supplier_id, $delivery_id, $user_id); 

    if (!$stmt->execute()) {
        $description = "Error inserting stock-in record: " . $stmt->error;
        log_system($con, $user_id, $_SESSION['username'] ?? 'Unknown', $_SESSION['role'] ?? 'Unknown', "Stock In", $description, "Products", "Stock In", "Failed", $current_date, $current_time);
        die($description);
    }
    $stmt->close();

    // Update product quantity
    $update_stmt = $con->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
    $update_stmt->bind_param("ii", $qty, $product_id);

    if (!$update_stmt->execute()) {
        $description = "Error updating product quantity: " . $update_stmt->error;
        log_system($con, $user_id, $_SESSION['username'] ?? 'Unknown', $_SESSION['role'] ?? 'Unknown', "Stock In", $description, "Products", "Stock In", "Failed", $current_date, $current_time);
        die($description);
    }
    $update_stmt->close();

    // Prepare success log
    $description = "User '{$_SESSION['username']}' (Role: {$_SESSION['role']}) added {$qty} unit(s) of '{$product_name}' (Product ID: {$product_id}) "
                 . "from Supplier ID {$supplier_id} on {$datetime}. Stock count changed from {$stock_before} to {$stock_after}.";

    log_system($con, $user_id, $_SESSION['username'] ?? 'Unknown', $_SESSION['role'] ?? 'Unknown', "Stock In", $description, "Products", "Stock In", "Success", $current_date, $current_time);

    header("Location: products.php"); 
    exit();
}

$con->close();
?>
