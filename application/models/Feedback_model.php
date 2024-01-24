<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Feedback_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function fetch_feedback($id) {
        $query = "select a.*,b.company_name from feedback a left join company b on a.company_id=b.id where a.id='" . $id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function SelectedFeedbackType($feedback_id){
        $query = "SELECT ft.id,ft.description as text,ifnull(fst.feedback_type_id,0) as fst_id FROM feedback_type as ft LEFT JOIN"
                . " feedbackset_type as fst ON fst.feedback_type_id=ft.id AND  fst.feedback_id=".$feedback_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.*,c.company_name FROM feedback as a left join company c on c.id=a.company_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = "SELECT COUNT(a.id) as total FROM feedback as a left join company c on c.id=a.company_id ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('feedback');
        return true;
    }
    public function find_by_id($id) {
        $query = "SELECT a.*,b.company_name FROM feedback as a LEFT JOIN company as b ON a.company_id = b.id WHERE a.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
     public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM workshop_feedback WHERE feedback_id= $ID";
        $query = $this->db->query($sQuery);
        return (count($query->row()) >0 ? false:true);
    }
    public function check_feedback($feedback, $cmp_id='',$feedback_id='') {
        
        $querystr="Select title from feedback where title='" . $feedback . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($feedback_id!=''){
            $querystr.=" and id!=".$feedback_id;
        }
       
        $query = $this->db->query($querystr);        
        return (count($query->row()) > 0 ? true : false);
    }
//    public function CheckUserAssignRole($roleID) {
//        $sQuery = "SELECT userid FROM workshop WHERE role= $roleID";
//        $query = $this->db->query($sQuery);
//        return (count($query->row()) >0 ? false:true);
//    }

}
