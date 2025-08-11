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
<link rel="stylesheet" href="styles/dashboardStyle.css">
</head>
<body>

<div class="topnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>
  <a class="active" href="#home">Dashboard</a>
  <a href="products.php">Products</a>
  <a href="sales.php">Sales</a>
  <a href="history.php">History</a>
  <a class="logout" href="logout.php">Logout</a>
</div>

<div style="padding-left:16px">
  <h2>Monthly Stats</h2>
  <p>Come see......</p>
</div>

<div class="row">
  <div class="column" id="showModalBtn" style="background-color:#dcdcdc;">
    <h2>No. of Products</h2>
    <p>
      <?php
          $sql = "SELECT COUNT(*) AS total FROM products";
          $result = $con->query($sql);

          if ($result && $row = $result->fetch_assoc()) {
              echo $row['total'];
          } else {
              echo "Error retrieving count";
          }
        ?>
    </p>
  </div>

  <!-- No. of Products Modal -->

  <div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>The Store's Products</h2>
        <p>Here you can provide additional details or data about the products.</p>
        <div style="height: 350px; overflow-y:scroll">
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
  </div>

  <div class="column" style="background-color:#d3d3d3;" id="lowStockBtn">
    <h2>Low Stock</h2>
    <p>
       <?php
            $sql = "SELECT COUNT(*) AS total FROM products WHERE quantity < 5";
            $result = $con->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                echo $row['total'];
            } else {
                echo "Error retrieving count";
            }
        ?>
    </p>
  </div>

  <!-- Low Stock Products Modal -->

  <div id="lowStockModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeLowStockModal">&times;</span>
        <h2>Low Stock Products</h2>
        <p>Here you can list products with low stock (less than 5 units).</p>
        <div style="height: 350px; overflow-y:scroll">
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
  </div>          

  <div id="outOfStockBtn" class="column" style="background-color:#c0c0c0;">
    <h2>Out of Stock</h2>
    <p>
        <?php
            $sql = "SELECT COUNT(*) AS total FROM products WHERE quantity < 1";
            $result = $con->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                echo $row['total'];
            } else {
                echo "Error retrieving count";
            }
        ?>      
    </p>
  </div>

  <!-- Out of Stock Products Modal -->

  <div id="outOfStockModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeOutOfStockModal">&times;</span>
        <h2>Out of Stock Products</h2>
        <p>Here you can list products that are out of stock.</p>
        <div style="height: 350px; overflow-y:scroll">
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
</div>         

</div>

<div class="row">
  <div class="column" style="background-color:#a9a9a9;">
    <h2>No. of Sales</h2>
    <p>
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
    </p>
  </div>

  <div class="column" style="background-color:#808080;">
    <h2>Revenue</h2>
    <p>
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
    </p>
  </div>

  <div class="column" style="background-color:#696969;">
    <h2>Net Profit</h2>
    <p>
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
    </p>
  </div>
</div>

<!--
<div class=chart>
  <h3>
     Top 5 Best Selling Products
  </h3>
</div>
-->

<div class="charts-card">
    <h2 class="charts-title">Top 5 Best-Selling Products</h2>
    <div id="bar-chart"></div>
</div>

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

<!-- Modal -->

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
