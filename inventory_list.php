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
<link rel="stylesheet" href="styles/all.css">

<link rel="stylesheet" href="styles/dashboardStyle.css">
<style>

    @font-face {
        font-family: 'Metropolis';
        src: url(fonts/Metropolis-Light.otf);
    }
    .products_table {
      overflow-x:auto; 
      height: 539px; 
      overflow-y: scroll;
    }
    
    /* body { 
    overflow: hidden; 
    /* height: 100vh; */
    /* margin: 0;  
    } */

    i {
      color: #121212;
      transition: all .1s;
    } 

    .sidenavIcon {
      color: white;
      transition: all .1s;
    } 

    .dark-mode { 
      i {
        color: white;
        transition: all .1s;
      }
    }

    .input-container {
    
    width: 100%;
}

    .input-container i {
        
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa; 
        transition: all .1s;
    }

    .stock_in {
        background-color: #950606;
        color: white;
        transition: all .1s;
    }

    .add_product {
        background-color: white;
        color: black;
        transition: all .1s;
    }

    input[type=text] {
        margin-top: 5px;
        margin-bottom: 5px;
        width: 100%;
        border: 1px solid #950606;
        font-family: 'Metropolis';
        transition: all .1s;
        padding: 12px 20px;
    }

    select {
        margin-top: 10px;
        margin-bottom: 10px;
        width: 25%;
        border: 1px solid #950606;
        font-family: 'Metropolis';
        transition: all .1s;
        padding: 12px 20px;
    }

/* Dark Mode CSS */
  
  .dark-mode {
    input[type=text], input[type=password], input[type=number], input[type=date], select {
      background-color: #121212;
      color: white;
      border: 1px solid #950606;
      transition: all .1s;
    } 

    input[type=file] {
      border: 1px solid #121212;
      color: white;
      background-color: #121212;
      transition: all .1s;
    }

    .modal-content {
      background-color: #282828;
      color: white;
      transition: all .1s;
    }

    #myInput {
      background-color: #121212;
      color: white;
      transition: all .1s;
      border-color: #950606;
      transition: all .1s;
    }

    table{
        background-color: #121212;
        color: white;
        transition: all .1s;
        border-color: #950606;
        transition: all .1s;
    }

    tr:nth-child(even) {
        background-color: #222222;
        color: white;
        transition: all .1s;
    }

    .add_product {
        background-color: #950606;
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
    background-color: #cecece;
}

input[type=password], input[type=number], input[type=date] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #950606;
  box-sizing: border-box;
  color: black;
  font-family: 'Metropolis';
}

input[type=file] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  box-sizing: border-box;
  color: black;
  font-family: 'Metropolis';
  background: white;
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

