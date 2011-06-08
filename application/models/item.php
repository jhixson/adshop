<?php defined('SYSPATH') or die('No direct script access.');

class Item_Model extends Model {
	
	public function __construct() {
		parent::__construct();
	}
 
	/**
	 * Get information about an item, given the item_id
	 * @param integer the item_id
	 * @return the result object
	 */
	public function get_item($item_id) {
		$this->db->from('items');
		$this->db->select('users.id, categories.title as cat_title, categories.slug as cat, subcategories.title as subcat_title, subcategories.slug as subcat, subsubcategories.title as subsubcat_title, subsubcategories.slug as subsubcat, users.username, items.*');
		$this->db->where(array('items.item_id'=>$item_id));
		$this->db->join('users',array('users.id'=>'items.user_id'));
		$this->db->join('categories',array('categories.id'=>'items.category_id'));
		$this->db->join('subcategories',array('subcategories.id'=>'items.subcategory_id'));
		$this->db->join('subsubcategories',array('subsubcategories.id'=>'items.subsubcategory_id'),'','LEFT');
	
		$item = $this->db->get();
		//$res = $item->result(FALSE);
		
		//clean_obj::clean($res[0]);
		//array_walk($res[0],create_function('&$v,$k','$v = htmlentities($v);'));
		
		//print_r($res[0]);
		return $item[0];
	}
	
	/**
	 * Get information about an item, given the slug
	 * @param string the slug
	 * @return the result object
	 */
	public function get_item_by_slug($slug) {
		$this->db->from('items');
		$this->db->select('users.id, categories.title as cat_title, categories.slug as cat, subcategories.title as subcat_title, subcategories.slug as subcat, subsubcategories.title as subsubcat_title, subsubcategories.slug as subsubcat, users.username, items.*');
		$this->db->where(array('items.slug'=>$slug));
		$this->db->join('users',array('users.id'=>'items.user_id'));
		$this->db->join('categories',array('categories.id'=>'items.category_id'));
		$this->db->join('subcategories',array('subcategories.id'=>'items.subcategory_id'));
		$this->db->join('subsubcategories',array('subsubcategories.id'=>'items.subsubcategory_id'),'','LEFT');
	
		$item = $this->db->get();
		//$res = $item->result(FALSE);
		
		//clean_obj::clean($res[0]);
		//array_walk($res[0],create_function('&$v,$k','$v = htmlentities($v);'));
		
		//print_r($res[0]);
		return $item[0];
	}
	
	/**
	 * Get images, videos, etc. for an item, given the item_id
	 * @param integer the item_id
	 * @return the result object
	 */
	public function get_media($item_id) {
		$this->db->where('item_id',$item_id);
		$media = $this->db->get('media');
		
		if($media[0])
			return $media[0]->media;
		else
			return '{}';
	}
	
	/**
	 * Save temporary media items in the database
	 * @param integer the user_id
	 * @param string the filename
	 * @param integer the width of the media
	 * @param integer the height of the media
	 * @return the count of records changed
	 */
	public function save_temp_media($user_id,$filename,$width='',$height='') {
		$save_status = $this->db->insert('media_temp',array('user_id'=>$user_id,'filename'=>$filename,'width'=>$width,'height'=>$height,'timestamp'=>time()));
		return count($save_status);
	}
	
	/**
	 * Retrieve temp media from the database
	 * @param integer the user_id
	 * @return an array of media items
	 */
	public function get_temp_media($user_id) {		
		$res = $this->db->query("select * from media_temp where user_id = '".$user_id."' or user_id = '".Session::instance()->id()."'");
		
		$media_arr = array('images'=>array());
		foreach($res as $row)
			$media_arr['images'][] = $row->filename;

		return $media_arr;
	}
	
	/**
	 * Delete temp media from the database
	 * @param boolean remove_files
	 * @return an count of records changed
	 */
	// lets try always using session id, both logged in and not logged in users have it
	public function delete_temp_media($remove_files=TRUE) {
		//$user_id = (Auth::instance()->logged_in()) ? Auth::instance()->get_user()->id : Session::instance()->id();
		$user_id = Session::instance()->id();
		/*
		if(!empty($except)) {
			$notin = "'".implode("','",$except)."'";
			$this->db->notin('filename',$except);
		}
		*/
		$this->db->where('user_id',$user_id);
		$res = $this->db->get('media_temp');
		
		if($remove_files) {
			foreach($res as $row) {
				if(file_exists('img/upload/'.$row->filename))
					unlink('img/upload/'.$row->filename);
			}
		}	
		
		$res = $this->db->delete('media_temp',array('user_id'=>$user_id));
		
		return count($res);
	}
	
