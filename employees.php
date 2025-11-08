<?php
require_once 'session_config.php';
require_once 'csrf.php';
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$query = "SELECT * FROM users";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="styles/employeesStyle.css">
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
      <a class="active" href="employees.php">Employees</a>
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

<div class="searchbar">
    <div><p> Search Employee Username: </p></div>
    <div><input type="text" class="filter_name_textbox" id="myInput" onkeyup="filterTable()" placeholder="Filter Usernames..." title="Type in a name"></div>
    <div>
      <button class="addProduct" onclick="openModal('modal-add-product')" style="width:auto;">
        Add Employee
      </button>
    </div>

    <div id="modal-add-product" class="modal">

        <div class="modal-content">
            <span onclick="closeModal('modal-add-product')" class="close">&times;</span>
            <h3>Add Employee</h3>
            <form action="add_employee.php" method="POST" enctype="multipart/form-data">
            <div class="modalHorizontal">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div><label for="fname"><b>First Name</b></label></div>
                <div><input type="text" placeholder="Enter First Name" name="fname" required></div>

                <div><label for="lname"><b>Last Name</b></label></div>
                <div><input type="text" placeholder="Enter Last Name" name="lname" required></div>

                <div><label for="username"><b>Username</b></label></div>
                <div><input type="text" placeholder="Enter Username" name="username" required></div>

                

            </div>
            <br>
            
            <div class="modalHorizontal">
                <div><label for="password"><b>Password</b></label></div>
                <div><input type="password" placeholder="Enter Password" name="password" required></div>
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

</div>

<div class="productsTable">
  <table id="myTable">
    <thead>
      <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Date Joined</th>
        <th>Username</th>
        <th>Action</th>
      </tr>
    </thead>
    

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <?php if ($row['Username'] === 'Admin' || $row['Username'] === 'Manager') continue; ?>
      <tr>
        <td><?= $row['first_name']; ?></td>
        <td><?= $row['last_name']; ?></td>
        <td><?= $row['date_joined']; ?></td>
        <td><?= $row['Username']; ?></td>       

        <td style="padding: 5px;">
          <button onclick="openModal('modal-delete-<?= $row['Id']; ?>')">
            <i class="fa-solid fa-trash"></i>&nbsp;Del
          </button>

          <div id="modal-delete-<?= $row['Id']; ?>" class="modal">
            <div class="modal-content">  
              <span onclick="closeModal('modal-delete-<?= $row['Id']; ?>')" class="close">&times;</span>
                <form action="delete_employee.php" method="POST">
                  <div class="container">
                    <h3>Delete Employee Account</h3>
                    <p>Are you sure you want to delete this Employee's Account from the database?</p>
                    <hr>
                    <input type="hidden" name="Id" value="<?= $row['Id']; ?>">

                    <div class="confirmOrCancel">
                      <button type="button" onclick="closeModal('modal-delete-<?= $row['Id']; ?>')" class="cancelbtn">Cancel</button>
                      <button type="submit" class="confirm" style="float: right">Confirm</button>
                    </div>
                  </div>
                </form>
            </div>
          </div>
        </td>
      </tr>
    <?php } ?>

  </table>
</div>

<script>
function filterTable() {
  var input, filter, table, tr, td, i, txtValue;
  
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[4];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>

        
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
function toggleNav() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>

</body>