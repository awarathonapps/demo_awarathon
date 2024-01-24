<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function workshop_attended($company_id, $user_id, $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
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

    public function wksh_histogram_range($company_id, $user_id, $WeekStartDate = '', $WeekEndDate = '', $RightsFlag, $WRightsFlag,$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
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

    public function histogram_range() {
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
    public function getWorkshopType($company_id='',$trainee_id='') {
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
    public function getRegion($company_id='',$trainee_id='') {
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
}
