<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Feedback_type_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT m.*,b.company_name FROM feedback_type as m left join company b on b.id=m.company_id $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(m.id) as total  FROM feedback_type as m left join company b on b.id=m.company_id $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "select ft.*,b.company_name from feedback_type ft left join company b on b.id=ft.company_id where ft.deleted=0 and ft.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
     public function check_type($type, $cmp_id='',$type_id='') {
        
        $querystr="Select description from feedback_type where description LIKE " .$this->db->escape($type);
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($type_id!=''){
            $querystr.=" and id!=".$type_id;
        }
        //echo $querystr;exit;
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM feedback_subtype WHERE feedbacktype_id= $ID"
                . " UNION ALL SELECT id FROM feedbackset_type WHERE feedback_type_id= $ID"
                . " UNION ALL SELECT id FROM feedback_questions WHERE feedback_type_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('feedback_type');
        return true;
    }
}
