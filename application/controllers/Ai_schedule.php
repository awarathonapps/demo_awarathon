<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ai_schedule extends MY_Controller {
    function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('ai_schedule');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('ai_schedule_model');
        $this->load->model('ai_cronjob_model');
    }
    public function index() {
        $data['module_id'] = '14.02';
        $data['acces_management'] = $this->acces_management;
        $_assessment_result = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'status=1','assessment');
        $data['company_id'] = $this->mw_session['company_id'];
        $data['assessment_result'] = $_assessment_result;
        $this->load->view('ai_schedule/index',$data);
    }
    function turnon_reports_flags(){
        $company_id    = $this->input->post('company_id', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $output = array();
        if ($company_id!="" AND $assessment_id!=""){
            // $is_cronjob_schedule = $this->common_model->get_value('ai_cronjob', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
            // if (isset($is_cronjob_schedule) AND count((array)$is_cronjob_schedule)>0) {
            //     $output = json_decode('{"success": "false", "message": "Cronjob for this assessment is already running can not generate a report at this moment."}');
            // }else{
                //SET REPORT DISPLAY FLAG OFF
                $show_report_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
                if (isset($show_report_result) AND count((array)$show_report_result)>0){
                    $update_data = array('show_reports' => 1);
                    $this->common_model->update('ai_cronreports', 'id', $show_report_result->id, $update_data);
                }else{
                    $post_data = array('company_id' => $company_id, 'assessment_id' => $assessment_id, 'show_reports' => 1);
                    $show_report_id = $this->common_model->insert('ai_cronreports', $post_data);
                }
                $output = json_decode('{"success": "true", "message": "Report flags turn on sucessfully"}');
            // }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
        }
        echo json_encode($output);
    }
    function fetch_participants(){
        $html = '';
        $company_id = $this->mw_session['company_id'];
        $asssessment_id = $this->input->post('assessment_id', true);
        // $is_cronjob_schedule = $this->common_model->get_value('ai_cronjob', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'"');
        // $is_cronjob_schedulei = $this->common_model->get_value('ai_cronjob', 'id', '1=1');

        // if ((isset($is_cronjob_schedule) AND count((array)$is_cronjob_schedule)>0) OR
        //    (!isset($is_cronjob_schedulei) AND count((array)$is_cronjob_schedulei)==0)) {
            $_participants_result =$this->ai_schedule_model->get_participants($company_id,$asssessment_id);
            $data['_participants_result'] = $_participants_result;
            // foreach ($_participants_result as $pdata) { 
            //     $company_id      = $pdata->company_id;
            //     $assessment_id   = $pdata->assessment_id;
            //     $user_id         = $pdata->user_id;
            //     $trans_id        = $pdata->trans_id;
            //     $question_id     = $pdata->question_id;
            //     $question_series = $pdata->question_series;

            //     $task_id = $this->common_model->get_value('ai_schedule', 'task_id,task_status', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            //     if (isset($task_id) AND count((array)$task_id)>0){
            //     }else{
            //         //INSERT BLANK RECORD.
            //         $now = date('Y-m-d H:i:s');
            //         $post_data = array(
            //             'company_id'        => $company_id,
            //             'assessment_id'     => $assessment_id,
            //             'user_id'           => $user_id,
            //             'trans_id'          => $trans_id,
            //             'question_id'       => $question_id,
            //             'question_series'   => $question_series,
            //             'py_parameter'      => "",
            //             'task_id'           => "",
            //             'task_status'       => 0,
            //             'xls_generated'     => 0,
            //             'xls_filename'      => '',
            //             'xls_imported'      => 0,
            //             'pdf_generated'     => 0,
            //             'pdf_filename'      => '',
            //             'mpdf_generated'    => 0,
            //             'mpdf_filename'     => '',
            //             'cpdf_generated'    => 0,
            //             'cpdf_filename'     => '',
            //             'schedule_by'       => $this->mw_session['user_id'],
            //             'schedule_datetime' => $now
            //         );
            //         $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
            //     }
            // }

            $_cronjob_result =$this->ai_schedule_model->get_schedule($company_id,$asssessment_id);
            if (isset($_cronjob_result) AND count((array)$_cronjob_result)>0){
                $data['_cronjob_result'] = $_cronjob_result->process_status;
            }else{
                $data['_cronjob_result'] = 0;
            }
            $html = $this->load->view('ai_schedule/load_participants',$data,true);
            $data['html'] = $html;
            $data['success'] = "true";
            $data['message'] = "";
        // }else{
        //     $data['html']    = "";
        //     $data['success'] = "false";
        //     $data['message'] = "CRONJOB_SCHEDULED";
        // }
        echo json_encode($data);
    }
    function schedule_process(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        if ($company_id!="" AND $assessment_id!=""){
            $is_cronjob_schedule = $this->common_model->get_value('ai_cronjob', 'id','1=1');
            if (!isset($is_cronjob_schedule) AND count((array)$is_cronjob_schedule)==0){
                // //DELETE EXISTING TASK FROM AZURE
                // $existing_task_result = $this->ai_schedule_model->get_existing_task($company_id,$assessment_id);
                // if (isset($existing_task_result) AND count((array)$existing_task_result) >0){
                //     foreach ($existing_task_result as $edata) { 
                //         $ex_company_id      = $edata->company_id;
                //         $ex_assessment_id   = $edata->assessment_id;
                //         $ex_user_id         = $edata->user_id;
                //         $ex_trans_id        = $edata->trans_id;
                //         $ex_question_id     = $edata->question_id;
                //         $ex_task_id         = $edata->task_id;
                //         if ($ex_company_id!="" AND $ex_assessment_id!="" AND $ex_user_id!="" AND $ex_trans_id!="" AND $ex_question_id!=""){
                //             try {
                //                 $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/delete_task.py --task_id='".$ex_task_id."' 2>&1"));
                //                 $output = json_decode($output);
                //                 if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                //                     $whereclause = "company_id='".$ex_company_id."' AND assessment_id='".$ex_assessment_id."' AND user_id='".$ex_user_id."' AND trans_id='".$ex_trans_id."' AND question_id='".$ex_question_id."'";
                //                     $this->common_model->delete_whereclause('ai_schedule', $whereclause);
                //                     $this->common_model->delete_whereclause('ai_subparameter_score', $whereclause);
                //                     $this->common_model->delete_whereclause('ai_sentkey_score', $whereclause);
                //                 }
                //             }catch(Exception $e) {
                //             }
                //         }
                //     }
                // }
                // $existing_task_result = $this->ai_schedule_model->get_existing_task($company_id,$assessment_id);
                // if (isset($existing_task_result) AND count((array)$existing_task_result) >0){
                //     //DELETE ALL EXISTING TASK
                //     $whereclause = "company_id='".$company_id."' AND assessment_id='".$assessment_id."'";
                //     $this->common_model->delete_whereclause('ai_schedule', $whereclause);
                //     $this->common_model->delete_whereclause('ai_subparameter_score', $whereclause);
                //     $this->common_model->delete_whereclause('ai_sentkey_score', $whereclause);
                // }
                //SET REPORT DISPLAY FLAG OFF
                $show_report_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
                $now = date('Y-m-d H:i:s');
                if (isset($show_report_result) AND count((array)$show_report_result)>0){
                    $update_data = array('show_reports' => 0);
                    $this->common_model->update('ai_cronreports', 'id', $show_report_result->id, $update_data);
                }else{
                    $post_data = array('show_reports' => 0);
                    $show_report_id = $this->common_model->insert('ai_cronreports', $post_data);
                }
                $process_id = $this->common_model->get_value('ai_cronjob', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
                $now = date('Y-m-d H:i:s');
                if (isset($process_id) AND count((array)$process_id)>0){
                    $update_data = array(
                        'process_status'    => 1,
                        'process_started'   => 0,
                        'schedule_by'       => $this->mw_session['user_id'],
                        'schedule_datetime' => $now
                    );
                    $this->common_model->update('ai_cronjob', 'id', $process_id->id, $update_data);
                }else{
                    $post_data = array(
                        'company_id'        => $company_id,
                        'assessment_id'     => $assessment_id,
                        'process_status'    => 1,
                        'process_started'   => 0,
                        'schedule_by'       => $this->mw_session['user_id'],
                        'schedule_datetime' => $now
                    );
                    $ai_cronjob_id = $this->common_model->insert('ai_cronjob', $post_data);
                }
                $output = json_decode('{"success": "true", "message": "Process scheduled"}');
                echo json_encode($output);
            }else{
                $output = json_decode('{"success": "false", "message": "CRONJOB_SCHEDULED"}');
                echo json_encode($output);
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function schedule_task(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $portal_name     = $this->input->post('portal_name');
        $assessment_name = $this->input->post('assessment_name');
        $user_name       = $this->input->post('user_name');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($portal_name!="" AND $assessment_name!="" AND $user_name!="" AND $question_series!="" AND $company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $task_id = $this->common_model->get_value('ai_schedule', 'task_id,task_status', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($task_id) AND count((array)$task_id)>0){
                if ($task_id->task_id !== ""){
                    if ($task_id->task_id == "FAILED"){
                        $output = json_decode('{"success": "false", "message": "FAILED"}'); 
                        echo json_encode($output); 
                    }else{
                        $output = json_decode('{"success": "true", "message": "'.$task_id->task_id.'"}');  
                        echo json_encode($output);
                    }
                }else{
                    $output = json_decode('{"success": "NA", "message": "Script failed"}');
                    echo json_encode($output);
                }
            }else{
                $output = json_decode('{"success": "NA", "message": "Invalid parameter"}');  
                echo json_encode($output);
            }
        }else{
            $output = json_decode('{"success": "NA", "message": "Invalid parameter"}');  
            echo json_encode($output);
        }
    }
    function pending_task(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $portal_name     = $this->input->post('portal_name');
        $assessment_name = $this->input->post('assessment_name');
        $user_name       = $this->input->post('user_name');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');

        if ($portal_name!="" AND $assessment_name!="" AND $user_name!="" AND $question_series!=""){
            $task_result = $this->common_model->get_value('ai_schedule', 'id,task_id,task_status', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            $now         = date('Y-m-d H:i:s');
            if (isset($task_result) AND ($task_result->task_status==0 OR $task_result->task_status=="0")){
                try {
                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/reschedule_task.py --task_id='".$task_result->task_id."' 2>&1"));
                    $output = json_decode($output);
                    if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                        if (isset($task_result) AND count((array)$task_result)>0){
                            $update_data = array(
                                'task_id'           => $output->id,
                                'task_status'       => 0,
                                'xls_generated'     => 0,
                                'xls_filename'      => '',
                                'xls_imported'      => 0,
                                'pdf_generated'     => 0,
                                'pdf_filename'      => '',
                                'mpdf_generated'    => 0,
                                'mpdf_filename'     => '',
                                'cpdf_generated'    => 0,
                                'cpdf_filename'     => '',
                                'schedule_by'       => $this->mw_session['user_id'],
                                'schedule_datetime' => $now
                            );
                            $this->common_model->update('ai_schedule', 'id', $task_result->id, $update_data);
                        }else{
                            $post_data = array(
                                'company_id'        => $company_id,
                                'assessment_id'     => $assessment_id,
                                'user_id'           => $user_id,
                                'trans_id'          => $trans_id,
                                'question_id'       => $question_id,
                                'question_series'   => $question_series,
                                'task_id'           => $output->id,
                                'task_status'       => 0,
                                'xls_generated'     => 0,
                                'xls_filename'      => '',
                                'xls_imported'      => 0,
                                'pdf_generated'     => 0,
                                'pdf_filename'      => '',
                                'mpdf_generated'    => 0,
                                'mpdf_filename'     => '',
                                'cpdf_generated'    => 0,
                                'cpdf_filename'     => '',
                                'schedule_by'       => $this->mw_session['user_id'],
                                'schedule_datetime' => $now
                            );
                            $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                        }
                    }
                    echo json_encode($output);
                }catch(Exception $e) {
                    $output = json_decode('{"success": "false", "message": "Script failed"}');
                    echo json_encode($output);
                }
            }else if (isset($task_result) AND ($task_result->task_status==1 OR $task_result->task_status=="1")){
                $output = json_decode('{"id": "'.$task_result->id.'", "success": "true", "message": "Scheduled"}');
                echo json_encode($output);
            }else{
                //NEW TASK
                if ($portal_name!="" AND $assessment_name!="" AND $user_name!="" AND $question_series!=""){
                    try {
                        $json   = '{"portal_name":"'.$portal_name.'","assessment_name":"'.$assessment_name.'","person_name":"'.$user_name.'","question_number":"'.$question_series.'"}';
                        $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/schedule_task.py --data='".$json."' 2>&1"));
                        $output = json_decode($output);
       
                        if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                            
                            $task_id = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                            $now = date('Y-m-d H:i:s');
                            if (isset($task_id) AND count((array)$task_id)>0){
                                $update_data = array(
                                    'task_id'           => $output->id,
                                    'task_status'       => 0,
                                    'xls_generated'     => 0,
                                    'xls_filename'      => '',
                                    'xls_imported'      => 0,
                                    'pdf_generated'     => 0,
                                    'pdf_filename'      => '',
                                    'mpdf_generated'    => 0,
                                    'mpdf_filename'     => '',
                                    'cpdf_generated'    => 0,
                                    'cpdf_filename'     => '',
                                    'schedule_by'       => $this->mw_session['user_id'],
                                    'schedule_datetime' => $now
                                );
                                $this->common_model->update('ai_schedule', 'id', $task_id->id, $update_data);
                            }else{
                                $post_data = array(
                                    'company_id'        => $company_id,
                                    'assessment_id'     => $assessment_id,
                                    'user_id'           => $user_id,
                                    'trans_id'          => $trans_id,
                                    'question_id'       => $question_id,
                                    'question_series'   => $question_series,
                                    'task_id'           => $output->id,
                                    'task_status'       => 0,
                                    'xls_generated'     => 0,
                                    'xls_filename'      => '',
                                    'xls_imported'      => 0,
                                    'pdf_generated'     => 0,
                                    'pdf_filename'      => '',
                                    'mpdf_generated'    => 0,
                                    'mpdf_filename'     => '',
                                    'cpdf_generated'    => 0,
                                    'cpdf_filename'     => '',
                                    'schedule_by'       => $this->mw_session['user_id'],
                                    'schedule_datetime' => $now
                                );
                                $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                            }
                            
                        }
                        echo json_encode($output);
                    }catch(Exception $e) {
                        $output = json_decode('{"success": "false", "message": "Script failed"}');
                        echo json_encode($output);
                    }
                }else{
                    $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
                    echo json_encode($output);
                }
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function task_status(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $task_id = $this->common_model->get_value('ai_schedule', 'task_id,task_status', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($task_id) AND count((array)$task_id)>0){
                if ($task_id->task_status == 1 OR $task_id->task_status=="1"){
                    $output = json_decode('{"success": "true", "message": "Completed"}');
                    echo json_encode($output);
                }else if ($task_id->task_status == 2 OR $task_id->task_status=="2"){
                    $output = json_decode('{"success": "false", "message": "Active"}');
                    echo json_encode($output);
                }else if ($task_id->task_status == 3 OR $task_id->task_status=="3"){
                    $output = json_decode('{"success": "false", "message": "Running"}');
                    echo json_encode($output);
                }else if ($task_id->task_status == 4 OR $task_id->task_status=="4"){
                    $output = json_decode('{"success": "false", "message": "Failed"}');
                    echo json_encode($output);
                }else if ($task_id->task_status == 5 OR $task_id->task_status=="5"){
                    $output = json_decode('{"success": "false", "message": "Update failed"}');
                    echo json_encode($output);
                }else{
                    $output = json_decode('{"success": "false", "message": "Active"}');
                    echo json_encode($output);
                }
            }else{
                $output = json_decode('{"success": "false", "message": "Task id missing"}');
                echo json_encode($output);
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function task_error_log(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $task_result = $this->common_model->get_value('ai_schedule', 'task_id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($task_result) AND count((array)$task_result)>0){
                try {
                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/task_error_details.py --task_id='".$task_result->task_id ."' 2>&1"));
                    $_output = print_r($output, true);
                    $encode_output = '{"success": "true", "message": '.json_encode($_output).'}';
                    echo $encode_output;
                }catch(Exception $e) {
                    $output = json_decode('{"success": "false", "message": "Script failed"}');
                    echo json_encode($output);
                }
            }else{
                $output = json_decode('{"success": "false", "message": "Task id missing"}');
                echo json_encode($output);
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function report_status(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $task_id = $this->common_model->get_value('ai_schedule', 'task_id,xls_generated', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($task_id) AND count((array)$task_id)>0){
                if ($task_id->xls_generated == 1 OR $task_id->xls_generated=="1"){
                    $output = json_decode('{"success": "true", "message": "Excel Generated"}');
                    echo json_encode($output);
                }else if ($task_id->xls_generated == 2 OR $task_id->xls_generated=="2"){
                    $output = json_decode('{"success": "false", "message": "Script failed"}');
                    echo json_encode($output);
                }else{
                    $output = json_decode('{"success": "", "message": ""}');
                    echo json_encode($output);
                }
            }else{
                $output = json_decode('{"success": "false", "message": "Task id missing"}');
                echo json_encode($output);
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            echo json_encode($output);
        }
    }
    function delete_single_task(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $uid             = $this->input->post('uid');

        if ($uid!="" AND $company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $task_result = $this->common_model->get_value('ai_schedule', 'id,task_id,task_status', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($task_result) AND count((array)$task_result)>0){
                try {
                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/delete_task.py --task_id='".$task_result->task_id."' 2>&1"));
                    $output = json_decode($output);
                    if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                        if (isset($task_result) AND count((array)$task_result)>0){
                            $whereclause = "company_id='".$company_id."' AND assessment_id='".$assessment_id."' AND user_id='".$user_id."' AND trans_id='".$trans_id."' AND question_id='".$question_id."'";
                            $this->common_model->delete_whereclause('ai_schedule', $whereclause);
                            $this->common_model->delete_whereclause('ai_subparameter_score', $whereclause);
                            $this->common_model->delete_whereclause('ai_sentkey_score', $whereclause);

                            $output = json_decode('{"success": "true", "message": "Task deleted successfully."}');
                        }else{
                            $output = json_decode('{"success": "false", "message": "Script failed"}');
                        }
                    }else{
                        $_output = print_r($output, true);
                        $output = '{"success": "false", "message": '.json_encode($_output).'}';
                    }
                }catch(Exception $e) {
                    $output = json_decode('{"success": "false", "message": "Script failed"}');
                }
            }else{
                $output = json_decode('{"success": "false", "message": "No records are associated with this task in a database."}');
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
            
        }
        echo json_encode($output);
    }
    function import_excel(){
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $schedule_result = $this->common_model->get_value('ai_schedule', '*', 'xls_generated=1 AND company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($schedule_result) AND count((array)$schedule_result)>0){
                if ($schedule_result->xls_imported == 1 OR $schedule_result->xls_imported=="1"){
                    $output = json_decode('{"success": "true", "message": "File imported successfully."}');
                }else{
                    $file_name          = $schedule_result->xls_filename;
                    $absolute_file_path = $_SERVER['DOCUMENT_ROOT'].'/'.$file_name;
                    $temp_file_path     = $file_name;
                    if (file_exists($temp_file_path)==TRUE){
                    }else{
                        $output = json_decode('{"success": "false", "message": "File not exists"}');
                    }
                }
            }else{
                $output = json_decode('{"success": "false", "message": "No records are associated with this task in a database."}');
            }
        }else{
            $output = json_decode('{"success": "false", "message": "FILE_NOT_FOUND"}');
        }
        echo json_encode($output);
    }
    function generate_pdf_ai(){
        $site_url        = base_url();
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $temp_pdf_filename = '';
            $pdf_result        = $this->common_model->get_value('ai_schedule', 'pdf_generated,pdf_filename', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($pdf_result) AND count((array)$pdf_result)>0){
                $pdf_generated     = (float)$pdf_result->pdf_generated;
                $temp_pdf_filename = $pdf_result->pdf_filename;
                $temp_file_path = $site_url.'/pdf_reports/'.$temp_pdf_filename;
                if ($pdf_generated==1){
                    if (file_exists('pdf_reports/'.$temp_pdf_filename)==TRUE){
                        $output = json_decode('{"success": "true", "message": "DONE","file_path": "'.$temp_file_path.'"}');
                    }else{
                        $output = json_decode('{"success": "true", "message": "DONE_FILE_NOT_FOUND","file_path": ""}');
                    }
                }else if ($pdf_generated==2){
                    $output = json_decode('{"success": "false", "message": "LOCKED"}');
                }else{
                    $output = json_decode('{"success": "false", "message": "PROCESS_PENDING"}');
                }
            }else{
                $output = json_decode('{"success": "false", "message": "PROCESS_PENDING"}');
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
        }
        echo json_encode($output);
    } 
    function generate_pdf_manual(){
        $site_url        = base_url();
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $temp_pdf_filename = '';
            $pdf_result        = $this->common_model->get_value('ai_schedule', 'mpdf_generated,mpdf_filename', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($pdf_result) AND count((array)$pdf_result)>0){
                $pdf_generated     = (float)$pdf_result->mpdf_generated;
                $temp_pdf_filename = $pdf_result->mpdf_filename;
                $temp_file_path = $site_url.'/pdf_reports/'.$temp_pdf_filename;
                if ($pdf_generated==1){
                    if (file_exists('pdf_reports/'.$temp_pdf_filename)==TRUE){
                        $output = json_decode('{"success": "true", "message": "DONE","file_path": "'.$temp_file_path.'"}');
                    }else{
                        $output = json_decode('{"success": "true", "message": "DONE_FILE_NOT_FOUND","file_path": ""}');
                    }
                }else if ($pdf_generated==2){
                    $output = json_decode('{"success": "false", "message": "LOCKED"}');
                }else{
                    $output = json_decode('{"success": "false", "message": "PROCESS_PENDING"}');
                }
            }else{
                $output = json_decode('{"success": "false", "message": "PROCESS_PENDING"}');
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
        }
        echo json_encode($output);
    }
    function generate_pdf_combined(){
        $site_url        = base_url();
        $company_id      = $this->input->post('company_id');
        $assessment_id   = $this->input->post('assessment_id');
        $user_id         = $this->input->post('user_id');
        $trans_id        = $this->input->post('trans_id');
        $question_id     = $this->input->post('question_id');
        $question_series = $this->input->post('question_series');
        $uid             = $this->input->post('uid');
        if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
            $temp_pdf_filename = '';
            $pdf_result        = $this->common_model->get_value('ai_schedule', 'cpdf_generated,cpdf_filename', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            if (isset($pdf_result) AND count((array)$pdf_result)>0){
                $pdf_generated     = (float)$pdf_result->cpdf_generated;
                $temp_pdf_filename = $pdf_result->cpdf_filename;
                $temp_file_path = $site_url.'/pdf_reports/'.$temp_pdf_filename;
                if ($pdf_generated==1){
                    if (file_exists('pdf_reports/'.$temp_pdf_filename)==TRUE){
                        $output = json_decode('{"success": "true", "message": "DONE","file_path": "'.$temp_file_path.'"}');
                    }else{
                        $output = json_decode('{"success": "true", "message": "DONE_FILE_NOT_FOUND","file_path": ""}');
                    }
                }else if ($pdf_generated==2){
                    $output = json_decode('{"success": "false", "message": "LOCKED"}');
                }else{
                    $output = json_decode('{"success": "false", "message": "PROCESS_PENDING"}');
                }
            }else{
                $output = json_decode('{"success": "false", "message": "PROCESS_PENDING"}');
            }
        }else{
            $output = json_decode('{"success": "false", "message": "Invalid parameter"}');
        }
        echo json_encode($output);
    }
    function check_schedule_completed(){
        $_company_id          = $this->input->post('company_id', true);
        $_assessment_id       = $this->input->post('assessment_id', true);

        $total_task           = 0;
        $total_task_completed = 0;
        $total_task_failed    = 0;
        $total_xls_completed  = 0;
        $total_xlsi_completed = 0;

        $_tasks_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'"');
        if (isset($_tasks_results) AND count((array)$_tasks_results)>0){
            $total_task = $_tasks_results->total;
        }
        
        $_taskfailed_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="4" AND company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'"');
        if (isset($_taskfailed_results) AND count((array)$_taskfailed_results)>0){
            $total_task_failed = $_taskfailed_results->total;
        }

        $_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'"');
        if (isset($_tasksc_results) AND count((array)$_tasksc_results)>0){
            $total_task_completed = $_tasksc_results->total;
        }
        
        $_xls_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'"');
        if (isset($_xls_results) AND count((array)$_xls_results)>0){
            $total_xls_completed = $_xls_results->total;
        }

        $_xlsi_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'"');
        if (isset($_xlsi_results) AND count((array)$_xlsi_results)>0){
            $total_xlsi_completed = $_xlsi_results->total;
        }

        if ((int)$total_task==(int)$total_task_failed){
            $whereclause = "company_id='".$_company_id."' AND assessment_id='".$_assessment_id."'";
            $this->common_model->delete_whereclause('ai_cronjob', $whereclause);
        }else if (((int)$total_task==(int)$total_task_completed) AND
            ((int)$total_task==(int)$total_xls_completed) AND
            ((int)$total_task==(int)$total_xlsi_completed)){
            $whereclause = "company_id='".$_company_id."' AND assessment_id='".$_assessment_id."'";
            $this->common_model->delete_whereclause('ai_cronjob', $whereclause);
            $output = json_decode('{"success": "true", "message": ""}');
        }else{
            $output = json_decode('{"success": "false", "message": ""}');
        }
        echo json_encode($output);       
    }
    public function fetch_statistics(){
        $_company_id            = $this->input->post('company_id', true);
        $_statistics_start_date = $this->input->post('statistics_start_date', true);
        $_statistics_end_date   = $this->input->post('statistics_end_date', true);
        $box_i_statistics       = 0;
        $box_ii_statistics      = 0;
        $box_iii_statistics     = 0;
        $box_iv_statistics      = 0;
        $box_v_statistics       = 0;
        $box_vi_statistics      = 0;

        $box_i_result =$this->ai_schedule_model->get_box_i_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_i_result) AND count((array)$box_i_result)>0){
            $box_i_statistics = $box_i_result->total;
        }
        $box_vi_result = $this->ai_schedule_model->get_box_vi_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_vi_result) AND count((array)$box_vi_result)>0){
            $box_vi_statistics = $box_vi_result->questions;
        }
        
        $box_ii_result =$this->ai_schedule_model->get_box_ii_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_ii_result) AND count((array)$box_ii_result)>0){
            $box_ii_statistics = $box_ii_result->total;
        }
        
        $box_iii_result =$this->ai_schedule_model->get_box_iii_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_iii_result) AND count((array)$box_iii_result)>0){
            $box_iii_statistics = $box_iii_result->completed."/".$box_iii_result->played;
        }
        
        $box_iv_result =$this->ai_schedule_model->get_box_iv_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_iv_result) AND count((array)$box_iv_result)>0){
            $box_iv_statistics = $box_iv_result->total;
        }
       
        $box_v_result =$this->ai_schedule_model->get_box_v_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_v_result) AND count((array)$box_v_result)>0){
            $box_v_statistics = $box_v_result->total;
        }

        $output = json_decode('{"success": "true", "box_i_statistics":"'.$box_i_statistics.'","box_vi_statistics":"'.$box_vi_statistics.'","box_ii_statistics":"'.$box_ii_statistics.'","box_iii_statistics":"'.$box_iii_statistics.'","box_iv_statistics":"'.$box_iv_statistics.'","box_v_statistics":"'.$box_v_statistics.'"}');
        echo json_encode($output);
    }
}