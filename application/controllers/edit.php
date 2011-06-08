<?php defined('SYSPATH') OR die('No direct access allowed.');
class Edit_Controller extends Template_Controller {

	public $template = 'adshop/template';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = FALSE;
		$this->template->display_favs = FALSE;
		$this->template->favs = '';
		$this->template->counties = '';
	}

	public function index($item_id,$step='') {
		$this->template->content = new View('edit_content');
		$this->template->title = 'Edit or Remove Ad';
		
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
			
		$this->template->content->steps = $steps;
		
		$home_model = new Home_Model;
		$this->template->content->counties = $home_model->list_counties();
		
		$item_model = new Item_Model;
		$item = $item_model->get_item($item_id);
		
		$this->template->content->item = $item;
		
		if(Auth::instance()->logged_in()) {
			$this->user = Auth::instance()->get_user();
			$this->template->content->user = $this->user;
		}
	}
	
}