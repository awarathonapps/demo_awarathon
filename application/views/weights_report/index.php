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
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        
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
/*            .table_style tbody tr td{
                background-color : #E0F5FE;                
                border: 1px;
                border-bottom: 2px solid #f7d9f6;
                color: #8896a0;
                vertical-align: middle;                
            }
            .tablediv{                              
                border: 1px solid;
                color: #8896a0; 
                background-color : #d4d1d1; 
            }*/
            .divstyle {               
                border: 1px solid;
            }
            .divstyle tr {
                background-color: #ffffff;
            }            
            .divstyle  thead tr th{
                color: #000000 !important;
            }
            .divstyle  tbody tr td{
                color: #000000 !important;
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
                                    <span>Feedback Report</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Weights</span>                                    
                                </li>                                
                            </ul>                            
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">                                                                                              
                                <div class="portlet light bordered">                                    
                                    <div class="portlet-body">                                        
                                            <div class="tabbable-line tabbable-full-width">
                                                <ul class="nav nav-tabs" id="tabs">
                                                    <li class="active">
                                                        <a href="#tab_dashboard" data-toggle="tab">Dashboard</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_workshop" data-toggle="tab">Workshop</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_ind_trainee" data-toggle="tab">Individual Trainee</a>
                                                    </li>                                                            
                                                    <li>
                                                        <a href="#tab_comparison" data-toggle="tab">Comparison</a>
                                                    </li>                                                    
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_dashboard">                                                                                                
                                                        <?php if ($company_id == "") { ?>
                                                        <div class="row">
                                                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                <div class="panel-group accordion" id="accordion3">
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading">
                                                                        <h4 class="panel-title">
                                                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                                               Filter Data </a>
                                                                        </h4>
                                                                        </div>
                                                                        <div id="collapse_3_2" class="panel-collapse collapse">
                                                                            <div class="panel-body" >
                                                                                <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">
                                                                                <div class="row margin-bottom-10">                                                                                
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label col-md-3">Company&nbsp;</label>
                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                            <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getCompanyWtypeData()">
                                                                                                <option value="">All Company</option>
                                                                                                <?php
                                                                                                    foreach ($CompanyData as $cmp) {?>
                                                                                                    <option value="<?=$cmp->id;?>"><?php echo $cmp->company_name; ?></option>
                                                                                                <?php }?>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>                                                                                
                                                                                </div>
                                                                                <div class="clearfix margin-top-20"></div>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                                                            <button type="button" id="btnSearch" class="btn blue-hoki btn-sm" onclick="dashboard_refresh()">Search</button>
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
                                                    <?php }?>
                                            <div class="row">                            
                                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                <div class="portlet-title potrait-title-mar">
                                                    <div class="caption">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        <span class="caption-subject font-dark bold uppercase">Statistics</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">                                                                                                                        
                                                    <div class="clearfix"></div>                                        
                                                    <div class="row">                        
                                                        <div class="col-lg-6 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                            <div class="dashboard-stat">
                                                                <div class="display">
                                                                    <div class="number">
                                                                        <h3 class="font-orange-sharp">
                                                                            <span data-counter="counterup" id="workshop_attended_counter" data-value="0">0</span>
                                                                        </h3>
                                                                        <small>Workshop Attended</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                       
                                                        <div class="col-lg-6 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                            <div class="dashboard-stat">
                                                                <div class="display">
                                                                    <div class="number">
                                                                        <h3 class="font-orange-sharp">
                                                                            <span data-counter="counterup" id="no_of_trainee_participated_counter" data-value="0">0</span>
                                                                        </h3>
                                                                        <small>No. Of trainee Participated</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                               
                                                    </div>                                                    
                                                    <div class="clearfix"></div>                                                    
                                                    <div class="row" style="margin-top: 5px;">                                                        
                                                        <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                            <div class="dashboard-stat">
                                                                <div class="display">
                                                                    <div class="number">
                                                                        <h3 class="font-orange-sharp">
                                                                            <span data-counter="counterup" id="best_workshop_counter" data-value="">------</span>
                                                                        </h3>
                                                                        <small>Best Workshop</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                       
                                                        <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                            <div class="dashboard-stat">
                                                                <div class="display">
                                                                    <div class="number">
                                                                        <h3 class="font-orange-sharp">
                                                                            <span data-counter="counterup" id="average_score_counter" data-value="0">0</span>
                                                                        </h3>
                                                                        <small>Average Score</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                        
                                                        <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                            <div class="dashboard-stat">
                                                                <div class="display">
                                                                    <div class="number">
                                                                        <h3 class="font-orange-sharp">
                                                                            <span data-counter="counterup" id= "worst_workshop_counter" data-value="">------</span>
                                                                        </h3>
                                                                        <small>Worst Workshop</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                                        
                                                    <div class="row" style="margin-top: 5px;">
                                                        <div class="col-lg-6 col-xs-12 col-sm-12">
                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                        <div class="portlet-title potrait-title-mar">
                                                            <div class="caption">
                                                                <i class="icon-bar-chart font-dark hide"></i>
                                                                <span class="caption-subject font-dark bold uppercase">Top 5 Workshop</span>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body" style="padding: 0px !important"> 
                                                        <div class="table-scrollable table-scrollable-borderless">
                                                            <table class="table table-hover table-light" id="wksh-top-five">
                                                                <thead>
                                                                    <tr class="uppercase">
                                                                        <th class="wksh-td" width="80%"> Workshop Name </th>
                                                                        <th class="wksh-td" width="20%"> Overall Score </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>                                       
                                                        </div>
                                                    </div>
                                                    </div>                
                                                    <div class="col-lg-6 col-xs-12 col-sm-12">
                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                        <div class="portlet-title potrait-title-mar">
                                                            <div class="caption">
                                                            <i class="icon-bar-chart font-dark hide"></i>
                                                            <span class="caption-subject font-dark bold uppercase">Bottom 5 Workshop</span>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body" style="padding: 0px !important"> 
                                                        <div class="table-scrollable table-scrollable-borderless">
                                                            <table class="table table-hover table-light" id="wksh-bottom-five">
                                                                <thead>
                                                                    <tr class="uppercase">
                                                                        <th class="wksh-td" width="80%"> Workshop Name </th>
                                                                        <th class="wksh-td" width="20%"> Overall Score </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                        </div>
                                                        </div>
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
                                    <div class="portlet-body" style="padding: 0px !important" id="feedback_dashboard_index">                                    
                                    </div>
                                </div>                               
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
                                                <div class="row">    
                                                    <div class="col-md-11">    
                                                        <div class="form-group last">
                                                            <label>Workshop Type</label>
                                                            <select id="wtype_id" name="wtype_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                <option value="">All Type</option>
																<?php
                                                                if (isset($WtypeResult)) {
                                                                    foreach ($WtypeResult as $Type) {
                                                                        ?>
                                                                        <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
                                                                        <?php
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
                            </div>                                                            
                            </div>    
                            <div class="tab-pane " id="tab_workshop">
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <?php if ($company_id == "") { ?>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wtab_company_id" name="wtab_company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
                                                                    <option value="">All Company</option>
                                                                    <?php 
                                                                        foreach ($CompanyData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                  <?php } ?>
                                                <div class="row margin-bottom-10">                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wtab_wtype_id" name="wtab_wtype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getTypeWiseWorkshop();">
                                                                    <option value="0">All Type</option>
                                                                <?php
                                                                if (isset($WtypeResult)) {
                                                                    foreach ($WtypeResult as $Type) {
                                                                        ?>
                                                                        <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-type</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wtab_workshop_subtype" name="wtab_workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getSubTypeWiseWorkshop();">
                                                                    <option value="">All Sub-type</option>

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
                                                                <select id="wtab_region_id" name="wtab_region_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getRegionWiseWorkshop();">
                                                                    <option value="0">All Region</option>
                                                                <?php
                                                                if (isset($RegionResult)) {
                                                                    foreach ($RegionResult as $region) {
                                                                        ?>
                                                                        <option value="<?= $region->id; ?>"><?php echo $region->region_name; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-Region</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wtab_subregion_id" name="wtab_subregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getSubTypeWiseWorkshop();">
                                                                    <option value="">All Sub-region</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wtab_workshop_id" name="wtab_workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Workshop</option>
                                                                    <?php
                                                                    if (isset($WorkshopResult)) {
                                                                        foreach ($WorkshopResult as $Type) {?>
                                                                            <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>  
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="WeightWorkshopTab_datatable()">Search</button>
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="portlet-body">                            
                                        <table class="table  table-bordered table-checkable order-column" id="WdataTable">                                                                    
                                             <thead>
                                                <tr>                                            
                                                    <th id="name_head">
                                                        Workshop
                                                    </th>
                                                    <th id="second_name_head">
                                                        Score %
                                                    </th>
                                                    <th>
                                                       No. Of Trainee
                                                    </th>                                            
                                                </tr>
                                             </thead>
                                        <tbody>     
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                            <div class="tab-pane " id="tab_ind_trainee">
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <?php if ($company_id == "") { ?>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="indtab_company_id" name="indtab_company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getIndCompanywiseData();">
                                                                    <option value="">All Company</option>
                                                                    <?php 
                                                                        foreach ($CompanyData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                                <div class="row margin-bottom-10">                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_wtype_id" name="ind_wtype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getIndTypeWiseWorkshop();">
                                                                    <option value="0">All Type</option>
                                                                <?php
                                                                if (isset($WtypeResult)) {
                                                                    foreach ($WtypeResult as $Type) {
                                                                        ?>
                                                                        <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-type</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_workshop_subtype" name="ind_workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getIndSubTypeWiseWorkshop()">
                                                                    <option value="">All Sub-type</option>

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
                                                                <select id="ind_region_id" name="ind_region_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getIndRegionWiseWorkshop();">
                                                                    <option value="0">All Region</option>
                                                                <?php
                                                                if (isset($RegionResult)) {
                                                                    foreach ($RegionResult as $region) {
                                                                        ?>
                                                                        <option value="<?= $region->id; ?>"><?php echo $region->region_name; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-Region</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_subregion_id" name="ind_subregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getIndSubTypeWiseWorkshop()">
                                                                    <option value="">All Sub-region</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_workshop_id" name="ind_workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getIndWorkshopWiseTrainee();">
                                                                    <option value="">All Workshop</option>
                                                                    <?php
                                                                    if (isset($WorkshopResult)) {
                                                                        foreach ($WorkshopResult as $Type) {?>
                                                                            <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
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
                                                            <label class="control-label col-md-3">Trainee Region&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_tregion_id" name="ind_tregion_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getIndWorkshopWiseTrainee();">
                                                                    <option value="0">All Trainee-region</option>
                                                                    <?php
                                                                    if (isset($TraineeRegionData)) {
                                                                        foreach ($TraineeRegionData as $TR) {
                                                                            ?>
                                                                            <option value="<?= $TR->id; ?>"><?php echo $TR->region_name; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                                <div class="row margin-bottom-10">    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_trainee_id" name="ind_trainee_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%">
                                                                    <option value="0">All Trainee</option>
                                                                    <?php
                                                                    if (isset($TraineeResult)) {
                                                                        foreach ($TraineeResult as $Trainee) {?>
                                                                            <option value="<?= $Trainee->user_id; ?>"><?php echo $Trainee->traineename; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>  
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="WeightIndTab_datatable()">Search</button>
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="portlet-body">                            
                                        <table class="table  table-bordered table-checkable order-column" id="InddataTable">                                                                    
                                             <thead>
                                                <tr>                                            
                                                    <th id="name_head">
                                                        Trainee
                                                    </th>
                                                    <th id="second_name_head">
                                                        Score 
                                                    </th>                                                                                               
                                                </tr>
                                             </thead>
                                        <tbody>     
                                        </tbody>
                                        </table>
                                    </div>
                            </div>
                            <div class="tab-pane " id="tab_comparison">
                                    <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                        <div class="panel-body" >
                                            <form id="ComparisonFilterFrm" name="ComparisonFilterFrm" method="post">
                                                <?php if ($company_id == "") { ?>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_company_id" name="cmptab_company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getCmpCompanywiseData();">
                                                                    <option value="">All Company</option>
                                                                    <?php 
                                                                        foreach ($CompanyData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                 <?php } ?>
                                                <div class="row margin-bottom-10">                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_wtype_id" name="cmptab_wtype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getCmpTypeWiseWorkshop();">
                                                                    <option value="0">All Type</option>
                                                                <?php
                                                                if (isset($WtypeResult)) {
                                                                    foreach ($WtypeResult as $Type) {
                                                                        ?>
                                                                        <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-type</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_workshop_subtype" name="cmptab_workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getCmpSubTypeWiseWorkshop()">
                                                                    <option value="">All Sub-type</option>

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
                                                                <select id="cmptab_region_id" name="cmptab_region_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getCmpRegionWiseWorkshop();">
                                                                    <option value="0">All Region</option>
                                                                <?php
                                                                if (isset($RegionResult)) {
                                                                    foreach ($RegionResult as $region) {
                                                                        ?>
                                                                        <option value="<?= $region->id; ?>"><?php echo $region->region_name; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-Region</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_subregion_id" name="cmptab_subregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getCmpSubTypeWiseWorkshop()">
                                                                    <option value="">All Sub-region</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_workshop_id" name="cmptab_workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getCmpWorkshopWiseTrainee();">
                                                                    <option value="">All Workshop</option>
                                                                    <?php
                                                                    if (isset($WorkshopResult)) {
                                                                        foreach ($WorkshopResult as $Type) {?>
                                                                            <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
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
                                                            <label class="control-label col-md-3">Trainee Region&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_tregion_id" name="cmptab_tregion_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getCmpWorkshopWiseTrainee();">
                                                                    <option value="0">All Trainee-region</option>
                                                                    <?php
                                                                    if (isset($TraineeRegionData)) {
                                                                        foreach ($TraineeRegionData as $TR) {
                                                                            ?>
                                                                            <option value="<?= $TR->id; ?>"><?php echo $TR->region_name; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_trainee_id" name="cmptab_trainee_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%">
                                                                    <option value="0">All Trainee</option>
                                                                    <?php
                                                                    if (isset($TraineeResult)) {
                                                                        foreach ($TraineeResult as $Trainee) {?>
                                                                            <option value="<?= $Trainee->user_id; ?>"><?php echo $Trainee->traineename; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>  
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="WeightCmpTab_data()">Add Set</button>
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                <div id="ComparisonChart" class="row mt-10" ></div>
                            </div>                                                        
                            </div>    
                            </div>                                                                                            
                        </div>
                    </div>
                </div>
            </div>
                        
            </div>
            </div>                
            </div>            
        </div>
        <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <img src="<?php echo base_url(); ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                        <span>
                            &nbsp;&nbsp;Loading... </span>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/highcharts.src.js"></script>
         <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            var firsttimeload=1;
            var table=$('#WdataTable');
            var indtable = $('#InddataTable');
            var cmptable = $('#CmpdataTable');
            var RowCount = 1;
            $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            jQuery(document).ready(function () {    
                <?php if ($company_id != "") { ?>
                    dashboard_refresh();
                <?php } ?>
                                             
                getWeek();
//                $('#btnSearch').click(function (event) {
//                    event.preventDefault();                    
//                    dashboard_refresh();
//                });
                $('#btnIndexFilter').click(function (event) {
                    event.preventDefault();
                    firsttimeload=0;
                    feedback_weightindex_refresh();
                    $('#responsive-modal').modal('toggle');
                });
                $('#opt_weekly').change(function (event) {
                    event.preventDefault();
                    $("#rpt_period").val('weekly');
                    feedback_weightindex_refresh();
                });
                $('#opt_monthly').change(function (event) {
                    event.preventDefault();
                    $("#rpt_period").val('monthly');
                    feedback_weightindex_refresh();
                });
                $('#opt_yearly').change(function (event) {
                    event.preventDefault();
                    $("#rpt_period").val('yearly');
                    feedback_weightindex_refresh();
                });                                                                                    
            });
            
            function dashboard_refresh(){
                var bestWorkshop = '';
                var worstWorkshop = '';
               if ($('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
                
                $.ajax({
                    type: "POST",
                    data:{company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>weights_report/getdashboardData",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (data) {
                        if (data != '') {
                            var json                        = jQuery.parseJSON(data);
                            
                            var WrkattendedParticipated     = json['WrkattendedParticipated'];
                            var avgScore                    = json['avgScore'];
                                
                            bestWorkshop                    = json['bestWorkshop'];                                                        
                            if(json['bestWorkshop'] == json['worstWorkshop']){
                                worstWorkshop = '----';
                            }else{
                                worstWorkshop = json['worstWorkshop'];
                            }
                            
                            var topFiveWorkshop             = json['feedback_top_five_table'];                            
                            var bottomFiveWorkshop          = json['feedback_bottom_five_table'];
                            
                            $('#workshop_attended_counter').attr('data-value',WrkattendedParticipated['workshop_attended']);
                            $('#workshop_attended_counter').counterUp();

                            $('#no_of_trainee_participated_counter').attr('data-value',WrkattendedParticipated['no_of_participated']);
                            $('#no_of_trainee_participated_counter').counterUp();

                            $('#best_workshop_counter').attr('data-value',bestWorkshop);
                            $('#best_workshop_counter').counterUp();

                            $('#average_score_counter').attr('data-value',avgScore['avg_score']+'%');
                            $('#average_score_counter').counterUp();  

                            $('#worst_workshop_counter').attr('data-value',worstWorkshop);
                            $('#worst_workshop_counter').counterUp();                                                                                                
                            
                            if (topFiveWorkshop != '') {
                                $('#wksh-top-five tbody').empty();
                                $('#wksh-top-five tbody').append(topFiveWorkshop);
                            }
                            if (bottomFiveWorkshop != '') {
                                $('#wksh-bottom-five tbody').empty();
                                $('#wksh-bottom-five tbody').append(bottomFiveWorkshop);
                            }
                            feedback_weightindex_refresh();
                            customunBlockUI();      
                        }
                    }
                });
            }
            function feedback_weightindex_refresh(){
                if ($('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),
                           rpt_period:$('#rpt_period').val(),
                           wtype_id:$('#wtype_id').val(),month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),graphtype_id :$('#graphtype_id').val()},
                    async: false,
                    url: "<?php echo $base_url; ?>weights_report/load_weightsReportIndex/"+firsttimeload,
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (response) {
                        if (response != '') {
                            var json = jQuery.parseJSON(response);
                            $("#feedback_dashboard_index").html(json['index_graph']);
                            $("#histogram").html(json['histogram']);
                        }
                        customunBlockUI();
                    },
                });
            }
            function getWeek(){
                $.ajax({
                    type: "POST",
                    data: {year: $('#year').val(),month: $('#month').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>weights_report/ajax_getWeeks",
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
            function getCompanyWtypeData(){
                if($('#company_id').val() ==''){                    
                    $('#wtype_id').empty();                    
                    return false;
                }                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {  
                            var Oresult = jQuery.parseJSON(msg);
                            $('#wtype_id').empty();
                            $('#wtype_id').append(Oresult['WTypeData']);                            
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getCompanywiseData(){
                if($('#wtab_company_id').val() ==''){                    
                    $('#wtab_wtype_id').empty();
                    $('#wtab_workshop_id').empty();
                    $('#wtab_region_id').empty();
                    $('#wtab_workshop_subtype').empty();
                    $('#wtab_subregion_id').empty();
                    return false;
                }
                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#wtab_company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);    
                            $('#wtab_wtype_id').empty();
                            $('#wtab_wtype_id').append(Oresult['WTypeData']);                                                                                                                                          
                            $('#wtab_workshop_id').empty();
                            $('#wtab_workshop_id').append(Oresult['FeedbackWorkshopData']);
                            $('#wtab_region_id').empty();
                            $('#wtab_region_id').append(Oresult['RegionData']); 
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getIndCompanywiseData(){
                if($('#indtab_company_id').val() ==''){                    
                    $('#ind_wtype_id').empty();
                    $('#ind_workshop_id').empty();
                    $('#ind_trainee_id').empty();
                    $('#ind_workshop_subtype').empty();
                    $('#ind_region_id').empty();
                    $('#ind_subregion_id').empty();
                    $('#ind_tregion_id').empty();
                    return false;
                }

                $.ajax({
                    type: "POST",
                    data: {company_id: $('#indtab_company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                              
                            $('#ind_wtype_id').empty();
                            $('#ind_wtype_id').append(Oresult['WTypeData']);
                            $('#ind_workshop_id').empty();
                            $('#ind_workshop_id').append(Oresult['FeedbackWorkshopData']);
                            $('#ind_trainee_id').empty();
                            $('#ind_trainee_id').append(Oresult['TraineeData']);
                            $('#ind_region_id').empty();
                            $('#ind_region_id').append(Oresult['RegionData']);
                            $('#ind_tregion_id').empty();
                            $('#ind_tregion_id').append(Oresult['TraineeRegionData'])
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getCmpCompanywiseData(){
                if($('#cmptab_company_id').val() ==''){
                    $('#cmptab_workshop_subtype').empty();
                    $('#cmptab_wtype_id').empty();
                    $('#cmptab_workshop_id').empty();
                    $('#cmptab_trainee_id').empty();
                    $('#cmptab_region_id').empty();
                    $('#cmptab_subregion_id').empty();
                    $('#cmptab_tregion_id').empty();
                    return false;
                }

                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);        
                            $('#cmptab_wtype_id').empty();
                            $('#cmptab_wtype_id').append(Oresult['WTypeData']);
                            $('#cmptab_workshop_id').empty();
                            $('#cmptab_workshop_id').append(Oresult['FeedbackWorkshopData']);                            
                            $('#cmptab_region_id').empty();
                            $('#cmptab_region_id').append(Oresult['RegionData']);
                            $('#cmptab_tregion_id').empty();
                            $('#cmptab_tregion_id').append(Oresult['TraineeRegionData'])
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getTypeWiseWorkshop(){
                if($('#wtab_wtype_id').val() ==''){                                                            
                    $('#wtab_workshop_subtype').empty();                    
                    return false;
                }                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#wtab_company_id').val(),workshoptype_id: $('#wtab_wtype_id').val(),region_id: $('#wtab_region_id').val(),
                            subregion_id:$('#wtab_subregion_id').val(),workshopsubtype_id:$('#wtab_workshop_subtype').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#wtab_workshop_subtype').empty();
                            $('#wtab_workshop_subtype').append(Oresult['WorkshopSubtypeData']);
                            $('#wtab_workshop_id').empty();
                            $('#wtab_workshop_id').append(Oresult['FeedbackWorkshopData']);                            
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getRegionWiseWorkshop(){                
                if($('#wtab_region_id').val() ==''){                                                                                 
                    $('#wtab_subregion_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#wtab_company_id').val(),workshoptype_id: $('#wtab_wtype_id').val(),region_id: $('#wtab_region_id').val(),
                           subregion_id:$('#wtab_subregion_id').val(),workshopsubtype_id:$('#wtab_workshop_subtype').val() },                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                            
                            $('#wtab_workshop_id').empty();
                            $('#wtab_workshop_id').append(Oresult['FeedbackWorkshopData']);
                            $('#wtab_subregion_id').empty();
                            $('#wtab_subregion_id').append(Oresult['WorkshopSubregionData']);
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getSubTypeWiseWorkshop(){
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#wtab_company_id').val(),workshoptype_id: $('#wtab_wtype_id').val(),region_id: $('#wtab_region_id').val(),
                           subregion_id:$('#wtab_subregion_id').val(),workshopsubtype_id:$('#wtab_workshop_subtype').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                            
                            $('#wtab_workshop_id').empty();
                            $('#wtab_workshop_id').append(Oresult['FeedbackWorkshopData']);                            
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getIndTypeWiseWorkshop(){
                if($('#ind_wtype_id').val() ==''){                                                            
                    $('#ind_workshop_subtype').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#indtab_company_id').val(),workshoptype_id: $('#ind_wtype_id').val(),
                           region_id: $('#ind_region_id').val(),subregion_id:$('#ind_subregion_id').val(),
                           workshopsubtype_id:$('#ind_workshop_subtype').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                                                        
                            $('#ind_workshop_id').empty();
                            $('#ind_workshop_id').append(Oresult['FeedbackWorkshopData']);
                            $('#ind_workshop_subtype').empty();
                            $('#ind_workshop_subtype').append(Oresult['WorkshopSubtypeData']);
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getIndSubTypeWiseWorkshop(){                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#indtab_company_id').val(),workshoptype_id: $('#ind_wtype_id').val(),
                           region_id: $('#ind_region_id').val(),subregion_id:$('#ind_subregion_id').val(),workshopsubtype_id:$('#ind_workshop_subtype').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                                                        
                            $('#ind_workshop_id').empty();
                            $('#ind_workshop_id').append(Oresult['FeedbackWorkshopData']);                                                        
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getIndRegionWiseWorkshop(){                
                if($('#wtab_region_id').val() ==''){                                                                                 
                    $('#wtab_subregion_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#indtab_company_id').val(),workshoptype_id: $('#ind_wtype_id').val(),region_id: $('#ind_region_id').val(),
                           subregion_id:$('#ind_subregion_id').val(),workshopsubtype_id:$('#ind_workshop_subtype').val() },                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                            
                            $('#ind_workshop_id').empty();
                            $('#ind_workshop_id').append(Oresult['FeedbackWorkshopData']);
                            $('#ind_subregion_id').empty();
                            $('#ind_subregion_id').append(Oresult['WorkshopSubregionData']);
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getCmpTypeWiseWorkshop(){
                if($('#cmptab_wtype_id').val() ==''){                                                            
                    $('#cmptab_workshop_subtype').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val(),workshoptype_id: $('#cmptab_wtype_id').val(),
                           region_id: $('#cmptab_region_id').val(),subregion_id:$('#cmptab_subregion_id').val(),
                           workshopsubtype_id:$('#cmptab_workshop_subtype').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#cmptab_workshop_id').empty();
                            $('#cmptab_workshop_id').append(Oresult['FeedbackWorkshopData']);
                            $('#cmptab_workshop_subtype').empty();
                            $('#cmptab_workshop_subtype').append(Oresult['WorkshopSubtypeData']);                                                                               
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getCmpSubTypeWiseWorkshop(){                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val(),workshoptype_id: $('#cmptab_wtype_id').val(),
                           region_id: $('#cmptab_region_id').val(),subregion_id:$('#cmptab_subregion_id').val(),
                           workshopsubtype_id:$('#cmptab_workshop_subtype').val() },                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#cmptab_workshop_id').empty();
                            $('#cmptab_workshop_id').append(Oresult['FeedbackWorkshopData']);                                                                                                              
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getCmpRegionWiseWorkshop(){
                if($('#cmptab_wtype_id').val() ==''){   
                    $('#cmptab_subregion_id').empty();                    
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val(),workshoptype_id: $('#cmptab_wtype_id').val(),
                           region_id: $('#cmptab_region_id').val(),subregion_id:$('#cmptab_subregion_id').val(),
                           workshopsubtype_id:$('#cmptab_workshop_subtype').val() },                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#cmptab_workshop_id').empty();
                            $('#cmptab_workshop_id').append(Oresult['FeedbackWorkshopData']);                            
                            $('#cmptab_subregion_id').empty();
                            $('#cmptab_subregion_id').append(Oresult['WorkshopSubregionData']);                                                                                    
                        }
                    customunBlockUI();    
                    }
                });
            }            
            function getIndWorkshopWiseTrainee(){                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#indtab_company_id').val(),workshop_id: $('#ind_workshop_id').val(),tregion_id:$('#ind_tregion_id').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_tregionwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                                                                                   
                            $('#ind_trainee_id').empty();
                            $('#ind_trainee_id').append(Oresult['AllSelectionTrainee']);                             
                        }
                    customunBlockUI();    
                    }
                });
            }
            function getCmpWorkshopWiseTrainee(){
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val(),workshop_id: $('#cmptab_workshop_id').val(),tregion_id:$('#cmptab_tregion_id').val()},                    
                    //async: false,
                    url: "<?php echo $base_url;?>common_controller/ajax_tregionwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#cmptab_trainee_id').empty();
                            $('#cmptab_trainee_id').append(Oresult['AllSelectionTrainee']);                                                                                    
                        }
                    customunBlockUI();    
                    }
                });
            }
            function WeightWorkshopTab_datatable(){
                if ($('#wtab_company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
                table.dataTable({
                    destroy: true,
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No Workshop data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ records",
                        "infoEmpty": "No records found",
                        "infoFiltered": "(filtered 1 from _MAX_ total records)",
                        "lengthMenu": "Show _MENU_",
                        "search": "Search:",
                        "zeroRecords": "No matching records found",
                        "paginate": {
                            "previous":"Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First"
                        }
                    },
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,            
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'width': '200px','orderable': false,'searchable': true,'targets': [0]}, 
                        {'width': '80px','orderable': false,'searchable': false,'targets': [1]}, 
                        {'width': '80px','orderable': false,'searchable': false,'targets': [2]},                         
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'weights_report/getWeightWorkshopTableData'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'wtab_company_id', value: $('#wtab_company_id').val()});
                        aoData.push({name: 'wtab_wtype_id', value: $('#wtab_wtype_id').val()});
                        aoData.push({name: 'wtab_workshop_id', value: $('#wtab_workshop_id').val()});
                        aoData.push({name: 'wtab_workshop_subtype', value: $('#wtab_workshop_subtype').val()});
                        aoData.push({name: 'wtab_region_id', value: $('#wtab_region_id').val()});
                        aoData.push({name: 'wtab_subregion_id', value: $('#wtab_subregion_id').val()});
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json);
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    }
                });
            }
            function WeightIndTab_datatable(){
                if ($('#indtab_company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
//                if ($('#ind_wtype_id').val() == "") {
//                    ShowAlret("Please select Workshop Type.!!", 'error');
//                    return false;
//                }
//                if ($('#ind_workshop_id').val() == "") {
//                    ShowAlret("Please select Workshop.!!", 'error');
//                    return false;
//                }
                indtable.dataTable({
                    destroy: true,
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No Workshop data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ records",
                        "infoEmpty": "No records found",
                        "infoFiltered": "(filtered 1 from _MAX_ total records)",
                        "lengthMenu": "Show _MENU_",
                        "search": "Search:",
                        "zeroRecords": "No matching records found",
                        "paginate": {
                            "previous":"Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First"
                        }
                    },
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,            
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'width': '','orderable': false,'searchable': true,'targets': [0]}, 
                        {'width': '200px','orderable': false,'searchable': false,'targets': [1]},                                                  
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'weights_report/getWeightIndTableData'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'indtab_company_id', value: $('#indtab_company_id').val()});
                        aoData.push({name: 'ind_wtype_id', value: $('#ind_wtype_id').val()});
                        aoData.push({name: 'ind_workshop_id', value: $('#ind_workshop_id').val()});
                        aoData.push({name: 'ind_trainee_id', value: $('#ind_trainee_id').val()});
                        aoData.push({name: 'ind_workshop_subtype', value: $('#ind_workshop_subtype').val()});
                        aoData.push({name: 'ind_region_id', value: $('#ind_region_id').val()});
                        aoData.push({name: 'ind_subregion_id', value: $('#ind_subregion_id').val()});
                        aoData.push({name: 'ind_tregion_id', value: $('#ind_tregion_id').val()});
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json);
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    }
                });
            }
            function WeightCmpTab_data(){
                if ($('#cmptab_company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
//                if ($('#cmptab_wtype_id').val() == "") {
//                    ShowAlret("Please select Workshop Type.!!", 'error');
//                    return false;
//                }
                if ($('#cmptab_workshop_id').val() == "") {
                    ShowAlret("Please select Workshop.!!", 'error');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val(),workshop_id: $('#cmptab_workshop_id').val(),wtype_id: $('#cmptab_wtype_id').val(),
                        RowCount:RowCount,trainee_id:$('#cmptab_trainee_id').val(),cmptab_workshop_subtype:$('#cmptab_workshop_subtype').val(),
                        cmptab_region_id:$('#cmptab_region_id').val(),cmptab_subregion_id:$('#cmptab_subregion_id').val(),cmptab_tregion_id:$('#cmptab_tregion_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>weights_report/ajax_ComparisonData",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var CompMSt = Oresult['CompTable'];     
                            if(Oresult['Error']!=''){                            
                                ShowAlret(Oresult['Error'], 'error');          
                            }else{                                                                
                                $('#ComparisonChart').append(CompMSt);
                                $('#datatr_'+RowCount).addClass('selectedBox');
                                RowCount++;
                            }

                        }
                    customunBlockUI();    
                    }
                });
            }
            function RemoveChart(Row_id){   
                $('#childdiv_'+Row_id).remove();                                   
            }
        </script>
    </body>
</html>