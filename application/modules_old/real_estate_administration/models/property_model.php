<?php

class Property_model extends CI_Model 
{	
	public function upload_property_image($property_path, $edit = NULL)
	{
		//upload product's gallery images
		$resize['width'] = 500;
		$resize['height'] = 500;
		
		if(!empty($_FILES['property_image']['tmp_name']))
		{
			$image = $this->session->userdata('property_file_name');
			
			if((!empty($image)) || ($edit != NULL))
			{
				if($edit != NULL)
				{
					$image = $edit;
				}
				//delete any other uploaded image
				$this->file_model->delete_file($property_path."\\".$image, $property_path);
				
				//delete any other uploaded thumbnail
				$this->file_model->delete_file($property_path."\\thumbnail_".$image, $property_path);
			}
			//Upload image
			$response = $this->file_model->upload_file($property_path, 'property_image', $resize, 'height');
			if($response['check'])
			{
				$file_name = $response['file_name'];
				$thumb_name = $response['thumb_name'];
				
				//crop file to 1920 by 1010
				$response_crop = $this->file_model->crop_file($property_path."\\".$file_name, $resize['width'], $resize['height']);
				
				if(!$response_crop)
				{
					$this->session->set_userdata('property_error_message', $response_crop);
				
					return FALSE;
				}
				
				else
				{
					//Set sessions for the image details
					$this->session->set_userdata('property_file_name', $file_name);
					$this->session->set_userdata('property_thumb_name', $thumb_name);
				
					return TRUE;
				}
			}
		
			else
			{
				$this->session->set_userdata('property_error_message', $response['error']);
				
				return FALSE;
			}
		}
		
		else
		{
			$this->session->set_userdata('property_error_message', '');
			return FALSE;
		}
	}
	
	public function get_all_properties($table, $where, $per_page, $page)
	{
		//retrieve all property
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('property.property_name');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Delete an existing property
	*	@param int $property_id
	*
	*/
	public function delete_property($property_id)
	{
		if($this->db->delete('property', array('property_id' => $property_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated property
	*	@param int $property_id
	*
	*/
	public function activate_property($property_id)
	{
		$data = array(
				'property_status' => 1
			);
		$this->db->where('property_id', $property_id);
		
		if($this->db->update('property', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated property
	*	@param int $property_id
	*
	*/
	public function deactivate_property($property_id)
	{
		$data = array(
				'property_status' => 0
			);
		$this->db->where('property_id', $property_id);
		
		if($this->db->update('property', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_active_property()
	{
  		$table = "property";
		$where = "property_status = 1";
		
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_property_name($property_id)
	{
		$table = "property";
		$where = "property_id = ".$property_id;
		
		$this->db->where($where);
		$query = $this->db->get($table);
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$property_name =$key->property_name;
			}
			return $property_name;
		}
		else
		{
			return FALSE;
		}
	}
}
