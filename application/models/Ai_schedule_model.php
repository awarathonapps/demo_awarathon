<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_schedule_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    } 
    public function get_participants($company_id,$assessment_id){
        $query  = "SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed 
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                        LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                    WHERE
                        ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id."' AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                        AND aa.is_completed =1
                    ORDER BY
                        ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                ORDER BY
                   main.user_id, main.trans_id";
        
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_schedule($company_id,$asssessment_id){
        $query  = "SELECT process_status from ai_cronjob WHERE company_id='".$company_id."' and assessment_id='".$asssessment_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_existing_task($company_id,$asssessment_id){
        $query  = "SELECT * from ai_schedule WHERE company_id='".$company_id."' and assessment_id='".$asssessment_id."'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_box_i_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $system_date = date('Y-m-d H:i:s');
        $query = "SELECT count(*) as total FROM `assessment_mst` AS am  WHERE
        am.STATUS = '1' AND am.company_id='".$company_id."' 
		AND start_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."' 
		OR end_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
		OR start_dttm <= '".$_statistics_start_date."' AND end_dttm >= '".$_statistics_start_date."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_box_vi_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $system_date = date('Y-m-d H:i:s');
        $query = "SELECT count(a.question_id) as questions from assessment_trans a 
				JOIN assessment_mst am ON a.assessment_id = am.id
				WHERE am.status=1 AND am.company_id='".$company_id."' 
				AND am.start_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR am.end_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR am.start_dttm <= '".$_statistics_start_date."' AND am.end_dttm >= '".$_statistics_start_date."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_box_ii_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $system_date = date('Y-m-d H:i:s');
        $query = "SELECT DISTINCT count(aau.user_id) as total FROM assessment_allow_users as aau
				left join assessment_mst as am on aau.assessment_id = am.id
				LEFT join device_users as du on aau.user_id=du.user_id
				WHERE am.STATUS = '1' AND am.company_id='".$company_id."' AND du.istester=0 
				AND am.start_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR am.end_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR am.start_dttm <= '".$_statistics_start_date."' AND am.end_dttm >= '".$_statistics_start_date."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_box_iii_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $system_date = date('Y-m-d H:i:s');
        $query = "SELECT sum(played) as played,sum(completed) as completed from (
				SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, sum(IF(is_completed=1,1,0)) as completed 
				FROM assessment_attempts as aa
				left join assessment_mst as am on aa.assessment_id = am.id 
				LEFT join device_users as du on du.user_id = aa.user_id
				WHERE am.STATUS = '1' AND am.company_id='".$company_id."' AND du.istester=0 
				AND am.start_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR am.end_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR am.start_dttm <= '".$_statistics_start_date."' AND am.end_dttm >= '".$_statistics_start_date."'
                AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."')
                group by assessment_id,aa.user_id
			) as main";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_box_iv_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $system_date = date('Y-m-d H:i:s');
        // $query = "SELECT count(*) as total FROM `assessment_results` WHERE company_id = '".$company_id."' and ftp_status=1";
		// $query = "SELECT count(*) as total FROM (
                // SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id FROM assessment_results as ar
                // left join assessment_mst as am on ar.assessment_id = am.id 
                // left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id and ar.user_id = aa.user_id
                // WHERE ar.ftp_status=1 and am.status=1 and aa.ftpto_vimeo_uploaded = 1 and aa.is_completed = 1 AND am.start_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."' OR am.end_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."' 
                // AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."')) as main";
		$query = "SELECT count(*) as total FROM (
                SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id FROM assessment_results as ar
                left join assessment_mst as am on ar.assessment_id = am.id 
                left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id and ar.user_id = aa.user_id
                WHERE ar.ftp_status=1 and am.status=1 and aa.ftpto_vimeo_uploaded = 1 and aa.is_completed = 1
                AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."')) as main";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_box_v_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $system_date = date('Y-m-d H:i:s');
        // $query = "SELECT count(*) as total FROM `ai_schedule` where company_id='".$company_id."' and task_status = 1";
        // $query = "SELECT count(*) as total FROM `ai_schedule` as ai 
				// LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                // LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
				// WHERE am.status=1 and ai.company_id='".$company_id."' AND ai.task_status = 1 AND du.istester=0 AND am.start_dttm >='".$_statistics_start_date."' AND am.end_dttm <='".$_statistics_end_date."'";
		$query = "SELECT count(*) as total FROM (
            select company_id,assessment_id,user_id,trans_id,question_id from ai_schedule where CONCAT('C',company_id,'A',assessment_id,'U',user_id,'T',trans_id,'Q',question_id) in (
            SELECT DISTINCT(CONCAT('C',a.company_id,'A',a.assessment_id,'U',a.user_id,'T',a.trans_id,'Q',a.question_id)) FROM assessment_results as a
            left join assessment_mst as am on a.assessment_id = am.id 
            left join assessment_attempts as aa on a.assessment_id = aa.assessment_id and a.user_id = aa.user_id
            WHERE am.status = 1 and aa.ftpto_vimeo_uploaded = 1 and aa.is_completed = 1) AND task_status = 1 
			AND DATE_FORMAT(task_status_dttm,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."') as main";
        $result = $this->db->query($query);
        return $result->row();
    }
}
