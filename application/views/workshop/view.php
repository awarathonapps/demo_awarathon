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
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url;?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url;?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url;?>assets/global/css/jquery.timepicker.min.css"/>
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
                                    <span>View Workshop</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>workshop" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Workshop Details
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        
                                            <div class="tabbable-line tabbable-full-width">
                                                <ul class="nav nav-tabs" id="tabs">
                                                    <li <?php echo ($step==1 ? 'class="active"':''); ?> >
                                                        <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                    </li>
                                                    <li  <?php echo ($step==2 ? 'class="active"':''); ?>>
                                                        <a href="#tab_users" data-toggle="tab" >Allowed Users</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_testers" data-toggle="tab" >Testing Users</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_avatar" data-toggle="tab" >Banners</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane <?php echo ($step==1 ? 'active"':''); ?>" id="tab_overview">    
                                                        <form id="WorkshopForm" name="WorkshopForm" method="POST"  action="<?php echo $base_url.'/workshop/update/'.base64_encode($Row->id);?>" enctype="multipart/form-data"> 
                                                            <fieldset disabled="">
                                                    <legend>General Information:</legend> 
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm " placeholder="Please select"  style="width:100%" onchange="question_set();" disabled="">
                                                                        <?php if(count($SelectCompany)>0){ 
                                                                                foreach ($SelectCompany as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>" <?php echo ($Row->company_id==$value->id ? 'selected':'') ?>><?php echo $value->company_name ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                          
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Creation Date<span class="required"> * </span></label>
                                                                    <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                        <input placeholder="DD-MM-YYYY" id="creation_date" name="creation_date" class="form-control date-picker input-sm" size="18" type="text" value="<?php echo date('d-m-Y',  strtotime($Row->creation_date)); ?>" 
                                                                                data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years"></div>
                                                                </div> 
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Workshop OneTimeCode (OTC)</label>
                                                                    <input type="text" name="otp" id="otp" maxlength="6" class="form-control input-sm" value="<?php echo $Row->	otp; ?>" style="text-transform:uppercase;">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Workshop Name<span class="required"> * </span></label>
                                                                    <input type="text" name="workshop_name" id="workshop_name" maxlength="255" class="form-control input-sm" 
                                                                           value="<?php echo $Row->workshop_name; ?>">   
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                                                                                     
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Workshop Region<span class="required"> * </span></label>
                                                                    <select id="region" name="region" class="form-control input-sm " placeholder="Please select" onchange="getRegionwisedata();">
                                                                        <?php if(count($Region)>0){ 
                                                                            foreach ($Region as  $rgn) { ?>
                                                                                <option value="<?php echo $rgn->id ?>" <?php echo ($Row->region==$rgn->id ? 'selected':'') ?>><?php echo $rgn->region_name ?></option>
                                                                                <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>                                                        
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Sub Region</label>
                                                                    <select id="subregion" name="subregion" class="form-control input-sm " placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        <?php if(count($SubRegion)>0){                                                                            
                                                                         foreach ($SubRegion as $sr) { ?>                                                                                
                                                                            <option value="<?= $sr->id; ?>" <?php echo ($Row->workshopsubregion_id==$sr->id ? 'selected':'') ?>><?php echo $sr->sub_region; ?></option>
                                                                        <?php } 
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Workshop Type<span class="required"> * </span></label>
                                                                    <select id="wktype" name="wktype" class="form-control input-sm " placeholder="Please select" onchange="getWsubtypedata();">
                                                                        <?php if(count($WorkshopType)>0){ 
                                                                        foreach ($WorkshopType as $key => $wktype) { ?>
                                                                                <option value="<?php echo $wktype->id ?>" <?php echo ($Row->workshop_type==$wktype->id ? 'selected':'') ?>><?php echo $wktype->workshop_type ?></option>
                                                                                <?php   }
                                                                            } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Workshop Sub-Type</label>
                                                                    <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm " placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        <?php if(count($WorkshopSubType)>0){
                                                                            foreach ($WorkshopSubType as $wst) { ?>
                                                                            <option value="<?= $wst->id; ?>" <?php echo ($Row->workshopsubtype_id==$wst->id ? 'selected':'') ?>><?php echo $wst->sub_type; ?></option>
                                                                        <?php } 
                                                                        } ?>
                                                                    </select>
                                                        </div>                                                                                                                                                                                                                                                                                                                                                                                                        
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Powered By<span class="required"> * </span></label>
                                                                    <input type="text" name="powered_by" id="powered_by" maxlength="255" value="<?php echo $Row->powered_by; ?>" class="form-control input-sm">   
                                                                </div>
                                                            </div>                                                        
                                                        </div>
                                                    </fieldset>
                                                            <fieldset disabled="">
                                                    <legend>Question Information:</legend>
                                                    <div class="row">
                                                    <div class="col-md-3">    
                                                        <div class="form-group">
                                                            <label>QuestionSet Type<span class="required"> * </span></label>
                                                            <select id="pre_question_type" name="pre_question_type" class="form-control input-sm " placeholder="Please select" onchange="SelectedQuestionSet()">
                                                                <option value="">Please Select</option>
                                                                <option value="1" <?php echo($Row->questionset_type==1 ? 'Selected':''); ?>>Question Set</option>
                                                                <option value="2" <?php echo($Row->questionset_type==2 ? 'Selected':''); ?>>Feedback Set</option>                                                                        
                                                            </select>
                                                        </div>
                                                    </div>
                                                    </div>
                                                        <div class="row prePostQuestionRow" <?php echo ($Row->questionset_type==2 ? 'style="display:none"' :'') ?> >
                                                            <div class="col-md-6 rightborder"> 
                                                                <h4 class="form-section customhr">Pre Session :</h4>
                                                                <div class="form-group">
                                                                    <label>Pre-Question Set</label>
                                                                    <select id="pre_question_set" name="pre_question_set[]" class="groupSelectClass form-control input-sm select2" placeholder="Please select" disabled="" multiple="" 
                                                                         >
                                                                         <?php if(count($pre_SelectQuestionSet)>0){ 
                                                                                foreach ($pre_SelectQuestionSet as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>" <?php echo ($Row->questionset_type==1 && $value->wc_id==$value->id ? 'selected':'') ?>><?php echo $value->text ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>                                                        
                                                            <div class="col-md-6">
                                                                <h4 class="form-section customhr">Post Session :</h4>
                                                                <div class="form-group">
                                                                    <label>Post-Question Set</label>
                                                                    <select id="post_question_set" name="post_question_set[]" class="groupSelectClass form-control input-sm select2" disabled="" placeholder="Please select" multiple="" >
                                                                       <?php if(count($post_SelectQuestionSet)>0){ 
                                                                                foreach ($post_SelectQuestionSet as $key => $value) { ?>
                                                                                    <option value="<?php echo $value->id ?>" <?php echo ($Row->questionset_type==1 && $value->wc_id==$value->id ? 'selected':'') ?>><?php echo $value->text ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row prePostQuestionRow" <?php echo ($Row->questionset_type==2 ? 'style="display:none"' :'') ?> >
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
                                                                    <select id="pre_feedback_id" name="pre_feedback_id[]" class="groupSelectClass2 form-control input-sm select2 PreSessionTime" placeholder="Please select" multiple="" disabled="" >
                                                                        <?php if(count($pre_SelectFeedbackSet)>0){ 
                                                                                foreach ($pre_SelectFeedbackSet as $key => $value) { ?>
                                                                                <option value="<?php echo $value->id ?>" <?php echo ($value->wc_id==$value->id ? 'selected':'') ?>><?php echo $value->text ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>                                                        
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Post Feedback Set</label>
                                                                    <select id="post_feedback_id" name="post_feedback_id[]" class="groupSelectClass2 form-control input-sm select2 PostSessionTime" placeholder="Please select" multiple="" disabled=""
                                                                            >
                                                                        <?php if(count($post_SelectFeedbackSet)>0){ 
                                                                                foreach ($post_SelectFeedbackSet as $key => $value) { ?>
                                                                                <option value="<?php echo $value->id ?>" <?php echo ($value->wc_id==$value->id ? 'selected':'') ?>><?php echo $value->text ?></option>
                                                                        <?php   }
                                                                        } ?>
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
                                                                            <th >Action</th>
                                                                        </tr></thead>
                                                                        <tbody id="PostFeedTbody">
                                                                             
                                                                        </tbody></table>
                                                                            </div>
                                                                        </div>
                                                        </div>
                                                        <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="start_date" name="start_date" class="form-control  input-sm " size="18" type="text" value="<?php echo ($Row->pre_start_date !="1970-01-01" ? date('d-m-Y',  strtotime($Row->pre_start_date)) : ''); ?>"
                                                                                    data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="start_time" name="start_time" 
                                                                                       class="form-control  timepicker-no-seconds " value="<?php echo $Row->pre_start_time; ?>" 
                                                                                           >                                                                            
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>  
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="post_start_date" name="post_start_date" class="form-control input-sm " size="18" type="text" 
                                                                                       value="<?php echo ($Row->post_start_date !="1970-01-01" ? date('d-m-Y',  strtotime($Row->post_start_date)) : ''); ?>" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="post_start_time" name="post_start_time" 
                                                                                       class="form-control  timepicker-no-seconds " 
                                                                                       value="<?php echo $Row->post_start_time; ?>">
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
                                                                                <input placeholder="DD-MM-YYYY" id="end_date" name="end_date" class="form-control  input-sm " size="18" type="text" 
                                                                                        value="<?php echo ($Row->pre_end_date !="1970-01-01" ? date('d-m-Y',  strtotime($Row->pre_end_date)) : ''); ?>" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" id="end_time" placeholder="h:mm" name="end_time" class="form-control  "
                                                                                       value="<?php echo $Row->pre_end_time; ?>" >

                                                                            </div>                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="post_end_date" name="post_end_date" class="form-control  input-sm " size="18" type="text" 
                                                                                       value="<?php echo ($Row->post_end_date !="1970-01-01" ? date('d-m-Y',  strtotime($Row->post_end_date)) : ''); ?>" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="post_end_time" name="post_end_time" 
                                                                                       class="form-control " value="<?php echo $Row->post_end_time; ?>" >                                                                            
                                                                            </div>                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <div class="row margin-top-10">
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label >Feedback Trigger after Nos. of Questions:<span class="required"> * </span></label>
                                                                        <input type="number" name="prefeedback_trigger" value="<?php echo ($Row->fset_pre_trigger >0 ? $Row->fset_pre_trigger :''); ?>" id="prefeedback_trigger" min="1" class="form-control input-sm PreFeedbackSession">   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">    
                                                                    <div class="form-group" style="padding-top: 25px; float: right;">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="Pretime_status"> In-Active Pre Session
                                                                            <input class="PreSessionTime" id="Pretime_status" name="Pretime_status" type="checkbox" value="1" <?php echo ($Row->pre_time_status==1?'checked':'') ?> /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label >Feedback Trigger after Nos. of Questions:<span class="required"> * </span></label>
                                                                        <input type="number" name="postfeedback_trigger" id="postfeedback_trigger" min="1" 
                                                                               class="form-control input-sm PostFeedbackSession" value="<?php echo ($Row->fset_post_trigger >0 ? $Row->fset_post_trigger :''); ?>" >   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group" style="padding-top: 25px; float: right;">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="Posttime_status"> In-Active Post Session
                                                                            <input class="PostSessionTime" id="Posttime_status" name="Posttime_status" type="checkbox" value="1" <?php echo ($Row->post_time_status==1?'checked':'') ?> /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                            </div>                                                        
                                                                                                                
                                                        <div class="row">                                                     
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Information Form</label>
                                                                    <select id="feedback_form" name="feedback_form" class="form-control input-sm " placeholder="Please select" style="width:100%">
                                                                        <?php if(count($FeedbackForm)>0){ 
                                                                        foreach ($FeedbackForm as $key => $value) { ?>
                                                                            <option value="<?php echo $value->id ?>" <?php echo ($Row->feedbackform_id==$value->id ? 'selected':'') ?>><?php echo $value->form_name ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Question Order</label>
                                                                    <select id="questions_order" name="questions_order" class="form-control input-sm " placeholder="Please select">
                                                                        <option value="0"  <?php echo ($Row->questions_order==0 ? 'selected':'') ?>>Question Set</option>
                                                                        <option value="1" <?php echo ($Row->questions_order==1 ? 'selected':'') ?>>Random</option>
                                                                        <option value="2" <?php echo ($Row->questions_order==2 ? 'selected':'') ?>>Sequence</option>
                                                                    </select>
                                                        </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Feedback Questions Order</label>
                                                                    <select id="feedback_qus_order" name="feedback_qus_order" class="form-control input-sm " placeholder="Please select">
                                                                        <option value="0" <?php echo ($Row->feedback_qus_order==0 ? 'selected':'') ?>>Feedback Set</option>
                                                                        <option value="1" <?php echo ($Row->feedback_qus_order==1 ? 'selected':'') ?>>Random</option>
                                                                        <option value="2" <?php echo ($Row->feedback_qus_order==2 ? 'selected':'') ?>>Sequence</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </fieldset>
                                                            <fieldset disabled="">
                                                    <legend>Point Multiplier Setting:</legend> 
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Point Multiplier<span class="required"> * </span></label>
                                                                    <input type="number" name="point_multiplier" id="point_multiplier" maxlength="255" 
                                                                    class="form-control input-sm" value="<?php echo $Row->point_multiplier; ?>">  
                                                                    <span class="text-muted">On each correct answer</span>
                                                                </div>
                                                            </div>                                                                   
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Payback Option</label>
                                                                    <select id="payback_option" name="payback_option" class="form-control input-sm " placeholder="Please select" onchange="ShowRedeem();" >
                                                                        <option value="0" <?php echo($Row->payback_option==0 ? 'Selected':''); ?>>None</option>
                                                                        <option value="1" <?php echo($Row->payback_option==1 ? 'Selected':''); ?>>Redeem</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4" <?php echo ($Row->payback_option==0 ? 'style="display:none"' : ''); ?>>    
                                                                <div class="form-group">
                                                                    <label>Reward</label>
                                                                    <select id="reward_id" name="reward_id[]" class="form-control input-sm " placeholder="Please select" multiple="" disabled="">
                                                                        <?php if(count($SelectReward)>0){ 
                                                                        foreach ($SelectReward as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>" <?php echo ($value->wr_id==$value->id ? 'selected':'') ?>><?php echo $value->text ?></option>
                                                                        <?php   }
                                                                        } ?>                                                                        
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="mt-checkbox mt-checkbox-outline" for="hide_on_website"> Hide workshop on the website
                                                                        <input id="hide_on_website" name="hide_on_website" type="checkbox" value="1" <?php echo($Row->hide_on_website ? 'Checked':''); ?> /><span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
<!--                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="mt-checkbox mt-checkbox-outline" for="play_only_once"> Play only once
                                                                        <input id="play_only_once" name="play_only_once" type="checkbox" value="1" < ?php echo($Row->play_only_once ? 'Checked':''); ?> /><span></span>
                                                                    </label>
                                                                    <a data-title="After all question palyed by trainee, it will stop." style="margin-left:5px;">
                                                                        <i class="icon-info font-black sub-title"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>-->
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="mt-checkbox mt-checkbox-outline" for="play_all_feedback"> Play all feedback
                                                                        <input id="play_all_feedback" name="play_all_feedback" type="checkbox" value="1" <?php echo($Row->play_all_feedback ? 'Checked':''); ?> /><span></span>
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
                                                                        <input id="end_time_display" name="end_time_display" type="checkbox" value="1" <?php echo($Row->end_time_display ? 'Checked':''); ?> /><span></span>
                                                                    </label>
                                                                    <a data-title="In atom app, Workshop end time will be visible." style="margin-left:5px;">
                                                                        <i class="icon-info font-black sub-title"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                     </fieldset>
                                                            <fieldset disabled="">
                                                    <legend>Other Information:</legend> 
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Heading</label>
                                                                    <input type="text" name="heading" id="heading" value="<?php echo $Row->heading ?>" maxlength="255" class="form-control input-sm">
                                                                    <span class="text-muted">(This heading will be displayed on workshop completed page.)</span>   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Message</label>
                                                                    <input type="text" name="message" id="message" value="<?php echo $Row->message ?>" maxlength="255" class="form-control input-sm">
                                                                    <span class="text-muted">(This message will be displayed on workshop completed page, below heading.)</span>      
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Short Description</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="150" name="short_description" placeholder=""><?php echo $Row->short_description ?></textarea>
                                                                    <span class="text-muted">(Max 150 Characters)</span>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-11">    
                                                                <div class="form-group">
                                                                    <label>Long Description</label>
                                                                    <textarea cols="80" id="long_description" name="long_description" rows="10" class="form-control input-sm cke-editor" disabled=""><?php echo $Row->long_description ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Note</label>
                                                                    <textarea rows="4" class="form-control input-sm" name="remarks" id="remarks" placeholder=""><?php echo $Row->remarks ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm " placeholder="Please select" >
                                                                        <option value="1" <?php echo($Row->status ? 'Selected':''); ?>>Active</option>
                                                                        <option value="0" <?php echo(!$Row->status ? 'Selected':''); ?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Workshop Image</label>
                                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                                <img src="<?php echo $asset_url.'assets/uploads/no_image.png'?>" alt=""/>
                                                                            </div>
                                                                            
                                                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                                <img src="<?php echo $asset_url.'assets/uploads/'.($Row->workshop_image!='' ?'workshop/'.$Row->workshop_image : 'no_image.png'); ?>" alt=""/>
                                                                            </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                     <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <a href="<?php echo site_url("workshop");?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>        
                                                    </form>
                                                    </div>
                                                    <div class="tab-pane  <?php echo ($step == 2 ? 'active"' : ''); ?>" id="tab_users">
                                                        <form role="form">
                                                                <div class="form-body">
                                                                    <div class="row ">
                                                                        <div class="col-md-12" id="workshop_panel" >
                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="UsersTable">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>ID</th>
                                                                                        <th>Name</th>
                                                                                        <th>Email</th>
                                                                                        <th>Mobile No</th>
																						<th>Trainee Region</th>
                                                                                        <th>Area</th>
																						<th></th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody></tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                        </div>    
                                                    </form> 
                                                    </div>
                                                    <div class="tab-pane" id="tab_testers">
                                                       <form role="form">
                                                          <div class="form-body">
                                                                    <div class="row ">
                                                                        <div class="col-md-12" id="workshop_panel" >
                                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="TesterviewTable">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>ID</th>
                                                                                        <th>Name</th>
                                                                                        <th>Email</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody></tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                            </div>    
                                                       </form> 
                                                    </div>
                                                    <div class="tab-pane " id="tab_avatar">
                                                    <div class="row">
                                                        <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12">
                                                        </div>
                                                    </div>
                                                    <form id="WorkshopBannerForm" name="WorkshopBannerForm" method="POST"   >    
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
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                             <?php
                                                                if (count($BannerImageSet) > 0) {
                                                                    foreach ($BannerImageSet as $key => $value) { ?>
                                                                    <tr id="Img<?php echo $value->id; ?>">
                                                                        <td>
                                                                                <?php
                                                                                $Image_path = $asset_url.'assets/uploads/workshop/banners/';
                                                                                if (!empty($value->thumbnail_image)) {
                                                                                    ?>
                                                                                <a href="<?php echo $Image_path.$value->thumbnail_image?>" class="fancybox-button" data-rel="fancybox-button">
                                                                                        <img class="img-responsive" src="<?php echo $Image_path.$value->thumbnail_image?>" alt="">
                                                                                </a>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="url[<?php echo $value->id; ?>]" value="<?php echo $value->url; ?>" disabled="">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="sort[<?php echo $value->id; ?>]" value="<?php echo $value->sorting; ?>" disabled="">
                                                                            </td>
                                                                        </tr>
                                                                 <?php }
                                                                } ?>
                                                        </tbody>
                                                    </table>
                                                    <div class="row">      
                                                        <div class="col-md-12 text-right">  
                                                            <a href="<?php echo site_url("workshop");?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
                                                    </div>
                                                    </form>
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
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
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
    <?php //$this->load->view('inc/inc_quick_nav'); ?>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
    <script src="<?php echo $asset_url;?>assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/scripts/jquery.timepicker.min.js"></script>
    
    <script>
    var NewUsersArrray=[];
    var oTable = null;
    var base_url="<?php echo $base_url;  ?>";
    var EncodeEdit_id ="<?php echo base64_encode($Row->id);  ?>";
    var AddEdit='V';
     var company_id = '<?php echo $Company_id; ?>';
    var PreLock = 1;
    var PostLock = 1;
    var token_key= '';
     <?php if($Row->pre_start_time !="") { ?>
        $('#end_time').timepicker({
            defaultTime: '<?php echo $Row->pre_end_time; ?>',                                        
        });
        $('#start_time').timepicker({                  
            defaultTime: '<?php echo $Row->pre_start_time; ?>',                    
        });
        <?php } ?>
        <?php if($Row->post_start_time !="") { ?>
            $('#post_start_time').timepicker({                  
                defaultTime: '<?php echo $Row->post_start_time; ?>'                   
            });
            $('#post_end_time').timepicker({                  
                defaultTime: '<?php echo $Row->post_end_time; ?>'                  
            });
        <?php } ?></script>
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/customjs/workshop_validation.js"></script>
    <script>
    jQuery(document).ready(function() {
            if (jQuery().datepicker) {
                $('.date-picker2').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    todayHighlight: true
                });

                $('.date-picker').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    todayHighlight: true
                });
            }
                DatatableUsersRefresh();
                DatatableTesterview();
                handleImages();
                CKEDITOR.replace('long_description');                
            selectPreFeedbackSet('pre');
            selectPreFeedbackSet('post');
            selectPreQuestionSet('pre');
            selectPreQuestionSet('post');
            });
        </script>
    </body>
</html>