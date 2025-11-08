<?php
require_once 'session_config.php'; 
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/reportStyle.css">
</head>
<body>

<div class="topnav" id="myTopnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>

  <?php if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager') { ?>
      <a href="dashboard.php">Dashboard</a>           
  <?php } ?>

  <a href="products.php">Products</a>

  <?php if ($_SESSION['valid'] != 'Manager') { ?>
    <a href="sales.php">Sales</a>
    <a href="services.php">Services</a>
  <?php } ?> 

  <?php if ($_SESSION['valid'] === 'Admin'  or $_SESSION['valid'] === 'Manager') { ?>
      <a href="history.php">History</a>
      <a href="employees.php">Employees</a>
      <a href="suppliers.php">Suppliers</a>
      <a class="active" href="report.php">Report</a>
      <a href="cloud.php">Backup</a>
  <?php } ?>

  <?php if ($_SESSION['valid'] === 'Admin') { ?>

  <a href="system_log.php">System Log</a>

  <?php } ?>  

  <a class="logout" href="logout.php">Logout</a>

  <!-- Hamburger icon -->
  <a href="javascript:void(0);" class="icon" onclick="toggleNav()">&#9776;</a>
</div>

<div style="padding-left:16px; margin-bottom: 25px">
  <h1>Generate Report</h1>
</div>

<div class="checkout">
    <h3>Enter Custom Date</h3>
    <hr>
    <form method="POST">
      <div id="product-list" style="display: block; padding: 5px">
            <div><label for="dateStart">Start:</label></div>
            <div><input type="date" id="dateStart" name="start_date" placeholder="Start Date" title="Select start date"></div>
            <div><label for="dateEnd">End:</label></div>
            <div><input type="date" id="dateEnd" name="end_date" placeholder="End Date" title="Select end date"></div>
      </div>
      <div class="buttons">
        <div><button type="submit" class="button1" formaction="generate_sales_report.php">Generate Sales Report</button></div>
        <div><button type="submit" class="button2" formaction="generate_services_report.php">Generate Services Report</button></div>
        <div><button type="submit" class="button3" formaction="generate_inventory_report.php">Generate Inventory Report</button></div>
      </div>
    </form>
</div>

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

</html>
