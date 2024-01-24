<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class adoption_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // FOR CHARTS AND COUNT
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

    public function assessment_wise_manager($assessment_id1)
    {
        $query = "SELECT DISTINCT cu.userid as user_id,CONCAT(cu.first_name,' ',cu.last_name) as user_name, cu.email, a.assessment_id as assessment_id, am.assessment
        FROM `assessment_mapping_user` as a 
        LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id 
        LEFT JOIN company_users as cu ON cu.userid=a.trainer_id 
        where 1=1 AND  a.assessment_id = '" . $assessment_id1 . "' 
        order by user_name";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function assessment_started($StartStrDt = '', $EndDate = '', $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "'";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as result,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.start_dttm) as wmonth,";
        }
        $query .= "DATE_FORMAT(am.start_dttm,'%d') 
                     wday FROM assessment_mst am 
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
    // Common Function.


    // THIS FUNCTION FOR LAST 30 DAYS DATA & FOR 60 DAYS
    public function assessment_index_30_60days($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as total,month(am.start_dttm) as wmonth
                         FROM assessment_mst am WHERE am.status =1 AND am.company_id='" . $Company_id . "'
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
    public function total_assessment_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id)
    {

        $query = "SELECT IFNULL(count(distinct am.id),0) as currentmonth FROM assessment_mst am 
                    WHERE  am.status =1 AND am.company_id='" . $Company_id . "' AND am.start_dttm BETWEEN '$monthstartdate' AND '$monthenddate' GROUP BY month(am.start_dttm)";

        $query1 = "SELECT IFNULL(count(distinct am.id),0) as months FROM assessment_mst am 
                    WHERE  am.status =1 AND am.company_id='" . $Company_id . "' AND am.start_dttm  BETWEEN '$lastmonthdate' AND '$lastmonthenddate' GROUP BY month(am.start_dttm)";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // END HERE

    // Raps Map User Start
    public function get_raps_mapped_user($StartDate, $EndDate, $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $cond = "";
        if ($StartDate != '' && $EndDate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartDate . "' AND '" . $EndDate . "' ";
        }
        $query = "SELECT DISTINCT count(aau.user_id) as result ,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.start_dttm) as wmonth,";
        }
        $query .= "DATE_FORMAT(am.start_dttm,'%d') wday FROM assessment_allow_users as aau 
                   left join assessment_mst as am on aau.assessment_id = am.id
                   LEFT join device_users as du on aau.user_id=du.user_id
                   WHERE  am.status =1 AND du.istester=0 AND am.company_id='" . $Company_id . "' $cond ";
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
    //last 30 and 60 day rap_map users
    public function  get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        $query = "SELECT DISTINCT count(aau.user_id) as total ,month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday 
                    FROM assessment_allow_users as aau
                    left join assessment_mst as am on aau.assessment_id = am.id
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
    //end here

    // Rep Map Total Users
    public function rap_total_user_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id)
    {

        $query = "SELECT DISTINCT count(aau.user_id) as currentmonth FROM assessment_allow_users as aau
                left join assessment_mst as am on aau.assessment_id = am.id
                LEFT join device_users as du on aau.user_id=du.user_id
                WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
                AND am.start_dttm BETWEEN '$monthstartdate' AND '$monthenddate' GROUP BY month(am.start_dttm) ";


        $query1 = "SELECT DISTINCT count(aau.user_id) as months FROM assessment_allow_users as aau
                left join assessment_mst as am on aau.assessment_id = am.id
                LEFT join device_users as du on aau.user_id=du.user_id
                WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
                AND am.start_dttm BETWEEN '$lastmonthdate' AND '$lastmonthenddate' GROUP BY month(am.start_dttm) ";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    //

    // End Here


    // ASSESSMENT COMPLETED COUNT AND MONTHLY COUNT START HERE
    public function assessment_index_end($StartStrDt, $EndDate, $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND date(am.end_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "'";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as result,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.end_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.end_dttm) as wmonth,";
        }
        $query .= "DATE_FORMAT(am.end_dttm,'%d') 
                     wday FROM assessment_mst am 
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
    public function assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.end_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        $query = "SELECT IFNULL(count(distinct am.id),0) as total,month(am.end_dttm) as wmonth
                          FROM assessment_mst am WHERE am.status =1 AND am.company_id='" . $Company_id . "'
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
    // End Here

    public function total_assessment_monthly_end($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id)
    {

        $query = "SELECT IFNULL(count(distinct am.id),0) as currentmonth,month(am.end_dttm) as wmonth, DATE_FORMAT(am.end_dttm,'%d') wday 
                      FROM assessment_mst am WHERE am.company_id='" . $Company_id . "' 
                      AND date(am.end_dttm) BETWEEN '$monthstartdate' AND '$monthenddate' group by month(am.end_dttm)";

        $query1 = "SELECT IFNULL(count(distinct am.id),0) as months,month(am.end_dttm) as wmonth, DATE_FORMAT(am.end_dttm,'%d') wday 
                        FROM assessment_mst am WHERE am.company_id='" . $Company_id . "'
                        AND date(am.end_dttm) BETWEEN '$lastmonthdate' AND '$lastmonthenddate' group by month(am.end_dttm)";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // END HERE

    // Total Videos Uploaded
    public function total_video_uploaded($StartStrDt, $EndDate, $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();

        $query = "SELECT count(*) as total ,wmonth,wday FROM (
        SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id, ";

        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(ar.addeddate,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(ar.addeddate) as wmonth,";
        }
        $query .= " DATE_FORMAT(ar.addeddate,'%d') wday FROM assessment_results as ar
                left join assessment_mst as am on ar.assessment_id = am.id 
                left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id and ar.user_id = aa.user_id
                LEFT JOIN device_users as du ON ar.user_id=du.user_id
                WHERE ar.ftp_status=1 and am.status=1 AND am.company_id ='" . $Company_id . "' and aa.ftpto_vimeo_uploaded = 1 
                and aa.is_completed = 1  AND du.istester=0 
                AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDate')) as main ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY wday";
        } else {
            $query .= "GROUP BY wmonth";
        }

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->total;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->total;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        }
    }

    public function total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();

        $query = "SELECT count(*) as total ,wmonth,wday FROM (SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id, 
                    month(ar.addeddate) as wmonth, 
                    DATE_FORMAT(ar.addeddate,'%d') wday 
                    FROM assessment_results as ar left join assessment_mst as am on ar.assessment_id = am.id 
                    left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id 
                    and ar.user_id = aa.user_id LEFT JOIN device_users as du ON ar.user_id=du.user_id 
                    WHERE ar.ftp_status=1 and am.status=1 AND am.company_id ='" . $Company_id . "' and aa.ftpto_vimeo_uploaded = 1 
                    and aa.is_completed = 1 AND du.istester=0 
                    AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDtdate')) as main GROUP BY wmonth";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }

    public function Month_Wise_Count($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id)
    {
        $query = "SELECT count(*) as currentmonth ,wmonth,wday 
                  FROM (SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id, 
                  month(ar.addeddate) as wmonth, 
                  DATE_FORMAT(ar.addeddate,'%d') wday 
                  FROM assessment_results as ar left join assessment_mst as am on ar.assessment_id = am.id 
                  left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id 
                  and ar.user_id = aa.user_id LEFT JOIN device_users as du ON ar.user_id=du.user_id 
                  WHERE ar.ftp_status=1 and am.status=1 AND am.company_id ='" . $Company_id . "' and aa.ftpto_vimeo_uploaded = 1 
                  and aa.is_completed = 1 AND du.istester=0 
                  AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '$monthstartdate' AND '$monthenddate')) as main GROUP BY wmonth";

        $query1 = "SELECT count(*) as months ,wmonth,wday 
                    FROM (SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id, 
                    month(ar.addeddate) as wmonth, 
                    DATE_FORMAT(ar.addeddate,'%d') wday 
                    FROM assessment_results as ar left join assessment_mst as am on ar.assessment_id = am.id 
                    left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id 
                    and ar.user_id = aa.user_id LEFT JOIN device_users as du ON ar.user_id=du.user_id 
                    WHERE ar.ftp_status=1 and am.status=1 AND am.company_id ='" . $Company_id . "' and aa.ftpto_vimeo_uploaded = 1 
                    and aa.is_completed = 1 AND du.istester=0 
                    AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '$lastmonthdate' AND '$lastmonthenddate')) as main GROUP BY wmonth";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // End Here

    // Total Videos Processed
    public function total_video_processed($StartStrDt, $EndDate, $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();

        $query = "SELECT count(*) as total, ";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(ai.task_status_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(ai.task_status_dttm) as wmonth,";
        }
        $query .= " DATE_FORMAT(ai.task_status_dttm,'%d') wday FROM `ai_schedule` as ai 
                   LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                   LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                   WHERE am.status=1 and ai.company_id='" . $Company_id . "' AND ai.task_status = 1 AND du.istester=0 
                   AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDate' ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(ai.task_status_dttm)";
        } else {
            $query .= "GROUP BY month(ai.task_status_dttm)";
        }

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->total;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->total;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        }
    }

    public function total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();

        $query = "SELECT count(*) as total, month(ai.task_status_dttm) as wmonth , DATE_FORMAT(ai.task_status_dttm,'%d') wday FROM `ai_schedule` as ai 
                LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                WHERE am.status=1 and ai.company_id='" . $Company_id . "' AND ai.task_status = 1 AND du.istester=0 
                AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDtdate' 
                GROUP BY month(ai.task_status_dttm)";
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }

    public function Month_Wise_Count_processed($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id)
    {
        $query = "SELECT count(*) as currentmonth, month(ai.task_status_dttm) as wmonth , DATE_FORMAT(ai.task_status_dttm,'%d') wday FROM `ai_schedule` as ai 
                LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                WHERE am.status=1 and ai.company_id='" . $Company_id . "' AND ai.task_status = 1 AND du.istester=0 
                AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '$monthstartdate' AND '$monthenddate' 
                GROUP BY month(ai.task_status_dttm)";

        $query1 = "SELECT count(*) as months, month(ai.task_status_dttm) as wmonth , DATE_FORMAT(ai.task_status_dttm,'%d') wday FROM `ai_schedule` as ai 
                LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                WHERE am.status=1 and ai.company_id='" . $Company_id . "' AND ai.task_status = 1 AND du.istester=0 
                AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '$lastmonthdate' AND '$lastmonthenddate' 
                GROUP BY month(ai.task_status_dttm)";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }

    // End Here

    //TOTAL active and inactive users 
    public function total_active_inactive($StartStrDt, $EndDate, $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();

        $query = "SELECT DISTINCT count(aau.user_id) as result ,";

        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.start_dttm) as wmonth,";
        }

        $query .= " DATE_FORMAT(am.start_dttm,'%d') wday 
                FROM assessment_allow_users as aau left join assessment_mst as am on aau.assessment_id = am.id 
                LEFT join device_users as du on aau.user_id=du.user_id WHERE am.status =1 AND du.istester=0 
                AND am.company_id='" . $Company_id . "'
                AND date(am.start_dttm) BETWEEN '$StartStrDt' AND '$EndDate' ";

        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(am.start_dttm)";
        } else if ($Day_type == '90_days' ||  $Day_type == '365_days') {
            $query .= "GROUP BY month(am.start_dttm)";
        } else {
            $query .= "GROUP BY month(am.start_dttm)";
        }


        //
        $query1 = "SELECT DISTINCT count(aau.user_id) as result ,";

        if ($Day_type == '365_days') {
            $query1 .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query1 .= "month(am.start_dttm) as wmonth,";
        }

        $query1 .= "DATE_FORMAT(am.start_dttm,'%d') wday 
                  FROM assessment_allow_users as aau left join assessment_mst as am on aau.assessment_id = am.id 
                  LEFT join device_users as du on aau.user_id=du.user_id WHERE am.status =1 AND du.istester=0 
                  AND du.status=1 AND am.company_id='" . $Company_id . "'
                  AND date(am.start_dttm) BETWEEN '$StartStrDt' AND '$EndDate' ";

        if ($Day_type == '7_days') {
            $query1 .= "GROUP BY day(am.start_dttm)";
        } else if ($Day_type == '90_days' ||  $Day_type == '365_days') {
            $query1 .= "GROUP BY month(am.start_dttm)";
        } else {
            $query1 .= "GROUP BY month(am.start_dttm)";
        }

        //
        $query2 = "SELECT DISTINCT count(aau.user_id) as result ,";

        if ($Day_type == '365_days') {
            $query2 .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query2 .= "month(am.start_dttm) as wmonth,";
        }

        $query2 .= "DATE_FORMAT(am.start_dttm,'%d') wday 
                 FROM assessment_allow_users as aau left join assessment_mst as am on aau.assessment_id = am.id 
                 LEFT join device_users as du on aau.user_id=du.user_id WHERE am.status =1 AND du.istester=0 
                 AND du.status=0 AND am.company_id='" . $Company_id . "'
                 AND date(am.start_dttm) BETWEEN '$StartStrDt' AND '$EndDate' ";

        if ($Day_type == '7_days') {
            $query2 .= "GROUP BY day(am.start_dttm)";
        } else if ($Day_type == '90_days' ||  $Day_type == '365_days') {
            $query2 .= "GROUP BY month(am.start_dttm)";
        } else {
            $query2 .= "GROUP BY month(am.start_dttm)";
        }
        //
        $total_user = $this->db->query($query);
        $Total_users = $total_user->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Total_users) > 0) {
                foreach ($Total_users as $value) {
                    $PeriodArray[$value->wday] = $value->result;
                }
            }
            $ResultArray['total_user'] = $PeriodArray;
        } else if ($Day_type == '90_days' or $Day_type == '365_days') {
            if (!empty((array)$Total_users) > 0) {
                foreach ($Total_users as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['total_user'] = $PeriodArray;
        } else {
            if (!empty((array)$Total_users) > 0) {
                foreach ($Total_users as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['total_user'] = $PeriodArray;
        }
        //

        $active_user = $this->db->query($query1);
        $Total_active_user = $active_user->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Total_active_user) > 0) {
                foreach ($Total_active_user as $value) {
                    $PeriodArray[$value->wday] = $value->result;
                }
            }
            $ResultArray['active_user'] = $PeriodArray;
        } else if ($Day_type == '90_days' or $Day_type == '365_days') {
            if (!empty((array)$Total_active_user) > 0) {
                foreach ($Total_active_user as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['active_user'] = $PeriodArray;
        } else {
            if (!empty((array)$Total_active_user) > 0) {
                foreach ($Total_active_user as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['active_user'] = $PeriodArray;
        }
        //
        $inactive_user = $this->db->query($query2);
        $Total_inactive_user = $inactive_user->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Total_inactive_user) > 0) {
                foreach ($Total_inactive_user as $value) {
                    $PeriodArray[$value->wday] = $value->result;
                }
            }
            $ResultArray['inactive_user'] = $PeriodArray;
        } else if ($Day_type == '90_days' or $Day_type == '365_days') {
            if (!empty((array)$Total_inactive_user) > 0) {
                foreach ($Total_inactive_user as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['inactive_user'] = $PeriodArray;
        } else {
            if (!empty((array)$Total_inactive_user) > 0) {
                foreach ($Total_inactive_user as $value) {
                    $PeriodArray[$value->wmonth] = $value->result;
                }
            }
            $ResultArray['inactive_user'] = $PeriodArray;
        }
        return $ResultArray;
    }

    public function total_active_inactive_last_30_60($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDtdate != '') {
            $cond .= " AND date(am.end_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'";
        }
        $query = "SELECT DISTINCT count(aau.user_id) as total ,month(am.start_dttm) as wmonth
              FROM assessment_allow_users as aau 
              left join assessment_mst as am on aau.assessment_id = am.id 
              LEFT join device_users as du on aau.user_id=du.user_id 
              WHERE am.status =1 AND du.istester=0 
              AND am.company_id='" . $Company_id . "' $cond 
              GROUP BY month(am.start_dttm)";

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }
    // end here

    public function RapsPlayedComplted($StartStrDt = '', $EndDate = '', $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();
        $MappedArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND (am.start_dttm BETWEEN  '" . $StartStrDt . "' AND '" . $EndDate . "'";
        }
        $query = "SELECT sum(played) as played,sum(completed) as completed, wmonth, wday from 
               ( SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, 
               sum(IF(is_completed=1,1,0)) as completed, DATE_FORMAT(aa.complete_dttm,'%d') wday,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(aa.complete_dttm,'%m-%Y') as wmonth ";
        } else {
            $query .= "month(aa.complete_dttm) as wmonth ";
        }
        $query .= "FROM assessment_attempts as aa 
               left join assessment_mst as am on aa.assessment_id = am.id 
               LEFT join device_users as du on du.user_id = aa.user_id 
               WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
               $cond 
               OR am.end_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "' 
               OR am.start_dttm <= '" . $StartStrDt . "' AND am.end_dttm >= '" . $EndDate . "') 
               AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "') 
               group by assessment_id,aa.user_id ) as main ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY wday";
        } else {
            $query .= "GROUP BY wmonth";
        }
        // For Mapped Users
        $query1 = "SELECT DISTINCT count(aau.user_id) as mapped ,";
        if ($Day_type == '365_days') {
            $query1 .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query1 .= "month(am.start_dttm) as wmonth,";
        }
        $query1 .= "DATE_FORMAT(am.start_dttm,'%d') wday FROM assessment_allow_users as aau 
                   left join assessment_mst as am on aau.assessment_id = am.id
                   LEFT join device_users as du on aau.user_id=du.user_id
                   WHERE  am.status =1 AND du.istester=0 AND am.company_id='" . $Company_id . "' and am.start_dttm BETWEEN  '" . $StartStrDt . "' AND '" . $EndDate . "' ";
        if ($Day_type == '7_days') {
            $query1 .= "GROUP BY day(am.start_dttm)";
        } else {
            $query1 .= "GROUP BY month(am.start_dttm)";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();

        // For Mapped Users
        $result1 = $this->db->query($query1);
        $Accuracy1 = $result1->result();

        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->played;
                    $AssessArray[$value->wday] = $value->completed;
                }
            }
            // For Mapped Users
            if (!empty((array)$Accuracy1) > 0) {
                foreach ($Accuracy1 as $value) {
                    $MappedArray[$value->wday] = $value->mapped;
                }
            }
            $ResultArray['played'] = $PeriodArray;
            $ResultArray['completed'] = $AssessArray;
            $ResultArray['mapped'] = $MappedArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->played;
                    $AssessArray[$value->wmonth] = $value->completed;
                }
            }
            // For Mapped Users
            if (!empty((array)$Accuracy1) > 0) {
                foreach ($Accuracy1 as $value) {
                    $MappedArray[$value->wmonth] = $value->mapped;
                }
            }
            $ResultArray['played'] = $PeriodArray;
            $ResultArray['completed'] = $AssessArray;
            $ResultArray['mapped'] = $MappedArray;
            return $ResultArray;
        }
    }

    public function raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();
        $playedArray = array();
        $MappedArray = array();
        $query = "SELECT sum(played) as played,sum(completed) as completed, wmonth, wday from (SELECT sum(played) as played,
                  sum(completed) as completed, wmonth, wday from 
                  ( SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, 
                  sum(IF(is_completed=1,1,0)) as completed,  month(aa.complete_dttm) as wmonth, 
                  DATE_FORMAT(aa.complete_dttm,'%d') wday 
                  FROM assessment_attempts as aa 
                  left join assessment_mst as am on aa.assessment_id = am.id 
                  LEFT join device_users as du on du.user_id = aa.user_id 
                  WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "'  
                  AND du.istester=0 
                  AND (am.start_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "' 
                  OR am.end_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "' 
                  OR am.start_dttm <= '" . $StartStrDt . "' AND am.end_dttm >= '" . $EndDtdate . "') 
                  AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "') 
                  group by assessment_id,aa.user_id ) as main group BY wmonth) as main2";

        $query1 = "SELECT DISTINCT count(aau.user_id) as mapped ,month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d') wday 
                   FROM assessment_allow_users as aau
                   left join assessment_mst as am on aau.assessment_id = am.id
                   LEFT join device_users as du on aau.user_id=du.user_id  
                   AND date(am.start_dttm) BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "'
                   WHERE am.STATUS = '1'AND du.istester=0  AND am.company_id='" . $Company_id . "'
                   GROUP BY month(am.start_dttm)";

        // User Mapped Query           
        $result1 = $this->db->query($query1);
        $Accuracy1 = $result1->result();
        if (!empty((array)$Accuracy1) > 0) {
            foreach ($Accuracy1 as $value) {
                $MappedArray = $value->mapped;
            }
        }


        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->completed;
                $playedArray = $value->played;
            }
        }


        $ResultArray['played'] = $PeriodArray;
        $ResultArray['completed'] = $playedArray;
        $ResultArray['mapped'] = $MappedArray;
        return $ResultArray;
    }
    // 30_60Days for no Raps Completed and played

    // Total Report Sent
    public function total_reports_sent($StartStrDt, $EndDate, $Day_type, $Company_id)
    {
        $ResultArray = array();
        $PeriodArray = array();

        $query = "SELECT count(*) as total,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(sent_at,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(sent_at) as wmonth,";
        }

        $query .= "  DATE_FORMAT(sent_at,'%d')as wday FROM trainee_report_schedule 
                 WHERE is_sent=1 AND company_id='" . $Company_id . "' AND sent_at BETWEEN '$StartStrDt' AND '$EndDate' ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(sent_at)";
        } else {
            $query .= "GROUP BY month(sent_at)";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->total;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->total;
                }
            }
            $ResultArray['period'] = $PeriodArray;
            return $ResultArray;
        }
    }

    public function total_reports_sent_last_30_60($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();

        $query = "SELECT count(*) as total,month(sent_at) as wmonth , DATE_FORMAT(sent_at,'%d') AS wday  FROM trainee_report_schedule
                    WHERE is_sent=1 AND company_id='" . $Company_id . "' 
                    AND sent_at BETWEEN '$StartStrDt' AND '$EndDtdate' GROUP BY month(sent_at)";

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->total;
            }
        }
        return $PeriodArray;
    }

    public function Month_Wise_Count_Send($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id)
    {
        $query = "SELECT count(*) as currentmonth,month(sent_at) as wmonth , DATE_FORMAT(sent_at,'%d') AS wday  
                  FROM trainee_report_schedule WHERE is_sent=1 
                  AND company_id='" . $Company_id . "' AND sent_at BETWEEN '$monthstartdate' AND '$monthenddate' GROUP BY month(sent_at)";

        $query1 = "SELECT count(*) as months,month(sent_at) as wmonth , DATE_FORMAT(sent_at,'%d') AS wday  
                   FROM trainee_report_schedule WHERE is_sent=1 
                   AND company_id='" . $Company_id . "' AND sent_at BETWEEN '$lastmonthdate' AND '$lastmonthenddate' GROUP BY month(sent_at)";
        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }


    // Find Last Expired Assessment
    public function LastExpiredAssessment($CurrentDate)
    {
        $query = "SELECT id,assessment FROM `assessment_mst` WHERE end_dttm <= '" . $CurrentDate . "' ORDER BY end_dttm DESC LIMIT 1";
        // $query = "SELECT id,assessment FROM `assessment_mst` WHERE end_dttm <= '" . $CurrentDate . "' AND report_type=3 ORDER BY end_dttm DESC LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // Custom Assessment Name using Assessment_id
    public function GetAssessmentName($Assessment_id)
    {
        $query = "SELECT assessment FROM `assessment_mst` WHERE id= '" . $Assessment_id . "' ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    // Find Last Expired Assessment Manager Name
    public function GetFiveManager($lastAssessmentId)
    {
        $query = "SELECT DISTINCT(am.trainer_id), CONCAT(cm.first_name,' ',cm.last_name) AS trainer_name 
                  FROM assessment_mapping_user am LEFT JOIN company_users cm ON am.trainer_id = cm.userid 
                  WHERE am.assessment_id='" . $lastAssessmentId . "' ORDER BY user_id DESC LIMIT 5";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // Custom Manager Name using Manager_id 
    public function GetManagerName($TrainerIDSet)
    {
        $query = "SELECT DISTINCT(am.trainer_id), CONCAT(cm.first_name,' ',cm.last_name) AS trainer_name 
                  FROM assessment_mapping_user am 
                  LEFT JOIN company_users cm ON am.trainer_id = cm.userid 
                  WHERE am.trainer_id IN (" . implode(',', $TrainerIDSet) . ") ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // Find User Assessment ID and Manager ID Wise.
    public function GetUserManagerwise($Assessment_id, $TrainerIDSet)
    {
        $query = "select manager_id, if(per_user_started,per_user_started, 0.00) as per_user_strated,if(per_user_completed,per_user_completed, 0.00) as per_user_completed from 
                  (SELECT manager_id, ROUND((100*cnt_user_started)/user_mapped,2) as per_user_started, 
                   ROUND((100*cnt_user_completed)/user_mapped,2) as per_user_completed 
                   FROM (SELECT COUNT(am.user_id) as user_mapped, COUNT(aa.user_id) as cnt_user_started, 
                   sum(aa.is_completed) as cnt_user_completed, am.trainer_id as manager_id 
                   FROM assessment_mapping_user as am 
                   LEFT join assessment_attempts as aa 
                   ON am.user_id = aa.user_id 
                   and am.assessment_id=aa.assessment_id 
                   WHERE am.trainer_id IN (" . implode(',', $TrainerIDSet) . ") 
              AND am.assessment_id = '" . $Assessment_id . "' GROUP BY am.trainer_id ORDER by am.trainer_id ASC) as main) as main2";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    //End Here
    // Adoption_by_team Graph End

    // Adoption by Division Start
    public function getdepartment($lastAssessmentId)
    {
        $query = "SELECT DISTINCT(amu.user_id), du.department 
        FROM `assessment_mapping_user` as amu 
        LEFT JOIN device_users as du on du.user_id = amu.user_id 
        WHERE amu.assessment_id ='" . $lastAssessmentId . "' 
        GROUP BY du.department";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function GetUserDepartmentwise($lastAssessmentId, $DepartmentIdSet)
    {
        $query = "select department_id, department_name, if(per_user_started,per_user_started, 0) as per_user_strated, 
        if(per_user_completed,per_user_completed, 0) as per_user_completed from
        (SELECT department_id, department_name, ROUND((100*cnt_user_started)/user_mapped,2) as per_user_started, 
         ROUND((100*cnt_user_completed)/user_mapped,2) as per_user_completed FROM 
        (select DISTINCT(du.user_id) as department_id,
                 du.department as department_name,
                 COUNT(am.user_id) as user_mapped,
                du.department  as department,
                count(aa.user_id) as cnt_user_started,
                sum(aa.is_completed) as cnt_user_completed
        from assessment_mapping_user as am
        left join device_users as du ON am.user_id = du.user_id
                   left join assessment_attempts as aa  on du.user_id = aa.user_id and aa.assessment_id = am.assessment_id
                   WHERE am.assessment_id = '" . $lastAssessmentId . "' and du.user_id IN (" . implode(',', $DepartmentIdSet) . ") group by du.user_id) as main) as main2";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function GetDivisionName($DivisionSet)
    {
        $query = "SELECT user_id,department FROM device_users 
                  WHERE user_id IN (" . implode(',', $DivisionSet) . ")";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // Adotipn by Division End   

    // Adoption By Modules Start 
    // find last Expired 5 assessment
    public function LastExpiredFiveAssessment($CurrentDate, $Company_id)
    {
        $query = "SELECT id,assessment FROM `assessment_mst` WHERE end_dttm <= '" . $CurrentDate . "' AND company_id='" . $Company_id . "' ORDER BY end_dttm DESC LIMIT 5";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function GetUserAssessmentwise($Assessment_id, $Company_id)
    {
        $query = "select assessment_id,assessment, if(per_user_started,per_user_started, 0.00) as per_user_strated,
                  if(per_user_completed,per_user_completed, 0.00) as per_user_completed 
                  from (SELECT assessment_id,assessment, ROUND((100*cnt_user_started)/user_mapped,2) as per_user_started, 
                  ROUND((100*cnt_user_completed)/user_mapped,2) as per_user_completed 
                  FROM (SELECT COUNT(am.user_id) as user_mapped, COUNT(aa.user_id) as cnt_user_started, 
                  sum(aa.is_completed) as cnt_user_completed, am.assessment_id as assessment_id,
                  amt.assessment as assessment
                  FROM assessment_mapping_user as am 
                  LEFT join assessment_attempts as aa 
                  ON am.user_id = aa.user_id 
                  and am.assessment_id=aa.assessment_id 
                  LEFT JOIN assessment_mst as amt ON am.assessment_id =amt.id
                  WHERE am.assessment_id IN(" . implode(',', $Assessment_id) . ") AND amt.company_id='" . $Company_id . "'
                 GROUP BY am.assessment_id ORDER by am.assessment_id ASC) as main) as main2";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // Adoption By Modules End

    // Adoption By Region Start

    //Last Expired assessment
    public function lastexpiredamt($CurrentDate,$Company_id)
    {
        $query = "SELECT id,assessment FROM `assessment_mst` WHERE end_dttm <= '" . $CurrentDate . "' AND company_id='" . $Company_id . "' 
                  ORDER BY end_dttm DESC LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    //Get last expired assessments region id 
    public function getregion_id($assessment_id, $Company_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
                  FROM assessment_mst am
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  LEFT JOIN device_users du ON du.user_id=amu.user_id
                  LEFT JOIN region rg ON du.region_id=rg.id
                  WHERE am.id='" . $assessment_id . "' AND am.company_id='" . $Company_id . "'  and du.region_id !='0'
                  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result_array();
    }
    
    public function assessment_wise_region($assessment_id, $Company_id)
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
                  FROM assessment_mst am
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  LEFT JOIN device_users du ON du.user_id=amu.user_id
                  LEFT JOIN region rg ON du.region_id=rg.id
                  WHERE am.id='" . $assessment_id . "' AND am.company_id='" . $Company_id . "'  and du.region_id !='0'
                  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function Adoption_by_region($Assessment_id, $Company_id, $Region_id)
    {
        $query = "select assessment_id,assessment,region_name,region_id, 
                  if(per_user_started,per_user_started, 0.00) as per_user_strated, 
                  if(per_user_completed,per_user_completed, 0.00) as per_user_completed 
                  from (SELECT assessment_id,assessment, 
                  region_name, region_id,
                  ROUND((100*cnt_user_started)/user_mapped,2) as per_user_started, 
                  ROUND((100*cnt_user_completed)/user_mapped,2) as per_user_completed FROM (
                  SELECT COUNT(am.user_id) as user_mapped, COUNT(aa.user_id) as cnt_user_started, 
                  sum(aa.is_completed) as cnt_user_completed, am.assessment_id as assessment_id, 
                  amt.assessment as assessment ,rg.region_name as region_name , du.region_id as region_id
                  FROM assessment_mapping_user as am 
                  LEFT join assessment_attempts as aa ON am.user_id = aa.user_id and am.assessment_id=aa.assessment_id 
                  LEFT JOIN assessment_mst as amt ON am.assessment_id =amt.id 
                  LEFT JOIN device_users du ON am.user_id=du.user_id
                  LEFT JOIN region rg on du.region_id=rg.id
                  WHERE am.assessment_id = '" . $Assessment_id . "' AND amt.company_id = '" . $Company_id . "' 
                  AND du.region_id IN(" . implode(',', $Region_id) . ") 
                  GROUP BY du.region_id ORDER by du.region_id ASC) as main) as main2";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    // End Here
}