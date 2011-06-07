<?php defined('SYSPATH') OR die('No direct access allowed.');
class Item_Controller extends Template_Controller {
	const ALLOW_PRODUCTION = FALSE;

	public $template = 'adshop/template';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = TRUE;
		$this->template->display_favs = TRUE;
		$this->template->favs = $this->get_favs();
		$this->template->counties = $this->counties;
	}
	
	public function index($item_id,$slug) {
		/*
		$cached_page = Cache::instance()->get('page-'.$item_id);
		if($cached_page) {
			die($cached_page);
		}
		else {
		*/
			$this->template->content = new View('item_content');
			
			$item_model = new Item_Model;
			$item = $item_model->get_item($item_id);

			//$item = $item_model->get_item_by_slug($slug);
		
			$page = $this->uri->segment('page',1);
		
			$segs = $this->uri->segment_array();
		
			$category = (isset($segs[2]) && $segs[2] != 'page' && !is_numeric($segs[2])) ? $segs[2] : $item->cat;
			$subcategory = (isset($segs[3]) && $segs[3] != 'page' && !is_numeric($segs[3]) && $segs[3] != url::title($item->title)) ? $segs[3] : $item->subcat;
		
			//echo request::referrer();
			$this->template->menu = parent::build_menu($category,$subcategory);
		
			$this->template->content->item = $item;
			$this->template->title = $item->title;
		
			$this->template->content->category = $category;
			$this->template->content->subcategory = $subcategory;
		
			if($item->sold)
				$this->template->item_sold = '1';
				
			$this->template->content->admin_role = $this->admin_role;
		
			$media = $item_model->get_media($item_id);
			$this->template->content->media = json_decode($media,true);
		
			$saved_json = json_decode(cookie::get('saved'),true);
			$this->template->content->is_saved = ($saved_json) && in_array($item_id,$saved_json);
		
			$item_model->add_view($item_id);
		
			$user_model =  new User_Model;
			$user_model->set_fav_cookie($subcategory);
			
			//Cache::instance()->set($item_id, $item, $slug, 3600);
			//Cache::instance()->set('page-'.$item_id, $this->template->render(), $slug, 3600);
		//}
	}
}
?>