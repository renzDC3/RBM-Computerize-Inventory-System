<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    $query = "
    SELECT 
        eph.*, 
        p.name,
        p.barcode,
        p.category,
        p.model
    FROM 
        edit_product_history eph
    JOIN 
        products p ON eph.product_id = p.product_id
    ORDER BY 
        eph.date_time DESC
    ";
    $result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="styles/editHistoryStyle.css">
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

<div class="pill-nav">
  <a href="history.php" style="margin: 0px 0px 0px 7px;">Add</a>
  <a href="stock_in_history.php">Stock In</a>
  <a class="active" href="edit_history.php">Edit</a>
  <a href="adjust_history.php">Adjust</a>
  <a href="delete_history.php">Delete</a>
  <a href="sales_history.php">Sales</a>
</div>

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
              <th>Edit Product ID</th>
              <th>Product ID</th>
              <th>Name</th>
              <th>Barcode</th>
              <th>Category</th>
              <th>Model</th>    
              <th>Reason</th>
              <th>User</th>    
          </tr>
          <?php
              while ($row = mysqli_fetch_assoc($result)) {
          ?>
              <tr>
                <td><?php echo $row['date_time']; ?></td>
                <td><?php echo $row['edit_product_history_id']; ?></td>
                <td><?php echo $row['product_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['barcode']; // Use the barcode text here ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['model']; ?></td>      
                <td><?php echo $row['reason']; ?></td>
                <td><?php echo $row['Id']; ?></td>     
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

    document.getElementById('category').addEventListener('change', function() {
    const selectedCategory = this.value;
    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const categoryCell = rows[i].getElementsByTagName('td')[5]; 
        if (selectedCategory === 'all' || (categoryCell && categoryCell.innerText === selectedCategory)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none"; 
        }
    }
});

</script>

</body>