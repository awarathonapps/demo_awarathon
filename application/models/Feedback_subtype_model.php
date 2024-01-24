<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Feedback_subtype_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT m.*,b.company_name,ft.description as type FROM feedback_subtype as m left join company b on b.id=m.company_id "
                . " left join feedback_type ft on ft.id=m.feedbacktype_id $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(m.id) as total,m.feedbacktype_id  FROM feedback_subtype as m left join company b on b.id=m.company_id "
                . " left join feedback_type ft on ft.id=m.feedbacktype_id $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "select fst.*,b.company_name,c.description as type from feedback_subtype fst left join company b on b.id=fst.company_id "
                . " left join feedback_type c on c.id=fst.feedbacktype_id where fst.deleted=0 and fst.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
     public function check_subtype($subtype, $cmp_id='',$feedbacktype_id='',$subtype_id='') {
        
        $querystr="Select description from feedback_subtype where description like " . $this->db->escape($subtype) ;
        if($cmp_id!=''){
            $querystr .=" and company_id=".$cmp_id;
        }
        if($feedbacktype_id!=''){
            $querystr .=" and feedbacktype_id=".$feedbacktype_id;
        }
        if($subtype_id!=''){
            $querystr .=" and id!=".$subtype_id;
        }
        //echo $querystr;exit;
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM feedback_questions WHERE feedback_subtype_id= $ID"
                . " UNION ALL SELECT id FROM feedbackset_type WHERE feedback_subtype_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('feedback_subtype');
        return true;
    }
}
