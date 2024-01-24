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
        if ($report_type == "2") {
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM assessment_trainer_weights art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id "
                . " where am.company_id =" . $Company_id;
            // if ($start_date != '' && $end_date != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
        } elseif ($report_type == "1") {
            $query = "select IFNULL(count(distinct am.id),0) as total_assessment 
                  FROM ai_subparameter_score art
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id "
                . " where am.company_id =" . $Company_id;
            // if ($start_date != '' && $end_date != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
        } else {
            $cond = "";
            // if ($start_date != '' && $end_date != '') {
            //     $cond .= " AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }

            if ($start_date != '' && $end_date != '') {
                $cond .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $cond .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $cond .= " AND art.user_id =" . $user_id;
            }
            $query = "SELECT SUM(main.total_assessment) as total_assessment FROM (select IFNULL(count(distinct am.id),0) as total_assessment 
            FROM assessment_trainer_weights art
            LEFT JOIN assessment_mst am ON am.id=art.assessment_id
            where 1=1 $cond
          
            UNION ALL
                              
            select IFNULL(count(distinct am.id),0) as total_assessment 
            FROM ai_subparameter_score  art
            LEFT JOIN assessment_mst am ON am.id=art.assessment_id
            where 1=1 $cond) as main";
        }
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalASM = 0;
        if (count((array)$RowSet) > 0) {
            $TotalASM = $RowSet->total_assessment;
        }
        return $TotalASM;
    }

    public function get_assessment($user_id = '', $StartDate, $EndDate, $report_type)
    {
        if ($report_type == "2") {
            $query = "SELECT distinct ar.assessment_id,am.assessment 
                        FROM assessment_complete_rating ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        WHERE 1=1";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND ar.user_id  = '" . $user_id . "'";
            }
            $query .= " order by am.assessment asc ";
            $result = $this->db->query($query);
            return $result->result();
        } elseif ($report_type == "1" || $report_type == "3") {
            $query = "SELECT distinct ar.assessment_id,am.assessment 
                        FROM ai_subparameter_score ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        WHERE 1=1";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND am.start_dttm BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
        if ($report_type == "2") {
            $query = "SELECT count(DISTINCT(question_id)) as question_answer FROM `assessment_results`
             as ar left JOIN assessment_managers as am on 
             am.assessment_id=ar.assessment_id
             LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
           
              WHERE 1=1";
            // if ($start_date != '' && $end_date != '') {
            //     $query .= " AND amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $query .= " AND (date(amst.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR date(amst.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR '" . $start_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm)";
                $query .= " OR '" . $end_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND ar.user_id =" . $user_id;
            }
        } elseif ($report_type == "1") {
            $query = "SELECT count(DISTINCT(question_id)) as question_answer FROM `ai_schedule`
            as ar
            LEFT JOIN assessment_managers  am on 
            am.assessment_id=ar.assessment_id  
            LEFT JOIN assessment_mst amst ON amst.id=ar.assessment_id
           
             where 1=1";
            // if ($start_date != '' && $end_date != '') {
            //     $query .= " AND amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $query .= " AND (date(amst.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR date(amst.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR '" . $start_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm)";
                $query .= " OR '" . $end_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND ar.user_id =" . $user_id;
            }
        } else {
            $cond = "";
            // if ($start_date != '' && $end_date != '') {
            //     $cond .= " AND amst.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $cond .= " AND (date(amst.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .= " OR date(amst.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .= " OR '" . $start_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm)";
                $cond .= " OR '" . $end_date . "' BETWEEN date(amst.start_dttm) AND date(amst.end_dttm))";
            }
            if ($user_id != '') {
                $cond .= " AND ar.user_id =" . $user_id;
            }

            $query = " SELECT (main.question_answer) as question_answer FROM
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
    public function get_Trainee_data($Company_id, $manager_id)
    {
        $query = "SELECT distinct au.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
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
        // if ($start_date != '' && $end_date != '') {
        //     $query .= " AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        //     $query .= " OR am.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        // }
        if ($start_date != '' && $end_date != '') {
            $query .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $query .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        $query .= "	UNION ALL			
                                SELECT  au.user_id, concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                                        FROM assessment_allow_users au		
                                        LEFT JOIN assessment_mst am ON am.id = au.assessment_id
                                        INNER JOIN device_users du ON du.user_id=au.user_id 
                                        WHERE am.company_id = " . $Company_id;
        // if ($start_date != '' && $end_date != '') {
        //     $query .= " AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        //     $query .= " OR am.end_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        // }
        if ($start_date != '' && $end_date != '') {
            $query .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $query .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
        // if ($start_date != '' && $end_date != '') {
        //     $query .= " AND am.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        // }
        if ($start_date != '' && $end_date != '') {
            $query .= " AND (date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR date(am.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            $query .= " OR '" . $start_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $query .= " OR '" . $end_date . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
        if ($report_type == "2") {
            if ($report_by == 0) {
                $query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0) as avg_result FROM assessment_final_results as a 
			    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1";
            } else {
                $query = "SELECT ifnull(FORMAT(avg(a.accuracy),2),0)as avg_result FROM assessment_trainer_weights as a 
			    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1";
            }
            // if ($start_date != '' && $end_date != '') {
            //     $query .= " AND b.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $query .= " AND (date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR date(b.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR '" . $start_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $query .= " OR '" . $end_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
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
        } elseif ($report_type == "1") {

            $query = "SELECT ifnull(FORMAT(avg(a.score),2),0)as avg_result FROM ai_subparameter_score as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1 AND a.parameter_type='parameter'";

            // if ($start_date != '' && $end_date != '') {
            //     $query .= " AND b.start_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $query .= " AND (date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR date(b.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $query .= " OR '" . $start_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $query .= " OR '" . $end_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
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
        } else {
            $cond = "";
            // if ($start_date != '' && $end_date != '') {
            //     $cond .= " AND date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            if ($start_date != '' && $end_date != '') {
                $cond .= " AND (date(b.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .= " OR date(b.end_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
                $cond .= " OR '" . $start_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $cond .= " OR '" . $end_date . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
            }
            if ($user_id != '') {
                $cond .= " AND a.user_id =" . $user_id;
            }

            if ($report_by == 0) {
                $query = "SELECT FORMAT(SUM(main.avg_result)/count(main.avg_result),2) as avg_result FROM 
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
            ) as main;";
            } else {
                $query = "SELECT FORMAT(SUM(main.avg_result)/count(main.avg_result),2) as avg_result FROM 
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
                       AND 1=1 $cond group by a.parameter_id) as main;";
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
    public function get_top_five_parameter($Company_id, $report_by, $SDate = '', $EDate = '', $user_id, $report_type)
    {
        if ($report_type == "2") {
            if ($report_by == 0) {
                $query = "
                     SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy)
                     as order_wt,b.assessment FROM assessment_final_results as a 
                     LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                     LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                     LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            } else {
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
                  FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                     LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                    LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }
            // if ($SDate != '' && $EDate != '') {
            //     $query .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            // }
            if ($SDate != '' && $EDate != '') {
                $query .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $query .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND a.user_id =" . $user_id;
            }

            if ($report_by == 0) {
                $query .= " group by a.assessment_id order by order_wt desc limit 0,5 ";
            } else {
                $query .= " group by a.parameter_id order by order_wt desc limit 0,5 ";
            }
        }
        //AI
        elseif ($report_type == "1") {
            if ($report_by == 0) {
                $query = "SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                    LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    WHERE parameter_type='parameter' AND 1=1";
            } else {
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                    FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                    LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                    LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                    LEFT JOIN device_users du ON du.user_id=a.user_id 
                    where parameter_type='parameter'
                    AND 1=1";
            }
            // if ($SDate != '' && $EDate != '') {
            //     $query .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            // }






            // --------------------------------------------------------------------------------------------------------------------------





            if ($SDate != '' && $EDate != '') {
                $query .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $query .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND a.user_id =" . $user_id;
            }
            if ($report_by == 0) {
                $query .= " group by a.user_id, a.assessment_id order by order_wt desc limit 0,5 ";
            } else {
                $query .= " group by a.user_id, a.parameter_id order by order_wt desc limit 0,5 ";
            }
        }
        //AI and Manual
        else {
            $cond = "";
            // if ($SDate != '' && $EDate != '') {
            //     $cond .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            // }
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
                $query = "SELECT distinct main.assessment_id, main.result, main.order_wt, main.assessment  FROM 
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
                    ) as main group by main.assessment_id order by main.order_wt asc limit 0,5;";
            } else {
                $query = "SELECT main.assessment_id, main.parameter_id, main.result, main.order_wt, main.parameter  FROM 
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
                    ) as main group by main.assessment_id order by main.order_wt desc limit 0,5;";
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
            . " where ar.company_id =" . $Company_id . " AND ar.question_id !='' ";
        // if ($start_date != '' && $end_date != '') {
        //     $query .= " AND date(am.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        // }
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
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_top_five_parameter_old($Company_id, $report_by, $SDate = '', $EDate = '', $user_id = '')
    {
        if ($report_by == 0) {
            $query = "SELECT a.assessment_id,a.accuracy as result,b.assessment FROM assessment_final_results as a 
			LEFT JOIN assessment_mst as b ON b.id=a.assessment_id WHERE 1=1";
        } else {
            $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
			FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
			LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id WHERE 1=1";
        }
        // if ($SDate != '' && $EDate != '') {
        //     $query .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
        // }
        if ($SDate != '' && $EDate != '') {
            $query .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $query .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $query .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
            $query .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
        }
        if ($user_id != '') {
            $query .= " AND a.user_id =" . $user_id;
        }
        if ($report_by == 0) {
            $query .= " group by a.assessment_id order by result desc limit 0,5 ";
        } else {
            $query .= " group by a.parameter_id order by order_wt desc limit 0,5 ";
        }
        //echo $query;
        //exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_bottom_five_parameter($Company_id, $report_by, $top_five_para_id, $SDate = '', $EDate = '', $user_id = '', $report_type)
    {
        if ($report_type == "2") {
            if ($report_by == 0) {
                $query = "SELECT a.assessment_id,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy)
                 as order_wt,b.assessment FROM assessment_final_results as a 
                 LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                 LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                 LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            } else {
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.accuracy),2) as result,avg(a.accuracy) as order_wt,pm.description as parameter
              FROM assessment_trainer_weights as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                 LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                LEFT JOIN device_users du ON du.user_id=a.user_id WHERE 1=1";
            }
            // if ($SDate != '' && $EDate != '') {
            //     $query .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            // }
            if ($SDate != '' && $EDate != '') {
                $query .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $query .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
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
            } else {
                $query .= " group by a.parameter_id order by order_wt asc limit 0,5 ";
            }
        }
        //AI
        elseif ($report_type == "1") {
            if ($report_by == 0) {
                $query = "SELECT a.assessment_id,FORMAT(avg(a.score),2) as result, avg(a.score) as order_wt,b.assessment FROM ai_subparameter_score as a 
                LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=b.id        
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                WHERE parameter_type='parameter' AND 1=1";
            } else {
                $query = "SELECT a.assessment_id,a.parameter_id ,FORMAT(avg(a.score),2) as result,avg(a.score) as order_wt,pm.description as parameter
                FROM ai_subparameter_score as a LEFT JOIN assessment_mst as b ON b.id=a.assessment_id
                LEFT JOIN parameter_mst pm ON pm.id=a.parameter_id
                LEFT JOIN assessment_managers asm ON asm.assessment_id=pm.id
                LEFT JOIN device_users du ON du.user_id=a.user_id 
                where parameter_type='parameter'
                AND 1=1";
            }
            // if ($SDate != '' && $EDate != '') {
            //     $query .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            // }
            if ($SDate != '' && $EDate != '') {
                $query .= " AND (date(b.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR date(b.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
                $query .= " OR '" . $SDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm)";
                $query .= " OR '" . $EDate . "' BETWEEN date(b.start_dttm) AND date(b.end_dttm))";
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
            } else {
                $query .= " group by a.user_id, a.parameter_id order by order_wt asc limit 0,5 ";
            }
        }
        //AI and Manual
        else {
            $cond = "";
            // if ($SDate != '' && $EDate != '') {
            //     $cond .= " AND b.start_dttm BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            // }
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
                $cond .= " AND pm.id NOT IN (" . $top_five_para_id . ") ";
            } else {
                $cond .= " AND a.assessment_id NOT IN (" . $top_five_para_id . ") ";
            }

            if ($report_by == 0) {
                $query = "SELECT main.assessment_id, main.result, main.order_wt, main.assessment  FROM 
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
            } else {
                $query = "SELECT main.assessment_id, main.result, main.order_wt, main.parameter  FROM 
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
        if ($report_type == "2") {
            $ResultArray = array();
            $PeriodArray = array();
            $AssessArray = array();
            $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                        FROM assessment_final_results art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                        WHERE 1=1 ";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by date(am.start_dttm) ";
        } elseif ($report_type == "1") {
            $ResultArray = array();
            $PeriodArray = array();
            $AssessArray = array();
            $query = "SELECT FORMAT(avg(art.score),2)  AS result,	
            am.assessment, art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
             FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
             WHERE art.parameter_type='parameter' AND 1=1 ";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by date(am.start_dttm) ";
        } else {
            $ResultArray = array();
            $PeriodArray = array();
            $AssessArray = array();
            $cond = "";
            // if ($StartDate != '' && $EndDate != '') {
            //     $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $cond .= " AND art.user_id =" . $user_id;
            }
            $query = "SELECT FORMAT(SUM(main.result)/count(main.result),2) as result,
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

    public function assessment_index_yearly_old($report_by, $StartDate = '', $EndDate = '', $user_id = '', $report_type)
    {
        if ($report_type == "2") {
            $ResultArray = array();
            $PeriodArray = array();
            $AssessArray = array();
            $query = " SELECT FORMAT(avg(art.accuracy),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_final_results art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id           
                    WHERE 1=1 ";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }

            $query .= " group by month(am.start_dttm) ";
        } elseif ($report_type == "1") {
            $ResultArray = array();
            $PeriodArray = array();
            $AssessArray = array();
            $query = " SELECT FORMAT(avg(art.score),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE parameter_type='parameter' AND 1=1 ";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $query .= " AND art.user_id =" . $user_id;
            }
            $query .= " group by month(am.start_dttm) ";
        } else {
            $ResultArray = array();
            $PeriodArray = array();
            $AssessArray = array();
            $cond = "";
            // if ($StartDate != '' && $EndDate != '') {
            //     $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $cond .= " AND art.user_id =" . $user_id;
            }

            $query = "SELECT FORMAT(SUM(main.result)/count(main.result),2) as result, main.wmonth, main.wday  FROM 
            (SELECT FORMAT(avg(art.accuracy),2) AS result, month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_final_results art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE 1=1 $cond group by month(am.start_dttm)
              
              UNION ALL
                                  
              SELECT FORMAT(avg(art.score),2) AS result,month(am.start_dttm) as wmonth,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN assessment_managers as amg on amg.assessment_id=art.assessment_id
                    WHERE art.parameter_type='parameter' AND 1=1  $cond group by month(am.start_dttm)
            ) as main group by main.wmonth";
        }

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->wmonth] = $value->result;
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
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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

            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
            // if ($StartDate != '' && $EndDate != '') {
            //     $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $cond .= " AND ps.user_id =" . $user_id;
            }
            if ($parameter_id != '') {
                $cond .= " AND ps.assessment_id=" . $parameter_id;
            }
            $query = "SELECT main.* from(SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, ps.parameter_id as parameter_id, ps.parameter_label_id, round(sum(ps.score)/count(*),2) as result FROM ai_subparameter_score as ps 
            left join parameter_mst as p on ps.parameter_id = p.id
            left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE ps.parameter_type ='parameter'  AND 1=1 $cond
            UNION ALL
            SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, p.id, ps.parameter_label_id, round(sum(ps.percentage)/count(*),2) as result FROM assessment_results_trans as ps 
            LEFT join parameter_mst as p on ps.parameter_id=p.id
            LEFT join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
            LEFT JOIN assessment_mst am ON am.id=ps.assessment_id 
            WHERE 1=1 $cond) as main";
        }

        //$query .= " group by art.assessment_id order by art.assessment_id desc limit 0,10";
        $result = $this->db->query($query);
        $Accuracy = $result->result();

        if (count((array)$Accuracy) > 0) {
            $x = 0;
            foreach ($Accuracy as $value) {
                //$PeriodArray[$value->parameter_id] = array('parameter_name'=>$value->parameter_name,'result'=>$value->result);
                $PeriodArray[$x] = array('parameter_name' => $value->parameter_name, 'result' => $value->result);
                $x++;
            }
        }

        return $PeriodArray;
    }
    public function parameter_index_charts($parameter_id, $report_by, $report_type, $StartDate = '', $EndDate = '', $user_id = '')
    {
        if ($report_type == "2") {
            $PeriodArray = array();
            $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE 1=1 ";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
            if (count((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
                }
            }
            return $PeriodArray;
        } elseif ($report_type == "1") {
            $PeriodArray = array();
            $query = " SELECT FORMAT(avg(art.score),2)  AS result,	
                       am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM ai_subparameter_score art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE parameter_type='parameter' AND 1=1 ";
            // if ($StartDate != '' && $EndDate != '') {
            //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
            if (count((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
                }
            }
            return $PeriodArray;
        } else {
            $PeriodArray = array();
            $cond = '';
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
            // if ($StartDate != '' && $EndDate != '') {
            //     $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            // }
            if ($StartDate != '' && $EndDate != '') {
                $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
                $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
                $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
            }
            if ($user_id != '') {
                $cond  .= " AND art.user_id =" . $user_id;
            }
            if ($parameter_id != '') {
                $cond .= " AND art.parameter_id=" . $parameter_id;
            }
            $query .= " group by main.assessment_id order by main.assessment_id desc limit 0,10";

            $result = $this->db->query($query);
            $Accuracy = $result->result();
            if (count((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
                }
            }
            return $PeriodArray;
        }
    }
    public function parameter_index_charts_old($parameter_id, $report_by, $StartDate = '', $EndDate = '', $user_id = '')
    {
        $PeriodArray = array();
        $query = " SELECT FORMAT(avg(art.accuracy),2)  AS result,	
                   am.assessment,art.assessment_id,DATE_FORMAT(am.start_dttm,'%d') wday
                    FROM assessment_trainer_weights art LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    WHERE 1=1 ";
        // if ($StartDate != '' && $EndDate != '') {
        //     $query .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        // }
        if ($StartDate != '' && $EndDate != '') {
            $query .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $query .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $query .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
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
        if (count((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray[$value->assessment_id] = array('assessment_name' => $value->assessment, 'result' => $value->result);
            }
        }
        return $PeriodArray;
    }
}
