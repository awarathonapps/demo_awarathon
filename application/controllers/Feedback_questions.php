<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_questions extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('feedback_questions');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('feedback_questions_model');
    }

    public function index() {
        $data['module_id'] = '7.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanySet'] = array();
            $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_questions/index', $data);
    }

    public function create() {
        $data['module_id'] = '7.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('feedback_questions');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id != "") {
            $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $Company_id);
        } else {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_questions/create', $data);
    }

    public function edit($id, $Errors = '') {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('feedback_questions');
            return;
        }
        $data['customr_errors'] = $Errors;
        $data['module_id'] = '7.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['RowSet'] = $this->common_model->fetch_object_by_id('feedback_questions', 'id', $Q_id);
        $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $data['RowSet']->company_id);
        $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        if ($data['RowSet']->feedback_type_id != "") {
            $data['feedback_subtypeSet'] = $this->common_model->getFeedbackSubTopic($data['RowSet']->feedback_type_id);
        }
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_questions/edit', $data);
    }

    public function view($id) {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('feedback_questions');
            return;
        }
        //echo $asset_url;
        //exit;
        $data['module_id'] = '7.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['RowSet'] = $this->common_model->fetch_object_by_id('feedback_questions', 'id', $Q_id);
        $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $data['RowSet']->company_id);
        $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $data['RowSet']->company_id);
        $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        if ($data['RowSet']->feedback_type_id != "") {
            $data['feedback_subtypeSet'] = $this->common_model->getFeedbackSubTopic($data['RowSet']->feedback_type_id);
        }
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_questions/view', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('a.id', 'a.id', 'c.company_name', 'l.name', 'a.question_title', 'a.question_type', 'a.status');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND a.company_id  = " . $company_id;
                } else {
                    $dtWhere .= " WHERE a.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $feedback_type = ($this->input->get('feedback_type') ? $this->input->get('feedback_type') : '');
        if ($feedback_type != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.feedback_type_id  = " . $feedback_type;
            } else {
                $dtWhere .= " WHERE a.feedback_type_id  = " . $feedback_type;
            }
        }
        $feedback_subtype = ($this->input->get('feedback_subtype') ? $this->input->get('feedback_subtype') : '');
        if ($feedback_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.feedback_subtype_id  = " . $feedback_subtype;
            } else {
                $dtWhere .= " WHERE a.feedback_subtype_id  = " . $feedback_subtype;
            }
        }
        $question_type = $this->input->get('question_type');
        if ($question_type != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.question_type  = " . $question_type;
            } else {
                $dtWhere .= " WHERE a.question_type  = " . $question_type;
            }
        }
        $language_id = $this->input->get('language_id');
        if ($language_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.language_id  = " . $language_id;
            } else {
                $dtWhere .= " WHERE a.language_id  = " . $language_id;
            }
        }
        $DTRenderArray = $this->feedback_questions_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'language_name', 'question_title', 'question_type', 'feedback_type', 'feedback_subtype', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        $company_id = "";
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            if ($company_id != $dtRow['company_id']) {
                $feedback_type = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $dtRow['company_id']);
                $company_id = $dtRow['company_id'];
            }
            if ($dtRow['feedback_type_id'] != "") {
                $SubTypedata = $this->common_model->getFeedbackSubTopic($dtRow['feedback_type_id']);
            } else {
                $SubTypedata = array();
            }
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
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'feedback_questions/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'feedback_questions/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        $action .= '</ul>
                            </div>';
                    } else {
                        $action = '<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                Locked&nbsp;&nbsp;<i class="fa fa-lock"></i>
                            </button>';
                    }
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] == "feedback_type") {
                    $topic_select = '<select name="topic[' . $dtRow['id'] . ']" id="topic' . $dtRow['id'] . '"  class="dropdownselect2 input-sm form-control" onchange="getSubType(' . $dtRow['id'] . ')">';
                    if ($dtRow['feedback_type_id'] == "") {
                        $topic_select .= '<option value="">Select</option>';
                    }
                    foreach ($feedback_type as $tp) {
                        $topic_select .= '<option value="' . $tp->id . '"' . ($tp->id == $dtRow['feedback_type_id'] ? 'Selected' : '') . '>' . $tp->description . '</option>';
                    }
                    $topic_select .= '</select>';
                    $row[] = $topic_select;
                } else if ($dtDisplayColumns[$i] == "feedback_subtype") {
                    $subtopic_select = '<select name="subtopic[' . $dtRow['id'] . ']" id="subtopic' . $dtRow['id'] . '" class="dropdownselect2 input-sm form-control" onchange="LoadUpdateDialog(' . $dtRow['id'] . ')">';
                    if ($dtRow['feedback_subtype_id'] == "") {
                        $subtopic_select .= '<option value="">Select</option>';
                    }
                    if (count((array)$SubTypedata) > 0) {
                        foreach ($SubTypedata as $stp) {
                            $subtopic_select .= '<option value="' . $stp->id . '"' . ($stp->id == $dtRow['feedback_subtype_id'] ? 'Selected' : '') . '>' . $stp->description . '</option>';
                        }
                    }
                    $subtopic_select .= '</select>';
                    $row[] = $subtopic_select;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function submit() {
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {

            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $upload_Path = './assets/uploads/feedback_questions';
            $this->form_validation->set_rules('question_type', 'Question Type', 'required');
            $this->form_validation->set_rules('feedback_type', 'Feedback Type', 'required');
            $this->form_validation->set_rules('question_title', 'Question', 'required');

            $option_a = $this->input->post('option_a');
            $option_b = $this->input->post('option_b');
            $option_c = $this->input->post('option_c');
            $option_d = $this->input->post('option_d');
            $option_e = $this->input->post('option_e');
            $option_f = $this->input->post('option_f');
            $question_type = $this->input->post('question_type');
            if ($question_type == 1) {
                $this->form_validation->set_rules('max_length', 'Max Length', 'required');
            } else {
                $this->form_validation->set_rules('option_a', 'Option A', 'required');
                $this->form_validation->set_rules('weight_a', 'Weightage A', 'required');
                if ($option_b != "") {
                    $this->form_validation->set_rules('weight_b', 'Weightage B', 'required');
                }
                if ($option_c != "") {
                    $this->form_validation->set_rules('weight_c', 'Weightage C', 'required');
                }
                if ($option_d != "") {
                    $this->form_validation->set_rules('weight_d', 'Weightage D', 'required');
                }
                if ($option_e != "") {
                    $this->form_validation->set_rules('weight_e', 'Weightage E', 'required');
                }
                if ($option_f != "") {
                    $this->form_validation->set_rules('weight_f', 'Weightage F', 'required');
                }
                if ($this->input->post('weight_b') != "") {
                    $this->form_validation->set_rules('option_b', 'Option B', 'required');
                }
                if ($this->input->post('weight_c') != "") {
                    $this->form_validation->set_rules('option_c', 'Option C', 'required');
                }
                if ($this->input->post('weight_d') != "") {
                    $this->form_validation->set_rules('option_d', 'Option D', 'required');
                }
                if ($this->input->post('weight_e') != "") {
                    $this->form_validation->set_rules('option_e', 'Option E', 'required');
                }
                if ($this->input->post('weight_f') != "") {
                    $this->form_validation->set_rules('option_b', 'Option F', 'required');
                }
                if ($this->input->post('weight_g') != "") {
                    $this->form_validation->set_rules('option_g', 'Option G', 'required');
                }
            }
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $question = $this->input->post('question_title');
                $QusDuplicateCheck = $this->feedback_questions_model->DuplicateQus($question, $Company_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $Message = "Question Already exists.!";
                    $SuccessFlag = 0;
                }
                if ($option_b == "") {
                    if ($option_c != "" || $option_d != "" || $option_f != "" || $option_e != "") {
                        $Message = "Option B cannot be empty.!";
                        $SuccessFlag = 0;
                    }
                }
                if ($option_c == "") {
                    if ($option_d != "" || $option_f != "" || $option_e != "") {
                        $Message = "Option C cannot be empty.!";
                        $SuccessFlag = 0;
                    }
                }
                if ($option_d == "") {
                    if ($option_f != "" || $option_e != "") {
                        $Message = "Option D cannot be empty.!";
                        $SuccessFlag = 0;
                    }
                }
                if ($option_e == "") {
                    if ($option_f != "") {
                        $Message = "Option E cannot be empty.!";
                        $SuccessFlag = 0;
                    }
                }
                $Hint_image = '';
                if ($SuccessFlag && isset($_FILES['hint_image']['name']) && $_FILES['hint_image']['size'] > 0) {
                    $config = array();
                    $Hint_image = time();
                    $config['upload_path'] = $upload_Path;
                    $config['overwrite'] = FALSE;
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    //$config['max_width'] = 750;
                    //$config['max_height'] = 400;
                    //$config['min_width'] = 750;
                    //$config['min_height'] = 400;
                    $config['file_name'] = $Hint_image;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('hint_image')) {
                        $Message = $this->upload->display_errors();
                        $SuccessFlag = 0;
                    } else {
                        $ImgArrays = explode('.', $_FILES['hint_image']['name']);
                        $Hint_image .="." . $ImgArrays[1];
                    }
                }
                if ($SuccessFlag) {
                    $language_id = $this->input->post('language_id');
                    $now = date('Y-m-d H:i:s');
                    $type_id = $this->input->post('feedback_type');
                    $subType = $this->input->post('feedback_subtype');
                    $data = array(
                        'company_id' => $Company_id,
                        'question_title' => $question,
                        'feedback_type_id' => $type_id,
                        'feedback_subtype_id' => $subType,
                        'question_type' => $question_type,
                        'status' => $this->input->post('status'),
                        'language_id' => $language_id,
                        'tip' => $this->input->post('tip'),
                        'hint_image' => $Hint_image,
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    if ($question_type == 1) {
                        $data['question_timer'] = $this->input->post('question_timer');
                        $data['text_weightage'] = $this->input->post('text_weightage');
                        $data['max_length'] = $this->input->post('max_length');
                        $data['min_length'] = $this->input->post('min_length');
                    } else {
                        $data['option_a'] = $option_a;
                        $data['option_b'] = $option_b;
                        $data['option_c'] = $option_c;
                        $data['option_d'] = $option_d;
                        $data['option_e'] = $option_e;
                        $data['option_f'] = $option_f;
                        $data['weight_a'] = $this->input->post('weight_a');
                        $data['weight_b'] = $this->input->post('weight_b');
                        $data['weight_c'] = $this->input->post('weight_c');
                        $data['weight_d'] = $this->input->post('weight_d');
                        $data['weight_e'] = $this->input->post('weight_e');
                        $data['weight_f'] = $this->input->post('weight_f');
                        $data['multiple_allow'] = $this->input->post('multiple_allow');
                    }
                    $insert_id = $this->common_model->insert('feedback_questions', $data);
                    if ($insert_id != "") {
                        $this->feedback_questions_model->AddnewQusWorkshop($Company_id, $insert_id, $type_id, $subType, $language_id);
                        $Message = "Feedback Question added Successfully.";
                    } else {
                        $Message = "Error while adding record,Contact Mediaworks for techincal support.";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encode_id) {
        $id = base64_decode($Encode_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
        } else {

            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $upload_Path = './assets/uploads/feedback_questions';
            $this->form_validation->set_rules('feedback_type', 'Feedback Type', 'required');
            $this->form_validation->set_rules('question_title', 'Question', 'required');
            $question_type = $this->input->post('question_type');
            if ($question_type == 1) {
                $this->form_validation->set_rules('max_length', 'Max Length', 'required');
            } else {
                $this->form_validation->set_rules('option_a', 'Option A', 'required');
                $this->form_validation->set_rules('weight_a', 'Weightage A', 'required');
                $option_a = $this->input->post('option_a');
                $option_b = $this->input->post('option_b');
                $option_c = $this->input->post('option_c');
                $option_d = $this->input->post('option_d');
                $option_e = $this->input->post('option_e');
                $option_f = $this->input->post('option_f');
                if ($option_b != "") {
                    $this->form_validation->set_rules('weight_b', 'Weightage B', 'required');
                }
                if ($option_c != "") {
                    $this->form_validation->set_rules('weight_c', 'Weightage C', 'required');
                }
                if ($option_d != "") {
                    $this->form_validation->set_rules('weight_d', 'Weightage D', 'required');
                }
                if ($option_e != "") {
                    $this->form_validation->set_rules('weight_e', 'Weightage E', 'required');
                }
                if ($option_f != "") {
                    $this->form_validation->set_rules('weight_f', 'Weightage F', 'required');
                }
                if ($option_f != "") {
                    $this->form_validation->set_rules('weight_f', 'Weightage F', 'required');
                }
                if ($this->input->post('weight_b') != "") {
                    $this->form_validation->set_rules('option_b', 'Option B', 'required');
                }
                if ($this->input->post('weight_c') != "") {
                    $this->form_validation->set_rules('option_c', 'Option C', 'required');
                }
                if ($this->input->post('weight_d') != "") {
                    $this->form_validation->set_rules('option_d', 'Option D', 'required');
                }
                if ($this->input->post('weight_e') != "") {
                    $this->form_validation->set_rules('option_e', 'Option E', 'required');
                }
                if ($this->input->post('weight_f') != "") {
                    $this->form_validation->set_rules('option_b', 'Option F', 'required');
                }
                if ($this->input->post('weight_g') != "") {
                    $this->form_validation->set_rules('option_g', 'Option G', 'required');
                }
            }

            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $OldDataSet = $this->common_model->get_value("feedback_questions", "hint_image,status", "id=" . $id);
                $Hint_image = $OldDataSet->hint_image;
                $question = $this->input->post('question_title');
                $QusDuplicateCheck = $this->feedback_questions_model->DuplicateQus($question, $Company_id, '', '', $id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $Message = "Question Already exists.!";
                    $SuccessFlag = 0;
                }
                if ($question_type == 0) {
                    if ($option_b == "") {
                        if ($option_c != "" || $option_d != "" || $option_f != "" || $option_e != "") {
                            $Message = "Option B cannot be empty.!";
                            $SuccessFlag = 0;
                        }
                    }
                    if ($option_c == "") {
                        if ($option_d != "" || $option_f != "" || $option_e != "") {
                            $Message = "Option C cannot be empty.!";
                            $SuccessFlag = 0;
                        }
                    }
                    if ($option_d == "") {
                        if ($option_f != "" || $option_e != "") {
                            $Message = "Option D cannot be empty.!";
                            $SuccessFlag = 0;
                        }
                    }
                    if ($option_e == "") {
                        if ($option_f != "") {
                            $Message = "Option E cannot be empty.!";
                            $SuccessFlag = 0;
                        }
                    }
                }
                if ($SuccessFlag && isset($_FILES['hint_image']['name']) && $_FILES['hint_image']['size'] > 0) {
                    $config = array();
                    $NewHint_image = time();
                    $config['upload_path'] = $upload_Path;
                    $config['overwrite'] = FALSE;
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    //$config['max_width'] = 750;
                    //$config['max_height'] = 400;
                    //$config['min_width'] = 750;
                    //$config['min_height'] = 400;
                    $config['file_name'] = $NewHint_image;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('hint_image')) {
                        $Message = $this->upload->display_errors();
                        $SuccessFlag = 0;
                    } else {
                        if ($Hint_image != "") {
                            $Path = $upload_Path . '/' . $Hint_image;
                            if (file_exists($Path)) {
                                unlink($Path);
                            }
                        }
                        $ImgArrays = explode('.', $_FILES['hint_image']['name']);
                        $Hint_image = $NewHint_image . "." . $ImgArrays[1];
                    }
                } else {
                    if ($this->input->post('RemoveHintImage') != '' && $Hint_image != "") {
                        $Path = $upload_Path . "/" . $Hint_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                            $Hint_image = '';
                        }
                    }
                }
                //$SuccessFlag = $this->feedback_questions_model->CrosstableValidation($id);
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $Topic_id = $this->input->post('feedback_type');
                    $subTopic_id = $this->input->post('feedback_subtype');
                    $language_id = $this->input->post('language_id');
                    $data = array(
                        'company_id' => $Company_id,
                        'question_title' => $question,
                        'feedback_type_id' => $Topic_id,
                        'feedback_subtype_id' => $subTopic_id,
                        'status' => $this->input->post('status'),
                        'question_type' => $question_type,
                        'language_id' => $language_id,
                        'tip' => $this->input->post('tip'),
                        'hint_image' => $Hint_image,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    if ($question_type == 1) {
                        $data['question_timer'] = $this->input->post('question_timer');
                        $data['text_weightage'] = $this->input->post('text_weightage');
                        $data['max_length'] = $this->input->post('max_length');
                        $data['min_length'] = $this->input->post('min_length');
                        $data['option_a'] = '';
                        $data['option_b'] = '';
                        $data['option_c'] = '';
                        $data['option_d'] = '';
                        $data['option_e'] = '';
                        $data['option_f'] = '';
                        $data['weight_a'] = '';
                        $data['weight_b'] = '';
                        $data['weight_c'] = '';
                        $data['weight_d'] = '';
                        $data['weight_e'] = '';
                        $data['weight_f'] = '';
                        $data['multiple_allow'] = '';
                    } else {
                        $data['question_timer'] = '';
                        $data['text_weightage'] = '';
                        $data['max_length'] = '';
                        $data['min_length'] = '';
                        $data['option_a'] = $option_a;
                        $data['option_b'] = $option_b;
                        $data['option_c'] = $option_c;
                        $data['option_d'] = $option_d;
                        $data['option_e'] = $option_e;
                        $data['option_f'] = $option_f;
                        $data['weight_a'] = $this->input->post('weight_a');
                        $data['weight_b'] = $this->input->post('weight_b');
                        $data['weight_c'] = $this->input->post('weight_c');
                        $data['weight_d'] = $this->input->post('weight_d');
                        $data['weight_e'] = $this->input->post('weight_e');
                        $data['weight_f'] = $this->input->post('weight_f');
                        $data['multiple_allow'] = $this->input->post('multiple_allow');
                    }
                    $this->common_model->update('feedback_questions', 'id', $id, $data);
                    $this->feedback_questions_model->UpdateQusWorkshop($Company_id, $id, $Topic_id, $subTopic_id, $language_id);
                    $Message = "Feedback Question Updated Successfully.";
                }
//					else{
//                 $Message = "Feedback Question can't be update. Feedback Question assigned to Feedback Workshop!.!";
//                 $SuccessFlag = 0;
//                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove($id) {
        $deleted_id = base64_decode($id);
        $alert_type = 'success';
        $message = '';
        $DeleteFlag = 1;
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $DeleteFlag = $this->feedback_questions_model->CrosstableValidation($deleted_id);
            if ($DeleteFlag) {
                $this->common_model->delete('feedback_questions', 'id', $deleted_id);
                $this->feedback_questions_model->DeleteWorkshopQus($deleted_id);
                $message = "Feedback Question deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Question cannot be deleted. Reference of Question found in other module!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
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
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('feedback_questions', 'id', $id, $data);
                $OldDataSet = $this->common_model->get_value("feedback_questions", "company_id,	feedback_type_id,feedback_subtype_id,language_id", "id=" . $id);
                $this->feedback_questions_model->AddnewQusWorkshop($OldDataSet->company_id, $id, $OldDataSet->feedback_type_id, $OldDataSet->feedback_subtype_id, $OldDataSet->language_id);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                //$StatusFlag = $this->questions_model->CrosstableValidation($id);
                $StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('feedback_questions', 'id', $id, $data);
                    $this->feedback_questions_model->DeleteWorkshopQus($id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Feedback(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            $DeleteFlag = 1;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->feedback_questions_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('feedback_questions', 'id', $id);
                    $this->feedback_questions_model->DeleteWorkshopQus($id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Question Set cannot be deleted. Question Set assigned to !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Feedback Question set(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function import() {
        $data['module_id'] = '7.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanySet'] = array();
            $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_questions/import', $data);
    }

    public function samplexls($language_id) {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Feedback_Questions');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()
                ->setCellValue('A2', 'Question*')
                ->setCellValue('B2', 'Option A*')
                ->setCellValue('C2', 'Weightage*')
                ->setCellValue('D2', 'Option B')
                ->setCellValue('E2', 'Weightage')
                ->setCellValue('F2', 'Option C')
                ->setCellValue('G2', 'Weightage')
                ->setCellValue('H2', 'Option D')
                ->setCellValue('I2', 'Weightage')
                ->setCellValue('J2', 'Option E')
                ->setCellValue('K2', 'Weightage')
                ->setCellValue('L2', 'Option F')
                ->setCellValue('M2', 'Weightage')
                ->setCellValue('N2', 'Tip');
        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:N2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('G')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('H')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('I')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('J')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('K')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('L')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('M')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('N')->setWidth("30");
        $Excel->getActiveSheet()->getStyle('A2:N2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        if ($language_id == "" || $language_id == 1) {
            header('Content-type: application/csv');
            header("Pragma: no-cache");
            header('Content-Disposition: attachment;filename="FeedbackQuestion_Import.csv"');
            header('Cache-Control: max-age=0');
            header("Expires: 0");
            $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="FeedbackQuestion_Import.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
            //ob_end_clean();
        }
        $objWriter->save('php://output');
        exit;
    }

    public function samplexls_text($language_id) {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Feedback_Questions');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()
                ->setCellValue('A2', 'Question*')
                ->setCellValue('B2', 'Min Length*')
                ->setCellValue('C2', 'Max Length*')
                ->setCellValue('D2', 'Weightage')
                ->setCellValue('E2', 'Timer')
                ->setCellValue('F2', 'Tip');
        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:F2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("30");
        $Excel->getActiveSheet()->getStyle('A2:F2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        if ($language_id == "" || $language_id == 1) {
            header('Content-type: application/csv');
            header("Pragma: no-cache");
            header('Content-Disposition: attachment;filename="FeedbackQuestion_Import.csv"');
            header('Cache-Control: max-age=0');
            header("Expires: 0");
            $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="FeedbackQuestion_Import.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
            //ob_end_clean();
        }
        $objWriter->save('php://output');
    }

    public function confirm_xls_csv() {
        $Message = '';
        $SuccessFlag = 1;
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $company_id = $this->input->post('company_id');
            } else {
                $company_id = $this->mw_session['company_id'];
            }
            $feedback_type = $this->input->post('feedback_type');
            if ($feedback_type != "") {
                $this->form_validation->set_rules('feedback_subtype', 'Feedback Subtype', 'required');
            }
            $this->form_validation->set_rules('language_id', 'Language', 'required');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $FileData = $_FILES['filename'];
                $fname = $_FILES['filename']['name'];
                $ext = pathinfo($fname, PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $Tdata = $this->UploadCSV($FileData, $company_id);
                } else {
                    $Tdata = $this->UploadXls($FileData, $company_id);
                }
                $SuccessFlag = $Tdata['success'];
                $Message = $Tdata['Msg'];
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function UploadCSV($data, $company_id) {
        $Message = '';
        $SuccessFlag = 1;
        $count = 0;
        $question_type = $this->input->post('question_type');
        $type_id = $this->input->post('feedback_type');
        $subType_id = $this->input->post('feedback_subtype');
        $language_id = $this->input->post('language_id');
        $fp = fopen($data['tmp_name'], 'r') or die("can't open file");
        while (($csv_line = fgetcsv($fp)) !== false) {
            if (count((array)$csv_line) > 10 && $question_type == 1) {
                $Message = "Please select correct Question type.";
                $SuccessFlag = 0;
                break;
            }
            if (count((array)$csv_line) < 10 && $question_type == 0) {
                $Message = "Please select correct Question type.";
                $SuccessFlag = 0;
                break;
            }
            $count++;
            if ($count <= 2) {
                continue;
            }
            $question_title = $csv_line[0];
            if ($question_title == '') {
                $SuccessFlag = 0;
                $Message .= "Row No. $count, Question is Empty. </br> ";
                continue;
            } else {
                $QusDuplicateCheck = $this->feedback_questions_model->DuplicateQus($question_title, $company_id, $type_id, $subType_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $Message .= "Row No. $count,Question Already exists.!<br/>";
                    $SuccessFlag = 0;
                    continue;
                }
            }
            if ($question_type == 0) {
                $option_a = $csv_line[1];
                $weightage_a = $csv_line[2];
                $tip = $csv_line[13];
                if ($option_a == '') {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Option A is Empty. </br> ";
                    continue;
                }
                if ($weightage_a == '') {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Weightage A is Empty. </br> ";
                    continue;
                }
                $Letter = 'A';
                for ($Temp = 3; $Temp <= 11; $Temp+=2) {
                    $tOption = $csv_line[$Temp];
                    $Letter++;
                    $tWeightage = $csv_line[$Temp + 1];
                    if ($tOption == '') {
                        if ($tWeightage != "") {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $count, Option $Letter is Empty. </br> ";
                            continue;
                        }
                    } else {
                        if ($tWeightage == "") {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $count, Weightage $Letter is Empty. </br> ";
                            continue;
                        }
                    }
                }
            } else {
                $min_length = $csv_line[1];
                $max_length = $csv_line[2];
                $text_weightage = $csv_line[3];
                $question_timer = $csv_line[4];
                $tip = $csv_line[5];
                if ($max_length == '') {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Max Length is Empty. </br> ";
                    continue;
                }
            }
        }
        if ($count <= 2) {
            $Message .= "Excel file cannot be empty.";
            $SuccessFlag = 0;
        }
        fclose($fp) or die("can't close file");
        if ($SuccessFlag) {
            $now = date('Y-m-d H:i:s');
            $cnt = 0;
            $fp1 = fopen($data['tmp_name'], 'r') or die("can't open file");
            while (($csv_line = fgetcsv($fp1)) !== false) {
                $cnt++;
                if ($cnt <= 2) {
                    continue;
                }
                $question_title = $csv_line[0];
                $QusDuplicateCheck = $this->feedback_questions_model->DuplicateQus($question_title, $company_id, $type_id, $subType_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $cnt--;
                    continue;
                }
                if ($question_type == 0) {
                    $option_a = $csv_line[1];
                    $weightage_a = $csv_line[2];
                    $option_b = $csv_line[3];
                    $weightage_b = $csv_line[4];
                    $option_c = $csv_line[5];
                    $weightage_c = $csv_line[6];
                    $option_d = $csv_line[7];
                    $weightage_d = $csv_line[8];
                    $option_e = $csv_line[9];
                    $weightage_e = $csv_line[10];
                    $option_f = $csv_line[11];
                    $weightage_f = $csv_line[12];

                    $data = array(
                        'company_id' => $company_id,
                        'question_title' => $question_title,
                        'feedback_type_id' => $type_id,
                        'feedback_subtype_id' => $subType_id,
                        'option_a' => $option_a,
                        'option_b' => $option_b,
                        'option_c' => $option_c,
                        'option_d' => $option_d,
                        'option_e' => $option_e,
                        'option_f' => $option_f,
                        'weight_a' => $weightage_a,
                        'weight_b' => $weightage_b,
                        'weight_c' => $weightage_c,
                        'weight_d' => $weightage_d,
                        'weight_e' => $weightage_e,
                        'weight_f' => $weightage_f,
                        'tip' => $csv_line[13],
                        'question_type' => $question_type,
                        'language_id' => $language_id,
                        'status' => '1',
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                } else {
                    $min_length = $csv_line[1];
                    $max_length = $csv_line[2];
                    $text_weightage = $csv_line[3];
                    $question_timer = $csv_line[4];
                    $tip = $csv_line[5];
                    $data = array(
                        'company_id' => $company_id,
                        'question_title' => $question_title,
                        'feedback_type_id' => $type_id,
                        'feedback_subtype_id' => $subType_id,
                        'min_length' => $min_length,
                        'max_length' => $max_length,
                        'text_weightage' => $text_weightage,
                        'question_timer' => $question_timer,
                        'tip' => $tip,
                        'question_type' => $question_type,
                        'language_id' => $language_id,
                        'status' => '1',
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                }
                $id = $this->common_model->insert('feedback_questions', $data);
                if ($id != "" && $type_id != "") {
                    $this->feedback_questions_model->AddnewQusWorkshop($company_id, $id, $type_id, $subType_id, $language_id);
                }
            }
            $Message = $cnt - 2 . " Questions Imported successfully.";
            fclose($fp1) or die("can't close file");
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        return $Rdata;
    }

    public function UploadXls($FileData, $company_id) {
        $Message = '';
        $SuccessFlag = 1;
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $worksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $highestColumm = $worksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
        if ($highestRow < 2) {
            $Message .= "Excel column mismatch,Please download sample file.";
            $SuccessFlag = 0;
        }if ($highestRow == 2) {
            $Message .= "Excel file cannot be empty.";
            $SuccessFlag = 0;
        }
        if ($highestColumnIndex < 13) {
            $Message .= "Excel column mismatch,Please download sample file.";
            $SuccessFlag = 0;
        }
        if ($SuccessFlag) {
            $feedback_type = $this->input->post('feedback_type');
            $feedback_subtype = $this->input->post('feedback_subtype');
            $language_id = $this->input->post('language_id');
            $question_type = $this->input->post('question_type');
            for ($row = 3; $row <= 10; $row++) {
                $Question = $worksheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
                if ($Question == '') {
                    continue;
                } else {
                    $QusDuplicateCheck = $this->feedback_questions_model->DuplicateQus($Question, $company_id, $feedback_type, $feedback_subtype);
                    if (count((array)$QusDuplicateCheck) > 0) {
                        $Message .= "Row No. $row,Question Already exists.!<br/>";
                        $SuccessFlag = 0;
                        continue;
                    }
                }
                if ($question_type == 0) {
                    $Option_a = $worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
                    if ($Option_a == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Option A is Empty. </br> ";
                        continue;
                    }
                    $Weightage_a = $worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue();
                    if ($Weightage_a == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Weightage A is Empty. </br> ";
                        continue;
                    }
                    $Letter = 'A';
                    for ($Temp = 3; $Temp <= 11; $Temp+=2) {
                        $tOption = $worksheet->getCellByColumnAndRow($Temp, $row)->getFormattedValue();
                        $Letter++;
                        $tWeightage = $worksheet->getCellByColumnAndRow($Temp + 1, $row)->getFormattedValue();
                        if ($tOption == '') {
                            if ($tWeightage != "") {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row, Option $Letter is Empty. </br> ";
                                continue;
                            }
                        } else {
                            if ($tWeightage == "") {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row, Weightage $Letter is Empty. </br> ";
                                continue;
                            }
                        }
                    }
                } else {
                    $max_length = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    if ($max_length == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Max Length is Empty. </br> ";
                        continue;
                    }
                }
            }
        }
        if ($SuccessFlag) {
            $now = date('Y-m-d H:i:s');
            $Counter = 0;
            for ($row = 3; $row <= $highestRow; $row++) {
                $Question = $worksheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
                if ($Question == '') {
                    continue;
                }
                $type_id = $this->input->post('feedback_type');
                $subType_id = $this->input->post('feedback_subtype');
                $QusDuplicateCheck = $this->feedback_questions_model->DuplicateQus($Question, $company_id, $type_id, $subType_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    continue;
                }
                $Counter++;
                if ($question_type == 0) {
                    $data = array(
                        'company_id' => $company_id,
                        'question_title' => $Question,
                        'feedback_type_id' => $type_id,
                        'feedback_subtype_id' => $subType_id,
                        'option_a' => $worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue(),
                        'weight_a' => $worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue(),
                        'option_b' => $worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue(),
                        'weight_b' => $worksheet->getCellByColumnAndRow(4, $row)->getFormattedValue(),
                        'option_c' => $worksheet->getCellByColumnAndRow(5, $row)->getFormattedValue(),
                        'weight_c' => $worksheet->getCellByColumnAndRow(6, $row)->getFormattedValue(),
                        'option_d' => $worksheet->getCellByColumnAndRow(7, $row)->getFormattedValue(),
                        'weight_d' => $worksheet->getCellByColumnAndRow(8, $row)->getFormattedValue(),
                        'option_e' => $worksheet->getCellByColumnAndRow(9, $row)->getFormattedValue(),
                        'weight_e' => $worksheet->getCellByColumnAndRow(10, $row)->getFormattedValue(),
                        'option_f' => $worksheet->getCellByColumnAndRow(11, $row)->getFormattedValue(),
                        'weight_f' => $worksheet->getCellByColumnAndRow(12, $row)->getFormattedValue(),
                        'tip' => $worksheet->getCellByColumnAndRow(13, $row)->getFormattedValue(),
                        'question_type' => $question_type,
                        'language_id' => $language_id,
                        'status' => '1',
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'question_title' => $worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue(),
                        'feedback_type_id' => $type_id,
                        'feedback_subtype_id' => $subType_id,
                        'min_length' => $worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue(),
                        'max_length' => $worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue(),
                        'text_weightage' => $worksheet->getCellByColumnAndRow(4, $row)->getFormattedValue(),
                        'question_timer' => $worksheet->getCellByColumnAndRow(5, $row)->getFormattedValue(),
                        'tip' => $worksheet->getCellByColumnAndRow(6, $row)->getFormattedValue(),
                        'question_type' => $question_type,
                        'language_id' => $language_id,
                        'status' => '1',
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                }
                $id = $this->common_model->insert('feedback_questions', $data);
                if ($id != "" && $type_id != "") {
                    $this->feedback_questions_model->AddnewQusWorkshop($company_id, $id, $type_id, $subType_id, $language_id);
                }
            }
            $Message = $Counter . " Questions Imported successfully.";
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        return $Rdata;
    }

    public function file_check($str) {
        $allowed_mime_type_arr = array('text/csv','application/csv','application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .xls,.csv or .xlsx file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select proper excel to import.');
            return false;
        }
    }

    public function updateTopicSubtopic($id) {
        $Message = '';
        $SuccessFlag = 1;
        $alert_type = 'success';
        if ($SuccessFlag) {
            $Topic_id = $this->input->post('tp_id');
            $subTopic_id = $this->input->post('stp_id');
            $data = array(
                'feedback_type_id' => $Topic_id,
                'feedback_subtype_id' => $subTopic_id
            );
            $this->common_model->update('feedback_questions', 'id', $id, $data);
            $OldDataSet = $this->common_model->get_value("feedback_questions", "company_id,language_id", "id=" . $id);
            $this->feedback_questions_model->UpdateQusWorkshop($OldDataSet->company_id, $id, $Topic_id, $subTopic_id, $OldDataSet->language_id);
            $Message = "Question updated successfully.!";
        }
        echo json_encode(array('message' => $Message, 'alert_type' => $alert_type));
        exit;
    }

    public function ajax_company_feedbackType() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['result'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'status=1 AND company_id=' . $company_id);
        echo json_encode($data);
    }

    public function ajax_feedback_subType() {
        $feedback_type = $this->input->post('feedback_type', TRUE);
        $data['result'] = $this->common_model->getFeedbackSubTopic($feedback_type);
        echo json_encode($data);
    }

    public function export_quest() {
        $question_id = $this->input->post('id', TRUE);
        $dtWhere = "";
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->post('company_id') ? $this->input->post('company_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND a.company_id  = " . $company_id;
                } else {
                    $dtWhere .= " WHERE a.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $feedback_type = ($this->input->post('feedback_type') ? $this->input->post('feedback_type') : '');
        if ($feedback_type != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.feedback_type_id  = " . $feedback_type;
            } else {
                $dtWhere .= " WHERE a.feedback_type_id  = " . $feedback_type;
            }
        }
        $feedback_subtype = ($this->input->post('feedback_subtype') ? $this->input->post('feedback_subtype') : '');
        if ($feedback_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.feedback_subtype_id  = " . $feedback_subtype;
            } else {
                $dtWhere .= " WHERE a.feedback_subtype_id  = " . $feedback_subtype;
            }
        }
        if ($question_id != "") {
            $id_list = implode(',', $question_id);
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.id IN(" . $id_list . ")";
            } else {
                $dtWhere .= " Where a.id IN(" . $id_list . ")";
            }
        }
        $DTQuestSet = $this->feedback_questions_model->ExportQuestions($dtWhere);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', 'ID')
                ->setCellValue('B2', 'Type')
                ->setCellValue('C2', 'Subtype')
                ->setCellValue('D2', 'Question')
                ->setCellValue('E2', 'Option A')
                ->setCellValue('F2', 'Weightage')
                ->setCellValue('G2', 'Option B')
                ->setCellValue('H2', 'Weightage')
                ->setCellValue('I2', 'Option C')
                ->setCellValue('J2', 'Weightage')
                ->setCellValue('K2', 'Option D')
                ->setCellValue('L2', 'Weightage')
                ->setCellValue('M2', 'Option E')
                ->setCellValue('N2', 'Weightage')
                ->setCellValue('O2', 'Option F')
                ->setCellValue('P2', 'Weightage');


        $styleArray = array(
            'font' => array(
                'bold' => true
        ));

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray_header);
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $i = 2;
        foreach ($DTQuestSet as $Question) {
            $i++;
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $Question->id)
                    ->setCellValue("B$i", $Question->feedback_type)
                    ->setCellValue("C$i", $Question->feedback_subtype)
                    ->setCellValue("D$i", $Question->question_title)
                    ->setCellValue("E$i", $Question->option_a)
                    ->setCellValue("F$i", $Question->weight_a)
                    ->setCellValue("G$i", $Question->option_b)
                    ->setCellValue("H$i", $Question->weight_b)
                    ->setCellValue("I$i", $Question->option_c)
                    ->setCellValue("J$i", $Question->weight_c)
                    ->setCellValue("K$i", $Question->option_d)
                    ->setCellValue("L$i", $Question->weight_d)
                    ->setCellValue("M$i", $Question->option_e)
                    ->setCellValue("N$i", $Question->weight_e)
                    ->setCellValue("O$i", $Question->option_f)
                    ->setCellValue("P$i", $Question->weight_f);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="FeedbackQuestionsExports.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

}
