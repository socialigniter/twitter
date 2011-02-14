<?php
class Connections extends MY_Controller
{
	protected $module_site;

    function __construct()
    {
        parent::__construct();
		   
		if (config_item('twitter_enabled') != 'TRUE') redirect(base_url());
	
		$this->load->library('twitter');
		
		$this->module_site = $this->social_igniter->get_site_view('module', 'twitter');
	}

	function index()
	{	
		if ($this->social_auth->logged_in()) redirect('connections/twitter/add');

		$tokens['access_token'] 		= NULL;
		$tokens['access_token_secret'] 	= NULL;
		$process_image					= FALSE;
	
		// Get Auth Tokens
		$auth = $this->twitter->oauth(config_item('twitter_consumer_key'), config_item('twitter_consumer_key_secret'), $tokens['access_token'], $tokens['access_token_secret'], base_url().'connections/twitter');
	
		// Returning from Twitter if tokens exist
		if (isset($auth['access_token']) && isset($auth['access_token_secret']))
		{
			$connection = $this->social_auth->check_connection_auth('twitter', $auth['access_token'], $auth['access_token_secret']);

			// Already Connected			
			if ($connection)
			{
				// Login
	        	if ($this->social_auth->social_login($connection->user_id, 'twitter')) 
	        	{
		        	$this->session->set_flashdata('message', "Login with Twitter was successful");
		        	redirect(base_url().'home', 'refresh');
		        }
		        else 
		        { 
		        	$this->session->set_flashdata('message', "Login with Twitter in-correct");
		        	redirect("login", 'refresh');
		        }		    
			}
			else
			{
				$twitter_user = $this->twitter->call('account/verify_credentials');
			
				// Checks for non default image
				if (($twitter_user->profile_image_url) && (!preg_match('/default_profile_/', $twitter_user->profile_image_url))) $process_image = TRUE;
				
				// If so snatch it up					
				if ($process_image)
				{
			   		$image_full		= str_replace('_normal', '', $twitter_user->profile_image_url); 
					$image_name		= $twitter_user->screen_name.'.'.pathinfo($image_full, PATHINFO_EXTENSION);
					$image_save		= $this->config->item('profile_raw_path').$image_name;
			    }
			    else
			    {
			    	$image_name		= "";
			    	$image_save 	= "";
			    }
				
				// Converts Timezone
	        	$offset				= $twitter_user->utc_offset / 60 / 60;

				foreach(timezones() as $key => $zone)
				{
					if ($offset === $zone) $time_zone = $key;						
				}
								
				// User Data
	        	$additional_data = array(
    				'name' 		 	=> $twitter_user->name,
					'location'	 	=> $twitter_user->location,
					'bio' 		 	=> $twitter_user->description,
					'url'	 	 	=> $twitter_user->url,
					'image'		 	=> $image_name,
					'home_base'		=> 'twitter',
					'language'		=> $twitter_user->lang,
					'time_zone'		=> $time_zone,
					'geo_enabled'	=> $twitter_user->geo_enabled,
					'utc_offset' 	=> $twitter_user->utc_offset
	        	);
	        			       			      				
	        	// Register User
	      		$user_id = $this->social_auth->social_register($twitter_user->screen_name, $twitter_user->screen_name.'@twitter', $additional_data);
	        		        	
	        	if($user_id)
	        	{	
	        		// Process Image	        	
					if ($process_image)
	        		{
		        		$this->load->model('image_model');													

		        		// Snatch Twitter Image
						$this->image_model->get_external_image($image_full, $image_save);		        	

		        		// Process Thumbnail Images Now
						$image_size 	= getimagesize($image_save);
						$image_width 	= $image_size[0];
						$image_height	= $image_size[1];
								        		
						$this->image_model->make_profile_images($image_name, $image_width, $image_height, $user_id); 						
						unlink($image_save);	
					}					

	        		$this->session->set_flashdata('message', "User Created");		    			        		
	       		}
	       		else
	       		{
	        		$this->session->set_flashdata('message', "Error Creating User");
	       		}
	       		
	       		$connection_data = array(
	       			'site_id'				=> $this->module_site[0]->site_id,
	       			'user_id'				=> $user_id,
	       			'module'				=> 'twitter',
	       			'type'					=> 'primary',
	       			'connection_user_id'	=> $twitter_user->id,
	       			'connection_username'	=> $twitter_user->screen_name,
	       			'auth_one'				=> $auth['access_token'],
	       			'auth_two'				=> $auth['access_token_secret']
	       		);
	       							
				// Add Connection
				$connection = $this->social_auth->add_connection($connection_data);
				
				// Login
	        	$this->social_auth->social_login($connection->user_id, 'twitter');
		        $this->session->set_flashdata('message', "Login with Twitter was successful");
		        
		        redirect(base_url().'home', 'refresh');
			}
		}
		else
		{
			redirect('connections/twitter', 'refresh');
		}
	}

	function add()
	{		
		if (!$this->social_auth->logged_in()) redirect('connections/twitter');

		$tokens['access_token'] 		= NULL;
		$tokens['access_token_secret'] 	= NULL;

		// Get Auth Tokens
		$auth = $this->twitter->oauth(config_item('twitter_consumer_key'), config_item('twitter_consumer_key_secret'), $tokens['access_token'], $tokens['access_token_secret'], base_url().'connections/twitter/add');
	
		// Returning from Twitter if tokens exist
		if (isset($auth['access_token']) && isset($auth['access_token_secret']))
		{
			// Check Connected			
			$check_connection = $this->social_auth->check_connection_auth('twitter', $auth['access_token'], $auth['access_token_secret']);
	
			if ($check_connection)
			{
				$this->session->set_flashdata('message', "You've already connected this Twitter account");
				redirect('settings/connections', 'refresh');							
			}
			else
			{			
				// Get User				
				$twitter_user = $this->twitter->call('account/verify_credentials');
				
	       		$connection_data = array(
	       			'site_id'				=> $this->module_site[0]->site_id,
	       			'user_id'				=> $this->session->userdata('user_id'),
	       			'module'				=> 'twitter',
	       			'type'					=> 'primary',
	       			'connection_user_id'	=> $twitter_user->id,
	       			'connection_username'	=> $twitter_user->screen_name,
	       			'auth_one'				=> $auth['access_token'],
	       			'auth_two'				=> $auth['access_token_secret']
	       		);
	       							
				// Add Connection
				$connection = $this->social_auth->add_connection($connection_data);
				
				if($connection)
				{
					$this->social_auth->set_userdata_connections($this->session->userdata('user_id'));
				
					$this->session->set_flashdata('message', "Twitter account connected");
				 	redirect('settings/connections', 'refresh');
				}
				else
				{
				 	$this->session->set_flashdata('message', "That Twitter account is connected to another user");
				 	redirect('settings/connections', 'refresh');
				}
			}
		}
		else
		{
			redirect('connections/twitter/add', 'refresh');		
		}	
	}

}