<?php

class Rental_unit_model extends CI_Model 
{	
	public function upload_rental_unit_image($rental_unit_path, $edit = NULL)
	{
		//upload product's gallery images
		$resize['width'] = 500;
		$resize['height'] = 500;
		
		if(!empty($_FILES['rental_unit_image']['tmp_name']))
		{
			$image = $this->session->userdata('rental_unit_file_name');
			
			if((!empty($image)) || ($edit != NULL))
			{
				if($edit != NULL)
				{
					$image = $edit;
				}
				//delete any other uploaded image
				$this->file_model->delete_file($rental_unit_path."\\".$image, $rental_unit_path);
				
				//delete any other uploaded thumbnail
				$this->file_model->delete_file($rental_unit_path."\\thumbnail_".$image, $rental_unit_path);
			}
			//Upload image
			$response = $this->file_model->upload_file($rental_unit_path, 'rental_unit_image', $resize, 'height');
			if($response['check'])
			{
				$file_name = $response['file_name'];
				$thumb_name = $response['thumb_name'];
				
				//crop file to 1920 by 1010
				$response_crop = $this->file_model->crop_file($rental_unit_path."\\".$file_name, $resize['width'], $resize['height']);
				
				if(!$response_crop)
				{
					$this->session->set_userdata('rental_unit_error_message', $response_crop);
				
					return FALSE;
				}
				
				else
				{
					//Set sessions for the image details
					$this->session->set_userdata('rental_unit_file_name', $file_name);
					$this->session->set_userdata('rental_unit_thumb_name', $thumb_name);
				
					return TRUE;
				}
			}
		
			else
			{
				$this->session->set_userdata('rental_unit_error_message', $response['error']);
				
				return FALSE;
			}
		}
		
		else
		{
			$this->session->set_userdata('rental_unit_error_message', '');
			return FALSE;
		}
	}
	
	public function get_all_rental_units($table, $where, $per_page, $page)
	{
		//retrieve all rental_unit
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('rental_unit.rental_unit_id,rental_unit.rental_unit_name');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Delete an existing rental_unit
	*	@param int $rental_unit_id
	*
	*/
	public function delete_rental_unit($rental_unit_id)
	{
		if($this->db->delete('rental_unit', array('rental_unit_id' => $rental_unit_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_rental_unit()
	{
		// check if it exisits

		$table = "rental_unit";
		$where = "property_id = ".$this->input->post("property_id")." AND rental_unit_name = '".$this->input->post("rental_unit_name")."'";
		
		$this->db->where($where);
		$query = $this->db->get($table);
		if($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			$data2 = array(
				'rental_unit_name'=>$this->input->post("rental_unit_name"),
				'property_id'=>$this->input->post("property_id"),
				'rental_unit_status'=>1,
				'created'=>date('Y-m-d'),
				'created_by'=>$this->session->userdata('personnel_id')
			);
			
			$table = "rental_unit";
			if($this->db->insert($table, $data2))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		
	}
	/*
	*	Activate a deactivated rental_unit
	*	@param int $rental_unit_id
	*
	*/
	public function activate_rental_unit($rental_unit_id)
	{
		$data = array(
				'rental_unit_status' => 1
			);
		$this->db->where('rental_unit_id', $rental_unit_id);
		
		if($this->db->update('rental_unit', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated rental_unit
	*	@param int $rental_unit_id
	*
	*/
	public function deactivate_rental_unit($rental_unit_id)
	{
		$data = array(
				'rental_unit_status' => 0
			);
		$this->db->where('rental_unit_id', $rental_unit_id);
		
		if($this->db->update('rental_unit', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_active_rental_unit()
	{
  		$table = "rental_unit";
		$where = "rental_unit_status = 1";
		
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}

	public function get_tenancy_details($rental_unit_id)
	{
		$table = "tenant_unit";
		$where = "rental_unit_id = ".$rental_unit_id." AND tenant_unit_status = 1";
		
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_rental_unit_name($rental_unit_id)
	{
		$table = "rental_unit";
		$where = "rental_unit_id = ".$rental_unit_id;
		
		$this->db->where($where);
		$query = $this->db->get($table);
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$rental_unit_name =$key->rental_unit_name;
			}
			return $rental_unit_name;
		}
		else
		{
			return FALSE;
		}
	}
}
