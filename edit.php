<?php
session_start(); 

$con = mysqli_connect("localhost", "root", "p", "weblog");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style2.css">
    <script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php"><i class="fa-solid fa-house"></i></a></p>
        </div>
        <div class="right-links">
            <a href='edit.php?Id=$'res_Id' class='changeProf'>Change Profile</a>
            <a href="logout.php"><button class="btn">Log Out</button></button></a>
        </div>
    </div>

    <div class = "container">
        <div class="form-box box">
            <?php
                if(isset($_POST['submit'])){
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $age = $_POST['age'];
                    
                    if(isset($_POST['id']) && !empty($_POST['id'])){
                        $id = $_POST['id'];

                        $edit_query = mysqli_query($con, "UPDATE users SET Username='$username', Email='$email', Age='$age' WHERE Id=$id") or die(mysqli_error($con));
                        
                        if($edit_query){
                            echo "<div class='message'>
                                <h1>Profile Updated!</h1>
                                <div> <br>";
                            echo "<a href='home.php'><button class='btn'>Go Home</button></a>";
                            exit();
                        }
                    } else {
                        echo "User ID is missing.";
                    }
                } else {
                    $id = $_SESSION['id'];

                    $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

                    while($result = mysqli_fetch_assoc($query)){
                        $res_Uname = $result['Username'];
                        $res_Email = $result['Email'];
                        $res_Age = $result['Age'];        
                    }
                }
            ?>
            <form action="" method="post">
                <div>
                    <header>Change Profile</header>
                </div>

                <div class="field input">
                    <label for="username">Username<br></label>
                    <input type="text" name="username" id="username" value="<?php echo $res_Uname; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email<br></label>
                    <input type="text" name="email" id="email" value="<?php echo $res_Email; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">Age<br></label>
                    <input type="number" name="age" id="age" value="<?php echo $res_Age; ?>" autocomplete="off" required>
                </div>

                <!-- Hidden field to pass the user ID -->
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="field">
                    <input type="submit" name="submit" class="btn" value="Update" required>
                </div>
            </form>
        </div>
        <?php mysqli_close($con); ?>
    </div>
</body>
</html>