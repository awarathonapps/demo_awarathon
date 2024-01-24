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
    <!-- <link href="<?php //echo $asset_url; 
                        ?>assets/layouts/auth/css/slick.css" rel="stylesheet" type="text/css" /> -->
    <!-- <link href="<?php //echo $asset_url; 
                        ?>assets/layouts/auth/css/header.css" rel="stylesheet" type="text/css" /> -->
    <!-- <link href="<?php //echo $asset_url; 
                        ?>assets/layouts/auth/css/media.css" rel="stylesheet" type="text/css" /> -->
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

        .api-details h5 {
            color: #54595f;
            font-weight: 500;
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Proxima Nova", "Open Sans", sans-serif;
        }
        textarea {
        width: 100%;
        height: 116px;
        padding: 8px 12px;
        box-sizing: border-box;
        border: 2px solid #ccc;
        border-radius: 4px;
        background-color: #f8f8f8;
        font-size: 16px;
        resize: none;
        color: grey;
        font-size: 15px;
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
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: 0.25rem;
        }

        .card-dashboard .card-content h3 {
            color: #db1f48;
            font-weight: 700;
        }

        .card-dashboard .card-content {
            padding-left: 100px;
        }

        .api-box a:hover .card,
        .api-box:hover .card {
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
            -webkit-box-align: center !important;
            -ms-flex-align: center !important;
            align-items: center !important;
        }

        .api-details {
            margin-bottom: 45px;
            margin-left: 22px;

            font-family: "Proxima Nova", "Open Sans", sans-serif;
        }

        .api-details h5 {
            color: #54595f;
            font-weight: 500;
            font-size: 17px;
        }

        .aw-dashboard .card {
            padding: 25px 25px 25px 25px;
            border-radius: 20px !important;
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
                                <span>API Documentation</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>Home</span>
                            </li>
                        </ul>
                        <div class="col-md-1 page-breadcrumb"></div>
                        <div class="page-toolbar">
                            <!-- <div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
								<i class="icon-calendar"></i>&nbsp;
								<span class="thin uppercase hidden-xs"></span>&nbsp;
								<i class="fa fa-angle-down"></i>
							</div> -->
                        </div>
                    </div>

                    <div class="row margin-top-10 ">
                        <div class="col-md-12">

                            <div class="portlet light bordered">
                                <div class="portlet-body">
                                    <div class="tabbable-line tabbable-full-width">
                                        <!-- Put here new code -->
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-12 mid-space">
                                                <div class="right-content aw-dashboard">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <h4 style="padding-left: 25px;font-weight:bold;color:grey;font-size:18px">API Authentication </h4>
                                                            <div class="api-details">
                                                                <h5 class="mb-25" style="font-size:15px">Learn how to generate the authentication token from our API documentation below:</h5>
                                                                <div class="req-url-bx">
                                                                    <pre style="white-space: pre-line;">
                                                                   <a href="https://restapi.awarathon.com/api_document/api_documantation" target="_blank">https://restapi.awarathon.com/api_document/api_documantation</a></pre>
                                                                </div>
                                                                <h5 class="mb-25" style="font-size:15px">To manage all the apps authenticated using JWT Token and Payload , <a href="https://restapi.awarathon.com/api_document/api_documantation/authentication" target="_blank">click here</a></h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px!important;">
                                                            <h4 style="font-weight: bold;color: grey; font-size:18px">API Usage Limit </h4>
                                                            
                                                            <table class="table table-sm table-striped" style='border:1px;'>
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Edition</th>
                                                                        <th scope="col">Maximum API Calls Per Day</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td scope="row">Pro Edition</td>
                                                                        <td>15,000</td>
                                                                    </tr>
                                                                    <!-- <tr>
                                                                        <td scope="row">Express Edition</td>
                                                                        <td>25,000</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td scope="row">Standard Edition</td>
                                                                        <td>200,000</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td scope="row">Professional Edition</td>
                                                                        <td>500,000</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td scope="row">Enterprise Edition</td>
                                                                        <td>1,000,000</td>
                                                                    </tr> -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px!important;">
                                                            <h4 style="font-weight: bold;color: grey; font-size:18px">Concurrent API Usage Limit </h4>
                                                            
                                                            <table class="table table-sm table-striped" style='border:1px;'>
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Edition</th>
                                                                        <th scope="col">Concurrency Limits per API Client/Org</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td scope="row">Pro Edition</td>
                                                                        <td>5</td>
                                                                    </tr>
                                                                    <!-- <tr>
                                                                        <td scope="row">Express Edition</td>
                                                                        <td>10</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td scope="row">Standard Edition</td>
                                                                        <td>10</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td scope="row">Professional Edition</td>
                                                                        <td>15</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td scope="row">Enterprise Edition</td>
                                                                        <td>25</td>
                                                                    </tr> -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px!important;">
                                                            <h4 style="font-weight: bold;color: grey; font-size:18px">Awarathon Service Communication (ASC) Key </h4>
                                                            <h5 style="color: grey;font-size:15px">This key is used to access Awarathon Desk modules from other Awarathon services. The generated key and email address mentioned below should be provided to other Awarathon services.</h5>
                                                            <!-- <br> -->
                                                            <h5><span style="font-size:14px">Email</span><span style="padding-left:30px;color: grey;font-size:15px">: info@awarathon.com</span></h5>
                                                            <h5><span style="font-size:14px">Token Key</span><span style="color: green;padding-left:12px;font-size:15px">: <?php echo isset($token->token)?$token->token:''; ?></span></h5>
                                                            <div style="display: flex;"><span style="font-size:14px;width:11%;">Payload key :</span><div style="font-size:15px;width:90%;"><textarea disabled><?php echo isset($token->payload)?trim($token->payload):''; ?></textarea></div></div>


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