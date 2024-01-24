<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Smtp_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }   
    public function find_by_id() {            
            $this->db->select('*')
                ->from('smtp');
                
        $query = $this->db->get();
        $result = $query->row();       
        return $result;
        
        
    }    
}
