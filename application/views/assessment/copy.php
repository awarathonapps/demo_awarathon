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
                                <a href="<?php echo $base_url ?>assessment" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                                <li disabled>
                                                    <a href="javascrip:void(0);" >Assessment</a>
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
                                                                        <input type="text" name="assessment_name" id="assessment_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->assessment; ?>">   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Assessment Type<span class="required"> * </span></label>
                                                                        <select id="assessment_type" name="assessment_type" class="form-control input-sm select2" placeholder="Please select" onchange="AssessmentChange()" >
                                                                            <option value="">Please Select</option>
                                                                            <?php foreach ($assessment_type as $at) { ?>
                                                                                <option value="<?= $at->id; ?>" <?php echo($result->assessment_type == $at->id ? 'selected' : '') ?>><?php echo $at->description; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                            <label>Question type<span class="required"> * </span></label>
                                                                            <select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select" onchange="getquestion_type();" >
                                                                                    <option value="0" <?php echo ($result->is_situation==0)?'selected':'';?>>Question</option>
                                                                                    <option value="1" <?php echo ($result->is_situation==1)?'selected':'';?>>Situation</option>
                                                                            </select>
                                                                    </div>
                                                                </div>
                                                                                                                               
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                    <label class="control-label">Start Date<span class="required"> * </span></label>                                                                    
                                                                        <div class="input-group date form_datetime">
                                                                            <input type="text" size="16" class="form-control" name="start_date" id="start_date" autocomplete="off" value="<?php echo date("d-m-Y h:i:s", strtotime($result->start_dttm ))?>" >
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
                                                                            <input type="text" size="16" class="form-control" name="end_date" id="end_date" autocomplete="off" value="<?php echo date("d-m-Y h:i:s", strtotime($result->end_dttm ))?>">
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
                                                                            <input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off" value="<?php echo ($result->assessor_dttm !='0000-00-00 00:00:00' ? date("d-m-Y h:i:s", strtotime($result->assessor_dttm )) : '')?>">
                                                                            <span class="input-group-btn">
                                                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                        </div>                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2" style="padding-left: 0px; width: 124px;">    
                                                                    <div class="form-group">
                                                                        <label>Number of attempts<span class="required"> * </span></label>
                                                                        <input type="number" name="number_attempts" id="number_attempts" min="1" class="form-control input-sm" value="<?php echo $result->number_attempts ?>">   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" style="margin-top: 25px;padding: 0px; width: 123px;">    
                                                                        <div class="form-group">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="is_preview"> Is preview?
                                                                                <input id="is_preview" name="is_preview" type="checkbox" value="1"  <?php echo ($result->is_preview==1) ? 'checked' : '';?>><span></span>
                                                                            </label>
                                                                        </div>
                                                                </div>
                                                            </div>
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
                                                            <legend>Mapping Questions/Situation:</legend>
<!--                                                            < ?php if(!$disabledflag) { ?>-->
<!--                                                            < ?php } ?>-->
                                                            <div class="row">  
                                                               <div class="col-md-12">
                                                                   <table class="table table-bordered table-hover" id="VQADatatable" name="VQADatatable" width="100%">
                                                                       <thead>
                                                                           <tr >
                                                                                <th width="35%" id="label_dyamic" ><?php echo ($result->is_situation==0? 'Questions':'Situation');?></th>
                                                                                <th width="45%">Parameter</th>
                                                                                <th width="10%"><a class="btn btn-primary btn-xs btn-mini " id="btnaddpanel3"  href="<?php echo base_url() . 'assessment/add_questions/'.base64_encode($result->id); ?>" 
                                                                    data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;</a></th>                                                                               
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                            $key=0;
                                                                            if(count($assessment_trans) > 0) { 
                                                                                foreach ($assessment_trans as $key=>$tr_id) {
                                                                                    $key++;
                                                                                    $lockFlag=(in_array($tr_id->question_id, $question_play_array) ? true:false );
                                                                                    
                                                                                ?>
                                                                            <tr id="Row-<?php echo $key; ?>">
                                                                                <td> <span id="question_text_<?php echo $key; ?>"><?php echo $tr_id->question; ?></span>
                                                                                    
                                                                                    <input type="hidden" value="<?php echo $tr_id->question_id;  ?>" id="question_id<?php echo $key; ?>" name="New_question_id[]">
                                                                                    
                                                                                </td>
                                                                               
                                                                                <td><select id="parameter_id<?php echo $key; ?>" name="New_parameter_id<?php echo $key; ?>[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" multiple="" >    
                                                                                        <?php if (count($Parameter) > 0) { 
                                                                                            foreach ($Parameter as $p) { ?>
                                                                                                <option value="<?php echo $p->id; ?>" <?php echo (in_array($p->id,$parameter_array[$tr_id->question_id])? 'selected' : '') ?>><?php echo $p->description; ?></option>
                                                                                        <?php
                                                                                            }
                                                                                        } ?>
                                                                                    </select> 
                                                                                </td>
                                                                                <input type="hidden" name="rowid[]" value="<?php echo $key; ?>"/>
                                                                                <td>
                                                                                    <a class="btn btn-success btn-sm" id="btnaddpanel3"  href="<?php echo base_url() . 'assessment/edit_questions/'.$key; ?>" 
                                                                                    data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-pencil"></i> </a>                                                                    
                                                                                    <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)" ><i class="fa fa-times"></i></button>
                                                                                </td>
                                                                            </tr>    
                                                                            <?php } } ?>
                                                                        </tbody>
                                                                   </table>
                                                               </div>
                                                           </div>
                                                        </fieldset>                                                        
                                                            <div class="row">      
                                                                <div class="col-md-12 text-right">                                                                    
                                                                    <button type="button" id="feedback-submit" name="questionset-submit" data-loading-text="Please wait..." 
                                                                            class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left"
                                                                            onclick="ConfirmSave('C');">
                                                                        <span class="ladda-label">Submit</span>
                                                                    </button>                                                                    
                                                                    <a href="<?php echo site_url("assessment"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <script src="<?php echo $asset_url; ?>assets/customjs/assessment_validation.js" type="text/javascript"></script>
        <script>
        jQuery(document).ready(function () {
            $(".form_datetime").datetimepicker({
                autoclose: true,                
                format: "dd-mm-yyyy hh:ii:ss"
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
            AssessmentUsersRefresh();
            
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
            
        });
        </script>
    </body>
</html>