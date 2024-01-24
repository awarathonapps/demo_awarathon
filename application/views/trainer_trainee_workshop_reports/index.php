<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
$acces_management1 = $this->session->userdata('awarathon_session');
$ismasterAdmin = ($acces_management1['username'] == 'masteradmin') ? 1 : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--datattable CSS  Start-->
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!--datattable CSS  End-->
    <?php $this->load->view('inc/inc_htmlhead'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/highcharts/css/highcharts.css"" />
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
            .highcharts-negative{
                fill: #FF0000;
                stroke: #FF0000;
            }
            .scroll {
            border: 0;
            border-collapse: collapse;
        }

        .scroll tr {
            display: flex;
        }

        .scroll td {
            padding: 3px;
            flex: 1 auto;
            border: 1px solid #aaa;
            width: 1px;
            word-wrap: break;
        }

        .scroll thead tr:after {
            content: '';
            overflow-y: scroll;
            visibility: hidden;
            height: 0;
        }

        .scroll thead th {
            flex: 1 auto;
            display: block;
            border: 1px solid #000;
        }

        .scroll tbody {
            display: block;
            width: 100%;
            overflow-y: auto;
            height: 400px;
        }
        .trClickeble{
                cursor: pointer;
            }
        #index_Wwr_table thead tr th {
            vertical-align: baseline;
        }
    </style>
