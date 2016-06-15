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
						<th><a>Points category date start</a></th>
						<th><a >Points category date end</a></th>
						<th><a >Points to award</a></th>
						<th><a >Status</a></th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			
			foreach ($query->result() as $row)
			{
				$points_category_id = $row->points_category_id;
				$points_category_date_from = $row->points_category_date_from;
				$points_category_date_to = $row->points_category_date_to;
				$points = $row->points;
				$created = $row->created;
				$points_category_status = $row->points_category_status;
				
				//status
				if($points_category_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
				
				//create deactivated status display
				if($points_category_status == 0)
				{
					$status = '<span class="label label-default"> Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'activate-points-category/'.$points_category_id.'" onclick="return confirm(\'Do you want to activate '.$points_category_date_from.'?\');" title="Activate '.$points_category_date_from.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($points_category_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'deactivate-points-category/'.$points_category_id.'" onclick="return confirm(\'Do you want to deactivate '.$points_category_date_from.'?\');" title="Deactivate '.$points_category_date_from.'"><i class="fa fa-thumbs-down"></i></a>';
				}
			
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$points_category_date_from.'</td>
						<td>'.$points_category_date_to.'</td>
						<td>'.$points.'</td>
						<td>'.$status.'</td>
						<td><a href="'.site_url().'edit-points-category/'.$points_category_id.'" class="btn btn-sm btn-success" title="Edit '.$points_category_date_from.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'deactivate-points-category/'.$points_category_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$points_category_date_from.'?\');" title="Delete '.$points_category_date_from.'"><i class="fa fa-trash"></i></a></td>
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
			$result .= "There are no points category added";
		}
?>

<section class="panel">
	<header class="panel-heading">						
		<h2 class="panel-title"><?php echo $title;?> <div class="pull-right" ><a href="<?php echo site_url();?>real-estate-administration/add-points-category" style="margin-top:-5px" class="btn btn-sm btn-info "><i class="fa fa-plus"></i> Add Points Category</a></div></h2>
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