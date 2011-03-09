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
				
				if ((!$connection->auth_one) && (!$connection->auth_two))
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
			
				// Checks for non default image
				if (($twitter_user->profile_image_url) && (!preg_match('/default_profile_/', $twitter_user->profile_image_url))) $process_image = TRUE;
				
				// If so snatch it up					
				if ($process_image)
				{
			   		$image_full	= str_replace('_normal', '', $twitter_user->profile_image_url); 
					$image_name	= $twitter_user->screen_name.'.'.pathinfo($image_full, PATHINFO_EXTENSION);
			    }
			    else
			    {
			    	$image_name	= "";
			    }
				
				// Converts Timezone
	        	$offset	= $twitter_user->utc_offset / 60 / 60;

				foreach(timezones() as $key => $zone)
				{
					if ($offset === $zone) $time_zone = $key;						
				}
				
				// User Credentials
				$username	= $twitter_user->screen_name;
				$email		= $twitter_user->screen_name.'@twitter.com';
								
				// User Data
	        	$additional_data = array(
    				'name' 		 	=> $twitter_user->name,
					'image'		 	=> $image_name,
					'language'		=> $twitter_user->lang,
					'time_zone'		=> $time_zone,
					'geo_enabled'	=> $twitter_user->geo_enabled
	        	);
	        			       			      				
	        	// Register User
	      		$user_id = $this->social_auth->social_register($username, $email, $additional_data);
	        		        	
	        	if($user_id)
	        	{
					// Add Meta
					$user_meta_data = array(
		        		'location'	 => $twitter_user->location,
						'bio' 		 => $twitter_user->description,
						'url'	 	 => $twitter_user->url
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

	        		$this->session->set_flashdata('message', "User Created");		    			        		
	       		}
	       		else
	       		{
	        		$this->session->set_flashdata('message', "Error Creating User");
	       		}
	       		
	       		$connection_data = array(
	       			'site_id'				=> $this->module_site->site_id,
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
	
			if (($check_connection->auth_one) && ($check_connection->auth_two))
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
	       		
	       		if ($check_connection->connection_user_id)
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