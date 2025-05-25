<?php
session_start();
include("config.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    $query = "DELETE FROM products WHERE product_id = ?";
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($con);
        exit();
    }
}

header("Location: inventory_list.php"); 
exit();
?>
