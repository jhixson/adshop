<?php defined('SYSPATH') or die('No direct script access.');

class View_Model extends Model {

	public function __construct() {
		parent::__construct();
	}
 
	/**
	 * Get subcategories for given category
	 * @return the result object
	 */
	public function get_subcategories($category,$subcategory='') {
		$subcategories = array();
		
		//$this->db->where('slug',$category);
		//$category_result = $this->db->get('categories');
		
		if(!is_numeric($category)) {
			$this->db->where('slug',$category);
			$category_result = $this->db->get('categories');
			$category = $category_result[0]->id;
		}
		
		$subcategory_id = 0;
		if(!empty($subcategory) && !is_numeric($subcategory)) {
			$this->db->where(array('slug'=>$subcategory,'category_id'=>$category));
			$subcategory_result = $this->db->get('subcategories');
			$subcategory_id = $subcategory_result[0]->id;
		}
		
		$this->db->from('subcategories');
		$this->db->select('subcategories.*, count(items.item_id) as c');
		$this->db->join('items',array('subcategories.id'=>'items.subcategory_id','items.active'=>1,'items.sold'=>0),'','LEFT');
		$this->db->where('subcategories.category_id',$category);
		$this->db->groupby('subcategories.id');
		$this->db->orderby(array('subcategories.order_id' => 'asc', 'subcategories.id' => 'asc'));
		$subcategory_result = $this->db->get();
		//print_r($this->db->get());
		
		$dupe_categories = array('cds-dvds','all-lawnmowers','horses','sports-tickets');
		foreach($subcategory_result as $s) {
			$class = '';
			if($s->id == $subcategory_id || $s->slug == $subcategory)
				$class = ' active';

			if(in_array($s->slug,$dupe_categories)) {
				$dupe_count = $this->db->query(' SELECT `subcategories`.*, count(items.item_id) as c FROM (`subcategories`) LEFT JOIN `items` ON (`subcategories`.`id` = `items`.`subcategory_id` AND `items`.`active` = 1 AND `items`.`sold` = 0) WHERE `subcategories`.`id` in (select id from subcategories where slug = \''.$s->slug.'\') GROUP BY `subcategories`.`title` ORDER BY `subcategories`.`id` ASC');
				$count = $dupe_count[0]->c;
			}
			else
				$count = $s->c;
			

			$subcategories[$s->slug] = array('id'=>$s->id,'title'=>$s->title,'image'=>$s->image,'class'=>$class,'count'=>$count);
		}
	
		return $subcategories;
	}
	
	/**
	 * Get subsubcategories for given subcategory
	 * @return the result object
	 */
	public function get_subsubcategories($subcategory,$subsubcategory='') {
		$subsubcategories = array();
		
		if(!is_numeric($subcategory)) {
			$this->db->where('slug',$subcategory);
			$subcategory_result = $this->db->get('subcategories');
			$subcategory = $subcategory_result[0]->id;
		}
		
		
		if(!empty($subsubcategory) && !is_numeric($subsubcategory)) {
			$this->db->where(array('slug'=>$subsubcategory,'subcategory_id'=>$subcategory));
			$subsubcategory_result = $this->db->get('subsubcategories');
			$subsubcategory = $subsubcategory_result[0]->id;
		}
		
		$this->db->from('subsubcategories');
		$this->db->select('subsubcategories.*, count(items.item_id) as c');
		$this->db->join('items',array('subsubcategories.id'=>'items.subsubcategory_id','items.active'=>1,'items.sold'=>0),'','LEFT');
		$this->db->where('subsubcategories.subcategory_id',$subcategory);
		$this->db->groupby('subsubcategories.id');
		$this->db->orderby('subsubcategories.title', 'asc');
		$subsubcategory_result = $this->db->get();
		
		foreach($subsubcategory_result as $s) {
			$class = '';
			if($s->id == $subsubcategory)
				$class = ' active';
				
			$subsubcategories[$s->slug] = array('id'=>$s->id,'title'=>$s->title,'class'=>$class,'count'=>$s->c);
		}
		
		return $subsubcategories;
	}
	
