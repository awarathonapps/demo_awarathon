<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports_competency extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('reports_competency');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('Reports_competency_model');
    }

    public function index()
    {
        $data['module_id'] = '90';
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        if ($data['company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
        }

        $data['ThresholdData'] = $this->common_model->get_selected_values('company_result_range', 'id,range_from,range_to,range_color', 'company_id=' . $data['company_id']);
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
        $data['assessment'] = $this->Reports_competency_model->get_all_assessment();
        $this->load->view('reports_competency/index', $data);
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
        $this->load->model('Reports_competency_model');
        $report_data = array();
        $report_title = '';
        $index_label = array();
        $u_count = 0;
        if ($Assessment_id == "") {
            $StartStrDt = date('Y-m-d', strtotime("-30 days"));
            $EndDtdate = date('Y-m-d');
            // $CurrentDate =  date("Y-m-d h:i:s");
            $getname_id_type = $this->Reports_competency_model->LastExpiredAssessment($StartStrDt, $EndDtdate);
            if (isset($getname_id_type) && !empty($getname_id_type)) {
                $Lastassessment_id = array();
                foreach ($getname_id_type as $rg) {
                    $Lastassessment_id[] = $rg['id'];
                }
                $assessment_id = $Lastassessment_id;
                $getscore = $this->Reports_competency_model->getCompetencyscore($assessment_id);
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
                if (isset($getscore) && !empty($getscore)) {
                    for ($i = 0; $i < count($getscore); $i++) {
                        $score[] = $getscore[$i]['overall_score'];
                    }
                    for ($j = 0; $j < count($score); $j++) {
                        if ($score[$j] >= 0.00 and $score[$j] <= 10.50) {
                            $count++;
                        } else if ($score[$j] >= 10.51 and $score[$j] <= 20.50) {
                            $count1++;
                        } else if ($score[$j] >= 20.51 and $score[$j] <= 30.50) {
                            $count2++;
                        } else if ($score[$j] >= 30.51 and $score[$j] <= 40.50) {
                            $count3++;
                        } else if ($score[$j] >= 40.51 and $score[$j] <= 50.50) {
                            $count4++;
                        } else if ($score[$j] >= 50.51 and $score[$j] <= 60.50) {
                            $count5++;
                        } else if ($score[$j] >= 60.51 and $score[$j] <= 70.50) {
                            $count6++;
                        } else if ($score[$j] >= 70.51 and $score[$j] <= 80.50) {
                            $count7++;
                        } else if ($score[$j] >= 80.51 and $score[$j] <= 90.50) {
                            $count8++;
                        } else if ($score[$j] >= 90.51 and $score[$j] <= 100) {
                            $count9++;
                        }
                    }
                    $index_dataset = array(
                        '0' => isset($count) ? $count : '0',
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
                    $u_count = count($index_dataset);
                } else {
                    $u_count = '';
                    $index_dataset[] = 0;
                }
            } else {
                $u_count = '';
                $index_dataset[] = 0;
            }
        } else {
            $getscore = $this->Reports_competency_model->getCompetencyscore($Assessment_id);

            if (isset($getscore) && !empty($getscore)) {
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

                for ($i = 0; $i < count($getscore); $i++) {
                    $score[] = $getscore[$i]['overall_score'];
                }

                for ($j = 0; $j < count($score); $j++) {
                    if ($score[$j] >= 0.00 and $score[$j] <= 10.50) {
                        $count++;
                    } else if ($score[$j] >= 10.51 and $score[$j] <= 20.50) {
                        $count1++;
                    } else if ($score[$j] >= 20.51 and $score[$j] <= 30.50) {
                        $count2++;
                    } else if ($score[$j] >= 30.51 and $score[$j] <= 40.50) {
                        $count3++;
                    } else if ($score[$j] >= 40.51 and $score[$j] <= 50.50) {
                        $count4++;
                    } else if ($score[$j] >= 50.51 and $score[$j] <= 60.50) {
                        $count5++;
                    } else if ($score[$j] >= 60.51 and $score[$j] <= 70.50) {
                        $count6++;
                    } else if ($score[$j] >= 70.51 and $score[$j] <= 80.50) {
                        $count7++;
                    } else if ($score[$j] >= 80.51 and $score[$j] <= 90.50) {
                        $count8++;
                    } else if ($score[$j] >= 90.51 and $score[$j] <= 100) {
                        $count9++;
                    }
                }
                $index_dataset = array(
                    '0' => isset($count) ? $count : '0',
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
                $u_count = count($index_dataset);
            } else {
                $u_count = '';
                $index_dataset[] = 0;
            }
        }
        $data['report'] = $report_data;
        $Rdata['ucount'] = $u_count;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label, JSON_NUMERIC_CHECK);
        $com_under_graph = $this->load->view('reports_competency/competency_understanding_graph', $Rdata, true);
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
        $this->load->model('Reports_competency_model');
        $report_data = array();
        $report_title = '';
        $index_label = array();
        $index_dataset = array();
        $m_count = 0;

        if ($Assessment_id == "") {
            $StartStrDt = date('Y-m-d', strtotime("-30 days"));
            $EndDtdate = date('Y-m-d');
            // $CurrentDate =  date("Y-m-d h:i:s");
            $getassessment = $this->Reports_competency_model->LastExpiredFiveAssessment($StartStrDt, $EndDtdate);

            for ($i = 0; $i < count($getassessment); $i++) {
                $assessment_Id[] = isset($getassessment[$i]['id']) ? $getassessment[$i]['id'] : " ";
            }

            $getassessment_score = $this->Reports_competency_model->performance_comparison_avg($assessment_Id);
            for ($i = 0; $i < count($getassessment_score); $i++) {
                $index_label[] = isset($getassessment_score[$i]['assessment']) ? $getassessment_score[$i]['assessment'] : "Empty Data";
                $index_dataset[] = isset($getassessment_score[$i]['score']) ? $getassessment_score[$i]['score'] : 0;
            }
            $m_count = count($index_label);
        } else {
            $getassessment_score = $this->Reports_competency_model->performance_comparison_avg($Assessment_id);
            for ($i = 0; $i < count($getassessment_score); $i++) {
                $index_label[] = isset($getassessment_score[$i]['assessment']) ? $getassessment_score[$i]['assessment'] : "Empty Data";
                $index_dataset[] = isset($getassessment_score[$i]['score']) ? $getassessment_score[$i]['score'] : 0;
            }
            $m_count = count($index_label);
        }
        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $Rdata['mcount'] = $m_count;
        $com_under_graph = $this->load->view('reports_competency/performance_comparison_graph', $Rdata, true);
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
        $assessment_list = $this->Reports_competency_model->getdepartment($assessmentid);
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
        $assessmentid = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', true) : '';
        $get_id_type = explode(',', $assessmentid);
        $Assessment_id = $get_id_type[0];
        $Report_Type = isset($get_id_type[1]) ? $get_id_type[1] : '';
        $DvisonId_Set = $this->input->post('dvisonid_set', TRUE) != '' ? $this->input->post('dvisonid_set', true) : '';
        $this->load->model('Reports_competency_model');

        $report_title = '';
        $index_label = array();
        $index_dataset = array();
        $index_diff_label = array();
        $final_index_label = array();
        $index_label_arr = array();
        $d_count = 0;
        if ($Assessment_id == "" and $DvisonId_Set == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $Lastexassessment = $this->Reports_competency_model->getLAassessment($CurrentDate);
            if (!empty($Lastexassessment)) {
                $report_title = $Lastexassessment[0]['assessment'];
                $assessment_id = $Lastexassessment[0]['id'];
                $report_type = $Lastexassessment[0]['report_type'];
            } else {
                $report_title = '';
                $assessment_id = '';
                $report_type = '';
            }
            $dvisonId_id = $this->Reports_competency_model->expired_assessment_divison($assessment_id);
            if (empty($dvisonId_id)) {
                $final_index_label[] = '0';
                $index_dataset[] = '0';
            } else {

                for ($i = 0; $i < count($dvisonId_id); $i++) {
                    $dvisonId_set[] = isset($dvisonId_id[$i]['department_name']) ? $dvisonId_id[$i]['department_name'] : "Empty Data";
                }
                $Get_divison_score = $this->Reports_competency_model->Get_score_divison_wise($assessment_id, $report_type, $dvisonId_set, $Company_id);
                for ($a = 0; $a < count($dvisonId_set); $a++) {
                    $index_label[] = isset($Get_divison_score[$a]['department']) ? $Get_divison_score[$a]['department'] : "";
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
                $d_count = count($final_index_label);
            }
        } elseif ($Assessment_id == "" and $DvisonId_Set != "") {
            $Assessment_id = '';
            $Report_Type = '';
            $Get_divison_score = $this->Reports_competency_model->Get_score_divison_wise($Assessment_id, $Report_Type, $DvisonId_Set, $Company_id);
            $report_title = '';
            for ($i = 0; $i < count($DvisonId_Set); $i++) {
                $index_label[] =   isset($Get_divison_score[$i]['department']) ? $Get_divison_score[$i]['department'] : '';
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
            $d_count = count($final_index_label);
        } else {
            if (isset($Assessment_id) && isset($DvisonId_Set)) {
                $Get_divison_score = $this->Reports_competency_model->Get_score_divison_wise($Assessment_id, $Report_Type, $DvisonId_Set, $Company_id);
                $report_title = $Get_divison_score[0]['assessment'];
                for ($i = 0; $i < count($DvisonId_Set); $i++) {
                    // $index_label[] =   isset($Get_divison_score[$i]['department_name']) ? $Get_divison_score[$i]['department_name']: 'Empty Data' ;
                    $index_label[] =   isset($Get_divison_score[$i]['department']) ? $Get_divison_score[$i]['department'] : '';
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
                $d_count = count($final_index_label);
            } else {
                $final_index_label = '';
                $$index_dataset = '';
            }
        }
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($final_index_label);
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['dcount'] = $d_count;
        $per_divsion_graph = $this->load->view('reports_competency/performance_comparison_by_division', $Rdata, true);
        $data['performance_comparison_by_division'] = $per_divsion_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    //end here

    // Competency_by_division Graph Start here
    public function competency_by_division_filter()
    {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $assessment_html = '';
        $assessmentid = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $manager_id = ($this->input->post('manager_id', TRUE) ? $this->input->post('manager_id', TRUE) : 0);
        $assessment_list = $this->Reports_competency_model->get_department($assessmentid, $manager_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value['department'] . '">' . $value['department'] . '</option>';
            }
        }
        $manager = '';
        $manager_list = $this->Reports_competency_model->assessment_wise_manager($assessmentid);
        // print_r($manager_list);exit;
        if (count((array)$manager_list) > 0) {
            foreach ($manager_list as $value) {
                $manager .= '<option value="' . $value->users_id . '"> [' . $value->users_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['division']  = $assessment_html;
        $data['manager']  = $manager;
        $data['manager_select'] = $manager_id;
        echo json_encode($data);
    }

    public function load_division_time_based()
    {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';

        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        if ($IsCustom == "") {
            $start_date = '';
            $CurrentDate = date("Y-m-d");
            $division = $this->Reports_competency_model->get_time_based_div($start_date, $CurrentDate);
        } else if ($IsCustom == "Current Year") {
            $startdate = date('Y-01-01');
            $CurrentDate = date("Y-m-d");
            $division = $this->Reports_competency_model->get_time_based_div($startdate, $CurrentDate);
        } else {
            $division = $this->Reports_competency_model->get_time_based_div($SDate, $EDate);
        }
        if (count((array)$division) > 0) {
            $assessment_html = '';
            foreach ($division as $value) {
                $assessment_html .= '<option value="' . $value->department . '">' . $value->department . '</option>';
            }
        }
        $data['division']  = $assessment_html;
        echo json_encode($data);
    }


    public function competency_by_division($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', true) : '';
        $manager_id_set =  $this->input->post('manager_id', TRUE) != '' ? $this->input->post('manager_id', true) : '';
        $reg_id_set =  $this->input->post('reg_id', TRUE) != '' ? $this->input->post('reg_id', true) : '';
        $division_id_set = $this->input->post('divisonid_set', TRUE) ? $this->input->post('divisonid_set', TRUE) : '';

        $this->load->model('Reports_competency_model');

        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));

        $report_data = array();
        $report_title = '';
        $index_label = array();
        $index_dataset = array();
        $index_diff_label = array();
        $final_index_label = array();
        $index_label_arr = array();
        if ($Assessment_id == "" && $division_id_set == "") {
            if ($IsCustom == "") {
                $start_date = date('Y-m-d', strtotime("-30 days"));
                $CurrentDate = date("Y-m-d");
                $last_ass = $this->Reports_competency_model->get_assessment($start_date, $CurrentDate);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $last_ass = $this->Reports_competency_model->get_assessment($startdate, $CurrentDate);
            } else {
                $last_ass = $this->Reports_competency_model->get_assessment($SDate, $EDate);
            }
            if (isset($last_ass) && !empty($last_ass)) {
                foreach ($last_ass as $k => $last_ass) {
                    $report_title = $last_ass->assessment;
                    $assessment_id[] = $last_ass->id;
                }
                if ($IsCustom == "") {
                    $start_date = date('Y-m-d', strtotime("-30 days"));
                    $CurrentDate = date("Y-m-d");
                    $division_details = $this->Reports_competency_model->assessment_wise_divsion($start_date, $CurrentDate, $assessment_id);
                } else if ($IsCustom == "Current Year") {
                    $startdate = date('Y-01-01');
                    $CurrentDate = date("Y-m-d");
                    $division_details = $this->Reports_competency_model->assessment_wise_divsion($startdate, $CurrentDate, $assessment_id);
                } else {
                    $division_details = $this->Reports_competency_model->assessment_wise_divsion($SDate, $EDate, $assessment_id);
                }
                if (!empty($division_details)) {
                    $division_set = array();
                    foreach ($division_details as $dt) {
                        if ($dt->department_name != '') {
                            $division_set[] = $dt->department_name != '' ? $dt->department_name : '';
                        }
                    }
                    $reg_id_set = '';
                    $manager_id_set = '';
                    if (count($division_set) != 0) {
                        // $ass_id = explode(",", $assessment_id);
                        if ($IsCustom == "") {
                            $start_date = date('Y-m-d', strtotime("-30 days"));
                            $CurrentDate = date("Y-m-d");
                            $Get_divison_score = $this->Reports_competency_model->get_division_data($start_date, $CurrentDate, $assessment_id, $division_set, $reg_id_set, $manager_id_set, $Company_id);
                        } else if ($IsCustom == "Current Year") {
                            $startdate = date('Y-01-01');
                            $CurrentDate = date("Y-m-d");
                            $Get_divison_score = $this->Reports_competency_model->get_division_data($startdate, $CurrentDate, $assessment_id, $division_set, $reg_id_set, $manager_id_set, $Company_id);
                        } else {
                            $Get_divison_score = $this->Reports_competency_model->get_division_data($SDate, $EDate, $assessment_id, $division_set, $reg_id_set, $manager_id_set, $Company_id);
                        }
                        // By Bhautik Rana 12-01-2023 Score accurate 
                        for ($a = 0; $a < count($Get_divison_score); $a++) {
                            $index_label[] = isset($Get_divison_score[$a]['department']) ? $Get_divison_score[$a]['department'] : "";
                            $index_dataset[] = isset($Get_divison_score[$a]['score']) ? $Get_divison_score[$a]['score'] : '0';
                        }
                        // By Bhautik Rana 12-01-2023 Score accurate 
                        for ($k = 0; $k < count($division_set); $k++) {
                            if (in_array($division_set[$k], $index_label)) {
                                continue;
                            } else {
                                $index_diff_label[] = $division_set[$k];
                            }
                        }
                        for ($l = 0; $l < count($index_label); $l++) {
                            if (!empty($index_label[$l])) {
                                $index_label_arr[] = $index_label[$l];
                            }
                        }
                        $final_index_label = array_merge($index_label_arr, $index_diff_label);
                    } else {
                        $index_label_arr = '0';
                        $final_index_label[] = '0';
                        $index_dataset[] = '0';
                    }
                } else {
                    $index_label_arr = '0';
                    $final_index_label[] = '0';
                    $index_dataset[] = '0';
                }
            } else {
                $index_label_arr = '0';
                $final_index_label[] = '0';
                $index_dataset[] = '0';
            }
        } else if ($Assessment_id == "" && $division_id_set != "") {
            $assessment_id = '';
            if ($IsCustom == "") {
                $start_date = '';
                $CurrentDate = '';
                $Get_divison_score = $this->Reports_competency_model->get_division_data($start_date, $CurrentDate, $assessment_id, $division_id_set, $reg_id_set, $manager_id_set, $Company_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $Get_divison_score = $this->Reports_competency_model->get_division_data($startdate, $CurrentDate, $assessment_id, $division_id_set, $reg_id_set, $manager_id_set, $Company_id);
            } else {
                $Get_divison_score = $this->Reports_competency_model->get_division_data($SDate, $EDate, $assessment_id, $division_id_set, $reg_id_set, $manager_id_set, $Company_id);
            }
            if (!empty($Get_divison_score) && isset($Get_divison_score)) {
                $report_title = 'Custom Division';
                // By Bhautik Rana 12-01-2023 Score accurate 
                for ($i = 0; $i < count($division_id_set); $i++) {
                    $index_label[] =   isset($Get_divison_score[$i]['department']) ? $Get_divison_score[$i]['department'] : '';
                    $index_dataset[] = isset($Get_divison_score[$i]['score']) ? $Get_divison_score[$i]['score'] : '0';
                }
                // By Bhautik Rana 12-01-2023 Score accurate 
                for ($k = 0; $k < count($division_id_set); $k++) {
                    if (in_array($division_id_set[$k], $index_label)) {
                        continue;
                    } else {
                        $index_diff_label[] = $division_id_set[$k];
                    }
                }
                for ($l = 0; $l < count($index_label); $l++) {
                    if (!empty($index_label[$l])) {
                        $index_label_arr[] = $index_label[$l];
                    }
                }
                $final_index_label = array_merge($index_label_arr, $index_diff_label);
            } else {
                $index_label_arr = '0';
                $final_index_label[] = '0';
                $index_dataset[] = '0';
            }
        } else {
            if ($IsCustom == "") {
                $start_date = '';
                $CurrentDate = '';
                $Get_divison_score = $this->Reports_competency_model->get_division_data($start_date, $CurrentDate, $Assessment_id, $division_id_set, $reg_id_set, $manager_id_set, $Company_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $Get_divison_score = $this->Reports_competency_model->get_division_data($startdate, $CurrentDate, $Assessment_id, $division_id_set, $reg_id_set, $manager_id_set, $Company_id);
            } else {
                $Get_divison_score = $this->Reports_competency_model->get_division_data($SDate, $EDate, $Assessment_id, $division_id_set, $reg_id_set, $manager_id_set, $Company_id);
            }
            $report_title = '';
            for ($i = 0; $i < count($division_id_set); $i++) {
                // By Bhautik Rana 12-01-2023 Score accurate 
                $index_label[] =   isset($Get_divison_score[$i]['department']) ? $Get_divison_score[$i]['department'] : '';
                $index_dataset[] = isset($Get_divison_score[$i]['score']) ? $Get_divison_score[$i]['score'] : '0';
                // By Bhautik Rana 12-01-2023 Score accurate 
            }
            for ($k = 0; $k < count($division_id_set); $k++) {
                if (in_array($division_id_set[$k], $index_label)) {
                    continue;
                } else {
                    $index_diff_label[] = $division_id_set[$k];
                }
            }
            for ($l = 0; $l < count($index_label); $l++) {
                if (!empty($index_label[$l])) {
                    $index_label_arr[] = $index_label[$l];
                }
            }
            $final_index_label = array_merge($index_label_arr, $index_diff_label);
        }
        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($final_index_label);
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['div_count'] = count(($index_label));
        $competency_graph = $this->load->view('reports_competency/competency_by_div_graph', $Rdata, true);
        $data['competency_graph'] = $competency_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // Competency_by_division end 

    // By Bhautik Rana (02 jan 2023) - new Graph 
    // Competency_by_region start
    public function competency_by_region_filter()
    {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $assessmentid = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $manager_id = ($this->input->post('manager_id', TRUE) ? $this->input->post('manager_id', TRUE) : 0);

        $assessment_html = '';
        $assessment_list = $this->Reports_competency_model->get_region($assessmentid, $manager_id, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '"> [' . $value->region_id . '] - ' . $value->region_name . '</option>';
            }
        }
        $manager = '';
        $manager_list = $this->Reports_competency_model->assessment_wise_manager($assessmentid);
        if (count((array)$manager_list) > 0) {
            foreach ($manager_list as $value) {
                $manager .= '<option value="' . $value->users_id . '"> [' . $value->users_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['region']  = $assessment_html;
        $data['manager']  = $manager;
        $data['manager_select'] = $manager_id;
        echo json_encode($data);
    }

    public function load_region_time_based()
    {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';

        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        if ($IsCustom == "") {
            $start_date = '';
            $CurrentDate = date("Y-m-d");
            $region = $this->Reports_competency_model->get_time_based_region($start_date, $CurrentDate);
        } else if ($IsCustom == "Current Year") {
            $startdate = date('Y-01-01');
            $CurrentDate = date("Y-m-d");
            $region = $this->Reports_competency_model->get_time_based_region($startdate, $CurrentDate);
        } else {
            $region = $this->Reports_competency_model->get_time_based_region($SDate, $EDate);
        }
        if (count((array)$region) > 0) {
            $assessment_html = '';
            foreach ($region as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '">' . $value->region_name . '</option>';
            }
        }
        $data['region']  = $assessment_html;
        echo json_encode($data);
    }
    public function competency_by_region($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', true) : '';
        $reg_id_set =  $this->input->post('reg_id', TRUE) != '' ? $this->input->post('reg_id', true) : '';
        $manager_id_set =  $this->input->post('manager_id', TRUE) != '' ? $this->input->post('manager_id', true) : '';

        $this->load->model('Reports_competency_model');

        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));


        $index_label = array();
        $index_dataset = array();
        $index_diff_label = array();
        if ($Assessment_id == "" && $reg_id_set == "") {
            if ($IsCustom == "") {
                $start_date = date('Y-m-d', strtotime("-30 days"));
                $CurrentDate = date("Y-m-d");
                $last_ass = $this->Reports_competency_model->get_assessment($start_date, $CurrentDate);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $last_ass = $this->Reports_competency_model->get_assessment($startdate, $CurrentDate);
            } else {
                $last_ass = $this->Reports_competency_model->get_assessment($SDate, $EDate);
            }
            if (isset($last_ass) && !empty($last_ass)) {
                foreach ($last_ass as $k => $last_ass) {
                    $report_title = $last_ass->assessment;
                    $assessment_id[] = $last_ass->id;
                }
                if ($IsCustom == "") {
                    $start_date = date('Y-m-d', strtotime("-30 days"));
                    $CurrentDate = date("Y-m-d");
                    $region_details = $this->Reports_competency_model->assessment_based_region($start_date, $CurrentDate, $assessment_id);
                } else if ($IsCustom == "Current Year") {
                    $startdate = date('Y-01-01');
                    $CurrentDate = date("Y-m-d");
                    $region_details = $this->Reports_competency_model->assessment_based_region($startdate, $CurrentDate, $assessment_id);
                } else {
                    $region_details = $this->Reports_competency_model->assessment_based_region($SDate, $EDate, $assessment_id);
                }
                if (!empty($region_details)) {
                    $region_set = array();
                    foreach ($region_details as $dt) {
                        if ($dt->region_name != '') {
                            $region_id[] = $dt->region_id != '' ? $dt->region_id : '';
                            $region_set[] = $dt->region_name != '' ? $dt->region_name : '';
                        }
                    }
                    $manager_id_set = '';
                    if (count($region_id) != 0) {
                        // $ass_id = explode(",", $assessment_id);
                        if ($IsCustom == "") {
                            $start_date = date('Y-m-d', strtotime("-30 days"));
                            $CurrentDate = date("Y-m-d");
                            $get_region_score = $this->Reports_competency_model->get_region_data($start_date, $CurrentDate, $assessment_id, $region_id, $manager_id_set, $Company_id);
                        } else if ($IsCustom == "Current Year") {
                            $startdate = date('Y-01-01');
                            $CurrentDate = date("Y-m-d");
                            $get_region_score = $this->Reports_competency_model->get_region_data($startdate, $CurrentDate, $assessment_id,  $region_id, $manager_id_set, $Company_id);
                        } else {
                            $get_region_score = $this->Reports_competency_model->get_region_data($SDate, $EDate, $assessment_id, $region_id, $manager_id_set, $Company_id);
                        }
                        if (!empty($get_region_score)) {
                            foreach ($get_region_score as $gs) {
                                $index_label[] = isset($gs->region_name) ? $gs->region_name : '';
                                $index_dataset[] = isset($gs->score) ? $gs->score : '0';
                            }
                        } else {
                            $index_label = '';
                            $index_dataset = 0;
                        }
                    } else {
                        $index_label = '';
                        $index_dataset = 0;
                    }
                } else {
                    $index_label = '';
                    $index_dataset = 0;
                }
            } else {
                $index_label = '';
                $index_dataset = 0;
            }
        } else if ($Assessment_id == "" && $reg_id_set != "") {
            $ass_id = '';
            if ($IsCustom == "") {
                $start_date = '';
                $CurrentDate = '';
                $get_region_score = $this->Reports_competency_model->get_region_data($start_date, $CurrentDate, $ass_id, $reg_id_set, $manager_id_set, $Company_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $get_region_score = $this->Reports_competency_model->get_region_data($startdate, $CurrentDate, $ass_id, $reg_id_set, $manager_id_set, $Company_id);
            } else {
                $get_region_score = $this->Reports_competency_model->get_region_data($SDate, $EDate, $ass_id, $reg_id_set, $manager_id_set, $Company_id);
            }
            if (!empty($get_region_score)) {
                $region_name_list = array();
                foreach ($get_region_score as $gs) {
                    $region_list[] = isset($gs->region) ? $gs->region : '';
                    $region_name_list[] = isset($gs->region_name) ? $gs->region_name : '';
                    $region_score[] = isset($gs->score) ? $gs->score : '0';
                }
                if (!empty($reg_id_set)) {
                    $get_region = $this->Reports_competency_model->get_region_name($reg_id_set);
                    foreach ($get_region as $rg) {
                        if (in_array($rg->region_name, $region_name_list)) {
                            continue;
                        } else {
                            $index_diff_label[] = $rg->region_name;
                        }
                    }
                }
                $index_label = array_merge($region_name_list, $index_diff_label);
                $i = 0;
                foreach ($index_label as $final) {
                    if (isset($region_name_list[$i])) {
                        if (in_array($region_name_list[$i], $index_label)) {
                            $index_dataset[] = $region_score[$i];
                        } else {
                            $index_dataset[] = '0';
                        }
                    } else {
                        $index_dataset[] = '0';
                    }

                    $i++;
                }
            } else {
                $index_label = '';
                $index_dataset = 0;
            }
        } else {
            if ($IsCustom == "") {
                $start_date = '';
                $CurrentDate = '';
                $get_region_score = $this->Reports_competency_model->get_region_data($start_date, $CurrentDate, $Assessment_id, $reg_id_set, $manager_id_set, $Company_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $get_region_score = $this->Reports_competency_model->get_region_data($startdate, $CurrentDate, $Assessment_id, $reg_id_set, $manager_id_set, $Company_id);
            } else {
                $get_region_score = $this->Reports_competency_model->get_region_data($SDate, $EDate, $Assessment_id, $reg_id_set, $manager_id_set, $Company_id);
            }
            if (!empty($get_region_score)) {
                $region_name_list = array();
                foreach ($get_region_score as $gs) {
                    $region_list[] = isset($gs->region) ? $gs->region : '';
                    $region_name_list[] = isset($gs->region_name) ? $gs->region_name : '';
                    $region_score[] = isset($gs->score) ? $gs->score : '0';
                }
                if (!empty($reg_id_set)) {
                    $region_name = array();
                    $get_region = $this->Reports_competency_model->get_region_name($reg_id_set);
                    foreach ($get_region as $rg) {
                        if (in_array($rg->region_name, $region_name_list)) {
                            continue;
                        } else {
                            $index_diff_label[] = $rg->region_name;
                        }
                    }
                }
                // new code
                $index_label = array_merge($region_name_list, $index_diff_label);
                $i = 0;

                foreach ($index_label as $final) {
                    if (isset($region_name_list[$i])) {

                        if (in_array($region_name_list[$i], $index_label)) {
                            $index_dataset[] = $region_score[$i];
                        } else {
                            $index_dataset[] = 0;
                        }
                    } else {
                        $index_dataset[] = 0;
                    }

                    $i++;
                }

                // new code
            } else {
                $index_label = '';
                $index_dataset = '0';
            }
        }
        $Rdata['index_label'] = json_encode($index_label);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['report_title'] = '';
        $Rdata['div_count'] =  count((array)$index_label);
        $competency_graph = $this->load->view('reports_competency/competency_by_region_graph', $Rdata, true);
        $data['competency_graph'] = $competency_graph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // Competency_by_region end 
    // By Bhautik Rana (02 jan 2023) - new Graph 

    // Performance comparison by Region graph start here
    public function assessment_wise_region()
    {
        $assessment_html = '';
        $Company_id = $this->mw_session['company_id'];
        $assessmentid = $this->input->post('assessmentid', TRUE);
        $get_type_id = explode(',', $assessmentid);
        $assessment_id = ($get_type_id[0]  != '' ? $get_type_id[0] : 0);
        $assessment_list = $this->Reports_competency_model->assessment_wise_region($assessment_id, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '">' . $value->region_name . '</option>';
            }
        }
        $data['region']  = $assessment_html;
        echo json_encode($data);
    }
    public function am_wise_region()
    {
        $region_data = '';
        $Company_id = $this->mw_session['company_id'];
        $assessmentid =  $this->input->post('assessmentid', TRUE) != '' ? $this->input->post('assessmentid', TRUE) : '0';
        $assessment_list = $this->Reports_competency_model->am_wise_region($assessmentid, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $region_data .= '<option value="' . $value->region_id . '">' . $value->region_name . '</option>';
            }
        }
        $data['rg_id']  = $region_data;
        echo json_encode($data);
    }


    public function performance_comparison_by_region($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $am_id = $this->input->post('assessment_id', TRUE);

        $get_type_id = explode(',', $am_id);
        $Assessment_id = isset($get_type_id[0]) ? $get_type_id[0] : '';
        $Report_Type = isset($get_type_id[1]) ? $get_type_id[1] : '';
        $Region_id = $this->input->post('region_id', TRUE);
        $this->load->model('Reports_competency_model');
        $R_count = 0;

        $report_data = array();
        $report_title = '';
        $index_label = array();
        $index_dataset = array();
        if ($Assessment_id == "" and $Region_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $Lastexassessment = $this->Reports_competency_model->LAassessment_and_type($CurrentDate);
            $report_title =  isset($Lastexassessment[0]['assessment'])  ? $Lastexassessment[0]['assessment']  : '';
            $assessment_id = isset($Lastexassessment[0]['id']) ? $Lastexassessment[0]['id']  : '';
            $report_type = isset($Lastexassessment[0]['report_type'])  ? $Lastexassessment[0]['report_type']  : '';
            if (isset($assessment_id) && !empty($assessment_id)) {
                $region_id_arr = $this->Reports_competency_model->expired_assessment_region($assessment_id);
                $region_id = array();
                foreach ($region_id_arr as $rg) {
                    $region_id[] = $rg['region_id'];
                }
                $Region_id = $region_id;
                if (isset($Region_id) && !empty($Region_id)) {
                    $Get_region_score = $this->Reports_competency_model->Get_score_region_wise($assessment_id, $report_type, $Region_id);
                    if (isset($Get_region_score) && !empty($Get_region_score)) {
                        for ($a = 0; $a < count($Get_region_score); $a++) {
                            $index_label[] = isset($Get_region_score[$a]['region_name']) ? $Get_region_score[$a]['region_name'] : "";
                            $index_dataset[] = isset($Get_region_score[$a]['score']) ? $Get_region_score[$a]['score'] : '0';
                        }
                        $R_count = count($index_label);
                    } else {
                        $index_label[] = '';
                        $index_dataset[] = '';
                        $R_count = '';
                    }
                } else {
                    $index_label[] = '';
                    $index_dataset[] = '';
                    $R_count = '';
                }
            } else {
                $index_label[] = '';
                $index_dataset[] = '';
                $R_count = '';
            }
        } elseif ($Assessment_id != "" && $Region_id == '') {

            $region_id_arr = $this->Reports_competency_model->expired_assessment_region($Assessment_id);
            $region_id = array();
            foreach ($region_id_arr as $rg) {
                $region_id[] = $rg['region_id'];
            }
            $Region_id = $region_id;
            if (isset($Region_id) && !empty($Region_id)) {
                $Get_region_score = $this->Reports_competency_model->Get_score_region_wise($Assessment_id, $Report_Type, $Region_id);
                if (isset($Get_region_score) && !empty($Get_region_score)) {
                    for ($a = 0; $a < count($Get_region_score); $a++) {
                        $index_label[] = isset($Get_region_score[$a]['region_name']) ? $Get_region_score[$a]['region_name'] : "";
                        $index_dataset[] = isset($Get_region_score[$a]['score']) ? $Get_region_score[$a]['score'] : '0';
                    }
                    $R_count = count($index_label);
                } else {
                    $R_count = '';
                    $index_label[] = '';
                    $index_dataset[] = '';
                }
            } else {
                $R_count = '';
                $index_label[] = '';
                $index_dataset[] = '';
            }
        } else {
            $Get_region_score = $this->Reports_competency_model->Get_score_region_wise($Assessment_id, $Report_Type, $Region_id);
            if (isset($Get_region_score) && !empty($Get_region_score)) {
                $report_title = $Get_region_score[0]['assessment'];

                for ($i = 0; $i < count($Get_region_score); $i++) {
                    $index_label[] =   isset($Get_region_score[$i]['region_name']) ? $Get_region_score[$i]['region_name'] : '';
                    $index_dataset[] = isset($Get_region_score[$i]['score']) ? $Get_region_score[$i]['score'] : '0';
                }
                $R_count = count($index_label);
            } else {
                $R_count = '';
                $index_label[] = '';
                $index_dataset[] = '';
            }
        }
        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['Rcount'] = $R_count;
        $per_divsion_graph = $this->load->view('reports_competency/performance_comparison_by_region', $Rdata, true);
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
        $assessment_id = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', TRUE) : '';
        $region_id = $this->input->post('region_id', TRUE) != '' ? $this->input->post('region_id', TRUE) : '';

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
        if (isset($assessment_id) && !empty($assessment_id) && isset($region_id) && !empty($region_id)) {
            $region_wise_score = $this->Reports_competency_model->get_region_score($assessment_id, $region_id, $above_range, $second_range_from, $second_range_to, $third_range_from, $third_range_to, $less_range);
            if (isset($region_wise_score) && !empty($region_wise_score)) {

                $region_data = $this->Reports_competency_model->get_exipired_assesment_region($assessment_id, $Company_id);
                $region_id = array();
                $region_name = array();
                foreach ($region_data as $rg) {
                    $region_id[] = $rg['region_id'];
                    $region_name[] = $rg['region_name'];
                }
                for ($i = 0; $i < count($region_wise_score); $i++) {
                    $index_label[] = isset($region_wise_score) ? $region_wise_score[$i]['region_name'] : $region_name[$i];
                    $less_than_range[] = isset($region_wise_score[$i]['less_' . $less_range . '']) ? $region_wise_score[$i]['less_' . $less_range . ''] : 0;
                    $second_range[] = isset($region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . '']) ? $region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . ''] : 0;
                    $third_range[] = isset($region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . '']) ? $region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . ''] : 0;
                    $above_range_final[] = isset($region_wise_score[$i]['above_' . $above_range . '']) ? $region_wise_score[$i]['above_' . $above_range . ''] : 0;
                }
                $N_count = count($index_label);
            } else {
                $index_label[] = 0;
                $less_than_range[] =  0;
                $second_range[] =  0;
                $third_range[] = 0;
                $above_range_final[] =  0;
                $N_count = '';
            }
        } else {
            $start_date =  date('Y-m-d', strtotime("-30 days"));
            $CurrentDate =  date("Y-m-d");
            $Lastexassessment = $this->Reports_competency_model->lastassessment($start_date, $CurrentDate);
            if (isset($Lastexassessment) && !empty($Lastexassessment)) {
                $lastAssessmentId = array();
                foreach ($Lastexassessment as $rld) {
                    $lastAssessmentId[] = $rld['id'];
                }
                $assessment_id = $lastAssessmentId;
                $region_data = $this->Reports_competency_model->get_exipired_assesment_region($assessment_id, $Company_id);
                $region_id = array();
                $region_name = array();
                foreach ($region_data as $rg) {
                    $region_id[] = $rg['region_id'];
                    $region_name[] = $rg['region_name'];
                }
                $region_wise_score = $this->Reports_competency_model->get_region_score($assessment_id, $region_id, $above_range, $second_range_from, $second_range_to, $third_range_from, $third_range_to, $less_range);
                if (isset($region_wise_score) && !empty($region_wise_score)) {
                    for ($i = 0; $i < count($region_wise_score); $i++) {
                        $index_label[] = isset($region_wise_score) ? $region_wise_score[$i]['region_name'] : $region_name[$i];
                        $less_than_range[] = isset($region_wise_score[$i]['less_' . $less_range . '']) ? $region_wise_score[$i]['less_' . $less_range . ''] : '0';
                        $second_range[] = isset($region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . '']) ? $region_wise_score[$i]['score_' . $second_range_from . '_' . $second_range_to . ''] : '0';
                        $third_range[] = isset($region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . '']) ? $region_wise_score[$i]['score_' . $third_range_from . '_' . $third_range_to . ''] : '0';
                        $above_range_final[] = isset($region_wise_score[$i]['above_' . $above_range . '']) ? $region_wise_score[$i]['above_' . $above_range . ''] : 0;
                    }
                    $N_count = count($index_label);
                } else {
                    $index_label[] = 0;
                    $less_than_range[] =  0;
                    $second_range[] =  0;
                    $third_range[] = 0;
                    $above_range_final[] =  0;
                    $N_count = '';
                }
            } else {
                $index_label[] = 0;
                $less_than_range[] =  0;
                $second_range[] =  0;
                $third_range[] = 0;
                $above_range_final[] =  0;
                $N_count = '';
            }
        }
        $range_list = array('less than ' . $less_range, $second_range_to . ' to ' . $second_range_from, $third_range_to . ' to ' . $third_range_from, 'above ' . $above_range . '');
        $Rdata['range_list'] = json_encode($range_list);
        $Rdata['index_label'] = json_encode($index_label);
        // $Rdata['report_title'] = json_encode($assessment_name);
        $Rdata['Ncount'] = $N_count;
        $Rdata['less_than_range'] = json_encode($less_than_range, JSON_NUMERIC_CHECK);
        $Rdata['second_range'] = json_encode($second_range, JSON_NUMERIC_CHECK);
        $Rdata['third_range'] = json_encode($third_range, JSON_NUMERIC_CHECK);
        $Rdata['above_range_final'] = json_encode($above_range_final, JSON_NUMERIC_CHECK);
        $region_performace = $this->load->view('reports_competency/region_performance', $Rdata, true);
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
        $assessment_id = $this->input->get('assessment_id', true) != '' ? $this->input->get('assessment_id', true) : '';
        if ($assessment_id == "") {
            $CurrentDate =  date("Y-m-d h:i:s");
            $get_assessment_id = $this->Reports_competency_model->all_assessment($CurrentDate);
            if (isset($get_assessment_id) && !empty($get_assessment_id)) {
                $lastAssessmentId = array();
                foreach ($get_assessment_id as $rld) {
                    $lastAssessmentId[] = $rld['id'];
                }
                $assessment_id = implode("', '", $lastAssessmentId);
            } else {
                $assessment_id = '';
            }
        }
        if ($assessment_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.assessment_id  in ('" . $assessment_id . "')";
            } else {
                $dtWhere .= " AND a.assessment_id in ('" . $assessment_id . "')";
            }
            $dtWhere1 = '';
            $user_final_scores = $this->Reports_competency_model->get_rockstars_user_final_score($company_id, $dtWhere, $dtWhere1, $dtLimit);
            // $user_final_scores = $this->Reports_competency_model->get_rockstars_user_final_score($company_id, $dtWhere,$assessment_id, $dtOrder, $dtLimit);

            if (!empty($user_final_scores)) {

                $user_list = [];
                $x = 0;
                foreach ($user_final_scores as $ud) {
                    $user_list[$x]['user_id'] = $ud->emp_id;
                    $amuser_id = $ud->users_id;
                    $am_id = $ud->assessment_id;
                    $user_list[$x]['user_name'] = $ud->user_name;
                    $user_list[$x]['division'] = isset($ud->department) ? $ud->department : '-';

                    $user_ai_score = $this->Reports_competency_model->get_ai_score($am_id, $amuser_id);

                    $ai_user_score = 0;
                    if (isset($user_ai_score) and count((array)$user_ai_score) > 0) {
                        $ai_user_score = $user_ai_score->ai_score;
                    }
                    if ($ai_user_score == "0") {
                        $user_list[$x]['ai_score'] = '-';
                    } else {
                        $user_list[$x]['ai_score'] = $ai_user_score;
                    }
                    $manual_overall_score = 0;
                    $user_manual_score = $this->Reports_competency_model->get_manual_score($am_id, $amuser_id);
                    if (isset($user_manual_score) and count((array)$user_manual_score) > 0) {
                        $manual_overall_score = $user_manual_score->manual_score;
                    }

                    if ($manual_overall_score == "0") {
                        $user_list[$x]['manual_score'] = '-';
                    } else {
                        $user_list[$x]['manual_score'] = $manual_overall_score;
                    }
                    $user_list[$x]['fianl_score'] = isset($user_final_scores[$x]->final_score) ? $user_final_scores[$x]->final_score : '-';
                    $x++;
                }
            } else {
                $user_list[] = "";
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
        $output['title'] = '';
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
            $get_assessment_id = $this->Reports_competency_model->all_assessment($CurrentDate);
            $lastAssessmentId = array();
            foreach ($get_assessment_id as $rld) {
                $lastAssessmentId[] = $rld['id'];
            }
            $assessment_Id = implode("', '", $lastAssessmentId);
        }
        // $assessment_name =  $this->Reports_competency_model->assessment_name($assessment_Id);

        // $assessment = $assessment_name[0]['assessment'];

        $dtWhere = ' WHERE 1=1 ';

        $dtWhere .= " AND a.assessment_id IN ('" . $assessment_Id . "')";

        $type = "rockstars_users";
        $DTRenderArray = $this->Reports_competency_model->export_rockstars_and_at_risk_users($dtWhere, '', '', $type);
        $x = 0;
        $user_list = [];
        foreach ($DTRenderArray as $ud) {

            $amuser_id = $ud->users_id;
            $am_id = $ud->assessment_id;
            $user_list[$x]['User Id'] = $ud->users_id;
            $user_list[$x]['E Code'] = $ud->emp_id;
            $user_list[$x]['Employee name'] = $ud->user_name;
            $user_list[$x]['Division'] = isset($ud->department) ? $ud->department : '-';
            // user Ai Score
            $ai_user_score = 0;
            $user_ai_score = $this->Reports_competency_model->get_ai_score($am_id, $amuser_id);

            if (isset($user_ai_score) and count((array)$user_ai_score) > 0) {
                $ai_user_score = $user_ai_score->ai_score;
            }
            if ($ai_user_score == "0") {
                $user_list[$x]['Ai Score'] = '-';
            } else {
                $user_list[$x]['Ai Score'] = $ai_user_score;
            }

            //user Manual score
            $manual_overall_score = 0;
            $user_manual_score = $this->Reports_competency_model->get_manual_score($am_id, $amuser_id);
            if (isset($user_manual_score) and count((array)$user_manual_score) > 0) {
                $manual_overall_score = $user_manual_score->manual_score;
            }

            if ($manual_overall_score == "0") {
                $user_list[$x]['Assessor Rating'] = '-';
            } else {
                $user_list[$x]['Assessor Rating'] = $manual_overall_score;
            }

            $user_list[$x]['Final Score'] = isset($DTRenderArray[$x]->final_score) ? $DTRenderArray[$x]->final_score : '-';
            $x++;
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
        // $file_name = "Rockstars (Reps who scored more than 85 %) ";
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename="Rockstars (Reps who scored more than 85 %).xls"');

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
            $get_assessment_id = $this->Reports_competency_model->all_assessment($CurrentDate);
            if (isset($get_assessment_id) && !empty($get_assessment_id)) {
                $lastAssessmentId = array();
                foreach ($get_assessment_id as $rld) {
                    $lastAssessmentId[] = $rld['id'];
                }
                $assessment_id = implode("', '", $lastAssessmentId);
            } else {
                $assessment_id = '';
            }
        }
        if ($assessment_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.assessment_id in ('" . $assessment_id . "')";
            } else {
                $dtWhere .= " AND a.assessment_id in ('" . $assessment_id . "')";
            }
            $dtWhere1 = '';
            $user_final_scores = $this->Reports_competency_model->At_risk_users_final_score($company_id, $dtWhere, $dtWhere1, $dtLimit);

            if (!empty($user_final_scores)) {

                $user_list = [];
                $x = 0;
                foreach ($user_final_scores as $ud) {
                    $user_list[$x]['user_id'] = $ud->emp_id;
                    $amuser_id = $ud->users_id;
                    $user_list[$x]['user_name'] = $ud->user_name;
                    $user_list[$x]['division'] = isset($ud->department) ? $ud->department : '-';
                    $am_id = $ud->assessment_id;
                    $user_ai_score = $this->Reports_competency_model->get_ai_score($am_id, $amuser_id);
                    $ai_user_score = 0;
                    if (isset($user_ai_score) and count((array)$user_ai_score) > 0) {
                        $ai_user_score = $user_ai_score->ai_score;
                    }
                    if ($ai_user_score == "0") {
                        $user_list[$x]['ai_score'] = '-';
                    } else {
                        $user_list[$x]['ai_score'] = $ai_user_score;
                    }
                    $manual_overall_score = 0;
                    $user_manual_score = $this->Reports_competency_model->get_manual_score($am_id, $amuser_id);

                    if (isset($user_manual_score) and count((array)$user_manual_score) > 0) {
                        $manual_overall_score = $user_manual_score->manual_score;
                    }

                    if ($manual_overall_score == "0") {
                        $user_list[$x]['manual_score'] = '-';
                    } else {
                        $user_list[$x]['manual_score'] = $manual_overall_score;
                    }
                    $user_list[$x]['fianl_score'] = isset($user_final_scores[$x]->final_score) ? $user_final_scores[$x]->final_score : '-';
                    $x++;
                }
            } else {
                $user_list[] = "";
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
            $get_assessment_id = $this->Reports_competency_model->all_assessment($CurrentDate);
            $assessment_Id = $get_assessment_id[0]['id'];
        }
        $assessment_name =  $this->Reports_competency_model->assessment_name($assessment_Id);
        $assessment = $assessment_name[0]['assessment'];

        $dtWhere = ' WHERE 1=1 ';

        $dtWhere .= " AND a.assessment_id IN ('" . $assessment_Id . "')";
        $type = "at_risk_user";
        $DTRenderArray = $this->Reports_competency_model->export_rockstars_and_at_risk_users($dtWhere, '', '', $type);
        $x = 0;
        $user_list = [];
        foreach ($DTRenderArray as $ud) {

            $amuser_id = $ud->users_id;
            $user_list[$x]['User Id'] = $ud->users_id;
            $user_list[$x]['E Code'] = $ud->emp_id;
            $user_list[$x]['Employee name'] = $ud->user_name;
            $user_list[$x]['Division'] = $ud->department;
            // user Ai Score
            $ai_user_score = 0;
            $user_ai_score = $this->Reports_competency_model->get_ai_score($assessment_Id, $amuser_id);

            if (isset($user_ai_score) and count((array)$user_ai_score) > 0) {
                $ai_user_score = $user_ai_score->ai_score;
            }
            if ($ai_user_score == "0") {
                $user_list[$x]['Ai Score'] = '-';
            } else {
                $user_list[$x]['Ai Score'] = $ai_user_score;
            }

            //user Manual score
            $manual_overall_score = 0;
            $user_manual_score = $this->Reports_competency_model->get_manual_score($assessment_Id, $amuser_id);
            if (isset($user_manual_score) and count((array)$user_manual_score) > 0) {
                $manual_overall_score = $user_manual_score->manual_score;
            }

            if ($manual_overall_score == "0") {
                $user_list[$x]['Assessor Rating'] = '-';
            } else {
                $user_list[$x]['Assessor Rating'] = $manual_overall_score;
            }

            $user_list[$x]['Final Score'] = isset($DTRenderArray[$x]->final_score) ? $DTRenderArray[$x]->final_score : '-';
            $x++;
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
        $data = '';
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $region_wise_score = $this->Reports_competency_model->get_top_region_score();
        if (count($region_wise_score) > 0) {
            for ($i = 0; $i < count($region_wise_score); $i++) {
                $data .= '<tr><td>' . $region_wise_score[$i]['region_name'] . '</td><td style="text-align:center">' . $region_wise_score[$i]['overall_score'] . '</td></tr>';
            }
        } else {
            $data .= '<tr><td colspan="5">No Data Found</td></tr>';
        }
        $data .= '</table>';
        echo  json_encode($data);
    }
    public function get_bottom_five_region_data()
    {
        $data = "";
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }

        $region_wise_score = $this->Reports_competency_model->get_bottom_region_score();

        if (count(array($region_wise_score)) > 0) {

            foreach ($region_wise_score as $rows) {
                $region_name = $rows['region_name'];
                $overall_score = $rows['overall_score'];
                $data .= '
                        <tr>
                        <td >' . $region_name . '</td>
                        <td style="text-align:center">' . $overall_score . '</td>
                        </tr>
                        ';
            }
        } else {
            $data .= '<tr>
                        <td colspan="5">No Data Found</td>
                    </tr>';
        }
        // $data .= '</table>';
        echo json_encode($data);
    }
    // Manager_wise_understanding Graph
    public function assessment_wise_manager()
    {
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $Company_id =  $this->input->post('company_id', TRUE);

        $assessment_list = $this->Reports_competency_model->assessment_wise_manager($assessment_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->users_id . '">[' . $value->users_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['assessment_list_data']  = $assessment_html;
        echo json_encode($data);
    }

    public function get_manager_data()
    {
        $assessment_id =  $this->input->get('assessment_id', true);
        $manager_id = $this->input->get('all_managers', true);
        $start_date = $this->input->get('StartDate', true);
        $end_date =   $this->input->get('EndDate', true);
        $is_custom =  $this->input->get('IsCustom', true);
        $company_id = $this->mw_session['company_id'];
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        // echo '<pre>';
        // print_r($this->input->get());exit;
        $manager_details = '';
        $user_list[] = "";
        if ($assessment_id == '' && $manager_id == '') {
            // Time filter
            if ($is_custom == "") {
                $start_date = date('Y-m-d', strtotime("-30 days"));
                $current_date =  date("Y-m-d h:i:s");
                $last_assessment_array = $this->Reports_competency_model->get_assessment($start_date, $current_date);
            } else if ($is_custom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $last_assessment_array = $this->Reports_competency_model->get_assessment($startdate, $CurrentDate);
            } else {
                $last_assessment_array = $this->Reports_competency_model->get_assessment($SDate, $EDate);
            }
            if (count((array)$last_assessment_array) > 0) {
                foreach ($last_assessment_array as $k => $rld) {
                    $assessmentid[] = $rld->id;
                }
                $manager_details = $this->Reports_competency_model->get_manager_details($company_id, $assessmentid);
                if (count((array)$manager_details) > 0) {
                    foreach ($manager_details as $md) {
                        $managerid[] = $md->manager_id;
                    }
                    $manager_id = implode(',', $managerid);
                }
            }
        } elseif ($assessment_id != '' && $manager_id == '') {

            $assessmentid = explode(',', $assessment_id);
            $manager_details = $this->Reports_competency_model->get_manager_details($company_id, $assessmentid);
            if (count((array)$manager_details) > 0) {
                foreach ($manager_details as $md) {
                    $managerid[] = $md->manager_id;
                }
                $manager_id = implode(',', $managerid);
            }
        }
        if ($assessment_id != '' or $manager_id != '') {
            if ($is_custom == "") {
                $start_date = date('Y-m-d', strtotime("-30 days"));
                $current_date =  date("Y-m-d");
                $get_reps_percent = $this->Reports_competency_model->get_reps_percent_manager_wise($manager_id, $start_date, $current_date, $assessment_id);
            } else if ($is_custom == "Current Year") {
                $startdate = date('Y-01-01');
                $current_date = date("Y-m-d");
                $get_reps_percent = $this->Reports_competency_model->get_reps_percent_manager_wise($manager_id, $startdate, $current_date, $assessment_id);
            } else {
                $get_reps_percent = $this->Reports_competency_model->get_reps_percent_manager_wise($manager_id, $SDate, $EDate, $assessment_id);
            }
            $total_reps = array();
            $complete_percent = array();
            $new_manager_id = array();
            $manager_name = array();
            $complete_cnt = array();

            if (count((array)$get_reps_percent) > 0) {
                foreach ($get_reps_percent as $grp) {
                    $new_manager_id[] = $grp->trainer_id;
                    $manager_name[] = $grp->manager_name;
                    $total_reps[] = $grp->total_reps;
                    $complete_percent[] = $grp->percetage;
                    $complete_cnt[] = $grp->completed;
                }
                if ($is_custom == "") {
                    $start_date = '';
                    $current_date = '';
                    $get_avg_accuracy = $this->Reports_competency_model->get_avg_accuracy($company_id, $new_manager_id, $start_date, $current_date, $assessment_id);
                } else if ($is_custom == "Current Year") {
                    $startdate = date('Y-01-01');
                    $current_date = date("Y-m-d");
                    $get_avg_accuracy = $this->Reports_competency_model->get_avg_accuracy($company_id, $new_manager_id, $startdate, $current_date, $assessment_id);
                } else {
                    $get_avg_accuracy = $this->Reports_competency_model->get_avg_accuracy($company_id, $new_manager_id, $SDate, $EDate, $assessment_id);
                }
                $new_avg_accuracy = array();
                foreach ($get_avg_accuracy as $ag) {
                    $new_avg_accuracy[$ag['trainer_id']] = $ag['overall_score'];
                }
            }
        }
        if (isset($new_manager_id) && !empty($new_manager_id)) {
            $x = 0;
            $base_url = base_url();
            $is_custom_new = str_replace(' ', '_', $is_custom);
            if ($assessment_id == '') {
                $assessment_id = '0';
            }
            $user_list = [];
            //  echo '<pre>';
            //  print_r($new_manager_id);exit;
            for ($i = 0; $i < count((array)$new_manager_id); $i++) {
                $user_list[$x]['manager_id'] = $new_manager_id[$i];
                $user_list[$x]['manager_name'] = $manager_name[$i];
                $modal_link = '<a id="user_data"  href="' . $base_url . 'reports_competency/user_data_modal/' . $assessment_id . '/' . $new_manager_id[$i] . '/' . $SDate . '/' . $EDate . '/' . $is_custom_new . '" 
                data-target="#user_data_modal" data-toggle="modal">' . $total_reps[$i] . '</a>';
                $user_list[$x]['reps'] = $modal_link;
                $assessment_html = '<div class="progress" style="margin-top: 10px;margin-bottom: 0px;">';
                if ($complete_cnt[$i] == 0) {
                    $assessment_html .= '<div  style="color:black; text-align:center;">Not Started</div>';
                } else {
                    $assessment_html .= '<div class="progress-bar" role="progressbar" style="width:' . $complete_percent[$i] . '%; margin-bottom: 5px; background-color: #004369;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" >' . $complete_percent[$i] . '</div>';
                }
                $assessment_html .= '</div>';
                $user_list[$x]['complete_percent'] = $assessment_html;
                // $score=isset($new_avg_accuracy[$new_manager_id[$i]])?$new_avg_accuracy[$new_manager_id[$i]]:0.00;
                if (isset($new_manager_id[$i]) && $new_manager_id[$i] != 0 && $new_avg_accuracy[$new_manager_id[$i]] != '') {
                    $accuracy_bar = '<div class="bold" style="' . get_graphcolor($new_avg_accuracy[$new_manager_id[$i]], 2) . '; text-align: center;padding: 10px;">' . $new_avg_accuracy[$new_manager_id[$i]] . '</div>';
                } else {
                    $accuracy_bar = '<div style = "text-align: center;padding-top: 10px;">Not Started </div>';
                }
                // $user_list[$x]['avg_accuracy'] = isset($new_avg_accuracy[$new_manager_id[$i]]) ? $new_avg_accuracy[$new_manager_id[$i]] : '-';
                $user_list[$x]['avg_accuracy'] =  $accuracy_bar;
                $x++;
            }
        }
        $DTRenderArray = $user_list;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            // "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalRecords" => count((array)$new_manager_id),
            "iTotalDisplayRecords" => count((array)$new_manager_id),
            // "iTotalDisplayRecords" => 20,
            "aaData" => array()
        );
        $dtDisplayColumns = array('manager_id', 'manager_name', 'reps', 'complete_percent', 'avg_accuracy');

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

    public function user_data_modal($assessment_id, $manager_id, $SDate, $EDate, $is_custom = '')
    {
        if ($assessment_id == '0') {
            $assessment_id = '';
        }
        $company_id = $this->mw_session['company_id'];
        if ($is_custom == "") {
            $start_date = '';
            $current_date =  '';
            $user_data = $this->Reports_competency_model->get_user_data($assessment_id, $manager_id, $company_id, $start_date, $current_date);
        } else if ($is_custom == "Current_Year") {
            $startdate = date('Y-01-01');
            $CurrentDate = date("Y-m-d");
            $user_data = $this->Reports_competency_model->get_user_data($assessment_id, $manager_id, $company_id, $startdate, $CurrentDate);
        } else {
            $user_data = $this->Reports_competency_model->get_user_data($assessment_id, $manager_id, $company_id, $SDate, $EDate);
        }
        if (isset($user_data) && !empty($user_data)) {
            $html_data = "";
            foreach ($user_data as $ud) {
                $html_data .= "<tr>";
                $html_data .= "<td>" . $ud->assessment . "</td>";
                $html_data .= "<td>" . $ud->emp_id . "</td>";
                $html_data .= "<td>" . $ud->manager_name . "</td>";
                // $html_data .= "<td>" . $ud->users_id . "</td>";
                $html_data .= "<td>" . $ud->learner_name . "</td>";
                $final_score = $ud->final_score;
                //  if ($final_score == '') {
                if ($ud->is_completed == '0' or $ud->is_completed == '') {
                    $html_data .= "<td>Not Started</td>";
                } else {
                    $html_data .= "<td style='" . get_graphcolor($ud->final_score, 2) . "'; text-align: center;padding: 10px;' >" . $ud->final_score . "</td>";
                }
                $html_data .= "</tr>";
            }
        } else {
            $html_data = "";
            $manager_id = "";
            $is_custom = "";
            $assessment_id = "";
            $SDate = "";
            $EDate = "";
        }

        $data['html_data'] = $html_data;
        $data['trainer_id'] = $manager_id;
        $data['is_custom'] = $is_custom;
        $data['assessment_id'] = $assessment_id;
        $data['SDate'] = $SDate;
        $data['EDate'] = $EDate;
        $this->load->view('reports_competency/user_data_modal', $data);
    }

    //export raps data
    public function export_raps_data()
    {
        $Company_id = $this->mw_session['company_id'];
        $trainer_id = $this->input->post('trainer_id', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $is_custom = $this->input->post('is_custom', true);
        $SDate = $this->input->post('SDate', true);
        $EDate = $this->input->post('EDate', true);
        if (isset($trainer_id) && count((array)$trainer_id) > 0) {
            if ($is_custom == "") {
                $start_date = '';
                $current_date =  '';
                $user_data = $this->Reports_competency_model->get_user_data($assessment_id, $trainer_id, $Company_id, $start_date, $current_date);
            } else if ($is_custom == "Current_Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $user_data = $this->Reports_competency_model->get_user_data($assessment_id, $trainer_id, $Company_id, $startdate, $CurrentDate);
            } else {
                $user_data = $this->Reports_competency_model->get_user_data($assessment_id, $trainer_id, $Company_id, $SDate, $EDate);
            }
            $user_list = array();
            $x = 0;
            foreach ($user_data as $us) {
                $user_list[$x]['assessment_name'] = $us->assessment;
                $user_list[$x]['emp_id'] = $us->emp_id;
                $user_list[$x]['manager_name'] = $us->manager_name;
                $user_list[$x]['learner_name'] = $us->learner_name;
                $final_score = $us->final_score;
                // if($final_score != 0.00){
                if ($us->is_completed > 0) {
                    $user_list[$x]['final_score'] = $us->final_score;
                } else {
                    $user_list[$x]['final_score'] = 'Not Started';
                }

                $x++;
            }
        } else {
            $user_list[] = "";
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
        // $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        // $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleArray_body);
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
        $file_name = "Manager wise Understanding graph";
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        // if ($assessment != "") {
        header('Content-Disposition: attachment;filename=' . "$file_name.xls");
        // } else {
        //     header('Content-Disposition: attachment;filename="Rockstars (Reps who scored more than 85 %).xls"');
        // }
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_end_clean();
        $objWriter->save('php://output');
    }

    public function export_manager_data()
    {

        $assessment_id =  $this->input->post('ass_id', true);
        $manager_id = $this->input->post('managerid', true);
        $start_date = $this->input->post('startdate', true);
        $end_date =   $this->input->post('enddate', true);
        $is_custom =  $this->input->post('iscustom', true);
        $company_id = $this->mw_session['company_id'];
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        // echo '<pre>';
        // print_r($this->input->get());exit;
        $manager_details = '';
        $user_list[] = "";
        if ($assessment_id == '' && $manager_id == '') {
            // Time filter
            if ($is_custom == "") {
                $start_date = '';
                $current_date =  date("Y-m-d h:i:s");
                $last_assessment_array = $this->Reports_competency_model->get_last_assessment($start_date, $current_date);
            } else if ($is_custom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $last_assessment_array = $this->Reports_competency_model->get_last_assessment($startdate, $CurrentDate);
            } else {
                $last_assessment_array = $this->Reports_competency_model->get_last_assessment($SDate, $EDate);
            }
            // echo '<pre>';
            // print_r($last_assessment_array);exit;
            if (count((array)$last_assessment_array) > 0) {
                foreach ($last_assessment_array as $k => $rld) {
                    $assessmentid[] = $rld->id;
                }
                $manager_details = $this->Reports_competency_model->get_manager_details($company_id, $assessmentid);
                if (count((array)$manager_details) > 0) {
                    foreach ($manager_details as $md) {
                        $managerid[] = $md->manager_id;
                    }
                    $manager_id = implode(',', $managerid);
                }
            }
        } elseif ($assessment_id != '' && $manager_id == '') {
            $assessmentid = explode(',', $assessment_id);
            $manager_details = $this->Reports_competency_model->get_manager_details($company_id, $assessmentid);
            if (count((array)$manager_details) > 0) {
                foreach ($manager_details as $md) {
                    $managerid[] = $md->manager_id;
                }
                $manager_id = implode(',', $managerid);
            }
        }
        if ($assessment_id != '' || $manager_id != '') {
            if ($is_custom == "") {
                $start_date = '';
                $current_date =  '';
                $get_reps_percent = $this->Reports_competency_model->get_reps_percent_manager_wise($manager_id, $start_date, $current_date, $assessment_id);
            } else if ($is_custom == "Current Year") {
                $startdate = date('Y-01-01');
                $current_date = date("Y-m-d");
                $get_reps_percent = $this->Reports_competency_model->get_reps_percent_manager_wise($manager_id, $startdate, $current_date, $assessment_id);
            } else {
                $get_reps_percent = $this->Reports_competency_model->get_reps_percent_manager_wise($manager_id, $SDate, $EDate, $assessment_id);
            }

            $total_reps = array();
            $complete_percent = array();
            $new_manager_id = array();
            $manager_name = array();
            $complete_cnt = array();

            if (count((array)$get_reps_percent) > 0) {
                foreach ($get_reps_percent as $grp) {
                    $new_manager_id[] = $grp->trainer_id;
                    $manager_name[] = $grp->manager_name;
                    $total_reps[] = $grp->total_reps;
                    $complete_percent[] = $grp->percetage;
                    $complete_cnt[] = $grp->completed;
                }
                if ($is_custom == "") {
                    $start_date = '';
                    $current_date =  date("Y-m-d h:i:s");
                    $get_avg_accuracy = $this->Reports_competency_model->get_avg_accuracy($company_id, $new_manager_id, $start_date, $current_date, $assessment_id);
                } else if ($is_custom == "Current Year") {
                    $startdate = date('Y-01-01');
                    $current_date = date("Y-m-d");
                    $get_avg_accuracy = $this->Reports_competency_model->get_avg_accuracy($company_id, $new_manager_id, $startdate, $current_date, $assessment_id);
                } else {
                    $get_avg_accuracy = $this->Reports_competency_model->get_avg_accuracy($company_id, $new_manager_id, $SDate, $EDate, $assessment_id);
                }
                $new_avg_accuracy = array();
                foreach ($get_avg_accuracy as $ag) {
                    $new_avg_accuracy[$ag['trainer_id']] = $ag['overall_score'];
                }
            }
        }

        $user_list = array();
        $x = 0;
        $count_manager = count((array)$new_manager_id);
        if ($count_manager != 0) {
            for ($i = 0; $i < $count_manager; $i++) {
                $user_list[$x]['Manager_id'] = $new_manager_id[$i];
                $user_list[$x]['Manager_name'] = $manager_name[$i];
                $user_list[$x]['No_of_reps'] = $total_reps[$i];
                $user_list[$x]['Completion_progress'] = $complete_percent[$i];
                if ($complete_cnt[$i] != 0) {
                    $user_list[$x]['Team_avg_accuracy']  = $new_avg_accuracy[$new_manager_id[$i]];
                } else {
                    $user_list[$x]['Team_avg_accuracy']  = 'Not Started';
                }
                $x++;
            }
        } else {
            $user_list[$x]['manager_id'] = '';
            $user_list[$x]['manager_name'] = '';
            $user_list[$x]['no_of_reps'] = '';
            $user_list[$x]['completion_progress'] = '';
            $user_list[$x]['avg_accuracy'] = '';
            $x++;
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
        // $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray_body);
        $i = 1;
        $j = 1;

        if ($user_list != '') {
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
        }
        $file_name = "Manager wise Understanding graph";
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        // if ($assessment != "") {
        header('Content-Disposition: attachment;filename=' . "$file_name.xls");
        // } else {
        //     header('Content-Disposition: attachment;filename="Rockstars (Reps who scored more than 85 %).xls"');
        // }
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_end_clean();
        $objWriter->save('php://output');
    }
    // heat map start here
    public function heat_wise_region()
    {
        $assessment_html = '';
        $Company_id = $this->mw_session['company_id'];
        $ass_id = ($this->input->post('ass_essment_id', TRUE) ? $this->input->post('ass_essment_id', TRUE) : 0);
        $assessment_list = $this->Reports_competency_model->heatmap_wise_region($ass_id, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '">' . $value->region_name . '</option>';
            }
        }
        $data['heat_region']  = $assessment_html;
        echo json_encode($data);
    }

    public function get_heatmap_data($returnflag = 0)
    {
        $Company_id = $this->mw_session['company_id'];
        $SDate = $this->input->post('StartDate', true);
        $StartDate = date('Y-m-d', strtotime($SDate));
        $EDate = $this->input->post('EndDate', true);
        $EndDate = date('Y-m-d', strtotime($EDate));
        $hit_custom = $this->input->post('iscustom', true) != '' ? $this->input->post('iscustom', true) : '';
        $region_id = $this->input->post('region_id');
        $assessment_id = $this->input->post('assessmentid');
        $para_assess = array();
        $regiondata_result = array();
        $region_list = array();
        $Vertical_avg = array();
        $Horizontal_avg = array();
        $modules_count = 0;
        $region_count = 0;
        if ($assessment_id == '' && $region_id  == '') {
            if ($hit_custom == "") {
                $StartDate = date('Y-m-d', strtotime("-30 days"));
                $EndDate = date("Y-m-d");
                $last_assessment_array = $this->Reports_competency_model->last_assessment($StartDate, $EndDate);
            } else {
                $last_assessment_array = $this->Reports_competency_model->last_assessment($StartDate, $EndDate);
            }
            if (count((array)$last_assessment_array) > 0) {
                $lastAssessmentId = array();
                foreach ($last_assessment_array as $rld) {
                    $lastAssessmentId[] = $rld['id'];
                }
                $assessment_id = $lastAssessmentId;
            }
            // for ($i = 0; $i < count((array)$last_assessment); $i++) {
            //     $lastAssessmentId[] = isset($last_assessment[$i]['id']) ? $last_assessment[$i]['id'] : 0;
            // }
        }
        if ($region_id != '' || $assessment_id != '') {

            if ($hit_custom == "") {
                $StartDate = date('Y-m-d', strtotime("-30 days"));
                $EndDate = date("Y-m-d");
                $regionset = $this->Reports_competency_model->get_region_result($Company_id, $region_id, $StartDate, $EndDate, $assessment_id);
            } else {
                $regionset = $this->Reports_competency_model->get_region_result($Company_id, $region_id, $StartDate, $EndDate, $assessment_id);
            }
            if (count((array)$regionset) > 0) {
                $horizontal_avg_array = $this->Reports_competency_model->get_horizontal_and_vertical_avg($Company_id, 1, $region_id, $StartDate, $EndDate, $assessment_id);
                $vertical_avg_array = $this->Reports_competency_model->get_horizontal_and_vertical_avg($Company_id, 2, $region_id, $StartDate, $EndDate, $assessment_id);
                foreach ($horizontal_avg_array as $key => $rld) {
                    $Horizontal_avg[$rld->region_id] = $rld->region_result;
                }
                foreach ($vertical_avg_array as $key => $v_g) {
                    $Vertical_avg[$v_g->assessment_id] = $v_g->region_result;
                }

                foreach ($regionset as $key => $rl) {
                    if (!in_array($rl->region_name, $region_list)) {
                        $region_list[$rl->region_id] = $rl->region_name;
                    }
                    if (!in_array($rl->name, $para_assess)) {
                        $para_assess[$rl->para_assess_id] = $rl->name;
                    }
                    $regiondata_result[$rl->region_id][$rl->para_assess_id] = $rl;
                }
                $modules_count = count($para_assess);
                $region_count = count($region_list);
            }
            // else {
            //     $Horizontal_avg = '';
            //     $region_list = '';
            //     $regiondata_result = '';
            //     $Vertical_avg = '';
            //     $para_assess = '';
            // }
        }
        $Rtdata['Horizontal_avg'] = $Horizontal_avg;
        $Rtdata['Vertical_avg'] = $Vertical_avg;
        $Rtdata['region_list'] = $region_list;
        $Rtdata['para_assess'] = $para_assess;
        $Rtdata['regiondata'] = $regiondata_result;
        $Rtdata['modules_count'] = $modules_count;
        $Rtdata['region_count'] = $region_count;

        $data['regiontable_graph'] = $this->load->view('reports_competency/regiontable_view', $Rtdata, true);
        $data['module_count'] = $modules_count;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }


    public function region_scoredata()
    {
        $data['company_id'] = $this->mw_session['company_id'];
        $data['region_id'] = $this->input->post('region_id', true);
        $data['assessment_id'] = $this->input->post('assessment_id', true);
        $this->load->view('reports_competency/regionwise_scoremodal', $data);
    }

    public function regionwise_table()
    {
        $company_id = $this->mw_session['company_id'];
        $region_id = $this->input->post('region_id', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $para_assess = array();
        $user_list = array();
        $regiondata_result = array();
        //  $result_data = $this->assessment_dashboard_model->get_parameter_user_result($company_id, $region_id, $assessment_id, $store_id);
        $result_data = $this->Reports_competency_model->get_parameter_user_result_new($company_id, $region_id, $assessment_id);
        $Horizontal_avg = array();
        $Vertical_avg = array();
        if (count((array)$result_data) > 0) {
            // $Avgset = $this->assessment_dashboard_model->get_user_average($company_id, 1,$region_id,$assessment_id,$store_id);
            $Avgset = $this->Reports_competency_model->get_user_average($company_id, 1, $region_id, $assessment_id);
            $Avgset2 = $this->Reports_competency_model->get_user_average($company_id, 3, $region_id, $assessment_id);
            $Horizontal_avg = array();
            $Vertical_avg = array();
            foreach ($Avgset2 as $key => $rl) {
                $Horizontal_avg[$rl->user_id] = $rl->p_result;
            }
            foreach ($Avgset as $key => $rl) {
                $Vertical_avg[$rl->parameter_id] = $rl->p_result;
            }
            foreach ($result_data as $key => $rd) {
                if (!isset($user_list[$rd->user_id])) {
                    $user_list[$rd->user_id] = array(
                        'name' => $rd->firstname,
                        'id' => $rd->user_id
                    );
                }
                if (!in_array($rd->parameter, $para_assess)) {
                    $para_assess[$rd->parameter_id] = $rd->parameter;
                }
                $regiondata_result[$rd->user_id][$rd->parameter_id] = $rd;
            }
            $user_count = count($user_list);
        }

        // Vertical Avg given to Horizontal same as Horizontal due to requirment.

        $Rtdata['Horizontal_avg'] = $Vertical_avg;
        $Rtdata['Vertical_avg'] = $Horizontal_avg;
        $Rtdata['user_list'] = $user_list;
        $Rtdata['para_assess'] = $para_assess;
        $Rtdata['regiondata'] = $regiondata_result;
        $data['regiontable_graph'] = $this->load->view('reports_competency/parameter_usertable_view', $Rtdata, true);
        $data['user_count'] = $user_count;
        echo json_encode($data);
    }

    //  competency by manager start here
    public function Getassessment_wise_d_r_m()
    {
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $Company_id =  $this->input->post('company_id', TRUE);
        $assessment_list = $this->Reports_competency_model->assessment_wise_managers($assessment_id, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $mvalue) {
                $assessment_html .= '<option value="' . $mvalue->user_id . '">[' . $mvalue->user_id . '] - ' . $mvalue->user_name . '</option>';
            }
        }
        $data['cm_managers']  = $assessment_html;
        echo json_encode($data);
    }
    public function time_wise_manager()
    {
        $assessment_html = '';
        $Company_id =  $this->input->post('company_id', TRUE);
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('iscustom', true) != '' ? $this->input->post('iscustom', true) : '';
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        if ($IsCustom == "") {
            $SDate = "";
            $EDate = date("Y-m-d");
            $assessment_list = $this->Reports_competency_model->time_wise_managers($SDate, $EDate, $Company_id);
        } elseif ($IsCustom == "Current Year") {
            $SDate = date('Y-01-01');
            $EDate = date("Y-m-d");
            $assessment_list = $this->Reports_competency_model->time_wise_managers($SDate, $EDate, $Company_id);
        } else {
            $assessment_list = $this->Reports_competency_model->time_wise_managers($SDate, $EDate, $Company_id);
        }
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $mvalue) {
                $assessment_html .= '<option value="' . $mvalue->manager_id . '">[' . $mvalue->manager_id . '] - ' . $mvalue->manager_name . '</option>';
            }
        }
        $data['cm_managers']  = $assessment_html;
        echo json_encode($data);
    }

    public function competency_by_manager($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessmentid', TRUE) != '' ?  $this->input->post('assessmentid', true) : '';
        $manager_id = $this->input->post('manager_id', TRUE) != '' ?  $this->input->post('manager_id', true) : '';
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('iscustom', true) != '' ? $this->input->post('iscustom', true) : '';
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $this->load->model('Reports_competency_model');
        $report_data = array();
        $manager_name = array();
        $score = array();

        if ($Assessment_id == '' and $manager_id == "") {
            if ($IsCustom == "") {
                $SDate = date('Y-m-d', strtotime("-30 days"));
                $EDate = date("Y-m-d");
                $getfiveassessment = $this->Reports_competency_model->lastassessment($SDate, $EDate);
            } else {
                $getfiveassessment = $this->Reports_competency_model->lastassessment($SDate, $EDate);
            }
            foreach ($getfiveassessment as $rld) {
                $lastAssessmentId[] = $rld['id'] != '' ? $rld['id'] : '';
            }
            $Assessment_id = $lastAssessmentId;
            if ($Assessment_id != '') {
                $assessment_list = $this->Reports_competency_model->get_manager_details($Company_id, $Assessment_id);
                if (!empty($assessment_list)) {
                    for ($m = 0; $m < count($assessment_list); $m++) {
                        $managerid[] = isset($assessment_list[$m]->manager_id) ? $assessment_list[$m]->manager_id : '';
                    }
                    $manager_id = $managerid;
                }
                if (!empty($manager_id)) {

                    if ($IsCustom == "") {
                        $SDate = date('Y-m-d', strtotime("-30 days"));
                        $EDate = date("Y-m-d");
                        $getmanager_score = $this->Reports_competency_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
                    } else {
                        $getmanager_score = $this->Reports_competency_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
                    }
                    if (!empty($getmanager_score)) {
                        for ($i = 0; $i < count($getmanager_score); $i++) {
                            $manager_name[] =    isset($getmanager_score[$i]['m_name']) ? $getmanager_score[$i]['m_name'] : 'No Name';
                            $score[] =    isset($getmanager_score[$i]['overall_score']) ? $getmanager_score[$i]['overall_score'] : '0';
                        }
                    } else {
                        $manager_name[] = '';
                        $score[] = '';
                    }
                } else {
                    $manager_name[] = '';
                    $score[] = '';
                }
            } else {
                $manager_name[] = '';
                $score[] = '';
            }
            $Mcount = count($manager_name);
        } elseif ($Assessment_id == ''  and $manager_id != "") {
            $Assessment_id = '';
            if ($IsCustom == "") {
                $SDate = '';
                $EDate = '';
                $getmanager_score = $this->Reports_competency_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            } else {
                $getmanager_score = $this->Reports_competency_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            }
            if (!empty($getmanager_score)) {
                $new = count($getmanager_score);
                for ($i = 0; $i < $new; $i++) {
                    $manager_name[] = isset($getmanager_score[$i]['m_name']) ? $getmanager_score[$i]['m_name'] : 'No Name';
                    $score[] =    isset($getmanager_score[$i]['overall_score']) ? $getmanager_score[$i]['overall_score'] : '0';
                }
            } else {
                $manager_name[] = '';
                $score[] = '';
            }
            $Mcount = count($manager_name);
        } else {
            if ($IsCustom == "") {
                $SDate = '';
                $EDate = '';
                $getmanager_score = $this->Reports_competency_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            } else {
                $getmanager_score = $this->Reports_competency_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            }
            if (!empty($getmanager_score)) {
                $new = count($manager_id);
                for ($i = 0; $i < $new; $i++) {
                    $manager_name[] =    isset($getmanager_score[$i]['m_name']) ? $getmanager_score[$i]['m_name'] : 'No Name';
                    $score[] =    isset($getmanager_score[$i]['overall_score']) ? $getmanager_score[$i]['overall_score'] : '0';
                }
            } else {
                $manager_name[] = '';
                $score[] = '';
            }
            $Mcount = count($manager_name);
        }
        $data['report'] = $report_data;
        $Rdata['Mcount'] = $Mcount;
        $Rdata['manager_name'] = json_encode($manager_name);
        $Rdata['score'] = json_encode($score, JSON_NUMERIC_CHECK);
        $cm_by_manager = $this->load->view('reports_competency/compentency_by_manager', $Rdata, true);
        $data['c_m_managers'] = $cm_by_manager;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    //  end here
}
