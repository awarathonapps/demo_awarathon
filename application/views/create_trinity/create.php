<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<style>
		.map-title {
			font-size: 16px;
			font-weight: 600;
		}

		.ps-container {
			display: inline-flex;
			width: 100%;
		}

		.ps-seperator {
			color: black;
			float: left;
			width: 1%;
			margin: 0.90px 0.25px 0.25px 0.25px;
		}

		.parameter-badge {
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #aee5ea;
			padding: 1px 10px 1px 10px;
			background: #dbfcff;
			margin: 0.90px 0.25px 0.25px 0.25px;
		}

		.sub-parameter-badge {
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #ffc9bc;
			padding: 1px 10px 1px 10px;
			background: #ffefeb;
			margin: 0.90px 0.25px 0.25px 0.25px;
		}

		.parameter-weight-badge {
			color: black;
			float: left;
			width: 20%;
			border: 1px solid #fff7bc;
			padding: 1px 10px 1px 10px;
			background: #feffef;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}

		.keysent-badge {
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #c5c5c5;
			padding: 1px 10px 1px 10px;
			background: #f9f9f9;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
	</style>

	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
	<?php $this->load->view('inc/inc_htmlhead'); ?>
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
								<span>Assessment</span>
								<i class="fa fa-circle"></i>
							</li>
							<li>
								<span>Create New Assessment</span>
							</li>
						</ul>
						<div class="page-toolbar">
							<a href="<?php echo $base_url ?>create_trinity" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
						</div>
					</div>
					<div class="row mt-10">
						<div class="col-md-12">
							<?php if ($this->session->flashdata('flash_message')) { ?>
								<div class="alert alert-success alert-dismissable">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
									<?php echo $this->session->flashdata('flash_message'); ?>
								</div>
							<?php } ?>
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption caption-font-24">
										Create Assessment
										<div class="tools"> </div>
									</div>
								</div>
								<div class="portlet-body">
									<div class="tabbable-line tabbable-full-width">
										<form id="AssessmentForm" name="AssessmentForm" method="POST" action="">
											<ul class="nav nav-tabs" id="tabs">
												<li class="active">
													<a href="#tab_overview" data-toggle="tab">Overview</a>
												</li>
												<!-- new tab -->
												<li class="tinity-content">
													<a href="javascript:void(0);">Map Persona</a>
												</li>
												<li class="tinity-content">
													<a href="javascript:void(0);">Mapping of Script</a>
												</li>
												<li class="tinity-content">
													<a href="javascript:void(0);">Map a Goal</a>
												</li>
												<!-- New tab -->
												<li>
													<a href="#tab_mapping_manager" data-toggle="tab">Mapping Manager</a>
												</li>
												<li>
													<a href="javascript:void(0);">Mapping rep</a>
												</li>
												<li>
													<a href="javascript:void(0);">Rep Manager mapping</a>
												</li>
											</ul>

											<div class="alert alert-danger display-hide" id="errordiv">
												<button class="close" data-close="alert"></button>
												You have some form errors. Please check below.
												<br><span id="errorlog"></span>
											</div>
											<div class="tab-content">
												<div class="tab-pane active" id="tab_overview">
													<?php
													if ($errors == "") {
														$errors = validation_errors();
													}
													if ($errors) {
													?>
														<div style="display: block;" class="alert alert-danger display-hide">
															<button class="close" data-close="alert"></button>
															You have some form errors. Please check below.
															<?php echo $errors; ?>
														</div>
													<?php } ?>

													<fieldset>
														<legend>General Information:</legend>
														<div class="row">
															<?php if ($Company_id == "") { ?>
																<div class="col-md-4">
																	<div class="form-group">
																		<label class="">Company Name<span class="required"> * </span></label>
																		<select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
																			<option value="">Please Select</option>
																			<?php foreach ($CompnayResultSet as $cmp) { ?>
																				<option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
															<?php } ?>
														</div>

														<div class="row">
															<!-- <div class="col-md-6">
																<div class="form-group">
																	<label>One Time Code (OTC)</label>
																	<input type="text" name="otc" value="" id="otc" maxlength="6" class="form-control input-sm uppercase">
																</div>
															</div> -->
															<div class="col-md-6">
																<div class="form-group">
																	<label>Assessment Name<span class="required"> * </span></label>
																	<input type="text" name="assessment_name" id="assessment_name" maxlength="72" class="form-control input-sm" value="">
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label>Report Type<span class="required"> * </span></label>
																	<select id="report_type" name="report_type" class="form-control input-sm select2" placeholder="Please select">
																		<?php foreach ($report_type as $rt) { ?>
																			<option value="<?= $rt->id; ?>"> <?php echo $rt->description; ?></option>
																		<?php } ?>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-3">
																<div class="form-group">
																	<label class="control-label">Start Date<span class="required"> * </span></label>
																	<div class="input-group date form_datetime">
																		<input type="text" size="16" class="form-control" name="start_date" id="start_date" autocomplete="off">
																		<span class="input-group-btn">
																			<button class="btn default date-set" type="button" id="st_date"><i class="fa fa-calendar"></i></button>
																		</span>
																	</div>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label class="control-label">End Date<span class="required"> * </span></label>
																	<div class="input-group date form_datetime">
																		<input type="text" size="16" class="form-control" name="end_date" id="end_date" autocomplete="off">
																		<span class="input-group-btn">
																			<button class="btn default date-set" type="button" id="ed_date"><i class="fa fa-calendar"></i></button>
																		</span>
																	</div>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label>Time Limit<span class="required"> * </span></label>
																	<input type="number" name="time_limit" id="time_limit" min="1" max="300" class="form-control input-sm" value="" required>
																	<span class="text-muted" style="color:red" id="file_desc">(Time limit cannot exceed 300 seconds.)</span>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label>Number of attempts<span class="required"> * </span></label>
																	<input type="number" name="number_attempts" id="number_attempts" min="1" class="form-control input-sm" value="1">
																</div>
															</div>
															<input type="hidden" name="ratingstyle" id="ratingstyle" class="form-control input-sm" value="2">
															</div>
														<!--Added below lines-->
														<div class="row" id="text_area">
															<div class="col-md-6">
																<div class="form-group">
																	<label>Description</label>
																	<textarea type="text" name="description" id="description" cols="3" rows="3" class="form-control input-sm"><p>Dear User, <br></p><p>We will be simulating a real world situation and a series of questions will be shot at you. <br></p><p>(1) Make sure you hold the phone close, so assessors can hear clearly;<br></p><p> (2) Speak Clearly<br></p>
                                                                        </textarea>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label>Instruction<span class="required"> * </span></label>
																	<textarea type="text" name="instruction" id="instruction" cols="3" rows="3" class="form-control input-sm"><p>(1) Make sure you hold the phone close, so assessors can hear clearly; <br></p><p>(2) Speak Clearly <br></p><p>(3)Hold the phone properly, so that you are clearly visible;<br></p><p> (4) There are number of allowed attempt. Do not submit the video, unless you are sure this is your best attempt<br></p><p> Best of luck. You are Going Live now! <br></p><p>Press Begin when you are ready.</p></textarea>
																</div>
															</div>
														</div>
													</fieldset>

													<div class="row">
														<div class="col-md-12 text-right">
															<button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave();">
																<span class="ladda-label">Save & Next</span>
															</button>
															<a href="<?php echo site_url("create_trinity"); ?>" class="btn btn-default btn-cons">Cancel</a>
														</div>
													</div>
												</div>
												<!--Map Persona-->
												<div class="tab-pane" id="tab_map_persona">
													<!-- Mapping persona Content -->
												</div>
												<!-- End -->
												<!-- Mapping Script-->
												<div class="tab-pane" id="tab_mapping_script">
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label>Select Script<span class="required"> * </span></label>
																<select name="script" id="script" class="form-control input-sm select2" onchange="script_based_question()">
																	<option value="">Please select</option>
																	<?php foreach ($map_script as $ms) { ?>
																		<option value="<?php echo $ms->id ?>"><?php echo $ms->script_title ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
													</div>
													<div class="row" id="sit-limit" style="display:none;">
														<div class="col-md-4">
															<div class="form-group">
																<label>Situation</label>
																<select name="situation" id="situation" class="form-control input-sm select2">
																	<option value="">Please select</option>
																</select>
															</div>
														</div>
														<div class="col-md-4">
															<div class="form-group">
																<label>Compulsory question limit<span class="required"> * </span></label>
																<input type="number" name="c_question_limit" id="c_question_limit" min="1" class="form-control input-sm">
															</div>
														</div>
													</div>
													<!-- datatable reload -->
													<div class="row margin-top-10">
														<div class="col-lg-12" id="question_ans_table"> </div>
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
													<!-- datatable reload -->

													<div class="row">
														<div class="col-md-12 text-right">
															<button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave();">
																<span class="ladda-label">Save & Next</span>
															</button>
															<a href="<?php echo site_url("create_trinity"); ?>" class="btn btn-default btn-cons">Cancel</a>
														</div>
													</div>
												</div>
												<!-- End -->

												<!-- Map Script -->
												<div class="tab-pane" id="tab_mapping_goal">
													<h1>Mapping Goal</h1>
												</div>
												<!-- End -->
												<!-- new Tab added -->
												<div class="tab-pane" id="tab_mapping_manager">
													<div class="form-body">
														<?php
														if ($errors == "") {
															$errors = validation_errors();
														}
														if ($errors) {
														?>
															<div style="display: block;" class="alert alert-danger display-hide">
																<button class="close" data-close="alert"></button>
																You have some form errors. Please check below.
																<?php echo $errors; ?>
															</div>
														<?php } ?>
														<div class="row">
															<div class="col-md-4">
																<div class="form-group">
																	<label class="control-label col-md-5">Last Assessor Date :<span class="required"> * </span></label>
																	<div class="input-group date form_datetime">
																		<input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off">
																		<span class="input-group-btn">
																			<button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
																		</span>
																	</div>
																</div>
															</div>
														</div>
														<div class="row margin-bottom-10">
															<span class="col-md-2 map-title">
																Mapping Manager
															</span>
															<div class="col-md-10 text-right">

																<button type="button" id="custom_remove" name="custom_remove" data-loading-text="Please wait..." accesskey="" class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="RemoveAllMappingManagers(1);" style="  margin-right: 10px;">
																	<span class="ladda-label"><i class="fa fa-remove"></i>&nbsp; Remove</span>
																</button>&nbsp;
																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/importManager' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
																&nbsp;

																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/addManagers' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Manager </a>
															</div>
														</div>
														<div class="row ">
															<div class="col-md-12" id="assessment_panel">
																<table class="table  table-bordered table-hover table-checkable order-column" id="ManagersTable">
																	<thead>
																		<tr>
																			<th>
																				<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
																					<input type="checkbox" class="Mapping_all group-checkable chk_mg" name="chk_mg" id="chk_mg" data-set="#ManagersTable .checkboxes" />
																					<span></span>
																				</label>
																			</th>
																			<th>Trainer ID</th>
																			<th>Trainer Region</th>
																			<th>Username</th>
																			<th>Name</th>
																			<th>Email</th>
																			<th>Designation</th>
																		</tr>
																	</thead>
																	<tbody></tbody>
																</table>
															</div>
														</div>
													</div>
													<hr>
													<div class="row margin-bottom-20"> </div>
													<div class="form-body">
														<div class="row margin-bottom-10">
															<span class="col-md-3 map-title">
																Mapping Supervisor
															</span>
															<div class="col-md-9 text-right">
																<button type="button" id="custom_remove" name="custom_remove" data-loading-text="Please wait..." accesskey="" class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="RemoveAllMappingSupervisors(1);" style="  margin-right: 10px;">
																	<span class="ladda-label"><i class="fa fa-remove"></i>&nbsp; Remove</span>
																</button>&nbsp;
																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/importSupervisor' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
																&nbsp;

																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/addSupervisors' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Supervisor </a>
															</div>
														</div>
														<div class="row ">
															<div class="col-md-12" id="assessment_panel">
																<table class="table  table-bordered table-hover table-checkable order-column" id="SupervisorTable">
																	<thead>
																		<tr>
																			<th>
																				<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
																					<input type="checkbox" class="Mappsuper_all group-checkable chk_sp" name="chk_sp" id="chk_sp" data-set="#SupervisorTable .checkboxes" />
																					<span></span>
																				</label>
																			</th>
																			<th>Trainer ID</th>
																			<th>Username</th>
																			<th>Name</th>
																			<th>Email</th>
																			<th>Designation</th>
																		</tr>
																	</thead>
																	<tbody></tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12 text-right">
															<button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave();">
																<span class="ladda-label">Save & Next</span>
															</button>
															<a href="<?php echo site_url("create_trinity"); ?>" class="btn btn-default btn-cons">Cancel</a>
														</div>
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
			</div>
		</div>
	</div>
	<div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body" id="modal-body">
					<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
					<span>
						&nbsp;&nbsp;Loading... </span>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="Mymodalid" role="basic" aria-hidden="true" data-width="400">
		<div class="modal-dialog modal-lg" style="width:524px;">
			<div class="modal-content">
				<div class="modal-body" id="modal-body1">
					<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
					<span>
						&nbsp;&nbsp;Loading... </span>
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
	<script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
	<script>
		var AddEdit = "A";
		var AssessmentForm = $('#AssessmentForm');
		var form_error = $('.alert-danger', AssessmentForm);
		var form_success = $('.alert-success', AssessmentForm);
		var Totalqstn = 1;
		var Totalpara = 0;
		var NewManagersArrray = [];
		var NewSupervisorsArrray = [];
		var TotalqstnArray = [];
		var NewQuestionArray = [];
		var Selected_QuestionArray = [];
		var Selected_ParameterArray = [];
		var Base_url = "<?php echo base_url(); ?>";
		var Encode_id = "";
		var company_id = '<?php echo $Company_id; ?>';
		var Unique_paramters = [];

		var TempSubParameterArray = [];
		//document.getElementById("ranking").checked = false;
		jQuery(document).ready(function() {
			$(".form_datetime").datetimepicker({
				autoclose: true,
				format: "dd-mm-yyyy hh:ii"
			});
			CKEDITOR.replace('instruction', {
				toolbar: [{
						name: 'styles',
						items: ['Styles', 'Format']
					},
					{
						name: 'basicstyles',
						items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
					},
					{
						name: 'paragraph',
						items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
					},
					{
						name: 'links',
						items: ['Link', 'Unlink', 'Anchor']
					}
				],
			});
			CKEDITOR.replace('description', {
				toolbar: [{
						name: 'styles',
						items: ['Styles', 'Format']
					},
					{
						name: 'basicstyles',
						items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
					},
					{
						name: 'paragraph',
						items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
					},
					{
						name: 'links',
						items: ['Link', 'Unlink', 'Anchor']
					}
				],
			});

			CKEDITOR.config.autoParagraph = false;
		});

		$('input:checkbox').click(function() {
			$('input:checkbox').not(this).prop('checked', false);
		});
		// $('#isweights').click(function() {
		// 	if ($(this).prop("checked") == true) {
		// 		$('#weightWindow').show();
		// 	} else {
		// 		$('#weightWindow').hide();
		// 	}
		// });
	</script>
	<script src="<?php echo $asset_url; ?>assets/customjs/create_trinity.js" type="text/javascript"></script>
</body>

</html>