<?php
class Connections extends MY_Controller
{
	protected $consumer;
	protected $twitter;
	protected $module_site;

    function __construct()
    {
        parent::__construct();
		   
		if (config_item('twitter_enabled') != 'TRUE') redirect(base_url());
		if ((config_item('twitter_consumer_key') == '') AND (config_item('twitter_consumer_secret') == ''))
		{
		    $this->session->set_flashdata('message', 'The Twitter app needs Application Keys before it connect');
			redirect(base_url().'settings/twitter');
		}
	
		// Load Library
        $this->load->library('oauth');
		
		// Get Site
		$this->module_site = $this->social_igniter->get_site_view_row('module', 'twitter');	
	}


	function index()
	{	
		// Is Logged In
		if ($this->social_auth->logged_in()) redirect('connections/twitter/add');
		
        // Create Consumer
        $consumer = $this->oauth->consumer(array(
            'key' 	 	=> config_item('twitter_consumer_key'),
            'secret' 	=> config_item('twitter_consumer_secret'),
            'callback'	=> base_url().'twitter/connections'
        ));

        // Load Provider
        $twitter = $this->oauth->provider('twitter');		
	
		// Send to Twitter
        if (!$this->input->get_post('oauth_token'))
        {		
            // Get request token for consumer
            $token = $twitter->request_token($consumer);

            // Store token
            $this->session->set_userdata('oauth_token', base64_encode(serialize($token)));

            // Redirect Twitter
            $twitter->authorize($token, array('oauth_callback' => base_url().'twitter/connections'));
		}
		else
		{
      		// Has Stored Token
            if ($this->session->userdata('oauth_token'))
            {
                // Get the token
                $token = unserialize(base64_decode($this->session->userdata('oauth_token')));
            }

			// Has Token
            if (!empty($token) AND $token->access_token !== $this->input->get_post('oauth_token'))
            {   
                // Delete token, it is not valid
                $this->session->unset_userdata('oauth_token');

                // Send the user back to the beginning
                exit('invalid token after coming back to site');
            }

            // Store Verifier
            $token->verifier($this->input->get_post('oauth_verifier'));

            // Exchange request token for access token
            $tokens = $twitter->access_token($consumer, $token);
		
			// Check Connection
			$check_connection = $this->social_auth->check_connection_auth('twitter', $tokens->access_token, $tokens->secret);

			// Load Tweet Library
			$this->load->library('tweet', array('access_key' => $tokens->access_token, 'access_secret' => $tokens->secret));	            
			
			// Get User Details
			$twitter_user = $this->tweet->call('get', 'account/verify_credentials');
			
			// Already Connected
			if ($check_connection)
			{
				// Adds Auth Tokens (if user had already been added via Twitter without having authed in)
				if (connection_has_auth($check_connection))
				{
					$connection_data = array(
						'auth_one'	=> $tokens->access_token,
						'auth_two'	=> $tokens->secret
					);

					$this->social_auth->update_connection($check_connection->connection_id, $connection_data);
				}
				
				// Login
	        	if ($this->social_auth->social_login($check_connection->user_id, 'twitter')) 
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
				// Signup Social Data
				$this->session->set_userdata($twitter_user);
				$this->session->set_userdata('access_token', $tokens->access_token);
		   		$this->session->set_userdata('access_token_secret', $tokens->secret);
				$this->session->set_userdata('signup_name', $twitter_user->name);
				$this->session->set_userdata('signup_user_state', 'has_connection_data');
				$this->session->set_userdata('connection_signup_module', 'twitter');
				$this->session->set_userdata('connection_return_url', base_url().'connections/twitter/signup');
				
				redirect(base_url().'signup_social');				
			}
		}		
	}


