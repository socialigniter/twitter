<?= $status_updater ?>
	
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