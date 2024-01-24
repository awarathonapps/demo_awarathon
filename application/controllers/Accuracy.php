<?php

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\PseudoTypes\True_;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Accuracy extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('accuracy');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('accuracy_model');
    }

    public function index()
    {
        $data['module_id'] = '45.02';
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        if ($data['company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
        }

        $data['ThresholdData'] = $this->common_model->get_selected_values('company_threshold_range', 'id,range_from,range_to,range_color', 'company_id=' . $data['company_id']);
        $Threshold_array = array();
        if (count((array)$data['ThresholdData']) > 0) {
            foreach ($data['ThresholdData'] as $value) {
                $Threshold_array[$value->id] = array(
                    'range_from' => $value->range_from,
                    'range_to' => $value->range_to, 'range_color' => $value->range_color,
                );
            }
        }
        $this->session->set_userdata('Assessment_threshold_session', $Threshold_array);

        $data['ResultData'] = $this->common_model->get_selected_values('company_threshold_result', 'result_from as range_from,result_to as range_to,result_color as range_color,assessment_status', 'company_id=' . $data['company_id']);
        $result_array = array();
        if (count((array)$data['ResultData']) > 0) {
            foreach ($data['ResultData'] as $value) {
                $result_array[$value->assessment_status] = array(
                    'range_from' => $value->range_from,
                    'range_to' => $value->range_to, 'range_color' => $value->range_color,
                );
            }
        }
        $this->session->set_userdata('Assessment_result_session', $result_array);

        // $data['region_data'] = $this->adoption_model->get_trainee_region($data['company_id']);

        $data['start_date'] = date('d-M-Y', strtotime('-6 days'));
        $data['end_date'] = date("d-m-Y");
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date("Y-m-d");

        //Added

        $data['company_id'] = $this->mw_session['company_id'];
        $company_id = $data['company_id'];
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="2" AND company_id="' . $company_id . '"');

        //$assessment_list= $this->adoption_model->get_assessment_list($company_id, $trainer_id, $start_date, $end_date);


        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');

        //----------------
        // $data['assessment_data'] = $this->adoption_model->get_assessment($data['company_id'], '', $start_date, $end_date);

        // $data['parameter_data'] = $this->adoption_model->get_parameter();
        $data['assessment'] = $this->accuracy_model->get_all_assessment();
        $this->load->view('accuracy/index', $data);
    }

    public function ajax_getWeeks()
    {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function Competency_understanding_graph($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $Report_Type = $this->input->post('report_type', TRUE);
        $this->load->model('accuracy_model');
        $report_data = array();
        $report_title = '';
        $Userstarted = array();
        $index_label = array();
        if($Assessment_id=="") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $getname_id_type = $this->accuracy_model->LastExpiredAssessment($CurrentDate);

            $report_title = $getname_id_type[0]['assessment'];
            $assessment_id=$getname_id_type[0]['id'];
            $report_type=$getname_id_type[0]['report_type'];
            
            $getscore = $this->accuracy_model->getCompetencyscore($assessment_id, $report_type);
                $count  = '0';
                $count1 = '0';
                $count2 = '0';
                $count3 = '0';
                $count4 = '0';
                $count5 = '0';
                $count6 = '0';
                $count7 = '0';
                $count8 = '0';
                $count9 = '0';
                if(count($getscore)==0){
                    $index_dataset[]='0';
                } else {
                    for ($i = 0; $i < count($getscore); $i++) {
                        $score[] = $getscore[$i]['overall_score'];
                    }
                    for ($j = 0; $j < count($score); $j++) {
                        if ($score[$j] < 10) {
                            $count++;
                        } else if ($score[$j] >= 10 and $score[$j] <= 20) {
                            $count1++;
                        } else if ($score[$j] >= 20 and $score[$j] <= 30) {
                            $count2++;
                        } else if ($score[$j] >= 30 and $score[$j] <= 40) {
                            $count3++;
                        } else if ($score[$j] >= 40 and $score[$j] <= 50) {
                            $count4++;
                        } else if ($score[$j] >= 50 and $score[$j] <= 60) {
                            $count5++;
                        } else if ($score[$j] >= 60 and $score[$j] <= 70) {
                            $count6++;
                        } else if ($score[$j] >= 70 and $score[$j] <= 80) {
                            $count7++;
                        } else if ($score[$j] >= 80 and $score[$j] <= 90) {
                            $count8++;
                        } else if ($score[$j] >= 90 and $score[$j] <= 100) {
                            $count9++;
                        }
                    }
                }
                $index_dataset = array(
                    '0' => isset($count)? $count :'0',
                    '1' => $count1,
                    '2' => $count2,
                    '3' => $count3,
                    '4' => $count4,
                    '5' => $count5,
                    '6' => $count6,
                    '7' => $count7,
                    '8' => $count8,
                    '9' => $count9,
                );
                $index_label = [
                    '0  to 10%',
                    '10  to 20%',
                    '20  to 30%',
                    '30  to 40%',
                    '40  to 50%',
                    '50  to 60%',
                    '60  to 70%',
                    '70  to 80%',
                    '80  to 90%',
                    '90  to 100%'
                ];
        } else {
                $getscore = $this->accuracy_model->getCompetencyscore($Assessment_id, $Report_Type);
                $count = '0';
                $count1 = '0';
                $count2 = '0';
                $count3 = '0';
                $count4 = '0';
                $count5 = '0';
                $count6 = '0';
                $count7 = '0';
                $count8 = '0';
                $count9 = '0';
                if(count($getscore)==0){
                    $index_dataset[]=0;
                } else {
                    $assessment_name =$this->accuracy_model->get_name($Assessment_id);
                    $report_title = $assessment_name[0]['assessment'];
                    for ($i = 0; $i < count($getscore); $i++) {
                        $score[] = $getscore[$i]['overall_score'];
                    }
                    for ($j = 0; $j < count($score); $j++) {
                        if ($score[$j] < 10) {
                            $count++;
                        } else if ($score[$j] >= 10 and $score[$j] <= 20) {
                            $count1++;
                        } else if ($score[$j] >= 20 and $score[$j] <= 30) {
                            $count2++;
                        } else if ($score[$j] >= 30 and $score[$j] <= 40) {
                            $count3++;
                        } else if ($score[$j] >= 40 and $score[$j] <= 50) {
                            $count4++;
                        } else if ($score[$j] >= 50 and $score[$j] <= 60) {
                            $count5++;
                        } else if ($score[$j] >= 60 and $score[$j] <= 70) {
                            $count6++;
                        } else if ($score[$j] >= 70 and $score[$j] <= 80) {
                            $count7++;
                        } else if ($score[$j] >= 80 and $score[$j] <= 90) {
                            $count8++;
                        } else if ($score[$j] >= 90 and $score[$j] <= 100) {
                            $count9++;
                        }
                    }
                }
                $index_dataset = array(
                    '0' => isset($count)? $count :'0',
                    '1' => $count1,
                    '2' => $count2,
                    '3' => $count3,
                    '4' => $count4,
                    '5' => $count5,
                    '6' => $count6,
                    '7' => $count7,
                    '8' => $count8,
                    '9' => $count9,
                );
                $index_label = [
                    '0  to 10%',
                    '10  to 20%',
                    '20  to 30%',
                    '30  to 40%',
                    '40  to 50%',
                    '50  to 60%',
                    '60  to 70%',
                    '70  to 80%',
                    '80  to 90%',
                    '90  to 100%'
                ];
        }
        $data['report'] = $report_data;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label, JSON_NUMERIC_CHECK);
        $com_under_graph = $this->load->view('accuracy/competency_understanding_graph', $Rdata, true);
        $data['competency_understanding_graph'] = $com_under_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
}