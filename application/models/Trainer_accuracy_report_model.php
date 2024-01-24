<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_accuracy_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }       
    public function get_userData($sessions_id,$workshop_id,$company_id) {  
        if($sessions_id==0){
            $querystr="Select wru.user_id,concat(du.firstname,' ',du.lastname) as username "
                    . " from workshop_registered_users wru "
                    . " inner join device_users du on du.user_id=wru.user_id where workshop_id=".$workshop_id." and workshop_session='PRE'";
        }
        if($sessions_id==1){
            $querystr="Select wru.user_id,concat(du.firstname,' ',du.lastname) as username "
                    . " from workshop_registered_users wru "
                    . " inner join device_users du on du.user_id=wru.user_id where workshop_id=".$workshop_id." and workshop_session='POST'";
        }else{
            $querystr="Select wru.user_id,concat(du.firstname,' ',du.lastname) as username "
                    . " from workshop_registered_users wru "
                    . " inner join device_users du on du.user_id=wru.user_id where workshop_id=".$workshop_id;
        }
        $result = $this->db->query($querystr);        
        return $result->result();
    }
     public function get_chartData($sessions_id='',$workshop_id='',$company_id='',$user_id='') {
                    $tsessions='PRE';
                    if($sessions_id==1){
                        $tsessions='POST';
                    }
                   $query = "SELECT *,TRUNCATE((correct*100/total_question),2) as accuracy FROM 
                        (SELECT
                        (qt.description) as Topic ,(qsubt.description) as SubTopic,wq.id,wq.company_id,wq.topic_id,wq.subtopic_id,count(wq.id) as total_question
                        FROM workshop_questions as wq 
                        INNER JOIN question_topic qt on qt.id=wq.topic_id
                        INNER JOIN question_subtopic qsubt on qsubt.id=wq.subtopic_id

                        WHERE wq.company_id=".$company_id." AND wq.workshop_id=".$workshop_id.") as tq
                  
                        INNER JOIN
                         
                        (SELECT company_id,user_id,workshop_id,workshop_session,sum(is_correct) as correct
                        FROM atom_results 
                        WHERE company_id=".$company_id." AND workshop_session ='".$tsessions."' AND user_id = ".$user_id." 
                        GROUP BY company_id,user_id,workshop_id,workshop_session
                        ORDER BY correct DESC) as correct_ans

                        group by workshop_id";
 
        $result = $this->db->query($query);
        return $result->result();
    } 

}
