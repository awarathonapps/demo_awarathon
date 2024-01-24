<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_dashboard_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // FOR CHARTS AND COUNT
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
    public function total_assessment_monthly($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id)
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
    public function rap_total_user_monthly($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id)
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

    public function total_assessment_monthly_end($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id)
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

    public function Month_Wise_Count($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id)
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

    public function Month_Wise_Count_processed($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id)
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
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND (am.start_dttm BETWEEN  '" . $StartStrDt . "' AND '" . $EndDate . "'";
        }
        // $query = "SELECT sum(played) as played,sum(completed) as completed, amonth, wday from 
        // ( SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, 
        // sum(IF(is_completed=1,1,0)) as completed,DATE_FORMAT(am.start_dttm,'%d') wday,";
        // if ($Day_type == '365_days') {
        //     $query .= "DATE_FORMAT(aa.addeddate,'%m-%Y') as amonth ";
        // } else {
        //     $query .= "month(aa.addeddate) as amonth ";
        // }
        // $query .= "FROM assessment_attempts as aa 
        // left join assessment_mst as am on aa.assessment_id = am.id 
        // LEFT join device_users as du on du.user_id = aa.user_id 
        // WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
        // $cond 
        // AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "') 
        // group by assessment_id,aa.user_id ) as main ";
        // if ($Day_type == '7_days') {
        //     $query .= "GROUP BY wday";
        // } else {
        //     $query .= "GROUP BY amonth";
        // }

        $query = "SELECT sum(played) as played,sum(completed) as completed, wmonth, wday from 
               ( SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, 
               sum(IF(is_completed=1,1,0)) as completed, DATE_FORMAT(aa.addeddate,'%d') wday,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(aa.addeddate,'%m-%Y') as wmonth ";
        } else {
            $query .= "month(aa.addeddate) as wmonth ";
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
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        
        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->played;
                    $AssessArray[$value->wday] = $value->completed;
                }
            }
            $ResultArray['played'] = $PeriodArray;
            $ResultArray['completed'] = $AssessArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->played;
                    $AssessArray[$value->wmonth] = $value->completed;
                }
            }
            $ResultArray['played'] = $PeriodArray;
            $ResultArray['completed'] = $AssessArray;
            return $ResultArray;
        }
    }

    public function raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;

        $PeriodArray = array();
        $playedArray = array();
        // $query = "SELECT sum(played) as played,sum(completed) as completed, wmonth,wday from 
        //                ( SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, 
        //                sum(IF(is_completed=1,1,0)) as completed, month(aa.addeddate) as wmonth,DATE_FORMAT(am.addeddate,'%d') wday 
        //                FROM assessment_attempts as aa 
        //                left join assessment_mst as am on aa.assessment_id = am.id 
        //                LEFT join device_users as du on du.user_id = aa.user_id 
        //                WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
        //                $cond 
        //                AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "') 
        //                group by assessment_id,aa.user_id ) as main group by wmonth ";
        $query = "SELECT sum(played) as played,sum(completed) as completed, wmonth, wday from 
        ( SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, sum(IF(is_completed=1,1,0)) as completed,  month(aa.addeddate) as wmonth, DATE_FORMAT(am.addeddate,'%d') wday 
        FROM assessment_attempts as aa 
        left join assessment_mst as am on aa.assessment_id = am.id 
        LEFT join device_users as du on du.user_id = aa.user_id 
        WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "'  
        AND du.istester=0 
        AND (am.start_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "' 
        OR am.end_dttm BETWEEN '" . $StartStrDt . "' AND '2022-04-24' 
        OR am.start_dttm <= '" . $StartStrDt . "' AND am.end_dttm >= '" . $EndDtdate . "') 
        AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '2022-04-18' AND '" . $EndDtdate . "') 
        group by assessment_id,aa.user_id ) as main group BY wmonth;";
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
        return $ResultArray;
    }
    // 30_60Days for no Raps Completed and played
}