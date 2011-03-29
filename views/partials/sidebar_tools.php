<?php if (config_item('twitter_social_connection') == 'TRUE') { ?>
<li>
  	<a class="sidebar_icon" href="<?= base_url() ?>home/twitter/timeline"><img src="<?= $this_module_assets ?>twitter_24.png"><span>Twitter</span></a>
  	<span class="feed_count_new" rel="twitter" id="twitter_count_new"></span>
  	<!-- make API result return an object rather than a number... something like {module:messages,count:3} so that core modules or other modules can benefit -->
</li>
<?php } ?>