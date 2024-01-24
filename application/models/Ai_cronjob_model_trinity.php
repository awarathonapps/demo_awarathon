<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ai_cronjob_model_trinity extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function get_completed_video(){
        $query  = "SELECT * from ai_schedule WHERE  task_status=1 and (schedule_by=0 or schedule_by='' or isnull(schedule_by)) ORDER BY id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_failed_video(){
        $query  = "SELECT * from ai_schedule WHERE task_status=4  ORDER BY id LIMIT 0,20";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment(){
        // $query  = "SELECT * from assessment_mst WHERE status=1 AND assessment_type != 3 order by id ASC";
        $query  = "SELECT * from assessment_mst WHERE status=1 and id=424 order by id ASC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_users($company_id,$assessment_id,$assessment_type){
        //GET TOTAL QUESTION MAPPED TO ASSIGNMENTS 
        $total_question = 0;
        if($assessment_type==1)
        {
            $query = "SELECT COUNT(*) AS total_question FROM assessment_trans where assessment_id=$assessment_id";
        }
        elseif($assessment_type==2)
        {
            $query = "SELECT question_limits AS total_question FROM assessment_mst where id='".$assessment_id."'";
        }
        elseif($assessment_type==3)
        {
            $query = "SELECT COUNT(*) AS total_question FROM trinity_trans where assessment_id='".$assessment_id."'";
        }
        $result = $this->db->query($query);
        
        $assqc_data = $result->result();
        if (isset($assqc_data) AND count((array)$assqc_data)>0){
            foreach($assqc_data as $rowqc){
                $total_question = $rowqc->total_question;
            }
        }

        if($assessment_type == 3){
            $query = "SELECT 
                    main.*, 'Q0' AS question_series, CONCAT(main.user_id) as uid, 
                    ucj.total_question 
                FROM( SELECT DISTINCT 
                    ar.company_id,ar.assessment_id,ar.user_id,tt.id as trans_id,0 as question_id,c.portal_name,am.assessment, 
                    CONCAT( du.firstname, ' ', du.lastname ) AS user_name,ar.video_url,ar.vimeo_uri,ar.ftp_status,aa.is_completed 
                    FROM trinity_results AS ar 
                    LEFT JOIN company AS c ON ar.company_id = c.id 
                    LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id 
                    LEFT JOIN trinity_cosine_score AS tcs ON tcs.assessment_id=ar.assessment_id AND tcs.user_id=ar.user_id 
                    LEFT JOIN trinity_trans AS tt ON tt.assessment_id=ar.assessment_id AND tt.question_id=tcs.question_id 
                    LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
                    LEFT JOIN assessment_attempts AS aa ON ar.assessment_id = aa.assessment_id AND ar.user_id = aa.user_id 
                    WHERE ar.company_id = ".$company_id." AND ar.assessment_id = ".$assessment_id." AND tt.id > 0 AND tcs.question_id > 0 AND ar.ftp_status=1 AND ar.vimeo_uri !='' 
                        AND aa.is_completed =1 ORDER BY ar.user_id LIMIT 1) AS main 
                    CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter 
                    CROSS JOIN ( SELECT arc.user_id,count(arc.id) as total_question from trinity_results as arc 
                    WHERE arc.company_id =".$company_id." AND arc.assessment_id=".$assessment_id." AND arc.ftp_status=1 AND arc.vimeo_uri !='' 
                    GROUP BY arc.user_id ORDER BY arc.user_id) as ucj 
                    WHERE main.user_id = ucj.user_id AND main.user_id NOT IN 
                    (SELECT user_id FROM ai_schedule where company_id=".$company_id." AND assessment_id=".$assessment_id." ) 
                    ORDER BY main.user_id";
        }elseif($assessment_type == 1 || $assessment_type == 2){        
            $query  = "SELECT
                        main.*,@dcp AS previous,
                        CONCAT('Q',CONVERT (( SELECT CASE WHEN main.user_id = previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
                        @dcp := main.user_id AS current,
                        CONCAT(main.user_id,'-',main.question_id) as uid,
                        ucj.total_question 
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
                        CROSS JOIN ( SELECT arc.user_id,count(arc.trans_id) as total_question from assessment_results as arc 
                        WHERE arc.company_id = '".$assessment_id."' AND arc.assessment_id='".$assessment_id."'  AND arc.trans_id > 0 AND arc.question_id > 0 AND arc.ftp_status=1 AND arc.vimeo_uri !='' 
                        GROUP BY arc.user_id ORDER BY arc.user_id) as ucj 
                        WHERE main.user_id = ucj.user_id and ucj.total_question='".$total_question."'  
                        AND main.user_id NOT IN (SELECT user_id FROM ai_schedule where company_id = '".$company_id."' AND assessment_id = '".$assessment_id."' )
                    ORDER BY
                    main.user_id, main.trans_id";
        }

        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_schedule(){
        $query  = "SELECT * from ai_cronjob WHERE process_status=1 order by id ASC LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_report_schedule(){
        $query  = "SELECT * from ai_cronreports order by id ASC";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_unique_candidates($company_id,$assessment_id){
        $query  = "SELECT DISTINCT company_id,assessment_id,user_id,pdf_filename,mpdf_filename,cpdf_filename from ai_schedule
        WHERE company_id='".$company_id."' AND assessment_id='".$assessment_id."'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_task(){
        $query  = "SELECT ais.*,CONCAT( du.firstname, ' ', du.lastname ) AS user_name,CONCAT(du.firstname,' ',du.lastname,' ',du.user_id) AS user_name_id,c.portal_name,am.assessment from ai_schedule  as ais 
        LEFT JOIN company AS c ON ais.company_id = c.id 
        LEFT JOIN assessment_mst AS am ON ais.assessment_id = am.id AND ais.company_id = am.company_id 
        LEFT JOIN device_users as du on ais.user_id = du.user_id 
        WHERE ais.assessment_type = 3
        ORDER BY ais.assessment_id ,ais.user_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function cronjob_status($status){
        $query = "UPDATE ai_cronjob SET process_started='".$status."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_status($status,$company_id,$assessment_id,$user_id,$trans_id,$question_id){
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE ai_schedule SET task_status='".$status."',task_status_dttm='".$now."' WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."' AND 
                trans_id='".$trans_id."' AND 
                question_id='".$question_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
			return TRUE;
		} else {
			return FALSE;
		}
    }
    public function update_status_failed_message($message,$company_id,$assessment_id,$user_id,$trans_id,$question_id){
        $query = 'UPDATE ai_schedule SET task_failed_message="'.$message.'" WHERE 
                company_id="'.$company_id.'" AND 
                assessment_id="'.$assessment_id.'" AND 
                user_id="'.$user_id.'" AND 
                trans_id="'.$trans_id.'" AND 
                question_id="'.$question_id.'"';
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
			return TRUE;
		} else {
			return FALSE;
		}
    }
    public function update_xls_status($status,$company_id,$assessment_id,$user_id,$trans_id,$question_id,$file_name){
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE ai_schedule SET xls_imported=0,xls_generated='".$status."',xls_generated_dttm='".$now."', xls_filename='".$file_name."' WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."' AND 
                trans_id='".$trans_id."' AND 
                question_id='".$question_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function update_xls_import_status($company_id,$assessment_id,$user_id,$trans_id,$question_id){
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE ai_schedule SET xls_imported=1,xls_imported_dttm='".$now."' WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."' AND 
                trans_id='".$trans_id."' AND 
                question_id='".$question_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function update_user_lock_status($company_id,$assessment_id,$user_id){
        $query = "UPDATE ai_schedule SET pdf_generated=2 WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function get_overall_score_rank($company_id,$assessment_id,$user_id){
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
       $query  = "SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND(sum(ps.score)/count(ps.question_id),2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id='".$user_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_questions($company_id,$assessment_id){
        $query  = "SELECT main.*,@dcp AS previous,
        CONCAT('Q',CONVERT ((SELECT CASE WHEN main.question_id != previous THEN @cnt := @cnt + 1 ELSE @cnt := 1 END ),UNSIGNED INTEGER)) AS question_series,
        @dcp := main.question_id AS current 
        FROM (SELECT DISTINCT ar.question_id, aq.question,ar.trans_id FROM
            assessment_results AS ar
            LEFT JOIN company AS c ON ar.company_id = c.id
            LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id
            LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id
            LEFT JOIN assessment_question AS aq ON ar.question_id = aq.id 
        WHERE
            ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id."' AND ar.trans_id > 0 AND ar.question_id > 0 
        ORDER BY ar.user_id, ar.trans_id) AS main
        CROSS JOIN ( SELECT @cnt := 0, @dcp := 0) AS qcounter";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_question_your_score($company_id,$assessment_id,$user_id,$question_id){
        $query  = "SELECT sum(ps.score)/count(*) as score FROM ai_subparameter_score as ps 
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.question_id ='".$question_id."'
        GROUP BY ps.user_id ,ps.question_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_question_minmax_score($company_id,$assessment_id,$question_id){
        $query  = "SELECT max( main.score ) AS max_score,min( main.score ) AS min_score FROM(
        SELECT ar.user_id,ar.question_id,round( IFNULL( sum( ps.score ) / count( ps.question_id ), 0 ), 2 ) AS score 
        FROM assessment_results AS ar
        LEFT JOIN ai_subparameter_score AS ps ON ar.company_id = ps.company_id AND ar.assessment_id = ps.assessment_id AND ar.user_id = ps.user_id AND ar.question_id = ps.question_id 
        AND ps.parameter_type = 'parameter' 
        WHERE ar.company_id = '".$company_id."' AND ar.assessment_id = '".$assessment_id."' AND ar.question_id ='".$question_id."'
        GROUP BY ar.user_id,ar.question_id ORDER BY ar.user_id, ar.question_id) AS main";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_your_video($company_id,$assessment_id,$user_id,$trans_id,$question_id){
        $query  = "SELECT CONCAT('https://player.vimeo.com/video/',vimeo_uri) as vimeo_url
                FROM assessment_results WHERE
                company_id = '".$company_id."' 
                AND assessment_id = '".$assessment_id."' 
                AND user_id = '".$user_id."' 
                AND trans_id = '".$trans_id."'
                AND question_id = '".$question_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_best_video($company_id,$assessment_id,$question_id){
        $query  = "SELECT ps.* ,CONCAT('https://player.vimeo.com/video/',ar.vimeo_uri) as vimeo_url
        FROM ai_subparameter_score as ps 
        LEFT JOIN assessment_results AS ar ON ps.company_id = ar.company_id 
        AND ps.assessment_id = ar.assessment_id
        AND ps.user_id = ar.user_id
        AND ps.trans_id = ar.trans_id
        AND ps.question_id = ar.question_id  AND ar.question_id = '".$question_id."'
        WHERE ps.parameter_type = 'parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.question_id ='".$question_id."' AND (ps.user_id) IN 
        (SELECT temp.user_id FROM (
        SELECT main.user_id, max(main.score) as score FROM (
        SELECT ps.user_id,ROUND(sum(ps.score)/count(ps.question_id),2) as score FROM ai_subparameter_score as ps
        WHERE ps.parameter_type ='parameter' AND        
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.question_id ='".$question_id."'
        group by ps.user_id,ps.question_id order by score desc) as main) as temp) LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameters($company_id,$assessment_id){
        $query  = "SELECT DISTINCT ps.parameter_id,ps.parameter_label_id,p.description as parameter_name,pl.description as parameter_label_name FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
        ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id){
        $query  = "SELECT round(sum(ps.score)/count(*),2) as score FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.parameter_id ='".$parameter_id."' AND ps.parameter_label_id ='".$parameter_label_id."'
        ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameter_minmax_score($company_id,$assessment_id,$parameter_id,$parameter_label_id){
        $query = "SELECT max( score ) as max_score,min( score ) as min_score FROM(
            SELECT ps.user_id,ps.parameter_id,ps.parameter_label_id,sum( ps.score ) / count( ps.parameter_id ) AS score ,count( ps.parameter_id ) FROM
            ai_subparameter_score AS ps
            LEFT JOIN parameter_mst AS p ON ps.parameter_id = p.id
            LEFT JOIN parameter_label_mst AS pl ON ps.parameter_label_id = pl.id 
            AND ps.parameter_id = pl.parameter_id 
            WHERE ps.parameter_type ='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' 
            AND ps.parameter_id ='".$parameter_id."' AND ps.parameter_label_id ='".$parameter_label_id."'
            GROUP BY ps.user_id,ps.parameter_id,ps.parameter_label_id
            ORDER BY ps.user_id,ps.parameter_id,ps.parameter_label_id) AS main";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_pdf_status($company_id,$assessment_id,$user_id,$file_name){
        $query = "UPDATE ai_schedule SET pdf_generated=1,pdf_filename='".$file_name."' WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function get_user_rated_by_manager($assessment_id){
        $query = "SELECT count(ais.user_id) as ai_count,count(art.user_id) as manual_count FROM `ai_schedule` as ais
                left join assessment_results_trans as art on ais.assessment_id = art.assessment_id AND  ais.user_id = art.user_id
                WHERE ais.assessment_id ='".$assessment_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_user_lock_manual_status($company_id,$assessment_id,$user_id){
        $query = "UPDATE ai_schedule SET mpdf_generated=2 WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function get_manager_name($assessment_id,$user_id){
        $query = "SELECT DISTINCT trainer_id as manager_id, CONCAT(c.first_name,' ',c.last_name) as manager_name 
        FROM assessment_mapping_user as art 
        LEFT JOIN company_users as c on art.trainer_id = c.userid
        WHERE assessment_id='".$assessment_id."' and user_id ='".$user_id."'
        LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_overall_score_rank($company_id,$assessment_id,$user_id){
        $query  = "SELECT * FROM (
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
            SELECT ps.user_id,(sum(ps.percentage)/count( ps.question_id)) AS overall_score 
            FROM assessment_results_trans AS ps 
            WHERE ps.user_id='".$user_id."' AND ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
            WHERE q.user_id='".$user_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_question_your_score($company_id,$assessment_id,$user_id,$question_id){
        $query  = "SELECT sum(ps.percentage)/count(*) as score FROM assessment_results_trans as ps 
        WHERE 
         ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.question_id ='".$question_id."'
        GROUP BY ps.user_id ,ps.question_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_question_minmax_score($company_id,$assessment_id,$question_id){
        $query = "SELECT max(ps.percentage)/count(ps.parameter_id) as max_score, min(ps.percentage)/count(ps.parameter_id) as 
        min_score FROM assessment_results_trans as ps WHERE ps.assessment_id= '".$assessment_id."'
         AND ps.question_id= '".$question_id."' GROUP BY ps.user_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_best_video($company_id,$assessment_id,$question_id){
        $query  = 
        "SELECT ps.* ,CONCAT('https://player.vimeo.com/video/',ar.vimeo_uri) as vimeo_url
        FROM assessment_results_trans as ps 
        LEFT JOIN assessment_results AS ar ON 
         ps.assessment_id = ar.assessment_id
        AND ps.user_id = ar.user_id
        
        AND ps.question_id = ar.question_id 
        WHERE (ps.question_id,ps.percentage) IN 
        (SELECT ps.question_id,max(ps.percentage) as score
        FROM assessment_results_trans AS ps 
        WHERE 
        ps.assessment_id = '".$assessment_id."'  AND ps.question_id = '".$question_id."'
        group by ps.question_id
        ) LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manager_comments($assessment_id,$user_id,$question_id,$manager_id){
        $query  = "SELECT remarks FROM assessment_trainer_remarks as ps 
                WHERE ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.question_id ='".$question_id."' AND trainer_id='".$manager_id."'
                LIMIT 0,1";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id){
        $query  = "SELECT avg(percentage) as percentage FROM assessment_results_trans as ps
        WHERE ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.parameter_id ='".$parameter_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_manual_parameter_minmax_score($user_id,$assessment_id,$parameter_id,$parameter_label_id){
        $query = "SELECT ps.user_id, max(ps.percentage) as max_score, min(ps.percentage) as 
        min_score FROM assessment_results_trans as ps WHERE ps.user_id='".$user_id."'
        AND ps.parameter_id = '".$parameter_id."' GROUP BY ps.parameter_id, ps.user_id";
     
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_manual_pdf_status($company_id,$assessment_id,$user_id,$file_name){
        $query = "UPDATE ai_schedule SET mpdf_generated=1,mpdf_filename='".$file_name."' WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function update_user_lock_combined_status($company_id,$assessment_id,$user_id){
        $query = "UPDATE ai_schedule SET cpdf_generated=2 WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function get_user_overall_score_combined($company_id,$assessment_id,$user_id){
        $query= "SELECT FORMAT(sum(comb.overall_score)/count(comb.overall_score),2) as overall_score from (SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND(sum(ps.percentage)/count(ps.question_id),2) AS overall_score 
            FROM assessment_results_trans AS ps 
            WHERE  ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id= '".$user_id."'
            UNION ALL
            SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND(sum(ps.score)/count(ps.question_id),2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id= '".$user_id."') as comb;";
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
    public function get_question_manual_score($assessment_id,$user_id,$question_id){
        $query  = "SELECT sum(ps.percentage)/count(ps.parameter_id) as score FROM assessment_results_trans as ps 
        WHERE 
        ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.question_id ='".$question_id."'
        GROUP BY ps.user_id ,ps.question_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameter_manual_score($assessment_id,$user_id,$parameter_id,$parameter_label_id){
        $query  = "SELECT avg(percentage) as percentage FROM assessment_results_trans as ps 
        WHERE ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.parameter_id ='".$parameter_id."'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function update_combined_pdf_status($company_id,$assessment_id,$user_id,$file_name){
        $query = "UPDATE ai_schedule SET cpdf_generated=1,cpdf_filename='".$file_name."' WHERE 
                company_id='".$company_id."' AND 
                assessment_id='".$assessment_id."' AND 
                user_id='".$user_id."'";
        $this->db->query($query);
        $error = $this->db->error();
        if ($error['code'] === 0 OR ($this->db->affected_rows() > 0)){
            return TRUE;
		} else {
			return FALSE;
		}
    }
    public function get_assessment_trans_parameter($assessment_id,$question_id){
        $query = "SELECT DISTINCT parameter_id,parameter_label_id FROM assessment_trans_sparam 
        WHERE assessment_id = '".$assessment_id."' and question_id='".$question_id."' order by parameter_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_trans_subparameter($assessment_id,$question_id){
        $query = "SELECT DISTINCT parameter_id,sub_parameter_id FROM assessment_trans_sparam 
        WHERE assessment_id = '".$assessment_id."' and question_id='".$question_id."' order by parameter_id,sub_parameter_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_trans_sentence_keyword($assessment_id,$question_id){
        $query = "SELECT DISTINCT parameter_id,parameter_label_id,sub_parameter_id,type_id,sentence_keyword FROM assessment_trans_sparam 
        WHERE assessment_id = '".$assessment_id."' and question_id='".$question_id."' and sentence_keyword!=''
        ORDER BY parameter_id,parameter_label_id,sub_parameter_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function audio_details($assessment_id,$question_id,$user_id,$atomdb=null)
    {
        $query  = "SELECT * FROM `ai_cosine_score` WHERE user_id='$user_id' AND assessment_id='$assessment_id' AND current_question_id='$question_id' 
                   AND attempts IN (SELECT MAX(attempts) FROM `ai_cosine_score` WHERE user_id='$user_id' AND assessment_id='$assessment_id' AND current_question_id='$question_id')";
       
      
            $result = $this->db->query($query);
            return $result->row();
       
    }
}
