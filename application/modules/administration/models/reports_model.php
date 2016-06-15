<?php

class Reports_model extends CI_Model 
{
	/*
	*	Count all items from a table
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function count_items($table, $where, $limit = NULL)
	{
		if($limit != NULL)
		{
			$this->db->limit($limit);
		}
		$this->db->from($table);
		$this->db->where($where);
		return $this->db->count_all_results();
	}
	
	public function get_queue_total($date = NULL, $where = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		if($where == NULL)
		{
			$where = 'visit.visit_id = visit_department.visit_id AND visit.close_card = 0 AND visit.visit_date = \''.$date.'\'';
		}
		
		else
		{
			$where .= ' AND visit.visit_id = visit_department.visit_id AND visit.close_card = 0 AND visit.visit_date = \''.$date.'\' ';
		}
		
		$this->db->select('COUNT(visit.visit_id) AS queue_total');
		$this->db->where($where);
		$query = $this->db->get('visit, visit_department');
		
		$result = $query->row();
		
		return $result->queue_total;
	}
	
	public function get_daily_balance($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		//select the user by email from the database
		$this->db->select('SUM(amount_paid) AS total_amount');
		$this->db->where('payment_type = 1 AND payment_method_id = 2 AND payment_created = \''.$date.'\'');
		$this->db->from('payments');
		$query = $this->db->get();
		
		$result = $query->row();
		
		return $result->total_amount;
	}
	
	public function get_patients_total($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$this->db->select('COUNT(visit_id) AS patients_total');
		$this->db->where('visit_date = \''.$date.'\'');
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->patients_total;
	}
	
	public function get_all_payment_methods()
	{
		$this->db->select('*');
		$query = $this->db->get('payment_method');
		
		return $query;
	}
	
	public function get_payment_method_total($payment_method_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$this->db->select('SUM(amount_paid) AS total_paid');
		$this->db->where('payments.visit_id = visit.visit_id AND payment_method_id = '.$payment_method_id.' AND visit_date = \''.$date.'\'');
		$query = $this->db->get('payments, visit');
		
		$result = $query->row();
		
		return $result->total_paid;
	}
	
	public function get_all_visit_types()
	{
		$this->db->select('*');
		$query = $this->db->get('visit_type');
		
		return $query;
	}
	
	public function get_visit_type_total($visit_type_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit_date = \''.$date.'\' AND visit_type = '.$visit_type_id;
		
		$this->db->select('COUNT(visit_id) AS visit_total');
		$this->db->where($where);
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->visit_total;
	}
	
	public function get_patient_type_total($patient_type_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit_date = \''.$date.'\' AND visit.inpatient = '.$patient_type_id;
		
		$this->db->select('COUNT(visit_id) AS visit_total');
		$this->db->where($where);
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->visit_total;
	}
	public function get_all_defaulters($table, $where, $per_page, $page, $order = 'property.property_name,rental_unit.rental_unit_name', $order_method = 'ASC')
	{
		//retrieve all leases
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function get_all_service_types()
	{
		$this->db->select('*');
		$this->db->where('service_delete', 0);
		$query = $this->db->get('service');
		
		return $query;
	}
	
	public function get_service_total($service_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'visit_charge, service_charge';
		
		$where = 'visit_charge_timestamp LIKE \''.$date.'%\' AND visit_charge.service_charge_id = service_charge.service_charge_id AND service_charge.service_id = '.$service_id;
		
		$visit_search = $this->session->userdata('all_departments_search');
		if(!empty($visit_search))
		{
			$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND service_charge.service_id = '.$service_id.' AND visit.visit_id = visit_charge.visit_id'. $visit_search;
			$table .= ', visit';
		}
		
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->service_total;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}
	
	public function get_all_appointments($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit.visit_delete = 0 AND patients.patient_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit.patient_id = patients.patient_id AND visit.appointment_id = 1 AND visit.close_card = 2 AND visit.visit_date >= \''.$date.'\' AND visit.personnel_id = personnel.personnel_id';
		
		$this->db->select('visit.*, visit_type.visit_type_name, patients.*, personnel.*');
		$this->db->where($where);
		$query = $this->db->get('visit, visit_type, patients, personnel');
		
		return $query;
	}
	
	public function get_doctor_appointments($personnel_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit.visit_delete = 0 AND patients.patient_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit.patient_id = patients.patient_id AND visit.appointment_id = 1 AND visit.close_card = 2 AND visit.visit_date >= \''.$date.'\' AND visit.personnel_id = '.$personnel_id;
		
		$this->db->select('visit.*, visit_type.visit_type_name, patients.*');
		$this->db->where($where);
		$query = $this->db->get('visit, visit_type, patients');
		
		return $query;
	}
	
	public function get_all_sessions($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'personnel.personnel_id = session.personnel_id AND session.session_name_id = session_name.session_name_id AND session_time LIKE \''.$date.'%\'';
		
		$this->db->select('session_name_name, session_time, personnel_fname, personnel_onames');
		$this->db->where($where);
		$this->db->order_by('session_time', 'DESC');
		$query = $this->db->get('session, session_name, personnel');
		
		return $query;
	}
	
	/*
	*	Retrieve visits
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_transactions($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('payments.*,payment_method.payment_method AS payment_method, tenants.*, rental_unit.rental_unit_name,property.property_name, leases.*');
		$this->db->where($where);
		$this->db->order_by('payments.payment_date, payments.receipt_number','DESC');
		// $this->db->group_by('visit.visit_id');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve all active services
	*
	*/
	public function get_all_active_services()
	{
		//retrieve all users
		$this->db->from('service');
		$this->db->where('service_delete = 0');
		$this->db->order_by('service_name','ASC');
		$query = $this->db->get();
		
		return $query;
	}
	

