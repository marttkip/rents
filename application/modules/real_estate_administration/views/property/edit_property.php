<?php
var_dump($property_row);die();

$property_name = $property_row->property_name;
$property_location = $property_row->property_location;
//personnel data

$validation_error = validation_errors();
				
if(!empty($validation_error))
{
	$property_name = set_value('property_name');
	$property_location = set_value('property_location');
}


?>          
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    <div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
                <a href="<?php echo site_url();?>real-estate-administration/properties" class="btn btn-info pull-right">Back to properties</a>
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
			            <label class="col-lg-5 control-label">Property Name: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="property_name" placeholder="Names" value="<?php echo $property_name;?>">
			            </div>
			        </div>
			        
			       
			        
			        
				</div>
			    
			    <div class="col-md-6">
			         <div class="form-group">
			            <label class="col-lg-5 control-label">Property Location: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="property_location" placeholder="Property Location" value="<?php echo $property_location;?>">
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