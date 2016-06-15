<?php
$all_leases = $this->leases_model->get_lease_detail($lease_id);
	foreach ($all_leases->result() as $leases_row)
	{
		$lease_id = $leases_row->lease_id;
		$tenant_unit_id = $leases_row->tenant_unit_id;
		$property_name = $leases_row->property_name;
		$rental_unit_name = $leases_row->rental_unit_name;
		$tenant_name = $leases_row->tenant_name;
		$lease_start_date = $leases_row->lease_start_date;
		$lease_duration = $leases_row->lease_duration;
		$rent_amount = $leases_row->rent_amount;
		$lease_number = $leases_row->lease_number;
		$arreas_bf = $leases_row->arreas_bf;
		$rent_calculation = $leases_row->rent_calculation;
		$deposit = $leases_row->deposit;
		$deposit_ext = $leases_row->deposit_ext;
		$tenant_phone_number = $leases_row->tenant_phone_number;
		$tenant_national_id = $leases_row->tenant_national_id;
		$lease_status = $leases_row->lease_status;
		$tenant_status = $leases_row->tenant_status;
		$created = $leases_row->created;

		$lease_start_date = date('jS M Y',strtotime($lease_start_date));
		
		// $expiry_date  = date('jS M Y',strtotime($lease_start_date, mktime()) . " + 365 day");
		$expiry_date  = date('jS M Y', strtotime(''.$lease_start_date.'+1 years'));
		
		


		//create deactivated status display
		if($lease_status == 0)
		{
			$status = '<span class="label label-default"> Deactivated</span>';

			$button = '';
			$delete_button = '';
		}
		//create activated status display
		else if($lease_status == 1)
		{
			$status = '<span class="label label-success">Active</span>';
			$button = '<td><a class="btn btn-default" href="'.site_url().'deactivate-rental-unit/'.$lease_id.'" onclick="return confirm(\'Do you want to deactivate '.$lease_number.'?\');" title="Deactivate '.$lease_number.'"><i class="fa fa-thumbs-down"></i></a></td>';
			$delete_button = '<td><a href="'.site_url().'deactivate-rental-unit/'.$lease_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$lease_number.'?\');" title="Delete '.$lease_number.'"><i class="fa fa-trash"></i></a></td>';

		}

		//create deactivated status display
		if($tenant_status == 0)
		{
			$status_tenant = '<span class="label label-default">Deactivated</span>';
		}
		//create activated status display
		else if($tenant_status == 1)
		{
			$status_tenant = '<span class="label label-success">Active</span>';
		}
	}
