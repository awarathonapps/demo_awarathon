<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Type extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'type');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('type_model');
            $this->load->model('common_model');
            $this->load->library('form_validation');
        }
    }
    public function index() {
        $data['module_id'] = '5.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $this->load->view('type/index', $data);
    }
    public function edit(){
        $alert_type='success';
        $message='';
        if(count((array)$this->input->post()) > 0 ){
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;
            if (!$data['acces_management']->allow_edit) {
            }else{
                $data['result'] = $this->type_model->find_by_id($edit_id);
                echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['result']));
            }            
        }else{
            $alert_type='error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>'')); 
        }
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('m.id','m.id', 'm.description', 'm.status', 'm.id');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->type_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','id', 'description', 'status', 'Actions');
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
                    $action='';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete){
                    // $action = '<div class="btn-group">';
                    // if ($acces_management->allow_edit){
                    //     $action .='<a type="button" class="btn btn-default btn-xs">Edit&nbsp;<i class="fa fa-pencil"></i></a>';
                    // }
                    // if ($acces_management->allow_delete){
                    //     $action .='<a type="button" class="btn btn-default btn-xs">Delete&nbsp;<i class="fa fa-trash-o"></i></a>'; 
                    // }
                    // $action .='</div>';
                    $action ='<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                                if ($acces_management->allow_edit){
                                    $action .= '<li>
                                        <a onclick="LoadEditModal(\''.base64_encode($dtRow['id']).'\')">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                                }
                                if ($acces_management->allow_delete){
                                    $action .= '<li>
                                        <a onclick="LoadDeleteDialog(\''.$dtRow['description'].'\',\''.base64_encode($dtRow['id']).'\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                                }
                    $action .= '</ul>';
                    }else{
                        $action='<button class="btn btn-default btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
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
        $alert_type='success';
        $url='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $url = base_url().'type';
        }else{
            $this->load->helper('form');
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('description', 'Type name', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                if ($this->input->post('edit_id')==''){
                    $mode= 'add';
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->insert('question_type', $data);
                    $this->session->set_flashdata('flash_message', "Questions Type created successfully.");
                    $message = "Questions Type created successfully.";
                    $url = base_url().'type';
                }else{
                    $mode= 'edit';
                    $now = date('Y-m-d H:i:s');
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $data = array(
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->update('question_type','id',$edit_id, $data);
                    $this->session->set_flashdata('flash_message', "Questions Type updated successfully.");
                    $message = "Questions Type updated successfully.";
                    $url = base_url().'type';
                }
                
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'url'=>$url,'mode'=>$mode));
    }
    public function update($role_id){
        $role_id = base64_decode($role_id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('type');
            return;
        }
        $data['username'] = $this->mw_session['username'];
        $data['rows'] = $this->type_model->fetch_access_data();
        $data['acessdata'] = $this->type_model->find_by_value($role_id);
        $data['result'] = $this->type_model->find_by_id($role_id);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('description', 'Type name', 'trim|required|max_length[250]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('type/edit', $data);
        } else {
            $this->type_model->update_role($role_id);
            $this->session->set_flashdata('flash_message', "Questions Type updated successfully.");
            redirect('type');
        }
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
            $StatusFlag = $this->type_model->CrosstableValidation(base64_decode($deleted_id));
            if($StatusFlag){
                $this->type_model->remove(base64_decode($deleted_id));  
                $message = "Questions Type deleted successfully.";
            }else{
                $alert_type = 'error';
                $message= "Questions Type cannot be deleted. Reference of Questions Type found in other module!<br/>"; 
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
                $this->common_model->update('question_type', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->type_model->CrosstableValidation($id);
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('question_type', 'id', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Status cannot be change. Reference of Questions Type found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->type_model->CrosstableValidation($id);
                if($DeleteFlag){
                    $this->common_model->delete('question_type', 'id', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Questions Type cannot be deleted. Reference of Questions Type found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Questions Type(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function validate() {
        $status = $this->type_model->validate($this->input->post());
        echo $status;
    }
}
