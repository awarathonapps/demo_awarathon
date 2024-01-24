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
        <style>
            .tr-background{
                background: #ffffff!important;
            }
            .wksh-td{
                color: #000000 !important;
                vertical-align: top !important;
            }
            .potrait-title-mar{
                margin-left: -9px;
                margin-right: -9px;
            }
            .page-content-white .page-title {
                margin: 20px 0;
                font-size: 22px;
                font-weight: 300!important;
            }
        #users_table {
            display: block;
            max-height: 350px;
            overflow-y: auto;
            table-layout:fixed;
        }
        .cust_container {
            overflow: hidden;
                width: 100%;
          }
          .left-col {
            padding-bottom: 500em;
            margin-bottom: -500em;
          }
          .right-col {
            margin-right: -1px; /* Thank you IE */
            padding-bottom: 500em;
            margin-bottom: -500em;
          }  
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
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
                                    <span>Dashboard</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li><span>Salesforce Dashboard</span></li>
                            </ul>
                        </div>
                        <h1 class="page-title">
                             Salesforce Optimisation Dashboard <small>Import Sales &amp; Optimisation</small>
                        </h1>
                        <div class="clearfix margin-top-10"></div>

                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
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
                                
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                   Filter Data </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse ">
                                            <form id="FrmAIImport" name="FrmAIImport" method="POST"  enctype="multipart/form-data" > 
                                                <div class="panel-body" >
                                                        <div class="row">    
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Assessment Name<span class="required"> * </span></label>
                                                                    <select id="assessment_id" name="assessment_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" onchange="dashboard_refresh()">
                                                                        <option value="">Please Select</option>
                                                                        <?php 
                                                                        foreach ($Assessment_list as $list) { ?>
                                                                        <option value="<?= $list->id; ?>"><?php echo $list->assessment; ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Choose File</label>
                                                                    <div class="form-control fileinput fileinput-new" style="width: 100%;border: none;height:auto; padding:0px;" data-provides="fileinput">
                                                                        <div class="input-group input-large" style="width: 330px!important;">
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
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <a href="<?php echo base_url() . 'salesforce_dashboard/samplexls_sales_input' ?>" class="form-control" style=" border: none;height:auto;" ><strong>Download Sample Xls File</strong></a>
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        <div class="row"> 
                                                            <div class="col-md-6"> 
                                                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                                                    <div class="portlet-title potrait-title-mar">
                                                                        <div class="caption ">
                                                                            Threshold ( in % )
                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body" style="padding: 0px !important"> 
                                                                        <div class="table-scrollable table-scrollable-borderless">
                                                                            <table class="table table-hover table-light" id="threshold_tb">
                                                                                <?php if(count($threshold_list) > 0) { 
                                                                                   foreach($threshold_list as $thr){ ?>
                                                                                    <tr class="tr-background">
                                                                                        <td class="wksh-td" width="65%"><?php echo $thr->category ?></td>
                                                                                        <input type="hidden"  name="category_id[]" value="<?php echo $thr->category_id ?>">
                                                                                        <input type="hidden"  name="category[]" value="<?php echo $thr->category ?>">
                                                                                        <td class="wksh-td form-group" width="35%"><input type="number" class=" form-control input-sm bold theme-font" id="threshold<?php echo $thr->category_id ?>" name="threshold[]" value="" min="0" max="100"></td>
                                                                                        <input type="hidden"  name="threshold_id[]" value="">
                                                                                    </tr>
                                                                                <?php }}else{

                                                                                } ?>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">      
                                                            <div class="col-md-8 col-md-offset-4 text-right">  
                                                                <button type="button" id="deviceusers-submit" name="deviceusers-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SubmitData();" >
                                                                    <span class="ladda-label"> Submit</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                           
                            </div>
                        </div>
                        <div class="row salestable" style="display :none">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered">
<!--                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption caption-font-24">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Salesforce Optimisation</span>
                                        </div>
                                    </div>-->
                                    <div class="portlet-body">                                                                                            
                                        <div class="tab-content">
                                               <div class="row" id="sales_table"></div>
                                        </div>                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                   
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');    ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');   ?>
        </div>
        <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
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
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script>
			var FrmAIImport = $('#FrmAIImport');
			var form_error = $('.alert-danger', FrmAIImport);
			var form_success = $('.alert-success', FrmAIImport);
                        var base_url = "<?php echo $base_url; ?>";
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
						'threshold[]': {
                                                        required: true,
							min : 0,
                                                        max : 100
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
					url: '<?php echo site_url("salesforce_dashboard/uploadXls_salses_input"); ?>',
					data: form_data,
					success: function (Odata) {
						//alert(result);
						var Data = $.parseJSON(Odata);
						if (Data['success']) {
							$('#RemoveFile').click();
							ShowAlret(Data['Msg'], 'success');
//							setTimeout(function () {// wait for 5 secs(2)
//								location.reload(); // then reload the page.(3)
//							}, 1000);
                                                        dashboard_refresh();
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
                        function dashboard_refresh() {
                            if ($('#assessment_id').val() == "") {
                                ShowAlret("Please select Assessment.!!", 'error');
                                return false;
                            }
                            $.ajax({
                                type: "POST",
                                data: {assessment_id: $('#assessment_id').val()},
                                //async: false,
                                url: base_url + "salesforce_dashboard/getdashboardData",
                                beforeSend: function () {
                                    customBlockUI();
                                },
                                success: function (data) {
                                    if (data != '') {
                                        var json = jQuery.parseJSON(data);
                                        $('#threshold_tb').html(json['tbhtml']);
                                        $('#sales_table').html(json['salestable']);
                                        if(json['sales_cnt'] > 0){
                                            $('.salestable').show();
                                        }else{
                                            $('.salestable').hide();
                                        }
                                        customunBlockUI();
                                    }
                                }
                            });
                        } 
                        function set_userid(user_id,question_id) {
                         $('.bg-remove').removeClass("active");
                         $('#user'+user_id).addClass("active");
                         $('#userid').val(user_id);
                         get_uservideo(question_id);
                        } 
                        function get_uservideo(question_id) {
                            if ($('#assessment_id').val() == "") {
                                ShowAlret("Please select Assessment.!!", 'error');
                                return false;
                            }
                            $.ajax({
                                type: "POST",
                                data: {assessment_id: $('#assessment_id').val(),user_id:$('#userid').val(),question_id : question_id},
                                //async: false,
                                url: base_url + "salesforce_dashboard/getvideoData",
                                beforeSend: function () {
                                    customBlockUI();
                                },
                                success: function (vdhtml) {
                                    if (vdhtml != '') {
                                        $('#video_details').html(vdhtml);
                                        $('.bg-question').removeClass("active");
                                        $('#qut'+question_id).addClass("active");
                                        customunBlockUI();
                                    }
                                }
                            });
                        } 
        </script>
    </body>
</html>