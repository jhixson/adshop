<?php defined('SYSPATH') or die('No direct script access.');

class User_Model extends ORM {
	protected $has_and_belongs_to_many = array('roles');
 
	public function unique_key($id = NULL) {
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id) ) {
			return 'username';
		}
 
		return parent::unique_key($id);
	}
	
	public function register($user_arr) {
		//attemp to load an existing user, otherwise create a new one
		$user = ORM::factory('user', $user_arr['username']);
		if($user->loaded) {
			Auth::instance()->force_login($user->username);
			return $user->id;
		}
		else {
			$user = ORM::factory('user');
			$user->name = $user_arr['name'];
	    	$user->username = $user_arr['username'];
	    	$user->phone = $user_arr['phone'];
	    	//$user->location = $this->input->post('location');
		    $user->password = Auth::instance()->hash_password($user_arr['password']);
		 
		    // if the user was successfully created...
		    if ($user->add(ORM::factory('role', 'login')) AND $user->save()) {
		        if(Auth::instance()->login($user->username, $user_arr['password']))
		        	return $user->id;
		        else
		        	return -1;
		    }
		    else
		    	return -1;
		}
	}
	
	public function valid_user($email,$logged_in=false) {
		if($logged_in)
			$is_logged_in =  Auth::instance()->logged_in();
		else
			$is_logged_in = true;
		$user = ORM::factory('user', $email);
		return ($user->loaded && $is_logged_in);
	}
	
	public function set_fav_cookie($subcategory) {
		$favs_json = json_decode(cookie::get('favs'),true);
		$favs_json = is_array($favs_json) ? $favs_json : array();
		if(isset($favs_json[$subcategory]) && is_array($favs_json[$subcategory]))
			$favs_json[$subcategory]['count']++;
		else {
			$favs_json[$subcategory] = array();
			$favs_json[$subcategory]['count'] = 1;
		}
		
			
		$favs_json[$subcategory]['time'] = time();
		
		arsort($favs_json); // sort favs from most viewed to least and by most recently viewed
		$favs_json = array_slice($favs_json, 0, 5);
		
		$cookie_params = array(
			'name'   => 'favs',
			'value'  => json_encode($favs_json),
			'expire' => '604800',
			'path' => '/'
		);
		cookie::set($cookie_params);
	}
	
	public function set_saved_cookie($item_id) {
		$resp = 'saved';
		$saved_json = json_decode(cookie::get('saved'),true);
		$saved_json = ($saved_json) ? $saved_json : array();
		if(!in_array($item_id,$saved_json))
			$saved_json[] = $item_id;
		else {
			unset($saved_json[array_search($item_id,$saved_json)]);
			$resp = 'removed';
		}
		
		$cookie_params = array(
			'name'   => 'saved',
			'value'  => json_encode($saved_json),
			'expire' => '604800',
			'path' => '/'
		);
		cookie::set($cookie_params);
		return $resp;
	}
	
	public function get_my_ad() {
		$user_id = Auth::instance()->get_user()->id;
		$this->db->where('user_id',$user_id);
		$item = $this->db->get('items');
		return $item[0];
	}
	
	public function get_my_ads($page=1,$limit=15) {
		$user_id = Auth::instance()->get_user()->id;	
		$this->db->from('items');
		$this->db->select('SQL_CALC_FOUND_ROWS users.id, users.name, users.phone, items.*, media.media');
		$this->db->where('user_id',$user_id);
		$this->db->where('sold',0);
		$this->db->where(array('publish_timestamp !=' => 0)); // publish_timestamp gets set on the payment callbacks
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		$this->db->orderby('publish_timestamp', 'desc');
		$this->db->limit($limit,($page-1)*$limit);
		
		return $this->db->get();
	}
	
	public function count_my_sold_ads() {
		$user_id = Auth::instance()->get_user()->id;	
		$count = $this->db->count_records('items', array('user_id'=>$user_id,'sold'=>1));
		return $count;
	}
	
	public function get_my_sold_ads($page=1,$limit=15) {
		$user_id = Auth::instance()->get_user()->id;	
		$this->db->from('items');
		$this->db->select('SQL_CALC_FOUND_ROWS users.id, users.name, users.phone, items.*, media.media');
		$this->db->where('user_id',$user_id);
		$this->db->where('sold',1);
		$this->db->join('users',array('users.id'=>'items.user_id'),'','INNER');
		$this->db->join('media',array('media.item_id'=>'items.item_id'),'','LEFT');
		$this->db->limit($limit,($page-1)*$limit);
		$this->db->orderby('sold_timestamp', 'desc');
		
		return $this->db->get();
	}
	
	public function get_ad_owner($item_id) {
		$this->db->from('items');
		$this->db->select('users.id, users.username, items.item_id, items.user_id');
		$this->db->join('users',array('users.id'=>'items.user_id'),'INNER');
		$this->db->where(array('items.item_id'=>$item_id));
		$item = $this->db->get();
		return $item[0]->username;
	}
	
	public function get_ad_owner_id($item_id) {
		$this->db->from('items');
		$this->db->select('users.id, users.username, items.item_id, items.user_id');
		$this->db->join('users',array('users.id'=>'items.user_id'),'INNER');
		$this->db->where(array('items.item_id'=>$item_id));
		$item = $this->db->get();
		return $item[0]->id;
	}
	
	public function send_email($to, $from, $subject, $message) { 
		return email::send($to, $from, $subject, $message, TRUE);
	}
	
	public function current_result_count() {
		return $this->db->query('select FOUND_ROWS() AS num_rows')->current()->num_rows;
	}
}

?>