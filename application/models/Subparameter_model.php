<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Subparameter_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = " SELECT p.*,pm.description as parameter_name FROM subparameter_mst as p ";
		$query .= " LEFT JOIN parameter_mst pm on p.parameter_id = pm.id";        
        $query .= " $dtWhere $dtOrder $dtLimit";        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(p.id) as total FROM subparameter_mst as p ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }        
    public function Check_subparameter_exist($subparameter,$subparameter_id) {        
        $querystr="select description from subparameter_mst where description='" . $subparameter . "'";
		if($subparameter_id!=''){
            $querystr.=" and id!=".$subparameter_id;
        }  
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
   
}
