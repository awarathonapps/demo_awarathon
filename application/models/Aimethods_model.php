<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Aimethods_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = " SELECT p.* FROM aimethods_mst as p ";
        $query .= " $dtWhere $dtOrder $dtLimit";        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(p.id) as total FROM aimethods_mst as p ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }        
    public function check_aimethods_exist($aimethods,$aimethod_id) {        
        $querystr="select description from aimethods_mst where description='" . $aimethods . "'";
		if($aimethod_id!=''){
            $querystr.=" and id!=".$aimethod_id;
        }  
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
   
}
