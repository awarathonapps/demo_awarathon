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
        <style>
        .checked {
          color: orange;
        }
        #question_table,#rating_table {
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
            background-color: #FFF;
        }
        .map-title{
          font-size:16px;
          font-weight:600;
        }
		.ps-container{
			display: inline-flex;
			width: 100%;
		}
		.ps-seperator{
			color: black;
			float: left;
			width: 1%;
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.parameter-badge{
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #aee5ea;
			padding: 1px 10px 1px 10px;
			background: #dbfcff;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.sub-parameter-badge{
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #ffc9bc;
			padding: 1px 10px 1px 10px;
			background: #ffefeb;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
        .parameter-weight-badge{
			color: black;
			float: left;
			width: 20%;
			border: 1px solid #fff7bc;
			padding: 1px 10px 1px 10px;
			background: #feffef;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
		.keysent-badge{
			color: black;
			float: left;
			width: 48%;
			border: 1px solid #c5c5c5;
			padding: 1px 10px 1px 10px;
			background: #f9f9f9;
			/* border-radius: 25px !important; */
			margin: 0.90px 0.25px 0.25px 0.25px;
		}
        </style>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />                
        <link href="<?php echo $asset_url; ?>assets/global/css/star-rating.css" rel="stylesheet" type="text/css" /> 
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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
                                    <i class="fa fa-circle"></i>
                                    <span>Assessment</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Video Q/A</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>create_trinity" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit Assessment
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
                                                    <a href="javascrip:void(0);" >Mapping Managers</a>
                                                </li>
                                                <li>
                                                    <a href="javascrip:void(0);" >Allowed Users</a>
                                                </li>
                                                <li>
                                                    <a href="javascrip:void(0);" >User-Manager Mapping</a>
                                                </li>                                                                                                
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">        
                                                    <form id="AssessmentForm" name="AssessmentForm" method="POST" action="" enctype="multipart/form-data">                                             
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
                                                                <?php
                                                            } ?>                                                            
                                                            <div id="errordiv" class="alert alert-danger display-hide">
                                                                <button class="close" data-close="alert"></button>
                                                                You have some form errors. Please check below.
                                                                <br><span id="errorlog"></span>
                                                            </div>                                                           
                                                            <fieldset>
                                                            <legend>General Information:</legend>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Assessment Type<span class="required"> * </span></label>
                                                                        <select id="assessment_type" name="assessment_type" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                            <option value="1" <?php echo ($result->assessment_type == 1) ? 'selected' : ''; ?>>Roleplay</option>
                                                                            <option value="2" <?php echo ($result->assessment_type == 2) ? 'selected' : ''; ?>>Spotlight</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php if ($Company_id == "") { ?>
                                                            <div class="row">
                                                                <div class="col-md-4">       
                                                                    <div class="form-group">
                                                                        <label class="">Company Name<span class="required"> * </span></label>
                                                                        <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                            <option value="">Please Select</option>
                                                                             <?php foreach ($cmp_result as $cmp) { ?>
                                                                                <option value="<?= $cmp->id; ?>"  <?php echo ($result->company_id==$cmp->id ? 'Selected': ''); ?>><?= $cmp->company_name; ?> </option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>    
                                                            <?php } ?>
                                                            <div class="row">
								                                <div class="col-md-2">    
                                                                    <div class="form-group">
                                                                        <label>One Time Code (OTC)</label>
                                                                        <input type="text" name="otc" value="<?php echo $result->code; ?>" id="otc" maxlength="6" class="form-control input-sm uppercase">   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">    
                                                                    <div class="form-group">
                                                                        <label>Assessment Name<span class="required"> * </span></label>
                                                                        <input type="text" name="assessment_name" id="assessment_name" maxlength="72" class="form-control input-sm" value="<?php echo $result->assessment; ?>">   
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Assessment Type<span class="required"> * </span></label>
                                                                        <select id="assessment_type" name="assessment_type" class="form-control input-sm select2" placeholder="Please select" onchange="AssessmentChange()" >
                                                                            <option value="">Please Select</option>
                                                                            < ?php foreach ($assessment_type as $at) { ?>
                                                                                <option value="< ?= $at->id; ?>" < ?php echo($result->assessment_type == $at->id ? 'selected' : '') ?>>< ?php echo $at->description; ?></option>
                                                                            < ?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div> -->
                                                                <!-- Default assessment type value set as 2 -->
                                                                <input type="hidden" name="assessment_type" id="assessment_type" class="form-control input-sm" value="<?php echo($result->assessment_type !='' ? $result->assessment_type : '2') ?>">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Report Type<span class="required"> * </span></label>
                                                                        <select id="report_type" name="report_type" class="form-control input-sm select2" placeholder="Please select">
                                                                            <option value="">Please Select</option>
                                                                            <?php foreach ($report_type as $rt) {?>
                                                                                <option value="<?= $rt->id; ?>" <?php echo($result->report_type == $rt->id ? 'selected' : '') ?>><?php echo $rt->description; ?></option>

                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                            <label>Question type<span class="required"> * </span></label>
                                                                            <select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select" onchange="getquestion_type();" >
                                                                                    <option value="0" < ?php echo ($result->is_situation==0)?'selected':'';?>>Question</option>
                                                                                    <option value="1" < ?php echo ($result->is_situation==1)?'selected':'';?>>Situation</option>
                                                                            </select>
                                                                    </div>
                                                                </div> -->
                                                                                                                               
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                    <label class="control-label">Start Date<span class="required"> * </span></label>                                                                    
                                                                        <div class="input-group date form_datetime">
                                                                            <input type="text" size="16" class="form-control" name="start_date" id="start_date" autocomplete="off" value="<?php echo date("d-m-Y H:i", strtotime($result->start_dttm ))?>" >
                                                                            <span class="input-group-btn">
                                                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                        </div>                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                    <label class="control-label">End Date<span class="required"> * </span></label>                                                                    
                                                                        <div class="input-group date form_datetime">
                                                                            <input type="text" size="16" class="form-control" name="end_date" id="end_date" autocomplete="off" value="<?php echo date("d-m-Y H:i", strtotime($result->end_dttm ))?>">
                                                                            <span class="input-group-btn">
                                                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                        </div>                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                    <label class="control-label">Last Assessor Date<span class="required"> * </span></label>                                                                    
                                                                        <div class="input-group date form_datetime">
                                                                            <input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off" value="<?php echo ($result->assessor_dttm !='0000-00-00 00:00:00' ? date("d-m-Y H:i", strtotime($result->assessor_dttm )) : '')?>">
                                                                            <span class="input-group-btn">
                                                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                        </div>                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Number of attempts<span class="required"> * </span></label>
                                                                        <input type="number" name="number_attempts" id="number_attempts" min="1" class="form-control input-sm" value="<?php echo $result->number_attempts ?>">   
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 123px;">    
                                                                        <div class="form-group">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="is_preview"> Is preview?
                                                                                <input id="is_preview" name="is_preview" type="checkbox" value="1"  < ?php echo ($result->is_preview==1) ? 'checked' : '';?>><span></span>
                                                                            </label>
                                                                        </div>
                                                                </div> -->
                                                            </div>
                                                            <div class="row">
                                                                <?php if ($result->assessment_type == 2) { ?>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Question Limits</label>
                                                                            <input type="number" name="question_limit" id="question_limit" min="1" class="form-control input-sm" value="<?php echo $result->question_limits; ?>">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <!-- <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Rating Type<span class="required"> * </span></label>
                                                                        <select id="ratingstyle" name="ratingstyle" class="form-control input-sm select2" placeholder="Please select" >
                                                                            <option value="1" < ?php echo ($result->ratingstyle==1)?'selected':'';?>>Star Rating</option>
                                                                            <option value="2" < ?php echo ($result->ratingstyle==2)?'selected':'';?>>Slider</option>
                                                                        </select>
                                                                    </div>
                                                                </div> -->
                                                                <input type="hidden" name="ratingstyle" id="ratingstyle" class="form-control input-sm" value="<?php echo ($result->ratingstyle !='' ? $result->ratingstyle : '2') ?>">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Status<span class="required"> * </span></label>
                                                                        <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                            <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                            <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                        </select>                                                                
                                                                    </div>
                                                                </div>
                                                                    
                                                            </div>
                                                            <!-- <div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 123px;">    
                                                                        <div class="form-group">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="ranking"> Ranking
                                                                                <input id="ranking" name="ranking" type="checkbox" value="1"  <?php echo ($result->ranking==1) ? 'checked' : '';?>><span></span>
                                                                            </label>
                                                                        </div>
                                                                </div>
                                                            </div> -->
                                                            
                                                            <div class="row">
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Description</label>
                                                                        <textarea type="text" name="description" id="description" cols="3" rows="3" class="form-control input-sm"><?php echo $result->description ?></textarea>   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Instruction<span class="required"> * </span></label>
                                                                        <textarea type="text" name="instruction" id="instruction" cols="3" rows="2" class="form-control input-sm" ><?php echo $result->instruction ?></textarea>   
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend>Mapping Questions</legend>
<!--                                                            < ?php if(!$disabledflag) { ?>-->
<!--                                                            < ?php } ?>-->
                                                            <div class="row" style="overflow-x: scroll;overflow-y: hidden;">  
                                                               <div class="col-md-12" style="width:100%;">
                                                                   <table class="table table-bordered table-hover" id="VQADatatable" name="VQADatatable" style="width:100%;min-width:1200px;">
                                                                       <thead>
                                                                           <tr >
                                                                                <?php if ($result->assessment_type == 2) { ?>
                                                                                    <th width="70px" id="label_dyamic">Default</th>
                                                                                <?php } ?>
                                                                                <th width="250px" id="label_dyamic" ><?php echo ($result->is_situation==0? 'Questions':'Situation');?></th>
                                                                                <!-- <th width="150px">AI Methods</th> -->
																				<th width="100px">Language</th>
																				<th width="400px">Parameter/ Sub Parameters/ Weights
																				<span style="float:right;padding-right:15px;">Sentence / Keyword</span> </th>
                                                                                <th width="300px"><a class="btn btn-primary btn-xs btn-mini " id="btnaddpanel3"  href="<?php echo base_url() . 'create_trinity/add_questions/'.base64_encode($result->id); ?>" 
                                                                    			data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;</a></th>                                                                               
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                            $key=0;
                                                                            if(count((array)$assessment_trans) > 0) { 
                                                                                foreach ($assessment_trans as $ky=>$tr_id) {
                                                                                    $key++;
                                                                                    // $lockFlag=(in_array($tr_id->question_id, $question_play_array) ? true:false );
                                                                                    if ($result->assessment_type == 2) {
                                                                                        if ($tr_id->is_default == 1) {
                                                                                            $checked = "checked";
                                                                                        } else {
                                                                                            $checked = "";
                                                                                        }
                                                                                    }
                                                                                    
                                                                                ?>
																				<tr id="Row-<?php echo $key; ?>">
                                                                                    <?php if ($result->assessment_type == 2) { ?>
                                                                                        <td>
                                                                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                                <input type="checkbox" class="checkboxes is_default" <?php echo $checked; ?> id="is_default<?php echo $tr_id->question_id; ?>" name="is_default[<?php echo $tr_id->question_id; ?>]" value="1" />
                                                                                                <span></span>
                                                                                            </label>
                                                                                        </td>
                                                                                    <?php } ?>
																					<td> <span id="question_text_<?php echo $key; ?>"><?php echo $tr_id->question; ?></span>
																						<!-- < ?php if(!$lockFlag){ ?> -->
																						<input type="hidden" value="<?php echo $tr_id->question_id;  ?>" id="question_id<?php echo $key; ?>" name="Old_question_id[<?php echo $tr_id->id ?>]">
																						<!-- < ?php } ?> -->
																					</td>
																					<!-- <td>
																						<input type="hidden" id="txt_trno<?php //echo $key; ?>" name="txt_trno_<?php //echo $key; ?>" class="txt_trno" value="<?php //echo $key; ?>" >
																						<select id="aimethods_id<?php //echo $key; ?>" name="aimethods_id<?php //echo $key; ?>[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" multiple <?php //echo(in_array($tr_id->question_id, $question_play_array) ? 'disabled':'')?>>    
																						<?php //if (count((array)$aimeth_result) > 0) { 
																							// foreach ($aimeth_result as $aim_data) { 
																							// 	if (isset($unique_aimethods[$key-1])){
																							// 		$aimethods_array =  explode(',', $unique_aimethods[$key-1]->ai_methods);
																							// 	}else{
																							// 		$aimethods_array = [];
																							// 	}
																							
																						?>
																							<option value="<?php //echo $aim_data->id; ?>" <?php //echo (in_array($aim_data->id,$aimethods_array)? 'selected' : '') ?>><?php //echo $aim_data->description; ?></option>
																						<?php
																							//}
																						//} ?>
																						</select> 
																					</td> -->
																					<td>
																						<select id="language_id<?php echo $key; ?>" name="language_id<?php echo $key; ?>" class="form-control input-sm select2 language_id" placeholder="Please select" style="width:100%" <?php echo(in_array($tr_id->question_id, $question_play_array) ? 'disabled':'')?>>    
																						<?php if (count((array)$language_result) > 0) { 
																							foreach ($language_result as $language_data) { 
																						?>
																							<option value="<?php echo $language_data->id; ?>" <?php echo ((isset($unique_aimethods[$key-1]) && $language_data->id == $unique_aimethods[$key-1]->language_id)? 'selected' : '') ?>><?php echo $language_data->name; ?></option>
																						<?php
																							}
																						} ?>
																						</select> 
																					</td>
																					<td>
																						<div id="paramsub<?php echo $key; ?>"></div>
																						<select id="parameter_id<?php echo $key; ?>" name="Old_parameter_id<?php echo $tr_id->id; ?>[]" multiple="" style="display:none;" onchange="getUnique_paramters()">    
																							<?php if (count((array)$Parameter) > 0) { 
																								foreach ($Parameter as $p) { ?>
																									<option value="<?php echo $p->id; ?>" <?php echo (in_array($p->id,$parameter_array[$tr_id->question_id])? 'selected' : '') ?>><?php echo $p->description; ?></option>
																							<?php
																								}
																							} ?>
																						</select> 
																					</td>
																					
																					<td>
																						<a class="btn btn-success btn-sm" href="<?php echo base_url().'create_trinity/add_parameters/'.$key.'/'.$result->assessment_type.'/'.$result->company_id;?>" 
																						accesskey="" <?php echo 'data-target="#LoadModalFilter" data-toggle="modal"'?>>Manage Parameters </a>
																						
																						<a class="btn btn-success btn-sm" id="btnaddpanel3"  href="<?php echo base_url() . 'create_trinity/edit_questions/'.$key; ?>" 
																						<?php echo 'data-target="#LoadModalFilter" data-toggle="modal"'?>><i class="fa fa-pencil"></i> </a>                                                                    
																						<button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)"><i class="fa fa-times"></i></button>
																					</td>
																					<input type="hidden" value="<?php echo $tr_id->id ?>"  name="rowid[]">
																				</tr>    
                                                                            <?php } } ?>
                                                                        </tbody>
                                                                   </table>
                                                               </div>
                                                           </div>
                                                            <!-- <div class="row">
																<div class="col-md-6" >    
                                                                        <div class="form-group">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="isweights"> Is Unequal Weights?
                                                                                <input id="isweights" name="isweights" type="checkbox" value="1" <?php echo ($result->is_weights==1) ? 'checked' : '';?>><span></span>
                                                                            </label>
                                                                        </div>
                                                                </div>
															</div>
                                                           <div class="row" id="weightWindow" < ?php echo ($result->is_weights==1) ? '' : 'style ="display:none;"';?> >	
                                                               <div class="col-md-6">
                                                                    <table class="table table-bordered table-hover" id="weights_table" name="weights_table" width="100%">
                                                                       <thead>
                                                                           <tr>
                                                                                <th width="45%" id="label_dyamic">Parameter Name</th>
                                                                                <th width="25%">Weights (%)</th>
                                                                           </tr>
                                                                       </thead>
                                                                       <tbody>
                                                                        <?php  if(count((array)$parametr_weights) > 0) { 
                                                                                foreach ($parametr_weights as $k => $paradata) {   
                                                                                ?>
                                                                            <tr id="prow-<?php echo $paradata->parameter_id; ?>">
                                                                                <td> <span id="parameter_text_<?php echo $paradata->parameter_id; ?>"><?php echo $paradata->parameter_name; ?></span>
                                                                                    <input type="hidden" value="<?php echo $paradata->id;  ?>" id="parameterid<?php echo $paradata->parameter_id; ?>" name="parameter_id[<?php echo $paradata->parameter_id ?>]">                                                                                  
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" value="<?php echo ($result->is_weights==1 ? $paradata->percentage : '');  ?>" id="weight<?php echo $paradata->parameter_id; ?>" class="form-control input-sm percent_cnt" name="weight[<?php echo $paradata->parameter_id ?>]" <?php echo ($completedflag ? 'readonly':''); ?> onchange="get_weight()">       
                                                                                    
                                                                                </td>
                                                                            </tr>    
                                                                            <?php } ?>
                                                                            <tr  style="font-weight:bold;"><td>Total</td><td><input type="number" id="total_weight" name="total_weight" class="form-control input-sm " disabled value=""></td></tr>
                                                                           <?php  } ?> 
                                                                       </tbody>
                                                                   </table>
															   </div>
                                                           </div> -->
                                                        </fieldset>                                                        
                                                        
                                                            <div class="row">      
                                                                <div class="col-md-12 text-right">                                                                    
                                                                    <button type="button" id="feedback-submit" name="questionset-submit" data-loading-text="Please wait..." 
                                                                            class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left"
                                                                            onclick="ConfirmSave('C');">
                                                                        <span class="ladda-label">Save & Next</span>
                                                                    </button>                                                                    
                                                                    <a href="<?php echo site_url("create_trinity"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
                    </div>
                </div>        
            </div>
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
        <div class="modal fade" id="LoadModalFilter1" role="basic" aria-hidden="true" data-width="400">
            <div class="modal-dialog modal-lg" style="width:524px;">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body1">
                        <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                        <span>
                            &nbsp;&nbsp;Loading... </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="Mymodalid" role="basic" aria-hidden="true" data-width="400">
            <div class="modal-dialog modal-lg" style="width:524px;">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body1">
                        <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                        <span>
                            &nbsp;&nbsp;Loading... </span>
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
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
        
        <script>
            var AddEdit = "C";    
            var NewUsersArrray=[];
            var NewManagersArrray=[];
            var NewQuestionArray=[];
            var Selected_QuestionArray=[];
            var company_id = '<?php echo $Company_id; ?>';
            var AssessmentForm = $('#AssessmentForm');
            var form_error = $('.alert-danger', AssessmentForm);
            var form_success = $('.alert-success', AssessmentForm);
            var TrainerArrray = [];
            var Totalqstn  = <?php echo $key + 1; ?>;            
            var Base_url   = "<?php echo base_url(); ?>";
            var Encode_id  = "<?php echo base64_encode($result->id); ?>";   
            var ParticipantForm = document.ParticipantForm;
            var MappingForm = document.MappingForm;
        </script>
        <script src="<?php echo $asset_url; ?>assets/customjs/create_trinity.js" type="text/javascript"></script>
        <script>
			var Unique_paramters = [];
			var TempSubParameterArray = [];
			<?php 
				if (count((array)$parameter_subparameter) > 0) { 
					foreach ($parameter_subparameter as $pstran) {
						?>
							var push_value                     = {};
							push_value['txn_id']               = "<?php echo $pstran->txn_id;?>"; 
							push_value['parameter_id']         = "<?php echo $pstran->parameter_id;?>"; 
							push_value['parameter_name']       = "<?php echo $pstran->parameter_name;?>"; 
							push_value['parameter_label_id']   = "<?php echo $pstran->parameter_label_id;?>"; 
							push_value['parameter_label_name'] = "<?php echo $pstran->parameter_label_name;?>"; 
							push_value['subparameter_id']      = "<?php echo $pstran->sub_parameter_id;?>"; 
							push_value['subparameter_name']    = "<?php echo $pstran->sub_parameter_name;?>"; 
							push_value['type_id']              = "<?php echo $pstran->type_id>0?$pstran->type_id:'';?>";
							push_value['type_name']            = "<?php echo $pstran->type_name;?>";
							push_value['sentence_keyword']     = "<?php echo htmlspecialchars($pstran->sentence_keyword);?>";
                            push_value['parameter_weight']     = "<?php echo $pstran->parameter_weight;?>"; 
							TempSubParameterArray.push(push_value);
							div_html = printonscreen_keyword_sentence(<?php echo $pstran->txn_id;?>,<?php echo $pstran->parameter_id;?>);
							var div_element= "#paramsub" + <?php echo $pstran->txn_id;?>;
							$(div_element).empty();
							$(div_element).html('');
							$(div_element).html(div_html);
							getUnique_paramters();
						<?php
					}
				}
			?>
		</script>

        <script>
        jQuery(document).ready(function () {
            $('.language_id').on('select2:select', function (e) {
				var data = e.params.data;
				$(".txt_trno").each(function () {
					let temp_language_id = "#language_id"+$(this).val();
					$(temp_language_id).val(data.id).trigger('change');
				});
			});
            $(".form_datetime").datetimepicker({
                autoclose: true,                
                format: "dd-mm-yyyy hh:ii"
            });
			CKEDITOR.replace( 'description',
            {
                toolbar :
		[
			{ name: 'styles', items : [ 'Styles','Format' ] },
                        { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] }
		],
            });
            CKEDITOR.replace( 'instruction',
            {
                toolbar :
		[
			{ name: 'styles', items : [ 'Styles','Format' ] },
                        { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] }
		],
            });
			
            CKEDITOR.config.autoParagraph = false;
            DatatableUsersRefresh();
            DatatableManagersRefresh();            
            
            $('.chk_mg').click(function () {
               if ($(this).is(':checked')) {
                   $("input[name='Mapping_all[]']").prop('checked', true);                                                
               } else {
                   $("input[name='Mapping_all[]']").prop('checked', false);
               }

           }); 
            $('.chk_tr').click(function () {
               if ($(this).is(':checked')) {
                   $("input[name='Participant_all[]']").prop('checked', true);                                                
               } else {
                   $("input[name='Participant_all[]']").prop('checked', false);
               }

           }); 
           getUnique_paramters(); 
           get_weight();
        });

        $('input:checkbox').click(function() {
			$('input:checkbox').not(this).prop('checked', false);
		});
        
        //  $('#isweights').click(function(){
        //     if($(this).prop("checked") == true){
        //         $('#weightWindow').show();
        //     }else{
        //             $('#weightWindow').hide();
        //     }
        // });
        </script>
    </body>
</html>