<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supervisor_comparison_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getComparisonData($company_id, $region_id, $workshoptype_id, $trainer_id, $workshop_id,$wsubtype_id='',$wsubregion_id='', $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "select ifnull(r.region_name,'Not Played') as region_name,
                ifnull(if($trainer_id !=0,cu.first_name,'ALL'),'Not Played') as first_name,
                ifnull(wtm.workshop_type,'Not Played') as workshop_type_name,
                ifnull(if($workshop_id !=0,w.workshop_name,'ALL'),'Not Played')as workshop_name,				
		IFNULL(format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ),0) as avgce,		
		wsr.description as workshop_subregion,wst.description as workshop_subtype
		from trainer_result as wr left join company_users cu
		on cu.userid = wr.trainer_id left join region r on r.id = wr.region_id
		left join workshoptype_mst wtm on wtm.id = wr.workshop_type
		left join workshop w on w.id = wr.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
		where wr.company_id=$company_id ";
        if ($region_id != 0) {
            $query .=" and wr.region_id=$region_id";
        }
        if ($workshoptype_id != 0) {
            $query .=" and wr.workshop_type=$workshoptype_id";
        }
        if ($workshop_id != 0) {
            $query .=" and wr.workshop_id=$workshop_id";
        }
        if ($wsubtype_id != "") {
                $query .= " and w.workshopsubtype_id  = " . $wsubtype_id;
        }
        if ($wsubregion_id != "") {
                 $query .= " and w.workshopsubregion_id  = " . $wsubregion_id;
        }
        if (!$WRightsFlag) {
            $query .= " AND wr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }

        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (wr.trainer_id = $login_id OR wr.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND wr.trainer_id= " . $trainer_id;
        }
//        $query .=" group by wr.region_id,wr.workshop_type ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        return $result->row();
    }
	public function gethighest_lowest_ce($company_id, $region_id, $workshoptype_id, $trainer_id, $workshop_id,$wsubtype_id='',$wsubregion_id='', $RightsFlag, $WRightsFlag){
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT IFNULL(FORMAT(MAX(avgce),2),0) AS highestce, IFNULL(FORMAT(MIN(avgce),2),0) AS lowestce
                    from (select 
                        sum(wr.post_correct)*100/sum(wr.post_total_questions) - sum(wr.pre_correct)*100/sum(wr.pre_total_questions) as avgce		
                        from trainer_result as wr left join company_users cu
                        on cu.userid = wr.trainer_id left join region r on r.id = wr.region_id
                        left join workshoptype_mst wtm on wtm.id = wr.workshop_type
                        left join workshop w on w.id = wr.workshop_id
                        LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                        LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                        where wr.company_id=$company_id ";
                    if ($region_id != 0) {
                        $query .=" and wr.region_id=$region_id";
                    }
                    if ($workshoptype_id != 0) {
                        $query .=" and wr.workshop_type=$workshoptype_id";
                    }
                    if ($workshop_id != 0) {
                        $query .=" and wr.workshop_id=$workshop_id";
                    }
                    if ($wsubtype_id != "") {
                            $query .= " and w.workshopsubtype_id  = " . $wsubtype_id;
                    }
                    if ($wsubregion_id != "") {
                             $query .= " and w.workshopsubregion_id  = " . $wsubregion_id;
                    }
                    if (!$WRightsFlag) {
                        $query .= " AND wr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
                    }

                    if ($trainer_id == "0") {
                        if (!$RightsFlag) {
                            $query .= " AND (wr.trainer_id = $login_id OR wr.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                        }
                    } else {
                        $query .= " AND wr.trainer_id= " . $trainer_id;
                    }
                    $query .=" group by wr.workshop_id )A ";
//                echo $query;
//                exit;
                $result = $this->db->query($query);
                return $result->row();
    }
    public function getParticipantList($company_id, $region_id, $workshoptype_id, $trainer_id, $workshop_id,$wsubtype_id, $wsubregion_id, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "select count(distinct a.user_id) as total_users,count(distinct a.workshop_id) as total_workshop FROM atom_results as a"
                . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id "
                . " LEFT JOIN workshop as wr ON wr.id=a.workshop_id where a.company_id=$company_id AND wtu.tester_id IS NULL ";
        if ($region_id != 0) {
            $query .=" and wr.region=$region_id";
        }
        if ($workshoptype_id != 0) {
            $query .=" and wr.workshop_type=$workshoptype_id";
        }
        if ($workshop_id != 0) {
            $query .=" and a.workshop_id=$workshop_id";
        }
        if ($wsubtype_id != "") {
                $query .= " and wr.workshopsubtype_id  = " . $wsubtype_id;
        }
        if ($wsubregion_id != "") {
                 $query .= " and wr.workshopsubregion_id  = " . $wsubregion_id;
        }
        if (!$WRightsFlag) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $query .= " AND a.trainer_id= " . $trainer_id;
        }
        $result = $this->db->query($query);
        $TotalUsers = $result->row();
        return $TotalUsers;
    }

}
