<?php

// use PhpOffice\PhpSpreadsheet\Reader\IReader;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Competency_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //Get All Assessment 
    public function get_all_assessment()
    {
        $query = "SELECT distinct am.id as assessment_id,am.report_type as report_type,
                  CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, 
                  if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
                  FROM assessment_mst am 
                  LEFT JOIN assessment_report_type as art on art.id=am.report_type
				  WHERE am.status = 1
                  GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
    // end here

    // Competency_understanding_graph get score
    public function LastExpiredAssessment($StartStrDt, $EndDtdate)
    {

        $result = "SELECT id, assessment,report_type FROM assessment_mst am 
                   WHERE end_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "' ORDER BY end_dttm DESC";
        $query = $this->db->query($result);
        $row = $query->result_array();
        return $row;
    }

    public function getCompetencyscore($Assessment_id)
    {
        $query = "SELECT assessment_id,users,u_name,IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as overall_score 
                  FROM (
                  (SELECT assessment_id,ifnull(overall_score,0)as overall_score,users,u_name,
                  IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt
                  FROM (
                  SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                  SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                  ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id,
                  am.assessment
                  FROM  assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                  LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  LEFT JOIN assessment_attempts as aa ON aa.user_id = amu.user_id AND aa.assessment_id=amu.assessment_id
                  WHERE ps.parameter_type='parameter' AND aa.is_completed =1 and amu.assessment_id in(" . implode(',', $Assessment_id) . ") 
                  GROUP BY am.id,amu.user_id
                  ) as main GROUP BY main.assessment_id,main.users ORDER BY main.users)
                  UNION ALL
                  (SELECT assessment_id,ifnull(overall_score,0)as overall_score,users,u_name,
                  IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt
                  FROM (
                  SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                  SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                  ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
                  am.assessment
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                  LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  LEFT JOIN assessment_attempts as aa ON aa.user_id = amu.user_id AND aa.assessment_id=amu.assessment_id
                  WHERE 1=1 AND aa.is_completed =1 AND amu.assessment_id in (" . implode(',', $Assessment_id) . ") GROUP BY am.id,amu.user_id
                  ) as main GROUP BY main.assessment_id,main.users ORDER BY main.users)
                ) as m2 GROUP BY m2.assessment_id, m2.users";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    //end here

    // Performance comparison by module
    public function LastExpiredFiveAssessment($StartStrDt, $EndDtdate)
    {
        $result = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'
                    ORDER BY end_dttm DESC";
        $query = $this->db->query($result);
        $row = $query->result_array();
        return $row;
    }

    public function performance_comparison_avg($assessment_id)
    {
        $query = "SELECT main.para_assess_id as assessment_id,main.assessment,
                  IF(SUM(main.cnt) > 1, IFNULL(FORMAT(AVG(main.result),2),'0'),IFNULL(FORMAT(SUM(main.result),2),'0')) AS score 
                  FROM (
                  (SELECT round(IFNULL(avg(a.result),0),2) AS result,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt,
                  a.user_id,a.para_assess_id ,a.assessment
                  FROM 
                  (SELECT IFNULL(FORMAT(avg(art.accuracy),2),0) AS result,art.user_id,art.assessment_id as para_assess_id,
                  am.assessment
                  FROM assessment_trainer_weights art 
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                  LEFT JOIN device_users du ON du.user_id=art.user_id 
                  WHERE 1=1  AND am.id IN ('" . implode("', '", $assessment_id) . "') 
                  ) as a group by a.para_assess_id,a.user_id order by a.user_id)
                  UNION ALL
                  (SELECT round(IFNULL(AVG(a.result),0),2) AS result,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, a.user_id,
                  a.para_assess_id,a.assessment
                  FROM 
                  (SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), 
                  round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result, 
                  art.user_id,art.assessment_id as para_assess_id,am.assessment 
                  FROM ai_subparameter_score art 
                  LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id 
                  AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id 
                  AND ats.question_id=art.question_id 
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                  LEFT JOIN device_users du ON du.user_id=art.user_id 
                  LEFT JOIN region rg ON rg.id=du.region_id  
                  WHERE parameter_type='parameter' AND du.user_id IS NOT NULL  AND am.id IN ('" . implode("', '", $assessment_id) . "')  
                  group by art.parameter_id,art.parameter_label_id,art.assessment_id,art.user_id
                  ) as a group by a.para_assess_id,a.user_id order by a.user_id)
                  ) as main  
                 group by main.para_assess_id ORDER BY main.para_assess_id";
                 
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function performance_comparison_avg_old($Assessment_id)
    {
        $query = "SELECT assessment_id,assessment,round(ifnull(avg(final_score),'0'),2)as score From (
                  SELECT assessment_id,assessment,users,u_name,
                  IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score  FROM (
                  (SELECT assessment_id,assessment,users,u_name,main.overall_score,
                  IF(FORMAT(IFNULL(AVG(main.overall_score),0),2) > 0,1,0) AS cnt FROM (
                  SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                  SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                  ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name,
                   am.id as assessment_id,am.assessment
                  FROM  assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                  LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  WHERE ps.parameter_type='parameter' AND am.id  IN (" . implode(',', $Assessment_id) . ") 
                  GROUP BY am.id,amu.user_id
                  ) as main GROUP BY main.assessment_id,main.users)
                  UNION ALL
                  (SELECT assessment_id,assessment,users,u_name, main1.overall_score,
                  IF(FORMAT(IFNULL(AVG(main1.overall_score),0),2) > 0,1,0) AS cnt FROM (
                  SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id),
                  SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                  ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, 
                  am.id as assessment_id ,am.assessment
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                  LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  WHERE am.id  IN (" . implode(',', $Assessment_id) . ")  GROUP BY amu.assessment_id,amu.user_id ORDER BY users ASC
                  ) as main1 GROUP by main1.assessment_id,main1.users)
                  ) as final GROUP BY final.assessment_id,final.users
                  ) as main3 GROUP BY main3.assessment_id";


        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    //end here

    //Performance comparison by Division
    public function getdepartment($Assessment_id)
    {
        $query = "SELECT DISTINCT du.department  FROM `device_users` as du 
                  LEFT JOIN assessment_mapping_user as amu ON amu.user_id=du.user_id 
                  WHERE du.department!= '' ";
        if ($Assessment_id != '0') {
            $query .= " and amu.assessment_id='" . $Assessment_id . "' ";
        }
        $query .= "  ORDER by du.department asc";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // competency_by_division
    public function get_department($assessmentid, $manager_id)
    {
        $query = "SELECT DISTINCT du.department  FROM `device_users` as du 
        LEFT JOIN assessment_mapping_user as amu ON amu.user_id=du.user_id 
        WHERE du.department!= '' ";
        if ($assessmentid != '0') {
            $query .= " and amu.assessment_id IN (" . implode(',', $assessmentid) . ") ";
        }
        if ($manager_id != 0) {
            $query .= " and amu.trainer_id IN (" . implode(',', $manager_id) . ") ";
        }
        $query .= "  ORDER by du.department asc";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function get_time_based_div($start_date, $end_date)
    {
        $query = "SELECT DISTINCT du.department  FROM `device_users` as du 
        LEFT JOIN assessment_mapping_user as amu ON amu.user_id=du.user_id 
        left join assessment_mst as am on am.id = amu.assessment_id
        WHERE du.department != '' ";
        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $end_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        }
        $query .= "  ORDER by du.department asc";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    // public function get_region($assessment_id, $Company_id)
    //  {
    //      $query = "SELECT du.region_id as region_id, rg.region_name as region_name
    //                FROM assessment_mst am
    //                LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
    //                LEFT JOIN device_users du ON du.user_id=amu.user_id
    //                LEFT JOIN region rg ON du.region_id=rg.id
    //                WHERE 1=1";
    //                if ($assessment_id != '0') {
    //                     $query .= " AND am.id='" . $assessment_id . "'";
    //                 }
    //                 $query .=" AND am.company_id='" . $Company_id . "'  AND du.region_id !='0'
    //                GROUP BY du.region_id ORDER BY du.region_id asc";
    //      $result = $this->db->query($query);
    //      return $result->result();
    //  }
    public function assessment_wise_divsion($start_date, $end_date, $assessment_id)
    {
        $query = "SELECT DISTINCT(amu.user_id), du.department as department_name
        FROM `assessment_mapping_user` as amu 
        LEFT JOIN device_users as du on du.user_id = amu.user_id 
        LEFT JOIN assessment_mst as am on am.id = amu.assessment_id
        WHERE 1=1 and amu.assessment_id IN (" . implode(',', $assessment_id) . ") ";
        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $end_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        }
        $query .= " AND du.department !='' GROUP BY du.department ORDER by du.department ASC";

        $result = $this->db->query($query);
        return $result->result();
    }
    // By Bhautik Rana 11-01-2023 Score accurate 
    public function Get_score_divison_wise($Assessment_id, $Report_Type, $DvisonId_Set, $Company_id)
    {
        if ($Report_Type == 1) {
            $query = "SELECT assessment_id,assessment,department, round(ifnull(avg(final_score),'0'),2)as score From (
                SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score,final.*  FROM (
                (SELECT assessment_id,assessment,department,users,u_name,main.overall_score,IF(FORMAT(IFNULL(AVG(main.overall_score),0),2) > 0,1,0) AS cnt FROM (
                SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                                ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id,am.assessment,du.department
                
                FROM  assessment_mst as am 
                LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                LEFT JOIN device_users as du ON du.user_id = amu.user_id
                WHERE ps.parameter_type='parameter'  ";
            if ($Assessment_id != '') {
                $query .= " AND am.id ='" . $Assessment_id . "' ";
            }
            if ($DvisonId_Set != '') {
                $query .= "  AND du.department IN ('" . implode("', '", $DvisonId_Set) . "') ";
            }
            $query .= "  GROUP BY am.id,amu.user_id,du.department
                ) as main GROUP BY main.assessment_id,main.users,main.department)
        
                ) as final GROUP BY final.assessment_id,final.users,final.department
                ) as main3 GROUP BY main3.assessment_id,main3.department";
        } else if ($Report_Type == 2) {
            $query = "SELECT assessment_id,assessment,department, round(ifnull(avg(final_score),'0'),2)as score From (
                SELECT assessment_id,assessment,department,users,u_name, main1.overall_score as final_score,IF(FORMAT(IFNULL(AVG(main1.overall_score),0),2) > 0,1,0) AS cnt FROM (
                    SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                                                ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,am.assessment,du.department
                                                FROM assessment_mst as am 
                                                LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                                                LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                                                LEFT JOIN device_users as du ON du.user_id = amu.user_id
                                                WHERE  1=1 ";
            if ($Assessment_id != '') {
                $query .= " AND am.id ='" . $Assessment_id . "' ";
            }
            if ($DvisonId_Set != '') {
                $query .= " and du.department IN ('" . implode("', '", $DvisonId_Set) . "') ";
            }
            $query .= "  GROUP BY amu.assessment_id,amu.user_id,du.department ORDER BY users ASC) as main1 GROUP by main1.assessment_id,main1.users,main1.department
                    ) as final GROUP BY final.assessment_id,final.department";
        } else {
            $query = "SELECT assessment_id,assessment,department, round(ifnull(avg(final_score),'0'),2)as score From (
                SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score,final.*  FROM (
                    
                (SELECT assessment_id,assessment,department,users,u_name,main.overall_score,IF(FORMAT(IFNULL(AVG(main.overall_score),0),2) > 0,1,0) AS cnt FROM (
                SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                                ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id,am.assessment,du.department
                
                FROM  assessment_mst as am 
                LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                LEFT JOIN device_users as du ON du.user_id = amu.user_id
                WHERE ps.parameter_type='parameter' ";
            if ($Assessment_id != '') {
                $query .= " AND am.id ='" . $Assessment_id . "'";
            }
            if ($DvisonId_Set != '') {
                $query .= " and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  ";
            }
            $query .= " GROUP BY am.id,amu.user_id,du.department
                    ) as main GROUP BY main.assessment_id,main.users,main.department)
                    UNION ALL
                    (SELECT assessment_id,assessment,department,users,u_name, main1.overall_score,IF(FORMAT(IFNULL(AVG(main1.overall_score),0),2) > 0,1,0) AS cnt FROM (
                SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                                ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,am.assessment,du.department
                                FROM assessment_mst as am 
                                LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                                LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                                LEFT JOIN device_users as du ON du.user_id = amu.user_id
                                WHERE  1=1 ";
            if ($Assessment_id != '') {
                $query .= " AND am.id ='" . $Assessment_id . "'";
            }
            if ($DvisonId_Set != '') {
                $query .= " and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  ";
            }
            $query .= " GROUP BY amu.assessment_id,amu.user_id,du.department ORDER BY users ASC
                    ) as main1 GROUP by main1.assessment_id,main1.users,main1.department)
                    ) as final GROUP BY final.assessment_id,final.users,final.department
                    ) as main3 GROUP BY main3.assessment_id,main3.department";
        }
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // By Bhautik Rana 11-01-2023 Score accurate

    // 12-01-2023 Competency Graph Score accurate
    public function get_assessment($start_date, $end_date)
    {
        $query = "SELECT DISTINCT(am.id), am.assessment, am.report_type FROM `assessment_mst` as am 
                  LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id 
                  WHERE 1=1 and am.end_dttm between '" . $start_date . "' AND '" . $end_date . "' ORDER BY am.end_dttm DESC";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }


     public function get_division_data($start_date, $end_date, $Assessment_id, $DvisonId_Set, $reg_id_set, $manager_id_set, $Company_id)
    {
        $cond = ''; $assmng ='';
        if ($Assessment_id != '') {
            $cond .= " AND am.id IN ('" . implode("', '", $Assessment_id) . "') ";
        }
        if ($DvisonId_Set != '') {
            $cond .= " and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  ";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $cond .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $cond .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
            }
        }
        if ($manager_id_set != '') {
            $cond .= " and amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "')";
            $assmng =" LEFT JOIN assessment_managers amu ON amu.assessment_id=art.assessment_id ";
        }
        $query = "SELECT main.department,IF(SUM(main.cnt) > 1, IFNULL(FORMAT(AVG(main.result),2),'0'),IFNULL(FORMAT(SUM(main.result),2),'0')) AS score 

        FROM (
        (SELECT round(IFNULL(avg(c.result),0),2) AS result,IF(IFNULL(avg(c.result),0) > 0,1,0) AS cnt,c.department 
                FROM
                (SELECT round(IFNULL(avg(b.result),0),2) AS result,b.user_id,b.region_id,b.department 
                FROM(SELECT round(IFNULL(avg(a.result),0),2) AS result,a.user_id,a.region_id,a.department 
                FROM 
                    ( SELECT IFNULL(FORMAT(avg(art.accuracy),2),0) AS result,art.user_id,du.region_id,art.assessment_id as para_assess_id,du.department 
                    FROM assessment_trainer_weights art 
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN region rg ON rg.id=du.region_id ".$assmng." 
                    WHERE 1=1 ".$cond; 
                $query .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id,du.department order by du.region_id
                    ) as a group by a.para_assess_id,a.region_id,a.department order by a.region_id 
                    ) AS b group by b.region_id,b.department order by b.department
                    ) AS c group by c.department order by c.department
                ) 
                UNION ALL 
                (SELECT round(IFNULL(AVG(c.result),0),2) AS result,IF(IFNULL(avg(c.result),0) > 0,1,0) AS cnt,c.department 
                FROM 
                        (SELECT round(IFNULL(AVG(b.result),0),2) AS result, b.user_id,b.region_id,b.department 
                FROM 
                        (SELECT round(IFNULL(AVG(a.result),0),2) AS result, a.user_id,a.region_id,a.department 
                FROM 
                        ( SELECT IF(ats.parameter_weight=0, round(IFNULL(sum(art.score)/count(*),0),2), round(IFNULL(sum(art.score*(ats.parameter_weight))/SUM(ats.parameter_weight),0),2)) as result, art.user_id,du.region_id,art.assessment_id as para_assess_id,du.department 
                        FROM ai_subparameter_score art 
                        LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=art.parameter_id AND ats.parameter_label_id=art.parameter_label_id AND ats.assessment_id=art.assessment_id AND ats.question_id=art.question_id 
                        LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                        LEFT JOIN device_users du ON du.user_id=art.user_id 
                        LEFT JOIN region rg ON rg.id=du.region_id ".$assmng." 
                        WHERE parameter_type='parameter' AND du.user_id IS NOT NULL ".$cond; 
                        
                $query .= " group by art.parameter_id,art.parameter_label_id,art.assessment_id,du.region_id,du.department 
                        ) as a group by a.para_assess_id,a.region_id,a.department order by a.region_id 
                        ) AS b group by b.region_id,b.department order by b.department 
                        ) AS c group by c.department order by c.department 
                    )
                        
        ) as main  
        group by main.department order by main.department";
        // echo $query;exit;
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function get_division_data_old($start_date, $end_date, $Assessment_id, $DvisonId_Set, $reg_id_set, $manager_id_set, $Company_id)
    {
        $query = "SELECT assessment_id,assessment,department, round(ifnull(avg(final_score),'0'),2)as score From (
            SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score,final.*  FROM (
                
            (SELECT assessment_id,assessment,department,users,u_name,main.overall_score,IF(FORMAT(IFNULL(AVG(main.overall_score),0),2) > 0,1,0) AS cnt FROM (
            SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                            ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id,am.assessment,du.department
            
            FROM  assessment_mst as am 
            LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
            LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
            LEFT JOIN device_users as du ON du.user_id = amu.user_id
            WHERE ps.parameter_type='parameter' ";
        if ($Assessment_id != '') {
            $query .= " AND am.id IN ('" . implode("', '", $Assessment_id) . "') ";
        }
        if ($DvisonId_Set != '') {
            $query .= " and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  ";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
            }
        }
        if ($manager_id_set != '') {
            $query .= " and amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "')";
        }
        $query .= " GROUP BY am.id,amu.user_id,du.department
                ) as main GROUP BY main.assessment_id,main.users,main.department)
                UNION ALL
                (SELECT assessment_id,assessment,department,users,u_name, main1.overall_score,IF(FORMAT(IFNULL(AVG(main1.overall_score),0),2) > 0,1,0) AS cnt FROM (
            SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                            ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,am.assessment,du.department
                            FROM assessment_mst as am 
                            LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                            LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                            LEFT JOIN device_users as du ON du.user_id = amu.user_id
                            WHERE  1=1 ";
        if ($Assessment_id != '') {
            $query .= " AND am.id IN ('" . implode("', '", $Assessment_id) . "') ";
        }
        if ($DvisonId_Set != '') {
            $query .= " and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  ";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
            }
        }
        if ($manager_id_set != '') {
            $query .= " and amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "')";
        }
        $query .= " GROUP BY amu.assessment_id,amu.user_id,du.department ORDER BY users ASC
                ) as main1 GROUP by main1.assessment_id,main1.users,main1.department)
                ) as final GROUP BY final.assessment_id,final.users,final.department
                ) as main3 GROUP BY main3.department";
    //   echo $query;exit;
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // 12-01-2023 Competency Graph Score accurate


    public function getLAassessment($CurrentDate)
    {
        $query = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm <='" . $CurrentDate . "' 
                    ORDER BY end_dttm DESC LIMIT 1 ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function expired_assessment_divison($Assessment_id)
    {
        $query = "SELECT DISTINCT(amu.user_id), du.department as department_name
                  FROM `assessment_mapping_user` as amu 
                  LEFT JOIN device_users as du on du.user_id = amu.user_id 
                  WHERE amu.assessment_id ='" . $Assessment_id . "' 
                  GROUP BY du.department ORDER by du.department ASC";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // end here

    //Performance comparison by Region
    public function assessment_wise_region($assessment_id, $Company_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
                  FROM assessment_mst am
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  LEFT JOIN device_users du ON du.user_id=amu.user_id
                  LEFT JOIN region rg ON du.region_id=rg.id
                  WHERE am.company_id='" . $Company_id . "'  and du.region_id !='0' ";
        if ($assessment_id != 0) {
            $query .= " and am.id='" . $assessment_id . "' ";
        }
        $query .= "  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function am_wise_region($assessment_id, $Company_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
        FROM assessment_mst am
        LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
        LEFT JOIN device_users du ON du.user_id=amu.user_id
        LEFT JOIN region rg ON du.region_id=rg.id
        WHERE am.company_id='" . $Company_id . "'  and du.region_id !='0' ";
        if ($assessment_id != 0) {
            $query .= " and am.id IN ('" . implode("', '", $assessment_id) . "') ";
        }
        $query .= " GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function Get_score_region_wise($Assessment_id, $Report_Type, $Region_id)
    {
        $query = "SELECT  assessment_id,region_id,region_name ,assessment, 
                      ifnull(round(AVG(m_score),2),0) as score  FROM (
                      SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2), SUM(overall_score)) as m_score,
                      user_id,u_name, assessment_id ,assessment,region_id,region_name,cnt FROM (
                      (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, 
                            user_id,u_name, assessment_id ,assessment,region_id,region_name FROM (
                            SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                            SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                            ifnull(amu.user_id,'')as user_id,
                            concat(du.firstname,'',du.lastname) as u_name,
                            am.id as assessment_id,du.region_id, rg.region_name,
                            am.assessment
                            FROM  assessment_mst as am 
                            LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                            LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                            LEFT JOIN device_users as du ON du.user_id = amu.user_id 
                            LEFT JOIN region as rg on rg.id = du.region_id
                            WHERE ps.parameter_type='parameter' ";
        if ($Assessment_id != '') {
            $query .= " AND amu.assessment_id = '" . $Assessment_id . "' ";
        }
        if ($Region_id != '') {
            $query .= " AND du.region_id IN ('" . implode("', '", $Region_id) . "') ";
        }
        $query .= "  GROUP BY amu.assessment_id,amu.user_id
                            ) as main GROUP BY main.assessment_id,main.user_id,main.region_id)
                        UNION ALL
                        (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, user_id,u_name, 
                        assessment_id ,assessment,region_id,region_name FROM (
                            SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                            SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                            ifnull(amu.user_id,'')as user_id,du.region_id, rg.region_name,
                            concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
                            am.assessment
                            FROM assessment_mst as am 
                            LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                            LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                            LEFT JOIN device_users as du ON du.user_id = amu.user_id
                            LEFT JOIN region as rg on rg.id = du.region_id
                            WHERE 1=1";
        if ($Assessment_id != '') {
            $query .= " AND amu.assessment_id = '" . $Assessment_id . "' ";
        }
        if ($Region_id != '') {
            $query .= " AND du.region_id IN ('" . implode("', '", $Region_id) . "') ";
        }
        $query .= " GROUP BY amu.assessment_id,amu.user_id
                            ) as main GROUP BY main.assessment_id,main.user_id,main.region_id)
                            ) as f GROUP BY f.assessment_id,f.user_id
                )as m GROUP BY m.region_id";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function LAassessment_and_type($CurrentDate)
    {
        $query = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm <='" . $CurrentDate . "'
                  ORDER BY end_dttm DESC LIMIT 1 ";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function expired_assessment_region($assessment_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
                  FROM assessment_mst am
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  LEFT JOIN device_users du ON du.user_id=amu.user_id
                  LEFT JOIN region rg ON du.region_id=rg.id
                  WHERE am.id='" . $assessment_id . "'   and du.region_id !='0'
                  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result_array();
    }
    // end here

    public function get_region_score($assessment_id, $region_id, $above_range, $second_range_from, $second_range_to, $third_range_from, $third_range_to, $less_range)
    {
        $query = "SELECT sum(above_" . $above_range . ") as above_" . $above_range . ", 
        sum(score_" . $third_range_from . "_" . $third_range_to . ") as score_" . $third_range_from . "_" . $third_range_to . ", 
        sum(score_" . $second_range_from . "_" . $second_range_to . ") as score_" . $second_range_from . "_" . $second_range_to . ", 
        sum(less_" . $less_range . ") as less_" . $less_range . ", 
        region,region_name,assessmnent_name from (
         SELECT users,overall_score, 
         CASE WHEN overall_score >= '" . $above_range . "' THEN count(overall_score) ELSE '' END as above_" . $above_range . ", 
         CASE WHEN overall_score >= '" . $third_range_from . "' and overall_score <= '" . $third_range_to . '.99' . "' THEN count(overall_score) ELSE '' END as score_" . $third_range_from . "_" . $third_range_to . ", 
         CASE WHEN overall_score >= '" . $second_range_from . "' and overall_score <= '" . $second_range_to . '.99' . "' THEN count(overall_score) ELSE '' END as score_" . $second_range_from . "_" . $second_range_to . ", 
         CASE WHEN overall_score <= '" . $less_range . '.99' . "' THEN count(overall_score) ELSE '' END as less_" . $less_range . ", 

            region,region_name,assessment_id,assessmnent_name FROM (
            SELECT round(sum(overall_score)/count(users),2) as overall_score, users, region, region_name,assessment_id,assessmnent_name from (
                        (SELECT ROUND( IF(SUM(ps.weighted_score)=0,SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score,du.user_id as users , du.region_id as region, rg.region_name as region_name ,amu.assessment_id as assessment_id,am.assessment as assessmnent_name
                        FROM ai_subparameter_score ps
                        LEFT JOIN assessment_mst as am ON am.id = ps.assessment_id 
                        LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                        LEFT JOIN device_users as du on amu.user_id = du.user_id 
                        LEFT JOIN region as rg ON du.region_id = rg.id 
                        WHERE amu.assessment_id in ('" . implode("', '", $assessment_id) . "')   
                        AND ps.parameter_type = 'parameter' AND du.region_id IN ('" . implode("', '", $region_id) . "')
                        GROUP BY du.user_id, amu.assessment_id ORDER BY du.region_id ASC)
             UNION ALL
                (SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score, du.user_id as users , du.region_id as region, rg.region_name as region_name ,amu.assessment_id as assessment_id,am.assessment as assessmnent_name
                    FROM assessment_results_trans ps 
                    LEFT JOIN assessment_mst as am ON am.id = ps.assessment_id
                    LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                    LEFT JOIN device_users as du on amu.user_id = du.user_id 
                    LEFT JOIN region as rg ON du.region_id = rg.id 
                    WHERE amu.assessment_id in ('" . implode("', '", $assessment_id) . "') AND du.region_id IN ('" . implode("', '", $region_id) . "') 
                    GROUP BY du.user_id, amu.assessment_id ORDER BY du.region_id ASC)
            )as main group by users,assessment_id  ORDER BY users ASC
         ) as main2 group by users,assessment_id
        ) as main group by region";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function get_exipired_assesment_region($assessment_id, $Company_id)
    {
        $query = "SELECT du.region_id as region_id , rg.region_name as region_name, assessment as assessment_name
                    FROM assessment_mst as am 
                    LEFT JOIN assessment_mapping_user as amu ON am.id=amu.assessment_id
                    LEFT JOIN device_users as du on amu.user_id=du.user_id
                    LEFT JOIN region as rg on rg.id=du.region_id
                    WHERE am.id in ('" . implode("', '", $assessment_id) . "')
                    AND am.company_id ='" . $Company_id . "' and du.region_id !='0'
                    GROUP BY region_id order by region_id asc";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    // Rockstars reps who scored more than 85% 

    public function all_assessment($start_date, $end_date)
    {

        $query = "SELECT id FROM assessment_mst as am WHERE am.start_dttm BETWEEN '" . $start_date . "' AND  '" . $end_date . "' ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function get_rockstars_user_final_score($company_id, $dtwhere, $dtwhere1, $tempLimit)
    {
        // $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt,assessment_id FROM (
        //           SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score, 
        //           emp_id,users_id,user_name,department,users,cnt, assessment_id FROM (
        //           (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
        //           IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM ( 
        //           SELECT ps.assessment_id as assessment_id,ROUND( 
        //           IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
        //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users, 
        //           c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, 
        //           c.department as department 
        //           FROM ai_subparameter_score ps 
        //           LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
        //           LEFT JOIN device_users as c on a.user_id = c.user_id 
        //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
        //           $dtwhere $dtwhere1
        //           AND ps.parameter_type = 'parameter' 
        //           GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)

        //          UNION ALL 

        //          (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
        //          IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
        //          (SELECT ps.assessment_id as assessment_id, 
        //          ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
        //          SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
        //          ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
        //          CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
        //          FROM assessment_results_trans ps 
        //          LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
        //          AND ps.question_id=ats.question_id
        //          LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
        //          LEFT JOIN device_users as c on a.user_id = c.user_id 
        //          LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
        //          $dtwhere $dtwhere1
        //          GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)
        //          )as main3
        //          GROUP BY assessment_id,users_id ORDER BY assessment_id,user_name) as main4 
        //          WHERE final_score >=85 GROUP by users_id 
        //          ORDER by user_name $tempLimit";
        $query  = "SELECT final_score,emp_id,users_id,user_name,department,cnt,assessment_id FROM (
                   SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score, 
                   emp_id,users_id,user_name,department,users,cnt, assessment_id FROM (
                   (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
                   IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
                   (SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/ count(ps.question_id), 
                     SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score
                    ,ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,ps.assessment_id,
                    CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                    FROM assessment_mst as am 
                    LEFT JOIN assessment_results_trans ps ON ps.assessment_id = am.id
                    LEFT JOIN device_users as c ON ps.user_id = c.user_id
                    $dtwhere $dtwhere1
                    GROUP BY ps.assessment_id,ps.user_id) as main GROUP BY assessment_id,users_id  
                    )
                    UNION ALL
                    (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
                    IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM ( 
                    SELECT ps.assessment_id as assessment_id,ROUND( 
                    IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                    SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users, 
                    c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, 
                    c.department as department 
                    FROM ai_subparameter_score ps 
                    LEFT JOIN device_users as c on ps.user_id = c.user_id 
                    LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                    $dtwhere $dtwhere1
                    AND ps.parameter_type = 'parameter' 
                    GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)  
                        )as main3
                    GROUP BY assessment_id,users_id ORDER BY assessment_id,user_name) as main4 
                    WHERE final_score >=85 GROUP by users_id 
                    ORDER by user_name $tempLimit";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    // end here

    // At risk resps who scored less than 25%
    public function At_risk_users_final_score($company_id, $dtwhere, $dtwhere1, $tempLimit)
    {
        // $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt,assessment_id FROM (
        //           SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score, 
        //           emp_id,users_id,user_name,department,users,cnt, assessment_id FROM (
        //           (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
        //           IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM ( 
        //           SELECT ps.assessment_id as assessment_id,ROUND( 
        //           IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
        //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users, 
        //           c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, 
        //           c.department as department 
        //           FROM ai_subparameter_score ps 
        //           LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
        //           LEFT JOIN device_users as c on a.user_id = c.user_id 
        //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
        //           $dtwhere $dtwhere1
        //           AND ps.parameter_type = 'parameter' 
        //           GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)

        //           UNION ALL 

        //           (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
        //           IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
        //           (SELECT ps.assessment_id as assessment_id, 
        //           ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
        //           SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
        //           ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
        //           CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
        //           FROM assessment_results_trans ps 
        //           LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
        //           AND ps.question_id=ats.question_id
        //           LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
        //           LEFT JOIN device_users as c on a.user_id = c.user_id 
        //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
        //           $dtwhere $dtwhere1
        //           GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)
        //           )as main3
        //           GROUP BY assessment_id,users_id ORDER BY assessment_id,user_name) as main4 
        //           WHERE final_score <=25 GROUP by users_id 
        //           ORDER by user_name $tempLimit";
        $query  = "SELECT final_score,emp_id,users_id,user_name,department,cnt,assessment_id FROM (
            SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score, 
            emp_id,users_id,user_name,department,users,cnt, assessment_id FROM (
            (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
            IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
            (SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/ count(ps.question_id), 
              SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score
             ,ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,ps.assessment_id,
             CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
             FROM assessment_mst as am 
             LEFT JOIN assessment_results_trans ps ON ps.assessment_id = am.id
             LEFT JOIN device_users as c ON ps.user_id = c.user_id
             $dtwhere $dtwhere1
             GROUP BY ps.assessment_id,ps.user_id) as main GROUP BY assessment_id,users_id  
             )
             UNION ALL
             (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
             IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM ( 
             SELECT ps.assessment_id as assessment_id,ROUND( 
             IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
             SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users, 
             c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, 
             c.department as department 
             FROM ai_subparameter_score ps 
             LEFT JOIN device_users as c on ps.user_id = c.user_id 
             LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
             $dtwhere $dtwhere1
             AND ps.parameter_type = 'parameter' 
             GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)  
                 )as main3
             GROUP BY assessment_id,users_id ORDER BY assessment_id,user_name) as main4 
             WHERE final_score <=25 GROUP by users_id 
             ORDER by user_name $tempLimit";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }

    // Export users of rockstars and at risk start here
    public function export_rockstars_and_at_risk_users($dtWhere, $dtOrder, $dtLimit, $type)
    {
        if ($type == "rockstars_users") {
            $score = "final_score >=85";
        } else {
            $score = "final_score <=25";
        }

        // $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt,assessment_id FROM (
        //         SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score, 
        //         emp_id,users_id,user_name,department,users,cnt, assessment_id FROM (
        //         (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
        //         IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM ( 
        //         SELECT ps.assessment_id as assessment_id,ROUND( 
        //         IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
        //         SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users, 
        //         c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, 
        //         c.department as department 
        //         FROM ai_subparameter_score ps 
        //         LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
        //         LEFT JOIN device_users as c on a.user_id = c.user_id 
        //         LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
        //         $dtWhere $dtOrder 
        //         AND ps.parameter_type = 'parameter' 
        //         GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)

        //         UNION ALL 

        //         (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
        //         IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
        //         (SELECT ps.assessment_id as assessment_id, 
        //         ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
        //         SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
        //         ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
        //         CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
        //         FROM assessment_results_trans ps 
        //         LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
        //         AND ps.question_id=ats.question_id
        //         LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
        //         LEFT JOIN device_users as c on a.user_id = c.user_id 
        //         LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
        //         $dtWhere $dtOrder 
        //         GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)
        //         )as main3
        //         GROUP BY assessment_id,users_id ORDER BY assessment_id,user_name) as main4 
        //         WHERE $score GROUP by users_id 
        //         ORDER by user_name $dtLimit";
        $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt,assessment_id FROM (
            SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score, 
            emp_id,users_id,user_name,department,users,cnt, assessment_id FROM (
            (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
            IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
            (SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/ count(ps.question_id), 
              SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score
             ,ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,ps.assessment_id,
             CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
             FROM assessment_mst as am 
             LEFT JOIN assessment_results_trans ps ON ps.assessment_id = am.id
             LEFT JOIN device_users as c ON ps.user_id = c.user_id
             $dtWhere $dtOrder 
             GROUP BY ps.assessment_id,ps.user_id) as main GROUP BY assessment_id,users_id  
             )
             UNION ALL
             (SELECT overall_score,emp_id,users_id,user_name,if(department != '', department,'-')as department ,users,
             IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,assessment_id FROM ( 
             SELECT ps.assessment_id as assessment_id,ROUND( 
             IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
             SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users, 
             c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, 
             c.department as department 
             FROM ai_subparameter_score ps 
             LEFT JOIN device_users as c on ps.user_id = c.user_id 
             LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
             $dtWhere $dtOrder 
             AND ps.parameter_type = 'parameter' 
             GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id)  
                 )as main3
             GROUP BY assessment_id,users_id ORDER BY assessment_id,user_name) as main4 
             WHERE $score GROUP by users_id 
             ORDER by user_name $dtLimit";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    // end here

    // rockstart and at risk reps ai and manual socre
    public function get_ai_score($assessment_id, $amuser_id)
    {
        $query = "SELECT ai_score,emp_id,users_id,user_name,department,users, 
                 IF(FORMAT(IFNULL(AVG(ai_score),0),2) > 0,1,0) AS cnt FROM ( 
                 SELECT ps.assessment_id as assessment_id,ROUND( 
                 IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                 SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS ai_score, ps.user_id as users, 
                 c.emp_id as emp_id, c.user_id as users_id, CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department 
                 FROM ai_subparameter_score ps 
                 LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                 LEFT JOIN device_users as c on a.user_id = c.user_id 
                 LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                 WHERE 1=1 AND a.assessment_id in ('" . $assessment_id . "') AND ps.user_id = '" . $amuser_id . "' 
                 AND ps.parameter_type = 'parameter' 
                 GROUP BY assessment_id,users_id) as main GROUP by assessment_id,users_id";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_manual_score($assessment_id, $amuser_id)
    {
        $query = "SELECT manual_score,emp_id,users_id,user_name,department,users,
                  IF(FORMAT(IFNULL(AVG(manual_score),0),2) > 0,1,0) AS cnt,assessment_id FROM 
                  (SELECT 
                  ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/ count(ps.question_id), 
                  SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS manual_score
                  ,ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,ps.assessment_id,
                  CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                  FROM assessment_results_trans ps 
                  LEFT JOIN device_users as c on ps.user_id = c.user_id 
                  LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                  WHERE 1=1  AND ps.assessment_id  in ('" . $assessment_id . "') and ps.user_id = '" . $amuser_id . "'  
                  GROUP BY assessment_id,users_id) as main GROUP BY assessment_id,users_id";

        $result = $this->db->query($query);
        return $result->row();
    }
    // end here

    // last expierd assessment and assessmnet name for rockstars and at risk start here
    public function get_last_expired_assessment($CurrentDate)
    {
        $query = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm <='" . $CurrentDate . "'
                  ORDER BY end_dttm DESC LIMIT 1 ";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function assessment_name($assessment_Id)
    {
        $query = "SELECT am.id, am.assessment as assessment,am.report_type 
                  FROM assessment_mst as am WHERE am.id='" . $assessment_Id . "' ";

        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // end here
    // public function get_top_region_score()
    // {

    //     $query = "SELECT round(sum(total)/sum(users),2) as overall_score, region_name from (
    //         (select sum(overall_score) as total, count(users) as users,region_name as region_name from
    //         (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/ count(ps.question_id),SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
    //          rg.region_name as region_name, ps.user_id as users 
    //          FROM ai_subparameter_score as ps 
    //          LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
    //          LEFT JOIN device_users as c ON ps.user_id = c.user_id 
    //          LEFT join region as rg on c.region_id =rg.id 
    //          WHERE ps.parameter_type = 'parameter'  AND rg.region_name != 'Null' GROUP BY users
    //          )as main group by region_name)
    //         UNION ALL
    //         (SELECT sum(overall_score) as total, count(users) as users, region_name from 
    //         (SELECT ps.assessment_id as assessment_id, ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id),
    //          SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , rg.region_name as region_name, 
    //          ps.user_id as users 
    //          FROM assessment_results_trans ps 
    //          LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
    //          LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
    //          LEFT JOIN device_users as c on ps.user_id = c.user_id 
    //          LEFT join region as rg on c.region_id =rg.id 
    //          GROUP BY users ORDER by region_name DESC
    //          )as main group by region_name)
    //         ) as main2 GROUP BY region_name ORDER BY overall_Score DESC LIMIT 5";
    //     $result = $this->db->query($query);
    //     $data =  $result->result_array();
    //     return $data;
    // }
    public function get_top_region_score()
    {

        $query = "SELECT round(sum(total)/sum(users),2) as overall_score, region_name from (
            
            (select sum(overall_score) as total, count(users) as users,region_name as region_name from 
            (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/ count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, rg.region_name as region_name, ps.user_id as users 
             FROM ai_subparameter_score as ps LEFT JOIN device_users as c ON ps.user_id = c.user_id 
             LEFT join region as rg on c.region_id =rg.id 
             WHERE ps.parameter_type = 'parameter' AND c.region_id != '0' GROUP BY users 
             )as main group by region_name
             )
                                                                                      
            UNION ALL                                     
            
            (SELECT sum(overall_score) as total, count(users) as users, region_name from 
            (SELECT ps.assessment_id as assessment_id, 
             ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , rg.region_name as region_name, ps.user_id as users 
             FROM assessment_results_trans ps 
             LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id 
             AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id             
             LEFT JOIN device_users as c on ps.user_id = c.user_id 
             LEFT join region as rg on c.region_id =rg.id 
             WHERE c.region_id != '0' 
             GROUP BY users 
             ORDER by region_name DESC)as main2 GROUP BY region_name ORDER BY overall_Score)
                                                                                      
            ) as main group by region_name ORDER by overall_score DESC LIMIT 5";

        $result = $this->db->query($query);
        $data =  $result->result_array();
        return $data;
    }
    public function get_bottom_region_score()
    {
        $query = "SELECT round(sum(total)/sum(users),2) as overall_score, region_name from (
            
            (select sum(overall_score) as total, count(users) as users,region_name as region_name from 
            (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/ count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, rg.region_name as region_name, ps.user_id as users 
             FROM ai_subparameter_score as ps LEFT JOIN device_users as c ON ps.user_id = c.user_id 
             LEFT join region as rg on c.region_id =rg.id 
             WHERE ps.parameter_type = 'parameter' AND c.region_id != '0' GROUP BY users 
             )as main group by region_name
             )                                                          
            UNION ALL                                     
            
            (SELECT sum(overall_score) as total, count(users) as users, region_name from 
            (SELECT ps.assessment_id as assessment_id, 
             ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , rg.region_name as region_name, ps.user_id as users 
             FROM assessment_results_trans ps 
             LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id 
             AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id             
             LEFT JOIN device_users as c on ps.user_id = c.user_id 
             LEFT join region as rg on c.region_id =rg.id 
             WHERE c.region_id != '0' 
             GROUP BY users 
             ORDER by region_name DESC)as main2 GROUP BY region_name ORDER BY overall_score)                                                                
            ) as main group by region_name ORDER by overall_score ASC limit 5";
        $result = $this->db->query($query);
        $data =  $result->result_array();
        return $data;
    }
    // Manager_wise_understanding graph start here
    public function assessment_wise_manager($assessment_id)
    {
        $query = "SELECT DISTINCT cu.userid as users_id,CONCAT(cu.first_name,' ',cu.last_name) as user_name, cu.email, a.assessment_id as assessment_id, am.assessment
         FROM `assessment_mapping_user` as a 
         LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id 
         LEFT JOIN company_users as cu ON cu.userid=a.trainer_id 
         where 1=1 ";
        if ($assessment_id != 0) {
            $query .= " AND  a.assessment_id IN (" . implode(',', $assessment_id) . ")";;
        }
        $query .= " group by users_id  order by user_name";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_last_assessment($start_date, $end_date)
    {
        $query = "SELECT DISTINCT(am.id), am.assessment, am.report_type FROM `assessment_mst` as am 
         LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
         WHERE 1=1";
        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $end_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        }
        $query .= "ORDER BY am.end_dttm DESC LIMIT 1";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    public function get_assessment_details($start_date, $end_date)
    {
        $query = "SELECT distinct am.id as assessment_id,am.report_type as report_type, CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status 
         FROM assessment_mst am LEFT JOIN assessment_report_type as art on art.id=am.report_type 
         WHERE am.status = 1 ";
        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $end_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        }
        $query .= " GROUP BY am.id ORDER BY am.end_dttm DESC";
        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    public function get_manager_details($company_id, $assessment_id)
    {
        $query = "SELECT DISTINCT(am.trainer_id) as manager_id, CONCAT(cu.first_name,' ', cu.last_name) as manager_name 
         FROM assessment_managers as am 
         LEFT JOIN company_users as cu on cu.userid = am.trainer_id where cu.company_id = '" . $company_id . "' 
         and am.assessment_id IN (" . implode(',', $assessment_id) . ")";
        //  echo $query;die;
        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }

    public function get_reps_percent_manager_wise($manager_id, $start_date, $end_date, $assessment_id)
    {
        $query = "SELECT trainer_id,manager_name, SUM(completed) AS completed, COUNT(DISTINCT(reps)) AS total_reps, ROUND(SUM(completed)*100/ COUNT(DISTINCT(reps)),2) AS percetage
         FROM (SELECT IF(SUM(is_completed) > 0,1,0) AS completed, reps, trainer_id, manager_name
         FROM (SELECT '0' AS is_completed, amu.user_id AS reps, amu.trainer_id, CONCAT(cu.first_name,' ', cu.last_name) AS manager_name,amu.user_id
         FROM assessment_mapping_user AS amu
         LEFT JOIN company_users AS cu ON cu.userid = amu.trainer_id
         LEFT JOIN assessment_mst AS am ON am.id = amu.assessment_id
         LEFT JOIN device_users du ON du.user_id = amu.user_id
         WHERE amu.trainer_id IN ($manager_id) AND du.user_id IS NOT NULL ";
        if ($assessment_id != '') {
            $query .= " and  amu.assessment_id IN ($assessment_id)";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
            }
        }
        $query .= " group by amu.trainer_id,amu.user_id 
         UNION ALL 
         SELECT aa.is_completed, '0' AS reps, amu.trainer_id, CONCAT(cu.first_name,' ', cu.last_name) AS manager_name,amu.user_id
         FROM assessment_mapping_user AS amu
         LEFT JOIN assessment_attempts AS aa ON aa.user_id = amu.user_id AND aa.assessment_id = amu.assessment_id
         LEFT JOIN company_users AS cu ON cu.userid = amu.trainer_id
         LEFT JOIN assessment_mst AS am ON am.id = amu.assessment_id
         LEFT JOIN device_users du ON du.user_id = amu.user_id 
         WHERE amu.trainer_id IN ($manager_id) and aa.is_completed=1 AND du.user_id IS NOT NULL";
        if ($assessment_id != '') {
            $query .= " and  amu.assessment_id IN ($assessment_id)";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
            }
        }
        $query .= " group by amu.trainer_id,amu.user_id 
         UNION ALL
         SELECT aa.is_completed, '0' AS reps, amu.trainer_id, CONCAT(cu.first_name,' ', cu.last_name) AS manager_name,amu.user_id
         FROM assessment_results_trans AS ps
         LEFT JOIN assessment_mst am ON am.id = ps.assessment_id
         LEFT JOIN device_users du ON ps.user_id = du.user_id
         LEFT JOIN company_users cu ON ps.trainer_id = cu.userid
         LEFT JOIN assessment_mapping_user AS amu ON amu.assessment_id = ps.assessment_id AND amu.user_id= ps.user_id AND amu.trainer_id = ps.trainer_id
         LEFT JOIN assessment_attempts AS aa ON aa.user_id = ps.user_id AND aa.assessment_id = ps.assessment_id
         WHERE amu.trainer_id IN($manager_id) AND aa.is_completed=1 AND du.user_id IS NOT NULL ";
        if ($assessment_id != '') {
            $query .= " and amu.assessment_id IN ($assessment_id)";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
            }
        }
        $query .= " GROUP BY amu.trainer_id,amu.user_id ) AS main GROUP BY main.trainer_id,main.user_id 
        ) AS main1 GROUP BY main1.trainer_id ";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }

    public function get_avg_accuracy($company_id, $new_manager_id, $start_date, $end_date, $new_assessment_id)
    {

        // $query = "SELECT main2.trainer_id,round(sum(main2.final_score)/count(main2.user_id),2) as overall_score, main2.color_range from 
        //     (SELECT main.*,round(sum(main.overall_score)/count(user_id),2) as final_score from 
        //       (SELECT a.*,crr.range_color as color_range from 
        //         (SELECT trainer_id, user_id, overall_score from 
        //             (SELECT amu.trainer_id as trainer_id,amu.user_id as user_id,ROUND(sum(ps.score)/count(ps.question_id),2) AS overall_score 
        //             FROM assessment_mapping_user amu 
        // 	        LEFT JOIN ai_subparameter_score AS ps ON amu.assessment_id = ps.assessment_id AND ps.user_id = amu.user_id 
        //             LEFT JOIN company_users cu on cu.userid = amu.trainer_id 
        //             LEFT JOIN device_users du ON du.user_id = amu.user_id
        //             LEFT JOIN assessment_mst am on am.id = ps.assessment_id 
        //             WHERE ps.parameter_type='parameter' and am.company_id = $company_id ";
        // if ($new_assessment_id != '') {
        //     $query .= " AND ps.assessment_id IN ($new_assessment_id)  ";
        // }
        // if ($start_date == '') {
        //     $query .= " and am.end_dttm <= '" . $end_date . "' ";
        // } else {
        //     $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
        // }
        // $query .= " AND amu.trainer_id IN (" . implode(',', $new_manager_id) . ")  AND amu.trainer_id!='' AND du.user_id IS NOT null GROUP BY amu.trainer_id,amu.user_id  
        //             union all 
        //             select amu.trainer_id as trainer_id, amu.user_id as user_id, 0 as overall_score 
        //             from assessment_mapping_user as amu 
        //             LEFT JOIN assessment_mst am on am.id = amu.assessment_id 
        //             LEFT JOIN company_users cu on cu.userid = amu.trainer_id 
        //             LEFT JOIN device_users du ON du.user_id = amu.user_id 
        //             WHERE 1=1 and am.company_id = $company_id  ";
        // if ($new_assessment_id != '') {
        //     $query .= " and amu.assessment_id IN ($new_assessment_id) ";
        // }
        // if ($start_date == '') {
        //     $query .= " and am.end_dttm <= '" . $end_date . "' ";
        // } else {
        //     $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
        // }
        // $query .= "  and amu.trainer_id  IN (" . implode(',', $new_manager_id) . ") AND amu.trainer_id!='' AND du.user_id IS NOT NULL GROUP BY amu.trainer_id,amu.user_id 
        //             ) as main GROUP by main.trainer_id,main.user_id 
        //         )as a LEFT JOIN company_result_range as crr ON a.overall_score between crr.range_from and crr.range_to group by a.trainer_id,a.user_id 
        //       union all 
        //       select a.*,crr.range_color as color_range from 
        //         (SELECT amu.trainer_id as trainer_id, ps.user_id,FORMAT((sum(ps.percentage)/count( ps.question_id)),2) AS overall_score 
        //         FROM assessment_results_trans AS ps 
        //         left join assessment_mapping_user as amu on amu.user_id = ps.user_id and ps.assessment_id = amu.assessment_id AND amu.trainer_id = ps.trainer_id
        //         left join assessment_mst am on am.id = ps.assessment_id 
        //         left join company_users cu on ps.trainer_id = cu.userid 
        //         LEFT JOIN device_users du ON du.user_id = amu.user_id 
        //         WHERE ps.trainer_id  IN (" . implode(',', $new_manager_id) . ") AND amu.trainer_id!='' AND du.user_id IS NOT NULL ";
        // if ($new_assessment_id != '') {
        //     $query .= " AND ps.assessment_id IN ($new_assessment_id) ";
        // }
        // $query .= "  GROUP BY ps.trainer_id,ps.user_id
        //     ) as a LEFT JOIN company_result_range as crr ON a.overall_score between crr.range_from and crr.range_to group by a.trainer_id,a.user_id
        // ) as main 
        // LEFT JOIN company_result_range as crr ON main.overall_score between crr.range_from and crr.range_to 
        // group by main.trainer_id,main.user_id
        // ) as main2 WHERE main2.trainer_id!='' group by trainer_id";

        $query = "SELECT trainer_id,manager_name,user_id,u_name, assessment_id ,assessment,overall_score,crr.range_color FROM (
                  SELECT trainer_id,manager_name,user_id,u_name, assessment_id ,assessment, 
                  ifnull(round(AVG(m_score),2),0) as overall_score,sum(m_score) FROM (
                  SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2), SUM(overall_score)) as m_score,f.* FROM  (
                  (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, trainer_id,manager_name,
                  user_id,u_name, assessment_id ,assessment FROM (
                  SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                  SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                  ifnull(amu.trainer_id ,'') as trainer_id,ifnull(amu.user_id,'')as user_id,
                  concat(cu.first_name,'',cu.last_name) as manager_name, 
                  concat(du.firstname,'',du.lastname) as u_name,
                  am.id as assessment_id,
                  am.assessment
                  FROM  assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                  LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id 
                  LEFT JOIN company_users as cu ON cu.userid = amu.trainer_id
                  WHERE ps.parameter_type='parameter' and am.company_id = '" . $company_id . "' 
                  and amu.trainer_id IN  (" . implode(',', $new_manager_id) . ") ";
        if ($new_assessment_id != '') {
            $query .= " and amu.assessment_id IN ($new_assessment_id) ";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
            }
        }
        $query .= "  GROUP BY amu.assessment_id,amu.user_id
                  ) as main GROUP BY main.assessment_id,main.user_id,main.trainer_id)
                  UNION ALL
                  (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, trainer_id,
                  manager_name,user_id,u_name, assessment_id ,assessment FROM (
                  SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                  SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                  ifnull(amu.trainer_id ,'') as trainer_id,ifnull(amu.user_id,'')as user_id,
                  concat(cu.first_name,'',cu.last_name) as manager_name, 
                  concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
                  am.assessment
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                  LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  LEFT JOIN company_users as cu ON cu.userid = amu.trainer_id
                  WHERE 1=1 and am.company_id = '" . $company_id . "' 
                  and amu.trainer_id IN  (" . implode(',', $new_manager_id) . ")  ";
        if ($new_assessment_id != '') {
            $query .= " and amu.assessment_id IN ($new_assessment_id) ";
        }
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
            }
        }
        $query .= "   GROUP BY amu.assessment_id,amu.user_id
                
                 ) as main GROUP BY main.assessment_id,main.user_id,main.trainer_id)
                 ) as f GROUP BY f.assessment_id,f.user_id
                 ) as m GROUP BY m.trainer_id ) as final 
                 LEFT JOIN company_result_range as crr ON final.overall_score between crr.range_from and crr.range_to 
                 GROUP BY final.trainer_id";
        
        $result = $this->db->query($query);
        $data =  $result->result_array();
        return $data;
    }

    // 22=02-2023
    public function get_assessment_wise_manager($company_id, $assessmentid)
    {

        $query = "SELECT DISTINCT(am.trainer_id) as manager_id, CONCAT(cu.first_name,' ', cu.last_name) as manager_name ,
                  am.assessment_id
                  FROM assessment_managers as am 
                  LEFT JOIN company_users as cu on cu.userid = am.trainer_id where cu.company_id = ".$company_id. "  
                  and am.assessment_id IN (" . implode(',', $assessmentid) . ") 
                  ORDER BY manager_id desc LIMIT 120";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }

    // 22=02-2023


    public function get_user_data($assessment_id, $manager_id, $company_id, $SDate, $EDate)
    {
        // $query = "SELECT main.*,round(sum(main.overall_score)/count(user_id),2) as final_score,SUM(main.is_completed) AS is_completed from (
        //         SELECT a.*,crr.range_color as color_range from (SELECT assessment, user_id,emp_id,manager_name, learner_name, overall_score,assessment_id,IFNULL(is_completed,0) AS is_completed from (  
        //         SELECT am.assessment as assessment,amu.user_id as user_id,du.emp_id as emp_id ,CONCAT(cu.first_name,' ',cu.last_name) as manager_name,
        //         CONCAT(du.firstname,' ',du.lastname) as learner_name ,ROUND(sum(ps.score)/count(ps.question_id),2) AS overall_score,amu.assessment_id,aa.is_completed 
        //         FROM assessment_mapping_user amu
        //         left join ai_subparameter_score AS ps on amu.assessment_id = ps.assessment_id and ps.user_id = amu.user_id
        //         left join company_users cu on cu.userid = amu.trainer_id
        //         left join device_users du on du.user_id = amu.user_id
        //         left join assessment_mst am on am.id = ps.assessment_id 
        //         LEFT JOIN assessment_attempts AS aa ON aa.user_id = amu.user_id AND aa.assessment_id = amu.assessment_id 
        //         WHERE ps.parameter_type='parameter' and am.company_id = '" . $company_id . "' ";
        // if ($assessment_id != '') {
        //     $query .= " AND  ps.assessment_id IN ($assessment_id)  ";
        // }
        // if ($SDate == '') {
        //     $query .= " and am.end_dttm <= '" . $EDate . "' ";
        // } else {
        //     $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
        // }
        // $query .= " AND amu.trainer_id = '" . $manager_id . "' AND du.user_id IS NOT NULL 
        //         GROUP BY amu.user_id
        //         union all
        //         select am.assessment as assessment,amu.user_id as user_id,du.emp_id as emp_id,CONCAT(cu.first_name,' ',cu.last_name) as manager_name,CONCAT(du.firstname,' ',du.lastname) as learner_name  ,0 as overall_score,amu.assessment_id,aa.is_completed 
        //         from assessment_mapping_user as amu
        //         left join assessment_mst am on am.id = amu.assessment_id
        //         left join company_users cu on cu.userid = amu.trainer_id
        //         left join device_users du on du.user_id = amu.user_id 
        //         LEFT JOIN assessment_attempts AS aa ON aa.user_id = amu.user_id AND aa.assessment_id = amu.assessment_id
        //         WHERE 1=1 and am.company_id = '" . $company_id . "' ";
        // if ($assessment_id != '') {
        //     $query .= " and amu.assessment_id  IN ($assessment_id)  ";
        // }
        // if ($SDate == '') {
        //     $query .= " and am.end_dttm <= '" . $EDate . "' ";
        // } else {
        //     $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
        // }
        // $query .= " and  amu.trainer_id ='" . $manager_id . "' AND du.user_id IS NOT NULL GROUP BY du.user_id 
        //         ) as main GROUP by main.user_id)as a LEFT JOIN company_result_range as crr ON a.overall_score between crr.range_from and crr.range_to group by a.user_id   
        //         union all  
        //         select a.*,crr.range_color as color_range from (SELECT am.assessment, ps.user_id,du.emp_id,CONCAT(cu.first_name,' ',cu.last_name) as manager_name,
        //         CONCAT(du.firstname,' ',du.lastname) as learner_name,FORMAT((sum(ps.percentage)/count( ps.question_id)),2) AS overall_score, ps.assessment_id,IFNULL(aa.is_completed,0) AS is_completed  
        //                     FROM assessment_results_trans AS ps 
        //                     left join assessment_mst am on am.id = ps.assessment_id
        //                     left join device_users du on ps.user_id = du.user_id
        //                     left join company_users cu on ps.trainer_id = cu.userid 
        //                     left join assessment_mapping_user as amu on  amu.assessment_id = ps.assessment_id and amu.user_id= ps.user_id and amu.trainer_id = ps.trainer_id 
        //                     LEFT JOIN assessment_attempts AS aa ON aa.user_id = ps.user_id AND aa.assessment_id = ps.assessment_id 
        //                     WHERE amu.trainer_id='" . $manager_id . "' AND du.user_id IS NOT null ";
        // if ($assessment_id != '') {
        //     $query .= " AND ps.assessment_id  IN ($assessment_id)";
        // }
        // $query .=  "  GROUP BY user_id)  as a LEFT JOIN company_result_range as crr ON a.overall_score between crr.range_from and crr.range_to group by a.user_id
        //         ) as main LEFT JOIN company_result_range as crr ON main.overall_score between crr.range_from and crr.range_to group by main.user_id;";
        // 
        $query = "SELECT user_id,emp_id,learner_name,manager_name,assessment_id,assessment,ifnull(is_completed,0) as is_completed,
                  final_score,total_score,crr.range_color FROM (
                  SELECT user_id,emp_id,learner_name,manager_name,assessment_id,assessment,sum(is_completed) as is_completed,
                  IF(SUM(cnt) > 1, round(AVG(score),2),SUM(score)) as final_score,sum(score) total_score FROM (
                  (SELECT round(ifnull(avg(overall_score),0),2)as score ,user_id,emp_id,learner_name,manager_name,
                  assessment_id,assessment,is_completed,
                  IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt from(
                  SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                  SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                  ifnull(amu.user_id ,'') as user_id,du.emp_id as emp_id ,concat(du.firstname,'',du.lastname) as learner_name,
                  am.assessment,aa.is_completed,CONCAT(cu.first_name,' ',cu.last_name) as manager_name,am.id as assessment_id
                  FROM  assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                  LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                  left join company_users cu on cu.userid = amu.trainer_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  LEFT JOIN assessment_attempts AS aa ON aa.user_id = amu.user_id AND aa.assessment_id = amu.assessment_id 
                  WHERE ps.parameter_type='parameter' and am.company_id = '" . $company_id . "' ";
        if ($assessment_id != '') {
            $query .= " and amu.assessment_id  IN ($assessment_id)  ";
        }
        if ($SDate != '' or $EDate != '') {
            if ($SDate == '') {
                $query .= " and am.end_dttm <= '" . $EDate . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
            }
        }
        $query .= " and  amu.trainer_id ='" . $manager_id . "' GROUP BY amu.user_id)  as main GROUP BY main.user_id)
                  UNION ALL 
                  (SELECT round(ifnull(avg(overall_score),0),2)as score ,user_id,emp_id,learner_name,manager_name,
                  assessment_id,assessment,is_completed,
                  IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt from(
                  SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                  SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                  ifnull(amu.user_id ,'') as user_id,du.emp_id as emp_id,concat(du.firstname,'',du.lastname) as learner_name,
                  am.assessment,aa.is_completed,CONCAT(cu.first_name,' ',cu.last_name) as manager_name,am.id as assessment_id
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                  LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                  left join company_users cu on cu.userid = amu.trainer_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  LEFT JOIN assessment_attempts AS aa ON aa.user_id = amu.user_id AND aa.assessment_id = amu.assessment_id
                  WHERE 1=1 and am.company_id = '" . $company_id . "' ";
        if ($assessment_id != '') {
            $query .= " and amu.assessment_id  IN ($assessment_id)  ";
        }
        if ($SDate != '' or $EDate != '') {
            if ($SDate == '') {
                $query .= " and am.end_dttm <= '" . $EDate . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
            }
        }
        $query .= " and  amu.trainer_id ='" . $manager_id . "' GROUP BY amu.user_id)  as main GROUP BY main.user_id)
                  ) as m1 GROUP by m1.user_id
                  ) as m2 
                  LEFT JOIN company_result_range as crr ON m2.final_score between crr.range_from and crr.range_to 
                  GROUP BY m2.user_id";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    //  Hitmap start here
    public function last_assessment($start_date, $current_date)
    {
        $query = "SELECT id, assessment,report_type FROM assessment_mst am WHERE 1=1 ";

        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $current_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $current_date . "' ";
        }
        $query .= " ORDER BY end_dttm DESC";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function heatmap_wise_region($ass_id, $Company_id)
    {
        $query = "SELECT * from (
          (SELECT du.region_id, art.assessment_id, ifnull(rg.region_name,'No Region') as region_name 
          FROM assessment_final_results art 
          LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
          LEFT JOIN device_users du ON du.user_id=art.user_id 
          LEFT JOIN region rg ON rg.id=du.region_id WHERE 1=1 ";
        if (!empty($ass_id)) {
            $query .= " and  am.id IN (" . implode(',', $ass_id) . ") ";
        }
        $query .= " and am.company_id='" . $Company_id . "' group by art.assessment_id,du.region_id order by du.region_id)

            UNION ALL
      
          (SELECT du.region_id,art.assessment_id, ifnull(rg.region_name,'No Region') as region_name 
          FROM ai_subparameter_score art 
          LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
          LEFT JOIN device_users du ON du.user_id=art.user_id 
          LEFT JOIN region rg ON rg.id=du.region_id 
          WHERE parameter_type='parameter' AND 1=1 ";
        if (!empty($ass_id)) {
            $query .= " and am.id IN (" . implode(',', $ass_id) . ") ";
        }
        $query .= " and  am.company_id='" . $Company_id . "' group by art.assessment_id,du.region_id order by region_id)
          ) as main group by main.region_name";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_region_result($Company_id, $region_id = '', $StartDate = '', $EndDate = '', $assessment_id = '')
    {
        $cond = '';
        $cond1 = '';
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $cond .= " OR '" . $EndDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($assessment_id != '') {
            $cond .= " AND am.id in (" . implode(',', $assessment_id) . ") ";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        $cond .= " group by art.parameter_id,art.assessment_id,rg.id,art.user_id ";

        $query = "SELECT (CASE WHEN E.result = 0 AND D.assessor_status = null or D.assessor_status = ''
                  THEN 'Not Completed' ELSE 'Completed' end) as status,
                  E.*,ifnull(D.assessor_status,'0') as assessor_status FROM (
                  SELECT f.assessment_id, f.para_assess_id, f.name,f.user_id, f.region_name,f.parameter_id, f.region_id,
                  FORMAT(avg(f.score),2) as result,ctr.range_color FROM (
                  SELECT main.assessment_id,main.para_assess_id,
                  main.name,main.user_id,IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'),
                  IFNULL(FORMAT(SUM(main.result),2),'---')) as score,main.region_name,main.parameter_id, main.region_id FROM (
                  (SELECT round(IFNULL(avg(a.result),0),2) AS result,a.tresult,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, a.user_id,a.region_id,a.assessment_id,a.name,a.para_assess_id,a.region_name,a.parameter_id FROM ( SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,IFNULL(avg(art.accuracy),0) AS tresult, art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id, art.parameter_id, ifnull(rg.region_name,'No Region') as region_name
                  FROM assessment_trainer_weights art
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                  LEFT JOIN device_users du ON du.user_id=art.user_id
                  LEFT JOIN region rg ON rg.id=du.region_id WHERE 1=1
                  $cond
                  order by du.region_id) as a group by a.para_assess_id,a.parameter_id,a.region_id,a.user_id order by a.parameter_id)
                  UNION ALL
                  (SELECT round(IFNULL(AVG(b.result),0),2) AS result, FORMAT(IFNULL(AVG(b.tresult),0),2) AS tresult,
                  IF(FORMAT(IFNULL(AVG(b.result),0),2) > 0,1,0) AS cnt, b.user_id,b.region_id,b.assessment_id,
                  b.name, b.para_assess_id,b.region_name,b.parameter_id FRom ( SELECT FORMAT(ifnull(avg(art.score),0),2) as result,
                  IFNULL(avg(art.score),0) AS tresult, art.user_id,du.region_id,art.assessment_id,am.assessment as name,
                  art.assessment_id as para_assess_id,art.parameter_id, ifnull(rg.region_name,'No Region') as region_name
                  FROM ai_subparameter_score art
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id
                  LEFT JOIN device_users du ON du.user_id=art.user_id
                  LEFT JOIN region rg ON rg.id=du.region_id WHERE parameter_type='parameter'
                  $cond ) as b
                  group by b.para_assess_id,b.parameter_id,b.region_id,b.user_id order by b.parameter_id)
                  ) as main group by main.assessment_id,main.parameter_id,main.user_id, main.region_id order by main.region_id
                    ) as f LEFT JOIN company_result_range ctr ON f.score BETWEEN ctr.range_from AND ctr.range_to 
                    group by f.assessment_id, f.region_id
                  ) E
                  LEFT JOIN (SELECT  A.assessment_id,A.user_id,IF(B.user_id IS NULL,0,1) as assessor_status,
                  B.region_id,B.region_name FROM ( SELECT user_id,sum(is_completed) as is_candidate_complete,end_dttm,start_dttm
                  assessment_id FROM (SELECT aa.user_id,aa.is_completed,aa.assessment_id,du.region_id,
                  ifnull(rg.region_name,'No Region') as region_name ,am.end_dttm,am.start_dttm
                  FROM assessment_attempts aa
                  LEFT JOIN assessment_mst as am ON am.id = aa.assessment_id
                  LEFT JOIN device_users as du ON aa.user_id = du.user_id
                  LEFT JOIN region as rg ON du.region_id = rg.id
                  WHERE  1=1 ";
        if ($assessment_id != '') {
            $query .= " AND aa.assessment_id in (" . implode(',', $assessment_id) . ") ";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        $query .= " UNION ALL
                  SELECT au.user_id,0 AS is_completed,au.assessment_id,du.region_id,
                  ifnull(rg.region_name,'No Region') as region_name
                  ,am.end_dttm,am.start_dttm
                  FROM assessment_allow_users au
                  LEFT JOIN assessment_mst as am ON am.id = au.assessment_id
                  LEFT JOIN device_users as du ON au.user_id = du.user_id
                  LEFT JOIN region as rg ON du.region_id = rg.id
                  WHERE 1=1 ";
        if ($assessment_id != '') {
            $query .= " AND au.assessment_id in (" . implode(',', $assessment_id) . ") ";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        $query .= " AND au.user_id NOT IN (
                  select user_id from assessment_attempts where 1=1  ";
        if ($assessment_id != '') {
            $query .= " AND assessment_id in (" . implode(',', $assessment_id) . ") ";
        }
        if ($region_id != '') {
            $query .= " AND region_id in (" . implode(',', $region_id) . ")";
        }
        $query .= "  )) AA
                  GROUP BY AA.user_id ) A
                  LEFT JOIN (
                  SELECT acr.assessment_id,acr.user_id ,du.region_id,ifnull(rg.region_name,'No Region') as region_name,
                  am.end_dttm,am.start_dttm
                  FROM assessment_complete_rating acr
                  LEFT JOIN assessment_mst as am ON am.id = acr.assessment_id
                  LEFT JOIN device_users as du ON acr.user_id = du.user_id
                  LEFT JOIN region as rg ON du.region_id = rg.id
                  WHERE  1=1";
        if ($assessment_id != '') {
            $query .= " AND acr.assessment_id in (" . implode(',', $assessment_id) . ") ";
        }
        if ($region_id != '') {
            $query .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        $query .= "  )B ON B.assessment_id = A.assessment_id
                  AND B.user_id = A.user_id
                  WHERE 1=1 ";
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND (date(B.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR date(B.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(B.start_dttm) AND date(B.end_dttm)";
            $cond .= " OR '" . $EndDate . "' BETWEEN date(B.start_dttm) AND date(B.end_dttm))";
        }
        if ($assessment_id != '') {
            $cond .= " AND B.assessment_id in (" . implode(',', $assessment_id) . ") ";
        }
        if ($region_id != '') {
            $cond .= " AND b.region_id in (" . implode(',', $region_id) . ")";
        }
        $query .= " GROUP BY A.assessment_id,B.region_id
                  ORDER BY B.region_name)D ON E.assessment_id =D.assessment_id
                  GROUP BY  E.assessment_id, E.region_id order by E.region_id";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_region_result_old($Company_id, $region_id, $StartDate = '', $EndDate = '', $assessment_id = '')
    {
        $cond = "";
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
        }
        if ($assessment_id != '') {
            $cond .= " AND am.id in (" . $assessment_id . ")";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        if ($Company_id != '') {
            $cond .= " AND am.company_id  = $Company_id";
        }

        $query = "SELECT FORMAT(ifnull(AVG(final.result),0),2)AS score,ctr.range_color,final.* FROM ( (
               SELECT main.result,main.tresult,main.user_id,main.region_id,main.assessment_id, main.name,
               main.region_name,main.para_assess_id
               FROM( 
               SELECT FORMAT(ifnull(avg(art.score),0),2) AS result, IFNULL(avg(art.score),0) AS tresult,
               art.user_id,du.region_id,art.assessment_id, am.assessment as name,art.assessment_id as para_assess_id, 
               ifnull(rg.region_name,'No Region') as region_name 
               FROM ai_subparameter_score art 
               LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
               LEFT JOIN device_users du ON du.user_id=art.user_id 
               LEFT JOIN region rg ON rg.id=du.region_id 
               WHERE `parameter_type` = 'parameter'  
               $cond 
               GROUP BY art.assessment_id,rg.id order by du.region_id) as main 
               GROUP BY main.para_assess_id,main.region_id order by main.region_id) 
               UNION ALL (
               SELECT main.result,main.tresult,main.user_id,main.region_id,main.assessment_id, main.name,main.region_name,
               main.para_assess_id
               FROM( 
               SELECT FORMAT(ifnull(avg(art.accuracy),0),2) AS result, IFNULL(avg(art.accuracy),0) AS tresult,art.user_id, 
               du.region_id,art.assessment_id, am.assessment as name,art.assessment_id as para_assess_id,
               ifnull(rg.region_name,'No Region') as region_name
               FROM assessment_trainer_weights art 
               LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
               LEFT JOIN device_users du ON du.user_id=art.user_id 
               LEFT JOIN region rg ON rg.id=du.region_id WHERE 1=1 
               $cond
               GROUP BY art.assessment_id,du.region_id order by du.region_id ) as main 
               GROUP BY main.assessment_id,main.region_id order by main.region_id) 
               )as final LEFT JOIN company_result_range ctr ON final.result BETWEEN ctr.range_from AND ctr.range_to 
               group by final.assessment_id, final.region_id order by final.region_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    // horizontal_and_vertical_avg
    public function get_horizontal_and_vertical_avg($Company_id, $report_by, $region_id = '', $SDate = '', $EDate = '', $assessment_id = '')
    {
        $cond = "";
        if ($SDate != '' && $EDate != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $SDate . "' AND '" . $EDate . "'";
            $cond .= " OR '" . $SDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $cond .= " OR '" . $EDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        if ($assessment_id != '') {
            $cond .= " AND am.id in (" . implode(',', $assessment_id) . ") ";
        }

        if ($report_by == 1) {
            $Qry = "SELECT IF(SUM(final.cnt) > 1,IFNULL(round(AVG(final.result),2),'---'),
                  IFNULL(round(SUM(final.result),2),'---')) AS region_result,final.* FROM (
                  SELECT FORMAT(IFNULL(avg(main.result),0),2) AS result,IF(SUM(main.cnt) > 0,1,0) AS cnt,
                  main.region_id,main.assessment_id ,
                  main.name,ctr.range_color
                  FROM (
                  (SELECT round(IFNULL(avg(a.result),0),2) AS result,a.tresult,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, 
                  a.user_id,a.region_id,a.assessment_id,a.name,a.para_assess_id,a.region_name FROM ( 
                  SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,IFNULL(avg(art.accuracy),0) AS tresult, 
                  art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id, 
                  ifnull(rg.region_name,'No Region') as region_name 
                  FROM assessment_trainer_weights art 
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                  LEFT JOIN device_users du ON du.user_id=art.user_id 
                  LEFT JOIN region rg ON rg.id=du.region_id 
                  WHERE 1=1  $cond
                  group by art.parameter_id,art.assessment_id,rg.id order by du.region_id) as a 
                  group by a.para_assess_id,a.region_id order by a.region_id)
                  UNION ALL
                  (SELECT round(IFNULL(AVG(b.result),0),2) AS result, 
                  FORMAT(IFNULL(AVG(b.tresult),0),2) AS tresult, IF(FORMAT(IFNULL(AVG(b.result),0),2) > 0,1,0) AS cnt, 
                  b.user_id,b.region_id,b.assessment_id,b.name, b.para_assess_id,b.region_name FRom ( 
                  SELECT FORMAT(ifnull(avg(art.score),0),2) as result, IFNULL(avg(art.score),0) AS tresult, art.user_id,
                  du.region_id,art.assessment_id,am.assessment as name, art.assessment_id as para_assess_id, 
                  ifnull(rg.region_name,'No Region') as region_name 
                  FROM ai_subparameter_score art 
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                  LEFT JOIN device_users du ON du.user_id=art.user_id 
                  LEFT JOIN region rg ON rg.id=du.region_id 
                  WHERE parameter_type='parameter' $cond
                  group by art.parameter_id,art.assessment_id,rg.id ) as b group by b.para_assess_id,b.region_id order by b.region_id)
                  ) as main LEFT JOIN company_result_range ctr ON main.result BETWEEN ctr.range_from AND ctr.range_to 
                  GROUP BY main.assessment_id,main.region_id ORDER by main.region_id
                  ) as final GROUP BY final.region_id";
        } else if ($report_by == 2) {
            $Qry = "SELECT IF(SUM(final.cnt) > 1,IFNULL(round(AVG(final.result),2),'---'),
                  IFNULL(round(SUM(final.result),2),'---')) AS region_result,final.* FROM (
                  SELECT FORMAT(IFNULL(avg(main.result),0),2) AS result,IF(SUM(main.cnt) > 0,1,0) AS cnt,
                  main.region_id,main.assessment_id ,
                  main.name,ctr.range_color
                  FROM (
                  (SELECT round(IFNULL(avg(a.result),0),2) AS result,a.tresult,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, 
                  a.user_id,a.region_id,a.assessment_id,a.name,a.para_assess_id,a.region_name FROM ( 
                  SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,IFNULL(avg(art.accuracy),0) AS tresult, 
                  art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id, 
                  ifnull(rg.region_name,'No Region') as region_name 
                  FROM assessment_trainer_weights art 
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                  LEFT JOIN device_users du ON du.user_id=art.user_id 
                  LEFT JOIN region rg ON rg.id=du.region_id 
                  WHERE 1=1 $cond
                  group by art.parameter_id,art.assessment_id,rg.id order by du.region_id) as a 
                  group by a.para_assess_id,a.region_id order by a.region_id)
                  UNION ALL
                  (SELECT round(IFNULL(AVG(b.result),0),2) AS result, 
                  FORMAT(IFNULL(AVG(b.tresult),0),2) AS tresult, IF(FORMAT(IFNULL(AVG(b.result),0),2) > 0,1,0) AS cnt, 
                  b.user_id,b.region_id,b.assessment_id,b.name, b.para_assess_id,b.region_name FRom ( 
                  SELECT FORMAT(ifnull(avg(art.score),0),2) as result, IFNULL(avg(art.score),0) AS tresult, art.user_id,
                  du.region_id,art.assessment_id,am.assessment as name, art.assessment_id as para_assess_id, 
                  ifnull(rg.region_name,'No Region') as region_name 
                  FROM ai_subparameter_score art 
                  LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                  LEFT JOIN device_users du ON du.user_id=art.user_id 
                  LEFT JOIN region rg ON rg.id=du.region_id 
                  WHERE parameter_type='parameter' $cond
                  group by art.parameter_id,art.assessment_id,rg.id ) as b group by b.para_assess_id,b.region_id order by b.region_id)
                  ) as main LEFT JOIN company_result_range ctr ON main.result BETWEEN ctr.range_from AND ctr.range_to 
                  GROUP BY main.assessment_id,main.region_id ORDER by main.assessment_id
                  ) as final GROUP BY final.assessment_id";
        }
        $result = $this->db->query($Qry);
        return $result->result();
    }

    public function get_horizontal_and_vertical_avg_old($Company_id, $report_by, $region_id, $StartDate, $EndDate, $lastAssessmentId)
    {
        $cond = "";
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND (date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR date(am.end_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "'";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm)";
            $cond .= " OR '" . $StartDate . "' BETWEEN date(am.start_dttm) AND date(am.end_dttm))";
        }
        if ($region_id != '') {
            $cond .= " AND du.region_id in (" . implode(',', $region_id) . ")";
        }
        if ($lastAssessmentId != '') {
            $cond .= " AND am.id in (" . implode(',', $lastAssessmentId) . ") ";
        }
        if ($report_by == 1) {
            // horizontal avg
            $query = "SELECT FORMAT(ifnull(AVG(final.result),0),2)AS region_score,ctr.range_color,final.* FROM ( (
              SELECT main.result,main.tresult,main.user_id,main.region_id,main.assessment_id, main.name,
              main.region_name,main.para_assess_id
              FROM( 
              SELECT FORMAT(ifnull(avg(art.score),0),2) AS result, IFNULL(avg(art.score),0) AS tresult,
              art.user_id,du.region_id,art.assessment_id, am.assessment as name,art.assessment_id as para_assess_id, 
              ifnull(rg.region_name,'No Region') as region_name 
              FROM ai_subparameter_score art 
              LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
              LEFT JOIN device_users du ON du.user_id=art.user_id 
              LEFT JOIN region rg ON rg.id=du.region_id 
              WHERE parameter_type = 'parameter' 
              $cond 
              GROUP BY art.assessment_id,rg.id order by du.region_id) as main 
              GROUP BY main.para_assess_id,main.region_id order by main.region_id) 
              UNION ALL (
              SELECT main.result,main.tresult,main.user_id,main.region_id,main.assessment_id, main.name,main.region_name,
              main.para_assess_id
              FROM( 
              SELECT FORMAT(ifnull(avg(art.accuracy),0),2) AS result, IFNULL(avg(art.accuracy),0) AS tresult,art.user_id, 
              du.region_id,art.assessment_id, am.assessment as name,art.assessment_id as para_assess_id,
              ifnull(rg.region_name,'No Region') as region_name
              FROM assessment_trainer_weights art 
              LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
              LEFT JOIN device_users du ON du.user_id=art.user_id 
              LEFT JOIN region rg ON rg.id=du.region_id WHERE 1=1 
              $cond
              GROUP BY art.assessment_id,du.region_id order by du.region_id ) as main 
              GROUP BY main.assessment_id,main.region_id order by main.region_id) 
              )as final LEFT JOIN company_result_range ctr ON final.result BETWEEN ctr.range_from AND ctr.range_to 
              group by final.region_name order by final.region_id";
        } elseif ($report_by == 2) {
            // vertical avg
            $query = "SELECT FORMAT(ifnull(AVG(final.result),0),2)AS region_score,ctr.range_color,final.region_id, final.assessment_id FROM ( (
              SELECT main.result,main.tresult,main.user_id,main.region_id,main.assessment_id, main.name,
              main.region_name,main.para_assess_id
              FROM( 
              SELECT FORMAT(ifnull(avg(art.score),0),2) AS result, IFNULL(avg(art.score),0) AS tresult,
              art.user_id,du.region_id,art.assessment_id, am.assessment as name,art.assessment_id as para_assess_id, 
              ifnull(rg.region_name,'No Region') as region_name 
              FROM ai_subparameter_score art 
              LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
              LEFT JOIN device_users du ON du.user_id=art.user_id 
              LEFT JOIN region rg ON rg.id=du.region_id 
              WHERE parameter_type = 'parameter'  $cond
              
              GROUP BY art.assessment_id,rg.id order by du.region_id) as main 
              GROUP BY main.para_assess_id,main.region_id order by main.region_id) 
              UNION ALL (
              SELECT main.result,main.tresult,main.user_id,main.region_id,main.assessment_id, main.name,main.region_name,
              main.para_assess_id
              FROM( 
              SELECT FORMAT(ifnull(avg(art.accuracy),0),2) AS result, IFNULL(avg(art.accuracy),0) AS tresult,art.user_id, 
              du.region_id,art.assessment_id, am.assessment as name,art.assessment_id as para_assess_id,
              ifnull(rg.region_name,'No Region') as region_name
              FROM assessment_trainer_weights art 
              LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
              LEFT JOIN device_users du ON du.user_id=art.user_id 
              LEFT JOIN region rg ON rg.id=du.region_id WHERE 
              1=1 $cond
              GROUP BY art.assessment_id,du.region_id order by du.region_id ) as main 
              GROUP BY main.assessment_id,main.region_id order by main.region_id) 
              )as final LEFT JOIN company_result_range ctr ON final.result BETWEEN ctr.range_from AND ctr.range_to 
              group by final.assessment_id order by final.assessment_id";
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_parameter_user_result_new_old($company_id, $region_id, $assessment_id)
    {
        $query = "SELECT main.*,round(sum(score)/count(user_id),2) AS result from (   
          SELECT a.*,ctr.range_color FROM 
          (select art.assessment_id as para_asses_id, art.user_id as user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
           du.region_id, plm.parameter_id as parameter_id,plm.description AS parameter, art.score 
           from assessment_trainer_weights as art 
           left join device_users as du on du.user_id = art.user_id 
           left join parameter_label_mst as plm on plm.id = art.parameter_id 
           WHERE art.assessment_id = $assessment_id and du.region_id =  $region_id)
          as a LEFT JOIN company_result_range ctr ON a.score between ctr.range_from and ctr.range_to

          UNION ALL

          SELECT a.*,ctr.range_color FROM     
          (select ais.assessment_id as para_asses_id ,ais.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
           du.region_id,plm.parameter_id as parameter_id,plm.description AS parameter, IFNULL(FORMAT(avg(ais.score),2),'---') AS score 
           from ai_subparameter_score as ais 
           left JOIN device_users du ON du.user_id=ais.user_id 
           Left join region as rg ON rg.id=du.region_id 
           left join parameter_label_mst as plm on plm.id = ais.parameter_id 
           where ais.assessment_id = $assessment_id and ais.parameter_type='parameter' and du.region_id= $region_id
           GROUP BY ais.user_id,ais.parameter_id)
          as a LEFT JOIN company_result_range ctr ON a.score between ctr.range_from and ctr.range_to

          ) as main GROUP by main.user_id, main.parameter_id";


        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameter_user_result_new($company_id, $region_id, $assessment_id)
    {
        $query = "SELECT main.assessment_id,main.para_assess_id,main.parameter_id,main.parameter,main.user_id,main.firstname,
                    main.region_id,main.region_name, IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'), 
                    IFNULL(FORMAT(SUM(main.result),2),'---')) AS p_result FROM (
                    (SELECT round(IFNULL(avg(a.result),0),2) AS result,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, 
                    a.user_id,a.region_id,a.assessment_id,a.firstname,a.parameter,a.para_assess_id,a.region_name,a.parameter_id 
                    FROM ( SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,CONCAT(du.firstname,' ',du.lastname) as firstname, 
                    art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id,
                    ifnull(rg.region_name,'No Region') as region_name,IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,
                    IF(plm.description is null,pm.description, plm.description) AS parameter
                    FROM assessment_trainer_weights art 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id
                    LEFT JOIN region rg ON rg.id=du.region_id 
                    left join parameter_label_mst as pm on pm.id = art.parameter_id 
                    WHERE 1=1  AND du.region_id = $region_id AND art.assessment_id = $assessment_id 
                    group by art.parameter_id,art.assessment_id,rg.id,art.user_id 
                    order by du.region_id) as a group by a.para_assess_id,a.parameter_id,a.region_id,a.user_id order by a.parameter_id)
                    UNION ALL
                    (SELECT round(IFNULL(avg(b.result),0),2) AS result,IF(IFNULL(avg(b.result),0) > 0,1,0) AS cnt, b.user_id,
                    b.region_id,
                    b.assessment_id,b.firstname,b.parameter,b.para_assess_id,b.region_name,b.parameter_id FROM ( 
                    SELECT IFNULL(FORMAT(avg(art.score),2),'---') AS result,CONCAT(du.firstname,' ',du.lastname) as firstname, 
                    art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id,
                    ifnull(rg.region_name,'No Region') as region_name,IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,
                    IF(plm.description is null,pm.description, plm.description) AS parameter
                    FROM ai_subparameter_score art 
                    LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                    LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                    LEFT JOIN device_users du ON du.user_id=art.user_id 
                    LEFT JOIN region rg ON rg.id=du.region_id
                    LEFT JOIN parameter_label_mst pm ON pm.id=art.parameter_label_id 
                    WHERE parameter_type='parameter' AND du.region_id = $region_id AND art.assessment_id = $assessment_id 
                    group by art.parameter_id,art.assessment_id,rg.id,art.user_id ) as b 
                    group by b.para_assess_id,b.parameter_id,b.region_id,b.user_id order by b.parameter_id) 
                    )as main GROUP BY  main.user_id, main.parameter_id order by main.user_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_user_average_old($Company_id, $report_by, $region_id = '', $assessment_id = '')
    {
        if ($report_by == 1) {
            $query = "SELECT main3.parameter_id, round(sum(result)/count(main3.user_id),2) as horizontal_avg  from (
              SELECT main.*,round(sum(score)/count(user_id),2) AS result from (   
                      SELECT a.*,ctr.range_color FROM 
                      (select art.assessment_id as para_asses_id, art.user_id as user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                       du.region_id, plm.parameter_id as parameter_id,plm.description AS parameter, art.score 
                       from assessment_trainer_weights as art 
                       left join device_users as du on du.user_id = art.user_id 
                       left join parameter_label_mst as plm on plm.id = art.parameter_id 
                       WHERE art.assessment_id = $assessment_id and du.region_id = $region_id)
                      as a LEFT JOIN company_result_range ctr ON a.score between ctr.range_from and ctr.range_to
                      UNION ALL
                      SELECT a.*,ctr.range_color FROM     
                      (select ais.assessment_id as para_asses_id ,ais.user_id,CONCAT(du.firstname,' ',du.lastname) as firstname,
                       du.region_id,plm.parameter_id as parameter_id,plm.description AS parameter, IFNULL(FORMAT(avg(ais.score),2),'---') AS score 
                       from ai_subparameter_score as ais 
                       left JOIN device_users du ON du.user_id=ais.user_id 
                       Left join region as rg ON rg.id=du.region_id 
                       left join parameter_label_mst as plm on plm.id = ais.parameter_id 
                       where ais.assessment_id = $assessment_id and ais.parameter_type='parameter' and du.region_id= $region_id
                       GROUP BY ais.user_id,ais.parameter_id)
                      as a LEFT JOIN company_result_range ctr ON a.score between ctr.range_from and ctr.range_to
                      ) as main GROUP by main.user_id, main.parameter_id)as main3 group by main3.parameter_id";
        } elseif ($report_by == 3) {
            $query = "SELECT FORMAT(sum(main.result)/count(*),2) as result, main.user_id from 
                    (SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result,du.user_id 
                     FROM assessment_trainer_weights art 
                     LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                     LEFT JOIN device_users du ON du.user_id=art.user_id 
                     WHERE art.assessment_id= $assessment_id AND du.region_id= $region_id 
                     GROUP BY du.user_id 
                     
                     UNION ALL 
                     
                     SELECT FORMAT(IFNULL(avg(art.score),0),2) AS result,du.user_id 
                     FROM ai_subparameter_score art 
                     LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                     LEFT JOIN device_users du ON du.user_id=art.user_id 
                     WHERE art.assessment_id= $assessment_id AND du.region_id= $region_id AND art.parameter_type='parameter' 
                     GROUP BY du.user_id) as main 
          GROUP by main.user_id order by main.user_id";
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_user_average($Company_id, $report_by, $region_id = '', $assessment_id = '')
    {

        if ($report_by == 1) {
            $query = "SELECT main.assessment_id,main.para_assess_id,main.parameter_id,main.parameter,main.user_id,main.firstname,
                      main.region_id,main.region_name, IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'), 
                      IFNULL(FORMAT(SUM(main.result),2),'---')) AS p_result FROM (
                      (SELECT round(IFNULL(avg(a.result),0),2) AS result,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, a.user_id,
                      a.region_id,a.assessment_id,a.firstname,a.parameter,a.para_assess_id,a.region_name,a.parameter_id FROM 
                      ( SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,CONCAT(du.firstname,' ',du.lastname) as firstname,
                       art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id,
                      ifnull(rg.region_name,'No Region') as region_name,IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,
                      IF(plm.description is null,pm.description, plm.description) AS parameter
                      FROM assessment_trainer_weights art 
                      LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                      LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                      LEFT JOIN device_users du ON du.user_id=art.user_id 
                      LEFT JOIN region rg ON rg.id=du.region_id 
                      left join parameter_label_mst as pm on pm.id = art.parameter_id 
                      WHERE 1=1 
                      AND du.region_id = $region_id AND art.assessment_id = $assessment_id 
                      group by art.parameter_id,art.assessment_id,rg.id,art.user_id 
                      order by du.region_id) as a group by a.para_assess_id,a.parameter_id,a.region_id,a.user_id 
                      order by a.parameter_id)
                      UNION ALL
                      (SELECT round(IFNULL(avg(b.result),0),2) AS result,IF(IFNULL(avg(b.result),0) > 0,1,0) AS cnt, b.user_id,
                      b.region_id,b.assessment_id,b.firstname,b.parameter,b.para_assess_id,b.region_name,b.parameter_id FROM 
                      ( SELECT IFNULL(FORMAT(avg(art.score),2),'---') AS result,CONCAT(du.firstname,' ',du.lastname) as firstname, 
                      art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id,
                      ifnull(rg.region_name,'No Region') as region_name,IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,
                      IF(plm.description is null,pm.description, plm.description) AS parameter
                      FROM ai_subparameter_score art 
                      LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                      LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                      LEFT JOIN device_users du ON du.user_id=art.user_id 
                      LEFT JOIN region rg ON rg.id=du.region_id
                      LEFT JOIN parameter_label_mst pm ON pm.id=art.parameter_label_id 
                      WHERE parameter_type='parameter' AND du.region_id = $region_id AND art.assessment_id = $assessment_id
                      group by art.parameter_id,art.assessment_id,rg.id,art.user_id ) as b 
                      group by b.para_assess_id,b.parameter_id,b.region_id,b.user_id order by b.parameter_id) 
                      )as main GROUP BY  main.parameter_id order by main.parameter_id";
        } elseif ($report_by == 3) {
            $query = "SELECT main.assessment_id,main.para_assess_id,main.parameter_id,main.parameter,main.user_id,main.firstname,
                      main.region_id,main.region_name, IF(SUM(main.cnt) > 1,IFNULL(FORMAT(AVG(main.result),2),'---'), 
                      IFNULL(FORMAT(SUM(main.result),2),'---')) AS p_result FROM (
                      (SELECT round(IFNULL(avg(a.result),0),2) AS result,IF(IFNULL(avg(a.result),0) > 0,1,0) AS cnt, a.user_id,
                      a.region_id,a.assessment_id,a.firstname,a.parameter,a.para_assess_id,a.region_name,a.parameter_id FROM (
                      SELECT IFNULL(FORMAT(avg(art.accuracy),2),'---') AS result,CONCAT(du.firstname,' ',du.lastname) as firstname, 
                      art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id,
                      ifnull(rg.region_name,'No Region') as region_name,IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,
                      IF(plm.description is null,pm.description, plm.description) AS parameter
                      FROM assessment_trainer_weights art 
                      LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                      LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                      LEFT JOIN device_users du ON du.user_id=art.user_id 
                      LEFT JOIN region rg ON rg.id=du.region_id 
                      left join parameter_label_mst as pm on pm.id = art.parameter_id 
                      WHERE 1=1 
                      AND du.region_id = $region_id AND art.assessment_id = $assessment_id 
                      group by art.parameter_id,art.assessment_id,rg.id,art.user_id 
                      order by du.region_id) as a group by a.para_assess_id,a.parameter_id,a.region_id,a.user_id 
                      order by a.parameter_id)
                      UNION ALL
                      (SELECT round(IFNULL(avg(b.result),0),2) AS result,IF(IFNULL(avg(b.result),0) > 0,1,0) AS cnt, b.user_id,
                      b.region_id,b.assessment_id,b.firstname,b.parameter,b.para_assess_id,b.region_name,b.parameter_id FROM ( 
                      SELECT IFNULL(FORMAT(avg(art.score),2),'---') AS result,CONCAT(du.firstname,' ',du.lastname) as firstname, 
                      art.user_id,du.region_id,art.assessment_id,am.assessment as name,art.assessment_id as para_assess_id,
                      ifnull(rg.region_name,'No Region') as region_name,IF(plm.id=0 or plm.id is null, pm.id, plm.id) as parameter_id,
                      IF(plm.description is null,pm.description, plm.description) AS parameter
                      FROM ai_subparameter_score art 
                      LEFT JOIN parameter_label_mst plm ON plm.id=art.parameter_label_id
                      LEFT JOIN assessment_mst am ON am.id=art.assessment_id 
                      LEFT JOIN device_users du ON du.user_id=art.user_id 
                      LEFT JOIN region rg ON rg.id=du.region_id
                      LEFT JOIN parameter_label_mst pm ON pm.id=art.parameter_label_id 
                      WHERE parameter_type='parameter' AND du.region_id = $region_id AND art.assessment_id = $assessment_id
                      group by art.parameter_id,art.assessment_id,rg.id,art.user_id ) as b 
                      group by b.para_assess_id,b.parameter_id,b.region_id,b.user_id order by b.parameter_id) 
                      )as main GROUP BY  main.user_id order by main.user_id";
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    // Hitmap End here

    // competency by manager all function start here
    public function lastassessment($SDate, $EDate)
    {

        $result = "SELECT id, assessment,report_type FROM assessment_mst am WHERE 1=1 ";
        if ($SDate == '') {
            $result .= " and am.end_dttm <= '" . $EDate . "' ";
        } else {
            $result .= " and am.end_dttm between '" . $SDate . "' AND  '" . $EDate . "' ";
        }
        $result .= " ORDER BY end_dttm DESC";
        $query = $this->db->query($result);
        $row = $query->result_array();
        return $row;
    }
    public function assessment_wise_managers($assessment_id1, $Company_id)
    {
        $query = "SELECT DISTINCT cu.userid as user_id,CONCAT(cu.first_name,' ',cu.last_name) as user_name, 
                    cu.email, a.assessment_id as assessment_id, am.assessment
                    FROM assessment_mapping_user as a 
                    LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id 
                    LEFT JOIN company_users as cu ON cu.userid=a.trainer_id 
                    where 1=1 ";
        if ($assessment_id1 != 0) {
            $query .= " and a.assessment_id IN(" . implode(',', $assessment_id1) . ") ";
        }
        $query .= " GROUP BY a.trainer_id  order by am.id";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function time_wise_managers($SDate, $EDate, $Company_id)
    {
        $query = "SELECT DISTINCT(am.trainer_id) as manager_id, CONCAT(cu.first_name,' ', cu.last_name) as manager_name 
                  FROM assessment_managers as am 
                  LEFT JOIN company_users as cu on cu.userid = am.trainer_id 
                  LEFT JOIN assessment_mst as a ON am.assessment_id = a.id
                  where cu.company_id = '" . $Company_id . "' ";
        if ($SDate == '') {
            $query .= " and a.end_dttm <= '" . $EDate . "' ";
        } else {
            $query .= " and a.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate)
    {
        // $query = "SELECT assessment_id,manager_id,m_name,user_id,u_name,sum(cnt),IF(SUM(cnt) > 1, round(AVG(score),2),  
        //           SUM(score)) as overall_score,sum(score) FROM (
        //           (SELECT 
        //           round(ifnull(avg(overall_score),0),2)as score,sum(overall_score),
        //           assessment_id,assessment,
        //           IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,
        //           manager_id,m_name,count(user_id) as user_id,u_name
        //           FROM (
        //           SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
        //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
        //           ifnull(amu.trainer_id ,'') as manager_id,ifnull(amu.user_id,'')as user_id,
        //           concat(cu.first_name,'',cu.last_name) as m_name, 
        //           concat(du.firstname,'',du.lastname) as u_name,
        //           am.id as assessment_id,
        //           am.assessment
        //           FROM  assessment_mst as am 
        //           LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
        //           LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
        //           LEFT JOIN device_users as du ON du.user_id = amu.user_id 
        //           LEFT JOIN company_users as cu ON cu.userid = amu.trainer_id
        //           WHERE ps.parameter_type='parameter' and am.company_id = '" . $Company_id . "' 
        //           and amu.trainer_id IN (" . implode(',', $manager_id) . ") ";
        // if ($Assessment_id != '') {
        //     $query .= " and amu.assessment_id IN (" . implode(',', $Assessment_id) . ") ";
        // }
        // if ($SDate == '') {
        //     $query .= " and am.end_dttm <= '" . $EDate . "' ";
        // } else {
        //     $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
        // }
        // $query .= " GROUP BY amu.assessment_id,amu.user_id
        //           ) as main GROUP BY main.assessment_id,main.manager_id)
        //           UNION ALL
        //           (SELECT 
        //           round(ifnull(avg(overall_score),0),2)as score,sum(overall_score),
        //           assessment_id,assessment,
        //           IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt,
        //           manager_id,m_name,count(user_id) as user_id,u_name
        //           FROM (
        //           SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
        //           SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
        //           ifnull(amu.trainer_id ,'') as manager_id,ifnull(amu.user_id,'')as user_id,
        //           concat(cu.first_name,'',cu.last_name) as m_name, 
        //           concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
        //           am.assessment
        //           FROM assessment_mst as am 
        //           LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
        //           LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
        //           LEFT JOIN device_users as du ON du.user_id = amu.user_id
        //           LEFT JOIN company_users as cu ON cu.userid = amu.trainer_id
        //           WHERE 1=1 and am.company_id = '" . $Company_id . "' 
        //           and amu.trainer_id IN (" . implode(',', $manager_id) . ") ";
        // if ($Assessment_id != '') {
        //     $query .= " and amu.assessment_id IN (" . implode(',', $Assessment_id) . ") ";
        // }
        // if ($SDate == '') {
        //     $query .= " and am.end_dttm <= '" . $EDate . "' ";
        // } else {
        //     $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
        // }
        // $query .= "  GROUP BY amu.assessment_id,amu.user_id
        //           ) as main GROUP BY main.assessment_id,main.manager_id)  
        //           ) as m3 GROUP BY m3.manager_id";

        $query = "SELECT  assessment_id,manager_id,m_name,count(user_id) as user_id,u_name,assessment, 
                  ifnull(round(AVG(m_score),2),0) as overall_score,sum(m_score),sum(cnt)FROM (
                  SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2), SUM(overall_score)) as m_score,f.* FROM  (
                  (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, manager_id,m_name,
                  user_id,u_name, assessment_id ,assessment FROM (
                  SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                  SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                  ifnull(amu.trainer_id ,'') as manager_id,ifnull(amu.user_id,'')as user_id,
                  concat(cu.first_name,'',cu.last_name) as m_name, 
                  concat(du.firstname,'',du.lastname) as u_name,
                  am.id as assessment_id,
                  am.assessment
                  FROM  assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                  LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id 
                  LEFT JOIN company_users as cu ON cu.userid = amu.trainer_id
                  WHERE ps.parameter_type='parameter' and am.company_id = '" . $Company_id . "'  
                  and amu.trainer_id IN (" . implode(',', $manager_id) . ")  ";

        if ($Assessment_id != '') {
            $query .= " and amu.assessment_id IN (" . implode(',', $Assessment_id) . ") ";
        }
        if ($SDate != '' or $EDate != '') {
            if ($SDate == '') {
                $query .= " and am.end_dttm <= '" . $EDate . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
            }
        }
        $query .= " GROUP BY amu.assessment_id,amu.user_id
                  ) as main GROUP BY main.assessment_id,main.user_id,main.manager_id)
                  UNION ALL
                  (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, manager_id,
                  m_name,user_id,u_name, assessment_id ,assessment FROM (
                  SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                  SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                  ifnull(amu.trainer_id ,'') as manager_id,ifnull(amu.user_id,'')as user_id,
                  concat(cu.first_name,'',cu.last_name) as m_name, 
                  concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
                  am.assessment
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                  LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                  LEFT JOIN device_users as du ON du.user_id = amu.user_id
                  LEFT JOIN company_users as cu ON cu.userid = amu.trainer_id
                  WHERE 1=1 and am.company_id = '" . $Company_id . "'  
                  and amu.trainer_id IN  (" . implode(',', $manager_id) . ")   ";
        if ($Assessment_id != '') {
            $query .= " and amu.assessment_id IN (" . implode(',', $Assessment_id) . ") ";
        }
        if ($SDate != '' or $EDate != '') {
            if ($SDate == '') {
                $query .= " and am.end_dttm <= '" . $EDate . "' ";
            } else {
                $query .= " and am.end_dttm BETWEEN '" . $SDate . "' and '" . $EDate . "' ";
            }
        }
        $query .= "  GROUP BY amu.assessment_id,amu.user_id
                  ) as main GROUP BY main.assessment_id,main.user_id,main.manager_id)
                  ) as f GROUP BY f.assessment_id,f.user_id
                  ) as m GROUP BY m.manager_id";

        $result = $this->db->query($query);
        return $result->result_array();
    }

    // End here
    // By Bhautik Rana (02 jan 2023) - new Graph 
    // Competency_by_region graph
    public function get_region($assessmentid, $manager_id, $Company_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
                  FROM assessment_mst am
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  LEFT JOIN device_users du ON du.user_id=amu.user_id
                  LEFT JOIN region rg ON du.region_id=rg.id
                  WHERE 1=1 ";
        if ($assessmentid != 0) {
            $query .= " and  am.id IN (" . implode(',', $assessmentid) . ")  ";
        }
        if ($manager_id != 0) {
            $query .= " and amu.trainer_id IN (" . implode(',', $manager_id) . ") ";
        }
        $query .= " AND am.company_id='" . $Company_id . "'  and du.region_id !='0'
                  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_time_based_region($start_date, $end_date)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
        FROM assessment_mst am
        LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
        LEFT JOIN device_users du ON du.user_id=amu.user_id
        LEFT JOIN region rg ON du.region_id=rg.id
        WHERE 1=1 ";
        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $end_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        }
        $query .= " and du.region_id !='0' GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    public function assessment_based_region($start_date, $end_date, $assessment_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
        FROM assessment_mst am
        LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
        LEFT JOIN device_users du ON du.user_id=amu.user_id
        LEFT JOIN region rg ON du.region_id=rg.id
        WHERE 1=1 ";
        if ($start_date == '') {
            $query .= " and am.end_dttm <= '" . $end_date . "' ";
        } else {
            $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        }
        if ($assessment_id != '') {
            $query .= " and am.id  IN (" . implode(',', $assessment_id) . ")  ";
        }
        $query .= " and du.region_id !='0' GROUP BY du.region_id ORDER BY du.region_id asc";

        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    public function  get_region_data($start_date, $end_date, $assessment_Id, $reg_id_set, $manager_id_set, $Company_id)
    {
        // $query = "SELECT assessment_id,assessment,region_id,region_name, sum(result),
        //           IF(SUM(cnt) > 1, round(AVG(result),2),  SUM(result)) as score FROM ( 
        //           (SELECT round(ifnull(avg(overall_score),0),2)as result,assessment_id,assessment,region_id,region_name,
        //           IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt FROM(
        //           SELECT assessment_id,assessment,region_id,region_name,overall_score
        //           FROM (
        //           SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
        //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
        //           ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id,
        //           am.assessment,du.region_id, rg.region_name
        //          FROM  assessment_mst as am 
        //          LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
        //          LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
        //          LEFT JOIN device_users as du ON du.user_id = amu.user_id
        //          LEFT JOIN region as rg on rg.id = du.region_id
        //          WHERE ps.parameter_type='parameter'  ";
        // if ($start_date != '' or $end_date != '') {
        //     if ($start_date == '') {
        //         $query .= " and am.end_dttm <= '" . $end_date . "' ";
        //     } else {
        //         $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        //     }
        // }
        // if ($assessment_Id != '') {
        //     $query .= " and  amu.assessment_id IN ('" . implode("', '", $assessment_Id) . "') ";
        // }
        // if ($manager_id_set != '') {
        //     $query .= " and  amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "') ";
        // }
        // $query .= " AND du.region_id IN ('" . implode("', '", $reg_id_set) . "')  
        //          GROUP BY am.id,du.region_id ORDER BY rg.region_name ASC
        //          ) as main GROUP BY main.assessment_id,main.region_id  
        //          ) as m1  GROUP BY m1.region_id)
        //          UNION ALL
        //          (SELECT round(ifnull(avg(overall_score),0),2)as result ,assessment_id,assessment,region_id,region_name, 
        //          IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt
        //          FROM(
        //          SELECT assessment_id,assessment,region_id,region_name, main1.overall_score
        //          FROM (
        //          SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
        //          SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
        //          ifnull(amu.user_id ,'') as users,concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
        //          am.assessment,du.department,du.region_id, rg.region_name
        //          FROM assessment_mst as am 
        //          LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
        //          LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
        //          LEFT JOIN device_users as du ON du.user_id = amu.user_id
        //          LEFT JOIN region as rg on rg.id = du.region_id
        //          WHERE 1=1  ";
        // if ($start_date != '' or $end_date != '') {
        //     if ($start_date == '') {
        //         $query .= " and am.end_dttm <= '" . $end_date . "' ";
        //     } else {
        //         $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
        //     }
        // }
        // if ($assessment_Id != '') {
        //     $query .= " and  amu.assessment_id IN ('" . implode("', '", $assessment_Id) . "') ";
        // }
        // if ($manager_id_set != '') {
        //     $query .= " and  amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "') ";
        // }
        // $query .= " AND du.region_id IN ('" . implode("', '", $reg_id_set) . "')   
        //          GROUP BY amu.assessment_id,du.region_id ORDER BY rg.region_name ASC
        //          ) as main1 GROUP by main1.assessment_id,main1.region_id
        //          ) as m1  GROUP BY m1.region_id)
        //          ) as m2 GROUP BY m2.region_name";
        $query = "SELECT  assessment_id,region_id,region_name ,assessment, 
                  ifnull(round(AVG(m_score),2),0) as score FROM (
                  SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2), SUM(overall_score)) as m_score,cnt,user_id,u_name, 
                  assessment_id ,assessment,region_id,region_name FROM (
                  (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, 
                        user_id,u_name, assessment_id ,assessment,region_id,region_name FROM (
                        SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                        SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                        ifnull(amu.user_id,'')as user_id,
                        concat(du.firstname,'',du.lastname) as u_name,
                        am.id as assessment_id,du.region_id, rg.region_name,
                        am.assessment
                        FROM  assessment_mst as am 
                        LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id = am.id
                        LEFT JOIN ai_subparameter_score as ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                        LEFT JOIN device_users as du ON du.user_id = amu.user_id 
                        LEFT JOIN region as rg on rg.id = du.region_id
                        WHERE ps.parameter_type='parameter' ";

        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
            }
        }
        if ($assessment_Id != '') {
            $query .= " and  amu.assessment_id IN ('" . implode("', '", $assessment_Id) . "') ";
        }
        if ($manager_id_set != '') {
            $query .= " and  amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "') ";
        }
        $query .= " AND du.region_id IN ('" . implode("', '", $reg_id_set) . "') GROUP BY amu.assessment_id,amu.user_id
                        ) as main GROUP BY main.assessment_id,main.user_id,main.region_id)
                    UNION ALL
                    (SELECT overall_score,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt, user_id,u_name, 
                    assessment_id ,assessment,region_id,region_name FROM (
                        SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                        SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                        ifnull(amu.user_id,'')as user_id,du.region_id, rg.region_name,
                        concat(du.firstname,'',du.lastname) as u_name, am.id as assessment_id ,
                        am.assessment
                        FROM assessment_mst as am 
                        LEFT JOIN assessment_mapping_user as amu ON am.id = amu.assessment_id 
                        LEFT JOIN assessment_results_trans AS ps  ON am.id = ps.assessment_id  AND amu.user_id = ps.user_id
                        LEFT JOIN device_users as du ON du.user_id = amu.user_id
                        LEFT JOIN region as rg on rg.id = du.region_id
                        WHERE 1=1 ";
        if ($start_date != '' or $end_date != '') {
            if ($start_date == '') {
                $query .= " and am.end_dttm <= '" . $end_date . "' ";
            } else {
                $query .= " and am.end_dttm between '" . $start_date . "' AND  '" . $end_date . "' ";
            }
        }
        if ($assessment_Id != '') {
            $query .= " and  amu.assessment_id IN ('" . implode("', '", $assessment_Id) . "') ";
        }
        if ($manager_id_set != '') {
            $query .= " and  amu.trainer_id IN ('" . implode("', '", $manager_id_set) . "') ";
        }
        $query .= " AND du.region_id IN ('" . implode("', '", $reg_id_set) . "')  
                    GROUP BY amu.assessment_id,amu.user_id
                        ) as main GROUP BY main.assessment_id,main.user_id,main.region_id)
                        ) as f GROUP BY f.assessment_id,f.user_id
                        )as m GROUP BY m.region_id";

        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }

    public function get_region_name($reg_id_set)
    {
        $query = "SELECT du.region_id as region, rg.region_name as region_name
                  from device_users as du 
                  LEFT JOIN region as rg ON du.region_id = rg.id
                  where  du.region_id IN ('" . implode("', '", $reg_id_set) . "') GROUP by region";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    // end
    // By Bhautik Rana (02 jan 2023) - new Graph 
}
