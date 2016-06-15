<?php

$result = '';
		
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
				<th><a>Lease Number</a></th>
				<th><a>Property Name</a></th>
				<th><a>Unit Name</a></th>
				<th><a>Tenant Name</a></th>
				<th><a>Lease Status</a></th>
				<th colspan="5">Actions</th>
			</tr>
		</thead>
		  <tbody>
		  
	';
	
	
	foreach ($query->result() as $leases_row)
	{
		$lease_id = $leases_row->lease_id;
		$tenant_unit_id = $leases_row->tenant_unit_id;
		$property_name = $leases_row->property_name;
		$rental_unit_name = $leases_row->rental_unit_name;
		$tenant_name = $leases_row->tenant_name;
		$lease_start_date = $leases_row->lease_start_date;
		$lease_duration = $leases_row->lease_duration;
		$rent_amount = $leases_row->rent_amount;
		$lease_number = $leases_row->lease_number;
		$arreas_bf = $leases_row->arreas_bf;
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
			$delete_button = '<td><a href="'.site_url().'deactivate-rental-unit/'.$lease_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$lease_number.'?\');" title="Delete '.$lease_number.'"><i class="fa fa-trash"></i></a></td>';

		}
	
		
		$count++;
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$lease_number.'</td>
				<td>'.$property_name.'</td>
				<td>'.$rental_unit_name.'</td>
				<td>'.$tenant_name.'</td>
				<td>'.$status.'</td>
				<td><a  class="btn btn-sm btn-primary" id="open_lease'.$lease_id.'" onclick="get_lease_details('.$lease_id.')" ><i class="fa fa-folder"></i> View Lease Info</a>
					<a  class="btn btn-sm btn-warning" id="close_lease'.$lease_id.'" style="display:none;" onclick="close_lease_details('.$lease_id.')" ><i class="fa fa-folder"></i> Close Lease Info</a></td>
				'.$button.'
				'.$delete_button.'
			</tr> 
		';
		$v_data['lease_id'] = $lease_id;
		$result .= '<tr id="lease_details'.$lease_id.'" style="display:none;">
						<td colspan="12">
							'.$this->load->view("leases/lease_details", $v_data, TRUE).'
						</td>
					</tr>';
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
			<h2 class="panel-title"><?php echo $title;?> <div class="pull-right" ><a href="<?php echo site_url();?>real-estate-administration/add-rental-unit" style="margin-top:-5px" class="btn btn-sm btn-info "><i class="fa fa-plus"></i> Add rental unit</a></div></h2>
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
        	<div class="row" style="margin-bottom:20px;">
                <!--<div class="col-lg-2 col-lg-offset-8">
                    <a href="<?php echo site_url();?>human-resource/export-personnel" class="btn btn-sm btn-success pull-right">Export</a>
                </div>-->
                <div class="col-lg-12">
                </div>
            </div>
			<div class="table-responsive">
            	
				<?php echo $result;?>
		
            </div>
		</div>
        <div class="panel-footer">
        	<?php if(isset($links)){echo $links;}?>
        </div>
	</section>

	<script type="text/javascript">
		function get_lease_details(lease_id){

			var myTarget2 = document.getElementById("lease_details"+lease_id);
			var button = document.getElementById("open_lease"+lease_id);
			var button2 = document.getElementById("close_lease"+lease_id);

			myTarget2.style.display = '';
			button.style.display = 'none';
			button2.style.display = '';
		}
		function close_lease_details(lease_id){

			var myTarget2 = document.getElementById("lease_details"+lease_id);
			var button = document.getElementById("open_lease"+lease_id);
			var button2 = document.getElementById("close_lease"+lease_id);

			myTarget2.style.display = 'none';
			button.style.display = '';
			button2.style.display = 'none';
		}

  </script>