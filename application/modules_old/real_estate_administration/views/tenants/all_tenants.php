
<?php 
if($rental_unit_id == NULL OR $rental_unit_id == 0)
{
echo $this->load->view('search/tenants_search','', true);

}
else
{

}
 ?>

<?php	
		$result = '';
		$items = '';
		if($rental_unit_id == NULL OR $rental_unit_id == 0)
		{
			$items = '';

		}else {
			# code...
			$items = '<th><a >Lease Status</a></th>';
		}
		//if tenants exist display them
		if ($tenants->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th><a >Name</a></th>
						<th><a >National Id</a></th>
						<th><a >Phone Number</a></th>
						<th><a >Email Address</a></th>
						<th><a >Profile Status</a></th>
						'.$items.'
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
			';
			foreach ($tenants->result() as $row)
			{
				$tenant_id = $row->tenant_id;
				$tenant_name = $row->tenant_name;
				//create deactivated status display
				if($row->tenant_status == 0)
				{
					$status = '<span class="label label-important">Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'activate-tenant/'.$tenant_id.'" onclick="return confirm(\'Do you want to activate '.$tenant_name.'?\');" title="Activate '.$tenant_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($row->tenant_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'deactivate-tenant/'.$tenant_id.'" onclick="return confirm(\'Do you want to deactivate '.$tenant_name.'?\');" title="Deactivate '.$tenant_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
				if($rental_unit_id == NULL  OR $rental_unit_id == 0)
				{
					$tenancy_status = '';
					$lease_info = '';
				}
				else
				{
					// check the tenancy status
					$tenancy_query = $this->tenants_model->get_tenancy_details($tenant_id,$rental_unit_id);
					if($tenancy_query->num_rows() > 0)
					{
							foreach ($tenancy_query->result() as $key) {
								# code...
								$tenant_unit_status =$key->tenant_unit_status;
							}

							if($tenant_unit_status == 1)
							{
								$tenancy_status = '<td><span class="label label-success">Active</span></td>';
							}
							else
							{
								$tenancy_status = '<td><span class="label label-warning">Not active</span></td>';
							}

					}
					else
					{
						$tenancy_status = '<td><span class="label label-warning">Not active</span></td>';
					}
					$lease_info = '
						<td>
							<a  class="btn btn-sm btn-primary" id="open_lease_details'.$tenant_id.'" onclick="get_tenant_leases('.$tenant_id.')" ><i class="fa fa-folder"></i> Open Lease Info</a>
							<a  class="btn btn-sm btn-warning" id="close_lease_details'.$tenant_id.'" style="display:none;" onclick="close_tenant_leases('.$tenant_id.')"><i class="fa fa-folder"></i> Close Lease Info</a>
						</td>';
				}

				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$row->tenant_name.' </td>
						<td>'.$row->tenant_national_id.' </td>
						<td>'.$row->tenant_phone_number.' </td>
						<td>'.$row->tenant_email.' </td>
						<td>'.$status.'</td>
						'.$tenancy_status.'
						<td>
							<a  class="btn btn-sm btn-success" id="open_tenant_info'.$tenant_id.'" onclick="get_tenant_info('.$tenant_id.');" ><i class="fa fa-folder"></i> Tenant Details</a>
							<a  class="btn btn-sm btn-warning" id="close_tenant_info'.$tenant_id.'" style="display:none;" onclick="close_tenant_info('.$tenant_id.')"><i class="fa fa-folder-open"></i> Close Tenant Info</a>
						</td>
						'.$lease_info.'
						<td><a href="'.site_url().'edit-tenant/'.$tenant_id.'" class="btn btn-sm btn-success"title="Edit '.$tenant_name.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'delete-tenant/'.$tenant_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$tenant_name.'?\');" title="Delete '.$tenant_name.'"><i class="fa fa-trash"></i></a></td>
					</tr> 
				';
				$v_data['tenant_id'] = $tenant_id;
				$v_data['rental_unit_id'] = $rental_unit_id;
				$result .= '<tr id="lease_details'.$tenant_id.'" style="display:none;">
								<td colspan="12">
									'.$this->load->view("leases/view_lease", $v_data, TRUE).'
								</td>
							</tr>';
				$result .= '<tr id="tenant_info'.$tenant_id.'" style="display:none;">
								<td colspan="12">
									'.$this->load->view("tenants/view_detail", $v_data, TRUE).'
								</td>
							</tr>';
			}
				
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no tenants";
		}

		
			$link = '<a href="'.site_url().'rental-management/rental-units" class="btn btn-sm btn-default pull-right" style="margin-left:5px; margin-top:-5px" >Back to rental units</a>';

		
		
		
?>
						<section class="panel">
							<header class="panel-heading">
								<div class="panel-actions">
									
								</div>
						
								<h2 class="panel-title"><?php echo $title;?> <?php echo $link;?></h2>
							</header>
							<div class="panel-body">
                            	<div class="row" style="margin-bottom:20px;">
                                    <div class="col-lg-12">
                                    	<!-- <a href="<?php echo site_url();?>add-tenant" class="btn btn-success btn-sm pull-right">Add tenant</a> -->
                                    	<?php
                                    	if($rental_unit_id == 0)
                                    	{

                                    	}
                                    	else
                                    	{?>
                                    		<a  class="btn btn-sm btn-primary pull-right" id="assign_new_tenant" onclick="assign_new_tenant();" style="margin-left:5px">Allocate Tenant to <?php echo $title;?></a>
											<a  class="btn btn-sm btn-default pull-right" id="close_assign_new_tenant" style="display:none; margin-left:5px;" onclick="close_assign_new_tenant();">Close tenant allocation view</a>
										<?php
                                    	}
                                    	?>
                                    	

										<a  class="btn btn-sm btn-success pull-right" id="open_new_tenant" onclick="get_new_tenant();" style="margin-left:5px">Add Tenant</a>
										<a  class="btn btn-sm btn-warning pull-right" id="close_new_tenant" style="display:none; margin-left:5px;" onclick="close_new_tenant();">Close new tenant</a>
										
                                    </div>
                                </div>
                                <div style="display:none;" class="col-md-12" style="margin-bottom:20px;" id="new_tenant" >
                                	<section class="panel">
										<header class="panel-heading">
											<div class="panel-actions">
											</div>
											<h2 class="panel-title">Add a new tenant</h2>
										</header>
										<div class="panel-body">
											<div class="row" style="margin-bottom:20px;">
                                    			<div class="col-lg-12 col-sm-12 col-md-12">
                                    				<div class="row">
                                    				<?php 
                                    					if($rental_unit_id == NULL AND $rental_unit_id == 0)
                                    					{
                                    						echo form_open("add-tenant", array("class" => "form-horizontal", "role" => "form"));

                                    					}
                                    					else
                                    					{
                                    						echo form_open("add-tenant/".$rental_unit_id."", array("class" => "form-horizontal", "role" => "form"));
                                    					}
                                    				?>
	                                    				<div class="col-md-12">
	                                    					<div class="row">
		                                    					<div class="col-md-6">
			                                    					<div class="form-group">
															            <label class="col-lg-5 control-label">Tenant Name: </label>
															            
															            <div class="col-lg-7">
															            	<input type="text" class="form-control" name="tenant_name" placeholder="Name" value="">
															            </div>
															        </div>
															        <div class="form-group">
															            <label class="col-lg-5 control-label">National id: </label>
															            
															            <div class="col-lg-7">
															            	<input type="text" class="form-control" name="tenant_national_id" placeholder="National ID" value="">
															            </div>
															        </div>
															    </div>
															    <div class="col-md-6">
															    	<div class="form-group">
															            <label class="col-lg-5 control-label">Phone number: </label>
															            
															            <div class="col-lg-7">
															            	<input type="text" class="form-control" name="tenant_phone_number" placeholder="Phone" value="">
															            </div>
															        </div>
															        <div class="form-group">
															            <label class="col-lg-5 control-label">Email address: </label>
															            
															            <div class="col-lg-7">
															            	<input type="email" class="form-control" name="tenant_email" placeholder="Email address" value="">
															            </div>
															        </div>
															    </div>
															</div>
														    <div class="row" style="margin-top:10px;">
																<div class="col-md-12">
															        <div class="form-actions center-align">
															            <button class="submit btn btn-primary" type="submit">
															                Add tenant
															            </button>
															        </div>
															    </div>
															</div>
	                                    				</div>
	                                    				<?php echo form_close();?>
	                                    				<!-- end of form -->
	                                    			</div>

                                    				
                                    			</div>
                                    			
                                    		</div>
										</div>
									</section>
                                </div>
                                <div style="display:none;" class="col-md-12" style="margin-bottom:20px;" id="new_tenant_allocation" >
                                	<section class="panel">
										<header class="panel-heading">
											<div class="panel-actions">
											</div>
											<h2 class="panel-title">Allocate tenant to <?php echo $title;?></h2>
										</header>
										<div class="panel-body">
											<div class="row" style="margin-bottom:20px;">
												<?php echo form_open("add-tenant-unit/".$rental_unit_id."", array("class" => "form-horizontal", "role" => "form"));?>
                                    			<div class="col-lg-12 col-sm-12 col-md-12">
                                    				<div class="row">
	                                    				<div class="col-md-12">
	                                    					<div class="row">
		                                    					<div class="col-md-10">
			                                    					<div class="form-group center-align">
															            <label class="col-lg-5 control-label">Tenant Name: </label>
															            
															            <div class="col-lg-5">
															            	<select id='tenant_id' name='tenant_id' class='form-control custom-select '>
														                    <!-- <select class="form-control custom-select " id='procedure_id' name='procedure_id'> -->
														                      <option value=''>None - Please Select a tenant</option>
														                      <?php echo $tenants_list;?>
														                    </select>
															            </div>
															        </div>
															    </div>
															</div>
														    <div class="row" style="margin-top:10px;">
																<div class="col-md-12">
															        <div class="form-actions center-align">
															            <button class="submit btn btn-primary btn-sm" type="submit">
															                Allocate tenant to <?php echo $title;?>
															            </button>
															        </div>
															    </div>
															</div>
	                                    				</div>
	                                    			</div>
                                    				
                                    			</div>
                                    			<?php echo form_close();?>
	                                    				<!-- end of form -->
                                    			
                                    		</div>
										</div>
									</section>
                                </div>
                                <div class="col-md-12">
									<div class="table-responsive">
	                                	
										<?php 
										
										$success = $this->session->userdata('success_message');
			
										if(!empty($success))
										{
											echo '<div class="alert alert-success"> <strong>Success!</strong> '.$success.' </div>';
											$this->session->unset_userdata('success_message');
										}
										
										$error = $this->session->userdata('error_message');
										
										if(!empty($error))
										{
											echo '<div class="alert alert-danger"> <strong>Oh snap!</strong> '.$error.' </div>';
											$this->session->unset_userdata('error_message');
										}

										$search =  $this->session->userdata('all_tenants_search');
										if(!empty($search))
										{
											echo '<a href="'.site_url().'close_search_tenants" class="btn btn-sm btn-warning">Close Search</a>';
										}
				
										echo $result;
										
										?>
								
	                                </div>
	                                   <div class="panel-footer">
		                            	<?php if(isset($links)){echo $links;}?>
		                            </div>
	                             </div>

							</div>
						</section>
<script type="text/javascript">
	$(function() {
	    $("#tenant_id").customselect();
	});
	$(document).ready(function(){
		$(function() {
			$("#tenant_id").customselect();
		});
	});

	function get_new_tenant(){

		var myTarget2 = document.getElementById("new_tenant");
		var myTarget3 = document.getElementById("new_tenant_allocation");
		var button = document.getElementById("open_new_tenant");
		var button2 = document.getElementById("close_new_tenant");

		myTarget2.style.display = '';
		button.style.display = 'none';
		myTarget3.style.display = 'none';
		button2.style.display = '';
	}
	function close_new_tenant(){

		var myTarget2 = document.getElementById("new_tenant");
		var button = document.getElementById("open_new_tenant");
		var button2 = document.getElementById("close_new_tenant");
		var myTarget3 = document.getElementById("new_tenant_allocation");

		myTarget2.style.display = 'none';
		button.style.display = '';
		myTarget3.style.display = 'none';
		button2.style.display = 'none';
	}


	function assign_new_tenant(){

		var myTarget2 = document.getElementById("new_tenant_allocation");
		var myTarget3 = document.getElementById("new_tenant");
		var button = document.getElementById("assign_new_tenant");
		var button2 = document.getElementById("close_assign_new_tenant");

		myTarget2.style.display = '';
		button.style.display = 'none';
		myTarget3.style.display = 'none';
		button2.style.display = '';
	}
	function close_assign_new_tenant(){

		var myTarget2 = document.getElementById("new_tenant_allocation");
		var button = document.getElementById("assign_new_tenant");
		var myTarget3 = document.getElementById("new_tenant");
		var button2 = document.getElementById("close_assign_new_tenant");

		myTarget2.style.display = 'none';
		button.style.display = '';
		myTarget3.style.display = 'none';
		button2.style.display = 'none';
	}


	// lease details


	function get_tenant_leases(tenant_id){

		var myTarget2 = document.getElementById("lease_details"+tenant_id);
		var myTarget3 = document.getElementById("new_tenant_allocation");
		var myTarget4 = document.getElementById("new_tenant");
		var button = document.getElementById("open_lease_details"+tenant_id);
		var button2 = document.getElementById("close_lease_details"+tenant_id);

		myTarget2.style.display = '';
		button.style.display = 'none';
		myTarget3.style.display = 'none';
		myTarget4.style.display = 'none';
		button2.style.display = '';
	}
	function close_tenant_leases(tenant_id){

		var myTarget2 = document.getElementById("lease_details"+tenant_id);
		var button = document.getElementById("open_lease_details"+tenant_id);
		var button2 = document.getElementById("close_lease_details"+tenant_id);
		var myTarget3 = document.getElementById("new_tenant_allocation");
		var myTarget4 = document.getElementById("new_tenant");

		myTarget2.style.display = 'none';
		button.style.display = '';
		myTarget3.style.display = 'none';
		myTarget4.style.display = 'none';
		button2.style.display = 'none';
	}

	// tenant_info
	function get_tenant_info(tenant_id){

		var myTarget2 = document.getElementById("tenant_info"+tenant_id);
		var myTarget3 = document.getElementById("new_tenant_allocation");
		var myTarget4 = document.getElementById("new_tenant");
		var button = document.getElementById("open_tenant_info"+tenant_id);
		var button2 = document.getElementById("close_tenant_info"+tenant_id);

		myTarget2.style.display = '';
		button.style.display = 'none';
		myTarget3.style.display = 'none';
		myTarget4.style.display = 'none';
		button2.style.display = '';
	}
	function close_tenant_info(tenant_id){

		var myTarget2 = document.getElementById("tenant_info"+tenant_id);
		var button = document.getElementById("open_tenant_info"+tenant_id);
		var button2 = document.getElementById("close_tenant_info"+tenant_id);
		var myTarget3 = document.getElementById("new_tenant_allocation");
		var myTarget4 = document.getElementById("new_tenant");

		myTarget2.style.display = 'none';
		button.style.display = '';
		myTarget3.style.display = 'none';
		myTarget4.style.display = 'none';
		button2.style.display = 'none';
	}

  </script>