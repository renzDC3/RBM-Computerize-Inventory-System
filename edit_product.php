<?php
require_once 'session_config.php'; 
require_once 'csrf.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $product_id = $_POST['product_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $barcode = $_POST['barcode'] ?? null;
    $category = $_POST['category'] ?? null;
    $reason = $_POST['reason'] ?? null;
    $model = $_POST['model'] ?? null;  

    $user_id = $_SESSION['id'];  

    if (empty($user_id)) {
        echo "User not logged in!";
        exit;
    }

    date_default_timezone_set('Australia/Perth');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Fetch current user details
    $user_stmt = $con->prepare("SELECT Username, role FROM users WHERE Id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $username = $user_data['Username'];
    $user_role = $user_data['role'];
    $user_stmt->close();

    // Fetch current product data for comparison
    $old_stmt = $con->prepare("SELECT name, barcode, category, model FROM products WHERE product_id = ?");
    $old_stmt->bind_param("i", $product_id);
    $old_stmt->execute();
    $old_result = $old_stmt->get_result();
    $old_data = $old_result->fetch_assoc();
    $old_stmt->close();

    $fieldsToUpdate = [];
    $params = [];
    $types = '';

    // Track changes for log description
    $changes = [];

    if (!empty($name) && $name !== $old_data['name']) {
        $fieldsToUpdate[] = "name = ?";
        $params[] = $name;
        $types .= 's';
        $changes[] = "Name of Product ID {$product_id} changed from '{$old_data['name']}' to '{$name}'";
    }

    if (!empty($barcode) && $barcode !== $old_data['barcode']) {
        $fieldsToUpdate[] = "barcode = ?";
        $params[] = $barcode;
        $types .= 's';
        $changes[] = "Barcode of Product ID {$product_id} changed from '{$old_data['barcode']}' to '{$barcode}'";
    }

    if (!empty($category) && $category !== 'Select an option' && $category !== $old_data['category']) {
        $fieldsToUpdate[] = "category = ?";
        $params[] = $category;
        $types .= 's';
        $changes[] = "Category of Product ID {$product_id} changed from '{$old_data['category']}' to '{$category}'";
    }

    if (!empty($model) && $model !== $old_data['model']) {
        $fieldsToUpdate[] = "model = ?";
        $params[] = $model;
        $types .= 's';  
        $changes[] = "Model of Product ID {$product_id} changed from '{$old_data['model']}' to '{$model}'";
    }

    if (count($fieldsToUpdate) > 0) {
        $sql = "UPDATE products SET " . implode(', ', $fieldsToUpdate) . " WHERE product_id = ?";
        $params[] = $product_id;
        $types .= 'i';  

        $update_stmt = $con->prepare($sql);
        $update_stmt->bind_param($types, ...$params);

        if ($update_stmt->execute()) {
            // Insert into edit history if reason provided
            if (!empty($reason)) {
                $history_stmt = $con->prepare("
                    INSERT INTO edit_product_history (product_id, name, barcode, category, model, reason, date_time, Id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $history_stmt->bind_param("issssssi", $product_id, $name, $barcode, $category, $model, $reason, $date . ' ' . $time, $user_id);
                $history_stmt->execute();
                $history_stmt->close();
            }

            // Build detailed log description
            if (!empty($changes)) {
                $description = implode("; ", $changes);
            } else {
                $description = "Product ID {$product_id}: details updated with no detected field changes";
            }

            if (!empty($reason)) {
                $description .= ". Reason: " . $reason;
            }

            // Truncate description to 255 characters
            $description = mb_substr($description, 0, 255, 'UTF-8');

            // Log success with date and time
            $log_stmt = $con->prepare("
                INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $action_type = "Edit Product";
            $module = "Products";
            $submodule = "Edit";
            $result = "Success";
            $log_stmt->bind_param(
                "isssssssss",
                $user_id,
                $username,
                $user_role,
                $action_type,
                $description,
                $module,
                $submodule,
                $result,
                $date,
                $time
            );
            $log_stmt->execute();
            $log_stmt->close();

            header("Location: products.php");
            exit();
        } else {
            echo "Error updating product: " . $update_stmt->error;

            // Log failure with date and time
            $fail_desc = "Product ID {$product_id}: failed to edit product. Error: " . $update_stmt->error;
            $fail_desc = mb_substr($fail_desc, 0, 255, 'UTF-8');

            $log_stmt = $con->prepare("
                INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $action_type = "Edit Product";
            $module = "Products";
            $submodule = "Edit";
            $result = "Failure";
            $log_stmt->bind_param(
                "isssssssss",
                $user_id,
                $username,
                $user_role,
                $action_type,
                $fail_desc,
                $module,
                $submodule,
                $result,
                $date,
                $time
            );
            $log_stmt->execute();
            $log_stmt->close();
        }

        $update_stmt->close();
    } else {
        // No fields changed — still log it
        $description = "Product ID {$product_id}: edit attempted but no fields were changed";
        if (!empty($reason)) {
            $description .= ". Reason: " . $reason;
        }
        $description = mb_substr($description, 0, 255, 'UTF-8');

        $log_stmt = $con->prepare("
            INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $action_type = "Edit Product";
        $module = "Products";
        $submodule = "Edit";
        $result = "No Change";
        $log_stmt->bind_param(
            "isssssssss",
            $user_id,
            $username,
            $user_role,
            $action_type,
            $description,
            $module,
            $submodule,
            $result,
            $date,
            $time
        );
        $log_stmt->execute();
        $log_stmt->close();
    }
}

$con->close();
?>
