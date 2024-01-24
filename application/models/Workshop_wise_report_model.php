<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Workshop_wise_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function LoadDataTable($dtWhere, $dthaving='', $dtOrder, $dtLimit) {
        
        $query = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.questionset_id) as questionset,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result

            from atom_results ar

            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.company_id=ar.company_id  
            LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere
            group by ar.workshop_id";
        
        $query_count = $query ." $dthaving $dtOrder ";
        $query .= " $dthaving $dtOrder $dtLimit "; 
//          echo $query;exit;           
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
        $excel_data = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.questionset_id) as questionset,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result

            from atom_results ar

            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.company_id=ar.company_id 
            LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere 
            group by ar.workshop_id";
        
        $excel_data .= " $dthaving "; 
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    public function getWorkshopList($Company_id = "", $region_id = "") {
        $lcSqlStr = "select a.workshop_id,b.workshop_name FROM workshop_registered_users a "
                . "LEFT JOIN workshop as b "
                . "ON b.id=a.workshop_id where 1=1";
        if ($Company_id != "") {
            $lcSqlStr .=" AND b.company_id=" . $Company_id;
        }
        if ($region_id != "") {
            $lcSqlStr .=" AND b.region=" . $region_id;
        }
        $lcSqlStr .=" group by a.workshop_id order by b.start_date desc,b.workshop_name ";
        //echo $lcSqlStr;
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }

}

