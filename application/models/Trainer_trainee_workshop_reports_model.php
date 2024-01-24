<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_trainee_workshop_reports_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getTrainerWorkshop($dtWhere, $dtOrder, $dtLimit)
    {
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

    public function workshop_statistics($RightsFlag, $company_id, $trainer_id, $Workshop_id, $live_workshop)
    {
        $login_id = $this->mw_session['user_id'];
        $ObjSet1 = $this->db->query("select a.workshop_id FROM atom_results as a LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND "
            . "wtu.tester_id=a.user_id where a.workshop_id = $Workshop_id AND a.workshop_session LIKE 'pre' AND wtu.tester_id IS NULL  ");
        $preplayed = (count((array)$ObjSet1->row()) > 0 ? 1 : 0);
        $ObjSet2 = $this->db->query("select a.workshop_id FROM atom_results as a LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND "
            . "wtu.tester_id=a.user_id where  a.workshop_id = $Workshop_id AND a.workshop_session LIKE 'POST' AND wtu.tester_id IS NULL  ");
        $postplayed = (count((array)$ObjSet2->row()) > 0 ? 1 : 0);

        $query = "SELECT ls.workshop_id ,IFNULL(FORMAT(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2)-FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),2),'NP') AS ce FROM(";
        $query2 = "SELECT count(distinct ls.topic_id) as total_topic,count(distinct ls.trainee_id) as total_trainee FROM(";
        $lcWhere = "";
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
            } else {
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
                $query .= $lcWhere;
            }
        } else {
            $query2 .= " SELECT w.workshop_id,w.trainee_id,w.topic_id FROM trainee_result AS w "
                . "LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id "
                . " WHERE w.company_id=$company_id AND w.workshop_id=$Workshop_id AND wtu.tester_id IS NULL  ";
            $query .= "SELECT w.trainer_id,w.company_id,w.workshop_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,sum(w.post_correct) as post_correct,
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
        $data['CEData'] = $result->row();
        $result2 = $this->db->query($query2);
        $data['total'] = $result2->row();
        return $data;
    }

    public function top_five_trainee($dtWhere, $Workshop_id = '0')
    {
        $TodayDt = date('Y-m-d H:i:s');
        $PostFlag = true;
        if ($Workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('atom_results', 'id', "workshop_session='POST' AND workshop_id=" . $Workshop_id);
            if (count((array)$WorshopSet) == 0) {
                $PostFlag = false;
            }
        }
        $query = "SELECT fs.trainee_id,fs.trainee_name, IF(fs.pre_played= 0,'NP', CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_played= 0,'NP', CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,
             FORMAT(fs.post_avg-fs.pre_avg,2) AS ce,@curRank := @curRank + 1 AS rank
            FROM (
            SELECT ls.trainee_id,sum(ls.pre_total_questions) as pre_played,sum(ls.post_total_questions) as post_played, ifnull(FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS pre_avg, 
            ifnull(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),0) AS post_avg, SUM(ls.post_correct) AS post_correct,
             CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,";
        if ($PostFlag) {
            $query .= "(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions)) as post_order";
        } else {
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

    public function bottom_five_trainee($dtWhere, $top_five_trainee_id, $Workshop_id = '0')
    {
        $TodayDt = date('Y-m-d H:i:s');
        $PostFlag = true;
        if ($Workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('atom_results', 'id', "workshop_session='POST' AND workshop_id=" . $Workshop_id);
            if (count((array)$WorshopSet) == 0) {
                $PostFlag = false;
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
        if ($PostFlag) {
            $query .= "(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions)) as post_order";
        } else {
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

    public function trainer_topic_wise_ce($dtWhere, $workshop_id)
    {
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

    public function trainer_topic_subtopic_wise_ce($RightsFlag, $islive_workshop, $trainer_id, $workshop_id, $trainee_id = "0")
    {
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

    public function wksh_trainer_histogram($RightsFlag, $islive_workshop, $trainer_id, $workshop_id, $workshop_session)
    {
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
        $query .= ") as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
        //        echo $query;
        //        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function wksh_topic_histogram($RightsFlag, $islive_workshop, $trainer_id, $workshop_id, $workshop_session)
    {
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




















    // Trainer Comperition Tab Model Start here ==============================================================================================    >
    public function isWorkshopPlayed($Workshop_id)
    {
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

    public function workshop_statistics_trainer_comperision($company_id, $trainer_id, $Workshop_id, $workshop_type_id = "0", $PreFlag, $PostFlag, $islive_workshop)
    {
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

    public function trainee_comparison($company_id, $islive_workshop, $trainer_id, $Workshop_id, $workshop_type_id)
    {
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
        $result = $this->db->query($query);
        return $result->result();
    }



































    // Trainer Accuracy Functions Start here 
    public function get_TraineeRegionData($company_id = '')
    {
        $lcSqlStr = "select du.region_id,r.region_name,r.id FROM device_users du "
            . " LEFT JOIN region as r "
            . " ON du.region_id = r.id where 1=1";
        if ($company_id != "") {
            $lcSqlStr .= " AND du.company_id=" . $company_id;
        }
        $lcSqlStr .= " group by r.id ";

        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }



    public function isWorkshopLive($workshop_id, $workshop_session)
    {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
            . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        $liveFlag = false;
        if ($workshop_session == "PRE") {
            if (strtotime($LiveSet->pre_date) >  strtotime($TodayDt)) {
                $liveFlag = true;
            }
        } else {
            if (strtotime($LiveSet->post_date) >  strtotime($TodayDt)) {
                $liveFlag = true;
            }
        }
        return $liveFlag;
    }
    public function get_traineeAccuracy($RightsFlag, $trainee_id = "0", $trainer_id, $workshop_id, $workshop_session, $liveFlag, $trainee_region_id = "0")
    {

        $login_id  = $this->mw_session['user_id'];

        $query = " SELECT z.* FROM (";
        if ($liveFlag) {
            $query .= " SELECT b.*,IF(wr.all_questions_fired=1,'Completed','Playing') AS status ,@curRank := @curRank + 1 AS rank 
                        FROM
                        (SELECT arp.workshop_id,arp.user_id AS trainee_id,arp.workshop_session, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
                         FORMAT(SUM(arp.is_correct)*100/ COUNT(arp.question_id),2) AS accuracy, SUM(arp.is_correct)*100/ COUNT(arp.question_id) AS acc_order,
                          COUNT(arp.question_id) AS played_questions, SUM(arp.is_correct) AS correct,
                          (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time,tr.region_name as trainee_region
                        FROM atom_results AS arp
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id  
                        LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                        LEFT JOIN region as tr ON tr.id=du.region_id,(SELECT @curRank := 0) r
                        where arp.workshop_id=$workshop_id AND wtu.tester_id IS NULL AND arp.workshop_session='$workshop_session'  ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY arp.user_id order by workshop_id,trainee_id,workshop_session) as b LEFT JOIN workshop_registered_users wr
                         ON wr.workshop_id=b.workshop_id AND wr.user_id=b.trainee_id AND wr.workshop_session=b.workshop_session
                         order by acc_order DESC,avg_time ASC,trainee_name ";
        } else {
            $query .= "SELECT fs.*,@curRank := @curRank + 1 AS rank FROM("
                . "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,tr.region_name as trainee_region,";
            if ($workshop_session == "PRE") {
                $query .= "SUM(ls.pre_correct) as correct,SUM(ls.pre_played_questions) as played_questions,"
                    . "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions) as acc_order,"
                    . "if(SUM(ls.pre_played_questions) < SUM(ls.pre_total_questions),'Incompleted','Completed') as status,
                    (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time";
            } else {
                $query .= "SUM(ls.post_correct) as correct,SUM(ls.post_played_questions) as played_questions,"
                    . "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_played_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_played_questions) as acc_order,"
                    . "if(SUM(ls.post_played_questions) < SUM(ls.post_total_questions),'Incompleted','Completed') as status,
                    (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time";
            }
            $query .= " FROM trainee_result AS ls 
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
                LEFT JOIN region as tr ON tr.id=du.region_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id ";
            $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY ls.trainee_id order by acc_order desc,avg_time ASC,trainee_name ) as fs,(SELECT @curRank := 0) r  ";
        }
        $query .= ")as z ";
        if ($trainee_id != "0") {
            $query .= " WHERE z.trainee_id=" . $trainee_id;
        }

        $result = $this->db->query($query);
        return $result->result();
    }
    public function top_five_trainee_accuracy($RightsFlag, $trainee_id = "0", $trainer_id, $workshop_id, $workshop_session, $liveFlag, $trainee_region_id = "0")
    {
        $login_id  = $this->mw_session['user_id'];
        $query = " SELECT fs.* FROM(";
        if ($liveFlag) {
            $query .= " SELECT arp.user_id as trainee_id, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
                format(SUM(arp.is_correct)*100/COUNT(arp.question_id),2) AS accuracy,SUM(arp.is_correct)*100/COUNT(arp.question_id) as acc_order,
                (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time
                FROM atom_results AS arp
                LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.workshop_id=$workshop_id ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " AND wtu.tester_id IS NULL and arp.workshop_session='$workshop_session' GROUP BY arp.user_id order by acc_order desc,avg_time asc,trainee_name limit 0,5 ";
        } else {
            $query .= "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,";
            if ($workshop_session == "PRE") {
                $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions) as acc_order,
                                (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time ";
            } else {
                $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) as acc_order,
                                (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time";
            }
            $query .= " FROM trainee_result AS ls 
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id ";
            $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            $query .= "
                GROUP BY ls.trainee_id order by acc_order desc,avg_time asc,trainee_name limit 0,5  ";
        }
        $query .= " ) as fs ";
        if ($trainee_id != "0") {
            $query .= " WHERE fs.trainee_id=" . $trainee_id;
        }
        // echo $query;
        // exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function bottom_five_trainee_accuracy($RightsFlag, $trainee_id = "0", $trainer_id, $workshop_id, $workshop_session, $top_five_trainee_id, $liveFlag, $trainee_region_id = "0")
    {
        $login_id  = $this->mw_session['user_id'];
        $query = " SELECT fs.* FROM(";
        if ($liveFlag) {
            $query .= "SELECT arp.user_id as trainee_id, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
                format(SUM(arp.is_correct)*100/COUNT(arp.question_id),2) AS accuracy,SUM(arp.is_correct)*100/COUNT(arp.question_id) as acc_order,
                (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time
                FROM atom_results AS arp
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                WHERE wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id AND arp.workshop_session='$workshop_session' ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            if ($top_five_trainee_id != "") {
                $query .= " AND arp.user_id NOT IN(" . $top_five_trainee_id . ")";
            }
            $query .= " GROUP BY arp.user_id order by acc_order asc,avg_time desc,trainee_name desc limit 0,5 ";
        } else {
            $query .= "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,";
            if ($workshop_session == "PRE") {
                $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions) as acc_order,
                     (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time ";
            } else {
                $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) as acc_order,
                     (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time ";
            }
            $query .= " FROM trainee_result AS ls 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id    
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id ";
            $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            if ($top_five_trainee_id != "") {
                $query .= " AND ls.trainee_id NOT IN(" . $top_five_trainee_id . ")";
            }
            $query .= " GROUP BY ls.trainee_id order by acc_order asc ,avg_time desc,trainee_name desc limit 0,5  ";
        }
        $query .= " ) as fs ";
        if ($trainee_id != "0") {
            $query .= " WHERE fs.trainee_id=" . $trainee_id;
        }
        //echo $query;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_PrepostAccuracy($workshop_id, $trainee_id = "0", $workshop_session, $trainer_id = "0", $RightsFlag, $liveFlag, $trainee_region_id = "0")
    {
        $login_id  = $this->mw_session['user_id'];
        if ($liveFlag) {
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,
                (SUM(arp.is_correct)*100/COUNT(arp.question_id)) AS accuracy
                FROM atom_results AS arp
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                INNER JOIN question_topic qt ON qt.id=arp.topic_id
                LEFT JOIN question_subtopic qst ON qst.id=arp.subtopic_id
                LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                WHERE wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id AND arp.workshop_session='$workshop_session' ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainee_id != "0") {
                $query .= " AND arp.user_id=$trainee_id";
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY arp.topic_id,arp.subtopic_id order by arp.topic_id ";
        } else {
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,";
            if ($workshop_session == "PRE") {
                $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
            } else {
                $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
            }
            $query .= " FROM trainee_result AS ls INNER JOIN question_topic qt ON qt.id=ls.topic_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
                LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id 
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id ";
            $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
            if ($trainee_region_id != "0") {
                $query .= " AND  du.region_id = " . $trainee_region_id;
            }
            if ($trainee_id != "0") {
                $query .= " and ls.trainee_id=$trainee_id";
            }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY ls.topic_id,ls.subtopic_id order by topic,accuracy desc  ";
        }

        $result = $this->db->query($query);
        return $result->result();
    }

    function getTrainee($workshop_id, $workshop_session)
    {
        $querystr = "Select distinct(wru.user_id) as user_id,concat(du.firstname,' ',du.lastname) as username "
            . " from workshop_registered_users wru"
            . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wru.workshop_id AND wtu.tester_id=wru.user_id "
            . " inner join device_users du on du.user_id=wru.user_id where wtu.tester_id IS NULL AND wru.workshop_id=" . $workshop_id;
        if ($workshop_session != "") {
            $querystr .= " AND wru.workshop_session='" . $workshop_session . "'";
        }
        $result = $this->db->query($querystr);
        return $result->result();
    }
    // Trainer Accuracy Functions end here 



    // ==========================================================================================================================================

























    // Trainee Dashboard I Functions Start here 
    public function getTraineeData($company_id, $workshoptype_id, $trainee_id, $dtOrder, $dtLimit, $dtWhere2, $RightsFlag, $WRightsFlag)
    {
        $dtWhere = "";
        $login_id = $this->mw_session['user_id'];
        if ($workshoptype_id != "0") {
            $dtWhere = " AND w.workshop_type  = " . $workshoptype_id;
        }
        $TodayDt = date('Y-m-d H:i:s');
        $query = "
                SELECT DATE_FORMAT(w.start_date,'%d-%m-%Y') AS start_date, w.workshop_name,ls.workshop_id, 
                FORMAT(SUM(ls.pre_correct)*100/sum(ls.pre_total_questions),2) as pre_average,
                IFNULL(FORMAT(SUM(ls.post_correct)*100/sum(ls.post_total_questions),2),'NP') as post_average,
                FORMAT(SUM(post_time_taken)/sum(ls.post_total_questions),2) as avg_time,count(distinct ar.topic_id) as total_topic  FROM (

                SELECT w.workshop_id,w.pre_correct,sum(w.pre_total_questions) as pre_total_questions,
                sum(w.post_correct) as post_correct,sum(w.post_total_questions) as post_total_questions,
                sum(w.pre_time_taken) as pre_time_taken,sum(w.post_time_taken) as post_time_taken
                 FROM trainee_result as w 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id 
                WHERE w.company_id=$company_id AND w.trainee_id =$trainee_id AND wtu.tester_id IS NULL $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND w.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by w.workshop_id union all 
                SELECT arp.workshop_id ,SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,0 as post_correct,
                0 as post_total_questions,sum(arp.seconds) as pre_time_taken,0 as post_time_taken  FROM atom_results as arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id 
                where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='PRE' AND
                CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by arp.workshop_id union all 
                SELECT arp.workshop_id ,0 as pre_correct,0 as pre_total_questions,SUM(arp.is_correct) AS post_correct,
                 COUNT(arp.question_id) AS post_total_questions,0 as pre_time_taken,sum(arp.seconds) as post_time_taken FROM atom_results as arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id 
                where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='POST' AND
                CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by arp.workshop_id
                ) as ls
                LEFT JOIN atom_results ar ON ar.company_id=$company_id AND ar.user_id =$trainee_id AND ar.workshop_id=ls.workshop_id
                LEFT JOIN workshop AS w ON w.id=ls.workshop_id
                $dtWhere2
                group by ls.workshop_id $dtOrder $dtLimit  ";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        $query1 = "SELECT count(distinct a.workshop_id) AS total FROM atom_results AS a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id 
            $dtWhere2 ";
        if ($dtWhere2 != "") {
            $query1 .= " AND a.company_id=$company_id AND a.user_id =$trainee_id AND wtu.tester_id IS NULL";
        } else {
            $query1 .= " WHERE a.company_id=$company_id AND a.user_id =$trainee_id AND wtu.tester_id IS NULL";
        }
        $result1 = $this->db->query($query1);
        $data_array = $result1->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }

    public function getPrePostData($workshop_id, $trainee_id = '', $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT a.trainee_id,b.workshop_name, 
                IF(a.pre_played_questions=0,'Not Played', CONCAT(a.pre_avg,'%')) AS pre_average, 
                IF(a.post_played_questions=0,'Not Played', CONCAT(a.post_avg,'%')) AS post_average,a.post_avg,a.pre_avg, 
                format(a.post_avg-a.pre_avg,2) AS ce,FORMAT(a.avg_time,2) AS response_time,a.trainee_region,
                a.trainee_name ,@curRank := @curRank + 1 AS rank
                FROM (
                select a.trainee_id,a.workshop_id, FORMAT(sum(a.pre_correct)*100/sum(a.pre_total_questions),2) as pre_avg,
                FORMAT(sum(a.post_correct)*100/sum(a.post_total_questions),2) as post_avg,
                SUM(a.post_correct)*100/ SUM(a.post_total_questions) AS post_order,
                (SUM(a.pre_time_taken)+ SUM(a.post_time_taken))/ SUM(a.pre_total_questions)+ SUM(a.post_total_questions) AS avg_time,
                sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions,
                CONCAT(du.firstname,' ',du.lastname) AS trainee_name,tr.region_name AS trainee_region  FROM trainee_result AS a
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
                LEFT JOIN device_users AS du ON du.user_id=a.trainee_id
                LEFT JOIN region AS tr ON tr.id=du.region_id
                WHERE a.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND a.trainee_id =$trainee_id";
        } else {
            $query .= " AND wtu.tester_id IS NULL ";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $query .= " group by a.trainee_id) as a
                LEFT JOIN workshop AS b ON b.id=a.workshop_id
                ,(
                SELECT @curRank := 0) r
                ORDER BY post_order DESC,avg_time,trainee_name";


        $result = $this->db->query($query);
        return $result->result();
    }

    public function getLivePrePostData($workshop_id = '', $trainee_id = '', $trainer_id = "0", $RightsFlag = 1)
    {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT fs.trainee_id,b.workshop_name,fs.trainee_name,FORMAT(fs.avg_time,2) AS response_time,fs.trainee_region, IF(fs.pre_avg is null,'Not Played',CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg is null,'Not Played',CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,fs.pre_avg, FORMAT(fs.post_avg-fs.pre_avg,2) AS ce
        ,@curRank := @curRank + 1 AS rank
        FROM (
        SELECT ls.trainee_id,ls.workshop_id, FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_avg, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_avg, SUM(ls.post_correct) AS post_correct, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,tr.region_name AS trainee_region
        FROM (
        SELECT es.trainee_id,es.workshop_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions,sum(es.pre_time_taken) AS total_time
        FROM trainee_result AS es
        WHERE es.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND es.trainee_id =$trainee_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY es.trainee_id UNION ALL
        SELECT arp.user_id AS trainee_id,arp.workshop_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,
         0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' "
            . "AND arp.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND arp.user_id =$trainee_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id UNION ALL
        SELECT arp.user_id AS trainee_id,arp.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' "
            . "AND arp.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND arp.user_id =$trainee_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id) AS ls 
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
        LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
        LEFT JOIN region AS tr ON tr.id=du.region_id
        where wtu.tester_id IS NULL GROUP BY ls.trainee_id
        ORDER BY post_correct DESC,avg_time,trainee_name
        ) AS fs
        LEFT JOIN workshop AS b ON b.id=fs.workshop_id,(SELECT @curRank := 0) r";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getPrePostWorkshopwise($workshop_id, $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $PreFlag = false;
        $query = "select distinct workshop_id from atom_results where workshop_id=$workshop_id AND workshop_session='PRE' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PreFlag = true;
        }

        $query = "SELECT b.workshop_name,SUM(pre_correct),SUM(pre_total_questions), FORMAT((SUM(pre_correct)*100/ SUM(pre_total_questions)),2) AS pre_average,
           FORMAT((SUM(post_correct)*100/ SUM(post_total_questions)),2) AS post_average
           FROM trainee_result AS a LEFT JOIN workshop AS b ON b.id=a.workshop_id
           LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
           WHERE a.workshop_id= $workshop_id and wtu.tester_id IS NULL ";
        if ($PreFlag) {
            $query .= " AND a.ce_eligible=1 ";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $result = $this->db->query($query);
        return $result->row();
    }

    public function getLivePrePostWorkshopwise($workshop_id, $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $PostFlag = false;
        $PreFlag = false;
        $query = "select distinct workshop_id from atom_results where workshop_id=$workshop_id AND workshop_session='POST' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PostFlag = true;
        }
        $query = "select distinct workshop_id from atom_results where workshop_id=$workshop_id AND workshop_session='PRE' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PreFlag = true;
        }
        $TodayDt = date('Y-m-d H:i');
        $query = " SELECT ls.workshop_id,b.workshop_name, 
            FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, 
            FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average
        FROM (
        SELECT es.workshop_id,sum(es.pre_correct) as pre_correct ,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions
        FROM trainee_result AS es 
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=es.workshop_id AND wtu.tester_id=es.trainee_id
        WHERE es.workshop_id= $workshop_id AND wtu.tester_id IS NULL ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        if ($PostFlag) {
            $query .= " AND es.trainee_id IN 
            (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='POST')";
        }
        $query .= " GROUP BY es.workshop_id UNION ALL
        SELECT arp.workshop_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
        WHERE wtu.tester_id IS NULL AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.workshop_id= $workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        if ($PostFlag) {
            $query .= " AND arp.user_id IN (SELECT distinct user_id FROM atom_results"
                . " WHERE workshop_id=$workshop_id AND workshop_session='POST')";
        }
        $query .= " GROUP BY arp.workshop_id UNION ALL
        SELECT arp.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id 
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
        WHERE wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.workshop_id= $workshop_id";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        if ($PreFlag) {
            $query .= " AND arp.user_id IN (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='PRE')";
        }
        $query .= " GROUP BY arp.workshop_id
        ) AS ls
        LEFT JOIN workshop AS b ON b.id=ls.workshop_id";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_PrePostTopicwise($workshop_id = '', $trainee_id = '', $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $query = $query = "
                SELECT qt.description AS topic,qst.description AS subtopic,
                FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average,
                FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2) AS ce,
                if(sum(ls.pre_total_questions)>0,sum(ls.pre_status),1) as pre_status,if(sum(ls.post_total_questions)>0,sum(ls.post_status),1) as post_status
                FROM trainee_result AS ls ";
        $query .= "
            INNER JOIN question_topic qt ON qt.id=ls.topic_id
            LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
            WHERE  ls.workshop_id=$workshop_id and ls.trainee_id=$trainee_id ";
        if (!$RightsFlag) {
            $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_LivePrePostTopicwise($workshop_id = '', $trainee_id = '', $RightsFlag = 1)
    {
        $TodayDt = date('Y-m-d H:i');
        $login_id = $this->mw_session['user_id'];
        $query = $query = "
                SELECT qt.description AS topic,qst.description AS subtopic,
                FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average,
                FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2) AS ce,
                if(sum(ls.pre_total_questions)>0,sum(ls.pre_status),1) as pre_status,if(sum(ls.post_total_questions)>0,sum(ls.post_status),1) as post_status
                FROM (
                SELECT w.topic_id,w.subtopic_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,0 as post_correct,
                0 as post_total_questions, w.pre_status, 0 as post_status
                FROM trainee_result AS w
                WHERE w.workshop_id=$workshop_id and w.trainee_id= $trainee_id ";
        if (!$RightsFlag) {
            $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY w.topic_id,w.subtopic_id UNION ALL    
                SELECT arp.topic_id,arp.subtopic_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,
                0 AS post_correct, 0 AS post_total_questions, 0 as pre_status, 0 as post_status
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id
                WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id and arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'";
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= "
                GROUP BY arp.topic_id,arp.subtopic_id UNION ALL
                SELECT arp.topic_id,arp.subtopic_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions,
                0 as pre_status, 0 as post_status
                FROM atom_results AS arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'";
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY arp.topic_id,arp.subtopic_id
                ) as ls ";
        $query .= "
            INNER JOIN question_topic qt ON qt.id=ls.topic_id
            LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
            GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getPrePostQuestionAnsData($workshop_id = '', $trainee_id = '', $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT sum(pre_correct) as pre_correct ,sum(pre_total_questions) as pre_total_questions ,"
            . "sum(post_correct) as post_correct,sum(post_total_questions) as post_total_questions,"
            . "sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions  FROM trainee_result"
            . " WHERE workshop_id=$workshop_id AND trainee_id=$trainee_id";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (trainer_id = $login_id OR trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND trainer_id= " . $trainer_id;
        }
        $result = $this->db->query($query);
        return $result->row();
    }

    public function getLivePrePostQuestionAnsData($workshop_id, $trainee_id, $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $TodayDt = date('Y-m-d H:i:s');
        $query = " SELECT SUM(ls.pre_correct) as pre_correct, SUM(ls.pre_total_questions)  AS pre_total_questions,
            SUM(ls.post_correct) as post_correct, SUM(ls.post_total_questions) as post_total_questions,
            sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions
            FROM (
            SELECT sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions,
            sum(es.pre_played_questions) as pre_played_questions,0 as post_played_questions
            FROM trainee_result AS es
            WHERE es.workshop_id= $workshop_id AND es.trainee_id =$trainee_id";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= "    
            UNION ALL
            SELECT SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 
             0 AS post_correct, 0 AS post_total_questions,COUNT(arp.question_id) as pre_played_questions,0 as post_played_questions
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND "
            . "arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id ";
        if ($trainer_id != "0") {
            $query .= " AND arp.trainer_id=" . $trainer_id;
        }
        $query .= " UNION ALL
            SELECT 0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct,
            COUNT(arp.question_id) AS post_total_questions,0 as pre_played_questions,COUNT(arp.question_id) as post_played_questions
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' "
            . "AND arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id ";
        if ($trainer_id != "0") {
            $query .= " AND arp.trainer_id=" . $trainer_id;
        }
        $query .= ") AS ls";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_WorkshopRegisterdusers($workshop_id, $Company_id = "")
    {
        $querystr = "Select distinct(wru.user_id) as user_id,concat(du.firstname,' ',du.lastname) as username "
            . " from workshop_registered_users wru "
            . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wru.workshop_id AND wtu.tester_id=wru.user_id "
            . " inner join device_users du on du.user_id=wru.user_id where wtu.tester_id IS NULL AND wru.workshop_id=" . $workshop_id;
        if ($Company_id != "") {
            $querystr .= " AND wru.company_id=" . $Company_id;
        }
        $querystr .= " order by username ";
        $result = $this->db->query($querystr);
        return $result->result();
    }

    // public function get_PrepostAccuracy($workshop_id = '', $trainee_id = '', $workshop_session = "PRE") {
    //     $TodayDt = date('Y-m-d H:i:s');
    //     $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
    //             . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
    //     $ObjSet = $this->db->query($query);
    //     $LiveSet = $ObjSet->row();
    //     $liveFlag = false;
    //     if ($workshop_session == "PRE") {
    //         if (strtotime($LiveSet->pre_date) > strtotime($TodayDt)) {
    //             $liveFlag = true;
    //         }
    //     } else {
    //         if (strtotime($LiveSet->post_date) > strtotime($TodayDt)) {
    //             $liveFlag = true;
    //         }
    //     }
    //     if ($liveFlag) {
    //         $query = "SELECT qt.description AS topic,qst.description AS subtopic,
    //             (SUM(arp.is_correct)*100/COUNT(arp.question_id)) AS accuracy
    //             FROM atom_results AS arp
    //             INNER JOIN question_topic qt ON qt.id=arp.topic_id
    //             LEFT JOIN question_subtopic qst ON qst.id=arp.subtopic_id
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
    //             WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id and"
    //             . " arp.workshop_session='$workshop_session' AND wtu.tester_id IS NULL "
    //             . "GROUP BY arp.topic_id,arp.subtopic_id order by arp.topic_id  ";
    //     } else {
    //         $query = "SELECT qt.description AS topic,qst.description AS subtopic,";
    //         if ($workshop_session == "PRE") {
    //             $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
    //         } else {
    //             $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
    //         }
    //         $query .= " FROM trainee_result AS ls 
    //             INNER JOIN question_topic qt ON qt.id=ls.topic_id
    //             LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
    //             WHERE  ls.workshop_id=$workshop_id and ls.trainee_id=$trainee_id AND wtu.tester_id IS NULL
    //             GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";
    //     }
    //     $result = $this->db->query($query);
    //     return $result->result();
    // }

    public function isWorkshopLiveTrainee($workshop_id)
    {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select if(end_date >='$TodayDt',1,0) as live_workshop FROM workshop where id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        return $LiveSet->live_workshop;
    }

    public function get_Traineewise_Rank($workshop_id = '', $user_id = '', $islive_workshop = "")
    {
        $TasterFlag = true;
        if ($islive_workshop == "") {
            $islive_workshop = $this->isWorkshopLiveTrainee($workshop_id);
        }
        if ($user_id != "" && $workshop_id != "") {
            $query = "select tester_id FROM workshop_tester_users where workshop_id = $workshop_id AND tester_id =" . $user_id;
            $ObjSet = $this->db->query($query);
            if (count((array)$ObjSet->row()) > 0) {
                $TasterFlag = false;
            }
        }
        $query = "select distinct workshop_id FROM atom_results where workshop_session='POST' AND workshop_id=" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        if ($islive_workshop) {

            $TodayDt = date('Y-m-d H:i');
            if (count((array)$LiveSet) > 0) {
                $LcSqlStr = " SELECT z.* FROM (SELECT fs.*,@curRank := @curRank + 1 AS rank FROM(
                    SELECT arp.user_id as trainee_id ,sum(arp.is_correct) as post_correct, FORMAT(SUM(arp.is_correct)*100/ count(arp.question_id),2) AS post_avg, 
                    SUM(arp.seconds)/count(arp.question_id) AS avg_time,CONCAT(du.firstname,' ',du.lastname) AS trainee_name
                    FROM atom_results AS arp
                    INNER JOIN workshop AS w ON w.id=arp.workshop_id
                    LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                    WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' 
                    AND arp.workshop_id=$workshop_id ";
                if ($TasterFlag) {
                    $LcSqlStr .= " and wtu.tester_id IS NULL ";
                }
                $LcSqlStr .= " GROUP BY arp.user_id ORDER BY post_correct DESC,avg_time,trainee_name
                    ) as fs ,(SELECT @curRank := 0) r) as z ";
            } else {
                $LcSqlStr = " SELECT z.* FROM (SELECT fs.*,@curRank := @curRank + 1 AS rank FROM(
                        SELECT arp.user_id as trainee_id,sum(arp.is_correct) as pre_correct, FORMAT(SUM(arp.is_correct)*100/ count(arp.question_id),2) AS pre_avg, 
                        SUM(arp.seconds)/count(arp.question_id) AS avg_time,CONCAT(du.firstname,' ',du.lastname) AS trainee_name
                        FROM atom_results AS arp
                        INNER JOIN workshop AS w ON w.id=arp.workshop_id
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                        LEFT JOIN device_users AS du ON du.user_id=arp.user_id  WHERE arp.workshop_session='PRE' AND
                        CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND
                        arp.workshop_id=$workshop_id ";
                if ($TasterFlag) {
                    $LcSqlStr .= " and wtu.tester_id IS NULL ";
                }
                $LcSqlStr .= " GROUP BY arp.user_id
                        ORDER BY pre_correct DESC,avg_time,trainee_name
                        ) as fs ,(SELECT @curRank := 0) r) as z  ";
            }
        } else {
            $LcSqlStr = "SELECT z.* FROM(
                SELECT ls.*,@curRank := @curRank + 1 AS rank
                FROM (
                SELECT a.trainee_id,a.workshop_id, (SUM(a.post_correct)*100/ SUM(a.post_total_questions)) AS post_avg,
                (SUM(a.pre_correct)*100/ SUM(a.pre_total_questions)) AS pre_avg,
                (sum(a.pre_time_taken) +sum(a.post_time_taken))/(sum(a.pre_played_questions)+sum(a.post_played_questions)) as avg_time,
                 CONCAT(du.firstname,' ',du.lastname) AS trainee FROM trainee_result as a
                LEFT JOIN device_users as du ON du.user_id=a.trainee_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
                where a.workshop_id=$workshop_id ";
            if ($TasterFlag) {
                $LcSqlStr .= " and wtu.tester_id IS NULL ";
            }
            if (count((array)$LiveSet) > 0) {
                $LcSqlStr .= " group by a.trainee_id order by post_avg desc,avg_time,trainee";
            } else {
                $LcSqlStr .= " group by a.trainee_id order by pre_avg desc,avg_time,trainee";
            }
            $LcSqlStr .= ") AS ls
                ,(SELECT @curRank := 0) r) as z  ";
        }
        if ($user_id != "") {
            $LcSqlStr .= " where z.trainee_id=" . $user_id;
        }
        $query = $this->db->query($LcSqlStr);
        return $query->result();
    }

    public function SynchTraineeData($Company_id = "", $Workshop_id = "")
    {
        if ($Company_id == "") {
            return false;
        }
        $CurrentTime = date('Y-m-d H:i');

        // Delete Pre Data set only
        $query = "SELECT distinct a.workshop_id,CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) as post_enddate "
            . "FROM trainee_result as a INNER JOIN workshop as b ON b.id=a.workshop_id "
            . "where a.company_id= $Company_id AND a.liveflag=1 "
            . " AND CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) <= '$CurrentTime'";
        if ($Workshop_id != "") {
            $query .= " AND a.workshop_id=" . $Workshop_id;
        }

        $ObjSet = $this->db->query($query);
        $Del_WorkshopSet = $ObjSet->result();
        if (count((array)$Del_WorkshopSet) > 0) {
            foreach ($Del_WorkshopSet as $value) {
                $tWorkshop_id = $value->workshop_id;
                $lcSqlStr = "delete from trainee_result where workshop_id=" . $tWorkshop_id;
                $this->db->query($lcSqlStr);
            }
        }
        $query = "SELECT distinct a.workshop_id,date(b.start_date) as start_date ,b.workshop_type,b.region,"
            . "CONCAT(b.pre_end_date,' ', STR_TO_DATE(b.pre_end_time, '%l:%i %p')) as pre_enddate,"
            . "CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) as post_enddate FROM atom_results as a LEFT JOIN "
            . " workshop as b ON b.id=a.workshop_id where a.company_id= $Company_id "
            . "AND a.workshop_id NOT IN(select distinct workshop_id FROM trainee_result where company_id= $Company_id)"
            . " AND (CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) <= '$CurrentTime' OR CONCAT(b.pre_end_date,' ', STR_TO_DATE(b.pre_end_time, '%l:%i %p')) <= '$CurrentTime')";
        if ($Workshop_id != "") {
            $query .= " AND a.workshop_id=" . $Workshop_id;
        }

        $result = $this->db->query($query);
        $WorkshopSet = $result->result();
        if (count((array)$WorkshopSet) > 0) {
            foreach ($WorkshopSet as $value) {
                $Workshop_id = $value->workshop_id;
                $Pre_endDate = $value->pre_enddate;
                $Post_endDate = $value->post_enddate;
                $tPostPlayed = $this->common_model->get_value('atom_results', 'id', " workshop_session='post' AND workshop_id=" . $Workshop_id);
                if ($Post_endDate != '1970-01-01 00:00:00' && strtotime($Post_endDate) <= strtotime($CurrentTime) && count((array)$tPostPlayed) > 0) {
                    $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainer_id,trainee_id,topic_id,subtopic_id,workshop_date,pre_correct,pre_played_questions, pre_total_questions,pre_avg,post_correct, post_played_questions,post_total_questions,post_avg,avg_time,workshop_type,region_id,pre_time_taken,
                        post_time_taken,pre_status,post_status,istester,liveflag)
                    SELECT $Company_id AS company_id,prpo.workshop_id,prpo.trainer_id,prpo.user_id,prpo.topic_id,prpo.subtopic_id,'" . $value->start_date . "' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton, SUM(prpo.pre_total_questions) AS pre_total_questions,
                    FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
                     SUM(prpo.post_correct) AS post_correct, SUM(prpo.post_played_quesiton) AS post_played_quesiton,
                      SUM(prpo.post_total_questions) AS post_total_questions, 
                      FORMAT((SUM(prpo.post_correct)*100)/ SUM(prpo.post_total_questions),2) AS post_average,
                    FORMAT(((SUM(prpo.pre_time_taken)+ SUM(prpo.post_time_taken))/ (SUM(prpo.pre_played_quesiton)+ SUM(prpo.post_played_quesiton))),2) AS avgtime, '" . $value->workshop_type . "' AS workshop_type,'" . $value->region . "' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken,
                     SUM(prpo.post_time_taken) AS post_time_taken,sum(prpo.pre_status) as pre_status,sum(prpo.post_status) as post_status, (wtu.tester_id IS NOT NULL) istester,0 AS liveflag
                    FROM(
                    SELECT a.user_id,a.workshop_id, b.pre_correct,a.total_questions AS pre_total_questions, 0 AS post_correct,0 AS post_total_questions,b.total_seconds_taken AS pre_time_taken, 0 AS post_time_taken,
                     b.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton, a.trainer_id,a.topic_id,a.subtopic_id,
                     if(b.pre_correct is null,1,0) as pre_status, 0 as post_status FROM
                    (
                    select a.workshop_id,a.user_id,b.trainer_id,b.topic_id,b.subtopic_id,b.total_questions from atom_results as a LEFT JOIN(
                            SELECT wq.workshop_id,COUNT(DISTINCT wq.question_id) AS total_questions, wq.trainer_id,wq.topic_id,wq.subtopic_id
                            FROM workshop_questions AS wq
                            INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                            WHERE wq.workshop_id = $Workshop_id
                            GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id,wq.topic_id,wq.subtopic_id
                            )as b ON b.workshop_id=a.workshop_id
                            where a.workshop_id=$Workshop_id and a.workshop_session='PRE'
                            group by a.user_id,b.trainer_id,b.topic_id,b.subtopic_id
                            order by user_id,b.subtopic_id
                    ) as a LEFT JOIN
                    (
                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session,SUM(arp.is_correct) AS pre_correct, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton,
                    arp.trainer_id,arp.topic_id,arp.subtopic_id
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id,arp.topic_id,arp.subtopic_id
                    ) as b  ON a.user_id=b.user_id AND a.workshop_id = b.workshop_id AND a.trainer_id=b.trainer_id AND a.topic_id=b.topic_id 
                    AND a.subtopic_id=b.subtopic_id
                    union all
                    SELECT c.user_id,c.workshop_id, 0 pre_correct,0 AS pre_total_questions, d.post_correct AS post_correct,
                    c.total_questions AS post_total_questions,0 AS pre_time_taken, d.total_seconds_taken AS post_time_taken,
                     0 AS pre_played_quesiton,d.played_quesiton AS post_played_quesiton, c.trainer_id,c.topic_id,c.subtopic_id,
                    0 as pre_status, if(d.post_correct is null,1,0) as post_status FROM
                    (
                    select a.workshop_id,a.user_id,b.trainer_id,b.topic_id,b.subtopic_id,b.total_questions from atom_results as a LEFT JOIN(
                            SELECT wq.workshop_id,COUNT(DISTINCT wq.question_id) AS total_questions, wq.trainer_id,wq.topic_id,wq.subtopic_id
                            FROM workshop_questions AS wq
                            INNER JOIN workshop_questionset_post AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                            WHERE wq.workshop_id = $Workshop_id
                            GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id,wq.topic_id,wq.subtopic_id
                            )as b ON b.workshop_id=a.workshop_id
                            where a.workshop_id=$Workshop_id and a.workshop_session='POST'
                            group by a.user_id,b.trainer_id,b.topic_id,b.subtopic_id
                            order by user_id,b.subtopic_id
                    ) as c LEFT JOIN
                    (
                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session,SUM(arp.is_correct) AS post_correct, SUM(arp.seconds) AS total_seconds_taken, 
                    COUNT(arp.question_id) AS played_quesiton,
                    arp.trainer_id,arp.topic_id,arp.subtopic_id
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'POST'
                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id,arp.topic_id,arp.subtopic_id
                    ) as d  ON c.user_id=d.user_id AND c.workshop_id = d.workshop_id AND c.trainer_id=d.trainer_id AND c.topic_id=d.topic_id 
                    AND c.subtopic_id=d.subtopic_id
                    ) AS prpo
                    LEFT JOIN device_users AS du ON du.user_id=prpo.user_id
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=prpo.workshop_id AND wtu.tester_id=prpo.user_id  
                    GROUP BY prpo.user_id,prpo.workshop_id,prpo.trainer_id,prpo.topic_id,prpo.subtopic_id
                    ORDER BY post_average DESC,avgtime ASC, CONCAT(du.firstname,' ',du.lastname)
                    ";
                    $this->db->query($lcSqlStr);
                    //CE Eligible
                    $query = "update trainee_result as a LEFT join 
                        (select tr.trainee_id,tr.workshop_id,if(SUM(tr.pre_played_questions) >0 && SUM(tr.post_played_questions)>0 ,1,0) as ce_eligible FROM trainee_result as tr
                        where tr.workshop_id=$Workshop_id group by tr.trainee_id
                        ) as b ON a.workshop_id=b.workshop_id and a.trainee_id=b.trainee_id
                        set a.ce_eligible=b.ce_eligible
                        where a.workshop_id=$Workshop_id";
                    $this->db->query($query);
                } else if ($Pre_endDate != '1970-01-01 00:00:00' && strtotime($Pre_endDate) <= strtotime($CurrentTime)) {
                    $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainer_id,trainee_id,topic_id,subtopic_id,workshop_date,pre_correct,pre_played_questions, pre_total_questions,pre_avg,post_correct, post_played_questions,post_total_questions,post_avg,avg_time,workshop_type,region_id,pre_time_taken,
                        post_time_taken,pre_status,post_status,istester,liveflag)
                    SELECT $Company_id AS company_id,prpo.workshop_id,prpo.trainer_id,prpo.user_id,prpo.topic_id,prpo.subtopic_id,'" . $value->start_date . "' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton, SUM(prpo.pre_total_questions) AS pre_total_questions,
                    FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
                     SUM(prpo.post_correct) AS post_correct, SUM(prpo.post_played_quesiton) AS post_played_quesiton,
                      SUM(prpo.post_total_questions) AS post_total_questions, 
                      FORMAT((SUM(prpo.post_correct)*100)/ SUM(prpo.post_total_questions),2) AS post_average,
                    FORMAT(((SUM(prpo.pre_time_taken)+ SUM(prpo.post_time_taken))/ (SUM(prpo.pre_played_quesiton)+ SUM(prpo.post_played_quesiton))),2) AS avgtime, '" . $value->workshop_type . "' AS workshop_type,'" . $value->region . "' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken,
                     SUM(prpo.post_time_taken) AS post_time_taken,sum(prpo.pre_status) as pre_status,sum(prpo.post_status) as post_status, (wtu.tester_id IS NOT NULL) as istester,1 AS liveflag
                    FROM(
                    SELECT a.user_id,a.workshop_id, b.pre_correct,a.total_questions AS pre_total_questions, 0 AS post_correct,0 AS post_total_questions,b.total_seconds_taken AS pre_time_taken, 0 AS post_time_taken,
                     b.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton, a.trainer_id,a.topic_id,a.subtopic_id,
                     if(b.pre_correct is null,1,0) as pre_status, 0 as post_status FROM
                    (
                    select a.workshop_id,a.user_id,b.trainer_id,b.topic_id,b.subtopic_id,b.total_questions from atom_results as a LEFT JOIN(
                            SELECT wq.workshop_id,COUNT(DISTINCT wq.question_id) AS total_questions, wq.trainer_id,wq.topic_id,wq.subtopic_id
                            FROM workshop_questions AS wq
                            INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                            WHERE wq.workshop_id = $Workshop_id
                            GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id,wq.topic_id,wq.subtopic_id
                            )as b ON b.workshop_id=a.workshop_id
                            where a.workshop_id=$Workshop_id and a.workshop_session='PRE'
                            group by a.user_id,b.trainer_id,b.topic_id,b.subtopic_id
                            order by user_id,b.subtopic_id
                    ) as a LEFT JOIN
                    (
                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session,SUM(arp.is_correct) AS pre_correct, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton,
                    arp.trainer_id,arp.topic_id,arp.subtopic_id
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id,arp.topic_id,arp.subtopic_id
                    ) as b  ON a.user_id=b.user_id AND a.workshop_id = b.workshop_id AND a.trainer_id=b.trainer_id AND a.topic_id=b.topic_id 
                    AND a.subtopic_id=b.subtopic_id) AS prpo
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=prpo.workshop_id AND wtu.tester_id=prpo.user_id  
                    LEFT JOIN device_users AS du ON du.user_id=prpo.user_id
                    GROUP BY prpo.user_id,prpo.workshop_id,prpo.trainer_id,prpo.topic_id,prpo.subtopic_id
                    ORDER BY post_average DESC,avgtime ASC, CONCAT(du.firstname,' ',du.lastname)";
                    // echo $lcSqlStr.'<br/>';
                    $this->db->query($lcSqlStr);
                }
            }
        }
    }
    public function get_traineeAccuracyTrainee($RightsFlag, $trainee_id, $trainer_id = "0", $workshop_id, $workshop_session, $liveFlag)
    {

        $login_id  = $this->mw_session['user_id'];

        $query = " SELECT z.* FROM (";
        if ($liveFlag) {
            $query .= " SELECT b.*,IF(wr.all_questions_fired=1,'Completed','Playing') AS status ,@curRank := @curRank + 1 AS rank 
                        FROM
                        (SELECT arp.workshop_id,arp.user_id AS trainee_id,arp.workshop_session, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
                         FORMAT(SUM(arp.is_correct)*100/ COUNT(arp.question_id),2) AS accuracy, SUM(arp.is_correct)*100/ COUNT(arp.question_id) AS acc_order,
                          COUNT(arp.question_id) AS played_questions, SUM(arp.is_correct) AS correct,
                          (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time,tr.region_name as trainee_region
                        FROM atom_results AS arp
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id  
                        LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                        LEFT JOIN region as tr ON tr.id=du.region_id,(SELECT @curRank := 0) r
                        where arp.workshop_id=$workshop_id AND wtu.tester_id IS NULL AND arp.workshop_session='$workshop_session'  ";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY arp.user_id order by workshop_id,trainee_id,workshop_session) as b LEFT JOIN workshop_registered_users wr
                         ON wr.workshop_id=b.workshop_id AND wr.user_id=b.trainee_id AND wr.workshop_session=b.workshop_session
                         order by acc_order DESC,avg_time ASC,trainee_name ";
        } else {
            $query .= "SELECT fs.*,@curRank := @curRank + 1 AS rank FROM("
                . "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,tr.region_name as trainee_region,";
            if ($workshop_session == "PRE") {
                $query .= "SUM(ls.pre_correct) as correct,SUM(ls.pre_played_questions) as played_questions,"
                    . "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions) as acc_order,"
                    . "if(SUM(ls.pre_played_questions) < SUM(ls.pre_total_questions),'Incompleted','Completed') as status,
                    (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time";
            } else {
                $query .= "SUM(ls.post_correct) as correct,SUM(ls.post_played_questions) as played_questions,"
                    . "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_played_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_played_questions) as acc_order,"
                    . "if(SUM(ls.post_played_questions) < SUM(ls.post_total_questions),'Incompleted','Completed') as status,
                    (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time";
            }
            $query .= " FROM trainee_result AS ls 
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
                LEFT JOIN region as tr ON tr.id=du.region_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id ";
            $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY ls.trainee_id order by acc_order desc,avg_time ASC,trainee_name ) as fs,(SELECT @curRank := 0) r  ";
        }
        $query .= ")as z ";

        $query .= " WHERE z.trainee_id=" . $trainee_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function WorkshopLive($workshop_id, $workshop_session)
    {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
            . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        $liveFlag = false;
        if ($workshop_session == "PRE") {
            if (strtotime($LiveSet->pre_date) >  strtotime($TodayDt)) {
                $liveFlag = true;
            }
        } else {
            if (strtotime($LiveSet->post_date) >  strtotime($TodayDt)) {
                $liveFlag = true;
            }
        }
        return $liveFlag;
    }
    // Trainee Dashboard i Functiobn end Here 








































    // Trainee_Comparition functions Start Here 
    // public function getTraineeData($company_id, $workshoptype_id, $trainee_id, $dtOrder, $dtLimit, $dtWhere2, $RightsFlag, $WRightsFlag)
    // {
    //     $dtWhere = "";
    //     $login_id = $this->mw_session['user_id'];
    //     if ($workshoptype_id != "0") {
    //         $dtWhere = " AND w.workshop_type  = " . $workshoptype_id;
    //     }
    //     $TodayDt = date('Y-m-d H:i:s');
    //     $query = "
    //             SELECT DATE_FORMAT(w.start_date,'%d-%m-%Y') AS start_date, w.workshop_name,ls.workshop_id, 
    //             FORMAT(SUM(ls.pre_correct)*100/sum(ls.pre_total_questions),2) as pre_average,
    //             IFNULL(FORMAT(SUM(ls.post_correct)*100/sum(ls.post_total_questions),2),'NP') as post_average,
    //             FORMAT(SUM(post_time_taken)/sum(ls.post_total_questions),2) as avg_time,count(distinct ar.topic_id) as total_topic  FROM (

    //             SELECT w.workshop_id,w.pre_correct,sum(w.pre_total_questions) as pre_total_questions,
    //             sum(w.post_correct) as post_correct,sum(w.post_total_questions) as post_total_questions,
    //             sum(w.pre_time_taken) as pre_time_taken,sum(w.post_time_taken) as post_time_taken
    //              FROM trainee_result as w 
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id 
    //             WHERE w.company_id=$company_id AND w.trainee_id =$trainee_id AND wtu.tester_id IS NULL $dtWhere ";
    //     if (!$WRightsFlag) {
    //         $query .= " AND w.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
    //     }
    //     if (!$RightsFlag) {
    //         $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= " group by w.workshop_id union all 
    //             SELECT arp.workshop_id ,SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,0 as post_correct,
    //             0 as post_total_questions,sum(arp.seconds) as pre_time_taken,0 as post_time_taken  FROM atom_results as arp
    //             INNER JOIN workshop AS w ON w.id=arp.workshop_id 
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id 
    //             where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='PRE' AND
    //             CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL $dtWhere ";
    //     if (!$WRightsFlag) {
    //         $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
    //     }
    //     if (!$RightsFlag) {
    //         $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= " group by arp.workshop_id union all 
    //             SELECT arp.workshop_id ,0 as pre_correct,0 as pre_total_questions,SUM(arp.is_correct) AS post_correct,
    //              COUNT(arp.question_id) AS post_total_questions,0 as pre_time_taken,sum(arp.seconds) as post_time_taken FROM atom_results as arp
    //             INNER JOIN workshop AS w ON w.id=arp.workshop_id 
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id 
    //             where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='POST' AND
    //             CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND wtu.tester_id IS NULL $dtWhere ";
    //     if (!$WRightsFlag) {
    //         $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
    //     }
    //     if (!$RightsFlag) {
    //         $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= " group by arp.workshop_id
    //             ) as ls
    //             LEFT JOIN atom_results ar ON ar.company_id=$company_id AND ar.user_id =$trainee_id AND ar.workshop_id=ls.workshop_id
    //             LEFT JOIN workshop AS w ON w.id=ls.workshop_id
    //             $dtWhere2
    //             group by ls.workshop_id $dtOrder $dtLimit  ";

    //     $result = $this->db->query($query);
    //     $data['ResultSet'] = $result->result_array();
    //     $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
    //     $query1 = "SELECT count(distinct a.workshop_id) AS total FROM atom_results AS a
    //         LEFT JOIN workshop AS w ON w.id=a.workshop_id
    //         LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id 
    //         $dtWhere2 ";
    //     if ($dtWhere2 != "") {
    //         $query1 .= " AND a.company_id=$company_id AND a.user_id =$trainee_id AND wtu.tester_id IS NULL";
    //     } else {
    //         $query1 .= " WHERE a.company_id=$company_id AND a.user_id =$trainee_id AND wtu.tester_id IS NULL";
    //     }
    //     $result1 = $this->db->query($query1);
    //     $data_array = $result1->row();
    //     $data['dtTotalRecords'] = $data_array->total;
    //     return $data;
    // }

    public function getPrePostData_tc($workshop_id, $trainee_id = '', $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT a.trainee_id,b.workshop_name, 
                IF(a.pre_played_questions=0,'Not Played', CONCAT(a.pre_avg,'%')) AS pre_average, 
                IF(a.post_played_questions=0,'Not Played', CONCAT(a.post_avg,'%')) AS post_average,a.post_avg,a.pre_avg, 
                format(a.post_avg-a.pre_avg,2) AS ce,FORMAT(a.avg_time,2) AS response_time,a.trainee_region,
                a.trainee_name ,@curRank := @curRank + 1 AS rank
                FROM (
                select a.trainee_id,a.workshop_id, FORMAT(sum(a.pre_correct)*100/sum(a.pre_total_questions),2) as pre_avg,
                FORMAT(sum(a.post_correct)*100/sum(a.post_total_questions),2) as post_avg,
                SUM(a.post_correct)*100/ SUM(a.post_total_questions) AS post_order,
                (SUM(a.pre_time_taken)+ SUM(a.post_time_taken))/ SUM(a.pre_total_questions)+ SUM(a.post_total_questions) AS avg_time,
                sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions,
                CONCAT(du.firstname,' ',du.lastname) AS trainee_name,tr.region_name AS trainee_region  FROM trainee_result AS a
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
                LEFT JOIN device_users AS du ON du.user_id=a.trainee_id
                LEFT JOIN region AS tr ON tr.id=du.region_id
                WHERE a.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND a.trainee_id =$trainee_id";
        } else {
            $query .= " AND wtu.tester_id IS NULL ";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $query .= " group by a.trainee_id) as a
                LEFT JOIN workshop AS b ON b.id=a.workshop_id
                ,(
                SELECT @curRank := 0) r
                ORDER BY post_order DESC,avg_time,trainee_name";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getLivePrePostData_tc($workshop_id = '', $trainee_id = '', $trainer_id = "0", $RightsFlag = 1)
    {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT fs.trainee_id,b.workshop_name,fs.trainee_name,FORMAT(fs.avg_time,2) AS response_time,fs.trainee_region, IF(fs.pre_avg is null,'Not Played',CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg is null,'Not Played',CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,fs.pre_avg, FORMAT(fs.post_avg-fs.pre_avg,2) AS ce
        ,@curRank := @curRank + 1 AS rank
        FROM (
        SELECT ls.trainee_id,ls.workshop_id, FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_avg, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_avg, SUM(ls.post_correct) AS post_correct, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time,tr.region_name AS trainee_region
        FROM (
        SELECT es.trainee_id,es.workshop_id,sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions,sum(es.pre_time_taken) AS total_time
        FROM trainee_result AS es
        WHERE es.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND es.trainee_id =$trainee_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY es.trainee_id UNION ALL
        SELECT arp.user_id AS trainee_id,arp.workshop_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,
         0 AS post_correct, 0 AS post_total_questions, SUM(arp.seconds) AS total_time
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' "
            . "AND arp.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND arp.user_id =$trainee_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id UNION ALL
        SELECT arp.user_id AS trainee_id,arp.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions, SUM(arp.seconds) AS total_time
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' "
            . "AND arp.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND arp.user_id =$trainee_id";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id) AS ls 
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
        LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
        LEFT JOIN region AS tr ON tr.id=du.region_id
        where wtu.tester_id IS NULL GROUP BY ls.trainee_id
        ORDER BY post_correct DESC,avg_time,trainee_name
        ) AS fs
        LEFT JOIN workshop AS b ON b.id=fs.workshop_id,(SELECT @curRank := 0) r";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getPrePostWorkshopwise_tc($workshop_id, $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $PreFlag = false;
        $query = "select distinct workshop_id from atom_results where workshop_id=$workshop_id AND workshop_session='PRE' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PreFlag = true;
        }

        $query = "SELECT b.workshop_name,SUM(pre_correct),SUM(pre_total_questions), FORMAT((SUM(pre_correct)*100/ SUM(pre_total_questions)),2) AS pre_average,
           FORMAT((SUM(post_correct)*100/ SUM(post_total_questions)),2) AS post_average
           FROM trainee_result AS a LEFT JOIN workshop AS b ON b.id=a.workshop_id
           LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
           WHERE a.workshop_id= $workshop_id and wtu.tester_id IS NULL ";
        if ($PreFlag) {
            $query .= " AND a.ce_eligible=1 ";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $result = $this->db->query($query);
        return $result->row();
    }

    public function getLivePrePostWorkshopwise_tc($workshop_id, $trainer_id = "0", $RightsFlag = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $PostFlag = false;
        $PreFlag = false;
        $query = "select distinct workshop_id from atom_results where workshop_id=$workshop_id AND workshop_session='POST' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PostFlag = true;
        }
        $query = "select distinct workshop_id from atom_results where workshop_id=$workshop_id AND workshop_session='PRE' limit 0,1";
        $ObjSet = $this->db->query($query);
        if (count((array)$ObjSet->row()) > 0) {
            $PreFlag = true;
        }
        $TodayDt = date('Y-m-d H:i');
        $query = " SELECT ls.workshop_id,b.workshop_name, 
            FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, 
            FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average
        FROM (
        SELECT es.workshop_id,sum(es.pre_correct) as pre_correct ,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions
        FROM trainee_result AS es 
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=es.workshop_id AND wtu.tester_id=es.trainee_id
        WHERE es.workshop_id= $workshop_id AND wtu.tester_id IS NULL ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        if ($PostFlag) {
            $query .= " AND es.trainee_id IN 
            (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='POST')";
        }
        $query .= " GROUP BY es.workshop_id UNION ALL
        SELECT arp.workshop_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
        WHERE wtu.tester_id IS NULL AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.workshop_id= $workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        if ($PostFlag) {
            $query .= " AND arp.user_id IN (SELECT distinct user_id FROM atom_results"
                . " WHERE workshop_id=$workshop_id AND workshop_session='POST')";
        }
        $query .= " GROUP BY arp.workshop_id UNION ALL
        SELECT arp.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id 
        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
        WHERE wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.workshop_id= $workshop_id";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        if ($PreFlag) {
            $query .= " AND arp.user_id IN (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='PRE')";
        }
        $query .= " GROUP BY arp.workshop_id
        ) AS ls
        LEFT JOIN workshop AS b ON b.id=ls.workshop_id";
        $result = $this->db->query($query);
        return $result->row();
    }

    // public function get_PrePostTopicwise($workshop_id = '', $trainee_id = '', $RightsFlag = 1)
    // {
    //     $login_id = $this->mw_session['user_id'];
    //     $query = $query = "
    //             SELECT qt.description AS topic,qst.description AS subtopic,
    //             FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average,
    //             FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2) AS ce,
    //             if(sum(ls.pre_total_questions)>0,sum(ls.pre_status),1) as pre_status,if(sum(ls.post_total_questions)>0,sum(ls.post_status),1) as post_status
    //             FROM trainee_result AS ls ";
    //     $query .= "
    //         INNER JOIN question_topic qt ON qt.id=ls.topic_id
    //         LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
    //         WHERE  ls.workshop_id=$workshop_id and ls.trainee_id=$trainee_id ";
    //     if (!$RightsFlag) {
    //         $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= " GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";

    //     //        echo $query;
    //     //        exit;
    //     $result = $this->db->query($query);
    //     return $result->result();
    // }

    // public function get_LivePrePostTopicwise($workshop_id = '', $trainee_id = '', $RightsFlag = 1)
    // {
    //     $TodayDt = date('Y-m-d H:i');
    //     $login_id = $this->mw_session['user_id'];
    //     $query = $query = "
    //             SELECT qt.description AS topic,qst.description AS subtopic,
    //             FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average,
    //             FORMAT((SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)),2) AS ce,
    //             if(sum(ls.pre_total_questions)>0,sum(ls.pre_status),1) as pre_status,if(sum(ls.post_total_questions)>0,sum(ls.post_status),1) as post_status
    //             FROM (
    //             SELECT w.topic_id,w.subtopic_id,sum(w.pre_correct) as pre_correct ,sum(w.pre_total_questions) as pre_total_questions ,0 as post_correct,
    //             0 as post_total_questions, w.pre_status, 0 as post_status
    //             FROM trainee_result AS w
    //             WHERE w.workshop_id=$workshop_id and w.trainee_id= $trainee_id ";
    //     if (!$RightsFlag) {
    //         $query .= " AND (w.trainer_id = $login_id OR w.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= " GROUP BY w.topic_id,w.subtopic_id UNION ALL    
    //             SELECT arp.topic_id,arp.subtopic_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,
    //             0 AS post_correct, 0 AS post_total_questions, 0 as pre_status, 0 as post_status
    //             FROM atom_results AS arp
    //             INNER JOIN workshop AS w ON w.id=arp.workshop_id
    //             WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id and arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'";
    //     if (!$RightsFlag) {
    //         $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= "
    //             GROUP BY arp.topic_id,arp.subtopic_id UNION ALL
    //             SELECT arp.topic_id,arp.subtopic_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions,
    //             0 as pre_status, 0 as post_status
    //             FROM atom_results AS arp
    //             INNER JOIN workshop AS w ON w.id=arp.workshop_id 
    //             WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'";
    //     if (!$RightsFlag) {
    //         $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //     }
    //     $query .= " GROUP BY arp.topic_id,arp.subtopic_id
    //             ) as ls ";
    //     $query .= "
    //         INNER JOIN question_topic qt ON qt.id=ls.topic_id
    //         LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
    //         GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";
    //     $result = $this->db->query($query);
    //     return $result->result();
    // }

    // public function getPrePostQuestionAnsData($workshop_id = '', $trainee_id = '', $trainer_id = "0", $RightsFlag = 1)
    // {
    //     $login_id = $this->mw_session['user_id'];
    //     $query = "SELECT sum(pre_correct) as pre_correct ,sum(pre_total_questions) as pre_total_questions ,"
    //         . "sum(post_correct) as post_correct,sum(post_total_questions) as post_total_questions,"
    //         . "sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions  FROM trainee_result"
    //         . " WHERE workshop_id=$workshop_id AND trainee_id=$trainee_id";
    //     if ($trainer_id == "0") {
    //         if (!$RightsFlag) {
    //             $query .= " AND (trainer_id = $login_id OR trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //         }
    //     } else {
    //         $query .= " AND trainer_id= " . $trainer_id;
    //     }
    //     $result = $this->db->query($query);
    //     return $result->row();
    // }

    // public function getLivePrePostQuestionAnsData($workshop_id, $trainee_id, $trainer_id = "0", $RightsFlag = 1)
    // {
    //     $login_id = $this->mw_session['user_id'];
    //     $TodayDt = date('Y-m-d H:i:s');
    //     $query = " SELECT SUM(ls.pre_correct) as pre_correct, SUM(ls.pre_total_questions)  AS pre_total_questions,
    //         SUM(ls.post_correct) as post_correct, SUM(ls.post_total_questions) as post_total_questions,
    //         sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions
    //         FROM (
    //         SELECT sum(es.pre_correct) as pre_correct,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions,
    //         sum(es.pre_played_questions) as pre_played_questions,0 as post_played_questions
    //         FROM trainee_result AS es
    //         WHERE es.workshop_id= $workshop_id AND es.trainee_id =$trainee_id";
    //     if ($trainer_id == "0") {
    //         if (!$RightsFlag) {
    //             $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //         }
    //     } else {
    //         $query .= " AND es.trainer_id= " . $trainer_id;
    //     }
    //     $query .= "    
    //         UNION ALL
    //         SELECT SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 
    //          0 AS post_correct, 0 AS post_total_questions,COUNT(arp.question_id) as pre_played_questions,0 as post_played_questions
    //         FROM atom_results AS arp
    //         INNER JOIN workshop AS w ON w.id=arp.workshop_id
    //         WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND "
    //         . "arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id ";
    //     if ($trainer_id != "0") {
    //         $query .= " AND arp.trainer_id=" . $trainer_id;
    //     }
    //     $query .= " UNION ALL
    //         SELECT 0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct,
    //         COUNT(arp.question_id) AS post_total_questions,0 as pre_played_questions,COUNT(arp.question_id) as post_played_questions
    //         FROM atom_results AS arp
    //         INNER JOIN workshop AS w ON w.id=arp.workshop_id
    //         WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' "
    //         . "AND arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id ";
    //     if ($trainer_id != "0") {
    //         $query .= " AND arp.trainer_id=" . $trainer_id;
    //     }
    //     $query .= ") AS ls";
    //     $result = $this->db->query($query);
    //     return $result->row();
    // }

    // public function get_WorkshopRegisterdusers($workshop_id, $Company_id = "")
    // {
    //     $querystr = "Select distinct(wru.user_id) as user_id,concat(du.firstname,' ',du.lastname) as username "
    //         . " from workshop_registered_users wru "
    //         . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wru.workshop_id AND wtu.tester_id=wru.user_id "
    //         . " inner join device_users du on du.user_id=wru.user_id where wtu.tester_id IS NULL AND wru.workshop_id=" . $workshop_id;
    //     if ($Company_id != "") {
    //         $querystr .= " AND wru.company_id=" . $Company_id;
    //     }
    //     $querystr .= " order by username ";
    //     $result = $this->db->query($querystr);
    //     return $result->result();
    // }

    // public function get_PrepostAccuracy($workshop_id = '', $trainee_id = '', $workshop_session = "PRE")
    // {
    //     $TodayDt = date('Y-m-d H:i:s');
    //     $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
    //         . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
    //     $ObjSet = $this->db->query($query);
    //     $LiveSet = $ObjSet->row();
    //     $liveFlag = false;
    //     if ($workshop_session == "PRE") {
    //         if (strtotime($LiveSet->pre_date) > strtotime($TodayDt)) {
    //             $liveFlag = true;
    //         }
    //     } else {
    //         if (strtotime($LiveSet->post_date) > strtotime($TodayDt)) {
    //             $liveFlag = true;
    //         }
    //     }
    //     if ($liveFlag) {
    //         $query = "SELECT qt.description AS topic,qst.description AS subtopic,
    //             (SUM(arp.is_correct)*100/COUNT(arp.question_id)) AS accuracy
    //             FROM atom_results AS arp
    //             INNER JOIN question_topic qt ON qt.id=arp.topic_id
    //             LEFT JOIN question_subtopic qst ON qst.id=arp.subtopic_id
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
    //             WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id and"
    //             . " arp.workshop_session='$workshop_session' AND wtu.tester_id IS NULL "
    //             . "GROUP BY arp.topic_id,arp.subtopic_id order by arp.topic_id  ";
    //     } else {
    //         $query = "SELECT qt.description AS topic,qst.description AS subtopic,";
    //         if ($workshop_session == "PRE") {
    //             $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
    //         } else {
    //             $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
    //         }
    //         $query .= " FROM trainee_result AS ls 
    //             INNER JOIN question_topic qt ON qt.id=ls.topic_id
    //             LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
    //             WHERE  ls.workshop_id=$workshop_id and ls.trainee_id=$trainee_id AND wtu.tester_id IS NULL
    //             GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";
    //     }
    //     $result = $this->db->query($query);
    //     return $result->result();
    // }

    public function isWorkshopLive_tc($workshop_id)
    {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select if(end_date >='$TodayDt',1,0) as live_workshop FROM workshop where id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        return $LiveSet->live_workshop;
    }

    public function get_Traineewise_Rank_tc($workshop_id = '', $user_id = '', $islive_workshop = "")
    {
        $TasterFlag = true;
        if ($islive_workshop == "") {
            $islive_workshop = $this->isWorkshopLive_tc($workshop_id);
        }
        if ($user_id != "" && $workshop_id != "") {
            $query = "select tester_id FROM workshop_tester_users where workshop_id = $workshop_id AND tester_id =" . $user_id;
            $ObjSet = $this->db->query($query);
            if (count((array)$ObjSet->row()) > 0) {
                $TasterFlag = false;
            }
        }
        $query = "select distinct workshop_id FROM atom_results where workshop_session='POST' AND workshop_id=" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        if ($islive_workshop) {

            //            echo "<pre>";
            //            print_r($LiveSet);
            //            exit;
            $TodayDt = date('Y-m-d H:i');
            if (count((array)$LiveSet) > 0) {
                $LcSqlStr = " SELECT z.* FROM (SELECT fs.*,@curRank := @curRank + 1 AS rank FROM(
                    SELECT arp.user_id as trainee_id ,sum(arp.is_correct) as post_correct, FORMAT(SUM(arp.is_correct)*100/ count(arp.question_id),2) AS post_avg, 
                    SUM(arp.seconds)/count(arp.question_id) AS avg_time,CONCAT(du.firstname,' ',du.lastname) AS trainee_name
                    FROM atom_results AS arp
                    INNER JOIN workshop AS w ON w.id=arp.workshop_id
                    LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                    WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' 
                    AND arp.workshop_id=$workshop_id ";
                if ($TasterFlag) {
                    $LcSqlStr .= " and wtu.tester_id IS NULL ";
                }
                $LcSqlStr .= " GROUP BY arp.user_id ORDER BY post_correct DESC,avg_time,trainee_name
                    ) as fs ,(SELECT @curRank := 0) r) as z ";
            } else {
                $LcSqlStr = " SELECT z.* FROM (SELECT fs.*,@curRank := @curRank + 1 AS rank FROM(
                        SELECT arp.user_id as trainee_id,sum(arp.is_correct) as pre_correct, FORMAT(SUM(arp.is_correct)*100/ count(arp.question_id),2) AS pre_avg, 
                        SUM(arp.seconds)/count(arp.question_id) AS avg_time,CONCAT(du.firstname,' ',du.lastname) AS trainee_name
                        FROM atom_results AS arp
                        INNER JOIN workshop AS w ON w.id=arp.workshop_id
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                        LEFT JOIN device_users AS du ON du.user_id=arp.user_id  WHERE arp.workshop_session='PRE' AND
                        CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND
                        arp.workshop_id=$workshop_id ";
                if ($TasterFlag) {
                    $LcSqlStr .= " and wtu.tester_id IS NULL ";
                }
                $LcSqlStr .= " GROUP BY arp.user_id
                        ORDER BY pre_correct DESC,avg_time,trainee_name
                        ) as fs ,(SELECT @curRank := 0) r) as z  ";
            }
        } else {
            $LcSqlStr = "SELECT z.* FROM(
                SELECT ls.*,@curRank := @curRank + 1 AS rank
                FROM (
                SELECT a.trainee_id,a.workshop_id, (SUM(a.post_correct)*100/ SUM(a.post_total_questions)) AS post_avg,
                (SUM(a.pre_correct)*100/ SUM(a.pre_total_questions)) AS pre_avg,
                (sum(a.pre_time_taken) +sum(a.post_time_taken))/(sum(a.pre_played_questions)+sum(a.post_played_questions)) as avg_time,
                 CONCAT(du.firstname,' ',du.lastname) AS trainee FROM trainee_result as a
                LEFT JOIN device_users as du ON du.user_id=a.trainee_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id
                where a.workshop_id=$workshop_id ";
            if ($TasterFlag) {
                $LcSqlStr .= " and wtu.tester_id IS NULL ";
            }
            if (count((array)$LiveSet) > 0) {
                $LcSqlStr .= " group by a.trainee_id order by post_avg desc,avg_time,trainee";
            } else {
                $LcSqlStr .= " group by a.trainee_id order by pre_avg desc,avg_time,trainee";
            }
            $LcSqlStr .= ") AS ls
                ,(SELECT @curRank := 0) r) as z  ";
        }
        if ($user_id != "") {
            $LcSqlStr .= " where z.trainee_id=" . $user_id;
        }
        //        echo $LcSqlStr;exit;
        $query = $this->db->query($LcSqlStr);
        return $query->result();
    }

    public function SynchTraineeData_tc($Company_id = "", $Workshop_id = "")
    {
        if ($Company_id == "") {
            return false;
        }
        $CurrentTime = date('Y-m-d H:i');
        // Delete Pre Data set only
        $query = "SELECT distinct a.workshop_id,CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) as post_enddate "
            . "FROM trainee_result as a INNER JOIN workshop as b ON b.id=a.workshop_id "
            . "where a.company_id= $Company_id AND a.liveflag=1 "
            . " AND CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) <= '$CurrentTime'";
        if ($Workshop_id != "") {
            $query .= " AND a.workshop_id=" . $Workshop_id;
        }

        $ObjSet = $this->db->query($query);
        $Del_WorkshopSet = $ObjSet->result();
        if (count((array)$Del_WorkshopSet) > 0) {
            foreach ($Del_WorkshopSet as $value) {
                $tWorkshop_id = $value->workshop_id;
                $lcSqlStr = "delete from trainee_result where workshop_id=" . $tWorkshop_id;
                $this->db->query($lcSqlStr);
            }
        }
        //exit;
        $query = "SELECT distinct a.workshop_id,date(b.start_date) as start_date ,b.workshop_type,b.region,"
            . "CONCAT(b.pre_end_date,' ', STR_TO_DATE(b.pre_end_time, '%l:%i %p')) as pre_enddate,"
            . "CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) as post_enddate FROM atom_results as a LEFT JOIN "
            . " workshop as b ON b.id=a.workshop_id where a.company_id= $Company_id "
            . "AND a.workshop_id NOT IN(select distinct workshop_id FROM trainee_result where company_id= $Company_id)"
            . " AND (CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) <= '$CurrentTime' OR CONCAT(b.pre_end_date,' ', STR_TO_DATE(b.pre_end_time, '%l:%i %p')) <= '$CurrentTime')";
        if ($Workshop_id != "") {
            $query .= " AND a.workshop_id=" . $Workshop_id;
        }
        $result = $this->db->query($query);
        $WorkshopSet = $result->result();
        if (count((array)$WorkshopSet) > 0) {
            foreach ($WorkshopSet as $value) {
                $Workshop_id = $value->workshop_id;
                $Pre_endDate = $value->pre_enddate;
                $Post_endDate = $value->post_enddate;
                $tPostPlayed = $this->common_model->get_value('atom_results', 'id', " workshop_session='post' AND workshop_id=" . $Workshop_id);
                if ($Post_endDate != '1970-01-01 00:00:00' && strtotime($Post_endDate) <= strtotime($CurrentTime) && count((array)$tPostPlayed) > 0) {
                    $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainer_id,trainee_id,topic_id,subtopic_id,workshop_date,pre_correct,pre_played_questions, pre_total_questions,pre_avg,post_correct, post_played_questions,post_total_questions,post_avg,avg_time,workshop_type,region_id,pre_time_taken,
                        post_time_taken,pre_status,post_status,istester,liveflag)
                    SELECT $Company_id AS company_id,prpo.workshop_id,prpo.trainer_id,prpo.user_id,prpo.topic_id,prpo.subtopic_id,'" . $value->start_date . "' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton, SUM(prpo.pre_total_questions) AS pre_total_questions,
                    FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
                     SUM(prpo.post_correct) AS post_correct, SUM(prpo.post_played_quesiton) AS post_played_quesiton,
                      SUM(prpo.post_total_questions) AS post_total_questions, 
                      FORMAT((SUM(prpo.post_correct)*100)/ SUM(prpo.post_total_questions),2) AS post_average,
                    FORMAT(((SUM(prpo.pre_time_taken)+ SUM(prpo.post_time_taken))/ (SUM(prpo.pre_played_quesiton)+ SUM(prpo.post_played_quesiton))),2) AS avgtime, '" . $value->workshop_type . "' AS workshop_type,'" . $value->region . "' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken,
                     SUM(prpo.post_time_taken) AS post_time_taken,sum(prpo.pre_status) as pre_status,sum(prpo.post_status) as post_status, (wtu.tester_id IS NOT NULL) istester,0 AS liveflag
                    FROM(
                    SELECT a.user_id,a.workshop_id, b.pre_correct,a.total_questions AS pre_total_questions, 0 AS post_correct,0 AS post_total_questions,b.total_seconds_taken AS pre_time_taken, 0 AS post_time_taken,
                     b.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton, a.trainer_id,a.topic_id,a.subtopic_id,
                     if(b.pre_correct is null,1,0) as pre_status, 0 as post_status FROM
                    (
                    select a.workshop_id,a.user_id,b.trainer_id,b.topic_id,b.subtopic_id,b.total_questions from atom_results as a LEFT JOIN(
                            SELECT wq.workshop_id,COUNT(DISTINCT wq.question_id) AS total_questions, wq.trainer_id,wq.topic_id,wq.subtopic_id
                            FROM workshop_questions AS wq
                            INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                            WHERE wq.workshop_id = $Workshop_id
                            GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id,wq.topic_id,wq.subtopic_id
                            )as b ON b.workshop_id=a.workshop_id
                            where a.workshop_id=$Workshop_id and a.workshop_session='PRE'
                            group by a.user_id,b.trainer_id,b.topic_id,b.subtopic_id
                            order by user_id,b.subtopic_id
                    ) as a LEFT JOIN
                    (
                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session,SUM(arp.is_correct) AS pre_correct, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton,
                    arp.trainer_id,arp.topic_id,arp.subtopic_id
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id,arp.topic_id,arp.subtopic_id
                    ) as b  ON a.user_id=b.user_id AND a.workshop_id = b.workshop_id AND a.trainer_id=b.trainer_id AND a.topic_id=b.topic_id 
                    AND a.subtopic_id=b.subtopic_id
                    union all
                    SELECT c.user_id,c.workshop_id, 0 pre_correct,0 AS pre_total_questions, d.post_correct AS post_correct,
                    c.total_questions AS post_total_questions,0 AS pre_time_taken, d.total_seconds_taken AS post_time_taken,
                     0 AS pre_played_quesiton,d.played_quesiton AS post_played_quesiton, c.trainer_id,c.topic_id,c.subtopic_id,
                    0 as pre_status, if(d.post_correct is null,1,0) as post_status FROM
                    (
                    select a.workshop_id,a.user_id,b.trainer_id,b.topic_id,b.subtopic_id,b.total_questions from atom_results as a LEFT JOIN(
                            SELECT wq.workshop_id,COUNT(DISTINCT wq.question_id) AS total_questions, wq.trainer_id,wq.topic_id,wq.subtopic_id
                            FROM workshop_questions AS wq
                            INNER JOIN workshop_questionset_post AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                            WHERE wq.workshop_id = $Workshop_id
                            GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id,wq.topic_id,wq.subtopic_id
                            )as b ON b.workshop_id=a.workshop_id
                            where a.workshop_id=$Workshop_id and a.workshop_session='POST'
                            group by a.user_id,b.trainer_id,b.topic_id,b.subtopic_id
                            order by user_id,b.subtopic_id
                    ) as c LEFT JOIN
                    (
                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session,SUM(arp.is_correct) AS post_correct, SUM(arp.seconds) AS total_seconds_taken, 
                    COUNT(arp.question_id) AS played_quesiton,
                    arp.trainer_id,arp.topic_id,arp.subtopic_id
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'POST'
                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id,arp.topic_id,arp.subtopic_id
                    ) as d  ON c.user_id=d.user_id AND c.workshop_id = d.workshop_id AND c.trainer_id=d.trainer_id AND c.topic_id=d.topic_id 
                    AND c.subtopic_id=d.subtopic_id
                    ) AS prpo
                    LEFT JOIN device_users AS du ON du.user_id=prpo.user_id
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=prpo.workshop_id AND wtu.tester_id=prpo.user_id  
                    GROUP BY prpo.user_id,prpo.workshop_id,prpo.trainer_id,prpo.topic_id,prpo.subtopic_id
                    ORDER BY post_average DESC,avgtime ASC, CONCAT(du.firstname,' ',du.lastname)
                    ";
                    $this->db->query($lcSqlStr);
                    //CE Eligible
                    $query = "update trainee_result as a LEFT join 
                        (select tr.trainee_id,tr.workshop_id,if(SUM(tr.pre_played_questions) >0 && SUM(tr.post_played_questions)>0 ,1,0) as ce_eligible FROM trainee_result as tr
                        where tr.workshop_id=$Workshop_id group by tr.trainee_id
                        ) as b ON a.workshop_id=b.workshop_id and a.trainee_id=b.trainee_id
                        set a.ce_eligible=b.ce_eligible
                        where a.workshop_id=$Workshop_id";
                    $this->db->query($query);
                } else if ($Pre_endDate != '1970-01-01 00:00:00' && strtotime($Pre_endDate) <= strtotime($CurrentTime)) {
                    $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainer_id,trainee_id,topic_id,subtopic_id,workshop_date,pre_correct,pre_played_questions, pre_total_questions,pre_avg,post_correct, post_played_questions,post_total_questions,post_avg,avg_time,workshop_type,region_id,pre_time_taken,
                        post_time_taken,pre_status,post_status,istester,liveflag)
                    SELECT $Company_id AS company_id,prpo.workshop_id,prpo.trainer_id,prpo.user_id,prpo.topic_id,prpo.subtopic_id,'" . $value->start_date . "' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton, SUM(prpo.pre_total_questions) AS pre_total_questions,
                    FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
                     SUM(prpo.post_correct) AS post_correct, SUM(prpo.post_played_quesiton) AS post_played_quesiton,
                      SUM(prpo.post_total_questions) AS post_total_questions, 
                      FORMAT((SUM(prpo.post_correct)*100)/ SUM(prpo.post_total_questions),2) AS post_average,
                    FORMAT(((SUM(prpo.pre_time_taken)+ SUM(prpo.post_time_taken))/ (SUM(prpo.pre_played_quesiton)+ SUM(prpo.post_played_quesiton))),2) AS avgtime, '" . $value->workshop_type . "' AS workshop_type,'" . $value->region . "' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken,
                     SUM(prpo.post_time_taken) AS post_time_taken,sum(prpo.pre_status) as pre_status,sum(prpo.post_status) as post_status, (wtu.tester_id IS NOT NULL) as istester,1 AS liveflag
                    FROM(
                    SELECT a.user_id,a.workshop_id, b.pre_correct,a.total_questions AS pre_total_questions, 0 AS post_correct,0 AS post_total_questions,b.total_seconds_taken AS pre_time_taken, 0 AS post_time_taken,
                     b.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton, a.trainer_id,a.topic_id,a.subtopic_id,
                     if(b.pre_correct is null,1,0) as pre_status, 0 as post_status FROM
                    (
                    select a.workshop_id,a.user_id,b.trainer_id,b.topic_id,b.subtopic_id,b.total_questions from atom_results as a LEFT JOIN(
                            SELECT wq.workshop_id,COUNT(DISTINCT wq.question_id) AS total_questions, wq.trainer_id,wq.topic_id,wq.subtopic_id
                            FROM workshop_questions AS wq
                            INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                            WHERE wq.workshop_id = $Workshop_id
                            GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id,wq.topic_id,wq.subtopic_id
                            )as b ON b.workshop_id=a.workshop_id
                            where a.workshop_id=$Workshop_id and a.workshop_session='PRE'
                            group by a.user_id,b.trainer_id,b.topic_id,b.subtopic_id
                            order by user_id,b.subtopic_id
                    ) as a LEFT JOIN
                    (
                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session,SUM(arp.is_correct) AS pre_correct, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton,
                    arp.trainer_id,arp.topic_id,arp.subtopic_id
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id,arp.topic_id,arp.subtopic_id
                    ) as b  ON a.user_id=b.user_id AND a.workshop_id = b.workshop_id AND a.trainer_id=b.trainer_id AND a.topic_id=b.topic_id 
                    AND a.subtopic_id=b.subtopic_id) AS prpo
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=prpo.workshop_id AND wtu.tester_id=prpo.user_id  
                    LEFT JOIN device_users AS du ON du.user_id=prpo.user_id
                    GROUP BY prpo.user_id,prpo.workshop_id,prpo.trainer_id,prpo.topic_id,prpo.subtopic_id
                    ORDER BY post_average DESC,avgtime ASC, CONCAT(du.firstname,' ',du.lastname)";
                    // echo $lcSqlStr.'<br/>';
                    $this->db->query($lcSqlStr);
                }
            }
        }
    }
    // public function get_traineeAccuracy($RightsFlag, $trainee_id, $trainer_id = "0", $workshop_id, $workshop_session, $liveFlag)
    // {

    //     $login_id  = $this->mw_session['user_id'];

    //     $query = " SELECT z.* FROM (";
    //     if ($liveFlag) {
    //         $query .= " SELECT b.*,IF(wr.all_questions_fired=1,'Completed','Playing') AS status ,@curRank := @curRank + 1 AS rank 
    //                     FROM
    //                     (SELECT arp.workshop_id,arp.user_id AS trainee_id,arp.workshop_session, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
    //                      FORMAT(SUM(arp.is_correct)*100/ COUNT(arp.question_id),2) AS accuracy, SUM(arp.is_correct)*100/ COUNT(arp.question_id) AS acc_order,
    //                       COUNT(arp.question_id) AS played_questions, SUM(arp.is_correct) AS correct,
    //                       (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time,tr.region_name as trainee_region
    //                     FROM atom_results AS arp
    //                     LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id  
    //                     LEFT JOIN device_users AS du ON du.user_id=arp.user_id
    //                     LEFT JOIN region as tr ON tr.id=du.region_id,(SELECT @curRank := 0) r
    //                     where arp.workshop_id=$workshop_id AND wtu.tester_id IS NULL AND arp.workshop_session='$workshop_session'  ";
    //         if ($trainer_id == "0") {
    //             if (!$RightsFlag) {
    //                 $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //             }
    //         } else {
    //             $query .= " AND arp.trainer_id= " . $trainer_id;
    //         }
    //         $query .= " GROUP BY arp.user_id order by workshop_id,trainee_id,workshop_session) as b LEFT JOIN workshop_registered_users wr
    //                      ON wr.workshop_id=b.workshop_id AND wr.user_id=b.trainee_id AND wr.workshop_session=b.workshop_session
    //                      order by acc_order DESC,avg_time ASC,trainee_name ";
    //     } else {
    //         $query .= "SELECT fs.*,@curRank := @curRank + 1 AS rank FROM("
    //             . "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,tr.region_name as trainee_region,";
    //         if ($workshop_session == "PRE") {
    //             $query .= "SUM(ls.pre_correct) as correct,SUM(ls.pre_played_questions) as played_questions,"
    //                 . "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions) as acc_order,"
    //                 . "if(SUM(ls.pre_played_questions) < SUM(ls.pre_total_questions),'Incompleted','Completed') as status,
    //                 (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time";
    //         } else {
    //             $query .= "SUM(ls.post_correct) as correct,SUM(ls.post_played_questions) as played_questions,"
    //                 . "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_played_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_played_questions) as acc_order,"
    //                 . "if(SUM(ls.post_played_questions) < SUM(ls.post_total_questions),'Incompleted','Completed') as status,
    //                 (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time";
    //         }
    //         $query .= " FROM trainee_result AS ls 
    //             LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
    //             LEFT JOIN region as tr ON tr.id=du.region_id
    //             LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id ";
    //         $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
    //         if ($trainer_id == "0") {
    //             if (!$RightsFlag) {
    //                 $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
    //             }
    //         } else {
    //             $query .= " AND ls.trainer_id= " . $trainer_id;
    //         }
    //         $query .= " GROUP BY ls.trainee_id order by acc_order desc,avg_time ASC,trainee_name ) as fs,(SELECT @curRank := 0) r  ";
    //     }
    //     $query .= ")as z ";

    //     $query .= " WHERE z.trainee_id=" . $trainee_id;
    //     //            echo $query;exit

    //     $result = $this->db->query($query);
    //     return $result->result();
    // }
    // public function WorkshopLive($workshop_id, $workshop_session)
    // {
    //     $TodayDt = date('Y-m-d H:i:s');
    //     $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
    //         . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
    //     $ObjSet = $this->db->query($query);
    //     $LiveSet = $ObjSet->row();
    //     $liveFlag = false;
    //     if ($workshop_session == "PRE") {
    //         if (strtotime($LiveSet->pre_date) >  strtotime($TodayDt)) {
    //             $liveFlag = true;
    //         }
    //     } else {
    //         if (strtotime($LiveSet->post_date) >  strtotime($TodayDt)) {
    //             $liveFlag = true;
    //         }
    //     }
    //     return $liveFlag;
    // }
    // public function get_TraineeRegionData($company_id = '')
    // {
    //     $lcSqlStr = "select du.region_id,r.region_name,r.id FROM device_users du "
    //         . " LEFT JOIN region as r "
    //         . " ON du.region_id = r.id where 1=1";
    //     if ($company_id != "") {
    //         $lcSqlStr .= " AND du.company_id=" . $company_id;
    //     }

    //     $lcSqlStr .= " group by r.id ";
    //     $result = $this->db->query($lcSqlStr);
    //     return $result->result();
    // }
    // Trainee_Comparition Functions End Here 



    // Trainee Accuracy Start Here 
    public function get_PrepostAccuracy_ta($workshop_id = '', $trainee_id = '', $workshop_session = "PRE")
    {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
            . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        $liveFlag = false;
        if ($workshop_session == "PRE") {
            if (strtotime($LiveSet->pre_date) > strtotime($TodayDt)) {
                $liveFlag = true;
            }
        } else {
            if (strtotime($LiveSet->post_date) > strtotime($TodayDt)) {
                $liveFlag = true;
            }
        }
        if ($liveFlag) {
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,
                (SUM(arp.is_correct)*100/COUNT(arp.question_id)) AS accuracy
                FROM atom_results AS arp
                INNER JOIN question_topic qt ON qt.id=arp.topic_id
                LEFT JOIN question_subtopic qst ON qst.id=arp.subtopic_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.user_id=$trainee_id AND arp.workshop_id=$workshop_id and"
                . " arp.workshop_session='$workshop_session' AND wtu.tester_id IS NULL "
                . "GROUP BY arp.topic_id,arp.subtopic_id order by arp.topic_id  ";
        } else {
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,";
            if ($workshop_session == "PRE") {
                $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
            } else {
                $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
            }
            $query .= " FROM trainee_result AS ls 
                INNER JOIN question_topic qt ON qt.id=ls.topic_id
                LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
                WHERE  ls.workshop_id=$workshop_id and ls.trainee_id=$trainee_id AND wtu.tester_id IS NULL
                GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    // Trainee Accuracy end here 




























    // Workshop Reports Functions Start here (Tab 3)
    public function Tpr_LoadDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = " select ar.id,c.company_name,w.workshop_name,rg.region_name,rgn.region_name as tregion_name,
                    ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,concat('(',ar.question_id,') ',q.question_title) as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,du.user_id,
                    srg.description as sub_region,wst.description as workshop_subtype,wt.workshop_type,dt.description as designation,
                    CASE ar.correct_answer
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as correct_answer,
                    CASE ar.option_clicked
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as user_answer,ar.is_correct,
                    CASE 1
                                        WHEN ar.is_correct THEN 'Correct'
                                        WHEN ar.is_wrong THEN 'Wrong'
                                        WHEN ar.is_timeout THEN 'Time Out'                   
                                        ELSE ''
                                        END as question_result,                    
                    DATE_FORMAT(ar.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(ar.end_dttm, '%d/%m/%Y %H:%i:%s') as end_dttm,
                    ar.seconds,ar.timer from atom_results ar
                    LEFT join company c on c.id=ar.company_id
                    LEFT join workshop w on w.id=ar.workshop_id
                    LEFT join question_set qs on qs.id=ar.questionset_id
                    LEFT join company_users cu on cu.userid=ar.trainer_id
                    LEFT join question_topic qt on qt.id=ar.topic_id
                    LEFT join question_subtopic qst on qst.id=ar.subtopic_id
                    LEFT join questions q on q.id=ar.question_id
                    LEFT join device_users du on du.user_id=ar.user_id
                    LEFT join region rgn on rgn.id=du.region_id
                    LEFT join region rg on rg.id=w.region
                    LEFT join workshoptype_mst wt on wt.id=w.workshop_type
                    LEFT join workshopsubregion_mst srg on srg.id=w.workshopsubregion_id
                    LEFT join workshopsubtype_mst wst on wst.id=w.workshopsubtype_id
                    LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";
        //            if($dtWhere!=""){
        //                $query .=" AND du.istester=0 ";
        //            }else{
        //                $query .=" WHERE du.istester=0 ";
        //            }            
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " $dtOrder $dtLimit ";
        //           echo $query;exit;  
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();

        $query_count = "SELECT COUNT(ar.id) as counter from atom_results ar
                    LEFT join company c on c.id=ar.company_id
                    LEFT join workshop w on w.id=ar.workshop_id
                    LEFT join question_set qs on qs.id=ar.questionset_id
                    LEFT join company_users cu on cu.userid=ar.trainer_id
                    LEFT join question_topic qt on qt.id=ar.topic_id
                    LEFT join question_subtopic qst on qst.id=ar.subtopic_id
                    LEFT join questions q on q.id=ar.question_id
                    LEFT join device_users du on du.user_id=ar.user_id
                    LEFT join region rgn on rgn.id=du.region_id
                    LEFT join region rg on rg.id=w.region
                    LEFT join workshoptype_mst wt on wt.id=w.workshop_type
                    LEFT join workshopsubregion_mst srg on srg.id=w.workshopsubregion_id
                    LEFT join workshopsubtype_mst wst on wst.id=w.workshopsubtype_id
                    LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";

        $result = $this->db->query($query_count);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->counter;
        return $data;
    }
    public function export_Tpr_ToExcel($exportWhere = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = " select ar.id,c.company_name,w.workshop_name,rg.region_name,rgn.region_name as tregion_name,ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,ar.question_id,q.question_title as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,du.user_id,dt.description as designation,
                    srg.description as sub_region,wst.description as workshop_subtype,wt.workshop_type,
                    CASE ar.correct_answer
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as correct_answer,
                    CASE ar.option_clicked
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as user_answer,ar.is_correct,
                    CASE 1
                                        WHEN ar.is_correct THEN 'Correct'
                                        WHEN ar.is_wrong THEN 'Wrong'
                                        WHEN ar.is_timeout THEN 'Time Out'                   
                                        ELSE ''
                                        END as question_result,                    
                    DATE_FORMAT(ar.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(ar.end_dttm, '%d/%m/%Y %H:%i:%s') as end_dttm,ar.seconds 
                    from atom_results ar
                    LEFT join company c on c.id=ar.company_id
                    LEFT join workshop w on w.id=ar.workshop_id
                    LEFT join question_set qs on qs.id=ar.questionset_id
                    LEFT join company_users cu on cu.userid=ar.trainer_id
                    LEFT join question_topic qt on qt.id=ar.topic_id
                    LEFT join question_subtopic qst on qst.id=ar.subtopic_id
                    LEFT join questions q on q.id=ar.question_id 
                    LEFT join device_users du on du.user_id=ar.user_id
                    LEFT join region rgn on rgn.id=du.region_id
                    LEFT join region rg on rg.id=w.region
                    LEFT join workshoptype_mst wt on wt.id=w.workshop_type
                    LEFT join workshopsubregion_mst srg on srg.id=w.workshopsubregion_id
                    LEFT join workshopsubtype_mst wst on wst.id=w.workshopsubtype_id
                    LEFT join designation_trainee dt on dt.id=du.designation_id $exportWhere ";
        //            if($exportWhere!=""){
        //                $excel_data .=" AND du.istester=0 ";
        //            }else{
        //                $excel_data .=" WHERE du.istester=0 ";
        //            }             
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query = $this->db->query($excel_data);
        return $query->result();
    }

    // ==========================================//* trainee_played_result End *//=====================================================================================================================================================================================

    // ==========================================//* trainee_wise_summary_report Start here 10-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function TraineeSummaryLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.user_id,concat(du.firstname,' ',du.lastname) as traineename,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,tr.region_name,
                  FORMAT(sum(ar.seconds)/COUNT(ar.id),2) as avg_resp_time,dt.description as designation
                  from atom_results ar
                    INNER JOIN device_users du ON du.user_id=ar.user_id
                    LEFT JOIN region tr ON tr.id=du.region_id
                    INNER JOIN company cm ON cm.id=ar.company_id 
                    LEFT JOIN workshop w ON w.id=ar.workshop_id
                    LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                    LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";
        //            if($dtWhere!=""){
        //                $query .=" AND du.istester=0 ";
        //            }else{
        //                $query .=" WHERE du.istester=0 ";
        //            }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " group by ar.user_id ";

        $query_count = $query . " $dthaving ";

        $query .= " $dthaving $dtOrder $dtLimit ";

        //        if($dtOrder ==""){
        //            $query .= " order by result desc  ";
        //        }
        //$query .= "  $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();

        $Countset = $this->db->query($query_count);
        $data['dtTotalRecords'] = $Countset->num_rows();
        return $data;
    }

    public function TraineeSummaryExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag, $dtOrder)
    {

        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.user_id,concat(du.firstname,' ',du.lastname) as traineename,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,tr.region_name,
                  FORMAT(sum(ar.seconds)/COUNT(ar.id),2) as avg_resp_time,dt.description as designation
                  from atom_results ar
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN region tr ON tr.id=du.region_id
                        INNER JOIN company cm ON cm.id=ar.company_id
                        LEFT JOIN workshop w ON w.id=ar.workshop_id
                        LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type
                        LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";
        //        if($dtWhere!=""){
        //            $excel_data .=" AND du.istester=0 ";
        //        }else{
        //            $excel_data .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.user_id $dtOrder ";
        $excel_data .= " $dthaving ";
        //echo $excel_data;exit;
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* trainee_wise_summary_report End *//=====================================================================================================================================================================================


    // ==========================================//* traineetopic_wise_report Start here 11-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function Ttqwr_LoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag, $report_type)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.user_id,du.emp_id,concat(du.firstname,' ',du.lastname) as traineename, w.workshop_name,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
                  wt.workshop_type,wr.region_name as workshop_region,rg.region_name as trainee_region, 
                  wsr.description as workshop_subregion,wst.description as workshop_subtype,dt.description as designation ";
        if ($report_type == 1) {
            $query .= " ,qt.description as title ";
        } else if ($report_type == 2) {
            $query .= ", qt.title ";
        } else {
            $query .= ", count(distinct ar.questionset_id ) as title ";
        }
        $query .= "
                from atom_results ar	
                LEFT JOIN device_users du ON du.user_id=ar.user_id
                LEFT JOIN region rg on rg.id=du.region_id
                LEFT JOIN company cm ON cm.id=ar.company_id 
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT JOIN region wr on wr.id=w.region 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id ";

        if ($report_type == 1) {
            $query .= " LEFT JOIN question_topic qt ON qt.id=ar.topic_id ";
        } else if ($report_type == 2) {
            $query .= " LEFT JOIN question_set qt ON qt.id=ar.questionset_id ";
        }
        $query .= "  $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by ar.user_id,ar.workshop_id ";
        if ($report_type == 1) {
            $query .= ", ar.topic_id ";
        } else if ($report_type == 2) {
            $query .= ", ar.questionset_id ";
        }
        $query .= " $dthaving ";
        $query_count = $query;
        $query .= " $dtOrder $dtLimit ";
        //        echo $query;exit;           
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function export_Ttqwr_ToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag, $report_type = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.user_id,du.emp_id,concat(du.firstname,' ',du.lastname) as traineename, w.workshop_name,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
                  wt.workshop_type,wr.region_name as workshop_region,rg.region_name as trainee_region, 
                  wsr.description as workshop_subregion,wst.description as workshop_subtype,dt.description as designation ";
        if ($report_type == 1) {
            $excel_data .= " ,qt.description as title ";
        } else if ($report_type == 2) {
            $excel_data .= ", qt.title ";
        } else {
            $excel_data .= ", count(distinct ar.questionset_id ) as title ";
        }
        $excel_data .= " from atom_results ar	
                LEFT JOIN device_users du ON du.user_id=ar.user_id
                LEFT join region rg on rg.id=du.region_id
                LEFT JOIN company cm ON cm.id=ar.company_id 
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT join region wr on wr.id=w.region
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id ";
        if ($report_type == 1) {
            $excel_data .= " LEFT JOIN question_topic qt ON qt.id=ar.topic_id ";
        } else if ($report_type == 2) {
            $excel_data .= " LEFT JOIN question_set qt ON qt.id=ar.questionset_id ";
        }
        $excel_data .= "  $dtWhere ";
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $excel_data .= " group by ar.user_id,ar.workshop_id ";
        if ($report_type == 1) {
            $excel_data .= ", ar.topic_id ";
        } else if ($report_type == 2) {
            $excel_data .= ", ar.questionset_id ";
        }
        $excel_data .= " $dthaving order by workshop_name,traineename ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* traineetopic_wise_report End  *//=====================================================================================================================================================================================

    // ==========================================//* trainer_wise_summary_report Start here 11-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function TrainerSummaryLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.trainer_id,CONCAT(cu.first_name,' ', cu.last_name) as trainername,count(DISTINCT ar.user_id) AS TOTALtrainee,
                  count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(DISTINCT ar.topic_id) as TOTALtopic, count( DISTINCT if(ar.subtopic_id>0, ar.subtopic_id,null )) as TOTALsubtopic,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        INNER JOIN workshop w on w.id=ar.workshop_id
                        LEFT JOIN company_users cu ON cu.userid=ar.trainer_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere   ";
        //        if($dtWhere!=""){
        //            $query .=" AND du.istester=0 ";
        //        }else{
        //            $query .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " group by ar.trainer_id ";

        $query_count = $query . " $dthaving $dtOrder ";
        $query .= " $dthaving $dtOrder $dtLimit ";
        //        echo $query;exit;           
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function TrainerSummaryExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.trainer_id,CONCAT(cu.first_name,' ', cu.last_name) as trainername,count(DISTINCT ar.user_id) AS TOTALtrainee,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(DISTINCT ar.topic_id) as TOTALtopic, count( DISTINCT if(ar.subtopic_id>0, ar.subtopic_id,null )) as TOTALsubtopic,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        INNER JOIN workshop w on w.id=ar.workshop_id
                        LEFT JOIN company_users cu ON cu.userid=ar.trainer_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere ";
        //            if($dtWhere!=""){
        //                $excel_data .=" AND du.istester=0 ";
        //            }else{
        //                $excel_data .=" WHERE du.istester=0 ";
        //            }            
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.trainer_id ";

        $excel_data .= " $dthaving ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* trainer_wise_summary_report End  *//=====================================================================================================================================================================================


    // ==========================================//* trainer_consolidated_report_tab Start here 11-04-2023 Nirmal Gajjar  *//=====================================================================================================================================================================================
    public function TrainerConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtHaving, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = " SELECT ar.id,c.company_name,w.workshop_name,r.region_name,wt.workshop_type,ar.user_id,
		 CONCAT(cu.first_name,' ',cu.last_name) AS trainername,qt.description AS topicname, 
		 qst.description AS subtopicname , count(distinct ar.question_id) as total_question,
		 count(distinct ar.user_id) as total_trainee_played,count(ar.question_id) as total_question_played,
		 sum(ar.is_correct) as total_correct_ans ,wsr.description as workshop_subregion,wst.description as workshop_subtype,
		 FORMAT(IFNULL((sum(ar.is_correct) * 100 / (count(ar.question_id)) ),0),2) as result		 	 			
				FROM atom_results ar
						INNER JOIN company c ON c.id=ar.company_id
						INNER JOIN workshop w ON w.id=ar.workshop_id
						INNER JOIN region r ON r.id = w.region
						INNER JOIN workshoptype_mst wt ON wt.id = w.workshop_type
						INNER JOIN company_users cu ON cu.userid=ar.trainer_id
						INNER JOIN question_topic qt ON qt.id=ar.topic_id
						INNER JOIN question_subtopic qst ON qst.id=ar.subtopic_id 
                                                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                                                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
        //            if($dtWhere!=""){
        //                $query .=" AND du.istester=0 ";
        //            }else{
        //                $query .=" WHERE du.istester=0 ";
        //            }             
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }

        $query_count = $query . " group by ar.company_id,ar.workshop_id,ar.topic_id,ar.subtopic_id,ar.trainer_id $dtHaving $dtOrder ";

        $query .= " group by ar.company_id,ar.workshop_id,ar.topic_id,ar.subtopic_id,ar.trainer_id $dtHaving $dtOrder $dtLimit ";



        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function TrainerConsolidatedExportToExcel($exportWhere = '', $exportHaving = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = " SELECT ar.id,c.company_name,w.workshop_name,r.region_name,wt.workshop_type,ar.user_id,
		 CONCAT(cu.first_name,' ',cu.last_name) AS trainername,qt.description AS topicname, 
		 qst.description AS subtopicname , count( distinct ar.question_id) as total_question,
		 count(distinct ar.user_id) as total_trainee_played,count( ar.question_id) as total_question_played,
		 sum(ar.is_correct) as total_correct_ans ,wsr.description as workshop_subregion,wst.description as workshop_subtype,
		 FORMAT(IFNULL((sum(ar.is_correct) * 100 / (count(  ar.question_id) ) ),0),2) as result		 	 			
				FROM atom_results ar
						INNER JOIN company c ON c.id=ar.company_id
						INNER JOIN workshop w ON w.id=ar.workshop_id
						INNER JOIN region r ON r.id = w.region
						INNER JOIN workshoptype_mst wt ON wt.id = w.workshop_type
						INNER JOIN company_users cu ON cu.userid=ar.trainer_id
						INNER JOIN question_topic qt ON qt.id=ar.topic_id
						INNER JOIN question_subtopic qst ON qst.id=ar.subtopic_id 
                                                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                                                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $exportWhere ";
        //            if($exportWhere !=""){
        //                $excel_data .=" AND du.istester=0 ";
        //            }else{
        //                $excel_data .=" WHERE du.istester=0 ";
        //            }             
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.company_id,ar.workshop_id,w.region,ar.topic_id,ar.subtopic_id,ar.trainer_id $exportHaving order by workshop_name,trainername";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* trainer_consolidated_report_tab End*//=====================================================================================================================================================================================


    // ==========================================//* workshop_wise_report_tab Start here 11-04-2023 Nirmal Gajjar  *//=====================================================================================================================================================================================
    public function WorkshopWiseLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.questionset_id) as questionset,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,wsr.description as workshop_subregion,wst.description as workshop_subtype
            from atom_results ar
            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.id=w.region  
            LEFT JOIN company cm ON cm.id=ar.company_id 
            LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
            LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
        //        if($dtWhere!=""){
        //            $query .=" AND du.istester=0 ";
        //        }else{
        //            $query .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= "group by ar.workshop_id";
        $query_count = $query . " $dthaving $dtOrder ";
        $query .= " $dthaving $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function WorkshopWiseExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.questionset_id) as questionset,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,wsr.description as workshop_subregion,wst.description as workshop_subtype

            from atom_results ar
            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.id=w.region  
            LEFT JOIN company cm ON cm.id=ar.company_id 
            LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
            LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
        //        if($dtWhere!=""){
        //            $excel_data .=" AND du.istester=0 ";
        //        }else{
        //            $excel_data .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.workshop_id";

        $excel_data .= " $dthaving ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* workshop_wise_report_tab End*//=====================================================================================================================================================================================


    // ==========================================//* question_wise_report_tab Start here 11-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================

    public function QuestionWiseLoadDataTable($dtWhere, $dtWhere2, $dtOrder, $dtLimit, $dtHaving = '')
    {
        $query = "SELECT a.*,c.company_name,qs.title AS questionset,r.region_name,wrk.question_title,wt.workshop_type,
                    CASE wrk.correct_answer WHEN 'a' THEN wrk.option_a WHEN 'b' THEN wrk.option_b WHEN 'c' THEN wrk.option_c WHEN 'd' THEN wrk.option_d ELSE '' END AS correct_answer
                FROM (
                SELECT wq.company_id,wq.question_id,wq.workshop_id,wq.questionset_id,w.workshop_name,w.workshop_type as workshop_type_id ,w.region, COUNT(DISTINCT wq.user_id) AS no_of_trainee_played, SUM(wq.is_correct) AS no_of_trainee_ans,
                FORMAT((SUM(wq.is_correct) * 100 / COUNT(wq.user_id)),2) AS result,wsr.description as workshop_subregion,wst.description as workshop_subtype
                FROM atom_results wq
                INNER JOIN workshop w ON w.id=wq.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type 
                $dtWhere
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id $dtHaving
                ORDER BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id
                ) AS a
                INNER JOIN company c ON c.id=a.company_id
                INNER JOIN question_set qs ON qs.id = a.questionset_id
                INNER JOIN workshoptype_mst wt ON wt.id = a.workshop_type_id
                INNER JOIN region r ON r.id = a.region
                LEFT JOIN workshop_questions wrk ON wrk.question_id=a.question_id AND wrk.workshop_id=a.workshop_id AND wrk.company_id=a.company_id AND
                wrk.questionset_id=a.questionset_id
                $dtWhere2 $dtOrder $dtLimit ";
        // echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = " SELECT count(a.question_id) as counter
                FROM (
                SELECT wq.company_id,wq.question_id,wq.workshop_id,wq.questionset_id,w.workshop_name,w.workshop_type as workshop_type_id ,w.region, COUNT(DISTINCT wq.user_id) AS no_of_trainee_played, SUM(wq.is_correct) AS no_of_trainee_ans,
                FORMAT((SUM(wq.is_correct) * 100 / COUNT(wq.user_id)),2) AS result
                FROM atom_results wq
                INNER JOIN workshop w ON w.id=wq.workshop_id
                $dtWhere
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id $dtHaving
                ORDER BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id
                ) AS a
                INNER JOIN company c ON c.id=a.company_id
                INNER JOIN question_set qs ON qs.id = a.questionset_id
                INNER JOIN workshoptype_mst wt ON wt.id = a.workshop_type_id
                INNER JOIN region r ON r.id = a.region
                LEFT JOIN workshop_questions wrk ON wrk.question_id=a.question_id AND wrk.workshop_id=a.workshop_id AND wrk.company_id=a.company_id AND
                wrk.questionset_id=a.questionset_id
                $dtWhere2";
        $result = $this->db->query($query_count);
        $data_array = $result->row();
        $total = $data_array->counter;
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function QuestionWiseExportToExcel($dtWhere = '', $dtHaving = '')
    {
        $excel_data = " SELECT a.*,c.company_name,qs.title AS questionset,r.region_name,wrk.question_title,wt.workshop_type,
                    CASE wrk.correct_answer WHEN 'a' THEN wrk.option_a WHEN 'b' THEN wrk.option_b WHEN 'c' THEN wrk.option_c WHEN 'd' THEN wrk.option_d ELSE '' END AS correct_answer
                FROM (
                SELECT wq.company_id,wq.question_id,wq.workshop_id,wq.questionset_id,w.workshop_name,w.workshop_type as workshop_type_id ,w.region, COUNT(DISTINCT wq.user_id) AS no_of_trainee_played, SUM(wq.is_correct) AS no_of_trainee_ans,
                FORMAT((SUM(wq.is_correct) * 100 / COUNT(wq.user_id)),2) AS result,wsr.description as workshop_subregion,wst.description as workshop_subtype
                FROM atom_results wq
                INNER JOIN workshop w ON w.id=wq.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type 
                $dtWhere
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id $dtHaving
                ORDER BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id
                ) AS a
                INNER JOIN company c ON c.id=a.company_id
                INNER JOIN question_set qs ON qs.id = a.questionset_id
                INNER JOIN workshoptype_mst wt ON wt.id = a.workshop_type_id
                INNER JOIN region r ON r.id = a.region
                LEFT JOIN workshop_questions wrk ON wrk.question_id=a.question_id AND wrk.workshop_id=a.workshop_id AND wrk.company_id=a.company_id AND
                wrk.questionset_id=a.questionset_id order by workshop_id desc,question_id  ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* question_wise_report_tab End*//=====================================================================================================================================================================================

    // ==========================================//* imei_report_tab Start here 11-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================

    public function getWorkshopList($Company_id = "", $region_id = "")
    {
        $lcSqlStr = "select a.workshop_id,b.workshop_name FROM workshop_registered_users a "
            . "LEFT JOIN workshop as b "
            . "ON b.id=a.workshop_id where 1=1";
        if ($Company_id != "") {
            $lcSqlStr .= " AND b.company_id=" . $Company_id;
        }
        if ($region_id != "") {
            $lcSqlStr .= " AND b.region=" . $region_id;
        }
        $lcSqlStr .= " group by a.workshop_id order by b.start_date desc,b.workshop_name ";
        //echo $lcSqlStr;
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }

    public function ImeiDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, u.employment_year,
                u.education_background,u.department,di.model,di.platform,di.imei,di.serial, u.region_id,u.email,u.mobile,u.status,
                u.istester,rg.region_name,dr.description AS designation
                FROM device_users AS u
                LEFT JOIN device_info AS di ON di.user_id= u.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$RightsFlag) {
            $query .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = "SELECT count(u.user_id) as total_count
                FROM device_users AS u
                LEFT JOIN device_info AS di ON di.user_id= u.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$RightsFlag) {
            $query_count .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $result = $this->db->query($query_count);
        $total = $result->row();
        $data['dtTotalRecords'] = $total->total_count;
        return $data;
    }
    public function ImeiExportToExcel($dtWhere, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, u.employment_year,
                u.education_background,u.department,di.model,di.platform,di.imei,di.serial, u.region_id,u.email,u.mobile,u.status,
                u.istester,rg.region_name,dr.description AS designation
                FROM device_users AS u
                LEFT JOIN device_info AS di ON di.user_id= u.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$RightsFlag) {
            $excel_data .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " ORDER BY u.user_id DESC,di.id desc";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* imei_report_tab End*//=====================================================================================================================================================================================

    // Workshop reports Function end here (Tab 3)
}
