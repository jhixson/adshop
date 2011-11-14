<?php defined('SYSPATH') OR die('No direct access allowed.');
class View_Controller extends Template_Controller {

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
		$this->template->content->subsubcategory = $subsubcategory;
		
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
		
		$wikipedia_links = array(
		  "afghan-hound" => "http://en.wikipedia.org/wiki/Afghan Hound",
      "airedale-terrier" => "http://en.wikipedia.org/wiki/Airedale Terrier",
      "akita" => "http://en.wikipedia.org/wiki/Akita (dog)",
      "alaskan-malamute" => "http://en.wikipedia.org/wiki/Alaskan Malamute",
      "basenji" => "http://en.wikipedia.org/wiki/Basenji",
      "basset-hound" => "http://en.wikipedia.org/wiki/Basset Hound",
      "beagle" => "http://en.wikipedia.org/wiki/Beagle",
      "bernese-mountain-dog" => "http://en.wikipedia.org/wiki/Bernese Mountain Dog",
      "bichon-frise" => "http://en.wikipedia.org/wiki/Bichon Frise",
      "bloodhound" => "http://en.wikipedia.org/wiki/Bloodhound",
      "border-terrier" => "http://en.wikipedia.org/wiki/Border Terrier",
      "boston-terrier" => "http://en.wikipedia.org/wiki/Boston Terrier",
      "boxer" => "http://en.wikipedia.org/wiki/Boxer (dog)",
      "bull-terrier" => "http://en.wikipedia.org/wiki/Bull Terrier",
      "bulldog" => "http://en.wikipedia.org/wiki/Bulldog",
      "bullmastiff" => "http://en.wikipedia.org/wiki/Bullmastiff",
      "cairn-terrier" => "http://en.wikipedia.org/wiki/Cairn Terrier",
      "cavalier-king-charles-spaniel" => "http://en.wikipedia.org/wiki/Cavalier King Charles Spaniel",
      "chihuahua" => "http://en.wikipedia.org/wiki/Chihuahua (dog)",
      "chinese-shar-pei" => "http://en.wikipedia.org/wiki/Chinese Shar-Pei",
      "chow-chow" => "http://en.wikipedia.org/wiki/Chow Chow",
      "cocker-spaniel" => "http://en.wikipedia.org/wiki/Cocker Spaniel",
      "collie" => "http://en.wikipedia.org/wiki/Collie",
      "dachshund" => "http://en.wikipedia.org/wiki/Dachshund",
      "dalmatian" => "http://en.wikipedia.org/wiki/Dalmatian (dog)",
      "doberman-pinscher" => "http://en.wikipedia.org/wiki/Doberman Pinscher",
      "dogue-de-bordeaux" => "http://en.wikipedia.org/wiki/Dogue de Bordeaux",
      "german-pointer" => "http://en.wikipedia.org/wiki/German Pointer",
      "german-shepherd-alsatian" => "http://en.wikipedia.org/wiki/German Shepherd Dog",
      "golden-retriever" => "http://en.wikipedia.org/wiki/Golden Retriever",
      "great-dane" => "http://en.wikipedia.org/wiki/Great Dane",
      "greyhound" => "http://en.wikipedia.org/wiki/Greyhound",
      "irish-terrier" => "http://en.wikipedia.org/wiki/Irish Terrier",
      "irish-water-spaniel" => "http://en.wikipedia.org/wiki/Irish Water Spaniel",
      "irish-wolfhound" => "http://en.wikipedia.org/wiki/Irish Wolfhound",
      "jack-russell-terrier" => "http://en.wikipedia.org/wiki/Jack Russell Terrier",
      "japanese-spitz" => "http://en.wikipedia.org/wiki/Japanese Spitz",
      "kerry-blue-terrier" => "http://en.wikipedia.org/wiki/Kerry Blue Terrier",
      "labrador-retriever" => "http://en.wikipedia.org/wiki/Labrador Retriever",
      "lhasa-apso" => "http://en.wikipedia.org/wiki/Lhasa Apso",
      "maltese" => "http://en.wikipedia.org/wiki/Maltese (dog)",
      "newfoundland" => "http://en.wikipedia.org/wiki/Newfoundland (dog)",
      "norfolk-terrier" => "http://en.wikipedia.org/wiki/Norfolk Terrier",
      "norwegian-elkhound" => "http://en.wikipedia.org/wiki/Norwegian Elkhound",
      "old-english-sheepdog" => "http://en.wikipedia.org/wiki/Old English Sheepdog",
      "papillon" => "http://en.wikipedia.org/wiki/Papillon (dog)",
      "pekingese" => "http://en.wikipedia.org/wiki/Pekingese",
      "pointer" => "http://en.wikipedia.org/wiki/Pointer (dog)",
      "pomeranian" => "http://en.wikipedia.org/wiki/Pomeranian (dog)",
      "poodle" => "http://en.wikipedia.org/wiki/Poodle",
      "pug" => "http://en.wikipedia.org/wiki/Pug",
      "puli" => "http://en.wikipedia.org/wiki/Puli",
      "rhodesian-ridgeback" => "http://en.wikipedia.org/wiki/Rhodesian Ridgeback",
      "rottweiler" => "http://en.wikipedia.org/wiki/Rottweiler",
      "samoyed" => "http://en.wikipedia.org/wiki/Samoyed (dog)",
      "schnauzer" => "http://en.wikipedia.org/wiki/Schnauzer",
      "scottish-terrier" => "http://en.wikipedia.org/wiki/Scottish Terrier",
      "setter" => "http://en.wikipedia.org/wiki/Setter",
      "border-collie-sheepdog" => "http://en.wikipedia.org/wiki/Border Collie",
      "shih-tzu" => "http://en.wikipedia.org/wiki/Shih Tzu",
      "siberian-husky" => "http://en.wikipedia.org/wiki/Siberian Husky",
      "springer-spaniel" => "http://en.wikipedia.org/wiki/English Springer Spaniel",
      "st-bernard" => "http://en.wikipedia.org/wiki/St. Bernard (dog)",
      "staffordshire-terrier" => "http://en.wikipedia.org/wiki/Staffordshire Bull Terrier",
      "tibetan-spaniel" => "http://en.wikipedia.org/wiki/Tibetan Spaniel",
      "weimaraner" => "http://en.wikipedia.org/wiki/Weimaraner",
      "welsh-corgi" => "http://en.wikipedia.org/wiki/Welsh Corgi",
      "west-highland-white-terrier" => "http://en.wikipedia.org/wiki/West Highland White Terrier",
      "whippet" => "http://en.wikipedia.org/wiki/Whippet",
      "yorkshire-terrier" => "http://en.wikipedia.org/wiki/Yorkshire Terrier"
    );
    
    $this->template->content->wikipedia_links = $wikipedia_links;

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
		
		$this->template->content->no_items_message = 'You can like ads for viewing later by selecting the "star icon" present on all ads.';
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
