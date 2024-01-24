<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_email_cron_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
	
	public function get_cron_status(){
		$query = "SELECT schedule_status,cron_status FROM email_schedule";
		$result = $this->db->query($query);
        return $result->result();
	}
	
	public function set_cron_status($status){
        $query = "UPDATE email_schedule SET cron_status=".$status."";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
        } else {
            return FALSE;
        }
    }
	
    public function get_schedule_users_data($company_id, $assessment_id, $trainee_ids, $sendAll){
		// $query = "SELECT DISTINCT amu.user_id,du.company_id,du.email FROM assessment_allow_users amu 
				// LEFT JOIN device_users du on du.user_id=amu.user_id 
				// LEFT JOIN ai_schedule ai on ai.user_id=amu.user_id 
				// WHERE amu.assessment_id IN (".$assessment_id.") AND ai.xls_imported=1 
				// AND amu.user_id NOT IN (SELECT user_id from trainee_report_schedule WHERE is_sent=0 and assessment_id IN (".$assessment_id."))";
		$query = "SELECT distinct user_id,company_id,assessment_id,email FROM (SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,du.email,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed 
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                        LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                        LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id 
                    WHERE
                    ar.company_id = '".$company_id."' AND ar.assessment_id IN (".$assessment_id.") AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                        AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1 ";
		if(!empty($trainee_ids)){
			$query .= " AND ar.user_id IN (".$trainee_ids.") ";
		}
		$condition = ($sendAll == 1) ? " AND is_sent = 0 " : '';
		$query .=   " AND ar.user_id NOT IN (SELECT user_id from trainee_report_schedule WHERE assessment_id IN (".$assessment_id.") ".$condition." AND attempt<3 )
                    ORDER BY
                        ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter
                ";
		$query .= " ORDER BY main.user_id, main.trans_id) AS final ORDER BY `final`.`user_id`  DESC";
		// $query .= " LIMIT 0,5";
		$result = $this->db->query($query);
        return $result->result();
	}
	
	public function get_assessment_data($assessment_id){
        $query  = "SELECT assessment,date_format(start_dttm, '%d-%m-%Y') as start_dttm,date_format(end_dttm, '%d-%m-%Y') as end_dttm,report_type FROM `assessment_mst` WHERE id='".$assessment_id."'";
        $result = $this->db->query($query);
        return $result->result();
    }

	public function get_scheduled_trainee_data(){
		$query = "SELECT ts.id,du.user_id,du.company_id,ts.email,concat(du.firstname,' ',du.lastname) as name,ts.attempt,am.id as assessment_id,am.assessment,date_format(am.start_dttm, '%d-%m-%Y') as start_dttm,date_format(am.end_dttm, '%d-%m-%Y') as end_dttm,am.report_type 
				FROM trainee_report_schedule ts LEFT JOIN device_users du ON ts.user_id = du.user_id LEFT JOIN assessment_mst am ON ts.assessment_id=am.id 
				WHERE ts.is_sent=0 AND ts.attempt < 3 LIMIT 0,50";
		$result = $this->db->query($query);
        return $result->result();
	}
}