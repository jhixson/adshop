<?php defined('SYSPATH') OR die('No direct access allowed.');
class Renew_Controller extends Template_Controller {

	public $template = 'adshop/template';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = FALSE;
		$this->template->display_favs = FALSE;
		$this->template->favs = '';
		$this->template->counties = '';
	}

	public function index($item_id='') {
		$this->template->content = new View('renew_content');
		$this->template->title = 'Renew Ad';
		
		$this->template->menu = '';
		$this->template->content->categories = parent::build_menu();
			
		if(Auth::instance()->logged_in()) {
			$user_model = new User_Model;
			$ad_count = count($user_model->get_my_ads());
			if($ad_count > 1 && empty($item_id))
				url::redirect('/user/myAds');
			
			if(empty($item_id)) {
				$item = $user_model->get_my_ad();
				$item_id = $item->item_id;
			}
			
			$item_model = new Item_Model;
			$item = $item_model->get_item($item_id);

			$this->template->content->item = $item;
		}
		else
			url::redirect('/user/login?action=renew&item='.$item_id);
	}
}