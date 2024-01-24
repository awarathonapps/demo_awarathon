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
	<style type="text/css">
		#participants_datatable_filter, #participants_datatable_paginate{
			float: right;
		}
		.select2-results__option--highlighted[aria-selected] {
			background-color: #d9d9d9 !important;
			color: #fff !important;
					 
		}
		.opt-green{
			color: #004369;
		}
		.opt-green:hover {
		  background-color: #d9d9d9;
		  color: #004369;
		}
		.opt-red{
			color: #db1f48;
		}
		.opt-red:hover {
			background-color: #d9d9d9;
			color: #db1f48;
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
                                <span>Reports</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>AI Reports</span>
                            </li>
                        </ul>
                    </div>
                    <div class="row margin-top-10 ">
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="col-md-12" style="padding:0px;">
                                    <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id;?>" />
									<label class="control-label">Assessment<span class="required" aria-required="true"> * </span></label>
                                    <select id="assessment_id" name="assessment_id" class="form-control input-sm select2me" placeholder="Select" style="width: 100%">
                                        <option value="">Please Select</option>
                                        <?php foreach ($assessment_result as $assres) { ?>
											<option value="<?= $assres->assessment_id;?>"><?php echo $assres->assessment.' - ['.$assres->status.']'; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row margin-top-15">

                    </div>

                    <div class="row margin-top-10">
                        <div class="col-md-12" id="participants_table">
                        </div>
                    </div>
                    <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true"  data-width="760">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Questions List</h4>
                                </div>
                                <div class="modal-body" id="mdl_questions">

                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right ">  
                                        <button type="button" data-dismiss="modal" class="btn btn-default btn-cons">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="responsive-video-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true"  data-width="760">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Assessment Video</h4>
                                </div>
                                <div class="modal-body" id="mdl_video">
                                <iframe id='dp-video' src='' frameborder='0' allow='autoplay; fullscreen; picture-in-picture;' allowFullScreen style='top: 0;left: 0;width: 100%;box-sizing: border-box;height: 500px;border-top-width: 0px;border-right-width: 0px;border-bottom-width: 0px;border-left-width: 0px;'></iframe>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right ">  
                                        <button type="button" data-dismiss="modal" class="btn btn-default btn-cons" onclick="stop_video()">Close</button>
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
    <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script>
        var json_participants = [];
        var base_url = '<?php echo base_url(); ?>';
        var frmModalForm = $('#frmModalForm');
		function format_assessment_data(data)
        {
            if (data.text.search("Live")>0){
                var $opt_data = $('<option class="opt-green">' + data.text + '</option>');
                return $opt_data;
            }else{
                var $opt_data = $('<option class="opt-red">' + data.text + '</option>');
                return $opt_data;
            }
        }	   
        jQuery(document).ready(function() {
            $('#assessment_id').select2({
                placeholder: "Select",
                width: '100%',
                allowClear: true,
				templateResult: format_assessment_data									  
            });
            $("#assessment_id").change(function() { 
               fetch_participants();
            });
        });
    </script>
    <script src="<?php echo $asset_url; ?>assets/customjs/ai_reports.js" type="text/javascript"></script>
</body>

</html>