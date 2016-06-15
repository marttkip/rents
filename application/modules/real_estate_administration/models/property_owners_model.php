<?php

class Property_owners_model extends CI_Model 
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
	*	Retrieve all property_owners
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_property_owners($table, $where, $per_page, $page, $order = 'property_owner_name', $order_method = 'ASC')
	{
		//retrieve all property_owners
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve all administrators
	*
	*/
	public function get_active_property_owners()
	{
		$this->db->from('personnel');
		$this->db->select('*');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all front end property_owners
	*
	*/
	public function get_all_front_end_property_owners()
	{
		$this->db->from('property_owners');
		$this->db->select('*');
		$this->db->where('property_owner_id > 0');
		$query = $this->db->get();
		
		return $query;
	}
	
	public function get_all_countries()
	{
		//retrieve all property_owners
		$query = $this->db->get('country');
		
		return $query;
	}
	
	/*
	*	Add a new property_owner to the database
	*
	*/
	public function add_property_owner()
	{
		$data = array(
				'property_owner_name'=>ucwords(strtolower($this->input->post('property_owner_name'))),
				'property_owner_email'=>$this->input->post('property_owner_email'),
				'property_owner_phone'=>$this->input->post('property_owner_phone'),
				'property_owner_username'=>$this->input->post('property_owner_username'),
				'property_owner_password'=>md5(123456),
				'created'=>date('Y-m-d H:i:s'),
				'property_owner_status'=>$this->input->post('property_owner_status')
			);
			
		if($this->db->insert('property_owners', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	

	
	/*
	*	Edit an existing property_owner
	*	@param int $property_owner_id
	*
	*/
	public function edit_property_owner($property_owner_id)
	{
		$data = array(
				'property_owner_name'=>ucwords(strtolower($this->input->post('property_owner_name'))),
				'property_owner_email'=>$this->input->post('property_owner_email'),
				'property_owner_phone'=>$this->input->post('property_owner_phone'),
				'property_owner_username'=>$this->input->post('property_owner_username'),
				'created'=>date('Y-m-d H:i:s'),
				'property_owner_status'=>$this->input->post('property_owner_status')
			);
		
		//check if property_owner wants to update their password
		$pwd_update = $this->input->post('admin_property_owner');
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
		
		$this->db->where('property_owner_id', $property_owner_id);
		
		if($this->db->update('property_owners', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing property_owner
	*	@param int $property_owner_id
	*
	*/
	public function edit_frontend_property_owner($property_owner_id)
	{
		$data = array(
				'property_owner_name'=>ucwords(strtolower($this->input->post('property_owner_name'))),
				'other_names'=>ucwords(strtolower($this->input->post('last_name'))),
				'phone'=>$this->input->post('phone')
			);
		
		//check if property_owner wants to update their password
		$pwd_update = $this->input->post('admin_property_owner');
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
		
		$this->db->where('property_owner_id', $property_owner_id);
		
		if($this->db->update('property_owners', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Edit an existing property_owner's password
	*	@param int $property_owner_id
	*
	*/
	public function edit_password($property_owner_id)
	{
		if($this->input->post('slug') == md5($this->input->post('current_password')))
		{
			if($this->input->post('new_password') == $this->input->post('confirm_password'))
			{
				$data['password'] = md5($this->input->post('new_password'));
		
				$this->db->where('property_owner_id', $property_owner_id);
				
				if($this->db->update('property_owners', $data))
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
	*	Retrieve a single property_owner
	*	@param int $property_owner_id
	*
	*/
	public function get_property_owner($property_owner_id)
	{
		//retrieve all property_owners
		$this->db->from('property_owners');
		$this->db->select('*');
		$this->db->where('property_owner_id = '.$property_owner_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve a single property_owner by their email
	*	@param int $email
	*
	*/
	public function get_property_owner_by_email($email)
	{
		//retrieve all property_owners
		$this->db->from('property_owners');
		$this->db->select('*');
		$this->db->where('property_owner_email = \''.$email.'\'');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing property_owner
	*	@param int $property_owner_id
	*
	*/
	public function delete_property_owner($property_owner_id)
	{
		if($this->db->delete('property_owners', array('property_owner_id' => $property_owner_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deproperty_owner_status property_owner
	*	@param int $property_owner_id
	*
	*/
	public function activate_property_owner($property_owner_id)
	{
		$data = array(
				'property_owner_status' => 1
			);
		$this->db->where('property_owner_id', $property_owner_id);
		
		if($this->db->update('property_owners', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an property_owner_status property_owner
	*	@param int $property_owner_id
	*
	*/
	public function deactivate_property_owner($property_owner_id)
	{
		$data = array(
				'property_owner_status' => 0
			);
		$this->db->where('property_owner_id', $property_owner_id);
		
		if($this->db->update('property_owners', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Reset a property_owner's password
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
		
		if($this->db->update('property_owners', $data))
		{
			//email the password to the property_owner
			$property_owner_details = $this->property_owners_model->get_property_owner_by_email($email);
			
			$property_owner = $property_owner_details->row();
			$property_owner_name = $property_owner->property_owner_name;
			
			//email data
			$receiver['email'] = $this->input->post('email');
			$sender['name'] = 'Fad Shoppe';
			$sender['email'] = 'info@fadshoppe.com';
			$message['subject'] = 'You requested a password change';
			$message['text'] = 'Hi '.$property_owner_name.'. Your new password is '.$pwd;
			
			//send the property_owner their new password
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
}
?>