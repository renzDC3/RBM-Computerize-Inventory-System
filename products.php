<?php
    require_once 'session_config.php';
    require_once 'csrf.php'; 
    require 'config.php';

    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    // Existing product query
    $query = "
        SELECT 
          p.*,
          aph.date_time 
        FROM 
          products p
        JOIN
          add_product_history aph ON p.product_id = aph.product_id
        ORDER BY
          name ASC";
    $result = mysqli_query($con,$query);

    // ✅ New query for suppliers
    $suppliersQuery = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
    $suppliersResult = mysqli_query($con, $suppliersQuery);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="styles/productsStyle.css">
</head>
<body>

<div class="topnav" id="myTopnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>

  <?php if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager') { ?>
      <a href="dashboard.php">Dashboard</a>           
  <?php } ?>

  <a class="active" href="products.php">Products</a>

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

<div class="searchbar-container">
  <button class="dropdown-toggle" onclick="toggleSearchbar()">Filters ▾</button>
  <div class="searchbar">
      <div><p> Sort by:</p></div>       
      <div>
          <form class="category">
            <select id="order" placeholder="Select Order" name="order" class=selectOrder required style>
              <option value="alphabetical_az">Alphabetical (A - Z)</option>
              <option value="alphabetical_za">Alphabetical (Z - A)</option>
              <option value="date_added_newest">Date Added (Newest)</option>
              <option value="date_added_oldest">Date Added (Oldest)</option>
            </select>
          </form>
      </div>
      <div><p> Search product name: </p></div>
      <div><input type="text" class="filter_name_textbox" id="myInput1" onkeyup="filterTable()" placeholder="Product name" title="Type in a name"></div>
      <div><p>Search the model name: </p></div>
      <div><input type="text" class="filter_name_textbox" id="myInput2" onkeyup="filterTable()" placeholder="Model name" title="Type in a name"></div>
      <div><p>Select category: </p></div>
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

      <?php 
        if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager') { 
      ?>

      <div>
        <button class="addProduct" onclick="openModal('modal-add-product')" style="width:auto;">
          Add Product
        </button>
      </div>

      <?php } ?>

      <!-- The Add Product Modal -->
      <div id="modal-add-product" class="modal">

          <!-- Modal content -->
          <div class="modal-content">
              <span onclick="closeModal('modal-add-product')" class="close">&times;</span>
              <h3>Add Product</h3>
              <form action="add_product.php" method="POST" enctype="multipart/form-data">
              <div class="modalHorizontal">
                  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                  <div><label for="name"><b>Name</b></label></div>
                  <div><input type="text" placeholder="Enter Product Name" name="name" required></div>

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
                      <option value="Oil">Oil</option>
                      <option value="Cleaning equipment">Cleaning equipment</option>
                      <option value="Lubricant">Lubricant</option>
                  </select></div>
                      
                  <div><label for="model"><b>Model</b></label></div>
                  <div><input type="text" placeholder="Submit Model" name="model"></div>

              </div>
              <br>
              
              <div class="modalHorizontal">
                <div><label for="quantity"><b>Initial Quantity</b></label></div>
                  <div><input type="number" min="0" max="1000" placeholder="Enter Quantity" name="quantity" required ></div>

                  <div><label for="cost"><b>Cost</b></label></div>
                  <div><input type="number" min="1.00" max="999999.99" value="1.00" placeholder="Enter Product Price" name="cost" required></div>
                  
                  <div><label for="price"><b>Retail Price</b></label></div>
                  <div><input type="number" min="1.00" max="999999.99" value="1.00" placeholder="Enter Product Price" name="price" required></div>

                  <!--
                  <div><label for="supplier_name"><b>Supplier Name</b></label></div>
                  <div><input type="text" placeholder="Enter Supplier Name" name="supplier_name"></div>
                  -->

                  <div><label for="category"><b>Suppliers</b></label></div>
                  <div>
                      <select id="category" name="supplier" required>
                          <option class="placeholder" value="" disabled selected>Select Supplier</option>
                          <?php while ($row = mysqli_fetch_assoc($suppliersResult)) { ?>
                              <option value="<?php echo htmlspecialchars($row['supplier_id']); ?>">
                                  <?php echo htmlspecialchars($row['supplier_name']); ?>
                              </option>
                          <?php } ?>
                      </select>
                  </div>
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
</div>




