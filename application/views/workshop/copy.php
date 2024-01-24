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
                                    <span>Copy Workshop</span>
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
                                            Copy Workshop
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="WorkshopForm" name="WorkshopForm" method="POST"  action="" enctype="multipart/form-data"> 
                                            <div class="tabbable-line tabbable-full-width">
                                                <ul class="nav nav-tabs" id="tabs">
                                                    <li class="active" >
                                                        <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_session" data-toggle="tab" >Session (PRE/POST) </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascrip:void(0);" >Allowed Users</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascrip:void(0);" >Banners</a>
                                                    </li>
                                                </ul>
                                                
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">    
                                                        
                                                    <div class="alert alert-danger display-hide" id="errordiv">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.<br/>
                                                         <span id="errorlog"></span>
                                                    </div> 
                                                         <fieldset>
                                                             <input type="hidden" value="<?php echo $Session_code; ?>" name="token_key" id="token_key">
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select"  style="width:100%" onchange="question_set();" >
                                                                        <?php if(count($SelectCompany)>0){ 
                                                                                foreach ($SelectCompany as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>" <?php echo ($Row->company_id==$value->id ? 'selected':'') ?>><?php echo $value->company_name ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php }?>  
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Creation Date<span class="required"> * </span></label>
                                                                    <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                        <input placeholder="DD-MM-YYYY" id="creation_date" name="creation_date" class="form-control date-picker input-sm" size="18" type="text" value="<?php echo date('d-m-Y'); ?>" 
                                                                                data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ></div>
                                                                </div> 
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Workshop OneTimeCode (OTC)</label>
                                                                    <input type="text" name="otp" id="otp" maxlength="6" class="form-control input-sm" value="<?php echo $Row->	otp; ?>" style="text-transform:uppercase;" >   
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
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Workshop Region<span class="required"> * </span></label>
                                                                    <select id="region" name="region" class="form-control input-sm select2" placeholder="Please select"  onchange="getRegionwisedata();">
                                                                        <?php if(count($Region)>0){ 
                                                                            foreach ($Region as  $rgn) { ?>
                                                                                <option value="<?php echo $rgn->id ?>" <?php echo ($Row->region==$rgn->id ? 'selected':'') ?>><?php echo $rgn->region_name ?></option>
                                                                                <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>                                                        
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Sub Region</label>
                                                                    <select id="subregion" name="subregion" class="form-control input-sm select2" placeholder="Please select"  >
                                                                        <option value="">Please Select</option>
                                                                        <?php if(count($SubRegion)>0){                                                                            
                                                                         foreach ($SubRegion as $sr) { ?>                                                                                
                                                                            <option value="<?= $sr->id; ?>" <?php echo ($Row->workshopsubregion_id==$sr->id ? 'selected':'') ?>><?php echo $sr->sub_region; ?></option>
                                                                        <?php } 
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Workshop Type<span class="required"> * </span></label>
                                                                    <select id="wktype" name="wktype" class="form-control input-sm select2" placeholder="Please select"  onchange="getWsubtypedata();">
                                                                        <?php if(count($WorkshopType)>0){ 
                                                                        foreach ($WorkshopType as $key => $wktype) { ?>
                                                                                <option value="<?php echo $wktype->id ?>" <?php echo ($Row->workshop_type==$wktype->id ? 'selected':'') ?>><?php echo $wktype->workshop_type ?></option>
                                                                                <?php   }
                                                                            } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Workshop Sub-Type</label>
                                                                    <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select" >
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
                                                                    <input type="text" name="powered_by" id="powered_by" maxlength="255" value="<?php echo $Row->powered_by; ?>" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
															<div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Default Language<span class="required"> * </span></label>
                                                                <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" >
                                                                    <?php if(isset($language_mst)){
                                                                            foreach ($language_mst as $RowSet) { ?>
                                                                                <option value="<?php echo $RowSet->id ?>" <?php echo ($Row->language_id == $RowSet->id ? 'Selected' : ''); ?> ><?php echo $RowSet->name ?></option>
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
                                                                    <input type="text" name="heading" id="heading" value="<?php echo $Row->heading ?>" maxlength="255" class="form-control input-sm" >
                                                                    <span class="text-muted">(This heading will be displayed on workshop completed page.)</span>   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Message<span class="required"> * </span></label>
                                                                    <input type="text" name="message" id="message" value="<?php echo $Row->message ?>" maxlength="255" class="form-control input-sm" >
                                                                    <span class="text-muted">(This message will be displayed on workshop completed page, below heading.)</span>      
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
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select"  >
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
                                                                            <div>
                                                                                    <span class="btn default btn-file">
                                                                                    <span class="fileinput-new">
                                                                                    Select image </span>
                                                                                    <span class="fileinput-exists">
                                                                                    Change </span>
                                                                                    <input type="file" name="workshop_image" id="workshop_image">
                                                                                    </span>
                                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput" onclick="RemoveWkImage();">
                                                                                    Remove </a>
                                                                                <input type="hidden" name="RemoveWrkImage" id="RemoveWrkImage" value="0"> 
                                                                            </div>
                                                                    </div>
                                                                    <span class="text-muted">(Extensions allowed: .png , .jpg, .jpeg)  )</span>
<!--                                                                    width:750px, height:400px-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave('C');">
                                                                    <span class="ladda-label">Save & Next</span>
                                                                </button>
                                                                <a href="<?php echo site_url("workshop");?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div> 
                                                    </fieldset>
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
                                                            <label>Questionset Type<span class="required"> * </span></label>
                                                            <select id="pre_question_type" name="pre_question_type" class="form-control input-sm select2" placeholder="Please select" 
                                                                    onchange="SelectedQuestionSet()" >
                                                                <option value="">Please Select</option>
                                                                <option value="1" <?php echo($Row->questionset_type==1 ? 'Selected':''); ?>>Question Set</option>
                                                                <option value="2" <?php echo($Row->questionset_type==2 ? 'Selected':''); ?>>Feedback Set</option>                                                                        
                                                            </select>
                                                        </div>
                                                    </div>
													<div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Default Trainer<span class="required"> </label>
                                                                        <select id="df_trainer_id" name="df_trainer_id" class="form-control input-sm select2" placeholder="Please select" >
																			 <option value="" >Please select</option>
                                                                            <?php 
																				if(count($df_trainer_list)>0){
                                                                                foreach ($df_trainer_list as $wst) { ?>
                                                                                <option value="<?= $wst->userid; ?>" <?php echo($Row->df_trainer_id==$wst->userid ? 'selected':''); ?>>
																				<?php echo $wst->trainer; ?></option>
																				<?php } } ?>
                                                                        </select>
																		<span class="text-muted">(Only Applicable for New Question set)</span>
                                                                    </div>
                                                                </div>
                                                    </div>
                                                    <div class="row prePostQuestionRow"  <?php echo ($Row->questionset_type==2 ? 'style="display:none"' :'') ?>>                                                                                                                    
                                                            <div class="col-md-6 rightborder"> 
                                                                <h4 class="form-section customhr">Pre Session :</h4>
                                                                <div class="form-group">
                                                                    <label>Pre-Question Set</label>
                                                                    <select id="pre_question_set" name="pre_question_set[]" class="groupSelectClass form-control input-sm select2" placeholder="Please select" multiple="" 
                                                                         >
                                                                         <?php if(count($pre_SelectQuestionSet)>0){ 
                                                                                foreach ($pre_SelectQuestionSet as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>" <?php echo ($Row->questionset_type==1 && $value->wc_id==$value->id ? 'selected':'') ?> 
                                                                                 ><?php echo $value->text ?></option>
                                                                        <?php   }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div>                                                        
                                                            <div class="col-md-6">
                                                                <h4 class="form-section customhr">Post Session :</h4>
                                                                <div class="form-group">
                                                                    <label>Post-Question Set</label>
                                                                    <select id="post_question_set" name="post_question_set[]" class="groupSelectClass form-control input-sm select2" placeholder="Please select" multiple="" 
                                                                         >
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
                                                                            <th align="center">Action</th>
                                                                            
                                                                        </tr></thead>
                                                                        <tbody id="PreTbody">
                                                                             <?php if(count($pre_QuestionSetStatus)>0){ 
                                                                                foreach ($pre_QuestionSetStatus as $key => $value) { ?>
                                                                            <tr id="QPre<?php echo $value->id ?>">
                                                                                <td><a href="javascript:void(0);" onclick="getTopic_subtopic('<?php echo $value->id ?>',1,1);" ><i class="fa fa-plus-circle"></i></a></td>
                                                                                <td><?php echo $value->text ?></td>
                                                                                <td><?php echo $value->timer ?></td>
                                                                                <td><?php echo $value->totalqsn ?></td>
                                                                                <td>    
                                                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline ">
                                                                                        <input type="checkbox" class="prehide_answer"  name="prehide_answer[]" value="<?php echo $value->id ?>" <?php echo (!$value->hide_answer ? 'checked':'') ?> id="prehide_answer" />
                                                                                            <span></span>
                                                                                        </label>
                                                                                </td>
                                                                                <td style="float:right">
                                                                                    <input type="checkbox" class="make-switch preqstatus_switch switch-mini" 
                                                                                    name="preqstatus_switch[]" id="preqstatus_switch" value="<?php echo $value->id ?>" <?php echo ($value->status ? 'checked':'') ?> 
                                                                                    data-size="small" data-off-color="danger" data-off-text="In-Active"
                                                                                    data-on-color="success" data-on-text="Active" >
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <?php } } ?>
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
                                                                             <?php if(count($post_QuestionSetStatus)>0){ 
                                                                                foreach ($post_QuestionSetStatus as $key => $value) {  ?>
                                                                            <tr id="QPost<?php echo $value->id ?>">
                                                                                <td><a href="javascript:void(0);" onclick="getTopic_subtopic('<?php echo $value->id ?>',2,1);" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus-circle"></i></a></td>
                                                                                <td><?php echo $value->text ?></td>
                                                                                <td><?php echo $value->timer ?></td>
                                                                                <td><?php echo $value->totalqsn ?></td>
                                                                                <td>    
                                                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline ">
                                                                                        <input type="checkbox" class="posthide_answer" name="posthide_answer[]" value="<?php echo $value->id ?>" <?php echo (!$value->hide_answer ? 'checked':'') ?> id="posthide_answer"  />
                                                                                        <span></span>
                                                                                    </label>
                                                                                </td>
                                                                                <td style="float:right"><input type="checkbox" class="make-switch postqstatus_switch" name="postqstatus_switch[]" 
                                                                                        <?php echo ($value->status ? 'checked':'') ?>                       id="postqstatus_switch" value="<?php echo $value->id ?>" data-size="small" 
                                                                                                               data-off-color="danger" data-off-text="In-Active" data-on-color="success" data-on-text="Active"
                                                                                                               ></td>
                                                                            </tr>
                                                                            <?php  } } ?>
                                                                        </tbody></table>
                                                                            </div>
                                                                        </div>
                                                        </div>
                                                        <div class="row prePostFeedBackRow" >
                                                            <div class="col-md-6 rightborder">    
                                                                <div class="form-group">
                                                                    <label>Pre Feedback Set</label>
                                                                    <select id="pre_feedback_id" name="pre_feedback_id[]" class="groupSelectClass2 form-control input-sm select2 PreSessionTime" placeholder="Please select" multiple="" 
                                                                        >
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
                                                                    <select id="post_feedback_id" name="post_feedback_id[]" class="groupSelectClass2 form-control input-sm select2 PostSessionTime" placeholder="Please select" multiple="" 
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
                                                                             <?php if(count($pre_FeedbackSetStatus)>0){ 
                                                                                foreach ($pre_FeedbackSetStatus as $key => $value) {?>
                                                                            <tr id="FPre<?php echo $value->id ?>">
                                                                                <td><a href="javascript:void(0);" onclick="get_feeback_subtopic(<?php echo $value->id ?>,1,1);"><i class="fa fa-plus-circle"></i></a></td>
                                                                                <td><?php echo $value->text ?></td>
                                                                                <td><?php echo $value->timer ?></td>
                                                                                <td><?php echo $value->totalqsn ?></td>
                                                                                <td style="float:right"><input type="checkbox" class="make-switch prefeedstatus_switch" name="prefeedstatus_switch[]" 
                                                                                    <?php echo ($value->status ? 'checked':'') ?> value="<?php echo $value->id ?>" data-size="small" data-off-color="danger" data-off-text="In-Active" data-on-color="success" data-on-text="Active"
                                                                                    ></td>
                                                                            </tr>
                                                                            <?php } } ?>
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
                                                                             <?php if(count($post_FeedbackSetStatus)>0){ 
                                                                                foreach ($post_FeedbackSetStatus as $key => $value) { ?>
                                                                            <tr id="FPost<?php echo $value->id ?>">
                                                                                <td><a href="javascript:void(0);" onclick="get_feeback_subtopic(<?php echo $value->id ?>,2,1);" ><i class="fa fa-plus-circle"></i></a></td>
                                                                                <td><?php echo $value->text ?></td>
                                                                                <td><?php echo $value->timer ?></td>
                                                                                <td><?php echo $value->totalqsn ?></td>
                                                                                <td style="float:right"><input type="checkbox"  <?php echo ($value->status ? 'checked':'') ?> class="make-switch postfeedstatus_switch" name="postfeedstatus_switch[]" value="<?php echo $value->id ?>" data-size="small" data-off-color="danger" 
                                                                                    data-off-text="In-Active" data-on-color="success" data-on-text="Active"
                                                                                    ></td>
                                                                            </tr>
                                                                            <?php } } ?>
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
                                                                                <input placeholder="DD-MM-YYYY" id="start_date" name="start_date" class="form-control date-picker2 PreSessionTime input-sm " size="18" type="text" value=""
                                                                                    data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="start_time" name="start_time" 
                                                                                       class="form-control timepicker PreSessionTime timepicker-no-seconds " value="" autocomplete="off">                                                                            
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>  
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="post_start_date" name="post_start_date" class="form-control date-picker2 PostSessionTime input-sm " size="18" type="text" 
                                                                                       value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">Start Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="post_start_time" name="post_start_time" 
                                                                                       class="form-control timepicker PostSessionTime timepicker-no-seconds " 
                                                                                       value="" autocomplete="off">
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
                                                                                <input placeholder="DD-MM-YYYY" id="end_date" name="end_date" class="form-control date-picker2 PreSessionTime input-sm " size="18" type="text" 
                                                                                        value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" id="end_time" placeholder="h:mm" name="end_time" class="form-control timepicker timepicker-no-seconds PreSessionTime "
                                                                                       value="" autocomplete="off">

                                                                            </div>                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Date:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                                <input placeholder="DD-MM-YYYY" id="post_end_date" name="post_end_date" class="form-control date-picker2 PostSessionTime input-sm " size="18" type="text" 
                                                                                       value="" 
                                                                                       data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label input-sm col-md-4">End Time:</label>
                                                                        <div class="col-md-8">
                                                                            <div class="input-icon"><i class="fa fa-clock-o"></i>
                                                                                <input type="text" placeholder="h:mm" id="post_end_time" name="post_end_time" 
                                                                                       class="form-control timepicker timepicker-no-seconds PostSessionTime" value="" autocomplete="off">                                                                            
                                                                            </div>                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <div class="row margin-top-10">
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label >Feedback Trigger after Nos. of Questions:<span class="required"> * </span></label>
                                                                        <input type="number" name="prefeedback_trigger" value="<?php echo ($Row->fset_pre_trigger >0 ? $Row->fset_pre_trigger :''); ?>" id="prefeedback_trigger" min="1" class="form-control input-sm PreFeedbackSession" 
                                                                            >
                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 rightborder">    
                                                                    <div class="form-group" style="padding-top: 25px; float: right;">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="Pretime_status"> In-Active Pre Session
                                                                            <input class="" id="Pretime_status" name="Pretime_status" type="checkbox" value="1" <?php echo ($Row->pre_time_status==1?'checked':'') ?> 
                                                                                 /><span></span>
                                                                        </label>
                                                                        <a data-title="If Session is Live and once InActive Pre session, then you can't active. It means Pre session has completed" style="margin-left:5px;">
                                                                        <i class="icon-info font-black sub-title"></i>
                                                                    </a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 ">
                                                                    <div class="form-group">
                                                                        <label >Feedback Trigger after Nos. of Questions:<span class="required"> * </span></label>
                                                                        <input type="number" name="postfeedback_trigger" id="postfeedback_trigger" min="1" 
                                                                               class="form-control input-sm PostFeedbackSession" 
                                                                               value="<?php echo ($Row->fset_post_trigger >0 ? $Row->fset_post_trigger :''); ?>"  >   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group" style="padding-top: 25px; float: right;">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="Posttime_status"> In-Active Post Session
                                                                            <input class="" id="Posttime_status" name="Posttime_status" type="checkbox" value="1" <?php echo ($Row->post_time_status==1?'checked':'') ?> 
                                                                                 /><span></span>
                                                                        </label>
                                                                        <a data-title="If Session is Live and once InActive Post session, then you can't active. It means Post session has completed" style="margin-left:5px;">
                                                                        <i class="icon-info font-black sub-title"></i>
                                                                    </a>
                                                                    </div>
                                                                </div>

                                                            </div>                                                                                                                
                                                        <div class="row">                                                     
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Information Form</label>
                                                                    <select id="feedback_form" name="feedback_form" class="form-control input-sm select2" placeholder="Please select" style="width:100%"
                                                                            >
																			<option value="">Select</option>
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
                                                                    <select id="questions_order" name="questions_order" class="form-control input-sm select2" placeholder="Please select">
                                                                        <option value="0"  <?php echo ($Row->questions_order==0 ? 'selected':'') ?>>Question Set</option>
                                                                        <option value="1" <?php echo ($Row->questions_order==1 ? 'selected':'') ?>>Random</option>
                                                                        <option value="2" <?php echo ($Row->questions_order==2 ? 'selected':'') ?>>Sequence</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Feedback Questions Order</label>
                                                                        <select id="feedback_qus_order" name="feedback_qus_order" class="form-control input-sm select2" placeholder="Please select">
                                                                            <option value="0" <?php echo ($Row->feedback_qus_order==0 ? 'selected':'') ?>>Feedback Set</option>
                                                                            <option value="1" <?php echo ($Row->feedback_qus_order==1 ? 'selected':'') ?>>Random</option>
                                                                            <option value="2" <?php echo ($Row->feedback_qus_order==2 ? 'selected':'') ?>>Sequence</option>
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
                                                                    <input type="number" name="point_multiplier" id="point_multiplier" min="1" 
                                                                    class="form-control input-sm" value="<?php echo $Row->point_multiplier; ?>" >  
                                                                    <span class="text-muted">On each correct answer</span>
                                                                </div>
                                                            </div>                                                                                                                    
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Payback Option</label>
                                                                    <select id="payback_option" name="payback_option" class="form-control input-sm select2" 
                                                                            placeholder="Please select" onchange="ShowRedeem();"  >
                                                                        <option value="0" <?php echo($Row->payback_option==0 ? 'Selected':''); ?>>None</option>
                                                                        <option value="1" <?php echo($Row->payback_option==1 ? 'Selected':''); ?>>Redeem</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4" <?php echo ($Row->payback_option==0 ? 'style="display:none"' : ''); ?> id="RewardBox" >    
                                                                <div class="form-group">
                                                                    <label>Reward<span class="required"> * </span></label>
                                                                    <select id="reward_id" name="reward_id[]" class="form-control input-sm select2" placeholder="Please select" multiple="">
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
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="mt-checkbox mt-checkbox-outline" for="play_all_feedback"> Play all feedback
                                                                        <input id="play_all_feedback" name="play_all_feedback" type="checkbox" value="1" <?php echo($Row->play_all_feedback ? 'Checked':''); ?> /><span></span>
                                                                    </label>
                                                                    <a data-title="After all question played by trainee. If any feedback is left, then it will fiered & it will stop." style="margin-left:5px;">
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
                                                            <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmSave('C');">
                                                                    <span class="ladda-label">Save & Next</span>
                                                                </button>
                                                                <a href="<?php echo site_url("workshop");?>" class="btn btn-default btn-cons">Cancel</a>
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
    var AddEdit='C';
    var company_id = '<?php echo $Company_id; ?>';
    var PreLock = 0;
    var PostLock = 0;
    var token_key= '<?php echo $Session_code; ?>';
</script>
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/customjs/workshop_validation.js"></script>
    <script>
    jQuery(document).ready(function() {
            if (jQuery().datepicker) {
                $('.date-picker2').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    startDate: '+0d'
                });
                $('.date-picker').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    endDate:'+0d'
                });
            }
            LoadPrepostValidation();
           // CKEDITOR.replace('long_description'); 
//            selectPreFeedbackSet('pre');
//            selectPreFeedbackSet('post');
//            selectPreQuestionSet('pre');
//            selectPreQuestionSet('post');
            });
        </script>
    </body>
</html>