.edit_product {
  background-color: #04AA6D;
  color: white;
  padding: 5px 10px;
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

.cancelbtn, .signupbtn {
  width: auto;
  padding: 10px 18px;
  background-color: #f44336;
  border-radius: 8px;
  color: white;
  font-family: 'Metropolis';
}

.signupbtn {
  float: right;
  width: auto;
  padding: 10px 18px;
  background-color: #04AA6D;
  border-radius: 8px;
  color: white;
  font-family: 'Metropolis';
}

.imgcontainer {
  text-align: center;
  margin: 24px 0 12px 0;
  position: relative;
}

img.avatar {
  width: 40%;
  border-radius: 50%;
}

.container {
  padding: 16px;
  
}

span.psw {
  float: right;
  padding-top: 16px;
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

  .products_table {
      
      height: 500px; 
    
    }

  input[type=file] {
    width: 100%;
  }

  button{
      font-size: small;
    }

    input[type=text] {
      font-size: small;
    }

}

select {
        opacity: 1; 
    }

    option.placeholder {
        color: gray; 
        opacity: 0.5; 
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
    <!--
  <a class="active" href="#home">Home</a>
  <a href="#news">News</a>
  <a href="#contact">Contact</a>
  -->
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
  <a href="stock_in_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Stock-In History</a>
  <a href="edit_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Editing History</a>
  <a href="adjustment_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Adjustment History</a>
  <a href="delete_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Deletion History</a>
  <a href="order_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Ordering History</a>
</div>

<!-- Main Content -->
<div style="padding-left:16px">
  <h1>Inventory List</h1>
  <p>Take a look at the list of products</p>
  
  <!--
  <p>The "Help" link in the navigation bar is separated from the rest of the navigation links, resulting in a "split navigation" layout.</p>
  -->
</div>


<hr class="hrRed">

<!-- Columns -->

<div class="topdash">

<div class="input-container">
    <!-- <i class="fa-solid fa-magnifying-glass"></i> -->
    <p>Search the product name: </p>
	<input type="text" class="filter_name_textbox" id="myInput1" onkeyup="filterTable()" placeholder="Filter product names..." title="Type in a name">

	<p>Search the model name: </p>
	<input type="text" class="filter_name_textbox" id="myInput2" onkeyup="filterTable()" placeholder="Filter model names..." title="Type in a name">


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
        
</div>

        <button class = "add_product lowerButton"   onclick="document.getElementById('id01').style.display='block'" style="width:auto;"><i class="fa-solid fa-plus"></i> Add Product</button>

        <br><br>

<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
  <form class="modal-content" action="add_product.php" method="POST" enctype="multipart/form-data" onsubmit="return submitForm(event);">
    <div class="container">
        <h1>Add Product</h1>
        <p>Please fill in the text fields to add a new product to the list.</p>
        <hr>
        <br>
        <label for="name"><b>Name</b></label>
        <input type="text" placeholder="Enter Product Name" name="name" required><br>

        <!--
        <label for="image"><b>Image</b></label><br>
        <input type="file" placeholder="Submit File Image" name="image" accept="image/*" required><br>
        -->

        <label for="barcode"><b>Barcode</b></label><br>
        <input type="text" placeholder="Submit Barcode" name="barcode" required><br>

        <label for="category"><b>Category</b></label><br>
        <select id="category" placeholder="Select Category" name="category" required>
          <option class="placeholder" value="" disabled selected>Category</option>
            <option value="all">All</option>
            <option value="Braking System">Braking System</option>
            <option value="Interior Accessories">Interior Accessories</option>
            <option value="Engine Component">Engine Components</option>
            <option value="Lighting">Lighting</option>
            <option value="Electrical Component">Electrical Components</option>
            <option value="Tires and Wheels">Tires and Wheels</option>
        </select><br>
            
        <label for="model"><b>Model</b></label><br>
        <input type="text" placeholder="Submit Model" name="model"><br>

        <label for="quantity"><b>Initial Quantity</b></label>
        <input type="number" min="0" max="1000" placeholder="Enter Quantity" name="quantity" required ><br>

        <label for="price"><b>Price</b></label>
        <input type="number" min="1.00" max="999999.99" value="1.00" placeholder="Enter Product Price" name="price" required><br>

        <br>
        <div class="clearfix">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
            <button type="submit" class="signupbtn">Confirm</button>
        </div>
    </div>
  </form>
</div>

    <!--
    <h1>
        Dashboard
    </h1>
    -->
    <center>
    <div class = "products_table">
    <table id = "myTable">
        <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Barcode</th>
            <th>Category</th>
            <th>Model</th>
            <th>Availability</th>
            <th>Quantity</th>
            <th>Price</th>
            <th class = "action">Stock In</th>
            <th class = "action">Edit</th>
            <th>Adjust</th>
            <th>Delete</th>
        </tr>
        <?php
            while($row = mysqli_fetch_assoc($result)) {
        ?> 
            <tr>
                <td><?php echo $row['product_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['barcode']; ?></td>             
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['model']; ?></td>
                <td><?php echo $row['quantity'] > 0 ? "In Stock" : "Out of Stock"; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['price']; ?></td>

                <td>
                  <button class="stock_in lowerButton" onclick="openAdjustModal2(<?php echo $row['product_id']; ?>)" style="width:auto;"><i class="fa-solid fa-plus" style="color:white"></i></button>

                  <div id="modal_adjust_<?php echo $row['product_id']; ?>" class="modal">
                      <span onclick="closeModal('modal_adjust_<?php echo $row['product_id']; ?>')" class="close" title="Close Modal">&times;</span>
                      <form class="modal-content" action="stock_in.php" method="POST">
                          <div class="container">
                              <h1>Stock In</h1>
                              <p>Please fill in the text fields to add stocks to a product.</p>
                              <hr>
                              <label for="product_id"><b>Product ID</b></label>
                              <input type="number" id="adjust_product_id_2_<?php echo $row['product_id']; ?>" name="product_id" value="<?php echo $row['product_id']; ?>" required><br>
                              <label for="qty"><b>Quantity</b></label>
                              <input type="number" min="1" max="1000" placeholder="Enter Quantity" name="qty" required><br>
                              <label for="delivery_id"><b>Delivery ID</b></label><br>
                              <input type="text" placeholder="Enter Delivery ID" name="delivery_id" required>
                              <div class="clearfix">
                                  <button type="button" onclick="closeModal('modal_adjust_<?php echo $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                                  <button type="submit" class="signupbtn">Confirm</button>
                              </div>
                          </div>
                      </form>
                  </div>
              </td>

              <td>
                  <button class="stock_in lowerButton" onclick="openAdjustModal4(<?php echo $row['product_id']; ?>)" style="width:auto;"><i class="fa-solid fa-pen-to-square" style="color:white"></i></button>

                  <div id="modal_edit_<?php echo $row['product_id']; ?>" class="modal">
                      <span onclick="closeModal('modal_edit_<?php echo $row['product_id']; ?>')" class="close" title="Close Modal">&times;</span>
                      <form class="modal-content" action="edit_product.php" method="POST">
                          <div class="container">
                              <h1>Edit Product</h1>
                              <p>Please fill in the text fields to edit the data of a product.</p>
                              <hr>
                              <label for="product_id"><b>Product ID</b></label>
                              <input type="number" id="adjust_product_id_4_<?php echo $row['product_id']; ?>" name="product_id" value="<?php echo $row['product_id']; ?>" required><br>
                              <label for="name"><b>Name</b></label>
                              <input type="text" placeholder="Enter Product Name" name="name"><br>
                              <label for="barcode"><b>Barcode</b></label><br>
                              <input type="text" placeholder="Submit Barcode" name="barcode"><br>
                              <label for="category"><b>Category</b></label><br>
                              <select id="category" placeholder="Select Category" name="category">
                                 <option class="placeholder" value="" disabled selected>Category</option>
                                    <option value="Braking System">Braking System</option>
                                    <option value="Interior Accessories">Interior Accessories</option>
                                    <option value="Engine Component">Engine Components</option>
                                    <option value="Lighting">Lighting</option>
                                    <option value="Electrical Component">Electrical Components</option>
                                    <option value="Tires and Wheels">Tires and Wheels</option>
                              </select><br>
                              <label for="model"><b>Model</b></label><br>
                              <input type="text" placeholder="Submit Model" name="model"><br>
                              <label for="reason"><b>Reason</b></label>
                              <input type="text" placeholder="Enter Reason for adjustment" name="reason">
                              <div class="clearfix">
                                  <button type="button" onclick="closeModal('modal_edit_<?php echo $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                                  <button type="submit" class="signupbtn">Confirm</button>
                              </div>
                          </div>
                      </form>
                  </div>
              </td>

              <td>
                  <button class="stock_in lowerButton" onclick="openAdjustModal(<?php echo $row['product_id']; ?>)" style="width:auto;"><i class="fa-solid fa-sliders" style="color:white"></i></button>

                  <div id="id03_<?php echo $row['product_id']; ?>" class="modal">
                      <span onclick="closeModal('id03_<?php echo $row['product_id']; ?>')" class="close" title="Close Modal">&times;</span>
                      <form class="modal-content" action="stock_adjustment.php" method="POST">
                          <div class="container">
                              <h1>Adjust</h1>
                              <p>Please fill in the text fields to adjust the stocks of a product.</p>
                              <hr>
                              <label for="product_id"><b>Product ID</b></label>
                              <input type="number" id="adjust_product_id_<?php echo $row['product_id']; ?>" name="product_id" required><br>
                              <label for="qty"><b>Quantity After Adjustment</b></label>
                              <input type="number" min="0" max="1000" placeholder="Enter Quantity" name="qty" required><br>
                              <label for="reason"><b>Reason</b></label>
                              <input type="text" placeholder="Enter Reason for adjustment" name="reason" required>
                              <div class="clearfix">
                                  <button type="button" onclick="closeModal('id03_<?php echo $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                                  <button type="submit" class="signupbtn">Confirm</button>
                              </div>
                          </div>
                      </form>
                  </div>
              </td>

              <td>
                  <button class="stock_in lowerButton" onclick="openAdjustModal5(<?php echo $row['product_id']; ?>)" style="width:auto;"><i class="fa-solid fa-trash" style="color:white"></i></button>

                  <div id="modal_delete_<?php echo $row['product_id']; ?>" class="modal">
                      <span onclick="closeModal('modal_delete_<?php echo $row['product_id']; ?>')" class="close" title="Close Modal">&times;</span>
                      <form class="modal-content" action="delete_product.php" method="POST">
                          <div class="container">
                              <h1>Delete Product</h1>
                              <hr>
                              <p>Are you sure you want to delete this product?</p>
                              <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                              <input type="hidden" name="barcode" value="<?php echo $row['barcode']; ?>">
                              <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                              <input type="hidden" name="category" value="<?php echo $row['category']; ?>">
                              <input type="hidden" name="model" value="<?php echo $row['model']; ?>">    
                              <input type="hidden" name="quantity" value="<?php echo $row['quantity']; ?>">
                              <div class="clearfix">
                                  <button type="button" onclick="closeModal('modal_delete_<?php echo $row['product_id']; ?>')" class="cancelbtn">Cancel</button>
                                  <button type="submit" class="signupbtn">Confirm</button>
                              </div>
                          </div>
                      </form>
                  </div>
              </td>

                
                <!--
                <td class="td_button">
                    <form action="#" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <button type="submit" class="remove_product" style="width:auto;">Adjust</button>
                    </form>
                </td>
                -->
            </tr>
        <?php
            }
        ?>
    </table>
    </div>
    </center>

 
</div>

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
        
<script>
function openAdjustModal(productId) {
    document.getElementById('adjust_product_id_' + productId).value = productId;
    document.getElementById('id03_' + productId).style.display = 'block';
}

function openAdjustModal2(productId) {
    document.getElementById('adjust_product_id_2_' + productId).value = productId;
    document.getElementById('modal_adjust_' + productId).style.display = 'block';
}

function openAdjustModal4(productId) {
    document.getElementById('adjust_product_id_4_' + productId).value = productId;
    document.getElementById('modal_edit_' + productId).style.display = 'block';
}

function openAdjustModal5(productId) {
    document.getElementById('modal_delete_' + productId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
}



</script>

<script>
var modal = document.getElementById('id02');

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<script>
var modal = document.getElementById('id03');

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<script>
function openEditModal(productId, name, quantity, price) {
    document.getElementById('edit_product_id').value = productId;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_product_quantity').value = quantity;
    document.getElementById('edit_product_price').value = price;
    document.getElementById('id02').style.display = 'block';
}
</script>

<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>

<script>
    function toggleDarkMode() {
        var element = document.body;
        element.classList.toggle("dark-mode");
        if (element.classList.contains("dark-mode")) {
            localStorage.setItem("dark-mode", "enabled");
        } else {
            localStorage.setItem("dark-mode", "disabled");
        }
    }

    window.onload = function() {
        if (localStorage.getItem("dark-mode") === "enabled") {
            document.body.classList.add("dark-mode");
        }
    }


    function submitForm(event) {
    event.preventDefault(); 

    const form = document.querySelector('.modal-content'); 
    const formData = new FormData(form);

    fetch('add_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('Error:')) {
            alert(data); 
        } else {
            window.location.href = 'inventory_list.php?success=1'; 
        }
    })
    .catch(error => console.error('Error:', error));
}

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

</body>
</html>