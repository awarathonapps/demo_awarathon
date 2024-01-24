<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url =$this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />

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
                                    <span>Go Live</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li><span>Assessment</span></li>
                            </ul>
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
                                            Import AI Score
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <form id="FrmAIImport" name="FrmAIImport" method="POST"  enctype="multipart/form-data" > 
                                        <div class="portlet-body">                                                                                            
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">    

                                                    <?php
                                                    $errors = validation_errors();
                                                    if ($errors) {
                                                        ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <?php echo $errors; ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div id="errordiv" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <br><span id="errorlog"></span>
                                                    </div> 
                                                        <div class="row">    
                                                            <div class="col-md-4">       
                                                                <div class="form-group">
                                                                    <label class="">Assessment Name<span class="required"> * </span></label>
                                                                    <select id="assessment_id" name="assessment_id" class="form-control input-sm select2" 
																	placeholder="Please select" style="width:100%" onchange="get_assessor();">
                                                                        <option value="">Please Select</option>
                                                                        <?php 
                                                                        foreach ($Assessment_list as $list) { ?>
                                                                        <option value="<?= $list->id; ?>"><?php echo $list->assessment; ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
															<div class="col-md-4">       
                                                                <div class="form-group">
                                                                    <label class="">Assessor Name<span class="required"> * </span></label>
                                                                    <select id="assessor_id" name="assessor_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Choose File<span class="required"> * </span></label>
                                                                <div class="form-control fileinput fileinput-new" style="width: 100%;border: none;height:auto;" data-provides="fileinput">
                                                                    <div class="input-group input-large">
                                                                        <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                                            </span>
                                                                        </div>
                                                                        <span class="input-group-addon btn default btn-file">
                                                                            <span class="fileinput-new">
                                                                                Select file </span>
                                                                            <span class="fileinput-exists">
                                                                                Change </span>
                                                                            <input type="file" name="filename" id="filename" >
                                                                        </span>
                                                                        <a href="javascript:;" id="RemoveFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                                            Remove </a>
                                                                    </div>
                                                                </div><br/>
                                                                <span class="text-muted">(only .xlsx and .xls allowed)</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <a href="<?php echo base_url() . 'assessment_ai_score/samplexls_ai_score' ?>" class="form-control" style="    border: none;height:auto;" ><strong>Download Sample Xls File</strong></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel panel-success">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title">Notes</h3>
                                                        </div>
                                                        <div class="panel-body">
                                                            <ul>
                                                                <li>Upload AI Score Data through Xls file.</li>
                                                                <li>xls file format must be same as sample xls format.</li>
                                                                <li>Do not modify or delete the Columns of sample xls.</li>
                                                                <li>In sample xls file * is mandatory Fields.</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>                                                           
                                            </div>                                      
                                        </div>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="deviceusers-submit" name="deviceusers-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SubmitData();" >
                                                    <span class="ladda-label"><i class="fa fa-upload"></i> Confirm</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>                   
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');    ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');   ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script>
			var FrmAIImport = $('#FrmAIImport');
			var form_error = $('.alert-danger', FrmAIImport);
			var form_success = $('.alert-success', FrmAIImport);
			jQuery(document).ready(function () {
				$('.select2me').select2({
					allowClear: true,
					placeholder: 'Please Select'
				});
				FrmAIImport.validate({
					errorElement: 'span',
					errorClass: 'help-block help-block-error',
					focusInvalid: false,
					ignore: "",
					rules: {
						assessment_id: {
							required: true
						},
						assessor_id: {
							required: true
						},
						filename: {
							required: true
						}
					},
					invalidHandler: function (event, validator) {
						form_success.hide();
						form_error.show();
						App.scrollTo(form_error, -200);
					},
					highlight: function (element) {
						$(element).closest('.form-group').addClass('has-error');
					},
					unhighlight: function (element) {
						$(element).closest('.form-group').removeClass('has-error');
					},
					success: function (label) {
						label.closest('.form-group').removeClass('has-error');
					},
					submitHandler: function (form) {
						form_success.show();
						form_error.hide();
						Ladda.bind('button[id=deviceusers-submit]');
						form.submit();
					}
				});
				$(".select2, .select2-multiple", FrmAIImport).change(function () {
					FrmAIImport.validate().element($(this));
				});
			});
			function get_assessor(){
				$('#assessor_id').empty();
				if($('#assessment_id').val()==""){
					ShowAlret("Please select Assessment first.!!");
					return false;
				}
				$.ajax({
					type: "POST",
					url: '<?php echo site_url("assessment_ai_score/get_assessor_list"); ?>',
					data: {'assessment_id': $('#assessment_id').val()},
					success: function (lcoption) {
						$('#assessor_id').append(lcoption);
					}, error: function (XMLHttpRequest, textStatus, errorThrown) {
						ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
					}
				});
			}
			function SubmitData() {
				$('#errordiv').hide();
				if (!$('#FrmAIImport').valid()) {
					return false;
				}
				var file_data = $('#filename').prop('files')[0];
				var form_data = new FormData();
				form_data.append('filename', file_data);
				var other_data = $('#FrmAIImport').serializeArray();
				$.each(other_data, function (key, input) {
					form_data.append(input.name, input.value);
				});
				$.ajax({
					cache: false,
					contentType: false,
					processData: false,
					type: "POST",
					url: '<?php echo site_url("assessment_ai_score/uploadXls_ai_score"); ?>',
					data: form_data,
					success: function (Odata) {
						//alert(result);
						var Data = $.parseJSON(Odata);
						if (Data['success']) {
							ShowAlret(Data['Msg'], 'success');
							setTimeout(function () {// wait for 5 secs(2)
								location.reload(); // then reload the page.(3)
							}, 1000);
						} else {
							$('#errordiv').show();
							$('#errorlog').html(Data['Msg']);
							App.scrollTo(form_error, -200);
						}
					}, error: function (XMLHttpRequest, textStatus, errorThrown) {
						ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
					}
				});
			}
        </script>
    </body>
</html>