<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Questions extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('questions');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('questions_model');
    }

    public function getQuestiondata() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
            $data['topic'] = $this->common_model->fetch_object_by_field('question_topic', 'status', '1');
        } else {
            $company_id = $this->mw_session['company_id'];
            $data['topic'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'status=1 AND company_id=' . $company_id);
        }
        $this->load->view('questions/questionData', $data);
    }

    public function index() {
        $data['module_id'] = '4.06';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $data['TopicSet'] = array();
        } else {
            $data['cmpdata'] = array();
            $data['TopicSet'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'status=1 AND company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('questions/index', $data);
    }

    public function create() {
        $data['module_id'] = '4.06';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('questions');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id != "") {
            $data['TopicSet'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'company_id=' . $Company_id);
        } else {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $data['Company_id'] = $Company_id;
        $this->load->view('questions/create', $data);
    }

    public function edit($id, $Errors = '') {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('questions');
            return;
        }
        $data['customr_errors'] = $Errors;
        $data['module_id'] = '4.06';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['Company_id'] = $Company_id;

        $data['RowSet'] = $this->common_model->fetch_object_by_id('questions', 'id', $Q_id);
        $company_id = $data['RowSet']->company_id;
        $data['TopicResultSet'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        $data['SubTopicResultSet'] = $this->common_model->getSubTopic($data['RowSet']->topic_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('questions/edit', $data);
    }

    public function view($id) {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('questions');
            return;
        }
        $data['module_id'] = '4.06';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['Company_id'] = $Company_id;
        $data['RowSet'] = $this->common_model->fetch_object_by_id('questions', 'id', $Q_id);
        $company_id = $data['RowSet']->company_id;
        $data['TopicResultSet'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        $data['SubTopicResultSet'] = $this->common_model->getSubTopic($data['RowSet']->topic_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('questions/view', $data);
    }

    public function DatatableRefresh() {

        $dtSearchColumns = array('a.id', 'a.id', 'c.company_name', 'l.name', 'a.question_title', 'qt.description', 'qs.description', 'a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $Start_date = ($this->input->get('start_date') ? $this->input->get('start_date') : '');
        $End_date = ($this->input->get('end_date') ? $this->input->get('end_date') : '');
        if ($Start_date != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.addeddate between '" . date('Y-m-d', strtotime($Start_date)) .
                        "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            } else {
                $dtWhere .= " WHERE a.addeddate between '" . date('Y-m-d', strtotime($Start_date)) .
                        "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            }
        }
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
        $topic_id = ($this->input->get('topic_id') ? $this->input->get('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE a.topic_id  = " . $topic_id;
            }
        }
        $subtopic_id = ($this->input->get('subtopic_id') ? $this->input->get('subtopic_id') : '');
        if ($subtopic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.subtopic_id  = " . $subtopic_id;
            } else {
                $dtWhere .= " WHERE a.subtopic_id  = " . $subtopic_id;
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
        $DTRenderArray = $this->questions_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'language_name', 'question_title', 'correct_value', 'topic', 'sub_topic', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        $company_id = "";
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            if ($company_id != $dtRow['company_id']) {
                $Topicdata = $this->common_model->get_selected_values('question_topic', 'id,description', 'company_id=' . $dtRow['company_id']);
                $company_id = $dtRow['company_id'];
            }
            if ($dtRow['topic_id'] != "") {
                $SubTopicdata = $this->common_model->getSubTopic($dtRow['topic_id']);
            } else {
                $SubTopicdata = array();
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
                                        <a href="' . $site_url . 'questions/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'questions/edit/' . base64_encode($dtRow['id']) . '">
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
                } else if ($dtDisplayColumns[$i] == "topic") {
                    $topic_select = '<select name="topic[' . $dtRow['id'] . ']" id="topic' . $dtRow['id'] . '"  class="dropdownselect2 input-sm form-control" onchange="getSubtopic(' . $dtRow['id'] . ')">';
                    if ($dtRow['topic_id'] == "" || $dtRow['topic_id'] == 0) {
                        $topic_select .= '<option value="">Select</option>';
                    }
                    foreach ($Topicdata as $tp) {
                        $topic_select .= '<option value="' . $tp->id . '"' . ($tp->id == $dtRow['topic_id'] ? 'Selected' : '') . '>' . $tp->description . '</option>';
                    }
                    $topic_select .= '</select>';
                    $row[] = $topic_select;
                } else if ($dtDisplayColumns[$i] == "sub_topic") {
                    $subtopic_select = '<select name="subtopic[' . $dtRow['id'] . ']" id="subtopic' . $dtRow['id'] . '" class="dropdownselect2 input-sm form-control" onchange="LoadUpdateDialog(' . $dtRow['id'] . ')">';
                    if ($dtRow['subtopic_id'] == "") {
                        $subtopic_select .= '<option value="">Select</option>';
                    }
                    if ($dtRow['topic_id'] > 0 && count((array)$SubTopicdata) > 0) {
                        foreach ($SubTopicdata as $stp) {
                            $subtopic_select .= '<option value="' . $stp->id . '"' . ($stp->id == $dtRow['subtopic_id'] ? 'Selected' : '') . '>' . $stp->description . '</option>';
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
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $upload_Path = './assets/uploads/questions';
            $this->form_validation->set_rules('topic_id', 'Topic name', 'required');
            $this->form_validation->set_rules('subtopic_id', 'Sub Topic', 'required');
            $this->form_validation->set_rules('question_title', 'Question', 'required');
            $this->form_validation->set_rules('option_a', 'Option A', 'required');
            $this->form_validation->set_rules('option_b', 'Option B', 'required');
            $this->form_validation->set_rules('correct_answer', 'Correct Answer', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $topic_id = $this->input->post('topic_id');
                $subtopic_id = $this->input->post('subtopic_id');
                $question = $this->input->post('question_title');
                $QusDuplicateCheck = $this->questions_model->DuplicateQus($question, $Company_id, $topic_id, $subtopic_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $Message = "Question Already exists.!";
                    $SuccessFlag = 0;
                } else {
                    $option_c = $this->input->post('option_c');
                    $option_d = $this->input->post('option_d');
                    if ($option_c == "") {
                        if ($option_d != "") {
                            $Message = "Option C cannot be empty.!";
                            $SuccessFlag = 0;
                        }
                    }
                    $Hint_image = '';
                    if ($SuccessFlag && isset($_FILES['hint_image']['name']) && $_FILES['hint_image']['size'] > 0) {
                        $config = array();
                        $Hint_image = time();
                        //echo $upload_Path;
                        $config['upload_path'] = $upload_Path;
                        $config['overwrite'] = FALSE;
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['max_width'] = 750;
                        $config['max_height'] = 400;
                        $config['min_width'] = 750;
                        $config['min_height'] = 400;
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
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $language_id = $this->input->post('language_id');
                    $data = array(
                        'company_id' => $Company_id,
                        'topic_id' => $topic_id,
                        'subtopic_id' => $subtopic_id,
                        'question_title' => $question,
                        'option_a' => $this->input->post('option_a'),
                        'option_b' => $this->input->post('option_b'),
                        'option_c' => $this->input->post('option_c'),
                        'option_d' => $this->input->post('option_d'),
                        'correct_answer' => $this->input->post('correct_answer'),
                        'language_id' => $language_id,
                        'tip' => $this->input->post('tip'),
                        'youtube_link' => $this->input->post('youtube_url'),
                        'status' => $this->input->post('status'),
                        'hint_image' => $Hint_image,
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $insert_id = $this->common_model->insert('questions', $data);
                    if ($insert_id != "" && $topic_id != "") {
                        $this->questions_model->AddnewQusWorkshop($Company_id, $insert_id, $topic_id, $subtopic_id, $language_id);
                        $Message = "Question created successfully.!";
                    }
                }
            }
            $Rdata['success'] = $SuccessFlag;
            $Rdata['Msg'] = $Message;
            echo json_encode($Rdata);
        }
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
            $upload_Path = './assets/uploads/questions';
            $this->form_validation->set_rules('topic_id', 'Topic name', 'required');
            $this->form_validation->set_rules('subtopic_id', 'Sub Topic', 'required');
            $this->form_validation->set_rules('question_title', 'Question', 'required');
            $this->form_validation->set_rules('option_a', 'Option A', 'required');
            $this->form_validation->set_rules('option_b', 'Option B', 'required');
            $this->form_validation->set_rules('correct_answer', 'Correct Answer', 'required');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $OldDataSet = $this->common_model->get_value("questions", "hint_image,status", "id=" . $id);
            $Hint_image = $OldDataSet->hint_image;
            $option_c = $this->input->post('option_c');
            $option_d = $this->input->post('option_d');
            if ($option_c == "") {
                if ($option_d != "") {
                    $Message = "Option C cannot be empty.!";
                    $SuccessFlag = 0;
                }
            }
            if ($SuccessFlag && isset($_FILES['hint_image']['name']) && $_FILES['hint_image']['size'] > 0) {
                $config = array();
                $NewHint_image = time();
                $config['upload_path'] = $upload_Path;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_width'] = 750;
                $config['max_height'] = 400;
                $config['min_width'] = 750;
                $config['min_height'] = 400;
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
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $subTopic_id = $this->input->post('subtopic_id');
                $status = $this->input->post('status');
                $language_id = $this->input->post('language_id');
                $data = array(
                    'company_id' => $Company_id,
                    'topic_id' => $this->input->post('topic_id'),
                    'subtopic_id' => $subTopic_id,
                    'question_title' => $this->input->post('question_title'),
                    'option_a' => $this->input->post('option_a'),
                    'option_b' => $this->input->post('option_b'),
                    'option_c' => $this->input->post('option_c'),
                    'option_d' => $this->input->post('option_d'),
                    'correct_answer' => $this->input->post('correct_answer'),
                    'language_id' => $language_id,
                    'tip' => $this->input->post('tip'),
                    'youtube_link' => $this->input->post('youtube_url'),
                    'status' => $this->input->post('status'),
                    'hint_image' => $Hint_image,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id'],
                );
                $this->common_model->update('questions', 'id', $id, $data);
                $Topic_id = $this->input->post('topic_id');
                if ($status && $OldDataSet->status != $status) {
                    $this->questions_model->AddnewQusWorkshop($Company_id, $id, $Topic_id, $subTopic_id, $language_id);
                } else {
                    $this->questions_model->UpdateQusWorkshop($Company_id, $id, $Topic_id, $subTopic_id, $language_id);
                }
                $Message = "Question updated successfully.!";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function updateTopicSubtopic($id) {
        $Message = '';
        $SuccessFlag = 1;
        $alert_type = 'success';
        if ($SuccessFlag) {
            $Topic_id = $this->input->post('tp_id');
            $subTopic_id = $this->input->post('stp_id');
            $data = array(
                'topic_id' => $Topic_id,
                'subtopic_id' => $subTopic_id);
            $this->common_model->update('questions', 'id', $id, $data);
            $OldDataSet = $this->common_model->get_value("questions", "company_id,language_id", "id=" . $id);
            $this->questions_model->UpdateQusWorkshop($OldDataSet->company_id, $id, $Topic_id, $subTopic_id, $OldDataSet->language_id);
            $Message = "Question updated successfully.!";
        }
        echo json_encode(array('message' => $Message, 'alert_type' => $alert_type));
        exit;
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
            $DeleteFlag = $this->questions_model->CrosstableValidation($deleted_id);
            if ($DeleteFlag) {
                $this->questions_model->DeleteWorkshopQus($deleted_id);
                $this->common_model->delete('questions', 'id', $deleted_id);
                $message = "Question deleted successfully.";
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
                $this->common_model->update('questions', 'id', $id, $data);
                $OldDataSet = $this->common_model->get_value("questions", "company_id,topic_id,subtopic_id,language_id", "id=" . $id);
                $this->questions_model->AddnewQusWorkshop($OldDataSet->company_id, $id, $OldDataSet->topic_id, $OldDataSet->subtopic_id, $OldDataSet->language_id);
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
                    $this->common_model->update('questions', 'id', $id, $data);
                    $this->questions_model->DeleteWorkshopQus($id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Question(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            $DeleteFlag = 1;
            foreach ($action_id as $id) {
                // $DeleteFlag = $this->workshop_model->CheckUserAssignRole($id);
                $DeleteFlag = $this->questions_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('questions', 'id', $id);
                    $this->questions_model->DeleteWorkshopQus($id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Question cannot be delete. Question Set assigned to !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Question set(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function import() {
        $data['module_id'] = '4.06';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('questions');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id != "") {
            $data['TopicSet'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'company_id=' . $Company_id);
        } else {
            $data['CompanySet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('questions/import', $data);
    }

    public function sample_csv() {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        $Excel->getActiveSheet()->setCellValue('A2', 'Question');
        $Excel->getActiveSheet()->setCellValue('B2', 'Option A*');
        $Excel->getActiveSheet()->setCellValue('C2', 'Option B*');
        $Excel->getActiveSheet()->setCellValue('D2', 'Option C');
        $Excel->getActiveSheet()->setCellValue('E2', 'Option D');
        $Excel->getActiveSheet()->setCellValue('F2', 'Correct Option');
        $Excel->getActiveSheet()->setCellValue('G2', 'Hint');

        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename="Question_Import.csv"');
        header('Cache-Control: max-age=0');
        header("Pragma: no-cache");
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        $objWriter->save('php://output');
    }

    public function sample_xls() {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;

        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Questions');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:B1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        $Excel->getActiveSheet()->mergeCells('A1:G1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()->setCellValue('A2', 'Question');
        $Excel->getActiveSheet()->setCellValue('B2', 'Option A*');
        $Excel->getActiveSheet()->setCellValue('C2', 'Option B*');
        $Excel->getActiveSheet()->setCellValue('D2', 'Option C');
        $Excel->getActiveSheet()->setCellValue('E2', 'Option D');
        $Excel->getActiveSheet()->setCellValue('F2', 'Correct Option');
        $Excel->getActiveSheet()->setCellValue('G2', 'Hint');
        $Excel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:G2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("50");
        $Excel->getActiveSheet()->getColumnDimension('G')->setWidth("50");

        $Excel->getActiveSheet()->getStyle('A2:G2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        $filename = "Question_Import.xlsx";
        header('Content-Disposition: attachment;filename="' . $filename . '"');
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
//          $this->form_validation->set_rules('topic_id', 'Topic name', 'required');
            $topic_id = $this->input->post('topic_id');
            if ($topic_id != "") {
                $this->form_validation->set_rules('subtopic_id', 'Sub Topic', 'required');
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
                    $Tdata = $this->UploadCsv($FileData, $company_id);
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

    public function UploadCsv($data, $company_id) {
        $Message = '';
        $SuccessFlag = 1;
        $count = 0;
        $topic_id = $this->input->post('topic_id');
        $subtopic_id = $this->input->post('subtopic_id');
        $fp = fopen($data['tmp_name'], 'r') or die("can't open file");
        while (($csv_line = fgetcsv($fp)) !== false) {
            if (count((array)$csv_line) < 7) {
                $Message = "CSV column mismatch,Please download sample file.";
                $SuccessFlag = 0;
                break;
            } else {
                $count++;
                if ($count <= 2) {
                    continue;
                }
                $company_id = $company_id;
                $topic_id = $topic_id;
                $subtopic_id = $subtopic_id;
                $question_title = $csv_line[0];
                $option_a = $csv_line[1];
                $option_b = $csv_line[2];
                $option_c = $csv_line[3];
                $option_d = $csv_line[4];
                $correct_answer = strtolower($csv_line[5]);
                $tip = $csv_line[6];
                if (empty($option_a)) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Option A is Empty. </br> ";
                    continue;
                }
                if (empty($option_b)) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Option B is Empty. </br> ";
                    continue;
                }
                if (empty($question_title)) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Question Title is Empty. </br> ";
                    continue;
                }
                if (empty($correct_answer)) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Correct Answer is Empty. </br> ";
                    continue;
                }
                if (!in_array($correct_answer, array('a', 'b', 'c', 'd'))) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $count, Correct Option between(A,B,C,D). </br> ";
                    continue;
                }
                switch ($correct_answer) {
                    case "c":
                        if (empty($option_c)) {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $count,You have Selected C as Correct Answer, Option C is Empty. </br> ";
                        }
                        break;
                    case "d":
                        if (empty($option_d)) {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $count,You have Selected D as Correct Answer, Option D is Empty. </br> ";
                        }
                        break;
                }
                $QusDuplicateCheck = $this->questions_model->DuplicateQus($question_title, $company_id, $topic_id, $subtopic_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $Message .= "Row No. $count,Question Already exists.!<br/>";
                    $SuccessFlag = 0;
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
            $cnt = 0;
            $fp1 = fopen($data['tmp_name'], 'r') or die("can't open file");
            $language_id = $this->input->post('language_id');
            while (($csv_line = fgetcsv($fp1)) !== false) {
                $cnt++;
                if ($cnt <= 2) {
                    continue;
                }
                $company_id = $company_id;
                $topic_id = $topic_id;
                $subtopic_id = $subtopic_id;
                $question_title = $csv_line[0];
                $option_a = $csv_line[1];
                $option_b = $csv_line[2];
                $option_c = $csv_line[3];
                $option_d = $csv_line[4];
                $correct_answer = strtolower($csv_line[5]);
                $tip = $csv_line[6];
                $QusDuplicateCheck = $this->questions_model->DuplicateQus($question_title, $company_id, $topic_id, $subtopic_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $cnt--;
                    continue;
                }
                $data = array(
                    'company_id' => $company_id,
                    'topic_id' => $topic_id,
                    'subtopic_id' => $subtopic_id,
                    'question_title' => utf8_encode($question_title),
                    'option_a' => $option_a,
                    'option_b' => $option_b,
                    'option_c' => $option_c,
                    'option_d' => $option_d,
                    'language_id' => $language_id,
                    'correct_answer' => $correct_answer,
                    'tip' => $tip,
                    'status' => 1,
                    'addeddate' => date('Y-m-d H:i:s'),
                    'addedby' => $this->mw_session['user_id'],
                );
                $insert_id = $this->db->insert('questions', $data);
                if ($insert_id != "" && $topic_id != "") {
                    $this->questions_model->AddnewQusWorkshop($company_id, $insert_id, $topic_id, $subtopic_id, $language_id);
                }
            }
            $Message = $cnt - 2 . " Questions Imported successfully.";
            fclose($fp1) or die("can't close file");
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        return $Rdata;
    }

    public function UploadXls($data, $company_id) {
        $Message = '';
        $SuccessFlag = 1;
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = PHPExcel_IOFactory::load($data['tmp_name']);
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
        if ($highestColumnIndex < 7) {
            $Message .= "Excel column mismatch,Please download sample file.";
            $SuccessFlag = 0;
        }
        $topic_id = $this->input->post('topic_id');
        $subtopic_id = $this->input->post('subtopic_id');
        if ($SuccessFlag) {
            for ($row = 3; $row <= $highestRow; $row++) {
                $Question = $worksheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
                if ($Question == '') {
                    continue;
                }
                //$Option_a = $worksheet->getCellByColumnAndRow(1, $row)->getDataType();
                $Option_a = trim($worksheet->getCellByColumnAndRow(1, $row)->getDataType());
                $Option_a_val = trim($worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue());
                if ($Option_a == 'null' || ($Option_a != 'b' && $Option_a_val == "" )) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $row, Option A is Empty. </br> ";
                    continue;
                }
                $Option_b = $worksheet->getCellByColumnAndRow(2, $row)->getDataType();
                $Option_b_val = trim($worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue());
                if ($Option_b == 'null' || ($Option_b != 'b' && $Option_b_val == "" )) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $row, Option B is Empty. </br> ";
                    continue;
                }
                $Correct_Option = trim(strtolower($worksheet->getCellByColumnAndRow(5, $row)->getValue()));
                $Option_c = $worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue();
                $Option_d = $worksheet->getCellByColumnAndRow(4, $row)->getFormattedValue();
                if ($Correct_Option == '') {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $row, Correct Option is Empty. </br> ";
                    continue;
                }
                if (!in_array($Correct_Option, array('a', 'b', 'c', 'd'))) {
                    $SuccessFlag = 0;
                    $Message .= "Row No. $row, Correct Option between(A,B,C,D). </br> ";
                    continue;
                }
                switch ($Correct_Option) {
                    case "c":
                        if ($Option_c == "") {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row,You have Selected C as Correct Answer, Option C is Empty. </br> ";
                        }
                        break;
                    case "d":
                        if ($Option_d == "") {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row,You have Selected D as Correct Answer, Option D is Empty. </br> ";
                        }
                        break;
                }
                $QusDuplicateCheck = $this->questions_model->DuplicateQus($Question, $company_id, $topic_id, $subtopic_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    $Message .= "Row No. $row,Question Already exists.!<br/>";
                    $SuccessFlag = 0;
                    continue;
                }
            }
        }
        if ($SuccessFlag) {
            $now = date('Y-m-d H:i:s');
            $Counter = 0;
            $language_id = $this->input->post('language_id');
            for ($row = 3; $row <= $highestRow; $row++) {
                $Question = $worksheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
                if ($Question == '') {
                    continue;
                }
                $Option_a = $worksheet->getCellByColumnAndRow(1, $row)->getDataType();
                $Option_a_val = trim($worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue());
                if ($Option_a == 'b') {
                    if ($Option_a_val) {
                        $Option_a_val = 'true';
                    } else {
                        $Option_a_val = 'false';
                    }
                }
                $Option_b = $worksheet->getCellByColumnAndRow(2, $row)->getDataType();
                $Option_b_val = trim($worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue());
                if ($Option_b == 'b') {
                    if ($Option_b_val) {
                        $Option_b_val = 'true';
                    } else {
                        $Option_b_val = 'false';
                    }
                }
                $QusDuplicateCheck = $this->questions_model->DuplicateQus($Question, $company_id, $topic_id, $subtopic_id);
                if (count((array)$QusDuplicateCheck) > 0) {
                    continue;
                }
                $Counter++;
                $data = array(
                    'company_id' => $company_id,
                    'topic_id' => $topic_id,
                    'subtopic_id' => $subtopic_id,
                    'question_title' => $Question,
                    'language_id' => $language_id,
                    'option_a' => ($Option_a_val),
                    'option_b' => ($Option_b_val),
                    'option_c' => ($worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue()),
                    'option_d' => ($worksheet->getCellByColumnAndRow(4, $row)->getFormattedValue()),
                    'correct_answer' => trim(strtolower($worksheet->getCellByColumnAndRow(5, $row)->getValue())),
                    'tip' => $worksheet->getCellByColumnAndRow(6, $row)->getFormattedValue(),
                    'status' => 1,
                    'addeddate' => $now,
                    'addedby' => $this->mw_session['user_id'],
                );
                $insert_id = $this->common_model->insert('questions', $data);
                if ($insert_id != "" && $topic_id != "") {
                    $this->questions_model->AddnewQusWorkshop($company_id, $insert_id, $topic_id, $subtopic_id, $language_id);
                }
            }
            $Message = $Counter . " Questions Imported successfully.";
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        return $Rdata;
    }

    public function file_check($str) {
        $allowed_mime_type_arr = array('text/csv','application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream');
        $mime = $_FILES['filename']['type'];
        //$file_ext = explode('.',$_FILES['filename']['name']);
//            if($file_ext[1] == 'csv' || $file_ext[1] == 'CSV'){
//                return true;
//            }
//            else { 
//              $this->form_validation->set_message('file_check', 'Please select only .csv file.');
//                return false;
//            }
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

    public function Check_questionset() {
        $question = $this->input->post('questionset', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $questionset_id = $this->input->post('feedback_id', true);
        echo $this->questions_model->check_Questionset($question, $cmp_id, $questionset_id);
    }

    public function ajax_company_topic() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['result'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        $data['trainerResult'] = $this->common_model->fetch_object_by_field('company_users', 'company_id', $company_id);
        echo json_encode($data);
    }

    public function ajax_topic_subtopic($id = '') {
        $topic_id = $this->input->post('data', TRUE);
        $data['result'] = $this->common_model->getSubTopic($topic_id);
        $data['SubTopicSelected'] = $this->common_model->get_selected_values('questions', 'subtopic_id', 'id=' . $id);
        echo json_encode($data);
    }

    public function export_quest() {
        $Start_date = ($this->input->post('start_date') ? $this->input->post('start_date') : '');
        $End_date = ($this->input->post('end_date') ? $this->input->post('end_date') : '');
        $dtWhere = "";
        if ($Start_date != "") {
            $dtWhere .= " WHERE a.addeddate between '" . date('Y-m-d', strtotime($Start_date)) .
                    "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
        }
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
        $topic_id = ($this->input->post('topic_id') ? $this->input->post('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE a.topic_id  = " . $topic_id;
            }
        }
        $subtopic_id = ($this->input->post('subtopic_id') ? $this->input->post('subtopic_id') : '');
        if ($subtopic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.subtopic_id  = " . $subtopic_id;
            } else {
                $dtWhere .= " WHERE a.subtopic_id  = " . $subtopic_id;
            }
        }
        $question_id = $this->input->post('id', TRUE);
        if ($question_id != "") {
            $id_list = implode(',', $question_id);
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.id IN(" . $id_list . ")";
            } else {
                $dtWhere .= " Where a.id IN(" . $id_list . ")";
            }
        }
        $DTQuestSet = $this->questions_model->ExportQuestions($dtWhere);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', 'ID')
                ->setCellValue('B2', 'Topic')
                ->setCellValue('C2', 'Subtopic')
                ->setCellValue('D2', 'Question')
                ->setCellValue('E2', 'Option A')
                ->setCellValue('F2', 'Option B')
                ->setCellValue('G2', 'Option C')
                ->setCellValue('H2', 'Option D')
                ->setCellValue('I2', 'Correct Option')
                ->setCellValue('J2', 'Hint');


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
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray_header);
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
                    ->setCellValue("B$i", $Question->topic)
                    ->setCellValue("C$i", $Question->sub_topic)
                    ->setCellValue("D$i", $Question->question_title)
                    ->setCellValue("E$i", $Question->option_a)
                    ->setCellValue("F$i", $Question->option_b)
                    ->setCellValue("G$i", $Question->option_c)
                    ->setCellValue("H$i", $Question->option_d)
                    ->setCellValue("I$i", strtoupper($Question->correct_answer))
                    ->setCellValue("J$i", $Question->tip);

            $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="QuestionsExports.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

}
