<?php defined('SYSPATH') OR die('No direct access allowed.');
class Place_Controller extends Template_Controller {

	public $template = 'adshop/template';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = FALSE;
		$this->template->display_favs = FALSE;
		$this->template->favs = '';
		$this->template->counties = '';
	}

	public function index($item_id='') {			
		$this->template->content = new View('place_content');
		$this->template->title = 'Place Ad';
		$this->template->content->editmode = FALSE;
		
		$item_model = new Item_Model;
		$item_model->delete_temp_media();
	
		$this->template->menu = '';
		$this->template->content->categories = parent::build_menu();
		
		$steps = array('title-and-description'=>array('title'=>'Title and Description','active'=>''),
					   'category-and-photos'=>array('title'=>'Category and Photos','active'=>''),
					   'your-details'=>array('title'=>'Your Details','active'=>''),
					   'review-ad'=>array('title'=>'Review Ad','active'=>''),
					   'finish-and-pay'=>array('title'=>'Finish and Pay','active'=>'')
					   );
		if(!empty($step) && array_key_exists($step, $steps))
			$steps[$step]['active'] = ' active';
		else
			$steps['title-and-description']['active'] = ' active';
		
		$home_model = new Home_Model;
		$this->template->content->counties = $home_model->list_counties();
		
		$isAdmin = false;
		
		if(!empty($item_id)) {
			$this->template->title = 'Edit or Remove Ad';
			$this->template->content->editmode = TRUE;
			$item_model = new Item_Model;
			$item = $item_model->get_item($item_id);
			
			$this->template->content->item = $item;
			$this->template->content->media = $item_model->get_media($item_id);
			$this->template->content->extra_attributes = !empty($item->extra_attributes) ? json_decode($item->extra_attributes,true) : array();
			
			$view_model = new View_Model;
			$this->template->content->subcategories = $view_model->get_subcategories($item->cat);
			$this->template->content->subsubcategories = $view_model->get_subsubcategories($item->subcat);
			$this->template->content->subsubcategory_label = $view_model->get_subsubcategory_label($item->subcategory_id);
			
			if(Auth::instance()->logged_in()) {
				$user = Auth::instance()->get_user();
				if($user->has($this->admin_role))
					$isAdmin = true;
					
				$this->template->content->user = $user;
			}
			
			$steps = array();
		}
		else {
			if(Auth::instance()->logged_in())
				Auth::instance()->logout();
		}
		
		$this->template->content->steps = $steps;
		$this->template->content->isAdmin = $isAdmin;
	}
	
	public function edit($item_id) {
		if(Auth::instance()->logged_in()) {
			$user = Auth::instance()->get_user();
			$user_model = new User_Model;
			//$item = $user_model->get_my_ad();
			$owner = $user_model->get_ad_owner($item_id);
			if($user->username == $owner || $user->has($this->admin_role))
				$this->index($item_id);
			else
				url::redirect('/user/login?action=edit&item='.$item_id);
		}
		else
			url::redirect('/user/login?action=edit&item='.$item_id);
	}
	
	public function finish() {
		$this->template->content = new View('finish_content');
		$this->template->title = 'Place Ad';
		$this->template->content->editmode = FALSE;	
		$this->template->menu = '';
		$this->template->content->categories = parent::build_menu();
		$steps = array();
		$this->template->content->steps = $steps;
	
		$this->template->content->success_msg = 'Your ad has been posted and payment accepted. Thank you!<br /><a href="'.url::base().'user/myAd">View Ad</a>';
	}
	
	public function remove($item_id) {
		$this->template->content = new View('remove_content');
		$this->template->title = 'Remove Ad';
		$this->template->content->editmode = FALSE;	
		$this->template->menu = '';
		$this->template->content->categories = parent::build_menu();
		
		if(Auth::instance()->logged_in()) {
			$user = Auth::instance()->get_user();
			$user_model = new User_Model;
			//$item = $user_model->get_my_ad();
			
			$owner = $user_model->get_ad_owner($item_id);
			if($user->username == $owner || $user->has($this->admin_role)) {
				$item_model = new Item_Model;
				$item = $item_model->get_item($item_id);
	
				$this->template->content->item = $item;
				
				$form = $_POST;
				if($form) {
					$item_model = new Item_Model;
					$item_model->remove($item->item_id);
					url::redirect('/user/myAd');
				}
			}
			else
				url::redirect('/user/login?action=remove&item='.$item_id);
		}
		else
			url::redirect('/user/login?action=remove&item='.$item_id);
		
		$steps = array();
		$this->template->content->steps = $steps;
	}
}