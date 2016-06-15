<?php

$tenant_query = $this->tenants_model->get_tenant($tenant_id);

if($tenant_query->num_rows() > 0)
{
	foreach ($tenant_query->result() as $tenant_key) {
		# code...
		$tenant_id = $tenant_key->tenant_id;
		$tenant_name = $tenant_key->tenant_name;
		$tenant_phone_number = $tenant_key->tenant_phone_number;
		$tenant_national_id = $tenant_key->tenant_national_id;
		$tenant_number = $tenant_key->tenant_number;
		$tenant_status = $tenant_key->tenant_status;
		$tenant_email = $tenant_key->tenant_email;
		if($tenant_key->tenant_status == 0)
		{
			$status = '<span class="label label-important">Deactivated</span>';
			$button = '<a class="btn btn-info" href="'.site_url().'activate-tenant/'.$tenant_id.'" onclick="return confirm(\'Do you want to activate '.$tenant_name.'?\');" title="Activate '.$tenant_name.'"><i class="fa fa-thumbs-up"></i></a>';
		}
		//create activated status display
		else if($tenant_key->tenant_status == 1)
		{
			$status = '<span class="label label-success">Active</span>';
			$button = '<a class="btn btn-default" href="'.site_url().'deactivate-tenant/'.$tenant_id.'" onclick="return confirm(\'Do you want to deactivate '.$tenant_name.'?\');" title="Deactivate '.$tenant_name.'"><i class="fa fa-thumbs-down"></i></a>';
		}
	}
}
?>
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">
        	<?php echo $tenant_name;?> Details          
        </h2>
    </header>
    <div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-md-12">
	            <div class="row">
					<div class="col-md-12"> 
						<div class="col-md-6"> 
							<div class="table-responsive">
								<table class="table table-bordered table-striped table-condensed">
									<thead>
										<tr>
											<th>Title</th>
											<th>Detail</th>
										</tr>
									</thead>
								  	<tbody>
								  		<tr><td><span>Tenant Number :</span></td><td><?php echo $tenant_number;?></td></tr>
								  		<tr><td><span>Tenant Name :</span></td><td><?php echo $tenant_name;?></td></tr>
								  		<tr><td><span>Tenant Phone :</span></td><td><?php echo $tenant_phone_number;?></td></tr>
								  		<tr><td><span>Tenant National Id :</span></td><td><?php echo $tenant_national_id;?></td></tr>
								  		<tr><td><span>Tenant Email :</span></td><td><?php echo $tenant_email;?></td></tr>
								  		<tr><td><span>Account Status :</span></td><td><?php echo $status;?></td></tr>
								  	</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-6"> 
							<div class="table-responsive">
								<table class="table table-bordered table-striped table-condensed">
									<thead>
										<tr>
											<th>Title</th>
											<th>Detail</th>
										</tr>
									</thead>
								  	<tbody>
								  		<tr><td><span>Active Leases :</span></td><td>2 Leases <span class="label label-success">View</span></td></tr>
								  		<tr><td><span>Inactive Leases :</span></td><td>3 Leases <span class="label label-success">View</span></td></tr>
								  	</tbody>
								</table>
							</div>
						</div>
	                </div>
	        	</div>
	        </div>
	    </div>        
    </div>
</section>