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
        <link href="<?php echo $base_url;?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>assets/global/highcharts/css/highcharts.css"" />
        <style>
            table tr {
                background-color: #ffffff;
            }
            .table.table-light thead tr th{
                color: #000000 !important;
            }
            .table.table-light tbody tr td{
                color: #000000 !important;
            }
            .highcharts-data-labels{
                font-size: 11px;
                color: #FFFFFF;
                font-family: Verdana, sans-serif;
                fill: #FFFFFF;
            }
            #topic_wise_ce .highcharts-color-0 {
                fill: #0070c0 !important;
                stroke: #0070c0 !important;
            }
            .highcharts-color-1 {
                fill: #00ffcc;
                stroke: #00ffcc;
            }.highcharts-color-2 {
                fill: #ffc000;
                stroke: #ffc000;
            }
            .highcharts-negative{
                fill: #FF0000;
                stroke: #FF0000;
            }
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">

                        <!-- PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <a href="index.html">Home</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Dashboard</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">


                                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </div>
                            </div>
                        </div>
                        <br/>

                        <!-- STAT FIRST ROW -->
                        <div class="row">

                            <!-- STAT CHART -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Topic wise Overall C.E</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="topic_wise_ce_panel"> 
                                        <div id="topic_wise_ce_loading" style="text-align: center;display: none;">
                                            <img src="<?php echo $base_url;?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT CHART -->

                            <!-- STAT TABLE -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Overall C.E</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="overall_ce_panel"> 
                                        <div id="overall_ce_loading" style="text-align: center;display: none;">
                                            <img src="<?php echo $base_url;?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT TABLE -->

                        </div>
                        <!-- STAT FIRST ROW -->

                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $base_url;?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?php echo $base_url;?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $base_url;?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $base_url;?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>

        <script src="<?php echo $base_url;?>assets/global/highcharts/highcharts.src.js"></script>
         <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            function loadChart(){
                $('#topic_wise_ce_loading').show();
                $('#overall_ce_loading').show();
                $.ajax({
                    type: "POST",
                    data: {company_id: <?php echo $wksh_company_id;?>,user_id: <?php echo $wksh_trainer_id;?>,workshop_type_id: <?php echo $wksh_workshop_type_id;?>,workshop_id: <?php echo $wksh_workshop_id;?>},
                    async: false,
                    url: "<?php echo $base_url; ?>trainer_wksh_summary/load_chart",
                    success: function (response) {
                        if (response != '') {
                            var json         = jQuery.parseJSON(response);
                            var chart    = json['chart'];
                            if (chart!=''){
                                $("#topic_wise_ce_panel").append(json['chart']);
                                $("#overall_ce_panel").append(json['overall_table']);
                                
                                $('#topic_wise_ce_loading').hide();
                                $('#overall_ce_loading').hide();
                                //$('#overall_ce').append(json['chart_table']);
                            } 
                        }
                    }
                });
            }
            jQuery(document).ready(function() {
                if (!jQuery().daterangepicker) {
                    return;
                }

                $('#dashboard-report-range').daterangepicker({
                    "ranges": {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                        'Last 7 Days': [moment().subtract('days', 6), moment()],
                        'Last 30 Days': [moment().subtract('days', 29), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                    },
                    "locale": {
                        "format": "MM/DD/YYYY",
                        "separator": " - ",
                        "applyLabel": "Apply",
                        "cancelLabel": "Cancel",
                        "fromLabel": "From",
                        "toLabel": "To",
                        "customRangeLabel": "Custom",
                        "daysOfWeek": [
                            "Su",
                            "Mo",
                            "Tu",
                            "We",
                            "Th",
                            "Fr",
                            "Sa"
                        ],
                        "monthNames": [
                            "January",
                            "February",
                            "March",
                            "April",
                            "May",
                            "June",
                            "July",
                            "August",
                            "September",
                            "October",
                            "November",
                            "December"
                        ],
                        "firstDay": 1
                    },
                    //"startDate": "11/08/2015",
                    //"endDate": "11/14/2015",
                    opens: (App.isRTL() ? 'right' : 'left'),
                }, function(start, end, label) {
                    if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                        $('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    }
                });
                 if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                    $('#dashboard-report-range span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
                }
                $('#dashboard-report-range').show();
                loadChart();
             });
        </script>
    </body>
</html>