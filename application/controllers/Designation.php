<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Designation extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('designation');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('designation_model');
    }
    
    public function trainer_index() {
        $data['module_id'] = '1.07';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;        
        $this->load->view('designation/trainer_index', $data);
    }
    public function trainee_index() {
        $data['module_id'] = '1.11';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;        
        $this->load->view('designation/trainee_index', $data);
    }
    public function trainer_edit(){        
        //$edit_id = base64_decode('edit_id',true);
        $alert_type='success';
        $message='';
        $data['module_id'] = '1.07';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if(count((array)$this->input->post()) > 0 ){
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;
            if (!$data['acces_management']->allow_edit) {
            }else{
                $data['result'] = $this->designation_model->trainer_find_by_id($edit_id);
               //$data['resultdata']=$this->subtopics_model->fetch_object_by_field('question_subtopic','id',$edit_id);
                echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['result']));
            }            
        }else{
            $alert_type='error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>'')); 
        }
    }
    public function trainee_edit(){        
        //$edit_id = base64_decode('edit_id',true);
        $alert_type='success';
        $message='';
        $data['module_id'] = '1.11';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if(count((array)$this->input->post()) > 0 ){
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;
            if (!$data['acces_management']->allow_edit) {
            }else{
                $data['result'] = $this->designation_model->trainee_find_by_id($edit_id);
               //$data['resultdata']=$this->subtopics_model->fetch_object_by_field('question_subtopic','id',$edit_id);
                echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['result']));
            }            
        }else{
            $alert_type='error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>'')); 
        }
    }
    public function trainer_DatatableRefresh() {
        $dtSearchColumns = array('s.id','s.id','company_name','s.description','s.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        
        if ($this->mw_session['company_id'] == "") {
            $cmp_id= ($this->input->get('filter_company_id') ? $this->input->get('filter_company_id') :'');
            if($cmp_id !="")
             {            
               if($dtWhere<>''){
                    $dtWhere .= " AND s.company_id  = ".$cmp_id; 
               }else{
                   $dtWhere .= " WHERE s.company_id  = ".$cmp_id; 
               }                                    
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND s.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE s.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $status= $this->input->get('filter_status');
        if($status !="")
         {            
           if($dtWhere<>''){
            $dtWhere .= " AND s.status  = ".$status; 
           }else{
               $dtWhere .= " WHERE s.status  = ".$status; 
           }                                    
        }
                
        $DTRenderArray = $this->designation_model->TrainerLoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','id','company_name','description','status', 'Actions');
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
                                        <a onclick="LoadDeleteDialog(\''.base64_encode($dtRow['id']).'\');" href="javascript:void(0)">
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
    public function trainee_DatatableRefresh() {
        $dtSearchColumns = array('s.id','s.id','company_name','s.description','s.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        
        if ($this->mw_session['company_id'] == "") {
            $cmp_id= ($this->input->get('filter_company_id') ? $this->input->get('filter_company_id') :'');
            if($cmp_id !="")
             {            
               if($dtWhere<>''){
                    $dtWhere .= " AND s.company_id  = ".$cmp_id; 
               }else{
                   $dtWhere .= " WHERE s.company_id  = ".$cmp_id; 
               }                                    
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND s.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE s.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $status= $this->input->get('filter_status');
        if($status !="")
         {            
           if($dtWhere<>''){
            $dtWhere .= " AND s.status  = ".$status; 
           }else{
               $dtWhere .= " WHERE s.status  = ".$status; 
           }                                    
        }
                
        $DTRenderArray = $this->designation_model->TraineeLoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','id','company_name','description','status', 'Actions');
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
                                        <a onclick="LoadDeleteDialog(\''.base64_encode($dtRow['id']).'\');" href="javascript:void(0)">
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
    public function trainer_submit() {
        $alert_type='success';
        $url='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $url = base_url().'designation/trainer_index';
        }else{
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('description', 'Designation', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                if ($this->input->post('edit_id')==''){
                    $mode= 'add';
                    $now = date('Y-m-d H:i:s');
                    $data = array(                        
                        'company_id' => $Company_id,
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->insert('designation', $data);
                    $this->session->set_flashdata('flash_message', "Designation created successfully.");
                    $message = "Designation created successfully.";
                    $url = base_url().'designation/trainer_index';
                }else{
                    $mode= 'edit';
                    $now = date('Y-m-d H:i:s');
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $data = array(                        
                        'company_id' => $Company_id,
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->update('designation','id',$edit_id, $data);
                    $this->session->set_flashdata('flash_message', "Designation updated successfully.");
                    $message = "Designation updated successfully.";
                    $url = base_url().'designation/trainer_index';
                }                
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'url'=>$url,'mode'=>$mode));
    }
    public function trainee_submit() {
        $alert_type='success';
        $url='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $url = base_url().'designation/trainee_index';
        }else{
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('description', 'Designation', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                if ($this->input->post('edit_id')==''){
                    $mode= 'add';
                    $now = date('Y-m-d H:i:s');
                    $data = array(                        
                        'company_id' => $Company_id,
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->insert('designation_trainee', $data);
                    $this->session->set_flashdata('flash_message', "Designation created successfully.");
                    $message = "Designation created successfully.";
                    $url = base_url().'designation/trainee_index';
                }else{
                    $mode= 'edit';
                    $now = date('Y-m-d H:i:s');
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $data = array(                        
                        'company_id' => $Company_id,
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->update('designation_trainee','id',$edit_id, $data);
                    $this->session->set_flashdata('flash_message', "Designation updated successfully.");
                    $message = "Designation updated successfully.";
                    $url = base_url().'designation/trainee_index';
                }                
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'url'=>$url,'mode'=>$mode));
    }
    public function trainer_remove(){
        $alert_type='success';
        $message='';
        $title='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        }else{
            $deleted_id = $this->input->Post('deleteid');
            $StatusFlag = $this->designation_model->TrainerCrosstableValidation(base64_decode($deleted_id));
            if($StatusFlag){
                $this->designation_model->trainer_remove(base64_decode($deleted_id));  
                $message = "Designation deleted successfully.";
            }else{
                $alert_type = 'error';
                $message= "Designation cannot be deleted. Reference of subtopic found in other module!<br/>"; 
            }   
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function trainee_remove(){
        $alert_type='success';
        $message='';
        $title='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        }else{
            $deleted_id = $this->input->Post('deleteid');
            $StatusFlag = $this->designation_model->TraineeCrosstableValidation(base64_decode($deleted_id));
            if($StatusFlag){
                $this->designation_model->trainee_remove(base64_decode($deleted_id));  
                $message = "Designation deleted successfully.";
            }else{
                $alert_type = 'error';
                $message= "Designation cannot be deleted. Reference of subtopic found in other module!<br/>"; 
            }   
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function trainer_record_actions($Action) {
        $action_id = $this->input->Post('id');
        if(count((array)$action_id)==0){
            echo json_encode(array('message' => "Please select record from the list", 'alert_type' => 'error'));
            exit;
        }
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
                $this->common_model->update('designation', 'id', $id, $data);
            }
            $message = 'Designation changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->designation_model->TrainerCrosstableValidation($id);
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('designation', 'id', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Designation cannot be change. Reference of subtopic found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Designation changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->designation_model->TrainerCrosstableValidation($id);
                if($DeleteFlag){
                    $this->common_model->delete('designation', 'id', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Designation cannot be deleted. Reference of designation found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Designation(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function trainee_record_actions($Action) {
        $action_id = $this->input->Post('id');
        if(count((array)$action_id)==0){
            echo json_encode(array('message' => "Please select record from the list", 'alert_type' => 'error'));
            exit;
        }
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
                $this->common_model->update('designation_trainee', 'id', $id, $data);
            }
            $message = 'Designation changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->designation_model->TraineeCrosstableValidation($id);
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('designation_trainee', 'id', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Designation cannot be change. Reference of subtopic found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Designation changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->designation_model->TraineeCrosstableValidation($id);
                if($DeleteFlag){
                    $this->common_model->delete('designation_trainee', 'id', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Designation cannot be deleted. Reference of designation found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Designation(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function trainer_check_designation() {
        $designation = $this->input->post('designation', true);
        $designation_id = $this->input->post('designation_id', true);
        if($this->mw_session['company_id']==""){
            $cmp_id = $this->input->post('company_id', TRUE);
        }else{
            $cmp_id =$this->mw_session['company_id'];
        }
        if($cmp_id !=''){
            echo $this->designation_model->trainer_check_designation($designation, $cmp_id,$designation_id);
        }
    }
    public function trainee_check_designation() {
        $designation = $this->input->post('designation', true);
        $designation_id = $this->input->post('designation_id', true);
        if($this->mw_session['company_id']==""){
            $cmp_id = $this->input->post('company_id', TRUE);
        }else{
            $cmp_id =$this->mw_session['company_id'];
        }
        if($cmp_id !=''){
            echo $this->designation_model->trainee_check_designation($designation, $cmp_id,$designation_id);
        }
    }
}
