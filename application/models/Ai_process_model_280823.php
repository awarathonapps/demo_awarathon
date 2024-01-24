<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_process_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    // ai_dashboard Functions 

    public function LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit)
    {
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

    public function getAssessmentUserCount($company_id, $assessment_id)
    {
        $query = "SELECT COUNT(DISTINCT aau.id) as mapped,COUNT(DISTINCT ar.user_id) as played FROM assessment_mst am 
				LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
				LEFT JOIN assessment_results ar ON am.id=ar.assessment_id  
                LEFT JOIN device_users du ON du.user_id=aau.user_id 
				WHERE am.company_id = '" . $company_id . "' and am.id = '" . $assessment_id . "' AND du.istester=0";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function getUserCount($company_id, $assessment_ids)
    {
        $query = "SELECT SUM(mapped) as mapped, sum(played) as played, id as assessment_id FROM 
            (SELECT COUNT(DISTINCT aau.id) as mapped,0 as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
            LEFT JOIN device_users du ON du.user_id=aau.user_id 
            WHERE am.company_id = '" . $company_id . "' and am.id IN (" . implode(',', $assessment_ids) . ") AND du.istester=0 GROUP BY am.id
                UNION ALL
            SELECT 0 as mapped,COUNT(DISTINCT ar.user_id) as played, am.id FROM assessment_mst am 
            LEFT JOIN assessment_results ar ON am.id=ar.assessment_id 
            LEFT JOIN device_users du ON du.user_id=ar.user_id 
            WHERE am.company_id = '" . $company_id . "' and am.id IN (" . implode(',', $assessment_ids) . ") AND du.istester=0 GROUP BY am.id)
        as main GROUP BY assessment_id ORDER BY assessment_id DESC";
        // $query = "SELECT COUNT(DISTINCT aau.id) as mapped,COUNT(DISTINCT ar.user_id) as played, aau.assessment_id FROM assessment_mst am 
        // 		LEFT JOIN assessment_allow_users aau on am.id=aau.assessment_id 
        // 		LEFT JOIN assessment_results ar ON am.id=ar.assessment_id  
        //         LEFT JOIN device_users du ON du.user_id=aau.user_id 
        // 		WHERE am.company_id = '".$company_id."' and am.id IN (".implode(',',$assessment_ids).") AND du.istester=0 
        //         GROUP BY aau.assessment_id ORDER BY aau.assessment_id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getAssessmentVideoCount($company_id, $assessment_id)
    {
        $query = "SELECT count(*) as total_video_processed FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
				WHERE am.status=1 and ai.company_id='" . $company_id . "' AND ai.task_status = 1 AND du.istester=0 AND am.id='" . $assessment_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function getVideoCount($company_id, $assessment_ids)
    {
        $query = "SELECT count(*) as total_video_processsed, am.id as assessment_id FROM `ai_schedule` as ai 
				LEFT JOIN device_users as du ON ai.user_id=du.user_id 
                LEFT JOIN assessment_mst as am on ai.assessment_id = am.id
				WHERE am.status=1 and ai.company_id='" . $company_id . "' AND ai.task_status = 1 AND am.id IN (" . implode(',', $assessment_ids) . ") AND du.istester=0 
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getVideoUploaded($company_id, $assessment_id)
    {
        $system_date = date('Y-m-d H:i:s');
        $query = "SELECT count(*) as total FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id
				WHERE am.status=1 and ar.company_id = '" . $company_id . "' and ar.assessment_id= '" . $assessment_id . "' and
                ar.ftp_status=1 and du.istester=0";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function getUploadedVideos($company_id, $assessment_ids)
    {
        $query = "SELECT count(*) as total_video_uploaded,am.id as assessment_id FROM `assessment_results` as ar 
				LEFT JOIN device_users as du ON du.user_id=ar.user_id 
                LEFT JOIN assessment_mst as am on ar.assessment_id = am.id
				WHERE am.status=1 and ar.company_id = '" . $company_id . "' AND ar.assessment_id IN (" . implode(',', $assessment_ids) . ") AND ar.ftp_status=1 AND du.istester=0
                GROUP BY am.id ORDER BY am.id DESC";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getAssessmentEmailCount($company_id)
    {
        $query = "SELECT assessment_id,count(DISTINCT user_id) as scheduled,sum(is_sent) as sent FROM `trainee_report_schedule` WHERE company_id='$company_id' GROUP by assessment_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_distinct_participants($company_id, $assessment_id, $division_id = '')
    {
        $query = "SELECT distinct company_id,assessment_id,user_id,user_name,email,mobile,is_sent,attempt FROM (SELECT
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
            ar.company_id = '" . $company_id . "' AND ar.assessment_id IN (" . $assessment_id . ") AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !=''
            AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1 ";
        if ($division_id != '' && $division_id != 0) {
            $query .= " am.division_id =" . $division_id;
        }
        $query .= " ORDER BY
            ar.user_id, ar.trans_id, trs.scheduled_at DESC 
        ) AS main
        CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
    ORDER BY
       main.user_id, main.trans_id) AS final GROUP BY user_id ORDER BY user_id";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array) $data_array);

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);
        return $data;
    }

    public function get_question($assessment_id)
    {
        $query = " SELECT art.id,art.question_id ,aq.question,art.parameter_id FROM assessment_trans art "
            . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
            . " where art.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }

    public function LoadQuestionDataTableRefresh($assessment_id, $question_id)
    {
        $query = "SELECT aq.question, aq.id as question_id, CONCAT('https://player.vimeo.com/video/',ar.vimeo_uri) as best_url, 
         abv.best_video_link as ideal_url
        FROM ai_subparameter_score as ps 
        LEFT JOIN assessment_results AS ar ON ps.company_id = ar.company_id 
        AND ps.assessment_id = ar.assessment_id
        AND ps.user_id = ar.user_id
        AND ps.trans_id = ar.trans_id
        AND ps.question_id = ar.question_id  
        LEFT JOIN assessment_question AS aq on aq.id=ps.question_id       
        LEFT JOIN ai_best_ideal_video AS abv ON abv.assessment_id=ps.assessment_id
        WHERE ps.parameter_type = 'parameter' AND  ps.assessment_id = '" . $assessment_id . "' AND ps.question_id = '" . $question_id . "' AND (ps.user_id) IN 
        (SELECT temp.user_id FROM (
        SELECT main.user_id, max(main.score) as score FROM (
        SELECT ps.user_id, IFNULL(ROUND(sum(ps.score)/count(ps.question_id),2),0) as score FROM ai_subparameter_score as ps
        WHERE ps.parameter_type ='parameter' AND        
        ps.assessment_id = '" . $assessment_id . "' AND ps.question_id = '" . $question_id . "'
        group by ps.user_id,ps.question_id order by score desc) as main) as temp) LIMIT 0,1";
        //echo $query;
        $result = $this->db->query($query);
        return $result->row();
    }

    public function ideal_url_link($assessment_id, $question_id)
    {
        $query = "SELECT abv.best_video_link as ideal_url FROM ai_best_ideal_video as abv 
                WHERE abv.assessment_id='" . $assessment_id . "' AND abv.question_id  ='" . $question_id . "'
                ";
        $result = $this->db->query($query);
        return $result->row();
    }

    // ai_dashboard Functions 
    // ==========================================================================================================================================
    // ai_reports functions



    public function get_assessments()
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' - [', art.description, ']') as assessment, if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status, if(am.assessment_type=3,'Trinity',if(am.assessment_type=2, 'Spotlight', 'Roleplay')) AS assessment_type 
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
    public function get_process_participants($company_id, $assessment_id, $startdate, $enddate, $division_id)
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
                        LEFT JOIN ai_schedule as ais on ais.assessment_id = ar.assessment_id and ais.user_id = ar.user_id
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
        if ($division_id != '') {
            $query .= "  AND am.division_id = '" . $division_id . "' ";
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

    public function get_distinct_participants_ai_reports($company_id, $assessment_id, $assessment_type ,$division_id)
    {
        $query = "SELECT distinct company_id,assessment_id,user_id,emp_id,user_name,email,mobile FROM (SELECT
                    main.*,@dcp AS previous,
                    CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                    @dcp := main.user_id AS current,
                    CONCAT(main.user_id,'-',main.question_id) as uid	 
                FROM(
                    SELECT DISTINCT
                        ar.company_id,ar.assessment_id,ar.user_id,ar.trans_id,ar.question_id,c.portal_name,am.assessment,
                        CONCAT( du.firstname, ' ', du.lastname ) AS user_name,du.emp_id,du.email,du.mobile,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed 
                    FROM
                        assessment_results AS ar
                        LEFT JOIN company AS c ON ar.company_id = c.id
                        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
                        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                        LEFT JOIN assessment_question as aq on ar.question_id=aq.id
                        LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                        LEFT JOIN ai_schedule as ai on ar.company_id = ai.company_id and ar.assessment_id=ai.assessment_id and  ar.user_id=ai.user_id 
                    WHERE
                        ar.company_id = '" . $company_id . "' AND ar.assessment_id = '" . $assessment_id . "' AND ar.trans_id > 0 AND ar.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !='' ";
        if ($division_id != '') {
            $query .= " AND am.division_id =" . $division_id;
        }
        $query .= " AND aa.is_completed =1 and ai.task_id != '' and  ai.xls_imported=1
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
    
    public function get_existing_task($company_id, $asssessment_id)
    {
        $query  = "SELECT * from ai_schedule WHERE company_id='" . $company_id . "' and assessment_id='" . $asssessment_id . "'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_completed_video()
    {
        $query  = "SELECT * from ai_schedule WHERE  task_status=1 and (schedule_by=0 or schedule_by='' or isnull(schedule_by)) ORDER BY id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_failed_video()
    {
        $query  = "SELECT * from ai_schedule WHERE task_status=4 ORDER BY id LIMIT 0,5";
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
            (SELECT CASE WHEN (ROUND(main.overall_score,2) = previous or istester=1) THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id, ROUND(IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/count(DISTINCT ps.question_id)),2) AS overall_score,IFNULL(u.istester,0) AS istester  
            FROM ai_subparameter_score AS ps 
            LEFT JOIN device_users AS u ON u.user_id = ps.user_id 
            WHERE ps.parameter_type='parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_question_your_score($company_id, $assessment_id, $user_id, $question_id)
    {
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
            GROUP BY ar.user_id,ar.question_id ORDER BY ar.user_id, ar.question_id) AS main group by main.question_id";
        $result = $this->db->query($query);
        return $result->result();
    }
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
    public function get_your_video($company_id, $assessment_id, $user_id, $trans_id, $question_id)
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
    public function get_best_video($company_id, $assessment_id, $question_id, $assessment_type ='')
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
    public function get_parameters_report($company_id, $assessment_id, $department_name)
    {
        $query = "SELECT DISTINCT IF (pl.id=0 OR pl.id IS NULL, ps.description, pl.description) as parameter_name, ps.id as parameter_id, IF (pl.id=0 OR pl.id IS NULL,ps.id,pl.id) as parameter_label_id FROM parameter_mst as ps 
        LEFT JOIN parameter_label_mst as pl on pl.parameter_id= ps.id
        WHERE pl.id IN (SELECT art.parameter_label_id FROM assessment_results_trans as art 
        inner join device_users as du on du.user_id = art.user_id
         where 1=1 ";
        if ($assessment_id != 0) {
            $query .= " and art.assessment_id IN (" . implode(',', $assessment_id) . ")  ";
        }
        if ($department_name != 0) {
            $query .= " and du.department IN ('" . implode("', '", $department_name) . "') group by du.department  ";
        }
        $query .= " ) ORDER BY  ps.id, pl.id";
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
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameter_minmax_score($company_id, $assessment_id, $parameter_id, $parameter_label_id)
    {
        $query = "SELECT max( score ) as max_score,min( score ) as min_score FROM(
            SELECT IF(ats.parameter_weight=0, round(sum(ps.score)/count(*),2), round(sum(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight),2)) as score FROM
            -- SELECT IF(ats.parameter_weight=0,sum( ps.score ),sum( ps.score*(ats.parameter_weight) )) / SUM(ats.parameter_weight) AS score FROM
            ai_subparameter_score AS ps
            LEFT JOIN parameter_mst AS p ON ps.parameter_id = p.id
            LEFT JOIN parameter_label_mst AS pl ON ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id 
            LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id
            WHERE ps.parameter_type ='parameter' AND ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' 
            AND ps.parameter_id ='" . $parameter_id . "' AND ps.parameter_label_id ='" . $parameter_label_id . "'
            GROUP BY ps.user_id,ps.parameter_id,ps.parameter_label_id
            ORDER BY ps.user_id,ps.parameter_id,ps.parameter_label_id) AS main";
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
    public function get_parameters_reports($company_id, $assessment_id, $department_id, $region_id, $manager_id)
    {
        $query = "SELECT DISTINCT IF (pl.id=0 OR pl.id IS NULL, ps.description, pl.description) as parameter_name, ps.id as parameter_id, IF (pl.id=0 OR pl.id IS NULL,ps.id,pl.id) as parameter_label_id FROM parameter_mst as ps 
            LEFT JOIN parameter_label_mst as pl on pl.parameter_id= ps.id
            WHERE pl.id IN (SELECT art.parameter_label_id FROM assessment_results_trans as art 
            INNER JOIN device_users as du on du.user_id = art.user_id
            where 1=1 ";
        if ($assessment_id != 0) {
            $query .= " and art.assessment_id IN (" . implode(',', $assessment_id) . ")  ";
        }
        if ($department_id != 0) {
            $query .= " and du.department IN ('" . implode("', '", $department_id) . "')   ";
        }
        if ($region_id != 0) {
            $query .= " and du.region_id IN ('" . implode("', '", $region_id) . "')  ";
        }
        if ($manager_id != 0) {
            $query .= " and art.trainer_id IN ('" . implode("', '", $manager_id) . "')  ";
        }
        if ($department_id != 0 or $region_id != 0) {
            $query .= " group by ";
            if ($department_id != 0) {
                $query .= " du.department";
            } else {
                $query .= " du.region_id";
            }
        }

        $query .= " ) ORDER BY  ps.id, pl.id ";
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
    // By Bhautik Rana  add option in final reports 
    public function status_check($company_id, $status_id, $dtwhere, $dtwhere1, $tempLimit = "")
    {
        $query = "SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  
                    rg.region_name, c.area, c.department, am.assessment,c.hq,IFNULL(b.attempts,0) as attempts,IFNULL(am.number_attempts,0) as total_attempts, cm.userid as trainer_id, 
                    CONCAT(cm.first_name,' ',cm.last_name) as trainer_name,IF(c.designation='',dt.description,c.designation) as designation,
                    if(b.is_completed,'Completed ','Incomplete') as status_u, if(cm.userid != '',1,0) as ismapped FROM `assessment_allow_users` as a 
                    LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
                    LEFT JOIN device_users as c on c.user_id=a.user_id 
                    LEFT JOIN designation_trainee as dt on dt.id=c.designation_id 
                    LEFT JOIN region as rg on rg.id= c.region_id  
                    LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
                    LEFT JOIN assessment_mapping_user as amu on amu.user_id = a.user_id and amu.assessment_id = a.assessment_id
                    LEFT JOIN company_users as cm on cm.userid = amu.trainer_id
                    $dtwhere $dtwhere1 order by status_u, user_name
                    $tempLimit
                    ";
        $result = $this->db->query($query);
        return $result->result();
    }
    // By Bhautik Rana  add option in final reports 
    public function status_check_manager($dtwhere, $ismapped, $dtwhere1)
    {
        $query = "SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  			rg.region_name, c.area, c.department, am.assessment,
            if(b.is_completed,'Completed ','Incomplete') as status1, CONCAT(cu.first_name,' ',cu.last_name) as trainer_name,
            if(cr.id is not null ,'Completed ','Incomplete') as trainer_status,IF(c.designation='',dt.description,c.designation) as designation
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
            left join assessment_mapping_user as amu on amu.user_id = a.user_id and amu.assessment_id = a.assessment_id
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
                WHERE amu.`assessment_id` IN ('" . implode("', '", $assessment_id) . "')";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function manager_details($assessment_id, $user_id, $ismapped)
    {
        $query = "SELECT  CONCAT(cu.first_name,' ', cu.last_name) as trainer_name,cu.userid as trainer_id ,cu.username as trainer_no FROM ";
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
            WHERE ps.parameter_type='parameter' AND  ps.assessment_id IN ('" . implode("', '", $assessment_id) . "')
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
        // $query = "SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter_name, ps.id as parameter_id, pl.id as parameter_label_id,
        // ROUND( IF(ass.weighted_score=0, sum(ass.score)/count(*), sum(ass.score*ats.parameter_weight)/sum(ats.parameter_weight)) ,2) as score
        // FROM parameter_mst as ps 
        // left join ai_subparameter_score as ass on ass.parameter_id = ps.id AND ass.parameter_type ='parameter' AND ass.assessment_id = '".$assessment_id."' AND ass.user_id ='".$user_id."' 
        // left join parameter_label_mst as pl on pl.parameter_id= ps.id AND pl.id=ass.parameter_label_id
        // LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ass.parameter_id AND ats.assessment_id=ass.assessment_id
        // WHERE pl.id IN (SELECT parameter_label_id FROM `assessment_trans_sparam` where assessment_id='".$assessment_id."')
        // group by ps.id, pl.id ORDER BY  ps.id, pl.id";
        $query = "SELECT IF (pl.id=0 OR pl.id IS NULL, p.description, pl.description) as parameter_name,p.id as parameter_id, IF (pl.id=0 OR pl.id IS NULL,p.id, pl.id) as parameter_label_id,  
                ROUND( IF(ats.parameter_weight=0, SUM(ps.score)/COUNT(*), SUM(ps.score*(ats.parameter_weight))/SUM(ats.parameter_weight)) ,2) as score
                FROM ai_subparameter_score as ps 
                left join parameter_mst as p on ps.parameter_id = p.id
                left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id 
                LEFT JOIN assessment_trans_sparam ats ON ats.parameter_id=ps.parameter_id AND ats.assessment_id=ps.assessment_id AND ats.question_id=ps.question_id
                WHERE ps.parameter_type ='parameter' AND
                ps.company_id = '" . $company_id . "' AND ps.assessment_id = '" . $assessment_id . "' AND ps.user_id ='" . $user_id . "' 
                GROUP BY ps.parameter_id,ps.parameter_label_id
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

    public function get_all_department($assessment_id)
    {
        $query = "SELECT DISTINCT(amu.user_id), du.department 
                  FROM `assessment_allow_users` as amu 
                  LEFT JOIN device_users as du on du.user_id = amu.user_id WHERE du.department != '' ";
        if ($assessment_id != '') {
            $query .= " and amu.assessment_id IN (" . implode(',', $assessment_id) . ") ";
        }
        $query .= " GROUP BY du.department";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    // By Bhautik Rana 01-03-2023
    public function get_participants_final_report($department_name)
    {
        $query = "SELECT c.user_id, c.emp_id, CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, c.registration_date,  rg.region_name, c.area, c.department, am.assessment, amu.trainer_id,
                  b.is_completed as completed FROM `assessment_allow_users` as a 
                  left join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
                  LEFT JOIN device_users as c on c.user_id=a.user_id 
                  LEFT JOIN region as rg on rg.id= c.region_id  
                  LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
                  left join assessment_mapping_user as amu on amu.user_id= a.user_id
                  where 1 =1 ";
        if ($department_name != '') {
            $query .= " and c.department IN ('" . implode("','", $department_name) . "') ";
        }
        $query .= " group by c.user_id order by user_name";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_based_div($department_name)
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' [', art.description, ']') as assessment,if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
        FROM assessment_mst as am 
        LEFT JOIN assessment_mapping_user as at on at.assessment_id = am.id
        LEFT JOIN device_users as du on du.user_id = at.user_id
        LEFT JOIN assessment_report_type as art on art.id=am.report_type 
        WHERE am.status = 1 ";
        if ($department_name != '') {
            $query .= " and du.department IN ('" . implode("','", $department_name) . "') ";
        }
        $query .= " GROUP BY am.id ORDER BY am.assessment";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    public function get_all_manager($department_name)
    {
        $query = "SELECT am.assessment_id, am.trainer_id, CONCAT(cu.first_name,' ' ,cu.last_name) as fullname 
                  FROM assessment_managers as am 
                  LEFT JOIN assessment_mapping_user as art on art.assessment_id = am.assessment_id
                  LEFT JOIN device_users as du  on du.user_id = art.user_id
                  LEFT JOIN company_users as cu on cu.userid=am.trainer_id WHERE 1=1 ";
        if ($department_name != '') {
            $query .= " and du.department IN ('" . implode("','", $department_name) . "') ";
        }
        $query .= " group by am.trainer_id ";
        // echo $query;exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_all_region($assesment_id = '')
    {
        $query = "SELECT du.region_id as region_id, rg.region_name as region_name
                  FROM assessment_mst am
                  LEFT JOIN assessment_mapping_user amu ON am.id=amu.assessment_id
                  LEFT JOIN device_users du ON du.user_id=amu.user_id
                  LEFT JOIN region rg ON du.region_id=rg.id
                  WHERE  1=1";
        if ($assesment_id != '') {
            $query .= " and  am.id in (" . implode(',', $assesment_id) . ") ";
        }
        $query .= " and du.region_id !='0'
                  GROUP BY du.region_id ORDER BY du.region_id asc";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_participate_region($region_id = '')
    {
        $query = "SELECT DISTINCT c.emp_id, c.user_id, CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, c.registration_date,  rg.region_name, c.area, c.department, am.assessment, amu.trainer_id,
        b.is_completed as completed FROM `assessment_allow_users` as a 
        left join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
        LEFT JOIN device_users as c on c.user_id=a.user_id 
        LEFT JOIN region as rg on rg.id= c.region_id  
        LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
        left join assessment_mapping_user as amu on amu.user_id= a.user_id
        where 1 =1 ";
        if ($region_id != '') {
            $query .= " and c.region_id IN ('" . implode("','", $region_id) . "') ";
        }
        $query .= " group by c.region_id, c.user_id";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }

    public function get_assessment_based_region($region_id)
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' [', art.description, ']') as assessment,if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
        FROM assessment_mst as am 
        LEFT JOIN assessment_results_trans as at on at.assessment_id = am.id
        LEFT JOIN device_users as du on du.user_id = at.user_id
        LEFT JOIN assessment_report_type as art on art.id=am.report_type 
        WHERE am.status = 1 ";
        if ($region_id != '') {
            $query .= " and du.region_id IN ('" . implode("','", $region_id) . "') ";
        }
        $query .= " GROUP BY am.id ORDER BY am.assessment";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_manager_based_region($region_id = '')
    {
        $query = "SELECT am.assessment_id, am.trainer_id, CONCAT(cu.first_name,' ' ,cu.last_name) as fullname 
        FROM assessment_managers as am 
        LEFT JOIN assessment_results_trans as art on art.assessment_id = am.assessment_id
        LEFT JOIN device_users as du  on du.user_id = art.user_id
        LEFT JOIN company_users as cu on cu.userid=am.trainer_id WHERE 1=1 ";
        if ($region_id != '') {
            $query .= " and du.region_id IN ('" . implode("','", $region_id) . "') ";
        }
        $query .= " group by am.trainer_id ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_participate_manager($ass_id = '')
    {
        $query = "SELECT DISTINCT c.emp_id, c.user_id, CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, c.registration_date,  rg.region_name, c.area, c.department, am.assessment, amu.trainer_id,
        b.is_completed as completed FROM `assessment_allow_users` as a 
        left join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
        LEFT JOIN device_users as c on c.user_id=a.user_id 
        LEFT JOIN region as rg on rg.id= c.region_id  
        LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
        left join assessment_mapping_user as amu on amu.user_id= a.user_id
        where 1 =1 ";
        if ($ass_id != '') {
            $query .= " and am.id IN ('" . implode("','", $ass_id) . "') ";
        }
        $query .= " group by c.region_id, c.user_id";
        $result = $this->db->query($query);
        $data = $result->result();
        return $data;
    }
    public function get_assessment_based_manager($managerid = '')
    {
        $query = "SELECT distinct am.id as assessment_id, CONCAT('[', am.id,'] ', am.assessment, ' [', art.description, ']') as assessment,if(DATE_FORMAT(am.end_dttm,'%Y-%m-%d %H:%i')>=CURDATE(),'Live','Expired') AS status
        FROM assessment_mst as am 
        LEFT JOIN assessment_results_trans as ats on ats.assessment_id = am.id
        LEFT JOIN device_users as du on du.user_id = ats.user_id
        LEFT JOIN assessment_report_type as art on art.id=am.report_type 
        WHERE am.status = 1 ";
        if ($managerid != '') {
            $query .= " and ats.trainer_id IN ('" . implode("','", $managerid) . "') ";
        }
        $query .= " GROUP BY am.id ORDER BY am.assessment";
        $result = $this->db->query($query);
        return $result->result();
    }
    // Changes by Bhautik rana 14-03-2023 (add tabs)


    /// for excel dump "Nirmal Gajjar" (18-07-2023) start
    public function get_process_participants_trinity($company_id, $assessment_id, $start_date, $end_date)
    {
        $query = "SELECT
        main.*,@dcp AS previous,
        CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
        @dcp := main.user_id AS current,
        CONCAT(main.user_id,'-',main.question_id) as uid	 
    FROM(
        SELECT DISTINCT
            ar.company_id,ar.assessment_id,ar.user_id,ass.trans_id,ass.question_id,c.portal_name,am.assessment,
            CONCAT( du.firstname, ' ', du.lastname ) AS user_name,aq.question,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed,aa.attempts, aa.ftpto_vimeo_uploaded
            ,aa.ftpto_vimeo_dttm as uploaded_dt,ar.addeddate as added_date
        FROM
            trinity_results AS ar
            LEFT JOIN ai_subparameter_score as ass ON ar.assessment_id = ass.assessment_id AND ar.user_id= ass.user_id
            LEFT JOIN company AS c ON ar.company_id = c.id
            LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
            LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
            LEFT JOIN assessment_script_qna as aq on ass.question_id=aq.id

            LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
        WHERE
            ar.company_id = '$company_id'  ";
        if ($assessment_id != '') {
            $query .= "  AND ar.assessment_id = '" . $assessment_id . "' ";
        }
        if ($start_date == '') {
            $query .= " and 1=1 ";
        } else {
            $query .= " and ar.addeddate BETWEEN '" . $start_date . "' and '" . $end_date . "' ";
        }
        $query .= "    AND ar.assessment_id = '$assessment_id'  and 1=1 AND ass.trans_id != 0    AND ar.ftp_status=1 AND ar.vimeo_uri !='' 
            AND aa.is_completed =1
        GROUP BY ar.user_id, ass.trans_id 
        ORDER BY ar.user_id, ass.trans_id 
        ) AS main
        CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
        ORDER BY
        main.user_id, main.trans_id";
        $result = $this->db->query($query);
        return $result->result();
    }


    public function sub_parameter_level_data($assessment_id, $company_id, $user_id)
    {
        $query = "SELECT concat(du.firstname,' ',du.lastname)user_name,ass.question_series,ass.parameter_type as type,pm.description as parameter_name,ifnull(gm.description,'-') as formal_name,ass.score FROM trinity_results as tr 
        LEFT JOIN device_users as du ON du.user_id = tr.user_id 
        LEFT JOIN ai_subparameter_score as ass ON ass.assessment_id = tr.assessment_id AND ass.user_id = tr.user_id
        LEFT JOIN parameter_mst as pm ON pm.id = ass.parameter_id
        LEFT JOIN goal_mst as gm on gm.id = ass.parameter_label_id
        WHERE tr.assessment_id = '$assessment_id' AND tr.user_id ='$user_id' AND tr.company_id = '$company_id' ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function raw_scores_data($assessment_id, $company_id, $user_id)
    {
        $query = "SELECT concat(du.firstname,' ',du.lastname)user_name,ass.question_series,pm.description as parameter_name,ass.score 
        FROM trinity_results as tr 
        LEFT JOIN device_users as du ON du.user_id = tr.user_id 
        LEFT JOIN ai_subparameter_score as ass ON ass.assessment_id = tr.assessment_id AND ass.user_id = tr.user_id LEFT JOIN parameter_mst as pm ON pm.id = ass.parameter_id 
        WHERE tr.assessment_id = '$assessment_id' AND tr.user_id ='$user_id' AND tr.company_id = '$company_id' AND ass.question_id =0 ";
        $result = $this->db->query($query);
        return $result->result();
    }

    // old query
    // public function transcripts_data($assessment_id, $company_id, $user_id)
    // {
    //     $query  = "SELECT concat(du.firstname,' ',du.lastname)as user_name,ass.question_series ,tcs.audio_totext as transcript
    //     FROM trinity_results as tr
    //     LEFT JOIN ai_subparameter_score as ass ON ass.assessment_id = tr.assessment_id AND ass.user_id = tr.user_id 
    //     LEFT JOIN device_users as du ON du.user_id = ass.user_id 
    //     LEFT JOIN trinity_cosine_score as tcs  on tcs.assessment_id = ass.assessment_id AND ass.user_id = tcs.user_id AND ass.question_id = tcs.question_id 
    //     WHERE tr.assessment_id = '$assessment_id' AND tr.user_id = '$user_id' AND tr.company_id = '$company_id' and ass.question_id != 0;";

    //     $result = $this->db->query($query);
    //     return $result->result();
    // } //old query 

    public function similarity_data($assessment_id, $company_id, $user_id)
    {
        $query = "SELECT DISTINCT ass.question_series,concat(du.firstname,' ',du.lastname)as user_name,ifnull(ai.score,'-') as score ,ifnull(ai.sentance_keyword,'-') as matches_with 
        FROM trinity_results as tr 
        LEFT JOIN device_users as du on du.user_id = tr.user_id 
        left join ai_subparameter_score as ass on ass.assessment_id = tr.assessment_id AND tr.user_id = ass.user_id 
        LEFT JOIN ai_sentkey_score as ai ON ai.user_id = tr.user_id AND ai.assessment_id = tr.assessment_id  And ass.question_id = ai.question_id
        WHERE tr.assessment_id = '$assessment_id' AND tr.user_id = '$user_id' and tr.company_id = '$company_id' and ass.question_id ORDER by ass.question_series ASC";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function assessment_transcript_details($user_id, $company_id, $assessment_id)
    {
        // $query = "SELECT d1.content, d1.attempts, d1.addeddate, d1.speaker FROM trinity_user_transcripts AS d1 
        //         LEFT OUTER JOIN trinity_user_transcripts AS d2 ON d1.attempts < d2.attempts AND d1.assessment_id=d2.assessment_id AND d1.user_id=d2.user_id
        //         WHERE (d2.content IS NULL) AND d1.assessment_id=$assessment_id AND d1.user_id=$user_id and d1.company_id = '$company_id' ";

        $query = "SELECT concat(du.firstname,' ',du.lastname)as user_name,d1.content, d1.attempts, d1.addeddate, d1.speaker 
        FROM trinity_user_transcripts AS d1 
        LEFT OUTER JOIN trinity_user_transcripts AS d2 ON d1.attempts < d2.attempts AND d1.assessment_id=d2.assessment_id AND d1.user_id=d2.user_id 
        LEFT JOIN device_users as du ON du.user_id = d1.user_id
        WHERE (d2.content IS NULL) AND d1.assessment_id='$assessment_id' AND d1.user_id='$user_id' and d1.company_id = '$company_id'";
        $result = $this->db->query($query);
        return $result->result();
    }
    // for excel dump "Nirmal Gajjar" (18-07-2023) end
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
}