?>
 <section class="panel">
	<header class="panel-heading">
		<h2 class="panel-title"><?php echo $title;?> <a href="<?php echo site_url();?>cash-office/accounts" class="btn btn-sm btn-success pull-right"  style="margin-top:-5px;">Back to leases</a> </h2>
	</header>
	
	<!-- Widget content -->
	
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
			<?php
				$error = $this->session->userdata('error_message');
				$success = $this->session->userdata('success_message');
				
				if(!empty($error))
				{
				  echo '<div class="alert alert-danger">'.$error.'</div>';
				  $this->session->unset_userdata('error_message');
				}
				
				if(!empty($success))
				{
				  echo '<div class="alert alert-success">'.$success.'</div>';
				  $this->session->unset_userdata('success_message');
				}
			 ?>
			</div>
		</div>
		
		
		
        <!--<div class="row">
        	<div class="col-sm-3 col-sm-offset-3">
            	<a href="<?php echo site_url().'doctor/print_prescription'.$tenant_unit_id;?>" class="btn btn-warning">Print prescription</a>
            </div>
            
        	<div class="col-sm-3">
            	<a href="<?php echo site_url().'doctor/print_lab_tests'.$tenant_unit_id;?>" class="btn btn-danger">Print lab tests</a>
            </div>
        </div>-->
        
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-4">
					<div class="row">
						<div class="col-md-12">
							<section class="panel panel-featured panel-featured-info">
								<header class="panel-heading">
									
									<h2 class="panel-title">Tenant's Details</h2>
								</header>
								<div class="panel-body">
                                	<div class="row">
                                    	<div class="col-md-12">
                                          </div>
                                    </div>
									<table class="table table-hover table-bordered col-md-12">
										<thead>
											<tr>
												<th>Title</th>
												<th>Detail</th>
											</tr>
										</thead>
									  	<tbody>
									  		<tr><td><span>Tenant Name :</span></td><td><?php echo $tenant_name;?></td></tr>
									  		<tr><td><span>Tenant Phone :</span></td><td><?php echo $tenant_phone_number;?></td></tr>
									  		<tr><td><span>Tenant National Id :</span></td><td><?php echo $tenant_national_id;?></td></tr>
									  		<tr><td><span>Property Name :</span></td><td><?php echo $property_name;?></td></tr>
									  		<tr><td><span>Unit Name :</span></td><td><?php echo $rental_unit_name;?></td></tr>
									  		<tr><td><span>Account Status :</span></td><td><?php echo $status_tenant;?></td></tr>
									  		
									  	</tbody>
									</table>
								</div>
							</section>
						</div>
					</div>
				</div>
				<!-- END OF THE SPAN 7 -->
				 <div class="col-md-3">
					<div class="row">
						<div class="col-md-12">
							<section class="panel panel-featured panel-featured-info">
								<header class="panel-heading">
									
									<h2 class="panel-title">Lease Details</h2>
								</header>
								<div class="panel-body">
                                	<div class="row">
                                    	<div class="col-md-12">
                                         </div>
                                    </div>
									<table class="table table-hover table-bordered col-md-12">
										<thead>
											<tr>
												<th>Title</th>
												<th>Detail</th>
											</tr>
										</thead>
									  	<tbody>
									  		<tr><td><span>Lease Number :</span></td><td><?php echo $lease_number;?></td></tr>
									  		<tr><td><span>Lease Start date :</span></td><td><?php echo $lease_start_date;?></td></tr>
									  		<tr><td><span>Lease Expiry date :</span></td><td><?php echo $expiry_date;?></td></tr>
									  		<tr><td><span>Rent amount :</span></td><td> Kes. <?php echo $rent_amount;?></td></tr>
									  		<tr><td><span>Arrears B/F :</span></td><td>Kes. <?php echo $arreas_bf;?></td></tr>
									  		<tr><td><span>Lease Status :</span></td><td><?php echo $status;?></td></tr>

									  		
									  	</tbody>
									</table>
								</div>
							</section>
						</div>
					</div>
				</div>
				<!-- START OF THE SPAN 5 -->
				<div class="col-md-5">
					<div class="row">
						<div class="col-md-12">
							<section class="panel panel-featured panel-featured-info">
								<header class="panel-heading">
									
									<h2 class="panel-title">Add payment</h2>
								</header>
                                
								<div class="panel-body">
									<?php echo form_open("accounts/make_payments/".$tenant_unit_id."/".$lease_id, array("class" => "form-horizontal"));?>
										<div class="form-group" id="payment_method">
											<label class="col-md-4 control-label">Payment Method: </label>
											  
											<div class="col-md-7">
												<select class="form-control" name="payment_method" onchange="check_payment_type(this.value)" required>
													<option value="0">Select a payment method</option>
                                                	<?php
													  $method_rs = $this->accounts_model->get_payment_methods();
														
														foreach($method_rs->result() as $res)
														{
														  $payment_method_id = $res->payment_method_id;
														  $payment_method = $res->payment_method;
														  
															echo '<option value="'.$payment_method_id.'">'.$payment_method.'</option>';
														  
														}
													  
												  ?>
												</select>
											  </div>
										</div>
										<div id="mpesa_div" class="form-group" style="display:none;" >
											<label class="col-md-4 control-label"> Mpesa TX Code: </label>

											<div class="col-md-7">
												<input type="text" class="form-control" name="mpesa_code" placeholder="">
											</div>
										</div>
									  
									  
										<div id="cheque_div" class="form-group" style="display:none;" >
											<label class="col-md-4 control-label"> Bank Name: </label>
										  
											<div class="col-md-7">
												<input type="text" class="form-control" name="bank_name" placeholder="Barclays">
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Receipt Number: </label>
										  
											<div class="col-md-7">
												<input type="text" class="form-control" name="receipt_number" placeholder="" autocomplete="off" required>
											</div>
										</div>
										

										<div class="form-group">
											<label class="col-md-4 control-label">Amount: </label>
										  
											<div class="col-md-7">
												<input type="text" class="form-control" name="amount_paid" placeholder="" autocomplete="off" required>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Paid in By: </label>
										  
											<div class="col-md-7">
												<input type="text" class="form-control" name="paid_by" placeholder="<?php echo $tenant_name;?>" autocomplete="off" required>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-4 control-label">Payment Date: </label>
										  
											<div class="col-md-7">
												 <div class="input-group">
					                                <span class="input-group-addon">
					                                    <i class="fa fa-calendar"></i>
					                                </span>
					                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Payment Date" required>
					                            </div>
											</div>
										</div>
										
										<div class="center-align">
											<button class="btn btn-info btn-sm" type="submit">Add Payment Information</button>
										</div>
										<?php echo form_close();?>
								</div>
							</section>
						</div>
					</div>

				</div>
				<!-- END OF THE SPAN 5 -->
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-info">
					<header class="panel-heading">
						
						<h2 class="panel-title">Payment Details <?php echo date('Y');?></h2>
					</header>
					<div class="panel-body">
                    	<div class="row">
                        	<div class="col-md-12">
                             </div>
                        </div>
						<table class="table table-hover table-bordered col-md-12">
							<thead>
								<tr>
									<th>#</th>
									<th>Payment Date</th>
									<th>Receipt Number</th>
									<th>Amount Paid</th>
									<th>Paid By</th>
									<th>Receipted Date</th>
									<th>Receipted By</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if($lease_payments->num_rows() > 0)
								{
									$y = 0;
									foreach ($lease_payments->result() as $key) {
										# code...
										$receipt_number = $key->receipt_number;
										$amount_paid = $key->amount_paid;
										$paid_by = $key->paid_by;
										$payment_date = $key->payment_date;
										$payment_created = $key->payment_created;
										$payment_created_by = $key->payment_created_by;

										$payment_date = date('jS M Y',strtotime($payment_date));
										$payment_created = date('jS M Y',strtotime($payment_created));
										$y++;
										?>
										<tr>
											<td><?php echo $y?></td>
											<td><?php echo $payment_date;?></td>
											<td><?php echo $receipt_number?></td>
											<td><?php echo number_format($amount_paid,2);?></td>
											<td><?php echo $paid_by;?></td>
											<td><?php echo $payment_created;?></td>
											<td><?php echo $payment_created_by;?></td>
										</tr>
										<?php

									}
								}
								?>
								
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
		<!-- <div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-info">
					<header class="panel-heading">
						
						<h2 class="panel-title">Payment Statement <?php echo date('Y');?></h2>
					</header>
					<div class="panel-body">
                    	<div class="row">
                        	<div class="col-md-12">
                             </div>
                        </div>
						<table class="table table-hover table-bordered col-md-12">
							<thead>
								<tr>
									<th>#</th>
									<th>Month</th>
									<th>Rental Amount</th>
									<th>Bal B/F</th>
									<th>Amount Paid</th>
									<th>Bal C/F</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$x=0;
								$month = date('m');
								for ($m=$month; $m>=1; $m--) {
							     	$month = date('F', mktime(0,0,0,$m, 1, date('Y')));
							     	$this_month = date('m');
							     	$this_year = date('Y');
							     	$total_paid = $this->accounts_model->get_months_other_amount($lease_id,$this_month,$this_year);
							     	$last_bal = $this->accounts_model->get_months_last_amount($lease_id,$rent_amount,$arreas_bf,$lease_start_date);
							     	if($last_bal == 0)
									{
										$last_bal = $arreas_bf;
									}
									else
									{
										$last_bal = $last_bal;
									}
							     	// $rent_amount = 1200;
							     	$x++;
							     	?>
							     	<tr>
							     		<td><?php echo $x;?></td>
							     		<td><?php echo $month;?></td>
							     		<td><?php echo number_format($rent_amount,2);?></td>
							     		<td><?php echo number_format($last_bal,2)?></td>
							     		<td><?php echo number_format($total_paid,2);?></td>
							     		<td><?php echo number_format((($last_bal+$rent_amount) - $total_paid),2)?></td>
							     		<td></td>
							     	</tr>
							     	<?php
							     }
								?>
								
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div> -->
	
		<!-- END OF PADD -->
	</div>
