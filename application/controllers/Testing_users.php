<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Testing_users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('testing_users');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('device_users_model');
    }

    public function index() {
        $data['module_id'] = '22.2';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('device_users/testing_index', $data);
    }

    public function create() {
        $data['module_id'] = '22.2';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('device_users/userlist', $data);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'c.company_name', 'u.firstname', 'u.email');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.istester  = 1";
        } else {
            $dtWhere .= " WHERE u.istester  =1 ";
        }
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $this->mw_session['company_id'];
            }
        }
        
        $DTRenderArray = $this->device_users_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'user_id', 'company_name', 'name', 'email', 'Actions');
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
                    $action = '';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        if ($acces_management->allow_delete) {
                            $action = '<button type="button" value="' . $dtRow['user_id'] . '" name="remove" onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['user_id']) . '\');" class="btn btn-danger btn-sm delete"'
                            . ' ><i class="fa fa-times"></i></button>';
                        }
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
    public function LoadDeviceUsersTable() {
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'c.company_name', 'u.firstname', 'u.email', 'u.lastname');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.istester  = 0 ";
        } else {
            $dtWhere .= " WHERE u.istester  =0 ";
        }
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('modalcompany_id') ? $this->input->get('modalcompany_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $DTRenderArray = $this->device_users_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'company_name', 'name', 'email', 'Actions');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                        $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="selected_id[]" id="modalchk' . $dtRow['user_id'] . '" value="' . $dtRow['user_id'] . '" onclick="SelectedUsers(' . $dtRow['user_id'] . ')"/>
                                <span></span>
                            </label>';
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
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
//            if ($this->mw_session['company_id'] == "") {
//                $this->form_validation->set_rules('company_id', 'Company name', 'required');
//                $Company_id = $this->input->post('company_id');
//            } else {
//                $Company_id = $this->mw_session['company_id'];
//            }
            $NewUsersArrray = $this->input->post('NewUsersArrray');
            if(count((array)$NewUsersArrray)==0){
                $Message = "Please select Device users";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                foreach ($NewUsersArrray as $key => $id) {
                    $data = array('istester' => 1,
                        'modifieddate' => date('Y-m-d H:i:s'),
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('device_users', 'user_id', $id, $data);
                }
                if ($SuccessFlag) {
                    $Message = "Testing user added successfully.";
                } else {
                    $Message = "Error while adding Testing User,Contact Mediaworks for technical support.!";
                    $SuccessFlag = 0;
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function remove() {
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = base64_decode($this->input->Post('deleteid'));
            $data = array(
                'istester' => 0,
                'modifieddate' => date('Y-m-d H:i:s'),
                'modifiedby' => $this->mw_session['user_id']);
            $this->common_model->update('device_users', 'user_id', $deleted_id, $data);
            $message = "Testing User removed successfully.";
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
         if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                // $DeleteFlag = $this->users_model->CheckUserAssignRole($id);
                $DeleteFlag = true;
                if ($DeleteFlag) {
                     $data = array('istester' => 0,
                    'modifieddate' => date('Y-m-d H:i:s'),
                    'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('device_users', 'user_id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "User cannot be deleted. User(s) assigned to role!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Testing User(s) removed successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
}
