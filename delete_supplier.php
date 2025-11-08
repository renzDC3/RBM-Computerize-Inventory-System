<?php
require_once 'session_config.php';
require_once 'csrf.php';  
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

// Check CSRF token if your csrf.php implements one
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    die("CSRF validation failed");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Id'])) {
    $supplier_id = intval($_POST['Id']); // sanitize input

    // Fetch supplier details
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Supplier not found.");
    }

    $supplier = $result->fetch_assoc();
    $stmt->close();

    // Insert into delete_supplier_history
    $stmt = $conn->prepare("INSERT INTO delete_supplier_history (supplier_name, supplier__business_address, supplier_contact_no, supplier_email, supplier_date_added, supplier_date_deleted) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param(
        "sssss",
        $supplier['supplier_name'],
        $supplier['supplier__business_address'],
        $supplier['supplier_contact_no'],
        $supplier['supplier_email'],
        $supplier['supplier_date_added']
    );
    $stmt->execute();
    $stmt->close();

    // Delete from suppliers
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $stmt->close();

    // Insert into system_log
    $user_id = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? '';
    $user_role = $_SESSION['role'] ?? '';

    $action_type = "Delete";
    $description = "Deleted supplier: " . $supplier['supplier_name'];
    $module = "Suppliers";
    $submodule = NULL;
    $result_status = "Success";
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $stmt = $conn->prepare("INSERT INTO system_log (user_id, username, user_role, action_type, description, module, submodule, result, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isssssssss",
        $user_id,
        $username,
        $user_role,
        $action_type,
        $description,
        $module,
        $submodule,
        $result_status,
        $date,
        $time
    );
    $stmt->execute();
    $stmt->close();

    // Redirect back or show success message
    header("Location: suppliers_list.php?msg=deleted");
    exit;
} else {
    die("Invalid request.");
}
?>
