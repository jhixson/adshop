<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

	<form id="login_form" class="form_section" action="" method="post">
		<?php if(isset($success_msg)): ?>
		<p class="success"><?php echo $success_msg ?></p>
		<?php else: ?>
		<?php if(isset($error_msg)): ?>
		<p class="error"><?php echo $error_msg ?></p>
		<?php endif; ?>
		<div class="formitem clearfix">
			<label for="login_username">E-mail:</label>
			<input type="email" class="text required email <?php echo isset($email) ? ' active' : '' ?>" name="username" id="login_username" value="<?php echo isset($email) ? $email : '' ?>" />
		</div>
		
		<div class="formitem clearfix">
			<label for="login_password">Password:</label>
			<label class="inset">Leave password blank if you forgot it.</label>
			<input type="password" class="text password" name="password" id="login_password" />
		</div>
		
		<input type="hidden" name="action" value="<?php echo $action ?>" />
		<input type="hidden" name="item_id" value="<?php echo $item_id ?>" />
		
		<div class="buttons">
			<a href="#" class="button right" id="login_button"><span><?php echo $login_label ?></span></a>
		</div>
		<?php endif; ?>
	</form>