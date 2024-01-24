<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Salesforce_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function SalesrDataTableRefresh($assessment_id,$knowledge,$skill,$bussiness) {        
        $query = "SELECT IF(".$knowledge."!='0',IF(SUM(knowledge_accuracy) >= ".$knowledge.",'High','Low'),'Low') as knowledge,
                IF(".$knowledge."!='0',IF(SUM(skill_accuracy) >= ".$skill.",'High','Low'),'Low') as skill,
                IF(".$knowledge."!='0',IF(SUM(bussiness_accuracy) >= ".$bussiness.",'High','Low'),'Low') as bussiness,
                SUM(knowledge_accuracy) as knowledge_result,SUM(skill_accuracy) as skill_result,
                SUM(bussiness_accuracy) as bussiness_result,user_id
                FROM
                ( SELECT IFNULL(FORMAT(avg(art.accuracy),2),0) AS knowledge_accuracy,0 as skill_accuracy, 0 as bussiness_accuracy,art.user_id,pm.category_id
                    FROM assessment_trainer_weights art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                    WHERE art.assessment_id =". $assessment_id." and pm.category_id = 1 
                    GROUP BY art.user_id "; 

            $query .= " UNION ALL

                    SELECT  0 as knowledge_accuracy,IFNULL(FORMAT(avg(art.accuracy),2),0) AS skill_accuracy,0 as bussiness_accuracy,art.user_id,pm.category_id
                    FROM assessment_trainer_weights art
                    LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                    WHERE art.assessment_id =". $assessment_id." and pm.category_id = 2 
                    GROUP BY art.user_id "; 

            $query .= "UNION ALL

                    SELECT 0 AS knowledge_accuracy,0 AS skill_accuracy, IFNULL(FORMAT(ais.input *100/ais.target,2),0) AS bussiness_accuracy, ais.user_id,3 AS category_id
                    FROM assessment_import_sales_sheet ais
                    WHERE ais.assessment_id =". $assessment_id."
                    GROUP BY ais.user_id
                ) as AA
                group by AA.user_id ";
                $result = $this->db->query($query);
        return $result->result();
    }  
    public function get_user($assessment_id) {
        $query = " SELECT ar.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
                    FROM assessment_results ar
                        LEFT JOIN assessment_results_trans art 
                                ON art.result_id=ar.id AND art.assessment_id=ar.assessment_id AND art.user_id=ar.user_id
                        LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
                        LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                    WHERE ar.assessment_id = $assessment_id AND art.question_id !='' 
                    GROUP BY du.user_id ORDER BY traineename";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_sales_status($knowledge,$skill,$bussiness){
        $query = " SELECT status FROM assessment_sales_status
                   WHERE knowledge='".$knowledge."' AND skill='".$skill."' AND sales='".$bussiness."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function LoadParameterQuestions($assessment_id) {
        $query = " SELECT art.question_id ,aq.question FROM assessment_trans art "
                . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
                . " where art.assessment_id =" . $assessment_id ." order by art.id";
        $query = $this->db->query($query);
        return $query->result();
    } 
}
