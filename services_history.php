<?php
    require_once 'session_config.php'; 
    require 'config.php';
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    $query = "
    SELECT 
        s.* ,
        u.Username
    FROM 
        services s
    JOIN 
        users u ON s.Id = u.Id
    ORDER BY 
        services_date DESC
    ";
    $result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/salesHistoryStyle.css">
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
      <a class="active" href="history.php">History</a>
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

<div class="pill-nav">
  <a href="history.php" style="margin: 0px 0px 0px 7px;">Add</a>
  <a href="stock_in_history.php">Stock In</a>
  <a href="edit_history.php">Edit</a>
  <a href="adjust_history.php">Adjust</a>
  <a href="defective_history.php">Defective</a>
  <a href="delete_history.php">Delete</a>
  <a href="sales_history.php">Sales</a>
  <a class="active" href="services_history.php">Services</a>
</div>

<div class="searchbar-container">
  <button class="dropdown-toggle" onclick="toggleSearchbar()">Filters ▾</button>
  <div class="searchbar">
        <div><p> Sort order: </p></div>
        <div><select name="sort" id="sort" style="margin-left: 15px">
        <option value="desc">Newest First</option>  
        <option value="asc">Oldest First</option>
        
    </select></div>
        <div><p>Pick category: </p></div>
        <div>
            <form class="category">
            <select id="category" placeholder="Select Category" name="category" class=selectCategory required style>
                <option class="placeholder" value="" disabled selected>Category</option>
                <option value="all">All</option>
                <option value="Braking System">Braking System</option>
                <option value="Interior Accessories">Interior Accessories</option>
                <option value="Engine Component">Engine Components</option>
                <option value="Lighting">Lighting</option>
                <option value="Electrical Component">Electrical Components</option>
                <option value="Tires and Wheels">Tires and Wheels</option>
            </select>
            </form>
        </div>
        <div>
        <form class="form-inline" onsubmit="myFunction(); return false;">
            <div><label for="email">Start:</label></div>
            <div><input type="date" id="dateStart" placeholder="Start Date" title="Select start date"></div>
            <div><label for="email">End:</label></div>
            <div><input type="date" id="dateEnd" placeholder="End Date" title="Select end date"></div>
            <div><button type="submit">Filter By Date</button></div>
        </form>
        </div>
    </div>
</div>

<div class="productsTable">
  <table id="myTable">
    <tr>
              <th style="width: 15%">Date and Time</th>
              <th>Service Description</th>
              <th>Total</th>
              <th>Cash</th>
              <th>Change</th>
              <th>User</th> 
          </tr>
          <?php
              while ($row = mysqli_fetch_assoc($result)) {
          ?>
              <tr>
                  <td><?php echo $row['services_date']; ?></td>
                  <td><?php echo $row['services_description']; ?></td>               
                  <td><?php echo $row['services_price']; ?></td>
                  <td><?php echo $row['services_customer_cash']; ?></td>
                  <td><?php echo $row['services_customer_change']; ?></td>
                  <td><?php echo $row['Username']; ?></td>                
              </tr>
          <?php
              }
          ?>
  </table>
</div>

<script>

document.getElementById('sort').addEventListener('change', function() {
    sortTable(this.value);
});

function sortTable(order) {
    const table = document.getElementById('myTable');
    const rows = Array.from(table.rows).slice(1); 
    const dateIndex = 0; 

    rows.sort((a, b) => {
        const dateA = new Date(a.cells[dateIndex].innerText);
        const dateB = new Date(b.cells[dateIndex].innerText);
        return order === 'asc' ? dateA - dateB : dateB - dateA;
    });

    rows.forEach(row => table.appendChild(row));
}

    function myFunction() {
        const dateStart = document.getElementById('dateStart').value;
        const dateEnd = document.getElementById('dateEnd').value;
        const table = document.getElementById('myTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            const dateCell = cells[0].innerText.split(' ')[0]; 

            if (!dateStart && !dateEnd) {
                rows[i].style.display = ""; 
            } else {
                const isDateInRange = (!dateStart || new Date(dateCell) >= new Date(dateStart)) &&
                                      (!dateEnd || new Date(dateCell) <= new Date(dateEnd));
                rows[i].style.display = isDateInRange ? "" : "none"; 
            }
        }
    }
;

</script>

<script>
function fetchOrderDetails(orderId) {
    document.getElementById('id01').style.display = 'block';

    fetch('fetch_order_details.php?order_id=' + orderId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const table = document.getElementById('orderDetailsTable');
            table.innerHTML = `
                <tr>
                    <th>Order Detail ID</th>
                    <th>Order ID</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            `;

            if (data.length === 0) {
                const row = table.insertRow();
                row.insertCell(0).innerText = 'No order details found';
                return;
            }

            data.forEach(detail => {
                const row = table.insertRow();
                row.insertCell(0).innerText = detail.order_detail_id;
                row.insertCell(1).innerText = detail.order_id;
                row.insertCell(2).innerText = detail.product_id;
                row.insertCell(3).innerText = detail.product_name;
                row.insertCell(4).innerText = detail.barcode; 
                row.insertCell(5).innerText = detail.category; 
                row.insertCell(6).innerText = detail.quantity;
                row.insertCell(7).innerText = detail.price;
                row.insertCell(8).innerText = detail.subtotal;
            });
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Failed to fetch order details. Please try again later.');
        });
}

window.onclick = function(event) {
    const modal = document.getElementById('id01');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

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

<script>
function toggleSearchbar() {
  const searchbar = document.querySelector('.searchbar');
  searchbar.classList.toggle('show');
}
</script>

</body>