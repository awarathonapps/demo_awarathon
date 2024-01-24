<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Knowledge_assessment_dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('knowledge_assessment_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('knowledge_assessment_dashboard_model');
    }

    public function index() {
        $data['module_id'] = '92';
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
                $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                if (count((array)$Rowset) > 0) {
                    $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
            }
            if (!$RightsFlag) {
                $this->common_model->SyncTrainerRights($trainer_id);
            }
            if (!$WRightsFlag) {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
            }
            $data['TrainerSet'] = $this->common_model->getUserRightsList($data['company_id'], $RightsFlag);
            $data['wtype_array'] = $this->common_model->getWTypeRightsList($data['company_id'], $WRightsFlag);
            $data['RegionResult'] = $this->common_model->getUserRegionList($data['company_id'], $WRightsFlag);
        }
        $data['start_date'] = date('01-m-Y', strtotime(date('Y-m') . " -1 month"));
        $data['end_date'] = date("t-m-Y", strtotime($data['start_date']));
        $this->load->view('knowledge_assessment_dashboard/index', $data);
    }

    public function getdashboardData() {
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        } else {
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = ($this->input->post('Trainer_id', true) != '' ? $this->input->post('Trainer_id', true) : 0);
        $wrktype_id = ($this->input->post('wrktype_id', true) != '' ? $this->input->post('wrktype_id', TRUE) : 0);
        $wsubtype_id = $this->input->post('wsubtype_id', true);
        $flt_region_id = ($this->input->post('flt_region_id', true) != '' ? $this->input->post('flt_region_id', TRUE) : 0);
        $subregion_id = $this->input->post('subregion_id', true);

        $StartDate = $this->input->post('StartDate', TRUE);
        if ($StartDate == "") {
            $start_date = date('Y-m-01', strtotime(date('Y-m') . " -1 month"));
        } else {
            $start_date = date('Y-m-d', strtotime($StartDate));
        }
        $EndDate = $this->input->post('EndDate', TRUE);
        if ($EndDate == "") {
            $end_date = date("Y-m-t", strtotime($start_date));
        } else {
            $end_date = date('Y-m-d', strtotime($EndDate));
        }
        $SyncFlag = $this->knowledge_assessment_dashboard_model->requiredSyncData($Company_id);
        if ($SyncFlag) {
            //$this->knowledge_assessment_dashboard_model->SyncTrainerResult($Company_id);
            $this->knowledge_assessment_dashboard_model->LiveDataSync($Company_id);
            //$this->knowledge_assessment_dashboard_model->SyncWorshopResult($Company_id);
        }

        $data['HLAdata1'] = $this->knowledge_assessment_dashboard_model->get_HighestLowestAvgCE($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, '', '', $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
        if ($data['HLAdata1'] > 0) {
            $data['BestRegionq1'] = $this->knowledge_assessment_dashboard_model->get_BestRegion($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $data['HLAdata1']['MaxCE'], '', '', $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
            $WorstRegionq1 = $this->knowledge_assessment_dashboard_model->get_WorstRegion($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $data['HLAdata1']['MinCE'], '', '', $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
            if ($data['BestRegionq1'] == $WorstRegionq1) {
                $WorstRegionq1 = '-';
            }
            $data['WorstRegionq1'] = $WorstRegionq1;
        }
        $data['TotalWorkshop1'] = $this->knowledge_assessment_dashboard_model->get_TotalWorkshop($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, '', '', $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);


        $data['HLAdata2'] = $this->knowledge_assessment_dashboard_model->get_HighestLowestAvgCE($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $start_date, $end_date, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
        if ($data['HLAdata2'] > 0) {
            $data['BestRegionq2'] = $this->knowledge_assessment_dashboard_model->get_BestRegion($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $data['HLAdata2']['MaxCE'], $start_date, $end_date, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
            // echo $this->db->last_query();
            // exit;
            $WorstRegionq2 = $this->knowledge_assessment_dashboard_model->get_WorstRegion($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $data['HLAdata2']['MinCE'], $start_date, $end_date, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
            if ($data['BestRegionq2'] == $WorstRegionq2) {
                $WorstRegionq2 = '-';
            }
            $data['WorstRegionq2'] = $WorstRegionq2;
        }
        $data['TotalWorkshop2'] = $this->knowledge_assessment_dashboard_model->get_TotalWorkshop($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $start_date, $end_date, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
        $RegionWise = $this->knowledge_assessment_dashboard_model->get_RegionWisePerformance($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $start_date, $end_date, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);

        if (count((array)$RegionWise) > 0) {
            $Rdataset = array();
            $Rdataset1 = array();
            $Rlabel = array();
            foreach ($RegionWise as $value) {
                $Rdataset[] = $value->lifetime;
                $Rdataset1[] = $value->monthly;
                $Rlabel[] = $value->region_name;
            }
            $Rdata['dataset'] = json_encode($Rdataset, JSON_NUMERIC_CHECK);
            $Rdata['dataset1'] = json_encode($Rdataset1, JSON_NUMERIC_CHECK);
            $Rdata['label'] = json_encode($Rlabel);
            $Rdata['totallabel'] = count((array)$Rlabel);
            $data['region_graph'] = $this->load->view('knowledge_assessment_dashboard/region_report', $Rdata, true);
        } else {
            $data['region_graph'] = "";
        }
        $TypeWise = $this->knowledge_assessment_dashboard_model->get_WorkshoptypeWisePerformance($Company_id, $trainer_id, $RightsFlag, $WRightsFlag, $start_date, $end_date, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
        if (count((array)$TypeWise) > 0) {
            $Rdataset = array();
            $Rdataset1 = array();
            $Rlabel = array();
            foreach ($TypeWise as $value) {
                $Rdataset[] = $value->lifetime;
                $Rdataset1[] = $value->monthly;
                $Rlabel[] = $value->workshop_type;
            }
            $Rdata['dataset'] = json_encode($Rdataset, JSON_NUMERIC_CHECK);
            $Rdata['dataset1'] = json_encode($Rdataset1, JSON_NUMERIC_CHECK);
            $Rdata['label'] = json_encode($Rlabel);
            $Rdata['totallabel'] = count((array)$Rlabel);
            $data['type_graph'] = $this->load->view('knowledge_assessment_dashboard/workshoptype_report', $Rdata, true);
        } else {
            $data['type_graph'] = "";
        }
        echo json_encode($data);
    }

    public function load_supervisor_index($firsttimeload = 1) {
        $data = array();
        $company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($company_id == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            if (!$this->mw_session['superaccess']) {
                $Login_id = $trainer_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $rpt_period = $this->input->post('rpt_period', true);
        $trainer_id = $this->input->post('Trainer_id', true);
//        $wtype_id = $this->input->post('wtype_id', true);
//        $region_id = $this->input->post('region_id', true);
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $wrktype_id = ($this->input->post('wrktype_id', true) != '' ? $this->input->post('wrktype_id', TRUE) : 0);
        $wsubtype_id = $this->input->post('wsubtype_id', true);
        $flt_region_id = ($this->input->post('flt_region_id', true) != '' ? $this->input->post('flt_region_id', TRUE) : 0);
        $subregion_id = $this->input->post('subregion_id', true);
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
                $CEArraySet = $this->knowledge_assessment_dashboard_model->supervisor_index_weekly_monthly($company_id, $trainer_id, $RightsFlag, $WRightsFlag, $WeekStartDate, $WeekEndDate, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    if (isset($CEArraySet[$day])) {
                        $index_dataset[] = json_encode($CEArraySet[$day], JSON_NUMERIC_CHECK);
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
                $CEArraySet = $this->knowledge_assessment_dashboard_model->supervisor_index_weekly_monthly($company_id, $trainer_id, $RightsFlag, $WRightsFlag, $WeekStartDate, $WeekEndDate, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
                for ($i = $StartWeek; $i <= $EndWeek; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-m-' . $i);
                    if (isset($CEArraySet[$day])) {
                        $index_dataset[] = json_encode($CEArraySet[$day], JSON_NUMERIC_CHECK);
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
            $CEArraySet = $this->knowledge_assessment_dashboard_model->supervisor_index_weekly_monthly($company_id, $trainer_id, $RightsFlag, $WRightsFlag, $WeekStartDate, $WeekEndDate, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
            $WeekNo = 1;
            $CEAvg = 0;
            $Divider = 0;
            for ($i = 1; $i <= $noofdays; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = $Year . '-' . $Month . '-' . $day;
                if (isset($CEArraySet[$day])) {
                    $index_dataset[] = json_encode($CEArraySet[$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("d-M", strtotime($TempDate));
            }
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';

            $report_xaxis_title = 'Yearly';
            $CEArraySet = $this->knowledge_assessment_dashboard_model->supervisor_index_yearly($company_id, $trainer_id, $RightsFlag, $WRightsFlag, $WeekStartDate, $WeekEndDate, $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
            for ($i = 1; $i <= 12; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($CEArraySet[$i])) {
                    $index_dataset[] = json_encode($CEArraySet[$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        }
        $report_title = 'Supervisor Index - (Period From ' . date('d-m-Y', strtotime($WeekStartDate)) . ' To ' . date('d-m-Y', strtotime($WeekEndDate));
        if ($wrktype_id != "0") {
            $Rowset = $this->common_model->get_value('workshoptype_mst', 'workshop_type', 'id=' . $wrktype_id);
            $workshop_type = $Rowset->workshop_type;
        } else {
            $workshop_type = 'All';
        }
        if ($flt_region_id != "0") {
            $Rowset = $this->common_model->get_value('region', 'region_name', 'id=' . $flt_region_id);
            $region_name = $Rowset->region_name;
        } else {
            $region_name = 'All';
        }
        $report_title .=" Workshop Type : " . $workshop_type . ", Region : " . $region_name . ")";
        $data['report'] = $report_data;
        $Rdata['report_period'] = $rpt_period;
        $Rdata['graphtype_id'] = $graphtype_id;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $indexGraph = $this->load->view('knowledge_assessment_dashboard/index_report', $Rdata, true);
        $dataset = [];
        $label = [];
//        if($firsttimeload){
//            $WeekStartDate="";
//            $WeekEndDate="";
//            $Sreport_title    = 'Supervisor Histogram';
//        }else{
//            $Sreport_title    = 'Supervisor Histogram';
//        }
        $Sreport_title = "(Supervisor Histogram- Workshop Type : " . $workshop_type . ", Region : " . $region_name . ")";
        $histogram_count = $this->knowledge_assessment_dashboard_model->trainer_histogram_range($company_id, $trainer_id, $RightsFlag, $WRightsFlag, '', '', $wrktype_id, $wsubtype_id, $flt_region_id, $subregion_id);
        $StopFlag = false;
        if (count((array)$histogram_count) > 0) {
            foreach ($histogram_count as $range) {
                if ($range->TrainerCount != "") {
                    $StopFlag = true;
                }
                if ($StopFlag) {
                    $from_range = $range->from_range;
                    $to_range = $range->to_range;
                    if ($from_range < 0) {
                        $label[] = "(" . $from_range . "-" . $to_range . "%)";
                    } else {
                        $label[] = $from_range . "-" . $to_range . "%";
                    }
                    $dataset[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
                }
            }
            $HRdata['graphtype_id'] = $graphtype_id;
            //$HRdata['report_title'] = json_encode($Sreport_title, JSON_NUMERIC_CHECK);
            $HRdata['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $HRdata['label'] = json_encode($label);
            $lcHtml = $this->load->view('knowledge_assessment_dashboard/histogram_report', $HRdata, true);
            $data['histogram'] = $lcHtml;
        } else {
            $data['histogram'] = "";
        }
        $data['index_graph'] = $indexGraph;
        echo json_encode($data);
    }

    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['TrainerSet'] = $this->common_model->getUserRightsList($company_id, 1);
        $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id, 1);
        $data['RegionResult'] = $this->common_model->getUserRegionList($company_id, 1);

        echo json_encode($data);
    }

    public function StoreWorkshopResult() {
        $CompanySet = $this->common_model->get_selected_values('atom_results', 'DISTINCT company_id', '1=1');
        $this->common_model->TruncateTable('workshop_result');
        $this->common_model->TruncateTable('trainer_result');
        foreach ($CompanySet as $value) {
            $this->knowledge_assessment_dashboard_model->SyncTrainerResult($value->company_id);
            $this->knowledge_assessment_dashboard_model->SyncWorshopResult($value->company_id);
        }
    }

    public function ajax_trainerwise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $trainer_id = $this->input->post('trainer_id', TRUE);

        $data['WtypeResult'] = $this->knowledge_assessment_dashboard_model->getWorkshopType($company_id, $trainer_id);
        $data['RegionResult'] = $this->knowledge_assessment_dashboard_model->getRegion($company_id, $trainer_id);

        echo json_encode($data);
    }

    public function ajax_wtypewise_data() {
        $wrktype_id = $this->input->post('wrktype_id', TRUE);
        $data['WsubtypeResult'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description as wsubtype', 'workshoptype_id=' . $wrktype_id);
        echo json_encode($data);
    }

    public function ajax_regionwise_data() {
        $region_id = $this->input->post('region_id', TRUE);
        $data['SubregionResult'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description as subregion', 'region_id=' . $region_id);
        echo json_encode($data);
    }

}
