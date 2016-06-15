<!-- search -->
<?php echo $this->load->view('search/transactions', '', TRUE);?>
<!-- end search -->
<?php echo $this->load->view('transaction_statistics', '', TRUE);?>
 
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?> <a href="<?php echo site_url();?>administration/reports/export_transactions" class="btn btn-sm btn-success pull-right" style='margin-top:-5px;'> Export Transactions</a></h2>
            </header>             

          <!-- Widget content -->
           <div class="panel-body">
	          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
	          <?php
					$result = '';
					$search =  $this->session->userdata('all_transactions_search');
					if(!empty($search))
					{
						echo '<a href="'.site_url().'administration/reports/close_search/'.$module.'" class="btn btn-sm btn-warning">Close Search</a>';
					}
					
					//if users exist display them
					if ($query->num_rows() > 0)
					{
						$count = $page;
						
						$result .= 
							'
								<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
								  <thead>
									<tr>
									  <th>#</th>
									  <th>Date</th>
									  <th>Property Name</th>
									  <th>Unit Name</th>
									  <th>Tenant</th>
									  <th>Payment Method</th>
									  <th>Amount Paid (Kes)</th>
									  <th>Receipt Number</th>	
									  <th>Paid By</th>									  
							';
							
						$result .= '
									  
									</tr>
								  </thead>
								  <tbody>
						';
						
						$personnel_query = $this->personnel_model->get_all_personnel();
						
						foreach ($query->result() as $row)
						{
							$total_invoiced = 0;
							$payment_date = date('jS M Y',strtotime($row->payment_date));
							
							$property_name = $row->property_name;
							$rental_unit_name = $row->rental_unit_name;
							$personnel_id = $row->personnel_id;
							$tenant_name = $row->tenant_name;
							$lease_number = $row->lease_number;
							$receipt_number = $row->receipt_number;
							$amount_paid = $row->amount_paid;
							$payment_method = $row->payment_method;
							$paid_by = $row->paid_by;

							$invoice_total = 0;//$this->accounts_model->total_invoice($visit_id);

							$balance = 0;//$this->accounts_model->balance($payments_value,$invoice_total);
							// end of the debit and credit notes


							
							$count++;
							

							// payment value ///
							
							
							$result .= 
								'
									<tr>
										<td>'.$count.'</td>
										<td>'.$payment_date.'</td>
										<td>'.$property_name.'</td>
										<td>'.$rental_unit_name.'</td>
										<td>'.$tenant_name.'</td>
										<td>'.$payment_method.'</td>
										<td>'.$amount_paid.'</td>
										<td>'.$receipt_number.'</td>
										<td>'.$paid_by.'</td>
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
						$result .= "There are no visits";
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