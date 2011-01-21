<?php
class Home extends Dashboard_Controller
{
    function __construct()
    {
        parent::__construct();
        
        if (config_item('twitter_enabled') != 'TRUE') redirect(base_url());

		$this->load->library('twitter');
		   
		$this->data['page_title'] 	= 'Twitter';
		$this->check_connection 	= $this->connections_model->check_connection_user($this->session->userdata('user_id'), 'twitter');
	}	
	
	function index()
	{
		$user_connections = $this->connections_model->get_user_connections_array($this->session->userdata('user_id'));

		$auth = $this->twitter->oauth(config_item('twitter_consumer_key'), config_item('twitter_consumer_key_secret'), $this->check_connection->token_one, $this->check_connection->token_two);									
		$user_timeline = $this->twitter->call('statuses/friends_timeline'); 	   

 	    $this->data['sub_title'] 			= "Home";

		$this->data['timeline'] 			= $user_timeline;

 	    $this->data['status_header'] 		= 'Whats Happening';
		$this->data['status_update']		= '';
		$this->data['post_to_social']		= $this->social_igniter->post_to_social($this->session->userdata('user_id'), $user_connections);

		$this->data['reply_to_status_id'] 	= $this->input->post('reply_to_status_id');
		$this->data['reply_to_user_id']		= $this->input->post('reply_to_user_id');
		$this->data['reply_to_username'] 	= $this->input->post('reply_to_username');

		$this->data['geo_locate']			= $this->session->userdata('geo_enabled');
		$this->data['geo_lat'] 				= $this->input->post('geo_lat');
		$this->data['geo_long'] 			= $this->input->post('geo_long');
		$this->data['geo_accuracy'] 		= $this->input->post('geo_accuracy');

		$this->data['status_updater']	= $this->load->view(config_item('dashboard_theme').'/partials/status_updater', $this->data, true);
				
		$this->render();	
	}
	
 	function replies()
 	{	
 		$this->load->library('twitter');
		$auth = $this->twitter->oauth(config_item('twitter_consumer_key'), config_item('twitter_consumer_key_secret'), $this->check_connection->token_one, $this->check_connection->token_two);									
		$user_timeline = $this->twitter->call('statuses/mentions');
      	   
 	    $this->data['sub_title'] 		= "@ Replies";
		$this->data['timeline'] 		= $user_timeline;
		
		$this->render();
 	}  
    
 	function direct_messages()
 	{
		$this->load->library('twitter');	
		$auth = $this->twitter->oauth(config_item('twitter_consumer_key'), config_item('twitter_consumer_key_secret'), $this->check_connection->token_one, $this->check_connection->token_two);									
		$user_timeline = $this->twitter->call('direct_messages');

 	    $this->data['sub_title'] 		= "Direct Messages";
		$this->data['timeline'] 		= $user_timeline;
		$this->render();
 	}  	

 	function favorites()
 	{
		$this->load->library('twitter');	
		$auth = $this->twitter->oauth(config_item('twitter_consumer_key'), config_item('twitter_consumer_key_secret'), $this->check_connection->token_one, $this->check_connection->token_two);									
		$user_timeline = $this->twitter->call('favorites');

 	    $this->data['sub_title'] 		= "Favorites";
		$this->data['timeline'] 		= $user_timeline;
		$this->render();
 	} 	
 
 	function post_to_social()
 	{
 	
	    if (($this->config->item('twitter')) && ($this->input->post('post_to_twitter') == 1))
    	{
			$this->load->library('twitter');	
			// IS THIS TWITTER ACCOUNT ALREADY CONNECTED			
			$check_connection = $this->connections_model->check_connection_user($this->session->userdata('user_id'), 'twitter');

			if ($check_connection)
			{
				$auth = $this->twitter->oauth($this->config->item('twitter_consumer_key'), $this->config->item('twitter_consumer_key_secret'), $check_connection->token_one, $check_connection->token_two);									
			}	

			$this->twitter->call('statuses/update', array('status' => $this->input->post('update')));
    	}
	}
	
	
}