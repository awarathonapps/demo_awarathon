<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Questionset extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('questionset');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('questionset_model');
    }

    public function update_workshop_trainer() {
        $lcSqlstr = "SELECT workshop_id,questionset_id FROM workshop_questionset_pre
                UNION ALL SELECT workshop_id,questionset_id FROM workshop_questionset_post";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $key => $value) {
                $Workshop_Id = $value->workshop_id;
                $workshop_setData = $this->common_model->get_value('workshop', 'id', 'id=' . $Workshop_Id);
                if (count((array)$workshop_setData) == 0) {
                    continue;
                }
                $questionset_id = $value->questionset_id;
                $Already_setData = $this->common_model->get_value('workshop_questionset_trainer', 'id', 'workshop_id=' . $Workshop_Id . ' AND questionset_id=' . $questionset_id);
                if (count((array)$Already_setData) == 0) {
                    $lcSqlstr = "INSERT INTO workshop_questionset_trainer (workshop_id,questionset_id,questions_trans_id,topic_id,subtopic_id,trainer_id)"
                            . "SELECT $Workshop_Id as workshop_id,$questionset_id as questionset_id,id,topic_id,subtopic_id,trainer_id FROM questionset_trainer"
                            . " where questionset_id= $questionset_id ";
                    $this->db->query($lcSqlstr);
                }
            }
        }
    }

    public function ajax_feedback_company() {
        return $this->common_model->fetch_company_data($this->input->get());
    }

    public function getQuestiondata() {
        $data['topic'] = $this->common_model->fetch_object_by_field('question_topic', 'status', '1');
        $this->load->view('question_set/questionData', $data);
    }

    public function index() {
        $data['module_id'] = '4.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->questionset_model->fetch_access_data();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmpdata'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('question_set/index', $data);
    }

    public function create() {
        $data['module_id'] = '4.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('questionset');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmpdata'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('question_set/create', $data);
    }

    public function edit($id, $step = 1, $Errors = '') {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('questionset');
            return;
        }
        $data['customr_errors'] = $Errors;
        $data['module_id'] = '4.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $company_id = $this->mw_session['company_id'];
        $data['Company_id'] = $company_id;
        $data['result'] = $this->common_model->fetch_object_by_id('question_set', 'id', $Q_id);
        if ($company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $company_id = $data['result']->company_id;
        } else {
            $data['SelectCompany'] = array();
            if ($data['result']->company_id != $company_id) {
                redirect('questionset');
                return;
            }
        }
        $data['TopicResultSet'] = $this->common_model->get_selected_values('question_topic', 'id as topic_id,description', 'status=1 AND company_id=' . $company_id);
        $data['SubTopicResultSet'] = $this->common_model->fetch_object_by_field('question_subtopic', 'company_id', $company_id);
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as username', 'company_id=' . $company_id);
        $trainer_id_set = $this->questionset_model->fetch_questionset_trainer($Q_id);

        $data['step'] = $step;
        $TrainerArray = array();
        $subTopicList = '';
        foreach ($trainer_id_set as $key => $tr_id) {
            $topic_id = $tr_id->topic_id;
            $trainer_id = $tr_id->trainer_id;
            $NextTopic = (isset($trainer_id_set[$key + 1]) ? $trainer_id_set[$key + 1]->topic_id : '' );
            $NextTrainer = (isset($trainer_id_set[$key + 1]) ? $trainer_id_set[$key + 1]->trainer_id : '' );
            $subTopicList .= $tr_id->subtopic_id . ',';
            if ($NextTopic != $topic_id || $NextTrainer != $trainer_id) {
                $SubTopic_set = $this->questionset_model->getEditSubtopic($Q_id, $topic_id, $trainer_id);
                $TrainerArray[] = array('id' => $tr_id->id,
                    'questionset_id' => $tr_id->questionset_id,
                    'topic_id' => $topic_id,
                    'trainer_id' => $trainer_id,
                    'subtopicSet' => $SubTopic_set);
            }
        }
        $data['TrainerArray'] = $TrainerArray;
        $data['TrainerArrayCount'] = count((array)$trainer_id_set);
        $data['step'] = $step;
        $data['SelectedTopic'] = $this->questionset_model->getQuestionTopic($Q_id);
        $data['SelectedTrainer'] = $this->questionset_model->getQuestionTrainer($Q_id);
        $UsedWorkshopList = $this->questionset_model->CheckQuestionset_ismap($Q_id);
        $LockFlag = false;
        if (count((array)$UsedWorkshopList) > 0) {
            $LockFlag = true;
        }
        $data['LockFlag'] = $LockFlag;
        $data['basic_lock'] = $this->questionset_model->Questionsetis_Played($Q_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('question_set/edit', $data);
    }

    public function copy($id, $step = 1, $Errors = '') {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('questionset');
            return;
        } else {
            $data['company'] = $this->questionset_model->find_by_id($Q_id);
            //echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['company']));
        }
        $data['customr_errors'] = $Errors;

        $data['module_id'] = '4.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['SelectCompany'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['trainer'] = $this->questionset_model->get_trainer();
        $data['result'] = $this->common_model->fetch_object_by_id('question_set', 'id', $Q_id);
        $company_id = $data['result']->company_id;
        $data['TopicResultSet'] = $this->questionset_model->fetch_company_topic($company_id);
        $data['TopicResultSet'] = $this->common_model->get_selected_values('question_topic', 'id as topic_id,description', 'status=1 AND company_id=' . $company_id);
        //$data['TopicResultSet'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        $data['SubTopicResultSet'] = $this->common_model->fetch_object_by_field('question_subtopic', 'company_id', $company_id);
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as username', 'company_id=' . $company_id);
        $trainer_id_set = $this->questionset_model->fetch_questionset_trainer($Q_id);
        $data['step'] = $step;
        $TrainerArray = array();
        $subTopicList = '';
        foreach ($trainer_id_set as $key => $tr_id) {
            $topic_id = $tr_id->topic_id;
            $trainer_id = $tr_id->trainer_id;
            $NextTopic = (isset($trainer_id_set[$key + 1]) ? $trainer_id_set[$key + 1]->topic_id : '' );
            $NextTrainer = (isset($trainer_id_set[$key + 1]) ? $trainer_id_set[$key + 1]->trainer_id : '' );
            $subTopicList .= $tr_id->subtopic_id . ',';
            if ($NextTopic != $topic_id || $NextTrainer != $trainer_id) {
                $SubTopic_set = $this->questionset_model->getEditSubtopic($Q_id, $topic_id, $trainer_id);
                $TrainerArray[] = array('id' => $tr_id->id,
                    'questionset_id' => $tr_id->questionset_id,
                    'topic_id' => $topic_id,
                    'trainer_id' => $trainer_id,
                    'subtopicSet' => $SubTopic_set);
            }
        }
        $data['TrainerArray'] = $TrainerArray;
        $data['TrainerArrayCount'] = count((array)$trainer_id_set);
        $data['step'] = $step;
        $data['SelectedTopic'] = $this->questionset_model->getQuestionTopic($Q_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('question_set/copy', $data);
    }

    public function view($id, $step = 1) {
        $Q_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('questionset');
            return;
        } else {
            $data['company'] = $this->questionset_model->find_by_id($Q_id);
            //echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['company']));
        }

        $data['module_id'] = '4.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['SelectCompany'] = array();
        }
        $data['Company_id'] = $Company_id;
        //$data['Topic'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'status=1');
        //$data['trainer'] = $this->questionset_model->get_trainer();
        $data['result'] = $this->common_model->fetch_object_by_id('question_set', 'id', $Q_id);
        $company_id = $data['result']->company_id;
        $data['TopicResultSet'] = $this->common_model->get_selected_values('question_topic', 'id as topic_id,description', 'status=1 AND company_id=' . $company_id);
        //$data['TopicResultSet'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        $data['SubTopicResultSet'] = $this->common_model->fetch_object_by_field('question_subtopic', 'company_id', $company_id);
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as username', 'company_id=' . $company_id);
        $trainer_id_set = $this->questionset_model->fetch_questionset_trainer($Q_id);
        $data['step'] = $step;
        $TrainerArray = array();
        $subTopicList = '';
        foreach ($trainer_id_set as $key => $tr_id) {
            $topic_id = $tr_id->topic_id;
            $trainer_id = $tr_id->trainer_id;
            $NextTopic = (isset($trainer_id_set[$key + 1]) ? $trainer_id_set[$key + 1]->topic_id : '' );
            $NextTrainer = (isset($trainer_id_set[$key + 1]) ? $trainer_id_set[$key + 1]->trainer_id : '' );
            $subTopicList .= $tr_id->subtopic_id . ',';
            if ($NextTopic != $topic_id || $NextTrainer != $trainer_id) {
                $SubTopic_set = $this->questionset_model->getEditSubtopic($Q_id, $topic_id, $trainer_id);
                $TrainerArray[] = array('id' => $tr_id->id,
                    'questionset_id' => $tr_id->questionset_id,
                    'topic_id' => $topic_id,
                    'trainer_id' => $trainer_id,
                    'subtopicSet' => $SubTopic_set);
            }
        }
        $data['TrainerArray'] = $TrainerArray;
        $data['TrainerArrayCount'] = count((array)$trainer_id_set);
        $data['step'] = $step;
        $data['SelectedTopic'] = $this->questionset_model->getQuestionTopic($Q_id);
        $data['SelectedTrainer'] = $this->questionset_model->getQuestionTrainer($Q_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('question_set/view', $data);
    }

    public function DatatableRefresh() {

        $dtSearchColumns = array('a.id', 'a.id', 'c.company_name', 'l.name', 'a.title', 'a.powered_by', 'a.timer', 'a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $company_id;
            }
        }
        $status = $this->input->get('status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.status  = " . $status;
            } else {
                $dtWhere .= " WHERE a.status  = " . $status;
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
        $DTRenderArray = $this->questionset_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'language_name', 'title', 'powered_by', 'timer', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $title = $dtRow['title'];
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
                    if ($acces_management->allow_add OR $acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'questionset/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'questionset/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_add) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'questionset/copy/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-copy"></i>&nbsp;Copy
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
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function submit($Copy_id = "") {
        if ($Copy_id != "") {
            $Copy_id = base64_decode($Copy_id);
        }
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $New_topic_idArray = $this->input->post('New_topic_id');
            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if (count((array)$New_topic_idArray) > 0) {
                $this->form_validation->set_rules('New_topic_id[]', 'New Topic', 'required');
                $this->form_validation->set_rules('New_trainer_id[]', 'New Trainer', 'required');
                //$this->form_validation->set_rules('New_subtopic_id[]', 'Sub Topic', 'required');
            }
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('feedback_name', 'Feedback name', 'required');
            $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
            $this->form_validation->set_rules('language_id', 'language', 'trim|required');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $NewTopic_Array = $this->input->post('New_topic_id');
                //$New_trainer_id = $this->input->post('New_trainer_id');
                if (count((array)$NewTopic_Array) > 0) {
                    $ArraySubTopic = array();
                    foreach ($NewTopic_Array as $key => $topic_id) {
                        $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                        $SubTopicArray = $this->input->post('New_subtopic_id' . $TotalSubTopic);
                        if (is_array($SubTopicArray)) {
                            $ArraySubTopic = array_merge($ArraySubTopic, $SubTopicArray);
                        }
                    }
                    $ArrayCountArray = array_count_values($ArraySubTopic);
                    foreach ($ArrayCountArray as $key => $value) {
                        if ($key != 0 && $value > 1) {
                            $SuccessFlag = 0;
                            $TopicData = $this->common_model->get_value("question_subtopic", "description", "id=" . $key);
                            $Message .= "Same  '" . $TopicData->description . "' sub-topic are selected.!<br/>";
                        }
                    }
                } else {
                    $Message = "Please add trainer first.!";
                    $SuccessFlag = 0;
                }
                if ($SuccessFlag) {
                    foreach ($New_topic_idArray as $key => $topic_id) {
                        $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                        $SubTopicArray = $this->input->post('New_subtopic_id' . $TotalSubTopic);
                        if (count((array)$SubTopicArray) == 0) {
                            $SubTopicData = $this->common_model->get_value("question_subtopic", "count(id) as counter", "topic_id=" . $topic_id);
                            if ($SubTopicData->counter > 0) {
                                $TopicData = $this->common_model->get_value("question_topic", "description", "id=" . $topic_id);
                                $Message .= "Please select sub-Topic of '" . $TopicData->description . "' Topic.<br/>";
                                $SuccessFlag = 0;
                            }
                        }
                        $QuestionSet = $this->common_model->get_value("questions", "count(id) as counter", "topic_id=" . $topic_id);
                        if ($QuestionSet->counter == 0) {
                            $TopicData = $this->common_model->get_value("question_topic", "description", "id=" . $topic_id);
                            $Message .= "Selected topic '" . $TopicData->description . "' has no any questions are mapped.<br/>";
                            $SuccessFlag = 0;
                        }
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'title' => $this->input->post('feedback_name'),
                        //'short_description' => $this->input->post('short_description'),ucfirst(strtolower(
                        'powered_by' => $this->input->post('powered_by'),
                        'language_id' => $this->input->post('language_id'),
                        //'trigger_after' => $this->input->post('no_of_question'), 
                        'timer' => $this->input->post('timer'),
                        'weight' => $this->input->post('weight'),
                        'reward' => $this->input->post('reward'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $insert_id = $this->common_model->insert('question_set', $data);
                    if ($insert_id != "") {
                        foreach ($New_topic_idArray as $key => $topic_id) {
                            //$Topic_id = $TopicData->topic_id;
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtopic_id' . $TotalSubTopic);
                            foreach ($SubTopicArray as $subtopic_id) {
                                $subtopicdata = array(
                                    'questionset_id' => $insert_id,
                                    'trainer_id' => $this->input->post('New_trainer_id')[$key],
                                    'topic_id' => $topic_id,
                                    'subtopic_id' => $subtopic_id
                                );
                                $this->common_model->insert('questionset_trainer', $subtopicdata);
                            }
                        }
                        if ($Copy_id != "") {
                            $this->questionset_model->CopyInactiveQuestions($insert_id, $Copy_id);
                        }
                        $Message = "Questionset created Successfully.";
                        $Rdata['id'] = base64_encode($insert_id);
                    } else {
                        $Message = "Error while creating Questionset,Contact administrator for technical support.!";
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

        $UsedWorkshopList = $this->questionset_model->CheckQuestionset_ismap($id);
        $LockFlag = false;
        if (count((array)$UsedWorkshopList) > 0) {
            $LockFlag = true;
        }
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $New_topic_idArray = $this->input->post('New_topic_id');
            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if (!$LockFlag) {
                $this->form_validation->set_rules('New_topic_id[]', 'New Topic', 'required');
                $this->form_validation->set_rules('New_trainer_id[]', 'New Trainer', 'required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('language_id', 'language', 'trim|required');
            }
            $basic_lock = $this->questionset_model->Questionsetis_Played($id);
            $this->form_validation->set_rules('feedback_name', 'Feedback name', 'required');
            $this->form_validation->set_rules('powered_by', 'Powered By', 'required');

            //$this->form_validation->set_rules('no_of_question', 'No of Questions', 'trim|required|max_length[50]');        
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                if (!$LockFlag) {
                    if (count((array)$New_topic_idArray) > 0) {
                        $ArraySubTopic = array();
                        foreach ($New_topic_idArray as $key => $topic_id) {
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtopic_id' . $TotalSubTopic);
                            if (is_array($SubTopicArray)) {
                                $ArraySubTopic = array_merge($ArraySubTopic, $SubTopicArray);
                            }
                        }
                        $ArrayCountArray = array_count_values($ArraySubTopic);
                        foreach ($ArrayCountArray as $key => $value) {
                            if ($key != 0 && $value > 1) {
                                $SuccessFlag = 0;
                                $TopicData = $this->common_model->get_value("question_subtopic", "description", "id=" . $key);
                                $Message .= "Same '" . $TopicData->description . "' sub-topic are selected.!<br/>";
                            }
                        }
                        //Update Check
                        foreach ($New_topic_idArray as $key => $topic_id) {
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtopic_id' . $TotalSubTopic);
                            if (count((array)$SubTopicArray) == 0) {
                                $SubTopicData = $this->common_model->get_value("question_subtopic", "count(id) as counter", "topic_id=" . $topic_id);
                                if ($SubTopicData->counter > 0) {
                                    $TopicData = $this->common_model->get_value("question_topic", "description", "id=" . $topic_id);
                                    $Message .= "Please select sub-Topic of '" . $TopicData->description . "' Topic.<br/>";
                                    $SuccessFlag = 0;
                                }
                            }
                            $QuestionSet = $this->common_model->get_value("questions", "count(id) as counter", "topic_id=" . $topic_id);
                            if ($QuestionSet->counter == 0) {
                                $TopicData = $this->common_model->get_value("question_topic", "description", "id=" . $topic_id);
                                $Message .= "Selectd topic '" . $TopicData->description . "' has no any questions are mapped.<br/>";
                                $SuccessFlag = 0;
                            }
                        }
                    } else {
                        $Message = "Please add trainer first.!";
                        $SuccessFlag = 0;
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'title' => $this->input->post('feedback_name'),
                        'powered_by' => $this->input->post('powered_by'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    if (!$basic_lock) {
                        $data['timer'] = $this->input->post('timer');
                        $data['weight'] = $this->input->post('weight');
                        $data['reward'] = $this->input->post('reward');
                    }
                    if (!$LockFlag) {
                        $data['status'] = $this->input->post('status');
                        $data['language_id'] = $this->input->post('language_id');
                    }
                    $this->common_model->update('question_set', 'id', $id, $data);
                    $Message = "Questionset updated Successfully.";
                    if (!$LockFlag) {
                        if ($id != "") {
                            $this->common_model->delete('questionset_trainer', 'questionset_id', $id);
                            $this->common_model->delete('workshop_questions', 'questionset_id', $id);
                            foreach ($New_topic_idArray as $key => $topic_id) {
                                $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                                $SubTopicArray = $this->input->post('New_subtopic_id' . $TotalSubTopic);
                                $trainner_id = $this->input->post('New_trainer_id')[$key];
                                foreach ($SubTopicArray as $subtopic_id) {
                                    $subtopicdata = array(
                                        'questionset_id' => $id,
                                        'trainer_id' => $trainner_id,
                                        'topic_id' => $topic_id,
                                        'subtopic_id' => $subtopic_id);
                                    $this->common_model->insert('questionset_trainer', $subtopicdata);
                                    //$this->questionset_model->copyQusWorkshop($id, $subtopicdata);
                                }
                            }
                        } else {
                            $Message = "Error while updating Questionset,Contact administrator for technical support.!";
                            $SuccessFlag = 0;
                        }
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove() {
        $deleted_id = base64_decode($this->input->Post('deleteid'));
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $DeleteFlag = $this->questionset_model->CrosstableValidation($deleted_id);
            if ($DeleteFlag) {
                $this->common_model->delete('question_set', 'id', $deleted_id);
                $this->common_model->delete('questionset_trainer', 'questionset_id', $deleted_id);
                $this->common_model->delete('workshop_questions', 'questionset_id', $deleted_id);
                $message = "Question set deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Question set cannot be deleted. Reference of Question set found in other module!<br/>";
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
                $this->common_model->update('question_set', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->questionset_model->CrosstableValidation($id);
                //$StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('question_set', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Questionset(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->questionset_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('question_set', 'id', $id);
                    $this->common_model->delete('questionset_trainer', 'questionset_id', $id);
                    $this->common_model->delete('question_inactive', 'questionset_id', $id);
                    $this->common_model->delete('workshop_questions', 'questionset_id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Question Set cannot be deleted. Question Set assigned to !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Question set(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function StatusUpdate($Encode_id) {
        $SuccessFlag = 1;
        $Qset_id = base64_decode($Encode_id);
        $Qstatus = $this->input->post('Qstatus');
        $Qid = $this->input->Post('Q_id');
        $now = date('Y-m-d H:i:s');
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $message = 'You have no rights to Edit,Contact Administrator for details.';
            $SuccessFlag = 0;
        } else {
            if ($Qstatus == 'true') {
                $data = array(
                    'questionset_id' => $Qset_id,
                    'question_id' => $Qid,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->insert('question_inactive', $data);
                $message = "Question In-Active successfully.";
                $this->questionset_model->DeleteInactiveQusWorkshop($Qset_id, $Qid);
            } else {
                $this->common_model->delete_whereclause('question_inactive', 'question_id=' . $Qid . ' and questionset_id=' . $Qset_id);
                $this->questionset_model->ActiveQusWorkshop($Qset_id, $Qid);
                $message = "Question Active successfully.";
            }
        }
        $Rdata['Msg'] = $message;
        $Rdata['success'] = $SuccessFlag;
        echo json_encode($Rdata);
    }

    public function QuestionTable_actions($Qset_id, $Action) {
        $action_id = $this->input->Post('id');
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        $title = '';
        $Qset_id = base64_decode($Qset_id);
        if ($Action == 2) {
            foreach ($action_id as $Qid) {
                $data = array(
                    'questionset_id' => $Qset_id,
                    'question_id' => $Qid,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->insert('question_inactive', $data);
                $this->questionset_model->DeleteInactiveQusWorkshop($Qset_id, $Qid);
            }
            $message = 'Status changed to In-Active successfully.';
        } else if ($Action == 1) {
            $SuccessFlag = false;
            foreach ($action_id as $Qid) {
                $StatusFlag = true;
                if ($StatusFlag) {
                    $this->common_model->delete_whereclause('question_inactive', 'question_id=' . $Qid . ' and questionset_id=' . $Qset_id);
                    $this->questionset_model->ActiveQusWorkshop($Qset_id, $Qid);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Questionset(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to Active sucessfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_questionset() {
        $question = $this->input->post('questionset', true);
        $questionset_id = $this->input->post('questionset_id', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if ($cmp_id != '') {
            echo $this->questionset_model->check_Questionset($question, $cmp_id, base64_decode($questionset_id));
        }
    }

    public function export_question($Encode_id) {
        $id = base64_decode($Encode_id);
        $dtWhere = " WHERE qtr.questionset_id=$id AND q.status=1";
        if ($this->mw_session['company_id'] != "") {
            $dtWhere .= " AND q.company_id  = " . $this->mw_session['company_id'];
        }
        $topic_id = $this->input->post('search_topic');
        if ($topic_id != "") {
            $dtWhere .= " AND q.topic_id  = " . $topic_id;
        }
        $subtopic_id = $this->input->post('search_subtopic');
        if ($subtopic_id != "") {
            $dtWhere .= " AND q.subtopic_id  = " . $subtopic_id;
        }
        $search_trainer = $this->input->post('search_trainer');
        if ($search_trainer != "") {
            $dtWhere .= " AND qtr.trainer_id  = " . $search_trainer;
        }
        $search_status = $this->input->post('search_status');
        if ($search_status != "") {
            if ($search_status == 2) {
                $dtWhere .= " AND q.id IN(select question_id from question_inactive where questionset_id=$id) ";
            } else {
                $dtWhere .= " AND q.id NOT IN(select question_id from question_inactive where questionset_id=$id)";
            }
        }
        $language_id = $this->input->post('language_id');
        if ($language_id != "") {
            $dtWhere .= " AND q.language_id  = " . $language_id;
        }
//        $question_id = $this->input->post('id', TRUE);
//        if ($question_id != "") {
//            $id_list = implode(',', $question_id);
//            if ($dtWhere <> '') {
//                $dtWhere .= " AND a.id IN(" . $id_list . ")";
//            } else {
//                $dtWhere .= " Where a.id IN(" . $id_list . ")";
//            }
//        }
        $DTQuestSet = $this->questionset_model->Export_questions($dtWhere, $id);
        $questionset_row = $this->common_model->get_value('question_set', 'title', 'id=' . $id);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'QUESTION SET : ' . $questionset_row->title)
                ->setCellValue('A2', 'Question ID')
                ->setCellValue('B2', 'Trainer')
                ->setCellValue('C2', 'Topic')
                ->setCellValue('D2', 'Subtopic')
                ->setCellValue('E2', 'Question')
                ->setCellValue('F2', 'Option A')
                ->setCellValue('G2', 'Option B')
                ->setCellValue('H2', 'Option C')
                ->setCellValue('I2', 'Option D')
                ->setCellValue('J2', 'Correct Option')
                ->setCellValue('K2', 'Hint');
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($styleArray_header);
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
                    ->setCellValue("B$i", $Question->trainer_name)
                    ->setCellValue("C$i", $Question->topic)
                    ->setCellValue("D$i", $Question->subtopic)
                    ->setCellValue("E$i", $Question->question_title)
                    ->setCellValue("F$i", $Question->option_a)
                    ->setCellValue("G$i", $Question->option_b)
                    ->setCellValue("H$i", $Question->option_c)
                    ->setCellValue("I$i", $Question->option_d)
                    ->setCellValue("J$i", strtoupper($Question->correct_answer))
                    ->setCellValue("K$i", $Question->tip);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:K$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Export_questionset.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

    public function Question_tableRefresh($Encode_id) {
        $id = base64_decode($Encode_id);
        $dtSearchColumns = array('q.id', 'q.id', 'cu.first_name', 'qt.description', 'qst.description', 'q.question_title', 'correct_answer');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($dtWhere <> '') {
            $dtWhere .= " AND qtr.questionset_id=$id";
        } else {
            $dtWhere .= " WHERE qtr.questionset_id=$id";
        }
        $dtWhere .= " AND q.status=1 ";
        $search_Tid = ($this->input->get('search_topic') ? $this->input->get('search_topic') : '');
        if ($search_Tid != "") {
            $dtWhere .= " AND q.topic_id  = " . $search_Tid;
        }
        $search_Stid = ($this->input->get('search_subtopic') ? $this->input->get('search_subtopic') : '');
        if ($search_Stid != "") {
            $dtWhere .= " AND q.subtopic_id  = " . $search_Stid;
        }
        $search_trainer = ($this->input->get('search_trainer') ? $this->input->get('search_trainer') : '');
        if ($search_trainer != "") {
            $dtWhere .= " AND qtr.trainer_id  = " . $search_trainer;
        }
        $language_id = $this->input->get('language_id');
        if ($language_id != "") {
            $dtWhere .= " AND q.language_id  = " . $language_id;
        }
        $search_status = ($this->input->get('search_status') ? $this->input->get('search_status') : '');
        if ($search_status != "") {
            if ($search_status == 2) {
                $dtWhere .= " AND q.id IN(select question_id from question_inactive where questionset_id=$id) ";
            } else {
                $dtWhere .= " AND q.id NOT IN(select question_id from question_inactive where questionset_id=$id)";
            }
        }
        $AddEdit = ($this->input->get('AddEdit') ? $this->input->get('AddEdit') : 'E');
        $DtQuestionArray = $this->questionset_model->getQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $id);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DtQuestionArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DtQuestionArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'trainer_name', 'topic', 'subtopic', 'question_title', 'correct_answer', 'status');
        foreach ($DtQuestionArray['ResultSet'] as $dtRow) {
            $row = array();
            $Qst_id = $dtRow['id'];
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    $row[] = '<input type="checkbox" class="make-switch" name="status_switch[]" value="' . $Qst_id . '" data-size="small" '
                            . 'data-off-color="success" data-off-text="Active" data-on-color="danger" data-on-text="In-Active"'
                            . ($AddEdit == "V" ? "disabled " : "enabled ") . '   ' . ($dtRow['inactive'] != "" ? "checked" : "") . '>';
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline ">
                                <input type="checkbox" class="checkboxes leftchk" name="id[]" value="' . $Qst_id . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function ajax_company_topic() {

        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['result'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        $data['trainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as username', 'company_id=' . $company_id);
        echo json_encode($data);
    }

    public function ajax_topic_subtopic() {
        $topic_id = $this->input->post('data', TRUE);
        if ($topic_id != "") {
            $data['result'] = $this->common_model->getSubTopic($topic_id);
        } else {
            $data['result'] = array();
        }
        echo json_encode($data);
    }

    public function addtrainer($tr_no) {
        $sub = '';
        $trainer_id = $this->input->post('trainer_id');
        $trainer = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as username', 'userid=' . $trainer_id);
        $topic_id = $this->input->post('topic_id');
        $topic = $this->common_model->get_value('question_topic', 'description', 'id=' . $topic_id);
        $subtopic = $this->input->post('subtopic');

        foreach ($subtopic as $st) {
            $subtopic_name = $this->common_model->get_value('question_subtopic', 'description', 'id=' . $st);
            $sub .= ',' . $subtopic_name->description;
        }
        $tdata = array(
            'trainer_id' => $trainer_id,
            'topic_id' => $topic_id,
            'subtopic' => $subtopic);
        $data['trainerData'] = array('Row' => $tr_no, 'data' => $tdata);
        $data['htmlData'] = '<tr id="Row-' . $tr_no . '"><td>' . $trainer->username . '</td><td>' . $topic->description . '</td><td>' . $sub . '</td><td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $tr_no . ')";><i class="fa fa-times"></i></button> </td></tr>';
        $data['Row_no'] = $tr_no;
        echo json_encode($data);
    }

    public function gettrainer($tr_no) {


        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('cmp_id', true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        //$selected_topic_Arrray = $this->input->post('SelectedArrray', true);
        $trdata = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as username', 'company_id=' . $cmp_id);
        //$tpdata = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $cmp_id);
        $tpdata = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $cmp_id);
        $htdata = '<tr id="Row-' . $tr_no . '">';
        $htdata .= '<td><select id="trainer_id' . $tr_no . '" name="New_trainer_id[]" class="form-control input-sm select2"  style="width:100%">';
        $htdata .= '<option value="">please select</option>';
        foreach ($trdata as $tr) {
            $htdata .= '<option value="' . $tr->userid . '">' . $tr->username . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<input type="hidden" value="' . $tr_no . '" name="TotalSubTopic[]">';
        $htdata .= '<td><select id="topic_id' . $tr_no . '" name="New_topic_id[]" class="form-control input-sm select2 ValueUnq" onchange="getTopicwiseSubtopic(' . $tr_no . ');" style="width:100%">';
        $htdata .= '<option value="">please select</option>';
        foreach ($tpdata as $tp) {
            $htdata .= '<option value="' . $tp->id . '">' . $tp->description . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<td><select id="subtopic' . $tr_no . '" name="New_subtopic_id' . $tr_no . '[]" class="form-control input-sm select2" style="width:100%" multiple="" selected></select></td>';
        $htdata .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $tr_no . ')";><i class="fa fa-times"></i></button> </td></tr>';
        $htdata .='<script>$( "#subtopic' . $tr_no . '" ).rules( "add", {
    required: true
});</script>';
        $data['htmlData'] = $htdata;
        echo json_encode($data);
    }

}
