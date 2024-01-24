<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_trainee_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function best_post_accuracy($trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT ls.workshop_id,IFNULL(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),'NP') AS post_accuracy,
                (SUM(ls.post_correct)*100/ SUM(ls.post_total_questions)) AS post_order 
                FROM (
                SELECT w.workshop_id,sum(w.post_correct) as post_correct,sum(w.post_total_questions) as post_total_questions
                FROM trainee_result AS w
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id
                LEFT JOIN workshop AS wrk ON wrk.id=w.workshop_id
                WHERE wtu.tester_id IS NULL AND w.post_played_questions>0 AND w.trainer_id =$trainer_id ";
                if($wrktype_id !='0'){
                    $query .=" AND wrk.workshop_type=".$wrktype_id;   
                }
                if($wsubtype_id !=""){
                    $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND wrk.region =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
                }
                if (!$WRightsFlag && $trainer_id != $login_id) {
                    $query .= " AND w.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
                }
                $query .= " group by w.workshop_id 
                UNION ALL
                SELECT arp.workshop_id,SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
                FROM atom_results AS arp
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                WHERE wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                    AND arp.trainer_id =$trainer_id ";
                if($wrktype_id !='0'){
                    $query .=" AND w.workshop_type=".$wrktype_id;   
                }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND w.region =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
                if (!$WRightsFlag && $trainer_id != $login_id) {
                    $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
                }
                $query .= "
                group by arp.workshop_id ) AS ls ";
        $query .= " group by ls.workshop_id ORDER BY post_order DESC LIMIT 0,1";

        $result = $this->db->query($query);
        $records = $result->row();
        $accuracy = 0;
        if (count((array)$records) > 0) {
            $accuracy = $records->post_accuracy;
        }
        return $accuracy;
    }

    public function overall_accuracy($trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT 
                IFNULL(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2),'NP') AS post_accuracy 
                FROM (
                SELECT sum(w.post_correct) as post_correct,sum(w.post_total_questions) as post_total_questions
                FROM trainee_result AS w
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=w.workshop_id AND wtu.tester_id=w.trainee_id
                LEFT JOIN workshop AS wrk ON wrk.id=w.workshop_id
                WHERE wtu.tester_id IS NULL AND w.post_played_questions>0 AND w.trainer_id =$trainer_id ";
                if($wrktype_id !='0'){
                    $query .=" AND wrk.workshop_type=".$wrktype_id;   
                }
                if($wsubtype_id !=""){
                    $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND wrk.region =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
                }
        if (!$WRightsFlag && $trainer_id != $login_id) {
            $query .= " AND w.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $query .= " UNION ALL
                SELECT  SUM(arp.is_correct) AS post_correct, COUNT(arp.question_id) AS post_total_questions
                FROM atom_results AS arp
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                WHERE wtu.tester_id IS NULL AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                    AND arp.trainer_id =$trainer_id ";
                if($wrktype_id !='0'){
                    $query .=" AND w.workshop_type=".$wrktype_id;   
                }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND w.region =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
        if (!$WRightsFlag && $trainer_id != $login_id) {
            $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $query .= " ) AS ls ";
        $ObjectSet = $this->db->query($query);
        $RowSet = $ObjectSet->row();
        $accuracy = 0;
        if (count((array)$RowSet) > 0) {
            $accuracy = $RowSet->post_accuracy;
        }
        return $accuracy;
    }

    public function get_HighestLowestAvgCE($Company_id, $StartDate = "", $EndDate = "", $trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $login_id = $this->mw_session['user_id'];
        $query = "select ifnull(max(ls.ce),0) as maxce,"
                . " ifnull(min(ls.ce),0) as mince,"
                . " IFNULL(format(format(sum(ls.post_correct)*100/sum(ls.post_total_questions),2) -format(sum(ls.pre_correct)*100/sum(ls.pre_total_questions),2),2 ),0) as avgce "
                . " FROM trainer_result as ls  "
                . " LEFT JOIN workshop AS w ON w.id=ls.workshop_id "
                . " where ls.company_id=$Company_id AND ls.trainer_id=$trainer_id";
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }    
        if (!$WRightsFlag && $trainer_id != $login_id) {
            $query .= " AND ls.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        if ($StartDate != "" && $EndDate != "") {
            $query .= " AND ls.workshop_date between '$StartDate' AND '$EndDate'";
        }
        
        $result = $this->db->query($query);
        $MaxCE = 0;
        $MinCE = 0;
        $Avg = 0;
        $RowSet = $result->row();
        if (count((array)$RowSet) > 0) {
            $MaxCE = $RowSet->maxce;
            if ($MaxCE != $RowSet->mince) {
                $MinCE = $RowSet->mince;
            }
            $Avg = $RowSet->avgce;
        }
        $data['MaxCE'] = $MaxCE;
        $data['MinCE'] = $MinCE;
        $data['Avg'] = $Avg;
        return $data;
    }

    public function top_five_topics($company_id, $trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        //AND w.topic_id IN(SELECT distinct topic_id FROM atom_results WHERE workshop_session='POST' AND company_id=$company_id )
        $query = " SELECT qt.description AS topic,a.topic_id,FORMAT(sum(a.post_correct)*100/sum(a.post_total_questions) -sum(a.pre_correct)*100/sum(a.pre_total_questions),2) as ce,
                sum(a.post_correct)*100/sum(a.post_total_questions) -sum(a.pre_correct)*100/sum(a.pre_total_questions) as orderce
                FROM topicwise_result as a 
                INNER JOIN question_topic qt ON qt.id=a.topic_id
                LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id
                where  a.company_id=$company_id  and a.trainer_id=$trainer_id and a.pre_played_questions >0 AND a.post_played_questions>0";
                if($wrktype_id !='0'){
                    $query .=" AND wrk.workshop_type=".$wrktype_id;   
                }
                if($wsubtype_id !=""){
                    $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND wrk.region =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
                }
                if (!$WRightsFlag && $trainer_id != $login_id) {
                    $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
                }
                $query .= " group by a.topic_id 
                order by orderce desc,topic limit 0,5 ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function bottom_five_topics($company_id, $trainer_id, $top_five_topic_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = " SELECT qt.description AS topic,a.topic_id,FORMAT(sum(a.post_correct)*100/sum(a.post_total_questions) -sum(a.pre_correct)*100/sum(a.pre_total_questions),2) as ce,
                sum(a.post_correct)*100/sum(a.post_total_questions) -sum(a.pre_correct)*100/sum(a.pre_total_questions) as orderce
                FROM topicwise_result as a 
                INNER JOIN question_topic qt ON qt.id=a.topic_id
                LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id
                where  a.company_id=$company_id  and a.trainer_id=$trainer_id and a.pre_played_questions >0 AND a.post_played_questions>0 
                AND a.topic_id NOT IN ($top_five_topic_id)";
            if($wrktype_id !='0'){
                $query .=" AND wrk.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND wrk.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }    
                if (!$WRightsFlag && $trainer_id != $login_id) {
                    $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
                }
        if (!$WRightsFlag && $trainer_id != $login_id) {
                    $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
                $query .= " group by a.topic_id 
                order by orderce asc,topic limit 0,5 ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function wksh_histogram_range($trainer_id, $workshop_session = "PRE", $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.workshop_id != '' ,COUNT(tr.workshop_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        $query .= "SELECT a.workshop_id, FORMAT(SUM(a.correct)*100/sum(a.total_questions),0) as average_accuracy FROM(";
        if ($workshop_session == "PRE") {
            $query .= " SELECT a.workshop_id, SUM(a.pre_correct) as correct, sum(a.pre_total_questions) as total_questions FROM "
                    . " trainee_result as a "
                    . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id"
                    . " LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id "
                    . " where wtu.tester_id IS NULL AND a.trainer_id=" . $trainer_id;
//            if ($region_id != "") {
//                $query .= " AND a.region_id='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND a.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND a.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND a.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by a.workshop_id "
                   . " UNION ALL 
                        SELECT arp.workshop_id, SUM(arp.is_correct) AS correct, COUNT(arp.question_id) AS total_questions
                        FROM atom_results AS arp 
                        INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                where wtu.tester_id IS NULL AND arp.trainer_id=$trainer_id "
                . " AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'";
//            if ($region_id != "") {
//                $query .= " AND w.region='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND w.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by arp.workshop_id";
        } else {
            $query .= "SELECT a.workshop_id, SUM(a.post_correct) as correct, sum(a.post_total_questions) as total_questions FROM "
                . " trainee_result as a "
                . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id "
                . " LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id "    
                . " where wtu.tester_id IS NULL AND a.trainer_id=" . $trainer_id;
//            if ($region_id != "") {
//                $query .= " AND a.region_id='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND a.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND a.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND a.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by a.workshop_id UNION ALL SELECT arp.workshop_id, SUM(arp.is_correct) AS correct, COUNT(arp.question_id) AS total_questions
                FROM atom_results AS arp INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id 
                where wtu.tester_id IS NULL AND arp.trainer_id=$trainer_id "
                . " AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'";
//            if ($region_id != "") {
//                $query .= " AND w.region='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND w.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by arp.workshop_id";
        }
        $query .= ") as a group by a.workshop_id )as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function topic_histogram_range($trainer_id, $workshop_session = "PRE",$WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.topic_id != '' ,COUNT(tr.topic_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        $query .= "SELECT a.topic_id, FORMAT(SUM(a.correct)*100/sum(a.total_questions),0) as average_accuracy FROM(";
        if ($workshop_session == "PRE") {
            $query .= "SELECT a.topic_id, SUM(a.pre_correct) as correct, sum(a.pre_total_questions) as total_questions FROM "
                    . " trainee_result as a "
                    . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id"
                    . " LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id "
                    . " where wtu.tester_id IS NULL AND a.trainer_id=" . $trainer_id;
//            if ($region_id != "") {
//                $query .= " AND a.region_id='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND a.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND a.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND a.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by a.topic_id"
                    . " UNION ALL SELECT arp.topic_id, SUM(arp.is_correct) AS correct, COUNT(arp.question_id) AS total_questions
                FROM atom_results AS arp INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                where wtu.tester_id IS NULL AND arp.trainer_id=$trainer_id "
                    . " AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'";
//            if ($region_id != "") {
//                $query .= " AND w.region='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND w.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by arp.topic_id";
        } else {
            $query .= "SELECT a.topic_id, SUM(a.post_correct) as correct, sum(a.post_total_questions) as total_questions FROM "
                    . " trainee_result as a "
                    . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id "
                    . " LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id "
                    . " where wtu.tester_id IS NULL AND a.trainer_id=" . $trainer_id;
//            if ($region_id != "") {
//                $query .= " AND a.region_id='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND a.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND a.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND a.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by a.topic_id UNION ALL SELECT arp.topic_id, SUM(arp.is_correct) AS correct, COUNT(arp.question_id) AS total_questions
                FROM atom_results AS arp INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                where wtu.tester_id IS NULL AND arp.trainer_id=$trainer_id "
                . " AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'";
//            if ($region_id != "") {
//                $query .= " AND w.region='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND w.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by arp.topic_id";
        }
        $query .= ") as a group by a.topic_id )as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainee_histogram_range($trainer_id, $workshop_session = "PRE", $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "select hr.from_range,hr.to_range,if(tr.trainee_id != '' ,COUNT(tr.trainee_id),null) as TrainerCount 
                FROM histogram_range as hr LEFT JOIN (";
        $query .= "SELECT a.trainee_id, FORMAT(SUM(a.correct)*100/sum(a.total_questions),0) as average_accuracy FROM(";
        if ($workshop_session == "PRE") {
            $query .= "SELECT a.trainee_id, SUM(a.pre_correct) as correct, sum(a.pre_total_questions) as total_questions "
                    . " FROM "
                    . " trainee_result as a "
                    . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id"
                    . " LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id "
                    . " where wtu.tester_id IS NULL AND a.trainer_id=" . $trainer_id;
//            if ($region_id != "") {
//                $query .= " AND a.region_id='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND a.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND a.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND a.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by a.trainee_id UNION ALL SELECT arp.user_id as trainee_id, SUM(arp.is_correct) AS correct, COUNT(arp.question_id) AS total_questions
                FROM atom_results AS arp INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                where wtu.tester_id IS NULL AND arp.trainer_id=$trainer_id";
//            if ($region_id != "") {
//                $query .= " AND w.region='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND w.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " AND arp.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'"
                    . " group by arp.user_id";
        } else {
            $query .= "SELECT a.trainee_id, SUM(a.post_correct) as correct, sum(a.post_total_questions) as total_questions FROM "
                    . " trainee_result as a "
                    . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.trainee_id "
                    . " LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id "
                    . " where wtu.tester_id IS NULL AND a.trainer_id=" . $trainer_id;
//            if ($region_id != "") {
//                $query .= " AND a.region_id='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND a.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND a.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND a.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND a.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by a.trainee_id UNION ALL SELECT arp.user_id as trainee_id, SUM(arp.is_correct) AS correct, COUNT(arp.question_id) AS total_questions
                FROM atom_results AS arp INNER JOIN workshop AS w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                where wtu.tester_id IS NULL AND arp.trainer_id=$trainer_id "
                . " AND arp.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'";
//            if ($region_id != "") {
//                $query .= " AND w.region='" . $region_id . "'";
//            }
//            if ($wtype_id != "") {
//                $query .= " AND w.workshop_type='" . $wtype_id . "'";
//            }
            if($wrktype_id !='0'){
                $query .=" AND w.workshop_type=".$wrktype_id;   
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND w.region =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
            if (!$WRightsFlag && $trainer_id != $login_id) {
                $query .= " AND arp.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
            }
            $query .= " group by arp.user_id";
        }
        $query .= ") as a group by a.trainee_id )as tr on (tr.average_accuracy between hr.from_range and hr.to_range) 
          group by hr.from_range ";
        //echo $query;
        //exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function supervisor_index_weekly_monthly($company_id = '',$StartDate, $EndDate, $Trainer_id = "", $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $login_id = $this->mw_session['user_id'];
        $lcWhere = "";
//        if ($region_id != "") {
//            $lcWhere .= " AND wr.region_id='" . $region_id . "'";
//        }
//        if ($wtype_id != "") {
//            $lcWhere .= " AND wr.workshop_type='" . $wtype_id . "'";
//        }
        if ($Trainer_id != "") {
            $lcWhere .= " AND wr.trainer_id='" . $Trainer_id . "'";
        }
        if (!$WRightsFlag && $Trainer_id != $login_id) {
            $lcWhere .= " AND wr.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $query = " SELECT w.start_date,DATE_FORMAT(w.start_date,'%d') wday,
            format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ) as avg_ce
                FROM trainer_result wr left join workshop w
                on w.id = wr.workshop_id  and w.company_id = wr.company_id
                where wr.company_id = $company_id ";
                if($wrktype_id !=""){
                    $query .= " AND wr.workshop_type =".$wrktype_id ;
                }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND wr.region_id =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
                $query .= " and w.start_date BETWEEN '$StartDate' AND '$EndDate' $lcWhere                    
                GROUP BY w.start_date ";

        //echo $query;
        $result = $this->db->query($query);
        $CE = $result->result();
        $ResultArray = array();
        if (count((array)$CE) > 0) {
            foreach ($CE as $value) {
                $ResultArray[$value->wday] = $value->avg_ce;
            }
        }
        return $ResultArray;
    }

    public function supervisor_index_yearly($company_id = '', $StartDate = '', $EndDate = '', $Trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $login_id = $this->mw_session['user_id'];
        $lcWhere = "";
//        if ($region_id != "") {
//            $lcWhere .= " AND wr.region_id='" . $region_id . "'";
//        }
//        if ($wtype_id != "") {
//            $lcWhere .= " AND wr.workshop_type='" . $wtype_id . "'";
//        }
        if ($Trainer_id != "") {
            $lcWhere .= " AND wr.trainer_id='" . $Trainer_id . "'";
        }
        if (!$WRightsFlag && $Trainer_id != $login_id) {
            $lcWhere .= " AND wr.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $query = "SELECT month(w.start_date) as wmonth,DATE_FORMAT(w.start_date,'%d') wday,
            format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ) as avg_ce
                FROM trainer_result wr
                left join workshop w
                on w.id = wr.workshop_id
                where wr.company_id = $company_id ";
                if($wrktype_id !=""){
                    $query .= " AND wr.workshop_type =".$wrktype_id ;
                }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND wr.region_id =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
        $query .= " and w.start_date BETWEEN '$StartDate' AND '$EndDate' $lcWhere
                GROUP BY month(w.start_date) ";

        $result = $this->db->query($query);
        $AvgCE = $result->result();
        $ResultArray = array();
        if (count((array)$AvgCE) > 0) {
            foreach ($AvgCE as $value) {
                $ResultArray[$value->wmonth] = $value->avg_ce;
            }
        }
        return $ResultArray;
    }

    public function histogram_range() {
        $query = "SELECT * FROM histogram_range ORDER BY from_range,to_range";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function workshop_attended($company_id, $trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $login_id = $this->mw_session['user_id'];
        $query = " SELECT count(DISTINCT ls.workshop_id) as total,COUNT(DISTINCT topic_id) as total_topic 
                    FROM atom_results as ls
            LEFT JOIN workshop as w ON w.id=ls.workshop_id
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.user_id
                    WHERE wtu.tester_id IS NULL AND ls.company_id ='" . $company_id . "' AND ls.trainer_id='" . $trainer_id . "'";
                    
        if (!$WRightsFlag && $trainer_id != $login_id) {
            $query .= " AND ls.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        
        $result = $this->db->query($query);
        $records = $result->row();
        $datal['workshop_Attend'] = $records->total;
        $datal['total_topic'] = $records->total_topic;
        return $datal;
    }

    public function subtopic_trained($company_id, $trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $query = "SELECT COUNT(DISTINCT ls.subtopic_id) as total 
                    FROM atom_results as ls
                    LEFT JOIN workshop as w ON w.id=ls.workshop_id
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.user_id
                WHERE wtu.tester_id IS NULL AND ls.subtopic_id>0 AND ls.company_id ='" . $company_id . "' AND ls.trainer_id='" . $trainer_id . "'";
                    if($wrktype_id !='0'){
                        $query .=" AND w.workshop_type=".$wrktype_id;   
                    }
                    if($wsubtype_id !=""){
                        $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                    }
                    if($flt_region_id !="0"){
                        $query .= " AND w.region =".$flt_region_id ;
                    }
                    if($subregion_id !=""){
                        $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                    }
        $login_id = $this->mw_session['user_id'];
        if (!$WRightsFlag && $trainer_id != $login_id) {
            $query .= " AND ls.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $result = $this->db->query($query);
        $records = $result->row();
        return $records->total;
    }
    public function workshop_last_week($company_id, $trainer_id, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $system_date = date('Y-m-d h:i:s');
        $last_week_date = date('Y-m-d h:i:s', strtotime("-1 week"));
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT count(DISTINCT ar.workshop_id) as total FROM atom_results as ar
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id
                            INNER JOIN workshop as w ON ar.workshop_id = w.id AND
                            ((CONCAT(w.pre_start_date,' ',STR_TO_DATE(w.pre_start_time, '%l:%i %p')) BETWEEN '" . $last_week_date . "' AND  '" . $system_date . "')
                            OR 
                            (CONCAT(w.post_start_date,' ',STR_TO_DATE(w.post_start_time, '%l:%i %p')) BETWEEN '" . $last_week_date . "' AND  '" . $system_date . "'))
                WHERE wtu.tester_id IS NULL AND ar.company_id ='" . $company_id . "' AND ar.trainer_id='" . $trainer_id . "'";
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag && $trainer_id != $login_id) {
            $query .= " AND ar.workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $result = $this->db->query($query);
        $records = $result->row();
        return $records->total;
    }

    public function trainer_ce_histogram($company_id, $WeekStartDate = '', $WeekEndDate = '', $Trainer_id,$WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $lcWhere = "";
        $login_id = $this->mw_session['user_id'];
//        if ($region_id != "") {
//            $lcWhere .= " AND trs.region_id='" . $region_id . "'";
//        }
//        if ($wtype_id != "") {
//            $lcWhere .= " AND trs.workshop_type='" . $wtype_id . "'";
//        }
        if (!$WRightsFlag && $Trainer_id != $login_id) {
            $lcWhere .= " AND workshop_id IN(select workshop_id FROM temp_wrights where user_id= $login_id )";
        }
        $query = "select hr.from_range,hr.to_range, if(tr.workshop_id != '' ,COUNT(DISTINCT tr.workshop_id),null) WorkshopCount 
                    FROM histogram_cerange as hr 
                    LEFT JOIN 
                           (select format(trs.ce,0) as ce,trs.workshop_id from trainer_result trs
                            LEFT JOIN workshop as w ON w.id = trs.workshop_id
                            where trs.company_id=$company_id AND trs.trainer_id=$Trainer_id ";
                            if($wrktype_id !='0'){
                                $query .=" AND trs.workshop_type=".$wrktype_id;   
                            }
                            if($wsubtype_id !=""){
                                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                            }
                            if($flt_region_id !="0"){
                                $query .= " AND trs.region_id =".$flt_region_id ;
                            }
                            if($subregion_id !=""){
                                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                            }
        if ($WeekStartDate != '' && $WeekEndDate != '') {
                                $query .= " and trs.workshop_date between '$WeekStartDate' AND '$WeekEndDate' ";
        }
        $query .= " $lcWhere ) as tr 
            on (tr.ce BETWEEN hr.from_range AND hr.to_range) OR (tr.ce BETWEEN hr.to_range AND hr.from_range)
            group by hr.from_range ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getWorkshopType($company_id='',$trainer_id='') {
        $query = " select wq.workshop_id,w.workshop_type as wtype_id,wt.workshop_type 
                    from workshop_questions wq
                    left join workshop w
                    on w.id = wq.workshop_id                    
                    left join workshoptype_mst wt
                    on wt.id = w.workshop_type
                    where wq.company_id =". $company_id;
                    if($trainer_id !=''){
                       $query .= " and wq.trainer_id =". $trainer_id; 
}
                    $query .= " group by w.workshop_type ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getRegion($company_id='',$trainer_id='') {
        $query = " select wq.workshop_id,w.region as region_id,r.region_name 
                    from workshop_questions wq
                    left join workshop w
                    on w.id = wq.workshop_id
                    left join region r
                    on r.id = w.region                    
                    where wq.company_id =". $company_id;
                    if($trainer_id !=''){
                       $query .= " and wq.trainer_id =". $trainer_id; 
                    }    
                    $query .= " group by w.region ";
        $result = $this->db->query($query);
        return $result->result();
    }





























































































































    // Trainee Dashboard Start Here 
    public function workshop_attended_trainee($company_id, $user_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $query = "SELECT count(DISTINCT ar.workshop_id) as total 
            FROM atom_results  as ar
            LEFT JOIN workshop as w ON w.id=ar.workshop_id
            WHERE ar.company_id ='" . $company_id . "' AND ar.user_id='" . $user_id . "'";
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        $login_id = $this->mw_session['user_id'];
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $result = $this->db->query($query);
        $records = $result->row();
        $total = 0;
        if (count((array)$records) > 0) {
            $total = $records->total;
        }
        return $total;
    }

    public function Totaltopic_subtopic_answer($company_id, $user_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $query = "SELECT ifnull(COUNT(DISTINCT ar.topic_id),0) as topic,ifnull(COUNT(DISTINCT if(ar.subtopic_id>0,ar.subtopic_id,null )),0) as subtopic,
                    ifnull(COUNT(ar.question_id),0) as total_question,ifnull(sum(ar.is_correct),0) as correct_ans,
                    ifnull(sum(ar.is_wrong),0) as wrong_ans,ifnull(sum(ar.is_timeout),0) as timeout
                    FROM atom_results as ar LEFT JOIN workshop as w ON w.id=ar.workshop_id  
                    WHERE ar.company_id=" . $company_id . " AND ar.user_id=" . $user_id;
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        $login_id = $this->mw_session['user_id'];
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }

        $result = $this->db->query($query);
        $records = $result->row();
        return $records;
    }

    public function overall_PrePostAverage($company_id, $user_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT IFNULL(FORMAT(((SUM(a.pre_correct)*100)/ SUM(a.pre_total_questions)),2),'NP') AS pre_average,
                IFNULL(FORMAT(((SUM(a.post_correct)*100)/ SUM(a.post_total_questions)),2),'NP') AS post_average 
                FROM(
               select SUM(a.pre_correct) as pre_correct,SUM(a.pre_total_questions) as pre_total_questions,SUM(a.post_correct) as post_correct,
               SUM(a.post_total_questions) as post_total_questions FROM trainee_result AS a
               LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id 
               WHERE a.company_id=$company_id AND a.trainee_id =$user_id ";
        if($wrktype_id !='0'){
            $query .=" AND a.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND a.region_id =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " union all 
               SELECT SUM(arp.is_correct) AS pre_correct, COUNT(arp.question_id) AS pre_total_questions,0 as post_correct,
               0 as post_total_questions FROM atom_results as arp
               INNER JOIN workshop AS w ON w.id=arp.workshop_id 
               where arp.company_id=$company_id AND arp.user_id =$user_id AND arp.workshop_session='PRE' AND
               CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'";
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }

        $query .= " union all 
               SELECT 0 as pre_correct,0 as pre_total_questions,SUM(arp.is_correct) AS post_correct,
                COUNT(arp.question_id) AS post_total_questions FROM atom_results as arp
               INNER JOIN workshop AS w ON w.id=arp.workshop_id 
               where arp.company_id=$company_id AND arp.user_id =$user_id AND arp.workshop_session='POST' AND
               CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' ";
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " ) as a";
        $result = $this->db->query($query);
        $records = $result->row();
        return $records;
    }

    public function overall_PrePostResponse_time($company_id, $user_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $login_id = $this->mw_session['user_id'];
        $query = "select FORMAT(IFNULL((sum(ar.seconds)/count(ar.question_id)),0),2) as avgresponcetime 
                from  atom_results ar  
                LEFT JOIN workshop as w ON w.id=ar.workshop_id
                where ar.company_id =" . $company_id . " AND ar.user_id =" . $user_id;
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        //echo $query;exit;
        $result = $this->db->query($query);
        $records = $result->row();
        return $records;
    }

    public function top_five_workshop($company_id, $user_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT a.workshop_id,b.workshop_name,ifnull(format(post_result,2),'NP') as post_average
            FROM( SELECT a.workshop_id,sum(a.post_correct)*100/sum(a.post_total_questions) AS post_result
            FROM trainee_result AS a
            LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.trainee_id =$user_id  ";
        if($wrktype_id !='0'){
            $query .=" AND a.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND a.region_id =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by a.workshop_id UNION ALL
             SELECT arp.workshop_id,FORMAT(SUM(arp.is_correct)*100/ COUNT(arp.question_id),2) as post_average FROM atom_results as arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id 
            where arp.company_id=$company_id AND arp.user_id =$user_id AND arp.workshop_session='POST' AND
            CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' ";
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= "  group by arp.workshop_id) as a
            LEFT JOIN workshop AS b ON b.id=a.workshop_id
            ORDER BY post_result DESC
            LIMIT 0,5";
        //echo $query;
        //exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function bottom_five_workshop($company_id, $user_id, $top_five_wksh_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT a.workshop_id,b.workshop_name,ifnull(format(post_result,2),'NP') as post_average
            FROM( SELECT a.workshop_id,sum(a.post_correct)*100/sum(a.post_total_questions) AS post_result
            FROM trainee_result AS a
            LEFT JOIN workshop AS wrk ON wrk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.trainee_id =$user_id AND a.workshop_id NOT IN (" . $top_five_wksh_id . ")  ";
        if($wrktype_id !='0'){
            $query .=" AND a.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND wrk.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND a.region_id =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND wrk.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by a.workshop_id UNION ALL
             SELECT arp.workshop_id,FORMAT(SUM(arp.is_correct)*100/ COUNT(arp.question_id),2) as post_average FROM atom_results as arp
            INNER JOIN workshop AS w ON w.id=arp.workshop_id 
            where arp.company_id=$company_id AND arp.user_id =$user_id AND arp.workshop_session='POST' AND
            arp.workshop_id NOT IN (" . $top_five_wksh_id . ") AND
            CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' ";
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by arp.workshop_id
            ) as a
            LEFT JOIN workshop AS b ON b.id=a.workshop_id
            ORDER BY post_result asc LIMIT 0,5";
//        $query = "SELECT a.trainee_id,b.workshop_name,a.workshop_id,post_avg as post_average FROM trainee_result as a LEFT JOIN workshop as b ON b.id=a.workshop_id "
//            . " WHERE a.company_id=$company_id AND a.trainee_id =$user_id AND a.workshop_id NOT IN (".$top_five_wksh_id.")  "
//            . " order by post_average asc LIMIT 0,5 ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function wksh_histogram_range_trainee($company_id, $user_id, $WeekStartDate = '', $WeekEndDate = '', $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT hr.from_range,hr.to_range, COUNT(fs.workshop_id) WorkshopCount
                FROM histogram_range AS hr
                LEFT JOIN(
                SELECT a.workshop_id, format(sum(a.post_correct)*100/sum(a.post_total_questions),2) post_avg
                FROM trainee_result AS a
                LEFT JOIN workshop AS b ON b.id=a.workshop_id
                WHERE a.company_id=$company_id AND a.trainee_id =$user_id ";
//        if ($wtype_id != "") {
//            $query .= " AND a.workshop_type=" . $wtype_id;
//        }
        if($wrktype_id !='0'){
            $query .=" AND b.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND b.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND b.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND b.workshopsubregion_id =".$subregion_id ;
        }
        if ($WeekStartDate != '' && $WeekEndDate != '') {
            $query .= " and a.workshop_date between '$WeekStartDate' AND '$WeekEndDate' ";
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by a.workshop_id union all 
                SELECT arp.workshop_id, FORMAT(SUM(arp.is_correct)*100/ COUNT(arp.question_id),2) as post_avg
                FROM atom_results as arp
                INNER JOIN workshop AS w ON w.id=arp.workshop_id 
                where arp.company_id=$company_id AND arp.user_id =$user_id AND arp.workshop_session='POST' AND
                CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt' ";
//        if ($wtype_id != "") {
//            $query .= " AND w.workshop_type=" . $wtype_id;
//        }
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if ($WeekStartDate != '' && $WeekEndDate != '') {
            $query .= " and a.workshop_date between '$WeekStartDate' AND '$WeekEndDate' ";
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by arp.workshop_id)
                 AS fs ON format(fs.post_avg,0) BETWEEN hr.from_range AND hr.to_range
                GROUP BY hr.from_range";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function histogram_range_trainee() {
        $query = "SELECT * FROM histogram_range ORDER BY from_range,to_range";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function trainee_index_postaverage_weekly_monthly($company_id = '', $trainee_id = '',$StartDate, $EndDate, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT month(ar.PostDate) AS post_month, ar.postday, FORMAT(sum(ar.post_correct)*100/sum(ar.total_questions),2) AS post_avg
            FROM (SELECT b.post_start_date AS PostDate, DATE_FORMAT(b.post_start_date,'%d') postday, SUM(a.post_correct) AS post_correct,
            SUM(post_total_questions) AS total_questions 
            FROM trainee_result AS a 
            LEFT JOIN workshop AS b ON b.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.trainee_id =$trainee_id AND"
            . " CONCAT(b.post_start_date,' ', STR_TO_DATE(b.post_start_time, '%l:%i %p')) BETWEEN '$StartDate' AND '$EndDate'";
        
//        if ($wtype_id != "") {
//            $query .= " AND a.workshop_type=" . $wtype_id;
//        }
        if($wrktype_id !='0'){
            $query .=" AND b.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND b.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND b.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND b.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY b.post_start_date UNION ALL 
            SELECT  w.post_start_date AS PostDate,DATE_FORMAT(w.post_start_date,'%b') postday,SUM(arp.is_correct) AS post_correct,
            COUNT(arp.question_id) AS total_questions
            FROM atom_results as arp 
            INNER JOIN workshop AS w ON w.id=arp.workshop_id 
            where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='POST' AND
            CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
            AND CONCAT(w.post_start_date,' ', STR_TO_DATE(w.post_start_time, '%l:%i %p')) BETWEEN '$StartDate' AND '$EndDate' ";
//        if ($wtype_id != "") {
//            $query .= " AND w.workshop_type=" . $wtype_id;
//        }
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY w.post_start_date) AS ar group by ar.PostDate ORDER BY ar.PostDate";

        //\echo $query;exit;

        $result = $this->db->query($query);
        $post_average = $result->result();
        $ResultArray = array();
        if (count((array)$post_average) > 0) {
            foreach ($post_average as $value) {
                $ResultArray[$value->postday] = $value->post_avg;
            }
        }
        return $ResultArray;
    }

    public function trainee_index_post_yearly($company_id = '', $trainee_id = '', $StartDate = '', $EndDate = '', $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT ar.PostDate AS post_month, ar.postday, FORMAT(sum(ar.post_correct)*100/sum(ar.total_questions),2) AS post_avg
            FROM (SELECT  MONTH(b.post_start_date) AS PostDate, DATE_FORMAT(b.post_start_date,'%b') postday, SUM(a.post_correct) AS post_correct,
            SUM(post_total_questions) AS total_questions 
            FROM trainee_result AS a LEFT JOIN workshop AS b ON b.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.trainee_id =$trainee_id AND"
            . " CONCAT(b.post_start_date,' ', STR_TO_DATE(b.post_start_time, '%l:%i %p')) BETWEEN '$StartDate' AND '$EndDate'";
//        if ($wtype_id != "") {
//            $query .= " AND a.workshop_type=" . $wtype_id;
//        }
        if($wrktype_id !='0'){
            $query .=" AND b.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND b.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND b.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND b.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY MONTH(b.post_start_date)
            UNION ALL 
            SELECT  MONTH(w.post_start_date) AS PostDate,DATE_FORMAT(w.post_start_date,'%b') postday,SUM(arp.is_correct) AS post_correct,
            COUNT(arp.question_id) AS total_questions
            FROM atom_results as arp INNER JOIN workshop AS w ON w.id=arp.workshop_id 
            where arp.company_id=$company_id AND arp.user_id =$trainee_id AND arp.workshop_session='POST' AND
            CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
            AND CONCAT(w.post_start_date,' ', STR_TO_DATE(w.post_start_time, '%l:%i %p')) BETWEEN '$StartDate' AND '$EndDate' ";
//        if ($wtype_id != "") {
//            $query .= " AND w.workshop_type=" . $wtype_id;
//        }
        if($wrktype_id !='0'){
            $query .=" AND w.workshop_type=".$wrktype_id;   
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if (!$WRightsFlag) {
            $query .= " AND arp.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " GROUP BY MONTH(w.post_start_date)) AS ar group by ar.PostDate ORDER BY ar.PostDate";
        $result = $this->db->query($query);
        $post_average = $result->result();
        $ResultArray = array();
        if (count((array)$post_average) > 0) {
            foreach ($post_average as $value) {
                $ResultArray[$value->post_month] = $value->post_avg;
            }
        }
        return $ResultArray;
    }

    public function getDistinctWorkshopYear($Company_id) {
        $query = "SELECT distinct DATE_FORMAT(start_date,'%Y') workshop_years FROM workshop"
                . " where company_id= $Company_id ORDER BY workshop_years";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getWorkshopType_trainee($company_id='',$trainee_id='') {
        $query = " select ar.workshop_id,w.workshop_type as wtype_id,wt.workshop_type 
                    from atom_results ar
                    left join workshop w
                    on w.id = ar.workshop_id                    
                    left join workshoptype_mst wt
                    on wt.id = w.workshop_type
                    where ar.company_id =". $company_id;
                    if($trainee_id !=''){
                       $query .= " and ar.user_id =". $trainee_id; 
                    }    
                    $query .= " group by w.workshop_type ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getRegion_trainee($company_id='',$trainee_id='') {
        $query = " select ar.workshop_id,w.region as region_id,r.region_name 
                    from atom_results ar
                    left join workshop w
                    on w.id = ar.workshop_id
                    left join region r
                    on r.id = w.region                    
                    where ar.company_id =". $company_id;
                    if($trainee_id !=''){
                       $query .= " and ar.user_id =". $trainee_id; 
                    }    
                    $query .= " group by w.region ";
        $result = $this->db->query($query);
        return $result->result();
    }
    // Trainee Dashboard end Here
}
