<?php
require_once 'session_config.php';
require_once 'csrf.php'; 
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];
    $reason = $_POST['reason'];
    $user_id = $_SESSION['id'];

    date_default_timezone_set('Australia/Perth');
    $date_time = date('Y-m-d H:i:s');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Get user details for the log
    $user_stmt = $con->prepare("SELECT Username, role FROM users WHERE Id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_stmt->bind_result($username, $user_role);
    $user_stmt->fetch();
    $user_stmt->close();

    // Insert into stock_adjustment
    $stmt = $con->prepare("INSERT INTO stock_adjustment (product_id, qty, date_time, reason, Id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $product_id, $qty, $date_time, $reason, $user_id);

    if ($stmt->execute()) {
        // Update product quantity
        $update_stmt = $con->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
        $update_stmt->bind_param("ii", $qty, $product_id);

        if ($update_stmt->execute()) {

            // --- LOG TO system_log TABLE ---
            $action_type = 'Stock Adjustment';
            $module = 'Inventory';
            $submodule = 'Products';
            $result = 'Success';

            // Optional: fetch product name for clarity
            $prod_stmt = $con->prepare("SELECT product_name FROM products WHERE product_id = ?");
            $prod_stmt->bind_param("i", $product_id);
            $prod_stmt->execute();
            $prod_stmt->bind_result($product_name);
            $prod_stmt->fetch();
            $prod_stmt->close();

            $description = sprintf(
                "Adjusted stock for product '%s' (ID: %d) to quantity %d. Reason: %s",
                $product_name ?? 'Unknown',
                $product_id,
                $qty,
                $reason
            );

            $log_stmt = $con->prepare("INSERT INTO system_log 
                (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->bind_param("isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $date, $time);
            $log_stmt->execute();
            $log_stmt->close();
            // --- END LOG ---

            header("Location: products.php");
            exit();
        } else {
            echo "Error updating product quantity: " . $update_stmt->error;

            // Log the failure
            $action_type = 'Stock Adjustment';
            $module = 'Inventory';
            $submodule = 'Products';
            $result = 'Failed';
            $description = "Failed to update product quantity for product ID: $product_id. Reason: " . $update_stmt->error;

            $log_stmt = $con->prepare("INSERT INTO system_log 
                (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->bind_param("isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $date, $time);
            $log_stmt->execute();
            $log_stmt->close();
        }

        $update_stmt->close();
    } else {
        echo "Error: " . $stmt->error;

        // Log the failure
        $action_type = 'Stock Adjustment';
        $module = 'Products';
        $submodule = 'Adjust';
        $result = 'Failed';
        $description = "Failed to insert stock adjustment for product ID: $product_id. Reason: " . $stmt->error;

        $log_stmt = $con->prepare("INSERT INTO system_log 
            (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $log_stmt->bind_param("isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $date, $time);
        $log_stmt->execute();
        $log_stmt->close();
    }

    $stmt->close();
}

$con->close();
?>
