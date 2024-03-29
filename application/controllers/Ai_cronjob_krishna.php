<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ai_cronjob_krishna extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('common_model');
        $this->load->model('ai_cronjob_model');
    }
    public function index(){
        echo "Welcome to the Awarathon";
        exit;
    }
    public function cronjob_delete(){
        $cvids_result = $this->ai_cronjob_model->get_completed_video();
        if (isset($cvids_result) AND count((array)$cvids_result)>0){
            foreach($cvids_result as $cvidsdata){
                $completed_task_id = $cvidsdata->task_id;
                try {
                    $pyc_output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/delete_task.py --task_id='".$completed_task_id."' 2>&1"));
                    $pyc_output = json_decode($pyc_output);
                    
                    if (isset($pyc_output) AND isset($pyc_output->success) AND $pyc_output->success=="true" AND isset($pyc_output->id)){
                        $update_data = array('schedule_by' => 1);
                        $this->common_model->update('ai_schedule', 'task_id', $completed_task_id, $update_data);
                    }else{
                        $update_data = array('schedule_by' => 1);
                        $this->common_model->update('ai_schedule', 'task_id', $completed_task_id, $update_data);
                    }
                }catch(Exception $e) {
                    $update_data = array('schedule_by' => 1);
                    $this->common_model->update('ai_schedule', 'task_id', $completed_task_id, $update_data);
                } 
            }
        }
    }
    public function cronjob_failed(){
        $fvids_result = $this->ai_cronjob_model->get_failed_video();
        if (isset($fvids_result) AND count((array)$fvids_result)>0){
            foreach($fvids_result as $fvidsdata){
                $failed_task_id  = $fvidsdata->task_id;
                $company_id      = $fvidsdata->company_id;
                $assessment_id   = $fvidsdata->assessment_id;
                $user_id         = $fvidsdata->user_id;
                $trans_id        = $fvidsdata->trans_id;
                $question_id     = $fvidsdata->question_id;
                try {
                    $py_output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/task_error_details.py --task_id='".$failed_task_id."' 2>&1"));
                    $_output_failed = print_r($py_output, true);

                    echo "<pre>";
                    echo  "<b>".$failed_task_id."</b><br>";
                    print_r($_output_failed);
                    echo "<br>";

                    // $update_data = array(
                    //     'task_failed_message' => json_encode($_output_failed),
                    // );
                    // $this->common_model->update('ai_schedule', 'task_id', $fvidsdata->task_id, $update_data);
                }catch(Exception $e) {
                } 
            }
        }
    }
    public function cronjob_reschedule(){
        $fvids_result = $this->ai_cronjob_model->get_failed_video();
        if (isset($fvids_result) AND count((array)$fvids_result)>0){
            foreach($fvids_result as $fvidsdata){
                $failed_task_id  = $fvidsdata->task_id;
                $company_id      = $fvidsdata->company_id;
                $assessment_id   = $fvidsdata->assessment_id;
                $user_id         = $fvidsdata->user_id;
                $trans_id        = $fvidsdata->trans_id;
                $question_id     = $fvidsdata->question_id;
                $question_series = $fvidsdata->question_series;
                try {
                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/reschedule_task.py --task_id='".$failed_task_id."' 2>&1"));
                    $output = json_decode($output);
                    
                    if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                        echo "Reschedule ". $failed_task_id. " | ". $output->id. "<br>";
                        $now         = date('Y-m-d H:i:s');
                        $task_result = $this->common_model->get_value('ai_schedule', 'id,task_id,task_status', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
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
                                'schedule_by'       => 0,
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
                                'schedule_by'       => 0,
                                'schedule_datetime' => $now
                            );
                            $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                        }
                    }else{
                        echo "Reschedule Failed". $failed_task_id."<br>";
                    }
                }catch(Exception $e) {
                } 
            }
        }
        echo "Done";
    }

    public function krishna_test(){
        $portal_name = 'aidemo_new';
        $assessment_name = '2way test';
        $user_name_id = 'Apoorva Rathi 108';
        $question_series = 'Q1';
        $db_task_id = '63668711-172e-42ff-9c8c-70bd51e8c71e';

        //check download file issue
        $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/download_file.py --domain_name='".$portal_name."' --assessment_name='".$assessment_name."' --task_id='".$db_task_id."' 2>&1"));
        $output = json_decode($output);
        print_r($output);
        die('here');

        // {"portal_name":"aidemo_new","assessment_name":"2way test","person_name":"Apoorva Rathi 108","question_number":"Q1"}
        try {
            $json   = '{"portal_name":"'.$portal_name.'","assessment_name":"'.$assessment_name.'","person_name":"'.$user_name_id.'","question_number":"'.$question_series.'"}';
            $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/schedule_task.py --data='".$json."' 2>&1"));
            $output = json_decode($output);
            // print_r($output);
            // die('here');
            if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                die('in if');
                $task_id_result = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                $now = date('Y-m-d H:i:s');
                if (isset($task_id_result) AND count((array)$task_id_result)>0){
                    $update_data = array(
                        'py_parameter'      => $json,
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
                        'schedule_by'       => 0,
                        'schedule_datetime' => $now
                    );
                    $this->common_model->update('ai_schedule', 'id', $task_id_result->id, $update_data);
                }else{
                    $post_data = array(
                        'company_id'        => $company_id,
                        'assessment_id'     => $assessment_id,
                        'user_id'           => $user_id,
                        'trans_id'          => $trans_id,
                        'question_id'       => $question_id,
                        'question_series'   => $question_series,
                        'py_parameter'      => $json,
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
                        'schedule_by'       => 0,
                        'schedule_datetime' => $now
                    );
                    $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                }
            }else{
                $task_id_result = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                $now = date('Y-m-d H:i:s');
                if (isset($task_id_result) AND count((array)$task_id_result)>0){
                    $update_data = array(
                        'py_parameter'      => $json,
                        'task_id'           => "FAILED",
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
                        'schedule_by'       => 0,
                        'schedule_datetime' => $now
                    );
                    $this->common_model->update('ai_schedule', 'id', $task_id_result->id, $update_data);
                }else{
                    $post_data = array(
                        'company_id'        => $company_id,
                        'assessment_id'     => $assessment_id,
                        'user_id'           => $user_id,
                        'trans_id'          => $trans_id,
                        'question_id'       => $question_id,
                        'question_series'   => $question_series,
                        'py_parameter'      => $json,
                        'task_id'           => "FAILED",
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
                        'schedule_by'       => 0,
                        'schedule_datetime' => $now
                    );
                    $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                }
            }
        }catch(Exception $e) {
            $task_id_result = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
            $now = date('Y-m-d H:i:s');
            if (isset($task_id_result) AND count((array)$task_id_result)>0){
                $update_data = array(
                    'py_parameter'      => $json,
                    'task_id'           => "FAILED",
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
                    'schedule_by'       => 0,
                    'schedule_datetime' => $now
                );
                $this->common_model->update('ai_schedule', 'id', $task_id_result->id, $update_data);
            }else{
                $post_data = array(
                    'company_id'        => $company_id,
                    'assessment_id'     => $assessment_id,
                    'user_id'           => $user_id,
                    'trans_id'          => $trans_id,
                    'question_id'       => $question_id,
                    'question_series'   => $question_series,
                    'py_parameter'      => $json,
                    'task_id'           => "FAILED",
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
                    'schedule_by'       => 0,
                    'schedule_datetime' => $now
                );
                $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
            }
        }
        die('here');
    }
    public function cronjob(){
        error_reporting(0);

        //GET ALL ASSESSMENTS
        // $all_assessment_result = $this->ai_cronjob_model->get_assessment();
        // if (isset($all_assessment_result) AND count((array)$all_assessment_result)>0){
        //     foreach($all_assessment_result as $allassdata){
        //         $temp_assessment_id = $allassdata->id;
        //         $temp_company_id    = $allassdata->company_id;

        //         //GET ASSESSMENT WISE USERS PLAYED
        //         $ass_users_result = $this->ai_cronjob_model->get_assessment_users($temp_company_id,$temp_assessment_id);
        //         if (isset($ass_users_result) AND count((array)$ass_users_result)>0){
        //             foreach($ass_users_result as $userdata){
        //                 $temp_company_id      = $userdata->company_id;
        //                 $temp_assessment_id   = $userdata->assessment_id;
        //                 $temp_user_id         = $userdata->user_id;
        //                 $temp_trans_id        = $userdata->trans_id;
        //                 $temp_question_id     = $userdata->question_id;
        //                 $temp_question_series = $userdata->question_series;

        //                 $temp_task_id = $this->common_model->get_value('ai_schedule', 'task_id,task_status', 'company_id="'.$temp_company_id.'" AND assessment_id="'.$temp_assessment_id.'" AND user_id="'.$temp_user_id.'" AND trans_id="'.$temp_trans_id.'" AND question_id="'.$temp_question_id.'"');
        //                 if (isset($temp_task_id) AND count((array)$temp_task_id)>0){
        //                 }else{
        //                     //INSERT BLANK RECORD.
        //                     $now = date('Y-m-d H:i:s');
        //                     $post_data = array(
        //                         'company_id'        => $temp_company_id,
        //                         'assessment_id'     => $temp_assessment_id,
        //                         'user_id'           => $temp_user_id,
        //                         'trans_id'          => $temp_trans_id,
        //                         'question_id'       => $temp_question_id,
        //                         'question_series'   => $temp_question_series,
        //                         'py_parameter'      => "",
        //                         'task_id'           => "",
        //                         'task_status'       => 0,
        //                         'xls_generated'     => 0,
        //                         'xls_filename'      => '',
        //                         'xls_imported'      => 0,
        //                         'pdf_generated'     => 0,
        //                         'pdf_filename'      => '',
        //                         'mpdf_generated'    => 0,
        //                         'mpdf_filename'     => '',
        //                         'cpdf_generated'    => 0,
        //                         'cpdf_filename'     => '',
        //                         'schedule_by'       => 0,
        //                         'schedule_datetime' => $now
        //                     );
        //                     $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
        //                 }
        //             }
        //         }
        //     }
        // }

        $assessment_result = $this->ai_cronjob_model->get_schedule();
        if (isset($assessment_result) AND count((array)$assessment_result)>0){
            foreach($assessment_result as $adata){
                $_process_started = 0;
                // $_process_started = (int)$adata->process_started;
                if ($_process_started==0 OR $_process_started=="0"){
                    //UPDATE CRONJOB STATUS = ACTIVE
                    // $cronjob_status = $this->ai_cronjob_model->cronjob_status(1);

                    $task_result = $this->ai_cronjob_model->get_task();
                    if (isset($task_result) AND count((array)$task_result)>0){
                        foreach($task_result as $tdata){
                            $company_id      = $tdata->company_id;
                            $assessment_id   = $tdata->assessment_id;
                            $portal_name     = $tdata->portal_name;
                            $assessment_name = $tdata->assessment;
                            $user_id         = $tdata->user_id;
                            $user_name       = $tdata->user_name;
                            $user_name_id    = $tdata->user_name_id;
                            $trans_id        = $tdata->trans_id;
                            $question_id     = $tdata->question_id;
                            $question_series = $tdata->question_series;
                            $db_task_id      = $tdata->task_id;
                            $task_status     = (int)$tdata->task_status;
                            $xls_generated   = (int)$tdata->xls_generated;
                            $xls_filename    = $tdata->xls_filename;
                            $xls_imported    = (int)$tdata->xls_imported;
                            echo "<br/>$tdata->id - $db_task_id";
                            if ($db_task_id==""){
                                echo "in";
                                print_r($tdata);
                                
                                try {
                                    $json   = '{"portal_name":"'.$portal_name.'","assessment_name":"'.$assessment_name.'","person_name":"'.$user_name_id.'","question_number":"'.$question_series.'"}';
                                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/schedule_task.py --data='".$json."' 2>&1"));
                                    $output = json_decode($output);
                                    if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                                        $task_id_result = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                                        $now = date('Y-m-d H:i:s');
                                        if (isset($task_id_result) AND count((array)$task_id_result)>0){
                                            $update_data = array(
                                                'py_parameter'      => $json,
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
                                                'schedule_by'       => 0,
                                                'schedule_datetime' => $now
                                            );
                                            $this->common_model->update('ai_schedule', 'id', $task_id_result->id, $update_data);
                                        }else{
                                            $post_data = array(
                                                'company_id'        => $company_id,
                                                'assessment_id'     => $assessment_id,
                                                'user_id'           => $user_id,
                                                'trans_id'          => $trans_id,
                                                'question_id'       => $question_id,
                                                'question_series'   => $question_series,
                                                'py_parameter'      => $json,
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
                                                'schedule_by'       => 0,
                                                'schedule_datetime' => $now
                                            );
                                            $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                                        }
                                    }else{
                                        $task_id_result = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                                        $now = date('Y-m-d H:i:s');
                                        if (isset($task_id_result) AND count((array)$task_id_result)>0){
                                            $update_data = array(
                                                'py_parameter'      => $json,
                                                'task_id'           => "FAILED",
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
                                                'schedule_by'       => 0,
                                                'schedule_datetime' => $now
                                            );
                                            $this->common_model->update('ai_schedule', 'id', $task_id_result->id, $update_data);
                                        }else{
                                            $post_data = array(
                                                'company_id'        => $company_id,
                                                'assessment_id'     => $assessment_id,
                                                'user_id'           => $user_id,
                                                'trans_id'          => $trans_id,
                                                'question_id'       => $question_id,
                                                'question_series'   => $question_series,
                                                'py_parameter'      => $json,
                                                'task_id'           => "FAILED",
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
                                                'schedule_by'       => 0,
                                                'schedule_datetime' => $now
                                            );
                                            $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                                        }
                                    }
                                }catch(Exception $e) {
                                    $task_id_result = $this->common_model->get_value('ai_schedule', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                                    $now = date('Y-m-d H:i:s');
                                    if (isset($task_id_result) AND count((array)$task_id_result)>0){
                                        $update_data = array(
                                            'py_parameter'      => $json,
                                            'task_id'           => "FAILED",
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
                                            'schedule_by'       => 0,
                                            'schedule_datetime' => $now
                                        );
                                        $this->common_model->update('ai_schedule', 'id', $task_id_result->id, $update_data);
                                    }else{
                                        $post_data = array(
                                            'company_id'        => $company_id,
                                            'assessment_id'     => $assessment_id,
                                            'user_id'           => $user_id,
                                            'trans_id'          => $trans_id,
                                            'question_id'       => $question_id,
                                            'question_series'   => $question_series,
                                            'py_parameter'      => $json,
                                            'task_id'           => "FAILED",
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
                                            'schedule_by'       => 0,
                                            'schedule_datetime' => $now
                                        );
                                        $ai_schedule_id = $this->common_model->insert('ai_schedule', $post_data);
                                    }
                                }
                            }
                            // die('1');
                            if (($task_status==0 OR ($task_status!==1 AND $task_status!==4)) AND  $company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
                                try {
                                    $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/task_status.py --task_id='".$db_task_id."' 2>&1"));
                                    $output = json_decode($output);
                                    if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->id)){
                                        $update_status = $this->ai_cronjob_model->update_status(1,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                    }else if (isset($output) AND isset($output->success) AND $output->success=="false"){
                                        if ($output->message=="Active"){
                                            $update_status = $this->ai_cronjob_model->update_status(2,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                        }else if ($output->message=="Running"){
                                            $update_status = $this->ai_cronjob_model->update_status(3,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                        }else if ($output->message=="Failed"){
                                            $update_status = $this->ai_cronjob_model->update_status(4,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                            // try {
                                            //     $output_failed  = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/task_error_details.py --task_id='".$db_task_id ."' 2>&1"));
                                            //     $_output_failed = print_r($output_failed, true);
                                            //     $update_status_failed = $this->ai_cronjob_model->update_status_failed_message(json_encode($_output_failed),$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                            // }catch(Exception $e) {
                                            //     // $update_status = $this->ai_cronjob_model->update_status(0,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                            // }
                                        }else{
                                            $update_status = $this->ai_cronjob_model->update_status(5,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                        }
                                    }else{
                                        $update_status = $this->ai_cronjob_model->update_status(0,$company_id,$assessment_id,$user_id,$trans_id,$question_id);    
                                    }
                                }catch(Exception $e) {
                                    $update_status = $this->ai_cronjob_model->update_status(0,$company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                }                            
                            }
                            if ($task_status==1 AND $xls_generated!==1){
                                if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
                                    try {
                                        $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/download_file.py --domain_name='".$portal_name."' --assessment_name='".$assessment_name."' --task_id='".$db_task_id."' 2>&1"));
                                        $output = json_decode($output);
                                        if (isset($output) AND isset($output->success) AND $output->success=="true" AND isset($output->file_name)){
                                            $update_status = $this->ai_cronjob_model->update_xls_status(1,$company_id,$assessment_id,$user_id,$trans_id,$question_id,$output->file_name);
                                        }else{
                                            $update_status = $this->ai_cronjob_model->update_xls_status(2,$company_id,$assessment_id,$user_id,$trans_id,$question_id,"");
                                        }
                                    }catch(Exception $e) {
                                        $update_status = $this->ai_cronjob_model->update_xls_status(2,$company_id,$assessment_id,$user_id,$trans_id,$question_id,"");
                                    }
                                }
                            }
                            if ($xls_generated==1 AND $xls_imported!==1){
                                if ($company_id!="" AND $assessment_id!="" AND $user_id!="" AND $trans_id!="" AND $question_id!=""){
                                    $file_name          = $xls_filename;
                                    $absolute_file_path = '/var/www/html/awarathon.com/demo/'.$file_name;
                                    $temp_file_path     = $file_name;

                                    if (file_exists($temp_file_path)==TRUE){
                                        $this->load->library('PHPExcel_CI');
                                        $objPHPExcel           = PHPExcel_IOFactory::load($absolute_file_path);
                                        $objPHPExcel->setActiveSheetIndex(0);
                                        $worksheet             = $objPHPExcel->getActiveSheet();
                                        $worksheet_max_row     = $worksheet->getHighestRow();
                                        $worksheet_max_col     = $worksheet->getHighestColumn();
                                        $worksheet_max_col_idx = PHPExcel_Cell::columnIndexFromString($worksheet_max_col);
                                        
                                        if ($worksheet_max_row <= 1) {
                                        }else if ($worksheet_max_col_idx < 6) {
                                        }else{
                                            for ($row = 2; $row <= $worksheet_max_row; $row++) {
                                                $participant_name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                                                $question_no      = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                                                $parameter_type   = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                                                $parameter        = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
                                                $parameter        = trim(str_replace("\t", '', $parameter));// remove tabs
                                                $parameter        = trim(str_replace("\n", '', $parameter));// remove new lines
                                                $parameter        = trim(str_replace("\r", '', $parameter));// remove carriage returns
                                                $parameter_label  = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());
                                                $parameter_label  = trim(str_replace("\t", '', $parameter_label));// remove tabs
                                                $parameter_label  = trim(str_replace("\n", '', $parameter_label));// remove new lines
                                                $parameter_label  = trim(str_replace("\r", '', $parameter_label));// remove carriage returns
                                                $score            = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                                                $rating           = number_format(($score*5)/100,2);
                    
                                                $parameter_weight = 0;
                                                $parameter_id       = 0;
                                                $sub_parameter_id   = 0;
                                                $parameter_label_id = 0;
                                                if ($parameter_type=="parameter"){
                                                    $parameter_result = $this->common_model->get_value('parameter_mst', '*', 'status=1 AND company_id="'.$company_id.'" AND description="'.$parameter.'"');
                                                    if (isset($parameter_result) AND count((array)$parameter_result)>0){
                                                        $parameter_id = (float)$parameter_result->id;
                                                        $sub_parameter_id = 0;
                                                    }
                                                    if ($parameter_id!=0){
                                                        $parameter_label_result = $this->common_model->get_value('parameter_label_mst', '*', 'status=1 AND parameter_id="'.$parameter_id.'" AND description="'.$parameter_label.'"');
                                                        if (isset($parameter_label_result) AND count((array)$parameter_label_result)>0){
                                                            $parameter_label_id = (float)$parameter_label_result->id;
                                                        }
                                                    }
                                                    $parameter_weight_result = $this->common_model->get_value('assessment_trans_sparam', 'SUM(parameter_weight) as parameter_weight', 'assessment_id="'.$assessment_id.'" AND question_id="'.$question_id.'" AND parameter_id="'.$parameter_id.'"');
                                                    if (isset($parameter_weight_result) AND count((array)$parameter_weight_result)>0){
                                                        $parameter_weight = (float)$parameter_weight_result->parameter_weight;
                                                    }
                                                } 
                                                if ($parameter_type=="subparameter"){
                                                    $parameter_result = $this->common_model->get_value('parameter_mst', '*', 'status=1 AND company_id="'.$company_id.'" AND description="'.$parameter.'"');
                                                    if (isset($parameter_result) AND count((array)$parameter_result)>0){
                                                        $parameter_id = (float)$parameter_result->id;
                                                    }
                    
                                                    $sub_parameter_result = $this->common_model->get_value('subparameter_mst', '*', 'status=1 AND parameter_id="'.$parameter_id.'" AND description="'.$parameter_label.'"');
                                                    if (isset($sub_parameter_result) AND count((array)$sub_parameter_result)>0){
                                                        $parameter_id     = (float)$sub_parameter_result->parameter_id;
                                                        $sub_parameter_id = (float)$sub_parameter_result->id;
                                                    }
                                                    $parameter_weight_result = $this->common_model->get_value('assessment_trans_sparam', 'parameter_weight', 'assessment_id="'.$assessment_id.'" AND question_id="'.$question_id.'" AND parameter_id="'.$parameter_id.'" AND sub_parameter_id="'.$sub_parameter_id.'"');
                                                    if (isset($parameter_weight_result) AND count((array)$parameter_weight_result)>0){
                                                        $parameter_weight = (float)$parameter_weight_result->parameter_weight;
                                                    }
                                                }
                                                $weighted_score = number_format($score*($parameter_weight/100),2);

                                                $now = date('Y-m-d H:i:s');
                                                $score_result = $this->common_model->get_value('ai_subparameter_score', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'" AND parameter_type="'.$parameter_type.'" AND parameter_id="'.$parameter_id.'" AND sub_parameter_id="'.$sub_parameter_id.'"');
                                                if (isset($score_result) AND count((array)$score_result)>0){
                                                    $ai_subparameter_score_id = (float)$score_result->id;
                                                    $update_data = array(
                                                        'score'           => $score,
                                                        'weighted_score'  => $weighted_score,
                                                        'rating'          => $rating,
                                                        'import_dttm'     => $now
                                                    );
                                                    $this->common_model->update('ai_subparameter_score', 'id', $ai_subparameter_score_id, $update_data);
                                                }else{
                                                    $import_data = array(
                                                        'company_id'         => $company_id,
                                                        'assessment_id'      => $assessment_id,
                                                        'user_id'            => $user_id,
                                                        'trans_id'           => $trans_id,
                                                        'question_id'        => $question_id,
                                                        'question_series'    => $question_series,
                                                        'parameter_type'     => $parameter_type,
                                                        'parameter_id'       => $parameter_id,
                                                        'sub_parameter_id'   => $sub_parameter_id,
                                                        'parameter_label_id' => $parameter_label_id,
                                                        'score'              => $score,
                                                        'weighted_score'     => $weighted_score,
                                                        'rating'             => $rating,
                                                        'import_dttm'        => $now
                                                    );
                                                    $ai_subparameter_score_id = $this->common_model->insert('ai_subparameter_score', $import_data);
                                                }
                                            }
                                            //IMPORT SENTANCE/KEYWORD SCORE
                                            $objPHPExcel->setActiveSheetIndex(3);
                                            $worksheet              = $objPHPExcel->getActiveSheet();
                                            $worksheet_max_rowi     = $worksheet->getHighestRow();
                                            $worksheet_max_coli     = $worksheet->getHighestColumn();
                                            $worksheet_max_col_idxi = PHPExcel_Cell::columnIndexFromString($worksheet_max_coli);
                    
                                            $whereclausei = "company_id='".$company_id."' AND assessment_id='".$assessment_id."' AND user_id='".$user_id."' AND trans_id='".$trans_id."' AND question_id='".$question_id."'";
                                            $this->common_model->delete_whereclause('ai_sentkey_score', $whereclausei);
                    
                                            for ($rowi = 2; $rowi <= $worksheet_max_rowi; $rowi++) {
                                                $sk_score         = $worksheet->getCellByColumnAndRow(3, $rowi)->getValue();
                                                $sentance_keyword = $worksheet->getCellByColumnAndRow(4, $rowi)->getValue();
                                                $now              = date('Y-m-d H:i:s');
                    
                                                $import_datai = array(
                                                    'company_id'       => $company_id,
                                                    'assessment_id'    => $assessment_id,
                                                    'user_id'          => $user_id,
                                                    'trans_id'         => $trans_id,
                                                    'question_id'      => $question_id,
                                                    'question_series'  => $question_series,
                                                    'sentance_keyword' => $sentance_keyword,
                                                    'score'            => $sk_score,
                                                    'import_dttm'      => $now
                                                );
                                                $ai_sentkey_score_id = $this->common_model->insert('ai_sentkey_score', $import_datai);
                                            }
                    
                                            $xls_import_status = $this->ai_cronjob_model->update_xls_import_status($company_id,$assessment_id,$user_id,$trans_id,$question_id);
                                            if ($xls_import_status){
                                                unset($objPHPExcel);
                                                if (copy($temp_file_path,'output_excel/'.$temp_file_path)){
                                                    unlink($temp_file_path);
                                                }
                                            }
                                        }
                                    }
                                }    
                            }
                        }  
                        die('1');

                        //CHECK ALL TASK COMPLETED?
                        $total_task           = 0;
                        $total_task_completed = 0;
                        $total_task_failed    = 0;
                        $total_xls_completed  = 0;
                        $total_xlsi_completed = 0;

                        $_tasks_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', '1=1');
                        if (isset($_tasks_results) AND count((array)$_tasks_results)>0){
                            $total_task = $_tasks_results->total;
                        }

                        $_taskfailed_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="4"');
                        if (isset($_taskfailed_results) AND count((array)$_taskfailed_results)>0){
                            $total_task_failed = $_taskfailed_results->total;
                        }
                        
                        $_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1"');
                        if (isset($_tasksc_results) AND count((array)$_tasksc_results)>0){
                            $total_task_completed = $_tasksc_results->total;
                        }
                        
                        $_xls_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!=""');
                        if (isset($_xls_results) AND count((array)$_xls_results)>0){
                            $total_xls_completed = $_xls_results->total;
                        }

                        $_xlsi_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1"');
                        if (isset($_xlsi_results) AND count((array)$_xlsi_results)>0){
                            $total_xlsi_completed = $_xlsi_results->total;
                        }

                        if ((int)$total_task==(int)$total_task_failed){
                            // $whereclause = "1=1";
                            // $this->common_model->delete_whereclause('ai_cronjob', $whereclause);
                             $cronjob_status = $this->ai_cronjob_model->cronjob_status(0);
                        }else if (((int)$total_task==(int)$total_task_completed) AND
                            ((int)$total_task==(int)$total_xls_completed) AND
                            ((int)$total_task==(int)$total_xlsi_completed)){
                            $cronjob_status = $this->ai_cronjob_model->cronjob_status(0);
                            // $whereclause = "1=1";
                            // $this->common_model->delete_whereclause('ai_cronjob', $whereclause);
                        }else{
                            //UPDATE CRONJOB STATUS = RE-RUN BECOZ IT IS NOT COMPLETED.
                            $cronjob_status = $this->ai_cronjob_model->cronjob_status(0);
                        }
                    }else{
                        $cronjob_status = $this->ai_cronjob_model->cronjob_status(0);
                    }
                }
            }
        }
    }
}