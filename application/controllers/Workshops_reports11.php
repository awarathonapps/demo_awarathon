<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshops_reports extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('workshop_reports');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('workshops_reports_model');
    }

    public function index()
    {
        $data['module_id'] = '93';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if (!$this->mw_session['superaccess']) {
                $trainer_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            // ==========================================*// trainee_played_result Start here 10-04-2023  Nirmal Gajjar //*=================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

            if ($RightsFlag) {
                $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $Company_id . '"');
            } else {
                $data['TrainerData'] = $this->common_model->getUserRightsList($Company_id, $RightsFlag);
            }
            if ($WRightsFlag) {
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
                $data['WorkshopTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'company_id,id,workshop_type', 'company_id=' . $Company_id);
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                    . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['WorkshopTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
            }
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
            // ==========================================*// trainee_played_result End  //*=================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

            // ==========================================*// trainee_wise_summary_report Start here 10-04-2023  Nirmal Gajjar //*=================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            $Login_id = $this->mw_session['user_id'];
            if ($WRightsFlag) {
                $data['Tws_TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                    . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
                $data['Tws_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                $data['Tws_WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id, 0);
                $data['Tws_TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                $data['Tws_RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['Tws_WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            $data['Tws_DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
            // =============================================*// trainee_wise_summary_report End  //*==============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

            // =============================================*// traineetopic_wise_report Start here 11-04-2023 Nirmal Gajjar  //*==============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            if ($WRightsFlag) {
                $data['Ttqwr_TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                    . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
                $data['Ttqwr_WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
                $data['Ttqwr_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                $data['Ttqwr_WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['Ttqwr_RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['Ttqwr_TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                $data['Ttqwr_WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['Ttqwr_WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            $data['Ttqwr_DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
            // =============================================*// traineetopic_wise_report End  //*==============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

            // =============================================*// trainer_wise_summary_report Start here 11-04-2023 Nirmal Gajjar  //*==============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            if ($RightsFlag) {
                $data['Twr_TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $Company_id . '"');
                $data['Twr_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['Twr_WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $data['Twr_TrainerData'] = $this->common_model->getUserRightsList($Company_id, $trainer_id);
                $data['Twr_RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['Twr_WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            // =============================================*// trainer_wise_summary_report End//*==============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================


            // =============================================*// trainer_consolidated_report Start here 11-04-2023 Nirmal Gajjar //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            if ($RightsFlag) {
                $data['Tcr_TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $Company_id . '"', 'first_name');
            } else {
                $this->common_model->SyncTrainerRights($trainer_id);
                $data['Tcr_TrainerData'] = $this->common_model->getUserRightsList($Company_id, $trainer_id);
            }
            if ($WRightsFlag) {
                $data['Tcr_WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                $data['Tcr_WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id, "id desc");
                $data['Tcr_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['Tcr_RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['Tcr_WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['Tcr_WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
            }
            // =============================================*// trainer_consolidated_report End  //*==============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================


            // =============================================*// workshop_wise_report Start here 11-04-2023 Nirmal Gajjar //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            if ($WRightsFlag) {
                $data['Wwr_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['Wwr_WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                $data['Wwr_WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id, 'id desc');
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['Wwr_WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['Wwr_WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['Wwr_RegionData'] = $this->common_model->getUserRegionList($Company_id);
            }
            // =============================================*// workshop_wise_report End //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

            // =============================================*// question_wise_report Start here 11-04-2023 Nirmal Gajjar //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            if ($WRightsFlag) {
                $data['Qwr_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['Qwr_WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['Qwr_RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['Qwr_WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            // =============================================*// question_wise_report End //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

            // =============================================*// imei_report Start here 11-04-2023 Nirmal Gajjar //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
            if ($WRightsFlag) {
                $data['Dir_RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['Dir_TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                    . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['Dir_RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['Dir_TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
            }
            $data['Dir_DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
            // =============================================*// imei_report End //*=============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================

        }
        $data['Company_id'] = $Company_id;
        $this->load->view('workshop_reports/index', $data);
    }
    // ==========================================//* trainee_played_result Start here 10-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function TPR_DatatableRefresh()
    {
        $dtSearchColumns = array('du.user_id', 'concat(du.firstname," ",du.lastname)', 'w.workshop_name', 'srg.description', 'wst.description', 'wt.workshop_type', 'rg.region_name', 'ar.workshop_session', 'qt.description', 'cu.last_name', 'cu.first_name', 'dt.description');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $wrkshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($wrkshoptype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $wrkshoptype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $wrkshoptype_id;
            }
        }
        $wrkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wrkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wrkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id = " . $wrkshop_id;
            }
        }
        $session_id = ($this->input->get('sessions') ? $this->input->get('sessions') : '');
        if ($session_id != "") {
            if ($session_id == 0) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.workshop_session  = 'PRE' ";
                } else {
                    $dtWhere .= " WHERE ar.workshop_session = 'PRE' ";
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.workshop_session  = 'POST' ";
                } else {
                    $dtWhere .= " WHERE ar.workshop_session = 'POST' ";
                }
            }
        }
        $topic_id = ($this->input->get('topic_id') ? $this->input->get('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE ar.topic_id = " . $topic_id;
            }
        }
        $subtopic_id = ($this->input->get('subtopic_id') ? $this->input->get('subtopic_id') : '');
        if ($subtopic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.subtopic_id  = " . $subtopic_id;
            } else {
                $dtWhere .= " WHERE ar.subtopic_id = " . $subtopic_id;
            }
        }

        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id = " . $user_id;
            }
        }
        $trainer_id = ($this->input->get('trainer_id') ? $this->input->get('trainer_id') : '0');
        if ($trainer_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.trainer_id  = " . $trainer_id;
            } else {
                $dtWhere .= " WHERE ar.trainer_id = " . $trainer_id;
            }
        }

        $result_search = ($this->input->get('result_search') ? $this->input->get('result_search') : '');
        if ($result_search != "") {
            if ($result_search == 1) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.is_correct  = 1";
                } else {
                    $dtWhere .= " WHERE ar.is_correct = 1";
                }
            }
            if ($result_search == 2) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.is_wrong  = 1";
                } else {
                    $dtWhere .= " WHERE ar.is_wrong = 1";
                }
            }
            if ($result_search == 3) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.is_timeout  = 1";
                } else {
                    $dtWhere .= " WHERE ar.is_timeout = 1";
                }
            }
        }
        $rgion_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($rgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $rgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $rgion_id;
            }
        }
        $workshopsubtype_id = ($this->input->get('workshopsubtype_id') ? $this->input->get('workshopsubtype_id') : '');
        if ($workshopsubtype_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubtype_id  = " . $workshopsubtype_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshopsubtype_id;
            }
        }
        $subregion_id = ($this->input->get('subregion_id') ? $this->input->get('subregion_id') : '');
        if ($subregion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubregion_id  = " . $subregion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $subregion_id;
            }
        }
        $trgion_id = ($this->input->get('tregion_id') ? $this->input->get('tregion_id') : '0');
        if ($trgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $trgion_id;
            } else {
                $dtWhere .= " WHERE du.region_id = " . $trgion_id;
            }
        }
        $designation_id = ($this->input->get('designation_id') ? $this->input->get('designation_id') : '0');
        //echo($designation_id);
        if ($designation_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.designation_id  = " . $designation_id;
            } else {
                $dtWhere .= " WHERE du.designation_id = " . $designation_id;
            }
        }
        $this->session->set_userdata(array('TPR_exportWhere' => $dtWhere, 'TPR_RightsFlag' => $RightsFlag, 'TPR_WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshops_reports_model->Tpr_LoadDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'traineename', 'designation', 'workshop_name', 'workshop_type', 'workshop_subtype', 'region_name', 'sub_region', 'workshop_session', 'questionset', 'trainername', 'tregion_name', 'topicname', 'subtopicname', 'question_title', 'correct_answer', 'user_answer', 'start_dttm', 'end_dttm', 'seconds', 'timer', 'question_result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_Tpr_Report()
    { //In use for Export
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Trainee Name")
                ->setCellValue('C3', "Designation")
                ->setCellValue('D3', "Workshop Name")
                ->setCellValue('E3', "Workshop Type")
                ->setCellValue('F3', "Workshop Sub-Type")
                ->setCellValue('G3', "Workshop Sub-Region")
                ->setCellValue('H3', "Workshop Region")
                ->setCellValue('I3', "Session")
                ->setCellValue('J3', "Question Set")
                ->setCellValue('K3', "Trainer Name")
                ->setCellValue('L3', "Trainee Region")
                ->setCellValue('M3', "TOPIC NAME")
                ->setCellValue('N3', "SUB TOPIC NAME")
                ->setCellValue('O3', "QUESTION ID")
                ->setCellValue('P3', "QUESTION TITLE")
                ->setCellValue('Q3', "CORRECT ANSWER")
                ->setCellValue('R3', "USER ANSWERER")
                ->setCellValue('S3', "START DATE / TIME	")
                ->setCellValue('T3', "END DATE / TIME")
                ->setCellValue('U3', "SECONDS")
                ->setCellValue('V3', "CORRECT/WRONG/TIME OUT");
            $styleArray = array(
                'font' => array(
                    //                'color' => array('rgb' => '990000'),
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:V3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $TPR_exportWhere = $this->session->userdata('TPR_exportWhere');
            $TPR_RightsFlag = $this->session->userdata('TPR_RightsFlag');
            $TPR_WRightsFlag = $this->session->userdata('TPR_WRightsFlag');
            $i = 3;
            $j = 0;
            $Data_list = $this->workshops_reports_model->export_Tpr_ToExcel($TPR_exportWhere, $TPR_RightsFlag, $TPR_WRightsFlag);

            foreach ($Data_list as $value) {
                $i++;
                $j++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->user_id)
                    ->setCellValue("B$i", $value->traineename)
                    ->setCellValue("C$i", $value->designation)
                    ->setCellValue("D$i", $value->workshop_name)
                    ->setCellValue("E$i", $value->workshop_type)
                    ->setCellValue("F$i", $value->workshop_subtype)
                    ->setCellValue("G$i", $value->sub_region)
                    ->setCellValue("H$i", $value->region_name)
                    ->setCellValue("I$i", $value->workshop_session)
                    ->setCellValue("J$i", $value->questionset)
                    ->setCellValue("K$i", $value->trainername)
                    ->setCellValue("L$i", $value->tregion_name)
                    ->setCellValue("M$i", $value->topicname)
                    ->setCellValue("N$i", $value->subtopicname)
                    ->setCellValue("O$i", $value->question_id)
                    ->setCellValue("P$i", $value->question_title)
                    ->setCellValue("Q$i", $value->correct_answer)
                    ->setCellValue("R$i", $value->user_answer)
                    ->setCellValue("S$i", $value->start_dttm)
                    ->setCellValue("T$i", $value->end_dttm)
                    ->setCellValue("U$i", $value->seconds)
                    ->setCellValue("V$i", $value->question_result);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:V$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee Played Results Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshops_reports');
        }
    }

    // ==========================================//* trainee_played_result End *//=====================================================================================================================================================================================


    // ==========================================//* trainee_wise_summary_report Start here 10-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================

    public function Tws_DatatableRefresh()
    {
        $dtSearchColumns = array('ar.user_id', 'concat(du.firstname," ",du.lastname)', 'dt.description', '', '', '', '', '', 'result');

        $DTRenderArray = DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }

        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id  = " . $user_id;
            }
        }
        $workshop_type = ($this->input->get('workshop_type') ? $this->input->get('workshop_type') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $wrgion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wrgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.region  = " . $wrgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $wrgion_id;
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $session_name = ($this->input->get('workshop_session') ? $this->input->get('workshop_session') : '');
        if ($session_name != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_session  = '" . $session_name . "'";
            } else {
                $dtWhere .= " WHERE ar.workshop_session  = '" . $session_name . "'";
            }
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $region_id;
            } else {
                $dtWhere .= " WHERE du.region_id  = " . $region_id;
            }
        }
        $designation_id = ($this->input->get('designation_id') ? $this->input->get('designation_id') : '0');
        if ($designation_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.designation_id  = " . $designation_id;
            } else {
                $dtWhere .= " WHERE du.designation_id = " . $designation_id;
            }
        }
        $dthaving = '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');

        if ($range_value != '') {
            $range_id = explode("-", $range_value);
            if (count((array)$range_id) > 0) {
                $from_range = $range_id[0];
                $to_range = $range_id[1];
                if ($from_range != "" && $to_range != "") {
                    $dthaving = " HAVING (format(sum(ar.is_correct)*100/count(ar.id),2)) between " . $from_range . " and " . $to_range;
                }
            }
        }

        $this->session->set_userdata(array('Tws_exportWhere' => $dtWhere, 'Tws_exportHaving' => $dthaving, 'Tws_RightsFlag' => $RightsFlag, 'Tws_WRightsFlag' => $WRightsFlag, 'Tws_Company_id' => $cmp_id, 'Tws_exportOrder' => $dtOrder));

        $DTRenderArray = $this->workshops_reports_model->TraineeSummaryLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'traineename', 'designation', 'region_name', 'TOTALworkshop', 'played_que', 'correct', 'wrong', 'result', 'avg_resp_time');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_Tws_Report()
    { //In use for Export
        $dtWhere = "";
        $dthaving = '';
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Trainee Name")
                ->setCellValue('C3', "Designation")
                ->setCellValue('D3', "Trainee Region")
                ->setCellValue('E3', "No of Workshop")
                ->setCellValue('F3', "Questions Played")
                ->setCellValue('G3', "Correct")
                ->setCellValue('H3', "Wrong")
                ->setCellValue('I3', "Result")
                ->setCellValue('J3', "Avg Responce Time");


            $styleArray = array(
                'font' => array(
                    //                    'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(23);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $dtOrder = $this->session->userdata('Tws_exportOrder');

            $dtWhere = $this->session->userdata('Tws_exportWhere');
            $dthaving = $this->session->userdata('Tws_exportHaving');
            $RightsFlag = $this->session->userdata('Tws_RightsFlag');
            $WRightsFlag = $this->session->userdata('Tws_WRightsFlag');
            $i = 3;

            $Data_list = $this->workshops_reports_model->TraineeSummaryExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag, $dtOrder);


            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->user_id)
                    ->setCellValue("B$i", $value->traineename)
                    ->setCellValue("C$i", $value->designation)
                    ->setCellValue("D$i", $value->region_name)
                    ->setCellValue("E$i", $value->TOTALworkshop)
                    ->setCellValue("F$i", $value->played_que)
                    ->setCellValue("G$i", $value->correct)
                    ->setCellValue("H$i", $value->wrong)
                    ->setCellValue("I$i", $value->result)
                    ->setCellValue("J$i", $value->avg_resp_time);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee_wise Summary Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshops_reports');
        }
        //          echo $cmp=$this->input->post('company_id');exit;
    }
    // ==========================================//* trainee_wise_summary_report End *//=====================================================================================================================================================================================

    // ==========================================//* traineetopic_wise_report Start here 11-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function Ttqwr_DatatableRefresh()
    {
        $report_type = ($this->input->get('report_type') ? $this->input->get('report_type') : '1');
        if ($report_type == 1) {
            $dtSearchColumns = array('ar.user_id', 'du.emp_id', 'concat(du.firstname," ",du.lastname)', 'wr.region_name', 'wt.workshop_type', 'w.workshop_name', 'qt.description', 'du.lastname', 'wsr.description', 'wst.description');
        } else if ($report_type == 2) {
            $dtSearchColumns = array('ar.user_id', 'du.emp_id', 'concat(du.firstname," ",du.lastname)', 'wr.region_name', 'wt.workshop_type', 'w.workshop_name', 'qt.title', 'du.lastname', 'wsr.description', 'wst.description');
        } else {
            $dtSearchColumns = array('ar.user_id', 'du.emp_id', 'concat(du.firstname," ",du.lastname)', 'wr.region_name', 'wt.workshop_type', 'w.workshop_name', 'w.workshop_name', 'du.lastname', 'wsr.description', 'wst.description');
        }

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id  = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }

        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id  = " . $user_id;
            }
        }
        $wkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id  = " . $wkshop_id;
            }
        }
        $topic_id = ($this->input->get('topic_id') ? $this->input->get('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE ar.topic_id  = " . $topic_id;
            }
        }
        $dthaving = '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');

        if ($range_value != '') {
            $range_id = explode("-", $range_value);
            if (count((array)$range_id) > 0) {
                $from_range = $range_id[0];
                $to_range = $range_id[1];
                if ($from_range != "" && $to_range != "") {
                    $dthaving = " HAVING (format(sum(ar.is_correct)*100/count(ar.id),2)) between " . $from_range . " and " . $to_range;
                }
            }
        }
        $session_name = ($this->input->get('workshop_session') ? $this->input->get('workshop_session') : '');
        if ($session_name != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_session  = '" . $session_name . "'";
            } else {
                $dtWhere .= " WHERE ar.workshop_session  = '" . $session_name . "'";
            }
        }
        $store_id = ($this->input->get('store_id') ? $this->input->get('store_id') : '');
        if ($store_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.store_id  = " . $store_id;
            } else {
                $dtWhere .= " WHERE du.store_id = " . $store_id;
            }
        }
        $tregion_id = ($this->input->get('tregion_id') ? $this->input->get('tregion_id') : '0');
        if ($tregion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $tregion_id;
            } else {
                $dtWhere .= " WHERE du.region_id = " . $tregion_id;
            }
        }
        $wrgion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wrgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.region  = " . $wrgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $wrgion_id;
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_type = ($this->input->get('workshop_type') ? $this->input->get('workshop_type') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $designation_id = ($this->input->get('designation_id') ? $this->input->get('designation_id') : '0');
        if ($designation_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.designation_id  = " . $designation_id;
            } else {
                $dtWhere .= " WHERE du.designation_id = " . $designation_id;
            }
        }
        $this->session->set_userdata(array('Ttqwr_exportWhere'  => $dtWhere, 'exportHaving'  => $dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag, 'report_type' => $report_type, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshops_reports_model->Ttqwr_LoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag, $report_type);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'emp_id', 'trainee_region', 'traineename', 'designation', 'workshop_region', 'workshop_subregion', 'workshop_type', 'workshop_subtype', 'workshop_name', 'title', 'played_que', 'correct', 'wrong', 'result');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function export_Ttqwr_Report()
    { //In use for Export
        $dtWhere = $this->session->userdata('Ttqwr_exportWhere');
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $report_type = $this->session->userdata('report_type');
            $dthaving = $this->session->userdata('exportHaving');
            $RightsFlag  = $this->session->userdata('RightsFlag');
            $WRightsFlag = $this->session->userdata('WRightsFlag');
            if ($report_type == 1) {
                $title = "Topic";
                $file_name = "Trainee-Topic Wise Report.xls";
            } else if ($report_type == 2) {
                $title = "Questions Set";
                $file_name = "Trainee-Questionsset Wise Report.xls";
            } else {
                $title = " No of Questions Set";
                $file_name = "Trainee-workshop Wise Report.xls";
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Employee ID")
                ->setCellValue('C3', "Trainee Region")
                //                ->setCellValue('D3', "Store Name")
                ->setCellValue('D3', "Trainee Name")
                ->setCellValue('E3', "Designation")
                ->setCellValue('F3', "Workshop Region")
                ->setCellValue('G3', "Workshop Sub-region")
                ->setCellValue('H3', "Workshop type")
                ->setCellValue('I3', "Workshop Sub-type")
                ->setCellValue('J3', "Workshop Name")
                ->setCellValue('K3', $title)
                ->setCellValue('L3', "Questions Played")
                ->setCellValue('M3', "Correct")
                ->setCellValue('N3', "Wrong")
                ->setCellValue('O3', "Result");
            $styleArray = array(
                'font' => array(
                    //                'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:O3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $i = 3;

            $Data_list = $this->workshops_reports_model->export_Ttqwr_ToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag, $report_type);

            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->user_id)
                    ->setCellValue("B$i", $value->emp_id)
                    ->setCellValue("C$i", $value->trainee_region)
                    //                    ->setCellValue("D$i", $value->store_name)
                    ->setCellValue("D$i", $value->traineename)
                    ->setCellValue("E$i", $value->designation)
                    ->setCellValue("F$i", $value->workshop_region)
                    ->setCellValue("G$i", $value->workshop_subregion)
                    ->setCellValue("H$i", $value->workshop_type)
                    ->setCellValue("I$i", $value->workshop_subtype)
                    ->setCellValue("J$i", $value->workshop_name)
                    ->setCellValue("K$i", $value->title)
                    ->setCellValue("L$i", $value->played_que)
                    ->setCellValue("M$i", $value->correct)
                    ->setCellValue("N$i", $value->wrong)
                    ->setCellValue("O$i", $value->result);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:O$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $file_name . '"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
        } else {
            redirect('workshops_reports');
        }

        // Sending headers to force the user to download the file
    }
    // ==========================================//* traineetopic_wise_report End *//=====================================================================================================================================================================================

    // ==========================================//* trainer_wise_summary_report Start here 10-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================
    public function ajax_companywise_data()
    {
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainername', 'company_id=' . $company_id, 'first_name');
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
            if ($RightsFlag) {
                $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"', 'first_name');
            } else {
                $data['TrainerData'] = $this->common_model->getUserRightsList($company_id, $trainer_id);
            }
        }
        echo json_encode($data);
    }

    public function Twr_DatatableRefresh()
    {
        $dtSearchColumns = array('CONCAT(cu.first_name," ", cu.last_name)');
        //'cm.company_name',
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $trainer_id = ($this->input->get('trainer_id') ? $this->input->get('trainer_id') : '');
        if ($trainer_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.trainer_id  = " . $trainer_id;
            } else {
                $dtWhere .= " WHERE ar.trainer_id  = " . $trainer_id;
            }
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $region_id;
            } else {
                $dtWhere .= " WHERE w.region  = " . $region_id;
            }
        }
        $wsubrgion_id = ($this->input->get('subregion_id') ? $this->input->get('subregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_type = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $workshop_subtype = ($this->input->get('workshopsubtype_id') ? $this->input->get('workshopsubtype_id') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $dthaving = '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');

        if ($range_value != '') {
            $range_id = explode("-", $range_value);
            if (count((array)$range_id) > 0) {
                $from_range = $range_id[0];
                $to_range = $range_id[1];
                if ($from_range != "" && $to_range != "") {
                    $dthaving = " HAVING (format(sum(ar.is_correct)*100/count(ar.id),2)) between " . $from_range . " and " . $to_range;
                }
            }
        }

        $this->session->set_userdata(array('Twr_exportWhere' => $dtWhere, 'Twr_exportHaving' => $dthaving, 'Twr_RightsFlag' => $RightsFlag, 'Twr_WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshops_reports_model->TrainerSummaryLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainername', 'TOTALworkshop', 'TOTALtrainee', 'TOTALtopic', 'TOTALsubtopic', 'played_que', 'correct', 'wrong', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_Twr_Report()
    { //In use for Export
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Trainer Name")
                ->setCellValue('B3', "No of Workshop")
                ->setCellValue('C3', "No of Trainees")
                ->setCellValue('D3', "No of Topics")
                ->setCellValue('E3', "No of Sub-topics")
                ->setCellValue('F3', "Questions Played")
                ->setCellValue('G3', "Correct")
                ->setCellValue('H3', "Wrong")
                ->setCellValue('I3', "Result");


            $styleArray = array(
                'font' => array(
                    //                'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $dtWhere = $this->session->userdata('Twr_exportWhere');
            $dthaving = $this->session->userdata('Twr_exportHaving');
            $RightsFlag = $this->session->userdata('Twr_RightsFlag');
            $WRightsFlag = $this->session->userdata('Twr_WRightsFlag');
            $i = 3;

            $Data_list = $this->workshops_reports_model->TrainerSummaryExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);


            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    //                    ->setCellValue("A$i", $value->company_name)
                    ->setCellValue("A$i", $value->trainername)
                    ->setCellValue("B$i", $value->TOTALworkshop)
                    ->setCellValue("C$i", $value->TOTALtrainee)
                    ->setCellValue("D$i", $value->TOTALtopic)
                    ->setCellValue("E$i", $value->TOTALsubtopic)
                    ->setCellValue("F$i", $value->played_que)
                    ->setCellValue("G$i", $value->correct)
                    ->setCellValue("H$i", $value->wrong)
                    ->setCellValue("I$i", $value->result);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
            }



            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainer_wise Summary Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshops_reports');
        }
    }
    // ==========================================//* trainer_wise_summary_report End*//=====================================================================================================================================================================================

    // ==========================================//* trainer_consolidated_report Start here 11-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================
    public function ajax_topicwise_data()
    {
        $topic_id = $this->input->post('topic_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $lchtml = '<option value="">Please Select</option>';
        if ($topic_id != '') {
            $SubTopicData = $this->common_model->get_selected_values('question_subtopic', 'id,description', 'company_id=' . $company_id . ' and topic_id=' . $topic_id, 'description');
            if (count((array)$SubTopicData) > 0) {
                foreach ($SubTopicData as $value) {
                    $lchtml .= '<option value="' . $value->id . '">' . $value->description . '</option>';
                }
            }
        }
        echo $lchtml;
    }
    public function Tcr_DatatableRefresh()
    {
        $dtSearchColumns = array('r.region_name', 'wsr.description', 'wt.workshop_type', 'wst.description', 'w.workshop_name', 'CONCAT(cu.first_name," ",cu.last_name)', 'qt.description', 'qst.description');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $dtHaving = '';
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $wrkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wrkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wrkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id = " . $wrkshop_id;
            }
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($region_id == 0) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND w.region  = " . $region_id;
                } else {
                    $dtWhere .= " WHERE w.region  = " . $region_id;
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND w.region  = " . $region_id;;
                } else {
                    $dtWhere .= " WHERE w.region  = " . $region_id;;
                }
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $topic_id = ($this->input->get('topic_id') ? $this->input->get('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE ar.topic_id = " . $topic_id;
            }
        }
        $subtopic_id = ($this->input->get('subtopic_id') ? $this->input->get('subtopic_id') : '');
        if ($subtopic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.subtopic_id  = " . $subtopic_id;
            } else {
                $dtWhere .= " WHERE ar.subtopic_id = " . $subtopic_id;
            }
        }

        $wtype_id = ($this->input->get('wtype_id') ? $this->input->get('wtype_id') : '0');
        if ($wtype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $wtype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $wtype_id;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $trainer_id = ($this->input->get('trainer_id') ? $this->input->get('trainer_id') : '0');
        if ($trainer_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.trainer_id  = " . $trainer_id;
            } else {
                $dtWhere .= " WHERE ar.trainer_id = " . $trainer_id;
            }
        }
        $session_id = ($this->input->get('session_id') ? $this->input->get('session_id') : '');
        if ($session_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_session  = '" . $session_id . "'";
            } else {
                $dtWhere .= " WHERE ar.workshop_session = '" . $session_id . "'";
            }
        }
        //      $from_range= ($this->input->get('from_range') ? $this->input->get('from_range') :'');
        //      $to_range= ($this->input->get('to_range') ? $this->input->get('to_range') :'');
        $range = ($this->input->get('result_range') ? $this->input->get('result_range') : '');
        if ($range != '') {
            $result_range = explode("-", $range);
            if (count((array)$result_range) > 0) {
                $from_range = $result_range[0];
                $to_range = $result_range[1];
                if ($from_range != "" && $to_range != '') {
                    $dtHaving .= " having result between " . $from_range . " AND " . $to_range;
                }
            }
        }
        $this->session->set_userdata(array('Tcr_exportWhere' => $dtWhere, 'Tcr_exportHaving' => $dtHaving, 'Tcr_RightsFlag' => $RightsFlag, 'Tcr_WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshops_reports_model->TrainerConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtHaving, $RightsFlag, $WRightsFlag);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('region_name', 'workshop_subregion', 'workshop_type', 'workshop_subtype', 'workshop_name', 'trainername', 'topicname', 'subtopicname', 'total_question', 'total_trainee_played', 'total_question_played', 'total_correct_ans', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == 'result') {
                    $row[] = $dtRow['result'] . '%';
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_Tcr_Report()
    { //In use for Export

        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Workshop Region")
                ->setCellValue('B3', "Workshop Sub-region")
                ->setCellValue('C3', "Workshop Type")
                ->setCellValue('D3', "Workshop Sub-type")
                ->setCellValue('E3', "Workshop Name")
                ->setCellValue('F3', "Trainer Name")
                ->setCellValue('G3', "TOPIC")
                ->setCellValue('H3', "SUB-TOPIC")
                ->setCellValue('I3', "NO. OF UNIQUE QUESTIONS")
                ->setCellValue('J3', "NO OF TRAINEE PLAYED")
                ->setCellValue('K3', "TOTAL QUESTIONS PLAYED")
                ->setCellValue('L3', "TOTAL CORRECT ANSWERS")
                ->setCellValue('M3', "RESULT");
            $styleArray = array(
                'font' => array(
                    //                'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $exportWhere = $this->session->userdata('Tcr_exportWhere');
            $exportHaving = $this->session->userdata('Tcr_exportHaving');
            $RightsFlag = $this->session->userdata('Tcr_RightsFlag');
            $WRightsFlag = $this->session->userdata('Tcr_WRightsFlag');
            $i = 3;
            $j = 0;
            $Data_list = $this->workshops_reports_model->TrainerConsolidatedExportToExcel($exportWhere, $exportHaving, $RightsFlag, $WRightsFlag);


            foreach ($Data_list as $value) {
                $i++;
                $j++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->region_name)
                    ->setCellValue("B$i", $value->workshop_subregion)
                    ->setCellValue("C$i", $value->workshop_type)
                    ->setCellValue("D$i", $value->workshop_subtype)
                    ->setCellValue("E$i", $value->workshop_name)
                    ->setCellValue("F$i", $value->trainername)
                    ->setCellValue("G$i", $value->topicname)
                    ->setCellValue("H$i", $value->subtopicname)
                    ->setCellValue("I$i", $value->total_question)
                    ->setCellValue("J$i", $value->total_trainee_played)
                    ->setCellValue("K$i", $value->total_question_played)
                    ->setCellValue("L$i", $value->total_correct_ans)
                    ->setCellValue("M$i", $value->result . '%');
                $objPHPExcel->getActiveSheet()->getStyle("A$i:M$i")->applyFromArray($styleArray_body);
            }


            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="TrainerConsolidated_Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshops_reports');
        }
    }
    // ==========================================//* trainer_consolidated_report End*//=====================================================================================================================================================================================


    // ==========================================//* workshop_wise_report Start here 11-04-2023*//=====================================================================================================================================================================================
    public function Wwr_DatatableRefresh()
    {
        $dtSearchColumns = array('w.id', 'r.region_name', 'wm.workshop_type', 'w.workshop_name', 'w.id', 'wsr.description', 'wst.description');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $workshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $workshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id  = " . $workshop_id;
            }
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshoptype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND wm.id  = " . $workshoptype_id;
            } else {
                $dtWhere .= " WHERE wm.id  = " . $workshoptype_id;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.id  = " . $region_id;
            } else {
                $dtWhere .= " WHERE r.id  = " . $region_id;
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $dthaving = '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');
        if ($range_value != '') {
            $range_id = explode("-", $range_value);
            if (count((array)$range_id) > 0) {
                $from_range = $range_id[0];
                $to_range = $range_id[1];
                if ($from_range != "" && $to_range != "") {
                    $dthaving = " HAVING (format(sum(ar.is_correct)*100/count(ar.id),2)) between " . $from_range . " and " . $to_range;
                }
            }
        }

        $this->session->set_userdata(array('Wwr_exportWhere' => $dtWhere, 'Wwr_exportHaving' => $dthaving, 'Wwr_RightsFlag' => $RightsFlag, 'Wwr_WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshops_reports_model->WorkshopWiseLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('region_name', 'workshop_subregion', 'workshop_type', 'workshop_subtype', 'workshop_name', 'questionset', 'played_que', 'correct', 'wrong', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_Wwr_Report()
    { //In use for Export       
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Workshop Region")
                ->setCellValue('B3', "Workshop Sub-region")
                ->setCellValue('C3', "Workshop Type")
                ->setCellValue('D3', "Workshop Sub-type")
                ->setCellValue('E3', "Workshop name")
                ->setCellValue('F3', "No of Question Set")
                ->setCellValue('G3', "Questions Played")
                ->setCellValue('H3', "Correct")
                ->setCellValue('I3', "Wrong")
                ->setCellValue('J3', "Result");
            $styleArray = array(
                'font' => array(
                    //                    'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(23);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $dtWhere = $this->session->userdata('Wwr_exportWhere');
            $dthaving = $this->session->userdata('Wwr_exportHaving');
            $RightsFlag = $this->session->userdata('Wwr_RightsFlag');
            $WRightsFlag = $this->session->userdata('Wwr_WRightsFlag');
            $i = 3;

            $Data_list = $this->workshops_reports_model->WorkshopWiseExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);


            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->region_name)
                    ->setCellValue("B$i", $value->workshop_subregion)
                    ->setCellValue("C$i", $value->workshop_type)
                    ->setCellValue("D$i", $value->workshop_subtype)
                    ->setCellValue("E$i", $value->workshop_name)
                    ->setCellValue("F$i", $value->questionset)
                    ->setCellValue("G$i", $value->played_que)
                    ->setCellValue("H$i", $value->correct)
                    ->setCellValue("I$i", $value->wrong)
                    ->setCellValue("J$i", $value->result . '%');

                $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->applyFromArray($styleArray_body);
            }


            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Workshop-wise Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshops_reports');
        }
    }
    // ==========================================//* workshop_wise_report End*//=====================================================================================================================================================================================


    // ==========================================//* question_wise_report Start here 11-04-2023*//=====================================================================================================================================================================================

    public function Qwr_DatatableRefresh()
    {
        $dtSearchColumns = array('a.question_id', 'wrk.question_title', 'qs.title', 'a.workshop_name', 'wt.workshop_type', 'r.region_name', 'wrk.option_a', 'wrk.option_b', 'wrk.option_c', 'wrk.option_d');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere2 = $DTRenderArray['dtWhere'];

        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $dtHaving = '';
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        $dtWhere = '';
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND wq.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE wq.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $region_id;
            } else {
                $dtWhere .= " WHERE w.region  = " . $region_id;
            }
        }
        $wsubrgion_id = ($this->input->get('subregion_id') ? $this->input->get('subregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_type = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $workshop_subtype = ($this->input->get('workshopsubtype_id') ? $this->input->get('workshopsubtype_id') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        //      $from_range= ($this->input->get('from_range') ? $this->input->get('from_range') :'');
        //      $to_range= ($this->input->get('from_range') ? $this->input->get('from_range') :'');
        $range = ($this->input->get('result_range') ? $this->input->get('result_range') : '');

        if ($range != '') {
            $result_range = explode("-", $range);
            if (count((array)$result_range) > 0) {
                $from_range = $result_range[0];
                $to_range = $result_range[1];
                if ($from_range != "" && $to_range != "") {
                    $dtHaving .= " having result between " . $from_range . " AND " . $to_range;
                }
            }
        }
        if (!$WRightsFlag) {
            $dtWhere .= " AND wq.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $dtWhere .= " AND (wq.trainer_id = $login_id OR wq.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }

        $this->session->set_userdata(array('Qwr_exportWhere' => $dtWhere, 'Qwr_exportHaving' => $dtHaving, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshops_reports_model->QuestionWiseLoadDataTable($dtWhere, $dtWhere2, $dtOrder, $dtLimit, $dtHaving);
        //        
        //        echo "<pre>";
        //        print_r($DTRenderArray['ResultSet']);exit;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('question_id', 'question_title', 'questionset', 'workshop_name', 'workshop_type', 'workshop_subtype', 'workshop_subregion', 'region_name', 'correct_answer', 'no_of_trainee_played', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == 'result') {
                    $row[] = $dtRow['result'] . '%';
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_Qwr_Report()
    { //In use for Export
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "QUESTION ID")
                ->setCellValue('B3', "QUESTION")
                ->setCellValue('C3', "QUESTION SET")
                ->setCellValue('D3', "WORKSHOP NAME")
                ->setCellValue('E3', "WORKSHOP TYPE")
                ->setCellValue('F3', "WORKSHOP SUB-TYPE")
                ->setCellValue('G3', "WORKSHOP REGION")
                ->setCellValue('H3', "WORKSHOP SUB-REGION")
                ->setCellValue('I3', "CORRECT ANSWERS")
                ->setCellValue('J3', "NO OF TRAINEE PLAYED")
                ->setCellValue('K3', "RESULT");
            $styleArray = array(
                'font' => array(
                    //                    'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);


            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $exportWhere = $this->session->userdata('Qwr_exportWhere');
            $exportHaving = $this->session->userdata('Qwr_exportHaving');
            $i = 3;
            $j = 0;
            $Data_list = $this->workshops_reports_model->QuestionWiseExportToExcel($exportWhere, $exportHaving);

            foreach ($Data_list as $value) {
                $i++;
                $j++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->question_id)
                    ->setCellValue("B$i", $value->question_title)
                    ->setCellValue("C$i", $value->questionset)
                    ->setCellValue("D$i", $value->workshop_name)
                    ->setCellValue("E$i", $value->workshop_type)
                    ->setCellValue("F$i", $value->workshop_subtype)
                    ->setCellValue("G$i", $value->region_name)
                    ->setCellValue("H$i", $value->workshop_subregion)
                    ->setCellValue("I$i", $value->correct_answer)
                    ->setCellValue("J$i", $value->no_of_trainee_played)
                    ->setCellValue("K$i", $value->result . '%');
                $objPHPExcel->getActiveSheet()->getStyle("A$i:K$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="QuestionWise_Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshops_reports');
        }
    }

    // ==========================================//* question_wise_report End*//=====================================================================================================================================================================================


    // ==========================================//* imei_report Start here 12-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================
    public function ajax_wtypewise_workshop()
    {
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $user_id = "";
        $region_id = "";
        $workshop_type = $this->input->post('workshop_type', TRUE);
        if ($WRightsFlag) {
            $data['WorkshopData'] = $this->common_model->getUserWorkshopList($company_id, $user_id, $workshop_type);
        } else {
            $data['WorkshopData'] = $this->common_model->getWkshopRegRightsList($company_id, $trainer_id, $region_id, $workshop_type);
        }

        echo json_encode($data);
    }

    public function ajax_resionwise_data()
    {
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $workshop_type = "";
        $region_id = $this->input->post('region_id', TRUE);
        if ($WRightsFlag) {
            $data['WorkshopData'] = $this->workshops_reports_model->getWorkshopList($company_id, $region_id);
        } else {
            $data['WorkshopData'] = $this->common_model->getWkshopRegRightsList($company_id, $trainer_id, $region_id, $workshop_type);
        }
        echo json_encode($data);
    }

    public function Dir_DatatableRefresh()
    {
        $dtSearchColumns = array('u.user_id', 'u.emp_id', 'u.firstname', 'u.lastname', '', '', '', 'u.education_background', 'u.department', 'rg.region_name', 'dr.description', 'u.area');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $cmp_id;
            }
        }
        $trgion_id = $this->input->get('tregion_id', true);
        if ($trgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.region_id  = " . $trgion_id;
            } else {
                $dtWhere .= " WHERE u.region_id = " . $trgion_id;
            }
        }
        $designation_id = $this->input->get('designation_id', true);
        if ($designation_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.designation_id  = " . $designation_id;
            } else {
                $dtWhere .= " WHERE u.designation_id = " . $designation_id;
            }
        }
        $user_id = $this->input->get('user_id', true);
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE u.user_id = " . $user_id;
            }
        }
        $DTRenderArray = $this->workshops_reports_model->ImeiDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array(
            'user_id', 'emp_id', 'firstname', 'lastname', 'email', 'mobile', 'employment_year', 'education_background',
            'department', 'region_name', 'designation', 'area', 'status', 'platform', 'model', 'imei', 'serial', 'info_dttm'
        );
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "info_dttm") {
                    $row[] = ($dtRow['info_dttm'] != '00-00-0000 12:00 AM' ? $dtRow['info_dttm'] : '');
                } else if ($dtDisplayColumns[$i] == "status") {
                    $row[] = ($dtRow['status'] == 1 ? 'Active' : 'In-Active');
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }

        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag));
        echo json_encode($output);
    }

    public function export_Dir_Report()
    { //In use for Export       
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', '')
            ->setCellValue('A2', 'Trainee Id')
            ->setCellValue('B2', 'Employee Code')
            ->setCellValue('C2', 'First Name')
            ->setCellValue('D2', 'Last Name')
            ->setCellValue('E2', 'Email')
            ->setCellValue('F2', 'Mobile No.')
            ->setCellValue('G2', 'Employment Year')
            ->setCellValue('H2', 'Education Background')
            ->setCellValue('I2', 'Department/Division')
            ->setCellValue('J2', 'Region/Branch')
            ->setCellValue('K2', 'Designation')
            ->setCellValue('L2', 'Area')
            ->setCellValue('M2', 'Status')
            ->setCellValue('N2', 'Platform')
            ->setCellValue('O2', 'Model')
            ->setCellValue('P2', 'IMEI')
            ->setCellValue('Q2', 'Serial No')
            ->setCellValue('R2', 'Date & Time');

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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A2:R2')->applyFromArray($styleArray_header);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A2:R2")->applyFromArray($styleArray_body);
        $dtWhere = $this->session->userdata('exportWhere');
        $RightsFlag = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');
        $i = 2;
        $TraineeSet = $this->workshops_reports_model->ImeiExportToExcel($dtWhere, $RightsFlag, $WRightsFlag);
        $j = 0;
        foreach ($TraineeSet as $Trainee) {
            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$i", $Trainee->user_id)
                ->setCellValue("B$i", $Trainee->emp_id)
                ->setCellValue("C$i", $Trainee->firstname)
                ->setCellValue("D$i", $Trainee->lastname)
                ->setCellValue("E$i", $Trainee->email)
                ->setCellValue("F$i", $Trainee->mobile)
                ->setCellValue("G$i", $Trainee->employment_year)
                ->setCellValue("H$i", $Trainee->education_background)
                ->setCellValue("I$i", $Trainee->department)
                ->setCellValue("J$i", $Trainee->region_name)
                ->setCellValue("K$i", $Trainee->designation)
                ->setCellValue("L$i", $Trainee->area)
                ->setCellValue("M$i", ($Trainee->status ? 'Active' : 'In-Active'))
                ->setCellValue("N$i", $Trainee->platform)
                ->setCellValue("O$i", $Trainee->model)
                ->setCellValue("P$i", $Trainee->imei)
                ->setCellValue("Q$i", $Trainee->serial)
                ->setCellValue("R$i", ($Trainee->info_dttm != '00-00-0000 12:00 AM' ? $Trainee->info_dttm : ''));
            $objPHPExcel->getActiveSheet()->getStyle("A$i:R$i")->applyFromArray($styleArray_body);
        }

        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="IMEI Report.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

    // ==========================================//* imei_report End*//=====================================================================================================================================================================================



}
