<?php
session_start();
include("config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit; 
}

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "
    SELECT 
        dph.*    
    FROM 
        delete_product_history dph    
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}
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

        .history_table {
            height: 400px;
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
            font-size: large;
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

        /* Dark Mode CSS */
        .dark-mode {
            table {
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

        tr:nth-child(even) {
            background-color: #cecece;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="topnav">
        <span style="font-size:30px;cursor:pointer;color:white" onclick="openNav()">&#9776;</span>
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
          <a href="stock_in_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Stock-In History</a>
          <a href="edit_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Editing History</a>
          <a href="adjustment_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Adjustment History</a>
          <a href="delete_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Deletion History</a>
          <a href="order_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Ordering History</a>
        </div>

    <!-- Main Content -->
    <div style="padding-left:16px">
        <h1>Delete Product History</h1>
        <p>Take a look at the history of the deletion of products</p> 
    </div>

    <hr class="hrRed">

        <div class="topdash">
            <select name="sort" id="sort">
                <option value="asc">Oldest First</option>
                <option value="desc">Newest First</option>
            </select>
                
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
                <label for="dateStart">Start:</label>
                <input type="date" id="dateStart" title="Select start date">
                <label for="dateEnd">End:</label>
                <input type="date" id="dateEnd" title="Select end date">
                <button type="submit">Filter By Date</button>
            </form>

            <center>
                <div class="history_table" style="overflow-x:auto; overflow-y: scroll;">
                    <table id="myTable">
                        <tr>
                            <th>Date and Time</th>
                           	<th>Name</th>
                            <th>Barcode</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>User</th>
                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['date_time']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['barcode']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['Id']; ?></td>    

                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </center>
        </div>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
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
            const rows = Array.from(table.rows).slice(1); 

            rows.forEach(row => {
                const rowDate = new Date(row.cells[0].innerText);
                if ((dateStart && rowDate < new Date(dateStart)) || 
                    (dateEnd && rowDate > new Date(dateEnd))) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });
        }
            
        document.getElementById('category').addEventListener('change', function() {
            const selectedCategory = this.value;
            const table = document.getElementById('myTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const categoryCell = rows[i].getElementsByTagName('td')[4]; 
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
