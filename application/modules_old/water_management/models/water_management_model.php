<?php

class Water_management_model extends CI_Model 
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

	/*
	*	Retrieve all personnel
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_water_records($table, $where, $per_page, $page, $order = 'water_management.document_number', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$this->db->group_by('water_management.document_number');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}



	/*
	*	Import Template
	*
	*/
	function import_template()
	{
		$this->load->library('Excel');
		
		$title = 'Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'HSE Number';
		$report[$row_count][1] = 'Prev Reading';
		$report[$row_count][2] = 'Curr Reading';
		$report[$row_count][3] = 'Units Consumed';
		$report[$row_count][4] = 'Prev B/f';
		$report[$row_count][5] = 'Total Due';
		$report[$row_count][6] = 'Paid';
		$report[$row_count][7] = 'Receipt No';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_csv_charges($upload_path)
	{
		//load the file model
		$this->load->model('admin/file_model');
		/*
			-----------------------------------------------------------------------------------------
			Upload csv
			-----------------------------------------------------------------------------------------
		*/
		$response = $this->file_model->upload_csv($upload_path, 'import_csv');
		
		if($response['check'])
		{
			$file_name = $response['file_name'];
			
			$array = $this->file_model->get_array_from_csv($upload_path.'/'.$file_name);
			//var_dump($array); die();
			$response2 = $this->sort_csv_charges_data($array);
		
			if($this->file_model->delete_file($upload_path."\\".$file_name, $upload_path))
			{
			}
			
			return $response2;
		}
		
		else
		{
			$this->session->set_userdata('error_message', $response['error']);
			return FALSE;
		}
	}
	public function sort_csv_charges_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);
		
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 8))
		{
			$count = 0;
			$comment = '';
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$document_number = $this->create_document_number();
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$hse_name = $array[$r][0];
				$prev_reading = $array[$r][1];
				$curr_reading = $array[$r][2];
				$units_consumed = $array[$r][3];
				$prev_bal = $array[$r][4];
				$total_due = $array[$r][5];
				$paid = $array[$r][6];
				$receipt_no = $array[$r][7];
				
				$count++;
				
				// service charge entry
				$service_charge_insert = array(
								"house_number" => $hse_name,
								"prev_reading" => $prev_reading,
								"current_reading" => $curr_reading,
								"units_consumed" => $units_consumed,
								"total_due" => $total_due,
								"paid" => $paid,
								"receipt_no" => $receipt_no,
								"prev_bill" => $prev_bal,
								"document_number" => $document_number,
								"created" => date("Y-m-d"),
								"created_by" => $this->session->userdata('personnel_id'),
								"branch_code" => $this->session->userdata('branch_code'),
								'property_id' => $this->input->post('property_id'),
							);
				
					if($this->db->insert('water_management', $service_charge_insert))
					{
						$comment .= '<br/>Details successfully added to the database';
						$class = 'success';
					}
					
					else
					{
						$comment .= '<br/>Not saved internal error';
						$class = 'danger';
					}
			}	
			$return['response'] = TRUE;
			$return['check'] = TRUE;
				
		}
		else
		{
			$return['response'] = FALSE;
			$return['check'] = FALSE;
		}
		
		return $return;
	}

	public function create_document_number()
	{
		//select product code
		$this->db->where('branch_code = "'.$this->session->userdata('branch_code').'"');
		$this->db->from('water_management');
		$this->db->select('MAX(document_number) AS number');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;//go to the next number
			if($number == 1){
				$number = "".$this->session->userdata('branch_code')."-000001";
			}
			
			if($number == 1)
			{
				$number = "".$this->session->userdata('branch_code')."-000001";
			}
			
		}
		else{//start generating receipt numbers
			$number = "".$this->session->userdata('branch_code')."-000001";
		}
		return $number;
	}
	public function get_document_details($document_number)
	{
		$this->db->where('document_number = "'.$document_number.'"');
		$this->db->from('water_management');
		$this->db->select('*');
		$query = $this->db->get();

		return $query;
	}

	public function sms($phone,$message)
	{
        // This will override any configuration parameters set on the config file
		// max of 160 characters
		// to get a unique name make payment of 8700 to Africastalking/SMSLeopard
		// unique name should have a maximum of 11 characters
		$phone_number = '+254'.$phone;
		// get items 

		$configuration = $this->admin_model->get_configuration();

		$mandrill = '';
		$configuration_id = 0;
		
		if($configuration->num_rows() > 0)
		{
			$res = $configuration->row();
			$configuration_id = $res->configuration_id;
			$mandrill = $res->mandrill;
			$sms_key = $res->sms_key;
			$sms_user = $res->sms_user;
	        $sms_suffix = $res->sms_suffix;
	        $sms_from = $res->sms_from;
		}
	    else
	    {
	        $configuration_id = '';
	        $mandrill = '';
	        $sms_key = '';
	        $sms_user = '';
	        $sms_suffix = '';

	    }

	    $actual_message = $message.' '.$sms_suffix;
	    // var_dump($actual_message); die();
		// get the current branch code
        $params = array('username' => $sms_user, 'apiKey' => $sms_key);  

        $this->load->library('AfricasTalkingGateway', $params);
		// var_dump($params)or die();
        // Send the message
		try 
		{
        	$results = $this->africastalkinggateway->sendMessage($phone_number, $actual_message, $sms_from);
			
			//var_dump($results);die();
			foreach($results as $result) {
				// status is either "Success" or "error message"
				// echo " Number: " .$result->number;
				// echo " Status: " .$result->status;
				// echo " MessageId: " .$result->messageId;
				// echo " Cost: "   .$result->cost."\n";
			}
			return $result->status;

		}
		
		catch(AfricasTalkingGatewayException $e)
		{
			// echo "Encountered an error while sending: ".$e->getMessage();
			return FALSE;
		}
    }
	
}
