<?php
class Home extends Dashboard_Controller
{
    function __construct()
    {
        parent::__construct();
        
        if (config_item('twitter_enabled') != 'TRUE') redirect(base_url());
		   
		$this->data['page_title'] 	= 'Twitter';

		$this->check_connection = $this->social_auth->check_connection_user($this->session->userdata('user_id'), 'twitter', 'primary');

		if (!$this->check_connection)
		{
			$this->session->set_flashdata('message', 'You need to "connect" a Twitter account before you can use that feature');			
			redirect('/settings/connections');
		}

		$this->load->library('twitter_igniter', $this->check_connection);

	}
	
	function timeline()
	{
 		$timeline		= NULL;
		$timeline_view	= NULL;

 		// Type of Feed
		if ($this->uri->segment(3) == 'you')
		{
			$timeline 						= $this->twitter_igniter->get_user_timeline();	   
 	   		$this->data['sub_title'] 		= "Timeline";
 	   	}
		elseif ($this->uri->segment(3) == 'mentions')
		{
			$timeline 						= $this->twitter_igniter->get_mentions();
	 	    $this->data['sub_title'] 		= "@ Mentions";		
		}
		elseif ($this->uri->segment(3) == 'favorites')		
		{
			$timeline 						= $this->twitter_igniter->get_favorites();
	 	    $this->data['sub_title'] 		= "Favorites";		
		}
		else
		{
			$timeline = '';
		}

		// Build Feed				 			
		if (!empty($timeline))
		{
			foreach ($timeline as $tweet)
			{
				// Item
				$this->data['item_id']				= $tweet->id;
				$this->data['item_type']			= 'tweet';
				
				// Contributor
				$this->data['item_user_id']			= $tweet->user->id;
				$this->data['item_avatar']			= $tweet->user->profile_image_url;
				$this->data['item_contributor']		= $tweet->user->name;
				$this->data['item_profile']			= 'https://twitter.com/'.$tweet->user->screen_name;
				
				// Activity
				$this->data['item_content']			= item_linkify($tweet->text);
				$this->data['item_content_id']		= $tweet->id;
				$this->data['item_date']			= timezone_datetime_to_elapsed($tweet->created_at);	
				$this->data['item_url']				= 'https://twitter.com/'.$tweet->user->screen_name.'/statuses/'.$tweet->id;	

		 		// Actions
			 	$this->data['item_comment']			= base_url().'comment/item/'.$tweet->id;
			 	$this->data['item_comment_avatar']	= $this->data['logged_image'];
			 	
				// View
				$timeline_view .= $this->load->view('partials/item_timeline.php', $this->data, true);
	 		}
	 	}
	 	else
	 	{
	 		$timeline_view = '<li><p>No tweets to show from anyone</p></li>';
 		}
 		
		$this->data['timeline_view'] 	= $timeline_view;				
		$this->render();	
	}
}