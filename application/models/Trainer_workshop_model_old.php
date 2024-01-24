<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_workshop_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function workshop_statistics($RightsFlag, $RightsFlagR="0", $RightsFlagWT="0", $RightsFlagW="0",$company_id, $trainer_id, $Workshop_id="0", $workshop_type_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id  = $this->mw_session['user_id'];
        $query ="";
        if($RightsFlagR !="0"){
        $query .= "
                SELECT DATE_FORMAT(w.start_date,'%d-%m-%Y') AS start_date, w.workshop_name,ls.workshop_id, 
                IFNULL(FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2),'NP') AS ce, 
                COUNT(DISTINCT ar.topic_id) AS total_topic,COUNT(DISTINCT ar.user_id) AS total_trainee
                FROM (
                SELECT w.company_id,w.workshop_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,sum(w.post_correct) as post_correct,
                sum(w.post_total_questions) as post_total_questions
                FROM trainee_result AS w
                WHERE istester=0 AND w.company_id=$company_id and w.pre_played_questions>0 and w.post_played_questions>0";
        if ($Workshop_id == "0" ||$Workshop_id == "") {
             if ($RightsFlagW) {
                $query .= " AND w.workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $trainer_id)";
            }  
        }else{
//            $query .= " AND w.workshop_id= $Workshop_id AND w.workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $trainer_id)";
            $query .= " AND w.workshop_id=" . $Workshop_id;  
        }
        if ($workshop_type_id == "0") {
            if ($RightsFlagWT) {
                $query .= " AND w.workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $trainer_id)";
            }
        }else{
//            $query .= " AND w.workshop_type= $workshop_type_id AND w.workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $trainer_id)";
            $query .= " AND w.workshop_type=" . $workshop_type_id;  
        }
         if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND w.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY w.workshop_id UNION ALL
                SELECT arp.company_id,arp.workshop_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,
                0 AS post_correct, 0 AS post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id
                INNER JOIN device_users AS du ON du.user_id=arp.user_id
                WHERE du.istester=0 AND arp.company_id=$company_id and arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'
                AND arp.user_id in(select user_id FROM atom_results where workshop_session='POST'  AND workshop_id= arp.workshop_id  )
                ";
         if ($Workshop_id == "0") {
             if ($RightsFlagW) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $trainer_id)";
            }  
        }else{
            $query .= " AND arp.workshop_id= $Workshop_id AND arp.workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $trainer_id)";
        }
        if ($workshop_type_id == "0") {
            if ($RightsFlagWT) {
                $query .= " AND w.workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $trainer_id)";
            }
        }else{
            $query .= " AND w.workshop_type= $workshop_type_id AND w.workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $trainer_id)";
        }
         if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "
                GROUP BY arp.workshop_id UNION ALL
                SELECT arp.company_id,arp.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                INNER JOIN device_users AS du ON du.user_id=arp.user_id
                WHERE du.istester=0 AND arp.company_id=$company_id AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                AND arp.user_id in(select user_id FROM atom_results where workshop_session='PRE'  AND workshop_id= arp.workshop_id  )
                ";
        if ($Workshop_id == "0") {
             if ($RightsFlagW) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $trainer_id)";
            }  
        }else{
            $query .= " AND arp.workshop_id= $Workshop_id AND arp.workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $trainer_id)";
        }
        if ($workshop_type_id == "0") {
            if ($RightsFlagWT) {
                $query .= " AND w.workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $trainer_id)";
            }
        }else{
            $query .= " AND w.workshop_type= $workshop_type_id AND w.workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $trainer_id)";
        }
         if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.workshop_id
                ) AS ls
                LEFT JOIN atom_results ar ON  ar.workshop_id=ls.workshop_id and ar.company_id=ls.company_id
                LEFT JOIN workshop AS w ON w.id=ls.workshop_id
                GROUP BY ls.workshop_id";
        }
