<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Role_play_rep_dashboard_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function get_Total_Assessment($Company_id, $start_date = '', $end_date = '', $user_id = '', $report_type)
    {
        $cond = "";
        if ($start_date != '' && $end_date != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $cond .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND art.user_id =" . $user_id;
        }
        if ($report_type == "2") {
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM assessment_trainer_weights art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    LEFT JOIN device_users du ON du.user_id=art.user_id "
                . " where  am.company_id =" . $Company_id . $cond;
        } else if ($report_type == "1") {
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                    FROM ai_subparameter_score  art
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                        LEFT JOIN device_users du ON du.user_id=art.user_id "
                . " where  am.company_id =" . $Company_id . $cond;
        } else {
            $query = "SELECT count(distinct total_assessment) as total_assessment 
            FROM (SELECT DISTINCT am.id as total_assessment 
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
                ) as main ";
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array) $RowSet) > 0) {
            $TotalASM = $RowSet->total_assessment;
        }
        return $TotalASM;
    }
    public function get_assessment($user_id = '', $StartDate, $EndDate, $report_type)
    {
        $query = "SELECT  distinct ar.id as assessment_id, ar.assessment as assessment FROM assessment_mst ar 	
			LEFT JOIN assessment_allow_users am ON am.assessment_id=ar.id where 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .= " AND (date(ar.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .= " OR date(ar.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .= " OR '" . $StartDate . "' BETWEEN date(ar.start_dttm) AND date(ar.end_dttm)";
            $query .= " OR '" . $EndDate . "' BETWEEN date(ar.start_dttm) AND date(ar.end_dttm))";
        }
        if ($user_id != '') {
            $query .= " AND am.user_id =" . $user_id;
        }
        $query .= "  order by assessment asc ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_old($user_id = '', $StartDate, $EndDate, $report_type)
    {
        if ($StartDate != '' && $EndDate != '') {
            $query .= " AND am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($user_id != '') {
            $query .= " AND ar.user_id  = '" . $user_id . "'";
        }
        if ($report_type == "2") {
            $query = "SELECT distinct ar.assessment_id,am.assessment 
                        FROM assessment_complete_rating ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        WHERE 1=1";
            $query .= " order by am.assessment asc ";
            $result = $this->db->query($query);
            return $result->result();
        } elseif ($report_type == "1") {
            $query = "SELECT distinct ar.assessment_id,am.assessment 
                        FROM ai_subparameter_score ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        WHERE 1=1";
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND ar.user_id  = '" . $user_id . "'";
            }
            $query .= " order by am.assessment asc ";

            $result = $this->db->query($query);
            return $result->result();
        }
    }
    public function get_Total_Questions_Time($Company_id, $start_date = '', $end_date = '', $user_id = '', $report_type)
    {
        $cond = '';
        if ($start_date != '' && $end_date != '') {
            $cond .= " AND (date(amst.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .= " OR date(amst.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .= " OR '" . $start_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm)";
            $cond .= " OR '" . $end_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND ar.user_id =" . $user_id;
        }
        if ($report_type == "2") {
            $query = "SELECT count(DISTINCT(question_id)) as question_answer 
            FROM assessment_results_trans as ar 
            LEFT JOIN assessment_mst as amst ON amst.id=ar.assessment_id  
            LEFT JOIN device_users as du ON du.user_id=ar.user_id 
            WHERE 1=1 " . $cond;
        } elseif ($report_type == "1") {
            $query = "SELECT count(DISTINCT(question_id)) as question_answer 
            FROM ai_subparameter_score as ar 
            LEFT JOIN assessment_mst as amst ON amst.id=ar.assessment_id 
            LEFT JOIN device_users as du ON du.user_id=ar.user_id 
            WHERE 1=1 " . $cond;
        } else {
            $query = " SELECT count(DISTINCT main.question_answer) as question_answer 
            FROM 
            (SELECT DISTINCT(question_id) as question_answer 
                FROM assessment_results_trans as ar 
                LEFT JOIN assessment_mst as amst ON amst.id=ar.assessment_id 
                LEFT JOIN device_users as du ON du.user_id=ar.user_id
                WHERE 1=1 $cond 
            UNION ALL
                SELECT DISTINCT(question_id) as question_answer 
                FROM ai_subparameter_score as ar  
                LEFT JOIN assessment_mst as amst ON amst.id=ar.assessment_id 
                LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                WHERE 1=1 $cond
            ) as main";
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        return $TotalAccuracy = $RowSet->question_answer;
    }
    public function get_Trainee_data($Company_id, $manager_id)
    {
        $query = "SELECT DISTINCT au.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
					FROM assessment_mapping_user au LEFT JOIN assessment_mst am ON am.id = au.assessment_id 
					INNER JOIN device_users du ON du.user_id=au.user_id 
					WHERE am.company_id = $Company_id ";
        if (!empty($manager_id)) {
            $query .= " AND au.trainer_id = '" . $manager_id . "'";
        }
        $query .= " ORDER BY traineename";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_Trainee($Company_id, $start_date = '', $end_date = '')
    {
        $query = "SELECT distinct A.user_id,A.traineename
                    FROM(
                         SELECT ar.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                                FROM assessment_attempts ar
							LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
                                                        INNER JOIN device_users du ON du.user_id=ar.user_id
                                WHERE ar.user_id NOT IN( SELECT user_id FROM assessment_allow_users where assessment_id = ar.assessment_id) 
                                AND am.company_id = " . $Company_id;
        if ($start_date != '' && $end_date != '') {
            $query .= " AND (am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR am.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }
        $query .= "	UNION ALL			
                                SELECT  au.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                                        FROM assessment_allow_users au		
                                        LEFT JOIN assessment_mst am ON am.id = au.assessment_id
                                        INNER JOIN device_users du ON du.user_id=au.user_id 
                                        WHERE am.company_id = " . $Company_id;
        if ($start_date != '' && $end_date != '') {
            $query .= " AND (am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR am.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        }

        $query .= "	)A ORDER BY A.traineename";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_MaxMin_Accuracy($Company_id, $start_date = '', $end_date = '', $report_by, $user_id = '')
    {
        $query = " SELECT MAX(a.result) AS max_accuracy,IF(COUNT(a.result) > 1,MIN(a.result),0) AS min_accuracy
                FROM(
                SELECT IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) AS result,
                SUM(art.score)/ SUM(art.weight_value) AS ord_res    
                FROM assessment_trainer_weights art  				 	   
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                WHERE 1=1 ";
        //                    WHERE ar.company_id = " . $Company_id . " AND art.question_id !='' ";
        if ($start_date != '' && $end_date != '') {
            $query .= " AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
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
    public function get_Average_Accuracy($Company_id, $report_by, $start_date = '', $end_date = '', $user_id = '', $report_type)
    {
        $cond = "";
        if ($start_date != '' && $end_date != '') {
            $cond .= " AND (date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .= " OR date(b.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $cond .= " OR '" . $start_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
            $cond .= " OR '" . $end_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND a.user_id =" . $user_id;
        }
        if ($report_by == 1) {
            $grpby = " group by a.parameter_id ";
        } else {
            $grpby = " group by a.assessment_id ";
        }
        if ($report_type == "2") { //Manual
            $query = "SELECT ifnull(FORMAT(avg(main.avg_result),2),0) as avg_result 
            FROM (
                SELECT round(ifnull(avg(a.accuracy),0),2) as avg_result 
                FROM assessment_trainer_weights as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN device_users du ON du.user_id=a.user_id ";
            $query .= " WHERE du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . ") as main";
        } elseif ($report_type == "1") {
            $query = "SELECT ifnull(FORMAT(avg(main.avg_ass_result),2),0) as avg_result 
                FROM (
                    SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as avg_ass_result 
                    FROM ai_subparameter_score as a
                    LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=a.parameter_id AND ats.parameter_label_id=a.parameter_label_id AND ats.assessment_id=a.assessment_id AND ats.question_id=a.question_id  
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN device_users du ON du.user_id=a.user_id ";
            $query .= " WHERE a.parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . ") as main";
        } else {
            $query = " SELECT IF(SUM(main.cnt) > 1, FORMAT(avg(main.avg_result),2),FORMAT(SUM(main.avg_result),2)) AS avg_result  
                FROM (
                    SELECT ifnull(FORMAT(avg(main1.avg_result),2),0) as avg_result,IF(ifnull(FORMAT(avg(main1.avg_result),2),0) > 0,1,0) as cnt 
                    FROM (
                        SELECT round(ifnull(avg(a.accuracy),0),2) as avg_result  
                        FROM assessment_trainer_weights as a 
                        LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                        LEFT JOIN device_users du ON du.user_id=a.user_id ";
            $query .= " WHERE du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . ") as main1 
                    UNION ALL
                    SELECT ifnull(FORMAT(avg(main2.avg_ass_result),2),0) as avg_result, IF(ifnull(FORMAT(avg(main2.avg_ass_result),2),0) > 0,1,0) as cnt 
                    FROM (
                        SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as avg_ass_result 
                        FROM ai_subparameter_score as a
                        LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=a.parameter_id AND ats.parameter_label_id=a.parameter_label_id AND ats.assessment_id=a.assessment_id AND ats.question_id=a.question_id  
                        LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                        LEFT JOIN device_users du ON du.user_id=a.user_id ";
            $query .= " WHERE a.parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . ") as main2) as main";
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();

        $TotalAccuracy = 0;
        if (count((array) $RowSet) > 0) {
            $TotalAccuracy = $RowSet->avg_result;
        }
        return $TotalAccuracy;
    }
    public function get_top_five_parameter($Company_id, $report_by, $SDate = '', $EDate = '', $user_id, $report_type)
    {
        $cond = '';
        if ($SDate != '' && $EDate != '') {
            $cond .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
            $cond .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND a.user_id =" . $user_id;
        }
        if ($report_by == 0) {
            $grpby = " group by a.assessment_id order by order_wt desc ";
            $maingrpby = " group by main.assessment_id order by main.order_wt desc limit 0,5 ";
        } else {
            $grpby = " group by a.parameter_id order by order_wt desc ";
            $maingrpby = " group by main.parameter_id order by main.order_wt desc limit 0,5 ";
        }
        if ($report_type == "2") {
            $query = "SELECT a.assessment_id,b.assessment,a.parameter_id,pm.description as parameter, ifnull(FORMAT(avg(a.accuracy),2),0) as result, avg(a.accuracy) as order_wt 
                FROM assessment_trainer_weights as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id 
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . " limit 0,5 ";
        } elseif ($report_type == "1") { //AI
            $query = "SELECT a.assessment_id,b.assessment,a.parameter_id,pm.description as parameter,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as order_wt 
                FROM ai_subparameter_score as a 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=a.parameter_id AND ats.parameter_label_id=a.parameter_label_id AND ats.assessment_id=a.assessment_id AND ats.question_id=a.question_id  
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id  
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . " limit 0,5 ";
        } else { //AI and Manual
            $query = "SELECT main.assessment_id, main.assessment, main.parameter_id, main.parameter, IF(SUM(main.cnt) > 1,ifnull(FORMAT(avg(main.result),2),0),ifnull(FORMAT(SUM(main.result),2),0)) AS result,
            avg(main.order_wt) as order_wt 
                FROM (
                    (SELECT a.assessment_id, b.assessment, a.parameter_id, pm.description as parameter, ifnull(round(avg(a.accuracy),2),0) as result, avg(a.accuracy) as order_wt, 
                    IF(ifnull(round(avg(a.accuracy),2),0) > 0,1,0) as cnt  
                    FROM assessment_trainer_weights as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id  
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE du.user_id IS NOT NULL " . $cond . $grpby . ")  
                UNION ALL
                    (SELECT a.assessment_id,b.assessment,a.parameter_id,pm.description as parameter,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as order_wt,
                    IF(IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0,1,0) as cnt 
                    FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=a.parameter_id AND ats.parameter_label_id=a.parameter_label_id AND ats.assessment_id=a.assessment_id AND ats.question_id=a.question_id 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id   
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter' AND du.user_id IS NOT NULL " . $cond . $grpby . ") 
                ) as main " . $maingrpby;
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
            . " where ar.company_id =" . $Company_id . " AND ar.question_id !='' ";
        if ($start_date != '' && $end_date != '') {
            $query .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $query .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($user_id != '') {
            $query .= " AND ar.user_id =" . $user_id;
        }
        $query .= " group by ar.user_id, ar.assessment_id,ar.question_id ) AS a";
        //echo $query;
        $result = $this->db->query($query);
        return $result->row();
    }


    public function get_bottom_five_parameter($Company_id, $report_by, $top_five_para_id, $SDate = '', $EDate = '', $user_id = '', $report_type)
    {

        $cond = '';
        if ($SDate != '' && $EDate != '') {
            $cond .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
            $cond .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND a.user_id =" . $user_id;
        }
        if ($report_by == 1) {
            $cond .= " AND a.parameter_id NOT IN (" . $top_five_para_id . ") ";
        } else {
            $cond .= " AND a.assessment_id NOT IN (" . $top_five_para_id . ") ";
        }
        if ($report_by == 0) {
            $grpby = " group by a.assessment_id order by order_wt asc ";
            $maingrpby = " group by main.assessment_id order by main.order_wt asc limit 0,5 ";
        } else {
            $grpby = " group by a.parameter_id order by order_wt asc ";
            $maingrpby = " group by main.parameter_id order by main.order_wt asc limit 0,5 ";
        }
        if ($report_type == "2") {
            $query = "SELECT a.assessment_id,b.assessment,a.parameter_id,pm.description as parameter, ifnull(FORMAT(avg(a.accuracy),2),0) as result, avg(a.accuracy) as order_wt 
                FROM assessment_trainer_weights as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id 
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . " limit 0,5 ";
        } elseif ($report_type == "1") { //AI
            $query = "SELECT a.assessment_id,b.assessment,a.parameter_id,pm.description as parameter,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as order_wt 
                FROM ai_subparameter_score as a 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=a.parameter_id AND ats.parameter_label_id=a.parameter_label_id AND ats.assessment_id=a.assessment_id AND ats.question_id=a.question_id  
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id 
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter' AND du.user_id IS NOT NULL ";
            $query .= $cond . $grpby . " limit 0,5 ";
        } else { //AI and Manual
            $query = "SELECT main.assessment_id, main.assessment, main.parameter_id, main.parameter, IF(SUM(main.cnt) > 1,ifnull(FORMAT(avg(main.result),2),0),ifnull(FORMAT(SUM(main.result),2),0)) AS result,
            avg(main.order_wt) as order_wt 
                FROM (
                    (SELECT a.assessment_id, b.assessment, a.parameter_id, pm.description as parameter, ifnull(round(avg(a.accuracy),2),0) as result, avg(a.accuracy) as order_wt, 
                    IF(ifnull(round(avg(a.accuracy),2),0) > 0,1,0) as cnt  
                    FROM assessment_trainer_weights as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id  
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE du.user_id IS NOT NULL " . $cond . $grpby . ")  
                UNION ALL
                    (SELECT a.assessment_id,b.assessment,a.parameter_id,pm.description as parameter,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as order_wt,
                    IF(IF(ats.parameter_weight=0, round(IFNULL(sum(a.score)/count(*),0),2), round(IFNULL(sum(a.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0,1,0) as cnt 
                    FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=a.parameter_id AND ats.parameter_label_id=a.parameter_label_id AND ats.assessment_id=a.assessment_id AND ats.question_id=a.question_id 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id   
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter' AND du.user_id IS NOT NULL " . $cond . $grpby . ") 
                ) as main " . $maingrpby;
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_user($company_id)
    {
        $query = " SELECT distinct ar.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                    FROM assessment_final_results ar INNER JOIN device_users du ON du.user_id=ar.user_id
                    WHERE du.company_id = $company_id  ORDER BY traineename";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_parameter($company_id, $user_id = '')
    {
        $query = " SELECT distinct ar.parameter_id,pm.description AS parameter
                    FROM assessment_trainer_weights ar LEFT JOIN parameter_mst pm ON pm.id=ar.parameter_id
					LEFT JOIN assessment_mst am ON am.id=ar.assessment_id WHERE 1=1";
        if ($user_id != '') {
            $query .= " AND ar.user_id =" . $user_id;
        }
        $query .= " order by parameter ";
        $result = $this->db->query($query);
        return $result->result();
    }



    public function assessment_index_weekly_monthly($report_by, $StartDate = '', $EndDate = '', $user_id = '', $report_type)
    {
        $cond = "";
        $ResultArray = array();
        $PeriodArray = array();

        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND art.user_id =" . $user_id;
        }
        if ($report_type == "2") {
            $query = " SELECT FORMAT(avg(art.accuracy),2) AS result,am.assessment,art.assessment_id, DATE_FORMAT(am.start_dttm,'%d') wday
                        FROM assessment_trainer_weights as art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                        WHERE 1=1 $cond group by date(am.start_dttm) ";
        } elseif ($report_type == "1") {
            $query = "SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result, am.assessment, art.assessment_id, DATE_FORMAT(am.start_dttm,'%d') wday 
                FROM ai_subparameter_score art 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                WHERE art.parameter_type='parameter' $cond group by date(am.start_dttm) ";
        } else {
            $query = "  SELECT IF(SUM(main.cnt) > 1, FORMAT(avg(main.result),2),FORMAT(SUM(main.result),2)) as result, main.assessment, main.assessment_id, main.wday  
                    FROM (
                        SELECT FORMAT(avg(art.accuracy),2) AS result, IF(ifnull(FORMAT(avg(art.accuracy),2),0) > 0,1,0) as cnt, 
                        am.assessment,art.assessment_id, DATE_FORMAT(am.start_dttm,'%d') wday, date(am.start_dttm) as adate 
                        FROM assessment_trainer_weights as art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                        WHERE 1=1 $cond group by date(am.start_dttm) 
                    UNION ALL
                        SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result, am.assessment, art.assessment_id,  
                        IF(IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0,1,0) as cnt,
                        DATE_FORMAT(am.start_dttm,'%d') wday, date(am.start_dttm) as adate  
                        FROM ai_subparameter_score art 
                        LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                        WHERE art.parameter_type='parameter' $cond group by date(am.start_dttm)
                    ) as main group by main.adate";
        }

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array) $Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wday] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        return $ResultArray;
    }

    public function assessment_index_yearly($report_by, $StartDate = '', $EndDate = '', $user_id = '', $report_type)
    {
        $cond = '';
        $assmng = "";
        $ResultArray = array();
        $PeriodArray = array();

        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm) ";
            $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND art.user_id =" . $user_id;
        }
        if ($report_type == "2") {
            $query = " SELECT FORMAT(avg(art.accuracy),2) AS result, month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights as art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                    WHERE 1=1 $cond group by month(am.start_dttm) ";
        } elseif ($report_type == "1") {
            $query = "SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result, month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday 
                    FROM ai_subparameter_score art 
                    LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                    WHERE art.parameter_type='parameter' $cond group by month(am.start_dttm) ";
        } else {
            $query = "  SELECT IF(SUM(main.cnt) > 1, FORMAT(avg(main.result),2),FORMAT(SUM(main.result),2)) as result, main.wmonth, main.wday  
                    FROM (
                        SELECT FORMAT(avg(art.accuracy),2) AS result,IF(ifnull(FORMAT(avg(art.accuracy),2),0) > 0,1,0) as cnt,
                        month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday
                        FROM assessment_trainer_weights as art 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                        WHERE 1=1 $cond group by month(am.start_dttm) 
                    UNION ALL
                        SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result, 
                        IF(IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0,1,0) as cnt,
                        month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday 
                        FROM ai_subparameter_score art 
                        LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id  
                        WHERE art.parameter_type='parameter' $cond group by month(am.start_dttm)
                    ) as main group by main.wmonth";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array) $Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wmonth] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        return $ResultArray;
    }


    public function parameter_index_charts_new($parameter_id, $report_by, $StartDate = '', $EndDate = '', $user_id = '', $report_type)
    {
        $PeriodArray = array();
        if ($report_type == "2") {
            $query = "SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, p.id, ps.parameter_label_id, round(sum(ps.percentage)/count(*),2) as result FROM assessment_results_trans as ps 
            LEFT join parameter_mst as p on ps.parameter_id=p.id
            LEFT join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE 1=1 ";
            if ($StartDate != '' && $EndDate != '') {

                $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND ps.user_id =" . $user_id;
            }
            if ($parameter_id != '') {
                $query .= " AND ps.assessment_id=" . $parameter_id;
            }
            if ($parameter_id != '') {
                $query .= " AND ps.assessment_id=" . $parameter_id;
            }
            $query .= " GROUP BY ps.parameter_id, ps.parameter_label_id
                ORDER BY ps.parameter_id,ps.parameter_label_id;";
        } elseif ($report_type == "1") {
            /*$query="SELECT DISTINCT  ps.parameter_id as parameter_id, ps.parameter_label_id, p.description as parameter_name,round(sum(ps.score)/count(*),2) as result FROM ai_subparameter_score as ps 
            left join parameter_mst as p on ps.parameter_id = p.id
            left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE ps.parameter_type ='parameter'  AND 1=1";*/

            $query = "SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, ps.parameter_id as parameter_id, ps.parameter_label_id, round(sum(ps.score)/count(*),2) as result FROM ai_subparameter_score as ps 
            left join parameter_mst as p on ps.parameter_id = p.id
            left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE ps.parameter_type ='parameter'  AND 1=1
            ";
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $query .= " AND ps.user_id =" . $user_id;
            }
            if ($parameter_id != '') {
                $query .= " AND ps.assessment_id=" . $parameter_id;
            }
            $query .= " GROUP BY ps.parameter_id, ps.parameter_label_id
            ORDER BY ps.parameter_id,ps.parameter_label_id;";
        } else {
            $cond = "";
            if ($StartDate != '' && $EndDate != '') {

                $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
            if ($user_id != '') {
                $cond .= " AND ps.user_id =" . $user_id;
            }
            if ($parameter_id != '') {
                $cond .= " AND ps.assessment_id=" . $parameter_id;
            }
            $cond .= " GROUP BY p.id, pl.id ";
            $query = "SELECT main.parameter_name, main.parameter_id, main.parameter_label_id, ROUND(AVG(main.result),2) as result from
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

        if (count((array) $Accuracy) > 0) {
            $x = 0;
            foreach ($Accuracy as $value) {
                //$PeriodArray[$value->parameter_id] = array('parameter_name'=>$value->parameter_name,'result'=>$value->result);
                $PeriodArray[$x] = array('parameter_name' => $value->parameter_name, 'result' => $value->result);
                $x++;

            }
        }

        return $PeriodArray;
    }
    public function parameter_index_charts($parameter_id, $report_by, $StartDate = '', $EndDate = '', $user_id = '', $report_type)
    {
        $PeriodArray = array();
        $cond = '';
        $assmng = "";
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm) ";
            $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($user_id != '') {
            $cond .= " AND art.user_id =" . $user_id;
        }
        if ($parameter_id != '') {
            $cond .= " AND (art.parameter_label_id=" . $parameter_id . " OR art.parameter_id=" . $parameter_id . ") ";
        }

        if ($report_type == "2") {
            $query = " SELECT FORMAT(avg(art.accuracy),2) AS result,am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id " . $assmng . "
                    WHERE 1=1 " . $cond;
            $query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";

            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array) $Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
                }
            }
        } elseif ($report_type == "1") {
            $query = " SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,
                    am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art 
                    LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id " . $assmng . " 
                    WHERE parameter_type='parameter' AND 1=1 " . $cond;
            $query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";

            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array) $Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
                }
            }
        } else {
            $query = "SELECT IF(SUM(main.cnt) > 1, FORMAT(avg(main.result),2),FORMAT(SUM(main.result),2)) as result, main.assessment, main.assessment_id, main.wday 
            FROM  (
                SELECT FORMAT(avg(art.accuracy),2)  AS result,IF(ifnull(FORMAT(avg(art.accuracy),2),0) > 0,1,0) as cnt,
                am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id " . $assmng . " 
                    WHERE 1=1 $cond group by art.assessment_id 
                UNION ALL
                SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result,
                IF(IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) > 0,1,0) as cnt,
                    am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art 
                    LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id " . $assmng . " 
                    WHERE parameter_type='parameter' AND 1=1  $cond group by art.assessment_id
                ) as main ";
            $query .= " group by main.assessment_id order by main.assessment_id desc limit 0,10";

            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array) $Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
                }
            }
        }
        return $PeriodArray;
    }
    public function parameter_index_charts_old($parameter_id, $report_by, $StartDate = '', $EndDate = '', $user_id = '')
    {
        $PeriodArray = array();
        $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                   am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($user_id != '') {
            $query .= " AND art.user_id =" . $user_id;
        }
        if ($parameter_id != '') {
            $query .= " AND art.parameter_id=" . $parameter_id;
        }
        $query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array) $Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
            }
        }
        return $PeriodArray;
    }
}