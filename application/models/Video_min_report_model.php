<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Video_min_report_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }    
    public function getuser_played_data($dbname,$where,$rpt_period=''){
       if($rpt_period !='all'){ 
            if($rpt_period == "weekly" || $rpt_period == "monthly"){
                $columname =" DATE_FORMAT(start_time,'%d') ";
                $groupby = " date(start_time) ";
            }elseif($rpt_period == "yearly"){
                $columname =" month(start_time) ";
                $groupby = " month(start_time) ";
            }
       }else{
           $columname =" year(start_time) ";
           $groupby = " year(start_time) ";
       }
		$lcsqlstr  = "SELECT company_id,".$columname." as period, sum(total_users) as total_users,CONCAT((FLOOR(sum(video_duration)/60)),'.',(sum(video_duration)%60)) as playing_duration FROM 
		(SELECT company_id, date(start_time) as start_time, count(DISTINCT user_id) as total_users,sum(video_duration) as video_duration 
		FROM $dbname.assessment_results  $where AND start_time !='' group by assessment_id,date(start_time)
		) as a group by ". $groupby;
		
        /*$lcsqlstr  = " SELECT company_id,".$columname." as period, count(distinct user_id) as total_users,CONCAT((FLOOR(sum(video_duration)/60)),'.',(sum(video_duration)%60)) as playing_duration FROM ".$dbname.".assessment_results  "; 
        $lcsqlstr  .= " $where and start_time !='' group by ". $groupby;*/
        
        $result = $this->db->query($lcsqlstr);
//        return $result->result();
        $DurationData = $result->result();
        $PeriodArray = array();
        $UserArray = array();
            if (count((array)$DurationData) > 0) {
                foreach ($DurationData as $value) {
                    $PeriodArray[$value->period] = $value->playing_duration;
                    $UserArray[$value->period] = $value->total_users;
                }
            }
            $ResultArray['duration']=$PeriodArray;
            $ResultArray['users']=$UserArray;
        return $ResultArray;
    } 
    public function getassessment_played_data_old($dbname,$where){
        
        $lcsqlstr  = "SELECT company_id, sum(total_users) as total_users,CONCAT((FLOOR(sum(video_duration)/60)),'.',(sum(video_duration)%60)) as playing_duration, "
                . " CONCAT((FLOOR((sum(total_duration)*sum(total_users))/60)),'.',((sum(total_duration)*sum(total_users))%60)) as total_duration,
					CONCAT((FLOOR(((sum(total_duration)*sum(total_users))-sum(video_duration))/60)),'.',(((sum(total_duration)*sum(total_users))-sum(video_duration))%60)) as unutilize_duration,assessment "
                . " FROM (SELECT ar.company_id, count(DISTINCT user_id) as total_users,sum(video_duration) as video_duration,sum(aq.response_timer) as total_duration,am.assessment "
                . " FROM $dbname.assessment_results ar"
                . " LEFT JOIN $dbname.assessment_question aq ON aq.id=ar.question_id "
                . " LEFT JOIN $dbname.assessment_mst am ON am.id=ar.assessment_id "
                . " $where AND ar.start_time !='' group by ar.assessment_id,date(start_time)) as a";
				
        $result = $this->db->query($lcsqlstr);
        return $result->result();
    }
	 public function getassessment_played_data($dbname,$where){
       
        $lcsqlstr  = "SELECT company_id, sum(total_users) as total_users,CONCAT((FLOOR(sum(video_duration)/60)),'.',(sum(video_duration)%60)) as playing_duration, "
                . " CONCAT((FLOOR(SUM(total_duration)/60)),'.',(SUM(total_duration)%60)) AS total_duration,
                    CONCAT((FLOOR((SUM(total_duration)- SUM(video_duration))/60)),'.',((SUM(total_duration)- SUM(video_duration))%60)) AS unutilize_duration,assessment "
                . " FROM (SELECT ar.company_id, count(DISTINCT user_id) as total_users,sum(video_duration) as video_duration,sum(aq.response_timer) as response_time,(SUM(aq.response_timer)*COUNT(DISTINCT user_id)) AS total_duration,am.assessment "
                . " FROM $dbname.assessment_results ar"
                . " LEFT JOIN $dbname.assessment_question aq ON aq.id=ar.question_id "
                . " LEFT JOIN $dbname.assessment_mst am ON am.id=ar.assessment_id "
                . " $where AND ar.start_time !='' group by ar.assessment_id) as a";

        $result = $this->db->query($lcsqlstr);
        return $result->result();
    }

}
