<?php
require_once 'session_config.php';
require 'config.php';

// Check if session exists and contains user info
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $user_role = $_SESSION['role'];

    // Prepare log data
    $action_type = 'Logout';
    $description = 'User logged out successfully.';
    $module = 'Logout';
    $submodule = 'Logout';
    $result = 'Success';
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert log record
    $stmt = $conn->prepare("
        INSERT INTO system_log 
        (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "isssssssss",
        $user_id,
        $username,
        $user_role,
        $action_type,
        $description,
        $module,
        $submodule,
        $result,
        $date,
        $time
    );
    $stmt->execute();
    $stmt->close();
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
?>
