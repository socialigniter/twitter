<?= $status_updater ?>

	<!--
	   [coordinates] => 
	    [in_reply_to_screen_name] => adam_griffiths
	    [geo] => 
	    [favorited] => 
	    [truncated] => 
	    [in_reply_to_status_id] => 15006766429
	    [source] => web
	    [contributors] => 
	    [in_reply_to_user_id] => 815638
	    [user] => stdClass Object
	        (
	            [profile_background_image_url] => http://a1.twimg.com/profile_background_images/3941228/back.jpg
	            [profile_link_color] => b42400
	            [followers_count] => 714
	            [url] => http://brennannovak.com
	            [description] => Future Positive, Designer, Developer,  Goofball, Believer of Magic. Builder of @eco_heroes
	            [profile_background_tile] => 1
	            [friends_count] => 480
	            [profile_sidebar_fill_color] => ffffff
	            [location] => Portland, OR
	            [statuses_count] => 4068
	            [notifications] => 
	            [profile_image_url] => http://a3.twimg.com/profile_images/779323865/thumbs_up_peace_normal.jpg
	            [favourites_count] => 178
	            [profile_sidebar_border_color] => E6E6E6
	            [contributors_enabled] => 
	            [lang] => en
	            [screen_name] => brennannovak
	            [geo_enabled] => 1
	            [profile_background_color] => ffffff
	            [protected] => 
	            [following] => 
	            [verified] => 
	            [time_zone] => Pacific Time (US & Canada)
	            [created_at] => Mon Dec 08 06:54:59 +0000 2008
	            [name] => Brennan Novak
	            [profile_text_color] => 333
	            [id] => 17958179
	            [utc_offset] => -28800
	        )
	    [created_at] => Sun May 30 00:11:23 +0000 2010
	    [id] => 15006861202
	    [place] => 
	    [text] => @adam_griffiths I recommend 1Password it's only like $30...
	-->
	
	<ol class="repeating_content" id="timeline">
		<?php
		if (!empty($timeline)) :
		foreach ($timeline as $status) : 
		?>
		<li class="repeating_item <?= $status->user->screen_name ?>" id="status_<?= $status->id; ?>">
			<span class="status_thumbnail">
				<a href="<?= base_url()."profile/".$status->user->screen_name ?>"><img src="<?= $status->user->profile_image_url ?>" border="0" /> </a>
			</span>
			<span class="status_text">
				<b><a href="<?= base_url()."profile/".$status->user->screen_name ?>"><?= $status->user->screen_name ?></a></b> <?= text_linkify($status->text) ?>
				<span class="status_meta"><?= $status->created_at ?></span>
			</span>	
			<ul class="status_actions">
				<?php if ($status->user->id == $this->session->userdata('user_id')) { ?>
				<li><span class="status_actions delete"><a href="#">Delete</a></span></li>
				<?php } else { ?>
				<li><span class="status_actions reply"><a href="#">Reply</a></span></li>
				<?php } ?>
			</ul>	
		</li>
		<?php endforeach; else : ?>
		<li>No updates from anyone :(</li>
		<?php endif; ?>
	</ol>
	
<div class="clear"></div>