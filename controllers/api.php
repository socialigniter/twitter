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

	function reinstall_authd_get()
	{
		// Load
		$this->load->library('installer');
		$this->load->config('install');        

		// Settings & Create Folders
		$settings = $this->installer->install_settings('twitter', config_item('twitter_settings'), TRUE);

		if ($settings == TRUE)
		{
            $message = array('status' => 'success', 'message' => 'Yay, the Twitter App was reinstalled');
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Dang Twitter App could not be uninstalled');
        }		
		
		$this->response($message, 200);
	}

	function social_post_authd_post()
	{
		if ($connection = $this->social_auth->check_connection_user($this->oauth_user_id, 'twitter', 'primary'))
		{
			// Load Libraries
			$this->load->library('twitter_igniter', $connection);

			// Uses Short URL if exists						
			if ($this->input->post('short_url')):
				$post_url = $this->input->post('short_url');
			else:
				$post_url = $this->input->post('long_url');				
			endif;

			/*	Twitter Status Post Data
				There is lots more that can be added, so look up the official docs here
				https://dev.twitter.com/docs/api/1/post/statuses/update
			*/
			$post_data = array(
				'status'	=> truncator($this->input->post('content'), 130, $post_url, 20),
				'lat'		=> $this->input->post('geo_lat'), 
				'long'		=> $this->input->post('geo_lon'),
				'include_entities'	=> TRUE
			);

			if ($twitter_post = $this->twitter_igniter->post_status_update($post_data))
			{
				// Add to Meta
				$content_meta = array(
					'site_id'		=> $this->module_site->site_id,
					'content_id'	=> $this->input->post('content_id'),
					'meta'			=> 'twitter_status_id',
					'value'			=> $twitter_post->id_str
				);
				
				$this->social_igniter->add_meta($content_meta);
	
				$message = array('status' => 'success', 'message' => 'Posted to Twitter successfully', 'data' => $twitter_post);
			}
			else
			{
				$message = array('status' => 'error', 'message' => 'Could not post message to Twitter', 'data' => $twitter_post);			
			}
		}
		else
		{
			$message = array('status' => 'error', 'message' => 'No Twitter account for that user');			
		}

        $this->response($message, 200);
	}
	
	function archive_tweet_authd_get()
	{
		$connection = $this->social_auth->check_connection_user($this->oauth_user_id, 'twitter', 'primary');

		$this->load->library('twitter_igniter', $connection);

		$tweet = $this->twitter_igniter->get_tweet($this->get('id'));

		echo '<pre>';
		print_r($tweet);
	}
	
}