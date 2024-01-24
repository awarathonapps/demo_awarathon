<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Create_trinity extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('create_trinity');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('create_trinity_model');
    }

    public function index()
    {
        $data['module_id'] = '90';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        $this->load->view('create_trinity/index', $data);
    }

    public function create($errors = "")
    {
        $data['module_id'] = '90';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('create_trinity');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['errors'] = $errors;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');
        $data['map_script'] = $this->common_model->get_selected_values('script_mst', 'id,script_title,script,situation', '1=1');
        $data['persona'] = $this->common_model->get_selected_values('trinity_persona', 'id,persona_name,persona_image', '1=1');

        $this->session->unset_userdata('NewSupervisorsArrray_session');
        $this->session->unset_userdata('NewManagersArrray_session');
        $this->load->view('create_trinity/create', $data);
    }

    // public function get_question_title()
    // {
    //     $question_id = $this->input->post('question_id');
    //     $Question_set = $this->common_model->get_value('assessment_question', 'question', 'id=' . $question_id);
    //     $data['lchtml'] = $Question_set->question;
    //     echo json_encode($data);
    // }

    public function submit($Copy_id = '')
    {
        if ($Copy_id != "") {
            $Copy_id = base64_decode($Copy_id);
        }
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;

        if ($Copy_id != "") {
            $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'id', 'assessment_id=' . $Copy_id);
            $LockFlag = (count((array)$ISEXIST) > 0 ? 1 : 0);
            $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $Copy_id);
            $edit_lockflag = (count((array)$isPlay2) > 0 ? 1 : 0);
        }
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            // $New_question_idArray = $this->input->post('New_question_id');
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            // if ($Copy_id != "") {
            // $Old_question_idArray = $this->input->post('Old_question_id');
            // if (count((array)$New_question_idArray) > 0) {
            //     if (isset($Old_question_idArray)) {
            //         $AlreayExist = array_intersect($Old_question_idArray, $New_question_idArray);
            //         if (count((array)$AlreayExist) > 0) {
            //             $Message .= "Duplicate Questions Found..!<br/>";
            //             $SuccessFlag = 0;
            //         }
            //     }
            //     $Nduplicate = array_diff_assoc($New_question_idArray, array_unique($New_question_idArray));
            //     if (count((array)$Nduplicate) > 0) {
            //         $Message .= "Duplicate Questions Found..!!<br/>";
            //         $SuccessFlag = 0;
            //     }
            //     foreach ($New_question_idArray as $key => $question_id) {
            //         $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
            //         $old_data = $this->common_model->get_value('assessment_trans', 'id', 'assessment_id=' . $Copy_id . ' AND question_id=' . $question_id);
            //         if (count((array)$old_data) > 0) {
            //             $Message .= "Duplicate Questions Found..!!<br/>";
            //             $SuccessFlag = 0;
            //         }
            //         if (!isset($New_parameter_idArray)) {
            //             $Message .= "Please Select Parameter!!!!.<br/>";
            //             $SuccessFlag = 0;
            //             break;
            //         }
            //     }
            // }
            // if (count((array)$Old_question_idArray) > 0) {
            //     $Oduplicate = array_diff_assoc($Old_question_idArray, array_unique($Old_question_idArray));
            //     if (count((array)$Oduplicate) > 0) {
            //         $Message .= "Duplicate Questions Found..!";
            //         $SuccessFlag = 0;
            //     }
            //     foreach ($Old_question_idArray as $key => $question_id) {
            //         $Old_parameters = $this->input->post('Old_parameter_id' . $key);
            //         if (count((array)$Old_parameters) == 0) {
            //             $Message .= "Please Select Parameter!<br/>";
            //             $SuccessFlag = 0;
            //             break;
            //         }
            //     }
            // }
            // } else {
            //     if (count((array)$New_question_idArray) > 0) {
            //         $duplicate = array_diff_assoc($New_question_idArray, array_unique($New_question_idArray));
            //         if (count((array)$duplicate) > 0) {
            //             $Message .= "Duplicate questions Found..!<br/>";
            //             $SuccessFlag = 0;
            //         }
            //         foreach ($New_question_idArray as $key => $v) {
            //             $tmp = $this->input->post('New_parameter_id' . $key);
            //             if (count((array)$tmp) == 0) {
            //                 $Message .= "Please Select Parameter!<br/>";
            //                 $SuccessFlag = 0;
            //                 break;
            //             }
            //         }
            //     }
            // }

            // $this->form_validation->set_rules('assessment_type', 'Assessment Type', 'required');
            $this->form_validation->set_rules('assessment_name', 'Assessment Name', 'required');
            $this->form_validation->set_rules('number_attempts', 'Number attempts', 'required');
            $this->form_validation->set_rules('time_limit', 'Time Limit', 'required');
            $this->form_validation->set_rules('instruction', 'instruction', 'required');
            // $this->form_validation->set_rules('ratingstyle', 'Rating Type', 'required');
            // $this->form_validation->set_rules('question_type', 'Question Type', 'required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            // $this->form_validation->set_rules('assessor_date', 'Assesser Date', 'required');

            // if ($this->input->post('isweights') == 1) {
            //     $this->form_validation->set_rules('weight[]', 'Weight', 'required');
            // }


            // $sub_parameter_result = json_decode($this->input->post('sub_parameter'),TRUE); 
            // $sub_parameter_result = $this->input->post('sub_parameter');
            // if (isset($sub_parameter_result) and count((array)$sub_parameter_result) <= 0) {
            //     $Message .= "Please map the parameters and sub-parameters to the question.<br/>";
            //     $SuccessFlag = 0;
            // }
            // if (isset($sub_parameter_result) and count((array)$sub_parameter_result) > 0) {
            //     foreach ($sub_parameter_result as $sparam) {
            //         $txn_id           = $sparam['txn_id'];
            //         $language_id      = $this->input->post('language_id' . $txn_id);
            //         if ($language_id == '') {
            //             $Message = "Please map the language to the question.<br/>";
            //             $SuccessFlag = 0;
            //         }
            //     }
            // }
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $start_date = strtotime($this->input->post('start_date'));
                $end_date = strtotime($this->input->post('end_date'));
                $assessor_date = strtotime($this->input->post('assessor_date'));
                $time_limit = $this->input->post('time_limit');

                if ($start_date < strtotime(date('Y-m-d H:i:s'))) {
                    $Message .= "Start date can not be less than todays date..";
                    $SuccessFlag = 0;
                }
                if ($start_date > $end_date) {
                    $Message .= "Start date cannot be more than end date..<br/>";
                    $SuccessFlag = 0;
                }
                // elseif ($assessor_date < $end_date) {
                //     $Message .= "Assessor last date cannot be less than End date..<br/>";
                //     $SuccessFlag = 0;
                // 
                if ($assessor_date != '') {
                    if ($assessor_date < $end_date) {
                        $Message .= "Assessor last date cannot be less than End date..<br/>";
                        $SuccessFlag = 0;
                    }
                }
                if ($time_limit > 300) {
                    $Message .= "Time limit cannot exceed 300 seconds..<br/>";
                    $SuccessFlag = 0;
                }
                // if (isset($Old_question_idArray) && isset($New_question_idArray) && count((array)$Old_question_idArray) == 0 && count((array)$New_question_idArray) == 0) {
                //     $Message = "Please select atleast one question..<br/>";
                //     $SuccessFlag = 0;
                // }
                if ($Copy_id == "") {
                    $NewManagersArrray    = $this->session->userdata('NewManagersArrray_session');
                    $NewSupervisorsArrray = $this->session->userdata('NewSupervisorsArrray_session');
                    // if (!isset($NewManagersArrray) && count((array)$NewManagersArrray) == 0) {
                    //     $Message .= "Please Map Managers..<br/>";
                    //     $SuccessFlag = 0;
                    // }
                    // if (count((array)$NewManagersArrray) > 1) {
                    //     $Message .= "Only one manager can be mapped";
                    //     $SuccessFlag = 0;
                    // }
                }
                if (isset($NewManagersArrray) && count((array)$NewManagersArrray) > 0) {
                    if ($assessor_date == '') {
                        $Message .= "Please Select Assessor Date...!!!<br/>";
                        $SuccessFlag = 0;
                    }
                } else if ($assessor_date != '') {
                    if (count((array)$NewManagersArrray) == 0) {
                        $Message .= "Please Map Manager...!!<br/>";
                        $SuccessFlag = 0;
                    }
                }

                if ($SuccessFlag) {
                    // if (isset($Old_question_idArray) or isset($New_question_idArray) or count((array)$Old_question_idArray) > 0 or count((array)$New_question_idArray) > 0) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'assessment' => $this->security->xss_clean($this->input->post('assessment_name')),
                        'code' => $this->security->xss_clean($this->input->post('otc')),
                        'is_situation' => $this->input->post('question_type') != null ? $this->security->xss_clean($this->input->post('question_type')) : '0',
                        'number_attempts' => $this->security->xss_clean($this->input->post('number_attempts')),
                        'assessment_type' => !empty($this->input->post('assessment_type')) ? $this->security->xss_clean($this->input->post('assessment_type')) : 1,
                        'report_type' => $this->security->xss_clean($this->input->post('report_type')),
                        'ratingstyle' => $this->security->xss_clean($this->input->post('ratingstyle')),
                        'start_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('start_date'))),
                        'end_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('end_date'))),
                        // 'assessor_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date'))),
                        'instruction' => $this->security->xss_clean($this->input->post('instruction')),
                        'description' => $this->security->xss_clean($this->input->post('description')),
                        'time_limit' => $this->input->post('time_limit'),
                        'is_preview' => ($this->input->post('is_preview') != null) ? 0 : 1,
                        // 'is_preview' => ($this->input->post('is_preview')==1 ? 1 : 0),
                        'ranking' => ($this->input->post('ranking') == 1 ? 1 : 0),
                        'is_weights' => 0,
                        'status' => 0,
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    if (count((array)$NewManagersArrray) > 0 && $assessor_date != '') {
                        $data['assessor_dttm'] = date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date')));
                    }
                    $insert_id = $this->common_model->insert('assessment_mst', $data);
                    if ($insert_id != "") {
                        if (isset($NewManagersArrray) && count((array)$NewManagersArrray) > 0) {
                            foreach ($NewManagersArrray as $user_id) {
                                $ISEXIST = $this->common_model->get_value('assessment_managers', 'id', 'assessment_id=' . $insert_id . ' AND trainer_id=' . $user_id);
                                $Mdata = array(
                                    'trainer_id' => $user_id,
                                    'assessment_id' => $insert_id
                                );
                                if (count((array)$ISEXIST) > 0) {
                                    continue;
                                } else {
                                    $this->common_model->insert('assessment_managers', $Mdata);
                                    // Jagdisha : 30/01/2023
                                    // For mail users
                                    $pattern[0] = '/\[SUBJECT\]/';
                                    $pattern[1] = '/\[ASSESSMENT_NAME\]/';
                                    $pattern[2] = '/\[ASSESSMENT_LINK\]/';
                                    // $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_assessment_alert'");
                                    $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='assessment_created_manger'");
                                    $pattern[3] = '/\[NAME\]/';
                                    $pattern[4] = '/\[DATE_TIME\]/';
                                    $pattern[5] = '/\[NAME\]/';
                                    $assessment_set = $this->common_model->get_value('assessment_mst', 'assessment,assessor_dttm,start_dttm,end_dttm', "id=" . $insert_id);
                                    $SuccessFlag = 1;
                                    $Message = '';
                                    // $AllowSet = $this->common_model->get_users_value('assessment_allow_users', 'user_id', 'assessment_id=' . $assessment_id .' AND send_mail=0');
                                    $AllowSet = $this->common_model->get_users_value('assessment_managers', 'trainer_id', 'assessment_id=' . $insert_id . ' AND send_mail=0');
                                    if (count((array)$AllowSet) > 0) {
                                        $u_id = array();
                                        $subject = $emailTemplate->subject;
                                        $replacement[0] = $subject;
                                        $replacement[1] = $assessment_set->assessment;
                                        foreach ($AllowSet as $id) {
                                            $u_id[] = $id['trainer_id'];

                                            $ManagerSet = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name,email,company_id', "userid=" . $id['trainer_id']);

                                            $replacement[2] = '<a target="_blank" style="display: inline-block;
                                                                    width: 200px;
                                                                    height: 20px;
                                                                    background: #db1f48;
                                                                    padding: 10px;
                                                                    text-align: center;
                                                                    border-radius: 5px;
                                                                    color: white;
                                                                    border: 1px solid black;
                                                                    text-decoration:none;
                                                                    font-weight: bold;" href="' . base_url() . 'assessment/view/' . $insert_id . '/2">View Assignment</a>';
                                            $replacement[3] = $ManagerSet->trainer_name;
                                            $replacement[4] = date("d-m-Y h:i a", strtotime($assessment_set->assessor_dttm));
                                            $replacement[5] = ''; //
                                            $ToName = $ManagerSet->trainer_name;
                                            $email_to = $ManagerSet->email;
                                            // $email_to = 'ranabhautik5@gmail.com';
                                            $Company_id = $ManagerSet->company_id;
                                            $message = $emailTemplate->message;
                                            $body = preg_replace($pattern, $replacement, $message);
                                            $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                                        }
                                    }
                                    if ($ReturnArray['sendflag'] == '1') {
                                        $this->common_model->update_id('assessment_managers', 'trainer_id', $insert_id, $u_id);
                                    }
                                    // Jagdisha End: 30/01/2023
                                }
                            }
                        }
                        if (isset($NewSupervisorsArrray) && count((array)$NewSupervisorsArrray) > 0) {
                            foreach ($NewSupervisorsArrray as $user_id) {
                                $ISEXIST = $this->common_model->get_value('assessment_supervisors', 'id', 'assessment_id=' . $insert_id . ' AND trainer_id=' . $user_id);
                                $Sdata = array(
                                    'trainer_id' => $user_id,
                                    'assessment_id' => $insert_id
                                );
                                if (count((array)$ISEXIST) > 0) {
                                    continue;
                                } else {
                                    $this->common_model->insert('assessment_supervisors', $Sdata);
                                }
                            }
                        }
                        // if ($this->input->post('isweights') == 1) {
                        //     $weight_array = $this->input->post('weight');
                        //     if (count((array)$weight_array) > 0) {
                        //         foreach ($weight_array as $paraid => $weight) {
                        //             $wdata = array(
                        //                 'assessment_id' => $insert_id,
                        //                 'parameter_id' => $paraid,
                        //                 'percentage' => $weight
                        //             );
                        //             $this->common_model->insert('assessment_para_weights', $wdata);
                        //         }
                        //     }
                        // }
                        $Rdata['id'] = base64_encode($insert_id);

                        // Default report rights
                        $reportdata = [
                            'company_id' => $Company_id,
                            'assessment_id' => $insert_id,
                            'show_reports' => 1
                        ];
                        $this->common_model->insert('ai_cronreports', $reportdata);
                        // End

                    } else {
                        $Message = "Error while creating Assessment,Contact administrator for technical support.!";
                        $SuccessFlag = 0;
                    }
                    // } else {
                    //     $Message = "Please add Question first.!";
                    //     $SuccessFlag = 0;
                    // }
                    if ($SuccessFlag) {
                        $Message = "Save Successfully!!!";
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encode_id)
    {
        $id = base64_decode($Encode_id);

        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $id);
        $edit_lockflag = (count((array)$isPlay2) > 0 ? 1 : 0);
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('assessment_name', 'Assessment Name', 'required');
            if (!$edit_lockflag) {
                $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            }
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            // $this->form_validation->set_rules('assessor_date', 'Assesser Date', 'required');
            $this->form_validation->set_rules('number_attempts', 'Number attempts', 'required');
            $this->form_validation->set_rules('time_limit', 'Time Limit', 'required');
            //$this->form_validation->set_rules('otc', 'OTC', 'required');
            $this->form_validation->set_rules('instruction', 'instruction', 'required');

            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $managers_data = $this->common_model->get_value('assessment_managers', 'trainer_id', 'assessment_id=' . $id);
                if (count((array)$managers_data) == 0 && $this->input->post('status') == 1) {
                    $Message = "Please Mapp Managers first..!";
                    $SuccessFlag = 0;
                } else {
                    if (!$edit_lockflag) {
                        $start_date = strtotime($this->input->post('start_date'));
                    } else {
                        $old_data = $this->common_model->get_value('assessment_mst', 'start_dttm', 'id=' . $id);
                        $start_date = $old_data->start_dttm;
                    }
                    $end_date = strtotime($this->input->post('end_date'));
                    // $assessor_date = strtotime($this->input->post('assessor_date'));
                    if ($start_date > $end_date) {
                        $Message .= "Start date cannot be more than end date..<br/>";
                        $SuccessFlag = 0;
                    }
                    // elseif ($assessor_date < $end_date) {
                    //     $Message .= "Assessor last date cannot be less than End date..<br/>";
                    //     $SuccessFlag = 0;
                    // }
                    $assessor_date = strtotime($this->input->post('assessor_date'));
                    if (isset($managers_data) && count((array)$managers_data) > 0) {
                        if ($assessor_date == '') {
                            $Message .= "Please Select Assessor Date...!!!<br/>";
                            $SuccessFlag = 0;
                        }
                    } else if ($assessor_date != '') {
                        if (count((array)$managers_data) == 0) {
                            $Message .= "Please Map Manager...!!<br/>";
                            $SuccessFlag = 0;
                        }
                    }
                    if ($assessor_date != '') {
                        if ($assessor_date < $end_date) {
                            $Message .= "Assessor last date cannot be less than End date..<br/>";
                            $SuccessFlag = 0;
                        }
                    }

                    $time_limit = $this->input->post('time_limit');
                    if ($time_limit > 300) {
                        $Message .= "Time limit cannot exceed 300 seconds..<br/>";
                        $SuccessFlag = 0;
                    }
                    $date = $this->input->post('end_date');
                    $Oed = $this->common_model->get_value('assessment_mst', 'end_dttm', 'id=' . $id);
                    $ed = $Oed->end_dttm;

                    $date1 = date_create($ed);

                    $now = date('Y-m-d H:i:s');
                    if ($SuccessFlag) {
                        $data = array(
                            'assessment'      => $this->security->xss_clean($this->input->post('assessment_name')),
                            'code'            => $this->security->xss_clean($this->input->post('otc')),
                            'number_attempts' => $this->security->xss_clean($this->input->post('number_attempts')),
                            'end_dttm'        => date("Y-m-d H:i:s", strtotime($this->input->post('end_date'))),
                            // 'assessor_dttm'   => date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date'))),
                            'instruction'     => $this->security->xss_clean($this->input->post('instruction')),
                            'description'     => $this->security->xss_clean($this->input->post('description')),
                            'time_limit' => $this->input->post('time_limit'),
                            'is_preview'      => ($this->input->post('is_preview') != null) ? 0 : 1,
                            'ranking'         => ($this->input->post('ranking') == 1 ? 1 : 0),
                            'status'          => $this->security->xss_clean($this->input->post('status')),
                            'modifieddate'    => $now,
                            'modifiedby'      => $this->mw_session['user_id'],
                        );
                        if (count((array)$managers_data) > 0 && $assessor_date != '') {
                            $data['assessor_dttm'] = date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date')));
                        } else {
                            $data['assessor_dttm'] = '';
                        }
                        if (!$edit_lockflag) {
                            $data['start_dttm'] = date("Y-m-d H:i:s", strtotime($this->input->post('start_date')));
                        }
                        $this->common_model->update('assessment_mst', 'id', $id, $data);
                        $date2 = date_create($date);
                        $interval = date_diff($date1, $date2);
                        $date_difference = $interval->format("%a");
                        if ($date_difference > 0) {
                            $pattern[0] = '/\[SUBJECT\]/';
                            $pattern[1] = '/\[ASSESSMENT_NAME\]/';
                            $pattern[2] = '/\[ASSESSMENT_LINK\]/';
                            $emailTemplate_user = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='assessment_date_extension_mail-rep'");
                            $emailTemplate_manager = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='assessment_date_extension_mail-manager'");

                            $pattern[3] = '/\[NAME\]/';
                            $pattern[4] = '/\[DATE_TIME\]/';
                            $pattern[5] = '/\[Client_mail_id\]/';
                            //Mail fucntion for reps(users)
                            $assessment_set = $this->common_model->get_value('assessment_mst', 'assessment,assessor_dttm,start_dttm,end_dttm', "id=" . $id);
                            if (count((array)$emailTemplate_user) > 0) {
                                $subject = $emailTemplate_user->subject;
                                $replacement[0] = $subject;
                                $replacement[1] = $assessment_set->assessment;
                                //  $userSet = $this->common_model->get_users_value('assessment_allow_users', 'user_id', 'assessment_id=' . $id);
                                $userSet = $this->common_model->get_assessment_wise_users('assessment_allow_users', $id);
                                if (count((array)$userSet) > 0) {

                                    foreach ($userSet as $a_id) {
                                        $UserData = $this->common_model->get_value('device_users', 'company_id,concat(firstname," ",lastname) as trainee_name,email', '  user_id =' . $a_id['user_id']);
                                        $ToName = $UserData->trainee_name;
                                        $email_to = $UserData->email;
                                        // $email_to = 'ranabhautik5@gmail.com';
                                        $Company_id = $UserData->company_id;
                                        $replacement[2] = '<a target="_blank" style="display: inline-block;
                                             background: #db1f48;
                                             padding: .45rem 1rem;
                                             box-sizing: border-box;
                                             border: none;
                                             border-radius: 3px;
                                             color: #fff;
                                             text-align: center;
                                             font-family: Lato,Arial,sans-serif;
                                             font-weight: 400;
                                             font-size: 1em;
                                             text-decoration:none;
                                             line-height: initial;" href="https://web.awarathon.com">View Assignment</a>';
                                        $replacement[3] = $UserData->trainee_name;
                                        $replacement[4] = date("d-m-Y h:i a", strtotime($date));
                                        $replacement[5] = 'info@awarathon.com';
                                        $message = $emailTemplate_user->message;
                                        $body = preg_replace($pattern, $replacement, $message);
                                        $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                                    }
                                }
                            }
                            //Mail funtion for managers
                            $mangerSet = $this->common_model->get_users_value('assessment_managers', 'trainer_id', 'assessment_id=' . $id);
                            if (count((array)$emailTemplate_manager) > 0) {
                                $subject = $emailTemplate_manager->subject;
                                $replacement[0] = $subject;
                                $replacement[1] = $assessment_set->assessment;
                                foreach ($mangerSet as $m_id) {

                                    $ManagerSet = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name,email,company_id', "userid=" . $m_id['trainer_id']);

                                    $replacement[2] = '<a target="_blank" style="display: inline-block;
                                         width: 200px;
                                         height: 20px;
                                         background: #db1f48;
                                         padding: 10px;
                                         text-align: center;
                                         border-radius: 5px;
                                         color: white;
                                         border: 1px solid black;
                                         text-decoration:none;
                                         font-weight: bold;" href="' . base_url() . 'assessment/view/' . $id . '/2">View Assignment</a>';
                                    $replacement[3] = $ManagerSet->trainer_name;
                                    $replacement[4] = date("d-m-Y h:i a", strtotime($date));
                                    $replacement[5] = ''; //
                                    $ToName = $ManagerSet->trainer_name;
                                    $email_to = $ManagerSet->email;
                                    // $email_to = 'ranabhautik5@gmail.com';
                                    $Company_id = $ManagerSet->company_id;
                                    $message = $emailTemplate_manager->message;
                                    $body = preg_replace($pattern, $replacement, $message);
                                    $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                                }
                            }
                        }
                        //mail function for reps and managers end here 24-01-2023
                        $Message = "Assessment updated Successfully..!";
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function edit($id, $step = 1, $errors = "")
    {
        $assessment_id = base64_decode($id);
        $data['errors'] = $errors;
        $data['module_id'] = '90';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $superaccess = $this->mw_session['superaccess'];
        $data['superaccess'] = ($superaccess ? 1 : 0);
        $login_id = $this->mw_session['user_id'];
        $ISEXIST  = $this->common_model->get_value('assessment_supervisors', 'id,trainer_id', 'trainer_id=' . $login_id . ' AND assessment_id=' . $assessment_id);
        $data['is_supervisor'] = (count((array)$ISEXIST) > 0 ? 1 : 0);
        if (!$data['acces_management']->allow_edit) {
            redirect('create_trinity');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        //Added for AI report, Manual report and Combined report
        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');
        $data['result'] = $this->common_model->get_value('assessment_mst', '*', 'id="' . $assessment_id . '"');

        $question_play_array = array();
        $isPlay = $this->common_model->get_selected_values('assessment_results_trans', 'id,question_id', 'assessment_id=' . $assessment_id);
        if (count((array)$isPlay) > 0) {
            foreach ($isPlay as $val) {
                $question_play_array[] = $val->question_id;
            }
            $disabledflag = 1;
        }
        if (count((array)$question_play_array) == 0) {
            $isPlay = $this->common_model->get_selected_values('ai_schedule', 'id,question_id', 'assessment_id=' . $assessment_id);
            if (count((array)$isPlay) > 0) {
                foreach ($isPlay as $val) {
                    $question_play_array[] = $val->question_id;
                }
                $disabledflag = 1;
            }
        }
        $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $assessment_id);
        $data['disabledflag'] = (count((array)$isPlay2) > 0 ? 1 : 0);

        $isComplete = $this->common_model->get_selected_values('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id);
        $data['completedflag'] = (count((array)$isComplete) > 0 ? 1 : 0);
        // $data['aimeth_result'] = $aimeth_result;

        $data['step'] = $step;
        $data['question_play_array'] = $question_play_array;
        $question_attempts = $this->common_model->get_selected_values('assessment_attempts', 'id', 'assessment_id=' . $assessment_id);
        $data['map_script'] = $this->common_model->get_selected_values('script_mst', 'id,script_title,script,situation', '1=1');
        $data['assessment_level_goal'] = $this->common_model->get_selected_values('trinity_trans', '*', 'question_id = "0" and assessment_id=' . $assessment_id);
        $data['mapping_script'] = $this->common_model->get_selected_values('trinity_trans', '*', 'assessment_id=' . $assessment_id);

        $this->db->distinct();
        $this->db->select("sm.situation");
        $this->db->from("trinity_trans as t");
        $this->db->join("script_mst as sm", "sm.id = t.script_id", "left");
        $this->db->where("assessment_id", $assessment_id);
        $data['ass_situation'] = $this->db->get()->result();
        // $data['persona_result'] = $this->common_model->get_selected_values('trinity_persona', 'id,persona_name,persona_image', 'assessment_id=' . $assessment_id);
        $this->db->select('tp.id,tp.persona_name,tp.persona_image');
        $this->db->from('trinity_persona as tp');
        $this->db->join('trinity_map_persona as tmp', 'tp.id = tmp.persona_id', 'left');
        $this->db->where('tmp.assessment_id', $assessment_id);
        $data['persona_result'] = $this->db->get()->result();

        $data['persona'] =  $this->common_model->get_selected_values('trinity_persona', 'id,persona_name,persona_image', '1=1');
        $data['lockQue'] = (count((array)$question_attempts) > 0 ? 1 : 0);
        $data['assessment_id'] = $id;
        // $data['question_limits'] = ($data['result']->question_limits == 0) ? count($assessment_trans) : $data['result']->question_limits;
        // $this->db->select('t.question_id');
        // $this->db->from('trinity_trans as t');
        // $this->db->join('assessment_script_qna as asq', 'asq.id=t.question_id');
        // $this->db->where('t.assessment_id= "' . $assessment_id . '" and t.is_default = 1 ');
        // $this->db->order_by('t.question_id', 'asc');
        // $data['c_question_arr'] = $this->db->get()->result();
        // Mapping goal start

        $this->db->select('t.id,t.assessment_id,t.script_id,t.question_id,t.is_default,asq.*');
        $this->db->from('trinity_trans as t');
        $this->db->join('assessment_script_qna as asq', 'asq.id=t.question_id');
        $this->db->where('t.assessment_id= "' . $assessment_id . '" and t.is_default = 1 ');
        $this->db->order_by('t.question_id', 'desc');
        $data['mapping_goal'] = $this->db->get()->result();

        $data['mapping_goal_parameter'] = $this->common_model->get_selected_values('goal_mst', 'id,parameter_id,description', 'parameter_id NOT IN (6,2) and status=1');

        // $this->db->select('id,parameter_id,description,status');
        // $this->db->from('parameter_label_mst');
        // $this->db->where('status = 1');
        // $data['mapping_goal_parameter'] = $this->db->get()->result();

        $this->db->select('t.*');
        $this->db->from('trinity_trans as t');
        $this->db->where('t.assessment_id= "' . $assessment_id . '" and t.is_default = 1 ');
        $mapping_goal_tarns = $this->db->get()->result();
        $parameter_array = array();

        if (count((array)$mapping_goal_tarns) > 0) {
            foreach ($mapping_goal_tarns as $v) {
                $para = explode(',', $v->parameter_id);
                // $para = explode(',', $v->goal_id);
                $parameter_array[$v->question_id] = $para;
            }
        }
        $parameter_subparameter_trans = $this->create_trinity_model->LoadParameterSubParameter($assessment_id);
        $data['parameter_subparameter'] = $parameter_subparameter_trans;
        $data['parameter_array'] = $parameter_array;
        $data['edit_id'] = $id;
        $this->load->view('create_trinity/edit', $data);
    }
    // public function copy($id, $errors = "")
    // {
    //     $assessment_id = base64_decode($id);
    //     $unique_aimethods = $this->create_trinity_model->LoadUniqueAIMethods($assessment_id);
    //     $data['unique_aimethods'] = $unique_aimethods;
    //     $data['errors'] = $errors;
    //     $data['module_id'] = '102';
    //     $data['username'] = $this->mw_session['username'];
    //     $data['acces_management'] = $this->acces_management;
    //     if (!$data['acces_management']->allow_edit) {
    //         redirect('assessment_create');
    //         return;
    //     }
    //     $Company_id = $this->mw_session['company_id'];
    //     if ($Company_id == "") {
    //         $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
    //     } else {
    //         $data['cmp_result'] = array();
    //     }
    //     $data['Company_id'] = $Company_id;
    //     $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
    //     //Added
    //     $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');
    //     $data['result'] = $this->common_model->get_value('assessment_mst', '*', 'id=' . $assessment_id);
    //     $Qdata = $this->common_model->get_selected_values('assessment_question', 'id,question', 'company_id=' . $data['result']->company_id);
    //     $Pdata = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'company_id=' . $data['result']->company_id);
    //     // $Qdata = $this->common_model->get_selected_values('assessment_question', 'id,question', 'assessment_type=' . $data['result']->assessment_type . ' AND company_id=' . $data['result']->company_id);
    //     // $Pdata = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'assessment_type=' . $data['result']->assessment_type . ' AND company_id=' . $data['result']->company_id);
    //     $assessment_trans = $this->create_trinity_model->LoadAssessmentQuestions($assessment_id);
    //     $parameter_subparameter_trans = $this->create_trinity_model->LoadParameterSubParameter($assessment_id);
    //     $language_result = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');

    // $data['parametr_weights'] = $this->create_trinity_model->LoadParametrWeights($assessment_id);
    // $query = " SELECT apw.*,p.description as parameter_name FROM assessment_para_weights apw "
    //     . " LEFT JOIN parameter_mst p ON p.id=apw.parameter_id "
    //     . " where apw.assessment_id =" . $assessment_id;
    // $query = $this->db->query($query);
    // return $query->result();

    //     $this->db->select('apw.*,p.description as parameter_name');
    //     $this->db->from('assessment_para_weights apw');
    //     $this->db->join('parameter_mst p', 'p.id=apw.parameter_id', 'left');
    //     $this->db->where('apw.assessment_id', $assessment_id);
    //     $data['parametr_weights'] = $this->db->get()->result();

    //     $parameter_array = array();
    //     $question_array = array();
    //     if (count((array)$assessment_trans) > 0) {
    //         foreach ($assessment_trans as $v) {
    //             $para = explode(',', $v->parameter_id);
    //             $parameter_array[$v->question_id] = $para;
    //             $question_array[] = $v->question_id;
    //         }
    //     }
    //     $question_play_array = array();
    //     $isPlay = $this->common_model->get_selected_values('assessment_results_trans', 'id,question_id', 'assessment_id=' . $assessment_id);
    //     if (count((array)$isPlay) > 0) {
    //         foreach ($isPlay as $val) {
    //             $question_play_array[] = $val->question_id;
    //         }
    //         $disabledflag = 1;
    //     }
    //     $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $assessment_id);
    //     $data['disabledflag'] = (count((array)$isPlay2) > 0 ? 1 : 0);
    //     $data['assessment_trans'] = $assessment_trans;
    //     $data['parameter_array'] = $parameter_array;
    //     $data['Questions'] = $Qdata;
    //     $data['Parameter'] = $Pdata;
    //     $data['question_array'] = $question_array;
    //     $data['question_play_array'] = $question_play_array;
    //     $data['parameter_subparameter'] = $parameter_subparameter_trans;
    //     $data['language_result'] = $language_result;

    //     $this->load->view('create_trinity/copy', $data);
    // }
    public function DatatableRefresh()
    {
        $dtSearchColumns = array('am.id', 'am.id', 'am.id', 'am.assessment', 'DATE_FORMAT(am.start_dttm,"%d-%m-%Y %H:%i")', 'DATE_FORMAT(am.end_dttm,"%d-%m-%Y %H:%i:%")', 'at.description');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns_qbuilder($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $dtLimitLength = $DTRenderArray['dtLimitLength'];
        $now = date('Y-m-d H:i:s');

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }

        $superaccess = $this->mw_session['superaccess'];
        $this->db->select("am.id,am.company_id,am.assessment_type as assessment_type_id,CASE WHEN am.assessment_type=2 THEN 'Spotlight' WHEN  am.assessment_type=3 THEN 'Trnity' ELSE 'Roleplay' END AS ass_type,am.assessment,at.description AS assessment_type, IF(is_situation=1,'Situation','Question') AS question_type, am.status,DATE_FORMAT(am.start_dttm,'%d-%m-%Y %H:%i') AS start_dttm,DATE_FORMAT(am.end_dttm,'%d-%m-%Y %H:%i') AS end_dttm");
        $this->db->from("assessment_mst am");
        $this->db->join("assessment_type as at", "at.id = am.assessment_type", "left");
        if ($cmp_id != "") {
            $this->db->where("am.company_id", $cmp_id);
        }
        // $assessment_type = $this->security->xss_clean($this->input->get('assessment_type'));
        // if ($assessment_type != "") {
            $this->db->where("am.assessment_type", 3);
        // }
        $question_type = $this->input->get('question_type') != null ? $this->security->xss_clean($this->input->get('question_type')) : '';
        if ($question_type != "") {
            $this->db->where("am.is_situation", $question_type);
        }
        $status = $this->security->xss_clean($this->input->get('filter_status'));
        if ($status == "1") {
            $this->db->where("am.end_dttm >= ", $now);
        } elseif ($status == "2") {
            $this->db->where("am.end_dttm < ", $now);
        } elseif ($status == "3") {
            $this->db->where("am.start_dttm  >  ", $now);
            $this->db->where("am.status  >  ", 1);
        } elseif ($status == "4") {
            $this->db->where("am.status = ", 0);
        }
        $this->db->group_by("am.id");
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $search = $_GET['sSearch'];
            foreach ($dtSearchColumns as $i => $sCol) {
                if ($i == 0) {
                    $this->db->like($sCol, $search);
                } else {
                    $this->db->or_like($sCol, $search);
                }
            }
        }
        $this->db->order_by($dtOrder);
        $this->db->limit($dtLimitLength, $dtLimit);
        $DTRenderArray['ResultSet'] = $this->db->get()->result_array();
        $DTRenderArray['dtPerPageRecords'] = count((array)$DTRenderArray['ResultSet']);

        $this->db->select("am.id as total");
        $this->db->from("assessment_mst as am");
        $this->db->join("assessment_type at", "at.id = am.assessment_type", "left");
        if ($cmp_id != "") {
            $this->db->where("am.company_id", $cmp_id);
        }
        // $assessment_type = $this->security->xss_clean($this->input->get('assessment_type'));
        // if ($assessment_type != "") {
            $this->db->where("am.assessment_type", 3);
        // }
        $question_type = $this->input->get('question_type') != null ? $this->security->xss_clean($this->input->get('question_type')) : '';
        if ($question_type != "") {
            $this->db->where("am.is_situation", $question_type);
        }
        $status = $this->security->xss_clean($this->input->get('filter_status'));
        if ($status == "1") {
            $this->db->where("am.end_dttm >= ", $now);
        } elseif ($status == "2") {
            $this->db->where("am.end_dttm < ", $now);
        } elseif ($status == "3") {
            $this->db->where("am.start_dttm  >  ", $now);
            $this->db->where("am.status  >  ", 1);
        } elseif ($status == "4") {
            $this->db->where("am.status = ", 0);
        }
        $this->db->group_by("am.id");
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $search = $_GET['sSearch'];
            foreach ($dtSearchColumns as $sCol) {
                $this->db->like($sCol, $search);
            }
        }
        $DTRenderArray['dtTotalRecords'] = $this->db->count_all_results();
        // $DTRenderArray = $this->create_trinity_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        // $dtDisplayColumns = array('checkbox', 'id','question_type','assessment', 'start_dttm', 'end_dttm', 'status','Actions');
        $dtDisplayColumns = array('checkbox', 'id', 'ass_type', 'assessment', 'start_dttm', 'end_dttm', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add or $acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'create_trinity/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        // if ($acces_management->allow_add) {
                        //     $action .= '<li>
                        //                 <a href="' . $site_url . 'create_trinity/copy/' . base64_encode($dtRow['id']) . '">
                        //                 <i class="fa fa-copy"></i>&nbsp;Copy
                        //                 </a>
                        //             </li>';
                        // }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        // if ($acces_management->allow_edit) {
                        //     $action .= '<li>
                        //                 <a href="' . $site_url . 'create_trinity/reports_preview/' . base64_encode($dtRow['id']) . '/1" target="_blank">
                        //                 <i class="fa fa-eye"></i>&nbsp;Preview
                        //                 </a>
                        //             </li>';
                        // }
                        $action .= '</ul>
                            </div>';
                    } else {
                        $action = '<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                Locked&nbsp;&nbsp;<i class="fa fa-lock"></i>
                            </button>';
                    }

                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function remove($id)
    {
        $assessment_id = base64_decode($id);
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'success';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $ReturnFlag = true;
            if ($ReturnFlag) {
                $this->common_model->delete('assessment_mst', 'id', $assessment_id);
                $this->common_model->delete('assessment_allow_users', 'assessment_id', $assessment_id);
                $this->common_model->delete('assessment_results', 'assessment_id', $assessment_id);
                $this->common_model->delete('assessment_results_trans', 'assessment_id', $assessment_id);
                $this->common_model->delete('assessment_trans', 'assessment_id', $assessment_id);
                $this->common_model->delete('assessment_trans_sparam', 'assessment_id', $assessment_id);
                $this->common_model->delete('assessment_mapping_user', 'assessment_id', $assessment_id);
                $message = "Assessment deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Assessment cannot be deleted.!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action)
    {
        $action_id = $this->security->xss_clean($this->input->post('id'));
        if (count((array)$action_id) == 0) {
            echo json_encode(array('message' => "Please select record from the list", 'alert_type' => 'error'));
            exit;
        }
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        $title = '';
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']
                );
                $this->common_model->update('assessment_mst', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                // $StatusFlag = $this->create_trinity_model->CheckUserAssignRole($id);
                $StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('assessment_mst', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $assessment_id) {
                // $DeleteFlag = $this->create_trinity_model->CheckUserAssignRole($id);
                $DeleteFlag = true;
                if ($DeleteFlag) {
                    $this->common_model->delete('assessment_mst', 'id', $assessment_id);
                    $this->common_model->delete('assessment_allow_users', 'assessment_id', $assessment_id);
                    $this->common_model->delete('assessment_results', 'assessment_id', $assessment_id);
                    $this->common_model->delete('assessment_results_trans', 'assessment_id', $assessment_id);
                    $this->common_model->delete('assessment_trans', 'assessment_id', $assessment_id);
                    $this->common_model->delete('assessment_trans_sparam', 'assessment_id', $assessment_id);
                    $this->common_model->delete('assessment_mapping_user', 'assessment_id', $assessment_id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Assessment cannot be deleted.!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Assessment(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_assessment()
    {
        $assessment = $this->security->xss_clean($this->input->post('assessment', true));
        $assessment_id = $this->security->xss_clean($this->input->post('assessment_id', true));
        if ($assessment_id != "") {
            $assessment_id = base64_decode($assessment_id);
        }
        $assessment_type = $this->security->xss_clean($this->input->post('assessment_type', true));
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->security->xss_clean($this->input->post('company_id', TRUE));
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        echo $this->create_trinity_model->check_assessment($Company_id, $assessment, $assessment_type, $assessment_id);
    }

    public function RemoveParticipantUser($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $Remove_id = $this->security->xss_clean($this->input->post('Remove_id'));
        $this->common_model->delete('assessment_allow_users', 'id', $Remove_id);
        $Message = "Participant User removed successfully .";
        $Rdata['success'] = 1;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function addParticipant($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['RegionList'] = $this->create_trinity_model->get_TraineeRegionList($company_id);

        $this->db->distinct();
        $this->db->select('department');
        $this->db->from('device_users');
        $this->db->where('company_id', $company_id);
        $this->db->where('department !=', '');
        $this->db->order_by('department', 'ASC');
        $data['DepartmentList'] = $this->db->get()->result();
        $this->load->view('create_trinity/UsersFilterModal', $data);
    }

    public function addManagers()
    {
        //$data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['RegionList'] = $this->create_trinity_model->get_TrainerRegionList($company_id);
        $this->load->view('create_trinity/ManagersFilterModal', $data);
    }
    public function addUserManagers($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['user_row'] = 1;
        // $data['assessor_users'] = $this->create_trinity_model->get_managers($data['assessment_id'], '');

        $this->db->select("u.userid, CONCAT(u.first_name,' ',u.last_name) as name, d.description as designation");
        $this->db->from('assessment_managers as w');
        $this->db->join('company_users as u', 'u.userid = w.trainer_id', 'left');
        $this->db->join('designation as d', 'd.id=u.designation_id', 'left');
        $this->db->where('w.assessment_id', $data['assessment_id']);
        $data['assessor_users'] = $this->db->get()->result();

        $data['RegionList'] = $this->create_trinity_model->get_TraineeRegionList($company_id);
        $this->load->view('create_trinity/UserManagersFilterModal', $data);
    }
    public function addSupervisors()
    {
        //$data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['RegionList'] = $this->create_trinity_model->get_TrainerRegionList($company_id);
        $this->load->view('create_trinity/SupervisorsFilterModal', $data);
    }
    public function Removeall_participants($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $action_id = $this->security->xss_clean($this->input->post('Participant_all'));
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $message = "You have no rights to Edit Assessment,Contact Administrator for rights";
            $alert_type = 'error';
        } else {
            if (count((array)$action_id) == 0) {
                $message = "Please select record from the list";
                $alert_type = 'error';
            } else {
                foreach ($action_id as $id) {
                    //START -- REMOVE USER ASSESOR MAPPING
                    // $deleted_user = $this->common_model->get_value('assessment_allow_users', 'user_id', 'id=' . $id . ' and assessment_id=' . $assessment_id);
                    // if(!empty($deleted_user)){
                    //     $this->common_model->delete_whereclause('assessment_mapping_user', 'user_id=' . $deleted_user->user_id . ' and assessment_id=' . $assessment_id);
                    // }
                    //END -- 

                    $this->common_model->delete_whereclause('assessment_allow_users', 'id=' . $id . ' and assessment_id=' . $assessment_id);
                }
                $message = "Assessment Participant User(s) removed successfully";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
    public function Removeall_mapping($Encode_id = '')
    {
        $alert_type = 'success';
        $message = '';
        $tot_cnt = array();
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        $action_id = $this->security->xss_clean($this->input->post('Mapping_all'));
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $message = "You have no rights to Edit Assessment,Contact Administrator for rights";
            $alert_type = 'error';
        } else {
            if (count((array)$action_id) == 0) {
                $message = "Please select record from the list";
                $alert_type = 'error';
            } else {
                foreach ($action_id as $key => $id) {
                    if ($Encode_id != '') {
                        $map_data = $this->create_trinity_model->get_map_manager($assessment_id, $id);
                        if (count((array)$map_data) > 0) {
                            $message = "You can't remove managers, who is mapp in User manager Mapping...</br>";
                            $alert_type = 'error';
                        } else {
                            $tot_cnt[] = $key;
                            $this->common_model->delete_whereclause('assessment_managers', 'trainer_id=' . $id . ' and assessment_id=' . $assessment_id);
                        }
                    } else {
                        $SessionManagersArrray = $this->session->userdata('NewManagersArrray_session');
                        if (count((array)$SessionManagersArrray) > 0) {
                            if (($key = array_search($id, $SessionManagersArrray)) !== false) {
                                unset($SessionManagersArrray[$key]);
                                $this->session->set_userdata('NewManagersArrray_session', $SessionManagersArrray);
                            }
                        }
                    }
                }
                $message = "Assessment Mapping Manager(s) removed successfully";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
    public function Removeall_supermapping($Encode_id = '')
    {
        $action_id = $this->security->xss_clean($this->input->post('Mappsuper_all'));
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        if (!$acces_management->allow_edit) {
            $message = "You have no rights to Edit Assessment,Contact Administrator for rights";
            $alert_type = 'error';
        } else {
            if (count((array)$action_id) == 0) {
                $message = "Please select record from the list";
                $alert_type = 'error';
            } else {
                foreach ($action_id as $id) {
                    if ($Encode_id != '') {
                        $this->common_model->delete_whereclause('assessment_supervisors', 'trainer_id=' . $id . ' and assessment_id=' . $assessment_id);
                    } else {
                        $SessionSupervisorsArrray = $this->session->userdata('NewSupervisorsArrray_session');

                        if (count((array)$SessionSupervisorsArrray) > 0) {
                            if (($key = array_search($id, $SessionSupervisorsArrray)) !== false) {
                                unset($SessionSupervisorsArrray[$key]);
                                $this->session->set_userdata('NewSupervisorsArrray_session', $SessionSupervisorsArrray);
                            }
                        }
                    }
                }
                $message = "Assessment Mapping Supervisor(s) removed successfully";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function SaveParticipantUsers($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $Message = '';
        $SuccessFlag = 1;
        $NewUsersArrray = $this->input->post('NewUsersArrray');
        if (count((array)$NewUsersArrray) > 0) {
            foreach ($NewUsersArrray as $user_id) {
                $AlreadyExist = $this->common_model->get_value('assessment_allow_users', 'user_id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id);
                if (count((array)$AlreadyExist) > 0) {
                    continue;
                }
                $data = array(
                    'user_id' => $user_id,
                    'assessment_id' => $assessment_id
                );
                $this->common_model->insert('assessment_allow_users', $data);
            }
            // Jagdisha : 30/01/2023
            // For mail users
            $pattern[0] = '/\[SUBJECT\]/';
            $pattern[1] = '/\[ASSESSMENT_NAME\]/';
            $pattern[2] = '/\[ASSESSMENT_LINK\]/';
            // get email template
            $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='assessment_created_rep'");
            $pattern[3] = '/\[NAME\]/';
            $pattern[4] = '/\[DATE_TIME\]/';
            $pattern[5] = '/\[Client_mail_id\]/';

            $assessment_set = $this->common_model->get_value('assessment_mst', 'assessment,assessor_dttm,start_dttm,end_dttm', "id=" . $assessment_id);
            $SuccessFlag = 1;
            $Message = '';
            if (count((array)$emailTemplate) > 0) {
                $subject = $emailTemplate->subject;
                $replacement[0] = $subject;
                $replacement[1] = $assessment_set->assessment;
                $AllowSet = $this->common_model->get_users_value('assessment_allow_users', 'user_id', 'assessment_id=' . $assessment_id . ' AND send_mail=0');
                if (count((array)$AllowSet) > 0) {

                    $u_id = array();
                    foreach ($AllowSet as $id) {
                        $u_id[] = $id['user_id'];
                        $UserData = $this->common_model->get_value('device_users', 'company_id,concat(firstname," ",lastname) as trainee_name,email', '  user_id =' . $id['user_id']);
                        $ToName = $UserData->trainee_name;
                        $email_to = $UserData->email;
                        $Company_id = $UserData->company_id;
                        $replacement[2] = '<a target="_blank" style="display: inline-block;
                         background: #db1f48;
                         padding: .45rem 1rem;
                         box-sizing: border-box;
                         border: none;
                         border-radius: 3px;
                         color: #fff;
                         text-align: center;
                         font-family: Lato,Arial,sans-serif;
                         font-weight: 400;
                         font-size: 1em;
                         text-decoration:none;
                         line-height: initial;" href="https://pwa.awarathon.com">View Assignment</a>';
                        $replacement[3] = $UserData->trainee_name;
                        $replacement[4] = date("d-m-Y h:i a", strtotime($assessment_set->start_dttm));
                        $replacement[5] = 'info@awarathon.com';
                        $message = $emailTemplate->message;
                        $body = preg_replace($pattern, $replacement, $message);
                        $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                    }
                    if ($ReturnArray['sendflag'] == '1') {
                        $this->common_model->update_id('assessment_allow_users', 'user_id', $assessment_id, $u_id);
                    }
                }
            }
            // Jagdisha End: 30/01/2023
            $Message = "User added successfully.!";
        } else {
            $Message = "Please select Users.!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function SaveParticipantManagers($Encode_id = '')
    {
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        $Message = '';
        $SuccessFlag = 1;
        $NewManagersArrray = $this->input->post('NewManagersArrray');
        if (count((array)$NewManagersArrray) > 0) {
            if ($Encode_id != '') {
                foreach ($NewManagersArrray as $user_id) {
                    $AlreadyExist = $this->common_model->get_value('assessment_managers', 'trainer_id', 'assessment_id=' . $assessment_id . ' AND trainer_id=' . $user_id);
                    if (count((array)$AlreadyExist) > 0) {
                        continue;
                    }
                    $data = array(
                        'trainer_id' => $user_id,
                        'assessment_id' => $assessment_id
                    );
                    $this->common_model->insert('assessment_managers', $data);
                }
                // Jagdisha : 30/01/2023
                $pattern[0] = '/\[SUBJECT\]/';
                $pattern[1] = '/\[ASSESSMENT_NAME\]/';
                $pattern[2] = '/\[ASSESSMENT_LINK\]/';
                // $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_assessment_alert'");
                $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='assessment_created_manger'");
                $pattern[3] = '/\[NAME\]/';
                $pattern[4] = '/\[DATE_TIME\]/';
                $pattern[5] = '/\[NAME\]/';
                $assessment_set = $this->common_model->get_value('assessment_mst', 'assessment,assessor_dttm,start_dttm,end_dttm', "id=" . $assessment_id);
                $SuccessFlag = 1;
                $Message = '';
                // $AllowSet = $this->common_model->get_users_value('assessment_allow_users', 'user_id', 'assessment_id=' . $assessment_id .' AND send_mail=0');
                $AllowSet = $this->common_model->get_users_value('assessment_managers', 'trainer_id', 'assessment_id=' . $assessment_id . ' AND send_mail=0');
                if (count((array)$AllowSet) > 0) {
                    $u_id = array();
                    $subject = $emailTemplate->subject;
                    $replacement[0] = $subject;
                    $replacement[1] = $assessment_set->assessment;
                    foreach ($AllowSet as $id) {
                        $u_id[] = $id['trainer_id'];

                        $ManagerSet = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name,email,company_id', "userid=" . $id['trainer_id']);

                        $replacement[2] = '<a target="_blank" style="display: inline-block;
                         width: 200px;
                         height: 20px;
                         background: #db1f48;
                         padding: 10px;
                         text-align: center;
                         border-radius: 5px;
                         color: white;
                         border: 1px solid black;
                         text-decoration:none;
                         font-weight: bold;" href="' . base_url() . 'assessment/view/' . $assessment_id . '/2">View Assignment</a>';
                        $replacement[3] = $ManagerSet->trainer_name;
                        $replacement[4] = date("d-m-Y h:i a", strtotime($assessment_set->assessor_dttm));
                        $replacement[5] = ''; //
                        $ToName = $ManagerSet->trainer_name;
                        $email_to = $ManagerSet->email;
                        $Company_id = $ManagerSet->company_id;
                        $message = $emailTemplate->message;
                        $body = preg_replace($pattern, $replacement, $message);
                        $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                    }
                }
                if ($ReturnArray['sendflag'] == '1') {
                    $this->common_model->update_id('assessment_managers', 'trainer_id', $assessment_id, $u_id);
                }
                // Jagdisha End: 30/01/2023

            } else {
                $ManagersArrray = array();
                if ($this->session->userdata('NewManagersArrray_session')) {
                    $ManagersArrray = $this->session->userdata('NewManagersArrray_session');
                }
                $Managers_array = array_merge($NewManagersArrray, $ManagersArrray);
                $this->session->set_userdata('NewManagersArrray_session', $Managers_array);
            }
            $Message = "Manager added successfully.!";
        } else {
            $Message = "Please select Manager.!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function SaveParticipantSupervisors($Encode_id = '')
    {
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        $Message = '';
        $SuccessFlag = 1;
        $NewSupervisorsArrray = $this->security->xss_clean($this->input->post('NewSupervisorsArrray'));
        if (count((array)$NewSupervisorsArrray) > 0) {
            if ($Encode_id != '') {
                foreach ($NewSupervisorsArrray as $user_id) {
                    $AlreadyExist = $this->common_model->get_value('assessment_supervisors', 'trainer_id', 'assessment_id=' . $assessment_id . ' AND trainer_id=' . $user_id);
                    if (count((array)$AlreadyExist) > 0) {
                        continue;
                    }
                    $data = array(
                        'trainer_id' => $user_id,
                        'assessment_id' => $assessment_id
                    );
                    $this->common_model->insert('assessment_supervisors', $data);
                }
            } else {
                $SupervisorsArrray = array();
                if ($this->session->userdata('NewSupervisorsArrray_session')) {
                    $SupervisorsArrray = $this->session->userdata('NewSupervisorsArrray_session');
                }
                $Supervisors_array = array_merge($NewSupervisorsArrray, $SupervisorsArrray);
                $this->session->set_userdata('NewSupervisorsArrray_session', $Supervisors_array);
            }
            $Message = "Supervisors added successfully.!";
        } else {
            $Message = "Please select Supervisor.!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    //START -- MAP AUTOMATIC USER AND ASSESSOR
    public function SaveAutoMappingUserAssessor($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $mapped_users = $this->common_model->get_selected_values('assessment_mapping_user', 'user_id', 'assessment_id=' . $assessment_id);
        $mappeduser_id_array = array_column($mapped_users, 'user_id');

        $where = 'assessment_id=' . $assessment_id;
        if (!empty($mappeduser_id_array)) {
            $where .= ' AND user_id NOT IN (' . implode(',', $mappeduser_id_array) . ')';
        }
        $assessment_user_ids = $this->common_model->get_selected_values('assessment_allow_users', 'user_id', $where);
        $user_id_array = array_column($assessment_user_ids, 'user_id');

        if (!empty($user_id_array)) {
            $manager_id_array = $this->common_model->get_selected_values('device_users', 'user_id,trainer_id', 'user_id IN (' . implode(',', $user_id_array) . ') AND trainer_id!= "0" AND trainer_id IS NOT NULL');
            if (!empty($manager_id_array)) {
                foreach ($manager_id_array as $user) {
                    $user_data = array(
                        'user_id' => $user->user_id,
                        'trainer_id' => $user->trainer_id,
                        'assessment_id' => $assessment_id
                    );
                    $this->common_model->insert('assessment_mapping_user', $user_data);

                    $AlreadyExist = $this->common_model->get_value('assessment_managers', 'trainer_id', 'assessment_id=' . $assessment_id . ' AND trainer_id=' . $user->trainer_id);
                    if (empty($AlreadyExist)) {
                        $data = array(
                            'trainer_id' => $user->trainer_id,
                            'assessment_id' => $assessment_id
                        );
                        $this->common_model->insert('assessment_managers', $data);
                    }
                }
            }
        }
        return true;
    }
    // -- END
    public function SaveMappingUserAssessor($Encode_id)
    {
        $Message = '';
        $SuccessFlag = 1;
        $assessment_id = base64_decode($Encode_id);
        $manager_id_array = $this->security->xss_clean($this->input->post('user_id', true));
        $user_id_array = $this->security->xss_clean($this->input->post('id', true));

        if (count((array)$manager_id_array) == 0) {
            $Message = "Please Mapp Manager!";
            $SuccessFlag = 0;
        }
        if (count((array)$user_id_array) == 0) {
            $Message = "Please Select Assessor!";
            $SuccessFlag = 0;
        }
        if ($SuccessFlag) {
            // foreach ($manager_id_array as $trainer) { 
            foreach ($user_id_array as $user) {
                //$lcwhere = 'assessment_id =' . $assessment_id . ' AND trainer_id=' . $trainer.' AND user_id='.$user;
                $lcwhere = 'assessment_id =' . $assessment_id . ' AND trainer_id=' . $manager_id_array . ' AND user_id=' . $user;
                $AlreadyExist = $this->common_model->get_value('assessment_mapping_user', 'id', $lcwhere);
                if (count((array)$AlreadyExist) > 0) {
                    $Message = "Selected User has Already Mapped with Manager!";
                    $SuccessFlag = 0;
                    break;
                }
            }
            //}
        }
        if ($SuccessFlag) {
            //foreach ($manager_id_array as $trainer) { 
            foreach ($user_id_array as $user) {
                $user_data = array(
                    'user_id' => $user,
                    'trainer_id' => $manager_id_array,
                    'assessment_id' => $assessment_id
                );
                $this->common_model->insert('assessment_mapping_user', $user_data);
            }
            //}
            $Message = "Mapping successfully.!";
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function UsersFilterTable($Encode_id, $is_mapped = '')
    {
        $assessment_id = base64_decode($Encode_id);
        $dtSearchColumns = array('u.user_id', 'tr.region_name', 'u.department', 'u.firstname', 'u.email', 'u.mobile', 'u.area', 'u.lastname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($this->input->get('sSearch') != '') {
            $search_Result = explode(' ', trim($this->input->get('sSearch')));
            if (count((array)$search_Result) > 1) {
                $dtWhere = " WHERE ((u.firstname like '%" . $search_Result[0] . "%' AND u.lastname like '%" . $search_Result[1] . "%') ) ";
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->get('company_id') ? $this->security->xss_clean($this->input->get('company_id')) : '';
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($Company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status=1 AND  u.company_id  = " . $Company_id;
            } else {
                $dtWhere .= " WHERE u.status=1 AND u.company_id  = " . $Company_id;
            }
        }
        $flt_region_id = $this->input->get('flt_region_id') ? $this->security->xss_clean($this->input->get('flt_region_id')) : '';
        if ($flt_region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.region_id  = " . $flt_region_id;
            } else {
                $dtWhere .= " WHERE u.region_id  = " . $flt_region_id;
            }
        }
        $flt_department_id = $this->input->get('flt_department_id') ? $this->security->xss_clean($this->input->get('flt_department_id')) : '';
        if ($flt_department_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.department LIKE '" . $flt_department_id . "'";
            } else {
                $dtWhere .= " WHERE u.department LIKE '" . $flt_department_id . "'";
            }
        }
        if ($is_mapped == 1) {
            $allow_users = $this->common_model->get_selected_values('assessment_allow_users', 'user_id', 'assessment_id=' . $assessment_id);
            if (count((array)$allow_users) > 0) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.user_id IN(SELECT user_id FROM assessment_allow_users where assessment_id=" . $assessment_id . ")";
                    $dtWhere .= " AND u.user_id NOT IN(SELECT user_id FROM assessment_mapping_user where assessment_id=" . $assessment_id . ")";
                } else {
                    $dtWhere .= " WHERE u.user_id IN(SELECT user_id FROM assessment_allow_users where assessment_id=" . $assessment_id . ")";
                    $dtWhere .= " AND u.user_id NOT IN(SELECT user_id FROM assessment_mapping_user where assessment_id=" . $assessment_id . ")";
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.user_id IN(SELECT user_id FROM device_users where status=1)";
                    $dtWhere .= " AND u.user_id NOT IN(SELECT user_id FROM assessment_mapping_user where assessment_id=" . $assessment_id . ")";
                } else {
                    $dtWhere .= " WHERE u.user_id IN(SELECT user_id FROM device_users where status=1)";
                    $dtWhere .= " AND u.user_id NOT IN(SELECT user_id FROM assessment_mapping_user where assessment_id=" . $assessment_id . ")";
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.user_id Not IN(SELECT user_id FROM assessment_allow_users where assessment_id=" . $assessment_id . ")";
            } else {
                $dtWhere .= " WHERE u.user_id Not IN(SELECT user_id FROM assessment_allow_users where assessment_id=" . $assessment_id . ")";
            }
        }
        $this->load->model('company_model');
        $DTRenderArray = $this->create_trinity_model->LoadUsersDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );

        $NewUsersArrray = $this->input->get('NewUsersArrray');
        $TestArray = explode(',', $NewUsersArrray);

        $dtDisplayColumns = array('user_id', 'region_name', 'department', 'name', 'email', 'mobile', 'area', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="id[]" id="chk' . $dtRow['user_id'] . '" ';
                    $action .= 'value="' . $dtRow['user_id'] . '" onclick="SelectedUsers(' . $dtRow['user_id'] . ')"';
                    if (count((array)$TestArray) > 0 && in_array($dtRow['user_id'], $TestArray)) {
                        $action .= "checked";
                    }
                    $action .= '/><span></span></label>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function ManagersFilterTable($Encode_id = '')
    {
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        $dtSearchColumns = array('u.userid', 'tr.region_name', 'u.username', 'u.first_name', 'u.email', 'd.description',);
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($this->input->get('sSearch') != '') {
            $search_Result = explode(' ', trim($this->input->get('sSearch')));
            if (count((array)$search_Result) > 1) {
                $dtWhere = " WHERE ((u.first_name like '%" . $search_Result[0] . "%' AND u.last_name like '%" . $search_Result[1] . "%') ) ";
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->get('company_id') ? $this->security->xss_clean($this->input->get('company_id')) : '';
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($Company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status=1 AND  u.company_id  = " . $Company_id;
            } else {
                $dtWhere .= " WHERE u.status=1 AND u.company_id  = " . $Company_id;
            }
        }
        $flt_region_id = $this->input->get('flt_tregion_id') ? $this->security->xss_clean($this->input->get('flt_tregion_id')) : '';
        if ($flt_region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.region_id  = " . $flt_region_id;
            } else {
                $dtWhere .= " WHERE u.region_id  = " . $flt_region_id;
            }
        }
        $NewManagersArrray = $this->input->get('NewManagersArrray');
        //$TestArray = explode(',', $NewManagersArrray);
        $TestArray = $this->session->userdata('NewManagersArrray_session');
        if ($Encode_id != '') {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.userid Not IN(SELECT trainer_id FROM assessment_managers where assessment_id=" . $assessment_id . ")";
            } else {
                $dtWhere .= " WHERE u.userid Not IN(SELECT trainer_id FROM assessment_managers where assessment_id=" . $assessment_id . ")";
            }
        } else {
            $manager_str = '';
            $sessionManagersArrray = $this->session->userdata('NewManagersArrray_session');
            if (count((array)$sessionManagersArrray) > 0) {
                $manager_str = implode(',', $sessionManagersArrray);
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.userid Not IN(" . $manager_str . ")";
                } else {
                    $dtWhere .= " WHERE u.userid Not IN(" . $manager_str . ")";
                }
            }
        }

        $this->load->model('company_model');
        $DTRenderArray = $this->create_trinity_model->LoadManagersDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );



        $dtDisplayColumns = array('userid', 'region_name', 'username', 'name', 'email', 'designation', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="Mapping_all[]" id="chk_' . $dtRow['userid'] . '" ';
                    $action .= 'value="' . $dtRow['userid'] . '" onclick="SelectedManagers(' . $dtRow['userid'] . ')"';
                    if (count((array)$TestArray) > 0 && in_array($dtRow['userid'], $TestArray)) {
                        $action .= "checked";
                    }
                    $action .= '/><span></span></label>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function SupervisorsFilterTable($Encode_id = '')
    {
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        $dtSearchColumns = array('u.userid', 'u.username', 'u.first_name', 'u.email', 'd.description',);
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($this->input->get('sSearch') != '') {
            $search_Result = explode(' ', trim($this->input->get('sSearch')));
            if (count((array)$search_Result) > 1) {
                $dtWhere = " WHERE ((u.first_name like '%" . $search_Result[0] . "%' AND u.last_name like '%" . $search_Result[1] . "%') ) ";
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->get('company_id') ? $this->security->xss_clean($this->input->get('company_id')) : '';
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($Company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status=1 AND  u.company_id  = " . $Company_id;
            } else {
                $dtWhere .= " WHERE u.status=1 AND u.company_id  = " . $Company_id;
            }
        }
        $NewSupervisorsArrray = $this->input->get('NewSupervisorsArrray');
        //$TestArray = explode(',', $NewSupervisorsArrray);
        $TestArray = $this->session->userdata('NewSupervisorsArrray_session');
        if ($Encode_id != '') {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.userid Not IN(SELECT trainer_id FROM assessment_supervisors where assessment_id=" . $assessment_id . ")";
            } else {
                $dtWhere .= " WHERE u.userid Not IN(SELECT trainer_id FROM assessment_supervisors where assessment_id=" . $assessment_id . ")";
            }
        } else {
            $supervisor_str = '';
            $sessionSupervisorArrray = $this->session->userdata('NewSupervisorsArrray_session');
            if (count((array)$sessionSupervisorArrray) > 0) {
                $supervisor_str = implode(',', $sessionSupervisorArrray);
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.userid Not IN(" . $supervisor_str . ")";
                } else {
                    $dtWhere .= " WHERE u.userid Not IN(" . $supervisor_str . ")";
                }
            }
        }

        $this->load->model('company_model');
        $DTRenderArray = $this->create_trinity_model->LoadManagersDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );



        $dtDisplayColumns = array('userid', 'username', 'name', 'email', 'designation', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="sid[]" id="ck_' . $dtRow['userid'] . '" ';
                    $action .= 'value="' . $dtRow['userid'] . '" onclick="SelectedSupervisors(' . $dtRow['userid'] . ')"';
                    if (count((array)$TestArray) > 0 && in_array($dtRow['userid'], $TestArray)) {
                        $action .= "checked";
                    }
                    $action .= '/><span></span></label>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function CandidateDataTable($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $dtSearchColumns = array('atr.trainer_id', 'u.firstname', 'atr.user_id', 'cm.first_name', 'atr.user_id',);
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        //        if ($this->input->get('sSearch') != '') {
        //            $search_Result = explode(' ', trim($this->input->get('sSearch')));
        //            if (count((array)$search_Result) > 1) {
        //                $dtWhere = " WHERE ((u.first_name like '%" . $search_Result[0] . "%' AND u.last_name like '%" . $search_Result[1] . "%') ) ";
        //            }
        //        }
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->get('company_id') ? $this->security->xss_clean($this->input->get('company_id')) : '';
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($Company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status=1 AND  u.company_id  = " . $Company_id;
            } else {
                $dtWhere .= " WHERE u.status=1 AND u.company_id  = " . $Company_id;
            }
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND atr.assessment_id=" . $assessment_id;
        } else {
            $dtWhere .= " WHERE atr.assessment_id=" . $assessment_id;
        }
        $this->load->model('company_model');
        $DTRenderArray = $this->create_trinity_model->LoadCandidateDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainer_id', 'trainee_name', 'trainee_status', 'trainer_name', 'trainer_status');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="sid[]" id="ck_' . $dtRow['userid'] . '" ';
                    $action .= 'value="' . $dtRow['userid'] . '" onclick="SelectedSupervisors(' . $dtRow['userid'] . ')"';
                    if (count((array)$TestArray) > 0 && in_array($dtRow['userid'], $TestArray)) {
                        $action .= "checked";
                    }
                    $action .= '/><span></span></label>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function ParticipantUsers($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'u.firstname', 'u.email', 'u.mobile', 'u.area', 'tr.region_name', 'u.lastname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND w.assessment_id  = " . $assessment_id;
        } else {
            $dtWhere .= " WHERE w.assessment_id  = " . $assessment_id;
        }
        $fttrainer_id = $this->input->get('fttrainer_id') ? $this->security->xss_clean($this->input->get('fttrainer_id')) : '';
        if ($fttrainer_id != "") {
            $dtWhere .= " AND u.trainer_id  = " . $fttrainer_id;
        }
        $DTRenderArray = $this->create_trinity_model->LoadParticipantUsers($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'user_id', 'name', 'email', 'mobile', 'region_name', 'area');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="Participant_all[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                        </label>';
                } elseif ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    $action = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete" '
                        . ' ><i class="fa fa-times"></i></button>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function MappingManagers($Encode_id = '')
    {
        $dtSearchColumns = array('u.userid', 'u.userid', 'tr.region_name', 'u.username', 'u.first_name', 'u.email', 'd.description', 'u.last_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $fttrainer_id = $this->input->get('fttrainer_id') ? $this->security->xss_clean($this->input->get('fttrainer_id')) : '';
        if ($fttrainer_id != "") {
            $dtWhere .= " AND u.trainer_id  = " . $fttrainer_id;
        }
        $NewManagers = $this->input->get('NewManagersArrray');
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.assessment_id  = " . $assessment_id;
            } else {
                $dtWhere .= " WHERE w.assessment_id  = " . $assessment_id;
            }
            $DTRenderArray = $this->create_trinity_model->LoadMappingManagers($dtWhere, $dtOrder, $dtLimit);
        } else {
            $manager_str = '';
            $sessionManagerArrray = $this->session->userdata('NewManagersArrray_session');
            if (count((array)$sessionManagerArrray) > 0) {
                $manager_str = implode(',', $sessionManagerArrray);
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.userid  IN (" . $manager_str . ")";
                } else {
                    $dtWhere .= " WHERE u.userid  IN (" . $manager_str . ")";
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND 1=0";
                } else {
                    $dtWhere .= " WHERE 1=0";
                }
            }
            $DTRenderArray = $this->create_trinity_model->TempLoadMappingManagers($dtWhere, $dtOrder, $dtLimit);
        }
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'userid', 'region_name', 'username', 'name', 'email', 'designation');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="Mapping_all[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                        </label>';
                } elseif ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    $action = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete" '
                        . ' ><i class="fa fa-times"></i></button>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        if ($DTRenderArray['dtTotalRecords'] > 0) {
            $sdata['status'] =  1;
        } else {
            $sdata['status'] =  0;
        }
        if ($Encode_id != '') {
            $this->common_model->update('assessment_mst', 'id', $assessment_id, $sdata);
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function UserMappingManagers($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'u.firstname', 'tr.region_name', 'cm.first_name',);
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND am.assessment_id  = " . $assessment_id;
        } else {
            $dtWhere .= " WHERE am.assessment_id  = " . $assessment_id;
        }

        //$dtWhere .= " AND u.user_id IN (select user_id FROM assessment_mapping_user where assessment_id=$assessment_id)";
        //$dtWhere .= " AND u.user_id IN (select user_id FROM assessment_allow_users where assessment_id=$assessment_id)";

        $DTRenderArray = $this->create_trinity_model->LoadUserMappingManagers($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'user_id', 'name', 'region_name', 'username');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="UserMapping_all[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                        </label>';
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function MappingSupervisors($Encode_id = '')
    {
        $dtSearchColumns = array('u.userid', 'u.userid', 'u.username', 'u.first_name', 'u.email', 'd.description', 'u.last_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $fttrainer_id = $this->input->get('fttrainer_id') ? $this->security->xss_clean($this->input->get('fttrainer_id')) : '';
        if ($fttrainer_id != "") {
            $dtWhere .= " AND u.trainer_id  = " . $fttrainer_id;
        }
        $NewSupervisors = $this->input->get('NewSupervisorsArrray');
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.assessment_id  = " . $assessment_id;
            } else {
                $dtWhere .= " WHERE w.assessment_id  = " . $assessment_id;
            }
            $DTRenderArray = $this->create_trinity_model->LoadMappingSupervisors($dtWhere, $dtOrder, $dtLimit);
        } else {
            $supervisor_str = '';
            $sessionSupervisorArrray = $this->session->userdata('NewSupervisorsArrray_session');
            if (count((array)$sessionSupervisorArrray) > 0) {
                $supervisor_str = implode(',', $sessionSupervisorArrray);
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.userid  IN (" . $supervisor_str . ")";
                } else {
                    $dtWhere .= " WHERE u.userid  IN (" . $supervisor_str . ")";
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND 1=0";
                } else {
                    $dtWhere .= " WHERE 1=0";
                }
            }
            $DTRenderArray = $this->create_trinity_model->TempLoadMappingSupervisors($dtWhere, $dtOrder, $dtLimit);
        }
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'userid', 'username', 'name', 'email', 'designation');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="Mappsuper_all[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                        </label>';
                } elseif ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    $action = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete" '
                        . ' ><i class="fa fa-times"></i></button>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }
    public function file_check($str)
    {
        $allowed_mime_type_arr = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .csv file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select csv to import.');
            return false;
        }
    }
    public function importTrainee($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        $this->load->view('create_trinity/import_trainee', $data);
    }
    public function import_user_manager($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        $this->load->view('create_trinity/import_user_manager', $data);
    }
    public function importManager()
    {
        $data = array();
        //$data['assessment_id'] = base64_decode($Encode_id);
        $this->load->view('create_trinity/import_manager', $data);
    }
    public function importSupervisor()
    {
        $data = array();
        //$data['assessment_id'] = base64_decode($Encode_id);
        $this->load->view('create_trinity/import_supervisor', $data);
    }
    public function trainee_samplecsv()
    {
        // $this->load->library('PHPExcel_CI');
        //$Excel = new PHPExcel_CI;
        $Excel = new Spreadsheet();
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Trainee_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        /*$Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
        $Excel->getActiveSheet()->mergeCells('A1:A1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()->setCellValue('A2', 'Employee Code*');
        $Excel->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:A2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");

        /*$Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('eb3a12');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename="trainee_sample.xls"');
        header('Cache-Control: max-age=0');

        //$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($Excel, "Xls");
        $objWriter->save('php://output');
        exit;
    }
    public function user_manager_samplecsv()
    {
        // $this->load->library('PHPExcel_CI');
        //$Excel = new PHPExcel_CI;
        $Excel = new Spreadsheet();
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Trainee_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        /*$Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->applyFromArray(array(
             'type' => PHPExcel_Style_Fill::FILL_SOLID,
             'startcolor' => array(
                 'rgb' => 'FF0000'
             )
         ));*/
        $Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
        $Excel->getActiveSheet()->mergeCells('A1:A1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()->setCellValue('A2', 'Employee Code/Employee Email *');
        $Excel->getActiveSheet()->setCellValue('B2', 'Manager Code/Manager Email *');
        $Excel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:B2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("50");
        /*$Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->applyFromArray(array(
             'type' => PHPExcel_Style_Fill::FILL_SOLID,
             'startcolor' => array(
                 'rgb' => 'eb3a12'
             )
         ));*/
        $Excel->getActiveSheet()->getStyle('A2:B2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('eb3a12');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename="user-manager-sample.xls"');
        header('Cache-Control: max-age=0');

        //$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($Excel, "Xls");
        $objWriter->save('php://output');
        exit;
    }
    public function manager_samplecsv()
    {
        //$this->load->library('PHPExcel_CI');
        //$Excel = new PHPExcel_CI;
        $Excel = new Spreadsheet();
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Manager_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        /*$Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
        $Excel->getActiveSheet()->mergeCells('A1:A1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()->setCellValue('A2', 'Manager Email ID*');
        $Excel->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:A2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");

        /*$Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('eb3a12');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename="manager_sample.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($Excel, "Xls");
        $objWriter->save('php://output');
        exit;
    }
    public function supervisor_samplecsv()
    {
        //$this->load->library('PHPExcel_CI');
        //$Excel = new PHPExcel_CI;
        $Excel = new Spreadsheet();
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Supervisor_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        /*$Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
        $Excel->getActiveSheet()->mergeCells('A1:A1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()->setCellValue('A2', 'Supervisor ID*');
        $Excel->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:A2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");

        /*$Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('eb3a12');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename="supervisor_sample.xls"');
        header('Cache-Control: max-age=0');

        //$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($Excel, "Xls");
        $objWriter->save('php://output');
        exit;
    }

    public function UploadTraineeXls($Encode_id)
    {
        $Message = '';
        $SuccessFlag = 1;
        $company_id = $this->security->xss_clean($this->input->post('company_id', TRUE));
        $assessment_id = base64_decode($Encode_id);
        $Error = '';
        $Error = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "CSV file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 1 || $highestColumnIndex > 1) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();

                    if ($Emp_code == '') {
                        continue;
                    }
                    $EmpId = $this->create_trinity_model->get_assessment_userid($company_id, $Emp_code);
                    if (count((array)$EmpId) == 0) {
                        $Message .= "Row No. $row, Employee Code does not exist. </br> ";
                        $SuccessFlag = 0;
                        continue;
                    }
                }
            }
            if ($SuccessFlag) {
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Emp_code == '') {
                        continue;
                    }
                    $EmpId = $this->create_trinity_model->get_assessment_userid($company_id, $Emp_code);
                    $UserId = $this->common_model->get_value('assessment_allow_users', 'id', " user_id =" . $EmpId->user_id . " AND assessment_id=" . $assessment_id);
                    if (count((array)$UserId) > 0) {
                        continue;
                    }
                    $Counter++;
                    $data = array(
                        'assessment_id' => $assessment_id,
                        'user_id' => $EmpId->user_id
                    );
                    $Inserted_id = $this->common_model->insert('assessment_allow_users', $data);
                }
                $Message = $Counter . " Trainee Map successfully.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function UploadManagerXls($Encode_id = '')
    {
        $Message = '';
        $SuccessFlag = 1;
        $Error = '';
        $company_id = $this->security->xss_clean($this->input->post('company_id', TRUE));
        $acces_management = $this->acces_management;
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            $ManagersArrray = array();
            if ($this->session->userdata('NewManagersArrray_session')) {
                $ManagersArrray = $this->session->userdata('NewManagersArrray_session');
            }
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "CSV file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 1 || $highestColumnIndex > 1) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Trainer_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Trainer_id == '') {
                        continue;
                    }
                    // $EmpId = $this->create_trinity_model->get_managerid($company_id, $Trainer_id);
                    $this->db->select('userid');
                    $this->db->from('company_usrs');
                    $this->db->like('email', $this->db->escape($Trainer_id));
                    if ($company_id != "") {
                        $this->db->where('company_id', $company_id);
                    }
                    $EmpId = $this->db->get()->row();

                    if (count((array)$EmpId) == 0) {
                        $Message .= "Row No. $row, Manager Email ID is Not Exist. </br> ";
                        $SuccessFlag = 0;
                        continue;
                    }
                }
            }
            if ($SuccessFlag) {
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Trainer_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Trainer_id == '') {
                        continue;
                    }
                    // Procedure Query 
                    $this->db->select('userid');
                    $this->db->from('company_usrs');
                    $this->db->like('email', $this->db->escape($Trainer_id));
                    if ($company_id != "") {
                        $this->db->where('company_id', $company_id);
                    }
                    $EmpId = $this->db->get()->row();
                    // Procdure Query

                    // $EmpId = $this->create_trinity_model->get_managerid($company_id, $Trainer_id);
                    if ($Encode_id != '') {
                        $UserId = $this->common_model->get_value('assessment_managers', 'id', " trainer_id =" . $EmpId->userid . " AND assessment_id=" . $assessment_id);
                        if (count((array)$UserId) > 0) {
                            continue;
                        }
                        $data = array(
                            'assessment_id' => $assessment_id,
                            'trainer_id' => $EmpId->userid
                        );
                        $Inserted_id = $this->common_model->insert('assessment_managers', $data);
                    } else {
                        if (in_array($EmpId->userid, $ManagersArrray)) {
                            continue;
                        } else {
                            array_push($ManagersArrray, $EmpId->userid);
                        }
                    }
                    $Counter++;
                }
                if ($Encode_id == '') {
                    $this->session->set_userdata('NewManagersArrray_session', $ManagersArrray);
                }
                $Message = $Counter . " Manager Map successfully.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function UploadSupervisorXls($Encode_id = '')
    {

        $Message = '';
        $SuccessFlag = 1;
        $Error = '';
        $company_id = $this->security->xss_clean($this->input->post('company_id', TRUE));
        $acces_management = $this->acces_management;
        if ($Encode_id != '') {
            $assessment_id = base64_decode($Encode_id);
        }
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            $SupervisorsArrray = array();
            if ($this->session->userdata('NewSupervisorsArrray_session')) {
                $SupervisorsArrray = $this->session->userdata('NewSupervisorsArrray_session');
            }
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "CSV file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 1 || $highestColumnIndex > 1) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Trainer_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Trainer_id == '') {
                        continue;
                    }

                    $this->db->select('userid');
                    $this->db->from('company_usrs');
                    $this->db->like('email', $this->db->escape($Trainer_id));
                    if ($company_id != "") {
                        $this->db->where('company_id', $company_id);
                    }
                    $EmpId = $this->db->get()->row();

                    // $EmpId = $this->create_trinity_model->get_managerid($company_id, $Trainer_id);
                    if (count((array)$EmpId) == 0) {
                        $Message .= "Row No. $row, Supervisor ID is Not Exist. </br> ";
                        $SuccessFlag = 0;
                        continue;
                    }
                }
            }
            if ($SuccessFlag) {
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Trainer_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Trainer_id == '') {
                        continue;
                    }
                    // Procedure query
                    $this->db->select('userid');
                    $this->db->from('company_usrs');
                    $this->db->like('email', $this->db->escape($Trainer_id));
                    if ($company_id != "") {
                        $this->db->where('company_id', $company_id);
                    }
                    $EmpId = $this->db->get()->row();
                    // Procedure query

                    // $EmpId = $this->create_trinity_model->get_managerid($company_id, $Trainer_id);
                    if ($Encode_id != '') {
                        $UserId = $this->common_model->get_value('assessment_supervisors', 'id', " trainer_id =" . $EmpId->userid . " AND assessment_id=" . $assessment_id);
                        if (count((array)$UserId) > 0) {
                            continue;
                        }
                        $data = array(
                            'assessment_id' => $assessment_id,
                            'trainer_id' => $EmpId->userid
                        );
                        $Inserted_id = $this->common_model->insert('assessment_supervisors', $data);
                    } else {
                        if (in_array($EmpId->userid, $SupervisorsArrray)) {
                            continue;
                        } else {
                            array_push($SupervisorsArrray, $EmpId->userid);
                        }
                    }
                    $Counter++;
                }
                if ($Encode_id == '') {
                    $this->session->set_userdata('NewSupervisorsArrray_session', $SupervisorsArrray);
                }
                $Message = $Counter . " Supervisor Map successfully.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function LoadViewModal($encoded_id, $en_user_id, $trainer_id)
    {
        $assessment_id = base64_decode($encoded_id);
        $user_id = base64_decode($en_user_id);
        $AssessmentData = $this->common_model->get_value('assessment_mst', 'assessment_type,assessor_dttm', 'id=' . $assessment_id);
        $company_id = $this->mw_session['company_id'];

        $RatingData = $this->common_model->get_value('assessment_trainer_result', '*', 'trainer_id=' . $trainer_id . ' AND user_id=' . $user_id . ' AND assessment_id=' . $assessment_id);

        $UserData = $this->common_model->get_value('device_users', 'user_id,concat(firstname," ",lastname) as username,email,avatar', 'company_id=' . $company_id . ' AND user_id=' . $user_id);
        $trainer_name = $this->common_model->get_value('company_users', 'userid,concat(first_name," ",last_name) as trainer_name', 'company_id=' . $company_id . ' AND userid=' . $trainer_id);
        $remarks_data = '';
        // $QuestionData = $this->create_trinity_model->LoadAssessmentQuestions($assessment_id);
        $this->db->select('art.id,art.question_id,art.is_default,aq.question,art.parameter_id');
        $this->db->from(' assessment_trans art');
        $this->db->join('assessment_question aq', 'aq.id=art.question_id', 'left');
        $this->db->where('art.assessment_id', $assessment_id);
        $QuestionData = $this->db->get()->result();

        $ass_result_id = '';
        $video_screen = '';
        $embed = '';
        $remarks = '';
        $your_rating = 0;
        $team_rating = 0;
        $cnt = 0;
        if (count((array)$RatingData) > 0) {
            $remarks = $RatingData->remarks;
        }

        $ScoreData = $this->create_trinity_model->get_your_rating($assessment_id, $user_id, $trainer_id);
        if (count((array)$ScoreData) > 0 && $ScoreData->total_rating != 0) {
            $your_rating = round($ScoreData->total_score / ($ScoreData->total_rating) * 100, 2);
            $cnt = 1;
        }
        $data['your_rating'] = $your_rating . '%';
        $total_rating = $this->create_trinity_model->get_team_rating($assessment_id, $user_id, $trainer_id);
        if (count((array)$total_rating) > 0) {
            $team_rating = round(($total_rating->total_rating + $your_rating) / ($total_rating->total_trainer + $cnt), 2);
            $data['team_rating'] = $team_rating . '%';
        } else {
            $data['team_rating'] = $your_rating . '%';
        }
        $data['trainer_id'] = $trainer_id;
        $data['remarks'] = $remarks;
        $data['question_remarks'] = $remarks_data;
        $data['ass_result_id'] = $ass_result_id;
        $data['UserData'] = $UserData;
        $data['trainer_name'] = $trainer_name;
        $data['Questions'] = $QuestionData;
        $data['company_id'] = $company_id;
        $data['assessment_id'] = $assessment_id;
        $data['assessment_type'] = $AssessmentData->assessment_type;

        $data['mode'] = (strtotime($AssessmentData->assessor_dttm) < strtotime(date('Y-m-d H:i:s')) ? 1 : 2);
        $data['user_id'] = $user_id;
        $this->load->view('create_trinity/ViewAssessmentModal', $data);
    }


    public function save_rating($rate_flag = '')
    {
        $Message = '';
        $SuccessFlag = 1;
        $your_rating = 0;
        $team_rating = 0;
        $cnt = 0;
        $trainer_id = $this->security->xss_clean($this->input->post('trainer_id'));
        $parameter_rating = $this->security->xss_clean($this->input->post('rating'));
        $question_id = $this->security->xss_clean($this->input->post('question_id'));
        $assessment_id = $this->security->xss_clean($this->input->post('assessment_id'));
        $user_id = $this->security->xss_clean($this->input->post('user_id'));
        $ass_result_id = $this->security->xss_clean($this->input->post('ass_result_id', true));
        if ($ass_result_id == "") {
            $ass_result_id = 0;
        }
        $que_remark = $this->security->xss_clean($this->input->post('que_remark', true));
        $remark_que = $this->security->xss_clean($this->input->post('remark_que', true));
        $Ratingdata = array(
            'result_id' => $ass_result_id,
            'assessment_id' => $assessment_id,
            'trainer_id' => $trainer_id,
            'user_id' => $user_id,
            'question_id' => $question_id
        );
        $para_array = array();
        $existpara = array();
        $Parameter = $this->common_model->get_value('assessment_trans', 'parameter_id', 'question_id=' . $question_id . ' AND assessment_id=' . $assessment_id);
        if (count((array)$Parameter) > 0 && $Parameter->parameter_id != '') {
            $existpara = explode(',', $Parameter->parameter_id);
        }
        if ($parameter_rating != '' && count((array)$parameter_rating) > 0) {
            foreach ($parameter_rating as $para_key => $rating) {
                $para_array[] = $para_key;
                $Ratingdata['parameter_id'] = $para_key;
                $Ratingdata['score'] = $rating;
                $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'parameter_id', 'question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND parameter_id=' . $para_key);
                if (count((array)$ISEXIST) > 0) {
                    $this->create_trinity_model->update_assessment_results_trans('assessment_results_trans', $question_id, $ass_result_id, $user_id, $trainer_id, $para_key, $Ratingdata);
                } else {
                    $this->common_model->insert('assessment_results_trans', $Ratingdata);
                }
            }
            $parameter = array_diff($existpara, $para_array);
            if (count((array)$parameter) > 0) {
                foreach ($parameter as $val) {
                    $Ratingdata['parameter_id'] = $val;
                    $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'parameter_id,score', 'question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND parameter_id=' . $val);
                    if (count((array)$ISEXIST) > 0) {
                        $Ratingdata['score'] = $ISEXIST->score;
                        $this->create_trinity_model->update_assessment_results_trans('assessment_results_trans', $question_id, $ass_result_id, $user_id, $trainer_id, $val, $Ratingdata);
                    } else {
                        $Ratingdata['score'] = 0;
                        $this->common_model->insert('assessment_results_trans', $Ratingdata);
                    }
                }
            }
        } else {
            $parameter = array_diff($existpara, $para_array);
            if (count((array)$parameter) > 0) {
                foreach ($parameter as $val) {
                    $Ratingdata['parameter_id'] = $val;
                    $Ratingdata['score'] = 0;
                    $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'parameter_id', 'question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND parameter_id=' . $val);
                    if (count((array)$ISEXIST) > 0) {
                        $this->create_trinity_model->update_assessment_results_trans('assessment_results_trans', $question_id, $ass_result_id, $user_id, $trainer_id, $val, $Ratingdata);
                    } else {
                        $this->common_model->insert('assessment_results_trans', $Ratingdata);
                    }
                }
            }
        }

        $ScoreData = $this->create_trinity_model->get_your_rating($assessment_id, $user_id, $trainer_id);
        if (count((array)$ScoreData) > 0 && $ScoreData->total_rating != 0) {
            $your_rating = round($ScoreData->total_score / ($ScoreData->total_rating) * 100, 2);
            $cnt = 1;
        }
        $total_rating = $this->create_trinity_model->get_team_rating($assessment_id, $user_id, $trainer_id);
        if (count((array)$total_rating) > 0) {
            $team_rating = round(($total_rating->total_rating + $your_rating) / ($total_rating->total_trainer + $cnt), 2);
        } else {
            $team_rating = $your_rating;
        }
        //        $data = array('remarks' => $que_remark, 'user_rating' => $your_rating);
        //$this->create_trinity_model->update_assessment_results('assessment_results',$company_id,$assessment_id,$user_id,$data);
        //        $this->common_model->update('assessment_results', 'id', $ass_result_id, $data);

        $trainer_data = array(
            'assessment_id' => $assessment_id,
            'user_id' => $user_id,
            'trainer_id' => $trainer_id,
            'remarks' => $que_remark,
            'user_rating' => $your_rating,
        );
        $trainer_rate = $this->common_model->get_value('assessment_trainer_result', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
        if (count((array)$trainer_rate) > 0) {
            $this->common_model->update('assessment_trainer_result', 'id', $trainer_rate->id, $trainer_data);
        } else {
            $this->common_model->insert('assessment_trainer_result', $trainer_data);
        }
        $remark_data = array(
            'result_id' => $ass_result_id,
            'assessment_id' => $assessment_id,
            'user_id' => $user_id,
            'trainer_id' => $trainer_id,
            'remarks' => $remark_que,
            'question_id' => $question_id
        );
        $trainer_question = $this->common_model->get_value('assessment_trainer_remarks', 'id', 'result_id=' . $ass_result_id . ' AND assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND question_id=' . $question_id);
        if (count((array)$trainer_question) > 0) {
            $this->common_model->update('assessment_trainer_remarks', 'id', $trainer_question->id, $remark_data);
        } else {
            $this->common_model->insert('assessment_trainer_remarks', $remark_data);
        }
        $cnt_rate = $this->common_model->get_value('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
        if ($rate_flag == 1 && count((array)$cnt_rate) == 0) {
            $qrate_data = array(
                'assessment_id' => $assessment_id,
                'user_id' => $user_id,
                'trainer_id' => $trainer_id
            );
            $this->common_model->insert('assessment_complete_rating', $qrate_data);
        }

        $Message = "Rating updated successfully.!";
        $Rdata['cnt_rate'] = count((array)$cnt_rate);
        $Rdata['your_rating'] = $your_rating . '%';
        $Rdata['team_rating'] = $team_rating . '%';

        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function save_retake()
    {
        $Message = '';
        $SuccessFlag = 1;
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $company_id = $this->input->post('company_id');
        $data = array('retake' => 1);
        $this->create_trinity_model->update_assessment_results('assessment_results', $company_id, $assessment_id, $user_id, $data);
        $Message = "Save successfully.!";
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    // public function add_sentences($sentence_keyword_id)
    // {
    //     $data['row_val'] = $sentence_keyword_id;
    //     $this->load->view('create_trinity/sentence_keyword_modal', $data);
    // }
    function search($array, $search_list)
    {
        $result = array();
        foreach ($array as $key => $value) {
            foreach ($search_list as $k => $v) {
                if (!isset($value[$k]) || $value[$k] != $v) {
                    continue 2;
                }
            }
            $result[] = $value;
        }
        return $result;
    }
    // public function datatable_subparameter_refresh()
    // {
    //     $html = '';
    //     $txn_id        = $this->input->post('txn_id');
    //     $assessment_type = $this->input->post('assessment_type');
    //     $parameter_id  = $this->input->post('parameter_id');
    //     $p2s2          = $this->input->post('p2s2');
    //     $p2s3          = $this->input->post('p2s3');
    //     $p6s8          = $this->input->post('p6s8');
    //     $p6s9          = $this->input->post('p6s9');
    //     $sub_parameter = json_decode($this->input->post('sub_parameter'), TRUE);

    //     if (isset($parameter_id) and $parameter_id != '') {
    //         // $sub_parameter_result  = $this->common_model->get_selected_values('subparameter_mst', 'id,description,has_sentences_keyword','status=1 AND parameter_id="'.$parameter_id.'"');
    //         $this->db->select('id,description,has_sentences_keyword');
    //         $this->db->from('subparameter_mst');
    //         $this->db->where('status', '1');
    //         if ($assessment_type == "2") {
    //             $sub_where =  ' id != 5 and parameter_id = "' . $parameter_id . '"  ';
    //             $this->db->where($sub_where);
    //         } else {
    //             $this->db->where('parameter_id', $parameter_id);
    //         }
    //         $this->db->where('status', 1);
    //         // $this->db->where('parameter_id', $parameter_id);
    //         $sub_parameter_result =  $this->db->get()->result();
    //         if (isset($sub_parameter_result) and count((array)$sub_parameter_result) > 0) {
    //             foreach ($sub_parameter_result as $s) {
    //                 $has_sentences_keyword = $s->has_sentences_keyword;

    //                 $disabled = '';
    //                 $p2s2 = 0;
    //                 $p2s3 = 0;
    //                 $p6s8 = 0;
    //                 $p6s9 = 0;
    //                 $search_p2s2 = array('txn_id' => $txn_id, 'parameter_id' => 2, 'subparameter_id' => 2);
    //                 $filter_p2s2 = $this->search($sub_parameter, $search_p2s2);
    //                 if (isset($filter_p2s2) and count((array)$filter_p2s2) > 0) {
    //                     $p2s2 = 1;
    //                 }
    //                 $search_p2s3 = array('txn_id' => $txn_id, 'parameter_id' => 2, 'subparameter_id' => 3);
    //                 $filter_p2s3 = $this->search($sub_parameter, $search_p2s3);
    //                 if (isset($filter_p2s3) and count((array)$filter_p2s3) > 0) {
    //                     $p2s3 = 1;
    //                 }
    //                 $search_p6s8 = array('txn_id' => $txn_id, 'parameter_id' => 6, 'subparameter_id' => 8);
    //                 $filter_p6s8 = $this->search($sub_parameter, $search_p6s8);
    //                 if (isset($filter_p6s8) and count((array)$filter_p6s8) > 0) {
    //                     $p6s8 = 1;
    //                 }
    //                 $search_p6s9 = array('txn_id' => $txn_id, 'parameter_id' => 6, 'subparameter_id' => 9);
    //                 $filter_p6s9 = $this->search($sub_parameter, $search_p6s9);
    //                 if (isset($filter_p6s9) and count((array)$filter_p6s9) > 0) {
    //                     $p6s9 = 1;
    //                 }
    //                 if ($parameter_id == 2 and $s->id == 2 and $p6s9 == 1) {
    //                     $disabled = 'disabled="disabled"';
    //                 }
    //                 if ($parameter_id == 6 and $s->id == 9 and $p2s2 == 1) {
    //                     $disabled = 'disabled="disabled"';
    //                 }
    //                 if ($parameter_id == 2 and $s->id == 3 and $p6s8 == 1) {
    //                     $disabled = 'disabled="disabled"';
    //                 }
    //                 if ($parameter_id == 6 and $s->id == 8 and $p2s3 == 1) {
    //                     $disabled = 'disabled="disabled"';
    //                 }

    //                 if (isset($sub_parameter)) {
    //                     $search_items = array('txn_id' => $txn_id, 'parameter_id' => $parameter_id, 'subparameter_id' => $s->id);
    //                     $filter_myarray = $this->search($sub_parameter, $search_items);
    //                     if (isset($filter_myarray) and count((array)$filter_myarray) > 0) {
    //                         $type_id          = $filter_myarray[0]['type_id'];
    //                         $sentence_keyword = $filter_myarray[0]['sentence_keyword'];
    //                         $html .= '<tr><td class="table-checkbox "><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
    // 						<input type="checkbox" class="checkboxes" name="chksp_list[]" value="' . $s->id . '" id="chksp_id' . $s->id . '" checked="checked" onchange="hide_unhide_keysent(' . $s->id . ',' . $has_sentences_keyword . ')" ' . $disabled . '/>
    // 						<span></span></label></td>
    // 						<td id="lblsp_id' . $s->id . '">' . $s->description . '</td>
    // 						<td>
    // 							<select id="type_id' . $s->id . '" name="type_id[]" class="form-control input-sm select2 hide" placeholder="Please select" style="width:100px;">
    // 							<option value="1" ' . (($type_id == 1) ? "selected" : "") . '>Sentence</option>
    // 							<option value="2" ' . (($type_id == 2) ? "selected" : "") . '>Keyword</option>
    // 							</select>
    // 						</td>
    // 						<!-- <td ><textarea id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide">' . $sentence_keyword . '</textarea></td> -->
    // 						<td>
    // 							<textarea readonly id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide">' . $sentence_keyword . '</textarea>
    // 							<a class="btn btn-success btn-sm" href="' . base_url() . 'create_trinity/add_sentences/' . $s->id . '" id="MybtnModal' . $s->id . '" class="hide btn btn-primary MybtnModal"
    // 						accesskey="" data-target="#Mymodalid" data-toggle="modal" style="margin-top:5px !important;">Add Sentence / Keyword </a>
    //                         </td>
    // 						</tr>';
    //                     } else {
    //                         $html .= '<tr><td class="table-checkbox "><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
    // 						<input type="checkbox" class="checkboxes" name="chksp_list[]" value="' . $s->id . '" id="chksp_id' . $s->id . '" onchange="hide_unhide_keysent(' . $s->id . ',' . $has_sentences_keyword . ')" ' . $disabled . '/>
    // 						<span></span></label></td>
    // 						<td id="lblsp_id' . $s->id . '">' . $s->description . '</td>
    // 						<td>
    // 							<select id="type_id' . $s->id . '" name="type_id[]" class="form-control input-sm select2 hide" placeholder="Please select" style="width:100px;">
    // 							<option value="1">Sentence</option>
    // 							<option value="2">Keyword</option>
    // 							</select>
    // 						</td>
    // 						<!-- <td ><textarea id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea></td> -->
    // 						<td>
    // 							<textarea readonly id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea>
    // 							<a class="btn btn-success btn-sm" href="' . base_url() . 'create_trinity/add_sentences/' . $s->id . '" id="MybtnModal' . $s->id . '" class="hide btn btn-primary MybtnModal"
    // 						accesskey="" data-target="#Mymodalid" data-toggle="modal" style="margin-top:5px !important;">Add Sentence / Keyword </a>
    // 						</td>
    // 						</tr>';
    //                     }
    //                 } else {
    //                     $html .= '<tr><td class="table-checkbox "><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
    // 						<input type="checkbox" class="checkboxes" name="chksp_list[]" value="' . $s->id . '" id="chksp_id' . $s->id . '" onchange="hide_unhide_keysent(' . $s->id . ',' . $has_sentences_keyword . ')" ' . $disabled . '/>
    // 						<span></span></label></td>
    // 						<td id="lblsp_id' . $s->id . '">' . $s->description . '</td>
    // 						<td >
    // 							<select id="type_id' . $s->id . '" name="type_id[]" class="form-control input-sm select2 hide" placeholder="Please select" style="width:100px;">
    // 							<option value="1">Sentence</option>
    // 							<option value="2">Keyword</option>
    // 							</select>
    // 						</td>
    // 						<!--<td ><textarea id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea></td> -->
    // 						<td>
    // 							<textarea readonly id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea>
    // 							<a class="btn btn-success btn-sm" href="' . base_url() . 'create_trinity/add_sentences/' . $s->id . '" id="MybtnModal' . $s->id . '" class="hide btn btn-primary MybtnModal"
    // 						accesskey="" data-target="#Mymodalid" data-toggle="modal" style="margin-top:5px !important;">Add Sentence / Keyword </a>
    // 						</td>
    // 						</tr>';
    //                 }
    //             }
    //         } else {
    //             $html .= '<tr><td colspan="4">Sub parameters not available.</td></tr>';
    //         }
    //     } else {
    //         $html .= '<tr><td colspan="4">To display sub-parameters, first choose parameter from the above list.</td></tr>';
    //     }
    //     $json['html'] = $html;
    //     echo json_encode($json);
    // }

    // public function add_questions($encoded_id = '')
    // {
    //     if ($encoded_id != '') {
    //         $data['assessment_id'] = base64_decode($encoded_id);
    //         $Ass_dataset = $this->common_model->get_value('assessment_mst', 'company_id,assessment_type', 'id=' . $data['assessment_id']);
    //         $data['assessment_type'] = $Ass_dataset->assessment_type;
    //     }
    //     $data['AddEdit'] = 'A';
    //     $data['edit_id'] = '';
    //     $this->load->view('create_trinity/questions_modal', $data);
    // }

    // public function edit_questions($row_id)
    // {
    //     $data['AddEdit'] = 'E';
    //     $data['edit_id'] = $row_id;
    //     $this->load->view('create_trinity/questions_modal', $data);
    // }

    // public function load_question_table($encoded_id = '')
    // {
    //     if ($encoded_id != '') {
    //         $assessment_id = base64_decode($encoded_id);
    //     } else {
    //         $assessment_id = '';
    //     }
    //     $dtSearchColumns = array('a.id', 'a.question', 'a.weightage',  'a.read_timer', 'a.response_timer',);
    //     $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
    //     $dtWhere = $DTRenderArray['dtWhere'];
    //     $dtOrder = $DTRenderArray['dtOrder'];
    //     $dtLimit = $DTRenderArray['dtLimit'];
    //     $company_id = $this->mw_session['company_id'];
    //     if ($company_id != '') {
    //         if ($dtWhere <> '') {
    //             $dtWhere .= " AND a.company_id  = " . $company_id;
    //         } else {
    //             $dtWhere .= " WHERE a.company_id  = " . $company_id;
    //         }
    //     }
    //     $AddEdit = $this->input->get('AddEdit');
    //     if ($AddEdit == 'A') {
    //         if ($assessment_id != "") {
    //             if ($dtWhere <> '') {
    //                 $dtWhere .= " AND a.id NOT IN (select question_id FROM assessment_trans where assessment_id= $assessment_id)";
    //             } else {
    //                 $dtWhere .= " WHERE a.id NOT IN (select question_id FROM assessment_trans where assessment_id= $assessment_id)";
    //             }
    //         }
    //         $Selected_QuestionArray = $this->input->get('Selected_QuestionArray');
    //         if ($Selected_QuestionArray != "") {
    //             if ($dtWhere <> '') {
    //                 $dtWhere .= " AND a.id NOT IN (" . $Selected_QuestionArray . ")";
    //             } else {
    //                 $dtWhere .= " WHERE a.id NOT IN (" . $Selected_QuestionArray . ")";
    //             }
    //         }
    //     }
    //     // $assessment_type = $this->input->get('assessment_type');
    //     // if ($assessment_type != "") {
    //     // 	if ($dtWhere <> '') {
    //     //     	$dtWhere .= " AND a.assessment_type  = " . $assessment_type;
    //     // 	}else{
    //     // 		$dtWhere .= " WHERE a.assessment_type  = " . $assessment_type;
    //     // 	}
    //     // }
    //     // $question_type = $this->input->get('question_type') !=null ? $this->input->get('question_type') : '';
    //     // if ($question_type != "") {
    //     // 	if ($dtWhere <> '') {
    //     //     	$dtWhere .= " AND a.is_situation  = " . $question_type;
    //     // 	}else{
    //     // 		$dtWhere .= " WHERE a.is_situation  = " . $question_type;
    //     // 	}
    //     // }

    //     $DTRenderArray = $this->create_trinity_model->load_question_table($dtWhere, $dtOrder, $dtLimit);
    //     $output = array(
    //         "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
    //         "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
    //         "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
    //         "aaData" => array()
    //     );
    //     $dtDisplayColumns = array('id', 'question', 'weightage', 'read_timer', 'response_timer', 'checkbox');
    //     foreach ($DTRenderArray['ResultSet'] as $dtRow) {
    //         $row = array();
    //         $TotalHeader = count((array)$dtDisplayColumns);
    //         for ($i = 0; $i < $TotalHeader; $i++) {
    //             if ($dtDisplayColumns[$i] == "checkbox") {
    //                 if ($AddEdit == 'A') {
    //                     $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
    //                                 <input type="checkbox" class="checkboxes" name="Question_list[]" value="' . $dtRow['id'] . '"
    //                                     id="ck_Question_id' . $dtRow['id'] . '" onclick="selected_questions(this.value)" />
    //                                 <span></span>
    //                         </label>';
    //                 } else {
    //                     $row[] = '<label class="radio-list">
    //                                 <input type="radio" class="radio" name="rd_question_id" value="' . $dtRow['id'] . '"
    //                                     id="ck_Question_id' . $dtRow['id'] . '" onclick="selected_questions(this.value)" />
    //                                 <span></span>
    //                         </label>';
    //                 }
    //             } else if ($dtDisplayColumns[$i] != ' ') {
    //                 $row[] = $dtRow[$dtDisplayColumns[$i]];
    //             }
    //         }
    //         $output['aaData'][] = $row;
    //     }
    //     //VAPT CHANGE POINT 3 -- START
    //     foreach ($output as $outkey => $outval) {
    //         if ($outkey !== 'aaData') {
    //             $output[$outkey] = $this->security->xss_clean($outval);
    //         }
    //     }
    //     //VAPT CHANGE POINT 3 -- END
    //     echo json_encode($output);
    // }
    public function send_notification($action = 1, $assess_id)
    {
        $assessment_id = base64_decode($assess_id);
        $pattern[0] = '/\[SUBJECT\]/';
        $pattern[1] = '/\[ASSESSMENT_NAME\]/';
        $pattern[2] = '/\[ASSESSMENT_LINK\]/';
        if ($action == 1) {
            $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_assessment_alert'");
            $invitation_id = $this->input->post('Mapping_all');
            $pattern[3] = '/\[TRAINER_NAME\]/';
            $pattern[4] = '/\[EXPIRE_DATE\]/';
            $pattern[5] = '/\[TRAINEE_NAME\]/';
        } else {
            $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_assessment_trainee_alert'");
            $invitation_id = $this->input->post('Participant_all');
            $pattern[3] = '/\[NAME\]/';
            $pattern[4] = '/\[DATE_TIME\]/';
        }

        //$assessment_users = $this->create_trinity_model->get_assessment_users($assessment_id);
        $assessment_set = $this->common_model->get_value('assessment_mst', 'assessment,assessor_dttm,start_dttm,end_dttm', "id=" . $assessment_id);
        $SuccessFlag = 1;
        $Message = '';
        if (count((array)$emailTemplate) > 0) {
            $subject = $emailTemplate->subject;
            $replacement[0] = $subject;
            $replacement[1] = $assessment_set->assessment;
            foreach ($invitation_id as $id) {
                if ($action == 1) {
                    $ManagerSet = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name,email,company_id', "userid=" . $id);

                    $replacement[2] = '<a target="_blank" style="display: inline-block;
                    width: 200px;
                    height: 20px;
                    background: #db1f48;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px;
                    color: white;
                    border: 1px solid black;
                    text-decoration:none;
                    font-weight: bold;" href="' . base_url() . 'assessment/view/' . $assess_id . '/2">View Assignment</a>';
                    $replacement[3] = $ManagerSet->trainer_name;
                    $replacement[4] = date("d-m-Y h:i a", strtotime($assessment_set->assessor_dttm));
                    $replacement[5] = ''; //
                    $ToName = $ManagerSet->trainer_name;
                    $email_to = $ManagerSet->email;
                    $Company_id = $ManagerSet->company_id;
                } else {

                    $AllowSet = $this->common_model->get_value('assessment_allow_users', 'user_id', '  id=' . $id);
                    if (count((array)$AllowSet) > 0) {
                        $UserData = $this->common_model->get_value('device_users', 'company_id,concat(firstname," ",lastname) as trainee_name,email', '  user_id=' . $AllowSet->user_id);
                        $ToName = $UserData->trainee_name;
                        $email_to = $UserData->email;
                        $Company_id = $UserData->company_id;
                        $replacement[2] = '<a target="_blank" style="display: inline-block;
						background: #eb3a12;
						padding: .45rem 1rem;
						box-sizing: border-box;
						border: none;
						border-radius: 3px;
						color: #fff;
						text-align: center;
						font-family: Lato,Arial,sans-serif;
						font-weight: 400;
						font-size: 1em;
						text-decoration:none;
						line-height: initial;" href="https://web.awarathon.com">View Assignment</a>';
                        $replacement[3] = $UserData->trainee_name;
                        $replacement[4] = date("d-m-Y h:i a", strtotime($assessment_set->start_dttm));
                    }
                }
                $message = $emailTemplate->message;
                $body = preg_replace($pattern, $replacement, $message);
                //$ToName ="Sameer Mansuri";
                //$email_to ="sameer@mworks.in";


                $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                if ($ReturnArray['sendflag']) {
                    $Message = "Notification send successfully.";
                } else {
                    $Message .= "Error while sending email,Plese try again..";
                    $Message .= '<br/>' . $ReturnArray['sendflag'];
                    $SuccessFlag = 0;
                }
            }
        } else {
            $SuccessFlag = 0;
            $Message = 'Email Template not defined,Contact Adminstrator for technical support';
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function send_notification_trainee($assess_id)
    {
        $assessment_manager_id = $this->input->post('Mapping_all');
        $assessment_id = base64_decode($assess_id);
        $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_assessment_alert'");
        // $assessment_users = $this->create_trinity_model->get_assessment_users($assessment_id);

        $this->db->select("w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.firstname,u.email,w.company_id,w.assessment_id,am.assessment,am.assessor_dttm");
        $this->db->from("device_users as u");
        $this->db->join('assessment_results as w', 'w.user_id=u.user_id', 'left');
        $this->db->join('assessment_mst as am', 'w.assessment_id=am.id', 'left');
        $this->db->where('w.assessment_id', $assess_id);
        $this->db->group_by("w.user_id");
        $assessment_users = $this->db->get()->result();

        $SuccessFlag = 1;
        $Message = '';
        if (count((array)$emailTemplate) > 0) {
            foreach ($assessment_manager_id as $id) {
                $TrainerData = $this->common_model->get_value('assessment_managers', 'trainer_id', " id=" . $id);
                $Trainer = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name,email,company_id', "userid=" . $TrainerData->trainer_id);

                if (count((array)$assessment_users) > 0) {
                    foreach ($assessment_users as $user) {
                        $pattern[0] = '/\[SUBJECT\]/';
                        $pattern[1] = '/\[TRAINER_NAME\]/';
                        $pattern[2] = '/\[ASSESSMENT_NAME\]/';
                        $pattern[3] = '/\[TRAINEE_NAME\]/';
                        $pattern[4] = '/\[ASSESSMENT_LINK\]/';
                        $pattern[5] = '/\[EXPIRE_DATE\]/';

                        $subject = $emailTemplate->subject;
                        $replacement[0] = $subject;
                        $replacement[1] = $Trainer->trainer_name;
                        $replacement[2] = $user->assessment;
                        $replacement[3] = $user->name;
                        $replacement[4] = '<a href="' . base_url() . 'create_trinity/view/' . $assess_id . '">' . base_url() . 'assessment_create' . '</a>';
                        $replacement[5] = date("d-m-Y H:i:s", strtotime($user->assessor_dttm));


                        $message = $emailTemplate->message;
                        $body = preg_replace($pattern, $replacement, $message);
                        //$ToName ="Sameer Mansuri";
                        //$recipient ="sameer@mworks.in";
                        $ToName = $Trainer->trainer_name;
                        $recipient = $Trainer->email;
                        $Company_id = $Trainer->company_id;
                        $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $recipient, $subject, $body);
                        if ($ReturnArray['sendflag']) {
                            $Message = "Notification send successfully.";
                        } else {
                            $Message .= "Error while sending email,Plese try again..";
                            $Message .= '<br/>' . $ReturnArray['sendflag'];
                            $SuccessFlag = 0;
                        }
                    }
                } else {
                    $Message = "No any Trainee data founds.";
                }
            }
        } else {
            $SuccessFlag = 0;
            $Message = 'Email Template not defined,Contact Adminstrator for technical support';
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function remove_assessmentuser($id)
    {
        $user_id = base64_decode($id);
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'success';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $ReturnFlag = true;
            if ($ReturnFlag) {
                $this->common_model->delete('assessment_results', 'user_id', $user_id);
                $this->common_model->delete('assessment_results_trans', 'user_id', $user_id);
                $message = "Assessment user deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Assessment User cannot be deleted.!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
    public function add_mappassessor()
    {
        $user_id = $this->input->post('user_id');
        $user_row = $this->input->post('user_row');
        $assessment_id = base64_decode($this->input->post('assessment_id'));
        $assessor_users = $this->create_trinity_model->get_managers($assessment_id, $user_id);
        //          $data['RegionList'] = $this->create_trinity_model->get_TrainerRegionList($company_id);
        $RegionList = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1', 'region_name');
        $success = 1;
        $Msg = '';
        $lchtml = '';
        if ($success) {
            $lchtml .= '<tr id="usr_' . $user_row . '">';
            $lchtml .= '<td>' . $assessor_users[0]->userid . '</td>';
            $lchtml .= '<td>' . $assessor_users[0]->name . '</td>';
            $lchtml .= '<td>' . $assessor_users[0]->designation . '</td>';
            $lchtml .= '<td>
                     <select id="trainer_region' . $user_row . '" name="trainer_region' . $user_row . '[]" class="form-control input-sm select2" placeholder="Please select" multiple="" style="width:80%" >';
            if (count((array)$RegionList) > 0) {
                foreach ($RegionList as $key => $value) {
                    $lchtml .= ' <option value="' . $value->id . '">' . $value->region_name . '</option>';
                }
            }
            $lchtml .= '</select></td>';
            $lchtml .= '<td><button class="btn btn-danger btn-xs btn-mini " type="button" onclick="remove_userrow(' . $user_row . ')" ><i class="fa fa-times"></i></button></td>';
            $lchtml .= '<input type="hidden" value="' . $assessor_users[0]->userid . '" id="trainer_id' . $user_row . '" name="trainer_id[]">';
            $lchtml .= '<input type="hidden" value="' . $user_row . '" id="row_id' . $user_row . '" name="row_id[]">';
            $lchtml .= '</tr>';
        }
        $response['html'] = $lchtml;
        $response['Success'] = $success;
        $response['Msg'] = $Msg;
        echo json_encode($response);
    }
    public function RemoveUserMappingPopup($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['user_id_array'] = $this->input->post('UserMapping_all');
        // $data['Trainer'] = $this->create_trinity_model->get_assessment_manager($company_id, $data['assessment_id']);

        $this->db->select("amu.trainer_id,CONCAT(cu.first_name,' ',cu.last_name) as name");
        $this->db->from("assessment_mapping_user as amu");
        $this->db->join('company_users as cu', 'cu.userid=amu.trainer_id', 'left');
        $this->db->where('amu.assessment_id', $data['assessment_id']);
        $this->db->group_by('amu.trainer_id');
        $data['Trainer'] = $this->db->get()->result();
        $this->load->view('create_trinity/UserManagersConfirmBoxModal', $data);
    }

    public function RemoveUserMappingManager($Encode_id)
    {
        $assessment_id = base64_decode($Encode_id);
        $Message = '';
        $SuccessFlag = 1;
        $user_id_array = $this->input->post('user_id_array');
        $assessment_id = $this->input->post('assessment_id');
        $trainer_id_array = $this->input->post('trainer_id');
        if (count((array)$trainer_id_array) > 0) {
            foreach ($trainer_id_array as $trainer_id) {
                foreach ($user_id_array as $user_id) {
                    $lcwhere = 'assessment_id =' . $assessment_id . ' AND trainer_id=' . $trainer_id . ' AND user_id=' . $user_id;
                    $this->common_model->delete_whereclause('assessment_mapping_user', $lcwhere);
                }
            }
            $Message = "User remove successfully.!";
        } else {
            $Message = "Please select Manager...!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function export_assessment()
    {

        $dtWhere = "where ar.question_id !=''";

        $assessment_type = $this->input->get('assessment_type');
        if ($assessment_type != "") {
            $dtWhere .= " AND am.assessment_type  = " . $assessment_type;
        }
        $question_type = $this->input->get('question_type') != null ? $this->input->get('question_type') : '';
        if ($question_type != "") {
            $dtWhere .= " AND am.is_situation  = " . $question_type;
        }
        $assessment_id = $this->input->post('id', TRUE);
        if ($assessment_id != "") {
            $id_list = implode(',', $assessment_id);
            $dtWhere .= " AND am.id IN(" . $id_list . ")";
        }
        //        $filter_status = $this->input->get('filter_status');
        //        if ($filter_status != "") {
        //            $dtWhere .= " AND a.status  = " . $filter_status;
        //        }
        $action_type = $this->input->post('action_status');
        $assesment_set = $this->create_trinity_model->assessment_export($dtWhere);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
            )
        );
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        if ($action_type == 5) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Assessment Name')
                ->setCellValue('B1', 'User Name')
                ->setCellValue('C1', 'Question No')
                ->setCellValue('D1', 'Parameter Name');
            $Tranier_set = $this->create_trinity_model->get_assessment_trainer($id_list);
            if (count((array)$Tranier_set) > 0) {
                $c = 'E';
                foreach ($Tranier_set as $value) {
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($c . '1', $value->name . ' - Manager');
                    $objPHPExcel->getActiveSheet()->getColumnDimension($c)->setWidth(30);
                    $c++;
                }
                foreach ($Tranier_set as $value) {
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($c . '1', $value->name . ' - Comments');
                    $objPHPExcel->getActiveSheet()->getColumnDimension($c)->setWidth(30);
                    $c++;
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($c . '1', $value->name . ' - Overall Comments');
                    $objPHPExcel->getActiveSheet()->getColumnDimension($c)->setWidth(30);
                    $c++;
                }
                $objPHPExcel->getActiveSheet()->getStyle('A1:' . $c . '1')->applyFromArray($styleArray_header);
            }
            $i = 1;
        } else {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', 'Assessment Name')
                ->setCellValue('B2', 'User Name')
                ->setCellValue('C2', 'Email')
                ->setCellValue('D2', 'Question No')
                ->setCellValue('E2', 'Question')
                ->setCellValue('F2', 'Video Url');
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleArray_header);
            $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleArray_body);
            $i = 2;
        }
        $lastass_id = '';
        $user_id = '';
        $q1 = 0;
        foreach ($assesment_set as $assess) {
            $i++;
            if ($lastass_id != $assess->id || $user_id != $assess->user_id) {
                $q1 = 1;
                $lastass_id = $assess->id;
                $user_id = $assess->user_id;
            }

            if ($action_type == 5) {
                $question_id = $assess->question_id;
                $parameter_id = $assess->parameter_id;
                $assess_id = $assess->id;
                $Parameter_set =  $this->common_model->get_selected_values('parameter_mst', 'id,description', "id IN(" . $parameter_id . ")", 'id');
                $Tranier_ResultSet = $this->create_trinity_model->get_assessment_trainer_score($assess_id, $user_id);
                $total_parameter = count((array)$Parameter_set);
                $pi = 0;
                foreach ($Parameter_set as $value2) {
                    $pi++;
                    $parameter_id = $value2->id;
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $assess->assessment)
                        ->setCellValue("B$i", $assess->username . '-' . $assess->user_id)
                        ->setCellValue("C$i", 'Q' . $q1)
                        ->setCellValue("D$i", $value2->description);
                    $c = 'E';
                    foreach ($Tranier_set as $value) {
                        // echo $value->trainer_id;
                        // if(isset($Tranier_ResultSet[$value->trainer_id])){
                        //     echo "<pre>";
                        //     echo $question_id.'<br/>';
                        //     print_r($Tranier_ResultSet[$value->trainer_id][$question_id]);
                        //     exit;
                        // }
                        if (isset($Tranier_ResultSet[$value->trainer_id][$question_id][$parameter_id])) {
                            $score = $Tranier_ResultSet[$value->trainer_id][$question_id][$parameter_id]->percentage;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($c . $i, $score);
                        } else {
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($c . $i, '');
                        }
                        $c++;
                    }
                    foreach ($Tranier_set as $value) {
                        if (isset($Tranier_ResultSet[$value->trainer_id][$question_id][$parameter_id])) {
                            $Obj = $Tranier_ResultSet[$value->trainer_id][$question_id][$parameter_id];
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($c . $i, $Obj->question_remarks);
                            $c++;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($c . $i, $Obj->overall_comments);
                            $c++;
                        } else {
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($c . $i, '');
                            $c++;
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue($c . $i, '');
                        }
                    }
                    if ($pi != $total_parameter) {
                        $i++;
                    }
                }
                //$objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->applyFromArray($styleArray_body);
            } else {
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $assess->assessment)
                    ->setCellValue("B$i", $assess->username . '-' . $assess->user_id)
                    ->setCellValue("C$i", $assess->email)
                    ->setCellValue("D$i", 'Q' . $q1)
                    ->setCellValue("E$i", $assess->question)
                    ->setCellValue("F$i", 'https://player.vimeo.com/video/' . $assess->vimeo_uri);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:E$i")->applyFromArray($styleArray_body);
            }
            $q1++;
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        //exit;
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if ($action_type == 5) {
            header('Content-Disposition: attachment;filename="Assessment_Rating_Manual.xlsx"');
        } else {
            //header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Assessment_Video_url.xlsx"');
        }
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

    public function confirm_upload_user_manager($Encode_id)
    {
        $Message = '';
        $SuccessFlag = 1;
        $company_id = $this->input->post('company_id', TRUE);
        $assessment_id = base64_decode($Encode_id);
        $Error = '';
        $Error = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "CSV file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 1 || $highestColumnIndex > 2) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $manager_code = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    //print_r($Emp_code);
                    //exit;
                    if ($Emp_code == '') {
                        continue;
                    }
                    $EmpId = $this->create_trinity_model->get_assessment_userid($company_id, $Emp_code);
                    if (count((array)$EmpId) == 0) {
                        $Message .= "Row No. $row, Employee does not exist.. </br> ";
                        //$SuccessFlag = 0;
                        continue;
                    } elseif ($manager_code == '') {
                        $Message .= "Row No. $row, Please enter manager code/Email </br> ";
                        $SuccessFlag = 0;
                        continue;
                    } elseif ($manager_code != 'Vacant') {
                        $Manager_data = $this->create_trinity_model->get_assessment_managerid($manager_code);
                        if (count((array)$Manager_data) == 0) {
                            $Message .= "Row No. $row, Invalid Manager Code/Email </br> ";
                            //$SuccessFlag = 0;
                            continue;
                        }
                    }
                }
            }
            if ($SuccessFlag) {
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $manager_code = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    if ($Emp_code == '') {
                        continue;
                    }
                    $Trainee_set = $this->create_trinity_model->get_assessment_userid($company_id, $Emp_code);
                    if (count((array)$Trainee_set) == 0) {
                        //$Message .= "Row No. $row, Employee does not exist.. </br> ";
                        //$SuccessFlag = 0;
                        continue;
                    }
                    $trainee_id = $Trainee_set->user_id;
                    $Traineeallow_set = $this->common_model->get_value('assessment_allow_users', 'id', " user_id = $trainee_id AND assessment_id=" . $assessment_id);
                    if (count((array)$Traineeallow_set) == 0) {
                        $data = array(
                            'assessment_id' => $assessment_id,
                            'user_id' => $trainee_id
                        );
                        $this->common_model->insert('assessment_allow_users', $data);
                    }
                    $Trainer_set = $this->create_trinity_model->get_assessment_managerid($manager_code);
                    if (count((array)$Trainer_set) > 0) {
                        $trainer_id = $Trainer_set->userid;
                        $Trainerallow_set = $this->common_model->get_value('assessment_managers', 'id', " trainer_id = $trainer_id AND assessment_id=" . $assessment_id);
                        if (count((array)$Trainerallow_set) == 0) {
                            $data = array(
                                'assessment_id' => $assessment_id,
                                'trainer_id' => $trainer_id
                            );
                            $this->common_model->insert('assessment_managers', $data);
                        }
                        $Mapping_set = $this->common_model->get_value('assessment_mapping_user', 'id', " trainer_id =$trainer_id AND user_id = $trainee_id 
						 AND assessment_id=" . $assessment_id);
                        if (count((array)$Mapping_set) == 0) {
                            $data = array(
                                'assessment_id' => $assessment_id,
                                'trainer_id' => $trainer_id,
                                'user_id' => $trainee_id
                            );
                            $this->common_model->insert('assessment_mapping_user', $data);
                        }
                    }
                    $Counter++;
                }
                if ($Message != '') {
                    $SuccessFlag = 2;
                    $Message = $Counter . " User Manager data mapped successfully.<br/>" . $Message;
                } else {
                    $Message = $Counter . " User Manager data mapped successfully.";
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    //  pdf preview for assessment_create user
    // public function reports_preview($assessment_id, $flag)
    // {
    //     $_company_id = $this->mw_session['company_id'];
    //     $user_id = $this->mw_session['user_id'];

    //     if ($_company_id == "") {
    //         echo "Invalid parameter passed";
    //     } else {
    //         if ($flag == 1) {
    //             $bulk_assessment_id = base64_decode($assessment_id);
    //             //GET COMPANY DETAILS
    //             $company_name = '';
    //             $company_logo = 'assets/images/Awarathon-Logo.png';
    //             $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $_company_id . '"');
    //             if (isset($company_result) and count((array)$company_result) > 0) {
    //                 $company_name = $company_result->company_name;
    //                 $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/' . $company_result->company_logo : '';
    //             }
    //             $data['company_name'] = $company_name;
    //             $data['company_logo'] = $company_logo;

    //             $data['participant_name'] = "";
    //             $data['manager_name'] = " ";
    //             $data['overall_score'] = "";
    //             $data['manager_comments_list']  = "";
    //             $data['overall_comments'] = "";
    //             $data['parameter_score'] = "0";
    //             $data['show_ranking'] = '1';

    //             if ($bulk_assessment_id != "") {
    //                 // $assessment_trans = $this->create_trinity_model->LoadAssessmentQuestions($bulk_assessment_id);

    //                 $this->db->select('art.id,art.question_id,art.is_default,aq.question,art.parameter_id');
    //                 $this->db->from("assessment_trans art");
    //                 $this->db->join('assessment_question aq', 'aq.id = art.question_id', 'left');
    //                 $this->db->where('art.assessment_id', $assessment_id);
    //                 $assessment_trans = $this->db->get()->result();

    //                 // $parameter_subparameter_trans = $this->create_trinity_model->LoadParameterSubParameter($bulk_assessment_id);

    //                 $this->db->select("art.question_id,aq.question,art.language_id,art.txn_id,art.ai_methods,art.parameter_id,p.description as parameter_name,art.parameter_label_id,l.description as parameter_label_name,art.sub_parameter_id,s.description as sub_parameter_name,art.type_id,IF(art.type_id=2,'Keyword',IF(art.type_id=1,'Sentence','')) as type_name,art.sentence_keyword,art.parameter_weight,IFNULL(s.has_sentences_keyword,0) as has_sentences_keyword");
    //                 $this->db->from("assessment_trans_sparam art");
    //                 $this->db->join('assessment_question aq', 'aq.id = art.question_id', 'left');
    //                 $this->db->join('parameter_mst p', 'p.id = art.parameter_id', 'left');
    //                 $this->db->join('parameter_label_mst l', 'l.id = art.parameter_label_id', 'left');
    //                 $this->db->join('subparameter_mst s', 's.id = art.sub_parameter_id', 'left');
    //                 $this->db->where("art.assessment_id", $assessment_id);
    //                 $this->db->order_by("art.id", 'ASC');
    //                 $this->db->order_by("art.txn_id", 'ASC');
    //                 $parameter_subparameter_trans = $this->db->result();
    //                 // $final_data['sub_parameter_data'] = $this->create_trinity_model->get_parameter_value($bulk_assessment_id);
    //             }
    //             foreach ($assessment_trans as $val) {
    //                 $question_id_set[] = $val->question_id;
    //             }
    //             $sub_parameter_main = array();
    //             $k = 0;
    //             foreach ($parameter_subparameter_trans as $dt) {
    //                 $sub_parameter_main[$dt->question_id][$k] = $dt;
    //                 $k++;
    //             }
    //             $temp_id = array();
    //             $parameter_label_arr = array();
    //             $question_sentance = array();
    //             if (count((array)$question_id_set) > 0) {
    //                 foreach ($question_id_set as $key => $question_id) {
    //                     if (in_array($question_id, $temp_id)) {
    //                         continue;
    //                     }
    //                     $temp_id[] = $question_id;

    //                     foreach ($sub_parameter_main[$question_id] as $t) {

    //                         if ($t->type_id == 1) {
    //                             $sentence_keyword[] = $t->sentence_keyword;
    //                         }
    //                         if (in_array($t->parameter_label_name, $parameter_label_arr)) {
    //                             continue;
    //                         } else {
    //                             $parameter_label_arr[]  =  $t->parameter_label_name;
    //                         }
    //                     }

    //                     array_push($question_sentance, array(
    //                         "question"             => $t->question,
    //                         "senetence_keyword"    => $sentence_keyword,
    //                     ));
    //                     $sentence_keyword = [];
    //                 }
    //             }

    //             $data['parameter_label'] = $parameter_label_arr;
    //             $data['question_sentance'] = $question_sentance;
    //             $data['flag'] = $flag;
    //             // $this->load->library('Pdf_Library');
    //             $htmlContent =  $this->load->view('create_trinity/report_preview', $data, true);
    //         } else {
    //             //GET COMPANY DETAILS
    //             $company_name = '';
    //             $company_logo = 'assets/images/Awarathon-Logo.png';
    //             $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="' . $_company_id . '"');
    //             if (isset($company_result) and count((array)$company_result) > 0) {
    //                 $company_name = $company_result->company_name;
    //                 $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/' . $company_result->company_logo : '';
    //             }
    //             $data['company_name'] = $company_name;
    //             $data['company_logo'] = $company_logo;

    //             $data['participant_name'] = "";
    //             $data['manager_name'] = " ";
    //             $data['overall_score'] = "";
    //             $data['manager_comments_list']  = "";
    //             $data['overall_comments'] = "";
    //             $data['parameter_score'] = "0";


    //             if ($assessment_id != "") {
    //                 $assessment_trans = $this->create_trinity_model->LoadAssessmentQuestions_temp($assessment_id);
    //                 // $parameter_subparameter_trans = $this->create_trinity_model->LoadParameterSubParameter_temp($assessment_id);
    //                 $this->db->select("art.question_id,aq.question,art.language_id,art.txn_id,art.ai_methods,art.parameter_id,p.description as parameter_name,art.parameter_label_id,l.description as parameter_label_name,art.sub_parameter_id,s.description as sub_parameter_name,art.type_id,IF(art.type_id=2,'Keyword',IF(art.type_id=1,'Sentence','')) as type_name,art.sentence_keyword,art.parameter_weight");
    //                 $this->db->from("assessment_trans_sparam_temp art");
    //                 $this->db->join('assessment_question aq', 'aq.id = art.question_id', 'left');
    //                 $this->db->join('parameter_mst p', 'p.id = art.parameter_id', 'left');
    //                 $this->db->join('parameter_label_mst l', 'l.id = art.parameter_label_id', 'left');
    //                 $this->db->join('subparameter_mst s', 's.id = art.sub_parameter_id', 'left');
    //                 $this->db->where("art.assessment_id", $assessment_id);
    //                 $this->db->order_by("art.id", 'ASC');
    //                 $this->db->order_by("art.txn_id", 'ASC');
    //                 $parameter_subparameter_trans = $this->db->result();
    //                 // $final_data['sub_parameter_data'] = $this->create_trinity_model->get_parameter_value($bulk_assessment_id);
    //             }
    //             if (isset($assessment_trans)) {
    //                 foreach ($assessment_trans as $val) {
    //                     $question_id_set[] = $val->question_id;
    //                 }
    //             }
    //             $sub_parameter_main = array();
    //             $k = 0;
    //             if (!empty($parameter_subparameter_trans)) {
    //                 foreach ($parameter_subparameter_trans as $dt) {
    //                     $sub_parameter_main[$dt->question_id][$k] = $dt;
    //                     $k++;
    //                 }
    //             }
    //             $temp_id = array();
    //             $question_arr = array();
    //             $parameter_label_arr = array();
    //             $question_sentance = array();
    //             if (count(($question_id_set)) > 0) {

    //                 foreach ($question_id_set as $key => $question_id) {
    //                     if (in_array($question_id, $temp_id)) {
    //                         continue;
    //                     }
    //                     $temp_id[] = $question_id;
    //                     $sentence_keyword = [];
    //                     foreach ($sub_parameter_main[$question_id] as $t) {
    //                         if ($t->type_id == 1) {
    //                             $sentence_keyword[] = $t->sentence_keyword;
    //                         }
    //                         if (in_array($t->parameter_label_name, $parameter_label_arr)) {
    //                             continue;
    //                         } else {
    //                             $parameter_label_arr[]  =  $t->parameter_label_name;
    //                         }
    //                     }

    //                     array_push($question_sentance, array(
    //                         "question"             => $t->question,
    //                         "senetence_keyword"    => $sentence_keyword,
    //                     ));
    //                     // $sentence_keyword = [];
    //                 }
    //             }
    //             $data['show_ranking'] = '1';
    //             $data['parameter_label'] = $parameter_label_arr;
    //             $data['question_sentance'] = $question_sentance;
    //             // $this->load->library('Pdf_Library');
    //             $htmlContent =  $this->load->view('create_trinity/report_preview', $data, true);
    //         }

    //         //pdf view start here
    //         ob_start();
    //         define('K_TCPDF_EXTERNAL_CONFIG', true);
    //         $this->load->library('Pdf');
    //         //  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //         $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //         $data['pdf'] = $pdf;
    //         $pdf->SetCreator(PDF_CREATOR);
    //         $pdf->SetAuthor('Awarathon');
    //         $pdf->SetTitle("Awarathon's Sales Readiness Reports");
    //         $pdf->SetSubject("Awarathon's Sales Readiness Reports");
    //         $pdf->SetKeywords('Awarathon');
    //         $pdf->SetHeaderData('', 0, '', '', array(255, 255, 255), array(255, 255, 255));
    //         $pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    //             <tr>
    //                 <td style="height:10px;width:60%">
    //                     <div class="page-title">Sales Readiness Reports</div>
    //                 </td>
    //                 <td style="height:10px;width:40%;text-align:right;">
    //                     <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="' . $data['company_logo'] . '"/>
    //                 </td>
    //             </tr>
    //         </table>');
    //         $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    //         $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    //         $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
    //         $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    //         $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //         //$pdf->SetAutoPageBreak(TRUE, 0);
    //         $pdf->SetAutoPageBreak(TRUE, 20);
    //         $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //         $pdf->PrintCoverPageFooter = True;

    //         $pdf->AddPage();
    //         $pdf->setJPEGQuality(100);
    //         $pdf->SetFont('helvetica', '', 10);
    //         $pdf->writeHTML($htmlContent, true, false, true, false, '');
    //         $pdf->lastPage();
    //         ob_end_clean();

    //         $now       = date('YmdHis');
    //         $file_name = 'COMB-C' . $_company_id . 'A' . 'U' . 'DTTM' . $now . '.pdf';
    //         $pdf->Output($file_name, 'I');
    //         // //pdf view end here
    //         // echo json_encode($datas);
    //     }
    // }



    // public function create_assessment($errors = "")
    // {
    //     $data['module_id'] = '102';
    //     $data['username'] = $this->mw_session['username'];
    //     $data['acces_management'] = $this->acces_management;
    //     if (!$data['acces_management']->allow_add) {
    //         redirect('assessment_create');
    //         return;
    //     }
    //     $superaccess = $this->mw_session['superaccess'];
    //     $data['superaccess'] = ($superaccess ? 1 : 0);
    //     $login_id = $this->mw_session['user_id'];
    //     $assessment_data = $this->create_trinity_model->get_assessment_data($login_id);
    //     $assessment_id = isset($assessment_data[0]['id']) ? $assessment_data[0]['id'] : '';
    //     // $ISEXIST  = $this->common_model->get_value('assessment_supervisors', 'id,trainer_id', 'trainer_id=' . $login_id . ' AND assessment_id=' . $assessment_id);
    //     // $data['is_supervisor'] = (count((array)$ISEXIST) > 0 ? 1 : 0);
    //     // if (!$data['acces_management']->allow_edit) {
    //     //     redirect('assessment_create');
    //     //     return;
    //     // }
    //     $Company_id = $this->mw_session['company_id'];
    //     if ($Company_id == "") {
    //         $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
    //     } else {
    //         $data['cmp_result'] = array();
    //     }

    //     $data['Company_id'] = $Company_id;

    //     $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
    //     //Added for AI report, Manual report and Combined report
    //     $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');
    //     if ($assessment_id != '') {
    //         $data['result'] = $this->common_model->get_value('assessment_mst_temp', '*', 'id=' . $assessment_id);

    //         $Qdata = $this->common_model->get_selected_values('assessment_question', 'id,question', 'assessment_type=' . $data['result']->assessment_type . ' AND company_id=' . $data['result']->company_id);

    //         $Pdata = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'assessment_type=' . $data['result']->assessment_type . ' AND company_id=' . $data['result']->company_id);
    //         $assessment_trans = $this->create_trinity_model->LoadQUestionPreview($assessment_id);

    //         $unique_aimethods = $this->create_trinity_model->LoadUniqueAIMethodsTemp($assessment_id);

    //         // $parameter_subparameter_trans = $this->create_trinity_model->LoadParameterSubParameter_temp($assessment_id);
    //         $this->db->select("art.question_id,aq.question,art.language_id,art.txn_id,art.ai_methods,art.parameter_id,p.description as parameter_name,art.parameter_label_id,l.description as parameter_label_name,art.sub_parameter_id,s.description as sub_parameter_name,art.type_id,IF(art.type_id=2,'Keyword',IF(art.type_id=1,'Sentence','')) as type_name,art.sentence_keyword,art.parameter_weight");
    //         $this->db->from("assessment_trans_sparam_temp art");
    //         $this->db->join('assessment_question aq', 'aq.id = art.question_id', 'left');
    //         $this->db->join('parameter_mst p', 'p.id = art.parameter_id', 'left');
    //         $this->db->join('parameter_label_mst l', 'l.id = art.parameter_label_id', 'left');
    //         $this->db->join('subparameter_mst s', 's.id = art.sub_parameter_id', 'left');
    //         $this->db->where("art.assessment_id", $assessment_id);
    //         $this->db->order_by("art.id", 'ASC');
    //         $this->db->order_by("art.txn_id", 'ASC');
    //         $parameter_subparameter_trans = $this->db->result();
    //         // $aimeth_result = $this->common_model->get_selected_values('aimethods_mst', 'id,description','status=1');
    //         $language_result = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');

    //         // $data['parametr_weights'] = $this->create_trinity_model->LoadParametrWeights_temp($assessment_id);
    //         $this->db->select("p.id,p.assessment_id, p.parameter_id,p.parameter_name as parameter_name, p.parameter_weight as parameter_weight");
    //         $this->db->from("assessment_trans_sparam_temp p");
    //         $this->db->where("p.assessment_id", $assessment_id);
    //         $data['parametr_weights'] = $this->db->result();


    //         $parameter_array = array();
    //         $question_array = array();
    //         if (count((array)$assessment_trans) > 0) {
    //             foreach ($assessment_trans as $v) {
    //                 $para = explode(',', $v->parameter_id);
    //                 $parameter_array[$v->question_id] = $para;
    //                 $question_array[] = $v->question_id;
    //             }
    //         }
    //         $question_play_array = array();
    //         $isPlay = $this->common_model->get_selected_values('assessment_results_trans', 'id,question_id', 'assessment_id=' . $assessment_id);
    //         if (count((array)$isPlay) > 0) {
    //             foreach ($isPlay as $val) {
    //                 $question_play_array[] = $val->question_id;
    //             }
    //             $disabledflag = 1;
    //         }
    //         $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $assessment_id);
    //         $data['disabledflag'] = (count((array)$isPlay2) > 0 ? 1 : 0);

    //         $isComplete = $this->common_model->get_selected_values('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id);
    //         $data['completedflag'] = (count((array)$isComplete) > 0 ? 1 : 0);

    //         $data['assessment_trans'] = $assessment_trans;
    //         $data['unique_aimethods'] = $unique_aimethods;
    //         $data['parameter_subparameter'] = $parameter_subparameter_trans;
    //         $data['language_result'] = $language_result;
    //         $data['parameter_array'] = $parameter_array;
    //         $data['Questions'] = $Qdata;
    //         $data['Parameter'] = $Pdata;
    //         $data['question_array'] = $question_array;
    //         $data['question_play_array'] = $question_play_array;
    //         $question_attempts = $this->common_model->get_selected_values('assessment_attempts', 'id', 'assessment_id=' . $assessment_id);
    //         $data['lockQue'] = (count((array)$question_attempts) > 0 ? 1 : 0);
    //         $data['assessment_id'] = $assessment_id;
    //         $data['errors'] = $errors;
    //     }
    //     $this->load->view('create_trinity/create', $data);
    // }

    // public function temp_data_save()
    // {
    //     $id = $this->input->post('Encode_id');
    //     $SuccessFlag = 1;
    //     $Message = '';
    //     $acces_management = $this->acces_management;
    //     if (!$acces_management->allow_edit) {
    //         $Message = "You have no rights to Edit,Contact Administrator for rights.";
    //         $SuccessFlag = 0;
    //     } else {
    //         $New_question_idArray = $this->input->post('New_question_id');
    //         $Old_question_idArray = $this->input->post('Old_question_id');
    //         $sub_parameter_result = $this->input->post('sub_parameter');
    //         if (isset($sub_parameter_result)) {
    //             $sub_parameter_result = $this->input->post('sub_parameter');
    //         } else {
    //             $sub_parameter_result = array();
    //         }
    //         $now = date('Y-m-d H:i:s');
    //         if ($id == '') {
    //             $start_date = $this->input->post('start_date');
    //             if ($start_date != '') {
    //                 $start_dttm = date("Y-m-d H:i:s", strtotime($this->input->post('start_date')));
    //             } else {
    //                 $start_dttm = date('Y-m-d H:i:s');
    //             }

    //             $end_date = $this->input->post('end_date');
    //             if ($end_date != '') {
    //                 $end_dttm = date("Y-m-d H:i:s", strtotime($this->input->post('end_date')));
    //             } else {
    //                 $end_dttm = date('Y-m-d H:i:s');
    //             }

    //             $assessor_date = $this->input->post('assessor_date');
    //             if ($assessor_date != '') {
    //                 $assessor_dttm = date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date')));
    //             } else {
    //                 $assessor_dttm = date('Y-m-d H:i:s');
    //             }

    //             $data = array(
    //                 'Company_id'      =>  $this->mw_session['company_id'],
    //                 'assessment'      => $this->input->post('assessment_name'),
    //                 'code'            => $this->input->post('otc'),
    //                 'is_situation' => ($this->input->post('is_situation') == 1 ? 1 : 0),
    //                 'number_attempts' => $this->input->post('number_attempts'),
    //                 'assessment_type' => $this->input->post('assessment_type'),
    //                 'end_dttm'        => $end_dttm,
    //                 'assessor_dttm'   => $assessor_dttm,
    //                 'instruction'     => $this->input->post('instruction'),
    //                 'description'     => $this->input->post('description'),
    //                 'is_preview'      => ($this->input->post('is_preview') != null) ? 0 : 1,
    //                 // 'is_preview'      => ($this->input->post('is_preview') == 1 ? 1 : 0),
    //                 'report_type'     => $this->input->post('report_type'),
    //                 'ranking'         => ($this->input->post('ranking') != 1 ? 0 : 1),
    //                 'is_weights'      => array_sum(array_column($sub_parameter_result, 'parameter_weight')) > 0 ? 1 : 0,
    //                 'status'          => ($this->input->post('status') == 1 ? 1 : 0),
    //                 'addeddate' => $now,
    //                 'modifiedby'      => $this->mw_session['user_id'],
    //                 'addedby' =>     $this->mw_session['user_id']
    //             );
    //             $data['start_dttm']  = $start_dttm;
    //             $data['ratingstyle'] = $this->input->post('ratingstyle');
    //             $insert_id = $this->common_model->insert('assessment_mst_temp', $data);
    //             if ($insert_id != "") {
    //                 if (isset($New_question_idArray) && count((array)$New_question_idArray) > 0) {
    //                     foreach ($New_question_idArray as $key => $question_id) {
    //                         $New_parameter_str = '';
    //                         $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
    //                         if (count((array)$New_parameter_idArray) > 0) {
    //                             $New_parameter_str = implode(',', $New_parameter_idArray);
    //                             $ASData = array(
    //                                 'assessment_id' => $insert_id,
    //                                 'question_id' => $question_id,
    //                                 'parameter_id' => $New_parameter_str,
    //                             );
    //                             $this->common_model->insert('assessment_trans_temp', $ASData);

    //                             // if ($Copy_id == "") {
    //                             if (isset($sub_parameter_result) and count((array)$sub_parameter_result) > 0) {
    //                                 foreach ($sub_parameter_result as $sparam) {
    //                                     $txn_id               = $sparam['txn_id'];
    //                                     $parameter_id         = $sparam['parameter_id'];
    //                                     $parameter_label_id   = $sparam['parameter_label_id'];
    //                                     $subparameter_id      = $sparam['subparameter_id'];
    //                                     $type_id              = $sparam['type_id'];
    //                                     $sentence_keyword     = $sparam['sentence_keyword'];
    //                                     $parameter_weight     = $sparam['parameter_weight'];
    //                                     $language_id          = $this->input->post('language_id' . $txn_id);

    //                                     if ((int)$txn_id == (int)$key) {
    //                                         $PSData = array(
    //                                             'assessment_id'        => $insert_id,
    //                                             'question_id'          => $question_id,
    //                                             'language_id'          => $language_id,
    //                                             'txn_id'               => $txn_id,
    //                                             'parameter_id'         => $parameter_id,
    //                                             'parameter_label_id'   => $parameter_label_id,
    //                                             'sub_parameter_id'     => $subparameter_id,
    //                                             'type_id'              => $type_id,
    //                                             'sentence_keyword'     => $sentence_keyword,
    //                                             'parameter_weight'     => $parameter_weight,
    //                                         );
    //                                         $this->common_model->insert('assessment_trans_sparam_temp', $PSData);
    //                                     }
    //                                 }
    //                             }
    //                             // }
    //                         }
    //                     }
    //                 }
    //                 $Rdata['id'] = $insert_id;
    //             } else {
    //                 $Message = "Error while creating Assessment,Contact administrator for technical support.!";
    //                 $SuccessFlag = 0;
    //             }
    //         } else {

    //             // Temp update code
    //             $data = array(
    //                 'assessment'      => $this->input->post('assessment_name'),
    //                 'code'            => $this->input->post('otc'),
    //                 'number_attempts' => ($this->input->post('number_attempts') == 0 ? 1 : $this->input->post('number_attempts')),
    //                 'end_dttm'        => date("Y-m-d H:i:s", strtotime($this->input->post('end_date'))),
    //                 'assessor_dttm'   => date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date'))),
    //                 'instruction'     => $this->input->post('instruction'),
    //                 'description'     => $this->input->post('description'),
    //                 'is_preview'      => ($this->input->post('is_preview') != null) ? 0 : 1,
    //                 // 'is_preview'      => ($this->input->post('is_preview') == 1 ? 1 : 0),
    //                 // 'report_type'     => $this->input->post('report_type'),
    //                 'ranking'         => ($this->input->post('ranking') == 1 ? 1 : 0),
    //                 'is_weights'      => array_sum(array_column($sub_parameter_result, 'parameter_weight')) > 0 ? 1 : 0,
    //                 'status'          => $this->input->post('status'),
    //                 'modifieddate'    => $now,
    //                 'modifiedby'      => $this->mw_session['user_id'],
    //             );

    //             $data['is_situation']    = $this->input->post('question_type') != null ? $this->input->post('question_type') : '0';
    //             $data['assessment_type'] = $this->input->post('assessment_type');
    //             $data['report_type']     = $this->input->post('report_type');
    //             $data['start_dttm']      = date("Y-m-d H:i:s", strtotime($this->input->post('start_date')));
    //             $data['ratingstyle'] = $this->input->post('ratingstyle');

    //             $this->common_model->update('assessment_mst_temp', 'id', $id, $data);
    //             //if(count((array)$Old_question_idArray) > 0){
    //             $Old_parameters = $this->input->post('Old_parameter_id');
    //             $assessment_trans = $this->common_model->get_selected_values('assessment_trans_temp', 'id,question_id', 'assessment_id=' . $id);
    //             foreach ($assessment_trans as $key => $value) {
    //                 $trans_id = $value->id;
    //                 if (isset($_POST['Old_question_id'][$trans_id]) && $_POST['Old_question_id'][$trans_id] != '') {
    //                     $question_id           = $this->input->post('Old_question_id', true)[$trans_id];
    //                     $Old_parameter_idArray = $this->input->post('Old_parameter_id' . $trans_id, true);
    //                     $OASData = array(
    //                         'question_id'  => $question_id,
    //                         'parameter_id' => implode(',', $Old_parameter_idArray),
    //                     );
    //                     $this->common_model->update('assessment_trans_temp', 'id', $trans_id, $OASData);
    //                 } else {
    //                     $ISLOCK = $this->common_model->get_value('assessment_trans_temp', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
    //                     // $ISLOCK = $this->common_model->get_value('assessment_results_trans_temp', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
    //                     if (count((array)$ISLOCK) == 0) {
    //                         $this->common_model->delete('assessment_trans_temp', 'id', $trans_id);
    //                     }
    //                 }
    //             }
    //             // Delete from Assessment_trans_sparm 
    //             $assessment_trans_sparam = $this->common_model->get_selected_values('assessment_trans_sparam_temp', 'id,question_id', 'assessment_id=' . $id);
    //             foreach ($assessment_trans_sparam as $key => $value) {
    //                 $trans_id = $value->id;
    //                 if (isset($_POST['Old_question_id'][$trans_id]) && $_POST['Old_question_id'][$trans_id] != '') {
    //                     $question_id           = $this->input->post('Old_question_id', true)[$trans_id];
    //                     $Old_parameter_idArray = $this->input->post('Old_parameter_id' . $trans_id, true);
    //                     $OASData = array(
    //                         'question_id'  => $question_id,
    //                         'parameter_id' => implode(',', $Old_parameter_idArray),
    //                     );
    //                     $this->common_model->update('assessment_trans_sparam_temp', 'id', $trans_id, $OASData);
    //                 }
    //                 // else {
    //                 //     $ISLOCK = $this->common_model->get_value('assessment_results_trans', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
    //                 //     if (count((array)$ISLOCK) == 0) {
    //                 //         $this->common_model->delete('assessment_trans_sparam_temp', 'id', $trans_id);
    //                 //     }
    //                 // }
    //             }
    //             if (count((array)$New_question_idArray) > 0) {
    //                 foreach ($New_question_idArray as $key => $question_id) {
    //                     $New_parameter_str = '';
    //                     // $pkey = $this->input->post('rowid')[$key];
    //                     $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
    //                     if (count((array)$New_parameter_idArray) > 0) {
    //                         $New_parameter_str = implode(',', $New_parameter_idArray);
    //                         $ASData = array(
    //                             'assessment_id' => $id,
    //                             'question_id'   => $question_id,
    //                             'parameter_id'  => $New_parameter_str,
    //                         );
    //                         $this->common_model->insert('assessment_trans_temp', $ASData);
    //                     }
    //                 }
    //             }
    //             //DP

    //             $assessment_trans = $this->common_model->get_selected_values('assessment_trans_temp', 'assessment_id,question_id', 'assessment_id="' . $id . '"');
    //             foreach ($assessment_trans as $key => $value) {
    //                 $mykey = array();
    //                 if (isset($sub_parameter_result) and count((array)$sub_parameter_result) > 0) {
    //                     foreach ($sub_parameter_result as $sparam) {
    //                         $txn_id                  = $sparam['txn_id'];

    //                         $parameter_id            = $sparam['parameter_id'];
    //                         $parameter_label_id      = $sparam['parameter_label_id'];
    //                         // $parameter_label_name = $sparam['parameter_label_name'];
    //                         $subparameter_id         = $sparam['subparameter_id'];
    //                         $type_id                 = $sparam['type_id'];
    //                         // $sentence_keyword        = json_encode($sparam['sentence_keyword']);
    //                         $sentence_keyword        = htmlspecialchars($sparam['sentence_keyword']);
    //                         $parameter_weight        = $sparam['parameter_weight'];
    //                         // $ai_methods_array     = $this->input->post('aimethods_id'.$txn_id);
    //                         $language_id             = $this->input->post('language_id' . $txn_id);
    //                         // $language_id             = $sparam['language_id'];					   
    //                         // if(is_array($ai_methods_array)) {
    //                         // 	$ai_methods          = implode(',', $ai_methods_array);
    //                         // }
    //                         if ((int)($txn_id - 1) == (int)($key)) {
    //                             $txn_exists = $this->common_model->get_selected_values('assessment_trans_sparam_temp', 'id', 'assessment_id="' . $id . '" AND question_id="' . $value->question_id . '" AND parameter_id="' . $parameter_id . '" AND parameter_label_id="' . $parameter_label_id . '" AND sub_parameter_id="' . $subparameter_id . '"');
    //                             $txnid = '';
    //                             foreach ($txn_exists as $txndata) {
    //                                 $mykey[]    = $txndata->id;
    //                                 $txnid = $txndata->id;
    //                             }
    //                             if (isset($txn_exists) and count((array)$txn_exists) > 0) {
    //                                 $update_data = array(
    //                                     'assessment_id'           => $id,
    //                                     'question_id'             => $value->question_id,
    //                                     // 'ai_methods'           => $ai_methods,
    //                                     'language_id'             => $language_id,
    //                                     'txn_id'                  => $txn_id,
    //                                     'parameter_id'            => $parameter_id,
    //                                     'parameter_label_id'      => $parameter_label_id,
    //                                     // 'parameter_label_name' => $parameter_label_name,
    //                                     'sub_parameter_id'        => $subparameter_id,
    //                                     'type_id'                 => $type_id,
    //                                     'sentence_keyword'        => $sentence_keyword,
    //                                     'parameter_weight'        => $parameter_weight,
    //                                 );
    //                                 $this->common_model->update('assessment_trans_sparam_temp', 'id', $txnid, $update_data);
    //                             } else {
    //                                 $PSData = array(
    //                                     'assessment_id'           => $id,
    //                                     'question_id'             => $value->question_id,
    //                                     // 'ai_methods'           => $ai_methods,
    //                                     'language_id'             => $language_id,
    //                                     'txn_id'                  => $txn_id,
    //                                     'parameter_id'            => $parameter_id,
    //                                     'parameter_label_id'      => $parameter_label_id,
    //                                     // 'parameter_label_name' => $parameter_label_name,
    //                                     'sub_parameter_id'        => $subparameter_id,
    //                                     'type_id'                 => $type_id,
    //                                     'sentence_keyword'        => $sentence_keyword,
    //                                     'parameter_weight'        => $parameter_weight,
    //                                 );
    //                                 $ats_id  = $this->common_model->insert('assessment_trans_sparam_temp', $PSData);
    //                                 $mykey[] = $ats_id;
    //                             }
    //                         }
    //                     }

    //                     $where_clause = "assessment_id='" . $id . "' AND question_id='" . $value->question_id . "'";
    //                     if (count((array)$mykey) > 0) {
    //                         $where_clause .= " AND id NOT IN(" . implode(',', $mykey) . ")";
    //                     }
    //                     $this->common_model->delete_whereclause('assessment_trans_sparam', $where_clause);
    //                 }
    //             }
    //             $Rdata['id'] = $id;
    //         }
    //     }
    //     $Rdata['success'] = $SuccessFlag;
    //     echo json_encode($Rdata);
    // }
    public function delete_question_id()
    {
        $txn_id = $this->input->post('txn_id', TRUE);
        $assessment_id = $this->input->post('assessment_id', TRUE);
        //  $del_id = $this->create_trinity_model->get_question_delete($txn_id, $assessment_id);

        $this->db->DISTINCT();
        $this->db->select('question_id');
        $this->db->from('assessment_trans_sparam_temp');
        $this->db->where('assessment_id', $assessment_id);
        $this->db->where('txn_id', $txn_id);
        $del_id = $this->db->get()->result();

        foreach ($del_id as $key) {
            $question_id = $key->question_id;
        }
        if ($question_id != '') {
            $ldcwhere = " question_id =" . $question_id;
            $this->common_model->delete_whereclause('assessment_trans_sparam_temp', $ldcwhere);
            $this->common_model->delete_whereclause('assessment_trans_temp', $ldcwhere);
        }
        $data['html'] = '';
        echo json_encode($data);
    }

    function script_based_situation()
    {
        $script_id = $this->input->post('script_id');
        if ($script_id != '' && isset($script_id)) {
            // $data_list = $this->create_trinity_model->load_script_question($script_id);
            $this->db->select("situation");
            $this->db->from("script_mst as fs");
            $this->db->join('assessment_script_qna as a', 'a.script_id = fs.id', 'left');
            $this->db->where('fs.id', $script_id);
            $data = $this->db->get()->row();
        }
        echo json_encode($data);
    }

    function save_script($Encode_id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('script_id', 'Script Id', 'required');
        $assessment_id = base64_decode($Encode_id);
        $Message = '';
        $SuccessFlag = 1;
        $script_id = $this->input->post('script_id');
        $situation = $this->input->post('situation');
        $script_question = $this->input->post('script_question');
        $c_question_limit = $this->input->post('c_question_limit');


        if (isset($assessment_id) and count((array)$assessment_id) > 0) {
            $data = array(
                'assessment_id'       => $assessment_id,
                'script_id'           => $script_id,
                'question_limit'      => $c_question_limit,
                'script_question'     => $script_question,
                'script_situation'    => $situation,
            );
            $this->common_model->insert('assessment_script', $data);
            $Message = "Data save successfully.!";
        } else {
            $Message = "Please add Script and Questions...!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    // Persona Upload Image
    function fetch_persona()
    {
        $persona_id = $this->input->post('persona_id');
        if ($persona_id != '' && isset($persona_id)) {
            $persona_details = $this->common_model->get_value('trinity_persona', 'id,persona_name,persona_image', 'id=' . $persona_id);
            if (isset($persona_details)) {
                $data['id'] = $persona_details->id;
                $data['persona_name'] = $persona_details->persona_name;
                $data['persona_image']  = $persona_details->persona_image;
            } else {
                $data['id'] = '';
                $data['persona_name'] = '';
                $data['persona_image'] = '';
                $data['persona_option'] = '';
            }
        } else {
            $data['persona_image'] = base_url() . 'assets/uploads/no_image.png';
        }

        echo json_encode($data);
    }
    public function selectImage()
    {
        $Message = '';
        $Success = 1;
        $que_image = '';
        $isUpdate = $this->input->post('isUpdate');
        if (isset($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
            $config = array();
            $que_image = str_replace(' ', '_', $_FILES['file']['name']);
            $upload_Path = './assets/uploads/trinity';
            $config['upload_path'] = $upload_Path;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            // $config['max_width'] = 750;
            // $config['max_height'] = 400;
            // $config['min_width'] = 750;
            // $config['min_height'] = 400;
            $config['file_name'] = $que_image;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $Message = $this->upload->display_errors();
                $Success = 0;
            } else {

                if ($isUpdate) {
                    if ($this->input->post('que_image') != '') {
                        $Path = $upload_Path . '/' . $this->input->post('que_image');
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                }
                $file_path =  base_url() . 'assets/uploads/trinity' . '/' . $que_image;
                $Message = 'Image uploaded successfully';
            }
        }
        $data['Message'] = $Message;
        $data['Success'] = $Success;
        $data['que_image'] = $que_image;
        echo json_encode($data);
    }
    // Persona Upload Image 


    // Fetch Question Answer Mapping Script 
    // public function fetch_question_answer()
    // {
    //     $html               = '';
    //     $company_id         = $this->security->xss_clean($this->input->post('company_id', true));
    //     $script_id          = $this->security->xss_clean($this->input->post('script_id', true));
    //     $view               = ($this->input->post('type', true) ? $this->input->post('type', true) : '');
    //     $c_question         = ($this->input->post('c_question', true) ? $this->input->post('c_question', true) : '');
    //     if (isset($script_id) && !empty($script_id)) {
    //         $fetch_question_answer         = $this->create_trinity_model->get_value('assessment_script_qna', '*', 'company_id="' . $company_id . '" AND script_id="' . $script_id . '"');
    //         $data['fetch_question_answer'] = $fetch_question_answer;
    //         $data['view']                  = $view;
    //         $data['c_question']            = $c_question;
    //         $html                          = $this->load->view('create_trinity/load_question_answer', $data, true);
    //     } else {
    //         $html = '';
    //     }
    //     $output['html']            = $html;
    //     $output['success']         = "true";
    //     $output['message']         = "";
    //     echo json_encode($output);
    // }
    public function fetch_question_answer($tr_no)
    {
        $assessment_id = base64_decode($this->input->post('edit_id'));
        // $NewQuestionArray = $this->input->post('NewQuestionArray');
        // $start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
        $company_id         = $this->security->xss_clean($this->input->post('company_id', true));
        $script_id          = $this->security->xss_clean($this->input->post('script_id', true));
        $message = '';
        $company_id = $this->mw_session['company_id'];
        $lchtml = '';

        if (count((array)$script_id) > 0) {
            $language_result = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
            $fetch_question_answer = $this->create_trinity_model->get_mapped_qna($company_id, $assessment_id, $script_id); 
            // $fetch_question_answer         = $this->create_trinity_model->get_value('assessment_script_qna', '*', 'company_id="' . $company_id . '" AND script_id="' . $script_id . '"');
            
            $parameter_id = array('6', '2');
            $this->db->select('id,description');
            $this->db->from('goal_mst');
            $this->db->where("status", "1");
            $this->db->where_in('parameter_id', $parameter_id);
            $Pdata =  $this->db->get()->result();
            if (isset($assessment_id)) {
                $que_parameter = $this->common_model->get_selected_values('trinity_trans', '*', 'question_id != 0 and assessment_id=' . $assessment_id);
                $language = $this->common_model->get_selected_values('trinity_trans_sparam', 'language_id', 'question_id != 0 and assessment_id=' . $assessment_id);
            }
            $language = (!empty($language)) ? $language[0]->language_id : '1';

            $i = 0;
            foreach ($fetch_question_answer as $fq) {
                // if (in_array($question_id, $temp_id)) {
                //     continue;
                // }
                // $temp_id[] = $question_id;
                // $Question_set = $this->common_model->get_value('assessment_question', 'question', 'id=' . $question_id);
                $lchtml .= '<tr id="Row-' . $tr_no . '">';
                $lchtml .= '<td> <span id="question_text_' . $tr_no . '">' . $fq->question
                    . '</span>
							<input type="hidden" id="txt_trno' . $tr_no . '" name="txt_trno_' . $tr_no . '" class="txt_trno" value="' . $tr_no . '" >
							<input type="hidden" id="question_id' . $tr_no . '" name="New_question_id[' . $tr_no . ']" value="' . $fq->id . '" ></td>';

                $lchtml .= '<td> <span id="ans_text_' . $tr_no . '">' . $fq->answer
                    . '</span>
                                    <input type="hidden" id="txt_trno' . $tr_no . '" name="txt_trno_' . $tr_no . '" class="txt_trno" value="' . $tr_no . '" >
                                    <input type="hidden" id="ans_id' . $tr_no . '" name="New_ans_id[' . $tr_no . ']" value="' . $fq->id . '" ></td>';
                $lchtml .= '<td><select id="language_id' . $tr_no . '" name="language_id' . $tr_no . '" class="form-control input-sm ValueUnq language_id" style="width:100%" placeholder="Please select" style="width:100px;" >';
                foreach ($language_result as $language_data) {
                    $lchtml .= '<option value="' . $language_data->id . '" ' . ($language != '' ? ($language_data->id == $language ? 'selected'  : '') : '') . '>' . $language_data->name . '</option>';
                    // $lchtml .= '<option value="' . $language_data->id . '" ' . ($language[0]->language_id != '' ? ($language_data->id == $language[0]->language_id ? 'selected'  : '') : '') . '>' . $language_data->name . '</option>';
                }
                $lchtml .= '</select></td>';
                // $lchtml .= '<td><div id="paramsub' . $tr_no . '"></div>
                // <select id="parameter_id' . $tr_no . '" name="New_parameter_id' . $tr_no . '[]" multiple style="display:none;" onchange="getUnique_paramters()">';
                // foreach ($Pdata as $p) {
                //     $lchtml .= '<option value="' . $p->id . '">' . $p->description . '</option>';
                // }
                // $lchtml .= '</select></td>';
                $lchtml .= '<td><select id="parameter_id' . $tr_no . '" name="New_parameter_id' . $tr_no . '[]" class="form-control input-sm ValueUnq verbleparameter" style="width:100%" multiple onchange="getUnique_paramters()">';
                if (count((array)$Pdata) > 0) {
                    foreach ($Pdata as $p) {
                        if(isset($que_parameter) && !empty($que_parameter)){
                            $lchtml .= '<option value="' . $p->id . '" ' . ($que_parameter[$i]->question_id != '' ? ($que_parameter[$i]->question_id == $fq->id ? (in_array($p->id, explode(',', $que_parameter[$i]->parameter_id)) ? 'selected' : '') : '')  : '') . '>' . $p->description . '</option>';
                        }else{
                            $lchtml .= '<option value="' . $p->id . '">' . $p->description . '</option>';
                        }
                    }
                }
                $lchtml .= '</select></td>';
                $lchtml .= '<input type="hidden" name="rowid[]" value="' . $tr_no . '"/>';
                $lchtml .= '<td>
							<a class="btn btn-success btn-sm" href="' . base_url() . '/create_trinity/que_ans_edit/' . base64_encode($fq->id) . '" 
                            accesskey=""  data-target="#LoadModalFilter-view" data-toggle="modal"><i class="fa fa-pencil"></i> </a>'
                    . '<button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="DelQueAns(' . $fq->id . ')" href="javascript:void(0)" ><i class="fa fa-times"></i></button> </td>';
                $lchtml .= "<script></script></tr>";
                $tr_no++;
                $i++;
            }
        }
        $data['Msg'] = $message;
        $data['tr_no'] = $tr_no;
        $data['lchtml'] = $lchtml;
        echo json_encode($data);
    }
    // Fetch Question answer  end 

    public function que_ans_edit($Encode_id)
    {
        $data['QAid'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $this->db->select('company_id');
            $this->db->from('assessment_script_qna');
            $this->db->where('id', $data['QAid']);
            $Company = $this->db->get()->row();
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }

        if (isset($data['QAid']) && !empty($data['QAid'])) {
            $this->db->select('*');
            $this->db->from('assessment_script_qna');
            $this->db->where('company_id', $company_id);
            $this->db->where('id', $data['QAid']);
            $result = $this->db->get()->row();
        } else {
            $result = '';
        }
        if (isset($result) and count((array)$result) > 0) {
            $QAid = (int)$result->id;
            $question = $result->question;
            $answer = $result->answer;
            $script_id = $result->script_id;
        } else {
            $QAid = '';
            $question = '';
            $answer = '';
            $script_id = '';
        }
        $data['company_id'] = $company_id;
        $data['QAid'] = $QAid;
        $data['question'] = $question;
        $data['answer'] = $answer;
        $data['script_id'] = $script_id;
        $this->load->view('create_trinity/que_ans_modal', $data);
    }

    public  function update_que_ans($QAid)
    {
        $Message = '';
        $SuccessFlag = 1;
        $QAid = base64_decode($QAid);
        if ($QAid != '' && isset($QAid)) {
            $SuccessFlag = 1;
            $acces_management = $this->acces_management;
            if (!$acces_management->allow_edit) {
                $Message .= "You have no rights to Edit,Contact Administrator for rights.";
                $SuccessFlag = 0;
            } else {
                $this->load->library('form_validation');
                if ($this->mw_session['company_id'] == "") {
                    $this->form_validation->set_rules('company_id', 'Company name', 'required');
                    $Company_id = $this->input->post('company_id');
                } else {
                    $Company_id = $this->mw_session['company_id'];
                }
                // $this->form_validation->set_rules('question', 'Question', 'required');
                $this->form_validation->set_rules('answer', 'Answer', 'required');
                if ($this->form_validation->run() == FALSE) {
                    $Message .= validation_errors();
                    $SuccessFlag = 0;
                }
                if ($SuccessFlag) {
                    $sentence_keyword = '';
                    if(!empty($this->input->post('answer'))){
                        $sentence_keyword = trim($this->security->xss_clean($this->input->post('answer')));
                        $sentence_keyword = str_replace(". ", "|", $sentence_keyword);
                    }
                    $data = array(
                        'sentence_keyword'    => $sentence_keyword
                    );
                    $update_id = $this->common_model->update('trinity_trans_sparam', 'question_id', $QAid, $data);
                    // $data = array(
                    //     'question'  => $this->security->xss_clean($this->input->post('question')),
                    //     'answer'    => $this->security->xss_clean($this->input->post('answer'))
                    // );
                    // $update_id = $this->common_model->update('assessment_script_qna', 'id', $QAid, $data);
                    $Message .= "Question Answer Update Successfully..";
                }
            }
        } else {
            $Message .= "Question Answer Update Failed..!!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function delete_que_ans()
    {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->security->xss_clean($this->input->post('deleteid'));
            $DeleteFlag = 1;
            if ($DeleteFlag) {
                $this->common_model->delete('assessment_script_qna', 'id', $deleted_id);
                $message = "Question and Answer deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Question and Answer cannot be deleted.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
    // Fetch Question Answer Datatable Mapping Script  

    public function map_persona($Encode_id)
    {
        $Message = '';
        $SuccessFlag = 1;
        $assessment_id = base64_decode($Encode_id);
        if (isset($assessment_id) && !empty($assessment_id)) {
            $persona_id = $this->input->post('persona_id');
            $is_create = $this->input->post('is_create');

            if ($is_create == 0) {
                $persona_name = $persona_id;
                $persona_image = $this->input->post('file');

                $now = date('Y-m-d H:i:s');
                $data = array(
                    'persona_name' => $persona_name,
                    'persona_image' => $persona_image,
                    'add_date' => $now,
                    'addedby' => $this->mw_session['user_id']
                );
                $this->db->select('*');
                $this->db->from('trinity_map_persona');
                $this->db->where('assessment_id', $assessment_id);
                $ass_id_array = $this->db->get()->result();


                if (count((array)$ass_id_array) > 0) {
                    $insert_id =  $this->common_model->insert('trinity_persona', $data);
                    $Rdata = array(
                        'company_id' => $this->mw_session['company_id'],
                        'assessment_id' => $assessment_id,
                        'persona_id' => $insert_id
                    );
                    $this->common_model->update('trinity_map_persona', 'assessment_id', $assessment_id, $Rdata);
                    $Message .= "Persona mapped Successfully..";
                    $insert_id = '';
                } else {
                    $insert_id =  $this->common_model->insert('trinity_persona', $data);
                    $Rdata = array(
                        'company_id' => $this->mw_session['company_id'],
                        'assessment_id' => $assessment_id,
                        'persona_id' => $insert_id
                    );
                    $this->common_model->insert('trinity_map_persona', $Rdata);
                    $Message .= "Persona mapped Successfully..";
                }
            } else {
                if (isset($persona_id)) {
                    $persona = $this->common_model->get_value('trinity_persona', '*', 'id=' . $persona_id);
                    if (count((array)$persona) > 0) {
                        $persona_image = $persona->persona_image;
                        $data = array(
                            'company_id' => $this->mw_session['company_id'],
                            'assessment_id' => $assessment_id,
                            'persona_id' => $persona_id
                        );
                        $persona = $this->common_model->get_value('trinity_map_persona', '*', 'assessment_id=' . $assessment_id);
                        if (count((array)$persona) > 0) {
                            $this->common_model->update('trinity_map_persona', 'assessment_id', $assessment_id, $data);
                            $Message .= "Persona mapped Successfully..";
                            $insert_id = $persona_id;
                        } else {
                            $insert_id =  $this->common_model->insert('trinity_map_persona', $data);
                            $Message .= "Persona mapped Successfully..";
                        }
                    }
                } else {
                    $Message .= "Persona Mapping Failed..!!";
                    $SuccessFlag = 0;
                }
            }
        }
        $data['Message'] = $Message;
        $data['Success'] = $SuccessFlag;
        $data['persona_image'] = $persona_image;
        $data['insert_id'] = $insert_id;
        $data['edit_id'] = $Encode_id;
        echo json_encode($data);
    }

    public function mapping_script($Encode_id)
    {
        $Message = '';
        $SuccessFlag = 1;
        $id = base64_decode($Encode_id);

        $acces_management = $this->acces_management;
        $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'id', 'assessment_id=' . $id);
        $LockFlag = (count((array) $ISEXIST) > 0 ? 1 : 0);
        if (!$LockFlag) {
            $ISEXIST2 = $this->common_model->get_value('ai_schedule', 'id', 'assessment_id=' . $id);
            $LockFlag = (count((array) $ISEXIST2) > 0 ? 1 : 0);
        }
        $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $id);
        $edit_lockflag = (count((array)$isPlay2) > 0 ? 1 : 0);
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $New_question_idArray = $this->input->post('New_question_id');
            $Old_question_idArray = $this->input->post('Old_question_id');
            $parameter_idArray = $this->input->post('parameter_id');



            $this->load->library('form_validation');
            $this->form_validation->set_rules('script', 'script', 'required');
            if ($this->input->post('isweights') == 1) {
                $this->form_validation->set_rules('weight[]', 'Weight', 'required');
            }
            // $sub_parameter_result = $this->input->post('sub_parameter');
            // if (isset($sub_parameter_result) and count((array)$sub_parameter_result) <= 0) {
            //     $Message .= "Please map the parameters and sub-parameters to the question.<br/>";
            //     $SuccessFlag = 0;
            // }
            // $sub_parameter_result = $this->input->post('sub_parameter');
            // if (isset($sub_parameter_result) and count((array)$sub_parameter_result) <= 0) {
            //     $Message .= "Please map the parameters and sub-parameters to the question.<br/>";
            //     $SuccessFlag = 0;
            // }

            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $managers_data = $this->common_model->get_value('assessment_managers', 'trainer_id', 'assessment_id=' . $id);
                if (count((array)$managers_data) == 0 && $this->input->post('status') == 1) {
                    $Message = "Please Mapp Managers first..!";
                    $SuccessFlag = 0;
                } else {
                    if (count((array)$New_question_idArray) == 0) {
                        $Message = "Please select atleast one question..<br/>";
                        $SuccessFlag = 0;
                    }
                    if (count((array)$parameter_idArray) == 0) {
                        $Message = "Please select atleast one Goal..<br/>";
                        $SuccessFlag = 0;
                    }

                    if (isset($Old_question_idArray) && isset($New_question_idArray) && count((array)$Old_question_idArray) == 0 && count((array)$New_question_idArray) == 0) {
                        $Message = "Please select atleast one question..<br/>";
                        $SuccessFlag = 0;
                    }

                    if (count((array)$New_question_idArray) > 0) {
                        if (isset($Old_question_idArray)) {
                            $AlreayExist = array_intersect($Old_question_idArray, $New_question_idArray);
                            if (count((array)$AlreayExist) > 0) {
                                $Message .= "Duplicate Questions Found..!<br/>";
                                $SuccessFlag = 0;
                            }
                        }

                        $Nduplicate = array_diff_assoc($New_question_idArray, array_unique($New_question_idArray));
                        if (count((array)$Nduplicate) > 0) {
                            $Message .= "Duplicate Questions Found..!!<br/>";
                            $SuccessFlag = 0;
                        }
                        if (count((array)$New_question_idArray) > 0) {
                            foreach ($New_question_idArray as $key => $question_id) {
                                // $pkey = $this->input->post('rowid')[$key];
                                $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);

                                // $old_data = $this->common_model->get_value('trinity_trans', 'id', 'assessment_id=' . $id . ' AND question_id=' . $question_id);
                                // if (count((array)$old_data) > 0) {
                                //     $Message .= "Duplicate Questions Found..!!<br/>";
                                //     $SuccessFlag = 0;
                                // }
                                if (!isset($New_parameter_idArray)) {
                                    $Message .= "Please Select Goal...!!.<br/>";
                                    $SuccessFlag = 0;
                                    break;
                                }
                            }
                        }
                    }
                    if (count((array)$Old_question_idArray) > 0) {
                        $Oduplicate = array_diff_assoc($Old_question_idArray, array_unique($Old_question_idArray));
                        if (count((array)$Oduplicate) > 0) {
                            $Message .= "Duplicate Questions Found..!";
                            $SuccessFlag = 0;
                        }
                        foreach ($Old_question_idArray as $key => $question_id) {
                            $Old_parameters = $this->input->post('Old_parameter_id' . $key);
                            if (count((array)$Old_parameters) == 0) {
                                $Message .= "Please Select each Question wise Goal!<br/>";
                                $SuccessFlag = 0;
                                break;
                            }
                        }
                    }

                    if ($SuccessFlag) {
                        // old parameter get and Update 
                        $Old_parameters = $this->input->post('Old_parameter_id');
                        $trinity_trans = $this->common_model->get_selected_values('trinity_trans', 'id,question_id', 'assessment_id=' . $id);

                        if (count($trinity_trans))
                            foreach ($trinity_trans as $key => $value) {
                                $trans_id = $value->id;
                                if (isset($_POST['Old_question_id'][$trans_id]) && $_POST['Old_question_id'][$trans_id] != '') {
                                    $question_id           = $this->input->post('Old_question_id', true)[$trans_id];
                                    $Old_parameter_idArray = $this->input->post('Old_parameter_id' . $trans_id, true);
                                    if ($assessment_type == 2) {
                                        if (isset($_POST['is_default'][$question_id])) {
                                            $is_default = $this->input->post('is_default', true)[$question_id];
                                        } else {
                                            $is_default = 0;
                                        }
                                    }
                                    $OASData = array(
                                        'question_id'  => $question_id,
                                        'parameter_id' => implode(',', $Old_parameter_idArray),
                                    );
                                    $this->common_model->update('trinity_trans', 'id', $trans_id, $OASData);
                                } else {
                                    $ISLEXIST = $this->common_model->get_value('assessment_results_trans', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
                                    $ISLOCK = (count((array) $ISLEXIST) > 0 ? 1 : 0);
                                    if (!$ISLOCK) {
                                        $ISLEXIST2 = $this->common_model->get_value('ai_schedule', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
                                        $ISLOCK = (count((array) $ISLEXIST2) > 0 ? 1 : 0);
                                    }
                                    if (!$ISLOCK) {
                                        $this->common_model->delete('trinity_trans', 'id', $trans_id);
                                    }
                                }
                            }
                        if (count((array)$New_question_idArray) > 0) {
                            foreach ($New_question_idArray as $key => $question_id) {
                                $New_parameter_str = '';
                                // $pkey = $this->input->post('rowid')[$key];
                                $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
                                if (count((array)$New_parameter_idArray) > 0) {
                                    $New_parameter_str = implode(',', $New_parameter_idArray);
                                    $ASData = array(
                                        'assessment_id' => $id,
                                        'question_id'   => $question_id,
                                        'script_id' => $this->input->post('script', true),
                                        'parameter_id'  => $New_parameter_str,
                                    );
                                    $this->common_model->insert('trinity_trans', $ASData);
                                }
                            }
                        }

                        if (count((array)$parameter_idArray) > 0) {
                            $parameter_ids = implode(',', $parameter_idArray);
                            // $para_exist = $this->common_model->get_value('trinity_trans', 'parameter_id', 'question_id=' . '0' . ' AND assessment_id=' . $id);
                            $ASData = array(
                                'assessment_id' => $id,
                                'question_id'   => 0,
                                'script_id' => $this->input->post('script', true),
                                'parameter_id'  => $parameter_ids,
                            );
                            $this->common_model->insert('trinity_trans', $ASData);
                        }
                        $para_trans_sparam = $this->common_model->get_selected_values('trinity_trans_sparam', '*', 'assessment_id=' . $id);
                        if (count((array)$para_trans_sparam) > 0) {
                            $this->common_model->delete('trinity_trans_sparam', 'assessment_id', $id);
                        }
                        if (count((array)$New_question_idArray) > 0) {
                            $script_id = $this->input->post('script', true);
                            foreach ($New_question_idArray as $key => $question_id) {
                                $standard_ans = $this->common_model->get_selected_values('assessment_script_qna', 'answer', 'script_id='. $script_id.' AND id='.$question_id);
                                $sentence_keyword = '';
                                if(!empty($standard_ans)){
									$sentence_keyword = trim($standard_ans[0]->answer);
									$sentence_keyword = str_replace(". ", "|", $sentence_keyword);
								}
                                $New_parameter_str = '';
                                $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
                                $language_id = $this->input->post('language_id' . $key);
                                $NewParameterId = explode(',', $New_parameter_idArray[0]);
                                $get_parameter_details = $this->create_trinity_model->get_parameter_dt($NewParameterId);
                                foreach ($get_parameter_details as $np) {
                                    $ParaArray = array(
                                        'assessment_id' => $id,
                                        'question_id'   => $question_id,
                                        'language_id' => $language_id,
                                        'txn_id' =>  $key,
                                        'script_id' => $script_id,
                                        'parameter_id'  => $np->parameter_id,
                                        'goal_id' => $np->goal_id,
                                        'goal' => $np->goal_name,
                                        'sub_parameter_id' => ($np->parameter_id == 6) ? 8 : 2, //Parameter Product knowledge => 8, Else 2 (Objection handling)
                                        'type_id'   =>  1, //By default set type = sentence
                                        'sentence_keyword'  => $sentence_keyword
                                    );
                                    $this->common_model->insert('trinity_trans_sparam', $ParaArray);
                                }
                            }
                        }
                        if (count((array)$parameter_idArray) > 0) {
                            $parameter_ids = implode(',', $parameter_idArray);
                            $paraArray = explode(',', $parameter_ids);
                            $get_non_verble_para = $this->create_trinity_model->get_parameter_dt($paraArray);
                            foreach ($get_non_verble_para as $pr) {
                                $ASData = array(
                                    'assessment_id' => $id,
                                    'question_id'   => 0,
                                    'language_id' => $language_id,
                                    'txn_id' => 0,
                                    'script_id' => $this->input->post('script', true),
                                    'parameter_id'  => $pr->parameter_id,
                                    'goal_id' => $pr->goal_id,
                                    'goal' => $pr->goal_name,
                                    'sub_parameter_id' => $pr->subparameter_id,
                                    'type_id'   =>  0, //By default for non-verbal parameter
                                    'sentence_keyword' => ''
                                );
                                $this->common_model->insert('trinity_trans_sparam', $ASData);
                            }
                        }
                        $para_exist = $this->common_model->get_selected_values('trinity_trans', 'parameter_id', 'assessment_id=' . $id);
                        $parameter_weight_html = '';
                        if (count((array)$para_exist) > 0 && isset($para_exist)) {
                            foreach ($para_exist as $p) {
                                $parameterArray[] = $p->parameter_id;
                            }
                            $parameter_details = $this->create_trinity_model->get_parameter_details($parameterArray);
                            // $parameter_weight_html .= '<tbody>';
                            foreach ($parameter_details as $pd) {
                                $parameter_weight_html .= '<tr>';
                                $parameter_weight_html .= '<td id="parameterid">' . $pd->description . ' </td>';
                                $parameter_weight_html .= '<td id="parameter"><input id="parameter_id' . $pd->id . '" name="parameter_id[' . $pd->id . ']"  type="text" value="" min="1" max="100" style="width:100%;"></td>';
                                $parameter_weight_html .= '</tr>';
                            }
                            // $parameter_weight_html .= '</tbody>';
                            // $this->load->view('create_trinity/parameter_weight_modal', $data);
                        }
                        $Message = "Parameters/Goals Mapped Successfully..!";
                    }
                }
            }
        }
        $Rdata['parameters'] = isset($parameter_weight_html) ? $parameter_weight_html : '';
        $Rdata['assessment_id'] = $Encode_id;
        $Rdata['success'] = $SuccessFlag;
        // $Rdata['success'] = 1;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function save_mapping_goal($Encode_id)
    {
        $Message = '';
        $SuccessFlag = 1;
        $assessment_id = base64_decode($Encode_id);

        $question_id = $this->input->post('map_question_id');

        if (!empty($question_id)) {
            foreach ($question_id as $map_question) {
                $goal_id = $this->input->post('p_id' . $map_question);
                $New_goal_id = implode(',', $goal_id);
                $MGData = array(
                    'goal_id'  => $New_goal_id,
                );
                $where = "assessment_id = '" . $assessment_id . "' AND question_id = '" . $map_question . "' and is_default = 1 ";
                $this->db->where($where);
                $this->db->update('trinity_trans', $MGData);
            }

            $Message .= "Mapping goal Successfully..";
            $SuccessFlag = 1;
        } else {
            $Message .= "Please select Default Questions ..!!";
            $SuccessFlag = 0;
        }
        $data['Message'] = $Message;
        $data['Success'] = $SuccessFlag;
        echo json_encode($data);
    }

    // Map Goal code
    public function add_goal($txn_id = '', $question_id = '')
    {
        if ($txn_id != '') {
            $this->db->select('id,description');
            $this->db->from('parameter_mst');
            $this->db->where("status", "1");
            $array = array('description' => 'Product Knowledge');
            $array2 = array('description' => 'Objection Handling');
            $this->db->like($array);
            $this->db->or_like($array2);
            $data['parameters'] =  $this->db->get()->result();
            $data['txn_id']          = $txn_id;
            $data['company_id']      = $company_id;
        }
        $data['AddEdit'] = 'A';
        $data['edit_id'] = '';
        $this->load->view('create_trinity/parameter_modal', $data);
    }
    public function datatable_subparameter_refresh()
    {
        $html = '';
        $txn_id        = $this->input->post('txn_id');
        $assessment_type = $this->input->post('assessment_type');
        $parameter_id  = $this->input->post('parameter_id');
        $p2s2          = $this->input->post('p2s2');
        $p2s3          = $this->input->post('p2s3');
        $p6s8          = $this->input->post('p6s8');
        $p6s9          = $this->input->post('p6s9');
        $sub_parameter = json_decode($this->input->post('sub_parameter'), TRUE);

        if (isset($parameter_id) and $parameter_id != '') {
            // $sub_parameter_result  = $this->common_model->get_selected_values('subparameter_mst', 'id,description,has_sentences_keyword','status=1 AND parameter_id="'.$parameter_id.'"');
            $this->db->select('id,description,has_sentences_keyword');
            $this->db->from('subparameter_mst');
            $this->db->where('status', '1');
            if ($assessment_type == "2") {
                $sub_where =  ' id != 5 and parameter_id = "' . $parameter_id . '"  ';
                $this->db->where($sub_where);
            } else {
                $this->db->where('parameter_id', $parameter_id);
            }
            $this->db->where('status', 1);
            // $this->db->where('parameter_id', $parameter_id);
            $sub_parameter_result =  $this->db->get()->result();
            if (isset($sub_parameter_result) and count((array)$sub_parameter_result) > 0) {
                foreach ($sub_parameter_result as $s) {
                    $has_sentences_keyword = $s->has_sentences_keyword;

                    $disabled = '';
                    $p2s2 = 0;
                    $p2s3 = 0;
                    $p6s8 = 0;
                    $p6s9 = 0;
                    $search_p2s2 = array('txn_id' => $txn_id, 'parameter_id' => 2, 'subparameter_id' => 2);
                    $filter_p2s2 = $this->search($sub_parameter, $search_p2s2);
                    if (isset($filter_p2s2) and count((array)$filter_p2s2) > 0) {
                        $p2s2 = 1;
                    }
                    $search_p2s3 = array('txn_id' => $txn_id, 'parameter_id' => 2, 'subparameter_id' => 3);
                    $filter_p2s3 = $this->search($sub_parameter, $search_p2s3);
                    if (isset($filter_p2s3) and count((array)$filter_p2s3) > 0) {
                        $p2s3 = 1;
                    }
                    $search_p6s8 = array('txn_id' => $txn_id, 'parameter_id' => 6, 'subparameter_id' => 8);
                    $filter_p6s8 = $this->search($sub_parameter, $search_p6s8);
                    if (isset($filter_p6s8) and count((array)$filter_p6s8) > 0) {
                        $p6s8 = 1;
                    }
                    $search_p6s9 = array('txn_id' => $txn_id, 'parameter_id' => 6, 'subparameter_id' => 9);
                    $filter_p6s9 = $this->search($sub_parameter, $search_p6s9);
                    if (isset($filter_p6s9) and count((array)$filter_p6s9) > 0) {
                        $p6s9 = 1;
                    }
                    if ($parameter_id == 2 and $s->id == 2 and $p6s9 == 1) {
                        $disabled = 'disabled="disabled"';
                    }
                    if ($parameter_id == 6 and $s->id == 9 and $p2s2 == 1) {
                        $disabled = 'disabled="disabled"';
                    }
                    if ($parameter_id == 2 and $s->id == 3 and $p6s8 == 1) {
                        $disabled = 'disabled="disabled"';
                    }
                    if ($parameter_id == 6 and $s->id == 8 and $p2s3 == 1) {
                        $disabled = 'disabled="disabled"';
                    }

                    if (isset($sub_parameter)) {
                        $search_items = array('txn_id' => $txn_id, 'parameter_id' => $parameter_id, 'subparameter_id' => $s->id);
                        $filter_myarray = $this->search($sub_parameter, $search_items);
                        if (isset($filter_myarray) and count((array)$filter_myarray) > 0) {
                            $type_id          = $filter_myarray[0]['type_id'];
                            $sentence_keyword = $filter_myarray[0]['sentence_keyword'];
                            $html .= '<tr><td class="table-checkbox "><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
							<input type="checkbox" class="checkboxes" name="chksp_list[]" value="' . $s->id . '" id="chksp_id' . $s->id . '" checked="checked" onchange="hide_unhide_keysent(' . $s->id . ',' . $has_sentences_keyword . ')" ' . $disabled . '/>
							<span></span></label></td>
							<td id="lblsp_id' . $s->id . '">' . $s->description . '</td>
							<td>
								<select id="type_id' . $s->id . '" name="type_id[]" class="form-control input-sm select2 hide" placeholder="Please select" style="width:100px;">
								<option value="1" ' . (($type_id == 1) ? "selected" : "") . '>Sentence</option>
								<option value="2" ' . (($type_id == 2) ? "selected" : "") . '>Keyword</option>
								</select>
							</td>
							<!-- <td ><textarea id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide">' . $sentence_keyword . '</textarea></td> -->
							<td>
								<textarea readonly id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide">' . $sentence_keyword . '</textarea>
								<a class="btn btn-success btn-sm" href="' . base_url() . 'create_trinity/add_sentences/' . $s->id . '" id="MybtnModal' . $s->id . '" class="hide btn btn-primary MybtnModal"
							accesskey="" data-target="#Mymodalid" data-toggle="modal" style="margin-top:5px !important;">Add Sentence / Keyword </a>
                            </td>
							</tr>';
                        } else {
                            $html .= '<tr><td class="table-checkbox "><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
							<input type="checkbox" class="checkboxes" name="chksp_list[]" value="' . $s->id . '" id="chksp_id' . $s->id . '" onchange="hide_unhide_keysent(' . $s->id . ',' . $has_sentences_keyword . ')" ' . $disabled . '/>
							<span></span></label></td>
							<td id="lblsp_id' . $s->id . '">' . $s->description . '</td>
							<td>
								<select id="type_id' . $s->id . '" name="type_id[]" class="form-control input-sm select2 hide" placeholder="Please select" style="width:100px;">
								<option value="1">Sentence</option>
								<option value="2">Keyword</option>
								</select>
							</td>
							<!-- <td ><textarea id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea></td> -->
							<td>
								<textarea readonly id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea>
								<a class="btn btn-success btn-sm" href="' . base_url() . 'create_trinity/add_sentences/' . $s->id . '" id="MybtnModal' . $s->id . '" class="hide btn btn-primary MybtnModal"
							accesskey="" data-target="#Mymodalid" data-toggle="modal" style="margin-top:5px !important;">Add Sentence / Keyword </a>
							</td>
							</tr>';
                        }
                    } else {
                        $html .= '<tr><td class="table-checkbox "><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
							<input type="checkbox" class="checkboxes" name="chksp_list[]" value="' . $s->id . '" id="chksp_id' . $s->id . '" onchange="hide_unhide_keysent(' . $s->id . ',' . $has_sentences_keyword . ')" ' . $disabled . '/>
							<span></span></label></td>
							<td id="lblsp_id' . $s->id . '">' . $s->description . '</td>
							<td >
								<select id="type_id' . $s->id . '" name="type_id[]" class="form-control input-sm select2 hide" placeholder="Please select" style="width:100px;">
								<option value="1">Sentence</option>
								<option value="2">Keyword</option>
								</select>
							</td>
							<!--<td ><textarea id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea></td> -->
							<td>
								<textarea readonly id="sentkey' . $s->id . '" name="sentkey[]" rows="4" cols="50" class="hide"></textarea>
								<a class="btn btn-success btn-sm" href="' . base_url() . 'create_trinity/add_sentences/' . $s->id . '" id="MybtnModal' . $s->id . '" class="hide btn btn-primary MybtnModal"
							accesskey="" data-target="#Mymodalid" data-toggle="modal" style="margin-top:5px !important;">Add Sentence / Keyword </a>
							</td>
							</tr>';
                    }
                }
            } else {
                $html .= '<tr><td colspan="4">Sub parameters not available.</td></tr>';
            }
        } else {
            $html .= '<tr><td colspan="4">To display sub-parameters, first choose parameter from the above list.</td></tr>';
        }
        $json['html'] = $html;
        echo json_encode($json);
    }
    public function add_sentences($sentence_keyword_id)
    {
        $data['row_val'] = $sentence_keyword_id;
        $this->load->view('create_trinity/sentence_keyword_modal', $data);
    }
    public function ajax_parameters_labels()
    {
        $parameter_id = $this->input->post('parameter_id', TRUE);
        $assessment_type = $this->security->xss_clean($this->input->post('assessment_type', TRUE));
        if ($parameter_id != '') {
            $this->db->select('id,description');
            $this->db->from('goal_mst');
            $this->db->where('parameter_id', $parameter_id);
            $data['result'] =  $this->db->get()->result();
        } else {
            // $data['result'] = $this->common_model->get_selected_values('goal_mst', 'id,description','status=1');
            $this->db->select('id,description');
            $this->db->from('goal_mst');
            $this->db->where('status', '1');
            $data['result'] =  $this->db->get()->result();
        }
        echo json_encode($data);
    }


    public function add_parameter_weights()
    {
        $Question_idarray = $this->input->post('rowid');
        $parameter_weight = $this->input->post('weight');
        $parameter_id = $this->input->post('parameter_id');
        $lchtml = '';
        $New_parameter_str = '';
        $New_Parameter_Array = array();
        if (count((array)$Question_idarray) > 0) {
            foreach ($Question_idarray as $key => $question_id) {
                $Old_parameter_id = $this->input->post('Old_parameter_id' . $question_id);
                $New_parameter_id = $this->input->post('New_parameter_id' . $question_id);
                if (count((array)$Old_parameter_id) > 0) {
                    if (count((array)$New_parameter_id) > 0) {
                        $NewParameterArray = array_merge($Old_parameter_id, $New_parameter_id);
                    }
                    $NewParameterArray = $Old_parameter_id;
                } else {
                    $NewParameterArray = $New_parameter_id;
                }
                if (count((array)$NewParameterArray) > 0) {
                    $New_Parameter_Array = array_merge($New_Parameter_Array, $NewParameterArray);
                }
            }
        }
        $parameter_data = array();
        if (count((array)$New_Parameter_Array) > 0) {
            $New_parameter_str = implode(',', array_unique($New_Parameter_Array));
            $parameter_data = $this->common_model->get_selected_values('parameter_mst', 'id,description as parameter', 'id IN(' . $New_parameter_str . ')');
        }
        if (count((array)$parameter_data) > 0) {
            foreach ($parameter_data as $key => $para) {
                $lchtml .= '<tr id="prow-' . $para->id . '">';
                $lchtml .= '<td> <span id="parameter_text_' . $para->id . '">' . $para->parameter
                    . '</span><input type="hidden" id="parameterid' . $para->id . '" name="parameter_id[' . $para->id . ']" value="' . (isset($parameter_id[$para->id]) ? $parameter_id[$para->id] : '') . '" ></td>';
                $lchtml .= '<td><input type="number" id="weight' . $para->id . '" name="weight[' . $para->id . ']" class="form-control input-sm percent_cnt" value="' . (isset($parameter_weight[$para->id]) ? $parameter_weight[$para->id] : '') . '" onchange="get_weight()"></td></tr>';
            }
            $lchtml .= '<tr style="font-weight:bold;"><td>Total</td><td><input type="number" id="total_weight" name="total_weight" class="form-control input-sm " value="" disabled></td></tr>';
        }
        $data['html'] = $lchtml;
        echo json_encode($data);
    }
    // Map Goal end here

    public function add_parameter_weight()
    {
        $SuccessFlag = 1;
        $Message = '';
        $editid = $this->input->post('editid');
        $edit_id = base64_decode($editid);
        if (isset($edit_id)) {
            $parameter_idArray = $this->input->post('parameter_id');
            if (isset($parameter_idArray) && count($parameter_idArray) > 0) {
                $sum = 0;
                $null_is = 1;
                foreach ($parameter_idArray as $key => $value) {
                    if ($value != '') {
                        $sum += $value;
                    }
                }
                if ($sum != 0) {
                    foreach ($parameter_idArray as $key => $value) {
                        if ($value == '' || $value == 0) {
                            $null_is = 0;
                        }
                    }
                }

                if (($sum != 0) && ($sum != 100)) {
                    $Message = 'Goal Weight should be 100 or 0 Only..!!';
                    $SuccessFlag = 0;
                } else  if (($null_is != 1)) {
                    $Message = 'Pealse add weight for all Goals..!!';
                    $SuccessFlag = 0;
                } else {
                    foreach ($parameter_idArray as $key => $value) {
                        $this->db->set('parameter_weight', $value);
                        $this->db->where('assessment_id', $edit_id);
                        $this->db->where('goal_id', $key);
                        $this->db->update('trinity_trans_sparam');
                    }
                    $para_Weight_html = '';
                    $get_parametervalue = $this->common_model->get_selected_values('trinity_trans_sparam', 'goal_id,goal,parameter_weight', 'assessment_id=' . $edit_id);
                    if (count((array)$get_parametervalue) > 0) {

                        $para_Weight_html .= "<table class='table table-hover' border='1'>";
                        $para_Weight_html .= "<thead>";
                        $para_Weight_html .= "<tr>";
                        $para_Weight_html .= "<th scope='col' style='text-align:center'>Mapped Goal</th>";
                        $para_Weight_html .= "<th scope='col'style='text-align:center'>Goal Weight</th>";
                        $para_Weight_html .= '</tr>';
                        $para_Weight_html .= "<thead>";
                        $para_Weight_html .= "<tbody>";
                        foreach ($get_parametervalue as $pw) {
                            $para_Weight_html .= '<tr>';
                            $para_Weight_html .= '<td style="text-align:center;" id="goal' . $pw->goal_id . '">' . $pw->goal . ' </td>';
                            $para_Weight_html .= '<td style="text-align:center;">' . $pw->parameter_weight . '</td>';
                            $para_Weight_html .= '</tr>';
                        }
                        $para_Weight_html .= "</tbody>";
                        $para_Weight_html .= '</table>';
                        $data['partameter_weight_table'] = $para_Weight_html;
                    }
                    $Message .= "Mapping goal weight Successfully..!!";
                    $SuccessFlag = 1;
                }
            } else {
                $Message .= "Please select Goals ..!!";
                $SuccessFlag = 0;
            }
        } else {
            $Message .= "Please select Goals ..!!";
            $SuccessFlag = 0;
        }
        $data['Encode_id'] = $editid;
        $data['Message'] = $Message;
        $data['Success'] = $SuccessFlag;
        echo json_encode($data);
    }

    public function regenerate_weight_table($encode_id)
    {
        $SuccessFlag = 1;
        $Message = '';
        $edit_id = base64_decode($encode_id);
        if (isset($edit_id)) {
            $para_Weight_html = '';
            $get_parametervalue = $this->common_model->get_selected_values('trinity_trans_sparam', 'goal_id,goal,parameter_weight', 'assessment_id=' . $edit_id);
            if (count((array)$get_parametervalue) > 0) {
                $para_Weight_html .= "<table class='table table-hover' border='1'>";
                $para_Weight_html .= "<thead>";
                $para_Weight_html .= "<tr>";
                $para_Weight_html .= "<th scope='col' style='text-align:center'>Mapped Goal</th>";
                $para_Weight_html .= "<th scope='col'style='text-align:center'>Goal Weight</th>";
                $para_Weight_html .= '</tr>';
                $para_Weight_html .= "<thead>";
                $para_Weight_html .= "<tbody>";
                foreach ($get_parametervalue as $pw) {
                    $para_Weight_html .= '<tr>';
                    $para_Weight_html .= '<td style="text-align:center;" id="goal' . $pw->goal_id . '">' . $pw->goal . ' </td>';
                    $para_Weight_html .= '<td style="text-align:center;">' . $pw->parameter_weight . '</td>';
                    $para_Weight_html .= '</tr>';
                }
                $para_Weight_html .= "</tbody>";
                $para_Weight_html .= '</table>';
                $data['partameter_weight_table'] = $para_Weight_html;
            }
        } else {
            $data['partameter_weight_table'] = '';
        }
        $data['Message'] = "Success";
        $data['Success'] = $SuccessFlag;
        echo json_encode($data);
    }
}