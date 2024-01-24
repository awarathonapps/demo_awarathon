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
                                    <span>Parameter</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>New Parameter</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>parameter" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Create Parameter
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <form id="frmParameter" name="frmParameter" method="POST" > 
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">                                                    
                                                    <?php
                                                    $errors = validation_errors();                                                    
                                                    if ($errors) { ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <?php echo $errors; ?>
                                                        </div>
                                                    <?php } ?>                                                    
                                                    <div class="row">
                                                        <?php if ($Company_id == "") { ?>
                                                            <div class="col-md-4">       
                                                                <div class="form-group">
                                                                    <label class="">Company Name<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($CompnayResultSet as $cmp) { ?>
                                                                            <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Assessment Type<span class="required"> * </span></label>
                                                                <select name="assessment_type" id="assessment_type" class="form-control input-sm select2">
                                                                    <option value="">Please select</option>
                                                                    <?php foreach ($assessment_type as $val) { ?>
                                                                        <option value="<?php echo $val->id ?>"><?php echo $val->description ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Parameter<span class="required"> * </span></label>
                                                                <input type="text" name="parameter" id="parameter" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
														<div class="col-md-4">       
                                                                <div class="form-group">
                                                                    <label class="">Category<span class="required"> * </span></label>
                                                                    <select id="category_id" name="category_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                        <!-- <option value="">Please Select</option> -->
                                                                        <?php foreach ($category_set as $cmp) { ?>
                                                                            <option value="<?= $cmp->id; ?>" selected><?php echo $cmp->name; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">    
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
                                            <div class="row">      
                                                <div class="col-md-12 text-right">  
                                                    <button type="button" id="parameter-submit" name="parameter-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="save_parameter();">
                                                        <span class="ladda-label">Save</span>
                                                    </button>
                                                    <a href="<?php echo site_url("parameter"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <script>         
        var base_url     = "<?php echo $base_url; ?>";
        var frmParameter = $('#frmParameter');            
        var form_error = $('.alert-danger', frmParameter);
        var form_success = $('.alert-success', frmParameter);
        jQuery(document).ready(function() {   
            $('.range-class').hide();
            frmParameter.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    company_id: {
                        required: true
                    },
                    assessment_type: {
                        required: true
                    },
					category_id: {
                        required: true
                    },
                    parameter: {
                        required: true,
                        parameterCheck: true
                    },
                    weight_type: {
                        required: true                                 
                    }, 
                    weight_value :{
                        required: function () {
                            return $('#weight_type').val() == 1;
                        }  
                    },
                    weight_range_from :{
                        required: function () {
                            return $('#weight_type').val() == 2;
                        },
                        max: function() {
                            return parseInt($('#weight_range_to').val());
                        },                        
                        min:0,                        
                    },
                    weight_range_to :{
                        required: function () {
                            return $('#weight_type').val() == 2;
                        },
                        min: function() {
                            return parseInt($('#weight_range_from').val());
                        }                                                                       
                    },
                    status: {
                        required: true
                    }                    
                },
                invalidHandler: function (event, validator) {
                    form_success.hide();
                    form_error.show();
                    App.scrollTo(form_error, -200);
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.hasClass('.form-group')) {
                        error.appendTo(element.parent().find('.has-error'));
                    } else if (element.parent('.form-group').length) {
                        error.appendTo(element.parent());
                    } else {
                        error.appendTo(element);
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').addClass('has-error');                    
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error');                    
                },
                success: function (label) {
                    label.closest('.form-group').removeClass('has-error');                    
                },
                submitHandler: function (form) {
                    form_success.show();
                    form_error.hide();
                    Ladda.bind('button[id=parameter-submit]');
                    form.submit();
                }
            });
            jQuery.validator.addMethod("parameterCheck", function (value, element) {
                var isSuccess = false;                                
                $.ajax({
                    type: "POST",
                    data: {parameter: value, assessment_type: $('#assessment_type').val()},
                    url: "<?php echo base_url(); ?>trinity_parameter/Check_parameter",
                    async: false,
                    success: function (msg) {
                        isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
            }
            , "Parameter already exists!!!");
        });   
        function save_parameter() {
            if (!$('#frmParameter').valid()) {
                return false;
            }                        
            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>trinity_parameter/submit',
                data: $('#frmParameter').serialize(),
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (Odata) {
                    var Data = $.parseJSON(Odata);
                    if (Data['success']) {
                        ShowAlret(Data['Msg'], 'success');  
                        window.location.href = base_url + 'parameter';
                    } else {
                        $('#errordiv').show();
                        $('#errorlog').html(Data['Msg']);
                        ShowAlret(Data['Msg'], "error");
                        App.scrollTo(form_error, -200);
                    }
                    customunBlockUI();
                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                    ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
                }
            });
        }                                   
        </script>                
    </body>
</html>