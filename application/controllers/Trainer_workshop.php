<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_workshop extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_workshop');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainer_workshop_model');
        }

    public function index($trainer_id = '') {
        $data['module_id'] = '25.1';

        $data['username'] = $this->mw_session['username'];
        $data['trainee_name'] = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        $company_id = $this->mw_session['company_id'];
        $data['user_id'] = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;
        $data['trainer_id'] = $trainer_id;
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($company_id == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
            if($trainer_id !=""){
                $Rowset = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $trainer_id);
                $company_id = $Rowset->company_id;
                $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
            }
        } else {
            $data['company_array'] = array();
            $login_id = $this->mw_session['user_id'];
            $this->load->model('trainee_reports_model');
            $this->trainee_reports_model->SynchTraineeData($company_id);
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
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
                $this->common_model->SyncWorkshopRights($login_id,0);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($company_id);
            }
        }
        $data['Supcompany_id'] = $company_id;
        $data['wksh_top_five_array'] = [];
        $this->load->view('trainer_workshop/index', $data);
    }

    public function ajax_company_trainer_type() {
        $RightsFlag=1;
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
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Trainer_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
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
    public function load_workshop() {
        
        $dtSearchColumns = array('a.workshop_id', 'w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->get('company_id', TRUE);
            $this->load->model('trainee_reports_model');
            $this->trainee_reports_model->SynchTraineeData($company_id);
        } else {
            $company_id = $this->mw_session['company_id'];
            $login_id  =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        $trainer_id = ($this->input->get('user_id', TRUE) !='' ? $this->input->get('user_id', TRUE) : 0);        
        $workshop_type_id = $this->input->get('workshop_type_id', TRUE);
        if($workshop_type_id==""){
            $workshop_type_id=0;
        }
        $Workshop_id = $this->input->get('workshop_id', TRUE);
        if($Workshop_id==null || $Workshop_id==''){
            $Workshop_id=0;
        }
        if($dtWhere !=""){
            $dtWhere .= " AND a.company_id=$company_id ";
        }else{
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
        if(!$WRightsFlag){
            $dtWhere .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $dtWhere .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $dtWhere .= " AND a.trainer_id= " . $trainer_id;
        }
        $DTRenderArray = $this->trainer_workshop_model->getTrainerWorkshop($dtWhere,$dtOrder, $dtLimit);
        if (count((array)$DTRenderArray['ResultSet'])>0) {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array(),
                "top_five_table" =>'',
                "bottom_five_table" =>''
            );
            $Custom_url = base_url().'trainer_individual/index/'.base64_encode($company_id).'/'.base64_encode($trainer_id);
            $dtDisplayColumns = array('workshop_id', 'workshop_name', 'total_topic', 'avg_ce', 'no_trainee', 'actions');
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $workshop_id = $dtRow['workshop_id'];
                $live_workshop = $dtRow['live_workshop'];
                $worksRowSet = $this->trainer_workshop_model->workshop_statistics($RightsFlag,$company_id, $trainer_id, $workshop_id,$live_workshop);
                if(count((array)$worksRowSet['CEData'])==0){
                    continue;
                }
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == "total_topic") {
                        $row[] = $worksRowSet['total']->total_topic;
                    } else if ($dtDisplayColumns[$i] == "no_trainee") {
                        $row[] = $worksRowSet['total']->total_trainee;
                    }else if ($dtDisplayColumns[$i] == "avg_ce") {
                        $row[] = $worksRowSet['CEData']->ce . ($worksRowSet['CEData']->ce != 'NP' ? '%' : '');
                    } else if ($dtDisplayColumns[$i] == "actions") {
                        $action = '<a data-toggle="modal" href="javascript:void(0)" onclick="workshop_summary(' . $workshop_id . ')" class="btn btn-xs blue">
                                    <i class="fa fa-bar-chart"></i> SUMMARY
                                </a>
                                <a data-toggle="modal" href="javascript:void(0)" onclick="workshop_detail(' . $workshop_id . ')" class="btn btn-xs red">
                                    <i class="fa fa-bar-chart"></i> DETAIL
                                </a>
                                <a data-toggle="modal" href="javascript:void(0)" onclick="workshop_trainee(' . $workshop_id . ')" class="btn btn-xs yellow">
                                    <i class="fa fa-bar-chart"></i> TRAINEE
                                </a>
                                <a href="'.$Custom_url.'/'.base64_encode($workshop_id).'/'.base64_encode($workshop_type_id).'" target="_blank"  class="btn btn-xs purple">
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
                $top_five_result = $this->trainer_workshop_model->top_five_trainee($dtWhere);
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
                        $htmlTopFive .='<tr>
                                            <td>' . $topfive->trainee_name . '</td>
                                            <td>' . $topfive->pre_average . '</td>
                                            <td>' . $topfive->post_average . '</td>
                                            <td>' . $tf_ce . '</td>
                                            <td>' . $tf_rank . '</td>
                                        </tr>';
                    }
                    $htmlTopFive .='</tbody></table>';
                } else {
                    $htmlTopFive .='<tr>
                                        <td colspan="5">No Participant</td>
                                    </tr>';
                    $htmlTopFive .='</tbody></table>';
                }
                if ($top_five_trainee_id != '') {
                    $top_five_trainee_id = substr($top_five_trainee_id, 0, strlen($top_five_trainee_id) - 1);
                }
                //BOTTOM 5 TRAINEE
                $bottom_five_result = $this->trainer_workshop_model->bottom_five_trainee($dtWhere, $top_five_trainee_id);
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
                        $htmlBottomFive .='<tr>
                                            <td>' . $topfive->trainee_name . '</td>
                                            <td>' . $topfive->pre_average . '</td>
                                            <td>' . $topfive->post_average . '</td>
                                            <td>' . $tf_ce . '</td>
                                            <td>' . $tf_rank . '</td>
                                        </tr>';
                    }
                    $htmlBottomFive .='</tbody></table>';
                } else {
                    $htmlBottomFive .='<tr>
                                        <td colspan="5">No Participant</td>
                                    </tr>';
                    $htmlBottomFive .='</tbody></table>';
                }
                $output['top_five_table'] = $htmlTopFive;
                $output['bottom_five_table'] = $htmlBottomFive;
        }else{
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array(),
                "top_five_table" =>'',
                "bottom_five_table" =>''
            );
        }
        echo json_encode($output);
    }

    public function load_wksh_summary() {
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) !='' ? $this->input->post('user_id', TRUE) : 0);        
        $workshop_id = $this->input->post('workshop_id', TRUE);
        
        $dtWhere = " WHERE a.company_id=$company_id ";
        if ($workshop_id != "0") {
            $dtWhere .= " AND a.workshop_id= " . $workshop_id;
        }
        if(!$WRightsFlag){
            $dtWhere .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $dtWhere .= " AND (a.trainer_id = $login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
            }
        } else {
            $dtWhere .= " AND a.trainer_id= " . $trainer_id;
        }
        $this->load->model('trainee_reports_model');
        $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
        if ($islive_workshop) {
            $workshop_overall_statistics = $this->trainee_reports_model->getLivePrePostWorkshopwise($workshop_id, $trainer_id,$RightsFlag);
        } else {
            $workshop_overall_statistics = $this->trainee_reports_model->getPrePostWorkshopwise($workshop_id, $trainer_id,$RightsFlag);
        }
        $trainer_topic_wise_ce_array = $this->trainer_workshop_model->trainer_topic_wise_ce($dtWhere,$workshop_id);
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
        $top_five_result = $this->trainer_workshop_model->top_five_trainee($dtWhere,$workshop_id);
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
                $htmlTopFive .='<tr>
                                    <td>' . $topfive->trainee_name . '</td>
                                    <td>' . $topfive->pre_average . '</td>
                                    <td>' . $topfive->post_average . '</td>
                                    <td>' . $tf_ce . '</td>
                                    <td>' . $tf_rank . '</td>
                                </tr>';
            }
            $htmlTopFive .='</tbody></table>';
        } else {
            $htmlTopFive .='<tr>
                                <td colspan="5">No Participant</td>
                            </tr>';
            $htmlTopFive .='</tbody></table>';
        }
        if ($top_five_trainee_id != '') {
            $top_five_trainee_id = substr($top_five_trainee_id, 0, strlen($top_five_trainee_id) - 1);
        }
        //BOTTOM 5 TRAINEE
        $bottom_five_result = $this->trainer_workshop_model->bottom_five_trainee($dtWhere, $top_five_trainee_id,$workshop_id);
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
                $htmlBottomFive .='<tr>
                                    <td>' . $topfive->trainee_name . '</td>
                                    <td>' . $topfive->pre_average . '</td>
                                    <td>' . $topfive->post_average . '</td>
                                    <td>' . $tf_ce . '</td>
                                    <td>' . $tf_rank . '</td>
                                </tr>';
            }
            $htmlBottomFive .='</tbody></table>';
        } else {
            $htmlBottomFive .='<tr>
                                <td colspan="5">No Participant</td>
                            </tr>';
            $htmlBottomFive .='</tbody></table>';
        }

        if (count((array)$workshop_overall_statistics) > 0) {
            $Pre_avg = $workshop_overall_statistics->pre_average;
            $Post_avg = $workshop_overall_statistics->post_average;
            $CE = $Post_avg - $Pre_avg . '%';
            if ($Pre_avg == 0) {
                $Pre_avg = "NP";
                $CE = "NP";
            } else {
                $Pre_avg .="%";
            }
            if ($Post_avg == 0) {
                $Post_avg = "NP";
                $CE = "NP";
            } else {
                $Post_avg .="%";
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
                                <div id='topic_wise_ce' style='height:".(count((array)$label)>5 ? '500':'400')."px'></div>
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

    public function load_wksh_detail() {
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) !='' ? $this->input->post('user_id', TRUE) : 0);        
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $this->load->model('trainee_reports_model');
        
        $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
        $PreSessionLive = false;
        $PostSessionLive = false;
        if ($islive_workshop) {
            $PostSessionLive = true;
            $trainee_result = $this->common_model->get_value('trainee_result', 'workshop_id', 'workshop_id=' . $workshop_id);
            if (count((array)$trainee_result) == 0) {
                $PreSessionLive = true;
            }
            $workshop_overall_statistics = $this->trainee_reports_model->getLivePrePostWorkshopwise($workshop_id, $trainer_id,$RightsFlag);
        } else {
            $workshop_overall_statistics = $this->trainee_reports_model->getPrePostWorkshopwise($workshop_id, $trainer_id,$RightsFlag);
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
        $trainer_topic_wise_ce_array = $this->trainer_workshop_model->trainer_topic_subtopic_wise_ce($RightsFlag,$islive_workshop,$trainer_id, $workshop_id);


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
                $Pre_avg .="%";
            }
            if ($Post_avg == 0) {
                $Post_avg = "NP";
                $CE = "NP";
            } else {
                $Post_avg .="%";
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
        $histogram_pre = $this->trainer_workshop_model->wksh_trainer_histogram($RightsFlag,$PreSessionLive, $trainer_id, $workshop_id, 'PRE');
        foreach ($histogram_pre as $range) {
            $wksh_label_pre[] = $range->from_range . "-" . $range->to_range . "%";
            $wksh_dataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_post = $this->trainer_workshop_model->wksh_trainer_histogram($RightsFlag,$PostSessionLive, $trainer_id, $workshop_id, 'POST');
        foreach ($histogram_post as $range) {
            $wksh_label_post[] = $range->from_range . "-" . $range->to_range . "%";
            $wksh_dataset_post[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_TopicCount_pre = $this->trainer_workshop_model->wksh_topic_histogram($RightsFlag,$PreSessionLive, $trainer_id, $workshop_id, 'PRE');
        foreach ($histogram_TopicCount_pre as $range) {
            $wksh_Topicdataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_TopicCount_post = $this->trainer_workshop_model->wksh_topic_histogram($RightsFlag,$PostSessionLive, $trainer_id, $workshop_id, 'POST');
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
                                <div id='topic_subtopic_ce' style='height:".(count((array)$label)>5 ? '600':'400')."px'></div>
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

    public function load_wksh_trainee() {
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) !='' ? $this->input->post('user_id', TRUE) : 0);        
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $this->load->model('trainee_reports_model');
        $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
        if ($islive_workshop) {
            $trainer_topic_wise_ce_array = $this->trainee_reports_model->getLivePrePostData($workshop_id, '', $trainer_id,$RightsFlag);
        } else {
            $trainer_topic_wise_ce_array = $this->trainee_reports_model->getPrePostData($workshop_id, '', $trainer_id,$RightsFlag);
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
                if ($ttwcea->pre_average == 'Not Played' || $ttwcea->post_average== 'Not Played') {
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
                $lcOption .='<option value="' . $user_id . '">' . $user_name . '</option>';
            }
        }
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; ' >
                                <div id='topic_subtopic_ce' style='height:".(count((array)$label)>5 ? '1000':'500')."px'></div>
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
                                        text: 'Trainer Name: " . $trainer_name."'
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

    public function gettraineewise_topic() {
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $this->load->model('trainee_reports_model');
        $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $trainer_id = ($this->input->post('user_id', TRUE) !='' ? $this->input->post('user_id', TRUE) : 0);        
        if ($workshop_id != "0") {
            $WorshopSet = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $workshop_name = $WorshopSet->workshop_name;
        }
        if ($trainee_id != "0") {
            $TraineeSet = $this->common_model->get_value('device_users', 'CONCAT(firstname," ",lastname) as name', 'user_id=' . $trainee_id);
            $trainee_name = $TraineeSet->name;
        }
         $RightsFlag=1; 
        if($this->mw_session['company_id'] !="" && !$this->mw_session['superaccess']){
            $Login_id  =$this->mw_session['user_id'];
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
            $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        $trainer_topic_wise_ce_array = $this->trainer_workshop_model->trainer_topic_subtopic_wise_ce($RightsFlag,$islive_workshop,$trainer_id, $workshop_id, $trainee_id);
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
        $lcHtml = "<div id='trainee_subtopic_ce' style='height:".(count((array)$label)>5 ? '600':'400')."px'></div>";
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
                                        text: 'Workshop Name:".$workshop_name."',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: 'Trainee Name:".$trainee_name."'
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

    // public function load_wksh_individual(){
    //     $workshop_id       = $this->input->post('workshop_id', TRUE);
    //     $report_name       = 'trainer_workshop_table';
    //     $rpt_token         = $this->mw_session['user_token'];
    //     $individual_result = $this->trainer_workshop_model->wksh_individual($report_name,$rpt_token,$workshop_id);
    //     $html_individual_table = '';
    //     if (count((array)$individual_result)>0){
    //         $html_individual_table ='
    //                 <table class="table table-hover table-light">
    //                     <thead>
    //                         <tr class="uppercase" style="background-color: #e6f2ff;">
    //                             <th width="45%">TRAINEE NAME</th>
    //                             <th width="12%">C.E</th>
    //                             <th width="12%">POST ACCURACY</th>
    //                             <th width="12%">NO. OF TOPICS</th>
    //                         </tr>
    //                     </thead><tbody>';
    //         foreach ($individual_result as $ind_row) {
    //             $ind_user_name     = $ind_row->user_name;
    //             $ind_pre_accuracy  = $ind_row->pre_average_accuracy;
    //             $ind_post_accuracy = $ind_row->post_average_accuracy;
    //             $ind_ce            = $ind_row->ce;
    //             $ind_topic_count   = $ind_row->topic_count;
    //             $html_individual_table .='<tr>
    //                             <td style="width: 45%;">'.$ind_user_name.'</td>
    //                             <td style="width: 12%;">'.$ind_ce.'%</td>
    //                             <td style="width: 12%;">'.$ind_post_accuracy.'%</td>
    //                             <td style="width: 12%;">'.$ind_topic_count.'</td>
    //                         </tr>';
    //         }
    //     }
    //     $html_individual_table .='</tbody></table>';
    //     $data['trainee_individual_table'] = $html_individual_table;
    //     echo json_encode($data);
    // }
}
