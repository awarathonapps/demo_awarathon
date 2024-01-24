<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_trainee_workshop_reports extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_trainee_workshop_reports');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('trainer_trainee_workshop_reports_model');
    }

    public function index($trainer_id = '')
    {
        $data['module_id'] = '97';
        $data['username'] = $this->mw_session['username'];
        $data['trainee_name'] = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        $company_id = $this->mw_session['company_id'];
        $data['user_id'] = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;


        // Trainer Workshop Start here
        $data['trainer_id'] = $trainer_id;
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($company_id == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
            if ($trainer_id != "") {
                $Rowset = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $trainer_id);
                $company_id = $Rowset->company_id;
                $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
            }
        } else {
            $data['company_array'] = array();
            $login_id = $this->mw_session['user_id'];
            $this->load->model('trainer_trainee_workshop_reports_model');
            $this->trainer_trainee_workshop_reports_model->SynchTraineeData($company_id);
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if ($RightsFlag) {
                $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            } else {
                $this->common_model->SyncTrainerRights($login_id);
                $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, $login_id);
            }
            if ($WRightsFlag) {
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $company_id);
            } else {
                $this->common_model->SyncWorkshopRights($login_id, 0);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($company_id);
            }
        }
        $data['Supcompany_id'] = $company_id;
        $data['wksh_top_five_array'] = [];
        // Trainer Workshop Stare here




        // Trainer Comperision Start here 
        $data['company_id'] = $this->mw_session['company_id'];
        $company_id = '';
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['company_id'] == "") {
            if ($trainer_id != '') {
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($company_id, 1);
            }
        } else {
            $data['company_array'] = array();
            $Login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            $company_id = $data['company_id'];
            if ($RightsFlag) {
                $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            } else {
                $this->common_model->SyncTrainerRights($Login_id);
                $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, $Login_id);
            }
            if ($WRightsFlag) {
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($company_id, 1);
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id, 0);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id);
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($company_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($company_id);
            }
        }
        $data['trainer_id'] = $trainer_id;
        // Trainer Comperision end here


        // Trainee Accuracy Start Here 
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $company_id = "";
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
            if ($trainer_id != "") {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $trainer_id);
                $company_id = $Rowset->company_id;
                $data['Trainer_array'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
                $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
            }
        } else {
            $data['company_array'] = array();
            $Login_id  = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            $company_id = $data['company_id'];
            if ($RightsFlag) {
                $data['Trainer_array'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            } else {
                $this->common_model->SyncTrainerRights($Login_id);
                $data['Trainer_array'] = $this->common_model->getUserRightsList($company_id, $Login_id);
            }
            if ($WRightsFlag) {
                $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id, 1);
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id, 0);
                $data['wksh_type_array'] = $this->common_model->getWTypeRightsList($company_id);
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($company_id);
            }

            $data['TraineeRegionData'] = $this->trainer_trainee_workshop_reports_model->get_TraineeRegionData($company_id);
        }
        $data['trainer_id'] = $trainer_id;
        $data['Supcompany_id'] = $company_id;
        $data['wksh_top_five_array'] = [];
        // Trainee Accuracy End here 



        // Trainee Dashboard I start here
        $Trainee_id = '';
        $RightsFlag = 1;
        $WRightsFlag = 1;

        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['company_array'] = array();
            if ($this->mw_session['login_type'] != 3) {
                $Login_id  = $this->mw_session['user_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                    $this->common_model->SyncWorkshopRights($Login_id, 0);
                }

                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'], $WRightsFlag);
                $data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'], $WRightsFlag);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'], $WRightsFlag);
            } else {
                $Trainee_id = $this->mw_session['user_id'];
                //$data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'],1);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'], 1);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'], 1);
            }
        }
        $data['TraineeRegionData'] = $this->trainer_trainee_workshop_reports_model->get_TraineeRegionData($data['company_id']);
        $data['acces_management'] = $this->acces_management;
        $data['DefaultTrainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        // Trainee Dashboard I end here 

        // Trainee Comperision Reports start Here
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $data['Company_id'] = $this->mw_session['company_id'];
        if ($data['Company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if ($this->mw_session['login_type'] == 3) {
                $Trainee_id = $this->mw_session['user_id'];
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'], 1);
                $data['WorkshopResultSet'] = $this->common_model->getUserWorkshopList($data['Company_id'], $Trainee_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['Company_id'], 1);
            } else {
                $Login_id  = $this->mw_session['user_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
                if (!$WRightsFlag) {
                    $this->common_model->SyncWorkshopRights($Login_id, 0);
                }
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($data['Company_id'], $WRightsFlag);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['Company_id'], $WRightsFlag);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['Company_id'], $WRightsFlag);
            }
        }
        $data['TraineeRegionData'] = $this->trainer_trainee_workshop_reports_model->get_TraineeRegionData($data['Company_id']);
        $data['Trainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        // Trainee Comperision Reports End Here 


        // Trainee Accuracy Reports Start Here 
        $Trainee_id = '';
        $data['company_id'] = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['company_array'] = array();
            if ($this->mw_session['login_type'] != 3) {
                $Login_id = $this->mw_session['user_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
                if (!$WRightsFlag) {
                    $this->common_model->SyncWorkshopRights($Login_id, 0);
                }
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'], $WRightsFlag);
                $data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'], $WRightsFlag);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'], $WRightsFlag);
            } else {
                $Trainee_id = $this->mw_session['user_id'];
                $data['WtypeResult'] = $this->common_model->getTraineeWTypeList($data['company_id'], $Trainee_id);
                $data['WorkshopResultSet'] = $this->common_model->getUserWorkshopList($data['company_id'], $Trainee_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'], 1);
            }
        }
        $data['DefaultTrainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        // Trainee Accuracy Reports end Here 


        // Workshop Reports Start Here 
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
        // Workshop Reports End Here 


        $this->load->view('trainer_trainee_workshop_reports/index', $data);
    }

    public function ajax_company_trainer_type()
    {
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $Trainer_id = '';
        } else {
            $company_id = $this->mw_session['company_id'];
            $Trainer_id = $this->mw_session['user_id'];
        }
        $lcWhere = 'status=1 AND login_type=1 AND company_id=' . $company_id;
        //$workshop_type_id = $this->input->post('workshop_type_id', TRUE);

        if ($Trainer_id != "") {
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
            if ($RightsFlag) {
                $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name)") as fullname', $lcWhere, "fullname");
            } else {
                $data['user_array'] = $this->common_model->getUserRightsList($company_id, $Trainer_id);
            }
        } else {
            $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
        }
        $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
        echo json_encode($data);
    }
    public function load_workshop()
    {

        $dtSearchColumns = array('a.workshop_id', 'w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->get('company_id', TRUE);
            $this->load->model('trainer_trainee_workshop_reports_model');
            $this->trainer_trainee_workshop_reports_model->SynchTraineeData($company_id);
        } else {
            $company_id = $this->mw_session['company_id'];
            $login_id  = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = ($this->input->get('user_id', TRUE) != '' ? $this->input->get('user_id', TRUE) : 0);
        $workshop_type_id = $this->input->get('workshop_type_id', TRUE);
        if ($workshop_type_id == "") {
            $workshop_type_id = 0;
        }
        $Workshop_id = $this->input->get('workshop_id', TRUE);
        if ($Workshop_id == null || $Workshop_id == '') {
            $Workshop_id = 0;
        }
        if ($dtWhere != "") {
            $dtWhere .= " AND a.company_id=$company_id ";
        } else {
            $dtWhere .= " WHERE a.company_id=$company_id ";
        }

        if ($Workshop_id != "0") {
            $dtWhere .= " AND a.workshop_id= " . $Workshop_id;
        }
        if ($workshop_type_id != "0") {
            $dtWhere .= " AND w.workshop_type= $workshop_type_id";
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
        }
        $wrgion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wrgion_id != "0") {
            $dtWhere .= " AND  w.region  = " . $wrgion_id;
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
        }
        if (!$WRightsFlag) {
            $dtWhere .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $dtWhere .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $dtWhere .= " AND a.trainer_id= " . $trainer_id;
        }
        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->getTrainerWorkshop($dtWhere, $dtOrder, $dtLimit);
        if (count((array)$DTRenderArray['ResultSet']) > 0) {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array(),
                "top_five_table" => '',
                "bottom_five_table" => ''
            );
            $Custom_url = base_url() . 'trainer_individual/index/' . base64_encode($company_id) . '/' . base64_encode($trainer_id);
            $dtDisplayColumns = array('workshop_id', 'workshop_name', 'total_topic', 'avg_ce', 'no_trainee', 'actions');
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $workshop_id = $dtRow['workshop_id'];
                $live_workshop = $dtRow['live_workshop'];
                $worksRowSet = $this->trainer_trainee_workshop_reports_model->workshop_statistics($RightsFlag, $company_id, $trainer_id, $workshop_id, $live_workshop);
                if (count((array)$worksRowSet['CEData']) == 0) {
                    continue;
                }
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == "total_topic") {
                        $row[] = $worksRowSet['total']->total_topic;
                    } else if ($dtDisplayColumns[$i] == "no_trainee") {
                        $row[] = $worksRowSet['total']->total_trainee;
                    } else if ($dtDisplayColumns[$i] == "avg_ce") {
                        $row[] = $worksRowSet['CEData']->ce . ($worksRowSet['CEData']->ce != 'NP' ? '%' : '');
                    } else if ($dtDisplayColumns[$i] == "actions") {
                        $action = '<a data-toggle="modal" href="javascript:void(0)" onclick="workshop_summary_trw(' . $workshop_id . ')" class="btn btn-xs blue">
                                    <i class="fa fa-bar-chart"></i> SUMMARY
                                </a>
                                <a data-toggle="modal" href="javascript:void(0)" onclick="workshop_detail(' . $workshop_id . ')" class="btn btn-xs red">
                                    <i class="fa fa-bar-chart"></i> DETAIL
                                </a>
                                <a data-toggle="modal" href="javascript:void(0)" onclick="workshop_trainee(' . $workshop_id . ')" class="btn btn-xs yellow">
                                    <i class="fa fa-bar-chart"></i> TRAINEE
                                </a>
                                <a href="' . $Custom_url . '/' . base64_encode($workshop_id) . '/' . base64_encode($workshop_type_id) . '" target="_blank"  class="btn btn-xs purple">
                                    <i class="fa fa-bar-chart"></i> INDIVIDUAL
                                </a>';
                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
            //TOP 5 TRAINEE
            $top_five_result = $this->trainer_trainee_workshop_reports_model->top_five_trainee($dtWhere);
            $top_five_trainee_id = "0,";
            $htmlTopFive = '<table class="table table-hover table-light">
                                <thead>
                                    <tr class="uppercase" style="background-color: #e6f2ff;">
                                        <th width="52%">NAME</th>
                                        <th width="12%">PRE SESSION</th>
                                        <th width="12%">POST SESSION</th>
                                        <th width="12%">C.E</th>
                                        <th width="12%">RANK</th>
                                    </tr>
                                </thead><tbody>';
            if (count((array)$top_five_result) > 0) {
                foreach ($top_five_result as $topfive) {
                    $top_five_trainee_id .= $topfive->trainee_id . ",";
                    $tf_ce = ($topfive->post_average == 'NP' || $topfive->pre_average == 'NP' ? 'NP' : $topfive->ce . "%");
                    if ($topfive->post_average == 'NP') {
                        $tf_rank = $topfive->rank;
                    } else {
                        $tf_rank = $topfive->rank;
                    }
                    $htmlTopFive .= '<tr>
                                            <td>' . $topfive->trainee_name . '</td>
                                            <td>' . $topfive->pre_average . '</td>
                                            <td>' . $topfive->post_average . '</td>
                                            <td>' . $tf_ce . '</td>
                                            <td>' . $tf_rank . '</td>
                                        </tr>';
                }
                $htmlTopFive .= '</tbody></table>';
            } else {
                $htmlTopFive .= '<tr>
                                        <td colspan="5">No Participant</td>
                                    </tr>';
                $htmlTopFive .= '</tbody></table>';
            }
            if ($top_five_trainee_id != '') {
                $top_five_trainee_id = substr($top_five_trainee_id, 0, strlen($top_five_trainee_id) - 1);
            }
            //BOTTOM 5 TRAINEE
            $bottom_five_result = $this->trainer_trainee_workshop_reports_model->bottom_five_trainee($dtWhere, $top_five_trainee_id);
            $htmlBottomFive = '<table class="table table-hover table-light">
                                <thead>
                                    <tr class="uppercase" style="background-color: #e6f2ff;">
                                        <th width="52%">NAME</th>
                                        <th width="12%">PRE SESSION</th>
                                        <th width="12%">POST SESSION</th>
                                        <th width="12%">C.E</th>
                                        <th width="12%">RANK</th>
                                    </tr>
                                </thead><tbody>';
            if (count((array)$bottom_five_result) > 0) {
                foreach ($bottom_five_result as $topfive) {
                    $tf_ce = ($topfive->post_average == 'NP' || $topfive->pre_average == 'NP' ? 'NP' : $topfive->ce . "%");
                    if ($topfive->post_average == 'NP') {
                        $tf_rank = $topfive->rank;
                    } else {
                        $tf_rank = $topfive->rank;
                    }
                    $htmlBottomFive .= '<tr>
                                            <td>' . $topfive->trainee_name . '</td>
                                            <td>' . $topfive->pre_average . '</td>
                                            <td>' . $topfive->post_average . '</td>
                                            <td>' . $tf_ce . '</td>
                                            <td>' . $tf_rank . '</td>
                                        </tr>';
                }
                $htmlBottomFive .= '</tbody></table>';
            } else {
                $htmlBottomFive .= '<tr>
                                        <td colspan="5">No Participant</td>
                                    </tr>';
                $htmlBottomFive .= '</tbody></table>';
            }
            $output['top_five_table'] = $htmlTopFive;
            $output['bottom_five_table'] = $htmlBottomFive;
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array(),
                "top_five_table" => '',
                "bottom_five_table" => ''
            );
        }
        echo json_encode($output);
    }

    public function load_wksh_summary()
    {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $login_id  = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) != '' ? $this->input->post('user_id', TRUE) : 0);
        $workshop_id = $this->input->post('workshop_id', TRUE);

        $dtWhere = " WHERE a.company_id=$company_id ";
        if ($workshop_id != "0") {
            $dtWhere .= " AND a.workshop_id= " . $workshop_id;
        }
        if (!$WRightsFlag) {
            $dtWhere .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $dtWhere .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $dtWhere .= " AND a.trainer_id= " . $trainer_id;
        }
        $this->load->model('trainer_trainee_workshop_reports_model');
        $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($workshop_id);
        if ($islive_workshop) {
            $workshop_overall_statistics = $this->trainer_trainee_workshop_reports_model->getLivePrePostWorkshopwise($workshop_id, $trainer_id, $RightsFlag);
        } else {
            $workshop_overall_statistics = $this->trainer_trainee_workshop_reports_model->getPrePostWorkshopwise($workshop_id, $trainer_id, $RightsFlag);
        }
        $trainer_topic_wise_ce_array = $this->trainer_trainee_workshop_reports_model->trainer_topic_wise_ce($dtWhere, $workshop_id);
        $trainerTopicCEGraph = '';
        $dataset1 = [];
        $dataset2 = [];
        $label = [];
        $workshop_name = '';
        $trainer_name = ' All Trainer';
        if ($workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $workshop_name = $WorshopSet->workshop_name;
        }
        if ($trainer_id != "0") {
            $TrainerSet = $this->common_model->get_value('company_users', 'CONCAT(first_name," ",last_name) as name', 'userid=' . $trainer_id);
            $trainer_name = $TrainerSet->name;
        }
        $htmlOverall = '';
        //TOP 5 TRAINEE
        $top_five_result = $this->trainer_trainee_workshop_reports_model->top_five_trainee($dtWhere, $workshop_id);
        $top_five_trainee_id = "0,";
        $htmlTopFive = '<table class="table table-hover table-light">
                        <thead>
                            <tr class="uppercase" style="background-color: #e6f2ff;">
                                <th width="52%">NAME</th>
                                <th width="12%">PRE SESSION</th>
                                <th width="12%">POST SESSION</th>
                                <th width="12%">C.E</th>
                                <th width="12%">RANK</th>
                            </tr>
                        </thead><tbody>';
        if (count((array)$top_five_result) > 0) {
            foreach ($top_five_result as $topfive) {
                $top_five_trainee_id .= $topfive->trainee_id . ",";
                $tf_ce = ($topfive->post_average == 'NP' || $topfive->pre_average == 'NP' ? 'NP' : $topfive->ce . "%");
                if ($topfive->post_average == 'NP') {
                    //$tf_rank = '-';
                    $tf_rank = $topfive->rank;
                } else {
                    $tf_rank = $topfive->rank;
                }
                $htmlTopFive .= '<tr>
                                    <td>' . $topfive->trainee_name . '</td>
                                    <td>' . $topfive->pre_average . '</td>
                                    <td>' . $topfive->post_average . '</td>
                                    <td>' . $tf_ce . '</td>
                                    <td>' . $tf_rank . '</td>
                                </tr>';
            }
            $htmlTopFive .= '</tbody></table>';
        } else {
            $htmlTopFive .= '<tr>
                                <td colspan="5">No Participant</td>
                            </tr>';
            $htmlTopFive .= '</tbody></table>';
        }
        if ($top_five_trainee_id != '') {
            $top_five_trainee_id = substr($top_five_trainee_id, 0, strlen($top_five_trainee_id) - 1);
        }
        //BOTTOM 5 TRAINEE
        $bottom_five_result = $this->trainer_trainee_workshop_reports_model->bottom_five_trainee($dtWhere, $top_five_trainee_id, $workshop_id);
        $htmlBottomFive = '<table class="table table-hover table-light">
                        <thead>
                            <tr class="uppercase" style="background-color: #e6f2ff;">
                                <th width="52%">NAME</th>
                                <th width="12%">PRE SESSION</th>
                                <th width="12%">POST SESSION</th>
                                <th width="12%">C.E</th>
                                <th width="12%">RANK</th>
                            </tr>
                        </thead><tbody>';
        if (count((array)$bottom_five_result) > 0) {
            foreach ($bottom_five_result as $topfive) {
                $tf_ce = ($topfive->post_average == 'NP' || $topfive->pre_average == 'NP' ? 'NP' : $topfive->ce . "%");
                if ($topfive->post_average == 'NP') {
                    //$tf_rank = '-';
                    $tf_rank = $topfive->rank;
                } else {
                    $tf_rank = $topfive->rank;
                }
                $htmlBottomFive .= '<tr>
                                    <td>' . $topfive->trainee_name . '</td>
                                    <td>' . $topfive->pre_average . '</td>
                                    <td>' . $topfive->post_average . '</td>
                                    <td>' . $tf_ce . '</td>
                                    <td>' . $tf_rank . '</td>
                                </tr>';
            }
            $htmlBottomFive .= '</tbody></table>';
        } else {
            $htmlBottomFive .= '<tr>
                                <td colspan="5">No Participant</td>
                            </tr>';
            $htmlBottomFive .= '</tbody></table>';
        }

        if (count((array)$workshop_overall_statistics) > 0) {
            $Pre_avg = $workshop_overall_statistics->pre_average;
            $Post_avg = $workshop_overall_statistics->post_average;
            $CE = $Post_avg - $Pre_avg . '%';
            if ($Pre_avg == 0) {
                $Pre_avg = "NP";
                $CE = "NP";
            } else {
                $Pre_avg .= "%";
            }
            if ($Post_avg == 0) {
                $Post_avg = "NP";
                $CE = "NP";
            } else {
                $Post_avg .= "%";
            }
            $htmlOverall = '
                    <table class="table table-hover table-light">
                        <thead>
                            <tr class="uppercase" style="background-color: #e6f2ff;">
                                <th width="36%">WORKSHOP NAME</th>
                                <th width="12%">PRE SESSION</th>
                                <th width="12%">POST SESSION</th>
                                <th width="12%">C.E</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>' . $workshop_overall_statistics->workshop_name . '</td>
                                <td>' . $Pre_avg . '</td>
                                <td>' . $Post_avg . '</td>
                                <td>' . $CE . '</td>
                            </tr>
                        </tbody>
                    </table>
                    ';

            $htmlOverall .= '<div class="row"><div class="col-lg-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Top 5 Participants</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important"> 
                                        ' . $htmlTopFive . '
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Bottom 5 Participants</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important"> 
                                        ' . $htmlBottomFive . '
                                    </div>
                                </div>
                            </div></div>
                            ';
        }
        if (count((array)$trainer_topic_wise_ce_array) > 0) {
            foreach ($trainer_topic_wise_ce_array as $ttwcea) {
                $ce = $ttwcea->ce;
                $label[] = $ttwcea->topic;
                if ($ce < 0) {
                    $dataset2[] = $ce;
                    $dataset1[] = '';
                } else {
                    $dataset1[] = $ce;
                    $dataset2[] = '';
                }
            }
        }
        $trainerTopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; '>
                                <div id='topic_wise_ce' style='height:" . (count((array)$label) > 5 ? '500' : '400') . "px'></div>
                            </div>
                            <div class='portlet-body' style='padding: 0px !important' id='overall_ce_panel'> 
                                " . $htmlOverall . "
                            </div>

                        <script>
                            $(document).ready(function () {
                                var chartData1 =" . json_encode($dataset1, JSON_NUMERIC_CHECK) . "
                                var chartData2 =" . json_encode($dataset2, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_wise_ce', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: '',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($label) . ",
                                        title: {
                                            text: 'Topic Wise'
                                        },
                                        
        scrollbar: {
            enabled: false
        },
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Overall C.E',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        },
                                        
                                    },
                                    tooltip: {
                                        valueSuffix: '%'
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}%',
                                                allowOverlap: true,
                                                crop: false,
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color:'black',
                                                    fontSize: '10px',
                                                }
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                                name: 'Positive C.E',
                                                data: chartData1,
                                                " . (count((array)$label) > 10 ? '' : 'pointWidth: 28,') . "
                                                stacking: 'normal',
                                                color:'#ffc000',
                                            },
                                            {
                                                name: 'Negative C.E',
                                                data: chartData2,
                                                " . (count((array)$label) > 10 ? '' : 'pointWidth: 28,') . "
                                                stacking: 'normal',
                                                color:'#FF0000',
                                                dataLabels: {
                                                    style: {
                                                        fontWeight: 'normal',
                                                        textOutline: '0',
                                                        color: 'black',
                                                        'fontSize': '12px',
                                                    },
                                                    formatter: function () {
                                                        if (this.y < 0) {
                                                            return this.y;
                                                        }
                                                    },
                                                    enabled: true,
                                                    overflow: 'none'
                                                }
                                            }  
                                        ]
                                });
                            });
                        </script>";

        $data['Modal_Title'] = "Summary Report :- Workshop Name : " . $workshop_name . ". Trainer Name: " . $trainer_name;
        $data['summary_report'] = $trainerTopicCEGraph;
        echo json_encode($data);
    }

    public function load_wksh_detail()
    {
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id  = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) != '' ? $this->input->post('user_id', TRUE) : 0);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $this->load->model('trainer_trainee_workshop_reports_model');

        $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($workshop_id);
        $PreSessionLive = false;
        $PostSessionLive = false;
        if ($islive_workshop) {
            $PostSessionLive = true;
            $trainee_result = $this->common_model->get_value('trainee_result', 'workshop_id', 'workshop_id=' . $workshop_id);
            if (count((array)$trainee_result) == 0) {
                $PreSessionLive = true;
            }
            $workshop_overall_statistics = $this->trainer_trainee_workshop_reports_model->getLivePrePostWorkshopwise($workshop_id, $trainer_id, $RightsFlag);
        } else {
            $workshop_overall_statistics = $this->trainer_trainee_workshop_reports_model->getPrePostWorkshopwise($workshop_id, $trainer_id, $RightsFlag);
        }
        $workshop_name = '';
        $trainer_name = ' All Trainer';
        if ($workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $workshop_name = $WorshopSet->workshop_name;
        }
        if ($trainer_id != "0") {
            $TrainerSet = $this->common_model->get_value('company_users', 'CONCAT(first_name," ",last_name) as name', 'userid=' . $trainer_id);
            $trainer_name = $TrainerSet->name;
        }
        $trainer_topic_wise_ce_array = $this->trainer_trainee_workshop_reports_model->trainer_topic_subtopic_wise_ce($RightsFlag, $islive_workshop, $trainer_id, $workshop_id);


        $histogramWkshPreGraph = '';
        $histogramWkshPostGraph = '';
        $histogramWkshTopicPreGraph = '';
        $histogramWkshTopicPostGraph = '';
        $wksh_dataset_pre = [];
        $wksh_label_pre = [];
        $wksh_dataset_post = [];
        $wksh_Topicdataset_pre = [];
        $wksh_Topicdataset_post = [];
        $wksh_label_post = [];
        $trainerTopicSubtopicCEGraph = '';
        $dataset1 = [];
        $dataset2 = [];
        $dataset3 = [];
        $dataset4 = [];
        $label = [];
        $htmlOverall = '';
        if (count((array)$workshop_overall_statistics) > 0) {
            $Pre_avg = $workshop_overall_statistics->pre_average;
            $Post_avg = $workshop_overall_statistics->post_average;
            $CE = $Post_avg - $Pre_avg . '%';
            if ($Pre_avg == 0) {
                $Pre_avg = "NP";
                $CE = "NP";
            } else {
                $Pre_avg .= "%";
            }
            if ($Post_avg == 0) {
                $Post_avg = "NP";
                $CE = "NP";
            } else {
                $Post_avg .= "%";
            }
            $htmlOverall = '<table class="table table-hover table-light">
                        <thead>
                            <tr class="uppercase" style="background-color: #e6f2ff;">
                                <th width="36%">WORKSHOP NAME</th>
                                <th width="12%">PRE SESSION</th>
                                <th width="12%">POST SESSION</th>
                                <th width="12%">C.E</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>' . $workshop_overall_statistics->workshop_name . '</td>
                                <td>' . $Pre_avg . '</td>
                                <td>' . $Post_avg . '</td>
                                <td>' . $CE . '</td>
                            </tr>
                        </tbody>
                    </table>';
        }
        if (count((array)$trainer_topic_wise_ce_array) > 0) {
            foreach ($trainer_topic_wise_ce_array as $ttwcea) {
                $topic_name = $ttwcea->topic;
                $subtopic_name = $ttwcea->subtopic;
                $ce = $ttwcea->ce;
                $label[] = $topic_name . ($subtopic_name != 'No sub-Topic' ? '-' . $subtopic_name : '');
                $dataset1[] = $ttwcea->pre_accuracy;
                $dataset2[] = $ttwcea->post_accuracy;
                if ($ce < 0) {
                    $dataset4[] = $ce;
                    $dataset3[] = '';
                } else {
                    $dataset3[] = $ce;
                    $dataset4[] = '';
                }
            }
        }
        $histogram_pre = $this->trainer_trainee_workshop_reports_model->wksh_trainer_histogram($RightsFlag, $PreSessionLive, $trainer_id, $workshop_id, 'PRE');
        foreach ($histogram_pre as $range) {
            $wksh_label_pre[] = $range->from_range . "-" . $range->to_range . "%";
            $wksh_dataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_post = $this->trainer_trainee_workshop_reports_model->wksh_trainer_histogram($RightsFlag, $PostSessionLive, $trainer_id, $workshop_id, 'POST');
        foreach ($histogram_post as $range) {
            $wksh_label_post[] = $range->from_range . "-" . $range->to_range . "%";
            $wksh_dataset_post[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_TopicCount_pre = $this->trainer_trainee_workshop_reports_model->wksh_topic_histogram($RightsFlag, $PreSessionLive, $trainer_id, $workshop_id, 'PRE');
        foreach ($histogram_TopicCount_pre as $range) {
            $wksh_Topicdataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_TopicCount_post = $this->trainer_trainee_workshop_reports_model->wksh_topic_histogram($RightsFlag, $PostSessionLive, $trainer_id, $workshop_id, 'POST');
        foreach ($histogram_TopicCount_post as $range) {
            $wksh_Topicdataset_post[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogramWkshTopicPreGraph = "<div id='container'>
                                <div id='wksh_Topichistogram_pre' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var histogramDataPre =" . json_encode($wksh_Topicdataset_pre, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('wksh_Topichistogram_pre', {
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: ' ',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
            }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($wksh_label_pre) . ",
                                        title: {
                                            text: 'Pre Compentency Range'
        }
                                    },
                                    yAxis: {
                                        allowDecimals: false,
                                        title: {
                                            text: 'No. of Topic',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        }
                                    },
                                    tooltip: {
                                        valueSuffix: ''
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}'
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Pre Compentency',
                                            data: histogramDataPre," .
            (count((array)$wksh_label_pre) > 10 ? '' : 'pointWidth: 28,')
            . "color: '#0070c0',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        }]
                                });
                            });
                        </script>";


        $histogramWkshTopicPostGraph = "<div id='container' >
                                <div id='wksh_Topichistogram_post' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var histogramDataPre =" . json_encode($wksh_Topicdataset_post, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('wksh_Topichistogram_post', {
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: ' ',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($wksh_label_post) . ",
                                        title: {
                                            text: 'Post Competency Range'
                                        }
                                    },
                                    yAxis: {
                                        allowDecimals: false,
                                        title: {
                                            text: 'No. of Topic',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        }
                                    },
                                    tooltip: {
                                        valueSuffix: ''
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}'
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Post Competency',
                                            data: histogramDataPre," .
            (count((array)$wksh_label_post) > 10 ? '' : 'pointWidth: 28,')
            . "color: '#00ffcc',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        }]
                                });
                            });
                        </script>";

        $histogramWkshPreGraph = "<div id='container' >
                                <div id='wksh_histogram_pre' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var histogramDataPre =" . json_encode($wksh_dataset_pre, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('wksh_histogram_pre', {
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: '',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($wksh_label_pre) . ",
                                        title: {
                                            text: 'Pre Competency Range'
                                        }
                                    },
                                    yAxis: {
                                        allowDecimals: false,
                                        title: {
                                            text: 'No. of Trainee',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        }
                                    },
                                    tooltip: {
                                        valueSuffix: ''
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}'
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Pre Compentency',
                                            data: histogramDataPre," .
            (count((array)$wksh_label_pre) > 10 ? '' : 'pointWidth: 28,')
            . "color: '#0070c0',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        }]
                                });
                            });
                        </script>";

        $histogramWkshPostGraph = "<div id='container' >
                                <div id='wksh_histogram_post' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var histogramDataPost =" . json_encode($wksh_dataset_post, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('wksh_histogram_post', {
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: ' ',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($wksh_label_post) . ",
                                        title: {
                                            text: 'Post Compentency Range'
                                        }
                                    },
                                    yAxis: {
                                        allowDecimals: false,
                                        title: {
                                            text: 'No. of Trainee',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        }
                                    },
                                    tooltip: {
                                        valueSuffix: ''
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}'
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Post Compentency',
                                            data: histogramDataPost," .
            (count((array)$wksh_label_post) > 10 ? '' : 'pointWidth: 28,')
            . "color: '#00ffcc',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        }]
                                });
                            });
                        </script>";
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:500px; overflow-y:auto; '>
                                <div id='topic_subtopic_ce' style='height:" . (count((array)$label) > 5 ? '600' : '400') . "px'></div>
                            </div>
                            <div class='portlet-body' style='padding: 0px !important' id='overall_ce_panel'> 
                                " . $htmlOverall . "
                            </div>
                            <div class='row'>
                                <div class='col-lg-6 col-xs-12 col-sm-12'>
                                    <div class='portlet light bordered' style='padding: 12px 20px 10px !important;'>
                                        <div class='portlet-title potrait-title-mar'>
                                            <div class='caption'>
                                                <i class='icon-bar-chart font-dark hide'></i>
                                                <span class='caption-subject font-dark bold uppercase'>HISTOGRAM TRAINEE WISE - PRE</span>
                                            </div>
                                        </div>
                                        <div class='portlet-body' style='padding: 0px !important' id='histogram_trainee_pre'>
                                        " . $histogramWkshPreGraph . "
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-lg-6 col-xs-12 col-sm-12'>
                                    <div class='portlet light bordered' style='padding: 12px 20px 10px !important;'>
                                        <div class='portlet-title potrait-title-mar'>
                                            <div class='caption'>
                                                <i class='icon-bar-chart font-dark hide'></i>
                                                <span class='caption-subject font-dark bold uppercase'>HISTOGRAM TRAINEE WISE - POST</span>
                                            </div>
                                        </div>
                                        <div class='portlet-body' style='padding: 0px !important' id='histogram_trainee_post'> 
                                        " . $histogramWkshPostGraph . "
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-lg-6 col-xs-12 col-sm-12'>
                                    <div class='portlet light bordered' style='padding: 12px 20px 10px !important;'>
                                        <div class='portlet-title potrait-title-mar'>
                                            <div class='caption'>
                                                <i class='icon-bar-chart font-dark hide'></i>
                                                <span class='caption-subject font-dark bold uppercase'>HISTOGRAM TOPIC WISE - PRE</span>
                                            </div>
                                        </div>
                                        <div class='portlet-body' style='padding: 0px !important' id='histogram_topic_pre'>
                                        " . $histogramWkshTopicPreGraph . "
                                        </div>
                                    </div>
                                </div>
                            
                                <div class='col-lg-6 col-xs-12 col-sm-12'>
                                    <div class='portlet light bordered' style='padding: 12px 20px 10px !important;'>
                                        <div class='portlet-title potrait-title-mar'>
                                            <div class='caption'>
                                                <i class='icon-bar-chart font-dark hide'></i>
                                                <span class='caption-subject font-dark bold uppercase'>HISTOGRAM TOPIC WISE - POST</span>
                                            </div>
                                        </div>
                                        <div class='portlet-body' style='padding: 0px !important' id='histogram_topic_post'> 
                                        " . $histogramWkshTopicPostGraph . "
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        <script>
                            $(document).ready(function () {
                                var chartData1 =" . json_encode($dataset1, JSON_NUMERIC_CHECK) . "
                                var chartData2 =" . json_encode($dataset2, JSON_NUMERIC_CHECK) . "
                                var chartData3 =" . json_encode($dataset3, JSON_NUMERIC_CHECK) . "
                                var chartData4 =" . json_encode($dataset4, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_subtopic_ce', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: '',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($label) . ",
                                        title: {
                                            text: 'Topic + Sub Topic Wise'
                                        },
                                        scrollbar: {
                                            enabled: false
                                        },
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Overall C.E',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        },
                                    },
                                    tooltip: {
                                        valueSuffix: '%'
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}%',
                                                allowOverlap: true,
                                                crop: false,
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color:'black',
                                                    fontSize: '10px',
                                                }
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Pre',
                                            data: chartData1,
                                            color:'#0070c0'
                                        }, {
                                            name: 'Post',
                                            data: chartData2,
                                            color:'#00ffcc'
                                        }, {
                                            name: 'Positive C.E',
                                            data: chartData3,
                                            stacking: 'normal',
                                            color:'#ffc000',
                                        },
                                        {
                                            name: 'Negative C.E',
                                            data: chartData4,
                                            stacking: 'normal',
                                            color:'#FF0000',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                },
                                                formatter: function () {
                                                    if (this.y < 0) {
                                                        return this.y;
                                                    }
                                                },
                                                enabled: true,
                                                overflow: 'none'
                                            }
                                        }   
                                    ]
                                });
                            });
                        </script>";
        $data['Modal_Title'] = "Details Report :- Workshop Name : " . $workshop_name . ". Trainer Name: " . $trainer_name;
        $data['detail_report'] = $trainerTopicSubtopicCEGraph;
        echo json_encode($data);
    }

    public function load_wksh_trainee()
    {
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id  = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) != '' ? $this->input->post('user_id', TRUE) : 0);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $this->load->model('trainer_trainee_workshop_reports_model');
        $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($workshop_id);
        if ($islive_workshop) {
            $trainer_topic_wise_ce_array = $this->trainer_trainee_workshop_reports_model->getLivePrePostData($workshop_id, '', $trainer_id, $RightsFlag);
        } else {
            $trainer_topic_wise_ce_array = $this->trainer_trainee_workshop_reports_model->getPrePostData($workshop_id, '', $trainer_id, $RightsFlag);
        }
        $workshop_name = '';
        $trainer_name = ' All Trainer';
        if ($workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $workshop_name = $WorshopSet->workshop_name;
        }
        if ($trainer_id != "0") {
            $TrainerSet = $this->common_model->get_value('company_users', 'CONCAT(first_name," ",last_name) as name', 'userid=' . $trainer_id);
            $trainer_name = $TrainerSet->name;
        }
        $dataset1 = [];
        $dataset2 = [];
        $dataset3 = [];
        $dataset4 = [];
        $label = [];
        $lcOption = "<option value=''> select</option>";
        if (count((array)$trainer_topic_wise_ce_array) > 0) {
            foreach ($trainer_topic_wise_ce_array as $ttwcea) {
                $user_id = $ttwcea->trainee_id;
                $user_name = $ttwcea->trainee_name;
                $pre_average_accuracy = $ttwcea->pre_avg;
                $post_average_accuracy = $ttwcea->post_avg;
                if ($ttwcea->pre_average == 'Not Played' || $ttwcea->post_average == 'Not Played') {
                    continue;
                }
                $ce = $ttwcea->ce;
                $label[] = $user_name;
                $dataset1[] = $pre_average_accuracy;
                $dataset2[] = $post_average_accuracy;
                if ($ce < 0) {
                    $dataset4[] = $ce;
                    $dataset3[] = '';
                } else {
                    $dataset3[] = $ce;
                    $dataset4[] = '';
                }
                $lcOption .= '<option value="' . $user_id . '">' . $user_name . '</option>';
            }
        }
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; ' >
                                <div id='topic_subtopic_ce' style='height:" . (count((array)$label) > 5 ? '1000' : '500') . "px'></div>
                            </div>";
        $trainerTopicSubtopicCEGraph .= '<div class="row margin-bottom-10"><div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Trainee wise :</label>
                                        <div class="col-md-8" style="padding:0px;">
                                            <select id="pop_trainee_id" name="pop_trainee_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getTrainnewiseData(' . $workshop_id . ');">
                                                ' . $lcOption . '
                                            </select>
                                        </div>
                                    </div>
                                </div></div>
                                <div class="row margin-bottom-10"><div class="col-md-12">
                                <div id="container2"></div>
                                </div></div>';
        $trainerTopicSubtopicCEGraph .= "
                        <script>
                            $(document).ready(function () {
                                var chartData1 =" . json_encode($dataset1, JSON_NUMERIC_CHECK) . "
                                var chartData2 =" . json_encode($dataset2, JSON_NUMERIC_CHECK) . "
                                var chartData3 =" . json_encode($dataset3, JSON_NUMERIC_CHECK) . "
                                var chartData4 =" . json_encode($dataset4, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_subtopic_ce', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: 'Workshop Name : " . $workshop_name . "',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: 'Trainer Name: " . $trainer_name . "'
                                    },
                                    xAxis: {
                                        categories:" . json_encode($label) . ",
                                        title: {
                                            text: 'Trainee Name'
                                        },
                                        scrollbar: {
                                            enabled: false
                                        }
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Overall C.E',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        },
                                    },
                                    tooltip: {
                                        valueSuffix: '%'
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}%',
                                                allowOverlap: true,
                                                crop: false,
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color:'black',
                                                    fontSize: '10px',
                                                }
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Pre',
                                            data: chartData1,
                                            color:'#0070c0'
                                        }, {
                                            name: 'Post',
                                            data: chartData2,
                                            color:'#00ffcc'
                                        }, {
                                            name: 'Positive C.E',
                                            data: chartData3,
                                            stacking: 'normal',
                                            color:'#ffc000',
                                        },
                                        {
                                            name: 'Negative C.E',
                                            data: chartData4,
                                            stacking: 'normal',
                                            color:'#FF0000',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                },
                                                formatter: function () {
                                                    if (this.y < 0) {
                                                        return this.y;
                                                    }
                                                },
                                                enabled: true,
                                                overflow: 'none'
                                            }
                                        }   
                                    ]
                                });
                            });
                        </script>";

        $data['trainee_report'] = $trainerTopicSubtopicCEGraph;
        $data['Modal_Title'] = "Trainee Report :- Workshop Name : " . $workshop_name . ". Trainer Name: " . $trainer_name;
        echo json_encode($data);
    }

    public function gettraineewise_topic()
    {
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $this->load->model('trainer_trainee_workshop_reports_model');
        $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLive($workshop_id);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $trainer_id = ($this->input->post('user_id', TRUE) != '' ? $this->input->post('user_id', TRUE) : 0);
        if ($workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $workshop_name = $WorshopSet->workshop_name;
        }
        if ($trainee_id != "0") {
            $TraineeSet = $this->common_model->get_value('device_users', 'CONCAT(firstname," ",lastname) as name', 'user_id=' . $trainee_id);
            $trainee_name = $TraineeSet->name;
        }
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] != "" && !$this->mw_session['superaccess']) {
            $Login_id  = $this->mw_session['user_id'];
            $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
            $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
        }
        $trainer_topic_wise_ce_array = $this->trainer_trainee_workshop_reports_model->trainer_topic_subtopic_wise_ce($RightsFlag, $islive_workshop, $trainer_id, $workshop_id, $trainee_id);
        $dataset1 = [];
        $dataset2 = [];
        $dataset3 = [];
        $dataset4 = [];
        $label = [];
        if (count((array)$trainer_topic_wise_ce_array) > 0) {
            foreach ($trainer_topic_wise_ce_array as $ttwcea) {
                $topic_name = $ttwcea->topic;
                $subtopic_name = $ttwcea->subtopic;
                $ce = $ttwcea->ce;
                $label[] = $topic_name . ($subtopic_name != 'No sub-Topic' ? '-' . $subtopic_name : '');
                $dataset1[] = $ttwcea->pre_accuracy;
                $dataset2[] = $ttwcea->post_accuracy;
                if ($ce < 0) {
                    $dataset4[] = $ce;
                    $dataset3[] = '';
                } else {
                    $dataset3[] = $ce;
                    $dataset4[] = '';
                }
            }
        }
        $lcHtml = "<div id='trainee_subtopic_ce' style='height:" . (count((array)$label) > 5 ? '600' : '400') . "px'></div>";
        $lcHtml .= "
                        <script>
                            $(document).ready(function () {
                                var chartData1 =" . json_encode($dataset1, JSON_NUMERIC_CHECK) . "
                                var chartData2 =" . json_encode($dataset2, JSON_NUMERIC_CHECK) . "
                                var chartData3 =" . json_encode($dataset3, JSON_NUMERIC_CHECK) . "
                                var chartData4 =" . json_encode($dataset4, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('trainee_subtopic_ce', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: 'Workshop Name:" . $workshop_name . "',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: 'Trainee Name:" . $trainee_name . "'
                                    },
                                    xAxis: {
                                        categories:" . json_encode($label) . ",
                                        title: {
                                            text: 'Topic-Sub Topic wise'
                                        },
        scrollbar: {
            enabled: false
        },
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Overall C.E',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        },
                                    },
                                    tooltip: {
                                        valueSuffix: '%'
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}%',
                                                allowOverlap: true,
                                                crop: false,
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color:'black',
                                                    fontSize: '10px',
                                                }
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Pre',
                                            data: chartData1,
                                            color:'#0070c0'
                                        }, {
                                            name: 'Post',
                                            data: chartData2,
                                            color:'#00ffcc'
                                        }, {
                                            name: 'Positive C.E',
                                            data: chartData3,
                                            stacking: 'normal',
                                            color:'#ffc000',
                                        },
                                        {
                                            name: 'Negative C.E',
                                            data: chartData4,
                                            stacking: 'normal',
                                            color:'#FF0000',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                },
                                                formatter: function () {
                                                    if (this.y < 0) {
                                                        return this.y;
                                                    }
                                                },
                                                enabled: true,
                                                overflow: 'none'
                                            }
                                        }   
                                    ]
                                });
                            });
                        </script>";
        echo $lcHtml;
    }



















    // Trainer Comperision Start Here ============================================================================================================
    public function ajax_company_trainer_type_comperision()
    {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $Trainer_id = '';
        } else {
            $company_id = $this->mw_session['company_id'];
            $Trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $lcWhere = 'status=1 AND  company_id=' . $company_id;
        if ($RightsFlag) {
            $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
        } else {
            $data['user_array'] = $this->common_model->getUserRightsList($company_id, $Trainer_id);
        }
        if ($WRightsFlag) {
            $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
        } else {
            $data['wksh_type_array'] = $this->common_model->getWTypeRightsList($company_id);
        }
        echo json_encode($data);
    }

    public function ajax_fetch_workshop()
    {
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,workshoprights_type', 'userid=' . $Login_id);
                //$RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = $this->input->post('user_id', TRUE);
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id, $WRightsFlag, $trainer_id, '0', $workshop_type_id);

        echo json_encode($data);
    }
    public function load_workshop_table($cnt)
    {
        //WORKSHOP ACCURACY TABLE
        //         $cnt = $this->input->post('cnt', TRUE);
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id  = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $this->load->model('trainer_trainee_workshop_reports_model');
        //$this->trainer_trainee_workshop_reports_model->SynchTraineeData($company_id);
        $trainer_id = $this->input->post('user_id', TRUE);
        $workshop_type_id = ($this->input->post('workshop_type_id', TRUE) != '' ? $this->input->post('workshop_type_id', TRUE) : 0);
        $Workshop_id = $this->input->post('workshop_id', TRUE);
        $PlayedStatus = $this->trainer_trainee_workshop_reports_model->isWorkshopPlayed($Workshop_id);
        $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($Workshop_id);
        $workshop_statistics = $this->trainer_trainee_workshop_reports_model->workshop_statistics_trainer_comperision(
            $company_id,
            $trainer_id,
            $Workshop_id,
            $workshop_type_id,
            $PlayedStatus['PreFlag'],
            $PlayedStatus['PostFlag'],
            $islive_workshop
        );
        $html = '';
        $comparison_html = '';
        $ExportRights = $this->acces_management;
        $trainer_name = "All Trainer";
        if (count((array)$workshop_statistics) > 0) {
            foreach ($workshop_statistics as $wksh) {
                $company_id = $wksh->company_id;
                //$workshop_id = $wksh->workshop_id;
                $workshop_name = $wksh->workshop_name;
                if ($trainer_id != "0") {
                    $trainer_name = $wksh->trainer_name;
                }
                if (!$PlayedStatus['PreFlag']  || $wksh->pre_accuracy == "") {
                    $pre_average_accuracy = 'Not Played';
                    $ce = 'Not Played';
                } else {
                    $pre_average_accuracy = $wksh->pre_accuracy . '%';
                }
                $ce = $wksh->ce . '%';
                if (!$PlayedStatus['PostFlag'] || $wksh->post_accuracy == "") {
                    $post_average_accuracy = 'Not Played';
                    $ce = 'Not Played';
                } else {
                    $post_average_accuracy = $wksh->post_accuracy . '%';
                }
                //TRAINEE COMPARISON TABLE
                $trainee_comparison = $this->trainer_trainee_workshop_reports_model->trainee_comparison($company_id, $islive_workshop, $trainer_id, $Workshop_id, $workshop_type_id);
                if (count((array)$trainee_comparison) > 0) {
                    $comparison_html .= '<div id="tdata' . $cnt . '" class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">' . $workshop_name . '</span>
                                            <h5>' . $trainer_name . '</h5>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:void(0)" class="remove" onclick="remove_workshop(' . $cnt . ');"> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <table class="table table-hover table-light scroll" width="400px">
                                            <thead>
                                                <tr class="uppercase">
                                                    <th>TRAINEE NAME</th>
                                                    <th >PRE</th>
                                                    <th >POST</th>
                                                    <th >C.E</th>
                                                    <th width="12%">RESPONSE TIME</th>
                                                    <th >RANK</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                    foreach ($trainee_comparison as $compr) {
                        $comp_trainee_name = $compr->trainee_name;

                        $comp_pre_average_accuracy = ($compr->pre_average == "NP" ? "Not Played" : $compr->pre_average);
                        $comp_post_average_accuracy = ($compr->post_average == "NP" ? "Not Played" : $compr->post_average);

                        $comp_ce = ($compr->pre_average == 'NP' || $compr->post_average == 'NP' ? "Not Played" : $compr->ce . "%");
                        $comp_rank = $compr->rank;

                        $comparison_html .= '<tr>
                                                <td>' . $comp_trainee_name . '</td>
                                                <td>' . $comp_pre_average_accuracy . '</td>
                                                <td>' . $comp_post_average_accuracy . '</td>
                                                <td>' . $comp_ce . '</td>
                                                <td width="12%">' . $compr->response_time . '</td>
                                                <td>' . $comp_rank . '</td>
                                            </tr>';
                    }
                    $comparison_html .= '</tbody></table></div></div></div>';
                }

                $html .= '<tr id="rdata' . $cnt . '">
                            <td style="width: 23.5%;">' . $workshop_name . '</td>
                            <td style="width: 15.5%;">' . $trainer_name . '</td>
                            <td style="width: 11.5%;">' . $pre_average_accuracy . '</td>
                            <td style="width: 11.5%;">' . $post_average_accuracy . '</td>
                            <td style="width: 11.5%;">' . $ce . '</td>
                            <td style="width: 28%;">';
                if ($ExportRights->allow_export) {
                    $html .= ' <a  href="' .  base_url() . 'trainer_comparison/export_workshop/' . $company_id . '/' . $Workshop_id . '/' . $trainer_id . '" class="btn btn-xs green">
                                        <i class="fa fa-file-excel-o"></i> Export
                                    </a>';
                }
                $html .= '<a  href="javascript:void(0)" onclick="remove_workshop(' . $cnt . ');" class="btn btn-xs red">
                                    <i class="fa fa-remove"></i> Remove
                                </a>
                                <a style="width:200px;float:right;text-decoration:none;display: block;">&nbsp;</a>
                            </td>
                        </tr>';
            }
        }
        $data['wksh_list'] = $html;
        $data['comparison_panels'] = $comparison_html;
        echo json_encode($data);
    }
    public function export_workshop($company_id, $Workshop_id, $trainer_id = "")
    {
        if ($company_id == "" || $Workshop_id == "") {
            redirect('trainer_comparison');
        }
        $ExportRights = $this->acces_management;
        if (!$ExportRights->allow_export) {
            redirect('trainer_comparison');
        }
        $workshop_type_id = '0';
        $PlayedStatus = $this->trainer_trainee_workshop_reports_model->isWorkshopPlayed($Workshop_id);
        $islive_workshop = ''; //$this->trainer_trainee_workshop_reports_model->isWorkshopLive($Workshop_id);
        $workshop_statistics = $this->trainer_trainee_workshop_reports_model->workshop_statistics(
            $company_id,
            $trainer_id,
            $Workshop_id,
            $workshop_type_id,
            $PlayedStatus['PreFlag'],
            $PlayedStatus['PostFlag'],
            $islive_workshop
        );
        if (count((array)$workshop_statistics) > 0) {
            $trainee_comparison = $this->trainer_trainee_workshop_reports_model->trainee_comparison($company_id, $islive_workshop, $trainer_id, $Workshop_id, $workshop_type_id);
            $tariner_name = 'All';
            if ($trainer_id != "0") {
                $Trainer_rowset = $this->common_model->get_value('company_users', "CONCAT(first_name,' ',last_name) as name ", 'userid=' . $trainer_id);
                $tariner_name = $Trainer_rowset->name;
            }
            $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $Workshop_id);
            $workshop_name = $Workshop_rowset->workshop_name;
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Trainer Name :" . $tariner_name)
                ->setCellValue('A2', "Workshop Name :" . $workshop_name)
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Trainee Name")
                ->setCellValue('C3', "Trainee Region")
                ->setCellValue('D3', "PRE")
                ->setCellValue('E3', "POST")
                ->setCellValue('F3', "C.E")
                ->setCellValue('G3', "RESPONSE TIME")
                ->setCellValue('H3', "RANK");
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleArray_header);

            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $i = 3;
            foreach ($trainee_comparison as $compr) {
                $i++;
                $comp_pre_average_accuracy = ($compr->pre_average == "NP" ? "Not Played" : $compr->pre_average);
                $comp_post_average_accuracy = ($compr->post_average == "NP" ? "Not Played" : $compr->post_average);
                $comp_ce = ($compr->pre_average == 'NP' || $compr->post_average == 'NP' ? "Not Played" : $compr->ce . "%");
                $comp_rank = $compr->rank;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $compr->trainee_id)
                    ->setCellValue("B$i", $compr->trainee_name)
                    ->setCellValue("C$i", $compr->trainee_region)
                    ->setCellValue("D$i", $comp_pre_average_accuracy)
                    ->setCellValue("E$i", $comp_post_average_accuracy)
                    ->setCellValue("F$i", $comp_ce)
                    ->setCellValue("G$i", $compr->response_time)
                    ->setCellValue("H$i", $comp_rank);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainer Comparison Reports.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
        }
    }









    // Trainer Accuracy Start Here 
    // public function ajax_company_trainer_type() {
    //     $RightsFlag=1;
    //     $WRightsFlag=1;
    //     if ($this->mw_session['company_id'] == "") {
    //         $company_id = $this->input->post('company_id', TRUE);
    //         $Trainer_id = '';
    //     } else {
    //         $company_id = $this->mw_session['company_id'];
    //          $Trainer_id = $this->mw_session['user_id'];
    //         if(!$this->mw_session['superaccess']){
    //             $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Trainer_id);
    //             $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
    //             $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
    //         }
    //     }
    //     $lcWhere = 'status=1 AND login_type=1 AND company_id=' . $company_id;
    //     if ($Trainer_id != "") {
    //         if ($RightsFlag) {
    //             $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
    //         } else {
    //             $data['user_array'] = $this->common_model->getUserRightsList($company_id, $Trainer_id);
    //         }
    //         if ($WRightsFlag) {
    //             $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
    //             $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1); 
    //         } else {
    //             $data['wksh_type_array'] = $this->common_model->getWTypeRightsList($company_id);
    //             $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,0);
    //         }
    //     } else {
    //         $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
    //         $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
    //         $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1);
    //     }
    //     echo json_encode($data);
    // }
    // public function ajax_fetch_workshop() {
    //     $WRightsFlag=1;
    //     $Login_id =$this->mw_session['user_id'];
    //     if ($this->mw_session['company_id'] == "") {
    //         $company_id = $this->input->post('company_id', TRUE);
    //     } else {
    //         $company_id = $this->mw_session['company_id'];
    //         if(!$this->mw_session['superaccess']){
    //             $Rowset = $this->common_model->get_value('company_users', 'company_id,workshoprights_type', 'userid=' . $Login_id);
    //             //$RightsFlag=($Rowset->userrights_type==1 ? 1:0);
    //             $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
    //         }
    //     }
    //     $trainer_id = $this->input->post('user_id', TRUE);
    //     $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
    //     $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,$WRightsFlag, $trainer_id,'0', $workshop_type_id);
    //     echo json_encode($data);
    // }
    public function ajax_fetch_trainee()
    {
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $workshop_session = $this->input->post('workshop_session', TRUE);
        $trainee_array = $this->trainer_trainee_workshop_reports_model->getTrainee($workshop_id, $workshop_session);
        $lchtml = '<option value="0">All Select</option>';
        if (count((array)$trainee_array) > 0) {
            foreach ($trainee_array as $value) {
                $lchtml .= '<option value="' . $value->user_id . '">' . $value->username . '</option>';
            }
        }
        echo $lchtml;
    }
    public function load_report()
    {
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id  = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $this->load->model('trainer_trainee_workshop_reports_model');
        $this->trainer_trainee_workshop_reports_model->SynchTraineeData($company_id);
        $trainer_id = ($this->input->post('user_id', TRUE) != '' ? $this->input->post('user_id', TRUE) : 0);
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);

        $workshop_session = $this->input->post('workshop_session', TRUE);
        $trainee_id = ($this->input->post('trainee_id', TRUE) != '' ? $this->input->post('trainee_id', TRUE) : 0);
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);

        $isWorkshopLive = $this->trainer_trainee_workshop_reports_model->isWorkshopLive($workshop_id, $workshop_session);

        $TraineeData = $this->trainer_trainee_workshop_reports_model->get_traineeAccuracy($RightsFlag, $trainee_id, $trainer_id, $workshop_id, $workshop_session, $isWorkshopLive, $trainee_region_id);
        //TOP AND BOTTOM 5 TRAINEE
        $top_five_trainee = array();
        $topfivetrainee = '';

        $trainee_top_five_array = $this->trainer_trainee_workshop_reports_model->top_five_trainee_accuracy($RightsFlag, $trainee_id, $trainer_id, $workshop_id, $workshop_session, $isWorkshopLive, $trainee_region_id);
        $trainee_top_five_html = '';
        if (count((array)$trainee_top_five_array) > 0) {
            foreach ($trainee_top_five_array as $trainee_top) {
                $top_five_trainee[] = $trainee_top->trainee_id;
                $trainee_top_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $trainee_top->trainee_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . ($trainee_top->accuracy == "" ? "Not Played" : $trainee_top->accuracy . "%") . '</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $trainee_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        if (count((array)$top_five_trainee) > 0) {
            $topfivetrainee = implode(',', $top_five_trainee);
        }
        $trainee_bottom_five_array = $this->trainer_trainee_workshop_reports_model->bottom_five_trainee_accuracy($RightsFlag, $trainee_id, $trainer_id, $workshop_id, $workshop_session, $topfivetrainee, $isWorkshopLive, $trainee_region_id);
        $trainee_bottom_five_html = '';
        if (count((array)$trainee_bottom_five_array) > 0) {
            foreach ($trainee_bottom_five_array as $trainee_bottom) {
                $trainee_bottom_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $trainee_bottom->trainee_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . ($trainee_bottom->accuracy == "" ? "Not Played" : $trainee_bottom->accuracy . "%") . '</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $trainee_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        $topic_subtopic_array = $this->trainer_trainee_workshop_reports_model->get_PrepostAccuracy($workshop_id, $trainee_id, $workshop_session, $trainer_id, $RightsFlag, $isWorkshopLive, $trainee_region_id);
        if (count((array)$topic_subtopic_array) > 0) {
            foreach ($topic_subtopic_array as $tst) {
                $subtopic_name = $tst->subtopic;
                $label[] = $tst->topic  . ($subtopic_name == "No sub-Topic" ? '' : '-' . $subtopic_name);
                $dataset[] = $tst->accuracy;
            }
        } else {
            $dataset = [];
            $label = [];
        }
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; '>
                                <div id='topic_subtopic' style='height:" . (count((array)$label) > 5 ? '600' : '400') . "px'></div>
                            </div>
                        <script>
                            $(document).ready(function () {
                                var chartData1 =" . json_encode($dataset, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_subtopic', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: '',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($label) . ",
                                        title: {
                                            text: 'Topic + Sub Topic Wise'
                                        },
                                        scrollbar: {
                                            enabled: false
                                        },
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Overall Accuracy',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        },
                                    },
                                    tooltip: {
                                        valueSuffix: '%'
                                    },
                                    legend: {
                                        enabled: false
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}%',
                                                allowOverlap: true,
                                                crop: false,
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color:'black',
                                                    fontSize: '10px',
                                                }
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Overall Accuracy',
                                            data: chartData1,
                                            " . (count((array)$label) > 10 ? '' : 'pointWidth: 28,') . "
                                            stacking: 'normal',
                                            color:'#0070c0',
                                        }
                                    ]
                                });
                            });
                        </script>";
        $data['topic_subtopic_chart'] = $trainerTopicSubtopicCEGraph;



        $html = '';
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $wksh) {
                $html .= '<tr>
                            <td>' . $wksh->trainee_name . '</td>
                            <td>' . $wksh->played_questions . '</td>
                            <td>' . $wksh->correct . '</td>
                            <td>' . ($wksh->played_questions - $wksh->correct) . '</td>
                            <td>' . ($wksh->accuracy == "" ? "Not Played" : $wksh->accuracy . "%") . '</td>
                            <td>' . $wksh->rank . '</td>
                            <td>' . ($wksh->played_questions > 0 ? $wksh->status : 'Not Attended') . '</td>
                        </tr>';
            }
        } else {
            $html .= '<tr class="tr-background">
                        <td colspan="7" class="wksh-td">No Records Found</td>
                    </tr>';
        }

        $data['trainee_top_five_table'] = $trainee_top_five_html;
        $data['trainee_bottom_five_table'] = $trainee_bottom_five_html;
        $data['wksh_list'] = $html;
        echo json_encode($data);
    }
    public function export_workshop_trainer_accuracy()
    {
        $ExportRights = $this->acces_management;
        if (!$ExportRights->allow_export) {
            redirect('trainer_accuracy');
        }
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id  = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = $this->input->post('user_id', TRUE);
        //$workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $workshop_session = $this->input->post('workshop_session', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $isWorkshopLive = $this->trainer_trainee_workshop_reports_model->isWorkshopLive($workshop_id, $workshop_session);
        $TraineeData = $this->trainer_trainee_workshop_reports_model->get_traineeAccuracy($RightsFlag, $trainee_id, $trainer_id, $workshop_id, $workshop_session, $isWorkshopLive, $trainee_region_id);
        $tariner_name = 'All';
        if ($trainer_id != "0") {
            $Trainer_rowset = $this->common_model->get_value('company_users', "CONCAT(first_name,' ',last_name) as name ", 'userid=' . $trainer_id);
            $tariner_name = $Trainer_rowset->name;
        }
        $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $workshop_id);
        $workshop_name = $Workshop_rowset->workshop_name;
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', "Workshop Name :" . $workshop_name)
            ->setCellValue('A2', "Workshop Session :" . $workshop_session)
            ->setCellValue('A3', "Trainer :" . $tariner_name)
            ->setCellValue('A4', "TRAINEE ID")
            ->setCellValue('B4', "TRAINEE NAME")
            ->setCellValue('C4', "TRAINEE REGION")
            ->setCellValue('D4', "TOTAL PLAYED")
            ->setCellValue('E4', "CORRECT")
            ->setCellValue('F4', "WRONG")
            ->setCellValue('G4', "RESULT")
            ->setCellValue('H4', "RANK")
            ->setCellValue('I4', "STATUS");
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray_header);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 4;
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $wksh) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $wksh->trainee_id)
                    ->setCellValue("B$i", $wksh->trainee_name)
                    ->setCellValue("C$i", $wksh->trainee_region)
                    ->setCellValue("D$i", $wksh->played_questions)
                    ->setCellValue("E$i", $wksh->correct)
                    ->setCellValue("F$i", ($wksh->played_questions - $wksh->correct))
                    ->setCellValue("G$i", ($wksh->accuracy == "" ? "Not Played" : $wksh->accuracy . "%"))
                    ->setCellValue("H$i", $wksh->rank)
                    ->setCellValue("I$i", ($wksh->played_questions > 0 ? $wksh->status : 'Not Attended'));
                $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
            }
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Trainer Accuracy Reports.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
    }
    // Trainer Accuracy End Here 











    // ==============================================================================================================================================








    // Trainee Workshop Tab 2 Start here
    public function ajax_getTraineeData()
    {
        $dtSearchColumns = array('w.start_date', 'w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $login_id  = $this->mw_session['user_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($this->mw_session['login_type'] == "3") {
            $trainee_id = $this->mw_session['user_id'];
            $RightsFlag = 1;
            $WRightsFlag = 1;
        } else {
            $trainee_id = ($this->input->get('trainee_id') ? $this->input->get('trainee_id') : '');
            if ($this->mw_session['company_id'] != "" && !$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshoptype_id != "0") {
            if ($dtWhere != "") {
                $dtWhere .= " AND w.workshop_type  = " . $workshoptype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type  = " . $workshoptype_id;
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
        if ($trainee_id != "") {
            $this->trainer_trainee_workshop_reports_model->SynchTraineeData($company_id);
            $DTRenderArray = $this->trainer_trainee_workshop_reports_model->getTraineeData($company_id, $workshoptype_id, $trainee_id, $dtOrder, $dtLimit, $dtWhere, $RightsFlag, $WRightsFlag);
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
        }
        $dtDisplayColumns = array('start_date', 'workshop_name', 'total_topic', 'post_average', 'avg_time', 'Actions');
        $site_url = base_url();
        if (isset($DTRenderArray['ResultSet'])) {
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == "post_average") {
                        $row[] = ($dtRow['post_average'] != 'NP' ? $dtRow['post_average'] . '%' : 'Not Played');
                    } else if ($dtDisplayColumns[$i] == "avgresponcetime") {
                        $row[] = $dtRow['avgresponcetime'] . ' Sec';
                    } else if ($dtDisplayColumns[$i] == "Actions") {
                        $action = "<a data-target='#LoadModalFilter' data-toggle='modal' href='" . $site_url . "trainer_trainee_workshop_reports/summary_ajax_chart/" . base64_encode($dtRow['workshop_id']) . "/" . base64_encode($trainee_id) . "/' class='btn btn-xs blue margin-bottom'><i class='fa fa-bar-chart'></i> SUMMARY</a>"
                            . "<a data-target='#LoadModalFilter' data-toggle='modal' href='" . $site_url . "trainer_trainee_workshop_reports/detail_ajax_chart/" . base64_encode($dtRow['workshop_id']) . "/" . base64_encode($trainee_id) . "' class='btn btn-xs red margin-bottom'><i class='fa fa-bar-chart'></i> DETAIL</a>";
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

    public function ajax_companywiseData()
    {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id, 1);
        $data['TraineeResult'] = $this->common_model->getUserTraineeList($company_id, 1);
        echo json_encode($data);
    }

    public function getTraineeWorkshopData()
    {
        if ($this->mw_session['login_type'] == 3) {
            $trainee_id = $this->mw_session['user_id'];
        } else {
            $trainee_id = $this->input->post('trainee_id', TRUE);
        }
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $data['WorkshopResultSet'] = $this->common_model->getUserWorkshopList('', $trainee_id, $workshoptype_id);
        echo json_encode($data);
    }

    public function summary_ajax_chart($dworkshop_id = '', $dtrainee_id = '')
    {
        $Table = '';
        $MainTable = '';
        $error = '';
        $Label = [];
        $dataset = [];
        $data['module_id'] = '24.3';
        $workshop_id = base64_decode($dworkshop_id);
        $trainee_id = base64_decode($dtrainee_id);
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id  = $this->mw_session['user_id'];
        if ($this->mw_session['login_type'] != 3) {
            if ($this->mw_session['company_id'] != "" && !$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }

        if ($workshop_id != '' && $trainee_id != '') {
            $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($workshop_id);
            if ($islive_workshop) {
                $ResultSetData = $this->trainer_trainee_workshop_reports_model->getLivePrePostData($workshop_id, $trainee_id);
                $data['PrePostMainData'] = $this->trainer_trainee_workshop_reports_model->get_LivePrePostTopicwise($workshop_id, $trainee_id);
            } else {
                $ResultSetData = $this->trainer_trainee_workshop_reports_model->getPrePostData($workshop_id, $trainee_id);
                $data['PrePostMainData'] = $this->trainer_trainee_workshop_reports_model->get_PrePostTopicwise($workshop_id, $trainee_id);
            }
            $RankData = $this->trainer_trainee_workshop_reports_model->get_Traineewise_Rank($workshop_id, $trainee_id, $islive_workshop);
            if (count((array)$RankData) > 0) {
                $Rank = $RankData[0]->rank;
            } else {
                $Rank = "-";
            }
            $PrePostData = $ResultSetData[0];
            $data['WorkshopName'] = $PrePostData->workshop_name;
            $pre_average = $PrePostData->pre_average;
            $ce = $PrePostData->ce;
            $ceTable = $PrePostData->ce . '%';
            if ($pre_average == 'Not Played') {
                $ceTable = "Not Played";
                $ce = 0;
            }
            $post_average = $PrePostData->post_average;
            if ($post_average == 'Not Played') {
                $ceTable = "NotPlayed";
                $ce = 0;
            }
            $Label[] = json_encode($PrePostData->trainee_name);
            if ($ce < 0) {
                $dataset1[] = $ce;
                $dataset[] = '';
            } else {
                $dataset[] = $ce;
                $dataset1[] = '';
            }
            $Table = '<table class="table table-hover table-light ranktable " id="ranktable" width="50%">
                                <thead >
                                <tr class="uppercase" style="background-color: #e6f2ff;">
                                    <th>Trainee Name</th>                        
                                    <th>Pre</th>
                                    <th>Post</th>
                                    <th>C.E</th>
                                    <th>Rank</th>
                                </tr></thead><tbody>';
            if (count((array)$PrePostData) > 0) {
                $Table .= '<tr id="datatr">
                        <td>' . $PrePostData->trainee_name . '</td>
                        <td>' . $pre_average . '</td>
                        <td>' . $post_average . '</td>
                        <td>' . $ceTable . '</td>
                        <td>.' . $Rank . '</td>
                        </tr>';
            }
            $Table .= "</tbody></table>";
            $MainTable = '<table class="table table-hover table-light ranktable" id="ranktable" width="50%">
                        <thead >
                        <tr class="uppercase" style="background-color: #e6f2ff;">
                            <th>Topics</th>                        
                            <th>Subtopics</th>
                            <th>Pre</th>
                            <th>Post</th>
                            <th>C.E.</th>
                        </tr></thead><tbody>';
            if (count((array)$data['PrePostMainData']) > 0) {
                foreach ($data['PrePostMainData'] as $value) {
                    $MainTable .= '<tr id="datatr">
                        <td>' . $value->topic . '</td>
                        <td>' . $value->subtopic . '</td>
               z         <td>' . ($value->pre_status > 0 ? 'Not Played' : $value->pre_average . '%') . '</td>
                        <td>' . ($value->post_status > 0 ? 'Not Played' : $value->post_average . '%') . '</td>
                        <td>' . ($value->pre_status > 0 || $value->post_status  ? 'Not Played' : $value->ce . '%') . '</td>
                    </tr>';
                }
            }
            $MainTable .= "</tbody></table>";
            $data['Table'] = $Table;
            $data['MainTable'] = $MainTable;
            $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $data['dataset1'] = json_encode($dataset1, JSON_NUMERIC_CHECK);
            $Rdata['totallabel'] = count((array)$Label);
            $data['label'] = json_encode($Label);

            $this->load->view('trainer_trainee_workshop_reports/show_summary_report', $data);
        } else {
            $error = "Invalid Filter Selections...";
        }
    }

    public function detail_ajax_chart($workshop_id = '', $trainee_id = '')
    {
        $Table = '';
        $MainTable = '';
        $error = '';
        $Label = [];
        $dataset = [];
        $data['module_id'] = '24.3';
        $workshop_id = base64_decode($workshop_id);
        $trainee_id = base64_decode($trainee_id);
        if ($workshop_id != '' && $trainee_id != '') {
            $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($workshop_id);
            if ($islive_workshop) {
                $ResultSetData = $this->trainer_trainee_workshop_reports_model->getLivePrePostData($workshop_id, $trainee_id);
            } else {
                $ResultSetData = $this->trainer_trainee_workshop_reports_model->getPrePostData($workshop_id, $trainee_id);
            }

            $PrePostData = $ResultSetData[0];
            $pre_average = $PrePostData->pre_average;
            $ce = $PrePostData->ce;
            $ceTable = $PrePostData->ce . '%';
            if ($pre_average == 'Not Played') {
                $ceTable = "Not Played";
                $ce = 0;
            }
            $post_average = $PrePostData->post_average;
            if ($post_average == 'Not Played') {
                $ceTable = "NotPlayed";
                $ce = 0;
            }

            $Label[] = json_encode($PrePostData->workshop_name);
            $dataset[] = $PrePostData->post_avg;

            $Table = '<div style="text-align: center;background-color:#e6f2ff;color:#000;height: 45px">
                Workshop <i>(Click on Workshop title to generate topic + sub-topic chart)</i>
            </div>
                        <table class="table table-hover table-light ranktable  " id="wtable" width="25%">
                                        <thead >
                                        <tr class="uppercase" style="background-color: #e6f2ff;">
                                            <th>Workshop</th>                        
                                            <th>Pre</th>
                                            <th>Post</th>
                                            <th>C.E</th>                                    
                                        </tr></thead><tbody>';
            $Table .= '<tr id="Mwrk' . $trainee_id . '" class="trClickeble" onclick="WorkshopWiseTopicSubtopicGraph(' . $workshop_id . ',' . $trainee_id . ')">
                   <td>' . $PrePostData->workshop_name . '</td>
                   <td>' . $pre_average . '</td>
                   <td>' . $post_average . '</td>
                   <td>' . $ceTable . '</td>                        
                   </tr>';
            $Table .= "</tbody></table>";

            $data['Table'] = $Table;
            $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $Rdata['totallabel'] = count((array)$Label);
            $data['label'] = json_encode($Label);
            $data['Trainee_name'] = $PrePostData->trainee_name;

            $this->load->view('trainer_trainee_workshop_reports/show_detail_report', $data);
        } else {
            $error = "Please Select Company,Workshop And Trainee";
        }
    }

    public function Detail_TopicSubtopicChart()
    {
        $Label = [];
        $QATable = '';
        $datasetpre = [];
        $datasetpost = [];
        $datasetCE = [];

        $trainee_id = $this->input->post('trainee_id', true);
        $workshop_id = $this->input->post('workshop_id', true);

        $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLive($workshop_id);
        if ($islive_workshop) {
            $QuestionAnsData = $this->trainer_trainee_workshop_reports_model->getLivePrePostQuestionAnsData($workshop_id, $trainee_id);
            $TopicSubtopicDataArray = $this->trainer_trainee_workshop_reports_model->get_LivePrePostTopicwise($workshop_id, $trainee_id);
        } else {
            $QuestionAnsData = $this->trainer_trainee_workshop_reports_model->getPrePostQuestionAnsData($workshop_id, $trainee_id);
            $TopicSubtopicDataArray = $this->trainer_trainee_workshop_reports_model->get_PrePostTopicwise($workshop_id, $trainee_id);
        }
        if (count((array)$TopicSubtopicDataArray) > 0) {
            foreach ($TopicSubtopicDataArray as $value) {
                $Label[] = $value->topic . ($value->subtopic != 'No sub-Topic' ? '-' . $value->subtopic : '');
                $datasetpre[] = $value->pre_average;
                $datasetpost[] = $value->post_average;
                $ce = $value->ce;
                if ($value->pre_status > 0 || $value->post_status > 0) {
                    $ce = 0;
                }
                if ($ce < 0) {
                    $dataset4[] = $ce;
                    $dataset3[] = '';
                } else {
                    $dataset3[] = $ce;
                    $dataset4[] = '';
                }
            }
        }
        $QATable = '<table class="table table-hover table-light ranktable" id="wtable" width="50%">
                                <thead>                                
                                <tr class="uppercase" style="background-color: #e6f2ff;">
                                    <th>Particulars</th>                        
                                    <th>No. Of Question</th>
                                    <th>Total Correct Answer</th>                                                                        
                                </tr></thead><tbody>';
        if (count((array)$QuestionAnsData) > 0) {
            $QATable .= '<tr>
                <td>PRE</td>
                <td>' . $QuestionAnsData->pre_total_questions . '</td>
                <td>' . $QuestionAnsData->pre_correct . '</td>                                                              
                </tr><tr><td>POST</td>
                <td>' . $QuestionAnsData->post_total_questions . '</td>
                <td>' . $QuestionAnsData->post_correct . '</td>                                                              
                </tr>';
        }
        $QATable .= "</tbody></table>";

        $Rdata['datasetpre'] = json_encode($datasetpre, JSON_NUMERIC_CHECK);
        $Rdata['datasetpost'] = json_encode($datasetpost, JSON_NUMERIC_CHECK);
        $Rdata['dataset3'] = json_encode($dataset3, JSON_NUMERIC_CHECK);
        $Rdata['dataset4'] = json_encode($dataset4, JSON_NUMERIC_CHECK);
        $Rdata['totallabel'] = count((array)$Label);
        $Rdata['label'] = json_encode($Label);

        $showreport = $this->load->view('trainer_trainee_workshop_reports/show_detail_topicsubtopic', $Rdata, true);
        $data['Error'] = '';
        $data['HTMLGraphData'] = $showreport;
        $data['QATable'] = $QATable;
        echo json_encode($data);
    }
    public function getTraineeData()
    {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $lcTrainee_html = '<option value="">Select Trainee</option>';
        if ($trainee_region_id != '0') {
            $TraineeData = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as traineename', 'company_id=' . $company_id . ' and region_id=' . $trainee_region_id);
        } else {
            $TraineeData = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as traineename', 'company_id=' . $company_id);
        }
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $value) {
                $lcTrainee_html .= '<option value="' . $value->user_id . '">' . $value->traineename . '</option>';
            }
        }
        $data['TraineeData'] = $lcTrainee_html;
        echo json_encode($data);
    }
    // Trainee Workshop Tab 2 End here


























    // trainee Workshop reports Start Here  (Tab 2 Second)
    public function ajax_traineeWiseData()
    {
        $TraineeTable = '';
        $error = '';
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $RowId = $this->input->post('RowId', TRUE);
        if ($workshop_id != '') {
            if ($this->mw_session['login_type'] == 3) {
                $trainee_id = $this->mw_session['user_id'];
            }
            $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLive_tc($workshop_id);
            if ($islive_workshop) {
                $TraineeCEData = $this->trainer_trainee_workshop_reports_model->getLivePrePostData_tc($workshop_id, $trainee_id);
            } else {
                $TraineeCEData = $this->trainer_trainee_workshop_reports_model->getPrePostData_tc($workshop_id, $trainee_id);
            }
            if ($trainee_id != "") {
                $RankData = $this->trainer_trainee_workshop_reports_model->get_Traineewise_Rank_tc($workshop_id, $trainee_id, $islive_workshop);
                if (count((array)$RankData) > 0) {
                    $Rank = $RankData[0]->rank;
                } else {
                    $Rank = "-";
                }
            }
            $WorkshopData = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $TraineeTable = '<div id="childdiv_' . $RowId . '" class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Workshop : ' . $WorkshopData->workshop_name . '</span>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:void(0)" class="remove" onclick="remove_workshop(' . $RowId . ');"> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">'
                . '<table class="table table-hover table-light scroll" id="Traineetable" width="400px">
                                        <thead >
                                        <tr class="uppercase" >                                            
                                            <th>Trainee Name</th>                        
                                            <th>Pre</th>
                                            <th>Post</th>
                                            <th>C.E</th>
                                            <th width="12%">RESPONSE TIME</th>
                                            <th>Rank</th>
                                        </tr></thead><tbody>';
            if (count((array)$TraineeCEData) > 0) {
                foreach ($TraineeCEData as $value) {
                    $ceTable = $value->ce . '%';
                    $pre_average = $value->pre_average;
                    if ($pre_average == 'Not Played') {
                        $ceTable = "Not Played";
                    }
                    $post_average = $value->post_average;
                    if ($post_average == 'Not Played') {
                        $ceTable = "Not Played";
                    }
                    if ($trainee_id == "") {
                        $Rank = $value->rank;
                    }
                    $TraineeTable .= '<tr class="datatr">
                                <td>' . $value->trainee_name . '</td>
                                <td>' . $pre_average . '</td>
                                <td>' . $post_average . '</td>
                                <td>' . $ceTable . '</td>    
                                <td>' . $value->response_time . '</td>      
                                <td>' . $Rank . '</td>
                                </tr>';
                }
            } else {
                $TraineeTable .= '<tr class="datatr"><td colspan="4">No Data found...</td></tr>';
            }

            $TraineeTable .= '</tbody></table></div></div></div>';
        } else {
            $error = "Please Select Company,Workshop";
        }
        $data['TraineeTable'] = $TraineeTable;
        $data['Error'] = $error;
        echo json_encode($data);
    }

    public function ComparisonWorkshopTable($Counter)
    {
        $WTable = '';
        $error = '';
        $ExportRights = $this->acces_management;
        $workshop_id = $this->input->post('workshop_id_tc', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id_tc', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $Trainee_id = $this->input->post('trainee_id_tc', TRUE);
        if ($workshop_id != '' && $company_id != '') {
            $this->trainer_trainee_workshop_reports_model->SynchTraineeData_tc($company_id);
            $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLive_tc($workshop_id);
            if ($islive_workshop) {
                $WorkshopPrePostData = $this->trainer_trainee_workshop_reports_model->getLivePrePostWorkshopwise_tc($workshop_id);
            } else {
                $WorkshopPrePostData = $this->trainer_trainee_workshop_reports_model->getPrePostWorkshopwise_tc($workshop_id);
            }

            if (count((array)$WorkshopPrePostData) > 0 && $WorkshopPrePostData->workshop_name != "") {
                $CE = $WorkshopPrePostData->post_average - $WorkshopPrePostData->pre_average . '%';
                $Pre_avg = $WorkshopPrePostData->pre_average;
                $Post_avg = $WorkshopPrePostData->post_average;
                if ($Pre_avg == 0) {
                    $Pre_avg = "Not Played";
                    $CE = "Not Played";
                } else {
                    $Pre_avg .= "%";
                }
                if ($Post_avg == 0) {
                    $Post_avg = "Not Played";
                    $CE = "Not Played";
                } else {
                    $Post_avg .= "%";
                }
                $WTable .= '<tr id="datatr_' . $Counter . '" class="datatr trClickeble">
                <td style="width: 33%;" onclick="traineeTableData(' . $workshop_id . ',' . $Counter . ',' . $Trainee_id . ')">' . $WorkshopPrePostData->workshop_name . '</td>
                <td style="width: 22%;">' . $Pre_avg . '</td>
                <td style="width: 20%;">' . $Post_avg . '</td>
                <td style="width: 20%;">' . $CE . '</td> 
                <td style="width: 30%;">';
                if ($ExportRights->allow_export) {
                    $WTable .= ' <a  href="' .  base_url() . 'trainer_trainee_workshop_reports/export_workshop_trainee/' . $company_id . '/' . $workshop_id . '/' . $Trainee_id . '" class="btn btn-xs green">
                                   <i class="fa fa-file-excel-o"></i> Export
                               </a>';
                }
                //                 $WTable .= '<td style="width: 30%;"><button id="button-filter"  class="btn btn-sm btn-small btn-danger" type="button" onclick="RemoveChart(' . $Counter . ');">X</button></td>
                //                </tr>';
                $WTable .= '<a  href="javascript:void(0)" onclick="RemoveChart(' . $Counter . ');" class="btn btn-xs red">
                                    <i class="fa fa-remove"></i> Remove
                                </a>
                  <a style="width:200px;float:right;text-decoration:none;display: block;">&nbsp;</a>
                            </td>
                        </tr>';
            }
        } else {
            $error = "Please Select Company,Workshop";
        }
        $data['ChartTable'] = $WTable;
        $data['Error'] = $error;

        echo json_encode($data);
    }



    public function export_workshop_trainee($company_id, $workshop_id, $trainee_id = "")
    {
        if ($company_id == "" || $workshop_id == "") {
            redirect('trainee_comparison_report');
        }
        $ExportRights = $this->acces_management;
        if (!$ExportRights->allow_export) {
            redirect('trainee_comparison_report');
        }

        if ($workshop_id != '') {
            if ($this->mw_session['login_type'] == 3) {
                $trainee_id = $this->mw_session['user_id'];
            }
            $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLive_tc($workshop_id);
            if ($islive_workshop) {
                $TraineeCEData = $this->trainer_trainee_workshop_reports_model->getLivePrePostData_tc($workshop_id, $trainee_id);
            } else {
                $TraineeCEData = $this->trainer_trainee_workshop_reports_model->getPrePostData_tc($workshop_id, $trainee_id);
            }
            if ($trainee_id != "") {
                $RankData = $this->trainer_trainee_workshop_reports_model->get_Traineewise_Rank_tc($workshop_id, $trainee_id, $islive_workshop);
                if (count((array)$RankData) > 0) {
                    $Rank = $RankData[0]->rank;
                } else {
                    $Rank = "-";
                }
            }
            $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $workshop_id);
            $workshop_name = $Workshop_rowset->workshop_name;
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', "Workshop Name :" . $workshop_name)
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Trainee Name")
                ->setCellValue('C3', "Trainee Region")
                ->setCellValue('D3', "PRE")
                ->setCellValue('E3', "POST")
                ->setCellValue('F3', "C.E")
                ->setCellValue('G3', "RESPONSE TIME")
                ->setCellValue('H3', "RANK");
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleArray_header);

            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $i = 3;
            foreach ($TraineeCEData as $compr) {
                $i++;
                $comp_pre_average_accuracy = ($compr->pre_average == "NP" ? "Not Played" : $compr->pre_average);
                $comp_post_average_accuracy = ($compr->post_average == "NP" ? "Not Played" : $compr->post_average);
                $comp_ce = ($compr->pre_average == 'Not Played' || $compr->post_average == 'Not Played' ? "Not Played" : $compr->ce . "%");
                $comp_rank = $compr->rank;

                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $compr->trainee_id)
                    ->setCellValue("B$i", $compr->trainee_name)
                    ->setCellValue("C$i", $compr->trainee_region)
                    ->setCellValue("D$i", $comp_pre_average_accuracy)
                    ->setCellValue("E$i", $comp_post_average_accuracy)
                    ->setCellValue("F$i", $comp_ce)
                    ->setCellValue("G$i", $compr->response_time)
                    ->setCellValue("H$i", $comp_rank);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee Comparison Reports.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
        }
    }
    public function getTraineeDataTrainee()
    {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $lcTrainee_html = '<option value="">Select Trainee</option>';
        if ($trainee_region_id != '0') {
            $TraineeData = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as traineename', 'company_id=' . $company_id . ' and region_id=' . $trainee_region_id);
        } else {
            $TraineeData = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as traineename', 'company_id=' . $company_id);
        }
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $value) {
                $lcTrainee_html .= '<option value="' . $value->user_id . '">' . $value->traineename . '</option>';
            }
        }
        $data['TraineeData'] = $lcTrainee_html;
        echo json_encode($data);
    }
    // trainee Workshop reports End here (Tab 2 second)

    // ================================================================================================================================
















    // Trainee Accuracy Report TAB 2 Start Here
    public function ajax_traineewtypewise_data()
    {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if ($this->mw_session['login_type'] != 3) {
                $Login_id = $this->mw_session['user_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
            }
        }
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $region_id = $this->input->post('region_id', TRUE);
        if ($WRightsFlag) {
            $WorkshopData = $this->common_model->getUserWorkshopList($company_id, $trainee_id, $workshoptype_id, $region_id);
            //            $WtypeResult = $this->common_model->getTraineeWTypeList($company_id, $trainee_id);
        } else {
            $WorkshopData = $this->common_model->getTrainerWorkshop($company_id, $WRightsFlag, 0, $region_id);
            //            $WtypeResult = $this->common_model->getWTypeRightsList($company_id,$WRightsFlag);
        }
        $lchtml = '<option value="">Please Select</option>';
        //         $lchtml1='<option value="">All Select</option>';

        if ($WorkshopData > 0) {
            foreach ($WorkshopData as $value) {
                $lchtml .= '<option value="' . $value->workshop_id . '">' . $value->workshop_name . '</option>';
            }
        }
        $data['WorkshopData'] = $lchtml;
        //        $data['WtypeResult'] = $lchtml1;
        $data['WorkshopSubtypeData'] = $this->get_workshop_subtype_selectbox($company_id, $workshoptype_id);
        $data['WorkshopSubregionData'] = $this->get_workshop_subregion_selectbox($company_id, $region_id);
        echo json_encode($data);
    }
    public function get_workshop_subtype_selectbox($company_id, $workshoptype_id = '')
    {
        $lchtml = '<option value="">Please Select</option>';
        if ($workshoptype_id != '') {
            $Dataset        = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description as sub_type', 'company_id=' . $company_id . ' and workshoptype_id=' . $workshoptype_id);
            if (count((array)$Dataset) > 0) {
                foreach ($Dataset as $value) {
                    $lchtml .= '<option value="' . $value->id . '">' . $value->sub_type . '</option>';
                }
            }
        }
        return $lchtml;
    }
    public function get_workshop_subregion_selectbox($company_id, $region_id = '')
    {
        $lchtml = '<option value="">Please Select</option>';
        if ($region_id != '') {
            $Dataset        = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description as sub_region', 'company_id=' . $company_id . ' and region_id=' . $region_id);
            if (count((array)$Dataset) > 0) {
                foreach ($Dataset as $value) {
                    $lchtml .= '<option value="' . $value->id . '">' . $value->sub_region . '</option>';
                }
            }
        }
        return $lchtml;
    }
    public function ajax_workshopwise_data()
    {
        $workshop_id = $this->input->post('workshop_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['UserData'] = $this->trainee_reports_model->get_WorkshopRegisterdusers($workshop_id, $company_id);

        echo json_encode($data);
    }

    public function ajax_chart_trainee_accuracy($TotalChart)
    {
        $successFlag = 0;
        $Table = '';
        $error = '';
        $lcHtml = '';
        $Label = [];
        $dataset = [];
        $ExportRights = $this->acces_management;
        $workshoptype_id = $this->input->post('workshoptype_id_ta', TRUE);
        $workshop_id = $this->input->post('workshop_id_ta', TRUE);
        if ($this->mw_session['login_type'] != 3) {
            $user_id = $this->input->post('trainee_id_ta', TRUE);
        } else {
            $user_id = $this->mw_session['user_id'];
        }
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id_ta', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $this->trainer_trainee_workshop_reports_model->SynchTraineeData($company_id);
        $workshop_session = $this->input->post('workshop_session_ta', TRUE);
        if ($workshop_id != '') {
            $ChartData = $this->trainer_trainee_workshop_reports_model->get_PrepostAccuracy_ta($workshop_id, $user_id, $workshop_session);
            if (count((array)$ChartData) > 0) {
                foreach ($ChartData as $value) {
                    $dataset[] = $value->accuracy;
                    $Label[] = $value->topic . ($value->subtopic != 'No sub-Topic' ? '-' . $value->subtopic : '');
                }
            }
            $username = $this->common_model->get_value('device_users', 'concat(firstname," ",lastname) as username,email', 'user_id=' . $user_id);
            $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $data['label'] = json_encode($Label);
            $data['totallabel'] = count((array)$Label);
            $data['user'] = json_encode($username->username);
            $data['user_id'] = json_encode($user_id);
            $data['email'] = json_encode($username->email);
            $data['TotalChart'] = $TotalChart;
            $WorkshopRow = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $data['Workshop_name'] = $WorkshopRow->workshop_name . '(' . ($workshop_session == "" ? 'All' : $workshop_session) . ' Session )';
            $lcHtml = $this->load->view('trainer_trainee_workshop_reports/show_reports_ta', $data, true);

            $islive_workshop = $this->trainer_trainee_workshop_reports_model->isWorkshopLiveTrainee($workshop_id);
            if ($islive_workshop) {
                $PrepostAccSet = $this->trainer_trainee_workshop_reports_model->getLivePrePostData($workshop_id, $user_id);
            } else {
                $PrepostAccSet = $this->trainer_trainee_workshop_reports_model->getPrePostData($workshop_id, $user_id);
            }
            $RankData = $this->trainer_trainee_workshop_reports_model->get_Traineewise_Rank($workshop_id, $user_id, $islive_workshop);
            if (count((array)$RankData) > 0) {
                $Rank = $RankData[0]->rank;
            } else {
                $Rank = "-";
            }
            if (count((array)$PrepostAccSet) > 0) {
                if ($workshop_session == 'PRE') {
                    $Overallaccuracy = $PrepostAccSet[0]->pre_average;
                } else {
                    $Overallaccuracy = $PrepostAccSet[0]->post_average;
                }
                $Table = '<tr id="datatr_' . $TotalChart . '">
                <td> Workshop :' . $data['Workshop_name'] . ',Trainee :' . $PrepostAccSet[0]->trainee_name . '</td>
                <td>' . $Overallaccuracy . '</td>                            
                <td>' . $Rank . '</td>
                <td style="width: 15%;">';
                if ($ExportRights->allow_export) {
                    $Table .= ' <a  href="' .  base_url() . 'trainer_trainee_workshop_reports/export_workshop_ta/' . $company_id . '/' . $workshop_id . '/' . $user_id . '/' . $workshop_session . '" class="btn btn-xs green">
                                   <i class="fa fa-file-excel-o"></i> Export
                               </a>';
                }
                '</td></tr>';
            } else {
                $Table = '<tr id="datatr_' . $TotalChart . '">
                <td> Workshop :' . $data['Workshop_name'] . '</td>
                <td>Not Played</td>                            
                <td>-</td>                            
                </tr>';
            }
        } else {
            $error = "Please Select Workshop..!";
        }
        $Rdata['HtmlData'] = $lcHtml;
        $Rdata['OverallTable'] = $Table;
        $Rdata['Error'] = $error;
        echo json_encode($Rdata);
    }
    public function export_workshop_ta($company_id, $workshop_id, $trainee_id, $workshop_session = '')
    {
        $ExportRights = $this->acces_management;
        if (!$ExportRights->allow_export) {
            redirect('trainee_accuracy_report');
        }
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id  = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = 0;
        $WorkshopLive = $this->trainer_trainee_workshop_reports_model->WorkshopLive($workshop_id, $workshop_session);
        $TraineeData = $this->trainer_trainee_workshop_reports_model->get_traineeAccuracyTrainee($RightsFlag, $trainee_id, $trainer_id, $workshop_id, $workshop_session, $WorkshopLive);

        $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $workshop_id);
        $workshop_name = $Workshop_rowset->workshop_name;
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', "Workshop Name :" . $workshop_name)
            ->setCellValue('A2', "Workshop Session :" . $workshop_session)
            //                    ->setCellValue('A3', "Trainer :")
            ->setCellValue('A4', "TRAINEE ID")
            ->setCellValue('B4', "TRAINEE NAME")
            ->setCellValue('C4', "TRAINEE REGION")
            ->setCellValue('D4', "TOTAL PLAYED")
            ->setCellValue('E4', "CORRECT")
            ->setCellValue('F4', "WRONG")
            ->setCellValue('G4', "RESULT")
            ->setCellValue('H4', "RANK")
            ->setCellValue('I4', "STATUS");
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray_header);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 4;
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $wksh) {
                $i++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $wksh->trainee_id)
                    ->setCellValue("B$i", $wksh->trainee_name)
                    ->setCellValue("C$i", $wksh->trainee_region)
                    ->setCellValue("D$i", $wksh->played_questions)
                    ->setCellValue("E$i", $wksh->correct)
                    ->setCellValue("F$i", ($wksh->played_questions - $wksh->correct))
                    ->setCellValue("G$i", ($wksh->accuracy == "" ? "Not Played" : $wksh->accuracy . "%"))
                    ->setCellValue("H$i", $wksh->rank)
                    ->setCellValue("I$i", ($wksh->played_questions > 0 ? $wksh->status : 'Not Attended'));
                $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
            }
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Trainee Accuracy Reports.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
    }
    public function getTraineeData_ta()
    {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $lcTrainee_html = '<option value="">Select Trainee</option>';
        if ($trainee_region_id != '0') {
            $TraineeData = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as traineename', 'company_id=' . $company_id . ' and region_id=' . $trainee_region_id);
        } else {
            $TraineeData = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as traineename', 'company_id=' . $company_id);
        }
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $value) {
                $lcTrainee_html .= '<option value="' . $value->user_id . '">' . $value->traineename . '</option>';
            }
        }
        $data['TraineeData'] = $lcTrainee_html;
        echo json_encode($data);
    }
    // Trainee Accuracy Report TAB 2 End Here

































    // Workshop reports Start here (TAB 3) 
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

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->Tpr_LoadDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


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
            $Data_list = $this->trainer_trainee_workshop_reports_model->export_Tpr_ToExcel($TPR_exportWhere, $TPR_RightsFlag, $TPR_WRightsFlag);

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

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->TraineeSummaryLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


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

            $Data_list = $this->trainer_trainee_workshop_reports_model->TraineeSummaryExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag, $dtOrder);


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

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->Ttqwr_LoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag, $report_type);
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

            $Data_list = $this->trainer_trainee_workshop_reports_model->export_Ttqwr_ToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag, $report_type);

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

        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'exportHaving' => $dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->TrainerSummaryLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


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
            $dtWhere = $this->session->userdata('exportWhere');
            $dthaving = $this->session->userdata('exportHaving');
            $RightsFlag = $this->session->userdata('RightsFlag');
            $WRightsFlag = $this->session->userdata('WRightsFlag');
            $i = 3;

            $Data_list = $this->trainer_trainee_workshop_reports_model->TrainerSummaryExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);


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

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->TrainerConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtHaving, $RightsFlag, $WRightsFlag);
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
            $Data_list = $this->trainer_trainee_workshop_reports_model->TrainerConsolidatedExportToExcel($exportWhere, $exportHaving, $RightsFlag, $WRightsFlag);


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

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->WorkshopWiseLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);

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

            $Data_list = $this->trainer_trainee_workshop_reports_model->WorkshopWiseExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);


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

        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->QuestionWiseLoadDataTable($dtWhere, $dtWhere2, $dtOrder, $dtLimit, $dtHaving);
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
            $Data_list = $this->trainer_trainee_workshop_reports_model->QuestionWiseExportToExcel($exportWhere, $exportHaving);

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
            $data['WorkshopData'] = $this->trainer_trainee_workshop_reports_model->getWorkshopList($company_id, $region_id);
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
        $DTRenderArray = $this->trainer_trainee_workshop_reports_model->ImeiDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);
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
        $TraineeSet = $this->trainer_trainee_workshop_reports_model->ImeiExportToExcel($dtWhere, $RightsFlag, $WRightsFlag);
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
