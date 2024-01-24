<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>assets/global/highcharts/css/highcharts.css" />
        <style>
            #tablecontainer {
                width: 50px;
                margin-left:700px;                
            }
            #maintablecontainer {
                width: 527px;
                margin-left:90px;
            }
            #headtr{
                width: 50px;
                border: 1px solid black; 
                background-color: steelblue;
            }
            #datatr{
                border: 1px solid black;
                background-color: white;
            }
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">

                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Report</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Summary Report</span>
                                </li>
                            </ul>                            
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="page-toolbar">
                                    <a href="<?php echo $base_url?>trainee_dashboard_i" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                                </div>
                                <div class="clearfix margin-top-20"></div>
                                <div class="col-md-6">
                                    <div id="AppendChart" style="margin-top: 20px !important; margin-left:70px; border-top: 1px solid #f1f2f7;" >
                                        <div class='col-lg-6' id='ChartDiv' style='border:1px solid #d4d4d4;padding: 5px;'>    
                                            <div id='container'>
                                                <div id='chart' style='min-width: 310px; height: 400px; margin: 0 auto'>

                                                </div>
                                            </div>
                                        </div>                                                  
                                    </div>
                                    <div class="col-md-6" id='tablecontainer'>
                                        <?php echo $Table ?> 
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix margin-top-30"></div>
                            <div class="row">
                                <div class="col-6 table-scrollable" id="maintablecontainer">                                    
                                        <?php echo $MainTable ?>                                    
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');  ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
<?php $this->load->view('inc/inc_footer_script'); ?>
<script src="<?php echo $base_url;?>assets/global/scripts/Chart.bundle.js"></script>
<script src="<?php echo $base_url;?>assets/global/highcharts/highcharts.src.js"></script>
 <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
<script src="<?php echo $base_url;?>assets/global/scripts/utils.js"></script>
<script>                    
                        var Lable = <?php echo $label; ?>;    
                        $(document).ready(function () {   
                        var Dataset = <?php echo $dataset; ?>;
                        Highcharts.chart('chart', {
                                chart:{
                                    type: 'bar'
                                },
                                title: {
                                text: 'Trainee Workshop Tab: Summary Report',
                                        'style': {
                                        'fontSize': '12px',
                                        'fontFamily': 'Arial'
                                        }
                                },
                                subtitle: {
                                    text: 'Trainee :'+Lable
                                },
                                xAxis: {
                                    categories:Lable,
                                    title: {
                                    text: 'User wise'
                                    }
                                },
                                yAxis: {
                                    title: {
                                    text: 'Overall C.E.',
                                    align: 'middle',
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
                                enabled: false
                                },
                                plotOptions: {
                                    bar: {
                                        zones: [{
                                            value: 0, 
                                            color: 'red'
                                        },{
                                            color: '#0070c0' 
                                        }]
                                    }
                                },
                                credits: {
                                    enabled: false
                                },
                                series: [{
                                        name: 'Accuracy',
                                        data: Dataset,
                                        <?php echo (count((array)$label) > 10 ? '':'pointWidth: 28,') ?>                   
                                        color: '#0070c0',
                                        dataLabels: {
                                            style: {
                                                fontWeight: 'normal',
                                                textOutline: '0',
                                                color: 'black',
                                                'fontSize': '12px',
                                            }
                                        }
                                }]
                            });
                        });                    
</script>
</body>
</html>