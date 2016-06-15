<?php

class Leases_model extends CI_Model 
{
	/*
	*	Count all items from a table
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function count_items($table, $where, $limit = NULL)
	{
		if($limit != NULL)
		{
			$this->db->limit($limit);
		}
		$this->db->from($table);
		$this->db->where($where);
		return $this->db->count_all_results();
	}
	
	/*
	*	Retrieve all leases
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_leases($table, $where, $per_page, $page, $order = 'leases.lease_status', $order_method = 'DESC')
	{
		//retrieve all leases
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function add_lease($tenant_id,$rental_unit_id)
	{
		// check if the tenant has been allocated a unit
		$checker = $this->check_tenant_unit_account($tenant_id,$rental_unit_id);

		if($checker > 0)
		{
			$update_array = array('lease_status'=>0);
			$this->db->where('rental_unit_id = '.$rental_unit_id);
			$this->db->update('leases',$update_array);
			// means that the item has been successfully inserted
			$data = array(
				'lease_start_date'=>$this->input->post('lease_start_date'),
				'lease_duration'=>$this->input->post('lease_duration'),
				'lease_number'=>$this->create_lease_number($rental_unit_id),
				'rent_amount'=>$this->input->post('rent_amount'),
				'arrears_bf'=>$this->input->post('arrears_bf'),
				'deposit'=>$this->input->post('deposit_amount'),
				'deposit_ext'=>$this->input->post('deposit_ext'),
				'tenant_unit_id'=>$checker,
				'created'=>date('Y-m-d H:i:s'),
				'lease_status'=>1,
				'rental_unit_id'=>$rental_unit_id,
				'created_by'=>$this->session->userdata('personnel_id'),
				'branch_code'=>$this->session->userdata('branch_code')
			);
			
			if($this->db->insert('leases', $data))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
		
	}
	public function check_tenant_unit_account($tenant_id,$rental_unit_id)
	{
		$this->db->where('tenant_id = '.$tenant_id.' AND rental_unit_id = '.$rental_unit_id.' AND tenant_unit_status = 1');
		$this->db->from('tenant_unit');
		$this->db->select('*');
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			foreach ($query->result() as $key) {
				# code...
				$tenant_unit_id = $key->tenant_unit_id;
				$tenant_unit_status = $key->tenant_unit_status;
				
			}

			return $tenant_unit_id;
		}
		else if($query->num_rows() == 0)
		{
			// create the tenant unit number
			$insert_array = array(
							'tenant_id'=>$tenant_id,
							'rental_unit_id'=>$rental_unit_id,
							'created'=>date('Y-m-d'),
							'created_by'=>$this->session->userdata('personnel_id'),
							'tenant_unit_status'=>1,
							);
			$this->db->insert('tenant_unit',$insert_array);
			$tenant_unit_id = $this->db->insert_id();

			return $tenant_unit_id;
		}
	}

	public function create_lease_number($rental_unit_id)
	{
		//select product code
		$this->db->where('branch_code = "'.$this->session->userdata('branch_code').'" AND rental_unit_id ='.$rental_unit_id);
		$this->db->from('leases');
		$this->db->select('MAX(lease_number) AS number');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;//go to the next number
			if($number == 1){
				$number = "".$this->session->userdata('branch_code')."-0001";
			}
			
			if($number == 1)
			{
				$number = "".$this->session->userdata('branch_code')."-0001";
			}
			
		}
		else{//start generating receipt numbers
			$number = "".$this->session->userdata('branch_code')."-0001";
		}
		return $number;
	}
	
	/*
	*	Add a new front end lease to the database
	*
	*/
	public function add_frontend_lease()
	{
		$data = array(
				'lease_name'=>ucwords(strtolower($this->input->post('lease_name'))),
				'lease_email'=>$this->input->post('lease_email'),
				'lease_national_id'=>$this->input->post('lease_national_id'),
				'lease_password'=>md5(123456),
				'lease_phone_number'=>$this->input->post('lease_phone_number'),
				'created'=>date('Y-m-d H:i:s'),
				'lease_status'=>1,
				'created_by'=>$this->session->userdata('personnel_id'),
			);
			
		if($this->db->insert('leases', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing lease
	*	@param int $lease_id
	*
	*/
	public function edit_lease($lease_id)
	{
		$data = array(
				'lease_name'=>ucwords(strtolower($this->input->post('lease_name'))),
				'lease_email'=>$this->input->post('lease_email'),
				'lease_national_id'=>$this->input->post('lease_national_id'),
				'lease_phone_number'=>$this->input->post('lease_phone_number'),
				'lease_status'=>1,
				'modified_by'=>$this->session->userdata('personnel_id'),
			);
		
		//check if lease wants to update their password
		$pwd_update = $this->input->post('admin_lease');
		if(!empty($pwd_update))
		{
			if($this->input->post('old_password') == md5($this->input->post('current_password')))
			{
				$data['password'] = md5($this->input->post('new_password'));
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'The current password entered does not match your password. Please try again');
			}
		}
		
		$this->db->where('lease_id', $lease_id);
		
		if($this->db->update('leases', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing lease
	*	@param int $lease_id
	*
	*/
	public function edit_frontend_lease($lease_id)
	{
		$data = array(
				'lease_name'=>ucwords(strtolower($this->input->post('lease_name'))),
				'other_names'=>ucwords(strtolower($this->input->post('last_name'))),
				'phone'=>$this->input->post('phone')
			);
		
		//check if lease wants to update their password
		$pwd_update = $this->input->post('admin_lease');
		if(!empty($pwd_update))
		{
			if($this->input->post('old_password') == md5($this->input->post('current_password')))
			{
				$data['password'] = md5($this->input->post('new_password'));
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'The current password entered does not match your password. Please try again');
			}
		}
		
		$this->db->where('lease_id', $lease_id);
		
		if($this->db->update('leases', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing lease's password
	*	@param int $lease_id
	*
	*/
	public function edit_password($lease_id)
	{
		if($this->input->post('slug') == md5($this->input->post('current_password')))
		{
			if($this->input->post('new_password') == $this->input->post('confirm_password'))
			{
				$data['password'] = md5($this->input->post('new_password'));
		
				$this->db->where('lease_id', $lease_id);
				
				if($this->db->update('leases', $data))
				{
					$return['result'] = TRUE;
				}
				else{
					$return['result'] = FALSE;
					$return['message'] = 'Oops something went wrong and your password could not be updated. Please try again';
				}
			}
			else{
					$return['result'] = FALSE;
					$return['message'] = 'New Password and Confirm Password don\'t match';
			}
		}
		
		else
		{
			$return['result'] = FALSE;
			$return['message'] = 'You current password is not correct. Please try again';
		}
		
		return $return;
	}
	
	/*
	*	Retrieve a single lease
	*	@param int $lease_id
	*
	*/
	public function get_lease($lease_id)
	{
		//retrieve all leases
		$this->db->from('leases');
		$this->db->select('*');
		$this->db->where('lease_id = '.$lease_id);
		$query = $this->db->get();
		
		return $query;
	}

	

/*
	*	Retrieve a single lease
	*	@param int $lease_id
	*
	*/
	public function get_lease_detail($lease_id)
	{
		//retrieve all leases
		$this->db->from('leases,rental_unit,tenant_unit,tenants,property');
		$this->db->select('*');
		$this->db->where('leases.lease_id > 0 AND leases.tenant_unit_id = tenant_unit.tenant_unit_id AND tenant_unit.tenant_id = tenants.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id AND rental_unit.property_id = property.property_id AND lease_id = '.$lease_id);
		$query = $this->db->get();
		
		return $query;
	}


	
	/*
	*	Retrieve a single lease by their email
	*	@param int $email
	*
	*/
	public function get_lease_by_email($email)
	{
		//retrieve all leases
		$this->db->from('leases');
		$this->db->select('*');
		$this->db->where('email = \''.$email.'\'');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing lease
	*	@param int $lease_id
	*
	*/
	public function delete_lease($lease_id)
	{
		if($this->db->delete('leases', array('lease_id' => $lease_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated lease
	*	@param int $lease_id
	*
	*/
	public function activate_lease($lease_id)
	{
		$data = array(
				'activated' => 1
			);
		$this->db->where('lease_id', $lease_id);
		
		if($this->db->update('leases', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated lease
	*	@param int $lease_id
	*
	*/
	public function deactivate_lease($lease_id)
	{
		$data = array(
				'activated' => 0
			);
		$this->db->where('lease_id', $lease_id);
		
		if($this->db->update('leases', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	
	
	public function create_web_name($field_name)
	{
		$web_name = str_replace(" ", "-", strtolower($field_name));
		
		return $web_name;
	}

	public function get_tenant_unit_leases($tenant_id,$rental_unit_id)
	{
		$this->db->from('leases,tenant_unit');
		$this->db->select('*');
		$this->db->where('leases.tenant_unit_id = tenant_unit.tenant_unit_id AND tenant_unit.tenant_id = '.$tenant_id.' AND tenant_unit.rental_unit_id ='.$rental_unit_id);
		$this->db->order_by('leases.lease_id','DESC');
		$query = $this->db->get();
		
		return $query;
	}

	public function check_for_account($rental_unit_id)
	{

		$this->db->from('lease_unit');
		$this->db->select('*');
		$this->db->where('lease_unit_status = 1 AND rental_unit_id ='.$rental_unit_id);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function get_lease_list($table, $where, $order)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order,'asc');
		$query = $this->db->get('');
		
		return $query;
	}
}
?>