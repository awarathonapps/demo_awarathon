<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_wksh_detail_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    } 
    public function workshop_statistics($company_id,$user_id,$workshop_type_id,$workshop_id){
            $query = "select 
                result.workshop_id,
                result.workshop_name,
                result.topic_id,
                result.topic_name,
                result.subtopic_id,
                result.subtopic_name,
                IFNULL(ROUND(AVG(result.pre_average),2),0) as pre_average,
                IFNULL(ROUND(AVG(result.post_average),2),0) as post_average,
                IFNULL(ROUND(AVG(result.post_average) - AVG(result.pre_average),2),0) as ce
                FROM (
                SELECT prpo.user_id,
                prpo.workshop_id,
                w.workshop_name,
                prpo.topic_id,
                qt.description as topic_name,
                prpo.subtopic_id,
                qst.description as subtopic_name,
                sum(prpo.pre_correct) as pre_correct,
                sum(prpo.post_correct) as post_correct, 
                sum(prpo.pre_total_questions) as pre_total_questions,
                sum(prpo.post_total_questions) as post_total_questions,
                FORMAT(((sum(prpo.pre_correct)*100)/sum(prpo.pre_total_questions)),2) as pre_average,
                FORMAT(((sum(prpo.post_correct)*100)/sum(prpo.post_total_questions)),2) as post_average,
                FORMAT((((sum(prpo.post_correct)*100)/sum(prpo.post_total_questions)) - ((sum(prpo.pre_correct)*100)/sum(prpo.pre_total_questions))),2) as ce
                FROM(
                SELECT ar.*, prwq.total_questions as pre_total_questions,0 as post_total_questions FROM 
                (SELECT company_id,user_id,trainer_id,workshop_id,workshop_session,questionset_id,topic_id,subtopic_id,sum(is_correct) as pre_correct,0 as post_correct,
                sum(timer) as total_seconds,sum(seconds) as total_seconds_taken
                FROM atom_results 
                WHERE company_id='".$company_id."' AND 
                workshop_id='".$workshop_id."' AND
                workshop_session = 'PRE' AND
                trainer_id='".$user_id."' 
                GROUP BY company_id,user_id,workshop_id,trainer_id,topic_id,subtopic_id) as ar
                INNER JOIN
                (SELECT wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id,count(wq.question_id) as total_questions 
                FROM workshop_questions as wq
                INNER JOIN workshop_questionset_pre as wpr ON 
                wq.workshop_id = wpr.workshop_id AND wq.questionset_id = wpr.questionset_id
                WHERE wq.company_id='".$company_id."'  AND 
                wq.workshop_id='".$workshop_id."' AND
                wq.trainer_id = '".$user_id."'
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id) as prwq
                ON ar.company_id = prwq.company_id AND ar.workshop_id = prwq.workshop_id 
                AND ar.questionset_id= prwq.questionset_id AND ar.trainer_id = prwq.trainer_id
                AND ar.topic_id = prwq.topic_id
                AND ar.subtopic_id = prwq.subtopic_id
                UNION ALL
                SELECT ar.*,0 as pre_total_questions,powq.total_questions as post_total_questions  FROM 
                (SELECT company_id,user_id,trainer_id,workshop_id,workshop_session,questionset_id,topic_id,subtopic_id,
                0 as pre_correct,sum(is_correct) as post_correct,
                sum(timer) as total_seconds,sum(seconds) as total_seconds_taken
                FROM atom_results 
                WHERE company_id='".$company_id."' AND 
                workshop_id='".$workshop_id."' AND
                workshop_session = 'POST' AND
                trainer_id='".$user_id."' 
                GROUP BY company_id,user_id,trainer_id,workshop_id,topic_id,subtopic_id) as ar
                INNER JOIN
                (SELECT wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id,count(wq.question_id) as total_questions 
                FROM workshop_questions as wq
                INNER JOIN workshop_questionset_post as wpo ON 
                wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id
                WHERE wq.company_id='".$company_id."'  AND 
                wq.workshop_id='".$workshop_id."' AND
                wq.trainer_id = '".$user_id."'
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id) as powq
                ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id 
                AND ar.questionset_id= powq.questionset_id AND ar.trainer_id = powq.trainer_id
                AND ar.topic_id = powq.topic_id
                AND ar.subtopic_id = powq.subtopic_id
                ) as prpo
                INNER JOIN workshop as w ON prpo.workshop_id = w.id AND prpo.company_id = w.company_id AND w.company_id='".$company_id."'
                INNER JOIN question_topic as qt ON prpo.topic_id = qt.id AND prpo.company_id = qt.company_id AND qt.company_id='".$company_id."'
                INNER JOIN question_subtopic as qst ON prpo.subtopic_id = qst.id AND prpo.company_id = qst.company_id AND qst.company_id='".$company_id."'";
                if ($workshop_type_id!=0){
                    $query .= " WHERE w.workshop_type = '".$workshop_type_id."' 
                                GROUP BY prpo.user_id,prpo.workshop_id,prpo.topic_id,prpo.subtopic_id) as result
                                GROUP BY result.workshop_id,result.topic_id,result.subtopic_id";
                }else{
                    $query .= " GROUP BY prpo.user_id,prpo.workshop_id,prpo.topic_id,prpo.subtopic_id) as result
                                GROUP BY result.workshop_id,result.topic_id,result.subtopic_id";
                }   
        $result = $this->db->query($query);        
        return $result->result();    
    }
    public function top_five_workshop($company_id,$user_id,$workshop_type_id,$workshop_id){
            $query = "select 
                    result.user_id,
                    result.trainee_name,
                    IFNULL(ROUND(AVG(result.pre_average),2),0) as pre_average,
                    IFNULL(ROUND(AVG(result.post_average),2),0) as post_average,
                    IFNULL(ROUND(AVG(result.post_average) - AVG(result.pre_average),2),0) as ce
                    FROM (
                    SELECT prpo.user_id,CONCAT(du.firstname,' ',du.lastname) as trainee_name,
                    prpo.workshop_id,
                    w.workshop_name,
                    prpo.topic_id,
                    qt.description as topic_name,
                    prpo.subtopic_id,
                    qst.description as subtopic_name,
                    sum(prpo.pre_correct) as pre_correct,
                    sum(prpo.post_correct) as post_correct, 
                    sum(prpo.pre_total_questions) as pre_total_questions,
                    sum(prpo.post_total_questions) as post_total_questions,
                    FORMAT(((sum(prpo.pre_correct)*100)/sum(prpo.pre_total_questions)),2) as pre_average,
                    FORMAT(((sum(prpo.post_correct)*100)/sum(prpo.post_total_questions)),2) as post_average,
                    FORMAT((((sum(prpo.post_correct)*100)/sum(prpo.post_total_questions)) - ((sum(prpo.pre_correct)*100)/sum(prpo.pre_total_questions))),2) as ce
                    FROM(
                    SELECT ar.*, prwq.total_questions as pre_total_questions,0 as post_total_questions FROM 
                    (SELECT company_id,user_id,trainer_id,workshop_id,workshop_session,questionset_id,topic_id,subtopic_id,sum(is_correct) as pre_correct,0 as post_correct,
                    sum(timer) as total_seconds,sum(seconds) as total_seconds_taken
                    FROM atom_results 
                    WHERE company_id='".$company_id."' AND 
                    workshop_id='".$workshop_id."' AND
                    workshop_session = 'PRE' AND
                    trainer_id='".$user_id."' 
                    GROUP BY company_id,user_id,workshop_id,trainer_id,topic_id,subtopic_id) as ar
                    INNER JOIN
                    (SELECT wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id,count(wq.question_id) as total_questions 
                    FROM workshop_questions as wq
                    INNER JOIN workshop_questionset_pre as wpr ON 
                    wq.workshop_id = wpr.workshop_id AND wq.questionset_id = wpr.questionset_id
                    WHERE wq.company_id='".$company_id."'  AND 
                    wq.workshop_id='".$workshop_id."' AND
                    wq.trainer_id = '".$user_id."'
                    GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id) as prwq
                    ON ar.company_id = prwq.company_id AND ar.workshop_id = prwq.workshop_id 
                    AND ar.questionset_id= prwq.questionset_id AND ar.trainer_id = prwq.trainer_id
                    AND ar.topic_id = prwq.topic_id
                    AND ar.subtopic_id = prwq.subtopic_id
                    UNION ALL
                    SELECT ar.*,0 as pre_total_questions,powq.total_questions as post_total_questions  FROM 
                    (SELECT company_id,user_id,trainer_id,workshop_id,workshop_session,questionset_id,topic_id,subtopic_id,
                    0 as pre_correct,sum(is_correct) as post_correct,
                    sum(timer) as total_seconds,sum(seconds) as total_seconds_taken
                    FROM atom_results 
                    WHERE company_id='".$company_id."' AND 
                    workshop_id='".$workshop_id."' AND
                    workshop_session = 'POST' AND
                    trainer_id='".$user_id."' 
                    GROUP BY company_id,user_id,trainer_id,workshop_id,topic_id,subtopic_id) as ar
                    INNER JOIN
                    (SELECT wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id,count(wq.question_id) as total_questions 
                    FROM workshop_questions as wq
                    INNER JOIN workshop_questionset_post as wpo ON 
                    wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id
                    WHERE wq.company_id='".$company_id."'  AND 
                    wq.workshop_id='".$workshop_id."' AND
                    wq.trainer_id = '".$user_id."'
                    GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id) as powq
                    ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id 
                    AND ar.questionset_id= powq.questionset_id AND ar.trainer_id = powq.trainer_id
                    AND ar.topic_id = powq.topic_id
                    AND ar.subtopic_id = powq.subtopic_id
                    ) as prpo
                    INNER JOIN workshop as w ON prpo.workshop_id = w.id AND prpo.company_id = w.company_id AND w.company_id='".$company_id."'
                    INNER JOIN question_topic as qt ON prpo.topic_id = qt.id AND prpo.company_id = qt.company_id AND qt.company_id='".$company_id."'
                    INNER JOIN question_subtopic as qst ON prpo.subtopic_id = qst.id AND prpo.company_id = qst.company_id AND qst.company_id='".$company_id."'
                    INNER JOIN device_users as du ON prpo.user_id = du.user_id
                    GROUP BY prpo.user_id,prpo.workshop_id,prpo.topic_id,prpo.subtopic_id) as result
                    GROUP BY result.user_id,result.workshop_id
                    ORDER BY ce DESC LIMIT 0,5";
                
        $result = $this->db->query($query);        
        return $result->result();    
    }
    public function bottom_five_workshop($company_id,$user_id,$workshop_type_id,$workshop_id,$top_five_user){
            $query = "select 
                    result.user_id,
                    result.trainee_name,
                    IFNULL(ROUND(AVG(result.pre_average),2),0) as pre_average,
                    IFNULL(ROUND(AVG(result.post_average),2),0) as post_average,
                    IFNULL(ROUND(AVG(result.post_average) - AVG(result.pre_average),2),0) as ce
                    FROM (
                    SELECT prpo.user_id,CONCAT(du.firstname,' ',du.lastname) as trainee_name,
                    prpo.workshop_id,
                    w.workshop_name,
                    prpo.topic_id,
                    qt.description as topic_name,
                    prpo.subtopic_id,
                    qst.description as subtopic_name,
                    sum(prpo.pre_correct) as pre_correct,
                    sum(prpo.post_correct) as post_correct, 
                    sum(prpo.pre_total_questions) as pre_total_questions,
                    sum(prpo.post_total_questions) as post_total_questions,
                    FORMAT(((sum(prpo.pre_correct)*100)/sum(prpo.pre_total_questions)),2) as pre_average,
                    FORMAT(((sum(prpo.post_correct)*100)/sum(prpo.post_total_questions)),2) as post_average,
                    FORMAT((((sum(prpo.post_correct)*100)/sum(prpo.post_total_questions)) - ((sum(prpo.pre_correct)*100)/sum(prpo.pre_total_questions))),2) as ce
                    FROM(
                    SELECT ar.*, prwq.total_questions as pre_total_questions,0 as post_total_questions FROM 
                    (SELECT company_id,user_id,trainer_id,workshop_id,workshop_session,questionset_id,topic_id,subtopic_id,sum(is_correct) as pre_correct,0 as post_correct,
                    sum(timer) as total_seconds,sum(seconds) as total_seconds_taken
                    FROM atom_results 
                    WHERE company_id='".$company_id."' AND 
                    workshop_id='".$workshop_id."' AND
                    workshop_session = 'PRE' AND
                    trainer_id='".$user_id."' 
                    GROUP BY company_id,user_id,workshop_id,trainer_id,topic_id,subtopic_id) as ar
                    INNER JOIN
                    (SELECT wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id,count(wq.question_id) as total_questions 
                    FROM workshop_questions as wq
                    INNER JOIN workshop_questionset_pre as wpr ON 
                    wq.workshop_id = wpr.workshop_id AND wq.questionset_id = wpr.questionset_id
                    WHERE wq.company_id='".$company_id."'  AND 
                    wq.workshop_id='".$workshop_id."' AND
                    wq.trainer_id = '".$user_id."'
                    GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id) as prwq
                    ON ar.company_id = prwq.company_id AND ar.workshop_id = prwq.workshop_id 
                    AND ar.questionset_id= prwq.questionset_id AND ar.trainer_id = prwq.trainer_id
                    AND ar.topic_id = prwq.topic_id
                    AND ar.subtopic_id = prwq.subtopic_id
                    UNION ALL
                    SELECT ar.*,0 as pre_total_questions,powq.total_questions as post_total_questions  FROM 
                    (SELECT company_id,user_id,trainer_id,workshop_id,workshop_session,questionset_id,topic_id,subtopic_id,
                    0 as pre_correct,sum(is_correct) as post_correct,
                    sum(timer) as total_seconds,sum(seconds) as total_seconds_taken
                    FROM atom_results 
                    WHERE company_id='".$company_id."' AND 
                    workshop_id='".$workshop_id."' AND
                    workshop_session = 'POST' AND
                    trainer_id='".$user_id."' 
                    GROUP BY company_id,user_id,trainer_id,workshop_id,topic_id,subtopic_id) as ar
                    INNER JOIN
                    (SELECT wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id,count(wq.question_id) as total_questions 
                    FROM workshop_questions as wq
                    INNER JOIN workshop_questionset_post as wpo ON 
                    wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id
                    WHERE wq.company_id='".$company_id."'  AND 
                    wq.workshop_id='".$workshop_id."' AND
                    wq.trainer_id = '".$user_id."'
                    GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.trainer_id,wq.topic_id,wq.subtopic_id) as powq
                    ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id 
                    AND ar.questionset_id= powq.questionset_id AND ar.trainer_id = powq.trainer_id
                    AND ar.topic_id = powq.topic_id
                    AND ar.subtopic_id = powq.subtopic_id
                    ) as prpo
                    INNER JOIN workshop as w ON prpo.workshop_id = w.id AND prpo.company_id = w.company_id AND w.company_id='".$company_id."'
                    INNER JOIN question_topic as qt ON prpo.topic_id = qt.id AND prpo.company_id = qt.company_id AND qt.company_id='".$company_id."'
                    INNER JOIN question_subtopic as qst ON prpo.subtopic_id = qst.id AND prpo.company_id = qst.company_id AND qst.company_id='".$company_id."'
                    INNER JOIN device_users as du ON prpo.user_id = du.user_id
                    WHERE prpo.user_id NOT IN (".$top_five_user.")
                    GROUP BY prpo.user_id,prpo.workshop_id,prpo.topic_id,prpo.subtopic_id) as result
                    GROUP BY result.user_id,result.workshop_id
                    ORDER BY ce ASC LIMIT 0,5";
                
        $result = $this->db->query($query);        
        return $result->result();    
    }
}
