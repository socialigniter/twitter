<?php

class Module_template_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_data($limit=8)
    {
 		$this->db->select('table.status_id, table.user_id');
 		$this->db->from('status');    
 		$this->db->join('users', 'table.user_id = table.user_id'); 				
 		$this->db->order_by('created_at', 'desc'); 
		$this->db->limit($limit);    
 		$result = $this->db->get();	
 		return $result->result();	      
    }
    
    function add_data($user_id, $status_data)
    {
 		$data = array(
			'user_id' 	 			=> $user_id,
			'source'				=> $status_data['source'],
			'text'  	 			=> $status_data['text'],
			'lat'		 			=> $status_data['lat'],
			'long'					=> $status_data['long'],
			'created_at' 			=> unix_to_mysql(now())
		);	
		$insert 	= $this->db->insert('status', $data);
		$status_id 	= $this->db->insert_id();
		return $this->db->get_where('status', array('status_id' => $status_id))->row();	
    }    
    
}