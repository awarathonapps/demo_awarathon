<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainee_dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainee_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('trainee_dashboard_model');
    }

    public function index() {
        $data['module_id'] = '12.02';
        $data['username'] = $this->mw_session['username'];
        $data['trainee_name'] = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        $data['company_id'] = $this->mw_session['company_id'];
        $data['user_id'] = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;
        $Trainee_id = "";
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['company_array'] = array();
            
            if ($this->mw_session['login_type'] != 3) {
                
                if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                    $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
                }
                if (!$WRightsFlag) {
                    $this->common_model->SyncWorkshopRights($Login_id,0);
                }
                $data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'],$WRightsFlag);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'],$WRightsFlag);
            } else {
                $Trainee_id = $this->mw_session['user_id'];
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'],1);
            }
        }
        $data['DefaultTrainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        $data['wksh_top_five_array'] = [];
        $this->load->view('trainee_dashboard/index', $data);
    }

    public function ajax_companywise_users() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($company_id != '') {
            $data['user_array'] = $this->common_model->getUserTraineeList($company_id,1);
            $data['wtype_array'] = $this->common_model->getWTypeRightsList($company_id,1);
            $YearDateSet = $this->trainee_dashboard_model->getDistinctWorkshopYear($company_id);
            $lcoptionStr = "";
            if (count((array)$YearDateSet) > 0) {
                foreach ($YearDateSet as $value) {
                    $lcoptionStr .='<option value="' . $value->workshop_years . '" ' . ($value->workshop_years == date('Y') ? 'selected' : '') . '>'
                            . '' . $value->workshop_years . '</option>';
                }
            } else {
                $lcoptionStr = '<option value="' . date('Y') . '">' . date('Y') . '</option>';
            }
            $data['YearOption'] = $lcoptionStr;
            echo json_encode($data);
        }
    }
    public function ajax_traineewise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $trainee_id = $this->input->post('trainee_id', TRUE);
        
        $data['WtypeResult'] = $this->trainee_dashboard_model->getWorkshopType($company_id,$trainee_id);
        $data['RegionResult'] = $this->trainee_dashboard_model->getRegion($company_id,$trainee_id);

        echo json_encode($data);
    }
    public function ajax_wtypewise_data() {        
        $wrktype_id = $this->input->post('wrktype_id', TRUE);        
        $data['WsubtypeResult'] = $this->common_model->get_selected_values('workshopsubtype_mst','id,description as wsubtype','workshoptype_id='.$wrktype_id);        
        echo json_encode($data);
    }
    public function ajax_regionwise_data() {        
        $region_id = $this->input->post('region_id', TRUE);        
        $data['SubregionResult'] = $this->common_model->get_selected_values('workshopsubregion_mst','id,description as subregion','region_id='.$region_id);        
        echo json_encode($data);
    }
    public function load_quick_statistics() {
        $user_id = $this->input->post('user_id', TRUE);
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['login_type'] == 3) {
            $user_id = $this->mw_session['user_id'];
            $company_id = $this->mw_session['company_id'];
        } else {
                $company_id = $this->mw_session['company_id'];
            if ($company_id != "") {
                if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
                }
            } else {
                $company_id = $this->input->post('company_id', TRUE);
            }
        }
        $this->load->model('trainee_reports_model');
        $this->trainee_reports_model->SynchTraineeData($company_id);
        
        $wrktype_id = $this->input->post('wrktype_id', true);
        $wsubtype_id = $this->input->post('wsubtype_id', true);
        $flt_region_id = $this->input->post('flt_region_id', true);
        $subregion_id = $this->input->post('subregion_id', true);
        
        $total_response_time = 0;
        $total_wrong_ans = 0;
        $data['workshop_attended'] = $this->trainee_dashboard_model->workshop_attended($company_id, $user_id,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $data['Totaltopic_subtopic_answer'] = $this->trainee_dashboard_model->Totaltopic_subtopic_answer($company_id, $user_id,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $data['overall_PrePostAverage'] = $this->trainee_dashboard_model->overall_PrePostAverage($company_id, $user_id,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $data['overall_Response_time'] = $this->trainee_dashboard_model->overall_PrePostResponse_time($company_id, $user_id,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $data['total_wt_anser'] = $data['Totaltopic_subtopic_answer']->wrong_ans + $data['Totaltopic_subtopic_answer']->timeout;
        $wksh_top_five_array = $this->trainee_dashboard_model->top_five_workshop($company_id, $user_id,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

        $top_five_wksh_id = 0;
        $wksh_top_five_html = '';
        if (count((array)$wksh_top_five_array) > 0) {
            foreach ($wksh_top_five_array as $wksh_top) {
                $top_five_wksh_id .= $wksh_top->workshop_id . ",";

                $wksh_top_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $wksh_top->workshop_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . ($wksh_top->post_average !='NP' ? $wksh_top->post_average.'%':'NP') . '</span>
                                            </td>
                                        </tr>';
            }
            if ($top_five_wksh_id != '') {
                $top_five_wksh_id = rtrim($top_five_wksh_id,",");
            }
        } else {
            $wksh_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }

        $wksh_bottom_five_array = $this->trainee_dashboard_model->bottom_five_workshop($company_id, $user_id, $top_five_wksh_id,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $wksh_bottom_five_html = '';
        if (count((array)$wksh_bottom_five_array) > 0) {
            foreach ($wksh_bottom_five_array as $wksh_bottom) {
                $wksh_bottom_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $wksh_bottom->workshop_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . ($wksh_bottom->post_average !='NP' ? $wksh_bottom->post_average.'%':'NP') . '</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $wksh_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }

        $data['wksh_top_five_table'] = $wksh_top_five_html;
        $data['wksh_bottom_five_table'] = $wksh_bottom_five_html;

        echo json_encode($data);
    }

    public function load_trainee_index($firsttimeload = 1) {
        $data = array();
        $trainee_id = $this->input->post('user_id', TRUE);
        $wrktype_id = $this->input->post('wrktype_id', true);
        $wsubtype_id = $this->input->post('wsubtype_id', true);
        $flt_region_id = $this->input->post('flt_region_id', true);
        $subregion_id = $this->input->post('subregion_id', true);
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['login_type'] == 3) {
            $trainee_id = $this->mw_session['user_id'];
            $company_id = $this->mw_session['company_id'];
        } else {
                $company_id = $this->mw_session['company_id'];
            if ($company_id != "") {
                if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
                }
            } else {
                $company_id = $this->input->post('company_id', TRUE);
            }
        }
        $rpt_period = $this->input->post('rpt_period', true);
        //$wtype_id = $this->input->post('wtype_id', true);
        if ($wrktype_id != '0') {
            $WtypeData = $this->common_model->get_value('workshoptype_mst', 'workshop_type', 'id=' . $wrktype_id);
            $Wtype = 'Workshop Type :' . $WtypeData->workshop_type;
        } else {
            $Wtype = 'Workshop Type : All';
        }
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
        $graphtype_id = $this->input->post('graphtype_id', true);
        if ($rpt_period == "weekly") {
            $WeekStartDate = '';
            $WeekEndDate = '';
            if ($Week != '' && $Month != '' && $Year != '') {
                $WeekDate = explode('-', $Week);
                $WeekStartDay = $WeekDate[0];
                $WeekEndDay = $WeekDate[1];
                $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
                $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
                $StartStrDt = date('d-m-Y', strtotime($WeekStartDate));
                $EndStrDt = date('d-m-Y', strtotime($WeekEndDate));
            } else {
                $WeekStartDate = date('Y-m-d', strtotime("-6 days"));
                $WeekEndDate = $current_date;
                $StartStrDt = date('d-m-Y', strtotime($WeekStartDate));
                $EndStrDt = date('d-m-Y', strtotime($WeekEndDate));
                $WeekStartDay = date('d', strtotime("-6 days"));
                $WeekEndDay = date('d');
            }
            $report_title = 'Trainee Index - (Period From ' . $StartStrDt . ' To ' . $EndStrDt . '),' . $Wtype;
            $report_xaxis_title = 'Weekly';
            $PostArraySet = $this->trainee_dashboard_model->trainee_index_postaverage_weekly_monthly($company_id, $trainee_id,$WeekStartDate, $WeekEndDate, 1,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

            for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                if ($Year != '' && $Month != '') {
                    $TempDate = $Year . '-' . $Month . '-' . $i;
                } else {
                    $TempDate = Date('Y-m-' . $i);
                }
                if (isset($PostArraySet[$day])) {
                    $index_dataset[] = json_encode($PostArraySet[$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("l", strtotime($TempDate));
            }
        } elseif ($rpt_period == "monthly") {
            if ($Year != '' && $Month != '' && $Month != $current_month) {
                $StartDate = $Year . '-' . $Month . '-01';
                $noofdays = date('t', strtotime($StartDate));
                $EndDate = $Year . '-' . $Month . '-' . $noofdays;
                $StartStrDt = date('d-m-Y', strtotime($StartDate));
                $EndStrDt = date('d-m-Y', strtotime($EndDate));
            } elseif ($Year != '' && $Month == '') {
                $StartDate = Date($Year . '-m-01');
                $EndDate = $current_date;
                $diff = abs(strtotime($current_date) - strtotime($StartDate));
                $nyears = floor($diff / (365 * 60 * 60 * 24));
                $nmonths = floor(($diff - $nyears * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $noofdays = floor(($diff - $nyears * 365 * 60 * 60 * 24 - $nmonths * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $StartStrDt = date('d-m-Y', strtotime($StartDate));
                $EndStrDt = date('d-m-Y', strtotime($EndDate));
            } else {
                $StartDate = Date('Y-m-01');
                $EndDate = $current_date;
                $diff = abs(strtotime($current_date) - strtotime($StartDate));
                $nyears = floor($diff / (365 * 60 * 60 * 24));
                $nmonths = floor(($diff - $nyears * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $noofdays = floor(($diff - $nyears * 365 * 60 * 60 * 24 - $nmonths * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $StartStrDt = date('d-m-Y', strtotime($StartDate));
                $EndStrDt = date('d-m-Y', strtotime($EndDate));
                $WeekStartDate = '';
                $WeekEndDate = '';
            }
            $WeekStartDate = $StartDate;
            $WeekEndDate = $EndDate;
            $report_title = 'Trainee Index - (Period ' . $StartStrDt . ' To ' . $EndStrDt . '),' . $Wtype;

            $report_xaxis_title = 'Monthly';
            $PostArraySet = $this->trainee_dashboard_model->trainee_index_postaverage_weekly_monthly($company_id, $trainee_id,$StartDate, $EndDate, 2,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
            $WeekNo = 1;
            $PostAvg = 0;
            $Divider = 0;

            for ($i = 1; $i <= $noofdays; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                if ($Year != '' && $Month != '') {
                    $TempDate = $Year . '-' . $Month . '-' . $day;
                } else {
                    $TempDate = $Year . '-' . $current_month . '-' . $day;
                }
                if (isset($PostArraySet[$day])) {
                    $index_dataset[] = json_encode($PostArraySet[$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("d-M", strtotime($TempDate));
            }
//            for ($i = 1; $i <= $noofdays; $i++) {
//                $day = str_pad($i ,2,"0",STR_PAD_LEFT);
//                $date = $Year.'-'.$Month.'-'.$day;
//                $DateWeek = ceil( date( 'j', strtotime( $date ) ) / 7 ); 
//                if($WeekNo !=$DateWeek){
//                    if($Divider !=0){
//                        $TempAvg= number_format($PostAvg/$Divider,2);
//                    }else{
//                        $TempAvg= $PostAvg;
//                    }
//                    $index_dataset[]  = json_encode($TempAvg, JSON_NUMERIC_CHECK);
//                    $index_label[]   = "Week ".$WeekNo;
//                    $PostAvg=0;
//                    $WeekNo++;
//                    $Divider=1;
//                }else{
//                    $day = str_pad($i ,2,"0",STR_PAD_LEFT);
//                    if(isset($PostArraySet[$day])){
//                        $PostAvg  += $PostArraySet[$day];
//                        $Divider++;
//                    }
//                    if($noofdays==$i){
//                        if($Divider !=0){
//                            $TempAvg= number_format($PostAvg/$Divider,2);
//                        }else{
//                            $TempAvg= $PostAvg;
//                        }
//                        $index_dataset[]  = json_encode($TempAvg, JSON_NUMERIC_CHECK);
//                        $index_label[]   = "Week ".$WeekNo;
//                        $PostAvg=0;
//                    }
//                }
//                
//            }
        } elseif ($rpt_period == "yearly") {

            $StartDate = $Year . '-01-01';
            $EndDate = $Year . '-12-31';
            $StartStrDt = date('d-m-Y', strtotime($StartDate));
            $EndStrDt = date('d-m-Y', strtotime($EndDate));
            $WeekStartDate = $StartDate;
            $WeekEndDate = $EndDate;
            $report_title = 'Trainee Index - (Period: ' . $StartStrDt . ' To ' . $EndStrDt . '),' . $Wtype;
            $report_xaxis_title = 'Yearly';
            $PostArraySet = $this->trainee_dashboard_model->trainee_index_post_yearly($company_id, $trainee_id,$StartDate, $EndDate,$RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

            for ($i = 1; $i <= 12; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                if ($Year != '') {
                    $TempDate = $Year . '-' . $day . '-01';
                } else {
                    $TempDate = Date('Y-' . $day . '-01');
                }
                if (isset($PostArraySet[$i])) {
                    $index_dataset[] = json_encode($PostArraySet[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        }
        $data['report'] = $report_data;
        $Rdata['rpt_period'] = $rpt_period;
        $Rdata['graphtype_id'] = $graphtype_id;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $indexGraph = $this->load->view('trainee_dashboard/index_report', $Rdata, true);
        $data['index_graph'] = $indexGraph;
        $dataset = [];
        $label = [];
        if ($firsttimeload) {
            $WeekStartDate = "";
            $WeekEndDate = "";
        }   
//        } else {
//            $report_title = 'Trainee Histogram - (Period From ' . date("01-01-" . $Year) . ' To ' . date('d-m-Y') . '),' . $Wtype;
//        }
        $report_title = 'Trainee Histogram';
        $histogram_count = $this->trainee_dashboard_model->wksh_histogram_range($company_id, $trainee_id, $WeekStartDate = '', $WeekEndDate = '', $RightsFlag,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

        foreach ($histogram_count as $range) {
            $from_range = $range->from_range;
            $to_range = $range->to_range;
            $label[] = $from_range . "-" . $to_range.'%';
            $dataset[] = $range->WorkshopCount;
        }
        $HRdata['graphtype_id'] = $graphtype_id;
        $HRdata['report_title'] = json_encode($report_title, JSON_NUMERIC_CHECK);
        $HRdata['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
        $HRdata['label'] = json_encode($label);
        $lcHtml = $this->load->view('trainee_dashboard/show_report', $HRdata, true);
        $data['histogram'] = $lcHtml;
        echo json_encode($data);
    }

    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

}
