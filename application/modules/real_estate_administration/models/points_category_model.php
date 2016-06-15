<?php

class Points_category_model extends CI_Model 
{	
	
	
	public function get_all_properties($table, $where, $per_page, $page)
	{
		//retrieve all points
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('points_category.points_category_date_from');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Delete an existing points
	*	@param int $points_category_id
	*
	*/
	public function delete_points($points_category_id)
	{
		if($this->db->delete('points_category', array('points_category_id' => $points_category_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated points
	*	@param int $points_category_id
	*
	*/
	public function activate_points_category($points_category_id)
	{
		$data = array(
				'points_category_status' => 1
			);
		$this->db->where('points_category_id', $points_category_id);
		
		if($this->db->update('points_category', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated points
	*	@param int $points_category_id
	*
	*/
	public function deactivate_points_category($points_category_id)
	{
		$data = array(
				'points_category_status' => 0
			);
		$this->db->where('points_category_id', $points_category_id);
		
		if($this->db->update('points_category', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_active_points()
	{
  		$table = "points_category";
		$where = "points_category_status = 1";
		
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}

	public function get_points_category_date_from($points_category_id)
	{
		$table = "points_category";
		$where = "points_category_id = ".$points_category_id;
		
		$this->db->where($where);
		$query = $this->db->get($table);
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$points_category_date_from =$key->points_category_date_from;
			}
			return $points_category_date_from;
		}
		else
		{
			return FALSE;
		}
	}
}
