<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('admin_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('admin_dashboard_model');
    }

    public function index()
    {
        $data['module_id'] = '44.06';
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

        // $data['region_data'] = $this->admin_dashboard_model->get_trainee_region($data['company_id']);

        $data['start_date'] = date('d-M-Y', strtotime('-6 days'));
        $data['end_date'] = date("d-m-Y");
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date("Y-m-d");

        //Added

        $data['company_id'] = $this->mw_session['company_id'];
        $company_id = $data['company_id'];
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="2" AND company_id="' . $company_id . '"');

        //$assessment_list= $this->admin_dashboard_model->get_assessment_list($company_id, $trainer_id, $start_date, $end_date);


        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');

        //----------------
        // $data['assessment_data'] = $this->admin_dashboard_model->get_assessment($data['company_id'], '', $start_date, $end_date);

        // $data['parameter_data'] = $this->admin_dashboard_model->get_parameter();
        $this->load->view('admin_dashboard/index', $data);
    }

    public function ajax_getWeeks()
    {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    // Assessment Started Graph 
    public function assessment_started($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('admin_dashboard_model');
        
        $start_date = $this->input->post('StartDate', true);
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


        if ($IsCustom == '' or $IsCustom == 'Current Year') {
            // Return Current year
            $YearStartDate = $newyear . '-01-01';
            $YearEndDate = $newyear . '-12-31';
            $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'curremt';
            $AssessmentCount = $this->admin_dashboard_model->assessment_started($YearStartDate, $YearEndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($AssessmentCount['period'][$i])) {
                    $index_dataset[] = json_encode($AssessmentCount['period'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        } else if ($IsCustom == "Last 7 Days") {
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
            $AssessmentCount = $this->admin_dashboard_model->assessment_started($StartStrDt, $Edate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($AssessmentCount['period'][$day])) {
                    $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
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
                    $AssessmentCount[] = $this->admin_dashboard_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id);
                    $result = $AssessmentCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {

                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($AssessmentCount[$i], JSON_NUMERIC_CHECK);
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
                    $startmonthdate = date('D', strtotime($value[0]));
                    $endmonthdate = date('D', strtotime($value[1]));
                    $AssessmentCount[] = $this->admin_dashboard_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id);
                    $result = $AssessmentCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($AssessmentCount[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            //Last 60 days
        } elseif ($IsCustom == "Last 90 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-89 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Last 90 Days';
            $Day_type = "90_days";
            $AssessmentCount = $this->admin_dashboard_model->assessment_started($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($AssessmentCount['period'][$day])) {
                    $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
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
            $AssessmentCount = $this->admin_dashboard_model->assessment_started($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($AssessmentCount['period'][$day])) {
                    $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
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
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P2W'))
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
                $AccuracySet = $this->admin_dashboard_model->assessment_started($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
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
                        $AssessmentCount[] = $this->admin_dashboard_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id);
                        $result = $AssessmentCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($AssessmentCount[$i], JSON_NUMERIC_CHECK);
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
                $AssessmentCount = $this->admin_dashboard_model->assessment_started($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($AssessmentCount['period'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
            }
            // custom Datepicker end
        }
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $first_date = strtotime('first day of previous month', time());
        $lastmonthdate = date('Y-m-d', $first_date);
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $Total_assessment_monthly = $this->admin_dashboard_model->total_assessment_monthly($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id);
        $latestmonth = isset($Total_assessment_monthly['Latestmonth'][0]) ? $Total_assessment_monthly['Latestmonth'][0]['currentmonth'] : 0;
        $oldmonth = isset($Total_assessment_monthly['Oldmonth'][0]) ? $Total_assessment_monthly['Oldmonth'][0]['months'] : 0;

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran";  color:black;">' . $latestmonth . ' <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-family: "Poppins",sans-serif; font-weight: bold; font-size: 12px;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth > $oldmonth) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . ' <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span>  <span style="color:green; font-family: "Poppins",sans-serif; font-weight: bold; font-size: 12px;"> ' . $newcount . '   from last month</span>  <i class="fa fa-angle-down"></i> </div>';
        } elseif ($latestmonth == 0 and $oldmonth == 0) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="color:red; font-size: 12px; font-family: "Catamaran"; color:black;"> 0 from last month</span></div>';
        } else {
            $truefalse = '';
        }
        if($truefalse == "0") {
            $Rdata['count'] = 0;
        } else {
            $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        }

        $indexStartGraph = $this->load->view('admin_dashboard/assessment_index_start', $Rdata, true);
        $data['startcount'] = $indexStartGraph;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    //Raps Users Start Here
    public function get_raps_mapped_user($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('admin_dashboard_model');
        
        
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

        if ($IsCustom == '' OR $IsCustom == "Current Year") {
            $StartDate = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'curremt';
            $UserCount = $this->admin_dashboard_model->get_raps_mapped_user($StartDate, $EndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($UserCount['period'][$i])) {
                    $index_dataset[] = json_encode($UserCount['period'][$i], JSON_NUMERIC_CHECK);
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
            $UserCount = $this->admin_dashboard_model->get_raps_mapped_user($StartStrDt, $Edate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($UserCount['period'][$day])) {
                    $index_dataset[] = json_encode($UserCount['period'][$day], JSON_NUMERIC_CHECK);
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
                    $UserCount[] = $this->admin_dashboard_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id);
                    $result = $UserCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($UserCount[$i], JSON_NUMERIC_CHECK);
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
                    $UserCount[] = $this->admin_dashboard_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id);
                    $result = $UserCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($UserCount[$i], JSON_NUMERIC_CHECK);
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
            $UserCount = $this->admin_dashboard_model->get_raps_mapped_user($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($UserCount['period'][$day])) {
                    $index_dataset[] = json_encode($UserCount['period'][$day], JSON_NUMERIC_CHECK);
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
            $UserCount = $this->admin_dashboard_model->get_raps_mapped_user($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($UserCount['period'][$day])) {
                    $index_dataset[] = json_encode($UserCount['period'][$day], JSON_NUMERIC_CHECK);
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
                $UserCount = $this->admin_dashboard_model->get_raps_mapped_user($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate); 
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($UserCount['period'][$day])) {
                        $index_dataset[] = json_encode($UserCount['period'][$day], JSON_NUMERIC_CHECK);
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
                        $UserCount[] = $this->admin_dashboard_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id);
                        $result = $UserCount;
                    }        
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($UserCount[$i], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] =  "Week " . $weekprint;
                    $weekprint++;
                }
            } elseif ($type >= "60"){
                // 01-01-2022 to current month
                $end_month = date('m', strtotime($end_date));
                $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $UserCount = $this->admin_dashboard_model->get_raps_mapped_user($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($UserCount['period'][$day])) {
                        $index_dataset[] = json_encode($UserCount['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
            }
            // custom Datepicker end
        }
        
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $first_date = strtotime('first day of previous month', time());
        $lastmonthdate = date('Y-m-d', $first_date);
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $rap_total_user_monthly = $this->admin_dashboard_model->rap_total_user_monthly($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id);
        $latestmonth  =  isset($rap_total_user_monthly['Latestmonth'][0]) ? $rap_total_user_monthly['Latestmonth'][0]['currentmonth'] : 0;
        $oldmonth     =  isset($rap_total_user_monthly['Oldmonth'][0]) ? $rap_total_user_monthly['Oldmonth'][0]['months'] : 0;

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran";  color:black;">' . $latestmonth . ' <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-family: "Poppins",sans-serif; font-weight: bold; font-size: 12px;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth > $oldmonth) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . ' <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span>  <span style="color:green; font-family: "Poppins",sans-serif; font-weight: bold; font-size: 12px;"> ' . $newcount . '   from last month</span>  <i class="fa fa-angle-down"></i> </div>';
        } elseif ($latestmonth == 0 and $oldmonth == 0) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="color:red; font-size: 12px; font-family: "Catamaran"; color:black;"> 0 from last month</span></div>';
        } else {
            $truefalse = '';
        }
        if ($truefalse == "0") {
            $Rdata['count'] = 0;
        } else {
            $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        }

        $Raped_userGraph = $this->load->view('admin_dashboard/raps_mapped_user', $Rdata, true);
        $data['map_user'] = $Raped_userGraph;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // Raps Users End Here

    public function assessment_complted($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('admin_dashboard_model');
        $report_by = $this->input->post('report_by', true);
        $report_type = $this->input->post('report_type', true);

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
        $report_data = array();
        $index_dataset = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $countyear = date('Y');
        $Week = $this->input->post('week', true);
        

         if ($IsCustom == '' OR $IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'curremt';
            $Assessment_complted = $this->admin_dashboard_model->assessment_index_end($StartStrDt, $EndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($Assessment_complted['period'][$i])) {
                    $index_dataset[] = json_encode($Assessment_complted['period'][$i], JSON_NUMERIC_CHECK);
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
            $Assessment_complted = $this->admin_dashboard_model->assessment_index_end($StartStrDt, $Edate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($Assessment_complted['period'][$day])) {
                    $index_dataset[] = json_encode($Assessment_complted['period'][$day], JSON_NUMERIC_CHECK);
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
                    $Assessment_complted[] = $this->admin_dashboard_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id);
                    $result = $Assessment_complted;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {

                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($Assessment_complted[$i], JSON_NUMERIC_CHECK);
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
                    $Assessment_complted[] = $this->admin_dashboard_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id);
                    $result = $Assessment_complted;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($Assessment_complted[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            //Last 60 days
        } elseif ($IsCustom == "Last 90 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-89 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Last 90 Days';
            $Day_type = "90_days";
            $AssessmentCount = $this->admin_dashboard_model->assessment_index_end($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($AssessmentCount['period'][$day])) {
                    $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
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
            $AssessmentCount = $this->admin_dashboard_model->assessment_index_end($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($AssessmentCount['period'][$day])) {
                    $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
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
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P2W'))
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
                $AccuracySet = $this->admin_dashboard_model->assessment_index_end($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
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
                        $AssessmentCount[] = $this->admin_dashboard_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id);
                        $result = $AssessmentCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($AssessmentCount[$i], JSON_NUMERIC_CHECK);
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
                $AssessmentCount = $this->admin_dashboard_model->assessment_index_end($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($AssessmentCount['period'][$day])) {
                        $index_dataset[] = json_encode($AssessmentCount['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = $month;
                }
            }
            // custom Datepicker end
        }
        
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $first_date = strtotime('first day of previous month', time());
        $lastmonthdate = date('Y-m-d', $first_date);
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $Total_assessment_monthly = $this->admin_dashboard_model->total_assessment_monthly_end($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id);

        $latestmonth = isset($Total_assessment_monthly['Latestmonth'][0]) ? $Total_assessment_monthly['Latestmonth'][0]['currentmonth'] : 0;
        $oldmonth = isset($Total_assessment_monthly['Oldmonth'][0]) ? $Total_assessment_monthly['Oldmonth'][0]['months'] : 0;

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '  <span style="style="color:#fc0303; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; color:black;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth > $oldmonth) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; color:black;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth == 0 or $oldmonth == 0) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0  <span style="color:red; font-size: 12px; font-family: "Catamaran"; color:black;">0  from last month</span></div>';
        } else {
            $truefalse = '';
        }
        if ($truefalse == 0) {
            $Rdata['count'] = 0;
        } else {
            $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        }
        
        $indexEndGraph = $this->load->view('admin_dashboard/assessment_index_end', $Rdata, true);
        $data['endcount'] = $indexEndGraph;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    public function total_video_uploded($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('admin_dashboard_model');
        $report_by = $this->input->post('report_by', true);
        $report_type = $this->input->post('report_type', true);

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
        $report_data = array();
        $index_dataset = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $countyear = date('Y');
        $Week = $this->input->post('week', true);

        if ($IsCustom == '' or $IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'curremt';
            $total_video_uploaded = $this->admin_dashboard_model->total_video_uploaded($StartStrDt, $EndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($total_video_uploaded['period'][$i])) {
                    $index_dataset[] = json_encode($total_video_uploaded['period'][$i], JSON_NUMERIC_CHECK);
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
            $total_video_uploaded = $this->admin_dashboard_model->total_video_uploaded($StartStrDt, $Edate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($total_video_uploaded['period'][$day])) {
                    $index_dataset[] = json_encode($total_video_uploaded['period'][$day], JSON_NUMERIC_CHECK);
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
                    $total_video_uploaded[] = $this->admin_dashboard_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id);
                    $result = $total_video_uploaded;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($total_video_uploaded[$i], JSON_NUMERIC_CHECK);
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
                    $total_video_uploaded[] = $this->admin_dashboard_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id);
                    $result = $total_video_uploaded;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($total_video_uploaded[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            //Last 60 days
        } elseif ($IsCustom == "Last 90 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-89 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Last 90 Days';
            $Day_type = "90_days";
            $total_video_uploaded = $this->admin_dashboard_model->total_video_uploaded($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($total_video_uploaded['period'][$day])) {
                    $index_dataset[] = json_encode($total_video_uploaded['period'][$day], JSON_NUMERIC_CHECK);
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
            $total_video_uploaded = $this->admin_dashboard_model->total_video_uploaded($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($total_video_uploaded['period'][$day])) {
                    $index_dataset[] = json_encode($total_video_uploaded['period'][$day], JSON_NUMERIC_CHECK);
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
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P2W'))
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
                $total_video_uploaded = $this->admin_dashboard_model->total_video_uploaded($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($total_video_uploaded['period'][$day])) {
                        $index_dataset[] = json_encode($total_video_uploaded['period'][$day], JSON_NUMERIC_CHECK);
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
                        $total_video_uploaded[] = $this->admin_dashboard_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id);
                        $result = $total_video_uploaded;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($total_video_uploaded[$i], JSON_NUMERIC_CHECK);
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
                $total_video_uploaded = $this->admin_dashboard_model->total_video_uploaded($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($total_video_uploaded['period'][$day])) {
                        $index_dataset[] = json_encode($total_video_uploaded['period'][$day], JSON_NUMERIC_CHECK);
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
        $first_date = strtotime('first day of previous month', time());
        $lastmonthdate = date('Y-m-d', $first_date);
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $total_video_uploaded = $this->admin_dashboard_model->Month_Wise_Count($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id);

        $latestmonth = isset($total_video_uploaded['Latestmonth'][0]) ? $total_video_uploaded['Latestmonth'][0]['currentmonth'] : 0;
        $oldmonth = isset($total_video_uploaded['Oldmonth'][0]) ? $total_video_uploaded['Oldmonth'][0]['months'] : 0;

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '  <span style="style="color:#fc0303; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; color:black;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth > $oldmonth) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; color:black;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth == 0 or $oldmonth == 0) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0  <span style="color:red; font-size: 12px; font-family: "Catamaran"; color:black;">0  from last month</span></div>';
        } else {
            $truefalse = '';
        }
        if ($truefalse == 0) {
            $Rdata['count'] = 0;
        } else {
            $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        }

        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $total_video_uploaded = $this->load->view('admin_dashboard/total_video_uploaded', $Rdata, true);
        $data['total_video_uploaded'] = $total_video_uploaded;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    public function total_video_processed($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('admin_dashboard_model');
        $report_by = $this->input->post('report_by', true);
        $report_type = $this->input->post('report_type', true);

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
        $report_data = array();
        $index_dataset = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $countyear = date('Y');
        $Week = $this->input->post('week', true);

        if ($IsCustom == '' or $IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'curremt';
            $total_video_processed = $this->admin_dashboard_model->total_video_processed($StartStrDt, $EndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($total_video_processed['period'][$i])) {
                    $index_dataset[] = json_encode($total_video_processed['period'][$i], JSON_NUMERIC_CHECK);
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
            $total_video_processed = $this->admin_dashboard_model->total_video_processed($StartStrDt, $Edate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($total_video_processed['period'][$day])) {
                    $index_dataset[] = json_encode($total_video_processed['period'][$day], JSON_NUMERIC_CHECK);
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
                    $total_video_processed[] = $this->admin_dashboard_model->total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id);
                    $result = $total_video_processed;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($total_video_processed[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            // Last 29 days 
        }elseif ($IsCustom == "Last 60 Days") {
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
                    $total_video_processed[] = $this->admin_dashboard_model->total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id);
                    $result = $total_video_processed;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i])) {
                    $index_dataset[] = json_encode($total_video_processed[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] =  "Week " . $weekprint;
                $weekprint++;
                // $index_label[] =  '"' . $StdWeek[$i] . '"' . ' To ' . '"' . $EndWeek[$i] . '"';
            }
            //Last 60 days
        }elseif ($IsCustom == "Last 90 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-89 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Last 90 Days';
            $Day_type = "90_days";
            $total_video_processed = $this->admin_dashboard_model->total_video_processed($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($total_video_processed['period'][$day])) {
                    $index_dataset[] = json_encode($total_video_processed['period'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = $month;
            }
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
        }elseif ($IsCustom == "Last 365 Days") {
            // Last 365 days
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $total_video_processed = $this->admin_dashboard_model->total_video_processed($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($total_video_processed['period'][$day])) {
                    $index_dataset[] = json_encode($total_video_processed['period'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = $month;
            }
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
            // Last 365 days
        }else {
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
                        DateTime::createFromFormat('!Y-n-d', "$year-$month-$day")->add(new DateInterval('P2W'))
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
                $total_video_processed = $this->admin_dashboard_model->total_video_processed($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($total_video_processed['period'][$day])) {
                        $index_dataset[] = json_encode($total_video_processed['period'][$day], JSON_NUMERIC_CHECK);
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
                        $total_video_processed[] = $this->admin_dashboard_model->total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id);
                        $result = $total_video_processed;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i])) {
                        $index_dataset[] = json_encode($total_video_processed[$i], JSON_NUMERIC_CHECK);
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
                $total_video_processed = $this->admin_dashboard_model->total_video_processed($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($total_video_processed['period'][$day])) {
                        $index_dataset[] = json_encode($total_video_processed['period'][$day], JSON_NUMERIC_CHECK);
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
        $first_date = strtotime('first day of previous month', time());
        $lastmonthdate = date('Y-m-d', $first_date);
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $total_video_processed = $this->admin_dashboard_model->Month_Wise_Count_processed($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id);

        $latestmonth = isset($total_video_processed['Latestmonth'][0]) ? $total_video_processed['Latestmonth'][0]['currentmonth'] : 0;
        $oldmonth = isset($total_video_processed['Oldmonth'][0]) ? $total_video_processed['Oldmonth'][0]['months'] : 0;

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '  <span style="style="color:#fc0303; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; color:black;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth > $oldmonth) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . '   <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span> <span style="color:green; font-size: 12px; font-family: "Poppins",sans-serif; font-weight: bold; color:black;"> ' . $newcount . '  from last month</span></div>';
        } elseif ($latestmonth == 0 or $oldmonth == 0) {
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0  <span style="color:red; font-size: 12px; font-family: "Catamaran"; color:black;">0  from last month</span></div>';
        } else {
            $truefalse = '';
        }
        if ($truefalse == 0) {
            $Rdata['count'] = 0;
        } else {
            $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        }



        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $total_video_processed = $this->load->view('admin_dashboard/total_video_processed', $Rdata, true);
        $data['total_video_processed'] = $total_video_processed;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    public function total_users_Ac_In($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('admin_dashboard_model');
        $report_by = $this->input->post('report_by', true);
        $report_type = $this->input->post('report_type', true);

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
        $report_data = array();
        $index_dataset = [];
        $Total_User = [];
        $Active_User = [];
        $Inactive_User = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $countyear = date('Y');
        $Week = $this->input->post('week', true);


        // if ($IsCustom == '' or $IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'curremt';
            $Total_active_inactive = $this->admin_dashboard_model->total_active_inactive($StartStrDt, $EndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($Total_active_inactive['total_user'][$i])) {
                    $Total_User[] = json_encode($Total_active_inactive['total_user'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $Total_User[] = 0;
                }

                if (isset($Total_active_inactive['active_user'][$i])) {
                    $Active_User[] = json_encode($Total_active_inactive['active_user'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $Active_User[] = 0;
                }

                if (isset($Total_active_inactive['inactive_user'][$i])) {
                    $Inactive_User[] = json_encode($Total_active_inactive['inactive_user'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $Inactive_User[] = 0;
                }

                $index_label[] = date("M", strtotime($TempDate));
            }
        // } 
        

        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['Total_User'] = json_encode($Total_User, JSON_NUMERIC_CHECK);
        $Rdata['Active_User'] = json_encode($Active_User, JSON_NUMERIC_CHECK);
        $Rdata['Inactive_User'] = json_encode($Inactive_User, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $Rdata['count'] = 0;
      
        $Airgraph = $this->load->view('admin_dashboard/AIr_grpah', $Rdata, true);
        $data['AIR_Users'] = $Airgraph;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    // END HERE

      // Raps Played and Completed
      public function get_raps_played_completed($returnflag = 0)
      {
          $data = array();
          $Company_id = $this->mw_session['company_id'];
          if ($Company_id == "") {
              $Company_id = $this->input->post('company_id', TRUE);
          }
          $this->load->model('admin_dashboard_model');
          $start_date = $this->input->post('StartDate', true);
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
          $completed_dataset = array();
          $index_label = [];
          $report_title = '';
          $report_xaxis_title = '';
          $newyear = date('Y');
          $Week = $this->input->post('week', true);
  
  
          if ($IsCustom == '' or $IsCustom == 'Current Year') {
              // Return Current year
              $YearStartDate = $newyear . '-01-01';
              $YearEndDate = $newyear . '-12-31';
              $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
              $report_xaxis_title = 'Yearly';
              $Day_type = 'curremt';
              $AssessmentCount = $this->admin_dashboard_model->RapsPlayedComplted($YearStartDate, $YearEndDate, $Day_type, $Company_id);
              for ($i = 1; $i <= $current_month; $i++) {
                  $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                  $TempDate = Date('Y-' . $day . '-01');
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
              $AssessmentCount = $this->admin_dashboard_model->RapsPlayedComplted($StartStrDt, $Edate, $Day_type,$Company_id);
              
               $begin = new DateTime($StartStrDt);
               $end   = new DateTime($Edate);
              for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                  $day = $i->format("d");
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
                  $StdWeek = array();
                  $EndWeek = array();
  
                  foreach ($dateByWeek as $value) {
                      $WStartDate = $value[0];
                      $WEndDate =  $value[1];
                      $StdWeek[] = date('d', strtotime($WStartDate));
                      $EndWeek[] = date('d', strtotime($WEndDate));
                      $weeksteddate[] = $value[0] . '-' . $value[1];
                      $AssessmentCount[] = $this->admin_dashboard_model->raps_played_completed_30_60($WStartDate, $WEndDate,$Company_id);
                      $result = $AssessmentCount;
                  }
              } else {
                  $result = '';
              }
              $recount = count((array)$result) - 1;
              $weekprint = 1;
              for ($i = 0; $i <= $recount; $i++) {
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
                  $StdWeek = array();
                  $EndWeek = array();
  
                  foreach ($dateByWeek as $value) {
                      $WStartDate = $value[0];
                      $WEndDate =  $value[1];
                      $StdWeek[] = date('d', strtotime($WStartDate));
  
                      $EndWeek[] = date('d', strtotime($WEndDate));
                      $weeksteddate[] = $value[0] . '-' . $value[1];
                      $startmonthdate = date('D', strtotime($value[0]));
                      $endmonthdate = date('D', strtotime($value[1]));
                      $AssessmentCount[] = $this->admin_dashboard_model->raps_played_completed_30_60($WStartDate, $WEndDate,$Company_id);
                      $result = $AssessmentCount;
                  }
              } else {
                  $result = '';
              }
              $recount = count((array)$result) - 1;
              $weekprint = 1;
              for ($i = 0; $i <= $recount; $i++) {
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
              $AssessmentCount = $this->admin_dashboard_model->RapsPlayedComplted($StartStrDt, $EndDtdate, $Day_type, $Company_id);
              $begin = new DateTime($StartStrDt);
              $end   = new DateTime($EndDtdate);
  
              for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                  $day = $i->format("n");
                  $month = $i->format("M");
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
              $AssessmentCount = $this->admin_dashboard_model->RapsPlayedComplted($StartStrDt, $EndDtdate, $Day_type, $Company_id);
              $begin = new DateTime($StartStrDt);
              $end   = new DateTime($EndDtdate);
              for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                  $day = $i->format("m-Y");
                  $month = $i->format("M");
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
                  $AssessmentCount = $this->admin_dashboard_model->RapsPlayedComplted($SDate, $EDate, $Day_type, $Company_id);
                  $begin = new DateTime($SDate);
                  $end   = new DateTime($EDate);
                  for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                      $day = $i->format("d");
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
                      $StdWeek = array();
                      $EndWeek = array();
                      foreach ($dateByWeek as $value) {
                          $WStartDate = $value[0];
                          $WEndDate =  $value[1];
                          $StdWeek[] = date('d', strtotime($WStartDate));
                          $EndWeek[] = date('d', strtotime($WEndDate));
                          $weeksteddate[] = $value[0] . '-' . $value[1];
                          $AssessmentCount[] = $this->admin_dashboard_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id);
                          $result = $AssessmentCount;
                          // continue;
                      }
                  } else {
                      $result = '';
                  }
                  $recount = count((array)$result) - 1;
                  $weekprint = 1;
                  for ($i = 0; $i <= $recount; $i++) {
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
                  $AssessmentCount = $this->admin_dashboard_model->RapsPlayedComplted($SDate, $EDate, $Day_type, $Company_id);
                  $begin = new DateTime($SDate);
                  $end   = new DateTime($EDate);
                  for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                      $day = $i->format("m-Y");
                      $month = $i->format("M");
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
          $data['report'] = $report_data;
          $Rdata['report_period'] = $report_xaxis_title;
          $Rdata['report_title'] = json_encode($report_title);
          $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
          $Rdata['completed_dataset'] = json_encode($completed_dataset, JSON_NUMERIC_CHECK);
          $Rdata['index_label'] = json_encode($index_label);
          $monthstartdate = date('Y-m-01');
          $monthenddate  = date('Y-m-t');
          $first_date = strtotime('first day of previous month', time());
          $lastmonthdate = date('Y-m-d', $first_date);
          $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
          $Total_assessment_monthly = $this->admin_dashboard_model->total_assessment_monthly($monthstartdate, $monthenddate, $first_date, $lastmonthdate, $lastmonthenddate, $Company_id);
          $latestmonth = isset($Total_assessment_monthly['Latestmonth'][0]) ? $Total_assessment_monthly['Latestmonth'][0]['currentmonth'] : 0;
          $oldmonth = isset($Total_assessment_monthly['Oldmonth'][0]) ? $Total_assessment_monthly['Oldmonth'][0]['months'] : 0;
  
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
              $truefalse = '<div style="font-size: 30px; font-family: "Catamaran";  color:black;">' . $latestmonth . ' <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span>  <span style="color:#fc0303; font-family: "Poppins",sans-serif; font-weight: bold; font-size: 12px;"> ' . $newcount . '  from last month</span></div>';
          } elseif ($latestmonth > $oldmonth) {
              $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;">' . $latestmonth . ' <span style="style="color:green; font-family: "Catamaran"; font-size: 12px; "> ▲ </span>  <span style="color:green; font-family: "Poppins",sans-serif; font-weight: bold; font-size: 12px;"> ' . $newcount . '   from last month</span>  <i class="fa fa-angle-down"></i> </div>';
          } elseif ($latestmonth == 0 and $oldmonth == 0) {
              $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="color:red; font-size: 12px; font-family: "Catamaran"; color:black;"> 0 from last month</span></div>';
          } else {
              $truefalse = '';
          }
          if($truefalse=="0"){
              $Rdata['count'] = 0;
          }else{
              $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
          }
          $Played_Raps_Completed = $this->load->view('admin_dashboard/PlayedCompletedGraph',$Rdata, true);
          $data['Played_Raps_Completed'] = $Played_Raps_Completed;
  
          if ($returnflag) {
              return $data;
          } else {
              echo json_encode($data);
          }
      } 
      // Raps Played and Completed 

}