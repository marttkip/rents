<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Property extends admin {
	var $property_path;
	var $property_location;
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('admin/users_model');
		$this->load->model('real_estate_administration/property_owners_model');
		$this->load->model('admin/admin_model');
		$this->load->model('real_estate_administration/property_model');	
		$this->load->model('administration/reports_model');	
		$this->load->library('image_lib');
		
		
		//path to image directory
		$this->property_path = realpath(APPPATH . '../assets/property');
		$this->property_location = base_url().'assets/property/';
	}
    
	/*
	*
	*	Default action is to show all the registered property
	*
	*/
	public function index() 
	{
		$where = 'property_id > 0';
		$table = 'property';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'real-estate-administration/properties';
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
		$query = $this->property_model->get_all_properties($table, $where, $config["per_page"], $page);
		
		
			$v_data['query'] = $query;
			$v_data['page'] = $page;
			$v_data['property_location'] = $this->property_location;
			$v_data['title'] = 'All property';
			
			$data['content'] = $this->load->view('property/all_properties', $v_data, true);
		
		$data['title'] = 'All property';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	
	function add_property()
	{
		
		$property_error = $this->session->userdata('property_error_message');
		
		$this->form_validation->set_rules('property_name', 'property name', 'required|xss_clean|trim|xss_clean');
		$this->form_validation->set_rules('property_prefix', 'property name', 'required|xss_clean|is_unique[property.property_prefix]|trim|xss_clean');
		$this->form_validation->set_rules('property_location', 'property location', 'required|xss_clean|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($property_error))
			{
				$data2 = array(
					'property_name'=>$this->input->post("property_name"),
					'property_location'=>$this->input->post("property_location"),
					'property_prefix'=>ucwords(strtoupper($this->input->post('property_prefix'))),
					'property_status'=>1,
					'property_owner_id'=>$this->input->post("property_owner_id"),
					'total_units'=>$this->input->post("total_units")

				);
				
				//  add property 
				$table = "property";
				$this->db->insert($table, $data2);

				$property_id = $this->db->insert_id();

				// $this->create_property_units($property_id,$this->input->post("total_units"));

				$this->session->unset_userdata('property_error_message');
				$this->session->set_userdata('success_message', 'property has been added');
				
				redirect('real-estate-administration/properties');
			}
		}
		$v_data['property_owners'] = $this->property_owners_model->get_all_front_end_property_owners();
		$v_data['title'] = 'Add property';
		$data['title'] = 'Add property';
		$data['content'] = $this->load->view("property/add_property", $v_data, TRUE);
		
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function create_property_units($property_id,$total_number)
	{
		$this->db->where('property_id = '.$property_id);
		$this->db->from('property');
		$this->db->select('property_prefix');
		$pro_query = $this->db->get();
		if($pro_query->num_rows() > 0)
		{
			$result = $pro_query->result();
			$property_prefix =  $result[0]->property_prefix;
		}

		//select product code
		$this->db->where('property_id = '.$property_id);
		$this->db->from('rental_unit');
		$this->db->select('MAX(rental_unit_name) AS number, COUNT(rental_unit_id) AS total_units');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$total_units =  $result[0]->total_units;

			if($total_units < $total_number)
			{
				$number++;//go to the next number
				for ($i=0; $i < $total_number; $i++) { 
					# code...
					$number = "".$property_prefix."-001";
				    $number++;
					$data2 = array(
						'rental_unit_name'=>$number,
						'rental_unit_status'=>1,
						'property_id'=>$property_id
					); 
					$table = "rental_unit";
					$this->db->insert($table, $data2);

				}
			}
			
		}
		else{//start generating receipt numbers
			 $number = 0;
			for ($i=$number; $i < $total_number; $i++) { 
				# code...
				if($i == 0)
				{
					$number = "".$property_prefix."-001";
				}
				else
				{
					$this->db->where('property_id = '.$property_id);
					$this->db->from('rental_unit');
					$this->db->select('MAX(rental_unit_name) AS number, COUNT(rental_unit_id) AS total_units');
					$query = $this->db->get();
					if($query->num_rows() > 0)
					{
						$result = $query->result();
						$number =  $result[0]->number;
					}
					$number++;
				}
				$data2 = array(
					'rental_unit_name'=>$number,
					'rental_unit_status'=>1,
					'property_id'=>$property_id
				); 
				$table = "rental_unit";
				$this->db->insert($table, $data2);
			}
		}
		return TRUE;

	}
	function edit_property($property_id, $page = NULL)
	{
		//get property data
		$table = "property, property_owners";
		$where = "property_owners.property_owner_id = property.property_owner_id AND property.property_id = ".$property_id;
		
		$this->db->where($where);
		$property_query = $this->db->get($table);
		$property_row = $property_query->row();
		$v_data['property_row'] = $property_row;		
		$v_data['property_owners'] = $this->property_owners_model->get_all_front_end_property_owners();
		
		$this->form_validation->set_rules('property_name', 'property name', 'trim|xss_clean');
		$this->form_validation->set_rules('property_location', 'property location', 'trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($property_error))
			{
		
				$data2 = array(
					'property_name'=>$this->input->post("property_name"),
					'property_location'=>$this->input->post("property_location"),
					'property_status'=>1,
					'property_owner_id'=>$this->input->post("property_owner_id")
				);
				
				$table = "property";
				$this->db->where('property_id', $property_id);
				$this->db->update($table, $data2);
				$this->session->set_userdata('success_message', 'property has been edited');
				
				redirect('real-estate-administration/properties/'.$page);
			}
		}
		
		$property = $this->session->userdata('property_file_name');
		
		
		
		$data['content'] = $this->load->view("property/edit_property", $v_data, TRUE);
		$data['title'] = 'Edit property';
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing property
	*	@param int $property_id
	*
	*/
	function delete_property($property_id, $page)
	{
		//get property data
		$table = "property";
		$where = "property_id = ".$property_id;
		
		$this->db->where($where);
		$property_query = $this->db->get($table);
		$property_row = $property_query->row();
		$property_path = $this->property_path;
		
		$image_name = $property_row->property_image_name;
		
		//delete any other uploaded image
		$this->file_model->delete_file($property_path."\\".$image_name);
		
		//delete any other uploaded thumbnail
		$this->file_model->delete_file($property_path."\\thumbnail_".$image_name);
		
		if($this->property_model->delete_property($property_id))
		{
			$this->session->set_userdata('success_message', 'property has been deleted');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'property could not be deleted');
		}
		redirect('real-estate-administration/properties/'.$page);
	}
    
	/*
	*
	*	Activate an existing property
	*	@param int $property_id
	*
	*/
	public function activate_property($property_id, $page = NULL)
	{
		if($this->property_model->activate_property($property_id))
		{
			$this->session->set_userdata('success_message', 'property has been activated');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'property could not be activated');
		}
		redirect('real-estate-administration/properties/'.$page);
	}
    
	/*
	*
	*	Deactivate an existing property
	*	@param int $property_id
	*
	*/
	public function deactivate_property($property_id, $page = NULL) 
	{
		if($this->property_model->deactivate_property($property_id))
		{
			$this->session->set_userdata('success_message', 'property has been disabled');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'property could not be disabled');
		}
		redirect('real-estate-administration/properties/'.$page);
	}
}
?>