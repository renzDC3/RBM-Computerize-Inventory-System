<?php
session_start();
include("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];
    $reason = $_POST['reason'];
    $user_id = $_SESSION['id'];

    date_default_timezone_set('Australia/Perth');
    $date = date('Y-m-d H:i:s');

    $stmt = $con->prepare("INSERT INTO stock_adjustment (product_id, qty, date_time, reason, Id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $product_id, $qty, $date, $reason, $user_id);

    if ($stmt->execute()) {
        $update_stmt = $con->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
        $update_stmt->bind_param("ii", $qty, $product_id);

        if ($update_stmt->execute()) {
            header("Location: inventory_list.php");
            exit();
        } else {
            echo "Error updating product quantity: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();
?>
