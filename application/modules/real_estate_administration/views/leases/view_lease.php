<?php
// $property_name = $property_row->property_name;
// $property_location = $property_row->property_location;
// //personnel data

// $validation_error = validation_errors();
				
// if(!empty($validation_error))
// {
// 	$property_name = set_value('property_name');
// 	$property_location = set_value('property_location');
// }


$all_leases = $this->leases_model->get_tenant_unit_leases($tenant_id,$rental_unit_id);

$result = '';
		
//if users exist display them
if ($all_leases->num_rows() > 0)
{
	$count = $page;
	
	$result .= 
	'
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<tr>
				<th>#</th>
				<th><a>Lease Number</a></th>
				<th><a>Lease Start Date</a></th>
				<th><a>Expiry Date</a></th>
				<th><a>Rent amount</a></th>
				<th><a>Deposit</a></th>
				<th><a>Lease Status</a></th>
				<th colspan="5">Actions</th>
			</tr>
		</thead>
		  <tbody>
		  
	';
	
	
	foreach ($all_leases->result() as $leases_row)
	{
		$lease_id = $leases_row->lease_id;
		$tenant_unit_id = $leases_row->tenant_unit_id;
		$lease_start_date = $leases_row->lease_start_date;
		$lease_duration = $leases_row->lease_duration;
		$rent_amount = $leases_row->rent_amount;
		$lease_number = $leases_row->lease_number;
		$arrears_bf = $leases_row->arrears_bf;
		$rent_calculation = $leases_row->rent_calculation;
		$deposit = $leases_row->deposit;
		$deposit_ext = $leases_row->deposit_ext;
		$lease_status = $leases_row->lease_status;
		$created = $leases_row->created;

		$lease_start_date = date('jS M Y',strtotime($lease_start_date));
		
		// $expiry_date  = date('jS M Y',strtotime($lease_start_date, mktime()) . " + 365 day");
		$expiry_date  = date('jS M Y', strtotime(''.$lease_start_date.'+1 years'));
		

		//create deactivated status display
		if($lease_status == 0)
		{
			$status = '<span class="label label-default"> Deactivated</span>';

			$button = '';
			$delete_button = '';
		}
		//create activated status display
		else if($lease_status == 1)
		{
			$status = '<span class="label label-success">Active</span>';
			$button = '<td><a class="btn btn-default" href="'.site_url().'deactivate-rental-unit/'.$lease_id.'" onclick="return confirm(\'Do you want to deactivate '.$lease_number.'?\');" title="Deactivate '.$lease_number.'"><i class="fa fa-thumbs-down"></i></a></td>';
			$delete_button = '<td><a href="'.site_url().'deactivate-rental-unit/'.$rental_unit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$lease_number.'?\');" title="Delete '.$lease_number.'"><i class="fa fa-trash"></i></a></td>';

		}
	
		
		$count++;
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$lease_number.'</td>
				<td>'.$lease_start_date.'</td>
				<td>'.$expiry_date.'</td>
				<td>KES '.number_format($rent_amount,2).'</td>
				<td>KES '.number_format($deposit,2).'</td>
				<td>'.$status.'</td>
				<td><a  class="btn btn-sm btn-primary" ><i class="fa fa-folder"></i> View Lease</a></td>
				'.$button.'
				'.$delete_button.'
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
	$result .= "There are no leases created";
}
?>  
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Active Lease
        	<a href="<?php echo site_url();?>real-estate-administration/properties" class="btn btn-info btn-sm pull-right" style="margin-top:-5px;">View all Leases</a>
			<a href="<?php echo site_url();?>real-estate-administration/properties" class="btn btn-info btn-sm pull-right" style="margin-top:-5px;">Create Leases</a>
	          
        </h2>
    </header>
    <div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
                </div>
        </div>
            
        <!-- Adding Errors -->
        <?php
			
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
        
        <?php echo form_open('create-new-lease/'.$tenant_id.'/'.$rental_unit_id, array("class" => "form-horizontal", "role" => "form"));?>
			<div class="row">
				<div class="col-md-4">
			        <div class="form-group">
			            <label class="col-lg-4 control-label"> Lease Start Date: </label>
			            <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="lease_start_date" placeholder="Lease Start date">
                            </div>
                        </div>
			        </div>
			        <div class="form-group">
			            <label class="col-lg-4 control-label"> Lease Duration: </label>
			            
			            <div class="col-lg-8">
			            	<input type="text" class="form-control" name="lease_duration" placeholder="12" value="">
			            </div>
			        </div>			        
				</div>
			    <div class="col-md-4">
			         <div class="form-group">
			            <label class="col-lg-4 control-label">Rent Amount: </label>
			            
			            <div class="col-lg-8">
			            	<input type="text" class="form-control" name="rent_amount" placeholder="12000" value="">
			            </div>
			        </div>
			         <div class="form-group">
			            <label class="col-lg-4 control-label">Arreas bf: </label>
			            
			            <div class="col-lg-8">
			            	<input type="text" class="form-control" name="arrears_bf" placeholder="" value="">
			            </div>
			        </div>		        
			    </div>
			    <div class="col-md-4">
			    	<div class="form-group">
			            <label class="col-lg-4 control-label">Deposit Amount: </label>
			            
			            <div class="col-lg-8">
			            	<input type="text" class="form-control" name="deposit_amount" placeholder="i.e 24000" value="">
			            </div>
			        </div>
			         <div class="form-group">
			            <label class="col-lg-4 control-label">Deposit ext: </label>
			            
			            <div class="col-lg-8">
			            	<input type="text" class="form-control" name="deposit_ext" placeholder="2" value="">
			            </div>
			        </div>		        
			    </div>
			</div>
			<div class="row" style="margin-top:10px;">
				<div class="col-md-12">
			        <div class="form-actions center-align">
			            <button class="submit btn btn-sm btn-primary" type="submit">
			                Create Lease
			            </button>
			        </div>
			    </div>
			</div>
			

			
        <?php echo form_close();?>
        <hr>
        <div class="row" style="margin-top:10px;">
			<div class="col-md-12">
				<?php echo $result;?>
			</div>
		</div>
    </div>
</section>