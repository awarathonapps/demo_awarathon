<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url =$this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/highcharts/css/highcharts.css"" />
        <style>
            .tr-background{
                background: #ffffff!important;
            }
            .wksh-td{
                color: #000000 !important;
                vertical-align: top !important;
            }
            .whsh-icon{
                float: right;
                position: absolute;
                top: 10px;
                right: 15px;
                color: #cccccc;
            }
            .potrait-title-mar{
                margin-left: -9px;
                margin-right: -9px;
            }
            .dashboard-stat{
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                -ms-border-radius: 4px;
                -o-border-radius: 4px;
                border-radius: 4px;
                background: #fff;
                padding: 5px 5px 5px;
                border: 1px solid #eef1f5;
                border-radius: 5px !important;
                background: aliceblue;
            }
            .dashboard-stat .display {
                height: 70px;
            }
            .dashboard-stat .display .number {
                text-align: center;
                float: left;
                display: inline-block;
                width: 100%;
            }
            .dashboard-stat .display .number small{
                font-size: 12px;
                color: #777777;
                font-weight: 600;
                text-transform: uppercase;
                width: 100%;
            }
            .font-orange-sharp{
                color: #f1592a !important;
                margin: 0px !important;
                padding: 5px !important;
            }
            .tokenize-sample { width: 100%;height:auto }
            .highcharts-data-labels{
                font-size: 11px;
                color: #FFFFFF;
                font-family: Verdana, sans-serif;
                fill: #FFFFFF;
            }
            .highcharts-color-0 {
                fill: #0070c0;
                stroke: #0070c0;
            }
            .highcharts-color-1 {
                fill: #00ffcc;
                stroke: #00ffcc;
            }.highcharts-color-2 {
                fill: #ffff00;
                stroke: #ffff00;
            }
