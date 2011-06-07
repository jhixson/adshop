<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<?php if(isset($error_msg)): ?>
<p class="error"><?php echo $error_msg ?></p>
<?php endif; ?>

<?php if(isset($success_msg)): ?>
<p class="success"><?php echo $success_msg ?></p>
<?php endif; ?>
		