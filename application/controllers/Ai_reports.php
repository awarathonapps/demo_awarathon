<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');
class Ai_reports extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('ai_process_reports');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('ai_reports_model');
    }
    public function index()
    {
        $data['module_id'] = '14.02';
        $data['acces_management'] = $this->acces_management;
        $data['assessment'] = $this->ai_reports_model->get_assessments();
        $data['assessment_manager'] = $this->ai_reports_model->get_all_assessment_manager();
        $data['company_id'] = $this->mw_session['company_id'];
        $this->load->view('ai_reports/index_tabs', $data);
    }
    //AI Process functions -------------------------------------------------------------------------------------------------------------------
    function fetch_process_participants()
    {
        $html = '';
        $company_id = $this->mw_session['company_id'];
        $assessment_id = $this->input->post('assessment_id', true);
        $get_ass_type = $this->common_model->get_value('assessment_mst', 'assessment_type', 'id=' . $assessment_id);
        $assessment_type = $get_ass_type->assessment_type;
        $start_date = '';
        $end_date = date("Y-m-d h:i:s");
        // $_participants_result = $this->ai_reports_model->get_process_participants($company_id, $assessment_id, $start_date, $end_date);
        if ($assessment_type == 3) {
            $_participants_result = $this->ai_reports_model->get_process_participants_trinity($company_id, $assessment_id, $start_date, $end_date);
        } else {
            $_participants_result = $this->ai_reports_model->get_process_participants($company_id, $assessment_id, $start_date, $end_date);
        }
        $data['_participants_result'] = $_participants_result;
        // $_cronjob_result =$this->ai_reports_model->get_process_schedule($company_id,$asssessment_id);
        // if (isset($_cronjob_result) AND count((array)$_cronjob_result)>0){
        //     $data['_cronjob_result'] = $_cronjob_result->process_status;
        // }else{
        //     $data['_cronjob_result'] = 0;
        // }
        // $assessment_type = $this->common_model->get_value('assessment_mst', 'assessment_type', "id = $assessment_id");
        $data['assessment_type'] = $assessment_type;
        $data['start_date'] = isset($start_date) ? $start_date : '';
        $data['end_date'] = isset($end_date) ? $end_date : '';
        $data['IsCustom'] = isset($IsCustom) ? $IsCustom : '';
        $data['assessment_id'] = isset($assessment_id) ? $assessment_id : '';
        $data['count_records'] = count($_participants_result);
        $html = $this->load->view('ai_reports/ai_process_participants', $data, true);
        $data['html'] = $html;
        $data['success'] = "true";
        $data['message'] = "";
        echo json_encode($data);
    }
    function task_status()
    {
        $company_id = $this->input->post('company_id');
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $trans_id = $this->input->post('trans_id');
        $question_id = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid = $this->input->post('uid');
        if ($company_id != "" and $assessment_id != "" and $user_id != "" and $trans_id != "" and $question_id != "") {
            $task_id = $this->common_model->get_value('ai_schedule', 'task_id,task_status', 'company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND user_id="' . $user_id . '" AND trans_id="' . $trans_id . '" AND question_id="' . $question_id . '"');
            if (isset($task_id) and count((array) $task_id) > 0) {
                if ($task_id->task_status == 1 or $task_id->task_status == "1") {
                    $output = json_decode('{"success": "true", "message": "Completed"}');
                    echo json_encode($output);
                } else if ($task_id->task_status == 2 or $task_id->task_status == "2") {
                    $output = json_decode('{"success": "false", "message": "Active"}');
                    echo json_encode($output);
                } else if ($task_id->task_status == 3 or $task_id->task_status == "3") {
                    $output = json_decode('{"success": "false", "message": "Running"}');
                    echo json_encode($output);
                } else if ($task_id->task_status == 4 or $task_id->task_status == "4") {
                    $output = json_decode('{"success": "false", "message": "Failed"}');
                    echo json_encode($output);
                } else if ($task_id->task_status == 5 or $task_id->task_status == "5") {
                    $output = json_decode('{"success": "false", "message": "Update failed"}');
                    echo json_encode($output);
                } else {
                    $output = json_decode('{"success": "false", "message": "Active"}');
                    echo json_encode($output);
                }
            } else {
                $output = json_decode('{"success": "false", "message": "Task id missing"}');
                echo json_encode($output);
            }
        } else {
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function task_error_log()
    {
        $company_id = $this->input->post('company_id');
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $trans_id = $this->input->post('trans_id');
        $question_id = $this->input->post('question_id');
        if ($company_id != "" and $assessment_id != "" and $user_id != "" and $trans_id != "" and $question_id != "") {
            $task_result = $this->common_model->get_value('ai_schedule', 'task_id', 'company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND user_id="' . $user_id . '" AND trans_id="' . $trans_id . '" AND question_id="' . $question_id . '"');
            if (isset($task_result) and count((array) $task_result) > 0) {
                try {
                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/task_error_details.py --task_id='" . $task_result->task_id . "' 2>&1"));
                    $_output = print_r($output, true);
                    $encode_output = '{"success": "true", "message": ' . json_encode($_output) . '}';
                    echo $encode_output;
                } catch (Exception $e) {
                    $output = json_decode('{"success": "false", "message": "Script failed"}');
                    echo json_encode($output);
                }
            } else {
                $output = json_decode('{"success": "false", "message": "Task id missing"}');
                echo json_encode($output);
            }
        } else {
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function report_status()
    {
        $company_id = $this->input->post('company_id');
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $trans_id = $this->input->post('trans_id');
        $question_id = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid = $this->input->post('uid');
        if ($company_id != "" and $assessment_id != "" and $user_id != "" and $trans_id != "" and $question_id != "") {
            $task_id = $this->common_model->get_value('ai_schedule', 'task_id,xls_generated', 'company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND user_id="' . $user_id . '" AND trans_id="' . $trans_id . '" AND question_id="' . $question_id . '"');
            if (isset($task_id) and count((array) $task_id) > 0) {
                if ($task_id->xls_generated == 1 or $task_id->xls_generated == "1") {
                    $output = json_decode('{"success": "true", "message": "Excel Generated"}');
                    echo json_encode($output);
                } else if ($task_id->xls_generated == 2 or $task_id->xls_generated == "2") {
                    $output = json_decode('{"success": "false", "message": "Script failed"}');
                    echo json_encode($output);
                } else {
                    $output = json_decode('{"success": "", "message": ""}');
                    echo json_encode($output);
                }
            } else {
                $output = json_decode('{"success": "false", "message": "Task id missing"}');
                echo json_encode($output);
            }
        } else {
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function import_excel()
    {
        $company_id = $this->input->post('company_id');
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $trans_id = $this->input->post('trans_id');
        $question_id = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid = $this->input->post('uid');
        if ($company_id != "" and $assessment_id != "" and $user_id != "" and $trans_id != "" and $question_id != "") {
            $schedule_result = $this->common_model->get_value('ai_schedule', '*', 'xls_generated=1 AND company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND user_id="' . $user_id . '" AND trans_id="' . $trans_id . '" AND question_id="' . $question_id . '"');
            if (isset($schedule_result) and count((array) $schedule_result) > 0) {
                if ($schedule_result->xls_imported == 1 or $schedule_result->xls_imported == "1") {
                    $output = json_decode('{"success": "true", "message": "File imported successfully."}');
                } else {
                    $file_name = $schedule_result->xls_filename;
                    $absolute_file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file_name;
                    $temp_file_path = $file_name;
                    if (file_exists($temp_file_path) == TRUE) {
                    } else {
                        $output = json_decode('{"success": "false", "message": "File not exists"}');
                    }
                }
            } else {
                $output = json_decode('{"success": "false", "message": "No records are associated with this task in a database."}');
            }
        } else {
            $output = json_decode('{"success": "false", "message": "FILE_NOT_FOUND"}');
        }
        echo json_encode($output);
    }
    function check_schedule_completed()
    {
        $_company_id = $this->input->post('company_id', true);
        $_assessment_id = $this->input->post('assessment_id', true);

        $total_task = 0;
        $total_task_completed = 0;
        $total_task_failed = 0;
        $total_xls_completed = 0;
        $total_xlsi_completed = 0;

        $_tasks_results = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'company_id="' . $_company_id . '" AND assessment_id="' . $_assessment_id . '"');
        if (isset($_tasks_results) and count((array) $_tasks_results) > 0) {
            $total_task = $_tasks_results->total;
        }
        $_xlsi_results = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="' . $_company_id . '" AND assessment_id="' . $_assessment_id . '"');
        if (isset($_xlsi_results) and count((array) $_xlsi_results) > 0) {
            $total_xlsi_completed = $_xlsi_results->total;
        }
        if (((int) $total_task == (int) $total_xlsi_completed)) {
            $output = json_decode('{"success": "true", "message": ""}');
        } else {
            $output = json_decode('{"success": "false", "message": ""}');
        }
        echo json_encode($output);
    }

    //AI Reports functions -------------------------------------------------------------------------------------------------------------------
    function fetch_participants()
    {
        $html = '';
        $company_id = $this->input->post('company_id', true);
        $asssessment_id = $this->input->post('assessment_id', true);
        $report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="' . $company_id . '" AND id="' . $asssessment_id . '"');
        $report_type = 0;
        if (isset($report_type_result) and count((array) $report_type_result) > 0) {
            $report_type = (int) $report_type_result->report_type;
        }
        $_participants_result = $this->ai_reports_model->get_distinct_participants($company_id, $asssessment_id);
        $data['report_type'] = $report_type;
        $data['_participants_result'] = $_participants_result;

        $total_questions_played = 0;
        $total_task_completed = 0;
        $total_manual_rating_completed = 0;
        $show_ai_pdf = false;
        $show_manual_pdf = false;
        $is_schdule_running = false;
        $show_reports_flag = false;
        $_total_played_result = $this->common_model->get_value('assessment_results', 'count(*) as total', "company_id = '" . $company_id . "' AND assessment_id = '" . $asssessment_id . "' AND trans_id > 0 AND question_id > 0 AND vimeo_uri!='' AND ftp_status=1");
        if (isset($_total_played_result) and count((array) $_total_played_result) > 0) {
            $total_questions_played = $_total_played_result->total;
        }
        $_tasksc_results = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="' . $company_id . '" AND assessment_id="' . $asssessment_id . '"');
        if (isset($_tasksc_results) and count((array) $_tasksc_results) > 0) {
            $total_task_completed = $_tasksc_results->total;
        }
        $_manualrate_results = $this->common_model->get_value('assessment_results_trans', 'count(DISTINCT user_id,question_id) as total', 'assessment_id="' . $asssessment_id . '"');
        if (isset($_manualrate_results) and count((array) $_manualrate_results) > 0) {
            $total_manual_rating_completed = $_manualrate_results->total;
        }
        $_schdule_running_result = $this->common_model->get_value('ai_cronjob', '*', 'assessment_id="' . $asssessment_id . '"');
        if (isset($_schdule_running_result) and count((array) $_schdule_running_result) > 0) {
            $is_schdule_running = true;
        }
        $show_report_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="' . $company_id . '" AND assessment_id="' . $asssessment_id . '" AND show_reports="1"');
        if (isset($show_report_result) and count((array) $show_report_result) > 0) {
            $show_reports_flag = true;
        }
        if (((int) $total_questions_played >= (int) $total_task_completed) and ((int) $total_task_completed > 0)) {
            $show_ai_pdf = true;
        }
        if (((int) $total_task_completed >= (int) $total_questions_played) and ((int) $total_questions_played > 0)) {
            $show_ai_pdf = true;
        }
        if ((int) $total_questions_played >= (int) $total_manual_rating_completed) {
            $show_manual_pdf = true;
        }
        $_user_rating_given = $this->common_model->get_selected_values('assessment_results_trans', 'DISTINCT user_id,question_id', 'assessment_id="' . $asssessment_id . '"');
        $data['show_reports_flag'] = $show_reports_flag;
        $data['show_ai_pdf'] = $show_ai_pdf;
        $data['show_manual_pdf'] = $show_manual_pdf;
        $data['user_rating'] = $_user_rating_given;
        $html = $this->load->view('ai_reports/load_participants', $data, true);
        $output['html'] = $html;
        $output['success'] = "true";
        $output['message'] = "";
        echo json_encode($output);
    }
    public function fetch_questions()
    {
        $company_id = $this->input->post('company_id', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $user_id = $this->input->post('user_id', true);
        $_participants_result = $this->ai_reports_model->get_questions_user_wise($company_id, $assessment_id, $user_id);
        $data['_participants_result'] = $_participants_result;
        $html = $this->load->view('ai_reports/load_questions', $data, true);
        $data['html'] = $html;
        $data['success'] = "true";
        $data['message'] = "";
        echo json_encode($data);
    }
    public function view_ai_reports($_company_id, $_assessment_id, $_user_id)
    {
        if ($_company_id == "" or $_assessment_id == "" or $_user_id == "") {
            echo "Invalid parameter passed";
        } else {
            //GET COMPANY DETAILS
            $company_name = '';
            $company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $_company_id . '"');
            if (isset($company_result) and count((array) $company_result) > 0) {
                $company_name = $company_result->company_name;
                // $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
            $data['company_logo'] = $company_logo;

            //spotlight change -----
            $assessment_type = '';
            $assessment_result = $this->common_model->get_value('assessment_mst', 'assessment_type', 'id="' . $_assessment_id . '"');
            if (isset($assessment_result) and count((array)$assessment_result) > 0) {
                $assessment_type = $assessment_result->assessment_type;
            }
            $data['assessment_type'] = $assessment_type;
            //spotlight change -----

            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="' . $_user_id . '"');
            if (isset($participant_result) and count((array) $participant_result) > 0) {
                $participant_name = $participant_result->firstname . " " . $participant_result->lastname . " - " . $_user_id;
            }
            $data['participant_name'] = $participant_name;
            $data['attempt'] = '';
            $attempt_data = $this->ai_reports_model->assessment_attempts_data($_assessment_id, $_user_id);
            if (count((array) $attempt_data) > 0) {
                $data['attempt'] = $attempt_data->attempts . '/' . $attempt_data->total_attempts;
            }
            //OVERALL SCORE
            $overall_score = 0;
            $your_rank = 0;
            // $istester = 0;
            $overall_score_result = $this->ai_reports_model->get_overall_score_rank($_company_id, $_assessment_id, $_user_id);
            if (isset($overall_score_result) and count((array) $overall_score_result) > 0) {
                $overall_score = $overall_score_result->overall_score;
                $your_rank = $overall_score_result->final_rank;
                // $istester = $overall_score_result->istester;
            }
            $data['overall_score'] = $overall_score;
            $data['your_rank'] = $your_rank;

            // Industry thresholds - 04-04-2023
            $this->db->select('company_id,range_from,range_to,title,rating');
            $this->db->from('industry_threshold_range');
            $this->db->order_by('rating', 'asc');
            $data['color_range'] = $this->db->get()->result();
            // end 04-04-2023

            $rating = '';
            // if ((float) $overall_score >= 69.9) {
            //     $rating = 'A';
            // } else if ((float) $overall_score < 69.9 and (float) $overall_score >= 63.23) {
            //     $rating = 'B';
            // } else if ((float) $overall_score < 63.23 and (float) $overall_score >= 54.9) {
            //     $rating = 'C';
            // } else if ((float) $overall_score < 54.9) {
            //     $rating = 'D';
            // }

            if ((float) $overall_score < $data['color_range'][0]->range_to . '.99' and (float) $overall_score >= $data['color_range'][0]->range_from) {
                $rating = $data['color_range'][0]->rating;
            } else if ((float) $overall_score < $data['color_range'][1]->range_to . '.99' and (float) $overall_score >= $data['color_range'][1]->range_from) {
                $rating = $data['color_range'][1]->rating;
            } else if ((float) $overall_score < $data['color_range'][2]->range_to . '.99' and (float) $overall_score >= $data['color_range'][2]->range_from) {
                $rating = $data['color_range'][2]->rating;
            } else if ((float) $overall_score < $data['color_range'][3]->range_to . '.99' and (float) $overall_score >= $data['color_range'][3]->range_from) {
                $rating = $data['color_range'][3]->rating;
            } else if ((float) $overall_score < $data['color_range'][4]->range_to . '.99' and (float) $overall_score >= $data['color_range'][4]->range_from) {
                $rating = $data['color_range'][4]->rating;
            } else if ((float) $overall_score < $data['color_range'][5]->range_to . '.99' and (float) $overall_score >= $data['color_range'][5]->range_from) {
                $rating = $data['color_range'][5]->rating;
            } else {
                $rating = '-';
            }
            $data['rating'] = $rating;
            //QUESTIONS LIST
            $best_video_list = [];
            $questions_list = [];
            $partd_list = [];
            $i = 0;
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id,$assessment_type,$_user_id); //Spotlight assessment
            // $question_result = $this->ai_reports_model->get_questions($_company_id, $_assessment_id);
            $question_minmax_score_result = $this->ai_reports_model->get_question_minmax_score($_company_id, $_assessment_id);
            $question_minmax_score_result_temp = [];
            if (!empty($question_minmax_score_result)) {
                foreach ($question_minmax_score_result as $que) {
                    $question_minmax_score_result_temp[$que->question_id] = [
                        'max_score' => $que->max_score,
                        'min_score' => $que->min_score
                    ];
                }
            }
            // echo '<pre>';
            // print_r($question_result);exit;
            foreach ($question_result as $qr) {
                $question_id = $qr->question_id;
                $question = $qr->question;
                $question_series = $qr->question_series;
                $_trans_id = $qr->trans_id;
                $question_your_score_result = $this->ai_reports_model->get_question_your_score($_company_id, $_assessment_id, $_user_id, $question_id);
                $question_your_video_result = $this->ai_reports_model->get_your_video($_company_id, $_assessment_id, $_user_id, $_trans_id, $question_id, $assessment_type);                
                $question_best_video_result = $this->ai_reports_model->get_best_video($_company_id, $_assessment_id, $question_id, $assessment_type);
                $ai_sentkey_score_result = $this->common_model->get_selected_values('ai_sentkey_score', '*', 'company_id="' . $_company_id . '" AND assessment_id="' . $_assessment_id . '" AND user_id="' . $_user_id . '" AND trans_id="' . $_trans_id . '" AND question_id="' . $question_id . '"');
                $your_vimeo_url = "";
                if (isset($question_your_video_result) and count((array) $question_your_video_result) > 0) {
                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                }
                $best_vimeo_url = "";
                if (isset($question_best_video_result) and count((array) $question_best_video_result) > 0) {
                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                    $ai_best_ideal_video_result = $this->common_model->get_value('ai_best_ideal_video', '*', 'assessment_id="' . $_assessment_id . '" AND question_id="' . $question_id . '"');
                    if (isset($ai_best_ideal_video_result) and count((array) $ai_best_ideal_video_result) > 0) {
                        $best_vimeo_url = $ai_best_ideal_video_result->best_video_link;
                    }
                } else {
                    $ai_best_ideal_video_result = $this->common_model->get_value('ai_best_ideal_video', '*', 'assessment_id="' . $_assessment_id . '" AND question_id="' . $question_id . '"');
                    if (isset($ai_best_ideal_video_result) and count((array) $ai_best_ideal_video_result) > 0) {
                        $best_vimeo_url = $ai_best_ideal_video_result->best_video_link;
                    }
                }
                $your_score = 0;
                if (isset($question_your_score_result) and count((array) $question_your_score_result) > 0) {
                    $your_score = $question_your_score_result->score;
                }
                $highest_score = 0;
                $lowest_score = 0;
                $failed_counter_your = 0;
                if (isset($question_minmax_score_result_temp) and count((array) $question_minmax_score_result_temp) > 0) {
                    $highest_score = $question_minmax_score_result_temp[$question_id]['max_score'];
                    $lowest_score = $question_minmax_score_result_temp[$question_id]['min_score'];
                }
                $ai_failed_result = $this->common_model->get_value('ai_schedule', '*', 'assessment_id="' . $_assessment_id . '" AND user_id="' . $_user_id . '" AND question_id="' . $question_id . '"');
                if (isset($ai_failed_result) and count((array) $ai_failed_result) > 0) {
                    $failed_counter_your = $ai_failed_result->failed_counter;
                }
                array_push($best_video_list, array(
                    "question_series" => $question_series,
                    "your_vimeo_url" => $your_vimeo_url,
                    "best_vimeo_url" => $best_vimeo_url,
                )
                );
                array_push($questions_list, array(
                    "question_id" => $question_id,
                    "question" => $question,
                    "question_series" => $question_series,
                    "your_score" => $your_score,
                    "highest_score" => $highest_score,
                    "lowest_score" => $lowest_score,
                    "failed_counter_your" => $failed_counter_your,
                )
                );
                $temp_partd_list = [];
                $partd_list[$i]['question_series'] = $question_series;
                $partd_list[$i]['question'] = $question;
                if (isset($ai_sentkey_score_result) and count($ai_sentkey_score_result) > 0) {
                    foreach ($ai_sentkey_score_result as $sksr) {
                        // $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'" AND sentence_keyword LIKE "%'.$sksr->sentance_keyword.'%" ');
                        $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="' . $_assessment_id . '" AND question_id="' . $question_id . '"');
                        $tick_icons = '';
                        if (isset($sentkey_type_result) and count((array)$sentkey_type_result) > 0) {
                            $que_lang = $sentkey_type_result->language_id;
                            // Set different range for English and other languages sentence/keyword
                            $gcolor_score = ($que_lang == 1) ? 60 : 50;
                            $ycolor_high_score = ($que_lang == 1) ? 60 : 50;
                            $ycolor_low_score = ($que_lang == 1) ? 50 : 40;
                            $rcolor_score = ($que_lang == 1) ? 50 : 40;
                            if ($sentkey_type_result->type_id == 1) { //Sentance 
                                if ($sksr->score >= $gcolor_score) {
                                    $tick_icons = 'green';
                                }
                                if ($sksr->score <= $rcolor_score) {
                                    $tick_icons = 'red';
                                }
                                if ($sksr->score > $ycolor_low_score and $sksr->score < $ycolor_high_score) {
                                    $tick_icons = 'yellow';
                                }
                            }
                            if ($sentkey_type_result->type_id == 2) { //Keyword
                                if ($sksr->score >= $gcolor_score) {
                                    $tick_icons = 'green';
                                }
                                if ($sksr->score < $gcolor_score) {
                                    $tick_icons = 'red';
                                }
                            }
                        }
                        array_push($temp_partd_list, array(
                            "sentance_keyword" => $sksr->sentance_keyword,
                            "score" => $sksr->score,
                            "tick_icons" => $tick_icons,
                        )
                        );
                    }
                    $partd_list[$i]['list'] = $temp_partd_list;
                }
                $i++;
            }
            $data['best_video_list'] = $best_video_list;
            $data['questions_list'] = $questions_list;
            $data['partd_list'] = $partd_list;
            //PARAMETER LIST
            $parameter_score = [];
            $parameter_score_result = $this->ai_reports_model->get_parameters($_company_id, $_assessment_id);
            foreach ($parameter_score_result as $psr) {
                $parameter_id = $psr->parameter_id;
                $parameter_label_id = $psr->parameter_label_id;
                $parameter_your_score_result = $this->ai_reports_model->get_parameters_your_score($_company_id, $_assessment_id, $_user_id, $parameter_id, $parameter_label_id);
                $parameter_minmax_score_result = $this->ai_reports_model->get_parameter_minmax_score($_company_id, $_assessment_id, $parameter_id, $parameter_label_id);
                $your_score = 0;
                if (isset($parameter_your_score_result) and count((array) $parameter_your_score_result) > 0) {
                    $your_score = $parameter_your_score_result->score;
                }
                $highest_score = 0;
                $lowest_score = 0;
                if (isset($parameter_minmax_score_result) and count((array) $parameter_minmax_score_result) > 0) {
                    $highest_score = $parameter_minmax_score_result->max_score;
                    $lowest_score = $parameter_minmax_score_result->min_score;
                }
                array_push($parameter_score, array(
                    "parameter_id" => $psr->parameter_id,
                    "parameter_label_id" => $psr->parameter_label_id,
                    "parameter_name" => $psr->parameter_name,
                    "parameter_label_name" => $psr->parameter_label_name,
                    "your_score" => $your_score,
                    "highest_score" => $highest_score,
                    "lowest_score" => $lowest_score,
                )
                );
            }
            $data['parameter_score'] = $parameter_score;
            // $this->load->library('Pdf_Library');
            $data['show_ranking'] = 0;
            $show_ranking_result = $this->common_model->get_value('ai_cronreports', 'show_ranking', 'assessment_id="' . $_assessment_id . '"');
            if (isset($show_ranking_result) and count((array) $show_ranking_result) > 0) {
                $data['show_ranking'] = $show_ranking_result->show_ranking;
            }
            $htmlContent = $this->load->view('ai_reports/ai_pdf', $data, true);

            // //DIVEYSH PANCHAL
            ob_start();
            define('K_TCPDF_EXTERNAL_CONFIG', true);
            $this->load->library('Pdf');
            // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $data['pdf'] = $pdf;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Awarathon');
            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
            $pdf->SetKeywords('Awarathon');
            $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
            $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                <tr>
                    <td style="height:10px;width:60%">
                        <div class="page-title">Sales Readiness Reports</div>
                    </td>
                    <td style="height:10px;width:40%;text-align:right;">
                        <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
                    </td>
                </tr>
            </table>');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, 20);
            //$pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->PrintCoverPageFooter = True;
            $pdf->AddPage();
            $pdf->setJPEGQuality(100);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            $pdf->lastPage();
            ob_end_clean();
            $now = date('YmdHis');
            $file_name = 'C' . $_company_id . 'A' . $_assessment_id . 'U' . $_user_id . 'DTTM' . $now . '.pdf';
            $pdf->Output($file_name, 'I');
        }
    }
    public function view_manual_reports($_company_id, $_assessment_id, $_user_id)
    {
        if ($_company_id == "" or $_assessment_id == "" or $_user_id == "") {
            echo "Invalid parameter passed";
        } else {
            //GET COMPANY DETAILS
            $company_name = '';
            $company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $_company_id . '"');
            if (isset($company_result) and count((array) $company_result) > 0) {
                $company_name = $company_result->company_name;
                // $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
            $data['company_logo'] = $company_logo;

            //spotlight change -----
            $assessment_type = '';
            $assessment_result = $this->common_model->get_value('assessment_mst', 'assessment_type', 'id="' . $_assessment_id . '"');
            if (isset($assessment_result) and count((array)$assessment_result) > 0) {
                $assessment_type = $assessment_result->assessment_type;
            }
            $data['assessment_type'] = $assessment_type;
            //spotlight change -----

            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="' . $_user_id . '"');
            if (isset($participant_result) and count((array) $participant_result) > 0) {
                $participant_name = $participant_result->firstname . " " . $participant_result->lastname . " - " . $_user_id;
            }
            $data['participant_name'] = $participant_name;
            $data['attempt'] = '';
            $attempt_data = $this->ai_reports_model->assessment_attempts_data($_assessment_id, $_user_id);
            if (count((array) $attempt_data) > 0) {
                $data['attempt'] = $attempt_data->attempts . '/' . $attempt_data->total_attempts;
            }
            //GET MANAGER NAME
            $manager_id = '';
            $manager_name = '';
            $manager_result = $this->ai_reports_model->get_manager_name($_assessment_id, $_user_id);
            if (isset($manager_result) and count((array) $manager_result) > 0) {
                $manager_id = $manager_result->manager_id;
                $manager_name = $manager_result->manager_name;
            }
            $data['manager_name'] = $manager_name;


            //OVERALL SCORE
            $overall_score = 0;
            $your_rank = 0;
            $user_rating = $this->common_model->get_selected_values('assessment_results_trans', 'DISTINCT user_id,question_id', 'assessment_id="' . $_assessment_id . '" AND user_id="' . $_user_id . '"');

            // Industry thresholds - 04-04-2023
            $this->db->select('company_id,range_from,range_to,title,rating');
            $this->db->from('industry_threshold_range');
            $this->db->order_by('rating', 'asc');
            $data['color_range'] = $this->db->get()->result();
            // end 04-04-2023  
            if (empty($user_rating)) {
                $data['overall_score'] = 'Not assessed';
                $data['your_rank'] = 'Pending';
                $data['rating'] = 'Pending';
            } else {
                $overall_score_result = $this->ai_reports_model->get_manual_overall_score_rank($_company_id, $_assessment_id, $_user_id);
                if (isset($overall_score_result) and count((array) $overall_score_result) > 0) {
                    $overall_score = $overall_score_result->overall_score;
                    $your_rank = $overall_score_result->final_rank;
                }
                $data['overall_score'] = number_format($overall_score, 2, '.', '') . '%';
                $data['your_rank'] = $your_rank;
                $rating = '';
                // if ((float) $overall_score >= 69.9) {
                //     $rating = 'A';
                // } else if ((float) $overall_score < 69.9 and (float) $overall_score >= 63.23) {
                //     $rating = 'B';
                // } else if ((float) $overall_score < 63.23 and (float) $overall_score >= 54.9) {
                //     $rating = 'C';
                // } else if ((float) $overall_score < 54.9) {
                //     $rating = 'D';
                // }

                if ((float) $overall_score < $data['color_range'][0]->range_to and (float) $overall_score >= $data['color_range'][0]->range_from) {
                    $rating = $data['color_range'][0]->rating;
                } else if ((float) $overall_score < $data['color_range'][1]->range_to . '.99' and (float) $overall_score >= $data['color_range'][1]->range_from) {
                    $rating = $data['color_range'][1]->rating;
                } else if ((float) $overall_score < $data['color_range'][2]->range_to . '.99' and (float) $overall_score >= $data['color_range'][2]->range_from) {
                    $rating = $data['color_range'][2]->rating;
                } else if ((float) $overall_score < $data['color_range'][3]->range_to . '.99' and (float) $overall_score >= $data['color_range'][3]->range_from) {
                    $rating = $data['color_range'][3]->rating;
                } else if ((float) $overall_score < $data['color_range'][4]->range_to . '.99' and (float) $overall_score >= $data['color_range'][4]->range_from) {
                    $rating = $data['color_range'][4]->rating;
                } else if ((float) $overall_score < $data['color_range'][5]->range_to . '.99' and (float) $overall_score >= $data['color_range'][5]->range_from) {
                    $rating = $data['color_range'][5]->rating;
                } else {
                    $rating = '-';
                }
                $data['rating'] = $rating;
            }

            //QUESTIONS LIST
            $best_video_list = [];
            $questions_list = [];
            $partd_list = [];
            $manager_comments_list = [];
            $i = 0;
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id,$assessment_type,$_user_id); //Spotlight assessment
            // $question_result = $this->ai_reports_model->get_questions($_company_id, $_assessment_id);
            foreach ($question_result as $qr) {
                $question_id = $qr->question_id;
                $question = $qr->question;
                $question_series = $qr->question_series;
                $_trans_id = $qr->trans_id;

                $question_your_score_result = $this->ai_reports_model->get_manual_question_your_score($_company_id, $_assessment_id, $_user_id, $question_id);
                $question_minmax_score_result = $this->ai_reports_model->get_manual_question_minmax_score($_company_id, $_assessment_id, $question_id);
                $question_your_video_result = $this->ai_reports_model->get_your_video($_company_id, $_assessment_id, $_user_id, $_trans_id, $question_id, $assessment_type);
                $question_best_video_result = $this->ai_reports_model->get_manual_best_video($_company_id, $_assessment_id, $question_id);
                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($_assessment_id, $_user_id, $question_id, $manager_id);

                $your_vimeo_url = "";
                if (isset($question_your_video_result) and count((array) $question_your_video_result) > 0) {
                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                }

                $best_vimeo_url = "";
                if (isset($question_best_video_result) and count((array) $question_best_video_result) > 0) {
                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                }

                $your_score = 0;
                if (isset($question_your_score_result) and count((array) $question_your_score_result) > 0) {
                    $your_score = number_format($question_your_score_result->score, 2, '.', '') . '%';
                } else {
                    $your_score = 'Not assessed';
                }
                $highest_score = 0;
                $lowest_score = 0;
                if (isset($question_minmax_score_result) and count((array) $question_minmax_score_result) > 0) {
                    $highest_score = $question_minmax_score_result->max_score;
                    $lowest_score = $question_minmax_score_result->min_score;
                }
                $comments = '';
                if (isset($question_manager_comment_result) and count((array) $question_manager_comment_result) > 0) {
                    $comments = $question_manager_comment_result->remarks;
                }

                array_push($best_video_list, array(
                    "question_series" => $question_series,
                    "your_vimeo_url" => $your_vimeo_url,
                    "best_vimeo_url" => $best_vimeo_url,
                )
                );
                array_push($questions_list, array(
                    "question_id" => $question_id,
                    "question" => $question,
                    "question_series" => $question_series,
                    "your_score" => $your_score,
                    "highest_score" => $highest_score,
                    "lowest_score" => $lowest_score,
                )
                );
                array_push($manager_comments_list, array(
                    "question_id" => $question_id,
                    "question" => $question,
                    "question_series" => $question_series,
                    "comments" => $comments,
                )
                );

                $temp_partd_list = [];
                $partd_list[$i]['question_series'] = $question_series;
                $partd_list[$i]['question'] = $question;
                $i++;
            }
            $data['best_video_list'] = $best_video_list;
            $data['questions_list'] = $questions_list;
            $data['manager_comments_list'] = $manager_comments_list;

            //GET OVERALL COMMENTS
            $overall_comments = '';
            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="' . $_assessment_id . '" and user_id="' . $_user_id . '" and trainer_id="' . $manager_id . '"');
            if (isset($overall_comments_result) and count((array) $overall_comments_result) > 0) {
                $overall_comments = $overall_comments_result->remarks;
            }
            $data['overall_comments'] = $overall_comments;

            //PARAMETER LIST
            $parameter_score = [];
            $parameter_manual_score_result = $this->ai_reports_model->get_manual_parameters_score($_company_id, $_assessment_id, $_user_id);
            $parameter_manual_score_result = json_decode(json_encode($parameter_manual_score_result), true);
            $parameter_score = [];
            if (!empty($parameter_manual_score_result)) {
                foreach ($parameter_manual_score_result as $p_result) {
                    $your_score = 0;
                    if (isset($p_result['percentage'])) {
                        $your_score = number_format($p_result['percentage'], 2, '.', '') . '%';
                    } else {
                        $your_score = 'Not assessed';
                    }
                    $parameter_score[] = [
                        'parameter_id' => $p_result['parameter_id'],
                        'parameter_label_id' => $p_result['parameter_label_id'],
                        'parameter_name' => $p_result['parameter_name'],
                        'parameter_label_name' => $p_result['parameter_label_name'],
                        'your_score' => $your_score,
                    ];
                }
            }
            $data['parameter_score'] = $parameter_score;


            // $this->load->library('Pdf_Library');
            $htmlContent = $this->load->view('ai_reports/manual_pdf', $data, true);

            // //DIVEYSH PANCHAL
            ob_start();
            define('K_TCPDF_EXTERNAL_CONFIG', true);
            $this->load->library('Pdf');
            //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            //Below line is added
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $data['pdf'] = $pdf;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Awarathon');
            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
            $pdf->SetKeywords('Awarathon');
            $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
            $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                <tr>
                    <td style="height:10px;width:60%">
                        <div class="page-title">Sales Readiness Reports</div>
                    </td>
                    <td style="height:10px;width:40%;text-align:right;">
                        <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
                    </td>
                </tr>
            </table>');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //$pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->SetAutoPageBreak(TRUE, 20);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            //Added below line: As we don't want footer on front page
            $pdf->PrintCoverPageFooter = True;

            $pdf->AddPage();
            $pdf->setJPEGQuality(100);
            $pdf->SetFont('helvetica', '', 10);

            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            $pdf->lastPage();
            ob_end_clean();

            $now = date('YmdHis');
            $file_name = 'MANU-C' . $_company_id . 'A' . $_assessment_id . 'U' . $_user_id . 'DTTM' . $now . '.pdf';
            $pdf->Output($file_name, 'I');
        }
    }
    public function view_combine_reports($_company_id, $_assessment_id, $_user_id)
    {
        $assessment_type = '';
        if ($_company_id == "" or $_assessment_id == "" or $_user_id == "") {
            echo "Invalid parameter passed";
        } else {
            //GET COMPANY DETAILS
            $company_name = '';
            $company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $_company_id . '"');
            if (isset($company_result) and count((array) $company_result) > 0) {
                $company_name = $company_result->company_name;
                // $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
            $data['company_logo'] = $company_logo;

            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="' . $_user_id . '"');
            if (isset($participant_result) and count((array) $participant_result) > 0) {
                $participant_name = $participant_result->firstname . " " . $participant_result->lastname . " - " . $_user_id;
            }
            $data['participant_name'] = $participant_name;
            $data['attempt'] = '';
            $attempt_data = $this->ai_reports_model->assessment_attempts_data($_assessment_id, $_user_id);
            if (count((array) $attempt_data) > 0) {
                $data['attempt'] = $attempt_data->attempts . '/' . $attempt_data->total_attempts;
            }
            //GET MANAGER NAME
            $manager_id = '';
            $manager_name = '';
            $manager_result = $this->ai_reports_model->get_manager_name($_assessment_id, $_user_id);
            if (isset($manager_result) and count((array) $manager_result) > 0) {
                $manager_id = $manager_result->manager_id;
                $manager_name = $manager_result->manager_name;
            }
            $data['manager_name'] = $manager_name;

            //OVERALL SCORE
            $overall_score = 0;
            $overall_score_result = $this->ai_reports_model->get_user_overall_score_combined($_company_id, $_assessment_id, $_user_id);
            if (isset($overall_score_result) and count((array) $overall_score_result) > 0) {
                $overall_score = $overall_score_result->overall_score;
            }
            $data['overall_score'] = $overall_score;


            //QUESTIONS LIST
            $questions_list = [];
            $manager_comments_list = [];
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id,$assessment_type,$_user_id); //Spotlight assessment
            // $question_result = $this->ai_reports_model->get_questions($_company_id, $_assessment_id);
            foreach ($question_result as $qr) {
                $question_id = $qr->question_id;
                $question = $qr->question;
                $question_series = $qr->question_series;

                $question_ai_score_result = $this->ai_reports_model->get_question_your_score($_company_id, $_assessment_id, $_user_id, $question_id);
                $question_manual_score_result = $this->ai_reports_model->get_question_manual_score($_assessment_id, $_user_id, $question_id);
                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($_assessment_id, $_user_id, $question_id, $manager_id);

                $ai_score = 0;
                if (isset($question_ai_score_result) and count((array) $question_ai_score_result) > 0) {
                    $ai_score = $question_ai_score_result->score;
                }
                $manual_score = 0;
                if (isset($question_manual_score_result) and count((array) $question_manual_score_result) > 0) {
                    $manual_score = $question_manual_score_result->score;
                }
                $comments = '';
                if (isset($question_manager_comment_result) and count((array) $question_manager_comment_result) > 0) {
                    $comments = $question_manager_comment_result->remarks;
                }
                if ($manual_score == 0 || $ai_score == 0) {
                    $combined_score = number_format((($ai_score + $manual_score)), 2);
                } else {
                    $combined_score = number_format((($ai_score + $manual_score) / 2), 2);
                }

                array_push($questions_list, array(
                    "question_id" => $question_id,
                    "question" => $question,
                    "question_series" => $question_series,
                    "ai_score" => $ai_score,
                    "manual_score" => empty($manual_score) ? 'Not assessed' : number_format($manual_score, 2, '.', '') . '%',
                    "combined_score" => $combined_score,
                )
                );

                array_push($manager_comments_list, array(
                    "question_id" => $question_id,
                    "question" => $question,
                    "question_series" => $question_series,
                    "comments" => $comments,
                )
                );

            }
            $data['questions_list'] = $questions_list;
            $data['manager_comments_list'] = $manager_comments_list;


            //GET OVERALL COMMENTS
            $overall_comments = '';
            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="' . $_assessment_id . '" and user_id="' . $_user_id . '" and trainer_id="' . $manager_id . '"');
            if (isset($overall_comments_result) and count((array) $overall_comments_result) > 0) {
                $overall_comments = $overall_comments_result->remarks;
            }
            $data['overall_comments'] = $overall_comments;

            //PARAMETER LIST
            $parameter_score = [];
            $parameter_combined_score = $this->ai_reports_model->get_combined_parameters_your_score($_company_id, $_assessment_id, $_user_id);
            if (!empty($parameter_combined_score)) {
                foreach ($parameter_combined_score as $pcs) {
                    $ai_score = 0;
                    if (isset($pcs->score)) {
                        $ai_score = $pcs->score;
                    }
                    $manual_score = 0;
                    if (isset($pcs->percentage)) {
                        $manual_score = $pcs->percentage;
                    }
                    if ($manual_score == 0 || $ai_score == 0) {
                        $combined_score = number_format((($ai_score + $manual_score)), 2);
                    } else {
                        $combined_score = number_format((($ai_score + $manual_score) / 2), 2);
                    }

                    array_push($parameter_score, array(
                        "parameter_id" => $pcs->parameter_id,
                        "parameter_label_id" => $pcs->parameter_label_id,
                        "parameter_name" => $pcs->parameter_name,
                        "parameter_label_name" => $pcs->parameter_label_name,
                        "your_score" => $ai_score,
                        "manual_score" => empty($manual_score) ? 'Not assessed' : number_format($manual_score, 2, '.', '') . '%',
                        "combined_score" => $combined_score,
                    )
                    );
                }
            }
            $data['parameter_score'] = $parameter_score;

            // $this->load->library('Pdf_Library');
            $htmlContent = $this->load->view('ai_reports/combined_pdf', $data, true);

            // //DIVEYSH PANCHAL
            ob_start();
            define('K_TCPDF_EXTERNAL_CONFIG', true);
            $this->load->library('Pdf');
            //  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $data['pdf'] = $pdf;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Awarathon');
            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
            $pdf->SetKeywords('Awarathon');
            $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
            $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                <tr>
                    <td style="height:10px;width:60%">
                        <div class="page-title">Sales Readiness Reports</div>
                    </td>
                    <td style="height:10px;width:40%;text-align:right;">
                        <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
                    </td>
                </tr>
            </table>');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //$pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->SetAutoPageBreak(TRUE, 20);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->PrintCoverPageFooter = True;

            $pdf->AddPage();
            $pdf->setJPEGQuality(100);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            $pdf->lastPage();
            ob_end_clean();

            $now = date('YmdHis');
            $file_name = 'COMB-C' . $_company_id . 'A' . $_assessment_id . 'U' . $_user_id . 'DTTM' . $now . '.pdf';
            $pdf->Output($file_name, 'I');
        }
    }
    public function regenerate_pdf()
    {
        $_company_id = 59; //$this->input->post('company_id', true);
        $_assessment_id = 94; //$this->input->post('assessment_id', true);
        $_report_type = 1; //$this->input->post('report_type', true);
        $site_url_result = $this->common_model->get_value('company', 'domin_url', 'id="' . $_company_id . '"');
        $site_url = 'https://demo.awarathon.com';
        if (isset($site_url_result) and count((array) $site_url_result) > 0) {
            $site_url = $site_url_result->domin_url;
        }
        $task_result = $this->ai_reports_model->get_unique_candidates($_company_id, $_assessment_id);
        if (isset($task_result) and count((array) $task_result) > 0) {
            foreach ($task_result as $tdata) {
                $company_id = $tdata->company_id;
                $assessment_id = $tdata->assessment_id;
                $user_id = $tdata->user_id;

                if ($_report_type == 1) { //AI PDF START
                    if ($company_id != "" and $assessment_id != "" and $user_id != "") {

                        //CHECK ALL EXCEL IMPORTED ?
                        $total_video = 0;
                        $total_xls_imported = 0;
                        $total_video_result = $this->common_model->get_value('ai_schedule', 'count(*) as total_video', 'company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '"');
                        if (isset($total_video_result) and count((array) $total_video_result) > 0) {
                            $total_video = (float) $total_video_result->total_video;
                        }
                        $total_xls_imported_result = $this->common_model->get_value('ai_schedule', 'count(*) as total_xls', 'company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND xls_imported=1');
                        if (isset($total_xls_imported_result) and count((array) $total_xls_imported_result) > 0) {
                            $total_xls_imported = (float) $total_xls_imported_result->total_xls;
                        }
                        if ($total_video == $total_xls_imported) {
                            //GET COMPANY DETAILS
                            $company_name = '';
                            $company_logo = 'assets/images/Awarathon-Logo.png';
                            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $company_id . '"');
                            if (isset($company_result) and count((array) $company_result) > 0) {
                                $company_name = $company_result->company_name;
                                $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/' . $company_result->company_logo : '';
                            }
                            $data['company_name'] = $company_name;
                            $data['company_logo'] = $company_logo;

                            //GET PARTICIPANT DETAILS
                            $participant_name = '';
                            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="' . $user_id . '"');
                            if (isset($participant_result) and count((array) $participant_result) > 0) {
                                $participant_name = $participant_result->firstname . " " . $participant_result->lastname . " - " . $user_id;
                            }
                            $data['participant_name'] = $participant_name;

                            //OVERALL SCORE
                            $overall_score = 0;
                            $your_rank = 0;
                            $overall_score_result = $this->ai_reports_model->get_overall_score_rank($company_id, $assessment_id, $user_id);
                            if (isset($overall_score_result) and count((array) $overall_score_result) > 0) {
                                $overall_score = $overall_score_result->overall_score;
                                $your_rank = $overall_score_result->final_rank;
                            }
                            $data['overall_score'] = $overall_score;
                            $data['your_rank'] = $your_rank;

                            $rating = '';
                            if ((float) $overall_score >= 69.9) {
                                $rating = 'A';
                            } else if ((float) $overall_score < 69.9 and (float) $overall_score >= 63.23) {
                                $rating = 'B';
                            } else if ((float) $overall_score < 63.23 and (float) $overall_score >= 54.9) {
                                $rating = 'C';
                            } else if ((float) $overall_score < 54.9) {
                                $rating = 'D';
                            }
                            $data['rating'] = $rating;


                            //QUESTIONS LIST
                            $best_video_list = [];
                            $questions_list = [];
                            $partd_list = [];
                            $i = 0;
                            $question_result = $this->ai_reports_model->get_questions($company_id, $assessment_id);
                            foreach ($question_result as $qr) {
                                $question_id = $qr->question_id;
                                $question = $qr->question;
                                $question_series = $qr->question_series;
                                $_trans_id = $qr->trans_id;

                                $question_your_score_result = $this->ai_reports_model->get_question_your_score($company_id, $assessment_id, $user_id, $question_id);
                                $question_minmax_score_result = $this->ai_reports_model->get_question_minmax_score($company_id, $assessment_id, $question_id);
                                $question_your_video_result = $this->ai_reports_model->get_your_video($company_id, $assessment_id, $user_id, $_trans_id, $question_id);
                                $question_best_video_result = $this->ai_reports_model->get_best_video($company_id, $assessment_id, $question_id);
                                $ai_sentkey_score_result = $this->common_model->get_selected_values('ai_sentkey_score', '*', 'company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND user_id="' . $user_id . '" AND trans_id="' . $_trans_id . '" AND question_id="' . $question_id . '"');

                                $your_vimeo_url = "";
                                if (isset($question_your_video_result) and count((array) $question_your_video_result) > 0) {
                                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                                }

                                $best_vimeo_url = "";
                                if (isset($question_best_video_result) and count((array) $question_best_video_result) > 0) {
                                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                                }

                                $your_score = 0;
                                if (isset($question_your_score_result) and count((array) $question_your_score_result) > 0) {
                                    $your_score = $question_your_score_result->score;
                                }
                                $highest_score = 0;
                                $lowest_score = 0;
                                if (isset($question_minmax_score_result) and count((array) $question_minmax_score_result) > 0) {
                                    $highest_score = $question_minmax_score_result->max_score;
                                    $lowest_score = $question_minmax_score_result->min_score;
                                }

                                array_push($best_video_list, array(
                                    "question_series" => $question_series,
                                    "your_vimeo_url" => $your_vimeo_url,
                                    "best_vimeo_url" => $best_vimeo_url,
                                )
                                );
                                array_push($questions_list, array(
                                    "question_id" => $question_id,
                                    "question" => $question,
                                    "question_series" => $question_series,
                                    "your_score" => $your_score,
                                    "highest_score" => $highest_score,
                                    "lowest_score" => $lowest_score,
                                )
                                );

                                $temp_partd_list = [];
                                $partd_list[$i]['question_series'] = $question_series;
                                $partd_list[$i]['question'] = $question;

                                if (isset($ai_sentkey_score_result) and count($ai_sentkey_score_result) > 0) {
                                    foreach ($ai_sentkey_score_result as $sksr) {

                                        // $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$assessment_id.'" AND question_id="'.$question_id.'" AND sentence_keyword LIKE "%'.$sksr->sentance_keyword.'%" ');
                                        $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="' . $_assessment_id . '" AND question_id="' . $question_id . '"');
                                        $tick_icons = '';
                                        if (isset($sentkey_type_result) and count((array) $sentkey_type_result) > 0) {
                                            $que_lang = $sentkey_type_result->language_id;
                                            // Set different range for Hindi sentence/keyword
                                            $gcolor_score = ($que_lang == 2) ? 50 : 60;
                                            $ycolor_high_score = ($que_lang == 2) ? 50 : 60;
                                            $ycolor_low_score = ($que_lang == 2) ? 40 : 50;
                                            $rcolor_score = ($que_lang == 2) ? 40 : 50;
                                            if ($sentkey_type_result->type_id == 1) { //Sentance 
                                                if ($sksr->score >= $gcolor_score) {
                                                    $tick_icons = 'green';
                                                }
                                                if ($sksr->score <= $rcolor_score) {
                                                    $tick_icons = 'red';
                                                }
                                                if ($sksr->score > $ycolor_low_score and $sksr->score < $ycolor_high_score) {
                                                    $tick_icons = 'yellow';
                                                }
                                            }
                                            if ($sentkey_type_result->type_id == 2) { //Keyword
                                                if ($sksr->score >= $gcolor_score) {
                                                    $tick_icons = 'green';
                                                }
                                                if ($sksr->score < $gcolor_score) {
                                                    $tick_icons = 'red';
                                                }
                                            }
                                        }
                                        array_push($temp_partd_list, array(
                                            "sentance_keyword" => $sksr->sentance_keyword,
                                            "score" => $sksr->score,
                                            "tick_icons" => $tick_icons,
                                        )
                                        );
                                    }
                                    $partd_list[$i]['list'] = $temp_partd_list;
                                }
                                $i++; 
                            }
                            $data['best_video_list'] = $best_video_list;
                            $data['questions_list'] = $questions_list;
                            $data['partd_list'] = $partd_list;

                            //PARAMETER LIST
                            $parameter_score = [];
                            $parameter_score_result = $this->ai_reports_model->get_parameters($company_id, $assessment_id);
                            foreach ($parameter_score_result as $psr) {
                                $parameter_id = $psr->parameter_id;
                                $parameter_label_id = $psr->parameter_label_id;
                                $parameter_your_score_result = $this->ai_reports_model->get_parameters_your_score($company_id, $assessment_id, $user_id, $parameter_id, $parameter_label_id);
                                $parameter_minmax_score_result = $this->ai_reports_model->get_parameter_minmax_score($company_id, $assessment_id, $parameter_id, $parameter_label_id);

                                $your_score = 0;
                                if (isset($parameter_your_score_result) and count((array) $parameter_your_score_result) > 0) {
                                    $your_score = $parameter_your_score_result->score;
                                }
                                $highest_score = 0;
                                $lowest_score = 0;
                                if (isset($parameter_minmax_score_result) and count((array) $parameter_minmax_score_result) > 0) {
                                    $highest_score = $parameter_minmax_score_result->max_score;
                                    $lowest_score = $parameter_minmax_score_result->min_score;
                                }

                                array_push($parameter_score, array(
                                    "parameter_id" => $psr->parameter_id,
                                    "parameter_label_id" => $psr->parameter_label_id,
                                    "parameter_name" => $psr->parameter_name,
                                    "parameter_label_name" => $psr->parameter_label_name,
                                    "your_score" => $your_score,
                                    "highest_score" => $highest_score,
                                    "lowest_score" => $lowest_score,
                                )
                                );
                            }
                            $data['parameter_score'] = $parameter_score;

                            // $this->load->library('Pdf_Library');
                            $htmlContent = $this->load->view('ai_reports/ai_pdf', $data, true);

                            // //DIVEYSH PANCHAL
                            ob_start();
                            define('K_TCPDF_EXTERNAL_CONFIG', true);
                            $this->load->library('Pdf');
                            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $data['pdf'] = $pdf;
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('Awarathon');
                            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
                            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
                            $pdf->SetKeywords('Awarathon');
                            $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
                            $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
									<tr>
                                        <td style="height:10px;width:60%">
                                            <div class="page-title">Sales Readiness Reports</div>
                                        </td>
                                        <td style="height:10px;width:40%;text-align:right;">
                                            <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
                                        </td>
                                    </tr>
								</table>');
                            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
                            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                            $pdf->SetAutoPageBreak(TRUE, 20);
                            //$pdf->SetAutoPageBreak(TRUE, 0);
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            $pdf->PrintCoverPageFooter = True;

                            $pdf->AddPage();
                            $pdf->setJPEGQuality(100);
                            $pdf->SetFont('helvetica', '', 10);
                            $pdf->writeHTML($htmlContent, true, false, true, false, '');
                            $pdf->lastPage();
                            ob_end_clean();

                            $now = date('YmdHis');
                            $file_name = 'C' . $company_id . 'A' . $assessment_id . 'U' . $user_id . 'DTTM' . $now . '.pdf';
                            $file_path = "/var/www/html/awarathon.com/demo/pdf_reports/" . $file_name;
                            if ($tdata->pdf_filename != '') {
                                $old_file_name = '/var/www/html/awarathon.com/demo/pdf_reports/' . $tdata->pdf_filename;
                                unlink($old_file_name);
                            }
                            //UPDATE PDF STATUS
                            $pdf_updtstatus_result = $this->ai_reports_model->update_pdf_status($company_id, $assessment_id, $user_id, $file_name);
                            $pdf->Output($file_path, 'F');
                            $temp_file_path = $site_url . '/pdf_reports/' . $file_name;


                        }
                    }
                } // AI PDF END

                if ($_report_type == 2) { //MANUAL PDF START

                    //CHECK ALL USERS HAS BEEN RATED FROM THE MANAGER
                    $aim_count_result = $this->ai_reports_model->get_user_rated_by_manager($assessment_id);
                    if (isset($aim_count_result) and count((array) $aim_count_result) > 0) {
                        $ai_count = $aim_count_result->ai_count;
                        $manual_count = $aim_count_result->manual_count;
                        if ((float) $ai_count == (float) $manual_count) {

                            //GET COMPANY DETAILS
                            $company_name = '';
                            $company_logo = 'assets/images/Awarathon-Logo.png';
                            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $company_id . '"');
                            if (isset($company_result) and count((array) $company_result) > 0) {
                                $company_name = $company_result->company_name;
                                $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/' . $company_result->company_logo : '';
                            }
                            $data['company_name'] = $company_name;
                            $data['company_logo'] = $company_logo;

                            //GET PARTICIPANT DETAILS
                            $participant_name = '';
                            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="' . $user_id . '"');
                            if (isset($participant_result) and count((array) $participant_result) > 0) {
                                $participant_name = $participant_result->firstname . " " . $participant_result->lastname . " - " . $user_id;
                            }
                            $data['participant_name'] = $participant_name;

                            //GET MANAGER NAME
                            $manager_id = '';
                            $manager_name = '';
                            $manager_result = $this->ai_reports_model->get_manager_name($assessment_id, $user_id);
                            if (isset($manager_result) and count((array) $manager_result) > 0) {
                                $manager_id = $manager_result->manager_id;
                                $manager_name = $manager_result->manager_name;
                            }
                            $data['manager_name'] = $manager_name;


                            //OVERALL SCORE
                            $overall_score = 0;
                            $your_rank = 0;
                            $overall_score_result = $this->ai_reports_model->get_manual_overall_score_rank($company_id, $assessment_id, $user_id);
                            if (isset($overall_score_result) and count((array) $overall_score_result) > 0) {
                                $overall_score = $overall_score_result->overall_score;
                                $your_rank = $overall_score_result->final_rank;
                            }
                            $data['overall_score'] = $overall_score;
                            $data['your_rank'] = $your_rank;

                            $rating = '';
                            if ((float) $overall_score >= 69.9) {
                                $rating = 'A';
                            } else if ((float) $overall_score < 69.9 and (float) $overall_score >= 63.23) {
                                $rating = 'B';
                            } else if ((float) $overall_score < 63.23 and (float) $overall_score >= 54.9) {
                                $rating = 'C';
                            } else if ((float) $overall_score < 54.9) {
                                $rating = 'D';
                            }
                            $data['rating'] = $rating;


                            //QUESTIONS LIST
                            $best_video_list = [];
                            $questions_list = [];
                            $partd_list = [];
                            $manager_comments_list = [];
                            $i = 0;
                            $question_result = $this->ai_reports_model->get_questions($company_id, $assessment_id);
                            foreach ($question_result as $qr) {
                                $question_id = $qr->question_id;
                                $question = $qr->question;
                                $question_series = $qr->question_series;
                                $_trans_id = $qr->trans_id;

                                $question_your_score_result = $this->ai_reports_model->get_manual_question_your_score($company_id, $assessment_id, $user_id, $question_id);
                                $question_minmax_score_result = $this->ai_reports_model->get_manual_question_minmax_score($company_id, $assessment_id, $question_id);
                                $question_your_video_result = $this->ai_reports_model->get_your_video($company_id, $assessment_id, $user_id, $_trans_id, $question_id);
                                $question_best_video_result = $this->ai_reports_model->get_manual_best_video($company_id, $assessment_id, $question_id);
                                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($assessment_id, $user_id, $question_id, $manager_id);

                                $your_vimeo_url = "";
                                if (isset($question_your_video_result) and count((array) $question_your_video_result) > 0) {
                                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                                }

                                $best_vimeo_url = "";
                                if (isset($question_best_video_result) and count((array) $question_best_video_result) > 0) {
                                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                                }

                                $your_score = 0;
                                if (isset($question_your_score_result) and count((array) $question_your_score_result) > 0) {
                                    $your_score = $question_your_score_result->score;
                                }
                                $highest_score = 0;
                                $lowest_score = 0;
                                if (isset($question_minmax_score_result) and count((array) $question_minmax_score_result) > 0) {
                                    $highest_score = $question_minmax_score_result->max_score;
                                    $lowest_score = $question_minmax_score_result->min_score;
                                }
                                $comments = '';
                                if (isset($question_manager_comment_result) and count((array) $question_manager_comment_result) > 0) {
                                    $comments = $question_manager_comment_result->remarks;
                                }

                                array_push($best_video_list, array(
                                    "question_series" => $question_series,
                                    "your_vimeo_url" => $your_vimeo_url,
                                    "best_vimeo_url" => $best_vimeo_url,
                                )
                                );
                                array_push($questions_list, array(
                                    "question_id" => $question_id,
                                    "question" => $question,
                                    "question_series" => $question_series,
                                    "your_score" => $your_score,
                                    "highest_score" => $highest_score,
                                    "lowest_score" => $lowest_score,
                                )
                                );
                                array_push($manager_comments_list, array(
                                    "question_id" => $question_id,
                                    "question" => $question,
                                    "question_series" => $question_series,
                                    "comments" => $comments,
                                )
                                );

                                $temp_partd_list = [];
                                $partd_list[$i]['question_series'] = $question_series;
                                $partd_list[$i]['question'] = $question;
                                $i++;
                            }
                            $data['best_video_list'] = $best_video_list;
                            $data['questions_list'] = $questions_list;
                            $data['manager_comments_list'] = $manager_comments_list;

                            //GET OVERALL COMMENTS
                            $overall_comments = '';
                            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="' . $assessment_id . '" and user_id="' . $user_id . '" and trainer_id="' . $manager_id . '"');
                            if (isset($overall_comments_result) and count((array) $overall_comments_result) > 0) {
                                $overall_comments = $overall_comments_result->company_name;
                            }
                            $data['overall_comments'] = $overall_comments;

                            //PARAMETER LIST
                            $parameter_score = [];
                            $parameter_score_result = $this->ai_reports_model->get_parameters($company_id, $assessment_id);
                            foreach ($parameter_score_result as $psr) {
                                $parameter_id = $psr->parameter_id;
                                $parameter_label_id = $psr->parameter_label_id;
                                $parameter_your_score_result = $this->ai_reports_model->get_manual_parameters_your_score($company_id, $assessment_id, $user_id, $parameter_id, $parameter_label_id);
                                $parameter_minmax_score_result = $this->ai_reports_model->get_manual_parameter_minmax_score($user_id, $assessment_id, $parameter_id, $parameter_label_id);

                                $your_score = 0;
                                if (isset($parameter_your_score_result) and count((array) $parameter_your_score_result) > 0) {
                                    $your_score = $parameter_your_score_result->percentage;
                                }
                                $highest_score = 0;
                                $lowest_score = 0;
                                if (isset($parameter_minmax_score_result) and count((array) $parameter_minmax_score_result) > 0) {
                                    $highest_score = $parameter_minmax_score_result->max_score;
                                    $lowest_score = $parameter_minmax_score_result->min_score;
                                }

                                array_push($parameter_score, array(
                                    "parameter_id" => $psr->parameter_id,
                                    "parameter_label_id" => $psr->parameter_label_id,
                                    "parameter_name" => $psr->parameter_name,
                                    "parameter_label_name" => $psr->parameter_label_name,
                                    "your_score" => $your_score,
                                    "highest_score" => $highest_score,
                                    "lowest_score" => $lowest_score,
                                )
                                );
                            }
                            $data['parameter_score'] = $parameter_score;


                            // $this->load->library('Pdf_Library');
                            $htmlContent = $this->load->view('ai_reports/manual_pdf', $data, true);

                            // //DIVEYSH PANCHAL
                            ob_start();
                            define('K_TCPDF_EXTERNAL_CONFIG', true);
                            $this->load->library('Pdf');
                            //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            //Below line is added
                            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $data['pdf'] = $pdf;
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('Awarathon');
                            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
                            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
                            $pdf->SetKeywords('Awarathon');
                            $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
                            $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
									<tr>
                                        <td style="height:10px;width:60%">
                                            <div class="page-title">Sales Readiness Reports</div>
                                        </td>
                                        <td style="height:10px;width:40%;text-align:right;">
                                            <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
                                        </td>
                                    </tr>
								</table>');
                            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
                            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                            //$pdf->SetAutoPageBreak(TRUE, 0);
                            $pdf->SetAutoPageBreak(TRUE, 20);
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            //Added below line: As we don't want footer on front page
                            $pdf->PrintCoverPageFooter = True;

                            $pdf->AddPage();
                            $pdf->setJPEGQuality(100);
                            $pdf->SetFont('helvetica', '', 10);

                            $pdf->writeHTML($htmlContent, true, false, true, false, '');
                            $pdf->lastPage();
                            ob_end_clean();

                            $now = date('YmdHis');
                            $file_name = 'MANU-C' . $company_id . 'A' . $assessment_id . 'U' . $user_id . 'DTTM' . $now . '.pdf';
                            $file_path = "/var/www/html/awarathon.com/demo/pdf_reports/" . $file_name;

                            $pdf->Output($file_path, 'F');
                            $temp_file_path = $site_url . '/pdf_reports/' . $file_name;

                            //UPDATE PDF STATUS
                            $pdf_updtstatus_result = $this->ai_reports_model->update_manual_pdf_status($company_id, $assessment_id, $user_id, $file_name);
                        }
                    }
                } // MANUAL PDF ENDS

                if ($_report_type == 3) { //COMBINE (AI + MANUAL) PDF START
                    //CHECK ALL USERS HAS BEEN RATED FROM THE MANAGER
                    $aim_count_result = $this->ai_reports_model->get_user_rated_by_manager($assessment_id);
                    if (isset($aim_count_result) and count((array) $aim_count_result) > 0) {
                        $ai_count = $aim_count_result->ai_count;
                        $manual_count = $aim_count_result->manual_count;
                        if ((float) $ai_count == (float) $manual_count) {

                            //GET COMPANY DETAILS
                            $company_name = '';
                            $company_logo = 'assets/images/Awarathon-Logo.png';
                            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $company_id . '"');
                            if (isset($company_result) and count((array) $company_result) > 0) {
                                $company_name = $company_result->company_name;
                                $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/' . $company_result->company_logo : '';
                            }
                            $data['company_name'] = $company_name;
                            $data['company_logo'] = $company_logo;

                            //GET PARTICIPANT DETAILS
                            $participant_name = '';
                            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="' . $user_id . '"');
                            if (isset($participant_result) and count((array) $participant_result) > 0) {
                                $participant_name = $participant_result->firstname . " " . $participant_result->lastname . " - " . $user_id;
                            }
                            $data['participant_name'] = $participant_name;

                            //GET MANAGER NAME
                            $manager_id = '';
                            $manager_name = '';
                            $manager_result = $this->ai_reports_model->get_manager_name($assessment_id, $user_id);
                            if (isset($manager_result) and count((array) $manager_result) > 0) {
                                $manager_id = $manager_result->manager_id;
                                $manager_name = $manager_result->manager_name;
                            }
                            $data['manager_name'] = $manager_name;

                            //OVERALL SCORE
                            $overall_score = 0;
                            $overall_score_result = $this->ai_reports_model->get_user_overall_score_combined($company_id, $assessment_id, $user_id);
                            if (isset($overall_score_result) and count((array) $overall_score_result) > 0) {
                                $overall_score = $overall_score_result->overall_score;
                            }
                            $data['overall_score'] = $overall_score;


                            //QUESTIONS LIST
                            $questions_list = [];
                            $manager_comments_list = [];
                            $question_result = $this->ai_reports_model->get_questions($company_id, $assessment_id);
                            foreach ($question_result as $qr) {
                                $question_id = $qr->question_id;
                                $question = $qr->question;
                                $question_series = $qr->question_series;

                                $question_ai_score_result = $this->ai_reports_model->get_question_your_score($company_id, $assessment_id, $user_id, $question_id);
                                $question_manual_score_result = $this->ai_reports_model->get_question_manual_score($assessment_id, $user_id, $question_id);
                                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($assessment_id, $user_id, $question_id, $manager_id);

                                $ai_score = 0;
                                if (isset($question_ai_score_result) and count((array) $question_ai_score_result) > 0) {
                                    $ai_score = $question_ai_score_result->score;
                                }
                                $manual_score = 0;
                                if (isset($question_manual_score_result) and count((array) $question_manual_score_result) > 0) {
                                    $manual_score = $question_manual_score_result->score;
                                }
                                $comments = '';
                                if (isset($question_manager_comment_result) and count((array) $question_manager_comment_result) > 0) {
                                    $comments = $question_manager_comment_result->remarks;
                                }
                                if ($manual_score == 0 || $ai_score == 0) {
                                    $combined_score = number_format((($ai_score + $manual_score)), 2);
                                } else {
                                    $combined_score = number_format((($ai_score + $manual_score) / 2), 2);
                                }
                                array_push($questions_list, array(
                                    "question_id" => $question_id,
                                    "question" => $question,
                                    "question_series" => $question_series,
                                    "ai_score" => $ai_score,
                                    "manual_score" => $manual_score,
                                    "combined_score" => $combined_score,
                                )
                                );

                                array_push($manager_comments_list, array(
                                    "question_id" => $question_id,
                                    "question" => $question,
                                    "question_series" => $question_series,
                                    "comments" => $comments,
                                )
                                );

                            }
                            $data['questions_list'] = $questions_list;
                            $data['manager_comments_list'] = $manager_comments_list;


                            //GET OVERALL COMMENTS
                            $overall_comments = '';
                            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="' . $assessment_id . '" and user_id="' . $user_id . '" and trainer_id="' . $manager_id . '"');
                            if (isset($overall_comments_result) and count((array) $overall_comments_result) > 0) {
                                $overall_comments = $overall_comments_result->company_name;
                            }
                            $data['overall_comments'] = $overall_comments;

                            //PARAMETER LIST
                            $parameter_score = [];
                            $parameter_score_result = $this->ai_reports_model->get_parameters($company_id, $assessment_id);
                            foreach ($parameter_score_result as $psr) {
                                $parameter_id = $psr->parameter_id;
                                $parameter_label_id = $psr->parameter_label_id;
                                $parameter_your_score_result = $this->ai_reports_model->get_parameters_your_score($company_id, $assessment_id, $user_id, $parameter_id, $parameter_label_id);
                                $parameter_manual_score_result = $this->ai_reports_model->get_parameter_manual_score($assessment_id, $user_id, $parameter_id, $parameter_label_id);

                                $your_score = 0;
                                if (isset($parameter_your_score_result) and count((array) $parameter_your_score_result) > 0) {
                                    $your_score = $parameter_your_score_result->score;
                                }
                                $manual_score = 0;
                                if (isset($parameter_manual_score_result) and count((array) $parameter_manual_score_result) > 0) {
                                    $manual_score = $parameter_manual_score_result->percentage;
                                }
                                if ($manual_score == 0 || $your_score == 0) {
                                    $combined_score = number_format((($your_score + $manual_score)), 2);
                                } else {
                                    $combined_score = number_format((($your_score + $manual_score) / 2), 2);
                                }
                                array_push($parameter_score, array(
                                    "parameter_id" => $psr->parameter_id,
                                    "parameter_label_id" => $psr->parameter_label_id,
                                    "parameter_name" => $psr->parameter_name,
                                    "parameter_label_name" => $psr->parameter_label_name,
                                    "your_score" => $your_score,
                                    "manual_score" => $manual_score,
                                    "combined_score" => $combined_score,
                                )
                                );
                            }
                            $data['parameter_score'] = $parameter_score;

                            // $this->load->library('Pdf_Library');
                            $htmlContent = $this->load->view('ai_reports/combined_pdf', $data, true);

                            // //DIVEYSH PANCHAL
                            ob_start();
                            define('K_TCPDF_EXTERNAL_CONFIG', true);
                            $this->load->library('Pdf');
                            //  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $data['pdf'] = $pdf;
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('Awarathon');
                            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
                            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
                            $pdf->SetKeywords('Awarathon');
                            $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
                            $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                                    <tr>
                                        <td style="height:10px;width:60%">
                                            <div class="page-title">Sales Readiness Reports</div>
                                        </td>
                                        <td style="height:10px;width:40%;text-align:right;">
                                            <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
                                        </td>
                                    </tr>               
									</table>');
                            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
                            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                            //$pdf->SetAutoPageBreak(TRUE, 0);
                            $pdf->SetAutoPageBreak(TRUE, 20);
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            $pdf->PrintCoverPageFooter = True;

                            $pdf->AddPage();
                            $pdf->setJPEGQuality(100);
                            $pdf->SetFont('helvetica', '', 10);
                            $pdf->writeHTML($htmlContent, true, false, true, false, '');
                            $pdf->lastPage();
                            ob_end_clean();

                            $now = date('YmdHis');
                            $file_name = 'COMB-C' . $company_id . 'A' . $assessment_id . 'U' . $user_id . 'DTTM' . $now . '.pdf';
                            $file_path = "/var/www/html/awarathon.com/demo/pdf_reports/" . $file_name;

                            $pdf->Output($file_path, 'F');
                            $temp_file_path = $site_url . '/pdf_reports/' . $file_name;

                            //UPDATE PDF STATUS
                            $pdf_updtstatus_result = $this->ai_reports_model->update_combined_pdf_status($company_id, $assessment_id, $user_id, $file_name);


                        }
                    }
                } //COMBINE (AI + MANUAL) PDF ENDS


            }
        }
    }

    //Report Status functions -------------------------------------------------------------------------------------------------------------------
    function generate_header()
    {
        $company_id = $this->mw_session['company_id'];
        $assessment_id = $this->input->post('assessment_id');
        $parameter_score_result = $this->ai_reports_model->get_parameters_reports($company_id, $assessment_id);
        $params = [];
        foreach ($parameter_score_result as $psr) {
            $parameter_id = $psr->parameter_id;
            $parameter_name = $psr->parameter_name;
            $params[] = $parameter_name;
        }
        $data['parameter_score_result'] = $params;
        $table_headers = $this->load->view('ai_reports/skill_report', $data);
        $data['thead'] = $table_headers;
    }
    public function report_assessment_trainee()
    {
        $assessment_html = '';
        $report_type_catg = $this->input->post('report_type_catg', true);
        $assessment_list = $this->ai_reports_model->get_assessment_types($report_type_catg);
        $assessment_html .= '<option value="">Please Select</option>';
        if (count((array) $assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->assessment_id . '">' . $value->assessment . ' - [' . $value->status . ']</option>';
            }
        }
        $data['assessment_list_data_trainee'] = $assessment_html;
        echo json_encode($data);
    }
    public function report_wise_assessment()
    {
        $assessment_html = '';
        $report_type_catg = $this->input->post('report_type', true);
        $assessment_list = $this->ai_reports_model->get_assessment_types($report_type_catg);
        $assessment_html .= '<option value="">Please Select</option>';
        if (count((array) $assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->assessment_id . '">' . $value->assessment . ' - [' . $value->status . ']</option>';
            }
        }
        $data['assessment_list_data'] = $assessment_html;
        echo json_encode($data);
    }
    public function ajax_assessmentwise_data()
    {
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessment_id', TRUE) ? $this->input->post('assessment_id', TRUE) : 0);
        $assessment_list = $this->ai_reports_model->get_participants_final_report($assessment_id);
        $assessment_html .= '<option value="">';
        if (count((array) $assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->user_id . '">[' . $value->user_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['assessment_list_data'] = $assessment_html;
        echo json_encode($data);
    }
    public function exportReport_trainee()
    {
        $Company_name = "";
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $company_id = $this->mw_session['company_id'];
            $status_id = $this->input->post('status_id_trainee', true);
            $dtWhere = ' WHERE 1=1 ';
            if ($status_id == 0) {
                $dtWhere .= ' AND b.is_completed= 1 ';
            } elseif ($status_id != 2) {
                $dtWhere .= ' AND (b.is_completed= 0 OR b.is_completed IS NULL) ';
            }
            $assessment_id1 = implode(',', $this->input->post('assessment_id_trainee', true));
            $dtWhere .= " AND a.assessment_id IN (" . $assessment_id1 . ")";
            $DTRenderArray = $this->ai_reports_model->trainee_report_data($dtWhere, '', '');
            $user_list = [];
            $x = 0;
            $user_details = $DTRenderArray['ResultSet'];
            foreach ($user_details as $ud) {
                $user_list[$x]['E Code'] = $ud['emp_id'];
                $user_list[$x]['Employee name'] = $ud['user_name'];
                $user_list[$x]['Date of Join'] = $ud['joining_date'];
                $user_id = $ud['user_id'];
                $user_list[$x]['Email'] = $ud['email'];
                $user_list[$x]['Assessment Name'] = $ud['assessment_name'];
                $user_list[$x]['Status'] = $ud['user_status'];
                $x++;
            }
            $Data_list = $user_list;
            $this->load->library('PHPExcel');
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);
            $i = 1;
            $j = 1;
            $dtDisplayColumns = array_keys($user_list[0]);
            foreach ($dtDisplayColumns as $column) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $column);
                $j++;
            }
            $j = 2;
            foreach ($Data_list as $value) {
                $i = 1;
                foreach ($dtDisplayColumns as $column) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                    $i++;
                }
                $j++;
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . "Trainee.xls");
            header('Cache-Control: max-age=0');
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
            // ob_end_clean();
            $objWriter->save('php://output');
        } else {
            redirect('ai_reports');
        }
    }
    function generate_report_trainee()
    {
        $company_id = $this->mw_session['company_id'];
        $status_id = $this->input->get('status_id_trainee', true);
        $dtSearchColumns = array('emp_id', 'user_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $dtWhere = ' WHERE 1=1 ';
        if ($status_id == 0) {
            $dtWhere .= ' AND b.is_completed= 1 ';
        } elseif ($status_id != 2) {
            $dtWhere .= ' AND (b.is_completed=0 OR b.is_completed IS NULL) ';
        }
        if ($dtOrder == '') {
            $dtOrder = ' ORDER BY assessment_id,user_status,user_name ';
        }
        $assessment_id1 = $this->input->get('assessment_id_trainee', true);
        $dtWhere .= " AND a.assessment_id IN (" . $assessment_id1 . ")";
        $DTRenderArray = $this->ai_reports_model->trainee_report_data($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            //"iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalRecords" => count((array) $DTRenderArray),
            "iTotalDisplayRecords" => 10,
            "aaData" => array()
        );
        $dtDisplayColumns = ['user_id', 'user_name', 'joining_date', 'email', 'assessment_name', 'user_status'];
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function exportReport_manager()
    { //In use for Export
        $Company_name = "";
        $Company_id = $this->mw_session['company_id'];
        //$Company_id=67;
        if ($Company_id != "") {

            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $company_id = $this->mw_session['company_id'];
            //$company_id=67;
            $manager_id = $this->input->post('manager_id', true);
            $status_id = $this->input->post('status_id_manager', true);
            $assessment_id1 = $this->input->post('assessment_id_manager', true);

            $dtWhere = '';
            if ($status_id == 0) {
                $dtWhere .= ' AND cr.id is not null ';
            } elseif ($status_id != 2) {
                $dtWhere .= ' AND (cr.id IS NULL) ';
            }

            if ($manager_id != "") {
                $dtWhere .= ' AND amu.trainer_id=' . $manager_id;
            }
            $user_list = [];
            $x = 0;
            //print_r($dtWhere);
            $assessment_id1 = $this->input->post('assessment_id_manager', true);
            foreach ($assessment_id1 as $ads) {
                $manager_count = $this->common_model->get_value('assessment_mapping_user', 'user_id', 'assessment_id=' . $ads);
                $ismapped = 0;
                if (count((array) $manager_count) > 0) {
                    $ismapped = 1;
                }

                $dtWhere1 = '';
                $dtWhere1 .= " AND a.assessment_id = " . $ads;

                $user_details = $this->ai_reports_model->status_check_manager($dtWhere, $ismapped, $dtWhere1);


                foreach ($user_details as $ud) {
                    $assessment_name = $ud->assessment;
                    $user_list[$x]['Emp id'] = $ud->emp_id;
                    $user_list[$x]['Emp Name'] = $ud->user_name;
                    $user_list[$x]['Email'] = $ud->email;
                    $user_list[$x]['Candidate Status'] = $ud->status1;
                    $user_list[$x]['Manager name'] = $ud->trainer_name;
                    $user_list[$x]['Manager status'] = $ud->trainer_status;
                    $user_id = $ud->user_id;
                    $assessment_id = $ud->assessment_id;

                    $user_list[$x]['assessment_name'] = $ud->assessment;

                    $x++;

                }
            }

            $Data_list = $user_list;

            $this->load->library('PHPExcel');
            //$objPHPExcel = new PHPExcel();
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);
            //$Excel->getActiveSheet()->setCellValueByColumnAndRow(1,1, $report_title);
            $i = 1;
            $j = 1;
            $dtDisplayColumns = array_keys($user_list[0]);


            foreach ($dtDisplayColumns as $column) {

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $column);
                $j++;
            }
            $j = 2;
            foreach ($Data_list as $value) {

                $i = 1;
                foreach ($dtDisplayColumns as $column) {

                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                    $i++;
                }

                $j++;
            }

            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            //if($assessment_id1!='')
            // {

            header('Content-Disposition: attachment;filename=' . "Manager.xls");
            //}
            //else
            // {
            //   header('Content-Disposition: attachment;filename="Report.xls"');
            //}
            header('Cache-Control: max-age=0');
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file

        } else {

            redirect('ai_reports');

        }
    }
    public function ajax_assessmentwise_data_manager()
    {
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessment_id_manager', TRUE) ? $this->input->post('assessment_id_manager', TRUE) : 0);
        $assessment_list = $this->ai_reports_model->get_distinct_manager($assessment_id);
        $assessment_html .= '<option value="">';
        if (count((array) $assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->trainer_id . '">' . $value->fullname . '</option>';
            }
        }
        $data['assessment_list_data'] = $assessment_html;
        echo json_encode($data);
    }
    function generate_report_manager()
    {
        $company_id = $this->mw_session['company_id'];
        $manager_id = $this->input->get('manager_id', true);
        $status_id = $this->input->get('status_id_manager', true);
        $dtWhere = '';
        $assessment_id1 = $this->input->get('assessment_id_manager', true);
        $assessment_id1 = explode(',', $assessment_id1);
        if ($status_id == 0) {
            $dtWhere .= ' AND cr.id is not null ';
        } elseif ($status_id != 2) {
            $dtWhere .= ' AND (cr.id IS NULL) ';
        }
        if ($manager_id != "") {
            $dtWhere .= ' AND amu.trainer_id=' . $manager_id;
        }
        $user_list = [];
        $x = 0;
        foreach ($assessment_id1 as $ads) {
            $manager_count = $this->common_model->get_value('assessment_mapping_user', 'user_id', 'assessment_id=' . $ads);
            $ismapped = 0;
            if (count((array) $manager_count) > 0) {
                $ismapped = 1;
            }
            $dtWhere1 = '';
            $dtWhere1 .= " AND a.assessment_id = " . $ads;
            $user_details = $this->ai_reports_model->status_check_manager($dtWhere, $ismapped, $dtWhere1);
            foreach ($user_details as $ud) {
                $assessment_name = $ud->assessment;
                $user_list[$x]['user_id'] = $ud->emp_id;
                $user_list[$x]['user_name'] = $ud->user_name;
                $user_list[$x]['email'] = $ud->email;
                $user_list[$x]['trainee_status'] = $ud->status1;
                $user_list[$x]['trainer_name'] = $ud->trainer_name;
                $user_list[$x]['trainer_status'] = $ud->trainer_status;
                $user_id = $ud->user_id;
                $assessment_id = $ud->assessment_id;
                $user_list[$x]['assessment_name'] = $ud->assessment;
                $x++;
            }
        }
        $dtSearchColumns = array('emp_id', 'user_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $DTRenderArray = $user_list;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            //"iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalRecords" => count((array) $user_list),
            "iTotalDisplayRecords" => 10,
            "aaData" => array()
        );
        $dtDisplayColumns = ['user_id', 'user_name', 'email', 'trainee_status', 'trainer_name', 'trainer_status', 'assessment_name'];
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ' and isset($dtDisplayColumns)) {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    function generate_report()
    {
        // $dtSearchColumns = array('c.emp_id','CONCAT(c.firstname, " ",c.lastname)','joining_date','c.department','','','','','c.email','','');
        $dtSearchColumns = array('c.emp_id', 'CONCAT(c.firstname, " ",c.lastname)', 'c.department', '', '', '', 'c.email', '', '');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $company_id = $this->mw_session['company_id'];
        $user_id = $this->input->get('user_id', true);
        $status_id = $this->input->get('status_id', true);
        if ($dtWhere == "") {
            $dtWhere .= " WHERE 1=1 ";
        }
        if ($status_id == 0) {
            $dtWhere .= ' AND b.is_completed= 1 ';
        } elseif ($status_id != 2) {
            $dtWhere .= ' AND (b.is_completed= 0 OR b.is_completed IS NULL) ';
        }
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND c.user_id  = " . $user_id;
            } else {
                $dtWhere .= " AND c.user_id = " . $user_id;
            }
        }
        $assessment_id1 = $this->input->get('assessment_id', true);
        if ($assessment_id1 != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.assessment_id  = " . $assessment_id1;
            } else {
                $dtWhere .= " AND a.assessment_id = " . $assessment_id1;
            }
        }
        $dtWhere1 = '';
        $total_users = $this->ai_reports_model->status_check($company_id, $status_id, $dtWhere, $dtWhere1);
        $user_details = $this->ai_reports_model->status_check($company_id, $status_id, $dtWhere, $dtWhere1, $dtLimit);

        $manager_details = $this->ai_reports_model->assessment_manager_details($assessment_id1);
        $ass_managers = [];
        if (!empty($manager_details)) {
            foreach ($manager_details as $manager) {
                $ass_managers[$manager->user_id] = [
                    'trainer_no' => $manager->trainer_id,
                    'trainer_name' => $manager->trainer_name
                ];
            }
        }

        $ass_ai_score = $this->ai_reports_model->assessment_get_ai_score($company_id, $assessment_id1);
        $user_ai_score = [];
        if (!empty($ass_ai_score)) {
            foreach ($ass_ai_score as $ai_score) {
                $user_ai_score[$ai_score->user_id] = $ai_score->overall_score;
            }
        }

        $ass_parameter_scores = $this->ai_reports_model->assessment_get_parameter_sub_parameter_score($company_id, $assessment_id1);
        $user_parameter_score = [];
        $params = [];
        if (!empty($ass_parameter_scores)) {
            foreach ($ass_parameter_scores as $parameter_score) {
                $user_parameter_score[$parameter_score->user_id][$parameter_score->parameter_label_id] = [
                    'parameter_name' => $parameter_score->parameter_name,
                    'parameter_id' => $parameter_score->parameter_id,
                    'parameter_score' => $parameter_score->score,
                ];
                $params[$parameter_score->parameter_label_id] = $parameter_score->parameter_name;
            }
        }

        $user_list = [];
        $x = 0;
        foreach ($user_details as $ud) {
            $assessment_name = $ud->assessment;
            $user_list[$x]['user_id'] = $ud->emp_id;
            $user_list[$x]['user_name'] = $ud->user_name;
            // $user_list[$x]['joining_date']= $ud->joining_date;
            $user_list[$x]['division'] = $ud->department;
            // $user_list[$x]['pc_hq']=  $ud->hq;
            // $user_list[$x]['state']= "";
            $user_list[$x]['zone'] = $ud->area;
            $user_list[$x]['region'] = $ud->region_name;
            $user_list[$x]['ec'] = "";
            $user_id = $ud->user_id;
            $assessment_id = $ud->assessment_id;
            $user_list[$x]['email'] = $ud->email;
            $user_list[$x]['designation'] = $ud->designation;
            $user_list[$x]['assessment_name'] = $ud->assessment;
            $user_list[$x]['status'] = $ud->status_u;
            // $manager_count=$this->common_model->get_value('assessment_mapping_user','user_id','assessment_id=' .$assessment_id1);
            // $ismapped=0;
            // if(count((array)$manager_count) >0){
            // 	$ismapped=1;
            // }
            // $manager_dt=$this->ai_reports_model->manager_details($assessment_id, $user_id, $ismapped);
            // $user_list[$x]['ec']= !empty($manager_dt->trainer_no) ? $manager_dt->trainer_no : '-';
            // $user_list[$x]['ec_name']= !empty($manager_dt->trainer_name) ? $manager_dt->trainer_name : '-';
            $user_list[$x]['ec'] = !empty($ass_managers) && isset($ass_managers[$user_id]) ? $ass_managers[$user_id]['trainer_no'] : '-';
            $user_list[$x]['ec_name'] = !empty($ass_managers) && isset($ass_managers[$user_id]) ? $ass_managers[$user_id]['trainer_name'] : '-';

            // $overall_score = 0;
            // $ai_score= $this->ai_reports_model->get_ai_score($company_id, $assessment_id, $user_id);
            // $user_list[$x]['ai_overall_score']= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
            // $ai_score1= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
            // $parameter_score_result = $this->ai_reports_model->get_parameter_sub_parameter_score($company_id,$assessment_id,$user_id);
            // $params = [];
            // foreach ($parameter_score_result as $psr){
            // 	$parameter_id= $psr->parameter_id;
            // 	$parameter_name =$psr->parameter_name;
            // 	$params[] = $parameter_name;
            // 	$parameter_label_id = $psr->parameter_label_id;
            // 	$user_list[$x][$parameter_name]= !empty($psr->score) ? $psr->score: '-';
            // 	$user_score[]= !empty($psr->score) ? $psr->score: '-';
            // }
            $overall_score = 0;
            $user_list[$x]['ai_overall_score'] = !empty($user_ai_score) && isset($user_ai_score[$user_id]) ? $user_ai_score[$user_id] : '-';
            $ai_score1 = !empty($user_ai_score) && isset($user_ai_score[$user_id]) ? $user_ai_score[$user_id] : '-';
            if (!empty($user_parameter_score) && isset($user_parameter_score[$user_id])) {
                foreach ($user_parameter_score[$user_id] as $up_score) {
                    $user_list[$x][$up_score['parameter_name']] = $up_score['parameter_score'];
                }
            }

            $manual_overall_score = 0;
            $manual_score = $this->ai_reports_model->get_manual_score($assessment_id, $user_id);
            if (isset($manual_score) and count((array) $manual_score) > 0) {
                $manual_overall_score = $manual_score->overall_score;
            }
            if ($manual_overall_score == "0") {
                $user_list[$x]['manual_overall_score'] = '-';
            } else {
                $user_list[$x]['manual_overall_score'] = $manual_overall_score;
            }
            if ($manual_overall_score == 0) {
                if ($ai_score1 == 0) {
                    $total = '-';
                    $diff = '-';
                } else {
                    $total = $ai_score1;
                    $diff = round(($ai_score1 - $manual_overall_score), 2);
                }
            } else if ($ai_score1 == 0) {
                if ($manual_overall_score == 0) {
                    $total = '-';
                    $diff = '-';
                } else {
                    $total = $manual_overall_score;
                    $diff = ($ai_score1 == '-') ? -$manual_overall_score : round(($ai_score1 - $manual_overall_score), 2);
                    // $diff=round(($ai_score1-$manual_overall_score),2);
                }
            } else {
                $total = round(($ai_score1 + $manual_overall_score) / 2, 2);
                $diff = round(($ai_score1 - $manual_overall_score), 2);
            }
            $user_list[$x]['aiandmanual'] = $total;
            // $user_list[$x]['differnce']= $diff;
            $user_list[$x]['total_attempts'] = $ud->attempts . '/' . $ud->total_attempts;
            $rating = '';
            // if ((float) $ai_score1 >= 75) {
            //     $rating = 'Above 75%';
            // } else if ((float) $ai_score1 < 75 and (float) $ai_score1 >= 60) {
            //     $rating = '60 to 74%';
            // } else if ((float) $ai_score1 < 60 and (float) $ai_score1 >= 40) {
            //     $rating = '40 to 59%';
            // } else if ((float) $ai_score1 < 40 and (float) $ai_score1 > 0) {
            //     $rating = 'Less then 40';
            // } else {
            //     $rating = '-';
            // }

            // Industry thresholds
            $this->db->select('*')->from('industry_threshold_range');
            $range = $this->db->get()->result();
            foreach ($range as $rg) {
                $range_from[] = $rg->range_from;
                $range_to[] = $rg->range_to;
            }
            if ((float) $ai_score1 >= $range_from['0'] and (float) $ai_score1 <= $range_to['0']) {
                $rating = 'Above 85%';
            } else if ((float) $ai_score1 > $range_from['1'] and (float) $ai_score1 <= $range_to['1'] . '99') {
                $rating = '75%  to 84%';
            } else if ((float) $ai_score1 > $range_from['2'] and (float) $ai_score1 <= $range_to['2'] . '99') {
                $rating = '65 to 74%';
            } else if ((float) $ai_score1 > $range_from['3'] and (float) $ai_score1 <= $range_to['3'] . '99') {
                $rating = '55% to 64%';
            } else if ((float) $ai_score1 > $range_from['4'] and (float) $ai_score1 <= $range_to['4'] . '99') {
                $rating = '26% to 54%';
            } else if ((float) $ai_score1 >= $range_from['5'] and (float) $ai_score1 <= $range_to['5'] . '99') {
                $rating = 'Below 25%';
            } else {
                $rating = '-';
            }
            $user_list[$x]['ai_rating'] = $rating;
            $manual_rating = '';
            // if ((float) $manual_overall_score >= 75) {
            //     $manual_rating = 'Above 75%';
            // } else if ((float) $manual_overall_score < 75 and (float) $manual_overall_score >= 60) {
            //     $manual_rating = '60 to 74%';
            // } else if ((float) $manual_overall_score < 60 and (float) $manual_overall_score >= 40) {
            //     $manual_rating = '40 to 59%';
            // } else if ((float) $manual_overall_score < 40 and (float) $manual_overall_score > 0) {
            //     $manual_rating = 'Less then 40';
            // } else {
            //     $manual_rating = '-';
            // }
            if ((float) $manual_overall_score >= $range_from['0'] and (float) $manual_overall_score <= $range_to['0']) {
                $manual_rating = 'Above 85%';
            } else if ((float) $manual_overall_score > $range_from['1'] and (float) $manual_overall_score <= $range_to['1'] . '99') {
                $manual_rating = '75%  to 84%';
            } else if ((float) $manual_overall_score > $range_from['2'] and (float) $manual_overall_score <= $range_to['2'] . '99') {
                $manual_rating = '65 to 74%';
            } else if ((float) $manual_overall_score > $range_from['3'] and (float) $manual_overall_score <= $range_to['3'] . '99') {
                $manual_rating = '55% to 64%';
            } else if ((float) $manual_overall_score > $range_from['4'] and (float) $manual_overall_score <= $range_to['4'] . '99') {
                $manual_rating = '26% to 54%';
            } else if ((float) $manual_overall_score >= $range_from['5'] and (float) $manual_overall_score <= $range_to['5'] . '99') {
                $manual_rating = 'Below 25%';
            } else {
                $manual_rating = '-';
            }
            $user_list[$x]['manual_rating'] = $manual_rating;
            // $reg_date=date_create($ud->joining_date);
            // $today = date_create(date('d-m-Y'));
            // $interval = date_diff($reg_date, $today)->format('%R%a days');
            // if($interval<182.5){
            // 	$join_interval="06 months";
            // }elseif($interval>182.5 AND $interval < 730){
            // 	$join_interval="Within 2 years";   
            // }elseif($interval>730 AND $interval <1825){
            // 	$join_interval="2 years to 5 years";
            // }else{
            // 	$join_interval="5 years and above";
            // }
            // $user_list[$x]['join_range']= $join_interval;
            $x++;
        }
        $DTRenderArray = $user_list;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            //"iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalRecords" => count((array) $total_users),
            "iTotalDisplayRecords" => count((array) $total_users),
            "aaData" => array()
        );
        //$dtDisplayColumns = array('user_id', 'traineename','designation', 'workshop_name','workshop_type','workshop_subtype','region_name','sub_region','workshop_session', 'questionset', 'trainername', 'tregion_name', 'topicname', 'subtopicname', 'question_title', 'correct_answer', 'user_answer', 'start_dttm', 'end_dttm', 'seconds', 'timer', 'question_result');
        $dtDisplayColumns[0] = 'user_id';
        $dtDisplayColumns[1] = 'user_name';
        // $dtDisplayColumns[2] = 'joining_date';
        $dtDisplayColumns[2] = 'division';
        // $dtDisplayColumns[4]='pc_hq';
        // $dtDisplayColumns[5]='state';
        $dtDisplayColumns[3] = 'zone';
        $dtDisplayColumns[4] = 'region';
        $dtDisplayColumns[5] = 'ec';
        $dtDisplayColumns[6] = 'ec_name';
        $dtDisplayColumns[7] = 'email';
        $dtDisplayColumns[8] = 'designation';
        $dtDisplayColumns[9] = 'assessment_name';
        $dtDisplayColumns[10] = 'status';
        $dtDisplayColumns[11] = 'ai_overall_score';
        $y = 12;
        $parameter_score_result = $this->ai_reports_model->get_parameters_report($company_id, $assessment_id1);
        foreach ($parameter_score_result as $psr) {
            $dtDisplayColumns[$y] = $psr->parameter_name;
            $y++;
        }
        $dtDisplayColumns[$y++] = 'manual_overall_score';
        $dtDisplayColumns[$y++] = 'aiandmanual';
        // $dtDisplayColumns[$y++]='differnce';
        $dtDisplayColumns[$y++] = 'total_attempts';
        $dtDisplayColumns[$y++] = 'ai_rating';
        $dtDisplayColumns[$y++] = 'manual_rating';
        // $dtDisplayColumns[$y++]='join_range';
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ' and isset($dtDisplayColumns)) {
                    $row[] = isset($dtRow[$dtDisplayColumns[$i]]) ? $dtRow[$dtDisplayColumns[$i]] : '-';
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function check_excel_status()
    {
        $task_id = $this->input->post('task_id', true);
        if ($task_id != "") {
            $report_output = $this->common_model->get_value('cronjob_reports', '*', "cronjob_status=1 AND id='" . $task_id . "'");
            if (count((array) $report_output) > 0) {
                $excel_filename = $report_output->excel_filename;
                $data['success'] = "true";
                $data['filepath'] = base_url() . "/report_excel/" . $excel_filename;
                $data['filename'] = $excel_filename;
            } else {
                $data['success'] = "false";
            }
        } else {
            $data['success'] = "false";
        }
        echo json_encode($data);
    }
    public function download_report()
    {
        $now = date('Y-m-d H:i:s');
        $post_data = array(
            'for_user_id' => $this->mw_session['user_id'],
            'company_id' => $this->mw_session['company_id'],
            'assessment_id' => $this->input->post('assessment_id', true),
            'user_id' => $this->input->post('user_id', true),
            'status_id' => $this->input->post('status_id', true),
            'cronjob_status' => 0,
            'excel_filename' => "",
            'cronjob_started' => 0,
            'system_dttm' => $now,
        );
        $task_id = $this->common_model->insert('cronjob_reports', $post_data);
        $data['success'] = "true";
        $data['task_id'] = $task_id;
        echo json_encode($data);
    }
    public function exportReport()
    {
        $Company_name = "";
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $company_id = $this->mw_session['company_id'];
            $user_id = $this->input->post('user_id', true);
            $status_id = $this->input->post('status_id', true);
            $dtWhere = '';
            if ($status_id == 0) {
                $dtWhere .= ' AND b.is_completed= 1 ';
            } elseif ($status_id != 2) {
                $dtWhere .= ' AND (b.is_completed= 0 OR b.is_completed IS NULL) ';
            }
            if ($user_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND c.user_id  = " . $user_id;
                } else {
                    $dtWhere .= " AND c.user_id = " . $user_id;
                }
            }
            $assessment_name = '';
            $assessment_id1 = $this->input->post('assessment_id3', true);
            if ($assessment_id1 != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND a.assessment_id  = " . $assessment_id1;
                } else {
                    $dtWhere .= " AND a.assessment_id = " . $assessment_id1;
                }
            }
            $dtWhere1 = '';
            $user_details = $this->ai_reports_model->status_check_excel($company_id, $status_id, $dtWhere, $dtWhere1);

            $manager_details = $this->ai_reports_model->assessment_manager_details($assessment_id1);
            $ass_managers = [];
            if (!empty($manager_details)) {
                foreach ($manager_details as $manager) {
                    $ass_managers[$manager->user_id] = [
                        'trainer_no' => $manager->trainer_id,
                        'trainer_name' => $manager->trainer_name
                    ];
                }
            }

            $ass_ai_score = $this->ai_reports_model->assessment_get_ai_score($company_id, $assessment_id1);
            $user_ai_score = [];
            if (!empty($ass_ai_score)) {
                foreach ($ass_ai_score as $ai_score) {
                    $user_ai_score[$ai_score->user_id] = $ai_score->overall_score;
                }
            }

            $ass_parameter_scores = $this->ai_reports_model->assessment_get_parameter_sub_parameter_score($company_id, $assessment_id1);
            $user_parameter_score = [];
            $params = [];
            if (!empty($ass_parameter_scores)) {
                foreach ($ass_parameter_scores as $parameter_score) {
                    $user_parameter_score[$parameter_score->user_id][$parameter_score->parameter_label_id] = [
                        'parameter_name' => $parameter_score->parameter_name,
                        'parameter_id' => $parameter_score->parameter_id,
                        'parameter_score' => $parameter_score->score,
                    ];
                    $params[$parameter_score->parameter_label_id] = $parameter_score->parameter_name;
                }
            }

            $user_list = [];
            $x = 0;
            foreach ($user_details as $ud) {
                $assessment_name = $ud->assessment;
                $user_id = $ud->user_id;

                $user_list[$x]['E Code'] = $ud->emp_id;
                $user_list[$x]['Employee name'] = $ud->user_name;
                // $user_list[$x]['DOJ']= $ud->joining_date;
                $user_list[$x]['Division'] = $ud->department;
                // $user_list[$x]['pc_hq']=  $ud->hq;
                // $user_list[$x]['state']= "";
                $user_list[$x]['Zone'] = $ud->area;
                $user_list[$x]['Region'] = $ud->region_name;
                // $user_list[$x]['EC']= "";
                // $user_list[$x]['L+1 name']= "";
                $user_list[$x]['EC'] = !empty($ass_managers) && isset($ass_managers[$user_id]) ? $ass_managers[$user_id]['trainer_no'] : '';
                $user_list[$x]['L+1 name'] = !empty($ass_managers) && isset($ass_managers[$user_id]) ? $ass_managers[$user_id]['trainer_name'] : '';

                $assessment_id = $ud->assessment_id;
                $user_list[$x]['Email'] = $ud->email;
                $user_list[$x]['Designation'] = $ud->designation;
                $user_list[$x]['Assessment Name'] = $ud->assessment;
                $user_list[$x]['Status'] = $ud->status_u;
                $overall_score = 0;
                // $ai_score= $this->ai_reports_model->get_ai_score($company_id, $assessment_id, $user_id);
                // $user_list[$x]['AI Score']= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
                // $ai_score1= !empty($ai_score->overall_score) ? $ai_score->overall_score: 0;
                // $parameter_score_result = $this->ai_reports_model->get_parameter_sub_parameter_score($company_id,$assessment_id,$user_id);
                // $params = [];
                // foreach ($parameter_score_result as $psr){
                //     $parameter_id= $psr->parameter_id;
                //     $parameter_name =$psr->parameter_name;
                //     $params[] = $parameter_name;
                //     $parameter_label_id            = $psr->parameter_label_id;
                //     $user_list[$x][$parameter_name]= !empty($psr->score) ? $psr->score: '-';
                //     $user_score[]= !empty($psr->score) ? $psr->score: '-';
                // }
                $user_list[$x]['AI Score'] = !empty($user_ai_score) && isset($user_ai_score[$user_id]) ? $user_ai_score[$user_id] : '-';
                $ai_score1 = !empty($user_ai_score) && isset($user_ai_score[$user_id]) ? $user_ai_score[$user_id] : '-';
                if (!empty($user_parameter_score) && isset($user_parameter_score[$user_id])) {
                    foreach ($user_parameter_score[$user_id] as $up_score) {
                        $user_list[$x][$up_score['parameter_name']] = isset($up_score['parameter_score']) ? $up_score['parameter_score'] : '-';
                    }
                }

                $manual_overall_score = 0;
                $manual_score = $this->ai_reports_model->get_manual_score($assessment_id, $user_id);
                if (isset($manual_score) and count((array) $manual_score) > 0) {
                    $manual_overall_score = $manual_score->overall_score;
                }
                if ($manual_overall_score == "0") {
                    $user_list[$x]['Assessor Rating'] = '-';
                } else {
                    $user_list[$x]['Assessor Rating'] = $manual_overall_score;
                }
                if ($manual_overall_score == 0) {
                    if ($ai_score1 == 0) {
                        $total = '-';
                        $diff = '-';
                    } else {
                        $total = $ai_score1;
                        $diff = round(($ai_score1 - $manual_overall_score), 2);
                    }
                } elseif ($ai_score1 == 0) {
                    if ($manual_overall_score == 0) {
                        $total = '-';
                        $diff = '-';
                    } else {
                        $total = $manual_overall_score;
                        $diff = ($ai_score1 == '-') ? -$manual_overall_score : round(($ai_score1 - $manual_overall_score), 2);
                        // $diff=round(($ai_score1-$manual_overall_score),2);
                    }
                } else {
                    $total = round(($ai_score1 + $manual_overall_score) / 2, 2);
                    $diff = round(($ai_score1 - $manual_overall_score), 2);
                }
                $user_list[$x]['Overall Avg'] = $total;
                // $user_list[$x]['DIFF (AI-Avg)']= $diff;
                $user_list[$x]['Number of Attempts'] = $ud->attempts . '/' . $ud->total_attempts;
                $rating = '';
                // if ((float) $ai_score1 >= 75) {
                //     $rating = 'Above 75%';
                // } else if ((float) $ai_score1 < 75 and (float) $ai_score1 >= 60) {
                //     $rating = '60 to 74%';
                // } else if ((float) $ai_score1 < 60 and (float) $ai_score1 >= 40) {
                //     $rating = '40 to 59%';
                // } else if ((float) $ai_score1 < 40 and (float) $ai_score1 > 0) {
                //     $rating = 'Less then 40';
                // } else {
                //     $rating = '-';
                // }
                // Industry thresholds
                $this->db->select('*')->from('industry_threshold_range');
                $range = $this->db->get()->result();
                foreach ($range as $rg) {
                    $range_from[] = $rg->range_from;
                    $range_to[] = $rg->range_to;
                }
                if ((float) $ai_score1 >= $range_from['0'] and (float) $ai_score1 <= $range_to['0']) {
                    $rating = 'Above 85%';
                } else if ((float) $ai_score1 > $range_from['1'] and (float) $ai_score1 <= $range_to['1'] . '99') {
                    $rating = '75%  to 84%';
                } else if ((float) $ai_score1 > $range_from['2'] and (float) $ai_score1 <= $range_to['2'] . '99') {
                    $rating = '65 to 74%';
                } else if ((float) $ai_score1 > $range_from['3'] and (float) $ai_score1 <= $range_to['3'] . '99') {
                    $rating = '55% to 64%';
                } else if ((float) $ai_score1 > $range_from['4'] and (float) $ai_score1 <= $range_to['4'] . '99') {
                    $rating = '26% to 54%';
                } else if ((float) $ai_score1 >= $range_from['5'] and (float) $ai_score1 <= $range_to['5'] . '99') {
                    $rating = 'Below 25%';
                } else {
                    $rating = '-';
                }
                $user_list[$x]['AI Rating'] = $rating;
                $manual_rating = '';
                // if ((float) $manual_overall_score >= 75) {
                //     $manual_rating = 'Above 75%';
                // } else if ((float) $manual_overall_score < 75 and (float) $manual_overall_score >= 60) {
                //     $manual_rating = '60 to 74%';
                // } else if ((float) $manual_overall_score < 60 and (float) $manual_overall_score >= 40) {
                //     $manual_rating = '40 to 59%';
                // } else if ((float) $manual_overall_score < 40 and (float) $manual_overall_score > 0) {
                //     $manual_rating = 'Less then 40';
                // } else {
                //     $manual_rating = '-';
                // }
                if ((float) $manual_overall_score >= $range_from['0'] and (float) $manual_overall_score <= $range_to['0']) {
                    $manual_rating = 'Above 85%';
                } else if ((float) $manual_overall_score > $range_from['1'] and (float) $manual_overall_score <= $range_to['1'] . '99') {
                    $manual_rating = '75%  to 84%';
                } else if ((float) $manual_overall_score > $range_from['2'] and (float) $manual_overall_score <= $range_to['2'] . '99') {
                    $manual_rating = '65 to 74%';
                } else if ((float) $manual_overall_score > $range_from['3'] and (float) $manual_overall_score <= $range_to['3'] . '99') {
                    $manual_rating = '55% to 64%';
                } else if ((float) $manual_overall_score > $range_from['4'] and (float) $manual_overall_score <= $range_to['4'] . '99') {
                    $manual_rating = '26% to 54%';
                } else if ((float) $manual_overall_score >= $range_from['5'] and (float) $manual_overall_score <= $range_to['5'] . '99') {
                    $manual_rating = 'Below 25%';
                } else {
                    $manual_rating = '-';
                }
                $user_list[$x]['Manual Rating'] = $manual_rating;
                // $reg_date=date_create($ud->joining_date);
                // $today = date_create(date('d-m-Y'));
                // $interval = date_diff($reg_date, $today)->format('%R%a days');
                // if($interval<182.5){
                //     $join_interval="06 months";
                // }elseif($interval>182.5 AND $interval < 730){
                //     $join_interval="Within 2 years";   
                // }elseif($interval>730 AND $interval <1825){
                //     $join_interval="2 years to 5 years";
                // }else{
                //     $join_interval="5 years and above";
                // }
                // $user_list[$x]['Joinning range']= $join_interval;
                $x++;
            }
            $Data_list = $user_list;
            $this->load->library('PHPExcel');
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);
            $i = 1;
            $j = 1;
            $dtDisplayColumns = array_keys($user_list[0]);
            foreach ($dtDisplayColumns as $column) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $column);
                $j++;
            }
            $j = 2;
            foreach ($Data_list as $value) {
                $i = 1;
                foreach ($dtDisplayColumns as $column) {
                    $column_score = isset($value[$column]) ? $value[$column] : '-';
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $column_score);
                    // $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                    $i++;
                }
                $j++;
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            if ($assessment_id1 != '') {
                header('Content-Disposition: attachment;filename=' . "$assessment_name.xls");
            } else {
                header('Content-Disposition: attachment;filename="Report.xls"');
            }
            header('Cache-Control: max-age=0');
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
            // ob_end_clean();
            $objWriter->save('php://output');
        } else {
            redirect('ai_reports');
        }
    }

    public function export_spotlight_dump()
    { //In use for Export
        $Company_id = $this->session->userdata();

        $c_id = $Company_id['awarathon_session']['company_id'];

        $user_id = $this->input->post('user_id', true);
        $assessment_id = $this->input->post('assessment_id', true);

        $file_name = "Spotlight_dump_" . $assessment_id . "_" . $user_id . ".xls";
        $objPHPExcel  = new Spreadsheet();

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A2', "Question Fired ")
            ->setCellValue('B2', "Embed Value")
            ->setCellValue('C2', "Answer Transcript (Text)")
            ->setCellValue('D2', "Cosine Score");


        $styleArray = array(
            'font' => array(
                //                'bold' => true
            )
        );

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
            )
        );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);


        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleArray_header);


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    //                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);
        $i = 2;

        $Data_list = $this->ai_reports_model->get_questions_user_details($c_id, $assessment_id, $user_id);
        
        foreach ($Data_list as $value) {
            $i++;
            $score = "'" . $value->cosine_score . "'";
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$i", "$value->question")
                ->setCellValue("B$i", "$value->embeddings")
                ->setCellValue("C$i", "$value->audio_totext")
                ->setCellValue("D$i", "$score");

            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill();
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

    // export_trinity dump code start here "Nirmal Gajjar" 
    public function export_trinity_dump()
    {

        $Company_id = $this->session->userdata();

        $c_id = $Company_id['awarathon_session']['company_id'];
        // $portal_name = $Company_id['awarathon_session']['company_name'];
        $user_id = $this->input->post('user_id', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $assessment_name = $this->common_model->get_value('assessment_mst', 'assessment', "id = $assessment_id");
        $assessment_name = $assessment_name->assessment;

        $portal_name = $this->common_model->get_value('company', 'portal_name', "id = $c_id");
        $portal_name = $portal_name->portal_name;
        //  $file_name = "Trinity_dump_" . $portal_name . "_" . $assessment_name . "_" . $user_id . ".xls";
        $file_name = $portal_name . "_" . $assessment_name . "_" . $user_id . ".xls";
        $objPHPExcel = new Spreadsheet();

        $styleArray_header = array(
            'font' => array(
                'border' => 1,
                'bold' => true

            )
        );

        $sub_parameter_level_data = $this->ai_reports_model->sub_parameter_level_data($assessment_id, $c_id, $user_id);
        $raw_scores_data = $this->ai_reports_model->raw_scores_data($assessment_id, $c_id, $user_id);
        // $transcripts_data = $this->ai_reports_model->transcripts_data($assessment_id, $c_id, $user_id); // old data
        $similarity_data = $this->ai_reports_model->similarity_data($assessment_id, $c_id, $user_id);
        $transcript_details = $this->ai_reports_model->assessment_transcript_details($user_id, $c_id, $assessment_id);
        // For Sub Parameter level sheet start
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Question no');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Types');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Param');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Formal name');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Score');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);


        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray_header);
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array()
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);
        if (!empty($sub_parameter_level_data)) {
            $i = 1;
            foreach ($sub_parameter_level_data as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", "$value->user_name")
                    ->setCellValue("B$i", "$value->question_series")
                    ->setCellValue("C$i", "$value->type")
                    ->setCellValue("D$i", "$value->parameter_name")
                    ->setCellValue("E$i", "$value->formal_name")
                    ->setCellValue("F$i", "$value->score");

                $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getFill();
            }
        } else {
            $i = 2;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$i", "-")
                ->setCellValue("B$i", "-")
                ->setCellValue("C$i", "-")
                ->setCellValue("D$i", "-")
                ->setCellValue("E$i", "-")
                ->setCellValue("F$i", "-");
        }
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sub Parameter level');
        // For Sub Parameter level sheet end

        // For creating 2nd sheet 
        $objPHPExcel->createSheet();
        // For creating 2nd sheet end

        // For Raw Scores sheet start
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Question no');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Types');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Scores');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray_header);
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array()
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);
        if (!empty($raw_scores_data)) {
            $a = 1;
            foreach ($raw_scores_data as $v2) {
                $a++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$a", "$v2->user_name")
                    ->setCellValue("B$a", "-")
                    ->setCellValue("C$a", "$v2->parameter_name")
                    ->setCellValue("D$a", "$v2->score");

                $objPHPExcel->getActiveSheet()->getStyle("A$a:D$a")->getFill();
            }
        } else {
            $a = 2;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$a", "-")
                ->setCellValue("B$a", "-")
                ->setCellValue("C$a", "-")
                ->setCellValue("D$a", "-");
        }

        // Rename 2nd sheet
        $objPHPExcel->getActiveSheet()->setTitle('Raw Scores');
        // For Raw Scores sheet end

        // For creating 3rd sheet 
        $objPHPExcel->createSheet();
        // For creating 3rd sheet end

        // For Transcripts sheet start
        $objPHPExcel->setActiveSheetIndex(2);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Question No');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Video ID');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Language');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Transcript');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Multi Transcript');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Duration');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Time Taken');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Words');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40); // for transcript
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray_header);
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array()
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);
        $full_transcript = '';
        $date_start = '';
        $date_end = '';
        $duration = '';
        if (!empty($transcript_details)) {
            $b = 2;
            foreach ($transcript_details as $key => $transcript) {
                if ($transcript->speaker == 'R') {
                    $full_transcript .= " " . $transcript->content;
                }
                if ($key == 0) {
                    $date_start = $transcript->addeddate;
                }
                if ($key == array_key_last($transcript_details)) {
                    $date_end = $transcript->addeddate;
                }
            }
            $user_name = $transcript_details[0]->user_name;
            $timestamp1 = strtotime($date_start);
            $timestamp2 = strtotime($date_end);

            $duration = $timestamp2 - $timestamp1;

            $no_of_words = str_word_count($full_transcript);
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$b", "$user_name")
                ->setCellValue("B$b", "-")
                ->setCellValue("C$b", "-")
                ->setCellValue("D$b", "English")
                ->setCellValue("E$b", "$full_transcript")
                ->setCellValue("F$b", "-")
                ->setCellValue("G$b", "$duration")
                // ->setCellValue("G$b", "-")
                ->setCellValue("H$b", "-")
                ->setCellValue("I$b", "$no_of_words");

            $objPHPExcel->getActiveSheet()->getStyle("A$a:D$a")->getFill();
        } else {
            $b = 2;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$b", "-")
                ->setCellValue("B$b", "-")
                ->setCellValue("C$b", "-")
                ->setCellValue("D$b", "-")
                ->setCellValue("E$b", "-")
                ->setCellValue("F$b", "-")
                ->setCellValue("G$b", "-")
                ->setCellValue("H$b", "-")
                ->setCellValue("I$b", "-");
        }


        // old code
        // if (!empty($transcripts_data)) {
        //     $b = 1;
        //     foreach ($transcripts_data as $v3) {
        //         $b++;
        //         $words = str_word_count($v3->transcript);
        //         $objPHPExcel->getActiveSheet()
        //             ->setCellValue("A$b", "$v3->user_name")
        //             ->setCellValue("B$b", "$v3->question_series")
        //             ->setCellValue("C$b", "-")
        //             ->setCellValue("D$b", "English")
        //             ->setCellValue("E$b", "$v3->transcript")
        //             ->setCellValue("F$b", "-")
        //             ->setCellValue("G$b", "-")
        //             ->setCellValue("H$b", "-")
        //             ->setCellValue("I$b", "$words");

        //         $objPHPExcel->getActiveSheet()->getStyle("A$a:D$a")->getFill();
        //     }
        // } else {
        //     $b = 2;
        //     $objPHPExcel->getActiveSheet()
        //         ->setCellValue("A$b", "-")
        //         ->setCellValue("B$b", "-")
        //         ->setCellValue("C$b", "-")
        //         ->setCellValue("D$b", "-")
        //         ->setCellValue("E$b", "-")
        //         ->setCellValue("F$b", "-")
        //         ->setCellValue("G$b", "-")
        //         ->setCellValue("H$b", "-")
        //         ->setCellValue("I$b", "-");
        // }
        //old code 

        // Rename 3rd sheet
        $objPHPExcel->getActiveSheet()->setTitle('Transcripts');
        // For Transcripts sheet end

        // For creating 4th sheet end
        $objPHPExcel->createSheet();
        // For creating 4th sheet end

        // For Similarity Data sheet start
        $objPHPExcel->setActiveSheetIndex(3);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Question No');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Text');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Score');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Matches With');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Threshold');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Score Scaled');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Type');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray_header);
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array()
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);

        if (!empty($similarity_data)) {
            $c = 1;
            foreach ($similarity_data as $v4) {
                $c++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$c", "$v4->user_name")
                    ->setCellValue("B$c", "$v4->question_series")
                    ->setCellValue("C$c", "-")
                    ->setCellValue("D$c", "$v4->score")
                    ->setCellValue("E$c", "$v4->matches_with")
                    ->setCellValue("F$c", "60")
                    ->setCellValue("G$c", "-")
                    ->setCellValue("H$c", "Product Knowledge");

                $objPHPExcel->getActiveSheet()->getStyle("A$a:H$a")->getFill();
            }
        } else {
            $c = 2;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$b", "-")
                ->setCellValue("B$b", "-")
                ->setCellValue("C$b", "-")
                ->setCellValue("D$b", "-")
                ->setCellValue("E$b", "-")
                ->setCellValue("F$b", "-")
                ->setCellValue("G$b", "-")
                ->setCellValue("H$b", "-");
        }

        // Rename 4th sheet
        $objPHPExcel->getActiveSheet()->setTitle('Similarity Data');
        // For Similarity Data sheet end

        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        $objWriter->save('php://output');
    }
    // export_trinity dump code end here "Nirmal Gajjar" 

}