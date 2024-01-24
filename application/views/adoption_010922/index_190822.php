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
               <form id="FilterFrm" name="FilterFrm" method="post">
                  <div class="page-bar">
                     <ul class="page-breadcrumb">
                        <li>
                           <a href="#">Dashboard</a>
                           <i class="fa fa-circle"></i>
                        </li>
                        <li>
                           <span>Admin</span>
                           <i class="fa fa-circle"></i>
                           <span>Adoption</span>
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
                  <div class='row' style="display: -webkit-box; overflow-x: auto; overflow-y: none;">
                     <div class='col-md-5'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Monthly Reps Mapped
                              <a data-title="Monthly Reps Mapped">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="reps_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="map_user" name="map_user" value="t_yearly" />
                           </div>
                        </div>
                        <div id='map_users'></div>
                        <br>
                     </div>
                     <div class='col-md-5'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Module Started
                              <a style="font-family:'Catamaran';" data-title="Number of Modules started in a time period">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>

                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="monthly_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="assessment_report" name="assessment_report" value="t_yearly" />
                           </div>
                        </div>
                        <div id='assessment_started'></div>
                        <br>
                     </div>

                     <div class='col-md-5'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran';  position: absolute;top: 22px; left: 24px;">Module Completed
                              <a data-title="Number of Modules completed in a time period">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="monthly_end_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="assessment_report_ended" name="assessment_report_ended" value="t_yearly" />
                           </div>
                        </div>
                        <div id='assessment_complted'></div>
                        <br>

                     </div>

                  </div>
                  <br>
                  <!-- Video Uploaded Graph -->
                  <div class="row" style="display: -webkit-box; overflow-x: auto;">
                  <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">No.of Reps Completed & Played
                              <a data-title="No.of Reps Completed & Played">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 4px;">
                              <div id="Unplayed_Played_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                           </div>
                        </div>
                        <div id='Unplayed_Played'></div>
                     </div>
                     

                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Total Videos Processed
                              <a data-title="Total videos processed">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="processed_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="Tvp" name="Tvp" value="t_yearly" />
                           </div>
                        </div>
                        <div id='total_videos_processed'></div>
                        <br>

                     </div>
                  </div>
                  <br>
                  <div class="row" style="display: -webkit-box; overflow-x: auto;">
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Total Report Sent
                              <a data-title="Total Report Sent">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="Total_Report_Sent_Picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="Tvu" name="Tvu" value="t_yearly" />
                           </div>
                        </div>
                        <div id='total_reports_sent'></div>
                        <br>
                     </div>

                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Total Videos Uploaded
                              <a data-title="Total videos uploaded">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="uploaded_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="Tvu" name="Tvu" value="t_yearly" />
                           </div>
                        </div>
                        <div id='total_videos_uploaded'></div>
                        <br>

                     </div>
                     

                  </div>
                  <br>

                  <!-- End Here -->

                  <!-- Adoption By Team -->
                  <div class="row" style="display: -webkit-box; overflow-x: auto;">
                     <!-- <div class="col-lg-8 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">AIR Users
                              <a data-title="Monthly Reps Mapped">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;">
                              <div id="total_users" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Monthly
                              </div>
                              <input type="hidden" id="AIR" name="AIR" value="t_yearly" />
                           </div>
                        </div> -->
                     <!-- <div id='AIR_Users'></div> -->
                     <!-- </div> -->
                     <!-- Adoption By Team -->
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by Team
                              <a data-title="Graph which shows %completion & %Start of candidates by manager">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-adoption">
                                 Filter</a>
                           </div>
                        </div>
                        <div id='adoption_by_team'></div>
                        <br>
                     </div>
                     <!--Adoption by Division -->
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by Division
                              <a data-title="Graph which shows %completion & %Start of candidates by Division">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-division">
                                 Filter</a>
                           </div>
                        </div>
                        <div id='adoption_by_division'></div>
                        <br>
                     </div>
                     <!-- Adoption By Division -->

                  </div>
                  <br>

                 <!-- Adoption By Module Nd Region  -->
                  <div class="row" style="display: -webkit-box; overflow-x: auto;">
                     
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by Modules
                              <a data-title="Graph which shows %completion & %Start of candidates by modules">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-module">
                                 Filter</a>
                           </div>
                        </div>
                        <div id='adoption_by_module'></div>
                       
                     </div>
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by Region
                              <a data-title="Graph which shows %completion & %Start of candidates by region">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-region">
                                 Filter</a>
                           </div>
                        </div>
                        <div id='adoption_by_region'></div>
                        <br>
                     </div>

                  </div>
                  <!-- Adoption By Module Nd Region  End Here  -->
                  <br>
                  <!-- End Here -->
               </form>
            </div>
         </div>
      </div>
   </div>
   <!-- SETTINGS BOX -->

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
   <!--Adoption By Team Filter Model  -->
   <div id="responsive-modal-adoption" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <select id="process_assessment_id" name="process_assessment_id" class="form-control input-sm select2" placeholder="Please select">
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
                           <label>Manager</label>
                           <select id="status_id_manager" name="status_id_manager[]" class="form-control input-sm select2" placeholder="Please select" multiple="" onchange="lenfind()">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="Adoption_by_team()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->
   <!-- Adoption By Modules -->
   <div id="responsive-modal-module" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <select id="new_assessment_id" name="new_assessment_id[]" class="form-control input-sm select2" placeholder="Please select" min="2" max="5" multiple="" onchange="lencountass()">
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
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="Adoption_by_module()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->
   <!-- Adoption By Division -->
   <div id="responsive-modal-division" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Division</label>
                           <select id="division_id" name="division_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="" min="1" max="5">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="Adoption_by_division()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->
   <!-- Adoption By Region -->
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
                           <select id="region_id" name="region_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="Adoption_by_region()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->
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
                              <?php foreach (range(1, 12) as $month) :
                                 $monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
                                 $fdate = date("F", strtotime(date('Y') . "-$monthPadding-01"));
                                 echo '<option value="' . $monthPadding . '" ' . ($monthPadding == date('m') ? 'selected' : '') . '>' . $fdate . '</option>';
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
                           <select id="year" name="year" class="form-control input-sm select2" placeholder="Please select">
                              <option value="<?php echo date('Y') ?>"><?php echo date('Y') ?></option>
                              <option value="<?php echo '2021' ?>"><?php echo '2021' ?></option>
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
      var step = 0;
      //            var owl =$("#region_data");
   </script>
   <script src="<?php echo $asset_url; ?>assets/customjs/adoption.js"></script>
   <script>

      $("#process_assessment_id").change(function() {
         fetch_assessment_id();
      });
      $("#assessment_id").change(function() {
         GetAssessmentWiseDivision();
      });
      $("#am_id").change(function(){
         Getassessmentregion();
      });

      function lenfind() {
         var idlen = $("#status_id_manager").val();
         var lencount = idlen.length;
         if (lencount > "5") {
            ShowAlret("Please select Only Five Managers .!!", 'error');
            return false;
         }
      }  
      function lencountass(){
         var idlen = $("#new_assessment_id").val();
         var lencount = idlen.length;
         if (lencount == "1") {
            ShowAlret("Must Be Select Two Modules .!!", 'error');
            return false;
         }
         else if (lencount > "5") {
            ShowAlret("Please select Only Five Modules .!!", 'error');
            return false;
         }
        
      }

      function fetch_assessment_id() {
         var assessment_id = $('#process_assessment_id').val();
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
            url: "<?php echo $base_url; ?>adoption/assessment_wise_manager",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#status_id_manager').empty();
                  $('#status_id_manager').append(Oresult['assessment_list_data']);
               }
               customunBlockUI();
            }
         });
      }

      function GetAssessmentWiseDivision() {
         var assessment_id = $('#assessment_id').val();
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
            url: "<?php echo $base_url; ?>adoption/assessment_division",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#division_id').empty();
                  $('#division_id').append(Oresult['division']);
               }
               customunBlockUI();
            }
         });
      }

      function Getassessmentregion() {
         var assessment_id = $('#am_id').val();
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
            url: "<?php echo $base_url; ?>adoption/assessment_wise_region",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#region_id').empty();
                  $('#region_id').append(Oresult['region']);
               }
               customunBlockUI();
            }
         });
      }

      function ResetFilter() {
         $('.select2me,.select2_rpt2').select("val", "");
         $('.select2me,.select2_rpt2').val(null).trigger('change');
         document.frmReorts_manager.reset();
      }
      jQuery(document).ready(function() {

         $(".select2_rpt").select2({
            // placeholder: 'Please Select',
            width: '100%'
         });
         $(".select2me").select2({
            placeholder: 'All',
            width: '100%',
            allowClear: true
         });
         getWeek();

         // filter Box 1
         if (!jQuery().daterangepicker) {
            return;
         }
         if (jQuery().datepicker) {
            $('.date-picker').datepicker({
               rtl: App.isRTL(),
               orientation: "left",
               autoclose: true,
               format: 'dd-mm-yyyy'
            });
         }

         $('#monthly_picker').daterangepicker({

               "ranges": {
                  'Current Year': [moment().subtract('days', 6), moment()],
                  'Last 7 Days': [moment().subtract('days', 7), moment()],
                  'Last 30 Days': [moment().subtract('days', 29), moment()],
                  'Last 60 Days': [moment().subtract('days', 59), moment()],
                  'Last 90 Days': [moment().subtract('days', 89), moment()],
                  'Last 365 Days': [moment().subtract('days', 365), moment()]
               },
               "autoApply": true,
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
               "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
               "endDate": moment().format("DD/MM/YYYY"),
               opens: (App.isRTL() ? 'right' : 'left'),
            },
            function(start, end, label) {
               sessionStorage.setItem("IsCustom", label);
            });
         if ($('#monthly_picker').attr('data-display-range') != '0') {
            var thisYear = (new Date()).getFullYear();
            var thisMonth = (new Date()).getMonth() + 1;
            var start = new Date(thisMonth + "/1/" + thisYear);

         }
         $('#monthly_picker').show();
         $('#monthly_picker').on('apply.daterangepicker', function(ev, picker) {
            $('#date_lable').text(picker.chosenLabel);
            StartDate = picker.startDate.format('DD-MM-YYYY');
            EndDate = picker.endDate.format('DD-MM-YYYY');

            let IsCustom = sessionStorage.getItem("IsCustom");


            assessment_started(IsCustom);

         });
         assessment_started();
      });
      //Addded

      // filter box 2
      $('#monthly_end_picker').daterangepicker({
            "ranges": {
               'Current Year': [moment().subtract('days', 300), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 60), moment()],
               'Last 90 Days': [moment().subtract('days', 90), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 300).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            if ($('#monthly_end_picker').attr('data-display-range') != '0') {}
            sessionStorage.setItem("IsCustom", label);

         });
      if ($('#monthly_end_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);
      }
      $('#monthly_end_picker').show();
      $('#monthly_end_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");

         assessment_complted(IsCustom);
      });
      assessment_complted();



      // Raps Mapped Users 
      $('#reps_picker').daterangepicker({

            "ranges": {
               'Current Year': [moment().subtract('days', 6), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 59), moment()],
               'Last 90 Days': [moment().subtract('days', 89), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);
         });
      if ($('#reps_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);

      }
      $('#reps_picker').show();
      $('#reps_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");

         raps_mapped_user(IsCustom);
      });
      raps_mapped_user();

      //total_videos_uploaded
      $('#uploaded_picker').daterangepicker({

            "ranges": {
               'Current Year': [moment().subtract('days', 6), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 59), moment()],
               'Last 90 Days': [moment().subtract('days', 89), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);
         });
      if ($('#uploaded_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);

      }
      $('#uploaded_picker').show();
      $('#uploaded_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");

         total_video_uploded(IsCustom);

      });

      total_video_uploded();
      //End Here

      //total_videos_processed
      $('#processed_picker').daterangepicker({

            "ranges": {
               'Current Year': [moment().subtract('days', 6), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 59), moment()],
               'Last 90 Days': [moment().subtract('days', 89), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);
         });
      if ($('#processed_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);

      }
      $('#processed_picker').show();
      $('#processed_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");

         total_video_processed(IsCustom);

      });

      total_video_processed();
      //End Here

      // Total User Active Inactive 
      $('#total_users').daterangepicker({

            "ranges": {
               'Current Year': [moment().subtract('days', 6), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 59), moment()],
               'Last 90 Days': [moment().subtract('days', 89), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);
         });
      if ($('#total_users').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);

      }
      $('#total_users').show();
      $('#total_users').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");

         total_users_Ac_In(IsCustom);

      });

      total_users_Ac_In();

      // No of Raps played and Completed
      $('#Unplayed_Played_picker').daterangepicker({

            "ranges": {
               'Current Year': [moment().subtract('days', 6), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 59), moment()],
               'Last 90 Days': [moment().subtract('days', 89), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);
         });
      if ($('#Unplayed_Played_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);

      }
      $('#Unplayed_Played_picker').show();
      $('#Unplayed_Played_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         RapsPlayedCompleted(IsCustom);

      });
      RapsPlayedCompleted();

      // Total_Report Sent Picker
      $('#Total_Report_Sent_Picker').daterangepicker({

            "ranges": {
               'Current Year': [moment().subtract('days', 6), moment()],
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'Last 60 Days': [moment().subtract('days', 59), moment()],
               'Last 90 Days': [moment().subtract('days', 89), moment()],
               'Last 365 Days': [moment().subtract('days', 365), moment()]
            },
            "autoApply": true,
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
            "startDate": moment().subtract('days', 6).format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            opens: (App.isRTL() ? 'right' : 'left'),
         },
         function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);
         });
      if ($('#Total_Report_Sent_Picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);

      }
      $('#Total_Report_Sent_Picker').show();
      $('#Total_Report_Sent_Picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         Total_Report_Sent(IsCustom);

      });
      Total_Report_Sent();

      //End All


      function getAssessmentwiseData() {
         var compnay_id = $('#company_id').val();
         if (compnay_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: compnay_id,
               userid_id: $('#supervisor_id').val(),
               startDate: StartDate
            },
            //async: false,
            url: "<?php echo $base_url; ?>adoption/ajax_assessmentwise_data",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);

                  $('#assessment_id1').empty();
                  $('#assessment_id1').append(Oresult['assessment_list_data']);
               }
               customunBlockUI();
            }
         });
      }
   </script>
   <style>image{display:none}</style>
</body>

</html>