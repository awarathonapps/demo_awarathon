<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Smtpsetting extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('smtp');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('smtp_model');
        $this->load->library('form_validation');
    }
    
    public function index() {
       
        $id='';
        $data['module_id'] = '99.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        //$rowdata=$this->common_model->fetch_object_by_id('smtp','status','1');  
        $data['smtpDetails'] = $this->smtp_model->find_by_id();
        
        $this->load->view('smtp/create', $data);
    }      
    public function submit() {
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        }else{
            $this->load->library('form_validation');
            $this->form_validation->set_rules('host_name', 'Host name', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('smtp_secure', 'SMTP Secure', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('port_no', 'Port No', 'trim|required');
            $this->form_validation->set_rules('authentication', 'Authentication', 'trim|required');
            $this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('alias_name', 'Alias Name', 'trim|required');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {      
                    $now = date('Y-m-d H:i:s');
                    $rowdata=$this->common_model->selectall('smtp');
                    if(count((array)$rowdata)>0){
                    $data = array(
                        'smtp_ipadress' => $this->input->post('host_name'),
                        'smtp_secure' => $this->input->post('smtp_secure'),
                        'smtp_portno' => $this->input->post('port_no'),
                        'smtp_authentication' => $this->input->post('authentication'),
                        'smtp_username' => $this->input->post('user_name'),
                        'smtp_alias' => $this->input->post('alias_name'),
                        'smtp_password' => $this->input->post('password'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],                        
                    );    
                    $this->common_model->update('smtp','smtp_id',$rowdata[0]->smtp_id, $data);
                    $Message = "SMTP setting updated successfully.";
                    }else{                    
                        $data = array(
                            'smtp_ipadress' => $this->input->post('host_name'),
                            'smtp_secure' => $this->input->post('smtp_secure'),
                            'smtp_portno' => $this->input->post('port_no'),
                            'smtp_authentication' => $this->input->post('authentication'),
                            'smtp_username' => $this->input->post('user_name'),
                            'smtp_alias' => $this->input->post('alias_name'),
                            'smtp_password' => $this->input->post('password'),
                            'status' => $this->input->post('status'),
                            'modifieddate' => $now,
                            'modifiedby' => $this->mw_session['user_id'],                        
                        );    
                    $inseted_id =$this->common_model->insert('smtp', $data);
                    if($inseted_id !=""){
                        $Message = "SMTP setting updated successfully.";
                    }
                    else{
                        $Message = "Error while creating SMTP setting,Contact Mediaworks for technical support.!";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function Testmail(){
        $SuccessFlag = 1;
        $Message = '';
        $emailData =$this->common_model->selectall('smtp');
        if(count((array)$emailData)>0){
            $testmail = $this->input->post('testmail');
            $body = "This is a test email generated by the Awarathon SMTP .";
            $subject=" Test Mail";
            $ReturnData=$this->common_model->sendPhpMailer('','Test Mail',$testmail,$subject,$body);
            if(!$ReturnData['sendflag']){
                $Message ="Email sending failed,Please check smtp setting..<br/>";
                $Message .=$ReturnData['Msg'];
                $SuccessFlag=0;
            }else{
                $Message ="Test Email sent success.";
            }
        }else{
            $Message ="Please enter valid smtp setting.";
            $SuccessFlag=0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
	public function testmail_mediaworks(){
        $SuccessFlag = 1;
        $Message = '';
        $emailData =$this->common_model->selectall('smtp');
        if(count((array)$emailData)>0){
            $testmail = 'sameer@mworks.in';
            $body = "This is a test email generated by the Awarathon SMTP .";
            $subject=" Test Mail";
            $ReturnData=$this->common_model->sendPhpMailer('','Test Mail',$testmail,$subject,$body);
            if(!$ReturnData['sendflag']){
                $Message ="Email sending failed,Please check smtp setting..<br/>";
                $Message .=$ReturnData['Msg'];
                $SuccessFlag=0;
            }else{
                $Message ="Test Email sent success.";
            }
        }else{
            $Message ="Please enter valid smtp setting.";
            $SuccessFlag=0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
}
