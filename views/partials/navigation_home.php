<h2 class="content_title"><img src="<?= $modules_assets ?>twitter_32.png"> Twitter</h2>
<ul class="content_navigation">
	<?= navigation_list_btn('home/twitter', 'Home') ?>
	<?= navigation_list_btn('home/twitter/replies', '@ Replies') ?>
	<?= navigation_list_btn('home/twitter/direct_messages', 'Direct Messages') ?>
	<?= navigation_list_btn('home/twitter/favorites', 'Favorites') ?>
	<?php if ($logged_user_level_id == 1) echo navigation_list_btn('home/twitter/settings', 'Settings') ?>	
</ul>