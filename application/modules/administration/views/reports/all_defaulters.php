<!-- search -->
<?php echo $this->load->view('search/defaulters', '', TRUE);?>
<!-- end search -->
 
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?> <a href="<?php echo site_url();?>export-defaulters" class="btn btn-sm btn-success pull-right" style='margin-top:-5px;'> Export Defaulters List</a></h2>
            </header>             

          <!-- Widget content -->
           <div class="panel-body">
	          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
	         <?php

				$result = '';
				$search =  $this->session->userdata('all_defaulters_search');
				if(!empty($search))
				{
					echo '<a href="'.site_url().'administration/reports/close_defaulters_search/'.$module.'" class="btn btn-sm btn-warning">Close Search</a>';
				}
						
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
								<th>Property</th>
								<th>Flat No.</th>
								<th>Rent P.M</th>
								<th>Tenant Name</th>
								<th>Arreas B/F</th>
								<th>Receipt/Date</th>
								<th>Amount paid</th>
								<th>Arreas C/F</th>
								<th>Phone Number</th>
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
						// $arreas_bf = $leases_row->arreas_bf;
						$rent_calculation = $leases_row->rent_calculation;
						$deposit = $leases_row->deposit;
						$deposit_ext = $leases_row->deposit_ext;
						$lease_status = $leases_row->lease_status;
						$tenant_phone_number = $leases_row->tenant_phone_number;
						$arrears_bf = $leases_row->arrears_bf;
						$created = $leases_row->created;

						// $lease_start_date = date('jS M Y',strtotime($lease_start_date));
						
						// $expiry_date  = date('jS M Y',strtotime($lease_start_date, mktime()) . " + 365 day");
						// $expiry_date  = date('jS M Y', strtotime(''.$lease_start_date.'+1 years'));
						
						$current_balance = $this->reports_model->check_lease_has_balance($lease_id,$rent_amount,$arrears_bf,$lease_start_date,$tenant_unit_id);
						// echo $current_balance;
						if($current_balance > 0 || $current_balance < 0)
						{					
							$count++;

							if($current_balance > 0)
							{
								$last_bal = $this->accounts_model->get_months_last_amount($lease_id,$rent_amount,$arrears_bf,$lease_start_date);
								if($last_bal == 0)
								{
									$last_bal = $arrears_bf;
								}
								else
								{
									$last_bal = $last_bal;
								}
							}
							else
							{
								$last_bal = $arrears_bf;
							}



					     	
					     	$this_month = date('m');
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

							$result .= 
							'
								<tr>
									<td>'.$count.'</td>
									<td>'.$property_name.'</td>
									<td>'.$rental_unit_name.'</td>
									<td>'.number_format($rent_amount,0).'</td>
									<td>'.$tenant_name.'</td>
									<td>'.number_format($last_bal,0).'</td>
										'.$current_items.'
									<td>'.number_format($current_balance,0).'</td>
									<td>'.$tenant_phone_number.'</td>
									
								</tr> 
							';
						}
						
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

				echo $result;
				?>  
			
	        </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        
		</section>
    </div>
  </div>
 <script type="text/javascript">
	$(function() {
	    $("#property_id").customselect();
	    $("#branch_code").customselect();
	});
	$(document).ready(function(){
		$(function() {
			$("#property_id").customselect();
			$("#branch_code").customselect();
		});
	});
</script>