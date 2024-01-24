<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_dashboard');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainer_dashboard_model');
        }

    public function index($trainer_id = '') {
        $data['module_id'] = '12.01';

        $data['username'] = $this->mw_session['username'];
        $data['trainee_name'] = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        $data['company_id'] = $this->mw_session['company_id'];
        $data['user_id'] = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;
        $data['trainer_id'] = $trainer_id;
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
            if ($trainer_id != "") {
                $Rowset = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $trainer_id);
                $data['company_id'] = $Rowset->company_id;
            }
        } else {
            $data['company_array'] = array();
            $login_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                if ($trainer_id == "") {
                    $trainer_id = $this->mw_session['user_id'];
                }
            }
            if (!$RightsFlag) {
                $this->common_model->SyncTrainerRights($trainer_id);
            }
            $data['user_array'] = $this->common_model->getUserRightsList($data['company_id'],$RightsFlag );
            if (count((array)$data['user_array']) == 1) {
                $data['trainer_id'] = $trainer_id;
            }
            if (!$WRightsFlag) {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
            }
            $data['wtype_array'] = $this->common_model->getWTypeRightsList($data['company_id'],$WRightsFlag);
            $data['RegionResult'] = $this->common_model->getUserRegionList($data['company_id'],$WRightsFlag);
        }
        $data['wksh_top_five_array'] = [];
        //$data['Supcompany_id'] = $company_id;
        $this->load->view('trainer_dashboard/index', $data);
    }

    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function ajax_company_filter() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['wtype_array'] = $this->common_model->getWTypeRightsList($company_id,1);
        $data['RegionResult'] = $this->common_model->getUserRegionList($company_id,1);
        $data['user_array'] = $this->common_model->getUserRightsList($company_id,1 );
        echo json_encode($data);
    }
    public function ajax_trainerwise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $trainer_id = $this->input->post('trainer_id', TRUE);

        $data['WtypeResult'] = $this->trainer_dashboard_model->getWorkshopType($company_id,$trainer_id);
        $data['RegionResult'] = $this->trainer_dashboard_model->getRegion($company_id,$trainer_id);

        echo json_encode($data);
    }
    public function ajax_wtypewise_data() {        
        $wrktype_id = $this->input->post('wrktype_id', TRUE);        
        $data['WsubtypeResult'] = $this->common_model->get_selected_values('workshopsubtype_mst','id,description as wsubtype','workshoptype_id='.$wrktype_id);        
        echo json_encode($data);
    }
    public function ajax_regionwise_data() {        
        $region_id = $this->input->post('region_id', TRUE);        
        $data['SubregionResult'] = $this->common_model->get_selected_values('workshopsubregion_mst','id,description as subregion','region_id='.$region_id);        
        echo json_encode($data);
    }
    public function load_quick_statistics() {
        $company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($company_id == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $this->load->model('supervisor_dashboard_model');
        $SyncFlag = $this->supervisor_dashboard_model->requiredSyncData($company_id);
        if ($SyncFlag) {
            //$this->supervisor_dashboard_model->SyncTrainerResult($company_id);
            $this->supervisor_dashboard_model->LiveDataSync($company_id);
            //$this->supervisor_dashboard_model->SyncWorshopResult($company_id);
            $this->load->model('trainee_reports_model');
            $this->trainee_reports_model->SynchTraineeData($company_id);
        }
        $trainer_id = $this->input->post('user_id', true);
        $wrktype_id = $this->input->post('wrktype_id', true);
        $wsubtype_id = $this->input->post('wsubtype_id', true);
        $flt_region_id = $this->input->post('flt_region_id', true);
        $subregion_id = $this->input->post('subregion_id', true);
        if($flt_region_id==""){
			$flt_region_id=0;
		}
		if($wrktype_id==""){
			$wrktype_id=0;
		}
        $WorshopData = $this->trainer_dashboard_model->workshop_attended($company_id, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $data['workshop_attended'] = $WorshopData['workshop_Attend'];
        $data['topic_trained'] = $WorshopData['total_topic'];
        $data['subtopic_trained'] = $this->trainer_dashboard_model->subtopic_trained($company_id, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

        $data['overall_post_accuracy'] = $this->trainer_dashboard_model->overall_accuracy($trainer_id,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

        $HightCEData = $this->trainer_dashboard_model->get_HighestLowestAvgCE($company_id, "", "", $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
//        echo "<pre>";
//        print_r($HightCEData);
//        
        $data['average_ce'] = $HightCEData['Avg'];
        $data['best_ce'] = $HightCEData['MaxCE'];
        $data['lowest_ce'] = $HightCEData['MinCE'];
        $data['workshop_lastweek'] = $this->trainer_dashboard_model->workshop_last_week($company_id, $trainer_id,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $data['best_post_accuracy'] = $this->trainer_dashboard_model->best_post_accuracy($trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);


        //TOP AND BOTTOM 5 TOPICS C.E TRAINER WISE
        $topic_top_five_array = $this->trainer_dashboard_model->top_five_topics($company_id, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $top_five_topic_id = "0,";
        $topic_top_five_html = '';
        if (count((array)$topic_top_five_array) > 0) {
            foreach ($topic_top_five_array as $topic_top) {
                $top_five_topic_id .= $topic_top->topic_id . ",";

                $topic_top_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $topic_top->topic . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . $topic_top->ce . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $topic_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        if ($top_five_topic_id != '') {
            $top_five_topic_id = substr($top_five_topic_id, 0, strlen($top_five_topic_id) - 1);
        }
        $topic_bottom_five_array = $this->trainer_dashboard_model->bottom_five_topics($company_id, $trainer_id, $top_five_topic_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $topic_bottom_five_html = '';
        if (count((array)$topic_bottom_five_array) > 0) {
            foreach ($topic_bottom_five_array as $topic_bottom) {
                $topic_bottom_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $topic_bottom->topic . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . $topic_bottom->ce . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $topic_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }

        $data['topic_top_five_table'] = $topic_top_five_html;
        $data['topic_bottom_five_table'] = $topic_bottom_five_html;


        echo json_encode($data);
    }

    public function load_histrogram_Data($trainer_id,$Month, $Year, $Workshop_Type, $Region_Name, $graphtype_id = '', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id) {
        $histogramWkshPreGraph = '';
        $histogramWkshPostGraph = '';
        $histogramTopicPreGraph = '';
        $histogramTopicPostGraph = '';
        $histogramTraineePreGraph = '';
        $histogramTraineePostGraph = '';
        $wksh_dataset_pre = [];
        $wksh_label_pre = [];
        $wksh_dataset_post = [];
        $wksh_label_post = [];
        $topic_dataset_pre = [];
        $topic_label_pre = [];
        $topic_dataset_post = [];
        $topic_label_post = [];
        $trainee_dataset_pre = [];
        $trainee_label_pre = [];
        $trainee_dataset_post = [];
        $trainee_label_post = [];

        $TopTitle = "Trainer Histogram (Workshop Type :$Workshop_Type ,Region : $Region_Name)";

        $histogram_pre = $this->trainer_dashboard_model->wksh_histogram_range($trainer_id, 'PRE', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        foreach ($histogram_pre as $range) {
            $wksh_label_pre[] = $range->from_range . "-" . $range->to_range . "%";
            $wksh_dataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_post = $this->trainer_dashboard_model->wksh_histogram_range($trainer_id, 'POST', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        foreach ($histogram_post as $range) {
            $wksh_label_post[] = $range->from_range . "-" . $range->to_range . "%";
            $wksh_dataset_post[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_pre = $this->trainer_dashboard_model->topic_histogram_range($trainer_id, 'PRE', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        foreach ($histogram_pre as $range) {
            $topic_label_pre[] = $range->from_range . "-" . $range->to_range . "%";
            $topic_dataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_post = $this->trainer_dashboard_model->topic_histogram_range($trainer_id, 'POST', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        foreach ($histogram_post as $range) {
            $topic_label_post[] = $range->from_range . "-" . $range->to_range . "%";
            $topic_dataset_post[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_TopicCount_pre = $this->trainer_dashboard_model->trainee_histogram_range($trainer_id, 'PRE', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        foreach ($histogram_TopicCount_pre as $range) {
            $trainee_label_pre[] = $range->from_range . "-" . $range->to_range . "%";
            $trainee_dataset_pre[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogram_TopicCount_post = $this->trainer_dashboard_model->trainee_histogram_range($trainer_id, 'POST', $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        foreach ($histogram_TopicCount_post as $range) {
            $trainee_label_post[] = $range->from_range . "-" . $range->to_range . "%";
            $trainee_dataset_post[] = ($range->TrainerCount > 0 ? $range->TrainerCount : 0);
        }
        $histogramWkshPreGraph = "<div id='container'>
                                <div id='wksh_histogram_pre' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var histogramDataPre =" . json_encode($wksh_dataset_pre, JSON_NUMERIC_CHECK) . "
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "'     
                                Highcharts.chart('wksh_histogram_pre', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
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
                                            text: 'No. of Workshops',
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
                                        column: {
                                            dataLabels: {
                                                enabled: false
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramWkshPreGraph .= "{
                                            type: 'column',                     
                                            name: 'PRE Competency',
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
                                        },";
        }if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramWkshPreGraph .= "{                    
                                            type: 'spline',  
                                            name: 'PRE Competency ',
                                            data: histogramDataPre,
                                            " . (count((array)$wksh_label_pre) > 10 ? '' : 'pointWidth: 28,') . "                    
                                            color: '#0070c0', dataLabels: {
                                                    style: {
                                                        fontWeight: 'normal',
                                                        textOutline: '0',
                                                        color: 'black',
                                                        'fontSize': '12px',
                                                    }}
                                        }";
        }
        $histogramWkshPreGraph .= "]
                                });
                            });
                        </script>";

        $histogramWkshPostGraph = "<div id='container'>
                                <div id='wksh_histogram_post' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "'
                                var histogramDataPost =" . json_encode($wksh_dataset_post, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('wksh_histogram_post', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
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
                                            text: 'No. of Workshops',
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
                                        column: {
                                            dataLabels: {
                                                enabled: false
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramWkshPostGraph .= "{
                                            type: 'column',                     
                                            name: 'POST Competency',
                                            data: histogramDataPost," .
                    (count((array)$wksh_label_post) > 10 ? '' : 'pointWidth: 28,')
                    . "color: '#00FFFF',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        },";
        }
        if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramWkshPostGraph .= "{                    
                                        type: 'spline',  
                                        name: 'POST Competency ',
                                        data: histogramDataPost,
                                        " . (count((array)$wksh_label_post) > 10 ? '' : 'pointWidth: 28,') . "                    
                                        color: '#00FFFF', dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                    }";
        }
        $histogramWkshPostGraph .= "]
                                });
                            });
                        </script>";
        $histogramTopicPreGraph = "<div id='container'>
                                <div id='topic_histogram_pre' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "'
                                var histogramTopicDataPre =" . json_encode($topic_dataset_pre, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_histogram_pre', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($topic_label_pre) . ",
                                        title: {
                                            text: 'Pre Compentency Range'
                                        }
                                    },
                                    yAxis: {
                                    allowDecimals: false,
                                        title: {
                                            text: 'No. of Topics',
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
                                        column: {
                                            dataLabels: {
                                                enabled: false,
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [
                                    ";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramTopicPreGraph .= "{
                                            type: 'column',                     
                                            name: 'PRE Competency',
                                            data: histogramTopicDataPre," .
                    (count((array)$topic_label_pre) > 10 ? '' : 'pointWidth: 28,')
                    . "color: '#0070c0',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        },";
        }
        if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramTopicPreGraph .= "{
                                        type: 'spline',  
                                        name: 'PRE Competency ',
                                        data: histogramTopicDataPre,
                                        " . (count((array)$topic_label_pre) > 10 ? '' : 'pointWidth: 28,') . "                    
                                        color: '#0070c0', dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                    }";
        }
        $histogramTopicPreGraph .= "]
                                });
                            });
                        </script>";

        $histogramTopicPostGraph = "<div id='container'>
                                <div id='topic_histogram_post' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "'
                                var histogramTopicDataPost =" . json_encode($topic_dataset_post, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_histogram_post', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($topic_label_post) . ",
                                        title: {
                                            text: 'Post Compentency Range'
                                        }
                                    },
                                    yAxis: {
                                        allowDecimals: false,
                                        title: {
                                            text: 'No. of Topics',
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
                                        column: {
                                            dataLabels: {
                                                enabled: false,
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramTopicPostGraph .= "{
                                            type: 'column',                     
                                            name: 'POST Competency',
                                            data: histogramTopicDataPost," .
                    (count((array)$topic_label_post) > 10 ? '' : 'pointWidth: 28,')
                    . "color: '#00FFFF',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        },";
        }
        if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramTopicPostGraph .= "{
                                        type: 'spline',  
                                        name: 'POST Competency ',
                                        data: histogramTopicDataPost,
                                        " . (count((array)$topic_label_post) > 10 ? '' : 'pointWidth: 28,') . "                    
                                        color: '#00FFFF', dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                    }";
        }
        $histogramTopicPostGraph .= "
                                    ]
                                });
                            });
                        </script>";
        $histogramTraineePreGraph = "<div id='container'>
                                <div id='trainee_histogram_pre' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "'
                                var histogramTraineeDataPre =" . json_encode($trainee_dataset_pre, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('trainee_histogram_pre', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($trainee_label_pre) . ",
                                        title: {
                                            text: 'Pre Compentency Range'
                                        }
                                    },
                                    yAxis: {
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
                                        column: {
                                            dataLabels: {
                                                enabled: false,
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramTraineePreGraph .= "{
                                            type: 'column',                     
                                            name: 'PRE Competency',
                                            data: histogramTraineeDataPre," .
                    (count((array)$topic_label_post) > 10 ? '' : 'pointWidth: 28,')
                    . "color: '#0070c0',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        },";
        }
        if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramTraineePreGraph .= "{
                                        type: 'spline',  
                                        name: 'PRE Competency ',
                                        data: histogramTraineeDataPre,
                                        " . (count((array)$topic_label_post) > 10 ? '' : 'pointWidth: 28,') . "                    
                                        color: '#0070c0', dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                    }";
        }
        $histogramTraineePreGraph .= "]
                                });
                            });
                        </script>";
        $histogramTraineePostGraph = "<div id='container'>
                                <div id='trainee_histogram_post' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "'
                                var histogramTraineeDataPost =" . json_encode($trainee_dataset_post, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('trainee_histogram_post', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
                                        'style': {
                                            'fontSize'  : '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($trainee_label_post) . ",
                                        title: {
                                            text: 'Post Compentency Range'
                                        }
                                    },
                                    yAxis: {
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
                                                enabled: false,
                                                format: '{point.y:.2f}'
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramTraineePostGraph .= "{
                                            type: 'column',                     
                                            name: 'POST Competency',
                                            data: histogramTraineeDataPost," .
                    (count((array)$topic_label_post) > 10 ? '' : 'pointWidth: 28,')
                    . "color: '#00FFFF',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        },";
        }
        if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramTraineePostGraph .= "{
                                        type: 'spline',  
                                        name: 'POST Competency ',
                                        data: histogramTraineeDataPost,
                                        " . (count((array)$topic_label_post) > 10 ? '' : 'pointWidth: 28,') . "                    
                                        color: '#00FFFF', dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                    }";
        }
        $histogramTraineePostGraph .= "]
                                });
                            });
                        </script>";
        $data['histogram_wksh_pre'] = $histogramWkshPreGraph;
        $data['histogram_wksh_post'] = $histogramWkshPostGraph;
        $data['histogram_topic_pre'] = $histogramTopicPreGraph;
        $data['histogram_topic_post'] = $histogramTopicPostGraph;
        $data['histogram_trainee_pre'] = $histogramTraineePreGraph;
        $data['histogram_trainee_post'] = $histogramTraineePostGraph;
        return $data;
    }

    public function load_trainer_index() {
        $data = array();
        $company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($company_id == "") {
            $company_id = $this->input->post('company_id', true);
        } else {
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = $this->input->post('user_id', true);
        $rpt_period = $this->input->post('rpt_period', true);
//        $wtype_id = $this->input->post('wtype_id', true);
//        $region_id = $this->input->post('region_id', true);

        $wrktype_id = $this->input->post('wrktype_id', true);
        $wsubtype_id = $this->input->post('wsubtype_id', true);
        $flt_region_id = $this->input->post('flt_region_id', true);
        $subregion_id = $this->input->post('subregion_id', true);
        if($flt_region_id==""){
			$flt_region_id=0;
		}
		if($wrktype_id==""){
			$wrktype_id=0;
		}
        $report_data = array();
        $index_dataset = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $Year = $this->input->post('year', true);
        $Week = $this->input->post('week', true);
        $graphtype_id = $this->input->post('graphtype_id', true);
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $WeekStartDate = '';
        $WeekEndDate = '';
        $WeekStartDate = '';
        $WeekEndStr = '';
        if ($Week != '' && $Month != '' && $Year != '') {
            $WeekDate = explode('-', $Week);
            $WeekStartDay = $WeekDate[0];
            $WeekEndDay = $WeekDate[1];
            $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekStartStr = date('d-m-Y', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
            $WeekEndStr = date('d-m-Y', strtotime("$Year-$Month-$WeekEndDay"));
        }
        if ($rpt_period == "weekly") {

            $report_xaxis_title = 'Weekly';
            if ($WeekStartDate != '' && $WeekEndDate != '') {
                $report_title = 'Trainer Index - (Period From ' . $WeekStartStr . ' To ' . $WeekEndStr;
                $CEArraySet = $this->trainer_dashboard_model->supervisor_index_weekly_monthly($company_id,$WeekStartDate, $WeekEndDate, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
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
                $previous_date = date('Y-m-d', strtotime("-6 days"));
                $StartStrDt = date('d-m-Y', strtotime("-6 days"));
                $EndStrDt = date('d-m-Y');
                $StartWeek = date('d', strtotime("-6 days"));
                $EndWeek = date('d');
                $report_title = 'Trainer Index - (Period From ' . $StartStrDt . ' To ' . $EndStrDt;
                $CEArraySet = $this->trainer_dashboard_model->supervisor_index_weekly_monthly($company_id, $previous_date, $current_date, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
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
        }
        if ($rpt_period == "monthly") {

            if ($Year != '' && $Month != '' && $Month != $current_month) {
                $StartDate = $Year . '-' . $Month . '-01';
                $StartStrDt = '01-' . $Month . '-' . $Year;
                $noofdays = date('t', strtotime($StartDate));
                $EndDate = $Year . '-' . $Month . '-' . $noofdays;
                $EndStrDt = $noofdays . '-' . $Month . '-' . $Year;
                $report_title = 'Trainer Index - (Period ' . $StartStrDt . ' To ' . $EndStrDt;
            } else {
                $StartDate = Date('Y-m-1');
                $EndDate = $current_date;
                $diff = abs(strtotime($current_date) - strtotime($StartDate));
                $nyears = floor($diff / (365 * 60 * 60 * 24));
                $nmonths = floor(($diff - $nyears * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $noofdays = floor(($diff - $nyears * 365 * 60 * 60 * 24 - $nmonths * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $report_title = 'Trainer Index - (Period ' . $StartDate . ' To ' . $EndDate;
            }
            $report_xaxis_title = 'Monthly';
            $CEArraySet = $this->trainer_dashboard_model->supervisor_index_weekly_monthly($company_id, $StartDate, $EndDate, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
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
        }
        if ($rpt_period == "yearly") {
            $StartDate = $Year . '-01-01';

            $EndDate = $Year . '-12-31';
            $report_title = 'Trainer Index - (Period: ' . date('M-Y', strtotime($StartDate)) . ' To ' . date('M-Y', strtotime($EndDate));
            $report_xaxis_title = 'Yearly';
            $CEArraySet = $this->trainer_dashboard_model->supervisor_index_yearly($company_id,$StartDate, $EndDate, $trainer_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
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
        $data['report'] = $report_data;

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
        $report_title .= ",Workshop Type : " . $workshop_type;
        $report_title .= ",Region : " . $region_name;
        $report_title .= ')';

        $indexGraph = "<div id='container'>
                                <div id='trainer_index_graph' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : 1 ) . "' 
                                var indexData =" . json_encode($index_dataset, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('trainer_index_graph', {
                                    chart: {
                                        type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: '" . $report_title . "',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($index_label) . ",
                                        title: {
                                            text: '" . $report_xaxis_title . "'
                                        }
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Time Period C.E',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value+ '%';
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
                                                format: '{point.y:.2f}'
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [{
                                            type: (graph_type == 3  ? 'spline' :''),                        
                                            name: 'Competency Enhancement(C.E)',
                                            data: (graph_type != 2  ? indexData :''),
                                            " . (count((array)$index_label) > 10 ? '' : 'pointWidth: 28,')
                . "color: '#ffc000',
                                            
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


        $data['index_graph'] = $indexGraph;
        // Load Trainer CE Histrogram
        $TCeData = $this->trainer_dashboard_model->trainer_ce_histogram($company_id, '', '', $trainer_id,$WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);

        $StopFlag = false;
        $dataset = array();
        $label = array();

        foreach ($TCeData as $range) {
            if ($range->WorkshopCount != "") {
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
                if ($range->WorkshopCount != "") {
                    $dataset[] = json_encode($range->WorkshopCount, JSON_NUMERIC_CHECK);
                } else {
                    $dataset[] = 0;
                }
                //$dataset[] = $range->WorkshopCount; 
            }
        }
//        echo "<pre>";
//        print_r($dataset);
//        exit;
        $from_range = 0;
        $TopTitle = "Trainer Histogram (Workshop Type : $workshop_type ,Region : $region_name)";
        $histogramWkshPreGraph = "<div id='container'>
                                <div id='thistogram_wksh_ce' style='min-width: 310px; height: auto; margin: 0 auto'></div>
                            </div>

                        <script>
                            $(document).ready(function () {
                                var graph_type = '" . ($graphtype_id == '' ? 1 : $graphtype_id ) . "' 
                                var histogramDataPre =" . json_encode($dataset, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('thistogram_wksh_ce', {
                                    chart: {
                                         type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
                                    },
                                    title: {
                                        text: ' $TopTitle',
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
                                            text: 'CE. Range'
                                        }                                        
                                    },
                                    yAxis: {
                                        allowDecimals: false,
                                        title: {
                                            text: 'No. of Workshops',
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
                                        column: {
                                            dataLabels: {
                                                enabled: false,
                                            }
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: [";
        if ($graphtype_id == 2 || $graphtype_id == 3) {
            $histogramWkshPreGraph .= "{
                                            type: 'column',                     
                                            name: 'Competency Enhancement(C.E)',
                                            data: histogramDataPre," .
                    (count((array)$label) > 10 ? '' : 'pointWidth: 28,')
                    . "color: '#ffc000',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                        },";
        }if ($graphtype_id == 1 || $graphtype_id == 3) {
            $histogramWkshPreGraph .= "{                    
                                        type: 'spline',  
                                        name: 'Competency Enhancement(C.E)',
                                        data: histogramDataPre,
                                        " . (count((array)$label) > 10 ? '' : 'pointWidth: 28,') . "                    
                                        color: '#ffc000', dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                }}
                                    }";
        }
        $histogramWkshPreGraph .= "
                                    ]
                                });
                            });
                        </script>";
        $data['histogram_CE'] = $histogramWkshPreGraph;
        $TData = $this->load_histrogram_Data($trainer_id,$Month, $Year, $workshop_type, $region_name, $graphtype_id, $WRightsFlag,$wrktype_id,$wsubtype_id,$flt_region_id,$subregion_id);
        $result = array_merge($data, $TData);
        echo json_encode($result);
    }

}
