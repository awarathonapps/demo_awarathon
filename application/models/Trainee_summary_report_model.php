<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainee_summary_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }       
    public function get_traineeData($workshop_id,$company_id) {         
            $querystr="Select wru.user_id,concat(du.firstname,' ',du.lastname) as username "
                    . " from workshop_registered_users wru "
                    . " inner join device_users du on du.user_id=wru.user_id where workshop_id=".$workshop_id;
        
        $result = $this->db->query($querystr);        
        return $result->result();
    }
    public function get_preData($company_id='',$workshop_id='',$trainee_id='') {
            $query='';                            
                $query ="SELECT ifnull(format((ar.correct_ans*100/wq.total_question),2),0) as pre,ar.workshop_id,wq.total_question,ar.correct_ans from
                        (select a.company_id,a.workshop_id,sum(a.is_correct) as correct_ans 
                        from atom_results a
                        where a.company_id=".$company_id." and a.user_id=".$trainee_id." and a.workshop_id=".$workshop_id." and a.workshop_session='PRE') ar 
                        inner join 
                        (select c.company_id,c.workshop_id,count(c.question_id) as total_question from workshop_questions as c
                        inner join workshop_questionset_pre as d on c.questionset_id=d.questionset_id and c.workshop_id=d.workshop_id
			where c.workshop_id=".$workshop_id." and c.company_id=".$company_id."
			group by c.workshop_id) wq				
                        on wq.workshop_id=ar.workshop_id and wq.company_id=ar.company_id ";            
            $result = $this->db->query($query);
            return $result->result();
    }
    public function get_postData($company_id='',$workshop_id='',$trainee_id='') {
            $query='';                            
                $query ="SELECT ifnull(format((ar.correct_ans*100/wq.total_question),2),0) as post,ar.workshop_id,wq.total_question,ar.correct_ans from
                        (select a.company_id,a.workshop_id,sum(a.is_correct) as correct_ans 
                        from atom_results a
                        where a.company_id=".$company_id." and a.user_id=".$trainee_id." and a.workshop_id=".$workshop_id." and a.workshop_session='POST') ar 
                        inner join 
                        (select c.company_id,c.workshop_id,count(c.question_id) as total_question from workshop_questions as c
			inner join workshop_questionset_post as d on c.questionset_id=d.questionset_id and c.workshop_id=d.workshop_id
			where c.workshop_id=".$workshop_id." and c.company_id=".$company_id."
			group by c.workshop_id) wq				
                        on wq.workshop_id=ar.workshop_id and wq.company_id=ar.company_id";            
            $result = $this->db->query($query);
            return $result->result();
    }
    public function get_PrePostMainData($company_id='',$workshop_id='',$trainee_id='') {
            $query='';                            
            $query ="select sum(res.pre_accuracy) as pre_accuracy,sum(res.post_accuracy) as post_accuracy,res.topic_id,
                                res.subtopic_id,qt.description as topic,qst.description as subtopic
                                from 
                                (SELECT 'PRE' as sessions,ifnull(format((ar.correct_ans*100/wq.total_question),2),0) as pre_accuracy,0 as post_accuracy,ar.workshop_id,
                                wq.total_question,ar.correct_ans,ar.topic_id,ar.subtopic_id from
                                (select a.company_id,a.workshop_id,sum(a.is_correct) as correct_ans,a.topic_id,a.subtopic_id		 
                                from atom_results a		
                                where a.company_id=6 and a.user_id=68 and a.workshop_id=115 and a.workshop_session='PRE'
                                group by a.topic_id,a.subtopic_id ) ar 

                            inner join 

                                (select c.company_id,c.workshop_id,count(c.question_id) as total_question from workshop_questions as c
                                inner join workshop_questionset_pre as d on c.questionset_id=d.questionset_id and c.workshop_id=d.workshop_id
                                where c.workshop_id=115 and c.company_id=6
                                group by c.workshop_id) wq

                                on wq.workshop_id=ar.workshop_id and wq.company_id=ar.company_id

                    union all

                        SELECT 'POST' as sessions,0 as pre_accuracy,ifnull(format((ar.correct_ans*100/wq.total_question),2),0) as post_accuracy,ar.workshop_id,
                                wq.total_question,ar.correct_ans,ar.topic_id,ar.subtopic_id from
                                (select a.company_id,a.workshop_id,sum(a.is_correct) as correct_ans,a.topic_id,a.subtopic_id
                                from atom_results a			
                                where a.company_id=6 and a.user_id=68 and a.workshop_id=115 and a.workshop_session='POST'
                                group by a.topic_id,a.subtopic_id ) ar 

                            inner join 

                                (select c.company_id,c.workshop_id,count(c.question_id) as total_question from workshop_questions as c
                                inner join workshop_questionset_post as d on c.questionset_id=d.questionset_id and c.workshop_id=d.workshop_id
                                where c.workshop_id=115 and c.company_id=6
                                group by c.workshop_id) wq

                                on wq.workshop_id=ar.workshop_id and wq.company_id=ar.company_id) res

                            inner join question_topic qt on  qt.id=res.topic_id
                            inner join question_subtopic qst on qst.id=res.subtopic_id

                    group by topic_id,subtopic_id";   
   
                
        $result = $this->db->query($query);
        return $result->result();
    }
    
}
