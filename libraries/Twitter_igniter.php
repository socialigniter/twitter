<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Twitter Igniter Library
*
* @package		Social Igniter
* @subpackage	Twitter Igniter Library
* @author		Brennan Novak
* @link			http://social-igniter.com/apps/twitter
*
* Basically a wrapper for signed calls to the Twitter API that uses core OAuth 1.0 library
*/

class Twitter_igniter
{
	protected $ci;
	protected $consumer;
	protected $twitter;
	protected $tokens;

	function __construct($config)
	{
		$this->ci =& get_instance();

		// Load Library
		$this->ci->load->library('oauth');
		$this->ci->load->library('curl');

        // Create Consumer
        $this->consumer = $this->ci->oauth->consumer(array(
            'key' 	 	=> config_item('twitter_consumer_key'),
            'secret' 	=> config_item('twitter_consumer_secret')
        ));

        // Load Provider
        $this->twitter = $this->ci->oauth->provider('twitter');

        // Create Tokens
		$this->tokens = OAuth_Token::forge('request', array(
			'access_token' 	=> $config->auth_one,
			'secret' 		=> $config->auth_two
		));

		// Merge Object Tokens & Data
		$this->request_array = array_merge(array(
			'oauth_consumer_key' 	=> $this->consumer->key,
			'oauth_token' 			=> $config->auth_one
		));	
	}

	function get_user_timeline()
	{
		return $this->twitter->get_user_timeline($this->consumer, $this->tokens, $this->request_array);	
	}

	function get_mentions()
	{
		return $this->twitter->get_mentions($this->consumer, $this->tokens, $this->request_array);	
	}

	function get_favorites()
	{
		return $this->twitter->get_favorites($this->consumer, $this->tokens, $this->request_array);	
	}

	function get_tweet($id)
	{
		$tweet = $this->ci->curl->simple_get(prep_url('https://api.twitter.com/1/statuses/show.json?id='.$id.'&include_entities=true'), array(CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1'));

		return json_decode($tweet);	
	}

	function post_status_update($post_data)
	{
		$this->request_array = array_merge($this->request_array, $post_data);

		return $this->twitter->post_status_update($this->consumer, $this->tokens, $this->request_array);	
	}

}