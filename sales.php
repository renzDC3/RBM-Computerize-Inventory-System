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
<link rel="stylesheet" href="styles/salesStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

</head>
<body>

<div class="topnav">
  <a class="image"><img src="images/rbm_tex.jpg" style="width: 50px; height: 15px"></a>
  <a href="dashboard.php">Dashboard</a>
  <a href="products.php">Products</a>
  <a class="active" href="#contact">Sales</a>
  <a href="history.php">History</a>
  <a class="logout" href="logout.php">Logout</a>
</div>

<div style="padding-left:16px">
  <h2>Sales</h2>
  <p>Come see......</p>
</div>

<!-- Checkout Section -->
<div class="checkout">
    <h3><i class="fa-solid fa-qrcode" style="font-size:24px;"></i> Scan</h3>
    <input type="text" id="barcode" placeholder="Scan barcode here" autofocus autocomplete="off">
    <hr>
    <div id="product-list" style="height: 54%; overflow-y:scroll; padding: 5px"></div>
    <div id="total">    
        <h3>Total: ₱ <span id="total-amount">0.00</span></h3>
    
    <input type="number" id="customer-cash" placeholder="Enter cash amount" step="0.01" min="0">
    <button id="submit-btn">Submit</button>
    </div>
</div>

<script>
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
                <table style="                
                  font-size: small;
                ">
                    <tr>
                        <td style="width: 70%"><h3>${name} (x${quantity})</h3></td>
                        <td style="width: 20%"><h3>₱ ${subtotal}</h3></td>
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
