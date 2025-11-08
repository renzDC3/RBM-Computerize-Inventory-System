<?php
require_once 'session_config.php'; 
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

$productQuery = "SELECT * FROM products";
$productResult = mysqli_query($con, $productQuery);

$totalProductsResult = $con->query("SELECT COUNT(*) AS total FROM products");
$totalProducts = ($totalProductsResult && $row = $totalProductsResult->fetch_assoc()) ? $row['total'] : 'Error retrieving count';

$lowStockResult = $con->query("SELECT COUNT(*) AS total FROM products WHERE quantity < 5 and quantity != 0");
$lowStock = ($lowStockResult && $row = $lowStockResult->fetch_assoc()) ? $row['total'] : 'Error retrieving count';

$outOfStockResult = $con->query("SELECT COUNT(*) AS total FROM products WHERE quantity < 1");
$outOfStock = ($outOfStockResult && $row = $outOfStockResult->fetch_assoc()) ? $row['total'] : 'Error retrieving count';

$defectiveQuery = "SELECT name, SUM(quantity) as total_quantity 
                  FROM handle_defective_product_history 
                  GROUP BY name";
$defectiveResult = mysqli_query($con, $defectiveQuery);

// =======================
// Monthly Revenue
// =======================
$salesRevenueResult = $con->query("
    SELECT SUM(orders_total) AS total_revenue
    FROM orders
    WHERE MONTH(order_date) = MONTH(CURRENT_DATE())
      AND YEAR(order_date) = YEAR(CURRENT_DATE())
");
$totalSalesRevenue = 0;
if ($salesRevenueResult && $row = $salesRevenueResult->fetch_assoc()) {
    $totalSalesRevenue = $row['total_revenue'] ?? 0;
}

// =======================
// Monthly Units Sold
// =======================
// Join order_detail with orders to filter by order_date
$unitsSoldResult = $con->query("
    SELECT SUM(od.quantity) AS total_units
    FROM order_detail od
    JOIN orders o ON od.order_id = o.order_id
    WHERE MONTH(o.order_date) = MONTH(CURRENT_DATE())
      AND YEAR(o.order_date) = YEAR(CURRENT_DATE())
");
$totalUnitsSold = 0;
if ($unitsSoldResult && $row = $unitsSoldResult->fetch_assoc()) {
    $totalUnitsSold = $row['total_units'] ?? 0;
}

// =======================
// Monthly Gross Profit (based on actual product cost)
// =======================
$grossProfitResult = $con->query("
    SELECT SUM( (od.price - p.cost) * od.quantity ) AS gross_profit
    FROM order_detail od
    JOIN orders o ON od.order_id = o.order_id
    JOIN products p ON od.product_id = p.product_id
    WHERE MONTH(o.order_date) = MONTH(CURRENT_DATE())
      AND YEAR(o.order_date) = YEAR(CURRENT_DATE())
");
$totalGrossProfit = 0;
if ($grossProfitResult && $row = $grossProfitResult->fetch_assoc()) {
    $totalGrossProfit = $row['gross_profit'] ?? 0;
}

// =======================
// Monthly Stock Turnover
// =======================

// 1. Total units sold this month (already computed but let's make sure)
$turnoverUnitsSoldResult = $con->query("
    SELECT SUM(od.quantity) AS total_sold
    FROM order_detail od
    JOIN orders o ON od.order_id = o.order_id
    WHERE MONTH(o.order_date) = MONTH(CURRENT_DATE())
      AND YEAR(o.order_date) = YEAR(CURRENT_DATE())
");
$totalUnitsSoldForTurnover = 0;
if ($turnoverUnitsSoldResult && $row = $turnoverUnitsSoldResult->fetch_assoc()) {
    $totalUnitsSoldForTurnover = (float)($row['total_sold'] ?? 0);
}

// 2. Total stock received this month
$stockInResult = $con->query("
    SELECT SUM(qty) AS total_stock_in
    FROM stock_in
    WHERE MONTH(date_time) = MONTH(CURRENT_DATE())
      AND YEAR(date_time) = YEAR(CURRENT_DATE())
");
$totalStockInThisMonth = 0;
if ($stockInResult && $row = $stockInResult->fetch_assoc()) {
    $totalStockInThisMonth = (float)($row['total_stock_in'] ?? 0);
}

// 3. Current total inventory (approximate ending inventory)
$endingInventoryResult = $con->query("
    SELECT SUM(quantity) AS total_current_inventory
    FROM products
");
$totalEndingInventory = 0;
if ($endingInventoryResult && $row = $endingInventoryResult->fetch_assoc()) {
    $totalEndingInventory = (float)($row['total_current_inventory'] ?? 0);
}

// 4. Estimate beginning inventory as: Ending + Sold - Received
$beginningInventory = $totalEndingInventory + $totalUnitsSoldForTurnover - $totalStockInThisMonth;

// 5. Average Inventory
$averageInventory = ($beginningInventory + $totalEndingInventory) / 2;

// 6. Calculate Stock Turnover Ratio
$stockTurnoverRatio = 0;
if ($averageInventory > 0) {
    $stockTurnoverRatio = $totalUnitsSoldForTurnover / $averageInventory;
}

// =======================
// Monthly Service Profits
// =======================
$serviceProfitResult = $con->query("
    SELECT SUM(services_customer_cash - services_customer_change) AS total_service_profit
    FROM services
    WHERE MONTH(services_date) = MONTH(CURRENT_DATE())
      AND YEAR(services_date) = YEAR(CURRENT_DATE())
");
$totalServiceProfit = 0;
if ($serviceProfitResult && $row = $serviceProfitResult->fetch_assoc()) {
    $totalServiceProfit = $row['total_service_profit'] ?? 0;
}



?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/dashboardStyle.css">

<style>


</style>
</head>
<body>

<div class="topnav" id="myTopnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>

  <?php if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager') { ?>
      <a class="active" href="dashboard.php">Dashboard</a>           
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

<div style="padding-left:16px; margin-bottom: 25px">
  <h1>Dashboard</h1>
</div>

    <div id="modal-add-product" class="modal">

        <div class="modal-content">
            <span onclick="closeModal('modal-add-product')" class="close">&times;</span>
            <h3>Generate Report</h3>
            <form action="" method="POST" enctype="multipart/form-data">
            <div class="modalHorizontal">
                <div><label for="email">Start:</label></div>
                <div><input type="date" id="dateStart" placeholder="Start Date" title="Select start date"></div>

                <div><label for="barcode"><b>Barcode</b></label></div>
                <div><input type="text" placeholder="Submit Barcode" name="barcode" required></div>

                <div><label for="category"><b>Category</b></label></div>
                <div><select id="category" placeholder="Select Category" name="category" required>
                  <option class="placeholder" value="" disabled selected>Category</option>
                    <option value="all">All</option>
                    <option value="Braking System">Braking System</option>
                    <option value="Interior Accessories">Interior Accessories</option>
                    <option value="Engine Component">Engine Components</option>
                    <option value="Lighting">Lighting</option>
                    <option value="Electrical Component">Electrical Components</option>
                    <option value="Tires and Wheels">Tires and Wheels</option>
                </select></div>
                    
                <div><label for="model"><b>Model</b></label></div>
                <div><input type="text" placeholder="Submit Model" name="model"></div>
            </div>
            <br>
            
            <div class="modalHorizontal">
              <div><label for="quantity"><b>Initial Quantity</b></label></div>
                <div><input type="number" min="0" max="1000" placeholder="Enter Quantity" name="quantity" required ></div>

                <div><label for="price"><b>Price</b></label></div>
                <div><input type="number" min="1.00" max="999999.99" value="1.00" placeholder="Enter Product Price" name="price" required></div>

                <div><label for="name"><b>Wholesaler</b></label></div>
                <div><input type="text" placeholder="Enter Wholesaler Name" name="name"></div>
            </div>
            <br>
            <hr>
            <div class="confirmOrCancel">
              <button type="button" onclick="closeModal('modal-add-product')" class="cancelbtn">Cancel</button>
              <button type="submit" class="confirm" style="float: right">Confirm</button>
            </div>
        </form>
      </div>
            
    </div>

<div class="majorSection">
  <h3 style="padding-left:15px;">Monthly Executive Summary</h3>

  <div class="horizontal">
    <div class="minorSection" id="showModalBtn" onclick="openModal('productModal')">
      <h1><?php echo $totalProducts; ?></h1>
      <h5>No. of Products</h5>
    </div>
    <div class="minorSection" id="showModalBtn" onclick="openModal('lowStockModal')">
      <h1><?php echo $lowStock; ?></h1>
      <h5>Low Stock</h5>
    </div>
    <div class="minorSection" id="showModalBtn" onclick="openModal('outOfStockModal')">
      <h1><?php echo $outOfStock; ?></h1>
      <h5>Out of Stock</h5>
    </div>
    <div class="minorSection" id="showModalBtn" onclick="openModal('defectiveModal')">
      <h1>5</h1>
      <h5>Defective Units</h5>
    </div>
  </div>
  
  <div class="horizontal">
    <div class="minorSection">
      <h1>P<?php echo number_format($totalSalesRevenue, 2); ?></h1>
      <h5>Total Sales Revenue</h5>
    </div>
    <div class="minorSection">
      <h1><?php echo number_format($totalUnitsSold); ?></h1>
      <h5>Units Sold</h5>
    </div>
    <div class="minorSection">
      <h1>P<?php echo number_format($totalGrossProfit, 2); ?></h1>
      <h5>Gross Profit</h5>
    </div>
    <div class="minorSection">
      <h1>P<?php echo number_format($totalServiceProfit, 0); ?></h1>
      <h5>Service Profits</h5>
    </div>
  </div>
</div>

<!--

<div class="majorSection">
  <h3 style="padding-left:15px;">Inventory Status</h3>
  <div class="productsTable">
    <table id="myTable">
      <thead>
        <tr>
          <th>SKU</th>
          <th>Name</th>
          <th>Opening Stock</th>
          <th>Received</th>
          <th>Sold</th>
          <th>Defective Products</th>
          <th>Closing Stock</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php /* while ($row = mysqli_fetch_assoc($productResult)) { ?>
        <tr>
          <td>P00<?php echo $row['product_id']; ?></td>
          <td><?php echo $row['name']; ?></td>
          <td><?php echo $row['quantity'] + 100; ?></td>
          <td><?php echo $row['quantity'] + 51; ?></td>
          <td><?php echo $row['quantity'] + 50; ?></td>
          <td><?php echo ($row['quantity'] % 2 == 0) ? 1 : 0; ?></td>
          <td><?php echo ($row['quantity'] % 2 == 0) ? $row['quantity'] + 100 : $row['quantity'] + 101; ?></td>
          <td>
            <?php
              if ($row['quantity'] > 10) {
                  echo "Sufficient";
              } elseif ($row['quantity'] < 10 && $row['quantity'] > 0) {
                  echo "Low Stock";
              } else {
                  echo "Out of Stock";
              }
            ?>
          </td>
        </tr>
      <?php } */ ?>
      </tbody>
    </table>
  </div>
</div>

 -->

<div class="majorSection">
  <div class="charts-card">
    <h2 class="charts-title" style="color: #880808">Top 5 Best-Selling Products</h2>
    <div id="bar-chart"></div>
  </div>  
</div>

  <div id="productModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal('productModal')" class="close">&times;</span>
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

  <div id="lowStockModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal('lowStockModal')" class="close">&times;</span>
        <h2>Low Stock Products</h2>
        <p>Here you can list products with low stock (less than 5 units).</p>
        <div style="height: 350px; overflow-y:scroll; text-align: center">
          <?php
          $query = "SELECT * FROM products WHERE quantity < 5 and quantity != 0";
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

  <div id="outOfStockModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal('outOfStockModal')" class="close">&times;</span>
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

  <div id="defectiveModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal('defectiveModal')" class="close">&times;</span>
        <h2>Out of Stock Products</h2>
        <p>Here you can list products that are out of stock.</p>
        <div style="height: 350px; overflow-y:scroll">
          <?php
          if ($defectiveResult) {
              while ($row = mysqli_fetch_assoc($defectiveResult)) {
                  echo "<center><h3>{$row['name']} - {$row['total_quantity']}</h3></center>";
              }
          } else {
              echo "Error: " . mysqli_error($con);
          }
          ?>
        </div>
    </div>
  </div> 

  <div id="defectiveModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal('defectiveModal')" class="close">&times;</span>
        <h2>Out of Stock Products</h2>
        <p>Here you can list products that are out of stock.</p>
        <div style="height: 350px; overflow-y:scroll">
          <?php
          if ($defectiveResult) {
              while ($row = mysqli_fetch_assoc($defectiveResult)) {
                  echo "<center><h3>{$row['name']} - {$row['total_quantity']}</h3></center>";
              }
          } else {
              echo "Error: " . mysqli_error($con);
          }
          ?>
        </div>
    </div>
  </div>

</body>

<script>
function openModal(modalId) {
  document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
  const modals = document.querySelectorAll('.modal');
  modals.forEach(modal => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });
};
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
function toggleNav() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.53.0/apexcharts.min.js" integrity="sha512-QbaChpzUJcRVsOFtDhh/VZMuljqvlPRIhIXsvfREDZcdqzIKdNvAhwrgW+flSxtbxK/BFpdX1y5NSO6bSYHlOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="scripts.js"></script>
</html>
