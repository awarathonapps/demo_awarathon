<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subtopics extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('subtopics');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('subtopics_model');
    }

    public function index() {
        $data['module_id'] = '4.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $data['Topic'] = array();
        } else {
            $data['CompnayResultSet'] = array();
            $data['Topic'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'status=1 AND company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;


        $this->load->view('subtopics/index', $data);
    }

    public function LoadModal($Edit_id = "") {

        $Company_id = $this->mw_session['company_id'];
        $TopicFlag = false;
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $data['Topic'] = array();
        } else {
            $data['CompnayResultSet'] = array();
            $TopicFlag = true;
        }
        $data['Company_id'] = $Company_id;
        if ($Edit_id != "") {
            $edit_id = base64_decode($Edit_id);
            $data['result'] = $this->subtopics_model->find_by_id($edit_id);
            $AddEdit = 'E';
            $TopicFlag = true;
            if (count((array)$data['result']) == 0) {
                $AddEdit = 'A';
            } else {
                $Company_id = $data['result'][0]['company_id'];
            }
        } else {
            $data['result'] = array();
            $AddEdit = 'A';
        }
        if ($TopicFlag) {
            $data['Topic'] = $this->common_model->get_selected_values('question_topic', 'id,description', 'status=1 AND company_id=' . $Company_id);
        }

        $data['AddEdit'] = $AddEdit;
        $this->load->view('subtopics/load_modal', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('s.id', 's.id', 'company_name', 'c.description', 's.description', 's.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($dtWhere <> '') {
            $dtWhere .= " AND s.id  != 0";
        } else {
            $dtWhere .= " WHERE s.id  != 0";
        }
        $cmp_id = "";
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('filter_cmp_id') ? $this->input->get('filter_cmp_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND s.company_id  = " . $cmp_id;
            }
        }
        $search_FTopic_id = ($this->input->get('filter_topic_id') ? $this->input->get('filter_topic_id') : '');

        if ($search_FTopic_id != "") {
            $dtWhere .= " AND s.topic_id  = " . $search_FTopic_id;
        }
        $search_Fstatus = $this->input->get('filter_status');
        if ($search_Fstatus != "") {
            $dtWhere .= " AND s.status  = " . $search_Fstatus;
        }

        $DTRenderArray = $this->subtopics_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'topic_name', 'description', 'status', 'Actions');
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
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'subtopics/LoadModal/' . base64_encode($dtRow['id']) . '"  data-target="#LoadModalFilter" data-toggle="modal">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        $action .= '</ul>';
                    } else {
                        $action = '<button class="btn btn-default btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
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

    public function submit($Edit_id = "") {
        if ($Edit_id != "") {
            $edit_id = base64_decode($Edit_id);
            $AddEdit = 'E';
        } else {
            $AddEdit = 'A';
        }
        $acces_management = $this->acces_management;
        $success = 1;
        $message = "";
        if ($AddEdit == "A") {
            if (!$acces_management->allow_add) {
                $message = "You have no rights to Add,Contact Administrator for rights";
                $success = 0;
            }
        } else {
            if (!$acces_management->allow_edit) {
                $message = "You have no rights to Edit,Contact Administrator for rights";
                $success = 0;
            }
        }
        if ($success) {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('topic_id', 'Topic name', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('description', 'Subtopic Name', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $message = validation_errors();
                $success = 0;
            } else {
                $now = date('Y-m-d H:i:s');
                $data = array(
                    'topic_id' => $this->input->post('topic_id'),
                    'company_id' => $Company_id,
                    'description' => ucfirst($this->input->post('description')),
                    'status' => $this->input->post('status')
                );
                if ($AddEdit == "A") {
                    $data['addeddate'] = $now;
                    $data['addedby'] = $this->mw_session['user_id'];
                    $data['deleted'] = 0;
                    $this->common_model->insert('question_subtopic', $data);
                    $message = "Subtopic added successfully.";
                } else {
                    $OldData = $this->common_model->get_value('question_subtopic', 'company_id', 'id =' . $edit_id);
                    if ($OldData->company_id != $Company_id) {
                        $LockFlag = $this->subtopics_model->CrosstableValidation($edit_id);
                        if (!$LockFlag) {
                            $message = "You cannot change the Company.Reference of Sub Topic found in other Company";
                            $success = 0;
                        }
                    }
                    if ($success) {
                        $data['modifieddate'] = $now;
                        $data['modifiedby'] = $this->mw_session['user_id'];
                        $this->common_model->update('question_subtopic', 'id', $edit_id, $data);
                        $this->session->set_flashdata('flash_message', "Subtopic updated successfully.");
                        $message = "Subtopic updated successfully.";
                    }
                }
            }
        }
        $Rdata['message'] = $message;
        $Rdata['success'] = $success;
        echo json_encode($Rdata);
    }

    public function remove() {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->input->Post('deleteid');
            $StatusFlag = $this->subtopics_model->CrosstableValidation(base64_decode($deleted_id));
            if ($StatusFlag) {
                $this->subtopics_model->remove(base64_decode($deleted_id));
                $message = "Subtopic deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Subtopic cannot be deleted. Reference of subtopic found in other module!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
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
                $this->common_model->update('question_subtopic', 'id', $id, $data);
            }
            $message = 'Subtopic changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->subtopics_model->CrosstableValidation($id);
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('question_subtopic', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Subtopic cannot be change. Reference of subtopic found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Subtopic changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->subtopics_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('question_subtopic', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Subtopic cannot be deleted. Reference of subtopic found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Subtopic(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function validate() {
        $status = $this->subtopics_model->validate($this->input->post());
        echo $status;
    }

    public function ajax_populate_topic() {
        return $this->subtopics_model->fetch_topic_data($this->input->get());
    }

    public function ajax_company_topic() {
        $company_id = $this->input->post('data', TRUE);
        $data['result'] = $this->common_model->fetch_object_by_field('question_topic', 'company_id', $company_id);
        echo json_encode($data);
    }

    public function ajax_company() {
        $data['result'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        echo json_encode($data);
    }

    public function Check_subtopic() {

        $subtopic = $this->input->post('subtopic', true);
        $subtopic_id = $this->input->post('subtopic_id', true);
        if ($subtopic_id != "") {
            $subtopic_id = base64_decode($subtopic_id);
        }
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $topic_id = $this->input->post('topic_id', true);
        if ($cmp_id != '' && $topic_id != '') {
            echo $this->subtopics_model->check_subtopic($subtopic, $cmp_id, $topic_id, $subtopic_id);
        }
    }

}