	/**
	 * Add watermark to image
	 * @param string the filename
	 * @param string the orientation
	 * @return true
	 */
	public function add_watermark_old($filename,$orientation='horizontal') {
		// /home/nerdcore/public_html/adshop/
		
		$info = getimagesize('img/upload/'.$filename);
		if($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg('img/upload/'.$filename);
		else if($info['mime'] == 'image/gif')
			$image = imagecreatefromgif('img/upload/'.$filename);
		else if($info['mime'] == 'image/png')
			$image = imagecreatefrompng('img/upload/'.$filename);
		else
			return false;
			
		imagealphablending($image, true);
			
		$logoImage = new Image('img/logo_watermark.png');
		
		$imageWidth = imagesx($image);
		$imageHeight = imagesy($image);
		
		$logoWidth_tmp = $logoImage->width;
		$logoHeight_tmp = $logoImage->height;
		
		$ideal_width = 524;
		$ideal_height = 393;
		$ideal_ratio = ($ideal_width/$ideal_height);
		if($orientation == 'horizontal') {
			$logoWidth = floor(($imageWidth/$ideal_width) * $logoWidth_tmp);
			$logoHeight = floor(($imageWidth/$ideal_width) * $logoHeight_tmp);
		}
		else {
			$logoWidth = floor(($imageHeight/$ideal_height) * $logoWidth_tmp);
			$logoHeight = floor(($imageHeight/$ideal_height) * $logoHeight_tmp);
		}		
		
		$logoImage->resize($logoWidth,$logoHeight);
		$now = time();
		$logoImage->save('img/logo_watermark-'.$now.'.png',0644,TRUE);
		
		$newLogo = new Image('img/logo_watermark-'.$now.'.png');
		$newLogoWidth = $newLogo->width;
		$newLogoHeight = $newLogo->height;
		
		$logoImage = imagecreatefrompng('img/logo_watermark-'.$now.'.png');
		imagealphablending($logoImage, true);
		
		imagecopy($image, $logoImage, ($imageWidth-$logoWidth-5), $imageHeight-$logoHeight, 0, 0, $newLogoWidth, $newLogoHeight);
		imagejpeg($image, 'img/upload/'.$filename, 95);
		
		imagedestroy($image);
		imagedestroy($logoImage);
		unlink('img/logo_watermark-'.$now.'.png');
		
		return true;
	}
	
	public function add_watermark($filename,$orientation='horizontal') { 
		$pathinfo = pathinfo('img/upload/'.$filename);
		$info = getimagesize('img/upload/'.$filename);
		if($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg('img/upload/'.$filename);
		else if($info['mime'] == 'image/gif')
			$image = imagecreatefromgif('img/upload/'.$filename);
		else if($info['mime'] == 'image/png')
			$image = imagecreatefrompng('img/upload/'.$filename);
		else
			return false;

		$filename = preg_replace('/-orig/','',$pathinfo['filename']);
		$newImage = $filename.'.jpg';

		//header('Content-type: image/jpeg');
		if(file_exists($newImage))
			unlink('img/upload/'.$newImage);
			
		$output = imagecreatetruecolor($info[0], $info[1]);
		$white = imagecolorallocate($output,  248, 248, 248);
		imagefilledrectangle($output, 0, 0, $info[0], $info[1], $white);
		imagecopy($output, $image, 0, 0, 0, 0, $info[0], $info[1]);
		
		imagejpeg($output,'img/upload/'.$newImage,100);
		imagedestroy($image);

		$image = imagecreatefromjpeg('img/upload/'.$newImage);

		$logoInfo = getimagesize('img/logo_watermark.png');
		$logoImage = imagecreatefrompng('img/logo_watermark.png');
		imagealphablending($logoImage, true);

		imagecopy($image, $logoImage, ($info[0]-$logoInfo[0]-5), $info[1]-$logoInfo[1], 0, 0, $logoInfo[0], $logoInfo[1]);
		if(file_exists($newImage))
			unlink('img/upload/'.$newImage);
		imagejpeg($image,'img/upload/'.$newImage,95);

		imagedestroy($image);

		//if(file_exists($filename))
			//unlink('img/upload/'.$filename);
			
		return true;
	}
	
	/**
	 * Save item to the database
	 * @param array the item data
	 * @param json string the media data
	 * @return the item_id
	 */
	public function save($item_arr,$media_json) {
		//$user,$cat,$subcat,$title,$desc,$price,$location,$trade,$allow_email

		$save_status = $this->db->insert('items', $item_arr);
		
		//$media = $this->get_temp_media($item_arr['user_id']);
		
		//$media = array('images'=>array());
		//$media['images'] = explode(',',$media_arr);
		
		$media_arr = json_decode($media_json,true);
		$media_status = 0;
		if(count($media_arr) > 0)			
			$media_status = $this->db->insert('media',array('item_id'=>$save_status->insert_id(),'media'=>$media_json,'timestamp'=>time()));
		
		$this->delete_temp_media(0);
		
		return $save_status->insert_id();
	}
	
	/**
	 * Update item in the database
	 * @param array the item data
	 * @param json string the media data
	 * @return count of records changed
	 */
	public function update($item_arr,$media_json) {
		$update_status = $this->db->update('items', $item_arr, array('item_id'=>$item_arr['item_id']));
		
		//Kohana::log('info', Kohana::debug($update_status));
		
		$media_arr = json_decode($media_json,true);
		
		Kohana::log('info', print_r($media_arr,true));
		
		$media_status = 0;
		if(count($media_arr) > 0 && !isset($media_arr[0]['mobile'])) {
			//Kohana::log('info', $item_arr);
			//Kohana::log('info', $media_json);
			if($this->db->count_records('media',array('item_id'=>$item_arr['item_id'])) > 0)
				$media_status = $this->db->update('media',array('media'=>$media_json,'timestamp'=>time()),array('item_id'=>$item_arr['item_id']));
			else
				$media_status = $this->db->insert('media',array('item_id'=>$item_arr['item_id'],'media'=>$media_json,'timestamp'=>time()));
				
			$this->delete_temp_media(0);
		}
		else if(!isset($media_arr[0]['mobile'])) {
			$media_status = $this->db->delete('media',array('item_id'=>$item_arr['item_id']));
		}
		
		//return (count($update_status) + count($media_status));
		return 1;
	}
	
	/**
	 * Renew item in the database
	 * @param integer the item_id
	 * @param integer the term of renewal
	 * @return count of records changed
	 * 
	 * @todo: change 'active'=>'0' and re-activate on payment callback
	 */
	public function renew($item_id,$term) {
		$publish_timestamp = time();
		$end_of_term = strtotime('+'.$term.' months');
		
		$status = $this->db->update('items', array('active'=>'0','term'=>$term,'publish_timestamp'=>$publish_timestamp,'expire_timestamp'=>$end_of_term), array('item_id'=>$item_id));
		
		return count($status);
	}
	
	/**
	 * Remove item from the database (mark as sold)
	 * @param integer the item_id
	 * @return count of records changed
	 */
	public function remove($item_id) {		
		$status = $this->db->update('items', array('sold'=>'1','sold_timestamp'=>time()), array('item_id'=>$item_id));
		
		return count($status);
	}
	
	/**
	 * Remove item from the database (delete for good)
	 * @param integer the item_id
	 * @return count of records changed
	 */
	public function delete($item_id) {		
		$status = $this->db->delete('items', array('item_id'=>$item_id));
		
		return count($status);
	}
	
	/**
	 * Increment view count for an item
	 * @param integer the item_id
	 * @return count of records changed
	 */
	public function add_view($item_id) {
		$res = $this->db->select('views')->where(array('item_id'=>$item_id))->get('items');
		$status = $this->db->update('items',array('views'=>$res[0]->views+1),array('item_id'=>$item_id));
		
		return count($status);
	}
	
	/**
	 * Activate an item (mark as active)
	 * @param integer the item_id
	 * @return count of records changed
	 */
	public function activate($item_id) {
		$publish_timestamp = time();		
		$status = $this->db->update('items',array('active'=>'1','publish_timestamp'=>$publish_timestamp),array('item_id'=>$item_id));
		return count($status);
	}
	
	/**
	 * Deactivate an item (mark as inactive)
	 * @param integer the item_id
	 * @return count of records changed
	 */
	public function deactivate($item_id) {
		$status = $this->db->update('items',array('active'=>'0'),array('item_id'=>$item_id));
		return count($status);
	}
	
	/**
	 * Get a list of items that will expire in 48 hours
	 * @return array of items
	 */
	public function remind() {
		$res = $this->db->query('SELECT `items`.`item_id`, `items`.`expire_timestamp`, `users`.`username` FROM (`items`) JOIN `users` ON (`users`.`id` = `items`.`user_id`) WHERE floor((`items`.`expire_timestamp` - unix_timestamp()) / 3600) = 48');
		
		return $res;
	}
	
	/**
	 * Get a list of expired items
	 * @return count of items
	 */
	public function expire_items() {
		$res = $this->db->query('SELECT `item_id`, `expire_timestamp` from `items` where `sold` = 0 and `expire_timestamp` < unix_timestamp() and `expire_timestamp` != 0');
		foreach ($res as $row) {
			$status = $this->db->update('items',array('active'=>'0'),array('item_id'=>$row->item_id));	// UNCOMMENT TO COMMIT CHANGE TO DB
			//Kohana::log('info','expired: '.$row->item_id);	
		}
			
		return count($res);
	}
	
	public function fixphones() {
		$res = $this->db->get('items');
		foreach ($res as $row) {
			$number = explode(' ',$row->owner_phone);
			if(isset($number[1])) {
				$prefix = $number[0];
				$phone = $number[1];
			}
			else {
				$prefix = substr($row->owner_phone, 0, 2);
				$phone = substr($row->owner_phone, 2);
			}
			
			$status = $this->db->update('items',array('owner_phone_prefix'=>$prefix,'owner_phone'=>$phone),array('item_id'=>$row->item_id));
		}
	}
	
	public function slugs() {
		$res = $this->db->get('items');
		foreach ($res as $row) {
			$slug = url::title($row->title);
			$status = $this->db->update('items',array('slug'=>$slug),array('item_id'=>$row->item_id));
		}
	}
	
	public function fixmedia() {
		return $res = $this->db->get('media');
	}
}
?>