</head>
<body class=" page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
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
                                <span>Trainer Trainer & Workshop Reports</span>
                            </li>
                        </ul>
                        <div class="page-toolbar">
                            <a href="<?php echo base_url() . 'reports'; ?>" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                        </div>
                    </div>
                    <br>
                    <!-- tabs -->
                    <div class="tab-pane " id="tab_overview">
                        <!-- Main tabs -->
                        <ul class="nav nav-tabs" id="tabs">
                        <?php if ($role == 2 || (isset($acces_management_trainer->allow_access) && $acces_management_trainer->allow_access)){ ?>
                            <li class="active">
                                <a href="#trainer_reports_tab" data-toggle="tab">Trainer Reports</a>
                            </li>
                        <?php } if ($role == 2 || (isset($acces_management_trainee->allow_access) && $acces_management_trainee->allow_access)){ ?>
                            <li>
                                <a href="#trainee_reports_tab" data-toggle="tab">Trainee Reports</a>
                            </li>
                        <?php } if ($role == 2 || (isset($acces_management_workshop->allow_access) && $acces_management_workshop->allow_access)){ ?>
                            <li>
                                <a href="#workshop_reports_tab" data-toggle="tab">Workshop Reports</a>
                            </li>
                        <?php } ?>
                        </ul>
                        <!-- Main Tabs -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="trainer_reports_tab">
                                <div class="row mt-10">
                                    <div class="col-md-12">
                                        <div class="panel-group accordion" id="trainer_reports_tab">
                                            <div class="panel panel-default">
                                                <div id="collapse_3_4" class="panel-collapse ">
                                                    <div class="panel-body">
                                                        <!-- Content 1 -->
                                                        <div class="portlet-body">
                                                            <div class="tabbable-line tabbable-full-width">
                                                                <ul class="nav nav-tabs" id="tabs">
                                                                    <li class="active">
                                                                        <a href="#trainer_workshop" data-toggle="tab">Trainer Workshop</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainer_compartion" data-toggle="tab">Trainer Comparition</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainer_accuracy" data-toggle="tab">Trainer Accuracy</a>
                                                                    </li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <!-- Trainer Workshop Start -->
                                                                    <div class="tab-pane active" id="trainer_workshop">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <!-- trainer workshop Content start here  -->
                                                                                    <?php if ($trainer_id == '') { ?>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                                    <div class="panel panel-default">
                                                                                                        <div class="panel-heading">
                                                                                                            <h4 class="panel-title">
                                                                                                                <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_1_2">
                                                                                                                    Filter Report </a>
                                                                                                            </h4>
                                                                                                        </div>
                                                                                                        <div id="collapse_1_2" class="panel-collapse ">
                                                                                                            <div class="panel-body">
                                                                                                                <form id="frmFilterDashboard_tw" name="frmFilterDashboard_tw" method="post">

                                                                                                                    <div class="row margin-bottom-10">
                                                                                                                        <?php if ($Supcompany_id == "") { ?>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="form-group">
                                                                                                                                    <label class="control-label col-md-4">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                                    <div class="col-md-8" style="padding:0px;">
                                                                                                                                        <select id="company_id_trw" name="company_id_trw" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainer_trw()">
                                                                                                                                            <option value="">All Company</option>
                                                                                                                                            <?php
                                                                                                                                            foreach ($company_array as $cmp) { ?>
                                                                                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                                            <?php } ?>
                                                                                                                                        </select>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        <?php } ?>
                                                                                                                        <div class="col-md-6">
                                                                                                                            <div class="form-group">
                                                                                                                                <label class="control-label col-md-4">Trainer&nbsp;</label>
                                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                                    <select id="user_id_trw" name="user_id_trw" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                                                                                        <option value="0">All Trainer</option>
                                                                                                                                        <?php if (isset($TrainerResult)) {
                                                                                                                                            foreach ($TrainerResult as $trainer) { ?>
                                                                                                                                                <option value="<?= $trainer->userid; ?>"><?php echo $trainer->fullname; ?></option>
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
                                                                                                                                <label class="control-label col-md-4">Workshop Type&nbsp;</label>
                                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                                    <select id="workshop_type_id_trw" name="workshop_type_id_trw" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData_trw();">
                                                                                                                                        <option value="0">All Type</option>
                                                                                                                                        <?php
                                                                                                                                        if (isset($WtypeResult)) {
                                                                                                                                            foreach ($WtypeResult as $Type) { ?>
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
                                                                                                                                <label class="control-label col-md-4">Workshop Sub-type</label>
                                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                                    <select id="workshop_subtype_trw" name="workshop_subtype_trw" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                                                                                        <option value="">All Sub-type</option>

                                                                                                                                    </select>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="row margin-bottom-10">
                                                                                                                        <div class="col-md-6">
                                                                                                                            <div class="form-group">
                                                                                                                                <label class="control-label col-md-4"> Workshop Region &nbsp;</label>
                                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                                    <select id="wregion_id_trw" name="wregion_id_trw" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData_trw();">
                                                                                                                                        <option value="0">All Region</option>
                                                                                                                                        <?php
                                                                                                                                        if (isset($RegionData)) {
                                                                                                                                            foreach ($RegionData as $Rdata) { ?>
                                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                                <label class="control-label col-md-4">Workshop Sub-region &nbsp;</label>
                                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                                    <select id="wsubregion_id_trw" name="wsubregion_id_trw" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                                                                                        <option value="">Select Sub-region</option>

                                                                                                                                    </select>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                                    <div class="row">

                                                                                                                        <div class="col-md-12">
                                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh_trw(0)">Preview Report</button>
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
                                                                                                    <div class="caption col-lg-12 col-xs-8 col-sm-8">
                                                                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                                                                        <span class="caption-subject font-dark bold uppercase">Workshop Accuracy</span>

                                                                                                        <span style='float:right;font-size:13px;font-weight:bold;color:red;'>* NP - Not Played</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="portlet-body">
                                                                                                    <table class="table table-hover table-light" id="wksh-list-trw">
                                                                                                        <thead>
                                                                                                            <tr class="uppercase">
                                                                                                                <!--                                                        <th width="22%">WORKSHOP DATE</th>-->
                                                                                                                <th width="22%">ID</th>
                                                                                                                <th width="22%">WORKSHOP NAME</th>
                                                                                                                <th width="12%">NO. OF TOPIC</th>
                                                                                                                <th width="12%">AVERAGE C.E</th>
                                                                                                                <th width="12%">NO. OF TRAINEES</th>
                                                                                                                <th width="32%">REPORT OPTION</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                    <div class="table-scrollable table-scrollable-borderless" id="wksh-total-trw" style="font-size:14px;font-weight:bold;">&nbsp;</div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- STAT BOX -->

                                                                                        <!-- TOP 5 TABLE -->
                                                                                        <div class="col-lg-6 col-xs-6 col-sm-6">
                                                                                            <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                                <div class="portlet-title potrait-title-mar">
                                                                                                    <div class="caption">
                                                                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                                                                        <span class="caption-subject font-dark bold uppercase">Top 5 Participants</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="portlet-body" style="padding: 0px !important" id="top_five_panel_trw">
                                                                                                    <div id="top_five_loading" style="text-align: center;display: none;">
                                                                                                        <img src="<?php echo $asset_url; ?>assets/global/img/loading.gif" alt="loading" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- TOP 5 TABLE -->

                                                                                        <!-- BOTTOM 5 TABLE -->
                                                                                        <div class="col-lg-6 col-xs-6 col-sm-6">
                                                                                            <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                                <div class="portlet-title potrait-title-mar">
                                                                                                    <div class="caption">
                                                                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                                                                        <span class="caption-subject font-dark bold uppercase">Bottom 5 Participants</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="portlet-body" style="padding: 0px !important" id="bottom_five_panel_trw">
                                                                                                    <div id="bottom_five_loading" style="text-align: center;display: none;">
                                                                                                        <img src="<?php echo $asset_url; ?>assets/global/img/loading.gif" alt="loading" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- BOTTOM 5 TABLE -->


                                                                                        <!-- CHART MODAL -->
                                                                                        <div id="chart-modal-trw" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="800">
                                                                                            <div class="modal-dialog modal-lg">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                                                        <h5 class="modal-title" id="modal_title-trw"></h5>
                                                                                                    </div>
                                                                                                    <div class="modal-body" id="popupchart-trw">
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <div class="col-md-12 text-right ">
                                                                                                            <button type="button" class="btn btn-orange" class="close" data-dismiss="modal" aria-hidden="true">
                                                                                                                <span class="ladda-label">Close</span>
                                                                                                            </button>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- CHART MODAL -->
                                                                                    </div>
                                                                                    <!-- STAT FIRST ROW -->
                                                                                    <!-- Trainer workshop Content end here -->
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- trainer_workshop End -->











                                                                    <!-- --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------                          -->
                                                                    <!-- Trainer Comparition Start -->
                                                                    <div class="tab-pane" id="trainer_compartion">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <!-- tab 1 Content 2 -->
                                                                                <div class="row">
                                                                                    <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                        <div class="panel-group accordion" id="accordion3">
                                                                                            <div class="panel panel-default">
                                                                                                <div class="panel-heading">
                                                                                                    <h4 class="panel-title">
                                                                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_2_2">
                                                                                                            Filter Set </a>
                                                                                                    </h4>
                                                                                                </div>
                                                                                                <div id="collapse_2_2" class="panel-collapse ">
                                                                                                    <div class="panel-body">
                                                                                                        <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">
                                                                                                            <div class="row margin-bottom-10">
                                                                                                                <?php if ($company_id == "") { ?>
                                                                                                                    <div class="col-md-6">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label></label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="company_id_trainer" name="company_id_trainer" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainer();">
                                                                                                                                    <option value="">Please select</option>
                                                                                                                                    <?php foreach ($company_array as $cmp) { ?>
                                                                                                                                        <option value="<?php echo $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                                    <?php } ?>
                                                                                                                                </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                <?php } ?>
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Trainer&nbsp;<span class="required"> * </span></label></label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="user_id_trainer" name="user_id_trainer" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshopTrainer();">
                                                                                                                                <option value="0">All Trainer</option>
                                                                                                                                <?php
                                                                                                                                if (isset($TrainerResult)) {
                                                                                                                                    foreach ($TrainerResult as $trainer) {
                                                                                                                                ?>
                                                                                                                                        <option value="<?= $trainer->userid; ?>" <?php echo ($trainer->userid == $trainer_id ? 'selected' : ''); ?>><?php echo $trainer->fullname; ?></option>
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
                                                                                                                        <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="workshop_type_id_trainer" name="workshop_type_id_trainer" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshopTrainer();">
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
                                                                                                                            <select id="workshop_subtype_trainer" name="workshop_subtype_trainer" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseDataTrainer();">
                                                                                                                                <option value="">All Sub-type</option>

                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row margin-bottom-10">

                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="wregion_id_trainer" name="wregion_id_trainer" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshopTrainer();">
                                                                                                                                <option value="0">All Region</option>
                                                                                                                                <?php
                                                                                                                                if (isset($RegionData)) {
                                                                                                                                    foreach ($RegionData as $Rdata) {
                                                                                                                                ?>
                                                                                                                                        <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                        <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="wsubregion_id_trainer" name="wsubregion_id_trainer" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseDataTrainer();">
                                                                                                                                <option value="">Select Sub-region</option>

                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row margin-bottom-10">
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label></label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="workshop_id_trainer" name="workshop_id_trainer" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                                                                                <option value="">Please select</option>
                                                                                                                                <?php
                                                                                                                                if (isset($WorkshopResultSet)) {
                                                                                                                                    foreach ($WorkshopResultSet as $wd) {
                                                                                                                                ?>
                                                                                                                                        <option value="<?= $wd->workshop_id; ?>"><?php echo $wd->workshop_name; ?></option>
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
                                                                                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh_trainer_comperition()">Add Set</button>
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
                                                                                <!-- CHART MODAL -->
                                                                                <div class="row">
                                                                                    <!-- STAT BOX -->
                                                                                    <div class="col-sm-12">
                                                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                            <div class="portlet-title potrait-title-mar">
                                                                                                <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                                                                                    <i class="icon-bar-chart font-dark hide"></i>
                                                                                                    <span class="caption-subject font-dark bold uppercase">Workshop Set</span>
                                                                                                    <span style='float:right;font-size:13px;font-weight:bold;color:red;'>(Add workshop set from above filter set panel)</span>
                                                                                                </div>

                                                                                            </div>
                                                                                            <div class="portlet-body">
                                                                                                <div class="table-scrollable table-scrollable-borderless" style="height:200px;">
                                                                                                    <table class="table table-hover table-light" id="wksh-list-trainer-comperition">
                                                                                                        <thead style="display: block;">
                                                                                                            <tr class="uppercase">
                                                                                                                <th width="24%">WORKSHOP NAME</th>
                                                                                                                <th width="16%">TRAINER NAME</th>
                                                                                                                <th width="12%">PRE ACCURACY</th>
                                                                                                                <th width="12%">POST ACCURACY</th>
                                                                                                                <th width="12%">C.E</th>
                                                                                                                <th width="28%">ACTIONS</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody style="display: block;height: 165px;overflow-y: auto;overflow-x: hidden;">
                                                                                                            <tr>
                                                                                                                <td colspan="5">
                                                                                                                    Please select workshop set from above filter set panel.
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- STAT BOX -->
                                                                                </div>
                                                                                <!-- WORKSHOP COMPARISON  -->
                                                                                <div class="row" id="comparison-table"></div>
                                                                                <!-- WORKSHOP COMPARISON  -->
                                                                                <!-- tab 1 Content 2 -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Trainer Comparition End -->



                                                                    <!-- Trainer Accuracy Start -->
                                                                    <div class="tab-pane" id="trainer_accuracy">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <!-- tab 1 Content 3 -->
                                                                                <div class="row">
                                                                                    <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                        <div class="panel-group accordion" id="accordion3">
                                                                                            <div class="panel panel-default">
                                                                                                <div class="panel-heading">
                                                                                                    <h4 class="panel-title">
                                                                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_3_3">
                                                                                                            Filter Report </a>
                                                                                                    </h4>
                                                                                                </div>
                                                                                                <div id="collapse_3_3_3" class="panel-collapse">
                                                                                                    <div class="panel-body">
                                                                                                        <form id="frm_accuracy" name="frm_accuracy" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_workshop_ta' ?>">
                                                                                                            <div class="row margin-bottom-10">
                                                                                                                <?php if ($company_id == "") { ?>
                                                                                                                    <div class="col-md-6">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label></label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="company_id_trainer_accuracy" name="company_id_trainer_accuracy" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainerAccuracy();">
                                                                                                                                    <option value="">Please select</option>
                                                                                                                                    <?php
                                                                                                                                    foreach ($company_array as $cmp) { ?>
                                                                                                                                        <option value="<?php echo $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                                    <?php } ?>
                                                                                                                                </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                <?php } ?>
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Trainer&nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="user_id_trainer_accuracy" name="user_id_trainer_accuracy" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshopAccruacy();">
                                                                                                                                <?php
                                                                                                                                if (isset($Trainer_array)) {
                                                                                                                                    echo '<option value="0" >All Trainer</option>';
                                                                                                                                    foreach ($Trainer_array as $cmp) { ?>
                                                                                                                                        <option value="<?php echo $cmp->userid; ?>" <?php echo ($cmp->userid == $trainer_id ? 'selected' : ''); ?>><?php echo $cmp->fullname; ?></option>
                                                                                                                                <?php }
                                                                                                                                } ?>
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
                                                                                                                            <select id="workshop_type_id_trainer_accuracy" name="workshop_type_id_trainer_accuracy" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshopAccruacy();">
                                                                                                                                <?php
                                                                                                                                if (isset($wksh_type_array)) {
                                                                                                                                    echo '<option value="0" >All Type</option>';
                                                                                                                                    foreach ($wksh_type_array as $cmp) { ?>
                                                                                                                                        <option value="<?php echo $cmp->id; ?>"><?php echo $cmp->workshop_type; ?></option>
                                                                                                                                <?php }
                                                                                                                                } ?>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="workshop_subtype_trainer_accuracy" name="workshop_subtype_trainer_accuracy" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseDataAccuracy();">
                                                                                                                                <option value="">All Sub-type</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row margin-bottom-10">

                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="wregion_id_trainer_accuracy" name="wregion_id_trainer_accuracy" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshopAccruacy();">
                                                                                                                                <option value="0">All Region</option>
                                                                                                                                <?php
                                                                                                                                if (isset($RegionData)) {
                                                                                                                                    foreach ($RegionData as $Rdata) {
                                                                                                                                ?>
                                                                                                                                        <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                        <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="wsubregion_id_trainer_accuracy" name="wsubregion_id_trainer_accuracy" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseDataAccuracy();">
                                                                                                                                <option value="">Select Sub-region</option>

                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row margin-bottom-10">
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label></label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="workshop_id_trainer_accuracy" name="workshop_id_trainer_accuracy" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeAccuracy();">
                                                                                                                                <?php
                                                                                                                                if (isset($workshop_array)) {
                                                                                                                                    echo '<option value="0" >Please select</option>';
                                                                                                                                    foreach ($workshop_array as $cmp) { ?>
                                                                                                                                        <option value="<?php echo $cmp->workshop_id; ?>"><?php echo $cmp->workshop_name; ?></option>
                                                                                                                                <?php }
                                                                                                                                } ?>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Session&nbsp;<span class="required"> * </span></label></label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="workshop_session_trainer_accuracy" name="workshop_session_trainer_accuracy" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeAccuracy();">
                                                                                                                                <option value="PRE">PRE</option>
                                                                                                                                <option value="POST">POST</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                            <div class="clearfix margin-top-20"></div>
                                                                                                            <div class="row">
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Trainee Region&nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="trainee_region_id_trainer_accuracy" name="trainee_region_id_trainer_accuracy" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeAccuracy()">
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
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3">Trainee &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="trainee_id_accuracy" name="trainee_id_accuracy" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                                                                                <option value="0">All Trainee</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="clearfix margin-top-20"></div>
                                                                                                            <div class="row">
                                                                                                                <div class="col-md-12">
                                                                                                                    <div class="col-md-offset-9 col-md-2 text-right">
                                                                                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh_trainer_accuracy()">Preview Report</button>
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

                                                                                <!-- STAT FIRST ROW -->
                                                                                <div class="row">
                                                                                    <div class="clearfix"></div>
                                                                                    <!-- TOP 5 TRAINEE -->
                                                                                    <div class="col-lg-6 col-xs-12 col-sm-12">
                                                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                            <div class="portlet-title potrait-title-mar">
                                                                                                <div class="caption">
                                                                                                    <i class="icon-bar-chart font-dark hide"></i>
                                                                                                    <span class="caption-subject font-dark bold uppercase">Top 5 Trainee</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="portlet-body" style="padding: 0px !important">
                                                                                                <div class="table-scrollable table-scrollable-borderless">
                                                                                                    <table class="table table-hover table-light" id="trainee-top-five">
                                                                                                        <thead>
                                                                                                            <tr class="uppercase">
                                                                                                                <th class="wksh-td" width="80%"> TRAINEE NAME </th>
                                                                                                                <th class="wksh-td" width="20%"> OVERALL ACCURACY </th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody></tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- TOP 5 TRAINEE -->


                                                                                    <!-- BOTTOM 5 TRAINEE -->
                                                                                    <div class="col-lg-6 col-xs-12 col-sm-12">
                                                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                            <div class="portlet-title potrait-title-mar">
                                                                                                <div class="caption">
                                                                                                    <i class="icon-bar-chart font-dark hide"></i>
                                                                                                    <span class="caption-subject font-dark bold uppercase">Bottom 5 Trainee</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="portlet-body" style="padding: 0px !important">
                                                                                                <div class="table-scrollable table-scrollable-borderless">
                                                                                                    <table class="table table-hover table-light" id="trainee-bottom-five">
                                                                                                        <thead>
                                                                                                            <tr class="uppercase">
                                                                                                                <th class="wksh-td" width="80%"> TRAINEE NAME </th>
                                                                                                                <th class="wksh-td" width="20%"> OVERALL ACCURACY </th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody></tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- BOTTOM 5 TRAINEE -->

                                                                                    <div class="clearfix"></div>

                                                                                    <!-- STAT BOX -->
                                                                                    <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                            <div class="portlet-title potrait-title-mar">
                                                                                                <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                                                                                    <i class="icon-bar-chart font-dark hide"></i>
                                                                                                    <span class="caption-subject font-dark bold uppercase">TOPIC + SUB-TOPIC WISE</span>
                                                                                                </div>

                                                                                            </div>
                                                                                            <div class="portlet-body" id='topic-subtopic-chart'>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- STAT BOX -->

                                                                                    <!-- TRAINEE STATISTICS -->
                                                                                    <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                            <div class="portlet-title potrait-title-mar">
                                                                                                <div class="caption col-lg-8 col-xs-8 col-sm-8">
                                                                                                    <i class="icon-bar-chart font-dark hide"></i>
                                                                                                    <span class="caption-subject font-dark bold uppercase">TRAINEE STATISTICS</span>
                                                                                                </div>
                                                                                                <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                    <div class="actions">
                                                                                                        <div class="btn-group pull-right">
                                                                                                            <button type="button" onclick="exportConfirmTrainerAccuracy()" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                            &nbsp;&nbsp;
                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php } ?>
                                                                                            </div>
                                                                                            <div class="portlet-body">
                                                                                                <div class="table-scrollable table-scrollable-borderless">
                                                                                                    <table class="table table-hover table-light" id="wksh-list-trainer_accuracy">
                                                                                                        <thead>
                                                                                                            <tr class="uppercase">
                                                                                                                <th width="28%">TRAINEE NAME</th>
                                                                                                                <th width="12%">TOTAL PLAYED</th>
                                                                                                                <th width="12%">CORRECT</th>
                                                                                                                <th width="12%">WRONG</th>
                                                                                                                <th width="12%">RESULT</th>
                                                                                                                <th width="12%">RANK</th>
                                                                                                                <th width="12%">STATUS</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td colspan="7">
                                                                                                                    Please select workshop set from above filter set panel.
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- STAT BOX -->
                                                                                </div>
                                                                                <!-- Tab 1 Content 3 -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Trainee Accuracy End -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Content 1 -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tab 1 end here  -->


                            <!-- Tab 2 Start Here ---------------------------------------------------------------------------------->
                            <div class="tab-pane" id="trainee_reports_tab">
                                <div class="row mt-10">
                                    <div class="col-md-12">
                                        <div class="panel-group accordion" id="trainee_reports_tab">
                                            <div class="panel panel-default">
                                                <div id="collapse_3_4" class="panel-collapse ">
                                                    <div class="panel-body">
                                                        <!--Tab 2 Content start here -->
                                                        <div class="portlet-body">
                                                            <div class="tabbable-line tabbable-full-width">
                                                                <ul class="nav nav-tabs" id="tabs">
                                                                    <li class="active">
                                                                        <a href="#trainee_workshop" data-toggle="tab">Trainee Workshop</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainee_comparition" data-toggle="tab">Trainee Comparition</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainee_accuracy" data-toggle="tab">Trainee Accuracy</a>
                                                                    </li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane active" id="trainee_workshop">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <!-- Tab 2 Content 1 -->
                                                                                    <div class="row">
                                                                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                            <div class="panel-group accordion" id="accordion3">
                                                                                                <div class="panel panel-default">
                                                                                                    <div class="panel-heading">
                                                                                                        <h4 class="panel-title">
                                                                                                            <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                                                                                Filter Report </a>
                                                                                                        </h4>
                                                                                                    </div>
                                                                                                    <div id="collapse_3_2" class="panel-collapse ">
                                                                                                        <div class="panel-body">
                                                                                                            <form id="FilterFrm" name="FilterFrm" method="post">

                                                                                                                <div class="row margin-bottom-10">
                                                                                                                    <?php if ($company_id == "") { ?>
                                                                                                                        <div class="col-md-6">
                                                                                                                            <div class="form-group">
                                                                                                                                <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                                    <select id="company_id_tw" name="company_id_tw" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanyWorkshopType();">
                                                                                                                                        <option value="">All Company</option>
                                                                                                                                        <?php foreach ($company_array as $cmp) { ?>
                                                                                                                                            <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                                        <?php } ?>
                                                                                                                                    </select>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    <?php } ?>
                                                                                                                    <div class="row margin-top-10"></div>
                                                                                                                    <div class="col-md-6">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Trainee-Region &nbsp;</label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="trainee_region_id_tw" name="trainee_region_id_tw" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTRegionwiseData()">
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
                                                                                                                    <?php if ($login_type != 3) { ?>
                                                                                                                        <div class="col-md-6">
                                                                                                                            <div class="form-group">
                                                                                                                                <label class="control-label col-md-3">Trainee Name&nbsp;<span class="required"> * </span></label>
                                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                                    <select id="trainee_id_tw" name="trainee_id_tw" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                                        <option value="">Select Trainer</option>
                                                                                                                                        <?php
                                                                                                                                        if (isset($Trainee)) {
                                                                                                                                            foreach ($Trainee as $Type) {
                                                                                                                                        ?>
                                                                                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
                                                                                                                                        <?php
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                        ?>
                                                                                                                                    </select>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    <?php } ?>
                                                                                                                    <div class="row margin-bottom-10"></div>
                                                                                                                    <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-20' : ''); ?>">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="workshoptype_id_tw" name="workshoptype_id_tw" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData();">
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

                                                                                                                    <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-10' : ''); ?>">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="workshop_subtype_tw" name="workshop_subtype_tw" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                                    <option value="">All Sub-type</option>

                                                                                                                                </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-20' : ''); ?>">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="wregion_id_tw" name="wregion_id_tw" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData();">
                                                                                                                                    <option value="0">All Region</option>
                                                                                                                                    <?php
                                                                                                                                    if (isset($RegionData)) {
                                                                                                                                        foreach ($RegionData as $Rdata) { ?>
                                                                                                                                            <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
                                                                                                                                    <?php
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                    ?>
                                                                                                                                </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-10' : ''); ?>">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="wsubregion_id_tw" name="wsubregion_id_tw" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                                    <option value="">Select Sub-region</option>

                                                                                                                                </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <?php echo ($company_id != "" ? '<div class="clearfix margin-top-20"></div>' : ''); ?>
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="TableRefresh()">Search</button>
                                                                                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
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
                                                                                    <!-- STAT FIRST ROW -->
                                                                                    <div class="row">

                                                                                        <!-- STAT BOX -->
                                                                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                            <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                                <div class="portlet-title potrait-title-mar">
                                                                                                    <div class="caption col-lg-12 col-xs-8 col-sm-8">
                                                                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                                                                        <span class="caption-subject font-dark bold uppercase">Workshop Accuracy</span>

                                                                                                        <span style='float:right;font-size:13px;font-weight:bold;color:red;'>* NP - Not Played</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="portlet-body">
                                                                                                    <table class="table table-hover table-light" id="table_index_tw">
                                                                                                        <thead>
                                                                                                            <tr class="uppercase">
                                                                                                                <th>Workshop Date</th>
                                                                                                                <th>Workshop Name</th>
                                                                                                                <th>No. Of Topics</th>
                                                                                                                <th>AVG Post</th>
                                                                                                                <th>AVG Response Time</th>
                                                                                                                <th width="28%">Actions</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- STAT BOX -->
                                                                                        <!-- CHART MODAL -->
                                                                                        <div class="modal fade bs-modal-lg modal-scroll in" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="550">
                                                                                            <div class="modal-dialog modal-lg">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-body">
                                                                                                        <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                                                                                                        <span>
                                                                                                            &nbsp;&nbsp;Loading... </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- CHART MODAL -->
                                                                                    </div>
                                                                                    <!-- Tab 2 Content 1 -->
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Tab 2 content 2 -->
                                                                    <div class="tab-pane" id="trainee_comparition">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <!-- Tab 2 Conetent 2 Start Here  -->
                                                                                    <div class="col-lg-12 col-xs-12 col-sm-12">
                                                                                        <div class="panel-group accordion" id="accordion3">
                                                                                            <div class="panel panel-default">
                                                                                                <div class="panel-heading">
                                                                                                    <h4 class="panel-title">
                                                                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_2_2_2">
                                                                                                            Filter Set </a>
                                                                                                    </h4>
                                                                                                </div>
                                                                                                <div id="collapse_2_2_2" class="panel-collapse ">
                                                                                                    <div class="panel-body">
                                                                                                        <form id="FilterFrmTC" name="FilterFrmTC" method="post">
                                                                                                            <?php if ($Company_id == "") { ?>
                                                                                                                <div class="row margin-bottom-10">
                                                                                                                    <div class="col-md-6">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="company_id_tc" name="company_id_tc" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
                                                                                                                                    <option value="">All Company</option>
                                                                                                                                    <?php foreach ($CompanyData as $cmp) { ?>
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
                                                                                                                            <select id="workshoptype_id_tc" name="workshoptype_id_tc" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTypeWiseWorkshop();">
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
                                                                                                                            <select id="workshop_subtype_tc" name="workshop_subtype_tc" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseData();">
                                                                                                                                <option value="">All Sub-type</option>

                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                            <div class="row margin-bottom-10">
                                                                                                                <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                        <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="wregion_id_tc" name="wregion_id_tc" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTypeWiseWorkshop();">
                                                                                                                                <option value="0">All Region</option>
                                                                                                                                <?php
                                                                                                                                if (isset($RegionData)) {
                                                                                                                                    foreach ($RegionData as $Rdata) {
                                                                                                                                ?>
                                                                                                                                        <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                        <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="wsubregion_id_tc" name="wsubregion_id_tc" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseData();">
                                                                                                                                <option value="">Select Sub-region</option>

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
                                                                                                                            <select id="workshop_id_tc" name="workshop_id_tc" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTrainee();">
                                                                                                                                <option value="">Please Workshop</option>
                                                                                                                                <?php
                                                                                                                                if (isset($WorkshopResultSet)) {
                                                                                                                                    foreach ($WorkshopResultSet as $Type) {
                                                                                                                                ?>
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
                                                                                                                        <label class="control-label col-md-3">Trainee-Region &nbsp;</label>
                                                                                                                        <div class="col-md-9" style="padding:0px;">
                                                                                                                            <select id="trainee_region_id_tc" name="trainee_region_id_tc" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTRegionwiseData()">
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
                                                                                                                <?php if ($Trainee_id == "") { ?>
                                                                                                                    <div class="col-md-6">
                                                                                                                        <div class="form-group">
                                                                                                                            <label class="control-label col-md-3">Trainee&nbsp;</label>
                                                                                                                            <div class="col-md-9" style="padding:0px;">
                                                                                                                                <select id="trainee_id_tc" name="trainee_id_tc" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                                    <option value="">All Trainee</option>

                                                                                                                                </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                <?php } ?>
                                                                                                            </div>
                                                                                                            <div class="clearfix margin-top-20"></div>
                                                                                                            <div class="row">
                                                                                                                <div class="col-md-12">
                                                                                                                    <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="ShowChart()">Add Set</button>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </form>
                                                                                                    </div>

                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                                            <div class="portlet-title potrait-title-mar">
                                                                                                <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                                                                                    <i class="icon-bar-chart font-dark hide"></i>
                                                                                                    <span class="caption-subject font-dark bold uppercase">Workshop Set</span>
                                                                                                    <span style='float:right;font-size:13px;font-weight:bold;color:red;'>(Add workshop set from above filter set panel)</span>
                                                                                                </div>

                                                                                            </div>
                                                                                            <div class="portlet-body">
                                                                                                <div id="AppendChart" class="table-scrollable table-scrollable-borderless" style="max-height:200px;">
                                                                                                    <table class="table table-hover table-light" id="CEtable" width="100%">
                                                                                                        <thead style="display: block;">
                                                                                                            <tr class="uppercase">
                                                                                                                <th width="25%">WORKSHOP NAME</th>
                                                                                                                <th width="12%">PRE </th>
                                                                                                                <th width="12%">POST </th>
                                                                                                                <th width="12%">C.E</th>
                                                                                                                <th width="9%">ACTIONS</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody style="display: block;height: 165px;overflow-y: auto;overflow-x: hidden;">
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- STAT BOX -->
                                                                                    <div class="row" id="TraineeChart"></div>
                                                                                    <!-- Tab 2 Conetent 2 End Here  -->

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Trainee Report Start -->
                                                                    <div class="tab-pane" id="trainee_accuracy">
                                                                        <!-- Tab 2 Content 3 start Here -->
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="alert alert-danger display-hide" id="errordiv">
                                                                                    <button class="close" data-close="alert"></button>
                                                                                    You have some form errors. Please check below.
                                                                                    <br><span id="errorlog"></span>
                                                                                </div>
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">

                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_2_2_3">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>
                                                                                        <div id="collapse_2_2_3" class="panel-collapse">
                                                                                            <div class="panel-body">
                                                                                                <form id="FilterFrm_ta" name="FilterFrm_ta" method="post">
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <?php if ($company_id == "") { ?>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_id_ta" name="company_id_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData_ta();">
                                                                                                                            <option value="">All Company</option>
                                                                                                                            <?php foreach ($company_array as $cmp) { ?>
                                                                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                            <?php } ?>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                        <div class="row margin-top-10"></div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainee-Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="trainee_region_id_ta" name="trainee_region_id_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTRegionwiseData_ta()">
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
                                                                                                        <?php if ($this->mw_session['login_type'] != 3) { ?>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Trainee &nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="trainee_id_ta" name="trainee_id_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeWiseData_ta()">
                                                                                                                            <option value="">Please select</option>
                                                                                                                            <?php
                                                                                                                            if (isset($Trainee)) {
                                                                                                                                foreach ($Trainee as $Type) { ?>
                                                                                                                                    <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
                                                                                                                            <?php
                                                                                                                                }
                                                                                                                            }
                                                                                                                            ?>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Type</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshoptype_id_ta" name="workshoptype_id_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeWiseData_ta()">
                                                                                                                        <option value="0">All Type</option>
                                                                                                                        <?php
                                                                                                                        if (isset($WtypeResult)) {
                                                                                                                            foreach ($WtypeResult as $Type) { ?>
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
                                                                                                                    <select id="workshop_subtype_ta" name="workshop_subtype_ta" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseData_ta();">
                                                                                                                        <option value="">All Sub-type</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wregion_id_ta" name="wregion_id_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeWiseData_ta();">
                                                                                                                        <option value="0">All Region</option>
                                                                                                                        <?php
                                                                                                                        if (isset($RegionData)) {
                                                                                                                            foreach ($RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wsubregion_id_ta" name="wsubregion_id_ta" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getWSubTypewiseData_ta();">
                                                                                                                        <option value="">Select Sub-region</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_id_ta" name="workshop_id_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Workshop</option>
                                                                                                                        <?php
                                                                                                                        if (isset($WorkshopResultSet)) {
                                                                                                                            foreach ($WorkshopResultSet as $Type) { ?>
                                                                                                                                <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        } ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Session</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_session_ta" name="workshop_session_ta" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%">

                                                                                                                        <option value="PRE">PRE</option>
                                                                                                                        <option value="POST">POST</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="ShowChart_ta()">Show Report</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12" id="AppendChart_ta">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mt-20 margin-top-20">
                                                                            <div class="col-md-8" id="tablecontainer">
                                                                            </div>
                                                                        </div>
                                                                        <!-- Tab 2 Content 3 End Here -->
                                                                        <!-- Trainee Report End -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Content 2 End Here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- tab 2 end here -->


                            <!-- Tab 3 Start Here -->
                            <div class="tab-pane" id="workshop_reports_tab">
                                <div class="row mt-10">
                                    <div class="col-md-12">
                                        <div class="panel-group accordion" id="workshop_reports_tab">
                                            <div class="panel panel-default">
                                                <div id="collapse_3_4" class="panel-collapse ">
                                                    <div class="panel-body">
                                                        <!--Tab 3 Content start here -->

                                                        <div class="portlet-body">
                                                            <div class="tab-pane">
                                                                <ul class="nav nav-tabs" id="tabs" style="margin-right: -2px;">
                                                                    <li class="active">
                                                                        <a href="#trainee_played_result_tab" data-toggle="tab">Trainee played</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainee_wise_summary_tab" data-toggle="tab">Summary Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#traineetopic_wise_report_tab" data-toggle="tab">Topic + Questions</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainer_wise_summary_report_tab" data-toggle="tab">Summary Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#trainer_consolidated_report_tab" data-toggle="tab">Consolidated Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#workshop_wise_report_tab" data-toggle="tab">Workshop-wise</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#question_wise_report_tab" data-toggle="tab">Question Wise</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#imei_report_tab" data-toggle="tab">Device IMEI</a>
                                                                    </li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <!-- trainee_played_result Start here 10-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane active" id="trainee_played_result_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">

                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_Tpr">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>

                                                                                        <div id="collapse_Tpr" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Tpr_Frm" name="Filter_Tpr_Frm" method="post">
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <?php if ($Company_id == "") { ?>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Tpr_id" name="company_Tpr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tpr_CompanywiseData()">
                                                                                                                            <option value="">All Company</option>
                                                                                                                            <?php
                                                                                                                            foreach ($CompanyData as $cmp) { ?>
                                                                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                            <?php } ?>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshoptype_Tpr_id" name="workshoptype_Tpr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tpr_WTypewiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($WorkshopTypeData)) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($WorkshopTypeData as $WType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_type; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-Type&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshopsubtype_Tpr_id" name="workshopsubtype_Tpr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tpr_WSubTypewiseData()">
                                                                                                                        <option value="">Select Workshop Type</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="region_Tpr_id" name="region_Tpr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tpr_WTypewiseData()">
                                                                                                                        <?php
                                                                                                                        if (isset($RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="subregion_Tpr_id" name="subregion_Tpr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tpr_WSubRegionwiseData()">
                                                                                                                        <option value="">Select SubRegion</option>
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
                                                                                                                    <select id="workshop_Tpr_id" name="workshop_Tpr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tpr_WorkshopwiseData()">
                                                                                                                        <option value="">Select Workshop</option>
                                                                                                                        <?php
                                                                                                                        if (isset($WorkshopData)) {
                                                                                                                            foreach ($WorkshopData as $WType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Session&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="Tpr_sessions" name="Tpr_sessions" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Session</option>
                                                                                                                        <option value="0">PRE</option>
                                                                                                                        <option value="1">POST</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Topic&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="topic_Tpr_id" name="topic_Tpr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tpr_TopicwiseData()">
                                                                                                                        <option value="">Select Topic</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">SubTopic &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="subtopic_Tpr_id" name="subtopic_Tpr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Subtopic</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainer &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="trainer_Tpr_id" name="trainer_Tpr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tpr_TrainerwiseData()">
                                                                                                                        <?php
                                                                                                                        if (isset($TrainerData)) {
                                                                                                                            echo '<option value="0">All Trainer</option>';
                                                                                                                            foreach ($TrainerData as $TDype) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $TDype->userid; ?>"><?php echo $TDype->fullname; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="tregion_Tpr_id" name="tregion_Tpr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tpr_TrainerwiseData()">

                                                                                                                        <?php

                                                                                                                        if (isset($RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        }
                                                                                                                        ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainee &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="user_Tpr_id" name="user_Tpr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">

                                                                                                                        <?php
                                                                                                                        if (isset($TraineeData)) {
                                                                                                                            echo '<option value="">All Trainee</option>';
                                                                                                                            foreach ($TraineeData as $Type) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Designation &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="designation_Tpr_id" name="designation_Tpr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                                                                        <?php

                                                                                                                        if (isset($DesignationData)) {
                                                                                                                            echo '<option value="0">All Designation</option>';
                                                                                                                            foreach ($DesignationData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        }
                                                                                                                        ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Search By Result&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="result_Tpr_search" name="result_Tpr_search" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All</option>
                                                                                                                        <option value="1">Correct</option>
                                                                                                                        <option value="2">Wrong</option>
                                                                                                                        <option value="3">Time Out</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Tpr_Filter()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Tpr_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="tprReorts" name="tprReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Tpr_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Trainee Played Results Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Tpr_Confirm()" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Tpr_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Trainee ID</th>
                                                                                                        <th>Trainee Name</th>
                                                                                                        <th>Designation</th>
                                                                                                        <th>Workshop</th>
                                                                                                        <th>Workshop Type</th>
                                                                                                        <th>Workshop Sub-Type</th>
                                                                                                        <th>Workshop Region</th>
                                                                                                        <th>Sub-Region</th>
                                                                                                        <th>Session</th>
                                                                                                        <th>Question Set Name</th>
                                                                                                        <th>Trainer Name</th>
                                                                                                        <th>Trainee Region</th>
                                                                                                        <th>Topic Name</th>
                                                                                                        <th>Sub Topic Name</th>
                                                                                                        <th>Question Id & Question Title</th>
                                                                                                        <th>Correct Answer</th>
                                                                                                        <th>User Answered</th>
                                                                                                        <th>Start Date / Time</th>
                                                                                                        <th>End Date / Time</th>
                                                                                                        <th>Seconds</th>
                                                                                                        <th>Timer</th>
                                                                                                        <th>Correct/Wrong/Time Out</th>
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
                                                                    <!-- trainee_played_result_tab End -->

                                                                    <!-- trainee_wise_summary Start here 10-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="trainee_wise_summary_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="alert alert-danger display-hide" id="errordiv">
                                                                                    <button class="close" data-close="alert"></button>
                                                                                    You have some form errors. Please check below.
                                                                                    <br><span id="errorlog"></span>
                                                                                </div>
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">
                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_Tws">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>
                                                                                        <div id="collapse_Tws" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Tws_Frm" name="Filter_Tws_Frm" method="post">
                                                                                                    <?php if ($Company_id == "") { ?>
                                                                                                        <div class="row margin-bottom-10">
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Tws_id" name="company_Tws_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tws_CompanywiseData();">
                                                                                                                            <option value="">Please select</option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Type</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_Tws_type" name="workshop_Tws_type" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tws_WTypewiseData();">
                                                                                                                        <?php if (count((array)$Tws_WTypeData) > 0) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($Tws_WTypeData as $Rgn) { ?>
                                                                                                                                <option value="<?= $Rgn->id; ?>"><?php echo $Rgn->workshop_type; ?></option>
                                                                                                                        <?php }
                                                                                                                        } ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_Tws_subtype" name="workshop_Tws_subtype" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Sub-type</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wregion_Tws_id" name="wregion_Tws_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tws_WTypewiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Tws_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Tws_RegionData as $Rdata) { ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wsubregion_Tws_id" name="wsubregion_Tws_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Sub-region</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="region_Tws_id" name="region_Tws_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tws_TregionwiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Tws_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Tws_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Session&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_Tws_session" name="workshop_Tws_session" class="form-control input-sm select2" placeholder="Please select" style="width: 50%">
                                                                                                                        <option value="">All</option>
                                                                                                                        <option value="PRE">PRE</option>
                                                                                                                        <option value="POST">POST</option>
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
                                                                                                                    <select id="user_Tws_id" name="user_Tws_id" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Trainee</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Tws_TraineeData)) {
                                                                                                                            foreach ($Tws_TraineeData as $Type) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Designation &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="designation_Tws_id" name="designation_Tws_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                                                                        <?php
                                                                                                                        if (isset($Tws_DesignationData)) {
                                                                                                                            echo '<option value="0">All Designation</option>';
                                                                                                                            foreach ($Tws_DesignationData as $Rdata) { ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="range_Tws_id" name="range_Tws_id" class="form-control input-sm select2" placeholder="Please select" style="width: 50%">
                                                                                                                        <option value="">Select</option>
                                                                                                                        <option value="0-10">0-10%</option>
                                                                                                                        <option value="10-20">10-20%</option>
                                                                                                                        <option value="20-30">20-30%</option>
                                                                                                                        <option value="30-40">30-40%</option>
                                                                                                                        <option value="40-50">40-50%</option>
                                                                                                                        <option value="50-60">50-60%</option>
                                                                                                                        <option value="60-70">60-70%</option>
                                                                                                                        <option value="70-80">70-80%</option>
                                                                                                                        <option value="80-90">80-90%</option>
                                                                                                                        <option value="90-100">90-100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Tws_Filter()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Tws_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="twsReorts" name="twsReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Tws_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Trainee-wise Summary Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Tws_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Tws_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Trainee ID</th>
                                                                                                        <th>Trainee Name</th>
                                                                                                        <th>Designation</th>
                                                                                                        <th>Trainee Region</th>
                                                                                                        <th>No of Workshop</th>
                                                                                                        <th>Questions Played</th>
                                                                                                        <th>Correct</th>
                                                                                                        <th>Wrong</th>
                                                                                                        <th>Result</th>
                                                                                                        <th>Avg Responce Time</th>
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
                                                                    <!-- trainee_wise_summary End -->

                                                                    <!-- traineetopic_wise_report Start here 10-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="traineetopic_wise_report_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">
                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>
                                                                                        <div id="collapse_3_2" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Ttqwr_Frm" name="Filter_Ttqwr_Frm" method="post">
                                                                                                    <?php if ($Company_id == "") { ?>
                                                                                                        <div class="row margin-bottom-10">
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Ttqwr_id" name="company_Ttqwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Ttqwr_CompanywiseData();">
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
                                                                                                                <label class="control-label col-md-3">Workshop Type</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_Ttqwr_type" name="workshop_Ttqwr_type" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="ge_Ttqwr_tWTypewiseData();">

                                                                                                                        <?php if (count((array)$Ttqwr_WTypeData) > 0) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($Ttqwr_WTypeData as $Rgn) { ?>
                                                                                                                                <option value="<?= $Rgn->id; ?>"><?php echo $Rgn->workshop_type; ?></option>
                                                                                                                        <?php }
                                                                                                                        } ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_Ttqwr_subtype" name="workshop_Ttqwr_subtype" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Ttqwr_WSubTypewiseData();">
                                                                                                                        <option value="">All Sub-type</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wregion_Ttqwr_id" name="wregion_Ttqwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="ge_Ttqwr_tWTypewiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Ttqwr_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Ttqwr_RegionData as $Rdata) { ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wsubregion_Ttqwr_id" name="wsubregion_Ttqwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Ttqwr_WSubTypewiseData();">
                                                                                                                        <option value="">Select Sub-region</option>

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
                                                                                                                    <select id="workshop_Ttqwr_id" name="workshop_Ttqwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Ttqwr_WorkshopwiseData();">
                                                                                                                        <option value="">All Workshop</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Ttqwr_WorkshopData)) {
                                                                                                                            foreach ($Ttqwr_WorkshopData as $cmp) { ?>
                                                                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->workshop_name; ?></option>
                                                                                                                        <?php }
                                                                                                                        } ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="tregion_Ttqwr_id" name="tregion_Ttqwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Ttqwr_TregionwiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Ttqwr_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Ttqwr_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                    <select id="user_Ttqwr_id" name="user_Ttqwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Trainee</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Ttqwr_TraineeData)) {
                                                                                                                            foreach ($Ttqwr_TraineeData as $cmp) { ?>
                                                                                                                                <option value="<?= $cmp->user_id; ?>"><?php echo $cmp->traineename; ?></option>
                                                                                                                        <?php }
                                                                                                                        } ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Topics&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="topic_Ttqwr_id" name="topic_Ttqwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="range_Ttqwr_id" name="range_Ttqwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 50%">
                                                                                                                        <option value="">Select</option>
                                                                                                                        <option value="0-10">0-10%</option>
                                                                                                                        <option value="10-20">10-20%</option>
                                                                                                                        <option value="20-30">20-30%</option>
                                                                                                                        <option value="30-40">30-40%</option>
                                                                                                                        <option value="40-50">40-50%</option>
                                                                                                                        <option value="50-60">50-60%</option>
                                                                                                                        <option value="60-70">60-70%</option>
                                                                                                                        <option value="70-80">70-80%</option>
                                                                                                                        <option value="80-90">80-90%</option>
                                                                                                                        <option value="90-100">90-100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Session&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshop_Ttqwr_session" name="workshop_Ttqwr_session" class="form-control input-sm select2me" placeholder="Please select" style="width: 50%">
                                                                                                                        <option value="">All</option>
                                                                                                                        <option value="PRE">PRE</option>
                                                                                                                        <option value="POST">POST</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Designation &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="designation_Ttqwr_id" name="designation_Ttqwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                                                                        <?php
                                                                                                                        if (isset($Ttqwr_DesignationData)) {
                                                                                                                            echo '<option value="0">All Designation</option>';
                                                                                                                            foreach ($Ttqwr_DesignationData as $Rdata) { ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Report By;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="report_Ttqwr_type" name="report_Ttqwr_type" class="form-control input-sm " placeholder="Please select" style="width: 50%">
                                                                                                                        <option value="1">Topic wise</option>
                                                                                                                        <option value="2">Question set wise</option>
                                                                                                                        <option value="3">Workshop wise</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-10"></div>
                                                                                                    <div class="col-md-12">
                                                                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Ttqwr_Filter()">Search</button>
                                                                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Ttqwr_Filter()">Reset</button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="ttqwrReorts" name="ttqwrReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Ttqwr_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Trainee Report (Topic+ Questions Set)
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Ttqwr_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Ttqwr_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Trainee ID</th>
                                                                                                        <th>Employee ID</th>
                                                                                                        <th>Trainee Region</th>
                                                                                                        <th>Trainee Name</th>
                                                                                                        <th>Designation</th>
                                                                                                        <th>Workshop Region</th>
                                                                                                        <th>Workshop Sub-region</th>
                                                                                                        <th>Workshop type</th>
                                                                                                        <th>Workshop Sub-type</th>
                                                                                                        <th>Workshop Name</th>
                                                                                                        <th id="dynamic_col">Topics</th>
                                                                                                        <th>Questions Played</th>
                                                                                                        <th>Correct</th>
                                                                                                        <th>Wrong</th>
                                                                                                        <th>Result</th>
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
                                                                    <!-- traineetopic_wise_report End -->

                                                                    <!-- trainer_wise_summary_report_tab Start here 10-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="trainer_wise_summary_report_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="alert alert-danger display-hide" id="errordiv">
                                                                                    <button class="close" data-close="alert"></button>
                                                                                    You have some form errors. Please check below.
                                                                                    <br><span id="errorlog"></span>
                                                                                </div>
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">
                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_Twr">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>
                                                                                        <div id="collapse_Twr" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Twr_Frm" name="Filter_Twr_Frm" method="post">
                                                                                                    <?php if ($Company_id == "") { ?>
                                                                                                        <div class="row margin-bottom-10">
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Twr_id" name="company_Twr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Twr_CompanywiseData();">
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
                                                                                                                    <select id="workshoptype_Twr_id" name="workshoptype_Twr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Twr_WTypewiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Twr_WTypeData)) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($Twr_WTypeData as $WType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_type; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-Type&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshopsubtype_Twr_id" name="workshopsubtype_Twr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Workshop Type</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="region_Twr_id" name="region_Twr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Twr_WTypewiseData();">

                                                                                                                        <?php
                                                                                                                        if (isset($Twr_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Twr_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="subregion_Twr_id" name="subregion_Twr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select SubRegion</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainer Name&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="trainer_Twr_id" name="trainer_Twr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Trainer</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Twr_TrainerData)) {
                                                                                                                            foreach ($Twr_TrainerData as $TDype) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $TDype->userid; ?>"><?php echo $TDype->fullname; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="range_Twr_id" name="range_Twr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 50%">
                                                                                                                        <option value="">Select</option>
                                                                                                                        <option value="0-10">0-10%</option>
                                                                                                                        <option value="10-20">10-20%</option>
                                                                                                                        <option value="20-30">20-30%</option>
                                                                                                                        <option value="30-40">30-40%</option>
                                                                                                                        <option value="40-50">40-50%</option>
                                                                                                                        <option value="50-60">50-60%</option>
                                                                                                                        <option value="60-70">60-70%</option>
                                                                                                                        <option value="70-80">70-80%</option>
                                                                                                                        <option value="80-90">80-90%</option>
                                                                                                                        <option value="90-100">90-100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Twr_Filter()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Twr_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="twrReorts" name="twrReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Twr_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Trainer-wise Summary Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Twr_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Twr_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <!--<th>Company Name</th>-->
                                                                                                        <th>Trainer Name</th>
                                                                                                        <th>No of Workshop</th>
                                                                                                        <th>No of Trainees</th>
                                                                                                        <th>No of Topics</th>
                                                                                                        <th>No of Sub-topics</th>
                                                                                                        <th>Questions Played</th>
                                                                                                        <th>Correct</th>
                                                                                                        <th>Wrong</th>
                                                                                                        <th>Result</th>
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
                                                                    <!-- trainer_wise_summary_report_tab End -->

                                                                    <!-- trainer_consolidated_report_tab Start here 11-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="trainer_consolidated_report_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">

                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_Tcr">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>

                                                                                        <div id="collapse_Tcr" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Tcr_Frm" name="Filter_Tcr_Frm" method="post">
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <?php if ($Company_id == "") { ?>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Tcr_id" name="company_Tcr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tcr_CompanywiseData();">
                                                                                                                            <option value="">All Company</option>
                                                                                                                            <?php
                                                                                                                            foreach ($CompanyData as $cmp) { ?>
                                                                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                            <?php } ?>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Trainer &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="trainer_Tcr_id" name="trainer_Tcr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">

                                                                                                                        <?php
                                                                                                                        if (isset($Tcr_TrainerData)) {
                                                                                                                            echo '<option value="0">All Trainer</option>';
                                                                                                                            foreach ($Tcr_TrainerData as $TDype) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $TDype->userid; ?>"><?php echo $TDype->fullname; ?></option>
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        }
                                                                                                                        ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wtype_Tcr_id" name="wtype_Tcr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tcr_WTypewiseData();">

                                                                                                                        <?php
                                                                                                                        if (isset($Tcr_WTypeData)) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($Tcr_WTypeData as $WRType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WRType->id; ?>"><?php echo $WRType->workshop_type; ?></option>
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
                                                                                                                    <select id="workshop_Tcr_subtype" name="workshop_Tcr_subtype" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tcr_WSubTypewiseData();">
                                                                                                                        <option value="">All Sub-type</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">

                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="region_Tcr_id" name="region_Tcr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Tcr_WTypewiseData();">
                                                                                                                        <option value="0">All Region</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Tcr_RegionData)) {
                                                                                                                            foreach ($Tcr_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wsubregion_Tcr_id" name="wsubregion_Tcr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tcr_WSubTypewiseData();">
                                                                                                                        <option value="">Select Sub-region</option>

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
                                                                                                                    <select id="workshop_Tcr_id" name="workshop_Tcr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tcr_WorkshopwiseData();">
                                                                                                                        <option value="">Select Workshop</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Tcr_WorkshopData)) {
                                                                                                                            foreach ($Tcr_WorkshopData as $WType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Session&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="session_Tcr_id" name="session_Tcr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Session</option>
                                                                                                                        <option value="PRE">PRE</option>
                                                                                                                        <option value="POST">POST</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-top-10">
                                                                                                        <div class="col-md-3">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-4">Topic&nbsp;</label>
                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                    <select id="topic_Tcr_id" name="topic_Tcr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Tcr_TopicwiseData();">
                                                                                                                        <option value="">Select Topic</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-3">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-4">SubTopic &nbsp;</label>
                                                                                                                <div class="col-md-8" style="padding:0px;">
                                                                                                                    <select id="subtopic_Tcr_id" name="subtopic_Tcr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Subtopic</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="result_Tcr_range" name="result_Tcr_range" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All</option>
                                                                                                                        <option value="0-10">0-10%</option>
                                                                                                                        <option value="10-20">10-20%</option>
                                                                                                                        <option value="20-30">20-30%</option>
                                                                                                                        <option value="30-40">30-40%</option>
                                                                                                                        <option value="40-50">40-50%</option>
                                                                                                                        <option value="50-60">50-60%</option>
                                                                                                                        <option value="60-70">60-70%</option>
                                                                                                                        <option value="70-80">70-80%</option>
                                                                                                                        <option value="80-90">80-90%</option>
                                                                                                                        <option value="90-100">90-100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Tcr_Filter()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Tcr_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="tcrReorts" name="tcrReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Tcr_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Trainer Consolidated Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Tcr_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Tcr_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Workshop Region</th>
                                                                                                        <th>Workshop Sub-region</th>
                                                                                                        <th>Workshop type</th>
                                                                                                        <th>Workshop Sub-type</th>
                                                                                                        <th>Workshop Name</th>
                                                                                                        <th>Trainer name</th>
                                                                                                        <th>Topics</th>
                                                                                                        <th>Subtopic</th>
                                                                                                        <th>No of Unique Questions</th>
                                                                                                        <th>No of Trainee played</th>
                                                                                                        <th>Total Questions played</th>
                                                                                                        <th>Total Correct answers</th>
                                                                                                        <th>Result %</th>
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
                                                                    <!-- trainer_consolidated_report_tab End -->

                                                                    <!-- workshop_wise_report_tab Start here 11-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="workshop_wise_report_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="alert alert-danger display-hide" id="errordiv">
                                                                                    <button class="close" data-close="alert"></button>
                                                                                    You have some form errors. Please check below.
                                                                                    <br><span id="errorlog"></span>
                                                                                </div>
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">
                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_Wwr">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>
                                                                                        <div id="collapse_Wwr" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Wwr_Frm" name="Filter_Wwr_Frm" method="post">
                                                                                                    <?php if ($Company_id == "") { ?>
                                                                                                        <div class="row margin-bottom-10">
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Wwr_id" name="company_Wwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Wwr_CompanywiseData();">
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
                                                                                                                    <select id="workshoptype_Wwr_id" name="workshoptype_Wwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Wwr_WTypewiseData();">

                                                                                                                        <?php
                                                                                                                        if (isset($Wwr_WTypeData)) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($Wwr_WTypeData as $WRType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WRType->id; ?>"><?php echo $WRType->workshop_type; ?></option>
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
                                                                                                                    <select id="workshop_Wwr_subtype" name="workshop_Wwr_subtype" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" onchange="get_Wwr_WSubTypewiseData();">
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
                                                                                                                    <select id="region_Wwr_id" name="region_Wwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Wwr_WTypewiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Wwr_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Wwr_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="wsubregion_Wwr_id" name="wsubregion_Wwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Wwr_WSubTypewiseData();">
                                                                                                                        <option value="">Select Sub-region</option>

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
                                                                                                                    <select id="workshop_Wwr_id" name="workshop_Wwr_id" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Workshop</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Wwr_WorkshopData)) {
                                                                                                                            foreach ($Wwr_WorkshopData as $WType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="range_Wwr_id" name="range_Wwr_id" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select</option>
                                                                                                                        <option value="0-10">0-10%</option>
                                                                                                                        <option value="10-20">10-20%</option>
                                                                                                                        <option value="20-30">20-30%</option>
                                                                                                                        <option value="30-40">30-40%</option>
                                                                                                                        <option value="40-50">40-50%</option>
                                                                                                                        <option value="50-60">50-60%</option>
                                                                                                                        <option value="60-70">60-70%</option>
                                                                                                                        <option value="70-80">70-80%</option>
                                                                                                                        <option value="80-90">80-90%</option>
                                                                                                                        <option value="90-100">90-100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Wwr_Filter()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Wwr_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="wwrReorts" name="wwrReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Wwr_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Workshop-wise Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Wwr_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Wwr_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Workshop <br />Region</th>
                                                                                                        <th>Sub-region</th>
                                                                                                        <th>Workshop <br />Type</th>
                                                                                                        <th>Sub-Type</th>
                                                                                                        <th>Workshop <br />name</th>
                                                                                                        <th>No of <br />Question Set</th>
                                                                                                        <th>Questions <br />Played</th>
                                                                                                        <th>Correct</th>
                                                                                                        <th>Wrong</th>
                                                                                                        <th>Result</th>
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
                                                                    <!-- workshop_wise_report_tab End -->

                                                                    <!-- question_wise_report_tab Start here 11-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="question_wise_report_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">

                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id != "" ? 'collapsed' : ''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_Qwr">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>

                                                                                        <div id="collapse_Qwr" class="panel-collapse <?php echo ($Company_id != "" ? 'collapse' : ''); ?>">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Qwr_Frm" name="Filter_Qwr_Frm" method="post">
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <?php if ($Company_id == "") { ?>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Qwr_id" name="company_Qwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Qwr_CompanywiseData();">
                                                                                                                            <option value="">All Company</option>
                                                                                                                            <?php
                                                                                                                            foreach ($CompanyData as $cmp) { ?>
                                                                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                                                            <?php } ?>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshoptype_Qwr_id" name="workshoptype_Qwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Qwr_WTypewiseData();">
                                                                                                                        <?php
                                                                                                                        if (isset($Qwr_WTypeData)) {
                                                                                                                            echo '<option value="0">All Type</option>';
                                                                                                                            foreach ($Qwr_WTypeData as $WType) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_type; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-Type&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="workshopsubtype_Qwr_id" name="workshopsubtype_Qwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select Workshop Type</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Workshop Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="region_Qwr_id" name="region_Qwr_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="get_Qwr_WTypewiseData();">

                                                                                                                        <?php
                                                                                                                        if (isset($Qwr_RegionData)) {
                                                                                                                            echo '<option value="0">All Region</option>';
                                                                                                                            foreach ($Qwr_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Workshop Sub-Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="subregion_Qwr_id" name="subregion_Qwr_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">Select SubRegion</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>


                                                                                                    <!--                                            <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                        <div class="col-md-3" style="padding:0px;">
                                                            <input type="number" id="from_range" name="from_range" class="form-control input-sm" style="width: 100%" min="0" max="100">                                                                    
                                                        </div>
                                                        <label class="control-label col-md-1">To&nbsp;</label>
                                                        <div class="col-md-3" style="padding:0px;">
                                                            <input type="number" id="to_range" name="to_range" class="form-control input-sm"  style="width: 100%" min="0" max="100">                                                                    
                                                        </div>
                                                    </div>
                                                </div>-->
                                                                                                    <div class="row margin-bottom-10">
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="result_Qwr_range" name="result_Qwr_range" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All</option>
                                                                                                                        <option value="0-10">0-10%</option>
                                                                                                                        <option value="10-20">10-20%</option>
                                                                                                                        <option value="20-30">20-30%</option>
                                                                                                                        <option value="30-40">30-40%</option>
                                                                                                                        <option value="40-50">40-50%</option>
                                                                                                                        <option value="50-60">50-60%</option>
                                                                                                                        <option value="60-70">60-70%</option>
                                                                                                                        <option value="70-80">70-80%</option>
                                                                                                                        <option value="80-90">80-90%</option>
                                                                                                                        <option value="90-100">90-100%</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="clearfix margin-top-20"></div>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Set_Qwr_Filter()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Qwr_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="qwrReorts" name="qwrReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Qwr_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Question Wise Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Qwr_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Qwr_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Question Id</th>
                                                                                                        <th>Question</th>
                                                                                                        <th>Question Set</th>
                                                                                                        <th>Workshop Name</th>
                                                                                                        <th>Workshop Type</th>
                                                                                                        <th>Workshop Sub-Type</th>
                                                                                                        <th>Workshop Sub-Region</th>
                                                                                                        <th>Workshop Region</th>
                                                                                                        <th>Correct answer</th>
                                                                                                        <th>No of Trainee played</th>
                                                                                                        <th>Result %</th>
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
                                                                    <!-- question_wise_report_tab End -->

                                                                    <!-- imei_report_tab Start here 11-04-2023 Nirmal Gajjar -->
                                                                    <div class="tab-pane" id="imei_report_tab">
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="panel-group accordion" id="accordion3">
                                                                                    <div class="panel panel-default">
                                                                                        <div class="panel-heading">
                                                                                            <h4 class="panel-title">
                                                                                                <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_Dir">
                                                                                                    Report Search </a>
                                                                                            </h4>
                                                                                        </div>
                                                                                        <div id="collapse_Dir" class="panel-collapse ">
                                                                                            <div class="panel-body">
                                                                                                <form id="Filter_Dir_Frm" name="Filter_Dir_Frm" method="post">
                                                                                                    <?php if ($Company_id == "") { ?>
                                                                                                        <div class="row margin-bottom-10">
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-group">
                                                                                                                    <label class="control-label col-md-3">Company&nbsp;</label>
                                                                                                                    <div class="col-md-9" style="padding:0px;">
                                                                                                                        <select id="company_Dir_id" name="company_Dir_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Dir_CompanywiseData();">
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
                                                                                                                <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="tregion_Dir_id" name="tregion_Dir_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="get_Dir_TrainerwiseData()">
                                                                                                                        <option value="">All Region</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Dir_RegionData)) {
                                                                                                                            foreach ($Dir_RegionData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Designation &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="designation_Dir_id" name="designation_Dir_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Designation</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Dir_DesignationData)) {
                                                                                                                            foreach ($Dir_DesignationData as $Rdata) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
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
                                                                                                                <label class="control-label col-md-3">Device Users &nbsp;</label>
                                                                                                                <div class="col-md-9" style="padding:0px;">
                                                                                                                    <select id="user_Dir_id" name="user_Dir_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                                                                        <option value="">All Users</option>
                                                                                                                        <?php
                                                                                                                        if (isset($Dir_RegionData)) {
                                                                                                                            foreach ($Dir_RegionData as $Type) {
                                                                                                                        ?>
                                                                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        }
                                                                                                                        ?>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="col-md-offset-8 col-md-4 text-right">
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Dir_DatatableRefresh()">Search</button>
                                                                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="Reset_Dir_Filter()">Reset</button>
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
                                                                        <div class="row mt-10">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet light bordered">
                                                                                    <form id="dirReorts" name="dirReorts" method="post" action="<?php echo base_url() . 'trainer_trainee_workshop_reports/export_Dir_Report' ?>">
                                                                                        <div class="portlet-title">
                                                                                            <div class="caption caption-font-24">
                                                                                                Device IMEI Report
                                                                                                <div class="tools"> </div>
                                                                                            </div>
                                                                                            <?php if ($role==1 || $role==2 || (isset($acces_management->allow_export) && $acces_management->allow_export)) { ?>
                                                                                                <div class="actions">
                                                                                                    <div class="btn-group pull-right">
                                                                                                        <button type="button" onclick="export_Dir_Confirm()" autofocus="" accesskey="" name="export_excel" id="export_excel" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                                                        &nbsp;&nbsp;

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        <div class="clearfix margin-top-20"></div>
                                                                                        <div class="portlet-body">
                                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_Dir_table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Trainee Id</th>
                                                                                                        <th>Employee Code</th>
                                                                                                        <th>First Name</th>
                                                                                                        <th>Last Name</th>
                                                                                                        <th>Email</th>
                                                                                                        <th>Mobile No.</th>
                                                                                                        <th>Employment Year</th>
                                                                                                        <th>Education Background</th>
                                                                                                        <th>Department/Division</th>
                                                                                                        <th>Region/Branch</th>
                                                                                                        <th>Designation</th>
                                                                                                        <th>Area</th>
                                                                                                        <th>Status</th>
                                                                                                        <th>Platform</th>
                                                                                                        <th>Model</th>
                                                                                                        <th>IMEI</th>
                                                                                                        <th>Serial No</th>
                                                                                                        <th>Date & Time</th>
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
                                                                    <!-- imei_report_tab End-->

                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!--Tab 3 Content End Here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- tab 3 end here -->


                        </div>
                    </div>
                    <!-- Tabs -->
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php //$this->load->view('inc/inc_quick_nav'); 
    ?>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/customjs/ai_process.js" type="text/javascript"></script>

    <script src="<?php echo $asset_url; ?>assets/global/highcharts/highstock.js"></script>
    <?php if ($role==1 || $role==2 || (isset($acces_management->allow_print) && $acces_management->allow_print)) { ?>
        <script src="<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js"></script>
    <?php } ?>
    <script>
        var trainer_id = "<?php echo $trainer_id; ?>";
        var company_id = "<?php echo $Supcompany_id; ?>";
        $(".select2_rpt2").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
        $(".select2_rpt").select2({
            placeholder: 'All Select',
            width: '100%'
        });
        $(".select2_rpt3").select2({
            placeholder: 'All Workshop',
            width: '100%'
        });
        //TableRefresh();
        function Redirect(url) {
            // window.location = url;
            window.open(url, '_blank');
        }
        jQuery(document).ready(function() {
            if (trainer_id != '') {
                dashboard_refresh_trw(0);
            }
        });

        function getCompanyTrainer_trw() {
            $('#wsubregion_id_trw').empty();
            $('#workshop_subtype_trw').empty();
            var compnay_id = $('#company_id_trw').val();
            if (compnay_id == "") {
                $('#user_id_trw').empty();
                $('#workshop_type_id_trw').empty();
                $('#wregion_id_trw').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_trw').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data/0",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_id_trw').empty();
                        $('#user_id_trw').append(Oresult['TrainerData']);
                        $('#wregion_id_trw').empty();
                        $('#wregion_id_trw').append(Oresult['RegionData']);
                        $('#workshop_type_id_trw').empty();
                        $('#workshop_type_id_trw').append(Oresult['WTypeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWTypewiseData_trw() {
            $('#wsubregion_id_trw').empty();
            $('#workshop_subtype_trw').empty();
            var compnay_id = $('#company_id_trw').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshop_type_id_trw').val();
            var workshop_region = $('#wregion_id_trw').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_subtype_trw').empty();
                        $('#workshop_subtype_trw').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_id_trw').empty();
                        $('#wsubregion_id_trw').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function dashboard_refresh_trw() {
            if (trainer_id == '') {
                if ($('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
            }
            TableRefresh_trw();
        }

        function TableRefresh_trw() {
            var table = $('#wksh-list-trw');
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
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'width': '20px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'width': '100px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [2]
                    },
                    {
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [3]
                    },
                    {
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [4]
                    },
                    {
                        'width': '200px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [5]
                    }
                ],

                "order": [
                    [0, "desc"]
                ],
                //bFilter: false,
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/load_workshop'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_id_trw').val()
                    });
                    if (trainer_id == '') {
                        aoData.push({
                            name: 'user_id',
                            value: $('#user_id_trw').val()
                        });
                    } else {
                        aoData.push({
                            name: 'user_id',
                            value: trainer_id
                        });
                    }
                    aoData.push({
                        name: 'workshop_type_id',
                        value: $('#workshop_type_id_trw').val()
                    });
                    //aoData.push({name: 'WorkshopFlag', value: $('#user_id').val()});
                    aoData.push({
                        name: 'wregion_id',
                        value: $('#wregion_id_trw').val()
                    });
                    aoData.push({
                        name: 'workshop_id',
                        value: $('#workshop_id_trw').val()
                    });
                    aoData.push({
                        name: 'wsubregion_id',
                        value: $('#wsubregion_id_trw').val()
                    });
                    aoData.push({
                        name: 'workshop_subtype',
                        value: $('#workshop_subtype_trw').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        $('#top_five_panel_trw').html(json['top_five_table']);
                        $('#bottom_five_panel_trw').html(json['bottom_five_table']);
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {}
            });
        }

        function workshop_summary_trw(workshop_id) {
            $('#popupchart-trw').empty();
            if (trainer_id == '') {
                //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                var tdata = {
                    company_id: $('#company_id_trw').val(),
                    user_id: $('#user_id_trw').val(),
                    workshop_id: workshop_id
                };
            } else {
                tdata = {
                    company_id: company_id,
                    user_id: trainer_id,
                    workshop_id: workshop_id
                };
            }
            $.ajax({
                type: "POST",
                data: tdata,
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/load_wksh_summary",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(response) {
                    if (response != '') {
                        var json = jQuery.parseJSON(response);
                        $('#popupchart-trw').empty();
                        $("#popupchart-trw").append(json['summary_report']);
                        $('#modal_title-trw').html(json['Modal_Title']);
                        $('#chart-modal-trw').modal('show');
                    }
                    customunBlockUI();
                }
            });
        }

        function workshop_detail(workshop_id) {
            $('#popupchart-trw').empty();
            if (trainer_id == '') {
                //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                var tdata = {
                    company_id: $('#company_id_trw').val(),
                    user_id: $('#user_id_trw').val(),
                    workshop_id: workshop_id
                };
            } else {
                tdata = {
                    company_id: company_id,
                    user_id: trainer_id,
                    workshop_id: workshop_id
                };
            }
            $.ajax({
                type: "POST",
                data: tdata,
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/load_wksh_detail",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(response) {
                    if (response != '') {
                        var json = jQuery.parseJSON(response);
                        $('#popupchart-trw').empty();
                        $("#popupchart-trw").append(json['detail_report']);
                        $('#modal_title-trw').html(json['Modal_Title']);
                        $('#chart-modal-trw').modal('show');
                    }
                    customunBlockUI();
                }
            });
        }

        function workshop_trainee(workshop_id) {
            $('#popupchart-trw').empty();
            if (trainer_id == '') {
                //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                var tdata = {
                    company_id: $('#company_id_trw').val(),
                    user_id: $('#user_id_trw').val(),
                    workshop_id: workshop_id
                };
            } else {
                tdata = {
                    company_id: company_id,
                    user_id: trainer_id,
                    workshop_id: workshop_id
                };
            }
            $.ajax({
                type: "POST",
                data: tdata,
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/load_wksh_trainee",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(response) {
                    if (response != '') {
                        var json = jQuery.parseJSON(response);
                        $('#popupchart-trw').empty();
                        $("#popupchart-trw").append(json['trainee_report']);
                        $('#chart-modal-trw').modal('show');
                        $('#modal_title-trw').html(json['Modal_Title']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getTrainnewiseData(workshop_id) {
            if (trainer_id == '') {
                //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                var tdata = {
                    company_id: $('#company_id_trw').val(),
                    user_id: $('#user_id_trw').val(),
                    workshop_id: workshop_id,
                    trainee_id: $('#pop_trainee_id_trw').val()
                };
            } else {
                tdata = {
                    company_id: company_id,
                    user_id: trainer_id,
                    workshop_id: workshop_id,
                    trainee_id: $('#pop_trainee_id_trw').val()
                };
            }
            $.ajax({
                type: "POST",
                data: tdata,
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/gettraineewise_topic",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(lchtml) {
                    $('#container2').html(lchtml);
                    customunBlockUI();
                }
            });
        }
    </script>














    <!-- Tab 1 Content 2 Js start here ----------------------------------------------------------------------------------------------------->
    <script>
        var trainer_id = "<?php echo $trainer_id; ?>";
        var company_id = "<?php echo $Supcompany_id; ?>";
        var cnt = 1;

        function Redirect(url) {
            // window.location = url;
            window.open(url, '_blank');
        }
        jQuery(document).ready(function() {
            $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            if (trainer_id != '') {
                $('#company_id_trainer').select2("val", "" + company_id);
            }
            // $('#company_id').val(< ?php //echo $company_id;?>).trigger('change');
            // $('#workshop_type_id_trainer').val(< ?php //echo $workshop_type_id_trainer;?>).trigger('change');
            // $('#user_id_trainer').val(< ?php //echo $trainer_id;?>).trigger('change');
            // $('#workshop_id_trainer').val(< ?php //echo $workshop_id_trainer;?>).trigger('change');
            // dashboard_refresh_trainer_comperition();
        });

        function getCompanyTrainer() {
            $('#workshop_id_trainer').empty();
            $('#wsubregion_id_trainer').empty();
            $('#workshop_subtype_trainer').empty();
            $('#workshop_id_trainer').empty();
            var compnay_id = $('#company_id_trainer').val();
            if (compnay_id == "") {
                $('#user_id_trainer').empty();
                $('#workshop_type_id_trainer').empty();
                $('#wregion_id_trainer').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_trainer').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data/0",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_id_trainer').empty();
                        $('#user_id_trainer').append(Oresult['TrainerData']);
                        $('#wregion_id_trainer').empty();
                        $('#wregion_id_trainer').append(Oresult['RegionData']);
                        $('#workshop_type_id_trainer').empty();
                        $('#workshop_type_id_trainer').append(Oresult['WTypeData']);
                        $('#workshop_id_trainer').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWorkshopTrainer() {
            $('#workshop_id_trainer').empty();
            $('#wsubregion_id_trainer').empty();
            $('#workshop_subtype_trainer').empty();
            var compnay_id = $('#company_id_trainer').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshop_type_id_trainer').val();
            var workshop_region = $('#wregion_id_trainer').val();
            var user_id = $('#user_id_trainer').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region,
                    user_id: user_id
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_trainer').empty();
                        $('#workshop_id_trainer').append(Oresult['WorkshopData']);
                        $('#workshop_subtype_trainer').empty();
                        $('#workshop_subtype_trainer').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_id_trainer').empty();
                        $('#wsubregion_id_trainer').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWSubTypewiseDataTrainer() {
            $('#topic_id').empty();
            $('#workshop_id_trainer').empty();
            var compnay_id = $('#company_id_trainer').val();
            if (compnay_id == "") {
                return false;
            }
            var workshoptype_id = $('#workshop_type_id_trainer').val();
            var region_id = $('#wregion_id_trainer').val();
            var subregion_id = $('#wsubregion_id_trainer').val();
            var workshopsubtype_id = $('#workshop_subtype_trainer').val();
            var user_id = $('#user_id_trainer').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_trainer').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id,
                    user_id: user_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_trainer').empty();
                        $('#workshop_id_trainer').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function dashboard_refresh_trainer_comperition() {
            if ($('#company_id_trainer').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            } else if (!$('#user_id_trainer').val() || $('#user_id_trainer').val() == "") {
                ShowAlret("Please select Trainer.!!", 'error');
                return false;
            } else if (!$('#workshop_id_trainer').val() || $('#workshop_id_trainer').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
            var tdata = {
                company_id: $('#company_id_trainer').val(),
                user_id: $('#user_id_trainer').val(),
                workshop_type_id: $('#workshop_type_id_trainer').val(),
                workshop_id: $('#workshop_id_trainer').val()
            };
            $.ajax({
                type: "POST",
                data: tdata,
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/load_workshop_table/" + cnt,
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(response) {
                    if (response != '') {
                        var json = jQuery.parseJSON(response);
                        var wksh_list = json['wksh_list'];
                        var comparison_panels = json['comparison_panels'];
                        if (cnt == 1) {
                            $('#wksh-list-trainer-comperition tbody').empty();
                        }
                        if (comparison_panels != '') {
                            $('#comparison-table').append(comparison_panels);
                        }
                        if (wksh_list != '') {
                            $('#wksh-list-trainer-comperition tbody').append(wksh_list);
                            cnt++;
                        } else {
                            ShowAlret("No Data Found for selected Workshop.!!", 'error');

                        }
                    }
                    customunBlockUI();
                }
            });
        }

        function remove_workshop(cnt) {
            $('#tdata' + cnt).remove();
            $('#rdata' + cnt).remove();
        }
    </script>
    <!-- tab 1 Content 2 js end here -->















    <!-- tab 1 Content 3 js end here -------------------------------------------------------------------------------------------------- -->
    <script>
        var trainer_id = "<?php echo $trainer_id; ?>";
        var company_id = "<?php echo $Supcompany_id; ?>";
        var frm_accuracy = document.frm_accuracy;


        function Redirect(url) {
            window.open(url, '_blank');
        }
        jQuery(document.frm_accuracy).ready(function() {
            if (trainer_id != '') {
                $('#company_id_trainer_accuracy').select2("val", "" + company_id);
            }
            $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
        });

        function getCompanyTrainerAccuracy() {
            $('#trainee_id_accuracy').empty();
            $('#wsubregion_id_trainer_accuracy').empty();
            $('#workshop_subtype_trainer_accuracy').empty();
            var compnay_id = $('#company_id_trainer_accuracy').val();
            if (compnay_id == "") {
                $('#workshop_id_trainer_accuracy').empty();
                $('#user_id_trainer_accuracy').empty();
                $('#workshop_type_id_trainer_accuracy').empty();
                $('#wregion_id_trainer_accuracy').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_trainer_accuracy').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_trainer_accuracy').empty();
                        $('#workshop_id_trainer_accuracy').append(Oresult['WorkshopData']);
                        $('#user_id_trainer_accuracy').empty();
                        $('#user_id_trainer_accuracy').append(Oresult['TrainerData']);
                        $('#wregion_id_trainer_accuracy').empty();
                        $('#wregion_id_trainer_accuracy').append(Oresult['RegionData']);
                        $('#workshop_type_id_trainer_accuracy').empty();
                        $('#workshop_type_id_trainer_accuracy').append(Oresult['WTypeData']);
                        $('#trainee_region_id_trainer_accuracy').empty();
                        $('#trainee_region_id_trainer_accuracy').append(Oresult['TraineeRegionData'])
                    }
                    customunBlockUI();
                }
            });
        }

        function getWorkshopAccruacy() {
            $('#workshop_id_trainer_accuracy').empty();
            $('#wsubregion_id_trainer_accuracy').empty();
            $('#workshop_subtype_trainer_accuracy').empty();
            var compnay_id = $('#company_id_trainer_accuracy').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshop_type_id_trainer_accuracy').val();
            var workshop_region = $('#wregion_id_trainer_accuracy').val();
            var user_id = $('#user_id_trainer_accuracy').val();

            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region,
                    user_id: user_id
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_trainer_accuracy').empty();
                        $('#workshop_id_trainer_accuracy').append(Oresult['WorkshopData']);
                        $('#workshop_subtype_trainer_accuracy').empty();
                        $('#workshop_subtype_trainer_accuracy').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_id_trainer_accuracy').empty();
                        $('#wsubregion_id_trainer_accuracy').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWSubTypewiseDataAccuracy() {
            $('#topic_id').empty();
            $('#workshop_id_trainer_accuracy').empty();
            var compnay_id = $('#company_id_trainer_accuracy').val();
            if (compnay_id == "") {
                return false;
            }
            var workshoptype_id = $('#workshop_type_id_trainer_accuracy').val();
            var region_id = $('#wregion_id_trainer_accuracy').val();
            var subregion_id = $('#wsubregion_id_trainer_accuracy').val();
            var workshopsubtype_id = $('#workshop_subtype_trainer_accuracy').val();
            var user_id = $('#user_id_trainer_accuracy').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_trainer_accuracy').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id,
                    user_id: user_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_trainer_accuracy').empty();
                        $('#workshop_id_trainer_accuracy').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function getTraineeAccuracy() {
            $('#trainee_id_accuracy').empty();
            var compnay_id = $('#company_id_trainer_accuracy').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshop_id: $('#workshop_id_trainer_accuracy').val(),
                    workshop_session: $('#workshop_session_trainer_accuracy').val(),
                    tregion_id: $('#trainee_region_id_trainer_accuracy').val(),
                    trainer_id: $('#user_id_trainer_accuracy').val(),
                    workshop_type: $('#workshop_type_id_trainer_accuracy').val()
                },
                url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_accuracy').empty();
                        $('#trainee_id_accuracy').append(Oresult['AllSelectionTrainee']);
                    }
                    customunBlockUI();
                }
            });
        }

        function dashboard_refresh_trainer_accuracy() {
            $('#wksh-list-trainer_accuracy tbody').empty();
            $('#trainee-top-five tbody').empty();
            $('#trainee-bottom-five tbody').empty();
            $('#topic-subtopic-chart').empty();
            if ($('#company_id_trainer_accuracy').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            } else if ($('#workshop_id_trainer_accuracy').val() == "0" || $('#workshop_id_trainer_accuracy').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    data: {
                        company_id: $('#company_id_trainer_accuracy').val(),
                        user_id: $('#user_id_trainer_accuracy').val(),
                        workshop_type_id: $('#workshop_type_id_trainer_accuracy').val(),
                        workshop_id: $('#workshop_id_trainer_accuracy').val(),
                        workshop_session: $('#workshop_session_trainer_accuracy').val(),
                        trainee_id: $('#trainee_id_accuracy').val(),
                        trainee_region_id: $('#trainee_region_id_trainer_accuracy').val()
                    },
                    //async: false,
                    url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/load_report",
                    beforeSend: function() {
                        customBlockUI();
                    },
                    success: function(response) {
                        if (response != '') {
                            var json = jQuery.parseJSON(response);
                            var wksh_list = json['wksh_list'];
                            var trainee_top_five_table = json['trainee_top_five_table'];
                            var trainee_bottom_five_table = json['trainee_bottom_five_table'];

                            if (trainee_top_five_table != '') {
                                $('#trainee-top-five tbody').empty();
                                $('#trainee-top-five tbody').append(trainee_top_five_table);
                            }
                            if (trainee_bottom_five_table != '') {
                                $('#trainee-bottom-five tbody').empty();
                                $('#trainee-bottom-five tbody').append(trainee_bottom_five_table);
                            }
                            if (wksh_list != '') {
                                $('#wksh-list-trainer_accuracy tbody').empty();
                                $('#wksh-list-trainer_accuracy tbody').append(wksh_list);
                            }
                            $('#topic-subtopic-chart').empty();
                            $("#topic-subtopic-chart").append(json['topic_subtopic_chart']);
                        }
                        customunBlockUI();
                    }
                });
            }
        }

        function exportConfirmTrainerAccuracy() {
            if ($('#company_id_trainer_accuracy').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            } else if ($('#workshop_id_trainer_accuracy').val() == "0" || $('#workshop_id_trainer_accuracy').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            frm_accuracy.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }
    </script>
    <!-- tab 1 Content 3 js end here -->



    <!-- TAB 2 Content 1 start here  -->
    <script>
        var DefaultTrainee = '<?php echo $DefaultTrainee_id ?>';
        jQuery(document).ready(function() {
            TableRefresh(true);
        });
        $(".select2_rpt").select2({
            placeholder: 'All Select',
            width: '100%'
        });

        function TableRefresh(firstTimeLoad) {
            var trainee_id = $('#trainee_id_tw').val();
            var company_id = $('#company_id_tw').val();
            if (firstTimeLoad == undefined) {
                if (company_id == "") {
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
                }
                if (trainee_id == "") {
                    ShowAlret("Please select Trainee.!!", 'error');
                    return false;
                }
            } else {
                trainee_id = DefaultTrainee;
            }
            var table = $('#table_index_tw');
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
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [2]
                    },
                    {
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [3]
                    },
                    {
                        'width': '60px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [4]
                    },
                    {
                        'width': '60px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [5]
                    }
                ],
                "order": [
                    [0, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/ajax_getTraineeData'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_id_tw').val()
                    });
                    aoData.push({
                        name: 'trainee_id',
                        value: $('#trainee_id_tw').val()
                    });
                    aoData.push({
                        name: 'workshoptype_id',
                        value: $('#workshoptype_id_tw').val()
                    });
                    aoData.push({
                        name: 'wregion_id',
                        value: $('#wregion_id_tw').val()
                    });
                    aoData.push({
                        name: 'wsubregion_id',
                        value: $('#wsubregion_id_tw').val()
                    });
                    aoData.push({
                        name: 'workshop_subtype',
                        value: $('#workshop_subtype_tw').val()
                    });

                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {}
            });
        }

        function workshop_summary(workshop_id) {
            var company_id = $('#company_id_tw').val();
            var trainee_id = $('#trainee_id_tw').val();
            var workshoptype_id = $('#workshoptype_id_tw').val();
            $.ajax({
                type: "POST",
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/ajax_chart_ta",
                data: {
                    workshop_id: workshop_id,
                    company_id: company_id,
                    trainee_id: trainee_id,
                    workshoptype_id: workshoptype_id
                },
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(Data) {
                    if (Data != '') {
                        var Oresult = jQuery.parseJSON(Data);
                        var DataMSt = Oresult['ChartDataHtml'];
                        if (Oresult['Error'] != '') {
                            $('#errordiv').show();
                            $('#errorlog').html(Oresult['Error']);
                            App.scrollTo(form_error, -200);
                        } else {
                            // console.log(DataMSt);
                            //$('#AppendChart').append(DataMSt);                                  
                        }
                    }
                    customunBlockUI();
                }
            });

        }

        function ResetFilter() {
            $('.select2me').val(null).trigger('change');
            document.FilterFrm.reset();
            TableRefresh(true);
        }

        function getCompanyWorkshopType() {
            var compnay_id = $('#company_id_tw').val();
            if (compnay_id == "") {
                $('#workshoptype_id_tw').empty();
                $('#wregion_id_tw').empty();
                $('#trainee_id_tw').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_tw').empty();
                        $('#trainee_id_tw').append(Oresult['TraineeData']);
                        $('#wregion_id_tw').empty();
                        $('#wregion_id_tw').append(Oresult['RegionData']);
                        $('#workshoptype_id_tw').empty();
                        $('#workshoptype_id_tw').append(Oresult['WTypeData']);
                        $('#trainee_region_id_tw').empty();
                        $('#trainee_region_id_tw').append(Oresult['TraineeRegionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWTypewiseData() {
            $('#wsubregion_id_tw').empty();
            $('#workshop_subtype_tw').empty();
            var compnay_id = $('#company_id_tw').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshoptype_id_tw').val();
            var workshop_region = $('#wregion_id_tw').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_tw').empty();
                        $('#workshop_id_tw').append(Oresult['WorkshopData']);
                        $('#workshop_subtype_tw').empty();
                        $('#workshop_subtype_tw').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_id_tw').empty();
                        $('#wsubregion_id_tw').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getTRegionwiseData() {
            var compnay_id = $('#company_id_tw').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    trainee_region_id: $('#trainee_region_id_tw').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/getTraineeData",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_tw').empty();
                        $('#trainee_id_tw').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }
    </script>
    <!-- TAB 2 Content 1 end here  -->












    <!-- Tab 2 Content 2 Start Here  -->
    <script>
        var FilterFrm = $('#FilterFrmTC');
        var form_error = $('.alert-danger', FilterFrm);
        var form_success = $('.alert-success', FilterFrm);
        var TotalWkshop = 1;
        $(".select2_rpt2").select2({
            placeholder: 'All Select',
            width: '100%'
        });
        $(".select2_rpt").select2({
            placeholder: 'Please Select',
            width: '100%'
        });

        function getCompanywiseData() {

            var compnay_id = $('#company_id_tc').val();
            if (compnay_id == "") {
                $('#trainee_id_tc').empty();
                $('#workshop_id_tc').empty();
                $('#wregion_id_tc').empty();
                $('#workshoptype_id_tc').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_tc').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_tc').empty();
                        $('#workshop_id_tc').append(Oresult['WorkshopData']);
                        $('#wregion_id_tc').empty();
                        $('#wregion_id_tc').append(Oresult['RegionData']);
                        $('#workshoptype_id_tc').empty();
                        $('#workshoptype_id_tc').append(Oresult['WTypeData']);
                        $('#trainee_region_id_tc').empty();
                        $('#trainee_region_id_tc').append(Oresult['TraineeRegionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getTrainee() {
            $('#trainee_id_tc').empty();
            var compnay_id = $('#company_id_tc').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_id = $('#workshop_id_tc').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshop_id: workshop_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_tc').empty();
                        $('#trainee_id_tc').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getTypeWiseWorkshop() {
            $('#workshop_id_tc').empty();
            $('#wsubregion_id_tc').empty();
            $('#workshop_subtype_tc').empty();
            var compnay_id = $('#company_id_tc').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshoptype_id_tc').val();
            var workshop_region = $('#wregion_id_tc').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_tc').empty();
                        $('#workshop_id_tc').append(Oresult['WorkshopData']);
                        $('#workshop_subtype_tc').empty();
                        $('#workshop_subtype_tc').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_id_tc').empty();
                        $('#wsubregion_id_tc').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWSubTypewiseData() {
            $('#topic_id_tc').empty();
            $('#workshop_id_tc').empty();
            var compnay_id = $('#company_id_tc').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshop_subtype_tc').val();
            var workshoptype_id = $('#workshoptype_id_tc').val();
            var region_id = $('#wregion_id_tc').val();
            var subregion_id = $('#wsubregion_id_tc').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_tc').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_tc').empty();
                        $('#workshop_id_tc').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function traineeTableData(workshop_id, RowId, trainee_id) {
            var Isseleted = $("#datatr_" + RowId).hasClass("selectedBox");
            if (Isseleted) {
                $("#datatr_" + RowId).removeClass("selectedBox");
                $('#childdiv_' + RowId).remove();
                return true;
            }
            $.ajax({
                type: "POST",
                data: {
                    workshop_id: workshop_id,
                    RowId: RowId,
                    trainee_id: trainee_id
                },
                async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/ajax_traineeWiseData",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        var TraineeMSt = Oresult['TraineeTable'];
                        if (Oresult['Error'] != '') {
                            ShowAlret(Oresult['Error'], 'error');
                        } else {
                            $('#TraineeChart').append(TraineeMSt);
                            $('#datatr_' + RowId).addClass('selectedBox');
                        }

                    }
                    customunBlockUI();
                }
            });
        }

        function ShowChart() {
            if ($('#company_id_tc').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            }
            if ($('#workshop_id_tc').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/ComparisonWorkshopTable/" + TotalWkshop,
                data: $('#FilterFrmTC').serialize(),
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(Data) {
                    if (Data != '') {
                        var Oresult = jQuery.parseJSON(Data);
                        var TableData = Oresult['ChartTable'];
                        if (Oresult['Error'] != '') {
                            ShowAlret(Oresult['Error'], 'error');
                        } else {
                            if (TableData != "") {
                                $('table#CEtable tbody').append(TableData);
                                traineeTableData($('#workshop_id_tc').val(), TotalWkshop, $('#trainee_id_tc').val());
                                TotalWkshop++;
                            } else {
                                ShowAlret("No Data Found for selected Workshop.!!", 'error');
                            }

                        }
                    }
                    customunBlockUI();
                }
            });
        }

        function RemoveChart(Row_id) {
            $('#datatr_' + Row_id).remove();
            $('#childdiv_' + Row_id).remove();
        }

        function remove_workshop(Row_id) {
            //$('#datatr_'+Row_id).remove();
            $("#datatr_" + Row_id).removeClass("selectedBox");
            $('#childdiv_' + Row_id).remove();
        }

        function getTRegionwiseData() {
            var compnay_id = $('#company_id_tc').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    trainee_region_id: $('#trainee_region_id_tc').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/getTraineeDataTrainee",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_tc').empty();
                        $('#trainee_id_tc').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }
    </script>
    <!-- tab 2 Content 2 end here -->
















    <!-- ---------------------------------------------------------------------------------------------------------------------------------------- -->




    <!-- Tab 2 Conetent 3 Start Here -->
    <script>
        var TotalChart = 1;
        var successflag = 1;
        $(".select2_rpt").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
        $(".select2_rpt2").select2({
            placeholder: 'All Select',
            width: '100%'
        });

        function getCompanywiseData_ta() {

            var compnay_id = $('#company_id_ta').val();
            if (compnay_id == "") {
                $('#trainee_id_ta').empty();
                $('#workshop_id_ta').empty();
                $('#wregion_id_ta').empty();
                $('#workshoptype_id_ta').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_ta').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_ta').empty();
                        $('#trainee_id_ta').append(Oresult['TraineeData']);
                        $('#wregion_id_ta').empty();
                        $('#wregion_id_ta').append(Oresult['RegionData']);
                        $('#workshoptype_id_ta').empty();
                        $('#workshoptype_id_ta').append(Oresult['WTypeData']);
                        $('#trainee_region_id_ta').empty();
                        $('#trainee_region_id_ta').append(Oresult['TraineeRegionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getTraineeWiseData_ta() {
            $('#workshop_id_ta').empty();
            $('#workshop_subtype_ta').empty();
            $('#wsubregion_id_ta').empty();
            var compnay_id = $('#company_id_ta').val();
            if (compnay_id == "") {
                return false;
            }
            var trainee_id = $('#trainee_id_ta').val();
            var workshoptype_id = $('#workshoptype_id_ta').val();
            var workshop_region = $('#wregion_id_ta').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_ta').val(),
                    trainee_id: trainee_id,
                    workshoptype_id: workshoptype_id,
                    region_id: workshop_region
                },
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/ajax_traineewtypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_ta').empty();
                        $('#workshop_id_ta').append(Oresult['WorkshopData']);
                        $('#workshop_subtype_ta').empty();
                        $('#workshop_subtype_ta').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_id_ta').empty();
                        $('#wsubregion_id_ta').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function getWSubTypewiseData_ta() {
            $('#workshop_id_ta').empty();
            var compnay_id = $('#company_id_ta').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshop_subtype_ta').val();
            var workshoptype_id = $('#workshoptype_id_ta').val();
            var region_id = $('#wregion_id_ta').val();
            var subregion_id = $('#wsubregion_id_ta').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_id_ta').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_id_ta').empty();
                        $('#workshop_id_ta').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function ShowChart_ta() {
            var TableMSt = '';
            var trainee_id = $('#trainee_id_ta').val();
            var company_id = $('#company_id_ta').val();
            if (company_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            if (trainee_id == "") {
                ShowAlret("Please select Trainee.!!", 'error');
                return false;
            }
            if ($('#workshop_id_ta').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/ajax_chart_trainee_accuracy/" + TotalChart,
                data: $('#FilterFrm_ta').serialize(),
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(Data) {
                    if (Data != '') {
                        var Oresult = jQuery.parseJSON(Data);
                        var ChartMSt = Oresult['HtmlData'];
                        if (successflag) {
                            TableMSt += '<table class="table table-hover table-light" id="ranktable" width="50%">\n\
                            <thead><tr class="uppercase" style="background-color: #e6f2ff;">\n\
                            <th>Workshop</th><th>Overall Accuracy</th><th>Rank</th><th>Action</th style="width: 15%;"></tr></thead><tbody>';
                            successflag = 0;
                            TableMSt += Oresult['OverallTable'];
                        }
                        $('#ranktable tr:last').after(Oresult['OverallTable']);
                        TableMSt += "</tbody></table>";
                        if (Oresult['Error'] != '') {
                            ShowAlret(Oresult['Error'], 'error');
                        } else {
                            $('#AppendChart_ta').append(ChartMSt);
                            $('#tablecontainer').append(TableMSt);
                            TotalChart++;
                        }
                    }
                    customunBlockUI();
                }
            });
        }

        function RemoveChart(id) {
            $('#ChartDiv_' + id).remove();
            $('#datatr_' + id).remove();
        }

        function getTRegionwiseData() {
            var compnay_id = $('#company_id_ta').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    trainee_region_id: $('#trainee_region_id_ta').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/getTraineeData_ta",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id_ta').empty();
                        $('#trainee_id_ta').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }
    </script>
    <!-- Tab 2 Conetent 3 End here  -->






        <!-- Workshop Reports Js Start Here  -->
        <script>
        // ===================================//* trainee_played_result all function start here 10-04-2023 Nirmal Gajjar *//================================================
        var search = 1;
        var tprReorts = document.tprReorts;
        jQuery(document.tprReorts).ready(function() {
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            if (jQuery().datepicker) {
                $('.date-picker').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy'
                });
            }
            TPR_DatatableRefresh();
        });

        function Set_Tpr_Filter() {
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                TPR_DatatableRefresh();
            }
        }

        function Reset_Tpr_Filter() {
            $('.select2me,.select2_rpt2').select("val", "");
            $('.select2me,.select2_rpt2').val(null).trigger('change');
            document.Filter_Tpr_Frm.reset();
            TPR_DatatableRefresh();
        }

        function TPR_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            var table = $('#index_Tpr_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'width': '30px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '230px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '220px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [9]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [10]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [11]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [12]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '290px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [13]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '290px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [14]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '230px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [15]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [16]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [17]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [18]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [19]
                    }

                ],
                "order": [
                    [0, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/TPR_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'workshoptype_id',
                        value: $('#workshoptype_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_id',
                        value: $('#workshop_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'sessions',
                        value: $('#Tpr_sessions').val()
                    });
                    aoData.push({
                        name: 'topic_id',
                        value: $('#topic_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'subtopic_id',
                        value: $('#subtopic_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'user_id',
                        value: $('#user_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'result_search',
                        value: $('#result_Tpr_search').val()
                    });
                    aoData.push({
                        name: 'trainer_id',
                        value: $('#trainer_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'region_id',
                        value: $('#region_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'tregion_id',
                        value: $('#tregion_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'workshopsubtype_id',
                        value: $('#workshopsubtype_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'subregion_id',
                        value: $('#subregion_Tpr_id').val()
                    });
                    aoData.push({
                        name: 'designation_id',
                        value: $('#designation_Tpr_id').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '180px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '150px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '100px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(10)').css({
                        'min-width': '150px',
                        'max-width': '400px'
                    });
                    $('thead > tr> th:nth-child(11)').css({
                        'min-width': '120px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(12)').css({
                        'min-width': '120px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(13)').css({
                        'min-width': '120px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(14)').css({
                        'min-width': '180px',
                        'max-width': '120px'
                    });
                    $('thead > tr> th:nth-child(15)').css({
                        'min-width': '150px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(16)').css({
                        'min-width': '150px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(17)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(18)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(19)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                }
            });
        }

        function export_Tpr_Confirm() {
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            tprReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function get_Tpr_CompanywiseData() {
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                $('#trainer_Tpr_id').empty();
                $('#user_Tpr_id').empty();
                $('#topic_Tpr_id').empty();
                $('#subtopic_Tpr_id').empty();
                $('#region_Tpr_id').empty();
                $('#tregion_Tpr_id').empty();
                $('#workshop_Tpr_id').empty();
                $('#workshoptype_Tpr_id').empty();
                $('#designation_Tpr_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tpr_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_Tpr_id').empty();
                        $('#user_Tpr_id').append(Oresult['TraineeData']);
                        $('#region_Tpr_id').empty();
                        $('#region_Tpr_id').append(Oresult['RegionData']);
                        $('#workshoptype_Tpr_id').empty();
                        $('#workshoptype_Tpr_id').append(Oresult['WTypeData']);
                        $('#workshop_Tpr_id').empty();
                        $('#workshop_Tpr_id').append(Oresult['WorkshopData']);
                        $('#trainer_Tpr_id').empty();
                        $('#trainer_Tpr_id').append(Oresult['TrainerData']);
                        $('#tregion_Tpr_id').empty();
                        $('#tregion_Tpr_id').append(Oresult['RegionData']);
                        $('#designation_Tpr_id').empty();
                        $('#designation_Tpr_id').append(Oresult['DesignationData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tpr_WSubTypewiseData() {
            $('#topic_Tpr_id').empty();
            $('#subtopic_Tpr_id').empty();
            $('#workshop_Tpr_id').empty();
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshopsubtype_Tpr_id').val();
            var workshoptype_id = $('#workshoptype_Tpr_id').val();
            var region_id = $('#region_Tpr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tpr_id').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Tpr_id').empty();
                        $('#workshop_Tpr_id').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function get_Tpr_WSubRegionwiseData() {
            $('#topic_Tpr_id').empty();
            $('#subtopic_Tpr_id').empty();
            $('#workshop_Tpr_id').empty();
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var subregion_id = $('#subregion_Tpr_id').val();
            var workshopsubtype_id = $('#workshopsubtype_Tpr_id').val();
            var workshoptype_id = $('#workshoptype_Tpr_id').val();
            var region_id = $('#region_Tpr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tpr_id').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Tpr_id').empty();
                        $('#workshop_Tpr_id').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function get_Tpr_WTypewiseData() {
            $('#topic_Tpr_id').empty();
            $('#subtopic_Tpr_id').empty();
            $('#workshop_Tpr_id').empty();
            $('#subregion_Tpr_id').empty();
            $('#workshopsubtype_Tpr_id').empty();
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshopsubtype_Tpr_id').val();
            var workshoptype_id = $('#workshoptype_Tpr_id').val();
            var region_id = $('#region_Tpr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tpr_id').val(),
                    workshoptype_id: workshoptype_id,
                    region_id: region_id,
                    workshopsubtype_id: workshopsubtype_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Tpr_id').empty();
                        $('#workshop_Tpr_id').append(Oresult['WorkshopData']);
                        $('#workshopsubtype_Tpr_id').empty();
                        $('#workshopsubtype_Tpr_id').append(Oresult['WorkshopSubtypeData']);
                        $('#subregion_Tpr_id').empty();
                        $('#subregion_Tpr_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function get_Tpr_WorkshopwiseData() {
            $('#topic_Tpr_id').empty();
            $('#subtopic_Tpr_id').empty();
            $('#trainer_Tpr_id').empty();
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_id = $('#workshop_Tpr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tpr_id').val(),
                    workshop_id: workshop_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#topic_Tpr_id').empty();
                        $('#topic_Tpr_id').append(Oresult['TopicData']);
                        $('#trainer_Tpr_id').empty();
                        $('#trainer_Tpr_id').append(Oresult['TrainerData']);
                        $('#user_Tpr_id').empty();
                        $('#user_Tpr_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tpr_TopicwiseData() {
            var compnay_id = $('#company_Tpr_id').val();
            if (compnay_id == "") {
                return false;
            }
            $('#subtopic_Tpr_id').empty();
            $.ajax({
                type: "POST",
                data: {
                    topic_id: $('#topic_Tpr_id').val(),
                    company_id: $('#company_Tpr_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_topicwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#subtopic_Tpr_id').empty();
                        $('#subtopic_Tpr_id').append(Oresult['SubTopicData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tpr_TrainerwiseData() {
            var trainer_id = $('#trainer_Tpr_id').val();
            var tregion_id = $('#tregion_Tpr_id').val();
            var compnay_id = $('#company_Tpr_id').val();
            var workshoptype_id = $('#workshoptype_Tpr_id').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    trainer_id: trainer_id,
                    tregion_id: tregion_id,
                    workshop_type: workshoptype_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_Tpr_id').empty();
                        $('#user_Tpr_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }
        // ===================================//* trainee_played_result all function End *//================================================

        // ===================================//* trainee_wise_summary all function start here 10-04-2023 Nirmal Gajjar *//================================================
        var twsReorts = document.twsReorts;
        jQuery(document.twsReorts).ready(function() {
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            Tws_DatatableRefresh();
        });

        function Set_Tws_Filter() {
            var compnay_id = $('#company_Tws_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                Tws_DatatableRefresh();
            }
        }

        function Reset_Tws_Filter() {
            $('.select2me,.select2_rpt2').select("val", "");
            $('.select2me,.select2_rpt2').val(null).trigger('change');
            document.Filter_Tws_Frm.reset();
            Tws_DatatableRefresh();
        }

        function get_Tws_CompanywiseData() {
            var compnay_id = $('#company_Tws_id').val();
            if (compnay_id == "") {
                $('#user_Tws_id').empty();
                $('#designation_Tws_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tws_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_Tws_id').empty();
                        $('#user_Tws_id').append(Oresult['TraineeData']);
                        $('#region_Tws_id').empty();
                        $('#region_Tws_id').append(Oresult['RegionData']);
                        $('#workshop_Tws_type').empty();
                        $('#workshop_Tws_type').append(Oresult['WTypeData']);
                        $('#wregion_Tws_id').empty();
                        $('#wregion_Tws_id').append(Oresult['RegionData']);
                        $('#designation_Tws_id').empty();
                        $('#designation_Tws_id').append(Oresult['DesignationData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tws_TregionwiseData() {
            var compnay_id = $('#company_Tws_id').val();
            if (compnay_id == "") {
                return false;
            }
            var region_id = $('#region_Tws_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    region_id: $('#region_Tws_id').val(),
                    workshop_type: $('#workshop_Tws_type').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_Tws_id').empty();
                        $('#user_Tws_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tws_WTypewiseData() {
            $('#wsubregion_Tws_id').empty();
            $('#workshop_Tws_subtype').empty();
            var compnay_id = $('#company_Tws_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshop_Tws_type').val();
            var workshop_region = $('#wregion_Tws_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Tws_subtype').empty();
                        $('#workshop_Tws_subtype').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_Tws_id').empty();
                        $('#wsubregion_Tws_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function Tws_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            //               var compnay_id =$('#company_id').val();
            //                if(compnay_id=="" ){
            //                    return false;
            //                }
            var table = $('#index_Tws_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "No records found",
                    "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    //                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': true,
                        'searchable': false,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [9]
                    }
                ],
                "order": [
                    [8, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Tws_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Tws_id').val()
                    });
                    aoData.push({
                        name: 'user_id',
                        value: $('#user_Tws_id').val()
                    });
                    aoData.push({
                        name: 'range_id',
                        value: $('#range_Tws_id').val()
                    });
                    aoData.push({
                        name: 'region_id',
                        value: $('#region_Tws_id').val()
                    });
                    aoData.push({
                        name: 'workshop_type',
                        value: $('#workshop_Tws_type').val()
                    });
                    aoData.push({
                        name: 'wregion_id',
                        value: $('#wregion_Tws_id').val()
                    });
                    aoData.push({
                        name: 'workshop_session',
                        value: $('#workshop_Tws_session').val()
                    });
                    aoData.push({
                        name: 'wsubregion_id',
                        value: $('#wsubregion_Tws_id').val()
                    });
                    aoData.push({
                        name: 'workshop_subtype',
                        value: $('#workshop_Tws_subtype').val()
                    });
                    aoData.push({
                        name: 'designation_id',
                        value: $('#designation_Tws_id').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '120px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '120px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '100px',
                        'max-width': '120px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                }
            });
        }

        function export_Tws_Confirm() {
            var compnay_id = $('#company_Tws_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            //                var me = $('#company_id').val();
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            //                            console.log(company_id);
                            twsReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }
        // ===================================//* trainee_wise_summary all function End *//================================================

        // ===================================//* traineetopic_wise_report all function start here 10-04-2023 Nirmal Gajjar *//================================================
        var ttqwrReorts = document.ttqwrReorts;
        jQuery(document.ttqwrReorts).ready(function() {
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            Ttqwr_DatatableRefresh();
        });

        function get_Ttqwr_CompanywiseData() {

            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                $('#user_Ttqwr_id').empty();
                $('#workshop_Ttqwr_id').empty();
                $('#wregion_Ttqwr_id').empty();
                $('#tregion_Ttqwr_id').empty();
                $('#designation_Ttqwr_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Ttqwr_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Ttqwr_id').empty();
                        $('#workshop_Ttqwr_id').append(Oresult['WorkshopData']);
                        $('#user_Ttqwr_id').empty();
                        $('#user_Ttqwr_id').append(Oresult['TraineeData']);
                        $('#wregion_Ttqwr_id').empty();
                        $('#wregion_Ttqwr_id').append(Oresult['RegionData']);
                        $('#tregion_Ttqwr_id').empty();
                        $('#tregion_Ttqwr_id').append(Oresult['RegionData'])
                        $('#workshop_Ttqwr_type').empty();
                        $('#workshop_Ttqwr_type').append(Oresult['WTypeData']);
                        $('#designation_Ttqwr_id').empty();
                        $('#designation_Ttqwr_id').append(Oresult['DesignationData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function Reset_Ttqwr_Filter() {
            $('.select2me,.select2_rpt2').select("val", "");
            $('.select2me,.select2_rpt2').val(null).trigger('change');
            document.Filter_Ttqwr_Frm.reset();
            Ttqwr_DatatableRefresh();
        }

        function get_Ttqwr_TregionwiseData() {
            $('#user_Ttqwr_id').empty();
            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var region_id = $('#region_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    region_id: $('#tregion_Ttqwr_id').val(),
                    workshop_type: $('#workshop_Ttqwr_type').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_Ttqwr_id').empty();
                        $('#user_Ttqwr_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Ttqwr_WorkshopwiseData() {
            $('#topic_Ttqwr_id').empty();
            $('#user_Ttqwr_id').empty();
            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_id = $('#workshop_Ttqwr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshop_id: workshop_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#topic_Ttqwr_id').empty();
                        $('#topic_Ttqwr_id').append(Oresult['TopicData']);
                        $('#user_Ttqwr_id').empty();
                        $('#user_Ttqwr_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function ge_Ttqwr_tWTypewiseData() {
            $('#topic_Ttqwr_id').empty();
            $('#workshop_Ttqwr_id').empty();
            $('#wsubregion_Ttqwr_id').empty();
            $('#workshop_Ttqwr_subtype').empty();
            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshop_Ttqwr_type').val();
            var workshop_region = $('#wregion_Ttqwr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Ttqwr_id').empty();
                        $('#workshop_Ttqwr_id').append(Oresult['WorkshopData']);
                        $('#workshop_Ttqwr_subtype').empty();
                        $('#workshop_Ttqwr_subtype').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_Ttqwr_id').empty();
                        $('#wsubregion_Ttqwr_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Ttqwr_WSubTypewiseData() {
            $('#topic_Ttqwr_id').empty();
            $('#workshop_Ttqwr_id').empty();
            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshop_Ttqwr_subtype').val();
            var workshoptype_id = $('#workshop_Ttqwr_type').val();
            var region_id = $('#wregion_Ttqwr_id').val();
            var subregion_id = $('#wsubregion_Ttqwr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Ttqwr_id').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Ttqwr_id').empty();
                        $('#workshop_Ttqwr_id').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function Ttqwr_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            var table = $('#index_Ttqwr_table');
            var report_type = $('#report_Ttqwr_type').val();
            if (report_type == 1) {
                $('#dynamic_col').text("Topic");
            } else if (report_type == 2) {
                $('#dynamic_col').text("Question Set");
            } else {
                $('#dynamic_col').text("NO OF QUESTION SET");
            }
            //                                if(report_type==3){
            //                                     // Get the column API object
            //                                     var column = table.column(5);
            //                                     column.visible(! column.visible());
            //                                }
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "No records found",
                    "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                //"bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "order": [
                    [5, "asc"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    //                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '150px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [9]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [10]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [11]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [12]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [13]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [14]
                    }
                ],

                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Ttqwr_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'user_id',
                        value: $('#user_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_id',
                        value: $('#workshop_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'topic_id',
                        value: $('#topic_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'range_id',
                        value: $('#range_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'wregion_id',
                        value: $('#wregion_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'tregion_id',
                        value: $('#tregion_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_type',
                        value: $('#workshop_Ttqwr_type').val()
                    });
                    aoData.push({
                        name: 'workshop_session',
                        value: $('#workshop_Ttqwr_session').val()
                    });
                    aoData.push({
                        name: 'report_type',
                        value: $('#report_Ttqwr_type').val()
                    });
                    aoData.push({
                        name: 'workshop_session',
                        value: $('#workshop_Ttqwr_session').val()
                    });
                    aoData.push({
                        name: 'report_type',
                        value: $('#report_Ttqwr_type').val()
                    });
                    aoData.push({
                        name: 'wsubregion_id',
                        value: $('#wsubregion_Ttqwr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_subtype',
                        value: $('#workshop_Ttqwr_subtype').val()
                    });
                    aoData.push({
                        name: 'designation_id',
                        value: $('#designation_Ttqwr_id').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);

                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '120px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '120px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '120px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(10)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(11)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(12)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(13)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(14)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                }
            });
        }

        function Set_Ttqwr_Filter() {
            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                Ttqwr_DatatableRefresh();
            }
        }

        function export_Ttqwr_Confirm() {
            var compnay_id = $('#company_Ttqwr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            //                var me = $('#company_id').val();
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            //                            console.log(company_id);
                            ttqwrReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }
        // ===================================//* traineetopic_wise_report all function End *//================================================

        // ===================================//* trainer_wise_summary_report all function Start here 11-04-2023 Nirmal Gajjar *//================================================
        var twrReorts = document.twrReorts;
        jQuery(document.twrReorts).ready(function() {
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            Twr_DatatableRefresh();
        });

        function get_Twr_CompanywiseData() {
            var compnay_id = $('#companycompany_Twr_id_id').val();
            if (compnay_id == "") {
                $('#trainer_Twr_id').empty();
                $('#region_Twr_id').empty();
                $('#workshoptype_Twr_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Twr_id').val()
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#region_Twr_id').empty();
                        $('#region_Twr_id').append(Oresult['RegionData']);
                        $('#workshoptype_Twr_id').empty();
                        $('#workshoptype_Twr_id').append(Oresult['WTypeData']);
                        $('#trainer_Twr_id').empty();
                        $('#trainer_Twr_id').append(Oresult['TrainerData']);

                    }
                    customunBlockUI();
                }
            });
        }

        function Set_Twr_Filter() {
            var compnay_id = $('#company_Twr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                Twr_DatatableRefresh();
            }
        }

        function Reset_Twr_Filter() {
            $('.select2me').select("val", "");
            $('.select2me').val(null).trigger('change');
            document.Filter_Twr_Frm.reset();
            Twr_DatatableRefresh();
        }

        function Twr_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            var table = $('#index_Twr_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "No records found",
                    "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    //                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                    //                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': true,'searchable': true,'targets': [0],"visible": < ?php echo ($Company_id == "" ? 'true' : 'false'); ?>},
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [8]
                    }
                ],
                "order": [
                    [0, "asc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Twr_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Twr_id').val()
                    });
                    aoData.push({
                        name: 'trainer_id',
                        value: $('#trainer_Twr_id').val()
                    });
                    aoData.push({
                        name: 'range_id',
                        value: $('#range_Twr_id').val()
                    });
                    aoData.push({
                        name: 'region_id',
                        value: $('#region_Twr_id').val()
                    });
                    aoData.push({
                        name: 'workshopsubtype_id',
                        value: $('#workshopsubtype_Twr_id').val()
                    });
                    aoData.push({
                        name: 'subregion_id',
                        value: $('#subregion_Twr_id').val()
                    });
                    aoData.push({
                        name: 'workshoptype_id',
                        value: $('#workshoptype_Twr_id').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(10)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                }
            });
        }

        function export_Twr_Confirm() {
            var compnay_id = $('#company_Twr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            twrReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function get_Twr_WTypewiseData() {
            $('#subregion_Twr_id').empty();
            $('#workshopsubtype_Twr_id').empty();
            var compnay_id = $('#company_Twr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshopsubtype_Twr_id').val();
            var workshoptype_id = $('#workshoptype_Twr_id').val();
            var region_id = $('#region_Twr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Twr_id').val(),
                    workshoptype_id: workshoptype_id,
                    region_id: region_id,
                    workshopsubtype_id: workshopsubtype_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshopsubtype_Twr_id').empty();
                        $('#workshopsubtype_Twr_id').append(Oresult['WorkshopSubtypeData']);
                        $('#subregion_Twr_id').empty();
                        $('#subregion_Twr_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }
        // ===================================//* trainer_wise_summary_report all function End *//================================================

        // ===================================//* trainer_consolidated_report_tab all function Start here 11-04-2023 Nirmal Gajjar *//================================================
        var tcrReorts = document.tcrReorts;
        jQuery(document.tcrReorts).ready(function() {
            Tcr_DatatableRefresh();
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            //                getCompanywiseData();
        });

        function Reset_Tcr_Filter() {
            $('.select2me,.select2_rpt2').select("val", "");
            $('.select2me,.select2_rpt2').val(null).trigger('change');
            document.Filter_Tcr_Frm.reset();
            Tcr_DatatableRefresh();
        }

        function Set_Tcr_Filter() {
            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                Tcr_DatatableRefresh();
            }
        }

        function Tcr_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            var table = $('#index_Tcr_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'className': 'dt-head-left dt-body-left',
                        'width': '230px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [9]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [10]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [11]
                    }
                ],
                "order": [
                    [1, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Tcr_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_id',
                        value: $('#workshop_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'wtype_id',
                        value: $('#wtype_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'topic_Tcr_id',
                        value: $('#topic_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'subtopic_id',
                        value: $('#subtopic_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'region_id',
                        value: $('#region_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'trainer_id',
                        value: $('#trainer_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'result_range',
                        value: $('#result_Tcr_range').val()
                    });
                    aoData.push({
                        name: 'wsubregion_id',
                        value: $('#wsubregion_Tcr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_subtype',
                        value: $('#workshop_Tcr_subtype').val()
                    });
                    aoData.push({
                        name: 'session_id',
                        value: $('#session_Tcr_id').val()
                    });
                    //                        aoData.push({name: 'from_range', value: $('#from_range').val()});
                    //                        aoData.push({name: 'to_range', value: $('#to_range').val()});
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '200px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '60px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '200px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(10)').css({
                        'min-width': '200px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(11)').css({
                        'min-width': '200px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(12)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(13)').css({
                        'min-width': '120px',
                        'max-width': '120px'
                    });
                    $('thead > tr> th:nth-child(14)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(15)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(16)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                }
            });
        }

        function export_Tcr_Confirm() {
            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            tcrReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function get_Tcr_CompanywiseData() {

            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                $('#trainer_Tcr_id').empty();
                $('#workshop_Tcr_id').empty();
                $('#region_Tcr_id').empty();
                $('#tregion_id').empty();
                $('#wtype_Tcr_id').empty();
                $('#topic_Tcr_id').empty();
                $('#subtopic_Tcr_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tcr_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainer_Tcr_id').empty();
                        $('#trainer_Tcr_id').append(Oresult['TrainerData']);
                        $('#workshop_Tcr_id').empty();
                        $('#workshop_Tcr_id').append(Oresult['WorkshopData']);
                        $('#region_Tcr_id').empty();
                        $('#region_Tcr_id').append(Oresult['RegionData']);
                        $('#wtype_Tcr_id').empty();
                        $('#wtype_Tcr_id').append(Oresult['WTypeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tcr_WTypewiseData() {
            $('#topic_Tcr_id').empty();
            $('#subtopic_Tcr_id').empty();
            $('#workshop_Tcr_id').empty();
            $('#wsubregion_Tcr_id').empty();
            $('#workshop_Tcr_subtype').empty();
            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#wtype_Tcr_id').val();
            var workshop_region = $('#region_Tcr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Tcr_id').empty();
                        $('#workshop_Tcr_id').append(Oresult['WorkshopData']);
                        $('#workshop_Tcr_subtype').empty();
                        $('#workshop_Tcr_subtype').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_Tcr_id').empty();
                        $('#wsubregion_Tcr_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tcr_WSubTypewiseData() {
            $('#topic_Tcr_id').empty();
            $('#subtopic_Tcr_id').empty();
            $('#workshop_Tcr_id').empty();
            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshop_Tcr_subtype').val();
            var workshoptype_id = $('#wtype_Tcr_id').val();
            var region_id = $('#region_Tcr_id').val();
            var subregion_id = $('#wsubregion_Tcr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Tcr_id').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Tcr_id').empty();
                        $('#workshop_Tcr_id').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        function get_Tcr_WorkshopwiseData() {
            $('#topic_Tcr_id').empty();
            $('#subtopic_Tcr_id').empty();
            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_id = $('#workshop_Tcr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshop_id: workshop_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#topic_Tcr_id').empty();
                        $('#topic_Tcr_id').append(Oresult['TopicData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Tcr_TopicwiseData() {
            $('#subtopic_Tcr_id').empty();
            var compnay_id = $('#company_Tcr_id').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    topic_id: $('#topic_Tcr_id').val(),
                    company_id: $('#company_Tcr_id').val()
                },
                async: false,
                url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/ajax_topicwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        $('#subtopic_Tcr_id').empty();
                        $('#subtopic_Tcr_id').append(msg);
                    }
                    customunBlockUI();
                }
            });
        }
        // ===================================//* trainer_consolidated_report_tab all function End *//================================================

        // ===================================//* workshop_wise_report_tab all function Start here 11-04-2023 Nirmal Gajjar *//================================================

        var wwrReorts = document.wwrReorts;
        jQuery(document.wwrReorts).ready(function() {

            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            Wwr_DatatableRefresh();
        });
        //                getCompanywiseData();
        //            function ResetFilter() {
        //                $('.select2me').select("val","");
        //                $('.select2me').val(null).trigger('change');
        //                document.FilterFrm.reset();
        //                DatatableRefresh();
        //            }
        function Set_Wwr_Filter() {
            var compnay_id = $('#company_Wwr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                Wwr_DatatableRefresh();
            }
        }

        function Reset_Wwr_Filter() {
            $('.select2me,.select2_rpt2').select("val", "");
            $('.select2me,.select2_rpt2').val(null).trigger('change');
            document.Filter_Wwr_Frm.reset();
            Wwr_DatatableRefresh();
        }

        function Wwr_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            var table = $('#index_Wwr_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "No records found",
                    "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    //                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [9]
                    }
                ],
                "order": [
                    [2, "asc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Wwr_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Wwr_id').val()
                    });
                    //                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                    aoData.push({
                        name: 'workshop_id',
                        value: $('#workshop_Wwr_id').val()
                    });
                    aoData.push({
                        name: 'workshoptype_id',
                        value: $('#workshoptype_Wwr_id').val()
                    });
                    aoData.push({
                        name: 'range_id',
                        value: $('#range_Wwr_id').val()
                    });
                    aoData.push({
                        name: 'region_id',
                        value: $('#region_Wwr_id').val()
                    });
                    aoData.push({
                        name: 'wsubregion_id',
                        value: $('#wsubregion_Wwr_id').val()
                    });
                    aoData.push({
                        name: 'workshop_subtype',
                        value: $('#workshop_Wwr_subtype').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '120px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                }
            });
        }

        function export_Wwr_Confirm() {
            var compnay_id = $('#company_Wwr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            wwrReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function get_Wwr_CompanywiseData() {

            var compnay_id = $('#company_Wwr_id').val();
            if (compnay_id == "") {
                $('#workshop_Wwr_id').empty();
                $('#region_Wwr_id').empty();
                $('#workshoptype_Wwr_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Wwr_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Wwr_id').empty();
                        $('#workshop_Wwr_id').append(Oresult['WorkshopData']);
                        $('#region_Wwr_id').empty();
                        $('#region_Wwr_id').append(Oresult['RegionData']);
                        $('#workshoptype_Wwr_id').empty();
                        $('#workshoptype_Wwr_id').append(Oresult['WTypeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Wwr_WTypewiseData() {
            $('#workshop_Wwr_id').empty();
            $('#wsubregion_Wwr_id').empty();
            $('#workshop_Wwr_subtype').empty();
            var compnay_id = $('#company_Wwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshop_type = $('#workshoptype_Wwr_id').val();
            var workshop_region = $('#region_Wwr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    workshoptype_id: workshop_type,
                    region_id: workshop_region
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Wwr_id').empty();
                        $('#workshop_Wwr_id').append(Oresult['WorkshopData']);
                        $('#workshop_Wwr_subtype').empty();
                        $('#workshop_Wwr_subtype').append(Oresult['WorkshopSubtypeData']);
                        $('#wsubregion_Wwr_id').empty();
                        $('#wsubregion_Wwr_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Wwr_WSubTypewiseData() {
            // $('#topic_id').empty();
            $('#workshop_Wwr_id').empty();
            var compnay_id = $('#company_Wwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshop_Wwr_subtype').val();
            var workshoptype_id = $('#workshoptype_Wwr_id').val();
            var region_id = $('#region_Wwr_id').val();
            var subregion_id = $('#wsubregion_Wwr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Wwr_id').val(),
                    region_id: region_id,
                    workshoptype_id: workshoptype_id,
                    workshopsubtype_id: workshopsubtype_id,
                    subregion_id: subregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshop_Wwr_id').empty();
                        $('#workshop_Wwr_id').append(Oresult['WorkshopData']);
                    }
                    customunBlockUI();
                }
            });

        }

        // ===================================//* workshop_wise_report_tab all function End *//================================================

        // ===================================//* question_wise_report_tab all function Start here 11-04-2023 Nirmal Gajjar *//================================================

        var qwrReorts = document.qwrReorts;
        jQuery(document.qwrReorts).ready(function() {
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            Qwr_DatatableRefresh();
            //              getCompanywiseData();
        });

        function Reset_Qwr_Filter() {
            $('.select2me').select("val", "");
            $('.select2me').val(null).trigger('change');
            document.Filter_Qwr_Frm.reset();
            Qwr_DatatableRefresh();
        }

        function Set_Qwr_Filter() {
            var compnay_id = $('#company_Qwr_id').val();
            if (compnay_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            } else {
                Qwr_DatatableRefresh();
            }
        }

        function Qwr_DatatableRefresh() {
            // if (!jQuery().dataTable) {
            //     return;
            // }
            var table = $('#index_Qwr_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [9]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [10]
                    }
                ],
                "order": [
                    [0, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Qwr_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Qwr_id').val()
                    });
                    aoData.push({
                        name: 'region_id',
                        value: $('#region_Qwr_id').val()
                    });
                    aoData.push({
                        name: 'result_range',
                        value: $('#result_Qwr_range').val()
                    });
                    aoData.push({
                        name: 'subregion_id',
                        value: $('#subregion_Qwr_id').val()
                    });
                    aoData.push({
                        name: 'workshoptype_id',
                        value: $('#workshoptype_Qwr_id').val()
                    });
                    aoData.push({
                        name: 'workshopsubtype_id',
                        value: $('#workshopsubtype_Qwr_id').val()
                    });
                    //                        aoData.push({name: 'from_range', value: $('#from_range').val()});
                    //                        aoData.push({name: 'to_range', value: $('#to_range').val()});
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '200px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '150px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '60px',
                        'max-width': '200px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(10)').css({
                        'min-width': '150px',
                        'max-width': '150px'
                    });
                }
            });
        }

        function export_Qwr_Confirm() {
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            qwrReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function get_Qwr_CompanywiseData() {
            var compnay_id = $('#company_Qwr_id').val();
            if (compnay_id == "") {
                $('#region_Qwr_id').empty();
                $('#workshoptype_Qwr_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Qwr_id').val()
                },
                async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#region_Qwr_id').empty();
                        $('#region_Qwr_id').append(Oresult['RegionData']);
                        $('#workshoptype_Qwr_id').empty();
                        $('#workshoptype_Qwr_id').append(Oresult['WTypeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Qwr_WTypewiseData() {
            $('#subregion_Qwr_id').empty();
            $('#workshopsubtype_Qwr_id').empty();
            var compnay_id = $('#company_Qwr_id').val();
            if (compnay_id == "") {
                return false;
            }
            var workshopsubtype_id = $('#workshopsubtype_Qwr_id').val();
            var workshoptype_id = $('#workshoptype_Qwr_id').val();
            var region_id = $('#region_Qwr_id').val();
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Qwr_id').val(),
                    workshoptype_id: workshoptype_id,
                    region_id: region_id,
                    workshopsubtype_id: workshopsubtype_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#workshopsubtype_Qwr_id').empty();
                        $('#workshopsubtype_Qwr_id').append(Oresult['WorkshopSubtypeData']);
                        $('#subregion_Qwr_id').empty();
                        $('#subregion_Qwr_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }

        // ===================================//* question_wise_report_tab all function End *//================================================

        // ===================================//* imei_report_tab all function Start here 11-04-2023 *//================================================
        var dirReorts = document.dirReorts;
        $(document.dirReorts).ready(function() {
            Dir_DatatableRefresh();
        });

        function Dir_DatatableRefresh() {
            var table = $('#index_Dir_table');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "No records found",
                    "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    //                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '50px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '100px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '100px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '100px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [4]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [5]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [6]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [7]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [8]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [9]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [10]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '200px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [11]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '180px',
                        'orderable': false,
                        'searchable': true,
                        'targets': [12]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '250px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [13]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '80px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [14]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '120px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [15]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [16]
                    },
                    {
                        'className': 'dt-head-left dt-body-left',
                        'width': '130px',
                        'orderable': false,
                        'searchable': false,
                        'targets': [17]
                    }
                ],
                "order": [
                    [0, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_trainee_workshop_reports/Dir_DatatableRefresh/'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_Dir_id').val()
                    });
                    aoData.push({
                        name: 'tregion_id',
                        value: $('#tregion_Dir_id').val()
                    });
                    aoData.push({
                        name: 'designation_id',
                        value: $('#designation_Dir_id').val()
                    });
                    aoData.push({
                        name: 'user_id',
                        value: $('#user_Dir_id').val()
                    });
                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {},
                "initComplete": function(settings, json) {
                    $('thead > tr> th:nth-child(1)').css({
                        'min-width': '80px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(2)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(3)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(4)').css({
                        'min-width': '120px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(5)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(6)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(7)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(8)').css({
                        'min-width': '80px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(9)').css({
                        'min-width': '100px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(10)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(11)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(12)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(13)').css({
                        'min-width': '80px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(14)').css({
                        'min-width': '80px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(15)').css({
                        'min-width': '80px',
                        'max-width': '150px'
                    });
                    $('thead > tr> th:nth-child(16)').css({
                        'min-width': '100px',
                        'max-width': '100px'
                    });
                    $('thead > tr> th:nth-child(17)').css({
                        'min-width': '100px',
                        'max-width': '80px'
                    });
                    $('thead > tr> th:nth-child(18)').css({
                        'min-width': '100px',
                        'max-width': '80px'
                    });
                }
            });
        }

        function export_Dir_Confirm() {
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            dirReorts.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function get_Dir_TrainerwiseData() {
            $('#user_Dir_id').empty();
            var tregion_id = $('#tregion_Dir_id').val();
            var compnay_id = $('#company_Dir_id').val();
            if (compnay_id == "") {
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: compnay_id,
                    tregion_id: tregion_id
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#user_Dir_id').empty();
                        $('#user_Dir_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function get_Dir_CompanywiseData() {
            var compnay_id = $('#company_Dir_id').val();
            if (compnay_id == "") {
                $('#tregion_Dir_id').empty();
                $('#user_Dir_id').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {
                    company_id: $('#company_Dir_id').val()
                },
                //async: false,
                url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#tregion_Dir_id').empty();
                        $('#tregion_Dir_id').append(Oresult['RegionData']);
                        $('#user_Dir_id').empty();
                        $('#user_Dir_id').append(Oresult['TraineeData']);
                    }
                    customunBlockUI();
                }
            });
        }

        function Reset_Dir_Filter() {
            $('.select2me,.select2_rpt2').select("val", "");
            $('.select2me,.select2_rpt2').val(null).trigger('change');
            document.Filter_Dir_Frm.reset();
            Dir_DatatableRefresh();
        }
        // ===================================//* imei_report_tab all function End *//================================================
    </script>
        <!-- Workshop Reports Js end Here  -->










    </body>

</html>