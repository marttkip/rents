<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo $this->load->view('includes/header');?>
</head>

<body>
	<input type="hidden" id="config_url" value="<?php echo site_url();?>"/>
	<?php echo $this->load->view('includes/navigation');?>

    <!-- Main content starts -->
    
    <div class="content">
    
        <!-- Sidebar -->
        <?php echo $this->load->view('includes/'.$sidebar);?>
        <!-- Sidebar ends -->
        
        <!-- Main bar -->
        <div class="mainbar">
        
            <!-- Page heading -->
            <?php //echo $this->load->view('includes/breadcrumbs');?>
            <!-- Page heading ends -->
            
            <!-- Matter -->
            
            <div class="matter">
                <div class="container" style="padding-top:20px;">
                	<?php echo $content;?>
                </div>
            </div>
            
            <!-- Matter ends -->
        
        </div>
        
        <!-- Mainbar ends -->	    	
        <div class="clearfix"></div>
    
    </div>
    <!-- Content ends -->

	<?php echo $this->load->view('includes/footer');?>
</body>
</html>