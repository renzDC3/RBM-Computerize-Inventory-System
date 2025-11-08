<?php
require_once 'session_config.php';
require_once 'csrf.php'; 
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CSRF validation
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $product_id = intval($_POST['product_id']);
    $def_qty = intval($_POST['qty']);
    $supplier_id = intval($_POST['supplier_id']); 
    $user_id = $_SESSION['id'];

    // Fetch user details
    $user_sql = "SELECT Username, role FROM users WHERE Id = ?";
    $user_stmt = $con->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();

    $username = $user['Username'];
    $user_role = $user['role'];

    // Fetch the product
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['quantity'] >= $def_qty) {

            // Update product quantity
            $new_qty = $row['quantity'] - $def_qty;
            $update_sql = "UPDATE products SET quantity = ? WHERE product_id = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_qty, $product_id);
            $update_stmt->execute();

            // Insert into defective product history
            $insert_sql = "INSERT INTO handle_defective_product_history 
                (product_id, name, barcode, category, model, quantity, price, date_time, supplier_id, Id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
            $insert_stmt = $con->prepare($insert_sql);
            $insert_stmt->bind_param(
                "issssidsi",
                $row['product_id'],
                $row['name'],
                $row['barcode'],
                $row['category'],
                $row['model'],
                $def_qty,
                $row['price'],
                $supplier_id,
                $user_id
            );
            $insert_stmt->execute();

            // --- Log the action in system_log with date and time ---
            $description = sprintf(
                "User %s (ID %d, Role: %s) marked %d unit(s) of '%s' (Product ID: %d, Barcode: %s) as defective. New quantity: %d.",
                $username,
                $user_id,
                $user_role,
                $def_qty,
                $row['name'],
                $row['product_id'],
                $row['barcode'],
                $new_qty
            );

            $log_sql = "INSERT INTO system_log 
                (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
            $log_stmt = $con->prepare($log_sql);
            $action_type = "Handle Defective Product";
            $module = "Products";
            $submodule = "Defective";
            $result_status = "Success";
            $log_stmt->bind_param(
                "isssssss",
                $user_id,
                $username,
                $user_role,
                $action_type,
                $description,
                $module,
                $submodule,
                $result_status
            );
            $log_stmt->execute();

            echo "<script>alert('Defective product handled successfully.'); window.location.href='products.php';</script>";

        } else {
            // Log failure (not enough stock)
            $description = sprintf(
                "User %s (ID %d, Role: %s) attempted to mark %d defective unit(s) for Product ID %d but only %d were in stock.",
                $username,
                $user_id,
                $user_role,
                $def_qty,
                $product_id,
                $row['quantity']
            );

            $log_sql = "INSERT INTO system_log 
                (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
            $log_stmt = $con->prepare($log_sql);
            $action_type = "Handle Defective Product";
            $module = "Inventory";
            $submodule = "Defective Products";
            $result_status = "Failure";
            $log_stmt->bind_param(
                "isssssss",
                $user_id,
                $username,
                $user_role,
                $action_type,
                $description,
                $module,
                $submodule,
                $result_status
            );
            $log_stmt->execute();

            echo "<script>alert('Error: Not enough stock available.'); window.history.back();</script>";
        }
    } else {
        // Log failure (product not found)
        $description = sprintf(
            "User %s (ID %d, Role: %s) attempted to mark Product ID %d as defective but product was not found.",
            $username,
            $user_id,
            $user_role,
            $product_id
        );

        $log_sql = "INSERT INTO system_log 
            (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME())";
        $log_stmt = $con->prepare($log_sql);
        $action_type = "Handle Defective Product";
        $module = "Inventory";
        $submodule = "Defective Products";
        $result_status = "Failure";
        $log_stmt->bind_param(
            "isssssss",
            $user_id,
            $username,
            $user_role,
            $action_type,
            $description,
            $module,
            $submodule,
            $result_status
        );
        $log_stmt->execute();

        echo "<script>alert('Error: Product not found.'); window.history.back();</script>";
    }
}
?>
