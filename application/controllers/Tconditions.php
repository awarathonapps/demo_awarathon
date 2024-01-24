<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Tconditions extends MY_Controller {
   
    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('tconditions');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
    }
    
    public function index() {
        
        $data['module_id']        = '99.04';
        $data['username']         = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        
        $data['Template'] = $this->common_model->selectall('atom_agreement');
        
        $this->load->view('tconditions/index', $data);
    }
    
    function fetchTemplateData(){
        $template_id  = $this->input->post('template_id');
        $TemplateBody = $this->common_model->get_value('atom_agreement','remarks','id='.$template_id);
        
        $Rdata['TemplateBody'] = $TemplateBody->remarks;
        echo json_encode($Rdata);
    }

    public function updateTemplate() {
        
        $SuccessFlag = 1;
        $Message     = '';
        
        $data['accessRights'] = $this->acces_management;
        $data['username'] = $this->mw_session['username'];
        
        if (!$data['accessRights']->allow_edit) {
            $Message     = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
            redirect('template');
            return;
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('template_name', 'Template Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $Message     = validation_errors();            
            $SuccessFlag = 0;
        } else {
            if($SuccessFlag){
                
                $template_id   = $this->input->post('template_name');
                $templateName  = $this->common_model->get_value('atom_agreement','terms_name','id='.$template_id);
                $template_body = $this->input->post('template_body');
                
                $data = array(
                    'remarks'     => $template_body
                );

                $this->common_model->update('atom_agreement','terms_name',$templateName->terms_name,$data);
                $Message = "Template preferences updated successfully..";
            }
        }
        
        $Rdata['success'] = $SuccessFlag;
        $Rdata['msg']     = $Message;
        echo json_encode($Rdata);
    }
}


