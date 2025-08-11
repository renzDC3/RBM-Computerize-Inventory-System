<?php
session_start();
include("config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $barcode = mysqli_real_escape_string($con, $_POST['barcode']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $model = isset($_POST['model']) && !empty($_POST['model']) ? mysqli_real_escape_string($con, $_POST['model']) : NULL;

    $user_id = $_SESSION['id'];

    $check_sql = "SELECT * FROM products WHERE name = ?";
    if ($check_stmt = mysqli_prepare($con, $check_sql)) {
        mysqli_stmt_bind_param($check_stmt, "s", $name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            echo "Error: A product with that name already exists.";
            mysqli_stmt_close($check_stmt);
            mysqli_close($con);
            exit;
        }

        mysqli_stmt_close($check_stmt);
    } else {
        echo "Error preparing check statement: " . mysqli_error($con);
        exit;
    }

    $sql = "INSERT INTO products (name, barcode, category, quantity, price, model) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($con, $sql)) {
        // Check if model is NULL and adjust the binding type accordingly
        if ($model === NULL) {
            mysqli_stmt_bind_param($stmt, "siissd", $name, $barcode, $category, $quantity, $price);
        } else {
            mysqli_stmt_bind_param($stmt, "sssids", $name, $barcode, $category, $quantity, $price, $model);
        }

        if (mysqli_stmt_execute($stmt)) {
            $product_id = mysqli_insert_id($con);

            $history_sql = "INSERT INTO add_product_history (product_id, Id, name, barcode, category, quantity, price, model, date_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $dateTime = new DateTime('now', new DateTimeZone('Australia/Perth'));
            $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

            if ($history_stmt = mysqli_prepare($con, $history_sql)) {
                if ($model === NULL) {
                    mysqli_stmt_bind_param($history_stmt, "iissids", $product_id, $user_id, $name, $barcode, $category, $quantity, $price, $formattedDateTime);
                } else {
                    mysqli_stmt_bind_param($history_stmt, "iisdsssss", $product_id, $user_id, $name, $barcode, $category, $quantity, $price, $model, $formattedDateTime);
                }

                if (!mysqli_stmt_execute($history_stmt)) {
                    echo "Error logging history: " . mysqli_error($con);
                }

                mysqli_stmt_close($history_stmt);
            } else {
                echo "Error preparing history statement: " . mysqli_error($con);
            }

            echo "<script>
                alert('Success: Product added successfully.');
                window.location.href = 'products.php';
            </script>";
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            exit;

        } else {
            echo "Error inserting product: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing product statement: " . mysqli_error($con);
    }

    mysqli_close($con);
}
?>
