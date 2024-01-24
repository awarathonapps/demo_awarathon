<?php

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\PseudoTypes\True_;


if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports_manager_adoption extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('reports_manager_adoption');
        // if (!$acces_management->allow_access) {
        //     redirect('dashboard');
        // }
        $this->acces_management = $acces_management;
        $this->load->model('Reports_manager_adoption_model');
    }

    public function index()
    {
        $data['module_id'] = '94';
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        $manager_id = $this->mw_session['user_id'];

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

        // $data['region_data'] = $this->Reports_manager_adoption_model->get_trainee_region($data['company_id']);

        $data['start_date'] = date('d-M-Y', strtotime('-6 days'));
        $data['end_date'] = date("d-m-Y");
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date("Y-m-d");

        //Added

        $data['company_id'] = $this->mw_session['company_id'];
        $company_id = $data['company_id'];
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="2" AND company_id="' . $company_id . '"');

        //$assessment_list= $this->Reports_manager_adoption_model->get_assessment_list($company_id, $trainer_id, $start_date, $end_date);


        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');

        //----------------
        // $data['assessment_data'] = $this->Reports_manager_adoption_model->get_assessment($data['company_id'], '', $start_date, $end_date);

        // $data['parameter_data'] = $this->Reports_manager_adoption_model->get_parameter();
        $data['assessment'] = $this->Reports_manager_adoption_model->get_all_assessment($manager_id);
        $this->load->view('reports_manager_adoption/index', $data);
    }

    public function ajax_getWeeks()
    {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function adoption_by_module($returnflag = 0)
    {

        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $manager_id = $this->mw_session['user_id'];
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $this->load->model('Manager_adoption_model');
        $report_data = array();
        $Userstarted = array();
        $Usercompleted = array();
        if ($Assessment_id == '') {
            // $CurrentDate = date("Y-m-d h:i:s");
            $LAssessmentDetails = $this->Reports_manager_adoption_model->LastExpiredFiveAssessment($manager_id, $Company_id);
            $len = count($LAssessmentDetails);
            for ($i = 0; $i < $len; $i++) {
                $lastAssessmentId[] = isset($LAssessmentDetails[$i]['id']) ? $LAssessmentDetails[$i]['id'] : '0';
                $lastAssessment[] = isset($LAssessmentDetails[$i]['assessment']) ? $LAssessmentDetails[$i]['assessment'] : 'Empty Data';
            }
            if ($lastAssessmentId == "") {
                $Userstarted[] =    '0';
                $Usercompleted[] =   '0';
            } else {
                $getUserstart = $this->Reports_manager_adoption_model->GetUserAssessmentwise($lastAssessmentId, $Company_id);
                $new = count($lastAssessmentId);
                for ($i = 0; $i < $new; $i++) {
                    $Userstarted[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $Usercompleted[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            }
        } else {
            $getUserstart = $this->Reports_manager_adoption_model->GetUserAssessmentwise($Assessment_id,$Company_id);
            $new = count($Assessment_id);
            for ($i = 0; $i < $new; $i++) {
                $lastAssessment[] =    isset($getUserstart[$i]['assessment']) ? $getUserstart[$i]['assessment'] : 'Empty Data';
                $Userstarted[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                $Usercompleted[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
            }
        }
        $data['report'] = $report_data;
        $Rdata['index_dataset'] = json_encode($lastAssessment);
        $Rdata['index_label'] = json_encode($Userstarted, JSON_NUMERIC_CHECK);
        $Rdata['user_completed'] = json_encode($Usercompleted, JSON_NUMERIC_CHECK);
        $ad_byModule = $this->load->view('reports_manager_adoption/AdoptionByModule', $Rdata, true);
        $data['adoption_by_modules'] = $ad_byModule;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    // total video uploaded and Processed
    public function get_uploaded_processed($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('Manager_adoption_model');
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $manager_id =  $this->mw_session['user_id'];

        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);
        $type  = $date2->diff($date1)->format('%a');

        $current_month = date('m');
        $lastDayThisMonth = date("Y-m-t");
        $report_data = array();
        $index_dataset = array();
        $completed_dataset = array();
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $newyear = date('Y');
        $Week = $this->input->post('week', true);
        $ManagerDetails = $this->Reports_manager_adoption_model->usersmangerwise($manager_id);
        $len = count($ManagerDetails);
        if ($len == '0') {
            $completed_dataset[] = '0';
            $index_dataset[] = '0';
            $index_label[] = '0';
            $report_title = '';
            $report_xaxis_title='';
        } 
        else 
        {

            if ($IsCustom == '' or $IsCustom == 'Current Year') {
                // Return Current year
                $YearStartDate = $newyear . '-01-01';
                $YearEndDate = $newyear . '-12-31';
                $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = 'current';
                $AssessmentCount = $this->Reports_manager_adoption_model->total_video_uploaded($YearStartDate, $YearEndDate, $Day_type, $Company_id, $manager_id);
                for ($i = 1; $i <= $current_month; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-' . $day . '-01');
                    if (isset($AssessmentCount['uploaded'][$i])) {
                        $index_dataset[] = json_encode($AssessmentCount['uploaded'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }

                    if (isset($AssessmentCount['processed'][$i])) {
                        $completed_dataset[] = json_encode($AssessmentCount['processed'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }

                    $index_label[] = date("M", strtotime($TempDate));
                }
            } else if ($IsCustom == 'Last 7 Days') {
                // Last 7 days  
                $StartStrDt = date('Y-m-d', strtotime("-6 days"));
                $StartWeek = date('d', strtotime("-6 days"));

                $Edate = date('Y-m-d');
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));
                $EndWeek = date('d');

                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1W'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();
                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('D') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('D');
                            $dateByWeek[$Week][] = $d->format('d-m');
                        }
                        $EndDate = $d->format('m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d-m', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0];
                        $result = $weeksteddate;
                    }
                } else {
                    $result = '';
                }
                $report_xaxis_title = 'Last 7 Days';
                $Day_type = "7_days";
                $AssessmentCount = $this->Reports_manager_adoption_model->total_video_uploaded($StartStrDt, $Edate, $Day_type, $Company_id, $manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($Edate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($AssessmentCount['uploaded'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['uploaded'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['processed'][$day])) {
                        $completed_dataset[] = json_encode($AssessmentCount['processed'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                }
                $index_label = $weeksteddate;
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($Edate)) . '';
                // Last 7 days  
            } elseif ($IsCustom == "Last 30 Days") {
                // Last 29 days 
                $StartStrDt = date('Y-m-d', strtotime("-29 days"));
                $StartWeek = date('d', strtotime("-29 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 30 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Get week
                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1M'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $AssessmentCount[] = $this->Reports_manager_adoption_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                        $result = $AssessmentCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i]['uploaded'])) {
                        $index_dataset[] = json_encode($result[$i]['uploaded'], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (!empty($result[$i]['processed'])) {
                        $completed_dataset[] = json_encode($result[$i]['processed'], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                }
                // Last 29 days 
            } elseif ($IsCustom == "Last 60 Days") {
                // Last 60 days
                $StartStrDt = date('Y-m-d', strtotime("-59 days"));
                $OldDay = date('d', strtotime("-59 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 60 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                //Get Week
                $result = '';
                if ($year != '' && $month != '' && $OldDay != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay")->add(new DateInterval('P2M'))
                    );

                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));

                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $AssessmentCount[] = $this->Reports_manager_adoption_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                        $result = $AssessmentCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i]['uploaded'])) {
                        $index_dataset[] = json_encode($result[$i]['uploaded'], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (!empty($result[$i]['processed'])) {
                        $completed_dataset[] = json_encode($result[$i]['processed'], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                }
                //Last 60 days
            } elseif ($IsCustom == "Last 90 Days") {
                $StartStrDt = date('Y-m-d', strtotime("-89 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Last 90 Days';
                $Day_type = "90_days";
                $AssessmentCount = $this->Reports_manager_adoption_model->total_video_uploaded($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);

                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("n");
                    $month = $i->format("M");
                    if (isset($AssessmentCount['uploaded'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['uploaded'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['processed'][$day])) {
                        $completed_dataset[] = json_encode($AssessmentCount['processed'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            } elseif ($IsCustom == "Last 365 Days") {
                // Last 365 days
                $StartStrDt = date('Y-m-d', strtotime("-365 days"));
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $AssessmentCount = $this->Reports_manager_adoption_model->total_video_uploaded($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($AssessmentCount['uploaded'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['uploaded'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['processed'][$day])) {
                        $completed_dataset[] = json_encode($AssessmentCount['processed'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Last 365 days
            } else {
                // Custom Datepicker
                if ($type < "15") {
                    // 7 to 15 days data
                    $day = date('d', strtotime($SDate));
                    $month = date('m', strtotime($SDate));
                    $year = date('Y', strtotime($SDate));
                    $EndWeek = date('d');
                    $EDate = date('Y-m-d', strtotime($end_date));
                    $result = '';
                    if ($year != '' && $month != '' && $day != '') {
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P15D'))
                        );
                        $WStartDate = array();
                        $WEndDate = array();

                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('D') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('D');
                                $dateByWeek[$Week][] = $d->format('d-m');
                            }
                            $EndDate = $d->format('m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d-m', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0];

                            $result = $weeksteddate;
                        }
                    } else {
                        $result = '';
                    }
                    $report_xaxis_title = '3 to 15 Days';
                    $Day_type = '7_days';
                    $AssessmentCount = $this->Reports_manager_adoption_model->total_video_uploaded($SDate, $EDate, $Day_type, $Company_id, $manager_id);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                        $day = $i->format("d");
                        if (isset($AssessmentCount['uploaded'][$day])) {
                            $index_dataset[] = json_encode($AssessmentCount['uploaded'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        if (isset($AssessmentCount['processed'][$day])) {
                            $completed_dataset[] = json_encode($AssessmentCount['processed'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $completed_dataset[] = 0;
                        }
                    }
                    $index_label = $weeksteddate;
                    $report_title = 'From ' . date('d-m-Y', strtotime($SDate)) . ' To ' . date('d-m-Y', strtotime($EDate)) . '';
                    // Last 7 days   
                } else if ($type >= "15" && $type <= "61") {
                    // One month or Two Month Logic
                    $startday = date('d', strtotime($start_date));
                    $startmonth = date('m', strtotime($start_date));
                    $startyear = date('Y', strtotime($start_date));

                    $EndWeek = date('d');
                    $EndDtdate = date('Y-m-d');
                    $report_xaxis_title = '15 to 30 Days';
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';

                    $result = '';
                    if ($startyear != '' && $startmonth != '' && $startday != '') {
                        $startDate = new DateTime($start_date);
                        $endDate = new DateTime($end_date);

                        $difference = $endDate->diff($startDate);
                        $scbd = $difference->format("%a") + 1;
                        $DaysLoop = 'P' . $scbd . 'D';

                        // Given two Month data
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday")->add(new DateInterval($DaysLoop))
                        );
                        $WStartDate = array();
                        $WEndDate = array();
                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('W') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('W');
                                $dateByWeek[$Week][] = $d->format('Y-m-d');
                            }
                            $EndDate = $d->format('Y-m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0] . '-' . $value[1];
                            $AssessmentCount[] = $this->Reports_manager_adoption_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                            $result = $AssessmentCount;
                        }
                    } else {
                        $result = '';
                    }
                    $recount = count((array)$result) - 1;
                    $weekprint = 1;
                    for ($i = 0; $i <= $recount; $i++) {
                        if (!empty($result[$i]['uploaded'])) {
                            $index_dataset[] = json_encode($result[$i]['uploaded'], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        if (!empty($result[$i]['processed'])) {
                            $completed_dataset[] = json_encode($result[$i]['processed'], JSON_NUMERIC_CHECK);
                        } else {
                            $completed_dataset[] = 0;
                        }
                        $index_label[] =  "Week " . $weekprint;
                        $weekprint++;
                    }
                } else if ($type >= "60") {
                    // 01-01-2022 to current month
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                    $report_xaxis_title = 'Yearly';
                    $Day_type = "365_days";
                    $AssessmentCount = $this->Reports_manager_adoption_model->total_video_uploaded($SDate, $EDate, $Day_type, $Company_id, $manager_id);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                        $day = $i->format("m-Y");
                        $month = $i->format("M");
                        if (isset($AssessmentCount['uploaded'][$day])) {
                            $index_dataset[] = json_encode($AssessmentCount['uploaded'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        if (isset($AssessmentCount['processed'][$day])) {
                            $completed_dataset[] = json_encode($AssessmentCount['processed'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $completed_dataset[] = 0;
                        }
                        $index_label[] = $month;
                    }
                }
                // custom Datepicker end
            }
        } 

        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['completed_dataset'] = json_encode($completed_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $Played_Raps_Completed = $this->load->view('reports_manager_adoption/video_uploaded_proccessed', $Rdata, true);
        $data['Uploaded_processed_video'] = $Played_Raps_Completed;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // total video uploaded and Processed
    // get reps played and complted
    public function get_raps_played_completed($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('Manager_adoption_model');
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $manager_id =  $this->mw_session['user_id'];

        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);
        $type  = $date2->diff($date1)->format('%a');

        $current_month = date('m');
        $lastDayThisMonth = date("Y-m-t");
        $report_data = array();
        $index_dataset = array();
        $mapped_dataset = array();
        $completed_dataset = array();
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $newyear = date('Y');
        $Week = $this->input->post('week', true);

        $mapped_user = $this->Reports_manager_adoption_model->usersmangerwise($manager_id);
        $len_user = count($mapped_user);
        if ($len_user == 0) {
            $Rdata['report_period'] = '';
            $Rdata['report_title'] = '';
            $Rdata['mapped_dataset'] = '0';
            $Rdata['index_dataset'] = '0';
            $Rdata['completed_dataset'] = '0';
            $Rdata['index_label'] = '';
        } else {
            $mapped_user_id = array();
            for ($i = 0; $i < $len_user; $i++) {
                if (isset($mapped_user)) {
                    $mapped_user_id[] = $mapped_user[$i]['user_id'];
                }
            }

            if ($IsCustom == '' or $IsCustom == 'Current Year') {
                // Return Current year
                $YearStartDate = $newyear . '-01-01';
                $YearEndDate = $newyear . '-12-31';
                $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = 'current';
                $AssessmentCount = $this->Reports_manager_adoption_model->RapsPlayedComplted($YearStartDate, $YearEndDate, $Day_type, $Company_id, $mapped_user_id);
                $MappedUserCount = $this->Reports_manager_adoption_model->MappedUsers($YearStartDate, $YearEndDate, $Day_type, $Company_id, $manager_id);
                for ($i = 1; $i <= $current_month; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-' . $day . '-01');
                    if (isset($MappedUserCount['mapped'][$i])) {
                        $mapped_dataset[] = json_encode($MappedUserCount['mapped'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $mapped_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['played'][$i])) {
                        $index_dataset[] = json_encode($AssessmentCount['played'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['completed'][$i])) {
                        $completed_dataset[] = json_encode($AssessmentCount['completed'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] = date("M", strtotime($TempDate));
                }
            } else if ($IsCustom == 'Last 7 Days') {
                // Last 7 days  
                $StartStrDt = date('Y-m-d', strtotime("-6 days"));
                $StartWeek = date('d', strtotime("-6 days"));

                $Edate = date('Y-m-d');
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));
                $EndWeek = date('d');

                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1W'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();
                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('D') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('D');
                            $dateByWeek[$Week][] = $d->format('d-m');
                        }
                        $EndDate = $d->format('m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d-m', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0];
                        $result = $weeksteddate;
                    }
                } else {
                    $result = '';
                }
                $report_xaxis_title = 'Last 7 Days';
                $Day_type = "7_days";
                $AssessmentCount = $this->Reports_manager_adoption_model->RapsPlayedComplted($StartStrDt, $Edate, $Day_type, $Company_id, $mapped_user_id);
                $MappedUserCount = $this->Reports_manager_adoption_model->MappedUsers($StartStrDt, $Edate, $Day_type, $Company_id, $manager_id);

                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($Edate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($MappedUserCount['mapped'][$day])) {
                        $mapped_dataset[] = json_encode($MappedUserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $mapped_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['played'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['played'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['completed'][$day])) {
                        $completed_dataset[] = json_encode($AssessmentCount['completed'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                }
                $index_label = $weeksteddate;
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($Edate)) . '';
                // Last 7 days  
            } elseif ($IsCustom == "Last 30 Days") {
                // Last 29 days 
                $StartStrDt = date('Y-m-d', strtotime("-29 days"));
                $StartWeek = date('d', strtotime("-29 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 30 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Get week
                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1M'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $result2 = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $AssessmentCount[] = $this->Reports_manager_adoption_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id, $mapped_user_id);
                        $MappedUserCount = $this->Reports_manager_adoption_model->raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);                
                        $result = $AssessmentCount;
                        $result2 = $MappedUserCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result2[$i]['mapped'])) {
                        $mapped_dataset[] = json_encode($result2[$i]['mapped'], JSON_NUMERIC_CHECK);
                    } else {
                        $mapped_dataset[] = 0;
                    }
                    if (!empty($result[$i]['played'])) {
                        $index_dataset[] = json_encode($result[$i]['played'], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (!empty($result[$i]['completed'])) {
                        $completed_dataset[] = json_encode($result[$i]['completed'], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                }
                // Last 29 days 
            } elseif ($IsCustom == "Last 60 Days") {
                // Last 60 days
                $StartStrDt = date('Y-m-d', strtotime("-59 days"));
                $OldDay = date('d', strtotime("-59 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 60 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                //Get Week
                $result = '';
                if ($year != '' && $month != '' && $OldDay != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay")->add(new DateInterval('P2M'))
                    );

                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $result2 = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $AssessmentCount[] = $this->Reports_manager_adoption_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id, $mapped_user_id);
                        $MappedUserCount[] = $this->Reports_manager_adoption_model->raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                        $result2 = $MappedUserCount;
                        $result = $AssessmentCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result2[$i]['mapped'])) {
                        $mapped_dataset[] = json_encode($result2[$i]['mapped'], JSON_NUMERIC_CHECK);
                    } else {
                        $mapped_dataset[] = 0;
                    }
                    if (!empty($result[$i]['played'])) {
                        $index_dataset[] = json_encode($result[$i]['played'], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (!empty($result[$i]['completed'])) {
                        $completed_dataset[] = json_encode($result[$i]['completed'], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                }
                //Last 60 days
            } elseif ($IsCustom == "Last 90 Days") {
                $StartStrDt = date('Y-m-d', strtotime("-89 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Last 90 Days';
                $Day_type = "90_days";
                $AssessmentCount = $this->Reports_manager_adoption_model->RapsPlayedComplted($StartStrDt, $EndDtdate, $Day_type, $Company_id, $mapped_user_id);
                $MappedUserCount = $this->Reports_manager_adoption_model->MappedUsers($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);

                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("n");
                    $month = $i->format("M");
                    if (isset($MappedUserCount['mapped'][$day])) {
                        $mapped_dataset[] = json_encode($MappedUserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $mapped_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['played'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['played'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['completed'][$day])) {
                        $completed_dataset[] = json_encode($AssessmentCount['completed'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            } elseif ($IsCustom == "Last 365 Days") {
                // Last 365 days
                $StartStrDt = date('Y-m-d', strtotime("-365 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $AssessmentCount = $this->Reports_manager_adoption_model->RapsPlayedComplted($StartStrDt, $EndDtdate, $Day_type, $Company_id, $mapped_user_id);
                $MappedUserCount = $this->Reports_manager_adoption_model->MappedUsers($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($MappedUserCount['mapped'][$day])) {
                        $mapped_dataset[] = json_encode($MappedUserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $mapped_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['played'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['played'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    if (isset($AssessmentCount['completed'][$day])) {
                        $completed_dataset[] = json_encode($AssessmentCount['completed'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $completed_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Last 365 days
            } else {
                // Custom Datepicker
                if ($type < "15") {
                    // 7 to 15 days data
                    $day = date('d', strtotime($SDate));
                    $month = date('m', strtotime($SDate));
                    $year = date('Y', strtotime($SDate));
                    $EndWeek = date('d');
                    $EDate = date('Y-m-d', strtotime($end_date));
                    $result = '';
                    if ($year != '' && $month != '' && $day != '') {
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P15D'))
                        );
                        $WStartDate = array();
                        $WEndDate = array();

                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('D') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('D');
                                $dateByWeek[$Week][] = $d->format('d-m');
                            }
                            $EndDate = $d->format('m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d-m', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0];

                            $result = $weeksteddate;
                        }
                    } else {
                        $result = '';
                    }
                    $report_xaxis_title = '3 to 15 Days';
                    $Day_type = '7_days';
                    $AssessmentCount = $this->Reports_manager_adoption_model->RapsPlayedComplted($SDate, $EDate, $Day_type, $Company_id, $mapped_user_id);
                    $MappedUserCount = $this->Reports_manager_adoption_model->MappedUsers($SDate, $EDate, $Day_type, $Company_id, $manager_id);
                    // print_r($MappedUserCount);exit;
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                        $day = $i->format("d");
                        if (isset($MappedUserCount['mapped'][$day])) {
                            $mapped_dataset[] = json_encode($MappedUserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $mapped_dataset[] = 0;
                        }
                        if (isset($AssessmentCount['played'][$day])) {
                            $index_dataset[] = json_encode($AssessmentCount['played'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        if (isset($AssessmentCount['completed'][$day])) {
                            $completed_dataset[] = json_encode($AssessmentCount['completed'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $completed_dataset[] = 0;
                        }
                    }
                    $index_label = $weeksteddate;
                    $report_title = 'From ' . date('d-m-Y', strtotime($SDate)) . ' To ' . date('d-m-Y', strtotime($EDate)) . '';
                    // Last 7 days   
                } else if ($type >= "15" && $type <= "61") {
                    // One month or Two Month Logic
                    $startday = date('d', strtotime($start_date));
                    $startmonth = date('m', strtotime($start_date));
                    $startyear = date('Y', strtotime($start_date));

                    $EndWeek = date('d');
                    $EndDtdate = date('Y-m-d');
                    $report_xaxis_title = '15 to 30 Days';
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';

                    $result = '';
                    if ($startyear != '' && $startmonth != '' && $startday != '') {
                        $startDate = new DateTime($start_date);
                        $endDate = new DateTime($end_date);

                        $difference = $endDate->diff($startDate);
                        $scbd = $difference->format("%a") + 1;
                        $DaysLoop = 'P' . $scbd . 'D';

                        // Given two Month data
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday")->add(new DateInterval($DaysLoop))
                        );
                        $WStartDate = array();
                        $WEndDate = array();
                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('W') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('W');
                                $dateByWeek[$Week][] = $d->format('Y-m-d');
                            }
                            $EndDate = $d->format('Y-m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $result2 = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0] . '-' . $value[1];
                            $AssessmentCount[] = $this->Reports_manager_adoption_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id, $mapped_user_id);
                            $MappedUserCount[] = $this->Reports_manager_adoption_model->raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                            $result = $AssessmentCount;
                            $result2 = $MappedUserCount;
                        }
                    } else {
                        $result = '';
                    }
                    $recount = count((array)$result) - 1;
                    $weekprint = 1;
                    for ($i = 0; $i <= $recount; $i++) {
                        if (!empty($result2[$i])) {
                            $mapped_dataset[] = json_encode($result2[$i], JSON_NUMERIC_CHECK);
                        } else {
                            $mapped_dataset[] = 0;
                        }
                        if (!empty($result[$i]['played'])) {
                            $index_dataset[] = json_encode($result[$i]['played'], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        if (!empty($result[$i]['completed'])) {
                            $completed_dataset[] = json_encode($result[$i]['completed'], JSON_NUMERIC_CHECK);
                        } else {
                            $completed_dataset[] = 0;
                        }
                        $index_label[] =  "Week " . $weekprint;
                        $weekprint++;
                    }
                } else if ($type >= "60") {
                    // 01-01-2022 to current month
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                    $report_xaxis_title = 'Yearly';
                    $Day_type = "365_days";
                    $AssessmentCount = $this->Reports_manager_adoption_model->RapsPlayedComplted($SDate, $EDate, $Day_type, $Company_id, $mapped_user_id);
                    $MappedUserCount = $this->Reports_manager_adoption_model->MappedUsers($SDate, $EDate, $Day_type, $Company_id, $manager_id);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                        $day = $i->format("m-Y");
                        $month = $i->format("M");
                        if (isset($MappedUserCount['mapped'][$day])) {
                            $mapped_dataset[] = json_encode($MappedUserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $mapped_dataset[] = 0;
                        }
                        if (isset($AssessmentCount['played'][$day])) {
                            $index_dataset[] = json_encode($AssessmentCount['played'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        if (isset($AssessmentCount['completed'][$day])) {
                            $completed_dataset[] = json_encode($AssessmentCount['completed'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $completed_dataset[] = 0;
                        }
                        $index_label[] = $month;
                    }
                }
                // custom Datepicker end
            }
        }
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['mapped_dataset'] = json_encode($mapped_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['completed_dataset'] = json_encode($completed_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $Played_Raps_Completed = $this->load->view('reports_manager_adoption/PlayedCompletedGraph', $Rdata, true);
        $data['Played_Raps_Completed'] = $Played_Raps_Completed;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // get reps played and complted End


    // Total Reports Sent
    public function total_report_sent($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('Manager_adoption_model');
        $start_date = $this->input->post('StartDate', true);
        $manager_id = $this->mw_session['user_id'];
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));

        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);
        $type  = $date2->diff($date1)->format('%a');

        $current_month = date('m');
        $lastDayThisMonth = date("Y-m-t");
        $report_data = array();
        $index_dataset = array();
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $newyear = date('Y');
        $Week = $this->input->post('week', true);
        $userDetails = $this->Reports_manager_adoption_model->usersmangerwise($manager_id);
        $len = count($userDetails);
        if ($len == '0') {
            $truefalse = '0';
            $index_dataset[] = '0';
            $index_label[] = '0';
            $report_title = '';
            $report_xaxis_title='';
        } 
        else 
        {
            for ($i = 0; $i < $len; $i++) {

                $userId[] = isset($userDetails[$i]['user_id']) ? $userDetails[$i]['user_id'] : '0';
            }
            if ($IsCustom == '' or $IsCustom == 'Current Year') {
                // Return Current year
                $StartStrDt = $newyear . '-01-01';
                $EndDate = $newyear . '-12-31';

                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = 'current';
                $total_reports_sent = $this->Reports_manager_adoption_model->total_reports_sent_manager($StartStrDt, $EndDate, $Day_type, $Company_id, $userId);

                for ($i = 1; $i <= $current_month; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-' . $day . '-01');
                    if (isset($total_reports_sent['period'][$i])) {
                        $index_dataset[] = json_encode($total_reports_sent['period'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("M", strtotime($TempDate));
                }
                // print_r($index_label);exit;
            } elseif ($IsCustom == "Last 7 Days") {
                // Last 7 days  
                $StartStrDt = date('Y-m-d', strtotime("-6 days"));
                $StartWeek = date('d', strtotime("-6 days"));

                $Edate = date('Y-m-d');
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));
                $EndWeek = date('d');

                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1W'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();
                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('D') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('D');
                            $dateByWeek[$Week][] = $d->format('d-m');
                        }
                        $EndDate = $d->format('m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d-m', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0];
                        $result = $weeksteddate;
                    }
                } else {
                    $result = '';
                }
                $report_xaxis_title = 'Last 7 Days';
                $Day_type = "7_days";
                $total_reports_sent = $this->Reports_manager_adoption_model->total_reports_sent_manager($StartStrDt, $Edate, $Day_type, $Company_id, $userId);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($Edate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($total_reports_sent['period'][$day])) {
                        $index_dataset[] = json_encode($total_reports_sent['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                }
                $index_label = $weeksteddate;
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($Edate)) . '';
                // Last 7 days  
            } elseif ($IsCustom == "Last 30 Days") {
                // Last 29 days 
                $StartStrDt = date('Y-m-d', strtotime("-29 days"));
                $StartWeek = date('d', strtotime("-29 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 30 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Get week
                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1M'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $total_reports_sent[] = $this->Reports_manager_adoption_model->total_reports_sent_manager_last_30_60($WStartDate, $WEndDate, $Company_id, $userId);
                        $result = $total_reports_sent;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($total_reports_sent[$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                    // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
                }
                // Last 29 days 
            } elseif ($IsCustom == "Last 60 Days") {
                // Last 60 days
                $StartStrDt = date('Y-m-d', strtotime("-59 days"));
                $OldDay = date('d', strtotime("-59 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 60 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                //Get Week
                $result = '';
                if ($year != '' && $month != '' && $OldDay != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay")->add(new DateInterval('P2M'))
                    );

                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));

                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $total_reports_sent[] = $this->Reports_manager_adoption_model->total_reports_sent_manager_last_30_60($WStartDate, $WEndDate, $Company_id, $userId);
                        $result = $total_reports_sent;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($total_reports_sent[$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    // $weekprint++;
                    // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
                }
                //Last 60 days
            } elseif ($IsCustom == "Last 90 Days") {
                $StartStrDt = date('Y-m-d', strtotime("-89 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Last 90 Days';
                $Day_type = "90_days";
                $total_reports_sent = $this->Reports_manager_adoption_model->total_reports_sent_manager($StartStrDt, $EndDtdate, $Day_type, $Company_id, $userId);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);

                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("n");
                    $month = $i->format("M");
                    if (isset($total_reports_sent['period'][$day])) {
                        $index_dataset[] = json_encode($total_reports_sent['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            } elseif ($IsCustom == "Last 365 Days") {
                // Last 365 days
                $StartStrDt = date('Y-m-d', strtotime("-365 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $total_reports_sent = $this->Reports_manager_adoption_model->total_reports_sent_manager($StartStrDt, $EndDtdate, $Day_type, $Company_id, $userId);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($total_reports_sent['period'][$day])) {
                        $index_dataset[] = json_encode($total_reports_sent['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Last 365 days
            } else {
                // Custom Datepicker
                if ($type <= "15") {
                    // 7 to 15 days data
                    $day = date('d', strtotime($SDate));
                    $month = date('m', strtotime($SDate));
                    $year = date('Y', strtotime($SDate));
                    $EndWeek = date('d');
                    $EDate = date('Y-m-d', strtotime($end_date));
                    $result = '';
                    if ($year != '' && $month != '' && $day != '') {
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P15D'))
                        );
                        $WStartDate = array();
                        $WEndDate = array();
                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('D') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('D');
                                $dateByWeek[$Week][] = $d->format('d-m');
                            }
                            $EndDate = $d->format('m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d-m', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0];

                            $result = $weeksteddate;
                        }
                    } else {
                        $result = '';
                    }
                    $report_xaxis_title = '3 to 15 Days';
                    $Day_type = '7_days';
                    $total_reports_sent = $this->Reports_manager_adoption_model->total_reports_sent_manager($SDate, $EDate, $Day_type, $Company_id, $userId);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                        $day = $i->format("d");
                        if (isset($total_reports_sent['period'][$day])) {
                            $index_dataset[] = json_encode($total_reports_sent['period'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                    }
                    $index_label = $weeksteddate;
                    $report_title = 'From ' . date('d-m-Y', strtotime($SDate)) . ' To ' . date('d-m-Y', strtotime($EDate)) . '';
                    // Last 7 days   
                } else if ($type >= "15" && $type <= "61") {
                    // One month or Two Month Logic
                    $startday = date('d', strtotime($start_date));
                    $startmonth = date('m', strtotime($start_date));
                    $startyear = date('Y', strtotime($start_date));
                    $EndWeek = date('d');
                    $EndDtdate = date('Y-m-d');
                    $report_xaxis_title = '15 to 30 Days';
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';

                    $result = '';
                    if ($startyear != '' && $startmonth != '' && $startday != '') {
                        $startDate = new DateTime($start_date);
                        $endDate = new DateTime($end_date);
                        $difference = $endDate->diff($startDate);
                        $scbd = $difference->format("%a") + 1;
                        $DaysLoop = 'P' . $scbd . 'D';
                        // Given two Month data
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday")->add(new DateInterval($DaysLoop))
                        );
                        $WStartDate = array();
                        $WEndDate = array();
                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('W') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('W');
                                $dateByWeek[$Week][] = $d->format('Y-m-d');
                            }
                            $EndDate = $d->format('Y-m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0] . '-' . $value[1];
                            $total_reports_sent[] = $this->Reports_manager_adoption_model->total_reports_sent_manager_last_30_60($WStartDate, $WEndDate, $Company_id, $userId);
                            $result = $total_reports_sent;
                        }
                    } else {
                        $result = '';
                    }
                    $recount = count((array)$result) - 1;
                    $weekprint = 1;
                    for ($i = 0; $i <= $recount; $i++) {
                        if (!empty($result[$i])) {
                            $index_dataset[] = json_encode($total_reports_sent[$i], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        $index_label[] =  "Week " . $weekprint;
                        $weekprint++;
                    }
                } else if ($type >= "60") {
                    // 01-01-2022 to current month
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                    $report_xaxis_title = 'Yearly';
                    $Day_type = "365_days";
                    $total_reports_sent = $this->Reports_manager_adoption_model->total_reports_sent_manager($SDate, $EDate, $Day_type, $Company_id, $userId);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                        $day = $i->format("m-Y");
                        $month = $i->format("M");
                        if (isset($total_reports_sent['period'][$day])) {
                            $index_dataset[] = json_encode($total_reports_sent['period'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        $index_label[] = $month;
                    }
                }
                // custom Datepicker end
            }

            //Monthly Count
            $monthstartdate = date('Y-m-01');
            $monthenddate  = date('Y-m-t');
            $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
            $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
            $total_reports_sent = $this->Reports_manager_adoption_model->Month_Wise_Count_Send_manager($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $userId);

            $latestmonth = isset($total_reports_sent['Latestmonth'][0]) ? $total_reports_sent['Latestmonth'][0]['currentmonth'] : 0;
            $oldmonth = isset($total_reports_sent['Oldmonth'][0]) ? $total_reports_sent['Oldmonth'][0]['months'] : 0;

            if (!empty($latestmonth) == 0) {
                $latestmonth = 0;
            }

            if (!empty($oldmonth) == 0) {
                $oldmonth = 0;
            }

            if ($oldmonth > $latestmonth) {
                $newcount = $oldmonth - $latestmonth;
            } else {
                $newcount = $latestmonth - $oldmonth;
            }

            if ($latestmonth < $oldmonth) {
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '  <span style="style="color:#fc0303; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; "> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth > $oldmonth) {
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold;"> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth == 0 or $oldmonth == 0) {
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
            } else {
                $truefalse = '';
            }
        }
       
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $total_report_sent = $this->load->view('reports_manager_adoption/total_report_sent', $Rdata, true);
        $data['total_report_sent'] = $total_report_sent;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    //End Here

    // Total Questions Mapped
    public function total_questions_mapped($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('Manager_adoption_model');
        $start_date = $this->input->post('StartDate', true);
        $manager_id = $this->mw_session['user_id'];
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));

        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);
        $type  = $date2->diff($date1)->format('%a');

        $current_month = date('m');
        $lastDayThisMonth = date("Y-m-t");
        $report_data = array();
        $index_dataset = array();
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $newyear = date('Y');
        $Week = $this->input->post('week', true);
        $AssesmentDetails = $this->Reports_manager_adoption_model->assessmentmangerwise($manager_id);
        $len = count($AssesmentDetails);
        if ($len == '0') {
            $truefalse = '0';
            $index_dataset[] = '0';
            $index_label[] = '0';
            $report_title = '';
            $report_xaxis_title='';
        } 
        else 
        {
            // for ($i = 0; $i < $len; $i++) {

            //     $Assessment_Id[] = isset($AssesmentDetails[$i]['assessment_id']) ? $AssesmentDetails[$i]['assessment_id'] : '0';
            // }
            if ($IsCustom == '' or $IsCustom == 'Current Year') {
                // Return Current year
                $StartStrDt = $newyear . '-01-01';
                $EndDate = $newyear . '-12-31';

                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = 'current';
                $total_question_mapped = $this->Reports_manager_adoption_model->total_questions_mapped($StartStrDt, $EndDate, $Day_type, $Company_id,$manager_id);
                

                for ($i = 1; $i <= $current_month; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-' . $day . '-01');
                    if (isset($total_question_mapped['questions'][$i])) {
                        $index_dataset[] = json_encode($total_question_mapped['questions'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("M", strtotime($TempDate));
                }
                // print_r($index_label);exit;
            } elseif ($IsCustom == "Last 7 Days") {
                 // Last 7 days  
                $StartStrDt = date('Y-m-d', strtotime("-6 days"));
                $StartWeek = date('d', strtotime("-6 days"));

                $Edate = date('Y-m-d');
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));
                $EndWeek = date('d');

                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1W'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();
                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('D') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('D');
                            $dateByWeek[$Week][] = $d->format('d-m');
                        }
                        $EndDate = $d->format('m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d-m', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0];
                        $result = $weeksteddate;
                    }
                } else {
                    $result = '';
                }
                $report_xaxis_title = 'Last 7 Days';
                $Day_type = "7_days";
                $total_question_mapped = $this->Reports_manager_adoption_model->total_questions_mapped($StartStrDt, $Edate, $Day_type, $Company_id,$manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($Edate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($total_question_mapped['questions'][$day])) {
                        $index_dataset[] = json_encode($total_question_mapped['questions'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                }
                $index_label = $weeksteddate;
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($Edate)) . '';
              // Last 7 days  
            } elseif ($IsCustom == "Last 30 Days") {
                // Last 29 days 
                $StartStrDt = date('Y-m-d', strtotime("-29 days"));
                $StartWeek = date('d', strtotime("-29 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 30 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Get week
                $result = '';
                if ($year != '' && $month != '' && $StartWeek != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1M'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $total_question_mapped[] = $this->Reports_manager_adoption_model->total_questions_mapped_30_60($WStartDate, $WEndDate, $Company_id,$manager_id);
                        $result = $total_question_mapped;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($total_question_mapped[$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                    // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
                }
                // Last 29 days 
            } elseif ($IsCustom == "Last 60 Days") {
                // Last 60 days
                $StartStrDt = date('Y-m-d', strtotime("-59 days"));
                $OldDay = date('d', strtotime("-59 days"));
                $month = date('m', strtotime($StartStrDt));
                $year = date('Y', strtotime($StartStrDt));

                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = 'Last 60 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                //Get Week
                $result = '';
                if ($year != '' && $month != '' && $OldDay != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay")->add(new DateInterval('P2M'))
                    );

                    $WStartDate = array();
                    $WEndDate = array();

                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();

                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));

                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $total_question_mapped[] = $this->Reports_manager_adoption_model->total_questions_mapped_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                        $result = $total_question_mapped;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($total_question_mapped[$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    // $weekprint++;
                    // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
                }
                //Last 60 days
            } elseif ($IsCustom == "Last 90 Days") {
                $StartStrDt = date('Y-m-d', strtotime("-89 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Last 90 Days';
                $Day_type = "90_days";
                $total_question_mapped = $this->Reports_manager_adoption_model->total_questions_mapped($StartStrDt, $EndDtdate, $Day_type, $Company_id,$manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);

                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("n");
                    $month = $i->format("M");
                    if (isset($total_question_mapped['questions'][$day])) {
                        $index_dataset[] = json_encode($total_question_mapped['questions'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            } elseif ($IsCustom == "Last 365 Days") {
                // Last 365 days
                $StartStrDt = date('Y-m-d', strtotime("-365 days"));
                $EndDtdate = date('Y-m-d');

                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $total_question_mapped = $this->Reports_manager_adoption_model->total_questions_mapped($StartStrDt, $EndDtdate, $Day_type, $Company_id,$manager_id);
                $begin = new DateTime($StartStrDt);
                $end   = new DateTime($EndDtdate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($total_question_mapped['questions'][$day])) {
                        $index_dataset[] = json_encode($total_question_mapped['questions'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
                $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
                // Last 365 days
            } else {
                // Custom Datepicker
                if ($type <= "15") {
                    // 7 to 15 days data
                    $day = date('d', strtotime($SDate));
                    $month = date('m', strtotime($SDate));
                    $year = date('Y', strtotime($SDate));
                    $EndWeek = date('d');
                    $EDate = date('Y-m-d', strtotime($end_date));
                    $result = '';
                    if ($year != '' && $month != '' && $day != '') {
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P15D'))
                        );
                        $WStartDate = array();
                        $WEndDate = array();
                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('D') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('D');
                                $dateByWeek[$Week][] = $d->format('d-m');
                            }
                            $EndDate = $d->format('m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d-m', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0];

                            $result = $weeksteddate;
                        }
                    } else {
                        $result = '';
                    }
                    $report_xaxis_title = '3 to 15 Days';
                    $Day_type = '7_days';
                    $total_question_mapped = $this->Reports_manager_adoption_model->total_questions_mapped($SDate, $EDate, $Day_type, $Company_id, $manager_id);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                        $day = $i->format("d");
                        if (isset($total_question_mapped['questions'][$day])) {
                            $index_dataset[] = json_encode($total_question_mapped['questions'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                    }
                    $index_label = $weeksteddate;
                    $report_title = 'From ' . date('d-m-Y', strtotime($SDate)) . ' To ' . date('d-m-Y', strtotime($EDate)) . '';
                    // Last 7 days   
                } else if ($type >= "15" && $type <= "61") {
                    // One month or Two Month Logic
                    $startday = date('d', strtotime($start_date));
                    $startmonth = date('m', strtotime($start_date));
                    $startyear = date('Y', strtotime($start_date));
                    $EndWeek = date('d');
                    $EndDtdate = date('Y-m-d');
                    $report_xaxis_title = '15 to 30 Days';
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';

                    $result = '';
                    if ($startyear != '' && $startmonth != '' && $startday != '') {
                        $startDate = new DateTime($start_date);
                        $endDate = new DateTime($end_date);
                        $difference = $endDate->diff($startDate);
                        $scbd = $difference->format("%a") + 1;
                        $DaysLoop = 'P' . $scbd . 'D';
                        // Given two Month data
                        $p = new DatePeriod(
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday"),
                            new DateInterval('P1D'),
                            DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday")->add(new DateInterval($DaysLoop))
                        );
                        $WStartDate = array();
                        $WEndDate = array();
                        $Week = 0;
                        $WeekStr = '';
                        $EndDate = '';
                        $i = 0;
                        foreach ($p as $d) {
                            $i++;
                            if ($d->format('W') != $WeekStr) {
                                if ($EndDate != "") {
                                    $dateByWeek[$Week][] = $EndDate;
                                }
                                $Week++;
                                $WeekStr = $d->format('W');
                                $dateByWeek[$Week][] = $d->format('Y-m-d');
                            }
                            $EndDate = $d->format('Y-m-d');
                        }
                        $dateByWeek[$Week][] = $EndDate;
                        $result = array();
                        $StdWeek = array();
                        $EndWeek = array();
                        foreach ($dateByWeek as $value) {
                            $WStartDate = $value[0];
                            $WEndDate =  $value[1];
                            $StdWeek[] = date('d', strtotime($WStartDate));
                            $EndWeek[] = date('d', strtotime($WEndDate));
                            $weeksteddate[] = $value[0] . '-' . $value[1];
                            $total_question_mapped[] = $this->Reports_manager_adoption_model->total_questions_mapped_30_60($WStartDate, $WEndDate, $Company_id, $manager_id);
                            $result = $total_question_mapped;
                        }
                    } else {
                        $result = '';
                    }
                    $recount = count((array)$result) - 1;
                    $weekprint = 1;
                    for ($i = 0; $i <= $recount; $i++) {
                        if (!empty($result[$i])) {
                            $index_dataset[] = json_encode($total_question_mapped[$i], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        $index_label[] =  "Week " . $weekprint;
                        $weekprint++;
                    }
                } else if ($type >= "60") {
                    // 01-01-2022 to current month
                    $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                    $report_xaxis_title = 'Yearly';
                    $Day_type = "365_days";
                    $total_question_mapped = $this->Reports_manager_adoption_model->total_questions_mapped($SDate, $EDate, $Day_type, $Company_id, $manager_id);
                    $begin = new DateTime($SDate);
                    $end   = new DateTime($EDate);
                    for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                        $day = $i->format("m-Y");
                        $month = $i->format("M");
                        if (isset($total_question_mapped['questions'][$day])) {
                            $index_dataset[] = json_encode($total_question_mapped['questions'][$day], JSON_NUMERIC_CHECK);
                        } else {
                            $index_dataset[] = 0;
                        }
                        $index_label[] = $month;
                    }
                }
                // custom Datepicker end
            }

            //Monthly Count
            $monthstartdate = date('Y-m-01');
            $monthenddate  = date('Y-m-t');
            $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
            $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
            $total_question_mapped = $this->Reports_manager_adoption_model->Month_wise_count_questions($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id,$manager_id);
            
            $latestmonth = isset($total_question_mapped['Latestmonth'][0]) ? $total_question_mapped['Latestmonth'][0]['currentmonth'] : 0;
            $oldmonth = isset($total_question_mapped['Oldmonth'][0]) ? $total_question_mapped['Oldmonth'][0]['months'] : 0;

            if (!empty($latestmonth) == 0) {
                $latestmonth = 0;
            }

            if (!empty($oldmonth) == 0) {
                $oldmonth = 0;
            }

            if ($latestmonth < $oldmonth) {
                $newcount = $oldmonth - $latestmonth;
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '  <span style="style="color:#fc0303; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; "> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth > $oldmonth) {
                $newcount = $latestmonth - $oldmonth;
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold;"> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth == $oldmonth AND $latestmonth !='0' and $oldmonth !='0' ) {
                $newcount = $oldmonth;
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold;"> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth == 0 AND $oldmonth == 0) {
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
            } else {
                $truefalse = '';
            }
        }
       
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $total_question_mapped = $this->load->view('reports_manager_adoption/total_question_mapped', $Rdata, true);
        $data['total_question_mapped'] = $total_question_mapped;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // End Here

    //Total reps mapped
    public function get_raps_mapped_user($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('Manager_adoption_model');
        $manager_id = $this->mw_session['user_id'];

        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true);

        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);
        $type  = $date2->diff($date1)->format('%a');

        $current_month = date('m');
        $current_date = date('Y-m-d');
        $lastDayThisMonth = date("Y-m-t");
        $countyear = date('Y');
        $report_data = array();
        $index_dataset = array();
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Week = $this->input->post('week', true);
        $ManagerDetails = $this->Reports_manager_adoption_model->usersmangerwise($manager_id);
        $len = count($ManagerDetails);
        if ($len == '0') {
            $truefalse = '0';
            $index_dataset[] = '0';
            $index_label[] = '0';
            $report_title = '';
            $report_xaxis_title='';
        } 
        else 
        {
        if ($IsCustom == '' or $IsCustom == "Current Year") {
            $YearStartDate = $countyear . '-01-01';
            $YearEndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
     
            $UserCount = $this->Reports_manager_adoption_model->MappedUsers($YearStartDate, $YearEndDate, $Day_type, $Company_id,$manager_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($UserCount['mapped'][$i])) {
                    $index_dataset[] = json_encode($UserCount['mapped'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        } elseif ($IsCustom == "Last 7 Days") {
            // Last 7 days  
            $StartStrDt = date('Y-m-d', strtotime("-6 days"));
            $StartWeek = date('d', strtotime("-6 days"));

            $Edate = date('Y-m-d');
            $month = date('m', strtotime($StartStrDt));
            $year = date('Y', strtotime($StartStrDt));
            $EndWeek = date('d');

            $result = '';
            if ($year != '' && $month != '' && $StartWeek != '') {
                $p = new DatePeriod(
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                    new DateInterval('P1D'),
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1W'))
                );
                $WStartDate = array();
                $WEndDate = array();
                $Week = 0;
                $WeekStr = '';
                $EndDate = '';
                $i = 0;
                foreach ($p as $d) {
                    $i++;
                    if ($d->format('D') != $WeekStr) {
                        if ($EndDate != "") {
                            $dateByWeek[$Week][] = $EndDate;
                        }
                        $Week++;
                        $WeekStr = $d->format('D');
                        $dateByWeek[$Week][] = $d->format('d-m');
                    }
                    $EndDate = $d->format('m-d');
                }
                $dateByWeek[$Week][] = $EndDate;
                $result = array();
                $StdWeek = array();
                $EndWeek = array();
                foreach ($dateByWeek as $value) {
                    $WStartDate = $value[0];
                    $WEndDate =  $value[1];
                    $StdWeek[] = date('d-m', strtotime($WStartDate));
                    $EndWeek[] = date('d', strtotime($WEndDate));
                    $weeksteddate[] = $value[0];
                    $result = $weeksteddate;
                }
            } else {
                $result = '';
            }
            $report_xaxis_title = 'Last 7 Days';
            $Day_type = "7_days";
            $UserCount = $this->Reports_manager_adoption_model->MappedUsers($StartStrDt, $Edate, $Day_type, $Company_id,$manager_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($UserCount['mapped'][$day])) {
                    $index_dataset[] = json_encode($UserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
            }
            $index_label = $weeksteddate;
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($Edate)) . '';
            // Last 7 days  
        } elseif ($IsCustom == "Last 30 Days") {
            // Last 29 days 
            $StartStrDt = date('Y-m-d', strtotime("-29 days"));
            $StartWeek = date('d', strtotime("-29 days"));
            $month = date('m', strtotime($StartStrDt));
            $year = date('Y', strtotime($StartStrDt));

            $EndWeek = date('d');
            $EndDtdate = date('Y-m-d');
            $report_xaxis_title = 'Last 30 Days';
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            // Get week
            $result = '';
            if ($year != '' && $month != '' && $StartWeek != '') {
                $p = new DatePeriod(
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek"),
                    new DateInterval('P1D'),
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-$StartWeek")->add(new DateInterval('P1M'))
                );
                $WStartDate = array();
                $WEndDate = array();

                $Week = 0;
                $WeekStr = '';
                $EndDate = '';
                $i = 0;
                foreach ($p as $d) {
                    $i++;
                    if ($d->format('W') != $WeekStr) {
                        if ($EndDate != "") {
                            $dateByWeek[$Week][] = $EndDate;
                        }
                        $Week++;
                        $WeekStr = $d->format('W');
                        $dateByWeek[$Week][] = $d->format('Y-m-d');
                    }
                    $EndDate = $d->format('Y-m-d');
                }
                $dateByWeek[$Week][] = $EndDate;
                $result = array();
                $StdWeek = array();
                $EndWeek = array();

                foreach ($dateByWeek as $value) {
                    $WStartDate = $value[0];
                    $WEndDate =  $value[1];
                    $StdWeek[] = date('d', strtotime($WStartDate));
                    $EndWeek[] = date('d', strtotime($WEndDate));
                    $weeksteddate[] = $value[0] . '-' . $value[1];
                    $UserCount[] = $this->Reports_manager_adoption_model->raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id,$manager_id);
                    $result = $UserCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {

                if (!empty($result[$i]['mapped'])) {
                    $index_dataset[] = json_encode($UserCount[$i]['mapped'], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            // Last 29 days 
        } elseif ($IsCustom == "Last 60 Days") {
            // Last 60 days
            $StartStrDt = date('Y-m-d', strtotime("-59 days"));
            $OldDay = date('d', strtotime("-59 days"));
            $month = date('m', strtotime($StartStrDt));
            $year = date('Y', strtotime($StartStrDt));

            $EndWeek = date('d');
            $EndDtdate = date('Y-m-d');
            $report_xaxis_title = 'Last 60 Days';
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            //Get Week
            $result = '';
            if ($year != '' && $month != '' && $OldDay != '') {
                $p = new DatePeriod(
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay"),
                    new DateInterval('P1D'),
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-$OldDay")->add(new DateInterval('P2M'))
                );

                $WStartDate = array();
                $WEndDate = array();

                $Week = 0;
                $WeekStr = '';
                $EndDate = '';
                $i = 0;
                foreach ($p as $d) {
                    $i++;
                    if ($d->format('W') != $WeekStr) {
                        if ($EndDate != "") {
                            $dateByWeek[$Week][] = $EndDate;
                        }
                        $Week++;
                        $WeekStr = $d->format('W');
                        $dateByWeek[$Week][] = $d->format('Y-m-d');
                    }
                    $EndDate = $d->format('Y-m-d');
                }
                $dateByWeek[$Week][] = $EndDate;
                $result = array();
                $StdWeek = array();
                $EndWeek = array();

                foreach ($dateByWeek as $value) {
                    $WStartDate = $value[0];
                    $WEndDate =  $value[1];
                    $StdWeek[] = date('d', strtotime($WStartDate));

                    $EndWeek[] = date('d', strtotime($WEndDate));
                    $weeksteddate[] = $value[0] . '-' . $value[1];
                    $UserCount[] = $this->Reports_manager_adoption_model->raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id,$manager_id);
                    $result = $UserCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i]['mapped'])) {
                    $index_dataset[] = json_encode($UserCount[$i]['mapped'], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                //  $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            //Last 60 days
        } elseif ($IsCustom == "Last 90 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-89 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Last 90 Days';
            $Day_type = "90_days";
            $UserCount = $this->Reports_manager_adoption_model->MappedUsers($StartStrDt, $EndDtdate, $Day_type, $Company_id,$manager_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($UserCount['mapped'][$day])) {
                    $index_dataset[] = json_encode($UserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = $month;
            }
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
        } elseif ($IsCustom == "Last 365 Days") {
            // Last 365 days
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $UserCount = $this->Reports_manager_adoption_model->MappedUsers($StartStrDt, $EndDtdate, $Day_type, $Company_id,$manager_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($UserCount['mapped'][$day])) {
                    $index_dataset[] = json_encode($UserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = $month;
            }
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            // Last 365 days
        }
        // Custom Datepicker
        else {
            if ($type <= "15") {
                // 7 to 15 days data
                $day = date('d', strtotime($SDate));
                $month = date('m', strtotime($SDate));
                $year = date('Y', strtotime($SDate));
                $EndWeek = date('d');
                $EDate = date('Y-m-d', strtotime($end_date));
                $result = '';
                if ($year != '' && $month != '' && $day != '') {
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$day"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P15D'))
                    );
                    $WStartDate = array();
                    $WEndDate = array();
                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('D') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('D');
                            $dateByWeek[$Week][] = $d->format('d-m');
                        }
                        $EndDate = $d->format('m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d-m', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0];
                        $result = $weeksteddate;
                    }
                } else {
                    $result = '';
                }
                $report_xaxis_title = '1 to 15 Days';
                $Day_type = '7_days';
                $UserCount = $this->Reports_manager_adoption_model->MappedUsers($SDate, $EDate, $Day_type, $Company_id,$manager_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($UserCount['mapped'][$day])) {
                        $index_dataset[] = json_encode($UserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                }
                $index_label = $weeksteddate;
                $report_title = 'From ' . date('d-m-Y', strtotime($SDate)) . ' To ' . date('d-m-Y', strtotime($EDate)) . '';
                // Last 7 days   
            } else if ($type >= "15" && $type <= "61") {
                // One month or Two Month Logic
                $startday = date('d', strtotime($start_date));
                $startmonth = date('m', strtotime($start_date));
                $startyear = date('Y', strtotime($start_date));
                $start_date;
                $EndWeek = date('d');
                $EndDtdate = date('Y-m-d');
                $report_xaxis_title = '15 to 30 Days';
                $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';

                $result = '';
                if ($startyear != '' && $startmonth != '' && $startday != '') {
                    $startDate = new DateTime($start_date);
                    $endDate = new DateTime($end_date);

                    $difference = $endDate->diff($startDate);
                    $scbd = $difference->format("%a") + 1;
                    $DaysLoop = 'P' . $scbd . 'D';

                    // Given two Month data
                    $p = new DatePeriod(
                        DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday"),
                        new DateInterval('P1D'),
                        DateTime::createFromFormat('!Y-n-d', "$startyear-$startmonth-$startday")->add(new DateInterval($DaysLoop))
                    );
                    $WStartDate = array();
                    $WEndDate = array();
                    $Week = 0;
                    $WeekStr = '';
                    $EndDate = '';
                    $i = 0;
                    foreach ($p as $d) {
                        $i++;
                        if ($d->format('W') != $WeekStr) {
                            if ($EndDate != "") {
                                $dateByWeek[$Week][] = $EndDate;
                            }
                            $Week++;
                            $WeekStr = $d->format('W');
                            $dateByWeek[$Week][] = $d->format('Y-m-d');
                        }
                        $EndDate = $d->format('Y-m-d');
                    }
                    $dateByWeek[$Week][] = $EndDate;
                    $result = array();
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $UserCount[] = $this->Reports_manager_adoption_model->raps_mapped_user_30_60($WStartDate, $WEndDate, $Company_id,$manager_id);
                        $result = $UserCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result['mapped'][$i])) {
                        $index_dataset[] = json_encode($UserCount['mapped'][$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                }
            } elseif ($type >= "60") {
                // 01-01-2022 to current month
                $end_month = date('m', strtotime($end_date));
                $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $UserCount = $this->Reports_manager_adoption_model->MappedUsers($SDate, $EDate, $Day_type, $Company_id,$manager_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($UserCount['mapped'][$day])) {
                        $index_dataset[] = json_encode($UserCount['mapped'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
            }
            // custom Datepicker end
        }

        
        // Monthly Count
            $monthstartdate = date('Y-m-01');
            $monthenddate  = date('Y-m-t');
            $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
            $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
            $total_reps_mapped_monthly = $this->Reports_manager_adoption_model->total_users_maped($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id,$manager_id);
            $latestmonth  =  isset($total_reps_mapped_monthly['Latestmonth'][0]) ? $total_reps_mapped_monthly['Latestmonth'][0]['currentmonth'] : 0;
            $oldmonth     =  isset($total_reps_mapped_monthly['Oldmonth'][0]) ? $total_reps_mapped_monthly['Oldmonth'][0]['months'] : 0;

            if (!empty($latestmonth) == 0) {
                $latestmonth = 0;
            }

            if (!empty($oldmonth) == 0) {
                $oldmonth = 0;
            }

            if ($latestmonth < $oldmonth) {
                $newcount = $oldmonth - $latestmonth;
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '  <span style="style="color:#fc0303; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; "> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth > $oldmonth) {
                $newcount = $latestmonth - $oldmonth;
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold;"> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth == $oldmonth AND $latestmonth !='0' and $oldmonth !='0' ) {
                $newcount = $oldmonth;
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold;"> ' . $newcount . '  from last month</span></div>';
            } elseif ($latestmonth == 0 AND $oldmonth == 0) {
                $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
            } else {
                $truefalse = '';
            }
        }
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $Raped_userGraph = $this->load->view('reports_manager_adoption/reps_mapped_user', $Rdata, true);
        $data['map_user'] = $Raped_userGraph;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // End here

    // Adoption By Reps 
    public function manager_wise_users()
    {
        $assessment_html = '';
        $manager_id = $this->mw_session['user_id'];
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $assessment_list = $this->Reports_manager_adoption_model->assessment_wise_manager($assessment_id,$manager_id);
        $assessment_html .= '<option value="">';
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->user_id . '">[' . $value->user_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['assessment_list_data']  = $assessment_html;
        echo json_encode($data);
    }

    public function adoption_by_reps($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $manager_id = $this->mw_session['user_id'];
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $user_id = $this->input->post('manager_id', TRUE);
        $this->load->model('Manager_adoption_model');
        $report_data = array();
        $index_dataset = array();
        $users_id = array();
        $report_title = '';
        $Userstarted = array();
        $Usercompleted = array();

        $ManagerDetails = $this->Reports_manager_adoption_model->usersmangerwise($manager_id);
        $len = count($ManagerDetails);
        if ($len == '0') {
            $report_title = '';
            $index_dataset[] = '0';
            $Userstarted[] = '0';
            $Usercompleted[] = '0';
        } 
        else 
        {
            if($Assessment_id==''){
                $CurrentDate = date("Y-m-d h:i:s");
                $LAssessmentDetails = $this->Reports_manager_adoption_model->LastExpiredAssessment($CurrentDate,$Company_id,$manager_id);
                $report_title = $LAssessmentDetails[0]['assessment'];
                $Assessment_id = $LAssessmentDetails[0]['id'];
                $username= $this->Reports_manager_adoption_model->get_user_name_last($Assessment_id,$manager_id);
                $looparray = count($username);
                for ($i = 0; $i < $looparray; $i++) {
                    $users_id[] =    isset($username[$i]['user_id']) ? $username[$i]['user_id'] : 'Empty Data';
                }
                $getUserstart = $this->Reports_manager_adoption_model->Getplayed_complted_managerwise($Assessment_id,$Company_id,$manager_id,$users_id);
                $count_user=count($getUserstart);
                for ($i = 0; $i < $count_user; $i++) {
                    $index_dataset[] =    isset($username[$i]['user_name']) ? $username[$i]['user_name'] : 'Empty Data';
                    $Userstarted[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $Usercompleted[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else{
                $customTitle = $this->Reports_manager_adoption_model->GetAssessmentName($Assessment_id);
                $report_title = $customTitle[0]['assessment'];
                $username= $this->Reports_manager_adoption_model->get_user_name($Assessment_id,$manager_id,$user_id);
                $getUserstart = $this->Reports_manager_adoption_model->Getplayed_complted_managerwise($Assessment_id,$Company_id,$manager_id,$user_id);
                $new = count($user_id);
                for ($i = 0; $i < $new; $i++) {
                        $index_dataset[] =    isset($username[$i]['user_name']) ? $username[$i]['user_name'] : 'Empty Data';
                        $Userstarted[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                        $Usercompleted[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            }
        }
        
        $data['report'] = $report_data; 
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($Userstarted, JSON_NUMERIC_CHECK);
        $Rdata['user_completed'] = json_encode($Usercompleted, JSON_NUMERIC_CHECK);

        $ad_by_Reps = $this->load->view('reports_manager_adoption/AdoptionByReps', $Rdata, true);
        $data['adoption_by_reps'] = $ad_by_Reps;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

}