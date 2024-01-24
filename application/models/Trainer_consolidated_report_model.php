<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_consolidated_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit ,$dtHaving) {
        
        $query = " SELECT ar.id,c.company_name,w.workshop_name,r.region_name,wt.workshop_type,ar.user_id,
		 CONCAT(cu.first_name,' ',cu.last_name) AS trainername,qt.description AS topicname, 
		 qst.description AS subtopicname , count( ar.question_id) as total_question,
		 count(distinct ar.user_id) as total_trainee_played,(count( ar.question_id) * count(distinct ar.user_id))as total_question_played,
		 sum(ar.is_correct) as total_correct_ans ,
		 FORMAT(IFNULL((sum(ar.is_correct) * 100 / (count( ar.question_id) * count(distinct ar.user_id)) ),0),2) as result		 	 			
				FROM atom_results ar
						INNER JOIN company c ON c.id=ar.company_id
						INNER JOIN workshop w ON w.id=ar.workshop_id
						INNER JOIN region r ON r.id = w.region
						INNER JOIN workshoptype_mst wt ON wt.id = w.workshop_type
						INNER JOIN company_users cu ON cu.userid=ar.trainer_id
						INNER JOIN question_topic qt ON qt.id=ar.topic_id
						INNER JOIN question_subtopic qst ON qst.id=ar.subtopic_id ";  
        
            
        $query_count = $query ." $dtWhere group by ar.company_id,ar.workshop_id,ar.topic_id,ar.subtopic_id,ar.trainer_id $dtHaving $dtOrder ";
        $query .= " $dtWhere  ";
        
        $query .= " group by ar.company_id,ar.workshop_id,ar.topic_id,ar.subtopic_id,ar.trainer_id $dtHaving $dtOrder $dtLimit "; 
             
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total=count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }
    public function exportToExcel($exportWhere='',$exportHaving='') {
        $excel_data = " SELECT ar.id,c.company_name,w.workshop_name,r.region_name,wt.workshop_type,ar.user_id,
		 CONCAT(cu.first_name,' ',cu.last_name) AS trainername,qt.description AS topicname, 
		 qst.description AS subtopicname , count( ar.question_id) as total_question,
		 count(distinct ar.user_id) as total_trainee_played,(count( ar.question_id) * count(distinct ar.user_id))as total_question_played,
		 sum(ar.is_correct) as total_correct_ans ,
		 FORMAT(IFNULL((sum(ar.is_correct) * 100 / (count( ar.question_id) * count(distinct ar.user_id)) ),0),2) as result		 	 			
				FROM atom_results ar
						INNER JOIN company c ON c.id=ar.company_id
						INNER JOIN workshop w ON w.id=ar.workshop_id
						INNER JOIN region r ON r.id = w.region
						INNER JOIN workshoptype_mst wt ON wt.id = w.workshop_type
						INNER JOIN company_users cu ON cu.userid=ar.trainer_id
						INNER JOIN question_topic qt ON qt.id=ar.topic_id
						INNER JOIN question_subtopic qst ON qst.id=ar.subtopic_id "; 
        $excel_data .= " $exportWhere group by ar.company_id,ar.workshop_id,w.region,ar.topic_id,ar.subtopic_id,ar.trainer_id $exportHaving ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    
    
}
