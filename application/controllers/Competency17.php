<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class competency extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('competency');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('Competency_model');
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
        $data['assessment'] = $this->Competency_model->get_all_assessment();
        $this->load->view('competency/index', $data);
    }

    public function ajax_getWeeks()
    {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    // Competency understanding graph start here
    public function Competency_understanding_graph($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $Report_Type = $this->input->post('report_type', TRUE);
        $this->load->model('Competency_model');
        $report_data = array();
        $report_title = '';
        $index_label = array();
        if ($Assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $getname_id_type = $this->Competency_model->LastExpiredAssessment($CurrentDate);
            if (count((array)$getname_id_type) > 0) {
                foreach ($getname_id_type as $as) {
                    $report_title = $as->assessment;
                    $assessment_id = $as->id;
                    $report_type = $as->report_type;
                }
                $getscore = $this->Competency_model->getCompetencyscore($assessment_id, $report_type);
                if (count((array)$getscore) > 0) {
                    for ($i = 0; $i < count($getscore); $i++) {
                        $score[] = $getscore[$i]['overall_score'];
                    }
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
                    for ($j = 0; $j < count($score); $j++) {
                        if ($score[$j] >= 0 and $score[$j] <= 10) {
                            $count++;
                        } else if ($score[$j] >= 11 and $score[$j] <= 20) {
                            $count1++;
                        } else if ($score[$j] >= 21 and $score[$j] <= 30) {
                            $count2++;
                        } else if ($score[$j] >= 31 and $score[$j] <= 40) {
                            $count3++;
                        } else if ($score[$j] >= 41 and $score[$j] <= 50) {
                            $count4++;
                        } else if ($score[$j] >= 51 and $score[$j] <= 60) {
                            $count5++;
                        } else if ($score[$j] >= 61 and $score[$j] <= 70) {
                            $count6++;
                        } else if ($score[$j] >= 71 and $score[$j] <= 80) {
                            $count7++;
                        } else if ($score[$j] >= 81 and $score[$j] <= 90) {
                            $count8++;
                        } else if ($score[$j] >= 91 and $score[$j] <= 100) {
                            $count9++;
                        }
                    }
                    $index_dataset = array(
                        '0' => $count,
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
                        '11  to 20%',
                        '22  to 30%',
                        '31  to 40%',
                        '41  to 50%',
                        '51  to 60%',
                        '61  to 70%',
                        '71  to 80%',
                        '81  to 90%',
                        '91  to 100%'
                    ];
                } else {
                    $index_dataset[] = '';
                    $index_label[] = '';
                }
            } else {
                $index_dataset[] = '';
                $index_label[] = '';
            }
        } else {
            $getscore = $this->Competency_model->getCompetencyscore($Assessment_id, $Report_Type);
            if (count((array)$getscore) > 0) {
                $assessment_name = $this->Competency_model->get_name($Assessment_id);
                $report_title = $assessment_name[0]['assessment'];
                for ($i = 0; $i < count($getscore); $i++) {
                    $score[] = $getscore[$i]['overall_score'];
                }
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
                for ($j = 0; $j < count($score); $j++) {
                    if ($score[$j] >= 0 and $score[$j] <= 10) {
                        $count++;
                    } else if ($score[$j] >= 11 and $score[$j] <= 20) {
                        $count1++;
                    } else if ($score[$j] >= 21 and $score[$j] <= 30) {
                        $count2++;
                    } else if ($score[$j] >= 31 and $score[$j] <= 40) {
                        $count3++;
                    } else if ($score[$j] >= 41 and $score[$j] <= 50) {
                        $count4++;
                    } else if ($score[$j] >= 51 and $score[$j] <= 60) {
                        $count5++;
                    } else if ($score[$j] >= 61 and $score[$j] <= 70) {
                        $count6++;
                    } else if ($score[$j] >= 71 and $score[$j] <= 80) {
                        $count7++;
                    } else if ($score[$j] >= 81 and $score[$j] <= 90) {
                        $count8++;
                    } else if ($score[$j] >= 91 and $score[$j] <= 100) {
                        $count9++;
                    }
                }
                $index_dataset = array(
                    '0' => $count,
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
                    '11  to 20%',
                    '21  to 30%',
                    '31  to 40%',
                    '41  to 50%',
                    '51  to 60%',
                    '61  to 70%',
                    '71  to 80%',
                    '81  to 90%',
                    '91  to 100%'
                ];
            } else {
                $index_dataset[] = '';
                $index_label[] = '';
            }
        }
        $data['report'] = $report_data;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label, JSON_NUMERIC_CHECK);
        $com_under_graph = $this->load->view('competency/competency_understanding_graph', $Rdata, true);
        $data['competency_understanding_graph'] = $com_under_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // end here

    //Performance comparison by module
    public function performance_comparison($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $this->load->model('Competency_model');
        $report_data = array();
        $report_title = '';
        $index_label = array();
        $index_dataset = array();

        if ($Assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $getassessment = $this->Competency_model->LastExpiredFiveAssessment($CurrentDate);
            if (count((array)$getassessment) > 0) {
                for ($i = 0; $i < count($getassessment); $i++) {
                    $assessment_Id[] = isset($getassessment[$i]['id']) ? $getassessment[$i]['id'] : " ";
                }

                $getassessment_score = $this->Competency_model->performance_comparison_avg($assessment_Id);
                for ($i = 0; $i < count($getassessment); $i++) {
                    $index_label[] = isset($getassessment_score[$i]['assessment']) ? $getassessment_score[$i]['assessment'] : "Empty Data";
                    $index_dataset[] = isset($getassessment_score[$i]['scores']) ? $getassessment_score[$i]['scores'] : 0;
                }
            } else {
                $index_label[] = '';
                $index_dataset[] = '';
            }
        } else {
            $getassessment_score = $this->Competency_model->performance_comparison_avg($Assessment_id);
            if (count($getassessment_score) > 0) {


                for ($i = 0; $i < count($Assessment_id); $i++) {
                    $index_label[] = isset($getassessment_score[$i]['assessment']) ? $getassessment_score[$i]['assessment'] : "Empty Data";
                    $index_dataset[] = isset($getassessment_score[$i]['scores']) ? $getassessment_score[$i]['scores'] : 0;
                }
            } else {
                $index_label[] = '';
                $index_dataset[] = '';
            }
        }

        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $com_under_graph = $this->load->view('competency/performance_comparison_graph', $Rdata, true);
        $data['performance_comparison_graph'] = $com_under_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // end here

    //Performance comparison by Division graph start here
    public function assessment_wise_division()
    {
        $assessment_html = '';
        $assessmentid = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $assessment_list = $this->Competency_model->getdepartment($assessmentid);
        $assessment_html .= '<option value="">';
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value['department'] . '">' . $value['department'] . '</option>';
            }
        }
        $data['division']  = $assessment_html;
        echo json_encode($data);
    }

    public function performance_comparison_by_division($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $Report_Type = $this->input->post('report_type', TRUE);
        $DvisonId_Set = $this->input->post('dvisonid_set', TRUE);
        $this->load->model('Competency_model');

        $report_data = array();
        $report_title = '';
        $index_label = array();
        $index_dataset = array();
        $index_diff_label = array();
        $final_index_label = array();
        $index_label_arr = array();
        if ($Assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $Lastexassessment = $this->Competency_model->getLAassessment($CurrentDate);
            if (count((array)$Lastexassessment) > 0) {
                $report_title = $Lastexassessment[0]['assessment'];
                $assessment_id = $Lastexassessment[0]['id'];
                $report_type = $Lastexassessment[0]['report_type'];
                $dvisonId_id = $this->Competency_model->expired_assessment_divison($assessment_id);
                if (count((array)$dvisonId_id) > 0) {
                    for ($i = 0; $i < count($dvisonId_id); $i++) {
                        $dvisonId_set[] = isset($dvisonId_id[$i]['department_name']) ? $dvisonId_id[$i]['department_name'] : "Empty Data";
                    }

                    $Get_divison_score = $this->Competency_model->Get_score_divison_wise($assessment_id, $report_type, $dvisonId_set);
                    for ($a = 0; $a < count($dvisonId_set); $a++) {
                        $index_label[] = isset($Get_divison_score[$a]['department_name']) ? $Get_divison_score[$a]['department_name'] : "";
                        $index_dataset[] = isset($Get_divison_score[$a]['score']) ? $Get_divison_score[$a]['score'] : '0';
                    }
                    for ($k = 0; $k < count($dvisonId_set); $k++) {
                        if (in_array($dvisonId_set[$k], $index_label)) {
                            continue;
                        } else {
                            $index_diff_label[] = $dvisonId_set[$k];
                        }
                    }

                    for ($l = 0; $l < count($index_label); $l++) {
                        if (!empty($index_label[$l])) {
                            $index_label_arr[] = $index_label[$l];
                        }
                    }
                    $final_index_label = array_merge($index_label_arr, $index_diff_label);
                } else {
                    $final_index_label[] = '0';
                    $index_dataset[] = '0';
                }
            } else {
                $final_index_label[] = '';
                $index_dataset[] = '';
            }
        } else {
            $Get_divison_score = $this->Competency_model->Get_score_divison_wise($Assessment_id, $Report_Type, $DvisonId_Set);
            if (count((array)$Get_divison_score) > 0) {
                $report_title = $Get_divison_score[0]['assessment'];
                for ($i = 0; $i < count($DvisonId_Set); $i++) {
                    // $index_label[] =   isset($Get_divison_score[$i]['department_name']) ? $Get_divison_score[$i]['department_name']: 'Empty Data' ;
                    $index_label[] =   isset($Get_divison_score[$i]['department_name']) ? $Get_divison_score[$i]['department_name'] : '';
                    $index_dataset[] = isset($Get_divison_score[$i]['score']) ? $Get_divison_score[$i]['score'] : '0';
                }

                for ($k = 0; $k < count($DvisonId_Set); $k++) {
                    if (in_array($DvisonId_Set[$k], $index_label)) {
                        continue;
                    } else {
                        $index_diff_label[] = $DvisonId_Set[$k];
                    }
                }
                for ($l = 0; $l < count($index_label); $l++) {
                    if (!empty($index_label[$l])) {
                        $index_label_arr[] = $index_label[$l];
                    }
                }
                $final_index_label = array_merge($index_label_arr, $index_diff_label);
            } else {
                $final_index_label[] = '';
                $index_dataset[] = '';
            }
        }
        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        // $Rdata['index_label'] = json_encode($index_label_arr);
        $Rdata['index_label'] = json_encode($final_index_label);
        $Rdata['report_title'] = json_encode($report_title);
        $per_divsion_graph = $this->load->view('competency/performance_comparison_by_division', $Rdata, true);
        $data['performance_comparison_by_division'] = $per_divsion_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    //end here

    // Performance comparison by Region graph start here
    public function assessment_wise_region()
    {
        $assessment_html = '';
        $Company_id = $this->mw_session['company_id'];
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $assessment_list = $this->Competency_model->assessment_wise_region($assessment_id, $Company_id);
        $assessment_html .= '<option value="">';
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '">' . $value->region_name . '</option>';
            }
        }
        $data['region']  = $assessment_html;
        echo json_encode($data);
    }

    public function performance_comparison_by_region($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $Report_Type = $this->input->post('report_type', TRUE);
        $Region_id = $this->input->post('region_id', TRUE);
        $this->load->model('Competency_model');

        $report_data = array();
        $report_title = '';
        $index_label = array();
        $index_dataset = array();
        if ($Assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $Lastexassessment = $this->Competency_model->LAassessment_and_type($CurrentDate);
            if (count((array)$Lastexassessment) > 0) {
                $report_title = $Lastexassessment[0]['assessment'];
                $assessment_id = $Lastexassessment[0]['id'];
                $report_type = $Lastexassessment[0]['report_type'];
                $get_region_id = $this->Competency_model->expired_assessment_region($assessment_id);
                if (count((array)$get_region_id) > 0) {
                    for ($r = 0; $r < count($get_region_id); $r++) {
                        $region_id[] = isset($get_region_id[$r]['region_id']) ? $get_region_id[$r]['region_id'] : '';
                    }
                    $Get_region_score = $this->Competency_model->Get_score_region_wise($assessment_id, $report_type, $region_id);
                    for ($a = 0; $a < count($Get_region_score); $a++) {
                        $index_label[] = isset($Get_region_score[$a]['region_name']) ? $Get_region_score[$a]['region_name'] : "";
                        $index_dataset[] = isset($Get_region_score[$a]['score']) ? $Get_region_score[$a]['score'] : '0';
                    }
                } else {
                    $index_label[] = '';
                    $index_dataset[] = '';
                }
            } else {
                $index_label[] = '';
                $index_dataset[] = '';
            }
        } else {
            $Get_region_score = $this->Competency_model->Get_score_region_wise($Assessment_id, $Report_Type, $Region_id);
            if (count((array)$Get_region_score) > 0) {
                $report_title = $Get_region_score[0]['assessment'];

                for ($i = 0; $i < count($Get_region_score); $i++) {
                    $index_label[] =   isset($Get_region_score[$i]['region_name']) ? $Get_region_score[$i]['region_name'] : '';
                    $index_dataset[] = isset($Get_region_score[$i]['score']) ? $Get_region_score[$i]['score'] : '0';
                }
            } else {
                $index_label[] = '';
                $index_dataset[] = '';
            }
        }
        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $Rdata['report_title'] = json_encode($report_title);
        $per_divsion_graph = $this->load->view('competency/performance_comparison_by_region', $Rdata, true);
        $data['performance_comparison_by_region'] = $per_divsion_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    public function region_wise_performance($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $assessment_id = $this->input->post('assessment_id', TRUE);
        $region_id = $this->input->post('region_id', TRUE);
        $report_type = $this->input->post('report_type', TRUE);
        $index_label = array();
        $less_than_range = array();
        $second_range = array();
        $third_range = array();
        $above_range_final = array();

        // static range for customization
        $less_range = 54;
        $second_range_from = 55;
        $second_range_to = 64;
        $third_range_from = 65;
        $third_range_to = 74;
        $above_range = 75;
        // static range for customization

        if (isset($assessment_id) && isset($region_id) && isset($report_type)) {
            $region_wise_score = $this->Competency_model->get_region_score($assessment_id, $region_id, $report_type, $above_range, $second_range_from, $second_range_to, $third_range_from, $third_range_to, $less_range);
            if (count((array)$region_wise_score) > 0) {
                $region_data = $this->Competency_model->get_exipired_assesment_region($assessment_id, $Company_id);
                $region_id = array();
                $region_name = array();
                foreach ($region_data as $rg) {
                    $region_id[] = $rg->region_id;
                    $region_name[] = $rg->region_name;
                    $assessment_name = $rg->assessment_name;
                }
                for ($i = 0; $i < count($region_wise_score); $i++) {
                    $index_label[] = isset($region_wise_score) ? $region_wise_score[$i]['region_name'] : $region_name[$i];
                    $less_than_range[] = isset($region_wise_score[$i]['less_' . $less_range . '']) ? $region_wise_score[$i]['less_' . $less_range . ''] : 0;
                    $second_range[] = isset($region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . '']) ? $region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . ''] : 0;
                    $third_range[] = isset($region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . '']) ? $region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . ''] : 0;
                    $above_range_final[] = isset($region_wise_score[$i]['above_' . $above_range . '']) ? $region_wise_score[$i]['above_' . $above_range . ''] : 0;
                }
            } else {
                $index_label[] = '';
                $less_than_range[] = '';
                $second_range[] = '';
                $third_range[] = '';
                $above_range_final[] = '';
            }
        } else {
            $CurrentDate =  date("Y-m-d h:i:s");
            $Lastexassessment = $this->Competency_model->getLAassessment($CurrentDate);
            if (count((array)$Lastexassessment) > 0) {
                $assessment_id = $Lastexassessment[0]['id'];
                $assessment_name = $Lastexassessment[0]['assessment'];
                $report_type = $Lastexassessment[0]['report_type'];
                $region_data = $this->Competency_model->get_exipired_assesment_region($assessment_id, $Company_id);
                $region_id = array();
                $region_name = array();
                if (count((array)$region_data) > 0) {
                    foreach ($region_data as $rg) {
                        $region_id[] = $rg->region_id;
                        $region_name[] = $rg->region_name;
                    }
                    $region_wise_score = $this->Competency_model->get_region_score($assessment_id, $region_id, $report_type, $above_range, $second_range_from, $second_range_to, $third_range_from, $third_range_to, $less_range);
                    for ($i = 0; $i < count($region_wise_score); $i++) {
                        $index_label[] = $region_wise_score[$i]['region_name'];
                        $less_than_range[] = isset($region_wise_score[$i]['less_' . $less_range . '']) ? $region_wise_score[$i]['less_' . $less_range . ''] : '0';
                        $second_range[] = isset($region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . '']) ? $region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . ''] : '0';
                        $third_range[] = isset($region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . '']) ? $region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . ''] : '0';
                        $above_range_final[] = isset($region_wise_score[$i]['above_' . $above_range . '']) ? $region_wise_score[$i]['above_' . $above_range . ''] : 0;
                    }
                } else {
                    $index_label[] = '';
                    $less_than_range[] = '';
                    $second_range[] = '';
                    $third_range[] = '';
                    $above_range_final[] = '';
                }
            } else {
                $index_label[] = '';
                $less_than_range[] = '';
                $second_range[] = '';
                $third_range[] = '';
                $above_range_final[] = '';
            }
        }
        $range_list = array('less than ' . $less_range, $second_range_from . ' to ' . $second_range_to, $third_range_from . ' to ' . $third_range_to, 'above ' . $above_range . '');
        $Rdata['range_list'] = json_encode($range_list);
        $Rdata['index_label'] = json_encode($index_label);
        $Rdata['report_title'] = json_encode($assessment_name);
        $Rdata['less_than_range'] = json_encode($less_than_range, JSON_NUMERIC_CHECK);
        $Rdata['second_range'] = json_encode($second_range, JSON_NUMERIC_CHECK);
        $Rdata['third_range'] = json_encode($third_range, JSON_NUMERIC_CHECK);
        $Rdata['above_range_final'] = json_encode($above_range_final, JSON_NUMERIC_CHECK);
        $region_performace = $this->load->view('competency/region_performance', $Rdata, true);
        $data['region_gp'] = $region_performace;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    //End Here

    // Reps who scored more than 85% start here
    public function get_rockstars_user_score()
    {
        $dtSearchColumns = array('emp_id', 'user_name', 'department');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $company_id = $this->mw_session['company_id'];
        if ($dtWhere == "") {
            $dtWhere .= " WHERE 1=1 ";
        }
        $assessment_id = $this->input->get('assessment_id', true);
        if ($assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $get_assessment_id = $this->Competency_model->get_last_expired_assessment($CurrentDate);
            $assessment_id = $get_assessment_id[0]['id'];
        }
        $assessment_name =  $this->Competency_model->assessment_name($assessment_id);
        $assessment = $assessment_name[0]['assessment'];

        if ($assessment_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.assessment_id  = " . $assessment_id;
            } else {
                $dtWhere .= " AND a.assessment_id = " . $assessment_id;
            }
        }
        $dtWhere1 = '';
        $user_final_scores = $this->Competency_model->get_rockstars_user_final_score($company_id, $dtWhere, $dtWhere1, $dtLimit);
        // $user_final_scores = $this->Competency_model->get_rockstars_user_final_score($company_id, $dtWhere,$assessment_id, $dtOrder, $dtLimit);
        if (!empty($user_final_scores) && isset($user_final_scores)) {
            $user_list = [];
            $x = 0;
            $amuser_id = array();
            foreach ($user_final_scores as $us) {
                $amuser_id[] = $us->users_id;
            }
            $ai_user_id = array();
            $ai_score = array();
            $user_ai_score = $this->Competency_model->get_ai_score($assessment_id, $amuser_id);

            if (isset($user_ai_score) && count((array)$user_ai_score) > 0) {
                foreach ($user_ai_score as $ai) {
                    $ai_user_id[] = $ai->users_id;
                    $ai_score[] = $ai->ai_score;
                }
                $y = 0;
                for ($j = 0; $j < count((array)$amuser_id); $j++) {
                    if (in_array($amuser_id[$j], $ai_user_id)) {
                        $user_list[$j]['ai_score'] = $ai_score[$y];
                    } else {
                        $user_list[$j]['ai_score'] = '-';
                        $y--;
                    }
                    $y++;
                }
            } else {
                for ($j = 0; $j < count((array)$amuser_id); $j++) {
                    $user_list[$j]['ai_score'] = '-';
                }
            }

            $user_manual_score = $this->Competency_model->get_manual_score($assessment_id, $amuser_id);
            if (isset($user_manual_score) && count((array)$user_manual_score) > 0) {
                foreach ($user_manual_score as $mu) {
                    $manual_user_id[] = $mu->users_id;
                    $manual_score[] = $mu->manual_score;
                }

                $k = 0;
                for ($l = 0; $l < count((array)$amuser_id); $l++) {
                    if (in_array($amuser_id[$l], $manual_user_id)) {
                        $user_list[$l]['manual_score'] = $manual_score[$k];
                    } else {
                        $user_list[$l]['manual_score'] = '-';
                        $k--;
                    }
                    $k++;
                }
            } else {
                for ($l = 0; $l < count((array)$amuser_id); $l++) {
                    $user_list[$l]['manual_score'] = '-';
                }
            }
            foreach ($user_final_scores as $us) {
                $user_list[$x]['user_id'] = $us->emp_id;
                $user_list[$x]['user_name'] = $us->user_name;
                $user_list[$x]['division'] = $us->department;
                $user_list[$x]['fianl_score'] = isset($user_final_scores[$x]->final_score) ? $user_final_scores[$x]->final_score : '-';
                $x++;
            }
        } else {
            $user_list[] = "";
        }

        $DTRenderArray = $user_list;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            //"iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalRecords" => count((array)$user_final_scores),
            "iTotalDisplayRecords" => count((array)$user_final_scores),
            // "iTotalDisplayRecords" => 5,
            "aaData" => array()
        );
        $output['title'] = $assessment;
        $dtDisplayColumns = array('user_id', 'user_name', 'division', 'ai_score', 'manual_score', 'fianl_score');

        if (!empty($DTRenderArray[0]) && isset($DTRenderArray[0])) {
            foreach ($DTRenderArray as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] != ' ' and isset($dtDisplayColumns)) {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }
    public function export_rockstar_users()
    {
        $Company_id = $this->mw_session['company_id'];
        $assessment_Id = $this->input->post('ammt_id', true);
        if ($assessment_Id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $get_assessment_id = $this->Competency_model->get_last_expired_assessment($CurrentDate);
            $assessment_Id = $get_assessment_id[0]['id'];
        }
        $assessment_name =  $this->Competency_model->assessment_name($assessment_Id);
        $assessment = $assessment_name[0]['assessment'];

        $dtWhere = ' WHERE 1=1 ';

        $dtWhere .= " AND a.assessment_id IN (" . $assessment_Id . ")";
        $type = "rockstars_users";
        $user_final_scores = $this->Competency_model->export_rockstars_and_at_risk_users($dtWhere, '', '', $type);
        $user_list = [];
        if (!empty($user_final_scores) && isset($user_final_scores)) {
            $x = 0;
            $z = 0;
            $amuser_id = array();
            foreach ($user_final_scores as $us) {
                $amuser_id[] = $us->users_id;
            }
            $ai_user_id = array();
            $ai_score = array();
            $user_ai_score = $this->Competency_model->get_ai_score($assessment_Id, $amuser_id);

            if (isset($user_ai_score) && count((array)$user_ai_score) > 0) {
                foreach ($user_ai_score as $ai) {
                    $ai_user_id[] = $ai->users_id;
                    $ai_score[] = $ai->ai_score;
                }
                $y = 0;
                for ($j = 0; $j < count((array)$amuser_id); $j++) {
                    if (in_array($amuser_id[$j], $ai_user_id)) {
                        $user_list[$j]['ai_score'] = $ai_score[$y];
                    } else {
                        $user_list[$j]['ai_score'] = '-';
                        $y--;
                    }
                    $y++;
                }
            } else {
                for ($j = 0; $j < count((array)$amuser_id); $j++) {
                    $user_list[$j]['ai_score'] = '-';
                }
            }

            $user_manual_score = $this->Competency_model->get_manual_score($assessment_Id, $amuser_id);
            if (isset($user_manual_score) && count((array)$user_manual_score) > 0) {
                foreach ($user_manual_score as $mu) {
                    $manual_user_id[] = $mu->users_id;
                    $manual_score[] = $mu->manual_score;
                }

                $k = 0;
                for ($l = 0; $l < count((array)$amuser_id); $l++) {
                    if (in_array($amuser_id[$l], $manual_user_id)) {
                        $user_list[$l]['manual_score'] = $manual_score[$k];
                    } else {
                        $user_list[$l]['manual_score'] = '-';
                        $k--;
                    }
                    $k++;
                }
            } else {
                for ($l = 0; $l < count((array)$amuser_id); $l++) {
                    $user_list[$l]['manual_score'] = '-';
                }
            }
            foreach ($user_final_scores as $us) {
                $user_list[$x]['user_id'] = $us->users_id;
                $user_list[$x]['e_code'] = $us->emp_id;
                $user_list[$x]['user_name'] = $us->user_name;
                $user_list[$x]['division'] = $us->department;
                $user_list[$x]['fianl_score'] = isset($user_final_scores[$x]->final_score) ? $user_final_scores[$x]->final_score : '-';
                $x++;
            }
        } else {
            $user_list[] = "";
        }
        $final_list = array();
        for($p = 0; $p<count((array)$user_list);$p++){
            $final_list[$p]['User ID'] = $user_list[$p]['user_id']; 
            $final_list[$p]['E Code'] = $user_list[$p]['e_code']; 
            $final_list[$p]['Employee name'] = $user_list[$p]['user_name']; 
            $final_list[$p]['Division'] = $user_list[$p]['division']; 
            $final_list[$p]['Ai Score'] = isset($user_list[$p]['ai_score']) ? $user_list[$p]['ai_score'] : '-'  ; 
            $final_list[$p]['Assessor Rating'] =  isset($user_list[$p]['manual_score']) ? $user_list[$p]['manual_score'] : '-'  ; 
            $final_list[$p]['Final Socre'] = isset($user_list[$p]['fianl_score']) ? $user_list[$p]['fianl_score'] : '-'  ; 
        }
        $Data_list = $final_list;
        $this->load->library('PHPExcel');
        $objPHPExcel = new Spreadsheet();

        $objPHPExcel->setActiveSheetIndex(0);
        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );
        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
            )
        );
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray_body);
        $i = 1;
        $j = 1;
        $dtDisplayColumns = array_keys($final_list[0]);
        foreach ($dtDisplayColumns as $column) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $column);
            $j++;
        }
        $j = 2;
        foreach ($Data_list as $value) {
            $i = 1;
            foreach ($dtDisplayColumns as $column) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                $i++;
            }
            $j++;
        }
        $file_name = "Rockstars (Reps who scored more than 85 %) " . $assessment;
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        if ($assessment != "") {
            header('Content-Disposition: attachment;filename=' . "$file_name.xls");
        } else {
            header('Content-Disposition: attachment;filename="Rockstars (Reps who scored more than 85 %).xls"');
        }
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_end_clean();
        $objWriter->save('php://output');
    }
    // end here


    public function get_at_risk_user_score()
    {
        $dtSearchColumns = array('emp_id', 'user_name', 'department');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $company_id = $this->mw_session['company_id'];
        if ($dtWhere == "") {
            $dtWhere .= " WHERE 1=1 ";
        }
        $assessment_id = $this->input->get('assessment_id', true);
        if ($assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $get_assessment_id = $this->Competency_model->get_last_expired_assessment($CurrentDate);
            $assessment_id = $get_assessment_id[0]['id'];
        }
        $assessment_name =  $this->Competency_model->assessment_name($assessment_id);
        $assessment = $assessment_name[0]['assessment'];

        if ($assessment_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.assessment_id  = " . $assessment_id;
            } else {
                $dtWhere .= " AND a.assessment_id = " . $assessment_id;
            }
        }
        $dtWhere1 = '';
        $user_final_scores = $this->Competency_model->At_risk_users_final_score($company_id, $dtWhere, $dtWhere1, $dtLimit);
        if (!empty($user_final_scores)) {
            $amuser_id  = array();
            $user_list = [];
            foreach ($user_final_scores as $far) {
                $amuser_id[] = $far->users_id;
            }
            $x = 0;
            $user_ai_score = $this->Competency_model->get_ai_score($assessment_id, $amuser_id);
            $au = array();
            foreach ($user_ai_score as $ai) {
                $au[] = $ai->users_id;
            }
            $as = 0;
            for ($a = 0; $a < count($amuser_id); $a++) {
                if (in_array($amuser_id[$a], $au)) {
                    $user_list[$a]['ai_score'] = $user_ai_score[$as]->ai_score;
                } else {
                    $user_list[$a]['ai_score'] = '-';
                    $as--;
                }
                $as++;
            }
            $user_manual_score = $this->Competency_model->get_manual_score($assessment_id, $amuser_id);
            $mu  = array();
            foreach ($user_manual_score as $mi) {
                $mu[] = $mi->users_id;
            }
            $ms = 0;
            for ($m = 0; $m < count($amuser_id); $m++) {
                if (in_array($amuser_id[$m], $mu)) {
                    $user_list[$m]['manual_score'] = $user_manual_score[$ms]->manual_score;
                } else {
                    $user_list[$m]['manual_score'] = '-';
                    $ms--;
                }
                $ms++;
            }

            foreach ($user_final_scores as $rd) {
                $user_list[$x]['user_id'] = $rd->emp_id;
                $user_list[$x]['user_name'] = $rd->user_name;
                $user_list[$x]['division'] = $rd->department;
                $user_list[$x]['fianl_score'] = isset($user_final_scores[$x]->final_score) ? $user_final_scores[$x]->final_score : '-';
                $x++;
            }
        } else {
            $user_list[] = "";
        }
        $DTRenderArray = $user_list;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            //"iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalRecords" => count((array)$user_final_scores),
            // "iTotalDisplayRecords" => count((array)$user_final_scores),
            "iTotalDisplayRecords" => 5,
            "aaData" => array()
        );
        $output['title'] = $assessment;
        $dtDisplayColumns = array('user_id', 'user_name', 'division', 'ai_score', 'manual_score', 'fianl_score');

        if (!empty($DTRenderArray[0]) && isset($DTRenderArray[0])) {
            foreach ($DTRenderArray as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] != ' ' and isset($dtDisplayColumns)) {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }
        echo json_encode($output);
    }

    public function export_at_risk_user()
    {
        $Company_name = "";
        $Company_id = $this->mw_session['company_id'];
        $assessment_Id = $this->input->post('AssessmentsId', true);
        if ($assessment_Id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $get_assessment_id = $this->Competency_model->get_last_expired_assessment($CurrentDate);
            $assessment_Id = $get_assessment_id[0]['id'];
        }
        $assessment_name =  $this->Competency_model->assessment_name($assessment_Id);
        $assessment = $assessment_name[0]['assessment'];

        $dtWhere = ' WHERE 1=1 ';

        $dtWhere .= " AND a.assessment_id IN (" . $assessment_Id . ")";
        $type = "at_risk_user";
        $DTRenderArray = $this->Competency_model->export_rockstars_and_at_risk_users($dtWhere, '', '', $type);
        $amuser_id  = array();
        $x = 0;
        $user_list = [];
        foreach ($DTRenderArray as $far) {
            $amuser_id[] = $far->users_id;
        }
       
        foreach ($DTRenderArray as $rd) {
            $user_list[$x]['User Id'] = $rd->users_id;
            $user_list[$x]['E Code'] = $rd->emp_id;
            $user_list[$x]['Employee name'] = $rd->user_name;
            $user_list[$x]['Division'] = $rd->department;
            $x++;
        }
        $x = 0;
        $user_ai_score = $this->Competency_model->get_ai_score($assessment_Id, $amuser_id);
        $au = array();
        foreach ($user_ai_score as $ai) {
            $au[] = $ai->users_id;
        }
        $as = 0;
        for ($a = 0; $a < count($amuser_id); $a++) {
            if (in_array($amuser_id[$a], $au)) {
                $user_list[$a]['Ai Score'] = $user_ai_score[$as]->ai_score;
            } else {
                $user_list[$a]['Ai Score'] = '0.00';
                $as--;
            }
            $as++;
        }
        $user_manual_score = $this->Competency_model->get_manual_score($assessment_Id, $amuser_id);
        $mu  = array();
        foreach ($user_manual_score as $mi) {
            $mu[] = $mi->users_id;
        }
        $ms = 0;
        for ($m = 0; $m < count($amuser_id); $m++) {
            if (in_array($amuser_id[$m], $mu)) {
                $user_list[$m]['Assessor Rating'] = $user_manual_score[$ms]->manual_score;
            } else {
                $user_list[$m]['Assessor Rating'] = '0.00';
                $ms--;
            }
            $user_list[$m]['Final Score'] = isset($DTRenderArray[$m]->final_score) ? $DTRenderArray[$m]->final_score : '-';

            $ms++;
        }

        $Data_list = $user_list;
        $this->load->library('PHPExcel');
        $objPHPExcel = new Spreadsheet();

        $objPHPExcel->setActiveSheetIndex(0);
        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );
        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
            )
        );
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray_body);
        $i = 1;
        $j = 1;
        $dtDisplayColumns = array_keys($user_list[0]);
        foreach ($dtDisplayColumns as $column) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $column);
            $j++;
        }
        $j = 2;
        foreach ($Data_list as $value) {
            $i = 1;
            foreach ($dtDisplayColumns as $column) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                $i++;
            }
            $j++;
        }
        $file_name = "At Risk (Reps who scored less than 25 %) " . $assessment;
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        if ($assessment != "") {
            header('Content-Disposition: attachment;filename=' . "$file_name.xls");
        } else {
            header('Content-Disposition: attachment;filename="At Risk (Reps who scored less than 25 %).xls"');
        }
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_end_clean();
        $objWriter->save('php://output');
    }

    public function get_top_five_region_data()
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $region_wise_score = $this->Competency_model->get_top_region_score();
        if (count($region_wise_score) > 0) {
            for ($i = 0; $i < count($region_wise_score); $i++) {
                $data .= '
                        <tr>
                        <td >' . $region_wise_score[$i]['region_name'] . '</td>
                        <td style="text-align:center">' . $region_wise_score[$i]['overall_score'] . '</td>
                        </tr>
                        ';
            }
        } else {
            $data .= '<tr>
                        <td colspan="5">No Data Found</td>
                    </tr>';
        }
        $data .= '</table>';
        echo  json_encode($data);
    }
}
