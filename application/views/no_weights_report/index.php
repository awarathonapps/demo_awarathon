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
             
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/highcharts/css/highcharts.css" />
        <style>
/*          .tokenize-sample { width: 100%;height:auto }
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
            }            */
        </style>
        <style>
            .divstyle tr {
                background-color: #ffffff;
                border: 1px solid;
            }
            .divstyle  thead tr th{
                color: #000000 !important;
            }
            .divstyle  tbody tr td{
                color: #000000 !important;
            }
            .highcharts-color-0 {
                fill: #06f;
                stroke: #06f;
            }
            .highcharts-color-1 {
                fill: #99ccff;
                stroke: #99ccff;
            }
            .highcharts-color-2 {
                fill: #e5b8b7;
                stroke: #e5b8b7;
            }
            .highcharts-color-3 {
                fill: #953734;
                stroke: #953734;
            }
            .highcharts-color-4 {
                fill: #d6e3bc;
                stroke: #d6e3bc;
            }
            .highcharts-color-5 {
                fill: #76923c;
                stroke: #76923c;
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
                                    <span>No Weights</span>                                    
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
                                    <div class="tab-pane active" id="tab_workshop">
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
                                                                    onchange="getTypeWiseWorkshop();"   >
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
                                                                    <option value="">All</option>
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
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="NoWeightWorkshopTab_datatable()">Search</button>
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
                            <div class="tab-pane" id="tab_ind_trainee">
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
                                                            <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="ind_workshop_id" name="ind_workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getIndWorkshopWiseTrainee();">
                                                                    <option value="">All</option>
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
                                                                    if (isset($TraineeResultSet)) {
                                                                        foreach ($TraineeResultSet as $Trainee) {?>
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
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="NoWeightIndTab_datatable()">Search</button>
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body">                            
                                    <table class="table  table-bordered table-checkable order-column" id="IndTraineedataTable">                                                                    
                                         <thead>
                                            <tr>                                            
                                                <th id="name_head">
                                                    Trainee
                                                </th>                                                    
                                                <th>
                                                   No.Of Question Attempted
                                                </th>                                            
                                            </tr>
                                         </thead>
                                    <tbody>     
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_comparison">
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
                                                                    <option value="">All</option>
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
                                                            <label class="control-label col-md-3">Trainee&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="cmptab_trainee_id" name="cmptab_trainee_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All</option>
                                                                    <?php
                                                                    if (isset($TraineeResultSet)) {
                                                                        foreach ($TraineeResultSet as $Trainee) {?>
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
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="NoWeightCmpTab_data()">Add Set</button>
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ComparisonChart" class="row mt-10"></div>
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
        <script src="<?php echo $asset_url; ?>assets/global/highcharts/highcharts.src.js"></script>        
        <?php if($acces_management->allow_print){ ?>
            <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            var RowCount = 1;
            var table = $('#WdataTable');
            var IndTraineeTable = $('#IndTraineedataTable');
            $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
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
                    async: false,
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
                    async: false,
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
                    data: {company_id: $('#wtab_company_id').val(),workshoptype_id: $('#wtab_wtype_id').val(),
                           region_id: $('#wtab_region_id').val(),subregion_id:$('#wtab_subregion_id').val(),workshopsubtype_id:$('#wtab_workshop_subtype').val()},                    
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
            function getIndTypeWiseWorkshop(){
                if($('#ind_wtype_id').val() ==''){                                                                                
                    $('#ind_workshop_subtype').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#indtab_company_id').val(),workshoptype_id: $('#ind_wtype_id').val(),
                           region_id: $('#ind_region_id').val(),subregion_id:$('#ind_subregion_id').val(),
                           workshopsubtype_id:$('#ind_workshop_subtype').val() },                    
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
                    async: false,
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
                if($('#cmptab_workshop_id').val() ==''){                                        
                    $('#cmptab_trainee_id').empty();
                    return false;
                }
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
                            $('#cmptab_trainee_id').append(Oresult['TraineeData']);                            
                        }
                    customunBlockUI();    
                    }
                });
            }
            function NoWeightWorkshopTab_datatable(){
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
                        {'width': '80px','orderable': false,'searchable': false,'targets': [1]}                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'no_weights_report/getNoWeightWorkshopTableData'; ?>",
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
            function NoWeightIndTab_datatable(){
                if ($('#indtab_company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }
//                if ($('#ind_wtype_id').val() == "") {
//                    ShowAlret("Please select Workshop Type.!!", 'error');
//                    return false;
//                }
                if ($('#ind_workshop_id').val() == "") {
                    ShowAlret("Please select Workshop.!!", 'error');
                    return false;
                }
                IndTraineeTable.dataTable({
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
                        {'width': '80px','orderable': false,'searchable': false,'targets': [1]}                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'no_weights_report/getNoWeightIndTableData'; ?>",
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
            function NoWeightCmpTab_data(){                
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
                if ($('#cmptab_trainee_id').val() == "") {
                    ShowAlret("Please select Trainee.!!", 'error');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#cmptab_company_id').val(),workshop_id: $('#cmptab_workshop_id').val(),wtype_id: $('#cmptab_wtype_id').val(),
                           RowCount:RowCount,trainee_id:$('#cmptab_trainee_id').val(),feedbackset_id :$('#cmp_feedback').val(),
                           cmptab_workshop_subtype:$('#cmptab_workshop_subtype').val(),
                           cmptab_region_id:$('#cmptab_region_id').val(),cmptab_subregion_id:$('#cmptab_subregion_id').val(),cmptab_tregion_id:$('#cmptab_tregion_id').val()},
                    async: false,
                    url: "<?php echo $base_url;?>no_weights_report/ajax_ComparisonData",
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
                                RowCount++ ;
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