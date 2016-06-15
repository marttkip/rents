
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
						<td>'.$status.'</td>
						<td>'.$tenancy_status.'</td>
						<td><a  class="btn btn-sm btn-primary" ><i class="fa fa-folder"></i> Rental unit Detail</a></td>
						<td><a href="'.site_url().'tenants/'.$rental_unit_id.'" class="btn btn-sm btn-warning" ><i class="fa fa-folder"></i> Tenants Detail</a></td>
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
								<h2 class="panel-title"><?php echo $title;?> <div class="pull-right" ><a href="<?php echo site_url();?>real-estate-administration/add-rental-unit" style="margin-top:-5px" class="btn btn-sm btn-info "><i class="fa fa-plus"></i> Add rental unit</a></div></h2>
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
								?>
                            	<div class="row" style="margin-bottom:20px;">
                                    <!--<div class="col-lg-2 col-lg-offset-8">
                                        <a href="<?php echo site_url();?>human-resource/export-personnel" class="btn btn-sm btn-success pull-right">Export</a>
                                    </div>-->
                                    <div class="col-lg-12">
                                    </div>
                                </div>
								<div class="table-responsive">
                                	
									<?php echo $result;?>
							
                                </div>
							</div>
                            <div class="panel-footer">
                            	<?php if(isset($links)){echo $links;}?>
                            </div>
						</section>