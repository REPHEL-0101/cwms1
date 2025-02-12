<!-- Test 2 File -->
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    // Initialize billing variables
    $total = 0;
    $items = array();
    $message = '';

    // Fetch inventory items
    $sql = "SELECT * FROM inventory WHERE Quantity > 0";
    $query = $dbh->prepare($sql);
    $query->execute();
    $inventory_items = $query->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['book'])) {
        $ptype = $_POST['packagetype'];
        $wpoint = $_POST['washingpoint'];
        $fname = $_POST['fname'];
        $mobile = $_POST['contactno'];
        $date = $_POST['washdate'];
        $time = $_POST['washtime'];
        $message = $_POST['message'];
        $status = 'New';
        $bno = mt_rand(100000000, 999999999);

        try {
            $dbh->beginTransaction();

            // Insert booking
            $sql = "INSERT INTO tblcarwashbooking(bookingId,packageType,carWashPoint,fullName,mobileNumber,washDate,washTime,message,status) VALUES(:bno,:ptype,:wpoint,:fname,:mobile,:date,:time,:message,:status)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':bno', $bno, PDO::PARAM_STR);
            $query->bindParam(':ptype', $ptype, PDO::PARAM_STR);
            $query->bindParam(':wpoint', $wpoint, PDO::PARAM_STR);
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
            $query->bindParam(':date', $date, PDO::PARAM_STR);
            $query->bindParam(':time', $time, PDO::PARAM_STR);
            $query->bindParam(':message', $message, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->execute();
            $bookingId = $dbh->lastInsertId();

            // Process accessories if any
            if (isset($_POST['accessories']) && is_array($_POST['accessories'])) {
                foreach ($_POST['accessories'] as $item) {
                    $sql = "INSERT INTO booking_accessories (booking_id, item_id, quantity) VALUES (:booking_id, :item_id, :quantity)";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
                    $query->bindParam(':item_id', $item['id'], PDO::PARAM_INT);
                    $query->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $query->execute();

                    // Update inventory
                    $sql = "UPDATE inventory SET Quantity = Quantity - :quantity WHERE ItemID = :item_id";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $query->bindParam(':item_id', $item['id'], PDO::PARAM_INT);
                    $query->execute();
                }
            }

            $dbh->commit();
            echo '<script>alert("Booking successful. Booking number: ' . $bno . '")</script>';
            echo "<script>window.location.href ='new-booking.php'</script>";
        } catch (Exception $e) {
            $dbh->rollBack();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>CWMS | Add Car Washing Booking</title>
    <link href="img/favicon.ico" rel="icon">
    <link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="css/morris.css" type="text/css" />
    <link rel="stylesheet" href="css/checkboxes.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="js/jquery-2.1.4.min.js"></script>
    <link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css' />
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step-indicator li {
            list-style: none;
            color: #DDE3EC;
            font-weight: 500;
        }

        .step-indicator li.active {
            color: #FE6600;
            font-weight: bold;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .form-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .form-buttons button:hover {
            background-color: #FE6600;
            color: white;
            transform: scale(1.05);
        }

        input:focus,
        textarea:focus {
            border-color: #FE6600;
            box-shadow: 0 0 5px rgba(106, 100, 241, 0.5);
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="left-content">
            <div class="mother-grid-inner">
                <?php include('includes/header.php'); ?>
                <div class="clearfix"></div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a><i class="fa fa-angle-right"></i>Add Car Washing Booking</li>
                </ol>
                <?php include('includes/booking.php'); ?>
                <div class="grid-form">
                    <div class="grid-form1">
                        <h3>Add Car Washing Booking</h3>
                        <ul class="step-indicator">
                            <li class="active">Basic Information</li>
                            <li>Services</li>
                            <li>Accessories</li>
                            <li>Confirm</li>
                        </ul>
                        <form class="form-horizontal" name="washingpoint" method="post" enctype="multipart/form-data">
                            <!-- Step 1: Basic Information -->
                            <div class="form-step active" id="basic-info">
                                <div class="form-group">
                                    <label for="focusedinput" class="col-sm-2 control-label">Customer Details</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="searchInput" name="contactno" class="form-control" pattern="[0-9]{10}" title="10 numeric characters only" required placeholder="Search by Mobile Number...">
                                        <div id="searchResults"></div>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" id="customerName"  name="fname" class="form-control" required placeholder="Customer Name" disabled>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="#" data-toggle="modal" data-target="#addCustomerModal" class="text-primary">New Customer</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="focusedinput" class="col-sm-2 control-label">Vehicle Details</label>
                                    <div class="col-sm-3">
                                        <select name="vehicletype" required class="form-control">
                                            <option value="">Select Vehicle Type</option>
                                            <option value="Sedan">Sedan</option>
                                            <option value="SUV">SUV</option>
                                            <option value="Truck">Truck</option>
                                            <option value="Hatchback">Hatchback</option>
                                            <option value="Bike">Bike</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" name="licenseplate" class="form-control" required placeholder="License Plate (TN-11-BD-7337)" pattern="[A-Z]{2}-[0-9]{2}-[A-Z]{2}-[0-9]{4}">
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="add-vehicletype.php" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Vehicle Type</a>
                                    </div>
                                </div>
                                <div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Washing Point</label>
									<div class="col-sm-6">
								        <select name="washingpoint" required class="form-control">
								        <!--	<option value="">Select Washing Point</option> -->
										<?php $sql = "SELECT * from tblwashingpoints";
										$query = $dbh -> prepare($sql);
										$query->execute();
										$results=$query->fetchAll(PDO::FETCH_OBJ);
										foreach($results as $result)
										{               ?>  
											<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->washingPointName);?> (<?php echo htmlentities($result->washingPointAddress);?>)</option>
										<?php } ?>
								        </select>
									</div>

									<div class="col-sm-3">
									<!-- New Washing Point Link -->
                          			<a href="addcar-washpoint.php" class="text-primary">New Washing Point</a>
       								</div>
								</div>

								<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Package Type</label>
									<div class="col-sm-6">
                                    <select name="packagetype" required class="form-control">
									<option value="">Select Package Type</option>
										<?php $sql = "SELECT * from package_details";
										$query = $dbh -> prepare($sql);
										$query->execute();
										$results=$query->fetchAll(PDO::FETCH_OBJ);
										foreach($results as $result)
										{               ?>  
											<option value="<?php echo htmlentities($result->packageid);?>"><?php echo htmlentities($result->packagename);?> (₹ <?php echo htmlentities($result->price);?>)</option>
										<?php } ?>
								    </select>
									<!-- <select name="packagetype" required class="form-control">
										<option value="">Package Type</option>
										<option value="1">Small Package (₹ 4500.00)</option>
										<option value="2">Medium Package (₹ 5500.00)</option>
										<option value="3 ">Large Package (₹ 6500.00)</option>
									</select> -->
									</div>
									<div class="col-sm-3">
									<!-- New Package Link -->
                          			<a href="add-package.php" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Package</a>
       								</div>
								</div>
                            </div>

                            <!-- Pop Ups Start -->
                             <!--Model-->
                                <div class="modal fade" id="addCustomerModal" role="dialog">
                                    <div class="modal-dialog">
                                    
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h4 class="modal-title">Add New Customer</h4>
                                        </div>
                                        <div class="modal-body">
                                           <!-- Form for adding a new customer -->
                                            <form id="customerForm" method="post">
                                                <div class="form-group">
                                                    <label for="customerName">Customer Name</label>
                                                    <input type="text" class="form-control" id="customerName" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="customerMobile">Customer Mobile</label>
                                                    <input type="text" class="form-control" id="customerMobile" required>
                                                </div>
                                                <!-- Add more fields as needed -->
                                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                                            </form>
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                    
                                        </div>
                                </div>
                                <script>
        $(document).ready(function() {
            $('#searchBox').on('input', function() {
                var query = $(this).val();
                if (query.length > 0) {
                    $.get('search_customers.php', { query: query }, function(data) {
                        var results = $('#searchResults');
                        results.empty();
                        data.forEach(function(customer) {
                            results.append('<div class="result-item">' + customer.name + ' - ' + customer.mobile + '</div>');
                        });
                    });
                } else {
                    $('#searchResults').empty();
                }
            });

            $('#addCustomerBtn').click(function() {
                $('#addCustomerModal').show();
            });

            $('#customerForm').on('submit', function(e) {
                e.preventDefault();
                console.log("submit triggerd");
                
                var name = $('#customerName').val();
                var mobile = $('#customerMobile').val();
                console.log(name,mobile);
                
                $.post('add_customer.php', { name: name, mobile: mobile }, function(response) {
                    alert('Customer added successfully!');
                    // $('#addCustomerModal').hide();
                    $('#customerForm')[0].reset();
                }, 'json').fail(function(xhr, status, error) {
    console.error('AJAX Error:', status, error);
    alert('Mobile Number Already Exists. Failed to add customer.');
});
            });
        });
    </script>
                                <!-- Trial 2 -->

                                <!--Model-->
 <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Update Booking #<?php echo $_GET['bookingid'];?></h4>
        </div>
        <div class="modal-body">
			<form method="post">   
			<p>
            <select name="txntype" required class="form-control">
                <option value="">Transaction Type</option>
                <option value="e-Wallet">e-Wallet</option>
                 <option value="UPI">UPI</option>
                  <option value="Debit/Credit Card">Debit/Credit Card</option>
                   <option value="Cash">Cash</option>
                    <option value="Other">Other</option>
              </select>

       
         
            <p><input type="text" name="transactionno" class="form-control"   placeholder="Transaction Number (if any)"></p>
       
             <p><textarea name="message"  class="form-control" placeholder="Admin Remark" required></textarea></p>
					<p><input type="submit" class="btn btn-custom" name="update" value="Update"></p>
			</form>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
      
    	</div>
  </div>
                            <!-- Pop Ups End -->
                            <!-- Script for Step 1: Basic Info -->
                            <script>
                                const searchInput = document.getElementById('searchInput');
                                const searchResults = document.getElementById('searchResults');
                                const customerName = document.getElementById('customerName');
                                let timeoutId = null;

                                searchInput.addEventListener('input', function() {
                                    clearTimeout(timeoutId);
                                    const query = this.value.trim();
                                    
                                    // Clear customer name when search input changes
                                    customerName.value = '';
                                    
                                    if (query.length < 2) {
                                        searchResults.style.display = 'none';
                                        return;
                                    }

                                    timeoutId = setTimeout(() => {
                                        fetchResults(query);
                                    }, 300);
                                });

                                document.addEventListener('click', function(e) {
                                    if (!searchResults.contains(e.target) && e.target !== searchInput) {
                                        searchResults.style.display = 'none';
                                    }
                                });

                                function fetchResults(query) {
                                    fetch(`search.php?query=${encodeURIComponent(query)}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            displayResults(data);
                                        })
                                        .catch(error => console.error('Error:', error));
                                }

                                function displayResults(results) {
                                    searchResults.innerHTML = '';
                                    
                                    if (results.length === 0) {
                                        searchResults.style.display = 'none';
                                        return;
                                    }

                                    results.forEach(result => {
                                        const div = document.createElement('div');
                                        div.className = 'result-item';
                                        div.textContent = `${result.mobile} - ${result.name}`;
                                        
                                        div.addEventListener('click', () => {
                                            searchInput.value = result.mobile;
                                            customerName.value = result.name;
                                            searchResults.style.display = 'none';
                                        });
                                        
                                        searchResults.appendChild(div);
                                    });
                                    
                                    searchResults.style.display = 'block';
                                }
                            </script>    

                            <!-- Step 2: Services -->
                            <div class="form-step" id="services">

                            <div class="form-group">
                                <label for="services" class="col-sm-2 control-label">Common Services
                                
                                </label>
							    <div class="col-sm-4">
								    <label for="Interior Enrichment">1. Interior Enrichment</label> 
								</div>
								
                                <div class="col-sm-4">
                                    <input type="radio" name="packagetype" value="1" required> Small &nbsp; &nbsp;
                                    
                                    <input type="radio" name="packagetype" value="2" required> Medium &nbsp; &nbsp;
                                
                                    <input type="radio" name="packagetype" value="3" required> Large &nbsp; &nbsp;	 
                                </div>
							
						    </div>

                            <div class="form-group">
                                <label for="services" class="col-sm-2 control-label">					
                                </label>
                                
                                <div class="col-sm-4">
                                <label  for="Interior Enrichment">2. Teflon Coating (6 Layer)</label>
                                </div>
                                <div class="col-sm-4">
                                <input type="radio" name="packagetype" value="1" required> Small &nbsp; &nbsp;
                                
                                <input type="radio" name="packagetype" value="2" required> Medium &nbsp; &nbsp;
                            
                                <input type="radio" name="packagetype" value="3" required> Large &nbsp; &nbsp;	 
                                </div>
                            </div>                            

                            <!-- New Feature Trial -->
                            <div class="form-group">
                                <label for="services" class="col-sm-2 control-label">Select Services
                                <a href="add-service.php" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Service</a>
                                
                                </label>
                                <div class="col-sm-8">
                                    <?php
                                    $sql = "SELECT * FROM services";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    foreach ($results as $result) {
                                    ?>

                                        <span class="checkbox" style="display:inline">
                                            <label class="checkbox-wrapper">
                                                <input type="checkbox" class="checkbox-input" name="services[]" value="<?php echo htmlentities($result->ServiceID); ?>"/>
                                                <span class="checkbox-tile">                                                       
                                                    <span class="checkbox-label">  <?php echo htmlentities($result->ServiceName); ?> <br/> (₹ <?php echo htmlentities($result->Price); ?>)
                                                    </span>
                                                </span>
                                            </label>
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>

                            </div>

                          <!-- Step 3: Accessories -->
                        <div class="form-step" id="accessories">
                        <div class="form-group">
                        <div class="col-sm-8">

                        <a href="add-service.php" class="btn btn-primary ms-3">+ Add New Accessories</a>
                        </div>
                                                            
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Select Items</label>
                            <div class="col-sm-8">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Item Selection Form -->
                                        <div class="row mb-3">
                                            <div class="col-md-5">
                                                
                                                <select id="item-select" class="form-control">
                                                <div id="status-message" class="message" style="display: none;"></div>

                        <option value="">Select Item</option>
                        <?php foreach ($inventory_items as $item): ?>
                            <option value="<?php echo $item['ItemID']; ?>" 
                                    data-price="<?php echo $item['UnitPrice']; ?>"
                                    data-name="<?php echo $item['ItemName']; ?>"
                                    data-unit="<?php echo $item['UnitOfMeasurement']; ?>">
                                <?php echo $item['ItemName']; ?> 
                                (₹<?php echo number_format($item['UnitPrice'], 2); ?>/<?php echo $item['UnitOfMeasurement']; ?>) 
                                - Available: <?php echo $item['Quantity']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" id="item-quantity" class="form-control" min="1" placeholder="Quantity">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" id="add-item-btn" class="btn btn-primary">Add Item</button>
                                            </div>
                                        </div>

                                        <!-- Items Table -->
                                        <table class="table" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                    <td colspan="2" id="subtotal">₹0.00</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>GST (18%):</strong></td>
                                                    <td colspan="2" id="tax">₹0.00</td>
                                                </tr>
                                                <tr class="discount-row d-none">
                                                    <td colspan="3" class="text-end"><strong>Discount (5%):</strong></td>
                                                    <td colspan="2" id="discount">-₹0.00</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                                    <td colspan="2" id="grand-total"><strong>₹0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

<div id="status-message" class="message" style="display: none;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventoryData = <?php echo json_encode($inventory_items); ?>;
    const itemSelect = document.getElementById('item-select');
    const quantityInput = document.getElementById('item-quantity');
    const addButton = document.getElementById('add-item-btn');
    const itemsTable = document.getElementById('items-table').getElementsByTagName('tbody')[0];
    let items = [];

    function getInventoryItem(id) {
        return inventoryData.find(item => item.ItemID === id);
    }

    function getRemainingQuantity(itemId) {
        const inventoryItem = getInventoryItem(itemId);
        const existingItem = items.find(item => item.id === itemId);
        const usedQuantity = existingItem ? existingItem.quantity : 0;
        return inventoryItem.Quantity - usedQuantity;
    }

    function validateQuantity(itemId, requestedQty, isEdit = false) {
        const inventoryItem = getInventoryItem(itemId);
        if (!inventoryItem) return false;
        
        let availableQty = inventoryItem.Quantity;
        if (!isEdit) {
            // Subtract quantities of this item already in cart
            const existingItem = items.find(item => item.id === itemId);
            if (existingItem) {
                availableQty -= existingItem.quantity;
            }
        }
        
        if (requestedQty > availableQty) {
            alert(`Insufficient quantity. Only ${availableQty} ${inventoryItem.UnitOfMeasurement} available.`);
            return false;
        }
        return true;
    }

    function editItem(rowIndex, newQuantity) {
        const item = items[rowIndex];
        const currentQty = item.quantity;
        
        // For edit, we need to check against total inventory minus other items' quantities
        const otherItemsQty = items.reduce((sum, i) => 
            i.id === item.id && i !== item ? sum + i.quantity : sum, 0);
        const totalAvailable = getInventoryItem(item.id).Quantity - otherItemsQty;
        
        if (newQuantity > totalAvailable) {
            alert(`Insufficient quantity. Only ${totalAvailable} ${item.unit} available.`);
            return false;
        }
        
        item.quantity = newQuantity;
        const row = itemsTable.rows[rowIndex];
        row.cells[1].textContent = `${newQuantity} ${item.unit}`;
        row.cells[3].textContent = `₹${(item.price * newQuantity).toFixed(2)}`;
        
        updateTotals();
        return true;
    }

    // Rest of the code remains the same (updateTotals, deleteItem, event listeners)
    // ... [previous code for these functions]

    quantityInput.addEventListener('change', function() {
        const itemId = itemSelect.value;
        if (itemId) {
            const remainingQty = getRemainingQuantity(itemId);
            if (this.value > remainingQty) {
                this.value = remainingQty;
                alert(`Maximum available quantity is ${remainingQty}`);
            }
        }
    });

    itemSelect.addEventListener('change', function() {
        quantityInput.value = '';
        const itemId = this.value;
        if (itemId) {
            const remainingQty = getRemainingQuantity(itemId);
            quantityInput.max = remainingQty;
        }
    });
});
</script>

<style>
.message {
    margin: 10px 0;
    padding: 10px;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}
.success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
    padding: 10px;
}
.error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
    padding: 10px;
}
</style>


<!-- Step 4: Confirm -->
<div class="form-step" id="confirm">
    <div class="form-group">
        <label class="col-sm-2 control-label">Booking Summary</label>
        <div class="col-sm-8">
            <div class="card">
                <div class="card-body">
                    <div id="booking-summary">
                        <h4>Customer Details</h4>
                        <p class="customer-name"></p>
                        <p class="customer-mobile"></p>
                        
                        <h4>Vehicle Details</h4>
                        <p class="vehicle-type"></p>
                        <p class="license-plate"></p>
                        
                        <h4>Service Details</h4>
                        <p class="washing-point"></p>
                        <p class="package-type"></p>
                        <div class="selected-services"></div>
                        
                        <h4>Accessories</h4>
                        <div class="accessories-list"></div>
                        
                        <h4>Total Amount</h4>
                        <p class="total-amount"></p>
                    </div>
                    
                    <div class="mt-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="terms-check" required>
                            <label class="form-check-label" for="terms-check">
                                I confirm that all the above details are correct
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

// Add item to cart
document.getElementById('add-item-btn').addEventListener('click', function() {
    const select = document.getElementById('item-select');
    const quantity = document.getElementById('item-quantity');
    const option = select.selectedOptions[0];
    
    if (select.value && quantity.value) {
        const item = {
            id: select.value,
            name: option.dataset.name,
            quantity: parseInt(quantity.value),
            price: parseFloat(option.dataset.price),
            unit: option.dataset.unit
        };
        
        cart.push(item);
        updateItemsTable();
        
        // Reset inputs
        select.value = '';
        quantity.value = '';
    }
});

// Update items table
function updateItemsTable() {
    const tbody = document.querySelector('#items-table tbody');
    tbody.innerHTML = '';
    
    cart.forEach((item, index) => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.quantity} ${item.unit}</td>
            <td>₹${item.price.toFixed(2)}</td>
            <td>₹${(item.quantity * item.price).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
    });
    
    calculateTotals();
}

// Remove item from cart
function removeItem(index) {
    cart.splice(index, 1);
    updateItemsTable();
}

// Calculate totals
function calculateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.quantity * item.price), 0);
    const tax = subtotal * 0.18;
    const discount = subtotal > 1000 ? subtotal * 0.05 : 0;
    const total = subtotal + tax - discount;
    
    document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('discount').textContent = `-₹${discount.toFixed(2)}`;
    document.getElementById('grand-total').textContent = `₹${total.toFixed(2)}`;
    
    document.querySelector('.discount-row').classList.toggle('d-none', discount === 0);
    
    return { subtotal, tax, discount, total };
}

// Update booking summary
function updateBookingSummary() {
    const summary = document.getElementById('booking-summary');
    
    // Update customer details
    summary.querySelector('.customer-name').textContent = `Name: ${document.getElementsByName('fname')[0].value}`;
    summary.querySelector('.customer-mobile').textContent = `Mobile: ${document.getElementsByName('contactno')[0].value}`;
    
    // Update vehicle details
    const vehicleType = document.getElementsByName('vehicletype')[0];
    summary.querySelector('.vehicle-type').textContent = `Type: ${vehicleType.options[vehicleType.selectedIndex].text}`;
    summary.querySelector('.license-plate').textContent = `License Plate: ${document.getElementsByName('licenseplate')[0].value}`;
    
    // Update service details
    const washingPoint = document.getElementsByName('washingpoint')[0];
    summary.querySelector('.washing-point').textContent = `Location: ${washingPoint.options[washingPoint.selectedIndex].text}`;
    
    const packageType = document.getElementsByName('packagetype')[0];
    summary.querySelector('.package-type').textContent = `Package: ${packageType.options[packageType.selectedIndex].text}`;
    
    // Update accessories list
    const accessoriesList = summary.querySelector('.accessories-list');
    accessoriesList.innerHTML = cart.map(item => 
        `<p>${item.name} - ${item.quantity} ${item.unit} - ₹${(item.quantity * item.price).toFixed(2)}</p>`
    ).join('');
    
    // Update total amount
    const totals = calculateTotals();
    summary.querySelector('.total-amount').textContent = `Total: ₹${totals.total.toFixed(2)}`;
}

// Initialize steps
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.form-step');
    const indicators = document.querySelectorAll('.step-indicator li');
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, i) => s.classList.toggle('active', i === step));
        indicators.forEach((ind, i) => ind.classList.toggle('active', i === step));
        
        if (step === 3) { // Confirmation step
            updateBookingSummary();
        }
    }

    document.querySelector('.next-btn').addEventListener('click', function() {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    document.querySelector('.back-btn').addEventListener('click', function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });
});
</script>
                            <!-- Form Buttons -->
                            <div class="form-buttons">
                                <button type="button" class="back-btn">Back</button>
                                <button type="button" class="next-btn">Next Step</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentStep = 0;
        const steps = document.querySelectorAll('.form-step');
        const stepIndicator = document.querySelectorAll('.step-indicator li');
        const nextBtn = document.querySelector('.next-btn');
        const backBtn = document.querySelector('.back-btn');

        function showStep(step) {
            steps.forEach((stepElement, index) => {
                stepElement.classList.toggle('active', index === step);
            });
            stepIndicator.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === step);
            });
            if (step === steps.length - 1) {
                nextBtn.textContent = "Submit";
            } else {
                nextBtn.textContent = "Next Step";
            }
        }

        nextBtn.addEventListener('click', function () {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            } else {
                document.querySelector('form').submit();
            }
        });

        backBtn.addEventListener('click', function () {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });

        showStep(currentStep);
    </script>

    
<!-- script-for sticky-nav -->
		<script>
		$(document).ready(function() {
			 var navoffeset=$(".header-main").offset().top;
			 $(window).scroll(function(){
				var scrollpos=$(window).scrollTop(); 
				if(scrollpos >=navoffeset){
					$(".header-main").addClass("fixed");
				}else{
					$(".header-main").removeClass("fixed");
				}
			 });
			 
		});
		</script>
		<!-- /script-for sticky-nav -->
      <!--inner block start here-->
<div class="inner-block">

</div>
<!--inner block end here-->
<!--copy rights start here-->
<?php include('includes/footer.php');?>
<!--COPY rights end here-->
</div>
</div>
  <!--//content-inner-->
		<!--/sidebar-menu-->
						<?php include('includes/sidebarmenu.php');?>
							  <div class="clearfix"></div>		
							</div>
							<script>
							var toggle = true;
										
							$(".sidebar-icon").click(function() {                
							  if (toggle)
							  {
								$(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
								$("#menu span").css({"position":"absolute"});
							  }
							  else
							  {
								$(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
								setTimeout(function() {
								  $("#menu span").css({"position":"relative"});
								}, 400);
							  }
											
											toggle = !toggle;
										});
							</script>
<!--js -->
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
   <!-- /Bootstrap Core JavaScript -->	   

</body>
</html>
<?php } ?>
