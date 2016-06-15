<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// require_once "./application/modules/real_estate_administration/controllers/property.php";

class Reports extends MX_Controller
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('administration/reports_model');
		$this->load->model('real_estate_administration/property_model');
		$this->load->model('administration/personnel_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/sections_model');
		$this->load->model('accounts/accounts_model');
	}
	
	public function all_reports($module = '__')
	{
		$this->session->unset_userdata('all_transactions_search');
		$this->session->unset_userdata('all_transactions_tables');
		
		$this->session->set_userdata('debtors', 'false2');
		$this->session->set_userdata('page_title', 'All Transactions');
		
		$this->all_transactions($module);
	}
	

	
	public function all_transactions($module = '__')
	{


		$where = 'payments.lease_id = leases.lease_id AND payment_method.payment_method_id = payments.payment_method_id AND leases.tenant_unit_id = tenant_unit.tenant_unit_id AND payments.payment_status = 1 AND tenant_unit.tenant_id = tenants.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id AND rental_unit.property_id = property.property_id';
		$table = 'payments, leases,rental_unit,property,tenant_unit,tenants,payment_method';
		
		$transaction_search = $this->session->userdata('all_transactions_search');
		$table_search = $this->session->userdata('all_transactions_tables');
		$property_search = $this->session->userdata('property_search');
		
		if(!empty($transaction_search))
		{
			$where .= $transaction_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		if($module == '__')
		{
			$segment = 3;
		}
		else
		{
			$segment = 4;	
		}
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'reports/all-transactions/'.$module;
		$config['total_rows'] = $this->reports_model->count_items($table, $where);
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
		$query = $this->reports_model->get_all_transactions($table, $where, $config["per_page"], $page, 'ASC');

		$v_data['branch_name'] = $this->session->userdata('branch_code');
		$v_data['module'] = $module;
		$v_data['query'] = $query;
		$v_data['page'] = $page;



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

		$branches = $this->reports_model->get_all_active_branches();

		$rs9 = $branches->result();
		$branches_list = '';
		foreach($branches->result() as $row):
			$branch_name = $row->branch_name;
			$branch_code = $row->branch_code;

		    $branches_list .="<option value='".$branch_code."'>".$branch_name."</option>";

		endforeach;
		$v_data['branches_list'] = $branches_list;



		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where, $table);
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where, $table);
		$v_data['total_expenses'] = 0;


		$leases_table= 'leases';
		$leases_where = 'lease_status = 1';
		$v_data['active_leases'] = $this->reports_model->count_items($leases_table, $leases_where);

		$inactive_leases_table= 'leases';
		$inactive_leases_where = 'lease_status <> 1';
		$v_data['inactive_leases'] = $this->reports_model->count_items($inactive_leases_table, $inactive_leases_where);



		// $v_data['properties'] = $this->properties_model->get_all_active_branches();
		$v_data['title'] = "All Transactions";
		$data['content'] = $this->load->view('reports/all_transactions', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	
	public function export_transactions()
	{
		$this->reports_model->export_transactions();
	}
	public function export_defaulters()
	{
		$this->reports_model->export_defaulters();
	}
	
	public function close_search()
	{
		$this->session->unset_userdata('all_transactions_search');
		$this->session->unset_userdata('all_transactions_tables');
		$this->session->unset_userdata('all_defaulters_search');
		$this->session->unset_userdata('search_title');
		
		$debtors = $this->session->userdata('debtors');
		
		
		redirect('reports/all-transactions');
	}

	public function close_defaulters_search()
	{
		$this->session->unset_userdata('all_defaulters_search');
		$this->session->unset_userdata('search_title');
		
		$debtors = $this->session->userdata('debtors');

		$this->all_defaulters();
	}
	
	public function all_defaulters($module=NULL)
	{

		$where = 'tenants.tenant_id > 0 AND property.property_id = rental_unit.property_id AND tenants.tenant_id = tenant_unit.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id  AND tenant_unit.tenant_unit_id = leases.tenant_unit_id AND leases.lease_status = 1';
		$table = 'tenants,tenant_unit,rental_unit,leases,property';

		$defaulters_search = $this->session->userdata('all_defaulters_search');
		$table_search = $this->session->userdata('all_transactions_tables');

		if(!empty($defaulters_search))
		{
			$where .= $defaulters_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		
		$segment = 3;	
		
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'reports/all-defaulters';
		$config['total_rows'] = $this->reports_model->count_items($table, $where);
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
		$query = $this->reports_model->get_all_defaulters($table, $where, $config["per_page"], $page);

		$v_data['branch_name'] = $this->session->userdata('branch_code');
		$v_data['module'] = $module;
		$v_data['query'] = $query;
		$v_data['page'] = $page;



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

		$branches = $this->reports_model->get_all_active_branches();

		$rs9 = $branches->result();
		$branches_list = '';
		foreach($branches->result() as $row):
			$branch_name = $row->branch_name;
			$branch_code = $row->branch_code;

		    $branches_list .="<option value='".$branch_code."'>".$branch_name."</option>";

		endforeach;
		$v_data['branches_list'] = $branches_list;

		$v_data['title'] = "Defaulters";
		$data['content'] = $this->load->view('reports/all_defaulters', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	
	public function debtors_report_invoices($visit_type_id, $order = 'debtor_invoice_created', $order_method = 'DESC')
	{
		//get bill to
		$v_data['visit_type_query'] = $this->reports_model->get_visit_type();
		
		//select first debtor from query
		if($visit_type_id == 0)
		{
			if($v_data['visit_type_query']->num_rows() > 0)
			{
				$res = $v_data['visit_type_query']->result();
				$visit_type_id = $res[0]->visit_type_id;
				$visit_type_name = $res[0]->visit_type_name;
			}
		}
		
		else
		{
			if($v_data['visit_type_query']->num_rows() > 0)
			{
				$res = $v_data['visit_type_query']->result();
				
				foreach($res as $r)
				{
					$visit_type_id2 = $r->visit_type_id;
					
					if($visit_type_id == $visit_type_id2)
					{
						$visit_type_name = $r->visit_type_name;
						break;
					}
				}
			}
		}
		
		if($visit_type_id > 0)
		{
			$where = 'debtor_invoice.visit_type_id = '.$visit_type_id;
			$table = 'debtor_invoice';
			
			$visit_search = $this->session->userdata('debtors_invoice_search');
			$table_search = $this->session->userdata('debtors_invoice_tables');
			
			if(!empty($visit_search))
			{
				$where .= $visit_search;
			
				if(!empty($table_search))
				{
					$table .= $table_search;
				}
			}
			
			$segment = 7;
			
			//pagination
			$this->load->library('pagination');
			$config['base_url'] = site_url().'administration/reports/debtors_report_data/'.$visit_type_id.'/'.$order.'/'.$order_method;
			$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
			$query = $this->reports_model->get_all_debtors_invoices($table, $where, $config["per_page"], $page, $order, $order_method);
			
			$where .= ' AND debtor_invoice.debtor_invoice_id = debtor_invoice_item.debtor_invoice_id AND visit.visit_id = debtor_invoice_item.visit_id ';
			$table .= ', visit, debtor_invoice_item';
			$v_data['where'] = $where;
			$v_data['table'] = $table;
			
			if($order_method == 'DESC')
			{
				$order_method = 'ASC';
			}
			else
			{
				$order_method = 'DESC';
			}
			$v_data['total_patients'] = $this->reports_model->get_total_visits($where, $table);
			$v_data['total_services_revenue'] = $this->reports_model->get_total_services_revenue($where, $table);
			$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);
			
			$v_data['order'] = $order;
			$v_data['order_method'] = $order_method;
			$v_data['visit_type_name'] = $visit_type_name;
			$v_data['visit_type_id'] = $visit_type_id;
			$v_data['query'] = $query;
			$v_data['page'] = $page;
			$v_data['search'] = $visit_search;
			
			$data['title'] = $this->session->userdata('page_title');
			$v_data['title'] = $this->session->userdata('page_title');
			$v_data['debtors'] = $this->session->userdata('debtors');
			
			$v_data['services_query'] = $this->reports_model->get_all_active_services();
			$v_data['type'] = $this->reception_model->get_types();
			$v_data['doctors'] = $this->reception_model->get_doctor();
			//$v_data['module'] = $module;
			
			$data['content'] = $this->load->view('reports/debtors_report_invoices', $v_data, true);
		}
		
		else
		{
			$data['title'] = $this->session->userdata('page_title');
			$data['content'] = 'Please add debtors first';
		}
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function create_new_batch($visit_type_id)
	{
		$this->form_validation->set_rules('invoice_date_from', 'Invoice date from', 'required|xss_clean');
		$this->form_validation->set_rules('invoice_date_to', 'Invoice date to', 'required|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->reports_model->add_debtor_invoice($visit_type_id))
			{
				
			}
			
			else
			{
				
			}
		}
		
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
		}
		//echo 'done '.$visit_type_id;
		redirect('accounts/insurance-invoices/'.$visit_type_id);
	}
	
	public function export_debt_transactions($debtor_invoice_id)
	{
		$this->reports_model->export_debt_transactions($debtor_invoice_id);
	}
	
	public function view_invoices($debtor_invoice_id)
	{
		$where = 'debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id.' AND debtor_invoice.visit_type_id = visit_type.visit_type_id';
		$table = 'debtor_invoice, visit_type';
		
		$v_data = array(
			'debtor_invoice_id'=>$debtor_invoice_id,
			'query' => $this->reports_model->get_debtor_invoice($where, $table),
			'debtor_invoice_items' => $this->reports_model->get_debtor_invoice_items($debtor_invoice_id),
			'personnel_query' => $this->personnel_model->get_all_personnel()
		);
			
		$where .= ' AND debtor_invoice.debtor_invoice_id = debtor_invoice_item.debtor_invoice_id AND visit.visit_id = debtor_invoice_item.visit_id ';
		$table .= ', visit, debtor_invoice_item';
		
		$v_data['where'] = $where;
		$v_data['table'] = $table;
			
		$data['title'] = $v_data['title'] = 'Debtors Invoice';
		
		$data['content'] = $this->load->view('reports/view_invoices', $v_data, TRUE);
		$this->load->view('admin/templates/general_page', $data);
	}

	public function activate_debtor_invoice_item($debtor_invoice_item_id, $debtor_invoice_id)
	{
		$visit_data = array('debtor_invoice_item_status'=>0);
		$this->db->where('debtor_invoice_item_id',$debtor_invoice_item_id);
		if($this->db->update('debtor_invoice_item', $visit_data))
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
		else
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
	}
	
	public function deactivate_debtor_invoice_item($debtor_invoice_item_id, $debtor_invoice_id)
	{
		$visit_data = array('debtor_invoice_item_status'=>1);
		$this->db->where('debtor_invoice_item_id',$debtor_invoice_item_id);
		if($this->db->update('debtor_invoice_item', $visit_data))
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
		else
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
	}
	
	public function invoice($debtor_invoice_id)
	{
		$where = 'debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id.' AND debtor_invoice.visit_type_id = visit_type.visit_type_id';
		$table = 'debtor_invoice, visit_type';
		
		$data = array(
			'debtor_invoice_id'=>$debtor_invoice_id,
			'query' => $this->reports_model->get_debtor_invoice($where, $table),
			'debtor_invoice_items' => $this->reports_model->get_debtor_invoice_items($debtor_invoice_id),
			'personnel_query' => $this->personnel_model->get_all_personnel()
		);
			
		$where .= ' AND debtor_invoice.debtor_invoice_id = debtor_invoice_item.debtor_invoice_id AND visit.visit_id = debtor_invoice_item.visit_id ';
		$table .= ', visit, debtor_invoice_item';
		
		$data['where'] = $where;
		$data['table'] = $table;
		$data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('reports/invoice', $data);
	}
	
	public function search_debtors($visit_type_id)
	{
		$_SESSION['all_transactions_search'] = NULL;
		$_SESSION['all_transactions_tables'] = NULL;
		
		$this->session->unset_userdata('search_title');
		
		$date_from = $this->input->post('batch_date_from');
		$date_to = $this->input->post('batch_date_to');
		$batch_no = $this->input->post('batch_no');
		
		if(!empty($batch_no) && !empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\' AND debtor_invoice.debtor_invoice_created >= \''.$date_from.'\' AND debtor_invoice.debtor_invoice_created <= \''.$date_to.'\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no.' created between '.date('jS M Y',strtotime($date_from)).' and '.date('jS M Y',strtotime($date_to));
		}
		
		else if(!empty($batch_no) && !empty($date_from) && empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_from.'%\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no.' created on '.date('jS M Y',strtotime($date_from));
		}
		
		else if(!empty($batch_no) && empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_to.'%\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no.' created on '.date('jS M Y',strtotime($date_to));
		}
		
		else if(empty($batch_no) && !empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.debtor_invoice_created >= \''.$date_from.'\' AND debtor_invoice.debtor_invoice_created <= \''.$date_to.'\'';
			$search_title = 'Showing invoices created between '.date('jS M Y',strtotime($date_from)).' and '.date('jS M Y',strtotime($date_to));
		}
		
		else if(empty($batch_no) && !empty($date_from) && empty($date_to))
		{
			$search = ' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_from.'%\'';
			$search_title = 'Showing invoices created created on '.date('jS M Y',strtotime($date_from));
		}
		
		else if(empty($batch_no) && empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_to.'%\'';
			$search_title = 'Showing invoices created created on '.date('jS M Y',strtotime($date_to));
		}
		else if(!empty($batch_no) && empty($date_from) && empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no;
		}
		
		else
		{
			$search = '';
			$search_title = '';
		}
		
		
		$_SESSION['all_transactions_search'] = $search;
		
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('administration/reports/debtors_report_data/'.$visit_type_id);
	}
	
	public function search_transactions($module = NULL)
	{
		$property_id = $this->input->post('property_id');
		$payment_date_from = $this->input->post('payment_date_from');
		$payment_date_to = $this->input->post('payment_date_to');
		$branch_code = $this->input->post('branch_code');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($property_id))
		{
			$property_id = ' AND property.property_id = '.$property_id.' ';
			
			$this->db->where('property_id', $property_id);
			$query = $this->db->get('property');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->property_name.' ';
			}
		}
		
		if(!empty($payment_date_from) && !empty($payment_date_to))
		{
			$payment_date = ' AND payments.payment_date BETWEEN \''.$payment_date_from.'\' AND \''.$payment_date_to.'\'';
			$search_title .= 'payment date from '.date('jS M Y', strtotime($payment_date_from)).' to '.date('jS M Y', strtotime($payment_date_to)).' ';
		}
		
		else if(!empty($payment_date_from))
		{
			$payment_date = ' AND payments.payment_date = \''.$payment_date_from.'\'';
			$search_title .= 'payment date of '.date('jS M Y', strtotime($payment_date_from)).' ';
		}
		
		else if(!empty($payment_date_to))
		{
			$payment_date = ' AND payments.payment_date = \''.$payment_date_to.'\'';
			$search_title .= 'payment date of '.date('jS M Y', strtotime($payment_date_to)).' ';
		}
		
		else
		{
			$payment_date = '';
		}

		$search = $property_id.$payment_date;

		$property_search = $property_id;

		$transactions_search = $this->session->userdata('all_transactions_search');
		
		
		$this->session->set_userdata('all_transactions_search', $transactions_search);
		$this->session->set_userdata('property_search', $property_search);
		$this->session->set_userdata('search_title', $search_title);
		
		$this->all_transactions($module);
	}


	public function search_defaulters($module = NULL)
	{
		$property_id = $this->input->post('property_id');
		$payment_date_from = $this->input->post('payment_date_from');
		$payment_date_to = $this->input->post('payment_date_to');
		$branch_code = $this->input->post('branch_code');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($property_id))
		{
			$property_id = ' AND property.property_id = '.$property_id.' ';
			
			$this->db->where('property_id', $property_id);
			$query = $this->db->get('property');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->property_name.' ';
			}
		}
		
		if(!empty($payment_date_from) && !empty($payment_date_to))
		{
			$payment_date = ' AND payments.payment_date BETWEEN \''.$payment_date_from.'\' AND \''.$payment_date_to.'\'';
			$search_title .= 'payment date from '.date('jS M Y', strtotime($payment_date_from)).' to '.date('jS M Y', strtotime($payment_date_to)).' ';
		}
		
		else if(!empty($payment_date_from))
		{
			$payment_date = ' AND payments.payment_date = \''.$payment_date_from.'\'';
			$search_title .= 'payment date of '.date('jS M Y', strtotime($payment_date_from)).' ';
		}
		
		else if(!empty($payment_date_to))
		{
			$payment_date = ' AND payments.payment_date = \''.$payment_date_to.'\'';
			$search_title .= 'payment date of '.date('jS M Y', strtotime($payment_date_to)).' ';
		}
		
		else
		{
			$payment_date = '';
		}

		$search = $property_id.$payment_date;

		$property_search = $property_id;

		$defaulters_search = $this->session->userdata('all_defaulters_search');
		
		if(!empty($defaulters_search))
		{
			$search .= $defaulters_search;
		}
		$this->session->set_userdata('all_defaulters_search', $search);
		$this->session->set_userdata('property_search', $property_search);
		$this->session->set_userdata('search_title', $search_title);
		
		$this->all_defaulters($module);
	}
	public function close_debtors_search($visit_type_id)
	{
		$_SESSION['all_transactions_search'] = NULL;
		$_SESSION['all_transactions_tables'] = NULL;
		
		$this->session->unset_userdata('search_title');
		redirect('administration/reports/debtors_report_data/'.$visit_type_id);
	}
	
	public function cash_report()
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id AND payments.payment_type = 1 AND visit.visit_delete = 0 AND visit.branch_code = \''.$branch_code.'\' AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0';
		
		$table = 'payments, visit, patients, visit_type, payment_method';
		$visit_search = $this->session->userdata('cash_report_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/cash-report';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
		$query = $this->reports_model->get_all_payments($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table, 'cash');
		
		//all normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where2, $table, 'cash');
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where2, $table, 'cash');
		
		//normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['total_cash_collection'] = $this->reports_model->get_total_cash_collection($where2, $table, 'cash');
		
		//count outpatient visits
		$where2 = $where.' AND patients.inpatient = 0';
		$v_data['outpatients'] = $this->reception_model->count_items($table, $where2);
		
		//count inpatient visits
		$where2 = $where.' AND patients.inpatient = 1';
		$v_data['inpatients'] = $this->reception_model->count_items($table, $where2);
		
		$page_title = $this->session->userdata('cash_search_title');
		
		if(empty($page_title))
		{
			$page_title = 'Cash report';
		}
		
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('reports/cash_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	
	public function close_cash_search()
	{
		$this->session->unset_userdata('cash_report_search');
		$this->session->unset_userdata('cash_search_title');
		
		redirect('hospital-reports/cash-report');
	}
	
	public function export_cash_report()
	{
		$this->reports_model->export_cash_report();
	}
	
	public function select_debtor()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		
		redirect('accounts/insurance-invoices/'.$visit_type_id);
	}
}
?>