//          echo $query ;exit;  
        $result = $this->db->query($query);
        return $result->result();
    }

    public function top_five_trainee($RightsFlag,$company_id, $trainer_id, $Workshop_id, $workshop_type_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id  = $this->mw_session['user_id'];
        $query = "SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_avg= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,
             FORMAT(fs.post_avg-fs.pre_avg,2) AS ce,@curRank := @curRank + 1 AS rank
            FROM (
            SELECT ls.trainee_id, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg, SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time
            FROM (
            SELECT es.trainee_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,sum(es.post_correct) AS post_correct,
            sum(post_total_questions) AS post_total_questions,sum(es.pre_time_taken) AS total_time
            FROM trainee_result AS es
            WHERE es.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND es.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND es.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY es.trainee_id UNION ALL
            SELECT arp.user_id AS trainee_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY arp.user_id UNION ALL
            SELECT arp.user_id AS trainee_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id
            ) AS ls
            LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
            WHERE du.istester=0
            GROUP BY ls.trainee_id 
            ORDER BY post_correct DESC,avg_time,trainee_name) AS fs
            ,(
            SELECT @curRank := 0) r limit 0,5";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function bottom_five_trainee($RightsFlag,$company_id, $trainer_id, $Workshop_id, $workshop_type_id, $top_five_trainee_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id  = $this->mw_session['user_id'];
        $query = "select z.* FROM(SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_avg= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,
             FORMAT(fs.post_avg-fs.pre_avg,2) AS ce,@curRank := @curRank + 1 AS rank
            FROM (
            SELECT ls.trainee_id, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg, SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time
            FROM (
            SELECT es.trainee_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,sum(es.post_correct) AS post_correct,
            sum(post_total_questions) AS post_total_questions,sum(es.pre_time_taken) AS total_time
            FROM trainee_result AS es
            WHERE es.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND es.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND es.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY es.trainee_id UNION ALL
            SELECT arp.user_id AS trainee_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY arp.user_id UNION ALL
            SELECT arp.user_id AS trainee_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id
            ) AS ls
            LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
            WHERE du.istester=0
            GROUP BY ls.trainee_id 
            ORDER BY post_correct DESC,avg_time,trainee_name) AS fs
            ,(
            SELECT @curRank := 0) r
                order by rank desc LIMIT 0,5
                ) as z
                 WHERE z.trainee_id NOT IN(" . $top_five_trainee_id . ")";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainer_topic_wise_ce($RightsFlag,$islive_workshop,$trainer_id, $workshop_id) {
        $TodayDt = date('Y-m-d H:i');
        $login_id  = $this->mw_session['user_id'];
        $query = "SELECT IFNULL(SUM(res.pre_accuracy),0) AS pre_accuracy, IFNULL(SUM(res.post_accuracy),0) AS post_accuracy,res.topic_id, qt.description AS topic, IFNULL(FORMAT(SUM(res.pre_accuracy),2),'Not Played') AS pre_average_np, IFNULL(FORMAT(SUM(res.post_accuracy),2),'Not Played') AS post_average_np,
            FORMAT(SUM(res.post_accuracy)- SUM(res.pre_accuracy),2) AS ce
            FROM (
            SELECT 'PRE' AS sessions, FORMAT((ar.correct_ans*100/ IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users))),2) AS pre_accuracy, NULL AS post_accuracy, wq.workshop_id, IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users)) AS total_question, IFNULL(ar.correct_ans,0) AS correct_ans,wq.topic_id
            FROM (
            SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id, COUNT(DISTINCT a.user_id) AS total_users, COUNT(a.question_id) AS played_questions
            FROM atom_results a ";
            if($islive_workshop){
                $query .= "INNER JOIN device_users AS du ON du.user_id=a.user_id WHERE du.istester=0";
            }else{
               $query .= " WHERE a.user_id NOT IN( SELECT distinct trainee_id FROM trainee_result WHERE workshop_id= a.workshop_id AND istester=1 )"; 
            }
            $query .= " AND a.workshop_id=$workshop_id AND a.workshop_session='PRE' AND a.user_id IN(
            SELECT user_id FROM atom_results WHERE workshop_session='POST' AND workshop_id= a.workshop_id AND topic_id =a.topic_id)";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY a.topic_id) ar
            RIGHT JOIN (
            SELECT c.company_id,c.workshop_id,c.topic_id, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$TodayDt', 1, 0) AS liveflag, COUNT(c.question_id) AS total_question
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.workshop_id=$workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (c.trainer_id = $login_id OR c.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND c.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY c.workshop_id,c.topic_id) wq ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND wq.topic_id=ar.topic_id UNION ALL
            SELECT 'POST' AS sessions, NULL AS pre_accuracy, FORMAT((ar.correct_ans*100/ IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users))),2) AS post_accuracy,
             wq.workshop_id, IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users)) AS total_question, IFNULL(ar.correct_ans,0) AS correct_ans,wq.topic_id
            FROM (
            SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id, COUNT(DISTINCT a.user_id) AS total_users, COUNT(a.question_id) AS played_questions
            FROM atom_results a ";
            if($islive_workshop){
                $query .= "INNER JOIN device_users AS du ON du.user_id=a.user_id WHERE du.istester=0";
            }else{
               $query .= " WHERE a.user_id NOT IN( SELECT distinct trainee_id FROM trainee_result WHERE workshop_id= a.workshop_id AND istester=1 )"; 
            }
            $query .= " AND a.workshop_id=$workshop_id AND a.workshop_session='POST' AND a.user_id IN(
            SELECT user_id FROM atom_results WHERE workshop_session='PRE' AND workshop_id= a.workshop_id AND topic_id =a.topic_id) ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY a.topic_id) ar
            RIGHT JOIN (
            SELECT c.company_id,c.workshop_id,c.topic_id, IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$TodayDt', 1, 0) AS liveflag, COUNT(c.question_id) AS total_question
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
                WHERE c.workshop_id=$workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (c.trainer_id = $login_id OR c.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND c.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY c.workshop_id,c.topic_id) wq ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND wq.topic_id=ar.topic_id) res
            INNER JOIN question_topic qt ON qt.id=res.topic_id
            GROUP BY topic_id";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainer_topic_subtopic_wise_ce($RightsFlag,$islive_workshop,$trainer_id, $workshop_id, $trainee_id = "0") {
        $TodayDt = date('Y-m-d H:i');
        $login_id  = $this->mw_session['user_id'];
        $query = "SELECT IFNULL(SUM(res.pre_accuracy),0) AS pre_accuracy, IFNULL(SUM(res.post_accuracy),0) AS post_accuracy,res.topic_id, qt.description AS topic,qst.description AS subtopic, IFNULL(FORMAT(SUM(res.pre_accuracy),2),'Not Played') AS pre_average_np, IFNULL(FORMAT(SUM(res.post_accuracy),2),'Not Played') AS post_average_np, FORMAT(SUM(res.post_accuracy)- SUM(res.pre_accuracy),2) AS ce
            FROM (
            SELECT 'PRE' AS sessions, FORMAT((ar.correct_ans*100/ IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users))),2) AS pre_accuracy,
             NULL AS post_accuracy, wq.workshop_id, IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users)) AS total_question, 
             IFNULL(ar.correct_ans,0) AS correct_ans,
             IF(liveflag=1,ar.topic_id,wq.topic_id) as topic_id,IF(liveflag=1,ar.subtopic_id,wq.subtopic_id) as subtopic_id
            FROM (
            SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id,a.subtopic_id, COUNT(DISTINCT a.user_id) AS total_users, COUNT(a.question_id) AS played_questions
            FROM atom_results a ";
            if($islive_workshop){
                $query .= "INNER JOIN device_users AS du ON du.user_id=a.user_id WHERE du.istester=0";
            }else{
               $query .= " WHERE a.user_id NOT IN( SELECT distinct trainee_id FROM trainee_result WHERE workshop_id= a.workshop_id AND istester=1 )"; 
            }
            $query .= " AND a.workshop_id=$workshop_id AND a.workshop_session='PRE' AND a.user_id IN(
            SELECT distinct user_id FROM atom_results
            WHERE workshop_session='POST' AND workshop_id= a.workshop_id AND topic_id =a.topic_id AND subtopic_id =a.subtopic_id)
            ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        if ($trainee_id != "0") {
            $query .= " AND a.user_id= " . $trainee_id;
        }
        $query .= "
            GROUP BY a.topic_id,a.subtopic_id) ar
            RIGHT JOIN (
            SELECT c.company_id,c.workshop_id,c.topic_id,c.subtopic_id, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$TodayDt', 1, 0) AS liveflag, COUNT(c.question_id) AS total_question
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.workshop_id=$workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (c.trainer_id = $login_id OR c.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND c.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY c.workshop_id,c.topic_id,c.subtopic_id) wq 
            ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND wq.topic_id=ar.topic_id AND wq.subtopic_id=ar.subtopic_id
             UNION ALL
            SELECT 'POST' AS sessions, NULL AS pre_accuracy, FORMAT((ar.correct_ans*100/ IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users))),2) AS post_accuracy, wq.workshop_id, IF(liveflag=1,ar.played_questions,(wq.total_question*ar.total_users)) AS total_question, IFNULL(ar.correct_ans,0) AS correct_ans,wq.topic_id,wq.subtopic_id
            FROM (
            SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id,a.subtopic_id, COUNT(DISTINCT a.user_id) AS total_users, COUNT(a.question_id) AS played_questions
            FROM atom_results a ";
            if($islive_workshop){
                $query .= "INNER JOIN device_users AS du ON du.user_id=a.user_id WHERE du.istester=0";
            }else{
               $query .= " WHERE a.user_id NOT IN( SELECT distinct trainee_id FROM trainee_result WHERE workshop_id= a.workshop_id AND istester=1 )"; 
            }
            $query .= " AND a.workshop_id=$workshop_id AND a.workshop_session='POST' AND a.user_id IN(
            SELECT distinct user_id FROM atom_results
            WHERE workshop_session='PRE' AND workshop_id= a.workshop_id AND topic_id =a.topic_id AND subtopic_id =a.subtopic_id)
            ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        if ($trainee_id != "0") {
            $query .= " AND a.user_id= " . $trainee_id;
        }
        $query .= "
            GROUP BY a.topic_id,a.subtopic_id) ar
            RIGHT JOIN (
            SELECT c.company_id,c.workshop_id,c.topic_id,c.subtopic_id, IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$TodayDt', 1, 0) AS liveflag, COUNT(c.question_id) AS total_question
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.workshop_id=$workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (c.trainer_id = $login_id OR c.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND c.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY c.workshop_id,c.topic_id,c.subtopic_id) wq ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND wq.topic_id=ar.topic_id AND wq.subtopic_id=ar.subtopic_id) res
            INNER JOIN question_topic qt ON qt.id=res.topic_id
            LEFT JOIN question_subtopic qst ON qst.id=res.subtopic_id
            GROUP BY topic_id,subtopic_id";

        $result = $this->db->query($query);

        return $result->result();
    }

    public function wksh_trainer_histogram($RightsFlag,$islive_workshop, $trainer_id, $workshop_id, $workshop_session) {
         $login_id  = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.user_id != '' ,COUNT(tr.user_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        if ($islive_workshop) {
            $query .= "SELECT a.user_id, FORMAT(SUM(a.is_correct)*100/ count(a.question_id),2) average_accuracy FROM atom_results AS a
                    WHERE a.workshop_session = '$workshop_session' AND  a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
                }
            } else {
                $query .= " AND a.trainer_id= " . $trainer_id;
            }
            $query .= " group by a.user_id";
        } else {
            if ($workshop_session == "PRE") {
                $query .= "SELECT a.trainee_id as user_id, FORMAT(SUM(a.pre_correct)*100/ SUM(a.pre_total_questions),2) average_accuracy ";
            } else {
                $query .= "SELECT a.trainee_id as user_id, FORMAT(SUM(a.post_correct)*100/ SUM(a.post_total_questions),2) average_accuracy ";
            }
            $query .= " FROM trainee_result AS a WHERE a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
            $query .= " group by a.trainee_id";
        }
        $query .=") as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function wksh_topic_histogram($RightsFlag,$islive_workshop, $trainer_id, $workshop_id, $workshop_session) {
        $login_id  = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.topic_id != '' ,COUNT(tr.topic_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        if ($islive_workshop) {
            $query .= "SELECT a.topic_id, FORMAT(SUM(a.is_correct)*100/ count(a.question_id),2) average_accuracy FROM atom_results AS a
                WHERE a.workshop_session = '$workshop_session' AND  a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
            $query .= " group by a.topic_id ";
        } else {
            $query .= "select rs.topic_id,FORMAT(rs.correct*100/ (qs.total_qustions*rs.total_users),2) average_accuracy FROM (
                SELECT a.topic_id,SUM(a.is_correct) as correct,count(distinct a.user_id) as total_users
                FROM atom_results AS a
                WHERE a.workshop_session = '$workshop_session' AND a.workshop_id =$workshop_id ";
            if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
            $query .= " GROUP BY a.topic_id
                ) as rs LEFT JOIN (
                select wq.topic_id,COUNT(wq.question_id) as total_qustions FROM workshop_questions AS wq 
                INNER JOIN " . ($workshop_session == "PRE" ? 'workshop_questionset_pre' : 'workshop_questionset_post') . " AS d ON d.questionset_id=wq.questionset_id AND d.workshop_id=wq.workshop_id AND d.active=1
                where wq.workshop_id=$workshop_id ";
            if ($trainer_id != "0") {
                $query .= " AND wq.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY wq.topic_id) as qs ON  qs.topic_id=rs.topic_id";
        }
        $query .=") as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

}
