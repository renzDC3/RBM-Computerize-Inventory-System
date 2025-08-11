<?php
session_start();
include("config.php");

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $result = mysqli_query($con, "SELECT * FROM users WHERE Username='$username' AND Password='$password'") or die("Selection Error");
    $row = mysqli_fetch_assoc($result);

    if (is_array($row) && !empty($row)) {
        $_SESSION['valid'] = $row['Username'];
        $_SESSION['id'] = $row['Id'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Wrong Username or Password!";
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

<h2>Inventory Management System</h2>

<form action="" method="post">
  <div class="imgcontainer">
    <img src="images/rbm_logo.jpg" alt="Avatar" class="avatar">
  </div>

  <?php if (isset($error_message)): ?>
    <div class="login_error_message">
        <?php echo $error_message; ?>
    </div>
  <?php endif; ?>

  <div class="container">
    <label for="uname"><b>Username</b></label>
    <input type="text" name="username" placeholder="Username" required>

    <label for="psw"><b>Password</b></label>
    <input type="password" name="password" placeholder="Password" required>
        
    <button type="submit" name="submit" value="Login">Login</button>
    
  </div>

</form>

</body>
</html>
