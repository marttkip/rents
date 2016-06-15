<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/real_estate_administration/controllers/property.php";

class Accounts extends property {
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('real_estate_administration/tenants_model');
		$this->load->model('real_estate_administration/rental_unit_model');
		$this->load->model('real_estate_administration/leases_model');
		$this->load->model('real_estate_administration/property_owners_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('administration/reports_model');
	}
    
	/*
	*
	*	Default action is to show all the tenants
	*
	*/
	public function index() 
	{
			
		$where = 'tenants.tenant_id > 0 AND tenants.tenant_id = tenant_unit.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id  AND tenant_unit.tenant_unit_id = leases.tenant_unit_id AND leases.lease_status = 1';
		$table = 'tenants,tenant_unit,rental_unit,leases';		


		$accounts_search = $this->session->userdata('all_accounts_search');
		
		if(!empty($accounts_search))
		{
			$where .= $accounts_search;	
			
		}
		$segment = 4;
		//pagination
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'cash-office/accounts';
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
		$query = $this->accounts_model->get_all_tenants($table, $where, $config["per_page"], $page, $order=NULL, $order_method=NULL);

		$properties = $this->property_model->get_active_property();
		$rs8 = $properties->result();
		$property_list = '';
		foreach ($rs8 as $property_rs) :
			$property_id = $property_rs->property_id;
			$property_name = $property_rs->property_name;
			$property_location = $property_rs->property_location;

		    $property_list .="<option value='".$property_id."'>".$property_name." Location: ".$property_location."</option>";

		endforeach;
		$v_data['property_list'] = $property_list;
		$data['title'] = 'All Tenants';
		$v_data['title'] = $data['title'];
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$data['content'] = $this->load->view('cash_office/tenants_list', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function search_accounts()
	{
		$property_id = $this->input->post('property_id');
		$tenant_name = $this->input->post('tenant_name');
		$tenant_phone_number = $this->input->post('tenant_phone_number');
		$tenant_national_id = $this->input->post('tenant_national_id');
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($property_id))
		{
			$property_id = ' AND rental_unit.property_id = '.$property_id.' ';
			
			$this->db->where('property_id', $property_id);
			$query = $this->db->get('property');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->property_name.' ';
			}
		}
		
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

		$search = $property_id.$tenant_name.$tenant_national_id.$tenant_phone_number;

		$property_search = $property_id;

		$accounts_search = $this->session->userdata('all_accounts_search');
		
		
		$this->session->set_userdata('all_accounts_search', $search);
		$this->session->set_userdata('search_title', $search_title);
		
		$this->index();
	}
	public function close_accounts_search()
	{
		$this->session->unset_userdata('all_accounts_search');
		$this->session->unset_userdata('search_title');
		
		
		redirect('cash-office/accounts');
	}
	public function make_payments($tenant_unit_id , $lease_id, $close_page = NULL)
	{
		$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');
		$this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('payment_date', 'Date Receipted', 'trim|required|xss_clean');
		$payment_method = $this->input->post('payment_method');
		
		// Normal
		
		if(!empty($payment_method))
		{
			if($payment_method == 1)
			{
				// check for cheque number if inserted
				$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
			}
			else if($payment_method == 5)
			{
				//  check for mpesa code if inserted
				$this->form_validation->set_rules('mpesa_code', 'Amount', 'trim|required|xss_clean');
			}
		}
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			
			
			$this->accounts_model->receipt_payment($lease_id);
			
			redirect('accounts/payments/'.$tenant_unit_id.'/'.$lease_id);
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
			redirect('accounts/payments/'.$tenant_unit_id.'/'.$lease_id);
		}
	}
	
    public function payments($tenant_unit_id,$lease_id)
	{
		$v_data = array('tenant_unit_id'=>$tenant_unit_id,'lease_id'=>$lease_id);
		$v_data['title'] = 'Tenant payments';
		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();
		// get lease payments for this year
		$v_data['lease_payments'] = $this->accounts_model->get_lease_payments($lease_id);

		$data['content'] = $this->load->view('cash_office/payments', $v_data, true);
		
		$data['title'] = 'Tenant payments';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	/*
	*
	*	Add a new tenant page
	*
	*/
	public function add_tenant($rental_unit_id = NULL) 
	{
		//form validation rules
		$this->form_validation->set_rules('tenant_email', 'Email', 'xss_clean|is_unique[tenants.tenant_email]|valid_email');
		$this->form_validation->set_rules('tenant_name', 'Tenant name', 'required|xss_clean');
		$this->form_validation->set_rules('tenant_phone_number', 'Tenant Phone Number', 'required|xss_clean|is_unique[tenants.tenant_phone_number]');
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
				$pwd_update = $this->input->post('admin_tenant');
				if(!empty($pwd_update))
				{
					redirect('admin-profile/'.$tenant_id);
				}
				
				else
				{
					redirect('admin/administrators');
				}
			}
			
			else
			{
				$data['error'] = 'Unable to add tenant. Please try again';
			}
		}
		
		//open the add new tenant page
		$data['title'] = 'Edit administrator';
		$v_data['title'] = $data['title'];
		
		//select the tenant from the database
		$query = $this->tenants_model->get_tenant($tenant_id);
		if ($query->num_rows() > 0)
		{
			$v_data['tenants'] = $query->result();
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
		
		redirect('admin/administrators');
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
		
		redirect('admin/administrators');
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
		
		redirect('admin/administrators');
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