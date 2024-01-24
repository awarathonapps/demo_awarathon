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
                                <span>Report API</span>
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
										Report API
										<div class="tools"> </div>  
									</div>
								</div>
								<div class="portlet-body">   
									<div class="tabbable-line tabbable-full-width">
										<ul class="nav nav-tabs" id="tabs">
											<li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
												<a href="#section-candidates" data-toggle="tab">Final Report</a>
											</li>
											<!-- <li <?php //echo ($step == 3 ? 'class="active"' : ''); ?>>
											<a href="#ideal-video" data-toggle="tab">Assessment Process Data</a>
											</li> -->
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
														    <!-- <h4>Final Report </h4> -->
															<!-- start content -->
															<div class="col-lg-9 col-md-9 col-sm-12 mid-space">
																<div class="right-content aw-dashboard">
																	<div class="row">
																		<div class="col-lg-9 col-md-9 col-sm-12">
																			<div class="api-details">
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'> Get list of Users Report</p>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Purpose:-</h4>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>To get list of Users and then report data of perticuler Asssessment available for your company domain</p><br>
																				
																				<h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Endpoints:-</h4>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>POST api/generate_report</p><br>
																				
																				<h4>Request URL:-</h4>
																				<p class="mb-25" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; font-weight: 500;'><a href="https://restapi.awarathon.com/api/generate_report" target="_blank">https://restapi.awarathon.com/api/generate_report</a></p><br>
																				
																				<h5 class="mb-15">Request JSON: </h5>								
																				<div class="req-url-bx"><pre style="white-space: pre-line;">{
																					"payload":"eyJ0eXAiOiJqd3QiLxxxxxxxxxxxxxxxxxxxxTEh24ARl_u_uLyQA6M",
																					"token_no":"92.bb98eca7xxxxxxxx",
																					"assessment_id":"***"
																					}</pre>
																				</div>
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>payload: String, Mandatory<br>
																					Specify the payload retrieve from Authentication API (NOTE: We can provide link for Authentication Page)
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>token_no: String, Mandatory<br>
																					Specify the token number retrieve from Authentication API (NOTE: We can provide link for Authentication Page)
																				</p>
																				
																				<p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>assessment_id: number format, Mandatory<br>
																					You can mention assessment_id on which data needs to be fetched
																				</p>
																				
																				<!-- <p>to_date: Date format, Not Mandatory<br>
																					You can mention date till which date assessment data needs to be fetched
																				</p> -->
																			</div>							
																		</div>
																		<div class="col-lg-12 col-md-12 col-sm-12">
																			<div class="api-details">
																				<h3>Response JSON:</h3>								
																				<div class="req-url-bx">
																					<pre style="white-space: pre-line;">{
																						"count": 3,
			"data": [
				{
					"user_id": "P00060981",
					"user_name": "Abdul kadir ",
					"joining_date": "03-11-2018",
					"division": "GG India - Wintura 1",
					"pc_hq": "Delhi",
					"zone": "North",
					"ec": "P00047699",
					"email": "abdulkadir1@drreddys.com",
					"assessment_name": "#PitchPerfect - Wintura 1 - MuOut Plus-Protectis - Apr 2022",
					"status": "Completed ",
					"xls_imported": "Ready",
					"ec_name": "Ashish Shankhdhar",
					"ec_of_l_2": "P00083975",
					"l_2_name": "Balwinder Singh",
					"ec_of_l_3": "P00006571",
					"l_3_name": "Sandeep Arora",
					"ai_overall_score": "69.06",
					"Voice Modulation": "40.00",
					"Pitch": "80.00",
					"Body Language": "63.66",
					"Pace Of Speech": "80.00",
					"Wintura 1 - Protectis Drops": "76.63",
					"Wintura 1 - MuOUT": "86.67",
					"manual_overall_score": "78.83",
					"aiandmanual": 73.9500000000000028421709430404007434844970703125,
					"differnce": -9.769999999999999573674358543939888477325439453125,
					"ai_rating": "60 to 74%",
					"manual_rating": "Above 75%",
					"join_range": "2 years to 5 years"
				},
				{
					"user_id": "P00070972",
					"user_name": "Aditya bajpai ",
					"joining_date": "03-11-2020",
					"division": "GG India - Wintura",
					"pc_hq": "Kanpur",
					"zone": "North",
					"ec": "P00033166",
					"email": "adityabajpai@drreddys.com",
					"assessment_name": "#PitchPerfect - Wintura 1 - MuOut Plus-Protectis - Apr 2022",
					"status": "Completed ",
					"xls_imported": "Ready",
					"ec_name": "Mahendra Singh",
					"ec_of_l_2": "P00013813",
					"l_2_name": "Manoj Singh",
					"ec_of_l_3": "P00006571",
					"l_3_name": "Sandeep Arora",
					"ai_overall_score": "50.87",
					"Voice Modulation": "20.00",
					"Pitch": "60.00",
					"Body Language": "32.67",
					"Pace Of Speech": "70.00",
					"Wintura 1 - Protectis Drops": "78.36",
					"Wintura 1 - MuOUT": "65.04",
					"manual_overall_score": "-",
					"aiandmanual": "50.87",
					"differnce": 50.86999999999999744204615126363933086395263671875,
					"ai_rating": "40 to 59%",
					"manual_rating": "-",
					"join_range": "2 years to 5 years"
				},
				{
					"user_id": "P00054745",
					"user_name": "Adla sai kumar ",
					"joining_date": "01-04-2017",
					"division": "GG India - Wintura",
					"pc_hq": "Karimnagar",
					"zone": "South",
					"ec": "P00012162",
					"email": "asaikumar@drreddys.com",
					"assessment_name": "#PitchPerfect - Wintura 1 - MuOut Plus-Protectis - Apr 2022",
					"status": "Completed ",
					"xls_imported": "Ready",
					"ec_name": "Shashi Bhushan Rao",
					"ec_of_l_2": "P00007513",
					"l_2_name": "S Althaf Hussain",
					"ec_of_l_3": "P00063008",
					"l_3_name": "Govindan V",
					"ai_overall_score": "68.64",
					"Voice Modulation": "40.00",
					"Pitch": "80.00",
					"Body Language": "45.33",
					"Pace Of Speech": "100.00",
					"Wintura 1 - Protectis Drops": "77.87",
					"manual_overall_score": "-",
					"aiandmanual": "68.64",
					"differnce": 68.6400000000000005684341886080801486968994140625,
					"ai_rating": "60 to 74%",
					"manual_rating": "-",
					"join_range": "5 years and above"
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
															<h3>Assessment Process Data</h3>
															<!-- start content -->
															<!-- <div class="col-lg-9 col-md-9 col-sm-12 mid-space">
																<div class="right-content aw-dashboard">
																	<div class="row">
																		<div class="col-lg-8 col-md-8 col-sm-12">
																			<div class="api-details">
																				<h3 class="mb-25">Assessment Process</h3>
																				<h4>Request URL:-</h4>
																				<p class="mb-25"><a href="https://mwweb.in/restapi/api/get_assesment_proccess_data" target="_blank">https://mwweb.in/restapi/api/get_assesment_proccess_data</a></p>
																				
																				<h5 class="mb-15">Post Data: </h5>
																				<p class="mb-10">-: Data must be in JSON format like below</p>
																				<div class="req-url-bx"><pre style="white-space: pre-line;">{
																					"payload":"eyJ0eXAiOiJqd3QiLxxxxxxxxxxxxxxxTEh24ARl_u_uLyQA6M",
																					"token_no":"92.bb98eca7xxxxxxxx",
																					"from_date":"dd-mm-yyyy",
																					"to_date":"dd-mm-yyyy"
																					}</pre></div>
																				<p>-: from date and to date are not mandatory.</p>
																			</div>							
																		</div>
																		<div class="col-lg-12 col-md-12 col-sm-12">
																			<div class="api-details">
																				<h3>Response:</h3>
																				<p>-Response format is json</p>
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
															</div> -->
            
															
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