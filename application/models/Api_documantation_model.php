<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api_documantation_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

       
    public function get_cmp_code($table,$where_clause){
       
            foreach ($where_clause as $key=>$value){
                $this->common_db->where($key, $value);
            }
            $query = $this->common_db->get($table);
      
        $data=$query->row();
        return isset($data->portal_name)?$data->portal_name:'';
    }
        //end here 
}