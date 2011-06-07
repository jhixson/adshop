<?php defined('SYSPATH') or die('No direct script access.');

class Menu_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	* Get menu categories
	* @return the result object
	*/
	public function get_menu() {
		return $this->db->get('categories');
	}
	 
}