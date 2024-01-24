<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Consolidated_reports_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        
        $query = " select ar.id,c.company_name,w.workshop_name,ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,concat('(',ar.question_id,') ',q.question_title) as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,
                    CASE ar.correct_answer
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as correct_answer,
                    CASE ar.option_clicked
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as user_answer,ar.is_correct,
                    CASE 1
                                        WHEN ar.is_correct THEN 'Correct'
                                        WHEN ar.is_wrong THEN 'Wrong'
                                        WHEN ar.is_timeout THEN 'Time Out'                   
                                        ELSE ''
                                        END as question_result,                    
                    DATE_FORMAT(ar.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(ar.end_dttm, '%d/%m/%Y %H:%i:%s') as end_dttm,
                    ar.seconds,ar.timer from atom_results ar
                    inner join company c on c.id=ar.company_id
                    inner join workshop w on w.id=ar.workshop_id
                    inner join question_set qs on qs.id=ar.questionset_id
                    inner join company_users cu on cu.userid=ar.trainer_id
                    inner join question_topic qt on qt.id=ar.topic_id
                    inner join question_subtopic qst on qst.id=ar.subtopic_id
                    inner join questions q on q.id=ar.question_id
                    inner join device_users du on du.user_id=ar.user_id
                    ";                                 
            
        $query_count = $query ." $dtWhere $dtOrder ";
        $query .= " $dtWhere $dtOrder $dtLimit ";
             
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total=count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }
    public function exportToExcel($exportWhere='') {
        $excel_data = "select ar.id,c.company_name,w.workshop_name,ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,concat('(',ar.question_id,') ',q.question_title) as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,
                    CASE ar.correct_answer
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as correct_answer,
                    CASE ar.option_clicked
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as user_answer,ar.is_correct,
                    CASE 1
                                        WHEN ar.is_correct THEN 'Correct'
                                        WHEN ar.is_wrong THEN 'Wrong'
                                        WHEN ar.is_timeout THEN 'Time Out'                   
                                        ELSE ''
                                        END as question_result,                    
                    DATE_FORMAT(ar.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(ar.end_dttm, '%d/%m/%Y %H:%i:%s') as end_dttm,ar.seconds 
                    from atom_results ar
                    inner join company c on c.id=ar.company_id
                    inner join workshop w on w.id=ar.workshop_id
                    inner join question_set qs on qs.id=ar.questionset_id
                    inner join company_users cu on cu.userid=ar.trainer_id
                    inner join question_topic qt on qt.id=ar.topic_id
                    inner join question_subtopic qst on qst.id=ar.subtopic_id
                    inner join questions q on q.id=ar.question_id 
                    inner join device_users du on du.user_id=ar.user_id
                    "; 
        $excel_data .= " $exportWhere";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    public function get_userData($sessions_id,$workshop_id,$company_id) {  

            $querystr ="Select DISTINCT ar.user_id,concat(du.firstname,' ',du.lastname) as username "
                    . " from atom_results ar "
                    . " inner join device_users du on du.user_id=ar.user_id where "
                    . " ar.company_id=".$company_id." and  ar.workshop_id=".$workshop_id;
                        
        $result = $this->db->query($querystr);        
        return $result->result();
    }
    
}
