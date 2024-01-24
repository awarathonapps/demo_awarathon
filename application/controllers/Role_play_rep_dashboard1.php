<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class role_play_rep_dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('role_play_rep_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('role_play_rep_dashboard_model');
    }

    public function index() {
        $data['module_id'] = '104';
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        if ($data['company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
        }

        $data['trainee'] = $this->role_play_rep_dashboard_model->get_user($data['company_id']);
        $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');        
		$data['start_date'] = date('d-M-Y', strtotime('-29 days'));
        $data['end_date'] = date("d-m-Y");
        $start_date = date('Y-m-d', strtotime('-29 days'));
        $end_date = date("Y-m-d");
		
		$user_set = $this->session->userdata('awarathon_session');
		$manager_id = $user_set['login_type']==2 ? $user_set['user_id'] : '';
        $data['trainee_data'] = $this->role_play_rep_dashboard_model->get_Trainee_data($data['company_id'],$manager_id);
        // $data['trainee_data'] = $this->role_play_rep_dashboard_model->get_Trainee($data['company_id'],$start_date,$end_date);
        //$data['parameter_data'] = $this->role_play_rep_dashboard_model->get_parameter($data['company_id']);
        $data['parameter'] = $this->common_model->get_selected_values('parameter_mst', 'id as parameter_id,description as parameter', 'status=1', 'description');
        $this->load->view('role_play_rep_dashboard/index', $data);
    }

    public function getdashboardData() {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $question_type = $this->input->post('question_type', true);
        $report_by = $this->input->post('report_by', true);
        $user_id = $this->input->post('user_id', true);
        $report_type = $this->input->post('report_type', true);
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $data=$this->load_assessment_index(1);
        $data['Total_Assessment'] = $this->role_play_rep_dashboard_model->get_Total_Assessment($Company_id, $SDate, $EDate, $user_id, $report_type);
        $data['question_answer'] = $this->role_play_rep_dashboard_model->get_Total_Questions_Time($Company_id, $SDate, $EDate, $user_id, $report_type);
        //$data['Total_Questions'] = $Questions_Time['TotalQ'];
        //$data['Total_Time'] = $Questions_Time['TotalT'];
        $time= $this->role_play_rep_dashboard_model->get_time($Company_id, $SDate, $EDate, $user_id);
       //print_r($time);
        $data['Total_Time'] =$time->total_time;
        $data['Avg_Accuracy'] = $this->role_play_rep_dashboard_model->get_Average_Accuracy($Company_id,$report_by, $SDate, $EDate, $user_id, $report_type);
//        
        $TopFiveParameter = $this->role_play_rep_dashboard_model->get_top_five_parameter($Company_id, $report_by, $SDate, $EDate, $user_id, $report_type);
        
        //--- High and Low Accuracy--//
        $data['high_Accuracy']= 0;
        $data['low_Accuracy']= 0;
        if(count((array)$TopFiveParameter) > 0){
            $data['high_Accuracy'] = $TopFiveParameter[0]->result;
            $existid = ($report_by == 1 ? $TopFiveParameter[0]->parameter_id : $TopFiveParameter[0]->assessment_id);
            $minSet = $this->role_play_rep_dashboard_model->get_bottom_five_parameter($Company_id, $report_by, $existid, $SDate, $EDate, $user_id, $report_type);
            if(count((array)$minSet)>0){
             $data['low_Accuracy'] = $minSet[0]->result;
            }
        }
        //----- End -----//
        $top_five_para_id = "0,";
        $para_top_five_html = '';
        if (count((array)$TopFiveParameter) > 0) {
            foreach ($TopFiveParameter as $para_top) {
                if ($report_by == 1) {
                    $top_five_para_id .= $para_top->parameter_id . ",";
                } else {
                    $top_five_para_id .= $para_top->assessment_id . ",";
                }
                $para_top_five_html .= '<tr class="tr-background">';
                if ($report_by == 1) {
                    $para_top_five_html .= '<td class="wksh-td">' . $para_top->parameter . '</td>';
                } else {
                    $para_top_five_html .= '<td class="wksh-td">' . $para_top->assessment . '</td>';
                }
                $para_top_five_html .= '<td class="wksh-td">
                                                <span class="bold theme-font">' . $para_top->result . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $para_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        if ($top_five_para_id != '') {
            $top_five_para_id = substr($top_five_para_id, 0, strlen($top_five_para_id) - 1);
        }
        $data['para_top_five_html'] = $para_top_five_html;

        $BottomFiveParameter = $this->role_play_rep_dashboard_model->get_bottom_five_parameter($Company_id, $report_by, $top_five_para_id, $SDate, $EDate, $user_id, $report_type);

        $para_bottom_five_html = '';
        if (count((array)$BottomFiveParameter) > 0) {
            foreach ($BottomFiveParameter as $para_bottom) {
                $para_bottom_five_html .= '<tr class="tr-background">';
                if ($report_by == 1) {
                    $para_bottom_five_html .= '<td class="wksh-td">' . $para_bottom->parameter . '</td>';
                } else {
                    $para_bottom_five_html .= '<td class="wksh-td">' . $para_bottom->assessment . '</td>';
                }
                $para_bottom_five_html .= '<td class="wksh-td">
                                                <span class="bold theme-font">' . $para_bottom->result . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $para_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        $data['para_bottom_five_table'] = $para_bottom_five_html;

        //$parameter_data = $this->role_play_rep_dashboard_model->get_parameter($Company_id,$user_id);
        $parameter_data = $this->role_play_rep_dashboard_model->get_assessment($user_id, $SDate, $EDate, $report_type);
        $parahtml = '<option value="">All</option>';
        if (count((array)$parameter_data) > 0) {
            foreach ($parameter_data as $value) {
                $parahtml .= '<option value="' . $value->assessment_id . '">' . $value->assessment . '</option>';
            }
        }
        $data['parahtml'] = $parahtml;
        echo json_encode($data);
    }
    

    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function load_assessment_index($returnflag=0) {
       $data = array();
       $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $report_by = $this->input->post('report_by', true);
        $user_id = $this->input->post('user_id', true);
        $report_type = $this->input->post('report_type', true);
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        
        $rpt_period = $this->input->post('rpt_period', true);
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $report_data = array();
        $index_dataset = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $Year = $this->input->post('year', true);
        $Week = $this->input->post('week', true);
        $WeekStartDate = '';
        $WeekEndDate = '';
        if ($Week != '' && $Month != '' && $Year != '') {
            $WeekDate = explode('-', $Week);
            $WeekStartDay = $WeekDate[0];
            $WeekEndDay = $WeekDate[1];
            $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
        }
        if ($rpt_period == "weekly") {
            if ($WeekStartDate != '' && $WeekEndDate != '') {
                $AccuracySet = $this->role_play_rep_dashboard_model->assessment_index_weekly_monthly($report_by,$WeekStartDate, $WeekEndDate, $parameter_id, $user_id, $report_type);
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
                }
            } else {
                $WeekStartDate = date('Y-m-d', strtotime("-6 days"));
                $WeekEndDate = $current_date;
                $StartStrDt = date('d-m-Y', strtotime("-6 days"));
                $EndStrDt = date('d-m-Y');
                $StartWeek = date('d', strtotime("-6 days"));
                $EndWeek = date('d');
                $AccuracySet = $this->role_play_rep_dashboard_model->assessment_index_weekly_monthly($report_by,$WeekStartDate, $WeekEndDate,  $user_id, $report_type);
                for ($i = $StartWeek; $i <= $EndWeek; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-m-' . $i);
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
                }
            }
            $report_xaxis_title = 'Weekly';
        } elseif ($rpt_period == "monthly") {
            if ($Year != '' && $Month != '' && $Month != $current_month) {
                $StartDate = $Year . '-' . $Month . '-01';
                $WeekStartDate = $StartDate;
                $StartStrDt = '01-' . $Month . '-' . $Year;
                $noofdays = date('t', strtotime($StartDate));
                $EndDate = $Year . '-' . $Month . '-' . $noofdays;
                $WeekEndDate = $EndDate;
                $EndStrDt = $noofdays . '-' . $Month . '-' . $Year;
            } else {
                $WeekStartDate = Date('Y-m-1');
                $WeekEndDate = $current_date;
                $noofdays = Date('d');
            }

            $report_xaxis_title = 'Monthly';
            $AccuracySet = $this->role_play_rep_dashboard_model->assessment_index_weekly_monthly($report_by,$WeekStartDate, $WeekEndDate,  $user_id, $report_type);
            $WeekNo = 1;
            $Divider = 0;
            for ($i = 1; $i <= $noofdays; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = $Year . '-' . $Month . '-' . $day;
                if (isset($AccuracySet['period'][$day])) {
                    $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("d-M", strtotime($TempDate));
            }
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';

            $report_xaxis_title = 'Yearly';
            $AccuracySet = $this->role_play_rep_dashboard_model->assessment_index_yearly($report_by,$WeekStartDate, $WeekEndDate, $user_id, $report_type);
            for ($i = 1; $i <= 12; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($AccuracySet['period'][$i])) {
                    $index_dataset[] = json_encode($AccuracySet['period'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        }
        $report_title = 'Assessment Index - (Period From ' . date('d-m-Y', strtotime($WeekStartDate)) . ' To ' . date('d-m-Y', strtotime($WeekEndDate)).')';
		$parameter_id = $this->input->post('parameter_id', true);
		if($parameter_id !=''){
			$data=$this->load_parameter_index(1);
		}
		
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $indexGraph = $this->load->view('role_play_rep_dashboard/assessment_index_report', $Rdata, true);
        $data['index_graph'] = $indexGraph;
		
        if($returnflag){
            return $data;
        }else{
            echo json_encode($data);
        }
	}
	public function load_parameter_index($returnflag=0) {
       $data = array();
       $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $report_by = $this->input->post('report_by', true);
        $user_id = $this->input->post('user_id', true);
        $report_type=$this->input->post('report_type', true);
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        
        $rpt_period = $this->input->post('rpt_period', true);
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $report_data = array();
        
        
        $index_paradataset = [];
        $index_paralabel = [];
        $report_paratitle = '';
        $report_xaxis_paratitle = '';
        
        $parameter_id = $this->input->post('parameter_id', true);
        //print_r($parameter_id);
        //exit;
        $Month = $this->input->post('month', true);
        $Year = $this->input->post('year', true);
        $Week = $this->input->post('week', true);
        $WeekStartDate = '';
        $WeekEndDate = '';
        if ($Week != '' && $Month != '' && $Year != '') {
            $WeekDate = explode('-', $Week);
            $WeekStartDay = $WeekDate[0];
            $WeekEndDay = $WeekDate[1];
            $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
        }
        if ($rpt_period == "weekly") {
            if ($WeekStartDate != '' && $WeekEndDate != '') {
                $AccuracySet = $this->role_play_rep_dashboard_model->parameter_index_charts_new($parameter_id, $report_by,$WeekStartDate, $WeekEndDate,  $user_id, $report_type);
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
					$index_paralabel[] = $assess->assessment;
                }
            } else {
                $WeekStartDate = date('Y-m-d', strtotime("-6 days"));
                $WeekEndDate = $current_date;
                $StartStrDt = date('d-m-Y', strtotime("-6 days"));
                $EndStrDt = date('d-m-Y');
                $StartWeek = date('d', strtotime("-6 days"));
                $EndWeek = date('d');
                $AccuracySet = $this->role_play_rep_dashboard_model->parameter_index_charts($parameter_id, $report_by,$WeekStartDate, $WeekEndDate,  $user_id);
            }
            $report_xaxis_title = 'Weekly';
        } elseif ($rpt_period == "monthly") {
            if ($Year != '' && $Month != '' && $Month != $current_month) {
                $StartDate = $Year . '-' . $Month . '-01';
                $WeekStartDate = $StartDate;
                $StartStrDt = '01-' . $Month . '-' . $Year;
                $noofdays = date('t', strtotime($StartDate));
                $EndDate = $Year . '-' . $Month . '-' . $noofdays;
                $WeekEndDate = $EndDate;
                $EndStrDt = $noofdays . '-' . $Month . '-' . $Year;
            } else {
                $WeekStartDate = Date('Y-m-1');
                $WeekEndDate = $current_date;
                $noofdays = Date('d');
            }
				$report_xaxis_title = 'Monthly';
				$AccuracySet = $this->role_play_rep_dashboard_model->parameter_index_charts($parameter_id, $report_by,$WeekStartDate, $WeekEndDate,  $user_id);
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';

            $report_xaxis_title = 'Yearly';
            $AccuracySet = $this->role_play_rep_dashboard_model->parameter_index_charts_new($parameter_id,$report_by,$WeekStartDate, $WeekEndDate,  $user_id, $report_type);
            
        }
        $AccuracySet = $this->role_play_rep_dashboard_model->parameter_index_charts_new($parameter_id,$report_by,$WeekStartDate, $WeekEndDate,  $user_id, $report_type);
        
        if(count((array)$AccuracySet) > 0){
            foreach($AccuracySet as  $assess ){
                $index_paradataset[] = json_encode($assess['result'], JSON_NUMERIC_CHECK);
                $index_paralabel[] = $assess['parameter_name'];
            }
        }
        //$report_paratitle = 'Parameter Index - (Period From ' . date('d-m-Y', strtotime($WeekStartDate)) . ' To ' . date('d-m-Y', strtotime($WeekEndDate));
        if($parameter_id !=''){
//            $parameter_data = $this->common_model->get_value('parameter_mst', 'id,description', 'status=1 and id='.$parameter_id);
              $parameter_data = $this->common_model->get_value('assessment_mst', 'id,assessment', 'id='.$parameter_id);
            $report_paratitle = ' <strong>'.$parameter_data->assessment.'</strong>';
        }else{
            $report_paratitle .= ' <strong>All Parameters</strong>)';
        }
        
        $PRdata['report_paraperiod'] = $report_xaxis_title;
		$PRdata['report_paratitle'] = $report_paratitle;
        $PRdata['index_paradataset'] = json_encode($index_paradataset, JSON_NUMERIC_CHECK);
        $PRdata['index_paralabel'] = json_encode($index_paralabel);
        $indexAssessGraph = $this->load->view('role_play_rep_dashboard/parameter_index_report', $PRdata, true);
        $data['index_paragraph'] = $indexAssessGraph;
        
        if($returnflag){
            return $data;
        }else{
            echo json_encode($data);
        }
    }
}