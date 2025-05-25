<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/all.css">
<link rel="stylesheet" href="styles/dashboardStyle.css">

</head>

<style>
    i {
      color: #121212;
    } 

    .sidenavIcon {
      color: white;
    }

  .dark-mode { 
    i {
      color: white;
    }
    
    .modal-content {
      background-color: #3a3b3c;
      color: white;      
    }
  }

  .charts-card {
    width: 97.5%;
    margin-top: 10px;
    margin-left: auto;
    margin-right: auto;
  }
        
.modal {
  display: none; 
  position: fixed; 
  z-index: 1; 
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%;
  overflow: auto; 
  background-color: rgb(0,0,0); 
  background-color: rgba(0,0,0,0.4); 
  padding-top: 60px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto 15% auto; 
  border: 1px solid #888;
  width: 50%; 
  border-radius: 8px;
  color: black;
  float: center;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}


  @media only screen and (max-width: 600px) {
    .charts-card {
    width: 90.5%;
    margin-top: 10px;
    margin-left: auto;
    margin-right: auto;
  }
  }
  
</style>


<body>

<div class="topnav">
    &nbsp;
    <span style="font-size:30px;cursor:pointer;color:white" onclick="openNav()">&#9776;</span>
    &nbsp;
    <button class="dmbutton" onclick="toggleDarkMode()"><i class="fa-solid fa-moon"></i></button>
    <a href="logout.php" class="split"><i class="fa-solid fa-right-to-bracket"></i> Logout</a>
</div>

<div id="mySidenav" class="sidenav">
  <center><img src="rbm_logo.jpg" alt="RBM Logo" height="150" width="150"></center>
  <a href="javascript:void(0)" class="sidenavIcon closebtn" onclick="closeNav()">&times;</a>
  <a href="dashboard.php"><i class="sidenavIcon fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="inventory_list.php"><i class="fa-solid sidenavIcon fa-box-open"></i> Products</a>
  <a href="ordering.php"><i class="sidenavIcon fa-solid fa-cart-shopping"></i> Ordering</a>
  <a href="add_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Adding History</a>
  <a href="stock_in_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Stock-In History</a>
  <a href="edit_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Editing History</a>
  <a href="adjustment_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Adjustment History</a>
  <a href="delete_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Deletion History</a>
  <a href="order_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Ordering History</a>
</div>

<?php

			

            $id = $_SESSION['id'];
            $query = mysqli_query($con,"SELECT*FROM users WHERE Id=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Username = $result['Username'];
            }
        ?>

<div style="padding-left:16px">
  <h1>Welcome Back, <?php echo "$res_Username"?></h1>
  <p>Take a look at the current stats of our inventory system.</p>
  
</div>


<hr class="hrRed">

<div class="topdash">
  <h1>
    Dashboard
  </h1>
  <h4>
    (Monthly Stats)
  </h4>
  <center>
          
<div class="insidetopdash" id="showModalBtn">
    <h3><i class="fa-solid fa-box"></i> Total Unique Products</h3>
    <h1 style="color: #950606;">
        <?php
          $sql = "SELECT COUNT(*) AS total FROM products";
          $result = $con->query($sql);

          if ($result && $row = $result->fetch_assoc()) {
              echo $row['total'];
          } else {
              echo "Error retrieving count";
          }
        ?>
    </h1>
</div>

<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <center><h2>Total Unique Products</h2>
        <p>Here you can provide additional details or data about the products.</p></center>
        
        <?php
        $sql = "SELECT name FROM products";
        $result = $con->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<center><h3>" . htmlspecialchars($row['name']) . "</h3></center>";
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
</div>

<div id="lowStockModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeLowStockModal">&times;</span>
        <h2>Low Stock Products</h2>
        <p>Here you can list products with low stock (less than 5 units).</p>
        
        <?php
        $query = "SELECT * FROM products WHERE quantity < 5";
        $result = mysqli_query($con, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<h3>{$row['name']}</h3>";
            }
        } else {
            echo "Error: " . mysqli_error($con);
        }
        ?>
    </div>
</div>

<div id="outOfStockModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeOutOfStockModal">&times;</span>
        <center>
        <h2>Out of Stock Products</h2>
        <p>Here you can list products that are out of stock.</p>
        
        <?php
        $query = "SELECT * FROM products WHERE quantity < 1";
        $result = mysqli_query($con, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<center><h3>{$row['name']}</h3></center>";
            }
        } else {
            echo "Error: " . mysqli_error($con);
        }
        ?>
    </div>
</div>


<div class="insidetopdash" id="lowStockBtn">
    <h3><i class="fa-solid fa-arrow-trend-down"></i> Low Stock</h3>
    <h1 style="color: #950606;">
        <?php
            $sql = "SELECT COUNT(*) AS total FROM products WHERE quantity < 5";
            $result = $con->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                echo $row['total'];
            } else {
                echo "Error retrieving count";
            }
        ?>
    </h1>
</div>

