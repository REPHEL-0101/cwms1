<!-- Test 1 File -->
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
	// Code for Booking
if(isset($_POST['book']))
{
$ptype=$_POST['packagetype'];
$wpoint=$_POST['washingpoint'];   
$fname=$_POST['fname'];
$mobile=$_POST['contactno'];
$date=$_POST['washdate'];
$time=$_POST['washtime'];
$message=$_POST['message'];
$status='New';
$bno=mt_rand(100000000, 999999999);
$sql="INSERT INTO tblcarwashbooking(bookingId,packageType,carWashPoint,fullName,mobileNumber,washDate,washTime,message,status) VALUES(:bno,:ptype,:wpoint,:fname,:mobile,:date,:time,:message,:status)";
$query = $dbh->prepare($sql);
$query->bindParam(':bno',$bno,PDO::PARAM_STR);
$query->bindParam(':ptype',$ptype,PDO::PARAM_STR);
$query->bindParam(':wpoint',$wpoint,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mobile',$mobile,PDO::PARAM_STR);
$query->bindParam(':date',$date,PDO::PARAM_STR);
$query->bindParam(':time',$time,PDO::PARAM_STR);
$query->bindParam(':message',$message,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
 
  echo '<script>alert("Your booking done successfully. Booking number is "+"'.$bno.'")</script>';
 echo "<script>window.location.href ='new-booking.php'</script>";
}
else 
{
 echo "<script>alert('Something went wrong. Please try again.');</script>";
}

}

	?>
<!DOCTYPE HTML>
<html>
<head>
<title>CWMS | Add Car Washing Booking</title>
<!-- Favicon -->
<link href="img/favicon.ico" rel="icon">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
// <link href="css/booking.css" rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="css/morris.css" type="text/css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="js/jquery-2.1.4.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />

  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>
		
// <script src="booking.js"></script>

</head> 
<body>
   <div class="page-container">
   <!--/content-inner-->
<div class="left-content">
	   <div class="mother-grid-inner">
              <!--header start here-->
<?php include('includes/header.php');?>
							
				     <div class="clearfix"> </div>	
				</div>
<!--heder end here-->
	<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a><i class="fa fa-angle-right"></i>Add Car Washing Booking </li>
				
            </ol>

			<?php include('includes/booking.php'); ?>

		<!--grid-->
 	<div class="grid-form">
 
<!---->
  <div class="grid-form1">
  	       <h3>Add Car Washing Booking</h3>

  	         <div class="tab-content">
						<div class="tab-pane active" id="horizontal-form">
							<form class="form-horizontal" name="washingpoint" method="post" enctype="multipart/form-data">

										
<!-- Tab 1 Start -->
							<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Customer Details</label>
									<div class="col-sm-3">
										<input type="text" id="searchBox" name="contactno" class="form-control" pattern="[0-9]{10}" title="10 numeric characters only" required placeholder="Mobile No.">
										<div id="searchResults"></div>  
									</div>
									<div class="col-sm-3">
										<input type="text" name="fname" class="form-control" required placeholder="Customer Name">
									</div>
									<div class="col-sm-3">
									<!-- New Customer Link -->
                          			<a href="#" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Customer</a>
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
                                <input type="text" name="licenseplate" class="form-control" required placeholder="License Plate">
                            </div>
							<div class="col-sm-3">
									<!-- New Vehicle Type Link -->
                          			<a href="#" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Vehicle Type</a>
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
										<option value="">Package Type</option>
										<option value="1">Small Package (₹ 4500.00)</option>
										<option value="2">Medium Package (₹ 5500.00)</option>
										<option value="3 ">Large Package (₹ 6500.00)</option>
									</select>
									</div>
									<div class="col-sm-3">
									<!-- New Package Link -->
                          			<a href="#" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Package</a>
       								</div>
								</div>

							 <!-- Separator -->
							 <hr style="border-bottom: 4px dotted #1BAFED; margin:50px auto" width="10%" align="center">
								
<!-- Tab 1 End -->
 <!-- Tab 2 Start -->

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
 
						<!-- Separator -->
 <hr style="border-bottom: 4px dotted #1BAFED; margin:50px auto" width="10%" align="center">

							 <div class="form-group">
							<label for="services" class="col-sm-2 control-label">Select Services
							<select name="packagetype" required class="form-control">
										<option value="1">Small</option>
										<option value="2">Medium</option>
										<option value="3 ">Large</option>
									</select>
							</label>
							<div class="col-sm-6">
								
								<?php 
								// Database query to fetch services
								$sql = "SELECT * FROM services";
								$query = $dbh->prepare($sql);
								$query->execute();
								$results = $query->fetchAll(PDO::FETCH_OBJ);
								
								// Display checkboxes for each service
								foreach($results as $result) {
								?>
									<div class="checkbox">
										<label>
											<input type="checkbox" 
												name="services[]" 
												value="<?php echo htmlentities($result->ServiceID); ?>">
											<?php echo htmlentities($result->ServiceName); ?> 
											(₹ <?php echo htmlentities($result->Price); ?>)
										</label>
									</div>
								<?php 
								} 
								?>
							</div>
							<div class="col-sm-3">
							<!-- New Service Link -->
							<a href="#" data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="text-primary">New Service</a>	
							</div>
						</div>

									
<!-- Tab 2 End -->
								

<!-- 
								<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Wash Date</label>
									<div class="col-sm-4">
									<input type="date" name="washdate" required class="form-control">
									</div>
								</div>

	

<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Wash Time</label>
									<div class="col-sm-4">
										<input type="time" name="washtime" required class="form-control">
									</div>
								</div> -->

							 <!-- Separator -->
							 <hr style="border-bottom: 4px dotted #1BAFED; margin:50px auto" width="10%" align="center">

								<div class="form-group">
									<label for="focusedinput" class="col-sm-2 control-label">Message (if any)</label>
									<div class="col-sm-6">
								<textarea name="message"  class="form-control" placeholder="Message if any"></textarea>
									</div>
								</div>
														
		

					
								<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<button type="submit" name="book" class="btn-primary btn">Add</button>

				<button type="reset" class="btn-inverse btn">Reset</button>
				<button data-toggle="modal" data-target="#myModal" class="btn-primary btn">Take Action</button>
			</div>
		</div>
						
						
						
					</div>
					
					</form>

     
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

      <!-- New Customer Modal Start -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for adding a new customer -->
                <form id="customerForm">
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
            </div>
        </div>
    </div>
    <!-- New Customer Modal End -->

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

            // $('#addCustomerBtn').click(function() {
            //     $('#addCustomerModal').show();
            // });

			$('#addCustomerBtn').click(function() {
    var modal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
    modal.show();
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
      <div class="panel-footer">
	
	 </div>
    
  </div>
 	</div>
 	<!--//grid-->

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