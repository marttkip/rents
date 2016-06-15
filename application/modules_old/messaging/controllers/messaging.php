<?php
class Messaging extends MX_Controller 
{
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('messaging_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/users_model');
		$this->load->model('administration/personnel_model');

		$this->csv_path = realpath(APPPATH . '../assets/csv');
	}
	
	public function index()
	{
		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
		
		else
		{
			redirect('message/dashboard');
		}
	}

	public function unsent_messages()
	{

		$where = 'messaging.message_category_id = message_category.message_category_id AND messaging.sent_status = 0 AND messaging.branch_code = "'. $this->session->userdata('branch_code').'"';
		$table = 'messaging, message_category';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'messaging/unsent-messages';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
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

		$query = $this->messaging_model->get_all_messages($table, $where, $config["per_page"], $page);
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['page'] = $page;
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('sms/unsent_messages', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function sent_messages()
	{

		$where = 'messaging.message_category_id = message_category.message_category_id AND messaging.sent_status = 1 AND messaging.branch_code = "'. $this->session->userdata('branch_code').'"';
		$table = 'messaging, message_category';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'messaging/unsent-messages';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
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

		$query = $this->messaging_model->get_all_messages($table, $where, $config["per_page"], $page);
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['page'] = $page;
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('sms/sent_messages', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function import_template()
	{
		$this->messaging_model->import_template();
	}
	function do_messages_import($message_category_id)
	{

		if(isset($_FILES['import_csv']))
		{
			// var_dump($message_category_id); die();
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 

				$response = $this->messaging_model->import_csv_charges($this->csv_path, $message_category_id);
				
				
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
		redirect('messaging/unsent-messages');
	}
	public function spoilt_messages()
	{

		$where = 'messaging.message_category_id = message_category.message_category_id AND messaging.sent_status = 2 AND messaging.branch_code = "'. $this->session->userdata('branch_code').'"';
		$table = 'messaging, message_category';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'all-posts';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
		$config['uri_segment'] = 2;
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
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["links"] = $this->pagination->create_links();
		$query = $this->messaging_model->get_all_messages($table, $where, $config["per_page"], $page);
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('sms/sent_messages', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function send_messages()
	{
		$this->messaging_model->send_unsent_messages();

		redirect('messaging/unsent-messages');
	}


}