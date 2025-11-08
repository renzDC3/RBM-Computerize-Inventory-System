<?php
require_once 'session_config.php';
require_once 'csrf.php';
require 'config.php';

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$query = "SELECT * FROM suppliers";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="styles/suppliersStyle.css">
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
      <a class="active" href="suppliers.php">Suppliers</a>
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
    <div><p> Search Supplier's Username: </p></div>
    <div><input type="text" class="filter_name_textbox" id="myInput" onkeyup="filterTable()" placeholder="Filter Usernames..." title="Type in a name"></div>
    <div>
      <button class="addProduct" onclick="openModal('modal-add-supplier')" style="width:auto;">
        Add Supplier
      </button>
    </div>

    <div id="modal-add-supplier" class="modal">

        <div class="modal-content">
            <span onclick="closeModal('modal-add-supplier')" class="close">&times;</span>
            <h3>Add Supplier</h3>
            <form action="delete_supplier.php" method="POST" enctype="multipart/form-data">
            <div class="modalHorizontal">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div><label for="name"><b>Supplier Name</b></label></div>
                <div><input type="text" placeholder="Enter Supplier Name" name="name" required></div>

                <div><label for="businessaddress"><b>Business Address</b></label></div>
                <div><input type="text" placeholder="Enter Last Name" name="businessaddress" required></div>

                <div><label for="contactno"><b>Contact No.</b></label></div>
                <div><input type="number" placeholder="Enter Contact No." name="contactno" required></div>
            </div>
            <br>
            
            <div class="modalHorizontal">
                <div><label for="emailaddress"><b>Email Address</b></label></div>
                <div><input type="text" placeholder="Enter Email Address" name="emailaddress" required></div>              
            </div>
            <br>
            <hr>
            <div class="confirmOrCancel">
              <button type="button" onclick="closeModal('modal-add-supplier')" class="cancelbtn">Cancel</button>
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
        <th>Name</th>
        <th>Business Address</th>
        <th>Contact No.</th>
        <th>Supplier Email</th>
        <th>Date Added</th>
        <th>Action</th>
      </tr>
    </thead>
    

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['supplier_name']; ?></td>
        <td><?= $row['supplier__business_address']; ?></td>
        <td><?= $row['supplier_contact_no']; ?></td>
        <td><?= $row['supplier_email']; ?></td>
        <td><?= $row['supplier_date_added']; ?></td>      

        <td style="padding: 5px;">
          <button onclick="openModal('modal-delete-<?= $row['supplier_id']; ?>')">
            <i class="fa-solid fa-trash"></i>&nbsp;Del
          </button>

          <div id="modal-delete-<?= $row['supplier_id']; ?>" class="modal">
            <div class="modal-content">  
              <span onclick="closeModal('modal-delete-<?= $row['supplier_id']; ?>')" class="close">&times;</span>
                <form action="delete_supplier.php" method="POST">
                  <div class="container">
                    <h3>Delete Supplier Information</h3>
                    <p>Are you sure you want to delete this Supplier's Information from the database?</p>
                    <hr>
                    <input type="hidden" name="Id" value="<?= $row['supplier_id']; ?>">

                    <div class="confirmOrCancel">
                      <button type="button" onclick="closeModal('modal-delete-<?= $row['supplier_id']; ?>')" class="cancelbtn">Cancel</button>
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
    td = tr[i].getElementsByTagName("td")[1];
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