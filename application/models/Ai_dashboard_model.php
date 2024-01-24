<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_dashboard_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_box_i_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        // $query = "SELECT count(*) as total FROM `assessment_mst` AS am  WHERE
        // am.STATUS = '1' AND am.company_id='".$company_id."' 
		// AND (start_dttm <= '".$_statistics_start_date."' OR end_dttm >= '".$_statistics_start_date."')";
		$query = "SELECT count(*) as total FROM `assessment_mst` AS am  WHERE
        am.STATUS = '1' AND am.company_id='".$company_id."' 
		AND (date(start_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."' 
		OR date(end_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
		OR date(start_dttm) <= '".$_statistics_start_date."' AND date(end_dttm) >= '".$_statistics_end_date."')";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_box_vi_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        // $query = "SELECT count(a.question_id) as questions from assessment_trans a 
		// 		JOIN assessment_mst am ON a.assessment_id = am.id
		// 		WHERE am.status=1 AND am.company_id='".$company_id."' 
		// 		AND am.start_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
		// 		OR am.end_dttm BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
		// 		OR am.start_dttm <= '".$_statistics_start_date."' AND am.end_dttm >= '".$_statistics_start_date."'";
        $query = "SELECT count(*) as total FROM trainee_report_schedule
                WHERE is_sent=1 AND company_id='".$company_id."' 
                AND date(sent_at) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_box_ii_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $query = "SELECT DISTINCT count(aau.user_id) as total FROM assessment_allow_users as aau
				left join assessment_mst as am on aau.assessment_id = am.id
				LEFT join device_users as du on aau.user_id=du.user_id
				WHERE am.STATUS = '1' AND am.company_id='".$company_id."' AND du.istester=0 
				AND (date(am.start_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR date(am.end_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR date(am.start_dttm) <= '".$_statistics_start_date."' AND date(am.end_dttm) >= '".$_statistics_end_date."')";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_box_iii_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
        $query = "SELECT sum(played) as played,sum(completed) as completed from (
				SELECT DISTINCT assessment_id,aa.user_id,count(aa.user_id) as played, sum(IF(is_completed=1,1,0)) as completed 
				FROM assessment_attempts as aa
				left join assessment_mst as am on aa.assessment_id = am.id 
				LEFT join device_users as du on du.user_id = aa.user_id
				WHERE am.STATUS = '1' AND am.company_id='".$company_id."' AND du.istester=0 
				AND (date(am.start_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR date(am.end_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR (date(am.start_dttm) <= '".$_statistics_start_date."' AND date(am.end_dttm) >= '".$_statistics_start_date."'))
                AND (DATE_FORMAT(aa.complete_dttm,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."')
                group by assessment_id,aa.user_id
			) as main";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_box_iv_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
		$query = "SELECT count(*) as total FROM (
                SELECT DISTINCT ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id FROM assessment_results as ar
                left join assessment_mst as am on ar.assessment_id = am.id 
                left join assessment_attempts as aa on ar.assessment_id = aa.assessment_id and ar.user_id = aa.user_id
                LEFT JOIN device_users as du ON ar.user_id=du.user_id
                WHERE ar.ftp_status=1 and am.status=1 and aa.ftpto_vimeo_uploaded = 1 and aa.is_completed = 1 AND du.istester=0
                AND (date(am.start_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR date(am.end_dttm) BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'
				OR (date(am.start_dttm) <= '".$_statistics_start_date."' AND am.end_dttm >= '".$_statistics_start_date."'))
                AND (DATE_FORMAT(ar.addeddate,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."')) as main";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_box_v_statistics($company_id,$_statistics_start_date,$_statistics_end_date){
		// $query = "SELECT count(*) as total FROM (
        //     select company_id,assessment_id,user_id,trans_id,question_id from ai_schedule where CONCAT('C',company_id,'A',assessment_id,'U',user_id,'T',trans_id,'Q',question_id) in (
        //     SELECT DISTINCT(CONCAT('C',a.company_id,'A',a.assessment_id,'U',a.user_id,'T',a.trans_id,'Q',a.question_id)) FROM assessment_results as a
        //     left join assessment_mst as am on a.assessment_id = am.id 
        //     left join assessment_attempts as aa on a.assessment_id = aa.assessment_id and a.user_id = aa.user_id
        //     LEFT JOIN device_users as du ON a.user_id=du.user_id
        //     WHERE am.status = 1 and aa.ftpto_vimeo_uploaded = 1 and aa.is_completed = 1 AND du.istester=0) AND task_status = 1 
		// 	AND DATE_FORMAT(task_status_dttm,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."') as main";
        $query = "SELECT count(*) as total FROM `ai_schedule` as ai 
                LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id 
                WHERE am.status=1 and ai.company_id='$company_id' AND ai.task_status = 1 AND du.istester=0 
                AND DATE_FORMAT(ai.task_status_dttm,'%Y-%m-%d') BETWEEN '".$_statistics_start_date."' AND '".$_statistics_end_date."'";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "  SELECT am.id,am.company_id,am.assessment_type,                     
                    am.assessment,at.description AS assessment_type, art.description as report_type, 
                    IF(is_situation=1,'Situation','Question') AS question_type, 
                    am.status,DATE_FORMAT(am.start_dttm,'%d-%m-%Y %H:%i') AS start_dttm, 
                    DATE_FORMAT(am.end_dttm,'%d-%m-%Y %H:%i') AS end_dttm,
					IFNULL(ac.show_reports,0) as show_reports,IFNULL(ac.show_dashboard,0) as show_dashboard,IFNULL(ac.show_pwa,0) as show_pwa, IFNULL(ac.show_ranking,0) as show_ranking, count(DISTINCT ar.question_id) as que_mapped
                FROM assessment_mst am
                        LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id
                        LEFT JOIN assessment_report_type as art on art.id=am.report_type
						LEFT JOIN assessment_type as at ON at.id = am.assessment_type
                        LEFT JOIN assessment_trans ar ON ar.assessment_id = am.id 
						LEFT JOIN ai_cronreports ac ON ac.assessment_id=am.id ";
        $query .= " $dtWhere GROUP BY am.id $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        
        $query = " SELECT  am.id as total FROM assessment_mst am "
                . " LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id "
                . " LEFT JOIN assessment_type at ON at.id = am.assessment_type"
                // . " LEFT JOIN assessment_results ar ON ar.assessment_id = atr.assessment_id "
                . " $dtWhere GROUP BY am.id ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array)$data_array);
        return $data;
    }

    public function getAssessmentUserCount($company_id,$assessment_id){
		$query = "SELECT COUNT(DISTINCT aau.id) as mapped,COUNT(DISTINCT ar.user_id) as played FROM assessment_mst am 
				LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
				LEFT JOIN assessment_results ar ON am.id=ar.assessment_id  
                LEFT JOIN device_users du ON du.user_id=aau.user_id 
				WHERE am.company_id = '".$company_id."' and am.id = '".$assessment_id."' AND du.istester=0";
		$result = $this->db->query($query);
        return $result->row();
	}
    public function getUserCount($company_id,$assessment_ids){
		$query = "SELECT SUM(mapped) as mapped, sum(played) as played, id as assessment_id FROM 
            (SELECT COUNT(DISTINCT aau.id) as mapped,0 as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
            LEFT JOIN device_users du ON du.user_id=aau.user_id 
            WHERE am.company_id = '".$company_id."' and am.id IN (".implode(',',$assessment_ids).") AND du.istester=0 GROUP BY am.id
                UNION ALL
            SELECT 0 as mapped,COUNT(DISTINCT ar.user_id) as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_results ar ON am.id=ar.assessment_id 
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
            WHERE am.company_id = '".$company_id."' and am.id IN (".implode(',',$assessment_ids).") AND du.istester=0 GROUP BY am.id)
        as main GROUP BY assessment_id ORDER BY assessment_id DESC";
		$result = $this->db->query($query);
        return $result->result();
	}
	
	public function getAssessmentVideoCount($company_id,$assessment_id){
		$query = "SELECT count(*) as total_video_processed FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
				WHERE am.status=1 and ai.company_id='".$company_id."' AND ai.task_status = 1 AND du.istester=0 AND am.id='".$assessment_id."'";
        $result = $this->db->query($query);
        return $result->row();
	}
    public function getVideoCount($company_id,$assessment_ids){
		$query = "SELECT count(*) as total_video_processsed, am.id as assessment_id FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
				WHERE am.status=1 and ai.company_id='".$company_id."' AND ai.task_status = 1 AND am.id IN (".implode(',',$assessment_ids).") AND du.istester=0 
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
	}

    public function getVideoUploaded($company_id, $assessment_id){
        $system_date = date('Y-m-d H:i:s');
        $query = "SELECT count(*) as total FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id
				WHERE am.status=1 and ar.company_id = '".$company_id."' and ar.assessment_id= '".$assessment_id."' and
                ar.ftp_status=1 and du.istester=0";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function getUploadedVideos($company_id, $assessment_ids){
        $query = "SELECT count(*) as total_video_uploaded,am.id as assessment_id FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id
				WHERE am.status=1 and ar.company_id = '".$company_id."' AND ar.assessment_id IN (".implode(',',$assessment_ids).") AND ar.ftp_status=1 AND du.istester=0
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getAssessmentEmailCount($company_id){
		$query = "SELECT assessment_id,count(DISTINCT user_id) as scheduled,sum(is_sent) as sent FROM `trainee_report_schedule` WHERE company_id='$company_id' GROUP by assessment_id";
		$result = $this->db->query($query);
		return $result->result();
	}

    public function get_distinct_participants($company_id,$assessment_id){
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
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                        LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                        LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id
						LEFT JOIN trainee_report_schedule trs ON ar.user_id=trs.user_id AND ar.assessment_id=trs.assessment_id						
                    WHERE
                        ar.company_id = '".$company_id."' AND ar.assessment_id IN (".$assessment_id.") AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                        AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1
                    ORDER BY
                        ar.user_id, ar.trans_id, trs.scheduled_at DESC 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                ORDER BY
                   main.user_id, main.trans_id) AS final GROUP BY user_id ORDER BY user_id";
        
        $result = $this->db->query($query);
		$data_array = $result->result_array();
		$data['dtTotalRecords'] = count((array)$data_array);
		
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        return $data;
    }

    public function get_question($assessment_id) {
        $query = " SELECT art.id,art.question_id ,aq.question,art.parameter_id FROM assessment_trans art "
                . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
                . " where art.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }

	public function LoadQuestionDataTableRefresh($assessment_id, $question_id){
		$query  = "SELECT aq.question, aq.id as question_id, CONCAT('https://player.vimeo.com/video/',ar.vimeo_uri) as best_url, 
         abv.best_video_link as ideal_url
        FROM ai_subparameter_score as ps 
        LEFT JOIN assessment_results AS ar ON ps.company_id = ar.company_id 
        AND ps.assessment_id = ar.assessment_id
        AND ps.user_id = ar.user_id
        AND ps.trans_id = ar.trans_id
        AND ps.question_id = ar.question_id  
        LEFT JOIN assessment_question AS aq on aq.id=ps.question_id       
        LEFT JOIN ai_best_ideal_video AS abv ON abv.assessment_id=ps.assessment_id
        WHERE ps.parameter_type = 'parameter' AND  ps.assessment_id = '".$assessment_id."' AND ps.question_id = '".$question_id."' AND (ps.user_id) IN 
        (SELECT temp.user_id FROM (
        SELECT main.user_id, max(main.score) as score FROM (
        SELECT ps.user_id, IFNULL(ROUND(sum(ps.score)/count(ps.question_id),2),0) as score FROM ai_subparameter_score as ps
        WHERE ps.parameter_type ='parameter' AND        
        ps.assessment_id = '".$assessment_id."' AND ps.question_id = '".$question_id."'
        group by ps.user_id,ps.question_id order by score desc) as main) as temp) LIMIT 0,1";
        //echo $query;
		$result = $this->db->query($query);
        return $result->row();	
	}

	public function ideal_url_link($assessment_id, $question_id)
    {
        $query="SELECT abv.best_video_link as ideal_url FROM ai_best_ideal_video as abv 
                WHERE abv.assessment_id='".$assessment_id."' AND abv.question_id  ='".$question_id."'
                ";
        $result = $this->db->query($query);
        return $result->row();
    }
}
