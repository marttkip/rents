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
						<th><a>Property Name</a></th>
						<th><a >Property Location</a></th>
						<th><a >Status</a></th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			
			foreach ($query->result() as $row)
			{
				$property_id = $row->property_id;
				$property_name = $row->property_name;
				$property_location = $row->property_location;
				$created = $row->created;
				$property_status = $row->property_status;
				
				//status
				if($property_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
				
				//create deactivated status display
				if($property_status == 0)
				{
					$status = '<span class="label label-default"> Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'activate-property/'.$property_id.'" onclick="return confirm(\'Do you want to activate '.$property_name.'?\');" title="Activate '.$property_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($property_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'deactivate-property/'.$property_id.'" onclick="return confirm(\'Do you want to deactivate '.$property_name.'?\');" title="Deactivate '.$property_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
			
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$property_name.'</td>
						<td>'.$property_location.'</td>
						<td>'.$status.'</td>
						<td><a  class="btn btn-sm btn-primary" ><i class="fa fa-folder"></i> Property Detail</a></td>
						<td><a href="'.site_url().'rental-units/'.$property_id.'" class="btn btn-sm btn-warning" ><i class="fa fa-folder"></i> Rental Units</a></td>
						<td><a href="'.site_url().'edit-property/'.$property_id.'" class="btn btn-sm btn-success" title="Edit '.$property_name.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'deactivate-property/'.$property_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$property_name.'?\');" title="Delete '.$property_name.'"><i class="fa fa-trash"></i></a></td>
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
			$result .= "There are no properties added";
		}
?>

						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title"><?php echo $title;?> <div class="pull-right" ><a href="<?php echo site_url();?>real-estate-administration/add-property" style="margin-top:-5px" class="btn btn-sm btn-info "><i class="fa fa-plus"></i> Add Property</a></div></h2>
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