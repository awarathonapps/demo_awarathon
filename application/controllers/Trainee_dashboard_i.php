<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Trainee_dashboard_i extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainee_post_accuracy_table');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('trainee_reports_model');
    }

    public function index() {
        $data['module_id'] = '26.1';
        $data['company_id'] = $this->mw_session['company_id'];
        $Trainee_id = '';
        $RightsFlag=1;
        $WRightsFlag=1;
        
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['company_array'] = array();
            if ($this->mw_session['login_type'] != 3) {
                $Login_id  =$this->mw_session['user_id'];
                if(!$this->mw_session['superaccess']){
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                    $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
                    $this->common_model->SyncWorkshopRights($Login_id,0);
                }
                
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'],$WRightsFlag);
                $data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'],$WRightsFlag);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'],$WRightsFlag);
            } else {
                $Trainee_id = $this->mw_session['user_id'];
                //$data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'],1);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'],1);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'],1);
            }
        }
        $data['TraineeRegionData'] = $this->trainee_reports_model->get_TraineeRegionData($data['company_id']);
        $data['acces_management'] = $this->acces_management;
        $data['DefaultTrainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        $this->load->view('trainee_dashboard_i/index', $data);
    }

    public function ajax_getTraineeData() {
        $dtSearchColumns = array('w.start_date', 'w.workshop_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $login_id  =$this->mw_session['user_id'];
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($this->mw_session['login_type'] == "3") {
            $trainee_id = $this->mw_session['user_id'];
            $RightsFlag=1;
            $WRightsFlag=1;
        } else {
            $trainee_id = ($this->input->get('trainee_id') ? $this->input->get('trainee_id') : '');
            if($this->mw_session['company_id'] !="" && !$this->mw_session['superaccess']){
               $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$login_id);
               $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
               $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0); 
            }
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshoptype_id != "0" ) {
            if($dtWhere !=""){
                $dtWhere .= " AND w.workshop_type  = " . $workshoptype_id;
            }else{
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
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id'): '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        if ($trainee_id != "") {
            $this->trainee_reports_model->SynchTraineeData($company_id);
            $DTRenderArray = $this->trainee_reports_model->getTraineeData($company_id,$workshoptype_id, $trainee_id,$dtOrder, $dtLimit, $dtWhere,$RightsFlag,$WRightsFlag);
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
                        $row[] = ($dtRow['post_average'] != 'NP' ? $dtRow['post_average'] . '%' : 'Not Played' );
                    } else if ($dtDisplayColumns[$i] == "avgresponcetime") {
                        $row[] = $dtRow['avgresponcetime'] . ' Sec';
                    } else if ($dtDisplayColumns[$i] == "Actions") {
                        $action = "<a data-target='#LoadModalFilter' data-toggle='modal' href='" . $site_url . "trainee_dashboard_i/summary_ajax_chart/" . base64_encode($dtRow['workshop_id']) . "/" . base64_encode($trainee_id) . "/' class='btn btn-xs blue margin-bottom'><i class='fa fa-bar-chart'></i> SUMMARY</a>"
                                . "<a data-target='#LoadModalFilter' data-toggle='modal' href='" . $site_url . "trainee_dashboard_i/detail_ajax_chart/" . base64_encode($dtRow['workshop_id']) . "/" . base64_encode($trainee_id) . "' class='btn btn-xs red margin-bottom'><i class='fa fa-bar-chart'></i> DETAIL</a>";
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

    public function ajax_companywiseData() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id,1);
        $data['TraineeResult'] = $this->common_model->getUserTraineeList($company_id,1);
        echo json_encode($data);
    }

    public function getTraineeWorkshopData() {
        if ($this->mw_session['login_type'] == 3) {
            $trainee_id = $this->mw_session['user_id'];
        } else {
            $trainee_id = $this->input->post('trainee_id', TRUE);
        }
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $data['WorkshopResultSet'] = $this->common_model->getUserWorkshopList('', $trainee_id, $workshoptype_id);
        echo json_encode($data);
    }

    public function summary_ajax_chart($dworkshop_id = '', $dtrainee_id = '') {
        $Table = '';
        $MainTable = '';
        $error = '';
        $Label = [];
        $dataset = [];
        $data['module_id'] = '24.3';
        $workshop_id = base64_decode($dworkshop_id);
        $trainee_id = base64_decode($dtrainee_id);
        $RightsFlag=1;
        $WRightsFlag=1;
        $Login_id  =$this->mw_session['user_id'];
        if ($this->mw_session['login_type'] != 3) {
            if($this->mw_session['company_id'] !="" && !$this->mw_session['superaccess']){
               $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
               $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
               $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0); 
            }
        }
        
        if ($workshop_id != '' && $trainee_id != '') {
            $islive_workshop =$this->trainee_reports_model->isWorkshopLive($workshop_id);
            if($islive_workshop){
                $ResultSetData = $this->trainee_reports_model->getLivePrePostData($workshop_id, $trainee_id);
                $data['PrePostMainData'] = $this->trainee_reports_model->get_LivePrePostTopicwise($workshop_id, $trainee_id);
            }else{
                $ResultSetData = $this->trainee_reports_model->getPrePostData($workshop_id, $trainee_id);
                $data['PrePostMainData'] = $this->trainee_reports_model->get_PrePostTopicwise($workshop_id, $trainee_id);
            }
            $RankData = $this->trainee_reports_model->get_Traineewise_Rank($workshop_id, $trainee_id,$islive_workshop);
            if(count((array)$RankData)>0){
                $Rank = $RankData[0]->rank;
            }else{
                $Rank = "-";
            }
            $PrePostData =$ResultSetData[0];
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
                $Table .='<tr id="datatr">
                        <td>' . $PrePostData->trainee_name . '</td>
                        <td>' . $pre_average . '</td>
                        <td>' . $post_average . '</td>
                        <td>' . $ceTable . '</td>
                        <td>.' . $Rank . '</td>
                        </tr>';
            }
            $Table .="</tbody></table>";
            
//            echo "<pre>";
//            print_r($data['PrePostMainData']);
//            exit;
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
                    $MainTable .='<tr id="datatr">
                        <td>' . $value->topic . '</td>
                        <td>' . $value->subtopic . '</td>
                        <td>' . ($value->pre_status> 0 ? 'Not Played' : $value->pre_average.'%' ) . '</td>
                        <td>' .($value->post_status> 0 ? 'Not Played' : $value->post_average.'%' ) . '</td>
                        <td>' . ($value->pre_status> 0 || $value->post_status  ? 'Not Played' : $value->ce.'%' ) . '</td>
                    </tr>';
                }
            }
            $MainTable .="</tbody></table>";
            $data['Table'] = $Table;
            $data['MainTable'] = $MainTable;
            $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $data['dataset1'] = json_encode($dataset1, JSON_NUMERIC_CHECK);
            $Rdata['totallabel'] =count((array)$Label);
            $data['label'] = json_encode($Label);

            $this->load->view('trainee_dashboard_i/show_summary_report', $data);
        } else {
            $error = "Invalid Filter Selections...";
        }
    }

    public function detail_ajax_chart($workshop_id = '', $trainee_id = '') {
        $Table = '';
        $MainTable = '';
        $error = '';
        $Label = [];
        $dataset = [];
        $data['module_id'] = '24.3';
        $workshop_id = base64_decode($workshop_id);
        $trainee_id = base64_decode($trainee_id);
        if ($workshop_id != '' && $trainee_id != '') {
            $islive_workshop =$this->trainee_reports_model->isWorkshopLive($workshop_id);
            if($islive_workshop){
               $ResultSetData = $this->trainee_reports_model->getLivePrePostData($workshop_id, $trainee_id);
            }else{
                $ResultSetData = $this->trainee_reports_model->getPrePostData($workshop_id, $trainee_id);
            }
             
            $PrePostData =$ResultSetData[0];
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
            $Table .='<tr id="Mwrk' . $trainee_id . '" class="trClickeble" onclick="WorkshopWiseTopicSubtopicGraph(' . $workshop_id . ',' . $trainee_id . ')">
                   <td>' . $PrePostData->workshop_name . '</td>
                   <td>' . $pre_average . '</td>
                   <td>' . $post_average . '</td>
                   <td>' . $ceTable . '</td>                        
                   </tr>';
            $Table .="</tbody></table>";

            $data['Table'] = $Table;
            $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $Rdata['totallabel'] =count((array)$Label);
            $data['label'] = json_encode($Label);
            $data['Trainee_name'] = $PrePostData->trainee_name;

            $this->load->view('trainee_dashboard_i/show_detail_report', $data);
        } else {
            $error = "Please Select Company,Workshop And Trainee";
        }
    }

    public function Detail_TopicSubtopicChart() {
        $Label = [];
        $QATable = '';
        $datasetpre = [];
        $datasetpost = [];
        $datasetCE = [];

        $trainee_id = $this->input->post('trainee_id', true);
        $workshop_id = $this->input->post('workshop_id', true);
        
        $islive_workshop =$this->trainee_reports_model->isWorkshopLive($workshop_id);
        if($islive_workshop){
            $QuestionAnsData = $this->trainee_reports_model->getLivePrePostQuestionAnsData($workshop_id, $trainee_id);
             $TopicSubtopicDataArray = $this->trainee_reports_model->get_LivePrePostTopicwise($workshop_id, $trainee_id);
        }else{
            $QuestionAnsData = $this->trainee_reports_model->getPrePostQuestionAnsData($workshop_id, $trainee_id);
            $TopicSubtopicDataArray = $this->trainee_reports_model->get_PrePostTopicwise($workshop_id, $trainee_id);
        }
        if (count((array)$TopicSubtopicDataArray) > 0) {
            foreach ($TopicSubtopicDataArray as $value) {
                $Label[] = $value->topic .($value->subtopic !='No sub-Topic' ? '-'.$value->subtopic:'') ;
                $datasetpre[] = $value->pre_average;
                $datasetpost[] = $value->post_average;
                $ce = $value->ce;
                if($value->pre_status >0 || $value->post_status>0){
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
             $QATable .='<tr>
                <td>PRE</td>
                <td>' . $QuestionAnsData->pre_total_questions . '</td>
                <td>' . $QuestionAnsData->pre_correct . '</td>                                                              
                </tr><tr><td>POST</td>
                <td>' . $QuestionAnsData->post_total_questions . '</td>
                <td>' . $QuestionAnsData->post_correct . '</td>                                                              
                </tr>';
        }
        $QATable .="</tbody></table>";

        $Rdata['datasetpre'] = json_encode($datasetpre, JSON_NUMERIC_CHECK);
        $Rdata['datasetpost'] = json_encode($datasetpost, JSON_NUMERIC_CHECK);
        $Rdata['dataset3'] = json_encode($dataset3, JSON_NUMERIC_CHECK);
        $Rdata['dataset4'] = json_encode($dataset4, JSON_NUMERIC_CHECK);
        $Rdata['totallabel'] =count((array)$Label);
        $Rdata['label'] = json_encode($Label);

        $showreport = $this->load->view('trainee_dashboard_i/show_detail_topicsubtopic', $Rdata, true);
        $data['Error'] = '';
        $data['HTMLGraphData'] = $showreport;
        $data['QATable'] = $QATable;
        echo json_encode($data);
    }
    public function getTraineeData(){
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }        
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $lcTrainee_html='<option value="">Select Trainee</option>';
        if($trainee_region_id !='0'){
            $TraineeData = $this->common_model->get_selected_values('device_users','user_id,concat(firstname," ",lastname) as traineename','company_id='.$company_id.' and region_id='.$trainee_region_id);
        }        
        else{
            $TraineeData = $this->common_model->get_selected_values('device_users','user_id,concat(firstname," ",lastname) as traineename','company_id='.$company_id);
        }
        if(count((array)$TraineeData)>0){
            foreach ($TraineeData as $value) {
                $lcTrainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
            }
        }
        $data['TraineeData'] = $lcTrainee_html;
        echo json_encode($data);
    }
}
