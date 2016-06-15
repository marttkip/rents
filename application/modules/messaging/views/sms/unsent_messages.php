<?php
		
		$result = '';
		$action_point = '';
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

				//$delivery_message = "Hello ".$messaging_tenant_name.", your outstanding ".$message_category_name." is KES. ".$messaging_arreas.".";
				$delivery_message = "Hello ".$messaging_tenant_name.", your outstanding water balance for unit ".$messaging_unit_name." is KES. ".$messaging_arreas.".";

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


				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$message_category_name.'</td>
						<td>'.$messaging_tenant_name.'</td>
						<td>'.$actual_message.'</td>
						<td>'.$messaging_tenant_phone_number.'</td>
						<td><a href="'.site_url().'microfinance/delete-individual/'.$messaging_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$messaging_tenant_name.'?\');" title="Delete '.$messaging_tenant_name.'"><i class="fa fa-trash"></i></a></td>
					</tr> 
				';

				$action_point = '<div class="pull-right"><a class="btn btn-success btn-sm" href="'.site_url().'messaging/send-messages" style="margin-top: -5px;" onclick="return confirm(\'Do you want to send the messages ?\');" title="Send Message">Send Unsent Messages</a></div>';
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
								<h2 class="panel-title"><?php echo $title;?> <?php echo $action_point;?></h2>

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

								<?php
					                if(isset($import_response))
					                {
					                    if(!empty($import_response))
					                    {
					                        echo $import_response;
					                    }
					                }
					                
					                if(isset($import_response_error))
					                {
					                    if(!empty($import_response_error))
					                    {
					                        echo '<div class="center-align alert alert-danger">'.$import_response_error.'</div>';
					                    }
					                }
					            ?>
                            	<div class="row" style="margin-bottom:20px;">
                                    <div class="col-lg-2 col-lg-offset-8">
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#import_rental_list"><i class="fa fa-plus"></i> Import Rental List</button>

                                    </div>
                                    <div class="col-lg-2">
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#import_sc_list"><i class="fa fa-plus"></i> Import S/C List</button>
                                    </div>
                                     <!-- Modal -->
						                <div class="modal fade" id="import_rental_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						                    <div class="modal-dialog" role="document">
						                        <div class="modal-content">
						                            <div class="modal-header">
						                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						                                <h4 class="modal-title" id="myModalLabel">Import Rental List </h4>
						                            </div>
						                            <div class="modal-body">
						                                <?php echo form_open_multipart("messaging/validate-import/1", array("class" => "form-horizontal","role" => "form"));?>
						                               
						                                 <div class="alert alert-info">
											            	Please ensure that you have set up the following in the hospital administration:
											                <ol>
											                    <li>Tenant Name</li>
											                    <li>Areaas</li>
											                    <li>Phone Number</li>
											                </ol>
											            </div>
											            <div class="row">
											                <div class="col-md-12">
											                    <ul>
											                        <li>Download the import template <a href="<?php echo site_url().'messaging/import-template';?>">here.</a></li>
											                        
											                        <li>Save your file as a <strong>csv</strong> file before importing</li>
											                        <li>After adding your patients to the import template please import them using the button below</li>
											                    </ul>
											                </div>
											            </div>
											            
											            <div class="row">
											                <div class="col-md-12">
											                    <?php
											                    /*$data = array(
											                          'class'       => 'custom-file-input btn-red btn-width',
											                          'name'        => 'import_csv',
											                          'onchange'    => 'this.form.submit();',
											                          'type'       	=> 'file'
											                        );
											                
											                    echo form_input($data);*/
											                    ?>
											                    <div class="fileUpload btn btn-primary">
											                        <span>Import Rental Arreas List</span>
											                        <input type="file" class="upload" onChange="this.form.submit();" name="import_csv" />
											                    </div>
											                </div>
											            </div>
						                                <?php echo form_close();?>
						                            </div>
						                            <div class="modal-footer">
						                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						                            </div>
						                        </div>
						                    </div>
						                </div>
						            <!-- MODAL END -->
						             <!-- Modal -->
						                <div class="modal fade" id="import_sc_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						                    <div class="modal-dialog" role="document">
						                        <div class="modal-content">
						                            <div class="modal-header">
						                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						                                <h4 class="modal-title" id="myModalLabel">Import S/C List </h4>
						                            </div>
						                            <div class="modal-body">
						                                <?php echo form_open_multipart("messaging/validate-import/2", array("class" => "form-horizontal","role" => "form"));?>
						                               
						                                 <div class="alert alert-info">
											            	Please ensure that you have set up the following in the hospital administration:
											                <ol>
											                    <li>Tenant Name</li>
											                    <li>Areaas</li>
											                    <li>Phone Number</li>
											                </ol>
											            </div>
											            <div class="row">
											                <div class="col-md-12">
											                    <ul>
											                        <li>Download the import template <a href="<?php echo site_url().'messaging/import-template';?>">here.</a></li>
											                        
											                        <li>Save your file as a <strong>csv</strong> file before importing</li>
											                        <li>After adding your patients to the import template please import them using the button below</li>
											                    </ul>
											                </div>
											            </div>
											            
											            <div class="row">
											                <div class="col-md-12">
											                    <?php
											                    /*$data = array(
											                          'class'       => 'custom-file-input btn-red btn-width',
											                          'name'        => 'import_csv',
											                          'onchange'    => 'this.form.submit();',
											                          'type'       	=> 'file'
											                        );
											                
											                    echo form_input($data);*/
											                    ?>
											                    <div class="fileUpload btn btn-primary">
											                        <span>Import Rental Arreas List</span>
											                        <input type="file" class="upload" onChange="this.form.submit();" name="import_csv" />
											                    </div>
											                </div>
											            </div>
						                                <?php echo form_close();?>
						                            </div>
						                            <div class="modal-footer">
						                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						                            </div>
						                        </div>
						                    </div>
						                </div>
						            <!-- MODAL END -->


                                </div>
                                
								<div class="table-responsive">
                                	
									<?php echo $result;?>
							
                                </div>
							</div>
                            <div class="panel-footer">
                            	<?php if(isset($links)){echo $links;}?>
                            </div>
						</section>