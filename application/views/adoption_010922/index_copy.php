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
               </form>
               <form id="FilterFrm" name="FilterFrm" method="post">
                  <div class="page-bar">
                     <ul class="page-breadcrumb">
                        <li>
                           <a href="#">Dashboard</a>
                           <i class="fa fa-circle"></i>
                        </li>
                        <li>
                           <span>Admin</span>
                        </li>
                     </ul>
                     <div class="col-md-1 page-breadcrumb"></div>
                     <div class="page-toolbar">
                        <div id="dashboard-report-range" name="daterange" class="pull-right btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                           <i class="icon-calendar"></i>&nbsp;
                           <span class="thin uppercase hidden-xs"></span>&nbsp;
                           <i class="fa fa-angle-down"></i>
                        </div>
                     </div>
                  </div>
                  <!-- PAGE BAR -->
                  <h1 class="page-title">
                     Go Live Admin Dashboard <small>reports &amp; statistics</small>
                  </h1>
                  <div class="clearfix margin-top-10"></div>
                  <div class="row">
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
                                    <div class="row margin-bottom-10">
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
                                       <div class="col-md-4">
                                          <div class="form-group">
                                             <label class="control-label">Report by&nbsp;<span class="required"> * </span></label>
                                             <select id="report_by" name="report_by" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%">
                                                <!--<option value="">Please Select</option>-->
                                                <option value="0">Assessment</option>
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
                  </div>
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

                     <div class='col-md-6'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Assessment Started
                              <a style="font-family:'Catamaran';" data-title="Number of assessment started in a time period">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>

                           <div id ="AssSt" class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;">
                              <div id="monthly_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Monthly
                           </div>
                              <input type="hidden" id="assessment_report" name="assessment_report" value="t_yearly" />
                           </div>

                        </div>
                        <div id='assessment_started'></div>
                        <div class="clearfix"></div>
                        <hr />
                     </div>

                     <div class='col-md-6'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran';  position: absolute;top: 22px; left: 24px;">Assessment Completed
                              <a data-title="Number of assessment completed in a time period">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>

                           <div id ="AssCd" class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;">
                              <div id="monthly_end_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Monthly
                           </div>
                              <input type="hidden" id="assessment_report_ended" name="assessment_report_ended" value="t_yearly" />
                           </div>
                        </div>
                        <div id='assessment_complted'></div>
                     </div>

                     <!-- <div class='col-md-6'>
                                 <div class='title-bar' >
                                    <span class="caption-subject font-dark bold" style="font-family:'Poppins',sans-serif; position: absolute;top: 50px; left: 24px;">Invitations Sent
                                    <a data-title="Total Invitations Sent">
                                    <i class="icon-info font-black sub-title"></i>
                                    </a>
                                    </span>
                                    <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-left: 218px; margin-top: 36px;">
                                       <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" style="">
                                       <input type="radio" name="rpt_period_option" class="toggle" id="total_user" value="t_yearly">Monthly</a>
                                       <a data-toggle="modal"  class="btn btn-circle btn-icon-only btn-default" href="#responsive-total-user" >
                                       <i class="icon-settings"></i>
                                       </a>
                                       <input type="hidden" id="invitations_sent" name="invitations_sent" value="t_yearly" />
                                    </div>
                                 </div>
                                 <div id='total_user_device'></div>
                              </div> -->
                     <!-- <div class="clearfix"></div> -->
                  </div>
                  <div class="clearfix"></div>
                  <hr />


                  <!-- New CHART Start Here -->
                  <!-- <div class="row" style="display: -webkit-box; overflow-x: auto;">
                              <div class="col-lg-8 col-xs-12 col-sm-12">
                                 <div class="portlet light bordered" style="12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                       <div class="caption">
                                          <i class="icon-bar-chart font-dark hide"></i>
                                          <span class="caption-subject font-dark">Assessment Wise users 
                                          <a data-title="Assessment">
                                          <i class="icon-info font-black sub-title"></i>
                                          </a>
                                          </span>
                                       </div>
                                       <div class="actions">
                                          <div class="btn-group btn-group-devided" data-toggle="buttons">
                                             <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                             <input type="radio" name="" class="toggle" id="assessment_wise_count" value="yearly">Monthly</a>
                                             <a data-toggle="modal"  class="btn btn-circle btn-icon-only btn-default" href="#" style="padding: 3px 0px !important;">
                                             <i class="icon-settings"></i>
                                             </a>
                                             <input type="hidden" id="#" name="" value="yearly" />
                                          </div>
                                       </div>
                                    </div>
                                    <div class="portlet-body bordered">
                                       <div class="row margin-bottom-15" id="assessment_count_users" style="padding: 0px !important"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr/>
                                 </div>
                              </div>
                              <div class="col-lg-8 col-xs-12 col-sm-12">
                                 <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                       <div class="caption">
                                          <i class="icon-bar-chart font-dark hide"></i>
                                          <span class="caption-subject font-dark bold uppercase">Assessment
                                          <a data-title="Assessment">
                                          <i class="icon-info font-black sub-title"></i>
                                          </a>
                                          </span>
                                       </div>
                                       <div class="actions">
                                          <div class="btn-group btn-group-devided" data-toggle="buttons">
                                             <a href="#" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">
                                             <input type="radio" name="" class="toggle" id="#" value="yearly">Monthly</a>
                                             <a data-toggle="modal"  class="btn btn-circle btn-icon-only btn-default" href="#" style="padding: 3px 0px !important;">
                                             <i class="icon-settings"></i>
                                             </a>
                                             <input type="hidden" id="rpt_period" name="" value="yearly" />
                                          </div>
                                       </div>
                                    </div>
                                    <div class="portlet-body bordered">
                                       <div class="row margin-bottom-15" id="#" style="padding: 0px !important"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr/>
                                 </div>
                              </div>
                           </div> -->
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

   <!-- Add By Nirmal -->
   <script src='<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js'></script>
   <script src='<?php echo $asset_url; ?>assets/global/highcharts/modules/export-data.js'></script>

   <!-- End Here -->
   <!--        <script src="< ?php echo $asset_url;?>assets/global/highcharts/highcharts.js"></script>-->
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
   <script src="<?php echo $asset_url; ?>assets/customjs/admin_dashboard.js"></script>
   <script>
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
         // assessment_started();
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



         //Box 3
         // btnIndexchartdevice
         $('#btnIndexchartdevice').click(function(event) {
            event.preventDefault();
            // total_device_user();
            $('#responsive-total-user').modal('toggle');
         });
         $('#total_user').change(function(event) {
            event.preventDefault();
            $("#invitations_sent").val('t_yearly');
            // total_device_user();                    
         });
         // NEW USER(ASSESSMENT WISE) COUNT
         // assessment_wise_count
         $('#assessment_wise_count').change(function(event) {
            event.preventDefault();
            $("#rpt_period").val('yearly');
            // total_device_user();                    
         });

         // Filters For Charts
         // $('#AssSt').click(function(){
         //    var DType= "Assesment_start";
         //    datepick(DType);
         // });
         // $('#AssCd').click(function(){
         //    var DType= "Assesment_Complted";
         //    datepick(DType);
         // });
               
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
         
         // function datepick(DType){
         //    console.log(DType);
         // }
         $('#monthly_picker').daterangepicker({
               "ranges": {
                  'Current Year': [moment().subtract('days', 6), moment()],
                  // 'Current Year': [moment().startOf('month'), moment().endOf('month')],
                  'Last 7 Days': [moment().subtract('days', 700), moment()],
                  'Last 30 Days': [moment().subtract('days', 2900), moment()],
                  'Last 60 Days': [moment().subtract('days', 5900), moment()],
                  'Last 90 Days': [moment().subtract('days', 8900), moment()],
                  'Last 365 Days': [moment().subtract('days', 3650), moment()]
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
               var IsCustom =  label;
               
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
            
            assessment_started(IsCustom);
            
         });
             
         });
      
         setTimeout(function() {
             assessment_started();

         }, 2000);
         
      });
   
   
 
      //Addded

      // filter box 2
      $('#monthly_end_picker').daterangepicker({
            "ranges": {
               'Last 7 Days': [moment().subtract('days', 7), moment()],
               'Current Year': [moment().subtract('days', 300), moment()],
               // 'Last 14 Days': [moment().subtract('days', 13), moment()],
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

         assessment_complted();
      });
      setTimeout(function() {
         assessment_complted();

      }, 2000);
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
            url: "<?php echo $base_url; ?>admin_dashboard/ajax_assessmentwise_data",
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
         //  "linkedCalendars": false,
         //"autoUpdateInput": false,
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
      }, function(start, end, label) {
         if ($('#dashboard-report-range').attr('data-display-range') != '0') {
            $('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
         }
      });
      if ($('#dashboard-report-range').attr('data-display-range') != '0') {
         // var thisYear = (new Date()).getFullYear();    
         //var start = new Date("1/1/" + thisYear);
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         //var start = new Date("1/1/" + thisYear);
         var start = new Date(thisMonth + "/1/" + thisYear);
         // $('#dashboard-report-range span').html(moment(start.valueOf()).startOf('month').format('MMMM D, YYYY') + ' - ' + moment().endOf('month').format('MMMM D, YYYY'));
         $('#dashboard-report-range span').html(moment().subtract('days', 6).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
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
      setTimeout(function() {
         dashboard_refresh();

      }, 2000);
      // );
   </script>
</body>

</html>