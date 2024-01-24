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
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
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
                                <span>Api Logs</span>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <span>View</span>
                                <i class="fa fa-circle"></i>
                            </li>
                        </ul>
                        <div class="page-toolbar">
                            <a href="<?php echo $base_url ?>api_logs" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption caption-font-24">
                                        View Api Logs
                                        <div class="tools"> </div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="tabbable-line tabbable-full-width">

                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">
                                                <form id="api_logs" name="api_logs" method="POST" action="#">

                                                    <div class="row">

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Api Name</label>
                                                                <input type="text" name="api_name" id="api_name" maxlength="50" value="<?php echo $company_data->api_name; ?>" class="form-control input-sm" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Comapny Id</label>
                                                                <input type="text" name="comapny_id" id="comapny_id" maxlength="50" value="<?php echo $company_data->company_id; ?>" class="form-control input-sm" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Company Name</label>
                                                                <input type="text" name="portal_name" id="portal_name" maxlength="50" value="<?php echo $company_data->portal_name; ?>" class="form-control input-sm" disabled="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="my-line"></div>

                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Api Parameter</label>
                                                                <textarea style="font-size:15px;width:500px;height:94px;" disabled=""><?php echo isset($company_data->api_parameter) ? trim($company_data->api_parameter) : ''; ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Date Time<span class="required"> * </span></label>
                                                                <input type="text" name="date_time" id="date_time" maxlength="50" class="form-control input-sm" value="<?php echo date('d-m-Y',  strtotime($company_data->date_time)); ?>" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Ip Address<span class="required"> * </span></label>
                                                                <input type="text" name="ip_address" id="ip_address" maxlength="50" class="form-control input-sm" value="<?php echo $company_data->ip_address; ?>" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">Status Msg<span class="required"> * </span></label>
                                                                <input type="text" name="status_msg" id="status_msg" maxlength="250" class="form-control input-sm" value="<?php echo $company_data->status_msg; ?>" disabled="">
                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class="row">
                                                        <div class="col-md-12 text-right">
                                                            <a href="<?php echo $base_url ?>api_logs" class="btn btn-default btn-cons">Cancel</a>
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
            <?php //$this->load->view('inc/inc_quick_sidebar'); 
            ?>
        </div>
        <?php //$this->load->view('inc/inc_footer'); 
        ?>
    </div>
    <?php //$this->load->view('inc/inc_quick_nav'); 
    ?>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>

</body>

</html>