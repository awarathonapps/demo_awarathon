<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Trainer_individual extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == false) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'trainer_workshop');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainer_individual_model');
            $this->load->model('common_model');
        }
    }
    public function index()
    {
        $data['module_id'] = '25.1';
        $segs = $this->uri->total_segments();
        if ($segs<6){
            redirect('trainer_workshop');
        }else{
            $company_id                    = base64_decode($this->uri->segment(3));
            $trainer_id                    = base64_decode($this->uri->segment(4));
            $workshop_id                   = base64_decode($this->uri->segment(5));
            $workshop_type_id              = base64_decode($this->uri->segment(6));
        }
        $data['username']          = $this->mw_session['username'];
        $data['trainee_name']      = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        // $data['company_id']        = $this->mw_session['company_id'];
        $data['user_id']           = $this->mw_session['user_id'];
        $Login_company_id  = $this->mw_session['company_id'];
        $data['acces_management']  = $this->acces_management;
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($Login_company_id == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
            $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1,0,0,$workshop_type_id); 
        }else{
            $company_id =$this->mw_session['company_id'];
            $login_id = $this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
            if ($RightsFlag) {
                $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            } else {
                $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, $trainer_id);
            }
            if ($WRightsFlag) {
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1,0,0,$workshop_type_id);
            } else {
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id);
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,0,0,0,$workshop_type_id);
            }
        }
        
        $data['company_id']          = $company_id;
        $data['trainer_id']          = $trainer_id;
        $data['workshop_id']         = $workshop_id;
        $data['workshop_type_id']    = $workshop_type_id;
        $data['wksh_top_five_array'] = [];
        $this->load->view('trainer_individual/index', $data);
    }
    public function ajax_company_trainer_type() {
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $Trainer_id = '';
        } else {
            $company_id = $this->mw_session['company_id'];
            $Trainer_id = $this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $Trainer_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        }
        $lcWhere = 'status=1 AND login_type=1 AND company_id=' . $company_id;
        //$workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        if ($RightsFlag) {
                $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name, " (",email,")") as fullname', $lcWhere, "fullname");
            } else {
                $data['user_array'] = $this->common_model->getUserRightsList($company_id, $Trainer_id);
            }
        $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="'.$company_id.'"');
        echo json_encode($data);
    } 
    public function ajax_fetch_workshop(){
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
        }
        }
        if($company_id=="" || $company_id==0){
             $company_id  = $this->uri->segment(3);
        }
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,$WRightsFlag,0,0,$workshop_type_id);
        echo json_encode($data);
    }
    public function load_trainee_table(){
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id  =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        if($company_id=="" || $company_id==0){
            $company_id  = $this->uri->segment(3);
        }
        $trainer_id       = $this->input->post('user_id', TRUE);
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $workshop_id      = $this->input->post('workshop_id', TRUE);
        $workshop_statistics = $this->trainer_individual_model->workshop_statistics($company_id,$workshop_id,$trainer_id,$workshop_type_id,$RightsFlag,$WRightsFlag);
        
        $html  = '';
        if (count((array)$workshop_statistics)>0){
            foreach($workshop_statistics as $wksh){
                $user_id               = $wksh->trainee_id;
                $ce                    = $wksh->ce.'%';
                if($wksh->pre_average=="NP"){
                  $ce ="Not Played"; 
                }
                if($wksh->post_average=="NP"){
                  $ce ="Not Played"; 
                  $post_average="Not Played"; 
                }else{
                    $post_average=$wksh->post_average; 
                }
                $TopicCount =$this->trainer_individual_model->topic_count($user_id,$workshop_id,$trainer_id,$workshop_type_id,$RightsFlag);
                    $html .= '<tr>
                                <td style="width: 34%;">'.$wksh->trainee_name.'</td>
                                <td style="width: 12%;">'.$ce.'</td>
                                <td style="width: 12%;">'.$post_average.'</td>
                                <td style="width: 12%;">'.$TopicCount.'</td>
                                <td style="width: 28%;">
                                    <a data-toggle="modal" href="javascript:void(0)" onclick="workshop_detail('.$user_id.')" class="btn btn-xs red">
                                        <i class="fa fa-bar-chart"></i> WORKSHOP WISE
                                    </a>
                                    <a style="float:right;text-decoration:none;width: 300px;display: block;">&nbsp;</a>
                                </td>
                            </tr>';
            }
        }else{
            $html .= '<tr class="tr-background">
                        <td colspan="2" class="wksh-td">No Records Found</td>
                    </tr>';
        }
        if ($html==''){
            $html .= '<tr class="tr-background">
                        <td colspan="2" class="wksh-td">No Records Found</td>
                    </tr>';
        }
        $data['wksh_list']         = $html;
        echo json_encode($data);
    }
    public function load_wksh_detail(){
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id  =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
            $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
            $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
        }
        }
        if($company_id=="" || $company_id==0){
             $company_id  = $this->uri->segment(3);
        }
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $workshop_id      = $this->input->post('workshop_id', TRUE);
        
        $trainee_id                  = $this->input->post('trainee_id', TRUE);
        $trainer_id                  = $this->input->post('trainer_id', TRUE);

        $trainer_ce_array = $this->trainer_individual_model->trainee_workshop_statistics($company_id,$workshop_id,$trainer_id,$workshop_type_id,$RightsFlag,$WRightsFlag,$trainee_id);
        $traineeSet  = $this->common_model->get_value('device_users', 'CONCAT(firstname," ",lastname) as name', 'user_id=' . $trainee_id);
        $trainerTopicSubtopicCEGraph = '';
        $dataset1                    = [];
        $dataset2                    = [];
        $label                       = [];
        $htmlOverall                 = '';
        $workshop_name               = '';
        $trainer_name                ='';
        if (count((array)$trainer_ce_array) > 0) {
            $htmlOverall ='<table class="table table-hover table-light">
                            <thead>
                                <tr class="uppercase" style="background-color: #e6f2ff;">
                                    <th width="26%">WORKSHOP NAME</th>
                                    <th width="12%">PRE SESSION</th>
                                    <th width="12%">POST SESSION</th>
                                    <th width="12%">C.E</th>
                                    <th width="8%">REPORT OPTION</th>
                                </tr>
                            </thead>
                            <tbody>';
            foreach ($trainer_ce_array as $ttwcea) {
                $workshop_name         = $ttwcea->workshop_name;
                $workshop_id= $ttwcea->workshop_id;
                $ce                    = $ttwcea->ce.'%';
                $pre_average_accuracy  = $ttwcea->pre_average;
                if($pre_average_accuracy=='NP'){
                    $pre_average_accuracy  = "Not Played";
                    $ce  = "Not Played";
                }
                $post_average_accuracy = $ttwcea->post_average;
                if($post_average_accuracy=='NP'){
                    $post_average_accuracy  = "Not Played";
                    $ce  = "Not Played";
                }
                $label[]               = $workshop_name; 
                $dataset[] = $ttwcea->post_avg;
                
                $htmlOverall .='<tr>
                                    <td>'.$workshop_name.'</td>
                                    <td>'.$pre_average_accuracy.'</td>
                                    <td>'.$post_average_accuracy.'</td>
                                    <td>'.$ce.'</td>
                                    <td>
                                        <a data-toggle="modal" href="javascript:void(0)" onclick="topic_subtopic('.$trainee_id.','.$workshop_id.')" class="btn btn-xs red">
                                            <i class="fa fa-bar-chart"></i> TOPIC + SUBTOPIC WISE
                                        </a>
                                    </td>
                                </tr>';
       
            }
            $htmlOverall .='</tbody></table>';
        }
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; '>
                                <div id='workshop_wise' style='height:".(count((array)$label)>5 ? '800':'400')."px'></div>
                            </div>
                            <div class='portlet-body' style='padding: 0px !important' id='overall_ce_panel'> 
                                ".$htmlOverall."
                            </div>

                        <script>
                            $(document).ready(function () {
                                var chartData1 =".json_encode($dataset, JSON_NUMERIC_CHECK)."
                                Highcharts.chart('workshop_wise', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: 'Workshop Name : ".$workshop_name."<br/>  Trainee Name: ".$traineeSet->name."',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:".json_encode($label).",
                                        title: {
                                            text: 'Workshop Wise'
                                        },
        scrollbar: {
            enabled: false
        },
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Post Competency',
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
                                            name: 'Post Competency',
                                            data: chartData1,"
                                            .(count((array)$label) > 10 ? '' : 'pointWidth: 28,')
                                           ."stacking: 'normal',
                                            color:'#00ffcc',
                                        },
                                        
                                    ]
                                });
                            });
                        </script>";
        $data['detail_report']     = $trainerTopicSubtopicCEGraph;
        echo json_encode($data);
    }
    public function load_topic_subtopic(){
        $workshop_id                 = $this->input->post('workshop_id', TRUE);
        $trainee_id                  = $this->input->post('trainee_id', TRUE);
        $trainer_id                  = $this->input->post('trainer_id', TRUE);
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] != "") {
            if(!$this->mw_session['superaccess']){
            $Login_id  =$this->mw_session['user_id'];
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
            $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        }
        $trainer_topic_wise_ce_array = $this->trainer_individual_model->trainer_topic_subtopic_wise_ce($RightsFlag,$trainer_id, $workshop_id, $trainee_id);
        $this->load->model('trainee_reports_model');
        $islive_workshop =$this->trainee_reports_model->isWorkshopLive($workshop_id);
        if($islive_workshop){
            $QuestionAnsData = $this->trainee_reports_model->getLivePrePostQuestionAnsData($workshop_id, $trainee_id);
        }else{
            $QuestionAnsData = $this->trainee_reports_model->getPrePostQuestionAnsData($workshop_id, $trainee_id);
        }
        $trainerTopicSubtopicCEGraph = '';
        $htmlOverall = '';
        if (count((array)$QuestionAnsData)>0){
            $htmlOverall ='<table class="table table-hover table-light">
                        <thead>
                            <tr class="uppercase" style="background-color: #e6f2ff;">
                                <th width="45%">WORKSHOP SESSION</th>
                                <th width="30%">NO. OF QUESTION ATTEMPTED</th>
                                <th width="20%">TOTAL CORRET ANSWER</th>
                            </tr>
                        </thead>
                        <tbody>';
             $htmlOverall .='<tr>
                <td>PRE</td>
                <td>' . $QuestionAnsData->pre_played_questions . '</td>
                <td>' . $QuestionAnsData->pre_correct . '</td>                                                              
                </tr><tr><td>POST</td>
                <td>' . $QuestionAnsData->post_played_questions . '</td>
                <td>' . $QuestionAnsData->post_correct . '</td>                                                              
                </tr>';
            $htmlOverall .='</tbody></table>';
        }
        $dataset1 = [];
        $dataset2 = [];
        $dataset3 = [];
        $dataset4 = [];
        $label = [];
        $traineeSet  = $this->common_model->get_value('device_users', 'CONCAT(firstname," ",lastname) as name', 'user_id=' . $trainee_id);
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
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; '>
                                <div id='topic_subtopic_ce' style='height:".(count((array)$label)>5 ? '1000':'800')."px'></div>
                            </div>
                            <div class='portlet-body' style='padding: 0px !important' id='overall_ce_panel'> 
                                ".$htmlOverall."
                            </div>

                        <script>
                            $(document).ready(function () {
                                var chartData1 =".json_encode($dataset1, JSON_NUMERIC_CHECK)."
                                var chartData2 =".json_encode($dataset2, JSON_NUMERIC_CHECK)."
                                var chartData3 =".json_encode($dataset3, JSON_NUMERIC_CHECK)."
                                var chartData4 =".json_encode($dataset4, JSON_NUMERIC_CHECK)."
                                Highcharts.chart('topic_subtopic_ce', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: 'Workshop Name : ".$workshop_name."<br/> Trainer : ".$trainer_name."<br/> Trainee Name: ".$traineeSet->name."',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:".json_encode($label).",
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
        $data['detail_report']     = $trainerTopicSubtopicCEGraph;
        echo json_encode($data);
    }
}
