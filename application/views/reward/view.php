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
                                    <span>View Reward</span>
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
                                            View Reward
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
                                                    <a href="#tab_avatar" data-toggle="tab">Banners</a>
                                                </li>
                                            </ul>
                                             <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url; ?>reward/submit" enctype="multipart/form-data"> 
                                            
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
                                            
                                                <div class="tab-pane active" id="tab_overview"> 
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
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
                                                                    <input type="text" name="sponsor_name" id="sponsor_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->sponsor_name; ?>" disabled>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Reward Title<span class="required"> * </span></label>
                                                                    <input type="text" name="reward_title" id="reward_title" maxlength="255" class="form-control input-sm" value="<?php echo $result->reward_name; ?>" disabled>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Short Description</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="150" name="short_description" placeholder="" value="" disabled><?php echo $result->short_description; ?></textarea>
                                                                    <span class="text-muted">(Max 150 Characters)</span>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="255" class="form-control input-sm" value="<?php echo $result->email; ?>" disabled>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Start/End Date<span class="required"> * </span></label>
                                                                    <div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="dd/mm/yyyy">
                                                                        <input type="text" class="form-control input-sm" id="start_date" name="start_date" value="<?php echo $result->start_date; ?>" disabled>
                                                                        <span class="input-group-addon"> to </span>
                                                                        <input type="text" class="form-control input-sm" id="end_date" name="end_date" value="<?php echo $result->end_date; ?>" disabled>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-2">    
                                                                    <div class="form-group">
                                                                        <label>Offer Code<span class="required"> * </span></label>
                                                                        <input type="text" name="offer_code" id="offer_code" maxlength="255" class="form-control input-sm" value="<?php echo $result->offer_code; ?>" disabled>                                                                           
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Voucher Quantity</label>
                                                                    <input type="text" name="qty" id="qty" class="form-control input-sm" value="<?php echo $result->quantity; ?>" disabled>   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Redeem Point</label>
                                                                    <input type="text" name="stride_limit" id="stride_limit" class="form-control input-sm" value="<?php echo $result->stride_limit; ?>" disabled>                                                                       
                                                                </div>
                                                            </div>                                                            
                                                        </div>                                                                                                              
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <input type="text" name="remarks" id="remarks" maxlength="255" class="form-control input-sm" value="<?php echo $result->remarks; ?>" disabled>                                                                    
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                        
                                                        <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" disabled>
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
                                                                    <input id="send_to_email_reward" name="send_to_email_reward" type="checkbox" value="1" class="checkable"<?php echo $result->send_reward_details?'checked':'';?> disabled/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Reward Details</label>
                                                                <textarea cols="80" id="reward_details" name="reward_details" rows="10" class="form-control input-sm cke-editor" value="" disabled>
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
                                                                    <input id="send_to_email_rules" name="send_to_email_rules" type="checkbox" value="1" class="checkable"<?php echo $result->send_contest_rules?'checked':'';?> disabled/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Contest Rules & Regulations</label>
                                                                <textarea cols="80" id="rules_regulation" name="rules_regulation" rows="10" class="form-control input-sm cke-editor" value="" disabled>
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
                                                                    <input id="send_to_email_term" name="send_to_email_term" type="checkbox" value="1" class="checkable"<?php echo $result->send_terms_conditions?'checked':'';?> disabled/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Term & Conditions </label>
                                                                <textarea cols="80" id="term_condition" name="term_condition" rows="10" class="form-control input-sm cke-editor" value="" disabled>
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
                                                                    <input id="send_to_email_contact" name="send_to_email_contact" type="checkbox" value="1" class="checkable"<?php echo $result->send_contact_details?'checked':'';?> disabled /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-11">    
                                                            <div class="form-group">
                                                                <label>Contact Details</label>
                                                                <textarea cols="80" id="contact_detail" name="contact_detail" rows="10" class="form-control input-sm cke-editor" value="" disabled>
                                                                <?php echo $result->contact_details; ?>
                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>      
                                                <div class="tab-pane mar" id="tab_avatar">
                                                         <fieldset disabled="">
                                                    <table class="table  table-bordered table-hover order-column dataTable " id="ImageTable">
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
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                             <?php
                                                                if (count($BannerImageSet) > 0) {
                                                                    foreach ($BannerImageSet as $key => $value) { ?>
                                                                    <tr id="Img<?php echo $value->id; ?>" >
                                                                        <td>
                                                                                <?php
                                                                                if (!empty($value->thumbnail_image)) {
                                                                                    ?>
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
                                                                        </tr>
                                                                 <?php }
                                                                } ?>
                                                        </tbody>
                                                    </table>
                                                   </fieldset>
                                                </div>                                                    
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">                                                          
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

        <script src="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/avatar_main.js" type="text/javascript"></script>
        <script>
             
            jQuery(document).ready(function () {
                
                $(".cke-editor").ckeditor();     
                CKEDITOR.replace('reward_details');
                CKEDITOR.replace('rules_regulation');
                CKEDITOR.replace('term_condition');
                CKEDITOR.replace('contact_detail');
                $(".select2, .select2-multiple", frmUsers).change(function () {
                    frmUsers.validate().element($(this));
                });
                if (jQuery().datepicker) {
                    $('.date-picker').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                }
            });
        </script>
    </body>
</html>