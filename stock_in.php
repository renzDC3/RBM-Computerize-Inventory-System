<?php
session_start();
include("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];
    //$date = $_POST['date'];
    $delivery_id = $_POST['delivery_id'];

    // Get the logged-in user's ID
    $user_id = $_SESSION['id'];

    // Set the timezone
    date_default_timezone_set('Australia/Perth'); // Replace with your timezone

    // Get the current date and time
    $date = date('Y-m-d H:i:s'); // Format: YYYY-MM-DD HH:MM:SS

    // Prepare an SQL statement to insert into stock_in (including user_id)
    $stmt = $con->prepare("INSERT INTO stock_in (product_id, qty, date_time, delivery_id, Id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $product_id, $qty, $date, $delivery_id, $user_id); // Added user_id

    // Execute the statement for stock_in
    if ($stmt->execute()) {
        // Successfully inserted into stock_in

        // Update the quantity in the products table
        $update_stmt = $con->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
        $update_stmt->bind_param("ii", $qty, $product_id);

        if ($update_stmt->execute()) {
            // Successfully updated the products table
            header("Location: inventory_list.php"); // Redirect to your inventory list page
            exit();
        } else {
            // Handle error in updating products
            echo "Error updating product quantity: " . $update_stmt->error;
        }

        // Close the update statement
        $update_stmt->close();
    } else {
        // Handle error in inserting into stock_in
        echo "Error: " . $stmt->error;
    }

    // Close the insert statement
    $stmt->close();
}

// Close the connection
$con->close();
?>