<div style="height: 18px;">
  <p style="font-size: smaller; margin: 3px 0px 3px 12px; font-style: italic;" >This inventory list is real-time.</p>
</div>

<div class="productsTable">
  <table id="myTable">
    <thead>
      <tr>
        <!-- <th>Product ID</th> -->
        <th>Name</th>
        <th>Barcode</th>
        <th>Category</th>
        <th>Model</th>
        <th>Availability</th>
        <th>Quantity</th>
        <th>Cost</th>
        <th>Retail Price</th>
        <th style="width: 150px">Date Added</th>

        <?php 
          if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager' ) { 
        ?>

        <th colspan="5" class="action" style="text-align: center;">Action</th>

        <?php } ?>      

      </tr>
    </thead>
    

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <!-- <td><?= $row['product_id']; ?></td> -->
        <td><?= $row['name']; ?></td>
        <td><?= $row['barcode']; ?></td>
        <td><?= $row['category']; ?></td>
        <td><?= $row['model']; ?></td>
        <td><?= $row['quantity'] > 0 ? "In Stock" : "Out of Stock"; ?></td>
        <td><?= $row['quantity']; ?></td>
        <td><?= $row['cost']; ?></td>
        <td><?= $row['price']; ?></td>
        <td><?= $row['date_time']; ?></td>

        <?php 
          if ($_SESSION['valid'] === 'Admin' or $_SESSION['valid'] === 'Manager' ) { 
        ?>

        <!-- STOCK IN -->
        <td style="padding: 5px;">
          <button onclick="openModal('modal-stockin-<?= $row['product_id']; ?>')">
            <i class="fa-solid fa-plus"></i>&nbsp;Stck
          </button>

          <div id="modal-stockin-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">
              <span onclick="closeModal('modal-stockin-<?= $row['product_id']; ?>')" class="close">&times;</span>
              <h3>Stock In Product</h3>
                <form action="stock_in.php" method="POST">
                    <div class="modalHorizontal">
                      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                      <div><input type="hidden" name="product_id" value="<?= $row['product_id']; ?>"></div>

                      <div><label for="quantity"><b>Quantity</b></label></div>
                      <div><input type="number" min="1" max="1000" placeholder="Enter Quantity" name="qty" required></div>

                      <div><label for="category"><b>Suppliers</b></label></div>
                      <div>
                          <select id="supplier_id" name="supplier_id" required>
                              <option class="placeholder" value="" disabled selected>Select Supplier</option>
                              <?php
                                // Run a fresh query here (since $suppliersResult is already consumed earlier)
                                $suppliersQuery2 = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
                                $suppliersResult2 = mysqli_query($con, $suppliersQuery2);

                                while ($supplier = mysqli_fetch_assoc($suppliersResult2)) { ?>
                                  <option value="<?php echo htmlspecialchars($supplier['supplier_id']); ?>">
                                      <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                  </option>
                              <?php } ?>
                          </select>

                      </div>

                    </div>
                      <br>     
                      <hr>          
                      <div class="confirmOrCancel">
                        <button type="button" onclick="closeModal('modal-stockin-<?= $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                        <button type="submit" class="confirm" style="float: right">Confirm</button>
                      </div>
                </form>
            </div>
          </div>
        </td>

        <!-- EDIT PRODUCT -->
        <td style="padding: 5px;">
          <button onclick="openModal('modal-edit-<?= $row['product_id']; ?>')">
            <i class="fa-solid fa-pen-to-square"></i>&nbsp;Edit
          </button>

          <div id="modal-edit-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">
              <span onclick="closeModal('modal-edit-<?= $row['product_id']; ?>')" class="close">&times;</span>
              <h3>Edit Product</h3>
              <form action="edit_product.php" method="POST">
                <div class="modalHorizontal">
                  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                  <div><input type="hidden" name="product_id" value="<?= $row['product_id']; ?>"></div>

                  <div><label><b>Name</b></label></div>
                  <div><input type="text" name="name" placeholder="Enter Product Name"><br></div>

                  <div><label><b>Barcode</b></label></div>
                  <div><input type="text" name="barcode" placeholder="Submit Barcode"><br></div>

                  <div><label><b>Category</b></label></div>
                  <div><select name="category">
                    <option value="" disabled selected>Select Category</option>
                    <option value="Braking System">Braking System</option>
                    <option value="Interior Accessories">Interior Accessories</option>
                    <option value="Engine Component">Engine Components</option>
                    <option value="Lighting">Lighting</option>
                    <option value="Electrical Component">Electrical Components</option>
                    <option value="Tires and Wheels">Tires and Wheels</option>
                  </select></div>
                
                  <div><label><b>Model</b></label></div>
                  <div><input type="text" name="model" placeholder="Submit Model"><br></div>

                </div>

                <br>   

                <div class="modalHorizontal">

                  <div><label><b>Reason</b></label></div>
                  <div><input type="text" name="reason" placeholder="Enter Reason for adjustment" required></div>

                </div>

                <br> 
                <hr>
                <div class="confirmOrCancel">
                  <button type="button" onclick="closeModal('modal-edit-<?= $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                  <button type="submit" class="confirm" style="float: right">Confirm</button>
                </div>
                
              </form>
            </div>
          </div>
        </td>

        <!-- ADJUST STOCK -->
        <td style="padding: 5px;">
          <button onclick="openModal('modal-adjust-<?= $row['product_id']; ?>')">
            <i class="fa-solid fa-sliders"></i>&nbsp;Adj
          </button>

          <div id="modal-adjust-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">
              <span onclick="closeModal('modal-adjust-<?= $row['product_id']; ?>')" class="close">&times;</span>
              <h3>Adjust Product</h3>
              <form action="stock_adjustment.php" method="POST">    
                <div class="modalHorizontal">
                  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                  <div><input type="hidden" name="product_id" value="<?= $row['product_id']; ?>"></div>

                  <div><label><b>Quantity After Adjustment</b></label></div>
                  <div><input type="number" min="0" max="1000" name="qty" placeholder="Enter Quantity" required><br></div>

                  <div><label><b>Reason</b></label></div>
                  <div><input type="text" name="reason" placeholder="Enter Reason for adjustment" required></div>
        
                </div>

                <br>
                <hr>
                <div class="confirmOrCancel">
                    <button type="button" onclick="closeModal('modal-adjust-<?= $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                    <button type="submit" class="confirm" style="float: right">Confirm</button>
                </div>

              </form>
            </div>
          </div>
        </td>

        <!-- DELETE PRODUCT -->
        <td style="padding: 5px;">
          <button onclick="openModal('modal-delete-<?= $row['product_id']; ?>')">
            <i class="fa-solid fa-trash"></i>&nbsp;Del
          </button>

          <div id="modal-delete-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">  
              <span onclick="closeModal('modal-delete-<?= $row['product_id']; ?>')" class="close">&times;</span>
                <form action="delete_product.php" method="POST">
                  <div class="container">
                    <h3>Delete Product</h3>
                    <p>Are you sure you want to delete this product from the inventory?</p>
                    <hr>
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                    <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
                    <input type="hidden" name="name" value="<?= $row['name']; ?>">
                    <input type="hidden" name="barcode" value="<?= $row['barcode']; ?>">
                    <input type="hidden" name="price" value="<?= $row['price']; ?>">
                    <input type="hidden" name="category" value="<?= $row['category']; ?>">
                    <input type="hidden" name="model" value="<?= $row['model']; ?>">
                    <input type="hidden" name="quantity" value="<?= $row['quantity']; ?>">

                    <div class="confirmOrCancel">
                      <button type="button" onclick="closeModal('modal-delete-<?= $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                      <button type="submit" class="confirm" style="float: right">Confirm</button>
                    </div>
                  </div>
                </form>
            </div>
          </div>
        </td>

        <!-- DEFECTIVE PRODUCT -->
        <td style="padding: 5px;">
          <button onclick="openModal('modal-defective-<?= $row['product_id']; ?>')">
            <i class="fa-solid fa-wrench"></i>&nbsp;Def
          </button>

          <div id="modal-defective-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">  
              <span onclick="closeModal('modal-defective-<?= $row['product_id']; ?>')" class="close">&times;</span>
                <h3>Defective Product</h3>
                <form action="defective.php" method="POST">    
                  <div class="modalHorizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                    <div><input type="hidden" name="product_id" value="<?= $row['product_id']; ?>"></div>

                    <div><label><b>Quantity of Defective Products</b></label></div>
                    <div><input type="number" min="0" max="1000" name="qty" placeholder="Enter Quantity" required><br></div>
                    
                    <div><label for="category"><b>Suppliers</b></label></div>
                    <div>
                          <select id="supplier_id" name="supplier_id" required>
                              <option class="placeholder" value="" disabled selected>Select Supplier</option>
                              <?php
                                $suppliersQuery3 = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
                                $suppliersResult3 = mysqli_query($con, $suppliersQuery3);

                                while ($supplier = mysqli_fetch_assoc($suppliersResult3)) { ?>
                                  <option value="<?php echo htmlspecialchars($supplier['supplier_id']); ?>">
                                      <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                  </option>
                              <?php } ?>
                          </select>
                    </div>
                    
                  </div>

                  <br>
                  <hr>
                  <div class="confirmOrCancel">
                      <button type="button" onclick="closeModal('modal-defective-<?= $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                      <button type="submit" class="confirm" style="float: right">Confirm</button>
                  </div>

                </form>
            </div>
          </div>
        </td>

        <?php } ?>

      </tr>
    <?php } ?>
  </table>
