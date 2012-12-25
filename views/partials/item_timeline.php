<li class="item" id="item_<?= $item_id; ?>" rel="timeline">
	<div class="item_thumbnail">
		<a href="<?= $item_profile ?>"><img src="<?= $item_avatar ?>" /></a>
	</div>
	<div class="item_content">
		<span class="item_content_body">
			<b><a href="<?= $item_profile ?>"><?= $item_contributor ?></a></b> <?= $item_content ?>		
		</span>		
		<?php if ($item_type): ?><span class="item_type<?= $item_type ?>"></span><?php endif; ?>
		<div class="clear"></div>
		<a href="<?= $item_url ?>" class="item_meta"><?= $item_date ?></a>
		<ul class="item_actions" rel="timeline">
			<li><a class="item_edit" href="<?= $item_id; ?>" id="item_action_edit_<?= $item_id ?>"><span class="actions action_edit"></span> Archive</a></li>
		</ul>
		<div class="clear"></div>		
	</div>
	<div class="clear"></div>	
</li>