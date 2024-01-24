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
        <link href="<?php echo $asset_url;?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />
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
                                    <a href="#">Home</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Supervisor Dashboard</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </div>
                            </div>
                        </div>
                        <!-- PAGE BAR -->
                        <h1 class="page-title"> Supervisor Dashboard
                            <small>- overview statistics, Region and Workshop Type,Index and Histogram </small>
                        </h1>
                        
                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                   Filter Data </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse ">
                                            <div class="panel-body" >
                                                <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">
                                                    <div class="row margin-bottom-10">
                                                         <?php if ($company_id == "") { ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                    <div class="col-md-9" style="padding:0px;">
                                                                        <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData()">
                                                                            <option value="">Select Company</option>
                                                                            <?php
                                                                                foreach ($CompanyData as $cmp) {?>
                                                                                <option value="<?=$cmp->id;?>"><?php echo $cmp->company_name; ?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <?php } ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Trainer Name&nbsp;</label>
                                                                    <div class="col-md-9" style="padding:0px;">
                                                                        <select id="user_id" name="user_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getTrainerwiseData()">
                                                                            <option value="0">All Trainer</option>
                                                                            <?php foreach ($TrainerSet as $cmp) { ?>
                                                                                <option value="<?= $cmp->userid; ?>" ><?php echo $cmp->fullname; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="row margin-bottom-10">                                                         
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="wrktype_id" name="wrktype_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData()">
                                                                        <option value="0">All Type</option>
                                                                         <?php
                                                                        if (isset($wtype_array)) {
                                                                            foreach ($wtype_array as $Type) {
                                                                                ?>
                                                                                <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
                                                                                <?php
                                                                            }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>                                                             
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Workshop Sub-Type&nbsp;</label>
                                                                <div class="col-md-8" style="padding:0px;">
                                                                    <select id="wsubtype_id" name="wsubtype_id" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                        <option value="">All Sub-Type</option>                                                                            
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row margin-bottom-10">                                                         
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Region&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="flt_region_id" name="flt_region_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getRegionwiseData()">
                                                                        <option value="0">All Region</option>
                                                                        <?php
                                                                        if (isset($RegionResult)) {
                                                                            foreach ($RegionResult as $Type) {
                                                                                ?>
                                                                                <option value="<?= $Type->id; ?>"><?php echo $Type->region_name; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>  
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>                                                             
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Workshop Sub-Region&nbsp;</label>
                                                                <div class="col-md-8" style="padding:0px;">
                                                                    <select id="subregion_id" name="subregion_id" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                        <option value="">All Sub-Region</option>                                                                            
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh()">Search</button>
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
                       
                        <div class="row">                            
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Quick Statistics</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">                                        
                                                                                
                                        <div class="clearfix"></div>
                                        <div class="portlet-title potrait-title-mar">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-dark hide"></i>
                                                <span class="caption-subject font-dark bold uppercase"><h3>Life Time</h3></span>
                                            </div>
                                        </div>
                                        <!-- STAT LIFE TIME ROW -->
                                        <div class="row" style="margin-top: 5px;">                                            
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="total_workshop" data-value="0">0</span>
                                                            </h3>
                                                            <small>Total Workshop</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="highest_ce" data-value="0"></span><span id="best_ce_sign">%</span>
                                                            </h3>
                                                            <small>Highest C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id= "lowest_ce" data-value="0">0</span>
                                                            </h3>
                                                            <small>Lowest C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 5px;">                                            
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="avg_ce" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Avg C.E </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span id="best_region" >-</span>
                                                            </h3>
                                                            <small>Best Region</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                           
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span  id= "worst_region" >-</span>
                                                            </h3>
                                                            <small>Worst Region</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase"><h3 id="date_lable">Last Month</h3></span>
                                        </div>
                                    </div>
                                        <!-- Last Month ROW -->
                                        <div class="row" style="margin-top: 5px;">
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="last_total_workshop" data-value="0">0</span>
                                                            </h3>
                                                            <small>Total Workshop</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="last_highest_ce" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Highest Workshop C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                           
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="last_lowest_ce" data-value="0">0</span>
                                                            </h3>
                                                            <small>Lowest Workshop C.E</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                           
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id= "last_avg_ce" data-value="0">0</span><span>%</span>
                                                            </h3>
                                                            <small>Avg C.E </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                                                                   
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span id="last_best_region" >-</span>
                                                            </h3>
                                                            <small>Best Region</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                           
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span  id="last_worst_region" >-</span>
                                                            </h3>
                                                            <small>Worst Region</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>                                        
                                        <div class="row" style="margin-top: 5px;">                                            
                                        </div>
                                        <div class="col-lg-6 col-xs-12 col-sm-12" >
                                            
                                                <div class="portlet-title potrait-title-mar">
                                                    <div class="caption">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        <span class="caption-subject font-dark bold uppercase">Region Wise Performance</span>
                                                    </div>
                                                </div>
                                                <div class="poortlet-body" style="padding: 0px !important;width: 955px" id="region_performance">
                                                   
                                                </div>
                                            
                                        </div>
                                        <div class="col-lg-6 col-xs-12 col-sm-12" >
                                            
                                                <div class="portlet-title potrait-title-mar">
                                                    <div class="caption">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        <span class="caption-subject font-dark bold uppercase">Workshop Type Wise Performance</span>
                                                    </div>
                                                </div>
                                                <div class="poortlet-body" style="padding: 0px !important;width: 955px" id="type_performance">
                                                   
                                                </div>
                                            
                                        </div>
                                        <div class="clearfix"></div>    
                                    </div>
                                </div>
                            </div>
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
                                    <div class="portlet-body" style="padding: 0px !important" id="supervisor_index"> 
                                        <!-- <div id="site_statistics_loading">
                                            <img src="< ?php //echo $base_url;?>assets/global/img/loading.gif" alt="loading" /> 
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
                            <!-- HISTOGRAM CHART -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">HISTOGRAM</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="histogram">                                         
                                    </div>
                                </div>
                            </div>
                            <!-- HISTOGRAM CHART -->
                        </div>
                        <!-- STAT FIRST ROW -->
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url;?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/highstock.js"></script>
         <?php if($acces_management->allow_print){ ?>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script> 
            var firsttimeload=1;
            var Company_id='<?php echo $company_id ?>';
            var StartDate="<?php echo $start_date; ?>";
            var EndDate="<?php echo $end_date; ?>";
            jQuery(document).ready(function() {
                 $(".select2_rpt").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
                getWeek();
                $('#btnIndexFilter').click(function(event) {
                    event.preventDefault();
                    firsttimeload=0;
                    supervisor_index_refresh();                    
                    $('#responsive-modal').modal('toggle');
                });
                $('#opt_weekly').change(function(event) {
                    event.preventDefault();
                    $("#rpt_period").val('weekly');
                    supervisor_index_refresh();                    
                });
                $('#opt_monthly').change(function(event) {
                    event.preventDefault();
                    $("#rpt_period").val('monthly');
                    supervisor_index_refresh();                    
                });
                $('#opt_yearly').change(function(event) {
                    event.preventDefault();
                    $("#rpt_period").val('yearly');
                    supervisor_index_refresh();                    
                });
                
                if (!jQuery().daterangepicker) {
                    return;
                }
                if(Company_id !=""){
                    dashboard_refresh();
                }
                if (jQuery().datepicker) {
                    $('.date-picker').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                }
               
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
                     "autoApply": true,
    "linkedCalendars": false,
    "autoUpdateInput": false,
                    "locale": {
                        "format": "DD-MM-YYYY",
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
                    "startDate": StartDate,
                    "endDate": EndDate,
                    opens: (App.isRTL() ? 'right' : 'left'),
                }, function(start, end, label) {
                    if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                        $('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                        
                    }
                    
                });
                 if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                    $('#dashboard-report-range span').html(moment().subtract('month', 1).startOf('month').format('MMMM D, YYYY') + ' - ' + moment().subtract('month', 1).endOf('month').format('MMMM D, YYYY'));
                }
                $('#dashboard-report-range').show();
                $('#dashboard-report-range').on('apply.daterangepicker', function(ev, picker) {
                    //console.log(ev.chosenLabel);
                    //console.log(picker.chosenLabel);
                    $('#date_lable').text(picker.chosenLabel);
                    StartDate=picker.startDate.format('DD-MM-YYYY');
                    EndDate=picker.endDate.format('DD-MM-YYYY');
                    dashboard_refresh();
                  });
            });
            function getWeek(){
                $.ajax({
                    type: "POST",
                    data: {year: $('#year').val(),month: $('#month').val()},
                    async: false,
                    url: "<?php echo $base_url;?>supervisor_dashboard/ajax_getWeeks",
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
            function dashboard_refresh(){
                //var start_date = $('#start_date').val();
               // var end_date   = $('#end_date').val();  
               if ($('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data:{company_id: $('#company_id').val(),StartDate:StartDate,EndDate:EndDate,Trainer_id: $('#user_id').val(),
                          wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                          flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>supervisor_dashboard/getdashboardData",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (data) {
                        if (data != '') {
                            var json                   = jQuery.parseJSON(data);
                            var allHLAdata       = json['HLAdata1'];
							console.log(allHLAdata);
                            var LastMonthHLAdata       = json['HLAdata2'];
                                //$('#date_lable').text("Filtered Data (From "+start_date+" To "+end_date+")");                                                                                               
                                    $('#total_workshop').attr('data-value',json['TotalWorkshop1']);
                                    $('#total_workshop').counterUp();
									if(allHLAdata['MinCE'] != allHLAdata['MaxCE']){
										$('#lowest_ce').attr('data-value',allHLAdata['MinCE']+'%');
										$('#lowest_ce').counterUp();
									}else{
										$('#lowest_ce').attr('data-value','-');
										$('#lowest_ce').counterUp();
									}
                                    
                                                                        
                                    $('#highest_ce').attr('data-value',allHLAdata['MaxCE']);
                                    $('#highest_ce').counterUp();
                                        
                                
                                    $('#avg_ce').attr('data-value',allHLAdata['Avg']);
                                    $('#avg_ce').counterUp();  
                                                                        
                                    $('#best_region').text(json['BestRegionq1']);
                                   //$('#best_region').counterUp();
                                        
                                    $('#worst_region').text(json['WorstRegionq1']);
                                    //$('#worst_region').counterUp(); 
                                     
                                //$('#date_lable').text("Filtered Data (From "+start_date+" To "+end_date+")"); 
                                //console.log(json['TotalWorkshop2']);
                                    $('#last_total_workshop').attr('data-value',json['TotalWorkshop2']);
                                    $('#last_total_workshop').counterUp();

									if(LastMonthHLAdata['MinCE'] != LastMonthHLAdata['MaxCE']){
										$('#last_lowest_ce').attr('data-value',LastMonthHLAdata['MinCE']+'%');
										$('#last_lowest_ce').counterUp();
									}else{
										$('#last_lowest_ce').attr('data-value','-');
										$('#last_lowest_ce').counterUp();
									}
                                
                                        
                                    $('#last_highest_ce').attr('data-value',LastMonthHLAdata['MaxCE']);
                                    $('#last_highest_ce').counterUp();
                                        
                                
                                    $('#last_avg_ce').attr('data-value',LastMonthHLAdata['Avg']);
                                    $('#last_avg_ce').counterUp();  
                                                                        
                                    $('#last_best_region').text(json['BestRegionq2']);
                                    //$('#last_best_region').counterUp();
                                        
                                    $('#last_worst_region').text(json['WorstRegionq2']);
                                    //$('#last_worst_region').counterUp();  
                                    $('#region_performance').html(json['region_graph']);
                                    $('#type_performance').html(json['type_graph']);
                                    
                                    supervisor_index_refresh();
                                    customunBlockUI();      
                        }
                    }
                });
            }
            function supervisor_index_refresh(){
                if ($('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),
                        Trainer_id: $('#user_id').val(),
                        rpt_period:$('#rpt_period').val(),
                        //wtype_id:$('#wtype_id').val(),region_id:$('#region_id').val(),
                        month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),graphtype_id :$('#graphtype_id').val(),
                        wrktype_id:$('#wrktype_id').val(),wsubtype_id:$('#wsubtype_id').val(),
                        flt_region_id:$('#flt_region_id').val(),subregion_id:$('#subregion_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>supervisor_dashboard/load_supervisor_index/"+firsttimeload,
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (response) {
                        if (response != '') {
                            var json = jQuery.parseJSON(response);
                            $("#supervisor_index").html(json['index_graph']);
                            $("#histogram").html(json['histogram']);
                        }
                        customunBlockUI();
                    },
                });
            }
            function getCompanywiseData(){
                $('#wrktype_id').empty();
                $('#wsubtype_id').empty();
                $('#flt_region_id').empty();
                $('#subregion_id').empty();
                $.ajax({
                    type: "POST",
                    data: {data: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>supervisor_dashboard/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
							
                            var user_option = '<option value="0">All Trainer</option>';
                            var user_array = Oresult['TrainerSet'];
                            for (var i = 0; i < user_array.length; i++) {
                                user_option += '<option value="' + user_array[i]['userid'] + '">' + user_array[i]['fullname'] + '</option>';
                            }
                            $('#user_id').empty();
                            $('#user_id').append(user_option);
                            var WtypeMSt = Oresult['WtypeResult'];
                            var RegionMSt = Oresult['RegionResult'];
                            var wtype_option = '<option value="0">All Type</option>';                            
                                for (var i = 0; i < WtypeMSt.length; i++) {
                                    wtype_option += '<option value="' + WtypeMSt[i]['id'] + '">' + WtypeMSt[i]['workshop_type'] + '</option>';
                                }
                            var region_option = '<option value="0">All Region</option>';                            
                            for (var i = 0; i < RegionMSt.length; i++) {
                                region_option += '<option value="' + RegionMSt[i]['id'] + '">' + RegionMSt[i]['region_name'] + '</option>';
                            }    
                            $('#wrktype_id').empty();
                            $('#wrktype_id').append(wtype_option);
                            $('#flt_region_id').empty();
                            $('#flt_region_id').append(region_option);
                                }
                    customunBlockUI();    
                                }    
                });
                        }
            function getTrainerwiseData(){
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),trainer_id:$('#user_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>supervisor_dashboard/ajax_trainerwise_data",
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
                            $('#wsubtype_id').empty();
                            $('#wrktype_id').empty();
                            $('#wrktype_id').append(wrktype_option);
                            
                            var region_option = '<option value="0">All Region</option>';
                            var RegionMst = Oresult['RegionResult'];
                            for (var i = 0; i < RegionMst.length; i++) {
                                region_option += '<option value="' + RegionMst[i]['region_id'] + '">' + RegionMst[i]['region_name'] + '</option>';
                            }
                            $('#flt_region_id').empty();
                            $('#subregion_id').empty();
                            $('#flt_region_id').append(region_option);
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getWTypewiseData(){
                $.ajax({
                    type: "POST",
                    data: {wrktype_id:$('#wrktype_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>supervisor_dashboard/ajax_wtypewise_data",
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
                $.ajax({
                    type: "POST",
                    data: {region_id:$('#flt_region_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>supervisor_dashboard/ajax_regionwise_data",
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