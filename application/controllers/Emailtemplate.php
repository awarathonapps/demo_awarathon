<?php
    if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Emailtemplate extends MY_Controller{  
    public function __construct(){    
        parent::__construct();         
        $acces_management = $this->check_rights('emailtemplate');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('emailtemplate_model');
        $this->load->library('form_validation');                    
    }  
    public function index(){
        // $data['module_id'] = '99.99';
		$data['module_id'] = '1.12';
        $data['accessRights'] = $this->acces_management;        
        $emailtemplate_data = $this->emailtemplate_model->fetch_all();
        $data['emailtemplates'] = $emailtemplate_data;
        $flash_login_success = $this->session->flashdata('flash_message');     
        if (isset($flash_login_success) && ! empty($flash_login_success)){
            $data['flash'] = $flash_login_success;
        }
        $this->load->view('email_template/create', $data);
    }    
    public function getemailbody(){
        // $data['module_id'] = '99.99';
		$data['module_id'] = '1.12';
		$data['accessRights'] = $this->acces_management;        
        if(!$data['accessRights']->allow_edit){
            redirect('emailtemplate');
            return;
        }
        $this->load->helper('form');	
		$alert_name   = $this->input->post('alert_name');
		$emailbody_data = $this->emailtemplate_model->emailbody($alert_name);
		$data['emailbodys'] = $emailbody_data;
		$emailtemplate_data = $this->emailtemplate_model->fetch_all();
			$data['emailtemplates'] = $emailtemplate_data;
		$this->load->view('email_template/create',$data);
    }
    public function update($alert_name=''){	
        $SuccessFlag = 1;
        $Message = '';
		$data['accessRights'] = $this->acces_management;        
        if(!$data['accessRights']->allow_edit){
               redirect('emailtemplate');
              return;
        }        
		$this->load->helper('form');
		$this->load->model('emailtemplate_model');            
        //$alert_name = $this->input->post('alert_name');
        $Company_id = $this->mw_session['company_id'];
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');            
        $upload_Path = './assets/uploads/questions';
        $this->form_validation->set_rules('label', 'Lebel', 'required');
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('message', 'Message', 'required');        
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            if($SuccessFlag){
                $data = array(
                    'alert_title'   => $this->input->post('label'),
                    'subject'       => $this->input->post('subject'),
                    'message'       => $this->input->post('message'),
                    'fromname'      => $this->input->post('fromname'),
                    'fromemail'     => $this->input->post('fromemail'),
                    'company_id'    => $Company_id
                );
                $this->emailtemplate_model->update($data, $alert_name);
                $Message = "Successfully Update Email Template !.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
  
}