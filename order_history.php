<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    $query = "SELECT * FROM orders";
    $result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/all.css">

<link rel="stylesheet" href="styles/dashboardStyle.css">
<style>

    @font-face {
        font-family: 'Metropolis';
        src: url(fonts/Metropolis-Light.otf);
    }

    * {
      font-family: 'Metropolis';
    }
    
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
    }

    

    select {
        width: 15%;
        height: 30px;
        font-size:large;
    }

    .input-container i {
        
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa; 
    }


    .stock_in {
        background-color: #950606;
        color: white;
        transition: all .1s;
    }

    .form-inline {  
      display: flex;
      flex-flow: row wrap;
      align-items: center;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    .form-inline label {
      margin: 5px 10px 5px 0;
    }

    .form-inline input {
      vertical-align: middle;
      margin: 5px 10px 5px 0;
      padding: 10px;
      background-color: #fff;
      border: 1px solid #ddd;
    }

    .form-inline button {
      padding: 10px 20px;
      background-color: white;
      border: 1px solid #950606;
      color: black;
      cursor: pointer;
    }

    .form-inline button:hover {
      background-color: royalblue;
    }

    @media (max-width: 600px) {
      .form-inline input {
        margin: 10px 0;
      }
      
      .form-inline {
        flex-direction: column;
        align-items: stretch;
      }

    }

/* Dark Mode CSS */
  
  .dark-mode {
    select {
        background-color: #121212;
        color: white;
        border: 1px solid #950606;
    }

    .form-inline input {
      background-color: #121212;
      border: 1px solid #950606;
      color: white;
    }

    .form-inline button {
      background-color: #950606;
      border: 1px solid #950606;
      color: white;
    }
    
    .modal-content {
      background-color: #282828;
      color: white;
    }


    table{
        background-color: #121212;
        color: white;
        transition: all .1s;
        border-color: #950606;
    }

    tr:nth-child(even) {
        background-color: #222222;
        color: white;
        transition: all .1s;
    }

    .stock_in {
      background-color: #950606;
      color: white;
      transition: all .1s;
    }

    
}

  
  
  /* Table Style */
    
table {
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    border: 1px solid #ddd;
    background-color: white;
    color: black;
    border-radius: 8px;
}

th, td {
    text-align: left;
    padding-left: 35px;
    padding-right: 35px;
    padding-top: 15px;
    padding-bottom: 15px;
}

.action {
    margin:none;
    text-align: left;
}

.td_button{
    text-align: left;
}

tr:nth-child(even) {
    background-color: gray;
}


.lowerButton {
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  font-family: 'Metropolis';
  border-radius: 8px;
}


button:hover {
  opacity: 0.8;
}


.container {
  padding: 16px;
  
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
  width: 80%; 
  border-radius: 8px;
  color: black;
}

.close {
  position: absolute;
  right: 25px;
  top: 0;
  color: #000;
  font-size: 35px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: red;
  cursor: pointer;
}

.animate {
  -webkit-animation: animatezoom 0.6s;
  animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
  from {-webkit-transform: scale(0)} 
  to {-webkit-transform: scale(1)}
}
  
@keyframes animatezoom {
  from {transform: scale(0)} 
  to {transform: scale(1)}
}

@media screen and (max-width: 300px) {
  span.psw {
     display: block;
     float: none;
  }
  .cancelbtn {
     width: 100%;
  }
}

/* Responsiveness */

@media only screen and (max-width: 600px) {
  th, td {
    text-align: left;
    padding-left: 8.75px;
    padding-right: 8.75px;
    padding-top: 3.75px;
    padding-bottom: 3.75px;
    font-size: small;
  }

  .input-container {
        width: 100%;
        font-size: small;
    }

    select {
        width: 100%;
        font-size: medium;
    }


}


</style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="topnav">
    &nbsp;
    <span style="font-size:30px;cursor:pointer;color:white" onclick="openNav()">&#9776;</span>
    &nbsp;
    <button class="dmbutton" onclick="toggleDarkMode()"><i class="fa-solid fa-moon"></i></button>
    <a href="logout.php" class="split"><i class="fa-solid fa-right-to-bracket"></i> Logout</a>
  
</div>

<!-- Side Navigation Menu -->
 <div id="mySidenav" class="sidenav">
        <center><img src="rbm_logo.jpg" alt="RBM Logo" height="150" width="150"></center>
         	<a href="javascript:void(0)" class="sidenavIcon closebtn" onclick="closeNav()">&times;</a>
          	<a href="dashboard.php"><i class="sidenavIcon fa-solid fa-chart-line"></i> Dashboard</a>
          	<a href="inventory_list.php"><i class="fa-solid sidenavIcon fa-box-open"></i> Products</a>
          	<a href="ordering.php"><i class="sidenavIcon fa-solid fa-cart-shopping"></i> Ordering</a>
          	<a href="add_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Adding History</a>
          	<a href="edit_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Editing History</a>
          	<a href="stock_in_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Stock-In History</a>
          	<a href="adjustment_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Adjustment History</a>
          	<a href="delete_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Deletion History</a>
          	<a href="order_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Ordering History</a>
    </div>

<!-- Main Content -->
  <div style="padding-left:16px">
      <h1>Order History</h1>
      <p>Take a history of orders</p>
  </div>

  <hr class="hrRed">

  <!-- Columns -->
  <div class="topdash">
  <select name="sort" id="sort">
    <option value="asc">Oldest First</option>
    <option value="desc">Newest First</option>
  </select>
      <div class="input-container">
        <!-- <i class="fa-solid fa-magnifying-glass"></i> -->
        <form class="form-inline" onsubmit="myFunction(); return false;">
          <label for="email">Start:</label>
          <input type="date" id="dateStart" placeholder="Start Date" title="Select start date">
          <label for="email">End:</label>
          <input type="date" id="dateEnd" placeholder="End Date" title="Select end date">
          <button type="submit">Filter</button>
        </form>
     </div>


      <center>
      <div style="overflow-x:auto;">
      <table id="myTable">
          <tr>
              <th>Date and Time</th>
              <th>Order ID</th>
              <th>Total</th>
              <th>Cash</th>
              <th>Change</th>
              <th>User</th>
              <th class="action">Details</th>
              <th>Receipt</th>            
          </tr>
          <?php
              while ($row = mysqli_fetch_assoc($result)) {
          ?>
              <tr>
                  <td><?php echo $row['order_date']; ?></td>
                  <td><?php echo $row['order_id']; ?></td>                 
                  <td><?php echo $row['orders_total']; ?></td>
                  <td><?php echo $row['orders_cash']; ?></td>
                  <td><?php echo $row['orders_change']; ?></td>
                  <td><?php echo $row['Id']; ?></td>
                  <td>
                      <button class="stock_in lowerButton" onclick="fetchOrderDetails(<?php echo $row['order_id']; ?>)" style="width:auto;">
                          <i class="fa-solid fa-circle-info"></i>
                      </button>
                  </td>
                  <td>
                      <button class="stock_in lowerButton" style="width:auto;" onclick="window.open('generate_receipt.php?order_id=<?php echo $row['order_id']; ?>', '_blank');">
                          <i class="fa-solid fa-receipt"></i>
                      </button>

                  </td>
              </tr>
          <?php
              }
          ?>
      </table>
      </div>
      </center>
  </div>

  <!-- Modal for Order Details -->
  <div id="id01" class="modal">
      <div class="modal-content">
            <div class="container">
                <h1>Order Details</h1>
                <p>Here is the detail of that particular order.</p>
                <hr>
                <div style="overflow-x:auto;">
                  <table id="orderDetailsTable">
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
                      <!-- Order details will be populated here -->
                  </table>
              </div>
            </div>
      </div>
  </div>


 
</div>

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

function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
}

window.onload = function() {
    if (localStorage.getItem("dark-mode") === "enabled") {
        document.body.classList.add("dark-mode");
    }
}

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

</script>

</body>
</html>