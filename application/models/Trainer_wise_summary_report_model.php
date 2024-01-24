<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_wise_summary_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dthaving='', $dtOrder, $dtLimit) {
        
        $query = "SELECT cm.company_name,ar.trainer_id,CONCAT(cu.first_name,' ', cu.last_name) as trainername,
                count(DISTINCT ar.user_id) AS TOTALtrainee,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(DISTINCT ar.topic_id) as TOTALtopic, count(DISTINCT ar.subtopic_id) as TOTALsubtopic,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        LEFT JOIN company_users cu ON cu.userid=ar.trainer_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere
                  group by ar.trainer_id ";
        
        $query_count = $query ." $dthaving $dtOrder ";
        $query .= " $dthaving $dtOrder $dtLimit "; 
//        echo $query;exit;           
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total=count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    
    }
    public function exportToExcel($dtWhere, $dthaving='') {
        $excel_data = "SELECT cm.company_name,ar.trainer_id,CONCAT(cu.first_name,' ', cu.last_name) as trainername,count(DISTINCT ar.user_id) AS TOTALtrainee,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(DISTINCT ar.topic_id) as TOTALtopic, count(DISTINCT ar.subtopic_id) as TOTALsubtopic,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        LEFT JOIN company_users cu ON cu.userid=ar.trainer_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere
                  group by ar.trainer_id ";
        
        $excel_data .= " $dthaving "; 
        $query = $this->db->query($excel_data);
        return $query->result();
    }

}

