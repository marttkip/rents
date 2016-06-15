<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
    	<h2 class="panel-title pull-right"></h2>
    	<h2 class="panel-title">Search</h2>
    </header>             

  <!-- Widget content -->
        <div class="panel-body">
	<?php
    echo form_open("search-rental-units", array("class" => "form-horizontal"));
    ?>
    <div class="row">
     <div class="col-md-6">
            
            <div class="form-group">
                <label class="col-lg-4 control-label">Unit Name: </label>
               							            
	            <div class="col-lg-8">
	            	<input type="text" class="form-control" name="unit_name" placeholder="Unit Name" value="">
	            </div>
            </div>
            
           
            
        </div>
        <div class="col-md-6">
            
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