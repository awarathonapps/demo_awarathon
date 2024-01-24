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
                                <span>Integration</span>
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
									         Integration
										<div class="tools"> </div>  
									</div>
								</div>
								<div class="portlet-body">   
									<div class="tabbable-line tabbable-full-width">
										<ul class="nav nav-tabs" id="tabs">
											<li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
												<a href="#section-candidates" data-toggle="tab"> SSO Integration document</a>
											</li>
											<li <?php echo ($step == 3 ? 'class="active"' : ''); ?>>
											<a href="#ideal-video" data-toggle="tab"> FTP Integration document</a>
											</li>
											                        
										</ul>
										<div class="tab-content">
										<!-- Assessment Metadata -->
											<div class="tab-pane <?php echo ($step == 1 ? 'active' : 'mar'); ?>"  id="section-candidates">
												<div class="form-body">
													<div class="row ">
														<div class="col-md-12" id="assessment_panel_view">
														    <!-- <h3>Assessment Metadata	</h3> -->
															<!-- start content -->
															<!-- <h4>SSO Integration document</h4> -->
															<p style='font-family: "Proxima Nova","Open Sans",sans-serif;padding: 20px;font-size: 17px; color: #54595f;font-weight: 500;'>
															SSO Integration Requirements:<br>
● Dummy login/credentials for client’s LMS or the platform to be integrated<br>
● Platform should redirect users to the Awarathon platform with the Unique Employee Id
(Employee Code).<br>
Sample Format:<br>
https://<domain>.awarathon.com/app/index.html?id=<b>encrypted_emp_code</b><br>
● We are using secure SSO i.e. Employee ID needs to be encrypted<br>
Encryption method to be used: base64_encode()<br><br>

User Integration Requirements:<br>
Our system required basic information to run application are mentioned below:<br>
• Employee ID - It is a mandatory<br>
• First name It is a mandator<br>
• Last name It is a mandatory<br>
• Email address It is a mandatory<br>
• Mobile number It is a mandatory<br>
• Password - It depends upon client requirements. Our system can assign a fixed
password or unique password to all the users. It is a mandatory<br>
• Registration date - Optional information<br>
• Department - Optional information<br>
• Designation - Optional information<br>
• Headquarters - Optional information<br>
• Region - Optional information<br>
• Area - Optional information<br>
• Status - To store employee status Active or Inactive. It is a mandatory<br>

Our system can import above mentioned information using a REST API or using a excel file<br>

REST API - The system will use this approach when a client wants to update employee de
tails on regular basis.<br> On our server, we have integrated a script to upload information every

night at a particular time.<br>
To import data, we need information from a client like API link, function name, and
parameters required to call API. <br>e.g. token, payload, username, and password.<br>
Generally, we test client API using a Postman application once API runs successfully using
Postman then we will integrate it into our system.<br><br>

Excel - The system will use this approach when a client wants to update employee details
for a single-use or one-time.<br>
Excel should have the above-mentioned fields if any fields/columns are optional or
information is not available then keep empty.<br> but please specify all fields in columns.
	                                                        </p>
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
			                                                <!-- <h4 style="margin-left: 22px;"> FTP Integration document</h4><br> -->
															<p style='font-family: "Proxima Nova","Open Sans",sans-serif;padding: 20px;font-size: 17px; color: #54595f;font-weight: 500;'>
															
															Here is the brief of FTP:<br>

FTP:
FTP (File Transfer Protocol) is a network protocol for transmitting files. One can connect to the server through the FTP via code/tool to access files.<br>

In our case we are using FTP to provide a seamless process to our client to import user data into our system.<br>
- Awarathon will provide one server path and FTP credentials to the client.<br>
- Clients can use this folder to upload user data in excel/csv format (this file format and filename should be pre-decided between Awarathon and client), from this file user data will automatically sync in Awarathon system every midnight (once in a day).
															</p>
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
       
    </script>
</body>

</html>