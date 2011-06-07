<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

	<h2 class="heading">My Profile</h2>

	<form id="profile_form" class="form_section clearfix" action="" method="post">
		<?php if(isset($error_msg)): ?>
		<p class="error"><?php echo $error_msg ?></p>
		<?php endif; ?>
		<div class="formitem clearfix">
			<label for="profile_name">Name:</label>
			<label class="inset"><?php if(isset($user->name)) echo $user->name ?></label>
			<input type="text" class="text" name="name" id="profile_name" />
		</div>
		
		<div class="formitem clearfix">
			<label for="profile_email">E-mail:</label>
			<label class="inset"><?php if(isset($user->username)) echo $user->username ?></label>
			<input type="text" class="text email" name="username" id="profile_username" />
		</div>
		
		<div class="formitem clearfix">
			<label for="profile_location">Location:</label>
			<label class="inset"><?php if(isset($user->location)) echo $user->location ?></label>
			<input type="text" class="text" name="location" id="profile_location" />
		</div>
		
		<div class="formitem clearfix">
			<label for="profile_phone">Phone:</label>
			<label class="inset"><?php if(isset($user->phone)) echo $user->phone ?></label>
			<input type="text" class="text" name="phone" id="profile_phone" />
		</div>
		
		<div class="formitem clearfix">
			<label for="profile_password">Password:</label>
			<input type="password" class="text password" name="password" id="profile_password" />
		</div>
		
		<div class="buttons">
			<a href="#" class="button right" id="save_button"><span>Save</span></a>
		</div>
	</form>