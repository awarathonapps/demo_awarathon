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
                                    <span>Video Q&A/Situation</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>View Video Q/A</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>video_situation" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            View Video Q&A/Situation
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <form id="frmVideo_situation" name="frmVideo_situation" method="POST" > 
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">                                                    
                                                       
                                                    <div class="row">                                                        
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Assessment Type<span class="required"> * </span></label>
                                                                <select name="assessment_type" id="assessment_type" class="form-control input-sm select2" disabled="">
                                                                    <option value="">Please select</option>
                                                                    <?php foreach ($assessment_type as $val) { ?>
                                                                        <option value="<?php echo $val->id ?>" <?php echo($result->assessment_type == $val->id ? 'selected':'') ?>><?php echo $val->description ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question type<span class="required"> * </span></label>
                                                                <select id="question_type" name="question_type" class="form-control input-sm " placeholder="Please select" disabled="" >
                                                                    <option value="0" < ?php echo ($result->is_situation==0)?'selected':'';?>>Question</option>
                                                                    <option value="1" < ?php echo ($result->is_situation==1)?'selected':'';?>>Situation</option>
                                                                </select>
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Read Timer<span class="required"> * </span></label>
                                                                <input type="number" name="read_timer" id="read_timer" min="0" disabled="" class="form-control input-sm" value="<?php echo $result->read_timer ?>">
                                                                <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Response timer<span class="required"> * </span></label>
                                                                <input type="number" name="response_timer" id="timer" min="0" disabled=""  class="form-control input-sm" value="<?php echo $result->response_timer ?>">
                                                                <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                    <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                    <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
														<!-- <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Weightage<span class="required"> * </span></label>
                                                                <input type="number" name="weightage" id="weightage" min="0" disabled="" class="form-control input-sm" value="< ?php echo $result->weightage ?>">                                                                
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                    <div class="row">
                                                    <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question Format<span class="required"> * </span></label>
                                                                <select id="question_option" name="question_option" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                    <option value="0" <?php echo ($result->question_format==0)?'selected':'';?>>Text</option>
                                                                    <option value="1" <?php echo ($result->question_format==1)?'selected':'';?>>Video</option>
                                                                    <option value="2" <?php echo ($result->question_format==2)?'selected':'';?>>Image</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label><span id="label_dyamic"><?php echo ($result->is_situation==0)?'Question':'Situation';?></span><span class="required"> * </span></label>
                                                                <?php if($result->question_format==0){ ?>
                                                                    <textarea type="text" name="question" id="question" cols="5" rows="3" class="form-control input-sm" disabled=""><?php echo $result->question ?></textarea>   
                                                                <?php }elseif($result->question_format==2){ ?>
                                                                    <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;" data-provides="fileinput">
                                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 500px;">
                                                                            <img src="<?php echo base_url() . 'assets/uploads/' . ($result->question_format == 2 ? 'questions/' . $result->question_path : 'no_image.png'); ?>" alt=""/>
                                                                        </div>
                                                                    </div>
                                                                <?php }
                                                                elseif($result->question_format==2){ ?>
                                                                    <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;" data-provides="fileinput">
                                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 500px;">
                                                                            <img src="<?php echo base_url() . 'assets/uploads/' . ($result->question_format == 2 ? 'questions/' . $result->question_path : 'no_image.png'); ?>" alt=""/>
                                                                        </div>
                                                                    </div>
                                                                <?php }elseif($result->question_format==1){ ?>
                                                                    <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;" data-provides="fileinput">
                                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 400px;">
                                                                            <iframe src="<?php echo $result->question_path ?>" width="470" height="300" frameborder="0" allow="autoplay; fullscreen; picture-in-picture\" allowfullscreen title="data/user/0/com.example.awarathon_pwa/cache/REC8474285680719209381.mp4"></iframe>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                <?php }?>
                                                            </div>
                                                        </div>
                                                        <?php if($result->question_format==1 || $result->question_format==2){ ?>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Question Title<span class="required"> * </span></label>
                                                                    <textarea type="text" name="question_tital" id="question_tital" cols="5" rows="2" class="form-control input-sm" disabled=""><?php echo $result->question ?></textarea>   
                                                                
                                                                </div>                                                            
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row">
                                                        <!-- <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label><span id="label_dyamic">Question</span><span class="required"> * </span></label>
                                                                <textarea type="text" name="question" id="question" cols="5" rows="3" class="form-control input-sm" disabled=""><?php echo $result->question ?></textarea>   
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Assessor Guide</label>
                                                                <textarea type="text" name="assessor_guide" id="assessor_guide" cols="5" rows="3" class="form-control input-sm" disabled=""><?php echo $result->assessor_guide ?></textarea>   
                                                            </div>                                                           
                                                        </div>                                            
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Slide Heading (For Spotlight App)</label>
                                                                <textarea type="text" name="slide_heading" id="slide_heading" cols="5" rows="3" class="form-control input-sm" disabled=""><?php echo $result->slide_heading ?></textarea>   
                                                            </div>                                                           
                                                        </div>   
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Slide Description (For Spotlight App)</label>
                                                                <textarea type="text" name="slide_description" id="slide_description" cols="5" rows="3" class="form-control input-sm" disabled=""><?php echo $result->slide_description ?></textarea>   
                                                            </div>                                                           
                                                        </div>                                           
                                                    </div>
                                                </div>                                                           
                                            </div>                                            
                                            <div class="row">      
                                                <div class="col-md-12 text-right">                                                      
                                                    <a href="<?php echo site_url("video_situation"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
	    <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
        <script>
            if($('#question_option').val() == 0){
                CKEDITOR.replace('question', {
                    toolbar: [{
                            name: 'styles',
                            items: ['Styles', 'Format']
                        },
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                        },
                        {
                            name: 'links',
                            items: ['Link', 'Unlink', 'Anchor']
                        }
                    ],
                });
                CKEDITOR.config.autoParagraph = false;
			    CKEDITOR.config.readOnly  = true;
            }else
            {
                
                CKEDITOR.replace('question_tital', {
                    toolbar: [{
                            name: 'styles',
                            items: ['Styles', 'Format']
                        },
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                        },
                        {
                            name: 'links',
                            items: ['Link', 'Unlink', 'Anchor']
                        }
                    ],
                });
                CKEDITOR.config.autoParagraph = false;
			    CKEDITOR.config.readOnly  = true;
          
            }
        </script>               
    </body>
</html>