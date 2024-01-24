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
        .api-details {     
            padding: 11px;
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
                                <span>Authentication</span>
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
									     API Authentication
										<div class="tools"> </div>  
									</div>
								</div>
								<div class="portlet-body">   
									<div class="tabbable-line tabbable-full-width">
                                    <div class="row">
									<!-- Put here new code -->
                                    <div class="col-lg-9 col-md-9 col-sm-12 mid-space">
                                        <div class="right-content aw-dashboard">
                                            <div class="row" style="margin: 0px;">
                                                <div class="col-12">
                                                    <div class="api-details">
                                                        <h3 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 17px; color: #54595f;font-weight: 500;'>NOTE</h3>
                                                        <h5 class="mb-25" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>For use of any our API Need to call one mandatory API in which you have to post Your Company code, and you got Credential from this API, like payload and token.<br><br>
                                                        While you use any of rest API you have to use this payload and token for Authentication
                                                        </h5>
                                                    </div>	
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-sm-12">
                                                    <div class="api-details">																
                                                        <h3 class="mb-25" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 17px; color: #54595f;font-weight: 500;'>Credential Access API</h3>
                                                        <h4 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Request URL:-</h4>
                                                        <p class="mb-25"style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; font-weight: 500;'><a href="https://restapi.awarathon.com/api/get_credential" target="_blank">https://restapi.awarathon.com/api/get_credential</a></p>
                                                        
                                                        <h5 class="mb-15" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Post Data: </h5>
                                                        <p class="mb-10" style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>-: Data must be in JSON format like below</p>
                                                        <div class="req-url-bx"><pre style="white-space: pre-line;">{"company_code":"*****"}</pre></div>								
                                                    </div>							
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="api-details">
                                                        <h3 style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>Response:</h3>
                                                        <p style='font-family: "Proxima Nova","Open Sans",sans-serif;font-size: 15px; color: #54595f;font-weight: 500;'>-Response format is json</p>
                                                        <div class="req-url-bx">
                                                            <pre style="white-space: pre-wrap;">{
    "success": true,
    "message": "Credential load sussesfull.",
    "token": "92.8d870944f4541xxxxxxxxxxxxxxxxx",
    "payload": "eyJ0eXAiOiJqd3QiLCJhbGciOxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
                                                            </pre>
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