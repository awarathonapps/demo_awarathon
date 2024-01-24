<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_reports_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getTraineeData($company_id,$workshoptype_id, $trainee_id, $dtOrder, $dtLimit, $dtWhere2) {
        $dtWhere="";
        if ($workshoptype_id != "" ) {
            $dtWhere = " AND w.workshop_type  = " . $workshoptype_id;
        }
        $TodayDt = date('Y-m-d H:i:s');
        $query = "
                SELECT DATE_FORMAT(w.start_date,'%d-%m-%Y') AS start_date, w.workshop_name,ls.workshop_id, 
                FORMAT(SUM(ls.pre_correct)*100/sum(ls.pre_total_questions),2) as pre_average,
                IFNULL(FORMAT(SUM(ls.post_correct)*100/sum(ls.post_total_questions),2),'NP') as post_average,
                FORMAT(SUM(post_time_taken)/sum(ls.post_total_questions),2) as avg_time,count(distinct ar.topic_id) as total_topic  FROM (

                SELECT w.workshop_id,w.pre_correct,w.pre_total_questions,w.post_correct,w.post_total_questions,w.pre_time_taken,w.post_time_taken
                 FROM trainee_result as w WHERE w.company_id=$company_id AND w.trainee_id =$trainee_id $dtWhere group by w.workshop_id
                union all 
                SELECT arp.workshop_id ,SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,0 as post_correct,
                0 as post_total_questions,sum(arp.seconds) as pre_time_taken,0 as post_time_taken  FROM atom_results as arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='PRE' AND
                CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' $dtWhere
                group by arp.workshop_id
                union all 
                SELECT arp.workshop_id ,0 as pre_correct,0 as pre_total_questions,SUM(arp.is_correct) AS post_correct,
                 COUNT(arp.question_id) AS post_total_questions,0 as pre_time_taken,sum(arp.seconds) as post_time_taken FROM atom_results as arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='POST' AND
                CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' $dtWhere
                group by arp.workshop_id
                ) as ls
                LEFT JOIN atom_results ar ON ar.company_id=$company_id AND ar.user_id =$trainee_id AND ar.workshop_id=ls.workshop_id
                LEFT JOIN workshop AS w ON w.id=ls.workshop_id $dtWhere2
                group by ls.workshop_id $dtOrder $dtLimit  ";

        //echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);
        $query1 = "SELECT count(distinct workshop_id) AS total FROM atom_results AS a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id $dtWhere2";
            if($dtWhere2 !=""){
                $query1 .= " AND a.company_id=$company_id AND a.user_id =$trainee_id ";
            }else{
                $query1 .= " WHERE a.company_id=$company_id AND a.user_id =$trainee_id ";
            }
             

        $result1 = $this->db->query($query1);
        $data_array = $result1->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    public function getPrePostData($workshop_id, $trainee_id = '',$trainer_id="0",$RightsFlag=1) {
        $login_id  = $this->mw_session['user_id'];
        $query = "SELECT a.trainee_id,b.workshop_name, 
                IF(a.pre_played_questions=0,'Not Played', CONCAT(a.pre_avg,'%')) AS pre_average, 
                IF(a.post_played_questions=0,'Not Played', CONCAT(a.post_avg,'%')) AS post_average,a.post_avg,a.pre_avg, 
                format(a.post_avg-a.pre_avg,2) AS ce,
                a.trainee_name ,@curRank := @curRank + 1 AS rank
                FROM (
                select a.trainee_id,a.workshop_id, FORMAT(sum(a.pre_correct)*100/sum(a.pre_total_questions),2) as pre_avg,
                FORMAT(sum(a.post_correct)*100/sum(a.post_total_questions),2) as post_avg,
                FORMAT((sum(a.pre_time_taken)+sum(a.post_time_taken))/sum(a.pre_total_questions)+sum(a.post_total_questions),2) as avg_time,
                sum(pre_played_questions) as pre_played_questions,sum(post_played_questions) as post_played_questions,
                CONCAT(du.firstname,' ',du.lastname) AS trainee_name  FROM trainee_result AS a
                LEFT JOIN device_users AS du ON du.user_id=a.trainee_id
                WHERE a.workshop_id= $workshop_id ";
                if ($trainee_id != "") {
                    $query .= " AND a.trainee_id =$trainee_id";
                }else{
                    $query .= " AND a.istester=0 ";
                }
                if ($trainer_id == "0") {
                    if (!$RightsFlag) {
                        $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
                    }
                } else {
                    $query .= " AND a.trainer_id= " . $trainer_id;
                }
                $query .= "
                group by a.trainee_id) as a
                LEFT JOIN workshop AS b ON b.id=a.workshop_id
                ,(
                SELECT @curRank := 0) r
                ORDER BY post_average DESC,avg_time,trainee_name";
        //echo $query;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getLivePrePostData($workshop_id = '', $trainee_id = '',$trainer_id="0",$RightsFlag=1) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id  = $this->mw_session['user_id'];
        $query = "SELECT fs.trainee_id,b.workshop_name,fs.trainee_name, IF(fs.pre_avg=null,'Not Played',CONCAT(fs.pre_avg,'%')) AS pre_average, 
            IF(fs.post_avg=null,'Not Played',CONCAT(fs.post_avg,'%')) AS post_average,fs.post_avg,fs.pre_avg, FORMAT(fs.post_avg-fs.pre_avg,2) AS ce
        ,@curRank := @curRank + 1 AS rank
        FROM (
        SELECT ls.trainee_id,ls.workshop_id, FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_avg, FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_avg, SUM(ls.post_correct) AS post_correct, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,(SUM(total_time)/(SUM(ls.pre_total_questions)+ SUM(ls.post_total_questions))) AS avg_time
        FROM (
        SELECT es.trainee_id,es.workshop_id,es.pre_correct,es.pre_total_questions,0 AS post_correct,0 AS post_total_questions,es.pre_time_taken AS total_time
        FROM trainee_result AS es
        WHERE es.workshop_id= $workshop_id ";
        if ($trainee_id != "") {
            $query .= " AND es.trainee_id =$trainee_id";
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
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
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
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .= " GROUP BY arp.user_id) AS ls 
        LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
        where du.istester=0 GROUP BY ls.trainee_id
        ORDER BY post_correct DESC,avg_time,trainee_name
        ) AS fs
        LEFT JOIN workshop AS b ON b.id=fs.workshop_id,(SELECT @curRank := 0) r";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function getPrePostWorkshopwise($workshop_id,$trainer_id="0",$RightsFlag=1) {
        $login_id  = $this->mw_session['user_id'];
        $query = "SELECT b.workshop_name,SUM(pre_correct),SUM(pre_total_questions), FORMAT((SUM(pre_correct)*100/ SUM(pre_total_questions)),2) AS pre_average,
           FORMAT((SUM(post_correct)*100/ SUM(post_total_questions)),2) AS post_average
           FROM trainee_result AS a LEFT JOIN workshop AS b ON b.id=a.workshop_id
           JOIN device_users AS du ON du.user_id=a.trainee_id
           WHERE a.workshop_id= $workshop_id and a.pre_played_questions>0 and a.post_played_questions>0 and a.istester=0";
           if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $result = $this->db->query($query);
        return $result->row();
    }
    public function getLivePrePostWorkshopwise($workshop_id,$trainer_id="0",$RightsFlag=1) {
        $login_id  = $this->mw_session['user_id'];
        $TodayDt = date('Y-m-d H:i');
        $query = " SELECT ls.workshop_id,b.workshop_name, 
            FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS pre_average, 
            FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS post_average
        FROM (
        SELECT es.workshop_id,sum(es.pre_correct) as pre_correct ,sum(es.pre_total_questions) as pre_total_questions,0 AS post_correct,0 AS post_total_questions
        FROM trainee_result AS es WHERE es.workshop_id= $workshop_id ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (es.trainer_id = $login_id OR es.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND es.trainer_id= " . $trainer_id;
        }
        $query .=" AND es.trainee_id IN 
        (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='POST')     
        GROUP BY es.workshop_id UNION ALL
        SELECT arp.workshop_id, SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 0 AS post_correct, 0 AS post_total_questions
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND arp.workshop_id= $workshop_id
        ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .=" AND arp.user_id IN (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='POST')
        GROUP BY arp.workshop_id UNION ALL
        SELECT arp.workshop_id,0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
        FROM atom_results AS arp
        INNER JOIN workshop AS w ON w.id=arp.workshop_id
        WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' AND arp.workshop_id= $workshop_id
        ";
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
            }
        } else {
            $query .= " AND arp.trainer_id= " . $trainer_id;
        }
        $query .="
        AND arp.user_id IN (SELECT distinct user_id FROM atom_results WHERE workshop_id=$workshop_id AND workshop_session='PRE')
        GROUP BY arp.workshop_id
        ) AS ls
        LEFT JOIN workshop AS b ON b.id=ls.workshop_id
        GROUP BY ls.workshop_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_PrePostTopicwise($workshop_id = '', $trainee_id = '') {
        $TodayDt = date('Y-m-d H:i');
        $query = "SELECT IFNULL(SUM(res.pre_accuracy),0) AS pre_accuracy, IFNULL(SUM(res.post_accuracy),0) AS post_accuracy,res.topic_id, res.subtopic_id, qt.description AS topic,qst.description AS subtopic, IFNULL(FORMAT(SUM(res.pre_accuracy),2),'Not Played') AS pre_average_np, IFNULL(FORMAT(SUM(res.post_accuracy),2),'Not Played') AS post_average_np, IFNULL(FORMAT(SUM(res.post_accuracy)- SUM(res.pre_accuracy),2),'Not Played') AS ce
        FROM (
        SELECT 'PRE' AS sessions, FORMAT((ar.correct_ans*100/wq.total_question),2) AS pre_accuracy, NULL AS post_accuracy, wq.workshop_id, wq.total_question, IFNULL(ar.correct_ans,0) AS correct_ans,wq.topic_id,wq.subtopic_id
        FROM (
        SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id,a.subtopic_id
        FROM atom_results a
        WHERE a.user_id= $trainee_id AND a.workshop_id=$workshop_id AND a.workshop_session='PRE' 
        GROUP BY a.topic_id,a.subtopic_id
        ) ar
        RIGHT JOIN (
        SELECT c.company_id,c.workshop_id,c.topic_id,c.subtopic_id,
         IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$TodayDt', COUNT(DISTINCT tar.question_id), COUNT(DISTINCT c.question_id)) AS total_question
        FROM workshop_questions AS c
        INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
        INNER JOIN workshop AS w ON w.id=c.workshop_id
        INNER JOIN atom_results tar ON tar.workshop_id=c.workshop_id AND tar.workshop_session='PRE'
        AND tar.topic_id=c.topic_id AND tar.subtopic_id=c.subtopic_id
        WHERE tar.workshop_id=$workshop_id AND tar.user_id= $trainee_id
        GROUP BY c.workshop_id,c.topic_id,c.subtopic_id
        )
         wq ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND wq.topic_id=ar.topic_id AND wq.subtopic_id=ar.subtopic_id 


         UNION ALL
        SELECT 'POST' AS sessions, NULL AS pre_accuracy, FORMAT((ar.correct_ans*100/wq.total_question),2) AS post_accuracy, wq.workshop_id, wq.total_question, IFNULL(ar.correct_ans,0) AS correct_ans,wq.topic_id,wq.subtopic_id
        FROM (
        SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id,a.subtopic_id
        FROM atom_results a
        WHERE a.user_id=$trainee_id AND a.workshop_id=$workshop_id AND a.workshop_session='POST'
        GROUP BY a.topic_id,a.subtopic_id) ar
        RIGHT JOIN (

        SELECT c.company_id,c.workshop_id,c.topic_id,c.subtopic_id,
        IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$TodayDt', COUNT(DISTINCT tar.question_id), COUNT(DISTINCT c.question_id)) AS total_question
        FROM workshop_questions AS c
        INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
        INNER JOIN workshop AS w ON w.id=c.workshop_id
        INNER JOIN atom_results tar ON tar.workshop_id=c.workshop_id AND tar.workshop_session='POST' AND tar.topic_id=c.topic_id AND tar.subtopic_id=c.subtopic_id
        WHERE tar.workshop_id=$workshop_id AND tar.user_id= $trainee_id
        GROUP BY c.workshop_id,c.topic_id,c.subtopic_id

        ) wq ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND wq.topic_id=ar.topic_id AND wq.subtopic_id=ar.subtopic_id) res
        INNER JOIN question_topic qt ON qt.id=res.topic_id
        INNER JOIN question_subtopic qst ON qst.id=res.subtopic_id
        GROUP BY topic_id,subtopic_id";
        //echo $query;exit;

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getPrePostQuestionAnsData($workshop_id = '', $trainee_id = '') {
        $query = "SELECT pre_correct,pre_total_questions,post_correct,post_total_questions FROM trainee_result"
                . " WHERE workshop_id=$workshop_id AND trainee_id=$trainee_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function getLivePrePostQuestionAnsData($workshop_id, $trainee_id) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = " SELECT  SUM(ls.pre_correct) as pre_correct, SUM(ls.pre_total_questions)  AS pre_total_questions,
            SUM(ls.post_correct) as post_correct, SUM(ls.post_total_questions) as post_total_questions
            FROM (
            SELECT es.pre_correct,es.pre_total_questions,0 AS post_correct,0 AS post_total_questions
            FROM trainee_result AS es
            WHERE es.workshop_id= $workshop_id AND es.trainee_id =$trainee_id UNION ALL
            SELECT SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions, 
             0 AS post_correct, 0 AS post_total_questions
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND "
                . "arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id UNION ALL
            SELECT 0 AS pre_correct,0 AS pre_total_questions, SUM(arp.is_correct) AS post_correct,
            COUNT(arp.question_id) AS post_total_questions
            FROM atom_results AS arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id
            WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' "
            . "AND arp.workshop_id= $workshop_id AND arp.user_id =$trainee_id
            ) AS ls";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_WorkshopRegisterdusers($workshop_id, $Company_id = "") {
        $querystr = "Select distinct(wru.user_id) as user_id,concat(du.firstname,' ',du.lastname) as username "
                . " from workshop_registered_users wru "
                . " inner join device_users du on du.user_id=wru.user_id where wru.workshop_id=" . $workshop_id;
        if ($Company_id != "") {
            $querystr .=" AND wru.company_id=" . $Company_id;
        }

        $result = $this->db->query($querystr);
        return $result->result();
    }

    public function get_PrepostAccuracy($workshop_id = '', $trainee_id = '', $workshop_session = "PRE") {
        $TodayDt = date('Y-m-d H:i:s');

        $query = "SELECT FORMAT((SUM(res.correct_ans)*100/SUM(res.total_question)),2) AS accuracy,IFNULL(SUM(res.correct_ans),0) AS correct_ans,
            IFNULL(SUM(res.total_question),0) AS total_question,
            res.topic_id, res.subtopic_id,qt.description AS topic,qst.description AS subtopic
                FROM (SELECT wq.workshop_id, wq.total_question,ifnull(ar.correct_ans,0) as correct_ans,wq.topic_id,wq.subtopic_id
                FROM (
                SELECT a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans,a.topic_id,a.subtopic_id
                FROM atom_results a
                WHERE a.user_id= $trainee_id AND a.workshop_id=$workshop_id AND a.workshop_session='$workshop_session' 
                GROUP BY a.topic_id,a.subtopic_id) ar
                right JOIN (
                SELECT c.company_id,c.workshop_id,c.topic_id,c.subtopic_id,";
        if ($workshop_session == "PRE") {
            $query .= "IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$TodayDt', COUNT(DISTINCT tar.question_id), COUNT(DISTINCT c.question_id)) AS total_question
                        FROM workshop_questions AS c INNER JOIN workshop_questionset_pre ";
        } else {
            $query .= "IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$TodayDt', COUNT(DISTINCT tar.question_id), COUNT(DISTINCT c.question_id)) AS total_question
                        FROM workshop_questions AS c INNER JOIN workshop_questionset_post ";
        }
        $query .= " AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
                    INNER JOIN atom_results tar ON tar.workshop_id=c.workshop_id AND tar.workshop_session='$workshop_session' AND
                    tar.topic_id=c.topic_id AND tar.subtopic_id =c.subtopic_id
                INNER JOIN workshop AS w ON w.id=c.workshop_id
                WHERE tar.workshop_id=$workshop_id AND tar.user_id= $trainee_id 
                GROUP BY c.workshop_id,c.topic_id,c.subtopic_id) wq 

                ON wq.workshop_id=ar.workshop_id AND wq.company_id=ar.company_id AND
                wq.topic_id=ar.topic_id AND wq.subtopic_id=ar.subtopic_id ) res
                INNER JOIN question_topic qt ON qt.id=res.topic_id
                INNER JOIN question_subtopic qst ON qst.id=res.subtopic_id
                GROUP BY topic_id,subtopic_id";
        //echo $query;exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function isWorkshopLive($workshop_id){
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select if(end_date >='$TodayDt',1,0) as live_workshop FROM workshop where id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet =$ObjSet->row();
        return $LiveSet->live_workshop;
    }
    public function get_Traineewise_Rank($workshop_id = '', $user_id = '',$islive_workshop="") {
        $TasterFlag = true;
        if($islive_workshop==""){
            $islive_workshop =$this->isWorkshopLive($workshop_id);
        }
        if ($user_id != "") {
            $query = "select user_id FROM device_users where istester=1 AND user_id =" . $user_id;
            $ObjSet = $this->db->query($query);
            if (count($ObjSet->row()) > 0) {
                $TasterFlag = false;
            }
        }
        if($islive_workshop){
            $query = "select id FROM atom_results where workshop_session='POST' AND workshop_id=" . $workshop_id;
            $ObjSet = $this->db->query($query);
            $LiveSet = $ObjSet->row();
            $TodayDt = date('Y-m-d H:i');
            if (count($LiveSet) > 0) {
                $LcSqlStr = " SELECT z.* FROM (SELECT fs.*,@curRank := @curRank + 1 AS rank FROM(
                    SELECT arp.user_id as trainee_id ,sum(arp.is_correct) as post_correct, FORMAT(SUM(arp.is_correct)*100/ count(arp.question_id),2) AS post_avg, 
                    SUM(arp.seconds)/count(arp.question_id) AS avg_time,CONCAT(du.firstname,' ',du.lastname) AS trainee_name
                    FROM atom_results AS arp
                    INNER JOIN workshop AS w ON w.id=arp.workshop_id
                    LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                    WHERE arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' 
                    AND arp.workshop_id=$workshop_id ";
                    if ($TasterFlag) {
                        $LcSqlStr .=" and du.istester=0";
                    }
                     $LcSqlStr .=" GROUP BY arp.user_id
                    ORDER BY post_correct DESC,avg_time,trainee_name
                    ) as fs ,(SELECT @curRank := 0) r) as z ";
            } else {
                $LcSqlStr = " SELECT z.* FROM (SELECT fs.*,@curRank := @curRank + 1 AS rank FROM(
                        SELECT arp.user_id as trainee_id,sum(arp.is_correct) as pre_correct, FORMAT(SUM(arp.is_correct)*100/ count(arp.question_id),2) AS pre_avg, 
                        SUM(arp.seconds)/count(arp.question_id) AS avg_time,CONCAT(du.firstname,' ',du.lastname) AS trainee_name
                        FROM atom_results AS arp
                        INNER JOIN workshop AS w ON w.id=arp.workshop_id
                        LEFT JOIN device_users AS du ON du.user_id=arp.user_id  WHERE arp.workshop_session='PRE' AND
                        CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt' AND
                        arp.workshop_id=$workshop_id ";
                        if ($TasterFlag) {
                            $LcSqlStr .=" and du.istester=0";
                        }
                        $LcSqlStr .=" GROUP BY arp.user_id
                        ORDER BY pre_correct DESC,avg_time,trainee_name
                        ) as fs ,(SELECT @curRank := 0) r) as z  ";
            }
        }else{
            $LcSqlStr = "SELECT z.* FROM (select a.trainee_id,a.workshop_id,a.post_avg,
                CONCAT(du.firstname,' ',du.lastname) as trainee , @curRank := @curRank + 1 AS rank
                FROM trainee_result as a 
                LEFT JOIN  device_users as du ON du.user_id=a.trainee_id,(SELECT @curRank := 0) r
                where a.workshop_id=$workshop_id ";
                if ($TasterFlag) {
                    $LcSqlStr .=" and a.istester=0";
                }
                $LcSqlStr .=" order by a.post_avg desc,a.avg_time,trainee) as z  ";
        }
        if ($user_id != "") {
            $LcSqlStr .=" where z.trainee_id=" . $user_id;
        }
        //echo $LcSqlStr;exit;
        $query = $this->db->query($LcSqlStr);
        return $query->result();
    }
    public function SynchTraineeData($Company_id="",$Workshop_id=""){
        if($Company_id==""){
            return false;
        }
        $this->UpdateLiveData($Company_id,$Workshop_id);
        //exit;
        $CurrentTime = date('Y-m-d H:i');
        $query = "SELECT distinct a.workshop_id,b.start_date,b.workshop_type,b.region,"
                . "CONCAT(b.pre_end_date,' ', STR_TO_DATE(b.pre_end_time, '%l:%i %p')) as pre_enddate,"
                . "CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) as post_enddate FROM atom_results as a LEFT JOIN "
            . " workshop as b ON b.id=a.workshop_id where a.company_id= $Company_id "
            . "AND a.workshop_id NOT IN(select distinct workshop_id FROM trainee_result where company_id= $Company_id)"
            . " AND (CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) <= '$CurrentTime' OR CONCAT(b.pre_end_date,' ', STR_TO_DATE(b.pre_end_time, '%l:%i %p')) <= '$CurrentTime')";
        if($Workshop_id !=""){
            $query .= " AND a.workshop_id=".$Workshop_id;
        }
        $result = $this->db->query($query);
        $WorkshopSet= $result->result();   
        if(count($WorkshopSet)>0){
            foreach ($WorkshopSet as $value) {
                $Workshop_id =$value->workshop_id;
                $Pre_endDate = $value->pre_enddate;
                $Post_endDate = $value->post_enddate;
                if($Post_endDate!='1970-01-01 00:00:00' && strtotime($Post_endDate)<=strtotime($CurrentTime)){
                    $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainee_id,workshop_date,pre_correct,pre_played_questions, pre_total_questions,pre_avg,post_correct, post_played_questions,post_total_questions,post_avg,avg_time,workshop_type,region_id,pre_time_taken,post_time_taken,trainer_id,istester,liveflag,ce)
                        select fs.*,(fs.post_average-fs.pre_average) as ce FROM(
                        SELECT $Company_id AS company_id,prpo.workshop_id,prpo.user_id,'".$value->start_date."' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton, SUM(prpo.pre_total_questions) AS pre_total_questions, 
                        FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
                        SUM(prpo.post_correct) AS post_correct, SUM(prpo.post_played_quesiton) AS post_played_quesiton,
                        SUM(prpo.post_total_questions) AS post_total_questions, FORMAT((SUM(prpo.post_correct)*100)/ SUM(prpo.post_total_questions),2) AS post_average, FORMAT(((SUM(prpo.pre_time_taken)+ SUM(prpo.post_time_taken))/ (SUM(prpo.pre_played_quesiton)+ SUM(prpo.post_played_quesiton))),2) AS avgtime,
                        '".$value->workshop_type."' AS workshop_type,'".$value->region."' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken, SUM(prpo.post_time_taken) AS post_time_taken, 
                          prpo.trainer_id,du.istester,0 AS liveflag
                        FROM(
                        SELECT ar.company_id,ar.user_id,ar.workshop_id, ar.pre_correct, IF(powq.liveflag=1,ar.played_quesiton,powq.total_questions) AS pre_total_questions,0 AS post_correct,0 AS post_total_questions, ar.total_seconds,ar.total_seconds_taken AS pre_time_taken,
                        0 AS post_time_taken, ar.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton,powq.liveflag AS pre_live,
                         0 AS post_live, ar.trainer_id
                        FROM (
                        SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session, SUM(arp.is_correct) AS pre_correct, 
                        SUM(arp.timer) AS total_seconds, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton
                        ,arp.trainer_id
                        FROM atom_results AS arp
                        WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
                        GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id

                        ) AS ar
                        INNER JOIN (
                        SELECT wq.company_id,wq.workshop_id,wq.questionset_id, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '2018-04-18 16:10', 1, 0) AS liveflag,
                         COUNT(DISTINCT wq.question_id) AS total_questions,wq.trainer_id
                        FROM workshop_questions AS wq
                        INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                        INNER JOIN workshop AS w ON w.id=wq.workshop_id
                        WHERE wq.workshop_id = $Workshop_id
                        GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id) AS powq ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id AND ar.trainer_id=powq.trainer_id

                         UNION ALL
                        SELECT ar.company_id,ar.user_id,ar.workshop_id,0 AS pre_correct,0 AS pre_total_questions, ar.post_correct, 
                        IF(powq.liveflag=1, ar.played_quesiton,powq.total_questions) AS post_total_questions, ar.total_seconds, 0 AS pre_time_taken,
                        ar.total_seconds_taken AS post_time_taken, 0 AS pre_played_quesiton,
                        played_quesiton AS post_played_quesiton,0 AS pre_live, powq.liveflag AS post_live,
                        ar.trainer_id
                        FROM (
                        SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session, SUM(arp.is_correct) AS post_correct, SUM(arp.timer) AS total_seconds, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton,arp.trainer_id
                        FROM atom_results AS arp
                        WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'POST'
                        GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id) AS ar
                        INNER JOIN (
                        SELECT wq.company_id,wq.workshop_id,wq.questionset_id, IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '2018-04-18 16:10', 1, 0) AS liveflag, COUNT(DISTINCT wq.question_id) AS total_questions,wq.trainer_id
                        FROM workshop_questions AS wq
                        INNER JOIN workshop_questionset_post AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                        INNER JOIN workshop AS w ON w.id=wq.workshop_id
                        WHERE wq.workshop_id = $Workshop_id
                        GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id) AS powq ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id 
                        AND ar.trainer_id=powq.trainer_id) AS prpo
                        LEFT JOIN device_users AS du ON du.user_id=prpo.user_id
                        GROUP BY prpo.user_id,prpo.workshop_id,prpo.trainer_id
                        ORDER BY post_average DESC,avgtime ASC, CONCAT(du.firstname,' ',du.lastname)
                        ) as fs";  
//                    echo $lcSqlStr;
//                    exit;
                    $this->db->query($lcSqlStr);
                }else if($Pre_endDate!='1970-01-01 00:00:00' && strtotime($Pre_endDate)<=strtotime($CurrentTime)){
                      $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainee_id,workshop_date,pre_correct,pre_played_questions, pre_total_questions,pre_avg,post_correct, post_played_questions,post_total_questions,post_avg,avg_time,workshop_type,region_id,pre_time_taken,post_time_taken,trainer_id,istester,liveflag,ce)
                        select fs.*,(fs.post_average-fs.pre_average) as ce FROM(
                        SELECT $Company_id AS company_id,prpo.workshop_id,prpo.user_id,'".$value->start_date."' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton, SUM(prpo.pre_total_questions) AS pre_total_questions, 
                        FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
                        SUM(prpo.post_correct) AS post_correct, SUM(prpo.post_played_quesiton) AS post_played_quesiton,
                        SUM(prpo.post_total_questions) AS post_total_questions, FORMAT((SUM(prpo.post_correct)*100)/ SUM(prpo.post_total_questions),2) AS post_average, FORMAT(((SUM(prpo.pre_time_taken)+ SUM(prpo.post_time_taken))/ (SUM(prpo.pre_played_quesiton)+ SUM(prpo.post_played_quesiton))),2) AS avgtime,
                        '".$value->workshop_type."' AS workshop_type,'".$value->region."' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken, SUM(prpo.post_time_taken) AS post_time_taken, 
                          prpo.trainer_id,du.istester,1 AS liveflag
                        FROM(
                        SELECT ar.company_id,ar.user_id,ar.workshop_id, ar.pre_correct, IF(powq.liveflag=1,ar.played_quesiton,powq.total_questions) AS pre_total_questions,0 AS post_correct,0 AS post_total_questions, ar.total_seconds,ar.total_seconds_taken AS pre_time_taken,
                        0 AS post_time_taken, ar.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton,powq.liveflag AS pre_live,
                        0 AS post_live, ar.trainer_id
                        FROM (
                        SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session, SUM(arp.is_correct) AS pre_correct, 
                        SUM(arp.timer) AS total_seconds, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton
                        ,arp.trainer_id
                        FROM atom_results AS arp
                        WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
                        GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.trainer_id
                        ) AS ar
                        INNER JOIN (
                        SELECT wq.company_id,wq.workshop_id,wq.questionset_id, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '2018-04-18 16:10', 1, 0) AS liveflag,
                         COUNT(DISTINCT wq.question_id) AS total_questions,wq.trainer_id
                        FROM workshop_questions AS wq
                        INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND wpo.active=1
                        INNER JOIN workshop AS w ON w.id=wq.workshop_id
                        WHERE wq.workshop_id = $Workshop_id
                        GROUP BY wq.company_id,wq.workshop_id,wq.trainer_id) AS powq ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id AND ar.trainer_id=powq.trainer_id
                        ) AS powq ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id 
                        AND ar.trainer_id=powq.trainer_id) AS prpo
                        LEFT JOIN device_users AS du ON du.user_id=prpo.user_id
                        GROUP BY prpo.user_id,prpo.workshop_id,prpo.trainer_id
                        ORDER BY post_average DESC,avgtime ASC, CONCAT(du.firstname,' ',du.lastname)
                        ) as fs";   
