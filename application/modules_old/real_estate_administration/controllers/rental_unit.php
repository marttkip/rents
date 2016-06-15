<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Rental_unit extends admin {
	var $rental_unit_path;
	var $rental_unit_location;
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('admin/users_model');
		$this->load->model('property_model');
		$this->load->model('admin/admin_model');
		$this->load->model('rental_unit_model');		
		$this->load->library('image_lib');
		
		
		//path to image directory
		$this->rental_unit_path = realpath(APPPATH . '../assets/rental_unit');
		$this->rental_unit_location = base_url().'assets/rental_unit/';
	}
    
	/*
	*
	*	Default action is to show all the registered rental_unit
	*
	*/
	public function index($property_id = NULL) 
	{
		if($property_id == NULL)
		{
			$where = 'rental_unit_id > 0 ';
		}
		else
		{
			$where = 'rental_unit_id > 0 AND property_id = '.$property_id;
		}
		
		$rental_unit_search = $this->session->userdata('all_rental_unit_search');
		
		if(!empty($rental_unit_search))
		{
			$where .= $rental_unit_search;	
			
		}
		$table = 'rental_unit';
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'rental-management/rental-units';
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
		$query = $this->rental_unit_model->get_all_rental_units($table, $where, $config["per_page"], $page);
		$property_name = $this->property_model->get_property_name($property_id);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['rental_unit_location'] = $this->rental_unit_location;
		$v_data['title'] = $property_name.'\'s rental units';
			
		$data['content'] = $this->load->view('rental_unit/all_rental_units', $v_data, true);
		
		$data['title'] = 'All rental unit';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function rental_units()
	{
		$where = 'rental_unit_id > 0 AND property.property_id = rental_unit.property_id ';
		$table = 'rental_unit,property';

		$rental_unit_search = $this->session->userdata('all_rental_unit_search');
		
		if(!empty($rental_unit_search))
		{
			$where .= $rental_unit_search;	
			
		}
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'rental-management/rental-units';
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
		$query = $this->rental_unit_model->get_all_rental_units($table, $where, $config["per_page"], $page);

		$properties = $this->property_model->get_active_property();
		$rs8 = $properties->result();
		$property_list = '';
		foreach ($rs8 as $property_rs) :
			$property_id = $property_rs->property_id;
			$property_name = $property_rs->property_name;
			$property_location = $property_rs->property_location;

		    $property_list .="<option value='".$property_id."'>".$property_name." Location: ".$property_location."</option>";

		endforeach;

		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['property_list'] = $property_list;
		$v_data['title'] = 'Rental units';			
		$data['content'] = $this->load->view('rental_unit/rental_units', $v_data, true);
		
		$data['title'] = 'All rental unit';
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_rental_units()
	{
		$unit_name = $this->input->post('unit_name');
				

		if(!empty($unit_name))
		{
			$unit_name = ' AND rental_unit.rental_unit_name  LIKE \'%'.$unit_name.'%\'';
		}
		else
		{
			$unit_name = '';
		}

		$search = $unit_name;

		$rental_unit_search = $this->session->userdata('all_rental_unit_search');
		
		
		$this->session->set_userdata('all_rental_unit_search', $search);
		$this->rental_units();
	}
	public function close_tenants_search()
	{
		$this->session->unset_userdata('all_rental_unit_search');		
		
		redirect('rental-management/rental-units');
	}
	
	function add_rental_unit_old()
	{
		
		$rental_unit_error = $this->session->userdata('rental_unit_error_message');
		
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
				$this->db->insert($table, $data2);
				$this->session->unset_userdata('rental_unit_error_message');
				$this->session->set_userdata('success_message', 'rental_unit has been added');
				
				redirect('real-estate-administration/rental-units');
			}
		}
		$v_data['rental_unit_owners'] = $this->rental_unit_owners_model->get_all_front_end_rental_unit_owners();
		$v_data['title'] = 'Add rental_unit';
		$data['title'] = 'Add rental_unit';
		$data['content'] = $this->load->view("rental_unit/add_rental_unit", $v_data, TRUE);
		
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function add_rental_unit()
	{
		$this->form_validation->set_rules('rental_unit_name', 'Unit name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('property_id', 'Property ', 'required|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if($this->rental_unit_model->add_rental_unit())
			{
				$this->session->set_userdata('success_message', 'The rental unit has been successfully added');
			}
			else
			{
				$this->session->unset_userdata('rental_unit_error_message');
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');

			}	
			
		}else
		{
			$this->session->set_userdata('error_message', 'Make sure you have entered the unit name and selected a property');
		}
		redirect('rental-management/rental-units');
	}

	function edit_rental_unit($rental_unit_id, $page = NULL)
	{
		//get rental_unit data
		$table = "rental_unit,property";
		$where = "property.property_id = rental_unit.property_id AND rental_unit.rental_unit_id = ".$rental_unit_id;
		
		$this->db->where($where);
		$rental_unit_query = $this->db->get($table);
		$v_data['query'] = $rental_unit_query;		
		// $v_data['rental_unit_owners'] = $this->rental_unit_owners_model->get_all_front_end_rental_unit_owners();
		

		
		$this->form_validation->set_rules('rental_unit_name', 'rental_unit name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('property_id', 'Property', 'required|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($rental_unit_error))
			{
		
				$data2 = array(
					'rental_unit_name'=>$this->input->post("rental_unit_name"),
					'rental_unit_status'=>1,
					'property_id'=>$this->input->post("property_id")
				);
				
				$table = "rental_unit";
				$this->db->where('rental_unit_id', $rental_unit_id);
				$this->db->update($table, $data2);
				$this->session->set_userdata('success_message', 'rental unit has been edited');
				
				redirect('rental-management/rental-units/'.$page);
			}
		}
		
		
		
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
		redirect('rental-management/rental-units/'.$page);
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
			redirect('rental-management/rental-units/'.$page);
	}
}
?>