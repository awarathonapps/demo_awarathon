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
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
	<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css"> -->
	<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
	<!--datattable CSS  End-->
	<?php $this->load->view('inc/inc_htmlhead'); ?>
	<style>
		.dashboard-stat.aiboxes {
			color: #db1f48;
			background-color: #e8e8e8;
		}

		.dashboard-stat.aiboxes .more {
			color: #db1f48;
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
								<span><a href="<?php echo base_url().'reports_ai_process';?>"> AI Dashboard</a></span>
							</li>
							<li>
								<i class="fa fa-circle"></i>
								<span><a href="<?php echo base_url().'ai_reports';?>">AI Reports</a></span>
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
										Total <br />Assessment
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
										Total <br />User Mapped
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
										Total <br />User Played
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
										Total Video <br />Uploaded
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
										Total Video <br />Processed
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
										Total Report <br />Sent
									</div>
								</div>
								<a class="more" href="#">&nbsp;</a>
							</div>
						</div>
					</div>
					<div class="row margin-top-10 ">
						<div class="col-md-12">
							<input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id; ?>" />
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption caption-font-24">
										Email Report Schedule
										<div class="tools"> </div>
									</div>
								</div>
								<div class="portlet-body">
									<div class="tabbable-line tabbable-full-width">
										<ul class="nav nav-tabs" id="tabs">
											<li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
												<a href="#section-candidates" data-toggle="tab">Preview</a>
											</li>
											<li <?php echo ($step == 3 ? 'class="active"' : ''); ?>>
												<a href="#ideal-video" data-toggle="tab">Ideal Video</a>
											</li>
											<li <?php echo ($step == 4 ? 'class="active"' : ''); ?>>
												<a href="#tab_template" data-toggle="tab">Email Template</a>
											</li>
											<li>
												<a href="#tab_email_send" data-toggle="tab">Send</a>
											</li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane <?php echo ($step == 1 ? 'active' : 'mar'); ?>" id="section-candidates">
												<form role="form" id="frmAssessment_view" name="frmAssessment_view" method="post" action="">
													<div class="form-body">
														<div class="row ">
															<div class="col-md-12" id="assessment_panel_view">
																<table class="table  table-bordered table-hover table-checkable order-column" id="index_table_view">
																	<thead>
																		<tr>
																			<th>ID</th>
																			<th>Assessment</th>
																			<th>Assessment Type</th>
																			<th>Start Date/Time</th>
																			<th>End Date/Time</th>
																			<th>Status</th>
																			<th>User <br /> Mapped</th>
																			<th>User <br /> Played</th>
																			<th>Video <br /> Uploaded</th>
																			<th>Video <br /> Processed</th>
																			<th>Ranking <br />Report</th>
																			<th>Manager <br />Dashboard</th>
																			<th>Report</th>
																			<th>PWA</th>
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
											<div class="tab-pane <?php echo ($step == 3 ? 'active' : 'mar'); ?>" id="ideal-video">
												<form role="form" id="frmAssessment_ideal" name="frmAssessment_ideal" method="post" action="">
													<div class="form-body">
														<div class="row ">
															<div class="col-md-12" id="assessment_panel">
																<table class="table table-bordered table-hover table-checkable order-column" id="index_table_ideal">
																	<thead>
																		<tr>
																			<th>ID</th>
																			<th>Assessment</th>
																			<th>Assessment Type</th>
																			<th>Start Date/Time</th>
																			<th>End Date/Time</th>
																			<th>Status</th>
																			<th>Question <br /> Mapped</th>
																			<th>User <br /> Mapped</th>
																			<th>User <br /> Played</th>
																			<th>Video <br /> Uploaded</th>
																			<th>Video <br /> Processed</th>
																		</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="modal fade" id="LoadModalFilter_ideal" role="basic" aria-hidden="true" data-width="400">
														<div class="modal-dialog modal-lg" style="width:1024px;">
															<div class="modal-content">
																<div class="modal-body" id="modal-body-ideal">
																	<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
																	<span>
																		&nbsp;&nbsp;Loading... </span>
																</div>
															</div>
														</div>
													</div>
												</form>
											</div>
											<div class="tab-pane <?php echo ($step == 4 ? 'active' : 'mar'); ?>" id="tab_template">
											</div>
											<div class="tab-pane <?php echo ($step == 5 ? 'active' : 'mar'); ?>" id="tab_email_send">
												<div class="form-body">
													<div class="row margin-bottom-10">
														<div class="col-md-12 text-right">
															<button type="button" id="schedule_mail" name="schedule_mail" data-loading-text="Please wait..." class="btn btn-orange btn-sm btn-outline" data-style="expand-right" style="margin-right: 10px;">
																<span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send </span>
															</button>
														</div>
													</div>
													<div class="row ">
														<div class="col-md-12" id="assessment_panel_send">
															<table class="table  table-bordered table-hover table-checkable order-column" id="index_table_send">
																<thead>
																	<tr>
																		<th>
																			<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
																				<input type="checkbox" class="all group-checkable assessment_check" name="assessment_check" id="assessment_check" data-set="#index_table .checkboxes" />
																				<span></span>
																			</label>
																		</th>
																		<th>ID</th>
																		<th>Assessment</th>
																		<th>Assessment Type</th>
																		<th>Start Date/Time</th>
																		<th>End Date/Time</th>
																		<th>Status</th>
																		<th>Question <br />Mapped</th>
																		<th>User <br />Mapped</th>
																		<th>User <br />Played</th>
																		<th>Video <br />Uploaded</th>
																		<th>Video <br />Processed</th>
																		<th>Email <br />Status</th>
																		<th>Send</th>
																	</tr>
																</thead>
																<tbody>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row margin-top-10">
							<div class="col-md-12" id="participants_table">
							</div>
						</div>
						<div class="modal fade" id="LoadModalFilter-view" role="basic" aria-hidden="true" data-width="400">
							<div class="modal-dialog modal-lg" style="width:1024px;">
								<div class="modal-content">
									<div class="modal-body" id="modal-body">
										<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
										<span>
											&nbsp;&nbsp;Loading... </span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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
		<script src="<?php echo $asset_url; ?>assets/customjs/reports_ai_process.js" type="text/javascript"></script>
		<script>
			var json_participants = [];
			var select_assessments = '';
			var base_url = '<?= base_url(); ?>';

			var statistics_start_date = moment(Date()).subtract(1, 'months').format("YYYY-MM-DD");
			var statistics_end_date = moment(Date()).format("YYYY-MM-DD");
			var options = {};
			options.startDate = moment(Date()).subtract(29, 'days').format("DD/MM/YYYY");
			options.endDate = moment(Date()).format("DD/MM/YYYY");
			options.timePicker = false;
			options.showDropdowns = true;
			options.alwaysShowCalendars = true;
			options.autoApply = true;
			options.ranges = {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			};
			options.locale = {
				direction: 'ltr',
				format: 'DD/MM/YYYY',
				separator: ' - ',
				applyLabel: 'Apply',
				cancelLabel: 'Cancel',
				fromLabel: 'From',
				toLabel: 'To',
				customRangeLabel: 'Custom',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			};

			jQuery(document).ready(function() {

				//Statistics Code Start -------------
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
				$('#dashboard-report-range').on('apply.daterangepicker', function(ev, picker) {
					statistics_start_date = picker.startDate.format('YYYY-MM-DD');
					statistics_end_date = picker.endDate.format('YYYY-MM-DD');
					statistics();
				});
				statistics();
				//Statistics Code  End ------------- 

				//EMail Schedule Code Start -----------------
				datatable_view();
				DatatableRefresh_Ideal();
				setEmailBody();
				DatatableRefresh_send();
				$('.assessment_check').click(function() {
					if ($(this).is(':checked')) {
						$("input[name='id[]']").prop('checked', true);
					} else {
						$("input[name='id[]']").prop('checked', false);
					}
				});
				$('#schedule_mail').click(function() {
					var select_assessments = $.map($(':checkbox[name=id\\[\\]]:checked'), function(n, i) {
						return n.value;
					}).join(',');
					if (!select_assessments.trim()) {
						ShowAlret('Please select the assessment!', 'error');
					} else {
						// console.log(select_assessments);
						scheduleEmail($('#company_id').val(), select_assessments, 1); //send to all candidates of the selected assessments
					}
				});
				//EMail Schedule Code End -----------------
			});
		</script>
</body>

</html>