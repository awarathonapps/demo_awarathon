<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_individual_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_workshop($company_id,$trainer_id,$workshop_type_id){
        $system_date = date('Y-m-d H:i:s');
        $whereClause = '';
        $whereClause2 = '';
        if ($workshop_type_id !=0){
            $whereClause = " AND w.workshop_type='".$workshop_type_id."'";
        }
        if ($trainer_id !=0){
            $whereClause2 = " AND trainer_id='".$trainer_id."'";
        }
        $query = "SELECT w.id,w.workshop_name FROM workshop as w
                LEFT JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
                WHERE w.company_id='".$company_id."' ".$whereClause."
                AND w.id IN (SELECT DISTINCT workshop_id from atom_results WHERE company_id='".$company_id."' 
                $whereClause2)";
        $result = $this->db->query($query);        
        return $result->result();
    }
    public function trainer_workshop($company_id,$trainer_id,$workshop_type_id,$workshop_id){
        $system_date = date('Y-m-d H:i:s');
        $whereClause = '';
        if ($workshop_type_id==0){

        }else{
            $whereClause = " AND w.workshop_type='".$workshop_type_id."'";
        }
        if ($workshop_id==0){

        }else{
            $whereClause .= " AND w.id='".$workshop_id."'";
        }
        $query = "SELECT w.*,'1' as workshop_mode, 'PRE' as workshop_session,wt.workshop_type as workshop_type_name,r.region_name 
                FROM workshop as w
                LEFT JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
                LEFT JOIN region as r ON w.company_id = r.company_id AND w.region = r.id
                WHERE w.company_id='".$company_id."' ".$whereClause."
                AND ((w.pre_start_date IS NOT NULL) AND (w.pre_start_date != '0000-00-00') AND (w.pre_start_date != '1970-01-01'))
                AND ((w.pre_end_date IS NOT NULL) AND (w.pre_end_date != '0000-00-00') AND (w.pre_end_date != '1970-01-01'))
                AND ((w.pre_start_time IS NOT NULL) AND (w.pre_start_time != ''))
                AND ((w.pre_end_time IS NOT NULL) AND (w.pre_end_time != ''))
                AND('".$system_date."' BETWEEN CONCAT(w.pre_start_date,' ',STR_TO_DATE(w.pre_start_time, '%l:%i %p')) AND 
                CONCAT(w.pre_end_date,' ',STR_TO_DATE(w.pre_end_time, '%l:%i %p')))
                AND w.id IN (SELECT DISTINCT workshop_id from atom_results WHERE company_id='".$company_id."' 
                AND trainer_id='".$trainer_id."' AND workshop_session='PRE')
                
                UNION ALL
                
                SELECT w.*,'1' as workshop_mode,'POST' as workshop_session,wt.workshop_type as workshop_type_name,r.region_name 
                FROM workshop as w
                LEFT JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
                LEFT JOIN region as r ON w.company_id = r.company_id AND w.region = r.id
                WHERE w.company_id='".$company_id."' ".$whereClause."
                AND ((w.post_start_date IS NOT NULL) AND (w.post_start_date != '0000-00-00') AND (w.post_start_date != '1970-01-01'))
                AND ((w.post_end_date IS NOT NULL) AND (w.post_end_date != '0000-00-00') AND (w.post_end_date != '1970-01-01'))
                AND ((w.post_start_time IS NOT NULL) AND (w.post_start_time != ''))
                AND ((w.post_end_time IS NOT NULL) AND (w.post_end_time != ''))
                AND('".$system_date."' BETWEEN CONCAT(w.post_start_date,' ',STR_TO_DATE(w.post_start_time, '%l:%i %p')) AND 
                CONCAT(w.post_end_date,' ',STR_TO_DATE(w.post_end_time, '%l:%i %p'))) 
                AND w.id IN (SELECT DISTINCT workshop_id from atom_results WHERE company_id='".$company_id."' 
                AND trainer_id='".$trainer_id."' AND workshop_session='POST')

                UNION ALL
                
                SELECT w.*,'2' as workshop_mode, 'PRE' as workshop_session,wt.workshop_type as workshop_type_name,r.region_name 
                FROM workshop as w
                LEFT JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
                LEFT JOIN region as r ON w.company_id = r.company_id AND w.region = r.id
                WHERE w.company_id='".$company_id."' ".$whereClause."
                AND ((w.pre_start_date IS NOT NULL) AND (w.pre_start_date != '0000-00-00') AND (w.pre_start_date != '1970-01-01'))
                AND ((w.pre_end_date IS NOT NULL) AND (w.pre_end_date != '0000-00-00') AND (w.pre_end_date != '1970-01-01'))
                AND ((w.pre_start_time IS NOT NULL) AND (w.pre_start_time != ''))
                AND ((w.pre_end_time IS NOT NULL) AND (w.pre_end_time != ''))
                AND ((CONCAT(w.pre_start_date,' ',STR_TO_DATE(w.pre_start_time, '%l:%i %p'))<='".$system_date."') 
                OR (CONCAT(w.pre_end_date,' ',STR_TO_DATE(w.pre_end_time, '%l:%i %p'))<='".$system_date."'))
                AND ('".$system_date."' NOT BETWEEN CONCAT(w.pre_start_date,' ',STR_TO_DATE(w.pre_start_time, '%l:%i %p')) AND 
                CONCAT(w.pre_end_date,' ',STR_TO_DATE(w.pre_end_time, '%l:%i %p'))) 
                AND w.id IN (SELECT DISTINCT workshop_id from atom_results WHERE company_id='".$company_id."' 
                AND trainer_id='".$trainer_id."' AND workshop_session='PRE')
                
                UNION ALL

                SELECT w.*,'2' as workshop_mode,'POST' as workshop_session,wt.workshop_type as workshop_type_name,r.region_name 
                FROM workshop as w
                LEFT JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
                LEFT JOIN region as r ON w.company_id = r.company_id AND w.region = r.id
                WHERE w.company_id='".$company_id."' ".$whereClause."
                AND ((w.post_start_date IS NOT NULL) AND (w.post_start_date != '0000-00-00') AND (w.post_start_date != '1970-01-01'))
                AND ((w.post_end_date IS NOT NULL) AND (w.post_end_date != '0000-00-00') AND (w.post_end_date != '1970-01-01'))
                AND ((w.post_start_time IS NOT NULL) AND (w.post_start_time != ''))
                AND ((w.post_end_time IS NOT NULL) AND (w.post_end_time != ''))
                AND ((CONCAT(w.post_start_date,' ',STR_TO_DATE(w.post_start_time, '%l:%i %p'))<='".$system_date."') 
                OR (CONCAT(w.post_end_date,' ',STR_TO_DATE(w.post_end_time, '%l:%i %p'))<='".$system_date."'))
                AND ('".$system_date."' NOT BETWEEN CONCAT(w.post_start_date,' ',STR_TO_DATE(w.post_start_time, '%l:%i %p')) AND 
                CONCAT(w.post_end_date,' ',STR_TO_DATE(w.post_end_time, '%l:%i %p'))) 
                AND w.id IN (SELECT DISTINCT workshop_id from atom_results WHERE company_id='".$company_id."' 
                AND trainer_id='".$trainer_id."' AND workshop_session='POST')";
        // $query = "SELECT w.*,1 as workshop_mode,wt.workshop_type as workshop_type_name,r.region_name FROM workshop as w 
        //         INNER JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
        //         INNER JOIN region as r ON w.region= r.id AND w.company_id = r.company_id
        //         WHERE w.company_id='".$company_id."' AND w.id='".$workshop_id."' ".$whereClause." 
        //         AND (('".$system_date."' BETWEEN CONCAT(w.pre_start_date,' ',STR_TO_DATE(w.pre_start_time, '%l:%i %p'))
        //         AND CONCAT(w.pre_end_date,' ',STR_TO_DATE(w.pre_end_time, '%l:%i %p')))
        //         OR ('".$system_date."' BETWEEN CONCAT(w.post_start_date,' ',STR_TO_DATE(w.post_start_time, '%l:%i %p'))
        //         AND CONCAT(w.post_end_date,' ',STR_TO_DATE(w.post_end_time, '%l:%i %p'))))
        //         AND (((w.pre_start_date IS NOT NULL) AND (w.pre_start_date != '0000-00-00') AND (w.pre_start_date != '1970-01-01'))
        //         AND ((w.pre_start_time IS NOT NULL) AND (w.pre_start_time != ''))
        //         OR  (((w.pre_end_date IS NOT NULL) AND (w.pre_end_date != '0000-00-00') AND (w.pre_end_date != '1970-01-01'))
        //         AND ((w.pre_end_time IS NOT NULL) AND (w.pre_end_time != '')))
        //         OR  (((w.post_start_date IS NOT NULL) AND (w.post_start_date != '0000-00-00') AND (w.post_start_date != '1970-01-01'))
        //         AND ((w.post_start_time IS NOT NULL) AND (w.post_start_time != '')))
        //         OR  (((w.post_end_date IS NOT NULL) AND (w.post_end_date != '0000-00-00') AND (w.post_end_date != '1970-01-01'))
        //         AND ((w.post_end_time IS NOT NULL) AND (w.post_end_time != ''))))
        //         UNION ALL
        //         SELECT w.*,2 as workshop_mode,wt.workshop_type as workshop_type_name,r.region_name FROM workshop as w 
        //         INNER JOIN workshoptype_mst as wt ON w.workshop_type = wt.id AND w.company_id = wt.company_id
        //         INNER JOIN region as r ON w.region= r.id AND w.company_id = r.company_id
        //         WHERE w.company_id='".$company_id."' AND w.id='".$workshop_id."' ".$whereClause." 
        //         AND (CONCAT(w.pre_end_date,' ',STR_TO_DATE(w.pre_end_time, '%l:%i %p')) <= '".$system_date."'
        //         OR  CONCAT(w.post_end_date,' ',STR_TO_DATE(w.post_end_time, '%l:%i %p')) <= '".$system_date."')
        //         AND (((w.pre_end_date IS NOT NULL) AND (w.pre_end_date != '0000-00-00') AND (w.pre_end_date != '1970-01-01'))
        //         AND ((w.pre_end_time IS NOT NULL) AND (w.pre_end_time != ''))
        //         OR  (((w.post_end_date IS NOT NULL) AND (w.post_end_date != '0000-00-00') AND (w.post_end_date != '1970-01-01'))
        //         AND ((w.post_end_time IS NOT NULL) AND (w.post_end_time != ''))))";
        $result = $this->db->query($query);        
        return $result->result();
    } 
    public function trainer_atom_result($company_id,$trainer_id,$workshop_id,$workshop_session,$questionset_id,$topic_id,$subtopic_id){
        $query = "SELECT ar.user_id,CONCAT(du.firstname,' ',du.lastname) as fullname,
                ar.trainer_id,CONCAT(cu.first_name,' ',cu.last_name) as trainer_name,
                count(ar.question_id) as question_played,
                sum(ar.is_correct) as is_correct,sum(ar.is_wrong) as is_wrong,sum(ar.is_timeout) as is_timeout FROM atom_results as ar
                INNER JOIN device_users as du ON ar.company_id = du.company_id AND ar.user_id = du.user_id 
                INNER JOIN company_users as cu ON ar.company_id=cu.company_id AND ar.trainer_id=cu.userid
                WHERE ar.company_id='".$company_id."' 
                AND ar.workshop_id='".$workshop_id."' 
                AND ar.workshop_session='".$workshop_session."' 
                AND ar.questionset_id='".$questionset_id."'
                AND ar.trainer_id='".$trainer_id."'
                AND ar.topic_id='".$topic_id."'
                AND ar.subtopic_id='".$subtopic_id."'
                GROUP BY ar.user_id";
        $result = $this->db->query($query);        
        return $result->result();  
    }
    public function fetch_workshop_question_count($company_id,$trainer_id,$workshop_id,$workshop_session){
        if ($workshop_session=="PRE"){
            $query = "SELECT wq.company_id,wq.workshop_id,wq.questionset_id,qs.title as questionset_name, 
                wq.topic_id,qt.description as topic_name,wq.subtopic_id,qst.description as subtopic_name,
                count(wq.question_id) as total_questions
                FROM workshop_questions as wq
                INNER JOIN question_set as qs ON wq.company_id = qs.company_id AND wq.questionset_id = qs.id
                INNER JOIN question_topic as qt ON wq.company_id = qt.company_id AND wq.topic_id = qt.id
                INNER JOIN question_subtopic as qst ON wq.company_id = qst.company_id AND wq.subtopic_id = qst.id
                WHERE wq.company_id='".$company_id."' AND wq.workshop_id = '".$workshop_id."' AND wq.trainer_id='".$trainer_id."' 
                AND wq.questionset_id IN (SELECT questionset_id FROM workshop_questionset_pre WHERE workshop_id = '".$workshop_id."')
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.topic_id,wq.subtopic_id";
        }
        if ($workshop_session=="POST"){
            $query = "SELECT wq.company_id,wq.workshop_id,wq.questionset_id,qs.title as questionset_name, 
                wq.topic_id,qt.description as topic_name,wq.subtopic_id,qst.description as subtopic_name,
                count(wq.question_id) as total_questions
                FROM workshop_questions as wq
                INNER JOIN question_set as qs ON wq.company_id = qs.company_id AND wq.questionset_id = qs.id
                INNER JOIN question_topic as qt ON wq.company_id = qt.company_id AND wq.topic_id = qt.id
                INNER JOIN question_subtopic as qst ON wq.company_id = qst.company_id AND wq.subtopic_id = qst.id
                WHERE wq.company_id='".$company_id."' AND wq.workshop_id = '".$workshop_id."' AND wq.trainer_id='".$trainer_id."' 
                AND wq.questionset_id IN (SELECT questionset_id FROM workshop_questionset_post WHERE workshop_id = '".$workshop_id."')
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.topic_id,wq.subtopic_id";
        }
        $result = $this->db->query($query);
        return $result->result();
    }    
    public function workshop_statistics($report_name,$rpt_token){
        $query = "SELECT user_id,user_name,
                ROUND(sum(pre_average_accuracy),2) as pre_average_accuracy,
                ROUND(sum(post_average_accuracy),2) as post_average_accuracy ,
                (ROUND(sum(post_average_accuracy),2) - ROUND(sum(pre_average_accuracy),2)) as ce
                FROM 
                (SELECT ttr.user_id,ttr.user_name,
                ROUND(IFNULL(((sum(ttr.correct)*100)/sum(ttr.total_question)),0),2) as pre_average_accuracy,
                0 as post_average_accuracy
                FROM temp_trainer_reports as ttr
                INNER JOIN temp_trainer_reports as ttrpo ON ttr.user_id = ttrpo.user_id AND ttrpo.workshop_session = 'POST'
                WHERE ttr.total_question>0 AND ttr.report_name='".$report_name."' AND ttr.rpt_token='".$rpt_token."' 
                AND ttr.workshop_session='PRE'
                GROUP BY ttr.user_id

                UNION ALL

                SELECT ttr.user_id,ttr.user_name,
                0 as pre_average_accuracy,
                ROUND(IFNULL(((sum(ttr.correct)*100)/sum(ttr.total_question)),0),2) as post_average_accuracy 
                FROM temp_trainer_reports as ttr
                INNER JOIN temp_trainer_reports as ttrpr ON ttr.user_id = ttrpr.user_id AND ttrpr.workshop_session = 'PRE'
                WHERE ttr.total_question>0 AND ttr.report_name='".$report_name."' AND ttr.rpt_token='".$rpt_token."' 
                AND ttr.workshop_session='POST'
                GROUP BY ttr.user_id) as ua
                GROUP BY ua.user_id 
                ORDER BY post_average_accuracy DESC, user_name";
//        echo $query;
//        exit;
        $result = $this->db->query($query);        
        return $result->result();    
    }
    public function topic_count($report_name,$rpt_token,$user_id){
        $query           =  "SELECT count(DISTINCT topic_id) as total FROM temp_trainer_reports 
                            WHERE report_name='".$report_name."' AND rpt_token='".$rpt_token."' AND user_id='".$user_id."'";
        $result          = $this->db->query($query);        
        $records         = $result->row();
        $total           = 0;
        if (count($records)>0){
            $total = $records->total;
        }
        return $total;
    }
    public function trainee_count($report_name,$rpt_token,$user_id){
        $query           =  "SELECT count(DISTINCT user_id) as total FROM temp_trainer_reports 
                            WHERE report_name='".$report_name."' AND rpt_token='".$rpt_token."' AND user_id='".$user_id."'";
        $result          = $this->db->query($query);        
        $records         = $result->row();
        $total           = 0;
        if (count($records)>0){
            $total = $records->total;
        }
        return $total;
    }
    public function trainee_workshop_ce($report_name,$rpt_token,$trainee_id){
        $query = "SELECT workshop_id,workshop_name,trainer_name,user_name,
                ROUND(sum(pre_average_accuracy),2) as pre_average_accuracy,
                ROUND(sum(post_average_accuracy),2) as post_average_accuracy ,
                (ROUND(sum(post_average_accuracy),2) - ROUND(sum(pre_average_accuracy),2)) as ce
                FROM 
                (SELECT ttr.workshop_id,ttr.workshop_name,ttr.trainer_name,ttr.user_name,
                ROUND(IFNULL(((sum(ttr.correct)*100)/sum(ttr.total_question)),0),2) as pre_average_accuracy,
                0 as post_average_accuracy
                FROM temp_trainer_reports as ttr
                INNER JOIN temp_trainer_reports as ttrpo ON ttr.user_id = ttrpo.user_id AND ttrpo.workshop_session = 'POST'
                WHERE ttr.total_question>0 AND ttr.report_name='".$report_name."' AND ttr.rpt_token='".$rpt_token."' 
                AND ttr.workshop_session='PRE' AND ttr.user_id='".$trainee_id."'
                GROUP BY ttr.workshop_id

                UNION ALL

                SELECT ttr.workshop_id,ttr.workshop_name,ttr.trainer_name,ttr.user_name,
                0 as pre_average_accuracy,
                ROUND(IFNULL(((sum(ttr.correct)*100)/sum(ttr.total_question)),0),2) as post_average_accuracy 
                FROM temp_trainer_reports as ttr
                INNER JOIN temp_trainer_reports as ttrpr ON ttr.user_id = ttrpr.user_id AND ttrpr.workshop_session = 'PRE'
                WHERE ttr.total_question>0 AND ttr.report_name='".$report_name."' AND ttr.rpt_token='".$rpt_token."' 
                AND ttr.workshop_session='POST' AND ttr.user_id='".$trainee_id."'
                GROUP BY ttr.workshop_id) as ua
                GROUP BY ua.workshop_id 
                ORDER BY workshop_name ASC";
        $result = $this->db->query($query);        
        return $result->result();  
    }
    public function trainer_topic_subtopic_wise_ce($report_name,$rpt_token,$trainee_id,$workshop_id){
        $query = "SELECT user_id,user_name,workshop_id,workshop_name,trainer_id,trainer_name,topic_id,topic_name,subtopic_id,subtopic_name,
                ROUND(sum(pre_average_accuracy),2) as pre_average_accuracy,
                ROUND(sum(post_average_accuracy),2) as post_average_accuracy ,
                (ROUND(sum(post_average_accuracy),2) - ROUND(sum(pre_average_accuracy),2)) as ce
                FROM 
                (SELECT ttr.user_id,ttr.user_name,ttr.workshop_id,ttr.workshop_name,ttr.trainer_id,ttr.trainer_name,ttr.topic_id,
                ttr.topic_name,ttr.subtopic_id,ttr.subtopic_name,
                ROUND(IFNULL(((sum(ttr.correct)*100)/sum(ttr.total_question)),0),2) as pre_average_accuracy,
                0 as post_average_accuracy
                FROM temp_trainer_reports as ttr
                INNER JOIN temp_trainer_reports as ttrpo ON ttr.user_id = ttrpo.user_id AND ttrpo.workshop_session = 'POST'
                WHERE ttr.total_question>0 AND ttr.report_name='".$report_name."' AND ttr.rpt_token='".$rpt_token."' 
                AND ttr.workshop_id='".$workshop_id."' AND ttr.workshop_session='PRE' AND ttr.user_id='".$trainee_id."'
                GROUP BY ttr.topic_id,ttr.subtopic_id

                UNION ALL

                SELECT ttr.user_id,ttr.user_name,ttr.workshop_id,ttr.workshop_name,ttr.trainer_id,ttr.trainer_name,ttr.topic_id,
                ttr.topic_name,ttr.subtopic_id,ttr.subtopic_name,
                0 as pre_average_accuracy,
                ROUND(IFNULL(((sum(ttr.correct)*100)/sum(ttr.total_question)),0),2) as post_average_accuracy 
                FROM temp_trainer_reports as ttr
                INNER JOIN temp_trainer_reports as ttrpr ON ttr.user_id = ttrpr.user_id AND ttrpr.workshop_session = 'PRE'
                WHERE ttr.total_question>0 AND ttr.report_name='".$report_name."' AND ttr.rpt_token='".$rpt_token."' 
                AND ttr.workshop_id='".$workshop_id."' AND ttr.workshop_session='POST' AND ttr.user_id='".$trainee_id."'
                GROUP BY ttr.topic_id,ttr.subtopic_id) as ua
                GROUP BY ua.topic_id,ua.subtopic_id 
                ORDER BY topic_name ASC ,subtopic_name ASC";
        $result = $this->db->query($query);        
        return $result->result();  
    }
    public function wksh_overall_statistics($report_name,$rpt_token,$trainee_id,$workshop_id){
        $query = "SELECT workshop_id,workshop_name,'PRE' as workshop_session, 
                sum(question_played) as question_played,
                sum(correct) as correct
                FROM temp_trainer_reports 
                WHERE total_question>0 AND report_name='".$report_name."' AND rpt_token='".$rpt_token."' 
                AND workshop_id='".$workshop_id."' AND workshop_session='PRE' AND user_id='".$trainee_id."'
                GROUP BY workshop_id
                UNION ALL
                SELECT workshop_id,workshop_name,'POST' as workshop_session,
                sum(question_played) as question_played,
                sum(correct) as correct
                FROM temp_trainer_reports 
                WHERE total_question>0 AND report_name='".$report_name."' AND rpt_token='".$rpt_token."' 
                AND workshop_id='".$workshop_id."' AND workshop_session='POST' AND user_id='".$trainee_id."'
                GROUP BY workshop_id";
        $result = $this->db->query($query);        
        return $result->result();    
    }
}
