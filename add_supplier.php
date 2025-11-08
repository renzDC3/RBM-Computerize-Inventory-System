<?php
require_once 'session_config.php';
require_once 'config.php';
require_once 'csrf.php';

// Set timezone to Perth, Australia (AWST, UTC+8)
date_default_timezone_set('Australia/Perth');

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    // Sanitize form input
    $name            = trim($_POST['name'] ?? '');
    $businessAddress = trim($_POST['businessaddress'] ?? '');
    $contactNo       = trim($_POST['contactno'] ?? '');
    $emailAddress    = trim($_POST['emailaddress'] ?? '');

    // Basic validation
    if (empty($name) || empty($businessAddress) || empty($contactNo) || empty($emailAddress)) {
        die("All fields are required.");
    }

    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (!preg_match('/^[0-9+\-\s]{6,20}$/', $contactNo)) { 
        die("Invalid contact number format.");
    }

    // Insert into suppliers table
    $stmt = $con->prepare(
        "INSERT INTO suppliers 
         (supplier_name, supplier__business_address, supplier_contact_no, supplier_email, supplier_date_added)
         VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->bind_param("ssss", $name, $businessAddress, $contactNo, $emailAddress);

    if ($stmt->execute()) {
        // Log the action in system_log
        $userId   = $_SESSION['user_id'] ?? 0; // Adjust keys according to your session setup
        $username = $_SESSION['username'] ?? '';
        $userRole = $_SESSION['role'] ?? '';

        $actionType = 'Add Supplier';
        $description = "Added supplier: Name='$name', Address='$businessAddress', Contact='$contactNo', Email='$emailAddress'";
        $module      = 'Suppliers';
        $submodule   = 'Add Supplier';
        $result      = 'Success';
        $date        = date('Y-m-d');
        $time        = date('H:i:s');

        $logStmt = $con->prepare(
            "INSERT INTO system_log 
             (user_id, username, user_role, action_type, description, module, submodule, result, date, time) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $logStmt->bind_param(
            "isssssssss",
            $userId, $username, $userRole, $actionType, $description, $module, $submodule, $result, $date, $time
        );
        $logStmt->execute();
        $logStmt->close();

        echo "<script>alert('Supplier added successfully!');window.location='suppliers.php';</script>";
    } else {
        // Log failed attempt
        $userId   = $_SESSION['user_id'] ?? 0;
        $username = $_SESSION['username'] ?? '';
        $userRole = $_SESSION['role'] ?? '';

        $actionType = 'Add Supplier';
        $description = "Attempted to add supplier: Name='$name', Address='$businessAddress', Contact='$contactNo', Email='$emailAddress'";
        $module      = 'Suppliers';
        $submodule   = null;
        $result      = 'Failed';
        $date        = date('Y-m-d');
        $time        = date('H:i:s');

        $logStmt = $con->prepare(
            "INSERT INTO system_log 
             (user_id, username, user_role, action_type, description, module, submodule, result, date, time) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $logStmt->bind_param(
            "isssssssss",
            $userId, $username, $userRole, $actionType, $description, $module, $submodule, $result, $date, $time
        );
        $logStmt->execute();
        $logStmt->close();

        echo "<script>alert('Error adding supplier.');window.location='suppliers.php';</script>";
    }

    $stmt->close();
    $con->close();
}
?>
