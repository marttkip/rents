<?php

class Messaging_model extends CI_Model 
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
	public function get_all_messages($table, $where, $per_page, $page, $order = 'messaging.messaging_tenant_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
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
		
		$title = 'Rental Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Unit Name';
		$report[$row_count][1] = 'Tenant Name';
		$report[$row_count][2] = 'Tenant Phone Number';
		$report[$row_count][3] = 'Arreas';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_csv_charges($upload_path, $service_id)
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
			$response2 = $this->sort_csv_charges_data($array, $service_id);
		
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
	public function sort_csv_charges_data($array, $message_category_id)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);
		
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 4))
		{
			$count = 0;
			$comment = '';
			$items['modified_by'] = $this->session->userdata('personnel_id');
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$messaging_unit_name = $array[$r][0];
				$messaging_tenant_name = ucwords(strtolower($array[$r][1]));
				$messaging_tenant_phone_number = $array[$r][2];
				$messaging_arreas = $array[$r][3];
				
				$count++;
				
				// service charge entry
				$service_charge_insert = array(
								"messaging_tenant_name" => $messaging_tenant_name,
								"message_category_id" => $message_category_id,
								"messaging_unit_name" => $messaging_unit_name,
								"messaging_arreas" => $messaging_arreas,
								"messaging_tenant_phone_number" => $messaging_tenant_phone_number,
								"date_created" => date("Y-m-d"),
								"created_by" => $this->session->userdata('personnel_id'),
								"branch_code" => $this->session->userdata('branch_code'),
								'sent_status' => 0,
							);
				
					if($this->db->insert('messaging', $service_charge_insert))
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


	public function send_unsent_messages()
	{
		$where = 'messaging.message_category_id = message_category.message_category_id AND messaging.sent_status = 0 AND messaging.branch_code = "'. $this->session->userdata('branch_code').'"';
		$table = 'messaging, message_category';
		
		$this->db->where($where);
		$query = $this->db->get($table);
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $row) {
				# code...
				$messaging_id = $row->messaging_id;
				$messaging_tenant_name = $row->messaging_tenant_name;
				$messaging_unit_name = $row->messaging_unit_name;
				$messaging_arreas = $row->messaging_arreas;
				$date_created = $row->date_created;
				$messaging_tenant_phone_number = $row->messaging_tenant_phone_number;
				$message_category_id = $row->message_category_id;
				$message_category_name = $row->message_category_name;
				$branch_code = $row->branch_code;
				$sent_status = $row->sent_status;

				$delivery_message = "Hello ".$messaging_tenant_name.", your outstanding ".$message_category_name." is KES. ".$messaging_arreas.".";
           
				$response = $this->sms($messaging_tenant_phone_number,$delivery_message);

				if($response == "Success" OR $response == "success")
				{
					$service_charge_update = array('sent_status' => 1);
					$this->db->where('messaging_id',$messaging_id);
					$this->db->update('messaging', $service_charge_update);

				}
				else
				{
					$service_charge_update = array('sent_status' => 0);
					$this->db->where('messaging_id',$messaging_id);
					$this->db->update('messaging', $service_charge_update);
				}

			}
		}
		else
		{

		}
		
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
