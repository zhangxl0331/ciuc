<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vars_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function var_exists($var) 
	{
		if($this->db->where('name', $var)->get('vars')->first_row())
		{		
			return TRUE;
		}
		
		return FALSE;
	}

}