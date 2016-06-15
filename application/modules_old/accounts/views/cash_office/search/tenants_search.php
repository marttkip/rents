<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
    	<h2 class="panel-title pull-right"></h2>
    	<h2 class="panel-title">Search</h2>
    </header>             

  <!-- Widget content -->
        <div class="panel-body">
	<?php
    echo form_open("search-accounts", array("class" => "form-horizontal"));
    ?>
    <div class="row">
     <div class="col-md-4">
            
            <div class="form-group">
                <label class="col-lg-4 control-label">Tenant Name: </label>
               							            
	            <div class="col-lg-8">
	            	<input type="text" class="form-control" name="tenant_name" placeholder="Tenant Name" value="">
	            </div>
            </div>
            
            <div class="form-group">
                <label class="col-lg-4 control-label">Phone Number: </label>
                
                <div class="col-lg-8">
                	<input type="text" class="form-control" name="tenant_phone_number" placeholder="Tenant Phone Number" value="">
                </div>
            </div>
            
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="col-lg-4 control-label">National Id: </label>
                
                <div class="col-lg-8">
                	<input type="text" class="form-control" name="tenant_national_id" placeholder="Tenant national Id" value="">
                </div>
            </div>
        </div>
        
       
        
        <div class="col-md-4">
            
            <div class="form-group">
                <label class="col-lg-1 control-label"></label>
                
                <div class="col-lg-8">
                   <select id='property_id' name='property_id' class='form-control custom-select '>
                      <option value=''>None - Please Select a property</option>
                      <?php echo $property_list;?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-lg-8 col-lg-offset-4">
                	<div class="center-align">
                   		<button type="submit" class="btn btn-info">Search</button>
    				</div>
                </div>
            </div>
        </div>
    </div>
    
    
    <?php
    echo form_close();
    ?>
  </div>
</section>