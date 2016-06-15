<?php echo $this->load->view('search/tenants_search','', true); ?>
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
				<th><a>Tenant Name</a></th>
				<th><a>Unit Name</a></th>
				<th><a>Points</a></th>
				<th><a>Lease Status</a></th>
				<th><a>Bal BF</a></th>
				<th><a>Last Receipt</a></th>
				<th><a>Amount Paid</a></th>
				<th><a>Curr Bal</a></th>
				<th colspan="5">Actions</th>
			</tr>
		</thead>
		  <tbody>
		  
	';
	
	
	foreach ($query->result() as $leases_row)
	{
		$lease_id = $leases_row->lease_id;
		$tenant_unit_id = $leases_row->tenant_unit_id;
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
		$points = $leases_row->points;
		$created = $leases_row->created;

	
		
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
		$current_balance = $this->reports_model->check_lease_has_balance($lease_id,$rent_amount,$arreas_bf,$lease_start_date,$tenant_unit_id);
		// echo $current_balance;
		

		// var_dump($current_balance);die();
			if($current_balance > 0)
			{
				$last_bal = $this->accounts_model->get_months_last_amount($lease_id,$rent_amount,$arreas_bf,$lease_start_date);
				if($last_bal == 0)
				{
					$last_bal = $arreas_bf;
				}
				else
				{
					$last_bal = $last_bal;
				}
			}
			else
			{
				$last_bal = $arreas_bf;
			}


			$lease_start_date = date('jS M Y',strtotime($lease_start_date));
	     	
	     	$this_month = date('m');
	     	$amount_paid = 0;
		     	$payments = $this->accounts_model->get_this_months_payment($lease_id,$this_month);
		     	$current_items = '<td> -</td>
		     			  <td>-</td>'; 
		     	if($payments->num_rows() > 0)
		     	{
		     		$counter = 0;
		     		$total_paid_amount = 0;
		     		$receipt_counter = '';
		     		foreach ($payments->result() as $value) {
		     			# code...
		     			$receipt_number = $value->receipt_number;
		     			$amount_paid = $value->amount_paid;

		     			if($counter > 0)
		     			{
		     				$addition = '#';
		     				// $receipt_counter .= $receipt_number.$addition;
		     			}
		     			else
		     			{
		     				$addition = ' ';
		     				
		     			}
		     			$receipt_counter .= $receipt_number.$addition;
		     			$counter++;
		     		}
		     		$current_items = '<td>'.$receipt_number.'</td>
		     					<td>'.number_format($amount_paid,0).'</td>';
		     	}
		     	else
		     	{
		     		$current_items = '<td> -</td>
		     					<td>-</td>';
		     	}
		
		$count++;
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$lease_number.'</td>
				<td>'.$tenant_name.'</td>
				<td>'.$rental_unit_name.'</td>
				<td>'.$points.'</td>
				<td>'.$status.'</td>
				<td>'.number_format($last_bal,0).'</td>
				'.$current_items.'
				<td>'.number_format($current_balance,0).'</td>
				<td><a class="btn btn-sm btn-success" href="payments/'.$tenant_unit_id.'/'.$lease_id.'" > Payments</a></td>				
				<td><a  class="btn btn-sm btn-primary" id="open_lease'.$lease_id.'" onclick="get_lease_details('.$lease_id.')" ><i class="fa fa-folder"></i> View Lease Info</a>
					<a  class="btn btn-sm btn-warning" id="close_lease'.$lease_id.'" style="display:none;" onclick="close_lease_details('.$lease_id.')" ><i class="fa fa-folder"></i> Close Lease Info</a></td>
				<td><a class="btn btn-sm btn-default" href="" > Send Message</a></td>	
			</tr> 
		';
		$v_data['lease_id'] = $lease_id;
		$v_data['amount_paid'] = $amount_paid;
		$v_data['current_balance'] = $current_balance;
		$v_data['balance_bf'] = $last_bal;
		$result .= '<tr id="lease_details'.$lease_id.'" style="display:none;">
						<td colspan="12">
							'.$this->load->view("lease_details", $v_data, TRUE).'
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
			<h2 class="panel-title"><?php echo $title;?></h2>
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
			$search =  $this->session->userdata('all_accounts_search');
			if(!empty($search))
			{
				echo '<a href="'.site_url().'close_search_accounts" class="btn btn-sm btn-warning">Close Search</a>';
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