<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Topics_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT m.*,b.company_name FROM question_topic m left join company b on b.id=m.company_id $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(m.id) as total  FROM question_topic as m left join company b on b.id=m.company_id $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "select a.*,b.company_name from question_topic a left join company b on a.company_id=b.id where a.deleted=0 and a.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
    public function validate($data){
        $status = "false";
        if(count((array)$data) > 0 ){
            $id = base64_decode($data['id']);
        }
        if ($id==''){
            $query = $this->db->query("select count(*) as found from question_topic where deleted='0' and description='".$data['description']."'");
        }else{
            $query = $this->db->query("select count(*) as found from question_topic where deleted='0' and description='".$data['description']."' and id!='".$id."'");
        }
        foreach ($query->result() as $row)
        {
            if ($row->found>0){
                $status = "false";
            }else{
                $status = "true";
            }
        }
        return $status;
    }
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM question_subtopic WHERE topic_id= $ID"
                . " UNION ALL SELECT id FROM questionset_trainer WHERE topic_id= $ID"
                . " UNION ALL SELECT id FROM questions WHERE topic_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('question_topic');
        return true;
    }
    public function check_topic($topic, $cmp_id='',$topic_id='') {
        
        $querystr="Select description from question_topic where description like " .  $this->db->escape($topic);
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($topic_id!=''){
            $querystr.=" and id!=".$topic_id;
        }
       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
}
