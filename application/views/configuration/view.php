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
                                    <span>Workshop</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>View Question</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>questions" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            View Question 
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">   
                                        <form id="FrmQns" name="FrmQns" method="POST" > 
                                            <div class="portlet-body">                                                                                            
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">
                                                        <?php if ($Company_id == "") { ?>
                                                            <div class="row">    
                                                                <div class="col-md-6">       
                                                                    <div class="form-group">
                                                                        <label class="">Company Name<span class="required"> * </span></label>
                                                                        <select id="company_id" name="company_id" disabled="" class="form-control input-sm select2" placeholder="Please select" style="width:100%" onchange="getComapnywiseTopic();">
                                                                            <?php foreach ($SelectCompany as $cmp) { ?>
                                                                                <option value="<?= $cmp->id; ?>"  <?php echo ($RowSet->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Topic<span class="required"> * </span></label>
                                                                    <select id="topic_id" name="topic_id" disabled="" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" onchange="getTopicwiseSubtopic()">
                                                                        <?php foreach ($TopicResultSet as $cmp) { ?>
                                                                            <option value="<?= $cmp->id; ?>"  <?php echo ($RowSet->topic_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->description; ?> </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Sub Topic<span class="required"> * </span></label>
                                                                    <select id="subtopic_id" disabled="" name="subtopic_id" class="form-control input-sm " placeholder="Please select" style="width:100%" >
                                                                        <?php foreach ($SubTopicResultSet as $cmp) { ?>
                                                                            <option value="<?= $cmp->id; ?>"  <?php echo ($RowSet->subtopic_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->description; ?> </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Language<span class="required"> * </span></label>
                                                                    <select id="language_id" name="language_id" disabled="" class="form-control input-sm " placeholder="Please select" style="width:100%" >
                                                                        <?php
                                                                        if (isset($language_mst)) {
                                                                            foreach ($language_mst as $Row) {
                                                                                ?>
                                                                                <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->language_id == $Row->id ? 'Selected' : ''); ?> ><?php echo $Row->name ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Question<span class="required"> * </span></label>
                                                                    <textarea rows="2" disabled="" class="form-control input-sm" id="question_title" maxlength="150" name="question_title" placeholder=""><?php echo $RowSet->question_title; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Option A<span class="required"> * </span></label>
                                                                    <input type="text" disabled="" name="option_a" id="option_a" value="<?php echo $RowSet->option_a; ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Option B<span class="required"> * </span></label>
                                                                    <input type="text" disabled="" name="option_b" id="option_b" value="<?php echo $RowSet->option_b; ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Option C</label>
                                                                    <input type="text" disabled="" name="option_c" id="option_c" value="<?php echo $RowSet->option_c; ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Option D</label>
                                                                    <input type="text" disabled="" name="option_d" id="option_d" value="<?php echo $RowSet->option_d; ?>" maxlength="255" class="form-control input-sm">   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Correct Answer<span class="required"> * </span></label>
                                                                    <select id="correct_answer" disabled="" name="correct_answer" class="form-control input-sm " placeholder="Please select" onchange="CheckValidAnswer();" >
                                                                        <option value="">Please Select</option>
                                                                        <option value="a" <?php echo ($RowSet->correct_answer == 'a' ? 'Selected' : ''); ?>>Option A</option>
                                                                        <option value="b" <?php echo ($RowSet->correct_answer == 'b' ? 'Selected' : ''); ?>>Option B</option>
                                                                        <option value="c" <?php echo ($RowSet->correct_answer == 'c' ? 'Selected' : ''); ?>>Option C</option>
                                                                        <option value="d" <?php echo ($RowSet->correct_answer == 'd' ? 'Selected' : ''); ?>>Option D</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Tip</label>
                                                                    <textarea rows="2" class="form-control input-sm" id="tip" disabled="" maxlength="150" name="tip" placeholder="Tip"><?php echo $RowSet->tip; ?></textarea>
                                                                    <span class="text-muted">(Max 150 Characters)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Question Image</label>
                                                                    <div class="form-control fileinput fileinput-exists" style="    border: none;height:auto;" data-provides="fileinput">
                                                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                            <img src="<?php echo base_url() . 'assets/uploads/no_image.png' ?>" alt=""/>
                                                                        </div>
                                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                            <img src="<?php echo base_url() . 'assets/uploads/' . ($RowSet->hint_image != '' ? 'questions/' . $RowSet->hint_image : 'no_image.png'); ?>" alt=""/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Youtube Link</label>
                                                                    <input type="text" name="youtube_url" id="youtube_url" placeholder="Youtube Url" value="<?php echo $RowSet->youtube_link; ?>" maxlength="255" class="form-control input-sm " disabled="">   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm " disabled="" placeholder="Please select" >
                                                                        <option value="1" <?php echo ($RowSet->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                        <option value="0" <?php echo ($RowSet->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                                                           
                                                </div>                                      
                                            </div>
                                            <div class="row">      
                                                <div class="col-md-12 text-right">  
                                                    <a href="<?php echo site_url("questions"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
    </body>
</html>