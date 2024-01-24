<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supervisor_accuracy extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('supervisor_accuracy');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('supervisor_accuracy_model');
        }

    public function index() {
        $data['module_id'] = '15.3';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Trainee_id = '';
        $data['Company_id'] = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['Company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if (!$RightsFlag) {
                $this->common_model->SyncTrainerRights($login_id);
            }
            $data['TrainerResult'] = $this->common_model->getUserRightsList($data['Company_id'], $RightsFlag);
            if (!$WRightsFlag) {
                $this->common_model->SyncWorkshopRights($login_id, 0);
            }
            $data['RegionResult'] = $this->common_model->getUserRegionList($data['Company_id'], $WRightsFlag);
            $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['Company_id'], $WRightsFlag);
            $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($data['Company_id'], $WRightsFlag);
        }
        $data['DefaultTrainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        $this->load->view('supervisor_accuracy/index', $data);
    }

    public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, 1);
        $data['RegionResult'] = $this->common_model->getUserRegionList($company_id, 1);
        $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id, 1);
        $data['WorkshopData'] = $this->common_model->getTrainerWorkshop($company_id, 1);
        echo json_encode($data);
    }

    public function ajax_workshoptypewise_data() {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $region_id = $this->input->post('region_id', TRUE);
        $trainer_id = $this->input->post('trainer_id', TRUE);
        $data['WorkshopData'] = $this->common_model->getTrainerWorkshop($company_id, $WRightsFlag, $trainer_id, $region_id, $workshoptype_id);
        //$data['TrainerResult'] = $this->common_model->get_selected_values('company_users','userid,concat(first_name," ",last_name) as trainer_name','company_id='.$company_id);               
        echo json_encode($data);
    }

    public function ajax_TrainerData() {
        $region_id = $this->input->post('region_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, 1, $region_id);
        echo json_encode($data);
    }

    public function ajax_chart($TotalChart) {
        $successFlag = 0;
        $Table = '';
        $error = '';
        $lcHtml = '';
        $Label = [];
        $dataset = [];
        $session = 'PRE Session';
        $workshop_session = $this->input->post('workshop_session', TRUE);
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $region_id = $this->input->post('region_id', TRUE);
        $trainer_id = $this->input->post('trainer_id', TRUE);
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
        }
        $this->load->model('supervisor_dashboard_model');
        $SyncFlag = $this->supervisor_dashboard_model->requiredSyncData($company_id);
        if ($SyncFlag) {
            //$this->supervisor_dashboard_model->SyncTrainerResult($company_id);
            $this->supervisor_dashboard_model->LiveDataSync($company_id);
            //$this->supervisor_dashboard_model->SyncWorshopResult($Company_id);
        }
        if ($workshop_id != '') {
            $ChartData = $this->supervisor_accuracy_model->getAccuracyDetails($workshop_id, $workshop_session, $trainer_id, $RightsFlag);
            $WorkshopRow = $this->supervisor_accuracy_model->get_PrepostAccuracy($workshop_id, $workshop_session, $trainer_id, $RightsFlag);
            $Overallaccuracy = 0;
            $Workshop_Name = '';
            if (count((array)$WorkshopRow) > 0) {
                $Overallaccuracy = $WorkshopRow->accuracy;
                $Workshop_Name = $WorkshopRow->workshop_name;
                if (count((array)$ChartData) > 0) {
                    foreach ($ChartData as $value) {
                        $dataset[] = $value->accuracy;
                        $Label[] = $value->topic . ($value->subtopic != 'No sub-Topic' ? '-' . $value->subtopic : '');
                    }
                }
                $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
                $data['totallabel'] = count((array)$Label);
                $data['label'] = json_encode($Label);
                $data['TotalChart'] = $TotalChart;
                if ($trainer_id != 0) {
                    $Trainer_name = $this->common_model->get_value('company_users', 'concat(first_name," ",last_name) as trainer_name', 'userid=' . $trainer_id);
                    $data['Trainer_name'] = $Trainer_name->trainer_name;
                } else {
                    $data['Trainer_name'] = "All";
                }
//                if ($workshop_session == '') {
//                    $session = 'POST Session';
//                }
                $data['Workshop_name'] = $Workshop_Name . ' ( ' . $workshop_session . ' Session )';
                $lcHtml = $this->load->view('supervisor_accuracy/show_report', $data, true);
//            $RankData = $this->trainee_reports_model->get_Traineewise_Rank($workshop_id,$user_id);
//            $Rank='-';
//            if(count((array)$RankData)>0){
//                $Rank =$RankData[0]->rank;
//            }
                $Table = '<tr id="datatr_' . $TotalChart . '">
                <td>' . $data['Workshop_name'] . ' ( Trainer : ' . $data['Trainer_name'] . ' )</td>
                <td>' . $Overallaccuracy . '%</td>                                                       
                </tr>';
            }else{
                $error = "No Data Found for Selected Workshop..!";
            }
        } else {
            $error = "Please Select Workshop..!";
        }
        $Rdata['HtmlData'] = $lcHtml;
        $Rdata['OverallTable'] = $Table;
        $Rdata['Error'] = $error;
        echo json_encode($Rdata);
    }

}
