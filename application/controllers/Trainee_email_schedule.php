<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Trainee_email_schedule extends MY_Controller {
	function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('ai_schedule');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('common_model');
        $this->load->model('trainee_email_schedule_model');
    }
	
	public function index() {
        $data['module_id'] = '100.01';
        $data['acces_management'] = $this->acces_management;
        $_assessment_result = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'status=1','assessment');
        $data['company_id'] = $this->mw_session['company_id'];
        $data['assessment_result'] = $_assessment_result;
        $this->load->view('email_schedule/index',$data);
    }
	
	function fetch_participants(){
        $html               = '';
        $company_id         = $this->input->post('company_id', true);
        $asssessment_id     = $this->input->post('assessment_id', true);
        $report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="'.$company_id.'" AND id="'.$asssessment_id.'"');
        $report_type        = 0;
        if (isset($report_type_result) AND count((array)$report_type_result)>0){
            $report_type = (int)$report_type_result->report_type;
        }
        $_participants_result         = $this->trainee_email_schedule_model->get_distinct_participants($company_id,$asssessment_id);
        $data['report_type']          = $report_type;
        $data['_participants_result'] = $_participants_result;

        $total_questions_played        = 0;
        $total_task_completed          = 0;
        $total_manual_rating_completed = 0;
        $show_ai_pdf                   = false;
        $show_manual_pdf               = false;
        $is_schdule_running            = false;
        $show_reports_flag             = false;
        $_total_played_result     = $this->common_model->get_value('assessment_results', 'count(*) as total', "company_id = '".$company_id."' AND assessment_id = '".$asssessment_id."' AND trans_id > 0 AND question_id > 0 AND vimeo_uri!='' AND ftp_status=1");
        if (isset($_total_played_result) AND count((array)$_total_played_result)>0){
            $total_questions_played = $_total_played_result->total;
        }
        $_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'"');
        if (isset($_tasksc_results) AND count((array)$_tasksc_results)>0){
            $total_task_completed = $_tasksc_results->total;
        }
        $_manualrate_results     = $this->common_model->get_value('assessment_results_trans', 'count(DISTINCT user_id,question_id) as total', 'assessment_id="'.$asssessment_id.'"');
        if (isset($_manualrate_results) AND count((array)$_manualrate_results)>0){
            $total_manual_rating_completed = $_manualrate_results->total;
        }
        $_schdule_running_result     = $this->common_model->get_value('ai_cronjob', '*', 'assessment_id="'.$asssessment_id.'"');
        if (isset($_schdule_running_result) AND count((array)$_schdule_running_result)>0){
            $is_schdule_running = true;
        }
        $show_report_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'" AND show_reports="1"');
        if (isset($show_report_result) AND count((array)$show_report_result)>0){
            $show_reports_flag = true;
        }
        if (((int)$total_questions_played >= (int)$total_task_completed) AND ((int)$total_task_completed>0)) {
            $show_ai_pdf = true;
        }
        if ((int)$total_questions_played == (int)$total_manual_rating_completed){
            $show_manual_pdf = true;
        }
        $data['show_reports_flag'] = $show_reports_flag;
        $data['show_ai_pdf']       = $show_ai_pdf;
        $data['show_manual_pdf']   = $show_manual_pdf;
        $html                      = $this->load->view('email_schedule/load_participants',$data,true);
        $output['html']            = $html;
        $output['success']         = "true";
        $output['message']         = "";
        echo json_encode($output);
    }
}