<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:		Social Igniter : Twitter Module : Routes
* Author: 	Brennan Novak
* 		  	contact@social-igniter.com
*
* Project:	http://social-igniter.com/
* Source: 	http://github.com/socialigniter/twitter
*
* Standard installed routes for Twitter Module. 
*/
$route['twitter'] 						= 'twitter';
$route['twitter/home/you']				= 'home/timeline';
$route['twitter/home/mentions']			= 'home/timeline';
$route['twitter/home/favorites']		= 'home/timeline';
