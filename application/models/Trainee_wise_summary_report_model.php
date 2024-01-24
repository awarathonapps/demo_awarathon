<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainee_wise_summary_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dthaving='', $dtOrder, $dtLimit) {
        
        $query = "SELECT cm.company_name,ar.user_id,concat(du.firstname,' ',du.lastname) as traineename,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        LEFT JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere
                  group by ar.user_id ";
        
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
        $excel_data = "SELECT cm.company_name,ar.user_id,concat(du.firstname,' ',du.lastname) as traineename,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        LEFT JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere
                  group by ar.user_id ";
        
        $excel_data .= " $dthaving "; 
        $query = $this->db->query($excel_data);
        return $query->result();
    }

}

