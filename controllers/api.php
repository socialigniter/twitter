<?php defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Twitter API : Module : Social-Igniter
 *
 */
class Api extends Oauth_Controller
{
	protected $module_site;

    function __construct()
    {
        parent::__construct(); 

		$this->load->library('tweet');
		
		// Get Site Twitter
		$this->module_site = $this->social_igniter->get_site_view_row('module', 'twitter');		
	}
	
	function install_authd_get()
	{
		// Load
		$this->load->library('installer');
		$this->load->config('install');        

		// Settings & Create Folders
		$settings = $this->installer->install_settings('twitter', config_item('twitter_settings'));
	
		// Site
		$site = $this->installer->install_sites(config_item('twitter_sites'));
	
		if ($settings == TRUE AND $site == TRUE)
		{
            $message = array('status' => 'success', 'message' => 'Yay, the Twitter App was installed');
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Dang Twitter App could not be uninstalled');
        }		
		
		$this->response($message, 200);
	}
	
	function new_authd_get()
	{
/*		$connection = $this->social_auth->check_connection_user($this->oauth_user_id, 'twitter', 'primary');

		$this->tweet->set_tokens(array('oauth_token' => $connection->auth_one, 'oauth_token_secret' => $connection->auth_two));
		
		$messages = $this->tweet->call('get', 'direct_messages');

		if ($messages)
		{
			foreach($messages as $msg)
			{			
				$connection_user_id	= $msg->sender->id;

				if ($check_connection = check_connection_user_id($connection_user_id, $module))
				{
					// Data
					$username			= $msg->sender->screen_name;
					$name				= $msg->sender->name;
					$image_name			= $msg->sender->profile_image_url;
					$lang				= $msg->sender->lang;
					$geo_enabled		= $msg->sender->geo_enabled;
			    	$offset				= $msg->sender->utc_offset;
			    	$timezone			= NULL;
					
					foreach(timezones() as $key => $zone)
					{
						if ($offset === $zone) $time_zone = $key;
					}
					
					// User Data
			    	$additional_data = array(
						'name' 		 	=> $name,
						'image'		 	=> $image_name,
						'language'		=> $lang,
						'time_zone'		=> $time_zone,
						'geo_enabled'	=> $geo_enabled
			    	);
			    			       			      				
			    	// Register User
			  		$user_id = $this->social_auth->social_register($username, $email, $additional_data);
	
					// Add Connection					
			   		$connection_data = array(
			   			'site_id'				=> $this->module_site->site_id,
			   			'user_id'				=> $user_id,
			   			'module'				=> 'twitter',
			   			'type'					=> 'primary',
			   			'connection_user_id'	=> $connection_user_id,
			   			'connection_username'	=> $username,
			   			'auth_one'				=> '',
			   			'auth_two'				=> ''
			   		);
			   							
					// Add Connection
					$connection = $this->social_auth->add_connection($connection_data);
				}
				else
				{
					
				}
			}
			
         	$message = array('status' => 'success', 'message' => 'New comments found', 'data' => count($messages));	
		}
		else
		{
         	$message = array('status' => 'error', 'message' => 'No new comments found', 'data' => count($messages));			
		}
*/		
		$message = array('status' => 'success', 'message' => 'New comments found', 'data' => count($messages));
		
        $this->response($message, 200);	        			
	}
	
	function social_post_authd_post()
	{
		if ($connection = $this->social_auth->check_connection_user($this->oauth_user_id, 'twitter', 'primary'))
		{
			$this->tweet->set_tokens(array('oauth_token' => $connection->auth_one, 'oauth_token_secret' => $connection->auth_two));

			$twitter_post = $this->tweet->call('post', 'statuses/update', array('status' => $this->input->post('content')));		

			$message = array('status' => 'success', 'message' => 'Posted to Twitter successfully', 'data' => $twitter_post);
		}
		else
		{
			$message = array('status' => 'error', 'message' => 'No Twitter account for that user');			
		}

        $this->response($message, 200);	
	}
	
	function social_message_authd_post()
	{
		if ($connection = $this->social_auth->check_connection_user($this->oauth_user_id, 'twitter', 'primary'))
		{
			$this->tweet->set_tokens(array('oauth_token' => $connection->auth_one, 'oauth_token_secret' => $connection->auth_two));

			$message_data = array(
				'user_id'		=> $this->input->post('remote_user_id'),
				'text'			=> $this->input->post('message'),
				'wrap_links'	=> TRUE
			);

			$twitter_post = $this->tweet->call('post', 'direct_messages/new', $message_data);		

			$message = array('status' => 'success', 'message' => 'Message sent to Twitter', 'data' => $twitter_post);
		}
		else
		{
			$message = array('status' => 'error', 'message' => 'No Twitter account for that user');			
		}

        $this->response($message, 200);
	}
	
}