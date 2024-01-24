<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_wksh_summary extends CI_Controller
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
            $this->load->model('trainer_wksh_summary_model');
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
            $this->load->view('trainer_wksh_summary/index', $data);
        }
        
    }
    public function load_chart(){
        $company_id                  = $this->input->post('company_id', TRUE);
        $user_id                     = $this->input->post('user_id', TRUE);
        $workshop_type_id            = $this->input->post('workshop_type_id', TRUE);
        $workshop_id                 = $this->input->post('workshop_id', TRUE);
        $trainer_data                = $this->common_model->fetch_object_by_id('company_users','userid',$user_id);
        $trainer_name                = $trainer_data->first_name. ' '.$trainer_data->last_name;
        $workshop_statistics         = $this->trainer_wksh_summary_model->workshop_statistics($company_id, $user_id, $workshop_type_id, $workshop_id);
        $workshop_overall_statistics = $this->trainer_wksh_summary_model->workshop_overall_statistics($company_id, $user_id, $workshop_type_id, $workshop_id);



        $dataset1 = [];
        $dataset2 = [];
        $label=[];

        if (count((array)$workshop_statistics)>0){
            foreach($workshop_statistics as $wksh){
                $workshop_id   = $wksh->workshop_id;
                $workshop_name = $wksh->workshop_name;
                $topic_id      = $wksh->topic_id;
                $topic_name    = $wksh->topic_name;
                $pre_accuracy  = $wksh->pre_average;
                $post_accuracy = $wksh->post_average;
                $ce            = $wksh->ce;
                $label[]       = $topic_name; 
                if ($ce < 0) {
                    $dataset2[] = $ce;
                    $dataset1[] = '';
                } else {
                    $dataset1[] = $ce;
                    $dataset2[] = '';
                }

            }
            $htmlOverall = '';
            if (count((array)$workshop_overall_statistics)>0){
                foreach ($workshop_overall_statistics as $ovrall) {
                    $ovrall_workshop_name = $ovrall->workshop_name;
                    $ovrall_pre_accuracy  = $ovrall->pre_average;
                    $ovrall_post_accuracy = $ovrall->post_average;
                    $ovrall_ce            = $ovrall->ce;
                }
                $htmlOverall ='
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
                                    <td>'.$workshop_name.'</td>
                                    <td>'.$ovrall_pre_accuracy.'%</td>
                                    <td>'.$ovrall_post_accuracy.'%</td>
                                    <td>'.$ovrall_ce.'%</td>
                                </tr>
                            </tbody>
                        </table>
                        ';
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
                                            text: 'Topic Wise'
                                        }
                                    },
                                    yAxis: {
                                        max:100,
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
                                                format: '{point.y:.2f}',
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
                                                ".(count((array)$label) >10 ? '':'pointWidth: 28,')."
                                                stacking: 'normal',
                                                color:'#ffc000',
                                            },
                                            {
                                                name: 'Negative C.E',
                                                data: chartData2,
                                                ".(count((array)$label) >10 ? '':'pointWidth: 28,')."
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
            $data['overall_table']=$htmlOverall;
        }else{
            $html .= '<tr class="tr-background">
                        <td colspan="2" class="wksh-td">No Records Found</td>
                    </tr>';
            $data['chart']    = $html;
        }
        
        echo json_encode($data);
    }

}
