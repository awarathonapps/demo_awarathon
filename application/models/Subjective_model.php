<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Subjective_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT ar.workshop_id,cm.company_name,ar.user_id, CONCAT(du.firstname,' ',du.lastname) AS traineename, COUNT(distinct ar.id) AS played_que, tr.region_name, 
                (COUNT(wqpr.id + wqpo.id)) AS total_que, dt.description AS designation
                FROM atom_results ar
                INNER JOIN device_users du ON du.user_id=ar.user_id
                LEFT JOIN region tr ON tr.id=du.region_id
                INNER JOIN company cm ON cm.id=ar.company_id
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT JOIN workshoptype_mst AS wt ON wt.id=w.workshop_type
                LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
                LEFT JOIN workshop_questionset_pre wqpr ON wqpr.workshop_id = ar.workshop_id
                LEFT JOIN workshop_questionset_post wqpo ON wqpo.workshop_id = ar.workshop_id $dtWhere ";
//            if($dtWhere!=""){
//                $query .=" AND du.istester=0 ";
//            }else{
//                $query .=" WHERE du.istester=0 ";
//            }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " group by ar.user_id order by ar.user_id";

//        $query_count = $query . " $dthaving ";

//        $query .= " $dthaving $dtOrder $dtLimit ";
        
//        if($dtOrder ==""){
//            $query .= " order by result desc  ";
//        }
        //$query .= "  $dtLimit ";
        $result = $this->db->query($query);
        $data = $result->result();
//        $data['dtPerPageRecords'] = $result->num_rows();

//        $Countset = $this->db->query($query_count);
//        $data['dtTotalRecords'] = $Countset->num_rows();
        return $data;
    }
   
   
}
