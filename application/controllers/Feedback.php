<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Feedback extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'feedback');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('feedback_model');
            $this->load->model('common_model');
            $this->load->library('form_validation');
            $this->load->library('upload');
        }
    } 
    public function ajax_feedback_company() {
        return $this->common_model->fetch_company_data($_GET);
    }
    public function index() {
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->feedback_model->fetch_access_data();
        $this->load->view('feedback/index', $data);
    }
    public function create() {
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('feedback');
            return;
        }
        $this->load->view('feedback/create', $data);
    }
    public function edit($id) {
        $alert_type='success';
        $message='';
        $feedback_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('feedback');
            return;
        }else{
            $data['company'] = $this->feedback_model->find_by_id($feedback_id);
           
            echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['company']));
        }
        $this->load->helper('form');
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;        
        $data['result'] = $this->feedback_model->fetch_feedback($feedback_id);
        $data['SelectFeedbackType']  = $this->feedback_model->SelectedFeedbackType($feedback_id);
        $data['cmp_result'] = $this->common_model->fetch_object_by_field('company','status','1');
        $this->load->view('feedback/edit', $data);
    }
    public function view($id) {
        $feedback_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('feedback');
            return;
        }
        $this->load->helper('form');
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
         $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->feedback_model->fetch_feedback($feedback_id);
        $data['SelectFeedbackType']  = $this->feedback_model->SelectedFeedbackType($feedback_id);
        $this->load->view('feedback/view', $data);
    }
    public function DatatableRefresh() {
        
        $dtSearchColumns = array('a.id','a.title','a.powered_by','c.company_name','a.trigger_after','a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->feedback_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','id','title','powered_by','company_name','trigger_after','status','Actions');
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
                    $action='';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete){
                    $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                                if ($acces_management->allow_view){
                                    $action .= '<li>
                                        <a href="'.$site_url.'feedback/view/'.base64_encode($dtRow['id']).'">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                                }
                                if ($acces_management->allow_edit){
                                    $action .= '<li>
                                        <a href="'.$site_url.'feedback/edit/'.base64_encode($dtRow['id']).'">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                                }
                                if ($acces_management->allow_delete){
                                    $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\''.$title.'\',\''.base64_encode($dtRow['id']).'\');" href="javascript:void(0)">
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
    public function submit() {
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            redirect('feedback');
            return;
        }
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('company_id', 'Company name', 'required');
        $this->form_validation->set_rules('feedback_name', 'Feedback name', 'required');
        $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
        $this->form_validation->set_rules('no_of_question', 'No of Questions', 'trim|required|max_length[50]');        
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
      
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $now = date('Y-m-d H:i:s');          
            $data = array(
                'company_id' => $this->input->post('company_id'),
                'title' => ucfirst(strtolower($this->input->post('feedback_name'))),
                'short_description' => $this->input->post('short_description'),
                'powered_by' => ucfirst(strtolower($this->input->post('powered_by'))),                                
                'feedback_url' => $this->input->post('url'),
                'trigger_after' => $this->input->post('no_of_question'),                            
                'status' => $this->input->post('status'),
                'addeddate' => $now,
                'addedby' => $this->mw_session['user_id'],                
            );
            $feedback_id=$this->common_model->insert('feedback', $data);
            
            $FeedBackTypeArray = $this->input->post('feedback_type');
            if ($feedback_id != '' && count((array)$FeedBackTypeArray) > 0) {
                foreach ($FeedBackTypeArray as $key => $value) {
                    $Fdata = array(
                        'feedback_id' => $feedback_id,
                        'feedback_type_id' => $value
                    );
                    $this->common_model->insert('feedbackset_type', $Fdata);
                }
            }
            if ($feedback_id != '') {
                $this->session->set_flashdata('flash_message', "Feedback Added Successfully.");
                redirect('feedback');
            } else {
                $this->session->set_flashdata('flash_message', "Error while Adding Feedback Set,Contact Mediaworks for technical support.!.");
                redirect('feedback');
            }
            
            
        }
    }
    public function update($id_id){        
        $id = base64_decode($id_id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            redirect('feedback');
            return;
        }
       $this->load->helper('form');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('company_id', 'Company name', 'required');
        $this->form_validation->set_rules('feedback_name', 'Feedback name', 'required');
        $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
        $this->form_validation->set_rules('no_of_question', 'No of Questions', 'trim|required|max_length[50]');        
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
      
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            
            $now = date('Y-m-d H:i:s');          
            $data = array(
                'company_id' => $this->input->post('company_id'),
                'title' => ucfirst(strtolower($this->input->post('feedback_name'))),
                'short_description' => $this->input->post('short_description'),
                'powered_by' => ucfirst(strtolower($this->input->post('powered_by'))),                                
                'feedback_url' => $this->input->post('url'),
                'trigger_after' => $this->input->post('no_of_question'),                            
                'status' => $this->input->post('status'),
                'addeddate' => $now,
                'addedby' => $this->mw_session['user_id'],                
            );
            $this->common_model->update('feedback','id',$id, $data);
            $FeedBackTypeArray  = $this->input->post('feedback_type');            
            $this->common_model->delete('feedbackset_type', 'feedback_id', $id);           
            if (count((array)$FeedBackTypeArray) > 0) {
                foreach ($FeedBackTypeArray as $key => $value) {
                    $Fdata = array(
                        'feedback_id' => $id,
                        'feedback_type_id' => $value
                    );
                    $this->common_model->insert('feedbackset_type', $Fdata);
                }
            }
            $this->session->set_flashdata('flash_message', "Feedback updated successfully");
            redirect('feedback');
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
            $DeleteFlag = $this->feedback_model->CrosstableValidation(base64_decode($deleted_id));
            if($DeleteFlag){
            $this->feedback_model->remove(base64_decode($deleted_id));  
            $message = "Feedback deleted successfully.";
            }else{
                $alert_type = 'error';
                $message= "Feedback cannot be deleted. Reference of Feedback found in other module!<br/>"; 
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
                $this->common_model->update('feedback', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->feedback_model->CrosstableValidation($id);
                //$StatusFlag = true;
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('feedback', 'id', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Status cannot be change. Feedback(s) assigned to Workshop!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                // $DeleteFlag = $this->workshop_model->CheckUserAssignRole($id);
                $DeleteFlag = $this->feedback_model->CrosstableValidation($id);
                //$DeleteFlag=true;
                if($DeleteFlag){
                    $this->common_model->delete('feedback', 'id', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Feedback cannot be deleted. Feedback(s) assigned to Workshop!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Feedback(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function Check_feedback() {
        $feedback = $this->input->post('feedback', true);
        $cmp_id = $this->input->post('company_id', true);
        $feedback_id = $this->input->post('feedback_id', true);
        echo $this->feedback_model->check_feedback($feedback, $cmp_id,$feedback_id);
    }
    public function ajax_company_feedbackType(){
        $company_id = $this->input->post('data', TRUE);          
        $data['result'] = $this->common_model->get_selected_values('feedback_type','id,description','company_id='.$company_id);        
        echo json_encode($data);
    }
//    public function validate() {
//        $status = $this->feedback_model->validate($_POST);
//        echo $status;
//    }
//
//    public function validate_edit() {
//        $status = $this->feedback_model->validate_edit($_POST);
//        echo $status;
//    }

}
