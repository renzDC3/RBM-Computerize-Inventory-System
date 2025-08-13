<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    $query = "SELECT * FROM orders ORDER BY order_date DESC";
    $result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/salesHistoryStyle.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
</head>
<body>

<div class="topnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>
  <a href="dashboard.php">Dashboard</a>
  <a href="products.php">Products</a>
  <a href="sales.php">Sales</a>
  <a class="active" href="history.php">History</a>
  <a class="logout" href="logout.php">Logout</a>
</div>

<div style="padding-left:16px">
  <h2>History</h2>
  <p>Come see......</p>
</div>

<div class="pill-nav">
  <a href="history.php" style="margin: 0px 0px 0px 7px;">Add</a>
  <a href="stock_in_history.php">Stock In</a>
  <a href="edit_history.php">Edit</a>
  <a href="adjust_history.php">Adjust</a>
  <a href="delete_history.php">Delete</a>
  <a class="active" href="sales_history.php">Sales</a>
</div>

<div class="searchbar">
    <div><p> Sort order: </p></div>
    <div><select name="sort" id="sort" style="margin-left: 15px">
      <option value="desc">Newest First</option>  
      <option value="asc">Oldest First</option>
    
  </select></div>
    <div>
      <form class="form-inline" onsubmit="myFunction(); return false;">
          <label for="email">Start:</label>
          <input type="date" id="dateStart" placeholder="Start Date" title="Select start date">
          <label for="email">End:</label>
          <input type="date" id="dateEnd" placeholder="End Date" title="Select end date">
          <button type="submit">Filter By Date</button>
      </form>
    </div>
</div>

<div class="productsTable">
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
                      <button onclick="fetchOrderDetails(<?php echo $row['order_id']; ?>)" style="width:auto;">
                          Details
                      </button>

                      <!-- Modal for Order Details -->
                        <div id="id01" class="modal">
                            <div class="modal-content">
                                    <div class="container">
                                        <h1>Order Details</h1>
                                        <p>Here is the detail of that particular order.</p>
                                        <hr>
                                        <div style="height: 300px; overflow-y:scroll;">
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
                  </td>
                  <td>
                      <button style="width:auto;" onclick="window.open('generate_receipt.php?order_id=<?php echo $row['order_id']; ?>', '_blank');">
                          Receipt
                      </button>

                  </td>
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

</body>