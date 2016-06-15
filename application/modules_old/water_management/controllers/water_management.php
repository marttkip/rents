<?php
class Water_management extends MX_Controller 
{
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('water_management_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/users_model');
		$this->load->model('real_estate_administration/property_model');
		$this->load->model('administration/personnel_model');

		$this->csv_path = realpath(APPPATH . '../assets/csv');
	}
	
	public function index()
	{
		$where = 'property.property_id = water_management.property_id';
		$table = 'water_management,property';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'water-management/property-readings';
		$config['total_rows'] = $this->water_management_model->count_items($table, $where);
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
        $v_data["links"] = $this->pagination->create_links();

		$query = $this->water_management_model->get_all_water_records($table, $where, $config["per_page"], $page);

		$data['title'] = $this->site_model->display_page_title();
		$v_data['properties'] = $this->property_model->get_active_property();
		$v_data['title'] = $data['title'];
		$v_data['page'] = $page;
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('water_records', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);	
	}

	
	public function import_water_readings_template()
	{
		$this->water_management_model->import_template();
	}
	function do_water_readings_import()
	{

		if(isset($_FILES['import_csv']))
		{
			// var_dump($message_category_id); die();
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 

				$response = $this->water_management_model->import_csv_charges($this->csv_path);
				
				
				if($response == FALSE)
				{

				}
				
				else
				{
					if($response['check'])
					{
						$v_data['import_response'] = $response['response'];
					}
					
					else
					{
						$v_data['import_response_error'] = $response['response'];
					}
				}
			}
			
			else
			{
				$v_data['import_response_error'] = 'Please select a file to import.';
			}
		}
		
		else
		{
			$v_data['import_response_error'] = 'Please select a file to import.';
		}
		redirect('water-management/property-readings');
	}
	
	public function print_water_readings($document_number)
	{
		$data['document_number'] = $document_number;
		$data['document_details'] = $this->water_management_model->get_document_details($document_number);
		$data['contacts'] = $this->site_model->get_contacts();
		$this->load->view('reading_reports', $data);
	}


}
?>