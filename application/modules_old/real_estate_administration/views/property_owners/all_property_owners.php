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
						<th><a href="'.site_url().'human-resource/personnel/property_owner_name/'.$order_method.'/'.$page.'">Name</a></th>
						<th><a href="'.site_url().'human-resource/personnel/property_owner_email/'.$order_method.'/'.$page.'">Email</a></th>
						<th><a href="'.site_url().'human-resource/personnel/property_owner_phone/'.$order_method.'/'.$page.'">Phone</a></th>
						<th><a href="'.site_url().'human-resource/personnel/property_owner_username/'.$order_method.'/'.$page.'">username</a></th>
						<th><a href="'.site_url().'human-resource/personnel/property_owner_status/'.$order_method.'/'.$page.'">Status</a></th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			//get all administrators
			$administrators = $this->users_model->get_active_users();
			if ($administrators->num_rows() > 0)
			{
				$admins = $administrators->result();
			}
			
			else
			{
				$admins = NULL;
			}
			
			foreach ($query->result() as $row)
			{
				$property_owner_id = $row->property_owner_id;
				$property_owner_id = $row->property_owner_id;
				$property_owner_name = $row->property_owner_name;
				$property_owner_email = $row->property_owner_email;
				$property_owner_username = $row->property_owner_username;
				$created = $row->created;
				$property_owner_phone = $row->property_owner_phone;
				$property_owner_status = $row->property_owner_status;
				
				//status
				if($property_owner_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
				
				//create deactivated status display
				if($property_owner_status == 0)
				{
					$status = '<span class="label label-default"> Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'real-estate-administration/property-owners/activate-property-owner/'.$property_owner_id.'" onclick="return confirm(\'Do you want to activate '.$property_owner_name.'?\');" title="Activate '.$property_owner_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($property_owner_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'real-estate-administration/property-owners/deactivate-property-owner/'.$property_owner_id.'" onclick="return confirm(\'Do you want to deactivate '.$property_owner_name.'?\');" title="Deactivate '.$property_owner_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
			
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$property_owner_name.'</td>
						<td>'.$property_owner_email.'</td>
						<td>'.$property_owner_phone.'</td>
						<td>'.$property_owner_username.'</td>
						<td>'.$status.'</td>
						<td><a  class="btn btn-sm btn-primary" ><i class="fa fa-folder"></i> View Properties</a></td>
						<td><a href="'.site_url().'real-estate-administration/property-owners/reset-property-owner-password/'.$property_owner_id.'" class="btn btn-sm btn-warning" onclick="return confirm(\'Reset password for '.$property_owner_name.'?\');">Reset Password</a></td>
						<td><a href="'.site_url().'real-estate-administration/property-owners/edit-property-owner/'.$property_owner_id.'" class="btn btn-sm btn-success" title="Edit '.$property_owner_name.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'real-estate-administration/property-owners/deactivate-property-owner/'.$property_owner_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$property_owner_name.'?\');" title="Delete '.$property_owner_name.'"><i class="fa fa-trash"></i></a></td>
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
			$result .= "There are no property owners added";
		}
?>

						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title"><?php echo $title;?> <div class="pull-right" ><a href="<?php echo site_url();?>real-estate-administration/property-owners/add-property-owner" style="margin-top:-5px" class="btn btn-sm btn-info "><i class="fa fa-plus"></i> Add Property Owner</a></div></h2>
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