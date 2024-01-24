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
													if (isset($errors) && $errors == "") {
														$errors = validation_errors();
													}
													if (isset($errors)) {
													?>
														<div style="display: block;" class="alert alert-danger display-hide">
															<button class="close" data-close="alert"></button>
															You have some form errors. Please check below.
															<?php echo $errors; ?>
														</div>
													<?php } ?>

													<fieldset>
														<legend>General Information:</legend>
														<?php if ($Company_id == "") { ?>
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<label class="">Company Name<span class="required"> * </span></label>
																		<select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
																			<option value="">Please Select</option>
																			<?php foreach ($CompnayResultSet as $cmp) { ?>
																				<option value="<?= $cmp->id; ?>" <?php echo (isset($result) && $result->company_id == $cmp->id ? 'Selected' : ''); ?>><?php echo $cmp->company_name; ?></option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
															</div>
														<?php } ?>
														<div class="row">
															<div class="col-md-2">
																<div class="form-group">
																	<label>One Time Code (OTC)</label>
																	<input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo isset($result) ? $result->id : '' ?>" class="form-control input-sm uppercase">
                                                                    <input type="text" name="otc" id="otc" value="<?php echo isset($result) ? $result->code : '' ?>" maxlength="6" class="form-control input-sm uppercase">
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label>Assessment Name<span class="required"> * </span></label>
																	<input type="text" name="assessment_name" id="assessment_name" maxlength="255" class="form-control input-sm" value="<?php echo isset($result) ? $result->assessment : '' ?>">
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label>Assessment Type<span class="required"> * </span></label>
																	<select id="assessment_type" name="assessment_type" class="form-control input-sm select2" placeholder="Please select" onchange="AssessmentChange()">
																		<?php foreach ($assessment_type as $at) { ?>
																			<option value="<?= $at->id; ?>" <?php echo (isset($result) && $result->assessment_type == $at->id ? 'selected' : '') ?>><?php echo $at->description; ?></option>
																		<?php } ?>
																	</select>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label>Question type<span class="required"> * </span></label>
																	<select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select" onchange="getquestion_type();">
																		<option value="0" <?php echo (isset($result) && $result->is_situation == 0) ? 'selected' : ''; ?>>Question</option>
																		<!-- <option value="1" <?php echo (isset($result) && $result->is_situation == 1) ? 'selected' : ''; ?>>Situation</option> -->
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-3">
																<div class="form-group">
																	<label class="control-label">Start Date<span class="required"> * </span></label>
																	<div class="input-group date form_datetime">
																		<input type="text" size="16" class="form-control" name="start_date" id="start_date" autocomplete="off"  value="<?php echo isset($result) ? date("d-m-Y H:i", strtotime($result->start_dttm)) : '' ?>">
																		<span class="input-group-btn">
																			<button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
																		</span>
																	</div>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label class="control-label">End Date<span class="required"> * </span></label>
																	<div class="input-group date form_datetime">
																		<input type="text" size="16" class="form-control" name="end_date" id="end_date" autocomplete="off"  value="<?php echo isset($result) ? date("d-m-Y H:i", strtotime($result->end_dttm)) : '' ?>">
																		<span class="input-group-btn">
																			<button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
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
															<div class="col-md-2" style="padding-left: 0px; width: 124px;">
																<div class="form-group">
																	<label>Number of attempts<span class="required"> * </span></label>
																	<input type="number" name="number_attempts" id="number_attempts" min="1" class="form-control input-sm" value="<?php echo isset($result) ? $result->number_attempts : '1'; ?>">
																</div>
															</div>
															<div class="col-md-2">
																<div class="form-group">
																	<label>Rating Type<span class="required"> * </span></label>
																	<select id="ratingstyle" name="ratingstyle" class="form-control input-sm select2" placeholder="Please select">
																		<option value="1" <?php echo (isset($result) && $result->ratingstyle == 1) ? 'selected' : ''; ?>>Star Rating</option>
                                                                        <option value="2" <?php echo (isset($result) && $result->ratingstyle == 2) ? 'selected' : ''; ?>>Slider</option>
																	</select>
																</div>
															</div>
															<div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 110px;">
																<div class="form-group">
																	<label class="mt-checkbox mt-checkbox-outline" for="is_preview"> Is preview?
																		<input id="is_preview" name="is_preview" type="checkbox" value="1" <?php echo (isset($result) && $result->is_preview == 1) ? 'checked' : ''; ?> checked=""><span></span>
																	</label>
																</div>
																
															</div>
															<!-- <div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 110px;">
															<div class="form-group">
																	<label class="mt-checkbox mt-checkbox-outline" for="ranking"> Ranking
																		<input id="ranking" name="ranking" type="checkbox" value="1" checked=""><span></span>
																	</label>
																</div>
																
															</div> -->
															
														</div>
														<!--Added below lines-->
														<div class="row">    
															
															<div class="col-md-3">
																<div class="form-group">
																	
																	<label>Report Type<span class="required"> * </span></label>
																	<select id="report_type" name="report_type" class="form-control input-sm " placeholder="Please select">
																		<?php foreach ($report_type as $rt) { ?>
																			<option value="<?= $rt->id; ?>" <?php echo (isset($result) && $result->report_type == $rt->id ? 'selected' : '') ?>> <?php echo $rt->description; ?></option>
																		<?php } ?>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label>Description</label>
																	<textarea type="text" name="description" id="description" cols="3" rows="3" class="form-control input-sm">
																		<?php
																			if (isset($result->description)) {
																				echo $result->description;
																			} else {
																		?><p>Dear User, <br></p><p>We will be simulating a real world situation and a series of questions will be shot at you. <br></p><p>(1) Make sure you hold the phone close, so assessors can hear clearly;<br></p><p> (2) Speak Clearly<br></p>
																		<?php } ?>
																	</textarea>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label>Instruction<span class="required"> * </span></label>
																	<textarea type="text" name="instruction" id="instruction" cols="3" rows="3" class="form-control input-sm">
																		<?php
																			if (isset($result->instruction)) {
																				echo $result->instruction;
																			} else {
																		?><p>(1) Make sure you hold the phone close, so assessors can hear clearly; <br></p><p>(2) Speak Clearly <br></p><p>(3)Hold the phone properly, so that you are clearly visible;<br></p><p> (4) There are number of allowed attempt. Do not submit the video, unless you are sure this is your best attempt<br></p><p> Best of luck. You are Going Live now! <br></p><p>Press Begin when you are ready.</p>
																		<?php } ?>
																	</textarea>
																</div>
															</div>
														</div>
													</fieldset>
													<fieldset>
														<legend>Mapping Questions/Situation:</legend>

														<div class="row" style="overflow-x: scroll;overflow-y: hidden;">
															<div class="col-md-12" style="width:100%;">
																<table class="table table-bordered table-hover" id="VQADatatable" name="VQADatatable" style="width:100%;min-width:1200px;">
																	<thead>
																		<tr>
																			<th width="250px" id="label_dyamic">Questions</th>
																			<!-- <th width="150px">AI Methods</th> -->
																			<th width="100px">Language</th>
																			<th width="400px">Parameter/ Sub Parameters/ Weights
																			<span style="float:right;padding-right:15px;">Sentence / Keyword</span> </th>
																			<!-- <th width="200px">Sentence / Keyword</th> -->
																			<!-- <span style="float:left;font-size:14px;color:red;text-transform: capitalize;">Use | (pipe line) for multiple.</span> -->
																			<th width="300px"><a class="btn btn-primary btn-xs btn-mini " id="btnaddpanel3" href="<?php echo base_url() . 'assessment_create/add_questions/'. (isset($result) ? base64_encode($result->id) : ''); ?>" accesskey="" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus" style="margin: 4px 3px 4px 5px;"></i>&nbsp;</a></th>
																		</tr>
																	</thead>
																	<tbody>
																		<!-- <tr id="Row-0">
																			<td colspan="7" style="text-align: center;"> Please Add Question..</td>
																		</tr> -->
																		<?php
                                                                        $key = 0;
                                                                        if (isset($result) && count($assessment_trans) > 0) {
                                                                            foreach ($assessment_trans as $ky => $tr_id) {
                                                                                $key++;
                                                                                $lockFlag = (in_array($tr_id->question_id, $question_play_array) ? true : false);
                                                                        ?>
                                                                                <tr id="Row-<?php echo $key; ?>">
                                                                                    <td> <span id="question_text_<?php echo $key; ?>"><?php echo $tr_id->question; ?></span>
                                                                                        <?php if (!$lockFlag) { ?>
                                                                                            <input type="hidden" value="<?php echo $tr_id->question_id; ?>" id="question_id<?php echo $key; ?>" name="Old_question_id[<?php echo $tr_id->id ?>]">
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <!-- <td>
            <input type="hidden" id="txt_trno<?php //echo $key; 
                                                ?>" name="txt_trno_<?php //echo $key; 
                                                                    ?>" class="txt_trno" value="<?php //echo $key; 
                                                                                                                                        ?>" >
            <select id="aimethods_id<?php //echo $key; 
                                    ?>" name="aimethods_id<?php //echo $key; 
                                                            ?>[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" multiple <?php //echo(in_array($tr_id->question_id, $question_play_array) ? 'disabled':'')
                                                                                                                                                                                                                    ?>>    
                                                                                        <?php
                                                                                        //if (count($aimeth_result) > 0) { 
                                                                                        // foreach ($aimeth_result as $aim_data) { 
                                                                                        // 	if (isset($unique_aimethods[$key-1])){
                                                                                        // 		$aimethods_array =  explode(',', $unique_aimethods[$key-1]->ai_methods);
                                                                                        // 	}else{
                                                                                        // 		$aimethods_array = [];
                                                                                        // 	}
                                                                                        ?>
                <option value="<?php //echo $aim_data->id; 
                                ?>" <?php //echo (in_array($aim_data->id,$aimethods_array)? 'selected' : '') 
                                    ?>><?php //echo $aim_data->description; 
                                                                                        ?></option>
                                                                                                <?php
                                                                                                //}
                                                                                                //} 
                                                                                                ?>
            </select> 
        </td> -->
                                                                                    <td>
                                                                                        <select id="language_id<?php echo $key; ?>" name="language_id<?php echo $key; ?>" class="form-control input-sm select2 language_id" placeholder="Please select" style="width:100%" <?php echo (in_array($tr_id->question_id, $question_play_array) ? 'disabled' : '') ?>>
                                                                                            <?php
                                                                                            if (count($language_result) > 0) {
                                                                                                foreach ($language_result as $language_data) {
                                                                                            ?>
                                                                                                    <option value="<?php echo $language_data->id; ?>" <?php echo ((isset($unique_aimethods[$key - 1]) && $language_data->id == $unique_aimethods[$key - 1]->language_id) ? 'selected' : '') ?>><?php echo $language_data->name; ?></option>
                                                                                            <?php
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div id="paramsub<?php echo $key; ?>"></div>
                                                                                        <select id="parameter_id<?php echo $key; ?>" name="Old_parameter_id<?php echo $tr_id->id; ?>[]" multiple="" style="display:none;" onchange="getUnique_paramters()">
                                                                                            <?php
                                                                                            if (count($Parameter) > 0) {
                                                                                                foreach ($Parameter as $p) {
                                                                                            ?>
                                                                                                    <option value="<?php echo $p->id; ?>" <?php echo (in_array($p->id, $parameter_array[$tr_id->question_id]) ? 'selected' : '') ?>><?php echo $p->description; ?></option>
                                                                                            <?php
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>

                                                                                        <a class="btn btn-success btn-sm" href="<?php echo ($lockFlag ? 'javascript:void(0);' : base_url() . 'assessment_create/add_parameters/' . $key . '/' . $result->assessment_type . '/' . $result->company_id); ?>" accesskey="" <?php echo ($lockFlag ? 'disabled' : 'data-target="#LoadModalFilter" data-toggle="modal"') ?>>Manage Parameters </a>
                                                                                        <a class="btn btn-success btn-sm" id="btnaddpanel3" href="<?php echo ($lockFlag ? 'javascript:void(0);' : base_url() . 'assessment_create/edit_questions/' . $key); ?>" <?php echo ($lockFlag ? 'disabled' : 'data-target="#LoadModalFilter" data-toggle="modal"') ?>><i class="fa fa-pencil"></i> </a>
                                                                                        <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)" <?php echo ($lockFlag ? 'disabled' : '') ?>><i class="fa fa-times"></i></button>
                                                                                    </td>
                                                                                    <input type="hidden" value="<?php echo $tr_id->id ?>" name="rowid[]">
                                                                                </tr>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
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
															<button type="button" id="preview" name="preview" class="btn btn-default btn-cons" data-style="expand-left" onclick="preview_assessment()">
                                                                <span class="ladda-label">Preview</span>
                                                            </button>
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
																		<input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off"  value="<?php echo isset($result) ? date("d-m-Y H:i", strtotime($result->assessor_dttm)) : '' ?>">
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
															<button type="button" id="preview" name="preview" class="btn btn-default btn-cons" data-style="expand-left" onclick="preview_assessment()">
                                                                <span class="ladda-label">Preview</span>
                                                            </button>
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
	<?php
    $row_id = array();
	if(isset($assessment_trans) && !empty($assessment_trans)){
		foreach ($assessment_trans as $p) {
			$row_id[] = $p->id;
		}
    }
    ?>
	<script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
	<script>
		var roww_id = <?php echo json_encode($row_id); ?>;
		var AddEdit = "A";
		var AssessmentForm = $('#AssessmentForm');
		var form_error = $('.alert-danger', AssessmentForm);
		var form_success = $('.alert-success', AssessmentForm);
		// var Totalqstn = 1;
		var Totalqstn = <?php echo $key + 1; ?>;
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
		var temp_data_save = [];
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
		// start here function 
        function preview_assessment() {
			if (Totalqstn == 1) {
				ShowAlret("Please Add Question & Parameter..", "error");
				return false;
			}
            var assessment_id = $('#assessment_id').val();
            var flag = 2;
            var new_url = Base_url + 'assessment_create/reports_preview/' + assessment_id + '/' + flag;
            window.open(new_url, '_blank');
        }

        $("form :input").change(function() {
            temp_data_save();
            // UpdateAssessment();
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
	<script>
        <?php
        if (isset($parameter_subparameter) && count((array) $parameter_subparameter) > 0) {
            foreach ($parameter_subparameter as $pstran) {
        ?>
                var push_value = {};
                push_value['language_id'] = "<?php echo $pstran->language_id; ?>";
                push_value['txn_id'] = "<?php echo $pstran->txn_id; ?>";
                push_value['parameter_id'] = "<?php echo $pstran->parameter_id; ?>";
                push_value['parameter_name'] = "<?php echo $pstran->parameter_name; ?>";
                push_value['parameter_label_id'] = "<?php echo $pstran->parameter_label_id; ?>";
                push_value['parameter_label_name'] = "<?php echo $pstran->parameter_label_name; ?>";
                push_value['subparameter_id'] = "<?php echo $pstran->sub_parameter_id; ?>";
                push_value['subparameter_name'] = "<?php echo $pstran->sub_parameter_name; ?>";
                push_value['type_id'] = "<?php echo $pstran->type_id > 0 ? $pstran->type_id : ''; ?>";
                push_value['type_name'] = "<?php echo $pstran->type_name; ?>";
                push_value['sentence_keyword'] = "<?php echo $pstran->sentence_keyword; ?>";
                push_value['parameter_weight'] = "<?php echo $pstran->parameter_weight; ?>";
                TempSubParameterArray.push(push_value);
                div_html = printonscreen_keyword_sentence(<?php echo $pstran->txn_id; ?>, <?php echo $pstran->parameter_id; ?>);
                console.log(div_html);
                var div_element = "#paramsub" + <?php echo $pstran->txn_id; ?>;
                $(div_element).empty();
                $(div_element).html('');
                $(div_element).html(div_html);

        <?php
            }
        }
        ?>
    </script>
</body>

</html>