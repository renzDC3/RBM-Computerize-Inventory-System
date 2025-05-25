<?php
session_start();
require 'GoogleAuthenticator.php'; 
require 'config.php'; 

$ga = new PHPGangsta_GoogleAuthenticator();

if (isset($_POST['verify'])) {
    $code = $_POST['code'];
    
    $userId = $_SESSION['two_factor_user'];
    $result = mysqli_query($con, "SELECT two_factor_secret FROM users WHERE Id='$userId'") or die("Selection Error");
    $row = mysqli_fetch_assoc($result);
    
    $secret = $row['two_factor_secret'];

    if ($ga->verifyCode($secret, $code, 2)) { 
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid 2FA code.";
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
            color: white; 
            text-shadow: 5px 5px 10px black; 
            text-align: center;
            transition: .1s;
        }
            
        .dmbutton {
          	background-color: #950606; 
          	font-size: 39.99px;
    	}
        
        .dark-mode{       
          	.dmbutton {
            	background-color: #3a3b3c;      
          	}
        }
            
        .dark-mode .login_error_message {
            color: red; 
            transition: .1s;
        }
            
        input[type=text] {
            width: 55%;
            margin-left: auto;
            margin-right: auto;
        }
            
        input[type=submit] {
            width: 25%;
            margin-left: auto;
            margin-right: auto;
        }
            
        .modal {
          display: none; 
          position: fixed; 
          z-index: 1; 
          padding-top: 100px; 
          left: 0;
          top: 0;
          width: 100%; 
          height: 100%; 
          overflow: auto; 
          background-color: rgb(0,0,0); 
          background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
          background-color: #fefefe;
          margin: auto;
          padding: 20px;
          border: 1px solid #888;
          width: 80%;
        }

        .close {
          color: #aaaaaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
        }

        .close:hover,
        .close:focus {
          color: #000;
          text-decoration: none;
          cursor: pointer;
        }
    </style>
</head>

<body>


<div class="container">
    &nbsp;
        <button class="dmbutton" onclick="toggleDarkMode()"><i class="fa-solid fa-moon"></i></button>
        <h1 style="text-align:center">Two Factor Authentication</h1><br>
    <form action="" method="post">
        <?php if (isset($error_message)): ?>
            <center style="font-size: 20px; color: white;"><div><?php echo $error_message; ?></div></center>
        <?php endif; ?>
        <center><input type="text" name="code" placeholder="Enter 2FA Code" required></center><br>
        <center><input type="submit" name="verify" value="Verify"></center><br>
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
        localStorage.setItem("dark-mode", element.classList.contains("dark-mode") ? "enabled" : "disabled");
    }

    window.onload = function() {
        if (localStorage.getItem("dark-mode") === "enabled") {
            document.body.classList.add("dark-mode");
        }
    }
</script>

</body>
</html>
