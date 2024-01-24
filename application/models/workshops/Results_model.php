<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Results_model extends CI_Model {
    public function __construct() {
        parent::__construct();
	}
	public function LoadQuestionsResult($company_id,$workshop_id,$workshop_session) {
		if ($workshop_session=="PRE"){
			$query = "SELECT *,((correct*100)/sum(total)) as correct_order,FORMAT(((correct*100)/sum(total)),2) as accuracy,(total_seconds/sum(total)) as avg_time  FROM (	
			SELECT ar.user_id,CONCAT(du.firstname,' ',du.lastname) as fullname,
			count(*) as total,sum(ar.is_correct) as correct,
			(sum(ar.is_wrong) + sum(ar.is_timeout)) as wrong,SUM(ar.seconds) as total_seconds
			FROM atom_results as ar
			INNER JOIN device_users as du ON ar.user_id = du.user_id 
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."' 
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre 
			WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')
			GROUP BY ar.user_id) as final GROUP BY user_id ORDER BY correct_order desc,avg_time asc";
		}
		if ($workshop_session=="POST"){
			$query = "SELECT *,((correct*100)/sum(total)) as correct_order,FORMAT(((correct*100)/sum(total)),2) as accuracy,(total_seconds/sum(total)) as avg_time FROM (		 
			SELECT ar.user_id,CONCAT(du.firstname,' ',du.lastname) as fullname,
			count(*) as total,sum(ar.is_correct) as correct,
			(sum(ar.is_wrong) + sum(ar.is_timeout)) as wrong,SUM(ar.seconds) as total_seconds
			FROM atom_results as ar
			INNER JOIN device_users as du ON ar.user_id = du.user_id 
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."' 
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_post 
			WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')
			GROUP BY ar.user_id) as final GROUP BY user_id ORDER BY correct_order desc,avg_time asc";
		}
		
		$result = $this->db->query($query);
		return $result->result();
		
	}	
	public function LoadFeedbackResult($company_id,$workshop_id,$workshop_session,$user_id='') {
		if ($workshop_session=="PRE"){
			$query = "SELECT ar.user_id,CONCAT(du.firstname,' ',du.lastname) as fullname,
					count(*) as total FROM atom_feedback as ar
					INNER JOIN device_users as du ON ar.user_id = du.user_id 
					LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			          WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."'";
                                    if($user_id !=''){
                                         $query .=" AND ar.user_id='".$user_id."'";
                                    }
                        $query .=" AND ar.feedbackset_id IN (SELECT feedbackset_id FROM workshop_feedbackset_pre 
					WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')
					GROUP BY ar.user_id";
		}
		if ($workshop_session=="POST"){
			$query = "SELECT ar.user_id,CONCAT(du.firstname,' ',du.lastname) as fullname,
					count(*) as total FROM atom_feedback as ar
					INNER JOIN device_users as du ON ar.user_id = du.user_id 
					LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			          WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."'";
                         if($user_id !=''){
                                         $query .=" AND ar.user_id='".$user_id."'";
                                    }
                        $query .=" AND ar.feedbackset_id IN (SELECT feedbackset_id FROM workshop_feedbackset_post 
					WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')
					GROUP BY ar.user_id";
		}
		$result = $this->db->query($query);
		return $result->result();
		
	}	
}
