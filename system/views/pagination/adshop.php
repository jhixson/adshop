<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * AdShop pagination style
 * 
 * @preview  <-Previous		page#		Next->
 */
?>

<p class="pagination">

	<?php if($current_page == 2): ?>
		<a href="<?php echo str_replace('page/{page}', '', $url) ?>" class="button arrow_left"><img src="/img/arrow_left.png" alt="arrow" /><span>Previous</span></a>
	<?php elseif($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>" class="button arrow_left"><img src="/img/arrow_left.png" alt="arrow" /><span>Previous</span></a>
	<?php elseif ($first_page): ?>
		<a href="<?php echo str_replace('{page}', 1, $url) ?>" class="button arrow_left"><img src="/img/arrow_left.png" alt="arrow" /><span>Previous</span></a>
	<?php else: ?>
		<a href="#" class="button arrow_left disabled"><img src="/img/arrow_left_disabled.png" alt="arrow" /><span>Previous</span></a>
	<?php endif ?>



	<?php for ($i = 1; $i <= $total_pages; $i++): ?>

		<?php if ($i == $current_page): ?>
			<strong><?php echo 'Page '.$i ?></strong>
		<?php endif ?>

	<?php endfor ?>


	<?php if($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $url) ?>" class="button right arrow_right"><img src="/img/arrow_right.png" alt="arrow" /><span>Next</span></a>
	<?php elseif ($last_page): ?>
		<a href="<?php echo str_replace('{page}', $last_page, $url) ?>" class="button right arrow_right"><img src="/img/arrow_right.png" alt="arrow" /><span>Next</span></a>
	<?php else: ?>
		<a href="#" class="button right arrow_right disabled"><img src="/img/arrow_right_disabled.png" alt="arrow" /><span>Next</span></a>
	<?php endif ?>

</p>