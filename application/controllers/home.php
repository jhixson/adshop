<?php defined('SYSPATH') OR die('No direct access allowed.');
class Home_Controller extends Template_Controller {

	public $template = 'adshop/template';
	public $no_items_message = 'No items were found in this category. Please try another.';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = TRUE;
		$this->template->display_favs = TRUE;
		$this->template->favs = $this->get_favs();
		$this->template->counties = $this->counties;
	}

	public function index() {
		$page = $this->uri->segment('page',1);
		
		$this->template->content = new View('view_content');
		$this->template->title = 'Ireland\'s simplest Ad Website.';
		
		$this->template->no_rollover = true;
		
		$this->template->menu = parent::build_menu();
		
		$this->template->content->subcategories = '';
		$this->template->content->save_list = FALSE;
		$this->template->content->no_items_message = $this->no_items_message;
		
		$view_model = new View_Model;
		
		$items = $view_model->get_items('','','',$page);
		$item_count = $view_model->current_result_count();
	
		$this->template->content->items = $items;

		$this->pagination = new Pagination(array(
		    'uri_segment'    => 'page',
		    'total_items'    => $item_count,
		    'items_per_page' => 15,
		    'style'          => 'adshop',
		    'auto_hide'		 => TRUE
		));
	}
	
	public function sold() {
		$page = $this->uri->segment('page',1);
		
		$this->template->content = new View('view_content');
		$this->template->title = 'Ireland\'s Classifieds!';
		
		$this->template->no_rollover = true;
		
		$this->template->menu = parent::build_menu();
		
		$this->template->content->subcategories = '';
		$this->template->content->save_list = FALSE;
		
		$view_model = new View_Model;
		
		$items = $view_model->get_sold_items($page);
		$item_count = $view_model->current_result_count();
	
		$this->template->content->items = $items;

		$this->pagination = new Pagination(array(
		    'uri_segment'    => 'page',
		    'total_items'    => $item_count,
		    'items_per_page' => 15,
		    'style'          => 'adshop',
		    'auto_hide'		 => TRUE
		));

	}
}
