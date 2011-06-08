<?php defined('SYSPATH') OR die('No direct access allowed.');
class Search_Controller extends Template_Controller {

	public $template = 'adshop/template';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = TRUE;
		$this->template->display_favs = TRUE;
		$this->template->favs = $this->get_favs();
		$this->template->counties = $this->counties;
	}

	public function index($page=0) {
		$term = $this->input->get('q');
		$desc = $this->input->get('desc');
		$this->template->content = new View('view_content');
		$this->template->title = 'Results for '.$term;
		
		$this->template->content->subcategories = FALSE;
		$this->template->content->save_list = FALSE;
		$this->template->content->no_items_message = 'No items were found. Please try another search or <a href="'.url::base().'">return home</a>.';
		
		$this->template->search_term = $term;
		
		if(!empty($page))
			$this->template->title .= ' - Page '.$page;
		
		$this->template->menu = parent::build_menu();
		
		$view_model = new View_Model;
		$this->template->content->items = $view_model->search_items($term,$desc);
	}
}