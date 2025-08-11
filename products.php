<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    $query = "SELECT * FROM products";
    $result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/productsStyle.css">
</head>
<body>

<div class="topnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>
  <a href="dashboard.php">Dashboard</a>
  <a class="active" href="products.php">Products</a>
  <a href="sales.php">Sales</a>
  <a href="history.php">History</a>
  <a class="logout" href="logout.php">Logout</a>
</div>

<div style="padding-left:16px">
  <h2>Products List</h2>
  <p>Come see......</p>
</div>

<div class="searchbar">
    <div><p> Search product name: </p></div>
    <div><input type="text" class="filter_name_textbox" id="myInput1" onkeyup="filterTable()" placeholder="Filter product names..." title="Type in a name"></div>
    <div><p>Search the model name: </p></div>
    <div><input type="text" class="filter_name_textbox" id="myInput2" onkeyup="filterTable()" placeholder="Filter model names..." title="Type in a name"></div>
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
      <button class="addProduct" onclick="openModal('modal-add-product')" style="width:auto;">
        Add Product
      </button>
    </div>

    <!-- The Add Product Modal -->
    <div id="modal-add-product" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span onclick="closeModal('modal-add-product')" class="close">&times;</span>
            <h3>Add Product</h3>
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="modalHorizontal">
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
                </select></div>
                    
                <div><label for="model"><b>Model</b></label></div>
                <div><input type="text" placeholder="Submit Model" name="model"></div>
            </div>
            <br>
            
            <div class="modalHorizontal">
              <div><label for="quantity"><b>Initial Quantity</b></label></div>
                <div><input type="number" min="0" max="1000" placeholder="Enter Quantity" name="quantity" required ></div>

                <div><label for="price" style="margin-left: 56px"><b>Price</b></label></div>
                <div><input type="number" min="1.00" max="999999.99" value="1.00" placeholder="Enter Product Price" name="price" required></div>
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
    <tr>
      <th>Product ID</th>
      <th>Name</th>
      <th>Barcode</th>
      <th>Category</th>
      <th>Model</th>
      <th>Availability</th>
      <th>Quantity</th>
      <th>Price</th>
      <th class="action">Stock In</th>
      <th class="action">Edit</th>
      <th>Adjust</th>
      <th>Delete</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['product_id']; ?></td>
        <td><?= $row['name']; ?></td>
        <td><?= $row['barcode']; ?></td>
        <td><?= $row['category']; ?></td>
        <td><?= $row['model']; ?></td>
        <td><?= $row['quantity'] > 0 ? "In Stock" : "Out of Stock"; ?></td>
        <td><?= $row['quantity']; ?></td>
        <td><?= $row['price']; ?></td>

        <!-- STOCK IN -->
        <td>
          <button onclick="openModal('modal-stockin-<?= $row['product_id']; ?>')">
            Stock In
          </button>

          <div id="modal-stockin-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">
              <span onclick="closeModal('modal-stockin-<?= $row['product_id']; ?>')" class="close">&times;</span>
              <h3>Stock In Product</h3>
                <form action="stock_in.php" method="POST">
                    <div class="modalHorizontal">
                      <div><input type="hidden" name="product_id" value="<?= $row['product_id']; ?>"></div>

                      <div><label for="quantity"><b>Initial Quantity</b></label></div>
                      <div><input type="number" min="1" max="1000" placeholder="Enter Quantity" name="qty" required></div>
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
        <td>
          <button onclick="openModal('modal-edit-<?= $row['product_id']; ?>')">
            Edit
          </button>

          <div id="modal-edit-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">
              <span onclick="closeModal('modal-edit-<?= $row['product_id']; ?>')" class="close">&times;</span>
              <h3>Edit Product</h3>
              <form action="edit_product.php" method="POST">
                <div class="modalHorizontal">
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
                  <div><input type="text" name="reason" placeholder="Enter Reason for adjustment"></div>

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
        <td>
          <button onclick="openModal('modal-adjust-<?= $row['product_id']; ?>')">
            Adjust
          </button>

          <div id="modal-adjust-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">
              <span onclick="closeModal('modal-adjust-<?= $row['product_id']; ?>')" class="close">&times;</span>
              <h3>Adjust Product</h3>
              <form action="stock_adjustment.php" method="POST">    
                <div class="modalHorizontal">
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
        <td>
          <button onclick="openModal('modal-delete-<?= $row['product_id']; ?>')">
            Delete
          </button>

          <div id="modal-delete-<?= $row['product_id']; ?>" class="modal">
            <div class="modal-content">  
              <span onclick="closeModal('modal-delete-<?= $row['product_id']; ?>')" class="close">&times;</span>
                <form action="delete_product.php" method="POST">
                  <div class="container">
                    <h3>Delete Product</h3>
                    <p>Are you sure you want to delete this product?</p>
                    <hr>
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
      </tr>
    <?php } ?>
  </table>
</div>

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

    if (td[1]) { 
      txtValue = td[1].textContent || td[1].innerText;
      if (txtValue.toUpperCase().indexOf(filter1) > -1) {
        nameMatch = true;
      }
    }

    if (td[4]) { 
      txtValue = td[4].textContent || td[4].innerText;
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
        const categoryCell = rows[i].getElementsByTagName('td')[3]; 
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

// Close modal when clicking outside
window.onclick = function(event) {
  const modals = document.querySelectorAll('.modal');
  modals.forEach(modal => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });
};
</script>

</body>