<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
// New code for Access Role

$acces_management = $this->session->userdata('awarathon_session');
$SupperAccess = false;
$Company_id = $acces_management['company_id'];
$login_type = $acces_management['login_type'];

if ($acces_management['superaccess']) {
   $SupperAccess = true;
} else {
   $userID = $acces_management['user_id'];
   $roleID = $acces_management['role'];
   $ReturnSet = CheckSidebarRights($acces_management);
   $SideBarDataSet = $ReturnSet['RightsArray'];
   $SideBarDataSet = $ReturnSet['GroupArray'];
}
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

      #common_check {
         margin-left: 169px;
         position: absolute;
         left: 73px;
         top: -3px;
      }

      .rg-check {
         position: absolute;
         left: 89%;
      }

      #rg_select {
         margin-left: 137px;
         top: 4px;
         position: absolute;
      }

      .select-all {
         margin-left: 122px;
         margin-top: 3px;
      }

      #di_select {
         margin-left: 129px;
         margin-top: 3px;
      }

      /* Assessment List Css */

      /* Adoption  graph */
      .img-style {
         height: 75% !important;
         /* width: 50%;
         text-align: center;
         margin: auto;
         margin-left: 23%; */
         margin-top: 2%;
      }

      .head-text {
         font-family: "Catamaran";
         font-size: 16px;
         font-weight: 600;
         line-height: 24px;
         color: #2A2E36;
         text-transform: inherit;
         margin-bottom: 8px;
         text-align: center;
      }

      .sub-head {
         font-family: "Catamaran";
         font-size: 12px;
         font-weight: 400;
         line-height: 16px;
         color: #2A2E36;
         text-transform: inherit;
         text-align: center;
      }

      #manager_checkbox {
         margin-left: 66%;
         position: absolute;
      }

      #region_check {
         margin-left: 70%;
         position: absolute;
      }

      /* box style */
      .dashboard-stat.aiboxes {
         color: #db1f48;
         background-color: #e8e8e8;
      }

      .dashboard-stat.aiboxes .more {
         color: white;
         background-color: #004369;
         opacity: 1;
      }

      .dashboard-stat.aiboxes .more:hover {
         opacity: 1;
      }

      .dashboard-stat .details .number {
         padding-top: 10px !important;
         font-size: 24px;
         font-weight: 600;
         margin-top: 15px;

      }

      /* end here */
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
                           <a href="#">Dashboard</a>
                           <i class="fa fa-circle"></i>
                        </li>
                        <li>
                           <span>Home</span>
                        </li>
                     </ul>
                     <div class="col-md-1 page-breadcrumb"></div>
                  </div>
                  <!-- PAGE BAR -->

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

                     .box-style {
                        background: white;
                        margin-left: 12px;
                     }

                     .label-style {
                        width: 420px;
                        height: 40px;
                     }

                     .label_li_style {
                        padding-left: 16px;
                        font-size: 16px;
                     }

                     .quick-bar {
                        background: white;
                        padding: 0px 0px 42px 9px;
                        font-size: 17px;
                        position: static;
                        margin-left: 12px;
                     }

                     .btn-style {
                        text-align: right;
                        padding-right: 10px;
                     }

                     #btn-color {
                        background: #eef1f5;
                     }

                     #assessment_panel {
                        width: 95%;
                        margin-left: 30px;
                     }

                     #index_table_ideal {
                        border-bottom: 1px solid white;
                     }
                  </style>
                  <!-- Row 1 -->
                  <div class='row' style="display: -webkit-box; overflow-x: auto; overflow-y: none;">
                     <div class='col-md-4' style="padding-right: 0px;">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Monthly Reps Mapped
                              <a data-title="No. of reps mapped in selected time interval">
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
                     <div class='col-md-4' style="padding-right: 0px;">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Module Started
                              <a style="font-family:'Catamaran';" data-title="No. of module started in selected time interval">
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
                     <div class='col-md-4' style="padding-right: 0px;">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran';  position: absolute;top: 22px; left: 24px;">Module Completed
                              <a data-title="No. of module completed in selected time interval">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 1px;">
                              <div id="monthly_end_picker" name="daterange" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active">Filter
                              </div>
                              <input type="hidden" id="assessment_report_ended" name="assessment_report_ended" value="t_yearly" />
                           </div>
                        </div>
                        <div id='assessment_complted'>
                        </div>
                        <!-- See more Report -->
                        <div style="text-align:right;margin-top:1%;">
                           <?php if ($SupperAccess || isset($SideBarDataSet['adoption'])) {  ?>
                              <a href="<?php echo base_url() . "adoption"; ?>" style="color:red;font-weight: bold;">See More Reports</a>
                           <?php } else { ?>
                              <a href="<?php echo base_url() . "adoption"; ?>" style="color:red;font-weight: bold;" disabled>See More Reports</a>
                           <?php  } ?>
                        </div>
                        <!-- See more Report -->
                        <br>
                     </div>
                  </div>
                  <!-- Row 1 -->
                  <div class="row">

                     <div class="col-lg-3 col-md-2 col-sm-3 col-xs-6">
                        <div class="dashboard-stat aiboxes" style="width: 102%">
                           <div class="visual">&nbsp;</div>
                           <div class="details">
                              <div class="number" id="box_ii_statistics">

                              </div>
                              <div class="desc">
                                 Admin <br />Dashboard
                              </div>
                           </div>
                           <?php if ($SupperAccess || isset($SideBarDataSet['adoption'])) {  ?>
                              <a type="button" href="<?php echo base_url() . "adoption"; ?>" class="more"><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php } else { ?>
                              <a type="button" href="<?php echo base_url() . "adoption"; ?>" class="more" disabled><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php  } ?>
                        </div>
                     </div>
                     <div class="col-lg-3 col-md-2 col-sm-3 col-xs-6">
                        <div class="dashboard-stat aiboxes">
                           <div class="visual">&nbsp;</div>
                           <div class="details">
                              <div class="number" id="box_iii_statistics">
                              </div>
                              <div class="desc">
                                 Create <br />Module
                              </div>
                           </div>
                           <?php if ($SupperAccess || isset($SideBarDataSet['assessment_create'])) { ?>
                              <a type="button" href="<?php echo base_url() . "assessment_create"; ?>" class="more"><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php } else { ?>
                              <a type="button" href="<?php echo base_url() . "assessment_create"; ?>" class="more" disabled><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php  } ?>
                        </div>
                     </div>
                     <div class="col-lg-3 col-md-2 col-sm-3 col-xs-6">
                        <div class="dashboard-stat aiboxes">
                           <div class="visual">&nbsp;</div>
                           <div class="details">
                              <div class="number" id="box_iv_statistics">
                              </div>
                              <div class="desc">
                                 Add <br />User
                              </div>
                           </div>
                           <?php if ($SupperAccess || isset($SideBarDataSet['device_users'])) { ?>
                              <a type="button" href="<?php echo base_url() . "device_users"; ?>" class="more"><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php } else { ?>
                              <a type="button" href="<?php echo base_url() . "device_users"; ?>" class="more" disabled><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php  } ?>
                        </div>
                     </div>
                     <div class="col-lg-3 col-md-2 col-sm-3 col-xs-6">
                        <div class="dashboard-stat aiboxes" style="width:106%">
                           <div class="visual">&nbsp;</div>
                           <div class="details" style="padding-right: 1px;">
                              <div class="number" id="box_v_statistics">
                              </div>
                              <div class="desc">
                                 Jarvis <br />Dashboard
                              </div>
                           </div>
                           <?php if ($SupperAccess || isset($SideBarDataSet['ai_dashboard'])) { ?>
                              <a type="button" href="<?php echo base_url() . "ai_dashboard"; ?>" class="more"><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php } else { ?>
                              <a type="button" href="<?php echo base_url() . "ai_dashboard"; ?>" class="more" disabled><i class="fa fa-angle-right"></i> Click Here</a>
                           <?php  } ?>
                        </div>
                     </div>
                  </div>
                  <!-- Row 2 -->
                  <div class='row'>
                     <div class='col-md-12' style="padding-right: 0px;">
                        <div class='quick-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 9px; left: 24px; font-size:20px">Jarvis
                           </span>
                        </div>
                        <div class="box-style">
                           <form role="form" id="frmAssessment_ideal" name="frmAssessment_ideal" method="post" action="">
                              <div class="form-body">
                                 <div class="row ">
                                    <div id="assessment_panel">
                                       <table class="table table-bordered table-hover table-checkable order-column" id="index_table_ideal">
                                          <thead>
                                             <tr>
                                                <th>ID</th>
                                                <th>Assessment</th>
                                                <th>Assessment Type</th>
                                                <th>Start Date/Time</th>
                                                <th>End Date/Time</th>
                                                <th>Status</th>
                                                <th>User<br /> Mapped</th>
                                                <th>User<br /> Played</th>
                                                <th>Video<br /> Uploaded</th>
                                                <th>Video<br /> Processed</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </form>

                        </div>
                     </div>
                     <!-- <div class="col-md-6" style="padding-right: 0px;">
                        <div class='quick-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 9px; left: 24px; font-size:20px">Quick Links
                           </span>
                        </div>
                        <div class="box-style">
                           <table style="margin-top: -10px; width:100%">
                              <ul>
                                 <tr>
                                    <td class="label-style">
                                       <li class="label_li_style">Admin Dashboard</li>
                                    </td>
                                    <td class="btn-style">
                                       <?php if ($SupperAccess || isset($SideBarDataSet['adoption'])) {  ?>
                                          <a type="button" href="<?php echo base_url() . "adoption"; ?>" id="btn-color" class="btn btn-light "><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php } else { ?>
                                          <a type="button" href="<?php echo base_url() . "adoption"; ?>" id="btn-color" class="btn btn-light " disabled><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php  } ?>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="label-style">
                                       <li class="label_li_style">Create module</li>
                                    </td>
                                    <td class="btn-style">
                                       <?php if ($SupperAccess || isset($SideBarDataSet['assessment_create'])) { ?>
                                          <a type="button" href="<?php echo base_url() . "assessment_create"; ?>" id="btn-color" class="btn btn-light "><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php } else { ?>
                                          <a type="button" href="<?php echo base_url() . "assessment_create"; ?>" id="btn-color" class="btn btn-light " disabled><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php  } ?>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="label-style">
                                       <li class="label_li_style">Add User</li>
                                    </td>
                                    <td class="btn-style">
                                       <?php if ($SupperAccess || isset($SideBarDataSet['device_users'])) { ?>
                                          <a type="button" href="<?php echo base_url() . "device_users"; ?>" id="btn-color" class="btn btn-light "><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php } else { ?>
                                          <a type="button" href="<?php echo base_url() . "device_users"; ?>" id="btn-color" class="btn btn-light " disabled><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php  } ?>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="label-style">
                                       <li class="label_li_style">Jarvis Dashboard</li>
                                    </td>
                                    <td class="btn-style">
                                       <?php if ($SupperAccess || isset($SideBarDataSet['ai_dashboard'])) { ?>
                                          <a type="button" href="<?php echo base_url() . "ai_dashboard"; ?>" id="btn-color" class="btn btn-light "><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php } else { ?>
                                          <a type="button" href="<?php echo base_url() . "ai_dashboard"; ?>" id="btn-color" class="btn btn-light " disabled><i class="fa fa-angle-right"></i> Click Here</a>
                                       <?php  } ?>
                                    </td>
                                 </tr>
                              </ul>
                           </table>
                        </div>
                     </div> -->
                  </div>
                  <div class='row'>
                     <div class='col-md-12' style="text-align: right;padding-right: 0px;">
                     <?php if ($SupperAccess || isset($SideBarDataSet['ai_dashboard'])) {  ?>
                              <a href="<?php echo base_url() . "ai_dashboard"; ?>" style="color:red;font-weight: bold;">See More</a>
                           <?php } else { ?>
                              <a href="<?php echo base_url() . "ai_dashboard"; ?>" style="color:red;font-weight: bold;" disabled>See More</a>
                           <?php  } ?>
                     </div>
                  </div>
                  <!-- Row 2 -->
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


   <!-- All New grpahs end here -->
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
      var quarter = moment().quarter();
      var year = moment().year();
      var step = 0;
      //            var owl =$("#region_data");
   </script>
   <script src="<?php echo $asset_url; ?>assets/customjs/home.js"></script>
   <script>
      jQuery(document).ready(function() {
         $(".select2_rpt").select2({
            // placeholder: 'Please Select',
            width: '100%'
         });
         $(".select2me").select2({
            placeholder: 'Please Select',
            width: '100%',
            // height: '20%',
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

         // monthly started date picker start here
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
      //end here

      //  assessment started Completed date picker start here
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
      // end here


      // Raps Mapped Users date picker start here
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
      DatatableRefresh_Ideal();
   </script>
   <style>
      image {
         display: none
      }
   </style>
</body>

</html>