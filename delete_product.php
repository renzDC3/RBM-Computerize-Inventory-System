<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $barcode = mysqli_real_escape_string($con, $_POST['barcode']);
    $price = (float)$_POST['price'];
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $model = (string)$_POST['model'];  
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['id']; 
          
    $delete_sql = "DELETE FROM products WHERE name = ? OR barcode = ?";

    if ($stmt = mysqli_prepare($con, $delete_sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $name, $barcode); 

        if (mysqli_stmt_execute($stmt)) {
            $history_sql = "INSERT INTO delete_product_history (name, barcode, price, category, model, quantity, date_time, Id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $dateTime = new DateTime('now', new DateTimeZone('Australia/Perth'));
            $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

            if ($history_stmt = mysqli_prepare($con, $history_sql)) {
                mysqli_stmt_bind_param($history_stmt, "ssdsdssi", $name, $barcode, $price, $category, $model, $quantity, $formattedDateTime, $user_id);

                if (!mysqli_stmt_execute($history_stmt)) {
                    echo "Error logging history: " . mysqli_error($con);
                }

                mysqli_stmt_close($history_stmt);
            } else {
                echo "Error preparing history statement: " . mysqli_error($con);
            }

            header("Location: inventory_list.php");
            exit;
        } else {
            echo "Error deleting product: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing delete statement: " . mysqli_error($con);
    }

    mysqli_close($con);
}

