<?php

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\PseudoTypes\True_;


if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports_adoption extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('reports_adoption');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('adoption_model');
    }

    public function index()
    {
        $data['module_id'] = '89';
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

   
        $data['start_date'] = date('d-M-Y', strtotime('-6 days'));
        $data['end_date'] = date("d-m-Y");
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date("Y-m-d");

        //Added

        $data['company_id'] = $this->mw_session['company_id'];
        $company_id = $data['company_id'];
        $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="2" AND company_id="' . $company_id . '"');

   

        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');

        $data['assessment'] = $this->adoption_model->get_all_assessment();
        $this->load->view('reports_adoption/index', $data);
    }

    public function ajax_getWeeks()
    {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function assessment_wise_manager()
    {
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $Company_id =  $this->input->post('company_id', TRUE);
        if ($assessment_id == '') {
            $assessment_list = $this->adoption_model->assessment_wise_tariners($assessment_id, $Company_id);
        } else {
            $assessment_list = $this->adoption_model->assessment_wise_manager($assessment_id);
        }
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->user_id . '">[' . $value->user_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['assessment_list_data']  = $assessment_html;
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
        $this->load->model('adoption_model');

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
            $AssessmentCount = $this->adoption_model->assessment_started($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
            $AssessmentCount = $this->adoption_model->assessment_started($YearStartDate, $YearEndDate, $Day_type, $Company_id);
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
            $AssessmentCount = $this->adoption_model->assessment_started($StartStrDt, $Edate, $Day_type, $Company_id);
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
                    $AssessmentCount[] = $this->adoption_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id);
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
                    $AssessmentCount[] = $this->adoption_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id);
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
            $AssessmentCount = $this->adoption_model->assessment_started($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
                $AccuracySet = $this->adoption_model->assessment_started($SDate, $EDate, $Day_type, $Company_id);
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
                        $AssessmentCount[] = $this->adoption_model->assessment_index_30_60days($WStartDate, $WEndDate, $Company_id);
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
                $AssessmentCount = $this->adoption_model->assessment_started($SDate, $EDate, $Day_type, $Company_id);
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
        $Total_assessment_monthly = $this->adoption_model->total_assessment_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);
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
        $indexStartGraph = $this->load->view('reports_adoption/assessment_index_start', $Rdata, true);
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
        $this->load->model('adoption_model');


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
            $UserCount = $this->adoption_model->get_raps_mapped_user($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
            $UserCount = $this->adoption_model->get_raps_mapped_user($StartStrDt, $EndDate, $Day_type, $Company_id);
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
            $UserCount = $this->adoption_model->get_raps_mapped_user($StartStrDt, $Edate, $Day_type, $Company_id);
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
                    $UserCount[] = $this->adoption_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id);
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
                    $UserCount[] = $this->adoption_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id);
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
            $UserCount = $this->adoption_model->get_raps_mapped_user($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
                $UserCount = $this->adoption_model->get_raps_mapped_user($SDate, $EDate, $Day_type, $Company_id);
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
                        $UserCount[] = $this->adoption_model->get_rap_users_last30_60_days($WStartDate, $WEndDate, $Company_id);
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
                $UserCount = $this->adoption_model->get_raps_mapped_user($SDate, $EDate, $Day_type, $Company_id);
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
        $rap_total_user_monthly = $this->adoption_model->rap_total_user_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);
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
        $Raped_userGraph = $this->load->view('reports_adoption/raps_mapped_user', $Rdata, true);
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
        $this->load->model('adoption_model');

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
            $Assessment_complted = $this->adoption_model->assessment_index_end($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
            $Assessment_complted = $this->adoption_model->assessment_index_end($StartStrDt, $EndDate, $Day_type, $Company_id);
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
            $Assessment_complted = $this->adoption_model->assessment_index_end($StartStrDt, $Edate, $Day_type, $Company_id);
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
                    $Assessment_complted[] = $this->adoption_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id);
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
                    $Assessment_complted[] = $this->adoption_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id);
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
            $AssessmentCount = $this->adoption_model->assessment_index_end($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
                $AccuracySet = $this->adoption_model->assessment_index_end($SDate, $EDate, $Day_type, $Company_id);
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
                        $AssessmentCount[] = $this->adoption_model->assessment_index_end_30_60days($WStartDate, $WEndDate, $Company_id);
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
                $AssessmentCount = $this->adoption_model->assessment_index_end($SDate, $EDate, $Day_type, $Company_id);
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
        $Total_assessment_monthly = $this->adoption_model->total_assessment_monthly_end($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);

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
        $indexEndGraph = $this->load->view('reports_adoption/assessment_index_end', $Rdata, true);
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
        $this->load->model('adoption_model');

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
            // Last 365 days
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $total_video_uploaded = $this->adoption_model->total_video_uploaded($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
        } else if ($IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $total_video_uploaded = $this->adoption_model->total_video_uploaded($StartStrDt, $EndDate, $Day_type, $Company_id);
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
            $total_video_uploaded = $this->adoption_model->total_video_uploaded($StartStrDt, $Edate, $Day_type, $Company_id);
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
                    $total_video_uploaded[] = $this->adoption_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id);
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
                    $total_video_uploaded[] = $this->adoption_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id);
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
            $total_video_uploaded = $this->adoption_model->total_video_uploaded($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
                $total_video_uploaded = $this->adoption_model->total_video_uploaded($SDate, $EDate, $Day_type, $Company_id);
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
                        $total_video_uploaded[] = $this->adoption_model->total_video_uploaded_last_30_60($WStartDate, $WEndDate, $Company_id);
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
                $total_video_uploaded = $this->adoption_model->total_video_uploaded($SDate, $EDate, $Day_type, $Company_id);
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
        $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $total_video_uploaded = $this->adoption_model->Month_Wise_Count($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
        } else {
            $truefalse = '';
        }

        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $total_video_uploaded = $this->load->view('reports_adoption/total_video_uploaded', $Rdata, true);
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
        $this->load->model('adoption_model');

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
            $total_video_processed = $this->adoption_model->total_video_processed($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
        } else if ($IsCustom == "Current Year") {
            $StartStrDt = $countyear . '-01-01';
            $EndDate = $countyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $total_video_processed = $this->adoption_model->total_video_processed($StartStrDt, $EndDate, $Day_type, $Company_id);
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
            $total_video_processed = $this->adoption_model->total_video_processed($StartStrDt, $Edate, $Day_type, $Company_id);
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
                    $total_video_processed[] = $this->adoption_model->total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id);
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
                    $total_video_processed[] = $this->adoption_model->total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id);
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
        } elseif ($IsCustom == "Last 90 Days") {
            $StartStrDt = date('Y-m-d', strtotime("-89 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Last 90 Days';
            $Day_type = "90_days";
            $total_video_processed = $this->adoption_model->total_video_processed($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
                $total_video_processed = $this->adoption_model->total_video_processed($SDate, $EDate, $Day_type, $Company_id);
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
                        $total_video_processed[] = $this->adoption_model->total_video_processed_last_30_60($WStartDate, $WEndDate, $Company_id);
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
                $total_video_processed = $this->adoption_model->total_video_processed($SDate, $EDate, $Day_type, $Company_id);
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
        $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $total_video_processed = $this->adoption_model->Month_Wise_Count_processed($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);

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
            $truefalse = '<div style="font-size: 30px; font-family: "Catamaran"; color:black;"> 0 <span style="style="color:red; font-family: "Catamaran"; font-size: 12px; "> ▼ </span> <span style="color:red; font-size: 12px; font-family: "Catamaran";">  0 from last month</span></div>';
        } else {
            $truefalse = '';
        }

        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);

        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $total_video_processed = $this->load->view('reports_adoption/total_video_processed', $Rdata, true);
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
        $this->load->model('adoption_model');

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
        $Total_User = [];
        $Active_User = [];
        $Inactive_User = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $countyear = date('Y');


        // if ($IsCustom == '' or $IsCustom == "Current Year") {
        $StartStrDt = $countyear . '-01-01';
        $EndDate = $countyear . '-12-31';

        $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
        $report_xaxis_title = 'Yearly';
        $Day_type = 'current';
        $Total_active_inactive = $this->adoption_model->total_active_inactive($StartStrDt, $EndDate, $Day_type, $Company_id);
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

        $Airgraph = $this->load->view('reports_adoption/AIr_grpah', $Rdata, true);
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
        $this->load->model('adoption_model');
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
        $mapped_dataset = array();
        $completed_dataset = array();
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
            $AssessmentCount = $this->adoption_model->RapsPlayedComplted($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);
            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("m-Y");
                $month = $i->format("M");
                if (isset($AssessmentCount['mapped'][$day])) {
                    $mapped_dataset[] = json_encode($AssessmentCount['mapped'][$day], JSON_NUMERIC_CHECK);
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
        } else if ($IsCustom == "Current Year") {
            // Return Current year
            $YearStartDate = $newyear . '-01-01';
            $YearEndDate = $newyear . '-12-31';
            $report_title = 'From ' . date('d-m-Y', strtotime($YearStartDate)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $AssessmentCount = $this->adoption_model->RapsPlayedComplted($YearStartDate, $YearEndDate, $Day_type, $Company_id);
            for ($i = 1; $i <= $current_month; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($AssessmentCount['mapped'][$i])) {
                    $mapped_dataset[] = json_encode($AssessmentCount['mapped'][$i], JSON_NUMERIC_CHECK);
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
            $AssessmentCount = $this->adoption_model->RapsPlayedComplted($StartStrDt, $Edate, $Day_type, $Company_id);

            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($Edate);
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $day = $i->format("d");
                if (isset($AssessmentCount['mapped'][$day])) {
                    $mapped_dataset[] = json_encode($AssessmentCount['mapped'][$day], JSON_NUMERIC_CHECK);
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
                $StdWeek = array();
                $EndWeek = array();

                foreach ($dateByWeek as $value) {
                    $WStartDate = $value[0];
                    $WEndDate =  $value[1];
                    $StdWeek[] = date('d', strtotime($WStartDate));
                    $EndWeek[] = date('d', strtotime($WEndDate));
                    $weeksteddate[] = $value[0] . '-' . $value[1];
                    $AssessmentCount[] = $this->adoption_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id);
                    $result = $AssessmentCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i]['mapped'])) {
                    $mapped_dataset[] = json_encode($result[$i]['mapped'], JSON_NUMERIC_CHECK);
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
                $StdWeek = array();
                $EndWeek = array();

                foreach ($dateByWeek as $value) {
                    $WStartDate = $value[0];
                    $WEndDate =  $value[1];
                    $StdWeek[] = date('d', strtotime($WStartDate));

                    $EndWeek[] = date('d', strtotime($WEndDate));
                    $weeksteddate[] = $value[0] . '-' . $value[1];
                    $AssessmentCount[] = $this->adoption_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id);
                    $result = $AssessmentCount;
                }
            } else {
                $result = '';
            }
            $recount = count((array)$result) - 1;
            $weekprint = 1;
            for ($i = 0; $i <= $recount; $i++) {
                if (!empty($result[$i]['mapped'])) {
                    $mapped_dataset[] = json_encode($result[$i]['mapped'], JSON_NUMERIC_CHECK);
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
            $AssessmentCount = $this->adoption_model->RapsPlayedComplted($StartStrDt, $EndDtdate, $Day_type, $Company_id);
            $begin = new DateTime($StartStrDt);
            $end   = new DateTime($EndDtdate);

            for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                $day = $i->format("n");
                $month = $i->format("M");
                if (isset($AssessmentCount['mapped'][$day])) {
                    $mapped_dataset[] = json_encode($AssessmentCount['mapped'][$day], JSON_NUMERIC_CHECK);
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
                $AssessmentCount = $this->adoption_model->RapsPlayedComplted($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $day = $i->format("d");
                    if (isset($AssessmentCount['mapped'][$day])) {
                        $mapped_dataset[] = json_encode($AssessmentCount['mapped'][$day], JSON_NUMERIC_CHECK);
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
                    $StdWeek = array();
                    $EndWeek = array();
                    foreach ($dateByWeek as $value) {
                        $WStartDate = $value[0];
                        $WEndDate =  $value[1];
                        $StdWeek[] = date('d', strtotime($WStartDate));
                        $EndWeek[] = date('d', strtotime($WEndDate));
                        $weeksteddate[] = $value[0] . '-' . $value[1];
                        $AssessmentCount[] = $this->adoption_model->raps_played_completed_30_60($WStartDate, $WEndDate, $Company_id);
                        $result = $AssessmentCount;
                    }
                } else {
                    $result = '';
                }
                $recount = count((array)$result) - 1;
                $weekprint = 1;
                for ($i = 0; $i <= $recount; $i++) {
                    if (!empty($result[$i]['mapped'])) {
                        $mapped_dataset[] = json_encode($result[$i]['mapped'], JSON_NUMERIC_CHECK);
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
                $AssessmentCount = $this->adoption_model->RapsPlayedComplted($SDate, $EDate, $Day_type, $Company_id);
                $begin = new DateTime($SDate);
                $end   = new DateTime($EDate);
                for ($i = $begin; $i <= $end; $i->modify('+1 month')) {
                    $day = $i->format("m-Y");
                    $month = $i->format("M");
                    if (isset($AssessmentCount['mapped'][$day])) {
                        $mapped_dataset[] = json_encode($AssessmentCount['mapped'][$day], JSON_NUMERIC_CHECK);
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
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['mapped_dataset'] = json_encode($mapped_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['completed_dataset'] = json_encode($completed_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        // Monthly Count
        $monthstartdate = date('Y-m-01');
        $monthenddate  = date('Y-m-t');
        $lastmonthdate = date("Y-m-d", strtotime("first day of previous month"));
        $lastmonthenddate = date('Y-m-d', strtotime('last day of previous month'));
        $Total_assessment_monthly = $this->adoption_model->total_assessment_monthly($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);
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
        $Played_Raps_Completed = $this->load->view('reports_adoption/PlayedCompletedGraph', $Rdata, true);
        $data['Played_Raps_Completed'] = $Played_Raps_Completed;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // Raps Played and Completed 

    // Total Reports Sent
    public function total_report_sent($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $this->load->model('adoption_model');
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
        if ($IsCustom == '' or $IsCustom == "Last 365 Days") {
            // Last 365 days
            $StartStrDt = date('Y-m-d', strtotime("-365 days"));
            $EndDtdate = date('Y-m-d');

            $report_xaxis_title = 'Yearly';
            $Day_type = "365_days";
            $total_reports_sent = $this->adoption_model->total_reports_sent($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
        } elseif ($IsCustom == 'Current Year') {
            // Return Current year
            $StartStrDt = $newyear . '-01-01';
            $EndDate = $newyear . '-12-31';

            $report_title = 'From ' . date('d-m-Y', strtotime($StartStrDt)) . ' To ' . date('d-m-Y', strtotime($lastDayThisMonth)) . '';
            $report_xaxis_title = 'Yearly';
            $Day_type = 'current';
            $total_reports_sent = $this->adoption_model->total_reports_sent($StartStrDt, $EndDate, $Day_type, $Company_id);
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
            $total_reports_sent = $this->adoption_model->total_reports_sent($StartStrDt, $Edate, $Day_type, $Company_id);
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
                    $total_reports_sent[] = $this->adoption_model->total_reports_sent_last_30_60($WStartDate, $WEndDate, $Company_id);
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
                    $total_reports_sent[] = $this->adoption_model->total_reports_sent_last_30_60($WStartDate, $WEndDate, $Company_id);
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
            $total_reports_sent = $this->adoption_model->total_reports_sent($StartStrDt, $EndDtdate, $Day_type, $Company_id);
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
                $total_reports_sent = $this->adoption_model->total_reports_sent($SDate, $EDate, $Day_type, $Company_id);
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
                        $total_reports_sent[] = $this->adoption_model->total_reports_sent_last_30_60($WStartDate, $WEndDate, $Company_id);
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
                $total_reports_sent = $this->adoption_model->total_reports_sent_last_30_60($SDate, $EDate, $Day_type, $Company_id);
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
        $total_reports_sent = $this->adoption_model->Month_Wise_Count_Send($monthstartdate, $monthenddate, $lastmonthdate, $lastmonthenddate, $Company_id);

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
        $Rdata['count'] = json_encode($truefalse, JSON_NUMERIC_CHECK);

        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['completed_dataset'] = json_encode($completed_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $total_report_sent = $this->load->view('reports_adoption/total_report_sent', $Rdata, true);
        $data['total_report_sent'] = $total_report_sent;

        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    //End Here

    // Adoption Graph
    // by Bhautik Rana 03 jan 2022 comments related changes
    public function adoption_by_team_manager()
    {
        $manager_list = '';
        $assessment_id = $this->input->post('assessmentid', TRUE) != '' ? $this->input->post('assessmentid', TRUE) : 0;
        $Company_id =  $this->input->post('company_id', TRUE);
        $am_list = $this->adoption_model->adoption_by_team_manager($assessment_id, $Company_id);
        if (count((array)$am_list) > 0) {
            foreach ($am_list as $value) {
                $manager_list .= '<option value="' . $value->user_id . '">[' . $value->user_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['manager_list']  = $manager_list;
        echo json_encode($data);
    }

    public function adoption_by_team($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', TRUE) : '';
        $TrainerIDSet = $this->input->post('manager_id', TRUE) != '' ? $this->input->post('manager_id', TRUE) : '';

        $this->load->model('adoption_model');
        $trainer_name = array();
        $team_title = '';
        $start = array();
        $complet = array();
        $started = array();
        $completed = array();
        $user_mapped = array();
        if ($Assessment_id == ''  && $TrainerIDSet == '') {
            $CurrentDate = date("Y-m-d h:i:s");
            $LAssessmentDetails = $this->adoption_model->LastExpiredAssessment($CurrentDate);

            $lastAssessmentId = array();
            foreach ($LAssessmentDetails as $val) {
                $lastAssessmentId[] = isset($val['id']) ? $val['id'] : '0';
                $team_title = $val['assessment'];
            }
            if (isset($lastAssessmentId)) {
                $assessment_id = isset($lastAssessmentId[0]) ? $lastAssessmentId[0] : '';
                $FiveManager = $this->adoption_model->GetFiveManager($assessment_id);
                $TrainerIDSet = array();
                if (!empty($FiveManager)) {
                    for ($l = 0; $l < count($FiveManager); $l++) {
                        $TrainerIDSet[] = $FiveManager[$l]['trainer_id'];
                    }

                    $getUserstart = $this->adoption_model->GetUserManagerwise($lastAssessmentId, $TrainerIDSet);
                    if (isset($getUserstart) && !empty($getUserstart)) {
                        for ($i = 0; $i < count($TrainerIDSet); $i++) {
                            $started[] =  isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                            $completed[] =  isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                            $user_mapped[] =  isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                            $trainer_name[] = isset($getUserstart[$i]['trainer_name']) ? $getUserstart[$i]['trainer_name'] : "Empty Data";
                            $start[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                            $complet[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                        }
                    } else {
                        $started[] =   '0';
                        $completed[] =   '0';
                        $user_mapped[] =  '0';
                        $trainer_name[] =  "Empty Data";
                        $start[] =  '0';
                        $complet[] = '0';
                    }
                } else {
                    $started[] =   '0';
                    $completed[] =   '0';
                    $user_mapped[] =  '0';
                    $trainer_name[] =  "Empty Data";
                    $start[] =  '0';
                    $complet[] = '0';
                }
            } else {
                $started[] =   '0';
                $completed[] =   '0';
                $user_mapped[] =  '0';
                $trainer_name[] =  "Empty Data";
                $start[] =  '0';
                $complet[] = '0';
            }
        } else if ($Assessment_id == ''  && $TrainerIDSet != '') {
            $team_title = '';
            $Assessment_id = '';
            $getUserstart = $this->adoption_model->GetUserManagerwise($Assessment_id, $TrainerIDSet);
            if (isset($getUserstart) && !empty($getUserstart)) {
                for ($i = 0; $i < count($TrainerIDSet); $i++) {
                    $started[] =  isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                    $completed[] =  isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                    $user_mapped[] =  isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                    $trainer_name[] = isset($getUserstart[$i]['trainer_name']) ? $getUserstart[$i]['trainer_name'] : "Empty Data";
                    $start[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $complet[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else {
                $started[] =   '0';
                $completed[] =   '0';
                $user_mapped[] =  '0';
                $trainer_name[] =  "Empty Data";
                $start[] =  '0';
                $complet[] = '0';
            }
        } else {
            $team_title = '';
            // $getManagerName = $this->adoption_model->GetManagerName($TrainerIDSet);
            $getUserstart = $this->adoption_model->GetUserManagerwise($Assessment_id, $TrainerIDSet);
            if (isset($getUserstart) && !empty($getUserstart)) {
                for ($i = 0; $i < count($TrainerIDSet); $i++) {
                    $started[] =  isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                    $completed[] =  isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                    $user_mapped[] =  isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                    $trainer_name[] = isset($getUserstart[$i]['trainer_name']) ? $getUserstart[$i]['trainer_name'] : "Empty Data";
                    $start[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $complet[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else {
                $started[] =   '0';
                $completed[] =   '0';
                $user_mapped[] =  '0';
                $trainer_name[] =  "Empty Data";
                $start[] =  '0';
                $complet[] = '0';
            }
        }
        $Rdata['team_title'] = json_encode($team_title);
        $Rdata['trainer_name'] = json_encode($trainer_name, JSON_NUMERIC_CHECK);
        $Rdata['start'] = json_encode($start, JSON_NUMERIC_CHECK);
        $Rdata['complet'] = json_encode($complet, JSON_NUMERIC_CHECK);
        $Rdata['started'] = json_encode($started, JSON_NUMERIC_CHECK);
        $Rdata['completed'] = json_encode($completed, JSON_NUMERIC_CHECK);
        $Rdata['mapping'] = json_encode($user_mapped, JSON_NUMERIC_CHECK);
        $ad_byTeam = $this->load->view('reports_adoption/AdoptionByTeam', $Rdata, true);
        $data['adb_team'] = $ad_byTeam;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // by Bhautik Rana 04 jan 2022 comments related changes
    // adoption by division
    public function get_adoption_divison()
    {
        $department_set = '';
        $manager_set = '';
        $assessment_id = $this->input->post('assessmentid', TRUE) != '' ? $this->input->post('assessmentid', TRUE) : 0;
        $assessment_list = $this->adoption_model->getdepartment($assessment_id);

        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $department_set .= '<option  value="' . $value->user_id . '">' . $value->department . '</option>';
            }
        }

        $manager_data = $this->adoption_model->get_manager($assessment_id);
        if (count((array)$manager_data) > 0) {
            foreach ($manager_data as $value) {
                $manager_set .= '<option value="' . $value->user_id . '">[' . $value->user_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['division']  = $department_set;
        $data['manager'] = $manager_set;
        echo json_encode($data);
    }

    public function adoption_by_divison($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $DivisionSet = $this->input->post('DivisionSet', TRUE);
        $Managerset = ($this->input->post('Manager_id', TRUE) ? $this->input->post('Manager_id', TRUE) : '');
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);

        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $this->load->model('adoption_model');
        $division_names = array();
        $division_title = '';
        $DivisionStart = array();
        $DivisionCompleted = array();
        $started = array();
        $completed = array();
        $DepartmentIdSet = array();
        $user_mapped = array();
        if ($Assessment_id == '' && $DivisionSet == '') {
            if ($IsCustom == "") {
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->LastExpiredAssessment($CurrentDate);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->lastassessment($startdate, $CurrentDate);
            } else {
                $LAssessmentDetails = $this->adoption_model->lastassessment($SDate, $EDate);
            }
            if (isset($LAssessmentDetails)) {
                $ass_id = array();
                foreach ($LAssessmentDetails as $val) {
                    $ass_id[] = isset($val['id']) ? $val['id'] : '';
                    $division_title = '';
                }
                if ($ass_id != "") {
                    $Department = $this->adoption_model->getdepartment($ass_id);
                } else {
                    $Department = '';
                }
                if (!empty($Department) && isset($Department)) {
                    $DepartmentName = array();
                    foreach ($Department as $dp) {
                        $DepartmentIdSet[] = $dp->user_id;
                        $DepartmentName[] = $dp->department;
                    }
                    if ($IsCustom == "") {
                        $startdate = '';
                        $CurrentDate = date("Y-m-d");
                        $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                    } else if ($IsCustom == "Current Year") {
                        $startdate = date('Y-01-01');
                        $CurrentDate = date("Y-m-d");
                        $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                    } else {
                        $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $SDate, $EDate);
                    }

                    if (isset($getUserstart)) {
                        for ($i = 0; $i < count($getUserstart); $i++) {
                            if (isset($DepartmentName[$i])) {
                                $division_names[] = $DepartmentName[$i];
                            } else {
                                $division_names[] = '';
                            }
                            $started[] = isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                            $completed[] = isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                            $user_mapped[] = isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                            $DivisionStart[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                            $DivisionCompleted[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                        }
                    }
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $DivisionStart[] = '';
                $DivisionCompleted[] = '';
            }
        } else if ($Assessment_id == '' && $DivisionSet != '') {
            //   new code
            if (isset($DivisionSet)) {
                $lastAssessmentId = '';
                $division_title = '';
                $division_name = $this->adoption_model->getdepartment_name($lastAssessmentId, $DivisionSet);
                $DepartmentName = array();
                if (!empty($division_name)) {
                    for ($l = 0; $l < count($division_name); $l++) {
                        $DepartmentIdSet[] = $division_name[$l]['user_id'];
                        $DepartmentName[] = $division_name[$l]['department'];
                    }
                } else {
                    $DepartmentIdSet = '';
                    $DepartmentName = '';
                }
                if (!empty($DepartmentName)) {
                    if ($IsCustom == '') {
                        $startdate = '';
                        $CurrentDate = date("Y-m-d");
                        $getUserstart = $this->adoption_model->GetUserDepartmentwise($lastAssessmentId, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                    } else if ($IsCustom == "Current Year") {
                        $startdate = date('Y-01-01');
                        $CurrentDate = date("Y-m-d");
                        $getUserstart = $this->adoption_model->GetUserDepartmentwise($lastAssessmentId, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                    } else {
                        $getUserstart = $this->adoption_model->GetUserDepartmentwise($lastAssessmentId, $DepartmentName, $Managerset, $SDate, $EDate);
                    }
                    if (isset($getUserstart)) {
                        for ($i = 0; $i < count($getUserstart); $i++) {
                            if (isset($DepartmentName[$i])) {
                                $division_names[] = $DepartmentName[$i];
                            } else {
                                $division_names[] = '';
                            }
                            $started[] = isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                            $completed[] = isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                            $user_mapped[] = isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                            $DivisionStart[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                            $DivisionCompleted[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                        }
                    }
                } else {
                    $started[] = '';
                    $completed[] = '';
                    $user_mapped[] = '';
                    $DivisionStart[] = '';
                    $DivisionCompleted[] = '';
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $DivisionStart[] = '';
                $DivisionCompleted[] = '';
            }
            // new code end 
        } else {
            $division_title = '';
            $division_name = $this->adoption_model->getdepartment_name($Assessment_id, $DivisionSet);
            $ass_id = $Assessment_id;
            $DepartmentName = array();
            for ($l = 0; $l < count($division_name); $l++) {
                $DepartmentIdSet[] = $division_name[$l]['user_id'];
                $DepartmentName[] = $division_name[$l]['department'];
            }
            if (!empty($DepartmentName)) {
                if ($IsCustom == '') {
                    $startdate = '';
                    $CurrentDate = date("Y-m-d");
                    $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                } else if ($IsCustom == "Current Year") {
                    $startdate = date('Y-01-01');
                    $CurrentDate = date("Y-m-d");
                    $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                } else {
                    $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $SDate, $EDate);
                }
                for ($i = 0; $i < count($getUserstart); $i++) {
                    // $index_dataset[] = isset($GetDivisionName[$i]['department']) ? $GetDivisionName[$i]['department'] : "Empty Data";
                    $started[] = isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                    $completed[] = isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                    $user_mapped[] = isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                    $division_names[] = isset($getUserstart[$i]['department_name']) ? $getUserstart[$i]['department_name'] : "Empty Data";
                    $DivisionStart[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $DivisionCompleted[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $DivisionStart[] = '';
                $DivisionCompleted[] = '';
            }
        }
        $Rdata['division_title'] = json_encode($division_title);
        $data['assessment_set'] = $ass_id;
        $data['division_set'] = $DepartmentIdSet;
        $data['manager_set'] = $Managerset;
        $Rdata['division_names'] = json_encode($division_names, JSON_NUMERIC_CHECK);
        $Rdata['DivisionStart'] = json_encode($DivisionStart, JSON_NUMERIC_CHECK);
        $Rdata['DivisionCompleted'] = json_encode($DivisionCompleted, JSON_NUMERIC_CHECK);
        $Rdata['start_count'] = json_encode($started, JSON_NUMERIC_CHECK);
        $Rdata['complete_count'] = json_encode($completed, JSON_NUMERIC_CHECK);
        $Rdata['mapped_count'] = json_encode($user_mapped, JSON_NUMERIC_CHECK);
        $ad_byDivision = $this->load->view('reports_adoption/AdoptionByDivision', $Rdata, true);
        $data['adoption_by_division'] = $ad_byDivision;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }


    // adoption by modules start here
    public function assessment_wise_mrd()
    {
        $region_html = '';
        $division_html = '';
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $Company_id =  $this->input->post('company_id', TRUE);
        $assessment_list = $this->adoption_model->assessment_wise_tariners($assessment_id, $Company_id);
        $region_list = $this->adoption_model->get_region($assessment_id, $Company_id);
        $divsion_list = $this->adoption_model->get_divsion($assessment_id, $Company_id);

        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $mvalue) {
                $assessment_html .= '<option value="' . $mvalue->user_id . '">[' . $mvalue->user_id . '] - ' . $mvalue->user_name . '</option>';
            }
        }

        if (count((array)$region_list) > 0) {
            foreach ($region_list as $rvalue) {
                $region_html .= '<option value="' . $rvalue->region_id . '">' . $rvalue->region_name . '</option>';
            }
        }

        if (count((array)$divsion_list) > 0) {
            foreach ($divsion_list as $dvalue) {
                $division_html .= '<option value="' . $dvalue->department . '">' . $dvalue->department . '</option>';
            }
        }

        $data['region_name']  = $region_html;
        $data['divsion_name']  = $division_html;
        $data['trainers_name']  = $assessment_html;
        echo json_encode($data);
    }

    // Adoption by region start here
    public function assessment_wise_region()
    {
        $assessment_html = '';
        $manager_html = '';
        $Company_id = $this->mw_session['company_id'];
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $assessment_list = $this->adoption_model->assessment_wise_region($assessment_id, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '">[' . $value->region_id . '] - ' . $value->region_name . '</option>';
            }
        }
        $trainer_list = $this->adoption_model->assessment_wise_trainer($assessment_id, $Company_id);
        if (count((array)$trainer_list) > 0) {
            foreach ($trainer_list as $td) {
                $manager_html .= '<option value="' . $td->user_id . '">[' . $td->user_id . '] - ' . $td->user_name . '</option>';
            }
        }
        $data['region']  = $assessment_html;
        $data['manager']  = $manager_html;
        echo json_encode($data);
    }
    public function adoption_by_region($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', TRUE) : '';
        $Region_id = $this->input->post('region_id', TRUE) != '' ? $this->input->post('region_id', TRUE) : '';
        $Manager_id = $this->input->post('manager_id', TRUE) != '' ? $this->input->post('manager_id', TRUE) : '';
        $this->load->model('adoption_model');

        $start_date = $this->input->post('st_date', true);
        $end_date = $this->input->post('end_date', true);
        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';

        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $report_data = array();
        $rigion_start = array();
        $region_completed = array();

        if ($Assessment_id == '' and $Region_id == '') {
            if ($IsCustom == "") {
                $CurrentDate = date("Y-m-d h:i:s");
                $LAssessmentDetails = $this->adoption_model->lastexpiredamt($CurrentDate, $Company_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->lastexpiredamt($startdate, $CurrentDate);
            } else {
                $LAssessmentDetails = $this->adoption_model->lastassessment($SDate, $EDate);
            }
            if (count((array)$LAssessmentDetails) > 0) {
                $last_ass_id = array();
                $lastAssessmentName = array();
                foreach ($LAssessmentDetails as $ld) {
                    $last_ass_id[] = $ld['id'];
                    $lastAssessmentName[] = isset($ld['assessment']) ? $ld['assessment'] : '';
                }
                $regionid = [];
                if (!empty($last_ass_id)) {
                    $Getregion = $this->adoption_model->getregion_id($last_ass_id, $Company_id);
                    if (!empty($Getregion)) {
                        foreach ($Getregion as $rg) {
                            $regionid[] = isset($rg['region_id']) ? $rg['region_id'] : '0';
                        }
                    }
                }
                if (count((array)$regionid) > 0) {
                    if ($IsCustom == "") {
                        $startdate = '';
                        $CurrentDate = date("Y-m-d h:i:s");
                        $getUserstart = $this->adoption_model->Adoption_by_region($last_ass_id, $Company_id, $regionid, $startdate, $CurrentDate, $Manager_id);
                    } else if ($IsCustom == "Current Year") {
                        $startdate = date('Y-01-01');
                        $CurrentDate = date("Y-m-d");
                        $getUserstart = $this->adoption_model->Adoption_by_region($last_ass_id, $Company_id, $regionid, $startdate, $CurrentDate, $Manager_id);
                    } else {
                        $getUserstart = $this->adoption_model->Adoption_by_region($last_ass_id, $Company_id, $regionid, $SDate, $EDate, $Manager_id);
                    }
                    $region_title = isset($getUserstart[0]['assessment']) ? $getUserstart[0]['assessment'] : 'Empty Data';
                    for ($i = 0; $i < count($regionid); $i++) {
                        $started[] =    isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                        $completed[] =    isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                        $user_mapped[] =    isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                        $region_name[] =    isset($getUserstart[$i]['region_name']) ? $getUserstart[$i]['region_name'] : 'No Region';
                        $rigion_start[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                        $region_completed[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                    }
                } else {
                    $started[] = '';
                    $completed[] = '';
                    $user_mapped[] = '';
                    $region_name[] = '';
                    $rigion_start[] = '';
                    $region_completed[] = '';
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $region_name[] = '';
                $rigion_start[] = '';
                $region_completed[] = '';
            }
        } else if ($Assessment_id == '' and $Region_id != '') {
            $lastAssessmentId = '';
            $region_title = '';
            if ($IsCustom == "") {
                $startdate = '';
                $CurrentDate = date("Y-m-d h:i:s");
                $getuserdetails = $this->adoption_model->Adoption_by_region($lastAssessmentId, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $getuserdetails = $this->adoption_model->Adoption_by_region($lastAssessmentId, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
            } else {
                $getuserdetails = $this->adoption_model->Adoption_by_region($lastAssessmentId, $Company_id, $Region_id, $SDate, $EDate, $Manager_id);
            }
            $GetRegionName =  $this->adoption_model->get_region_name($Region_id);
            if (!empty($GetRegionName)) {
                $region_id_set = array();
                foreach ($GetRegionName as $rg) {
                    $region_id_set[] =  $rg['region_id'];
                }
            }
            if (count((array)$getuserdetails) > 0) {
                if (isset($region_id_set)) {
                    for ($i = 0; $i < count($Region_id); $i++) {
                        if (in_array($region_id_set[$i], $getuserdetails)) {
                            $started[] =    isset($getuserdetails[$i]['started']) ? $getuserdetails[$i]['started'] : '0';
                            $completed[] =    isset($getuserdetails[$i]['completed']) ? $getuserdetails[$i]['completed'] : '0';
                            $user_mapped[] =    isset($getuserdetails[$i]['user_mapped']) ? $getuserdetails[$i]['user_mapped'] : '0';
                            $region_name[] =    isset($getuserdetails[$i]['region_name']) ? $getuserdetails[$i]['region_name'] : 'No Region';
                            $rigion_start[] =    isset($getuserdetails[$i]['per_user_strated']) ? $getuserdetails[$i]['per_user_strated'] : '0';
                            $region_completed[] =    isset($getuserdetails[$i]['per_user_completed']) ? $getuserdetails[$i]['per_user_completed'] : '0';
                        } else {
                            $started[] = $getuserdetails[$i]['started'];
                            $completed[] = $getuserdetails[$i]['completed'];
                            $user_mapped = $getuserdetails[$i]['user_mapped'];
                            $region_name[] = $GetRegionName[$i]['region_name'];
                            $rigion_start[] = $getuserdetails[$i]['per_user_strated'];
                            $region_completed[] = $getuserdetails[$i]['per_user_completed'];
                        }
                    }
                } else {
                    $started[] = '';
                    $completed[] = '';
                    $user_mapped[] = '';
                    $region_name[] = '';
                    $rigion_start[] = '';
                    $region_completed[] = '';
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $region_name[] = '';
                $rigion_start[] = '';
                $region_completed[] = '';
            }
        } else {
            if ($IsCustom == '') {
                $startdate = '';
                $CurrentDate = date("Y-m-d");
                if ($Region_id != '') {
                    $getUserstart = $this->adoption_model->Adoption_by_region($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
                } else {
                    $getUserstart = '';
                }
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                if ($Region_id != '') {
                    $getUserstart = $this->adoption_model->Adoption_by_region($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
                } else {
                    $getUserstart = '';
                }
            } else {
                if ($Region_id != '') {
                    $getUserstart = $this->adoption_model->Adoption_by_region($Assessment_id, $Company_id, $Region_id, $SDate, $EDate, $Manager_id);
                } else {
                    $getUserstart = '';
                }
            }

            if ($getUserstart != '') {
                $GetRegionName =  $this->adoption_model->get_region_name($Region_id);
                $Get_region_id = array();
                for ($a = 0; $a < count($getUserstart); $a++) {
                    $Get_region_id[] = $getUserstart[$a]['region_id'];
                }
            }

            if ($getUserstart != '') {
                $region_title = '';
                $y = 0;
                for ($i = 0; $i < count($GetRegionName); $i++) {
                    if (in_array($GetRegionName[$i]['region_id'], $Get_region_id)) {
                        $started[] =    isset($getUserstart[$y]['started']) ? $getUserstart[$y]['started'] : '0';
                        $completed[] =    isset($getUserstart[$y]['completed']) ? $getUserstart[$y]['completed'] : '0';
                        $user_mapped[] =    isset($getUserstart[$y]['user_mapped']) ? $getUserstart[$y]['user_mapped'] : '0';
                        $region_name[] =    isset($getUserstart[$y]['region_name']) ? $getUserstart[$y]['region_name'] : 'No Region';
                        $rigion_start[] =    isset($getUserstart[$y]['per_user_strated']) ? $getUserstart[$y]['per_user_strated'] : '0';
                        $region_completed[] =    isset($getUserstart[$y]['per_user_completed']) ? $getUserstart[$y]['per_user_completed'] : '0';
                        $y++;
                    } else {
                        $started[] = '0';
                        $completed[] = '0';
                        $user_mapped[] = '0';
                        $region_name[] = isset($GetRegionName[$i]['region_name']) ? $GetRegionName[$i]['region_name'] : 'No Region';
                        $rigion_start[] = '0';
                        $region_completed[] = '0';
                    }
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $region_name[] = '';
                $rigion_start[] = '';
                $region_completed[] = '';
            }
        }
        $data['report'] = $report_data;
        $data['last_assessment_id'] = isset($last_ass_id) ? $last_ass_id : '';
        $data['region_id'] = isset($regionid) ? $regionid : '';
        $Rdata['region_title'] = isset($region_title) ? json_encode($region_title) : '';
        $Rdata['region_name'] = json_encode($region_name);
        $Rdata['rigion_start'] = json_encode($rigion_start, JSON_NUMERIC_CHECK);
        $Rdata['region_completed'] = json_encode($region_completed, JSON_NUMERIC_CHECK);
        $Rdata['us_started'] = json_encode($started, JSON_NUMERIC_CHECK);
        $Rdata['us_completed'] = json_encode($completed, JSON_NUMERIC_CHECK);
        $Rdata['us_mapped'] = json_encode($user_mapped, JSON_NUMERIC_CHECK);
        $ad_byRegion = $this->load->view('reports_adoption/AdoptionByRegion', $Rdata, true);
        $data['adoption_by_region'] = $ad_byRegion;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // adoption_by_region end here

    // By Bhautik Rana 09-01-2023 Adoption changes 
    public function adoption_by_module_new($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $this->load->model('adoption_model');
        $report_data = array();
        $module_start = array();
        $start_user_count = array();
        $complete_user_count = array();
        $mapped_user_count = array();
        $modules_name = array();
        $module_start = array();
        $module_completed = array();

        if ($Assessment_id == '') {
            $SDate = date('Y-m-d', strtotime("-30 days"));
            $EDate = date("Y-m-d");
            $LAssessmentDetails = $this->adoption_model->LastExpiredFiveAssessment($SDate, $EDate, $Company_id);
            if (count((array)$LAssessmentDetails) > 0) {
                $lastAssessmentId = array();

                for ($i = 0; $i < count($LAssessmentDetails); $i++) {
                    $lastAssessmentId[] = isset($LAssessmentDetails[$i]['id']) ? $LAssessmentDetails[$i]['id'] : 0;
                    $modules_name[] = isset($LAssessmentDetails[$i]['assessment']) ? $LAssessmentDetails[$i]['assessment'] : 'Empty Data';
                }
                if (isset($lastAssessmentId) && !empty($lastAssessmentId)) {
                    $Assessment_id = $lastAssessmentId;
                    $getUserstart = $this->adoption_model->getuserassessmentbased($lastAssessmentId, $Company_id);
                    if (isset($getUserstart)) {
                        for ($i = 0; $i < count((array)$getUserstart); $i++) {
                            $start_user_count[] =    isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                            $complete_user_count[] =    isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                            $mapped_user_count[] =    isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                            $module_start[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                            $module_completed[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                        }
                    } else {
                        $modules_name[] = "";
                        $start_user_count[] = "";
                        $complete_user_count[] = "";
                        $mapped_user_count[] = "";
                        $module_start[] = "";
                        $module_completed[] = "";
                    }
                } else {
                    $modules_name[] = "";
                    $start_user_count[] = "";
                    $complete_user_count[] = "";
                    $mapped_user_count[] = "";
                    $module_start[] = "";
                    $module_completed[] = "";
                }
            }
        } else {
            $getUserstart = $this->adoption_model->getuserassessmentbased($Assessment_id, $Company_id);
            if (isset($getUserstart)) {
                // $getname = $this->adoption_model->getassessment_name($Assessment_id, $Company_id);
                for ($i = 0; $i < count((array)$getUserstart); $i++) {
                    $start_user_count[] =    isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                    $complete_user_count[] =    isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                    $mapped_user_count[] =    isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                    $modules_name[] =    isset($getUserstart[$i]['assessment']) ? $getUserstart[$i]['assessment'] : 'Empty Data';
                    $module_start[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $module_completed[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else {
                $modules_name[] = "";
                $start_user_count[] = "";
                $complete_user_count[] = "";
                $mapped_user_count[] = "";
                $module_start[] = "";
                $module_completed[] = "";
            }
        }
        $data['report'] = $report_data;
        $data['assessment_id'] = $Assessment_id;
        $Rdata['modules_name'] = json_encode($modules_name);
        $Rdata['module_start'] = json_encode($module_start, JSON_NUMERIC_CHECK);
        $Rdata['module_completed'] = json_encode($module_completed, JSON_NUMERIC_CHECK);
        $Rdata['st_users'] = json_encode($start_user_count, JSON_NUMERIC_CHECK);
        $Rdata['co_users'] = json_encode($complete_user_count, JSON_NUMERIC_CHECK);
        $Rdata['um_users'] = json_encode($mapped_user_count, JSON_NUMERIC_CHECK);
        $ad_byModule = $this->load->view('reports_adoption/AdoptionByModule', $Rdata, true);
        $data['adoption_by_modules'] = $ad_byModule;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // By Bhautik Rana 09-01-2023 Adoption changes 
    // By Bhautik Rana 10-01-2023 Aditional Graph  
    public function get_div_manager()
    {
        $department_set = '';
        $manager_set = '';
        $assessment_id = $this->input->post('assessmentid', TRUE) != '' ? $this->input->post('assessmentid', TRUE) : 0;
        $assessment_list = $this->adoption_model->getdepartment($assessment_id);

        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $department_set .= '<option  value="' . $value->user_id . '">' . $value->department . '</option>';
            }
        }

        $manager_data = $this->adoption_model->get_manager($assessment_id);
        if (count((array)$manager_data) > 0) {
            foreach ($manager_data as $value) {
                $manager_set .= '<option value="' . $value->user_id . '">[' . $value->user_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['division']  = $department_set;
        $data['manager'] = $manager_set;
        echo json_encode($data);
    }
    public function adoption_by_divison_overall($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE);
        $DivisionSet = $this->input->post('DivisionSet', TRUE);
        $Managerset = ($this->input->post('Manager_id', TRUE) ? $this->input->post('Manager_id', TRUE) : '');
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('IsCustom', true) != '' ? $this->input->post('IsCustom', true) : '';


        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $this->load->model('adoption_model');
        $division_names = array();
        $division_title = '';
        $DivisionStart = array();
        $DivisionCompleted = array();
        $started = array();
        $completed = array();
        $DepartmentIdSet = array();
        $user_mapped = array();
        if ($Assessment_id == '' && $DivisionSet == '') {
            if ($IsCustom == "") {
                $start_date = date(("Y-m-d"),strtotime("-30 days"));
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->lastassessment($start_date,$CurrentDate);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->lastassessment($startdate, $CurrentDate);
            } else {
                $LAssessmentDetails = $this->adoption_model->lastassessment($SDate, $EDate);
            }
            if (isset($LAssessmentDetails)) {
                $Assessment_id = array();
                foreach ($LAssessmentDetails as $ad) {
                    $Assessment_id[] = isset($ad['id']) ? $ad['id'] : '';
                }
                if (isset($Assessment_id)) {
                    $ass_id = $Assessment_id;
                    $division_title =  '';
                    if (isset($ass_id)) {
                        $Department = $this->adoption_model->getdepartment($ass_id);
                        $DepartmentIdSet = array();
                        $DepartmentName = array();
                        foreach ($Department as $dp) {
                            $DepartmentIdSet[] = $dp->user_id;
                            $DepartmentName[] = $dp->department;
                        }
                    }
                    if (isset($DepartmentName) && !empty($DepartmentName)) {
                        if ($IsCustom == "") {
                            $startdate =   date(("Y-m-d"),strtotime("-30 days"));
                            $CurrentDate = date("Y-m-d");
                            $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                        } else if ($IsCustom == "Current Year") {
                            $startdate = date('Y-01-01');
                            $CurrentDate = date("Y-m-d");
                            $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                        } else {
                            $getUserstart = $this->adoption_model->GetUserDepartmentwise($ass_id, $DepartmentName, $Managerset, $SDate, $EDate);
                        }
                        if (isset($getUserstart)) {
                            for ($i = 0; $i < count($getUserstart); $i++) {
                                if (isset($DepartmentName[$i])) {
                                    $division_names[] = $DepartmentName[$i];
                                } else {
                                    $division_names[] = '';
                                }
                                $started[] = isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                                $completed[] = isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                                $user_mapped[] = isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                                $DivisionStart[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                                $DivisionCompleted[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                            }
                        }
                    } else {
                        $started[] = '';
                        $completed[] = '';
                        $user_mapped[] = '';
                        $DivisionStart[] = '';
                        $DivisionCompleted[] = '';
                    }
                } else {
                    $started[] = '';
                    $completed[] = '';
                    $user_mapped[] = '';
                    $DivisionStart[] = '';
                    $DivisionCompleted[] = '';
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $DivisionStart[] = '';
                $DivisionCompleted[] = '';
            }
        } else if ($Assessment_id == '' && $DivisionSet != '') {
            //   new code
            if (isset($DivisionSet)) {
                $lastAssessmentId = '';
                $division_title = '';
                $division_name = $this->adoption_model->getdepartment_name($lastAssessmentId, $DivisionSet);
                $DepartmentName = array();
                for ($l = 0; $l < count($division_name); $l++) {
                    $DepartmentIdSet[] = $division_name[$l]['user_id'];
                    $DepartmentName[] = $division_name[$l]['department'];
                }
                if ($IsCustom == '') {
                    $startdate = '';
                    $CurrentDate = '';
                    $getUserstart = $this->adoption_model->GetUserDepartmentwise($lastAssessmentId, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                } else if ($IsCustom == "Current Year") {
                    $startdate = date('Y-01-01');
                    $CurrentDate = date("Y-m-d");
                    $getUserstart = $this->adoption_model->GetUserDepartmentwise($lastAssessmentId, $DepartmentName, $Managerset, $startdate, $CurrentDate);
                } else {
                    $getUserstart = $this->adoption_model->GetUserDepartmentwise($lastAssessmentId, $DepartmentName, $Managerset, $SDate, $EDate);
                }
                if (isset($getUserstart)) {
                    for ($i = 0; $i < count($getUserstart); $i++) {
                        if (isset($DepartmentName[$i])) {
                            $division_names[] = $DepartmentName[$i];
                        } else {
                            $division_names[] = '';
                        }
                        $started[] = isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                        $completed[] = isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                        $user_mapped[] = isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                        $DivisionStart[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                        $DivisionCompleted[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                    }
                }
            } else {
                $started[] = '';
                $completed[] = '';
                $user_mapped[] = '';
                $DivisionStart[] = '';
                $DivisionCompleted[] = '';
            }
            // new code end 
        } else {
            $division_title = '';
            $division_name = $this->adoption_model->getdepartment_name($Assessment_id, $DivisionSet);
            $DepartmentName = array();
            for ($l = 0; $l < count($division_name); $l++) {
                $DepartmentIdSet[] = $division_name[$l]['user_id'];
                $DepartmentName[] = $division_name[$l]['department'];
            }
            if ($IsCustom == '') {
                $startdate = '';
                $CurrentDate = '';
                $getUserstart = $this->adoption_model->GetUserDepartmentwise($Assessment_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $getUserstart = $this->adoption_model->GetUserDepartmentwise($Assessment_id, $DepartmentName, $Managerset, $startdate, $CurrentDate);
            } else {
                $getUserstart = $this->adoption_model->GetUserDepartmentwise($Assessment_id, $DepartmentName, $Managerset, $SDate, $EDate);
            }
            for ($i = 0; $i < count($getUserstart); $i++) {
                // $index_dataset[] = isset($GetDivisionName[$i]['department']) ? $GetDivisionName[$i]['department'] : "Empty Data";
                $started[] = isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                $completed[] = isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                $user_mapped[] = isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                $division_names[] = isset($getUserstart[$i]['department_name']) ? $getUserstart[$i]['department_name'] : "Empty Data";
                $DivisionStart[] = isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                $DivisionCompleted[] = isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
            }
        }
        $Rdata['division_title'] = json_encode($division_title);
        $data['assessment_id'] = $Assessment_id;
        $data['division_id'] = $DepartmentIdSet;
        $data['manager_id'] = $Managerset;
        $Rdata['division_names'] = json_encode($division_names, JSON_NUMERIC_CHECK);
        $Rdata['DivisionStart'] = json_encode($DivisionStart, JSON_NUMERIC_CHECK);
        $Rdata['DivisionCompleted'] = json_encode($DivisionCompleted, JSON_NUMERIC_CHECK);
        $Rdata['start_count'] = json_encode($started, JSON_NUMERIC_CHECK);
        $Rdata['complete_count'] = json_encode($completed, JSON_NUMERIC_CHECK);
        $Rdata['mapped_count'] = json_encode($user_mapped, JSON_NUMERIC_CHECK);
        $ad_byDivision = $this->load->view('reports_adoption/AdoptionByDivisionOverall', $Rdata, true);
        $data['adoption_by_division_overall'] = $ad_byDivision;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // By Bhautik Rana 10-01-2023 Aditional Graph  

    // Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  start here "Nirmal Gajjar"
    // Adoption by team (overall) "09-01-2023" start here "Nirmal Gajjar"
    public function Getassessment_wise_d_r_m()
    {
        $assessment_html = '';
        $assessment_id = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);
        $Company_id =  $this->input->post('company_id', TRUE);
        $assessment_list = $this->adoption_model->assessment_wise_managers($assessment_id, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $mvalue) {
                $assessment_html .= '<option value="' . $mvalue->user_id . '">[' . $mvalue->user_id . '] - ' . $mvalue->user_name . '</option>';
            }
        }
        $data['cm_managers']  = $assessment_html;
        echo json_encode($data);
    }
    public function adoption_by_manager($returnflag = 0)
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
        $this->load->model('adoption_model');
        $report_data = array();
        $abm_trainer_name = array();
        $abm_start = array();
        $abm_complet = array();
        $abm_started = array();
        $abm_completed = array();
        $abm_user_mapped = array();

        if ($Assessment_id == '' and $manager_id == "") {

            if ($IsCustom == "") {
                $SDate = date(("Y-m-d"),strtotime("-30 days"));
                $EDate = date("Y-m-d");
                $getfiveassessment = $this->adoption_model->Last_assessment($SDate, $EDate);
            } else {
                $getfiveassessment = $this->adoption_model->Last_assessment($SDate, $EDate);
            }

            foreach ($getfiveassessment as $rld) {
                $lastAssessmentId[] = $rld['id'] != '' ? $rld['id'] : '';
            }
            $Assessment_id = $lastAssessmentId;

            if (isset($Assessment_id) && !empty($Assessment_id)) {

                $assessment_list = $this->adoption_model->get_manager_details($Company_id, $Assessment_id);
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
                        $getmanager_score = $this->adoption_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
                    } else {
                        $getmanager_score = $this->adoption_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
                    }
                    if (!empty($getmanager_score)) {
                        for ($i = 0; $i < count($getmanager_score); $i++) {
                            $abm_started[] =  isset($getmanager_score[$i]['started']) ? $getmanager_score[$i]['started'] : '0';
                            $abm_completed[] =  isset($getmanager_score[$i]['completed']) ? $getmanager_score[$i]['completed'] : '0';
                            $abm_user_mapped[] =  isset($getmanager_score[$i]['user_mapped']) ? $getmanager_score[$i]['user_mapped'] : '0';
                            $abm_start[] = isset($getmanager_score[$i]['per_user_strated']) ? $getmanager_score[$i]['per_user_strated'] : '0';
                            $abm_complet[] = isset($getmanager_score[$i]['per_user_completed']) ? $getmanager_score[$i]['per_user_completed'] : '0';
                            $abm_trainer_name[] = isset($getmanager_score[$i]['trainer_name']) ? $getmanager_score[$i]['trainer_name'] : "Empty Data";
                        }
                    } else {
                        $abm_started[] = '';
                        $abm_completed[] = '';
                        $abm_user_mapped[] = '';
                        $abm_start[] = '';
                        $abm_complet[] = '';
                        $abm_trainer_name[] = '';
                    }
                } else {
                    $abm_started[] = '';
                    $abm_completed[] = '';
                    $abm_user_mapped[] = '';
                    $abm_start[] = '';
                    $abm_complet[] = '';
                    $abm_trainer_name[] = '';
                }
            } else {
                $abm_started[] = '';
                $abm_completed[] = '';
                $abm_user_mapped[] = '';
                $abm_start[] = '';
                $abm_complet[] = '';
                $abm_trainer_name[] = '';
            }
        } elseif ($Assessment_id == ''  and $manager_id != "") {

            $Assessment_id = '';
            if ($IsCustom == "") {
                $SDate = '';
                $EDate = '';
                $getmanager_score = $this->adoption_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            } else {
                $getmanager_score = $this->adoption_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            }

            if (!empty($getmanager_score)) {
                for ($i = 0; $i < count($getmanager_score); $i++) {
                    $abm_started[] =  isset($getmanager_score[$i]['started']) ? $getmanager_score[$i]['started'] : '0';
                    $abm_completed[] =  isset($getmanager_score[$i]['completed']) ? $getmanager_score[$i]['completed'] : '0';
                    $abm_user_mapped[] =  isset($getmanager_score[$i]['user_mapped']) ? $getmanager_score[$i]['user_mapped'] : '0';
                    $abm_start[] = isset($getmanager_score[$i]['per_user_strated']) ? $getmanager_score[$i]['per_user_strated'] : '0';
                    $abm_complet[] = isset($getmanager_score[$i]['per_user_completed']) ? $getmanager_score[$i]['per_user_completed'] : '0';
                    $abm_trainer_name[] = isset($getmanager_score[$i]['trainer_name']) ? $getmanager_score[$i]['trainer_name'] : "Empty Data";
                }
            } else {
                $abm_started[] = '';
                $abm_completed[] = '';
                $abm_user_mapped[] = '';
                $abm_start[] = '';
                $abm_complet[] = '';
                $abm_trainer_name[] = '';
            }
        } else {
            if ($IsCustom == "") {
                $SDate = '';
                $EDate = '';
                $getmanager_score = $this->adoption_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            } else {
                $getmanager_score = $this->adoption_model->Getscoremanagerwise($Assessment_id, $Company_id, $manager_id, $SDate, $EDate);
            }
            if (!empty($getmanager_score)) {
                for ($i = 0; $i < count($getmanager_score); $i++) {
                    $abm_started[] =  isset($getmanager_score[$i]['started']) ? $getmanager_score[$i]['started'] : '0';
                    $abm_completed[] =  isset($getmanager_score[$i]['completed']) ? $getmanager_score[$i]['completed'] : '0';
                    $abm_user_mapped[] =  isset($getmanager_score[$i]['user_mapped']) ? $getmanager_score[$i]['user_mapped'] : '0';
                    $abm_start[] = isset($getmanager_score[$i]['per_user_strated']) ? $getmanager_score[$i]['per_user_strated'] : '0';
                    $abm_complet[] = isset($getmanager_score[$i]['per_user_completed']) ? $getmanager_score[$i]['per_user_completed'] : '0';
                    $abm_trainer_name[] = isset($getmanager_score[$i]['trainer_name']) ? $getmanager_score[$i]['trainer_name'] : "Empty Data";
                }
            } else {
                $abm_started[] = '';
                $abm_completed[] = '';
                $abm_user_mapped[] = '';
                $abm_start[] = '';
                $abm_complet[] = '';
                $abm_trainer_name[] = '';
            }
        }
        $data['report'] = $report_data;
        $Rdata['abm_trainer_name'] = json_encode($abm_trainer_name, JSON_NUMERIC_CHECK);
        $Rdata['abm_start'] = json_encode($abm_start, JSON_NUMERIC_CHECK);
        $Rdata['abm_complet'] = json_encode($abm_complet, JSON_NUMERIC_CHECK);
        $Rdata['abm_started'] = json_encode($abm_started, JSON_NUMERIC_CHECK);
        $Rdata['abm_completed'] = json_encode($abm_completed, JSON_NUMERIC_CHECK);
        $Rdata['abm_mapping'] = json_encode($abm_user_mapped, JSON_NUMERIC_CHECK);

        $a_b_managers = $this->load->view('reports_adoption/adoption_by_manager', $Rdata, true);
        $data['a_b_managers'] = $a_b_managers;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    // Adoption by region (overall) "10-01-2023" start here
    public function adoption_by_region_filters()
    {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $assessmentid = ($this->input->post('assessmentid', TRUE) ? $this->input->post('assessmentid', TRUE) : 0);

        $assessment_html = '';
        $assessment_list = $this->adoption_model->am_wise_region($assessmentid, $Company_id);
        if (count((array)$assessment_list) > 0) {
            foreach ($assessment_list as $value) {
                $assessment_html .= '<option value="' . $value->region_id . '"> [' . $value->region_id . '] - ' . $value->region_name . '</option>';
            }
        }
        $manager = '';
        $manager_list = $this->adoption_model->am_wise_managers($assessmentid);
        if (count((array)$manager_list) > 0) {
            foreach ($manager_list as $value) {
                $manager .= '<option value="' . $value->users_id . '"> [' . $value->users_id . '] - ' . $value->user_name . '</option>';
            }
        }
        $data['a_b_region']  = $assessment_html;
        $data['a_b_manager']  = $manager;
        echo json_encode($data);
    }

    public function ad_by_region($returnflag = 0)
    {
        $data = array();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Assessment_id = $this->input->post('assessment_id', TRUE) != '' ? $this->input->post('assessment_id', TRUE) : '';
        $Region_id = $this->input->post('reg_id', TRUE) != '' ? $this->input->post('reg_id', TRUE) : '';
        $Manager_id = $this->input->post('manager_id', TRUE) != '' ? $this->input->post('manager_id', TRUE) : '';
        $this->load->model('adoption_model');

        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $IsCustom = $this->input->post('ad_by_IsCustom', true) != '' ? $this->input->post('ad_by_IsCustom', true) : '';

        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $report_data = array();
        $started_count = array();
        $completed_count = array();
        $user_mapped_count = array();
        $region_name_count = array();
        $rigion_start_count = array();
        $region_completed_count = array();

        if ($Assessment_id == '' and $Region_id == '' and $Manager_id == '') {
            if ($IsCustom == "") {
                $startdate =date(('Y-01-01'),strtotime("-30 days"));
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->last_expierd_assessment($startdate, $CurrentDate);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $LAssessmentDetails = $this->adoption_model->last_expierd_assessment($startdate, $CurrentDate);
            } else {
                $LAssessmentDetails = $this->adoption_model->last_expierd_assessment($SDate, $EDate);
            }
            if (count((array)$LAssessmentDetails) > 0) {
                $lastAssessmentId = array();
                foreach ($LAssessmentDetails as $rld) {
                    $lastAssessmentId[] = $rld['id'] != '' ? $rld['id'] : '';
                }
                $Assessment_id = $lastAssessmentId;

                $regionid = array();
                if (isset($Assessment_id) &&  !empty($Assessment_id)) {
                    $Getregion = $this->adoption_model->get_last_region_id($Assessment_id, $Company_id);
                    if (!empty($Getregion)) {
                        for ($f = 0; $f < count($Getregion); $f++) {
                            $regionid[] = isset($Getregion[$f]['region_id']) ? $Getregion[$f]['region_id'] : '0';
                        }
                    }
                    $Region_id = $regionid;
                    if (count((array)$Region_id) > 0) {
                        if ($IsCustom == "") {
                            $startdate = date(("Y-m-d"),strtotime("-30 days"));
                            $CurrentDate = date("Y-m-d");
                            $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
                        } else if ($IsCustom == "Current Year") {
                            $startdate = date('Y-01-01');
                            $CurrentDate = date("Y-m-d");
                            $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
                        } else {
                            $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $SDate, $EDate, $Manager_id);
                        }
                        $region_title = '';
                        if (isset($getUserstart) && !empty($getUserstart)) {
                            for ($i = 0; $i < count($regionid); $i++) {
                                $started_count[] =    isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                                $completed_count[] =    isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                                $user_mapped_count[] =    isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                                $region_name_count[] =    isset($getUserstart[$i]['region_name']) ? $getUserstart[$i]['region_name'] : 'No Region';
                                $rigion_start_count[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                                $region_completed_count[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                            }
                        } else {
                            $started_count[] = '';
                            $completed_count[] = '';
                            $user_mapped_count[] = '';
                            $region_name_count[] = '';
                            $rigion_start_count[] = '';
                            $region_completed_count[] = '';
                        }
                    } else {
                        $started_count[] = '';
                        $completed_count[] = '';
                        $user_mapped_count[] = '';
                        $region_name_count[] = '';
                        $rigion_start_count[] = '';
                        $region_completed_count[] = '';
                    }
                } else {
                    $started_count[] = '';
                    $completed_count[] = '';
                    $user_mapped_count[] = '';
                    $region_name_count[] = '';
                    $rigion_start_count[] = '';
                    $region_completed_count[] = '';
                }
            } else {
                $started_count[] = '';
                $completed_count[] = '';
                $user_mapped_count[] = '';
                $region_name_count[] = '';
                $rigion_start_count[] = '';
                $region_completed_count[] = '';
            }
        } else if ($Assessment_id == '' and $Region_id != '') {
            $Assessment_id = '';
            if ($IsCustom == "") {
                $startdate = '';
                $CurrentDate = '';
                $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
            } else {
                $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $SDate, $EDate, $Manager_id);
            }
            $region_title = '';
            if (isset($getUserstart) && !empty($getUserstart)) {
                for ($i = 0; $i < count($Region_id); $i++) {
                    $started_count[] =    isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                    $completed_count[] =    isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                    $user_mapped_count[] =    isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                    $region_name_count[] =    isset($getUserstart[$i]['region_name']) ? $getUserstart[$i]['region_name'] : 'No Region';
                    $rigion_start_count[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $region_completed_count[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else {
                $started_count[] = '';
                $completed_count[] = '';
                $user_mapped_count[] = '';
                $region_name_count[] = '';
                $rigion_start_count[] = '';
                $region_completed_count[] = '';
            }
        } else {
            if ($IsCustom == "") {
                $startdate = '';
                $CurrentDate = '';
                $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
            } else if ($IsCustom == "Current Year") {
                $startdate = date('Y-01-01');
                $CurrentDate = date("Y-m-d");
                $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $startdate, $CurrentDate, $Manager_id);
            } else {
                $getUserstart = $this->adoption_model->get_region_wise_score($Assessment_id, $Company_id, $Region_id, $SDate, $EDate, $Manager_id);
            }
            $region_title = '';
            if (isset($getUserstart) && !empty($getUserstart)) {
                for ($i = 0; $i < count($Region_id); $i++) {
                    $started_count[] =    isset($getUserstart[$i]['started']) ? $getUserstart[$i]['started'] : '0';
                    $completed_count[] =    isset($getUserstart[$i]['completed']) ? $getUserstart[$i]['completed'] : '0';
                    $user_mapped_count[] =    isset($getUserstart[$i]['user_mapped']) ? $getUserstart[$i]['user_mapped'] : '0';
                    $region_name_count[] =    isset($getUserstart[$i]['region_name']) ? $getUserstart[$i]['region_name'] : 'No Region';
                    $rigion_start_count[] =    isset($getUserstart[$i]['per_user_strated']) ? $getUserstart[$i]['per_user_strated'] : '0';
                    $region_completed_count[] =    isset($getUserstart[$i]['per_user_completed']) ? $getUserstart[$i]['per_user_completed'] : '0';
                }
            } else {
                $started_count[] = '';
                $completed_count[] = '';
                $user_mapped_count[] = '';
                $region_name_count[] = '';
                $rigion_start_count[] = '';
                $region_completed_count[] = '';
            }
        }
        $data['report'] = $report_data;
        $data['amt_id'] = isset($Assessment_id) ? $Assessment_id : '';
        $data['rg_id'] = isset($regionid) ? $regionid : '';
        $Rdata['region_title'] = isset($region_title) ? json_encode($region_title) : '';
        $Rdata['region_name_count'] = json_encode($region_name_count);
        $Rdata['rigion_start_count'] = json_encode($rigion_start_count, JSON_NUMERIC_CHECK);
        $Rdata['region_completed_count'] = json_encode($region_completed_count, JSON_NUMERIC_CHECK);
        $Rdata['started_count'] = json_encode($started_count, JSON_NUMERIC_CHECK);
        $Rdata['completed_count'] = json_encode($completed_count, JSON_NUMERIC_CHECK);
        $Rdata['user_mapped_count'] = json_encode($user_mapped_count, JSON_NUMERIC_CHECK);
        $ab_region = $this->load->view('reports_adoption/adoption_by_region', $Rdata, true);
        $data['adption_by_region'] = $ab_region;
        if ($returnflag) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    // Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  end here "Nirmal Gajjar"
}