	function signup()
	{
    	if ($this->session->userdata('signup_user_state') != 'has_connection_and_email') redirect('signup', 'refresh');

		// User Info		
		$twitter_image	= $this->session->userdata('profile_image_url');	
		$username 		= $this->session->userdata('screen_name');
		$email			= $this->session->userdata('signup_email');
		$process_image  = FALSE;
				
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
				'geo_enabled'	=> $this->session->userdata('geo_enabled'),
				'connection'	=> 'Twitter'
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
						
					// Delete / Make Folder
					$create_path = config_item('users_images_folder').$user_id.'/';
					delete_files($create_path);
					make_folder($create_path);

	        		// Get Twitter Image
					$this->image_model->get_external_image($image_full, $create_path.$image_name);

					// Make Sizes
					$this->image_model->make_thumbnail($create_path, $image_name, 'users', 'medium');
					$this->image_model->make_thumbnail($create_path, $image_name, 'users', 'small');

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

				// Clear Twitter Data				
				$this->session->unset_userdata('access_token');
		   		$this->session->unset_userdata('access_token_secret');
				$this->session->unset_userdata('signup_name');
				$this->session->unset_userdata('signup_user_state');
				$this->session->unset_userdata('connection_signup_module');
				$this->session->unset_userdata('connection_return_url');

				// Login
				if ($this->social_auth->social_login($user_id, 'twitter'))
				{
		        	$this->session->set_flashdata('message', "Login with Twitter was successful");
		        	redirect(base_url().'home');								
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
		// User Is Logged In
		if (!$this->social_auth->logged_in()) redirect('connections/twitter');

        // Create Consumer
        $consumer = $this->oauth->consumer(array(
            'key' 	 	=> config_item('twitter_consumer_key'),
            'secret' 	=> config_item('twitter_consumer_secret'),
            'callback'	=> base_url().'twitter/connections/add'
        ));

        // Load Provider
        $twitter = $this->oauth->provider('twitter');		
	
		// Send to Twitter
        if (!$this->input->get_post('oauth_token'))
        {		
            // Get request token for consumer
            $token = $twitter->request_token($consumer);

            // Store token
            $this->session->set_userdata('oauth_token', base64_encode(serialize($token)));

            // Redirect Twitter
            $twitter->authorize($token, array('oauth_callback' => base_url().'twitter/connections'));
		}
		else
		{
      		// Has Stored Token
            if ($this->session->userdata('oauth_token'))
            {
                // Get the token
                $token = unserialize(base64_decode($this->session->userdata('oauth_token')));
            }

			// Has Token
            if (!empty($token) AND $token->access_token !== $this->input->get_post('oauth_token'))
            {   
                // Delete token, it is not valid
                $this->session->unset_userdata('oauth_token');

                // Send the user back to the beginning
                exit('invalid token after coming back to site');
            }

            // Store Verifier
            $token->verifier($this->input->get_post('oauth_verifier'));

            // Exchange request token for access token
            $tokens = $twitter->access_token($consumer, $token);
		
			// Check Connection
			$check_connection = $this->social_auth->check_connection_auth('twitter', $tokens->access_token, $tokens->secret);

			// Load Tweet Library
			$this->load->library('tweet', array('access_key' => $tokens->access_token, 'access_secret' => $tokens->secret));	            
			
			// Get User Details
			$twitter_user = $this->tweet->call('get', 'account/verify_credentials');

			if (connection_has_auth($check_connection))
			{			
				$this->session->set_flashdata('message', "You've already connected this Twitter account");
				redirect('settings/connections', 'refresh');							
			}
			else
			{
				// Add Connection	
	       		$connection_data = array(
	       			'site_id'				=> $this->module_site->site_id,
	       			'user_id'				=> $this->session->userdata('user_id'),
	       			'module'				=> 'twitter',
	       			'type'					=> 'primary',
	       			'connection_user_id'	=> $twitter_user->id,
	       			'connection_username'	=> $twitter_user->screen_name,
	       			'auth_one'				=> $tokens->access_token,
	       			'auth_two'				=> $tokens->secret
	       		);

	       		// Update / Add Connection	       		
	       		if ($check_connection)
	       		{
	       			$connection = $this->social_auth->update_connection($check_connection->connection_id, $connection_data);
	       		}
	       		else
	       		{
					$connection = $this->social_auth->add_connection($connection_data);
				}

				// Connection Status				
				if ($connection)
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
	}
}