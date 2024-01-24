<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_reports_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getTraineeData($company_id, $workshoptype_id, $trainee_id, $dtOrder, $dtLimit, $dtWhere2, $RightsFlag, $WRightsFlag) {
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

//        echo $query;exit;
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

    public function getPrePostData($workshop_id, $trainee_id = '', $trainer_id = "0", $RightsFlag = 1) {
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
//         echo $query;
//         exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getLivePrePostData($workshop_id = '', $trainee_id = '', $trainer_id = "0", $RightsFlag = 1) {
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

    public function getPrePostWorkshopwise($workshop_id, $trainer_id = "0", $RightsFlag = 1) {
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
        if($PreFlag){
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

    public function getLivePrePostWorkshopwise($workshop_id, $trainer_id = "0", $RightsFlag = 1) {
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
        $query .=" GROUP BY arp.workshop_id UNION ALL
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
        $query .=" GROUP BY arp.workshop_id
        ) AS ls
        LEFT JOIN workshop AS b ON b.id=ls.workshop_id";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_PrePostTopicwise($workshop_id = '', $trainee_id = '', $RightsFlag = 1) {
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

//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_LivePrePostTopicwise($workshop_id = '', $trainee_id = '', $RightsFlag = 1) {
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
            GROUP BY ls.topic_id,ls.subtopic_id order by ls.topic_id  ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getPrePostQuestionAnsData($workshop_id = '', $trainee_id = '', $trainer_id = "0", $RightsFlag = 1) {
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

    public function getLivePrePostQuestionAnsData($workshop_id, $trainee_id, $trainer_id = "0", $RightsFlag = 1) {
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
        $query .="    
            UNION ALL
            SELECT SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 
             0 AS post_correct, 0 AS post_total_questions,COUNT(arp.question_id) as pre_played_questions,0 as post_played_questions
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND "
                . "arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id ";
        if ($trainer_id != "0") {
            $query .=" AND arp.trainer_id=" . $trainer_id;
        }
        $query .=" UNION ALL
            SELECT 0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct,
            COUNT(arp.question_id) AS post_total_questions,0 as pre_played_questions,COUNT(arp.question_id) as post_played_questions
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' "
                . "AND arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id ";
        if ($trainer_id != "0") {
            $query .=" AND arp.trainer_id=" . $trainer_id;
        }
        $query .=") AS ls";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_WorkshopRegisterdusers($workshop_id, $Company_id = "") {
        $querystr = "Select distinct(wru.user_id) as user_id,concat(du.firstname,' ',du.lastname) as username "
                . " from workshop_registered_users wru "
                . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wru.workshop_id AND wtu.tester_id=wru.user_id "
                . " inner join device_users du on du.user_id=wru.user_id where wtu.tester_id IS NULL AND wru.workshop_id=" . $workshop_id;
        if ($Company_id != "") {
            $querystr .=" AND wru.company_id=" . $Company_id;
        }
        $querystr .=" order by username ";
        $result = $this->db->query($querystr);
        return $result->result();
    }

    public function get_PrepostAccuracy($workshop_id = '', $trainee_id = '', $workshop_session = "PRE") {
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

    public function isWorkshopLive($workshop_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select if(end_date >='$TodayDt',1,0) as live_workshop FROM workshop where id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        return $LiveSet->live_workshop;
    }

    public function get_Traineewise_Rank($workshop_id = '', $user_id = '', $islive_workshop = "") {
        $TasterFlag = true;
        if ($islive_workshop == "") {
            $islive_workshop = $this->isWorkshopLive($workshop_id);
        }
        if ($user_id != "" && $workshop_id !="") {
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
                    $LcSqlStr .=" and wtu.tester_id IS NULL ";
                }
                $LcSqlStr .=" GROUP BY arp.user_id ORDER BY post_correct DESC,avg_time,trainee_name
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
                    $LcSqlStr .=" and wtu.tester_id IS NULL ";
                }
                $LcSqlStr .=" GROUP BY arp.user_id
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
                $LcSqlStr .=" and wtu.tester_id IS NULL ";
            }
            if (count((array)$LiveSet) > 0) {
                $LcSqlStr .=" group by a.trainee_id order by post_avg desc,avg_time,trainee";
            } else {
                $LcSqlStr .=" group by a.trainee_id order by pre_avg desc,avg_time,trainee";
            }
            $LcSqlStr .=") AS ls
                ,(SELECT @curRank := 0) r) as z  ";
        }
        if ($user_id != "") {
            $LcSqlStr .=" where z.trainee_id=" . $user_id;
        }
//        echo $LcSqlStr;exit;
        $query = $this->db->query($LcSqlStr);
        return $query->result();
    }

    public function SynchTraineeData($Company_id = "", $Workshop_id = "") {
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
//        echo $query."<pre>";
//        print_r($Del_WorkshopSet);
//        exit;
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
//                    echo $lcSqlStr;
//                    exit;
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
    public function get_traineeAccuracy($RightsFlag,$trainee_id, $trainer_id="0", $workshop_id, $workshop_session,$liveFlag) {
        
        $login_id  = $this->mw_session['user_id'];
        
        $query = " SELECT z.* FROM (";
        if($liveFlag){
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
        }else{
            $query .= "SELECT fs.*,@curRank := @curRank + 1 AS rank FROM("
                . "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,tr.region_name as trainee_region,";
                if($workshop_session=="PRE"){
                    $query .= "SUM(ls.pre_correct) as correct,SUM(ls.pre_played_questions) as played_questions,"
                    . "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_played_questions) as acc_order,"
                    . "if(SUM(ls.pre_played_questions) < SUM(ls.pre_total_questions),'Incompleted','Completed') as status,
                    (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time";
                }else{
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
        
            $query .= " WHERE z.trainee_id=".$trainee_id;
//            echo $query;exit
        
        $result = $this->db->query($query);
        return $result->result();
    }
     public function WorkshopLive($workshop_id,$workshop_session){
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
                . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet =$ObjSet->row();
        $liveFlag=false;
        if($workshop_session=="PRE"){
            if(strtotime($LiveSet->pre_date) >  strtotime($TodayDt)){
                $liveFlag=true;
            }
        }else{
            if(strtotime($LiveSet->post_date) >  strtotime($TodayDt)){
                $liveFlag=true;
            }
        }
        return $liveFlag;
    }
    public function get_TraineeRegionData($company_id=''){
        $lcSqlStr = "select du.region_id,r.region_name,r.id FROM device_users du "
                . " LEFT JOIN region as r "
                . " ON du.region_id = r.id where 1=1";
        if ($company_id != "") {
            $lcSqlStr .=" AND du.company_id=" . $company_id;
        }        
        
        $lcSqlStr .=" group by r.id ";
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }
}
