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
	<!-- <link href="<?php //echo $asset_url; ?>assets/layouts/auth/css/slick.css" rel="stylesheet" type="text/css" /> -->
	<!-- <link href="<?php //echo $asset_url; ?>assets/layouts/auth/css/header.css" rel="stylesheet" type="text/css" /> -->
	<!-- <link href="<?php //echo $asset_url; ?>assets/layouts/auth/css/media.css" rel="stylesheet" type="text/css" /> -->
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
		.api-details h5 {
    color: #54595f;
    font-weight: 500;
}
.api-details h3 {
    color: #004369;
    margin-bottom: 15px;
    font-weight: 600;
}
body, h1, h2, h3, h4, h5, h6 {
    font-family: "Proxima Nova","Open Sans",sans-serif;
}
		.card {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.25rem;
}
.card-dashboard .card-content h3 {
    color: #db1f48;
    font-weight: 700;
}
.card-dashboard .card-content {
    padding-left: 100px;
}
.api-box a:hover .card, .api-box:hover .card {
    border: 2px solid #db1f48;
    transition: all 0.3s;
}

h3 {
    font-size: 25px;
    line-height: 27px;
	margin: 0;
    padding: 0;
}
.align-items-center {
    -webkit-box-align: center!important;
    -ms-flex-align: center!important;
    align-items: center!important;
}
.api-details {
    margin-bottom: 45px;
    margin-left: 22px;
   
	font-family: "Proxima Nova","Open Sans",sans-serif;
}

.api-details h5 {
    color: #54595f;
    font-weight: 500;
	font-size: 17px;
}
.aw-dashboard .card {
    padding: 25px 25px 25px 25px;
    border-radius:20px!important;
    border: 2px solid #f6f6f6;
    min-height: 111px;
    max-height: 111px;
    background-color: #fdfdfd;
    margin-bottom: 20px;
    transition: all 0.3s;
}
.card-dashboard .iconbx {
    width: 100%;
    max-width: 65px;
    height: 57px;
    line-height: 57px;
	margin: 0px 0px -42px 0px;
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
                                <span>Api Documantation</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>Introducton</span>
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
						
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption caption-font-24">
										API Introduction
										<div class="tools"> </div>  
									</div>
								</div>
								<div class="portlet-body">   
									<div class="tabbable-line tabbable-full-width">
										<!-- Put here new code -->
										<div class="row">
										<div class="col-lg-9 col-md-9 col-sm-12 mid-space">
											<div class="right-content aw-dashboard">
												<div class="row mb-25">
													<!-- <div class="col-lg-6 col-md-6 col-sm-12 api-box">	
														<a href="assessment-metadata.html">
														<div class="card">								
															<div class="card-dashboard d-flex align-items-center">
																<div class="iconbx"><img src="<?php //echo $asset_url; ?>assets/images/icon-01.png" alt="Integration API" class="img-fluid"></div>
																<div class="card-content">
																	<h3>Assessment API</h3>
																	
																</div>
															</div>
														</div>	
														</a>	
													</div>
													<div class="col-lg-6 col-md-6 col-sm-12 api-box">							
														<div class="card">								
															<div class="card-dashboard d-flex align-items-center">
																<div class="iconbx"><img src="<?php //echo $asset_url; ?>assets/images/icon-4.png" alt="Integration API" class="img-fluid"></div>
																<div class="card-content">
																	<h3>Integration API</h3>
																	
																</div>
															</div>
														</div>							
													</div>
													<div class="col-lg-6 col-md-6 col-sm-12 api-box">							
														<div class="card">								
															<div class="card-dashboard d-flex align-items-center">
																<div class="iconbx"><img src="<?php //echo $asset_url; ?>assets/images/icon-4.png" alt="Reports API" class="img-fluid"></div>
																<div class="card-content">
																	<h3>Reports API</h3>
																	
																</div>
															</div>
														</div>							
													</div>
													<div class="col-lg-6 col-md-6 col-sm-12 api-box">							
														<div class="card">								
															<div class="card-dashboard d-flex align-items-center">
																<div class="iconbx"><img src="<?php //echo $asset_url; ?>assets/images/icon-02.png" alt="Video Process" class="img-fluid"></div>
																<div class="card-content">
																	<h3>Video Process</h3>
																	
																</div>
															</div>
														</div>							
													</div> -->
													
												</div>
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12">
														<div class="api-details">
															<h3>Introducton</h3>   
															<h5 class="mb-25">Awarathon has standard modules such as Assessments, Users, Reports and so on. Using AWARATHON REST API, you can retrieve the list of available module details.<br><br>
														REST APIs are mainly developed to fetch Assessment level data
														<br>
														<br>
														<br>
														<b>Purpose -:</b><br><br>
														Provide module with full details.<br><br><br>
														<b>NOTE -:</b><br><br>
														For use of any our API Need to call one mandatory API in which you have to post Your Company code, and you got Credential from this API, like payload and token.
														While you use any of rest API you have to use this payload and token for Authentication

														</div>							
													</div>                        
												</div>
											</div>
										</div>
	                                    </div>
										<!-- End here new code -->	
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
       
    </script>
</body>

</html>