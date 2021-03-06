<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:			Social Igniter : Twitter : Install
* Author: 		Brennan Novak
* 		  		contact@social-igniter.com
*         		@brennannovak
*          
* Created: 		Brennan Novak
*
* Project:		http://social-igniter.com/
* Source: 		http://github.com/socialigniter/twitter
*
* Description: 	Install values for Twitter App for Social Igniter 
*/
/* Settings */
$config['twitter_settings']['widgets'] 				= 'TRUE';
$config['twitter_settings']['categories'] 			= 'TRUE';
$config['twitter_settings']['enabled'] 				= 'TRUE';
$config['twitter_settings']['consumer_key'] 		= '';
$config['twitter_settings']['consumer_secret'] 		= '';
$config['twitter_settings']['social_login'] 		= 'TRUE';
$config['twitter_settings']['social_connection'] 	= 'TRUE';
$config['twitter_settings']['social_post'] 			= 'TRUE';
$config['twitter_settings']['archive']				= 'TRUE';
$config['twitter_settings']['login_redirect']		= '';
$config['twitter_settings']['connections_redirect']	= '';

/* Sites */
$config['twitter_sites'][] = array(
	'url'		=> 'http://twitter.com/', 
	'module'	=> 'twitter', 
	'type' 		=> 'remote', 
	'title'		=> 'Twitter', 
	'favicon'	=> 'http://twitter.com/phoenix/favicon.ico'
);