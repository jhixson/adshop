<?php defined('SYSPATH') or die('No direct script access.');

class Payment_Model extends Model {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function store_transaction($payment_data) {
		//$trans_status = $this->db->insert('transactions',array('item'=>$post_arr['item_name'],'amount'=>$post_arr['mc_gross'],'name'=>$post_arr['first_name'].' '.$post_arr['last_name'],'email'=>$post_arr['payer_email'],'status'=>$post_arr['payment_status'],'signature'=>$post_arr['verify_sign'],'timestamp'=>time()));
		$trans_status = $this->db->insert('transactions',$payment_data);
		return count($trans_status);
	}
	
	public function valid_coupon($code) {
		$this->db->where(array('code'=>$code,'redeemed'=>0));
		$res = $this->db->get('coupons');
		return count($res);
	}
	
	public function apply_coupon($code,$user_id,$item_id) {
		Kohana::log('info', 'coupon: '.$code." ".$user_id." ".$item_id);
		$this->db->where(array('code'=>$code,'redeemed'=>0));
		$res = $this->db->get('coupons');
		if(count($res) > 0) {
			$this->db->update('coupons',array('user_id'=>$user_id,'item_id'=>$item_id,'redeemed'=>1,'timestamp'=>time()),array('code'=>$code));
			$apply_status = $this->db->update('items',array('active'=>1),array('user_id'=>$user_id,'item_id'=>$item_id));
			return count($apply_status);
		}
		else
			return FALSE;
	}
 
}