</div>

<!-- Sorting Function -->

<script>
function sortTable(order) {
    const table = document.getElementById('myTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));

    const bodyRows = rows.length ? rows : Array.from(table.querySelectorAll('tr')).slice(1);

    bodyRows.sort(function (a, b) {
        if (order === 'alphabetical_az') {
            const nameA = a.cells[0].textContent.trim().toLowerCase();
            const nameB = b.cells[0].textContent.trim().toLowerCase();
            return nameA.localeCompare(nameB);
        }
        if (order === 'alphabetical_za') {
            const nameA = a.cells[0].textContent.trim().toLowerCase();
            const nameB = b.cells[0].textContent.trim().toLowerCase();
            return nameB.localeCompare(nameA);
        }
        if (order === 'date_added_newest') {
            const dateA = new Date(a.cells[8].textContent.trim());
            const dateB = new Date(b.cells[8].textContent.trim());
            return dateB - dateA;
        }
        if (order === 'date_added_oldest') {
            const dateA = new Date(a.cells[8].textContent.trim());
            const dateB = new Date(b.cells[8].textContent.trim());
            return dateA - dateB;
        }
    });

    bodyRows.forEach(row => table.appendChild(row));
}

document.getElementById('order').addEventListener('change', function () {
    sortTable(this.value);
});

// Default to Alphabetical (A-Z) on page load
window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('order').value = 'alphabetical_az';
    sortTable('alphabetical_az');
});
</script>

