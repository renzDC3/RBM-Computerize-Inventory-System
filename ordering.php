<?php
session_start();
include("config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$query = "SELECT product_id, name, barcode, price FROM products";
$result = mysqli_query($con, $query);
$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[$row['barcode']] = [
        'name' => $row['name'],
        'price' => (float)$row['price'],
        'product_id' => $row['product_id']
    ];
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        .topdash {
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        .button {
            border-radius: 8px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #product-list {
            margin-bottom: 20px;
        }
        .product {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }
        .product h3 {
            margin: 0;
        }
        .button {
            margin-top: none;
            cursor: pointer;
        }
        #total {
            text-align: left;
        }
        #submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        #submit-btn:hover {
            background-color: #218838;
        }
        @media (max-width: 720px) {
            input[type=number], input[type=text] {
                width: 100%;
            }
            input[type=number], input[type=text] {
                font-size: medium;
            }
            .button {
                width: 100%;
            }
            .topdash {
                width: 100%;
                margin: 10px;
            }
            .product_list {
                width: 100%;
            }
            .product {
                flex-direction: column;
            }
            tr {
                width: 25%;
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
  <a href="stock_in_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Stock-In History</a>
  <a href="edit_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Editing History</a>
  <a href="adjustment_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Adjustment History</a>
  <a href="delete_product_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Product Deletion History</a>
  <a href="order_history.php"><i class="sidenavIcon fa-solid fa-clock-rotate-left"></i> Ordering History</a>
</div>

<!-- Main Content -->
<div style="padding-left:16px">
    <h1>Ordering</h1>
    <p>Make orders on this page</p>
</div>

<hr class="hrRed">

<!-- Checkout Section -->
<div class="topdash">
    <h1>Checkout</h1>
    <input type="text" id="barcode" placeholder="Scan barcode here" autofocus autocomplete="off">
    <div id="product-list"></div>
    <div id="total">
        <h2>Total: ₱ <span id="total-amount">0.00</span></h2>
    </div>
    <input type="number" id="customer-cash" placeholder="Enter cash amount" step="0.01" min="0">
    <button id="submit-btn">Submit</button>
</div>

<script>
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }

    function toggleDarkMode() {
        const element = document.body;
        element.classList.toggle("dark-mode");
        localStorage.setItem("dark-mode", element.classList.contains("dark-mode") ? "enabled" : "disabled");
    }

    window.onload = function() {
        if (localStorage.getItem("dark-mode") === "enabled") {
            document.body.classList.add("dark-mode");
        }
    }

    const products = <?php echo json_encode($products); ?>; 

    let totalAmount = 0;
    const productList = {};
    const totalAmountDisplay = document.getElementById('total-amount');

    document.getElementById('barcode').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            const barcode = event.target.value;
            addProduct(barcode);
            event.target.value = ''; 
        }
    });

    document.getElementById('submit-btn').addEventListener('click', async function() {
    if (Object.keys(productList).length === 0) {
        alert('No products scanned! Please scan at least one product before submitting.');
        return;
    }

    const customerCash = parseFloat(document.getElementById('customer-cash').value);
    if (isNaN(customerCash) || customerCash < totalAmount) {
        alert('Insufficient cash! Please enter a valid amount.');
        return;
    }

    const change = (customerCash - totalAmount).toFixed(2);
    const orderDetails = Object.keys(productList).map(barcode => ({
        product_name: productList[barcode].name,
        product_id: products[barcode].product_id,
        quantity: productList[barcode].quantity,
        subtotal: (productList[barcode].quantity * productList[barcode].price).toFixed(2),
        price: productList[barcode].price.toFixed(2),
    }));

    const orderData = {
        total: totalAmount.toFixed(2),
        cash: customerCash.toFixed(2),
        change: change,
        order_details: orderDetails,
    };

    try {
        const response = await fetch('submit_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();
        if (result.success) {
            alert('Transaction successful! Thank you for your purchase.');
            resetCart();
            window.open(`generate_receipt.php?order_id=${result.order_id}`, '_blank');
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error processing your request: ' + error.message);
    }
});


    function addProduct(barcode) {
        const product = products[barcode];

        if (product) {
            if (productList[barcode]) {
                productList[barcode].quantity++;
            } else {
                productList[barcode] = { ...product, quantity: 1 };
            }

            updateProductList();
            updateTotal();
        } else {
            alert('Product not found!');
        }
    }

    function updateProductList() {
        const productListDiv = document.getElementById('product-list');
        productListDiv.innerHTML = ''; // Clear the list

        for (const barcode in productList) {
            const { name, price, quantity } = productList[barcode];
            const subtotal = (quantity * price).toFixed(2);

            const productElement = document.createElement('div');
            productElement.className = 'product';
            productElement.innerHTML = `
                <table style="border:none;">
                    <tr>
                        <td><h3>${name} (x${quantity})</h3></td>
                        <td><h3>₱ ${subtotal}</h3></td>
                        <td><button class="button" onclick="decrementProduct('${barcode}')">-</button></td>
                        <td><button class="button" onclick="removeProduct('${barcode}')">Remove</button></td>
                    </tr>
                </table>
            `;
            productListDiv.appendChild(productElement);
        }
    }

    function updateTotal() {
        totalAmount = Object.values(productList).reduce((total, product) => {
            return total + (product.price * product.quantity);
        }, 0);
        totalAmountDisplay.textContent = totalAmount.toFixed(2);
    }

    function decrementProduct(barcode) {
        if (productList[barcode].quantity > 1) {
            productList[barcode].quantity--;
        } else {
            delete productList[barcode];
        }
        updateProductList();
        updateTotal();
    }

    function removeProduct(barcode) {
        delete productList[barcode];
        updateProductList();
        updateTotal();
    }

    function resetCart() {
        Object.keys(productList).forEach(key => delete productList[key]);
        totalAmount = 0;
        totalAmountDisplay.textContent = totalAmount.toFixed(2);
        document.getElementById('customer-cash').value = '';
        document.getElementById('product-list').innerHTML = ''; 
    }
</script>

</body>
</html>
