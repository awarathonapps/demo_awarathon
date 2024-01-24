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
                                    <span>Organisation</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Information Form</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>information_form" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Create Information Form
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    
                                    <form id="feedbackform" name="feedbackform" method="POST"  action="<?php echo $base_url; ?>information_form/submit" > 
                                        <div class="portlet-body">
                                             <div class="tabbable-full-width">   
                                                  <div class="tab-content">
                                            
                                                    <?php
                                                    $errors = validation_errors();
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
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($CompanySet as $cs) { ?>
                                                                            <option value="<?php echo $cs->id ?>"><?php echo $cs->company_name ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Form Name<span class="required"> * </span></label>
                                                                <input type="text" name="form_name" id="form_name" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Short Description</label>
                                                                <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="255" name="short_description" placeholder=""></textarea>
                                                                <span class="text-muted">(Max 255 Characters)</span>
                                                            </div>
                                                        </div>
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
                                                </div>                                                           
                                            </div>

                                            <div class="row margin-bottom-10">      
                                                <div class="col-md-12 text-right">  
                                                    <button type="button" id="confirm" name="confirm" class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmField();">
                                                        <span class="ladda-label">Add Field</span>
                                                    </button>                                               
                                                </div>
                                            </div>
                                            <div class="row">  
                                                <div class="col-md-12">
                                                    <table class="table table-striped table-bordered table-hover" id="FieldDatatable" name="FieldDatatable" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="20%">Display Name</th>
                                                                <th width="20%">Type</th>
                                                                <th width="20%">Data&nbsp;<a data-title="Data must enter the seperated by commas(,)">
                                                                        <i class="icon-info font-black sub-title"></i>
                                                                    </a></th>
                                                                <th width="5%">Mandatory</th>
                                                                <th width="10%">Order</th>
                                                                <th width="10%">Status</th>
                                                                <th width="5%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>                                        
                                        </div>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="feedback_form-submit" name="feedback_form-submit" data-loading-text="Please wait..." 
                                                        class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SaveFormData();">
                                                    <span class="ladda-label">Submit</span>
                                                </button>
                                                <a href="<?php echo site_url("information_form"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script>
            var FieldArrray = [];
            var SelectedArrray = [];
            var Totalfield = 1;
            var trainer_no;
            var base_url = "<?php echo $base_url; ?>";
            var Encode_id = "";
            var AddEdit = 'A';
        </script>
        <script type="text/javascript" src="<?php echo $asset_url; ?>assets/customjs/informationform_validation.js"></script>
    </body>
</html>