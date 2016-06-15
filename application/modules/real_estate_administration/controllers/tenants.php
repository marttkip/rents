<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/real_estate_administration/controllers/property.php";

class Tenants extends property {
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('tenants_model');
		$this->load->model('rental_unit_model');
		$this->load->model('leases_model');
	}
    
	/*
	*
	*	Default action is to show all the tenants
	*
	*/
	public function index($rental_unit_id = NULL,$pager = NULL) 
	{
		
		if($rental_unit_id == NULL || $rental_unit_id == 0)
		{
			$where = 'tenant_id > 0';
			$table = 'tenants';
			$addition = '/'.$rental_unit_id.'/'.$pager;
			$segment = 5;
		}
		else
		{
			if($pager == NULL)
			{
				$addition = '/'.$rental_unit_id;
				$segment = 4;
			}
			else
			{
				$addition = '/'.$rental_unit_id.'/'.$pager;
				$segment = 5;
			}
			
			 $where = 'tenants.tenant_id > 0 AND tenants.tenant_id = tenant_unit.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id  AND tenant_unit.rental_unit_id ='.$rental_unit_id;
			$table = 'tenants,tenant_unit,rental_unit';
		}
		
		$tenants_search = $this->session->userdata('all_tenants_search');
		
		if(!empty($tenants_search))
		{
			$where .= $tenants_search;	
			
		}
		//pagination
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'tenants'.$addition;
		$config['total_rows'] = $this->tenants_model->count_items($table, $where);
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
		$query = $this->tenants_model->get_all_tenants($table, $where, $config["per_page"], $page, $order=NULL, $order_method=NULL);

		$tenant_order = 'tenants.tenant_name';
		$tenant_table = 'tenants';
		$tenant_where = 'tenants.tenant_status = 1';

		$tenant_query = $this->tenants_model->get_tenant_list($tenant_table, $tenant_where, $tenant_order);
		$rs8 = $tenant_query->result();
		$tenants_list = '';
		foreach ($rs8 as $tenant_rs) :
			$tenant_id = $tenant_rs->tenant_id;
			$tenant_name = $tenant_rs->tenant_name;

			$tenant_national_id = $tenant_rs->tenant_national_id;
			$tenant_phone_number = $tenant_rs->tenant_phone_number;

		    $tenants_list .="<option value='".$tenant_id."'>".$tenant_name." Phone: ".$tenant_phone_number."</option>";

		endforeach;

		$v_data['tenants_list'] = $tenants_list;
		$v_data['pager'] = $pager;

		if($rental_unit_id != NULL AND $rental_unit_id != 0)
		{
			$rental_unit_name = $this->rental_unit_model->get_rental_unit_name($rental_unit_id);
			$data['title'] = $rental_unit_name.'\'s Tenants';
			$v_data['title'] = $data['title'];
		}
		else
		{
			$data['title'] = 'All Tenants';
			$v_data['title'] = $data['title'];
		}		
		
		$v_data['rental_unit_id'] = $rental_unit_id;
		$v_data['tenants'] = $query;
		$v_data['page'] = $page;
		$data['content'] = $this->load->view('tenants/all_tenants', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_tenants()
	{
		$tenant_name = $this->input->post('tenant_name');
		$tenant_phone_number = $this->input->post('tenant_phone_number');
		$tenant_national_id = $this->input->post('tenant_national_id');
		
		$search_title = 'Showing reports for: ';
		
		
		if(!empty($tenant_name))
		{
			$tenant_name = ' AND tenants.tenant_name LIKE \'%'.$tenant_name.'%\'';
		}
		else
		{
			$tenant_name = '';
		}

		if(!empty($tenant_phone_number))
		{
			$tenant_phone_number = ' AND tenants.tenant_phone_number LIKE \'%'.$tenant_phone_number.'%\'';
		}
		else
		{
			$tenant_phone_number = '';
		}

		if(!empty($tenant_national_id))
		{
			$tenant_national_id = ' AND tenants.tenant_national_id LIKE \'%'.$tenant_national_id.'%\'';
		}
		else
		{
			$tenant_national_id = '';
		}

		$search = $tenant_name.$tenant_national_id.$tenant_phone_number;

		$tenants_search = $this->session->userdata('all_tenants_search');
		
		
		$this->session->set_userdata('all_tenants_search', $search);
		$this->index(0,NULL);
	}
	public function close_tenants_search()
	{
		$this->session->unset_userdata('all_tenants_search');
		$this->session->unset_userdata('search_title');
		
		
		redirect('rental-management/tenants');
	}
    
	/*
	*
	*	Add a new tenant page
	*
	*/
	public function add_tenant($rental_unit_id = NULL) 
	{
		//form validation rules
		// $this->form_validation->set_rules('tenant_email', 'Email', 'xss_clean|is_unique[tenants.tenant_email]|valid_email');
		$this->form_validation->set_rules('tenant_name', 'Tenant name', 'required|xss_clean|is_unique[tenants.tenant_name]');
		// $this->form_validation->set_rules('tenant_phone_number', 'Tenant Phone Number', 'required|xss_clean|is_unique[tenants.tenant_phone_number]');
		$this->form_validation->set_rules('tenant_national_id', 'National Id', 'xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//check if tenant has valid login credentials
			if($this->tenants_model->add_tenant())
			{
				$this->session->unset_userdata('tenants_error_message');
				$this->session->set_userdata('success_message', 'Tenant has been successfully added');

				if($rental_unit_id == NULL OR $rental_unit_id == 0)
				{
					redirect('rental-management/tenants');
				}
				else
				{
					redirect('rents/tenants/'.$rental_unit_id);
				}

				
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Sorry something went wrong. Please try again');
				if($rental_unit_id == NULL OR $rental_unit_id == 0)
				{
					redirect('rental-management/tenants');
				}
				else
				{
					redirect('rents/tenants/'.$rental_unit_id);
				}

			}
		}
		
		//open the add new tenant page
	}
	public function allocate_tenant_to_unit($rental_unit_id)
	{
		$this->form_validation->set_rules('tenant_id', 'Tenant id', 'required|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if($this->tenants_model->add_tenant_to_unit($rental_unit_id))
			{
				$this->session->unset_userdata('lease_error_message');
				$this->session->set_userdata('success_message', 'Tenant has been added successfully');
			}
			else
			{
				$this->session->set_userdata('error_message', 'Sorry please check for errors has been added successfully');
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Please fill in all the fields');
		}
		redirect('tenants/'.$rental_unit_id);	
	}
	public function allocate_tenant_to_unit_other($rental_unit_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('tenant_id', 'Tenant name', 'required|xss_clean');
		$this->form_validation->set_rules('rental_unit_id', 'Rental Unit', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			// check if another tenant account is active
			$rental_unit_id = $this->input->post('rental_unit_id');
			$checker = $this->tenants_model->check_for_account($rental_unit_id);

			if($checker = TRUE)
			{
				$this->session->set_userdata('error_message', 'Seems Like another lease is still active, Please close the active lease before proceeding');
				redirect('rents/tenants/'.$rental_unit_id);
			}
			else
			{
				//check if tenant has valid login credentials
				if($this->tenants_model->allocate_tenant_to_rental_unit())
				{
					$this->session->unset_userdata('tenants_error_message');
					$this->session->set_userdata('success_message', 'Tenant has been successfully added');
					redirect('rents/tenants/'.$rental_unit_id);
				}
				
				else
				{
					$this->session->set_userdata('error_message', 'Sorry something went wrong. Please try again');
					redirect('rents/tenants/'.$rental_unit_id);

				}
			}
				
		}
		
		//open the add new tenant page
	}
    
	/*
	*
	*	Edit an existing tenant page
	*	@param int $tenant_id
	*
	*/
	public function edit_tenant($tenant_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('tenant_email', 'Email', 'xss_clean|exists[tenants.tenant_email]|valid_email');
		$this->form_validation->set_rules('tenant_name', 'Tenant name', 'required|xss_clean');
		$this->form_validation->set_rules('tenant_phone_number', 'Tenant Phone Number', 'required|xss_clean|exists[tenants.tenant_phone_number]');
		$this->form_validation->set_rules('tenant_national_id', 'National Id', 'xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//check if tenant has valid login credentials
			if($this->tenants_model->edit_tenant($tenant_id))
			{
				$this->session->set_userdata('success_message', 'tenant edited successfully');
				
				
			}
			
			else
			{
				$data['error'] = 'Unable to add tenant. Please try again';
			}
			redirect('rental-management/tenants');
		}
		
		//open the add new tenant page
		$data['title'] = 'Edit Tenant';
		$v_data['title'] = $data['title'];
		
		//select the tenant from the database
		$query = $this->tenants_model->get_tenant($tenant_id);
		if ($query->num_rows() > 0)
		{
			$v_data['query'] = $query;
			$data['content'] = $this->load->view('tenants/edit_tenant', $v_data, true);
		}
		
		else
		{
			$data['content'] = 'tenant does not exist';
		}
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing tenant page
	*	@param int $tenant_id
	*
	*/
	public function delete_tenant($tenant_id) 
	{
		if($this->tenants_model->delete_tenant($tenant_id))
		{
			$this->session->set_userdata('success_message', 'Administrator has been deleted');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Administrator could not be deleted');
		}
		
		redirect('rental-management/tenants');
	}
    
	/*
	*
	*	Activate an existing tenant page
	*	@param int $tenant_id
	*
	*/
	public function activate_tenant($tenant_id) 
	{
		if($this->tenants_model->activate_tenant($tenant_id))
		{
			$this->session->set_userdata('success_message', 'Administrator has been activated');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Administrator could not be activated');
		}
		
		redirect('rental-management/tenants');
	}
    
	/*
	*
	*	Deactivate an existing tenant page
	*	@param int $tenant_id
	*
	*/
	public function deactivate_tenant($tenant_id) 
	{
		if($this->tenants_model->deactivate_tenant($tenant_id))
		{
			$this->session->set_userdata('success_message', 'Administrator has been disabled');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Administrator could not be disabled');
		}
		
		redirect('rental-management/tenants');
	}
	
	/*
	*
	*	Reset a tenant's password
	*	@param int $tenant_id
	*
	*/
	public function reset_password($tenant_id)
	{
		$new_password = $this->login_model->reset_password($tenant_id);
		$this->session->set_userdata('success_message', 'New password is <br/><strong>'.$new_password.'</strong>');
		
		redirect('admin/administrators');
	}
	
	/*
	*
	*	Show an administrator's profile
	*	@param int $tenant_id
	*
	*/
	public function admin_profile($tenant_id)
	{
		//open the add new tenant page
		$data['title'] = 'Edit tenant';
		
		//select the tenant from the database
		$query = $this->tenants_model->get_tenant($tenant_id);
		if ($query->num_rows() > 0)
		{
			$v_data['tenants'] = $query->result();
			$v_data['admin_tenant'] = 1;
			$tab_content[0] = $this->load->view('tenants/edit_tenant', $v_data, true);
		}
		
		else
		{
			$data['tab_content'][0] = 'tenant does not exist';
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
				if($this->tenants_model->change_password())
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