<div class="insidetopdash" id="outOfStockBtn">
    <h3><i class="fa-solid fa-sack-xmark"></i> Out of Stock</h3>
    <h1 style="color: #950606;">
        <?php
            $sql = "SELECT COUNT(*) AS total FROM products WHERE quantity < 1";
            $result = $con->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                echo $row['total'];
            } else {
                echo "Error retrieving count";
            }
        ?>
    </h1>
</div>

<br>

<div class="lowerInsideTopDash">
    <h3><i class="fa-solid fa-receipt"></i> Total Sales</h3>
    <h1 style="color: #950606;">
        <?php
        $currentYear = date('Y');
        $currentMonth = date('m'); 

        $startDate = "$currentYear-$currentMonth-01 00:00:00";
        $endDate = "$currentYear-$currentMonth-" . date('t') . " 23:59:59"; 

        $sql = "SELECT COUNT(*) AS total FROM orders WHERE order_date BETWEEN '$startDate' AND '$endDate'";
        $result = $con->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            echo $row['total'];
        } else {
            echo "Error retrieving count";
        }
        ?>
    </h1>
</div>

<div class="lowerInsideTopDash">
    <h3><i class="fa-solid fa-money-bill-wave"></i> Revenue</h3>
    <h1 style="color: #950606;">
        <?php
            $currentMonth = date('m'); 
            $currentYear = date('Y');  

            $startDate = $currentYear . '-' . $currentMonth . '-01 00:00:00'; 
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate)); 

            $sql = "SELECT SUM(orders_total) AS total FROM orders WHERE order_date BETWEEN '$startDate' AND '$endDate'";
            $result = $con->query($sql);

            if ($result && $row = $result->fetch_assoc()) {
                echo $row['total'] ? $row['total'] : "0.00";
            } else {
                echo "Error retrieving revenue data";
            }
        ?>
    </h1>
</div>

<div class="lowerInsideTopDash">
    <h3><i class="fa-solid fa-money-bill-trend-up"></i> Net Profit</h3>
    <h1 style="color: #950606;">
        <?php
            $currentYear = date('Y');
            $currentMonth = date('m');

            $startDate = "$currentYear-$currentMonth-01"; 
            $endDate = date("Y-m-t", strtotime($startDate)); 

            $sql = "SELECT ROUND(SUM(orders_total) * 0.75, 2) AS total FROM orders WHERE order_date BETWEEN '$startDate' AND '$endDate'";
            $result = $con->query($sql);

            if ($result && $row = $result->fetch_assoc()) {
                echo $row['total'] ? $row['total'] : "0.00";
            } else {
                echo "Error retrieving count";
            }
        ?>
    </h1>
</div>

</center>

</div>

<div class="charts-card">
    <h2 class="charts-title">Top 5 Best-Selling Products</h2>
    <div id="bar-chart"></div>
</div>

<br>

<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>

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

<script>
  const xValues = [50,60,70,80,90,100,110,120,130,140,150];
  const yValues = [7,8,8,9,9,9,10,11,14,14,15];
  
  new Chart("myChart", {
    type: "line",
    data: {
      labels: xValues,
      datasets: [{
        fill: false,
        lineTension: 0,
        data: yValues
      }]
    },
    options: {
      legend: {display: false},
      scales: {
        yAxes: [{ticks: {min: 6, max:16}}],
      }
    }
  });
</script>

<script>
function myFunction2() {
  var popup = document.getElementById("myPopup");
  popup.classList.toggle("show");
}
</script>

<script>
var modalProduct = document.getElementById("productModal");
var btnProduct = document.getElementById("showModalBtn");
var spanProduct = document.getElementsByClassName("close")[0];

btnProduct.onclick = function() {
    modalProduct.style.display = "block";
}

spanProduct.onclick = function() {
    modalProduct.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modalProduct) {
        modalProduct.style.display = "none";
    }
}

var modalLowStock = document.getElementById("lowStockModal");
var btnLowStock = document.getElementById("lowStockBtn");
var spanLowStock = document.getElementById("closeLowStockModal");

btnLowStock.onclick = function() {
    modalLowStock.style.display = "block";
}

spanLowStock.onclick = function() {
    modalLowStock.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modalLowStock) {
        modalLowStock.style.display = "none";
    }
}

var modalOutOfStock = document.getElementById("outOfStockModal");
var btnOutOfStock = document.getElementById("outOfStockBtn");
var spanOutOfStock = document.getElementById("closeOutOfStockModal");

btnOutOfStock.onclick = function() {
    modalOutOfStock.style.display = "block";
}

spanOutOfStock.onclick = function() {
    modalOutOfStock.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modalOutOfStock) {
        modalOutOfStock.style.display = "none";
    }
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.53.0/apexcharts.min.js" integrity="sha512-QbaChpzUJcRVsOFtDhh/VZMuljqvlPRIhIXsvfREDZcdqzIKdNvAhwrgW+flSxtbxK/BFpdX1y5NSO6bSYHlOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="scripts.js"></script>

</body>
</html>
