<?php defined('SYSPATH') OR die('No direct access allowed.');

ini_set('memory_limit', '64M');

include('application/libraries/Zong.php');

class Request_Controller extends Template_Controller {

	public $template = 'adshop/blank';
	public $domain = 'http://test.adshop.ie';
	
	public function __construct() {
		parent::__construct();
		if(IN_PRODUCTION)
			$this->domain = 'http://adshop.ie';
	}

	// request cannot be directly accessed
	public function index() {
		$this->call();
	}
	
	/*
	public function footer($tip) {
		$tip_model = new Tooltip_Model;
		$content = $tip_model->get_tip($tip);
		$this->template->content = $content;
	}
	
	public function item($item_id) {
		$this->template->content = new View('tipitem_content');
		$item_model = new Item_Model;
		$this->template->content->item = $item_model->get_item($item_id);
	}
	*/
	
	/**
	 * Get a list of subcategories
	 * @param string the category name
	 * @return json object of subcategories
	 */
	public function subcat($cat='') {
		//$this->template->content = new View('subcat_content');
		$view_model = new View_Model;
		if(empty($cat))
			$subcats = array();
		else
			$subcats = $view_model->get_subcategories($cat);
		$this->template->content = json_encode(array('status'=>'ok','content'=>$subcats));
	}
	
	/**
	 * Get a list of sub-subcategories
	 * @param string the subcategory name
	 * @return json object of sub-subcategories
	 */
	public function subsubcat($subcat='') {
		//$this->template->content = new View('subcat_content');
		$view_model = new View_Model;
		$label = 'Choose';
		if(empty($subcat))
			$subsubcats = array();
		else {
			$subsubcats = $view_model->get_subsubcategories($subcat);
			$label = $view_model->get_subsubcategory_label($subcat);
		}
		$this->template->content = json_encode(array('status'=>'ok','label'=>$label,'content'=>$subsubcats));
	}
	
