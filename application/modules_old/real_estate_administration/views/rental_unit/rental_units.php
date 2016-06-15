<?php echo $this->load->view('search/rental_unit_search','', true); ?>
<?php
		
		$result = '';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th><a>Rental Unit Name</a></th>
						<th><a>Property Name</a></th>
						<th><a>Status</a></th>
						<th><a>Tenancy Status</a></th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			
			foreach ($query->result() as $row)
			{
				$rental_unit_id = $row->rental_unit_id;
				$rental_unit_name = $row->rental_unit_name;
				$property_name = $row->property_name;
				$rental_unit_price = $row->rental_unit_price;
				$created = $row->created;
				$rental_unit_status = $row->rental_unit_status;
				
				//status
				if($rental_unit_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
				
				// get tenancy status 

				$tenancy_query = $this->rental_unit_model->get_tenancy_details($rental_unit_id);

				if($tenancy_query->num_rows() > 0)
				{

					$tenancy_status = '<span class="label label-success"> Occupied </span>';
				}
				else
				{
					$tenancy_status = '<span class="label label-warning"> Vacant </span>';
					
				}

				//create deactivated status display
				if($rental_unit_status == 0)
				{
					$status = '<span class="label label-default"> Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'activate-rental-unit/'.$rental_unit_id.'" onclick="return confirm(\'Do you want to activate '.$rental_unit_name.'?\');" title="Activate '.$rental_unit_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($rental_unit_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'deactivate-rental-unit/'.$rental_unit_id.'" onclick="return confirm(\'Do you want to deactivate '.$rental_unit_name.'?\');" title="Deactivate '.$rental_unit_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
			
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$rental_unit_name.'</td>
						<td>'.$property_name.'</td>
						<td>'.$status.'</td>
						<td>'.$tenancy_status.'</td>
						<td><a  class="btn btn-sm btn-primary" ><i class="fa fa-folder"></i> Rental unit Detail</a></td>
						<td><a href="'.site_url().'tenants/'.$rental_unit_id.'/2" class="btn btn-sm btn-warning" ><i class="fa fa-folder"></i> Tenants Detail</a></td>
						<td><a href="'.site_url().'edit-rental-unit/'.$rental_unit_id.'" class="btn btn-sm btn-success" title="Edit '.$rental_unit_name.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'deactivate-rental-unit/'.$rental_unit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$rental_unit_name.'?\');" title="Delete '.$rental_unit_name.'"><i class="fa fa-trash"></i></a></td>
					</tr> 
				';
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no rental units added";
		}
?>

						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title"><?php echo $title;?> 
								<div class="pull-right" >
									<a style="margin-top:-5px" id="open_new_rental_unit" class="btn btn-sm btn-info" onclick="get_new_rental_unit()"><i class="fa fa-plus"></i> Add rental unit</a>
									<a style="display:none; margin-top:-5px" id="close_new_rental_unit" class="btn btn-sm btn-warning" onclick="close_new_rental_unit()"><i class="fa fa-plus"></i> Close rental Unit</a>
								</div>
								</h2>
							</header>
							<div class="panel-body">
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
								$search =  $this->session->userdata('all_rental_unit_search');
								if(!empty($search))
								{
									echo '<a href="'.site_url().'close_search_rental_units" class="btn btn-sm btn-warning">Close Search</a>';
								}
								?>
                            	
                                   <div style="display:none;" class="col-md-12" style="margin-bottom:20px;" id="new_rental_unit" >
                                	<section class="panel">
										<header class="panel-heading">
											<div class="panel-actions">
											</div>
											<h2 class="panel-title">Add a rental unit</h2>
										</header>
										<div class="panel-body">
											<div class="row" style="margin-bottom:20px;">
                                    			<div class="col-lg-12 col-sm-12 col-md-12">
                                    				<div class="row">
                                    				<?php echo form_open("add-rental-unit", array("class" => "form-horizontal", "role" => "form"));?>
	                                    				<div class="col-md-12">
	                                    					<div class="row">
		                                    					<div class="col-md-5">
			                                    					<div class="form-group">
															            <label class="col-lg-5 control-label">Unit Name: </label>
															            
															            <div class="col-lg-7">
															            	<input type="text" class="form-control" name="rental_unit_name" placeholder="Rental Unit Name" value="">
															            </div>
															        </div>
															    </div>
															    <div class="col-md-5">
															    	<div class="form-group">
															            <label class="col-lg-5 control-label">Property Name: </label>
															            
															            <div class="col-lg-5">
															            	<select id='property_id' name='property_id' class='form-control custom-select '>
														                    <!-- <select class="form-control custom-select " id='procedure_id' name='procedure_id'> -->
														                      <option value=''>None - Please Select a property</option>
														                      <?php echo $property_list;?>
														                    </select>
															            </div>
															        </div>
															    </div>
															</div>
														    <div class="row" style="margin-top:10px;">
																<div class="col-md-12">
															        <div class="form-actions center-align">
															            <button class="submit btn btn-primary" type="submit">
															                Add rental unit
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
								
                                <div class="row" style="margin-bottom:20px;">
                                   
                                    <div class="col-lg-12">
                                    	<div class="table-responsive">
											<?php echo $result;?>
                                		</div>
                                    </div>
                                </div>
							</div>
                            <div class="panel-footer">
                            	<?php if(isset($links)){echo $links;}?>
                            </div>
						</section>
<script type="text/javascript">
	$(function() {
	    $("#property_id").customselect();
	});
	$(document).ready(function(){
		$(function() {
			$("#property_id").customselect();
		});
	});
	function get_new_rental_unit(){

		var myTarget2 = document.getElementById("new_rental_unit");
		var button = document.getElementById("open_new_rental_unit");
		var button2 = document.getElementById("close_new_rental_unit");

		myTarget2.style.display = '';
		button.style.display = 'none';
		button2.style.display = '';
	}
	function close_new_rental_unit(){

		var myTarget2 = document.getElementById("new_rental_unit");
		var button = document.getElementById("open_new_rental_unit");
		var button2 = document.getElementById("close_new_rental_unit");

		myTarget2.style.display = 'none';
		button.style.display = '';
		button2.style.display = 'none';
	}
</script>