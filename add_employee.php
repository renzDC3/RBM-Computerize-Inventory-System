<?php
require_once 'session_config.php';
require_once 'config.php';
require_once 'csrf.php';

date_default_timezone_set('Australia/Perth');

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Input validation
    if (!preg_match('/^[a-zA-Z]+$/', $fname) || !preg_match('/^[a-zA-Z]+$/', $lname)) {
        die("Invalid name format.");
    }

    if (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $username)) {
        die("Invalid username format. Only letters, numbers, and underscore allowed.");
    }

    if (strcasecmp($username, 'admin') === 0) {
        die("The username 'Admin' is not allowed.");
    }

    if (strlen($password) <= 13 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/\d/', $password) ||
        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        die("Password must be >13 characters, and include upper, lower, digit, special char.");
    }

    // Check if username already exists
    $stmt = $con->prepare("SELECT Username FROM users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Username already taken.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $con->prepare(
        "INSERT INTO users (first_name, last_name, date_joined, Username, Password)
         VALUES (?, ?, NOW(), ?, ?)"
    );
    $stmt->bind_param("ssss", $fname, $lname, $username, $hashedPassword);

    $actionType = "Add Employee";
    $module = "Employees";
    $submodule = "Add Employee";

    // Details for logging who performed the action
    $loggedUserId = $_SESSION['id'] ?? 0;
    $loggedUsername = $_SESSION['username'] ?? 'Unknown';
    $loggedRole = $_SESSION['role'] ?? 'Unknown';
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    if ($stmt->execute()) {
        $result = "Success";
        $description = "Created new employee account for {$fname} {$lname} (username: {$username}).";

        echo "<script>alert('Employee created successfully!');window.location='employees.php';</script>";
    } else {
        $result = "Failure";
        $description = "Attempted to create employee account for {$fname} {$lname} (username: {$username}) but failed: " . $stmt->error;

        echo "<script>alert('Error creating employee.');window.location='employees.php';</script>";
    }

    // Log the action to system_log table including date & time
    $logStmt = $con->prepare("
        INSERT INTO system_log 
            (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $logStmt->bind_param(
        "isssssssss",
        $loggedUserId,
        $loggedUsername,
        $loggedRole,
        $actionType,
        $description,
        $module,
        $submodule,
        $result,
        $currentDate,
        $currentTime
    );
    $logStmt->execute();
    $logStmt->close();

    $stmt->close();
}
?>
