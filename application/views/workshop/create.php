<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>

        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/css/jquery.timepicker.min.css"/>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <style>
            .ui-timepicker-container{
                z-index: 99999999 !important;
            }
            .customhr{
                border-bottom: 1px solid #eee;
            }
            .rightborder{
                border-right: 1px solid #eee;
            }
            table tr {
                background-color: #fff;
            }
            .table.table-light thead tr th{
                color: #000000 !important;
            }
            .table.table-light tbody tr td{
                color: #000000 !important;
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
                                    <span>Workshop</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Create New Workshop</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>workshop" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Create Workshop
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="WorkshopForm" name="WorkshopForm" method="POST"  action="<?php echo $base_url; ?>workshop/submit" enctype="multipart/form-data"> 
                                            <div class="tabbable-line tabbable-full-width">
                                                <ul class="nav nav-tabs" id="tabs">
                                                    <li class="active">
                                                        <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_session" data-toggle="tab" >Session (PRE/POST) </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascrip:void(0);" >Allowed Users</a>
                                                    </li>
                                                    <li disabled>
                                                        <a href="javascrip:void(0);" >Banners</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">    
                                                        <input type="hidden" value="<?php echo $Session_code; ?>" name="token_key" id="token_key">
                                                        <?php
                                                        if ($errors == "") {
                                                            $errors = validation_errors();
                                                        }
                                                        if ($errors) {
                                                            ?>
                                                            <div style="display: block;" class="alert alert-danger display-hide">
                                                                <button class="close" data-close="alert"></button>
                                                                You have some form errors. Please check below.
                                                                <?php echo $errors; ?>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="alert alert-danger display-hide" id="errordiv">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <br><span id="errorlog"></span>
                                                        </div>
                                                        <fieldset>
                                                            <?php if ($Company_id == "") { ?>
                                                                <div class="row">
                                                                    <div class="col-md-6">    
                                                                        <div class="form-group">
                                                                            <label>Company<span class="required"> * </span></label>
                                                                            <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" onchange="question_set();">
                                                                                <option value="">Please Select</option>
                                                                                <?php foreach ($cmpdata as $cmp) { ?>
                                                                                    <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>                                                        
                                                                </div>
                                                            <?php } ?>
                                                            <div class="row">                                                     
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Creation Date<span class="required"> * </span></label>
                                                                        <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                            <input placeholder="DD-MM-YYYY" id="creation_date" name="creation_date" class="form-control date-picker input-sm" size="18" type="text" value="<?php echo date('d-m-Y'); ?>" 
                                                                                   data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                                        </div> 
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Workshop One Time Code (OTC)</label>
                                                                        <input type="text" name="otp" value="<?php echo generateRandomString(6); ?>" id="otp" maxlength="6" class="form-control input-sm uppercase">   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Workshop Name<span class="required"> * </span></label>
                                                                        <input type="text" name="workshop_name" id="workshop_name" maxlength="255" class="form-control input-sm" value="<?php echo set_value('workshop_name'); ?>">   
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Workshop Region<span class="required"> * </span></label>
                                                                        <select id="region" name="region" class="form-control input-sm select2" placeholder="Please select" onchange="getRegionwisedata();">
                                                                            <option value="">Please Select</option>
                                                                            <?php foreach ($Region as $rg) { ?>
                                                                                <option value="<?= $rg->id; ?>"><?php echo $rg->region_name; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>                                                        
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Sub Region</label>
                                                                        <select id="subregion" name="subregion" class="form-control input-sm select2" placeholder="Please select" >
                                                                            <option value="">Please Select</option>
                                                                            <?php foreach ($SubRegion as $sr) { ?>                                                                                
                                                                                <option value="<?= $sr->id; ?>"><?php echo $sr->sub_region; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Workshop Type<span class="required"> * </span></label>
                                                                        <select id="wktype" name="wktype" class="form-control input-sm select2" placeholder="Please select" onchange="getWsubtypedata();">
                                                                            <option value="">Please Select</option>
                                                                            <?php foreach ($WorkshopType as $wt) { ?>
                                                                                <option value="<?= $wt->id; ?>"><?php echo $wt->workshop_type; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Workshop Sub-Type</label>
                                                                        <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select" >
                                                                            <option value="">Please Select</option>
                                                                            <?php foreach ($WorkshopSubType as $wst) { ?>
                                                                                <option value="<?= $wst->id; ?>"><?php echo $wst->sub_type; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>                                                                                                                        
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Powered By<span class="required"> * </span></label>
                                                                        <input type="text" name="powered_by" id="powered_by" maxlength="255" class="form-control input-sm" value="<?php echo set_value('powered_by'); ?>">   
                                                                    </div>
                                                                </div>
                                                            <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Default Language<span class="required"> * </span></label>
                                                                <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" >
                                                                    <?php if(isset($language_mst)){
                                                                            foreach ($language_mst as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>"><?php echo $Row->name ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                            </div>
                                                        </fieldset>    
                                                        <fieldset>
                                                            <legend>Other Information:</legend> 
                                                            <div class="row">
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Heading<span class="required"> * </span></label>
                                                                        <input type="text" name="heading" id="heading" maxlength="255" class="form-control input-sm">
                                                                        <span class="text-muted">(This heading will be displayed on workshop completed page.)</span>   
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Message<span class="required"> * </span></label>
                                                                        <input type="text" name="message" id="message" maxlength="255" class="form-control input-sm">
                                                                        <span class="text-muted">(This message will be displayed on workshop completed page, below heading.)</span>      
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="my-line"></div>
                                                            <div class="row">
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Note</label>
                                                                        <textarea rows="4" class="form-control input-sm" name="remarks" id="remarks" placeholder=""></textarea>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">    
                                                                    <div class="form-group">
                                                                        <label>Workshop Image</label>
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
                                                                                    <input type="file" name="workshop_image" id="workshop_image" >
                                                                                </span>
                                                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                                                    Remove </a>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-muted">(Extensions allowed: .png , .gif, .jpg, .jpeg)</span>
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
                                                        </fieldset>
                                                        <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave('A');">
                                                                    <span class="ladda-label">Save & Next</span>
                                                                </button>
                                                                <a href="<?php echo site_url("workshop"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="tab_session">
                                                        <div class="alert alert-danger display-hide" id="errordiv2">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <br><span id="errorlog2"></span>
                                                        </div>
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>QuestionSet Type<span class="required"> * </span></label>
                                                                        <select id="pre_question_type" name="pre_question_type" class="form-control input-sm select2" placeholder="Please select" onchange="SelectedQuestionSet();" >
                                                                            <option value="1" selected>Question Set</option>
                                                                            <option value="2">Feedback Set</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Default Trainer<span class="required"> </label>
                                                                        <select id="df_trainer_id" name="df_trainer_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                            <option value="" >Please select</option>
                                                                            <?php
                                                                            if (count($df_trainer_list) > 0) {
                                                                                foreach ($df_trainer_list as $wst) {
                                                                                    ?>
                                                                                    <option value="<?= $wst->userid; ?>">
                                                                                        <?php echo $wst->trainer; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        <span class="text-muted">(Only Applicable for New Question set)</span>
                                                                    </div>
                                                                </div>
                                                            </div>  

                                                            <div class="row prePostQuestionRow" >                                                                                                                    
                                                                <div class="col-md-6 rightborder">    
                                                                    <h4 class="form-section customhr">Pre Session :</h4>
                                                                    <div class="form-group">
                                                                        <label>Pre-Question Set</label>
                                                                        <select id="pre_question_set" name="pre_question_set[]" class="groupSelectClass form-control input-sm select2" placeholder="Please select" multiple="" >
                                                                            <?php
                                                                            if (count($question_Qresult) > 0) {
                                                                                foreach ($question_Qresult as $qset) {
                                                                                    ?>
                                                                                    <option value="<?= $qset->id; ?>"><?php echo $qset->title; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>                                                        
                                                                <div class="col-md-6">  
                                                                    <h4 class="form-section customhr">Post Session :</h4>
                                                                    <div class="form-group">
                                                                        <label>Post-Question Set</label>
                                                                        <select id="post_question_set" name="post_question_set[]" class="groupSelectClass form-control input-sm select2" placeholder="Please select" multiple="" >
                                                                            <?php
                                                                            if (count($question_Qresult) > 0) {

                                                                                foreach ($question_Qresult as $qset) {
                                                                                    ?>
                                                                                    <option value="<?= $qset->id; ?>"><?php echo $qset->title; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row prePostQuestionRow" >
                                                                <div class="col-md-6 rightborder">    
                                                                    <div class="form-group">
                                                                        <table class="table table-hover table-light " id="preQussetTable" width="50%">
                                                                            <thead>
                                                                                <tr style="background-color: #e6f2ff;">
                                                                                    <th colspan="2">Pre-Question Set</th>                 
                                                                                    <th>Time</th>
                                                                                    <th>Total Qus</th>
                                                                                    <th width="10">Show Answer</th>
                                                                                    <th >Action</th>
                                                                                </tr></thead>
                                                                            <tbody id="PreTbody">

                                                                            </tbody></table>
                                                                    </div>
                                                                </div>                                                        
                                                                <div class="col-md-6 rightborder">    
                                                                    <div class="form-group">
                                                                        <table class="table table-hover table-light " id="preQussetTable" width="50%">
                                                                            <thead>
                                                                                <tr style="background-color: #e6f2ff;">
                                                                                    <th colspan="2">Post-Question Set</th>
                                                                                    <th>Time</th>
                                                                                    <th>Total Qus</th>
                                                                                    <th width="10">Show Answer</th>
                                                                                    <th >Action</th>
                                                                                </tr></thead>
                                                                            <tbody id="PostTbody">

                                                                            </tbody></table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row prePostFeedBackRow" >
                                                                <div class="col-md-6 rightborder">    
                                                                    <div class="form-group">
                                                                        <label>Pre Feedback Set</label>
                                                                        <select id="pre_feedback_id" name="pre_feedback_id[]" class="groupSelectClass2 form-control input-sm select2 PreSessionTime" placeholder="Please select" multiple="" >
                                                                            <?php
                                                                            if (count($feedbackset_Qresult) > 0) {
                                                                                foreach ($feedbackset_Qresult as $qset) {
                                                                                    ?>
                                                                                    <option value="<?= $qset->id; ?>"><?php echo $qset->title; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>                                                        
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Post Feedback Set</label>
                                                                        <select id="post_feedback_id" name="post_feedback_id[]" class="groupSelectClass2 form-control input-sm select2 PostSessionTime" placeholder="Please select" multiple="" >
                                                                            <?php
                                                                            if (count($feedbackset_Qresult) > 0) {
                                                                                foreach ($feedbackset_Qresult as $qset) {
                                                                                    ?>
                                                                                    <option value="<?= $qset->id; ?>"><?php echo $qset->title; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row prePostFeedBackRow" >
                                                                <div class="col-md-6 rightborder">    
                                                                    <div class="form-group">
                                                                        <table class="table table-hover table-light " id="preQussetTable" width="50%">
                                                                            <thead>
                                                                                <tr style="background-color: #e6f2ff;">
                                                                                    <th colspan="2">Pre-Feedback Set</th>
                                                                                    <th>Time</th>
                                                                                    <th>Total Qus</th>
                                                                                    <th >Action</th>
                                                                                </tr></thead>
                                                                            <tbody id="PreFeedTbody">

                                                                            </tbody></table>
                                                                    </div>
                                                                </div>                                                        
                                                                <div class="col-md-6 rightborder">    
                                                                    <div class="form-group">
                                                                        <table class="table table-hover table-light " id="preQussetTable" width="50%">
                                                                            <thead>
                                                                                <tr style="background-color: #e6f2ff;">
                                                                                    <th colspan="2">Post-Feedback Set</th>
                                                                                    <th>Time</th>
                                                                                    <th>Total Qus</th>
                                                                                    <th>Action</th>
                                                                                </tr></thead>
                                                                            <tbody id="PostFeedTbody">
                                                                            </tbody></table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="start_date" name="start_date" class="form-control date-picker2 input-sm PreSessionTime" size="18" type="text" value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="start_time" name="start_time" class="form-control timepicker timepicker-no-seconds PreSessionTime" autocomplete="off">                                                                            
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>  
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="post_start_date" name="post_start_date" class="form-control date-picker2 input-sm PostSessionTime" size="18" type="text" value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="post_start_time" name="post_start_time" class="form-control timepicker timepicker-no-seconds PostSessionTime" autocomplete="off">
        <!--                                                                        <span class="input-group-btn">
                                                                                <button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
                                                                                </span>                                                                            -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>                                                      

                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="end_date" name="end_date" class="form-control date-picker2 input-sm PreSessionTime" size="18" type="text" value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" id="end_time" placeholder="h:mm" name="end_time" class="form-control timepicker timepicker-no-seconds PreSessionTime" autocomplete="off">

                                                                            </div>                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="post_end_date" name="post_end_date" class="form-control date-picker2 input-sm PostSessionTime" size="18" type="text" value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="post_end_time" name="post_end_time" class="form-control timepicker timepicker-no-seconds PostSessionTime" autocomplete="off">                                                                            
                                                                            </div>                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row margin-top-10">
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label >Feedback Trigger after Nos. of Questions:<span class="required"> * </span></label>
                                                                        <input type="number" name="prefeedback_trigger" id="prefeedback_trigger" min="1" class="form-control input-sm PreFeedbackSession">   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">    
                                                                    <div class="form-group" style="padding-top: 25px; float: right;">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="Pretime_status"> In-Active Pre Session
                                                                            <input class="PreSessionTime" id="Pretime_status" name="Pretime_status" type="checkbox" value="1" /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label >Feedback Trigger after Nos. of Questions:<span class="required"> * </span></label>
                                                                        <input type="number" name="postfeedback_trigger" id="postfeedback_trigger" min="1" class="form-control input-sm PostFeedbackSession" >   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group" style="padding-top: 25px; float: right;">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="Posttime_status"> In-Active Post Session
                                                                            <input class="PostSessionTime" id="Posttime_status" name="Posttime_status" type="checkbox" value="1" /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="row">                                                        
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Information Form</label>
                                                                        <select id="feedback_form" name="feedback_form" class="form-control input-sm select2" placeholder="Please select">
                                                                            <option value="">Please Select</option>
                                                                            <?php
                                                                            if (count($feedback_form) > 0) {
                                                                                foreach ($feedback_form as $qset) {
                                                                                    ?>
                                                                                    <option value="<?= $qset->id; ?>"><?php echo $qset->form_name; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Questions Order</label>
                                                                        <select id="questions_order" name="questions_order" class="form-control input-sm select2" placeholder="Please select">
                                                                            <option value="0">Question Set</option>
                                                                            <option value="1">Random</option>
                                                                            <option value="2">Sequence</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Feedback Questions Order</label>
                                                                        <select id="feedback_qus_order" name="feedback_qus_order" class="form-control input-sm select2" placeholder="Please select">
                                                                            <option value="0">Feedback Set</option>
                                                                            <option value="1">Random</option>
                                                                            <option value="2">Sequence</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend>Point Multiplier Setting:</legend>  
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Point Multiplier<span class="required"> * </span></label>
                                                                        <input type="number" name="point_multiplier" id="point_multiplier" min="1" class="form-control input-sm" value="<?php echo '1'; ?>">   
                                                                        <span class="text-muted">On each correct answer</span>
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Time (In Sec.)</label>
                                                                        <input type="text" name="time" id="time" maxlength="255" class="form-control input-sm" value="20">
                                                                        <span class="text-muted">This time will be used if no time is mapped to the question set</span>
                                                                    </div>
                                                                </div>   -->                                                    
                                                                <!--                                                            <div class="col-md-3">    
                                                                                                                                <div class="form-group">
                                                                                                                                    <label>Target</label>
                                                                                                                                    <input type="text" name="target" id="target" maxlength="255" class="form-control input-sm">   
                                                                                                                                    <span class="text-muted">(Question to be played)</span>
                                                                                                                                </div>
                                                                                                                            </div>-->
                                                                <div class="col-md-2">    
                                                                    <div class="form-group">
                                                                        <label>Payback Option</label>
                                                                        <select id="payback_option" name="payback_option" class="form-control input-sm select2" placeholder="Please select" onchange="ShowRedeem();" >
                                                                            <option value="0" selected>None</option>
                                                                            <option value="1">Redeem</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4" style="display:none" id="RewardBox">    
                                                                    <div class="form-group">
                                                                        <label>Reward<span class="required"> * </span></label>
                                                                        <select id="reward_id" name="reward_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
                                                                            <option value="">Please Select</option>
                                                                            <?php
                                                                            if (count($RewardResult) > 0) {
                                                                                foreach ($RewardResult as $qset) {
                                                                                    ?>
                                                                                    <option value="<?= $qset->id; ?>"><?php echo $qset->reward_name; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="hide_on_website"> Hide workshop on the website
                                                                            <input id="hide_on_website" name="hide_on_website" type="checkbox" value="1" /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="play_all_feedback"> Play all feedback
                                                                            <input id="play_all_feedback" name="play_all_feedback" type="checkbox" value="1" /><span></span>
                                                                        </label>
                                                                        <a data-title="After all question palyed by trainee. If any feedback is left, then it will fiered & it will stop." style="margin-left:5px;">
                                                                            <i class="icon-info font-black sub-title"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">    
                                                                    <div class="form-group">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="end_time_display"> Do you want to hide end time?
                                                                            <input id="end_time_display" name="end_time_display" type="checkbox" value="1" /><span></span>
                                                                        </label>
                                                                        <a data-title="In atom app, Workshop end time will be visible." style="margin-left:5px;">
                                                                            <i class="icon-info font-black sub-title"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave('A');">
                                                                    <span class="ladda-label">Save & Next</span>
                                                                </button>
                                                                <a href="<?php echo site_url("workshop"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>
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
                <?php //$this->load->view('inc/inc_quick_sidebar');     ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');    ?>
        </div>
        <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body">
                        <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                        <span>
                            &nbsp;&nbsp;Loading... </span>
                    </div>
                </div>
            </div>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');    ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>    
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/scripts/jquery.timepicker.min.js"></script>
        <script>
    var NewUsersArrray = [];
    var oTable = null;
    var base_url = "<?php echo $base_url; ?>";
    var EncodeEdit_id = "";
    var AddEdit = 'A';
    var company_id = '<?php echo $Company_id; ?>';
    var PreLock = 0;
    var PostLock = 0;
    var token_key = '<?php echo $Session_code; ?>';
</script>
<script type="text/javascript" src="<?php echo $base_url; ?>assets/customjs/workshop_validation.js"></script>
<script>
    jQuery(document).ready(function () {
        //$(".cke-editor").ckeditor();
        $(".PreSessionTime,.PostSessionTime,#post_feedback_id,#pre_feedback_id,\n\
            .PostFeedbackSession,.PreFeedbackSession").prop('disabled', true);
        $('.timepicker').timepicker({
            timeFormat: 'h:mm p',
            interval: 60,
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
        if (jQuery().datepicker) {
            $('.date-picker2').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                startDate: '+0d'
            });

            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                endDate: '+0d'
            });
        }
        //CKEDITOR.replace('long_description');
    });
        </script>
    </body>
</html>