/*            .highcharts-negative{
                fill: #FF0000;
                stroke: #FF0000;
            }*/
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
                                    <a href="javascript:;">Home</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Trainer Dashboard</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">


                                <!-- <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </div> -->
                            </div>
                        </div>
                        <!-- PAGE BAR -->
                        <h1 class="page-title"> Trainer Dashboard
                            <!-- <small>- overview statistics, charts, recent workshop and reports</small> -->
                        </h1>
                        <?php if ((isset($user_array)&& count((array)$user_array)>0) || $company_id=="") { ?>
                            <div class="row">
                                <div class="col-lg-12 col-xs-12 col-sm-12">
                                    <div class="panel-group accordion" id="accordion3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                        Filter Dashboard </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_2" class="panel-collapse ">                                            
                                                <div class="panel-body" >
                                                    <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">

                                                        <div class="row margin-bottom-10">
                                                            <?php if ($company_id == "") { ?>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-md-4">Company&nbsp;<span class="required"> * </span></label>
                                                                        <div class="col-md-8" style="padding:0px;">
                                                                            <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getCompanyFilter();">
                                                                                <option value="">All Company</option>
                                                                                <?php foreach ($company_array as $cmp) { ?>
                                                                                    <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Trainer Name&nbsp;<span class="required"> * </span></label>
                                                                    <div class="col-md-8" style="padding:0px;">
                                                                        <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getTrainerwiseData()">
                                                                            <option value="">Please select</option>
                                                                            <?php foreach ($user_array as $cmp) { ?>
                                                                                <option value="<?= $cmp->userid; ?>" <?php echo ($trainer_id==$cmp->userid ? 'selected':''); ?>><?php echo $cmp->fullname; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row margin-bottom-10">                                                         
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Workshop Type&nbsp;</label>
                                                                    <div class="col-md-8" style="padding:0px;">
                                                                        <select id="wrktype_id" name="wrktype_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData()">
                                                                            <option value="0">All  Type</option>                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>                                                             
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Workshop Sub-Type&nbsp;</label>
                                                                    <div class="col-md-8" style="padding:0px;">
                                                                        <select id="wsubtype_id" name="wsubtype_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                            <option value="">All Sub-Type</option>                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row margin-bottom-10">                                                         
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Workshop Region&nbsp;</label>
                                                                    <div class="col-md-8" style="padding:0px;">
                                                                        <select id="flt_region_id" name="flt_region_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getRegionwiseData()">
                                                                            <option value="0">All Region</option>                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>                                                             
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Workshop Sub-Region&nbsp;</label>
                                                                    <div class="col-md-8" style="padding:0px;">
                                                                        <select id="subregion_id" name="subregion_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                            <option value="">All Sub-Region</option>                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="row margin-bottom-10">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Workshop Status&nbsp;</label>
                                                                    <div class="col-md-3" style="padding:0px;">
                                                                        <select id="workshop_status" name="workshop_status" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                            <option value="1,2">Both</option>
                                                                            <option value="1">Live</option>
                                                                            <option value="2">Completed</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> -->
                                                        <div class="clearfix margin-top-20"></div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="col-md-offset-10 col-md-2 text-right">
                                                                    <button id="btnSearch" type="button" class="btn blue-hoki btn-sm" >Preview</button>
                                                                    <!-- <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_reset()">Reset</button> -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- STAT FIRST ROW -->
                        <div class="row">

                            <!-- STAT BOX -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Quick Statistics</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <!-- <div id="site_statistics_loading">
                                            <img src="<?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->

                                        <!-- STAT ROW 1 -->
                                        <div class="row">
                                            <!-- BOX 1 -->
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="workshop_attended_counter" data-value="0">0</span>
                                                                <!-- <div class="icon whsh-icon">
                                                                    <i class="icon-pie-chart"></i>
                                                                </div> -->
                                                            </h3>
                                                            <small>Workshop Attended</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 2 -->
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="topic_trained_counter"  data-value="0">0</span>
                                                            </h3>
                                                            <small>No. Of Topic Trained</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 3 -->
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="subtopic_trained_counter" data-value="0">0</span>
                                                            </h3>
                                                            <small>No. Of Sub Topic Trained</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 4 -->
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="overall_post_accuracy_counter" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Average Post Accuracy</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 5-->
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="average_ce_counter" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Average C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 6 -->
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="last_week_wksh_counter"  data-value="0">0</span>
                                                            </h3>
                                                            <small>Latest Workshop Conducted In Last Week</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- STAT ROW 1 -->

                                        <div class="clearfix"></div>

                                        <!-- STAT ROW 2 -->
                                        <div class="row" style="margin-top: 5px;">
                                            <!-- BOX 7 -->
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="best_post_counter" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Best Post</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 8 -->
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="best_ce_counter" data-value="0">0</span><span id="best_ce_sign">%</span>
                                                            </h3>
                                                            <small>Best C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOX 9 -->
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id= "lowest_ce_counter" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Lowest C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                      
                                        <div class="row" style="margin-top: 5px;">

                                        </div>                                       

                                        <div class="clearfix"></div>    
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->

                            <div class="clearfix"></div>

                            <!-- TOP 5 WORKSHOP -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Top 5 Topics</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important"> 

                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table table-hover table-light" id="wksh-top-five">
                                                <thead>
                                                    <tr class="uppercase">
                                                        <th class="wksh-td" width="80%"> TOPIC NAME </th>
                                                        <th class="wksh-td" width="20%"> OVERALL C.E </th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>

                                        <!-- <div id="site_statistics_loading">
                                            <img src="< ?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->

                                    </div>
                                </div>
                            </div>
                
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Bottom 5 Topics</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important"> 

                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table table-hover table-light" id="wksh-bottom-five">
                                                <thead>
                                                    <tr class="uppercase">
                                                        <th class="wksh-td" width="80%"> TOPIC NAME </th>
                                                        <th class="wksh-td" width="20%"> OVERALL C.E </th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>

                                        <!-- <div id="site_statistics_loading">
                                            <img src="< ?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->

                                    </div>
                                </div>
                            </div>
                            <!-- BOTTOM 5 WORKSHOP -->

                            <div class="clearfix"></div>

                            <!-- INDEX CHART -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">INDEX</span>
                                        </div>
                                        <div class="actions">
                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                    <input type="radio" name="rpt_period_option" class="toggle" id="opt_weekly" value="weekly">Weekly</a>
                                                <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm ">
                                                    <input type="radio" name="rpt_period_option" class="toggle" id="opt_monthly" value="monthly">Monthly</a>
                                                <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                    <input type="radio" name="rpt_period_option" class="toggle" id="opt_yearly" value="yearly">Yearly</a>
                                                <a data-toggle="modal"  class="btn btn-circle btn-icon-only btn-default" href="#responsive-modal" style="padding: 3px 0px !important;">
                                                    <i class="icon-settings"></i>
                                                </a>
                                                <input type="hidden" id="rpt_period" name="rpt_period" value="yearly" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="trainer_index"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="< ?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>

                                <!-- SETTINGS BOX -->
                                <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form id="frmModalForm" name="frmModalForm" onsubmit="return false;"> 
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                    <h4 class="modal-title">Filter</h4>
                                                </div>
                                                <div class="modal-body">
                                                <div id='dsk' style="display: none">&nbsp;</div>                                                
                                                <div class="row">
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Month</label>
                                                            <select id="month" name="month" class="form-control input-sm select2" placeholder="Please select" onchange="getWeek()">
                                                                <?php foreach (range(1, 12) as $month):
                                                                $monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
                                                                $fdate = date("F", strtotime(date('Y') . "-$monthPadding-01"));
                                                                echo '<option value="' . $monthPadding . '" '.($monthPadding==date('m') ? 'selected':'').'>' . $fdate . '</option>';
                                                            endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Week</label>
                                                            <select id="week" name="week" class="form-control input-sm select2" placeholder="Please select">
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">    
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Year</label>
                                                            <select id="year" name="year" class="form-control input-sm select2" placeholder="Please select" >
                                                                <option value="<?php echo date('Y') ?>"><?php echo date('Y') ?></option>
                                                                <option value="<?php echo date('Y',strtotime("-1 year")) ?>"><?php echo date('Y',strtotime("-1 year")) ?></option>
                                                            </select>
                                                        </div>
                                                    </div> 
                                                </div>
<!--                                                <div class="row">    
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Workshop Type</label>
                                                            <select id="wtype_id" name="wtype_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                <option value="">All Type</option>
                                                                < ?php
                                                                if (isset($wtype_array)) {
                                                                    foreach ($wtype_array as $Type) {
                                                                        ?>
                                                                        <option value="< ?= $Type->id; ?>">< ?php echo $Type->workshop_type; ?></option>
                                                                        < ?php
                                                                    }
                                                                }
                                                                ?>   
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">    
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Region</label>
                                                            <select id="region_id" name="region_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                <option value="">All</option>
                                                                < ?php
                                                                if (isset($RegionResult)) {
                                                                    foreach ($RegionResult as $Type) {
                                                                        ?>
                                                                        <option value="< ?= $Type->id; ?>">< ?php echo $Type->region_name; ?></option>
                                                                        < ?php
                                                                    }
                                                                }
                                                                ?>  
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>-->
                                                <div class="row">    
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Graph Type</label>
                                                            <select id="graphtype_id" name="graphtype_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                <option value="1">Line Chart</option>
                                                                <option value="2">Bar Graph</option>
                                                                <option value="3">Both</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="modal-footer">
                                                    <div class="col-md-12 text-right ">  
                                                        <button type="button" class="btn btn-orange" id="btnIndexFilter">
                                                            <span class="ladda-label">Apply</span>
                                                        </button>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>    
                                    </div>    
                                </div>
                                <!-- SETTINGS BOX --> 

                            </div>
                            <!-- INDEX CHART -->
                            <!-- HISTOGRAM PRE CHART -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM WORKSHOP WISE - CE.</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_wksh_ce"> 
                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM PRE CHART -->
                            <!-- HISTOGRAM PRE CHART -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM WORKSHOP WISE - PRE</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_wksh_pre"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="< ?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM PRE CHART -->

                            <!-- HISTOGRAM POST CHART -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM WORKSHOP WISE - POST</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_wksh_post"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="< ?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM POST CHART -->

                            <!-- HISTOGRAM TOPIC WISE PRE CHART -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM TOPIC WISE - PRE</span>
                                        </div>
                                        <!-- <div class="actions">
                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                    <input type="radio" name="options" class="toggle" id="option1">Line Chart</label>
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                    <input type="radio" name="options" class="toggle" id="option2">Bar Chart</label>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_topic_pre"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="<?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM TOPIC WISE PRE CHART -->                            
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM TOPIC WISE - POST</span>
                                        </div>
                                        <!-- <div class="actions">
                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                    <input type="radio" name="options" class="toggle" id="option1">Line Chart</label>
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                    <input type="radio" name="options" class="toggle" id="option2">Bar Chart</label>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_topic_post"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="<?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM TOPIC WISE POST CHART -->

                            <!-- HISTOGRAM TRAINEE WISE PRE CHART -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM TRAINEE WISE - PRE</span>
                                        </div>
                                        <!-- <div class="actions">
                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                    <input type="radio" name="options" class="toggle" id="option1">Line Chart</label>
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                    <input type="radio" name="options" class="toggle" id="option2">Bar Chart</label>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_trainee_pre"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="<?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM TRAINEE WISE PRE CHART -->

                            <!-- HISTOGRAM TRAINEE WISE POST CHART -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM TRAINEE WISE - POST</span>
                                        </div>
                                        <!-- <div class="actions">
                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                    <input type="radio" name="options" class="toggle" id="option1">Line Chart</label>
                                                <label class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                    <input type="radio" name="options" class="toggle" id="option2">Bar Chart</label>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram_trainee_post"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="<?php //echo $base_url;    ?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div> -->


                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM TRAINEE WISE POST CHART -->

                        </div>
                        <!-- STAT FIRST ROW -->


                    </div>
                    <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
                </div>
                <?php //$this->load->view('inc/inc_footer');  ?>
            </div>
            <?php //$this->load->view('inc/inc_quick_nav');   ?>
            <?php $this->load->view('inc/inc_footer_script'); ?>
            <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
            <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>

            <script src="<?php echo $asset_url; ?>assets/global/highcharts/highcharts.src.js"></script>
             <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
            <script>
                                                var trainer_id = "<?php echo $trainer_id; ?>";
                                                var company_id = "<?php echo $company_id; ?>";
                                                jQuery(document).ready(function () {
                         $(".select2_rpt").select2({
            placeholder: 'All',
            width: '100%'
        });
                                                    if (trainer_id != '') {
                                                        dashboard_refresh();
                                                    }
                                                    getWeek();
                                                    $('#btnSearch').click(function (event) {
                                                        event.preventDefault();
                                                        dashboard_refresh();
                                                    });
                                                    $('#btnIndexFilter').click(function (event) {
                                                        event.preventDefault();
                                                        trainer_index_refresh();
                                                        $('#responsive-modal').modal('toggle');
                                                    });
                                                    $('#opt_weekly').change(function (event) {
                                                        event.preventDefault();
                                                        $("#rpt_period").val('weekly');
                                                        trainer_index_refresh();
                                                    });
                                                    $('#opt_monthly').change(function (event) {
                                                        event.preventDefault();
                                                        $("#rpt_period").val('monthly');
                                                        trainer_index_refresh();
                                                    });
                                                    $('#opt_yearly').change(function (event) {
                                                        event.preventDefault();
                                                        $("#rpt_period").val('yearly');
                                                        trainer_index_refresh();
                                                    });
                                                });
                                                function LoadSettings() {
                                                    $('#responsive-modal').modal('show');
                                                }
                                                function getWeek(){
                                                    $.ajax({
                                                        type: "POST",
                                                        data: {year: $('#year').val(),month: $('#month').val()},
                                                        //async: false,
                                                        url: "<?php echo $base_url;?>trainer_dashboard/ajax_getWeeks",
                                                        beforeSend: function () {
                                                            customBlockUI();
                                                        },
                                                        success: function (msg) {                        
                                                            if (msg != '') {
                                                                var Oresult = jQuery.parseJSON(msg);                                                        
                                                                var WStartEndDate = Oresult['WStartEnd'];                            
                                                                var week_option = '<option value="">All Week</option>';                            
                                                                    for (var i = 0; i < WStartEndDate.length; i++) {
                                                                        week_option += '<option value="' + WStartEndDate[i] + '">' +'Week-'+ (i+1) + '</option>';
                                                                    }                             
                                                                $('#week').empty();
                                                                $('#week').append(week_option);
                                                            }
                                                        customunBlockUI();    
                                                        }
                                                    });                               
                                                }
                                                function getCompanyFilter() {
                                                    if($('#company_id').val()==''){
                                                        $('#flt_region_id').empty();
                                                        $('#wrktype_id').empty();
                                                        $('#user_id').empty();
                                                        $('#wsubtype_id').empty();
                                                        $('#subregion_id').empty();
                                                        return false;
                                                    }
                                                    $.ajax({
                                                        type: "POST",
                                                        data: {company_id: $('#company_id').val()},
                                                        //cache: false,
                                                        //async: false,
                                                        url: "<?php echo $base_url; ?>trainer_dashboard/ajax_company_filter",
                                                        beforeSend: function () {
                                                            customBlockUI();
                                                        },
                                                        success: function (msg) {
                                                            if (msg != '') {
                                                                var Oresult = jQuery.parseJSON(msg);
                                                                var user_array = Oresult['user_array'];
                                                                var wtype_array = Oresult['wtype_array'];

                                                                var user_option = '<option value="">Please Select</option>';
                                                                for (var i = 0; i < user_array.length; i++) {
                                                                    user_option += '<option value="' + user_array[i]['userid'] + '">' + user_array[i]['fullname'] + '</option>';
                                                                }
                                                                $('#user_id').empty();
                                                                $('#user_id').append(user_option);

                                                                var wtype_option = '<option value="">All Type</option>';
                                                                for (var i = 0; i < wtype_array.length; i++) {
                                                                    wtype_option += '<option value="' + wtype_array[i]['id'] + '">' + wtype_array[i]['workshop_type'] + '</option>';
                                                                }
                                                                $('#wrktype_id').empty();
                                                                $('#wrktype_id').append(wtype_option);
                                                                var RegionMSt = Oresult['RegionResult'];
                                                                var region_option = '<option value="">All Region</option>';                            
                                                                    for (var i = 0; i < RegionMSt.length; i++) {
                                                                        region_option += '<option value="' + RegionMSt[i]['id'] + '">' + RegionMSt[i]['region_name'] + '</option>';
                                                                    }
                                                                $('#flt_region_id').empty();
                                                                $('#flt_region_id').append(region_option);
                                                                }
                                                            customunBlockUI();
                                                        }
                                                    });
                                                }
                                                // (function($) {
                                                //     $('#btnSearch').click(function(event) {
                                                //         event.preventDefault();
                                                //         // console.log('clicked on '+$(this).attr('id'));
                                                //         dashboard_refresh();
                                                //     });
                                                // })(jQuery);
                                                function dashboard_refresh() {
                                                    //console.log(trainer_id);
                                                    if (trainer_id == '') {
                                                        if ( $('#company_id').val() == "") {
                                                                ShowAlret("Please select Company.!!", 'error');
                                                                return false;
                                                            }
                                                        if ($('#user_id').val() == "") {
                                                            ShowAlret("Please select Trainer.!!", 'error');
                                                            return false;
                                                        }
                                                        if(company_id !=""){
                                var tdata = {company_id: company_id, user_id: $('#user_id').val(),
                                            wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                                            flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val() };
                                                        }else{
                                tdata = {company_id: $('#company_id').val(), user_id: $('#user_id').val(),
                                        wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                                        flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val()};
                                                        }
                                                        
                                                    } else {
                            tdata = {company_id: company_id, user_id: trainer_id,
                                    wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                                    flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val()};
                                                    }
                                                    $.ajax({
                                                        type: "POST",
                                                        data: tdata,
                                                        //async: false,
                                                        url: "<?php echo $base_url; ?>trainer_dashboard/load_quick_statistics",
                                                        beforeSend: function () {
                                                            customBlockUI();
                                                        },
                                                        success: function (response) {
                                                            if (response != '') {
                                                                var json = jQuery.parseJSON(response);
                                                                var workshop_attended = json['workshop_attended'];
                                                                var topic_trained = json['topic_trained'];
                                                                var subtopic_trained = json['subtopic_trained'];
                                                                var overall_post_accuracy = json['overall_post_accuracy'];
                                                                var average_ce = json['average_ce'];
                                                                var workshop_lastweek = json['workshop_lastweek'];
                                                                var best_post_accuracy = json['best_post_accuracy'];
                                                                var best_ce = json['best_ce'];
                                                                var lowest_ce = json['lowest_ce'];
                                                                var topic_top_five_table = json['topic_top_five_table'];
                                                                var topic_bottom_five_table = json['topic_bottom_five_table'];

                                                                if ((typeof (workshop_attended) == 'undefined' || workshop_attended == null)) {
                                                                    workshop_attended = 0;
                                                                }
                                                                if ((typeof (topic_trained) == 'undefined' || topic_trained == null)) {
                                                                    workshop_attended = 0;
                                                                }
                                                                if ((typeof (subtopic_trained) == 'undefined' || subtopic_trained == null)) {
                                                                    subtopic_trained = 0;
                                                                }
                                                                if ((typeof (overall_post_accuracy) == 'undefined' || overall_post_accuracy == null)) {
                                                                    overall_post_accuracy = 0;
                                                                }
                                                                if ((typeof (average_ce) == 'undefined' || average_ce == null)) {
                                                                    average_ce = 0;
                                                                }
                                                                if ((typeof (workshop_lastweek) == 'undefined' || workshop_lastweek == null)) {
                                                                    workshop_lastweek = 0;
                                                                }
                                                                if ((typeof (best_post_accuracy) == 'undefined' || best_post_accuracy == null)) {
                                                                    best_post_accuracy = 0;
                                                                }
                                                                if ((typeof (best_ce) == 'undefined' || best_ce == null)) {
                                                                    best_ce = 0;
                                                                }
                                                                if ((typeof (lowest_ce) == 'undefined' || lowest_ce == null)) {
                                                                    lowest_ce = 0;
                                                                }


                                                                $('#workshop_attended_counter').attr('data-value', workshop_attended);
                                                                $('#workshop_attended_counter').counterUp();

                                                                $('#topic_trained_counter').attr('data-value', topic_trained);
                                                                $('#topic_trained_counter').counterUp();

                                                                $('#subtopic_trained_counter').attr('data-value', subtopic_trained);
                                                                $('#subtopic_trained_counter').counterUp();

                                                                $('#overall_post_accuracy_counter').attr('data-value', overall_post_accuracy);
                                                                $('#overall_post_accuracy_counter').counterUp();

                                                                $('#average_ce_counter').attr('data-value', average_ce);
                                                                $('#average_ce_counter').counterUp();

                                                                $('#last_week_wksh_counter').attr('data-value', workshop_lastweek);
                                                                $('#last_week_wksh_counter').counterUp();

                                                                $('#best_post_counter').attr('data-value', best_post_accuracy);
                                                                $('#best_post_counter').counterUp();

                                                                if (best_ce == "NP") {
                                                                    $('#best_ce_sign').hide();
                                                                }
                                                                $('#best_ce_counter').attr('data-value', best_ce);
                                                                $('#best_ce_counter').counterUp();

                                                                $('#lowest_ce_counter').attr('data-value', lowest_ce);
                                                                $('#lowest_ce_counter').counterUp();

                                                                if (topic_top_five_table != '') {
                                                                    $('#wksh-top-five tbody').empty();
                                                                    $('#wksh-top-five tbody').append(topic_top_five_table);
                                                                }
                                                                if (topic_bottom_five_table != '') {
                                                                    $('#wksh-bottom-five tbody').empty();
                                                                    $('#wksh-bottom-five tbody').append(topic_bottom_five_table);
                                                                }
                                                                trainer_index_refresh();
                                                            }
                                                            customunBlockUI();
                                                        },
                                                        // error: function () {
                                                        //     customunBlockUI();
                                                        // },
                                                        // complete: function () {
                                                        //     customunBlockUI();
                                                        // },
                                                    });
                                                }
                                                function trainer_index_refresh() {
                                                    if (trainer_id == '') {
                                                        var tdata = {company_id: $('#company_id').val(),user_id: $('#user_id').val(),
                                                        rpt_period:$('#rpt_period').val(),
                            month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),graphtype_id :$('#graphtype_id').val(),
                            wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                            flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val()};
                                                    }else{
                                                        tdata = {company_id: company_id, user_id: trainer_id,rpt_period:$('#rpt_period').val(),
                            month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),graphtype_id :$('#graphtype_id').val(),
                            wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                            flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val()};
                                                    }
                                                    $.ajax({
                                                        type: "POST",
                                                        data:tdata,                                                        
                                                        async: false,
                                                        url: "<?php echo $base_url; ?>trainer_dashboard/load_trainer_index",
                                                        beforeSend: function () {
                                                            customBlockUI();
                                                        },
                                                        success: function (response) {
                                                            if (response != '') {
                                                                var json = jQuery.parseJSON(response);
                                                                $("#trainer_index").append(json['index_graph']);
                                                                $("#histogram_wksh_ce").html(json['histogram_CE']);
                                                                $("#histogram_wksh_pre").html(json['histogram_wksh_pre']);
                                                                $("#histogram_wksh_post").html(json['histogram_wksh_post']);
                                                                $("#histogram_topic_pre").html(json['histogram_topic_pre']);
                                                                $("#histogram_topic_post").html(json['histogram_topic_post']);
                                                                $("#histogram_trainee_pre").html(json['histogram_trainee_pre']);
                                                                $("#histogram_trainee_post").html(json['histogram_trainee_post']);
                                                            }
                                                            customunBlockUI();
                                                        },
                                                    });
                                                }
                    function getTrainerwiseData(){      
                        if($('#user_id').val()==''){
                            $('#flt_region_id').empty();
                            $('#wrktype_id').empty();
                            $('#subregion_id').empty();
                            $('#wsubtype_id').empty();
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            data: {company_id: $('#company_id').val(),trainer_id:$('#user_id').val()},
                            //async: false,
                            url: "<?php echo $base_url;?>trainer_dashboard/ajax_trainerwise_data",
                            beforeSend: function () {
                                customBlockUI();
                            },
                            success: function (msg) {                        
                                if (msg != '') {
                                    var Oresult = jQuery.parseJSON(msg);
                                    var wrktype_option = '<option value="0">All Type</option>';
                                    var WtypeMst = Oresult['WtypeResult'];                            
                                    for (var i = 0; i < WtypeMst.length; i++) {
                                        wrktype_option += '<option value="' + WtypeMst[i]['wtype_id'] + '">' + WtypeMst[i]['workshop_type'] + '</option>';
                                    }
                                    $('#wrktype_id').empty();
                                    $('#wrktype_id').append(wrktype_option);

                                    var region_option = '<option value="0">All Region</option>';
                                    var RegionMst = Oresult['RegionResult'];
                                    for (var i = 0; i < RegionMst.length; i++) {
                                        region_option += '<option value="' + RegionMst[i]['region_id'] + '">' + RegionMst[i]['region_name'] + '</option>';
                                    }
                                    $('#flt_region_id').empty();
                                    $('#flt_region_id').append(region_option);
                                }
                            customunBlockUI();    
                            }
                        });
                    }
                    function getWTypewiseData(){
                        if($('#wrktype_id').val()==''){                            
                            $('#wsubtype_id').empty();
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            data: {wrktype_id:$('#wrktype_id').val()},
                            //async: false,
                            url: "<?php echo $base_url;?>trainer_dashboard/ajax_wtypewise_data",
                            beforeSend: function () {
                                customBlockUI();
                            },
                            success: function (msg) {                        
                                if (msg != '') {
                                    var Oresult = jQuery.parseJSON(msg);
                                    var wsubtype_option = '<option value="">All Workshop Sub-Type</option>';
                                    var WsubtypeMst = Oresult['WsubtypeResult'];                            
                                    for (var i = 0; i < WsubtypeMst.length; i++) {
                                        wsubtype_option += '<option value="' + WsubtypeMst[i]['id'] + '">' + WsubtypeMst[i]['wsubtype'] + '</option>';
                                    }
                                    $('#wsubtype_id').empty();
                                    $('#wsubtype_id').append(wsubtype_option);                            
                                }
                            customunBlockUI();    
                            }
                        });
                    }
                    function getRegionwiseData(){
                        if($('#flt_region_id').val()==''){                            
                            $('#subregion_id').empty();                            
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            data: {region_id:$('#flt_region_id').val()},
                            //async: false,
                            url: "<?php echo $base_url;?>trainer_dashboard/ajax_regionwise_data",
                            beforeSend: function () {
                                customBlockUI();
                            },
                            success: function (msg) {                        
                                if (msg != '') {
                                    var Oresult = jQuery.parseJSON(msg);
                                    var subregion_option = '<option value="">All Sub-Region</option>';
                                    var SubregionMst = Oresult['SubregionResult'];                            
                                    for (var i = 0; i < SubregionMst.length; i++) {
                                        subregion_option += '<option value="' + SubregionMst[i]['id'] + '">' + SubregionMst[i]['subregion'] + '</option>';
                                    }
                                    $('#subregion_id').empty();
                                    $('#subregion_id').append(subregion_option);                            
                                }
                            customunBlockUI();    
                            }
                        });
                    }
            </script>
    </body>
</html>