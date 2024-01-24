<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_accuracy_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function isWorkshopLive($workshop_id,$workshop_session){
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
    public function get_traineeAccuracy($RightsFlag,$trainee_id="0", $trainer_id, $workshop_id, $workshop_session,$liveFlag,$trainee_region_id="0") {
        
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
                        if ($trainee_region_id != "0") {
                            $query .= " AND  du.region_id = ".$trainee_region_id;
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
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
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
        if($trainee_id !="0"){
            $query .= " WHERE z.trainee_id=".$trainee_id;
        }
  
        $result = $this->db->query($query);
        return $result->result();
    }
    public function top_five_trainee($RightsFlag,$trainee_id="0", $trainer_id, $workshop_id, $workshop_session,$liveFlag,$trainee_region_id="0") {
        $login_id  = $this->mw_session['user_id'];
        $query = " SELECT fs.* FROM(";
        if($liveFlag){
            $query .= " SELECT arp.user_id as trainee_id, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
                format(SUM(arp.is_correct)*100/COUNT(arp.question_id),2) AS accuracy,SUM(arp.is_correct)*100/COUNT(arp.question_id) as acc_order,
                (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time
                FROM atom_results AS arp
                LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.workshop_id=$workshop_id ";
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
                }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " AND wtu.tester_id IS NULL and arp.workshop_session='$workshop_session' GROUP BY arp.user_id order by acc_order desc,avg_time asc,trainee_name limit 0,5 ";
        }else{
            $query .= "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,";
                if($workshop_session=="PRE"){
                     $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions) as acc_order,
                                (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time ";
                }else{
                     $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) as acc_order,
                                (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time";
                }
                $query .= " FROM trainee_result AS ls 
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id ";
                $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
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
        if($trainee_id !="0"){
            $query .= " WHERE fs.trainee_id=".$trainee_id;
        }
        // echo $query;
        // exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function bottom_five_trainee($RightsFlag,$trainee_id="0", $trainer_id, $workshop_id, $workshop_session,$top_five_trainee_id,$liveFlag,$trainee_region_id="0") {
        $login_id  = $this->mw_session['user_id'];
        $query = " SELECT fs.* FROM(";
        if($liveFlag){
            $query .= "SELECT arp.user_id as trainee_id, CONCAT(du.firstname,' ',du.lastname) AS trainee_name,
                format(SUM(arp.is_correct)*100/COUNT(arp.question_id),2) AS accuracy,SUM(arp.is_correct)*100/COUNT(arp.question_id) as acc_order,
                (SUM(arp.seconds)/(COUNT(arp.question_id))) AS avg_time
                FROM atom_results AS arp
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                WHERE wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id AND arp.workshop_session='$workshop_session' ";
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
                }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            if($top_five_trainee_id !=""){
                $query .= " AND arp.user_id NOT IN(" . $top_five_trainee_id.")";
            }
            $query .= " GROUP BY arp.user_id order by acc_order asc,avg_time desc,trainee_name desc limit 0,5 ";
        }else{
            $query .= "SELECT ls.trainee_id,CONCAT(du.firstname,' ',du.lastname) AS trainee_name,";
                if($workshop_session=="PRE"){
                     $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy,SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions) as acc_order,
                     (SUM(ls.pre_time_taken)/(SUM(ls.pre_total_questions))) AS avg_time ";
                }else{
                     $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy,SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) as acc_order,
                     (SUM(ls.post_time_taken)/(SUM(ls.post_total_questions))) AS avg_time ";
                }
                $query .= " FROM trainee_result AS ls 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id    
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id ";
                $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
                }
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            if($top_five_trainee_id !=""){
                $query .= " AND ls.trainee_id NOT IN(" . $top_five_trainee_id.")";
            }
            $query .= " GROUP BY ls.trainee_id order by acc_order asc ,avg_time desc,trainee_name desc limit 0,5  ";
        }
        $query .= " ) as fs ";
        if($trainee_id !="0"){
            $query .= " WHERE fs.trainee_id=".$trainee_id;
        }
        //echo $query;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_PrepostAccuracy($workshop_id,$trainee_id="0",$workshop_session,$trainer_id="0",$RightsFlag,$liveFlag,$trainee_region_id="0") {
        $login_id  = $this->mw_session['user_id'];
        if($liveFlag){
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,
                (SUM(arp.is_correct)*100/COUNT(arp.question_id)) AS accuracy
                FROM atom_results AS arp
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                INNER JOIN question_topic qt ON qt.id=arp.topic_id
                LEFT JOIN question_subtopic qst ON qst.id=arp.subtopic_id
                LEFT JOIN device_users AS du ON du.user_id=arp.user_id
                WHERE wtu.tester_id IS NULL AND arp.workshop_id=$workshop_id AND arp.workshop_session='$workshop_session' ";
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
                }
            if($trainee_id !="0"){
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
        }else{
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,";
                if($workshop_session=="PRE"){
                     $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
                }else{
                     $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
                }
                $query .= " FROM trainee_result AS ls INNER JOIN question_topic qt ON qt.id=ls.topic_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
                LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id 
                LEFT JOIN device_users AS du ON du.user_id=ls.trainee_id ";
                $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
                if ($trainee_region_id != "0") {
                    $query .= " AND  du.region_id = ".$trainee_region_id;
                }
                if($trainee_id !="0"){
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
    
    function getTrainee($workshop_id,$workshop_session){
        $querystr = "Select distinct(wru.user_id) as user_id,concat(du.firstname,' ',du.lastname) as username "
                . " from workshop_registered_users wru"
                . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wru.workshop_id AND wtu.tester_id=wru.user_id "
                . " inner join device_users du on du.user_id=wru.user_id where wtu.tester_id IS NULL AND wru.workshop_id=" . $workshop_id;
        if ($workshop_session != "") {
            $querystr .=" AND wru.workshop_session='" . $workshop_session."'";
        }
        $result = $this->db->query($querystr);
        return $result->result();
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
