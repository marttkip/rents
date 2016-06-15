<?php

class Projects_model extends CI_Model 
{
	
	public function getregisteredlaboreres()
	{
		$this->db->where('tenant_id > 0 ');
		$query = $this->db->get('tenants');
		
		if($query->num_rows() > 0)
		{
			$response['admindetails'] = array();
			foreach ($query->result() as $key) {
				# code...
				$product = array();
				$product['tenant_id'] = $key->tenant_id;
				$product['tenant_name'] = $key->tenant_name;

				array_push($response['admindetails'], $product);
			}
			$response['status']=1;
        		$response['message'] ="Successfull login";
		}
		else
		{
			$response['status']=0;
        	 	$response['message'] ="Successfull login";
		}
		return $response;
	}
	public function save_tenant($array)
	{
		$fname = $array['fname'];
		$lname = $array['lname'];
		
		$array = array('fname'=>$fname,'lname'=>$lname);
		if($this->db->insert('tenants',$array))
		{
			$response['status'] = 1;
			$response['message'] = "Success";
		}
		else
		{
			$response['status'] = 0;
			$response['message'] = "Not successfull";
		}
		
		return $response;
	}
}
?>