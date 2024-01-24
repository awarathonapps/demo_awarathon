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
        .dashboard-stat.aiboxes .more{
            color: #db1f48;
            background-color: #004369;
			opacity: 1;
        }
		.dashboard-stat.aiboxes .more:hover{
			opacity: 1;
		}
        .dashboard-stat .details .number{
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
                                <i class="fa fa-circle"></i>
                                <span>API Documantation</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>Assessment API</span>
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
					
                    <div class="row margin-top-10 ">
						<div class="col-md-12">
							<input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id;?>" />
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption caption-font-24">
										Assessment API
										<div class="tools"> </div>  
									</div>
								</div>
								<div class="portlet-body">   
									<div class="tabbable-line tabbable-full-width">
										<ul class="nav nav-tabs" id="tabs">
											<li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
												<a href="#section-candidates" data-toggle="tab">Assessment Metadata</a>
											</li>
											<li <?php echo ($step == 3 ? 'class="active"' : ''); ?>>
											<a href="#ideal-video" data-toggle="tab">Assessment Process Data</a>
											</li>
											<!-- <li <?php //echo ($step == 4 ? 'class="active"' : ''); ?>>
												<a href="#tab_template" data-toggle="tab">Email Template</a>
											</li>
											<li>
												<a href="#tab_email_send" data-toggle="tab">Send</a>
											</li>                                                 -->
										</ul>
										<div class="tab-content">
										<!-- Assessment Metadata -->
											<div class="tab-pane <?php echo ($step == 1 ? 'active' : 'mar'); ?>"  id="section-candidates">
												<div class="form-body">
													<div class="row ">
														<div class="col-md-12" id="assessment_panel_view">
														    <!-- <h3>Assessment Metadata	</h3> -->
															<!-- start content -->
															<div class="col-lg-9 col-md-9 col-sm-12 mid-space">
																<div class="right-content aw-dashboard">
																	<div class="row">
																		<div class="col-lg-9 col-md-9 col-sm-12">
																			<div class="api-details">
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Get list of Assessments</p>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Purpose:-</h4>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>To get list of assessments available for your company domain</p><br>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Endpoints:-</h4>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>POST api/get_assesment_metadata</p><br>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Request URL:-</h4>
																				<p class="mb-25" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px;font-weight: 500;'><a href="https://restapi.awarathon.com/api/get_assesment_metadata" target="_blank">https://restapi.awarathon.com/api/get_assesment_metadata</a></p><br>
																				
																				<h5 class="mb-15" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Request JSON: </h5>								
																				<div class="req-url-bx"><pre style="white-space: pre-line;">{
																					"payload":"eyJ0eXAiOiJqd3QiLxxxxxxxxxxxxxxxxxxxxTEh24ARl_u_uLyQA6M",
																					"token_no":"92.bb98eca7xxxxxxxx",
																					"from_date":"dd-mm-yyyy",
																					"to_date":"dd-mm-yyyy"
																					}</pre>
																				</div>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>payload: String, Mandatory<br>
																					Specify the payload retrieve from Authentication API (NOTE: We can provide link for Authentication Page)
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>token_no: String, Mandatory<br>
																					Specify the token number retrieve from Authentication API (NOTE: We can provide link for Authentication Page)
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>from_date: Date format, Not Mandatory<br>
																					You can mention date from which assessment data needs to be fetched
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>to_date: Date format, Not Mandatory<br>
																					You can mention date till which date assessment data needs to be fetched
																				</p>
																			</div>							
																		</div>
																		<div class="col-lg-12 col-md-12 col-sm-12">
																			<div class="api-details">
																				<h3 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Response JSON:</h3>								
																				<div class="req-url-bx">
																					<pre style="white-space: pre-line;">{
													"count": 2,
													"data": [
														{
															"assessment_id": "1",
															"assessment_name": "Awarathon Skill Assessment",
															"read_more": "<p>Dear User,</p>\r\n\r\n<p>We will be simulating a real world situation and a series of questions will be shot at you.</p>\r\n\r\n<p>(1) Make sure you hold the phone close, so assessors can hear clearly;</p>\r\n\r\n<p>(2) Speak Clearly</p>\r\n",
															"assessment_type": "2",
															"assessment_type_name": "VIDEO",
															"assessment_start_date": "24-08-2022 17:00:00",
															"assessment_end_date": "31-08-2022 18:00:00",
															"instruction": "<p>(1) Make sure you hold the phone close, so assessors can hear clearly;</p>\r\n\r\n<p>(2) Speak Clearly</p>\r\n\r\n<p>(3)Hold the phone properly, so that you are clearly visible;</p>\r\n\r\n<p>(4) There are number of allowed attempt. Do not submit the video, unless you are sure this is your best attempt</p>\r\n\r\n<p>Best of luck. You are Going Live now!</p>\r\n\r\n<p>Press Begin when you are ready.</p>\r\n",
															"attempts_allowed": "10",
															"total_question": 2,
															"assessment_status": "Expired",
															"mapped_question": [
																{
																	"question_id": "1",
																	"question": "What will be your response if the client says \"I like it but I am not sure if I want to buy the policy now?",
																	"format": "0",
																	"format_name": "Text"
																},
																{
																	"question_id": "2",
																	"question": "What will be your approach to resolve Ramesh#39;s concern &amp; how will you manage him when he is persistent to cancel the Insurance?\r\n\r\n<p>this is title for Test</p>\r\n\r\n<p>for test</p>",
																	"format": "0",
																	"format_name": "Text"
																}
															],
															"mapped_users": [
																{
																	"user_id": "7905",
																	"full_name": "Test 1",
																	"user_name": "121",
																	"email": test1@awarathon.com",
																	"manager_user_id": "320",
																	"manager_username": "13",
																	"manager_fullname": "Manager 1",
																	"manager_email": "manager1@gmail.com"
																}
															],
															"mapped_manager": [
																{
																	"user_id": "320",
																	"user_name": "13",
																	"full_name": "Manager 1",
																	"email": "manage1@gmail.com"
																}
															]
														},
														{
															"assessment_id": "2",
															"assessment_name": "Awarathon test2",
															"read_more": "<p>Dear User,</p>\r\n\r\n<p>We will be simulating a real world situation and a series of questions will be shot at you.</p>\r\n\r\n<p>(1) Make sure you hold the phone close, so assessors can hear clearly;</p>\r\n\r\n<p>(2) Speak Clearly</p>\r\n",
															"assessment_type": "2",
															"assessment_type_name": "VIDEO",
															"assessment_start_date": "10-08-2022 17:00:00",
															"assessment_end_date": "10-10-2022 22:00:00",
															"instruction": "<p>(1) Make sure you hold the phone close, so assessors can hear clearly;</p>\r\n\r\n<p>(2) Speak Clearly</p>\r\n\r\n<p>(3)Hold the phone properly, so that you are clearly visible;</p>\r\n\r\n<p>(4) There are number of allowed attempt. Do not submit the video, unless you are sure this is your best attempt</p>\r\n\r\n<p>Best of luck. You are Going Live now!</p>\r\n\r\n<p>Press Begin when you are ready.</p>\r\n",
															"attempts_allowed": "10",
															"total_question": 3,
															"assessment_status": "Live",
															"mapped_question": [
																{
																	"question_id": "200",
																	"question": "no_videos.png",
																	"format": "2",
																	"format_name": "Image"
																},
																{
																	"question_id": "190",
																	"question": "What are the Annual General Meetings? How     exactly your role is in AGM<p>this is tital for Test</p>\r\n\r\n<p>for test</p>",
																	"format": "0",
																	"format_name": "Text"
																},
																{
																	"question_id": "199",
																	"question": "https://player.vimeo.com/video/744137899?h=c3d9a98077",
																	"format": "1",
																	"format_name": "Video"
																}
															],
															"mapped_users": [
																{
																	"user_id": "7781",
																	"full_name": "Test 2",
																	"user_name": "2",
																	"email": test2@awarathon.com",
																	"manager_user_id": "22",
																	"manager_username": "Manager 2",
																	"manager_fullname": "Manager Test 2",
																	"manager_email": "manager2@awarathon.com"
																},
																{
																	"user_id": "7784",
																	"full_name": "Test 3",
																	"user_name": "001",
																	"email": "test3@awarathon.com",
																	"manager_user_id": "22",
																	"manager_username": "Manager 2",
																	"manager_fullname": "Manager Test 2",
																	"manager_email": "manager2@awarathon.com"
																},
																{
																	"user_id": "7785",
																	"full_name": "Test 4",
																	"user_name": "DP",
																	"email": "test4@awarathon.com",
																	"manager_user_id": "37",
																	"manager_username": "manager 3",
																	"manager_fullname": "Manager Test3",
																	"manager_email": "manager3@awarathon.com"
																},
																{
																	"user_id": "7786",
																	"full_name": "Test 5",
																	"user_name": "MEG",
																	"email": "test5@awarathon.com",
																	"manager_user_id": "37",
																	"manager_username": "Manager 3",
																	"manager_fullname": "Manager Test3",
																	"manager_email": "manager3@awarathon.com"
																}
															],
															"mapped_manager": [
																{
																	"user_id": "22",
																	"user_name": "Manager 2",
																	"full_name": "Manager Test 2",
																	"email": "manager2@awarathon.com"
																},                {
																	"user_id": "37",
																	"user_name": "Manager 3",
																	"full_name": "Manager Test 3",
																	"email": "manager3@awarathon.com"
																}
															]
														}
													],
													"success": true,
													"message": "Assessment data loaded successfully."
												}
																					</pre>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
           
														    <!-- end content -->
														</div>
													</div>
												</div>
										    </div>
											<!-- end Assessment Metadata -->
											<!-- Assessment Process Data -->
											<div class="tab-pane <?php echo ($step == 3 ? 'active' : 'mar'); ?>" id="ideal-video"> 
												<div class="form-body">
													<div class="row ">
														<div class="col-md-12" id="assessment_panel">
															<!-- <h3>Assessment Process Data</h3> -->
															<!-- start content -->
															<div class="col-lg-9 col-md-9 col-sm-12 mid-space">
																<div class="right-content aw-dashboard">
																	<div class="row">
																		<div class="col-lg-9 col-md-9 col-sm-12">
																		<div class="api-details">
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Get list of Assessments prossess data</p>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Purpose:-</h4>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>To get list of assessments prossess data available for your company domain</p><br>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Endpoints:-</h4>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>POST api/get_assesment_proccess_data</p><br>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Request URL:-</h4>
																				<p class="mb-25" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; font-weight: 500;'><a href="https://restapi.awarathon.com/api/get_assesment_proccess_data" target="_blank">https://restapi.awarathon.com/api/get_assesment_proccess_data</a></p>
																				<h5 class="mb-15" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Request JSON: </h5>								
																				<div class="req-url-bx"><pre style="white-space: pre-line;">{
																					"payload":"eyJ0eXAiOiJqd3QiLxxxxxxxxxxxxxxxxxxxxTEh24ARl_u_uLyQA6M",
																					"token_no":"92.bb98eca7xxxxxxxx",
																					"from_date":"dd-mm-yyyy",
																					"to_date":"dd-mm-yyyy"
																					}</pre>
																				</div>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>payload: String, Mandatory<br>
																					Specify the payload retrieve from Authentication API (NOTE: We can provide link for Authentication Page)
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>token_no: String, Mandatory<br>
																					Specify the token number retrieve from Authentication API (NOTE: We can provide link for Authentication Page)
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>from_date: Date format, Not Mandatory<br>
																					You can mention date from which assessment data needs to be fetched
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>to_date: Date format, Not Mandatory<br>
																					You can mention date till which date assessment data needs to be fetched
																				</p>
																			</div>							
																		</div>
																		<div class="col-lg-12 col-md-12 col-sm-12">
																			<div class="api-details">
																				<h3 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Response:</h3>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>-Response format is json</p>
																				<div class="req-url-bx">
																					<pre style="white-space: pre-wrap;">{
															"count": 2,
															"data": [
																{
																	"assessment_id": "184",
																	"assessment_name": "Awarathon Skill Assessment",
																	"assessment_type_name": "VIDEO",
																	"assessment_start_date": "24-08-2022 17:00:00",
																	"assessment_end_date": "31-08-2022 18:00:00",
																	"attempts_allowed": "10",
																	"total_question": 2,
																	"assessment_status": "Expired",
																	"prossess_data": [
																		{
																			"user_id": "7905",
																			"username": "Nikita Karmarkar",
																			"emp_code": "121",
																			"email": "nikita.karmarkar@awarathon.com",
																			"user_status": "Completed",
																			"attempts": "1",
																			"completed_date": "25-08-2022 12:14:04",
																			"last_attempt_date": "25-08-2022 12:11:23"
																		},
																		{
																			"user_id": "7906",
																			"username": "Nikita Karmarkar",
																			"emp_code": "212",
																			"email": "nikita.karmarkar12@awarathon.com",
																			"user_status": "Completed",
																			"attempts": "1",
																			"completed_date": "25-08-2022 12:40:05",
																			"last_attempt_date": "25-08-2022 12:37:32"
																		},
																		{
																			"user_id": "7827",
																			"username": "Priyanka Phadke",
																			"emp_code": "D_4",
																			"email": "priyankaphadke@gmail.com",
																			"user_status": "Completed",
																			"attempts": "3",
																			"completed_date": "25-08-2022 13:02:04",
																			"last_attempt_date": "25-08-2022 12:59:22"
																		},
																		{
																			"user_id": "7852",
																			"username": "Priyanka Phadke",
																			"emp_code": "D_40",
																			"email": "priyanka.phadke@gmail.com",
																			"user_status": "Incomplete",
																			"attempts": null,
																			"completed_date": "13-10-2022 10:20:09",
																			"last_attempt_date": "13-10-2022 10:20:09"
																		},
																		{
																			"user_id": "7862",
																			"username": "Priyanka Awarathon2",
																			"emp_code": "222",
																			"email": "priyanka1234@gmail.com",
																			"user_status": "Incomplete",
																			"attempts": null,
																			"completed_date": "13-10-2022 10:20:09",
																			"last_attempt_date": "13-10-2022 10:20:09"
																		}
																	]
																},
																{
																	"assessment_id": "183",
																	"assessment_name": "Krishna test12",
																	"assessment_type_name": "VIDEO",
																	"assessment_start_date": "10-08-2022 17:00:00",
																	"assessment_end_date": "10-10-2022 22:00:00",
																	"attempts_allowed": "100",
																	"total_question": 3,
																	"assessment_status": "Expired",
																	"prossess_data": []
																}
															],
															"success": true,
															"message": "Assessment data loaded successfully."
												}</pre>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
            
															
															<!-- end content -->	
														</div>
													</div>
												</div>
											</div>
											<!-- End Assessment Process Data -->
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
	<script src="<?php echo $asset_url; ?>assets/customjs/ai_dashboard.js" type="text/javascript"></script>
    <script>
        var json_participants = [];
        var select_assessments = '';
        var base_url = '<?= base_url(); ?>';

		var statistics_start_date = moment(Date()).subtract(1, 'months').format("YYYY-MM-DD");
        var statistics_end_date   = moment(Date()).format("YYYY-MM-DD");
		var options               = {};
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
			$('#dashboard-report-range').on('apply.daterangepicker', function (ev, picker) {
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
			$('.assessment_check').click(function () {
               if ($(this).is(':checked')) {
                   $("input[name='id[]']").prop('checked', true);                                                
               } else {
                   $("input[name='id[]']").prop('checked', false);
               }
			});
			$('#schedule_mail').click(function(){
				var select_assessments = $.map($(':checkbox[name=id\\[\\]]:checked'), function(n, i){
					  return n.value;
				}).join(',');
				if(!select_assessments.trim()){
					ShowAlret('Please select the assessment!', 'error');
				}else{
					// console.log(select_assessments);
					scheduleEmail($('#company_id').val(),select_assessments,1); //send to all candidates of the selected assessments
				}
			});
			//EMail Schedule Code End -----------------
        });
    </script>
</body>

</html>