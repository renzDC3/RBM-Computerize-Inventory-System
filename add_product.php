<?php
require_once 'session_config.php';
require_once 'csrf.php';  
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $barcode = mysqli_real_escape_string($con, $_POST['barcode']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $cost = (float)$_POST['cost'];
    $model = !empty($_POST['model']) ? mysqli_real_escape_string($con, $_POST['model']) : NULL;
    $supplier_id = (int)$_POST['supplier'];
    $user_id = $_SESSION['id'];

    // Fetch user info
    $user_query = "SELECT Username, role FROM users WHERE Id = ?";
    $user_stmt = mysqli_prepare($con, $user_query);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    mysqli_stmt_bind_result($user_stmt, $username, $user_role);
    mysqli_stmt_fetch($user_stmt);
    mysqli_stmt_close($user_stmt);

    // Helper function to log system actions
    function log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, $result) {
        $dateTime = new DateTime('now', new DateTimeZone('Australia/Perth'));
        $log_date = $dateTime->format('Y-m-d');
        $log_time = $dateTime->format('H:i:s');

        $sql = "INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($con, $sql)) {
            mysqli_stmt_bind_param(
                $stmt,
                "isssssssss",
                $user_id,
                $username,
                $user_role,
                $action_type,
                $description,
                $module,
                $submodule,
                $result,
                $log_date,
                $log_time
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    $action_type = "Add Product";
    $module = "Products";
    $submodule = "Add Product";

    // Check for duplicate product name
    $check_sql = "SELECT * FROM products WHERE name = ?";
    if ($check_stmt = mysqli_prepare($con, $check_sql)) {
        mysqli_stmt_bind_param($check_stmt, "s", $name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $description = sprintf(
                "Attempted to add a product with name '%s', but it already exists.",
                $username, $user_role, $user_id, $name
            );
            log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, "Failed - Duplicate");
            echo "Error: A product with that name already exists.";
            mysqli_stmt_close($check_stmt);
            mysqli_close($con);
            exit;
        }
        mysqli_stmt_close($check_stmt);
    } else {
        $description = "Database error while checking for duplicate product: " . mysqli_error($con);
        log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, "Failed - SQL Error");
        echo "Error preparing check statement: " . mysqli_error($con);
        exit;
    }

    // Insert new product
    $sql = "INSERT INTO products (name, barcode, category, quantity, price, cost, model, supplier_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssddssi", $name, $barcode, $category, $quantity, $price, $cost, $model, $supplier_id);

        if (mysqli_stmt_execute($stmt)) {
            $product_id = mysqli_insert_id($con);

            $dateTime = new DateTime('now', new DateTimeZone('Australia/Perth'));
            $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

            // Insert into add_product_history
            $history_sql = "INSERT INTO add_product_history 
                (product_id, Id, name, barcode, category, model, quantity, price, cost, supplier_id, date_time) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if ($history_stmt = mysqli_prepare($con, $history_sql)) {
                mysqli_stmt_bind_param(
                    $history_stmt, 
                    "isssssidids", 
                    $product_id, 
                    $user_id, 
                    $name, 
                    $barcode, 
                    $category, 
                    $model, 
                    $quantity, 
                    $price, 
                    $cost, 
                    $supplier_id, 
                    $formattedDateTime
                );
                mysqli_stmt_execute($history_stmt);
                mysqli_stmt_close($history_stmt);
            }

            // Insert into stock_in
            $delivery_id = NULL;
            $stock_sql = "INSERT INTO stock_in (product_id, qty, date_time, supplier_id, delivery_id, Id) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            if ($stock_stmt = mysqli_prepare($con, $stock_sql)) {
                mysqli_stmt_bind_param(
                    $stock_stmt, 
                    "iisssi", 
                    $product_id, 
                    $quantity, 
                    $formattedDateTime, 
                    $supplier_id, 
                    $delivery_id, 
                    $user_id
                );
                mysqli_stmt_execute($stock_stmt);
                mysqli_stmt_close($stock_stmt);
            }

            // Log success
            $description = sprintf(
                "Successfully added product [ID: %d] - Name: %s, Barcode: %s, Category: %s, Model: %s, Quantity: %d, Price: %.2f, Cost: %.2f, Supplier ID: %d",
                $username,
                $user_role,
                $user_id,
                $product_id,
                $name,
                $barcode,
                $category,
                $model ?? 'N/A',
                $quantity,
                $price,
                $cost,
                $supplier_id
            );
            log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, "Success");

            echo "<script>
                alert('Success: Product added successfully.');
                window.location.href = 'products.php';
            </script>";

        } else {
            $description = sprintf(
                "Failed to add product '%s' due to database error: %s",
                $username, $user_role, $user_id, $name, mysqli_error($con)
            );
            log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, "Failed - SQL Error");
            echo "Error inserting product: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);
    } else {
        $description = "Error preparing product insert statement: " . mysqli_error($con);
        log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule, "Failed - SQL Error");
        echo "Error preparing product statement: " . mysqli_error($con);
    }

    mysqli_close($con);
}
?>
