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
						<th>Message category</th>
						<th>Send to</th>
						<th>Message</th>
						<th>Phone</th>
						<th colspan="1">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			
			
			foreach ($query->result() as $row)
			{
				$messaging_id = $row->messaging_id;
				$messaging_tenant_name = $row->messaging_tenant_name;
				$messaging_unit_name = $row->messaging_unit_name;
				$messaging_arreas = $row->messaging_arreas;
				$date_created = $row->date_created;
				$messaging_tenant_phone_number = $row->messaging_tenant_phone_number;
				$message_category_id = $row->message_category_id;
				$message_category_name = $row->message_category_name;
				$branch_code = $row->branch_code;
				$sent_status = $row->sent_status;

				$delivery_message = "Hello ".$messaging_tenant_name.", your outstanding arreas is KES. ".$messaging_arreas.".";

				$configuration = $this->admin_model->get_configuration();
				$mandrill = '';
				$configuration_id = 0;
				
				if($configuration->num_rows() > 0)
				{
					$res = $configuration->row();
					$configuration_id = $res->configuration_id;
					$mandrill = $res->mandrill;
					$sms_key = $res->sms_key;
					$sms_user = $res->sms_user;
			        $sms_suffix = $res->sms_suffix;
			        $sms_from = $res->sms_from;
				}
			    else
			    {
			        $configuration_id = '';
			        $mandrill = '';
			        $sms_key = '';
			        $sms_user = '';
			        $sms_suffix = '';

			    }

			    $actual_message = $delivery_message.' '.$sms_suffix;

				// get the message 

				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$message_category_name.'</td>
						<td>'.$messaging_tenant_name.'</td>
						<td>'.$actual_message.'</td>
						<td>'.$messaging_tenant_phone_number.'</td>
						<td><a class="btn btn-sm btn-success"  title="Delete">Sent</a></td>
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
			$result .= "There are no messages";
		}
?>

						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title"><?php echo $title;?></h2>
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
                            	
								<div class="table-responsive">
                                	
									<?php echo $result;?>
							
                                </div>
							</div>
                            <div class="panel-footer">
                            	<?php if(isset($links)){echo $links;}?>
                            </div>
						</section>