	public function get_subsubcategory_label($subcategory) {
		//$subcategory = strtolower($subcategory);
		
		if(is_numeric($subcategory)) {
			$this->db->where('id',$subcategory);
			$subcategory_result = $this->db->get('subcategories');
		
			$subcategory = $subcategory_result[0]->slug;
		}
		
		$defaults = array('cars'=>'Make','motorbikes'=>'Make','dogs'=>'Breed');
		$default = 'Choose';
		if(array_key_exists($subcategory,$defaults))
			$default = $defaults[$subcategory];
			
		return $default;
	}
	
	public function get_items($category='',$subcategory='',$subsubcategory='',$page=1) {
		
		if(!empty($category)) {
			$this->db->where('slug',$category);
			$category_result = $this->db->get('categories');
			$category_id = $category_result[0]->id;

			$subcategory_result = $this->db->query("select * from subcategories where slug in(SELECT slug FROM `subcategories` WHERE category_id = ".$category_id.")");
			$arr = $subcategory_result->result_array(FALSE);
			$subcategory_id = array();
			foreach($arr as $a)
				$subcategory_id[] = $a['id'];
		}
		
		if(!empty($subcategory)) {
			//if($dupe) {
				$this->db->where(array('slug'=>$subcategory));
				$subcategory_result = $this->db->get('subcategories');
				//$subcategory_id = $subcategory_result[0]->id;
				$arr = $subcategory_result->result_array(FALSE);
				$subcategory_id = array();
				foreach($arr as $a)
					$subcategory_id[] = $a['id'];
					
				//print_r($subcategory_id);
			//}
			/*
			else {
				$this->db->where(array('slug'=>$subcategory,'category_id'=>$category_id));
				$subcategory_result = $this->db->get('subcategories');
				$subcategory_id = $subcategory_result[0]->id;
			}
			*/
		}
		
		if(!empty($subsubcategory)) {
			$this->db->where(array('slug'=>$subsubcategory,'subcategory_id'=>$subcategory_id[0]));
			$subsubcategory_result = $this->db->get('subsubcategories');
			$subsubcategory_id = $subsubcategory_result[0]->id;
		}
		
		$this->db->from('items');
		$this->db->select('SQL_CALC_FOUND_ROWS items.item_id, users.id, users.name, users.phone, items.*, media.media');
		if(!empty($subsubcategory_id))
			$this->db->where(array('category_id'=>$category_id,'subcategory_id'=>$subcategory_id[0],'subsubcategory_id'=>$subsubcategory_id));
		else if(!empty($subcategory_id)) {
			//if($dupe) 
				$this->db->in('subcategory_id',$subcategory_id);
			//else
			//	$this->db->where(array('category_id'=>$category_id,'subcategory_id'=>$subcategory_id));
		}
		else if(!empty($category_id))
			$this->db->where(array('category_id'=>$category_id));	
		$this->db->where(array('active'=>1,'sold'=>0));
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		//$this->db->groupby('media.item_id');
		$this->db->limit(15,($page-1)*15);
		$this->db->orderby('publish_timestamp', 'desc');
		
		return $this->db->get();
	}
	
	public function get_sold_items($page=1) {
		$this->db->from('items');
		$this->db->select('SQL_CALC_FOUND_ROWS items.item_id, users.id, users.name, users.phone, items.*, media.media');	
		$this->db->where(array('sold'=>1));
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		//$this->db->groupby('media.item_id');
		$this->db->limit(15,($page-1)*15);
		$this->db->orderby('sold_timestamp', 'desc');
		
		return $this->db->get();
	}
	
	public function current_result_count() {
		return $this->db->query('select FOUND_ROWS() AS num_rows')->current()->num_rows;
	}
	
	public function get_featured_items($category) {
		$this->db->where('slug',$category);
		$category_result = $this->db->get('categories');
		$category_id = $category_result[0]->id;
		
		$this->db->from('items');
		$this->db->select('users.id, users.name, users.phone, items.*, media.media');
		$this->db->where(array('category_id'=>$category_id));
		$this->db->where(array('active'=>1,'sold'=>0));
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		//$this->db->groupby('media.item_id');
		$this->db->limit(10);
		$this->db->orderby('publish_timestamp', 'desc');
		
		return $this->db->get();
	}
	
