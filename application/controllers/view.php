<?php defined('SYSPATH') OR die('No direct access allowed.');
class View_Controller extends Template_Controller {
	const ALLOW_PRODUCTION = FALSE;

	public $template = 'adshop/template';
	public $no_items_message = 'No items were found in this category. Please try another.';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = TRUE;
		$this->template->display_favs = TRUE;
		$this->template->favs = $this->get_favs();
		$this->template->counties = $this->counties;
	}

	public function index($category) {
		$page = $this->uri->segment('page',1);
		
		$segs = $this->uri->segment_array();
		
		$subcategory = (isset($segs[3]) && $segs[3] != 'page' && !is_numeric($segs[3])) ? $segs[3] : '';
		$subsubcategory = (isset($segs[4]) && $segs[4] != 'page' && !is_numeric($segs[4])) ? $segs[4] : '';
		
		$this->template->content = new View('view_content');
		$this->template->title = $this->menu[$category]['title'];
		
		if(!empty($page))
			$this->template->title .= ' - Page '.$page;
		
		$this->template->menu = parent::build_menu($category,$subcategory);
		
		$this->template->content->no_items_message = $this->no_items_message;
		$this->template->content->save_list = FALSE;
		
		$view_model = new View_Model;
		$this->template->content->category = $category;
		$this->template->content->subcategory = $subcategory;
		
		$subcategories = $view_model->get_subcategories($category,$subcategory);
		$this->template->content->subcategories = $subcategories;
		
		$total_subsubcategories = 0;
		
		if(!empty($subcategory)) {
			$subsubcategories = $view_model->get_subsubcategories($subcategory,$subsubcategory);
			$this->template->content->subsubcategories = $subsubcategories;
			
			foreach($subsubcategories as $s)
				$total_subsubcategories += $s['count'];
		}
		
		$this->template->content->subsubcategory_default = $view_model->get_subsubcategory_label($subcategory);
		
		$this->template->content->subsubcategory_total = $total_subsubcategories;
		
		//$dupe_categories = array('cds-dvds','all-lawnmowers','horses');
		//$dupe = in_array($subcategory,$dupe_categories);
		$items = $view_model->get_items($category,$subcategory,$subsubcategory,$page);
		$item_count = $view_model->current_result_count();
		
		$this->template->content->item_count = $total_subsubcategories;
	
		$this->template->content->items = $items;

		$this->pagination = new Pagination(array(
		    'uri_segment'    => 'page',
		    'total_items'    => $item_count,
		    'items_per_page' => 15,
		    'style'          => 'adshop',
		    'auto_hide'		 => TRUE
		));
	}
	
	public function saved() {
		$page = $this->uri->segment('page',1);
		
		$this->template->content = new View('view_content');
		$this->template->title = 'Saved Ads';
	
		$this->template->title .= !empty($page) ? ' - Page '.$page : '';
	
		$this->template->menu = parent::build_menu();
		
		$this->template->content->no_items_message = 'To save an ad, use the \'Save Ad\' button on the lower right of any ad page.';
		$this->template->content->save_list = TRUE;
		
		$view_model = new View_Model;
	
		$this->template->content->subcategories = '';
		
		$saved_json = json_decode(cookie::get('saved'),true);
		$this->template->content->items = ($saved_json) ? $view_model->get_saved_items($saved_json,$page) : array();
		
		$item_count = $view_model->current_result_count();
		
		$this->pagination = new Pagination(array(
		    'uri_segment'    => 'page',
		    'total_items'    => $item_count,
		    'items_per_page' => 15,
		    'style'          => 'adshop',
		    'auto_hide'		 => TRUE
		));		
	}
	
	public function county($location,$page=0) {
		$this->template->content = new View('view_content');
		$this->template->title = 'Stuff in '.$name;
		
		$this->template->content->no_items_message = $this->no_items_message;
		
		$view_model = new View_Model;	
		$this->template->content->items = $view_model->get_items_by_location($location);
	}
}