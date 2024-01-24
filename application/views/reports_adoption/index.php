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
                           <span>Adoption</span>
                        </li>
                     </ul>
                     <div class="page-toolbar">
                        <a href="<?php echo base_url() . 'reports'; ?>" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                     </div>
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
                  </style>
                  <div class='row' style="display: -webkit-box; overflow-x: auto; overflow-y: none;">
                     <div class='col-md-5'>
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
                     <div class='col-md-5'>
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

                     <div class='col-md-5'>
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
                              <a data-title="No. of reps completed & played in selected time interval ">
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
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Videos processed
                              <a data-title="No. of videos processed in selected time interval">
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
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Report Sent
                              <a data-title="No. of report sent in selected time interval">
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
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Videos uploaded
                              <a data-title="No. of videos uploaded in selected time interval">
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

                  <!-- Adoption By Module Nd Region  -->
                  <div class="row" style="display: -webkit-box; overflow-x: auto;">
                     <!-- By Bhautik Rana 09-01-2023 -->
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by Modules
                              <a data-title="% No. of reps started and completed under selected division">
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
                     <!-- Adoption_by_division overall  -->
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by division (Overall)
                              <a data-title="% No. of reps started and completed under selected division">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-division-overall">
                                 Filter</a>
                           </div>
                        </div>
                        <input type="hidden" name="is_custom_overall" id="is_custom_overall" value=''>
                        <div id='adoption_by_division_overall'></div>
                        <br>
                     </div>
                     <!-- Adoption By Division Overall -->
                     <!-- By Bhautik Rana 09-01-2023 -->



                  </div>
                  <!-- Adoption By Module Nd Region End Here  -->
                  <br>
                  <!-- By Bhautik Rana 10-01-2023 -->
                  <!-- Adoption by region (overall) "01-10-2023"  start here "Nirmal Gajjar" -->

                  <div class="row" style="display: -webkit-box; overflow-x: auto;">

                     <!-- Adoption by team (overall) "01-09-2023" start here "Nirmal Gajjar" -->
                     <!-- Adoption by team (overall) "01-09-2023" start here -->
                     <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute;top: 22px; left: 24px;">Adoption by team (Overall)
                              <a data-title="Graph which shows %completion & %Start of candidates by manager">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px;margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive_modal_manager">
                                 Filter</a>
                           </div>
                        </div>
                        <input id="c_m_iscustom" type="hidden" value="" name="c_m_iscustom">
                        <div id='a_b_managers'></div>
                     </div>
                     <!-- End here -->
                     <!-- Adoption by region (overall) "01-10-2023" start here -->
                     <div class='col-md-6'>
                        <div class='title-bar'>
                           <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Adopion by region (Overall)
                              <a data-title="Graph which shows %completion & %Start of candidates by region">
                                 <i class="icon-info font-black sub-title"></i>
                              </a>
                           </span>
                           <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                              <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#adoption_by_region_model">
                                 Filter
                              </a>
                           </div>
                        </div>
                        <input type="hidden" name="is_custom_by_reg" value="" id="is_custom_by_reg">
                        <div id='ad_by_region_overall'></div>
                        <br>
                     </div>
                     <!-- End here -->
                  </div>
                  <br>
                  <!-- End Here -->
                  <!-- By Bhautik Rana 10-01-2023 -->
         
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
                           <label>Modules<p style="color: red;position: absolute;top: -19px;left: 61px;font-size: 21px;">* </p><span style="position: absolute;top: 7px;left: 70px;color: red;font-size: 8px;"> (Mandatory)</span><span class="select-all">Select All</span></label>
                           <input class="amt_modules" id="amt_modules" type="checkbox" id="common_check">
                           <select id="new_assessment_id" name="new_assessment_id[]" class="form-control input-sm select2me" placeholder="Please select" multiple="">
                              <!-- <option value="">Select</option> -->
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
                     <button type="button" class="btn btn-orange" onclick="checkId()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End Here -->

   <!-- By Bhautik Rana  10-01-2023 start -->
   <!-- Adoption_by_division_overall  -->
   <div id="responsive-modal-division-overall" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Modules </label>
                           <select id="assessment_id_overall" name="assessment_id_overall[]" class="form-control input-sm select2me" placeholder="Please select" multiple=''>
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
                           <label>Manager
                              <!-- <span class="select-all">Select All</span> -->
                           </label>
                           <!-- <input id="manager_check" type="checkbox" id="common_check"> -->
                           <select id="manager_id_overall" name="manager_id_overall[]" class="form-control input-sm select2me" placeholder="Please select" multiple="">
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Division<p style="color: red;position: absolute;top: -24px;left: 58px;font-size: 21px;">* </p><span style="position: absolute;top: 2px;left: 67px;color: red;font-size: 8px;"> (Mandatory)</span>
                              <!-- <span class="select-all">Select All</span> -->
                           </label>
                           <!-- <input id="div_check" type="checkbox" id="common_check"> -->
                           <select id="division_id_overall" name="division_id_overall[]" class="form-control input-sm select2me" placeholder="Please select" multiple="">
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Date</label>
                           <input class="form-control input-sm" id="division_picker" value="" name="division_picker" readonly>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                     <button type="button" class="btn btn-orange" onclick="check_division()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Adoption_by_division_overall end  -->

   <!-- By Bhautik Rana  10-01-2023 -->
   <!-- Adoption by team new -->
   <div id="responsive_modal_manager" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <label>Manager <p style="color: red;position: absolute;top: -24px;left: 64px;font-size: 21px;">* </p><span style="position: absolute;top: 2px;left: 73px;color: red;font-size: 8px;"> (Mandatory)</span></label>
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
   <!-- Adoption by region new 10-01-2023-->
   <div id="adoption_by_region_model" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
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
                           <select id="as_id" name="as_id" class="form-control input-sm select2" placeholder="Please select" multiple=''>
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
                           <select id="man_rg_id" name="man_rg_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                              <option value="">All Manager</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Region <p style="color: red;position: absolute;top: -22px;left: 53px;font-size: 21px;">* </p><span style="position: absolute;top: 2px;left: 61px;color: red;font-size: 8px;"> (Mandatory)</span></label>
                           <select id="ab_reg_id" name="ab_reg_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-11">
                        <div class="form-group last">
                           <label>Select Time</label>
                           <input class="form-control input-sm" id="adoption_by_region_picker" value="" name="adoption_by_region_picker" readonly>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <div class="col-md-12 text-right ">
                     <button type="button" class="btn btn-orange" onclick="check_ab_region()">
                        <span class="ladda-label">Apply</span>
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- End here -->
   <!-- Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  end here "Nirmal Gajjar" -->


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
      var step = 0;
      //            var owl =$("#region_data");
   </script>
   <script src="<?php echo $asset_url; ?>assets/customjs/reports_adoption.js"></script>
   <script>
      adoptionbymodule();
      get_div_manager();

    
      function get_div_manager(division_set) {
         var assessment_id = $('#assessment_id').val();
         if (Company_id == "") {
            return false;
         }
         $.ajax({
            type: "POST",
            data: {
               company_id: Company_id,
               assessmentid: assessment_id,
            },
            //async: false,
            url: "<?php echo $base_url; ?>reports_adoption/get_adoption_divison",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(response) {
               if (response != '') {
                  var Oresult = jQuery.parseJSON(response);
                  $('#division_id').empty();
                  $('#division_id').append(Oresult['division']);
                  $('#manager_id').empty();
                  $('#manager_id').append(Oresult['manager']);
                  if (division_set != '') {
                     $('#division_id').val(division_set);
                     $('#division_id').trigger('change');
                  }

               }
               customunBlockUI();
            }
         });
      }

      Getassessmentregion();
      $("#am_id").change(function() {
         Getassessmentregion();
      });

      function Getassessmentregion(rg_id) {
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
            url: "<?php echo $base_url; ?>reports_adoption/assessment_wise_region",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#region_id').empty();
                  $('#managerid').empty();
                  $('#region_id').append(Oresult['region']);
                  $('#managerid').append(Oresult['manager']);

                  if (rg_id != '') {
                     $('#region_id').val(rg_id);
                     $('#region_id').trigger('change');
                  }
               }
               customunBlockUI();
            }
         });
      }



      // Adoption By Modules 
      function checkId() {
         var id = $("#new_assessment_id").val();
         if (id == null) {
            ShowAlret("Please select Modules .!!", 'error');
            return false;
         } else {
            adoptionbymodule();
         }
      }
      // select all function for Mdules
      $("#amt_modules").click(function() {
         if ($("#amt_modules").is(':checked')) {
            $("#new_assessment_id").find('option').prop("selected", true);
            $("#new_assessment_id").trigger('change');
         } else { //deselect all
            $("#new_assessment_id").find('option').prop("selected", false);
            $("#new_assessment_id").trigger('change');
         }
      });
      // end here
      // select all function for Managers
      $("#modules_managers").click(function() {
         if ($("#modules_managers").is(':checked')) { //select all
            $("#trainer_id").find('option').prop("selected", true);
            $("#trainer_id").trigger('change');
         } else { //deselect all
            $("#trainer_id").find('option').prop("selected", false);
            $("#trainer_id").trigger('change');
         }
      });

      // select all function for region
      $("#modules_region").click(function() {
         if ($("#modules_region").is(':checked')) { //select all
            $("#regionId").find('option').prop("selected", true);
            $("#regionId").trigger('change');
         } else { //deselect all
            $("#regionId").find('option').prop("selected", false);
            $("#regionId").trigger('change');
         }
      });

      // select all function for division
      $("#modules_division").click(function() {
         if ($("#modules_division").is(':checked')) { //select all
            $("#divsionId").find('option').prop("selected", true);
            $("#divsionId").trigger('change');
         } else { //deselect all
            $("#divsionId").find('option').prop("selected", false);
            $("#divsionId").trigger('change');
         }
      });


      //Adoption by Region Check boxes
      $("#managerCheck").click(function() {
         if ($("#managerCheck").is(':checked')) { //select all
            $("#managerid").find('option').prop("selected", true);
            $("#managerid").trigger('change');
         } else { //deselect all
            $("#managerid").find('option').prop("selected", false);
            $("#managerid").trigger('change');
         }
      });
      $("#region_check").click(function() {
         if ($("#region_check").is(':checked')) { //select all
            $("#region_id").find('option').prop("selected", true);
            $("#region_id").trigger('change');
         } else { //deselect all
            $("#region_id").find('option').prop("selected", false);
            $("#region_id").trigger('change');
         }
      });

      //  By Bhautik Rana 10-01-2023 
      // adoption_by_division_overall start 
      function check_division() {
         var division_set = $('#division_id_overall').val();
         if (division_set == null) {
            ShowAlret("Please select Division.!!", 'error');
            return false;
         } else {
            Adoption_by_division_overall();
         }
      }
      get_manager_div_overall();
      $("#assessment_id_overall").change(function() {
         get_manager_div_overall();
      });

      function get_manager_div_overall(division_set, manager_set) {
         var assessment_id = $('#assessment_id_overall').val();
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
            url: "<?php echo $base_url; ?>reports_adoption/get_div_manager",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(msg) {
               if (msg != '') {
                  var Oresult = jQuery.parseJSON(msg);
                  $('#manager_id_overall').empty();
                  $('#manager_id_overall').append(Oresult['manager']);
                  $('#division_id_overall').empty();
                  $('#division_id_overall').append(Oresult['division']);
               }
               if (division_set != '') {
                  $('#division_id_overall').val(division_set);
                  $('#division_id_overall').trigger('change');
               }
               if (manager_set != '') {
                  $('#manager_id_overall').val(manager_set);
                  $('#manager_id_overall').trigger('change');
               }
               customunBlockUI();
            }
         });
      }
      // adoption_by_division_overall end
      //  By Bhautik Rana 10-01-2023 

      function region_division_manager_assessment_wise(trainer_id, region_id, division_id) {
         var assessment_id = $('#new_assessment_id').val();
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
            url: "<?php echo $base_url; ?>reports_adoption/assessment_wise_mrd",
            beforeSend: function() {
               customBlockUI();
            },
            success: function(response) {
               if (response != '') {
                  var Oresult = jQuery.parseJSON(response);
                  $('#trainer_id').empty();
                  $('#regionId').empty();
                  $('#divsionId').empty();
                  $('#trainer_id').append(Oresult['trainers_name']);
                  $('#regionId').append(Oresult['region_name']);
                  $('#divsionId').append(Oresult['divsion_name']);
               }
               if (trainer_id != null) {
                  $('#trainer_id').val(trainer_id);
                  $('#trainer_id').trigger('change');
               }
               if (region_id != null) {
                  $('#regionId').val(region_id);
                  $('#regionId').trigger('change');
               }
               if (division_id != null) {
                  $('#divsionId').val(division_id);
                  $('#divsionId').trigger('change');
               }
               customunBlockUI();
            }
         });
      }
      // adoption by modules function end here
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

         // Adoption Division functions start here

         $("#div_check").click(function() {
            if ($("#div_check").is(':checked')) { //select all
               $("#division_id").find('option').prop("selected", true);
               $("#division_id").trigger('change');
            } else { //deselect all
               $("#division_id").find('option').prop("selected", false);
               $("#division_id").trigger('change');
            }
         });


         $("#manager_check").click(function() {
            if ($("#manager_check").is(':checked')) { //select all
               $("#manager_id").find('option').prop("selected", true);
               $("#manager_id").trigger('change');
            } else { //deselect all
               $("#manager_id").find('option').prop("selected", false);
               $("#manager_id").trigger('change');
            }
         });
         // Adoption Division functions end here

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
               "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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
            "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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
            "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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
      // end here

      //total_videos_uploaded date picker start here
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
            "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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

      //total_videos_processed date picker start here
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
            "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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

      // Total User Active Inactive  date picker start here
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
      // end here

      // No of Raps played and Completed date picker start here
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
            "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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
      // end here

      // Total_Report Sent Picker date picker start here
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
            "startDate": moment().subtract('days', 365).format("DD/MM/YYYY"),
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
      // end here

      // Adoption by modules datepicker date picker start here
      var quarter = moment().quarter();
      var year = moment().year();

      $('#module_picker').daterangepicker({
         "ranges": {
            // moment().quarter(quarter).startOf('quarter'), moment().quarter(quarter).endOf('quarter')
            // moment().year(year).startOf('year'), moment().year(year).endOf('year')

            // dateStart: function() { return moment().startOf('year') }, dateEnd: function() { return moment() } 
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
         "drops": "up",
         "opens": "right",
         //   opens: (App.isRTL() ? 'right' : 'left'),
      }, function(start, end, label) {
         sessionStorage.setItem("IsCustom", label);
      });
      if ($('#module_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);


      }
      $('#module_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         // Adoption_by_module(IsCustom);
      });
      // adoptionbymodule('');
      // end here

      //  By Bhautik Rana 10-01-2023 
      // Adoption_by_division_overall picker
      $('#division_picker').daterangepicker({
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
         "drops": "up",
         "opens": "right",
         //   opens: (App.isRTL() ? 'right' : 'left'),
      }, function(start, end, label) {
         sessionStorage.setItem("IsCustom", label);
      });
      if ($('#division_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);
      }
      $('#division_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         $('#is_custom_overall').val(IsCustom);
      });
      Adoption_by_division_overall('');
      // Adoption_by_division_overall picker
      //  By Bhautik Rana 10-01-2023 

      // Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  end here "Nirmal Gajjar"
      // New Adoption by team "09-01-2023"
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

      });

      adoption_by_manager();

      // Adoption by region new 10-01-2023
      ad_by_region();

      function check_ab_region() {
         var region_id = $('#ab_reg_id').val();
         if (region_id == null) {
            ShowAlret("Please select Region .!!", 'error');
            return false;
         } else {
            ad_by_region();
         }
      }
      $('#adoption_by_region_picker').daterangepicker({
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
      if ($('#adoption_by_region_picker').attr('data-display-range') != '0') {
         var thisYear = (new Date()).getFullYear();
         var thisMonth = (new Date()).getMonth() + 1;
         var start = new Date(thisMonth + "/1/" + thisYear);
      }
      $('#adoption_by_region_picker').on('apply.daterangepicker', function(ev, picker) {
         $('#date_lable').text(picker.chosenLabel);
         StartDate = picker.startDate.format('DD-MM-YYYY');
         EndDate = picker.endDate.format('DD-MM-YYYY');

         let IsCustom = sessionStorage.getItem("IsCustom");
         $('#is_custom_by_reg').val(IsCustom);

      });
      // Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  end here "Nirmal Gajjar"
   </script>
   <style>
      image {
         display: none
      }
   </style>
</body>

</html>