<?php
    session_start();

    include("config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style2.css">
    <link rel="stylesheet" href="style/all.css">
    <link rel="stylesheet" href="style/dashboardStyle.css">
    <script src="https://kit.fontawesome.com/8a559c8a28.js" crossorigin="anonymous"></script>
    <title>Home</title>
</head>
<body>
    <div class="ken">

    </div>

    <div class="nav">
        <div class="logo">
            <p><a href="home.php"><i class="fa-solid fa-house"></i></a></p>
        </div>
        <div class="right-links">
            
        <?php

            $id = $_SESSION['id'];
            $query = mysqli_query($con,"SELECT*FROM users WHERE Id=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Uname = $result['Username'];
                //$res_Email = $result['Email'];
                //$res_Age = $result['Age'];
                $res_id = $result['Id'];
            }
            
            //  echo "<a href='edit.php?Id=$'res_Id' class='changeProf'>Change Profile</a>";
        ?>
            <!--<a href="edit.php" class="changeProf">Change Profile</a>-->
            <a href="logout.php"><button class="btn">Log Out</button></button></a>
        </div>
        
    </div>

    <main>

    <div class="row">
			<div class="col-lg-12">
				<div class="card card-default rounded-0 shadow">
                    <div class="card-header">
                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Product List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-end">
                                <button type="button" name="add" id="addProduct" class="btn btn-primary bg-gradient rounded-0 btn-sm"><i class="far fa-plus-square"></i> Add Product</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="productList" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>      
									<th>Category</th>	
									<th>Brand Name</th>									
                                    <th>Product Name</th>
									<th>Product Model</th>
                                    <th>Quantity</th>
                                    <th>Supplier Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

        <div id="productModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Product</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="productForm">
                                <input type="hidden" name="pid" id="pid" />
                                <input type="hidden" name="btn_action" id="btn_action" />
                                <div class="form-group">
                                    <label>Select Category</label>
                                    <select name="categoryid" id="categoryid" class="form-select rounded-0" required>
                                        <option value="">Select Category</option>
                                        <?php echo $inventory->categoryDropdownList();?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Select Brand</label>
                                    <select name="brandid" id="brandid" class="form-select rounded-0" required>
                                        <option value="">Select Brand</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" name="pname" id="pname" class="form-control rounded-0" required />
                                </div>
                                <div class="form-group">
                                    <label>Product Model</label>
                                    <input type="text" name="pmodel" id="pmodel" class="form-control rounded-0" required />
                                </div>
                                <div class="form-group">
                                    <label>Product Description</label>
                                    <textarea name="description" id="description" class="form-control rounded-0" rows="5" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Product Quantity</label>
                                    <div class="input-group">
                                        <input type="text" name="quantity" id="quantity" class="form-control rounded-0" required pattern="[+-]?([0-9]*[.])?[0-9]+" /> 
                                        <select name="unit" class="form-select rounded-0" id="unit" required>
                                            <option value="">Select Unit</option>
                                            <option value="Bags">Bags</option>
                                            <option value="Bottles">Bottles</option>
                                            <option value="Box">Box</option>
                                            <option value="Dozens">Dozens</option>
                                            <option value="Feet">Feet</option>
                                            <option value="Gallon">Gallon</option>
                                            <option value="Grams">Grams</option>
                                            <option value="Inch">Inch</option>
                                            <option value="Kg">Kg</option>
                                            <option value="Liters">Liters</option>
                                            <option value="Meter">Meter</option>
                                            <option value="Nos">Nos</option>
                                            <option value="Packet">Packet</option>
                                            <option value="Rolls">Rolls</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Product Base Price</label>
                                    <input type="text" name="base_price" id="base_price" class="form-control rounded-0" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
                                </div>
                                <div class="form-group">
                                    <label>Product Tax (%)</label>
                                    <input type="text" name="tax" id="tax" class="form-control rounded-0" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
                                </div>
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select name="supplierid" id="supplierid" class="form-select rounded-0" required>
                                        <option value="">Select Supplier</option>
                                        <?php echo $inventory->supplierDropdownList();?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="action" id="action" class="btn btn-primary rounded-0 btn-sm" value="Add" form="productForm"/>
                            <button type="button" class="btn btn-default border rounded-0 btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
            </div>
        </div>

        <div id="productViewModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" id="product_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-th-list"></i> Product Details</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <Div id="productDetails"></Div>
                        </div>
                        <div class="modal-footer">
                            
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	
</div>	

        <!--
        <div class="main-box top">
            <div class="top">
                
                <div class="box">
                    <p style="color=white">Hey <b><?php echo $res_Uname ?></b>!, Welcome to the website. It's awesome to have you here!</p>
                </div>
                
                <div class="box">
                    <p style="color: white">You are logged in!</p>
                    <p style="color: white">So, your email is <b><?php echo $res_Email ?></b>, huh? Cool, got it!</p>
                </div>
                
            </div>
            <div class="bottom">
                
                <div class="box">
                    <p>Nice one! So, your age is <b><?php echo $res_Age ?></b>? Sweet!</p>
                </div>
                
            </div>
        </div>
        -->

    </main>

</body>
</html>