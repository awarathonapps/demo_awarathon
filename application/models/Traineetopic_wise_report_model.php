<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Traineetopic_wise_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag, $report_type) {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.user_id,du.emp_id,concat(du.firstname,' ',du.lastname) as traineename, w.workshop_name,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
                  wt.workshop_type,wr.region_name as workshop_region,rg.region_name as trainee_region, 
                  wsr.description as workshop_subregion,wst.description as workshop_subtype,dt.description as designation ";
        if ($report_type == 1) {
            $query .=" ,qt.description as title ";
        } else if ($report_type == 2) {
            $query .=", qt.title ";
        } else {
            $query .=", count(distinct ar.questionset_id ) as title ";
        }
        $query .= "
                from atom_results ar	
                LEFT JOIN device_users du ON du.user_id=ar.user_id
                LEFT JOIN region rg on rg.id=du.region_id
                LEFT JOIN company cm ON cm.id=ar.company_id 
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT JOIN region wr on wr.id=w.region 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id ";
                
        if ($report_type == 1) {
            $query .=" LEFT JOIN question_topic qt ON qt.id=ar.topic_id ";
        } else if ($report_type == 2) {
            $query .=" LEFT JOIN question_set qt ON qt.id=ar.questionset_id ";
        }
        $query .= "  $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .=" group by ar.user_id,ar.workshop_id ";
        if ($report_type == 1) {
            $query .=", ar.topic_id ";
        } else if ($report_type == 2) {
            $query .=", ar.questionset_id ";
        }
        $query .=" $dthaving ";
        $query_count = $query;
        $query .= " $dtOrder $dtLimit ";
//        echo $query;exit;           
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function exportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag, $report_type = 1) {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.user_id,du.emp_id,concat(du.firstname,' ',du.lastname) as traineename, w.workshop_name,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
                  wt.workshop_type,wr.region_name as workshop_region,rg.region_name as trainee_region, 
                  wsr.description as workshop_subregion,wst.description as workshop_subtype,dt.description as designation ";
        if ($report_type == 1) {
            $excel_data .=" ,qt.description as title ";
        } else if ($report_type == 2) {
            $excel_data .=", qt.title ";
        } else {
            $excel_data .=", count(distinct ar.questionset_id ) as title ";
        }
        $excel_data .= " from atom_results ar	
                LEFT JOIN device_users du ON du.user_id=ar.user_id
                LEFT join region rg on rg.id=du.region_id
                LEFT JOIN company cm ON cm.id=ar.company_id 
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT join region wr on wr.id=w.region
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id ";
        if ($report_type == 1) {
            $excel_data .=" LEFT JOIN question_topic qt ON qt.id=ar.topic_id ";
        } else if ($report_type == 2) {
            $excel_data .=" LEFT JOIN question_set qt ON qt.id=ar.questionset_id ";
        }
        $excel_data .= "  $dtWhere ";
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $excel_data .=" group by ar.user_id,ar.workshop_id ";
        if ($report_type == 1) {
            $excel_data .=", ar.topic_id ";
        } else if ($report_type == 2) {
            $excel_data .=", ar.questionset_id ";
        }
        $excel_data .=" $dthaving order by workshop_name,traineename ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }

    public function workshopwise_data($company_id, $workshop_id) {
        $LcSqlStr = "SELECT qt.id,qt.description as topic_name FROM workshop_questions wq LEFT JOIN question_topic qt ON qt.id=wq.topic_id and qt.company_id=wq.company_id "
                . " where wq.company_id=" . $company_id . " and wq.workshop_id=" . $workshop_id . " group by qt.id ";

        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }

}
