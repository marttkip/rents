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
		$arrears_bf = $leases_row->arreas_bf;
		$rent_calculation = $leases_row->rent_calculation;
		$deposit = $leases_row->deposit;
		$deposit_ext = $leases_row->deposit_ext;
		$tenant_phone_number = $leases_row->tenant_phone_number;
		$tenant_national_id = $leases_row->tenant_national_id;
		$lease_status = $leases_row->lease_status;
		$created = $leases_row->created;

		$lease_start_date = date('jS M Y',strtotime($lease_start_date));
		
		// $expiry_date  = date('jS M Y',strtotime($lease_start_date, mktime()) . " + 365 day");
		$expiry_date  = date('jS M Y', strtotime(''.$lease_start_date.'+1 years'));
		
		$total_due = $current_balance;

		$total_paid = $amount_paid;


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
	}
?>

<section class="panel">
		<header class="panel-heading">						
			<h2 class="panel-title">Lease Info </h2>
		</header>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12"> 
					<div class="col-md-4"> 
						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title">Lease details </h2>
							</header>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed">
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
									  		<tr><td><span>Rent amount :</span></td><td><?php echo $rent_amount;?></td></tr>
									  		<tr><td><span>Lease Status :</span></td><td><?php echo $status;?></td></tr>

									  		
									  	</tbody>
									  </table>
								</div>
							</div>
						</section>
					</div>
					<div class="col-md-4"> 
						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title">Tenancy Info </h2>
							</header>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed">
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
									  		
									  	</tbody>
									  </table>
								</div>
							</div>
						</section>
					</div>
					<div class="col-md-4"> 
						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title">Payment Details</h2>
							</header>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed">
										<thead>
											<tr>
												<th>Title</th>
												<th>Detail</th>
											</tr>
										</thead>
									  	<tbody>
									  		<tr><td><span>Bal B/F:</span></td><td>KES. <?php echo number_format($balance_bf,2);?></td></tr>
									  		<tr><td><span>Paid amount :</span></td><td>KES <?php echo number_format($total_paid,2);?></td></tr>
									  		<tr><td><span>Total Due :</span></td><td>KES. <?php echo number_format(($total_due),2);?></td></tr>
									  	</tbody>
									  </table>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>
</section>