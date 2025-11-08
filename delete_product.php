<?php
require_once 'session_config.php';
require_once 'csrf.php'; 
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $barcode = mysqli_real_escape_string($con, $_POST['barcode']);
    $price = (float)$_POST['price'];
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $model = (string)$_POST['model'];  
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['id']; 

    // Fetch user details for logging
    $user_sql = "SELECT Username, role FROM users WHERE Id = ?";
    $user_stmt = mysqli_prepare($con, $user_sql);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    mysqli_stmt_bind_result($user_stmt, $username, $user_role);
    mysqli_stmt_fetch($user_stmt);
    mysqli_stmt_close($user_stmt);

    // Try to get the product ID before deletion for logging
    $product_id = null;
    $product_sql = "SELECT id FROM products WHERE name = ? OR barcode = ? LIMIT 1";
    if ($pstmt = mysqli_prepare($con, $product_sql)) {
        mysqli_stmt_bind_param($pstmt, "ss", $name, $barcode);
        mysqli_stmt_execute($pstmt);
        mysqli_stmt_bind_result($pstmt, $product_id);
        mysqli_stmt_fetch($pstmt);
        mysqli_stmt_close($pstmt);
    }
          
    // Delete product
    $delete_sql = "DELETE FROM products WHERE name = ? OR barcode = ?";
    $dateTime = new DateTime('now', new DateTimeZone('Australia/Perth'));
    $log_date = $dateTime->format('Y-m-d');
    $log_time = $dateTime->format('H:i:s');

    if ($stmt = mysqli_prepare($con, $delete_sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $name, $barcode); 

        if (mysqli_stmt_execute($stmt)) {
            $rows_deleted = mysqli_stmt_affected_rows($stmt);

            if ($rows_deleted > 0) {
                // Log deletion in delete_product_history
                $history_sql = "INSERT INTO delete_product_history (name, barcode, price, category, model, quantity, date_time, Id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                if ($history_stmt = mysqli_prepare($con, $history_sql)) {
                    mysqli_stmt_bind_param($history_stmt, "ssdsdssi", $name, $barcode, $price, $category, $model, $quantity, $dateTime->format('Y-m-d H:i:s'), $user_id);
                    mysqli_stmt_execute($history_stmt);
                    mysqli_stmt_close($history_stmt);
                }

                // Log success in system_log
                $action_type = "Delete";
                $module = "Products";
                $submodule = "Delete Product";
                $result = "Success";
                $description = "Deleted product ID {$product_id}: {$name} (Barcode: {$barcode}, Category: {$category}, Model: {$model}, Quantity: {$quantity}, Price: {$price})";

                $log_sql = "INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if ($log_stmt = mysqli_prepare($con, $log_sql)) {
                    mysqli_stmt_bind_param($log_stmt, "isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $log_date, $log_time);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }

                header("Location: products.php");
                exit;
            } else {
                // Log failed deletion (no product found)
                $action_type = "Delete";
                $module = "Products";
                $submodule = "Delete Product";
                $result = "Failed";
                $description = "Attempted to delete product '{$name}' (Barcode: {$barcode}) but no matching record (ID: {$product_id}) was found.";

                $log_sql = "INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if ($log_stmt = mysqli_prepare($con, $log_sql)) {
                    mysqli_stmt_bind_param($log_stmt, "isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $log_date, $log_time);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }

                echo "No matching product found to delete.";
            }
        } else {
            // Log SQL execution error
            $action_type = "Delete";
            $module = "Products";
            $submodule = "Delete Product";
            $result = "Failed";
            $description = "Database error occurred while deleting product ID {$product_id} ('{$name}', Barcode: {$barcode}): " . mysqli_error($con);

            $log_sql = "INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if ($log_stmt = mysqli_prepare($con, $log_sql)) {
                mysqli_stmt_bind_param($log_stmt, "isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $log_date, $log_time);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
            }

            echo "Error deleting product: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);
    } else {
        // Log preparation failure
        $action_type = "Delete Product";
        $module = "Products";
        $submodule = "Delete";
        $result = "Failed";
        $description = "Failed to prepare deletion statement for product ID {$product_id} ('{$name}', Barcode: {$barcode}): " . mysqli_error($con);

        $log_sql = "INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($log_stmt = mysqli_prepare($con, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "isssssssss", $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result, $log_date, $log_time);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        }

        echo "Error preparing delete statement: " . mysqli_error($con);
    }

    mysqli_close($con);
}
?>
