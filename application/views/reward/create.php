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
                                    <span>Reward</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Create New Reward</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>reward" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Create Reward
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li class="active">
                                                    <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                </li>                                                
                                                <li>
                                                    <a href="#tab_reward_details" data-toggle="tab">Reward Details</a>
                                                </li>
                                                <li>
                                                    <a href="#tab_rules_regulation" data-toggle="tab">Rules & Regulations</a>
                                                </li>
                                                <li>
                                                    <a href="#tab_term_condition" data-toggle="tab">Terms & Conditions</a>
                                                </li>
                                                <li>
                                                    <a href="#tab_contact_details" data-toggle="tab">Contact Details</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" >Banners</a>
                                                </li>
                                            </ul>
                                             <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url; ?>reward/submit" enctype="multipart/form-data"> 
                                            
                                                  <?php
                                                $errors = validation_errors();
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
                                                            <div class="col-md-6">    
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
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Sponsor Name<span class="required"> * </span></label>
                                                                    <input type="text" name="sponsor_name" id="sponsor_name" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Reward Title<span class="required"> * </span></label>
                                                                    <input type="text" name="reward_title" id="reward_title" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Short Description</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="150" name="short_description" placeholder=""></textarea>
                                                                    <span class="text-muted">(Max 150 Characters)</span>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Start/End Date<span class="required"> * </span></label>
                                                                    <div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="dd/mm/yyyy">
                                                                        <input type="text" class="form-control input-sm" id="start_date" name="start_date">
                                                                        <span class="input-group-addon"> to </span>
                                                                        <input type="text" class="form-control input-sm" id="end_date" name="end_date">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2">    
                                                                    <div class="form-group">
                                                                        <label>Offer Code<span class="required"> * </span></label>
                                                                        <input type="text" name="offer_code" id="offer_code"  class="form-control input-sm" maxlength="10">                                                                           
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Voucher Quantity<span class="required"> * </span></label>
                                                                    <input type="text" name="qty" id="qty" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Redeem Point<span class="required"> * </span> </label>
                                                                    <input type="text" name="stride_limit" id="stride_limit" class="form-control input-sm">                                                                       
                                                                </div>
                                                            </div>                                                            
                                                        </div>                                                                                                              
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <input type="text" name="remarks" id="remarks" maxlength="255" class="form-control input-sm">                                                                    
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        <div class="row">
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
                                                <div class="tab-pane mar" id="tab_reward_details">
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="send_to_email_reward"> Send to email 
                                                                    <input id="send_to_email_reward" name="send_to_email_reward" type="checkbox" value="1" /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Reward Details</label>
                                                                <textarea cols="80" id="reward_details" name="reward_details" rows="10" class="form-control input-sm cke-editor">

                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="tab-pane mar" id="tab_rules_regulation">
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="send_to_email_rules"> Send to email 
                                                                    <input id="send_to_email_rules" name="send_to_email_rules" type="checkbox" value="1" /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Contest Rules & Regulations</label>
                                                                <textarea cols="80" id="rules_regulation" name="rules_regulation" rows="10" class="form-control input-sm cke-editor">

                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                    
                                                <div class="tab-pane mar" id="tab_term_condition">
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="send_to_email_term"> Send to email 
                                                                    <input id="send_to_email_term" name="send_to_email_term" type="checkbox" value="1" /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Term & Conditions </label>
                                                                <textarea cols="80" id="term_condition" name="term_condition" rows="10" class="form-control input-sm cke-editor">

                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                
                                                <div class="tab-pane mar" id="tab_contact_details">    
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="send_to_email_contact"> Send to email 
                                                                    <input id="send_to_email_contact" name="send_to_email_contact" type="checkbox" value="1" /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Contact Details</label>
                                                                <textarea cols="80" id="contact_detail" name="contact_detail" rows="10" class="form-control input-sm cke-editor">

                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                                                                          
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">  
                                                        <button type="submit" id="reward-submit" name="reward-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left">
                                                        <span class="ladda-label">Save & Next</span>
                                                        </button>
                                                        <a href="<?php echo site_url("reward"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
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
                CKEDITOR.replace('reward_details');
                CKEDITOR.replace('rules_regulation');
                CKEDITOR.replace('term_condition');
                CKEDITOR.replace('contact_detail'); 
                      
            jQuery.validator.addMethod("greaterThan", 
                function(value, element, params) {
                     if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) > new Date($(params).val());
                        }
                        return isNaN(value) && isNaN($(params).val()) 
                        || (Number(value) >= Number($(params).val())); 
                },'Must be greater than Start Date.');       
                
                var frmUsers = $('#frmUsers');
                var form_error = $('.alert-danger', frmUsers);
                var form_success = $('.alert-success', frmUsers);
                frmUsers.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {    
                        company_id: {
                            required: true
                        },
                        sponsor_name: {
                            required: true
                        },
                        reward_title: {
                            required: true
                        },
                        email: {
                            required: true,
                            email:true
                        },
                        start_date: {
                            required: true
                            //greaterThan: "#start_date"
                        },
                        end_date: {
                            required: true,
                            greaterThan: "#start_date"
                        },
                        offer_code: {
                            required: true,
                            offer_codeCheck:true                            
                        },
                        qty: {
                            required: true,
                            digits:true,
                            Quantity_Check:true
                        },
                        stride_limit: {
                            required: true,
                            digits:true
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
                        Ladda.bind('button[id=reward-submit]');                                
                        form.submit();
                    }
                });
            $('.select2,.select2-multiple').on('change', function() {
                    $(this).valid();
                });
            jQuery.validator.addMethod("offer_codeCheck", function(value, element){                  
                var isSuccess = false;   
                $.ajax({
                    type: "POST",
                    data: {offer_code:value,company_id:$('#company_id').val()},
                    url: "<?php echo base_url(); ?>reward/Check_offercode",
                    async: false,
                    success: function (msg) {
                         isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
                }             
            , "Code already exists!!!"); 
            
            jQuery.validator.addMethod("Quantity_Check", function(value, element){                     
                if(value>0){
                    return true;
                }else{
                    return false;
                }
            }
            , "Value must be greater than zero  !!!");                
            });
        </script>
    </body>
</html>