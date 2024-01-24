<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_manager_adoption_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // FOR CHARTS AND COUNT
    public function get_all_assessment($manager_id)
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
                FROM assessment_mst am 
                LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                LEFT JOIN assessment_report_type as art on art.id=am.report_type
				WHERE am.status = 1 AND amu.trainer_id = '" . $manager_id . "'
                GROUP BY am.id ORDER BY am.id DESC";
        // echo  $query;die;        
        $result = $this->db->query($query);
        return $result->result();
    }

    // Adoption By Modules Start 
    // find last Expired 5 assessment
    public function LastExpiredFiveAssessment($manager_id, $Company_id)
    {
        $query = "SELECT DISTINCT(am.assessment), am.id FROM `assessment_mst` am 
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id 
                  WHERE amu.trainer_id = '" . $manager_id . "' AND am.company_id ='" . $Company_id . "'  ORDER BY amu.trainer_id DESC LIMIT 5;";
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

    // Total Video Uploaded and processed
    public function total_video_uploaded($StartStrDt, $EndDate, $Day_type, $Company_id, $manager_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $ProcessedArray = array();
        // Video Uploaded
        $query = "SELECT count(*) as uploaded ,wmonth,wday FROM (
        SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id, ";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(ar.addeddate,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(ar.addeddate) as wmonth,";
        }
        $query .= " DATE_FORMAT(ar.addeddate,'%d') wday FROM assessment_results as ar
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id 
                LEFT JOIN assessment_attempts as aa on ar.assessment_id = aa.assessment_id and ar.user_id = aa.user_id
                LEFT JOIN device_users as du ON ar.user_id=du.user_id
                LEFT JOIN assessment_mapping_user as amu ON amu.user_id=ar.user_id
                WHERE ar.ftp_status=1 and am.status=1 
                AND am.company_id ='" . $Company_id . "'and amu.trainer_id = '" . $manager_id . "' 
                and aa.ftpto_vimeo_uploaded = 1 and aa.is_completed = 1  
                AND du.istester=0 AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDate')) as main ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY wday";
        } else {
            $query .= "GROUP BY wmonth";
        }

        // Video Proccessed
        $query1 = "SELECT count(*) as processed, ";
        if ($Day_type == '365_days') {
            $query1 .= "DATE_FORMAT(ai.task_status_dttm,'%m-%Y') as wmonth,";
        } else {
            $query1 .= "month(ai.task_status_dttm) as wmonth,";
        }
        $query1 .= " DATE_FORMAT(ai.task_status_dttm,'%d') wday FROM `ai_schedule` as ai 
                   LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                   LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                   LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id=am.id AND amu.user_id=ai.user_id
                   WHERE am.status=1 and ai.company_id='" . $Company_id . "' AND amu.trainer_id = '" . $manager_id . "' AND ai.task_status = 1 AND du.istester=0 
                   AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDate' ";
        if ($Day_type == '7_days') {
            $query1 .= "GROUP BY day(ai.task_status_dttm)";
        } else {
            $query1 .= "GROUP BY month(ai.task_status_dttm)";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();

        $result1 = $this->db->query($query1);
        $Accuracy1 = $result1->result();

        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->uploaded;
                }
            }
            if (!empty((array)$Accuracy1) > 0) {
                foreach ($Accuracy1 as $value) {
                    $$ProcessedArray[$value->wday] = $value->processed;
                }
            }
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->uploaded;
                }
            }
            if (!empty((array)$Accuracy1) > 0) {
                foreach ($Accuracy1 as $value) {
                    $ProcessedArray[$value->wmonth] = $value->processed;
                }
            }
        }
        $ResultArray['uploaded'] = $PeriodArray;
        $ResultArray['processed'] = $ProcessedArray;
        return $ResultArray;
    }

    public function total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id, $manager_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();
        $ProcessedArray = array();

        $query = "SELECT count(*) as uploaded ,wmonth,wday FROM (SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id, 
                    month(ar.addeddate) as wmonth, 
                    DATE_FORMAT(ar.addeddate,'%d') wday 
                    FROM assessment_results as ar left join assessment_mst as am on ar.assessment_id = am.id 
                    LEFT JOIN assessment_attempts as aa on ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                    LEFT JOIN device_users as du ON ar.user_id=du.user_id 
                    LEFT JOIN assessment_mapping_user as amu ON amu.user_id=ar.user_id 
                    WHERE ar.ftp_status=1 and am.status=1 AND am.company_id ='" . $Company_id . "'and amu.trainer_id = '" . $manager_id . "' 
                     and aa.ftpto_vimeo_uploaded = 1 
                    and aa.is_completed = 1 AND du.istester=0 
                    AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDtdate')) as main GROUP BY wmonth";

        $query1 = "SELECT count(*) as processed, month(ai.task_status_dttm) as wmonth , DATE_FORMAT(ai.task_status_dttm,'%d') wday FROM `ai_schedule` as ai 
                    LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                    LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                    LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id=am.id AND amu.user_id=ai.user_id
                    WHERE am.status=1 and ai.company_id='" . $Company_id . "' and amu.trainer_id = '" . $manager_id . "' AND ai.task_status = 1 AND du.istester=0 
                    AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '$StartStrDt' AND '$EndDtdate' 
                    GROUP BY month(ai.task_status_dttm)";


        $result = $this->db->query($query);
        $Accuracy = $result->result();

        $result1 = $this->db->query($query1);
        $Accuracy1 = $result1->result();

        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->uploaded;
            }
        }
        if (!empty((array)$Accuracy1) > 0) {
            foreach ($Accuracy1 as $value) {
                $ProcessedArray = $value->processed;
            }
        }
        $ResultArray['uploaded'] = $PeriodArray;
        $ResultArray['processed'] = $ProcessedArray;
        return $ResultArray;
    }
    // Total Video Uploaded and processed

    // Total Report Sent Start Here
    public function usersmangerwise($manager_id)
    {
        $query = "SELECT DISTINCT(amu.user_id) FROM  assessment_mapping_user amu WHERE amu.trainer_id = '" . $manager_id . "' ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function MappedUsers($YearStartDate, $YearEndDate, $Day_type, $Company_id, $manager_id)
    {
        $PeriodArray = array();
        $data = array();
        $query = "SELECT count(DISTINCT(amu.user_id)) as mapped, 
              DATE_FORMAT(aa.addeddate,'%d') wday,";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(aa.addeddate,'%m-%Y') as wmonth  ";
        } else {
            $query .= "month(aa.addeddate) as wmonth  ";
        }
        $query .= "FROM assessment_allow_users  amu 
              left JOIN assessment_attempts as aa 
              on aa.assessment_id = amu.assessment_id 
              LEFT JOIN assessment_mst as am ON amu.assessment_id = am.id 
              left JOIN assessment_mapping_user as ams on ams.user_id = amu.user_id AND ams.assessment_id=amu.assessment_id
              WHERE am.company_id = '" . $Company_id . "' AND ams.trainer_id = '" . $manager_id . "' 
              AND am.start_dttm BETWEEN '" . $YearStartDate . "' AND '" . $YearEndDate . "' ";
        if ($Day_type == '7_days') {
            $query .= "GROUP BY day(am.start_dttm)";
        } else {
            $query .= "GROUP BY wmonth";
        }
        // echo $query;die;
        $result = $this->db->query($query);
        $mappedarray = $result->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$mappedarray) > 0) {
                foreach ($mappedarray as $value) {
                    $PeriodArray[$value->wday] = $value->mapped;
                }
            }
            $data['mapped'] = $PeriodArray;
        } else {
            if (!empty((array)$mappedarray) > 0) {
                foreach ($mappedarray as $value) {
                    $PeriodArray[$value->wmonth] = $value->mapped;
                }
            }
            $data['mapped'] = $PeriodArray;
        }
        return $data;
    }

    public function raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id, $manager_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();
        $query = "SELECT  count(DISTINCT (amu.user_id)) as mapped,
                  month(am.start_dttm) as wmonth,
                  DATE_FORMAT(am.start_dttm,'%d') wday   
                  FROM assessment_allow_users as aau 
                  left join assessment_mst as am on aau.assessment_id = am.id 
                  LEFT join device_users as du on aau.user_id=du.user_id 
                  LEFT JOIN assessment_mapping_user amu ON aau.user_id=amu.user_id AND amu.assessment_id=aau.assessment_id
                  WHERE  am.status =1 AND du.istester=0 AND am.company_id='" . $Company_id . "'  AND amu.trainer_id='" . $manager_id . "' and
                  (am.start_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "')
                  GROUP BY wmonth";

        // echo $query;die;          
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->mapped;
            }
        }
        $ResultArray['mapped'] = $PeriodArray;
        return $ResultArray;
    }

    public function total_users_maped($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id)
    {
        $query = "SELECT count(DISTINCT(amu.user_id)) as currentmonth, DATE_FORMAT(am.start_dttm,'%d') wday,month(am.start_dttm) as wmonth 
                  FROM assessment_allow_users amu 
                  LEFT JOIN assessment_mst as am ON amu.assessment_id = am.id 
                  left JOIN assessment_mapping_user as ams on ams.user_id = amu.user_id AND ams.assessment_id=amu.assessment_id 
                  WHERE am.status =1 AND am.company_id = '" . $Company_id . "' AND ams.trainer_id = '" . $manager_id . "'
                  AND am.start_dttm BETWEEN '" . $monthstartdate . "' AND '" . $monthenddate . "' 
                  GROUP BY wmonth";

        $query1 = "SELECT count(DISTINCT(amu.user_id)) as months, DATE_FORMAT(am.start_dttm,'%d') wday,month(am.start_dttm) as wmonth 
                   FROM assessment_allow_users amu 
                   LEFT JOIN assessment_mst as am ON amu.assessment_id = am.id 
                   left JOIN assessment_mapping_user as ams on ams.user_id = amu.user_id AND ams.assessment_id=amu.assessment_id 
                   WHERE am.status =1 AND am.company_id = '" . $Company_id . "' AND ams.trainer_id = '" . $manager_id . "'
                   AND am.start_dttm BETWEEN '" . $lastmonthdate . "' AND '" . $lastmonthenddate . "' 
                   GROUP BY wmonth";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }

    public function RapsPlayedComplted($StartStrDt = '', $EndDate = '', $Day_type, $Company_id, $mapped_user_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $AssessArray = array();
        $cond = "";
        if ($StartStrDt != '' && $EndDate != '') {
            $cond .= " AND (am.start_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDate . "') ";
        }
        $query = "SELECT sum(attempts) as attempts,sum(completed) as completed, wmonth, wday 
         from (SELECT aa.assessment_id as assessment_id, COUNT(aa.user_id) as attempts, sum(aa.is_completed=1) as completed,
         DATE_FORMAT(aa.complete_dttm,'%d') wday, ";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(aa.complete_dttm,'%m-%Y') as wmonth  ";
        } else {
            $query .= "month(aa.complete_dttm) as wmonth  ";
        }
        $query .= "FROM `assessment_attempts` as aa 
        LEFT JOIN assessment_mst as am on am.id = aa.assessment_id
        LEFT join device_users as du on du.user_id = aa.user_id 
         WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 AND aa.user_id IN(" . implode(',', $mapped_user_id) . ") 
          $cond  
          GROUP BY assessment_id,aa.user_id) as main ";
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
                    $PeriodArray[$value->wday] = $value->attempts;
                    $AssessArray[$value->wday] = $value->completed;
                }
            }
            $ResultArray['played'] = $PeriodArray;
            $ResultArray['completed'] = $AssessArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->attempts;
                    $AssessArray[$value->wmonth] = $value->completed;
                }
            }
            $ResultArray['played'] = $PeriodArray;
            $ResultArray['completed'] = $AssessArray;
            return $ResultArray;
        }
    }
    public function raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id, $mapped_user_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();
        $playedArray = array();
        $query = "SELECT sum(attempts) as attempts,sum(completed) as completed, wmonth, wday 
                  from (SELECT aa.assessment_id as assessment_id, COUNT(aa.user_id) as attempts, 
                  sum(aa.is_completed=1) as completed, DATE_FORMAT(aa.complete_dttm,'%d') wday, 
                  month(aa.complete_dttm) as wmonth 
                  FROM `assessment_attempts` as aa 
                  LEFT JOIN assessment_mst as am on am.id = aa.assessment_id 
                  LEFT join device_users as du on du.user_id = aa.user_id 
                  WHERE am.STATUS = '1' AND am.company_id='" . $Company_id . "' AND du.istester=0 
                  AND aa.user_id IN(" . implode(',', $mapped_user_id) . ") 
                  AND (am.start_dttm BETWEEN '" . $StartStrDt . "' AND '" . $EndDtdate . "') 
                  GROUP BY assessment_id,aa.user_id) as main GROUP BY wmonth";
        // echo $query;die;
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
    // Reps Played and Completed Graph

    // total Reports Send Graph
    public function total_reports_sent_manager($StartStrDt, $EndDate, $Day_type, $Company_id, $userId)
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
                 WHERE is_sent=1 AND company_id='" . $Company_id . "' AND user_id in(" . implode(',', $userId) . ")
                  AND sent_at BETWEEN '$StartStrDt' AND '$EndDate' ";
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

    public function total_reports_sent_manager_last_30_60($WStartDate, $WEndDate, $Company_id, $userId)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();

        $query = "SELECT count(*) as total,month(sent_at) as wmonth , DATE_FORMAT(sent_at,'%d') AS wday  FROM trainee_report_schedule
                    WHERE is_sent=1 AND company_id='" . $Company_id . "' AND user_id in(" . implode(',', $userId) . ")
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

    public function Month_Wise_Count_Send_manager($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $userId)
    {
        $query = "SELECT count(*) as currentmonth,month(sent_at) as wmonth , DATE_FORMAT(sent_at,'%d') AS wday  
                  FROM trainee_report_schedule WHERE is_sent=1 
                  AND company_id='" . $Company_id . "' AND user_id in(" . implode(',', $userId) . ") AND sent_at BETWEEN '$monthstartdate' AND '$monthenddate' GROUP BY month(sent_at)";

        $query1 = "SELECT count(*) as months,month(sent_at) as wmonth , DATE_FORMAT(sent_at,'%d') AS wday  
                   FROM trainee_report_schedule WHERE is_sent=1 
                   AND company_id='" . $Company_id . "' AND user_id in(" . implode(',', $userId) . ") AND sent_at BETWEEN '$lastmonthdate' AND '$lastmonthenddate' GROUP BY month(sent_at)";


        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // Total Reports Sent End Here

    // Total Question Mapped
    public function assessmentmangerwise($manager_id)
    {
        $query = "SELECT DISTINCT(amu.assessment_id) FROM  assessment_mapping_user amu WHERE amu.trainer_id = '" . $manager_id . "' ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }


    public function total_questions_mapped($StartStrDt, $EndDate, $Day_type, $Company_id, $manager_id)
    {
        $ResultArray = array();
        $PeriodArray = array();
        $query = "SELECT assessment_id,count(question_id) as question_id ,wmonth,wday 
                 from (SELECT art.assessment_id as assessment_id, art.question_id as question_id, ";
        if ($Day_type == '365_days') {
            $query .= "DATE_FORMAT(am.start_dttm,'%m-%Y') as wmonth,";
        } else {
            $query .= "month(am.start_dttm) as wmonth,";
        }
        $query .= " DATE_FORMAT(am.start_dttm,'%d')as wday 
                 FROM assessment_mst as am LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id 
                 LEFT JOIN assessment_trans art ON amu.assessment_id=art.assessment_id 
                 WHERE am.company_id='" . $Company_id . "' AND amu.trainer_id='" . $manager_id . "' AND am.start_dttm BETWEEN '" . $StartStrDt . "' and '" . $EndDate . "' 
                 GROUP BY art.assessment_id )as main ";

        if ($Day_type == '7_days') {
            $query .= "GROUP BY wday ORDER BY assessment_id ASC";
        } else {
            $query .= "GROUP BY wmonth ORDER BY assessment_id ASC";
        }
        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if ($Day_type == '7_days') {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wday] = $value->question_id;
                }
            }
            $ResultArray['questions'] = $PeriodArray;
            return $ResultArray;
        } else {
            if (!empty((array)$Accuracy) > 0) {
                foreach ($Accuracy as $value) {
                    $PeriodArray[$value->wmonth] = $value->question_id;
                }
            }
            $ResultArray['questions'] = $PeriodArray;
            return $ResultArray;
        }
    }

    public function total_questions_mapped_30_60($WStartDate, $WEndDate, $Company_id, $manager_id)
    {
        $StartStrDt = $WStartDate;
        $EndDtdate = $WEndDate;
        $PeriodArray = array();

        $query = "SELECT assessment_id,count(question_id) as question_id ,wmonth,wday 
                  from (SELECT art.assessment_id as assessment_id, art.question_id as question_id, 
                  month(am.start_dttm) as wmonth, DATE_FORMAT(am.start_dttm,'%d')as wday 
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id 
                  LEFT JOIN assessment_trans art ON amu.assessment_id=art.assessment_id 
                  WHERE am.company_id='" . $Company_id . "' AND amu.trainer_id='" . $manager_id . "' AND am.start_dttm 
                  BETWEEN '" . $StartStrDt . "' and '" . $EndDtdate . "' GROUP BY art.assessment_id)as main GROUP BY wmonth ORDER BY assessment_id ASC";

        $result = $this->db->query($query);
        $Accuracy = $result->result();
        if (!empty((array)$Accuracy) > 0) {
            foreach ($Accuracy as $value) {
                $PeriodArray = $value->question_id;
            }
        }
        return $PeriodArray;
    }

    public function Month_wise_count_questions($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id)
    {
        $query = "SELECT count(question_id) as currentmonth ,wmonth
                  from (SELECT art.question_id as question_id ,month(am.start_dttm) as wmonth
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id 
                  LEFT JOIN assessment_trans art ON amu.assessment_id=art.assessment_id 
                  WHERE am.company_id='" . $Company_id . "' AND amu.trainer_id='" . $manager_id . "' 
                  AND am.start_dttm BETWEEN '" . $monthstartdate . "' and '" . $monthenddate . "' 
                  GROUP BY art.assessment_id )as main GROUP by wmonth";

        $query1 = "SELECT count(question_id) as months ,wmonth
                   from (SELECT art.question_id as question_id ,month(am.start_dttm) as wmonth
                   FROM assessment_mst as am 
                   LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id 
                   LEFT JOIN assessment_trans art ON amu.assessment_id=art.assessment_id 
                   WHERE am.company_id='" . $Company_id . "' AND amu.trainer_id='" . $manager_id . "' 
                   AND am.start_dttm BETWEEN '" . $lastmonthdate . "' and '" . $lastmonthenddate . "' 
                   GROUP BY art.assessment_id )as main GROUP by wmonth";

        $result = $this->db->query($query);
        $data['Latestmonth'] = $result->result_array();

        $OldMonth = $this->db->query($query1);
        $data['Oldmonth'] = $OldMonth->result_array();
        return $data;
    }
    // End Here

    // Adoption by reps
    public function assessment_wise_manager($assessment_id1, $manager_id)
    {
        $query = "SELECT DISTINCT a.user_id as user_id,CONCAT(du.firstname,' ',du.lastname) as user_name 
                FROM `assessment_mapping_user` as a 
                LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id 
                LEFT JOIN device_users as du ON du.user_id=a.user_id 
                where 1=1 AND a.assessment_id = '" . $assessment_id1 . "' AND a.trainer_id='" . $manager_id . "' order by user_id";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function GetAssessmentName($Assessment_id)
    {
        $query = "SELECT assessment FROM `assessment_mst` WHERE id= '" . $Assessment_id . "' ";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function get_user_name($assessment_id1, $manager_id, $user_id)
    {
        $query = "SELECT DISTINCT a.user_id as user_id,CONCAT(du.firstname,' ',du.lastname) as user_name 
                FROM `assessment_mapping_user` as a 
                LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id 
                LEFT JOIN device_users as du ON du.user_id=a.user_id 
                where 1=1 AND a.assessment_id = '" . $assessment_id1 . "' and a.user_id in(" . implode(',', $user_id) . ")  AND a.trainer_id='" . $manager_id . "' order by a.user_id";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    public function get_user_name_last($assessment_id1, $manager_id)
    {
        $query = "SELECT DISTINCT a.user_id as user_id,CONCAT(du.firstname,' ',du.lastname) as user_name 
                  FROM `assessment_mapping_user` as a 
                  LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id 
                  LEFT JOIN device_users as du ON du.user_id=a.user_id 
                  where 1=1 AND a.assessment_id = '" . $assessment_id1 . "' AND a.trainer_id='" . $manager_id . "' order by a.user_id LIMIT 5";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
    //end here

    //
    public function LastExpiredAssessment($CurrentDate, $Company_id, $manager_id)
    {
        $query = "SELECT am.id,am.assessment,am.end_dttm 
                  FROM assessment_mst as am 
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  WHERE end_dttm <= '" . $CurrentDate . "' AND am.company_id='" . $Company_id . "' AND amu.trainer_id='" . $manager_id . "' ORDER BY am.end_dttm DESC LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }

    public function Getplayed_complted_managerwise($Assessment_id, $Company_id, $manager_id, $user_id)
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
                  WHERE am.assessment_id ='" . $Assessment_id . "' and am.user_id in(" . implode(',', $user_id) . ") AND am.trainer_id='".$manager_id."' AND amt.company_id='" . $Company_id . "'
                 GROUP BY am.user_id ORDER by am.user_id ASC) as main) as main2";
        // echo $query;die;
        $result = $this->db->query($query);
        $data = $result->result_array();
        return $data;
    }
}