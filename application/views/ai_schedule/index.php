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
    <!--datattable CSS  Start-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css">
	<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

    <!--datattable CSS  End-->
    <?php $this->load->view('inc/inc_htmlhead'); ?>
    <style>
        .dashboard-stat.aiboxes {
            color: #232323;
            background-color: #e8e8e8;
        }
        .dashboard-stat.aiboxes .more{
            color: #232323;
            background-color: #dcdcdc;
        }
        .dashboard-stat .details .number{
            padding-top: 10px !important;
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
                                <span>Reports</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>AI Schedule</span>
                            </li>
                        </ul>
						<div class="col-md-1 page-breadcrumb"></div>
						<div class="page-toolbar">
							<div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
								<i class="icon-calendar"></i>&nbsp;
								<span class="thin uppercase hidden-xs"></span>&nbsp;
								<i class="fa fa-angle-down"></i>
							</div>
						</div>
                    </div>
                    <div class="row margin-top-15">
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_i_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total <br/>Assessment
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_vi_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                    Total Question <br/>Mapped
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_ii_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total <br/>User Mapped
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_iii_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total <br/>User Played
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_iv_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                    Total Video <br/>Uploaded
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_v_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                    Total Video <br/>Processed
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-top-10 ">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="col-md-12" style="padding:0px;">
                                    <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id;?>" />
                                    <select id="assessment_id" name="assessment_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                        <option value="">Please Select</option>
                                        <?php foreach ($assessment_result as $assres) { ?>
                                            <option value="<?php echo $assres->id; ?>"><?php echo $assres->assessment; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-1" style="padding:0px 0px 0px 20px;margin:0px 0px 5px 0px;width:100px;">
                            <button id='btn_run_schedule_new' data-toggle="modal" onclick="run_schedule('new');" class="btn btn-sm btn-orange">Run Schedule</button>&nbsp;
                        </div>
                        <div class="col-md-1" style="padding:0px 0px 0px 20px;margin:0px 0px 5px 0px;width:130px;">
                            <button id='btn_display_reports' onclick="turnon_reports_flags();" class="btn btn-sm btn-orange">Generate Reports</button>&nbsp;
                        </div>
                        <div class="col-md-1" style="margin:0px 10px 0px 0px;width:130px;">
                            <button id='btn_run_schedule_pending' data-toggle="modal" onclick="run_schedule('pending');" class="btn btn-sm btn-orange">Run Schedule (Pending)</button>&nbsp;
                        </div> -->
                    </div>

                    <div class="row margin-top-10">
                        <div class="col-md-12" id="participants_table">
                        </div>
                    </div>
                    <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true"  data-width="760">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Error Logs</h4>
                                </div>
                                <div class="modal-body" id="mdl_error_log">

                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right ">  
                                        <button type="button" data-dismiss="modal" class="btn btn-default btn-cons">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="responsive-video-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true"  data-width="760">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button> -->
                                    <h4 class="modal-title">Assessment Video</h4>
                                </div>
                                <div class="modal-body" id="mdl_video">
                                <iframe id='dp-video' src='' frameborder='0' allow='autoplay; fullscreen; picture-in-picture;' allowFullScreen style='top: 0;left: 0;width: 100%;box-sizing: border-box;height: 500px;border-top-width: 0px;border-right-width: 0px;border-bottom-width: 0px;border-left-width: 0px;'></iframe>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right ">  
                                        <button type="button" data-dismiss="modal" class="btn btn-default btn-cons" onclick="stop_video()">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script>
		var statistics_start_date = moment(Date()).subtract(1, 'months').format("YYYY-MM-DD");
        var statistics_end_date   = moment(Date()).format("YYYY-MM-DD");
        var json_participants     = [];
        var base_url              = '<?php echo base_url(); ?>';
        var frmModalForm          = $('#frmModalForm');
        var options               = {};
        // options.drops=  "up";
        options.startDate           = moment(Date()).subtract(29, 'days').format("DD/MM/YYYY");
        options.endDate             = moment(Date()).format("DD/MM/YYYY");
        options.timePicker          = false;
        options.showDropdowns       = true;
        options.alwaysShowCalendars = true;
        options.autoApply = true;
        options.ranges              = {
              'Today'       : [moment(), moment()],
              'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month'  : [moment().startOf('month'), moment().endOf('month')],
              'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };
        options.locale = {
            direction       : 'ltr',
            format          : 'DD/MM/YYYY',
            separator       : ' - ',
            applyLabel      : 'Apply',
            cancelLabel     : 'Cancel',
            fromLabel       : 'From',
            toLabel         : 'To',
            customRangeLabel: 'Custom',
            daysOfWeek      : ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
            monthNames      : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay        : 1
        };
        jQuery(document).ready(function() {
            $('#dashboard-report-range').daterangepicker(options, function(start, end, label) {
				if ($('#dashboard-report-range').attr('data-display-range') != '0') {
					$('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				}
                statistics_start_date = start.format('YYYY-MM-DD');
                statistics_end_date = end.format('YYYY-MM-DD');
                statistics();
            }).show();
			if ($('#dashboard-report-range').attr('data-display-range') != '0') {
				$('#dashboard-report-range span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
			}
			$('#dashboard-report-range').on('apply.daterangepicker', function (ev, picker) {
				statistics_start_date = picker.startDate.format('YYYY-MM-DD');
				statistics_end_date = picker.endDate.format('YYYY-MM-DD');
                statistics();
			});
            $('#assessment_id').select2({
                placeholder: "Please Select",
                width: '100%',
                allowClear: true
            });
            $("#assessment_id").change(function() { 
                fetch_participants();
            });
            statistics();
        });
    </script>
    <script src="<?php echo $asset_url; ?>assets/customjs/ai_schedule.js" type="text/javascript"></script>
</body>

</html>