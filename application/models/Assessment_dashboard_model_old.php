<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function get_Total_Assessment($Company_id, $start_date = '', $end_date = '', $region_id = '', $store_id = '') {
        $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM assessment_trainer_weights art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id "
                . " where am.company_id =" . $Company_id;
        if ($start_date != '' && $end_date != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
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
    public function get_Total_Assessment_old($Company_id, $start_date = '', $end_date = '') {
        $query = "select count(am.id) as total_assessment "
                . " from assessment_mst am "
                . " where am.company_id =" . $Company_id;
        if ($start_date != '' && $end_date != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }

        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array)$RowSet) > 0) {
            $TotalASM = $RowSet->total_assessment;
        }
        return $TotalASM;
    }
   
    public function get_Candidate_Assessment($Company_id, $question_type, $report_by, $start_date = '', $end_date = '', $region_id = '', $store_id = '') {
        $query = " SELECT count(ar.user_id) as assessment_candidate FROM assessment_final_results ar
				LEFT JOIN assessment_mst am ON am.id = ar.assessment_id
				LEFT JOIN device_users du ON du.user_id=ar.user_id WHERE 1=1 ";
        if ($start_date != '' && $end_date != '') {
            $query .=" AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        
        return $RowSet->assessment_candidate;
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
//         WHERE ar.company_id = " . $Company_id . " AND art.question_id !='' ";
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
    public function get_Average_Accuracy($Company_id,$report_by, $start_date = '', $end_date = '', $region_id = '', $store_id = '') {
        if ($report_by == 0) {
			$query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0) as avg_result FROM assessment_final_results as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
		}else{
			$query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0)as avg_result FROM assessment_trainer_weights as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN device_users du ON du.user_id=a.user_id	WHERE 1=1";
		}
        if ($start_date != '' && $end_date != '') {
            $query .=" AND b.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        return $TotalAccuracy = $RowSet->avg_result;
    }
    public function get_top_five_parameter($Company_id, $report_by, $SDate = '', $EDate = '', $region_id = '', $store_id = '') {
        if ($report_by == 0) {
			$query = "SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,b.assessment FROM assessment_final_results as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
		}else{
			$query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
			FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
			LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
		}
        if ($SDate != '' && $EDate != '') {
            $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
		if ($report_by == 0) {
			$query .= " group by a.assessment_id order by order_wt desc limit 0,5 ";
		}else{
			$query .= " group by a.parameter_id order by order_wt desc limit 0,5 ";
		}
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_bottom_five_parameter($Company_id, $report_by, $top_five_para_id, $SDate = '', $EDate = '', $region_id = '', $store_id = '') {
       if ($report_by == 0) {
			$query = "SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,b.assessment FROM assessment_final_results as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
		}else{
			$query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
			FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
			LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
		}
        if ($SDate != '' && $EDate != '') {
            $query .=" AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
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

        $result = $this->db->query($query);
        return $result->result();
    }

    public function overall_result_parameter($isoverall, $passrange_from, $passrange_to, $failrange_from, $failrange_to, $SDate = '', $EDate = '', $region_id = '', $assessment_string = '', $step = 0, $store_id = '') {
        $lcwhere = "";
        if ($SDate != '' && $EDate != '') {
            $lcwhere .=" AND date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
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
        $query = "select a.region_id,sum(a.total_users) as total_users,sum(b.pass) as pass,sum(b.fail) as fail";
        if (!$isoverall) {
            $query .= ", ifnull(rg.region_name,'No Region') as region_name ";
        }
        $query .= " FROM (
            select du.region_id,art.parameter_id,count(atm.user_id) as total_users
                    FROM assessment_mst am
                    INNER JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                    INNER JOIN assessment_results_trans art ON art.assessment_id=atm.assessment_id AND art.user_id=atm.user_id
                    INNER JOIN device_users du ON du.user_id=atm.user_id
                    WHERE 1=1 $lcwhere GROUP BY art.parameter_id,du.region_id having total_users>0  ";
        $query .= " union all 
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
        $query .= "    ) as a GROUP BY a.parameter_id,a.region_id
            ) as b ON b.region_id= a.region_id AND b.parameter_id=a.parameter_id";
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

    public function overall_result_assessment($isoverall, $passrange_from, $passrange_to, $failrange_from, $failrange_to, $SDate = '', $EDate = '', $region_id = '', $assessment_string = '', $step = 0, $store_id = '') {

        $lcwhere = "";
        if ($SDate != '' && $EDate != '') {
            $lcwhere .=" AND date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
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

    public function get_trainee_region($Company_id, $region_id = '', $report_by = '', $SDate = '', $EDate = '', $assessment_id = '', $store_id = '') {
        $query = "SELECT distinct du.region_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_attempts ar 
                        LEFT JOIN assessment_mst am  ON am.id=ar.assessment_id
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN region rg ON rg.id=du.region_id
                        WHERE 1=1";
        if ($SDate != '' && $EDate != '') {
            $query .=" AND am.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
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
            $query .=" AND am.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
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
	public function get_region_average($Company_id, $report_by, $region_id = '', $SDate = '', $EDate = '', $assessment_id = '', $store_id = '') {
        $query = " SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result,du.region_id, ";
			if($report_by==1 || $report_by==3){
				$query .= "art.parameter_id as id FROM assessment_trainer_weights art  ";
			}else if($report_by==2 || $report_by==4){
				$query .= " am.id FROM assessment_final_results art";
			}
			$query .= " LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id 
			WHERE 1=1 ";
        if ($SDate != '' && $EDate != '') {
            $query .=" AND am.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
        }
        if ($assessment_id != '') {
            $query .= " AND am.id in(" . $assessment_id . ") ";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in(" . $region_id . ") ";
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        if ($report_by == 1) {
            $query .= " group by art.parameter_id ";
        }elseif ($report_by == 2) {
            $query .= " group by art.assessment_id ";
        }elseif ($report_by == 3 || $report_by == 4) {
            $query .= " group by du.region_id ";
        }
        $query .= " order by region_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_region_result($Company_id, $report_by, $region_id = '', $SDate = '', $EDate = '', $assessment_id = '', $store_id = '') {
        $query = "
            select a.*,ctr.range_color from (
            SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,
            IFNULL(avg(art.accuracy),0) AS tresult,art.user_id,du.region_id,art.assessment_id,";
			if($report_by==1){
				$query .= "pm.description as name,pm.id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_trainer_weights art LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id ";
			}else{
				$query .= "am.assessment as name,art.assessment_id as para_assess_id,ifnull(rg.region_name,'No Region') as region_name FROM assessment_final_results art";
			}
			$query .= " LEFT JOIN assessment_mst am ON am.id=art.assessment_id LEFT JOIN device_users du ON du.user_id=art.user_id 
				LEFT JOIN region rg ON rg.id=du.region_id  WHERE 1=1 ";
        if ($SDate != '' && $EDate != '') {
            $query .=" AND am.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
        }
        if ($assessment_id != '') {
            $query .= " AND am.id in(" . $assessment_id . ") ";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in(" . $region_id . ") ";
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        if ($report_by == 1) {
            $query .= " group by pm.id,du.region_id ";
        } else {
            $query .= " group by art.assessment_id,du.region_id ";
        }
        //$query  .= " , du.region_id ";
        $query .= " order by region_id) as a "
                . "LEFT JOIN company_threshold_range ctr ON  a.result between ctr.range_from and ctr.range_to  ";
		//echo $query;
		//exit;
        $result = $this->db->query($query);
        return $result->result();
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

    public function get_parameter_assessment_score($Company_id, $parassess_id = '', $region_id = '', $StartDate = '', $EndDate = '', $store_id = '') {
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
            $query .=" AND DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        $query .= " group by art.assessment_id ";

        $query .= " order by art.assessment_id ";
            
        $result = $this->db->query($query);
        return $result->result();
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

    public function get_assessment($Company_id, $report_by = '', $StartDate, $EndDate) {
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
        $query = " SELECT distinct ar.parameter_id,pm.description AS parameter
                    FROM assessment_trainer_weights ar LEFT JOIN parameter_mst pm ON pm.id=ar.parameter_id
					LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
					INNER JOIN device_users du ON du.user_id=ar.user_id	WHERE 1=1 ";
                if($assessment_id!=''){
                    $query .= " AND ar.assessment_id=".$assessment_id;
                }
                if($region_id!=''){
                    $query .= " AND du.region_id=".$region_id;
                }
			$query .= " GROUP BY ar.parameter_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    
    public function get_user_average($Company_id, $report_by, $region_id = '', $assessment_id = '') {
        if ($report_by == 1) {
			$query = " SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result,du.user_id,art.parameter_id as id 
                   FROM assessment_trainer_weights art 
                   LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                   LEFT JOIN device_users du ON du.user_id=art.user_id 
		   WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id";
            $query .= " group by art.parameter_id ";
        }elseif ($report_by == 3) {
			$query = " SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result,du.user_id
                   FROM assessment_final_results art 
                   LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                   LEFT JOIN device_users du ON du.user_id=art.user_id 
		   WHERE art.assessment_id=$assessment_id AND du.region_id=$region_id";
            $query .= " group by du.user_id  ";
        }
        $result = $this->db->query($query);
        return $result->result();
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
    public function get_pararegion_average($Company_id, $report_by, $assessment_id = '') {
        $query = " SELECT FORMAT(IFNULL(avg(art.accuracy),0),2) AS result,du.region_id,art.parameter_id as id 
                   FROM assessment_trainer_weights art 
                   LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                   LEFT JOIN device_users du ON du.user_id=art.user_id 
		   WHERE art.assessment_id=$assessment_id ";

        if ($report_by == 1) {
            $query .= " group by art.parameter_id ";
        }elseif ($report_by == 3) {
            $query .= " group by du.region_id  ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameter_assessment_result($company_id, $assessment_id) {
        $query = " SELECT a.*,ctr.range_color
                    FROM (
                        SELECT art.assessment_id as para_assess_id,du.region_id,IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,
                        ifnull(rg.region_name,'No Region') as region_name,pm.id as parameter_id,pm.description AS parameter
                        FROM assessment_trainer_weights art 
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id	
                        INNER JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id 
                        WHERE art.assessment_id= $assessment_id 
                        group by pm.id,du.region_id 
                        order by region_id
                        ) as a 
                        LEFT JOIN company_threshold_range ctr 
                        ON a.result between ctr.range_from and ctr.range_to ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_assessment_user($assessment_id = '', $StartDate = '', $EndDate = '') {
        $query = "(SELECT aa.user_id,aa.is_completed,aa.assessment_id
                        FROM assessment_attempts aa LEFT JOIN assessment_mst am ON am.id = aa.assessment_id ";
        if ($assessment_id != '') {
            $query .= " WHERE aa.assessment_id IN(" . $assessment_id . ") AND aa.user_id NOT IN(select user_id FROM assessment_allow_users where aa.assessment_id=assessment_id)";
        } else {
            $query .= " WHERE aa.user_id NOT IN(select user_id FROM assessment_allow_users where aa.assessment_id=assessment_id)";
        }
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        $query .= " UNION ALL
            
                      SELECT au.user_id,0 AS is_completed,au.assessment_id
                      FROM assessment_allow_users  au 
                      LEFT JOIN assessment_mst am ON am.id = au.assessment_id ";
        if ($assessment_id != '') {
            $query .= " WHERE au.assessment_id IN(" . $assessment_id . ")";
            if ($StartDate != '' && $EndDate != '') {
                $query .=" AND DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
        } else {
            if ($StartDate != '' && $EndDate != '') {
                $query .=" WHERE DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            }
        }

        $query .=" ) as a ";

        $user_count = " SELECT count(a.user_id) as user_count FROM " . $query;

        $result_cnt = $this->db->query($user_count);
        $data['user_count'] = $result_cnt->row();

        $ass_status = " SELECT a.user_id, IF(a.is_completed=1,'complete','incomplete') as assess_status
                           from " . $query;

        $result_status = $this->db->query($ass_status);

        $data['ass_status'] = $result_status->result();

        return $data;
    }

    public function region_level_result($region_id, $StartDate = '', $EndDate = '', $pass_range_from, $pass_range_to, $fail_range_from, $fail_range_to) {
        $query = " select a.assessment_id,amm.assessment,a.total_users,IFNULL(b.pass,0) as pass,IFNULL(b.fail,0) as fail 
                        FROM (
                                SELECT du.region_id,am.id as assessment_id,count(atm.user_id) as total_users
                                    FROM assessment_mst am
                                    LEFT JOIN assessment_allow_users as atm ON atm.assessment_id=am.id
                                    INNER JOIN device_users du ON du.user_id=atm.user_id
                                    WHERE 1=1 AND du.region_id IN($region_id)";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        $query .="having total_users>0 ";
        $query .=" UNION ALL 
										
				SELECT du.region_id,am.id,count(distinct atm.user_id)
                                    FROM assessment_mst am
                                    LEFT JOIN assessment_attempts as atm ON atm.assessment_id=am.id
                                    INNER JOIN device_users du ON du.user_id=atm.user_id
                                    WHERE 1=1 AND atm.is_completed =1 AND du.region_id IN($region_id) AND
                                    atm.user_id not in (select user_id FROM assessment_allow_users where assessment_id=am.id )";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
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
					WHERE du.region_id IN($region_id)";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND DATE(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        $query .="  GROUP BY art.user_id,art.assessment_id,du.region_id) as a 
                                    GROUP BY a.assessment_id,a.region_id
                            ) as b 
                ON  b.assessment_id=a.assessment_id
                LEFT JOIN assessment_mst amm ON amm.id=a.assessment_id ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function assessment_index_weekly_monthly($Company_id, $report_by,$StartDate = '', $EndDate = '', $parameter_id = '', $region_id = '', $store_id = '') {
        $ResultArray = array();$PeriodArray = array();$AssessArray = array();
        $query = " SELECT IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) AS result,	
                   am.assessment,pm.description as parameter,pm.id,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE 1=1 ";
//                    WHERE ar.company_id = " . $Company_id . " AND art.question_id !='' ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        //----parameter level--//
            $query_para = $query;
            if($parameter_id !=''){
                $query_para .=" AND art.parameter_id=".$parameter_id;
            }
            $query_para .= " group by art.assessment_id";
        //----End------ //
     
        $query .= " group by date(am.start_dttm) ";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wday] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        //---- parameter level ----//
            $result_para = $this->db->query($query_para);
            $Accuracy_para = $result_para->result();
            if (count((array)$Accuracy_para) > 0) {
                foreach ($Accuracy_para as $value1) {
                    $AssessArray[$value1->assessment_id] = $value1->result;
                }
            }
            $ResultArray['assess'] = $AssessArray;
        //----End------//
        return $ResultArray;
    }
    public function assessment_index_yearly($Company_id, $report_by,$StartDate = '', $EndDate = '', $parameter_id='', $region_id = '', $store_id = '') {
        $ResultArray = array();$PeriodArray = array();$AssessArray = array();
        $query = " SELECT IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) AS result,	
                    am.assessment,pm.description as parameter ,
                    pm.id,art.assessment_id,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art 				 	   
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                    INNER JOIN device_users du ON du.user_id=art.user_id
                    WHERE 1=1 ";
//                    WHERE ar.company_id = " . $Company_id . " AND art.question_id !='' ";
        if ($StartDate != '' && $EndDate != '') {
            $query .=" AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id =" . $region_id;
        }
        if ($store_id != '') {
            $query .= " AND du.store_id =" . $store_id;
        }
        //----parameter level--//
            $query_para = $query;
            if($parameter_id !=''){
                $query_para .=" AND art.parameter_id=".$parameter_id;
            }
            $query_para .= " group by art.assessment_id ";
        //----End------ // 
        $query .= " group by month(am.start_dttm) ";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wmonth] = $value->result;
            }
        }
        $ResultArray['period'] = $PeriodArray;
        //---- parameter level ----//
            $result_para = $this->db->query($query_para);
            $Accuracy_para = $result_para->result();
            if (count((array)$Accuracy_para) > 0) {
                foreach ($Accuracy_para as $value1) {
                    $AssessArray[$value1->assessment_id] = $value1->result;
                }
            }
            $ResultArray['assess'] = $AssessArray;
        //----End------//
        return $ResultArray ;
    }

}
