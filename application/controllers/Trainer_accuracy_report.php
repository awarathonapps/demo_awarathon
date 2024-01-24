<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainer_accuracy_report extends CI_Controller {
    public function __construct() {
        
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'trainer_accuracy_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainer_accuracy_report_model');
            $this->load->model('common_model');            
        }
    }       
    public function index() {
        $data['module_id'] = '12.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanyData'] = array();
        }
        $data['Company_id'] = $Company_id;  
        $this->load->view('trainer_accuracy_report/index', $data);
    }        
    public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        
        $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $company_id);        
        echo json_encode($data);
    } 
    public function ajax_sessionswise_data() {
        $sessions_id = $this->input->post('sessions_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        
            $data['UserData'] = $this->trainer_accuracy_report_model->get_userData($sessions_id,$workshop_id,$company_id);        
       
        echo json_encode($data);
    } 
    public function ajax_chart($TotalChart){
        
        $error='';
        $lcHtml='';
        $Label=[];
        $dataset=[];
        $sessions_id = $this->input->post('sessions', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $user_id = $this->input->post('user_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if($workshop_id !='' && $company_id !='' && $user_id !=''){
            $data['ChartData'] = $this->trainer_accuracy_report_model->get_chartData($sessions_id,$workshop_id,$company_id,$user_id);                               
            if(count((array)$data['ChartData']) > 0){  
                foreach ($data['ChartData'] as $value){
                    $dataset[]=$value->accuracy;
                    $Label[]=$value->Topic.'-'.$value->SubTopic;
                }
            }
            $username=$this->common_model->get_value('device_users','concat(firstname," ",lastname) as username','user_id='.$user_id);  
            $data['dataset'] =json_encode($dataset, JSON_NUMERIC_CHECK);
            $data['label'] =json_encode($Label);
            $data['user'] =json_encode($username->username);
            $data['TotalChart']=$TotalChart;   
            $lcHtml = $this->load->view('trainer_accuracy_report/show_report',$data,true);
    }else{
            $error="Please Select Company And Workshop And User";
    }
    $Rdata['HtmlData']  = $lcHtml;
    $Rdata['Error']     = $error;    
    echo json_encode($Rdata);    
 }

}