<?php
    require_once 'session_config.php'; 
    require_once 'csrf.php';
    require 'config.php';
    if (!isset($_SESSION['valid'])) {
        header("Location: index.php");
        exit();
    }

    $query = "SELECT product_id, name, barcode, price FROM products";
    $result = mysqli_query($con, $query);
    $products = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $products[$row['barcode']] = [
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'product_id' => $row['product_id']
        ];
    }

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/servicesStyle.css">
</head>
<body>

<meta name="csrf_token" content="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

<div class="topnav" id="myTopnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>

  <?php if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager') { ?>
      <a href="dashboard.php">Dashboard</a>           
  <?php } ?>

  <a href="products.php">Products</a>

  <?php if ($_SESSION['valid'] != 'Manager') { ?>
    <a href="sales.php">Sales</a>
    <a class="active" href="services.php">Services</a>
  <?php } ?> 

  <?php if ($_SESSION['valid'] === 'Admin'  or $_SESSION['valid'] === 'Manager') { ?>
      <a href="history.php">History</a>
      <a href="employees.php">Employees</a>
      <a href="suppliers.php">Suppliers</a>
      <a href="report.php">Report</a>
      <a href="cloud.php">Backup</a>
  <?php } ?>

  <?php if ($_SESSION['valid'] === 'Admin') { ?>

  <a href="system_log.php">System Log</a>

  <?php } ?>  

  <a class="logout" href="logout.php">Logout</a>

  <!-- Hamburger icon -->
  <a href="javascript:void(0);" class="icon" onclick="toggleNav()">&#9776;</a>
</div>

<div class="services">
            <h1>Services</h1>
            <form action="add_service.php" method="POST" enctype="multipart/form-data">

            <!--
            <div class="servicesHorizontal">
                
            </div>
      -->
            
            
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <label for="description"><b>Service Description</b></label>
            <input style="width: 100%; margin-top: 5px" type="text" placeholder="Enter Service Description" name="description" required>             
            
              
            <div class="servicesHorizontal" style="margin-top: 19.99px">
                <div><label for="price"><b>Service Price</b></label></div>
                <div><input type="number" min="0" max="1000" placeholder="Enter Price" name="price" required ></div>

                <div><label for="customer_cash"><b>Customer's Cash</b></label></div>
                <div><input type="number" min="1.00" max="999999.99" value="1.00" placeholder="Enter Customer's Cash" name="customer_cash" required></div>

                <div><input type="hidden" name="change" id="change"></div>
            </div>           
            <hr>
            <div class="confirmOrCancel">
              <button type="submit" class="confirm">Confirm</button>
            </div>
        </form>
      </div>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script>
    alert("Service has been successfully added!");
</script>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const priceInput = document.getElementById("price");
    const cashInput  = document.getElementById("customer_cash");
    const changeInput = document.getElementById("change");

    function updateChange() {
        const price = parseFloat(priceInput.value) || 0;
        const cash  = parseFloat(cashInput.value) || 0;
        const change = cash - price;
        changeInput.value = change.toFixed(2); // store with 2 decimals
    }

    priceInput.addEventListener("input", updateChange);
    cashInput.addEventListener("input", updateChange);
});
</script>

<script>
function toggleNav() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>

