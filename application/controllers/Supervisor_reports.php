<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supervisor_reports extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('supervisor_reports');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('supervisor_reports_model');
        }

    public function index() {
        $data['module_id'] = '15.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['Company_id'] = $this->mw_session['company_id'];
        $WRightsFlag = 1;
        if ($data['Company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            $login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                if ($Rowset->userrights_type != 1) {
                    $this->common_model->SyncTrainerRights($login_id);
                }
                if ($Rowset->workshoprights_type != 1) {
                    $this->common_model->SyncWorkshopRights($login_id, 0);
                }
            }
            $data['RegionResult'] = $this->common_model->getUserRegionList($data['Company_id'],$WRightsFlag);
            $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['Company_id'],$WRightsFlag);
        }
        $data['login_type'] = $this->mw_session['login_type'];
        $this->load->view('supervisor_report/index', $data);
    }

    public function getReportTableData() {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
            $login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $this->load->model('supervisor_dashboard_model');
        $SyncFlag = $this->supervisor_dashboard_model->requiredSyncData($company_id);
        if ($SyncFlag) {
            $this->supervisor_dashboard_model->LiveDataSync($company_id);
            //$this->supervisor_dashboard_model->SyncTrainerResult($company_id);
            //$this->supervisor_dashboard_model->SyncWorshopResult($Company_id);
        }
        $query = " AND wr.company_id= " . $company_id;
        if (!$WRightsFlag) {
            $query .= " AND wr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (wr.trainer_id = $login_id OR wr.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $wrkshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($wrkshoptype_id != "0") {            
                $query .= " AND w.workshop_type  = " . $wrkshoptype_id; 
        } 
        $wregion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wregion_id != "0") {
                $query .= " AND w.region  = " . $wregion_id;
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
                $query .= " AND w.workshopsubtype_id  = " . $workshop_subtype;
        }
        $wsubregion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubregion_id != "") {
                $query .= " AND w.workshopsubregion_id  = " . $wsubregion_id;
        }
        $reportsby_id = ($this->input->get('reportsby_id') ? $this->input->get('reportsby_id') : '');
        if ($reportsby_id == 2) {
            $dtSearchColumns = array('wtm.workshop_type');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
            $dtWhere = $DTRenderArray['dtWhere'];
            if ($dtWhere != "") {
                $dtWhere .=$query;
            } else {
                $dtWhere .=" WHERE 1=1 " . $query;
            }
            $dtOrder = $DTRenderArray['dtOrder'];
            $dtLimit = $DTRenderArray['dtLimit'];
            $DTRenderArray = $this->supervisor_reports_model->getWorkshopTypeWiseData($dtLimit, $dtWhere);
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
            $dtDisplayColumns = array('PlusBtn', 'workshop_type_name', 'total_workshop', 'trainee_trained','avgce', 'highestce', 'lowestce', 'PlusBtn');
            $site_url = base_url();
            if (isset($DTRenderArray['ResultSet'])) {
                foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                    $row = array();
                    $TotalHeader = count((array)$dtDisplayColumns);
                    for ($i = 0; $i < $TotalHeader; $i++) {
                        if ($dtDisplayColumns[$i] == "PlusBtn") {
                            $row[] = $dtRow['workshop_type'];
                        } else if ($dtDisplayColumns[$i] == "workshop_type_name") {
                            $action = '<span class="row-details "><h5>' . $dtRow['workshop_type_name'] . '</h5></span>';
                            $row[] = $action;
                        } else if ($dtDisplayColumns[$i] == "avgce") {
                            $rowvalue = $dtRow['avgce'] . "%";
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "highestce") {
                            $rowvalue = $dtRow['highestce'] . "%";
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "lowestce") {
                            $rowvalue = ($dtRow['highestce'] == $dtRow['lowestce'] ? '-' : $dtRow['lowestce'] . "%" );
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] != ' ') {
                            $row[] = $dtRow[$dtDisplayColumns[$i]];
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        } else if ($reportsby_id == 3) {
            $dtSearchColumns = array('first_name', 'last_name');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
            $dtWhere = $DTRenderArray['dtWhere'];
            $dtWhere = $DTRenderArray['dtWhere'];
            if ($dtWhere != "") {
                $dtWhere .=$query;
            } else {
                $dtWhere .=" WHERE 1=1 " . $query;
            }
            $dtOrder = $DTRenderArray['dtOrder'];
            $dtLimit = $DTRenderArray['dtLimit'];
            $DTRenderArray = $this->supervisor_reports_model->getTrainerWiseData($dtLimit, $dtWhere);
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
            $dtDisplayColumns = array('PlusBtn', 'trainer_name', 'total_workshop', 'trainee_trained','avgce', 'highestce', 'lowestce', 'actions');
            $site_url = base_url();
            if (isset($DTRenderArray['ResultSet'])) {
                foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                    $row = array();
                    $TotalHeader = count((array)$dtDisplayColumns);
                    for ($i = 0; $i < $TotalHeader; $i++) {
                        if ($dtDisplayColumns[$i] == "PlusBtn") {
                            $row[] = $dtRow['trainer_id'];
                        } else if ($dtDisplayColumns[$i] == "trainer_name") {
                            $action = '<h5>' . $dtRow['trainer_name'] . '</h5>';
                            $row[] = $action;
                        } else if ($dtDisplayColumns[$i] == "avgce") {
                            $rowvalue = $dtRow['avgce'] . "%";
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "actions") {
                            $row[] = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">                                
                                    <li>
                                        <a href="' . $site_url . 'trainer_dashboard/index/' . $dtRow['trainer_id'] . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . $site_url . 'trainer_workshop/index/' . $dtRow['trainer_id'] . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Workshop
                                        </a>
                                    </li>                                                                
                                    <li>
                                        <a href="' . $site_url . 'trainer_comparison/index/' . $dtRow['trainer_id'] . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Comparison
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . $site_url . 'trainer_accuracy/index/' . $dtRow['trainer_id'] . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Accuracy
                                        </a>
                                    </li>
                                </ul>
                            </div>';
                        } else if ($dtDisplayColumns[$i] == "highestce") {
                            $rowvalue = $dtRow['highestce'] . "%";
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "lowestce") {
                            $rowvalue = ($dtRow['highestce'] == $dtRow['lowestce'] ? '-' : $dtRow['lowestce'] . "%" );
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] != ' ') {
                            $row[] = $dtRow[$dtDisplayColumns[$i]];
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        } else if ($reportsby_id == 1 || $reportsby_id == '') {
            $dtSearchColumns = array('region_name');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
            $dtOrder = $DTRenderArray['dtOrder'];
            $dtLimit = $DTRenderArray['dtLimit'];
            $dtWhere = $DTRenderArray['dtWhere'];
            if ($dtWhere != "") {
                $dtWhere .=$query;
            } else {
                $dtWhere .=" WHERE 1=1 " . $query;
            }
            $DTRenderArray = $this->supervisor_reports_model->getRegionWiseData($dtLimit, $dtWhere);
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
            $dtDisplayColumns = array('PlusBtn', 'region_name', 'total_workshop', 'trainee_trained', 'avgce', 'highestce', 'lowestce', 'PlusBtn');
            $site_url = base_url();
            if (isset($DTRenderArray['ResultSet'])) {
                foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                    $row = array();
                    $TotalHeader = count((array)$dtDisplayColumns);
                    for ($i = 0; $i < $TotalHeader; $i++) {
                        if ($dtDisplayColumns[$i] == "PlusBtn") {
                            $row[] = $dtRow['region_id'];
                        } else if ($dtDisplayColumns[$i] == "region_name") {
                            $action = '<span class="row-details "><h5>' . $dtRow['region_name'] . '</h5></span>';
                            $row[] = $action;
                        } else if ($dtDisplayColumns[$i] == "avgce") {
                            $rowvalue = $dtRow['avgce'] . "%";
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "highestce") {
                            $rowvalue = $dtRow['highestce'] . "%";
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "lowestce") {
                            $rowvalue = ($dtRow['highestce'] == $dtRow['lowestce'] ? '-' : $dtRow['lowestce'] . "%" );
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] != ' ') {
                            $row[] = $dtRow[$dtDisplayColumns[$i]];
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        } else if ($reportsby_id == 4) {
            $site_url = base_url();
            $dtSearchColumns = array('workshop_name', 'wtm.workshop_type');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
            $dtWhere = $DTRenderArray['dtWhere'];
            $dtOrder = $DTRenderArray['dtOrder'];
            $dtLimit = $DTRenderArray['dtLimit'];
            if ($dtWhere != "") {
                $dtWhere .=$query;
            } else {
                $dtWhere .=" WHERE 1=1 " . $query;
            }

            $DTRenderArray = $this->supervisor_reports_model->getWorkshopWiseData($dtLimit, $dtWhere);

            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
            $dtDisplayColumns = array('PlusBtn', 'workshop_name', 'workshop_type_name', 'trainee_trained' ,'avgce', 'highest_topic', 'lowest_topic', 'PlusBtn');
            $site_url = base_url();

            if (isset($DTRenderArray['ResultSet'])) {
                foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                    $row = array();
                    $dtWhere2 = " WHERE wr.workshop_id=" . $dtRow['workshop_id'] . $query;
                    $getWorkshopTopic = $this->supervisor_reports_model->getWorkshopTopicdt($dtWhere2, $dtRow['workshop_id']);
                    $TotalHeader = count((array)$dtDisplayColumns);
                    for ($i = 0; $i < $TotalHeader; $i++) {
                        if ($dtDisplayColumns[$i] == "PlusBtn") {
                            $row[] = $dtRow['workshop_id'];
                        } else if ($dtDisplayColumns[$i] == "workshop_name") {
                            $action = '<span class="row-details "><h5>' . $dtRow['workshop_name'] . '</h5></span>';
                            $row[] = $action;
                        } else if ($dtDisplayColumns[$i] == "avgce") {
                            if ($dtRow['avgce'] == 'NP') {
                                $rowvalue = $dtRow['avgce'];
                            } else {
                                $rowvalue = $dtRow['avgce'] . "%";
                            }
                            $row[] = $rowvalue;
                        } else if ($dtDisplayColumns[$i] == "highest_topic") {
                            $row[] = $getWorkshopTopic['BesicTopic'];
                        } else if ($dtDisplayColumns[$i] == "lowest_topic") {
                            $row[] = $getWorkshopTopic['WorstTopic']; //($dtRow['highest_topic'] !=$dtRow['lowest_topic'] ? $dtRow['lowest_topic'] :'-');
                        } else if ($dtDisplayColumns[$i] != ' ') {
                            $row[] = $dtRow[$dtDisplayColumns[$i]];
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        }
        echo json_encode($output);
    }

    public function getTrainerData() {
        $site_url = base_url();
        $id = $this->input->post('id', true);
        $reportby_id = $this->input->post('reportby_id', true);
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Trainerlab =""; 
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
            $login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
         $workshoptype_id = $this->input->post('workshoptype_id', true);
         $workshop_subtype = $this->input->post('workshop_subtype', true);
         $wregion_id = $this->input->post('wregion_id', true);
         $wsubregion_id = $this->input->post('wsubregion_id', true);
         $reportby_id =$this->input->post('reportby_id', true);
   
        $WhereCond = " AND wr.company_id= " . $company_id;
        if ($reportby_id != 2) {
            if ($workshoptype_id != "0") {            
                   $WhereCond .= " AND w.workshop_type  = " . $workshoptype_id; 
               } 
        }
            if ($workshop_subtype != '') {
                    $WhereCond .= " AND w.workshopsubtype_id  = " . $workshop_subtype;
            }
        if ($reportby_id != 1 || $reportby_id != '') {
                   if ($wregion_id != "0") {
                       $WhereCond .= " AND w.region  = " . $wregion_id;
                   }
        }
                   if ($wsubregion_id != '') {
                           $WhereCond .= " AND w.workshopsubregion_id  = " . $wsubregion_id;
                   }         
          
        if (!$WRightsFlag) {
            if (!$WRightsFlag) {
                $WhereCond .= " AND wr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
            }
        }
        if (!$RightsFlag) {
            $WhereCond .= " AND (wr.trainer_id = $login_id OR wr.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        
        if ($reportby_id == 1 || $reportby_id == '') {
            $TrainerArray = $this->supervisor_reports_model->getRegionWiseTrainer($id, $WhereCond);
            $Trainerlab = "Sub-region";
        } else if ($reportby_id == 2) {
            $TrainerArray = $this->supervisor_reports_model->getWTypeWiseTrainer($id, $WhereCond);
            $Trainerlab = "Sub-type";
        } else if ($reportby_id == 4) {
            $TrainerArray = $this->supervisor_reports_model->getWorkshopWiseTrainer($id, $WhereCond);
            $Trainerlab = "Trainer Name";
        }
        $Table = '<table class="table table-bordered  " id="trainertable" width="50%">
                    <thead >                        
                        <tr class="uppercase" style="background-color: #e6f2ff;">
                            <th>'.$Trainerlab.'</th>                        
                            <th>Avg C.E %</th>
                            <th>Trainee Trained</th>
                            <th>Highest C.E %</th>
                            <th>Lowest C.E %</th>
                            <th>Action</th>
                        </tr>
                    </thead><tbody>';
        if (count($TrainerArray) > 0) {
            $ActionFlag=true;
            foreach ($TrainerArray as $value) {
                    if ($reportby_id == 1 || $reportby_id == '') {
//                             $Trainersub = '<a href="javascript:void(0)" onclick="get_trainerlist('.$value->workshopsubregion_id.','.$value->region_id.')" >' . $value->workshop_subregion. '</a>';
                          $Trainersub ='<button class="btn btn-xs blue " type="button" onclick="get_trainerlist('.$value->workshopsubregion_id.','.$value->region_id.')"> 
                                   Details
                                </button>';
                             $Trainerdata = $value->workshop_subregion;
                             $ActionFlag=false;
                      } else if ($reportby_id == 2) {
//                            $Trainersub = '<a href="javascript:void(0)" onclick="get_trainerlist('.$value->workshopsubtype_id.','.$value->workshop_type.')" >' . $value->workshop_subtype. '</a>';                          
                           $Trainersub ='<button class="btn btn-xs blue " type="button" onclick="get_trainerlist('.$value->workshopsubtype_id.','.$value->workshop_type.')"> 
                                   Details
                                </button>';
                            $Trainerdata = $value->workshop_subtype;
                            $ActionFlag=false;
                      } else if ($reportby_id == 4) {
                             $Trainerdata = $value->trainer_name;
                      }
                $Table .='<tr>
                          
                            <td>' . $Trainerdata . '</td>
                            <td>' . $value->avgce . ' %</td>
                            <td>' . $value->trainee_trained . '</td>
                            <td>' . $value->highestce . ' %</td>
                            <td>' .  $rowvalue = ($value->highestce == $value->lowestce ? '-' : $value->lowestce . "%" ) . ' </td>';
                        if($ActionFlag){
                            $Table .='<td><div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">                                
                                    <li>
                                        <a href="' . $site_url . 'trainer_dashboard/index/' . $value->trainer_id . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . $site_url . 'trainer_workshop/index/' . $value->trainer_id . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Workshop
                                        </a>
                                    </li>                                                                
                                    <li>
                                        <a href="' . $site_url . 'trainer_comparison/index/' . $value->trainer_id . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Comparison
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . $site_url . 'trainer_accuracy/index/' . $value->trainer_id . '" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Accuracy
                                        </a>
                                    </li>
                                </ul>
                            </div></td>';
                        }else{
                             $Table .='<td>'.$Trainersub.'</td>';
            }
                            
                        $Table .='</tr>';
        }
        }
        $Table .="</tbody></table>";
        $data['Table'] = $Table;
        $data['Error'] = '';
        echo json_encode($data);
    }
    public function trainer_list($sub_id,$id) {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id');
        } else {
            $company_id = $this->mw_session['company_id'];
}
         $workshoptype_id = $this->input->post('workshoptype_id', true);
         $workshop_subtype = $this->input->post('workshop_subtype', true);
         $wregion_id = $this->input->post('wregion_id', true);
         $wsubregion_id = $this->input->post('wsubregion_id', true);
         $reportsby_id =$this->input->post('reportsby_id', true);
         
       $dtWhere = " WHERE wr.company_id= " . $company_id;
       if($reportsby_id==1){
            if ($id != '0') {
                    $dtWhere .= " AND w.region  = " . $id;
            }
            if ($sub_id != '') {
                    $dtWhere .= " AND w.workshopsubregion_id  = " . $sub_id;
            }
            if ($workshoptype_id != "0") {            
                $dtWhere .= " AND w.workshop_type  = " . $workshoptype_id; 
            } 
            if ($workshop_subtype != '') {
                    $dtWhere .= " AND w.workshopsubtype_id  = " . $workshop_subtype;
            }
       }else{
            if ($id != '0') {
                    $dtWhere .= " AND w.workshop_type  = " . $id;
            } 
            if ($sub_id != '') {
                    $dtWhere .= " AND w.workshopsubtype_id  = " . $sub_id;
            }
            if ($wregion_id != "0") {
                $dtWhere .= " AND w.region  = " . $wregion_id;
            }
            if ($wsubregion_id != '') {
                    $dtWhere .= " AND w.workshopsubregion_id  = " . $wsubregion_id;
            }
       }
        $data['traineedata'] = $this->supervisor_reports_model->getsubregionWiseTrainer($dtWhere);
        $this->load->view('supervisor_report/trainer_list', $data);
    }
}