<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trinity_parameter_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = " SELECT p.id,p.description,p.status,p.assessment_type,p.weight_type,pc.name as category_name, "
               . " at.description as assessment_type,c.company_name FROM parameter_mst as p "
               . " left join assessment_type at on at.id=p.assessment_type "
               . " LEFT JOIN parameter_category pc on pc.id=p.category_id LEFT JOIN company c on c.id=p.company_id";
        $query .= " $dtWhere $dtOrder $dtLimit";        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(p.id) as total FROM parameter_mst as p "
                . " left join assessment_type at on at.id=p.assessment_type"
                . " LEFT JOIN parameter_category pc on pc.id=p.category_id left join company c on c.id=p.company_id ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }        
    public function check_parameter_exist($assessment_type,$parameter,$parameter_id) {        
        $querystr="select description from parameter_mst where description='" . $parameter . "'";
        if($assessment_type!=''){
            $querystr.=" and assessment_type=".$assessment_type;
        }
        if($parameter_id!=''){
            $querystr.=" and id!=".$parameter_id;
        }       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
   
}