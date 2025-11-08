<?php
require_once 'session_config.php';
require_once 'csrf.php';
require 'config.php'; // contains $con for DB connection

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

// Ensure request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: services.php");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    die("Invalid CSRF token");
}

// Get and sanitize form inputs
$description   = trim($_POST['description']);
$price         = isset($_POST['price']) ? (float) $_POST['price'] : 0;
$customer_cash = isset($_POST['customer_cash']) ? (float) $_POST['customer_cash'] : 0;

// Validate inputs
if (empty($description) || $price <= 0 || $customer_cash <= 0) {
    die("Invalid input values");
}

if ($customer_cash < $price) {
    die("Customer cash must be greater than or equal to service price.");
}

// Calculate change
$change = $customer_cash - $price;

// Get current logged-in user's ID from session
if (!isset($_SESSION['id'])) {
    die("User session invalid.");
}
$userId = (int) $_SESSION['id'];

// --- Retrieve username and role for logging ---
$userQuery = $con->prepare("SELECT Username, role FROM users WHERE Id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    die("User not found for logging.");
}

$userData = $userResult->fetch_assoc();
$username = $userData['Username'];
$userRole = $userData['role'];
$userQuery->close();

// --- Insert into services table ---
$stmt = $con->prepare("
    INSERT INTO services 
    (services_description, services_price, services_customer_cash, services_customer_change, services_date, Id)
    VALUES (?, ?, ?, ?, NOW(), ?)
");

if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param("sdddi", $description, $price, $customer_cash, $change, $userId);

if ($stmt->execute()) {
    $service_id = $stmt->insert_id;

    // --- Log the action into system_log with date and time ---
    $logStmt = $con->prepare("
        INSERT INTO system_log 
        (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE(), CURRENT_TIME())
    ");

    if ($logStmt) {
        $action_type = "CREATE";
        $log_description = "Added new service (ID: $service_id) - '$description' with price ₱$price and change ₱$change.";
        $module = "Services";
        $submodule = "Add Service";
        $result = "Success";

        $logStmt->bind_param(
            "isssssss",
            $userId,
            $username,
            $userRole,
            $action_type,
            $log_description,
            $module,
            $submodule,
            $result
        );

        $logStmt->execute();
        $logStmt->close();
    } else {
        error_log("Failed to prepare system log statement: " . $con->error);
    }

    // Open invoice in a new window using JavaScript
    echo '<script type="text/javascript">
            window.open("generate_service_invoice.php?service_id=' . $service_id . '", "_blank");
            window.location.href = "services.php"; // redirect current page back to services list
        </script>';
    exit();

} else {
    // Log failure as well
    $logStmt = $con->prepare("
        INSERT INTO system_log 
        (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE(), CURRENT_TIME())
    ");

    if ($logStmt) {
        $action_type = "Service";
        $log_description = "Failed to add new service - '$description'. Error: " . $stmt->error;
        $module = "Services";
        $submodule = "Services";
        $result = "Failed";

        $logStmt->bind_param(
            "isssssss",
            $userId,
            $username,
            $userRole,
            $action_type,
            $log_description,
            $module,
            $submodule,
            $result
        );

        $logStmt->execute();
        $logStmt->close();
    }

    die("Error inserting service: " . $stmt->error);
}

$stmt->close();
$con->close();
?>
