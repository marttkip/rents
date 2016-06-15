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
						<th>Document Number</th>
						<th>Property Name</th>
						<th colspan="1">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			
			
			foreach ($query->result() as $row)
			{
				$record_id = $row->record_id;
				$property_id = $row->property_id;
				$property_name = $row->property_name;
				$document_number = $row->document_number;	
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$document_number.'</td>
						<td>'.$property_name.'</td>
						<td><a href="'.site_url().'print-water-readings/'.$document_number.'" target="_blank" class="btn btn-sm btn-danger" ><i class="fa fa-print"></i> Print Documents</a></td>
					</tr> 
				';

				// $action_point = '<div class="pull-right"><a class="btn btn-success btn-sm" href="'.site_url().'messaging/send-messages" style="margin-top: -5px;" onclick="return confirm(\'Do you want to send the messages ?\');" title="Send Message">Send Unsent Messages</a></div>';
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
                                <div class="row">
                                	<div class="col-md-12">
                                		 <?php echo form_open_multipart("import-water-readings", array("class" => "form-horizontal","role" => "form"));?>
			                               <div class="row">
			                               		<div class="col-md-6">
			                               			<ul>
								                        <li>Download the import template <a href="<?php echo site_url().'import-water-readings-template';?>">here.</a></li>
								                        
								                        <li>Save your file as a <strong>csv</strong> file before importing</li>
								                        <li>After adding your patients to the import template please import them using the button below</li>
								                    </ul>
			                               		</div>
								                <div class="col-md-6">
			                               			 <div class="form-group">

											            <label class="col-lg-5 control-label">Property Name: </label>
											            
											            <div class="col-lg-7">
											            	<select class="form-control" name="property_id">
											            		<option value="0"> Select a property </option>
											            		<?php
											            		if($properties->num_rows() > 0)
											            		{
											            			foreach ($properties->result() as $key) {
											            				# code...
											            				$property_id = $key->property_id;
											            				$property_name = $key->property_name;
											            				?>
											            				<option value="<?php echo $property_id;?>"><?php echo $property_name;?></option>
											            				<?php
											            			}
											            		}
											            		?>
											            		
											            	</select>
											            </div>
											        </div>
								                    <!-- <div class="fileUpload btn btn-primary"> -->
								                    <div class="form-group">
								                        <label class="col-lg-5 control-label">Import Rental Arreas List</label>
								                        <div class="col-lg-7">
								                        	<input type="file" class="upload" onChange="this.form.submit();" name="import_csv" />
								                        </div>
								                    </div>
								                </div>
								            </div>
			                              <?php echo form_close();?>
                                	</div>

                                </div>
                                <hr>
                                <div class="row">
                                	<div class="col-md-12">
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