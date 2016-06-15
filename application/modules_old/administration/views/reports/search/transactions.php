        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title pull-right">Active branch: <?php echo $branch_name;?></h2>
            	<h2 class="panel-title">Search</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
			<?php
            echo form_open("search-transactions/".$module, array("class" => "form-horizontal"));
            ?>
            <div class="row">
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

                </div>
                
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Payment Date From: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date_from" placeholder="Report Date From">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Payment Date To: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date_to" placeholder="Report Date To">
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-4">
                    
                    <!--<div class="form-group">
                        <label class="col-lg-4 control-label">Patient number: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient number">
                        </div>
                    </div>-->
                    
                    <div class="form-group">
                        <label class="col-lg-1 control-label"> </label>
                        
                        <div class="col-lg-6">
                            <select id='branch_code' name='branch_code' class='form-control custom-select'>
                            	<option value="">---Select branch---</option>
                                <?php
									echo $branches_list;
								?>
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