	/**
	 * Handle file uploads
	 * Validates file against types and size
	 * Creates 4 different rotation thumbnails
	 * Saves all versions, with width and height, into temp media table
	 * Adds a watermark to display version
	 * Deletes original file
	 * @return on success, json object with file data. on fail, error message.
	 */
	public function upload() {
		$files = Validation::factory($_FILES)->add_rules('userfile', 'upload::valid', 'upload::type[gif,jpg,png,jpeg,bmp]', 'upload::size[20M]');
		if ($files->validate()) {
			$clean_name = time().sanitize_filename::clean(Input::instance()->xss_clean($_FILES['userfile']['name']));
			$filename = upload::save('userfile',$clean_name);
			
			$user_id = (Auth::instance()->logged_in()) ? Auth::instance()->get_user()->id : $this->input->post('session');
			
			$image = new Image('img/upload/'.$clean_name);
			$pathinfo = pathinfo('img/upload/'.$clean_name);
			$filename = $pathinfo['filename'];
			$ext = $pathinfo['extension'];
			
			if($image->width >= 524 || $image->height >= 393)
				$image->resize(524,393);
			$image->save('img/upload/'.$clean_name);
			
			$orig = $filename."-orig.".$ext;
			$image->resize(524,393)->save('img/upload/'.$orig);
			
			$image_t = $filename."-t.".$ext;
			$image0 = $filename."-0.".$ext;
			$image90 = $filename."-90.".$ext;
			$image180 = $filename."-180.".$ext;
			$image270 = $filename."-270.".$ext;
			
			$image->rotate(0)->resize(142,106)->save('img/upload/'.$image_t);
			$image->rotate(0)->resize(76,59)->save('img/upload/'.$image0);
			$image->rotate(90)->resize(76,59)->save('img/upload/'.$image90);
			$image->rotate(180)->resize(76,59)->save('img/upload/'.$image180);
			$image->rotate(270)->resize(76,59)->save('img/upload/'.$image270);
			
			$image_arr = array('file'=>$image0,'width'=>$image->width,'height'=>$image->height,'ext'=>'.'.$ext);

			$this->template->content = json_encode(array('status'=>'ok','content'=>$image_arr));
			
			$item_model = new Item_Model;
			$item_model->save_temp_media($user_id,$clean_name,$image->width,$image->height);
			$item_model->save_temp_media($user_id,$orig,$image->width,$image->height);
			$item_model->save_temp_media($user_id,$image_t,$image->width,$image->height);
			$item_model->save_temp_media($user_id,$image0,$image->width,$image->height);
			$item_model->save_temp_media($user_id,$image90,$image->width,$image->height);
			$item_model->save_temp_media($user_id,$image180,$image->width,$image->height);
			$item_model->save_temp_media($user_id,$image270,$image->width,$image->height);
			
			$item_model->add_watermark($clean_name,'horizontal');
			
			//unlink('img/upload/'.$clean_name);
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Error uploading media.'));
	}
	
	public function rotate_image() {
		$src = $this->input->post('image');
		$angle = $this->input->post('angle');
		
		$path = pathinfo($src);
		$filename = $path['filename'];
		$ext = $path['extension'];
		$matches = preg_split('/-\d+$/',$filename);
		
		$image = new Image('img/upload/'.$matches[0].'-orig.'.$ext);
		$image->rotate($angle)->save('img/upload/'.$matches[0].'.'.$ext);
		$image->rotate($angle)->resize(142,106)->save('img/upload/'.$matches[0].'-t.'.$ext);
		
		$orientation = ($angle == '90' || $angle == '270') ? 'vertical' : 'horizontal';
		$item_model = new Item_Model;
		$item_model->add_watermark($matches[0].'.'.$ext, $orientation);
		
		$this->template->content = json_encode(array('status'=>'ok','content'=>''));
	}
	
	/**
	 * E-mail seller of an item
	 * Validates name and email address
	 * @return on success, json object with message. on fail, error message.
	 */
	public function email_seller() {
		$form = $_POST;
		if($form) {
			$form_data = Validation::factory($form)->add_rules('name', 'required')->add_rules('email', 'required', 'valid::email')->add_rules('phone', 'required', 'length[3,20]')->add_rules('message', 'required');
			if($form_data->validate()) {
				$item_id = $this->input->post('item_id');
				$user_model = new User_Model;
				$owner_email = $user_model->get_ad_owner($item_id);
				
				$item_model = new Item_Model;
				$item = $item_model->get_item($item_id);
				
				$name = $this->input->post('name');
				$email = $this->input->post('email');
				$phone = $this->input->post('phone');
				
				$message = "<p>".$name." has sent you a message about your AdShop.ie ad: ".$item->title."</p>";
				$message .= "<p>\"".$this->input->post('message')."\"</p>";
				$message .= !empty($phone) ? '<p>(you can reply to this e-mail or call '.$name.' on: <b style="color: #ff0000;">'.$phone.'</b>)</p>' : "\n";
				if(email::send($owner_email,$email,'Hello from AdShop.ie',$message, TRUE))
					$this->template->content = json_encode(array('status'=>'ok','content'=>'E-mail sent successfully.'));
				else
					$this->template->content = json_encode(array('status'=>'err','content'=>'Error sending e-mail.'));
			}
			else
				$this->template->content = json_encode(array('status'=>'err','content'=>'Please complete all fields.'));
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Error sending email.'));
	}
	
	/**
	 * E-mail site owner
	 * Validates name and email address
	 * @return on success, json object with message. on fail, error message.
	 */
	public function contact_us() {
		$form = $_POST;
		if($form) {
			$action = $this->input->post('action');
			switch($action) {
				case 'contact_us':
        $form_data = Validation::factory($form)->add_rules('name', 'required')->add_rules('email', 'required', 'valid::email')->add_rules('message', 'required');
				$error = "Please complete all fields.";
				break;
				case 'report_ad':
        $form_data = Validation::factory($form)->add_rules('message', 'required');
				$error = "Please provide a reason.";
				break;
				case 'ad_mistake':
        $form_data = Validation::factory($form)->add_rules('name', 'required')->add_rules('email', 'required', 'valid::email')->add_rules('message', 'required');
				$error = "Please complete all fields.";
				break;
				default:
				$error = '';
				break;
			}
			if($form_data->validate()) {
				$user_model = new User_Model;
				
				$name = $this->input->post('name');
				$email = $this->input->post('email');
				$phone = $this->input->post('phone');
				$ad = $this->input->post('ad');
				$link = $this->input->post('ad_link');
				$item_id = $this->input->post('item_id');
				$message = '';
				
				if($action == 'contact_us') {
					$subject = "AdShop.ie User Feedback";
					$message = "From: ".$name."<br />\n";
					$response = 'Your message has been sent.';
				}
				else if($action == 'report_ad') {
					$subject = "AdShop.ie Ad Reported";
					$email = 'noreply@adshop.ie';
					$message = 'Ad: <a href="'.$link.'">'.$ad.'</a>'."<br />\n";
					$response = 'Thanks for reporting this ad.';
				}
        else if($action == 'ad_mistake') {
          $auto_login = base64_encode('admin@adshop.ie:'.$item_id);
					$subject = "AdShop.ie Ad Correction";
					$message = "From: ".$name;
					if(!empty($phone))
						$message .= " (".$phone.")";
					$message .= "<br />\n";
					$message .= 'Ad: <a href="'.$link.'?u='.$auto_login.'#step_4">'.$ad.'</a>'."<br />\n";
					$response = 'Your message has been sent.';
				}
				
				$message .= $this->input->post('message')."<br />\n";
				if(email::send('mail@adshop.ie',$email,$subject,$message,TRUE))
					$this->template->content = json_encode(array('status'=>'ok','content'=>$response));
				else
					$this->template->content = json_encode(array('status'=>'err','content'=>'Error sending message. Please try again.'));
			}
			else
				$this->template->content = json_encode(array('status'=>'err','content'=>$error));
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Error sending message. Please try again.'));
	}
	
	/**
	 * E-mail user after posting an ad
	 * @return on success, json object with message. on fail, error message.
	 */
	public function new_item_email($username,$item_id,$item_title) {
		$auto_login = base64_encode($username.':'.$item_id);
		$subject = "Your ad is now on AdShop.ie";
		$message = "Your ad: ".$item_title." has been posted on Adshop.ie\n\n";
		$message .= "To View or Edit your ad use this link:\n";
		$message .= $this->domain."/place/edit/".$item_id."?u=".$auto_login."#step_4\n\n";
		$message .= "To Renew your ad use this link:\n";
		$message .= $this->domain."/renew/".$item_id."?u=".$auto_login."\n\n";
		$message .= "To Remove your ad and \"mark it as sold\" use this link:\n";
		$message .= $this->domain."/place/remove/".$item_id."?u=".$auto_login."\n\n";
		$message .= "Thanks for using Ireland's Simplest Ad Website.\n";
		if(email::send($username,'mail@adshop.ie',$subject,$message))
      return 'ok';
    else
      return 'err';
	}
	
	public function edit() {
   		$this->template->content = 'ok';
   	}
   	
   	/**
	 * Add item to user's saved list
	 * @return json object with status of 'saved' or 'removed'
	 */
   	public function save_ad() {
   		$item_id = $this->input->post('item_id');
   		$user_model = new User_Model;
   		$this->template->content = json_encode(array('status'=>$user_model->set_saved_cookie($item_id),'content'=>''));
   	}
   	
   	/**
   	 * Save item to database
   	 * Validates POST data of item title, description and location
   	 * If the user is logged in, use the current user_id, otherwise, create and log-in user
   	 * Saves item and media data
   	 * @return on success, json object with status, user_id and item_id. on fail, error message
   	 *
   	 */
	public function save_item() {
		$item_model = new Item_Model;	
		$form = $_POST;
		if($form) {
			$form_data = Validation::factory($form)
						 ->add_rules('item_title', 'required','length[1,55]')
						 ->add_rules('item_desc', 'required')
						 ->add_rules('item_location', 'required');
			if($form_data->validate()) {
				//$user,$cat,$subcat,$title,$desc,$price,$location,$trade,$allow_email
				//$user_id = (Auth::instance()->logged_in()) ? Auth::instance()->get_user()->id : $this->session->id();
				$user_model = new User_Model;
				
				$item_id = $this->input->post('item_id');
				
				//if(Auth::instance()->logged_in())
				//	$user_id = Auth::instance()->get_user()->id;
				//else {
					$user_arr = array('name'=>$this->input->post('item_name'),
									  'username'=>$this->input->post('item_email'),
									  'phone'=>$this->input->post('item_phone'),
									  'password'=>$this->input->post('item_password'));
									
          if(Auth::instance()->logged_in('admin')) {
            //$orm_user = ORM::factory('user', Auth::instance()->get_user()->username);
            //if($orm_user->has($this->admin_role)) {
              Kohana::log('info','is admin');
              $user_arr['isAdmin'] = true;
            //}
          }
						
          $user_id = $user_model->register($user_arr);
					
					if(Auth::instance()->logged_in('admin')) // if user is admin, get the ad owner id instead
						$user_id = $user_model->get_ad_owner_id($item_id);
				//}
				if($user_id > 0) {
					
					$item_arr = array('item_id'=>$item_id,
									  'user_id'=>$user_id,
									  'category_id'=>$this->input->post('item_cat'),
									  'subcategory_id'=>$this->input->post('item_subcat'),
									  'subsubcategory_id'=>$this->input->post('item_subsubcat'),
									  //'active'=>'1',
									  'title'=>$this->input->post('item_title'),
									  'description'=>$this->input->post('item_desc'),
									  'price'=>$this->input->post('item_price'),
									  'owner_name'=>$this->input->post('item_name'),
									  'owner_phone_prefix'=>$this->input->post('item_phone_prefix'),
									  'owner_phone'=>$this->input->post('item_phone'),
									  'location'=>$this->input->post('item_location'),
									  'trade_ad'=>$this->input->post('item_trade_ad'),
									  'trade_company'=>$this->input->post('item_business_name'),
									  'trade_address'=>$this->input->post('item_business_address'),
									  'hide_email'=>$this->input->post('item_hide_email'));
									  
					$extras = $this->input->post('extra_attributes');
					if(isset($extras)) {
						foreach($extras as $e)
							$extra_attributes[$e] = 'true';
					
						$extra_attributes = json_encode($extra_attributes);
						$item_arr['extra_attributes'] = $extra_attributes;
					}
					else
						$item_arr['extra_attributes'] = '';
						
					$media = $this->input->post('media');
					$coupon = $this->input->post('item_coupon');
					$item_model = new Item_Model;
					$update_item = 0;
					$new_item = 0;
					if(!empty($item_id)) {
						$owner = $user_model->get_ad_owner($item_id);
						Kohana::log('info', 'owner: '.$owner);
						Kohana::log('info', 'media: '.$media);
						
						//$my_item = $user_model->get_my_ad();
						if($owner == Auth::instance()->get_user()->username || Auth::instance()->logged_in('admin'))
							$update_item = $item_model->update($item_arr,$media);
							
						Kohana::log('info', 'update: '.$update_item);
					}
					else {
						//$item_arr['publish_timestamp'] = time();
						$item_arr['term'] = $this->input->post('item_term');
						$item_arr['expire_timestamp'] = strtotime('+'.$item_arr['term'].' months',time());
						$new_item = $item_model->save($item_arr,$media);
						
						if(isset($coupon) && $coupon != 'Enter PAYCODE here.') {
							$payment_model = new Payment_Model;
							if($payment_model->apply_coupon($coupon,$user_id,$new_item)) {
                $item_model->activate($new_item);
                Kohana::log('info',print_r($user_arr,true));
                Kohana::log('info',print_r($item_arr,true));
								$this->new_item_email($user_arr['username'],$new_item,$item_arr['title']);
								Kohana::log('info', 'applied coupon code: '.$coupon);
							}								
							else
								Kohana::log('info', 'error applying coupon code: '.$coupon);
						}
					}
						
					$status = 'ok';
					if($update_item)
						$return_content = 'Item updated.';
					else if($new_item) {
						$item_arr['item_id'] = $new_item;
						$return_content = 'New item added.';
						//$this->new_item_email($user_arr,$item_arr);
					}
					else {
						$return_content = 'Error listing item. Please try again.';
						$status = 'err';
					}
					
					$return_item_id = !empty($item_id) ? $item_id : $new_item;
					$this->template->content = json_encode(array('status'=>$status,'content'=>$return_content,'uid'=>$user_id,'item_id'=>$return_item_id));
				}
				else
					$this->template->content = json_encode(array('status'=>'err','content'=>'Incorrect e-mail address or password.<br />If you have previously posted an ad, please use the same password as before.<br />Otherwise, please use a different e-mail address.'));
			}
			else {
				$errors = $form_data->errors();
				$fields = array('item_title'=>'title','item_desc'=>'description','item_price'=>'price','item_location'=>'county');
				$error_list = array_intersect_key($fields, $errors);
				$content = 'Invalid fields: '.implode(', ',$error_list);
				$this->template->content = json_encode(array('status'=>'err','content'=>$content));
			}
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Error saving data.'));
	}
	
	/**
	 * Renew a user's ad
	 * If a user is logged in and item_id and item_term are POSTed, renew item
	 * @return on success, json object with status, user_id and item_id. on fail, error message
	 */
	public function renew_ad() {
		if(Auth::instance()->logged_in()) {
			$user_model = new User_Model;
			$user = Auth::instance()->get_user();
			$user_id = $user->id;
			$username = $user->username;
			$item_id = $this->input->post('item_id');
			$term = $this->input->post('item_term');
			$coupon = $this->input->post('item_coupon');
			//$item_arr = array('item_id'=>$item_id,'user_id'=>$user_id,'term'=>$term);
			if(!empty($item_id)) {
				$owner = $user_model->get_ad_owner($item_id);
				//$my_item = $user_model->get_my_ad();
				if($owner == $username || Auth::instance()->logged_in('admin')) {
					$item_model = new Item_Model;
					//if($item_model->renew($item_id,$term))
						$this->template->content = json_encode(array('status'=>'ok','content'=>'Processing renewal for '.$term.' months.','item_id'=>$item_id,'uid'=>$user_id,));
					//else
					//	$this->template->content = json_encode(array('status'=>'err','content'=>'Error renewing ad.'));
						
					if(isset($coupon) && $coupon != 'Enter PAYCODE here.') {
						$payment_model = new Payment_Model;
						if($payment_model->apply_coupon($coupon,$user_id,$item_id)) {
              $item_model->activate($item_id);
							$item_model->renew($item_id,$term);
							Kohana::log('info', 'applied coupon code: '.$coupon);
						}
						else
							Kohana::log('info', 'error applying coupon code: '.$coupon);
					}
				}
			}
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Error renewing ad.'));
	}
	
	/**
	 * Remove a user's ad
	 * If a user is logged in and item_id is POSTed, mark item as sold
	 * @return on success, json object with status, user_id and item_id. on fail, error message
	 */
	public function remove_ad() {
		if(Auth::instance()->logged_in()) {
			$user_model = new User_Model;
			$user = Auth::instance()->get_user();
			$user_id = $user->id;
			$username = $user->username;
			$item_id = $this->input->post('item_id');
			//$item_arr = array('item_id'=>$item_id,'user_id'=>$user_id,'term'=>$term);
			if(!empty($item_id)) {
				$owner = $user_model->get_ad_owner($item_id);
				//$my_item = $user_model->get_my_ad();
				if($owner == $username || Auth::instance()->logged_in('admin')) {
					$item_model = new Item_Model;
					if($item_model->remove($item_id))
						$this->template->content = json_encode(array('status'=>'ok','content'=>'Your ad has been removed.'));
					else
						$this->template->content = json_encode(array('status'=>'err','content'=>'Error removing ad.'));
				}
			}
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Error removing ad.'));
	}
	
	/**
	 * Reset user's password
	 * Load user record based on POSTed email address
	 * Create new hashed password and save user record
	 * Email user with new password and link to change
	 * @return on success, json object with message. on fail, error message.
	 */
	public function reset_password() {
		$email = $this->input->post('email');
		$user = ORM::factory('user', $email);
		if(!empty($user)) {
			if($user->loaded) {
				$length = 5;
				$characters = 'abcdefghijklmnopqrstuvwxyz';
				$pwd = '';
	
				for ($p = 0; $p < $length; $p++)
				    $pwd .= $characters[mt_rand(0, strlen($characters)-1)];
	
				$pwd .= rand(0,9);
	
				$user->password = Auth::instance()->hash_password($pwd);
				if($user->save()) {
					$message = "Your AdShop.ie password has been reset.\n\n";
					$message .= "Click this link below to enter your new password:\n";
					$message .= $this->domain."/user/new_password/".base64_encode($user->id.'_'.time().'_'.$pwd)."\n\n";
					$message .= "If you require any further assistance use the Contact Us form on AdShop.ie.\n";
					email::send($email,'noreply@adshop.ie','Hello from AdShop.ie',$message);
					$this->template->content = json_encode(array('status'=>'ok','content'=>'Password reset. Please check your email.'));		
				}
				else
					$this->template->content = json_encode(array('status'=>'err','content'=>'Error resetting password.'));
			}
			else {
				$this->template->content = json_encode(array('status'=>'err','content'=>'Wrong e-mail entered. Typo?'));
			}
		}
		else {
			$this->template->content = json_encode(array('status'=>'err','content'=>'No e-mail entered.'));
		}
	}
	
	/**
	 * Handle IPN requests from PayPal
	 * POST data back to PayPal with validate command
	 * Parse result for valid status and compare timestamp to now
	 * Set item to active
	 */
	public function ipn() {
		//$this->template->content = print_r($_POST,true);
		
		$post_arr = $_POST;
		$post_arr['cmd'] = '_notify-validate';
		
		Kohana::log('info', print_r($post_arr,true));
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://www.sandbox.paypal.com/cgi-bin/webscr");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_arr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		$result = curl_exec($ch);
		$req_info = curl_getinfo($ch);
		
		Kohana::log('info', print_r($result,true));
		Kohana::log('info', print_r($req_info,true));
		
		curl_close($ch);
		
		$item_model = new Item_Model;
		
		if($req_info['http_code'] == '200') {
			
			$custom_arr = json_decode($post_arr['custom'],true);
			$pp_timestamp = $custom_arr[0]['timestamp'];
			//$uid = $custom_arr[0]['uid'];
			$item_id = $custom_arr[0]['item_id'];
			
			if($post_arr['payment_status'] == 'Completed') {
				Kohana::log('info', 'time wtf: ('.time().' - '.$pp_timestamp.') / 86400');
				Kohana::log('info', 'time diff: '.(time() - $pp_timestamp) / 86400);
				
				if((time() - $pp_timestamp) / 86400 < 1) {
					$item = $item_model->activate($item_id);
					$item = $item_model->renew($item_id);
					$this->template->content = json_encode(array('status'=>'ok','content'=>'Payment complete.'));
					Kohana::log('info', 'everything ok');

          $user = $item_model->get_ad_owner($item_id);
          $item = $item_model->get_item($item_id);
          $this->new_item_email($user,$item_id,$item->title);
					
					// store transaction data in DB
          $payment_data = array(
            'item'=>$post_arr['item_name'],
            'amount'=>$post_arr['mc_gross'],
            'name'=>$post_arr['first_name'].' '.$post_arr['last_name'],
            'email'=>$post_arr['payer_email'],
            'status'=>$post_arr['payment_status'],
            'signature'=>$post_arr['verify_sign'],
            'timestamp'=>time());
					$payment_model = new Payment_Model;
					$payment_model->store_transaction($payment_data);
				}
				else
					Kohana::log('info', 'payment complete, timestamp fail');
			}
			else {
				$item = $item_model->deactivate($item_id);
				$this->template->content = json_encode(array('status'=>'err','content'=>'Payment could not be completed.'));
				Kohana::log('info', 'payment error');
			}
		}
		else {
			$this->template->content = json_encode(array('status'=>'err','content'=>'Request could not be completed.'));
			Kohana::log('info', 'request error');
		}
	}
	
	public function zong_callback() {
		$zong = new Zong_Core;
		//Kohana::log('info', print_r($_GET,true));
		
		$queryString = $_SERVER['QUERY_STRING'];
		
		Kohana::log('info', 'zong qs:'.$queryString);
		
		$transRef = $_GET['transactionRef'];
	    $itemRef = $_GET['itemRef'];
	    $method = $_GET['method'];
	    $msisdn = $_GET['msisdn'];
	    $outPayment = $_GET['outPayment'];
      $simulated = $_GET['simulated'];
      $consumerPrice = $_GET['consumerPrice'];
      $status = $_GET['status'];
      $signature = $_GET['signature'];

	    $item_model = new Item_Model;
		
		//Verify that postback originated from Zong
		if($zong->verifySignature($queryString)) {
		    //Check "status" flag . For Production, remember to check "simulated" flag
		    $status = $_GET['status'];
		    if($status == 'FAILED') {
		        $failure = $_GET['failure'];
				$item = $item_model->deactivate($transRef);
		    	$this->template->content = json_encode(array('status'=>'err','content'=>'Payment could not be completed.'));
				Kohana::log('info', 'zong error:'.$failure);
      }
			else {
          $item = $item_model->activate($transRef);
          $item = $item_model->renew($transRef);
          //$this->template->content = json_encode(array('status'=>'ok','content'=>'Payment complete.'));
          $this->template->content = $transRef.':OK';

          // store transaction data in DB
          $item = $item_model->get_item($transRef);
          $user_model = new User_Model;
          $username = $user_model->get_ad_owner($transRef);
          $payment_data = array(
            'item'=>'AdShop Ad placement for 3 Months',
            'amount'=>$consumerPrice,
            'name'=>$item->owner_name,
            'email'=>$username,
            'status'=>$status,
            'signature'=>$signature,
            'timestamp'=>time());
					$payment_model = new Payment_Model;
          $payment_model->store_transaction($payment_data);

          $email = $this->new_item_email($username,$transRef,$item->title);

          Kohana::log('info', 'everything ok from zong:'.$transRef);
		  }
		}
		else {
			//Signature Verification Failed
			$item = $item_model->deactivate($transRef);
			$this->template->content = json_encode(array('status'=>'err','content'=>'Payment could not be completed.'));
			Kohana::log('info', 'zong error: signature verification failed');
		}
	}
	
	/**
	 * Return email address of a given ad's owner
	 * @param integer the item_id
	 * @return string an email address 
	 */
	public function get_ad_owner($item_id) {
		$user_model = new User_Model;
		$this->template->content = $user_model->get_ad_owner($item_id);
	}
	
	/**
	 * Return true/false if username exists
	 * @param string email address
	 * @return boolean 
	 */
	public function valid_user() {
		$form = $_POST;
		$form_data = Validation::factory($form)->add_rules('email','required','valid::email');
		if($form_data->validate()) {
			$email = $this->input->post('email');
			$logged_in = isset($form['logged_in']);
			$user_model = new User_Model;
			if($user_model->valid_user($email,$logged_in))
				$this->template->content = json_encode(array('status'=>'ok','content'=>'User exists.'));
			else
				$this->template->content = json_encode(array('status'=>'err','content'=>'User does not exist.'));
		}
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Invalid e-mail address.'));
	}
	
	/**
	 * Test validity of coupon
	 * @param string coupon code 
	 * @return json object with status
	 */
	public function valid_coupon() {
		$code = $this->input->post('code');
		$payment_model = new Payment_Model;
		if($payment_model->valid_coupon($code) > 0)
			$this->template->content = json_encode(array('status'=>'ok','content'=>'Valid code.'));
		else
			$this->template->content = json_encode(array('status'=>'err','content'=>'Invalid code.'));
	}
	
	
	/**
	 * Run hourly cron tasks
	 * 1. Remind users if their ad is expiring within 48 hours
	 * 2. Expire items that have gone past their term date
	 * 3. Clean up temporary media
	 */
	public function cron_hourly() {
		$item_model = new Item_Model;
		$reminders = $item_model->remind();
		
		foreach($reminders as $r) {
			$auto_login = base64_encode($r->username.':'.$r->item_id);
			$message = "If you do not wish to renew your ad: ".$r->title." you can simply ignore this e-mail.\n\n";
			$message .= "To Renew your ad use this link:\n";
			$message .= $this->domain."/renew/".$r->item_id."?u=".$auto_login."\n\n";
			$message .= "To Remove your ad and \"mark it as sold\" use this link:\n";
			$message .= $this->domain."/place/remove/".$r->item_id."?u=".$auto_login."\n\n";
			$message .= "Thanks from AdShop.ie";
			if(IN_PRODUCTION)
				email::send($r->username,'noreply@adshop.ie','Your AdShop.ie ad will expire in 48 hours',$message);
			Kohana::log('info','emailed: '.$r->username);
		}
		
		$expired = $item_model->expire_items();
		
		$this->template->content = '';
	}
	
	/**
	 * Run daily cron tasks
	 * 1. Cache new pricepoints from Zong
	 */
	public function cron_daily() {
		$zong = new Zong_Core;
		$zong->cacheZong();
		$this->template->content = '';
	}
   	
	public function __call($method,$arguments) {
		$this->template->content = json_encode(array('status'=>'err','content'=>'Bad request.'));
    }
    
	/*** test functions below ***/
	
	public function what_is_cookie() {
		$this->template->content = print_r($_COOKIE,true);
	}
	
	public function imgdump() {
		$image = new Image('img/L11841187.JPG');
		$pathinfo = pathinfo('img/L11841187.JPG');
		//$this->template->content = Kohana::debug($image);
		$this->template->content = print_r($pathinfo,TRUE);
	}
	
	public function test_email() {
		$to      = 'hixsonj@gmail.com';
		$from    = 'mail@adshop.ie';
		$subject = 'This is an example subject';
		$message = "<p>Brendan has sent you a message about your AdShop.ie ad: 'Scuba Tanks'</p>";
		$message .= "<p>Saw these Scuba tanks and they look exactly like what I want, but I am only prepared to pay Û500.</p>";
		$message .= "<p>(you can reply to this e-mail or call Brendan on: <b style=\"color: #f00;\">0851220852</b>)";
 
		$this->template->content = email::send($to, $from, $subject, $message, TRUE);
	}
	
	public function ps() {
		$src = 'adshop/img/upload/1291834850Koala-180.jpg';
		$path = pathinfo($src);
		$filename = $path['filename'];
		echo $filename;
		$ext = $path['extension'];
		$matches = preg_split('/-\d+$/',$filename);
		
		$this->template->content = print_r($matches,true);
	}
	
	public function force() {
		Auth::instance()->force_login('brendanjnr@gmail.com');
		$this->template->content = 'ok';
	}
	
	public function test_transaction() {
		$post_arr = array('mc_gross'=>'2.00', 'protection_eligibility'=>'Eligible', 'address_status'=>'confirmed', 'payer_id'=>'KW5JNBDY7JF7Q', 'tax'=>'0.00', 'address_street'=>'1 Main St', 'payment_date'=>'11:37:21 Dec 16, 2010 PST', 'payment_status'=>'Completed', 'charset'=>'windows-1252', 'address_zip'=>'95131', 'first_name'=>'Test', 'mc_fee'=>'0.41', 'address_country_code'=>'US', 'address_name'=>'Test User', 'notify_version'=>'3.0', 'custom'=>'[{"timestamp":"1292528205","uid":23,"item_id":33}]', 'payer_status'=>'verified', 'business'=>'j_hixs_1291143289_biz@yahoo.com', 'address_country'=>'United States', 'address_city'=>'San Jose', 'quantity'=>'1', 'verify_sign'=>'A4SNC7PzIeCv7JmeJyR3WIGxe8KUA-l8uiUFtg11TzyxIXroOFFCVqcL', 'payer_email'=>'bren_1291154412_per@yahoo.com', 'txn_id'=>'3U040132U9032662C', 'payment_type'=>'instant', 'last_name'=>'User', 'address_state'=>'CA', 'receiver_email'=>'j_hixs_1291143289_biz@yahoo.com', 'payment_fee'=>'', 'receiver_id'=>'CSY2CWA7YES2A', 'txn_type'=>'web_accept', 'item_name'=>'AdShop Ad placement for 2 Months', 'mc_currency'=>'EUR', 'item_number'=>'', 'residence_country'=>'US', 'test_ipn'=>'1', 'handling_amount'=>'0.00', 'transaction_subject'=>'[{"timestamp":"1292528205","uid":23,"item_id":33}]', 'payment_gross'=>'', 'shipping'=>'0.00', 'cmd'=>'_notify-validate');
		$payment_model = new Payment_Model;
		$this->template->content = $payment_model->store_transaction($post_arr);
	}

	public function test_reg() {
		$user = array('name'=>'Jimmy','username'=>'brendanjnr@gmail.com','phone'=>'555666','password'=>'bren');
		$user_model = new User_Model;
		$this->template->content = $user_model->register($user);
		print_r(Kohana::debug($_SESSION));
	}
	
	public function reset_bren() {
		$user = ORM::factory('user','brendanjnr@gmail.com');
		if($user->loaded) {
	    	$user->password = Auth::instance()->hash_password('1');
	    	$user->save();
	    	$this->template->content = 'changed password to "1"';
		}
		else
			$this->template->content = 'fail';
	}
	
	public function fix_thumbs() {
		$item_model = new Item_Model;
		$items = $item_model->fixmedia();
		foreach($items as $i) {
			$media = json_decode($i->media,true);
			foreach($media as $m) {
				
				
				$src = preg_replace('/\?\w+=\d+/','',$m['src']);
				$pathinfo = pathinfo($src);
				//$image = new Image('img/upload/'.$src);
				//$image_t = basename($src,'.'.$image->ext)."-t.".$image->ext;
				//$image->rotate(0)->resize(142,106)->save('img/upload/'.$image_t);
				//echo $image_t."<br />\n";
				
				
				if($pathinfo['extension'] != 'jpg') {
					print_r($pathinfo);
					if(file_exists('img/upload/'.$pathinfo['filename'].'-orig.'.$pathinfo['extension'])) {
						$file = $pathinfo['filename'].'-orig.'.$pathinfo['extension'];
						echo $file;
					}
					else
						$file = $pathinfo['basename'];
					$item_model->add_watermark($file,'horizontal');
					//$item_mode->update 
				}
			}
		}
		$this->template->content = '';
	}
	
	public function one_thumb() {
		$item_model = new Item_Model;
		$item_model->add_watermark('1301172044Screen_shot_2011-03-26_at_20.40.46-orig.png');
		$this->template->content = '';
	}
	
	public function fix_phones() {
		$item_model = new Item_Model;
		$items = $item_model->fixphones();
		$this->template->content = 'ok';
	}
	
	public function fire_new_ad_email() {
		$username = 'hixsonj@gmail.com';
    $item_title = 'Samsung Backlit LED TV';
    $item_id = '25';
		$this->new_item_email($username,$item_id,$item_title);
	}
	
	public function make_admin() {
		$user = ORM::factory('user','admin@adshop.ie');
	    $user->password = Auth::instance()->hash_password('XQ94RH73N73GYJSB6');
	 
	    // if the user was successfully created...
	    if ($user->save())
			$this->template->content = 'ok';
		else
			$this->template->content = 'err';
	}
	
	public function in_production() {
		$this->template->content = (IN_PRODUCTION) ? 'yes' : 'no';
	}
}
