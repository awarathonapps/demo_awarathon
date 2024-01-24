<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function get_Total_Assessment($Company_id, $start_date = '', $end_date = '', $region_id = '', $store_id = '', $supervisor_id, $report_type) {
        $cond="";
        if ($start_date != '' && $end_date != '') {
            $cond .=" AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .=" OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id =" . $region_id;
        }
        if($supervisor_id !=''){
            $cond .= " AND asm.trainer_id =" . $supervisor_id;
        }
        if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }
        if($report_type == "2"){    
             $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM assessment_trainer_weights art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    LEFT JOIN assessment_managers asm ON asm.trainer_id=art.trainer_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id "
                . " where  am.company_id =" . $Company_id . $cond;
        }else if($report_type=="1"){
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                    FROM ai_subparameter_score  art
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                        LEFT JOIN assessment_managers asm ON asm.assessment_id=am.id
                        LEFT JOIN device_users du ON du.user_id=art.user_id "
                . " where  am.company_id =" . $Company_id . $cond;
        }else{
            $query = "SELECT count(total) as total_assessment FROM (SELECT count(total_assessment) as total FROM (SELECT DISTINCT am.id as total_assessment 
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers asm ON asm.trainer_id=art.trainer_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    WHERE 1=1 $cond
                UNION ALL 
                    SELECT DISTINCT am.id as total_assessment 
                    FROM ai_subparameter_score art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=am.id
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    WHERE 1=1 $cond
                    )as main GROUP BY total_assessment
            ) as main2";
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array)$RowSet) > 0) {
            $TotalASM = $RowSet->total_assessment;
        }
        return $TotalASM;
    }
    public function get_Candidate_Assessment($Company_id, $question_type, $report_by, $start_date = '', $end_date = '', $region_id = '', $store_id = '', $report_type, $supervisor_id) {
        $cond="";
        if ($start_date != '' && $end_date != '') {
            $cond .=" AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .=" OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id =" . $region_id;
        }
        if($supervisor_id !=''){
            $cond .= " AND asm.trainer_id =" . $supervisor_id;
        }
        if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }
        if($report_type=="2"){
            $query = "SELECT count(distinct ar.user_id) as assessment_candidate FROM assessment_final_results ar
                    LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=ar.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=ar.user_id WHERE 1=1 $cond";
       }elseif($report_type==1 || $report_type="1"){
            $query = "SELECT  count(distinct ar.user_id) as assessment_candidate 
            FROM ai_subparameter_score ar
            LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
            LEFT JOIN assessment_managers asm ON asm.assessment_id=ar.assessment_id 
            LEFT JOIN device_users du ON du.user_id=ar.user_id WHERE 1=1 $cond";
            
        }else{
            $query= "SELECT SUM(main.assessment_candidate) as assessment_candidate FROM
            (SELECT count(ar.user_id) as assessment_candidate FROM assessment_final_results ar
                    LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
                    
                    LEFT JOIN device_users du ON du.user_id=ar.user_id WHERE 1=1 $cond
                UNION ALL
                SELECT count(distinct ar.user_id) as assessment_candidate 
                FROM ai_subparameter_score ar
                LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=ar.assessment_id 
                LEFT JOIN device_users du ON du.user_id=ar.user_id WHERE 1=1 $cond 
            )as main";
         
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array)$RowSet) > 0) {
            $TotalASM = $RowSet->assessment_candidate;
        }
        return $TotalASM;
    }
    public function get_MaxMin_Accuracy($Company_id, $start_date = '', $end_date = '', $report_by, $region_id = '', $store_id = '') {
        $query = " SELECT MAX(a.result) AS max_accuracy,IF(COUNT(a.result) > 1,MIN(a.result),0) AS min_accuracy
                FROM(
                SELECT IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) AS result,
                SUM(art.score)/ SUM(art.weight_value) AS ord_res                    
                FROM assessment_trainer_weights art  
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                INNER JOIN device_users du ON du.user_id=art.user_id 
                WHERE 1=1 ";
        if ($start_date != '' && $end_date != '') {
            $query .=" AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        if ($report_by == 1) {
            $query .= " group by art.parameter_id ";
        } else {
            $query .= " group by art.assessment_id ";
        }
        $query .= " ) as a ";
        $result = $this->db->query($query);
        $RowSet = $result->row();
        return $RowSet;
    }
    public function get_Average_Accuracy($Company_id,$report_by, $start_date = '', $end_date = '', $region_id = '', $store_id = '', $report_type, $supervisor_id, $assessment_id1) {
        $cond="";$assmng="";
        if ($start_date != '' && $end_date != '') {
            $cond .=" AND (date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .=" OR date(b.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }
        if ($assessment_id1 != '') {
            $cond .= " AND a.assessment_id =" . $assessment_id1;
        }
        if($supervisor_id !=''){
            $cond .= " AND asm.trainer_id =" . $supervisor_id;
            $assmng =" LEFT JOIN assessment_managers asm ON asm.assessment_id=a.assessment_id ";
        }
        if($report_type=="2"){//Manual
            if ($report_by==0) {
                $query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0) as avg_result FROM assessment_final_results as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;
                
                $query .= " WHERE 1=1";
            }elseif($report_by==1){ 
                $query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0)as avg_result FROM assessment_trainer_weights as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;
                $query .= " WHERE 1=1";
            }
            $query .= $cond;
        }elseif($report_type=="1"){
            $query = "SELECT ifnull(FORMAT(avg(avg_ass_result),2),0) as avg_result FROM (
                SELECT ifnull(FORMAT(avg(a.score),2),0)as avg_ass_result FROM ai_subparameter_score as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;
                $query .= " WHERE a.parameter_type='parameter' AND 1=1 ";
            $query .= $cond;
            if ($report_by == 0) {
                $query .= " group by a.assessment_id";
            }if($report_by==1){
                $query .= " group by a.parameter_id";   
            }
            $query .= ") as main";
        }else{
            if($report_by==0){
                $query="SELECT FORMAT(SUM(main.avg_result)/count(main.avg_result),2) as avg_result FROM 
                (SELECT FORMAT(avg(a.accuracy),2) as avg_result FROM assessment_final_results as a 
                            LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                            LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;
                            $query .=" where 1=1 ".$cond." 
                        UNION ALL
                        SELECT FORMAT(avg(a.score),2) as avg_result FROM ai_subparameter_score as a 
                            LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                            LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;
                            $query .=" WHERE a.parameter_type='parameter' AND 1=1 ".$cond." group by a.assessment_id               
                ) as main;";
            }else{
                $query="SELECT FORMAT(SUM(main.avg_result)/count(main.avg_result),2) as avg_result FROM 
                    (SELECT ifnull(FORMAT(avg(a.accuracy),2),0)as avg_result FROM assessment_trainer_weights as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;	
                    $query .=" WHERE 1=1 ".$cond." 
                UNION ALL
                    SELECT ifnull(FORMAT(avg(a.score),2),0)as avg_result FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN device_users du ON du.user_id=a.user_id ".$assmng;
                    $query .=" WHERE a.parameter_type='parameter'
                    AND 1=1 ".$cond." group by a.parameter_id) as main;";
            }
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        //return $RowSet->assessment_candidate;
        //$RowSet = $result->row();
        $TotalAccuracy = 0;
        if (count((array)$RowSet) > 0) {
            $TotalAccuracy = $RowSet->avg_result;
        }
        return $TotalAccuracy;
    }
    public function get_top_five_parameter($Company_id, $report_by, $SDate = '', $EDate = '', $region_id = '', $store_id = '', $report_type, $assessment_id1, $supervisor_id) {
        $cond="";  $mng="";
        if ($SDate != '' && $EDate != '') {
            $cond .=" AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .=" OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }
        if ($assessment_id1 != '') {
            $cond .= " AND a.assessment_id =" . $assessment_id1;
        }
        if($supervisor_id !=''){
            $cond .= " AND asm.trainer_id =" . $supervisor_id;
            if ($report_by == 0) {
                $mng = " LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id ";
            }else{
                $mng = " LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id ";
            }
        } 

        if($report_type=="2")
        {
            if ($report_by == 0) {
                $query = "
                SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy)
                as order_wt,b.assessment FROM assessment_final_results as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng."
                  
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }else{
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."
                
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }
            $query .= $cond;
            if ($report_by == 0) {
                $query .= " group by a.assessment_id order by order_wt desc limit 0,5 ";
            }else{
                $query .= " group by a.parameter_id order by order_wt desc limit 0,5 ";
            }
        }elseif($report_type=="1"){ //AI
            if ($report_by == 0) {
                $query = "SELECT distinct a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng."
                        
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                WHERE parameter_type='parameter' AND 1=1 ";
            }else{
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."
                  
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter'
                AND 1=1";
            }
            $query .= $cond;
            if ($report_by == 0) {
                $query .= " group by  a.assessment_id order by order_wt desc limit 0,5 ";
            }else{
                $query .= " group by  a.parameter_id order by order_wt desc limit 0,5 ";
            }
        }else{   //AI and Manual
            if($report_by==0)
            {
                $query="SELECT main.assessment_id, main.result, main.order_wt, main.assessment  FROM 
                (SELECT a.assessment_id, ifnull(FORMAT(avg(a.accuracy),2),0) as result,avg(a.accuracy) as order_wt,b.assessment FROM assessment_final_results as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng." 
                            
                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1 ".$cond." 
                    group by a.assessment_id
                UNION ALL
                    SELECT a.assessment_id, ifnull(FORMAT(avg(a.score),2),0) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng." 
                            
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE parameter_type='parameter' AND 1=1 ".$cond." 
                    group by a.assessment_id
                ) as main group by main.assessment_id order by main.order_wt asc limit 0,5;";
            }
            else
            {
                $query="SELECT main.assessment_id, main.parameter_id, main.result, main.order_wt, main.parameter  FROM 
                (SELECT a.assessment_id,a.parameter_id, ifnull(FORMAT(avg(a.accuracy),2),0) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                    FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng." 
                     
                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1 ".$cond." 
                    group by a.parameter_id
                UNION ALL
                    SELECT a.assessment_id,a.parameter_id, ifnull(FORMAT(avg(a.score),2),0) as result,avg(a.score) as order_wt,pm.description as parameter
                    FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."  
                     
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter' AND 1=1 ".$cond." 
                    group by a.parameter_id
                    ) as main group by  main.parameter_id order by main.order_wt desc limit 0,5;";
            }
        }   
        
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_bottom_five_parameter($Company_id, $report_by, $top_five_para_id, $SDate = '', $EDate = '', $region_id = '', $store_id = '', $report_type, $assessment_id1, $supervisor_id) {
        $cond="";$mng="";
        if ($SDate != '' && $EDate != '') {
            $cond .=" AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .=" OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
       if ($region_id != '') {
            $cond .= " AND du.region_id =" . $region_id;
        }
       if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }
        if ($report_by == 1) {
            $cond .= " AND pm.id NOT IN (" . $top_five_para_id . ") ";
        } else {
            $cond .= " AND a.assessment_id NOT IN (" . $top_five_para_id . ") ";
        }
        if ($assessment_id1 != '') {
            $cond .= " AND a.assessment_id =" . $assessment_id1;
        }
        if($supervisor_id !=''){
            $cond .= " AND asm.trainer_id =" . $supervisor_id;
            if ($report_by == 0) {
                $mng = " LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id ";
            }else{
                $mng = " LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id ";
            }
        } 
        if($report_type=="2")
        {
            if ($report_by == 0) {
                $query = "SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy)
                as order_wt,b.assessment FROM assessment_final_results as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng."  
                      
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }else{
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."  
                
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }
            $query .= $cond;
            if ($report_by == 0) {
                $query .= " group by a.assessment_id order by order_wt asc limit 0,5 ";
            }else{
                $query .= " group by a.parameter_id order by order_wt asc limit 0,5 ";
            }
        }elseif($report_type=="1"){  //AI
            if ($report_by == 0) {
                $query = "SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng."  
                         
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                WHERE parameter_type='parameter' AND 1=1";
            }else{
                $query = "SELECT distinct a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."  
                 
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter'
                AND 1=1";
            }
            $query .= $cond;
            if ($report_by == 0) {
                $query .= " group by a.user_id, a.assessment_id order by order_wt asc limit 0,5 ";
            }else{
                $query .= " group by a.user_id, a.parameter_id order by order_wt asc limit 0,5 ";
            }
        }else{   //AI and Manual
            if($report_by==0)
            {
                $query="SELECT main.assessment_id, main.result, main.order_wt, main.assessment  FROM 
                (SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,b.assessment FROM assessment_final_results as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng."  
                            
                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1 ".$cond." 
                    group by a.assessment_id
                UNION ALL
                    SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id ".$mng."  
                            
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE parameter_type='parameter' AND 1=1 ".$cond." 
                    group by a.user_id, a.assessment_id                
                    ) as main group by main.assessment_id order by main.order_wt asc limit 0,5;";
            }
            else
            {
                $query="SELECT main.assessment_id, main.result, main.order_wt, main.parameter  FROM 
                (SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                    FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."  

                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1 ".$cond." 
                    group by a.parameter_id
                UNION ALL
                    SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                    FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id ".$mng."  
                     
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter' AND 1=1 ".$cond." 
                    group by a.user_id, a.parameter_id
                ) as main group by main.parameter_id order by main.order_wt asc limit 0,5;";
            }   
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function overall_result_parameter($isoverall, $passrange_from, $passrange_to, $failrange_from, $failrange_to, $SDate = '', $EDate = '', $region_id = '', $assessment_string = '', $step = 0, $store_id = '', $report_type) {
        $lcwhere = "";
        if ($SDate != '' && $EDate != '') {
            $lcwhere .=" AND (date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $lcwhere .=" OR date(am.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
        if ($region_id != '') {
            $lcwhere .= " AND du.region_id in(" . $region_id . ")";
        }   
        if ($store_id != '') {
            $lcwhere .= " AND du.store_id =" . $store_id;
        }
        if ($assessment_string != '') {
            $lcwhere .= " AND am.id IN (" . $assessment_string . ")";
        }
        if($report_type=="2"){
            $query1 = "select a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query1 .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query1 .= " FROM (
                select du.region_id,art.parameter_id,count(atm.user_id) as total_users
                        FROM assessment_mst am
                        INNER JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                        INNER JOIN assessment_results_trans art ON art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE 1=1 $lcwhere GROUP BY art.parameter_id,du.region_id having total_users>0  ";
            $query1 .= " union all 
                        SELECT du.region_id,art.parameter_id,count(distinct atm.user_id)
                        FROM assessment_mst am
                        LEFT JOIN assessment_results as atm ON atm.assessment_id=am.id
                        INNER JOIN assessment_results_trans art ON  art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                        $lcwhere
                        GROUP BY art.parameter_id,du.region_id
                ) as a	
                        LEFT JOIN (
                        select a.region_id,a.parameter_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                        count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                        SELECT du.region_id,art.parameter_id,
                        IFNULL(FORMAT(avg(accuracy),2),0) AS pass_result	
                        FROM assessment_trainer_weights art
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                        INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE 1=1 $lcwhere
                        GROUP BY du.region_id,art.parameter_id,art.user_id  ";
            $query1 .= "    ) as a GROUP BY a.parameter_id,a.region_id
                ) as b ON b.region_id= a.region_id AND b.parameter_id=a.parameter_id";
            if (!$isoverall) {
                $query1 .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";
                $query_count = $query1;
                $query1 .= " limit " . $step . ",3 ";
                $result_count = $this->db->query($query_count);
                $data['region_total'] = count((array)$result_count->result());
                $result = $this->db->query($query1);
                $data['region_data'] = $result->result();
                return $data;
            }
        }elseif($report_type=="1"){   
            $query2 = "select a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query2 .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query2 .= " FROM (
                select du.region_id,art.parameter_id,count(atm.user_id) as total_users
                        FROM assessment_mst am
                        INNER JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                        INNER JOIN ai_subparameter_score art ON art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE  1=1 $lcwhere GROUP BY art.parameter_id,du.region_id having total_users>0  ";
            $query2 .= " union all 
                        SELECT du.region_id,art.parameter_id,count(distinct atm.user_id)
                        FROM assessment_mst am
                        LEFT JOIN ai_subparameter_score as atm ON atm.assessment_id=am.id
                        INNER JOIN ai_subparameter_score art ON  art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                        $lcwhere
                        GROUP BY art.parameter_id,du.region_id
                ) as a	
                        LEFT JOIN (
                        select a.region_id,a.parameter_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                        count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                        SELECT du.region_id,art.parameter_id,
                        IFNULL(FORMAT(avg(score),2),0) AS pass_result	
                        FROM ai_subparameter_score art
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                        INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE art.parameter_type='parameter' AND 1=1 $lcwhere
                        GROUP BY du.region_id,art.parameter_id,art.user_id  ";
            $query2 .= "    ) as a GROUP BY a.parameter_id,a.region_id
                ) as b ON b.region_id= a.region_id AND b.parameter_id=a.parameter_id";
            if (!$isoverall) {
                $query2 .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";
                $query_count = $query2;
                $query2 .= " limit " . $step . ",3 ";
                $result_count = $this->db->query($query_count);
                $data['region_total'] = count((array)$result_count->result());
                $result = $this->db->query($query2);
                $data['region_data'] = $result->result();
                return $data;
            }
        }else{
            $query1 = "select a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query1 .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query1 .= " FROM (
                select du.region_id,art.parameter_id,count(atm.user_id) as total_users
                        FROM assessment_mst am
                        INNER JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                        INNER JOIN assessment_results_trans art ON art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE 1=1 $lcwhere GROUP BY art.parameter_id,du.region_id having total_users>0  ";
            $query1 .= " union all 
                        SELECT du.region_id,art.parameter_id,count(distinct atm.user_id)
                        FROM assessment_mst am
                        LEFT JOIN assessment_results as atm ON atm.assessment_id=am.id
                        INNER JOIN assessment_results_trans art ON  art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                        $lcwhere
                        GROUP BY art.parameter_id,du.region_id
                ) as a	
                        LEFT JOIN (
                        select a.region_id,a.parameter_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                        count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                        SELECT du.region_id,art.parameter_id,
                        IFNULL(FORMAT(avg(accuracy),2),0) AS pass_result	
                        FROM assessment_trainer_weights art
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                        INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE 1=1 $lcwhere
                        GROUP BY du.region_id,art.parameter_id,art.user_id  ";
            $query1 .= "    ) as a GROUP BY a.parameter_id,a.region_id
                ) as b ON b.region_id= a.region_id AND b.parameter_id=a.parameter_id";
            if (!$isoverall) {
                $query1 .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";
                $query_count = $query1;
                //$query1 .= " limit " . $step . ",3 ";
            }
            $query2 = "select a.region_id, sum(a.total_users) as total_users, sum(b.pass) as pass, sum(b.fail) as fail";
            if (!$isoverall) {
                $query2 .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query2 .= " FROM (
                select du.region_id,art.parameter_id,count(atm.user_id) as total_users
                        FROM assessment_mst am
                        INNER JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                        INNER JOIN ai_subparameter_score art ON art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE  1=1 $lcwhere GROUP BY art.parameter_id,du.region_id having total_users>0  ";
            $query2 .= " union all 
                        SELECT du.region_id,art.parameter_id,count(distinct atm.user_id)
                        FROM assessment_mst am
                        LEFT JOIN ai_subparameter_score as atm ON atm.assessment_id=am.id
                        INNER JOIN ai_subparameter_score art ON  art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                        $lcwhere
                        GROUP BY art.parameter_id,du.region_id
                ) as a	
                        LEFT JOIN (
                        select a.region_id,a.parameter_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                        count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                        SELECT du.region_id,art.parameter_id,
                        IFNULL(FORMAT(avg(score),2),0) AS pass_result	
                        FROM ai_subparameter_score art
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                        INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE art.parameter_type='parameter' AND 1=1 $lcwhere
                        GROUP BY du.region_id,art.parameter_id,art.user_id  ";
            $query2 .= "    ) as a GROUP BY a.parameter_id,a.region_id
                ) as b ON b.region_id= a.region_id AND b.parameter_id=a.parameter_id";
            if (!$isoverall) {
                $query2 .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";
                $query_count = $query2;
                //$query2 .= " limit " . $step . ",3 ";           
            }
            $query3= "SELECT main.* from (($query1) UNION ALL ($query2)) as main";
            if (!$isoverall) {
                $query3 .= " LEFT JOIN region as rg ON rg.id=main.region_id GROUP BY main.region_id ";          
                $query_count = $query3;
                $query3 .= " limit " . $step . ",3 ";
                $result_count = $this->db->query($query_count);
                $data['region_total'] = count((array)$result_count->result());
            }
            $result = $this->db->query($query3);
            $data['region_data'] = $result->result();
            return $data;
        }
    }
    public function overall_result_assessment($isoverall, $passrange_from, $passrange_to, $failrange_from, $failrange_to, $SDate = '', $EDate = '', $region_id = '', $assessment_string = '', $step = 0, $store_id = '', $report_type) {
        $lcwhere = "";
        if ($SDate != '' && $EDate != '') {
            $lcwhere .=" AND (date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $lcwhere .=" OR date(am.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
        if ($region_id != '') {
            $lcwhere .= " AND du.region_id in(" . $region_id . ")";
        }
         if ($store_id != '') {
            $lcwhere .= " AND du.store_id =" . $store_id;
        }
        if ($assessment_string != '') {
            $lcwhere .= " AND am.id IN (" . $assessment_string . ")";
        }
        if($report_type=="2")
        {
            $query = "SELECT a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query .= " FROM (
            select du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                    FROM assessment_mst am
                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE 1=1 $lcwhere GROUP BY am.id,du.region_id having total_users>0";
            $query .= " union all 
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                    FROM assessment_mst am
                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE atm.is_completed =1 AND atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                    $lcwhere
                    GROUP BY am.id,du.region_id
            ) as a	
                    LEFT JOIN (
                    select a.region_id,a.assessment_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                    count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                    SELECT du.region_id,art.assessment_id,
                    IFNULL(FORMAT(avg(accuracy),2),0) AS pass_result FROM assessment_trainer_weights art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE 1=1 $lcwhere
                    GROUP BY du.region_id,art.assessment_id,art.user_id  ";
            $query .= "    ) as a GROUP BY a.assessment_id,a.region_id
            ) as b ON b.region_id= a.region_id AND b.assessment_id=a.assessment_id";

            if (!$isoverall) {
            $query .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";

	            $query_count = $query;
                $query .= " limit " . $step . ",3 ";
                $result_count = $this->db->query($query_count);
                $data['region_total'] = count((array)$result_count->result());
            }
            
            $result = $this->db->query($query);
            $data['region_data'] = $result->result();
            return $data;
        }   
        elseif($report_type=="1"){
            $query = "select a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query .= " FROM (
            select du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                    FROM assessment_mst am
                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE 1=1 $lcwhere GROUP BY am.id,du.region_id having total_users>0";
            $query .= " union all 
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                    FROM assessment_mst am
                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE atm.is_completed =1 AND atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                    $lcwhere
                    GROUP BY am.id,du.region_id
            ) as a	
                    LEFT JOIN (
                    select a.region_id,a.assessment_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                    count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                    SELECT du.region_id,art.assessment_id,
                    IFNULL(FORMAT(avg(score),2),0) AS pass_result FROM ai_subparameter_score art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE parameter_type='parameter' AND 1=1 $lcwhere
                    GROUP BY du.region_id,art.assessment_id,art.user_id  ";
            $query .= "    ) as a GROUP BY a.assessment_id,a.region_id
            ) as b ON b.region_id= a.region_id AND b.assessment_id=a.assessment_id";

            if (!$isoverall) {
                $query .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";          
                $query_count = $query;
                $query .= " limit " . $step . ",3 ";
                $result_count = $this->db->query($query_count);
                $data['region_total'] = count((array)$result_count->result());
            }
            $result = $this->db->query($query);
            $data['region_data'] = $result->result();
            return $data;
        }else{
            $query1 = "SELECT a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query1 .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query1 .= " FROM (
            select du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                    FROM assessment_mst am
                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE 1=1 $lcwhere GROUP BY am.id,du.region_id having total_users>0";
            $query1 .= " union all 
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                    FROM assessment_mst am
                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE atm.is_completed =1 AND atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                    $lcwhere
                    GROUP BY am.id,du.region_id
            ) as a	
                    LEFT JOIN (
                    select a.region_id,a.assessment_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                    count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                    SELECT du.region_id,art.assessment_id,
                    IFNULL(FORMAT(avg(accuracy),2),0) AS pass_result FROM assessment_trainer_weights art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE 1=1 $lcwhere
                    GROUP BY du.region_id,art.assessment_id,art.user_id  ";
            $query1 .= "    ) as a GROUP BY a.assessment_id,a.region_id
            ) as b ON b.region_id= a.region_id AND b.assessment_id=a.assessment_id";
            if (!$isoverall) {
                $query1 .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";          
            }

            $query2 = "select a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
            if (!$isoverall) {
                $query2 .= ", ifnull(rg.region_name,'No Region') as region_name ";
            }
            $query2 .= " FROM (
            select du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                    FROM assessment_mst am
                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE 1=1 $lcwhere GROUP BY am.id,du.region_id having total_users>0";
            $query2 .= " union all 
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                    FROM assessment_mst am
                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE atm.is_completed =1 AND atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )
                    $lcwhere
                    GROUP BY am.id,du.region_id
            ) as a	
                    LEFT JOIN (
                    select a.region_id,a.assessment_id,count(if(pass_result >=$failrange_from && pass_result<=$failrange_to,1,null)) as fail,
                    count(if(pass_result >=$passrange_from && pass_result<=$passrange_to,1,null)) as pass FROM(
                    SELECT du.region_id,art.assessment_id,
                    IFNULL(FORMAT(avg(score),2),0) AS pass_result FROM ai_subparameter_score art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE parameter_type='parameter' AND 1=1 $lcwhere
                    GROUP BY du.region_id,art.assessment_id,art.user_id  ";
            $query2 .= "    ) as a GROUP BY a.assessment_id,a.region_id
            ) as b ON b.region_id= a.region_id AND b.assessment_id=a.assessment_id";
            if (!$isoverall) {
                $query2 .= " LEFT JOIN region as rg ON rg.id=a.region_id GROUP BY a.region_id ";          
            }
            $query3 = "select * from ($query1 union all $query2) as main ";
            if (!$isoverall) {
                $query3 .= " LEFT JOIN region as rg ON rg.id=main.region_id GROUP BY main.region_id ";          
                $query_count = $query3;
                $query3 .= " limit " . $step . ",3 ";
                $result_count = $this->db->query($query_count);
                $data['region_total'] = count((array)$result_count->result());
            }
            $result = $this->db->query($query3);
            $data['region_data'] = $result->result();
            return $data;
        }
    }
    public function get_trainee_region($Company_id, $region_id = '', $report_by = '', $SDate = '', $EDate = '', $assessment_id = '', $store_id = '') {
        $query = "SELECT distinct du.region_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_attempts ar 
                        LEFT JOIN assessment_mst am  ON am.id=ar.assessment_id
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN region rg ON rg.id=du.region_id
                        WHERE 1=1";
        if ($SDate != '' && $EDate != '') {
            $query .=" AND (am.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $query .=" OR am.end_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
        if ($assessment_id != '') {
            $query .= " AND am.id in(" . $assessment_id . ")";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in(" . $region_id . ")";
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
       // $query .= " group by du.region_id ";
        $query .= " order by region_name asc ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_trainee_store($Company_id, $region_id = '', $report_by = '', $SDate = '', $EDate = '', $assessment_id = '') {
        $query = "SELECT distinct du.store_id,ifnull(st.store_name,'No Store') as store_name FROM assessment_attempts ar 
                        LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN store_mst st ON st.id=du.store_id
                        WHERE 1=1";
        if ($SDate != '' && $EDate != '') {
            $query .=" AND (date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $query .=" OR date(am.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
        if ($assessment_id != '') {
            $query .= " AND am.id in(" . $assessment_id . ")";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in(" . $region_id . ")";
        }
        //$query .= " group by du.store_id ";
        $query .= " order by store_name asc ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_paaraassessment($Company_id, $report_by) {
        $query = " SELECT " . ($report_by == 1 ? 'art.parameter_id' : 'ar.assessment_id') . " as para_ass_id
                    FROM assessment_results ar 				 	   
                        LEFT JOIN assessment_results_trans art 
                        ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        WHERE ar.company_id = " . $Company_id . " AND art.question_id !='' ";
        if ($report_by == 1) {
            $query .= " group by art.parameter_id ";
        } else {
            $query .= " group by ar.assessment_id ";
        }
        if ($report_by == 1) {
            $query .= " order by art.parameter_id ";
        } else {
            $query .= " order by ar.assessment_id ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_region_average($Company_id, $report_by, $region_id = '', $SDate = '', $EDate = '', $assessment_id = '', $report_type, $supervisor_id) {
        $cond="";$cond1="";$cond2='';$assmng="";
        if ($SDate != '' && $EDate != '') {
            $cond .=" AND (date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .=" OR date(am.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
	    }

        if ($region_id != '') {
            $cond .= " AND du.region_id in(" . $region_id . ") ";
        }
        if($supervisor_id !=''){
            $cond .= " AND amg.trainer_id =" . $supervisor_id;
            $assmng =" LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id ";
        }        
        /*
        if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }*/
        if ($assessment_id != '') {
            $cond .= " AND am.id in(" . $assessment_id . ") ";
        }
        if ($report_by == 1) {
            $cond .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id ";
            $cond1 .= " group by a.id,a.region_id ";
            $cond2 .= " group by b.id ";
            // $cond .= " group by art.parameter_id ";
        }elseif ($report_by == 2) {
            $cond .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id ";
            $cond1 .= " group by a.id,a.region_id ";
            $cond2 .= " group by b.id ";
        }elseif ($report_by == 3) {
            $cond .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id ";
            $cond1 .= " group by a.id,a.region_id ";
            $cond2 .= " group by b.region_id ";
            // $cond .= " group by du.region_id ";
        }elseif($report_by == 4){
            $cond .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id ";
            $cond1 .= " group by a.id,a.region_id ";
            $cond2 .= " group by b.region_id ";
        }
        if($report_type=="2"){
            $query = "SELECT round(IFNULL(avg(b.result),0),2) AS result,b.region_id,b.id
            FROM (SELECT round(IFNULL(avg(a.result),0),2) AS result,a.region_id,a.id
            FROM (SELECT round(IFNULL(avg(art.accuracy),0),2) AS result,du.region_id, ";
                if($report_by==1 || $report_by==3){
                    $query .= "art.parameter_id as id FROM assessment_trainer_weights art  ";
                }else if($report_by==2 || $report_by==4){
                    $query .= " am.id FROM assessment_trainer_weights art";
                }
                $query .= " LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                LEFT JOIN device_users du ON du.user_id=art.user_id ".$assmng." 
                WHERE 1=1 ";
            $query .= $cond.') as a '.$cond1.') as b '.$cond2;
            $query.= " order by b.region_id";
        }elseif($report_type=="1"){
            $query = " SELECT FORMAT(IFNULL(avg(b.result_1),0),2) AS result_1,round(IFNULL(avg(b.result),0),2) AS result,b.region_id,b.id
            FROM (SELECT FORMAT(IFNULL(avg(a.result_1),0),2) AS result_1,round(IFNULL(avg(a.result),0),2) AS result,a.region_id,a.id
            FROM (SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,
            IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,
            du.region_id, ";
                if($report_by==1 || $report_by==3){
                    $query .= "art.parameter_id as id FROM ai_subparameter_score art  ";
                }else if($report_by==2 || $report_by==4){
                    $query .= " am.id FROM ai_subparameter_score art";
                }
                $query .= " LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id ".$assmng." 
                WHERE art.parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query .= $cond .') as a '.$cond1.') as b '.$cond2;
            $query .= " order by b.region_id";
        }else{
            $query="SELECT IF(SUM(main.cnt) > 1,IFNULL(round(AVG(main.result),2),'---'),IFNULL(round(SUM(main.result),2),'---')) AS result, main.region_id, main.id 
                FROM(
                    (SELECT FORMAT(IFNULL(avg(b.result_1),0),2) AS result_1,round(IFNULL(avg(b.result),0),2) AS result,
                    IF(SUM(b.cnt) > 0,1,0) AS cnt,b.region_id,b.id
                    FROM (SELECT FORMAT(IFNULL(avg(a.result_1),0),2) AS result_1,round(IFNULL(avg(a.result),0),2) AS result,
                    IF(FORMAT(IFNULL(AVG(a.result),0),2) > 0,1,0) AS cnt,a.region_id,a.id
                    FROM (SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result_1,round(IFNULL(avg(art.accuracy),0),2) AS result,du.region_id,";
                    if($report_by==1 || $report_by==3){
                        $query .= "art.parameter_id as id FROM assessment_trainer_weights art  ";
                    }else if($report_by==2 || $report_by==4){
                        $query .= " am.id as id FROM assessment_trainer_weights art";
                    }
                    $query .= " LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id ".$assmng."  
             
                    WHERE 1=1 ";
                $query .= $cond.') as a '.$cond1.') as b '.$cond2.') ';
                $query.=" UNION ALL
                    (SELECT FORMAT(IFNULL(avg(b.result_1),0),2) as result_1,FORMAT(IFNULL(avg(b.result),0),2) as result, 
                    IF(SUM(b.cnt) > 0,1,0) AS cnt,b.region_id, b.id 
                    FROM (SELECT FORMAT(IFNULL(avg(a.result_1),0),2) as result_1,round(IFNULL(avg(a.result),0),2) as result, 
                    IF(FORMAT(IFNULL(AVG(a.result),0),2) > 0,1,0) AS cnt,a.region_id, a.id 
                    FROM (SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,
                    IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,
                    du.region_id, ";
                    if($report_by==1 || $report_by==3){
                        $query .= "art.parameter_id as id FROM ai_subparameter_score art  ";
                    }else if($report_by==2 || $report_by==4){
                        $query .= " am.id as id FROM ai_subparameter_score art";
                    }
                    $query .= " LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id ".$assmng." 
             
                    WHERE parameter_type='parameter' AND du.user_id IS NOT NULL ";
                $query .= $cond .') as a '.$cond1 .') as b '.$cond2;
 
                $query .= " order by b.region_id)";
                $query.=" ) as main";   
                if ($report_by == 1) {
                    $query .= " WHERE main.id IS NOT NULL group by main.id order by main.region_id  ";
                }elseif ($report_by == 2) {
                    $query .= " group by main.id order by main.region_id";
                }elseif ($report_by == 3 || $report_by == 4) {
                    $query .= " WHERE main.region_id IS NOT NULL group by main.region_id";
                }
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_region_result($Company_id, $report_by, $region_id = '', $SDate = '', $EDate = '', $assessment_id = '', $report_type, $supervisor_id) {
        $cond = '';$cond1 ='';$assmng="";
        if ($SDate != '' && $EDate != '') {
            $cond .=" AND (date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .=" OR date(am.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "')";
        }
        if($supervisor_id !=''){
            $cond .= " AND amg.trainer_id =" . $supervisor_id;
            $assmng = " LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id ";
        }
        if ($assessment_id != '') {
            $cond .= " AND am.id in(" . $assessment_id . ") ";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id in(" . $region_id . ") ";
        }
        // if ($store_id != '') {
        //     $cond .= " AND du.store_id =" . $store_id;
        // }
        if ($report_by == 1) {
            $cond .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id ";
            // $cond .= " group by pm.id,du.region_id ";
        } else {
            $cond .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id ";
            // $cond .= " group by art.assessment_id,du.region_id ";
        }
    if($report_type=="2")
    {
            $query = "
                select a.*,ctr.range_color,round(IFNULL(avg(a.result),0),2) AS result 
                from (
                SELECT IFNULL(round(avg(art.accuracy),2),'---') AS result,
                IFNULL(avg(art.accuracy),0) AS tresult,art.user_id,du.region_id,art.assessment_id,";
                if($report_by==1){
                    $query .= "pm.description as name,pm.id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_trainer_weights art LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id ";
                }else{
                    $query .= "am.assessment as name,art.assessment_id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_trainer_weights art";
                }
                $query .= " LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN region rg ON rg.id=du.region_id ". $assmng; 
                
            $query .= " WHERE 1=1 ";
            $query .= $cond;
            $query .= " order by du.region_id) as a "
                    . "LEFT JOIN company_threshold_range ctr ON  a.result between ctr.range_from and ctr.range_to  ";
            $query  .= " group by a.para_assess_id,a.region_id order by a.region_id ";
            $result = $this->db->query($query);
            return $result->result();
        }
        elseif($report_type=="1"){
            $query = "
                select a.*,ctr.range_color,FORMAT(IFNULL(AVG(a.result),0),2) AS result,round(IFNULL(AVG(a.tresult),0),2) AS tresult 
                    FRom (SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,
                IFNULL(avg(art.score),0) AS tresult,art.user_id,du.region_id,art.assessment_id,";
                if($report_by==1){
                    $query .= "pm.description as name,pm.id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM ai_subparameter_score art LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id ";
                }else{
                    $query .= "am.assessment as name,art.assessment_id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM ai_subparameter_score art";
                }
                $query .= " LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id ".$assmng;
                
            $query .= " LEFT JOIN region rg ON rg.id=du.region_id  WHERE parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query .= $cond.') as a ';
            $query .= " LEFT JOIN company_threshold_range ctr ON  a.result between ctr.range_from and ctr.range_to  ";
            $query  .= " group by a.para_assess_id,a.region_id order by a.region_id ";
            $result = $this->db->query($query);
            return $result->result();
        }else{
            $query1 = "
                SELECT round(IFNULL(avg(a.result),0),2) AS result,a.tresult,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt,a.user_id,a.region_id,a.assessment_id,a.name,a.para_assess_id,a.region_name
                 FROM (
                SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,IFNULL(avg(art.accuracy),0) AS tresult,art.user_id,du.region_id,art.assessment_id,";
                if($report_by==1){
                    $query1 .= "pm.description as name,pm.id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_trainer_weights art LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id ";
                }else{
                    $query1 .= "am.assessment as name,art.assessment_id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_trainer_weights art";
                }
                $query1 .= " LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN region rg ON rg.id=du.region_id ".$assmng; 
                    
                    $query1 .= " WHERE 1=1 ";
            $query1 .= $cond;
            $query1 .= " order by du.region_id) as a ";
            $query1  .= " group by a.para_assess_id,a.region_id order by a.region_id ";
   
            $query2 = "
                SELECT round(IFNULL(AVG(b.result),0),2) AS result, FORMAT(IFNULL(AVG(b.tresult),0),2) AS tresult,IF(FORMAT(IFNULL(AVG(b.result),0),2) > 0,1,0) AS cnt,b.user_id,b.region_id,b.assessment_id,b.name,b.para_assess_id,b.region_name
                FRom (
                SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,
                IFNULL(avg(art.score),0) AS tresult,art.user_id,du.region_id,art.assessment_id,";
                if($report_by==1){
                    $query2 .= "pm.description as name,pm.id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM ai_subparameter_score art LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id ";
                }else{
                    $query2 .= "am.assessment as name,art.assessment_id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM ai_subparameter_score art ";
                }
                $query2 .= " LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id ".$assmng;
                
            $query2 .= " LEFT JOIN region rg ON rg.id=du.region_id  WHERE parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query2 .= $cond." ) as b ";
            $query2  .= " group by b.para_assess_id,b.region_id order by b.region_id ";
            
            $query3="select main.*,ctr.range_color,IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'),IFNULL(FORMAT(SUM(main.result),2),'---')) AS result from ((".$query1.") UNION ALL (".$query2.")) as main ";
            $query3 .="LEFT JOIN company_threshold_range ctr ON main.result BETWEEN ctr.range_from AND ctr.range_to ";
            if ($report_by == 1) {
                $query3 .= " group by main.para_assess_id,main.region_id order by main.region_id ";
            } else {
                $query3 .= " group by main.assessment_id, main.region_id order by main.region_id ";
            }
            $result = $this->db->query($query3);
            return $result->result();
        }
    }
    public function assessmentwise_overall_result($Company_id, $region_id) {
        $query = " SELECT res.result,ctr.result_color,ctr.assessment_status,res.region_id, 
                    IFNULL(COUNT(res.assessment_id),0) AS tot_assessments,assessment_id,assessment
                    FROM company_threshold_result ctr
                    LEFT JOIN (
                        SELECT IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) AS result,	
                            IFNULL(SUM(art.score)/ SUM(art.weight_value),0) AS ord_res,du.region_id, art.user_id, 
                            SUM(art.score) AS rating,am.assessment,pm.id,art.assessment_id,am.company_id
                            FROM assessment_trainer_weights art 
                                LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                                LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                                INNER JOIN device_users du ON du.user_id=art.user_id
                            WHERE am.company_id = $Company_id AND du.region_id=$region_id
                        GROUP BY art.assessment_id
                    ) AS res ON res.company_id=ctr.company_id
                        WHERE (res.result BETWEEN ctr.result_from AND ctr.result_to)
                        GROUP BY ctr.assessment_status,res.assessment_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameter_assessment_score_new($Company_id, $parassess_id = '', $region_id = '', $StartDate = '', $EndDate = '', $report_type,$store_id = '') {
        $cond = '';
        if ($parassess_id != '') {
            $cond .= " AND art.parameter_id=" . $parassess_id;
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id=" . $region_id;
        }
        if ($store_id != '') {
            $cond .= " AND du.store_id =" . $store_id;
        }
        if ($StartDate != '' && $EndDate != '') {
            $cond .=" AND (DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .=" OR DATE(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        if($report_type=="2"){
            $query = " SELECT CONCAT(IFNULL(FORMAT(avg(art.accuracy),2),0),'%') AS result,du.region_id,
                    art.user_id,pm.id,art.assessment_id,am.assessment,pm.description as parameter
                    FROM assessment_trainer_weights art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE 1=1 ";
            $query .= $cond;
            $query .= " group by art.assessment_id ";
            $query .= " order by art.assessment_id ";
            $result = $this->db->query($query);
            return $result->result();
        }elseif($report_type=="1"){
            $query = " SELECT CONCAT(IFNULL(FORMAT(avg(art.score),2),0),'%') AS result,du.region_id,
                    art.user_id,pm.id,art.assessment_id,am.assessment,pm.description as parameter
                    FROM ai_subparameter_score art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE parameter_type='parameter' AND 1=1 ";
            $query .= $cond;
            $query .= " group by art.assessment_id ";
            $query .= " order by art.assessment_id ";
            $result = $this->db->query($query);
            return $result->result();
        }else{
            $query = "SELECT IFNULL(FORMAT(sum(main.result)/count(main.result),2),0) as result, main.region_id, main.user_id, main.id, main.assessment_id, main.assessment, main.parameter from 
                    ((SELECT CONCAT(IFNULL(FORMAT(avg(art.accuracy),2),0),'%') AS result,du.region_id,
                    art.user_id,pm.id,art.assessment_id,am.assessment,pm.description as parameter
                    FROM assessment_trainer_weights art
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE 1=1 $cond group by art.assessment_id order by art.assessment_id)
                        UNION ALL
                    (SELECT CONCAT(IFNULL(FORMAT(avg(art.score),2),0),'%') AS result,du.region_id,
                    art.user_id,pm.id,art.assessment_id,am.assessment,pm.description as parameter
                    FROM ai_subparameter_score art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE parameter_type='parameter' AND 1=1 $cond group by art.assessment_id order by art.assessment_id)) as main";
            $query .= " group by main.assessment_id ";
            $query .= " order by main.assessment_id ";
            $result = $this->db->query($query);
            return $result->result();
        }
    }
    public function get_parameter_assessment_score($Company_id, $parassess_id = '', $region_id = '', $StartDate = '', $EndDate = '', $report_type,$store_id = '') {
        $query = " SELECT CONCAT(IFNULL(FORMAT(avg(art.accuracy),2),0),'%') AS result,du.region_id,
                    art.user_id,pm.id,art.assessment_id,am.assessment,pm.description as parameter
                    FROM assessment_trainer_weights art
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE 1=1 ";
//                        WHERE art.question_id !=''";
        if ($parassess_id != '') {
            $query .= " AND art.parameter_id=" . $parassess_id;
        }
        if ($region_id != '') {
            $query .= " AND du.region_id=" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND (DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .=" OR DATE(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        $query .= " group by art.assessment_id ";

        $query .= " order by art.assessment_id ";
            
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_question_count($Company_id,$report_by, $start_date, $end_date, $region_id, $store_id, $report_type, $supervisor_id, $assessment_id1)
    {
        if($report_type=="2")
        {
             $query="SELECT count(DISTINCT(question_id)) as question_answer FROM `assessment_results`
             as ar left JOIN assessment_managers as am on 
             am.assessment_id=ar.assessment_id
             LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
             LEFT JOIN device_users du ON du.user_id=ar.user_id 
              WHERE 1=1";
             if ($start_date != '' && $end_date != '') {
                $query .=" AND (amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .=" OR amst.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            }
            if ($region_id != '') {
                $query .= " AND du.region_id =" . $region_id;
            }
            if ($assessment_id1 != '') {
                $query .= " AND ar.assessment_id =" . $assessment_id1;
            }
            if ($store_id != '') {
                $query .= " AND du.store_id =" . $store_id;
            }
            if($supervisor_id !='')
            {
                $query .= " AND am.trainer_id =" . $supervisor_id;
            }
            
        }
        elseif($report_type=="1")
        {
            $query="SELECT count(DISTINCT(question_id)) as question_answer FROM `ai_schedule`
            as ar
            LEFT JOIN assessment_managers  am on 
            am.assessment_id=ar.assessment_id  
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
             where 1=1";
            if ($start_date != '' && $end_date != '') {
                $query .=" AND (amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .=" OR amst.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            }
            if ($region_id != '') {
                $query .= " AND du.region_id =" . $region_id;
            }
            if ($assessment_id1 != '') {
                $query .= " AND ar.assessment_id =" . $assessment_id1;
            }
            if ($store_id != '') {
                $query .= " AND du.store_id =" . $store_id;
            }
            if($supervisor_id !='')
            {
                $query .= " AND am.trainer_id =" . $supervisor_id;
            }
            
        }
        else
        {
            $cond="";
            if ($start_date != '' && $end_date != '') {
                $cond .=" AND (amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .=" OR amst.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            }
            if ($region_id != '') {
                $cond .= " AND du.region_id =" . $region_id;
            }
            if ($assessment_id1 != '') {
                $cond .= " AND ar.assessment_id =" . $assessment_id1;
            }
            if ($store_id != '') {
                $cond .= " AND du.store_id =" . $store_id;
            }
            if($supervisor_id !='')
            {
                $cond .= " AND am.trainer_id =" . $supervisor_id;
            }

            $query=" SELECT (main.question_answer) as question_answer FROM
            (SELECT count(DISTINCT(question_id)) as question_answer FROM `assessment_results`
            as ar left JOIN assessment_managers as am on 
            am.assessment_id=ar.assessment_id
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
            LEFT JOIN device_users du ON du.user_id=ar.user_id
            where 1=1 $cond

            UNION ALL
            
            SELECT count(DISTINCT(question_id)) as question_answer FROM `ai_schedule`
            as ar
            LEFT JOIN assessment_managers  am on 
            am.assessment_id=ar.assessment_id  
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
             where 1=1 $cond) as main
            
            ";
           
        }
        
        $result = $this->db->query($query);
        $RowSet = $result->row();
        return $TotalAccuracy = $RowSet->question_answer;
    }
    public function get_questions_score_new($dtWhere, $dtOrder, $dtLimit, $report_type) {
        if($report_type=="2"){
            $query = " SELECT  IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0),'%'), CONCAT(IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0),'%')) AS result,du.region_id,
                    IF(am.ratingstyle=2,IFNULL(SUM(art.percentage),0),IFNULL(SUM(art.score),0)) AS rating,
                    IF(am.ratingstyle=2,IFNULL(count(pm.id),0),IFNULL(SUM(pm.weight_value),0))as total_rate,
                    pm.id,ar.assessment_id,ar.company_id,
                    am.assessment,pm.description as parameter,aq.question,am.is_weights
                    FROM assessment_results ar 				 	   
                        LEFT JOIN assessment_results_trans art 
                        ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
                        INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=ar.user_id AND cr.trainer_id=art.trainer_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=ar.assessment_id	
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN assessment_question aq ON aq.id=art.question_id
                        $dtWhere AND art.question_id !=''";
            $query .= " group by art.question_id ";
            $query_count = $query;
            $query .= " $dtOrder $dtLimit ";
            $result = $this->db->query($query);
            $data['ResultSet'] = $result->result_array();
            $data['dtPerPageRecords'] = $result->num_rows();
            $result = $this->db->query($query_count);
            $data_array = $result->result_array();
            $total = count((array)$data_array);
            $data['dtTotalRecords'] = $total;
            return $data;
        }elseif($report_type=="1"){
            $query = " SELECT  IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(art.score)/ COUNT(pm.id),2),0),'%'), CONCAT(IFNULL(FORMAT(AVG(art.score),2),0),'%')) AS result,du.region_id,
                    IF(am.ratingstyle=2,IFNULL(SUM(art.score),0),IFNULL(SUM(art.score),0)) AS rating,
                    IF(am.ratingstyle=2,IFNULL(count(pm.id),0),IFNULL(SUM(pm.weight_value),0))as total_rate,
                    pm.id,art.assessment_id,art.company_id,
                    am.assessment,pm.description as parameter,aq.question,am.is_weights
                    FROM ai_subparameter_score art
                    INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=art.user_id
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    LEFT JOIN assessment_question aq ON aq.id=art.question_id
                    WHERE 1=1 $dtWhere AND parameter_type='parameter' AND art.question_id !=''";
            $query .= " group by art.question_id ";
            $query_count = $query;
            $query .= " $dtOrder $dtLimit ";
            $result = $this->db->query($query);
            $data['ResultSet'] = $result->result_array();
            $data['dtPerPageRecords'] = $result->num_rows();
            $result = $this->db->query($query_count);
            $data_array = $result->result_array();
            $total = count((array)$data_array);
            $data['dtTotalRecords'] = $total;
            return $data;
        }else{
            $query = " SELECT FORMAT(SUM(main.result)/count(main.result),2) as result, main.question, main.question_id from( 
                ( SELECT   CONCAT(IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0),'%') AS result,du.region_id,
                        am.assessment,pm.description as parameter,aq.question,am.is_weights, art.question_id
                    FROM assessment_results as ar 				 	   
                        LEFT JOIN assessment_results_trans as art 
                        ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
                        INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=ar.user_id AND cr.trainer_id=art.trainer_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=ar.assessment_id	
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN assessment_question aq ON aq.id=art.question_id
                       where 1=1 $dtWhere AND art.question_id !=''
                     group by art.question_id order by art.question_id)
                     UNION ALL
                ( SELECT  CONCAT(IFNULL(FORMAT(AVG(ar.score),2),0),'%') AS result,du.region_id,
                    am.assessment,pm.description as parameter,aq.question,am.is_weights, ar.question_id
                    FROM ai_subparameter_score ar
                    INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=ar.assessment_id AND cr.user_id=ar.user_id
                    LEFT JOIN parameter_mst pm ON pm.id=ar.parameter_id 
                    LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=ar.assessment_id AND aw.parameter_id=ar.parameter_id
                    LEFT JOIN assessment_mst am ON am.id=ar.assessment_id	
                    INNER JOIN device_users du ON du.user_id=ar.user_id
                    LEFT JOIN assessment_question aq ON aq.id=ar.question_id
                    where 1=1 $dtWhere AND parameter_type='parameter' AND ar.question_id !='' group by ar.question_id ORDER BY ar.question_id asc)) as main
                    GROUP BY main.question_id";
            $query_count = $query;
            $query .= " $dtLimit ";
            $result = $this->db->query($query);
            $data['ResultSet'] = $result->result_array();
            $data['dtPerPageRecords'] = $result->num_rows();
            $result = $this->db->query($query_count);
            $data_array = $result->result_array();
            $total = count((array)$data_array);
            $data['dtTotalRecords'] = $total;
            return $data;
        }
    }
    public function get_questions_score($dtWhere, $dtOrder, $dtLimit) {
        $query = " SELECT  IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0),'%'), CONCAT(IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0),'%')) AS result,du.region_id,
                    IF(am.ratingstyle=2,IFNULL(SUM(art.percentage),0),IFNULL(SUM(art.score),0)) AS rating,
                    IF(am.ratingstyle=2,IFNULL(count(pm.id),0),IFNULL(SUM(pm.weight_value),0))as total_rate,
                    pm.id,ar.assessment_id,ar.company_id,
                    am.assessment,pm.description as parameter,aq.question,am.is_weights
                    FROM assessment_results ar 				 	   
                        LEFT JOIN assessment_results_trans art 
                        ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
                        INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=ar.user_id AND cr.trainer_id=art.trainer_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=ar.assessment_id	
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN assessment_question aq ON aq.id=art.question_id
                        $dtWhere AND art.question_id !=''";
        $query .= " group by art.question_id ";
        
        $query_count = $query;
        $query .= " $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }
    public function get_assessment($Company_id, $report_by = '', $StartDate, $EndDate, $supervisor_id) {
        $query = "SELECT  distinct ar.id as assessment_id, ar.assessment as assessment FROM assessment_mst ar 	
			LEFT JOIN assessment_managers am ON am.assessment_id=ar.id where 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND (date(ar.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .=" OR date(ar.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        if($supervisor_id !=''){
            $query .= " AND am .trainer_id =" . $supervisor_id;
        }
        $query .= "  order by assessment asc ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_all_assessment($Company_id, $StartDate, $EndDate) {
        $query = "SELECT  distinct ar.id as assessment_id, ar.assessment as assessment FROM assessment_mst ar 	
			 where 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND (ar.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .=" OR ar.end_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        
        $query .= "  order by assessment asc ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_old($Company_id, $report_by = '', $StartDate, $EndDate) {
        $query = "SELECT  distinct ar.assessment_id,am.assessment FROM assessment_complete_rating ar 	
			LEFT JOIN assessment_mst am ON am.id=ar.assessment_id ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" where am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        $query .= "  order by assessment asc ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_user($company_id, $region_id, $assessment_id) {
        $query = " SELECT ar.user_id,du.firstname
                    FROM assessment_results ar
                        LEFT JOIN assessment_results_trans art 
                                ON art.result_id=ar.id AND art.assessment_id=ar.assessment_id AND art.user_id=ar.user_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                    WHERE ar.company_id = $company_id AND art.question_id !='' AND ar.assessment_id=$assessment_id AND du.region_id=$region_id
                    GROUP BY du.user_id ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameter($region_id='', $assessment_id='') {
       $query="SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter, ps.id as parameter_id, pl.id as parameter_label_id FROM parameter_mst as ps 
            left join parameter_label_mst as pl on pl.parameter_id= ps.id
            WHERE 1=1
            ORDER BY  ps.id, pl.id";
                if($assessment_id!=''){
                    $query .= " AND ar.assessment_id=".$assessment_id;
                }
                if($region_id!=''){
                    $query .= " AND du.region_id=".$region_id;
                }
			//$query .= " GROUP BY ps.id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_user_average($Company_id, $report_by, $region_id = '', $assessment_id = '', $report_type) {
        if($report_type=="2"){ //Manual
            if ($report_by == 1){
			    $query = "SELECT round(IFNULL(avg(art.accuracy),0),2) AS result,du.user_id,IF(art.parameter_label_id=0,art.parameter_id, art.parameter_label_id) as id 
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
		            WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id";
                $query .= " group by art.parameter_id,art.parameter_label_id ";
            }elseif ($report_by == 3) {
                $query = " SELECT FORMAT(IFNULL(AVG(a.result),0),2) AS result,a.user_id
                    FROM (
                        SELECT round(IFNULL(avg(art.accuracy),0),2) AS result,du.user_id
                        FROM assessment_trainer_weights art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id 
                        group by art.parameter_id,art.parameter_label_id,du.user_id 
                   ) as a ";  
                $query .= " group by a.user_id order by a.user_id";
            }
            $result = $this->db->query($query);
            return $result->result();
        }elseif($report_type=="1"){
            if ($report_by == 1) {
            $query = " SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,
                       IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result,
                       du.user_id,IF(art.parameter_label_id=0 or art.parameter_label_id is null, art.parameter_id, art.parameter_label_id) as id 
                       FROM ai_subparameter_score  art 
                       LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                       LEFT JOIN device_users du ON du.user_id=art.user_id 
                       LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                       WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id AND art.parameter_type='parameter'";
                $query .= " group by art.parameter_id,art.parameter_label_id ";
            }elseif ($report_by == 3) {
                 $query = " SELECT FORMAT(IFNULL(AVG(a.result_1),0),2) AS result_1,FORMAT(IFNULL(AVG(a.result),0),2) AS result,a.user_id,a.id
                    FROM (
                     SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,
                       IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result,
                       du.user_id,IF(art.parameter_label_id=0 or art.parameter_label_id is null, art.parameter_id, art.parameter_label_id) as id 
                       FROM ai_subparameter_score  art 
                       LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                       LEFT JOIN device_users du ON du.user_id=art.user_id 
                       LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                       WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id AND art.parameter_type='parameter' 
                       GROUP BY art.parameter_id,art.parameter_label_id,du.user_id
                    ) AS a ";
                $query .= " GROUP BY a.user_id ORDER BY a.user_id";
            }
            $result = $this->db->query($query);
            return $result->result();
        }else{
            if ($report_by == 1) {
                $query = "SELECT main.*,IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'),IFNULL(FORMAT(SUM(main.result),2),'---')) AS result
                    from (SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result_1,du.user_id,
                    IF(plm.id=0 or plm.id is null,pm.id,plm.id) as id,
                    round(IFNULL(avg(art.accuracy),0),2) AS result,
                    IF(FORMAT(IFNULL(AVG(art.accuracy),0),2) > 0 OR du.user_id IS NOT NULL,1,0) AS cnt
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id and plm.parameter_id=art.parameter_id
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id   
                    WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id
                    GROUP BY art.parameter_id,art.parameter_label_id,du.user_id
                UNION ALL
                    SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,du.user_id,
                    IF(plm.id=0 or plm.id is null,pm.id,plm.id) as id,
                    IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result,
                    IF(IF(SUM(ats.parameter_weight)=0,(FORMAT(IFNULL(avg(art.score),0),2)),FORMAT(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0 OR du.user_id IS NOT NULL,1,0) as cnt
                    FROM ai_subparameter_score art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id and plm.parameter_id=art.parameter_id
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id   
                    LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                    WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id AND art.parameter_type='parameter'
                    GROUP BY art.parameter_id,art.parameter_label_id,du.user_id) as main GROUP BY main.id";
            } elseif ($report_by == 3) {
                $query = "SELECT IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'),IFNULL(FORMAT(SUM(main.result),2),'---')) AS result, main.user_id
                    FROM (
                        SELECT FORMAT(IFNULL(AVG(b.result_1),0),2) AS result_1,b.user_id,FORMAT(IFNULL(AVG(b.result),0),2) AS result,
                        IF(FORMAT(IFNULL(AVG(b.result),0),2) > 0 OR b.user_id IS NOT NULL,1,0) AS cnt
                        FROM (
                            SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result_1,du.user_id,
                        round(IFNULL(avg(art.accuracy),0),2) AS result
                            FROM assessment_trainer_weights art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id
                          GROUP BY art.parameter_id,art.parameter_label_id,du.user_id 
                        ) as b GROUP BY b.user_id 
                    UNION ALL
                        SELECT FORMAT(IFNULL(AVG(a.result_1),0),2) AS result_1,a.user_id,FORMAT(IFNULL(AVG(a.result),0),2) AS result,
                        IF(FORMAT(IFNULL(AVG(a.result),0),2) > 0 OR a.user_id IS NOT NULL,1,0) AS cnt
                        FROM (
                        SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,du.user_id,
                        IF(SUM(ats.parameter_weight)=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result
                        FROM ai_subparameter_score  art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                        WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id AND art.parameter_type='parameter'
                            GROUP BY art.parameter_id,art.parameter_label_id,du.user_id
                            ) as a GROUP BY a.user_id
                        ) as main GROUP BY main.user_id ";
            }
            $result = $this->db->query($query);
            return $result->result();
        }  
    }
    public function get_parameter_user_result($company_id, $region_id, $assessment_id) {
        $query = " SELECT a.*,ctr.range_color
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,art.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                        IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,pm.id as parameter_id,pm.description AS parameter
                        FROM assessment_trainer_weights art 
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id
                        group by pm.id,du.user_id order by du.user_id
                        ) as a 
                    LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameter_user_result_new($company_id, $region_id, $assessment_id, $report_type) {
        if($report_type=="2"){
            $query = " SELECT a.*,ctr.range_color
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,art.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                        IFNULL(round(avg(art.accuracy),2),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM assessment_trainer_weights art 
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id
                        group by pm.id,plm.id,du.user_id order by du.user_id
                        ) as a 
                    LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to order by a.user_id,a.parameter_id";
        }elseif($report_type== "1"){            
             $query = "SELECT a.*,ctr.range_color FROM 
                        (SELECT art.assessment_id as para_assess_id,du.region_id,art.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                        IFNULL(IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM ai_subparameter_score art 
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id AND plm.parameter_id = art.parameter_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        LEFT JOIN assessment_trans_sparam ats ON art.assessment_id=ats.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE parameter_type='parameter' AND art.assessment_id=$assessment_id AND du.region_id=$region_id 
                        group by pm.id,plm.id,du.user_id order by du.user_id) as a 
                        LEFT JOIN company_threshold_range ctr ON a.result between ctr.range_from and ctr.range_to order by a.user_id,a.parameter_id";
        }else{
            $query = " SELECT *,IF(SUM(cnt) > 1,IFNULL(FORMAT(AVG(result),2),'---'),IFNULL(FORMAT(SUM(result),2),'---')) AS result 
                from (SELECT a.*,ctr.range_color,IF(a.result > 0 OR a.user_id IS NOT NULL, 1 ,0) AS cnt
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,art.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                        IFNULL(round(avg(art.accuracy),2),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM assessment_trainer_weights art
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id
                        group by pm.id,plm.id,du.user_id order by du.user_id
                        ) as a 
                    LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to 

                        UNION ALL

                        SELECT a.*,ctr.range_color,IF(a.result > 0 OR a.user_id IS NOT NULL, 1 ,0) AS cnt
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,art.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                        IFNULL(IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM ai_subparameter_score art 
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id and plm.parameter_id=art.parameter_id 
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        LEFT JOIN assessment_trans_sparam ats ON art.assessment_id=ats.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE parameter_type='parameter' AND art.assessment_id=$assessment_id AND du.region_id=$region_id 
                        group by pm.id,plm.id,du.user_id order by du.user_id
                        ) as a 
                    LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to)  as main group by main.user_id, main.parameter_id order by main.user_id";
                }
                $result = $this->db->query($query);
                return $result->result();
        }
    public function get_pararegion_average($Company_id, $report_by, $assessment_id = '', $report_type) {
        if($report_type=="2")
        { //Manual
            
        if ($report_by == 1) {
            $query = " SELECT FORMAT(IFNULL(avg(a.result),0),2) AS result,a.region_id,a.id
                FROM(
                    SELECT round(IFNULL(avg(art.accuracy),0),2) AS result,du.region_id,IF(art.parameter_label_id=0, art.parameter_id, art.parameter_label_id) as id 
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
            WHERE art.assessment_id=$assessment_id ";
                    $query .= " group by art.parameter_id,art.parameter_label_id,du.region_id 
                ) AS a GROUP BY a.id ";
            }elseif ($report_by == 3) {
                $query = " SELECT FORMAT(IFNULL(AVG(a.result),0),2) AS result,a.region_id
                FROM (
                    SELECT round(IFNULL(avg(art.accuracy),0),2) AS result,du.region_id
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
            WHERE art.assessment_id=$assessment_id ";
                    $query .= " group by art.parameter_id,art.parameter_label_id,du.region_id 
                ) as a ";
            $query .= " GROUP BY a.region_id ORDER BY a.region_id";
            }
            $result = $this->db->query($query);
            return $result->result();
        }
        elseif($report_type=="1")
        {
        if ($report_by == 1) {
            $query = " SELECT FORMAT(IFNULL(avg(a.result_1),0),2) AS result_1,FORMAT(IFNULL(avg(a.result),0),2) AS result,a.region_id,a.id
                FROM(
                SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,
                        IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result,
                        du.region_id,IF(art.parameter_label_id=0 or art.parameter_label_id is null, art.parameter_id, art.parameter_label_id) as id 
                        FROM ai_subparameter_score  art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                WHERE art.assessment_id=$assessment_id AND art.parameter_type='parameter' 
                group by art.parameter_id,art.parameter_label_id,du.region_id 
            ) AS a GROUP BY a.id ";
        }elseif ($report_by == 3) {
                $query = " SELECT FORMAT(IFNULL(AVG(a.result_1),0),2) AS result_1,FORMAT(IFNULL(AVG(a.result),0),2) AS result,a.region_id,a.id 
                    FROM (
                        SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,
                        IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result,
                        du.region_id,IF(art.parameter_label_id=0 or art.parameter_label_id is null, art.parameter_id, art.parameter_label_id) as id 
                        FROM ai_subparameter_score  art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                    WHERE art.assessment_id=$assessment_id AND art.parameter_type='parameter' 
                    group by art.parameter_id,art.parameter_label_id,du.region_id 
                    ) as a ";
                $query .= " group by a.region_id order by a.region_id";
        }
        $result = $this->db->query($query);
        return $result->result();
    }else{
            if ($report_by == 1) {
                $query = "SELECT main.*,IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'),IFNULL(FORMAT(SUM(main.result),2),'---')) AS result 
                    from (SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result_1,du.region_id,
                    IF(plm.id=0 or plm.id is null,pm.id,plm.id) as id,
                    round(IFNULL(avg(art.accuracy),0),2) AS result,
                    IF(round(IFNULL(AVG(art.accuracy),0),2) > 0 OR du.region_id IS NOT NULL,1,0) AS cnt
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id and plm.parameter_id=art.parameter_id
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id   
                    WHERE art.assessment_id=$assessment_id 
                    GROUP BY art.parameter_id,art.parameter_label_id,du.region_id 
                UNION ALL
                    SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,du.region_id,
                    IF(plm.id=0 or plm.id is null,pm.id,plm.id) as id,
                    IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result,
                    IF(IF(SUM(ats.parameter_weight)=0,(FORMAT(IFNULL(avg(art.score),0),2)),FORMAT(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0 OR du.region_id IS NOT NULL,1,0) as cnt
                    FROM ai_subparameter_score art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id and plm.parameter_id=art.parameter_id
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id   
                    LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                    WHERE art.assessment_id=$assessment_id AND art.parameter_type='parameter'
                    GROUP BY art.parameter_id,art.parameter_label_id,du.region_id) as main group by main.id";
                }elseif ($report_by == 3) {
                $query = " SELECT IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'),IFNULL(FORMAT(SUM(main.result),2),'---')) AS result, main.region_id 
                    FROM (
                    SELECT FORMAT(IFNULL(AVG(a.result_1),0),2) AS result_1,a.region_id,FORMAT(IFNULL(AVG(a.result),0),2) AS result,
                        IF((FORMAT(IFNULL(AVG(a.result),0),2) > 0 OR a.region_id IS NOT NULL),1,0) AS cnt
                    FROM (
                        SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result_1,du.region_id,
                        round(IFNULL(avg(art.accuracy),0),2) AS result
                        FROM assessment_trainer_weights art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        WHERE art.assessment_id=$assessment_id 
                        group by art.parameter_id,art.parameter_label_id,du.region_id 
                        ) as a 
                    GROUP BY a.region_id  
                UNION ALL
                    SELECT FORMAT(IFNULL(AVG(b.result_1),0),2) AS result_1,b.region_id,FORMAT(IFNULL(AVG(b.result),0),2) AS result,
                        IF((FORMAT(IFNULL(AVG(b.result),0),2) > 0 OR b.region_id IS NOT NULL),1,0) AS cnt
                        FROM (
                            SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result_1,du.region_id,
                                IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) AS result
                            FROM ai_subparameter_score  art 
                            LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                            LEFT JOIN device_users du ON du.user_id=art.user_id 
                            LEFT JOIN assessment_trans_sparam ats ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                            WHERE art.assessment_id=$assessment_id AND art.parameter_type='parameter'
                            group by art.parameter_id,art.parameter_label_id,du.region_id
                        ) as b GROUP BY b.region_id
                    ) as main GROUP by main.region_id ORDER BY main.region_id ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    }
    public function get_parameter_assessment_result($company_id, $assessment_id) {
        if($report_type=="2")
        {
        $query = " SELECT a.*,ctr.range_color
                FROM (
                    SELECT art.assessment_id as para_assess_id,du.region_id,
                    ifnull(rg.region_name,'No Region') as region_name,
                    IFNULL(round(avg(art.accuracy),2),'---') AS result,
                    IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                    FROM assessment_trainer_weights art 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                    INNER JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN region rg ON rg.id=du.region_id 
                    WHERE art.assessment_id=$assessment_id 
                    group by pm.id,plm.id,du.region_id order by du.region_id
                    ) as a 
                LEFT JOIN company_threshold_range ctr 
                    ON a.result between ctr.range_from and ctr.range_to order by a.region_id,a.parameter_id";
        }
        elseif($report_type== "1")
        {
            $query = "SELECT a.*,ctr.range_color FROM 
                        (SELECT art.assessment_id as para_assess_id,du.region_id,
                        ifnull(rg.region_name,'No Region') as region_name,
                        IFNULL(IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM ai_subparameter_score art 
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id AND plm.parameter_id = art.parameter_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        LEFT JOIN assessment_trans_sparam ats ON art.assessment_id=ats.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE parameter_type='parameter' AND art.assessment_id=$assessment_id 
                        group by pm.id,plm.id,du.region_id order by du.region_id) as a 
                        LEFT JOIN company_threshold_range ctr ON a.result between ctr.range_from and ctr.range_to order by a.region_id,a.parameter_id";
        }
        else
        {
            $query = " SELECT *,IF(SUM(cnt) > 1,IFNULL(FORMAT(AVG(result),2),'---'),IFNULL(FORMAT(SUM(result),2),'---')) AS result  
                from (SELECT a.*,ctr.range_color,IF(a.result > 0, 1 ,0) AS cnt
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,
                        ifnull(rg.region_name,'No Region') as region_name,
                        IFNULL(round(avg(art.accuracy),2),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM assessment_trainer_weights art 
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE art.assessment_id=$assessment_id 
                        group by pm.id,plm.id,du.region_id order by du.region_id
                        ) as a 
                        LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to 

                    UNION ALL

                        SELECT a.*,ctr.range_color,IF(a.result > 0, 1 ,0) AS cnt
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,
                        ifnull(rg.region_name,'No Region') as region_name,
                        IFNULL(IF(SUM(ats.parameter_weight)=0,(round(IFNULL(avg(art.score),0),2)),round(IFNULL(SUM(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)),'---') AS result,
                        IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,IF(plm.description is null, pm.description, plm.description) AS parameter
                        FROM ai_subparameter_score art 
                        LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id and plm.parameter_id=art.parameter_id 
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        LEFT JOIN assessment_trans_sparam ats ON art.assessment_id=ats.assessment_id AND ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.question_id=art.question_id
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                    WHERE parameter_type='parameter' AND art.assessment_id=$assessment_id 
                        group by pm.id,plm.id,du.region_id order by du.region_id
                        ) as a 
                        LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to)  as main group by main.region_id, main.parameter_id order by main.region_id";
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_user($assessment_id = '', $StartDate = '', $EndDate = '') {
        $cond = '';
        if ($StartDate != '' && $EndDate != '') {
            $cond .=" (DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .=" OR DATE(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        $query = "(SELECT aa.user_id,aa.is_completed,aa.assessment_id
                        FROM assessment_attempts aa LEFT JOIN assessment_mst am ON am.id = aa.assessment_id ";
        if ($assessment_id != '') {
            $query .= " WHERE aa.assessment_id IN(" . $assessment_id . ") AND aa.user_id NOT IN(select user_id FROM assessment_allow_users where aa.assessment_id=assessment_id)";
        } else {
            $query .= " WHERE aa.user_id NOT IN(select user_id FROM assessment_allow_users where aa.assessment_id=assessment_id)";
        }
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND ".$cond;
        }
        $query .= " UNION ALL
                      SELECT au.user_id,0 AS is_completed,au.assessment_id
                      FROM assessment_allow_users  au 
                      LEFT JOIN assessment_mst am ON am.id = au.assessment_id ";
        if ($assessment_id != '') {
            $query .= " WHERE au.assessment_id IN(" . $assessment_id . ")";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND ".$cond;
            }
        } else {
            if ($StartDate != '' && $EndDate != '') {
                $query .=" WHERE ".$cond;
            }
        }
        $query .=" ) as a ";
        $user_count = " SELECT count(a.user_id) as user_count FROM " . $query;
        $result_cnt = $this->db->query($user_count);
        $data['user_count'] = $result_cnt->row();
        $ass_status = " SELECT a.user_id, IF(a.is_completed=1,'complete','incomplete') as assess_status FROM " . $query;
        $result_status = $this->db->query($ass_status);
        $data['ass_status'] = $result_status->result();
        return $data;
    }
    public function region_level_result($region_id, $StartDate = '', $EndDate = '', $pass_range_from, $pass_range_to, $fail_range_from, $fail_range_to, $report_type, $supervisor_id) {
        $cond = '';
        if ($StartDate != '' && $EndDate != '') {
            $cond .=" AND (DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .=" OR DATE(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        if($supervisor_id !=''){
            $cond .= " AND amg.trainer_id =" . $supervisor_id;
        }
        if($report_type=="2"){
            $query = " select a.assessment_id,amm.assessment,a.total_users,IFNULL(b.pass,0) as pass,IFNULL(b.fail,0) as fail 
                        FROM (
                                SELECT du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                                    FROM assessment_mst am
                                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                                    LEFT JOIN assessment_managers as amg on amg.assessment_id=am.id
                                    INNER JOIN device_users du ON du.user_id=atm.user_id
                                    WHERE 1=1 AND du.region_id IN($region_id)";
            $query .= $cond;
            $query .=" having total_users>0 ";
            $query .=" UNION ALL 
										
				    SELECT du.region_id,am.id,count(distinct atm.user_id)
                                    FROM assessment_mst am
                                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                                    LEFT JOIN assessment_managers as amg on amg.assessment_id=am.id
                                    INNER JOIN device_users du ON du.user_id=atm.user_id
                                    WHERE 1=1 AND atm.is_completed =1 AND du.region_id IN($region_id) AND
                                    atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )";
            $query .= $cond;
            $query .=" GROUP BY am.id,du.region_id
                ) as a 
                    LEFT JOIN 
                    (
                    SELECT a.region_id,a.assessment_id,count(if(pass_result >$fail_range_from && pass_result<=$fail_range_to,1,null)) as fail,
                        count(if(pass_result >$pass_range_from && pass_result<=$pass_range_to,1,null)) as pass 
                        FROM
                            (
                            SELECT du.region_id,art.assessment_id,
                            IFNULL(FORMAT(avg(art.accuracy),2),0) AS pass_result FROM assessment_final_results as art
                            LEFT JOIN assessment_mst am ON am.id=art.assessment_id	INNER JOIN device_users du ON du.user_id=art.user_id
                            LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                            WHERE du.region_id IN($region_id)";
            $query .= $cond;                
            $query .="  GROUP BY art.user_id,art.assessment_id,du.region_id) as a 
                            GROUP BY a.assessment_id,a.region_id
                    ) as b 
                    ON  b.assessment_id=a.assessment_id
                    LEFT JOIN assessment_mst amm ON amm.id=a.assessment_id ";
                $result = $this->db->query($query);
                return $result->result();
        }elseif($report_type="1"){
            $query = " select a.assessment_id,amm.assessment,a.total_users,IFNULL(b.pass,0) as pass,IFNULL(b.fail,0) as fail 
                        FROM (
                                SELECT du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                                    FROM assessment_mst am
                                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                                    LEFT JOIN assessment_managers as amg on amg.assessment_id=am.id
                                    INNER JOIN device_users du ON du.user_id=atm.user_id
                                    WHERE 1=1 AND du.region_id IN($region_id)";
            $query .= $cond;
            $query .=" having total_users>0 ";
            $query .=" UNION ALL 							
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                                    FROM assessment_mst am
                                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                                    LEFT JOIN assessment_managers as amg on amg.assessment_id=am.id
                                    INNER JOIN device_users du ON du.user_id=atm.user_id
                                    WHERE 1=1 AND atm.is_completed =1 AND du.region_id IN($region_id) AND
                                    atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )";
            $query .= $cond;
            $query .=" GROUP BY am.id,du.region_id
                        ) as a			
                        LEFT JOIN 
                        (
                        SELECT a.region_id,a.assessment_id,count(if(pass_result >$fail_range_from && pass_result<=$fail_range_to,1,null)) as fail,
                                count(if(pass_result >$pass_range_from && pass_result<=$pass_range_to,1,null)) as pass 
                                FROM
                        (
                        SELECT du.region_id,art.assessment_id,
                        IFNULL(FORMAT(avg(art.score),2),0) AS pass_result FROM ai_subparameter_score as art
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	INNER JOIN device_users du ON du.user_id=art.user_id
                        LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                        WHERE art.parameter_type='parameter' AND du.region_id IN($region_id)";
            $query .= $cond;
            $query .="  GROUP BY art.user_id,art.assessment_id,du.region_id) as a 
                                        GROUP BY a.assessment_id,a.region_id
                                ) as b 
                    ON  b.assessment_id=a.assessment_id
                    LEFT JOIN assessment_mst amm ON amm.id=a.assessment_id ";
            $result = $this->db->query($query);
            // print_r($query);
                return $result->result();
        }else{
            $query="select a.assessment_id,amm.assessment,a.total_users,IFNULL(b.pass,0) as pass,IFNULL(b.fail,0) as fail 
                            FROM (
                                    SELECT du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                                        FROM assessment_mst am
                                        LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                                        INNER JOIN device_users du ON du.user_id=atm.user_id
                                        WHERE 1=1 AND du.region_id IN($region_id)";
            $query .= $cond;
            $query .=" having total_users>0 ";
            $query .=" UNION ALL 					
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                                        FROM assessment_mst am
                                        LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                                        INNER JOIN device_users du ON du.user_id=atm.user_id
                                        WHERE 1=1 AND atm.is_completed =1 AND du.region_id IN($region_id) AND
                                        atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )";
            $query .= $cond;
            $query .=" GROUP BY am.id,du.region_id
                                ) as a	
                        LEFT JOIN 
                                (
                                SELECT a.region_id,a.assessment_id,count(if(pass_result >$fail_range_from && pass_result<=$fail_range_to,1,null)) as fail,
                                        count(if(pass_result >$pass_range_from && pass_result<=$pass_range_to,1,null)) as pass 
                                        FROM
                        (
                        SELECT du.region_id,art.assessment_id,
                        IFNULL(FORMAT(avg(art.score),2),0) AS pass_result FROM ai_subparameter_score as art
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	INNER JOIN device_users du ON du.user_id=art.user_id
                        WHERE art.parameter_type='parameter' AND du.region_id IN($region_id)";
            $query .= $cond;
            $query .="  GROUP BY art.user_id,art.assessment_id,du.region_id) as a 
                                        GROUP BY a.assessment_id,a.region_id
                                ) as b 
                    ON  b.assessment_id=a.assessment_id
                    LEFT JOIN assessment_mst amm ON amm.id=a.assessment_id";
            // $query.= "UNION ALL";
            $query1="select a.assessment_id,amm.assessment,a.total_users,IFNULL(b.pass,0) as pass,IFNULL(b.fail,0) as fail 
            FROM (
                    SELECT du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                        FROM assessment_mst am
                        LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE 1=1 AND du.region_id IN($region_id)";
            $query1 .= $cond;
            $query .=" having total_users>0 ";
            $query .=" UNION ALL        
                    SELECT du.region_id,am.id,count(distinct atm.user_id)
                        FROM assessment_mst am
                        LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                        INNER JOIN device_users du ON du.user_id=atm.user_id
                        WHERE 1=1 AND atm.is_completed =1 AND du.region_id IN($region_id) AND
                        atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )";
            $query1 .= $cond;
            $query1 .=" GROUP BY am.id,du.region_id
                ) as a	
                LEFT JOIN 
                (
                SELECT a.region_id,a.assessment_id,count(if(pass_result >$fail_range_from && pass_result<=$fail_range_to,1,null)) as fail,
                        count(if(pass_result >$pass_range_from && pass_result<=$pass_range_to,1,null)) as pass 
                        FROM
                (
                SELECT du.region_id,art.assessment_id,
                IFNULL(FORMAT(avg(art.accuracy),2),0) AS pass_result FROM assessment_final_results as art
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id	INNER JOIN device_users du ON du.user_id=art.user_id
                WHERE du.region_id IN($region_id)";
            $query1 .= $cond;
            $query1 .="  GROUP BY art.user_id,art.assessment_id,du.region_id) as a 
                        GROUP BY a.assessment_id,a.region_id
                ) as b 
            ON  b.assessment_id=a.assessment_id
            LEFT JOIN assessment_mst amm ON amm.id=a.assessment_id";
            $query3= "Select * from $query UNION all $query1";
            $result = $this->db->query($query3);       
            return $result->result();                    
        }
    }
    public function assessment_index_weekly_monthly($report_by,$StartDate = '', $EndDate = '', $report_type,$supervisor_id,$user_id = '') {
        $cond="";
        $ResultArray = array();$PeriodArray = array();$AssessArray = array();
        if ($StartDate != '' && $EndDate != '') {
            $cond .=" AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .=" OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        if($supervisor_id !=''){
            $cond .= " AND amg.trainer_id =" . $supervisor_id;
        }
        if($report_type=="2")
        {
            $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                    am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                        FROM assessment_final_results art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                        WHERE 1=1 ";
            $query .= $cond;
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by date(am.start_dttm) ";
        }elseif($report_type=="1"){
            $query = "SELECT FORMAT(avg(art.score),2)  AS result,	
                am.assessment, art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id 
                WHERE art.parameter_type='parameter' AND 1=1 ";
            $query .= $cond;
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by date(am.start_dttm) ";
        }else{
            $query="SELECT FORMAT(SUM(main.result)/count(main.result),2) as result,
                    main.assessment, main.assessment_id, main.wday  FROM 
                        (SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                           am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                                FROM assessment_final_results art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                                LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                                WHERE 1=1 $cond group by date(am.start_dttm)
                        UNION ALL
                        SELECT FORMAT(avg(art.score),2)  AS result,	
                            am.assessment, art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                            FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                            LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                        WHERE art.parameter_type='parameter'
                        AND 1=1 $cond group by date(am.start_dttm)
                        ) as main GROUP by main.assessment_id;";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wday] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        return $ResultArray;
    }
    public function assessment_index_yearly($report_by,$StartDate = '', $EndDate = '', $report_type, $supervisor_id) {
        $cond = '';
        $ResultArray = array();$PeriodArray = array();$AssessArray = array();
        if ($StartDate != '' && $EndDate != '') {
            $cond .=" AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .=" OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')";
        }
        if($supervisor_id !=''){
            $cond .= " AND amg.trainer_id =" . $supervisor_id;
        }
        if($report_type=="2"){
            $query = " SELECT FORMAT(avg(art.accuracy),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_final_results art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id           
                    WHERE 1=1 ";
            $query .= $cond;
            $query .= " group by month(am.start_dttm) ";
        }elseif($report_type=="1"){
            $query = " SELECT FORMAT(avg(art.score),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                        FROM ai_subparameter_score art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                        WHERE parameter_type='parameter' AND 1=1 ";
            $query .= $cond;
            $query .= " group by month(am.start_dttm) ";      
        }else{
            $query="SELECT FORMAT(SUM(main.result)/count(main.result),2) as result, main.wmonth, main.wday  FROM 
                (SELECT FORMAT(avg(art.accuracy),2) AS result, month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_final_results art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE 1=1 $cond group by month(am.start_dttm)
                UNION ALL 
                SELECT FORMAT(avg(art.score),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE art.parameter_type='parameter' AND 1=1  $cond group by month(am.start_dttm)
            ) as main";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wmonth] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        return $ResultArray ;
    }
     public function get_assessment_list($company_id, $userid_id, $start_date, $end_date)
     {
         
        $query = "SELECT amst.assessment, amst.id FROM assessment_mst as amst LEFT  JOIN 
        assessment_managers as am on am.assessment_id=amst.id"
                . " where  am.trainer_id =" . $userid_id;
        if ($start_date != '' && $end_date != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
/*  $query = "SELECT  distinct ar.id as assessment_id, ar.assessment FROM assessment_mst ar 	
        LEFT JOIN assessment_managers am ON am.assessment_id=ar.id"
                . " where  am.trainer_id =" . $userid_id;
                if ($start_date != '' && $end_date != '') {
                    $query .=" where ar.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                }
                if($userid_id !='')
                {
                    $query .= " AND am.trainer_id =" . $userid_id;
                }
                $query .= "  order by assessment asc "; */         
         $result=$this->db->query($query);
         return $result->result();
     }
     
     public function get_ai_total_assessment($Company_id, $start_date = '', $end_date = '', $region_id = '', $store_id = '', $supervisor_id)
        {          
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                FROM ai_subparameter_score  art
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                  LEFT JOIN assessment_managers asm ON asm.assessment_id=am.id
                  INNER JOIN device_users du ON du.user_id=art.user_id "
          . " where  am.company_id =" . $Company_id;

    if ($start_date != '' && $end_date != '') {
        $query .=" AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        $query .=" OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
    }
    if ($region_id != '') {
        $query .= " AND du.region_id =" . $region_id;
    }
    if($supervisor_id !='')
    {
        $query .= " AND asm.trainer_id =" . $supervisor_id;
    }
    if ($store_id != '') {
        $query .= " AND du.store_id =" . $store_id;
    }
    $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array)$RowSet) > 0) {
            $TotalASM = $RowSet->total_assessment;
        }
        return $TotalASM;
         
     }
     public function get_questions($company_id,$assessment_id,$user_id){
        $query  = "SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url,ar.vimeo_uri
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                    WHERE
                        ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id."' AND ar.trans_id > 0 AND ar.question_id > 0 
                    ORDER BY
                        ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                WHERE main.company_id = '".$company_id."' AND main.assessment_id = '".$assessment_id."' AND main.user_id='".$user_id."'
                ORDER BY
                   main.user_id, main.trans_id";
        
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_distinct_participants($company_id,$assessment_id,$manager_id){
        $query  = "SELECT distinct company_id,assessment_id,user_id,user_name FROM (SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id ";
		if(!empty($manager_id)){
			$query .= "LEFT JOIN assessment_mapping_user amu ON amu.user_id=ar.user_id ";
		}
		$query .= " WHERE
                        ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id."' AND ar.trans_id > 0 AND ar.question_id > 0 ";
		if(!empty($manager_id)){
			$query .= " AND amu.trainer_id = '".$manager_id."'";
		}
		$query .= " ORDER BY
                        ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                ORDER BY
                   main.user_id, main.trans_id) AS final ORDER BY user_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_participant($company_id,$assessment_id1)     
    {
        if($assessment_id1!='')
        {
            $query="SELECT
            main.*,@dcp AS previous,
            CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
            @dcp := main.user_id AS current,
            CONCAT(main.user_id,'-',main.question_id) as uid	 
        FROM(
            SELECT DISTINCT
                ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url
            FROM
                assessment_results AS ar
                LEFT JOIN company AS c ON ar.company_id = c.id
                LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                LEFT JOIN assessment_question as aq on ar.question_id=aq.id
            WHERE
                ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id1."' AND ar.trans_id > 0 AND ar.question_id > 0 
            ORDER BY
                ar.user_id, ar.trans_id 
            ) AS main
            CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
        ORDER BY
        main.user_id, main.trans_id";
        //    echo $query;
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    // ==================================================trainee Dashboard =================================================================//


    public function get_Total_Assessment_trainee($Company_id, $start_date = '', $end_date = '', $user_id = '', $report_type) {
        if($report_type=="2")
        {
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM assessment_trainer_weights art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id "
                . " where am.company_id =" . $Company_id;
            if ($start_date != '' && $end_date != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $result = $this->db->query($query);
            $RowSet = $result->row();
            $TotalASM = 0;
            if (count((array)$RowSet) > 0) {
                $TotalASM = $RowSet->total_assessment;
            }
            return $TotalASM;
        }
        elseif($report_type=="1")
        {
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM ai_subparameter_score art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id "
                . " where am.company_id =" . $Company_id;
            if ($start_date != '' && $end_date != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $result = $this->db->query($query);
            $RowSet = $result->row();
            $TotalASM = 0;
            if (count((array)$RowSet) > 0) {
                $TotalASM = $RowSet->total_assessment;
            }
            return $TotalASM;
        }
        else
        {
            $cond="";
            if ($start_date != '' && $end_date != '') {
                $cond .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($user_id != '') {
                $cond .= " AND art.user_id =" . $user_id;
            }
            // $query="SELECT SUM(main.total_assessment) as total_assessment FROM (select IFNULL(count(distinct am.id),0) as total_assessment 
            // FROM assessment_trainer_weights art
            // LEFT JOIN assessment_mst am ON am.id=art.assessment_id
            // where 1=1 $cond
          
            // UNION ALL
                              
            // select IFNULL(count(distinct am.id),0) as total_assessment 
            // FROM ai_subparameter_score  art
            // LEFT JOIN assessment_mst am ON am.id=art.assessment_id
            // where 1=1 $cond) as main";
            $query = "SELECT count(total) as total_assessment FROM 
            (SELECT count(total_assessment) as total FROM 
                (SELECT DISTINCT am.id as total_assessment 
                FROM assessment_trainer_weights art 
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                LEFT JOIN device_users du ON du.user_id=art.user_id 
                WHERE 1=1 $cond
            UNION ALL 
                SELECT DISTINCT am.id as total_assessment 
                FROM ai_subparameter_score art 
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                LEFT JOIN device_users du ON du.user_id=art.user_id 
                WHERE 1=1 $cond
                )as main GROUP BY total_assessment
            ) as main2";
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array)$RowSet) > 0) {
            $TotalASM = $RowSet->total_assessment;
        }
        return $TotalASM;
    }
	public function get_assessment_trainee($user_id='', $StartDate, $EndDate, $report_type) {
        if($report_type== "2")
        {
            $query = "SELECT distinct ar.assessment_id,am.assessment 
                        FROM assessment_complete_rating ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        WHERE 1=1";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if($user_id!='')
            {
                $query .=" AND ar.user_id  = '".$user_id."'";
            }
            $query .= " order by am.assessment asc ";
            $result = $this->db->query($query);
            return $result->result();
        }
        elseif($report_type=="1" || $report_type=="3")
        {
            $query = "SELECT distinct ar.assessment_id,am.assessment 
                        FROM ai_subparameter_score ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        WHERE 1=1";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if($user_id!='')
            {
                $query .=" AND ar.user_id  = '".$user_id."'";
            }
            $query .= " order by am.assessment asc ";        
            $result = $this->db->query($query);
            return $result->result();
        }
    }
    public function get_Total_Questions_Time($Company_id, $start_date = '', $end_date = '', $user_id = '', $report_type) {
        if($report_type=="2")
        {
             $query="SELECT count(DISTINCT(question_id)) as question_answer FROM `assessment_results`
             as ar left JOIN assessment_managers as am on 
             am.assessment_id=ar.assessment_id
             LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
           
              WHERE 1=1";
             if ($start_date != '' && $end_date != '') {
                $query .=" AND amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($user_id != '') {
                $query .= " AND ar.user_id =" . $user_id;
            }
            
        }
        elseif($report_type=="1")
        {
            $query="SELECT count(DISTINCT(question_id)) as question_answer FROM `ai_schedule`
            as ar
            LEFT JOIN assessment_managers  am on 
            am.assessment_id=ar.assessment_id  
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
           
             where 1=1";
            if ($start_date != '' && $end_date != '') {
                $query .=" AND amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($user_id != '') {
                $query .= " AND ar.user_id =" . $user_id;
            }
        }
        else
        {
            $cond="";
            if ($start_date != '' && $end_date != '') {
                $cond .=" AND amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($user_id != '') {
                $cond .= " AND ar.user_id =" . $user_id;
            }

            $query=" SELECT (main.question_answer) as question_answer FROM
            (SELECT count(DISTINCT(question_id)) as question_answer FROM `assessment_results`
            as ar left JOIN assessment_managers as am on 
            am.assessment_id=ar.assessment_id
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
            LEFT JOIN device_users du ON du.user_id=ar.user_id
            where 1=1 $cond

            UNION ALL
            
            SELECT count(DISTINCT(question_id)) as question_answer FROM `ai_schedule`
            as ar
            LEFT JOIN assessment_managers  am on 
            am.assessment_id=ar.assessment_id  
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
             where 1=1 $cond) as main
            
            ";
           
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        return $TotalAccuracy = $RowSet->question_answer;
		
    }
    public function get_Trainee_data($Company_id,$manager_id){
		$query = "SELECT DISTINCT au.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
					FROM assessment_mapping_user au LEFT JOIN assessment_mst am ON am.id = au.assessment_id 
					INNER JOIN device_users du ON du.user_id=au.user_id 
					WHERE am.company_id = $Company_id ";
		if(!empty($manager_id)){
			$query .= " AND au.trainer_id = '".$manager_id."'";
		}			
		$query .= " ORDER BY traineename";
		$result = $this->db->query($query);
        return $result->result();
	}
    public function get_Trainee($Company_id, $start_date = '', $end_date = '') {
        $query = "SELECT distinct A.user_id,A.traineename
                    FROM(
                         SELECT ar.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                                FROM assessment_attempts ar
							LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
                                                        INNER JOIN device_users du ON du.user_id=ar.user_id
                                WHERE ar.user_id NOT IN( SELECT user_id FROM assessment_allow_users where assessment_id = ar.assessment_id) 
                                AND am.company_id = " . $Company_id;
        if ($start_date != '' && $end_date != '') {
            $query .=" AND (am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .=" OR am.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }
        $query .= "	UNION ALL			
                                SELECT  au.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                                        FROM assessment_allow_users au		
                                        LEFT JOIN assessment_mst am ON am.id = au.assessment_id
                                        INNER JOIN device_users du ON du.user_id=au.user_id 
                                        WHERE am.company_id = " . $Company_id;
        if ($start_date != '' && $end_date != '') {
            $query .=" AND (am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .=" OR am.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }
       
        $query .= "	)A ORDER BY A.traineename";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_MaxMin_Accuracy_trainee($Company_id, $start_date = '', $end_date = '', $report_by, $user_id = '') {
        $query = " SELECT MAX(a.result) AS max_accuracy,IF(COUNT(a.result) > 1,MIN(a.result),0) AS min_accuracy
                FROM(
                SELECT IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) AS result,
                SUM(art.score)/ SUM(art.weight_value) AS ord_res    
                FROM assessment_trainer_weights art  				 	   
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                WHERE 1=1 ";
//                    WHERE ar.company_id = " . $Company_id . " AND art.question_id !='' ";
        if ($start_date != '' && $end_date != '') {
            $query .=" AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($user_id != '') {
            $query .= " AND art.user_id =" . $user_id;
        }
        if ($report_by == 1) {
            $query .= " group by art.parameter_id ";
        } else {
            $query .= " group by art.assessment_id ";
        }
        $query .= " ) as a ";
        $result = $this->db->query($query);
        $RowSet = $result->row();
        return $RowSet;
    }
    public function get_Average_Accuracy_trainee($Company_id,$report_by, $start_date = '', $end_date = '', $user_id = '', $report_type) {
    if($report_type=="2")
        {
		    if ($report_by == 0) {
			    $query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0) as avg_result FROM assessment_final_results as a 
			    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1";
		    }else{
			    $query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0)as avg_result FROM assessment_trainer_weights as a 
			    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1";
    		}		
            if ($start_date != '' && $end_date != '') {
                $query .=" AND b.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
             }
            if ($user_id != '') {
                $query .= " AND a.user_id =" . $user_id;
            }
            
            $result = $this->db->query($query);
            $RowSet = $result->row();
            $TotalAccuracy = 0;
            if (count((array)$RowSet) > 0) {
                $TotalAccuracy = $RowSet->avg_result;
            }
            return $TotalAccuracy;
        }
        
        elseif($report_type=="1")
        {
            
			$query = "SELECT ifnull(FORMAT(avg(a.score),2),0)as avg_result FROM ai_subparameter_score as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1 AND a.parameter_type='parameter'";
    		
            if ($start_date != '' && $end_date != '') {
                $query .=" AND b.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
             }
            if ($user_id != '') {
                $query .= " AND a.user_id =" . $user_id;
            }
            $result = $this->db->query($query);
            $RowSet = $result->row();
            $TotalAccuracy = 0;
            if (count((array)$RowSet) > 0) {
                $TotalAccuracy = $RowSet->avg_result;
            }
            return $TotalAccuracy;
        }
        else
        {
            $cond="";
        if ($start_date != '' && $end_date != '') {
            $cond .=" AND date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($user_id != '') {
            $cond .= " AND a.user_id =" . $user_id;
        }
        
        if($report_by==0)
        {
            $query="SELECT ifnull(FORMAT(SUM(main.avg_result)/count(main.avg_result),2),0) as avg_result FROM 
            (SELECT FORMAT(avg(a.accuracy),2) as avg_result FROM assessment_final_results as a 
                        LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                        LEFT JOIN device_users du ON du.user_id=a.user_id 
                        LEFT JOIN assessment_managers asm ON asm.assessment_id=a.assessment_id
                        where 1=1 $cond 
                                           
                      UNION ALL
                      SELECT FORMAT(avg(a.score),2) as avg_result FROM ai_subparameter_score as a 
                        LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                        LEFT JOIN device_users du ON du.user_id=a.user_id 
                        LEFT JOIN assessment_managers asm ON asm.assessment_id=a.assessment_id 
                        WHERE a.parameter_type='parameter' AND 1=1 $cond group by a.assessment_id               
            ) as main";
        }
        else
        {
            $query="SELECT FORMAT(SUM(main.avg_result)/count(main.avg_result),2) as avg_result FROM 
            (SELECT ifnull(FORMAT(avg(a.accuracy),2),0)as avg_result FROM assessment_trainer_weights as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN device_users du ON du.user_id=a.user_id	
            LEFT JOIN assessment_managers asm ON asm.assessment_id=a.assessment_id 
            WHERE 1=1 $cond
                      UNION ALL
                      SELECT ifnull(FORMAT(avg(a.score),2),0)as avg_result FROM ai_subparameter_score as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN device_users du ON du.user_id=a.user_id	
            LEFT JOIN assessment_managers asm ON asm.assessment_id=a.assessment_id 
            WHERE  a.parameter_type='parameter'
                       AND 1=1 $cond group by a.parameter_id) as main";           
        }
        
        $result = $this->db->query($query);
        $RowSet = $result->row();
        
        $TotalAccuracy = 0;
        if (count((array)$RowSet) > 0) {
            $TotalAccuracy = $RowSet->avg_result;
        }
        return $TotalAccuracy;
        }
    }
    public function get_top_five_parameter_trainee($Company_id, $report_by, $SDate = '', $EDate = '',$user_id ,$report_type) {
        if($report_type=="2")
        {
                 if ($report_by == 0) {
                     $query = "
                     SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy)
                     as order_wt,b.assessment FROM assessment_final_results as a 
                     LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                     LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                     LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
                 }else{
                     $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                  FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                     LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
                }
                if ($SDate != '' && $EDate != '') {
                     $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                 }
                 if ($user_id != '') {
                    $query .= " AND a.user_id =" . $user_id;
                }
                
                 if ($report_by == 0) {
                     $query .= " group by a.assessment_id order by order_wt desc limit 0,5 ";
                 }else{
                     $query .= " group by a.parameter_id order by order_wt desc limit 0,5 ";
                 }
            }
            //AI
            elseif($report_type=="1"){
                if ($report_by == 0) {
                    $query = "SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE parameter_type='parameter' AND 1=1";
                }else{
                    $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                    FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter'
                    AND 1=1";
               }
               if ($SDate != '' && $EDate != '') {
                    $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                }
                if ($user_id != '') {
                    $query .= " AND a.user_id =" . $user_id;
                }
                if ($report_by == 0) {
                    $query .= " group by a.user_id, a.assessment_id order by order_wt desc limit 0,5 ";
                }else{
                    $query .= " group by a.user_id, a.parameter_id order by order_wt desc limit 0,5 ";
                }
            }        
            //AI and Manual
            else
            {
                $cond="";
                if ($SDate != '' && $EDate != '') {
                    $cond .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                }
                if ($user_id != '') {
                    $cond .= " AND a.user_id =" . $user_id;
                }
            
                if($report_by==0)
                {
                    $query="SELECT distinct main.assessment_id, main.result, main.order_wt, main.assessment  FROM 
                    (SELECT distinct a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,b.assessment FROM assessment_final_results as a 
                     LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                     LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                     LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1   $cond
                     group by a.assessment_id 
                               
                              UNION ALL
                            SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE parameter_type='parameter' AND 1=1  $cond
                    group by a.user_id, a.assessment_id                
                    ) as main group by main.assessment_id order by main.order_wt desc limit 0,5";
                }
                else
                {
                    $query="SELECT main.assessment_id, main.parameter_id, main.result, main.order_wt, main.parameter  FROM 
                    (SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                  FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                     LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1
                     group by a.parameter_id
                               
                              UNION ALL
                          SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                    FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter' AND 1=1 
                    group by a.user_id, a.parameter_id
                    ) as main group by main.assessment_id order by main.order_wt desc limit 0,5";
                }   
            }
             $result = $this->db->query($query);
             return $result->result();                         
    }
    public function get_time($Company_id, $start_date = '', $end_date = '', $user_id)
    {
        $query = " SELECT IFNULL(count(a.question_id),0) as total_questions,IF(sum(a.vtime) > 0,SEC_TO_TIME(sum(a.vtime)),'00:00:00') as total_time 
                  FROM(
                  select ar.question_id, IFNULL(sum(ar.video_duration),0) as vtime  
                  FROM assessment_results ar 				 	   
                    LEFT JOIN assessment_mst am ON am.id=ar.assessment_id "
                . " where ar.company_id =" . $Company_id. " AND ar.question_id !='' ";
        if ($start_date != '' && $end_date != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($user_id != '') {
            $query .= " AND ar.user_id =" . $user_id;
        }
        $query .=" group by ar.user_id, ar.assessment_id,ar.question_id ) AS a";
        //echo $query;
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_top_five_parameter_old($Company_id, $report_by, $SDate = '', $EDate = '', $user_id = '') {
		if ($report_by == 0) {
			$query = "SELECT a.assessment_id,a.accuracy as result,b.assessment FROM assessment_final_results as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1";
		}else{
			$query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
			FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id WHERE 1=1";
		}
        if ($SDate != '' && $EDate != '') {
            $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
        }
        if ($user_id != '') {
            $query .= " AND a.user_id =" . $user_id;
        }
		if ($report_by == 0) {
			$query .= " group by a.assessment_id order by result desc limit 0,5 ";
		}else{
			$query .= " group by a.parameter_id order by order_wt desc limit 0,5 ";
		}
		//echo $query;
		//exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_bottom_five_parameter_trainee($Company_id, $report_by, $top_five_para_id, $SDate = '', $EDate = '', $user_id = '', $report_type) {
    if($report_type=="2")
        {
             if ($report_by == 0) {
                 $query = "SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy)
                 as order_wt,b.assessment FROM assessment_final_results as a 
                 LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                 LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                 LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
             }else{
                 $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
              FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                 LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }
            if ($SDate != '' && $EDate != '') {
                $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND a.user_id =" . $user_id;
            }
       
            if ($report_by == 1) {
                 $query .= " AND pm.id NOT IN (" . $top_five_para_id . ") ";
            } else {
                 $query .= " AND a.assessment_id NOT IN (" . $top_five_para_id . ") ";
            }
            
             if ($report_by == 0) {
                 $query .= " group by a.assessment_id order by order_wt asc limit 0,5 ";
             }else{
                 $query .= " group by a.parameter_id order by order_wt asc limit 0,5 ";
             }
        }
        //AI
        elseif($report_type=="1"){
            if ($report_by == 0) {
                $query = "SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                WHERE parameter_type='parameter' AND 1=1";
            }else{
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter'
                AND 1=1";
           }
           if ($SDate != '' && $EDate != '') {
            $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            }
            if ($user_id != '') {
            $query .= " AND a.user_id =" . $user_id;
            }
       
           if ($report_by == 1) {
                $query .= " AND pm.id NOT IN (" . $top_five_para_id . ") ";
           } else {
                $query .= " AND a.assessment_id NOT IN (" . $top_five_para_id . ") ";
           }
       
            if ($report_by == 0) {
                $query .= " group by a.user_id, a.assessment_id order by order_wt asc limit 0,5 ";
            }else{
                $query .= " group by a.user_id, a.parameter_id order by order_wt asc limit 0,5 ";
            }
        }        
        //AI and Manual
        else
        {
            $cond="";
            if ($SDate != '' && $EDate != '') {
                $cond .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            }
            if ($user_id != '') {
                $cond .= " AND a.user_id =" . $user_id;
            }
            if ($report_by == 1) {
                $cond .= " AND pm.id NOT IN (" . $top_five_para_id . ") ";
            } else {
                $cond .= " AND a.assessment_id NOT IN (" . $top_five_para_id . ") ";
            }
       
            if($report_by==0)
            {
                $query="SELECT main.assessment_id, main.result, main.order_wt, main.assessment  FROM 
                (SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,b.assessment FROM assessment_final_results as a 
                 LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                 LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                 LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1   $cond
                 group by a.assessment_id 
                           
                          UNION ALL
                        SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                WHERE parameter_type='parameter' AND 1=1  $cond
                group by a.user_id, a.assessment_id                
                ) as main group by main.assessment_id order by main.order_wt asc limit 0,5;";
            }
            else
            {
                $query="SELECT main.assessment_id, main.result, main.order_wt, main.parameter  FROM 
                (SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
              FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                 LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1
                 group by a.parameter_id
                           
                          UNION ALL
                      SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter' AND 1=1 
                group by a.user_id, a.parameter_id
                ) as main group by main.parameter_id order by main.order_wt asc limit 0,5;";
            }   
        }
         $result = $this->db->query($query);
         return $result->result();
    }

    public function get_user_trainee($company_id) {
        $query = " SELECT distinct ar.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                    FROM assessment_final_results ar INNER JOIN device_users du ON du.user_id=ar.user_id
                    WHERE du.company_id = $company_id  ORDER BY traineename";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_parameter_trainee($company_id,$user_id='') {
        $query = " SELECT distinct ar.parameter_id,pm.description AS parameter
                    FROM assessment_trainer_weights ar LEFT JOIN parameter_mst pm ON pm.id=ar.parameter_id
					LEFT JOIN assessment_mst am ON am.id=ar.assessment_id WHERE 1=1";
        if ($user_id != '') {
            $query .= " AND ar.user_id =" . $user_id;
        }
        $query .=" order by parameter ";
        $result = $this->db->query($query);
        return $result->result();
    }

    

    public function assessment_index_weekly_monthly_trainee($report_by,$StartDate = '', $EndDate = '', $user_id = '', $report_type) {
        if($report_type=="2")
        {
            $ResultArray = array();$PeriodArray = array();$AssessArray = array();
            $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                        FROM assessment_final_results art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                        WHERE 1=1 ";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by date(am.start_dttm) ";
        }
        elseif($report_type=="1")
        {
            $ResultArray = array();$PeriodArray = array();$AssessArray = array();
            $query = "SELECT FORMAT(avg(art.score),2)  AS result,	
            am.assessment, art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
             FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
             WHERE art.parameter_type='parameter' AND 1=1 ";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by date(am.start_dttm) ";
        }
        else
        {
            $ResultArray = array();$PeriodArray = array();$AssessArray = array();
            $cond="";
            if ($StartDate != '' && $EndDate != '') {
                    $cond .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                    $cond .= " AND art.user_id =" . $user_id;
            }
            $query="SELECT FORMAT(SUM(main.result)/count(main.result),2) as result,
            main.assessment, main.assessment_id, main.wday  FROM 
                        (SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                               am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                                FROM assessment_final_results art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                                LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                                 WHERE 1=1 $cond group by date(am.start_dttm)
                          
                          UNION ALL
                                              
                          SELECT FORMAT(avg(art.score),2)  AS result,	
                    am.assessment, art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                     FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                     LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                     WHERE art.parameter_type='parameter'
                                AND 1=1 $cond group by date(am.start_dttm)
                        ) as main GROUP by main.assessment_id;";
        }
    
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wday] = $value->result;
            }
        }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
    }
    
    public function assessment_index_yearly_trainee($report_by,$StartDate = '', $EndDate = '', $user_id = '', $report_type) {
        if($report_type=="2")
        {
        $ResultArray = array();$PeriodArray = array();$AssessArray = array();
        $query = " SELECT FORMAT(avg(art.accuracy),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_final_results art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id           
                    WHERE 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($user_id != '') {
            $query .= " AND art.user_id =" . $user_id;
        }
        
        $query .= " group by month(am.start_dttm) ";
        }
        elseif($report_type=="1")
        {
            $ResultArray = array();$PeriodArray = array();$AssessArray = array();
         $query = " SELECT FORMAT(avg(art.score),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE parameter_type='parameter' AND 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($user_id != '') {
            $query .= " AND art.user_id =" . $user_id;
        }
        $query .= " group by month(am.start_dttm) ";      
        }
        else
        {   
            $ResultArray = array();$PeriodArray = array();$AssessArray = array();
            $cond="";
            if ($StartDate != '' && $EndDate != '') {
                $cond .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $cond .= " AND art.user_id =" . $user_id;
            }
            
            $query="SELECT FORMAT(SUM(main.result)/count(main.result),2) as result, main.wmonth, main.wday  FROM 
            (SELECT FORMAT(avg(art.accuracy),2) AS result, month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_final_results art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE 1=1 $cond group by month(am.start_dttm)
              
              UNION ALL
                                  
              SELECT FORMAT(avg(art.score),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE art.parameter_type='parameter' AND 1=1  $cond group by month(am.start_dttm)
            ) as main";
            
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wmonth] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        return $ResultArray ;
    }
    
        
	public function parameter_index_charts_new($parameter_id,$report_by,$StartDate = '', $EndDate = '', $user_id = '', $report_type) {
        $PeriodArray = array();
        if($report_type=="2"){
            $query="SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, p.id, ps.parameter_label_id, round(sum(ps.percentage)/count(*),2) as result FROM assessment_results_trans as ps 
            LEFT join parameter_mst as p on ps.parameter_id=p.id
            LEFT join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE 1=1 ";
                if ($StartDate != '' && $EndDate != '') {
            
                    $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                }
                if ($user_id != '') {
                $query .= " AND ps.user_id =" . $user_id;
                }
                if($parameter_id !=''){
                    $query .=" AND ps.assessment_id=".$parameter_id;
                }
                if($parameter_id !=''){
                    $query .=" AND ps.assessment_id=".$parameter_id;
                }
                $query.= " GROUP BY ps.parameter_id, ps.parameter_label_id
                ORDER BY ps.parameter_id,ps.parameter_label_id;";
        }elseif($report_type=="1"){
            /*$query="SELECT DISTINCT  ps.parameter_id as parameter_id, ps.parameter_label_id, p.description as parameter_name,round(sum(ps.score)/count(*),2) as result FROM ai_subparameter_score as ps 
            left join parameter_mst as p on ps.parameter_id = p.id
            left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE ps.parameter_type ='parameter'  AND 1=1";*/
        
            $query="SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, ps.parameter_id as parameter_id, ps.parameter_label_id, round(sum(ps.score)/count(*),2) as result FROM ai_subparameter_score as ps 
            left join parameter_mst as p on ps.parameter_id = p.id
            left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE ps.parameter_type ='parameter'  AND 1=1
            ";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
            $query .= " AND ps.user_id =" . $user_id;
            }
            if($parameter_id !=''){
			    $query .=" AND ps.assessment_id=".$parameter_id;
	    	}
            $query.= " GROUP BY ps.parameter_id, ps.parameter_label_id
            ORDER BY ps.parameter_id,ps.parameter_label_id;";
        }else{
            $cond= "";
            if ($StartDate != '' && $EndDate != '') {
            
                $cond .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $cond .= " AND ps.user_id =" . $user_id;
            }
            if($parameter_id !=''){
			    $cond .=" AND ps.assessment_id=".$parameter_id;
	    	}
            $cond .= " GROUP BY p.id, pl.id ";
            $query="SELECT main.parameter_name, main.parameter_id, main.parameter_label_id, ROUND(AVG(main.result),2) as result from
            (SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, ps.parameter_id as parameter_id, ps.parameter_label_id, round(sum(ps.score)/count(*),2) as result FROM ai_subparameter_score as ps 
            left join parameter_mst as p on ps.parameter_id = p.id
            left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE ps.parameter_type ='parameter'  AND 1=1 $cond
            UNION ALL
            SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, p.id, ps.parameter_label_id, round(sum(ps.percentage)/count(*),2) as result FROM assessment_results_trans as ps 
            LEFT join parameter_mst as p on ps.parameter_id=p.id
            LEFT join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE 1=1 $cond) as main GROUP by parameter_id, parameter_label_id"; 
        }

		//$query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        
        if (count((array)$Accuracy) > 0) {
            $x=0;
            foreach ($Accuracy as $value) {
               //$PeriodArray[$value->parameter_id] = array('parameter_name'=>$value->parameter_name,'result'=>$value->result);
               $PeriodArray[$x] = array('parameter_name'=>$value->parameter_name,'result'=>$value->result);
               $x++;
        
            }
        }
        
        return $PeriodArray;
    }
    public function parameter_index_charts($parameter_id,$report_by,$report_type,$StartDate = '', $EndDate = '', $user_id = '') {
        if($report_type=="2")
        {
            $PeriodArray = array();
            $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE 1=1 ";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            if($parameter_id !=''){
			    $query .=" AND art.parameter_id=".$parameter_id;
		    }
		    $query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";
        
            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name'=>$value->assessment,'result'=>$value->result);
                }
            }
            return $PeriodArray;
        }
        elseif($report_type=="1")
        {
            $PeriodArray = array();
            $query = " SELECT FORMAT(avg(art.score),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE parameter_type='parameter' AND 1=1 ";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            if($parameter_id !=''){
			    $query .=" AND art.parameter_id=".$parameter_id;
		    }
		    $query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";
            
            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name'=>$value->assessment,'result'=>$value->result);
                }
            }
            return $PeriodArray;
        }
        else
        {
            $PeriodArray = array();
            $cond='';
            $query = "SELECT SUM(main.result)/count(main.result) as result, main.assessment, main.assessment_id, main.wday from  (
                SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE 1=1 $cond
                UNION ALL
                SELECT FORMAT(avg(art.score),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE parameter_type='parameter' AND 1=1 $cond) as main ";
            if ($StartDate != '' && $EndDate != '') {
                $cond .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $cond  .= " AND art.user_id =" . $user_id;
            }
            if($parameter_id !=''){
			    $cond .=" AND art.parameter_id=".$parameter_id;
		    }
		    $query .= " group by main.assessment_id order by main.assessment_id desc limit 0,10";
            
            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name'=>$value->assessment,'result'=>$value->result);
                }
            }
            return $PeriodArray;
        }
    }
    public function parameter_index_charts_old($parameter_id,$report_by,$StartDate = '', $EndDate = '', $user_id = '') {
        $PeriodArray = array();
        $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                   am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($user_id != '') {
            $query .= " AND art.user_id =" . $user_id;
        }
        if($parameter_id !=''){
			$query .=" AND art.parameter_id=".$parameter_id;
		}
		$query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->assessment_id] = array('assessment_name'=>$value->assessment,'result'=>$value->result);
            }
        }
        return $PeriodArray;
    }
}