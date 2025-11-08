<?php 
require_once 'session_config.php'; 
require_once 'csrf.php';
require 'config.php';
require 'GoogleAuthenticator.php';

function log_system_action($con, $user_id, $username, $user_role, $action_type, $description, $module, $submodule = null, $result = 'Pending') {
    date_default_timezone_set('Australia/Perth');

    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    $stmt = $con->prepare("INSERT INTO system_log 
        (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssss", 
        $user_id, 
        $username, 
        $user_role, 
        $action_type, 
        $description, 
        $module, 
        $submodule, 
        $result, 
        $current_date, 
        $current_time
    );
    $stmt->execute();
    $stmt->close();
}

$ga = new PHPGangsta_GoogleAuthenticator();

if (isset($_POST['verify'])) {
    // Keep CSRF protection as-is
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $code = $_POST['code'];

    if (!isset($_SESSION['two_factor_user']) && !isset($_SESSION['id'])) {
        die("Session expired. Please log in again.");
    }

    $userId = $_SESSION['two_factor_user'] ?? $_SESSION['id'];

    $stmt = $con->prepare("SELECT Username, two_factor_secret FROM users WHERE Id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        die("User not found.");
    }

    // ----------------------------
    // TESTING ONLY: 2FA BYPASS START
    // ----------------------------
    // This bypass makes the form accept ANY input in the `code` field and proceed.
    // IMPORTANT: Remove this bypass before deploying to production.
    unset($_SESSION['two_factor_user']); 

    // Fetch user role
    $roleStmt = $con->prepare("SELECT role FROM users WHERE Id = ?");
    $roleStmt->bind_param("i", $userId);
    $roleStmt->execute();
    $roleRes = $roleStmt->get_result();
    $roleRow = $roleRes->fetch_assoc();
    $user_role = $roleRow['role'] ?? 'Unknown';
    $roleStmt->close();

    // Log that 2FA was bypassed for testing
    log_system_action($con, $userId, $row['Username'], $user_role, '2FA', '2FA bypassed for testing. Any code accepted.', 'Login', '2FA', 'Bypassed');

    if ($row['Username'] != 'Admin' && $row['Username'] != 'Manager') {
        header("Location: products.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
    // --------------------------
    // TESTING ONLY: 2FA BYPASS END
    // --------------------------
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/indexStyle.css">
</head>
<body>

<h2>Two Factor Authentication (Testing)</h2>

<form action="" method="post">
    <?php if (isset($error_message)): ?>
        <div class="login_error_message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="container">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="text" name="code" placeholder="Enter 2FA Code (any value accepted for testing)" required>
        <button type="submit" name="verify" value="Verify">Submit</button>
    </div>
</form>

</body>
</html>
