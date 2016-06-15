<?php
// var_dump($query);
foreach ($query->result() as $key) {
	# code...
	$property_id_one = $key->property_id;
	$rental_unit_id = $key->rental_unit_id;
	$rental_unit_name = $key->rental_unit_name;
}


$properties = $this->property_model->get_active_property();
$rs8 = $properties->result();
$property_list = '';
foreach ($rs8 as $property_rs) :
	$property_id = $property_rs->property_id;
	$property_name = $property_rs->property_name;
	$property_location = $property_rs->property_location;
	if($property_id == $property_id_one)
	{
		  $property_list .="<option value='".$property_id."' selected>".$property_name." Location: ".$property_location."</option>";
	}
	else
	{
		  $property_list .="<option value='".$property_id."'>".$property_name." Location: ".$property_location."</option>";
	}
  

endforeach;
?>
<section class="panel">
	<header class="panel-heading">
		<div class="panel-actions">
		</div>
		<h2 class="panel-title">Add a rental unit</h2>
	</header>
	<div class="panel-body">
		<div class="row" style="margin-bottom:20px;">
			<div class="col-lg-12 col-sm-12 col-md-12">
				<div class="row">
				<?php echo form_open("edit-rental-unit/".$rental_unit_id, array("class" => "form-horizontal", "role" => "form"));?>
    				<div class="col-md-12">
    					<div class="row">
        					<div class="col-md-5">
            					<div class="form-group">
						            <label class="col-lg-5 control-label">Unit Name: </label>
						            
						            <div class="col-lg-7">
						            	<input type="text" class="form-control" name="rental_unit_name" placeholder="Rental Unit Name" value="<?php echo $rental_unit_name?>">
						            </div>
						        </div>
						    </div>
						    <div class="col-md-5">
						    	<div class="form-group">
						            <label class="col-lg-5 control-label">Property Name: </label>
						            
						            <div class="col-lg-5">
						            	<select id='property_id' name='property_id' class='form-control custom-select '>
					                    <!-- <select class="form-control custom-select " id='procedure_id' name='procedure_id'> -->
					                      <option value=''>None - Please Select a property</option>
					                      <?php echo $property_list;?>
					                    </select>
						            </div>
						        </div>
						    </div>
						</div>
					    <div class="row" style="margin-top:10px;">
							<div class="col-md-12">
						        <div class="form-actions center-align">
						            <button class="submit btn btn-primary" type="submit">
						                Edit rental unit
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