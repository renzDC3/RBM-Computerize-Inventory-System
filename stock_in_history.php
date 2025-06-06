<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    $query = "
    SELECT 
        si.*, 
        p.barcode,
        p.name,
        p.category,
        p.model
    FROM 
        stock_in si
    JOIN 
        products p ON si.product_id = p.product_id
    ";
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
        margin-right: 10px;
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
    background-color: #cecece;
}

@-webkit-keyframes animatezoom {
  from {-webkit-transform: scale(0)} 
  to {-webkit-transform: scale(1)}
}
  
@keyframes animatezoom {
  from {transform: scale(0)} 
  to {transform: scale(1)}
}

/* Change styles for span and cancel button on extra small screens */
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
        margin-right: 0px;
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
      <h1>Stock-In History</h1>
      <p>Take a look at the history of the stocking in of products</p>
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
        <form class="form-inline">
          <select id="category" placeholder="Select Category" name="category" required style>
            <option class="placeholder" value="" disabled selected>Category</option>
            <option value="all">All</option>
            <option value="Braking System">Braking System</option>
            <option value="Interior Accessories">Interior Accessories</option>
            <option value="Engine Component">Engine Components</option>
            <option value="Lighting">Lighting</option>
            <option value="Electrical Component">Electrical Components</option>
            <option value="Tires and Wheels">Tires and Wheels</option>
          </select><br>
        </form>

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
              <th>Stock-In ID</th>
              <th>Product ID</th>
              <th>Name</th>
              <th>Barcode</th>
              <th>Category</th>
              <th>Model</th>    
              <th>Quantity</th>
              <th>Delivery ID</th>
              <th>User</th>
          </tr>
          <?php
              while ($row = mysqli_fetch_assoc($result)) {
          ?>
              <tr>
                  <td><?php echo $row['date_time']; ?></td>
                  <td><?php echo $row['stock_in_id']; ?></td>
                  <td><?php echo $row['product_id']; ?></td>
                  <td><?php echo $row['name']; ?></td>
                  <td><?php echo $row['barcode']; // Use the barcode text here ?></td>
                  <td><?php echo $row['category']; ?></td>
                  <td><?php echo $row['model']; ?></td>    
                  <td><?php echo $row['qty']; ?></td>                 
                  <td><?php echo $row['delivery_id']; ?></td>
                  <td><?php echo $row['Id']; ?></td>
              </tr>
          <?php
              }
          ?>
      </table>
      </div>
      </center>
  </div>

</div>

<script>

// Side Navigation Functions
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

// Dark Mode Functionality
function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
}

window.onload = function() {
    if (localStorage.getItem("dark-mode") === "enabled") {
        document.body.classList.add("dark-mode");
    }
}

// Sorting function
document.getElementById('sort').addEventListener('change', function() {
    sortTable(this.value);
});

function sortTable(order) {
    const table = document.getElementById('myTable');
    const rows = Array.from(table.rows).slice(1); // Get rows excluding header
    const dateIndex = 0; // Assuming the second column (0-indexed) is the date

    rows.sort((a, b) => {
        const dateA = new Date(a.cells[dateIndex].innerText);
        const dateB = new Date(b.cells[dateIndex].innerText);
        return order === 'asc' ? dateA - dateB : dateB - dateA;
    });

    // Remove existing rows and re-add sorted rows
    rows.forEach(row => table.appendChild(row));
}

function myFunction() {
        const dateStart = document.getElementById('dateStart').value;
        const dateEnd = document.getElementById('dateEnd').value;
        const table = document.getElementById('myTable');
        const rows = table.getElementsByTagName('tr');

        // Loop through all rows (excluding the header)
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            const dateCell = cells[0].innerText.split(' ')[0]; // Assuming the first cell contains the date

            // Check if both dates are provided
            if (!dateStart && !dateEnd) {
                rows[i].style.display = ""; // Show all rows if no date is selected
            } else {
                // Check if the row's date is within the specified range
                const isDateInRange = (!dateStart || new Date(dateCell) >= new Date(dateStart)) &&
                                      (!dateEnd || new Date(dateCell) <= new Date(dateEnd));
                rows[i].style.display = isDateInRange ? "" : "none"; // Show or hide the row
            }
        }
    }

    document.getElementById('category').addEventListener('change', function() {
    const selectedCategory = this.value;
    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const categoryCell = rows[i].getElementsByTagName('td')[5]; // Assuming category is the 5th column
        if (selectedCategory === 'all' || (categoryCell && categoryCell.innerText === selectedCategory)) {
            rows[i].style.display = ""; // Show row
        } else {
            rows[i].style.display = "none"; // Hide row
        }
    }
});

</script>

</body>
</html>