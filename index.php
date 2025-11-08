<?php
require_once 'session_config.php'; 
require 'config.php';
require_once 'csrf.php';
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

$ip = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION['login_attempts'][$ip])) {
    $_SESSION['login_attempts'][$ip] = ['count' => 0, 'time' => time()];
}

if (time() - $_SESSION['login_attempts'][$ip]['time'] > 900) {
    $_SESSION['login_attempts'][$ip] = ['count' => 0, 'time' => time()];
}

if ($_SESSION['login_attempts'][$ip]['count'] >= 5) {
    die("Too many failed login attempts. Try again in 15 minutes.");
}

if (isset($_POST['submit'])) {
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT Id, Username, Password, role, two_factor_secret FROM users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // user exists
        if (password_verify($password, $row['Password'])) {
            session_regenerate_id(true);

            $_SESSION['valid'] = $row['Username'];
            $_SESSION['id'] = $row['Id'];

            // Log successful password verification
            log_system_action($con, $row['Id'], $row['Username'], $row['role'], 'Login', 'Password verified successfully.', 'Login', null, 'Success');

            if (!empty($row['two_factor_secret'])) {
                $_SESSION['two_factor_user'] = $row['Id'];
                
                // Log 2FA step start
                log_system_action($con, $row['Id'], $row['Username'], $row['role'], '2FA', 'Redirected to 2FA verification.', 'Login', '2FA', 'Pending');

                header("Location: verify_2fa.php");
                exit();
            } else {
                $ga = new PHPGangsta_GoogleAuthenticator();
                $secret = $ga->createSecret();
                $update = $con->prepare("UPDATE users SET two_factor_secret = ? WHERE Id = ?");
                $update->bind_param("si", $secret, $row['Id']);
                $update->execute();

                // Log 2FA setup
                log_system_action($con, $row['Id'], $row['Username'], $row['role'], '2FA Setup', 'New 2FA secret generated for user.', 'Login', '2FA', 'Success');

                $qrCodeUrl = $ga->getQRCodeGoogleUrl('YourAppName', $secret);

                echo '<center><br><br><h2>Scan this QR Code with Google Authenticator</h2>';
                echo '<img src="' . $qrCodeUrl . '" />';
                echo '<form action="verify_2fa.php" method="post">
                        <input type="hidden" name="setup" value="1">
                        <input type="text" name="code" placeholder="Enter 2FA Code" required>
                        <button type="submit" name="verify">Verify</button>
                      </form></center>';
                exit();
            }
        } else {
            $_SESSION['login_attempts'][$ip]['count']++;
            $error_message = "Wrong Username or Password";

            // Log wrong password
            log_system_action($con, $row['Id'], $row['Username'], $row['role'], 'Login', 'User entered incorrect password.', 'Login', null, 'Failed');
        }
    } else {
        $_SESSION['login_attempts'][$ip]['count']++;
        $error_message = "Wrong Username or Password";

        // Log invalid username attempt (no user_id)
        log_system_action($con, 0, $username, 'Unknown', 'Login', 'Login attempt with invalid username.', 'Login', null, 'Failed');
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/indexStyle.css">
</head>
<body>

<h2>Sales and Inventory Management System</h2>

<form action="" method="post">
  <div class="imgcontainer">
    <img src="images/rbm_logo.jpg" alt="Avatar" class="avatar">
  </div>

  <?php if (isset($error_message)): ?>
    <div class="login_error_message"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <div class="container">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

    <label><b>Username</b></label>
    <input type="text" name="username" placeholder="Username" required>

    <label><b>Password</b></label>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="submit" value="Login">Login</button>
  </div>
</form>

</body>
</html>
