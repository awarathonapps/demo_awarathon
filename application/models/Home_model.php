<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class home_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function get_all_assessment()
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
                FROM assessment_mst am 
                LEFT JOIN assessment_report_type as art on art.id=am.report_type
				WHERE am.status = 1
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function assessment_started($StartStrDt = '', $EndDate = '', $Day_type, $Company_id, $manager_id = '')
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "'";
        }
        if ($manager_id != '') {
            $cond .= " AND amu.trainer_id = '" . $manager_id . "' ";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as result,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.start_dttm) as wmonth,";
        }
        $query .= "DATE_FORMAT(am.start_dttm,'%d') 
                     wday FROM assessment_mst am 
                     left join assessment_mapping_user as amu on amu.assessment_id = am.id
                     WHERE  am.status =1 AND am.company_id='" . $Company_id . "' $cond ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(am.start_dttm)";
        } else {
            $query .= "GROUP BY month(am.start_dttm)";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();

        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->result;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        }
    }
    // THIS FUNCTION FOR LAST 30 DAYS DATA & FOR 60 DAYS
    public function assessment_index_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id = '')
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        if ($manager_id != '') {
            $cond .= " AND amu.trainer_id = '" . $manager_id . "' ";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as total,month(am.start_dttm) as wmonth
                             FROM assessment_mst am
                             left join assessment_mapping_user as amu on amu.assessment_id = am.id
                             WHERE am.status =1 AND am.company_id='" . $Company_id . "'
                             $cond GROUP BY month(am.start_dttm)";

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }

    // THIS FUNCTION IS FOR STARTED COUNT MONTHS WISE
    public function total_assessment_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id = '')
    {

        $query = "SELECT IFNULL(count(distinct am.id),0) as currentmonth FROM assessment_mst am 
                  left join assessment_mapping_user as amu on amu.assessment_id = am.id
                  WHERE  am.status =1 AND am.company_id='" . $Company_id . "' AND am.start_dttm BETWEEN '$monthstartdate' AND '$monthenddate' ";
        if ($manager_id != '') {
            $query .= " AND amu.trainer_id = '$manager_id' ";
        }
        $query .= " GROUP BY month(am.start_dttm)";

        $query1 = "SELECT IFNULL(count(distinct am.id),0) as months FROM assessment_mst am 
                  left join assessment_mapping_user as amu on amu.assessment_id = am.id
                  WHERE  am.status =1 AND am.company_id='" . $Company_id . "' AND am.start_dttm  BETWEEN '$lastmonthdate' AND '$lastmonthenddate' ";
        if ($manager_id != '') {
            $query1 .= " AND amu.trainer_id = '$manager_id' ";
        }
        $query1 .= " GROUP BY month(am.start_dttm)";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // END HERE
    // ASSESSMENT COMPLETED COUNT AND MONTHLY COUNT START HERE
    public function assessment_index_end($StartStrDt, $EndDate, $Day_type, $Company_id, $manager_id = '')
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND date(am.end_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "'";
        }
        if ($manager_id != '') {
            $cond .= " AND amu.trainer_id = '" . $manager_id . "' ";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as result,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.end_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.end_dttm) as wmonth,";
        }
        $query .= "DATE_FORMAT(am.end_dttm,'%d') 
                     wday FROM assessment_mst am  
                     left join assessment_mapping_user as amu on amu.assessment_id = am.id
                     WHERE  am.status =1 AND am.company_id='" . $Company_id . "' $cond ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(am.end_dttm)";
        } else {
            $query .= "GROUP BY month(am.end_dttm)";
        }

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->result;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        }
    }

    // THIS FUNCTION FOR LAST 30 DAYS DATA & FOR 60 DAYS
    public function assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id = '')
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.end_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        if ($manager_id != '') {
            $cond .= " AND amu.trainer_id = '" . $manager_id . "' ";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as total,month(am.end_dttm) as wmonth
                          FROM assessment_mst am  
                          left join assessment_mapping_user as amu on amu.assessment_id = am.id 
                          WHERE am.status =1 AND am.company_id='" . $Company_id . "'
                          $cond GROUP BY month(am.end_dttm)";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }

    // Raps Map User Start
    public function get_raps_mapped_user($StartDate, $EndDate, $Day_type, $Company_id, $manager_id = '')
    {
        $ResultArray = array();
        $PeriodArray = array();
        $cond = "";
        $mng = "";
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "' ";
        }
        if ($manager_id != '') {
            $cond .= " AND amu.trainer_id = '" . $manager_id . "' ";
            $mng = " left join assessment_mapping_user as amu on amu.assessment_id = am.id ";
        }
        $query = "SELECT DISTINCT count(aau.user_id) as result ,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.start_dttm) as wmonth,";
        }
        $query .= "DATE_FORMAT(am.start_dttm,'%d') wday FROM assessment_allow_users as aau 
                    left join assessment_mst as am on aau.assessment_id = am.id ".$mng." 
                    LEFT join device_users as du on aau.user_id=du.user_id
                    WHERE  am.status =1 AND du.istester=0 AND am.company_id='" . $Company_id . "' $cond ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(am.start_dttm)";
        } else {
            // $query .= "GROUP BY month(am.start_dttm)";
            $query .= "GROUP BY wmonth";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();

        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->result;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        }
    }
    //last 30 and 60 day rap_map users
    public function  get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id, $manager_id = '')
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        if ($manager_id != '') {
            $cond .= " AND amu.trainer_id = '" . $manager_id . "' ";
        }
        $query = "SELECT DISTINCT count(aau.user_id) as total ,month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday 
                    FROM assessment_allow_users as aau
                    left join assessment_mst as am on aau.assessment_id = am.id
                    left join assessment_mapping_user as amu on amu.assessment_id = am.id
                    LEFT join device_users as du on aau.user_id=du.user_id
                    WHERE am.STATUS = '1'AND du.istester=0  AND am.company_id='" . $Company_id . "'
                         $cond GROUP BY month(am.start_dttm)";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }
    // Rep Map Total Users
    public function rap_total_user_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id = '')
    {

        $query = "SELECT DISTINCT count(aau.user_id) as currentmonth FROM assessment_allow_users as aau
                    left join assessment_mst as am on aau.assessment_id = am.id
                    left join assessment_mapping_user as amu on amu.assessment_id  = am.id
                    LEFT join device_users as du on aau.user_id=du.user_id
                    WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
                    AND am.start_dttm BETWEEN '$monthstartdate' AND '$monthenddate' ";
        if ($manager_id != '') {
            $query .= " AND amu.trainer_id = '$manager_id' ";
        }
        $query .= " GROUP BY month(am.start_dttm) ";


        $query1 = "SELECT DISTINCT count(aau.user_id) as months FROM assessment_allow_users as aau
                    left join assessment_mst as am on aau.assessment_id = am.id
                    left join assessment_mapping_user as amu on amu.assessment_id  = am.id
                    LEFT join device_users as du on aau.user_id=du.user_id
                    WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
                    AND am.start_dttm BETWEEN '$lastmonthdate' AND '$lastmonthenddate' ";
        if ($manager_id != '') {
            $query1 .= " AND amu.trainer_id = '$manager_id' ";
        }
        $query1 .= " GROUP BY month(am.start_dttm) ";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    public function LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "  SELECT am.id,am.company_id,am.assessment_type,                     
                    am.assessment,at.description AS assessment_type, art.description as report_type, 
                    IF(is_situation=1,'Situation','Question') AS question_type, 
                    am.status,DATE_FORMAT(am.start_dttm,'%d-%m-%Y %H:%i') AS start_dttm, 
                    DATE_FORMAT(am.end_dttm,'%d-%m-%Y %H:%i') AS end_dttm,
					IFNULL(ac.show_reports,1) as show_reports,IFNULL(ac.show_dashboard,0) as show_dashboard,IFNULL(ac.show_pwa,0) as show_pwa, IFNULL(ac.show_ranking,0) as show_ranking, count(DISTINCT ar.question_id) as que_mapped
                FROM assessment_mst am
                        LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
                        LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id
                        LEFT JOIN assessment_report_type as art on art.id=am.report_type
						LEFT JOIN assessment_type as at ON at.id = am.assessment_type
                        LEFT JOIN assessment_trans ar ON ar.assessment_id = am.id 
						LEFT JOIN ai_cronreports ac ON ac.assessment_id=am.id ";
        $query .= " $dtWhere GROUP BY am.id $dtOrder limit 5";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        
        $query = " SELECT  am.id as total FROM assessment_mst am "
                . " LEFT JOIN assessment_mapping_user amu ON amu.assessment_id = am.id "
                . " LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id "
                . " LEFT JOIN assessment_type at ON at.id = am.assessment_type"
                // . " LEFT JOIN assessment_results ar ON ar.assessment_id = atr.assessment_id "
                . " $dtWhere GROUP BY am.id ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array)$data_array);
        return $data;
    }

    //KRISHNA -- Trinity - Show trinity assessment users
    public function getUserCount($company_id, $assessment_ids, $manager_id = ''){
        $query = "SELECT SUM(mapped) as mapped, sum(played) as played, id as assessment_id FROM 
            (SELECT COUNT(DISTINCT aau.id) as mapped,0 as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
            LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
            LEFT JOIN device_users du ON du.user_id=aau.user_id 
            WHERE am.company_id = '" . $company_id . "' and am.id IN (" . implode(',', $assessment_ids) . ") ";
        if ($manager_id != '') {
            $query .= " AND  amu.trainer_id = '$manager_id' ";
        }
        $query .= "AND du.istester=0 GROUP BY am.id
                UNION ALL
            SELECT 0 as mapped,COUNT(DISTINCT ar.user_id) as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
            LEFT JOIN assessment_results ar ON am.id=ar.assessment_id 
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
            WHERE am.company_id = '" . $company_id . "' and am.id IN (" . implode(',', $assessment_ids) . ") ";
        if ($manager_id != '') {
            $query .= " AND  amu.trainer_id = '$manager_id' ";
        }
        $query .= " AND du.istester=0 GROUP BY am.id
            UNION ALL
            SELECT 0 as mapped,COUNT(DISTINCT ar.user_id) as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
            LEFT JOIN trinity_results ar ON am.id=ar.assessment_id 
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
            WHERE am.company_id = '" . $company_id . "' and am.id IN (" . implode(',', $assessment_ids) . ") ";
        if ($manager_id != '') {
            $query .= " AND  amu.trainer_id = '$manager_id' ";
        }
        $query .= " AND du.istester=0 GROUP BY am.id)
        as main GROUP BY assessment_id ORDER BY assessment_id DESC";
		$result = $this->db->query($query);
        return $result->result();
    }
    public function getVideoCount_old($company_id, $assessment_ids, $manager_id = '')
    {
        $query = "SELECT count(*) as total_video_processsed, am.id as assessment_id FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
                LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
				WHERE am.status=1 and ai.company_id='" . $company_id . "' ";
        if ($manager_id != '') {
            $query .= " AND  amu.trainer_id = '$manager_id' ";
        }
        $query .= " AND ai.task_status = 1 AND am.id IN (" . implode(',', $assessment_ids) . ") AND du.istester=0 
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
    //KRISHNA -- HOME PAGE VIDEO PROCESSED AND UPLOADED COUNT MISMATCH WITH JARVIS PAGE
    public function getVideoCount($company_id, $assessment_ids, $manager_id = '')
    {
        $query = "SELECT count(*) as total_video_processsed, am.id as assessment_id FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id ";
        if ($manager_id != '') {
            $query .= " LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id ";
            $query .= " WHERE am.status=1 and ai.company_id='" . $company_id . "' AND  amu.trainer_id = '$manager_id' ";
        }else{
            $query .= " WHERE am.status=1 and ai.company_id='" . $company_id . "' ";
        }
        $query .= " AND ai.task_status = 1 AND am.id IN (" . implode(',', $assessment_ids) . ") AND du.istester=0 
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getUploadedVideos_old($company_id, $assessment_ids, $manager_id = '')
    {
        $query = "SELECT count(*) as total_video_uploaded,am.id as assessment_id FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id
                LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id
				WHERE am.status=1 and ar.company_id = '" . $company_id . "' AND ar.assessment_id IN (" . implode(',', $assessment_ids) . ") ";
        if ($manager_id != '') {
            $query .= " AND  amu.trainer_id = '$manager_id' ";
        }
        $query .= " AND ar.ftp_status=1 AND du.istester=0
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }

    //KRISHNA -- Trinity - Show trinity assessment users
    public function getUploadedVideos($company_id, $assessment_ids, $manager_id = '')
    {
        $query = "SELECT SUM(total_video_uploaded) as total_video_uploaded, assessment_id FROM(
            SELECT count(*) as total_video_uploaded,am.id as assessment_id FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id ";
        if ($manager_id != '') {
            $query .= " LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id ";
            $query .= " WHERE am.status=1 and ar.company_id = '" . $company_id . "' AND  amu.trainer_id = '$manager_id' ";
        }else{
            $query .= " WHERE am.status=1 and ar.company_id = '" . $company_id . "' ";
        }
        $query .= " AND ar.ftp_status=1 AND du.istester=0 AND ar.assessment_id IN (" . implode(',', $assessment_ids) . ") 
                GROUP BY am.id
            UNION ALL
            SELECT count(*) as total_video_uploaded,am.id as assessment_id FROM `trinity_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id ";
        if ($manager_id != '') {
            $query .= " LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id ";
            $query .= " WHERE am.status=1 and ar.company_id = '" . $company_id . "' AND  amu.trainer_id = '$manager_id' ";
        }else{
            $query .= " WHERE am.status=1 and ar.company_id = '" . $company_id . "' ";
        }
        $query .= " AND ar.ftp_status=1 AND du.istester=0 AND ar.assessment_id IN (" . implode(',', $assessment_ids) . ") 
                GROUP BY am.id
            ) as main GROUP BY assessment_id ORDER BY assessment_id DESC";
        // $query = "SELECT count(*) as total_video_uploaded,am.id as assessment_id FROM `assessment_results` as ar 
		// 		LEFT JOIN device_users as du ON du.user_id=ar.user_id 
        //         LEFT JOIN assessment_mst as am on ar.assessment_id = am.id ";
        // if ($manager_id != '') {
        //     $query .= " LEFT JOIN assessment_mapping_user amu on amu.assessment_id = am.id ";
        //     $query .= " WHERE am.status=1 and ar.company_id = '" . $company_id . "' AND  amu.trainer_id = '$manager_id' ";
        // }else{
        //     $query .= " WHERE am.status=1 and ar.company_id = '" . $company_id . "' ";
        // }
        // $query .= " AND ar.ftp_status=1 AND du.istester=0 AND ar.assessment_id IN (" . implode(',', $assessment_ids) . ") 
        //         GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function total_assessment_monthly_end($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id = '')
    {

        $query = "SELECT IFNULL(count(distinct am.id),0) as currentmonth,month(am.end_dttm) as wmonth, DATE_FORMAT(am.end_dttm,'%d') wday 
                      FROM assessment_mst am
                      left join assessment_mapping_user as amu on amu.assessment_id = am.id
                      WHERE am.company_id='" . $Company_id . "' 
                      AND date(am.end_dttm) BETWEEN '$monthstartdate' AND '$monthenddate' ";
        if ($manager_id != '') {
            $query .= " AND amu.trainer_id = '$manager_id' ";
        }
        $query .= " group by month(am.end_dttm)";

        $query1 = "SELECT IFNULL(count(distinct am.id),0) as months,month(am.end_dttm) as wmonth, DATE_FORMAT(am.end_dttm,'%d') wday 
                        FROM assessment_mst am
                        left join assessment_mapping_user as amu on amu.assessment_id = am.id
                        WHERE am.company_id='" . $Company_id . "'
                        AND date(am.end_dttm) BETWEEN '$lastmonthdate' AND '$lastmonthenddate'  ";
        if ($manager_id != '') {
            $query1 .= " AND amu.trainer_id = '$manager_id' ";
        }
        $query1 .= " group by month(am.end_dttm)";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // By Bhautik rana 01-02-2023
    public function get_distinct_participants($company_id, $assessment_id, $assessment_type, $manager_id = '')
    {
        if($assessment_type == 1 || $assessment_type == 2){
            
            $query  = "SELECT distinct company_id,assessment_id,user_id,user_name,email,mobile,is_sent,attempt FROM (SELECT
                        main.*,@dcp AS previous,
                        CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                        @dcp := main.user_id AS current,
                        CONCAT(main.user_id,'-',main.question_id) as uid	 
                    FROM(
                        SELECT DISTINCT
                            ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                            CONCAT( du.firstname, ' ', du.lastname ) AS user_name,du.email,du.mobile,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed,trs.is_sent,trs.attempt,trs.scheduled_at
                        FROM
                            assessment_results AS ar
                            LEFT JOIN company AS c ON ar.company_id = c.id
                            LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                            LEFT JOIN assessment_mapping_user as amu on amu.assessment_id = am.id
                            LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                            LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                            LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                            LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id
                            LEFT JOIN trainee_report_schedule trs ON ar.user_id=trs.user_id AND ar.assessment_id=trs.assessment_id						
                        WHERE
                            ar.company_id = '" . $company_id . "' AND ar.assessment_id IN (" . $assessment_id . ") ";
            if ($manager_id != '') {
                $query .= " AND amu.trainer_id = '$manager_id' ";
            }
            $query .= " AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                            AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1
                        ORDER BY
                            ar.user_id, ar.trans_id, trs.scheduled_at DESC 
                        ) AS main
                        CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                    ORDER BY
                    main.user_id, main.trans_id) AS final GROUP BY user_id ORDER BY user_id";
        } elseif ($assessment_type == 3) {
            $query = "SELECT DISTINCT
                    ar.company_id,ar.assessment_id,ar.user_id,c.portal_name,am.assessment,CONCAT( du.firstname, ' ', du.lastname ) AS user_name,du.email,du.mobile
                    ,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed,trs.is_sent,trs.attempt,trs.scheduled_at
                FROM
                    trinity_results AS ar
                    LEFT JOIN company AS c ON ar.company_id = c.id
                    LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                    LEFT JOIN assessment_mapping_user as amu on amu.assessment_id = am.id
                    LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                    LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                    LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id
                    LEFT JOIN trainee_report_schedule trs ON ar.user_id=trs.user_id AND ar.assessment_id=trs.assessment_id						
                WHERE
                    ar.company_id = " . $company_id . " AND ar.assessment_id IN (" . $assessment_id . ") ";
            if ($manager_id != '') {
                $query .= " AND amu.trainer_id = '$manager_id' ";
            }
            $query .= " AND ar.ftp_status=1 AND ar.vimeo_uri !='' AND aa.is_completed =1 AND ai.task_id != '' AND ai.xls_imported=1
                ORDER BY ar.user_id, trs.scheduled_at DESC ";
        }
        
        $result = $this->db->query($query);
		$data_array = $result->result_array();
		$data['dtTotalRecords'] = count((array)$data_array);
		
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        return $data;
    }
    // By Bhautik rana 01-02-2023

}
