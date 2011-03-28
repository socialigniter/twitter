<?php

class Twitter extends Site_Controller
{	
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('tweet');
	}
	
	function index()
	{
		redirect(base_url());
	}
}