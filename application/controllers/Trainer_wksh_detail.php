<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_wksh_detail extends CI_Controller
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
            $this->load->model('trainer_wksh_detail_model');
            $this->load->model('common_model');
        }
    }
    public function index()
    {
        $segs = $this->uri->total_segments();
        if ($segs<6){
            redirect('trainer_workshop');
        }else{
            $company_id                    = base64_decode($this->uri->segment(3, 0));
            $trainer_id                    = base64_decode($this->uri->segment(4, 0));
            $workshop_type_id              = base64_decode($this->uri->segment(5, 0));
            $workshop_id                   = base64_decode($this->uri->segment(6, 0));
            $data['module_id']             = '12.01';
            $data['username']              = $this->mw_session['username'];
            $data['trainee_name']          = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
            $data['company_id']            = $this->mw_session['company_id'];
            $data['user_id']               = $this->mw_session['user_id'];
            $data['acces_management']      = $this->acces_management;
            $data['wksh_company_id']       = $company_id;
            $data['wksh_trainer_id']       = $trainer_id;
            $data['wksh_workshop_type_id'] = $workshop_type_id;
            $data['wksh_workshop_id']      = $workshop_id;
            $this->load->view('trainer_wksh_detail/index', $data);
        }
        
    }
    public function load_chart(){
        $company_id                  = $this->input->post('company_id', TRUE);
        $user_id                     = $this->input->post('user_id', TRUE);
        $workshop_type_id            = $this->input->post('workshop_type_id', TRUE);
        $workshop_id                 = $this->input->post('workshop_id', TRUE);
        $trainer_data                = $this->common_model->fetch_object_by_id('company_users','userid',$user_id);
        $trainer_name                = $trainer_data->first_name. ' '.$trainer_data->last_name;
        $workshop_statistics         = $this->trainer_wksh_detail_model->workshop_statistics($company_id, $user_id, $workshop_type_id, $workshop_id);

        $workshop_overall_statistics =[];


        $dataset1 = [];
        $dataset2 = [];
        $dataset3 = [];
        $dataset4 = [];
        $label=[];

        if (count((array)$workshop_statistics)>0){
            foreach($workshop_statistics as $wksh){
                $workshop_id   = $wksh->workshop_id;
                $workshop_name = $wksh->workshop_name;
                $topic_id      = $wksh->topic_id;
                $topic_name    = $wksh->topic_name;
                $subtopic_id   = $wksh->subtopic_id;
                $subtopic_name = $wksh->subtopic_name;
                $pre_accuracy  = $wksh->pre_average;
                $post_accuracy = $wksh->post_average;
                $ce            = $wksh->ce;
                $label[]       = $topic_name.' - '.$subtopic_name; 
                $dataset1[]    = $pre_accuracy;
                $dataset2[]    = $post_accuracy;
                if ($ce < 0) {
                    $dataset4[] = $ce;
                    $dataset3[] = '';
                } else {
                    $dataset3[] = $ce;
                    $dataset4[] = '';
                }

            }
            $top_five_result  = $this->trainer_wksh_detail_model->top_five_workshop($company_id, $user_id, $workshop_type_id, $workshop_id);
            $top_five_user_id = "0,";
            $htmlTopFive ='<table class="table table-hover table-light">
                            <thead>
                                <tr class="uppercase" style="background-color: #e6f2ff;">
                                    <th width="52%">NAME</th>
                                    <th width="12%">PRE SESSION</th>
                                    <th width="12%">POST SESSION</th>
                                    <th width="12%">C.E</th>
                                    <th width="12%">RANK</th>
                                </tr>
                            </thead><tbody>';
            if (count((array)$top_five_result)>0){
                foreach ($top_five_result as $topfive) {
                    $top_five_user_id .= $topfive->user_id.",";


                    $tf_trainee_name  = $topfive->trainee_name;
                    $tf_pre_accuracy  = $topfive->pre_average;
                    $tf_post_accuracy = $topfive->post_average;
                    $tf_ce            = $topfive->ce;
                    $htmlTopFive .='<tr>
                                        <td>'.$tf_trainee_name.'</td>
                                        <td>'.$tf_pre_accuracy.'</td>
                                        <td>'.$tf_post_accuracy.'</td>
                                        <td>'.$tf_ce.'</td>
                                        <td>-</td>
                                    </tr>';
                }
                $htmlTopFive .='</tbody></table>';
                if ($top_five_user_id!=''){
                    $top_five_user_id = substr($top_five_user_id,0,strlen($top_five_user_id)-1);
                }
            }else{
                $htmlTopFive .='<tr>
                                    <td colspan="5">No Participant</td>
                                </tr>';
                $htmlTopFive .='</tbody></table>';
            }
            $bottom_five_result          = $this->trainer_wksh_detail_model->bottom_five_workshop($company_id, $user_id, $workshop_type_id, $workshop_id,$top_five_user_id);
            $htmlBottomFive ='<table class="table table-hover table-light">
                            <thead>
                                <tr class="uppercase" style="background-color: #e6f2ff;">
                                    <th width="52%">NAME</th>
                                    <th width="12%">PRE SESSION</th>
                                    <th width="12%">POST SESSION</th>
                                    <th width="12%">C.E</th>
                                    <th width="12%">RANK</th>
                                </tr>
                            </thead><tbody>';
            if (count((array)$bottom_five_result)>0){
                foreach ($bottom_five_result as $botfive) {
                    $bf_trainee_name  = $botfive->trainee_name;
                    $bf_pre_accuracy  = $botfive->pre_average;
                    $bf_post_accuracy = $botfive->post_average;
                    $bf_ce            = $botfive->ce;
                    $htmlBottomFive .='<tr>
                                        <td>'.$bf_trainee_name.'</td>
                                        <td>'.$bf_pre_accuracy.'</td>
                                        <td>'.$bf_post_accuracy.'</td>
                                        <td>'.$bf_ce.'</td>
                                        <td>-</td>
                                    </tr>';
                }
                $htmlBottomFive .='</tbody></table>';
            }else{
                $htmlBottomFive .='<tr>
                                    <td colspan="5">No Participant</td>
                                </tr>';
                $htmlBottomFive .='</tbody></table>';
            }

            $htmlGraph ="<div class='row'>
                            <div id='container' class='col-12'>
                                <div id='topic_wise_ce' style='min-width: 310px; height: auto; margin: 0'></div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                var chartData1 =".json_encode($dataset1, JSON_NUMERIC_CHECK)."
                                var chartData2 =".json_encode($dataset2, JSON_NUMERIC_CHECK)."
                                var chartData3 =".json_encode($dataset3, JSON_NUMERIC_CHECK)."
                                var chartData4 =".json_encode($dataset4, JSON_NUMERIC_CHECK)."
                                Highcharts.chart('topic_wise_ce', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: 'Workshop Name : ".$workshop_name."<br/> Trainer Name: ".$trainer_name."',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:". json_encode($label).",
                                        title: {
                                            text: 'Topic + Sub Topic'
                                        }
                                    },
                                    yAxis: {
                                        max: 100,
                                        title: {
                                            text: 'Overall C.E',
                                            align: 'middle',
                                            format: '{point.y:.1f}%'
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value + '%';
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
            $data['chart']=$htmlGraph;
            $data['top_five_table']=$htmlTopFive;
            $data['bottom_five_table']=$htmlBottomFive;
        }else{
            $html .= '<tr class="tr-background">
                        <td colspan="2" class="wksh-td">No Records Found</td>
                    </tr>';
            $data['chart']    = $html;
        }
        
        echo json_encode($data);
    }

}
