<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Index_model extends CI_Model {
    public function __construct() {
        parent::__construct();
	}
	public function LoadDataTable($company_id) {
		if ($company_id!=''){
			$system_date = date('Y-m-d H:i:s');

			$query = "SELECT * FROM (
				(SELECT w.*,DATE_FORMAT(w.pre_start_date,'%d.%m.%Y') as workshop_start_date,DATE_FORMAT(w.pre_end_date,'%d.%m.%Y') as workshop_end_date,'PRE' as workshop_session
				FROM workshop as w
				WHERE w.hide_on_website=0 AND w.status='1' AND w.company_id='".$company_id."'
				AND ((w.pre_start_date IS NOT NULL) AND (w.pre_start_date != '0000-00-00') AND (w.pre_start_date != '1970-01-01'))
				AND ((w.pre_end_date IS NOT NULL) AND (w.pre_end_date != '0000-00-00') AND (w.pre_end_date != '1970-01-01'))
				AND ((w.pre_start_time IS NOT NULL) AND (w.pre_start_time != ''))
				AND ((w.pre_end_time IS NOT NULL) AND (w.pre_end_time != ''))
				AND('".$system_date."' BETWEEN CONCAT(w.pre_start_date,' ',STR_TO_DATE(w.pre_start_time, '%l:%i %p')) AND 
				CONCAT(w.pre_end_date,' ',STR_TO_DATE(w.pre_end_time, '%l:%i %p')))
				GROUP BY w.id,workshop_session
				ORDER BY w.start_date DESC)
				UNION ALL
				(SELECT w.*,DATE_FORMAT(w.post_start_date,'%d.%m.%Y') as workshop_date,DATE_FORMAT(w.post_end_date,'%d.%m.%Y') as workshop_end_date,'POST' as workshop_session
				FROM workshop as w
				WHERE w.hide_on_website=0 AND w.status='1' AND w.company_id='".$company_id."'
				AND ((w.post_start_date IS NOT NULL) AND (w.post_start_date != '0000-00-00') AND (w.post_start_date != '1970-01-01'))
				AND ((w.post_end_date IS NOT NULL) AND (w.post_end_date != '0000-00-00') AND (w.post_end_date != '1970-01-01'))
				AND ((w.post_start_time IS NOT NULL) AND (w.post_start_time != ''))
				AND ((w.post_end_time IS NOT NULL) AND (w.post_end_time != ''))
				AND('".$system_date."' BETWEEN CONCAT(w.post_start_date,' ',STR_TO_DATE(w.post_start_time, '%l:%i %p')) AND 
				CONCAT(w.post_end_date,' ',STR_TO_DATE(w.post_end_time, '%l:%i %p')))
				GROUP BY w.id,workshop_session
				ORDER BY w.start_date DESC)
				) as wshop 
				ORDER BY wshop.start_date DESC,wshop.id,wshop.workshop_session";
			$result = $this->db->query($query);
			return $result->result();
		}else{
			return null;
		}
	}
	public function fetch_total_questions_played($company_id,$workshop_id,$workshop_session){
		if ($workshop_session=='PRE'){
            // $query = "SELECT count(id) as total FROM atom_results
            // WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."'
            // AND questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";

			$query = "SELECT count(ar.id) as total FROM atom_results as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND  ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."'
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";
        }
        if ($workshop_session=='POST'){
            // $query = "SELECT count(id) as total FROM atom_results
            // WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."'
			// AND questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";
			
			$query = "SELECT count(ar.id) as total FROM atom_results as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."'
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";
		}
        $result = $this->db->query($query);
        return $result->row();
	}
	public function fetch_total_feedback_played($company_id,$workshop_id,$workshop_session){
		if ($workshop_session=='PRE'){
            // $query = "SELECT count(id) as total FROM atom_feedback
            //       WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."'
			//       AND feedbackset_id IN (SELECT feedbackset_id FROM workshop_feedbackset_pre WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";
			
			$query = "SELECT count(ar.id) as total FROM atom_feedback as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."'
		 	AND ar.feedbackset_id IN (SELECT feedbackset_id FROM workshop_feedbackset_pre WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";
        }
        if ($workshop_session=='POST'){
            // $query = "SELECT count(id) as total FROM atom_feedback
            //       WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."'
            //       AND feedbackset_id IN (SELECT feedbackset_id FROM workshop_feedbackset_post WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";

			$query = "SELECT count(ar.id) as total FROM atom_feedback as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."'
			AND ar.feedbackset_id IN (SELECT feedbackset_id FROM workshop_feedbackset_post WHERE (`status`='1' AND active='1') and workshop_id='".$workshop_id."')";
		}
        $result = $this->db->query($query);
        return $result->row();
	}
	public function fetch_total_correct($company_id,$workshop_id,$workshop_session){
        if ($workshop_session=='PRE'){
            // $query = "SELECT count(id) as total FROM atom_results
            // WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."' AND is_correct=1
			// AND questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
			
			$query = "SELECT count(ar.id) as total FROM atom_results as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE  wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."' AND ar.is_correct=1
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
        }
        if ($workshop_session=='POST'){
            // $query = "SELECT count(id) as total FROM atom_results
            // WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."' AND is_correct=1
			// AND questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
			
			$query = "SELECT count(ar.id) as total FROM atom_results as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."' AND ar.is_correct=1
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
		}
        $result = $this->db->query($query);
        return $result->row();
	}
	public function fetch_total_wrong($company_id,$workshop_id,$workshop_session){
        if ($workshop_session=='PRE'){
            // $query = "SELECT count(id) as total FROM atom_results
            // WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."' AND (is_wrong=1 OR is_timeout=1)
			// AND questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
			
			$query = "SELECT count(ar.id) as total FROM atom_results as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."' AND (ar.is_wrong=1 OR ar.is_timeout=1)
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
        }
        if ($workshop_session=='POST'){
            // $query = "SELECT count(id) as total FROM atom_results
            // WHERE  company_id='".$company_id."' AND workshop_id='".$workshop_id."' AND workshop_session='".$workshop_session."' AND (is_wrong=1 OR is_timeout=1)
			// AND questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
			
			$query = "SELECT count(ar.id) as total FROM atom_results as ar
			LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
			WHERE wtu.tester_id IS NULL AND  ar.company_id='".$company_id."' AND ar.workshop_id='".$workshop_id."' AND ar.workshop_session='".$workshop_session."' AND (ar.is_wrong=1 OR ar.is_timeout=1)
			AND ar.questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE `status`='1' and active='1' and workshop_id='".$workshop_id."')";
		}
        $result = $this->db->query($query);
        return $result->row();
	}
}
