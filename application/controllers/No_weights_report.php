<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class No_weights_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('No_weights_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('no_weights_report_model');
        }

    public function index() {
        $data['module_id'] = '16.2';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
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
            $data['TraineeRegionData'] = $this->no_weights_report_model->get_TraineeRegionData($data['company_id']);
        }
        $this->load->view('no_weights_report/index', $data);
    }    
    public function showNoWeightWorkshopFeedback($dcompany_id, $dworkshop_id) {
        $company_id = base64_decode($dcompany_id);
        $workshop_id = base64_decode($dworkshop_id);
        $data['Company_id'] = $company_id;
        $data['Workshop_id'] = $workshop_id;
        $data['Trainee_id'] = '';
        $data['FeedbackSet'] = $this->no_weights_report_model->getWorkshopFeedbackData($company_id, $workshop_id);
        $this->load->view('no_weights_report/FeedbackFilterModal', $data);
    }

    public function showNoWeightIndQA($dcompany_id = '', $dworkshop_id = '', $dtrainee_id = '') {
        $company_id = base64_decode($dcompany_id);
        $workshop_id = base64_decode($dworkshop_id);
        $trainee_id = base64_decode($dtrainee_id);
        $data['Company_id'] = $company_id;
        $data['Trainee_id'] = $trainee_id;
        $data['Workshop_id'] = $workshop_id;
        $data['Trainee'] = $this->common_model->get_value('device_users', 'concat(firstname," ",lastname) as traineename', 'user_id=' . $trainee_id);
        $data['FeedbackSet'] = $this->no_weights_report_model->getFeedbackData($company_id, $workshop_id, $trainee_id);
        $this->load->view('no_weights_report/FeedbackQAFilterModal', $data);
    }

    public function getNoWeightWorkshopTableData() {
        $dtSearchColumns = array('w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] != "") {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
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
        if (!$WRightsFlag) {
            $dtWhere .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id ) ";
        }
        $DTRenderArray = $this->no_weights_report_model->getNoWeightWorkshopTableData($company_id, $dtLimit, $dtWhere);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('workshop_name', 'no_of_trainee');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == 'workshop_name') {
                        $action = '<a href="' . base_url() . 'no_weights_report/showNoWeightWorkshopFeedback/' . base64_encode($dtRow["company_id"]) . '/' . base64_encode($dtRow["workshop_id"]) . '" data-target="#LoadModalFilter" data-toggle="modal">' . $dtRow[$dtDisplayColumns[$i]] . '</a>';
                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }

    public function getNoWeightIndTableData() {
        $dtSearchColumns = array('w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        if ($this->mw_session['login_type'] == 3) {
            $user_id = $this->mw_session['user_id'];
            $company_id = $this->mw_session['company_id'];
        } else {
            if ($this->mw_session['company_id'] != "") {
                $company_id = $this->mw_session['company_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
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

        $ind_wtype_id = ($this->input->get('ind_wtype_id') ? $this->input->get('ind_wtype_id') : '0');
        if ($ind_wtype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $ind_wtype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type  = " . $ind_wtype_id;
            }
        }
        $ind_workshop_id = ($this->input->get('ind_workshop_id') ? $this->input->get('ind_workshop_id') : '');
        if ($ind_workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.workshop_id  = " . $ind_workshop_id;
            } else {
                $dtWhere .= " WHERE af.workshop_id  = " . $ind_workshop_id;
            }
        }
        $ind_trainee_id = ($this->input->get('ind_trainee_id') ? $this->input->get('ind_trainee_id') : '');
        if ($ind_trainee_id != "") {
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
        if (!$WRightsFlag) {
            $dtWhere .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id ) ";
        }
        $DTRenderArray = $this->no_weights_report_model->getNoWeightIndTraineeData($company_id, $dtLimit, $dtWhere);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainee_name', 'no_of_question_atmpt');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == 'trainee_name') {
                        $action = '<a href="' . base_url() . 'no_weights_report/showNoWeightIndQA/' . base64_encode($dtRow["company_id"]) . '/' . base64_encode($dtRow["workshop_id"]) . '/' . base64_encode($dtRow["user_id"]) . '" data-target="#LoadModalFilter" data-toggle="modal">' . $dtRow[$dtDisplayColumns[$i]] . '</a>';
                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }

    public function ajax_NoWeightWorkshopDataChart($TotalChart) {
        $ChartHTML = '';
        $lcHtml = '';
        $error = '';
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $feedbackset_id = $this->input->post('feedbackset_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $RowCount = $this->input->post('RowCount', TRUE);
        $graphtype_id = $this->input->post('graphtype_id', TRUE);
        if ($feedbackset_id != '') {            
            $ChartData = $this->no_weights_report_model->getWorkshopDataChartValue($company_id, $workshop_id, $feedbackset_id, $WRightsFlag, $login_id);                            
            if (count((array)$ChartData) > 0) {                
                foreach ($ChartData as $value) {                    
                    $Question = $this->common_model->get_value('workshop_feedback_questions', 'question_title', 'question_id=' . $value->feedback_id);
                    $question = str_replace("'", "\'", $Question->question_title);                    
                    $data ['Question_title'] = json_encode($question, JSON_NUMERIC_CHECK);
                    $data ['no_of_users']    = json_encode($value->total_users, JSON_NUMERIC_CHECK);
                    $data ['option_a_title'] = json_encode($value->option_a, JSON_NUMERIC_CHECK);
                    $data ['option_b_title'] = json_encode($value->option_b, JSON_NUMERIC_CHECK);
                    $data ['option_c_title'] = json_encode($value->option_c, JSON_NUMERIC_CHECK);
                    $data ['option_d_title'] = json_encode($value->option_d, JSON_NUMERIC_CHECK);
                    $data ['option_e_title'] = json_encode($value->option_e, JSON_NUMERIC_CHECK);
                    $data ['option_f_title'] = json_encode($value->option_f, JSON_NUMERIC_CHECK);
                    $data ['option_a'] = json_encode($value->opt_a_selected, JSON_NUMERIC_CHECK);
                    $data ['option_b'] = json_encode($value->opt_b_selected, JSON_NUMERIC_CHECK);
                    $data ['option_c'] = json_encode($value->opt_c_selected, JSON_NUMERIC_CHECK);
                    $data ['option_d'] = json_encode($value->opt_d_selected, JSON_NUMERIC_CHECK);
                    $data ['option_e'] = json_encode($value->opt_e_selected, JSON_NUMERIC_CHECK);
                    $data ['option_f'] = json_encode($value->opt_f_selected, JSON_NUMERIC_CHECK);
                    $data ['TotalChart'] = $TotalChart;
                    
                    $data ['graphtype_id'] = $graphtype_id;
                    $ar = array();
                    $ar_val = array();
                    $ar_ofuser = array();
                    $data ['selected_user_a'] = json_encode($value->option_a_selected, JSON_NUMERIC_CHECK);
                    $data ['selected_user_b'] = json_encode($value->option_b_selected, JSON_NUMERIC_CHECK);
                    $data ['selected_user_c'] = json_encode($value->option_c_selected, JSON_NUMERIC_CHECK);
                    $data ['selected_user_d'] = json_encode($value->option_d_selected, JSON_NUMERIC_CHECK);
                    $data ['selected_user_e'] = json_encode($value->option_e_selected, JSON_NUMERIC_CHECK);
                    $data ['selected_user_f'] = json_encode($value->option_f_selected, JSON_NUMERIC_CHECK);
                    $ar_ofuser[]= $value->option_a_selected;
                    $ar_ofuser[]= $value->option_b_selected;                                        
                    $ar[] = $value->option_a;
                    $ar[] = $value->option_b;                    
                    if ($value->option_c != '') {
                        $ar[] = $value->option_c;
                        $ar_ofuser[]= $value->option_c_selected;
                    }
                    if ($value->option_d != '') {
                        $ar[] = $value->option_d;
                        $ar_ofuser[]= $value->option_d_selected;
                    }
                    if ($value->option_e != '') {
                        $ar[] = $value->option_e;
                        $ar_ofuser[]= $value->option_e_selected;
                    }
                    if ($value->option_f != '') {
                        $ar[] = $value->option_f;
                        $ar_ofuser[]= $value->option_f_selected;
                    }
                    $data ['TotalUsers'] = $ar_ofuser; 
                    $data ['label']     = json_encode($ar, JSON_NUMERIC_CHECK);                    
                    $data ['ar_ofuser'] = json_encode($ar_ofuser, JSON_NUMERIC_CHECK);
                    $lcHtml .= $this->load->view('no_weights_report/show_report', $data, true);
                    $TotalChart++;
                }
            }
        } else {
            $error = "Please Select Feedabck..!";
        }
        $Rdata['HtmlData'] = $lcHtml;
        $Rdata['Error'] = $error;
        echo json_encode($Rdata);
    }
    public function getQuestionAnsData() {
        $dtSearchColumns = array('wfq.question_title');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('fcmp_id') ? $this->input->get('fcmp_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE af.company_id  = " . $company_id;
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
        if (!$WRightsFlag) {
            $dtWhere .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id ) ";
        }
        $DTRenderArray = $this->no_weights_report_model->getFeedbackQueAns($dtOrder, $dtLimit, $dtWhere);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('question_title','option_a_title','option_b_title','option_c_title','option_d_title','option_e_title','option_f_title','trainee_answer');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    $ans = '';
                    if ($dtDisplayColumns[$i] == 'trainee_answer') {
                        for ($j = 'a'; $j <= 'f'; $j++) {
                            if ($dtRow['option_' . $j] == 1) {
                                $ans .= $dtRow['option_' . $j . '_title'] . "/";
                            }
                        }
                        $ans = rtrim($ans, "/");
                        if ($ans != '') {
                            $row[] = $ans;
                        } else {
                            $row[] = "Not Answered";
                        }
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        }

        echo json_encode($output);
    }

    public function ajax_ComparisonData() {
        $CompTable = '';
        $error = '';
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
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
        
        $feedbackset_id = $this->input->post('feedbackset_id', TRUE);
        if ($company_id != '' && $workshop_id != '' && $trainee_id != '') {
            $FeedbackSet = $this->no_weights_report_model->getFeedbackData($company_id, $workshop_id, $trainee_id);
            if ($trainee_id != '') {
                $HeadingData = $this->common_model->get_value('device_users', 'concat(firstname," ",lastname) as traineename', 'user_id=' . $trainee_id);
                $labelData = "Trainee :" . $HeadingData->traineename;
            } else {
                $HeadingData = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
                $labelData = "Workshop :" . $HeadingData->workshop_name;
            }
            $CompTable .= '<div class="col-md-6 divstyle" id="childdiv_' . $RowCount . '">
                                <table class="table table-hover table-light table_style" id="Comptable" width="30%">
                                <thead >';
//                                    <tr ><td colspan="2" style="text-align: center;">
//                                        <select id="cmp_feedback" name="cmp_feedback" class="form-control input-sm select2me" onchange="NoWeightCmpTab_data()">
//                                            <option>Please Select</option>';
//                                            if(count((array)$FeedbackSet) > 0){
//                                                foreach ($FeedbackSet as $val){
//                                                    $CompTable .= '<option value="'.$val->feedbackset_id.'">'.$val->feedback_name.'</option>';
//                                                }         
//                                            } 
//                                    $CompTable .= '</select></td></tr>';
            $CompTable .= '<tr ><td colspan="2" style="text-align: center;">' . $labelData . '<div class="ScrollStyle" style="text-align: right;"><button id=button-filter  class="btn btn-sm btn-small"  type="button" onclick="RemoveChart(' . $RowCount . ');">X</button></div></td></tr>    
                                                    <tr class="uppercase" style="background-color: #e6f2ff;">                                            
                                                        <th>Question</th>                        
                                                        <th>Trainee Answered</th>                                                                      
                                                    </tr></thead><tbody>';
            $FeedbackQueAns = $this->no_weights_report_model->getComparisonFeedbcakQueData($company_id, $workshop_id, $wtype_id, $trainee_id, $feedbackset_id, $WRightsFlag, $login_id,$workshop_subtype,$region_id,$subregion_id,$cmptab_tregion_id);
            if (count((array)$FeedbackQueAns) > 0) {
                foreach ($FeedbackQueAns as $value) {
                    $ans = '';
                    $CompTable .='<tr class="datatr">
                            <td>' . $value->question_title . '</td>';
                    for ($j = 'a'; $j <= 'f'; $j++) {
                        $opt = 'option_' . $j;
                        if ($value->$opt == 1) {
                            $obj = 'option_' . $j . '_title';
                            $ans .= $value->$obj . "/";
                        }
                    }
                    $answer = rtrim($ans, "/");
                    if ($answer != '') {
                        $CompTable .='<td>' . $answer . '</td>                                                                                        
                                            </tr>';
                    } else {
                        $CompTable .='<td>Not Answered</td>                                                                                        
                                            </tr>';
                    }
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

    public function ajax_NoWeightTraineeOptionChart() {
        $ChartHTML = '';
        $lcHtml = '';
        $error = '';
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $feedbackset_id = $this->input->post('feedbackset_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);

        if ($feedbackset_id != '') {
            $ChartData = $this->no_weights_report_model->getTraineeOptionChartValue($company_id, $workshop_id, $feedbackset_id, $trainee_id, $WRightsFlag, $login_id);

            if (count((array)$ChartData) > 0) {
                foreach ($ChartData as $value) {
                    $data ['option_a'] = json_encode($value->opt_a_selected, JSON_NUMERIC_CHECK);
                    $data ['option_b'] = json_encode($value->opt_b_selected, JSON_NUMERIC_CHECK);
                    $data ['option_c'] = json_encode($value->opt_c_selected, JSON_NUMERIC_CHECK);
                    $data ['option_d'] = json_encode($value->opt_d_selected, JSON_NUMERIC_CHECK);
                    $data ['option_e'] = json_encode($value->opt_e_selected, JSON_NUMERIC_CHECK);
                    $data ['option_f'] = json_encode($value->opt_f_selected, JSON_NUMERIC_CHECK);
                    $lcHtml .= $this->load->view('no_weights_report/show_report_option_overview', $data, true);
                }
            }
        } else {
            $error = "Please Select Feedabck..!";
        }

        $Rdata['HtmlData'] = $lcHtml;
        $Rdata['Error'] = $error;
        echo json_encode($Rdata);
    }

}
