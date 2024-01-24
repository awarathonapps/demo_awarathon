<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment_create extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('assessment_create');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('assessment_create_model');
        $this->demo_assessment_id = 11;
    }

    public function index()
    {
        $data['module_id'] = '13.04';
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
        $this->load->view('assessment_create/index', $data);
    }

    public function create($errors = "")
    {
        $data['module_id'] = '13.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('assessment_create');
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
        //Added for AI, Manual and Combined
        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');
        $this->session->unset_userdata('NewSupervisorsArrray_session');
        $this->session->unset_userdata('NewManagersArrray_session');
        $this->load->view('assessment_create/create', $data);
    }

    public function get_question_title()
    {
        $question_id = $this->input->post('question_id');
        $Question_set = $this->common_model->get_value('assessment_question', 'question', 'id=' . $question_id);
        $data['lchtml'] = $Question_set->question;
        echo json_encode($data);
    }

    // DCP
    public function append_questions($tr_no)
    {
        $assessment_id = base64_decode($this->input->post('Encode_id'));
        $NewQuestionArray = $this->input->post('NewQuestionArray');
        $start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
        $message = '';
        /* if($start_date !='' && count((array)$NewQuestionArray) > 0){
        $hcwhere =" '".$start_date."' >= from_date AND '".$start_date."' <= to_date ";
        $billmin_set = $this->common_model->get_value('company_billing_minute', 'from_date,to_date,allocated_minute', $hcwhere);
        if(count((array)$billmin_set)>0){
        $dwhere = " WHERE date(am.start_dttm) BETWEEN '$billmin_set->from_date' AND '$billmin_set->to_date' ";
        $played_min = $this->assessment_create_model->get_assessment_mindata($dwhere);
        $question_str = implode(',', $NewQuestionArray);
        $question_set = $this->common_model->get_value('assessment_question', 'IFNULL(FORMAT(CONCAT((FLOOR(SUM(response_timer)/60)),".",(SUM(response_timer)%60)),2),0) AS question_time', 'id IN('.$question_str.')');
        $totalmin = $played_min + $question_set->question_time;
        if($billmin_set->allocated_minute < $totalmin){
        $message .=" Sorry,You don't have minutes to mapped these questoins";
        }
        }else{
        $message .=" Sorry,No minutes are allocated to this assessment date";
        }
        } */
        $company_id = $this->mw_session['company_id'];
        $lchtml = '';
        if (count((array) $NewQuestionArray) > 0) {
            $assessment_type = $this->input->post('assessment_type', true);
            $Pdata = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'company_id=' . $company_id);
            // $Pdata = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'assessment_type=' . $assessment_type . ' AND company_id=' . $company_id);
            //$aimeth_result = $this->common_model->get_selected_values('aimethods_mst', 'id,description','status=1');
            $language_result = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
            $temp_id = array();
            foreach ($NewQuestionArray as $key => $question_id) {
                if (in_array($question_id, $temp_id)) {
                    continue;
                }
                $temp_id[] = $question_id;
                $Question_set = $this->common_model->get_value('assessment_question', 'question', 'id=' . $question_id);
                $lchtml .= '<tr id="Row-' . $tr_no . '">';
                if ($assessment_type == "2") {
                    $lchtml .= '<td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="checkboxes is_default" id="is_default' . $question_id . '" name="is_default[' . $question_id . ']" value="1"/>
                    <span></span>
                </label></td>';
                }
                $lchtml .= '<td> <span id="question_text_' . $tr_no . '">' . $Question_set->question
                    . '</span>
							<input type="hidden" id="txt_trno' . $tr_no . '" name="txt_trno_' . $tr_no . '" class="txt_trno" value="' . $tr_no . '" >
							<input type="hidden" id="question_id' . $tr_no . '" name="New_question_id[' . $tr_no . ']" value="' . $question_id . '" ></td>';
                // $lchtml .= '<td><select id="aimethods_id' . $tr_no . '" name="aimethods_id' . $tr_no . '[]" class="form-control input-sm select2 ValueUnq" style="width:100%" multiple placeholder="Please select" style="width:160px;">';
                // foreach ($aimeth_result as $aim_data) {
                // 	$lchtml .= '<option value="' . $aim_data->id . '">' . $aim_data->description . '</option>';
                // }
                // $lchtml .= '</select></td>';
                $lchtml .= '<td><select id="language_id' . $tr_no . '" name="language_id' . $tr_no . '" class="form-control input-sm select2 ValueUnq language_id" style="width:100%" placeholder="Please select" style="width:100px;" >';
                foreach ($language_result as $language_data) {
                    $lchtml .= '<option value="' . $language_data->id . '">' . $language_data->name . '</option>';
                }
                $lchtml .= '</select></td>';
                $lchtml .= '<td><div id="paramsub' . $tr_no . '"></div>
				<select id="parameter_id' . $tr_no . '" name="New_parameter_id' . $tr_no . '[]" multiple style="display:none;" onchange="getUnique_paramters()">';
                foreach ($Pdata as $p) {
                    $lchtml .= '<option value="' . $p->id . '">' . $p->description . '</option>';
                }
                $lchtml .= '</select></td>';

                // $lchtml .= '<td><select id="type_id' . $tr_no . '" name="type_id[]" class="form-control input-sm select2" placeholder="Please select" style="width:100px;">';
                // $lchtml .= '<option value="1">Sentence</option>';
                // $lchtml .= '<option value="2">Keyword</option>';
                // $lchtml .= '</select></td>';
                // $lchtml .= '<td><textarea id="sentkey' . $tr_no . '" name="sentkey[]" rows="4" cols="50"></textarea></td>';
                // $lchtml .= '<td><select id="parameter_id' . $tr_no . '" name="New_parameter_id' . $tr_no . '[]" class="form-control input-sm select2 ValueUnq" style="width:100%" multiple onchange="getUnique_paramters()">';
                // foreach ($Pdata as $p) {
                //     $lchtml .= '<option value="' . $p->id . '">' . $p->description . '</option>';
                // }
                // $lchtml .= '</select></td>';
                $lchtml .= '<input type="hidden" name="rowid[]" value="' . $tr_no . '"/>';
                $lchtml .= '<td>
							<a class="btn btn-success btn-sm" href="' . base_url() . 'assessment_create/add_parameters/' . $tr_no . '/' . $assessment_type . '/' . $company_id . '" 
							accesskey="" data-target="#LoadModalFilter" data-toggle="modal">Manage Parameters </a>
							<a class="btn btn-success btn-sm" href="' . base_url() . 'assessment_create/edit_questions/' . $tr_no . '" 
                            accesskey=""  data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-pencil"></i> </a>'
                    . '<button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $tr_no . ')";><i class="fa fa-times"></i></button> </td>';
                $lchtml .= "<script></script></tr>";
                $tr_no++;
            }
        }
        $data['Msg'] = $message;
        $data['tr_no'] = $tr_no;
        $data['lchtml'] = $lchtml;
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
        if (count((array) $Question_idarray) > 0) {
            foreach ($Question_idarray as $key => $question_id) {
                $Old_parameter_id = $this->input->post('Old_parameter_id' . $question_id);
                $New_parameter_id = $this->input->post('New_parameter_id' . $question_id);
                if (count((array) $Old_parameter_id) > 0) {
                    if (count((array) $New_parameter_id) > 0) {
                        $NewParameterArray = array_merge($Old_parameter_id, $New_parameter_id);
                    }
                    $NewParameterArray = $Old_parameter_id;
                } else {
                    $NewParameterArray = $New_parameter_id;
                }
                if (count((array) $NewParameterArray) > 0) {
                    $New_Parameter_Array = array_merge($New_Parameter_Array, $NewParameterArray);
                }
            }
        }
        $parameter_data = array();
        if (count((array) $New_Parameter_Array) > 0) {
            $New_parameter_str = implode(',', array_unique($New_Parameter_Array));
            $parameter_data = $this->common_model->get_selected_values('parameter_mst', 'id,description as parameter', 'id IN(' . $New_parameter_str . ')');
        }
        if (count((array) $parameter_data) > 0) {
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
            $LockFlag = (count((array) $ISEXIST) > 0 ? 1 : 0);
            if (!$LockFlag) {
                $ISEXIST2 = $this->common_model->get_value('ai_schedule', 'id', 'assessment_id=' . $Copy_id);
                $LockFlag = (count((array) $ISEXIST2) > 0 ? 1 : 0);
            }
            $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $Copy_id);
            $edit_lockflag = (count((array) $isPlay2) > 0 ? 1 : 0);
        }
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $New_question_idArray = $this->input->post('New_question_id');
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            if ($Copy_id != "") {
                $Old_question_idArray = $this->input->post('Old_question_id');
                if (count((array) $New_question_idArray) > 0) {
                    if (isset($Old_question_idArray)) {
                        $AlreayExist = array_intersect($Old_question_idArray, $New_question_idArray);
                        if (count((array) $AlreayExist) > 0) {
                            $Message .= "Duplicate Questions Found..!<br/>";
                            $SuccessFlag = 0;
                        }
                    }
                    $Nduplicate = array_diff_assoc($New_question_idArray, array_unique($New_question_idArray));
                    if (count((array) $Nduplicate) > 0) {
                        $Message .= "Duplicate Questions Found..!!<br/>";
                        $SuccessFlag = 0;
                    }
                    foreach ($New_question_idArray as $key => $question_id) {
                        $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
                        $old_data = $this->common_model->get_value('assessment_trans', 'id', 'assessment_id=' . $Copy_id . ' AND question_id=' . $question_id);
                        if (count((array) $old_data) > 0) {
                            $Message .= "Duplicate Questions Found..!!<br/>";
                            $SuccessFlag = 0;
                        }
                        if (!isset($New_parameter_idArray)) {
                            $Message .= "Please Select Parameter!!!!.<br/>";
                            $SuccessFlag = 0;
                            break;
                        }
                    }
                }
                if (count((array) $Old_question_idArray) > 0) {
                    $Oduplicate = array_diff_assoc($Old_question_idArray, array_unique($Old_question_idArray));
                    if (count((array) $Oduplicate) > 0) {
                        $Message .= "Duplicate Questions Found..!";
                        $SuccessFlag = 0;
                    }
                    foreach ($Old_question_idArray as $key => $question_id) {
                        $Old_parameters = $this->input->post('Old_parameter_id' . $key);
                        if (count((array) $Old_parameters) == 0) {
                            $Message .= "Please Select Parameter!<br/>";
                            $SuccessFlag = 0;
                            break;
                        }
                    }
                }
            } else {
                if (count((array) $New_question_idArray) > 0) {
                    $duplicate = array_diff_assoc($New_question_idArray, array_unique($New_question_idArray));
                    if (count((array) $duplicate) > 0) {
                        $Message .= "Duplicate questions Found..!<br/>";
                        $SuccessFlag = 0;
                    }
                    foreach ($New_question_idArray as $key => $v) {
                        $tmp = $this->input->post('New_parameter_id' . $key);
                        if (count((array) $tmp) == 0) {
                            $Message .= "Please Select Parameter!<br/>";
                            $SuccessFlag = 0;
                            break;
                        }
                    }
                }
            }

            // $this->form_validation->set_rules('assessment_type', 'Assessment Type', 'required');
            $this->form_validation->set_rules('assessment_name', 'Assessment Name', 'required');
            $this->form_validation->set_rules('number_attempts', 'Number attempts', 'required');
            $this->form_validation->set_rules('instruction', 'instruction', 'required');
            // $this->form_validation->set_rules('ratingstyle', 'Rating Type', 'required');
            // $this->form_validation->set_rules('question_type', 'Question Type', 'required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('assessor_date', 'Assesser Date', 'required');

            if ($this->input->post('isweights') == 1) {
                $this->form_validation->set_rules('weight[]', 'Weight', 'required');
            }


            // $sub_parameter_result = json_decode($this->input->post('sub_parameter'),TRUE); 
            $sub_parameter_result = $this->input->post('sub_parameter');
            if (isset($sub_parameter_result) and count((array) $sub_parameter_result) <= 0) {
                $Message .= "Please map the parameters and sub-parameters to the question.<br/>";
                $SuccessFlag = 0;
            }
            if (isset($sub_parameter_result) and count((array) $sub_parameter_result) > 0) {
                foreach ($sub_parameter_result as $sparam) {
                    $txn_id = $sparam['txn_id'];
                    $language_id = $this->input->post('language_id' . $txn_id);
                    if ($language_id == '') {
                        $Message = "Please map the language to the question.<br/>";
                        $SuccessFlag = 0;
                    }
                }
            }
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $start_date = strtotime($this->input->post('start_date'));
                $end_date = strtotime($this->input->post('end_date'));
                $assessor_date = strtotime($this->input->post('assessor_date'));

                if ($start_date < strtotime(date('Y-m-d H:i:s'))) {
                    $Message .= "Start date can not be less than todays date..";
                    $SuccessFlag = 0;
                }
                if ($start_date > $end_date) {
                    $Message .= "Start date cannot be more than end date..<br/>";
                    $SuccessFlag = 0;
                } elseif ($assessor_date < $end_date) {
                    $Message .= "Assessor last date cannot be less than End date..<br/>";
                    $SuccessFlag = 0;
                }
                if (isset($Old_question_idArray) && isset($New_question_idArray) && count((array) $Old_question_idArray) == 0 && count((array) $New_question_idArray) == 0) {
                    $Message = "Please select atleast one question..<br/>";
                    $SuccessFlag = 0;
                }
                if ($Copy_id == "") {
                    $NewManagersArrray = $this->session->userdata('NewManagersArrray_session');
                    $NewSupervisorsArrray = $this->session->userdata('NewSupervisorsArrray_session');
                    if (!isset($NewManagersArrray) && count((array) $NewManagersArrray) == 0) {
                        $Message .= "Please Map Managers..<br/>";
                        $SuccessFlag = 0;
                    }
                    // if(count((array)$NewManagersArrray)>1)
                    // {
                    //     $Message.="Only one manager can be mapped";
                    //     $SuccessFlag = 0;
                    // }
                }
                if ($SuccessFlag) {
                    if (isset($Old_question_idArray) or isset($New_question_idArray) or count((array) $Old_question_idArray) > 0 or count((array) $New_question_idArray) > 0) {
                        $now = date('Y-m-d H:i:s');
                        $data = array(
                            'company_id' => $Company_id,
                            'assessment' => $this->input->post('assessment_name'),
                            'code' => $this->input->post('otc'),
                            'is_situation' => $this->input->post('question_type') != null ? $this->input->post('question_type') : '0',
                            'number_attempts' => $this->input->post('number_attempts'),
                            'assessment_type' => !empty($this->input->post('assessment_type')) ? $this->input->post('assessment_type') : 1,
                            'report_type' => $this->input->post('report_type'),
                            'ratingstyle' => $this->input->post('ratingstyle'),
                            'start_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('start_date'))),
                            'end_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('end_date'))),
                            'assessor_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date'))),
                            'instruction' => $this->input->post('instruction'),
                            'description' => $this->input->post('description'),
                            'is_preview' => ($this->input->post('is_preview') != null) ? 0 : 1,
                            // 'is_preview' => ($this->input->post('is_preview')==1 ? 1 : 0),
                            'ranking' => ($this->input->post('ranking') == 1 ? 1 : 0),
                            'is_weights' => array_sum(array_column($sub_parameter_result, 'parameter_weight')) > 0 ? 1 : 0,
                            'status' => 0,
                            'addeddate' => $now,
                            'addedby' => $this->mw_session['user_id'],
                        );
                        if ($this->security->xss_clean($this->input->post('assessment_type')) == "2") {
                            $data['question_limits'] = $this->security->xss_clean($this->input->post('question_limit'));
                        }
                        $insert_id = $this->common_model->insert('assessment_mst', $data);
                        if ($insert_id != "") {
                            if ($Copy_id != "") {
                                $this->assessment_create_model->CopyAllowedUsers($insert_id, $Copy_id);
                                $this->assessment_create_model->CopyAssessmentManagers($insert_id, $Copy_id);
                                $this->assessment_create_model->CopyUserManagersMapping($insert_id, $Copy_id);

                                $Old_parameters = $this->input->post('Old_parameter_id' . $key);

                                $assessment_trans = $this->common_model->get_selected_values('assessment_trans', 'id,question_id', 'assessment_id=' . $Copy_id);
                                foreach ($assessment_trans as $key => $value) {
                                    $trans_id = $value->id;
                                    if (isset($_POST['Old_question_id'][$trans_id]) && $_POST['Old_question_id'][$trans_id] != '') {
                                        $question_id = $this->input->post('Old_question_id', true)[$trans_id];
                                        $Old_parameter_idArray = $this->input->post('Old_parameter_id' . $trans_id, true);
                                        if (isset($_POST['is_default'][$question_id])) {
                                            $is_default = $this->input->post('is_default', true)[$question_id];
                                        } else {
                                            $is_default = 0;
                                        }
                                        $OASData = array(
                                            'assessment_id' => $insert_id,
                                            'question_id' => $question_id,
                                            'parameter_id' => implode(',', $Old_parameter_idArray),
                                            'is_default' => $is_default
                                        );
                                        $this->common_model->insert('assessment_trans', $OASData);
                                    }
                                }
                                $assessment_trans = $this->common_model->get_selected_values('assessment_trans', 'assessment_id,question_id', 'assessment_id=' . $Copy_id);
                                $trans_param_temp = [];
                                if (isset($sub_parameter_result) and count((array) $sub_parameter_result) > 0) {
                                    foreach ($sub_parameter_result as $pindex => $sparam) {
                                        $txn_id = $sparam['txn_id'];
                                        $temp = [
                                            'parameter_id' => $sparam['parameter_id'],
                                            'parameter_label_id' => $sparam['parameter_label_id'],
                                            'subparameter_id' => $sparam['subparameter_id'],
                                            'type_id' => $sparam['type_id'],
                                            'sentence_keyword' => htmlspecialchars_decode($sparam['sentence_keyword']),
                                            'parameter_weight' => $sparam['parameter_weight'],
                                            'language_id' => $this->input->post('language_id' . $txn_id)
                                        ];
                                        $trans_param_temp[$txn_id][] = $temp;
                                    }
                                }
                                $trans_param = [];
                                foreach ($trans_param_temp as $param) {
                                    $trans_param[] = $param;
                                }
                                $new_txn = 1;
                                if (!empty($assessment_trans)) {
                                    //remove question param for this assessment
                                    // $this->common_model->delete('assessment_trans_sparam', 'assessment_id',$insert_id);
                                    foreach ($assessment_trans as $aindex => $value) {
                                        foreach ($trans_param as $tindex => $param) {
                                            if ($aindex == $tindex) {
                                                foreach ($param as $pindex) {
                                                    $PSData = array(
                                                        'assessment_id' => $insert_id,
                                                        'question_id' => $value->question_id,
                                                        'language_id' => $pindex['language_id'],
                                                        'txn_id' => $new_txn,
                                                        'parameter_id' => $pindex['parameter_id'],
                                                        'parameter_label_id' => $pindex['parameter_label_id'],
                                                        'sub_parameter_id' => $pindex['subparameter_id'],
                                                        'type_id' => $pindex['type_id'],
                                                        'sentence_keyword' => htmlspecialchars_decode($pindex['sentence_keyword']),
                                                        'parameter_weight' => $pindex['parameter_weight'],
                                                    );
                                                    // echo "<br/>at 451 - insert"; print_r($PSData);
                                                    $ats_id = $this->common_model->insert('assessment_trans_sparam', $PSData);
                                                }
                                            }
                                        }
                                        $new_txn++;
                                    }
                                }
                                // foreach ($assessment_trans as $key => $value) {
                                //     if (isset($sub_parameter_result) AND count((array)$sub_parameter_result)>0){
                                //         foreach($sub_parameter_result as $sparam){
                                //             $txn_id                  = $sparam['txn_id'];
                                //             $parameter_id            = $sparam['parameter_id'];
                                //             $parameter_label_id      = $sparam['parameter_label_id'];
                                //             $subparameter_id         = $sparam['subparameter_id'];
                                //             $type_id                 = $sparam['type_id'];
                                //             // $sentence_keyword        = $sparam['sentence_keyword'];
                                //             $sentence_keyword        = htmlspecialchars_decode($sparam['sentence_keyword']);
                                //             $parameter_weight        = $sparam['parameter_weight'];
                                //             $language_id             = $this->input->post('language_id'.$txn_id);
                                //             if ((int)($txn_id-1) == (int)($key)){
                                //                 $PSData = array(
                                //                     'assessment_id'           => $insert_id,
                                //                     'question_id'             => $value->question_id,
                                //                     'language_id'             => $language_id,
                                //                     'txn_id'                  => $txn_id,
                                //                     'parameter_id'            => $parameter_id,
                                //                     'parameter_label_id'      => $parameter_label_id,
                                //                     'sub_parameter_id'        => $subparameter_id,
                                //                     'type_id'                 => $type_id,
                                //                     'sentence_keyword'        => $sentence_keyword,
                                //                     'parameter_weight'        => $parameter_weight,
                                //                 );
                                //                 $this->common_model->insert('assessment_trans_sparam', $PSData);
                                //             }
                                //         }
                                //     }
                                // }
                            }

                            if (isset($New_question_idArray) && count((array) $New_question_idArray) > 0) {
                                foreach ($New_question_idArray as $key => $question_id) {
                                    $New_parameter_str = '';
                                    $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
                                    if (isset($_POST['is_default'][$question_id])) {
                                        $is_default = $this->input->post('is_default', true)[$question_id];
                                    } else {
                                        $is_default = 0;
                                    }
                                    if (count((array) $New_parameter_idArray) > 0) {
                                        $New_parameter_str = implode(',', $New_parameter_idArray);
                                        $ASData = array(
                                            'assessment_id' => $insert_id,
                                            'question_id' => $question_id,
                                            'parameter_id' => $New_parameter_str,
                                            'is_default' => $is_default,
                                        );
                                        $this->common_model->insert('assessment_trans', $ASData);

                                        if ($Copy_id == "") {
                                            if (isset($sub_parameter_result) and count((array) $sub_parameter_result) > 0) {
                                                foreach ($sub_parameter_result as $sparam) {
                                                    $txn_id = $sparam['txn_id'];
                                                    $parameter_id = $sparam['parameter_id'];
                                                    $parameter_label_id = $sparam['parameter_label_id'];
                                                    $subparameter_id = $sparam['subparameter_id'];
                                                    $type_id = $sparam['type_id'];
                                                    // $sentence_keyword     = $sparam['sentence_keyword'];
                                                    $sentence_keyword = htmlspecialchars_decode($sparam['sentence_keyword']);
                                                    $parameter_weight = $sparam['parameter_weight'];
                                                    $language_id = $this->input->post('language_id' . $txn_id);

                                                    if ((int) $txn_id == (int) $key) {
                                                        $PSData = array(
                                                            'assessment_id' => $insert_id,
                                                            'question_id' => $question_id,
                                                            'language_id' => $language_id,
                                                            'txn_id' => $txn_id,
                                                            'parameter_id' => $parameter_id,
                                                            'parameter_label_id' => $parameter_label_id,
                                                            'sub_parameter_id' => $subparameter_id,
                                                            'type_id' => $type_id,
                                                            'sentence_keyword' => $sentence_keyword,
                                                            'parameter_weight' => $parameter_weight,
                                                        );
                                                        $this->common_model->insert('assessment_trans_sparam', $PSData);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (isset($NewManagersArrray) && count((array) $NewManagersArrray) > 0) {
                                foreach ($NewManagersArrray as $user_id) {
                                    $ISEXIST = $this->common_model->get_value('assessment_managers', 'id', 'assessment_id=' . $insert_id . ' AND trainer_id=' . $user_id);
                                    $Mdata = array(
                                        'trainer_id' => $user_id,
                                        'assessment_id' => $insert_id
                                    );
                                    if (count((array) $ISEXIST) > 0) {
                                        continue;
                                    } else {
                                        $this->common_model->insert('assessment_managers', $Mdata);
                                        // Jagdisha : 30/01/2023
                                        // Notification Module - For mail users
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
                                        if (count((array) $AllowSet) > 0) {
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
                            if (isset($NewSupervisorsArrray) && count((array) $NewSupervisorsArrray) > 0) {
                                foreach ($NewSupervisorsArrray as $user_id) {
                                    $ISEXIST = $this->common_model->get_value('assessment_supervisors', 'id', 'assessment_id=' . $insert_id . ' AND trainer_id=' . $user_id);
                                    $Sdata = array(
                                        'trainer_id' => $user_id,
                                        'assessment_id' => $insert_id
                                    );
                                    if (count((array) $ISEXIST) > 0) {
                                        continue;
                                    } else {
                                        $this->common_model->insert('assessment_supervisors', $Sdata);
                                    }
                                }
                            }
                            if ($this->input->post('isweights') == 1) {
                                $weight_array = $this->input->post('weight');
                                if (count((array) $weight_array) > 0) {
                                    foreach ($weight_array as $paraid => $weight) {
                                        $wdata = array(
                                            'assessment_id' => $insert_id,
                                            'parameter_id' => $paraid,
                                            'percentage' => $weight
                                        );
                                        $this->common_model->insert('assessment_para_weights', $wdata);
                                    }
                                }
                            }
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
                    } else {
                        $Message = "Please add Question first.!";
                        $SuccessFlag = 0;
                    }
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
        $assessment_type = $this->input->post('assessment_type');
        $id = base64_decode($Encode_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'id', 'assessment_id=' . $id);
        $LockFlag = (count((array) $ISEXIST) > 0 ? 1 : 0);
        if (!$LockFlag) {
            $ISEXIST2 = $this->common_model->get_value('ai_schedule', 'id', 'assessment_id=' . $id);
            $LockFlag = (count((array) $ISEXIST2) > 0 ? 1 : 0);
        }
        $isPlay2 = $this->common_model->get_selected_values('assessment_results', 'id', 'assessment_id=' . $id);
        $edit_lockflag = (count((array) $isPlay2) > 0 ? 1 : 0);
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $New_question_idArray = $this->input->post('New_question_id');
            $Old_question_idArray = $this->input->post('Old_question_id');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('assessment_name', 'Assessment Name', 'required');
            if (!$edit_lockflag) {
                $this->form_validation->set_rules('start_date', 'Start Date', 'required');
                // $this->form_validation->set_rules('question_type', 'Question Type', 'required');
                // $this->form_validation->set_rules('assessment_type', 'Assessment Type', 'required');
            }
            // if (!$LockFlag) {
            //     $this->form_validation->set_rules('ratingstyle', 'Rating Type', 'required');
            // }
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('assessor_date', 'Assesser Date', 'required');
            $this->form_validation->set_rules('number_attempts', 'Number attempts', 'required');
            //$this->form_validation->set_rules('otc', 'OTC', 'required');
            $this->form_validation->set_rules('instruction', 'instruction', 'required');
            if ($this->input->post('isweights') == 1) {
                $this->form_validation->set_rules('weight[]', 'Weight', 'required');
            }
            // $sub_parameter_result = json_decode($this->input->post('sub_parameter'),TRUE); 
            $sub_parameter_result = $this->input->post('sub_parameter');
            if (isset($sub_parameter_result) and count((array) $sub_parameter_result) <= 0) {
                $Message .= "Please map the parameters and sub-parameters to the question.<br/>";
                $SuccessFlag = 0;
            }
            // if (isset($sub_parameter_result) AND count((array)$sub_parameter_result)>0){
            // 	foreach($sub_parameter_result as $sparam){
            // 		$txn_id           = $sparam['txn_id'];
            // 		// $ai_methods_array = $this->input->post('aimethods_id'.$txn_id);
            // 		// if ($ai_methods_array ==''){
            // 		// 	$Message = "Please map the ai methods to the question.<br/>";
            // 		// 	$SuccessFlag = 0;
            // 		// }
            // 	}
            // }

            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $managers_data = $this->common_model->get_value('assessment_managers', 'trainer_id', 'assessment_id=' . $id);
                if (count((array) $managers_data) == 0 && $this->input->post('status') == 1) {
                    $Message = "Please Mapp Managers first..!";
                    $SuccessFlag = 0;
                    // if(count((array)$managers_data)>1)
                    // {
                    //     $Message = "Only one manager can be mapped";
                    //     $SuccessFlag=0;
                    // }
                } else {
                    if (!$edit_lockflag) {
                        $start_date = strtotime($this->input->post('start_date'));
                    } else {
                        $old_data = $this->common_model->get_value('assessment_mst', 'start_dttm', 'id=' . $id);
                        $start_date = $old_data->start_dttm;
                    }
                    $end_date = strtotime($this->input->post('end_date'));
                    $assessor_date = strtotime($this->input->post('assessor_date'));

                    // $Okey = 0;
                    // if(isset($Old_question_idArray)){
                    // 	$Okey = count((array)$Old_question_idArray);
                    // }else{
                    // 	$count = $this->common_model->get_value('assessment_trans', 'count((array)question_id) as qcount', 'assessment_id='. $id);
                    // 	$Okey = $count->qcount;
                    // }
                    if ($start_date > $end_date) {
                        $Message .= "Start date cannot be more than end date..<br/>";
                        $SuccessFlag = 0;
                    } elseif ($assessor_date < $end_date) {
                        $Message .= "Assessor last date cannot be less than End date..<br/>";
                        $SuccessFlag = 0;
                    }
                    // if($start_date < strtotime(date('Y-m-d H:i:s')) || $end_date < strtotime(date('Y-m-d H:i:s'))){
                    // 	$Message = "Start date and End date can not be less than todays date..<br/>";
                    // 	$SuccessFlag = 0;
                    // }

                    // Notification code start
                    $date = $this->input->post('end_date');
                    $Oed = $this->common_model->get_value('assessment_mst', 'end_dttm', 'id=' . $id);
                    // for mails 
                    $ed = $Oed->end_dttm;
                    $date1 = date_create($ed);
                    // Notification code end

                    if (isset($Old_question_idArray) && isset($New_question_idArray) && count((array) $Old_question_idArray) == 0 && count((array) $New_question_idArray) == 0) {
                        $Message = "Please select atleast one question..<br/>";
                        $SuccessFlag = 0;
                    }
                    if (count((array) $New_question_idArray) > 0) {
                        if (isset($Old_question_idArray)) {
                            $AlreayExist = array_intersect($Old_question_idArray, $New_question_idArray);
                            if (count((array) $AlreayExist) > 0) {
                                $Message .= "Duplicate Questions Found..!<br/>";
                                $SuccessFlag = 0;
                            }
                        }
                        $Nduplicate = array_diff_assoc($New_question_idArray, array_unique($New_question_idArray));
                        if (count((array) $Nduplicate) > 0) {
                            $Message .= "Duplicate Questions Found..!!<br/>";
                            $SuccessFlag = 0;
                        }
                        foreach ($New_question_idArray as $key => $question_id) {
                            // $pkey = $this->input->post('rowid')[$key];
                            $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
                            $old_data = $this->common_model->get_value('assessment_trans', 'id', 'assessment_id=' . $id . ' AND question_id=' . $question_id);
                            if (count((array) $old_data) > 0) {
                                $Message .= "Duplicate Questions Found..!!<br/>";
                                $SuccessFlag = 0;
                            }
                            if (!isset($New_parameter_idArray)) {
                                $Message .= "Please Select Parameter!!!!.<br/>";
                                $SuccessFlag = 0;
                                break;
                            }
                        }
                    }
                    if (count((array) $Old_question_idArray) > 0) {
                        $Oduplicate = array_diff_assoc($Old_question_idArray, array_unique($Old_question_idArray));
                        if (count((array) $Oduplicate) > 0) {
                            $Message .= "Duplicate Questions Found..!";
                            $SuccessFlag = 0;
                        }
                        foreach ($Old_question_idArray as $key => $question_id) {
                            $Old_parameters = $this->input->post('Old_parameter_id' . $key);
                            if (count((array) $Old_parameters) == 0) {
                                $Message .= "Please Select Parameter!<br/>";
                                $SuccessFlag = 0;
                                break;
                            }
                        }
                    }
                    $now = date('Y-m-d H:i:s');
                    if ($SuccessFlag) {
                        $data = array(
                            'assessment' => $this->input->post('assessment_name'),
                            'code' => $this->input->post('otc'),
                            'number_attempts' => $this->input->post('number_attempts'),
                            'end_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('end_date'))),
                            'assessor_dttm' => date("Y-m-d H:i:s", strtotime($this->input->post('assessor_date'))),
                            'instruction' => $this->input->post('instruction'),
                            'description' => $this->input->post('description'),
                            'is_preview' => ($this->input->post('is_preview') != null) ? 0 : 1,
                            // 'is_preview'      => ($this->input->post('is_preview')==1 ? 1 : 0),
                            // 'report_type'     => $this->input->post('report_type'),
                            'ranking' => ($this->input->post('ranking') == 1 ? 1 : 0),
                            'is_weights' => array_sum(array_column($sub_parameter_result, 'parameter_weight')) > 0 ? 1 : 0,
                            // 'is_weights'      => ($this->input->post('isweights')==1 ? 1 : 0),
                            'status' => $this->input->post('status'),
                            'modifieddate' => $now,
                            'modifiedby' => $this->mw_session['user_id'],
                        );
                        if (!$edit_lockflag) {
                            $data['is_situation'] = $this->input->post('question_type') != null ? $this->input->post('question_type') : '0';
                            $data['assessment_type'] = $this->input->post('assessment_type');
                            $data['report_type'] = $this->input->post('report_type');
                            $data['start_dttm'] = date("Y-m-d H:i:s", strtotime($this->input->post('start_date')));
                        }
                        if (!$LockFlag) {
                            $data['ratingstyle'] = $this->input->post('ratingstyle');
                        }
                        $this->common_model->update('assessment_mst', 'id', $id, $data);
                        // Notification Module - mail function for reps(user) and manger start here 24-01-2023
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
                            if (count((array) $emailTemplate_user) > 0) {
                                $subject = $emailTemplate_user->subject;
                                $replacement[0] = $subject;
                                $replacement[1] = $assessment_set->assessment;
                                // $userSet = $this->common_model->get_users_value('assessment_allow_users', 'user_id', 'assessment_id=' . $id);
                                $userSet = $this->common_model->get_assessment_wise_users('assessment_allow_users', $id);
                                if (count((array) $userSet) > 0) {

                                    foreach ($userSet as $a_id) {
                                        $UserData = $this->common_model->get_value('device_users', 'company_id,concat(firstname," ",lastname) as trainee_name,email', '  user_id =' . $a_id['user_id']);
                                        $ToName = $UserData->trainee_name;
                                        $email_to = $UserData->email;
                                        $Company_id = $UserData->company_id;

                                        $notify_reps = [
                                            'company_id' => $Company_id,
                                            'assessment_id' => $id,
                                            'email_alert_id' => $emailTemplate_user->alert_id,
                                            'user_id' => $a_id['user_id'],
                                            'role_id' => 3,
                                            'user_name' => $ToName,
                                            'email' => $email_to,
                                            'scheduled_at' => $now
                                        ];
                                        $this->common_model->insert('assessment_notification_schedule', $notify_reps);  //Add Reps to send date entension notification
                                        // $replacement[2] = '<a target="_blank" style="display: inline-block;
                                        //     background: #db1f48;
                                        //     padding: .45rem 1rem;
                                        //     box-sizing: border-box;
                                        //     border: none;
                                        //     border-radius: 3px;
                                        //     color: #fff;
                                        //     text-align: center;
                                        //     font-family: Lato,Arial,sans-serif;
                                        //     font-weight: 400;
                                        //     font-size: 1em;
                                        //     text-decoration:none;
                                        //     line-height: initial;" href="https://web.awarathon.com">View Assignment</a>';
                                        // $replacement[3] = $UserData->trainee_name;
                                        // $replacement[4] = date("d-m-Y h:i a", strtotime($date));
                                        // $replacement[5] = 'info@awarathon.com';
                                        // $message = $emailTemplate_user->message;
                                        // $body = preg_replace($pattern, $replacement, $message);
                                        // $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                                    }
                                }
                            }
                            //Mail fucntion for managers
                            $mangerSet = $this->common_model->get_users_value('assessment_managers', 'trainer_id', 'assessment_id=' . $id);
                            if (count((array) $emailTemplate_manager) > 0) {
                                $subject = $emailTemplate_manager->subject;
                                $replacement[0] = $subject;
                                $replacement[1] = $assessment_set->assessment;
                                foreach ($mangerSet as $m_id) {

                                    $ManagerSet = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name,email,company_id', "userid=" . $m_id['trainer_id']);
                                    if(!empty($ManagerSet)){
                                        $ToName = $ManagerSet->trainer_name;
                                        $email_to = $ManagerSet->email;
                                        $Company_id = $ManagerSet->company_id;

                                        $notify_managers = [
                                            'company_id' => $Company_id,
                                            'assessment_id' => $id,
                                            'email_alert_id' => $emailTemplate_manager->alert_id,
                                            'user_id' => $m_id['trainer_id'],
                                            'role_id' => 2,
                                            'user_name' => $ToName,
                                            'email' => $email_to,
                                            'scheduled_at' => $now
                                        ];
                                        $this->common_model->insert('assessment_notification_schedule', $notify_managers);      //Add Managers to send date entension notification
                                    }

                                    // $replacement[2] = '<a target="_blank" style="display: inline-block;
                                    //     width: 200px;
                                    //     height: 20px;
                                    //     background: #db1f48;
                                    //     padding: 10px;
                                    //     text-align: center;
                                    //     border-radius: 5px;
                                    //     color: white;
                                    //     border: 1px solid black;
                                    //     text-decoration:none;
                                    //     font-weight: bold;" href="' . base_url() . 'assessment/view/' . $id . '/2">View Assignment</a>';
                                    // $replacement[3] = $ManagerSet->trainer_name;
                                    // $replacement[4] = date("d-m-Y h:i a", strtotime($date));
                                    // $replacement[5] = ''; //
                                    // $ToName = $ManagerSet->trainer_name;
                                    // $email_to = $ManagerSet->email;
                                    // $Company_id = $ManagerSet->company_id;
                                    // $message = $emailTemplate_manager->message;
                                    // $body = preg_replace($pattern, $replacement, $message);
                                    // $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                                }
                            }
                        }
                        //mail function for reps and managers end here 24-01-2023


                        //if(count((array)$Old_question_idArray) > 0){
                        $Old_parameters = $this->input->post('Old_parameter_id');
                        $assessment_trans = $this->common_model->get_selected_values('assessment_trans', 'id,question_id', 'assessment_id=' . $id);

                        foreach ($assessment_trans as $key => $value) {
                            $trans_id = $value->id;
                            if (isset($_POST['Old_question_id'][$trans_id]) && $_POST['Old_question_id'][$trans_id] != '') {
                                $question_id = $this->input->post('Old_question_id', true)[$trans_id];
                                $Old_parameter_idArray = $this->input->post('Old_parameter_id' . $trans_id, true);
                                if ($assessment_type == 2) {
                                    if (isset($_POST['is_default'][$question_id])) {
                                        $is_default = $this->input->post('is_default', true)[$question_id];
                                    } else {
                                        $is_default = 0;
                                    }
                                }
                                $OASData = array(
                                    'question_id' => $question_id,
                                    'parameter_id' => implode(',', $Old_parameter_idArray),
                                );
                                if ($assessment_type == 2) {
                                    $OASData['is_default'] = $is_default;
                                }
                                $this->common_model->update('assessment_trans', 'id', $trans_id, $OASData);
                            } else {
                                $ISLEXIST = $this->common_model->get_value('assessment_results_trans', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
                                $ISLOCK = (count((array) $ISLEXIST) > 0 ? 1 : 0);
                                if (!$ISLOCK) {
                                    $ISLEXIST2 = $this->common_model->get_value('ai_schedule', 'id', 'assessment_id=' . $id . ' AND question_id=' . $value->question_id);
                                    $ISLOCK = (count((array) $ISLEXIST2) > 0 ? 1 : 0);
                                }
                                if (!$ISLOCK) {
                                    $this->common_model->delete('assessment_trans', 'id', $trans_id);
                                }
                            }
                        }
                        // }else{
                        // 	if(!$LockFlag) {
                        // 		$this->common_model->delete('assessment_trans','assessment_id',$id);
                        // 	}
                        // }

                        if (count((array) $New_question_idArray) > 0) {
                            foreach ($New_question_idArray as $key => $question_id) {
                                $New_parameter_str = '';
                                // $pkey = $this->input->post('rowid')[$key];
                                $New_parameter_idArray = $this->input->post('New_parameter_id' . $key);
                                if (count((array) $New_parameter_idArray) > 0) {
                                    $New_parameter_str = implode(',', $New_parameter_idArray);
                                    if ($assessment_type == 2) {
                                        if (isset($_POST['is_default'][$question_id])) {
                                            $is_default = $this->input->post('is_default', true)[$question_id];
                                        } else {
                                            $is_default = 0;
                                        }
                                    }
                                    $ASData = array(
                                        'assessment_id' => $id,
                                        'question_id' => $question_id,
                                        'parameter_id' => $New_parameter_str,
                                    );
                                    if ($assessment_type == 2) {
                                        $OASData['is_default'] = $is_default;
                                    }
                                    $this->common_model->insert('assessment_trans', $ASData);
                                }
                            }
                        }

                        //KR
                        $assessment_trans = $this->common_model->get_selected_values('assessment_trans', 'assessment_id,question_id', 'assessment_id="' . $id . '"');
                        $trans_param_temp = [];
                        if (isset($sub_parameter_result) and count((array) $sub_parameter_result) > 0) {
                            foreach ($sub_parameter_result as $pindex => $sparam) {
                                $txn_id = $sparam['txn_id'];
                                $temp = [
                                    'parameter_id' => $sparam['parameter_id'],
                                    'parameter_label_id' => $sparam['parameter_label_id'],
                                    'subparameter_id' => $sparam['subparameter_id'],
                                    'type_id' => $sparam['type_id'],
                                    'sentence_keyword' => htmlspecialchars_decode($sparam['sentence_keyword']),
                                    'parameter_weight' => $sparam['parameter_weight'],
                                    'language_id' => $this->input->post('language_id' . $txn_id)
                                ];
                                $trans_param_temp[$txn_id][] = $temp;
                            }
                        }
                        $trans_param = [];
                        foreach ($trans_param_temp as $param) {
                            $trans_param[] = $param;
                        }
                        $new_txn = 1;
                        if (!empty($assessment_trans)) {
                            //remove question param for this assessment
                            $this->common_model->delete('assessment_trans_sparam', 'assessment_id', $id);
                            foreach ($assessment_trans as $aindex => $value) {
                                // echo "<br/><br/>Question: $value->question_id - New txn- ".$new_txn."<br/>";
                                foreach ($trans_param as $tindex => $param) {
                                    if ($aindex == $tindex) {
                                        foreach ($param as $pindex) {
                                            $PSData = array(
                                                'assessment_id' => $id,
                                                'question_id' => $value->question_id,
                                                'language_id' => $pindex['language_id'],
                                                'txn_id' => $new_txn,
                                                'parameter_id' => $pindex['parameter_id'],
                                                'parameter_label_id' => $pindex['parameter_label_id'],
                                                'sub_parameter_id' => $pindex['subparameter_id'],
                                                'type_id' => $pindex['type_id'],
                                                'sentence_keyword' => htmlspecialchars_decode($pindex['sentence_keyword']),
                                                'parameter_weight' => $pindex['parameter_weight'],
                                            );
                                            // echo "<br/>at 830 - insert"; print_r($PSData);
                                            $ats_id = $this->common_model->insert('assessment_trans_sparam', $PSData);
                                        }
                                    }
                                }
                                $new_txn++;
                            }
                        }
                        //DP
                        // foreach ($assessment_trans as $key => $value) {
                        //     $mykey = array();
                        // 	if (isset($sub_parameter_result) AND count((array)$sub_parameter_result)>0){
                        // 		foreach($sub_parameter_result as $sparam){
                        // 			$txn_id                  = $sparam['txn_id'];
                        // 			$parameter_id            = $sparam['parameter_id'];
                        // 			$parameter_label_id      = $sparam['parameter_label_id'];
                        // 			// $parameter_label_name = $sparam['parameter_label_name'];
                        // 			$subparameter_id         = $sparam['subparameter_id'];
                        // 			$type_id                 = $sparam['type_id'];
                        // 			// $sentence_keyword        = json_encode($sparam['sentence_keyword']);
                        // 			// $sentence_keyword        = htmlspecialchars($sparam['sentence_keyword']);
                        //             $sentence_keyword        = htmlspecialchars_decode($sparam['sentence_keyword']);
                        //             $parameter_weight        = $sparam['parameter_weight'];
                        // 			// $ai_methods_array     = $this->input->post('aimethods_id'.$txn_id);
                        // 			$language_id             = $this->input->post('language_id'.$txn_id);
                        // 			// $language_id             = $sparam['language_id'];
                        // 			// if(is_array($ai_methods_array)) {
                        // 			// 	$ai_methods          = implode(',', $ai_methods_array);
                        // 			// }
                        // 			if ((int)($txn_id-1) == (int)($key)){
                        //                 $txn_exists = $this->common_model->get_selected_values('assessment_trans_sparam', 'id', 'assessment_id="'.$id.'" AND question_id="'.$value->question_id.'" AND parameter_id="'.$parameter_id.'" AND parameter_label_id="'.$parameter_label_id.'" AND sub_parameter_id="'.$subparameter_id.'"');
                        //                 $txnid = '';
                        //                 foreach ($txn_exists as $txndata) {
                        //                     $mykey[]    = $txndata->id;    
                        //                     $txnid = $txndata->id;
                        //                 }
                        // 				if (isset($txn_exists) AND count((array)$txn_exists)>0){
                        //                     $update_data = array(
                        // 						'assessment_id'           => $id,
                        // 						'question_id'             => $value->question_id,
                        // 						// 'ai_methods'           => $ai_methods,
                        // 						'language_id'             => $language_id,
                        // 						'txn_id'                  => $txn_id,
                        // 						'parameter_id'            => $parameter_id,
                        // 						'parameter_label_id'      => $parameter_label_id,
                        // 						// 'parameter_label_name' => $parameter_label_name,
                        // 						'sub_parameter_id'        => $subparameter_id,
                        // 						'type_id'                 => $type_id,
                        // 						'sentence_keyword'        => $sentence_keyword,
                        //                         'parameter_weight'        => $parameter_weight,
                        // 					);
                        //                     $this->common_model->update('assessment_trans_sparam', 'id', $txnid, $update_data);
                        // 				}else{
                        // 					$PSData = array(
                        // 						'assessment_id'           => $id,
                        // 						'question_id'             => $value->question_id,
                        // 						// 'ai_methods'           => $ai_methods,
                        // 						'language_id'             => $language_id,
                        // 						'txn_id'                  => $txn_id,
                        // 						'parameter_id'            => $parameter_id,
                        // 						'parameter_label_id'      => $parameter_label_id,
                        // 						// 'parameter_label_name' => $parameter_label_name,
                        // 						'sub_parameter_id'        => $subparameter_id,
                        // 						'type_id'                 => $type_id,
                        // 						'sentence_keyword'        => $sentence_keyword,
                        //                         'parameter_weight'        => $parameter_weight,
                        // 					);
                        //                     $ats_id  = $this->common_model->insert('assessment_trans_sparam', $PSData);
                        //                     $mykey[] = $ats_id;
                        // 				}
                        // 			}
                        // 		}
                        //         $where_clause = "assessment_id='".$id."' AND question_id='".$value->question_id."'";
                        //         if(count((array)$mykey)>0){
                        //             $where_clause.= " AND id NOT IN(".implode(',', $mykey).")";
                        //         }
                        //         $this->common_model->delete_whereclause('assessment_trans_sparam', $where_clause);
                        // 	}
                        // }

                        if ($this->input->post('isweights') == 1) {
                            $para_array = array();
                            $weight_array = $this->input->post('weight');
                            if (count((array) $weight_array) > 0) {
                                foreach ($weight_array as $paraid => $weight) {
                                    $wdata = array(
                                        'assessment_id' => $id,
                                        'parameter_id' => $paraid,
                                        'percentage' => $weight
                                    );
                                    $exist_id = $this->input->post('parameter_id')[$paraid];
                                    if ($exist_id != '') {
                                        $para_array[] = $exist_id;
                                        $this->common_model->update('assessment_para_weights', 'id', $exist_id, $wdata);
                                    } else {
                                        $new_id = $this->common_model->insert('assessment_para_weights', $wdata);
                                        $para_array[] = $new_id;
                                    }
                                }
                            }
                            $ldcwhere = " assessment_id=" . $id;
                            if (count((array) $para_array) > 0) {
                                $ldcwhere .= " AND id NOT IN(" . implode(',', $para_array) . ")";
                            }
                            $this->common_model->delete_whereclause('assessment_para_weights', $ldcwhere); // Delete
                        }
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
        $data['module_id'] = '13.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $superaccess = $this->mw_session['superaccess'];
        $data['superaccess'] = ($superaccess ? 1 : 0);
        $login_id = $this->mw_session['user_id'];
        $ISEXIST = $this->common_model->get_value('assessment_supervisors', 'id,trainer_id', 'trainer_id=' . $login_id . ' AND assessment_id=' . $assessment_id);
        $data['is_supervisor'] = (count((array) $ISEXIST) > 0 ? 1 : 0);
        if (!$data['acces_management']->allow_edit) {
            redirect('assessment_create');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        //Added for AI report, Manual report and Combined report
        $data['report_type'] = $this->common_model->get_sel