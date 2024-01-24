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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css">
    <!--datattable CSS  End-->
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
                                <i class="fa fa-circle"></i>
                                <span>Reports</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>Send Reports</span>
                            </li>
                        </ul>
                    </div>
                    <div class="row margin-top-10 ">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="col-md-12" style="padding:0px;">
                                    <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id;?>" />
                                    <select id="assessment_id" name="assessment_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                        <option value="">Please Select</option>
                                        <?php foreach ($assessment_result as $assres) { ?>
                                            <option value="<?php echo $assres->id; ?>"><?php echo $assres->assessment; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row margin-top-10">
                        <div class="col-md-12" id="participants_table">
                        </div>
                    </div>
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
    <script>
        var json_participants = [];
        var base_url = '<?= base_url(); ?>';
        jQuery(document).ready(function() {
            $('#assessment_id').select2({
                placeholder: "Please Select",
                width: '100%',
                allowClear: true
            });
            $("#assessment_id").change(function() { 
                fetch_participants();
            });
        });
    </script>
    <script src="<?php echo $asset_url; ?>assets/customjs/email_schedule.js" type="text/javascript"></script>
</body>

</html>