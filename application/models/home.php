<?php defined('SYSPATH') or die('No direct script access.');

class Home_Model extends Model {

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get a list of counties
	 * @return the array
	 */
	public function list_counties() {
		$counties = array('Carlow', 'Cavan', 'Clare', 'Cork', 'Donegal', 
						  'Dublin', 'Galway', 'Kerry', 'Kildare', 'Kilkenny', 'Laois', 'Leitrim', 
						  'Limerick', 'Longford', 'Louth', 'Mayo', 'Meath', 'Monaghan', 'Offaly', 'Roscommon', 'Sligo', 
						  'Tipperary', 'Waterford', 'Westmeath', 'Wexford', 'Wicklow');
		return $counties;
	}
 
	/**
	 * Get most recent ads
	 * @return the array
	 */
	public function recent_ads() {
		/*
		$recent = array(0=>array('id'=>'1','title'=>'Guitar','image'=>'fendertelecaster.jpg'),
						1=>array('id'=>'2','title'=>'AuidQ7','image'=>'AuidQ7.jpg'),
						2=>array('id'=>'2','title'=>'iPhone','image'=>'phone1.jpg'),
						3=>array('id'=>'1','title'=>'Bike','image'=>'bike1.jpg'),
						4=>array('id'=>'1','title'=>'iPhone','image'=>'phone2.jpg'),
						5=>array('id'=>'1','title'=>'Helmet','image'=>'helmet.jpg'),
						6=>array('id'=>'2','title'=>'Quad','image'=>'quad1.jpg'),
						7=>array('id'=>'2','title'=>'BMX Bike','image'=>'bike2.jpg'),
						8=>array('id'=>'1','title'=>'iPhone','image'=>'phone3.jpg')
						);
		return $recent;
		*/
		
		$this->db->from('items');
		$this->db->select('items.item_id, items.title, media.media');
		$this->db->join('media',array('media.item_id'=>'items.item_id'));
		$this->db->where(array('active'=>1,'sold'=>0));
		$this->db->orderby('publish_timestamp','DESC');
		$this->db->limit('9');
		$recent = $this->db->get();
		
		return $recent;
	}
	
	/**
	 * Get recently sold items
	 * @return the array
	 */
	public function recently_sold() {
		/*
		$recent = array(0=>array('title'=>'iPhone','image'=>'phone1.jpg'),
						1=>array('title'=>'BMX Bike','image'=>'bike2.jpg'),
						2=>array('title'=>'Quad','image'=>'quad1.jpg'),
						3=>array('title'=>'Helmet','image'=>'helmet.jpg')
						);
		*/
		$this->db->from('items');
		$this->db->select('items.item_id, items.title, media.media');
		$this->db->join('media',array('media.item_id'=>'items.item_id'));
		$this->db->where(array('sold'=>1));
		$this->db->orderby('publish_timestamp','DESC');
		$this->db->limit('4');
		$recent = $this->db->get();
		
		return $recent;
	}
}
?>
