<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <!--<link rel="stylesheet" type="text/css" href="< ?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />-->
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <style>
            .arrow-row{width:100%; height:auto;}
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
            .theme-panel>.theme-options>.theme-option>span{
                width: 115px;
            }
            .no-padding{
                padding:0px !important;
            }
            .page-content-white .page-title {
                margin: 15px 0;
                font-size: 22px;
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
                        <form id="rangeform">
                            <div class="theme-panel hidden-xs hidden-sm">
                                <div class="toggler" >
                                </div>
                                <div class="toggler-close" >
                                </div>

                                <div class="theme-options" >

                                    <div class="theme-option theme-colors clearfix">
                                        <span>THRESHOLD COLOR</span>
                                    </div>
                                    <?php
                                    if (count($ThresholdData) > 0) {
                                        $slot_row = 1;
                                        foreach ($ThresholdData as $rng) {
                                            ?>
                                            <div class="theme-option">
                                                <span style="float: left;background-color: <?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>">&nbsp;
                                                </span>
                                                <div class="col-md-4" ><input  class=" form-control input-sm " id="range_from<?php echo $slot_row; ?>" name="range_from[]" placeholder="" type="text" value="<?php echo ($rng->range_from != '' ? $rng->range_from : ''); ?>"></div>
                                                <div class="col-md-4"><input  class="form-control input-sm " id="range_to<?php echo $slot_row; ?>" name="range_to[]" placeholder="" type="text" value="<?php echo ($rng->range_to != '' ? $rng->range_to : ''); ?>"></div>
                                                <input class="form-control input-sm " id="range_color<?php echo $slot_row; ?>" name="range_color[]" placeholder="" type="hidden" value="<?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>">
                                                <input class="form-control input-sm " id="range_id<?php echo $slot_row; ?>" name="range_id[]" placeholder="" type="hidden" value="<?php echo ($rng->id != '' ? $rng->id : ''); ?>">
                                            </div>
                                            <?php $slot_row++;
                                        }
                                    }
                                    ?>
                                    <div class="theme-option theme-colors clearfix" style="margin-top:10px">
                                        <span>Pass/Fail Color</span>
                                    </div>
                                    <?php
                                    if (count($ResultData) > 0) {
                                        $result_row = 1;
                                        foreach ($ResultData as $rng) {
                                            ?>
                                            <div class="theme-option">
                                                <span style="float: left;background-color: <?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>"><?php echo $rng->assessment_status; ?>
                                                </span>
                                                <?php if ($rng->range_from == 0 && $rng->range_to == 0) { ?>    
                                                    <div class="col-md-4"><input  class="form-control input-sm " id="result_from<?php echo $result_row; ?>" name="result_from[]" placeholder="" type="text" value="0" readonly></div>
                                                    <div class="col-md-4" ><input  class="form-control input-sm " id="result_to<?php echo $result_row; ?>" name="result_to[]" placeholder="" type="text" value="0" readonly></div>
                                                <?php } else { ?>
                                                    <div class="col-md-4"><input  class="form-control input-sm " id="result_from<?php echo $result_row; ?>" name="result_from[]" placeholder="" type="text" value="<?php echo ($rng->range_from != '' ? $rng->range_from : ''); ?>"></div>
                                                    <div class="col-md-4" ><input  class="form-control input-sm " id="result_to<?php echo $result_row; ?>" name="result_to[]" placeholder="" type="text" value="<?php echo ($rng->range_to != '' ? $rng->range_to : ''); ?>"></div>
                                            <?php } ?>
                                                <input class="form-control input-sm " id="result_color<?php echo $result_row; ?>" name="result_color[]" placeholder="" type="hidden" value="<?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>">
                                                <input class="form-control input-sm " id="result_status<?php echo $result_row; ?>" name="result_status[]" placeholder="" type="hidden" value="<?php echo ($rng->assessment_status != '' ? $rng->assessment_status : ''); ?>">
                                            </div>
                                            <?php $result_row++;
                                        }
                                    }
                                    ?>
                                    <div class="col-md-offset-9 pull-right" style="margin:10px;">
                                        <button type="button" name="submit" id="btnSubmit" class="btn btn-orange" onclick="SubmitData();">Change</button>
                                    </div>

                                </div>  

                            </div>
                        </form>
                        <form id="FilterFrm" name="FilterFrm" method="post">
                            <div class="page-bar">
                                <ul class="page-breadcrumb">
                                    <li>
                                        <a href="#">Dashboard</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <span>Supervisor</span>
                                    </li>
                                </ul>
                               <!--<div class="col-md-1 page-breadcrumb"></div>-->
                                <div class="col-md-3 page-breadcrumb" style="margin-left: 40px;">
                                    <div class="form-group">
                                        <!--<div class="row" style="padding:0px;">-->
                                        <label class="control-label col-md-4" style="font-size: 15px; padding-right: 0px;">Report by&nbsp;</label>
                                        <div class="col-md-8 " style="padding-left: 0px;" >
                                            <select id="report_by" name="report_by" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="dashboard_refresh()">                                                                        
                                                <option value="0">Assessment</option>
                                                <option value="1">Parameter</option>
                                            </select>
                                        </div>
                                        <!--</div>-->
                                    </div>
                                </div>
                                <div class="col-md-2 page-breadcrumb">
                                    <div class="form-group">
                                        <!--<label class="control-label col-md-2">&nbsp;</label>-->
                                        <div class="col-md-11" style="padding:0px;">
                                            <select id="region_id" name="region_id" class="form-control input-sm select2me" style="width: 100%" onchange="dashboard_refresh()">
                                            <option value="">All Region</option>
                                            <?php foreach ($region_data as $rg) { ?>
                                                    <option value="<?= $rg->region_id; ?>"><?php echo $rg->region_name; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 page-breadcrumb">
                                    <div class="form-group">
                                        <!--<label class="control-label col-md-2">&nbsp;</label>-->
                                        <div class="col-md-11" style="padding:0px;">
                                            <select id="store_id" name="store_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="dashboard_refresh()">                                                                        
                                                <option value="">All Store</option>
                                                <?php foreach ($store_data as $st) { ?>
                                                    <option value="<?= $st->store_id; ?>"><?php echo $st->store_name; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="page-toolbar">
                                    <div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                        <i class="icon-calendar"></i>&nbsp;
                                        <span class="thin uppercase hidden-xs"></span>&nbsp;
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- PAGE BAR -->      
                            <h3 class="page-title">
                             Go Live Supervisor Dashboard <small>reports &amp; statistics</small>
                            </h3>
                            <div class="clearfix margin-top-10"></div>
                            <div class="row">                            
                                <div class="col-lg-12 col-xs-12 col-sm-12">
                                    <div class="portlet light bordered" >
                                        <div class="portlet-title potrait-title-mar">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-dark hide"></i>
                                                <span class="caption-subject font-dark bold uppercase">Quick Statistics</span>
                                            </div>
                                        </div>
                                        <div class="portlet-body">                                                                                                                        
                                            <div class="clearfix"></div>

                                            <!-- STAT LIFE TIME ROW -->
                                            <div class="row" style="margin-top: 5px;"> 
                                                <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="dashboard-stat">
                                                        <div class="display">
                                                            <div class="number">
                                                                <h3 class="font-orange-sharp">
                                                                    <span data-counter="counterup" id="total_assessment" data-value="0">0</span>
                                                                </h3>
                                                                <small>Total Assessment</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="dashboard-stat">
                                                        <div class="display">
                                                            <div class="number">
                                                                <h3 class="font-orange-sharp">
                                                                    <span data-counter="counterup" id="candidate_count" data-value="0">0</span>
                                                                </h3>
                                                                <small>Candidate Count</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="dashboard-stat">
                                                        <div class="display">
                                                            <div class="number">
                                                                <h3 class="font-orange-sharp">
                                                                    <span data-counter="counterup" id="average_accuracy" data-value="0">0</span>
                                                                </h3>
                                                                <small>Average Accuracy</small>
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
                                                                    <span data-counter="counterup" id="highest_accuracy" data-value="0">0</span>
                                                                </h3>
                                                                <small>Highest Accuracy</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="dashboard-stat">
                                                        <div class="display">
                                                            <div class="number">
                                                                <h3 class="font-orange-sharp">
                                                                    <span data-counter="counterup" id="lowest_accuracy" data-value="0">0</span>
                                                                </h3>
                                                                <small>Lowest Accuracy</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="dashboard-stat">
                                                        <div class="display">
                                                            <div class="number">
                                                                <h3 class="font-orange-sharp">
                                                                    <span data-counter="counterup" id="attempt_taken" data-value="--">--</span>
                                                                </h3>
                                                                <small>Attempts taken per Assessment</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 5px;">  
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                        <div class="portlet-title potrait-title-mar">
                                                            <div class="caption">
                                                                <i class="icon-bar-chart font-dark hide"></i>
                                                                <span class="caption-subject font-dark bold uppercase">Strength</span>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body" style="padding: 0px !important"> 
                                                            <div class="table-scrollable table-scrollable-borderless">
                                                                <table class="table table-hover table-light" id="asmnt-top-five">
                                                                    <thead>
                                                                        <tr class="uppercase">
                                                                            <th class="wksh-td th-desc" width="80%"> PARAMETER </th>
                                                                            <th class="wksh-td" width="20%"> Accuracy </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                    <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                        <div class="portlet-title potrait-title-mar">
                                                            <div class="caption">
                                                                <i class="icon-bar-chart font-dark hide"></i>
                                                                <span class="caption-subject font-dark bold uppercase">Improvements</span>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body" style="padding: 0px !important"> 
                                                            <div class="table-scrollable table-scrollable-borderless">
                                                                <table class="table table-hover table-light" id="asmnt-bottom-five">
                                                                    <thead>
                                                                        <tr class="uppercase">
                                                                            <th class="wksh-td th-desc" width="80%"> PARAMETER </th>
                                                                            <th class="wksh-td" width="20%"> Accuracy </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4" style="padding: 0px 5px 5px 5px;" id="region_performance">

                                                </div>
                                            </div>   
                                            <div class="clearfix"></div> 
                                            <hr/>                                        

                                        </div>
                                    </div>
                                </div>                            
                            </div>                        
                            <div class="row">    
                                <!-- INDEX CHART -->
                                <div class="col-lg-12 col-xs-12 col-sm-12">
                                    <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                        <div class="portlet-title potrait-title-mar">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-dark hide"></i>
                                                <span class="caption-subject font-dark bold uppercase th-desc">Parameter</span>
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
                                        <div class="portlet-body bordered">                                             
                                            <div class="row margin-bottom-15" id="assessment_index" style="padding: 0px !important"></div>
                                        </div>
                                        <div class="clearfix"></div> 
                                        <hr/> 
                                        <div class="portlet-title potrait-title-mar">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-dark hide"></i>
                                                <span class="caption-subject font-dark bold uppercase ">Parameter Level Analysis</span>
                                            </div>
                                            <div class="col-md-4 pull-right">    
                                                <div class="form-group">
                                                    <label class="col-md-4" style="font-size: 15px;">Parameters</label>
                                                    <div class="col-md-8">
                                                    <select id="parameter_id" name="parameter_id" class="form-control input-sm select2" placeholder="Select Parameter" style="width: 100%" onchange="assessment_index_refresh();">
                                                        <option value="">All</option>
                                                        <?php foreach ($parameter_data as $para) { ?>
                                                            <option value="<?= $para->id; ?>"><?php echo $para->description; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="actions">
                                                    
                                            </div>
                                        </div>
                                        <div class="portlet-body">                                             
                                            <div class="row margin-bottom-15" id="parameter_index" style="padding: 0px !important"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xs-12 col-sm-12">
                                    <div class="portlet light bordered" >
                                        <div class="portlet-title potrait-title-mar">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-dark hide"></i>
                                                <span class="caption-subject font-dark bold uppercase">Region</span>
                                            </div>
                                            <div class="actions">
                                                <a data-toggle="modal"  class="btn btn-circle btn-default" href="#filter-modal" >
                                                    Filter By <i class="fa fa-angle-down"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row margin-bottom-15 owl-carousel" id="region_data" style="opacity:1!important; position: relative;">

                                            </div>
                                            <div class="arrow-row row" style="padding:0; margin-bottom: 15px;">
                                                <div class="col-lg-6 col-xs-6 col-sm-6" id="btn-naxt" style="cursor: pointer; float:right; text-align: left; display: none;" onclick="dashboard_region_change(2);"><img src="<?php echo $asset_url; ?>assets/images/play-button-right.png"></div>
                                                <div class="col-lg-6 col-xs-6 col-sm-6" id="btn-prev" style="cursor: pointer; float:left; text-align: right; display: none;" onclick="dashboard_region_change(1);"><img src="<?php echo $asset_url; ?>assets/images/play-button-left.png"></div>
                                            </div>    

                                            <div class="clearfix"></div>
                                            <div class="row" id="region_table"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>	
                        </form>
                    </div>                    
                </div>
            </div>                    
        </div>
        <!-- SETTINGS BOX -->
        <div id="filter-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form id="frmModalForm" name="frmModalForm" onsubmit="return false;"> 
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Filter By</h4>
                        </div>
                        <div class="modal-body">
                            <div id='dsk' style="display: none">&nbsp;</div>                                                
<!--                            <div class="row">
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Region</label>
                                        <select id="region_id" name="region_id[]" class="form-control input-sm select2" style="width: 100%" multiple="">
                                            <option value="">All Region</option>
                                        < ?php foreach ($region_data as $rgn) { ?>
                                                <option value="< ?= $rgn->region_id; ?>">< ?php echo $rgn->region_name; ?></option>
                                        < ?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Assessment</label>
                                        <select id="assessment_id" name="assessment_id[]" class="form-control input-sm select2" style="width: 100%" multiple="">
                                            <?php foreach ($assessment_data as $val) { ?>
                                                <option value="<?= $val->assessment_id; ?>"><?php echo $val->assessment; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right ">  
                                <button type="button" class="btn btn-orange" id="btnIndexFilter" onclick="dashboard_region_refresh();">
                                    <span class="ladda-label">Apply</span>
                                </button>

                            </div>
                        </div>
                    </form>
                </div>    
            </div>    
        </div>
        <!-- SETTINGS BOX --> 
        <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="600">
            <div class="modal-dialog modal-lg" style="width:80%">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body">
                        <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                        <span>
                            &nbsp;&nbsp;Loading... </span>
                    </div>
                </div>
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
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 text-right ">  
                            <button type="button" class="btn btn-orange" id="btnIndexchartFilter">
                                <span class="ladda-label">Apply</span>
                            </button>

                        </div>
                    </div>
                </form>
                </div>    
            </div>    
        </div>
        <!-- SETTINGS BOX -->
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/highcharts/highstock.js"></script>
<!--        <script src="< ?php echo $asset_url;?>assets/global/highcharts/highcharts.js"></script>-->
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <?php if ($acces_management->allow_print) { ?>
            <script src="<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            var firsttimeload = 1;
            var Company_id = '<?php echo $company_id ?>';
            var StartDate = "<?php echo $start_date; ?>";
            var EndDate = "<?php echo $end_date; ?>";
            var base_url = "<?php echo $base_url; ?>";
            var step = 0;
            //            var owl =$("#region_data");
        </script>
        <script src="<?php echo $asset_url; ?>assets/customjs/assessment_dashboard.js"></script>
        <script>
                jQuery(document).ready(function () {
                    $(".select2_rpt").select2({
                        placeholder: 'Please Select',
                        width: '100%'
                    });
                    $("#region_id").select2({
                        placeholder: 'Trainee Region',
                        width: '100%',
                        allowClear: true
                    });
                     $("#store_id").select2({
                        placeholder: 'Store wise/Vertical wise',
                        width: '100%',
                        allowClear: true
                    });
                getWeek();
                $('#btnIndexchartFilter').click(function(event) {
                    event.preventDefault();
                    assessment_index_refresh();                    
                    $('#responsive-modal').modal('toggle');
                });
                $('#opt_weekly').change(function(event) {
                    event.preventDefault();
                    $("#rpt_period").val('weekly');
                    assessment_index_refresh();                    
                });
                $('#opt_monthly').change(function(event) {
                    event.preventDefault();
                    $("#rpt_period").val('monthly');
                    assessment_index_refresh();                    
                });
                $('#opt_yearly').change(function(event) {
                    event.preventDefault();
                    $("#rpt_period").val('yearly');
                    assessment_index_refresh();                    
                });
                    if (!jQuery().daterangepicker) {
                        return;
                    }
                   dashboard_refresh();   
                    if (jQuery().datepicker) {
                        $('.date-picker').datepicker({
                            rtl: App.isRTL(),
                            orientation: "left",
                            autoclose: true,
                            format: 'dd-mm-yyyy'
                        });
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
                    }, function (start, end, label) {
                        if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                            $('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                        }

                    });
                    if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                        $('#dashboard-report-range span').html(moment().subtract(3, 'months').startOf('month').format('MMMM D, YYYY') + ' - ' + moment().endOf('month').format('MMMM D, YYYY'));
                    }
                    $('#dashboard-report-range').show();
                    $('#dashboard-report-range').on('apply.daterangepicker', function (ev, picker) {
                        //console.log(ev.chosenLabel);
                        //console.log(picker.chosenLabel);
                        $('#date_lable').text(picker.chosenLabel);
                        StartDate = picker.startDate.format('DD-MM-YYYY');
                        EndDate = picker.endDate.format('DD-MM-YYYY');
                        //getDatewiseAssessment();
                        //getDatewiseRegion();
                        dashboard_refresh();
                    });

                });
        </script>
    </body>
</html>