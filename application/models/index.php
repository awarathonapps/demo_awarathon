<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css"
        rel="stylesheet" type="text/css" />
    <!--<link rel="stylesheet" type="text/css" href="< ?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />-->
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css"
        rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css"
        rel="stylesheet" type="text/css" />
    <style>
    .arrow-row {
        width: 100%;
        height: auto;
    }

    .tr-background {
        background: #ffffff !important;
    }

    .wksh-td {
        color: #000000 !important;
        vertical-align: top !important;
    }

    .whsh-icon {
        float: right;
        position: absolute;
        top: 10px;
        right: 15px;
        color: #cccccc;
    }

    .potrait-title-mar {
        margin-left: -9px;
        margin-right: -9px;
    }

    .dashboard-stat {
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

    .dashboard-stat .display .number small {
        font-size: 12px;
        color: #777777;
        font-weight: 600;
        text-transform: uppercase;
        width: 100%;
    }

    .font-orange-sharp {
        color: #f1592a !important;
        margin: 0px !important;
        padding: 5px !important;
    }

    .tokenize-sample {
        width: 100%;
        height: auto
    }

    .theme-panel>.theme-options>.theme-option>span {
        width: 115px;
    }

    .no-padding {
        padding: 0px !important;
    }

    .page-content-white .page-title {
        margin: 20px 0;
        font-size: 22px;
        font-weight: 300 !important;
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
                    <form id="FilterFrm" name="FilterFrm" method="post">
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <a href="<?php echo base_url() . 'reports'; ?>">Reports</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Role - Play Rep Dashboard</span>
                                </li>
                            </ul>
                            <div class="col-md-1 page-breadcrumb"></div>
                            <div class="page-toolbar">
                                <a href="<?php echo base_url() . 'reports'; ?>"
                                    class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                                <!-- </div>
                            <div class="page-toolbar"> -->
                                <div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm"
                                    data-container="body" data-placement="bottom"
                                    data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </div>
                            </div>
                        </div>
                        <!-- PAGE BAR -->

                        <div class="clearfix margin-top-10"></div>
                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled "
                                                    data-toggle="collapse" data-parent="#accordion3"
                                                    href="#collapse_3_2">
                                                    Filter Data </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse ">
                                            <div class="panel-body">
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Report by&nbsp;<span
                                                                    class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="report_by" name="report_by"
                                                                    class="form-control input-sm select2_rpt"
                                                                    placeholder="Please select" style="width: 100%">
                                                                    <option value="0">Assessment</option>
                                                                    <option value="1">Parameter</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Report Type&nbsp;<span
                                                                    class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="report_type" name="report_type"
                                                                    class="form-control input-sm select2_rpt"
                                                                    placeholder="Please Select">
                                                                    <option value=""></option>
                                                                    <?php foreach ($report_type as $rt) { ?>
                                                                    <option value="<?= $rt->id; ?>"
                                                                        <?php echo ($rt->default_selected ? 'selected' : ''); ?>>
                                                                        <?php echo $rt->description; ?></option>

                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3"
                                                                style="padding:0px;">Trainee Name&nbsp;<span
                                                                    class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id"
                                                                    class="form-control input-sm select2_rpt"
                                                                    placeholder="Please select" style="width: 100%">
                                                                    <option value="">Select Trainee</option>
                                                                    <?php
                                                                        if (isset($trainee_data)) {
                                                                            foreach ($trainee_data as $Tn) {
                                                                                ?>
                                                                    <option value="<?= $Tn->user_id; ?>">
                                                                        <?php echo $Tn->traineename; ?></option>
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
                                                    <div class="col-md-10">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="col-md-offset-6 col-md-4 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm"
                                                                onclick="dashboard_refresh()">Search</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Quick
                                                Statistics</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="clearfix"></div>

                                        <!-- STAT LIFE TIME ROW -->
                                        <div class="row" style="margin-top: 5px;">
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="total_assessment"
                                                                    data-value="0">0</span>
                                                            </h3>
                                                            <small>Total Assessment</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="question_answer"
                                                                    data-value="0">0</span>
                                                            </h3>
                                                            <small>Total Question Answered</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="average_accuracy"
                                                                    data-value="0">0</span>
                                                            </h3>
                                                            <small>Average Accuracy</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 5px;">
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="highest_accuracy"
                                                                    data-value="0">0</span>
                                                            </h3>
                                                            <small>Highest Accuracy</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="lowest_accuracy"
                                                                    data-value="0">0</span>
                                                            </h3>
                                                            <small>Lowest Accuracy</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="dashboard-stat">
                                                    <div class="display">
                                                        <div class="number">
                                                            <h3 class="font-orange-sharp">
                                                                <span data-counter="counterup" id="total_time"
                                                                    data-value="--">--</span>
                                                            </h3>
                                                            <small>Time Taken</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
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
                                                </div>-->
                                        </div>
                                        <div class="row" style="margin-top: 5px;">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="portlet light bordered"
                                                    style="padding: 12px 20px 10px !important;">
                                                    <div class="portlet-title potrait-title-mar">
                                                        <div class="caption">
                                                            <i class="icon-bar-chart font-dark hide"></i>
                                                            <span
                                                                class="caption-subject font-dark bold uppercase">Strength</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body" style="padding: 0px !important">
                                                        <div class="table-scrollable table-scrollable-borderless">
                                                            <table class="table table-hover table-light"
                                                                id="asmnt-top-five">
                                                                <thead>
                                                                    <tr class="uppercase">
                                                                        <th class="wksh-td th-desc" width="80%">
                                                                            PARAMETER </th>
                                                                        <th class="wksh-td" width="20%"> Accuracy </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"
                                                style="padding: 0px 5px 5px 5px;">
                                                <div class="portlet light bordered"
                                                    style="padding: 12px 20px 10px !important;">
                                                    <div class="portlet-title potrait-title-mar">
                                                        <div class="caption">
                                                            <i class="icon-bar-chart font-dark hide"></i>
                                                            <span
                                                                class="caption-subject font-dark bold uppercase">Improvements</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body" style="padding: 0px !important">
                                                        <div class="table-scrollable table-scrollable-borderless">
                                                            <table class="table table-hover table-light"
                                                                id="asmnt-bottom-five">
                                                                <thead>
                                                                    <tr class="uppercase">
                                                                        <th class="wksh-td th-desc" width="80%">
                                                                            PARAMETER </th>
                                                                        <th class="wksh-td" width="20%"> Accuracy </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--                                                <div class="col-md-4" style="padding: 0px 5px 5px 5px;" id="region_performance">

                                                </div>-->
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr />

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
                                            <span
                                                class="caption-subject font-dark bold uppercase th-desc">Parameter</span>
                                        </div>
                                        <div class="actions">
                                            <div class="btn-group btn-group-devided" data-toggle="buttons">
                                                <!-- <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm">
                                                        <input type="radio" name="rpt_period_option" class="toggle" id="opt_weekly" value="weekly">Weekly</a>
                                                    <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm ">
                                                        <input type="radio" name="rpt_period_option" class="toggle" id="opt_monthly" value="monthly">Monthly</a> -->
                                                <a href="#"
                                                    class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                                    <input type="radio" name="rpt_period_option" class="toggle"
                                                        id="opt_yearly" value="yearly">Yearly</a>
                                                <a data-toggle="modal" class="btn btn-circle btn-icon-only btn-default"
                                                    href="#responsive-modal" style="padding: 3px 0px !important;">
                                                    <i class="icon-settings"></i>
                                                </a>
                                                <input type="hidden" id="rpt_period" name="rpt_period" value="yearly" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body bordered">
                                        <div class="row margin-bottom-15" id="assessment_index"
                                            style="padding: 0px !important"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr />
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase ">Parameter Level
                                                Analysis</span>
                                        </div>
                                        <div class="col-md-4 pull-right">
                                            <div class="form-group">
                                                <label class="col-md-4" style="font-size: 15px;">Assessment</label>
                                                <div class="col-md-8">
                                                    <select id="parameter_id" name="parameter_id"
                                                        class="form-control input-sm select2"
                                                        placeholder="Select Parameter" style="width: 100%"
                                                        onchange="parameter_index_refresh();">
                                                        <option value="">All</option>
                                                        <?php foreach ($parameter_data as $para) { ?>
                                                        <option value="<?= $para->assessment_id; ?>">
                                                            <?php echo $para->assessment; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="actions">

                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row margin-bottom-15" id="parameter_index"
                                            style="padding: 0px !important"></div>
                                    </div>
                                </div>
                            </div>
                            <!--                                <div class="col-lg-12 col-xs-12 col-sm-12">
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
                                </div>-->
                        </div>
                        <!-- RUDRA PATEL 16/11/2023 -->
                        <div class="row" id="responsive-modal-user">
                            <!-- INDEX CHART -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class='portlet light bordered'>
                                    <div class="caption">
                                        <span class="caption-subject font-dark bold"
                                            style="font-family:'Catamaran'; position: absolute; font-size:18px; top: 14px; left: 28px;">User
                                            score
                                            <a data-title="User score">
                                                <i class="icon-info font-black sub-title"></i>
                                            </a>
                                        </span>
                                        <div class="btn-group btn-group-devided header-right pull-right"
                                            data-toggle="buttons"
                                            style="margin-bottom: 0px; margin-top: 0px; margin-right: 5px;">
                                            <a data-toggle="modal"
                                                class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active"
                                                href="#user_understanding_modal">
                                                Filter
                                            </a>
                                        </div>
                                        <div style="width:99%;padding-top: 53px;font-size: 12px;" class="clearfix">
                                        </div>
                                        <form id="frmUsers" name="frmUsers" method="post" action="">
                                            <input type="hidden" id="iscustom" name="iscustom">
                                            <div class="portlet-body">
                                                <table
                                                    class="table table-bordered table-hover table-checkable order-column no-footer dataTable"
                                                    id="user_understanding_table" style="border: none;">
                                                    <thead>
                                                        <tr>
                                                            <th>Your<br>score</th>
                                                            <th>Top<br>performer</th>
                                                            <th>Bottom<br>performer</th>
                                                            <th>Ranks</th>
                                                            <th>Badge</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- RUDRA PATEL 16/11/2023 -->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- SETTINGS BOX -->
    <div id="filter-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog"
        aria-hidden="true" tabindex="-1" data-width="200">
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
                                    <select id="assessment_id" name="assessment_id[]"
                                        class="form-control input-sm select2" style="width: 100%" multiple="">
                                        <?php foreach ($assessment_data as $val) { ?>
                                        <option value="<?= $val->assessment_id; ?>"><?php echo $val->assessment; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 text-right ">
                            <button type="button" class="btn btn-orange" id="btnIndexFilter"
                                onclick="dashboard_region_refresh();">
                                <span class="ladda-label">Apply</span>
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User_understanding Modal start Here -->
    <div id="user_understanding_modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static"
        role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                                    <label>Assessment
                                    </label>
                                    <select id="amt_id" name="amt_id[]" class="form-control input-sm select2  select"
                                        placeholder="Please select" multiple=''>
                                        <?php
                                        if (isset($assessment)) {
                                            foreach ($assessment as $adata) {
                                        ?>
                                        <option value="<?php echo  $adata->assessment_id; ?>">
                                            <?php echo $adata->assessment . ' '; ?></option>
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
                                    <label>Trainee<p
                                            style="color: red;position: absolute;top: -24px;left: 57px;font-size: 21px;">
                                            * </p><span
                                            style="position: absolute;top: 2px;left: 67px;color: red;font-size: 8px;">
                                            (Mandatory)</span>
                                    </label>
                                    <select id="trainee_id" name="trainee_id" class="form-control input-sm"
                                        placeholder="Please select" style="width: 100%">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group last">
                                    <label>Select Time</label>
                                    <input class="form-control input-sm" id="user_wise_understanding_picker" value=""
                                        name="user_wise_understanding_picker" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 text-right ">
                            <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                            <button type="button" class="btn btn-orange" onclick="user_wise_understanding(2)">
                                <span class="ladda-label">Apply</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User_understanding_modal End Here -->

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
    <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog"
        aria-hidden="true" tabindex="-1" data-width="200">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="frmModalForm2" name="frmModalForm2" onsubmit="return false;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Filter</h4>
                    </div>
                    <div class="modal-body">
                        <div id='dsk' style="display: none">&nbsp;</div>
                        <!-- <div class="row">
                            <div class="col-md-11">    
                                <div class="form-group last">
                                    <label>Month</label>
                                    <select id="month" name="month" class="form-control input-sm select2" placeholder="Please select" onchange="getWeek()">
                                        < ?php foreach (range(1, 12) as $month):
                                            $monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
                                            $fdate = date("F", strtotime(date('Y') . "-$monthPadding-01"));
                                            echo '<option value="' . $monthPadding . '" ' . ($monthPadding == date('m') ? 'selected' : '') . '>' . $fdate . '</option>';
                                        endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="row">
                            <div class="col-md-11">    
                                <div class="form-group last">
                                    <label>Week</label>
                                    <select id="week" name="week" class="form-control input-sm select2" placeholder="Please select">

                                    </select>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group last">
                                    <label>Year</label>
                                    <select id="year" name="year" class="form-control input-sm select2"
                                        placeholder="Please select">
                                        <option value="<?php echo date('Y') ?>"><?php echo date('Y') ?></option>
                                        <option value="<?php echo '2020' ?>"><?php echo '2020' ?></option>
                                        <option value="<?php echo '2019' ?>"><?php echo '2019' ?></option>
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
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.waypoints.min.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.counterup.min.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/highcharts/highstock.js"></script>
    <!--        <script src="< ?php echo $asset_url;?>assets/global/highcharts/highcharts.js"></script>-->
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript">
    </script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js"
        type="text/javascript"></script>
    <?php if ($role==1 || $role==2 || (isset($acces_management->allow_print) && $acces_management->allow_print)) { ?>
    <script src="<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js"></script>
    <?php } ?>
    <script>
    var firsttimeload = 1;
    var Company_id = '<?php echo $company_id ?>';
    var StartDate = "<?php echo $start_date; ?>";
    var EndDate = "<?php echo $end_date; ?>";
    var base_url = "<?php echo $base_url; ?>";
    var quarter = moment().quarter();
    var year = moment().year(); // for getting year 
    var step = 0;
    //            var owl =$("#region_data");
    </script>
    <script src="<?php echo $asset_url; ?>assets/customjs/role_play_rep_dashboard.js"></script>
    <script>
    user_wise_understanding(1); // Rudra Patel 20/11/2023
    get_all_trainee();

    $('#amt_id').change(function() {
        get_all_trainee();
    });

    //function get_all_trainee created by Patel Rudra 
    function get_all_trainee() {
        var assessmentid = $('#amt_id').val();
        if (Company_id == "") {
            return false;
        }

        $.ajax({
            type: "POST",
            data: {
                company_id: Company_id,
                assessmentid: assessmentid
            },

            url: base_url + "role_play_rep_dashboard/get_all_trainee",
            beforeSend: function() {
                customBlockUI();
            },

            success: function(msg) {
                if (msg != '') {
                    var Oresult = jQuery.parseJSON(msg);
                    $('#trainee_id').empty();
                    $('#trainee_id').append(Oresult['trainee_set']);
                }
                customunBlockUI();
            }
        });
    }
    //function get_all_trainee created by Patel Rudra 

    //for select in input box  
    $("#trainee_id").select2({
        dropdownParent: $('#user_understanding_modal'),
        placeholder: 'Please select',
        width: '100%',
        allowClear: true,
    });
    jQuery(document).ready(function() {
        $(".select2_rpt").select2({
            placeholder: 'Please Select',
            width: '100%'
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
        //dashboard_refresh();   
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
        }
        var year = moment().year();
        $('#dashboard-report-range').daterangepicker({
            "ranges": {
                'Current Year': [moment().year(year).startOf('year'), moment()],
                'Last 7 Days': [moment().subtract('days', 7), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'Last 60 Days': [moment().subtract('days', 59), moment()],
                'Last 90 Days': [moment().subtract('days', 89), moment()],
                'Last 365 Days': [moment().subtract('days', 365), moment()]
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
            "startDate": moment().subtract('days', 365),
            "endDate": moment(),
            opens: (App.isRTL() ? 'right' : 'left'),
        }, function(start, end, label) {
            if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                $('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end
                    .format('MMMM D, YYYY'));

            }

        });
        if ($('#dashboard-report-range').attr('data-display-range') != '0') {
            var thisYear = (new Date()).getFullYear();
            var start = new Date("1/1/" + thisYear);
            // $('#dashboard-report-range span').html(moment(start.valueOf()).startOf('month').format('MMMM D, YYYY') + ' - ' + moment().endOf('month').format('MMMM D, YYYY'));
            $('#dashboard-report-range span').html(moment().subtract('days', 365).format('MMMM D, YYYY') +
                ' - ' + moment().format('MMMM D, YYYY'));
        }
        $('#dashboard-report-range').show();
        $('#dashboard-report-range').on('apply.daterangepicker', function(ev, picker) {
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
    //For date picking in filter model of User understanding created by Rudra patel 20/11/2023
    $('#user_wise_understanding_picker').daterangepicker({
        "ranges": {
            'Current Year': [moment().year(year).startOf('year'), moment()],
            'Last 7 Days': [moment().subtract('days', 7), moment()],
            'Last 30 Days': [moment().subtract('days', 29), moment()],
            'Last 60 Days': [moment().subtract('days', 59), moment()],
            'Last 90 Days': [moment().subtract('days', 89), moment()],
            'Last 365 Days': [moment().subtract('days', 365), moment()]
        },
        "autoApply": true,
        "mirrorOnCollision": true,
        "applyOnMenuSelect": true,
        "autoFitCalendars": true,
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
            // "firstDay": 1
        },
        "startDate": moment().subtract('month', 1).format("DD/MM/YYYY"),
        "endDate": moment().format("DD/MM/YYYY"),
        "drops": "down",
        "opens": "right",
        //   opens: (App.isRTL() ? 'right' : 'left'),
    }, function(start, end, label) {
        sessionStorage.setItem("IsCustom", label);
    });
    if ($('#user_wise_understanding_picker').attr('data-display-range') != '0') {
        var thisYear = (new Date()).getFullYear();
        var thisMonth = (new Date()).getMonth() + 1;
        var start = new Date(thisMonth + "/1/" + thisYear);


    }
    $('#user_wise_understanding_picker').on('apply.daterangepicker', function(ev, picker) {
        $('#date_lable').text(picker.chosenLabel);
        StartDate = picker.startDate.format('DD-MM-YYYY');
        EndDate = picker.endDate.format('DD-MM-YYYY');
        let IsCustom = sessionStorage.getItem("IsCustom");
        //console.log(IsCustom);
        $('#iscustom').val(IsCustom);
    });
    //End of date picking in filter model of User understanding created by Rudra patel 20/11/2023

    //User score table datatable created by Rudra Patel 20/11/2023
    function user_wise_understanding(type, IsCustom = '') {

        var assessment_id = $("#amt_id").val();
        var user_id = $("#trainee_id").val();
        if (type == 2 && user_id == '') {
            ShowAlret("Please select Trainee ID .!!", 'error');
            return false;
        }
        var table = $('#user_understanding_table');
        table.dataTable({
            destroy: true,
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending",
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered1 from _MAX_ total records)",
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous": "Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            "bStateSave": true,
            "lengthMenu": [
                [5, 10, 15, 20, -1],
                [5, 10, 15, 20, "All"]
            ],
            "pageLength": 5,
            "paging": true,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{
                    'className': 'dt-head-left dt-body-left',
                    'width': '50px',
                    'orderable': false,
                    'searchable': true,
                    'targets': [0]
                },
                {
                    'className': 'dt-head-left dt-body-left',
                    'width': '100px',
                    'orderable': false,
                    'searchable': true,
                    'targets': [1]
                },
                {
                    'className': 'dt-head-left dt-body-left',
                    'width': '100px',
                    'orderable': false,
                    'searchable': true,
                    'targets': [2]
                },
                {
                    'className': 'dt-head-left dt-body-left',
                    'width': '100px',
                    'orderable': false,
                    'searchable': true,
                    'targets': [3]
                },
                {
                    'className': 'dt-head-left dt-body-left',
                    'width': '80px',
                    'orderable': false,
                    'searchable': true,
                    'targets': [4]
                },
            ],
            "order": [
                [0, "desc"]
            ],
            "processing": true,
            // "serverSide": true,
            "serverSide": false,
            "sAjaxSource": base_url + "role_play_rep_dashboard/user_wise_understanding",
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    name: 'assessment_id',
                    value: $('#amt_id').val(),
                });
                aoData.push({
                    name: 'trainee_id',
                    value: $('#trainee_id').val(),
                });
                aoData.push({
                    name: 'StartDate',
                    value: StartDate,
                });
                aoData.push({
                    name: 'EndDate',
                    value: EndDate,
                });
                aoData.push({
                    name: 'IsCustom',
                    value: $('#iscustom').val()
                });
                $.getJSON(sSource, aoData, function(json) {

                    fnCallback(json);
                  
                    if (json.trainee_id != '') {
                        $('#trainee_id').val(json.trainee_id);
                        $('#trainee_id').trigger('change');
                    }
                   
                    if ($('#amt_id').val() != "") {
                        $('#user_understanding_modal').modal('hide');
                    }

                });
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                return nRow;
            },
            "fnFooterCallback": function(nRow, aData) {},
            "initComplete": function(settings, json) {
                $('thead > tr> th:nth-child(1 )').css({
                    'min-width': '100px',
                    'max-width': '100px'
                });
            }
        });
    }
    //End of User score table datatable
    </script>
</body>

</html>