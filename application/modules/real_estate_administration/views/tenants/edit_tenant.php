<?php
foreach ($query->result() as $key) {
	$tenant_id = $key->tenant_id;
	$tenant_national_id = $key->tenant_national_id;	
	$tenant_name = $key->tenant_name;
	$tenant_phone_number = $key->tenant_phone_number;
	$tenant_email = $key->tenant_email;
}
?>
<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
			</div>
			<h2 class="panel-title">Edit <?php echo $tenant_name;?></h2>
		</header>
		<div class="panel-body">
			<div class="row" style="margin-bottom:20px;">
    			<div class="col-lg-12 col-sm-12 col-md-12">
    				<div class="row">
    				<?php echo form_open("edit-tenant/".$tenant_id, array("class" => "form-horizontal", "role" => "form"));?>
        				<div class="col-md-12">
        					<div class="row">
            					<div class="col-md-6">
                					<div class="form-group">
							            <label class="col-lg-5 control-label">Tenant Name: </label>
							            
							            <div class="col-lg-7">
							            	<input type="text" class="form-control" name="tenant_name" placeholder="Name" value="<?php echo $tenant_name;?>">
							            </div>
							        </div>
							        <div class="form-group">
							            <label class="col-lg-5 control-label">National id: </label>
							            
							            <div class="col-lg-7">
							            	<input type="text" class="form-control" name="tenant_national_id" placeholder="National ID" value="<?php echo $tenant_national_id;?>">
							            </div>
							        </div>
							    </div>
							    <div class="col-md-6">
							    	<div class="form-group">
							            <label class="col-lg-5 control-label">Phone number: </label>
							            
							            <div class="col-lg-7">
							            	<input type="text" class="form-control" name="tenant_phone_number" placeholder="Phone" value="<?php echo $tenant_phone_number;?>">
							            </div>
							        </div>
							        <div class="form-group">
							            <label class="col-lg-5 control-label">Email address: </label>
							            
							            <div class="col-lg-7">
							            	<input type="email" class="form-control" name="tenant_email" placeholder="Email address" value="<?php echo $tenant_email;?>">
							            </div>
							        </div>
							    </div>
							</div>
						    <div class="row" style="margin-top:10px;">
								<div class="col-md-12">
							        <div class="form-actions center-align">
							            <button class="submit btn btn-primary" type="submit">
							                Edit tenant
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