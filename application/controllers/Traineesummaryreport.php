<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Traineesummaryreport extends CI_Controller {
    public function __construct() {
        
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'traineesummaryreport');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainee_summary_report_model');
            $this->load->model('common_model');            
        }
    }       
    public function index() {
        $data['module_id'] = '24.4';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanyData'] = array();
        }
        $data['Company_id'] = $Company_id;  
        $this->load->view('trainee_summary_report/index', $data);
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
    public function ajax_workshopwise_data() {        
        $workshop_id = $this->input->post('workshop_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        
            $data['TraineeData'] = $this->trainee_summary_report_model->get_traineeData($workshop_id,$company_id);        
       
        echo json_encode($data);
    } 
    public function ajax_chart(){
        
        $Table='';
        $MainTable='';
        $error='';
        $lcHtml='';
        $Label=[];
        $dataset=[];        
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if($workshop_id !='' && $company_id !='' && $trainee_id !=''){
            
            $data['PreData']  = $this->trainee_summary_report_model->get_preData($company_id,$workshop_id,$trainee_id);
            $data['PostData'] = $this->trainee_summary_report_model->get_postData($company_id,$workshop_id,$trainee_id);
            $trainee          = $this->common_model->get_value('device_users','concat(firstname," ",lastname) as traineename','user_id='.$trainee_id);
            $preavg           =(count($data['PreData'])>0 ? number_format($data['PreData'][0]->pre,2) : 0);
            $postavg          =(count($data['PostData'])>0 ? number_format($data['PostData'][0]->post,2) : 0);
            $Label[]          =json_encode($trainee->traineename);
            $Table            ='<table class="table table-bordered " id="ranktable" width="50%">
                                <thead style="background-color:#4f81bd;color:#fff">
                                <tr id="headtr">
                                    <th>Trainee Name</th>                        
                                    <th>Pre</th>
                                    <th>Post</th>
                                    <th>C.E</th>
                                    <th>Rank</th>
                                </tr></thead><tbody>';   
            if(count($data['PreData']) > 0 || count($data['PostData']) > 0){                
                $Table .='<tr id="datatr">
                        <td>'.$trainee->traineename.'</td>
                        <td>'.$preavg.'%</td>
                        <td>'.$postavg.'%</td>
                        <td>'.($postavg-$preavg).'%</td>
                        <td>0</td>
                        </tr>';
            }
            $Table .="</tbody></table>";                                                                
            $dataset[]=($postavg-$preavg);
            $topic_id=[];
            $subtopic_id=[];
            $data['PrePostMainData'] = $this->trainee_summary_report_model->get_PrePostMainData($company_id,$workshop_id,$trainee_id);
                                   
            $MainTable ='<table class="table table-bordered " id="ranktable" width="50%">
                        <thead style="background-color:#4f81bd;color:#fff">
                        <tr id="headtr">
                            <th>Topics</th>                        
                            <th>Subtopics</th>
                            <th>Pre</th>
                            <th>Post</th>
                            <th>C.E.</th>
                        </tr></thead><tbody>';   
           
           
            if(count($data['PrePostMainData']) > 0){
                foreach ($data['PrePostMainData'] as $value){                                                                      
                        $MainTable .='<tr id="datatr">
                            <td>'.$value->topic.'</td>
                            <td>'.$value->subtopic.'</td>
                            <td>'.$value->pre_accuracy.'%</td>
                            <td>'.$value->post_accuracy.'%</td>
                            <td>'.($value->post_accuracy-$value->pre_accuracy).'%</td>
                        </tr>';                            
                                                                                        
                }
            }            
    $MainTable .="</tbody></table>";               
    $data['dataset'] =json_encode($dataset, JSON_NUMERIC_CHECK);
    $data['label'] = json_encode($Label);   
    
    $lcHtml = $this->load->view('trainee_summary_report/show_report',$data,true);
        
    }else{
        $error="Please Select Company,Workshop And Trainee";
    }
    $main_html ="<div class='row'>
                <div class='col-12' id='graph_table'>
                <div class='col-6'>".
                    $lcHtml
                ."</div>
                <div class='col-6' id='tablecontainer'>".
                    $Table
                ."</div>
                </div></div>
                <div class='clearfix margin-top-30'></div>
                <div class='row'>
                <div class='col-12' id='maintablecontainer'>
                $MainTable
                </div></div>";
                    
    $data['ChartDataHtml']=$main_html;    
    $data['Error']=$error;    
    echo json_encode($data);    
    }

}