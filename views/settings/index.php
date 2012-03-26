<form name="settings_update" id="settings_update" method="post" action="<?= base_url() ?>api/settings/modify" enctype="multipart/form-data">
<div class="content_wrap_inner">

	<div class="content_inner_top_right">
		<h3>App</h3>
		<p><?= form_dropdown('enabled', config_item('enable_disable'), $settings['twitter']['enabled']) ?></p>
		<p><a href="<?= base_url() ?>api/<?= $this_module ?>/reinstall" id="app_reinstall" class="button_action">Reinstall</a>
		<a href="<?= base_url() ?>api/<?= $this_module ?>/uninstall" id="app_uninstall" class="button_delete">Uninstall</a></p>
	</div>
	
	<h3>Application Keys</h3>

	<p>Twitter requires <a href="https://twitter.com/apps" target="_blank">registering your application</a></p>
				
	<p><input type="text" name="consumer_key" value="<?= $settings['twitter']['consumer_key'] ?>"> Consumer Key </p> 
	<p><input type="text" name="consumer_secret" value="<?= $settings['twitter']['consumer_secret'] ?>"> Consumer Secret</p>


</div>

<span class="item_separator"></span>

<div class="content_wrap_inner">

	<h3>Social</h3>

	<p>Login
	<?= form_dropdown('social_login', config_item('yes_or_no'), $settings['twitter']['social_login']) ?>
	</p>

	<p>Login Redirect<br>
	<?= base_url() ?> <input type="text" size="30" name="login_redirect" value="<?= $settings['twitter']['login_redirect'] ?>" />
	</p>	
	
	<p>Connections 
	<?= form_dropdown('social_connection', config_item('yes_or_no'), $settings['twitter']['social_connection']) ?>
	</p>

	<p>Connections Redirect<br>
	<?= base_url() ?> <input type="text" size="30" name="connections_redirect" value="<?= $settings['twitter']['connections_redirect'] ?>" />
	</p>

	<p>Post
	<?= form_dropdown('social_post', config_item('yes_or_no'), $settings['twitter']['social_post']) ?>	
	</p>

	<p>Archive Tweets
	<?= form_dropdown('archive', config_item('yes_or_no'), $settings['twitter']['archive']) ?>
	</p>

</div>

<span class="item_separator"></span>

<div class="content_wrap_inner">

	<h3>Default Account</h3>

	<p>Auto Publish
	<?= form_dropdown('auto_publish', config_item('yes_or_no'), $settings['twitter']['auto_publish']) ?>
	</p>

	<p>@ <input type="text" name="default_account" value="<?= $settings['twitter']['default_account'] ?>"></p> 
	
	<p><a href="#">Connect</a> a Twitter account for this site to generate automatic tweets.</p>

	<input type="hidden" name="module" value="<?= $this_module ?>">

	<p><input type="submit" name="save" value="Save" /></p>

</div>

</form>

<?= $shared_ajax ?>