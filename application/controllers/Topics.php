<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Topics extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('topics');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('topics_model');
    }

    public function ajax_company() {
        return $this->common_model->fetch_company_data($this->input->get());
    }

    public function index() {
        $data['module_id'] = '4.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('topics/index', $data);
    }

    public function edit() {
        $alert_type = 'success';
        $message = '';
        if (count((array)$this->input->post()) > 0) {
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;
            if (!$data['acces_management']->allow_edit) {
                
            } else {
                $data['result'] = $this->topics_model->find_by_id($edit_id);
                echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'result' => $data['result']));
            }
        } else {
            $alert_type = 'error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'result' => ''));
        }
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('m.id', 'm.id', 'm.description', 'b.company_name', 'm.status', 'm.id');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->get('filter_company_id') ? $this->input->get('filter_company_id') : '';
            if ($cmp_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND m.company_id  = " . $cmp_id;
                } else {
                    $dtWhere .= " WHERE m.company_id  = " . $cmp_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND m.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE m.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $status = $this->input->get('filter_status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND m.status  = " . $status;
            } else {
                $dtWhere .= " WHERE m.status  = " . $status;
            }
        }

        $DTRenderArray = $this->topics_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'description', 'status', 'Actions');
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
                        // $action = '<div class="btn-group">';
                        // if ($acces_management->allow_edit){
                        //     $action .='<a type="button" class="btn btn-default btn-xs">Edit&nbsp;<i class="fa fa-pencil"></i></a>';
                        // }
                        // if ($acces_management->allow_delete){
                        //     $action .='<a type="button" class="btn btn-default btn-xs">Delete&nbsp;<i class="fa fa-trash-o"></i></a>'; 
                        // }
                        // $action .='</div>';
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a onclick="LoadEditModal(\'' . base64_encode($dtRow['id']) . '\')">
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

    public function submit() {
        $alert_type = 'success';
        $url = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $url = base_url() . 'topics';
        } else {
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('description', 'Topic name', 'trim|required|max_length[250]');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                if ($this->input->post('edit_id') == '') {
                    $mode = 'add';
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'description' => $this->input->post('description'),
                        'company_id' => $Company_id,
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );
                    $this->common_model->insert('question_topic', $data);
                    $this->session->set_flashdata('flash_message', "Questions Topic created successfully.");
                    $message = "Topic created successfully.";
                    $url = base_url() . 'topics';
                } else {
                    $Success = 1;
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $OldData = $this->common_model->get_value('question_topic', 'company_id', 'id =' . $edit_id);
                    if ($OldData->company_id != $Company_id) {
                        $LockFlag = $this->topics_model->CrosstableValidation($edit_id);
                        if (!$LockFlag) {
                            $alert_type = 'error';
                            $message = "You cannot change the Company.Reference of Topic Name found in other Company";
                            $Success = 0;
                        }
                    }
                    $mode = 'edit';
                    $now = date('Y-m-d H:i:s');
                    if ($Success) {
                        $data = array(
                            'description' => $this->input->post('description'),
                            'company_id' => $Company_id,
                            'status' => $this->input->post('status'),
                            'addeddate' => $now,
                            'addedby' => $this->mw_session['user_id'],
                            'deleted' => 0
                        );
                        $this->common_model->update('question_topic', 'id', $edit_id, $data);
                        $this->session->set_flashdata('flash_message', "Questions Topic updated successfully.");
                        $message = "Questions Topic updated successfully.";
                        $url = base_url() . 'topics';
                    }
                }
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'url' => $url, 'mode' => $mode));
    }

    public function update($role_id) {
        $role_id = base64_decode($role_id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('topics');
            return;
        }
        $data['username'] = $this->mw_session['username'];
        $data['rows'] = $this->topics_model->fetch_access_data();
        $data['acessdata'] = $this->topics_model->find_by_value($role_id);
        $data['result'] = $this->topics_model->find_by_id($role_id);

        $this->load->library('form_validation');
        //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('description', 'Type name', 'trim|required|max_length[250]');
        if ($this->mw_session['company_id'] == "") {
            $this->form_validation->set_rules('company_id', 'Company name', 'required');
            $Company_id = $this->input->post('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('topics/edit', $data);
        } else {
            $this->topics_model->update_role($role_id);
            $this->session->set_flashdata('flash_message', "Questions Topic updated successfully.");
            redirect('topics');
        }
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
            $StatusFlag = $this->topics_model->CrosstableValidation(base64_decode($deleted_id));
            if ($StatusFlag) {
                $this->topics_model->remove(base64_decode($deleted_id));
                $message = "Questions Topic deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Questions Topic cannot be deleted. Reference of Questions Type found in other module!<br/>";
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
                $this->common_model->update('question_topic', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->topics_model->CrosstableValidation($id);
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('question_topic', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Reference of Questions Topic found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->topics_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('question_topic', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Questions Topic cannot be deleted. Reference of Questions Topic found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Questions Topic(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function validate() {
        $status = $this->topics_model->validate($this->input->post());
        echo $status;
    }

    public function Check_topic() {
        $topic = $this->input->post('topic', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $topic_id = $this->input->post('topic_id', true);
        if ($cmp_id != '') {
            echo $this->topics_model->check_topic($topic, $cmp_id, $topic_id);
        }
    }

}
