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
        $start_date = '';
        $end_date = date("Y-m-d h:i:s");
        $_participants_result = $this->ai_reports_model->get_process_participants($company_id, $assessment_id, $start_date, $end_date);
        $data['_participants_result'] = $_participants_result;
        // $_cronjob_result =$this->ai_reports_model->get_process_schedule($company_id,$asssessment_id);
        // if (isset($_cronjob_result) AND count((array)$_cronjob_result)>0){
        //     $data['_cronjob_result'] = $_cronjob_result->process_status;
        // }else{
        //     $data['_cronjob_result'] = 0;
        // }
        $assessment_type = $this->common_model->get_value('assessment_mst', 'assessment_type', "id = $assessment_id");
        $data['assessment_type'] = $assessment_type->assessment_type;
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
            $file_name = 'MANU-C' . $_company_id . 'A' . $_assessment_id . 'U' . $