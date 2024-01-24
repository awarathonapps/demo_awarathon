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
        <link href="<?php echo $asset_url; ?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
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
                                    <span>Advertisement</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Create New Advertisement</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>advertisement" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
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
                                            Create Advertisement
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">                                           
                                             <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url; ?>advertisement/submit" enctype="multipart/form-data"> 
                                            
                                                  <?php
                                                  if($errors==""){
                                                $errors = validation_errors();
                                                //echo $errors;
                                                  }
                                                if ($errors) {?>
                                                    <div style="display: block;" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <?php echo $errors;?>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                    </div>
                                                 
                                                 <div class="tab-content">
                                            
                                                <div class="tab-pane active" id="tab_overview">  
                                                    <?php if ($Company_id == "") { ?>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                        <option value="">Please Select </option>
                                                                        <?php if(count($CompnayResultSet)>0){
                                                                        foreach ($CompnayResultSet as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>"><?php echo $value->company_name ?> </option>                                                                       
                                                                        <?php }  } ?>
                                                                </select>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Title<span class="required"> * </span></label>
                                                                    <input type="text" name="advt_name" id="advt_name" value="<?php echo set_value('advt_name'); ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Start/End Date</label>
                                                                    <div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                                                        <input type="text" class="form-control input-sm" value="<?php echo set_value('start_date'); ?>"  id="start_date" name="start_date">
                                                                        <span class="input-group-addon"> to </span>
                                                                        <input type="text" class="form-control input-sm" value="<?php echo set_value('end_date'); ?>" id="end_date" name="end_date">
                                                                    </div>
                                                                </div>
                                                            </div>   
                                                        </div> 
                                                                                                                     
<!--                                                    <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <input type="text" name="remarks" id="remarks" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        </div>                                                        -->
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>URL</label>
                                                                    <input type="text" name="url" id="url" maxlength="255" value="<?php echo set_value('url'); ?>" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Banner image</label>
                                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                                    <div class="input-group input-large">
                                                                                            <div class="form-control uneditable-input" data-trigger="fileinput">
                                                                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                                                                    </span>
                                                                                            </div>
                                                                                            <span class="input-group-addon btn default btn-file">
                                                                                            <span class="fileinput-new">
                                                                                            Select file </span>
                                                                                            <span class="fileinput-exists">
                                                                                            Change </span>
                                                                                            <input type="file" name="thumbnail_image" id="thumbnail_image" accept="image/png,image/gif,image/jpeg">
                                                                                            </span>
                                                                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                                                            Remove </a>
                                                                                    </div>
                                                                            </div>
                                                                    <span class="text-muted">((Extensions allowed: .png , .gif, .jpg, .jpeg)  width:750px, height:400px)</span>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                        
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Remarks</label>
                                                                <textarea rows="4" class="form-control input-sm" id="remarks" maxlength="150" name="remarks" placeholder=""><?php echo set_value('remarks'); ?></textarea>
                                                                <span class="text-muted">(Max 150 Characters)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>By Sorting</label>
                                                                    <input type="text" name="sorting" value="<?php echo set_value('sorting'); ?>" id="sorting" maxlength="5" class="form-control input-sm">                                                                    
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="1" selected>Active</option>
                                                                        <option value="0">In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                                                                                                                                                                                                                                             
                                                </div>                                                                                                                                                       
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">  
                                                        <button type="submit" id="reward-submit" name="advertisement-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right">
                                                        <span class="ladda-label">Submit</span>
                                                        </button>
                                                        <a href="<?php echo site_url("advertisement"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                    </div>
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
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <!-- <script src="<?php //echo $base_url;  ?>assets/global/scripts/jquery-1.12.4.min.js" type="text/javascript"></script> -->
        <!-- <script src="<?php //echo $asset_url;  ?>assets/global/scripts/bootstrap.min.js" type="text/javascript"></script> -->
        
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/avatar_main.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        
        <script type="text/javascript">
            
            jQuery(document).ready(function () {
                if (jQuery().datepicker) {
                    $('.date-picker').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy',
                        startDate:'+0d'
                    });
                }                 
                      
                jQuery.validator.addMethod("greaterThan", 
                function(value, element, params) {
                     if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) > new Date($(params).val());
                        }
                        return isNaN(value) && isNaN($(params).val()) 
                        || (Number(value) >= Number($(params).val())); 
                },'Must be greater than Start Date.');
                jQuery.validator.addMethod("validateUrl", function(val, element) {
                    //var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
                    if (val.length == 0) {
                            return true;
                        }
                    if (!/^(https?|ftp):\/\//i.test(val)) {
                            val = 'http://' + val; // set both the value
                            $(element).val(val); // also update the form element
                        }
                        return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(val);
                    }, "Please enter valid url");
                var frmUsers = $('#frmUsers');
                var form_error = $('.alert-danger', frmUsers);
                var form_success = $('.alert-success', frmUsers);
                frmUsers.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {                        
                        advt_name: {
                            required: true,
                            advt_nameCheck:true
                        }, 
                        company_id: {
                            required: true                            
                        },
                        start_date: {
                            //required: true                            
                        },
                        sorting:{
                            digits:true
                        },
                        end_date: {
                           // required: true,
                            greaterThan: "#start_date"
                        },
                        url: {
                            validateUrl: true
                        },                        
                        status: {
                            required: true,                            
                        }
                    },
                    invalidHandler: function (event, validator) {
                        form_success.hide();
                        form_error.show();
                        if (validator.errorList.length) {
                            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
                        }

                        App.scrollTo(form_error, -200);
                    },
                    errorPlacement: function(error, element) {
                        if(element.hasClass('form-group')) {
                            error.appendTo(element.parent().find('.has-error'));
                        }
                        else if(element.parent('.form-group').length) {
                            error.appendTo(element.parent());
                        }
                        else {
                            error.appendTo(element);
                        }
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
                        Ladda.bind('button[id=advertisement-submit]');                                                                    
                        form.submit();
                    }
                });
                $('.select2,.select2-multiple').on('change', function() {
                    $(this).valid();
                });
                
                jQuery.validator.addMethod("advt_nameCheck", function(value, element){                  
                var isSuccess = false;   
                $.ajax({
                    type: "POST",
                    data: {advt_name:value,company_id:$('#company_id').val()},
                    url: "<?php echo base_url(); ?>Advertisement/Check_advertisement",
                    async: false,
                    success: function (msg) {
                         isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
                }             
            , "Advertisement already exists!!!");
                
            });
        </script>
    </body>
</html>