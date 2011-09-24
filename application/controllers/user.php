<?php defined('SYSPATH') OR die('No direct access allowed.');

class User_Controller extends Template_Controller {
	
	public $template = 'adshop/template';
	
	function __construct(){
		parent::__construct();
		$this->template->display_search = TRUE;
		$this->template->display_favs = FALSE;
		$this->template->favs = '';
		$this->template->menu = '';	
		$this->template->counties = $this->counties;
	}
	
	public function index() {
		if(Auth::instance()->logged_in())
			url::redirect('/user/myAds');
		else
			url::redirect('/user/login');
	}
	
	public function login($role='') {
		$this->template->display_search = FALSE;
		$this->template->counties = FALSE;
		$this->template->content = new View('user_login_content');
		$this->template->title = 'Log In';
		
		//$redirect = $this->input->post('redirect');
		$post_action = $this->input->post('action');
		$post_action = empty($post_action) ? $this->input->get('action') : $post_action;
		$action = !empty($post_action) ? $post_action : 'myAd';
		$item_id = $this->input->post('item_id');
		if(empty($item_id))
			$item_id = $this->input->get('item');
		$edit = !empty($item_id) ? '/place/edit/'.$item_id.'#step_4' : '/user/myAd/edit';
		$renew = !empty($item_id) ? '/renew/'.$item_id : '/renew';
		
		$login_labels = array('myAd'=>'View Ads','renew'=>'Renew Ad','edit'=>'Edit or Remove Ad','remove'=>'Remove Ad','change_password'=>'Update Password');
		$redirects = array('myAd'=>'/user/myAds','renew'=>$renew,'edit'=>$edit,'remove'=>$edit);
		$this->template->content->login_label = $login_labels[$action];
		$this->template->content->action = $action;
		$this->template->content->item_id = $item_id;
		$redirect = array_key_exists($action,$redirects) ? $redirects[$action] : '/user/myAds';
	
		$form = $_POST;
		if($form) {
			// Load the user
			$user = ORM::factory('user', $form['username']);
			if($user->loaded) {
				if(Auth::instance()->login($user->username, $form['password'])) {
					if(isset($redirect))
						url::redirect($redirect);
					else
						url::redirect('');
				}		
				else {
					$this->template->content->error_msg = 'Error: you have entered the wrong password.';
					$this->template->content->action = $action;
					$this->template->content->item_id = $item_id;
					$this->template->content->email = $this->input->post('username');
				}
			}
			else {
				$this->template->content->error_msg = 'Error: please check your e-mail and password.';
				$this->template->content->action = $action;
				$this->template->content->item_id = $item_id;
				$this->template->content->email = $this->input->post('username');
			}
		}		
	}
	
	/*
	public function profile() {
		if(!Auth::instance()->logged_in())
			url::redirect('/user/login');
	
		$this->template->content = new View('user_profile_content');
		$this->template->title = 'My Profile';
		$this->user = Auth::instance()->get_user();
		$this->template->content->user = $this->user;
		
		$form = $_POST;
		if($form) {	    
			$user = ORM::factory('user',$this->user->id);
			$user->name = $this->input->post('name');
	    	$user->username = $this->input->post('username');
	    	$user->phone = $this->input->post('phone');
	    	$user->location = $this->input->post('location');
	    	if($this->input->post('password'))
	    		$user->password = Auth::instance()->hash_password($this->input->post('password'));
	    		
	    	$user->save();
	    	url::redirect(url::current());
		}
	}
	*/
	
	public function myAd($edit='') {
		if(Auth::instance()->logged_in()) {
			$user_model = new User_Model;
			$ad_count = count($user_model->get_my_ads());
			if($ad_count > 1)
				url::redirect('/user/myAds');
			$ad = $user_model->get_my_ad();
			if($ad) {
				if(!empty($edit))
					url::redirect('/place/edit/'.$ad->item_id.'#step_4');
				else
					url::redirect('/item/'.$ad->item_id.'/'.url::title($ad->title));
			}
			else
				url::redirect('');
		}
		else
			url::redirect('/user/login');
	}
	
