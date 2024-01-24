<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Workshop_play_attendence_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "  select wru.user_id,concat(du.firstname,' ',du.lastname) as trainee,du.emp_id,
					wru.registered_date_time as regdate,wru.all_questions_fired,wru.all_feedbacks_fired,
					max(ar.end_dttm) as end_date,
                    DATE_FORMAT(wru.registered_date_time,'%d-%m-%Y %h:%i:%s') as registered_date_time ,                    
					DATE_FORMAT(max(ar.end_dttm),'%d-%m-%Y %h:%i:%s') AS session_preclose_dttm					
                    from workshop_registered_users wru                    
					LEFT JOIN atom_results ar ON ar.user_id = wru.user_id 
					LEFT JOIN device_users du ON wru.user_id=du.user_id AND du.company_id = ar.company_id
					AND ar.workshop_id = wru.workshop_id AND ar.workshop_session = wru.workshop_session	";            
        $query .= " $dtWhere ";
		$query .= " group by du.user_id $dtOrder $dtLimit ";  

			
		
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = " select count(wru.user_id) as total from workshop_registered_users wru                    
					LEFT JOIN atom_results ar ON ar.user_id = wru.user_id
					LEFT JOIN device_users du ON wru.user_id=du.user_id AND du.company_id = ar.company_id
					AND ar.workshop_id = wru.workshop_id AND ar.workshop_session = wru.workshop_session	";
        $query_count .= " $dtWhere group by wru.user_id ";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
		$data['dtTotalRecords'] = count((array)$data_array);
        //$data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }   
    public function get_workshopdatetime($company_id,$workshop_id){
        $query = "  select DATE_FORMAT(pre_start_date,'%d-%m-%Y') as pre_start_date ,pre_start_time,
                    DATE_FORMAT(post_start_date,'%d-%m-%Y')as post_start_date,post_start_time
                    from workshop where company_id=".$company_id." and id=".$workshop_id;
		
        $result = $this->db->query($query);
        return $result->row();
    }
	public function get_feedbackquestion($company_id,$workshop_id){
        $query = " SELECT id AS totalqsn
				FROM workshop_feedback_questions 
				WHERE company_id=".$company_id." and workshop_id=".$workshop_id;
		
        $result = $this->db->query($query);
        return $result->result();
    }
}