<!-- Search Function -->

<script>
function filterTable() {
  var input1, input2, filter1, filter2, table, tr, td, i, j, txtValue;
  
  input1 = document.getElementById("myInput1");
  input2 = document.getElementById("myInput2");
  filter1 = input1.value.toUpperCase();
  filter2 = input2.value.toUpperCase();
  
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  
  for (i = 1; i < tr.length; i++) {
    tr[i].style.display = "none"; 
    td = tr[i].getElementsByTagName("td");

    let nameMatch = false;
    let modelMatch = false;

    if (td[0]) { 
      txtValue = td[0].textContent || td[0].innerText;
      if (txtValue.toUpperCase().indexOf(filter1) > -1) {
        nameMatch = true;
      }
    }

    if (td[3]) { 
      txtValue = td[3].textContent || td[3].innerText;
      if (txtValue.toUpperCase().indexOf(filter2) > -1) {
        modelMatch = true;
      }
    }

    if (nameMatch && modelMatch) {
      tr[i].style.display = "";
    }
  }
}

</script>
        
<!-- Category Function -->

<script>  
document.getElementById('category').addEventListener('change', function() {
    const selectedCategory = this.value;
    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const categoryCell = rows[i].getElementsByTagName('td')[2]; 
        if (selectedCategory === 'all' || (categoryCell && categoryCell.innerText === selectedCategory)) {
            rows[i].style.display = ""; 
        } else {
            rows[i].style.display = "none"; 
        }
    }
});
        
</script>

<!-- Modal -->

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

<script>
function toggleSearchbar() {
  const searchbar = document.querySelector('.searchbar');
  searchbar.classList.toggle('show');
}
</script>



</body>