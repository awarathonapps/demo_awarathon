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
                                    <span>Feedback Set</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>New Feedback Set</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>feedback_set" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Create Feedback Set
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <form id="frmFeedbackSet" name="frmFeedbackSet" method="POST"  action="<?php echo $base_url; ?>feedback_set/submit"> 
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">                                                    
                                                    <?php
                                                    $errors = validation_errors();
                                                    //echo $errors;

                                                    if ($errors) {
                                                        ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
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
                                                    <div class="row">
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" onchange="CompanyChange();">
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($CompnayResultSet as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Feedback Set Title<span class="required"> * </span></label>
                                                                <input type="text" name="feedback_name" id="feedback_name" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Powered By<span class="required"> * </span></label>
                                                                <input type="text" name="powered_by" id="powered_by" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Language<span class="required"> * </span></label>
                                                                <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" >
                                                                    <?php if(isset($language_mst)){
                                                                            foreach ($language_mst as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>"><?php echo $Row->name ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Timer (In Sec.)</label>
                                                                <input type="number" name="timer" id="timer" maxlength="255" class="form-control input-sm" value="20">   
                                                                <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" selected>Active</option>
                                                                    <option value="0">In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>                                                           
                                        </div>    
                                        <div class="row margin-bottom-10">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="confirm" name="confirm" class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmType();">
                                                    <span class="ladda-label">Add Feedback Type</span>
                                                </button>                                               
                                            </div>
                                        </div>
                                        <div class="row">  
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered table-hover" id="typeDatatable" name="typeDatatable" width="100%">
                                                    <thead>
                                                        <tr>                                                    
                                                            <th width="20%">Type</th>
                                                            <th width="30%">Subtype</th>
                                                            <th width="10%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="feedback-submit" name="feedback-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SaveFeedbackSet();">
                                                    <span class="ladda-label">Save & Next</span>
                                                </button>
                                                <a href="<?php echo site_url("feedback_set"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                            </div>
                                        </div>
                                        </form>                                                                                    
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');  ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script>
            var TrainerArrray = [];
            var Totaltrainer = 1;
            var Base_url = "<?php echo base_url(); ?>";
            var Encode_id = "";
            var AddEdit = 'A';
            var SelectedArrray = []; 
        </script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/customjs/feedbackset_validation.js"></script>
        <<script>ConfirmType()</script>>
    </body>
</html>