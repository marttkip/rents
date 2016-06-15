<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class leases extends admin {
	var $leases_path;
	var $leases_location;
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('admin/users_model');
		$this->load->model('property_model');
		$this->load->model('admin/admin_model');
		$this->load->model('leases_model');		
		$this->load->library('image_lib');
		
		
		//path to image directory
		$this->leases_path = realpath(APPPATH . '../assets/leases');
		$this->leases_location = base_url().'assets/leases/';
	}
    
	/*
	*
	*	Default action is to show all the registered lease
	*
	*/
	public function index() 
	{
		
		$where = 'leases.lease_id > 0 AND leases.tenant_unit_id = tenant_unit.tenant_unit_id AND tenant_unit.tenant_id = tenants.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id AND rental_unit.property_id = property.property_id ';
		$table = 'leases,rental_unit,tenant_unit,tenants,property';
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'lease-management/leases';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
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
		
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $data["links"] = $this->pagination->create_links();
		$query = $this->leases_model->get_all_leases($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['title'] ='Leases';
			
		$data['content'] = $this->load->view('leases/all_leases', $v_data, true);
		
		$data['title'] = 'All rental unit';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	function add_lease($tenant_id,$rental_unit_id)
	{
		
		$lease_error = $this->session->userdata('lease_error_message');
		
		$this->form_validation->set_rules('lease_start_date', 'lease name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('lease_duration', 'lease duration', 'required|trim|xss_clean');
		$this->form_validation->set_rules('rent_amount', 'Rent amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('deposit_amount', 'lease location', 'required|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($lease_error))
			{
				if($this->leases_model->add_lease($tenant_id,$rental_unit_id))
				{
					$this->session->unset_userdata('lease_error_message');
					$this->session->set_userdata('success_message', 'Lease has been added successfully');
				}
				else
				{
					$this->session->set_userdata('error_message', 'Sorry please check for errors has been added successfully');
				}				
			
			
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
	
	function edit_lease($lease_id, $page = NULL)
	{
		//get lease data
		$table = "rental_unit, rental_unit_owners";
		$where = "rental_unit_owners.rental_unit_owner_id = rental_unit.rental_unit_owner_id AND rental_unit.rental_unit_id = ".$rental_unit_id;
		
		$this->db->where($where);
		$rental_unit_query = $this->db->get($table);
		$rental_unit_row = $rental_unit_query->row();
		$v_data['rental_unit_row'] = $rental_unit_row;		
		$v_data['rental_unit_owners'] = $this->rental_unit_owners_model->get_all_front_end_rental_unit_owners();
		
		$this->form_validation->set_rules('rental_unit_name', 'rental_unit name', 'trim|xss_clean');
		$this->form_validation->set_rules('rental_unit_location', 'rental_unit location', 'trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($rental_unit_error))
			{
		
				$data2 = array(
					'rental_unit_name'=>$this->input->post("rental_unit_name"),
					'rental_unit_location'=>$this->input->post("rental_unit_location"),
					'rental_unit_status'=>1,
					'rental_unit_owner_id'=>$this->input->post("rental_unit_owner_id")
				);
				
				$table = "rental_unit";
				$this->db->where('rental_unit_id', $rental_unit_id);
				$this->db->update($table, $data2);
				$this->session->set_userdata('success_message', 'rental_unit has been edited');
				
				redirect('real-estate-administration/rental-units/'.$page);
			}
		}
		
		$rental_unit = $this->session->userdata('rental_unit_file_name');
		
		
		
		$data['content'] = $this->load->view("rental_unit/edit_rental_unit", $v_data, TRUE);
		$data['title'] = 'Edit rental_unit';
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing rental_unit
	*	@param int $rental_unit_id
	*
	*/
	function delete_rental_unit($rental_unit_id, $page)
	{
		//get rental_unit data
		$table = "rental_unit";
		$where = "rental_unit_id = ".$rental_unit_id;
		
		$this->db->where($where);
		$rental_unit_query = $this->db->get($table);
		$rental_unit_row = $rental_unit_query->row();
		$rental_unit_path = $this->rental_unit_path;
		
		$image_name = $rental_unit_row->rental_unit_image_name;
		
		//delete any other uploaded image
		$this->file_model->delete_file($rental_unit_path."\\".$image_name);
		
		//delete any other uploaded thumbnail
		$this->file_model->delete_file($rental_unit_path."\\thumbnail_".$image_name);
		
		if($this->rental_unit_model->delete_rental_unit($rental_unit_id))
		{
			$this->session->set_userdata('success_message', 'rental_unit has been deleted');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'rental_unit could not be deleted');
		}
		redirect('real-estate-administration/rental-units/'.$page);
	}
    
	/*
	*
	*	Activate an existing rental_unit
	*	@param int $rental_unit_id
	*
	*/
	public function activate_rental_unit($rental_unit_id, $page = NULL)
	{
		if($this->rental_unit_model->activate_rental_unit($rental_unit_id))
		{
			$this->session->set_userdata('success_message', 'rental_unit has been activated');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'rental_unit could not be activated');
		}
		redirect('rental-units/'.$page);
	}
    
	/*
	*
	*	Deactivate an existing rental_unit
	*	@param int $rental_unit_id
	*
	*/
	public function deactivate_rental_unit($rental_unit_id, $page = NULL) 
	{
		if($this->rental_unit_model->deactivate_rental_unit($rental_unit_id))
		{
			$this->session->set_userdata('success_message', 'rental_unit has been disabled');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'rental_unit could not be disabled');
		}
		redirect('rental-units/'.$page);
	}
}
?>