<?php

use PhpOffice\PhpSpreadsheet\Reader\IReader;

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
    public function LastExpiredAssessment($CurrentDate)
    {
        $result = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm <= '" . $CurrentDate . "' 
                       ORDER BY end_dttm DESC LIMIT 1 ";
        $query = $this->db->query($result);
        $row = $query->result();
        return $row;
    }
    public function getCompetencyscore($Assessment_id, $Report_Type)
    {
        if ($Report_Type == 1) {
            // $query = "SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
            //         SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score ,
            //         ps.user_id as users
            //         FROM ai_subparameter_score ps 
            //         LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
            //         LEFT join assessment_mst as am on am.id = amu.assessment_id
            //         WHERE am.id = '" . $Assessment_id . "' AND parameter_type = 'parameter'
            //         GROUP BY ps.user_id ORDER BY users ASC";

            $query = "SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
            SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score ,
            ps.user_id as users
            FROM assessment_mst as am 
            LEFT JOIN ai_subparameter_score ps on am.id = ps.assessment_id
            -- LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
            WHERE am.id = '" . $Assessment_id . "' AND parameter_type = 'parameter'
            GROUP BY ps.user_id ORDER BY users ASC";
        } else if ($Report_Type == 2) {
            // $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
            //         SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score  ,
            //         ps.user_id as users
            //         FROM assessment_results_trans AS ps 
            //         LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
            //         LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id 
            //         LEFT join assessment_mst as am on am.id = amu.assessment_id
            //         WHERE am.id = '" . $Assessment_id . "' 
            //         GROUP BY ps.user_id ORDER BY users ASC";
            $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
            SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score  ,
            ps.user_id as users
            FROM assessment_mst as am 
            LEFT JOIN assessment_results_trans AS ps ON am.id = ps.assessment_id
            LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
            -- LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id 
            WHERE am.id = '" . $Assessment_id . "'
            GROUP BY ps.user_id ORDER BY users ASC";
        } else {
            // $query = "SELECT (
            //           CASE WHEN overall_score=0 THEN SUM(overall_score) ELSE round(AVG(overall_score),2) end) as overall_score, users  
            //           FROM(   
            //           (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
            //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score ,ps.user_id as users
            //           FROM ai_subparameter_score ps 
            //           LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
            //           left join assessment_mst as am ON amu.assessment_id = am.id
            //           WHERE am.id  = '" . $Assessment_id . "' AND parameter_type = 'parameter'
            //           GROUP BY ps.user_id ORDER BY overall_score ASC) 
            //           UNION ALL 
            //           SELECT (CASE WHEN overall_score=0 THEN '0' ELSE overall_score end) as overall_score, users  FROM  
            //           (SELECT  ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
            //           SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score  ,ps.user_id as users
            //           FROM assessment_results_trans AS ps 
            //           LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
            //           LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id 
            //           left join assessment_mst as am ON amu.assessment_id = am.id
            //           WHERE am.id = '" . $Assessment_id . "' 
            //           GROUP BY ps.user_id ORDER BY overall_score ASC) as main
            //           ) as main2 GROUP BY users";
            $query = "SELECT IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as overall_score ,users,cnt FROM (
                (SELECT overall_score ,users,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt FROM(
                (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score,
                ps.user_id as users 
                FROM assessment_mst as am 
                LEFT JOIN ai_subparameter_score ps on am.id = ps.assessment_id
                -- LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                WHERE am.id = '" . $Assessment_id . "'  AND parameter_type = 'parameter' 
                GROUP BY ps.user_id ORDER BY overall_score ASC) ) as main GROUP BY users ORDER BY overall_score ASC)
              
                UNION ALL
                  
                (SELECT overall_score, users,IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt 
                FROM (SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight)) ,2) AS overall_score ,ps.user_id as users
                FROM assessment_mst as am 
                LEFT JOIN assessment_results_trans AS ps ON am.id = ps.assessment_id
                LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
                -- LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id 
                WHERE am.id = '" . $Assessment_id . "'  GROUP BY ps.user_id 
                ORDER BY overall_score ASC) as main GROUP BY users 
                ORDER BY overall_score ASC) ) as final GROUP by users  
                ORDER BY `overall_score`  DESC";
        }
        
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function get_name($Assessment_id)
    {
        $LcSqlStr = "SELECT assessment FROM assessment_mst am WHERE id='" . $Assessment_id . "' ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->result_array();
        return $row;
    }
    //end here

    // Performance comparison by module
    public function LastExpiredFiveAssessment($CurrentDate)
    {
        $result = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm <='" . $CurrentDate . "' 
                    ORDER BY end_dttm DESC LIMIT 5 ";
        $query = $this->db->query($result);
        $row = $query->result_array();
        return $row;
    }
    public function performance_comparison_avg($Assessment_id)
    {
        $query = "SELECT assessment_id,assessment ,round(sum(overall_score) /sum(users),2) as scores FROM (
                  (SELECT assessment_id,assessment ,sum(overall_score) as overall_score ,count(users) as users FROM
                  (SELECT ROUND( IF(SUM(ps.weighted_score)=0, 
                  SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , 
                  ps.user_id as users, am.id as assessment_id, am.assessment as assessment 
                  FROM ai_subparameter_score ps 
                  LEFT JOIN assessment_mst as am ON am.id = ps.assessment_id
                --   LEFT JOIN assessment_allow_users as amu ON ps.assessment_id = amu.assessment_id AND ps.user_id = amu.user_id
                  WHERE am.id  IN (" . implode(',', $Assessment_id) . ") AND parameter_type = 'parameter' 
                  GROUP BY ps.user_id ORDER BY users ASC) as main1 GROUP by assessment_id)
                  
                  UNION ALL
                
                  (SELECT assessment_id,assessment ,sum(overall_score) as overall_score ,count(users) as users FROM 
                  (SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                  SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score , 
                  ps.user_id as users, am.id as assessment_id ,am.assessment as  assessment
                  FROM assessment_mst as am 
                  -- LEFT JOIN assessment_mapping_user as amu ON am.id  = amu.assessment_id
                  -- LEFT JOIN assessment_results_trans AS ps ON am.id = ps.assessment_id AND amu.user_id = ps.user_id
                  LEFT JOIN assessment_results_trans AS ps ON am.id = ps.assessment_id
                  LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id 
                   AND am.id=ats.assessment_id AND ps.question_id=ats.question_id 
                  WHERE am.id IN (" . implode(',', $Assessment_id) . ") GROUP BY ps.user_id ORDER BY users ASC) 
                  as main GROUP BY assessment_id)) as final GROUP by assessment_id";

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
                  WHERE amu.assessment_id='" . $Assessment_id . "' AND du.department IS NOT NULL ORDER by du.department asc";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function Get_score_divison_wise($Assessment_id, $Report_Type, $DvisonId_Set)
    {

        if ($Report_Type == 1) {
            $query = "SELECT assessment_id,SUM(overall_score),round(SUM(overall_score) / COUNT(user_id),2) as score,
                      department_name,assessment FROM (
                      SELECT ps.assessment_id as assessment_id,
                      ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                      SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score,ps.user_id as user_id, 
                      du.department as department_name,am.assessment as assessment
                      FROM ai_subparameter_score as ps 
                      LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
                      LEFT JOIN device_users as du on amu.user_id = du.user_id
                      LEFT JOIN assessment_mst as am ON ps.assessment_id = am.id 
                      WHERE am.id ='" . $Assessment_id . "' AND ps.parameter_type = 'parameter' 
                      and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  
                      GROUP BY user_id) as main GROUP BY department_name ORDER BY department_name";
        } else if ($Report_Type == 2) {
            $query = " SELECT assessment_id,round(SUM(overall_score) / COUNT(user_id),2) as score,department_name,assessment FROM (
                        SELECT ps.assessment_id as assessment_id, 
                        ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
                        SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score,ps.user_id as user_id, du.department as department_name , am.assessment as assessment
                        FROM assessment_results_trans as ps 
                        LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
                        LEFT JOIN device_users as du on ps.user_id = du.user_id 
                        LEFT JOIN assessment_mst as am ON ps.assessment_id = am.id
                        WHERE am.id ='" . $Assessment_id . "'  
                        and du.department IN ('" . implode("', '", $DvisonId_Set) . "')
                        GROUP BY user_id) as main GROUP BY department_name ORDER BY department_name asc";
        } else {

            // new Query
            $query = "SELECT assessment_id,round(SUM(overall_score) / COUNT(user_id),2) as score,
            department_name,assessment FROM (
            (SELECT ps.assessment_id as assessment_id,
            ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
            SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score,ps.user_id as user_id, 
            du.department as department_name , am.assessment as assessment
            FROM ai_subparameter_score as ps 
            LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
            LEFT JOIN device_users as du on amu.user_id = du.user_id 
            LEFT JOIN assessment_mst as am ON ps.assessment_id = am.id 
            WHERE am.id ='" . $Assessment_id . "' AND ps.parameter_type = 'parameter' 
            and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  
            GROUP BY user_id)
      
            UNION ALL 
          
            (SELECT ps.assessment_id as assessment_id, 
            ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
            SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score,ps.user_id as user_id, 
            du.department as department_name , am.assessment as assessment
            FROM assessment_results_trans as ps 
            LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
            LEFT JOIN device_users as du on ps.user_id = du.user_id 
            LEFT JOIN assessment_mst as am ON ps.assessment_id = am.id
            WHERE am.id ='" . $Assessment_id . "' 
            and du.department IN ('" . implode("', '", $DvisonId_Set) . "')  
            GROUP BY user_id) ) as main GROUP by department_name";
        }
        
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

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
                  WHERE am.id='" . $assessment_id . "' AND am.company_id='" . $Company_id . "'  and du.region_id !='0'
                  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function Get_score_region_wise($Assessment_id, $Report_Type, $Region_id)
    {
        if ($Report_Type == 1) {
            // $query = "SELECT assessment_id, 
            //           round(sum(overall_score) / COUNT(users),2) as score ,region,region_name,assessment  FROM (
            //           SELECT ps.assessment_id as assessment_id,
            //           ROUND( IF(SUM(ps.weighted_score)=0, 
            //           SUM(ps.score)/count(ps.question_id), 
            //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users,
            //           du.region_id as region, rg.region_name as region_name,am.assessment as assessment
            //           FROM ai_subparameter_score ps 
            //           LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
            //           LEFT JOIN device_users as du on amu.user_id = du.user_id 
            //           LEFT JOIN region as rg ON du.region_id = rg.id 
            //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
            //           WHERE amu.assessment_id = '" . $Assessment_id . "' AND ps.parameter_type = 'parameter' 
            //           and du.region_id IN ('" . implode("', '", $Region_id) . "')  GROUP by users) as main 
            //           GROUP BY region_name ORDER BY region_name asc";
            $query = "SELECT assessment_id,round(sum(overall_score) / COUNT(users),2) as score ,
                    region,region_name,assessment  FROM (
                    SELECT ps.assessment_id as assessment_id, ROUND( IF(SUM(ps.weighted_score)=0, 
                    SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
                    ps.user_id as users, du.region_id as region, rg.region_name as region_name,am.assessment as assessment 
                    FROM ai_subparameter_score ps 
                    LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
                    -- LEFT JOIN assessment_allow_users as amu ON am.id=amu.assessment_id AND ps.user_id=amu.user_id 
                    LEFT JOIN device_users as du on ps.user_id = du.user_id 
                    LEFT JOIN region as rg ON du.region_id = rg.id 
                    WHERE am.id = '" . $Assessment_id . "' AND du.region_id in ('" . implode("', '", $Region_id) . "') AND ps.parameter_type = 'parameter' 
                    GROUP by users ) as main GROUP BY region_name ORDER BY region_name asc";
        } else if ($Report_Type == 2) {
            // $query = "SELECT assessment_id,
            //           round(sum(overall_score) / COUNT(users),2) as score ,region,region_name,assessment  FROM (
            //           SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_percentage)=0, 
            //           SUM(ps.score)/count(ps.question_id), SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score, ps.user_id as users,
            //           du.region_id as region, rg.region_name as region_name,am.assessment as assessment
            //           FROM assessment_results_trans ps 
            //           LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
            //           LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
            //           LEFT JOIN device_users as du on amu.user_id = du.user_id 
            //           LEFT JOIN region as rg ON du.region_id = rg.id 
            //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
            //           WHERE amu.assessment_id = '" . $Assessment_id . "'  and du.region_id IN ('" . implode("', '", $Region_id) . "') 
            //           GROUP by users) as main GROUP BY region_name ORDER BY region_name asc";
            $query = "SELECT assessment_id, 
                      round(sum(overall_score) / COUNT(users),2) as score ,region,region_name,assessment  FROM (
                      SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_percentage)=0, 
                      SUM(ps.score)/count(ps.question_id), 
                      SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight)) ,2) AS overall_score, 
                      ps.user_id as users, du.region_id as region, rg.region_name as region_name,am.assessment as assessment 
                      FROM assessment_results_trans ps 
                      LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                    --   LEFT JOIN assessment_mapping_user as amu ON am.id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
                      LEFT JOIN device_users as du on ps.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id = '" . $Assessment_id . "' AND du.region_id in('" . implode("', '", $Region_id) . "')  GROUP by users) as main 
                      GROUP BY region_name ORDER BY region_name asc";
        } else {
            // $query = "SELECT assessment_id, round(SUM(overall_score) / COUNT(users),2) as score,region,region_name,assessment FROM (
            //           (SELECT ps.assessment_id as assessment_id,
            //           ROUND( IF(SUM(ps.weighted_score)=0, 
            //           SUM(ps.score)/count(ps.question_id), 
            //           SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, ps.user_id as users,
            //           du.region_id as region, rg.region_name as region_name,am.assessment as assessment
            //           FROM ai_subparameter_score ps 
            //           LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
            //           LEFT JOIN device_users as du on amu.user_id = du.user_id 
            //           LEFT JOIN region as rg ON du.region_id = rg.id 
            //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
            //           WHERE amu.assessment_id = '" . $Assessment_id . "' AND ps.parameter_type = 'parameter' 
            //           and du.region_id IN ('" . implode("', '", $Region_id) . "')  GROUP by users) 
            //           UNION ALL
            //           (SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_percentage)=0, 
            //           SUM(ps.score)/count(ps.question_id), 
            //           SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score, ps.user_id as users,
            //           du.region_id as region, rg.region_name as region_name,am.assessment as assessment
            //           FROM assessment_results_trans ps 
            //           LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
            //           LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
            //           LEFT JOIN device_users as du on amu.user_id = du.user_id 
            //           LEFT JOIN region as rg ON du.region_id = rg.id 
            //           LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
            //           WHERE amu.assessment_id = '" . $Assessment_id . "'  
            //           and du.region_id IN ('" . implode("', '", $Region_id) . "') 
            //           GROUP by users) ) as main GROUP BY region_name";

            $query = "SELECT assessment_id, round(SUM(overall_score) / COUNT(users),2) as score,region,region_name,assessment 
                    FROM ( 
                    (SELECT ps.assessment_id as assessment_id, 
                      ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
                      ps.user_id as users, du.region_id as region, rg.region_name as region_name,am.assessment as assessment 
                      FROM ai_subparameter_score ps 
                      LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
                    --   LEFT JOIN assessment_allow_users as amu ON am.id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN device_users as du on ps.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id = '" . $Assessment_id . "' AND du.region_id in('" . implode("', '", $Region_id) . "') AND ps.parameter_type = 'parameter' GROUP by users) 
                      UNION ALL 
                      (SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight)) ,2) AS overall_score, 
                      ps.user_id as users, du.region_id as region, rg.region_name as region_name,am.assessment as assessment 
                      FROM assessment_results_trans ps 
                      LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                    --  LEFT JOIN assessment_mapping_user as amu ON am.id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
                      LEFT JOIN device_users as du on ps.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id = '" . $Assessment_id . "' AND du.region_id in('" . implode("', '", $Region_id) . "')  
                      GROUP by users) 
                    ) as main GROUP BY region_name";
        }
        
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
    public function get_region_score($assessment_id, $region_id, $report_type, $above_range, $second_range_from, $second_range_to, $third_range_from, $third_range_to, $less_range)
    {
        if ($report_type == 1) {
            $query = "SELECT sum(above_" . $above_range . ") as above_" . $above_range . ", 
                      sum(score_" . $third_range_from . "_" . $third_range_to . ") as score_" . $third_range_from . "_" . $third_range_to . ", 
                      sum(score_" . $second_range_from . "_" . $second_range_to . ") as score_" . $second_range_from . "_" . $second_range_to . ", 
                      sum(less_" . $less_range . ") as less_" . $less_range . ", 
                      region,region_name from(SELECT users,overall_score,
                      CASE WHEN overall_score >= '" . $above_range . "' THEN count(overall_score) ELSE '' END as above_" . $above_range . ",
                      CASE WHEN overall_score >= '" . $third_range_from . "' and overall_score <= '" . $third_range_to . '.99' . "' THEN count(overall_score) ELSE '' END as score_" . $third_range_from . "_" . $third_range_to . ",
                      CASE WHEN overall_score >= '" . $second_range_from . "' and overall_score <= '" . $second_range_to . '.99' . "' THEN count(overall_score) ELSE '' END as score_" . $second_range_from . "_" . $second_range_to . ",
                      CASE WHEN overall_score <= '" . $less_range . '.99' . "' THEN count(overall_score) ELSE '' END as less_" . $less_range . ",
                      region,region_name,assessment
                      FROM (
                      SELECT ROUND( IF(SUM(ps.weighted_score)=0,SUM(ps.score)/count(ps.question_id), 
                      SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                      du.user_id as users , du.region_id as region, rg.region_name as region_name ,am.assessment as assessment                               
                      FROM assessment_mst as am 

                      LEFT JOIN ai_subparameter_score ps ON am.id = ps.assessment_id
                      LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN device_users as du on amu.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id =  '" . $assessment_id . "' AND ps.parameter_type = 'parameter' 
                      and du.region_id IN ('" . implode("', '", $region_id) . "')   
                      GROUP BY du.user_id ORDER BY du.region_id ASC) as main group by users) as main group by region";
        } else if ($report_type == 2) {
            $query = "SELECT sum(above_" . $above_range . ") as above_" . $above_range . ", 
                      sum(score_" . $third_range_from . "_" . $third_range_to . ") as score_" . $third_range_from . "_" . $third_range_to . ", 
                      sum(score_" . $second_range_from . "_" . $second_range_to . ") as score_" . $second_range_from . "_" . $second_range_to . ", 
                      sum(less_" . $less_range . ") as less_" . $less_range . ",  
                      region,region_name from(SELECT users,overall_score, 
                      CASE WHEN overall_score >= '" . $above_range . "' THEN count(overall_score) ELSE '' END as above_" . $above_range . ",
                      CASE WHEN overall_score >= '" . $third_range_from . "' and overall_score <= '" . $third_range_to . '.99' . "' THEN count(overall_score) ELSE '' END as score_" . $third_range_from . "_" . $third_range_to . ",
                      CASE WHEN overall_score >= '" . $second_range_from . "' and overall_score <= '" . $second_range_to . '.99' . "' THEN count(overall_score) ELSE '' END as score_" . $second_range_from . "_" . $second_range_to . ",
                      CASE WHEN overall_score <= '" . $less_range . '.99' . "' THEN count(overall_score) ELSE '' END as less_" . $less_range . ",
                      region,region_name ,assessment
                      FROM (
                      SELECT 
                      ROUND( IF(SUM(ps.weighted_percentage)=0,
                      SUM(ps.score)/count(ps.question_id),SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score, 
                      du.user_id as users , du.region_id as region, rg.region_name as region_name , am.assessment as assessment
                      FROM  assessment_mst as am 
                      LEFT JOIN assessment_results_trans ps ON am.id = ps.assessment_id
                      LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id 
                       AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
                      LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN device_users as du on amu.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id = '" . $assessment_id . "' and du.region_id IN ('" . implode("', '", $region_id) . "') 
                      GROUP BY du.user_id ORDER BY du.region_id ASC) as main group by users) as main group by region;";
        } else {

            $query = "SELECT sum(above_" . $above_range . ") as above_" . $above_range . ", 
                      sum(score_" . $third_range_from . "_" . $third_range_to . ") as score_" . $third_range_from . "_" . $third_range_to . ", 
                      sum(score_" . $second_range_from . "_" . $second_range_to . ") as score_" . $second_range_from . "_" . $second_range_to . ", 
                      sum(less_" . $less_range . ") as less_" . $less_range . ", 
                      region,region_name,assessment from (
                      SELECT users,overall_score, 
                      CASE WHEN overall_score >= '" . $above_range . "' THEN count(overall_score) ELSE '' END as above_" . $above_range . ", 
                      CASE WHEN overall_score >= '" . $third_range_from . "' and overall_score <= '" . $third_range_to . '.99' . "' 
                      THEN count(overall_score) ELSE '' END as score_" . $third_range_from . "_" . $third_range_to . ", 
                      CASE WHEN overall_score >= '" . $second_range_from . "' and overall_score <= '" . $second_range_to . '.99' . "' 
                      THEN count(overall_score) ELSE '' END as score_" . $second_range_from . "_" . $second_range_to . ", 
                      CASE WHEN overall_score <= '" . $less_range . '.99' . "' THEN count(overall_score) ELSE '' END as less_" . $less_range . ", 
                
                      region,region_name,assessment FROM (
                        select round(sum(overall_score)/count(users),2) as overall_score, users, region, region_name,assessment from (
                      (SELECT ROUND( IF(SUM(ps.weighted_score)=0,SUM(ps.score)/count(ps.question_id), 
                      SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score,  
                      du.user_id as users , du.region_id as region, rg.region_name as region_name , am.assessment as assessment
                      FROM assessment_mst as am 

                      LEFT JOIN ai_subparameter_score ps ON am.id = ps.assessment_id
                      LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN device_users as du on amu.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id =  '" . $assessment_id . "' AND ps.parameter_type = 'parameter' 
                      AND du.region_id IN ('" . implode("', '", $region_id) . "')
                      GROUP BY du.user_id ORDER BY du.region_id ASC)
                      
                      UNION ALL
                      
                      ( SELECT 
                      ROUND( IF(SUM(ps.weighted_percentage)=0,
                      SUM(ps.score)/count(ps.question_id),
                      SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score, 
                      du.user_id as users , du.region_id as region, rg.region_name as region_name , am.assessment as assessment
                      FROM  assessment_mst as am 

                      LEFT JOIN assessment_results_trans ps ON am.id = ps.assessment_id
                      LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id 
                      AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
                      LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
                      LEFT JOIN device_users as du on amu.user_id = du.user_id 
                      LEFT JOIN region as rg ON du.region_id = rg.id 
                      WHERE am.id = '" . $assessment_id . "'
                      AND du.region_id IN ('" . implode("', '", $region_id) . "') 
                      GROUP BY du.user_id ORDER BY du.region_id ASC)
                      )as main group by users ORDER BY users ASC
                      ) as main2 group by users) as main 
                      group by region";
        }

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
                    WHERE am.id = '" . $assessment_id . "' AND am.company_id ='" . $Company_id . "' and du.region_id !='0'
                    GROUP BY region_id order by region_id asc";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }

    // Rockstars reps who scored more than 85% 
    public function get_rockstars_user_final_score($company_id, $dtwhere, $dtwhere1, $tempLimit)
    {
        $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt FROM 
                  ( SELECT  overall_score , 
                  IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score,
                  COUNT(users) ,emp_id,users_id,user_name,department,cnt FROM 
                  ( 
                  SELECT overall_score,emp_id,users_id,user_name,department,users,
                  IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt
                  FROM ( SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_score)=0, 
                  SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
                  ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
                  CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                  FROM ai_subparameter_score ps 
                  LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                  LEFT JOIN device_users as c on a.user_id = c.user_id 
                  LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                  $dtwhere $dtwhere1 AND ps.parameter_type = 'parameter' GROUP BY users_id) as main GROUP BY users_id
                
                  UNION ALL 

                  (SELECT overall_score,emp_id,users_id,user_name,department,users,
                  IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt FROM 
                  ( SELECT ps.assessment_id as assessment_id, 
                  ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
                  SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score,  
                  ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
                  CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                  FROM assessment_results_trans ps 
                  LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
                   AND ps.question_id=ats.question_id
                  LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                  LEFT JOIN device_users as c on a.user_id = c.user_id 
                  LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                  $dtwhere $dtwhere1  GROUP BY users_id) as main GROUP BY users_id) ) as main1 
                  GROUP BY users_id
                  ) as main2 WHERE final_score >=85 GROUP by users_id ORDER by user_name $tempLimit";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    // end here

    // At risk resps who scored less than 25%
    public function At_risk_users_final_score($company_id, $dtwhere, $dtwhere1, $tempLimit)
    {
        $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt FROM 
                  ( SELECT  overall_score , 
                  IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score,
                  COUNT(users) ,emp_id,users_id,user_name,department,cnt FROM 
                ( 
                SELECT overall_score,emp_id,users_id,user_name,department,users,
                IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt
                FROM ( SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_score)=0, 
                SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
                ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
                CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                FROM ai_subparameter_score ps 
                LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                LEFT JOIN device_users as c on a.user_id = c.user_id 
                LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                $dtwhere $dtwhere1 AND ps.parameter_type = 'parameter' GROUP BY users_id) as main GROUP BY users_id

                UNION ALL 

                (SELECT overall_score,emp_id,users_id,user_name,department,users,
                IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt FROM 
                ( SELECT ps.assessment_id as assessment_id, 
                ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
                SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score, 
                ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
                CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                FROM assessment_results_trans ps 
                LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
                 AND ps.question_id=ats.question_id
                LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                LEFT JOIN device_users as c on a.user_id = c.user_id 
                LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                $dtwhere $dtwhere1  GROUP BY users_id) as main GROUP BY users_id) ) as main1 
                GROUP BY users_id
                ) as main2 WHERE final_score <=25 GROUP by users_id ORDER by user_name $tempLimit";

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
        $query = "SELECT final_score,emp_id,users_id,user_name,department,cnt FROM 
                   ( SELECT  overall_score , 
                   IF(SUM(cnt) > 1, round(AVG(overall_score),2),  SUM(overall_score)) as final_score,
                   COUNT(users) ,emp_id,users_id,user_name,department,cnt FROM 
                   ( 
                   SELECT overall_score,emp_id,users_id,user_name,department,users,
                   IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt
                   FROM ( SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_score)=0, 
                   SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
                   ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
                   CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                   FROM ai_subparameter_score ps 
                   LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                   LEFT JOIN device_users as c on a.user_id = c.user_id 
                   LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                   $dtWhere $dtOrder AND ps.parameter_type = 'parameter' GROUP BY users_id) as main GROUP BY users_id
                   UNION ALL 
                   (SELECT overall_score,emp_id,users_id,user_name,department,users,
                   IF(FORMAT(IFNULL(AVG(overall_score),0),2) > 0,1,0) AS cnt FROM 
                   ( SELECT ps.assessment_id as assessment_id, 
                   ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
                   SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS overall_score, 
                   ps.user_id as users, c.emp_id as emp_id, c.user_id as users_id,
                   CONCAT(c.firstname,' ',c.lastname) as user_name, c.department as department
                   FROM assessment_results_trans ps 
                   LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
                    AND ps.question_id=ats.question_id
                   LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                   LEFT JOIN device_users as c on a.user_id = c.user_id 
                   LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id 
                   $dtWhere $dtOrder  GROUP BY users_id) as main GROUP BY users_id) ) as main1 
                   GROUP BY users_id
                   ) as main2 WHERE $score GROUP by users_id ORDER by user_name $dtLimit";

        $result = $this->db->query($query);
        $data =  $result->result();
        return $data;
    }
    // end here

    // rockstart and at risk reps ai and manual socre
    public function get_ai_score($assessment_id, $amuser_id)
    {
        $query = "SELECT ai_score,emp_id,users_id,user_name,department,users FROM (
                SELECT ps.assessment_id as assessment_id,ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS ai_score,
                ps.user_id as users,
                c.emp_id as emp_id, c.user_id as users_id,CONCAT(c.firstname,' ',c.lastname) as user_name, 
                c.department as department
                FROM ai_subparameter_score ps 
                LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                LEFT JOIN device_users as c on a.user_id = c.user_id 
                LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
                WHERE 1=1 AND ps.assessment_id ='" . $assessment_id . "' AND ps.user_id  IN ('" . implode("','", $amuser_id) . "')
                AND  ps.parameter_type = 'parameter'
                GROUP by users) as main ORDER by user_name";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_manual_score($assessment_id, $amuser_id)
    {
        $query = "SELECT manual_score,emp_id,users_id,user_name,department FROM (
                    SELECT ps.assessment_id as assessment_id, 
                    ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id), 
                    SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight))  ,2) AS manual_score,
                    ps.user_id as users,
                    c.emp_id as emp_id, c.user_id as users_id,CONCAT(c.firstname,' ',c.lastname) as user_name, 
                    c.department as department
                    FROM assessment_results_trans ps 
                    LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id
                    LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
                    LEFT JOIN device_users as c on a.user_id = c.user_id 
                    LEFT JOIN assessment_mst as am ON ps.assessment_id =am.id
                    WHERE 1=1 AND ps.assessment_id ='" . $assessment_id . "' and ps.user_id IN ('" . implode("','", $amuser_id) . "') 
                    GROUP by users) as main  ORDER by user_name";
        $result = $this->db->query($query);
        return $result->result();
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
    public function get_top_region_score()
    {
        // $query = "SELECT round(sum(overall_score)/count(users),2) as overall_score, users, region, region_name from (
        //         (SELECT ROUND( IF(SUM(ps.weighted_score)=0,SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, du.user_id as users , du.region_id as region, rg.region_name as region_name 
        //         FROM ai_subparameter_score ps 
        //         LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
        //         LEFT JOIN device_users as du on amu.user_id = du.user_id 
        //         LEFT JOIN region as rg ON du.region_id = rg.id 
        //         WHERE ps.parameter_type = 'parameter' GROUP BY du.user_id ) 

        //         UNION ALL 

        //         (SELECT ROUND( IF(SUM(ps.weighted_percentage)=0,SUM(ps.score)/count(ps.question_id),SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, du.user_id as users , du.region_id as region, rg.region_name as region_name 
        //         FROM assessment_results_trans ps 
        //         LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id 
        //         LEFT JOIN device_users as du on amu.user_id = du.user_id 
        //         LEFT JOIN region as rg ON du.region_id = rg.id 
        //         GROUP BY du.user_id) )as main group by region ORDER BY overall_score DESC LIMIT 5";
        $query = "SELECT round(sum(total)/sum(users),2) as overall_score, region_name from (
            (select sum(overall_score) as total, count(users) as users,region_name as region_name from
            (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/ count(ps.question_id),
                SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
             rg.region_name as region_name, ps.user_id as users 
             FROM ai_subparameter_score as ps 
             LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
             LEFT JOIN device_users as c ON ps.user_id = c.user_id 
             LEFT join region as rg on c.region_id =rg.id 
             WHERE ps.parameter_type = 'parameter'  AND c.region_id != '0' GROUP BY users
             )as main group by region_name)
            UNION ALL
            (SELECT sum(overall_score) as total, count(users) as users, region_name from 
            (SELECT ps.assessment_id as assessment_id, ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id),
             SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , rg.region_name as region_name, 
             ps.user_id as users 
             FROM assessment_results_trans ps 
             LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
             AND ps.question_id=ats.question_id
             LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
             LEFT JOIN device_users as c on ps.user_id = c.user_id 
             LEFT join region as rg on c.region_id =rg.id 
             WHERE  c.region_id != '0'
             GROUP BY users ORDER by region_name DESC
             )as main group by region_name)
            ) as main2 GROUP BY region_name ORDER BY overall_Score DESC LIMIT 5";
    //   $query = "SELECT round(sum(total)/sum(users),2) as overall_score, region_name from (
    //     (select sum(overall_score) as total, count(users) as users,region_name as region_name from
    //     (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/ count(ps.question_id),SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score, 
    //      rg.region_name as region_name, ps.user_id as users 
    //      FROM ai_subparameter_score as ps 
    //      LEFT JOIN assessment_allow_users as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
    //      LEFT JOIN device_users as c ON ps.user_id = c.user_id 
    //      LEFT join region as rg on c.region_id =rg.id 
    //      WHERE ps.parameter_type = 'parameter'  AND rg.region_name != 'Null' GROUP BY users
    //      )as main group by region_name)
    //     UNION ALL
    //     (SELECT sum(overall_score) as total, count(users) as users, region_name from 
    //     (SELECT ps.assessment_id as assessment_id, ROUND( IF(SUM(ps.weighted_percentage)=0, SUM(ps.score)/count(ps.question_id),
    //      SUM(ps.weighted_percentage)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score , rg.region_name as region_name, 
    //      ps.user_id as users 
    //      FROM assessment_results_trans ps 
    //      LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id 
    //      AND ps.question_id=ats.question_id
    //      LEFT JOIN assessment_mapping_user as a ON ps.assessment_id=a.assessment_id AND ps.user_id=a.user_id 
    //      LEFT JOIN device_users as c on ps.user_id = c.user_id 
    //      LEFT join region as rg on c.region_id =rg.id 
    //      WHERE  rg.region_name != 'Null'
    //      GROUP BY users ORDER by region_name DESC
    //      )as main group by region_name)
    //     ) as main2 GROUP BY region_name ORDER BY overall_Score DESC LIMIT 5";
        $result = $this->db->query($query);
        $data =  $result->result_array();
        return $data;
    }
}
