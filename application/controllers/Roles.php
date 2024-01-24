<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Roles extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('roles');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('roles_model');
    }
    public function index() {
        $data['module_id'] = '99.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->roles_model->fetch_access_data();
        $this->load->view('roles/index', $data);
    }
    public function create() {
        $data['module_id'] = '99.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->roles_model->fetch_access_data();
        $this->load->view('roles/create', $data);
    }
    public function edit($role_id) {
        $role_id = base64_decode($role_id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('roles');
            return;
        }

        $data['module_id'] = '99.01';
        $data['role_id'] = $role_id;
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->roles_model->fetch_access_data();
        $data['role_data'] = $this->roles_model->find_by_value($role_id);
        $data['result'] = $this->roles_model->find_by_id($role_id);
        $this->load->view('roles/edit', $data);
    }
    public function copy($role_id) {
        $role_id = base64_decode($role_id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('roles');
            return;
        }
        $data['module_id'] = '99.01';
        $data['role_id'] = $role_id;
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->roles_model->fetch_access_data();
        $data['role_data'] = $this->roles_model->find_by_value($role_id);
        $data['result'] = $this->roles_model->find_by_id($role_id);
        $this->load->view('roles/copy', $data);
    }
    public function view($role_id) {
        $role_id = base64_decode($role_id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('roles');
            return;
        }

        $data['module_id'] = '99.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->roles_model->fetch_access_data();
        $data['role_data'] = $this->roles_model->find_by_value($role_id);
        $data['result'] = $this->roles_model->find_by_id($role_id);
        $this->load->view('roles/view', $data);
    }
    public function Check_role() {
        $role = $this->input->post('role', true);
        $role_id = $this->input->post('role_id', true);
        if ($role_id != "") {
            $role_id = base64_decode($role_id);
        }
        echo $this->roles_model->check_role($role, $role_id);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('a.arid','a.arid', 'a.rolename', 'a.description', 'a.status', 'a.arid');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->roles_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','arid', 'rolename', 'description','usercount', 'status', 'Actions');
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
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['arid'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action='';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete){
                    $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                                if ($acces_management->allow_view){
                                    $action .= '<li>
                                        <a href="'.$site_url.'roles/view/'.base64_encode($dtRow['arid']).'">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                                }
                                if ($acces_management->allow_edit){
                                    $action .= '<li>
                                        <a href="'.$site_url.'roles/edit/'.base64_encode($dtRow['arid']).'">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                                }
                                 if ($acces_management->allow_add) {
                                    $action .= '<li>
                                                <a href="' . $site_url . 'roles/copy/' . base64_encode($dtRow['arid']) . '">
                                                <i class="fa fa-copy"></i>&nbsp;Copy
                                                </a>
                                            </li>';
                                }
                                if ($acces_management->allow_delete){
                                    $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\''.$dtRow['rolename'].'\',\''.base64_encode($dtRow['arid']).'\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                                }
                                $action .= '</ul>
                            </div>';
                    }else{
                        $action='<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
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
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Role Name', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $Moduleset = $this->roles_model->fetch_access_data();
                $TempFlag = 0;
                foreach ($Moduleset as $checks) {
                    $modulename = $checks->modulename;
                    $role_checks = $this->input->post($modulename . '_own');
                    if (count((array)$role_checks) > 0) {
                        $TempFlag = 1;
                        break;
                    }
                }
                if (!$TempFlag) {
                    $Message = "You have not selected any modules,Please select Modules.!";
                    $SuccessFlag = 0;
                } else {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'rolename' => $this->input->post('name'),
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0);
                    $newid = $this->common_model->insert('access_roles', $data);
                    if ($newid != "") {
                        foreach ($Moduleset as $checks) {
                            $modulename = $checks->modulename;
                            $moduleid = $checks->moduleid;
                            $role_checks = $this->input->post($modulename . '_own');
                            if (count((array)$role_checks) > 0) {
                                $data = array(
                                    'roleid' => $newid,
                                    'moduleid' => $moduleid,
                                    'allow_access' => 1,
                                    'addeddate' => $now,
                                    'addedby' => $this->mw_session['user_id']
                                );
                                foreach ($role_checks as $rc) {
                                    switch ($rc) {
                                        case 2:
                                            $data['allow_view'] = 1;
                                            break;
                                        case 3:
                                            $data['allow_add'] = 1;
                                            break;
                                        case 4:
                                            $data['allow_edit'] = 1;
                                            break;
                                        case 5:
                                            $data['allow_delete'] = 1;
                                            break;
                                        case 6:
                                            $data['allow_print'] = 1;
                                            break;
                                        case 7:
                                            $data['allow_import'] = 1;
                                            break;
                                        case 8:
                                            $data['allow_export'] = 1;
                                            break;
                                    }
                                }
                                $this->common_model->insert('access_role_modules', $data);
                            }
                        }
                        $Message = "Role created successfully.";
                    } else {
                        $Message = "Error while creating Role,Contact Mediaworks for technical support.!";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($role_id) {
        $role_id = base64_decode($role_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Role Name', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $Moduleset = $this->roles_model->fetch_access_data();
                $TempFlag = 0;
                foreach ($Moduleset as $checks) {
                    $modulename = $checks->modulename;
                    $role_checks = $this->input->post($modulename . '_own');
                    if (count((array)$role_checks) > 0) {
                        $TempFlag = 1;
                        break;
                    }
                }
                if (!$TempFlag) {
                    $Message = "You have not selected any modules,Please select Modules.!";
                    $SuccessFlag = 0;
                } else {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'rolename' => $this->input->post('name'),
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                        'deleted' => 0);
                    $this->common_model->update('access_roles', 'arid', $role_id, $data);
                    $this->common_model->delete('access_role_modules','roleid',$role_id);
                    foreach ($Moduleset as $re) {
                        $modulename = $re->modulename;
                        $moduleid = $re->moduleid;
                        $role_checks = $this->input->post($modulename . '_own');
                        if (count((array)$role_checks) > 0) {
                            $data = array(
                                'roleid' => $role_id,
                                'moduleid' => $moduleid,
                                'allow_access' => 1,
                                'addeddate' => $now,
                                'addedby' => $this->mw_session['user_id']
                            );
                            foreach ($role_checks as $rc) {
                                switch ($rc) {
                                    case 2:
                                        $data['allow_view'] = 1;
                                        break;
                                    case 3:
                                        $data['allow_add'] = 1;
                                        break;
                                    case 4:
                                        $data['allow_edit'] = 1;
                                        break;
                                    case 5:
                                        $data['allow_delete'] = 1;
                                        break;
                                    case 6:
                                        $data['allow_print'] = 1;
                                        break;
                                    case 7:
                                        $data['allow_import'] = 1;
                                        break;
                                    case 8:
                                        $data['allow_export'] = 1;
                                        break;
                                }
                            }
                            $this->common_model->insert('access_role_modules', $data);
                        }
                    }
                    $Message = "Role updated successfully.";
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function remove(){
        $alert_type='success';
        $message='';
        $title='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        }else{
            $deleted_id = $this->input->Post('deleteid');
            $StatusFlag = $this->roles_model->CheckUserAssignRole(base64_decode($deleted_id));
            if($StatusFlag){
                $this->roles_model->remove_role(base64_decode($deleted_id));  
                $message = "Role deleted successfully.";
            }else{
                $alert_type = 'error';
                $message= "Role cannot be deleted. User(s) assigned to role!<br/>"; 
            }   
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
        $now = date('Y-m-d H:i:s');
        $alert_type='success';
        $message='';
        $title='';
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('access_roles', 'arid', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->roles_model->CheckUserAssignRole($id);
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('access_roles', 'arid', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Status cannot be change. User(s) assigned to role!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->roles_model->CheckUserAssignRole($id);
                if($DeleteFlag){
                    $this->common_model->delete('access_roles', 'arid', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Role cannot be deleted. User(s) assigned to role!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Role(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function validate() {
        $status = $this->roles_model->validate($this->input->post());
        echo $status;
    }

    public function validate_edit() {
        $status = $this->roles_model->validate_edit($this->input->post());
        echo $status;
    }
}
