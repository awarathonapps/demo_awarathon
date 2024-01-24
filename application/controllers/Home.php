<?php


use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\PseudoTypes\True_;


if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('Home');
        // if (!$acces_management->allow_access) {
        //     redirect('dashboard');
        // }
        $this->acces_management = $acces_management;
        $this->load->model('home_model');
        $this->is_manager = ($this->mw_session['role'] == 2 ? 1 : 0);
    }

    public function index()
    {
        $data['module_id'] = '11.01';
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

        // $data['region_data'] = $this->home_model->get_trainee_region($data['company_id']);

        $data['start_date'] = date('d-M-Y', strtotime('-6 days'));
        $data['end_date'] = date("d-m-Y");
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date("Y-m-d");

        //Added

        $data['company_id'] = $this->mw_session['company_id'];
        $company_id = $data['company_id'];
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="2" AND company_id="' . $company_id . '"');

        //$assessment_list= $this->home_model->get_assessment_list($company_id, $trainer_id, $start_date, $end_date);
        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');
        // $data['assessment_data'] = $this->home_model->get_assessment($data['company_id'], '', $start_date, $end_date);

        // $data['parameter_data'] = $this->home_model->get_parameter();
        $data['assessment'] = $this->home_model->get_all_assessment();
        $this->load->view('home/index', $data);
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
        $this->load->model('home_model');
        $manager_id = $this->is_manager ? $this->mw_session['user_id'] : '';
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


        if ($IsCustom == '' or $IsCustom == "Last 365 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $AssessmentCount = $this->home_model->assessment_started($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
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
        } else if ($IsCustom == "Current Year") {
               // Return Current year
            $YearStartDate = $newyear . '-01-01';
            $YearEndDate = $newyear . '-12-31';
            $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $AssessmentCount = $this->home_model->assessment_started($YearStartDate, $YearEndDate, $Day_type, $Company_id, $manager_id);
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
            $AssessmentCount = $this->home_model->assessment_started($StartStrDt, $Edate, $Day_type, $Company_id, $manager_id);
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
                    $AssessmentCount[] = $this->home_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
                    $AssessmentCount[] = $this->home_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
            $AssessmentCount = $this->home_model->assessment_started($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
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
                $AccuracySet = $this->home_model->assessment_started($SDate, $EDate, $Day_type, $Company_id, $manager_id);
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
                        $AssessmentCount[] = $this->home_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
                $AssessmentCount = $this->home_model->assessment_started($SDate, $EDate, $Day_type, $Company_id, $manager_id);
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
        // Monthly Count
        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $Total_assessment_monthly = $this->home_model->total_assessment_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id);
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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
        } else {
            $truefalse = '';
        }

        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $indexStartGraph = $this->load->view('home/assessment_index_start', $Rdata, true);
        $data['startcount'] = $indexStartGraph;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    public function assessment_complted($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('home_model');
        $manager_id = $this->is_manager ? $this->mw_session['user_id'] : '';
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

        if ($IsCustom == '' or $IsCustom == "Last 365 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $Assessment_complted = $this->home_model->assessment_index_end($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($Assessment_complted['period'][$day])) {
                    $index_dataset[] = json_encode($Assessment_complted['period'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = $month;
            }
            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($EndDtdate)) . '';
        } else if ($IsCustom == "Current Year") {
        $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $Assessment_complted = $this->home_model->assessment_index_end($StartStrDt, $EndDate, $Day_type, $Company_id, $manager_id);
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
            $Assessment_complted = $this->home_model->assessment_index_end($StartStrDt, $Edate, $Day_type, $Company_id, $manager_id);
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
                    $Assessment_complted[] = $this->home_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
                    $Assessment_complted[] = $this->home_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
            $AssessmentCount = $this->home_model->assessment_index_end($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
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
                $AccuracySet = $this->home_model->assessment_index_end($SDate, $EDate, $Day_type, $Company_id, $manager_id);
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
                        $AssessmentCount[] = $this->home_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
                $AssessmentCount = $this->home_model->assessment_index_end($SDate, $EDate, $Day_type, $Company_id, $manager_id);
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
        // Monthly Count
        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $Total_assessment_monthly = $this->home_model->total_assessment_monthly_end($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id);

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
        } else {
            $truefalse = '';
        }

        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $indexEndGraph = $this->load->view('home/assessment_index_end', $Rdata, true);
        $data['endcount'] = $indexEndGraph;

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
        $this->load->model('home_model');
        $manager_id = $this->is_manager ? $this->mw_session['user_id'] : '';


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

        if ($IsCustom == '' or $IsCustom == "Last 365 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $UserCount = $this->home_model->get_raps_mapped_user($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
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
        } else if ($IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $UserCount = $this->home_model->get_raps_mapped_user($StartStrDt, $EndDate, $Day_type, $Company_id, $manager_id);
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
            $UserCount = $this->home_model->get_raps_mapped_user($StartStrDt, $Edate, $Day_type, $Company_id, $manager_id);
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
                    $UserCount[] = $this->home_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
                    $UserCount[] = $this->home_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
            $UserCount = $this->home_model->get_raps_mapped_user($StartStrDt, $EndDtdate, $Day_type, $Company_id, $manager_id);
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
        } else {
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
                $UserCount = $this->home_model->get_raps_mapped_user($SDate, $EDate, $Day_type, $Company_id, $manager_id);
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
                        $UserCount[] = $this->home_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id, $manager_id);
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
            } elseif ($type >= "60") {
                // 01-01-2022 to current month
                $end_month = date('m', strtotime($end_date));
                $report_title = 'From ' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '';
                $report_xaxis_title = 'Yearly';
                $Day_type = "365_days";
                $UserCount = $this->home_model->get_raps_mapped_user($SDate, $EDate, $Day_type, $Company_id, $manager_id);
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
        // Monthly Count
        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $rap_total_user_monthly = $this->home_model->rap_total_user_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id, $manager_id);
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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
        } else {
            $truefalse = '';
        }
        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $Raped_userGraph = $this->load->view('home/raps_mapped_user', $Rdata, true);
        $data['map_user'] = $Raped_userGraph;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // Raps Users End Here
    public function DatatableRefresh_ideal() 
    {
        $dtSearchColumns = array('am.id','am.assessment','art.description','am.start_dttm','am.end_dttm','am.status');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND am.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
            }
        }
        $manager_id = $this->is_manager ? $this->mw_session['user_id'] : '';
        if ($manager_id != '') {
            if ($dtWhere <> '') {
                $dtWhere .= " AND amu.trainer_id  = " . $manager_id;
            }else{
                $dtWhere .= " WHERE amu.trainer_id  = " . $manager_id;
            }
        }

        $superaccess = $this->mw_session['superaccess'];
        $DTRenderArray = $this->home_model->LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        //$dtDisplayColumns = array('checkbox', 'id','assessment', 'report_type','start_dttm', 'end_dttm', 'status', 'mapped', 'played', 'uploaded','processed');
        $dtDisplayColumns = array( 'id','assessment','report_type','start_dttm','end_dttm', 'status', 'mapped', 'played', 'uploaded', 'processed');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        if(!empty($DTRenderArray['ResultSet'])){
            $assessment_ids = array_column($DTRenderArray['ResultSet'], 'id');
            $users_count = $this->home_model->getUserCount($cmp_id, $assessment_ids, $manager_id);
            $assessment_users_count = [];
            foreach ($users_count as $user_cnt) {
                $assessment_users_count[$user_cnt->assessment_id] = [
                    'mapped' => $user_cnt->mapped, 
                    'played' => $user_cnt->played
                ];
            }
            $videos_processed = $this->home_model->getVideoCount($cmp_id, $assessment_ids, $manager_id);
            $assessment_video_processed = [];
            foreach ($videos_processed as $video_cnt) {
                $assessment_video_processed[$video_cnt->assessment_id] = $video_cnt->total_video_processsed;
            }
            $videos_uploaded = $this->home_model->getUploadedVideos($cmp_id, $assessment_ids, $manager_id);
            $assessment_video_uploaded = [];
            foreach ($videos_uploaded as $video_cnt) {
                $assessment_video_uploaded[$video_cnt->assessment_id] = $video_cnt->total_video_uploaded;
            }
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
            $assessment_id = $dtRow['id'];
            // $users_count = $this->home_model->getAssessmentUserCount($cmp_id,$assessment_id);
            // $video_count = $this->home_model->getAssessmentVideoCount($cmp_id,$assessment_id);
            // $video_upload_count= $this->home_model->getVideoUploaded($cmp_id,$assessment_id);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "assessment") {
                    $acces_management = $this->session->userdata('awarathon_session');
                    $SupperAccess = false;
                    if ($acces_management['superaccess']) {
                        $SupperAccess = true;
                    } else {
                        $ReturnSet = CheckSidebarRights($acces_management);
                        $SideBarDataSet = $ReturnSet['RightsArray'];
                        $GrouprightSet = $ReturnSet['GroupArray'];
                    }

                    if ($SupperAccess ||  isset($SideBarDataSet['ai_dashboard'])) {
                        $row[] = '<a href="' . $site_url . 'ai_dashboard/candidates_list/' . base64_encode($dtRow['id']) . '" 
                        data-target="#LoadModalFilter-view" data-toggle="modal">' . $dtRow['assessment'] . ' </a>';
                    } else {
                        $row[] = '<a href= "#" onclick="showMessage()" >' . $dtRow['assessment'] . ' </a>'; ?>
                        
                <?php }
                } else if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
                } else if($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if($dtDisplayColumns[$i] == "mapped") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['mapped'] : 0;
                } else if($dtDisplayColumns[$i] == "played") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['played'] : 0;
                } else if($dtDisplayColumns[$i] == "uploaded") {
                    $row[] = (!empty($assessment_video_uploaded) && isset($assessment_video_uploaded[$assessment_id])) ? $assessment_video_uploaded[$assessment_id] : 0;
                }else if($dtDisplayColumns[$i] == "processed") {
                    $row[] = (!empty($assessment_video_processed) && isset($assessment_video_processed[$assessment_id])) ? $assessment_video_processed[$assessment_id] : 0;
                }  else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    } 
    // By Bhautik Rana 01-02-2023
    public function candidates_list($Encode_id)
    {

        $data['assessment_id'] = base64_decode($Encode_id);
        $data['is_send'] = 0;
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="' . $company_id . '" AND id="' . $data['assessment_id'] . '"');
        $report_type = 0;
        if (isset($report_type_result) and count((array)$report_type_result) > 0) {
            $report_type = (int)$report_type_result->report_type;
        }
        $data['report_type'] = $report_type;
        $this->load->view('home/CandidateListModal', $data);
    }
    public function CandidateDatatableRefresh($assessment_id, $is_send_tab)
    {
        $manager_id = $this->is_manager ? $this->mw_session['user_id'] : '';
        // $dtSearchColumns = array('ar.user_id', 'ar.user_id', "CONCAT(du.firstname,' ',du.lastname)", 'du.email');
        $dtSearchColumns = array('user_id', 'user_id', 'user_name', 'email', 'mobile', 'user_id', 'user_id', 'user_id');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $data['assessment_id'] = $assessment_id;
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $company_id;
            }
        }
        //KRISHNA -- Trinity PDF Report level changes - Show trinity assessment users
        $report_type_result = $this->common_model->get_value('assessment_mst', 'assessment_type,report_type', 'id="' . $assessment_id . '"');
        $assessment_type = 0;
        if (isset($report_type_result) and count((array)$report_type_result) > 0) {
            $assessment_type = (int)$report_type_result->assessment_type;
        }
        $DTRenderArray = $this->home_model->get_distinct_participants($company_id, $assessment_id, $assessment_type, $manager_id);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        // $report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'id="' . $assessment_id . '"');
        $report_type = 0;
        if (isset($report_type_result) and count((array)$report_type_result) > 0) {
            $report_type = (int)$report_type_result->report_type;
        }
        $dtDisplayColumns = array('checkbox', 'user_id', 'user_name', 'email', 'mobile');
        if ($report_type == 1 or $report_type == 3 or $report_type == 0) {
            array_push($dtDisplayColumns, 'Report_ai');
        }
        if ($report_type == 2 or $report_type == 3 or $report_type == 0) {
            array_push($dtDisplayColumns, 'Report_manual');
        }
        if ($report_type == 3 or $report_type == 0) {
            array_push($dtDisplayColumns, 'Report_combine');
        }
        if ($is_send_tab) {
            array_push($dtDisplayColumns, 'Status');
        }
        $site_url = base_url();
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $total_questions_played        = 0;
        $total_task_completed          = 0;
        $total_manual_rating_completed = 0;
        $show_ai_pdf                   = false;
        $show_manual_pdf               = false;
        $is_schdule_running            = false;
        $show_reports_flag             = true;

        if($assessment_type == 1 || $assessment_type ==2){
            $_total_played_result     = $this->common_model->get_value('assessment_results', 'count(*) as total', "company_id = '" . $company_id . "' AND assessment_id = '" . $assessment_id . "' AND trans_id > 0 AND question_id > 0 AND vimeo_uri!='' AND ftp_status=1");
        }elseif($assessment_type == 3){ //trinity
            $_total_played_result     = $this->common_model->get_value('trinity_results', 'count(*) as total', "company_id = '" . $company_id . "' AND assessment_id = '" . $assessment_id . "' AND vimeo_uri!='' AND ftp_status=1");
        }
        if (isset($_total_played_result) and count((array)$_total_played_result) > 0) {
            $total_questions_played = $_total_played_result->total;
        }
        $_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '"');
        if (isset($_tasksc_results) and count((array)$_tasksc_results) > 0) {
            $total_task_completed = $_tasksc_results->total;
        }
        $_manualrate_results     = $this->common_model->get_value('assessment_results_trans', 'count(DISTINCT user_id,question_id) as total', 'assessment_id="' . $assessment_id . '"');
        if (isset($_manualrate_results) and count((array)$_manualrate_results) > 0) {
            $total_manual_rating_completed = $_manualrate_results->total;
        }
        $_schdule_running_result     = $this->common_model->get_value('ai_cronjob', '*', 'assessment_id="' . $assessment_id . '"');
        if (isset($_schdule_running_result) and count((array)$_schdule_running_result) > 0) {
            $is_schdule_running = true;
        }
        if (((int)$total_questions_played >= (int)$total_task_completed) and ((int)$total_task_completed > 0) and ($is_schdule_running == false)) {
            $show_ai_pdf = true;
        }
        if ((int)$total_questions_played >= (int)$total_manual_rating_completed) {
            $show_manual_pdf = true;
        }
        $user_rating = $this->common_model->get_selected_values('assessment_results_trans', 'DISTINCT user_id,question_id', 'assessment_id="' . $assessment_id . '"');
        $TotalHeader = count((array)$dtDisplayColumns);
        $Curr_Time = strtotime($now);
        $user_rating_array = json_decode(json_encode($user_rating), true);
        $userid_rating_array = array_column($user_rating_array, 'user_id');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $user_id = $dtRow['user_id'];
            //Added
            $row = array();
            $_score_imported = false;
            $_xls_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="' . $company_id . '" AND assessment_id="' . $assessment_id . '" AND user_id="' . $user_id . '"');
            if (isset($_xls_results) and count((array)$_xls_results) > 0) {
                if ((int)$_xls_results->total > 0) {
                    $_score_imported = true;
                }
            }
            $pdf_icon = "";
            $mpdf_icon = "";
            $cpdf_icon = "";
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Report_ai") {
                    if ($show_reports_flag == false) {
                        $pdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                    } else if ($show_reports_flag == true and $show_ai_pdf == true and $_score_imported == true) {
                        $pdf_icon        = '<a href="' . base_url() . '/ai_process/view_ai_process/' . $company_id . '/' . $assessment_id . '/' . $user_id . '" target="_blank"><img src="' . base_url() . '/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
                    } else {
                        $pdf_icon    = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span>';
                    }
                    $row[] = $pdf_icon;
                } elseif ($dtDisplayColumns[$i] == "Report_manual") {
                    if ($show_reports_flag == false) {
                        $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                    } else if ($show_reports_flag == true and $show_manual_pdf) {
                        if (in_array($user_id, $userid_rating_array)) {
                            $mpdf_icon       = '<a href="' . base_url() . '/ai_process/view_manual_reports/' . $company_id . '/' . $assessment_id . '/' . $user_id . '" target="_blank"><img src="' . base_url() . '/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
                        } else {
                            $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                        }
                    } else {
                        $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                    }
                    $row[] = $mpdf_icon;
                } elseif ($dtDisplayColumns[$i] == "Report_combine") {
                    if ($show_reports_flag == false) {
                        $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                    } else if ($show_reports_flag == true and $show_ai_pdf == true and $_score_imported == true) {
                        if ($show_manual_pdf) {
                            if (in_array($user_id, $userid_rating_array)) {
                                $cpdf_icon       = '<a href="' . base_url() . '/ai_process/view_combine_reports/' . $company_id . '/' . $assessment_id . '/' . $user_id . '" target="_blank"><img src="' . base_url() . '/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
                            } else {
                                $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                            }
                        } else {
                            $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                        }
                    } else {
                        $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span>';
                    }
                    $row[] = $cpdf_icon;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="candidate_id[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Status") {
                    if ($dtRow['is_sent']) {
                        $row[] = '<span style="height: 25px;width: 25px;background: #4caf50;padding: 9px;color: #ffffff;">Sent</span>';
                    } else if ($dtRow['is_sent'] === '0' && $dtRow['attempt'] > 0) {
                        $row[] = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">Failed</span>';
                    } else if ($dtRow['is_sent'] === '0') {
                        $row[] = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">Scheduled</span>';
                    } else {
                        $row[] = '';
                    }
                } elseif ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    // By Bhautik rana 01-02-2023
    }
