<?php defined('SYSPATH') OR die('No direct access allowed.');

include_once 'application/classes/CreditsManager.php';
include_once 'application/classes/PricePoint.php';

class Zong_Controller extends Template_Controller {

	public $template = 'adshop/template';
	public $no_items_message = 'No items were found in this category. Please try another.';
	
	public function __construct() {
		parent::__construct();
		$this->template->display_search = FALSE;
		$this->template->display_favs = FALSE;
		$this->template->favs = array();
		$this->template->counties = array();
		
		$this->template->content = new View('zong_content');
		$this->template->title = 'Zong Test';
		
		$this->template->menu = parent::build_menu();
		
		$this->template->content->no_items_message = $this->no_items_message;
		$this->template->content->save_list = FALSE;
			
		$this->template->content->subcategories = '';
	}

	public function index() {		
		$creditsManager = CreditsManager::singleton();

		//Get list of countries from cache (countries.txt in this example)
		$countries = $creditsManager->getCountries();
		$this->template->content->countries = $countries;

		//Get current number of credits
		$credits = $creditsManager->getUserCredits();
		$this->template->content->credits = $credits;
		
	}
	
	
	public function selectPrice() {
		$country = $this->input->post('country');

		$creditsManager = CreditsManager::singleton();
		
		/*
		//Grab price points for selected country
		$pricePoints = $creditsManager->getPricePointsForCountry($country);
		//Calculates and populates numCredits and itemDesc for each price point
		$pricePoints = $creditsManager->calculateCredits($pricePoints);
		
		$this->template->content->pricePoints = $pricePoints;
		*/
		$pricePoint = $creditsManager->getLowestPricePointForCountry('IE');
		$pricePoint->itemDesc = 'Ad Placement for 3 months';
		//$pricePoint = unserialize(base64_decode($pricePoint));

		$creditsManager = CreditsManager::singleton();
		
		$entryPointUrl = $creditsManager->initiateTransaction($pricePoint);
		$this->template->content->entryPointUrl = $entryPointUrl;		
	}
	
	public function iFrame() {
		$pricePoint = $this->input->post('pricePoint');
		
		$pricePoint = unserialize(base64_decode($pricePoint));

		$creditsManager = CreditsManager::singleton();
		
		$entryPointUrl = $creditsManager->initiateTransaction($pricePoint);
		
		$this->template->content->entryPointUrl = $entryPointUrl;		
	}
	
}

?>