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
        $error_message = "Your username or password may be incorrect!";
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


<form action="" method="post">
  <div class="imgcontainer">
        <h2 class='textSignin'>Inventory System</h2>

    <img src="images/rbm_logo.png" alt="Avatar" class="avatar">
    
  </div>


    <h1>sign in</h1>
      <?php if (isset($error_message)): ?>
    <div class="login_error_message">
        <?php echo $error_message; ?>
    </div>
  <?php endif; ?>
  <div class="container">
  <div class="input-group">
    <input type="text" name="username" placeholder=" " required>
    <label>USERNAME</label>
  </div>

  <div class="input-group">
    <input type="password" name="password" placeholder=" " required>
    <label>PASSWORD</label>
  </div>

  <button type="submit" name="submit" value="Login">
<span style="font-size:25px;">&#10149;</span></button>
</div>

</form>


</body>
</html>