//                    $lcSqlStr = "INSERT INTO trainee_result(company_id,workshop_id,trainee_id,workshop_date,pre_correct,pre_played_questions,
//                        pre_total_questions,pre_avg,avg_time,workshop_type,region_id,pre_time_taken,total_topic,istester,rank,liveflag)
//                    select fs.*, @curRank := @curRank + 1 AS rank FROM(
//                    SELECT $Company_id AS compnay_id,prpo.workshop_id,prpo.user_id,'".$value->start_date."' AS start_date, SUM(prpo.pre_correct) AS pre_correct, SUM(prpo.pre_played_quesiton) AS pre_played_quesiton,
//                        SUM(prpo.pre_total_questions) AS pre_total_questions,
//                     FORMAT((SUM(prpo.pre_correct)*100)/ SUM(prpo.pre_total_questions),2) AS pre_average,
//                     FORMAT((SUM(prpo.pre_time_taken)/ SUM(prpo.pre_played_quesiton)),2) AS avgtime, 
//                    '".$value->workshop_type."' AS workshop_type,'".$value->region."' AS region, SUM(prpo.pre_time_taken) AS pre_time_taken,
//                    count(distinct prpo.topic_id) as total_topic,du.istester,1 as liveflag
//                    FROM(
//                    SELECT ar.company_id,ar.user_id,ar.workshop_id, ar.pre_correct, IF(powq.liveflag=1,ar.played_quesiton,powq.total_questions) AS pre_total_questions,0 AS post_correct,0 AS post_total_questions,
//                     ar.total_seconds,ar.total_seconds_taken AS pre_time_taken,0 AS post_time_taken,
//                     ar.played_quesiton AS pre_played_quesiton,0 AS post_played_quesiton,powq.liveflag AS pre_live, 0 AS post_live,
//                     IF(powq.liveflag=1,ar.topic_id,powq.topic_id) as topic_id
//                    FROM (
//                    SELECT arp.company_id,arp.user_id,arp.workshop_id,arp.workshop_session, SUM(arp.is_correct) AS pre_correct,
//                    SUM(arp.timer) AS total_seconds, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton,arp.topic_id
//                    FROM atom_results AS arp
//                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'PRE'
//                    GROUP BY arp.company_id,arp.user_id,arp.workshop_id,arp.topic_id
//                    ) AS ar
//                    INNER JOIN (
//                    SELECT wq.company_id,wq.workshop_id,wq.questionset_id, 
//                    IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$CurrentTime', 1, 0) AS liveflag,
//                    COUNT(DISTINCT wq.question_id) AS total_questions,wq.topic_id
//                    FROM workshop_questions AS wq
//                    INNER JOIN workshop_questionset_pre AS wpo ON wq.workshop_id = wpo.workshop_id AND wq.questionset_id = wpo.questionset_id AND
//                     wpo.active=1
//                    INNER JOIN workshop AS w ON w.id=wq.workshop_id
//                    WHERE wq.workshop_id = $Workshop_id
//                    GROUP BY wq.company_id,wq.workshop_id,wq.topic_id
//                    ) AS powq ON ar.company_id = powq.company_id AND ar.workshop_id = powq.workshop_id AND ar.topic_id=powq.topic_id
//                    ) AS prpo 
//                    LEFT JOIN device_users as du ON du.user_id=prpo.user_id
//                    GROUP BY prpo.user_id,prpo.workshop_id
//                    ORDER BY pre_average DESC,avgtime ASC ,CONCAT(du.firstname,' ',du.lastname)
//                    ) fs,(SELECT @curRank := 0) r";

                   $this->db->query($lcSqlStr);
                }
            }
        }
        
    }
    public function UpdateLiveData($Company_id,$Workshop_id=""){
        $CurrentTime = date('Y-m-d H:i');
        $query = "SELECT distinct a.workshop_id,CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) as post_enddate "
            . "FROM trainee_result as a INNER JOIN workshop as b ON b.id=a.workshop_id "
            . "where a.company_id= $Company_id AND a.liveflag=1 "
            . " AND CONCAT(b.post_end_date,' ', STR_TO_DATE(b.post_end_time, '%l:%i %p')) <= '$CurrentTime'";
        if($Workshop_id !=""){
            $query .= " AND a.workshop_id=".$Workshop_id;
        }
        $result =$this->db->query($query);
        $WorkshopSet= $result->result();       
        if(count($WorkshopSet)>0){
            foreach ($WorkshopSet as $value) {
                $Workshop_id =$value->workshop_id;
                    $lcSQlStr ="UPDATE trainee_result AS a
                    INNER JOIN(
                    SELECT arp.user_id,arp.workshop_id, SUM(arp.is_correct) AS post_correct, SUM(arp.seconds) AS total_seconds_taken, COUNT(arp.question_id) AS played_quesiton
                    FROM atom_results AS arp
                    WHERE arp.workshop_id = $Workshop_id AND arp.workshop_session = 'POST'
                    GROUP BY arp.workshop_id,arp.user_id) AS b ON b.user_id=a.trainee_id AND b.workshop_id=a.workshop_id 
                    INNER JOIN(
                    SELECT wq.workshop_id, COUNT(DISTINCT wq.question_id) AS total_questions
                    FROM workshop_questions AS wq
                    INNER JOIN workshop_questionset_post AS wpo ON wq.workshop_id = wpo.workshop_id AND
                    wq.questionset_id = wpo.questionset_id AND wpo.active=1
                    INNER JOIN workshop AS w ON w.id=wq.workshop_id
                    WHERE wq.workshop_id = $Workshop_id
                    GROUP BY wq.company_id,wq.workshop_id
                    ) AS wq ON  wq.workshop_id=a.workshop_id
                    LEFT JOIN device_users as du ON du.user_id=a.trainee_id
                    SET a.post_correct=b.post_correct,a.post_total_questions=wq.total_questions,
                    a.post_played_questions=b.played_quesiton,
                    a.post_avg=(b.post_correct*100/wq.total_questions),a.post_time_taken=b.total_seconds_taken,
                    a.avg_time=((a.pre_time_taken+b.total_seconds_taken)/(a.pre_played_questions+b.played_quesiton)),
                    a.ce =if(a.pre_avg>0 ,(b.post_correct*100/wq.total_questions)-a.pre_avg,0),
                    a.istester=du.istester,a.liveflag=0";
                    echo $lcSQlStr;
                    exit;
                    $this->db->query($lcSQlStr);
                
//                $lcSQlStr2 ="UPDATE trainee_result AS a
//                INNER JOIN(
//                select a.trainee_id,a.workshop_id,a.post_avg,CONCAT(du.firstname,' ',du.lastname) as trainee , @curRank := @curRank + 1 AS rank
//                FROM trainee_result as a 
//                LEFT JOIN  device_users as du ON du.user_id=a.trainee_id,(SELECT @curRank := 0) r
//                where a.workshop_id=$Workshop_id
//                order by a.post_avg desc,a.avg_time,trainee
//                ) fs ON fs.trainee_id=a.trainee_id and fs.workshop_id=a.workshop_id
//                set a.rank=fs.rank";
                // $this->db->query($lcSQlStr2);
            }
        }
    }

}