	/*
	*	Retrieve all active services
	*
	*/
	public function get_all_active_payment_method()
	{
		//retrieve all users
		$this->db->from('payment_method');
		$this->db->where('payment_method_id > 0');
		$this->db->order_by('payment_method_id','ASC');
		$query = $this->db->get();
		
		return $query;
	}
	
	
	/*
	*	Retrieve all visit payments
	*
	*/
	public function get_all_visit_payments($visit_id)
	{
		//retrieve all users
		$this->db->from('payments');
		$this->db->select('SUM(payments.amount_paid) AS total_paid');
		$this->db->where('visit_id', $visit_id);
		// $this->db->group_by('visit_id');
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_paid > 0)
		{
			return $cash->total_paid;
		}
		
		else
		{
			return 0;
		}
	}
	
	/*
	*	Retrieve all service charges
	*
	*/
	public function get_all_visit_charges($visit_id, $service_id)
	{
		//retrieve all users
		$this->db->from('visit_charge, service_charge');
		$this->db->select('SUM(visit_charge.visit_charge_amount * visit_charge.visit_charge_units) AS total_invoiced');
		$this->db->where('visit_charge.visit_id = '.$visit_id.' AND service_charge.service_id = '.$service_id.' AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_charge_delete = 0');
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_invoiced > 0)
		{
			return $cash->total_invoiced;
		}
		
		else
		{
			return 0;
		}
	}
	
	public function get_service_notes($visit_id, $service_id, $payment_type)
	{
		//retrieve all users
		$this->db->from('payments');
		$this->db->select('SUM(amount_paid) AS total_invoiced');
		$this->db->where('payments.visit_id = '.$visit_id.' AND payments.payment_service_id = '.$service_id.' AND payments.payment_type = '.$payment_type);
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_invoiced > 0)
		{
			return $cash->total_invoiced;
		}
		
		else
		{
			return 0;
		}
	}
	
	public function get_all_payment_values($visit_id,$payment_method_id)
	{
		# code...
		//retrieve all users
		$this->db->from('payments');
		$this->db->select('SUM(amount_paid) AS total_paid');
		$this->db->where('payments.cancel = 0 AND visit_id = '.$visit_id.' AND payment_method_id = '.$payment_method_id.' AND payment_type = 1');
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_paid > 0)
		{
			return $cash->total_paid;
		}
		
		else
		{
			return 0;
		}
	}
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_total_services_revenue($where, $table)
	{
		//invoiced
		$this->db->from($table.', visit_charge, service_charge');
		$this->db->select('SUM(visit_charge.visit_charge_amount * visit_charge.visit_charge_units) AS total_invoiced');
		$this->db->where($where.' AND visit.visit_id = visit_charge.visit_id AND visit_charge.service_charge_id = service_charge.service_charge_id');
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_invoiced = $cash->total_invoiced;
		
		if($total_invoiced > 0)
		{
			
		}
		
		else
		{
			$total_invoiced = 0;
		}
		
		return $total_invoiced;
	}
	
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_total_cash_collection($where, $table, $page = NULL)
	{
		//payments
		$table_search = $this->session->userdata('all_transactions_tables');
		
		if($page != 'cash')
		{
			$where .= ' AND payments.cancel = 0';
		}
		if((!empty($table_search)) || ($page == 'cash'))
		{
			$this->db->from($table);
		}
		
		else
		{
			$this->db->from($table);
		}
		$this->db->select('SUM(payments.amount_paid) AS total_paid');
		$this->db->where($where);
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_paid = $cash->total_paid;
		if($total_paid > 0)
		{
		}
		
		else
		{
			$total_paid = 0;
		}
		
		return $total_paid;
	}
	
	/*
	*	Retrieve a single lease
	*	@param int $lease_id
	*
	*/
	public function get_all_active_lease($lease_id)
	{
		//retrieve all leases
		$this->db->from('leases');
		$this->db->select('*');
		$this->db->where('lease_id = '.$lease_id);
		$query = $this->db->get();
		
		return $query;
	}
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_normal_payments($where, $table, $page = NULL)
	{
		if($page != 'cash')
		{
			$where .= ' AND payments.cancel = 0';
		}
		//payments
		$table_search = $this->session->userdata('all_transactions_tables');
		if((!empty($table_search)) || ($page == 'cash'))
		{
			$this->db->from($table);
		}
		
		else
		{
			$this->db->from($table);
		}
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get();
		
		return $query;
	}
	
	public function get_payment_methods()
	{
		$this->db->select('*');
		$query = $this->db->get('payment_method');
		
		return $query;
	}
	
	/*
	*	Export Transactions
	*
	*/
	function export_transactions()
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'payments.lease_id = leases.lease_id AND payment_method.payment_method_id = payments.payment_method_id AND leases.tenant_unit_id = tenant_unit.tenant_unit_id AND payments.payment_status = 1 AND tenant_unit.tenant_id = tenants.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id AND rental_unit.property_id = property.property_id';
		$table = 'payments, leases,rental_unit,property,tenant_unit,tenants,payment_method';
		
		$visit_search = $this->session->userdata('time_reports_search');
		$table_search = $this->session->userdata('time_reports_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
		}
		
		$this->db->where($where);
		$this->db->order_by('property.property_name,rental_unit.rental_unit_name', 'ASC');
		$this->db->select('*');
		$transactions_query = $this->db->get($table);
		
		$title = 'Transaction Report';
		
		if($transactions_query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/

			$row_count = 0;
			$report[$row_count][0] = '#';
			$report[$row_count][1] = 'Property Name';
			$report[$row_count][2] = 'Flat No.';
			$report[$row_count][3] = 'Tenant Name';
			$report[$row_count][4] = 'Payment Method';
			$report[$row_count][5] = 'Amount Paid';
			$report[$row_count][6] = 'Receipt Number';
			$report[$row_count][7] = 'Payment Date';
			$report[$row_count][8] = 'Paid By';
			$report[$row_count][9] = 'Receipted Date';
			$report[$row_count][10] = 'Receipt By';
			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($transactions_query->result() as $leases_row)
			{
				
				$payment_date = date('jS M Y',strtotime($leases_row->payment_date));
				$payment_created = date('jS M Y',strtotime($leases_row->payment_created));
							
				$property_name = $leases_row->property_name;
				$rental_unit_name = $leases_row->rental_unit_name;
				$personnel_id = $leases_row->personnel_id;
				$tenant_name = $leases_row->tenant_name;
				$lease_number = $leases_row->lease_number;
				$receipt_number = $leases_row->receipt_number;
				$amount_paid = $leases_row->amount_paid;
				$payment_method = $leases_row->payment_method;
				$paid_by = $leases_row->paid_by;
				$created_by = $leases_row->created_by;


				//creators and editors
				$personnel_query = $this->personnel_model->get_all_personnel();
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($created_by == $personnel_id2)
						{
							$personnel = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}
						
						else
						{
							$personnel = '-';
						}
					}
				}
				
				else
				{
					$personnel = '-';
				}
				$row_count++;
				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $property_name;
				$report[$row_count][2] = $rental_unit_name;
				$report[$row_count][3] = $tenant_name;
				$report[$row_count][4] = $payment_method;
				$report[$row_count][5] = number_format($amount_paid,0);
				$report[$row_count][6] = $receipt_number;
				$report[$row_count][7] = $payment_date;
				$report[$row_count][8] = $paid_by;
				$report[$row_count][9] = $payment_created;
				$report[$row_count][10] = $personnel;
				
				$count++;
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	/*
	*	Export Time report
	*
	*/
	function export_defaulters()
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'tenants.tenant_id > 0 AND property.property_id = rental_unit.property_id AND tenants.tenant_id = tenant_unit.tenant_id AND tenant_unit.rental_unit_id = rental_unit.rental_unit_id  AND tenant_unit.tenant_unit_id = leases.tenant_unit_id AND leases.lease_status = 1';
		$table = 'tenants,tenant_unit,rental_unit,leases,property';

		$visit_search = $this->session->userdata('time_reports_search');
		$table_search = $this->session->userdata('time_reports_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
		}
		
		$this->db->where($where);
		$this->db->order_by('property.property_name,rental_unit.rental_unit_name', 'ASC');
		$this->db->select('*');
		$defaulters_query = $this->db->get($table);
		
		$title = 'List of Defaulters';
		
		if($defaulters_query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/

			$row_count = 0;
			$report[$row_count][0] = '#';
			$report[$row_count][1] = 'Property Name';
			$report[$row_count][2] = 'Flat No.';
			$report[$row_count][3] = 'Rent P.M';
			$report[$row_count][4] = 'Tenant Name';
			$report[$row_count][5] = 'Arrears B/F';
			$report[$row_count][6] = 'Receipt/Date';
			$report[$row_count][7] = 'Amount Paid';
			$report[$row_count][8] = 'Arrears C/F';
			$report[$row_count][9] = 'Phone Number';
			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($defaulters_query->result() as $leases_row)
			{
				
				$lease_id = $leases_row->lease_id;
				$tenant_unit_id = $leases_row->tenant_unit_id;
				$property_name = $leases_row->property_name;
				$rental_unit_name = $leases_row->rental_unit_name;
				$tenant_name = $leases_row->tenant_name;
				$lease_start_date = $leases_row->lease_start_date;
				$lease_duration = $leases_row->lease_duration;
				$rent_amount = $leases_row->rent_amount;
				$lease_number = $leases_row->lease_number;
				// $arreas_bf = $leases_row->arreas_bf;
				$rent_calculation = $leases_row->rent_calculation;
				$deposit = $leases_row->deposit;
				$deposit_ext = $leases_row->deposit_ext;
				$lease_status = $leases_row->lease_status;
				$tenant_phone_number = $leases_row->tenant_phone_number;
				$arrears_bf = $leases_row->arrears_bf;
				$created = $leases_row->created;

				// $lease_start_date = date('jS M Y',strtotime($lease_start_date));
				
				// $expiry_date  = date('jS M Y',strtotime($lease_start_date, mktime()) . " + 365 day");
				// $expiry_date  = date('jS M Y', strtotime(''.$lease_start_date.'+1 years'));
				
				$current_balance = $this->reports_model->check_lease_has_balance($lease_id,$rent_amount,$arrears_bf,$lease_start_date,$tenant_unit_id);
				// echo $current_balance;
				if($current_balance > 0 || $current_balance < 0)
				{					
					$row_count++;

					if($current_balance < 0)
					{
						$last_bal = $this->accounts_model->get_months_last_amount($lease_id);
						if($last_bal == 0)
						{
							$last_bal = $arrears_bf;
						}
						else
						{
							$last_bal = $last_bal;
						}
					}
					else
					{
						$last_bal = $arrears_bf;
					}



			     	
			     	$this_month = date('m');
			     	$payments = $this->accounts_model->get_this_months_payment($lease_id,$this_month);
			     	$current_items = '';
			     	$total_amount_paid = 0;
			     	if($payments->num_rows() > 0)
			     	{
			     		$counter = 0;
			     		$total_amount_paid = 0;
			     		$receipt_counter = '';
			     		foreach ($payments->result() as $value) {
			     			# code...
			     			$receipt_number = $value->receipt_number;
			     			$amount_paid = $value->amount_paid;

			     			if($counter > 0)
			     			{
			     				$addition = '#';
			     				// $receipt_counter .= $receipt_number.$addition;
			     			}
			     			else
			     			{
			     				$addition = ' ';
			     				
			     			}
			     			$receipt_counter .= $receipt_number.$addition;
			     			$total_amount_paid += $total_amount_paid + $amount_paid;
			     			$counter++;
			     		}
			     		$current_items = $receipt_number;
			     		$total_amount_paid = $total_amount_paid;
			     	}
			     	else
			     	{
			     		$current_items = '-';
			     		$total_paid_amount = '-';
			     	}

					$rent_amount = number_format($rent_amount,0);
						//display the patient data
						$report[$row_count][0] = $count;
						$report[$row_count][1] = $property_name;
						$report[$row_count][2] = $rental_unit_name;
						$report[$row_count][3] = $rent_amount;
						$report[$row_count][4] = $tenant_name;
						$report[$row_count][5] = number_format($last_bal,0);
						$report[$row_count][6] = $current_items;
						$report[$row_count][7] = $total_amount_paid;
						$report[$row_count][8] = number_format($current_balance,0);
						$report[$row_count][9] = $tenant_phone_number;
				}
				$count++;
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_visit_departments($where, $table)
	{
		//invoiced
		$this->db->from($table.', visit_department');
		$this->db->select('visit_department.*');
		$this->db->where($where.' AND visit.visit_id = visit_department.visit_id');
		$query = $this->db->get();
		
		return $query;
	}


	public function get_insurance_company()
	{
		//invoiced
		$this->db->from('insurance_company');
		$this->db->select('*');
		$this->db->order_by('insurance_company_name');
		$query = $this->db->get();
		
		return $query;
	}
	
	public function calculate_debt_total($debtor_invoice_id, $where, $table)
	{
		$where .= ' AND debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id;
		
		$total_services_revenue = $this->reports_model->get_total_services_revenue($where, $table);
		
		$where2 = $where.' AND payments.payment_type = 1';
		$total_cash_collection = $this->reports_model->get_total_cash_collection($where2, $table);
		
		return $total_services_revenue - $total_cash_collection;
	}
	
	public function get_debtor_invoice($where, $table)
	{
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}


	public function get_all_doctors()
	{
		$this->db->select('personnel.*');
		$this->db->where('personnel.personnel_type_id != 1');
		$this->db->order_by('personnel_fname');
		$query = $this->db->get('personnel');
		
		return $query;
	}

	public function get_total_collected($doctor_id, $date_from = NULL, $date_to = NULL)
	{
		$table = 'visit_charge, visit';
		
		$where = 'visit_charge.visit_id = visit.visit_id AND visit.personnel_id = '.$doctor_id;
		
		$visit_search = $this->session->userdata('all_doctors_search');
		if(!empty($visit_search))
		{
			$where = 'visit_charge.visit_id = visit.visit_id AND visit.personnel_id = '.$doctor_id.' '. $visit_search;
		}
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (date >= \''.$date_from.'\' AND date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND date LIKE \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND date LIKE \''.$date_from.'\'';
		}
		
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		// $result = $query->row();
		// $total = $result[0]->service_total;
		
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key):
				# code...
				$total = $key->service_total;

				if(!is_numeric($total))
				{
					return 0;
				}
				else
				{
					return $total;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
		
	}
	/*
	*	Retrieve all active branches
	*
	*/
	public function get_all_active_branches()
	{
		//retrieve all users
		$this->db->from('branch');
		$this->db->where('branch_status = 1');
		$this->db->order_by('branch_name','ASC');
		$query = $this->db->get();
		
		return $query;
	}
	public function get_total_patients($doctor_id, $date_from = NULL, $date_to = NULL)
	{
		$table = 'visit';
		
		$where = 'visit.personnel_id = '.$doctor_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_from.'\'';
		}
		
		$this->db->where($where);
		$total = $this->db->count_all_results('visit');
		
		return $total;
	}

	/*
	*	Export Time report
	*
	*/
	function doctor_reports_export($date_from = NULL, $date_to = NULL)
	{
		$this->load->library('excel');
		$report = array();
		
		//export title
		if(!empty($date_from) && !empty($date_to))
		{
			$title = 'Doctors report from '.date('jS M Y',strtotime($date_from)).' to '.date('jS M Y',strtotime($date_to));
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_to));
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_from));
		}
		
		else
		{
			$date_from = date('Y-m-d');
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_from));
		}
		
		//document ehader
		$row_count = 0;
		$report[$row_count][0] = '#';
		$report[$row_count][1] = 'Doctor\'s name';
		$report[$row_count][2] = 'Total collection';
		$report[$row_count][3] = 'Patients seen';
		
		//get all doctors
		$doctor_results = $this->reports_model->get_all_doctors();
		$result = $doctor_results->result();
		$grand_total = 0;
		$patients_total = 0;
		$count = 0;
		
		foreach($result as $res)
		{
			$personnel_id = $res->personnel_id;
			$personnel_onames = $res->personnel_onames;
			$personnel_fname = $res->personnel_fname;
			$count++;
			$row_count++;
			
			//get service total
			$total = $this->reports_model->get_total_collected($personnel_id, $date_from, $date_to);
			$patients = $this->reports_model->get_total_patients($personnel_id, $date_from, $date_to);
			$grand_total += $total;
			$patients_total += $patients;
			
			$report[$row_count][0] = $count;
			$report[$row_count][1] = $personnel_fname.' '.$personnel_onames;
			$report[$row_count][2] = number_format($total, 0);
			$report[$row_count][3] = $patients;
		}
		$row_count++;
		
		$report[$row_count][0] = '';
		$report[$row_count][1] = '';
		$report[$row_count][2] = number_format($grand_total, 0);
		$report[$row_count][3] = $patients_total;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	function doctor_patients_export($personnel_id, $date_from = NULL, $date_to = NULL)
	{
		$where = ' AND visit.personnel_id = '.$personnel_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_from.'\'';
		}
		$_SESSION['all_transactions_search'] = $where;
		
		$this->export_transactions();
	}
	
	public function calculate_hours_worked($personnel_id, $date_from, $date_to)
	{
		$where = 'personnel_id = '.$personnel_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (schedule_date >= \''.$date_from.'\' AND schedule_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_from.'\'';
		}
		
		$this->db->where($where);
		$query = $this->db->get('schedule_item');
		$total_hours = 0;
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$schedule_start_time = $res->schedule_start_time;
				$schedule_end_time = $res->schedule_end_time;
				
				$hours_difference = (strtotime($schedule_end_time) - strtotime($schedule_start_time)) / 3600;
				$total_hours += $hours_difference;
			}
		}
		
		return $total_hours;
	}
	
	public function calculate_days_worked($personnel_id, $date_from, $date_to)
	{
		$where = 'personnel_id = '.$personnel_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (schedule_date >= \''.$date_from.'\' AND schedule_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_from.'\'';
		}
		
		$this->db->where($where);
		$query = $this->db->get('schedule_item');
		$total_days = $query->num_rows();
		
		return $total_days;
	}
	
	public function get_visit_type()
	{
		//invoiced
		$this->db->select('*');
		$this->db->from('visit_type');
		$this->db->where('visit_type_id > 1');
		$this->db->order_by('visit_type_name');
		$query = $this->db->get();
		
		return $query;
	}
	/*
	*	Retrieve total visits
	*
	*/
	public function get_total_visits($where, $table)
	{
		$this->db->from($table);
		$this->db->where($where);
		$total = $this->db->count_all_results();
		
		return $total;
	}
	
	/*
	*	Retrieve debtors_invoices
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_debtors_invoices($table, $where, $per_page, $page, $order, $order_method)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function add_debtor_invoice($visit_type_id)
	{
		$data = array(
			'debtor_invoice_created'=>date('Y-m-d H:i:s'),
			'debtor_invoice_created_by'=>$this->session->userdata('personnel_id'),
			'batch_no'=>$this->create_batch_number(),
			'visit_type_id'=>$visit_type_id,
			'debtor_invoice_modified_by'=>$this->session->userdata('personnel_id'),
			'date_from' => $this->input->post('invoice_date_from'),
			'date_to' => $this->input->post('invoice_date_to')
		);
		
		if($this->db->insert('debtor_invoice', $data))
		{
			$debtor_invoice_id = $this->db->insert_id();
			
			if($debtor_invoice_id > 0)
			{
				//get all invoices within the selected dates
				$this->db->where(
					array(
						'close_card' => 1,
						'visit_delete' => 0,
						'visit_type' => $visit_type_id,
						'visit_date >= ' => $this->input->post('invoice_date_from'),
						'visit_date <= ' => $this->input->post('invoice_date_to')
					)
				);
				$this->db->select('visit_id');
				$query = $this->db->get('visit');
				
				if($query->num_rows() > 0)
				{
					$invoice_data['debtor_invoice_id'] = $debtor_invoice_id;
					
					foreach($query->result() as $res)
					{
						$visit_id = $res->visit_id;
						
						$invoice_data['visit_id'] = $visit_id;
						
						if($this->db->insert('debtor_invoice_item', $invoice_data))
						{
						}
						
						else
						{
							$this->session->set_userdata('error_message', 'Unable to add details for visit ID '.$visit_id);
						}
					}
					$this->session->set_userdata('success_message', 'Batch added successfully');
					return TRUE;
				}
				
				else
				{
					$this->session->set_userdata('error_message', 'The selected date range does not contain any invoices');
					return FALSE;
				}
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'The selected date range does not contain any invoices');
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Create batch number
	*
	*/
	public function create_batch_number()
	{
		//select product code
		$this->db->from('debtor_invoice');
		$this->db->where("batch_no LIKE '".$this->session->userdata('branch_code').'-'.date('y')."-%'");
		$this->db->select('MAX(batch_no) AS number');
		$query = $this->db->get();
		$preffix = $this->session->userdata('branch_code').'-'.date('y').'-';
		
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$real_number = str_replace($preffix, "", $number);
			$real_number++;//go to the next number
			$number = $preffix.sprintf('%06d', $real_number);
		}
		else{//start generating receipt numbers
			$number = $preffix.sprintf('%06d', 1);
		}
		
		return $number;
	}
	

	public function check_lease_has_balance($lease_id,$rent_amount,$arrears_bf,$lease_start_date,$tenant_unit_id)
	{
		// get all leases for this tenant and for that unit

		$this->db->from('leases');
		$this->db->where('tenant_unit_id = '.$tenant_unit_id);
		$this->db->select('lease_id');
		$query = $this->db->get();

		$total_amount_paid = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$payment_lease_id = $key->lease_id;

				$total_paid = $this->accounts_model->get_months_amount($payment_lease_id);

				$total_amount_paid = $total_amount_paid + $total_paid;

			}
		}
		

		$date1 = $lease_start_date;

		// $date2 = date('Y-m-d');
		$date2 = '2016-03-01';

		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y',$ts1);
		$year2 = date('Y',$ts2);

		$month1 = date('m',$ts1);
		$month2 = date('m',$ts2);
		$total_months_comb = $month2 - $month2;
		$total_months = ($year2-$year1) * 12 + $total_months_comb;


			
		if($total_months == 0)
		{
			$total_months = 1;
		}
		$total_months = $total_months+1;
		

		$lease_balance = ($rent_amount * $total_months) + $arrears_bf;

		// var_dump("close".$lease_balance); die();

		
		if($lease_balance < 0)
		{
			
			$current_balance = $total_amount_paid - $lease_balance;
		}
		else
		{
			$current_balance = $lease_balance - $total_amount_paid;
		}
		
		
		return $current_balance;
		
		
		// get total_

	}
	/*
	*	Retrieve visits
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_payments($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name, payments.*, payment_method.*, personnel.personnel_fname, personnel.personnel_onames, service.service_name');
		$this->db->join('personnel', 'payments.payment_created_by = personnel.personnel_id', 'left');
		$this->db->join('service', 'payments.payment_service_id = service.service_id', 'left');
		$this->db->where($where);
		$this->db->order_by('payments.time','DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Export Transactions
	*
	*/
	function export_cash_report()
	{
		$this->load->library('excel');
		
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
		
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name, payments.*, payment_method.*, personnel.personnel_fname, personnel.personnel_onames, service.service_name');
		$this->db->join('personnel', 'payments.payment_created_by = personnel.personnel_id', 'left');
		$this->db->join('service', 'payments.payment_service_id = service.service_id', 'left');
		$this->db->where($where);
		$this->db->order_by('payments.time','DESC');
		$query = $this->db->get($table);
		
		$title = 'Cash report '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
		$col_count = 0;
		
		if($query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/
			$row_count = 0;
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Payment Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Time recorded';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'Service';
			$col_count++;
			$report[$row_count][$col_count] = 'Amount';
			$col_count++;
			$report[$row_count][$col_count] = 'Method';
			$col_count++;
			$report[$row_count][$col_count] = 'Description';
			$col_count++;
			$report[$row_count][$col_count] = 'Recorded by';
			$col_count++;
			$current_column = $col_count ;
			
			foreach ($query->result() as $row)
			{
				$count++;
				$row_count++;
				$col_count = 0;
				
				$total_invoiced = 0;
				$payment_created = date('jS M Y',strtotime($row->payment_created));
				$time = date('H:i a',strtotime($row->time));
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$payment_method = $row->payment_method;
				$amount_paid = $row->amount_paid;
				$service_name = $row->service_name;
				$transaction_code = $row->transaction_code;
				$created_by = $row->personnel_fname.' '.$row->personnel_onames;
				
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $payment_created;
				$col_count++;
				$report[$row_count][$col_count] = $time;
				$col_count++;
				$report[$row_count][$col_count] = $patient_surname.' '.$patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = $service_name;
				$col_count++;
				$report[$row_count][$col_count] = number_format($amount_paid, 2);
				$col_count++;
				$report[$row_count][$col_count] = $payment_method;
				$col_count++;
				$report[$row_count][$col_count] = $transaction_code;
				$col_count++;
				$report[$row_count][$col_count] = $created_by;
				$col_count++;
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	public function get_debtor_invoice_items($debtor_invoice_id)
	{
		$this->db->select('SUM(visit_charge.visit_charge_units * visit_charge.visit_charge_amount) AS invoice_amount, patients.patient_surname, patients.patient_othernames, patients.patient_number, patients.current_patient_number, visit.visit_id, visit.visit_date, visit.patient_insurance_number, debtor_invoice_item.debtor_invoice_item_status, debtor_invoice_item.debtor_invoice_item_id');
		$this->db->where('visit.visit_delete = 0 AND visit.visit_id = debtor_invoice_item.visit_id AND visit.patient_id = patients.patient_id AND visit.visit_id = visit_charge.visit_id AND debtor_invoice_item.debtor_invoice_id = '.$debtor_invoice_id);
		
		$this->db->group_by('visit_id');
		$this->db->order_by('visit_date');
		$query = $this->db->get('debtor_invoice_item, visit, visit_charge, patients');
		
		return $query;
	}
}