<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_email_schedule_model extends CI_Model {
    public function __construct() {
        parent::__construct();
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
		//SELECT am.id,am.company_id, am.assessment, am.status,DATE_FORMAT(am.start_dttm,'%d-%m-%Y %H:%i') AS start_dttm, DATE_FORMAT(am.end_dttm,'%d-%m-%Y %H:%i') AS end_dttm,COUNT(DISTINCT aau.id) as mapped,COUNT(DISTINCT ar.user_id) as played FROM assessment_mst am left JOIN assessment_allow_users aau on am.id=aau.assessment_id LEFt JOIN assessment_results ar ON am.id=ar.assessment_id  WHERE am.company_id = 59 GROUP BY am.id ORDER BY am.id desc LIMIT 0,10
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        
        $query = " SELECT  am.id as total FROM assessment_mst am "
                . " LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id "
                . " LEFT JOIN assessment_type at ON at.id = am.assessment_type"
                . " LEFT JOIN assessment_results ar ON ar.assessment_id = atr.assessment_id "
                . " $dtWhere GROUP BY am.id ";
        //echo $query;
        //exit;
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array)$data_array);
        return $data;
    }

    public function getAssessmentUserCount($company_id,$assessment_id){
		$query = "SELECT COUNT(DISTINCT aau.id) as mapped,COUNT(DISTINCT ar.user_id) as played FROM assessment_mst am 
				LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
				LEFt JOIN assessment_results ar ON am.id=ar.assessment_id  
				WHERE am.company_id = '".$company_id."' and am.id = '".$assessment_id."'";
		$result = $this->db->query($query);
        return $result->row();
	}
	
	public function getAssessmentVideoCount($company_id,$assessment_id){
		$query = "SELECT count(*) as total_video_processed FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
				WHERE am.status=1 and ai.company_id='".$company_id."' AND ai.task_status = 1 AND du.istester=0 AND am.id='".$assessment_id."'";
        $result = $this->db->query($query);
        return $result->row();
	}
    public function getVideoUploaded($company_id, $assessment_id){
        $system_date = date('Y-m-d H:i:s');
        // $query = "SELECT count(*) as total FROM `assessment_results` WHERE company_id = '".$company_id."' and ftp_status=1";
        $query = "SELECT count(*) as total FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id
				WHERE am.status=1 and ar.company_id = '".$company_id."' and ar.assessment_id= '".$assessment_id."' and
                ar.ftp_status=1 and du.istester=0";
        $result = $this->db->query($query);
        return $result->row();
    }
	public function LoadCandidateDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id){
		$query  = "SELECT distinct user_id,user_name,email,mobile FROM (SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,du.email,du.mobile,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed 
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                        LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                        LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id 
					".$dtWhere." AND ar.assessment_id IN (".$assessment_id.") AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                        AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1
                    ORDER BY
                        ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                ORDER BY
                   main.user_id, main.trans_id) AS final $dtOrder ";
		$result = $this->db->query($query);
        $data_array = $result->result_array();
		$data['dtTotalRecords'] = count((array)$data_array);
		
		$query .= " $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        return $data;
	}
    /*public function get_question($assessment_id)
    {
        $query = "SELECT DISTINCT ar.question_id, aq.question FROM
        assessment_results AS ar
        LEFT JOIN company AS c ON ar.company_id = c.id
        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
        LEFT JOIN assessment_question AS aq ON ar.question_id = aq.id 
		WHERE ar.assessment_id = '".$assessment_id."' AND ar.trans_id > 0 AND ar.question_id > 0 
		ORDER BY ar.user_id, ar.trans_id ";
        $result = $this->db->query($query);
        return $result->result();
    }*/
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
        $query="select abv.best_video_link as ideal_url from ai_best_ideal_video as abv 
                where abv.assessment_id='".$assessment_id."' AND abv.question_id  ='".$question_id."'
                ";
        $result = $this->db->query($query);
        return $result->row();
    }
	public function get_distinct_participants($company_id,$assessment_id){
            $query  = "SELECT distinct company_id,assessment_id,user_id,user_name,email,mobile,is_sent FROM (SELECT
            main.*,@dcp AS previous,
            CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
            @dcp := main.user_id AS current,
            CONCAT(main.user_id,'-',main.question_id) as uid	 
        FROM(
            SELECT DISTINCT
                ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                CONCAT( du.firstname, ' ', du.lastname ) AS user_name,du.email,du.mobile,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed,trs.is_sent
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
        public function getAssessmentEmailCount($company_id){
        $query = "SELECT assessment_id,count(DISTINCT user_id) as scheduled,sum(is_sent) as sent FROM `trainee_report_schedule` WHERE company_id='$company_id' GROUP by assessment_id";
        $result = $this->db->query($query);
        return $result->result();
        }

        }