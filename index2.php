<?php
    SESSION_START();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style/all.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
<style>

.bottom-container{
    background-color: #950606;
}


body {
  font-family: Metropolis, Arial, Helvetica, sans-serif;
  color:white;
}

* {
  box-sizing: border-box;
}

.container {
  position: relative;
  border-radius: 5px;
  background-color: #950606;
  padding: 20px 0 30px 0;
  margin-top: 25px;
  margin-bottom: 25px;
  margin-left: 25px;
  margin-right: 25px;
} 

input,
.btn {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 4px;
  margin: 5px 0;
  opacity: 0.85;
  display: inline-block;
  font-size: 17px;
  line-height: 20px;
  text-decoration: none; 
}

input:hover,
.btn:hover {
  opacity: 1;
}

.fb {
  background-color: #3B5998;
  color: white;
}

.twitter {
  background-color: #55ACEE;
  color: white;
}

.google {
  background-color: #dd4b39;
  color: white;
}

input[type=submit] {
  background-color: #f1f1f1;
  color: white;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #45a049;
}

.col {
  float: left;
  width: 50%;
  margin: auto;
  padding: 0 50px;
  margin-top: 6px;
}

.row:after {
  content: "";
  display: table;
  clear: both;
}

.vl {
  position: absolute;
  left: 50%;
  transform: translate(-50%);
  border: 2px solid #ddd;
  height: 175px;
}

.vl-innertext {
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  background-color: #f1f1f1;
  border: 1px solid #ccc;
  border-radius: 50%;
  padding: 8px 10px;
}

.hide-md-lg {
  display: none;
}

.bottom-container {
  text-align: center;
  background-color: #666;
  border-radius: 0px 0px 4px 4px;
}

img{
    box-shadow: 8px 8px 15px rgba(0, 0, 0, 0.5);
}

@media screen and (max-width: 650px) {
  .col {
    width: 100%;
    margin-top: 0;
  }
  .vl {
    display: none;
  }
  .hide-md-lg {
    display: block;
    text-align: center;
  }
}
</style>
</head>
<body>

<div class="container"  style="border: 2px solid black;">
  <form action="config.php" method="post">
    <div class="row">
    <!--
      <div class="vl">
        <span class="vl-innertext">or</span>
      </div>
-->

      <div class="col" style="padding-top: 30px; margin-left: 70px; margin:auto;">
      <img src="rbm_logo.jpg" alt="RBM Logo" height="250" width="250" style="border-color: 2px solid black;">
      <h1 style="text-shadow: 2px 2px 8px black">Inventory Management System</h1>
      
      </div>

      <div class="col" style="padding-top: 50px;">
        <div class="hide-md-lg">
          <p>Or sign in manually:</p>
        </div>

         <div class = "container">
                <div class="form-box box">
                    <?php
                    include("config.php");
                    if(isset($_POST['submit'])){
                        $username = mysqli_real_escape_string($con, $_POST['username']);
                        $password = mysqli_real_escape_string($con, $_POST['password']);

                        $result = mysqli_query($con, "SELECT * FROM users WHERE Username='$username' AND Password='$password'") or die("Selection Error");
                        $row = mysqli_fetch_assoc($result);

                        if(is_array($row) && ! empty($row)){
                            //$_SESSION['valid'] = $row['Email'];
                            $_SESSION['valid'] = $row['Username'];
                            //$_SESSION['age'] = $row['Age'];
                            $_SESSION['id'] = $row['Id'];
                        }else{
                            echo "<div class='message' style='background-color: white; color: black; border: 1px solid black; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                                <p>Wrong Username or Password</p>
                                <div> <br>";
                            echo "<a href='javascript:self.history.back()'><button class='btn' style='background-color: white;'>Go back</button></a>";
                        }
                        if(isset($_SESSION['valid'])){
                            header("Location: dashboard.php");
                        }
                    }else{
                    ?>
                    <form action="" method="post">
                        <!--
                        <div>
                            <header><center>Login</center></header>
                        </div>
                        -->
                        <br>

                        <div class="field input">
                            <!--<label for="email">Email<br></label>-->
                            <input type="text" style="border:1px solid black;" name="username" id="username" placeholder="Username" required><br>
                        </div>

                        <div class="field input">
                            <!--<label for="password">Password<br></label>-->
                            <input type="password" style="border: 1px solid black;" name="password" id="password" placeholder="Password"required><br>
                        </div>

                        <div class="field">
                            <button type="submit" style="border: 1px solid black;" name="submit" class="btn">
                            Login <i class="fas fa-sign-in-alt"></i> 
                            </button>
                        </div>
                        <br>
                        <!--
                        <div class="links" style="color:white">
                            <center>Don't have an account? <a href="register.php">Sign Up Now!</a></center>
                        </div>
                        -->
                        
                    </form>
             </div>
            
            <?php } ?>

            
        </div>
      </div>
      
    </div>
  </form>
</div>


<div class="bottom-container" style="margin-left: 25px; margin-right: 25px; border: 2px solid black;">
  <div class="row">
    <div class="col1" >
      <a href="register.php" style="color:white" class="btn"> Sign up</a>
    </div>
  </div>
</div>

</body>
</html>
