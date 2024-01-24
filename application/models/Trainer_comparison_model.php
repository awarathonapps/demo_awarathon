<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_comparison_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function isWorkshopPlayed($Workshop_id) {
        $PostFlag = false;
        $PreFlag = false;
        $query = "select distinct workshop_id from atom_results where workshop_id=$Workshop_id AND workshop_session='POST' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PostFlag = true;
        }
        $query = "select distinct workshop_id from atom_results where workshop_id=$Workshop_id AND workshop_session='PRE' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PreFlag = true;
        }
        $data['PreFlag'] = $PreFlag;
        $data['PostFlag'] = $PostFlag;
        return $data;
    }

    public function workshop_statistics($company_id, $trainer_id, $Workshop_id, $workshop_type_id="0", $PreFlag, $PostFlag, $islive_workshop) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "
            select fs.*,FORMAT(fs.post_accuracy - fs.pre_accuracy,2) AS ce FROM(
                SELECT DATE_FORMAT(w.start_date,'%d-%m-%Y') AS start_date, w.workshop_name, 
                CONCAT(cu.first_name,' ',cu.last_name) AS trainer_name,ls.workshop_id,ls.company_id,
                FORMAT(((SUM(ls.pre_correct)*100)/ SUM(ls.pre_total_questions)),2) AS pre_accuracy, 
                FORMAT(((SUM(ls.post_correct)*100)/ SUM(ls.post_total_questions)),2) AS post_accuracy
                FROM (
               SELECT w.company_id,w.workshop_id,w.trainer_id,SUM(w.pre_correct) as pre_correct,SUM(w.pre_total_questions) as pre_total_questions,
                SUM(w.post_correct) as post_correct ,SUM(w.post_total_questions) as post_total_questions
                FROM trainee_result AS w 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id";
        $query .= " WHERE w.company_id=$company_id AND wtu.tester_id IS NULL ";
        if ($PostFlag) {
            $query .= " AND (w.ce_eligible=1 OR
            w.trainee_id IN(SELECT user_id FROM atom_results WHERE workshop_session='POST' AND workshop_id= $Workshop_id))";
        }
        if ($Workshop_id != " ") {
            $query .= " AND w.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id != "0") {
            $query .= " AND w.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY w.workshop_id UNION ALL
                SELECT arp.company_id,arp.workshop_id,arp.trainer_id, SUM(arp.is_correct) as pre_correct,COUNT(arp.question_id) as pre_total_questions,
                0 as post_correct ,0 as post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE  arp.company_id=$company_id AND wtu.tester_id IS NULL AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'
                ";
        if ($PostFlag) {
            $query .= " AND arp.user_id in(select user_id FROM atom_results where workshop_session='POST'  AND workshop_id= $Workshop_id  )";
        }
        if ($Workshop_id != " ") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id != "0") {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.workshop_id UNION ALL
                SELECT arp.company_id,arp.workshop_id,arp.trainer_id,0 as pre_correct ,0 as pre_total_questions,
                SUM(arp.is_correct) as post_correct,COUNT(arp.question_id) as post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.company_id=$company_id AND wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                ";
        if ($PreFlag) {
            $query .= "AND arp.user_id in(select user_id FROM atom_results where workshop_session='PRE'  AND workshop_id= $Workshop_id  )";
        }
        if ($Workshop_id != " ") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id != "0") {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.workshop_id
                ) AS ls
                LEFT JOIN workshop AS w ON w.id=ls.workshop_id
                LEFT JOIN company_users cu ON cu.userid=ls.trainer_id
                GROUP BY ls.workshop_id) as fs";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainee_comparison($company_id, $islive_workshop, $trainer_id, $Workshop_id, $workshop_type_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_avg is null ,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg is null,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,
             FORMAT(fs.post_avg-fs.pre_avg,2) AS ce,@curRank := @curRank + 1 AS rank,FORMAT(fs.avg_time,2) as response_time,fs.trainee_region
            FROM (
            SELECT ls.trainee_id, FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_avg, 
            FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_avg, SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,
            tr.region_name as trainee_region
            FROM (
            SELECT es.trainee_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,sum(es.post_correct) AS post_correct,
            sum(post_total_questions) AS post_total_questions,sum(es.pre_time_taken)+sum(es.post_time_taken) AS total_time
            FROM trainee_result AS es 
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=es.workshop_id AND wtu.tester_id=es.trainee_id ";
        $query .= " WHERE es.company_id =$company_id AND wtu.tester_id IS NULL ";
        if ($Workshop_id != " ") {
            $query .= " AND es.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND es.workshop_type= $workshop_type_id";
        }
        if ($trainer_id != "0") {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY es.trainee_id UNION ALL
            SELECT arp.user_id AS trainee_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
            WHERE arp.company_id =$company_id AND wtu.tester_id IS NULL AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' ";
        if ($Workshop_id != " ") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id != "0") {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= "
            GROUP BY arp.user_id UNION ALL
            SELECT arp.user_id AS trainee_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
            WHERE arp.company_id =$company_id AND wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'  ";
        if ($Workshop_id != " ") {
            $query .= " AND arp.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $query .= " AND w.workshop_type= $workshop_type_id";
        }
        if ($trainer_id != "0") {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id
            ) AS ls
            LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
            LEFT JOIN region as tr ON tr.id=du.region_id
            GROUP BY ls.trainee_id 
            ORDER BY post_correct DESC,avg_time,trainee_name) AS fs
            ,(
            SELECT @curRank := 0) r ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

}
