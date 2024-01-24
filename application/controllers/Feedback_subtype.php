<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Feedback_subtype extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('feedback_subtype');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('feedback_subtype_model');
    }
    
    public function index() {
        $data['module_id'] = '7.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $data['FedbackTypeset'] =array();
        } else {
            $data['CompnayResultSet'] = array();
            $data['feedback_typeSet'] = $this->common_model->get_selected_values('feedback_type','description,id','company_id='.$Company_id);
        }
        $data['Company_id'] = $Company_id;        
        $this->load->view('feedback_subtype/index', $data);
    }
    public function edit(){
        $alert_type='success';
        $message='';
        if(count((array)$this->input->post()) > 0 ){
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;
            if (!$data['acces_management']->allow_edit) {
            }else{
                $data['result'] = $this->feedback_subtype_model->find_by_id($edit_id);
                echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['result']));
            }            
        }else{
            $alert_type='error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>'')); 
        }
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('m.id','m.id','b.company_name', 'm.description','ft.description', 'm.status', 'm.id');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if($dtWhere<>''){
            $dtWhere .= " AND m.id  != 0"; 
        }else{
            $dtWhere .= " WHERE m.id  != 0"; 
        } 
        if ($this->mw_session['company_id'] == "") {
            $search_cmp= ($this->input->get('search_cmp') ? $this->input->get('search_cmp') :'');
            if($search_cmp !="")
            {            
              $dtWhere .= " AND m.company_id  = ".$search_cmp;                                     
           }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND m.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $search_ftype= ($this->input->get('search_ftype') ? $this->input->get('search_ftype') :'');
        if($search_ftype !="")
         {            
            $dtWhere .= " AND m.feedbacktype_id  = ".$search_ftype;                                    
        }
        $DTRenderArray = $this->feedback_subtype_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','id', 'company_name','type','description', 'status', 'Actions');
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
    public function submit() {
        $alert_type='success';
        $url='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $url = base_url().'feedback_subtype';
        }else{

            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('description', 'Sub-Type name', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('feedbacktype_id', 'Feedback Type', 'trim|required');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                if ($this->input->post('edit_id')==''){
                    $mode= 'add';
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'feedbacktype_id' => $this->input->post('feedbacktype_id'),
                        'description' => $this->input->post('description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );    
                    $this->common_model->insert('feedback_subtype', $data);
                    $this->session->set_flashdata('flash_message', "Feedback Sub-Type created successfully.");
                    $message = "Feedback Sub-Type created successfully.";
                    $url = base_url().'feedback_subtype';
                }else{
                    $mode= 'edit';
                    $now = date('Y-m-d H:i:s');
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $OldData = $this->common_model->get_value('feedback_subtype', 'company_id', 'id =' . $edit_id);
                    $Success=1;
                    if($OldData->company_id !=$Company_id){
                        $LockFlag = $this->feedback_subtype_model->CrosstableValidation($edit_id);
                        if(!$LockFlag){
                            $alert_type = 'error';
                            $message = "You cannot change the Company.Reference of SubType found in other Company";
                            $Success=0;
                        }
                    }
                    if($Success){
                            $data = array(
                            'company_id' => $Company_id,
                            'feedbacktype_id' => $this->input->post('feedbacktype_id'),
                            'description' => $this->input->post('description'),
                            'status' => $this->input->post('status'),
                            'addeddate' => $now,
                            'addedby' => $this->mw_session['user_id'],
                            'deleted' => 0
                        );    
                        $this->common_model->update('feedback_subtype','id',$edit_id, $data);
                        $this->session->set_flashdata('flash_message', "Feedback Sub-Type updated successfully.");
                        $message = "Feedback Sub-Type updated successfully.";
                    }
                    
                    $url = base_url().'feedback_subtype';
                }
                
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'url'=>$url,'mode'=>$mode));
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
            $StatusFlag=true;
            $deleted_id = $this->input->Post('deleteid');
            $StatusFlag = $this->feedback_subtype_model->CrosstableValidation(base64_decode($deleted_id));
            if($StatusFlag){
                $this->feedback_subtype_model->remove(base64_decode($deleted_id));  
                $message = "Feedback Sub-Type deleted successfully.";
            }else{
                $alert_type = 'error';
                $message= "Feedback Sub-Type cannot be deleted. Reference of Feedback Sub Type found in other module!<br/>"; 
            }   
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function record_actions($Action) {
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
                $this->common_model->update('feedback_subtype', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            $StatusFlag=true;
            foreach ($action_id as $id) {
                $StatusFlag = $this->feedback_subtype_model->CrosstableValidation($id);
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('feedback_subtype', 'id', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Status cannot be change. Reference of Feedback Type found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->feedback_subtype_model->CrosstableValidation($id);
                if($DeleteFlag){
                    $this->common_model->delete('feedback_subtype', 'id', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Feedback Sub-Type cannot be deleted. Reference of Feedback Type found in other module!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Feedback Sub-Type(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }    
    public function ajax_company() {
        $data['result'] = $this->common_model->get_selected_values('company','company_name,id','status=1');
        echo json_encode($data);
    }
    public function ajax_company_type() {               
        $company_id = $this->input->post('data', TRUE);          
        $data['result'] = $this->common_model->get_selected_values('feedback_type','description,id','company_id='.$company_id);
        echo json_encode($data);    
    }
     public function Check_feedbacksubtype() {
        $subtype = $this->input->post('subtype', true);
        if($this->mw_session['company_id']==""){
            $cmp_id = $this->input->post('company_id', TRUE);
        }else{
            $cmp_id =$this->mw_session['company_id'];
        }
        $feedbacktype_id = $this->input->post('feedbacktype_id', true);
        $subtype_id = $this->input->post('subtype_id', true);
        if($cmp_id !='' && $feedbacktype_id !=''){
        echo $this->feedback_subtype_model->check_subtype($subtype,$cmp_id,$feedbacktype_id,$subtype_id);
        }
    }
}
