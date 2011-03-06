<?php defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Classes API : Module : Social-Igniter
 *
 */
class Api extends Oauth_Controller
{
    function __construct()
    {
        parent::__construct(); 
    
    	$this->form_validation->set_error_delimiters('', '');

		$this->load->library('twitter');
	}
	
	
	function install_authd_get()
	{
		
	}
	
}