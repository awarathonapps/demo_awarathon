<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Ai_reports_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_assessments()
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
                FROM assessment_mst am 
                LEFT JOIN assessment_report_type as art on art.id=am.report_type
				WHERE am.status = 1
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_all_assessment()
    {
        $query = "SELECT distinct ap.id as assessment_id, CONCAT('[', ap.id,'] ', ap.assessment, ' [', art.description, ']') as assessment, if(DATE_FORMAT(ap.end_dttm,'%y-%m-%d %H:%i')<=CURDATE(),'Expired','Live') AS status
                FROM assessment_mst ap 
                LEFT JOIN assessment_report_type as art on art.id=ap.report_type
                WHERE ap.report_type='2' OR ap.report_type='3' 
                GROUP BY ap.id ORDER BY ap.assessment";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_types($report_type_catg)
    {
        $query = "SELECT distinct ap.id as assessment_id, CONCAT('[', ap.id,'] ', ap.assessment, ' [', art.description, '] ') as assessment,  if(DATE_FORMAT(ap.end_dttm,'%y-%m-%d %H:%i')<=CURDATE(),'Expired','Live') AS status
				FROM assessment_mst ap 
				LEFT JOIN assessment_report_type as art on art.id=ap.report_type ";
        if (!empty($report_type_catg)) {
            $query .= " WHERE  ap.report_type='$report_type_catg' ";
        }
        $query .= " GROUP BY ap.id ORDER by ap.assessment";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_process_participants($company_id, $assessment_id, $startdate, $enddate)
    {
        $query = "SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed,aa.attempts, aa.ftpto_vimeo_uploaded
                        ,aa.ftpto_vimeo_dttm as uploaded_dt,ais.task_status_dttm as process_dt, DATEDIFF(ais.task_status_dttm,aa.ftpto_vimeo_dttm) AS datediff, TIMEDIFF(ais.task_status_dttm,aa.ftpto_vimeo_dttm) as time_diff, ar.addeddate as added_date
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                        LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                        LEFT JOIN ai_schedule as ais on ais.assessment_id = ar.assessment_id and ais.user_id = ar.user_id and ais.question_id = ar.question_id 
                    WHERE
                        ar.company_id = '" . $company_id . "'  ";
        if ($assessment_id != '') {
            $query .= "  AND ar.assessment_id = '" . $assessment_id . "' ";
        }
        if ($startdate == '') {
            $query .= " and 1=1 ";
        } else {
            $query .= " and ar.addeddate BETWEEN '" . $startdate . "' and '" . $enddate . "' ";
        }
        $query .= " AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                        AND aa.is_completed =1
                    GROUP BY ar.user_id, ar.trans_id 
                    ORDER BY ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                ORDER BY
                   main.user_id, main.trans_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_ai_data($Company_id, $assessment_id, $user_id, $trans_id, $question_id)
    {
        $LcSqlStr = "SELECT * FROM ai_schedule WHERE company_id='" . $Company_id . "' ";
        if ($assessment_id != '') {
            $LcSqlStr .= " AND assessment_id='" . $assessment_id . "' ";
        }
        $LcSqlStr .= " AND user_id='" . $user_id . "' AND trans_id='" . $trans_id . "' AND question_id='" . $question_id . "' ";

        $query = $this->db->query($LcSqlStr);
        $row = $query->row();
        return $row;
    }
    public function get_process_schedule($company_id, $asssessment_id)
    {
        $query = "SELECT process_status from ai_cronjob WHERE company_id='" . $company_id . "' and assessment_id='" . $asssessment_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_all_assessment_manager()
    {
        $query = "SELECT distinct ap.id as assessment_id, CONCAT('[', ap.id,'] ', ap.assessment, ' [', art.description, '] ') as assessment,  if(DATE_FORMAT(ap.end_dttm,'%y-%m-%d %H:%i')<=CURDATE(),'Expired','Live') AS status
                FROM assessment_mst ap 
                LEFT JOIN assessment_report_type as art on art.id=ap.report_type 
                WHERE ap.report_type='2' OR ap.report_type='3'
                GROUP BY ap.id
                ORDER BY ap.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_distinct_manager($assessment_id)
    {
        $query = "SELECT am.assessment_id, am.trainer_id, CONCAT(cu.first_name,' ' ,cu.last_name) as fullname FROM assessment_managers as am 
        LEFT JOIN company_users as cu on cu.userid=am.trainer_id WHERE assessment_id='" . $assessment_id . "'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_participants_final_report($assessment_id1)
    {
        $query = "SELECT DISTINCT c.emp_id, c.user_id, CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, c.registration_date,  rg.region_name, c.area, c.department, am.assessment, amu.trainer_id,
                b.is_completed as completed FROM `assessment_allow_users` as a 
                left join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id LEFT JOIN device_users as c on c.user_id=a.user_id 
                LEFT JOIN region as rg on rg.id= c.region_id  
                LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
                left join assessment_mapping_user as amu on amu.user_id= a.user_id
                where a.assessment_id='" . $assessment_id1 . "'
                order by user_name";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_distinct_participants($company_id, $assessment_id)
    {
        $query = "SELECT distinct company_id,assessment_id,user_id,user_name,email,mobile FROM (SELECT
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
        WHERE
            ar.company_id = '" . $company_id . "' AND ar.assessment_id = '" . $assessment_id . "' AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
            AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1
        ORDER BY
            ar.user_id, ar.trans_id 
        ) AS main
        CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
    ORDER BY
       main.user_id, main.trans_id) AS final ORDER BY user_id";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_users_min_score_status($assessment_id, $question_id, $task_status)
    {
        $query = "SELECT count(task_status) AS failed_count FROM ai_schedule WHERE user_id IN (
            SELECT main.user_id from (
            SELECT ar.user_id,round( IFNULL( sum( ps.score ) / count( ps.question_id ), 0 ), 2 ) AS score 
            FROM assessment_results AS ar
            LEFT JOIN ai_subparameter_score AS ps ON ar.company_id = ps.company_id AND ar.assessment_id = ps.assessment_id AND ar.user_id = ps.user_id AND ar.question_id = ps.question_id 
            AND ps.parameter_type = 'parameter' 
            WHERE ar.assessment_id = '" . $assessment_id . "' AND ar.question_id = '" . $question_id . "'
            GROUP BY ar.user_id,ar.question_id ORDER BY ar.user_id, ar.question_id) as main WHERE main.score = 0) and assessment_id='" . $assessment_id . "' AND question_id ='" . $question_id . "' and task_status='" . $task_status . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_questions($company_id, $assessment_id, $assessment_type = '', $user_id = '')
    {
        $query = "SELECT main.*,@dcp AS previous,
        CONCAT('Q',CONVERT ((SELECT CASE WHEN main.question_id != previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
        @dcp := main.question_id AS current 
        FROM (SELECT DISTINCT ar.question_id, aq.question,ar.trans_id FROM
            assessment_results AS ar
            LEFT JOIN company AS c ON ar.company_id = c.id
            LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
            LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id
            LEFT JOIN assessment_question AS aq ON ar.question_id = aq.id 
        WHERE
            ar.company_id = '" . $company_id . "' AND ar.assessment_id = '" . $assessment_id . "' AND ar.trans_id > 0 AND ar.question_id > 0 ";
        if ($assessment_type == 2 && $user_id != 0) {
            $query .= " AND ar.user_id=$user_id ORDER BY ar.id";
        } else {
            $query .= "ORDER BY ar.user_id, ar.trans_id";
        }
        $query .= ") AS main
        CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_questions_user_wise($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT
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
                        LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id 
                    WHERE
                        ar.company_id = '" . $company_id . "' AND ar.assessment_id = '" . $assessment_id . "' AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
                        AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1
                    ORDER BY
                        ar.user_id, ar.trans_id 
                    ) AS main
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                WHERE main.company_id = '" . $company_id . "' AND main.assessment_id = '" . $assessment_id . "' AND main.user_id='" . $user_id . "'
                ORDER BY
                   main.user_id, main.trans_id";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_questions_user_details($company_id, $assessment_id, $user_id)
    {
        $query2 = "SELECT question_limits FROM assessment_mst WHERE id='$assessment_id'";
        $result2 = $this->db->query($query2);
        $data2 = $result2->result();
        $question_limits = $data2[0]->question_limits;
        $res = array();

        $query3 = "SELECT MAX(attempts) as attempts FROM ai_cosine_score WHERE assessment_id = '$assessment_id' AND user_id='$user_id'";
        $result3 = $this->db->query($query3);
        $data3 = $result3->result();
        $attempts = $data3[0]->attempts;

        $query = "SELECT ac.user_id,ac.assessment_id,ac.current_question_id as question_id,ac.cosine_score,ac.audio_totext,aq.question,ac.added_at,ac.next_question_id,em.embeddings
                FROM `ai_cosine_score` as ac 
                LEFT JOIN assessment_question as aq on aq.id=ac.current_question_id 
                LEFT JOIN ai_embeddings as em on em.question_id=ac.current_question_id
                WHERE ac.assessment_id = '$assessment_id' AND ac.user_id='$user_id' AND ac.attempts=$attempts GROUP BY ac.current_question_id ORDER BY ac.id ASC LIMIT $question_limits";
        $result = $this->db->query($query);
        $res = $result->result();
        return $res;
    }

    public function get_existing_task($company_id, $asssessment_id)
    {
        $query = "SELECT * from ai_schedule WHERE company_id='" . $company_id . "' and assessment_id='" . $asssessment_id . "'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_completed_video()
    {
        $query = "SELECT * from ai_schedule WHERE  task_status=1 and (schedule_by=0 or schedule_by='' or isnull(schedule_by)) ORDER BY id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_failed_video()
    {
        $query = "SELECT * from ai_schedule WHERE task_status=4 ORDER BY id LIMIT 0,5";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment()
    {
        $query = "SELECT * from assessment_mst WHERE status=1 order by id ASC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_schedule()
    {
        $query = "SELECT * from ai_cronjob WHERE process_status=1 order by id ASC LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_report_schedule()
    {
        $query = "SELECT * from ai_cronreports order by id ASC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_unique_candidates($company_id, $assessment_id)
    {
        $query = "SELECT DISTINCT company_id,assessment_id,user_id,pdf_filename,mpdf_filename,cpdf_filename from ai_schedule
        WHERE company_id='" . $company_id . "' AND assessment_id='" . $assessment_id . "'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_task()
    {
        $query = "SELECT ais.*,CONCAT( du.firstname, ' ', du.lastname ) AS user_name,CONCAT(du.firstname,' ',du.lastname,' ',du.user_id) AS user_name_id,c.portal_name,am.assessment from ai_schedule  as ais
        LEFT JOIN company AS c ON ais.company_id = c.id
        LEFT JOIN assessment_mst AS am ON ais.assessment_id = am.id AND ais.company_id = am.company_id
        LEFT JOIN device_users as du on ais.user_id = du.user_id ORDER BY ais.assessment_id ,ais.user_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function cronjob_status($status)
    {
        $query = "UPDATE ai_cronjob SET process_started='" . $status . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_status($status, $company_id, $assessment_id, $user_id, $trans_id, $question_id)
    {
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE ai_schedule SET task_status='" . $status . "',task_status_dttm='" . $now . "' WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "' AND 
                trans_id='" . $trans_id . "' AND 
                question_id='" . $question_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_status_failed_message($message, $company_id, $assessment_id, $user_id, $trans_id, $question_id)
    {
        $query = 'UPDATE ai_schedule SET task_failed_message="' . $message . '" WHERE 
                company_id="' . $company_id . '" AND 
                assessment_id="' . $assessment_id . '" AND 
                user_id="' . $user_id . '" AND 
                trans_id="' . $trans_id . '" AND 
                question_id="' . $question_id . '"';
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_xls_status($status, $company_id, $assessment_id, $user_id, $trans_id, $question_id, $file_name)
    {
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE ai_schedule SET xls_imported=0,xls_generated='" . $status . "',xls_generated_dttm='" . $now . "', xls_filename='" . $file_name . "' WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "' AND 
                trans_id='" . $trans_id . "' AND 
                question_id='" . $question_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_xls_import_status($company_id, $assessment_id, $user_id, $trans_id, $question_id)
    {
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE ai_schedule SET xls_imported=1,xls_imported_dttm='" . $now . "' WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "' AND 
                trans_id='" . $trans_id . "' AND 
                question_id='" . $question_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_user_lock_status($company_id, $assessment_id, $user_id)
    {
        $query = "UPDATE ai_schedule SET pdf_generated=2 WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_overall_score_rank($company_id, $assessment_id, $user_id)
    {
        // $query  = "SELECT * FROM (
        //     SELECT main.*,ROUND(@dcp,2) AS previous,  @lastrank as last_rank,
        //     (SELECT 
        //     CASE 
        //     WHEN main.overall_score > previous THEN @cnt := @cnt + 1
        //     WHEN main.overall_score < previous THEN @cnt := @cnt + 1
        //     WHEN main.overall_score = previous THEN @cnt := @cnt
        //     ELSE @cnt := 1 END ) as rank,
        //     ( SELECT 
        //     CASE 
        //     WHEN @lastrank=0 THEN @lastrank := @cnt
        //     WHEN main.overall_score = @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank
        //     WHEN main.overall_score < @dcp AND @lastrank != @cnt THEN @lastrank := @lastrank + 2
        //     WHEN main.overall_score < @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank + 1
        //     END) as final_rank,
        //     @dcp := main.overall_score AS current
        //     FROM (
        //     SELECT ps.user_id,(sum(ps.score)/count(DISTINCT ps.parameter_id)/count(DISTINCT ps.question_id)) AS overall_score 
        //     FROM ai_subparameter_score AS ps 
        //     WHERE ps.parameter_type='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
        //     GROUP BY user_id ORDER BY overall_score desc) as main 
        //     CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
        //     WHERE q.user_id='".$user_id."'";
        $query = "SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
                SELECT ps.user_id, ROUND(IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/count(DISTINCT ps.question_id)),2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_question_your_score($company_id, $assessment_id, $user_id, $question_id)
    {
        // $query  = "SELECT sum(ps.score)/count(*) as score FROM ai_subparameter_score as ps 
        // WHERE ps.parameter_type ='parameter' AND
        // ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.question_id ='".$question_id."'
        // GROUP BY ps.user_id ,ps.question_id";
        $query = "SELECT  IF(ps.weighted_score=0, SUM(ps.score)/count(*), SUM(ps.weighted_score))  as score FROM ai_subparameter_score as ps 
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.question_id ='" . $question_id . "'
        GROUP BY ps.user_id ,ps.question_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_question_minmax_score($company_id, $assessment_id)
    {
        $query = "SELECT main.question_id, max( main.score ) AS max_score,min( main.score ) AS min_score FROM(
            SELECT ar.user_id,ar.question_id, IF(ps.weighted_score=0, round( IFNULL(SUM(ps.score)/count(ps.question_id),0),2) , SUM(ps.weighted_score)) AS score 
            FROM assessment_results AS ar
            LEFT JOIN ai_subparameter_score AS ps ON ar.company_id = ps.company_id AND ar.assessment_id = ps.assessment_id AND ar.user_id = ps.user_id AND ar.question_id = ps.question_id 
            AND ps.parameter_type = 'parameter' 
            WHERE ar.company_id = '" . $company_id . "' AND ar.assessment_id = '" . $assessment_id . "'
            GROUP BY ar.user_id,ar.question_id ORDER BY ar.user_id, ar.question_id) AS main GROUP BY main.question_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    // public function get_question_minmax_score($company_id,$assessment_id,$question_id){
    //     $query  = "SELECT max( main.score ) AS max_score,min( main.score ) AS min_score FROM(
    //     SELECT ar.user_id,ar.question_id,round( IFNULL( sum( ps.score ) / count( ps.question_id ), 0 ), 2 ) AS score 
    //     FROM assessment_results AS ar
    //     LEFT JOIN ai_subparameter_score AS ps ON ar.company_id = ps.company_id AND ar.assessment_id = ps.assessment_id AND ar.user_id = ps.user_id AND ar.question_id = ps.question_id 
    //     AND ps.parameter_type = 'parameter' 
    //     WHERE ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id."' AND ar.question_id ='".$question_id."'
    //     GROUP BY ar.user_id,ar.question_id ORDER BY ar.user_id, ar.question_id) AS main";
    //     $result = $this->db->query($query);
    //     return $result->row();
    // }
    public function get_user_from_minmax_score($company_id, $assessment_id, $question_id, $score)
    {
        $query = "SELECT * FROM (
        SELECT ar.user_id,ar.question_id, ais.failed_counter,round( IFNULL( sum( IF(ps.weighted_score=0,ps.score,ps.weighted_score) ) / count( ps.question_id ), 0 ), 2 ) AS score 
        FROM assessment_results AS ar
        LEFT JOIN ai_subparameter_score AS ps ON ar.company_id = ps.company_id AND ar.assessment_id = ps.assessment_id AND ar.user_id = ps.user_id AND ar.question_id = ps.question_id 
        LEFT JOIN ai_schedule as ais ON ar.company_id = ps.company_id AND ar.assessment_id = ps.assessment_id AND ar.user_id = ps.user_id AND ar.question_id = ps.question_id 
        AND ps.parameter_type = 'parameter' 
        WHERE ar.company_id = '" . $company_id . "' AND ar.assessment_id = '" . $assessment_id . "' AND ar.question_id ='" . $question_id . "'
        GROUP BY ar.user_id,ar.question_id ORDER BY ar.user_id, ar.question_id,ais.failed_counter) as main
        WHERE score = $score ORDER BY score LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_your_video($company_id, $assessment_id, $user_id, $trans_id, $question_id, $assessment_type = '')
    {
        $source = ($assessment_type == 2) ? 'https://aiapi.awarathon.com/audio/' : 'https://player.vimeo.com/video/'; //spotlight change
        $query = "SELECT CONCAT('$source',vimeo_uri) as vimeo_url
                FROM assessment_results WHERE
                company_id = '" . $company_id . "' 
                AND assessment_id = '" . $assessment_id . "' 
                AND user_id = '" . $user_id . "' 
                AND trans_id = '" . $trans_id . "'
                AND question_id = '" . $question_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_best_video($company_id, $assessment_id, $question_id, $assessment_type = '')
    {
        $source = ($assessment_type == 2) ? 'https://aiapi.awarathon.com/audio/' : 'https://player.vimeo.com/video/'; //spotlight change
        $query = "SELECT ps.* ,CONCAT('$source',ar.vimeo_uri) as vimeo_url
        FROM ai_subparameter_score as ps 
        LEFT JOIN assessment_results AS ar ON ps.company_id = ar.company_id 
        AND ps.assessment_id = ar.assessment_id
        AND ps.user_id = ar.user_id
        AND ps.trans_id = ar.trans_id
        AND ps.question_id = ar.question_id  AND ar.question_id = '" . $question_id . "'
        WHERE ps.parameter_type = 'parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' AND ps.question_id ='" . $question_id . "' AND (ps.user_id) IN 
        (SELECT temp.user_id FROM (
        SELECT main.user_id, max(main.score) as score FROM (
            SELECT ps.user_id, ROUND(IF(ps.weighted_score=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)),2) as score FROM ai_subparameter_score as ps
            WHERE ps.parameter_type ='parameter' AND          
        ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' AND ps.question_id ='" . $question_id . "'
        group by ps.user_id,ps.question_id order by score desc) as main) as temp) LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameters($company_id, $assessment_id)
    {
        $query = "SELECT DISTINCT ps.parameter_id,ps.parameter_label_id,p.description as parameter_name,pl.description as parameter_label_name FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "'
        ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameters_report($company_id, $assessment_id)
    {
        $query = "SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter_name, ps.id as parameter_id, pl.id as parameter_label_id FROM parameter_mst as ps 
            LEFT JOIN parameter_label_mst as pl on pl.parameter_id= ps.id
            WHERE pl.id IN (SELECT parameter_label_id FROM `assessment_trans_sparam` where assessment_id='" . $assessment_id . "')
            ORDER BY  ps.id, pl.id;";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameters_your_score($company_id, $assessment_id, $user_id, $parameter_id, $parameter_label_id)
    {
        $query = "SELECT IF(ats.parameter_weight=0, round(sum(ps.score)/count(*),2), round(sum(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight),2)) as score
        FROM ai_subparameter_score as ps 
        LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id AND ats.question_id=ps.question_id
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.parameter_id ='" . $parameter_id . "' AND ps.parameter_label_id ='" . $parameter_label_id . "'
        ORDER BY ps.parameter_id,ps.parameter_label_id";
        // $query  = "SELECT round(sum(ps.score)/count(*),2) as score FROM ai_subparameter_score as ps 
        // left join parameter_mst as p on ps.parameter_id = p.id
        // left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        // WHERE ps.parameter_type ='parameter' AND
        // ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.parameter_id ='".$parameter_id."' AND ps.parameter_label_id ='".$parameter_label_id."'
        // ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameter_minmax_score($company_id, $assessment_id, $parameter_id, $parameter_label_id)
    {
        $query = "SELECT max( score ) as max_score,min( score ) as min_score FROM(
            SELECT IF(ats.parameter_weight=0, round(sum(ps.score)/count(*),2), round(sum(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight),2)) as score FROM
            -- SELECT IF(ats.parameter_weight=0,sum( ps.score ),sum( ps.score*(ats.parameter_weight) ) / SUM(ats.parameter_weight)) AS score FROM
            ai_subparameter_score AS ps
            LEFT JOIN parameter_mst AS p ON ps.parameter_id = p.id
            LEFT JOIN parameter_label_mst AS pl ON ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id 
            LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id
            WHERE ps.parameter_type ='parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' 
            AND ps.parameter_id ='" . $parameter_id . "' AND ps.parameter_label_id ='" . $parameter_label_id . "'
            GROUP BY ps.user_id,ps.parameter_id,ps.parameter_label_id
            ORDER BY ps.user_id,ps.parameter_id,ps.parameter_label_id) AS main";
        // $query = "SELECT max( score ) as max_score,min( score ) as min_score FROM(
        //     SELECT ps.user_id,ps.parameter_id,ps.parameter_label_id,sum( ps.score ) / count( ps.parameter_id ) AS score ,count( ps.parameter_id ) FROM
        //     ai_subparameter_score AS ps
        //     LEFT JOIN parameter_mst AS p ON ps.parameter_id = p.id
        //     LEFT JOIN parameter_label_mst AS pl ON ps.parameter_label_id = pl.id 
        //     AND ps.parameter_id = pl.parameter_id 
        //     WHERE ps.parameter_type ='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' 
        //     AND ps.parameter_id ='".$parameter_id."' AND ps.parameter_label_id ='".$parameter_label_id."'
        //     GROUP BY ps.user_id,ps.parameter_id,ps.parameter_label_id
        //     ORDER BY ps.user_id,ps.parameter_id,ps.parameter_label_id) AS main";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_pdf_status($company_id, $assessment_id, $user_id, $file_name)
    {
        $query = "UPDATE ai_schedule SET pdf_generated=1,pdf_filename='" . $file_name . "' WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_user_rated_by_manager($assessment_id)
    {
        $query = "SELECT count(ais.user_id) as ai_count,count(art.user_id) as manual_count FROM `ai_schedule` as ais
                left join assessment_results_trans as art on ais.assessment_id = art.assessment_id AND  ais.user_id = art.user_id
                WHERE ais.assessment_id ='" . $assessment_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_user_lock_manual_status($company_id, $assessment_id, $user_id)
    {
        $query = "UPDATE ai_schedule SET mpdf_generated=2 WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_manager_name($assessment_id, $user_id)
    {
        $query = "SELECT DISTINCT trainer_id as manager_id, CONCAT(c.first_name,' ',c.last_name) as manager_name 
        FROM assessment_mapping_user as art 
        LEFT JOIN company_users as c on art.trainer_id = c.userid
        WHERE assessment_id='" . $assessment_id . "' and user_id ='" . $user_id . "'
        LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_overall_score_rank($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  @lastrank as last_rank,
            (SELECT 
            CASE 
            WHEN main.overall_score > previous THEN @cnt := @cnt + 1
            WHEN main.overall_score < previous THEN @cnt := @cnt + 1
            WHEN main.overall_score = previous THEN @cnt := @cnt
            ELSE @cnt := 1 END ) as rank,
            ( SELECT 
            CASE 
            WHEN @lastrank=0 THEN @lastrank := @cnt
            WHEN main.overall_score = @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank
            WHEN main.overall_score < @dcp AND @lastrank != @cnt THEN @lastrank := @lastrank + 2
            WHEN main.overall_score < @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank + 1
            END) as final_rank,
            @dcp := main.overall_score AS current
            FROM (
                SELECT ps.user_id, ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score 
            FROM assessment_results_trans AS ps 
            WHERE ps.user_id='" . $user_id . "' AND ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
            WHERE q.user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_question_your_score($company_id, $assessment_id, $user_id, $question_id)
    {
        $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(*), SUM(ps.weighted_percentage) ) ,2) AS score  FROM assessment_results_trans as ps 
        WHERE 
         ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.question_id ='" . $question_id . "'
        GROUP BY ps.user_id ,ps.question_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_question_minmax_score($company_id, $assessment_id, $question_id)
    {
        $query = "SELECT ROUND( IF(ps.weighted_percentage=0, MAX(ps.percentage)/count(ps.question_id), MAX(ps.weighted_percentage) ) ,2) AS max_score , ROUND( IF(ps.weighted_percentage=0, min(ps.percentage)/count(ps.question_id), min(ps.weighted_percentage) ) ,2) AS min_score 
         FROM assessment_results_trans as ps WHERE ps.assessment_id= '" . $assessment_id . "'
         AND ps.question_id= '" . $question_id . "' GROUP BY ps.user_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_best_video($company_id, $assessment_id, $question_id)
    {
        $query =
            "SELECT ps.* ,CONCAT('https://player.vimeo.com/video/',ar.vimeo_uri) as vimeo_url
        FROM assessment_results_trans as ps 
        LEFT JOIN assessment_results AS ar ON 
         ps.assessment_id = ar.assessment_id
        AND ps.user_id = ar.user_id
        
        AND ps.question_id = ar.question_id 
        WHERE (ps.question_id,ps.percentage) IN 
        (SELECT ps.question_id,ROUND( IF(ps.weighted_percentage=0, max(ps.percentage), max(ps.weighted_percentage) ) ,2) AS score 
        FROM assessment_results_trans AS ps 
        WHERE 
        ps.assessment_id = '" . $assessment_id . "'  AND ps.question_id = '" . $question_id . "'
        group by ps.question_id
        ) LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manager_comments($assessment_id, $user_id, $question_id, $manager_id)
    {
        $query = "SELECT remarks FROM assessment_trainer_remarks as ps 
                WHERE ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.question_id ='" . $question_id . "' AND trainer_id='" . $manager_id . "'
                LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_parameters_score($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT ps.parameter_id, ps.parameter_label_id, pm.description as parameter_name, plm.description as parameter_label_name, 
                ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(*), SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight) ) ,2) AS percentage 
                FROM assessment_results_trans as ps 
                LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
                LEFT JOIN parameter_mst pm ON pm.id=ps.parameter_id 
                LEFT JOIN parameter_label_mst plm ON plm.id=ps.parameter_label_id 
                WHERE ps.assessment_id = '$assessment_id' AND ps.user_id='$user_id' GROUP by ps.parameter_id,ps.parameter_label_id ORDER BY ps.parameter_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_manual_parameters_your_score($company_id, $assessment_id, $user_id, $parameter_id, $parameter_label_id)
    {
        // $query  = "SELECT avg(percentage) as percentage FROM assessment_results_trans as ps
        // WHERE ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.parameter_id ='".$parameter_id."'";
        $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(*), SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight) ) ,2) AS percentage FROM assessment_results_trans as ps 
        LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
        WHERE ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.parameter_id ='" . $parameter_id . "' AND ps.parameter_label_id='" . $parameter_label_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_parameter_minmax_score($user_id, $assessment_id, $parameter_id, $parameter_label_id)
    {
        $query = "SELECT ps.user_id, ROUND(IF(ps.weighted_percentage=0, MAX(ps.percentage), MAX(ps.weighted_percentage)),2) as max_score, ROUND(IF(ps.weighted_percentage=0, MIN(ps.percentage), MIN(ps.weighted_percentage)),2) as 
        min_score FROM assessment_results_trans as ps WHERE ps.user_id='" . $user_id . "'
        AND ps.parameter_id = '" . $parameter_id . "' GROUP BY ps.parameter_id, ps.user_id";

        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_combined_parameters_your_score($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT pm.id as parameter_id, IF(plm.id is NULL, pm.id, plm.id) as parameter_label_id, pm.description as parameter_name, iF(plm.description IS NULL,pm.description,plm.description) as parameter_label_name, 
                IF(ats.parameter_weight=0, round(sum(ps.score)/count(*),2), round(sum(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight),2)) as score, 
                ROUND( IF(art.weighted_percentage=0, SUM(art.percentage)/count(*), SUM(art.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight) ) ,2) AS percentage 
                FROM ai_subparameter_score as ps left join parameter_mst as pm on ps.parameter_id = pm.id 
                LEFT JOIN parameter_label_mst as plm on ps.parameter_label_id = plm.id AND ps.parameter_id = plm.parameter_id 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id AND ats.question_id=ps.question_id 
                LEFT JOIN assessment_results_trans art ON ats.assessment_id=art.assessment_id AND ats.parameter_id=art.parameter_id AND ats.question_id=art.question_id AND ps.user_id=art.user_id
                WHERE ps.parameter_type ='parameter' AND ps.company_id = '$company_id' AND ps.assessment_id = '$assessment_id' AND ps.user_id ='$user_id' 
                GROUP BY ps.parameter_id,ps.parameter_label_id 
                ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function update_manual_pdf_status($company_id, $assessment_id, $user_id, $file_name)
    {
        $query = "UPDATE ai_schedule SET mpdf_generated=1,mpdf_filename='" . $file_name . "' WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_user_lock_combined_status($company_id, $assessment_id, $user_id)
    {
        $query = "UPDATE ai_schedule SET cpdf_generated=2 WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_user_overall_score_combined($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT FORMAT(sum(comb.overall_score)/count(comb.overall_score),2) as overall_score from (SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id)) ,2) AS overall_score  
            FROM assessment_results_trans AS ps 
            WHERE  ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id= '" . $user_id . "'
            UNION ALL
            SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id, IF(ps.weighted_score=0, ROUND(sum(ps.score)/count(ps.question_id),2), ROUND(sum(ps.weighted_score)/count(DISTINCT ps.question_id),2))  AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id= '" . $user_id . "') as comb;";
        /* $query = "SELECT main.user_id,ROUND((sum(main.overall_score)/count(main.user_id)),2) AS overall_score FROM (
         SELECT ps.user_id,ROUND((sum(ps.score)/count(DISTINCT ps.parameter_id)),2) AS overall_score 
         FROM ai_subparameter_score AS ps 
         WHERE ps.parameter_type='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
         GROUP BY user_id 
         UNION ALL 
         SELECT art.user_id,ROUND((sum(art.percentage)/count(DISTINCT art.parameter_id)),2) AS overall_score 
         FROM assessment_results_trans AS art 
         WHERE art.assessment_id = '".$assessment_id."'
         GROUP BY user_id ) as main 
         where user_id ='".$user_id."'
         GROUP BY user_id";*/
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_question_manual_score($assessment_id, $user_id, $question_id)
    {
        $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)) ,2) AS score  FROM assessment_results_trans as ps 
        WHERE 
        ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.question_id ='" . $question_id . "'
        GROUP BY ps.user_id ,ps.question_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameter_manual_score($assessment_id, $user_id, $parameter_id, $parameter_label_id)
    {
        // $query  = "SELECT avg(percentage) as percentage FROM assessment_results_trans as ps 
        $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(*), SUM(ps.percentage*(ats.parameter_weight))/SUM(ats.parameter_weight) ) ,2) AS percentage FROM assessment_results_trans as ps 
        LEFT JOIN assessment_trans_sparam ats ON ps.parameter_id=ats.parameter_id AND ps.assessment_id=ats.assessment_id AND ps.question_id=ats.question_id 
        WHERE ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' AND ps.parameter_id ='" . $parameter_id . "' AND ps.parameter_label_id='" . $parameter_label_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_combined_pdf_status($company_id, $assessment_id, $user_id, $file_name)
    {
        $query = "UPDATE ai_schedule SET cpdf_generated=1,cpdf_filename='" . $file_name . "' WHERE 
                company_id='" . $company_id . "' AND 
                assessment_id='" . $assessment_id . "' AND 
                user_id='" . $user_id . "'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 or ($this->db->affected_rows() > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_parameters_reports($company_id, $assessment_id)
    {
        $query = "SELECT DISTINCT IF (pl.id=0 OR pl.id IS NULL, ps.description, pl.description) as parameter_name, ps.id as parameter_id, IF (pl.id=0 OR pl.id IS NULL,ps.id,pl.id) as parameter_label_id FROM parameter_mst as ps 
            LEFT JOIN parameter_label_mst as pl on pl.parameter_id= ps.id
            WHERE pl.id IN (SELECT parameter_label_id FROM `assessment_trans_sparam` where assessment_id='" . $assessment_id . "')
            ORDER BY  ps.id, pl.id;";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function trainee_report_data($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  rg.region_name, c.area, c.department, am.assessment as assessment_name,
                if(b.is_completed,'Completed ','Incomplete') as user_status FROM `assessment_allow_users` as a 
                LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
                LEFT JOIN device_users as c on c.user_id=a.user_id 
                LEFT JOIN region as rg on rg.id= c.region_id  
                LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
                $dtWhere $dtOrder $dtLimit
                ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);

        $query = " SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  rg.region_name, c.area, c.department, am.assessment as assessment_name,
                    if(b.is_completed,'Completed ','Incomplete') as user_status FROM `assessment_allow_users` as a 
                    LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
                    LEFT JOIN device_users as c on c.user_id=a.user_id 
                    LEFT JOIN region as rg on rg.id= c.region_id  
                    LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
                    $dtWhere GROUP BY am.id ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array) $data_array);
        return $data;
    }
    public function status_check($company_id, $status_id, $dtwhere, $dtwhere1, $tempLimit = "")
    {
        $query = "SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  
                    rg.region_name, c.area, c.department, am.assessment,c.hq,IFNULL(b.attempts,0) as attempts,IFNULL(am.number_attempts,0) as total_attempts,
                    if(b.is_completed,'Completed ','Incomplete') as status_u,IF(c.designation='',dt.description,c.designation) as designation 
                    FROM `assessment_allow_users` as a 
                    LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
                    LEFT JOIN device_users as c on c.user_id=a.user_id 
                    LEFT JOIN designation_trainee as dt on dt.id=c.designation_id 
                    LEFT JOIN region as rg on rg.id= c.region_id  
                    LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
                    $dtwhere $dtwhere1 order by status_u, user_name
                    $tempLimit
                    ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function status_check_manager($dtwhere, $ismapped, $dtwhere1)
    {
        $query = "SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  			rg.region_name, c.area, c.department, am.assessment,
            if(b.is_completed,'Completed ','Incomplete') as status1, CONCAT(cu.first_name,' ',cu.last_name) as trainer_name,
            if(cr.id is not null ,'Completed ','Incomplete') as trainer_status, IF(c.designation='',dt.description,c.designation) as designation 
                FROM `assessment_allow_users` as a 
            LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
            LEFT JOIN device_users as c on c.user_id=a.user_id 
            LEFT JOIN designation_trainee as dt on dt.id=c.designation_id 
            LEFT JOIN region as rg on rg.id= c.region_id  
            LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id ";
        if ($ismapped) {
            $query .= "LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id=a.assessment_id AND amu.user_id=a.user_id";
        } else {
            $query .= "LEFT JOIN assessment_managers as amu ON amu.assessment_id=a.assessment_id ";
        }
        $query .= " LEFT JOIN company_users as cu ON cu.userid=amu.trainer_id ";
        $query .= " LEFT JOIN assessment_complete_rating as cr On cr.assessment_id=a.assessment_id 
        and cr.trainer_id=amu.trainer_id AND cr.user_id=a.user_id where 1=1 $dtwhere $dtwhere1 order by trainer_status, user_name";
        $query;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function status_check_excel($company_id, $status_id, $dtwhere, $dtwhere1)
    {
        $query = "SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,
            rg.region_name, c.area, c.department, am.assessment,c.hq,IFNULL(b.attempts,0) as attempts,IFNULL(am.number_attempts,0) as total_attempts,
            if(b.is_completed,'Completed ','Incomplete') as status_u,IF(c.designation='',dt.description,c.designation) as designation 
            FROM `assessment_allow_users` as a 
            LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
            LEFT JOIN device_users as c on c.user_id=a.user_id 
            LEFT JOIN designation_trainee as dt on dt.id=c.designation_id 
            LEFT JOIN region as rg on rg.id= c.region_id  
            LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
            WHERE  1=1 $dtwhere $dtwhere1 order by status_u, user_name
            ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function assessment_manager_details($assessment_id)
    {
        $query = "SELECT amu.user_id,amu.trainer_id,CONCAT(cu.first_name, ' ',cu.last_name) as trainer_name 
                FROM `assessment_mapping_user` amu 
                LEFT JOIN company_users cu ON cu.userid=amu.trainer_id 
                WHERE amu.`assessment_id` = $assessment_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function manager_details($assessment_id, $user_id, $ismapped)
    {
        $query = "SELECT  CONCAT(cu.first_name,' ', cu.last_name) as trainer_name, cu.username as trainer_no FROM ";
        $query .= $ismapped ? " `assessment_mapping_user` as a " : " `assessment_managers` as a ";
        $query .= " LEFT JOIN company_users as cu ON cu.userid=a.trainer_id ";
        $query .= " where 1=1 AND a.assessment_id='" . $assessment_id . "'";
        $query .= $ismapped ? " AND a.user_id='" . $user_id . "' " : "";

        $result = $this->db->query($query);
        return $result->row();
    }
    public function assessment_get_ai_score($company_id, $assessment_id)
    {
        $query = "SELECT q.user_id,q.overall_score as overall_score FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND  ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_ai_score($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT q.overall_score as overall_score FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND  ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_score($assessment_id, $user_id)
    {
        $query = "SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  @lastrank as last_rank,
            (SELECT 
            CASE 
            WHEN main.overall_score > previous THEN @cnt := @cnt + 1
            WHEN main.overall_score < previous THEN @cnt := @cnt + 1
            WHEN main.overall_score = previous THEN @cnt := @cnt
            ELSE @cnt := 1 END ) as rank,
            ( SELECT 
            CASE 
            WHEN @lastrank=0 THEN @lastrank := @cnt
            WHEN main.overall_score = @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank
            WHEN main.overall_score < @dcp AND @lastrank != @cnt THEN @lastrank := @lastrank + 2
            WHEN main.overall_score < @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank + 1
            END) as final_rank,
            @dcp := main.overall_score AS current
            FROM (
            SELECT ps.user_id, ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS overall_score 
            FROM assessment_results_trans AS ps 
            WHERE ps.user_id='" . $user_id . "' AND ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
            WHERE q.user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameter_sub_parameter_score($company_id, $assessment_id, $user_id)
    {
        // $query = "SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter_name, ps.id as parameter_id, pl.id as parameter_label_id,round(sum(ass.score)/count(*),2) as score
        // FROM parameter_mst as ps 
        // left join parameter_label_mst as pl on pl.parameter_id= ps.id
        // left join ai_subparameter_score as ass on ass.parameter_id = ps.id AND ass.parameter_type ='parameter' AND ass.assessment_id = '".$assessment_id."' AND ass.user_id ='".$user_id."' 
        // WHERE pl.id IN (SELECT parameter_label_id FROM `assessment_trans_sparam` where assessment_id='".$assessment_id."')
        // group by ps.id, pl.id ORDER BY  ps.id, pl.id;";
        $query = "SELECT IF (pl.id=0 OR pl.id IS NULL, p.description, pl.description) as parameter_name,p.id as parameter_id, IF (pl.id=0 OR pl.id IS NULL,p.id, pl.id) as parameter_label_id,  
                ROUND( IF(ats.parameter_weight=0, SUM(ps.score)/COUNT(*), SUM(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight)) ,2) as score
                FROM ai_subparameter_score as ps 
                left join parameter_mst as p on ps.parameter_id = p.id
                left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id AND ats.question_id=ps.question_id
                WHERE ps.parameter_type ='parameter' AND
                ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' GROUP BY ps.parameter_id,ps.parameter_label_id
                ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function assessment_get_parameter_sub_parameter_score($company_id, $assessment_id)
    {
        $query = "SELECT ps.user_id,IF (pl.id=0 OR pl.id IS NULL, p.description, pl.description) as parameter_name,p.id as parameter_id, IF (pl.id=0 OR pl.id IS NULL,p.id, pl.id) as parameter_label_id, 
                ROUND( IF(ats.parameter_weight=0, SUM(ps.score)/COUNT(*), SUM(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight)) ,2) as score
                FROM ai_subparameter_score as ps 
                LEFT JOIN parameter_mst as p ON ps.parameter_id = p.id 
                LEFT JOIN parameter_label_mst as pl ON ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id AND ats.question_id=ps.question_id 
                WHERE ps.parameter_type ='parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' 
                GROUP BY ps.user_id,ps.parameter_id,ps.parameter_label_id 
                ORDER BY ps.user_id,ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function assessment_attempts_data($assessment_id, $user_id)
    {
        $query = "SELECT am.assessment,IFNULL(b.attempts,0) as attempts,IFNULL(am.number_attempts,0) as total_attempts
                    FROM assessment_attempts as b 
                    LEFT JOIN assessment_mst AS am ON b.assessment_id = am.id
                    WHERE b.assessment_id =" . $assessment_id . " AND b.user_id =" . $user_id;
        $result = $this->db->query($query);

        return $result->row();
    }
}