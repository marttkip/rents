<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Property_owners extends admin {
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('property_owners_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/users_model');
	}
    
	/*
	*
	*	Default action is to show all the property_owners
	*
	*/
	public function index($order = 'property_owner_name', $order_method = 'ASC') 
	{
		$where = 'property_owner_id > 0';
		$table = 'property_owners';
		//pagination
		$segment = 5;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'real-estate-administration/property-owners/'.$order.'/'.$order_method;
		$config['total_rows'] = $this->property_owners_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $data["links"] = $this->pagination->create_links();
		$query = $this->property_owners_model->get_all_property_owners($table, $where, $config["per_page"], $page, $order, $order_method);

		$data['title'] = 'Property Owners';
		$v_data['title'] = $data['title'];
		
		$v_data['order'] = $order;
		$v_data['order_method'] = $order_method;
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$data['content'] = $this->load->view('property_owners/all_property_owners', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Add a new property_owner page
	*
	*/
	public function add_property_owner() 
	{
		//form validation rules
		$this->form_validation->set_rules('property_owner_email', 'Email', 'required|xss_clean|is_unique[property_owners.property_owner_email]|valid_email');
		$this->form_validation->set_rules('property_owner_name', 'Owner Name', 'required|xss_clean');
		$this->form_validation->set_rules('property_owner_phone', '', 'required|xss_clean');
		$this->form_validation->set_rules('property_owner_username', 'Username', 'xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//check if property_owner has valid login credentials
			if($this->property_owners_model->add_property_owner())
			{
				redirect('real-estate-administration/property-owners');
			}
			
			else
			{
				$data['error'] = 'Unable to add property owner. Please try again';
			}
		}
		
		//open the add new property_owner page

		$data['title'] = 'Add administrator';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('property_owners/add_property_owner', $v_data, TRUE);
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Edit an existing property_owner page
	*	@param int $property_owner_id
	*
	*/
	public function edit_property_owner_details($property_owner_id) 
	{
		 // var_dump($property_owner_id); die();
		//form validation rules
		$this->form_validation->set_rules('property_owner_email', 'Email', 'required|xss_clean|exists[property_owners.property_owner_email]|valid_email');
		$this->form_validation->set_rules('property_owner_name', 'Owner Name', 'required|xss_clean');
		$this->form_validation->set_rules('property_owner_phone', '', 'required|xss_clean');
		$this->form_validation->set_rules('property_owner_username', 'Username', 'xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//check if property_owner has valid login credentials
			if($this->property_owners_model->edit_property_owner($property_owner_id))
			{
				$this->session->set_userdata('success_message', 'property_owner edited successfully');
				$pwd_update = $this->input->post('admin_property_owner');
				if(!empty($pwd_update))
				{
					redirect('admin-profile/'.$property_owner_id);
				}
				
				else
				{
					redirect('real-estate-administration/property-owners');
				}
			}
			
			else
			{
				$data['error'] = 'Unable to add property_owner. Please try again';
			}
		}
		
		//open the add new property_owner page
		$data['title'] = 'Edit administrator';
		$v_data['title'] = $data['title'];
		
		//select the property_owner from the database
		$query = $this->property_owners_model->get_property_owner($property_owner_id);
		if ($query->num_rows() > 0)
		{
			$v_data['query'] = $query;
			$data['content'] = $this->load->view('property_owners/edit_property_owner', $v_data, true);
		}
		
		else
		{
			$data['content'] = 'property_owner does not exist';
		}
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing property_owner page
	*	@param int $property_owner_id
	*
	*/
	public function delete_property_owner($property_owner_id) 
	{
		if($this->property_owners_model->delete_property_owner($property_owner_id))
		{
			$this->session->set_userdata('success_message', 'Administrator has been deleted');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Administrator could not be deleted');
		}
		
		redirect('real-estate-administration/property-owners');
	}
    
	/*
	*
	*	Activate an existing property_owner page
	*	@param int $property_owner_id
	*
	*/
	public function activate_property_owner($property_owner_id) 
	{
		if($this->property_owners_model->activate_property_owner($property_owner_id))
		{
			$this->session->set_userdata('success_message', 'Administrator has been activated');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Administrator could not be activated');
		}
		
		redirect('real-estate-administration/property-owners');
	}
    
	/*
	*
	*	Deactivate an existing property_owner page
	*	@param int $property_owner_id
	*
	*/
	public function deactivate_property_owner($property_owner_id) 
	{
		if($this->property_owners_model->deactivate_property_owner($property_owner_id))
		{
			$this->session->set_userdata('success_message', 'Administrator has been disabled');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Administrator could not be disabled');
		}
		
		redirect('real-estate-administration/property-owners');
	}
	
	/*
	*
	*	Reset a property_owner's password
	*	@param int $property_owner_id
	*
	*/
	public function reset_password($property_owner_id)
	{
		$new_password = $this->login_model->reset_password($property_owner_id);
		$this->session->set_userdata('success_message', 'New password is <br/><strong>'.$new_password.'</strong>');
		
		redirect('real-estate-administration/property-owners');
	}
	
	/*
	*
	*	Show an administrator's profile
	*	@param int $property_owner_id
	*
	*/
	public function admin_profile($property_owner_id)
	{
		//open the add new property_owner page
		$data['title'] = 'Edit property_owner';
		
		//select the property_owner from the database
		$query = $this->property_owners_model->get_property_owner($property_owner_id);
		if ($query->num_rows() > 0)
		{
			$v_data['property_owners'] = $query->result();
			$v_data['admin_property_owner'] = 1;
			$tab_content[0] = $this->load->view('property_owners/edit_property_owner', $v_data, true);
		}
		
		else
		{
			$data['tab_content'][0] = 'property_owner does not exist';
		}
		$tab_name[1] = 'Overview';
		$tab_name[0] = 'Edit Account';
		$tab_content[1] = 'Coming soon';//$this->load->view('account_overview', $v_data, true);
		$data['total_tabs'] = 2;
		$data['content'] = $tab_content;
		$data['tab_name'] = $tab_name;
		
		$this->load->view('templates/tabs', $data);
	}
	public function change_password()
	{
		$this->form_validation->set_rules('current_password', 'Current Password', 'required|xss_clean');
		$this->form_validation->set_rules('new_password', 'New Password', 'required|xss_clean');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm New Password', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->input->post('new_password') == $this->input->post('confirm_new_password'))
			{
				if($this->property_owners_model->change_password())
				{
					$this->session->set_userdata('success_message', 'Your password has been changed successfully');
				}
				else
				{
					$this->session->set_userdata('error_message', 'Something went wrong, please try again');
				}
			}
			else
			{
				$this->session->set_userdata('error_message', 'Ensure that the new password match with the confirm password');
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Please check that all the fields have values');
		}
		redirect('dashboard');
	}
}
?>