<?php defined('SYSPATH') or die('No direct script access.');

class Tooltip_Model extends Model {
	
	public $tip_content = array('tip1'=>"<p>&quot;It's very simple. We want to continue to be the easiest to use, best value classified ads website in Ireland.&quot;</p>",
								'tip2'=>"<div class=\"inner\"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer sed tristique lectus. Donec sodales leo quis sem blandit eget convallis nulla faucibus. In venenatis, magna in posuere porta, turpis sem consequat justo, sit amet rutrum magna est id mauris. Aenean magna mauris, placerat et laoreet vel, convallis eu nibh. Suspendisse sed nibh neque. Suspendisse sed porta orci. In dictum erat id ligula porta ultrices. Donec lectus sapien, semper eu ultricies eget, condimentum non justo. Vivamus fringilla tristique nibh, vitae pharetra lectus venenatis ut. Aliquam erat volutpat. Nunc ut iaculis libero. Nullam neque urna, lacinia et bibendum at, posuere ac urna. Aenean eget velit non augue aliquet cursus id et mi. Sed ut magna enim.</p><p>Morbi id elit massa. Suspendisse et magna sit amet elit tempor convallis in in urna. Aliquam tincidunt lorem a odio posuere sit amet fringilla arcu convallis. Ut ornare porta viverra. Praesent a quam sit amet erat semper faucibus. Nulla facilisi. Mauris tempus tortor quis lacus pretium eu sollicitudin elit placerat. Curabitur mattis hendrerit aliquam. Mauris tempus ligula at quam egestas vitae molestie velit consequat. Pellentesque in molestie nisl. Fusce sit amet leo vitae mauris tincidunt convallis. Vivamus tempus facilisis augue, et rutrum risus tristique id. Sed non velit sapien.</p></div>",
								'tip3'=>'<div class="formitem inset"><label for="login_email">Your E-mail</label><input type="text" id="login_email" /></div><div class="formitem inset"><label for="login_password">Login Password</label><input type="password" id="login_password" /></div><div class="formitem"><a class="button right arrow_right" href="#"><img src="/adshop/img/arrow_right.png" alt="arrow" /><span>Login</span></a><br style="clear: both;" /></div><br style="clear: both;" />',
								'tip4'=>'<div class="formitem inset"><label for="contact_name">Your Name</label><input type="text" id="contact_name" /></div><div class="formitem inset"><label for="contact_email">Your E-mail</label><input type="text" id="contact_email" /></div><div class="formitem inset"><label for="contact_message">Questions / Comments</label><textarea id="contact_message" rows="5"></textarea></div><div class="formitem"><a class="button right" href="#"><span>Send Message</span></a><br style="clear: both;" /></div><br style="clear: both;" />');
	
	public function __construct() {
		parent::__construct();
	}
 
	/**
	 * Get tooltip content, given the tip_id
	 * @param integer the tip_id
	 * @return the tooltip string
	 */
	public function get_tip($tip_id) {
		return $this->tip_content[$tip_id];
	}
}
?>