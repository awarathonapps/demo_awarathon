<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supervisor_comparison extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('supervisor_comparison');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('supervisor_comparison_model');
        }

    public function index() {
        $data['module_id'] = '15.2';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['Company_id'] = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['Company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
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
            $data['RegionResult'] = $this->common_model->getUserRegionList($data['Company_id'],$WRightsFlag);
            $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['Company_id'],$WRightsFlag);
            $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($data['Company_id'], $WRightsFlag);
        }
        $data['login_type'] = $this->mw_session['login_type'];
        $this->load->view('supervisor_comparison/index', $data);
    }

    public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $this->load->model('supervisor_dashboard_model');
        $SyncFlag = $this->supervisor_dashboard_model->requiredSyncData($company_id);
        if ($SyncFlag) {
            //$this->supervisor_dashboard_model->SyncTrainerResult($company_id);
            $this->supervisor_dashboard_model->LiveDataSync($company_id);
            //$this->supervisor_dashboard_model->SyncWorshopResult($Company_id);
        }
        
        $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, 1);
        $data['RegionResult'] = $this->common_model->getUserRegionList($company_id,1);
        $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id,1);
        $data['WorkshopData'] = $this->common_model->getTrainerWorkshop($company_id, 1);
        echo json_encode($data);
    }

    public function ajax_wtypewise_workshop() {
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
        $data['WorkshopData'] = $this->common_model->getTrainerWorkshop($company_id, $WRightsFlag,$trainer_id, $region_id, $workshoptype_id);
        echo json_encode($data);
    }

    public function ComparisonTable() {
        $ChildTable = '';
        $error = '';
        $rowID = $this->input->post('rowID', TRUE);
//      $workshop_id = $this->input->post('workshop_id', TRUE);
        $workshop_id = ($this->input->post('workshop_id', TRUE) !='' ? $this->input->post('workshop_id', TRUE) : 0);        
        $trainer_id = ($this->input->post('trainer_id', TRUE)!='' ? $this->input->post('trainer_id', TRUE) : 0);
        $region_id = ($this->input->post('region_id', TRUE) !='' ? $this->input->post('region_id', TRUE) : 0) ;
        $workshoptype_id = ($this->input->post('workshoptype_id', TRUE) !='' ? $this->input->post('workshoptype_id', TRUE):0);
        $wsubtype_id = $this->input->post('wsubtype_id', TRUE);
        $wsubregion_id = $this->input->post('wsubregion_id', TRUE);
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
        if ($company_id != '') {
            $ComparisonData = $this->supervisor_comparison_model->getComparisonData($company_id, $region_id, $workshoptype_id, $trainer_id, $workshop_id, $wsubtype_id, $wsubregion_id, $RightsFlag, $WRightsFlag);
            $HighestLowestCE = $this->supervisor_comparison_model->gethighest_lowest_ce($company_id, $region_id, $workshoptype_id, $trainer_id, $workshop_id, $wsubtype_id, $wsubregion_id, $RightsFlag, $WRightsFlag);
			$Total_users = $this->supervisor_comparison_model->getParticipantList($company_id, $region_id, $workshoptype_id, $trainer_id, $workshop_id, $wsubtype_id, $wsubregion_id, $RightsFlag, $WRightsFlag);

            $ChildTable = '<div class="col-md-6" id="childdiv_' . $rowID . '"><table class="table table-hover table-light" width="30%">
                                        <thead>                                            
                                        <tr class="uppercase" style="background-color: #e6f2ff;">
                                            <th>Filter By</th>
                                            <th>Total Workshop</th>                        
                                            <th>Avg. C.E</th>
                                            <th>Highest C.E</th>
                                            <th>Lowest C.E</th>
                                            <th>No of participant attended</th>
                                        </tr></thead><tbody>';
            if (count((array)$ComparisonData) > 0) {
                $ChildTable .='<tr class="datatr">
                                <td>Region :' . ($region_id != 0 ? $ComparisonData->region_name : 'All') . ',';
                      if($wsubregion_id !=''){
                            $ChildTable .=' Workshop Sub-region:' . $ComparisonData->workshop_subregion  . ',';
                        }     
                     $ChildTable .=' Workshop Type:' . ($workshoptype_id != 0 ? $ComparisonData->workshop_type_name : 'All') . ',';
                      if($wsubtype_id !=''){
                            $ChildTable .= '      Workshop Sub-type:' . $ComparisonData->workshop_subtype  . ',';
                        }    
                     $ChildTable .= '      Trainer :' . ($trainer_id != 0 ? $ComparisonData->first_name : 'All') . ','
                        . ' Workshop :' . ($workshop_id != 0 ? $ComparisonData->workshop_name : 'All') . '.</td>
                                <td>' . $Total_users->total_workshop . '</td>
                                <td>' . $ComparisonData->avgce . '%</td>
                                <td>' . $HighestLowestCE->highestce . '%</td>
                                <td>' . ($HighestLowestCE->lowestce != $HighestLowestCE->highestce ? $HighestLowestCE->lowestce."%" :"-") . '</td>
                                <td>' . $Total_users->total_users . '</td>
                                </tr>';
            } else {
                $ChildTable .='<tr class="datatr"><td colspan="4">No Data found...</td></tr>';
            }
            $ChildTable .='</tbody></table></div>';
        } else {
            $error = "Please Select Company,Workshop Type,Region";
        }
        $data['ChildTable'] = $ChildTable;
        $data['Error'] = $error;

        echo json_encode($data);
    }

}
