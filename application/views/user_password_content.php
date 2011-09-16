<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

	<form id="password_form" class="form_section" action="" method="post">
		<?php if(isset($error_msg)): ?>
		<p class="error"><?php echo $error_msg ?></p>
		<?php endif; ?>
		<?php if(isset($success_msg)): ?>
    <p class="success"><?php echo $success_msg ?></p>
    <p><a href="<?php echo url::base() ?>user/myAds">View My Ads</a>
		<?php else: ?>
		<?php 
		/*
		<div class="formitem clearfix">
			<label for="current_password">Temp. Password:</label>
			<input type="password" class="text password" name="current_password" id="current_password" />
		</div>
		*/
		?>
		<div class="formitem clearfix">
			<label for="new_password">New Password:</label>
			<input type="text" class="text password" name="new_password" id="new_password" value="Enter your password of choice here." />
		</div>
		
		<input type="hidden" name="user" value="<?php echo $user ?>" />
		
		<div class="buttons">
			<a href="#" class="button right" id="change_password_button"><span>Set Password</span></a>
		</div>
		<?php endif; ?>
	</form>
