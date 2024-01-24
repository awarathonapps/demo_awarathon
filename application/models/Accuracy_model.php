<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Accuracy_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // FOR CHARTS AND COUNT
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

       // Competency_understanding_graph get score
       public function LastExpiredAssessment($CurrentDate)
       {
            $result = "SELECT id, assessment,report_type FROM assessment_mst am WHERE end_dttm <='".$CurrentDate."' 
                       ORDER BY end_dttm DESC LIMIT 1 ";
            $query = $this->db->query($result);
            $row = $query->result_array();
            return $row;
       }
       public function getCompetencyscore($Assessment_id,$Report_Type)
       {
        if($Report_Type==1){
            $query="SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                    SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score ,
                    ps.user_id as users
                    FROM ai_subparameter_score ps 
                    LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
                    -- LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
                    WHERE amu.assessment_id = '".$Assessment_id."' AND parameter_type = 'parameter'
                    GROUP BY ps.user_id ORDER BY users ASC";
        } else if($Report_Type==2) {
            $query="SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                    SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score  ,
                    ps.user_id as users
                    FROM assessment_results_trans AS ps 
                    LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id 
                    WHERE amu.assessment_id = '".$Assessment_id."' 
                    GROUP BY ps.user_id ORDER BY users ASC";
        } else {
            $query = "SELECT (
                      CASE WHEN overall_score=0 THEN SUM(overall_score) ELSE round(AVG(overall_score),2) end) as overall_score, users  
                      FROM(   
                      (SELECT ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), 
                      SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score ,ps.user_id as users
                      FROM ai_subparameter_score ps 
                      LEFT JOIN assessment_allow_users as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
                    --   LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id AND ps.user_id=amu.user_id
                      WHERE amu.assessment_id = '".$Assessment_id."' AND parameter_type = 'parameter'
                      GROUP BY ps.user_id ORDER BY overall_score ASC) 
                      UNION ALL 
                      SELECT (CASE WHEN overall_score=0 THEN '0' ELSE overall_score end) as overall_score, users  FROM  
                      (SELECT  ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), 
                      SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score  ,ps.user_id as users
                      FROM assessment_results_trans AS ps 
                      LEFT JOIN assessment_mapping_user as amu ON ps.assessment_id=amu.assessment_id 
                      WHERE amu.assessment_id = '".$Assessment_id."' 
                      GROUP BY ps.user_id ORDER BY overall_score ASC) as main
                      ) as main2 GROUP BY users";
            }
            $result = $this->db->query($query);
            $data = $result->result_array();
            return $data;           
        }
        public function get_name($Assessment_id) {
            $LcSqlStr = "SELECT assessment FROM assessment_mst am WHERE id='".$Assessment_id."' ";
            $query = $this->db->query($LcSqlStr);
            $row = $query->result_array();
            return $row;
        }
    //end here 
}