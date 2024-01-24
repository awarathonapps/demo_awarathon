<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Information_form_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.form_name,a.status,b.company_name FROM feedback_form_header as a left join company b on b.id=a.company_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(a.id) as total FROM feedback_form_header as a left join company b on b.id=a.company_id ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function LoadInfoDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "select fd.user_id,fd.workshop_id,
                    concat(du.firstname,' ',du.lastname) as username,
                    w.workshop_name from feedback_form_data fd 
                    left join device_users du on du.user_id=fd.user_id
                    left join workshop w on w.id=fd.workshop_id  ";
        $query .= " $dtWhere group by fd.user_id,fd.workshop_id $dtOrder $dtLimit";        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "select fd.user_id from feedback_form_data fd 
                    left join device_users du on du.user_id=fd.user_id
                    left join workshop w on w.id=fd.workshop_id  ";
        $query .= " $dtWhere group by fd.user_id,fd.workshop_id ";  
        $result = $this->db->query($query);
        $data_array = $result->result();
        $data['dtTotalRecords'] = count((array)$data_array);
        return $data;
    }
    public function getInfoFormData($Header_id,$user_id,$workshop_id){
        $query = "SELECT a.field_value,a.submission_dttm,b.field_type "
                . " FROM feedback_form_data as a LEFT JOIN  feedback_form_details as b ON b.header_id=a.form_header_id"
                . " AND b.id=a.form_detail_id where form_header_id= $Header_id AND"
                . " user_id=$user_id AND workshop_id= $workshop_id order by b.field_order asc ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getFormDetails($F_id){
        $query = "SELECT * FROM  feedback_form_details where header_id= ".$F_id." order by field_order " ;        
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getFieldDetails($F_id){
        $query = "SELECT * FROM questionset_trainer where questionset_id= ".$F_id." group by topic_id " ;        
        $result = $this->db->query($query);
        return $result->result();
        
    }
    public function getFeedbackField($hid,$id){
        $query = "SELECT id FROM feedback_form_details where header_id= ".$hid." and id=".$id ;        
        $result = $this->db->query($query);
        return $result->row();
        
    }
    public function removeField($F_id){
        $query = " Delete FROM feedback_form_details where id= ".$F_id ;
               // . " AND header_id=".$id;
                        
        return $this->db->query($query);
        
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('question_set');
        return true;
    }   
     public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM workshop WHERE feedbackform_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function check_form($form, $cmp_id='',$form_id='') {
        
        $querystr="Select form_name from feedback_form_header where form_name='" . $form . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($form_id!=''){
            $querystr.=" and id!=".$form_id;
        }
       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function check_fieldDuplication($form_name, $field_name,$form_id='') {
        
        $querystr="Select a.form_name,b.field_name from feedback_form_header as a left join feedback_form_details as b on a.id=b.header_id where a.form_name='" . $form_name . "'";
        if($field_name!=''){
            $querystr.=" and b.field_name='".$field_name."'";
        }
        if($form_id!=''){
            $querystr.=" and id!=".$form_id;
        }
       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function get_company() {
        $this->db->select('id ,company_name')
                ->from('company')
                ->where('status', '1');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }
    public function getEditSubtopic($Q_id,$topic_id) {
        $query = "SELECT a.id,a.description,ifnull(b.subtopic_id,0) as subtopic_id FROM question_subtopic as a "
                . "LEFT JOIN questionset_trainer as b ON a.id = b.subtopic_id AND b.questionset_id=$Q_id WHERE a.topic_id=".$topic_id;
        $result = $this->db->query($query);
        $output = $result->result();
        return $output;
    }
    public function getQuestionSubTopic($Q_id,$Topic_id,$subTopic){
        $query = "SELECT id FROM questionset_trainer where questionset_id= ".$Q_id." "
                . "AND topic_id=".$Topic_id." AND subtopic_id=".$subTopic;
        $result = $this->db->query($query);
        return $result->row();
        
    }
    public function removeTopic($Q_id,$Topic_id,$subTopic="",$inFlag=false){
        $query = " Delete FROM questionset_trainer where questionset_id= ".$Q_id." "
                . " AND topic_id=".$Topic_id;
        if(!$inFlag && $subTopic!=""){
            $query .= " AND subtopic_id=".$subTopic;
        }
        if($inFlag && $subTopic!=""){
            $query .= " AND id NOT IN(".$subTopic.")";
        }
        return $this->db->query($query);
        
    }
    public function fetch_company_topic($cmp_id) {
        $query = "select a.topic_id,b.description from question_subtopic a left join question_topic b on a.topic_id=b.id where b.company_id=".$cmp_id." group by a.topic_id";
        $result = $this->db->query($query);
        return $result->result();
    }

}
