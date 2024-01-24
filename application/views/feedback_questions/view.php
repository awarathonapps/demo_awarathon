<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
//Added 
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
    
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <!--Added-->
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
                                    <span>Feedback</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Question</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>feedback_questions" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            View Feedback Question
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <form id="FrmFeedbackQns" name="FrmFeedbackQns" method="POST"  action="<?php echo $base_url; ?>feedback_questions/submit" enctype="multipart/form-data" > 
                                        <fieldset disabled="">
                                            <div class="portlet-body">                                                                                            
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">    
                                                        <?php
                                                        $errors = validation_errors();
                                                        if ($errors) {
                                                            ?>
                                                            <div  class="alert alert-danger">
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
                                                        <?php if ($this->mw_session['company_id'] == "") { ?>
                                                            <div class="row">    
                                                                <div class="col-md-6">       
                                                                    <div class="form-group">
                                                                        <label class="">Company Name<span class="required"> * </span></label>
                                                                        <select id="company_id" name="company_id" class="form-control input-sm " placeholder="Please select" onchange="feedbackTypeData();" disabled="">
                                                                            <option value="">Please Select</option>
                                                                            <?php
                                                                            if (isset($CompanySet)) {
                                                                                foreach ($CompanySet as $Row) {
                                                                                    ?>
                                                                                    <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->company_id == $Row->id ? 'Selected' : ''); ?> ><?php echo $Row->company_name ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                        <?php } ?>
                                                        <div class="row">    
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Feedback Type<span class="required"> * </span></label>
                                                                    <select id="feedback_type" name="feedback_type" class="groupSelectClass form-control input-sm " placeholder="Please select" onchange="getfeedbacksupType();" disabled="">
                                                                        <option value="">Please Select</option>
                                                                        <?php
                                                                        if (isset($feedback_typeSet)) {
                                                                            foreach ($feedback_typeSet as $Row) {
                                                                                ?>
                                                                                <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->feedback_type_id == $Row->id ? 'Selected' : ''); ?>><?php echo $Row->description ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Feedback Sub-Type</label>
                                                                    <select id="feedback_subtype" name="feedback_subtype" class="groupSelectClass form-control input-sm" placeholder="Please select" >
                                                                        <?php
                                                                        if (isset($feedback_subtypeSet)) {
                                                                            foreach ($feedback_subtypeSet as $Row) {
                                                                                ?>
                                                                                <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->feedback_subtype_id == $Row->id ? 'Selected' : ''); ?>><?php echo $Row->description ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Question Type<span class="required"> * </span></label>
                                                                    <select id="question_type" name="question_type" class=" form-control input-sm " placeholder="Please select" onchange="valid_multiple_opt();" >
                                                                        <option value="0" <?php echo ($RowSet->question_type == 0 ? 'Selected' : ''); ?>>Multiple choice</option>
                                                                        <option value="1" <?php echo ($RowSet->question_type == 1 ? 'Selected' : ''); ?>>Text</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm" placeholder="Please select" >
                                                                        <option value="1" <?php echo ($RowSet->status == 1 ? 'Selected' : ''); ?> >Active</option>
                                                                        <option value="0" <?php echo ($RowSet->status == 0 ? 'Selected' : ''); ?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                   
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Question<span class="required"> * </span></label>
                                                                    <textarea rows="2" class="form-control input-sm" id="question_title" maxlength="150" name="question_title" placeholder="Question ?"><?php echo $RowSet->question_title; ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Language<span class="required"> * </span></label>
                                                                <select id="language_id" name="language_id" class="form-control input-sm " placeholder="Please select"  disabled="">
                                                                    <?php if(isset($language_mst)){
                                                                            foreach ($language_mst as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->language_id == $Row->id ? 'Selected' : ''); ?> ><?php echo $Row->name ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="row text_opt">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Min Length<span class="required"> * </span></label>
                                                                    <input type="number" name="min_length" id="min_length" placeholder="Min Length" min="0"  class="form-control input-sm" value="<?php echo $RowSet->min_length; ?>">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Max Length<span class="required"> * </span></label>
                                                                    <input type="number" name="max_length" id="max_length" placeholder="Max Length" min="0"  class="form-control input-sm" value="<?php echo $RowSet->max_length; ?>">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Weightage</label>
                                                                    <input type="Number" name="text_weightage" id="text_weightage" min="0"  placeholder="Weightage" class="form-control input-sm" value="<?php echo ($RowSet->text_weightage == "0" ? '' : $RowSet->text_weightage); ?>" >   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Timer (In Sec.)</label>
                                                                    <input type="number" name="question_timer" id="question_timer" maxlength="255" class="form-control input-sm" value="<?php echo $RowSet->question_timer; ?>">   
                                                                </div>
                                                                <span class="text-muted">(This sets the maximum time in seconds,Zero means no time)</span>
                                                            </div>
                                                        </div>
                                                        <div class="row multiple_opt">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Option A<span class="required"> * </span></label>
                                                                    <input type="text" name="option_a" id="option_a" maxlength="255" placeholder="Option A" value="<?php echo $RowSet->option_a; ?>" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <input type="Number" name="weight_a" id="weight_a" value="<?php echo $RowSet->weight_a; ?>" maxlength="255" placeholder="Weightage" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Option B</label>
                                                                    <input type="text" name="option_b" id="option_b" placeholder="Option B" value="<?php echo $RowSet->option_b; ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <input type="Number" name="weight_b" id="weight_b" min="0" maxlength="255" value="<?php echo $RowSet->weight_b; ?>" placeholder="Weightage" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row multiple_opt">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Option C</label>
                                                                    <input type="text" name="option_c" id="option_c" placeholder="Option C" value="<?php echo $RowSet->option_c; ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <input type="Number" name="weight_c" id="weight_c" min="0" maxlength="255" value="<?php echo $RowSet->weight_c; ?>" placeholder="Weightage" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Option D</label>
                                                                    <input type="text" name="option_d" id="option_d" value="<?php echo $RowSet->option_d; ?>" placeholder="Option D" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <input type="Number" name="weight_d" id="weight_d" value="<?php echo $RowSet->weight_d; ?>" min="0" maxlength="255" placeholder="Weightage" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row multiple_opt">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Option E</label>
                                                                    <input type="text" name="option_e" id="option_e" value="<?php echo $RowSet->option_e; ?>" placeholder="Option E" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <input type="Number" name="weight_e" id="weight_e"  value="<?php echo $RowSet->weight_e; ?>"min="0" maxlength="255" placeholder="Weightage" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Option F</label>
                                                                    <input type="text" name="option_f" id="option_f" value="<?php echo $RowSet->option_f; ?>" placeholder="Option F" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <input type="Number" name="weight_f" id="weight_f" value="<?php echo $RowSet->weight_f; ?>" maxlength="255" min="0" placeholder="Weightage" class="form-control input-sm" >   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row multiple_opt">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Multiple Selection<span class="required"> * </span></label>
                                                                    <select id="multiple_allow" name="multiple_allow" class="form-control input-sm " placeholder="Please select" disabled="">
                                                                        <option value="0" <?php echo ($RowSet->multiple_allow == 0 ? 'Selected' : ''); ?>>Only One</option>
                                                                        <option value="1" <?php echo ($RowSet->multiple_allow == 1 ? 'Selected' : ''); ?>>One or More</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Tip</label>
                                                                    <textarea rows="2" class="form-control input-sm" id="tip" maxlength="150" name="tip" placeholder="Tip" value="<?php echo $RowSet->tip; ?>"><?php echo $RowSet->tip; ?></textarea>
                                                                    <span class="text-muted">(Max 150 Characters)</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Hint Image</label>
                                                                    <div class="form-control fileinput fileinput-exists" style="    border: none;height:auto;" data-provides="fileinput">                                                                            
                                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                            <img src="<?php echo $asset_url . 'assets/uploads/' . ($RowSet->hint_image != '' ? 'feedback_questions/' . $RowSet->hint_image : 'no_image.png'); ?>" alt=""/>
                                                                             
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>    
                                                    </div>                                                           
                                                </div>                                      
                                            </div>
                                        </fieldset>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <a href="<?php echo site_url("feedback_questions"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>                   
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');     ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');    ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');   ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script>
            valid_multiple_opt();
            function valid_multiple_opt() {
                var type = $('#question_type').val();
                if (type == 0 || type == '') {
                    $('.multiple_opt').show();
                    $('.text_opt').hide();
                } else {
                    $('.multiple_opt').hide();
                    $('.text_opt').show();
                }
            }
        </script>
    </body>
</html>