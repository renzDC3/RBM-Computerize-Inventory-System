<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style2.css">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
    <title>Register</title>

    <style>
        body {
            color: white;
        }
    </style>
</head>
<body>
    &nbsp; <button class="dmbutton" onclick="myFunction()"><i class="fa-solid fa-moon"></i></button>
        
    <br>
    <div class="container">
        <div class="form-box box">

        <?php
            require_once 'session_config.php'; 
            require 'config.php';

            function isValidPassword($password) {
                
                if (strlen($password) <= 13) {
                    return false;
                }

                if (!preg_match('/[A-Z]/', $password)) {
                    return false;
                }

                if (!preg_match('/[a-z]/', $password)) {
                    return false;
                }

             
                if (!preg_match('/\d/', $password)) {
                    return false;
                }

                
                if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                    return false;
                }

                return true;
            }

            if (isset($_POST['submit'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];

             
                if (!isValidPassword($password)) {
                    echo "<div class='message' style='background-color: white; color: black; border: 1px solid black; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                            <p>Password must be more than 13 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.</p>
                            <div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go back</button></a>";
                } else {
                    $verify_query = mysqli_query($con, "SELECT Username FROM users WHERE Username='$username'");

                    if (mysqli_num_rows($verify_query) != 0) {
                        echo "<div class='message' style='background-color: white; color: black; border: 1px solid black; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                                <p>This username is already taken, try another one please.</p>
                                <div> <br>";
                        echo "<a href='javascript:self.history.back()'><button class='btn'>Go back</button></a>";
                    } else {
                        mysqli_query($con, "INSERT INTO users (Username, Password) VALUES ('$username', '$password')") or die("Error Occurred");

                        echo "<div class='message' style='background-color: white; color: black; border: 1px solid black; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                                <p>Registration successful!</p>
                                <div> <br>";
                        echo "<a href='index.php'><button class='btn'>Login now</button></a>";
                    }
                }
            } else {
        ?>
            <form action="" method="post">
                <div>
                    <header><center>Sign Up</center></header>
                </div>

                <div class="field input">
                    <label for="username">Username<br></label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="field input">
                    <label for="password">Password<br></label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" style="background-color:white;" name="submit" class="btn" value="Register" required>
                </div>
                <div class="links">
                    <center>Already signed up? <a href="index.php">Log in</a></center>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
        
        
    <script>
      function myFunction() {
         var element = document.body;
         element.classList.toggle("dark-mode");
      }
    </script>
</body>
</html>