</section>
  <!-- END OF ROW -->
<script type="text/javascript">
  function getservices(id){

        var myTarget1 = document.getElementById("service_div");
        var myTarget2 = document.getElementById("username_div");
        var myTarget3 = document.getElementById("password_div");
        var myTarget4 = document.getElementById("service_div2");
        var myTarget5 = document.getElementById("payment_method");
		
        if(id == 1)
        {
          myTarget1.style.display = 'none';
          myTarget2.style.display = 'none';
          myTarget3.style.display = 'none';
          myTarget4.style.display = 'block';
          myTarget5.style.display = 'block';
        }
        else
        {
          myTarget1.style.display = 'block';
          myTarget2.style.display = 'block';
          myTarget3.style.display = 'block';
          myTarget4.style.display = 'none';
          myTarget5.style.display = 'none';
        }
        
  }
  function check_payment_type(payment_type_id){
   
    var myTarget1 = document.getElementById("cheque_div");

    var myTarget2 = document.getElementById("mpesa_div");

    var myTarget3 = document.getElementById("insuarance_div");

    if(payment_type_id == 1)
    {
      // this is a check
     
      myTarget1.style.display = 'block';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
    }
    else if(payment_type_id == 2)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
    }
    else if(payment_type_id == 3)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'block';
    }
    else if(payment_type_id == 4)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
    }
    else if(payment_type_id == 5)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'block';
      myTarget3.style.display = 'none';
    }
    else
    {
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'block';  
    }

  }
</script>