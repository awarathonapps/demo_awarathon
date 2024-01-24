<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_individual_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function isWorkshopLive($workshop_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select if(end_date >='$TodayDt',1,0) as live_workshop FROM workshop where id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        return (count((array)$LiveSet)>0 ? $LiveSet->live_workshop: 1);
    }

    public function workshop_statistics($company_id, $Workshop_id, $trainer_id = "0", $workshop_type_id = "0", $RightsFlag, $WRightsFlag) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $islive_workshop = $this->isWorkshopLive($Workshop_id);
        $query = "SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_total_questions= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_total_questions= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,FORMAT(fs.post_avg-fs.pre_avg,2) as ce
            FROM (
            SELECT ls.trainee_id,sum(pre_total_questions) as pre_total_questions,sum(post_total_questions) as post_total_questions, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg,
            SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) as post_order,SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time
            FROM (
            SELECT es.workshop_id,es.trainee_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,sum(es.post_correct) AS post_correct,
            sum(post_total_questions) AS post_total_questions,sum(es.pre_time_taken) AS total_time
            FROM trainee_result AS es LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=es.workshop_id AND wtu.tester_id=es.trainee_id ";
        $query .= " WHERE wtu.tester_id IS NULL AND es.company_id =$company_id  ";
        if ($Workshop_id != "0") {
            $query .= " AND es.workshop_id= " . $Workshop_id;
        }else{
            if (!$WRightsFlag) {
               $query .= " AND es.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if ($workshop_type_id != "0") {
            $query .= " AND es.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY es.trainee_id UNION ALL
            SELECT arp.workshop_id,arp.user_id AS trainee_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
            WHERE wtu.tester_id IS NULL AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }else{
            if (!$WRightsFlag) {
               $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "  GROUP BY arp.user_id UNION ALL
            SELECT arp.workshop_id,arp.user_id AS trainee_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
            WHERE wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }else{
            if (!$WRightsFlag) {
               $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id) AS ls
            LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
            INNER JOIN workshop AS w ON w.id=ls.workshop_id
         where	du.user_id !=''		";
        $query .= " GROUP BY ls.trainee_id ORDER BY post_order DESC,avg_time,trainee_name) AS fs";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function topic_count($user_id, $Workshop_id, $trainer_id, $workshop_type_id, $RightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT count(DISTINCT topic_id) as total FROM atom_results as arp
                        INNER JOIN workshop AS w ON w.id=arp.workshop_id
                            WHERE arp.user_id=$user_id ";

        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $result = $this->db->query($query);
        $records = $result->row();
        return $records->total;
    }

    public function trainee_workshop_statistics($company_id, $Workshop_id = "0", $trainer_id = "0", $workshop_type_id = "0", $RightsFlag, $WRightsFlag, $trainee_id = "0") {
        $login_id = $this->mw_session['user_id'];
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT fs.workshop_id,fs.workshop_name, IF(fs.pre_total_questions= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_total_questions= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,FORMAT(fs.post_avg-fs.pre_avg,2) as ce
            FROM (
            SELECT ls.workshop_id,sum(ls.pre_total_questions) as pre_total_questions,sum(ls.post_total_questions) as post_total_questions, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg, SUM(ls.post_correct) AS post_correct,
            (SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,w.workshop_name
            FROM (
            SELECT es.workshop_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,sum(es.post_correct) AS post_correct,
            sum(post_total_questions) AS post_total_questions,sum(es.pre_time_taken) AS total_time
            FROM trainee_result AS es
            WHERE es.company_id =$company_id AND es.trainee_id= $trainee_id ";
        if ($Workshop_id != "0") {
            $query .= " AND es.workshop_id= " . $Workshop_id;
        }else{
            if (!$WRightsFlag) {
               $query .= " AND es.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if ($workshop_type_id != "0") {
            $query .= " AND es.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY es.workshop_id UNION ALL
            SELECT arp.workshop_id , SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.user_id= $trainee_id AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }else{
            if (!$WRightsFlag) {
               $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY arp.workshop_id UNION ALL
            SELECT arp.workshop_id ,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.user_id= $trainee_id AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.company_id =$company_id ";
        if ($Workshop_id != "0") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }else{
            if (!$WRightsFlag) {
               $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.workshop_id
            ) AS ls
            LEFT JOIN workshop AS w ON w.id=ls.workshop_id ";
        
        $query .= " GROUP BY ls.workshop_id 
            ORDER BY post_avg DESC,avg_time) AS fs";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainee_count($report_name, $rpt_token, $user_id) {
        $query = "SELECT count(DISTINCT user_id) as total FROM temp_trainer_reports 
                            WHERE report_name='" . $report_name . "' AND rpt_token='" . $rpt_token . "' AND user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        $records = $result->row();
        $total = 0;
        if (count((array)$records) > 0) {
            $total = $records->total;
        }
        return $total;
    }

    public function trainer_topic_subtopic_wise_ce($RightsFlag, $trainer_id = "0", $workshop_id, $trainee_id) {
        $TodayDt = date('Y-m-d H:i');
        $login_id = $this->mw_session['user_id'];
        $query = "
                SELECT qt.description AS topic,qst.description AS subtopic,
                FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_accuracy, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_accuracy,
                FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2) AS ce
                FROM (
                SELECT w.topic_id,w.subtopic_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,sum(w.post_correct) as post_correct,
                sum(w.post_total_questions) as post_total_questions
                FROM trainee_result AS w
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id
                WHERE w.trainee_id =$trainee_id AND wtu.tester_id IS NULL AND w.workshop_id=$workshop_id AND (w.ce_eligible=1 OR
                w.trainee_id IN(SELECT user_id FROM atom_results WHERE workshop_session='POST' AND workshop_id= $workshop_id)) ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND w.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY w.topic_id,w.subtopic_id UNION ALL
                SELECT arp.topic_id,arp.subtopic_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,
                0 AS post_correct, 0 AS post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.user_id =$trainee_id AND wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id and arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'
                AND arp.user_id in(select user_id FROM atom_results where workshop_session='POST'  AND workshop_id= $workshop_id  )
                ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "
                GROUP BY arp.topic_id,arp.subtopic_id UNION ALL
                SELECT arp.topic_id,arp.subtopic_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.user_id =$trainee_id AND wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                AND arp.user_id in(select user_id FROM atom_results where workshop_session='PRE'  AND workshop_id= $workshop_id )
                ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.topic_id,arp.subtopic_id) AS ls
            INNER JOIN question_topic qt ON qt.id=ls.topic_id
            LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
            GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";

        $result = $this->db->query($query);
        return $result->result();
    }

}
