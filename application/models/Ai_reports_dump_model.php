<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_reports_dump_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    } 
	public function get_assessments()
    {
        $query= "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
                FROM assessment_mst am 
                LEFT JOIN assessment_report_type as art on art.id=am.report_type
				WHERE am.status = 1
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
   
    public function get_process_participants($company_id,$assessment_id){
        $query  = "SELECT a.user_id,a.assessment_id,CONCAT(d.firstname, ' ', d.lastname ) AS user_name FROM `assessment_attempts` as a 
        LEFT JOIN device_users as d on d.user_id=a.user_id 
        WHERE a.assessment_id='$assessment_id'";
        $result = $this->db->query($query);
        return $result->result();
    }
   
    public function get_questions_user_details($company_id,$assessment_id,$user_id){
        $query1 = "SELECT id FROM device_users WHERE user_id='$user_id' AND company_id='$company_id'";
        $result1 = $this->common_db->query($query1);
        $data1=$result1->result();
    
        $query2 = "SELECT question_limits FROM assessment_mst WHERE id='$assessment_id'";
       
        $result2 = $this->db->query($query2);
        $data2= $result2->result();
        $question_limits=$data2[0]->question_limits;

        
        $res = array();
        if(isset($data1[0]->id)){
            $u_id=$data1[0]->id;

            $query3 = "SELECT MAX(attempts) as attempts FROM ai_cosine_score WHERE assessment_id = '$assessment_id' AND user_id='$u_id'";
            $result3 = $this->db->query($query3);
            $data3= $result3->result();
            $attempts=$data3[0]->attempts;

            $query  = "SELECT ac.user_id,ac.assessment_id,ac.current_question_id as question_id,ac.cosine_score,ac.audio_totext,aq.question,ac.added_at,ac.next_question_id,em.embeddings
                FROM `ai_cosine_score` as ac 
                LEFT JOIN assessment_question as aq on aq.id=ac.current_question_id 
                LEFT JOIN ai_embeddings as em on em.question_id=ac.current_question_id
                WHERE ac.assessment_id = '$assessment_id' AND ac.user_id='$u_id' AND ac.attempts=$attempts GROUP BY ac.current_question_id ORDER BY ac.id ASC LIMIT $question_limits";
        //   echo $query;
        //   exit;
            $result = $this->db->query($query);
            $res = $result->result();
        }
        return $res;
    }

   
}