<style type="text/css">
/* Dashboard - Twitter */
#feed								{ margin: 0; }
#feed li							{ float: left; }
#feed ul.item_actions				{ position: relative; bottom: 8px; right: 5px; }

li.item								{ width: 645px; margin: 0; padding: 0; background: url(<?= $dashboard_assets ?>images/item_separator.png) left bottom repeat-x; margin: 0 0 5px 0; }
li.item:hover ul.item_actions		{ visibility: visible; position: relative; bottom: 8px; right: 0px !important; }
li.item_new							{ width: 645px; margin: 0; padding: 0; background: url(<?= $dashboard_assets ?>images/item_separator.png) left bottom repeat-x; margin: 0 0 5px 0; }
li.item_new:hover ul.item_actions	{ visibility: visible; position: relative; bottom: 8px; right: 0px !important; }
li.item_created						{ background: #f2e3af; }
li.item_link						{ width: 100%; height: 2px; background: url(<?= $dashboard_assets ?>images/item_separator.png) left bottom repeat-x; margin: 0 0 5px 0; }

div.item_thumbnail 					{ width: 48px; height: 48px; display: block; position: relative; top: 7px; left: 0; margin: 0 20px 18px 15px; overflow: hidden; float: left; }
div.item_content 					{ width: 550px; display: block; line-height: 21px; margin: 2px 0 5px 0; position: relative; top: 2px; left: 0; float: left; }
div.item_content_small 				{ width: 400px; display: block; line-height: 21px; margin: 0 0 10px 0; position: relative; top: 8px; left: 0; float: left; }
span.item_separator					{ width: 100%; height: 2px; display: block; background: url(<?= $dashboard_assets ?>images/item_separator.png) 0 0 repeat-x; margin: 6px 0 15px 0; }
span.item_verb						{ color: #999999; }
span.item_content_body				{ width: 450px; float: left; }
span.item_content_body_small		{ width: 375px; float: left; word-wrap: break-word; word-break: inherit;  }
img.item_content_thumb				{ width: 125px; display: block; float: left; margin: 0 15px 0 0; }
span.item_content_detail			{ width: 350px; display: block; float: left; margin: 4px 0; color: #999999; font-size: 12px; line-height: 21px; }
span.item_content_detail_sm			{ width: 250px; display: block; float: left; margin: 4px 0; color: #999999; font-size: 12px; line-height: 21px; }
a.item_meta 						{ height: 12px; display: block; margin: 5px 0 0 0; font-size: 12px; line-height: 12px; color: #999999 !important; width: 150px; float: left; overflow: hidden; }
a.item_meta:hover					{ color: #2078CE !important; }
</style>