<?php
require_once 'session_config.php'; 
require 'config.php';
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
}

// Fetch logs from database
$query = "
    SELECT 
        id,
        user_id,
        username,
        user_role,
        action_type,
        description,
        module,
        result,
        date,
        time
    FROM 
        system_log
    ORDER BY 
        date DESC, time DESC
";
$result = mysqli_query($con,$query);

// Functions to format date and time
function formatDate($date) {
    return date("F j Y", strtotime($date)); // December 5 2019
}
function formatTime($time) {
    return date("g:i A", strtotime($time)); // 11:01 PM
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/systemLogStyle.css">
</head>
<body>

<div class="topnav" id="myTopnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>

  <?php if ($_SESSION['role'] === 'Admin' or $_SESSION['role'] === 'Manager') { ?>
      <a href="dashboard.php">Dashboard</a>           
  <?php } ?>

  <a href="products.php">Products</a>

  <?php if ($_SESSION['role'] != 'Manager') { ?>
    <a href="sales.php">Sales</a>
    <a href="services.php">Services</a>
  <?php } ?> 

  <?php if ($_SESSION['role'] === 'Admin'  or $_SESSION['role'] === 'Manager') { ?>
      <a href="history.php">History</a>
      <a href="employees.php">Employees</a>
      <a href="suppliers.php">Suppliers</a>
      <a href="report.php">Report</a>
      <a href="cloud.php">Backup</a>
  <?php } ?>

  <?php if ($_SESSION['role'] === 'Admin' or $_SESSION['role'] === 'Manager') { ?>
      <a class="active" href="system_log.php">System Log</a>
  <?php } ?>

  <a class="logout" href="logout.php">Logout</a>
  <a href="javascript:void(0);" class="icon" onclick="toggleNav()">☰</a>
</div>

<div class="searchbar-container">
  <button class="dropdown-toggle" onclick="toggleSearchbar()">Filters ▾</button>
  <div class="searchbar">
        <div><p>Sort order:</p></div>
        <div>
            <select name="sort" id="sort" style="margin-left: 15px">
              <option value="desc">Newest First</option>  
              <option value="asc">Oldest First</option>
            </select>
        </div>

    <div><p>User Role:</p></div>
    <div>
        <select id="userRole" name="userRole">
            <option value="all">All</option>
            <option value="Admin">Admin</option>
            <option value="Manager">Manager</option>
            <option value="Employee">Employee</option>
        </select>
    </div>

    <div><p>Module:</p></div>
    <div>
        <select id="module" name="module">
            <option value="all">All</option>
            <option value="Login">Login</option>
            <option value="Logout">Logout</option>  
            <option value="Products">Products</option>
            <option value="Sales">Sales</option>
            <option value="Services">Services</option>
            <option value="Employees">Employees</option>
            <option value="Suppliers">Suppliers</option>
            <option value="Report">Report</option>
            <option value="Backup">Backup</option>
        </select>
    </div>

    <div><p>Action Type:</p></div>
    <div>
        <select id="actionType" name="actionType">
            <option value="all">All</option>            
            <option value="Login">Login</option>
            <option value="2FA">2FA</option>
            <option value="Logout">Logout</option>            
            <option value="Add Product">Add Product</option>
            <option value="Stock In">Stock In</option>
            <option value="Edit Product">Edit Product</option>
            <option value="Stock Adjustment">Stock Adjustment</option>
            <option value="Delete Product">Delete Product</option>
            <option value="Handle Defective Product">Handle Defective Product</option>
            <option value="Sales">Sales</option>
            <option value="Add Service">Add Service</option>
            <option value="Add Employee">Add Employee</option>
            <option value="Delete Employee">Delete Employee</option>
            <option value="Add Supplier">Add Supplier</option>
            <option value="Generate Sales Report">Generate Sales Report</option>
            <option value="Generate Inventory Report">Generate Inventory Report</option>
            <option value="Generate Service Report">Generate Services Report</option>
            <option value="Upload to Cloud">Upload to Cloud</option>
            <option value="Restore from Cloud">Restore from Cloud</option>
        </select>
    </div>

    <div>
        <form class="form-inline" onsubmit="applyAllFilters(); return false;">
            <div><label>Start:</label></div>
            <div><input type="date" id="dateStart"></div>
            <div><label>End:</label></div>
            <div><input type="date" id="dateEnd"></div>
            <div><button type="submit">Filter By Date</button></div>
        </form>
    </div>
  </div>
</div>

<div class="productsTable">
  <table id="myTable">
    <tr>
      <th>Date</th>
      <th>Time</th>
      <th>Username</th>
      <th>User Role</th>
      <th>Module</th>
      <th>Action Type</th>
      <th>Description</th>
      <th>Result</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td data-date="<?php echo $row['date']; ?>"><?php echo formatDate($row['date']); ?></td>
        <td data-time="<?php echo $row['time']; ?>"><?php echo formatTime($row['time']); ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['user_role']; ?></td>
        <td><?php echo $row['module']; ?></td>
        <td><?php echo $row['action_type']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['result']; ?></td>
      </tr>
    <?php } ?>
  </table>
</div>

<script>
document.getElementById('sort').addEventListener('change', function() {
    sortTable(this.value);
});

function sortTable(order) {
    const table = document.getElementById('myTable');
    const rows = Array.from(table.rows).slice(1);
    const dateIndex = 0, timeIndex = 1;

    rows.sort((a, b) => {
        const dateA = a.cells[dateIndex].getAttribute('data-date');
        const timeA = a.cells[timeIndex].getAttribute('data-time');
        const dateB = b.cells[dateIndex].getAttribute('data-date');
        const timeB = b.cells[timeIndex].getAttribute('data-time');

        const dtA = new Date(dateA + ' ' + timeA);
        const dtB = new Date(dateB + ' ' + timeB);

        return order === 'asc' ? dtA - dtB : dtB - dtA;
    });

    rows.forEach(row => table.appendChild(row));
}

// Combined filter function (date + selects)
function applyAllFilters() {
    const dateStart = document.getElementById('dateStart').value;
    const dateEnd = document.getElementById('dateEnd').value;
    const userRole = document.getElementById('userRole').value;
    const actionType = document.getElementById('actionType').value;
    const module = document.getElementById('module').value;

    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const rowDate = rows[i].cells[0].getAttribute('data-date');
        const role = rows[i].cells[3].innerText;
        const mod = rows[i].cells[4].innerText;
        const action = rows[i].cells[5].innerText;

        const matchDate = (!dateStart || new Date(rowDate) >= new Date(dateStart)) &&
                          (!dateEnd || new Date(rowDate) <= new Date(dateEnd));
        const matchRole = (userRole === 'all' || role === userRole);
        const matchAction = (actionType === 'all' || action === actionType);
        const matchModule = (module === 'all' || mod === module);

        rows[i].style.display = (matchDate && matchRole && matchAction && matchModule) ? "" : "none";
    }
}

// Event listeners for select filters
['userRole', 'actionType', 'module'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyAllFilters);
});

function toggleNav() {
  const x = document.getElementById("myTopnav");
  if (x.className === "topnav") x.className += " responsive";
  else x.className = "topnav";
}

function toggleSearchbar() {
  const searchbar = document.querySelector('.searchbar');
  searchbar.classList.toggle('show');
}
</script>

</body>
</html>
