<?php
$outpatients = 2;
$inpatients = 1;
$total_services_revenue = 200;
$debit_notes = 39;
$credit_notes = 200;

$total_cash_collection = 200;
$total_inpatient_debt = 20;
$total_outpatient_debt = 200;
$total_patients = 28;
?>

<div class="row statistics">
    <div class="col-md-2 col-sm-12">
    	 <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
                <h2 class="panel-title">Visits</h2>
              </header>             
        
              <!-- Widget content -->
              <div class="panel-body">
                <h5>Leases breakdown</h5>
                <table class="table table-striped table-hover table-condensed">
                	<thead>
                    	<tr>
                        	<th>Type</th>
                            <th>Leases</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Active</th>
                            <td><?php echo $active_leases;?></td>
                        </tr>
                        <tr>
                            <th>Inactive</th>
                            <td><?php echo $inactive_leases;?></td>
                        </tr>
                    </tbody>
                </table>
               
                
                <div class="clearfix"></div>
          	</div>
		</section>
    </div>
    
    <div class="col-md-10 col-sm-12">
    	 <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title">Transaction breakdown</h2>
            </header>             
        
              <!-- Widget content -->
              <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
						<?php

                        ?>
                        <h5>Revenue Type</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <tr>
                                    <th>Collections</th>
                                    <td><?php echo number_format($total_payments, 2);?></td>
                                </tr>
                                <tr>
                                    <th>Expenses</th>
                                    <td><?php echo number_format($total_expenses, 2);?></td>
                                </tr>
                                <tr>
                                    <th>Total Revenue</th>
                                    <td><?php echo number_format(($total_payments - $total_expenses), 2);?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="clearfix"></div>
            		</div>
                    <!-- End Transaction Breakdown -->
                    
                    <?php 
					
					if($title != 'Debtors Report')
					{
					?>
                    <div class="col-md-3">
                        <h5>Methods Breakdown</h5>
                         <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <?php
                                $total_cash_breakdown = 0;
                                if($payment_methods->num_rows() > 0)
                                {
                                    foreach($payment_methods->result() as $res)
                                    {
                                        $method_name = $res->payment_method;
                                        $payment_method_id = $res->payment_method_id;
                                        $total = 0;
                                        
                                        if($normal_payments->num_rows() > 0)
                                        {
                                            foreach($normal_payments->result() as $res2)
                                            {
                                                $payment_method_id2 = $res2->payment_method_id;
                                            
                                                if($payment_method_id == $payment_method_id2)
                                                {
                                                    $total += $res2->amount_paid;
                                                }
                                            }
                                        }
                                        
                                        $total_cash_breakdown += $total;
                                    
                                        echo 
                                        '
                                        <tr>
                                            <th>'.$method_name.'</th>
                                            <td>'.number_format($total, 2).'</td>
                                        </tr>
                                        ';
                                    }
                                    
                                    echo 
                                    '
                                    <tr>
                                        <th>Total</th>
                                        <td>'.number_format($total_cash_breakdown, 2).'</td>
                                    </tr>
                                    ';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Cash Breakdown -->
                    
                    <?php 
					}
					if($title != 'Cash Report')
					{
					?>
                    <div class="col-md-3">
                        <h5>Debt Breakdown</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <tr>
                                    <th>Active Leases Debt</th>
                                    <td><?php echo number_format(($total_inpatient_debt), 2);?></td>
                                </tr>
                                <tr>
                                    <th>Inactive Leases Debt</th>
                                    <td><?php echo number_format(($total_outpatient_debt - $total_cash_collection), 2);?></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td><?php echo number_format((($total_outpatient_debt - $total_cash_collection) + $total_inpatient_debt), 2);?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="clearfix"></div>
            		</div>
                    <!-- End debt Breakdown -->
                    <?php
					}
					?>
                    <div class="col-md-3">
                        <h4>Total Revenue</h4>
                        <h3>Ksh <?php echo number_format(($total_payments - $total_expenses), 2);?></h3>
                    </div>
                </div>
          	</div>
		</section>
    </div>
</div>