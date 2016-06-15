<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Points_category extends admin {

	function __construct()
	{
		parent:: __construct();
		$this->load->model('admin/users_model');
		$this->load->model('admin/admin_model');
		$this->load->model('real_estate_administration/points_category_model');	
		$this->load->model('administration/reports_model');	
		$this->load->library('image_lib');
	}
    
	/*
	*
	*	Default action is to show all the registered points_category
	*
	*/
	public function index() 
	{
		$where = 'points_category_id > 0';
		$table = 'points_category';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'real-estate-administration/points-categories';
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
		$query = $this->points_category_model->get_all_properties($table, $where, $config["per_page"], $page);
		
		
			$v_data['query'] = $query;
			$v_data['page'] = $page;
			$v_data['title'] = 'All points_category';
			
			$data['content'] = $this->load->view('points_category/all_points_categories', $v_data, true);
		
		$data['title'] = 'All points_category';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	
	function add_points_category()
	{
		
		$points_category_error = $this->session->userdata('points_category_error_message');
		
		$this->form_validation->set_rules('points_category_date_from', 'points category date from', 'required|xss_clean|trim|xss_clean');
		$this->form_validation->set_rules('points_category_date_to', 'points category date to', 'required|xss_clean|trim|xss_clean');
		$this->form_validation->set_rules('points', 'Points', 'required|xss_clean|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($points_category_error))
			{
				$data2 = array(
					'points_category_date_from'=>$this->input->post("points_category_date_from"),
					'points_category_date_to'=>$this->input->post("points_category_date_to"),
					'points_category_status'=>1,
					'points'=>$this->input->post("points")

				);
				
				//  add points_category 
				$table = "points_category";
				$this->db->insert($table, $data2);

				$points_category_id = $this->db->insert_id();

				// $this->create_points_category_units($points_category_id,$this->input->post("total_units"));

				$this->session->unset_userdata('points_category_error_message');
				$this->session->set_userdata('success_message', 'points_category has been added');
				
				redirect('real-estate-administration/points-categories');
			}
		}
		$v_data['title'] = 'Add points category';
		$data['title'] = 'Add points category';
		$data['content'] = $this->load->view("points_category/add_points_category", $v_data, TRUE);
		
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	function edit_points_category($points_category_id, $page = NULL)
	{
		//get points_category data
		$table = "points_category";
		$where = "points_category.points_category_id = ".$points_category_id;
		
		$this->db->where($where);
		$points_category_query = $this->db->get($table);
		$points_category_row = $points_category_query->row();
		$v_data['points_category_row'] = $points_category_row;		
		
		$this->form_validation->set_rules('points_category_date_from', 'points category date from', 'required|xss_clean|trim|xss_clean');
		$this->form_validation->set_rules('points_category_date_to', 'points category date to', 'required|xss_clean|trim|xss_clean');
		$this->form_validation->set_rules('points', 'Points', 'required|xss_clean|trim|xss_clean');

		if ($this->form_validation->run())
		{	
			if(empty($points_category_error))
			{
		
				$data2 = array(
					'points_category_date_from'=>$this->input->post("points_category_date_from"),
					'points_category_date_to'=>$this->input->post("points_category_date_to"),
					'points_category_status'=>1,
					'points'=>$this->input->post("points")

				);
				
				$table = "points_category";
				$this->db->where('points_category_id', $points_category_id);
				$this->db->update($table, $data2);
				$this->session->set_userdata('success_message', 'points_category has been edited');
				
				redirect('real-estate-administration/points-categories/'.$page);
			}
		}
		
		
		$v_data['title'] = 'Edit points category';
		
		$data['content'] = $this->load->view("points_category/edit_points_category", $v_data, TRUE);
		$data['title'] = 'Edit points category';
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing points_category
	*	@param int $points_category_id
	*
	*/
	function delete_points_category($points_category_id, $page)
	{
		//get points_category data
		$table = "points_category";
		$where = "points_category_id = ".$points_category_id;
		
		$this->db->where($where);
		$points_category_query = $this->db->get($table);
		$points_category_row = $points_category_query->row();
		
		if($this->points_category_model->delete_points_category($points_category_id))
		{
			$this->session->set_userdata('success_message', 'points_category has been deleted');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'points_category could not be deleted');
		}
		redirect('real-estate-administration/properties/'.$page);
	}
    
	/*
	*
	*	Activate an existing points_category
	*	@param int $points_category_id
	*
	*/
	public function activate_points_category($points_category_id, $page = NULL)
	{
		if($this->points_category_model->activate_points_category($points_category_id))
		{
			$this->session->set_userdata('success_message', 'points_category has been activated');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'points_category could not be activated');
		}
		redirect('real-estate-administration/points-categories/'.$page);
	}
    
	/*
	*
	*	Deactivate an existing points_category
	*	@param int $points_category_id
	*
	*/
	public function deactivate_points_category($points_category_id, $page = NULL) 
	{
		if($this->points_category_model->deactivate_points_category($points_category_id))
		{
			$this->session->set_userdata('success_message', 'points_category has been disabled');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'points_category could not be disabled');
		}
		redirect('real-estate-administration/points-categories/'.$page);
	}
}
?>