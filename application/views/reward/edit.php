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
                                    <span>Edit Reward</span>
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
                                            Edit Reward
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                 <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url;?>reward/update/<?php echo base64_encode($result->id);?>" enctype="multipart/form-data">     
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li <?php echo ($step==1 ? 'class="active"':''); ?>>
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
                                                <li <?php echo ($step==2 ? 'class="active"':''); ?>>
                                                    <a href="#tab_avatar" data-toggle="tab">Banners</a>
                                                </li>
                                            </ul>
                                            
                                            
                                                  <?php
                                                $errors = validation_errors();
                                                //echo $errors;

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
                                            
                                                <div class="tab-pane <?php echo ($step==1 ? 'active"':''); ?>" id="tab_overview">                                                        
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                    <option value="">Please Select </option>
                                                                   <?php if(count($CompnayResultSet)>0){
                                                                            foreach ($CompnayResultSet as $key => $value) { ?>
                                                                    <option value="<?php echo $value->id ?>" <?php echo($value->id==$result->company_id ? 'selected' : '')?>><?php echo $value->company_name ?> </option>
                                                                       
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
                                                                    <input type="text" name="sponsor_name" id="sponsor_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->sponsor_name; ?>">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Reward Title<span class="required"> * </span></label>
                                                                    <input type="text" name="reward_title" id="reward_title" maxlength="255" class="form-control input-sm" value="<?php echo $result->reward_name; ?>">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Short Description</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="150" name="short_description" placeholder="" value=""><?php echo $result->short_description; ?></textarea>
                                                                    <span class="text-muted">(Max 150 Characters)</span>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="100" class="form-control input-sm" value="<?php echo $result->email; ?>">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Start/End Date<span class="required"> * </span></label>
                                                                    <div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="dd/mm/yyyy">
                                                                        <input type="text" class="form-control input-sm" id="start_date" name="start_date" value="<?php echo $result->start_date; ?>" >
                                                                        <span class="input-group-addon"> to </span>
                                                                        <input type="text" class="form-control input-sm" id="end_date" name="end_date" value="<?php echo $result->end_date; ?>" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2">    
                                                                    <div class="form-group">
                                                                        <label>Offer Code<span class="required"> * </span></label>
                                                                        <input type="text" name="offer_code" id="offer_code" maxlength="10" class="form-control input-sm" value="<?php echo $result->offer_code; ?>" >                                                                           
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Voucher Quantity<span class="required"> * </span></label>
                                                                    <input type="text" name="qty" id="qty" class="form-control input-sm" value="<?php echo $result->quantity; ?>">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Redeem Point<span class="required"> * </span></label>
                                                                    <input type="text" name="stride_limit" id="stride_limit" class="form-control input-sm" value="<?php echo $result->stride_limit; ?>" >                                                                       
                                                                </div>
                                                            </div>                                                            
                                                        </div>                                                                                                              
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <input type="text" name="remarks" id="remarks" maxlength="255" class="form-control input-sm" value="<?php echo $result->remarks; ?>">                                                                    
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                        <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        
                                                        <input type="hidden" class="avatar-path" name="avatar_path" id="avatar_path" value="">                                                                                                     
                                                </div>                                                    
                                                <div class="tab-pane mar" id="tab_reward_details">
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="send_to_email_reward"> Send to email 
                                                                    <input id="send_to_email_reward" name="send_to_email_reward" type="checkbox" value="1" class="checkable"<?php echo $result->send_reward_details?'checked':'';?>/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Reward Details</label>
                                                                <textarea cols="80" id="reward_details" name="reward_details" rows="10" class="form-control input-sm cke-editor" value="">
                                                                   <?php echo $result->reward_details; ?> 
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
                                                                    <input id="send_to_email_rules" name="send_to_email_rules" type="checkbox" value="1" class="checkable"<?php echo $result->send_contest_rules?'checked':'';?> /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Contest Rules & Regulations</label>
                                                                <textarea cols="80" id="rules_regulation" name="rules_regulation" rows="10" class="form-control input-sm cke-editor" value="">
                                                                    <?php echo $result->contest_rules; ?>
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
                                                                    <input id="send_to_email_term" name="send_to_email_term" type="checkbox" value="1" class="checkable"<?php echo $result->send_terms_conditions?'checked':'';?>/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Term & Conditions </label>
                                                                <textarea cols="80" id="term_condition" name="term_condition" rows="10" class="form-control input-sm cke-editor" value="">
                                                                    <?php echo $result->terms_conditions; ?>
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
                                                                    <input id="send_to_email_contact" name="send_to_email_contact" type="checkbox" value="1" class="checkable"<?php echo $result->send_contact_details?'checked':'';?> /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Contact Details</label>
                                                                <textarea cols="80" id="contact_detail" name="contact_detail" rows="10" class="form-control input-sm cke-editor" value="">
                                                                <?php echo $result->contact_details; ?>
                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>      
                                                <div class="tab-pane  <?php echo ($step==2 ? 'active"':'mar'); ?>" id="tab_avatar">
                                                    <span class="text-muted">(Extensions allowed: .png , .jpg, .jpeg)  width:320px, height:60px)</span>
                                                     <div id="tab_images_uploader_container" class="text-align-reverse margin-bottom-10">
                                                         
                                                        <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn yellow">
                                                            <i class="fa fa-plus"></i> Select Files </a>
                                                        <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn green">
                                                            <i class="fa fa-share"></i> Upload Files </a>
                                                    </div>
                                                    <div class="row">
                                                        <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12">
                                                        </div>
                                                    </div>
                                                    <table class="table  table-bordered table-hover order-column" id="ImageTable">
                                                        <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="8%">
                                                                    Image
                                                                </th>
                                                                <th width="25%">
                                                                    URL
                                                                </th>
                                                                <th width="8%">
                                                                    Sort Order
                                                                </th>
                                                                <th width="10%">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                             <?php
                                                                if (count($BannerImageSet) > 0) {
                                                                    foreach ($BannerImageSet as $key => $value) { ?>
                                                                    <tr id="Img<?php echo $value->id; ?>">
                                                                        <td>
                                                                                <?php
                                                                                if (!empty($value->thumbnail_image)) {?>
                                                                                <a href="<?php echo $Image_path.$value->thumbnail_image?>" class="fancybox-button" data-rel="fancybox-button">
                                                                                        <img class="img-responsive" src="<?php echo $Image_path.$value->thumbnail_image?>" alt="">
                                                                                </a>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="url[<?php echo $value->id; ?>]" value="<?php echo $value->url; ?>">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="sort[<?php echo $value->id; ?>]" value="<?php echo $value->sorting; ?>">
                                                                            </td>

                                                                            <td>
                                                                                <a href="javascript:;" class="btn red btn-sm" onclick="RemoveImage(<?php echo $value->id; ?>)">
                                                                                    <i class="fa fa-times"></i> Remove </a>
                                                                            </td>
                                                                        </tr>
                                                                 <?php }
                                                                } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                              </div>
                                                 
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">  
                                                        <button type="submit" id="reward-submit" name="reward-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left">
                                                        <span class="ladda-label">Update</span>
                                                        </button>
                                                        <a href="<?php echo site_url("reward"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                    </div>
                                                </div>                                                                                                                                                  
                                          </div>  
                                    </div></form>
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
        <script src="<?php echo $asset_url;?>assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/avatar_main.js" type="text/javascript"></script>
        <script>            
            jQuery(document).ready(function () {
                 handleImages();
                if (jQuery().datepicker) {
                    $('.date-picker').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                }
                CKEDITOR.replace('reward_details');
                CKEDITOR.replace('rules_regulation');
                CKEDITOR.replace('term_condition');
                CKEDITOR.replace('contact_detail');
                $(".select2, .select2-multiple", frmUsers).change(function () {
                    frmUsers.validate().element($(this));
                });
                        
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
//                    errorPlacement: function (error, element) {
//                        if (element.parents('.mt-radio-list') || element.parents('.mt-checkbox-list')) {
//                            if (element.parents('.mt-radio-list')[0]) {
//                                error.appendTo(element.parents('.mt-radio-list')[0]);
//                            }
//                            if (element.parents('.mt-checkbox-list')[0]) {
//                                error.appendTo(element.parents('.mt-checkbox-list')[0]);
//                            }
//                        } else if (element.parents('.mt-radio-inline') || element.parents('.mt-checkbox-inline')) {
//                            if (element.parents('.mt-radio-inline')[0]) {
//                                error.appendTo(element.parents('.mt-radio-inline')[0]);
//                            }
//                            if (element.parents('.mt-checkbox-inline')[0]) {
//                                error.appendTo(element.parents('.mt-checkbox-inline')[0]);
//                            }
//                        } else if (element.parent(".input-group").size() > 0) {
//                            error.insertAfter(element.parent(".input-group"));
//                        } else if (element.attr("data-error-container")) {
//                            error.appendTo(element.attr("data-error-container"));
//                        } else {
//                            error.insertAfter(element);
//                        }
//                    },
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
                    data: {offer_code:value,company_id:$('#company_id').val(),reward_id:<?php echo $result->id ?>},
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
            var handleImages = function () {
                // see http://www.plupload.com/
                var uploader = new plupload.Uploader({
                    runtimes: 'html5,flash,silverlight,html4',
                    browse_button: document.getElementById('tab_images_uploader_pickfiles'), // you can pass in id...
                    container: document.getElementById('tab_images_uploader_container'), // ... or DOM Element itself
                    
                    url: "<?php echo base_url().'reward/UploadBanner/'.$Row->id; ?>",
                    filters: {
                        max_file_size: '10mb',
                        mime_types: [
                            {title: "Image files", extensions: "jpg,gif,jpeg,png"}
                        ]
                    },
                    // Flash settings
                    flash_swf_url: 'assets/plugins/plupload/js/Moxie.swf',
                    // Silverlight settings
                    silverlight_xap_url: 'assets/plugins/plupload/js/Moxie.xap',
                    init: {
                        PostInit: function () {
                            $('#tab_images_uploader_filelist').html("");
                            
                            $('#tab_images_uploader_uploadfiles').click(function () {
                                uploader.start();
                                return false;
                            });
                            
                            $('#tab_images_uploader_filelist').on('click', '.added-files .remove', function () {
                                uploader.removeFile($(this).parent('.added-files').attr("id"));    
                                $(this).parent('.added-files').remove();                     
                            });
                        },
                        FilesAdded: function (up, files) {
                            plupload.each(files, function (file) {
                                $('#tab_images_uploader_filelist').append('<div class="alert alert-warning added-files" id="uploaded_file_' + file.id + '">' + file.name + '(' + plupload.formatSize(file.size) + ') <span class="status label label-info"></span>&nbsp;<a href="javascript:;" style="margin-top:-5px" class="remove pull-right btn btn-sm red"><i class="fa fa-times"></i> remove</a></div>');
                            });
                        },
                        UploadProgress: function (up, file) {
                            $('#uploaded_file_' + file.id + ' > .status').html(file.percent + '%');
                        },
                        FileUploaded: function (up, file, response) {
                            var response = $.parseJSON(response.response);
                            
                            if (response.result && response.result == 'OK') {
                                var id = response.id; // uploaded file's unique name. Here you can collect uploaded file names and submit an jax request to your server side script to process the uploaded files and update the images tabke
                                
                                $('#uploaded_file_' + file.id + ' > .status').removeClass("label-info").addClass("label-success").html('<i class="fa fa-check"></i> Done');
                                var html ='<tr id="Img'+response.NewId+'">';
                                    html +='<td><a href="'+response.image+'" class="fancybox-button" data-rel="fancybox-button"><img class="img-responsive" src="'+response.image+'" alt=""></a></td>'
                                    html +='<td><input type="text" class="form-control" name="url['+response.NewId+']" value=""></td>'
                                    html +='<td><input type="number" class="form-control" name="sort['+response.NewId+']" value="'+response.NewSortNo+'"></td>'
                                    html +='<td><a href="javascript:;" class="btn red btn-sm" onclick="RemoveImage('+response.NewId+')"><i class="fa fa-times"></i> Remove </a></td>';
                                    html +='<tr>';
                                $('#ImageTable tr:last').after(html);
                                // set successfull upload
                            } else {
                                $('#uploaded_file_' + file.id + ' > .status').removeClass("label-info").addClass("label-danger").html('<i class="fa fa-warning"></i> Failed'); // set failed upload
                                ShowAlret(response.result.error,'error');
                            }
                        },
                        Error: function (up, err) {
                            ShowAlret(err.message,'error');
                        }
                    }
                });
                
                uploader.init();
            }
            function RemoveImage(ImgId){
                $.confirm({
                    title: 'Confirm!',
                    content: " Are you sure you want to delete this banner ? ",
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function(){
                            $.ajax({
                                type: "POST",
                                data: {company_id:$('#company_id').val(),ImageId:ImgId},
                                url: "<?php echo base_url(); ?>reward/RemoveBanner",
                                success: function (Flag) {
                                    if (Flag) {
                                        $('#Img' + ImgId).remove(); 
                                        ShowAlret('Banner image deleted successfully','success');
                                    }
                                }
                            });
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                    }
                });
            }
        </script>
    </body>
</html>