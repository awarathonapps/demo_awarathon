<?php
    if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Emailtemplate_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
/*** Get all Email Alert Name.** @return type */
    public function fetch_all(){
        $this->db->select('alert_name, alert_title')
            ->from('auto_emails')
            ->where('status', 1)
            ->order_by('alert_id', 'asc'); 
        $q = $this->db->get();    
    return $q->result();
    }  
 /*** Get all Email Body.** @return type */
    public function emailbody($alert_name){
        $this->db->select('*')
        ->from('auto_emails')
	->like('alert_name', $alert_name)
	->where('status', 1);
	$q = $this->db->get();    
        return $q->result();
    }
 /*** Update all Email Body.** @return type */ 
    public function update($data, $alert_name){   
	$this->db->where('alert_name', $alert_name);
        $this->db->update('auto_emails', $data);	     		 
        return true;
    }

}