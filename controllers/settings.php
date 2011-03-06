<?php
class Settings extends Dashboard_Controller 
{
    function __construct() 
    {
        parent::__construct();
        
		if ($this->data['logged_user_level_id'] > 1) redirect('home');	
        
		$this->load->library('twitter');

		$this->data['page_title'] = 'Settings';
    }
 
 	function index()
	{
		$this->data['sub_title'] 	= 'Twitter';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		
		$this->render();
	}
	
}