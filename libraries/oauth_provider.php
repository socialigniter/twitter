<?php

class OAuth_Provider_Twitter extends OAuth_Provider {

	public $name = 'twitter';
	
	public $uid_key = 'user_id';

	public function url_request_token()
	{
		return 'https://api.twitter.com/oauth/request_token';
	}

	public function url_authorize()
	{
		return 'https://api.twitter.com/oauth/authenticate';
	}

	public function url_access_token()
	{
		return 'https://api.twitter.com/oauth/access_token';
	}
	
	public function get_user_info(OAuth_Consumer $consumer, OAuth_Token $token)
	{		
		// Create a new GET request with the required parameters
		$request = OAuth_Request::forge('resource', 'GET', 'http://api.twitter.com/1/users/lookup.json', array(
			'oauth_consumer_key' => $consumer->key,
			'oauth_token' => $token->access_token,
			'user_id' => $token->uid,
		));

		// Sign the request using the consumer and token
		$request->sign($this->signature, $consumer, $token);

		$user = current(json_decode($request->execute()));
		
		// Create a response from the request
		return array(
			'uid' 			=> $token->uid,
			'nickname'		=> $user->screen_name,
			'name'			=> $user->name ?: $user->screen_name,
			'location'		=> $user->location,
			'image'			=> $user->profile_image_url,
			'description'	=> $user->description,
			'urls' => array(
			  'Website' => $user->url,
			  'Twitter' => 'http://twitter.com/'.$user->screen_name,
			),
		);
	}

	public function post_status_update(OAuth_Consumer $consumer, OAuth_Token $token, $post_data)
	{
		// Merge Object Tokens & Data
		$request_array = array_merge(array(
			'oauth_consumer_key' 	=> $consumer->key,
			'oauth_token' 			=> $token->access_token), 
			$post_data
		);

		// Create a new POST request with the required parameters
		$request = OAuth_Request::forge('resource', 'POST', 'https://api.twitter.com/1/statuses/update.json', $request_array);

		// Sign the request using the consumer and token
		$request->sign($this->signature, $consumer, $token);

		$status_update = json_decode($request->execute());
		
		// Create a response from the request
		return $status_update;
	}	
	

} // End Provider_Twitter