<?php
session_start();
include("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $date = date('Y-m-d H:i:s');

    $fieldsToUpdate = [];
    $params = [];
    $types = '';

    if (!empty($name)) {
        $fieldsToUpdate[] = "name = ?";
        $params[] = $name;
        $types .= 's';
    }

    if (!empty($barcode)) {
        $fieldsToUpdate[] = "barcode = ?";
        $params[] = $barcode;
        $types .= 's';
    }

    if (!empty($category) && $category !== 'Select an option') {
        $fieldsToUpdate[] = "category = ?";
        $params[] = $category;
        $types .= 's';
    }

    if (!empty($model)) {
        $fieldsToUpdate[] = "model = ?";
        $params[] = $model;
        $types .= 's';  
    }

    if (count($fieldsToUpdate) > 0) {
        $sql = "UPDATE products SET " . implode(', ', $fieldsToUpdate) . " WHERE product_id = ?";
        $params[] = $product_id;
        $types .= 'i';  

        $update_stmt = $con->prepare($sql);
        $update_stmt->bind_param($types, ...$params);

        if ($update_stmt->execute()) {
            if (!empty($reason)) {
                $history_stmt = $con->prepare("INSERT INTO edit_product_history (product_id, name, barcode, category, model, reason, date_time, Id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $history_stmt->bind_param("issssssi", $product_id, $name, $barcode, $category, $model, $reason, $date, $user_id);
                $history_stmt->execute();
                $history_stmt->close();
            }

            header("Location: inventory_list.php");
            exit();
        } else {
            echo "Error updating product: " . $update_stmt->error;
        }

        $update_stmt->close();
    }
}

$con->close();
?>
