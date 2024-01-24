<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment_minute_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
   public function getassessment_minutechart_data($WeekStartDate,$WeekEndDate){
        $lcsqlstr  = "SELECT total_users,IFNULL(CONCAT((FLOOR(SUM(total_duration)/60)),'.',(SUM(total_duration)%60)),0) AS utilize_duration "
                . " FROM (SELECT count(DISTINCT a.user_id) as total_users,SUM(c.response_timer) AS total_duration "
                . " 
					FROM assessment_attempts  as a 
					LEFT JOIN assessment_trans as b ON b.assessment_id=a.assessment_id
					LEFT JOIN assessment_question c ON c.id=b.question_id 
					LEFT JOIN assessment_mst am ON am.id=a.assessment_id 
					";
					$lcsqlstr .="  WHERE a.user_id !='' AND date(am.addeddate) BETWEEN '$WeekStartDate' AND '$WeekEndDate') as a";
					
        $result = $this->db->query($lcsqlstr);
        return $result->row();
    }
    public function LoadAssessmentMinuteData($dtWhere, $dtOrder, $dtLimit) {
        $query  = "SELECT a.assessment,total_users,CONCAT((FLOOR(SUM(total_duration)/60)),'.',(SUM(total_duration)%60)) AS 						utilize_duration,SUM(total_duration) as video_order " 
                . " FROM (SELECT a.assessment_id,count(DISTINCT a.user_id) as total_users,SUM(c.response_timer) AS total_duration,am.assessment "
                . "
					FROM assessment_attempts  as a 
					LEFT JOIN assessment_trans as b ON b.assessment_id=a.assessment_id
					LEFT JOIN assessment_question c ON c.id=b.question_id 
					LEFT JOIN assessment_mst am ON am.id=a.assessment_id ";
					$query .="  $dtWhere group by a.assessment_id ) as a group by a.assessment_id";	
	
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
    public function exportToExcel($dtWhere) {
        $excel_data  = "SELECT a.assessment,total_users,CONCAT((FLOOR(SUM(total_duration)/60)),'.',(SUM(total_duration)%60)) AS 						utilize_duration,SUM(total_duration) as video_order " 
                . " FROM (SELECT a.assessment_id,count(DISTINCT a.user_id) as total_users,SUM(c.response_timer) AS total_duration,am.assessment "
                . " 
					FROM assessment_attempts  as a 
					LEFT JOIN assessment_trans as b ON b.assessment_id=a.assessment_id
					LEFT JOIN assessment_question c ON c.id=b.question_id 
					LEFT JOIN assessment_mst am ON am.id=a.assessment_id ";
					$excel_data .="  $dtWhere group by a.assessment_id ) as a group by a.assessment_id order by video_order desc";
     
        $query = $this->db->query($excel_data);
        return $query->result();
    }
}
