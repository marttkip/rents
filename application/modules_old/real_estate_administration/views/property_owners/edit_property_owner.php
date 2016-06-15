<?php
//personnel data
$row = $query->row();

$property_owner_id = $row->property_owner_id;
$property_owner_id = $row->property_owner_id;
$property_owner_name = $row->property_owner_name;
$property_owner_email = $row->property_owner_email;
$property_owner_username = $row->property_owner_username;
$created = $row->created;
$property_owner_phone = $row->property_owner_phone;
$property_owner_status = $row->property_owner_status;


$validation_error = validation_errors();
				
if(!empty($validation_error))
{
	$property_owner_name = set_value('property_owner_name');
	$property_owner_email = set_value('property_owner_email');
	$property_owner_phone = set_value('property_owner_phone');
	$property_owner_status = set_value('property_owner_status');
	$property_owner_username = set_value('property_owner_username');

}
?>          
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    <div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
                <a href="<?php echo site_url();?>real-estate-administration/property-owners" class="btn btn-info pull-right">Back to property owners</a>
            </div>
        </div>
            
        <!-- Adding Errors -->
        <?php
			$success = $this->session->userdata('success_message');
			$error = $this->session->userdata('error_message');
			
			if(!empty($success))
			{
				echo '
					<div class="alert alert-success">'.$success.'</div>
				';
				
				$this->session->unset_userdata('success_message');
			}
			
			if(!empty($error))
			{
				echo '
					<div class="alert alert-danger">'.$error.'</div>
				';
				
				$this->session->unset_userdata('error_message');
			}

			$validation_errors = validation_errors();
			
			if(!empty($validation_errors))
			{
				echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
			}
        
			$validation_errors = validation_errors();
			
			if(!empty($validation_errors))
			{
				echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
			}
        ?>
        
        <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
			<div class="row">
				<div class="col-md-6">
			      
			        <div class="form-group">
			            <label class="col-lg-5 control-label">Property Owner Name: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="property_owner_name" placeholder="Names" value="<?php echo $property_owner_name;?>">
			            </div>
			        </div>
			        
			        <div class="form-group">
			            <label class="col-lg-5 control-label">Property Owner Email: </label>
			            
			            <div class="col-lg-7">
			            	<input type="email" class="form-control" name="property_owner_email" placeholder="Email" value="<?php echo $property_owner_email;?>">
			            </div>
			        </div>
			        
			       
			        
			        
				</div>
			    
			    <div class="col-md-6">
			         <div class="form-group">
			            <label class="col-lg-5 control-label">Property Owner Phone: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="property_owner_phone" placeholder="Phone number" value="<?php echo $property_owner_phone;?>">
			            </div>
			        </div>
			        
			        <div class="form-group">
			            <label class="col-lg-5 control-label">Property Owner Username: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="property_owner_username" placeholder="Username" value="<?php echo $property_owner_username;?>">
			            </div>
			        </div>
			        
			        
			    </div>
			</div>
			<div class="row" style="margin-top:10px;">
				<div class="col-md-12">
			        <div class="form-actions center-align">
			            <button class="submit btn btn-primary" type="submit">
			                Add personnel
			            </button>
			        </div>
			    </div>
			</div>
        <?php echo form_close();?>
    </div>
</section>