	public function myAds() {
		if(Auth::instance()->logged_in()) {
			$user_model = new User_Model;
			$ad_count = count($user_model->get_my_ads());
			/*
			if($ad_count == 1) {
				$ad = $user_model->get_my_ad();
				url::redirect('/item/'.$ad->item_id.'/'.url::title($ad->title));
			}
			*/
			$page = $this->uri->segment('page',1);
			$this->template->content = new View('view_content');
			$this->template->title = 'My Ads';
		
			$this->template->menu = parent::build_menu();
		
			$this->template->content->no_items_message = 'You have no live ads currently.';
			$this->template->content->save_list = FALSE;
			
			$user_model = new User_Model;
			$ads = $user_model->get_my_ads($page);
			
			$item_count = $user_model->current_result_count();
		
			$this->template->content->subcategories = '';

			$this->template->content->items = $ads;
			
			$this->template->content->sold_ads_count = $user_model->count_my_sold_ads();			
			
			$this->pagination = new Pagination(array(
			    'uri_segment'    => 'page',
			    'total_items'    => $item_count,
			    'items_per_page' => 15,
			    'style'          => 'adshop',
			    'auto_hide'		 => TRUE
			));	
		}
		else
			url::redirect('/user/login');
	}

	public function mySoldAds() {
		if(Auth::instance()->logged_in()) {		
			$page = $this->uri->segment('page',1);	
			$this->template->content = new View('view_content');
			$this->template->title = 'My Ads';
		
			$this->template->menu = parent::build_menu();
		
			$this->template->content->no_items_message = 'You have no sold ads currently.';
			$this->template->content->save_list = FALSE;
			
			$user_model = new User_Model;
			$ads = $user_model->get_my_sold_ads($page);
			
			$item_count = $user_model->current_result_count();
		
			$this->template->content->subcategories = '';

			$this->template->content->items = $ads;
			
			$this->pagination = new Pagination(array(
			    'uri_segment'    => 'page',
			    'total_items'    => $item_count,
			    'items_per_page' => 15,
			    'style'          => 'adshop',
			    'auto_hide'		 => TRUE
			));	
		}
	}
	
	public function logout() {
		Auth::instance()->logout();
		url::redirect('');
	}
	
	public function new_password($user='') {
		$this->template->display_search = FALSE;
		$this->template->counties = FALSE;
		$this->template->content = new View('user_password_content');
		$this->template->title = 'Change Password';
		$this->template->content->user = 0;
	    $user_arr = explode('_',base64_decode($user));
		$orm_user = ORM::factory('user', $user_arr[0]);
		if(isset($user_arr[0]) && isset($user_arr[1]) && ((time() - $user_arr[1]) / 86400) < 7 && $orm_user->last_login < $user_arr[1]) {
			$this->template->content->user = $this->input->xss_clean($user);
			$form = $_POST;
			if($form) {
				// Load the user
				$user_arr = explode('_',base64_decode($form['user']));
				$user_id = $user_arr[0];
				$pwd = $user_arr[2];
				$user = ORM::factory('user', $user_id);
				if($user->loaded && $user->last_login < $user_arr[1]) {
					$salt = Auth::instance()->find_salt($user->password);
					$current_password = Auth::instance()->hash_password($pwd,$salt);
	
					if($user->password == $current_password) {
						$user->password = Auth::instance()->hash_password($form['new_password']);
						$user->save();
						if(Auth::instance()->login($user->username, $form['new_password'])) {
							$this->template->content->success_msg = 'Your password has been updated';
						}
					}
					else
						$this->template->content->error_msg = 'Error: current password is incorrect.';
				}
				else
					$this->template->content->error_msg = 'Error: user not found or link has expired. Please <a href="/user/login">try again</a>.';
			}
		}
		else
			$this->template->content->error_msg = 'Error: user not found or reset link has expired. Please <a href="/user/login">try again</a>.';
	}
	
	public function delete_item() {
		// need to verify email/password
		$item_id = $this->input->post('item_id');
		$item_model = new Item_Model;
		$item_model->delete($item_id);
		url::redirect('/user/myAds');
	}
}

?>
