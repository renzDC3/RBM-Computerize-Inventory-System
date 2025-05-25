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

        // Direct login, skip 2FA
        header("Location: dashboard.php"); // Replace 'dashboard.php' with your actual page after login
        exit();
    } else {
        $error_message = "Wrong Username or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
    <title>Login - Inventory Management System</title>

<style>
    .login_error_message {
        color:white; 
        text-shadow: 5px 5px 10px black; 
        text-align:center;
        transition: .1s;
    }

    .dmbutton {
        background-color: #950606; 
        font-size: 25px;
    }
    
    .dark-mode{
        .login_error_message {
            color:red; 
            transition: .1s;
        }
            
        .dmbutton {
          background-color: #3a3b3c;      
        }
    }
</style>
</head>

<body>

<div class="container">
    &nbsp;
    <button class="dmbutton" onclick="toggleDarkMode()"><i class="fa-solid fa-moon"></i></button>
    <form action="" method="post">
        <div class="row">
            <h1 style="text-align:center">Inventory Management System</h1>

            <div class="col">
                <center>
                    <img src="rbm_logo.jpg" alt="Logo">
                </center>
            </div>

            <div class="col">
                <?php if (isset($error_message)): ?>
                    <div class="login_error_message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <input class="usernameInputBox" type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" name="submit" value="Login">
            </div>
        </div>
    </form>
</div>

<div class="bottom-container">
    <div class="row">
        <a href="#" style="color:white" class="btn"></a>
    </div>
</div>

<script>
    function toggleDarkMode() {
        var element = document.body;
        element.classList.toggle("dark-mode");
        if (element.classList.contains("dark-mode")) {
            localStorage.setItem("dark-mode", "enabled");
        } else {
            localStorage.setItem("dark-mode", "disabled");
        }
    }

    window.onload = function() {
        if (localStorage.getItem("dark-mode") === "enabled") {
            document.body.classList.add("dark-mode");
        }
    }
</script>

</body>
</html>
