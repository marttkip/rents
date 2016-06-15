<?php

class tenants_model extends CI_Model 
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
	*	Retrieve all tenants
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_tenants($table, $where, $per_page, $page, $order = 'tenants.tenant_name', $order_method = 'ASC')
	{
		//retrieve all tenants
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		// $this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve all administrators
	*
	*/
	public function get_active_tenants()
	{
		$this->db->from('personnel');
		$this->db->select('*');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all front end tenants
	*
	*/
	public function get_all_front_end_tenants()
	{
		$this->db->from('tenants');
		$this->db->select('*');
		$this->db->where('tenant_level_id = 2');
		$query = $this->db->get();
		
		return $query;
	}
	

	public function get_all_countries()
	{
		//retrieve all tenants
		$query = $this->db->get('country');
		
		return $query;
	}
	
	/*
	*	Add a new tenant to the database
	*
	*/
	public function add_tenant()
	{
		$data = array(
				'tenant_name'=>ucwords(strtolower($this->input->post('tenant_name'))),
				'tenant_email'=>$this->input->post('tenant_email'),
				'tenant_number'=>$this->create_tenant_number(),
				'tenant_national_id'=>$this->input->post('tenant_national_id'),
				'tenant_phone_number'=>$this->input->post('tenant_phone_number'),
				'created'=>date('Y-m-d H:i:s'),
				'tenant_status'=>1,
				'created_by'=>$this->session->userdata('personnel_id')
			);
			
		if($this->db->insert('tenants', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function add_tenant_to_unit($rental_unit_id)
	{
		$this->db->where('tenant_unit_status = 1 AND rental_unit_id = '.$rental_unit_id.'');
		$this->db->from('tenant_unit');
		$this->db->select('*');
		$query = $this->db->get();

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$tenant_unit_id = $key->tenant_unit_id;
				$tenant_unit_status = $key->tenant_unit_status;
					// update the details the status to 1 
				$update_array = array('tenant_unit_status'=>0);
				$this->db->where('tenant_unit_id = '.$tenant_unit_id);
				$this->db->update('tenant_unit',$update_array);
			}
			$insert_array = array(
							'tenant_id'=>$this->input->post('tenant_id'),
							'rental_unit_id'=>$rental_unit_id,
							'created'=>date('Y-m-d'),
							'created_by'=>$this->session->userdata('personnel_id'),
							'tenant_unit_status'=>1,
							);
			$this->db->insert('tenant_unit',$insert_array);
			return TRUE;
		}
		else
		{
			// create the tenant unit number
			$insert_array = array(
							'tenant_id'=>$this->input->post('tenant_id'),
							'rental_unit_id'=>$rental_unit_id,
							'created'=>date('Y-m-d'),
							'created_by'=>$this->session->userdata('personnel_id'),
							'tenant_unit_status'=>1,
							);
			$this->db->insert('tenant_unit',$insert_array);
			$tenant_unit_id = $this->db->insert_id();

			return TRUE;
		}
	}
	public function create_tenant_number()
	{
		//select product code
		$this->db->where('branch_code = "'.$this->session->userdata('branch_code').'"');
		$this->db->from('tenants');
		$this->db->select('MAX(tenant_number) AS number');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;//go to the next number
			if($number == 1){
				$number = "".$this->session->userdata('branch_code')."-000001";
			}
			
			if($number == 1)
			{
				$number = "".$this->session->userdata('branch_code')."-000001";
			}
			
		}
		else{//start generating receipt numbers
			$number = "".$this->session->userdata('branch_code')."-000001";
		}
		return $number;
	}
	
	/*
	*	Add a new front end tenant to the database
	*
	*/
	public function add_frontend_tenant()
	{
		$data = array(
				'tenant_name'=>ucwords(strtolower($this->input->post('tenant_name'))),
				'tenant_email'=>$this->input->post('tenant_email'),
				'tenant_national_id'=>$this->input->post('tenant_national_id'),
				'tenant_password'=>md5(123456),
				'tenant_phone_number'=>$this->input->post('tenant_phone_number'),
				'created'=>date('Y-m-d H:i:s'),
				'tenant_status'=>1,
				'created_by'=>$this->session->userdata('personnel_id'),
			);
			
		if($this->db->insert('tenants', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing tenant
	*	@param int $tenant_id
	*
	*/
	public function edit_tenant($tenant_id)
	{
		$data = array(
				'tenant_name'=>ucwords(strtolower($this->input->post('tenant_name'))),
				'tenant_email'=>$this->input->post('tenant_email'),
				'tenant_national_id'=>$this->input->post('tenant_national_id'),
				'tenant_phone_number'=>$this->input->post('tenant_phone_number'),
				'tenant_status'=>1,
				'modified_by'=>$this->session->userdata('personnel_id'),
			);
		
		
		$this->db->where('tenant_id', $tenant_id);
		
		if($this->db->update('tenants', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing tenant
	*	@param int $tenant_id
	*
	*/
	public function edit_frontend_tenant($tenant_id)
	{
		$data = array(
				'tenant_name'=>ucwords(strtolower($this->input->post('tenant_name'))),
				'other_names'=>ucwords(strtolower($this->input->post('last_name'))),
				'phone'=>$this->input->post('phone')
			);
		
		//check if tenant wants to update their password
		$pwd_update = $this->input->post('admin_tenant');
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
		
		$this->db->where('tenant_id', $tenant_id);
		
		if($this->db->update('tenants', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing tenant's password
	*	@param int $tenant_id
	*
	*/
	public function edit_password($tenant_id)
	{
		if($this->input->post('slug') == md5($this->input->post('current_password')))
		{
			if($this->input->post('new_password') == $this->input->post('confirm_password'))
			{
				$data['password'] = md5($this->input->post('new_password'));
		
				$this->db->where('tenant_id', $tenant_id);
				
				if($this->db->update('tenants', $data))
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
	*	Retrieve a single tenant
	*	@param int $tenant_id
	*
	*/
	public function get_tenant($tenant_id)
	{
		//retrieve all tenants
		$this->db->from('tenants');
		$this->db->select('*');
		$this->db->where('tenant_id = '.$tenant_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve a single tenant by their email
	*	@param int $email
	*
	*/
	public function get_tenant_by_email($email)
	{
		//retrieve all tenants
		$this->db->from('tenants');
		$this->db->select('*');
		$this->db->where('email = \''.$email.'\'');
		$query = $this->db->get();
		
		return $query;
	}

	
	
	/*
	*	Delete an existing tenant
	*	@param int $tenant_id
	*
	*/
	public function delete_tenant($tenant_id)
	{
		if($this->db->delete('tenants', array('tenant_id' => $tenant_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated tenant
	*	@param int $tenant_id
	*
	*/
	public function activate_tenant($tenant_id)
	{
		$data = array(
				'tenant_status' => 1
			);
		$this->db->where('tenant_id', $tenant_id);
		
		if($this->db->update('tenants', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an tenant_status tenant
	*	@param int $tenant_id
	*
	*/
	public function deactivate_tenant($tenant_id)
	{
		$data = array(
				'tenant_status' => 0
			);
		$this->db->where('tenant_id', $tenant_id);
		
		if($this->db->update('tenants', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Reset a tenant's password
	*	@param string $email
	*
	*/
	public function reset_password($email)
	{
		//reset password
		$result = md5(date("Y-m-d H:i:s"));
		$pwd2 = substr($result, 0, 6);
		$pwd = md5($pwd2);
		
		$data = array(
				'password' => $pwd
			);
		$this->db->where('email', $email);
		
		if($this->db->update('tenants', $data))
		{
			//email the password to the tenant
			$tenant_details = $this->tenants_model->get_tenant_by_email($email);
			
			$tenant = $tenant_details->row();
			$tenant_name = $tenant->tenant_name;
			
			//email data
			$receiver['email'] = $this->input->post('email');
			$sender['name'] = 'Fad Shoppe';
			$sender['email'] = 'info@fadshoppe.com';
			$message['subject'] = 'You requested a password change';
			$message['text'] = 'Hi '.$tenant_name.'. Your new password is '.$pwd;
			
			//send the tenant their new password
			if($this->email_model->send_mail($receiver, $sender, $message))
			{
				return TRUE;
			}
			
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	public function create_web_name($field_name)
	{
		$web_name = str_replace(" ", "-", strtolower($field_name));
		
		return $web_name;
	}
	public function change_password()
	{
		
		$data = array(
				'personnel_password' => md5($this->input->post('new_password'))
			);
		$this->db->where('personnel_password = "'.md5($this->input->post('current_password')).'" AND personnel_id ='.$this->session->userdata('personnel_id'));
		
		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_tenancy_details($tenant_id,$rental_unit_id)
	{
		$this->db->from('tenant_unit');
		$this->db->select('*');
		$this->db->where('tenant_id = '.$tenant_id.' AND rental_unit_id ='.$rental_unit_id);
		$query = $this->db->get();
		
		return $query;
	}

	public function check_for_account($rental_unit_id)
	{

		$this->db->from('tenant_unit');
		$this->db->select('*');
		$this->db->where('tenant_unit_status = 1 AND rental_unit_id ='.$rental_unit_id);
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

	public function get_tenant_list($table, $where, $order)
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