	public function search_items($term,$desc=false,$page=1) {
		if(!empty($term)) {
			//if(strlen($term) < 4) {
				$query = " SELECT SQL_CALC_FOUND_ROWS `items`.`item_id`, `users`.`id`, `users`.`name`, `users`.`phone`, `items`.*, `media`.`media`";
				$query .= " FROM (`items`)";
				$query .= " INNER JOIN `users` ON (`users`.`id` = `items`.`user_id`)";
				$query .= " LEFT JOIN `media` ON (`media`.`item_id` = `items`.`item_id`)";
				if($desc)
					$query .= " WHERE (items.title regexp '[[:<:]]".$term."s?[[:>:]]' or items.description regexp '[[:<:]]".$term."s?[[:>:]]')";
				else
					$query .= " WHERE (items.title regexp '[[:<:]]".$term."s?[[:>:]]')";
				$query .= " AND `active` = '1' AND `sold` = '0'";
				$query .= " ORDER BY `publish_timestamp` DESC LIMIT 0, 15";
				
				$res = $this->db->query($query);
				//if($desc)
				//	$this->db->where('items.title regexp \'[[:<:]]'.$term.'[[:>:]]\' or items.description regexp \'[[:<:]]'.$term.'[[:>:]]\'');
				//else
				//	$this->db->where('items.title regexp \'[[:<:]]'.$term.'[[:>:]]\'');
			/*
			}
			else {
				$this->db->from('items');
				$this->db->select('SQL_CALC_FOUND_ROWS items.item_id, users.id, users.name, users.phone, items.*, media.media');
				if($desc)
					$this->db->where('MATCH(items.title, items.description) AGAINST (\''.$term.'\')');
				else
					$this->db->where('MATCH(items.title) AGAINST (\''.$term.'\')');
			
			
				$this->db->where(array('active'=>1,'sold'=>0));
				$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
				$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
				//$this->db->groupby('media.item_id');
				$this->db->limit(15,($page-1)*15);
				$this->db->orderby('publish_timestamp', 'desc');
				$res = $this->db->get();
			}
			*/
			//print_r($res);
			return $res;
		}
		else
			return array();
	}
	
	public function get_saved_items($saved_json,$page=1) {
		$this->db->from('items');
		$this->db->select('SQL_CALC_FOUND_ROWS items.item_id, users.id, users.name, users.phone, items.*, media.media');
		$this->db->in('items.item_id',$saved_json);
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		//$this->db->groupby('media.item_id');
		$this->db->limit(15,($page-1)*15);
		$this->db->orderby('publish_timestamp', 'desc');
		
		return $this->db->get();
	}
	
	/*
	public function get_items_by_location($location,$page=0) {
		$this->db->from('items');
		$this->db->select('users.id, users.name, users.phone, items.*, media.src as image');
		$this->db->where(array('users.location'=>$location);
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		$this->db->groupby('media.item_id');
		$this->db->limit(10,$page*10);
		$this->db->orderby('timestamp', 'desc');
		
		return $this->db->get();
	}
	*/
	
	/**
	 * Create fav categories/images for given array
	 * @return the result object
	 */
	public function build_favs($favs) {
		// $favs = array_filter($favs, function($a){return $a > 0;}); // use anonymous function when server gets PHP 5.3
		$filter_function = create_function('$a','return $a > 0;');
		$favs = array_filter($favs, $filter_function);
		$favs_arr = array();
		foreach($favs as $k=>$v) {
			$this->db->from('subcategories');
			$this->db->select('subcategories.category_id, subcategories.image, categories.slug as cat, subcategories.slug as subcat');
			$this->db->where('subcategories.slug',$k);
			$this->db->join('categories',array('categories.id'=>'subcategories.category_id'),'','INNER');
			$result = $this->db->get();
			$favs_arr[] = $result[0];
		}
		return $favs_arr;
	}
}
?>