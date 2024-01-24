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
	<style>
		.map-title {
			font-size: 16px;
			font-weight: 600;
		}
		.ps-container{
			display: inline-flex;
			width: 100%;
		}
		.ps-seperator{
			color: black;
			float: left;
			width: 1%;
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.parameter-badge{
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #aee5ea;
			padding: 1px 10px 1px 10px;
			background: #dbfcff;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.sub-parameter-badge{
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #ffc9bc;
			padding: 1px 10px 1px 10px;
			background: #ffefeb;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.parameter-weight-badge{
			color: black;
			float: left;
			width: 20%;
			border: 1px solid #fff7bc;
			padding: 1px 10px 1px 10px;
			background: #feffef;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.keysent-badge{
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
							<a href="<?php echo $base_url ?>assessment_create" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
												<li>
													<a href="#tab_mapping_manager" data-toggle="tab">Mapping Managers</a>
												</li>
												<li>
													<a href="javascrip:void(0);">Allowed Users</a>
												</li>
												<li>
													<a href="javascrip:void(0);">User-Manager Mapping</a>
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
															<div class="col-md-4">
																<div class="form-group">
																	<label class="">Assessment Type<span class="required"> * </span></label>
																	<select id="assessment_type" name="assessment_type" class="form-control input-sm select2" placeholder="Please select" onchange="assessment_type_change()">
																		<option value="">Please Select</option>
																		<option value="1">Roleplay</option>
																		<option value="2">Spotlight</option>
																	</select>
																</div>
															</div>
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
															<div class="col-md-2">
																<div class="form-group">
																	<label>One Time Code (OTC)</label>
																	<input type="text" name="otc" value="" id="otc" maxlength="6" class="form-control input-sm uppercase">
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label>Assessment Name<span class="required"> * </span></label>
																	<input type="text" name="assessment_name" id="assessment_name" maxlength="72" class="form-control input-sm" value="">
																</div>
															</div>
															<!-- <div class="col-md-3">
																<div class="form-group">
																	<label>Assessment Type<span class="required"> * </span></label>
																	<select id="assessment_type" name="assessment_type" class="form-control input-sm select2" placeholder="Please select" onchange="AssessmentChange()">
																		< ?php foreach ($assessment_type as $at) { ?>
																				<option value="< ?= $at->id; ?>" < ?php echo ($at->default_selected ? 'selected' : ''); ?>>< ?php echo $at->description; ?></option>
																		< ?php } ?>
																	</select>
																</div>
															</div> -->
															<!-- Default assessment type value set as 2 -->
															<!-- <input type="hidden" name="assessment_type" id="assessment_type" class="form-control input-sm" value="2"> -->
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
															<!-- <div class="col-md-3">
																<div class="form-group">
																	<label>Question type<span class="required"> * </span></label>
																	<select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select" onchange="getquestion_type();">
																		<option value="0" selected>Question</option>
																		<option value="1">Situation</option>
																	</select>
																</div>
															</div> -->
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
															<!--                                                            <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                    <label class="control-label">Last Assessor Date<span class="required"> * </span></label>                                                                    
                                                                        <div class="input-group date form_datetime">
                                                                            <input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off">
                                                                            <span class="input-group-btn">
                                                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                        </div>                                                                        
                                                                    </div>
                                                                </div>-->
																<div class="col-md-3">
																<div class="form-group">
																	<label>Number of attempts<span class="required"> * </span></label>
																	<input type="number" name="number_attempts" id="number_attempts" min="1" class="form-control input-sm" value="1">
																</div>
															</div>
															<div class="col-md-3" id="q_limit">
																<div class="form-group">
																	<label>Question Limits</label>
																	<input type="number" name="question_limit" id="question_limit" min="1" class="form-control input-sm">
																</div>
															</div>
															<!-- <div class="col-md-3">
																<div class="form-group">
																	<label>Rating Type<span class="required"> * </span></label>
																	<select id="ratingstyle" name="ratingstyle" class="form-control input-sm select2" placeholder="Please select">
																		<option value="1">Star Rating</option>
																		<option value="2" selected>Slider</option>
																	</select>
																</div>
															</div> -->
															<input type="hidden" name="ratingstyle" id="ratingstyle" class="form-control input-sm" value="2">
															<!-- <div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 110px;">
																<div class="form-group">
																	<label class="mt-checkbox mt-checkbox-outline" for="is_preview"> Is preview?
																		<input id="is_preview" name="is_preview" type="checkbox" value="1" checked=""><span></span>
																	</label>
																</div>
															</div> -->
															<!-- <div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 110px;">
															<div class="form-group">
																	<label class="mt-checkbox mt-checkbox-outline" for="ranking"> Ranking
																		<input id="ranking" name="ranking" type="checkbox" value="1" checked=""><span></span>
																	</label>
																</div>
																
															</div> -->
															
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
													<fieldset id="P_table">
														<legend id="title"> </legend>

														<div class="row" style="overflow-x: scroll;overflow-y: hidden;">
															<div class="col-md-12" style="width:100%;">
																<table class="table table-bordered table-hover" id="VQADatatable" name="VQADatatable" style="width:100%;min-width:1200px;">
																	<thead>
																		<tr>
																			<th width="70px" id="label_dyamic2">Default</th>
																			<th width="250px" id="label_dyamic">Questions</th>
																			<!-- <th width="150px">AI Methods</th> -->
																			<th width="100px">Language</th>
																			<th width="400px">Parameter/ Sub Parameters/ Weights
																				<span style="float:right;padding-right:15px;">Sentence / Keyword</span>
																			</th>
																			<!-- <th width="200px">Sentence / Keyword</th> -->
																			<!-- <span style="float:left;font-size:14px;color:red;text-transform: capitalize;">Use | (pipe line) for multiple.</span> -->
																			<th width="300px"><a class="btn btn-primary btn-xs btn-mini " id="btnaddpanel3" href="<?php echo base_url() . 'assessment_create/add_questions/' ?>" accesskey="" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus" style="margin: 4px 3px 4px 5px;"></i>&nbsp;</a></th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr id="Row-0">
																			<td colspan="7" style="text-align: center;"> Please Add Question..</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>
														<!-- <div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label class="mt-checkbox mt-checkbox-outline" for="isweights"> Is Unequal Weights?
																		<input id="isweights" name="isweights" type="checkbox" value="1"><span></span>
																	</label>
																</div>
															</div>
														</div>
														<div class="row" id="weightWindow" style="display:none;">
															<div class="col-md-6">
																<table class="table table-bordered table-hover" id="weights_table" name="weights_table" width="100%">
																	<thead>
																		<tr>
																			<th width="45%" id="label_dyamic">Parameter Name</th>
																			<th width="25%">Weights (%)</th>
																		</tr>
																	</thead>
																	<tbody>

																	</tbody>
																</table>
															</div>
														</div> -->
													</fieldset>
													<div class="row">
														<div class="col-md-12 text-right">
															<button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave();">
																<span class="ladda-label">Save & Next</span>
															</button>
															<a href="<?php echo site_url("assessment_create"); ?>" class="btn btn-default btn-cons">Cancel</a>
														</div>
													</div>
												</div>
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
																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'assessment_create/importManager' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
																&nbsp;

																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'assessment_create/addManagers' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Manager </a>
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
																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'assessment_create/importSupervisor' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
																&nbsp;

																<a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'assessment_create/addSupervisors' ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Supervisor </a>
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
															<a href="<?php echo site_url("assessment_create"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
	<script src="<?php echo $asset_url; ?>assets/customjs/assessment_create_validation.js" type="text/javascript"></script>
</body>

</html>
