<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Weights_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('weights_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('weights_report_model');
        }

    public function index() {
        $data['module_id'] = '16.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if (!$WRightsFlag) {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
            }
            $data['WorkshopResult'] = $this->common_model->getWkshopFeedRightsList($data['company_id'], '', $WRightsFlag);
            $data['WtypeResult'] = $this->common_model->getWTypeFeedRightsList($data['company_id'], $WRightsFlag);
            $data['TraineeResult'] = $this->common_model->getUserTraineeList($data['company_id'], $WRightsFlag);
            $data['RegionResult'] = $this->common_model->getUserRegionList($data['company_id'],$WRightsFlag);
            $data['TraineeRegionData'] = $this->weights_report_model->get_TraineeRegionData($data['company_id']);
        }
        $this->load->view('weights_report/index', $data);
    }

    public function getdashboardData() {
        $feedback_top_five_html = '';
        $feedback_bottom_five_html = '';
        $top_five_wksh_id = 0;
        $wksh_top_five_html = '';
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['login_type'] == 3) {
            $user_id = $this->mw_session['user_id'];
            $Company_id = $this->mw_session['company_id'];
        } else {
            if ($this->mw_session['company_id'] != "") {
                $Company_id = $this->mw_session['company_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
            } else {
                $Company_id = $this->input->post('company_id', TRUE);
            }
        }
        $data['WrkattendedParticipated'] = $this->weights_report_model->get_WrkattendedParticipated($Company_id, $WRightsFlag);
        
        $data['avgScore'] = $this->weights_report_model->get_avgScore($Company_id, $WRightsFlag);
        $data['bestWorkshop'] = $this->weights_report_model->get_bestWorkshop($Company_id, $WRightsFlag);
        $data['worstWorkshop'] = $this->weights_report_model->get_worstWorkshop($Company_id, $WRightsFlag);

        $feedback_top_five_array = $this->weights_report_model->get_topFiveWorkshop($Company_id, $WRightsFlag);
        if (count((array)$feedback_top_five_array) > 0) {
            foreach ($feedback_top_five_array as $feedback_top) {
                $top_five_wksh_id .= $feedback_top->workshop_id . ",";
                $feedback_top_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $feedback_top->workshop_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . $feedback_top->wavg_score . '%</span>
                                            </td>
                                        </tr>';
            }
            if ($top_five_wksh_id != '') {
                $top_five_wksh_id = substr($top_five_wksh_id, 0, strlen($top_five_wksh_id) - 1);
            }
        } else {
            $feedback_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }

        $feedback_bottom_five_array = $this->weights_report_model->get_bottomFiveWorkshop($Company_id, $top_five_wksh_id, $WRightsFlag);
        if (count((array)$feedback_bottom_five_array) > 0) {
            foreach ($feedback_bottom_five_array as $feedback_bottom) {
                $feedback_bottom_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $feedback_bottom->workshop_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . $feedback_bottom->wavg_score . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $feedback_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        $data['feedback_top_five_table'] = $feedback_top_five_html;
        $data['feedback_bottom_five_table'] = $feedback_bottom_five_html;
        echo json_encode($data);
    }

    public function load_weightsReportIndex($firsttimeload = 1) {
        $data = array();
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        $company_id = $this->mw_session['company_id'];
        if ($company_id == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $rpt_period = $this->input->post('rpt_period', true);
        $wtype_id = $this->input->post('wtype_id', true);
		
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
                $AvgScoreArraySet = $this->weights_report_model->feedbackWeightsIndexWeeklyMonthly($company_id, $wtype_id, $WeekStartDate, $WeekEndDate, $WRightsFlag);
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    if (isset($AvgScoreArraySet[$day])) {
                        $index_dataset[] = json_encode($AvgScoreArraySet[$day], JSON_NUMERIC_CHECK);
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
                $AvgScoreArraySet = $this->weights_report_model->feedbackWeightsIndexWeeklyMonthly($company_id, $wtype_id, $WeekStartDate, $WeekEndDate, $WRightsFlag);
                for ($i = $StartWeek; $i <= $EndWeek; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-m-' . $i);
                    if (isset($AvgScoreArraySet[$day])) {
                        $index_dataset[] = json_encode($AvgScoreArraySet[$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
                }
            }
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
                $diff = abs(strtotime($WeekEndDate) - strtotime($WeekStartDate));
                $nyears = floor($diff / (365 * 60 * 60 * 24));
                $nmonths = floor(($diff - $nyears * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $noofdays = floor(($diff - $nyears * 365 * 60 * 60 * 24 - $nmonths * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            }

            $report_xaxis_title = 'Monthly';
            $AvgScoreArraySet = $this->weights_report_model->feedbackWeightsIndexWeeklyMonthly($company_id, $wtype_id, $WeekStartDate, $WeekEndDate, $WRightsFlag);

            $WeekNo = 1;
            $CEAvg = 0;
            $Divider = 0;
            for ($i = 1; $i <= $noofdays; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = $Year . '-' . $Month . '-' . $day;
                if (isset($AvgScoreArraySet[$day])) {
                    $index_dataset[] = json_encode($AvgScoreArraySet[$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("d-M", strtotime($TempDate));
            }
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';

            $report_xaxis_title = 'Yearly';
            $AvgScoreArraySet = $this->weights_report_model->feedbackWeightsIndexYearly($company_id, $wtype_id, $WeekStartDate, $WeekEndDate, $WRightsFlag);
            for ($i = 1; $i <= 12; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($AvgScoreArraySet[$i])) {
                    $index_dataset[] = json_encode($AvgScoreArraySet[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        }
        $report_title = 'Feedback Weights Index - (Period From ' . date('d-m-Y', strtotime($WeekStartDate)) . ' To ' . date('d-m-Y', strtotime($WeekEndDate));
        if ($wtype_id != "0") {
            $Rowset = $this->common_model->get_value('workshoptype_mst', 'workshop_type', 'id=' . $wtype_id);
            $workshop_type = $Rowset->workshop_type;
        } else {
            $workshop_type = 'All';
        }
        $report_title .=" Workshop Type : " . $workshop_type . ")";
        $data['report'] = $report_data;
        $Rdata['report_period'] = $rpt_period;
        $Rdata['graphtype_id'] = $graphtype_id;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);

        $indexGraph = $this->load->view('weights_report/index_report', $Rdata, true);
        $dataset = [];
        $label = [];
        $Sreport_title = "(Feedback Weights Histogram- Workshop Type : " . $workshop_type . ")";
        $histogram_count = $this->weights_report_model->feedbackWeightsHistogram_range($company_id, '', '', $wtype_id, $WRightsFlag);
        $StopFlag = false;
        if (count((array)$histogram_count) > 0) {
            foreach ($histogram_count as $range) {
                $from_range = $range->from_range;
                    $to_range = $range->to_range;
                    if ($from_range < 0) {
                        $label[] = "(" . $from_range . "-" . $to_range . ")";
                    } else {
                        $label[] = $from_range . "-" . $to_range.'%';
                    }
                    $dataset[] = ($range->workshop_count > 0 ? $range->workshop_count : 0);
            }
            $HRdata['graphtype_id'] = $graphtype_id;
            $HRdata['report_title'] = json_encode($Sreport_title, JSON_NUMERIC_CHECK);
            $HRdata['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $HRdata['label'] = json_encode($label);
            $lcHtml = $this->load->view('weights_report/histogram_report', $HRdata, true);
            $data['histogram'] = $lcHtml;
        } else {
            $data['histogram'] = "";
        }
        $data['index_graph'] = $indexGraph;
        echo json_encode($data);
    }

    public function getWeightWorkshopTableData() {
        $dtSearchColumns = array('w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] != "") {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        } else {
            $company_id = ($this->input->get('wtab_company_id') ? $this->input->get('wtab_company_id') : '');
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE af.company_id  = " . $company_id;
            }
        }

        $wtab_wtype_id = ($this->input->get('wtab_wtype_id') ? $this->input->get('wtab_wtype_id') : '0');
        if ($wtab_wtype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $wtab_wtype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type  = " . $wtab_wtype_id;
            }
        }
        $wtab_workshop_id = ($this->input->get('wtab_workshop_id') ? $this->input->get('wtab_workshop_id') : '');
        if ($wtab_workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.workshop_id  = " . $wtab_workshop_id;
            } else {
                $dtWhere .= " WHERE af.workshop_id  = " . $wtab_workshop_id;
            }
        }
        $wtab_workshop_subtype = ($this->input->get('wtab_workshop_subtype') ? $this->input->get('wtab_workshop_subtype') : '');
        if ($wtab_workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubtype_id  = " . $wtab_workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id  = " . $wtab_workshop_subtype;
            }
        }
        $wtab_region_id = ($this->input->get('wtab_region_id') ? $this->input->get('wtab_region_id') : '0');
        if ($wtab_region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $wtab_region_id;
            } else {
                $dtWhere .= " WHERE w.region  = " . $wtab_region_id;
            }
        }
        $wtab_subregion_id = ($this->input->get('wtab_subregion_id') ? $this->input->get('wtab_subregion_id') : '');
        if ($wtab_subregion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubregion_id  = " . $wtab_subregion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id  = " . $wtab_subregion_id;
            }
        }
        $DTRenderArray = $this->weights_report_model->getWeightWorkshopTableData($company_id, $dtLimit, $dtWhere, $WRightsFlag);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('workshop_name', 'total_score', 'no_of_trainee');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == 'workshop_name') {
                        $action = '<a href="' . base_url() . 'weights_report/showWorkshopFeedback/' . base64_encode($dtRow["company_id"]) . '/' . base64_encode($dtRow["workshop_id"]) . '" data-target="#LoadModalFilter" data-toggle="modal">' . $dtRow[$dtDisplayColumns[$i]] . '</a>';
                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] == 'total_score') {
                        $row[] = $dtRow["total_score"] . "%";
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }

    public function getWeightIndTableData() {
        $dtSearchColumns = array('du.firstname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['login_type'] == 3) {
            $user_id = $this->mw_session['user_id'];
            $company_id = $this->mw_session['company_id'];
        } else {
            if ($this->mw_session['company_id'] != "") {
                $company_id = $this->mw_session['company_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
            } else {
                $company_id = ($this->input->get('indtab_company_id') ? $this->input->get('indtab_company_id') : '');
            }
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE af.company_id  = " . $company_id;
            }
        }

        $indtab_wtype_id = ($this->input->get('ind_wtype_id') ? $this->input->get('ind_wtype_id') : '0');
        if ($indtab_wtype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $indtab_wtype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type  = " . $indtab_wtype_id;
            }
        }
        $wtab_workshop_id = ($this->input->get('ind_workshop_id') ? $this->input->get('ind_workshop_id') : '');
        if ($wtab_workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.workshop_id  = " . $wtab_workshop_id;
            } else {
                $dtWhere .= " WHERE af.workshop_id  = " . $wtab_workshop_id;
            }
        }
        $ind_trainee_id = ($this->input->get('ind_trainee_id') ? $this->input->get('ind_trainee_id') : '0');
        if ($ind_trainee_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.user_id  = " . $ind_trainee_id;
            } else {
                $dtWhere .= " WHERE af.user_id  = " . $ind_trainee_id;
            }
        }
        $ind_workshop_subtype = ($this->input->get('ind_workshop_subtype') ? $this->input->get('ind_workshop_subtype') : '');
        if ($ind_workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubtype_id  = " . $ind_workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id  = " . $ind_workshop_subtype;
            }
        }
        $ind_region_id = ($this->input->get('ind_region_id') ? $this->input->get('ind_region_id') : '0');
        if ($ind_region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $ind_region_id;
            } else {
                $dtWhere .= " WHERE w.region  = " . $ind_region_id;
            }
        }
        $ind_subregion_id = ($this->input->get('ind_subregion_id') ? $this->input->get('ind_subregion_id') : '');
        if ($ind_subregion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubregion_id  = " . $ind_subregion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id  = " . $ind_subregion_id;
            }
        }
        $ind_tregion_id = (isset($_GET['ind_tregion_id']) ? $_GET['ind_tregion_id'] : '');
        if ($ind_tregion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $ind_tregion_id;
            } else {
                $dtWhere .= " WHERE du.region_id  = " . $ind_tregion_id;
            }
        }
        $DTRenderArray = $this->weights_report_model->getWeightIndTableData($company_id, $dtLimit, $dtWhere, $WRightsFlag);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainee_name', 'score_avg');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    $action = '';
                    if ($dtDisplayColumns[$i] == 'trainee_name') {
                        $action = '<a href="' . base_url() . 'weights_report/showFeedbackDetails/' . base64_encode($dtRow["company_id"]) . '/' . base64_encode($dtRow["user_id"]) . '/' . base64_encode($dtRow["workshop_id"]) . '" data-target="#LoadModalFilter" data-toggle="modal">' . $dtRow[$dtDisplayColumns[$i]] . '</a>';
                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] == 'score_avg') {
                        $row[] = $dtRow["score_avg"] . "%";
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }

    public function getQuestionScoreData() {
        $dtSearchColumns = array('ft.description', 'fst.description');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('fcmp_id') ? $this->input->get('fcmp_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND af.company_id  = " . $company_id;
                } else {
                    $dtWhere .= " WHERE af.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE af.company_id  = " . $this->mw_session['company_id'];
            }
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $feedbackset_id = ($this->input->get('feedbackset_id') ? $this->input->get('feedbackset_id') : '');
        if ($feedbackset_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.feedbackset_id  = " . $feedbackset_id;
            } else {
                $dtWhere .= " WHERE af.feedbackset_id  = " . $feedbackset_id;
            }
        }
        $ftrainee_id = ($this->input->get('ftrainee_id') ? $this->input->get('ftrainee_id') : '');
        if ($ftrainee_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.user_id  = " . $ftrainee_id;
            } else {
                $dtWhere .= " WHERE af.user_id  = " . $ftrainee_id;
            }
        }
        $fworkshop_id = ($this->input->get('fworkshop_id') ? $this->input->get('fworkshop_id') : '');
        if ($fworkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.workshop_id  = " . $fworkshop_id;
            } else {
                $dtWhere .= " WHERE af.workshop_id  = " . $fworkshop_id;
            }
        }
        $DTRenderArray = $this->weights_report_model->getFeedbackQueData($dtOrder, $dtLimit, $dtWhere, $WRightsFlag);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('type_name', 'subtype_name', 'question_title', 'score_avg');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == 'score_avg') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]] . '%';
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }

    public function showFeedbackDetails($company_id, $trainee_id, $workshop_id) {
        $company_id = base64_decode($company_id);
        $trainee_id = base64_decode($trainee_id);
        $workshop_id = base64_decode($workshop_id);
        $data['Company_id'] = $company_id;
        $data['Trainee_id'] = $trainee_id;
        $data['Workshop_id'] = $workshop_id;
        $data['FeedbackSet'] = $this->weights_report_model->getFeedbackData($company_id, $trainee_id, $workshop_id);
        $this->load->view('weights_report/FeedbackFilterModal', $data);
    }

    public function showWorkshopFeedback($company_id, $workshop_id) {
        $company_id = base64_decode($company_id);
        $workshop_id = base64_decode($workshop_id);
        $data['Company_id'] = $company_id;
        $data['Workshop_id'] = $workshop_id;
        $data['Trainee_id'] = '';
        $data['FeedbackSet'] = $this->weights_report_model->getWorkshopFeedbackData($company_id, $workshop_id);
        $this->load->view('weights_report/FeedbackFilterModal', $data);
    }

    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }    
    public function ajax_ComparisonData() {
        $CompTable = '';
        $error = '';
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }

        $workshop_id = $this->input->post('workshop_id', TRUE);
        $wtype_id = $this->input->post('wtype_id', TRUE);
        $RowCount = $this->input->post('RowCount', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        
        $workshop_subtype = $this->input->post('cmptab_workshop_subtype', TRUE);
        $region_id = $this->input->post('cmptab_region_id', TRUE);
        $subregion_id = $this->input->post('cmptab_subregion_id', TRUE);

        $cmptab_tregion_id = $this->input->post('cmptab_tregion_id', TRUE);

        if ($company_id != '' && $workshop_id != '') {
            $FeedbackQueData = $this->weights_report_model->getComparisonFeedbcakQueData($company_id, $workshop_id, $wtype_id, $trainee_id, $WRightsFlag,$workshop_subtype,$region_id,$subregion_id,$cmptab_tregion_id);
            $FeedbackOverallAVG = $this->weights_report_model->getComparisonFeedbcakOverallAvg($company_id, $workshop_id, $wtype_id, $trainee_id, $WRightsFlag,$workshop_subtype,$region_id,$subregion_id,$cmptab_tregion_id);
            if ($trainee_id != '0') {
                $HeadingData = $this->common_model->get_value('device_users', 'concat(firstname," ",lastname," (",email," )") as traineename', 'user_id=' . $trainee_id);
                $labelData = "<b>Trainee :</b>" . $HeadingData->traineename;
            } else {
                $HeadingData = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
                $labelData = "<b>Workshop :</b>" . $HeadingData->workshop_name;
            }
            $CompTable .= '<div class="col-md-6 divstyle" id="childdiv_' . $RowCount . '">
                                <table class="table table-hover table_style" id="Comptable" width="30%">
                                <thead>
                                    <tr ><td colspan="5" style="text-align: center;">' . $labelData . '<div class="ScrollStyle"><b>Overall Score(Avg) : </b>' . $FeedbackOverallAVG->overall_score_avg . ' % <button id=button-filter  style="float: right;" class="btn btn-sm btn-small"  type="button" onclick="RemoveChart(' . $RowCount . ');">X</button></div></td></tr>    
                                <tr class="uppercase" style="background-color: #e6f2ff;">                                            
                                    <th>Type</th>                        
                                    <th>Sub-Type</th>
                                    <th>Questions</th>
                                    <th>Score</th>                                    
                                </tr></thead><tbody>';

            if (count((array)$FeedbackQueData) > 0) {
                foreach ($FeedbackQueData as $value) {
                    $CompTable .='<tr class="datatr">
                                <td>' . $value->type_name . '</td>
                                <td>' . $value->subtype_name . '</td>
                                <td>' . $value->question_title . '</td>
                                <td>' . $value->score_avg . '%</td>                                                        
                                </tr>';
                }
            } else {
                $CompTable .='<tr class="datatr"><td colspan="4">No Data found...</td></tr>';
            }
            $CompTable .='</tbody></table></div>';
        } else {
            $error = "Please Select Company,Workshop";
        }
        $data['CompTable'] = $CompTable;
        $data['Error'] = $error;

        echo json_encode($data);
    }

    public function ajax_FeedbackOverallScore() {
        $error = '';
        $overallScore = '';
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $feedbackset_id = $this->input->post('feedbackset_id', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        if ($feedbackset_id != '') {
            $FeedbackOverallScore = $this->weights_report_model->getWorkshopFeedbackOverallScore($company_id, $workshop_id, $feedbackset_id, $trainee_id, $WRightsFlag);
            if (count((array)$FeedbackOverallScore) > 0) {
                $overallScore = $FeedbackOverallScore->total_score;
            }
        } else {
            $error = "Please Select Feedback Set";
        }

        $data['Error'] = $error;
        $data['OverallScore'] = $overallScore;
        echo json_encode($data);
    }

}
