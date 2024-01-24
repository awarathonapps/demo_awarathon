<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> 
<html lang="en" class="ie8 no-js">
   <![endif]-->
<!--[if IE 9]> 
   <html lang="en" class="ie9 no-js">
      <![endif]-->
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
         /* float: left; */
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
         /*color: #f1592a !important;*/
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

      /* Assessment List Css */
      .table-scrollable>.table>tbody>tr>th,
      .table-scrollable>.table>tfoot>tr>td,
      .table-scrollable>.table>tfoot>tr>th,
      .table-scrollable>.table>thead>tr>th {
         white-space: normal;
      }

      .select2-results__option--highlighted[aria-selected] {
         background-color: #d9d9d9 !important;
         color: #fff !important;

      }

      .opt-green {
         color: #004369;
      }

      .opt-green:hover {
         background-color: #d9d9d9;
         color: #004369;
      }

      .opt-red {
         color: #db1f48;
      }

      .opt-red:hover {
         background-color: #d9d9d9;
         color: #db1f48;
      }

      /* Assessment List Css */

      /* Competency graphs img style start here */
      .img-style {
         height: 60%;
         width: 50%;
         text-align: center;
         margin: auto;
         margin-left: 23%;
         margin-top: 2%;
      }

      .head-text {
         font-family: 'Catamaran';
         font-size: 16px;
         font-weight: 600;
         line-height: 24px;
         color: #2A2E36;
         text-transform: inherit;
         margin-bottom: 8px;
         text-align: center;
      }

      .sub-head {
         font-family: 'Catamaran';
         font-size: 12px;
         font-weight: 400;
         line-height: 16px;
         color: #2A2E36;
         text-transform: inherit;
         text-align: center;
      }

      #color-bar {
         margin-top: 3%;
         margin-left: 61.5%;
      }

      .color-boxes {
         padding: 8px;
         font-size: 13px
      }

      .myFont {
         font-size: 14px;
      }

      /* End here */
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
               <!-- <form id="rangeform">
                  <div class="theme-panel hidden-xs hidden-sm">
                     <div class="toggler">
                     </div>
                     <div class="toggler-close">
                     </div>
                     <div class="theme-options">
                        <div class="theme-option theme-colors clearfix">
                           <span>THRESHOLD COLOR</span>
                        </div>
                        <?php
                        if (count((array)$ThresholdData) > 0) {
                           $slot_row = 1;
                           foreach ($ThresholdData as $rng) {
                        ?>
                              <div class="theme-option">
                                 <span style="float: left;background-color: <?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>">&nbsp;
                                 </span>
                                 <div class="col-md-4"><input class=" form-control input-sm " id="range_from<?php echo $slot_row; ?>" name="range_from[]" placeholder="" type="text" value="<?php echo ($rng->range_from != '' ? $rng->range_from : ''); ?>"></div>
                                 <div class="col-md-4"><input class="form-control input-sm " id="range_to<?php echo $slot_row; ?>" name="range_to[]" placeholder="" type="text" value="<?php echo ($rng->range_to != '' ? $rng->range_to : ''); ?>"></div>
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
                        if (count((array)$ResultData) > 0) {
                           $result_row = 1;
                           foreach ($ResultData as $rng) {
                        ?>
                              <div class="theme-option">
                                 <span style="float: left;background-color: <?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>"><?php echo $rng->assessment_status; ?>
                                 </span>
                                 <?php if ($rng->range_from == 0 && $rng->range_to == 0) { ?>
                                    <div class="col-md-4"><input class="form-control input-sm " id="result_from<?php echo $result_row; ?>" name="result_from[]" placeholder="" type="text" value="0" readonly></div>
                                    <div class="col-md-4"><input class="form-control input-sm " id="result_to<?php echo $result_row; ?>" name="result_to[]" placeholder="" type="text" value="0" readonly></div>
                                 <?php } else { ?>
                                    <div class="col-md-4"><input class="form-control input-sm " id="result_from<?php echo $result_row; ?>" name="result_from[]" placeholder="" type="text" value="<?php echo ($rng->range_from != '' ? $rng->range_from : ''); ?>"></div>
                                    <div class="col-md-4"><input class="form-control input-sm " id="result_to<?php echo $result_row; ?>" name="result_to[]" placeholder="" type="text" value="<?php echo ($rng->range_to != '' ? $rng->range_to : ''); ?>"></div>
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
               </form> -->
               <!-- <form id="FilterFrm" name="FilterFrm" method="post"> -->
               <div class="page-bar">
                  <ul class="page-breadcrumb">
                     <li>
                        <a href="#">Dashboard</a>
                        <i class="fa fa-circle"></i>
                     </li>
                     <li>
                        <span>Admin</span>
                        <i class="fa fa-circle"></i>
                        <span>Competency</span>
                     </li>
                  </ul>
                  <div class="col-md-1 page-breadcrumb"></div>
               </div>
               <!-- PAGE BAR -->

               <!-- <div class="row">
                     <div class="col-lg-12 col-xs-12 col-sm-12">
                        <div class="panel-group accordion" id="accordion3">
                           <div class="panel panel-default">
                              <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2" aria-expanded="false">
                                       Filter Data </a>
                                 </h4>
                              </div>
                              <div id="collapse_3_2" class="panel-collapse collapse" aria-expanded="false">
                                 <div class="panel-body">
                                    <div class="row margin-bottom-10"> -->
               <!--Added below 2 div class-->
               <!-- <div class="col-md-4">
                                                   <div class="form-group">
                                                       <label class="control-label">Supervisor&nbsp; </label>
                                                           <select id="supervisor_id" name="supervisor_id" class="form-control input-sm select2me" style="width: 100%" onchange="getAssessmentwiseData()" >
                                                               <option value="">Please Select</option>
                                                               <?php foreach ($TrainerResult as $trainer) { ?>
                                                               <option value="<?= $trainer->userid; ?>"><?php echo $trainer->fullname; ?></option>
                                                               <?php } ?>
                                                           </select>
                                                   </div>
                                                   </div>                                                             
                                                   <div class="col-md-4">
                                                   <div class="form-group">
                                                       <label class="control-label">Assessment&nbsp;<span class="required"> </label>
                                                           <select id="assessment_id1" name="assessment_id1" class="form-control input-sm select2me" style="width: 100%">
                                                               <option value="">Select Value</option>
                                                               <?php
                                                               //if (isset($assessment_list_data)) {
                                                               foreach ($assessment_list_data as $at) { ?>
                                                               <option value="<?= $at->id; ?>"><?php echo $at->assessment; ?></option>
                                                               <?php } ?>
                                                           </select>
                                                   </div>
                                                   </div>      -->
               <!-- <div class="col-md-4">
                                          <div class="form-group">
                                             <label class="control-label">Report by&nbsp;<span class="required"> * </span></label>
                                             <select id="report_by" name="report_by" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%">
                                                <option value="">Please Select</option>-->
               <!-- <option value="0">Assessment</option>
                                                <option value="1">Parameter</option>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-md-4">
                                          <div class="form-group">
                                             <label class="control-label">Report Type&nbsp;<span class="required"> * </span></label>
                                             <select id="report_type" name="report_type" class="form-control input-sm select2_rpt" placeholder="Please Select">
                                                <option value=""></option>
                                                <?php foreach ($report_type as $rt) { ?>
                                                   <option value="<?= $rt->id; ?>" <?php echo ($rt->default_selected ? 'selected' : ''); ?>><?php echo $rt->description; ?></option>
                                                <?php } ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-md-4">
                                          <div class="form-group">
                                             <label class="control-label">Trainee Region&nbsp;</label>
                                             <select id="region_id" name="region_id" class="form-control input-sm select2me" style="width: 100%">
                                                <option value="">All Region</option>
                                                <?php foreach ($region_data as $rg) { ?>
                                                   <option value="<?= $rg->region_id; ?>"><?php echo $rg->region_name; ?></option>
                                                <?php } ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-md-4">
                                          <div class="form-group">
                                             <label class="control-label">Store wise/Vertical wise&nbsp;</label>
                                             <select id="store_id" name="store_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                <option value="">All Store</option>
                                                <?php foreach ($store_data as $st) { ?>
                                                   <option value="<?= $st->store_id; ?>"><?php echo $st->store_name; ?></option>
                                                <?php } ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-md-1">
                                          <div class="text-right" style="margin-top: 20px;">
                                             <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh()">Search</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div> -->
               <div class="clearfix margin-top-20"></div>

               <div class="row">
                  <!-- INDEX CHART -->
               </div>
               <!-- New Module -->
               <style>
                  .btn.btn-outline.blue-oleo.active {
                     background-color: #004369;
                  }

                  .portlet>.portlet-title {
                     border-bottom: none;
                  }

                  .portlet.light.bordered>.portlet-title {
                     border-bottom: none;
                  }

                  .title-bar {
                     background: white;
                     padding: 0px 0px 47px 8px;
                     font-size: 17px;
                     position: static;
                  }

                  .header-right {
                     float: right;
                  }
               </style>
               <!-- Performance comparison by module start here -->
               <div class='row' style="display: -webkit-box; overflow-x: auto; overflow-y: none;">

                  <div class='col-md-5'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency by module
                           <a data-title="Comparison of module based on overall score">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-comparison">
                              Filter
                           </a>
                        </div>
                     </div>
                     <div id='performance_comparison_graph'></div>
                     <br>
                  </div>

                  <!--competency by divsion (Overall)-->
                  <div class='col-md-5'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency by Division (Overall)
                           <a data-title="Division level competency comparison ">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#competency_by_division_model">
                              Filter
                           </a>
                        </div>
                     </div>
                     <div id='competency_by_division'></div>
                     <br>
                  </div>
                  <!-- competency by divsion (Overall)-->

                  <!--competency by Region -->
                     <div class='col-md-5'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency by region (Overall)
                              <a data-title="Region level competency comparison">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#competency_by_region_model">
                                 Filter
                              </a>
                           </div>
                        </div>
                        <div id='competency_by_region'></div>
                        <br>
                     </div>
                  <br>
                  <!--End Here-->

                  <!-- 14-02-2023 "Nirmal" -->
                  <!-- <div class='col-md-5'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency by divisions
                           <a data-title="Comparison of division based on overall score">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-dvision">
                              Filter
                           </a>
                        </div>
                     </div>
                     <div id='performance_comparison_by_division'></div>
                     <br>
                  </div> -->

                  <!-- <div class='col-md-5'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency by region
                           <a data-title="Comparison of region based on overall score">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-region">
                              Filter
                           </a>
                        </div>
                     </div>
                     <div id='performance_comparison_by_region'></div>
                     <br>
                  </div> -->
                  <!-- 14-02-2023 "Nirmal" -->

               </div>
               <br />
               <!-- End Here -->
               <!-- Start Here-->
               <div class='row'>
                  <div class='col-md-6'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency categorization
                           <a data-title="Competency categorization based on rep final scores">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-accuracy">
                              Filter
                           </a>
                        </div>
                     </div>
                     <div id='competency_understanding_graph'></div>
                     <br>
                  </div>

                  <!-- Region wise Performance -->
                  <div class="col-md-6">
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Region wise performance as per the industry thresholds
                           <a data-title="Count of reps for each region based on industry thresholds">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-region-performance">
                              Filter</a>
                        </div>
                     </div>
                     <div id='region_performance'></div>
                     <br>
                  </div>
                  <!-- Region wise Performance -->

               </div>
               <br>
               <div class='row'>

                  <!-- Competency By Manager -->
                  <div class='col-md-6'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency by manager (Overall)
                           <a data-title=" Manager level competency comparison">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-manager">
                              Filter
                           </a>
                        </div>
                     </div>
                     <input id="c_m_iscustom" type="hidden" value="" name="c_m_iscustom">
                     <div id='competency_by_manager'></div>
                     <br>
                  </div>
                  <!-- End here -->
               </div>
               <br>

               <!-- Rockstars Reps scored more than 85% start here -->
               <div class='row'>
                  <div class='col-md-12'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 14px;">Rockstars (Reps scored more than 85%)
                           <a data-title="Rockstars (Reps scored more than 85%)">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>

                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-rockstar-user">
                              Filter
                           </a>

                        </div>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 58px; margin-right: -57px;">
                           <a id="export_button" class="btn orange btn-sm btn-outline" style="margin-right: 10px;" onclick="export_rockstar_users()">
                              <i class="fa fa-file-excel-o"></i> Export</a>
                           </a>
                        </div>
                        <!-- Table start here -->
                        <div style="width:99%;padding-top: 115px;font-size: 12px;">
                           <form id="frmUsers" name="frmUsers" method="post" action="<?php echo base_url() . 'competency/export_rockstar_users' ?>">
                              <input id="ammt_id" type="hidden" value="" name="ammt_id">
                              <input id="data_count" type="hidden" value="" name="data_count">
                              <div class="portlet-body">
                                 <table class="table table-bordered table-hover table-checkable order-column no-footer dataTable" id="index_table" style="border: none;">
                                    <thead>
                                       <tr>
                                          <th>E code</th>
                                          <th>Employee Name</th>
                                          <th>Division</th>
                                          <th>Ai Score</th>
                                          <th>Assessor Rating %</th>
                                          <th>Final Score</th>
                                       </tr>
                                    </thead>
                                    <tbody></tbody>
                                 </table>
                              </div>

                           </form>
                        </div>
                        <!-- Table end here -->

                     </div>

                  </div>
               </div>
               <br>
               <!-- end here -->
               <div class='row'>
                  <div class='col-md-12'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 14px;">At risk (Reps who scored less than 25%)
                           <a data-title="At risk (Reps who scored less than 25%)">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>

                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-user-score-atrisk">
                              Filter
                           </a>

                        </div>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 58px; margin-right: -57px;">

                           <a id="export_button" class="btn orange btn-sm btn-outline" style="margin-right: 10px;" onclick="export_at_risk_users()">
                              <i class="fa fa-file-excel-o"></i> Export</a>
                           </a>
                        </div>
                        <!-- Table start here -->
                        <div style="width:99%;padding-top: 115px;font-size: 12px;">
                           <form id="frmUsersAt_risk" name="frmUsersAt_risk" method="post" action="<?php echo base_url() . 'competency/export_at_risk_user' ?>">
                              <input id="AssessmentsId" type="hidden" value="" name="AssessmentsId">
                              <input id="atriskData_count" type="hidden" value="" name="atriskData_count">
                              <div class="portlet-body">
                                 <table class="table table-bordered table-hover table-checkable order-column no-footer dataTable" id="index_table_at_risk" style="border: none;">
                                    <thead>
                                       <tr>
                                          <th>E code</th>
                                          <th>Employee Name</th>
                                          <th>Division</th>
                                          <th>Ai Score</th>
                                          <th>Assessor Rating %</th>
                                          <th>Final Score</th>
                                       </tr>
                                    </thead>
                                    <tbody></tbody>
                                 </table>
                              </div>
                           </form>
                        </div>
                        <!-- Table end here -->

                     </div>

                  </div>
               </div>
               <!-- </form> -->
               <br>
               <div class='row'>
                  <!--  Top Five region based overall score -->
                  <div class='col-md-6'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Top Five region based on overall scores
                           <a data-title="Top Five region based on overall scores">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div style="width:99%;padding-top: 60px;font-size: 12px;">
                           <div class="portlet-body">
                              <table class="table table-bordered table-hover table-checkable order-column no-footer dataTable" id="top_five_region">
                                 <thead>
                                    <tr>
                                       <th>Region Name</th>
                                       <th style="text-align:center;">Competency</th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                              </table>
                           </div>
                           <br>
                        </div>
                     </div>
                  </div>
                  <!--  Top Five region based overall score -->


                  <!-- Bottom Five region based overall score -->
                  <div class="col-md-6">
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Bottom Five region based on overall scores
                           <a data-title="Bottom Five region based on overall scores">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>
                        <div style="width:99%;padding-top: 60px;font-size: 12px;">
                           <div class="portlet-body">
                              <table class="table table-bordered table-hover table-checkable order-column no-footer dataTable" id="bottom_five_region">
                                 <thead>
                                    <tr>
                                       <th>Region Name</th>
                                       <th style="text-align:center;">Competency</th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                              </table>
                           </div>
                           <br>
                        </div>
                     </div>
                  </div>
                  <!-- Bottom Five region based overall score -->
                  <br>
               </div>
               <br>
               <br>
               <!-- Manager wise understanding -->
               <div class='row'>
                  <div class='col-md-12'>
                     <div class='title-bar'>
                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 14px;">Manager wise Understanding
                           <a data-title="Manager wise Understanding">
                              <i class="icon-info font-black sub-title"></i>
                           </a>
                        </span>

                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                           <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#manager_understanding_modal">
                              Filter
                           </a>
                        </div>
                        <span class="caption-subject font-dark" style="font-family:'Catamaran';position: absolute;top: 63px;">
                           <p id="manager_wise_understanding" name="manager_wise_understanding" style="font-family:'Catamaran'; font-size: 12px;"></p>
                        </span>
                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 58px; margin-right: -57px;">

                           <a id="export_button" class="btn orange btn-sm btn-outline" style="margin-right: 14px;margin-bottom:9px;" onclick="export_manager_data()">
                              <i class="fa fa-file-excel-o"></i> Export</a>
                           </a>
                        </div>
                        <div style="width:99%;padding-top: 58px;font-size: 12px;">
                           <form id="manager_understanding" name="manager_understanding" method="post" action="<?php echo base_url() . 'competency/export_manager_data' ?>">
                              <input id="ass_id" type="hidden" value="" name="ass_id">
                              <input id="startdate" type="hidden" value="" name="startdate">
                              <input id="enddate" type="hidden" value="" name="enddate">
                              <input id="iscustom" type="hidden" value="" name="iscustom">
                              <input id="managerid" type='hidden' value="" name="managerid">
                              <input id="manager_wise_count" type="hidden" value="" name="manager_wise_count">

                              <div class="portlet-body">
                                 <table class="table table-bordered table-hover table-checkable order-column no-footer dataTable" id="manager_understaning_table" style="border: none;">
                                    <thead>
                                       <tr>
                                          <th>Manager ID</th>
                                          <th>Manager Name</th>
                                          <th>No. of Reps</th>
                                          <th>Completion Progress</th>
                                          <th>Team Avg. Accuracy</th>
                                       </tr>
                                    </thead>
                                    <tbody></tbody>
                                 </table>
                              </div>
                           </form>
                        </div>
                        <!-- Table end here -->
                     </div>
                  </div>
               </div>
               <!-- Manager wise Understanding end -->
               <br>
               <!-- Hitmap Start Here -->
               <div class='row'>
                  <div class="col-lg-12 col-xs-12 col-sm-12">
                     <div class="portlet light bordered">
                        <div class="portlet-title potrait-title-mar">
                           <div class="caption">
                              <i class="icon-bar-chart font-dark hide"></i>
                              <span class="caption-subject font-dark bold ">Heatmap</span>
                           </div>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#hitmap_modal">
                                 Filter
                              </a>
                           </div>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 58px; margin-right: -57px;">
                              <a id="image_button" class="btn orange btn-sm btn-outline" style="margin-right: 4px; margin-bottom: -13px;" onclick="heatmapToimage()">
                                 <i class="fa fa-file-excel-o"></i> Download</a>
                              </a>
                              <a id="export_button" class="btn orange btn-sm btn-outline" style="margin-right: 4px; margin-bottom: -13px;" onclick="export_heat_map_data()">
                                 <i class="fa fa-file-excel-o"></i> Export</a>
                              </a>
                           </div>
                           <div class="header-right" id="color-bar">
                              <span style=" background:#a6f7c5" class="color-boxes">Score 75% ></span>
                              <span style=" background:#fff27d" class="color-boxes">Score 74 to 65%</span>
                              <span style=" background:#fab2af" class="color-boxes">Score 64 to 55%</span>
                              <span style=" background:#ff8288" class="color-boxes">Score < 54% </span>
                           </div>
                        </div>
                        <div class="portlet-body">
                           <form id="heat_map" name="heat_map" method="post">
                              <input id="hit_custom" type="hidden" value="" name="iscustom">
                              <input id="hit_map_count" type="hidden" value="" name="hit_map_count">

                              <div class="row" id="region_table"></div>

                           </form>

                        </div>
                     </div>
                  </div>
               </div>
               <br>
               <!-- Hitmap End Here -->

            </div>
         </div>
      </div>
   </div>
   <br>
   <!-- Hitmap setting box -->
   <div id="hitmap_modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="hitmap_assessment_id" name="hitmap_assessment_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Region</label>
                           <select id="hitmap_region" name="hitmap_region[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Region</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Select Time</label>
                           <input class="form-control input-sm" id="hitmap_picker" value="" name="hitmap_picker" readonly>
                        </div>
                     </div>
                  </div>

               </div>

               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="check_region()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Heatmap setting box -->

   <!-- Manager_understanding Modal start Here -->
   <div id="manager_understanding_modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="manager_assessment_id" name="manager_assessment_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">Select</option>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Manager <span style=" color:red;color: red;font-size: 23px;position: absolute;top: -4px;left: 66px;">*</span></label>
                           <select id="all_managers" name="all_managers[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>


                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Select Time</label>
                           <input class="form-control input-sm" id="manager_wise_understanding_picker" value="" name="manager_wise_understanding_picker" readonly>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="manager_wise_understanding()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Manager_understanding Modal End Here -->
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
   <!-- Competency Understanding Graph Filter Model  -->
   <div id="responsive-modal-accuracy" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="process_assessment_id" name="process_assessment_id[]" class="form-control input-sm select2" placeholder="Please select" multiple=''>
                              <option value="">Select</option>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
                              <?php
                                 }
                              }
                              ?>
                           </select>
                        </div>
                     </div>
                  </div>


               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="check_module()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->

   <!-- Performance comparison by division  -->
   <div id="responsive-modal-dvision" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="assessment_id" name="assessment_id" class="form-control input-sm select2" placeholder="Please select">
                              <option value="">Select</option>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?><?php echo ',' . $adata->report_type; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Division</label>
                           <select id="division_id" name="division_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Division</option>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="lenfind2()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->
   <!-- Responsive region wise performace -->
   <div id="responsive-modal-region" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="am_id" name="am_id" class="form-control input-sm select2" placeholder="Please select">
                              <option value="">Select</option>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?><?php echo ',' . $adata->report_type; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Region</label>
                           <select id="region_id" name="region_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option>All Region</option>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="check_regionid()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->

   <!--Performance comparison by module Filter Model  -->
   <div id="responsive-modal-comparison" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <!-- <select id="assessments_id" name="assessments_id[]" class="form-control input-sm select2" placeholder="Please select" multiple='' onchange="lenfind();"> -->
                           <select id="assessments_id" name="assessments_id[]" class="form-control input-sm select2" placeholder="Please select" multiple=''>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>

                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
                              <?php
                                 }
                              }
                              ?>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="performance_comparison()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here  -->

   <div id="responsive-region-performance" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="as_id" name="as_id[]" class="form-control input-sm select2" placeholder="Please select" multiple=''>
                              <option value="">Select</option>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Region</label>
                           <select id="rg_id" name="rg_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Region</option>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="count_region()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Competency by division -->
   <div id="competency_by_division_model" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="new_ass_id" name="new_ass_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Manager </label>
                           <select id="man_id" name="man_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value=""> All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Division<span style="color:red;color: red;font-size: 23px;position: absolute;top: -4px;left: 66px;">*</span></label>
                           <select id="div_id" name="div_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option> Select</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Select Time</label>
                           <input type="hidden" name="is_custom_by_div" id="is_custom_by_div">
                           <input class="form-control input-sm" id="competency_by_division_picker" value="" name="competency_by_division_picker" readonly>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="check_div()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- end -->
   <!-- Competency by Region -->
   <div id="competency_by_region_model" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="ass_gp_id" name="ass_gp_id" class="form-control input-sm select2" placeholder="Please select" multiple=''>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Manager </label>
                           <select id="man_gp_id" name="man_gp_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Region<span style=" color:red;color: red;font-size: 23px;position: absolute;top: -4px;left: 55px;">*</span></label>
                           <select id="reg_gp_id" name="reg_gp_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Select Time</label>
                           <input type="hidden" name="is_custom_by_reg" id="is_custom_by_reg">
                           <input class="form-control input-sm" id="competency_by_region_picker" value="" name="competency_by_division_picker" readonly>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="check_gp_region()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- end -->

   <!-- Reps who scored more than 85% -->
   <div id="responsive-modal-rockstar-user" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="amt_id" name="amt_id[]" class="form-control input-sm select2" placeholder="Please select" multiple=''>
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
                              <?php
                                 }
                              }
                              ?>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="DatatableRefresh_Rockstars()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!--Reps who scored more than 85% end here  -->

   <!-- Reps who scored less than 25 % -->
   <div id="responsive-modal-user-score-atrisk" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="AssessmentId" name="AssessmentId[]" class="form-control input-sm select2" multiple='' placeholder="Please select">
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
                              <?php
                                 }
                              }
                              ?>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="DatatableRefresh_Atrisk()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Reps who scored less than 25 % end here -->

   <!-- Competency by manager -->
   <div id="responsive-modal-manager" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules</label>
                           <select id="c_m_am_id" name="c_m_am_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <?php
                              if (isset($assessment)) {
                                 foreach ($assessment as $adata) {
                              ?>
                                    <option value="<?php echo  $adata->assessment_id; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
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
                           <label>Manager <span style=" color:red;color: red;font-size: 23px;position: absolute;top: -4px;left: 66px;">*</span></label>
                           <select id="c_m_managers" name="c_m_managers[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Select Time</label>
                           <input class="form-control input-sm" id="c_m_time" value="" name="c_m_time" readonly>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="check_manager_id()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End here -->
   <div class="modal fade" id="user_data_modal" role="basic" aria-hidden="true" data-width="400" style="height:fit-content">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-body">
               <span> &nbsp;&nbsp;Loading... </span>
            </div>
         </div>
      </div>
   </div>
   <div class="page-footer">
      <div class="page-footer-inner"> <?php echo $this->setting_value->copyright; ?></div>
      <div class="scroll-to-top">
         <i class="icon-arrow-up"></i>
      </div>
   </div>

   <?php $this->load->view('inc/inc_footer_script'); ?>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/highcharts/highstock.js"></script>

   <!-- Graph Export js -->
   <script src='<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js'></script>
   <script src='<?php echo $asset_url; ?>assets/global/highcharts/modules/export-data.js'></script>
   <!-- End Here -->

   <!-- <script src="< ?php echo $asset_url;?>assets/global/highcharts/highcharts.js"></script>-->
   <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
   <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
   <!-- <script src="< ?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js"></script> -->

   <script>
      var firsttimeload = 1;
      var Company_id = '<?php echo $company_id ?>';
      var StartDate = "<?php echo $start_date; ?>";
      var EndDate = "<?php echo $end_date; ?>";
      var base_url = "<?php echo $base_url; ?>";
      var quarter = moment().quarter();
      var year = moment().year();
      var step = 0;
   </script>
   <script src="<?php echo $asset_url; ?>assets/customjs/competency.js"></script>
   <!-- For download heat map as image  -->
   <script src="<?php echo $asset_url; ?>assets/customjs/html2canvas.min.js"></script>
   <script>
      Competency_understanding_graph();
      // performance_division();
      competency_by_division();
      competency_by_region();
      // performance_region();
      performance_comparison();
      region_wise_performance();
      DatatableRefresh_Rockstars();
      DatatableRefresh_Atrisk();
      get_top_five_region();
      get_bottom_five_region();
      manager_wise_understanding();
      GetAssessmentWiseManager();
      getdashboardData();
      Getheatmapregion();

      // For search functionlity in all graphs 
      // Devleloped by Nirmal
      //  For Competency by module
      $("#assessments_id").select2({
         dropdownParent: $('#responsive-modal-comparison'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
         // dropdownCssClass: "myFont"
      });
      // 

      // 14-02-2023 "Nirmal"
      // For Competency by divisions
      // $("#assessment_id").select2({
      //    dropdownParent: $('#responsive-modal-dvision'),
      //    placeholder: 'Please select',
      //    width: '100%',
      //    allowClear: true,
      //    // dropdownCssClass: "myFont"
      // });

      // $("#division_id").select2({
      //    dropdownParent: $('#responsive-modal-dvision'),
      //    placeholder: 'Please select',
      //    width: '100%',
      //    allowClear: true,
      //    // dropdownCssClass: "myFont"
      // });
      // 
      // 14-02-2023 "Nirmal"


      // 14-02-2023 "Nirmal"
      // For Competency by region 
      // $("#am_id").select2({
      //    dropdownParent: $('#responsive-modal-region'),
      //    placeholder: 'Please select',
      //    width: '100%',
      //    allowClear: true,
      //    // dropdownCssClass: "myFont"
      // });

      // $("#region_id").select2({
      //    dropdownParent: $('#responsive-modal-region'),
      //    placeholder: 'Please select',
      //    width: '100%',
      //    allowClear: true,
      //    // dropdownCssClass: "myFont"
      // });
      //
      // 14-02-2023 "Nirmal"

      //  For Competency categorization
      function check_module() {
         var idlen = $("#process_assessment_id").val();
         if (idlen == null) {
            ShowAlret("Please select Modules .!!", 'error');
            return false;
         } else {
            Competency_understanding_graph();
         }
      }
      $("#process_assessment_id").select2({
         dropdownParent: $('#responsive-modal-accuracy'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
         // dropdownCssClass: "myFont"
      });
      // For Region wise performance as per the industry thresholds
      $("#as_id").select2({
         dropdownParent: $('#responsive-region-performance'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });

      $("#rg_id").select2({
         dropdownParent: $('#responsive-region-performance'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      // 

      // For Competency by Division (Overall) 
      $("#new_ass_id").select2({
         dropdownParent: $('#competency_by_division_model'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#man_id").select2({
         dropdownParent: $('#competency_by_division_model'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#div_id").select2({
         dropdownParent: $('#competency_by_division_model'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      // 

      // For Competency by manager (Overall) 
      $("#c_m_am_id").select2({
         dropdownParent: $('#responsive-modal-manager'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#c_m_managers").select2({
         dropdownParent: $('#responsive-modal-manager'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      // 

      // Competency by region (Overall)
      $("#ass_gp_id").select2({
         dropdownParent: $('#competency_by_region_model'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#man_gp_id").select2({
         dropdownParent: $('#competency_by_region_model'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#reg_gp_id").select2({
         dropdownParent: $('#competency_by_region_model'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      // 

      // For Rockstars (Reps scored more than 85%)
      $("#amt_id").select2({
         dropdownParent: $('#responsive-modal-rockstar-user'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      //

      // For At risk (Reps who scored less than 25%)
      $("#AssessmentId").select2({
         dropdownParent: $('#responsive-modal-user-score-atrisk'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });

      // For Manager wise Understanding 
      $("#manager_assessment_id").select2({
         dropdownParent: $('#manager_understanding_modal'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#all_managers").select2({
         dropdownParent: $('#manager_understanding_modal'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      // 

      // For Heatmap
      $("#hitmap_assessment_id").select2({
         dropdownParent: $('#hitmap_modal'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      $("#hitmap_region").select2({
         dropdownParent: $('#hitmap_modal'),
         placeholder: 'Please select',
         width: '100%',
         allowClear: true,
      });
      // End here




      $("#manager_assessment_id").change(function() {
         GetAssessmentWiseManager();
      });



      // function lenfind() {
      //    var idlen = $("#assessments_id").val();
      //    var lencount = idlen.length;
      //    if (lencount > "5") {
      //       ShowAlret("Please select Only Five Modules .!!", 'error');
      //       return false;
      //    }
      // }
      // 14-02-2023 "Nirmal"
      //for performance comparison by division
      // function lenfind2() {
      //    var idlen = $("#division_id").val();
      //    if (idlen == null) {
      //       ShowAlret("Please select divisions .!!", 'error');
      //       return false;
      //    }
      //    var lencount = idlen.length;
      //    if (lencount > "5") {
      //       ShowAlret("Please select Only Five divisions .!!", 'error');
      //       return false;
      //    } else {
      //       performance_division();
      //    }
      // }

      // 14-02-2023 "Nirmal"

      function count_region() {
         var AssessmentId = $("#as_id").val();
         var rg_id = $("#rg_id").val();
         if (AssessmentId == null) {
            ShowAlret("Please select modules .!!", 'error');
            return false;
         }
         if (rg_id == null) {
            ShowAlret("Please select region .!!", 'error');
            return false;
         } else {
            region_wise_performance();
         }
      }
      // 14-02-2023 "Nirmal"
      // GetAssessmentWiseDivision();
      // $("#assessment_id").change(function() {
      //    GetAssessmentWiseDivision();
      // });

      // function GetAssessmentWiseDivision() {
      //    var AssessmentId = $('#assessment_id').val();
      //    if (Company_id == "") {
      //       return false;
      //    }
      //    var myArray = AssessmentId.split(",");
      //    var assessment_id = myArray[0];
      //    var Report_Type = myArray[1];
      //    $.ajax({
      //       type: "POST",
      //       data: {
      //          company_id: Company_id,
      //          assessmentid: assessment_id
      //       },
      //       //async: false,
      //       url: "< ?php echo $base_url; ?>competency/assessment_wise_division",
      //       beforeSend: function() {
      //          customBlockUI();
      //       },
      //       success: function(msg) {
      //          if (msg != '') {
      //             var Oresult = jQuery.parseJSON(msg);
      //             $('#division_id').empty();
      //             $('#division_id').append(Oresult['division']);
      //          }
      //          customunBlockUI();
      //       }
      //    });
      // }
      // competency_by_division
      // 14-02-2023 "Nirmal"
      function check_div() {
         var div_id = $('#div_id').val();
         if (div_id == null) {
            ShowAlret("Please select Division .!!", 'error');
            return false;
         } else {
            competency_by_division();
         }
      }

      function load_division_time_based(IsCustom) {
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               StartDate: StartDate,
               EndDate: EndDate,
               IsCustom: IsCustom
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/load_division_time_based",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#div_id').empty();
                  $('#div_id').append(Oresult['division']);
               }
               customunBlockUI();
            }
         });
      }

      load_division();
      $("#new_ass_id").change(function() {
         load_division();
      });
      $("#man_id").change(function() {
         load_division();
      });

      function load_division() {
         var AssessmentId = $('#new_ass_id').val();
         var manager_id = $("#man_id").val();
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               assessmentid: AssessmentId,
               manager_id: manager_id
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/competency_by_division_filter",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#div_id').empty();
                  $('#div_id').append(Oresult['division']);
                  $('#man_id').empty();
                  $('#man_id').append(Oresult['manager']);
                  $('#man_id').val(Oresult['manager_select']);

               }
               customunBlockUI();
            }
         });
      }
      // Competency_by_division end
      // Competency_by_region started
      function check_gp_region() {
         var region_id = $('#reg_gp_id').val();
         if (region_id == null) {
            ShowAlret("Please select Region .!!", 'error');
            return false;
         } else {
            competency_by_region();
         }
      }

      function load_region_time_based(IsCustom) {
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               StartDate: StartDate,
               EndDate: EndDate,
               IsCustom: IsCustom
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/load_region_time_based",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#reg_gp_id').empty();
                  $('#reg_gp_id').append(Oresult['region']);
               }
               customunBlockUI();
            }
         });
      }
      load_region_filter();
      $("#ass_gp_id").change(function() {
         load_region_filter();
      });
      $("#man_gp_id").change(function() {
         load_region_filter();
      });

      function load_region_filter() {
         var AssessmentId = $('#ass_gp_id').val();
         var manager_id = $('#man_gp_id').val();
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               assessmentid: AssessmentId,
               manager_id: manager_id
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/competency_by_region_filter",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#reg_gp_id').empty();
                  $('#reg_gp_id').append(Oresult['region']);
                  $('#man_gp_id').empty();
                  $('#man_gp_id').append(Oresult['manager']);
                  $('#man_gp_id').val(Oresult['manager_select']);
               }
               customunBlockUI();
            }
         });
      }

      $('#competency_by_region_picker').daterangepicker({
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
      if ($('#competency_by_region_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);
      }
      $('#competency_by_region_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         var assessmnet_id = $('#ass_gp_id').val();
         var manager_id = $('#man_gp_id').val();
         var reg_id = $('#reg_gp_id').val();
         $('#is_custom_by_reg').val(IsCustom);
         if (assessmnet_id == null && manager_id == null && reg_id == null) {
            load_region_time_based(IsCustom);
         }
      });
      // Competency_by_region end


      // 14-02-2023 "Nirmal"
      // Perfromance understanding by region 
      // Getassessmentregion();
      // $("#am_id").change(function() {
      //    Getassessmentregion();
      // });

      // function check_regionid() {
      //    var region_id = $('#region_id').val();
      //    if (region_id == null) {
      //       ShowAlret("Please select Region.!!", 'error');
      //       return false;
      //    } else {
      //       performance_region();
      //    }
      // }

      // function Getassessmentregion() {
      //    var AssessmentId = $('#am_id').val();
      //    if (Company_id == "") {
      //       return false;
      //    }
      //    $.ajax({
      //       type: "POST",
      //       data: {
      //          company_id: Company_id,
      //          assessmentid: AssessmentId
      //       },
      //       //async: false,
      //       url: "< ?php echo $base_url; ?>competency/assessment_wise_region",
      //       beforeSend: function() {
      //          customBlockUI();
      //       },
      //       success: function(msg) {
      //          if (msg != '') {
      //             var Oresult = jQuery.parseJSON(msg);
      //             $('#region_id').empty();
      //             $('#region_id').append(Oresult['region']);
      //          }
      //          customunBlockUI();
      //       }
      //    });
      // }
      // 14-02-2023 "Nirmal"

      $('#as_id').change(function() {
         Getassessment_region();
      });

      function Getassessment_region() {
         var AssessmentId = $('#as_id').val();

         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               assessmentid: AssessmentId
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/am_wise_region",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  if (assessment_id != null) {
                     $('#rg_id').empty();
                     $('#rg_id').append(Oresult['rg_id']);
                  } else {
                     $('#rg_id').empty();
                  }
               }
               customunBlockUI();
            }
         });
      }

      // Competency_by_division graph 
      $('#competency_by_division_picker').daterangepicker({
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
      if ($('#competency_by_division_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);
      }
      $('#competency_by_division_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         var ass_id = $('#new_ass_id').val();
         var manager_id = $('#man_id').val();
         var div_id = $('#div_id').val();
         $('#is_custom_by_div').val(IsCustom);
         if (ass_id == null && manager_id == null && div_id == null) {
            load_division_time_based(IsCustom);
         }
      });
      // Competency_by_division end
      // reps who  scored more than 85%
      function DatatableRefresh_Rockstars() {
         var table = $('#index_table');
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
                  'searchable': false,
                  'targets': [3]
               },
               {
                  'className': 'dt-head-left dt-body-left',
                  'width': '80px',
                  'orderable': true,
                  'searchable': false,
                  'targets': [4]
               },
               {
                  'className': 'dt-head-left dt-body-left',
                  'width': '120px',
                  'orderable': true,
                  'searchable': false,
                  'targets': [5]
               },
            ],
            "order": [
               [5, "desc"]
            ],
            "processing": true,
            // "serverSide": true,
            "serverSide": false,
            "sAjaxSource": "<?php echo base_url() . 'competency/get_rockstars_user_score'; ?>",
            "fnServerData": function(sSource, aoData, fnCallback) {
               aoData.push({
                  name: 'assessment_id',
                  value: $('#amt_id').val()
               });
               $.getJSON(sSource, aoData, function(json) {

                  fnCallback(json);
                  // customunBlockUI();

                  // $('#Title').text(json.title);
                  $('#data_count').val(json.iTotalRecords);
                  if ($('#amt_id').val() != "") {
                     $('#responsive-modal-rockstar-user').modal('hide');
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
      //end here

      // reps who scored less than 25%
      function DatatableRefresh_Atrisk() {
         var table = $('#index_table_at_risk');
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

            "bStateSave": false,
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
                  'searchable': false,
                  'targets': [3]
               },
               {
                  'className': 'dt-head-left dt-body-left',
                  'width': '80px',
                  'orderable': true,
                  'searchable': false,
                  'targets': [4]
               },
               {
                  'className': 'dt-head-left dt-body-left',
                  'width': '120px',
                  'orderable': true,
                  'searchable': false,
                  'targets': [5]
               },
            ],
            "order": [
               [5, "desc"]
            ],

            "processing": true,
            // "serverSide": true,
            "serverSide": false,

            "sAjaxSource": "<?php echo base_url() . 'competency/get_at_risk_user_score'; ?>",
            "fnServerData": function(sSource, aoData, fnCallback) {
               aoData.push({
                  name: 'assessment_id',
                  value: $('#AssessmentId').val()
               });
               $.getJSON(sSource, aoData, function(json) {

                  fnCallback(json);

                  $('#atriskData_count').val(json.iTotalRecords);
                  if ($('#AssessmentId').val() != "") {
                     $('#responsive-modal-user-score-atrisk').modal('hide');
                  }
               });
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
               return nRow;
            },
            "fnFooterCallback": function(nRow, aData) {},
            "initComplete": function(settings, json) {
               $('thead > tr> th:nth-child(1)').css({
                  'min-width': '100px',
                  'max-width': '100px',
                  'background': 'none',
               });
            }
         });

      }

      function export_rockstar_users() {
         var check_data = $('#data_count').val();

         if (check_data == 0) {
            ShowAlret("No Data Found.!!", 'error');
            return false;
         }

         var compnay_id = Company_id;
         var assessment_id = $('#amt_id').val();
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
                     $('#ammt_id').val(assessment_id);
                     frmUsers.submit();
                  }
               },
               cancel: function() {
                  this.onClose();
               }
            }
         });
      }

      function export_at_risk_users() {
         var check_data = $('#atriskData_count').val();
         if (check_data == 0) {
            ShowAlret("No Data Found.!!", 'error');
            return false;
         }

         var compnay_id = Company_id;
         var assessment_id = $('#AssessmentId').val();
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
                     $('#AssessmentsId').val(assessment_id);
                     frmUsersAt_risk.submit();
                  }
               },
               cancel: function() {
                  this.onClose();
               }
            }
         });
      }
      // Manager_wise_understanding graph
      function GetAssessmentWiseManager(manager_set) {
         var assessment_id = $('#manager_assessment_id').val();
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               assessmentid: assessment_id
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/assessment_wise_manager",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#all_managers').empty();
                  $('#all_managers').append(Oresult['assessment_list_data']);

               }
               // if (assessment_id != '') {
               //    $('#all_managers').val(manager_set);
               //    $('#all_managers').trigger('change');
               // }
               customunBlockUI();
            }
         });
      }

      function manager_wise_understanding(IsCustom) {
         var manager_assessment_id = $("#manager_assessment_id").val();
         var manager_id = $("#all_managers").val();
         if (manager_assessment_id != null && manager_id == null) {
            ShowAlret("Please select Manager..!!", 'error');
            return false;
         }
         var table = $('#manager_understaning_table');
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
            "pageLength": 20,
            "paging": true,
            "pagingType": "bootstrap_full_number",
            "columnDefs": [{
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
                  'orderable': false,
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
                  'orderable': true,
                  'searchable': true,
                  'targets': [4]
               },
            ],
            "order": [
               [4, "desc"]
            ],
            "processing": true,
            // "serverSide": true,
            "serverSide": false,
            "sAjaxSource": "<?php echo base_url() . 'competency/get_manager_data'; ?>",
            "fnServerData": function(sSource, aoData, fnCallback) {
               aoData.push({
                  name: 'assessment_id',
                  value: $("#manager_assessment_id").val()
               });
               aoData.push({
                  name: 'StartDate',
                  value: StartDate
               });
               aoData.push({
                  name: 'EndDate',
                  value: EndDate
               });
               aoData.push({
                  name: 'IsCustom',
                  value: $('#iscustom').val()
               });
               aoData.push({
                  name: 'all_managers',
                  value: $("#all_managers").val()
               });
               $.getJSON(sSource, aoData, function(json) {
                  fnCallback(json);
                  $('#manager_wise_count').val(json.iTotalRecords);
                  $('#manager_understanding_modal').modal('hide');
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

      function export_manager_data() {
         var compnay_id = Company_id;
         var manager_id = $('#all_managers').val();
         var ass_id = $('#manager_assessment_id').val();
         var check_data = $('#manager_wise_count').val();
         if (check_data == 0) {
            ShowAlret("No data found.!!", 'error');
            return false;
         }
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
                     $('#startdate').val(StartDate);
                     $('#enddate').val(EndDate);
                     $('#iscustom').val();
                     $('#managerid').val(manager_id);
                     $("#ass_id").val(ass_id);
                     manager_understanding.submit();
                  }
               },
               cancel: function() {
                  this.onClose();
               }
            }
         });
      }

      $('#manager_wise_understanding_picker').daterangepicker({
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
      if ($('#manager_wise_understanding_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);


      }
      $('#manager_wise_understanding_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         $('#iscustom').val(IsCustom);
      });
      // Manager_wise_understanding graph end here
      // Hitmap Start Here
      $('#hitmap_picker').daterangepicker({
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
      if ($('#hitmap_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);


      }
      $('#hitmap_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');
         let IsCustom = sessionStorage.getItem("IsCustom");
         $('#hit_custom').val(IsCustom);
      });


      function check_region() {
         var region_id = $('#hitmap_region').val();
         if (region_id == null) {
            ShowAlret("Please select Region.!!", 'error');
            return false;
         } else {
            getdashboardData();
         }
      }

      function getdashboardData() {
         var assessment_id = $('#hitmap_assessment_id').val();
         var region_heatmap = $('#hitmap_region').val();

         if (assessment_id != null && region_heatmap == null) {
            ShowAlret("Please select Region.!!", 'error');
            return false;
         }
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               assessmentid: assessment_id,
               region_id: region_heatmap,
               StartDate: StartDate,
               EndDate: EndDate,
               iscustom: $('#hit_custom').val()
            },
            //async: false,
            url: "<?php echo $base_url; ?>competency/get_heatmap_data",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  var count = Oresult.module_count;
                  $('#hit_map_count').val(count);
                  // if (count <= 20) {
                  //    document.getElementById('image_button').style.visibility = 'visible';
                  // } else {
                  //    document.getElementById('image_button').style.visibility = 'hidden';

                  // }
                  $('#region_table').html(Oresult['regiontable_graph']);
                  $('#hitmap_modal').modal('hide');
               }
               customunBlockUI();
            }
         });
      }


      // export heat map to excel
      function export_heat_map_data() {
         var h_count = $('#hit_map_count').val();
         if (h_count == 0) {
            ShowAlret("No data found please select module.!!", 'error');
            return false;
         }
         if (Company_id == "") {
            ShowAlret("Please select Company first.!!", 'error');
            return false;
         }
         $.confirm({
            title: 'Confirm!',
            content: "Are you sure want to Export Heat Map. ? ",
            buttons: {
               confirm: {
                  text: 'Confirm',
                  btnClass: 'btn-primary',
                  keys: ['enter', 'shift'],
                  action: function() {
                     exportToExcel();
                  }
               },
               cancel: function() {
                  this.onClose();
               }
            }
         });
      }

      function exportToExcel() {
         var h_count = $('#hit_map_count').val();
         if (h_count == "") {
            ShowAlret("No data found please select module.!!", 'error');
            return false;
         }
         var htmls = "";
         var uri = 'data:application/vnd.ms-excel;base64,';
         var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
         var base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
         };
         var format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
               return c[p];
            })
         };
         htmls = document.getElementById("region_table").innerHTML;

         var ctx = {
            worksheet: 'Worksheet',
            table: htmls
         }
         const date = new Date();
         var link = document.createElement("a");
         link.download = "Heat Map " + date + ".xls";
         link.href = uri + base64(format(template, ctx));
         link.click();
      }

      function heatmapToimage() {
         var h_count = $('#hit_map_count').val();
         if (h_count == 0) {
            ShowAlret("No data found please select module.!!", 'error');
            return false;
         }
         if (Company_id == "") {
            ShowAlret("Please select Company first.!!", 'error');
            return false;
         }

         $.confirm({
            title: 'Confirm!',
            content: "Are you sure want to download Heat Map. ? ",

            buttons: {
               confirm: {
                  text: 'Confirm',
                  btnClass: 'btn-primary',
                  keys: ['enter', 'shift'],

                  action: function() {
                     $('.btn-primary').hide();
                     download_png();
                     customBlockUI();
                  },
               },
               cancel: function() {
                  $('.btn-primary').show();
                  this.onClose();

               }
            }
         });
      }
      // 
      function download_png() {
         $('.btn-primary').hide();
         customBlockUI();
         const date = new Date();
         const screenshotTarget = document.getElementById('region_table');
         html2canvas(screenshotTarget).then((canvas) => {
            const base64image = canvas.toDataURL("image/png");
            var anchor = document.createElement('a');
            anchor.setAttribute("href", base64image);
            anchor.setAttribute("download", "Heat Map " + date + ".png");
            anchor.click();
            anchor.remove();
            customunBlockUI();
            $('.btn-primary').show();

            // setTimeout(function(){
            // $.unblockUI();
            // }, 2500);
         });

      }
      // Hitmap End Here

      //  competency by manager start here
      $('#c_m_time').daterangepicker({
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
      }, function(start, end, label) {
         sessionStorage.setItem("IsCustom", label);
      });
      if ($('#c_m_time').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);


      }
      $('#c_m_time').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         $('#c_m_iscustom').val(IsCustom);

         var a_id = $('#c_m_am_id').val();
         var m_id = $('#c_m_managers').val();

         if (m_id == null && a_id == null) {
            get_time_wise_manager(IsCustom);
         }

      });

      competency_by_manager();

      // End here
   </script>
   <style>
      image {
         display: none
      }
   </style>
</body>

</html>