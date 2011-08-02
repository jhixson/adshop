<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller extends Controller_Core {	
	
	public $menu = array();
	public $favs = array();
	public $counties = array();
	public $admin_role = '';
	
	public function __construct() {
		parent::__construct();
		$menu_model = new Menu_Model;
		$menu_items = $menu_model->get_menu();
		foreach($menu_items as $m)
			$this->menu[$m->slug] = array('id'=>$m->id,'title'=>$m->title,'active'=>'');
			
		$home_model = new Home_Model;
		$this->counties = $home_model->list_counties();
			
		$this->session = Session::instance();
		
		$this->admin_role = ORM::factory('role', array('name' => 'admin'));
				
		$u = $this->input->get('u');
		if(isset($u)) {
			$current = url::current();
			preg_match('/\d+$/',$current,$matches);
			//print_r($matches);
			$user_arr = explode(':',base64_decode($u));
      //print_r($user_arr);
			//die();
			if(count($user_arr) == 2 && count($matches) == 1) {
				$user = $user_arr[0];
				$ad = $user_arr[1];
				if($ad == $matches[0]) {
					$user_model = new User_Model;
					$owner = $user_model->get_ad_owner($ad);
          $orm_user = ORM::factory('user',$user);
          if($owner == $user || $orm_user->has($this->admin_role))
						Auth::instance()->force_login($user);
					else
						url::redirect('/user/login');
				}
				else
					url::redirect('/user/login');
			}
			else
				url::redirect('/user/login');
		}
	}
	
	public function build_menu($active='',$sub='') {
		$menu_list = $this->menu;		

		if(!empty($active) && array_key_exists($active, $menu_list)) {
			$menu_list[$active]['active'] = ' active';
			if(!empty($sub))
				$menu_list[$active]['active'] .= ' sub';	
		}

		return $menu_list;
	}
	
	public function get_favs() {
		$view_model = new View_Model;
		$favs_json = json_decode(cookie::get('favs'),true);
		if(!empty($favs_json)) {
			$this->favs = $view_model->build_favs($favs_json);
		}
		return $this->favs;
	}
}

?>
