<?php
class Connections extends MY_Controller
{
	protected $module_site;

    function __construct()
    {
        parent::__construct();
		   
		if (config_item('twitter_enabled') != 'TRUE') redirect(base_url());
	
		$this->load->library('twitter');
		
		$this->module_site = $this->social_igniter->get_site_view_row('module', 'twitter');
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
				$twitter_user = $this->twitter->call('account/verify_credentials');
				
				// Adds Auth Tokens
				if (connection_has_auth($connection))
				{
					$connection_data = array(
						'auth_one'	=> $auth['access_token'],
						'auth_two'	=> $auth['access_token_secret']
					);

					$this->social_auth->update_connection($connection->connection_id, $connection_data);
				}		    
				
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
				
				// Signup Social Data
				$this->session->set_userdata($twitter_user);
				$this->session->set_userdata('access_token', $auth['access_token']);
		   		$this->session->set_userdata('access_token_secret', $auth['access_token_secret']);
				$this->session->set_userdata('signup_name', $twitter_user->name);
				$this->session->set_userdata('signup_user_state', 'has_connection_data');
				$this->session->set_userdata('connection_signup_module', 'twitter');
				$this->session->set_userdata('connection_return_url', base_url().'connections/twitter/signup');
				
				redirect(base_url().'signup_social');		          
			}
		}
		else
		{
			redirect('connections/twitter', 'refresh');
		}
	}
	
	function signup()
	{
    	if ($this->session->userdata('signup_user_state') != 'has_connection_and_email') redirect('signup', 'refresh');

		// User Info		
		$twitter_image	= $this->session->userdata('profile_image_url');	
		$username 		= $this->session->userdata('screen_name');
		$email			= $this->session->userdata('signup_email');
				
		// Check Email Address
		$user = $this->social_auth->get_user('email', $email);
						
		if ($user)
		{
			// Empty Userdata
			$this->session->sess_destroy();
				        
	        // Redirect
	        redirect(base_url().'signup', 'refresh');	
		}
		else
		{	
			// If non default image
			if (($twitter_image) && (!preg_match('/default_profile_/', $twitter_image))) $process_image = TRUE;
			
			// If image, snatch it up					
			if ($process_image)
			{
		   		$image_full	= str_replace('_normal', '', $twitter_image); 
				$image_name	= $username.'.'.pathinfo($image_full, PATHINFO_EXTENSION);
		    }
		    else
		    {
		    	$image_name	= "";
		    }
			
			// Converts Timezone
	    	$offset		= $this->session->userdata('utc_offset') / 60 / 60;
	    	$timezone	= NULL;
			
			foreach(timezones() as $key => $zone)
			{
				if ($offset === $zone) $time_zone = $key;
			}
							
			// User Data
	    	$additional_data = array(
				'name' 		 	=> $this->session->userdata('name'),
				'image'		 	=> $image_name,
				'language'		=> $this->session->userdata('lang'),
				'time_zone'		=> $time_zone,
				'geo_enabled'	=> $this->session->userdata('geo_enabled')
	    	);
	    			       			      				
	    	// Register User
	  		$user_id = $this->social_auth->social_register($username, $email, $additional_data);
	    		        	
	    	if($user_id)
	    	{
				// Add Meta
				$user_meta_data = array(
	        		'location'	 => $this->session->userdata('location'),
					'bio' 		 => $this->session->userdata('description'),
					'url'	 	 => $this->session->userdata('url')
				);
				
				$this->social_auth->update_user_meta(config_item('site_id'), $user_id, 'users', $user_meta_data);
	    	
	    		// Process Image	        	
				if ($process_image)
	    		{
	        		$this->load->model('image_model');
	
	        		// Snatch Twitter Image
	        		$image_save	= $image_name;
					$this->image_model->get_external_image($image_full, config_item('uploads_folder').$image_save);
	
					// Process New Images
					$image_size 	= getimagesize(config_item('uploads_folder').$image_save);
					$file_data		= array('file_name'	=> $image_save, 'image_width' => $image_size[0], 'image_height' => $image_size[1]);
					$image_sizes	= array('full', 'large', 'medium', 'small');
					$create_path	= config_item('users_images_folder').$user_id.'/';
	
					$this->image_model->make_images($file_data, 'users', $image_sizes, $create_path, TRUE);
	
					unlink(config_item('uploads_folder').$image_save);
				}
								
		   		$connection_data = array(
		   			'site_id'				=> $this->module_site->site_id,
		   			'user_id'				=> $user_id,
		   			'module'				=> 'twitter',
		   			'type'					=> 'primary',
		   			'connection_user_id'	=> $this->session->userdata('id'),
		   			'connection_username'	=> $username,
		   			'auth_one'				=> $this->session->userdata('access_token'),
		   			'auth_two'				=> $this->session->userdata('access_token_secret')
		   		);
		   							
				// Add Connection
				$connection = $this->social_auth->add_connection($connection_data);
				
				// Empty Userdata
				//$this->session->sess_destroy();
				
				// Login
				if ($this->social_auth->social_login($user_id, 'twitter'))
				{
		        	$this->session->set_flashdata('message', "Login with Twitter was successful");
		        	redirect(base_url().'home', 'refresh');								
				}
				else
				{
	    			$this->session->set_flashdata('message', "User Created");		    			        		
	   			}
	   		}
	   		else
	   		{
	    		$this->session->set_flashdata('message', "Error Creating User");
	   		}
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
	
			if (connection_has_auth($check_connection))
			{			
				$this->session->set_flashdata('message', "You've already connected this Twitter account");
				redirect('settings/connections', 'refresh');							
			}
			else
			{			
				// Get User				
				$twitter_user = $this->twitter->call('account/verify_credentials');
				
	       		$connection_data = array(
	       			'site_id'				=> $this->module_site->site_id,
	       			'user_id'				=> $this->session->userdata('user_id'),
	       			'module'				=> 'twitter',
	       			'type'					=> 'primary',
	       			'connection_user_id'	=> $twitter_user->id,
	       			'connection_username'	=> $twitter_user->screen_name,
	       			'auth_one'				=> $auth['access_token'],
	       			'auth_two'				=> $auth['access_token_secret']
	       		);
	       		
	       		if ($check_connection)
	       		{
	       			// Update Connection
	       			$connection = $this->social_auth->update_connection($connection_data);
	       		}
	       		else
	       		{				
					// Add Connection
					$connection = $this->social_auth->add_connection($connection_data);
				}
				
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