<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_workshop_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function getTrainerWorkshop($dtWhere, $dtOrder, $dtLimit) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select distinct a.workshop_id,w.workshop_name,if(end_date >='$TodayDt',1,0) as live_workshop"
                . " FROM atom_results as a LEFT JOIN workshop as w ON w.id=a.workshop_id ";
        $query .= "  $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet']        = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query  = "SELECT COUNT(distinct workshop_id) as total FROM atom_results as a LEFT JOIN workshop as w ON w.id=a.workshop_id $dtWhere ";
        $result2 = $this->db->query($query);
        $data_array             = $result2->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }

    public function workshop_statistics($RightsFlag, $company_id, $trainer_id, $Workshop_id, $live_workshop) {        
        $login_id = $this->mw_session['user_id'];
        $ObjSet1 = $this->db->query("select a.workshop_id FROM atom_results as a LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND "
            . "wtu.tester_id=a.user_id where a.workshop_id = $Workshop_id AND a.workshop_session LIKE 'pre' AND wtu.tester_id IS NULL  ");
        $preplayed =(count((array)$ObjSet1->row())> 0 ? 1:0);
        $ObjSet2 = $this->db->query("select a.workshop_id FROM atom_results as a LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND "
            . "wtu.tester_id=a.user_id where  a.workshop_id = $Workshop_id AND a.workshop_session LIKE 'POST' AND wtu.tester_id IS NULL  ");
        $postplayed =(count((array)$ObjSet2->row())> 0 ? 1:0);
        
        $query = "SELECT ls.workshop_id ,IFNULL(FORMAT(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2)-FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),2),'NP') AS ce FROM(";
        $query2 = "SELECT count(distinct ls.topic_id) as total_topic,count(distinct ls.trainee_id) as total_trainee FROM(";
        $lcWhere="";
        if ($live_workshop) {
            $ObjSet = $this->db->query("select workshop_id from trainee_result where workshop_id=$Workshop_id");
            $PreCalculated = $ObjSet->row();
            if (count((array)$PreCalculated) > 0) {
                $query2 .= "SELECT w.workshop_id,w.trainee_id,w.topic_id FROM trainee_result AS w "
                        . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id
                WHERE  w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL ";
                $query .= " SELECT w.trainer_id,w.company_id,w.workshop_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,0 as post_correct,
                0 as post_total_questions FROM trainee_result AS w 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id
                WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL ";
                if ($postplayed) {
                    $query .= " AND w.trainee_id in(select user_id FROM atom_results where workshop_session='POST' AND workshop_id= $Workshop_id)";
                }
            } else {
                $query2 .= "SELECT w.workshop_id,w.user_id as trainee_id,w.topic_id FROM atom_results AS w 
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.user_id
                    WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND  wtu.tester_id IS NULL  and w.workshop_session='PRE'";
                $query .= " SELECT w.trainer_id,w.company_id,w.workshop_id, SUM(w.is_correct) AS pre_correct, COUNT(w.question_id) AS pre_total_questions,
                    0 AS post_correct, 0 AS post_total_questions FROM atom_results AS w 
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.user_id
                    WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL and w.workshop_session='PRE'";
                if ($postplayed) {
                    $query .= " AND w.user_id in(select user_id FROM atom_results where workshop_session='POST' AND workshop_id= $Workshop_id)";
                }
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $lcWhere = " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $lcWhere = " AND w.trainer_id= " . $trainer_id;
            }
            $query .= $lcWhere;
            if (count((array)$PreCalculated) > 0) {
                $query2 .= $lcWhere . " GROUP BY w.trainee_id,w.topic_id ";
            }else{
                $query2 .= $lcWhere . " GROUP BY w.user_id,w.topic_id ";
            }
            if ($postplayed) {
                $query2 .= " UNION ALL SELECT w.workshop_id,w.user_id as trainee_id,w.topic_id FROM atom_results AS w 
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.user_id
                    WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL AND  w.workshop_session='POST'";
                $query .= " UNION ALL
                SELECT w.trainer_id,w.company_id,w.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(w.is_correct) AS post_correct, COUNT(w.question_id) AS post_total_questions
                FROM atom_results AS w LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.user_id
                    WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL AND w.workshop_session='POST'";
                if ($trainer_id == "0") {
                    if (!$RightsFlag) {
                        $lcWhere .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                    }
                } else {
                    $lcWhere .= " AND w.trainer_id= " . $trainer_id;
                }
                if ($preplayed) {
                    $query .= "AND w.user_id in(select user_id FROM atom_results where workshop_session='PRE'  AND workshop_id= $Workshop_id  )";
                }
                $query2 .= " GROUP BY w.user_id,w.topic_id";
                $query .= $lcWhere ;
            }
        } else {
            $query2 .= " SELECT w.workshop_id,w.trainee_id,w.topic_id FROM trainee_result AS w "
                    . "LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id "
                . " WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL  ";
            $query .="SELECT w.trainer_id,w.company_id,w.workshop_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,sum(w.post_correct) as post_correct,
                sum(w.post_total_questions) as post_total_questions
                FROM trainee_result AS w LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id
                WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND  wtu.tester_id IS NULL AND  w.ce_eligible=1";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $lcWhere = " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $lcWhere = " AND w.trainer_id= " . $trainer_id;
            }
            $query .= $lcWhere;
            $query2 .= $lcWhere . " GROUP BY w.trainee_id,w.topic_id";
        }
        $query .= ") as ls  ";
        $query2 .= ") as ls ";
       //echo $query2.'<br/>';
   //exit;
        $result = $this->db->query($query);
        $data['CEData'] =$result->row();
        $result2 = $this->db->query($query2);
        $data['total'] =$result2->row();
        return $data;
    }
    
    public function top_five_trainee($dtWhere,$Workshop_id='0') {
        $TodayDt = date('Y-m-d H:i:s');
        $PostFlag=true;
        if ($Workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('atom_results', 'id', "workshop_session='POST' AND workshop_id=" . $Workshop_id);
            if(count((array)$WorshopSet)==0){
                $PostFlag=false;        
            }
        }
        $query = "SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_played= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_played= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,
             FORMAT(fs.post_avg-fs.pre_avg,2) AS ce,@curRank := @curRank + 1 AS rank
            FROM (
            SELECT ls.trainee_id,sum(ls.pre_total_questions) as pre_played,sum(ls.post_total_questions) as post_played, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg, SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,";
            if($PostFlag){
                $query .= "(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions)) as post_order";
            }else{
                $query .= "(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)) as post_order";
            }
            $query .= "
            FROM (
            select ed.trainee_id,ed.pre_correct,ed.pre_total_questions,ed.post_correct,ed.post_total_questions,ed.total_time FROM(
            SELECT a.trainee_id,SUM(a.pre_correct) AS pre_correct, SUM(a.pre_total_questions) AS pre_total_questions, SUM(a.post_correct) AS post_correct, 
            SUM(a.post_total_questions) AS post_total_questions, SUM(a.pre_time_taken)+ SUM(a.post_time_taken) AS total_time
            FROM trainee_result AS a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
            $dtWhere AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY a.trainee_id) as ed
             UNION ALL
            SELECT a.user_id AS trainee_id, SUM(a.is_correct) AS pre_correct, COUNT(a.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(a.seconds) AS total_time
            FROM atom_results AS a
            INNER JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            $dtWhere AND a.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY a.user_id UNION ALL
            SELECT a.user_id AS trainee_id,0 AS pre_correct,0 AS pre_total_questions, SUM(a.is_correct) AS post_correct, COUNT(a.question_id) AS post_total_questions, SUM(a.seconds) AS total_time
            FROM atom_results AS a
            INNER JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
             $dtWhere AND a.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY a.user_id
            ) AS ls
            LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
			where du.user_id !=''
            GROUP BY ls.trainee_id 
            ORDER BY post_order DESC,avg_time,trainee_name) AS fs
            ,(
            SELECT @curRank := 0) r limit 0,5";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function bottom_five_trainee($dtWhere,$top_five_trainee_id,$Workshop_id='0') {
        $TodayDt = date('Y-m-d H:i:s');
        $PostFlag=true;
        if ($Workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('atom_results', 'id', "workshop_session='POST' AND workshop_id=" . $Workshop_id);
            if(count((array)$WorshopSet)==0){
                $PostFlag=false;        
            }
        }
        $query = "select z.* FROM(SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_avg= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,
             FORMAT(fs.post_avg-fs.pre_avg,2) AS ce,@curRank := @curRank + 1 AS rank
            FROM (
            SELECT ls.trainee_id, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg, SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,
              ";
             if($PostFlag){
                $query .= "(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions)) as post_order";
            }else{
                $query .= "(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)) as post_order";
            }
            $query .= " FROM (
            select ed.trainee_id,ed.pre_correct,ed.pre_total_questions,ed.post_correct,ed.post_total_questions,ed.total_time FROM(
            SELECT a.trainee_id,a.istester, SUM(a.pre_correct) AS pre_correct, SUM(a.pre_total_questions) AS pre_total_questions, SUM(a.post_correct) AS post_correct, 
            SUM(a.post_total_questions) AS post_total_questions, SUM(a.pre_time_taken)+ SUM(a.post_time_taken) AS total_time
            FROM trainee_result AS a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
            $dtWhere AND wtu.tester_id IS NULL ";

        $query .= " GROUP BY a.trainee_id) as ed
            UNION ALL
            SELECT a.user_id AS trainee_id, SUM(a.is_correct) AS pre_correct, COUNT(a.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions, SUM(a.seconds) AS total_time
            FROM atom_results AS a
            INNER JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            $dtWhere AND a.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL ";
        $query .= "
            GROUP BY a.user_id UNION ALL
            SELECT a.user_id AS trainee_id,0 AS pre_correct,0 AS pre_total_questions, SUM(a.is_correct) AS post_correct, COUNT(a.question_id) AS post_total_questions, SUM(a.seconds) AS total_time
            FROM atom_results AS a
            INNER JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            $dtWhere AND a.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY a.user_id
            ) AS ls
            INNER JOIN device_users AS du ON du.user_id=ls.trainee_id
			where du.user_id !=''
            GROUP BY ls.trainee_id 
            ORDER BY post_order desc,avg_time ,trainee_name) AS fs
            ,(
            SELECT @curRank := 0) r
                order by rank desc 
                ) as z
                 WHERE z.trainee_id NOT IN(" . $top_five_trainee_id . ") LIMIT 0,5";
//                echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainer_topic_wise_ce($dtWhere, $workshop_id) {
        $TodayDt = date('Y-m-d H:i');
        $query = " SELECT qt.description AS topic, 
                FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2) AS ce
                FROM (
                SELECT a.topic_id,sum(a.pre_correct) as pre_correct ,sum(a.pre_total_questions) as pre_total_questions ,sum(a.post_correct) as post_correct,
                sum(a.post_total_questions) as post_total_questions
                FROM trainee_result AS a 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id";
                $query .= "  $dtWhere AND wtu.tester_id IS NULL "
                . " AND ((a.post_played_questions >0 and a.pre_played_questions >0)  OR
                a.trainee_id IN(SELECT distinct user_id FROM atom_results WHERE workshop_session='POST' AND workshop_id= $workshop_id and topic_id= a.topic_id )) ";
        $query .= " GROUP BY a.topic_id  UNION ALL
                SELECT a.topic_id,0 AS pre_correct,0 AS pre_total_questions, SUM(a.is_correct) AS post_correct, COUNT(a.question_id) AS post_total_questions
                FROM atom_results AS a
                INNER JOIN workshop AS w ON w.id=a.workshop_id 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
                INNER JOIN trainee_result as tr ON tr.company_id=a.company_id AND tr.trainer_id=a.trainer_id 
                AND tr.topic_id=a.topic_id AND tr.workshop_id=a.workshop_id AND tr.trainee_id=a.user_id
                AND tr.pre_played_questions >0
                $dtWhere AND a.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                AND a.user_id in(select distinct user_id FROM atom_results where workshop_session='PRE'  AND workshop_id= $workshop_id  )
                AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY a.topic_id) AS ls
            INNER JOIN question_topic qt ON qt.id=ls.topic_id
                GROUP BY ls.topic_id order by ce desc ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainer_topic_subtopic_wise_ce($RightsFlag,$islive_workshop, $trainer_id, $workshop_id, $trainee_id = "0") {
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
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id ";
                $query .= " WHERE wtu.tester_id IS NULL AND w.workshop_id=$workshop_id AND ((w.post_played_questions >0 and w.pre_played_questions >0)  OR
                w.trainee_id IN(SELECT distinct user_id FROM atom_results WHERE workshop_session='POST' AND workshop_id= $workshop_id and topic_id= w.topic_id )) ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND w.trainer_id= " . $trainer_id;
        }
        if ($trainee_id != "0") {
            $query .= " AND w.trainee_id= " . $trainee_id;
        }
        $query .= " GROUP BY w.topic_id,w.subtopic_id  UNION ALL
                SELECT arp.topic_id,arp.subtopic_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                INNER JOIN trainee_result as tr ON tr.company_id=arp.company_id AND tr.trainer_id=arp.trainer_id 
                AND tr.topic_id=arp.topic_id AND tr.workshop_id=arp.workshop_id AND tr.trainee_id=arp.user_id
                AND tr.pre_played_questions >0
                WHERE wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'";
        if ($trainee_id != "0") {
            $query .= " AND arp.user_id= " . $trainee_id;
        }
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

    public function wksh_trainer_histogram($RightsFlag, $islive_workshop, $trainer_id, $workshop_id, $workshop_session) {
        $login_id = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.user_id != '' ,COUNT(tr.user_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        if ($islive_workshop) {
            $query .= "SELECT a.user_id, FORMAT(SUM(a.is_correct)*100/ count(a.question_id),2) average_accuracy FROM atom_results AS a
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
                WHERE wtu.tester_id IS NULL AND a.workshop_session = '$workshop_session' AND  a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND a.trainer_id= " . $trainer_id;
            }
            $query .= " group by a.user_id";
        } else {
            if ($workshop_session == "PRE") {
                $query .= "SELECT a.trainee_id as user_id, FORMAT(SUM(a.pre_correct)*100/ SUM(a.pre_total_questions),0) average_accuracy ";
            } else {
                $query .= "SELECT a.trainee_id as user_id, FORMAT(SUM(a.post_correct)*100/ SUM(a.post_total_questions),0) average_accuracy ";
            }
            $query .= " FROM trainee_result AS a "
                    . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id "
                    . " WHERE wtu.tester_id IS NULL AND a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND a.trainer_id= " . $trainer_id;
            }
            $query .= " group by a.trainee_id";
        }
        $query .=") as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function wksh_topic_histogram($RightsFlag, $islive_workshop, $trainer_id, $workshop_id, $workshop_session) {
        $login_id = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.topic_id != '' ,COUNT(tr.topic_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        if ($islive_workshop) {
            $query .= "SELECT a.topic_id, FORMAT(SUM(a.is_correct)*100/ count(a.question_id),0) average_accuracy FROM atom_results AS a
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            WHERE wtu.tester_id IS NULL AND a.workshop_session = '$workshop_session' AND  a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND a.trainer_id= " . $trainer_id;
            }
        } else {
            if ($workshop_session == "PRE") {
                $query .= "SELECT a.topic_id, FORMAT(SUM(a.pre_correct)*100/sum(a.pre_total_questions),0) as average_accuracy";
            } else {
                $query .= "SELECT a.topic_id, FORMAT(SUM(a.post_correct)*100/sum(a.post_total_questions),0) as average_accuracy";
            }
            $query .= " FROM trainee_result AS a "
                . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id "
                . " WHERE wtu.tester_id IS NULL AND a.workshop_id =$workshop_id";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND a.trainer_id= " . $trainer_id;
            }
        }
        $query .= " GROUP BY a.topic_id)as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
        $result = $this->db->query($query);
        return $result->result();
    }

}
