<?php
//personnel data
$points_category_date_from = set_value('points_category_date_from');
$points_category_date_to = set_value('points_category_date_to');
$points = set_value('points');
?>          
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?>
        	<a href="<?php echo site_url();?>real-estate-administration/points-categories" class="btn btn-sm btn-info pull-right" style="margin-top:-5px;">Back to points categories</a>

        </h2>
    </header>
    <div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
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
				<div class="col-md-4">
			      
			        <div class="form-group">
			            <label class="col-lg-5 control-label">Points Date From: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="points_category_date_from" placeholder="Date from i.e. 1" value="<?php echo $points_category_date_from;?>">

			            </div>
			        </div>
			          
				</div>
			    
			    <div class="col-md-4">
			    	<div class="form-group">
			            <label class="col-lg-5 control-label">Points Date To: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="points_category_date_to" placeholder="Date to i.e 2" value="<?php echo $points_category_date_to;?>">
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4">
			     	<div class="form-group">
			            <label class="col-lg-5 control-label">Points: </label>
			            
			            <div class="col-lg-7">
			            	<input type="text" class="form-control" name="points" placeholder="No of points" value="<?php echo $points;?>">
			            </div>
			        </div>

			    </div>
			</div>
			<div class="row" style="margin-top:10px;">
				<div class="col-md-12">
			        <div class="form-actions center-align">
			            <button class="submit btn btn-sm btn-primary" type="submit">
			                Add points Category
			            </button>
			        </div>
			    </div>
			</div>
        <?php echo form_close();?>
    </div>
</section>